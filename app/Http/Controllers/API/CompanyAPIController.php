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

use App\helper\Helper;
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
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Illuminate\Support\Facades\DB;
use Response;

/**
 * Class CompanyController
 * @package App\Http\Controllers\API
 */
class CompanyAPIController extends AppBaseController
{
    /** @var  CompanyRepository */
    private $companyRepository;

    public function __construct(CompanyRepository $companyRepo)
    {
        $this->companyRepository = $companyRepo;
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

        $companies = $this->companyRepository->create($input);

        return $this->sendResponse($companies->toArray(), 'Company saved successfully');
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

        /** @var Company $company */
        $company = $this->companyRepository->findWithoutFail($id);

        if (empty($company)) {
            return $this->sendError('Company not found');
        }

        $company = $this->companyRepository->update($input, $id);

        return $this->sendResponse($company->toArray(), 'Company updated successfully');
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

        $companies = Company::whereIn('companySystemID',$childCompanies)->with(['reportingcurrency','localcurrency','exchange_gl','country']);

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
            ->with('orderCondition', $sort)
            ->addColumn('Actions', 'Actions', "Actions")
            ->make(true);
    }

    public function getCompanyFormData(Request $request){
        $input = $request->all();
        $selectedCompanyId = $input['companySystemID'];

        $isGroup = \Helper::checkIsCompanyGroup($selectedCompanyId);
        if ($isGroup) {
            $companies = \Helper::getGroupCompany($selectedCompanyId);
        } else {
            $companies = [$selectedCompanyId];
        }
        $chartOfAccount = ChartOfAccountsAssigned::where('isApproved', 1)->whereIn('companySystemID', $companies)->groupBy('chartOfAccountSystemID')->get();
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
            'chartOfAccount' => $chartOfAccount
        );
        return $this->sendResponse($output, 'Record retrieved successfully');
    }


}
