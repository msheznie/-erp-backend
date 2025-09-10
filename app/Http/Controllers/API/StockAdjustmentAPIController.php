<?php
/**
 * =============================================
 * -- File Name : StockAdjustmentAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Stock Adjustment
 * -- Author : Mohamed Fayas
 * -- Create date : 20 - August 2018
 * -- Description : This file contains the all CRUD for Stock Adjustment
 * -- REVISION HISTORY
 * -- Date: 21 - August 2018 By: Fayas Description: Added new functions named as getAllStockAdjustmentsByCompany(),getStockAdjustmentFormData(),
 *                        getStockAdjustmentAudit()
 * -- Date: 03 - September 2018 By: Fayas Description: Added new functions named as getStockAdjustmentApprovedByUser(),getStockAdjustmentApprovalByUser()
 * -- Date: 03 - February 2019 By: Fayas Description: Added new functions named as stockAdjustmentReopen()
 * -- Date: 06 - February 2019 By: Fayas Description: Added new functions named as stockAdjustmentReferBack()
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateStockAdjustmentAPIRequest;
use App\Http\Requests\API\UpdateStockAdjustmentAPIRequest;
use App\Models\Company;
use App\Models\CompanyDocumentAttachment;
use App\Models\FinanceItemCategorySub;
use App\Models\CompanyFinanceYear;
use App\Models\CompanyPolicyMaster;
use App\Models\StockAdjustmentReason;
use App\Models\DocumentApproved;
use App\Models\DocumentMaster;
use App\Models\DocumentReferedHistory;
use App\Models\EmployeesDepartment;
use App\Models\Months;
use App\Models\SegmentMaster;
use App\Models\StockAdjustment;
use App\Models\StockAdjustmentDetails;
use App\Models\StockAdjustmentDetailsRefferedBack;
use App\Models\StockAdjustmentRefferedBack;
use App\Models\Unit;
use App\Models\WarehouseMaster;
use App\Models\YesNoSelection;
use App\Models\YesNoSelectionForMinus;
use App\Repositories\StockAdjustmentRepository;
use App\Traits\AuditTrial;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\DB;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class StockAdjustmentController
 * @package App\Http\Controllers\API
 */

class StockAdjustmentAPIController extends AppBaseController
{
    /** @var  StockAdjustmentRepository */
    private $stockAdjustmentRepository;

    public function __construct(StockAdjustmentRepository $stockAdjustmentRepo)
    {
        $this->stockAdjustmentRepository = $stockAdjustmentRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/stockAdjustments",
     *      summary="Get a listing of the StockAdjustments.",
     *      tags={"StockAdjustment"},
     *      description="Get all StockAdjustments",
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
     *                  @SWG\Items(ref="#/definitions/StockAdjustment")
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
        $this->stockAdjustmentRepository->pushCriteria(new RequestCriteria($request));
        $this->stockAdjustmentRepository->pushCriteria(new LimitOffsetCriteria($request));
        $stockAdjustments = $this->stockAdjustmentRepository->all();

        return $this->sendResponse($stockAdjustments->toArray(), trans('custom.stock_adjustments_retrieved_successfully'));
    }

    /**
     * @param CreateStockAdjustmentAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/stockAdjustments",
     *      summary="Store a newly created StockAdjustment in storage",
     *      tags={"StockAdjustment"},
     *      description="Store StockAdjustment",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="StockAdjustment that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/StockAdjustment")
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
     *                  ref="#/definitions/StockAdjustment"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateStockAdjustmentAPIRequest $request)
    {
        $input = $request->all();

        $input = $this->convertArrayToValue($input);

        $employee = \Helper::getEmployeeInfo();

        $input['createdPCid'] = gethostname();
        $input['createdUserID'] = $employee->empID;
        $input['createdUserSystemID'] = $employee->employeeSystemID;

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
            'stockAdjustmentDate' => 'required|date|before_or_equal:today',
            'serviceLineSystemID' => 'required|numeric|min:1',
            'location' => 'required|numeric|min:1',
            'refNo' => 'required',
            'comment' => 'required',
            'reason'  => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        if (isset($input['stockAdjustmentDate'])) {
            if ($input['stockAdjustmentDate']) {
                $input['stockAdjustmentDate'] = new Carbon($input['stockAdjustmentDate']);
            }
        }

        $documentDate = $input['stockAdjustmentDate'];
        $monthBegin = $input['FYBiggin'];
        $monthEnd = $input['FYEnd'];
        if (($documentDate >= $monthBegin) && ($documentDate <= $monthEnd)) {
        } else {
            return $this->sendError('Document date is not within the selected financial period !', 500);
        }

        DB::beginTransaction();
        $input['documentSystemID'] = 7;
        $input['documentID'] = 'SA';

        $lastSerial = StockAdjustment::where('companySystemID', $input['companySystemID'])
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

        $warehouse = WarehouseMaster::where('wareHouseSystemCode', $input['location'])->first();
        if (empty($warehouse)) {
            DB::rollBack();
            return $this->sendError(trans('custom.location_not_found'),500);
        }

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
            $stockAdjustmentCode = ($company->CompanyID . '\\' . $finYear . '\\' . $documentMaster['documentID'] . str_pad($lastSerialNumber, 6, '0', STR_PAD_LEFT));
            $input['stockAdjustmentCode'] = $stockAdjustmentCode;
        }

        $stockAdjustments = $this->stockAdjustmentRepository->create($input);
        DB::commit();
        return $this->sendResponse($stockAdjustments->toArray(), trans('custom.stock_adjustment_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/stockAdjustments/{id}",
     *      summary="Display the specified StockAdjustment",
     *      tags={"StockAdjustment"},
     *      description="Get StockAdjustment",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of StockAdjustment",
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
     *                  ref="#/definitions/StockAdjustment"
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
        /** @var StockAdjustment $stockAdjustment */
        $stockAdjustment = $this->stockAdjustmentRepository->with(['confirmed_by', 'created_by', 'finance_period_by' => function ($query) {
            $query->selectRaw("CONCAT(DATE_FORMAT(dateFrom,'%d/%m/%Y'),' | ',DATE_FORMAT(dateTo,'%d/%m/%Y')) as financePeriod,companyFinancePeriodID");
        }, 'finance_year_by' => function ($query) {
            $query->selectRaw("CONCAT(DATE_FORMAT(bigginingDate,'%d/%m/%Y'),' | ',DATE_FORMAT(endingDate,'%d/%m/%Y')) as financeYear,companyFinanceYearID");
        },'segment_by','warehouse_by'])->findWithoutFail($id);

        if (empty($stockAdjustment)) {
            return $this->sendError(trans('custom.stock_adjustment_not_found'));
        }

        return $this->sendResponse($stockAdjustment->toArray(), trans('custom.stock_adjustment_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateStockAdjustmentAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/stockAdjustments/{id}",
     *      summary="Update the specified StockAdjustment in storage",
     *      tags={"StockAdjustment"},
     *      description="Update StockAdjustment",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of StockAdjustment",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="StockAdjustment that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/StockAdjustment")
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
     *                  ref="#/definitions/StockAdjustment"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateStockAdjustmentAPIRequest $request)
    {
        $input = $request->all();
        $input = array_except($input, ['created_by', 'confirmedByName', 'finance_period_by', 'finance_year_by',
            'confirmedByEmpID', 'confirmedDate', 'confirmed_by', 'confirmedByEmpSystemID','segment_by','warehouse_by']);

        $input = $this->convertArrayToValue($input);
        $confirm_validate = array('type' => 'confirm_validate');
        $wareHouseError = array('type' => 'wareHouse');
        $serviceLineError = array('type' => 'serviceLine');
        


        /** @var StockAdjustment $stockAdjustment */
        $stockAdjustment = $this->stockAdjustmentRepository->findWithoutFail($id);

        if (empty($stockAdjustment)) {
            return $this->sendError(trans('custom.stock_adjustment_not_found'));
        }

        if ($input['serviceLineSystemID']) {
            $checkDepartmentActive = SegmentMaster::find($input['serviceLineSystemID']);
            if (empty($checkDepartmentActive)) {
                return $this->sendError(trans('custom.segment_not_found'));
            }

            if ($checkDepartmentActive->isActive == 0) {
                $this->stockAdjustmentRepository->update(["serviceLineSystemID" => null,"serviceLineCode" => null],$id);
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
                $this->stockAdjustmentRepository->update(["location" => null],$id);
                return $this->sendError('Please select a active location', 500, $wareHouseError);
            }
        }

        if (isset($input['stockAdjustmentDate'])) {
            if ($input['stockAdjustmentDate']) {
                $input['stockAdjustmentDate'] = new Carbon($input['stockAdjustmentDate']);
            }
        }


        if ($stockAdjustment->confirmedYN == 0 && $input['confirmedYN'] == 1) {

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
                'stockAdjustmentDate' => 'required|date|before_or_equal:today',
                'serviceLineSystemID' => 'required|numeric|min:1',
                'location' => 'required|numeric|min:1',
                'refNo' => 'required',
                'comment' => 'required',
                'reason'  => 'required',
            ]);


            if ($validator->fails()) {
                return $this->sendError($validator->messages(), 422);
            }
            
            if(isset($input['reason']) && $input['reason'] == 0) {
                return $this->sendError('Please select reason', 500);
            }

            $documentDate = $input['stockAdjustmentDate'];
            $monthBegin = $input['FYBiggin'];
            $monthEnd = $input['FYEnd'];
            if (($documentDate >= $monthBegin) && ($documentDate <= $monthEnd)) {
            } else {
                return $this->sendError('Document  date is not within the selected financial period !', 500);
            }

            $checkItems = StockAdjustmentDetails::where('stockAdjustmentAutoID', $id)
                ->count();
            if ($checkItems == 0) {
                return $this->sendError('Every document should have at least one item', 500);
            }

            $checkQuantity = StockAdjustmentDetails::where('stockAdjustmentAutoID', $id)
                                                    ->where(function ($q) {
                                                        $q->where('noQty', '<=', 0)
                                                            ->orWhereNull('noQty');
                                                    })
                                                    ->count();
            if ($checkQuantity > 0) {
                //return $this->sendError('Every item should have at least one minimum Qty requested', 500);
            }

            $details = StockAdjustmentDetails::where('stockAdjustmentAutoID', $id)
                                                ->get();

            $errorMessage = [];
            foreach ($details as $key => $value) {
                $data = array('companySystemID' => $stockAdjustment->companySystemID,
                    'itemCodeSystem' => $value->itemCodeSystem,
                    'wareHouseId' => $stockAdjustment->location);

                $itemCurrentCostAndQty = \Inventory::itemCurrentCostAndQty($data);

                $currenStockQty = ($stockAdjustment->stockAdjustmentType == 2) ? $itemCurrentCostAndQty['currentStockQty'] : $itemCurrentCostAndQty['currentWareHouseStockQty'];

                $currenctStockQty = $currenStockQty;

                $balanceQty = $currenctStockQty + $value->noQty;

                if ($balanceQty < 0) {
                      if ($currenStockQty != $value->currenctStockQty) {

                            $errorMessage[] = $value->itemPrimaryCode.' - Current stock quantity has been updated from '.$value->currenctStockQty.' to '.$currenStockQty.'. Adjusted quantity cannot be less than current stock quantity';
                      } else {
                            $errorMessage[] = $value->itemPrimaryCode.' - Adjusted quantity cannot be less than current stock quantity';
                      }
                } 

                $stockAdjustmentDetailsRes = StockAdjustmentDetails::where('stockAdjustmentDetailsAutoID', $value->stockAdjustmentDetailsAutoID)->update(['currenctStockQty' => $currenctStockQty]);
            }

            if (count($errorMessage) > 0) {
                return $this->sendError($errorMessage, 500, $confirm_validate);
            }


            $input['RollLevForApp_curr'] = 1;
            $params = array('autoID' => $id,
                'company' => $stockAdjustment->companySystemID,
                'document' => $stockAdjustment->documentSystemID,
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

        $stockAdjustment = $this->stockAdjustmentRepository->update($input, $id);

        return $this->sendReponseWithDetails($stockAdjustment->toArray(), 'StockAdjustment updated successfully',1,$confirm['data'] ?? null);
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/stockAdjustments/{id}",
     *      summary="Remove the specified StockAdjustment from storage",
     *      tags={"StockAdjustment"},
     *      description="Delete StockAdjustment",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of StockAdjustment",
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
        /** @var StockAdjustment $stockAdjustment */
        $stockAdjustment = $this->stockAdjustmentRepository->findWithoutFail($id);

        if (empty($stockAdjustment)) {
            return $this->sendError(trans('custom.stock_adjustment_not_found'));
        }

        $stockAdjustment->delete();

        return $this->sendResponse($id, trans('custom.stock_adjustment_deleted_successfully'));
    }


    /**
     * get All Stock Adjustments By Company
     * POST /getAllStockAdjustmentsByCompany
     *
     * @param Request $request
     *
     * @return Response
     */

    public function getAllStockAdjustmentsByCompany(Request $request)
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

        $reasons = (isset($input['reason'])) ? collect($input['reason'])->pluck('id') : null;

        $serviceLineSystemID = $request['serviceLineSystemID'];
        $serviceLineSystemID = (array)$serviceLineSystemID;
        $serviceLineSystemID = collect($serviceLineSystemID)->pluck('id');

        $stockAdjustments = $this->stockAdjustmentRepository->stockAdjustmentListQuery($request, $input, $search, $grvLocation,$serviceLineSystemID,$reasons);

        return \DataTables::eloquent($stockAdjustments)
            ->addColumn('Actions', 'Actions', "Actions")
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('stockAdjustmentAutoID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    /**
     * get Stock Adjustment Form Data
     * Get /getStockAdjustmentFormData
     *
     * @param Request $request
     *
     * @return Response
     */
    public function getStockAdjustmentFormData(Request $request)
    {
        $companyId = $request['companyId'];

        $segments = SegmentMaster::where("companySystemID", $companyId)->approved()->withAssigned($companyId);
        if (isset($request['type']) && $request['type'] != 'filter') {
            $segments = $segments->where('isActive', 1);
        }
        $segments = $segments->get();

        /** Yes and No Selection */
        $yesNoSelection = YesNoSelection::all();

        $reasons = StockAdjustmentReason::where('is_active',true)->get();

        /** all Units*/
        $yesNoSelectionForMinus = YesNoSelectionForMinus::all();

        $month = Months::all();

        $years = StockAdjustment::select(DB::raw("YEAR(createdDateTime) as year"))
                                ->whereNotNull('createdDateTime')
                                ->groupby('year')
                                ->orderby('year', 'desc')
                                ->get();

        $wareHouseLocation = WarehouseMaster::where("companySystemID", $companyId);
        if (isset($request['type']) && $request['type'] != 'filter') {
            $wareHouseLocation = $wareHouseLocation->where('isActive', 1);
        }
        $wareHouseLocation = $wareHouseLocation->get();

        $companyPolicy = CompanyPolicyMaster::where('companySystemID', $companyId)
            ->where('companyPolicyCategoryID', 22)
            ->first();

        $typeId = [];

        if (!empty($companyPolicy)) {
            if ($companyPolicy->isYesNO == 0) {
                $typeId = [2];
            } else if ($companyPolicy->isYesNO == 1) {
                $typeId = [1];
            }
        }

        $financeSubCategories = FinanceItemCategorySub::where('itemCategoryID',1)
                                                      ->get();


        $financialYears = array(array('value' => intval(date("Y")), 'label' => date("Y")),
            array('value' => intval(date("Y", strtotime("-1 year"))), 'label' => date("Y", strtotime("-1 year"))));

        $companyFinanceYear = \Helper::companyFinanceYear($companyId);

        $contracts = "";

        $units = Unit::all();

        $output = array(
            'segments' => $segments,
            'financeSubCategories' => $financeSubCategories,
            'yesNoSelection' => $yesNoSelection,
            'yesNoSelectionForMinus' => $yesNoSelectionForMinus,
            'month' => $month,
            'years' => $years,
            'wareHouseLocation' => $wareHouseLocation,
            'financialYears' => $financialYears,
            'companyFinanceYear' => $companyFinanceYear,
            'contracts' => $contracts,
            'units' => $units,
            'reasons' => $reasons
        );

        return $this->sendResponse($output, trans('custom.record_retrieved_successfully_1'));
    }

    /**
     * Display the specified Stock Adjustment Audit.
     * GET|HEAD /getStockAdjustmentAudit
     *
     * @param  int $id
     *
     * @return Response
     */
    public function getStockAdjustmentAudit(Request $request)
    {
        $id = $request->get('id');
        $stockAdjustment = $this->stockAdjustmentRepository->getAudit($id);

        if (empty($stockAdjustment)) {
            return $this->sendError(trans('custom.stock_adjustment_not_found'));
        }

        $stockAdjustment->docRefNo = \Helper::getCompanyDocRefNo($stockAdjustment->companySystemID, $stockAdjustment->documentSystemID);

        return $this->sendResponse($stockAdjustment->toArray(), trans('custom.stock_adjustment_retrieved_successfully'));
    }

    public function getStockAdjustmentApprovedByUser(Request $request)
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
                'erp_stockadjustment.*','stockadjustment_reasons.reason as reason',
                'employees.empName As created_emp',
                'serviceline.ServiceLineDes As serviceLineDes',
                'warehousemaster.wareHouseDescription As wareHouseDescription',
                'erp_documentapproved.documentApprovedID',
                'rollLevelOrder',
                'approvalLevelID',
                'documentSystemCode')
            ->join('erp_stockadjustment', function ($query) use ($companyId, $search) {
                $query->on('erp_documentapproved.documentSystemCode', '=', 'stockAdjustmentAutoID')
                    ->where('erp_stockadjustment.companySystemID', $companyId)
                    ->where('erp_stockadjustment.confirmedYN', 1);
            })
            ->where('erp_documentapproved.approvedYN', -1)
            ->leftJoin('employees', 'createdUserSystemID', 'employees.employeeSystemID')
            ->leftJoin('warehousemaster', 'location', 'warehousemaster.wareHouseSystemCode')
            ->leftJoin('serviceline', 'erp_stockadjustment.serviceLineSystemID', 'serviceline.serviceLineSystemID')
            ->leftJoin('stockadjustment_reasons', 'id', 'erp_stockadjustment.reason')
            ->where('erp_documentapproved.rejectedYN', 0)
            ->whereIn('erp_documentapproved.documentSystemID', [7])
            ->where('erp_documentapproved.companySystemID', $companyId)
            ->where('erp_documentapproved.employeeSystemID', $empID);

        if (array_key_exists('serviceLineSystemID', $input)) {
            if ($input['serviceLineSystemID'] && !is_null($input['serviceLineSystemID'])) {
                $purchaseReturnMaster->where('erp_stockadjustment.serviceLineSystemID', $input['serviceLineSystemID']);
            }
        }

        if (array_key_exists('location', $input)) {
            if ($input['location'] && !is_null($input['location'])) {
                $purchaseReturnMaster->where('erp_stockadjustment.location', $input['location']);
            }
        }

        if (array_key_exists('month', $input)) {
            if ($input['month'] && !is_null($input['month'])) {
                $purchaseReturnMaster->whereMonth('erp_stockadjustment.purchaseReturnDate', '=', $input['month']);
            }
        }

        if (array_key_exists('year', $input)) {
            if ($input['year'] && !is_null($input['year'])) {
                $purchaseReturnMaster->whereYear('erp_stockadjustment.purchaseReturnDate', '=', $input['year']);
            }
        }

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $purchaseReturnMaster = $purchaseReturnMaster->where(function ($query) use ($search) {
                $query->where('stockAdjustmentCode', 'LIKE', "%{$search}%")
                    ->orWhere('comment', 'LIKE', "%{$search}%");
            });
        }

        return \DataTables::of($purchaseReturnMaster)
            ->addColumn('Actions', 'Actions', "Actions")
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('stockAdjustmentAutoID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    public function getStockAdjustmentApprovalByUser(Request $request)
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
                'erp_stockadjustment.*','stockadjustment_reasons.reason as reason',
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
                    ->where('documentSystemID', 7)
                    ->first();

                if ($serviceLinePolicy && $serviceLinePolicy->isServiceLineApproval == -1) {
                    //$query->on('erp_documentapproved.serviceLineSystemID', '=', 'employeesdepartments.ServiceLineSystemID');
                }

                $query->whereIn('employeesdepartments.documentSystemID', [7])
                    ->where('employeesdepartments.companySystemID', $companyId)
                    ->where('employeesdepartments.employeeSystemID', $empID)
                    ->where('employeesdepartments.isActive', 1)
                    ->where('employeesdepartments.removedYN', 0);
            })
            ->join('erp_stockadjustment', function ($query) use ($companyId, $search) {
                $query->on('erp_documentapproved.documentSystemCode', '=', 'stockAdjustmentAutoID')
                    ->on('erp_documentapproved.rollLevelOrder', '=', 'RollLevForApp_curr')
                    ->where('erp_stockadjustment.companySystemID', $companyId)
                    ->where('erp_stockadjustment.approved', 0)
                    ->where('erp_stockadjustment.confirmedYN', 1);
            })
            ->where('erp_documentapproved.approvedYN', 0)
            ->leftJoin('employees', 'createdUserSystemID', 'employees.employeeSystemID')
            ->leftJoin('stockadjustment_reasons', 'id', 'erp_stockadjustment.reason')
            ->leftJoin('warehousemaster', 'location', 'warehousemaster.wareHouseSystemCode')
            ->leftJoin('serviceline', 'erp_stockadjustment.serviceLineSystemID', 'serviceline.serviceLineSystemID')
            ->where('erp_documentapproved.rejectedYN', 0)
            ->whereIn('erp_documentapproved.documentSystemID', [7])
            ->where('erp_documentapproved.companySystemID', $companyId);


        if (array_key_exists('serviceLineSystemID', $input)) {
            if ($input['serviceLineSystemID'] && !is_null($input['serviceLineSystemID'])) {
                $purchaseReturnMaster->where('erp_stockadjustment.serviceLineSystemID', $input['serviceLineSystemID']);
            }
        }

        if (array_key_exists('location', $input)) {
            if ($input['location'] && !is_null($input['location'])) {
                $purchaseReturnMaster->where('erp_stockadjustment.location', $input['location']);
            }
        }

        if (array_key_exists('month', $input)) {
            if ($input['month'] && !is_null($input['month'])) {
                $purchaseReturnMaster->whereMonth('erp_stockadjustment.purchaseReturnDate', '=', $input['month']);
            }
        }

        if (array_key_exists('year', $input)) {
            if ($input['year'] && !is_null($input['year'])) {
                $purchaseReturnMaster->whereYear('erp_stockadjustment.purchaseReturnDate', '=', $input['year']);
            }
        }

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $purchaseReturnMaster = $purchaseReturnMaster->where(function ($query) use ($search) {
                $query->where('stockAdjustmentCode', 'LIKE', "%{$search}%")
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
                        $query->orderBy('stockAdjustmentAutoID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);

    }

    public function stockAdjustmentReopen(Request $request)
    {
        $input = $request->all();

        $id = $input['stockAdjustmentAutoID'];
        $stockAdjustment = $this->stockAdjustmentRepository->findWithoutFail($id);
        $emails = array();
        if (empty($stockAdjustment)) {
            return $this->sendError(trans('custom.stock_adjustment_not_found'));
        }

        if ($stockAdjustment->approved == -1) {
            return $this->sendError(trans('custom.you_cannot_reopen_this_stock_adjustment_it_is_alre_1'));
        }

        if ($stockAdjustment->RollLevForApp_curr > 1) {
            return $this->sendError(trans('custom.you_cannot_reopen_this_stock_adjustment_it_is_alre'));
        }

        if ($stockAdjustment->confirmedYN == 0) {
            return $this->sendError(trans('custom.you_cannot_reopen_this_stock_adjustment_it_is_not_'));
        }

        $updateInput = ['confirmedYN' => 0,'confirmedByEmpSystemID' => null,'confirmedByEmpID' => null,
            'confirmedByName' => null, 'confirmedDate' => null,'RollLevForApp_curr' => 1];

        $this->stockAdjustmentRepository->update($updateInput,$id);

        $employee = \Helper::getEmployeeInfo();

        $document = DocumentMaster::where('documentSystemID', $stockAdjustment->documentSystemID)->first();

        $cancelDocNameBody = $document->documentDescription . ' <b>' . $stockAdjustment->stockAdjustmentCode . '</b>';
        $cancelDocNameSubject = $document->documentDescription . ' ' . $stockAdjustment->stockAdjustmentCode;

        $subject = $cancelDocNameSubject . ' is reopened';

        $body = '<p>' . $cancelDocNameBody . ' is reopened by ' . $employee->empID . ' - ' . $employee->empFullName . '</p><p>Comment : ' . $input['reopenComments'] . '</p>';

        $documentApproval = DocumentApproved::where('companySystemID', $stockAdjustment->companySystemID)
            ->where('documentSystemCode', $stockAdjustment->stockAdjustmentAutoID)
            ->where('documentSystemID', $stockAdjustment->documentSystemID)
            ->where('rollLevelOrder', 1)
            ->first();

        if ($documentApproval) {
            if ($documentApproval->approvedYN == 0) {
                $companyDocument = CompanyDocumentAttachment::where('companySystemID', $stockAdjustment->companySystemID)
                    ->where('documentSystemID', $stockAdjustment->documentSystemID)
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
            ->where('companySystemID', $stockAdjustment->companySystemID)
            ->where('documentSystemID', $stockAdjustment->documentSystemID)
            ->delete();

        /*Audit entry*/
        AuditTrial::createAuditTrial($stockAdjustment->documentSystemID,$id,$input['reopenComments'],'Reopened');

        return $this->sendResponse($stockAdjustment->toArray(), trans('custom.stock_adjustment_reopened_successfully'));
    }

    public function stockAdjustmentReferBack(Request $request)
    {
        $input = $request->all();

        $id = $input['id'];

        $stockAdjustment = $this->stockAdjustmentRepository->find($id);
        if (empty($stockAdjustment)) {
            return $this->sendError(trans('custom.stock_adjustment_not_found'));
        }

        if ($stockAdjustment->refferedBackYN != -1) {
            return $this->sendError(trans('custom.you_cannot_refer_back_this_stock_adjustment'));
        }

        $stockAdjustmentArray = $stockAdjustment->toArray();

        $storeSAHistory = StockAdjustmentRefferedBack::insert($stockAdjustmentArray);

        $fetchDetails = StockAdjustmentDetails::where('stockAdjustmentAutoID', $id)
                                                ->get();

        if (!empty($fetchDetails)) {
            foreach ($fetchDetails as $detail) {
                $detail['timesReferred'] = $stockAdjustment->timesReferred;
            }
        }

        $stockAdjustmentDetailArray = $fetchDetails->toArray();

        $storeSADetailHistory = StockAdjustmentDetailsRefferedBack::insert($stockAdjustmentDetailArray);

        $fetchDocumentApproved = DocumentApproved::where('documentSystemCode', $id)
            ->where('companySystemID', $stockAdjustment->companySystemID)
            ->where('documentSystemID', $stockAdjustment->documentSystemID)
            ->get();

        if (!empty($fetchDocumentApproved)) {
            foreach ($fetchDocumentApproved as $DocumentApproved) {
                $DocumentApproved['refTimes'] = $stockAdjustment->timesReferred;
            }
        }

        $DocumentApprovedArray = $fetchDocumentApproved->toArray();

        $storeDocumentRefereedHistory = DocumentReferedHistory::insert($DocumentApprovedArray);

        $deleteApproval = DocumentApproved::where('documentSystemCode', $id)
            ->where('companySystemID', $stockAdjustment->companySystemID)
            ->where('documentSystemID', $stockAdjustment->documentSystemID)
            ->delete();

        if ($deleteApproval) {
            $updateArray = ['refferedBackYN' => 0,'confirmedYN' => 0,'confirmedByEmpSystemID' => null,
                'confirmedByEmpID' => null,'confirmedByName' => null,'confirmedDate' => null,'RollLevForApp_curr' => 1];

            $this->stockAdjustmentRepository->update($updateArray,$id);
        }

        return $this->sendResponse($stockAdjustment->toArray(), trans('custom.stock_adjustment_amend_successfully'));
    }
}
