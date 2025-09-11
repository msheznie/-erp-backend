<?php
/**
 * =============================================
 * -- File Name : PurchaseReturnAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Purchase Return
 * -- Author : Mohamed Fayas
 * -- Create date : 31 - July 2018
 * -- Description : This file contains the all CRUD for Purchase Return
 * -- REVISION HISTORY
 * -- Date: 10 - August 2018 By: Fayas Description: Added new functions named as getPurchaseReturnByCompany(),getPurchaseReturnFormData()
 * -- Date: 10 - August 2018 By: Fayas Description: Added new functions named as purchaseReturnSegmentChkActive(),grvForPurchaseReturn()
 * -- Date: 17 - August 2018 By: Fayas Description: Added new functions named as getPurchaseReturnAudit(),getPurchaseReturnApprovedByUser(),
 *                          getPurchaseReturnApprovalByUser()
 * Date: 20 - August 2018 By: Fayas Description: Added new functions named as printPurchaseReturn()
 * Date: 28 - August 2018 By: Fayas Description: Added new functions named as purchaseReturnReopen()
 *
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreatePurchaseReturnAPIRequest;
use App\Http\Requests\API\UpdatePurchaseReturnAPIRequest;
use App\Models\PurchaseReturnMasterRefferedBack;
use App\Models\PurchaseReturnDetailsRefferedBack;
use App\Models\DocumentReferedHistory;
use App\Models\Company;
use App\Models\CompanyDocumentAttachment;
use App\Models\CompanyFinancePeriod;
use App\Models\CompanyFinanceYear;
use App\Models\CompanyPolicyMaster;
use App\Models\CurrencyMaster;
use App\Models\DocumentApproved;
use App\Models\DocumentMaster;
use App\Models\EmployeesDepartment;
use App\Models\GRVDetails;
use App\Models\GRVMaster;
use App\Models\GRVTypes;
use App\Models\ItemIssueMaster;
use App\Models\Location;
use App\Models\Months;
use App\Models\BookInvSuppDet;
use App\Models\PurchaseReturn;
use App\Models\PurchaseReturnDetails;
use App\Models\SegmentMaster;
use App\Models\SupplierAssigned;
use App\Models\SupplierCurrency;
use App\Models\SupplierMaster;
use App\Models\WarehouseMaster;
use App\Models\Year;
use App\Models\YesNoSelection;
use App\Models\YesNoSelectionForMinus;
use App\Repositories\PurchaseReturnRepository;
use App\Traits\AuditTrial;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\DB;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use App\helper\TaxService;
use App\helper\ItemTracking;

/**
 * Class PurchaseReturnController
 * @package App\Http\Controllers\API
 */
class PurchaseReturnAPIController extends AppBaseController
{
    /** @var  PurchaseReturnRepository */
    private $purchaseReturnRepository;
    private $purchaseReturnDetailsRepository;

    public function __construct(PurchaseReturnRepository $purchaseReturnRepo, PurchaseReturnDetails $purchaseReturnDetailsRepo)
    {
        $this->purchaseReturnRepository = $purchaseReturnRepo;
        $this->purchaseReturnDetailsRepository = $purchaseReturnDetailsRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/purchaseReturns",
     *      summary="Get a listing of the PurchaseReturns.",
     *      tags={"PurchaseReturn"},
     *      description="Get all PurchaseReturns",
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
     *                  @SWG\Items(ref="#/definitions/PurchaseReturn")
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
        $this->purchaseReturnRepository->pushCriteria(new RequestCriteria($request));
        $this->purchaseReturnRepository->pushCriteria(new LimitOffsetCriteria($request));
        $purchaseReturns = $this->purchaseReturnRepository->all();

        return $this->sendResponse($purchaseReturns->toArray(), trans('custom.purchase_returns_retrieved_successfully'));
    }

    /**
     * @param CreatePurchaseReturnAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/purchaseReturns",
     *      summary="Store a newly created PurchaseReturn in storage",
     *      tags={"PurchaseReturn"},
     *      description="Store PurchaseReturn",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="PurchaseReturn that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/PurchaseReturn")
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
     *                  ref="#/definitions/PurchaseReturn"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreatePurchaseReturnAPIRequest $request)
    {
        /*
        'totalSupplierDefaultAmount',
        'totalSupplierTransactionAmount',
        'totalLocalAmount',
        'totalComRptAmount',
         */

        $input = $request->all();
        $input = $this->convertArrayToValue($input);
        $employee = \Helper::getEmployeeInfo();

        $input['createdPcID'] = gethostname();
        $input['createdUserID'] = $employee->empID;
        $input['createdUserSystemID'] = $employee->employeeSystemID;
        $input['documentSystemID'] = 24;
        $input['documentID'] = 'PRN';

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
            'purchaseReturnLocation' => 'required|numeric|min:1',
            'companyFinancePeriodID' => 'required|numeric|min:1',
            'companyFinanceYearID' => 'required|numeric|min:1',
            'purchaseReturnDate' => 'required|date|before_or_equal:today',
            'purchaseReturnRefNo' => 'required',
            'narration' => 'required',
            'serviceLineSystemID' => 'required|numeric|min:1',
            'supplierID' => 'required|numeric|min:1',
            'supplierTransactionCurrencyID' => 'required|numeric|min:1'
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        if (isset($input['purchaseReturnDate'])) {
            if ($input['purchaseReturnDate']) {
                $input['purchaseReturnDate'] = new Carbon($input['purchaseReturnDate']);
            }
        }

        $documentDate = $input['purchaseReturnDate'];
        $monthBegin = $input['FYBiggin'];
        $monthEnd = $input['FYEnd'];

        if (($documentDate >= $monthBegin) && ($documentDate <= $monthEnd)) {
        } else {
            return $this->sendError(trans('custom.purchase_return_date_not_between_financial_period'), 500);
        }

        $segment = SegmentMaster::where('serviceLineSystemID', $input['serviceLineSystemID'])->first();
        if ($segment) {
            $input['serviceLineCode'] = $segment->ServiceLineCode;
        }

        $companyCurrencyConversion = \Helper::currencyConversion($input['companySystemID'], $input['supplierTransactionCurrencyID'], $input['supplierTransactionCurrencyID'], 0);

        //var_dump($companyCurrencyConversion);
        $company = Company::where('companySystemID', $input['companySystemID'])->first();
        if ($company) {
            $input['companyID'] = $company->CompanyID;
            $input['localCurrencyID'] = $company->localCurrencyID;
            $input['companyReportingCurrencyID'] = $company->reportingCurrency;
            $input['companyReportingER'] = $companyCurrencyConversion['trasToRptER'];
            $input['localCurrencyER'] = $companyCurrencyConversion['trasToLocER'];
        }

        DB::beginTransaction();
        $lastSerial = PurchaseReturn::where('companySystemID', $input['companySystemID'])
            ->where('companyFinanceYearID', $input['companyFinanceYearID'])
            ->orderBy('serialNo', 'desc')
            ->lockForUpdate()
            ->first();

        $lastSerialNumber = 1;
        if ($lastSerial) {
            $lastSerialNumber = intval($lastSerial->serialNo) + 1;
        }


        $input['serialNo'] = $lastSerialNumber;
        $input['supplierTransactionER'] = 1;

        $supplier = SupplierMaster::where('supplierCodeSystem', $input['supplierID'])->first();
        if ($supplier) {
            $input['supplierPrimaryCode'] = $supplier->primarySupplierCode;
            $input['supplierName'] = $supplier->supplierName;
            $input['liabilityAccountSysemID'] = $supplier->liabilityAccountSysemID;
            $input['liabilityAccount'] = $supplier->liabilityAccount;
            $input['UnbilledGRVAccountSystemID'] = $supplier->UnbilledGRVAccountSystemID;
            $input['UnbilledGRVAccount'] = $supplier->UnbilledGRVAccount;
        }

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
            $grvCode = ($company->CompanyID . '\\' . $finYear . '\\' . $documentMaster['documentID'] . str_pad($lastSerialNumber, 6, '0', STR_PAD_LEFT));
            $input['purchaseReturnCode'] = $grvCode;
        }

        $supplierCurrency = SupplierCurrency::where('supplierCodeSystem', $input['supplierID'])
            ->where('isDefault', -1)
            ->first();

        if ($supplierCurrency) {

            $erCurrency = CurrencyMaster::where('currencyID', $supplierCurrency->currencyID)->first();

            $input['supplierDefaultCurrencyID'] = $supplierCurrency->currencyID;

            if ($erCurrency) {
                $input['supplierDefaultER'] = $erCurrency->ExchangeRate;
            }
        }

        $purchaseReturns = $this->purchaseReturnRepository->create($input);
        DB::commit();
        return $this->sendResponse($purchaseReturns->toArray(), trans('custom.purchase_return_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/purchaseReturns/{id}",
     *      summary="Display the specified PurchaseReturn",
     *      tags={"PurchaseReturn"},
     *      description="Get PurchaseReturn",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PurchaseReturn",
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
     *                  ref="#/definitions/PurchaseReturn"
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
        /** @var PurchaseReturn $purchaseReturn */
        $purchaseReturn = $this->purchaseReturnRepository->with(['confirmed_by', 'segment_by', 'location_by', 'finance_period_by' => function ($query) {
            $query->selectRaw("CONCAT(DATE_FORMAT(dateFrom,'%d/%m/%Y'),' | ',DATE_FORMAT(dateTo,'%d/%m/%Y')) as financePeriod,companyFinancePeriodID");
        }, 'finance_year_by' => function ($query) {
            $query->selectRaw("CONCAT(DATE_FORMAT(bigginingDate,'%d/%m/%Y'),' | ',DATE_FORMAT(endingDate,'%d/%m/%Y')) as financeYear,companyFinanceYearID");
        },'supplier_by','currency_by'])->findWithoutFail($id);

        if (empty($purchaseReturn)) {
            return $this->sendError(trans('custom.purchase_return_not_found'));
        }

        return $this->sendResponse($purchaseReturn->toArray(), trans('custom.purchase_return_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdatePurchaseReturnAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/purchaseReturns/{id}",
     *      summary="Update the specified PurchaseReturn in storage",
     *      tags={"PurchaseReturn"},
     *      description="Update PurchaseReturn",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PurchaseReturn",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="PurchaseReturn that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/PurchaseReturn")
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
     *                  ref="#/definitions/PurchaseReturn"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdatePurchaseReturnAPIRequest $request)
    {
        $input = $request->all();
        $input = array_except($input, ['confirmed_by', 'segment_by', 'location_by', 'finance_period_by', 'finance_year_by',
            'confirmedByEmpSystemID', 'confirmedByEmpID', 'confirmedDate', 'confirmedByName','supplier_by','currency_by']);
        $wareHouseError = array('type' => 'wareHouse');
        $serviceLineError = array('type' => 'serviceLine');

        $input = $this->convertArrayToValue($input);
        /** @var PurchaseReturn $purchaseReturn */
        $purchaseReturn = $this->purchaseReturnRepository->findWithoutFail($id);

        if (empty($purchaseReturn)) {
            return $this->sendError(trans('custom.purchase_return_not_found'));
        }

        if (isset($input['serviceLineSystemID'])) {
            $checkDepartmentActive = SegmentMaster::find($input['serviceLineSystemID']);
            if (empty($checkDepartmentActive)) {
                return $this->sendError(trans('custom.segment_not_found'));
            }

            if ($checkDepartmentActive->isActive == 0) {
                $this->purchaseReturnRepository->update(['serviceLineSystemID' => null, 'serviceLineCode' => null], $id);
                return $this->sendError(trans('custom.please_select_active_segment_return'), 500, $serviceLineError);
            }

            $input['serviceLineCode'] = $checkDepartmentActive->ServiceLineCode;
        }

        if (isset($input['purchaseReturnLocation'])) {
            $checkWareHouseActive = WarehouseMaster::find($input['purchaseReturnLocation']);
            if (empty($checkWareHouseActive)) {
                return $this->sendError(trans('custom.location_not_found'), 500, $wareHouseError);
            }

            if ($checkWareHouseActive->isActive == 0) {
                $this->purchaseReturnRepository->update(['purchaseReturnLocation' => null], $id);
                return $this->sendError(trans('custom.please_select_active_location_return'), 500, $wareHouseError);
            }
        }

        if (isset($input['purchaseReturnDate'])) {
            if ($input['purchaseReturnDate']) {
                $input['purchaseReturnDate'] = new Carbon($input['purchaseReturnDate']);
            }
        }

        if (isset($input['supplierID'])) {
            $supplier = SupplierMaster::where("supplierCodeSystem", $input["supplierID"])->first();

            if (!empty($supplier)) {
                $input['supplierPrimaryCode'] = $supplier->primarySupplierCode;
                $input['supplierName'] = $supplier->supplierName;
                $input['liabilityAccountSysemID'] = $supplier->liabilityAccountSysemID;
                $input['liabilityAccount'] = $supplier->liabilityAccount;
                $input['UnbilledGRVAccountSystemID'] = $supplier->UnbilledGRVAccountSystemID;
                $input['UnbilledGRVAccount'] = $supplier->UnbilledGRVAccount;
            }
        }

        if ($purchaseReturn->confirmedYN == 0 && $input['confirmedYN'] == 1) {

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

            $trackingValidation = ItemTracking::validateTrackingOnDocumentConfirmation($purchaseReturn->documentSystemID, $purchaseReturn->purhaseReturnAutoID);

            if (!$trackingValidation['status']) {
                return $this->sendError($trackingValidation["message"], 500, ['type' => 'confirm']);
            }

            unset($inputParam);

            $validator = \Validator::make($input, [
                'purchaseReturnLocation' => 'required|numeric|min:1',
                'companyFinancePeriodID' => 'required|numeric|min:1',
                'companyFinanceYearID' => 'required|numeric|min:1',
                'purchaseReturnDate' => 'required|date|before_or_equal:today',
                'purchaseReturnRefNo' => 'required',
                'narration' => 'required',
                'serviceLineSystemID' => 'required|numeric|min:1',
                'supplierID' => 'required|numeric|min:1',
                'supplierTransactionCurrencyID' => 'required|numeric|min:1'
            ]);

            if ($validator->fails()) {
                return $this->sendError($validator->messages(), 422);
            }

            $documentDate = $input['purchaseReturnDate'];
            $monthBegin = $input['FYBiggin'];
            $monthEnd = $input['FYEnd'];
            if (($documentDate >= $monthBegin) && ($documentDate <= $monthEnd)) {
            } else {
                return $this->sendError('Return date is not within the selected financial period !', 500);
            }

            $checkItems = PurchaseReturnDetails::where('purhaseReturnAutoID', $id)
                ->count();
            if ($checkItems == 0) {
                return $this->sendError('Every return should have at least one item', 500);
            }

            $checkQuantity = PurchaseReturnDetails::where('purhaseReturnAutoID', $id)
                ->where(function ($q) {
                    $q->where('noQty', '<=', 0)
                        ->orWhereNull('noQty');
                })
                ->count();
            if ($checkQuantity > 0) {
                return $this->sendError('Every Item should have at least one minimum Qty Requested', 500);
            }

            $itemIssueDetails = PurchaseReturnDetails::where('purhaseReturnAutoID', $id)->get();

            $finalError = array('cost_zero' => array(),
                'cost_neg' => array(),
                'same_item' => array(),
                'qty_zero' => array(),
                'more_then_grv_qty' => array(),
                'currentStockQty_zero' => array(),
                'currentWareHouseStockQty_zero' => array(),
                'currentStockQty_more' => array(),
                'currentWareHouseStockQty_more' => array());
            $error_count = 0;

            foreach ($itemIssueDetails as $item) {
                $updateItem = $this->purchaseReturnDetailsRepository->find($item['purhasereturnDetailID']);
                $data = array('companySystemID' => $purchaseReturn->companySystemID,
                    'itemCodeSystem' => $updateItem->itemCode,
                    'wareHouseId' => $purchaseReturn->purchaseReturnLocation);
                $itemCurrentCostAndQty = \Inventory::itemCurrentCostAndQty($data);

                if ($itemCurrentCostAndQty['currentWareHouseStockQty'] <= 0) {
                    array_push($finalError['currentStockQty_zero'], $item['itemPrimaryCode']);
                    $error_count++;
                }
                if ($itemCurrentCostAndQty['currentStockQty'] <= 0) {
                    array_push($finalError['currentWareHouseStockQty_zero'], $item['itemPrimaryCode']);
                    $error_count++;
                }
                if ($item['noQty'] > $itemCurrentCostAndQty['currentStockQty']) {
                    array_push($finalError['currentStockQty_more'], $item['itemPrimaryCode']);
                    $error_count++;
                }

                if ($item['noQty'] > $itemCurrentCostAndQty['currentWareHouseStockQty']) {
                    array_push($finalError['currentWareHouseStockQty_more'], $item['itemPrimaryCode']);
                    $error_count++;
                }

                if ($item['noQty'] > $item['GRVQty']) {
                    array_push($finalError['more_then_grv_qty'], $item['itemPrimaryCode']);
                    $error_count++;
                }
            }

            $confirm_error = array('type' => 'confirm_error', 'data' => $finalError);
            if ($error_count > 0) {
                return $this->sendError("You cannot confirm this document.", 500, $confirm_error);
            }

            //check Input Vat Transfer GL Account if vat exist
            $totalVAT = PurchaseReturnDetails::where('purhaseReturnAutoID',$id)->selectRaw('SUM(VATAmount*noQty) as totalVAT')->first();
            if(TaxService::checkGRVVATEligible($purchaseReturn->companySystemID,$purchaseReturn->supplierID) && !empty($totalVAT) && $totalVAT->totalVAT > 0){
                if ($purchaseReturn->isInvoiceCreatedForGrv == 1) {
                    if(empty(TaxService::getInputVATGLAccount($purchaseReturn->companySystemID))){
                        return $this->sendError(trans('custom.cannot_confirm_input_vat_control_gl_account_not_co'), 500);
                    }
                } else {
                     if(empty(TaxService::getInputVATTransferGLAccount($purchaseReturn->companySystemID))){
                        return $this->sendError(trans('custom.cannot_confirm_input_vat_transfer_gl_account_not_c'), 500);
                    }
                }

            }

            $amount = PurchaseReturnDetails::where('purhaseReturnAutoID', $id)
                ->sum('netAmount');

            $piDetailSingleData = PurchaseReturnDetails::where('purhaseReturnAutoID', $id)
                                                       ->first();

            if ($piDetailSingleData) {
                $checkGrvAddedToIncoice = BookInvSuppDet::where('grvAutoID', $piDetailSingleData->grvAutoID)
                                                        ->with(['suppinvmaster'])
                                                        ->whereHas('suppinvmaster', function($query) {
                                                            $query->where('approved', 0);
                                                        })
                                                        ->first();

                if ($checkGrvAddedToIncoice) {
                    $supInvCode = (isset($checkGrvAddedToIncoice->suppinvmaster->bookingInvCode)) ? $checkGrvAddedToIncoice->suppinvmaster->bookingInvCode : "";
                    return $this->sendError('Selected GRV is been added to a draft supplier invoice '.$supInvCode.'. Delete the GRV from the invoice and try again.', 500);
                }
            }


            $input['RollLevForApp_curr'] = 1;
            $params = array('autoID' => $id,
                'company' => $purchaseReturn->companySystemID,
                'document' => $purchaseReturn->documentSystemID,
                'segment' => $input['serviceLineSystemID'],
                'category' => 0,
                'amount' => $amount
            );

            $confirm = \Helper::confirmDocument($params);
            if (!$confirm["success"]) {
                return $this->sendError($confirm["message"], 500);
            }


            if ($piDetailSingleData) {
                $input['isInvoiceCreatedForGrv'] =  $this->updateGrvInvoiceStatus($id, $piDetailSingleData->grvAutoID, $input['isInvoiceCreatedForGrv']);
            }
        }

        $employee = \Helper::getEmployeeInfo();

        $input['modifiedPc'] = gethostname();
        $input['modifiedUser'] = $employee->empID;
        $input['modifiedUserSystemID'] = $employee->employeeSystemID;


        $purchaseReturn = $this->purchaseReturnRepository->update($input, $id);

        return $this->sendReponseWithDetails($purchaseReturn->toArray(), 'PurchaseReturn updated successfully',1,$confirm['data'] ?? null);
    }


    public function updateReturnQtyInGrvDetails($grvDetailsID)
    {
        $totalQty = PurchaseReturnDetails::selectRaw('SUM(noQty) as totalRtnQty')
                                         ->where('grvDetailsID', $grvDetailsID)
                                         ->whereHas('master', function ($query) {
                                            $query->where('approved', -1);
                                         })
                                         ->groupBy('grvDetailsID')
                                         ->first();

        $updateData = [
                        'returnQty' => $totalQty->totalRtnQty
                    ];


        $updateRes = GRVDetails::where('grvDetailsID', $grvDetailsID)
                               ->update($updateData);
    }

    public function updateGrvInvoiceStatus($purhaseReturnAutoID, $grvAutoID, $isInvoiceCreatedForGrv)
    {
        $purchaseReturn = PurchaseReturn::find($purhaseReturnAutoID);

        if (!$purchaseReturn) {
            return $isInvoiceCreatedForGrv;
        }

        $checkGrvAddedToIncoice = BookInvSuppDet::where('grvAutoID', $grvAutoID)
                                                ->whereHas('suppinvmaster', function($query) {
                                                    $query->where('approved', -1);
                                                })
                                                ->first();

        if ($checkGrvAddedToIncoice) {
            $isInvoiceCreatedForGrv = 1;
            $purchaseReturn->isInvoiceCreatedForGrv = 1;
        } else {
            $isInvoiceCreatedForGrv = 0;
            $purchaseReturn->isInvoiceCreatedForGrv = 0;
        }

        $purchaseReturn->save();

        return $isInvoiceCreatedForGrv;
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/purchaseReturns/{id}",
     *      summary="Remove the specified PurchaseReturn from storage",
     *      tags={"PurchaseReturn"},
     *      description="Delete PurchaseReturn",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PurchaseReturn",
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
        /** @var PurchaseReturn $purchaseReturn */
        $purchaseReturn = $this->purchaseReturnRepository->findWithoutFail($id);

        if (empty($purchaseReturn)) {
            return $this->sendError(trans('custom.purchase_return_not_found'));
        }

        $purchaseReturn->delete();

        return $this->sendResponse($id, trans('custom.purchase_return_deleted_successfully'));
    }

    public function getPurchaseReturnByCompany(Request $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input, array('serviceLineSystemID',
            'purchaseReturnLocation', 'confirmedYN', 'approved', 'month', 'year'));
        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $search = $request->input('search.value');
        $grvLocation = $request['purchaseReturnLocation'];
        $grvLocation = (array)$grvLocation;
        $grvLocation = collect($grvLocation)->pluck('id');

        $serviceLineSystemID = $request['serviceLineSystemID'];
        $serviceLineSystemID = (array)$serviceLineSystemID;
        $serviceLineSystemID = collect($serviceLineSystemID)->pluck('id');

        $purchaseReturn = $this->purchaseReturnRepository->purchaseReturnListQuery($request, $input, $search, $serviceLineSystemID, $grvLocation);

        $historyPolicy = CompanyPolicyMaster::where('companyPolicyCategoryID', 29)
            ->where('companySystemID', $input['companyId'])->first();

        $policy = 0;

        if (!empty($historyPolicy)) {
            $policy = $historyPolicy->isYesNO;
        }

        return \DataTables::eloquent($purchaseReturn)
            ->addColumn('Actions', $policy)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('purhaseReturnAutoID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    public function getPurchaseReturnFormData(Request $request)
    {
        $companyId = $request['companyId'];

        $grvAutoID = $request['grvAutoID'];

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

        $years = Year::orderBy('year', 'desc')->get();

        $supplier = SupplierAssigned::select(DB::raw("supplierCodeSytem,CONCAT(primarySupplierCode, ' | ' ,supplierName) as supplierName"))
            ->where('companySystemID', $companyId)
            ->where('isActive', 1)
            ->where('isAssigned', -1)
            ->get();

        $currencies = CurrencyMaster::select(DB::raw("currencyID,CONCAT(CurrencyCode, ' | ' ,CurrencyName) as CurrencyName"))
            ->get();

       $locations = Location::where('is_deleted',0)->get();

        $wareHouseLocation = WarehouseMaster::where("companySystemID", $companyId);
        if (isset($request['type']) && $request['type'] != 'filter') {
            $wareHouseLocation = $wareHouseLocation->where('isActive', 1);
        }
        $wareHouseLocation = $wareHouseLocation->get();

        $grvTypes = GRVTypes::all();

        $financialYears = array(array('value' => intval(date("Y")), 'label' => date("Y")),
            array('value' => intval(date("Y", strtotime("-1 year"))), 'label' => date("Y", strtotime("-1 year"))));


        $companyFinanceYear = \Helper::companyFinanceYear($companyId);


        $output = array('segments' => $segments,
            'yesNoSelection' => $yesNoSelection,
            'yesNoSelectionForMinus' => $yesNoSelectionForMinus,
            'month' => $month,
            'years' => $years,
            'currencies' => $currencies,
            'locations' => $locations,
            'wareHouseLocation' => $wareHouseLocation,
            'financialYears' => $financialYears,
            'suppliers' => $supplier,
            'grvTypes' => $grvTypes,
            'companyFinanceYear' => $companyFinanceYear
        );

        return $this->sendResponse($output, trans('custom.record_retrieved_successfully_1'));
    }

    public function purchaseReturnSegmentChkActive(Request $request)
    {

        $input = $request->all();
        $wareHouseError = array('type' => 'wareHouse');
        $serviceLineError = array('type' => 'serviceLine');
        $purchaseReturnAutoID = $input['purchaseReturnAutoID'];

        $purchaseReturn = PurchaseReturn::find($purchaseReturnAutoID);

        if (empty($purchaseReturn)) {
            return $this->sendError(trans('custom.purchase_return_not_found'));
        }

        $validator = \Validator::make($purchaseReturn->toArray(), [
            'purchaseReturnLocation' => 'required|numeric|min:1',
            'serviceLineSystemID' => 'required|numeric|min:1',
            'supplierID' => 'required|numeric|min:1',
            'supplierTransactionCurrencyID' => 'required|numeric|min:1'
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }


        $checkDepartmentActive = SegmentMaster::find($purchaseReturn->serviceLineSystemID);
        if (empty($checkDepartmentActive)) {
            return $this->sendError(trans('custom.segment_not_found'));
        }

        if ($checkDepartmentActive->isActive == 0) {
          //  $this->purchaseReturnRepository->update(['serviceLineSystemID' => null, 'serviceLineCode' => null], $purchaseReturnAutoID);
            return $this->sendError('Please select a active segment ', 500, $serviceLineError);
        }

        $checkWareHouseActive = WarehouseMaster::find($purchaseReturn->purchaseReturnLocation);
        if (empty($checkWareHouseActive)) {
            return $this->sendError(trans('custom.location_not_found'), 500, $wareHouseError);
        }

        if ($checkWareHouseActive->isActive == 0) {
            $this->purchaseReturnRepository->update(['purchaseReturnLocation' => null], $purchaseReturnAutoID);
            return $this->sendError('Please select a active location', 500, $wareHouseError);
        }


        return $this->sendResponse($purchaseReturn, 'success');
    }
    public function grvForPurchaseReturn(Request $request)
    {

        $input = $request->all();

        $purchaseReturnAutoID = $input['purchaseReturnAutoID'];

        $purchaseReturn = PurchaseReturn::find($purchaseReturnAutoID);

        if (empty($purchaseReturn)) {
            return $this->sendError(trans('custom.purchase_return_not_found'));
        }

        $grv = GRVMaster::where('companySystemID', $purchaseReturn->companySystemID)
                        ->where('serviceLineSystemID', $purchaseReturn->serviceLineSystemID)
                        ->where('grvLocation', $purchaseReturn->purchaseReturnLocation)
                        ->where('approved', -1)
                        ->where('grvCancelledYN', '!=',-1)
                        ->where('supplierID', $purchaseReturn->supplierID)
                        ->whereDate('grvDate', '<=',$purchaseReturn->purchaseReturnDate)
                        ->where('supplierTransactionCurrencyID', $purchaseReturn->supplierTransactionCurrencyID)
                        ->get();

        return $this->sendResponse($grv, 'success');
    }

    public function grvDetailByMasterForPurchaseReturn(Request $request)
    {

        $input = $request->all();

        $grvAutoID = $input['grvAutoID'];

        $grvMaster = GRVMaster::find($grvAutoID);

        if (empty($grvMaster)) {
            return $this->sendError(trans('custom.good_receipt_voucher_not_found_1'));
        }

        $grvDetails = GRVDetails::where('grvAutoID', $grvMaster->grvAutoID)
                                ->with(['unit'])
                                ->whereRaw('noQty - returnQty != ?', [0])
                                ->get();

        return $this->sendResponse($grvDetails, 'success');
    }

    /**
     * Display the specified Purchase Return Audit.
     * GET|HEAD /getPurchaseReturnAudit
     *
     * @param  int $id
     *
     * @return Response
     */
    public function getPurchaseReturnAudit(Request $request)
    {
        $id = $request->get('id');
        $purchaseReturn = $this->purchaseReturnRepository->getAudit($id);

        if (empty($purchaseReturn)) {
            return $this->sendError(trans('custom.purchase_return_not_found'));
        }

        $purchaseReturn->docRefNo = \Helper::getCompanyDocRefNo($purchaseReturn->companySystemID, $purchaseReturn->documentSystemID);

        return $this->sendResponse($purchaseReturn->toArray(), trans('custom.purchase_return_retrieved_successfully'));
    }

    public function getPurchaseReturnApprovalByUser(Request $request)
    {

        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input, array('serviceLineSystemID', 'confirmedYN', 'approved', 'purchaseReturnLocation', 'month', 'year'));

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
                'erp_purchasereturnmaster.*',
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
                    ->where('documentSystemID', 24)
                    ->first();

                if ($serviceLinePolicy && $serviceLinePolicy->isServiceLineApproval == -1) {
                    //$query->on('erp_documentapproved.serviceLineSystemID', '=', 'employeesdepartments.ServiceLineSystemID');
                }

                $query->whereIn('employeesdepartments.documentSystemID', [24])
                    ->where('employeesdepartments.companySystemID', $companyId)
                    ->where('employeesdepartments.employeeSystemID', $empID)
                    ->where('employeesdepartments.isActive', 1)
                    ->where('employeesdepartments.removedYN', 0);
            })
            ->join('erp_purchasereturnmaster', function ($query) use ($companyId, $search) {
                $query->on('erp_documentapproved.documentSystemCode', '=', 'purhaseReturnAutoID')
                    ->on('erp_documentapproved.rollLevelOrder', '=', 'RollLevForApp_curr')
                    ->where('erp_purchasereturnmaster.companySystemID', $companyId)
                    ->where('erp_purchasereturnmaster.approved', 0)
                    ->where('erp_purchasereturnmaster.confirmedYN', 1);
            })
            ->where('erp_documentapproved.approvedYN', 0)
            ->leftJoin('employees', 'createdUserSystemID', 'employees.employeeSystemID')
            ->leftJoin('warehousemaster', 'purchaseReturnLocation', 'warehousemaster.wareHouseSystemCode')
            ->leftJoin('serviceline', 'erp_purchasereturnmaster.serviceLineSystemID', 'serviceline.serviceLineSystemID')
            ->where('erp_documentapproved.rejectedYN', 0)
            ->whereIn('erp_documentapproved.documentSystemID', [24])
            ->where('erp_documentapproved.companySystemID', $companyId);


        if (array_key_exists('serviceLineSystemID', $input)) {
            if ($input['serviceLineSystemID'] && !is_null($input['serviceLineSystemID'])) {
                $purchaseReturnMaster->where('erp_purchasereturnmaster.serviceLineSystemID', $input['serviceLineSystemID']);
            }
        }

        if (array_key_exists('purchaseReturnLocation', $input)) {
            if ($input['purchaseReturnLocation'] && !is_null($input['purchaseReturnLocation'])) {
                $purchaseReturnMaster->where('erp_purchasereturnmaster.purchaseReturnLocation', $input['purchaseReturnLocation']);
            }
        }

        if (array_key_exists('month', $input)) {
            if ($input['month'] && !is_null($input['month'])) {
                $purchaseReturnMaster->whereMonth('erp_purchasereturnmaster.purchaseReturnDate', '=', $input['month']);
            }
        }

        if (array_key_exists('year', $input)) {
            if ($input['year'] && !is_null($input['year'])) {
                $purchaseReturnMaster->whereYear('erp_purchasereturnmaster.purchaseReturnDate', '=', $input['year']);
            }
        }

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $purchaseReturnMaster = $purchaseReturnMaster->where(function ($query) use ($search) {
                $query->where('purchaseReturnCode', 'LIKE', "%{$search}%");
                    // ->orWhere('comment', 'LIKE', "%{$search}%");
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
                        $query->orderBy('purhaseReturnAutoID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);

    }

    public function getPurchaseReturnApprovedByUser(Request $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input, array('serviceLineSystemID', 'confirmedYN', 'approved', 'purchaseReturnLocation', 'month', 'year'));

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
                'erp_purchasereturnmaster.*',
                'employees.empName As created_emp',
                'serviceline.ServiceLineDes As serviceLineDes',
                'warehousemaster.wareHouseDescription As wareHouseDescription',
                'erp_documentapproved.documentApprovedID',
                'rollLevelOrder',
                'approvalLevelID',
                'documentSystemCode')
            ->join('erp_purchasereturnmaster', function ($query) use ($companyId, $search) {
                $query->on('erp_documentapproved.documentSystemCode', '=', 'purhaseReturnAutoID')
                    ->where('erp_purchasereturnmaster.companySystemID', $companyId)
                    ->where('erp_purchasereturnmaster.confirmedYN', 1);
            })
            ->where('erp_documentapproved.approvedYN', -1)
            ->leftJoin('employees', 'createdUserSystemID', 'employees.employeeSystemID')
            ->leftJoin('warehousemaster', 'purchaseReturnLocation', 'warehousemaster.wareHouseSystemCode')
            ->leftJoin('serviceline', 'erp_purchasereturnmaster.serviceLineSystemID', 'serviceline.serviceLineSystemID')
            ->where('erp_documentapproved.rejectedYN', 0)
            ->whereIn('erp_documentapproved.documentSystemID', [24])
            ->where('erp_documentapproved.companySystemID', $companyId)
            ->where('erp_documentapproved.employeeSystemID', $empID);

        if (array_key_exists('serviceLineSystemID', $input)) {
            if ($input['serviceLineSystemID'] && !is_null($input['serviceLineSystemID'])) {
                $purchaseReturnMaster->where('erp_purchasereturnmaster.serviceLineSystemID', $input['serviceLineSystemID']);
            }
        }

        if (array_key_exists('purchaseReturnLocation', $input)) {
            if ($input['purchaseReturnLocation'] && !is_null($input['purchaseReturnLocation'])) {
                $purchaseReturnMaster->where('erp_purchasereturnmaster.purchaseReturnLocation', $input['purchaseReturnLocation']);
            }
        }

        if (array_key_exists('month', $input)) {
            if ($input['month'] && !is_null($input['month'])) {
                $purchaseReturnMaster->whereMonth('erp_purchasereturnmaster.purchaseReturnDate', '=', $input['month']);
            }
        }

        if (array_key_exists('year', $input)) {
            if ($input['year'] && !is_null($input['year'])) {
                $purchaseReturnMaster->whereYear('erp_purchasereturnmaster.purchaseReturnDate', '=', $input['year']);
            }
        }

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $purchaseReturnMaster = $purchaseReturnMaster->where(function ($query) use ($search) {
                $query->where('purchaseReturnCode', 'LIKE', "%{$search}%");
                   // ->orWhere('comment', 'LIKE', "%{$search}%");
            });
        }

        return \DataTables::of($purchaseReturnMaster)
            ->addColumn('Actions', 'Actions', "Actions")
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('purhaseReturnAutoID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    public function printPurchaseReturn(Request $request)
    {
        $id = $request->get('id');
        $purchaseReturn = $this->purchaseReturnRepository->getAudit($id);

        if (empty($purchaseReturn)) {
            return $this->sendError(trans('custom.purchase_return_not_found'));
        }

        $purchaseReturn->docRefNo = \Helper::getCompanyDocRefNo($purchaseReturn->companySystemID, $purchaseReturn->documentSystemID);

        $array = array('entity' => $purchaseReturn);
        $time = strtotime("now");
        $fileName = 'purchase_return_' . $id . '_' . $time . '.pdf';
        $html = view('print.purchase_return', $array);
        $htmlFooter = view('print.purchase_return_footer', $array);
        $mpdf = new \Mpdf\Mpdf(['tempDir' => public_path('tmp'), 'mode' => 'utf-8', 'format' => 'A4-L', 'setAutoTopMargin' => 'stretch', 'autoMarginPadding' => -10]);
        $mpdf->AddPage('L');
        $mpdf->setAutoBottomMargin = 'stretch';
        $mpdf->SetHTMLFooter($htmlFooter);
        $mpdf->WriteHTML($html);
        return $mpdf->Output($fileName, 'I');
    }

    public function purchaseReturnReopen(Request $request)
    {
        $input = $request->all();

        $id = $input['purhaseReturnAutoID'];
        $purchaseReturnMaster = $this->purchaseReturnRepository->findWithoutFail($id);
        $emails = array();
        if (empty($purchaseReturnMaster)) {
            return $this->sendError(trans('custom.purchase_return_not_found'));
        }

        if ($purchaseReturnMaster->approved == -1) {
            return $this->sendError(trans('custom.you_cannot_reopen_this_purchase_return_it_is_alrea_1'));
        }

        if ($purchaseReturnMaster->RollLevForApp_curr > 1) {
            return $this->sendError(trans('custom.you_cannot_reopen_this_purchase_return_it_is_alrea'));
        }

        if ($purchaseReturnMaster->confirmedYN == 0) {
            return $this->sendError(trans('custom.you_cannot_reopen_this_purchase_return_it_is_not_c'));
        }

        $updateInput = ['confirmedYN' => 0, 'confirmedByEmpSystemID' => null, 'confirmedByEmpID' => null,
            'confirmedByName' => null, 'confirmedDate' => null, 'RollLevForApp_curr' => 1];

        $this->purchaseReturnRepository->update($updateInput, $id);

        $employee = \Helper::getEmployeeInfo();

        $document = DocumentMaster::where('documentSystemID', $purchaseReturnMaster->documentSystemID)->first();

        $cancelDocNameBody = $document->documentDescription . ' <b>' . $purchaseReturnMaster->itemReturnCode . '</b>';
        $cancelDocNameSubject = $document->documentDescription . ' ' . $purchaseReturnMaster->itemReturnCode;

        $subject = $cancelDocNameSubject . ' is reopened';

        $body = '<p>' . $cancelDocNameBody . ' is reopened by ' . $employee->empID . ' - ' . $employee->empFullName . '</p><p>Comment : ' . $input['reopenComments'] . '</p>';

        $documentApproval = DocumentApproved::where('companySystemID', $purchaseReturnMaster->companySystemID)
            ->where('documentSystemCode', $purchaseReturnMaster->purhaseReturnAutoID)
            ->where('documentSystemID', $purchaseReturnMaster->documentSystemID)
            ->where('rollLevelOrder', 1)
            ->first();

        if ($documentApproval) {
            if ($documentApproval->approvedYN == 0) {
                $companyDocument = CompanyDocumentAttachment::where('companySystemID', $purchaseReturnMaster->companySystemID)
                    ->where('documentSystemID', $purchaseReturnMaster->documentSystemID)
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
            ->where('companySystemID', $purchaseReturnMaster->companySystemID)
            ->where('documentSystemID', $purchaseReturnMaster->documentSystemID)
            ->delete();

        /*Audit entry*/
        AuditTrial::createAuditTrial($purchaseReturnMaster->documentSystemID,$id,$input['reopenComments'],'Reopened');

        return $this->sendResponse($purchaseReturnMaster->toArray(), trans('custom.purchase_return_reopened_successfully'));
    }


     public function purchaseReturnForGRV(Request $request)
    {
        $input = $request->all();
        $companyID = $input['companyId'];
        $grvAutoID = $input['grvAutoID'];

        $grvMaster = GRVMaster::where('grvAutoID', $grvAutoID)
            ->first();

        if (empty($grvMaster)) {
            return $this->sendError(trans('custom.good_receipt_voucher_not_found_1'));
        }

        //checking segment is active
        $segments = SegmentMaster::where("serviceLineSystemID", $grvMaster->serviceLineSystemID)
            ->where('companySystemID', $companyID)
            ->where('isActive', 1)
            ->first();

        if (empty($segments)) {
            return $this->sendError('Selected segment is not active. Please select an active segment');
        }

        $purchaseReturn = PurchaseReturn::where('companySystemID', $companyID)
                                        ->where('serviceLineSystemID', $grvMaster->serviceLineSystemID)
                                        ->where('supplierID', $grvMaster->supplierID)
                                        ->where('supplierTransactionCurrencyID', $grvMaster->supplierTransactionCurrencyID)
                                        ->where('approved', -1)
                                        ->where('confirmedYN', 1)
                                        ->where('prClosedYN', 0)
                                        ->where('grvRecieved', '<>', 2)
                                        ->orderBy('purhaseReturnAutoID', 'DESC')
                                        ->get();

        return $this->sendResponse($purchaseReturn->toArray(), trans('custom.purchase_return_details_retrieved_successfully'));
    }

    public function getPurchaseReturnDetailForGRV(Request $request)
    {
        $input = $request->all();
        $purhaseReturnAutoID = $input['purhaseReturnAutoID'];

        $detail = PurchaseReturnDetails::select(DB::raw('itemPrimaryCode,itemDescription,supplierPartNumber,"" as isChecked, "" as noQty,noQty as prnQty,unitOfMeasure,purhaseReturnAutoID,purhasereturnDetailID,itemCode,receivedQty,companyID,itemPrimaryCode,itemDescription,itemFinanceCategoryID,itemFinanceCategorySubID,financeGLcodebBSSystemID,financeGLcodebBS,financeGLcodePLSystemID,financeGLcodePL,includePLForGRVYN,supplierPartNumber,unitOfMeasure,netAmount,comment,supplierDefaultCurrencyID,supplierDefaultER,companyReportingCurrencyID,companyReportingER,localCurrencyID,localCurrencyER,GRVcostPerUnitLocalCur,GRVcostPerUnitSupDefaultCur,GRVcostPerUnitSupTransCur,GRVcostPerUnitComRptCur,grvDetailsID, grvAutoID'))
            ->with(['unit' => function ($query) {
            }])
            ->where('purhaseReturnAutoID', $purhaseReturnAutoID)
            ->where('GRVSelectedYN', 0)
            ->where('goodsRecievedYN', '<>', 2)
            ->get();

        return $this->sendResponse($detail, trans('custom.purchase_order_details_retrieved_successfully'));

    }


    public function purchaseReturnAmend(Request $request)
    {
        $input = $request->all();

        $purhaseReturnAutoID = $input['purhaseReturnAutoID'];

        $prMasterData = PurchaseReturn::find($purhaseReturnAutoID);
        if (empty($prMasterData)) {
            return $this->sendError(trans('custom.good_receipt_voucher_not_found'));
        }

        if ($prMasterData->refferedBackYN != -1) {
            return $this->sendError(trans('custom.you_cannot_refer_back_this_good_receipt_voucher'));
        }

        $prMasterDataArray = $prMasterData->toArray();

        $storePRHistory = PurchaseReturnMasterRefferedBack::insert($prMasterDataArray);

        $fetchPRDetails = PurchaseReturnDetails::where('purhaseReturnAutoID', $purhaseReturnAutoID)
            ->get();

        if (!empty($fetchPRDetails)) {
            foreach ($fetchPRDetails as $bookDetail) {
                $bookDetail['timesReferred'] = $prMasterData->timesReferred;
            }
        }

        $prDetailArray = $fetchPRDetails->toArray();

        $storePRDetailHistory = PurchaseReturnDetailsRefferedBack::insert($prDetailArray);

        $fetchDocumentApproved = DocumentApproved::where('documentSystemCode', $purhaseReturnAutoID)
                                                 ->where('companySystemID', $prMasterData->companySystemID)
                                                 ->where('documentSystemID', $prMasterData->documentSystemID)
                                                 ->get();

        if (!empty($fetchDocumentApproved)) {
            foreach ($fetchDocumentApproved as $DocumentApproved) {
                $DocumentApproved['refTimes'] = $prMasterData->timesReferred;
            }
        }

        $DocumentApprovedArray = $fetchDocumentApproved->toArray();

        $storeDocumentReferedHistory = DocumentReferedHistory::insert($DocumentApprovedArray);

        $deleteApproval = DocumentApproved::where('documentSystemCode', $purhaseReturnAutoID)
                                        ->where('companySystemID', $prMasterData->companySystemID)
                                        ->where('documentSystemID', $prMasterData->documentSystemID)
                                        ->delete();

        if ($deleteApproval) {
            $prMasterData->refferedBackYN = 0;
            $prMasterData->confirmedYN = 0;
            $prMasterData->confirmedByEmpSystemID = null;
            $prMasterData->confirmedByEmpID = null;
            $prMasterData->confirmedByName = null;
            $prMasterData->confirmedDate = null;
            $prMasterData->RollLevForApp_curr = 1;
            $prMasterData->save();
        }

        return $this->sendResponse($prMasterData->toArray(), trans('custom.purchase_return_amend_successfully'));
    }
}
