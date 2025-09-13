<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateStockCountAPIRequest;
use App\Http\Requests\API\UpdateStockCountAPIRequest;
use App\Models\StockCount;
use App\Models\Company;
use App\Models\ItemAssigned;
use App\Models\ItemReturnDetails;
use App\Models\ItemIssueDetails;
use App\Models\StockTransferDetails;
use App\Models\StockReceiveDetails;
use App\Models\PurchaseReturnDetails;
use App\Models\DeliveryOrderDetail;
use App\Models\StockCountDetailsRefferedBack;
use App\Models\StockCountRefferedBack;
use App\Models\CustomerInvoiceItemDetails;
use App\Models\InventoryReclassificationDetail;
use App\Models\StockAdjustmentDetails;
use App\Models\GRVDetails;
use App\Models\CompanyDocumentAttachment;
use App\Models\CompanyFinanceYear;
use App\Models\CompanyPolicyMaster;
use App\Models\DocumentApproved;
use App\Models\StockCountDetail;
use App\Models\DocumentMaster;
use App\Models\DocumentReferedHistory;
use App\Models\EmployeesDepartment;
use App\Models\Months;
use App\Models\SegmentMaster;
use App\Models\Unit;
use App\Models\WarehouseMaster;
use App\Models\YesNoSelection;
use App\Models\YesNoSelectionForMinus;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Repositories\StockCountRepository;
use App\Repositories\StockCountDetailRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use App\Traits\AuditTrial;
use App\Jobs\StockCount\StockCountDetailJob;
use Illuminate\Support\Facades\Log;
/**
 * Class StockCountController
 * @package App\Http\Controllers\API
 */

class StockCountAPIController extends AppBaseController
{
    /** @var  StockCountRepository */
    private $stockCountRepository;
    private $stockCountDetailRepository;

    public function __construct(StockCountRepository $stockCountRepo, StockCountDetailRepository $stockCountDetailRepo)
    {
        $this->stockCountRepository = $stockCountRepo;
        $this->stockCountDetailRepository = $stockCountDetailRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/stockCounts",
     *      summary="Get a listing of the StockCounts.",
     *      tags={"StockCount"},
     *      description="Get all StockCounts",
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
     *                  @SWG\Items(ref="#/definitions/StockCount")
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
        $this->stockCountRepository->pushCriteria(new RequestCriteria($request));
        $this->stockCountRepository->pushCriteria(new LimitOffsetCriteria($request));
        $stockCounts = $this->stockCountRepository->all();

        return $this->sendResponse($stockCounts->toArray(), trans('custom.stock_counts_retrieved_successfully'));
    }

    /**
     * @param CreateStockCountAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/stockCounts",
     *      summary="Store a newly created StockCount in storage",
     *      tags={"StockCount"},
     *      description="Store StockCount",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="StockCount that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/StockCount")
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
     *                  ref="#/definitions/StockCount"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        $input = $request->all();

        $input = $this->convertArrayToValue($input);

        $employee = \Helper::getEmployeeInfo();

        $input['createdPCid'] = gethostname();
        $input['createdUserID'] = $employee->empID;
        $input['createdUserSystemID'] = $employee->employeeSystemID;

        $companyFinanceYear = \Helper::companyFinanceYearCheck($input);
        if (!$companyFinanceYear["success"]) {
            DB::rollBack();
            return $this->sendError($companyFinanceYear["message"], 500);
        }

        $inputParam = $input;
        $inputParam["departmentSystemID"] = 10;
        $companyFinancePeriod = \Helper::companyFinancePeriodCheck($inputParam);
        if (!$companyFinancePeriod["success"]) {
            DB::rollBack();
            return $this->sendError($companyFinancePeriod["message"], 500);
        } else {
            $input['FYBiggin'] = $companyFinancePeriod["message"]->dateFrom;
            $input['FYEnd'] = $companyFinancePeriod["message"]->dateTo;
        }
        unset($inputParam);

        $validator = \Validator::make($input, [
            'companyFinancePeriodID' => 'required|numeric|min:1',
            'companyFinanceYearID' => 'required|numeric|min:1',
            'stockCountDate' => 'required|date|before_or_equal:today',
            'serviceLineSystemID' => 'required|numeric|min:1',
            'location' => 'required|numeric|min:1',
            'refNo' => 'required',
            'comment' => 'required',
        ]);

        if ($validator->fails()) {
            DB::rollBack();
            return $this->sendError($validator->messages(), 422);
        }

        if (isset($input['stockCountDate'])) {
            if ($input['stockCountDate']) {
                $input['stockCountDate'] = new Carbon($input['stockCountDate']);
            }
        }

        $documentDate = $input['stockCountDate'];
        $monthBegin = $input['FYBiggin'];
        $monthEnd = $input['FYEnd'];
        if (($documentDate >= $monthBegin) && ($documentDate <= $monthEnd)) {
        } else {
            DB::rollBack();
            return $this->sendError(trans('custom.document_date_not_within_financial_period_stock_count'), 500);
        }

        $input['documentSystemID'] = 97;
        $input['stockCountType'] = 1;
        $input['documentID'] = 'SC';

        $lastSerial = StockCount::where('companySystemID', $input['companySystemID'])
                                ->where('companyFinanceYearID', $input['companyFinanceYearID'])
                                ->orderBy('serialNo', 'desc')
                                ->lockForUpdate()
                                ->first();

        $lastSerialNumber = 1;
        if ($lastSerial) {
            $lastSerialNumber = intval($lastSerial->serialNo) + 1;
        }


        $segment = SegmentMaster::where('serviceLineSystemID', $input['serviceLineSystemID'])->first();
        if ($segment) {
            $input['serviceLineCode'] = $segment->ServiceLineCode;
        }else{
            DB::rollBack();
            return $this->sendError(trans('custom.segment_not_found'),500);
        }

        if ($segment->isActive == 0) {
            DB::rollBack();
            return $this->sendError(trans('custom.please_select_active_segment_stock_count'), 500);
        }

        $warehouse = WarehouseMaster::where('wareHouseSystemCode', $input['location'])->first();
        if (empty($warehouse)) {
            DB::rollBack();
            return $this->sendError(trans('custom.location_not_found'),500);
        }

        if ($warehouse->isActive == 0) {
            DB::rollBack();
            return $this->sendError(trans('custom.please_select_active_location_stock_count'), 500);
        }

        $items = ItemAssigned::where('companySystemID', $input['companySystemID'])
                            ->where('financeCategoryMaster', 1)
                            ->select(['itemPrimaryCode', 'itemDescription', 'itemCodeSystem', 'secondaryItemCode'])
                            ->get();

        $skipItemIds = [];
        if (isset($input['preCheck']) && $input['preCheck']) {
            $checkProducts = $this->stockCountRepository->validateProductsForStockCount($input, $items);

            if (count($checkProducts['usedItems']) > 0) {
                DB::rollBack();
                return $this->sendError(trans('custom.you_cannot_used_these_items_these_items_have_been_'), 500, array('type' => 'used_items', 'used_items' => $checkProducts['usedItems']));
            }
        } else {
            $skipProducts = $this->stockCountRepository->validateProductsForStockCount($input, $items);

            $skipItemIds = $skipProducts['skipItemIds'];
        }


        $finalItems = ItemAssigned::where('companySystemID', $input['companySystemID'])
                            ->where('financeCategoryMaster', 1)
                            ->whereNotIn('itemCodeSystem', $skipItemIds)
                            ->select(['itemPrimaryCode', 'itemDescription', 'itemCodeSystem', 'secondaryItemCode'])
                            ->get();

        $company = Company::where('companySystemID', $input['companySystemID'])->first();
        if ($company) {
            $input['companyID'] = $company->CompanyID;
        }

        $input['serialNo'] = $lastSerialNumber;
        $input['RollLevForApp_curr'] = 1;

        $documentMaster = DocumentMaster::where('documentSystemID', $input['documentSystemID'])->first();

        $companyFinanceYear = CompanyFinanceYear::where('companyFinanceYearID', $input['companyFinanceYearID'])
            ->where('companySystemID', $input['companySystemID'])
            ->first();
        if ($companyFinanceYear) {
            $startYear = $companyFinanceYear['bigginingDate'];
            $finYearExp = explode('-', $startYear);
            $finYear = $finYearExp[0];
        } else {
            $finYear = date("Y");
        }


        if ($documentMaster) {
            $stockCountCode = ($company->CompanyID . '\\' . $finYear . '\\' . $documentMaster['documentID'] . str_pad($lastSerialNumber, 6, '0', STR_PAD_LEFT));
            $input['stockCountCode'] = $stockCountCode;
        }

        try {
            $errorMessage = [];
            $stockCount = $this->stockCountRepository->create($input);
            if ($stockCount) {
                $db = isset($request->db) ? $request->db : "";


                $dataArray = array(
                    'stockCount' => $stockCount,
                    'companySystemID' => $input['companySystemID'],
                    'stockCountAutoID' => $stockCount->stockCountAutoID,
                    'skipItemIds' => $skipItemIds
                );

                StockCountDetailJob::dispatch($db,$dataArray);
        
            }
        
            DB::commit();
            return $this->sendResponse(['stockCount' => $stockCount->toArray(), trans('custom.errormessage') => $errorMessage], trans('custom.stock_count_saved_successfully'));
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage()." ".$exception->getLine());
        }

    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/stockCounts/{id}",
     *      summary="Display the specified StockCount",
     *      tags={"StockCount"},
     *      description="Get StockCount",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of StockCount",
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
     *                  ref="#/definitions/StockCount"
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
        /** @var StockCount $stockCount */
        $stockCount = $this->stockCountRepository->with(['confirmed_by', 'created_by', 'finance_period_by' => function ($query) {
            $query->selectRaw("CONCAT(DATE_FORMAT(dateFrom,'%d/%m/%Y'),' | ',DATE_FORMAT(dateTo,'%d/%m/%Y')) as financePeriod,companyFinancePeriodID");
        }, 'finance_year_by' => function ($query) {
            $query->selectRaw("CONCAT(DATE_FORMAT(bigginingDate,'%d/%m/%Y'),' | ',DATE_FORMAT(endingDate,'%d/%m/%Y')) as financeYear,companyFinanceYearID");
        },'segment_by','warehouse_by'])->findWithoutFail($id);

        if (empty($stockCount)) {
            return $this->sendError(trans('custom.stock_count_not_found'));
        }

        return $this->sendResponse($stockCount->toArray(), trans('custom.stock_count_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateStockCountAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/stockCounts/{id}",
     *      summary="Update the specified StockCount in storage",
     *      tags={"StockCount"},
     *      description="Update StockCount",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of StockCount",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="StockCount that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/StockCount")
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
     *                  ref="#/definitions/StockCount"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateStockCountAPIRequest $request)
    {
        $input = $request->all();
        $input = array_except($input, ['created_by', 'confirmedByName', 'finance_period_by', 'finance_year_by',
            'confirmedByEmpID', 'confirmedDate', 'confirmed_by', 'confirmedByEmpSystemID','segment_by','warehouse_by']);

        $input = $this->convertArrayToValue($input);
        $wareHouseError = array('type' => 'wareHouse');
        $serviceLineError = array('type' => 'serviceLine');

        DB::beginTransaction();
        try {
            /** @var StockCount $stockCount */
            $stockCount = $this->stockCountRepository->findWithoutFail($id);

            if (empty($stockCount)) {
                return $this->sendError(trans('custom.stock_count_not_found'));
            }

            if ($input['serviceLineSystemID']) {
                $checkDepartmentActive = SegmentMaster::find($input['serviceLineSystemID']);
                if (empty($checkDepartmentActive)) {
                    return $this->sendError(trans('custom.segment_not_found'));
                }

                if ($checkDepartmentActive->isActive == 0) {
                    $this->stockCountRepository->update(["serviceLineSystemID" => null,"serviceLineCode" => null],$id);
                    return $this->sendError('Please select a active Segment', 500,$serviceLineError);
                }

                $input['serviceLineCode'] = $checkDepartmentActive->ServiceLineCode;
            }

            if ($input['location']) {
                $checkWareHouseActive = WarehouseMaster::find($input['location']);
                if (empty($checkWareHouseActive)) {
                    return $this->sendError(trans('custom.location_not_found'), 500, $wareHouseError);
                }

                if ($checkWareHouseActive->isActive == 0) {
                    $this->stockCountRepository->update(["location" => null],$id);
                    return $this->sendError('Please select a active location', 500, $wareHouseError);
                }
            }

            if (isset($input['stockCountDate'])) {
                if ($input['stockCountDate']) {
                    $input['stockCountDate'] = new Carbon($input['stockCountDate']);
                }
            }


            if ($stockCount->confirmedYN == 0 && $input['confirmedYN'] == 1) {

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

                $validator = \Validator::make($input, [
                    'companyFinancePeriodID' => 'required|numeric|min:1',
                    'companyFinanceYearID' => 'required|numeric|min:1',
                    'stockCountDate' => 'required|date|before_or_equal:today',
                    'serviceLineSystemID' => 'required|numeric|min:1',
                    'location' => 'required|numeric|min:1',
                    'refNo' => 'required',
                    'comment' => 'required'
                ]);

                if ($validator->fails()) {
                    return $this->sendError($validator->messages(), 422);
                }

                $documentDate = $input['stockCountDate'];
                $monthBegin = $input['FYBiggin'];
                $monthEnd = $input['FYEnd'];
                if (($documentDate >= $monthBegin) && ($documentDate <= $monthEnd)) {
                } else {
                    return $this->sendError('Document  date is not within the selected financial period !', 500);
                }

                $checkItems = StockCountDetail::where('stockCountAutoID', $id)
                    ->count();
                if ($checkItems == 0) {
                    return $this->sendError('Every document should have at least one item', 500);
                }

                $deleteNotUpdatedItems = StockCountDetail::where('stockCountAutoID', $id)
                                                        ->where('updatedFlag', 0)
                                                        ->delete();

                $this->updateStockCountAdjustmentDetail($id, $stockCount);
             
                $input['RollLevForApp_curr'] = 1;
                $params = array('autoID' => $id,
                    'company' => $stockCount->companySystemID,
                    'document' => $stockCount->documentSystemID,
                    'segment' => $input['serviceLineSystemID'],
                    'category' => 0,
                    'amount' => 0
                );

                $confirm = \Helper::confirmDocument($params);
                if (!$confirm["success"]) {
                    return $this->sendError($confirm["message"], 500);
                }
            }

            $employee = \Helper::getEmployeeInfo();

            $input['modifiedPc'] = gethostname();
            $input['modifiedUser'] = $employee->empID;
            $input['modifiedUserSystemID'] = $employee->employeeSystemID;

            $stockCount = $this->stockCountRepository->update($input, $id);

            DB::commit();
            return $this->sendReponseWithDetails($stockCount->toArray(), 'Stock Count updated successfully',1,$confirm['data'] ?? null);
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage()." ".$exception->getLine());
        }
    }


    public function updateStockCountAdjustmentDetail($stockCountAutoID, $stockCount)
    {
        $stockCountDetails = StockCountDetail::where('stockCountAutoID', $stockCountAutoID)
                                            ->get();

        foreach ($stockCountDetails as $key => $value) {
            $data = array('companySystemID' => $stockCount->companySystemID,
                        'itemCodeSystem' => $value->itemCodeSystem,
                        'wareHouseId' => $stockCount->location);

            $itemCurrentCostAndQty = \Inventory::itemCurrentCostAndQty($data);

            $updateData = [
                'currenctStockQty' => $itemCurrentCostAndQty['currentWareHouseStockQty'],
                'systemQty' => $itemCurrentCostAndQty['currentWareHouseStockQty'],
                'wacAdjRpt' => $itemCurrentCostAndQty['wacValueReporting'],
                'currentWacRpt' => $itemCurrentCostAndQty['wacValueReporting'],
                'adjustedQty' => $value->noQty - $itemCurrentCostAndQty['currentWareHouseStockQty']
            ];

            $item = ItemAssigned::where('itemCodeSystem', $value->itemCodeSystem)
                                ->where('companySystemID', $stockCount->companySystemID)
                                ->first();

            if ($item) {
                $companyCurrencyConversion = \Helper::currencyConversion($stockCount->companySystemID,$item->wacValueReportingCurrencyID,$item->wacValueReportingCurrencyID,$itemCurrentCostAndQty['wacValueReporting']);
                $updateData['currentWaclocal'] = $companyCurrencyConversion['localAmount'];
                $updateData['wacAdjLocal'] = $companyCurrencyConversion['localAmount'];
                $updateData['wacAdjRptER'] = $companyCurrencyConversion['trasToRptER'];
                $updateData['wacAdjLocalER'] = 1;
            }

            StockCountDetail::where('stockCountDetailsAutoID', $value->stockCountDetailsAutoID)
                            ->update($updateData);
        }

        return true;
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/stockCounts/{id}",
     *      summary="Remove the specified StockCount from storage",
     *      tags={"StockCount"},
     *      description="Delete StockCount",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of StockCount",
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
        /** @var StockCount $stockCount */
        $stockCount = $this->stockCountRepository->findWithoutFail($id);

        if (empty($stockCount)) {
            return $this->sendError(trans('custom.stock_count_not_found'));
        }

        $stockCount->delete();

        return $this->sendSuccess('Stock Count deleted successfully');
    }

    public function getAllStockCountsByCompany(Request $request)
    {

        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input, array('serviceLineSystemID', 'confirmedYN', 'approved', 'location', 'month', 'year'));

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $search = $request->input('search.value');

        $grvLocation = $request['location'];
        $grvLocation = (array)$grvLocation;
        $grvLocation = collect($grvLocation)->pluck('id');

        $serviceLineSystemID = $request['serviceLineSystemID'];
        $serviceLineSystemID = (array)$serviceLineSystemID;
        $serviceLineSystemID = collect($serviceLineSystemID)->pluck('id');

        $stockAdjustments = $this->stockCountRepository->stockCountListQuery($request, $input, $search, $grvLocation, $serviceLineSystemID);

        return \DataTables::eloquent($stockAdjustments)
            ->addColumn('Actions', 'Actions', "Actions")
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('stockCountAutoID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }


    public function stockCountReopen(Request $request)
    {
        $input = $request->all();

        $id = $input['stockCountAutoID'];
        $stockCount = $this->stockCountRepository->findWithoutFail($id);
        $emails = array();
        if (empty($stockCount)) {
            return $this->sendError(trans('custom.stock_count_not_found'));
        }

        if ($stockCount->approved == -1) {
            return $this->sendError(trans('custom.you_cannot_reopen_this_stock_count_it_is_already_f'));
        }

        if ($stockCount->RollLevForApp_curr > 1) {
            return $this->sendError(trans('custom.you_cannot_reopen_this_stock_count_it_is_already_p'));
        }

        if ($stockCount->confirmedYN == 0) {
            return $this->sendError(trans('custom.you_cannot_reopen_this_stock_count_it_is_not_confi'));
        }

        $updateInput = ['confirmedYN' => 0,'confirmedByEmpSystemID' => null,'confirmedByEmpID' => null,
            'confirmedByName' => null, 'confirmedDate' => null,'RollLevForApp_curr' => 1];

        $this->stockCountRepository->update($updateInput,$id);

        $employee = \Helper::getEmployeeInfo();

        $document = DocumentMaster::where('documentSystemID', $stockCount->documentSystemID)->first();

        $cancelDocNameBody = $document->documentDescription . ' <b>' . $stockCount->stockCountCode . '</b>';
        $cancelDocNameSubject = $document->documentDescription . ' ' . $stockCount->stockCountCode;

        $subject = trans('email.is_reopened_subject', ['attribute' => $cancelDocNameSubject]);

        $body = trans('email.is_reopened_body', [
            'attribute' => $cancelDocNameBody,
            'empID' => $employee->empID,
            'empName' => $employee->empFullName,
            'reopenComments' => $input['reopenComments']
        ]);

        $documentApproval = DocumentApproved::where('companySystemID', $stockCount->companySystemID)
            ->where('documentSystemCode', $stockCount->stockCountAutoID)
            ->where('documentSystemID', $stockCount->documentSystemID)
            ->where('rollLevelOrder', 1)
            ->first();

        if ($documentApproval) {
            if ($documentApproval->approvedYN == 0) {
                $companyDocument = CompanyDocumentAttachment::where('companySystemID', $stockCount->companySystemID)
                    ->where('documentSystemID', $stockCount->documentSystemID)
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

                $sendEmail = \Email::sendEmail($emails);
                if (!$sendEmail["success"]) {
                    return ['success' => false, 'message' => $sendEmail["message"]];
                }
            }
        }

        DocumentApproved::where('documentSystemCode', $id)
            ->where('companySystemID', $stockCount->companySystemID)
            ->where('documentSystemID', $stockCount->documentSystemID)
            ->delete();

        /*Audit entry*/
        AuditTrial::createAuditTrial($stockCount->documentSystemID,$id,$input['reopenComments'],'Reopened');

        return $this->sendResponse($stockCount->toArray(), trans('custom.stock_count_reopened_successfully'));
    }

    public function getStockCountAudit(Request $request)
    {
        $id = $request->get('id');
        $stockCount = $this->stockCountRepository->getAudit($id);

        if (empty($stockCount)) {
            return $this->sendError(trans('custom.stock_count_not_found'));
        }

        $stockCount->docRefNo = \Helper::getCompanyDocRefNo($stockCount->companySystemID, $stockCount->documentSystemID);

        return $this->sendResponse($stockCount->toArray(), trans('custom.stock_count_retrieved_successfully'));
    }

     public function getStockCountApprovedByUser(Request $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input, array('serviceLineSystemID', 'confirmedYN', 'approved', 'location', 'month', 'year'));

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $companyId = $input['companyId'];
        $empID = \Helper::getEmployeeSystemID();

        $search = $request->input('search.value');
        $purchaseReturnMaster = DB::table('erp_documentapproved')
            ->select(
                'erp_stockcount.*',
                'employees.empName As created_emp',
                'serviceline.ServiceLineDes As serviceLineDes',
                'warehousemaster.wareHouseDescription As wareHouseDescription',
                'erp_documentapproved.documentApprovedID',
                'rollLevelOrder',
                'approvalLevelID',
                'documentSystemCode')
            ->join('erp_stockcount', function ($query) use ($companyId, $search) {
                $query->on('erp_documentapproved.documentSystemCode', '=', 'stockCountAutoID')
                    ->where('erp_stockcount.companySystemID', $companyId)
                    ->where('erp_stockcount.confirmedYN', 1);
            })
            ->where('erp_documentapproved.approvedYN', -1)
            ->leftJoin('employees', 'createdUserSystemID', 'employees.employeeSystemID')
            ->leftJoin('warehousemaster', 'location', 'warehousemaster.wareHouseSystemCode')
            ->leftJoin('serviceline', 'erp_stockcount.serviceLineSystemID', 'serviceline.serviceLineSystemID')
            ->where('erp_documentapproved.rejectedYN', 0)
            ->whereIn('erp_documentapproved.documentSystemID', [97])
            ->where('erp_documentapproved.companySystemID', $companyId)
            ->where('erp_documentapproved.employeeSystemID', $empID);

        if (array_key_exists('serviceLineSystemID', $input)) {
            if ($input['serviceLineSystemID'] && !is_null($input['serviceLineSystemID'])) {
                $purchaseReturnMaster->where('erp_stockcount.serviceLineSystemID', $input['serviceLineSystemID']);
            }
        }

        if (array_key_exists('location', $input)) {
            if ($input['location'] && !is_null($input['location'])) {
                $purchaseReturnMaster->where('erp_stockcount.location', $input['location']);
            }
        }

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $purchaseReturnMaster = $purchaseReturnMaster->where(function ($query) use ($search) {
                $query->where('stockCountCode', 'LIKE', "%{$search}%")
                    ->orWhere('comment', 'LIKE', "%{$search}%");
            });
        }

        return \DataTables::of($purchaseReturnMaster)
            ->addColumn('Actions', 'Actions', "Actions")
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('stockCountAutoID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    public function getStockCountApprovalByUser(Request $request)
    {

        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input, array('serviceLineSystemID', 'confirmedYN', 'approved', 'location', 'month', 'year'));

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $companyId = $input['companyId'];
        $empID = \Helper::getEmployeeSystemID();

        $search = $request->input('search.value');
        $purchaseReturnMaster = DB::table('erp_documentapproved')
            ->select(
                'employeesdepartments.approvalDeligated',
                'erp_stockcount.*',
                'employees.empName As created_emp',
                'serviceline.ServiceLineDes As serviceLineDes',
                'warehousemaster.wareHouseDescription As wareHouseDescription',
                'erp_documentapproved.documentApprovedID',
                'rollLevelOrder',
                'approvalLevelID',
                'documentSystemCode')
            ->join('employeesdepartments', function ($query) use ($companyId, $empID) {
                $query->on('erp_documentapproved.approvalGroupID', '=', 'employeesdepartments.employeeGroupID')
                    ->on('erp_documentapproved.documentSystemID', '=', 'employeesdepartments.documentSystemID')
                    ->on('erp_documentapproved.companySystemID', '=', 'employeesdepartments.companySystemID');

                $serviceLinePolicy = CompanyDocumentAttachment::where('companySystemID', $companyId)
                    ->where('documentSystemID', 97)
                    ->first();

                if ($serviceLinePolicy && $serviceLinePolicy->isServiceLineApproval == -1) {
                    //$query->on('erp_documentapproved.serviceLineSystemID', '=', 'employeesdepartments.ServiceLineSystemID');
                }

                $query->whereIn('employeesdepartments.documentSystemID', [97])
                    ->where('employeesdepartments.companySystemID', $companyId)
                    ->where('employeesdepartments.employeeSystemID', $empID)
                    ->where('employeesdepartments.isActive', 1)
                    ->where('employeesdepartments.removedYN', 0);
            })
            ->join('erp_stockcount', function ($query) use ($companyId, $search) {
                $query->on('erp_documentapproved.documentSystemCode', '=', 'stockCountAutoID')
                    ->on('erp_documentapproved.rollLevelOrder', '=', 'RollLevForApp_curr')
                    ->where('erp_stockcount.companySystemID', $companyId)
                    ->where('erp_stockcount.approved', 0)
                    ->where('erp_stockcount.confirmedYN', 1);
            })
            ->where('erp_documentapproved.approvedYN', 0)
            ->leftJoin('employees', 'createdUserSystemID', 'employees.employeeSystemID')
            ->leftJoin('warehousemaster', 'location', 'warehousemaster.wareHouseSystemCode')
            ->leftJoin('serviceline', 'erp_stockcount.serviceLineSystemID', 'serviceline.serviceLineSystemID')
            ->where('erp_documentapproved.rejectedYN', 0)
            ->whereIn('erp_documentapproved.documentSystemID', [97])
            ->where('erp_documentapproved.companySystemID', $companyId);


        if (array_key_exists('serviceLineSystemID', $input)) {
            if ($input['serviceLineSystemID'] && !is_null($input['serviceLineSystemID'])) {
                $purchaseReturnMaster->where('erp_stockcount.serviceLineSystemID', $input['serviceLineSystemID']);
            }
        }

        if (array_key_exists('location', $input)) {
            if ($input['location'] && !is_null($input['location'])) {
                $purchaseReturnMaster->where('erp_stockcount.location', $input['location']);
            }
        }

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $purchaseReturnMaster = $purchaseReturnMaster->where(function ($query) use ($search) {
                $query->where('stockCountCode', 'LIKE', "%{$search}%")
                    ->orWhere('comment', 'LIKE', "%{$search}%");
            });
        }

        $isEmployeeDischarched = \Helper::checkEmployeeDischarchedYN();

        if ($isEmployeeDischarched == 'true') {
            $purchaseReturnMaster = [];
        }

        return \DataTables::of($purchaseReturnMaster)
            ->addColumn('Actions', 'Actions', "Actions")
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('stockCountAutoID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);

    }

    public function stockCountReferBack(Request $request)
    {
        $input = $request->all();

        $id = $input['id'];

        $stockCount = $this->stockCountRepository->find($id);
        if (empty($stockCount)) {
            return $this->sendError(trans('custom.stock_count_not_found'));
        }

        if ($stockCount->refferedBackYN != -1) {
            return $this->sendError(trans('custom.you_cannot_refer_back_this_stock_count'));
        }

        $stockCountArray = $stockCount->toArray();

        $storeSAHistory = StockCountRefferedBack::insert($stockCountArray);

        $fetchDetails = StockCountDetail::where('stockCountAutoID', $id)
                                        ->get();

        if (!empty($fetchDetails)) {
            foreach ($fetchDetails as $detail) {
                $detail['timesReferred'] = $stockCount->timesReferred;
            }
        }

        $stockCountDetailArray = $fetchDetails->toArray();

        $storeSADetailHistory = StockCountDetailsRefferedBack::insert($stockCountDetailArray);

        $fetchDocumentApproved = DocumentApproved::where('documentSystemCode', $id)
            ->where('companySystemID', $stockCount->companySystemID)
            ->where('documentSystemID', $stockCount->documentSystemID)
            ->get();

        if (!empty($fetchDocumentApproved)) {
            foreach ($fetchDocumentApproved as $DocumentApproved) {
                $DocumentApproved['refTimes'] = $stockCount->timesReferred;
            }
        }

        $DocumentApprovedArray = $fetchDocumentApproved->toArray();

        $storeDocumentRefereedHistory = DocumentReferedHistory::insert($DocumentApprovedArray);

        $deleteApproval = DocumentApproved::where('documentSystemCode', $id)
            ->where('companySystemID', $stockCount->companySystemID)
            ->where('documentSystemID', $stockCount->documentSystemID)
            ->delete();

        if ($deleteApproval) {
            $updateArray = ['refferedBackYN' => 0,'confirmedYN' => 0,'confirmedByEmpSystemID' => null,
                'confirmedByEmpID' => null,'confirmedByName' => null,'confirmedDate' => null,'RollLevForApp_curr' => 1];

            $this->stockCountRepository->update($updateArray,$id);
        }

        return $this->sendResponse($stockCount->toArray(), trans('custom.stock_count_amend_successfully'));
    }
}
