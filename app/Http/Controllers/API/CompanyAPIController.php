<?php
/**
=============================================
-- File Name : CompanyAPIController.php
-- Project Name : ERP
-- Module Name :  Company Master
-- Author : Mohamed Fayas
-- Create date : 14 - March 2018
-- Description : This file contains the all CRUD for company master.
-- REVISION HISTORY
-- Date: 14-March 2018 By: Fayas Description: Added new functions named as getSupplierFormData(),getAllCompanies()
 */

namespace App\Http\Controllers\API;

use App\helper\CompanyService;
use App\helper\Helper;
use App\helper\hrCompany;
use App\Http\Requests\API\CreateCompanyAPIRequest;
use App\Http\Requests\API\UpdateCompanyAPIRequest;
use App\Models\ChartOfAccountsAssigned;
use App\Models\Company;
use App\Models\ChartOfAccount;
use App\Models\CompanyPolicyMaster;
use App\Models\CountryMaster;
use App\Models\SupplierCategoryICVMaster;
use App\Models\SupplierCategoryMaster;
use App\Models\CurrencyMaster;
use App\Models\SupplierImportance;
use App\Models\SupplierMaster;
use App\Models\suppliernature;
use App\Models\SupplierContactType;
use App\Models\YesNoSelection;
use App\Models\SupplierCritical;
use App\Models\SupplierType;
use App\Repositories\CompanyRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Repositories\CompanyPolicyCategoryRepository;
use App\Repositories\CompanyPolicyMasterRepository;
use Exception;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Illuminate\Support\Facades\DB;
use Response;
use Carbon\Carbon;

/**
 * Class CompanyController
 * @package App\Http\Controllers\API
 */
class CompanyAPIController extends AppBaseController
{
    /** @var  CompanyRepository */
    private $companyRepository;
    private $policyCategoryRepository;
    private $policyMasterRepository;

    public function __construct(CompanyRepository $companyRepo, CompanyPolicyCategoryRepository $policyCategoryRepo,
     CompanyPolicyMasterRepository $policyMasterRepo)
    {
        $this->companyRepository = $companyRepo;
        $this->policyCategoryRepository = $policyCategoryRepo;
        $this->policyMasterRepository = $policyMasterRepo;
    }
    
    
    /**
     * Display a listing of the Company.
     * GET|HEAD /companies
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->companyRepository->pushCriteria(new RequestCriteria($request));
        $this->companyRepository->pushCriteria(new LimitOffsetCriteria($request));
        $companies = $this->companyRepository->all();

        return $this->sendResponse($companies->toArray(), 'Companies retrieved successfully');
    }


    /**
     * Get Supplier Form Data
     * Created by Fayas
     * on 28-02-2018
     */
    public function getSupplierFormData(Request $request)
    {

        $selectedCompanyId = $request['selectedCompanyId'];
        $supplierID = isset($request['supplierID'])?$request['supplierID']:0;

        /** all Company  Drop Down */
        $allCompanies = Company::where("isGroup",0)->get();

        $isGroup = \Helper::checkIsCompanyGroup($selectedCompanyId);
        if($isGroup){
            $subCompanies  = \Helper::getGroupCompany($selectedCompanyId);
            //$subCompanies  = \Helper::getSubCompaniesByGroupCompany($selectedCompanyId);
            /**  Companies by group  Drop Down */
            $companies = Company::whereIn("companySystemID",$subCompanies)->where("isGroup",0)->get();
        }else{
            $companies = Company::where("companySystemID",$selectedCompanyId)->get();
        }


        /**Chart of Account Drop Down */
        $liabilityAccount = ChartOfAccount::where('controllAccountYN', '=', 1)
            ->where('controlAccountsSystemID', 4)
            ->where('catogaryBLorPL', '=', 'BS')
            ->orderBy('AccountDescription', 'asc')
            ->get();

        /**Country Drop Down */
        $country = CountryMaster::orderBy('countryName', 'asc')
            ->get();

        /** Supplier category  */
        $supplierCategory = SupplierCategoryMaster::orderBy('categoryDescription', 'asc')
            ->get();

        /** Currency Master */
        $currencyMaster = CurrencyMaster::orderBy('CurrencyName', 'asc')
                                          ->get();

        /** Supplier Importance */
        $supplierNature = suppliernature::all();

        /** Supplier Nature */
        $supplierType = SupplierType::all();

        /** Supplier Type */
        $supplierImportance = SupplierImportance::all();

        /** supplier Critical */
        $supplierCritical = SupplierCritical::all();

        /** Yes and No Selection */
        $yesNoSelection = YesNoSelection::all();

        /** contact types */
        $contactTypes = SupplierContactType::all();


        $icvCategories = SupplierCategoryICVMaster::all();

        $hasPolicy = false;
        $hasExternalSupplierGeneratePolicy = false;
        $hasEEOSSPolicy = false;
        if($supplierID !=0){
            $supplier = SupplierMaster::find($supplierID);
            if(isset($supplier->primaryCompanySystemID) && $supplier->primaryCompanySystemID){
                $hasPolicy = CompanyPolicyMaster::where('companySystemID', $supplier->primaryCompanySystemID)
                    ->where('companyPolicyCategoryID', 38)
                    ->where('isYesNO',1)
                    ->exists();

                $hasEEOSSPolicy = CompanyPolicyMaster::where('companySystemID', $supplier->primaryCompanySystemID)
                    ->where('companyPolicyCategoryID', 41)
                    ->where('isYesNO',1)
                    ->exists();
            }
        }

        $hasExternalSupplierGeneratePolicy = CompanyPolicyMaster::where('companySystemID', $selectedCompanyId)
                                                                ->where('companyPolicyCategoryID', 48)
                                                                ->where('isYesNO',1)
                                                                ->exists();


        $output = array('companies' => $companies->toArray(),
            'liabilityAccount' => $liabilityAccount,
            'country' => $country,
            'supplierCategoryMaster' => $supplierCategory,
            'currencyMaster' => $currencyMaster,
            'supplierImportance' => $supplierImportance,
            'supplierNature' => $supplierNature,
            'supplierType' => $supplierType,
            'supplierCritical' => $supplierCritical,
            'yesNoSelection' => $yesNoSelection,
            'allCompanies' => $allCompanies,
            'contactTypes' => $contactTypes,
            'icvCategories' => $icvCategories,
            'isSupplierCatalogPolicy' => $hasPolicy,
            'hasExternalSupplierGeneratePolicy' => $hasExternalSupplierGeneratePolicy,
            'isEEOSSPolicy' => $hasEEOSSPolicy
            );
        return $this->sendResponse($output, 'Record retrieved successfully');

    }

    /**
     * Get all companies
     * Created by Fayas
     * on 15-03-2018
     */

    public function getAllCompanies (){

        /** all Company  Drop Down */
        $allCompanies = Company::where("isGroup",0)->get();

        return $this->sendResponse($allCompanies->toArray(), 'Record retrieved successfully');
    }


    /**
     * Store a newly created Company in storage.
     * POST /companies
     *
     * @param CreateCompanyAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateCompanyAPIRequest $request)
    {
        $input = $request->all();
        $messages = [
            'CompanyID.required' => 'CompanyID is required.',
            'localCurrencyID.required' => 'Local Currency ID is required.',
            'reportingCurrency.required' => 'Reporting Currency ID is required.',
            'exchangeGainLossGLCodeSystemID.required' => 'Exchange Gain/Loss GL is required.',
        ];
        $validator = \Validator::make($input, [
            'CompanyID' => 'required|unique:companymaster',
            'CompanyName' => 'required|unique:companymaster',
            'companyShortCode' => 'required|unique:companymaster',
            'companyCountry' => 'required|numeric|min:1',
            'CompanyTelephone' => 'required',
            'CompanyEmail' => 'required',
            'registrationNumber' => 'required',
            'localCurrencyID' => 'required|numeric|min:1',
            'reportingCurrency' => 'required|numeric|min:1',
            'exchangeGainLossGLCodeSystemID' => 'required|numeric|min:1'
        ], $messages);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        if(isset($input['masterCompanySystemIDReorting']) && $input['masterCompanySystemIDReorting'] > 0){
            $masterCompany = Company::find($input['masterCompanySystemIDReorting']);
            if($masterCompany){
                $input['masterComapanyIDReporting'] = $masterCompany->CompanyID;
            }
        }

        if(isset($input['exchangeGainLossGLCodeSystemID']) && $input['exchangeGainLossGLCodeSystemID'] > 0){
            $gl = ChartOfAccount::find($input['exchangeGainLossGLCodeSystemID']);
            if($gl){
                $input['exchangeGainLossGLCode'] = $gl->AccountCode;
            }
        }


        $disk = Helper::policyWiseDisk($input['masterCompanySystemIDReorting'], 'local_public');
        $awsPolicy = Helper::checkPolicy($input['masterCompanySystemIDReorting'], 50);

        /*image upload*/
        $attachment = $input['nextAttachment'];
        if(!empty($attachment) && isset($attachment['file'])){
            $extension = $attachment['fileType'];
            $allowExtensions = ['png','jpg','jpeg'];

            if (!in_array($extension, $allowExtensions))
            {
                return $this->sendError('This type of file not allow to upload.',500);
            }

            if(isset($attachment['size'])){
                if ($attachment['size'] > 2097152) {
                    return $this->sendError("Maximum allowed file size is 2 MB. Please upload lesser than 2 MB.",500);
                }
            }

            $file = $attachment['file'];
            $decodeFile = base64_decode($file);

            $input['companyLogo'] = $input['CompanyID'].'_logo.' . $extension;

            if ($awsPolicy) {
                $path = $input['CompanyID'].'/logos/' . $input['companyLogo'];
            } else {
                $path = 'logos/' . $input['companyLogo'];
            }

            $input['logoPath'] = $path;
            Storage::disk($disk)->put($path, $decodeFile);
        }


        $employee = Helper::getEmployeeInfo();
        $input['createdPcID'] = gethostname();
        $input['createdUserID'] = $employee->empID;

        if (isset($input['jsrsExpiryDate'])) {
            $input['jsrsExpiryDate'] = Carbon::parse($input['jsrsExpiryDate']);
        }

        DB::beginTransaction();
        try {
            $companies = $this->companyRepository->create($input);
            $companies = $companies->toArray();

            [ 'companySystemID'=> $company_id, 'CompanyID'=> $company_code ] = $companies;


            CompanyService::assign_policies($company_id, $company_code);
            CompanyService::assign_document_attachments($company_id, $company_code);

            $hrCompany = app()->make(hrCompany::class);
            $hrCompany->store($companies);
 
            DB::commit();
            return $this->sendResponse($companies, 'Company saved successfully');
        }
        catch(Exception $ex){
            DB::rollback();
            $ex_arr = Helper::exception_to_error($ex);
            return $this->sendError('Error in company create process.', 500, $ex_arr);
        }
    }

    /**
     * Display the specified Company.
     * GET|HEAD /companies/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var Company $company */
        $company = $this->companyRepository->findWithoutFail($id);

        if (empty($company)) {
            return $this->sendError('Company not found');
        }

        return $this->sendResponse($company->toArray(), 'Company retrieved successfully');
    }

    /**
     * Update the specified Company in storage.
     * PUT/PATCH /companies/{id}
     *
     * @param  int $id
     * @param UpdateCompanyAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateCompanyAPIRequest $request)
    {
        $input = $request->all();
        // $input = $this->convertArrayToValue($input);
        $input = $this->convertArrayToSelectedValue($input,['companyCountry','exchangeGainLossGLCodeSystemID','isActive','localCurrencyID','reportingCurrency','vatRegisteredYN']);
        /** @var Company $company */
        $company = $this->companyRepository->findWithoutFail($id);

        if (empty($company)) {
            return $this->sendError('Company not found');
        }

        $messages = [
            'CompanyID.required' => 'CompanyID is required.',
            'localCurrencyID.required' => 'Local Currency ID is required.',
            'reportingCurrency.required' => 'Reporting Currency ID is required.',
            'exchangeGainLossGLCodeSystemID.required' => 'Exchange Gain/Loss GL is required.',
        ];
        $validator = \Validator::make($input, [
            'CompanyID' => ['required',Rule::unique('companymaster')->ignore($id, 'companySystemID')],
            'CompanyName' => ['required',Rule::unique('companymaster')->ignore($id, 'companySystemID')],
            'companyShortCode' => 'required',
            'companyCountry' => 'required|numeric|min:1',
            'CompanyTelephone' => 'required',
            'CompanyEmail' => 'required',
            'registrationNumber' => 'required',
            'localCurrencyID' => 'required|numeric|min:1',
            'reportingCurrency' => 'required|numeric|min:1',
            'exchangeGainLossGLCodeSystemID' => 'required|numeric|min:1'
        ], $messages);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        if(isset($input['exchangeGainLossGLCodeSystemID']) && $input['exchangeGainLossGLCodeSystemID'] > 0){
            $gl = ChartOfAccount::find($input['exchangeGainLossGLCodeSystemID']);
            if($gl){
                $input['exchangeGainLossGLCode'] = $gl->AccountCode;
            }
        }

        $disk = Helper::policyWiseDisk($company->masterCompanySystemIDReorting, 'local_public');
        $awsPolicy = Helper::checkPolicy($company->masterCompanySystemIDReorting, 50);

        if (isset($input['jsrsExpiryDate'])) {
            $input['jsrsExpiryDate'] = Carbon::parse($input['jsrsExpiryDate']);
        }

        /*image upload*/
        $attachment = $input['nextAttachment'];
        if(!empty($attachment) && isset($attachment['file'])){
            $extension = $attachment['fileType'];
            $allowExtensions = ['png','jpg','jpeg'];

            if (!in_array($extension, $allowExtensions))
            {
                return $this->sendError('This type of file not allow to upload.',500);
            }

            if(isset($attachment['size'])){
                if ($attachment['size'] > 2097152) {
                    return $this->sendError("Maximum allowed file size is 2 MB. Please upload lesser than 2 MB.",500);
                }
            }

            $file = $attachment['file'];
            $decodeFile = base64_decode($file);

            $input['companyLogo'] = $input['CompanyID'].'_logo.' . $extension;

            if ($awsPolicy) {
                $path = $input['CompanyID'].'/logos/' . $input['companyLogo'];
            } else {
                $path = 'logos/' . $input['companyLogo'];
            }

            if ($exists = Storage::disk($disk)->exists($path)) {
                Storage::disk($disk)->delete($path);
            }


            $input['logoPath'] = $path;

            Storage::disk($disk)->put($path, $decodeFile);
        }

        $employee = Helper::getEmployeeInfo();
        $input['modifiedPc'] = gethostname();
        $input['modifiedUser'] = $employee->empID;

        $input = array_except($input, ['createdDateTime', 'timeStamp']); 

        DB::beginTransaction();
        try {
            $company = $this->companyRepository->update($input, $id);

            $hrCompany = app()->make(hrCompany::class);
            $hrCompany->update($id, $input);
            
            DB::commit();
            return $this->sendResponse($company->toArray(), 'Company updated successfully');
        }
        catch(Exception $ex){
            DB::rollback();
            $ex_arr = Helper::exception_to_error($ex);
            return $this->sendError('Error in company updated process.', 500, $ex_arr);
        }
    }

    /**
     * Remove the specified Company from storage.
     * DELETE /companies/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var Company $company */
        $company = $this->companyRepository->findWithoutFail($id);

        if (empty($company)) {
            return $this->sendError('Company not found');
        }

        $company->delete();

        return $this->sendResponse($id, 'Company deleted successfully');
    }

    public function getCompaniesByGroup(){
        $employee_id = Helper::getEmployeeSystemID();
        $company = [];
        if ($employee_id) {
            $company = Company::select(DB::raw("companySystemID,CONCAT(CompanyID,' - ',CompanyName) as label"))
                ->whereHas('employee_departments' , function($q) use($employee_id){
                    $q->where('employeeSystemID',$employee_id);
                })
                ->where('isGroup',0)
                ->get();
        }
        return $this->sendResponse($company, 'Company retrieved successfully');
    }

    public function getCompanies(Request $request){

        $input = $request->all();
        $companySystemID = $input['companySystemID'];
        $isGroup = \Helper::checkIsCompanyGroup($companySystemID);

        if ($isGroup) {
            $childCompanies = \Helper::getGroupCompany($companySystemID);
        } else {
            $childCompanies = [$companySystemID];
        }

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $companies = Company::whereIn('companySystemID',$childCompanies)->with(['reportingcurrency','localcurrency','exchange_gl','country','vat_input_gl','vat_output_gl']);

        $search = $request->input('search.value');
        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $companies = $companies->where(function ($query) use ($search) {
                $query->where('CompanyID', 'LIKE', "%{$search}%")
                    ->orWhere('CompanyName','LIKE', "%{$search}%")
                    ->orWhere('sortOrder','LIKE', "%{$search}%")
                    ->orWhere('CompanyAddress','LIKE', "%{$search}%")
                    ->orWhere('companyCountry','LIKE', "%{$search}%")
                    ->orWhere('CompanyTelephone','LIKE', "%{$search}%")
                    ->orWhere('CompanyFax','LIKE', "%{$search}%")
                    ->orWhere('CompanyEmail','LIKE', "%{$search}%")
                    ->orWhere('CompanyURL','LIKE', "%{$search}%")
                    ->orWhere('registrationNumber','LIKE', "%{$search}%")
                    ->orWhere('qhseApiKey','LIKE', "%{$search}%")
                    ->orWhere('companyShortCode','LIKE', "%{$search}%")
                    ->orWhereHas('exchange_gl', function ($q) use($search){
                        $q->where('AccountCode','LIKE', "%{$search}%")
                        ->orWhere('AccountDescription','LIKE', "%{$search}%");
                    });
            });
        }

        return \DataTables::eloquent($companies)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('companySystemID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->rawColumns(['logo_url'])
            ->with('orderCondition', $sort)
            ->addColumn('Actions', 'Actions', "Actions")
            ->make(true);
    }

    public function getCompanySettingFormData(Request $request){
        $input = $request->all();
        $selectedCompanyId = $input['companySystemID'];
        $type = $input['type'];

        $isGroup = \Helper::checkIsCompanyGroup($selectedCompanyId);
        if ($isGroup) {
            $companies = \Helper::getGroupCompany($selectedCompanyId);
        } else {
            $companies = [$selectedCompanyId];
        }

        $chartOfAccount = ChartOfAccountsAssigned::where('isActive', 1)->whereIn('companySystemID', $companies)->groupBy('chartOfAccountSystemID')->get();
        $currencyMaster = CurrencyMaster::orderBy('CurrencyName', 'asc')
            ->get();

        /** Yes and No Selection */
        $yesNoSelection = YesNoSelection::all();

        /**Country Drop Down */
        $country = CountryMaster::orderBy('countryName', 'asc')
            ->get();

        $output = array(
            'country' => $country,
            'currencyMaster' => $currencyMaster,
            'yesNoSelection' => $yesNoSelection,
            'chartOfAccount' => $chartOfAccount,
            'isGroup' => $isGroup
        );
        return $this->sendResponse($output, 'Record retrieved successfully');
    }

    public function uploadCompanyLogo(Request $request) {
        $input = $request->all();
        $extension = $input['fileType'];
        $allowExtensions = ['png','jpg','jpeg'];

        if (!in_array($extension, $allowExtensions))
        {
            return $this->sendError('This type of file not allow to upload.',500);
        }


        if(isset($input['size'])){
            if ($input['size'] > 2097152) {
                return $this->sendError("Maximum allowed file size is 2 MB. Please upload lesser than 2 MB.",500);
            }
        }

        if (isset($input['companySystemID'])) {


            $companyMaster = Company::where('companySystemID', $input['companySystemID'])->first();

            $disk = Helper::policyWiseDisk($companyMaster->masterCompanySystemIDReorting, 'local_public');
            $awsPolicy = Helper::checkPolicy($companyMaster->masterCompanySystemIDReorting, 50);
            if ($companyMaster) {

                $file = $request->request->get('file');
                $decodeFile = base64_decode($file);

                $myFileName = $companyMaster->CompanyID.'_logo.' . $extension;

                if ($awsPolicy) {
                    $path = $companyMaster->CompanyID.'/logos/' . $myFileName;
                } else {
                    $path = 'logos/' . $myFileName;
                }

                $logoPath = $path;
                Storage::disk($disk)->put($path, $decodeFile);

                $this->companyRepository->update(['companyLogo'=>$myFileName, 'logoPath' => $logoPath],$input['companySystemID']);

                return $this->sendResponse([], 'Company Logo uploaded successfully');

            }
        }
    }

}
