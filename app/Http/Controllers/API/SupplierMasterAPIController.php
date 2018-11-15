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
 * -- Date: 06-June 2018 By: Mubashir Description: Modified getSupplierMasterByCompany() to handle filters from local storage
 * -- Date: 25-June 2018 By: Mubashir Description: Added new functions named as getSearchSupplierByCompany()
 * -- Date: 17-July 2018 By: Fayas Description: Added new functions named as getSupplierMasterAudit()
 * -- Date: 18-July 2018 By: Fayas Description: Added new functions named as exportSupplierMaster()
 * -- Date: 04-November 2018 By: Fayas Description: Added new functions named as printSuppliers()
 */

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateSupplierMasterAPIRequest;
use App\Http\Requests\API\UpdateSupplierMasterAPIRequest;
use App\Models\Company;
use App\Models\CountryMaster;
use App\Models\ProcumentOrder;
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
        $input = $this->convertArrayToSelectedValue($input, array('supplierCountryID', 'isCriticalYN', 'isActive','supplierConfirmedYN','approvedYN'));
        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }
        $supplierId = 'supplierCodeSytem';
        if($request['type'] == 'all'){
            $supplierId = 'supplierCodeSystem';
        }

        $search = $request->input('search.value');
        $supplierMasters = $this->getSuppliersByFilterQry($input,$search);

        return \DataTables::eloquent($supplierMasters)
            ->order(function ($query) use ($input,$supplierId) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy($supplierId, $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->addColumn('Actions', 'Actions', "Actions")
            ->make(true);
    }

    /**
     * export to csv Supplier Master
     * POST /exportSupplierMaster
     *
     * @param Request $request
     *
     * @return Response
     */
    public function exportSupplierMaster(Request $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input, array('supplierCountryID', 'supplierNatureID', 'isActive'));
        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }
        $search = $request->input('search.value');
        $supplierId = 'supplierCodeSytem';
        if($request['type'] == 'all'){
            $supplierId = 'supplierCodeSystem';
        }
        $supplierMasters = $this->getSuppliersByFilterQry($input,$search)->orderBy($supplierId,$sort)->get();
        $data = array();
        $x = 0;
        foreach ($supplierMasters as $val) {
            $x++;
            $data[$x]['Supplier Code'] = $val->primarySupplierCode;
            $data[$x]['Supplier Name'] = $val->supplierName;
            $currency = "";
            $country = "";
            if(count($val['supplierCurrency']) > 0){
                if($val['supplierCurrency'][0]['currencyMaster']) {
                    $currency = $val['supplierCurrency'][0]['currencyMaster']['CurrencyCode'];
                }
            }

            if($val['country']){
                $country = $val['country']['countryName'];
            }

             $data[$x]['Country'] = $country;
             $data[$x]['Currency'] = $currency;
             $data[$x]['Address'] = $val->address;
             $data[$x]['Telephone'] = $val->telephone;
             $data[$x]['Fax'] = $val->fax;
             $data[$x]['Email'] = $val->supEmail;
             $data[$x]['Website'] = $val->webAddress;
             $data[$x]['Credit Limit'] = $val->creditLimit;
             $data[$x]['Credit Period'] = $val->creditPeriod;
        }

        $csv = \Excel::create('supplier_master', function ($excel) use ($data) {
            $excel->sheet('sheet name', function ($sheet) use ($data) {
                $sheet->fromArray($data, null, 'A1', true);
                //$sheet->getStyle('A1')->getAlignment()->setWrapText(true);
                $sheet->setAutoSize(true);
                $sheet->getStyle('C1:C2')->getAlignment()->setWrapText(true);
            });
            $lastrow = $excel->getActiveSheet()->getHighestRow();
            $excel->getActiveSheet()->getStyle('A1:J' . $lastrow)->getAlignment()->setWrapText(true);
        })->download('csv');

        return $this->sendResponse([], 'Supplier Masters export to CSV successfully');
    }

    public function printSuppliers(Request $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input, array('supplierCountryID', 'supplierNatureID', 'isActive'));
        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }
        $search = $request->input('search.value');
        $supplierId = 'supplierCodeSytem';
        if($request['type'] == 'all'){
            $supplierId = 'supplierCodeSystem';
        }
        $supplierMasters = $this->getSuppliersByFilterQry($input,$search)->orderBy($supplierId,$sort)->get();

        $company = Company::find( $request['companyId']);

        if(empty($company)){
            return $this->sendError('Company not found');
        }

        $docRefNo = \Helper::getCompanyDocRefNo( $request['companyId'], 56);

        $array = array('entities' => $supplierMasters,'docRefNo' => $docRefNo,'company' => $company);
        $time = strtotime("now");
        $fileName = 'suppliers_'. $time . '.pdf';
        $html = view('print.suppliers', $array);
        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($html);

        return $pdf->setPaper('a4','landscape')->setWarnings(false)->stream($fileName);
    }

    public function getSuppliersByFilterQry($request,$search){

        $input = $request;
        $companyId = $request['companyId'];

        $isGroup = \Helper::checkIsCompanyGroup($companyId);

        if ($isGroup) {
            $childCompanies = \Helper::getGroupCompany($companyId);
        } else {
            $childCompanies = [$companyId];
        }

        if($request['type'] == 'all'){
            $supplierMasters = SupplierMaster::with(['categoryMaster','critical','country','supplierCurrency' => function ($query) {
                $query->where('isDefault', -1)
                    ->with(['currencyMaster']);
            }]);
        }else{
            //by_company
            $supplierMasters = SupplierAssigned::with(['categoryMaster','critical','country','supplierCurrency' => function ($query) {
                $query->where('isDefault', -1)
                    ->with(['currencyMaster']);
            }])->whereIn('CompanySystemID', $childCompanies);

        }

        if (array_key_exists('supplierCountryID', $input)) {
            if ($input['supplierCountryID'] && !is_null($input['supplierCountryID'])) {
                $supplierMasters->where('supplierCountryID', '=', $input['supplierCountryID']);
            }
        }

        if (array_key_exists('isCriticalYN', $input)) {
            if ($input['isCriticalYN'] && !is_null($input['isCriticalYN'])) {
                $supplierMasters->where('isCriticalYN', '=', $input['isCriticalYN']);
            }
        }

        if (array_key_exists('supplierConfirmedYN', $input)) {
            if (($input['supplierConfirmedYN'] == 0 || $input['supplierConfirmedYN'] == 1) && !is_null($input['supplierConfirmedYN'])) {
                $supplierMasters->where('supplierConfirmedYN', '=', $input['supplierConfirmedYN']);
            }
        }

        if (array_key_exists('approvedYN', $input)) {
            if (($input['approvedYN'] == 0 || $input['approvedYN'] == 1) && !is_null($input['approvedYN'])) {
                $supplierMasters->where('approvedYN', '=', $input['approvedYN']);
            }
        }

        if (array_key_exists('supplierNatureID', $input)) {
            if ($input['supplierNatureID']  && !is_null($input['supplierNatureID'])) {
                $supplierMasters->where('supplierNatureID', '=', $input['supplierNatureID']);
            }
        }

        if (array_key_exists('isActive', $input)) {
            if (($input['isActive'] == 0 || $input['isActive'] == 1) && !is_null($input['isActive'])) {
                $supplierMasters->where('isActive', '=', $input['isActive']);
            }
        }

        if ($search) {
            $supplierMasters = $supplierMasters->where(function ($query) use ($search) {
                $query->where('primarySupplierCode', 'LIKE', "%{$search}%")
                    ->orWhere('supplierName', 'LIKE', "%{$search}%");
            });
        }

        return $supplierMasters;

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

        $companyId = $request->selectedCompanyID;

        $isGroup = \Helper::checkIsCompanyGroup($companyId);

        if ($isGroup) {
            $companyID = \Helper::getGroupCompany($companyId);
        } else {
            $companyID = [$companyId];
        }

        $empID = \Helper::getEmployeeSystemID();

        $search = $request->input('search.value');

        $supplierMasters = DB::table('erp_documentapproved')
            ->select('suppliermaster.*', 'erp_documentapproved.documentApprovedID',
                'rollLevelOrder', 'currencymaster.CurrencyCode',
                'suppliercategorymaster.categoryDescription', 'approvalLevelID', 'documentSystemCode')
            ->join('employeesdepartments', function ($query) use ($companyID, $empID) {
                $query->on('erp_documentapproved.approvalGroupID', '=', 'employeesdepartments.employeeGroupID')->
                on('erp_documentapproved.documentSystemID', '=', 'employeesdepartments.documentSystemID')->
                on('erp_documentapproved.companySystemID', '=', 'employeesdepartments.companySystemID')
                    ->where('employeesdepartments.documentSystemID', 56)
                    ->whereIn('employeesdepartments.companySystemID', $companyID)
                    ->where('employeesdepartments.employeeSystemID', $empID);
            })
            ->join('suppliermaster', function ($query) use ($companyID, $empID, $search) {
                $query->on('erp_documentapproved.documentSystemCode', '=', 'supplierCodeSystem')
                    ->on('erp_documentapproved.rollLevelOrder', '=', 'RollLevForApp_curr')
                    ->whereIn('primaryCompanySystemID', $companyID)
                    ->where('suppliermaster.approvedYN', 0)
                    ->where('suppliermaster.supplierConfirmedYN', 1)
                    ->when($search != "", function ($q) use ($search) {
                        $q->where(function ($query) use ($search) {
                            $query->where('primarySupplierCode', 'LIKE', "%{$search}%")
                                ->orWhere('supplierName', 'LIKE', "%{$search}%");
                        });
                    });
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
        $input = $this->convertArrayToValue($request->all());
        $employee = \Helper::getEmployeeInfo();

        $input['createdPcID'] = gethostname();
        $input['createdUserID'] = $employee->empID;
        $input['createdUserSystemID'] = $employee->employeeSystemID;

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


        $updateSupplierMasters = SupplierMaster::where('supplierCodeSystem', $supplierMasters['supplierCodeSystem'])->first();
        $updateSupplierMasters->primarySupplierCode = 'S0' . strval($supplierMasters->supplierCodeSystem);

        $updateSupplierMasters->save();

        return $this->sendResponse($supplierMasters->toArray(), 'Supplier Master saved successfully');
    }


    public function updateSupplierMaster(Request $request)
    {
        $input = $request->all();
        $input = array_except($input, ['supplierConfirmedEmpID', 'supplierConfirmedEmpSystemID',
            'supplierConfirmedEmpName','supplierConfirmedDate','final_approved_by']);
        $input = $this->convertArrayToValue($input);
        $employee = \Helper::getEmployeeInfo();
        $input['modifiedPc']   = gethostname();
        $input['modifiedUser'] = $employee->empID;
        $input['modifiedUserSystemID'] =  $employee->employeeSystemID;

        $company = Company::where('companySystemID', $input['primaryCompanySystemID'])->first();

        if ($company) {
            $input['primaryCompanyID'] = $company->CompanyID;
        }

        $isConfirm = $input['supplierConfirmedYN'];
        //unset($input['companySystemID']);

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

        if ($isConfirm && $supplierMaster->supplierConfirmedYN == 0) {

            $checkDefaultCurrency = SupplierCurrency::where('supplierCodeSystem', $id)
                ->where('isDefault', -1)
                ->count();

            if ($checkDefaultCurrency == 0) {
                return $this->sendError("Default currency not found. Setup currency in currency tab", 500);
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
        $supplierMaster = $this->supplierMasterRepository->with(['finalApprovedBy'])->findWithoutFail($id);

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
        $supplierMaster = SupplierAssigned::where('companySystemID', $request->selectedCompanyId)->get();
        return $this->sendResponse($supplierMaster, 'Supplier Master retrieved successfully');
    }

    public function getPOSuppliers(Request $request)
    {
        $companyId = $request->selectedCompanyId;
        $isGroup = \Helper::checkIsCompanyGroup($companyId);
        if ($isGroup) {
            $companyID = \Helper::getGroupCompany($companyId);
        } else {
            $companyID = [$companyId];
        }

        $filterSuppliers = ProcumentOrder::whereIN('companySystemID', $companyID)
            ->select('supplierID')
            ->groupBy('supplierID')
            ->pluck('supplierID');
        $supplierMaster = SupplierAssigned::whereIN('companySystemID', $companyID)->whereIN('supplierCodeSytem', $filterSuppliers)->groupBy('supplierCodeSytem')->get();
        return $this->sendResponse($supplierMaster, 'Supplier Master retrieved successfully');
    }


    /**
     *  Search Supplier By Company
     * GET /getSearchSupplierByCompany
     *
     * @param  int $id
     *
     * @return Response
     */
    public function getSearchSupplierByCompany(Request $request)
    {

        $companyId = $request->companyId;
        $input = $request->all();
        $isGroup = \Helper::checkIsCompanyGroup($companyId);

        if ($isGroup) {
            $companies = \Helper::getGroupCompany($companyId);
        } else {
            $companies = [$companyId];
        }

        $suppliers = SupplierAssigned::whereIn('companySystemID', $companies)
            ->select(['supplierCodeSytem', 'supplierName', 'primarySupplierCode'])
            ->when(request('search', false), function ($q, $search) {
                return $q->where(function ($query) use ($search) {
                    return $query->where('primarySupplierCode', 'LIKE', "%{$search}%")
                        ->orWhere('supplierName', 'LIKE', "%{$search}%");
                });
            })
            ->take(20)
            ->get();


        return $this->sendResponse($suppliers->toArray(), 'Data retrieved successfully');
    }

    /**
     * Display the specified Supplier Master Audit.
     * GET|HEAD /getSupplierMasterAudit
     *
     * @param  int $id
     *
     * @return Response
     */
    public function getSupplierMasterAudit(Request $request)
    {
        $id = $request->get('id');

        $materielRequest = $this->supplierMasterRepository
            ->with(['created_by', 'confirmed_by', 'modified_by', 'approved_by' => function ($query) {
                $query->with('employee')
                    ->where('documentSystemID', 56);
            }])
            ->findWithoutFail($id);

        if (empty($materielRequest)) {
            return $this->sendError('Supplier Master not found');
        }

        return $this->sendResponse($materielRequest->toArray(), 'Materiel Issue retrieved successfully');
    }
}
