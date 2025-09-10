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
 * -- Date: 14-August 2018 By:Mubashir Description: Added new functions named as getAllInvReclassificationByCompany(),getInvReclassificationFormData(),getItemsOptionForReclassification()
 * -- Date: 17-August 2018 By:Mubashir Description: Added new functions named as getInvReclassificationAudit(),getInvReclassificationApprovalByUser(),getInvReclassificationApprovedByUser()
 */

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateInventoryReclassificationAPIRequest;
use App\Http\Requests\API\UpdateInventoryReclassificationAPIRequest;
use App\Models\Company;
use App\Models\CompanyDocumentAttachment;
use App\Models\DocumentApproved;
use App\Models\DocumentMaster;
use App\Models\SystemGlCodeScenarioDetail;
use App\Models\EmployeesDepartment;
use App\Models\InventoryReclassification;
use App\Models\InventoryReclassificationDetail;
use App\Models\ItemAssigned;
use App\Models\Months;
use App\Models\SegmentMaster;
use App\Models\WarehouseMaster;
use App\Models\YesNoSelection;
use App\Repositories\InventoryReclassificationRepository;
use App\Traits\AuditTrial;
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

        return $this->sendResponse($inventoryReclassifications->toArray(), trans('custom.inventory_reclassifications_retrieved_successfully'));
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
            'inventoryReclassificationDate' => 'required|date|before_or_equal:today',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        $companyFinanceYear = \Helper::companyFinanceYearCheck($input);
        if (!$companyFinanceYear["success"]) {
            return $this->sendError($companyFinanceYear["message"], 500);
        }

        $inputParam = $input;
        $inputParam["departmentSystemID"] = 10;
        $companyFinancePeriod = \Helper::companyFinancePeriodCheck($inputParam);
        if (!$companyFinancePeriod["success"]) {
            return $this->sendError($companyFinancePeriod["message"], 500);
        } else {
            $input['FYBiggin'] = $companyFinancePeriod["message"]->dateFrom;
            $input['FYEnd'] = $companyFinancePeriod["message"]->dateTo;
        }

        unset($inputParam);
        DB::beginTransaction();
        $input['inventoryReclassificationDate'] = new Carbon($input['inventoryReclassificationDate']);

        $monthBegin = $input['FYBiggin'];
        $monthEnd = $input['FYEnd'];

        if (($input['inventoryReclassificationDate'] >= $monthBegin) && ($input['inventoryReclassificationDate'] <= $monthEnd)) {
        } else {
            DB::rollBack();
            return $this->sendError('Reclassification date is not within financial period!', 500);
        }

        $segment = SegmentMaster::find($input['serviceLineSystemID']);
        if ($segment) {
            $input['serviceLineCode'] = $segment->ServiceLineCode;
        }

        $warehouse = WarehouseMaster::find($input['wareHouseSystemCode']);
        if ($warehouse) {
            $input['wareHouseCode'] = $warehouse->wareHouseCode;
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
            ->lockForUpdate()
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
        DB::commit();
        return $this->sendResponse($inventoryReclassifications->toArray(), trans('custom.inventory_reclassification_saved_successfully'));
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
        $inventoryReclassification = $this->inventoryReclassificationRepository->with(['confirmed_by', 'created_by', 'financeperiod_by' => function ($query) {
            $query->selectRaw("CONCAT(DATE_FORMAT(dateFrom,'%d/%m/%Y'),' | ',DATE_FORMAT(dateTo,'%d/%m/%Y')) as financePeriod,companyFinancePeriodID");
        }, 'financeyear_by' => function ($query) {
            $query->selectRaw("CONCAT(DATE_FORMAT(bigginingDate,'%d/%m/%Y'),' | ',DATE_FORMAT(endingDate,'%d/%m/%Y')) as financeYear,companyFinanceYearID");
        }, 'segment_by', 'warehouse_by'])->findWithoutFail($id);

        if (empty($inventoryReclassification)) {
            return $this->sendError(trans('custom.inventory_reclassification_not_found'));
        }

        return $this->sendResponse($inventoryReclassification->toArray(), trans('custom.inventory_reclassification_retrieved_successfully'));
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
        $input = array_except($input, ['created_by', 'confirmedByName', 'financeperiod_by', 'financeyear_by',
            'confirmedByEmpID', 'confirmedDate', 'confirmed_by', 'confirmedByEmpSystemID', 'segment_by', 'warehouse_by']);
        $input = $this->convertArrayToValue($input);

        $validator = \Validator::make($request->all(), [
            'serviceLineSystemID' => 'required',
            'wareHouseSystemCode' => 'required',
            'narration' => 'required',
            'inventoryReclassificationDate' => 'required|date|before_or_equal:today'
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        /** @var InventoryReclassification $inventoryReclassification */
        $inventoryReclassification = $this->inventoryReclassificationRepository->findWithoutFail($id);

        if (empty($inventoryReclassification)) {
            return $this->sendError(trans('custom.inventory_reclassification_not_found'));
        }

        $input['inventoryReclassificationDate'] = new Carbon($input['inventoryReclassificationDate']);

        if ($input['serviceLineSystemID']) {
            $checkDepartmentActive = SegmentMaster::find($input['serviceLineSystemID']);
            if (empty($checkDepartmentActive)) {
                return $this->sendError(trans('custom.department_not_found'));
            }
            if ($checkDepartmentActive->isActive == 0) {
                return $this->sendError('Please select an active department', 500);
            }
            $input['serviceLineCode'] = $checkDepartmentActive->ServiceLineCode;
        }

        if ($input['wareHouseSystemCode']) {
            $checkWarehouseActive = WarehouseMaster::find($input['wareHouseSystemCode']);
            if (empty($checkWarehouseActive)) {
                return $this->sendError(trans('custom.warehouse_not_found'));
            }
            if ($checkWarehouseActive->isActive == 0) {
                return $this->sendError('Please select an active warehouse', 500);
            }
            $input['wareHouseCode'] = $checkWarehouseActive->wareHouseCode;
        }

        if ($inventoryReclassification->confirmedYN == 0 && $input['confirmedYN'] == 1) {

            $companyFinanceYear = \Helper::companyFinanceYearCheck($input);
            if (!$companyFinanceYear["success"]) {
                return $this->sendError($companyFinanceYear["message"], 500);
            }

            $inputParam = $input;
            $inputParam["departmentSystemID"] = 10;
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
                return $this->sendError('Reclassification date is not within financial period!', 500);
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
                return $this->sendError('Every item should have at least one minimum Qty', 500);
            }

            $finalError = array(
                'currentStockQty_notEqualTo_currentWareHouseStockQty' => array());
            $error_count = 0;

            $inventoryReclassificationDetail = InventoryReclassificationDetail::where('inventoryreclassificationID', $id)->get();
            if ($inventoryReclassificationDetail) {
                foreach ($inventoryReclassificationDetail as $val) {
                    $updateItem = InventoryReclassificationDetail::find($val['inventoryReclassificationDetailID']);
                    $data = array('companySystemID' => $inventoryReclassification->companySystemID,
                        'itemCodeSystem' => $updateItem->itemSystemCode,
                        'wareHouseId' => $inventoryReclassification->wareHouseSystemCode);
                    $itemCurrentCostAndQty = \Inventory::itemCurrentCostAndQty($data);
                    $updateItem->currentStockQty = $itemCurrentCostAndQty['currentStockQty'];
                    $updateItem->currentWareHouseStockQty = $itemCurrentCostAndQty['currentWareHouseStockQty'];
                    $updateItem->unitCostLocal = $itemCurrentCostAndQty['wacValueLocal'];
                    $updateItem->unitCostRpt = $itemCurrentCostAndQty['wacValueReporting'];
                    $updateItem->save();

                    if ($updateItem->currentStockQty != $updateItem->currentWareHouseStockQty) {
                        array_push($finalError['currentStockQty_notEqualTo_currentWareHouseStockQty'], $updateItem->itemPrimaryCode);
                        $error_count++;
                    }
                }
            }

            $checkPlAccount = SystemGlCodeScenarioDetail::getGlByScenario($inventoryReclassification->companySystemID, $inventoryReclassification->documentSystemID, "inventory-reclassification-bs-account");

            if (is_null($checkPlAccount)) {
                return $this->sendError('Please configure BS account for inventory recalssification', 500);
            }

            $confirm_error = array('type' => 'confirm_error', 'data' => $finalError);
            if ($error_count > 0) {
                return $this->sendError("You cannot confirm this document.", 500, $confirm_error);
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

        return $this->sendReponseWithDetails($inventoryReclassification->toArray(), 'Inventory reclassification updated successfully',1,$confirm['data'] ?? null);
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
            return $this->sendError(trans('custom.inventory_reclassification_not_found'));
        }

        $inventoryReclassification->delete();

        return $this->sendResponse($id, trans('custom.inventory_reclassification_deleted_successfully'));
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

        $search = $request->input('search.value');
        
        $invReclassification = $this->inventoryReclassificationRepository->inventoryReclassificationListQuery($request, $input, $search);

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

        $isGroup = \Helper::checkIsCompanyGroup($companyId);

        if ($isGroup) {
            $subCompanies = \Helper::getGroupCompany($companyId);
        } else {
            $subCompanies = [$companyId];
        }

        $segments = SegmentMaster::whereIn("companySystemID", $subCompanies);
        $wareHouses = WarehouseMaster::whereIn('companySystemID', $subCompanies);
        if (isset($request['type']) && $request['type'] != 'filter') {
            $segments = $segments->where('isActive', 1);
            $wareHouses = $wareHouses->where('isActive', 1);
        }
        $segments = $segments->get();
        $wareHouses = $wareHouses->get();

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
            'warehouse' => $wareHouses,
            'financialYears' => $financialYears,
            'companyFinanceYear' => $companyFinanceYear,
            'yesNoSelection' => $yesNoSelection,
            'month' => $month,
            'years' => $years,
        );

        return $this->sendResponse($output, trans('custom.record_retrieved_successfully_1'));
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
        return $this->sendResponse($items->toArray(), trans('custom.data_retrieved_successfully'));
    }


    public function getInvReclassificationAudit(Request $request)
    {
        $id = $request->get('id');
        $invReclassification = $this->inventoryReclassificationRepository->getAudit($id);

        if (empty($invReclassification)) {
            return $this->sendError(trans('custom.inventory_reclassification_not_found'));
        }

        $invReclassification->docRefNo = \Helper::getCompanyDocRefNo($invReclassification->companySystemID, $invReclassification->documentSystemID);

        return $this->sendResponse($invReclassification->toArray(), trans('custom.inventory_reclassification_retrieved_successfully_1'));
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
        $reclassifyMaster = DB::table('erp_documentapproved')
            ->select(
                'employeesdepartments.approvalDeligated',
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
                    ->where('employeesdepartments.employeeSystemID', $empID)
                    ->where('employeesdepartments.isActive', 1)
                    ->where('employeesdepartments.removedYN', 0);
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
                $reclassifyMaster->where('erp_inventoryreclassification.serviceLineSystemID', $input['serviceLineSystemID']);
            }
        }


        if (array_key_exists('month', $input)) {
            if ($input['month'] && !is_null($input['month'])) {
                $reclassifyMaster->whereMonth('erp_inventoryreclassification.issueDate', '=', $input['month']);
            }
        }

        if (array_key_exists('year', $input)) {
            if ($input['year'] && !is_null($input['year'])) {
                $reclassifyMaster->whereYear('erp_inventoryreclassification.issueDate', '=', $input['year']);
            }
        }

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $reclassifyMaster = $reclassifyMaster->where(function ($query) use ($search) {
                $query->where('documentCode', 'LIKE', "%{$search}%")
                    ->orWhere('narration', 'LIKE', "%{$search}%");
            });
        }

         $isEmployeeDischarched = \Helper::checkEmployeeDischarchedYN();

        if ($isEmployeeDischarched == 'true') {
            $reclassifyMaster = [];
        }

        return \DataTables::of($reclassifyMaster)
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
        $reclassifyMaster = DB::table('erp_documentapproved')
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
                $reclassifyMaster->where('erp_inventoryreclassification.serviceLineSystemID', $input['serviceLineSystemID']);
            }
        }

        if (array_key_exists('month', $input)) {
            if ($input['month'] && !is_null($input['month'])) {
                $reclassifyMaster->whereMonth('erp_inventoryreclassification.issueDate', '=', $input['month']);
            }
        }

        if (array_key_exists('year', $input)) {
            if ($input['year'] && !is_null($input['year'])) {
                $reclassifyMaster->whereYear('erp_inventoryreclassification.issueDate', '=', $input['year']);
            }
        }

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $reclassifyMaster = $reclassifyMaster->where(function ($query) use ($search) {
                $query->where('documentCode', 'LIKE', "%{$search}%")
                    ->orWhere('narration', 'LIKE', "%{$search}%");
            });
        }

        return \DataTables::of($reclassifyMaster)
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


    public function invRecalssificationReopen(Request $request)
    {
        DB::beginTransaction();
        try {
            $input = $request->all();
            $inventoryreclassificationID = $input['inventoryreclassificationID'];

            $inventoryReclassification = $this->inventoryReclassificationRepository->findWithoutFail($inventoryreclassificationID);
            $emails = array();
            if (empty($inventoryReclassification)) {
                return $this->sendError(trans('custom.inventory_reclassification_not_found'));
            }

            if ($inventoryReclassification->RollLevForApp_curr > 1) {
                return $this->sendError(trans('custom.you_cannot_reopen_this_inventory_reclassification_'));
            }

            if ($inventoryReclassification->approved == -1) {
                return $this->sendError(trans('custom.you_cannot_reopen_this_inventory_reclassification__1'));
            }

            if ($inventoryReclassification->confirmedYN == 0) {
                return $this->sendError(trans('custom.you_cannot_reopen_this_inventory_reclassification__2'));
            }

            // updating fields
            $inventoryReclassification->confirmedYN = 0;
            $inventoryReclassification->confirmedByEmpSystemID = null;
            $inventoryReclassification->confirmedByEmpID = null;
            $inventoryReclassification->confirmedByName = null;
            $inventoryReclassification->confirmedDate = null;
            $inventoryReclassification->RollLevForApp_curr = 1;
            $inventoryReclassification->save();

            $employee = \Helper::getEmployeeInfo();
            $document = DocumentMaster::where('documentSystemID', $inventoryReclassification->documentSystemID)->first();
            $cancelDocNameBody = $document->documentDescription . ' <b>' . $inventoryReclassification->documentCode . '</b>';
            $cancelDocNameSubject = $document->documentDescription . ' ' . $inventoryReclassification->documentCode;
            $subject = $cancelDocNameSubject . ' ' . trans('email.is_reopened');
            $body = '<p>' . $cancelDocNameBody . ' ' . trans('email.is_reopened_by', ['empID' => $employee->empID, 'empName' => $employee->empFullName]) . '</p><p>' . trans('email.comment') . ' : ' . $input['reopenComments'] . '</p>';

            $documentApproval = DocumentApproved::where('companySystemID', $inventoryReclassification->companySystemID)
                ->where('documentSystemCode', $inventoryReclassification->inventoryreclassificationID)
                ->where('documentSystemID', $inventoryReclassification->documentSystemID)
                ->where('rollLevelOrder', 1)
                ->first();

            if ($documentApproval) {
                if ($documentApproval->approvedYN == 0) {
                    $companyDocument = CompanyDocumentAttachment::where('companySystemID', $inventoryReclassification->companySystemID)
                        ->where('documentSystemID', $inventoryReclassification->documentSystemID)
                        ->first();
                    if (empty($companyDocument)) {
                        return ['success' => false, 'message' => 'Policy not found for this document'];
                    }
                    $approvalList = EmployeesDepartment::where('employeeGroupID', $documentApproval->approvalGroupID)
                        ->where('companySystemID', $documentApproval->companySystemID)
                        ->where('documentSystemID', $documentApproval->documentSystemID);

                    if ($companyDocument['isServiceLineApproval'] == -1) {
                        $approvalList = $approvalList->where('ServiceLineSystemID', $documentApproval->serviceLineSystemID);
                    }

                    $approvalList = $approvalList
                        ->with(['employee'])
                        ->groupBy('employeeSystemID')
                        ->get();

                    if ($approvalList) {
                        foreach ($approvalList as $da) {
                            if ($da->employee) {
                                $emails[] = array('empSystemID' => $da->employee->employeeSystemID,
                                    'companySystemID' => $documentApproval->companySystemID,
                                    'docSystemID' => $documentApproval->documentSystemID,
                                    'alertMessage' => $subject,
                                    'emailAlertMessage' => $body,
                                    'docSystemCode' => $documentApproval->documentSystemCode);
                            }
                        }
                    } else {
                        return $this->sendError(trans('custom.approval_list_not_found'), 500);
                    }

                    $sendEmail = \Email::sendEmail($emails);
                    if (!$sendEmail["success"]) {
                        return ['success' => false, 'message' => $sendEmail["message"]];
                    }
                }
            }

            DocumentApproved::where('documentSystemCode', $inventoryreclassificationID)
                ->where('companySystemID', $inventoryReclassification->companySystemID)
                ->where('documentSystemID', $inventoryReclassification->documentSystemID)
                ->delete();

            /*Audit entry*/
            AuditTrial::createAuditTrial($inventoryReclassification->documentSystemID,$inventoryreclassificationID,$input['reopenComments'],'Reopened');

            DB::commit();
            return $this->sendResponse($inventoryReclassification->toArray(), trans('custom.inventory_reclassification_reopened_successfully'));
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError(trans('custom.error_occurred'), 500);
        }
    }

}
