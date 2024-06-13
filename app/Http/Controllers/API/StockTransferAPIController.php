<?php
/**
 * =============================================
 * -- File Name : StockTransferAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Stock Transfer
 * -- Author : Mohamed Nazir
 * -- Create date : 13-July 2018
 * -- Description : This file contains the all CRUD for Stock Transfer
 * -- REVISION HISTORY
 * -- Date: 13-July 2018 By: Nazir Description: Added new functions named as getStockTransferMasterView() For load Master View
 * -- Date: 24-July 2018 By: Fayas Description: Added new functions named as getStockTransferForReceive(),getStockTransferDetailsByMaster()
 * -- Date: 30-July 2018 By: Fayas Description: Added new functions named as printStockTransfer()
 * -- Date: 27-August 2018 By: Fayas Description: Added new functions named as stockTransferReopen()
 * -- Date: 29-November 2018 By: Fayas Description: Added new functions named as stockTransferReferBack()
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateStockTransferAPIRequest;
use App\Http\Requests\API\UpdateStockTransferAPIRequest;
use App\Models\CompanyDocumentAttachment;
use App\Models\CompanyFinancePeriod;
use App\Models\SystemGlCodeScenarioDetail;
use App\Models\CompanyFinanceYear;
use App\Models\CustomerMaster;
use App\Models\DocumentApproved;
use App\Models\ChartOfAccountsAssigned;
use App\Models\DocumentMaster;
use App\Models\DocumentReferedHistory;
use App\Models\EmployeesDepartment;
use App\Models\ItemAssigned;
use App\Models\Months;
use App\Models\Company;
use App\Models\SegmentMaster;
use App\Models\StockReceive;
use App\Models\StockReceiveDetails;
use App\Models\StockTransfer;
use App\Models\StockTransferDetailsRefferedBack;
use App\Models\StockTransferRefferedBack;
use App\Models\SupplierMaster;
use App\Models\WarehouseMaster;
use App\Models\YesNoSelection;
use App\Models\YesNoSelectionForMinus;
use App\Models\StockTransferDetails;
use App\Repositories\StockTransferRepository;
use App\Traits\AuditTrial;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Repositories\UserRepository;
use Carbon\Carbon;
use Response;
use App\helper\ItemTracking;
use App\Models\ItemMaster;
use App\Models\UnitConversion;
use App\Models\Unit;
/**
 * Class StockTransferController
 * @package App\Http\Controllers\API
 */
class StockTransferAPIController extends AppBaseController
{
    /** @var  StockTransferRepository */
    private $stockTransferRepository;
    private $userRepository;

    public function __construct(StockTransferRepository $stockTransferRepo, UserRepository $userRepo)
    {
        $this->stockTransferRepository = $stockTransferRepo;
        $this->userRepository = $userRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/stockTransfers",
     *      summary="Get a listing of the StockTransfers.",
     *      tags={"StockTransfer"},
     *      description="Get all StockTransfers",
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
     *                  @SWG\Items(ref="#/definitions/StockTransfer")
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
        $this->stockTransferRepository->pushCriteria(new RequestCriteria($request));
        $this->stockTransferRepository->pushCriteria(new LimitOffsetCriteria($request));
        $stockTransfers = $this->stockTransferRepository->all();

        return $this->sendResponse($stockTransfers->toArray(), 'Stock Transfers retrieved successfully');
    }

    /**
     * @param CreateStockTransferAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/stockTransfers",
     *      summary="Store a newly created StockTransfer in storage",
     *      tags={"StockTransfer"},
     *      description="Store StockTransfer",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="StockTransfer that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/StockTransfer")
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
     *                  ref="#/definitions/StockTransfer"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateStockTransferAPIRequest $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToValue($input);
        $employee = \Helper::getEmployeeInfo();

        $input['createdPCID'] = gethostname();
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
            'locationFrom' => 'required|numeric|min:1',
            'locationTo' => 'required|numeric|min:1',
            'companyFinancePeriodID' => 'required|numeric|min:1',
            'companyFinanceYearID' => 'required|numeric|min:1',
            'tranferDate' => 'required|date|before_or_equal:today',
            'companyToSystemID' => 'required|numeric|min:1',
            'companyFromSystemID' => 'required|numeric|min:1',
            'serviceLineSystemID' => 'required|numeric|min:1',
            'refNo' => 'required',
            'comment' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        if (isset($input['tranferDate'])) {
            if ($input['tranferDate']) {
                $input['tranferDate'] = new Carbon($input['tranferDate']);
            }
        }

         $warehouse = WarehouseMaster::where("wareHouseSystemCode", $input['locationTo'])
                                    ->first();

        if (!$warehouse) {
            return $this->sendError('Location To not found', 500);
        }

        if ($warehouse->manufacturingYN == 1) {
            if (is_null($warehouse->WIPGLCode)) {
                return $this->sendError('Please assigned WIP GLCode for this warehouse', 500);
            } else {
                $checkGLIsAssigned = ChartOfAccountsAssigned::checkCOAAssignedStatus($warehouse->WIPGLCode, $input['companyToSystemID']);
                if (!$checkGLIsAssigned) {
                    return $this->sendError('Assigned WIP GL Code is not assigned to this company!', 500);
                }
            }
        }


        $documentDate = $input['tranferDate'];
        $monthBegin = $input['FYBiggin'];
        $monthEnd = $input['FYEnd'];

        if (($documentDate >= $monthBegin) && ($documentDate <= $monthEnd)) {
        } else {
            return $this->sendError('Transfer date is not within the selected financial period !', 500);
        }


        if ($input['interCompanyTransferYN']) {

            $checkCustomer = CustomerMaster::where('companyLinkedToSystemID', $input['companyToSystemID'])->where('approvedYN',1)->count();
            if ($checkCustomer == 0) {
                $cusError = array('type' => 'cus_not');
                return $this->sendError('Customer is not linked to the selected company. Please create a customer and link to the company.', 500, $cusError);
            }

            $checkSupplier = SupplierMaster::where('companyLinkedToSystemID', $input['companyFromSystemID'])->where('approvedYN',1)->count();
            if ($checkSupplier == 0) {
                $supError = array('type' => 'sup_not');
                return $this->sendError('Supplier is not linked to the selected company. Please create a supplier and link to the company.', 500, $supError);
            }

            $toCompanyFinancePeriod = CompanyFinancePeriod::where('companySystemID', $input['companyToSystemID'])
                ->where('departmentSystemID', 10)
                ->where('isActive', -1)
                ->whereHas('finance_year_by', function($query) {
                    $query->where('isCurrent', -1);
                })
                // ->where('dateFrom', '<', $documentDate)
                // ->where('dateTo', '>', $documentDate)
                ->where('isCurrent', -1)
                ->count();

            $companyTo = Company::where('companySystemID', $input['companyToSystemID'])->first();

            if ($toCompanyFinancePeriod == 0) {
                return $this->sendError('Financial year and period is not activated in ' . $companyTo->CompanyName, 500);
            }
        }

        $lastSerial = StockTransfer::where('companySystemID', $input['companySystemID'])
            ->where('companyFinanceYearID', $input['companyFinanceYearID'])
            ->orderBy('serialNo', 'desc')
            ->first();

        $lastSerialNumber = 1;
        if ($lastSerial) {
            $lastSerialNumber = intval($lastSerial->serialNo) + 1;
        }

        //checking selected segment is active
        $segments = SegmentMaster::where("serviceLineSystemID", $input['serviceLineSystemID'])
            ->where('companySystemID', $input['companySystemID'])
            ->where('isActive', 1)
            ->first();

        if (empty($segments)) {
            return $this->sendError('Selected segment is not active. Please select an active segment', 500);
        }

        if ($input['locationFrom'] == $input['locationTo']) {
            return $this->sendError('Location From and Location To  cannot be same', 500);
        }

        $segment = SegmentMaster::where('serviceLineSystemID', $input['serviceLineSystemID'])->first();
        if ($segment) {
            $input['serviceLineCode'] = $segment->ServiceLineCode;
        }

        $company = Company::where('companySystemID', $input['companySystemID'])->first();
        if ($company) {
            $input['companyID'] = $company->CompanyID;
        }

        $companyFrom = Company::where('companySystemID', $input['companyFromSystemID'])->first();
        if ($companyFrom) {
            $input['companyFrom'] = $companyFrom->CompanyID;
        }

        $companyTo = Company::where('companySystemID', $input['companyToSystemID'])->first();
        if ($companyTo) {
            $input['companyTo'] = $companyTo->CompanyID;
        }

        $input['serialNo'] = $lastSerialNumber;

        $documentMaster = DocumentMaster::where('documentSystemID', $input['documentSystemID'])->first();
        if ($documentMaster) {
            $input['documentID'] = $documentMaster->documentID;
        }

        $companyfinanceyear = CompanyFinanceYear::where('companyFinanceYearID', $input['companyFinanceYearID'])
            ->where('companySystemID', $input['companySystemID'])
            ->first();

        if ($companyfinanceyear) {
            $startYear = $companyfinanceyear['bigginingDate'];
            $finYearExp = explode('-', $startYear);
            $finYear = $finYearExp[0];
        } else {
            $finYear = date("Y");
        }

        if ($input['interCompanyTransferYN']) {
            $input['interCompanyTransferYN'] = -1;
        } else {
            $input['interCompanyTransferYN'] = 0;
        }

        if ($documentMaster) {
            $stockTransferCode = ($company->CompanyID . '\\' . $finYear . '\\' . $documentMaster['documentID'] . str_pad($lastSerialNumber, 6, '0', STR_PAD_LEFT));
            $input['stockTransferCode'] = $stockTransferCode;
        }

        $stockTransfers = $this->stockTransferRepository->create($input);

        return $this->sendResponse($stockTransfers->toArray(), 'Stock Transfer saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/stockTransfers/{id}",
     *      summary="Display the specified StockTransfer",
     *      tags={"StockTransfer"},
     *      description="Get StockTransfer",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of StockTransfer",
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
     *                  ref="#/definitions/StockTransfer"
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
        /** @var StockTransfer $stockTransfer */
        $stockTransfer = $this->stockTransferRepository->with(['created_by', 'confirmed_by', 'segment_by', 'finance_period_by' => function ($query) {
            $query->selectRaw("CONCAT(DATE_FORMAT(dateFrom,'%d/%m/%Y'),' | ',DATE_FORMAT(dateTo,'%d/%m/%Y')) as financePeriod,companyFinancePeriodID");
        }, 'finance_year_by' => function ($query) {
            $query->selectRaw("CONCAT(DATE_FORMAT(bigginingDate,'%d/%m/%Y'),' | ',DATE_FORMAT(endingDate,'%d/%m/%Y')) as financeYear,companyFinanceYearID");
        },'location_to_by','location_from_by','company_from','company_to'])->findWithoutFail($id);

        if (empty($stockTransfer)) {
            return $this->sendError('Stock Transfer not found');
        }

        return $this->sendResponse($stockTransfer->toArray(), 'Stock Transfer retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateStockTransferAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/stockTransfers/{id}",
     *      summary="Update the specified StockTransfer in storage",
     *      tags={"StockTransfer"},
     *      description="Update StockTransfer",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of StockTransfer",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="StockTransfer that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/StockTransfer")
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
     *                  ref="#/definitions/StockTransfer"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateStockTransferAPIRequest $request)
    {

        $input = $request->all();
        $input = array_except($input, ['created_by', 'confirmed_by', 'segment_by', 'finance_period_by', 'finance_year_by','location_to_by','location_from_by','company_from','company_to']);
        $input = $this->convertArrayToValue($input);
        $wareHouseFromError = array('type' => 'locationFrom');
        $wareHouseToError   = array('type' => 'locationTo');
        $serviceLineError   = array('type' => 'serviceLine');
        $companyToError   = array('type' => 'companyTo');

        /** @var StockTransfer $stockTransfer */
        $stockTransfer = $this->stockTransferRepository->findWithoutFail($id);

        if (empty($stockTransfer)) {
            return $this->sendError('Stock Transfer not found');
        }

        if (isset($input['tranferDate'])) {
            if ($input['tranferDate']) {
                $input['tranferDate'] = new Carbon($input['tranferDate']);
            }
        }

        if (isset($input['serviceLineSystemID'])) {
            $segments = SegmentMaster::where("serviceLineSystemID", $input['serviceLineSystemID'])
                ->where('companySystemID', $input['companySystemID'])
                ->where('isActive', 1)
                ->first();

            if (empty($segments)) {
                $this->stockTransferRepository->update(['serviceLineSystemID' => null,'serviceLineCode' => null],$id);
                return $this->sendError('Selected department is not active. Please select an active department', 500, $serviceLineError);
            }

            if ($segments) {
                $input['serviceLineCode'] = $segments->ServiceLineCode;
            }
        }

        if (isset($input['locationFrom'])) {
            if($input['locationFrom']) {
                $checkWareHouseActiveFrom = WarehouseMaster::find($input['locationFrom']);
                if (empty($checkWareHouseActiveFrom)) {
                    return $this->sendError('Location from not found', 500, $wareHouseFromError);
                }

                if ($checkWareHouseActiveFrom->isActive == 0) {
                    $this->stockTransferRepository->update(['locationFrom' => null], $id);
                    return $this->sendError('Selected location from is not active. Please select an active location from', 500, $wareHouseFromError);
                }
            }
        }

        if (isset($input['locationTo'])) {

            if($input['locationTo']) {
                $checkWareHouseActiveTo = WarehouseMaster::find($input['locationTo']);
                if (empty($checkWareHouseActiveTo)) {
                    return $this->sendError('Location to not found', 500, $wareHouseToError);
                }

                if ($checkWareHouseActiveTo->isActive == 0) {
                    $this->stockTransferRepository->update(['locationTo' => null], $id);
                    return $this->sendError('Selected location to is not active.Please select an active location to', 500, $wareHouseToError);
                }

                if ($checkWareHouseActiveTo->manufacturingYN == 1) {
                    if (is_null($checkWareHouseActiveTo->WIPGLCode)) {
                        return $this->sendError('Please assigned WIP GLCode for this warehouse', 500);
                    } else {
                        $checkGLIsAssigned = ChartOfAccountsAssigned::checkCOAAssignedStatus($checkWareHouseActiveTo->WIPGLCode, $input['companyToSystemID']);
                        if (!$checkGLIsAssigned) {
                            return $this->sendError('Assigned WIP GL Code is not assigned to this company!', 500);
                        }
                    }
                }
            }
        }


        if ($input['interCompanyTransferYN']) {

            $checkCustomer = CustomerMaster::where('companyLinkedToSystemID', $input['companyToSystemID'])->count();
            if ($checkCustomer == 0) {
                $this->stockTransferRepository->update(['companyToSystemID' => null,'companyTo' => null,'locationTo' => null,'interCompanyTransferYN' => 0], $id);
                return $this->sendError('Customer is not linked to the selected company. Please create a customer and link to the company.', 500,$companyToError);
            }

            $checkSupplier = SupplierMaster::where('companyLinkedToSystemID', $input['companyFromSystemID'])->count();
            if ($checkSupplier == 0) {
                $this->stockTransferRepository->update(['companyToSystemID' => null,'companyTo' => null,'locationTo' => null,'interCompanyTransferYN' => 0], $id);
                return $this->sendError('Supplier is not linked to the selected company. Please create a supplier and link to the company.', 500,$companyToError);
            }

            $toCompanyFinancePeriod = CompanyFinancePeriod::where('companySystemID', $input['companyToSystemID'])
                                                            ->where('departmentSystemID', 10)
                                                            ->where('isActive', -1)
                                                            ->where('dateFrom', '<', $input['tranferDate'])
                                                            ->where('dateTo', '>', $input['tranferDate'])
                                                            ->where('isCurrent', -1)
                                                            ->count();

            $companyTo = Company::where('companySystemID', $input['companyToSystemID'])->first();

            if ($toCompanyFinancePeriod == 0) {
                $this->stockTransferRepository->update(['companyToSystemID' => null,'companyTo' => null,'locationTo' => null,'interCompanyTransferYN' => 0], $id);
                return $this->sendError('Financial year and period is not activated in ' . $companyTo->CompanyName, 500,$companyToError);
            }
        }


        if ($input['locationFrom'] == $input['locationTo']) {
            $this->stockTransferRepository->update(['locationTo' => null], $id);
            return $this->sendError('Location From and Location To  cannot be same', 500,$wareHouseToError);
        }


        $companyTo = Company::where('companySystemID', $input['companyToSystemID'])->first();
        if ($companyTo) {
            $input['companyTo'] = $companyTo->CompanyID;
        }

        if ($input['interCompanyTransferYN']) {
            $input['interCompanyTransferYN'] = -1;
        } else {
            $input['interCompanyTransferYN'] = 0;
        }

        if ($stockTransfer->confirmedYN == 0 && $input['confirmedYN'] == 1) {

            $companyFinanceYear = \Helper::companyFinanceYearCheck($input);
            if (!$companyFinanceYear["success"]) {
                return $this->sendError($companyFinanceYear["message"], 500);
            }

            $trackingValidation = ItemTracking::validateTrackingOnDocumentConfirmation($stockTransfer->documentSystemID, $stockTransfer->stockTransferAutoID);

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

            $documentDate = $input['tranferDate'];
            $monthBegin = $input['FYBiggin'];
            $monthEnd = $input['FYEnd'];

            if (($documentDate >= $monthBegin) && ($documentDate <= $monthEnd)) {
            } else {
                return $this->sendError('Transfer date is not within the selected financial period !', 500);
            }

            $validator = \Validator::make($input, [
                'locationFrom' => 'required|numeric|min:1',
                'locationTo' => 'required|numeric|min:1',
                'companyFinancePeriodID' => 'required|numeric|min:1',
                'companyFinanceYearID' => 'required|numeric|min:1',
                'tranferDate' => 'required|date|before_or_equal:today',
                'companyToSystemID' => 'required|numeric|min:1',
                'companyFromSystemID' => 'required|numeric|min:1',
                'serviceLineSystemID' => 'required|numeric|min:1',
                'refNo' => 'required',
                'comment' => 'required'
            ]);

            if ($validator->fails()) {
                return $this->sendError($validator->messages(), 422);
            }

            $stockTransDetailExist = StockTransferDetails::select(DB::raw('stockTransferDetailsID'))
                                                        ->where('stockTransferAutoID', $input['stockTransferAutoID'])
                                                        ->first();

            if (empty($stockTransDetailExist)) {
                return $this->sendError('Stock Transfer document cannot confirm without details',500);
            }

            $checkQuantity = StockTransferDetails::where('stockTransferAutoID', $id)
                                                ->where('qty', '<=', 0)
                                                ->count();

            if ($checkQuantity > 0) {
                return $this->sendError('Every item should have at least one minimum Qty', 500);
            }

            unset($input['confirmedYN']);
            unset($input['confirmedByEmpSystemID']);
            unset($input['confirmedByEmpID']);
            unset($input['confirmedByName']);
            unset($input['confirmedDate']);

            $itemTransferDetails = StockTransferDetails::where('stockTransferAutoID', $id)->get();

            $finalError = array('cost_zero' => array(),
                'cost_neg' => array(),
                'currentStockQty_zero' => array(),
                'currentWareHouseStockQty_zero' => array(),
                'currentStockQty_more' => array(),
                'currentWareHouseStockQty_more' => array());
            $error_count = 0;
            
            foreach ($itemTransferDetails as $item) {
                $updateItem = StockTransferDetails::find($item['stockTransferDetailsID']);
                $data = array('companySystemID' => $stockTransfer->companySystemID,
                               'itemCodeSystem' => $updateItem->itemCodeSystem,
                               'wareHouseId' => $stockTransfer->locationFrom);
                $itemCurrentCostAndQty = \Inventory::itemCurrentCostAndQty($data);
                $updateItem->currentStockQty = $itemCurrentCostAndQty['currentStockQty'];
                $updateItem->warehouseStockQty = $itemCurrentCostAndQty['currentWareHouseStockQty'];
                // $updateItem->unitCostLocal = $itemCurrentCostAndQty['wacValueLocal'];
                // $updateItem->unitCostRpt = $itemCurrentCostAndQty['wacValueReporting'];
                $updateItem->save();

                if ($updateItem->unitCostLocal == 0 || $updateItem->unitCostRpt == 0) {
                    array_push($finalError['cost_zero'], $updateItem->itemPrimaryCode);
                    $error_count++;
                }
                if ($updateItem->unitCostLocal < 0 || $updateItem->unitCostRpt < 0) {
                    array_push($finalError['cost_neg'], $updateItem->itemPrimaryCode);
                    $error_count++;
                }
                if ($updateItem->currentStockQty <= 0) {
                    array_push($finalError['currentStockQty_zero'], $updateItem->itemPrimaryCode);
                    $error_count++;
                }
                if ($updateItem->warehouseStockQty <= 0) {
                    array_push($finalError['currentWareHouseStockQty_zero'], $updateItem->itemPrimaryCode);
                    $error_count++;
                }
                if ($updateItem->qty > $updateItem->currentStockQty) {
                    array_push($finalError['currentStockQty_more'], $updateItem->itemPrimaryCode);
                    $error_count++;
                }

                if ($updateItem->qty > $updateItem->warehouseStockQty) {
                    array_push($finalError['currentWareHouseStockQty_more'], $updateItem->itemPrimaryCode);
                    $error_count++;
                }
            }

            $confirm_error = array('type' => 'confirm_error', 'data' => $finalError);
            if ($error_count > 0) {
                return $this->sendError("You cannot confirm this document.", 500, $confirm_error);
            }

            $checkPlAccount = ($stockTransfer->interCompanyTransferYN == -1) ? SystemGlCodeScenarioDetail::getGlByScenario($stockTransfer->companySystemID, $stockTransfer->documentSystemID, "stock-transfer-pl-account-for-inter-company-transfer") : SystemGlCodeScenarioDetail::getGlByScenario($stockTransfer->companySystemID, $stockTransfer->documentSystemID, "stock-transfer-pl-account");

            if (is_null($checkPlAccount)) {
                return $this->sendError('Transit account for stock transfer is not configured. Please update it in Chart of Account → Chart of Account Configuration', 500);
            }

            if ($stockTransfer->interCompanyTransferYN == -1) {
                $checkRevenueAc = SystemGlCodeScenarioDetail::getGlByScenario($stockTransfer->companySystemID, $stockTransfer->documentSystemID, "inter-company-transfer-revenue");
                
                if (is_null($checkRevenueAc)) {
                    return $this->sendError('Please configure Inter Company stock transfer revenue account', 500);
                }
            }


            $params = array('autoID' => $id, 'company' => $input["companySystemID"], 'document' => $input["documentSystemID"], 'segment' => $input["serviceLineSystemID"], 'category' => '', 'amount' => 0);
            $confirm = \Helper::confirmDocument($params);
            if (!$confirm["success"]) {
                return $this->sendError($confirm["message"]);
            }
        }
        $employee = \Helper::getEmployeeInfo();
        $input['modifiedPc'] = gethostname();
        $input['modifiedUser'] = $employee->empID;
        $input['modifiedUserSystemID'] = $employee->employeeSystemID;


        $stockTransfer = $this->stockTransferRepository->update($input, $id);

        return $this->sendResponse($stockTransfer->toArray(), 'StockTransfer updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/stockTransfers/{id}",
     *      summary="Remove the specified StockTransfer from storage",
     *      tags={"StockTransfer"},
     *      description="Delete StockTransfer",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of StockTransfer",
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
        /** @var StockTransfer $stockTransfer */
        $stockTransfer = $this->stockTransferRepository->findWithoutFail($id);

        if (empty($stockTransfer)) {
            return $this->sendError('Stock Transfer not found');
        }

        $stockTransfer->delete();

        return $this->sendResponse($id, 'Stock Transfer deleted successfully');
    }

    public function getStockTransferMasterView(Request $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input, array('serviceLineSystemID', 'locationFrom', 'confirmedYN', 'approved', 'month', 'year', 'interCompanyTransferYN'));
        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $search = $request->input('search.value');

        $grvLocation = $request['locationFrom'];
        $grvLocation = (array)$grvLocation;
        $grvLocation = collect($grvLocation)->pluck('id');

        $serviceLineSystemID = $request['serviceLineSystemID'];
        $serviceLineSystemID = (array)$serviceLineSystemID;
        $serviceLineSystemID = collect($serviceLineSystemID)->pluck('id');

        $stockTransferMaster = $this->stockTransferRepository->stockTransferListQuery($request, $input, $search, $grvLocation, $serviceLineSystemID);

        $policy = 0;

        return \DataTables::eloquent($stockTransferMaster)
            ->addColumn('Actions', $policy)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('stockTransferAutoID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    public function getStockTransferFormData(Request $request)
    {
        $companyId = $request['companyId'];

        $segments = SegmentMaster::where("companySystemID", $companyId);
        if (isset($request['type']) && $request['type'] != 'filter') {
            $segments = $segments->where('isActive', 1);
        }
        $segments = $segments->get();

        /** Yes and No Selection */
        $yesNoSelection = YesNoSelection::all();

        /** all Units*/
        $yesNoSelectionForMinus = YesNoSelectionForMinus::all();

        $month = Months::all();

        $years = StockTransfer::select(DB::raw("YEAR(createdDateTime) as year"))
            ->whereNotNull('createdDateTime')
            ->groupby('year')
            ->orderby('year', 'desc')
            ->get();

        $wareHouseLocation = WarehouseMaster::where("companySystemID", $companyId);
        if (isset($request['type']) && $request['type'] != 'filter') {
            $wareHouseLocation = $wareHouseLocation->where('isActive', 1);
        }
        $wareHouseLocation = $wareHouseLocation->get();


        $financialYears = array(array('value' => intval(date("Y")), 'label' => date("Y")),
            array('value' => intval(date("Y", strtotime("-1 year"))), 'label' => date("Y", strtotime("-1 year"))));

        $companyFinanceYear = \Helper::companyFinanceYear($companyId);

        $companies = \Helper::allCompanies();

        $output = array('segments' => $segments,
            'yesNoSelection' => $yesNoSelection,
            'yesNoSelectionForMinus' => $yesNoSelectionForMinus,
            'month' => $month,
            'years' => $years,
            'wareHouseLocation' => $wareHouseLocation,
            'financialYears' => $financialYears,
            'companyFinanceYear' => $companyFinanceYear,
            'companies' => $companies
        );

        return $this->sendResponse($output, 'Record retrieved successfully');
    }

    public function getItemsOptionForStockTransfer(Request $request)
    {
        $input = $request->all();

        $companyId = $input['companyId'];

        $items = ItemAssigned::where('companySystemID', $companyId)->where('isActive', 1)->where('isAssigned', -1);
        $items = $items->where('financeCategoryMaster', 1);

        if (array_key_exists('search', $input)) {

            $search = $input['search'];

            $items = $items->where(function ($query) use ($search) {
                $query->where('itemPrimaryCode', 'LIKE', "%{$search}%")
                    ->orWhere('itemDescription', 'LIKE', "%{$search}%")
                    ->orWhere('secondaryItemCode', 'LIKE', "%{$search}%");
            });
        }

        $items = $items
            ->take(20)
            ->get(['itemPrimaryCode', 'itemDescription', 'itemCodeSystem', 'secondaryItemCode']);

        return $this->sendResponse($items->toArray(), 'Data retrieved successfully');
    }

    public function StockTransferAudit(Request $request)
    {
        $id = $request->get('id');

        $stockTransfer = $this->stockTransferRepository->getAudit($id);

        if (empty($stockTransfer)) {
            return $this->sendError('Stock Transfer not found');
        }

        $stockTransfer->docRefNo = \Helper::getCompanyDocRefNo($stockTransfer->companySystemID, $stockTransfer->documentSystemID);

        return $this->sendResponse($stockTransfer->toArray(), 'Stock Transfer retrieved successfully');
    }

    public function printStockTransfer(Request $request)
    {
        $id = $request->get('id');
        $stockTransfer = $this->stockTransferRepository->getAudit($id);

        if (empty($stockTransfer)) {
            return $this->sendError('Stock Transfer not found');
        }

        $stockTransfer->docRefNo = \Helper::getCompanyDocRefNo($stockTransfer->companySystemID, $stockTransfer->documentSystemID);

        $array = array('entity' => $stockTransfer);
        $time = strtotime("now");
        $fileName = 'stock_transfer_' . $id . '_' . $time . '.pdf';
        $html = view('print.stock_transfer', $array);
        $htmlFooter = view('print.stock_transfer_footer', $array);
        $mpdf = new \Mpdf\Mpdf(['tempDir' => public_path('tmp'), 'mode' => 'utf-8', 'format' => 'A4-L', 'setAutoTopMargin' => 'stretch', 'autoMarginPadding' => -10]);
        $mpdf->AddPage('L');
        $mpdf->setAutoBottomMargin = 'stretch';
        $mpdf->SetHTMLFooter($htmlFooter);
        $mpdf->WriteHTML($html);
        return $mpdf->Output($fileName, 'I');
    }


    public function getStockTransferApproval(Request $request)
    {
        $input = $request->all();

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $companyID = $request->companyId;
        $empID = \Helper::getEmployeeSystemID();

        $serviceLinePolicy = CompanyDocumentAttachment::where('companySystemID', $companyID)
            ->where('documentSystemID', 13)
            ->first();

        $stockTransferMasters = DB::table('erp_documentapproved')->select(
            'employeesdepartments.approvalDeligated',
            'erp_stocktransfer.stockTransferAutoID',
            'erp_stocktransfer.stockTransferCode',
            'erp_stocktransfer.documentSystemID',
            'erp_stocktransfer.refNo',
            'erp_stocktransfer.tranferDate',
            'erp_stocktransfer.comment',
            'erp_stocktransfer.serviceLineCode',
            'erp_stocktransfer.createdDateTime',
            'erp_stocktransfer.confirmedDate',
            'erp_documentapproved.documentApprovedID',
            'erp_documentapproved.rollLevelOrder',
            'approvalLevelID',
            'documentSystemCode',
            'employees.empName As created_user',
            'serviceline.ServiceLineDes as serviceLineDescription'
        )->join('employeesdepartments', function ($query) use ($companyID, $empID, $serviceLinePolicy) {
            $query->on('erp_documentapproved.approvalGroupID', '=', 'employeesdepartments.employeeGroupID')
                ->on('erp_documentapproved.documentSystemID', '=', 'employeesdepartments.documentSystemID')
                ->on('erp_documentapproved.companySystemID', '=', 'employeesdepartments.companySystemID');
            if ($serviceLinePolicy && $serviceLinePolicy->isServiceLineApproval == -1) {
                $query->on('erp_documentapproved.serviceLineSystemID', '=', 'employeesdepartments.ServiceLineSystemID');
            }
            $query->where('employeesdepartments.documentSystemID', 13)
                ->where('employeesdepartments.companySystemID', $companyID)
                ->where('employeesdepartments.employeeSystemID', $empID)
                ->where('employeesdepartments.isActive', 1)
                ->where('employeesdepartments.removedYN', 0);
        })->join('erp_stocktransfer', function ($query) use ($companyID, $empID) {
            $query->on('erp_documentapproved.documentSystemCode', '=', 'stockTransferAutoID')
                ->on('erp_documentapproved.rollLevelOrder', '=', 'RollLevForApp_curr')
                ->where('erp_stocktransfer.companySystemID', $companyID)
                ->where('erp_stocktransfer.approved', 0)
                ->where('erp_stocktransfer.confirmedYN', 1);
        })->where('erp_documentapproved.approvedYN', 0)
            ->join('employees', 'createdUserSystemID', 'employees.employeeSystemID')
            ->join('serviceline', 'erp_stocktransfer.serviceLineSystemID', 'serviceline.serviceLineSystemID')
            ->where('erp_documentapproved.rejectedYN', 0)
            ->where('erp_documentapproved.documentSystemID', 13)
            ->where('erp_documentapproved.companySystemID', $companyID);

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $stockTransferMasters = $stockTransferMasters->where(function ($query) use ($search) {
                $query->where('stockTransferCode', 'LIKE', "%{$search}%")
                    ->orWhere('comment', 'LIKE', "%{$search}%");
            });
        }

        $isEmployeeDischarched = \Helper::checkEmployeeDischarchedYN();

        if ($isEmployeeDischarched == 'true') {
            $stockTransferMasters = [];
        }

        return \DataTables::of($stockTransferMasters)
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
            //->addColumn('Index', 'Index', "Index")
            ->make(true);
    }

    public function getApprovedSTForCurrentUser(Request $request)
    {
        $input = $request->all();

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $companyID = $request->companyId;
        $empID = \Helper::getEmployeeSystemID();

        $stockTransferMasters = DB::table('erp_documentapproved')->select(
            'erp_stocktransfer.stockTransferAutoID',
            'erp_stocktransfer.stockTransferCode',
            'erp_stocktransfer.documentSystemID',
            'erp_stocktransfer.refNo',
            'erp_stocktransfer.postedDate',
            'erp_stocktransfer.tranferDate',
            'erp_stocktransfer.comment',
            'erp_stocktransfer.serviceLineCode',
            'erp_stocktransfer.createdDateTime',
            'erp_stocktransfer.confirmedDate',
            'erp_documentapproved.documentApprovedID',
            'erp_documentapproved.rollLevelOrder',
            'approvalLevelID',
            'documentSystemCode',
            'employees.empName As created_user',
            'serviceline.ServiceLineDes as serviceLineDescription'
        )->join('erp_stocktransfer', function ($query) use ($companyID, $empID) {
            $query->on('erp_documentapproved.documentSystemCode', '=', 'stockTransferAutoID')
                ->where('erp_stocktransfer.companySystemID', $companyID)
                ->where('erp_stocktransfer.approved', -1)
                ->where('erp_stocktransfer.confirmedYN', 1);
        })->where('erp_documentapproved.approvedYN', -1)
            ->join('employees', 'createdUserSystemID', 'employees.employeeSystemID')
            ->join('serviceline', 'erp_stocktransfer.serviceLineSystemID', 'serviceline.serviceLineSystemID')
            ->where('erp_documentapproved.documentSystemID', 13)
            ->where('erp_documentapproved.companySystemID', $companyID)->where('erp_documentapproved.employeeSystemID', $empID);

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $stockTransferMasters = $stockTransferMasters->where(function ($query) use ($search) {
                $query->where('stockTransferCode', 'LIKE', "%{$search}%")
                    ->orWhere('comment', 'LIKE', "%{$search}%");
            });
        }

        return \DataTables::of($stockTransferMasters)
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
            //->addColumn('Index', 'Index', "Index")
            ->make(true);
    }

    public function approveStockTransfer(Request $request)
    {
        $approve = \Helper::approveDocument($request);
        if (!$approve["success"]) {
            return $this->sendError($approve["message"]);
        } else {
            return $this->sendResponse(array(), $approve["message"]);
        }

    }

    public function rejectStockTransfer(Request $request)
    {
        $reject = \Helper::rejectDocument($request);
        if (!$reject["success"]) {
            return $this->sendError($reject["message"]);
        } else {
            return $this->sendResponse(array(), $reject["message"]);
        }

    }

    public function getStockTransferForReceive(Request $request)
    {

        $input = $request->all();
        $id = $input['stockReceiveAutoID'];
        $stockReceive = StockReceive::find($id);
        if (empty($stockReceive)) {
            return $this->sendError('Stock Receive not found');
        }

        $stockTransfers = StockTransfer::where('companyToSystemID', $stockReceive->companyToSystemID)
            ->where('companyFromSystemID', $stockReceive->companyFromSystemID)
            ->where('locationTo', $stockReceive->locationTo)
            ->where('locationFrom', $stockReceive->locationFrom)
            ->where('approved', -1)
            ->where('fullyReceived', 0)
            ->where('interCompanyTransferYN', 0)
            ->orderby('createdDateTime', 'desc')
            ->get();

        return $this->sendResponse($stockTransfers->toArray(), 'Stock Transfer retrieved successfully');
    }

    public function getStockTransferDetailsByMaster(Request $request)
    {

        $input = $request->all();
        $id = $input['stockTransferAutoID'];
        $stockTransfer = StockTransfer::find($id);
        if (empty($stockTransfer)) {
            return $this->sendError('Stock Transfer not found');
        }
        $stockTransferDetails = StockTransferDetails::where('stockTransferAutoID', $id)->with(['unit_by'])
            ->where('stockRecieved', 0)
            ->get();

        return $this->sendResponse($stockTransferDetails->toArray(), 'Stock Transfer retrieved successfully');
    }


    public function stockTransferReopen(Request $request)
    {
        $input = $request->all();

        $id = $input['stockTransferAutoID'];
        $stockTransfer = $this->stockTransferRepository->findWithoutFail($id);
        $emails = array();
        if (empty($stockTransfer)) {
            return $this->sendError('Stock Transfer not found');
        }

        if ($stockTransfer->approved == -1) {
            return $this->sendError('You cannot reopen this Stock Transfer it is already fully approved');
        }

        if ($stockTransfer->RollLevForApp_curr > 1) {
            return $this->sendError('You cannot reopen this Stock Transfer it is already partially approved');
        }

        if ($stockTransfer->confirmedYN == 0) {
            return $this->sendError('You cannot reopen this Stock Transfer, it is not confirmed');
        }

        $updateInput = ['confirmedYN' => 0,'confirmedByEmpSystemID' => null,'confirmedByEmpID' => null,
            'confirmedByName' => null, 'confirmedDate' => null,'RollLevForApp_curr' => 1];

        $this->stockTransferRepository->update($updateInput,$id);

        $employee = \Helper::getEmployeeInfo();

        $document = DocumentMaster::where('documentSystemID', $stockTransfer->documentSystemID)->first();

        $cancelDocNameBody = $document->documentDescription . ' <b>' . $stockTransfer->stockTransferCode . '</b>';
        $cancelDocNameSubject = $document->documentDescription . ' ' . $stockTransfer->stockTransferCode;

        $subject = $cancelDocNameSubject . ' is reopened';

        $body = '<p>' . $cancelDocNameBody . ' is reopened by ' . $employee->empID . ' - ' . $employee->empFullName . '</p><p>Comment : ' . $input['reopenComments'] . '</p>';

        $documentApproval = DocumentApproved::where('companySystemID', $stockTransfer->companySystemID)
            ->where('documentSystemCode', $stockTransfer->stockTransferAutoID)
            ->where('documentSystemID', $stockTransfer->documentSystemID)
            ->where('rollLevelOrder', 1)
            ->first();

        if ($documentApproval) {
            if ($documentApproval->approvedYN == 0) {
                $companyDocument = CompanyDocumentAttachment::where('companySystemID', $stockTransfer->companySystemID)
                    ->where('documentSystemID', $stockTransfer->documentSystemID)
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
            ->where('companySystemID', $stockTransfer->companySystemID)
            ->where('documentSystemID', $stockTransfer->documentSystemID)
            ->delete();

        /*Audit entry*/
        AuditTrial::createAuditTrial($stockTransfer->documentSystemID,$id,$input['reopenComments'],'Reopened');

        return $this->sendResponse($stockTransfer->toArray(), 'Stock Transfer reopened successfully');
    }

    public function stockTransferReferBack(Request $request)
    {
        $input = $request->all();

        $id = $input['id'];

        $stockTransfer = $this->stockTransferRepository->find($id);
        if (empty($stockTransfer)) {
            return $this->sendError('Stock Transfer not found');
        }

        if ($stockTransfer->refferedBackYN != -1) {
            return $this->sendError('You cannot refer back this stock transfer');
        }

        $stockTransferArray = $stockTransfer->toArray();

        $storeSTHistory = StockTransferRefferedBack::insert($stockTransferArray);

        $fetchDetails = StockTransferDetails::where('stockTransferAutoID', $id)
                                                            ->get();

        if (!empty($fetchDetails)) {
            foreach ($fetchDetails as $detail) {
                $detail['timesReferred'] = $stockTransfer->timesReferred;
            }
        }

        $stockTransferDetailArray = $fetchDetails->toArray();

        $storePRDetailHistory = StockTransferDetailsRefferedBack::insert($stockTransferDetailArray);

        $fetchDocumentApproved = DocumentApproved::where('documentSystemCode', $id)
            ->where('companySystemID', $stockTransfer->companySystemID)
            ->where('documentSystemID', $stockTransfer->documentSystemID)
            ->get();

        if (!empty($fetchDocumentApproved)) {
            foreach ($fetchDocumentApproved as $DocumentApproved) {
                $DocumentApproved['refTimes'] = $stockTransfer->timesReferred;
            }
        }

        $DocumentApprovedArray = $fetchDocumentApproved->toArray();

        $storeDocumentRefereedHistory = DocumentReferedHistory::insert($DocumentApprovedArray);

        $deleteApproval = DocumentApproved::where('documentSystemCode', $id)
            ->where('companySystemID', $stockTransfer->companySystemID)
            ->where('documentSystemID', $stockTransfer->documentSystemID)
            ->delete();

        if ($deleteApproval) {

            $updateArray = ['refferedBackYN' => 0,'confirmedYN' => 0,'confirmedByEmpSystemID' => null,
                            'confirmedByEmpID' => null,'confirmedByName' => null,'confirmedDate' => null,'RollLevForApp_curr' => 1];

            $this->stockTransferRepository->update($updateArray,$id);
        }

        return $this->sendResponse($stockTransfer->toArray(), 'Stock Transfer Amend successfully');
    }

    public function getallUomConvertion(Request $request)  {
        $input = $request->all();
        $id = $input['itemCode'];
        $iemUnit = ItemMaster::where('itemCodeSystem',$id)->select('unit')->first();
        $convertionUnit = UnitConversion::where('masterUnitID',$iemUnit->unit)->pluck('subUnitID')->toArray();

        $defaulUnit = [$iemUnit->unit];
        $mergedArray = isset($convertionUnit)?array_merge($convertionUnit, $defaulUnit):$defaulUnit;
        $uniqueArray = array_unique($mergedArray);
        $units = Unit::whereIn('UnitID',$uniqueArray)->select('UnitID as unitOfMeasure','UnitShortCode')->get();
        return $this->sendResponse($units, 'COnvertion unit retrived successfully');

    }

}
