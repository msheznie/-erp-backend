<?php
/**
 * =============================================
 * -- File Name : SupplierMasterAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Supplier Master
 * -- Author : Mohamed Fayas
 * -- Create date : 14 - March 2018
 * -- Description : This file contains the all CRUD for Supplier Master
 * -- REVISION HISTORY
 * -- Date: 14-March 2018 By: Fayas Description: Added new functions named as getSupplierMasterByCompany(),getAssignedCompaniesBySupplier(),
 */

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateSupplierMasterAPIRequest;
use App\Http\Requests\API\UpdateSupplierMasterAPIRequest;
use App\Models\Company;
use App\Models\CountryMaster;
use App\Models\SupplierAssigned;
use App\Models\SupplierCurrency;
use App\Models\DocumentApproved;
use App\Models\SupplierMaster;
use App\Models\DocumentMaster;
use App\Models\ChartOfAccount;
use App\Repositories\SupplierMasterRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\Input;
use Illuminate\Validation\Rules\In;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use App\Criteria\FilterSupplierMasterByCompanyCriteria;
use Illuminate\Support\Facades\DB;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;

/**
 * Class SupplierMasterController
 * @package App\Http\Controllers\API
 */
class SupplierMasterAPIController extends AppBaseController
{
    /** @var  SupplierMasterRepository */
    private $supplierMasterRepository;
    private $userRepository;

    public function __construct(SupplierMasterRepository $supplierMasterRepo, UserRepository $userRepo)
    {
        $this->supplierMasterRepository = $supplierMasterRepo;
        $this->userRepository = $userRepo;
    }

    /**
     * Display a listing of the SupplierMaster.
     * GET|HEAD /supplierMasters
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        //$this->supplierMasterRepository->pushCriteria(new FilterSupplierMasterByCompanyCriteria($request));

        //return $this->supplierMasterRepository->getFieldsSearchable();

        $this->supplierMasterRepository->pushCriteria(new RequestCriteria($request));

        $this->supplierMasterRepository->pushCriteria(new LimitOffsetCriteria($request));

        $supplierMasters = $this->supplierMasterRepository
            //->with(['categoryMaster','employee'])
            ->paginate($request->get('limit'));
        //->all();


        return $this->sendResponse($supplierMasters->toArray(), 'Supplier Masters retrieved successfully');
    }

    /**
     * get supplier master by company.
     * POST /getSupplierMasterByCompany
     *
     * @param Request $request
     *
     * @return Response
     */
    public function getSupplierMasterByCompany(Request $request)
    {
        $input = $request->all();

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $companyId = $request['companyId'];

        $isGroup = \Helper::checkIsCompanyGroup($companyId);

        if($isGroup){
            $childCompanies = \Helper::getGroupCompany($companyId);
        }else{
            $childCompanies = [$companyId];
        }

        $supplierMasters = SupplierMaster::with(['categoryMaster', 'employee', 'supplierCurrency' => function ($query) {
                                    $query->where('isDefault', -1)
                                        ->with(['currencyMaster']);
                                }])
                                ->whereIn('primaryCompanySystemID',$childCompanies);

        $search = $request->input('search.value');
        if($search){
            $supplierMasters =   $supplierMasters->where(function ($query) use($search) {
                    $query->where('primarySupplierCode','LIKE',"%{$search}%")
                           ->orWhere( 'supplierName', 'LIKE', "%{$search}%");
                });
        }

        return \DataTables::eloquent($supplierMasters)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('supplierCodeSystem', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->addColumn('Actions', 'Actions', "Actions")
            //->addColumn('Index', 'Index', "Index")
            ->make(true);
        ///return $this->sendResponse($supplierMasters->toArray(), 'Supplier Masters retrieved successfully');*/
    }


    /**
     * get supplier master approval by company.
     * POST /getAllSupplierMasterApproval
     *
     * @param Request $request
     *
     * @return Response
     */
    public function getAllSupplierMasterApproval(Request $request)
    {
        $input = $request->all();

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $companyID = $request->selectedCompanyID;
        $companyID = \Helper::getGroupCompany($companyID);
        $empID = \Helper::getEmployeeSystemID();

        $supplierMasters = DB::table('erp_documentapproved')->select('suppliermaster.*','erp_documentapproved.documentApprovedID','rollLevelOrder','currencymaster.CurrencyCode','suppliercategorymaster.categoryDescription','approvalLevelID','documentSystemCode')
            ->join('employeesdepartments', function ($query) use ($companyID, $empID) {
                $query->on('erp_documentapproved.approvalGroupID', '=', 'employeesdepartments.employeeGroupID')->
                on('erp_documentapproved.documentSystemID', '=', 'employeesdepartments.documentSystemID')->
                on('erp_documentapproved.companySystemID', '=', 'employeesdepartments.companySystemID')
                    ->where('employeesdepartments.documentSystemID', 56)
                    ->whereIn('employeesdepartments.companySystemID', $companyID)
                    ->where('employeesdepartments.employeeSystemID', $empID);
            })
            ->join('suppliermaster', function ($query) use ($companyID, $empID) {
                $query->on('erp_documentapproved.documentSystemCode', '=', 'supplierCodeSystem')
                    ->on('erp_documentapproved.rollLevelOrder', '=', 'RollLevForApp_curr')
                    ->whereIn('primaryCompanySystemID', $companyID)
                    ->where('suppliermaster.approvedYN', 0)
                    ->where('suppliermaster.supplierConfirmedYN', 1);
            })
            ->leftJoin('suppliercategorymaster', 'suppliercategorymaster.supCategoryMasterID', '=', 'suppliermaster.supCategoryMasterID')
            ->leftJoin('currencymaster', 'suppliermaster.currency', '=', 'currencymaster.currencyID')
            ->where('erp_documentapproved.approvedYN', 0)
            ->where('erp_documentapproved.rejectedYN', 0)
            ->where('erp_documentapproved.documentSystemID', 56)
            ->whereIn('erp_documentapproved.companySystemID', $companyID);

        return \DataTables::of($supplierMasters)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('documentApprovedID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->addColumn('Actions', 'Actions', "Actions")
            ->make(true);

    }

    /**
     * get sub categories by supplier.
     * POST /getSubcategoriesBySupplier
     *
     * @param Request $request
     *
     * @return Response
     */
    public function getSubcategoriesBySupplier(Request $request)
    {

        $supplierId = $request['supplierId'];
        $supplier = SupplierMaster::where('supplierCodeSystem', '=', $supplierId)
            ->first();
        if ($supplier) {
            $suppliersubcategory = DB::table('suppliersubcategoryassign')
                ->leftJoin('suppliercategorysub', 'suppliersubcategoryassign.supSubCategoryID', '=', 'suppliercategorysub.supCategorySubID')
                ->where('supplierID', $supplierId)
                ->orderBy('supplierSubCategoryAssignID', 'DESC')->get();
        } else {
            $suppliersubcategory = [];
        }

        return $this->sendResponse($suppliersubcategory, 'Supplier Category Subs retrieved successfully');
    }


    /**
     * Store a newly created SupplierMaster in storage.
     * POST /supplierMasters
     *
     * @param CreateSupplierMasterAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateSupplierMasterAPIRequest $request)
    {
        $input = $request->all();

        $id = Auth::id();
        $user = $this->userRepository->with(['employee'])->findWithoutFail($id);

        $empId = $user->employee['empID'];
        $input['createdPcID'] = gethostname();
        $input['createdUserID'] = $empId;

        $input['uniqueTextcode'] = 'S';

        if (array_key_exists('supplierCountryID', $input)) {
            $input['countryID'] = $input['supplierCountryID'];
        }

        $company = Company::where('companySystemID', $input['primaryCompanySystemID'])->first();

        $input['primaryCompanyID'] = $company->CompanyID;


        $document = DocumentMaster::where('documentID', 'SUPM')->first();

        $input['documentSystemID'] = $document->documentSystemID;
        $input['documentID'] = $document->documentID;

        $input['isActive'] = 1;
        //$input['isCriticalYN'] = 1;

        $liabilityAccountSysemID = ChartOfAccount::where('chartOfAccountSystemID', $input['liabilityAccountSysemID'])->first();
        $unbilledGRVAccountSystemID = ChartOfAccount::where('chartOfAccountSystemID', $input['UnbilledGRVAccountSystemID'])->first();

        $input['liabilityAccount'] = $liabilityAccountSysemID['AccountCode'];
        $input['UnbilledGRVAccount'] = $unbilledGRVAccountSystemID['AccountCode'];

        $supplierMasters = $this->supplierMasterRepository->create($input);


        $updateSupplierMasters = SupplierMaster::where('supplierCodeSystem',$supplierMasters['supplierCodeSystem'])->first();
        $updateSupplierMasters->primarySupplierCode = 'S0'.strval($supplierMasters->supplierCodeSystem);

        $updateSupplierMasters->save();

        return $this->sendResponse($supplierMasters->toArray(), 'Supplier Master saved successfully');
    }


    public function updateSupplierMaster(Request $request)
    {
        $input = $this->convertArrayToValue($request->all());
        $userId = Auth::id();
        $user = $this->userRepository->with(['employee'])->findWithoutFail($userId);
        $empId = $user->employee['empID'];
        $input['modifiedPc'] = gethostname();
        $input['modifiedUser'] = $empId;
        $empName = $user->employee['empName'];


        $company = Company::where('companySystemID', $input['primaryCompanySystemID'])->first();


        if($company){
            $input['primaryCompanyID'] = $company->CompanyID;
        }

        $isConfirm = $input['supplierConfirmedYN'];
        //unset($input['companySystemID']);
        unset($input['supplierConfirmedYN']);
        unset($input['supplierConfirmedEmpID']);
        unset($input['supplierConfirmedEmpSystemID']);
        unset($input['supplierConfirmedEmpName']);
        unset($input['supplierConfirmedDate']);

        $id = $input['supplierCodeSystem'];

        if (array_key_exists('supplierCountryID', $input)) {
            $input['countryID'] = $input['supplierCountryID'];
        }

        $liabilityAccountSysemID = ChartOfAccount::where('chartOfAccountSystemID', $input['liabilityAccountSysemID'])->first();
        $unbilledGRVAccountSystemID = ChartOfAccount::where('chartOfAccountSystemID', $input['UnbilledGRVAccountSystemID'])->first();

        $input['liabilityAccount'] = $liabilityAccountSysemID['AccountCode'];
        $input['UnbilledGRVAccount'] = $unbilledGRVAccountSystemID['AccountCode'];

        $supplierMaster = SupplierMaster::where('supplierCodeSystem', $id)->first();

        if (empty($supplierMaster)) {
            return $this->sendError('Supplier Master not found');
        }

        if($isConfirm && $supplierMaster->supplierConfirmedYN == 0){

            $checkDefaultCurrency = SupplierCurrency::where('supplierCodeSystem',$id)
                                                           ->where('isDefault',-1)
                                                           ->count();

            if($checkDefaultCurrency == 0){
                return $this->sendError("Default currency not found. Setup currency in currency tab",500);
            }


            $params = array('autoID' => $id, 'company' => $input["primaryCompanySystemID"], 'document' => $input["documentSystemID"]);
            $confirm = \Helper::confirmDocument($params);
            if (!$confirm["success"]) {
                return $this->sendError($confirm["message"]);
            }
        }

        $supplierMaster = $this->supplierMasterRepository->update($input, $id);

        return $this->sendResponse($supplierMaster->toArray(), 'SupplierMaster updated successfully');
    }


    public function getAssignedCompaniesBySupplier(Request $request)
    {
        $supplierId = $request['supplierId'];
        $supplier = SupplierMaster::where('supplierCodeSystem', '=', $supplierId)
            ->first();
        if ($supplier) {
            $supplierCompanies = DB::table('supplierassigned')
                ->leftJoin('supplierimportance', 'supplierassigned.supplierImportanceID', '=', 'supplierimportance.supplierImportanceID')
                ->leftJoin('suppliernature', 'supplierassigned.supplierNatureID', '=', 'suppliernature.supplierNatureID')
                ->leftJoin('suppliertype', 'supplierassigned.supplierTypeID', '=', 'suppliertype.supplierTypeID')
                ->leftJoin('suppliercritical', 'supplierassigned.isCriticalYN', '=', 'suppliercritical.suppliercriticalID')
                ->leftJoin('yesnoselection', 'supplierassigned.isActive', '=', 'yesnoselection.idyesNoselection')
                ->where('supplierCodeSytem', $supplierId)
                ->orderBy('supplierAssignedID', 'DESC')
                ->get();
        } else {
            $supplierCompanies = [];
        }

        return $this->sendResponse($supplierCompanies, 'Supplier Category Subs retrieved successfully');
    }

    /**
     * Display the specified SupplierMaster.
     * GET|HEAD /supplierMasters/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var SupplierMaster $supplierMaster */
        $supplierMaster = $this->supplierMasterRepository->findWithoutFail($id);
        //$supplierMaster = SupplierMaster::where("supplierCodeSystem", $id)->first();

        if (empty($supplierMaster)) {
            return $this->sendError('Supplier Master not found');
        }

        return $this->sendResponse($supplierMaster->toArray(), 'Supplier Master retrieved successfully');
    }

    /**
     * Update the specified SupplierMaster in storage.
     * PUT/PATCH /supplierMasters/{id}
     *
     * @param  int $id
     * @param UpdateSupplierMasterAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateSupplierMasterAPIRequest $request)
    {
        $input = $request->all();

        /** @var SupplierMaster $supplierMaster */
        $supplierMaster = $this->supplierMasterRepository->findWithoutFail($id);

        if (empty($supplierMaster)) {
            return $this->sendError('Supplier Master not found');
        }

        $supplierMaster = $this->supplierMasterRepository->update($input, $id);

        return $this->sendResponse($supplierMaster->toArray(), 'SupplierMaster updated successfully');
    }

    /**
     * Remove the specified SupplierMaster from storage.
     * DELETE /supplierMasters/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var SupplierMaster $supplierMaster */
        $supplierMaster = $this->supplierMasterRepository->findWithoutFail($id);

        if (empty($supplierMaster)) {
            return $this->sendError('Supplier Master not found');
        }

        $supplierMaster->delete();

        return $this->sendResponse($id, 'Supplier Master deleted successfully');
    }


    public function approveSupplier(Request $request)
    {
        $approve = \Helper::approveDocument($request);
        if (!$approve["success"]) {
            return $this->sendError($approve["message"]);
        } else {
            return $this->sendResponse(array(), $approve["message"]);
        }

    }

    public function rejectSupplier(Request $request)
    {
        $reject = \Helper::rejectDocument($request);
        if (!$reject["success"]) {
            return $this->sendError($reject["message"]);
        } else {
            return $this->sendResponse(array(), $reject["message"]);
        }

    }

    public function getSuppliersByCompany(Request $request)
    {
        $supplierMaster = SupplierAssigned::where('companySystemID',$request->selectedCompanyId)->get();
        return $this->sendResponse($supplierMaster, 'Supplier Master retrieved successfully');
    }
}
