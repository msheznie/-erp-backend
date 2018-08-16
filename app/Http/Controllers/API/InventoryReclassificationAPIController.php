<?php
/**
 * =============================================
 * -- File Name : InventoryReclassificationAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Inventory
 * -- Author : Mohamed Mubashir
 * -- Create date : 10 - August 2018
 * -- Description : This file contains the all CRUD for Inventory Reclassification
 * -- REVISION HISTORY
 * -- Date: 14-March 2018 By: Description: Added new functions named as checkUser(),userCompanies()
 */

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateInventoryReclassificationAPIRequest;
use App\Http\Requests\API\UpdateInventoryReclassificationAPIRequest;
use App\Models\Company;
use App\Models\DocumentMaster;
use App\Models\InventoryReclassification;
use App\Models\InventoryReclassificationDetail;
use App\Models\ItemAssigned;
use App\Models\Months;
use App\Models\SegmentMaster;
use App\Models\WarehouseMaster;
use App\Models\YesNoSelection;
use App\Repositories\InventoryReclassificationRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class InventoryReclassificationController
 * @package App\Http\Controllers\API
 */
class InventoryReclassificationAPIController extends AppBaseController
{
    /** @var  InventoryReclassificationRepository */
    private $inventoryReclassificationRepository;

    public function __construct(InventoryReclassificationRepository $inventoryReclassificationRepo)
    {
        $this->inventoryReclassificationRepository = $inventoryReclassificationRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/inventoryReclassifications",
     *      summary="Get a listing of the InventoryReclassifications.",
     *      tags={"InventoryReclassification"},
     *      description="Get all InventoryReclassifications",
     *      produces={"application/json"},
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  type="array",
     *                  @SWG\Items(ref="#/definitions/InventoryReclassification")
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function index(Request $request)
    {
        $this->inventoryReclassificationRepository->pushCriteria(new RequestCriteria($request));
        $this->inventoryReclassificationRepository->pushCriteria(new LimitOffsetCriteria($request));
        $inventoryReclassifications = $this->inventoryReclassificationRepository->all();

        return $this->sendResponse($inventoryReclassifications->toArray(), 'Inventory Reclassifications retrieved successfully');
    }

    /**
     * @param CreateInventoryReclassificationAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/inventoryReclassifications",
     *      summary="Store a newly created InventoryReclassification in storage",
     *      tags={"InventoryReclassification"},
     *      description="Store InventoryReclassification",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="InventoryReclassification that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/InventoryReclassification")
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/InventoryReclassification"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateInventoryReclassificationAPIRequest $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToValue($input);

        $validator = \Validator::make($request->all(), [
            'serviceLineSystemID' => 'required',
            'narration' => 'required',
            'inventoryReclassificationDate' => 'required|date',
        ]);

        if ($validator->fails()) {//echo 'in';exit;
            return $this->sendError($validator->messages(), 422);
        }

        $companyFinanceYear = \Helper::companyFinanceYearCheck($input);
        if (!$companyFinanceYear["success"]) {
            return $this->sendError($companyFinanceYear["message"], 500);
        }

        $inputParam = $input;
        $inputParam["departmentSystmeID"] = 10;
        $companyFinancePeriod = \Helper::companyFinancePeriodCheck($inputParam);
        if (!$companyFinancePeriod["success"]) {
            return $this->sendError($companyFinancePeriod["message"], 500);
        } else {
            $input['FYBiggin'] = $companyFinancePeriod["message"]->dateFrom;
            $input['FYEnd'] = $companyFinancePeriod["message"]->dateTo;
        }

        unset($inputParam);
        $input['inventoryReclassificationDate'] = new Carbon($input['inventoryReclassificationDate']);

        $monthBegin = $input['FYBiggin'];
        $monthEnd = $input['FYEnd'];

        if (($input['inventoryReclassificationDate'] >= $monthBegin) && ($input['inventoryReclassificationDate'] <= $monthEnd)) {
        } else {
            return $this->sendError('Reclassification date not between financial period!', 500);
        }

        $segment = SegmentMaster::find($input['serviceLineSystemID']);
        if ($segment) {
            $input['serviceLineCode'] = $segment->ServiceLineCode;
        }

        $company = Company::find($input['companySystemID']);
        if ($company) {
            $input['companyID'] = $company->CompanyID;
        }

        $documentMaster = DocumentMaster::find($input['documentSystemID']);
        if ($documentMaster) {
            $input['documentID'] = $documentMaster->documentID;
        }

        $lastSerial = InventoryReclassification::where('companySystemID', $input['companySystemID'])
            ->where('companyFinanceYearID', $input['companyFinanceYearID'])
            ->orderBy('inventoryreclassificationID', 'desc')
            ->first();

        $lastSerialNumber = 1;
        if ($lastSerial) {
            $lastSerialNumber = intval($lastSerial->serialNo) + 1;
        }

        if ($companyFinanceYear["message"]) {
            $startYear = $companyFinanceYear["message"]['bigginingDate'];
            $finYearExp = explode('-', $startYear);
            $finYear = $finYearExp[0];
        } else {
            $finYear = date("Y");
        }
        if ($documentMaster) {
            $documentCode = ($company->CompanyID . '\\' . $finYear . '\\' . $documentMaster->documentID . str_pad($lastSerialNumber, 6, '0', STR_PAD_LEFT));
            $input['documentCode'] = $documentCode;
        }
        $input['serialNo'] = $lastSerialNumber;
        $input['createdPCid'] = gethostname();
        $input['createdUserID'] = \Helper::getEmployeeID();
        $input['createdUserSystemID'] = \Helper::getEmployeeSystemID();

        $inventoryReclassifications = $this->inventoryReclassificationRepository->create($input);

        return $this->sendResponse($inventoryReclassifications->toArray(), 'Inventory Reclassification saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/inventoryReclassifications/{id}",
     *      summary="Display the specified InventoryReclassification",
     *      tags={"InventoryReclassification"},
     *      description="Get InventoryReclassification",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of InventoryReclassification",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/InventoryReclassification"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function show($id)
    {
        /** @var InventoryReclassification $inventoryReclassification */
        $inventoryReclassification = $this->inventoryReclassificationRepository->with(['confirmed_by','created_by','financeperiod_by'=> function($query){
            $query->selectRaw("CONCAT(DATE_FORMAT(dateFrom,'%d/%m/%Y'),' | ',DATE_FORMAT(dateTo,'%d/%m/%Y')) as financePeriod,companyFinancePeriodID");
        },'financeyear_by'=> function($query){
            $query->selectRaw("CONCAT(DATE_FORMAT(bigginingDate,'%d/%m/%Y'),' | ',DATE_FORMAT(endingDate,'%d/%m/%Y')) as financeYear,companyFinanceYearID");
        }])->findWithoutFail($id);

        if (empty($inventoryReclassification)) {
            return $this->sendError('Inventory Reclassification not found');
        }

        return $this->sendResponse($inventoryReclassification->toArray(), 'Inventory Reclassification retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateInventoryReclassificationAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/inventoryReclassifications/{id}",
     *      summary="Update the specified InventoryReclassification in storage",
     *      tags={"InventoryReclassification"},
     *      description="Update InventoryReclassification",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of InventoryReclassification",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="InventoryReclassification that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/InventoryReclassification")
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/InventoryReclassification"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateInventoryReclassificationAPIRequest $request)
    {
        $input = $request->all();
        $input = array_except($input, ['created_by','confirmedByName','financeperiod_by','financeyear_by',
            'confirmedByEmpID','confirmedDate','confirmed_by','confirmedByEmpSystemID']);
        $input = $this->convertArrayToValue($input);

        $validator = \Validator::make($request->all(), [
            'serviceLineSystemID' => 'required',
            'narration' => 'required',
            'inventoryReclassificationDate' => 'required|date',
        ]);

        if ($validator->fails()) {//echo 'in';exit;
            return $this->sendError($validator->messages(), 422);
        }

        /** @var InventoryReclassification $inventoryReclassification */
        $inventoryReclassification = $this->inventoryReclassificationRepository->findWithoutFail($id);

        if (empty($inventoryReclassification)) {
            return $this->sendError('Inventory Reclassification not found');
        }

        $input['inventoryReclassificationDate'] = new Carbon($input['inventoryReclassificationDate']);

        if ($input['serviceLineSystemID']) {
            $checkDepartmentActive = SegmentMaster::find($input['serviceLineSystemID']);
            if (empty($checkDepartmentActive)) {
                return $this->sendError('Department not found');
            }
            if ($checkDepartmentActive->isActive == 0) {
                return $this->sendError('Please select a active department', 500);
            }
            $input['serviceLineCode'] = $checkDepartmentActive->ServiceLineCode;
        }

        if ($inventoryReclassification->confirmedYN == 0 && $input['confirmedYN'] == 1) {

            $companyFinanceYear = \Helper::companyFinanceYearCheck($input);
            if (!$companyFinanceYear["success"]) {
                return $this->sendError($companyFinanceYear["message"], 500);
            }

            $inputParam = $input;
            $inputParam["departmentSystmeID"] = 10;
            $companyFinancePeriod = \Helper::companyFinancePeriodCheck($inputParam);
            if (!$companyFinancePeriod["success"]) {
                return $this->sendError($companyFinancePeriod["message"], 500);
            } else {
                $input['FYBiggin'] = $companyFinancePeriod["message"]->dateFrom;
                $input['FYEnd'] = $companyFinancePeriod["message"]->dateTo;
            }

            unset($inputParam);

            $monthBegin = $input['FYBiggin'];
            $monthEnd = $input['FYEnd'];

            if (($input['inventoryReclassificationDate'] >= $monthBegin) && ($input['inventoryReclassificationDate'] <= $monthEnd)) {
            } else {
                return $this->sendError('Reclassification date not between financial period!', 500);
            }

            $checkItems = InventoryReclassificationDetail::where('inventoryreclassificationID', $id)
                ->count();
            if ($checkItems == 0) {
                return $this->sendError('Every recalssification should have at least one item', 500);
            }

            $checkQuantity = InventoryReclassificationDetail::where('inventoryreclassificationID', $id)
                ->where(function ($q) {
                    $q->where('currentStockQty', '<=', 0)
                        ->orWhereNull('currentStockQty');
                })
                ->count();
            if ($checkQuantity > 0) {
                return $this->sendError('Every Item should have at least one minimum Qty', 500);
            }

            $amount = InventoryReclassificationDetail::where('inventoryreclassificationID', $id)
                ->sum('unitCostRpt');
            $input['RollLevForApp_curr'] = 1;
            $params = array('autoID' => $id,
                'company' => $inventoryReclassification->companySystemID,
                'document' => $inventoryReclassification->documentSystemID,
                'segment' => $input['serviceLineSystemID'],
                'category' => 0,
                'amount' => $amount
            );

            $confirm = \Helper::confirmDocument($params);
            if (!$confirm["success"]) {
                return $this->sendError($confirm["message"], 500);
            }
        }

        $input['modifiedPc'] = gethostname();
        $input['modifiedUser'] = \Helper::getEmployeeID();
        $input['modifiedUserSystemID'] = \Helper::getEmployeeSystemID();

        $inventoryReclassification = $this->inventoryReclassificationRepository->update($input, $id);

        return $this->sendResponse($inventoryReclassification->toArray(), 'Inventory reclassification updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/inventoryReclassifications/{id}",
     *      summary="Remove the specified InventoryReclassification from storage",
     *      tags={"InventoryReclassification"},
     *      description="Delete InventoryReclassification",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of InventoryReclassification",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function destroy($id)
    {
        /** @var InventoryReclassification $inventoryReclassification */
        $inventoryReclassification = $this->inventoryReclassificationRepository->findWithoutFail($id);

        if (empty($inventoryReclassification)) {
            return $this->sendError('Inventory Reclassification not found');
        }

        $inventoryReclassification->delete();

        return $this->sendResponse($id, 'Inventory Reclassification deleted successfully');
    }


    public function getAllInvReclassificationByCompany(Request $request)
    {

        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input, array('segment_by', 'created_by'));

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $selectedCompanyId = $request['companyID'];
        $isGroup = \Helper::checkIsCompanyGroup($selectedCompanyId);

        if ($isGroup) {
            $subCompanies = \Helper::getGroupCompany($selectedCompanyId);
        } else {
            $subCompanies = [$selectedCompanyId];
        }

        $invReclassification = InventoryReclassification::with(['segment_by', 'created_by'])->whereIN('companySystemID', $subCompanies);

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $invReclassification = $invReclassification->where(function ($query) use ($search) {
                $query->where('documentCode', 'LIKE', "%{$search}%")
                    ->orWhere('narration', 'LIKE', "%{$search}%");
            });
        }

        return \DataTables::eloquent($invReclassification)
            ->addColumn('Actions', 'Actions', "Actions")
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('inventoryreclassificationID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    public function getInvReclassificationFormData(Request $request)
    {

        $companyId = $request['companyId'];

        $segments = SegmentMaster::where("companySystemID", $companyId);
        if (isset($request['type']) && $request['type'] != 'filter') {
            $segments = $segments->where('isActive', 1);
        }
        $segments = $segments->get();

        $financialYears = array(array('value' => intval(date("Y")), 'label' => date("Y")),
            array('value' => intval(date("Y", strtotime("-1 year"))), 'label' => date("Y", strtotime("-1 year"))));

        $companyFinanceYear = \Helper::companyFinanceYear($companyId);
        /** Yes and No Selection */
        $yesNoSelection = YesNoSelection::all();

        $month = Months::all();

        $years = InventoryReclassification::select(DB::raw("YEAR(createdDateTime) as year"))
            ->whereNotNull('createdDateTime')
            ->groupby('year')
            ->orderby('year', 'desc')
            ->get();

        $output = array(
            'segments' => $segments,
            'financialYears' => $financialYears,
            'companyFinanceYear' => $companyFinanceYear,
            'yesNoSelection' => $yesNoSelection,
            'month' => $month,
            'years' => $years,
        );

        return $this->sendResponse($output, 'Record retrieved successfully');
    }


    public function getItemsOptionForReclassification(Request $request)
    {
        $input = $request->all();
        $companyID = $input['companyID'];
        $items = ItemAssigned::where('companySystemID', $companyID)->where('financeCategoryMaster', 1);
        if (array_key_exists('search', $input)) {
            $search = $input['search'];
            $items = $items->where(function ($query) use ($search) {
                $query->where('itemPrimaryCode', 'LIKE', "%{$search}%")
                    ->orWhere('itemDescription', 'LIKE', "%{$search}%")
                    ->orWhere('secondaryItemCode', 'LIKE', "%{$search}%");
            });
        }

        $items = $items->take(20)->get();
        return $this->sendResponse($items->toArray(), 'Data retrieved successfully');
    }


    public function getInvReclassificationAudit(Request $request)
    {
        $id = $request->get('id');
        $invReclassification = $this->inventoryReclassificationRepository->getAudit($id);

        if (empty($invReclassification)) {
            return $this->sendError('Inventory Reclassification not found');
        }

        $invReclassification->docRefNo = \Helper::getCompanyDocRefNo($invReclassification->companySystemID,$invReclassification->documentSystemID);

        return $this->sendResponse($invReclassification->toArray(), 'Inventory reclassification retrieved successfully');
    }


    public function getInvReclassificationApprovalByUser(Request $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input, array('serviceLineSystemID', 'confirmedYN', 'approved', 'month', 'year'));

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $companyId = $input['companyId'];
        $empID = \Helper::getEmployeeSystemID();

        $search = $request->input('search.value');
        $itemIssueMaster = DB::table('erp_documentapproved')
            ->select(
                'erp_inventoryreclassification.*',
                'employees.empName As created_emp',
                'serviceline.ServiceLineDes As ServiceLineDes',
                'erp_documentapproved.documentApprovedID',
                'rollLevelOrder',
                'approvalLevelID',
                'documentSystemCode')
            ->join('employeesdepartments', function ($query) use ($companyId, $empID) {
                $query->on('erp_documentapproved.approvalGroupID', '=', 'employeesdepartments.employeeGroupID')
                    ->on('erp_documentapproved.documentSystemID', '=', 'employeesdepartments.documentSystemID')
                    ->on('erp_documentapproved.companySystemID', '=', 'employeesdepartments.companySystemID');

                $query->whereIn('employeesdepartments.documentSystemID', [61])
                    ->where('employeesdepartments.companySystemID', $companyId)
                    ->where('employeesdepartments.employeeSystemID', $empID);
            })
            ->join('erp_inventoryreclassification', function ($query) use ($companyId, $search) {
                $query->on('erp_documentapproved.documentSystemCode', '=', 'inventoryreclassificationID')
                    ->on('erp_documentapproved.rollLevelOrder', '=', 'RollLevForApp_curr')
                    ->where('erp_inventoryreclassification.companySystemID', $companyId)
                    ->where('erp_inventoryreclassification.approved', 0)
                    ->where('erp_inventoryreclassification.confirmedYN', 1);
            })
            ->where('erp_documentapproved.approvedYN', 0)
            ->leftJoin('employees', 'createdUserSystemID', 'employees.employeeSystemID')
            ->leftJoin('serviceline', 'erp_inventoryreclassification.serviceLineSystemID', 'serviceline.serviceLineSystemID')
            ->where('erp_documentapproved.rejectedYN', 0)
            ->whereIn('erp_documentapproved.documentSystemID', [61])
            ->where('erp_documentapproved.companySystemID', $companyId);


        if (array_key_exists('serviceLineSystemID', $input)) {
            if ($input['serviceLineSystemID'] && !is_null($input['serviceLineSystemID'])) {
                $itemIssueMaster->where('erp_inventoryreclassification.serviceLineSystemID', $input['serviceLineSystemID']);
            }
        }


        if (array_key_exists('month', $input)) {
            if ($input['month'] && !is_null($input['month'])) {
                $itemIssueMaster->whereMonth('erp_inventoryreclassification.issueDate', '=', $input['month']);
            }
        }

        if (array_key_exists('year', $input)) {
            if ($input['year'] && !is_null($input['year'])) {
                $itemIssueMaster->whereYear('erp_inventoryreclassification.issueDate', '=', $input['year']);
            }
        }

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $itemIssueMaster = $itemIssueMaster->where(function ($query) use ($search) {
                $query->where('documentCode', 'LIKE', "%{$search}%")
                    ->orWhere('narration', 'LIKE', "%{$search}%");
            });
        }

        return \DataTables::of($itemIssueMaster)
            ->addColumn('Actions', 'Actions', "Actions")
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('inventoryreclassificationID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);

    }

    public function getInvReclassificationApprovedByUser(Request $request)
    {

        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input, array('serviceLineSystemID', 'confirmedYN', 'approved', 'month', 'year'));

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $companyId = $input['companyId'];
        $empID = \Helper::getEmployeeSystemID();

        $search = $request->input('search.value');
        $itemIssueMaster = DB::table('erp_documentapproved')
            ->select(
                'erp_inventoryreclassification.*',
                'employees.empName As created_emp',
                'serviceline.ServiceLineDes As ServiceLineDes',
                'erp_documentapproved.documentApprovedID',
                'rollLevelOrder',
                'approvalLevelID',
                'documentSystemCode')
            ->join('erp_inventoryreclassification', function ($query) use ($companyId, $search) {
                $query->on('erp_documentapproved.documentSystemCode', '=', 'inventoryreclassificationID')
                    ->where('erp_inventoryreclassification.companySystemID', $companyId)
                    ->where('erp_inventoryreclassification.confirmedYN', 1);
            })
            ->where('erp_documentapproved.approvedYN', -1)
            ->leftJoin('employees', 'createdUserSystemID', 'employees.employeeSystemID')
            ->leftJoin('serviceline', 'erp_inventoryreclassification.serviceLineSystemID', 'serviceline.serviceLineSystemID')
            ->where('erp_documentapproved.rejectedYN', 0)
            ->whereIn('erp_documentapproved.documentSystemID', [61])
            ->where('erp_documentapproved.companySystemID', $companyId)
            ->where('erp_documentapproved.employeeSystemID', $empID);

        if (array_key_exists('serviceLineSystemID', $input)) {
            if ($input['serviceLineSystemID'] && !is_null($input['serviceLineSystemID'])) {
                $itemIssueMaster->where('erp_inventoryreclassification.serviceLineSystemID', $input['serviceLineSystemID']);
            }
        }

        if (array_key_exists('month', $input)) {
            if ($input['month'] && !is_null($input['month'])) {
                $itemIssueMaster->whereMonth('erp_inventoryreclassification.issueDate', '=', $input['month']);
            }
        }

        if (array_key_exists('year', $input)) {
            if ($input['year'] && !is_null($input['year'])) {
                $itemIssueMaster->whereYear('erp_inventoryreclassification.issueDate', '=', $input['year']);
            }
        }

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $itemIssueMaster = $itemIssueMaster->where(function ($query) use ($search) {
                $query->where('documentCode', 'LIKE', "%{$search}%")
                    ->orWhere('narration', 'LIKE', "%{$search}%");
            });
        }

        return \DataTables::of($itemIssueMaster)
            ->addColumn('Actions', 'Actions', "Actions")
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('inventoryreclassificationID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

}
