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

use App\Http\Requests\API\CreateCompanyAPIRequest;
use App\Http\Requests\API\UpdateCompanyAPIRequest;
use App\Models\Company;
use App\Models\ChartOfAccount;
use App\Models\CountryMaster;
use App\Models\SupplierCategoryMaster;
use App\Models\CurrencyMaster;
use App\Models\SupplierImportance;
use App\Models\SupplierNature;
use App\Models\SupplierContactType;
use App\Models\YesNoSelection;
use App\Models\SupplierCritical;
use App\Models\SupplierType;
use App\Repositories\CompanyRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
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

        $masterCompany = Company::where("companySystemID",$selectedCompanyId)->first();

        /** all Company  Drop Down */
        $allCompanies = Company::where("isGroup",0)->get();

        /**  Companies by group  Drop Down */
        $companies = Company::where("masterComapanyID",$masterCompany->CompanyID)
                              ->where("isGroup",0)->get();

        /**Chart of Account Drop Down */
        $liabilityAccount = ChartOfAccount::where('controllAccountYN', '=', 1)
            ->where('catogaryBLorPL', '=', 'BS')
            ->orderBy('AccountDescription', 'asc')
            ->get();

        /**Country Drop Down */
        $country = CountryMaster::orderBy('countryName', 'asc')
            ->get();

        /** Supplier category  */
        $supplierCategory = SupplierCategoryMaster:: orderBy('categoryDescription', 'asc')
            ->get();

        /** Currency Master */
        $currencyMaster = CurrencyMaster:: orderBy('CurrencyName', 'asc')
            ->get();

        /** Supplier Importance */
        $supplierNature = SupplierNature::all();

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
            'contactTypes' => $contactTypes
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
}
