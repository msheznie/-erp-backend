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
use App\Models\AppearanceSettings;
use App\Models\ChartOfAccountsAssigned;
use App\Models\Company;
use App\Models\ChartOfAccount;
use App\Models\SupplierCategory;
use App\Models\SupplierGroup;

use App\Models\CompanyPolicyMaster;
use App\Models\CountryMaster;
use App\Models\SupplierCategoryICVMaster;
use App\Models\SupplierCategoryMaster;
use App\Models\CurrencyMaster;
use App\Models\SupplierImportance;
use App\Models\SupplierMaster;
use App\Models\suppliernature;
use App\Models\SupplierContactType;
use App\Models\SystemGlCodeScenarioDetail;
use App\Models\Tax;
use App\Models\YesNoSelection;
use App\Models\SupplierCritical;
use App\Models\SupplierType;
use App\Repositories\CompanyRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Models\CompanyDigitalStamp;
use App\Models\SystemGlCodeScenario;
use App\Repositories\CompanyPolicyCategoryRepository;
use App\Repositories\CompanyPolicyMasterRepository;
use App\Repositories\CompanyDigitalStampRepository;
use Exception;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Illuminate\Support\Facades\DB;
use Response;
use Carbon\Carbon;
use Image;

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
    private $CompanyDigitalStampRepository;

    public function __construct(CompanyRepository $companyRepo, CompanyPolicyCategoryRepository $policyCategoryRepo,
     CompanyPolicyMasterRepository $policyMasterRepo, CompanyDigitalStampRepository $companyDigitalStampRepo)
    {
        $this->companyRepository = $companyRepo;
        $this->policyCategoryRepository = $policyCategoryRepo;
        $this->policyMasterRepository = $policyMasterRepo;
        $this->companyDigitalStampRepository = $companyDigitalStampRepo;
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
            ->whereHas('chartofaccount_assigned', function($query) use ($selectedCompanyId) {
                $query->where('companySystemID', $selectedCompanyId)
                    ->where('isAssigned', -1)
                    ->where('isActive', 1);
            })->where('controlAccountsSystemID', 4)
            ->where('isApproved',1)
            ->where('isActive',1)
            ->where('catogaryBLorPL', '=', 'BS')
            ->orderBy('AccountDescription', 'asc')
            ->get();

        $assetAndLiabilityAccount = ChartOfAccount::
        where(function ($query)  {
            $query->where('controlAccountsSystemID', 3)
                ->orWhere('controlAccountsSystemID', 4);
        })
         ->where('isBank',0)
            ->where('isApproved',1)
            ->where('isActive',1)
        ->where('catogaryBLorPL', '=', 'BS')
        ->whereHas('chartofaccount_assigned',function($query) use($selectedCompanyId){
            $query->where('companySystemID',$selectedCompanyId)->where('isAssigned',-1)->where('isActive', 1);
        })
        ->orderBy('AccountDescription', 'asc')
        ->get();

        $assetAndLiabilityAccountCOA = ChartOfAccount::where('isBank',0)
            ->where('isApproved',1)
            ->where('isActive',1)
            ->where('catogaryBLorPL', '=', 'BS')
            ->whereHas('chartofaccount_assigned',function($query) use($selectedCompanyId){
                $query->where('companySystemID',$selectedCompanyId)->where('isAssigned',-1)->where('isActive', 1);
            })
            ->orderBy('AccountDescription', 'asc')
            ->get();


        /**Country Drop Down */
        $country = CountryMaster::orderBy('countryName', 'asc')
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

        $supplierCategories = SupplierCategory::onlyNotDeletedAndActive();

        $supplierGroups = SupplierGroup::onlyNotDeletedAndActive();

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

        $hasExternalSupplierGeneratePolicy = Helper::checkPolicy($selectedCompanyId, 48);

        $hasExistingSupplierSRMLinkPolicy = Helper::checkPolicy($selectedCompanyId, 86);

        $hasSupplierGeneratePolicy = Helper::checkPolicy($selectedCompanyId, 76);
        $hasPublicLinkGeneratePolicy = Helper::checkPolicy($selectedCompanyId, 94);

        $discountsChartOfAccounts = ChartOfAccount::where('isApproved',1)
            ->where('isActive',1)
            ->where('catogaryBLorPL', '=', 'PL')
            ->whereHas('chartofaccount_assigned',function($query) use($selectedCompanyId){
                $query->where('companySystemID',$selectedCompanyId)->where('isAssigned',-1)->where('isActive', 1);
            })
            ->orderBy('AccountDescription', 'asc')
            ->get();

        $whtTypes = Tax::where('companySystemID',$selectedCompanyId)->where('taxCategory',3)->where('isActive',1)->get();

        $businessCategories = SupplierCategoryMaster::where('isActive',1)->get();

        $output = array('companies' => $companies->toArray(),
            'liabilityAccount' => $liabilityAccount,
            'assetAndLiaAccount' => $assetAndLiabilityAccount,
            'country' => $country,
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
            'hasExistingSupplierSRMLinkPolicy' => $hasExistingSupplierSRMLinkPolicy,
            'isEEOSSPolicy' => $hasEEOSSPolicy,
            'supplierCategories' => $supplierCategories,
            'supplierGroups' => $supplierGroups,
            'isGroup' => $isGroup,
            'hasSupplierGeneratePolicy'=> $hasSupplierGeneratePolicy,
            'discountsChartOfAccounts' => $discountsChartOfAccounts,
            'assetAndLiabilityAccountCOA' => $assetAndLiabilityAccountCOA,
            'businessCategories' => $businessCategories,
            'whtTypes' => $whtTypes,
            'hasPublicLinkGeneratePolicy' => $hasPublicLinkGeneratePolicy
        );

        return $this->sendResponse($output, 'Record retrieved successfully');

    }

    public function getAppearance(Request $request){

        $appearanceSystemID = $request->get('appearance_system_id');

        $data= AppearanceSettings::with(['elements'])->where('appearance_system_id', $appearanceSystemID)->get();
        foreach ($data as $dt){
            if($dt->appearance_element_id == 2){
                $dt->value = Helper::getFileUrlFromS3($dt->value);
            }
            if($dt->appearance_element_id == 9){
                $dt->value = Helper::getFileUrlFromS3($dt->value);
            }


        }

        return $this->sendResponse($data,'Record retrieved successfully');
    }

    public function getAdvanceAccount(Request $request)
    {
        $selectedCompanyId = $request['selectedCompanyId'];
        $assetAndLiabilityAccount = ChartOfAccount::where(function ($query)  {
            $query->where('controlAccountsSystemID', 3)
                ->orWhere('controlAccountsSystemID', 4);
        })
            ->where('isBank',0)
            ->where('isApproved',1)
            ->where('catogaryBLorPL', '=', 'BS')
            ->whereHas('chartofaccount_assigned',function($query) use($selectedCompanyId){
                $query->where('companySystemID',$selectedCompanyId)->where('isAssigned',-1);
            })
            ->orderBy('AccountDescription', 'asc')
            ->get();

        $output = array('assetAndLiaAccount' => $assetAndLiabilityAccount);
        return $this->sendResponse($output, 'Record retrieved successfully');

    }

    public function getChartOfAccountConfigs(Request $request)
    {
        $selectedCompanyId = $request['selectedCompanyId'];

        $liabilityAccountConfigs = SystemGlCodeScenario::where('slug','account-payable-liability-account')
                        ->with(['detail'=>function($query) use($selectedCompanyId){
                            $query->where('companySystemID',$selectedCompanyId);
                        }])
                        ->whereHas('detail',function($query) use($selectedCompanyId){
                            $query->where('companySystemID',$selectedCompanyId);
                        })
                        ->first();

        $unbilledAccountConfigs = SystemGlCodeScenario::where('slug','account-payable-unbilled-account')
                        ->with(['detail'=>function($query) use($selectedCompanyId){
                            $query->where('companySystemID',$selectedCompanyId);
                        }])                        
                        ->whereHas('detail',function($query) use($selectedCompanyId){
                            $query->where('companySystemID',$selectedCompanyId);
                        })
                        ->first();

        $advanceAccountConfigs = SystemGlCodeScenario::where('slug','account-payable-advance-account')
                        ->with(['detail'=>function($query) use($selectedCompanyId){
                            $query->where('companySystemID',$selectedCompanyId);
                        }])                        
                        ->whereHas('detail',function($query) use($selectedCompanyId){
                            $query->where('companySystemID',$selectedCompanyId);
                        })
                        ->first();

        $output = array('advanceAccountConfigs' => $advanceAccountConfigs,
                        'unbilledAccountConfigs' => $unbilledAccountConfigs,
                        'liabilityAccountConfigs' => $liabilityAccountConfigs
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
            'reportingCurrency.required' => 'Reporting Currency ID is required.'
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
            'reportingCurrency' => 'required|numeric|min:1'
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
            try {
                $extension = $attachment['fileType'];
                $allowExtensions = ['png', 'jpg', 'jpeg'];

                if (!in_array($extension, $allowExtensions)) {
                    return $this->sendError('This type of file not allow to upload.', 500);
                }

                if (isset($attachment['size'])) {
                    if ($attachment['size'] > 2097152) {
                        return $this->sendError("Maximum allowed file size is 2 MB. Please upload lesser than 2 MB.", 500);
                    }
                }

                $file = $attachment['file'];
                $decodeFile = base64_decode($file);

                $input['companyLogo'] = $input['CompanyID'] . '_logo.' . $extension;

                if ($awsPolicy) {
                    $path = $input['CompanyID'] . '/logos/' . $input['companyLogo'];
                } else {
                    $path = 'logos/' . $input['companyLogo'];
                }

                $input['logoPath'] = $path;
                Storage::disk($disk)->put($path, $decodeFile);
            } catch(Exception $ex){
                return $this->sendError('It is not possible to upload the company logo at the moment. Please contact the System Administrator', 500);
            }
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
        $company = $this->companyRepository->with('reportingcurrency')->findWithoutFail($id);

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
        
        
        unset($input['reportingcurrency']);
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
            'reportingCurrency.required' => 'Reporting Currency ID is required.'
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
            'reportingCurrency' => 'required|numeric|min:1'
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
            try {
                $extension = $attachment['fileType'];
                $allowExtensions = ['png', 'jpg', 'jpeg'];

                if (!in_array($extension, $allowExtensions)) {
                    return $this->sendError('This type of file not allow to upload.', 500);
                }

                if (isset($attachment['size'])) {
                    if ($attachment['size'] > 2097152) {
                        return $this->sendError("Maximum allowed file size is 2 MB. Please upload lesser than 2 MB.", 500);
                    }
                }

                $file = $attachment['file'];

                $decodeFile = base64_decode($file);

                $input['companyLogo'] = $input['CompanyID'] . '_logo.' . $extension;

                if ($awsPolicy) {
                    $path = $input['CompanyID'] . '/logos/' . $input['companyLogo'];
                } else {
                    $path = 'logos/' . $input['companyLogo'];
                }

                if ($exists = Storage::disk($disk)->exists($path)) {
                    Storage::disk($disk)->delete($path);
                }
                $input['logoPath'] = $path;

                Storage::disk($disk)->put($path, $decodeFile);
            } catch(Exception $ex){
                return $this->sendError('It is not possible to upload the company logo at the moment. Please contact the System Administrator', 500);
            }
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

        $selectedCompanyId  = isset($input['companySystemID']) ? $input['companySystemID'] : null;

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

    public function uploadDigitalStamp(Request $request){
        try {
            $input = $request->all();
            $imageData = $input['item_path'];
            $companySystemID = $input['companySystemID'];
            unset($input['item_path']);

            $masterData = [];
            $path_dir['path'] = '';

            $disk = Helper::policyWiseDisk($companySystemID, 'public');

            if ($imageData != null || !empty($imageData)) {
                foreach ($imageData as $key => $val) {
                    // $path_dir['path'] = '';
                    if (preg_match('/^https/', $val['path'])) {
                        $path_dir['path'] = $val['db_path'];
                    } else {
                        $t = time();
                        $tem = substr($t, 5);
                        $valtt = $this->quickRandom();
                        $random_words = $valtt . '_' . $tem;
                        if (Helper::checkPolicy($input['companySystemID'], 50)) {
                            $base_path = $companySystemID . '/G_ERP/company-setting/digital-stamps/';
                        } else {

                            $base_path = 'company-setting/digital-stamps/';
                        }

                        $path_dir = $this->storeImage($val['path'], $random_words, $base_path, $disk);

                        $masterData = [
                            'path' => $path_dir,
                            'is_default' => false,
                            'company_system_id' => $companySystemID
                        ];

                        $digitalStampUpload = $this->companyDigitalStampRepository->create($masterData);
                    }
                }
            }
            return $this->sendResponse($digitalStampUpload, 'Digital Stamp Uploaded successfully');
        } catch (\Exception $e) {
            return $this->sendError('It is not possible to upload the digital stamp at the moment. Please contact the System Administrator', 500);
        }
    }

    private function storeImage($imageData, $picName, $picBasePath,$disk)
    {
        if (preg_match('/^data:image\/(\w+);base64,/', $imageData, $type)) {
            $imageData = substr($imageData, strpos($imageData, ',') + 1);
            $type = strtolower($type[1]); 

            if (!in_array($type, ['jpg', 'jpeg', 'png'])) {
                throw new Exception('invalid image type');
            }

            $imageData = base64_decode($imageData);

            if ($imageData === false) {
                throw new Exception('image decode failed');
            }

            $picNameExtension = "{$picName}.{$type}";
            $picFullPath = $picBasePath . $picNameExtension;
            Storage::disk($disk)->put($picFullPath, $imageData);
        } else if (preg_match('/^https/', $imageData)) {
            $imageData = basename($imageData);
            $picFullPath = $picBasePath;
        } else {
            throw new Exception('did not match data URI with image data');
        }

        return $picFullPath;
    }

    public function updateDefaultStamp(Request $request){
        $input = $request->all();
        $id = $input['id'];
        $company_system_id = $input['company_system_id'];
        $is_default = $input['is_default'];

        $reverseMasterData=[
            'company_system_id'=>$company_system_id,
            'is_default'=>0
        ];

        $masterData=[
            'company_system_id'=>$company_system_id,
            'is_default'=>$is_default
        ];

        
        $removeIsDefault= CompanyDigitalStamp::where('company_system_id', $company_system_id)->update($reverseMasterData);

        $updateIsDefault =CompanyDigitalStamp::where('id', $id)->update($masterData);

        return $this->sendResponse($updateIsDefault , 'Default digital stamp updated successfully');
    }

    public function getDigitalStamps(Request $request){
        $input = $request->all();
        $companySystemID = $input['companySystemID'];
        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $digitalStamps = CompanyDigitalStamp::where('company_system_id', $companySystemID);
        return \DataTables::eloquent($digitalStamps)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('id', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->addColumn('Actions', 'Actions', "Actions")
            ->rawColumns(['image_url'])
            ->make(true);
    }

    public static function quickRandom($length = 6)
    {
        $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    
        return substr(str_shuffle(str_repeat($pool, 2)), 0, $length);
    }

    public function getChartOfAccountsForDropwdown(Request $request) {
        $selectedCompanyId = $request['selectedCompanyId'];

        $liabilityAccount = ChartOfAccount::whereHas('chartofaccount_assigned', function($query) use ($selectedCompanyId) {
            $query->where('companySystemID', $selectedCompanyId)
                ->where('isAssigned', -1)
                ->where('isActive', 1);
        })->where('controllAccountYN', '=', 1)
            ->where('isApproved',1)
            ->where('isActive',1)
            ->where('controlAccountsSystemID', 4)
            ->where('catogaryBLorPL', '=', 'BS')
            ->orderBy('AccountDescription', 'asc')
            ->get();

        $assetAndLiabilityAccount = ChartOfAccount::whereHas('chartofaccount_assigned', function($query) use ($selectedCompanyId) {
            $query->where('companySystemID', $selectedCompanyId)
                ->where('isAssigned', -1)
                ->where('isActive', 1);
        })->where(function ($query)  {
            $query->where('controlAccountsSystemID', 3)
                ->orWhere('controlAccountsSystemID', 4);
        })
            ->where('isBank',0)
            ->where('isApproved',1)
            ->where('isActive',1)
            ->where('catogaryBLorPL', '=', 'BS')
            ->whereHas('chartofaccount_assigned',function($query) use($selectedCompanyId){
                $query->where('companySystemID',$selectedCompanyId)->where('isAssigned',-1);
            })
            ->orderBy('AccountDescription', 'asc')
            ->get();

        $discountsChartOfAccounts = ChartOfAccount::whereHas('chartofaccount_assigned', function($query) use ($selectedCompanyId) {
            $query->where('companySystemID', $selectedCompanyId)
                ->where('isAssigned', -1)
                ->where('isActive', 1);
        })->where('isApproved',1)
            ->where('isApproved',1)
            ->where('isActive',1)
            ->where('catogaryBLorPL', '=', 'PL')
            ->orderBy('AccountDescription', 'asc')
            ->get();


        $chartOfAccounts = ChartOfAccount::where('controllAccountYN', '=', 1)
            ->whereHas('chartofaccount_assigned', function($query) use ($selectedCompanyId) {
                $query->where('companySystemID', $selectedCompanyId)
                    ->where('isAssigned', -1)
                    ->where('isActive', 1);
            })
            ->where('isApproved',1)
            ->where('isActive',1)
            ->where('controlAccountsSystemID',3)
            ->where('catogaryBLorPL', '=', 'BS')
            ->orderBy('AccountDescription', 'asc')
            ->get();


        $liabilityAccountsCOA =  ChartOfAccount::where('controllAccountYN', '=', 1)
            ->whereHas('chartofaccount_assigned', function($query) use ($selectedCompanyId) {
                $query->where('companySystemID', $selectedCompanyId)
                    ->where('isAssigned', -1)
                    ->where('isActive', 1);
            })
            ->where(function($q){
                $q->where('controlAccountsSystemID',3)
                ->orWhere('controlAccountsSystemID',4)
                ->orWhere('controlAccountsSystemID',5);
             })
            ->where('isApproved',1)
            ->where('isActive',1)
            ->where('catogaryBLorPL', '=', 'BS')
            ->orderBy('AccountDescription', 'asc')
            ->get();

        $controlAccountBalanceGlCOA = ChartOfAccount::where('controllAccountYN', '=', 1)
            ->whereHas('chartofaccount_assigned', function($query) use ($selectedCompanyId) {
                $query->where('companySystemID', $selectedCompanyId)
                    ->where('isAssigned', -1)
                    ->where('isActive', 1);
            })
            ->where('isApproved',1)
            ->where('isActive',1)
            ->where('catogaryBLorPL', '=', 'BS')
            ->orderBy('AccountDescription', 'asc')
            ->get();

        $output = array(
            'liabilityAccountsCOA' => $liabilityAccountsCOA,
            'chartOfAccounts' => $chartOfAccounts,
            'discountsChartOfAccounts' => $discountsChartOfAccounts,
            'assetAndLiabilityAccountCOA' => $assetAndLiabilityAccount,
            'liabilityAccount' => $liabilityAccount,
            'controlAccountBalanceGlCOA' => $controlAccountBalanceGlCOA,
            'assetAndLiabilityAccount' => $assetAndLiabilityAccount

        );
        return $this->sendResponse($output, 'Record retrieved successfully');


    }

}
