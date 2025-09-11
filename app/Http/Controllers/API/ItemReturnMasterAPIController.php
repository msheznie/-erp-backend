<?php
/**
 * =============================================
 * -- File Name : ItemReturnMasterAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Item Return Details
 * -- Author : Mohamed Fayas
 * -- Create date : 16 - July 2018
 * -- Description : This file contains the all CRUD for Item Return Details
 * -- REVISION HISTORY
 * -- Date: 16 - July 2018 By: Fayas Description: Added new functions named as getAllMaterielReturnByCompany(),getMaterielReturnFormData()
 * -- Date: 17 - July 2018 By: Fayas Description: Added new functions named as getMaterielReturnAudit(),getMaterielReturnApprovalByUser(),getMaterielReturnApprovedByUser()
 * -- Date: 30 - July 2018 By: Fayas Description: Added new functions named as printItemReturn()
 * -- Date: 27 - August 2018 By: Fayas Description: Added new functions named as materielReturnReopen()
 * -- Date: 03-December 2018 By: Fayas Description: Added new functions named as materielReturnReferBack()
 *
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateItemReturnMasterAPIRequest;
use App\Http\Requests\API\UpdateItemReturnMasterAPIRequest;
use App\Models\Company;
use App\Models\CompanyDocumentAttachment;
use App\Models\CompanyFinancePeriod;
use App\Models\CompanyFinanceYear;
use App\Models\DocumentApproved;
use App\Models\DocumentMaster;
use App\Models\DocumentReferedHistory;
use App\Models\EmployeesDepartment;
use App\Models\ItemIssueMaster;
use App\Models\ItemReturnDetails;
use App\Models\ItemReturnDetailsRefferedBack;
use App\Models\ItemReturnMaster;
use App\Models\ItemReturnMasterRefferedBack;
use App\Models\Months;
use App\Models\SegmentMaster;
use App\Models\Unit;
use App\Models\WarehouseMaster;
use App\Models\YesNoSelection;
use App\Models\YesNoSelectionForMinus;
use App\Repositories\ItemReturnMasterRepository;
use App\Traits\AuditTrial;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\DB;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use App\helper\ItemTracking;

/**
 * Class ItemReturnMasterController
 * @package App\Http\Controllers\API
 */
class ItemReturnMasterAPIController extends AppBaseController
{
    /** @var  ItemReturnMasterRepository */
    private $itemReturnMasterRepository;

    public function __construct(ItemReturnMasterRepository $itemReturnMasterRepo)
    {
        $this->itemReturnMasterRepository = $itemReturnMasterRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/itemReturnMasters",
     *      summary="Get a listing of the ItemReturnMasters.",
     *      tags={"ItemReturnMaster"},
     *      description="Get all ItemReturnMasters",
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
     *                  @SWG\Items(ref="#/definitions/ItemReturnMaster")
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
        $this->itemReturnMasterRepository->pushCriteria(new RequestCriteria($request));
        $this->itemReturnMasterRepository->pushCriteria(new LimitOffsetCriteria($request));
        $itemReturnMasters = $this->itemReturnMasterRepository->all();

        return $this->sendResponse($itemReturnMasters->toArray(), trans('custom.item_return_masters_retrieved_successfully'));
    }


    /**
     * @param CreateItemReturnMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/itemReturnMasters",
     *      summary="Store a newly created ItemReturnMaster in storage",
     *      tags={"ItemReturnMaster"},
     *      description="Store ItemReturnMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ItemReturnMaster that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ItemReturnMaster")
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
     *                  ref="#/definitions/ItemReturnMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateItemReturnMasterAPIRequest $request)
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
            'ReturnDate' => 'required|date|before_or_equal:today',
            'serviceLineSystemID' => 'required|numeric|min:1',
            'wareHouseLocation' => 'required|numeric|min:1',
            'ReturnType' => 'required|numeric|min:1',
            //'customerID' => 'required|numeric|min:1',
            'ReturnRefNo' => 'required',
            'comment' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        DB::beginTransaction();
        if (isset($input['ReturnDate'])) {
            if ($input['ReturnDate']) {
                $input['ReturnDate'] = new Carbon($input['ReturnDate']);
            }
        }

        $documentDate = $input['ReturnDate'];
        $monthBegin = $input['FYBiggin'];
        $monthEnd = $input['FYEnd'];
        if (($documentDate >= $monthBegin) && ($documentDate <= $monthEnd)) {
        } else {
            DB::rollBack();
            return $this->sendError('Return date is not within the selected financial period !', 500);
        }

        $input['documentSystemID'] = 12;
        $input['documentID'] = 'SR';

        $lastSerial = ItemReturnMaster::where('companySystemID', $input['companySystemID'])
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
            $itemReturnCode = ($company->CompanyID . '\\' . $finYear . '\\' . $documentMaster['documentID'] . str_pad($lastSerialNumber, 6, '0', STR_PAD_LEFT));
            $input['itemReturnCode'] = $itemReturnCode;
        }

        $itemReturnMasters = $this->itemReturnMasterRepository->create($input);
        DB::commit();
        return $this->sendResponse($itemReturnMasters->toArray(), trans('custom.item_return_master_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/itemReturnMasters/{id}",
     *      summary="Display the specified ItemReturnMaster",
     *      tags={"ItemReturnMaster"},
     *      description="Get ItemReturnMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ItemReturnMaster",
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
     *                  ref="#/definitions/ItemReturnMaster"
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
        /** @var ItemReturnMaster $itemReturnMaster */
        $itemReturnMaster = $this->itemReturnMasterRepository->with(['confirmed_by', 'created_by', 'finance_period_by' => function ($query) {
            $query->selectRaw("CONCAT(DATE_FORMAT(dateFrom,'%d/%m/%Y'),' | ',DATE_FORMAT(dateTo,'%d/%m/%Y')) as financePeriod,companyFinancePeriodID");
        }, 'finance_year_by' => function ($query) {
            $query->selectRaw("CONCAT(DATE_FORMAT(bigginingDate,'%d/%m/%Y'),' | ',DATE_FORMAT(endingDate,'%d/%m/%Y')) as financeYear,companyFinanceYearID");
        },'segment_by','warehouse_by','customer_by'])->findWithoutFail($id);

        if (empty($itemReturnMaster)) {
            return $this->sendError(trans('custom.item_return_master_not_found'));
        }

        return $this->sendResponse($itemReturnMaster->toArray(), trans('custom.item_return_master_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateItemReturnMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/itemReturnMasters/{id}",
     *      summary="Update the specified ItemReturnMaster in storage",
     *      tags={"ItemReturnMaster"},
     *      description="Update ItemReturnMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ItemReturnMaster",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ItemReturnMaster that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ItemReturnMaster")
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
     *                  ref="#/definitions/ItemReturnMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateItemReturnMasterAPIRequest $request)
    {
        $input = $request->all();
        $input = array_except($input, ['created_by', 'confirmedByName','segment_by','warehouse_by','customer_by',
            'confirmedByEmpID', 'confirmedDate', 'confirmed_by', 'confirmedByEmpSystemID', 'finance_period_by', 'finance_year_by']);

        $input = $this->convertArrayToValue($input);
        $wareHouseError = array('type' => 'wareHouse');
        $serviceLineError = array('type' => 'serviceLine');

        if (isset($input['ReturnDate'])) {
            if ($input['ReturnDate']) {
                $input['ReturnDate'] = new Carbon($input['ReturnDate']);
            }
        }

        /** @var ItemReturnMaster $itemReturnMaster */
        $itemReturnMaster = $this->itemReturnMasterRepository->findWithoutFail($id);

        if (empty($itemReturnMaster)) {
            return $this->sendError(trans('custom.item_return_master_not_found'));
        }
        if (isset($input['serviceLineSystemID'])) {
            if ($input['serviceLineSystemID']) {
                $checkDepartmentActive = SegmentMaster::find($input['serviceLineSystemID']);
                if (empty($checkDepartmentActive)) {
                    return $this->sendError(trans('custom.department_not_found'), 500);
                }

                if ($checkDepartmentActive->isActive == 0) {
                    $this->itemReturnMasterRepository->update(['serviceLineSystemID' => null, 'serviceLineCode' => null], $id);
                    return $this->sendError('Please select a active department.', 500, $serviceLineError);
                }

                if ($checkDepartmentActive) {
                    $input['serviceLineCode'] = $checkDepartmentActive->ServiceLineCode;
                }
            }
        }
        if (isset($input['wareHouseLocation'])) {
            if ($input['wareHouseLocation']) {
                $checkWareHouseActive = WarehouseMaster::find($input['wareHouseLocation']);
                if (empty($checkWareHouseActive)) {
                    return $this->sendError(trans('custom.warehouse_not_found_1'), 500, $wareHouseError);
                }

                if ($checkWareHouseActive->isActive == 0) {
                    $this->itemReturnMasterRepository->update(['wareHouseLocation' => null], $id);
                    return $this->sendError('Please select a active warehouse.', 500, $wareHouseError);
                }
            }

            if ($input['wareHouseLocation'] != $itemReturnMaster->wareHouseLocation) {
                $resWareHouseUpdate = ItemTracking::updateTrackingDetailWareHouse($input['wareHouseLocation'], $id, $itemReturnMaster->documentSystemID);

                if (!$resWareHouseUpdate['status']) {
                    return $this->sendError($resWareHouseUpdate['message'], 500);
                }
            }
        }

        if ($itemReturnMaster->confirmedYN == 0 && $input['confirmedYN'] == 1) {

            $companyFinanceYear = \Helper::companyFinanceYearCheck($input);
            if (!$companyFinanceYear["success"]) {
                return $this->sendError($companyFinanceYear["message"], 500);
            }

            $trackingValidation = ItemTracking::validateTrackingOnDocumentConfirmation($itemReturnMaster->documentSystemID, $id);

            if (!$trackingValidation['status']) {
                return $this->sendError($trackingValidation["message"], 500, ['type' => 'confirm']);
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
                'ReturnDate' => 'required|date|before_or_equal:today',
                'serviceLineSystemID' => 'required|numeric|min:1',
                'wareHouseLocation' => 'required|numeric|min:1',
                'ReturnType' => 'required|numeric|min:1',
                //'customerID' => 'required|numeric|min:1',
                'ReturnRefNo' => 'required',
                'comment' => 'required',
            ]);

            if ($validator->fails()) {
                return $this->sendError($validator->messages(), 422);
            }

            $documentDate = $input['ReturnDate'];
            $monthBegin = $input['FYBiggin'];
            $monthEnd = $input['FYEnd'];
            if (($documentDate >= $monthBegin) && ($documentDate <= $monthEnd)) {
            } else {
                return $this->sendError('Return date is not within the selected financial period !', 500);
            }

            $checkItems = ItemReturnDetails::where('itemReturnAutoID', $id)
                ->count();
            if ($checkItems == 0) {
                return $this->sendError('Every return should have at least one item', 500);
            }

            $checkQuantity = ItemReturnDetails::where('itemReturnAutoID', $id)
                ->where(function ($q) {
                    $q->where('qtyIssued', '<=', 0)
                        ->orWhereNull('qtyIssued');
                })
                ->count();

            if ($checkQuantity > 0) {
                return $this->sendError('Every Item should have at least one minimum Qty Requested', 500);
            }

            $checkCost = ItemReturnDetails::where('itemReturnAutoID', $id)
                ->where(function ($q) {
                    $q->where('unitCostLocal', '<=', 0)
                        ->orWhere('unitCostLocal', '<=', 0)->orWhereNull('qtyIssued');
                })
                ->count();

            if ($checkCost > 0) {
                return $this->sendError('Unit Cost should be greater than 0 for every items', 500);
            }

            $itemReturnDetails = ItemReturnDetails::where('itemReturnAutoID', $input['itemReturnAutoID'])->get();

            $finalError = array('item_is_not_issued' => array());
            $error_count = 0;


            foreach ($itemReturnDetails as $detail) {
                if ($detail['qtyIssuedDefaultMeasure'] > $detail['qtyFromIssue']) {
                    return $this->sendError("Return quantity should not be greater than issues quantity. Please check again.", 500);
                }

                $itemIssuesCount = ItemIssueMaster::where('itemIssueAutoID', $detail['issueCodeSystem'])
                    ->where('companySystemID', $input['companySystemID'])
                    ->where('serviceLineSystemID', $input['serviceLineSystemID'])
                    ->where('wareHouseFrom', $input['wareHouseLocation'])
                    ->where('approved', -1)
                    ->whereHas('details', function ($q) use ($detail) {
                        $q->having('itemCodeSystem', $detail['itemCodeSystem']);
                    })
                    ->count();

                if ($itemIssuesCount == 0) {
                    array_push($finalError['item_is_not_issued'], $detail['itemPrimaryCode']);
                    $error_count++;
                    //return $this->sendError('Selected item is not issued. Please check again', 500);
                }
            }

            $confirm_error = array('type' => 'confirm_error', 'data' => $finalError);
            if ($error_count > 0) {
                return $this->sendError("You cannot confirm this document.", 500, $confirm_error);
            }

            $amount = 0;
            /*ItemReturnDetails::where('itemReturnAutoID', $id)
                                        ->sum('issueCostRptTotal');*/

            $input['RollLevForApp_curr'] = 1;
            $params = array('autoID' => $id,
                'company' => $itemReturnMaster->companySystemID,
                'document' => $itemReturnMaster->documentSystemID,
                'segment' => $input['serviceLineSystemID'],
                'category' => 0,
                'amount' => $amount
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

        $itemReturnMaster = $this->itemReturnMasterRepository->update($input, $id);

        return $this->sendReponseWithDetails($itemReturnMaster->toArray(), 'Material Return Master Updated Successfully',1,$confirm['data'] ?? null);
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/itemReturnMasters/{id}",
     *      summary="Remove the specified ItemReturnMaster from storage",
     *      tags={"ItemReturnMaster"},
     *      description="Delete ItemReturnMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ItemReturnMaster",
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
        /** @var ItemReturnMaster $itemReturnMaster */
        $itemReturnMaster = $this->itemReturnMasterRepository->findWithoutFail($id);

        if (empty($itemReturnMaster)) {
            return $this->sendError(trans('custom.item_return_master_not_found'));
        }

        $itemReturnMaster->delete();

        return $this->sendResponse($id, trans('custom.item_return_master_deleted_successfully'));
    }

    /**
     * get All Materiel Returns By Company
     * POST /getAllMaterielIssuesByCompany
     *
     * @param Request $request
     *
     * @return Response
     */

    public function getAllMaterielReturnByCompany(Request $request)
    {

        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input, array('serviceLineSystemID', 'confirmedYN', 'approved', 'wareHouseLocation', 'month', 'year'));

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $search = $request->input('search.value');
        $grvLocation = $request['wareHouseLocation'];
        $grvLocation = (array)$grvLocation;
        $grvLocation = collect($grvLocation)->pluck('id');

        $serviceLineSystemID = $request['serviceLineSystemID'];
        $serviceLineSystemID = (array)$serviceLineSystemID;
        $serviceLineSystemID = collect($serviceLineSystemID)->pluck('id');

        $itemReturnMaster = $this->itemReturnMasterRepository->itemReturnListQuery($request, $input, $search,$grvLocation, $serviceLineSystemID);

        return \DataTables::eloquent($itemReturnMaster)
            ->addColumn('Actions', 'Actions', "Actions")
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('itemReturnAutoID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    /**
     * get Materiel Return Form Data
     * Get /getMaterielIssueFormData
     *
     * @param Request $request
     *
     * @return Response
     */
    public function getMaterielReturnFormData(Request $request)
    {
        $companyId = $request['companyId'];

        $segments = SegmentMaster::where("companySystemID", $companyId)->approved()->withAssigned($companyId);
        if (isset($request['type']) && $request['type'] != 'filter') {
            $segments = $segments->where('isActive', 1);
        }
        $segments = $segments->get();

        /** Yes and No Selection */
        $yesNoSelection = YesNoSelection::all();

        /** all Units*/
        $yesNoSelectionForMinus = YesNoSelectionForMinus::all();

        $month = Months::all();

        $years = ItemReturnMaster::select(DB::raw("YEAR(createdDateTime) as year"))
            ->whereNotNull('createdDateTime')
            ->groupby('year')
            ->orderby('year', 'desc')
            ->get();

        $wareHouseLocation = WarehouseMaster::where("companySystemID", $companyId);
        if (isset($request['type']) && $request['type'] != 'filter') {
            $wareHouseLocation = $wareHouseLocation->where('isActive', 1);
        }
        $wareHouseLocation = $wareHouseLocation->get();

        $types = array(array('value' => 1, "label" => trans('custom.issue_return')),
            array('value' => 2, "label" => trans('custom.damaged_repaired_return')));

        $financialYears = array(array('value' => intval(date("Y")), 'label' => date("Y")),
            array('value' => intval(date("Y", strtotime("-1 year"))), 'label' => date("Y", strtotime("-1 year"))));

        $companyFinanceYear = \Helper::companyFinanceYear($companyId);

        $contracts = "";

        $units = Unit::all();

        $output = array(
            'segments' => $segments,
            'yesNoSelection' => $yesNoSelection,
            'yesNoSelectionForMinus' => $yesNoSelectionForMinus,
            'month' => $month,
            'years' => $years,
            'wareHouseLocation' => $wareHouseLocation,
            'financialYears' => $financialYears,
            'types' => $types,
            'companyFinanceYear' => $companyFinanceYear,
            'contracts' => $contracts,
            'units' => $units
        );

        return $this->sendResponse($output, trans('custom.record_retrieved_successfully_1'));
    }

    /**
     * Display the specified Materiel Return Audit.
     * GET|HEAD /getMaterielReturnAudit
     *
     * @param  int $id
     *
     * @return Response
     */
    public function getMaterielReturnAudit(Request $request)
    {
        $id = $request->get('id');

        $materielReturn = $this->itemReturnMasterRepository->getAudit($id);

        if (empty($materielReturn)) {
            return $this->sendError(trans('custom.materiel_return_not_found_1'));
        }

        $materielReturn->docRefNo = \Helper::getCompanyDocRefNo($materielReturn->companySystemID, $materielReturn->documentSystemID);

        return $this->sendResponse($materielReturn->toArray(), trans('custom.materiel_return_retrieved_successfully'));
    }

    public function printItemReturn(Request $request)
    {
        $id = $request->get('id');
        $materielReturn = $this->itemReturnMasterRepository->getAudit($id);

        if (empty($materielReturn)) {
            return $this->sendError(trans('custom.materiel_return_not_found_1'));
        }

        $materielReturn->docRefNo = \Helper::getCompanyDocRefNo($materielReturn->companySystemID, $materielReturn->documentSystemID);

        $array = array('entity' => $materielReturn);
        $time = strtotime("now");
        $fileName = 'materiel_return_' . $id . '_' . $time . '.pdf';
        $html = view('print.materiel_return', $array);
        $htmlFooter = view('print.materiel_return_footer', $array);
        $mpdf = new \Mpdf\Mpdf(['tempDir' => public_path('tmp'), 'mode' => 'utf-8', 'format' => 'A4-L', 'setAutoTopMargin' => 'stretch', 'autoMarginPadding' => -10]);
        $mpdf->AddPage('L');
        $mpdf->setAutoBottomMargin = 'stretch';
        $mpdf->SetHTMLFooter($htmlFooter);
        $mpdf->WriteHTML($html);
        return $mpdf->Output($fileName, 'I');
    }

    /**
     * get Materiel Return Approval By User
     * POST /getMaterielReturnApprovalByUser
     *
     * @param Request $request
     *
     * @return Response
     */

    public function getMaterielReturnApprovalByUser(Request $request)
    {

        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input, array('serviceLineSystemID', 'confirmedYN', 'approved', 'wareHouseFrom', 'month', 'year'));

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $companyId = $input['companyId'];
        $empID = \Helper::getEmployeeSystemID();

        $search = $request->input('search.value');
        $itemReturnMaster = DB::table('erp_documentapproved')
            ->select(
                'employeesdepartments.approvalDeligated',
                'erp_itemreturnmaster.*',
                'employees.empName As created_emp',
                'serviceline.ServiceLineDes As MRServiceLineDes',
                'warehousemaster.wareHouseDescription As MRWareHouseDescription',
                'erp_documentapproved.documentApprovedID',
                'rollLevelOrder',
                'approvalLevelID',
                'documentSystemCode')
            ->join('employeesdepartments', function ($query) use ($companyId, $empID) {
                $query->on('erp_documentapproved.approvalGroupID', '=', 'employeesdepartments.employeeGroupID')
                    ->on('erp_documentapproved.documentSystemID', '=', 'employeesdepartments.documentSystemID')
                    ->on('erp_documentapproved.companySystemID', '=', 'employeesdepartments.companySystemID');

                $serviceLinePolicy = CompanyDocumentAttachment::where('companySystemID', $companyId)
                    ->where('documentSystemID', 12)
                    ->first();

                if ($serviceLinePolicy && $serviceLinePolicy->isServiceLineApproval == -1) {
                    //$query->on('erp_documentapproved.serviceLineSystemID', '=', 'employeesdepartments.ServiceLineSystemID');
                }

                $query->whereIn('employeesdepartments.documentSystemID', [12])
                    ->where('employeesdepartments.companySystemID', $companyId)
                    ->where('employeesdepartments.employeeSystemID', $empID)
                    ->where('employeesdepartments.isActive', 1)
                    ->where('employeesdepartments.removedYN', 0);
            })
            ->join('erp_itemreturnmaster', function ($query) use ($companyId, $search) {
                $query->on('erp_documentapproved.documentSystemCode', '=', 'itemReturnAutoID')
                    ->on('erp_documentapproved.rollLevelOrder', '=', 'RollLevForApp_curr')
                    ->where('erp_itemreturnmaster.companySystemID', $companyId)
                    ->where('erp_itemreturnmaster.approved', 0)
                    ->where('erp_itemreturnmaster.confirmedYN', 1);
            })
            ->where('erp_documentapproved.approvedYN', 0)
            ->leftJoin('employees', 'createdUserSystemID', 'employees.employeeSystemID')
            ->leftJoin('warehousemaster', 'erp_itemreturnmaster.wareHouseLocation', 'warehousemaster.wareHouseSystemCode')
            ->leftJoin('serviceline', 'erp_itemreturnmaster.serviceLineSystemID', 'serviceline.serviceLineSystemID')
            ->where('erp_documentapproved.rejectedYN', 0)
            ->whereIn('erp_documentapproved.documentSystemID', [12])
            ->where('erp_documentapproved.companySystemID', $companyId);


        if (array_key_exists('serviceLineSystemID', $input)) {
            if ($input['serviceLineSystemID'] && !is_null($input['serviceLineSystemID'])) {
                $itemReturnMaster->where('erp_itemreturnmaster.serviceLineSystemID', $input['serviceLineSystemID']);
            }
        }

        if (array_key_exists('wareHouseLocation', $input)) {
            if ($input['wareHouseLocation'] && !is_null($input['wareHouseLocation'])) {
                $itemReturnMaster->where('erp_itemreturnmaster.wareHouseLocation', $input['wareHouseLocation']);
            }
        }

        if (array_key_exists('month', $input)) {
            if ($input['month'] && !is_null($input['month'])) {
                $itemReturnMaster->whereMonth('erp_itemreturnmaster.ReturnDate', '=', $input['month']);
            }
        }

        if (array_key_exists('year', $input)) {
            if ($input['year'] && !is_null($input['year'])) {
                $itemReturnMaster->whereYear('erp_itemreturnmaster.ReturnDate', '=', $input['year']);
            }
        }

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $itemReturnMaster = $itemReturnMaster->where(function ($query) use ($search) {
                $query->where('itemReturnCode', 'LIKE', "%{$search}%")
                    ->orWhere('comment', 'LIKE', "%{$search}%");
            });
        }

        $isEmployeeDischarched = \Helper::checkEmployeeDischarchedYN();

        if ($isEmployeeDischarched == 'true') {
            $itemReturnMaster = [];
        }

        return \DataTables::of($itemReturnMaster)
            ->addColumn('Actions', 'Actions', "Actions")
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('itemReturnAutoID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }


    /**
     * get Materiel Return Approved By User
     * POST /getMaterielReturnApprovedByUser
     *
     * @param Request $request
     *
     * @return Response
     */

    public function getMaterielReturnApprovedByUser(Request $request)
    {

        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input, array('serviceLineSystemID', 'confirmedYN', 'approved', 'wareHouseFrom', 'month', 'year'));

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $companyId = $input['companyId'];
        $empID = \Helper::getEmployeeSystemID();

        $search = $request->input('search.value');
        $itemReturnMaster = DB::table('erp_documentapproved')
            ->select(
                'erp_itemreturnmaster.*',
                'employees.empName As created_emp',
                'serviceline.ServiceLineDes As MRServiceLineDes',
                'warehousemaster.wareHouseDescription As MRWareHouseDescription',
                'erp_documentapproved.documentApprovedID',
                'rollLevelOrder',
                'approvalLevelID',
                'documentSystemCode')
            ->join('erp_itemreturnmaster', function ($query) use ($companyId, $search) {
                $query->on('erp_documentapproved.documentSystemCode', '=', 'itemReturnAutoID')
                    ->where('erp_itemreturnmaster.companySystemID', $companyId)
                    ->where('erp_itemreturnmaster.confirmedYN', 1);
            })
            ->where('erp_documentapproved.approvedYN', -1)
            ->leftJoin('employees', 'createdUserSystemID', 'employees.employeeSystemID')
            ->leftJoin('warehousemaster', 'erp_itemreturnmaster.wareHouseLocation', 'warehousemaster.wareHouseSystemCode')
            ->leftJoin('serviceline', 'erp_itemreturnmaster.serviceLineSystemID', 'serviceline.serviceLineSystemID')
            ->where('erp_documentapproved.rejectedYN', 0)
            ->whereIn('erp_documentapproved.documentSystemID', [12])
            ->where('erp_documentapproved.companySystemID', $companyId)
            ->where('erp_documentapproved.employeeSystemID', $empID);

        if (array_key_exists('serviceLineSystemID', $input)) {
            if ($input['serviceLineSystemID'] && !is_null($input['serviceLineSystemID'])) {
                $itemReturnMaster->where('erp_itemreturnmaster.serviceLineSystemID', $input['serviceLineSystemID']);
            }
        }

        if (array_key_exists('wareHouseLocation', $input)) {
            if ($input['wareHouseLocation'] && !is_null($input['wareHouseLocation'])) {
                $itemReturnMaster->where('erp_itemreturnmaster.wareHouseLocation', $input['wareHouseLocation']);
            }
        }

        if (array_key_exists('month', $input)) {
            if ($input['month'] && !is_null($input['month'])) {
                $itemReturnMaster->whereMonth('erp_itemreturnmaster.ReturnDate', '=', $input['month']);
            }
        }

        if (array_key_exists('year', $input)) {
            if ($input['year'] && !is_null($input['year'])) {
                $itemReturnMaster->whereYear('erp_itemreturnmaster.ReturnDate', '=', $input['year']);
            }
        }

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $itemReturnMaster = $itemReturnMaster->where(function ($query) use ($search) {
                $query->where('itemReturnCode', 'LIKE', "%{$search}%")
                    ->orWhere('comment', 'LIKE', "%{$search}%");
            });
        }

        return \DataTables::of($itemReturnMaster)
            ->addColumn('Actions', 'Actions', "Actions")
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('itemReturnAutoID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    public function materielReturnReopen(Request $request)
    {
        $input = $request->all();

        $id = $input['itemReturnAutoID'];
        $itemReturnMaster = $this->itemReturnMasterRepository->findWithoutFail($id);
        $emails = array();
        if (empty($itemReturnMaster)) {
            return $this->sendError(trans('custom.materiel_return_not_found_1'));
        }

        if ($itemReturnMaster->approved == -1) {
            return $this->sendError(trans('custom.you_cannot_reopen_this_materiel_return_it_is_alrea'));
        }

        if ($itemReturnMaster->RollLevForApp_curr > 1) {
            return $this->sendError(trans('custom.you_cannot_reopen_this_materiel_return_it_is_alrea_1'));
        }

        if ($itemReturnMaster->confirmedYN == 0) {
            return $this->sendError(trans('custom.you_cannot_reopen_this_materiel_return_it_is_not_c'));
        }

        $updateInput = ['confirmedYN' => 0, 'confirmedByEmpSystemID' => null, 'confirmedByEmpID' => null,
            'confirmedByName' => null, 'confirmedDate' => null, 'RollLevForApp_curr' => 1];

        $this->itemReturnMasterRepository->update($updateInput, $id);

        $employee = \Helper::getEmployeeInfo();

        $document = DocumentMaster::where('documentSystemID', $itemReturnMaster->documentSystemID)->first();

        $cancelDocNameBody = $document->documentDescription . ' <b>' . $itemReturnMaster->itemReturnCode . '</b>';
        $cancelDocNameSubject = $document->documentDescription . ' ' . $itemReturnMaster->itemReturnCode;

        $subject = $cancelDocNameSubject . ' ' . trans('email.is_reopened');

        $body = '<p>' . $cancelDocNameBody . ' ' . trans('email.is_reopened_by', ['empID' => $employee->empID, 'empName' => $employee->empFullName]) . '</p><p>' . trans('email.comment') . ' : ' . $input['reopenComments'] . '</p>';

        $documentApproval = DocumentApproved::where('companySystemID', $itemReturnMaster->companySystemID)
            ->where('documentSystemCode', $itemReturnMaster->itemReturnAutoID)
            ->where('documentSystemID', $itemReturnMaster->documentSystemID)
            ->where('rollLevelOrder', 1)
            ->first();

        if ($documentApproval) {
            if ($documentApproval->approvedYN == 0) {
                $companyDocument = CompanyDocumentAttachment::where('companySystemID', $itemReturnMaster->companySystemID)
                    ->where('documentSystemID', $itemReturnMaster->documentSystemID)
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
            ->where('companySystemID', $itemReturnMaster->companySystemID)
            ->where('documentSystemID', $itemReturnMaster->documentSystemID)
            ->delete();

        /*Audit entry*/
        AuditTrial::createAuditTrial($itemReturnMaster->documentSystemID,$id,$input['reopenComments'],'Reopened');

        return $this->sendResponse($itemReturnMaster->toArray(), trans('custom.materiel_return_reopened_successfully'));
    }

    public function materielReturnReferBack(Request $request)
    {
        $input = $request->all();

        $id = $input['id'];

        $itemReturn = $this->itemReturnMasterRepository->find($id);
        if (empty($itemReturn)) {
            return $this->sendError(trans('custom.materiel_return_not_found_1'));
        }

        if ($itemReturn->refferedBackYN != -1) {
            return $this->sendError(trans('custom.you_cannot_refer_back_this_materiel_return'));
        }

        $itemReturnArray = $itemReturn->toArray();

        $storeMRHistory = ItemReturnMasterRefferedBack::insert($itemReturnArray);

        $fetchDetails = ItemReturnDetails::where('itemReturnAutoID', $id)
            ->get();

        if (!empty($fetchDetails)) {
            foreach ($fetchDetails as $detail) {
                $detail['timesReferred'] = $itemReturn->timesReferred;
            }
        }

        $itemReturnDetailArray = $fetchDetails->toArray();

        $storeMRDetailHistory = ItemReturnDetailsRefferedBack::insert($itemReturnDetailArray);

        $fetchDocumentApproved = DocumentApproved::where('documentSystemCode', $id)
            ->where('companySystemID', $itemReturn->companySystemID)
            ->where('documentSystemID', $itemReturn->documentSystemID)
            ->get();

        if (!empty($fetchDocumentApproved)) {
            foreach ($fetchDocumentApproved as $DocumentApproved) {
                $DocumentApproved['refTimes'] = $itemReturn->timesReferred;
            }
        }

        $DocumentApprovedArray = $fetchDocumentApproved->toArray();

        $storeDocumentRefereedHistory = DocumentReferedHistory::insert($DocumentApprovedArray);

        $deleteApproval = DocumentApproved::where('documentSystemCode', $id)
            ->where('companySystemID', $itemReturn->companySystemID)
            ->where('documentSystemID', $itemReturn->documentSystemID)
            ->delete();

        if ($deleteApproval) {
            $updateArray = ['refferedBackYN' => 0,'confirmedYN' => 0,'confirmedByEmpSystemID' => null,
                'confirmedByEmpID' => null,'confirmedByName' => null,'confirmedDate' => null,'RollLevForApp_curr' => 1];

            $this->itemReturnMasterRepository->update($updateArray,$id);
        }

        return $this->sendResponse($itemReturn->toArray(), trans('custom.materiel_return_amend_successfully'));
    }
}
