<?php
/**
 * =============================================
 * -- File Name : ProcumentOrderAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Purchase Order
 * -- Author : Mohamed Nazir
 * -- Create date : 28 - March 2018
 * -- Description : This file contains the all CRUD for Purchase Order
 * -- REVISION HISTORY
 * -- Date: 28-March 2018 By: Nazir Description: Added new functions named as getProcumentOrderByDocumentType() For load Master View
 * -- Date: 29-March 2018 By: Nazir Description: Added new functions named as getProcumentOrderFormData() for Master View Filter
 * -- Date: 10-April 2018 By: Nazir Description: Added new functions named as getShippingAndInvoiceDetails() for pull details from erp_address table
 * -- Date: 11-April 2018 By: Nazir Description: Added new functions named as procumentOrderDetailTotal() for pull details total from erp_purchaseorderdetails table
 * -- Date: 24-April 2018 By: Nazir Description: Added new functions named as getProcumentOrderAllAmendments() For load PO Amendment Master View
 * -- Date: 25-April 2018 By: Nazir Description: Added new functions named as poCheckDetailExistinGrv() for check in grv details,erp_advancepaymentdetails table before closing a PO in amendment pull details total from erp_purchaseorderdetails table
 * -- Date: 25-April 2018 By: Nazir Description: Added new functions named as procumentOrderCancel() for cancel PO
 * -- Date: 25-April 2018 By: Nazir Description: Added new functions named as procumentOrderReturnBack() for cancel return back PO to start level PO
 * -- Date: 03-May 2018 By: Nazir Description: Added new functions named as reportSpentAnalysis() for load Spent Analysis by Report master view
 * -- Date: 03-May 2018 By: Nazir Description: Added new functions named as reportSpentAnalysisExport() for report Spent Analysis export to excel report
 * -- Date: 08-May 2018 By: Nazir Description: Added new functions named as manualCloseProcurementOrder()
 * -- Date: 09-May 2018 By: Nazir Description: Added new functions named as getProcumentOrderPrintPDF()
 * -- Date: 10-May 2018 By: Nazir Description: Added new functions named as procumentOrderSegmentchk()
 * -- Date: 10-May 2018 By: Nazir Description: Added new functions named as getAllApprovedPO()
 * -- Date: 10-May 2018 By: Nazir Description: Added new functions named as getApprovedPOForCurrentUser()
 * -- Date: 11-May 2018 By: Nazir Description: Added new functions named as getGRVDrilldownSpentAnalysis()
 * -- Date: 15-May 2018 By: Nazir Description: Added new functions named as manualCloseProcurementOrderPrecheck()
 * -- Date: 15-May 2018 By: Nazir Description: Added new functions named as getGRVDrilldownSpentAnalysisTotal(),
 * -- Date: 16-May 2018 By: Fayas Description: Added new functions named as amendProcurementOrder(),
 * -- Date: 18-May 2018 By: Fayas Description: Added new functions named as procumentOrderPrHistory(),
 * -- Date: 21-May 2018 By: Fayas Description: Added new functions named as amendProcurementOrderPreCheck(),
 * -- Date: 24-May 2018 By: Fayas Description: Added new functions named as procumentOrderChangeSupplier(),
 * -- Date: 24-May 2018 By: Nazir Description: Added new functions named as ProcurementOrderAudit(),
 * -- Date: 25-May 2018 By: Nazir Description: Added new functions named as reportSpentAnalysisDrilldownExport(),
 * -- Date: 28-May 2018 By: Nazir Description: Added new functions named as getGRVBasedPODropdowns(),
 */

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateProcumentOrderAPIRequest;
use App\Http\Requests\API\UpdateProcumentOrderAPIRequest;
use App\Models\Employee;
use App\Models\Months;
use App\Models\Company;
use App\Models\SupplierMaster;
use App\Models\CompanyPolicyMaster;
use App\Models\CurrencyMaster;
use App\Models\DocumentMaster;
use App\Models\FinanceItemCategoryMaster;
use App\Models\Location;
use App\Models\DocumentApproved;
use App\Models\ProcumentOrder;
use App\Models\SegmentMaster;
use App\Models\YesNoSelection;
use App\Models\YesNoSelectionForMinus;
use App\Models\ItemAssigned;
use App\Models\PurchaseOrderDetails;
use App\Models\ErpAddress;
use App\Models\PoPaymentTermTypes;
use App\Models\SupplierAssigned;
use App\Models\CompanyDocumentAttachment;
use App\Models\PoPaymentTerms;
use App\Models\SupplierCurrency;
use App\Models\GRVDetails;
use App\Models\AdvancePaymentDetails;
use App\Repositories\ProcumentOrderRepository;
use Illuminate\Http\Request;
use App\Repositories\UserRepository;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;


/**
 * Class ProcumentOrderController
 * @package App\Http\Controllers\API
 */
class ProcumentOrderAPIController extends AppBaseController
{
    /** @var  ProcumentOrderRepository */
    private $procumentOrderRepository;
    private $userRepository;

    public function __construct(ProcumentOrderRepository $procumentOrderRepo, UserRepository $userRepo)
    {
        $this->procumentOrderRepository = $procumentOrderRepo;
        $this->userRepository = $userRepo;
    }

    /**
     * Display a listing of the ProcumentOrder.
     * GET|HEAD /procumentOrders
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->procumentOrderRepository->pushCriteria(new RequestCriteria($request));
        $this->procumentOrderRepository->pushCriteria(new LimitOffsetCriteria($request));
        $procumentOrders = $this->procumentOrderRepository->all();

        return $this->sendResponse($procumentOrders->toArray(), 'Procurement Orders retrieved successfully');
    }

    /**
     * Store a newly created ProcumentOrder in storage.
     * POST /procumentOrders
     *
     * @param CreateProcumentOrderAPIRequest $request
     *
     * @return Response
     */

    public function store(CreateProcumentOrderAPIRequest $request)
    {
        $input = $request->all();

        if ($input['documentSystemID'] == 5) {
            if (isset($input['WO_PeriodFrom'])) {
                $WO_PeriodFrom = new Carbon($input['WO_PeriodFrom'][0]);
                $WO_PeriodTo = new Carbon($input['WO_PeriodFrom'][1]);
            }
        }

        $input = $this->convertArrayToValue($input);

        $id = Auth::id();
        $user = $this->userRepository->with(['employee'])->findWithoutFail($id);

        $input['createdPcID'] = gethostname();
        $input['createdUserID'] = $user->employee['empID'];
        $input['createdUserSystemID'] = $user->employee['employeeSystemID'];
        $input['departmentID'] = 'PROC';

        $lastSerial = ProcumentOrder::where('companySystemID', $input['companySystemID'])
            ->orderBy('purchaseOrderID', 'desc')
            ->first();

        $lastSerialNumber = 0;
        if ($lastSerial) {
            $lastSerialNumber = intval($lastSerial->serialNumber) + 1;
        }

        $erpAddress = ErpAddress::where("companySystemID", $input['companySystemID'])
            ->where('isDefault', -1)
            ->get();

        if (!empty($erpAddress)) {
            foreach ($erpAddress as $address) {
                if ($address['addressTypeID'] == 1) {
                    $input['shippingAddressID'] = $address['addressID'];
                    $input['shippingAddressDescriprion'] = $address['addressDescrption'];
                    $input['shipTocontactPersonID'] = $address['contactPersonID'];
                    $input['shipTocontactPersonTelephone'] = $address['contactPersonTelephone'];
                    $input['shipTocontactPersonFaxNo'] = $address['contactPersonFaxNo'];
                    $input['shipTocontactPersonEmail'] = $address['contactPersonEmail'];
                } else if ($address['addressTypeID'] == 2) {
                    $input['invoiceToAddressID'] = $address['addressID'];
                    $input['invoiceToAddressDescription'] = $address['addressDescrption'];
                    $input['invoiceTocontactPersonID'] = $address['contactPersonID'];
                    $input['invoiceTocontactPersonTelephone'] = $address['contactPersonTelephone'];
                    $input['invoiceTocontactPersonFaxNo'] = $address['contactPersonFaxNo'];
                    $input['invoiceTocontactPersonEmail'] = $address['contactPersonEmail'];
                } else if ($address['addressTypeID'] == 3) {
                    $input['soldToAddressID'] = $address['addressID'];
                    $input['soldToAddressDescriprion'] = $address['addressDescrption'];
                    $input['soldTocontactPersonID'] = $address['contactPersonID'];
                    $input['soldTocontactPersonTelephone'] = $address['contactPersonTelephone'];
                    $input['soldTocontactPersonFaxNo'] = $address['contactPersonFaxNo'];
                    $input['soldTocontactPersonEmail'] = $address['contactPersonEmail'];
                }
            }
        }
        //calculate total months in WO type
        if ($input['documentSystemID'] == 5) {
            if (isset($input['WO_PeriodFrom'])) {
                $input['WO_PeriodFrom'] = $WO_PeriodFrom;
                $input['WO_PeriodTo'] = $WO_PeriodTo;
                $input['WO_NoOfGeneratedTimes'] = 0;
                $ts1 = strtotime($WO_PeriodFrom);
                $ts2 = strtotime($WO_PeriodTo);

                $year1 = date('Y', $ts1);
                $year2 = date('Y', $ts2);

                $month1 = date('n', $ts1);
                $month2 = date('n', $ts2);

                $input['WO_NoOfAutoGenerationTimes'] = abs((($year2 - $year1) * 12) + ($month2 - $month1));

            }
        }

        $segment = SegmentMaster::where('serviceLineSystemID', $input['serviceLineSystemID'])->first();
        if ($segment) {
            $input['serviceLine'] = $segment->ServiceLineCode;
        }

        $input['serialNumber'] = $lastSerialNumber;

        if (isset($input['expectedDeliveryDate'])) {
            if ($input['expectedDeliveryDate']) {
                $input['expectedDeliveryDate'] = new Carbon($input['expectedDeliveryDate']);
            }
        }

        $document = DocumentMaster::where('documentSystemID', $input['documentSystemID'])->first();
        if ($document) {
            $input['documentID'] = $document->documentID;
        }

        $companyDocumentAttachment = CompanyDocumentAttachment::where('companySystemID', $input['companySystemID'])
            ->where('documentSystemID', $input['documentSystemID'])
            ->first();

        if ($companyDocumentAttachment) {
            $input['docRefNo'] = $companyDocumentAttachment->docRefNumber;
        }

        $companyCurrencyConversion = \Helper::currencyConversion($input['companySystemID'], $input['supplierTransactionCurrencyID'], $input['supplierTransactionCurrencyID'], 0);

        //var_dump($companyCurrencyConversion);
        $company = Company::where('companySystemID', $input['companySystemID'])->first();
        if ($company) {
            $input['companyID'] = $company->CompanyID;
            $input['localCurrencyID'] = $company->localCurrencyID;
            $input['companyReportingCurrencyID'] = $company->reportingCurrency;
            $input['vatRegisteredYN'] = $company->vatRegisteredYN;
            $input['companyReportingER'] = $companyCurrencyConversion['trasToRptER'];
            $input['localCurrencyER'] = $companyCurrencyConversion['trasToLocER'];
        }

        if (isset($input['partiallyGRVAllowed']) && $input['partiallyGRVAllowed']) {
            $input['partiallyGRVAllowed'] = -1;
        } else {
            $input['partiallyGRVAllowed'] = 0;
        }
        if (isset($input['logisticsAvailable']) && $input['logisticsAvailable']) {
            $input['logisticsAvailable'] = -1;
        } else {
            $input['logisticsAvailable'] = 0;
        }
        $documentMaster = DocumentMaster::where('documentSystemID', $input['documentSystemID'])->first();

        if ($documentMaster) {
            $poCode = ($company->CompanyID . '\\' . $documentMaster['documentID'] . str_pad($lastSerialNumber, 6, '0', STR_PAD_LEFT));
            $input['purchaseOrderCode'] = $poCode;
        }

        $supplier = SupplierMaster::where('supplierCodeSystem', $input['supplierID'])->first();
        if ($supplier) {
            $input['supplierPrimaryCode'] = $supplier->primarySupplierCode;
            $input['supplierName'] = $supplier->supplierName;
            $input['supplierAddress'] = $supplier->address;
            $input['supplierTelephone'] = $supplier->telephone;
            $input['supplierFax'] = $supplier->fax;
            $input['supplierEmail'] = $supplier->supEmail;
            $input['creditPeriod'] = $supplier->creditPeriod;
        }

        $supplierCurrency = SupplierCurrency::where('supplierCodeSystem', $input['supplierID'])
            ->where('isDefault', -1)
            ->first();

        if ($supplierCurrency) {
            $input['supplierDefaultCurrencyID'] = $supplierCurrency->currencyID;
            $input['supplierTransactionER'] = 1;
        }

        $supplierAssignedDetai = SupplierAssigned::where('supplierCodeSytem', $input['supplierID'])
            ->where('companySystemID', $input['companySystemID'])
            ->first();

        if ($supplierAssignedDetai) {
            $input['supplierVATEligible'] = $supplierAssignedDetai->vatEligible;
            $input['VATPercentage'] = $supplierAssignedDetai->vatPercentage;
        }

        $procumentOrders = $this->procumentOrderRepository->create($input);

        return $this->sendResponse($procumentOrders->toArray(), 'Procurement Order saved successfully');
    }

    /**
     * Display the specified ProcumentOrder.
     * GET|HEAD /procumentOrders/{id}
     *
     * @param  int $id
     *
     * @return Response
     */

    public function show($id)
    {
        /** @var ProcumentOrder $procumentOrder */
        $procumentOrder = $this->procumentOrderRepository->with(['created_by', 'confirmed_by', 'segment'])->findWithoutFail($id);

        if (empty($procumentOrder)) {
            return $this->sendError('Procurement Order not found');
        }

        $employee = \Helper::getEmployeeInfo();
        $procumentOrder->isAmendAccess = 0;
        if ($procumentOrder->WO_amendYN == -1 && $procumentOrder->WO_amendRequestedByEmpID == $employee->empID) {
            $procumentOrder->isAmendAccess = 1;
        }

        return $this->sendResponse($procumentOrder->toArray(), 'Procurement Order retrieved successfully');
    }

    /**
     * Update the specified ProcumentOrder in storage.
     * PUT/PATCH /procumentOrders/{id}
     *
     * @param  int $id
     * @param UpdateProcumentOrderAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateProcumentOrderAPIRequest $request)
    {
        //$empInfo = self::getEmployeeInfo();
        $userId = Auth::id();
        $user = $this->userRepository->with(['employee'])->findWithoutFail($userId);

        $input = $request->all();

        $isAmendAccess = $input['isAmendAccess'];

        $input = array_except($input, ['created_by', 'confirmed_by', 'expectedDeliveryDate', 'totalOrderAmount', 'segment', 'isAmendAccess']);
        $input = $this->convertArrayToValue($input);

        $procumentOrderUpdate = ProcumentOrder::where('purchaseOrderID', '=', $id)->first();

        if (isset($input['expectedDeliveryDate'])) {
            if ($input['expectedDeliveryDate']) {
                $input['expectedDeliveryDate'] = new Carbon($input['expectedDeliveryDate']);
            }
        }

        /** @var ProcumentOrder $procumentOrder */
        $procumentOrder = $this->procumentOrderRepository->findWithoutFail($id);

        if (empty($procumentOrder)) {
            return $this->sendError('Procurement Order not found');
        }

        $oldPoTotalSupplierTransactionCurrency = $procumentOrder->poTotalSupplierTransactionCurrency;
        $employee = \Helper::getEmployeeInfo();
        //$employee->employeeSystemID;

        if ($procumentOrder->WO_amendYN == -1 && $isAmendAccess == 1 && $procumentOrder->WO_amendRequestedByEmpID != $employee->empID) {
            return $this->sendError('You cannot amend this order, this is already amending by ' . $procumentOrder->WO_amendRequestedByEmpID, 500);
        }

        if ($procumentOrder->poCancelledYN == -1) {
            return $this->sendError('This Purchase Order closed. You cannot edit.', 500);
        }


        if ($procumentOrder->approved == -1 && $procumentOrder->WO_amendYN != -1 && $isAmendAccess != 1) {
            return $this->sendError('This Purchase Order fully approved. You cannot edit.', 500);
        }

        //checking segment is active

        $segments = SegmentMaster::where("serviceLineSystemID", $input['serviceLineSystemID'])
            ->where('companySystemID', $input['companySystemID'])
            ->where('isActive', 1)
            ->first();

        if (empty($segments)) {
            return $this->sendError('Selected segment is not active. Please select an active segment');
        }

        $purchaseOrderID = $input['purchaseOrderID'];

        $segment = SegmentMaster::where('serviceLineSystemID', $input['serviceLineSystemID'])->first();
        if ($segment) {
            $procumentOrderUpdate->serviceLine = $segment->ServiceLineCode;
        }

        foreach ($input as $key => $value) {
            $procumentOrderUpdate->$key = $value;
        }

        $procumentOrderUpdate->modifiedPc = gethostname();
        $procumentOrderUpdate->modifiedUser = $user->employee['empID'];
        $procumentOrderUpdate->modifiedUserSystemID = $user->employee['employeeSystemID'];

        if ($input['partiallyGRVAllowed']) {
            $procumentOrderUpdate->partiallyGRVAllowed = -1;
        } else {
            $procumentOrderUpdate->partiallyGRVAllowed = 0;
        }
        if ($input['logisticsAvailable']) {
            $procumentOrderUpdate->logisticsAvailable = -1;
        } else {
            $procumentOrderUpdate->logisticsAvailable = 0;
        }

        // finding supplier default currencyID
        $supplierCurrency = SupplierCurrency::where('supplierCodeSystem', $input['supplierID'])
            ->where('isDefault', -1)
            ->first();

        if ($supplierCurrency) {
            $procumentOrderUpdate->supplierDefaultCurrencyID = $supplierCurrency->currencyID;
            $procumentOrderUpdate->supplierTransactionER = 1;
        }

        //getting total sum of PO detail Amount
        $poMasterSum = PurchaseOrderDetails::select(DB::raw('COALESCE(SUM(netAmount),0) as masterTotalSum'))
            ->where('purchaseOrderMasterID', $input['purchaseOrderID'])
            ->first();

        $poMasterSumDeducted = ($poMasterSum['masterTotalSum'] - $input['poDiscountAmount']) + $input['VATAmount'];

        $input['poTotalSupplierTransactionCurrency'] = $poMasterSum['masterTotalSum'];

        $currencyConversionMaster = \Helper::currencyConversion($input["companySystemID"], $input['supplierTransactionCurrencyID'], $input['supplierTransactionCurrencyID'], $poMasterSumDeducted);

        $procumentOrderUpdate->poTotalComRptCurrency = round($currencyConversionMaster['reportingAmount'], 8);
        $procumentOrderUpdate->poTotalLocalCurrency = round($currencyConversionMaster['localAmount'], 8);
        $procumentOrderUpdate->poTotalSupplierTransactionCurrency = round($poMasterSumDeducted, 8);
        $procumentOrderUpdate->companyReportingER = round($currencyConversionMaster['trasToRptER'], 8);
        $procumentOrderUpdate->localCurrencyER = round($currencyConversionMaster['trasToLocER'], 8);


        // calculating total Supplier Default currency

        $currencyConversionMaster = \Helper::currencyConversion($input["companySystemID"], $supplierCurrency->currencyID, $input['supplierTransactionCurrencyID'], $poMasterSumDeducted);

        $procumentOrderUpdate->poTotalSupplierDefaultCurrency = round($currencyConversionMaster['documentAmount'], 8);


        $supplier = SupplierMaster::where('supplierCodeSystem', $input['supplierID'])->first();
        if ($supplier) {

            $procumentOrderUpdate->supplierPrimaryCode = $supplier->primarySupplierCode;
            $procumentOrderUpdate->supplierName = $supplier->supplierName;
            $procumentOrderUpdate->supplierAddress = $supplier->address;
            $procumentOrderUpdate->supplierTelephone = $supplier->telephone;
            $procumentOrderUpdate->supplierFax = $supplier->fax;
            $procumentOrderUpdate->supplierEmail = $supplier->supEmail;
            $procumentOrderUpdate->creditPeriod = $supplier->creditPeriod;
            //$procumentOrderUpdate->supplierVATEligible = $supplier->vatEligible;
        }

        $supplierAssignedDetai = SupplierAssigned::where('supplierCodeSytem', $input['supplierID'])
            ->where('companySystemID', $input['companySystemID'])
            ->first();

        if ($supplierAssignedDetai) {
            $procumentOrderUpdate->supplierVATEligible = $supplierAssignedDetai->vatEligible;
            //$input['VATPercentage'] = $supplierAssignedDetai->vatPercentage;
        }

        if ($input['VATAmount'] > 0) {

            $currencyConversionVatAmount = \Helper::currencyConversion($input['companySystemID'], $input['supplierTransactionCurrencyID'], $input['supplierTransactionCurrencyID'], $input['VATAmount']);

            $procumentOrderUpdate->VATAmountLocal = round($currencyConversionVatAmount['localAmount'], 8);
            $procumentOrderUpdate->VATAmountRpt = round($currencyConversionVatAmount['reportingAmount'], 8);
        } else {
            $procumentOrderUpdate->VATAmountLocal = 0;
            $procumentOrderUpdate->VATAmountRpt = 0;
        }

        $company = Company::where('companySystemID', $input['companySystemID'])->first();
        if ($company) {
            $procumentOrderUpdate->vatRegisteredYN = $company->vatRegisteredYN;
        }

        if (($procumentOrder->poConfirmedYN == 0 && $input['poConfirmedYN'] == 1) || $isAmendAccess == 1) {

            $poDetailExist = PurchaseOrderDetails::select(DB::raw('purchaseOrderDetailsID'))
                ->where('purchaseOrderMasterID', $input['purchaseOrderID'])
                ->first();

            if (empty($poDetailExist)) {
                return $this->sendError('PO Document cannot confirm without details');
            }

            $checkQuantity = PurchaseOrderDetails::where('purchaseOrderMasterID', $id)
                ->where('noQty', '<', 1)
                ->count();

            if ($checkQuantity > 0) {
                return $this->sendError('Every Item should have at least one minimum Qty Requested', 500);
            }

            //checking atleast one po payment terms should exist
            $PoPaymentTerms = PoPaymentTerms::where('poID', $input['purchaseOrderID'])
                ->first();

            if(empty($PoPaymentTerms)){
                return $this->sendError('PO should have at least one payment term');
            }

            //po payment terms exist
            $PoPaymentTerms = PoPaymentTerms::where('poID', $input['purchaseOrderID'])
                ->where('LCPaymentYN', 2)
                ->where('isRequested', 0)
                ->first();

            if (!empty($PoPaymentTerms)) {
                return $this->sendError('Advance Payment Request is pending');
            }

            $poAdvancePaymentType = PoPaymentTerms::where("poID", $input['purchaseOrderID'])
                ->get();

            $detailSum = PurchaseOrderDetails::select(DB::raw('sum(netAmount) as total'))
                ->where('purchaseOrderMasterID', $input['purchaseOrderID'])
                ->first();

            if (!empty($poAdvancePaymentType)) {
                foreach ($poAdvancePaymentType as $payment) {
                    $paymentPercentageAmount = ($detailSum['total'] * $payment['comPercentage']) / 100;
                    if ($payment['comAmount'] != $paymentPercentageAmount) {
                        return $this->sendError('Payment terms is not matching with the PO total');
                    }
                }
            }

            unset($input['poConfirmedYN']);
            unset($input['poConfirmedByEmpSystemID']);
            unset($input['poConfirmedByEmpID']);
            unset($input['poConfirmedByName']);
            unset($input['poConfirmedDate']);


            if ($isAmendAccess != 1) {
                $params = array('autoID' => $id, 'company' => $input["companySystemID"], 'document' => $input["documentSystemID"], 'segment' => $input["serviceLineSystemID"], 'category' => $input["financeCategory"], 'amount' => $poMasterSumDeducted);
                $confirm = \Helper::confirmDocument($params);
                if (!$confirm["success"]) {
                    return $this->sendError($confirm["message"]);
                }
            }
        }
        //updating PO Master
        /*        $procumentOrderUpdate->poDiscountAmount = $input['poDiscountAmount'];
                $procumentOrderUpdate->poDiscountPercentage = $input['poDiscountPercentage'];
                $procumentOrderUpdate->VATPercentage = $input['VATPercentage'];
                $procumentOrderUpdate->VATAmount = $input['VATAmount'];*/

        //$procumentOrder = $this->procumentOrderRepository->update($input, $id);
        $updateDetailDiscount = PurchaseOrderDetails::where('purchaseOrderMasterID', $purchaseOrderID)
            ->get();

        // calculate total discount
        if ($input['poDiscountAmount'] > 0) {

            if (!empty($updateDetailDiscount)) {

                foreach ($updateDetailDiscount as $itemDiscont) {

                    $calculateItemDiscount = (($itemDiscont['netAmount'] - (($input['poDiscountAmount'] / $input['poTotalSupplierTransactionCurrency']) * $itemDiscont['netAmount'])) / $itemDiscont['noQty']);

                    $currencyConversion = \Helper::currencyConversion($itemDiscont['companySystemID'], $input['supplierTransactionCurrencyID'], $input['supplierTransactionCurrencyID'], $calculateItemDiscount);

                    //$detail['netAmount'] = $calculateItemDiscount * $itemDiscont['noQty'];

                    PurchaseOrderDetails::where('purchaseOrderDetailsID', $itemDiscont['purchaseOrderDetailsID'])
                        ->update([
                            'GRVcostPerUnitLocalCur' => round($currencyConversion['localAmount'], 8),
                            'GRVcostPerUnitSupTransCur' => round($calculateItemDiscount, 8),
                            'GRVcostPerUnitComRptCur' => round($currencyConversion['reportingAmount'], 8),
                            'purchaseRetcostPerUnitLocalCur' => round($currencyConversion['localAmount'], 8),
                            'purchaseRetcostPerUnitTranCur' => round($calculateItemDiscount, 8),
                            'purchaseRetcostPerUnitRptCur' => round($currencyConversion['reportingAmount'], 8),
                        ]);
                }
            }
        }

        // calculate total Tax for item if
        if ($input['supplierVATEligible'] == 1 && $input['vatRegisteredYN'] == 0) {

            if (!empty($updateDetailDiscount)) {
                foreach ($updateDetailDiscount as $itemDiscont) {

                    if ($input['poDiscountAmount'] > 0) {

                        $calculateItemDiscount = (($itemDiscont['netAmount'] - (($input['poDiscountAmount'] / $input['poTotalSupplierTransactionCurrency']) * $itemDiscont['netAmount'])) / $itemDiscont['noQty']);
                    } else {
                        $calculateItemDiscount = $itemDiscont['unitCost'] - $itemDiscont['discountAmount'];
                    }
                    $calculateItemTax = (($input['VATPercentage'] / 100) * $calculateItemDiscount) + $calculateItemDiscount;

                    $currencyConversion = \Helper::currencyConversion($itemDiscont['companySystemID'], $input['supplierTransactionCurrencyID']
                        , $input['supplierTransactionCurrencyID'], $calculateItemTax);

                    //$detail['netAmount'] = $calculateItemTax * $itemDiscont['noQty'];

                    $vatLineAmount = ($calculateItemTax - $calculateItemDiscount);

                    $currencyConversionForLineAmount = \Helper::currencyConversion($itemDiscont['companySystemID'], $input['supplierTransactionCurrencyID'], $input['supplierTransactionCurrencyID'], $vatLineAmount);

                    PurchaseOrderDetails::where('purchaseOrderDetailsID', $itemDiscont['purchaseOrderDetailsID'])
                        ->update([
                            'GRVcostPerUnitLocalCur' => round($currencyConversion['localAmount'], 8),
                            'GRVcostPerUnitSupTransCur' => round($calculateItemTax, 8),
                            'GRVcostPerUnitComRptCur' => round($currencyConversion['reportingAmount'], 8),
                            'purchaseRetcostPerUnitLocalCur' => round($currencyConversion['localAmount'], 8),
                            'purchaseRetcostPerUnitTranCur' => round($calculateItemTax, 8),
                            'purchaseRetcostPerUnitRptCur' => round($currencyConversion['reportingAmount'], 8),
                            'VATPercentage' => round($procumentOrder->VATPercentage, 8),
                            'VATAmount' => round($vatLineAmount, 8),
                            'VATAmountLocal' => round($currencyConversionForLineAmount['localAmount'], 8),
                            'VATAmountRpt' => round($currencyConversionForLineAmount['reportingAmount'], 8)
                        ]);
                }
            }

        }
        $procumentOrderUpdate->save();

        if ($procumentOrder->WO_amendYN == -1 && $isAmendAccess == 1 && $procumentOrder->WO_amendRequestedByEmpID == $employee->empID) {

            $employee = \Helper::getEmployeeInfo();
            $procumentOrderUpdate->WO_amendYN = 0;
            $procumentOrderUpdate->WO_confirmedYN = 1;
            // $procumentOrderUpdate->WO_amendRequestedByEmpID = null;
            // $procumentOrderUpdate->WO_amendRequestedByEmpSystemID = null;
            // $procumentOrderUpdate->WO_amendRequestedDate = null;
            $procumentOrderUpdate->WO_confirmedDate = now();
            $procumentOrderUpdate->WO_confirmedByEmpID = $employee->empID;

            $procumentOrderUpdate->save();


            if ($procumentOrderUpdate->poTotalSupplierTransactionCurrency != $oldPoTotalSupplierTransactionCurrency) {
                $emails = array();

                $document = DocumentMaster::where('documentSystemID', $procumentOrder->documentSystemID)->first();

                $cancelDocNameBody = $document->documentDescription . ' <b>' . $procumentOrder->purchaseOrderCode . '</b>';
                $cancelDocNameSubject = $document->documentDescription . ' ' . $procumentOrder->purchaseOrderCode;

                $body = '<p>' . $cancelDocNameBody . ' has been changed by ' . $employee->empName . '. Current total amount of the order is ' . $procumentOrderUpdate->poTotalSupplierTransactionCurrency . '.Previous total amount was ' . $oldPoTotalSupplierTransactionCurrency . '.';
                $subject = $cancelDocNameSubject . ' has been changed';

                if ($procumentOrder->poConfirmedYN == 1) {
                    $emails[] = array('empSystemID' => $procumentOrder->poConfirmedByEmpSystemID,
                        'companySystemID' => $procumentOrder->companySystemID,
                        'docSystemID' => $procumentOrder->documentSystemID,
                        'alertMessage' => $subject,
                        'emailAlertMessage' => $body,
                        'docSystemCode' => $procumentOrder->purchaseOrderID);
                }

                $documentApproval = DocumentApproved::where('companySystemID', $procumentOrder->companySystemID)
                    ->where('documentSystemCode', $procumentOrder->purchaseOrderID)
                    ->where('documentSystemID', $procumentOrder->documentSystemID)
                    ->get();

                foreach ($documentApproval as $da) {
                    if ($da->approvedYN == -1) {
                        $emails[] = array('empSystemID' => $da->employeeSystemID,
                            'companySystemID' => $procumentOrder->companySystemID,
                            'docSystemID' => $procumentOrder->documentSystemID,
                            'alertMessage' => $subject,
                            'emailAlertMessage' => $body,
                            'docSystemCode' => $procumentOrder->purchaseOrderID);
                    }
                }

                $sendEmail = \Email::sendEmail($emails);
                if (!$sendEmail["success"]) {
                    return $this->sendError($sendEmail["message"], 500);
                }

            }

        }


        return $this->sendResponse($procumentOrder->toArray(), 'Procurement Order updated successfully');
    }

    /**
     * Remove the specified ProcumentOrder from storage.
     * DELETE /procumentOrders/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var ProcumentOrder $procumentOrder */
        $procumentOrder = $this->procumentOrderRepository->findWithoutFail($id);

        if (empty($procumentOrder)) {
            return $this->sendError('Procurement Order not found');
        }

        $procumentOrder->delete();

        return $this->sendResponse($id, 'Procurement Order deleted successfully');
    }

    public function getProcumentOrderByDocumentType(Request $request)
    {
        $input = $request->all();
        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $procumentOrders = ProcumentOrder::where('companySystemID', $input['companyId']);
        $procumentOrders->where('documentSystemID', $input['documentId']);
        if ($input['poType_N'] != 1) {
            $procumentOrders->where('poType_N', $input['poType_N']);
        }
        $procumentOrders->with(['created_by' => function ($query) {
            //$query->select(['empName']);
        }, 'location' => function ($query) {
        }, 'supplier' => function ($query) {
        }, 'currency' => function ($query) {
        }, 'fcategory' => function ($query) {
        }, 'segment' => function ($query) {
        }]);

        if (array_key_exists('serviceLineSystemID', $input)) {
            $procumentOrders->where('serviceLineSystemID', $input['serviceLineSystemID']);
        }

        if (array_key_exists('poCancelledYN', $input)) {
            if ($input['poCancelledYN'] == 0 || $input['poCancelledYN'] == -1) {
                $procumentOrders->where('poCancelledYN', $input['poCancelledYN']);
            }
        }

        if (array_key_exists('poConfirmedYN', $input)) {
            if ($input['poConfirmedYN'] == 0 || $input['poConfirmedYN'] == 1) {
                $procumentOrders->where('poConfirmedYN', $input['poConfirmedYN']);
            }
        }

        if (array_key_exists('approved', $input)) {
            if ($input['approved'] == 0 || $input['approved'] == -1) {
                $procumentOrders->where('approved', $input['approved']);
            }
        }

        if (array_key_exists('grvRecieved', $input)) {
            if ($input['grvRecieved'] == 0 || $input['grvRecieved'] == 1 || $input['grvRecieved'] == 2) {
                $procumentOrders->where('grvRecieved', $input['grvRecieved']);
            }
        }

        if (array_key_exists('invoicedBooked', $input)) {
            if ($input['invoicedBooked'] == 0 || $input['invoicedBooked'] == 1 || $input['invoicedBooked'] == 2) {
                $procumentOrders->where('invoicedBooked', $input['invoicedBooked']);
            }
        }

        if (array_key_exists('month', $input)) {
            $procumentOrders->whereMonth('createdDateTime', '=', $input['month']);
        }

        if (array_key_exists('year', $input)) {
            $procumentOrders->whereYear('createdDateTime', '=', $input['year']);
        }

        $procumentOrders = $procumentOrders->select(
            ['erp_purchaseordermaster.purchaseOrderID',
                'erp_purchaseordermaster.purchaseOrderCode',
                'erp_purchaseordermaster.documentSystemID',
                'erp_purchaseordermaster.budgetYear',
                'erp_purchaseordermaster.createdDateTime',
                'erp_purchaseordermaster.createdUserSystemID',
                'erp_purchaseordermaster.narration',
                'erp_purchaseordermaster.poLocation',
                'erp_purchaseordermaster.manuallyClosed',
                'erp_purchaseordermaster.poCancelledYN',
                'erp_purchaseordermaster.poConfirmedYN',
                'erp_purchaseordermaster.poConfirmedDate',
                'erp_purchaseordermaster.approved',
                'erp_purchaseordermaster.approvedDate',
                'erp_purchaseordermaster.timesReferred',
                'erp_purchaseordermaster.serviceLineSystemID',
                'erp_purchaseordermaster.supplierID',
                'erp_purchaseordermaster.supplierName',
                'erp_purchaseordermaster.expectedDeliveryDate',
                'erp_purchaseordermaster.referenceNumber',
                'erp_purchaseordermaster.supplierTransactionCurrencyID',
                'erp_purchaseordermaster.poTotalSupplierTransactionCurrency',
                'erp_purchaseordermaster.financeCategory',
                'erp_purchaseordermaster.grvRecieved',
                'erp_purchaseordermaster.invoicedBooked',
            ]);

        $search = $request->input('search.value');
        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $procumentOrders = $procumentOrders->where(function ($query) use ($search) {
                $query->where('purchaseOrderCode', 'LIKE', "%{$search}%")
                    ->orWhere('narration', 'LIKE', "%{$search}%")
                    ->orWhere('supplierName', 'LIKE', "%{$search}%");
            });
        }


        $historyPolicy = CompanyPolicyMaster::where('companyPolicyCategoryID', 29)
            ->where('companySystemID', $input['companyId'])->first();

        $policy = 0;

        if (!empty($historyPolicy)) {
            $policy = $historyPolicy->isYesNO;
        }

        return \DataTables::eloquent($procumentOrders)
            ->addColumn('Actions', $policy)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('purchaseOrderID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
        ///return $this->sendResponse($supplierMasters->toArray(), 'Supplier Masters retrieved successfully');*/
    }


    public function getProcumentOrderFormData(Request $request)
    {

        $companyId = $request['companyId'];

        $purchaseOrderID = $request['purchaseOrderID'];

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

        $years = ProcumentOrder::select(DB::raw("YEAR(createdDateTime) as year"))
            ->whereNotNull('createdDateTime')
            ->groupby('year')
            ->orderby('year', 'desc')
            ->get();

        $supplier = SupplierAssigned::select(DB::raw("supplierCodeSytem,CONCAT(primarySupplierCode, ' | ' ,supplierName) as supplierName"))
            ->where('companySystemID', $companyId)
            ->where('isActive', 1)
            ->where('isAssigned', -1)
            ->get();

        $currencies = CurrencyMaster::select(DB::raw("currencyID,CONCAT(CurrencyCode, ' | ' ,CurrencyName) as CurrencyName"))
            ->get();

        $detailSum = PurchaseOrderDetails::select(DB::raw('sum(netAmount) as total'))
            ->where('purchaseOrderMasterID', $purchaseOrderID)
            ->get();

        $financeCategories = FinanceItemCategoryMaster::all();

        $locations = Location::all();

        $financialYears = array(array('value' => intval(date("Y")), 'label' => date("Y")),
            array('value' => intval(date("Y", strtotime("-1 year"))), 'label' => date("Y", strtotime("-1 year"))));

        $checkBudget = CompanyPolicyMaster::where('companyPolicyCategoryID', 17)
            ->where('companySystemID', $companyId)
            ->first();

        $allowFinanceCategory = CompanyPolicyMaster::where('companyPolicyCategoryID', 20)
            ->where('companySystemID', $companyId)
            ->first();

        $allowPRinPO = CompanyPolicyMaster::where('companyPolicyCategoryID', 29)
            ->where('companySystemID', $companyId)
            ->first();

        $addressTypeShippings = DB::table("erp_address")
            ->select('addressID', 'addressTypeDescription')
            ->join("erp_addresstype", "erp_addresstype.addressTypeID", "=", "erp_address.addressTypeID")
            ->where("erp_address.addressTypeID", "1")
            ->where("companySystemID", $companyId)
            ->get();

        $addressTypeInvoice = DB::table("erp_address")
            ->select('addressID', 'addressTypeDescription')
            ->join("erp_addresstype", "erp_addresstype.addressTypeID", "=", "erp_address.addressTypeID")
            ->where("erp_address.addressTypeID", "2")
            ->where("companySystemID", $companyId)
            ->get();

        $addressTypeSold = DB::table("erp_address")
            ->select('addressID', 'addressTypeDescription')
            ->join("erp_addresstype", "erp_addresstype.addressTypeID", "=", "erp_address.addressTypeID")
            ->where("erp_address.addressTypeID", "3")
            ->where("companySystemID", $companyId)
            ->get();

        $PoPaymentTermTypes = PoPaymentTermTypes::all();

        if (!empty($purchaseOrderID)) {
            $checkDetailExist = PurchaseOrderDetails::where('purchaseOrderMasterID', $purchaseOrderID)
                ->where('companySystemID', $companyId)
                ->first();

            if (!empty($checkDetailExist)) {
                $detail = 1;
            }
        }

        $conditions = array('checkBudget' => 0, 'allowFinanceCategory' => 0, 'detailExist' => 0, 'pullPRPolicy' => 0);

        $grvRecieved = array(['id' => '0', 'value' => 'Not Received'], ['id' => '1', 'value' => 'Partial Received'], ['id' => '2', 'value' => 'Fully Received']);

        $invoiceBooked = array(['id' => '0', 'value' => 'Not Invoiced'], ['id' => '1', 'value' => 'Partial Invoiced'], ['id' => '2', 'value' => 'Fully Invoiced']);

        if ($checkBudget) {
            $conditions['checkBudget'] = $checkBudget->isYesNO;
        }

        if ($allowFinanceCategory) {
            $conditions['allowFinanceCategory'] = $allowFinanceCategory->isYesNO;
        }

        if ($allowPRinPO) {
            $conditions['pullPRPolicy'] = $allowPRinPO->isYesNO;
        }

        if (!empty($purchaseOrderID)) {
            $checkDetailExist = PurchaseOrderDetails::where('purchaseOrderMasterID', $purchaseOrderID)
                ->where('companySystemID', $companyId)
                ->first();

            if (!empty($checkDetailExist)) {
                $conditions['detailExist'] = 1;
            }
        }

        $output = array('segments' => $segments,
            'yesNoSelection' => $yesNoSelection,
            'yesNoSelectionForMinus' => $yesNoSelectionForMinus,
            'month' => $month,
            'years' => $years,
            'currencies' => $currencies,
            'financeCategories' => $financeCategories,
            'locations' => $locations,
            'financialYears' => $financialYears,
            'conditions' => $conditions,
            'suppliers' => $supplier,
            'addresstypeShippings' => $addressTypeShippings,
            'addresstypeinvoice' => $addressTypeInvoice,
            'addresstypesold' => $addressTypeSold,
            'paymentterms' => $PoPaymentTermTypes,
            'detailSum' => $detailSum,
            'grvRecieved' => $grvRecieved,
            'invoiceBooked' => $invoiceBooked
        );

        return $this->sendResponse($output, 'Record retrieved successfully');
    }

    public function getItemsOptionForProcumentOrder(Request $request)
    {
        $input = $request->all();

        $companyId = $input['companyId'];
        $purchaseOrderID = $input['purchaseOrderID'];

        $policy = 1;

        $financeCategoryId = 0;

        $allowFinanceCategory = CompanyPolicyMaster::where('companyPolicyCategoryID', 20)
            ->where('companySystemID', $companyId)
            ->first();

        if ($allowFinanceCategory) {
            $policy = $allowFinanceCategory->isYesNO;

            if ($policy == 0) {
                $purchaseOrderMaster = ProcumentOrder::where('purchaseOrderID', $purchaseOrderID)->first();

                if ($purchaseOrderMaster) {
                    $financeCategoryId = $purchaseOrderMaster->financeCategory;
                }
            }
        }

        $items = ItemAssigned::where('companySystemID', $companyId);


        if ($policy == 0 && $financeCategoryId != 0) {
            $items = $items->where('financeCategoryMaster', $financeCategoryId);
        }

        if (array_key_exists('search', $input)) {

            $search = $input['search'];

            $items = $items->where(function ($query) use ($search) {
                $query->where('itemPrimaryCode', 'LIKE', "%{$search}%")
                    ->orWhere('itemDescription', 'LIKE', "%{$search}%");
            });
        }

        $items = $items
            ->take(20)
            ->get();

        return $this->sendResponse($items->toArray(), 'Data retrieved successfully');
    }

    public function getShippingAndInvoiceDetails(Request $request)
    {
        $input = $request->all();

        $companyId = $input['companyId'];
        $addressID = $input['addressID'];

        $erpAddressDetails = ErpAddress::where('addressID', $addressID)
            ->where('companySystemID', $companyId)
            ->first();

        return $this->sendResponse($erpAddressDetails->toArray(), 'Data retrieved successfully');

    }

    public function procumentOrderDetailTotal(Request $request)
    {
        $input = $request->all();

        $purchaseOrderID = $input['purchaseOrderID'];

        $detailSum = PurchaseOrderDetails::select(DB::raw('sum(netAmount) as total'))
            ->where('purchaseOrderMasterID', $purchaseOrderID)
            ->get();

        return $this->sendResponse($detailSum->toArray(), 'Data retrieved successfully');
    }

    public function getProcurementOrderRecord(Request $request)
    {
        $output = ProcumentOrder::where('purchaseOrderID', $request->purchaseOrderID)->with(['detail' => function ($query) {
            $query->with('unit');
        }, 'approved' => function ($query) {
            $query->with('employee');
            $query->whereIN('documentSystemID', [2, 5, 52]);
        }, 'suppliercontact' => function ($query) {
            $query->where('isDefault', -1);
        }, 'company', 'transactioncurrency', 'companydocumentattachment'])->first();
        return $this->sendResponse($output, 'Data retrieved successfully');

    }

    public function getPOMasterApproval(Request $request)
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
            ->where('documentSystemID', 2)
            ->first();

        $poMasters = DB::table('erp_documentapproved')->select(
            'erp_purchaseordermaster.purchaseOrderID',
            'erp_purchaseordermaster.purchaseOrderCode',
            'erp_purchaseordermaster.documentSystemID',
            'erp_purchaseordermaster.referenceNumber',
            'erp_purchaseordermaster.expectedDeliveryDate',
            'erp_purchaseordermaster.supplierPrimaryCode',
            'erp_purchaseordermaster.supplierName',
            'erp_purchaseordermaster.narration',
            'erp_purchaseordermaster.serviceLine',
            'erp_purchaseordermaster.createdDateTime',
            'erp_purchaseordermaster.poConfirmedDate',
            'erp_purchaseordermaster.poTotalSupplierTransactionCurrency',
            'erp_documentapproved.documentApprovedID',
            'erp_documentapproved.rollLevelOrder',
            'currencymaster.CurrencyCode',
            'approvalLevelID',
            'documentSystemCode'
        )->join('employeesdepartments', function ($query) use ($companyID, $empID, $serviceLinePolicy) {
            $query->on('erp_documentapproved.approvalGroupID', '=', 'employeesdepartments.employeeGroupID')
                ->on('erp_documentapproved.documentSystemID', '=', 'employeesdepartments.documentSystemID')
                ->on('erp_documentapproved.companySystemID', '=', 'employeesdepartments.companySystemID');
            if ($serviceLinePolicy && $serviceLinePolicy->isServiceLineApproval == -1) {
                $query->on('erp_documentapproved.serviceLineSystemID', '=', 'employeesdepartments.ServiceLineSystemID');
            }
            $query->whereIn('employeesdepartments.documentSystemID', [2, 5, 52])
                ->where('employeesdepartments.companySystemID', $companyID)
                ->where('employeesdepartments.employeeSystemID', $empID);
        })->join('erp_purchaseordermaster', function ($query) use ($companyID, $empID) {
            $query->on('erp_documentapproved.documentSystemCode', '=', 'purchaseOrderID')
                ->on('erp_documentapproved.rollLevelOrder', '=', 'RollLevForApp_curr')
                ->where('erp_purchaseordermaster.companySystemID', $companyID)
                ->where('erp_purchaseordermaster.approved', 0)
                ->where('erp_purchaseordermaster.poConfirmedYN', 1);
        })->where('erp_documentapproved.approvedYN', 0)
            ->join('currencymaster', 'supplierTransactionCurrencyID', '=', 'currencyID')
            ->where('erp_documentapproved.rejectedYN', 0)
            ->whereIn('erp_documentapproved.documentSystemID', [2, 5, 52])
            ->where('erp_documentapproved.companySystemID', $companyID);

        return \DataTables::of($poMasters)
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

    public function approveProcurementOrder(Request $request)
    {
        $approve = \Helper::approveDocument($request);
        if (!$approve["success"]) {
            return $this->sendError($approve["message"]);
        } else {
            return $this->sendResponse(array(), $approve["message"]);
        }

    }

    public function rejectProcurementOrder(Request $request)
    {
        $reject = \Helper::rejectDocument($request);
        if (!$reject["success"]) {
            return $this->sendError($reject["message"]);
        } else {
            return $this->sendResponse(array(), $reject["message"]);
        }

    }

    public function getGoodReceivedNoteDetailsForPO(Request $request)
    {
        $input = $request->all();

        $purchaseOrderID = $input['purchaseOrderID'];

        $detail = DB::select('SELECT erp_grvdetails.grvAutoID,erp_grvdetails.companyID,erp_grvdetails.purchaseOrderMastertID,erp_grvmaster.grvDate,erp_grvmaster.grvPrimaryCode,erp_grvmaster.grvDoRefNo,erp_grvdetails.itemPrimaryCode,
erp_grvdetails.itemDescription,warehousemaster.wareHouseDescription,erp_grvmaster.grvNarration,erp_grvmaster.supplierName,erp_grvdetails.poQty AS POQty,erp_grvdetails.noQty,erp_grvmaster.approved,erp_grvmaster.grvConfirmedYN,currencymaster.CurrencyCode,erp_grvdetails.GRVcostPerUnitSupTransCur,erp_grvdetails.unitCost,erp_grvdetails.GRVcostPerUnitSupTransCur*erp_grvdetails.noQty AS total,erp_grvdetails.GRVcostPerUnitSupTransCur*erp_grvdetails.noQty AS totalCost FROM erp_grvdetails INNER JOIN erp_grvmaster ON erp_grvdetails.grvAutoID = erp_grvmaster.grvAutoID INNER JOIN warehousemaster ON erp_grvmaster.grvLocation = warehousemaster.wareHouseSystemCode INNER JOIN currencymaster ON erp_grvdetails.supplierItemCurrencyID = currencymaster.currencyID WHERE purchaseOrderMastertID = ' . $purchaseOrderID . ' ');

        return $this->sendResponse($detail, 'Details retrieved successfully');

    }

    function getInvoiceDetailsForPO(Request $request)
    {
        $input = $request->all();

        $purchaseOrderID = $input['purchaseOrderID'];

        $detail = DB::select('SELECT erp_bookinvsuppmaster.bookingSuppMasInvAutoID,erp_bookinvsuppmaster.companyID,erp_bookinvsuppdet.purchaseOrderID,erp_bookinvsuppmaster.documentID,erp_grvmaster.grvPrimaryCode,erp_bookinvsuppmaster.bookingInvCode,erp_bookinvsuppmaster.bookingDate,erp_bookinvsuppmaster.comments,erp_bookinvsuppmaster.supplierInvoiceNo,erp_bookinvsuppmaster.confirmedYN,erp_bookinvsuppmaster.confirmedByName,erp_bookinvsuppmaster.approved,currencymaster.CurrencyCode,erp_bookinvsuppdet.totTransactionAmount FROM erp_bookinvsuppmaster INNER JOIN erp_bookinvsuppdet ON erp_bookinvsuppmaster.bookingSuppMasInvAutoID = erp_bookinvsuppdet.bookingSuppMasInvAutoID LEFT JOIN currencymaster ON erp_bookinvsuppmaster.supplierTransactionCurrencyID = currencymaster.currencyID LEFT JOIN erp_grvmaster ON erp_bookinvsuppdet.grvAutoID = erp_grvmaster.grvAutoID WHERE purchaseOrderID = ' . $purchaseOrderID . ' ');

        return $this->sendResponse($detail, 'Details retrieved successfully');
    }

    public function getProcumentOrderAllAmendments(Request $request)
    {
        $input = $request->all();
        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $procumentOrders = ProcumentOrder::where('companySystemID', $input['companyId'])
            ->where('poCancelledYN', 0)
            ->with(['created_by' => function ($query) {
                //$query->select(['empName']);
            }, 'location' => function ($query) {
            }, 'supplier' => function ($query) {
            }, 'currency' => function ($query) {
            }, 'fcategory' => function ($query) {
            }, 'segment' => function ($query) {
            }]);

        if (array_key_exists('serviceLineSystemID', $input)) {
            $procumentOrders->where('serviceLineSystemID', $input['serviceLineSystemID']);
        }

        if (array_key_exists('grvRecieved', $input)) {
            if ($input['grvRecieved'] == 0 || $input['grvRecieved'] == 1 || $input['grvRecieved'] == 2) {
                $procumentOrders->where('grvRecieved', $input['grvRecieved']);
            }
        }

        if (array_key_exists('invoicedBooked', $input)) {
            if ($input['invoicedBooked'] == 0 || $input['invoicedBooked'] == 1 || $input['invoicedBooked'] == 2) {
                $procumentOrders->where('invoicedBooked', $input['invoicedBooked']);
            }
        }

        if (array_key_exists('month', $input)) {
            $procumentOrders->whereMonth('createdDateTime', '=', $input['month']);
        }

        if (array_key_exists('year', $input)) {
            $procumentOrders->whereYear('createdDateTime', '=', $input['year']);
        }

        $procumentOrders = $procumentOrders->select(
            ['erp_purchaseordermaster.purchaseOrderID',
                'erp_purchaseordermaster.purchaseOrderCode',
                'erp_purchaseordermaster.documentSystemID',
                'erp_purchaseordermaster.budgetYear',
                'erp_purchaseordermaster.createdDateTime',
                'erp_purchaseordermaster.createdUserSystemID',
                'erp_purchaseordermaster.narration',
                'erp_purchaseordermaster.poLocation',
                'erp_purchaseordermaster.poCancelledYN',
                'erp_purchaseordermaster.poConfirmedYN',
                'erp_purchaseordermaster.manuallyClosed',
                'erp_purchaseordermaster.poConfirmedDate',
                'erp_purchaseordermaster.approved',
                'erp_purchaseordermaster.approvedDate',
                'erp_purchaseordermaster.timesReferred',
                'erp_purchaseordermaster.serviceLineSystemID',
                'erp_purchaseordermaster.supplierID',
                'erp_purchaseordermaster.supplierName',
                'erp_purchaseordermaster.expectedDeliveryDate',
                'erp_purchaseordermaster.referenceNumber',
                'erp_purchaseordermaster.supplierTransactionCurrencyID',
                'erp_purchaseordermaster.poTotalSupplierTransactionCurrency',
                'erp_purchaseordermaster.financeCategory',
                'erp_purchaseordermaster.grvRecieved',
                'erp_purchaseordermaster.invoicedBooked',
                'erp_purchaseordermaster.documentSystemID',
                'erp_purchaseordermaster.poType_N',
            ]);

        $search = $request->input('search.value');
        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $procumentOrders = $procumentOrders->where(function ($query) use ($search) {
                $query->where('purchaseOrderCode', 'LIKE', "%{$search}%")
                    ->orWhere('narration', 'LIKE', "%{$search}%")
                    ->orWhere('supplierName', 'LIKE', "%{$search}%");
            });
        }

        return \DataTables::eloquent($procumentOrders)
            ->addColumn('Actions', 'Actions', "Actions")
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('purchaseOrderID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
        ///return $this->sendResponse($supplierMasters->toArray(), 'Supplier Masters retrieved successfully');*/
    }

    public function poCheckDetailExistinGrv(Request $request)
    {
        $purchaseOrderID = $request['purchaseOrderID'];
        $type = $request['type'];

        if ($type == 1) {
            $comment = 'cancel';
        } else {
            $comment = 'revert';
        }

        $purchaseOrder = ProcumentOrder::where('purchaseOrderID', $purchaseOrderID)
            ->first();

        if (empty($purchaseOrder)) {
            return $this->sendError('Purchase Order not found');
        }

        $detailExistGRV = GRVDetails::where('purchaseOrderMastertID', $purchaseOrderID)
            ->first();

        if (!empty($detailExistGRV)) {
            if ($type == 1) {
                return $this->sendError('Cannot cancel, GRV is created for this PO');
            } else {
                return $this->sendError('Cannot revert it back to amend. GRV is created for this PO');
            }

        }

        $detailExistAPD = AdvancePaymentDetails::where('purchaseOrderID', $purchaseOrderID)
            ->first();

        if (!empty($detailExistAPD)) {
            return $this->sendError('Cannot ' . $comment . '. Advance Payment is created for this PO');
        }

        return $this->sendResponse($purchaseOrderID, 'Details retrieved successfully');
    }

    public function procumentOrderCancel(Request $request)
    {
        $input = $request->all();

        $purchaseOrderID = $input['purchaseOrderID'];

        $employee = \Helper::getEmployeeInfo();

        $purchaseOrder = ProcumentOrder::find($purchaseOrderID);

        if (empty($purchaseOrder)) {
            return $this->sendError('Purchase Order not found');
        }

        $update = ProcumentOrder::where('purchaseOrderID', $purchaseOrderID)
            ->update([
                'poCancelledYN' => -1,
                'poCancelledBySystemID' => $employee->employeeSystemID,
                'poCancelledBy' => $employee->empID,
                'poCancelledByName' => $employee->empName,
                'poCancelledDate' => now(),
                'cancelledComments' => $input['cancelComments']
            ]);

        if($purchaseOrder->approved == -1){

        }

        $emails = array();
        $document = DocumentMaster::where('documentSystemID', $purchaseOrder->documentSystemID)->first();

        $cancelDocNameBody = $document->documentDescription . ' <b>' . $purchaseOrder->purchaseOrderCode . '</b>';
        $cancelDocNameSubject = $document->documentDescription . ' ' . $purchaseOrder->purchaseOrderCode;

        $body = '<p>' . $cancelDocNameBody . ' is cancelled due to below reason.</p><p>Comment : ' . $input['cancelComments'] . '</p>';
        $subject = $cancelDocNameSubject . ' is cancelled';

        if ($purchaseOrder->poConfirmedYN == 1) {
            $emails[] = array('empSystemID' => $purchaseOrder->poConfirmedByEmpSystemID,
                'companySystemID' => $purchaseOrder->companySystemID,
                'docSystemID' => $purchaseOrder->documentSystemID,
                'alertMessage' => $subject,
                'emailAlertMessage' => $body,
                'docSystemCode' => $purchaseOrder->purchaseOrderID);
        }

        $documentApproval = DocumentApproved::where('companySystemID', $purchaseOrder->companySystemID)
            ->where('documentSystemCode', $purchaseOrder->purchaseOrderID)
            ->where('documentSystemID', $purchaseOrder->documentSystemID)
            ->where('approvedYN', -1)
            ->get();

        foreach ($documentApproval as $da) {
            $emails[] = array('empSystemID' => $da->employeeSystemID,
                'companySystemID' => $purchaseOrder->companySystemID,
                'docSystemID' => $purchaseOrder->documentSystemID,
                'alertMessage' => $subject,
                'emailAlertMessage' => $body,
                'docSystemCode' => $purchaseOrder->purchaseOrderID);
        }

        $sendEmail = \Email::sendEmail($emails);
        if (!$sendEmail["success"]) {
            return $this->sendError($sendEmail["message"], 500);
        }

        return $this->sendResponse($purchaseOrderID, 'Order canceled successfully ');
    }

    public function procumentOrderReturnBack(Request $request)
    {
        $input = $request->all();

        $purchaseOrderID = $input['purchaseOrderID'];

        $emails = array();

        $purchaseOrder = ProcumentOrder::find($purchaseOrderID);

        if (empty($purchaseOrder)) {
            return $this->sendError('Purchase Order not found');
        }

        if ($purchaseOrder->manuallyClosed == 1) {
            return $this->sendError('You cannot revert back this request as it is closed manually.');
        }

        $document = DocumentMaster::where('documentSystemID', $purchaseOrder->documentSystemID)->first();

        $cancelDocNameBody = $document->documentDescription . ' <b>' . $purchaseOrder->purchaseOrderCode . '</b>';
        $cancelDocNameSubject = $document->documentDescription . ' ' . $purchaseOrder->purchaseOrderCode;

        $body = '<p>' . $cancelDocNameBody . ' is return back to amend due to below reason.</p><p>Comment : ' . $input['returnComment'] . '</p>';
        $subject = $cancelDocNameSubject . ' is return back to amend';

        if ($purchaseOrder->poConfirmedYN == 1) {
            $emails[] = array('empSystemID' => $purchaseOrder->poConfirmedByEmpSystemID,
                'companySystemID' => $purchaseOrder->companySystemID,
                'docSystemID' => $purchaseOrder->documentSystemID,
                'alertMessage' => $subject,
                'emailAlertMessage' => $body,
                'docSystemCode' => $purchaseOrder->purchaseOrderID);
        }

        $documentApproval = DocumentApproved::where('companySystemID', $purchaseOrder->companySystemID)
            ->where('documentSystemCode', $purchaseOrder->purchaseOrderID)
            ->where('documentSystemID', $purchaseOrder->documentSystemID)
            //->where('approvedYN', -1)
            ->get();

        foreach ($documentApproval as $da) {

            if ($da->approvedYN == -1) {
                $emails[] = array('empSystemID' => $da->employeeSystemID,
                    'companySystemID' => $purchaseOrder->companySystemID,
                    'docSystemID' => $purchaseOrder->documentSystemID,
                    'alertMessage' => $subject,
                    'emailAlertMessage' => $body,
                    'docSystemCode' => $purchaseOrder->purchaseOrderID);
            }
        }

        $deleteApproval = DocumentApproved::where('documentSystemCode', $purchaseOrderID)
            ->where('documentSystemID', $input['documentSystemID'])
            ->delete();

        if ($deleteApproval) {
            $update = ProcumentOrder::where('purchaseOrderID', $purchaseOrderID)
                ->update([
                    'poConfirmedYN' => 0,
                    'poConfirmedByEmpSystemID' => null,
                    'poConfirmedByEmpID' => null,
                    'poConfirmedByName' => null,
                    'poConfirmedDate' => null,
                    'approved' => 0,
                    'approvedDate' => null,
                    'approvedByUserID' => null,
                    'approvedByUserSystemID' => null,
                    'RollLevForApp_curr' => 1,
                    'sentToSupplier' => 0,
                    'sentToSupplierByEmpSystemID' => null,
                    'sentToSupplierByEmpID' => null,
                    'sentToSupplierByEmpName' => null,
                    'sentToSupplierDate' => null
                ]);
        }


        $sendEmail = \Email::sendEmail($emails);
        if (!$sendEmail["success"]) {
            return $this->sendError($sendEmail["message"], 500);
        }


        return $this->sendResponse($purchaseOrderID, 'PO return back to amend successfully ');
    }

    public function reportSpentAnalysisBySupplierFilter(Request $request)
    {
        $selectedCompanyId = $request['selectedCompanyId'];
        $isGroup = \Helper::checkIsCompanyGroup($selectedCompanyId);

        if ($isGroup) {
            $subCompanies = \Helper::getGroupCompany($selectedCompanyId);
        } else {
            $subCompanies = [$selectedCompanyId];
        }

        $allCompanies = Company::whereIn("companySystemID", $subCompanies)
            ->select('companySystemID', 'CompanyID', 'CompanyName')
            ->get();

        $currency = Company::select(DB::raw("cm1.CurrencyCode as localCurrency,cm2.CurrencyCode as reportingCurrency"))
            ->leftjoin('currencymaster as cm1', 'cm1.currencyID', '=', 'companymaster.localCurrencyID')
            ->leftjoin('currencymaster as cm2', 'cm2.currencyID', '=', 'companymaster.reportingCurrency')
            ->whereIn("companySystemID", $subCompanies)
            ->get();

        $years = ProcumentOrder::select(DB::raw("YEAR(createdDateTime) as year"))
            ->whereNotNull('createdDateTime')
            ->groupby('year')
            ->orderby('year', 'desc')
            ->get(['year']);

        $output = array(
            'allCompanies' => $allCompanies,
            'years' => $years,
            'currency' => $currency
        );

        return $this->sendResponse($output, 'Record retrieved successfully');
    }

    public function reportSpentAnalysisHeader(Request $request)
    {
        $input = $request->all();

        $firstYear = reset($input['years']);
        $lastYear = end($input['years']);

        $months = array();

        if (!empty($input['years'])) {

            $startMonthCN = new Carbon($lastYear . '-01-01');
            $endMonthCN = new Carbon($firstYear . '-12-31');

            if (now() > $endMonthCN) {
                $endMonthCN = new Carbon($firstYear . '-12-31');
            } else {
                $endMonthCN = now();
            }

            $start = $startMonthCN->startOfMonth();
            $end = $endMonthCN->startOfMonth();

            $i = 0;

            do {
                $months [$i]["id"] = $start->format('Y-m');
                $months [$i]["value"] = $start->format('F Y');
                $i++;
            } while ($start->addMonth() <= $end);
        }


        return $this->sendResponse($months, 'Record retrieved successfully');

    }

    public function reportSpentAnalysis(Request $request)
    {
        $input = $request->all();

        $validator = \Validator::make($input, [
            'currency' => 'required',
            'documentId' => 'required',
            'companySystemID' => 'required',
            'years' => 'required',
        ]);

        $supplierReportGRVBase = array();

        if ($validator->fails()) {//echo 'in';exit;
            return $supplierReportGRVBase = array();
            return $this->sendError($validator->messages(), 422);
        }

        $firstYear = reset($input['years']);
        $lastYear = end($input['years']);

        $startMonthCN = new Carbon($lastYear . '-01-01');
        $endMonthCN = new Carbon($firstYear . '-12-31');

        if (now() > $endMonthCN) {
            $endMonthCN = new Carbon($firstYear . '-12-31');
        } else {
            $endMonthCN = now();
        }
        $start = $startMonthCN->startOfMonth();
        $end = $endMonthCN->startOfMonth();
        $feilds = "";
        $colums = "";

        $commaSeperatedYears = join($input['years'], ",");
        $commaSeperatedCompany = join($input['companySystemID'], ",");

        IF ($input['documentId'] == 1) {
            if ($input['currency'] == 1) {
                $currencyField = 'GRVcostPerUnitLocalCur';
            } else if ($input['currency'] == 2) {
                $currencyField = 'GRVcostPerUnitComRptCur';
            }
        } else if ($input['documentId'] == 2) {
            if ($input['currency'] == 1) {
                $currencyField = 'totLocalAmount';
            } else if ($input['currency'] == 2) {
                $currencyField = 'totRptAmount';
            }
        }

        if (!empty($input['years'])) {

            do {
                $months[$start->format('Y-m')] = $start->format('F Y');
            } while ($start->addMonth() <= $end);

            if (!empty($months)) {
                foreach ($months as $key => $val) {
                    if ($input['documentId'] == 1) {
                        $feilds .= "SUM(if(DATE_FORMAT(erp_grvmaster.grvDate,'%Y-%m') = '$key',$currencyField * noQty ,0)) as `$key`,";
                        $colums .= "GRVDet.`$key` as $key,";
                    } else {
                        $feilds .= "SUM(if(DATE_FORMAT(erp_bookinvsuppmaster.postedDate,'%Y-%m') = '$key',$currencyField,0)) as `$key`,";
                        $colums .= "InvoiceDet.`$key` as $key,";
                    }

                }
            }
        }

        if ($input['documentId'] == 1) {

            $supplierReportGRVBase = DB::select('SELECT
        GRVDet.*,
	PODet.purchaseOrderMasterID,
	PODet.companyID,
	PODet.supplierID,
	erp_purchaseordermaster.supplierPrimaryCode,
	erp_purchaseordermaster.supplierName,
	PODet.POlocalAmount,
	PODet.PORptAmount,
	GRVDet.LinelocalTotal,
	GRVDet.LineRptTotal,
	InvoiceDet.InvoicelocalAmount,
	InvoiceDet.InvoiceRptAmount
FROM
	erp_purchaseordermaster
INNER JOIN (
	SELECT
		erp_purchaseorderdetails.purchaseOrderMasterID,
		erp_purchaseordermaster.companyID,
		erp_purchaseordermaster.supplierID,
		erp_purchaseordermaster.approvedDate,
		sum(
			GRVcostPerUnitLocalCur * noQty
		) AS POlocalAmount,
		sum(
			GRVcostPerUnitComRptCur * noQty
		) AS PORptAmount
	FROM
		erp_purchaseorderdetails
	INNER JOIN erp_purchaseordermaster ON erp_purchaseordermaster.purchaseOrderID = erp_purchaseorderdetails.purchaseOrderMasterID
	WHERE
		erp_purchaseordermaster.approved = -1
	AND erp_purchaseordermaster.poCancelledYN = 0
	GROUP BY
		erp_purchaseorderdetails.purchaseOrderMasterID
) AS PODet ON erp_purchaseordermaster.purchaseOrderID = PODet.purchaseOrderMasterID
INNER JOIN (
	SELECT
	    ' . $feilds . '
		erp_grvdetails.purchaseOrderMastertID,
		erp_grvdetails.companyID,
		erp_grvmaster.grvDate,
		supplierID,
		approvedDate,
		GRVcostPerUnitLocalCur,
		GRVcostPerUnitComRptCur,
		noQty,
		sum(
			GRVcostPerUnitLocalCur * noQty
		) AS LinelocalTotal,
		sum(
			GRVcostPerUnitComRptCur * noQty
		) AS LineRptTotal
	FROM
		erp_grvdetails
	INNER JOIN erp_grvmaster ON erp_grvmaster.grvAutoID = erp_grvdetails.grvAutoID
	WHERE
		erp_grvmaster.approved = -1
	AND erp_grvmaster.grvCancelledYN = 0 AND year(erp_grvmaster.grvDate) IN (' . $commaSeperatedYears . ')
	GROUP BY
		erp_grvdetails.purchaseOrderMastertID,
		erp_grvmaster.supplierID
) AS GRVDet ON GRVDet.purchaseOrderMastertID = erp_purchaseordermaster.purchaseOrderID
LEFT JOIN (
	SELECT
		erp_bookinvsuppdet.purchaseOrderID,
		erp_bookinvsuppdet.companyID,
		erp_bookinvsuppmaster.supplierID,
		erp_bookinvsuppmaster.postedDate,
		sum(
			erp_bookinvsuppdet.totLocalAmount
		) AS InvoicelocalAmount,
		sum(
			erp_bookinvsuppdet.totRptAmount
		) AS InvoiceRptAmount
	FROM
		erp_bookinvsuppdet
	INNER JOIN erp_bookinvsuppmaster ON erp_bookinvsuppmaster.bookingSuppMasInvAutoID = erp_bookinvsuppdet.bookingSuppMasInvAutoID
	WHERE
		erp_bookinvsuppmaster.approved = - 1
	AND erp_bookinvsuppmaster.cancelYN = 0
	GROUP BY
		erp_bookinvsuppdet.purchaseOrderID
) AS InvoiceDet ON InvoiceDet.purchaseOrderID = erp_purchaseordermaster.purchaseOrderID
WHERE
	erp_purchaseordermaster.approved = - 1
AND erp_purchaseordermaster.poCancelledYN = 0
AND erp_purchaseordermaster.companySystemID IN (' . $commaSeperatedCompany . ') AND year(GRVDet.grvDate) IN (' . $commaSeperatedYears . ') GROUP BY PODet.supplierID');
        } else if ($input['documentId'] == 2) {
            $supplierReportGRVBase = DB::select('SELECT
         InvoiceDet.*,
	PODet.purchaseOrderMasterID,
	PODet.companyID,
	PODet.supplierID,
	erp_purchaseordermaster.supplierPrimaryCode,
	erp_purchaseordermaster.supplierName,
	PODet.POlocalAmount,
	PODet.PORptAmount,
	InvoiceDet.LinelocalTotal,
	InvoiceDet.LineRptTotal
FROM
	erp_purchaseordermaster
INNER JOIN (
	SELECT
		erp_purchaseorderdetails.purchaseOrderMasterID,
		erp_purchaseordermaster.companyID,
		erp_purchaseordermaster.supplierID,
		erp_purchaseordermaster.approvedDate,
		sum(
			GRVcostPerUnitLocalCur * noQty
		) AS POlocalAmount,
		sum(
			GRVcostPerUnitComRptCur * noQty
		) AS PORptAmount
	FROM
		erp_purchaseorderdetails
	INNER JOIN erp_purchaseordermaster ON erp_purchaseordermaster.purchaseOrderID = erp_purchaseorderdetails.purchaseOrderMasterID
	WHERE
		erp_purchaseordermaster.approved = -1
	AND erp_purchaseordermaster.poCancelledYN = 0
	GROUP BY
		erp_purchaseorderdetails.purchaseOrderMasterID
) AS PODet ON erp_purchaseordermaster.purchaseOrderID = PODet.purchaseOrderMasterID
INNER JOIN (
	SELECT
		erp_grvdetails.purchaseOrderMastertID,
		erp_grvdetails.companyID,
		erp_grvmaster.grvDate,
		supplierID,
		approvedDate,
		GRVcostPerUnitLocalCur,
		GRVcostPerUnitComRptCur,
		noQty,
		sum(
			GRVcostPerUnitLocalCur * noQty
		) AS GRVlocalAmount,
		sum(
			GRVcostPerUnitComRptCur * noQty
		) AS GRVRptAmount
	FROM
		erp_grvdetails
	INNER JOIN erp_grvmaster ON erp_grvmaster.grvAutoID = erp_grvdetails.grvAutoID
	WHERE
		erp_grvmaster.approved = -1
	AND erp_grvmaster.grvCancelledYN = 0
	GROUP BY
		erp_grvdetails.purchaseOrderMastertID
) AS GRVDet ON GRVDet.purchaseOrderMastertID = erp_purchaseordermaster.purchaseOrderID
LEFT JOIN (
	SELECT
	    ' . $feilds . '
		erp_bookinvsuppdet.purchaseOrderID,
		erp_bookinvsuppdet.companyID,
		erp_bookinvsuppmaster.supplierID,
		erp_bookinvsuppmaster.postedDate,
		sum(
			erp_bookinvsuppdet.totLocalAmount
		) AS LinelocalTotal,
		sum(
			erp_bookinvsuppdet.totRptAmount
		) AS LineRptTotal
	FROM
		erp_bookinvsuppdet
	INNER JOIN erp_bookinvsuppmaster ON erp_bookinvsuppmaster.bookingSuppMasInvAutoID = erp_bookinvsuppdet.bookingSuppMasInvAutoID
	WHERE
		erp_bookinvsuppmaster.approved = - 1
	AND erp_bookinvsuppmaster.cancelYN = 0
	GROUP BY
		erp_bookinvsuppdet.purchaseOrderID,
		erp_bookinvsuppmaster.supplierID
) AS InvoiceDet ON InvoiceDet.purchaseOrderID = erp_purchaseordermaster.purchaseOrderID
WHERE
	erp_purchaseordermaster.approved = - 1
AND erp_purchaseordermaster.poCancelledYN = 0
AND erp_purchaseordermaster.companySystemID IN (' . $commaSeperatedCompany . ') AND year(InvoiceDet.postedDate) IN (' . $commaSeperatedYears . ') GROUP BY PODet.supplierID');
        }
        $alltotal = array();
        $i = 0;
        if (!empty($months)) {
            foreach ($months as $key => $val) {
                if ($input['currency'] == 1) {
                    $tot = collect($supplierReportGRVBase)->pluck($key)->toArray();
                    $alltotal [$i]["id"] = $key;
                    $alltotal [$i]["value"] = array_sum($tot);
                } else {
                    $tot = collect($supplierReportGRVBase)->pluck($key)->toArray();
                    $alltotal [$i]["id"] = $key;
                    $alltotal [$i]["value"] = array_sum($tot);
                }
                $i++;
            }
        }
        if ($input['documentId'] == 1) {
            if ($input['currency'] == 1) {
                $tot = collect($supplierReportGRVBase)->pluck('LinelocalTotal')->toArray();
                $pageTotal = array_sum($tot);
            } else {
                $tot = collect($supplierReportGRVBase)->pluck('LineRptTotal')->toArray();
                $pageTotal = array_sum($tot);
            }
        } else if ($input['documentId'] == 2) {
            if ($input['currency'] == 1) {
                $tot = collect($supplierReportGRVBase)->pluck('LinelocalTotal')->toArray();
                $pageTotal = array_sum($tot);
            } else {
                $tot = collect($supplierReportGRVBase)->pluck('LineRptTotal')->toArray();
                $pageTotal = array_sum($tot);
            }
        }

        $dataRec = \DataTables::of($supplierReportGRVBase)
            ->addIndexColumn()
            ->with('totalAmount', $alltotal)
            ->with('pageTotal', $pageTotal)
            ->make(true);

        return $dataRec;

    }

    public function reportSpentAnalysisExport(Request $request)
    {
        $input = $request->all();

        $validator = \Validator::make($input, [
            'currency' => 'required',
            'documentId' => 'required',
            'companySystemID' => 'required',
            'years' => 'required',
        ]);

        $supplierReportGRVBase = array();

        if ($validator->fails()) {//echo 'in';exit;
            return $this->sendError($validator->messages(), 422);
            exit();
        }

        $type = $request->type;

        $firstYear = reset($input['years']);
        $lastYear = end($input['years']);

        $startMonthCN = new Carbon($lastYear . '-01-01');
        $endMonthCN = new Carbon($firstYear . '-12-31');

        if (now() > $endMonthCN) {
            $endMonthCN = new Carbon($firstYear . '-12-31');
        } else {
            $endMonthCN = now();
        }

        $start = $startMonthCN->startOfMonth();
        $end = $endMonthCN->startOfMonth();

        $feilds = "";
        $colums = "";

        $commaSeperatedYears = join($input['years'], ",");
        $commaSeperatedCompany = join($input['companySystemID'], ",");

        if ($input['documentId'] == 1) {
            if ($input['currency'] == 1) {
                $currencyField = 'GRVcostPerUnitLocalCur';
            } else if ($input['currency'] == 2) {
                $currencyField = 'GRVcostPerUnitComRptCur';
            }
        } else if ($input['documentId'] == 2) {
            if ($input['currency'] == 1) {
                $currencyField = 'totLocalAmount';
            } else if ($input['currency'] == 2) {
                $currencyField = 'totRptAmount';
            }
        }

        if (!empty($input['years'])) {

            do {
                $months[$start->format('Y-m')] = $start->format('F Y');
            } while ($start->addMonth() <= $end);

            if (!empty($months)) {
                foreach ($months as $key => $val) {
                    if ($input['documentId'] == 1) {
                        $feilds .= "SUM(if(DATE_FORMAT(erp_grvmaster.grvDate,'%Y-%m') = '$key',$currencyField * noQty ,0)) as `$key`,";
                        $colums .= "GRVDet.`$key` as $key,";
                    } else {
                        $feilds .= "SUM(if(DATE_FORMAT(erp_bookinvsuppmaster.postedDate,'%Y-%m') = '$key',$currencyField,0)) as `$key`,";
                        $colums .= "InvoiceDet.`$key` as $key,";
                    }

                }
            }
        }

        if ($input['documentId'] == 1) {

            $supplierReportGRVBase = DB::select('SELECT
        GRVDet.*,
	PODet.purchaseOrderMasterID,
	PODet.companyID,
	PODet.supplierID,
	erp_purchaseordermaster.supplierPrimaryCode,
	erp_purchaseordermaster.supplierName,
	PODet.POlocalAmount,
	PODet.PORptAmount,
	GRVDet.GRVlocalTotal,
	GRVDet.GRVRptTotal,
	InvoiceDet.InvoicelocalAmount,
	InvoiceDet.InvoiceRptAmount
FROM
	erp_purchaseordermaster
INNER JOIN (
	SELECT
		erp_purchaseorderdetails.purchaseOrderMasterID,
		erp_purchaseordermaster.companyID,
		erp_purchaseordermaster.supplierID,
		erp_purchaseordermaster.approvedDate,
		sum(
			GRVcostPerUnitLocalCur * noQty
		) AS POlocalAmount,
		sum(
			GRVcostPerUnitComRptCur * noQty
		) AS PORptAmount
	FROM
		erp_purchaseorderdetails
	INNER JOIN erp_purchaseordermaster ON erp_purchaseordermaster.purchaseOrderID = erp_purchaseorderdetails.purchaseOrderMasterID
	WHERE
		erp_purchaseordermaster.approved = -1
	AND erp_purchaseordermaster.poCancelledYN = 0
	GROUP BY
		erp_purchaseorderdetails.purchaseOrderMasterID
) AS PODet ON erp_purchaseordermaster.purchaseOrderID = PODet.purchaseOrderMasterID
INNER JOIN (
	SELECT
	    ' . $feilds . '
		erp_grvdetails.purchaseOrderMastertID,
		erp_grvdetails.companyID,
		erp_grvmaster.grvDate,
		supplierID,
		approvedDate,
		GRVcostPerUnitLocalCur,
		GRVcostPerUnitComRptCur,
		noQty,
		sum(
			GRVcostPerUnitLocalCur * noQty
		) AS GRVlocalTotal,
		sum(
			GRVcostPerUnitComRptCur * noQty
		) AS GRVRptTotal
	FROM
		erp_grvdetails
	INNER JOIN erp_grvmaster ON erp_grvmaster.grvAutoID = erp_grvdetails.grvAutoID
	WHERE
		erp_grvmaster.approved = -1
	AND erp_grvmaster.grvCancelledYN = 0 AND year(erp_grvmaster.grvDate) IN (' . $commaSeperatedYears . ')
	GROUP BY
		erp_grvdetails.purchaseOrderMastertID,
		erp_grvmaster.supplierID
) AS GRVDet ON GRVDet.purchaseOrderMastertID = erp_purchaseordermaster.purchaseOrderID
LEFT JOIN (
	SELECT
		erp_bookinvsuppdet.purchaseOrderID,
		erp_bookinvsuppdet.companyID,
		erp_bookinvsuppmaster.supplierID,
		erp_bookinvsuppmaster.postedDate,
		sum(
			erp_bookinvsuppdet.totLocalAmount
		) AS InvoicelocalAmount,
		sum(
			erp_bookinvsuppdet.totRptAmount
		) AS InvoiceRptAmount
	FROM
		erp_bookinvsuppdet
	INNER JOIN erp_bookinvsuppmaster ON erp_bookinvsuppmaster.bookingSuppMasInvAutoID = erp_bookinvsuppdet.bookingSuppMasInvAutoID
	WHERE
		erp_bookinvsuppmaster.approved = - 1
	AND erp_bookinvsuppmaster.cancelYN = 0
	GROUP BY
		erp_bookinvsuppdet.purchaseOrderID
) AS InvoiceDet ON InvoiceDet.purchaseOrderID = erp_purchaseordermaster.purchaseOrderID
WHERE
	erp_purchaseordermaster.approved = - 1
AND erp_purchaseordermaster.poCancelledYN = 0
AND erp_purchaseordermaster.companySystemID IN (' . $commaSeperatedCompany . ') AND year(GRVDet.grvDate) IN (' . $commaSeperatedYears . ') GROUP BY PODet.supplierID');
        } else if ($input['documentId'] == 2) {
            $supplierReportGRVBase = DB::select('SELECT
         InvoiceDet.*,
	PODet.purchaseOrderMasterID,
	PODet.companyID,
	PODet.supplierID,
	erp_purchaseordermaster.supplierPrimaryCode,
	erp_purchaseordermaster.supplierName,
	PODet.POlocalAmount,
	PODet.PORptAmount,
	InvoiceDet.LinelocalTotal,
	InvoiceDet.LineRptTotal
FROM
	erp_purchaseordermaster
INNER JOIN (
	SELECT
		erp_purchaseorderdetails.purchaseOrderMasterID,
		erp_purchaseordermaster.companyID,
		erp_purchaseordermaster.supplierID,
		erp_purchaseordermaster.approvedDate,
		sum(
			GRVcostPerUnitLocalCur * noQty
		) AS POlocalAmount,
		sum(
			GRVcostPerUnitComRptCur * noQty
		) AS PORptAmount
	FROM
		erp_purchaseorderdetails
	INNER JOIN erp_purchaseordermaster ON erp_purchaseordermaster.purchaseOrderID = erp_purchaseorderdetails.purchaseOrderMasterID
	WHERE
		erp_purchaseordermaster.approved = -1
	AND erp_purchaseordermaster.poCancelledYN = 0
	GROUP BY
		erp_purchaseorderdetails.purchaseOrderMasterID
) AS PODet ON erp_purchaseordermaster.purchaseOrderID = PODet.purchaseOrderMasterID
INNER JOIN (
	SELECT
		erp_grvdetails.purchaseOrderMastertID,
		erp_grvdetails.companyID,
		erp_grvmaster.grvDate,
		supplierID,
		approvedDate,
		GRVcostPerUnitLocalCur,
		GRVcostPerUnitComRptCur,
		noQty,
		sum(
			GRVcostPerUnitLocalCur * noQty
		) AS GRVlocalAmount,
		sum(
			GRVcostPerUnitComRptCur * noQty
		) AS GRVRptAmount
	FROM
		erp_grvdetails
	INNER JOIN erp_grvmaster ON erp_grvmaster.grvAutoID = erp_grvdetails.grvAutoID
	WHERE
		erp_grvmaster.approved = -1
	AND erp_grvmaster.grvCancelledYN = 0
	GROUP BY
		erp_grvdetails.purchaseOrderMastertID
) AS GRVDet ON GRVDet.purchaseOrderMastertID = erp_purchaseordermaster.purchaseOrderID
LEFT JOIN (
	SELECT
	    ' . $feilds . '
		erp_bookinvsuppdet.purchaseOrderID,
		erp_bookinvsuppdet.companyID,
		erp_bookinvsuppmaster.supplierID,
		erp_bookinvsuppmaster.postedDate,
		sum(
			erp_bookinvsuppdet.totLocalAmount
		) AS LinelocalTotal,
		sum(
			erp_bookinvsuppdet.totRptAmount
		) AS LineRptTotal
	FROM
		erp_bookinvsuppdet
	INNER JOIN erp_bookinvsuppmaster ON erp_bookinvsuppmaster.bookingSuppMasInvAutoID = erp_bookinvsuppdet.bookingSuppMasInvAutoID
	WHERE
		erp_bookinvsuppmaster.approved = - 1
	AND erp_bookinvsuppmaster.cancelYN = 0
	GROUP BY
		erp_bookinvsuppdet.purchaseOrderID,
		erp_bookinvsuppmaster.supplierID
) AS InvoiceDet ON InvoiceDet.purchaseOrderID = erp_purchaseordermaster.purchaseOrderID
WHERE
	erp_purchaseordermaster.approved = - 1
AND erp_purchaseordermaster.poCancelledYN = 0
AND erp_purchaseordermaster.companySystemID IN (' . $commaSeperatedCompany . ') AND year(InvoiceDet.postedDate) IN (' . $commaSeperatedYears . ') GROUP BY PODet.supplierID');
        }
        $alltotal = array();
        $i = 0;
        if (!empty($months)) {
            foreach ($months as $key => $val) {
                if ($input['currency'] == 1) {
                    $tot = collect($supplierReportGRVBase)->pluck($key)->toArray();
                    $alltotal [$i]["id"] = $key;
                    $alltotal [$i]["value"] = array_sum($tot);
                } else {
                    $tot = collect($supplierReportGRVBase)->pluck($key)->toArray();
                    $alltotal [$i]["id"] = $key;
                    $alltotal [$i]["value"] = array_sum($tot);
                }
                $i++;
            }
        }

        if ($input['documentId'] == 1) {
            if ($input['currency'] == 1) {
                $currencyField = 'GRVcostPerUnitLocalCur';
            } else if ($input['currency'] == 2) {
                $currencyField = 'GRVcostPerUnitComRptCur';
            }
        } else if ($input['documentId'] == 2) {
            if ($input['currency'] == 1) {
                $currencyField = 'totLocalAmount';
            } else if ($input['currency'] == 2) {
                $currencyField = 'totRptAmount';
            }
        }

        foreach ($supplierReportGRVBase as $val) {
            $test = array('CompanyID' => $val->companyID, 'SupplierCode' => $val->supplierPrimaryCode, 'SupplierName' => $val->supplierName);

            if (!empty($months)) {
                foreach ($months as $key => $row) {
                    if ($input['currency'] == 1) {
                        $test[$row] = $val->$key;
                    } else {
                        $test[$row] = $val->$key;
                    }
                    $i++;
                }
            }

            $data[] = $test;
        }

        $csv = \Excel::create('item_wise_po_analysis', function ($excel) use ($data) {

            $excel->sheet('sheet name', function ($sheet) use ($data) {
                $sheet->fromArray($data);
                //$sheet->getStyle('A1')->getAlignment()->setWrapText(true);
                $sheet->setAutoSize(true);
                $sheet->getStyle('C1:C2')->getAlignment()->setWrapText(true);
            });
            $lastrow = $excel->getActiveSheet()->getHighestRow();
            $excel->getActiveSheet()->getStyle('A1:J' . $lastrow)->getAlignment()->setWrapText(true);
        })->download($type);

        return $this->sendResponse(array(), 'successfully export');

    }

    /**
     * manual Close Purchase Order
     * Post /manualCloseProcurementOrder
     *
     * @param $request
     *
     * @return Response
     */
    public function manualCloseProcurementOrder(Request $request)
    {
        $input = $request->all();
        $procumentOrder = $this->procumentOrderRepository->with(['created_by', 'confirmed_by'])->findWithoutFail($input['purchaseOrderID']);

        if (empty($procumentOrder)) {
            return $this->sendError('Procurement Order not found');
        }

        if ($procumentOrder->grvRecieved == 2) {
            return $this->sendError('You cannot close this order, this is already fully received');
        }

        if ($procumentOrder->manuallyClosed == 1) {
            return $this->sendError('You cannot close this order, this order already manually closed');
        }

        if ($procumentOrder->approved != -1 || $procumentOrder->poCancelledYN == -1) {
            return $this->sendError('You cannot close this order, this order is only approved');
        }

        if ($procumentOrder->approved != -1 || $procumentOrder->grvRecieved == 0) {
            return $this->sendError('You cannot close this order, You can only close partially received order');
        }

        $employee = \Helper::getEmployeeInfo();

        $emails = array();

        $document = DocumentMaster::where('documentSystemID', $procumentOrder->documentSystemID)->first();

        $cancelDocNameBody = $document->documentDescription . ' <b>' . $procumentOrder->purchaseOrderCode . '</b>';
        $cancelDocNameSubject = $document->documentDescription . ' ' . $procumentOrder->purchaseOrderCode;

        $body = '<p>' . $cancelDocNameBody . ' is manually closed due to below reason.</p><p>Comment : ' . $input['manuallyClosedComment'] . '</p>';
        $subject = $cancelDocNameSubject . ' is closed';

        if ($procumentOrder->poConfirmedYN == 1) {
            $emails[] = array('empSystemID' => $procumentOrder->poConfirmedByEmpSystemID,
                'companySystemID' => $procumentOrder->companySystemID,
                'docSystemID' => $procumentOrder->documentSystemID,
                'alertMessage' => $subject,
                'emailAlertMessage' => $body,
                'docSystemCode' => $procumentOrder->purchaseOrderID);
        }

        $procumentOrder->manuallyClosed = 1;
        $procumentOrder->manuallyClosedByEmpSystemID = $employee->employeeSystemID;
        $procumentOrder->manuallyClosedByEmpID = $employee->empID;
        $procumentOrder->manuallyClosedByEmpName = $employee->empName;
        $procumentOrder->manuallyClosedComment = $input['manuallyClosedComment'];
        $procumentOrder->manuallyClosedDate = now();
        $procumentOrder->save();

        $purchaseDetails = PurchaseOrderDetails::where('purchaseOrderMasterID', $procumentOrder->purchaseOrderID)
            ->where('GRVSelectedYN', 0)
            ->where('goodsRecievedYN', '!=', 2)
            ->get();

        foreach ($purchaseDetails as $det) {

            $detail = PurchaseOrderDetails::where('purchaseOrderDetailsID', $det['purchaseOrderDetailsID'])->first();

            if ($detail) {
                if ($detail->GRVSelectedYN == 0 and $detail->goodsRecievedYN != 2) {
                    $detail->manuallyClosed = 1;
                    $detail->manuallyClosedByEmpSystemID = $employee->employeeSystemID;
                    $detail->manuallyClosedByEmpID = $employee->empID;
                    $detail->manuallyClosedByEmpName = $employee->empName;
                    $detail->manuallyClosedComment = $input['manuallyClosedComment'];
                    $detail->manuallyClosedDate = now();
                    $detail->save();
                }
            }
        }

        $documentApproval = DocumentApproved::where('companySystemID', $procumentOrder->companySystemID)
            ->where('documentSystemCode', $procumentOrder->purchaseOrderID)
            ->where('documentSystemID', $procumentOrder->documentSystemID)
            ->get();

        foreach ($documentApproval as $da) {
            if ($da->approvedYN == -1) {
                $emails[] = array('empSystemID' => $da->employeeSystemID,
                    'companySystemID' => $procumentOrder->companySystemID,
                    'docSystemID' => $procumentOrder->documentSystemID,
                    'alertMessage' => $subject,
                    'emailAlertMessage' => $body,
                    'docSystemCode' => $procumentOrder->purchaseOrderID);
            }
        }

        $sendEmail = \Email::sendEmail($emails);
        if (!$sendEmail["success"]) {
            return $this->sendError($sendEmail["message"], 500);
        }

        return $this->sendResponse($procumentOrder, 'Purchase Order successfully closed');
    }

    /**
     * Display the specified Procurement Order print.
     * GET|HEAD /printProcumentOrder
     *
     * @param  int $request
     *
     * @return Response
     */

    public function getProcumentOrderPrintPDF(Request $request)
    {
        $id = $request->get('id');

        $procumentOrder = $this->procumentOrderRepository->findWithoutFail($id);

        if (empty($procumentOrder)) {
            return $this->sendError('Procurement Order not found');
        }

        $outputRecord = ProcumentOrder::where('purchaseOrderID', $procumentOrder->purchaseOrderID)->with(['detail' => function ($query) {
            $query->with('unit');
        }, 'approved_by' => function ($query) {
            $query->with('employee');
            $query->whereIN('documentSystemID', [2, 5, 52]);
        }, 'suppliercontact' => function ($query) {
            $query->where('isDefault', -1);
        }, 'company', 'transactioncurrency', 'companydocumentattachment'])->get();

        $refernaceDoc = CompanyDocumentAttachment::where('companySystemID', $procumentOrder->companySystemID)
            ->where('documentSystemID', $procumentOrder->documentSystemID)
            ->first();

        $currencyDecimal = CurrencyMaster::select('DecimalPlaces')->where('currencyID', $procumentOrder->supplierTransactionCurrencyID)
            ->first();
        $decimal = 2;
        if (!empty($currencyDecimal)) {
            $decimal = $currencyDecimal['DecimalPlaces'];
        }

        $documentTitle = 'Purchase Order';
        if ($procumentOrder->documentSystemID == 2) {
            $documentTitle = 'Purchase Order';
        } else if ($procumentOrder->documentSystemID == 5 && $procumentOrder->poType_N == 5) {
            $documentTitle = 'Work Order';
        } else if ($procumentOrder->documentSystemID == 5 && $procumentOrder->poType_N == 6) {
            $documentTitle = 'Sub Work Order';
        } else if ($procumentOrder->documentSystemID == 52) {
            $documentTitle = 'Direct Order';
        }

        $order = array('podata' => $outputRecord[0], 'docRef' => $refernaceDoc, 'numberFormatting' => $decimal, 'title' => $documentTitle);

        $html = view('print.purchase_order_print_pdf', $order);

        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($html);

        return $pdf->setPaper('a4', 'portrait')->setWarnings(false)->stream();
    }

    public function procumentOrderSegmentchk(Request $request)
    {

        $input = $request->all();

        $purchaseOrderID = $input['purchaseOrderID'];

        $purchaseOrder = ProcumentOrder::find($purchaseOrderID);

        if (empty($purchaseOrder)) {
            return $this->sendError('Purchase Order not found');
        }

        //checking segment is active

        $segments = SegmentMaster::where("serviceLineSystemID", $purchaseOrder->serviceLineSystemID)
            ->where('companySystemID', $input['companySystemID'])
            ->where('isActive', 1)
            ->first();

        if (empty($segments)) {
            return $this->sendError('Selected segment is not active. Please select an active segment');
        }

        return $this->sendResponse($purchaseOrderID, 'sucess');
    }

    public function getApprovedPOForCurrentUser(Request $request)
    {
        $input = $request->all();

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $companyID = $request->companyId;
        $empID = \Helper::getEmployeeSystemID();

        $poMasters = DB::table('erp_documentapproved')->select(
            'erp_purchaseordermaster.purchaseOrderID',
            'erp_purchaseordermaster.purchaseOrderCode',
            'erp_purchaseordermaster.documentSystemID',
            'erp_purchaseordermaster.referenceNumber',
            'erp_purchaseordermaster.expectedDeliveryDate',
            'erp_purchaseordermaster.supplierPrimaryCode',
            'erp_purchaseordermaster.supplierName',
            'erp_purchaseordermaster.narration',
            'erp_purchaseordermaster.serviceLine',
            'erp_purchaseordermaster.createdDateTime',
            'erp_purchaseordermaster.poConfirmedDate',
            'erp_purchaseordermaster.poTotalSupplierTransactionCurrency',
            'erp_documentapproved.documentApprovedID',
            'erp_documentapproved.rollLevelOrder',
            'currencymaster.CurrencyCode',
            'approvalLevelID',
            'documentSystemCode'
        )->join('erp_purchaseordermaster', function ($query) use ($companyID, $empID) {
            $query->on('erp_documentapproved.documentSystemCode', '=', 'purchaseOrderID')
                ->where('erp_purchaseordermaster.companySystemID', $companyID)
                ->where('erp_purchaseordermaster.approved', -1)
                ->where('erp_purchaseordermaster.poConfirmedYN', 1);
        })->where('erp_documentapproved.approvedYN', -1)
            ->join('currencymaster', 'supplierTransactionCurrencyID', '=', 'currencyID')
            ->whereIn('erp_documentapproved.documentSystemID', [2, 5, 52])
            ->where('erp_documentapproved.companySystemID', $companyID)->where('erp_documentapproved.employeeSystemID', $empID);

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $poMasters = $poMasters->where(function ($query) use ($search) {
                $query->where('purchaseOrderCode', 'LIKE', "%{$search}%")
                    ->orWhere('narration', 'LIKE', "%{$search}%")
                    ->orWhere('supplierName', 'LIKE', "%{$search}%");
            });
        }

        return \DataTables::of($poMasters)
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

    public function getGRVDrilldownSpentAnalysis(Request $request)
    {
        $input = $request->all();

        $monthExp = explode('-', $input['month']);

        $expYear = $monthExp[0];
        $expMonth = $monthExp[1];

        $commaSeperatedYears = join($input['years'], ",");
        $commaSeperatedCompany = join($input['companySystemID'], ",");

        $supplierID = $input['supplierID'];

        if ($input['documentId'] == 1) {

            $detail = DB::select('SELECT
	GRVDet.*, PODet.purchaseOrderMasterID,
	PODet.companyID,
	PODet.supplierID,
	erp_purchaseordermaster.supplierPrimaryCode,
	erp_purchaseordermaster.supplierName,
	PODet.POlocalAmount,
	PODet.PORptAmount,
	warehousemaster.wareHouseDescription
FROM
	erp_purchaseordermaster
INNER JOIN (
	SELECT
		erp_purchaseorderdetails.purchaseOrderMasterID,
		erp_purchaseordermaster.companyID,
		erp_purchaseordermaster.supplierID,
		erp_purchaseordermaster.approvedDate,
		sum(
			GRVcostPerUnitLocalCur * noQty
		) AS POlocalAmount,
		sum(
			GRVcostPerUnitComRptCur * noQty
		) AS PORptAmount
	FROM
		erp_purchaseorderdetails
	INNER JOIN erp_purchaseordermaster ON erp_purchaseordermaster.purchaseOrderID = erp_purchaseorderdetails.purchaseOrderMasterID
	WHERE
		erp_purchaseordermaster.approved = - 1
	AND erp_purchaseordermaster.poCancelledYN = 0
	GROUP BY
		erp_purchaseorderdetails.purchaseOrderMasterID
) AS PODet ON erp_purchaseordermaster.purchaseOrderID = PODet.purchaseOrderMasterID
INNER JOIN (
	SELECT
		erp_grvdetails.purchaseOrderMastertID,
		erp_grvdetails.itemPrimaryCode,
        erp_grvdetails.itemDescription,
		erp_grvmaster.grvDate,
		erp_grvmaster.grvPrimaryCode,
		erp_grvmaster.grvConfirmedYN,
		erp_grvmaster.approved as grvApproved,
		erp_grvmaster.companySystemID,
		erp_grvmaster.grvLocation,
		supplierID,
		approvedDate,
		GRVcostPerUnitLocalCur,
		GRVcostPerUnitComRptCur,
		noQty,
		sum(
			GRVcostPerUnitLocalCur * noQty
		) AS LinelocalTotal,
		sum(
			GRVcostPerUnitComRptCur * noQty
		) AS LineRptTotal
	FROM
		erp_grvdetails
	INNER JOIN erp_grvmaster ON erp_grvmaster.grvAutoID = erp_grvdetails.grvAutoID
	WHERE
		erp_grvmaster.approved = - 1
	AND erp_grvmaster.grvCancelledYN = 0
	AND YEAR (erp_grvmaster.grvDate) = (' . $expYear . ') AND MONTH (erp_grvmaster.grvDate) = (' . $expMonth . ')
	GROUP BY
		erp_grvdetails.purchaseOrderMastertID
) AS GRVDet ON GRVDet.purchaseOrderMastertID = erp_purchaseordermaster.purchaseOrderID
INNER JOIN warehousemaster ON GRVDet.grvLocation = warehousemaster.wareHouseSystemCode
WHERE
	erp_purchaseordermaster.approved = -1
AND erp_purchaseordermaster.poCancelledYN = 0
AND GRVDet.companySystemID IN (' . $commaSeperatedCompany . ')
AND PODet.supplierID = ' . $supplierID . '');
        } else if ($input['documentId'] == 2) {
            $detail = DB::select('SELECT
	erp_bookinvsuppmaster.bookingSuppMasInvAutoID,
	erp_bookinvsuppmaster.companyID,
	erp_bookinvsuppdet.purchaseOrderID,
	erp_bookinvsuppmaster.documentID,
	erp_grvmaster.grvPrimaryCode,
	erp_bookinvsuppmaster.bookingInvCode,
	erp_bookinvsuppmaster.bookingDate,
	erp_bookinvsuppmaster.comments,
	erp_bookinvsuppmaster.supplierInvoiceNo,
	erp_bookinvsuppmaster.confirmedYN,
	erp_bookinvsuppmaster.confirmedByName,
	erp_bookinvsuppmaster.approved,
	currencymaster.CurrencyCode,
	erp_bookinvsuppdet.totLocalAmount,
	erp_bookinvsuppdet.totRptAmount
FROM
	erp_bookinvsuppmaster
INNER JOIN erp_bookinvsuppdet ON erp_bookinvsuppmaster.bookingSuppMasInvAutoID = erp_bookinvsuppdet.bookingSuppMasInvAutoID
LEFT JOIN currencymaster ON erp_bookinvsuppmaster.supplierTransactionCurrencyID = currencymaster.currencyID
LEFT JOIN erp_grvmaster ON erp_bookinvsuppdet.grvAutoID = erp_grvmaster.grvAutoID
WHERE
	erp_bookinvsuppmaster.supplierID =  ' . $supplierID . '
		AND YEAR (erp_bookinvsuppmaster.postedDate) = (' . $expYear . ') AND MONTH (erp_bookinvsuppmaster.postedDate) = (' . $expMonth . ')');
        }
        return $this->sendResponse($detail, 'Details retrieved successfully');
    }

    /**
     * Report spent dnalysis drill down Export
     * Post /reportSpentAnalysisDrilldownExport
     *
     * @param $request
     *
     * @return Response
     */

    public function reportSpentAnalysisDrilldownExport(Request $request)
    {

        $input = $request->all();

        $monthExp = explode('-', $input['month']);

        $expYear = $monthExp[0];
        $expMonth = $monthExp[1];

        $type = $request->type;

        $commaSeperatedYears = join($input['years'], ",");
        $commaSeperatedCompany = join($input['companySystemID'], ",");

        $supplierID = $input['supplierID'];

        if ($input['documentId'] == 1) {

            $detail = DB::select('SELECT
	GRVDet.*, PODet.purchaseOrderMasterID,
	PODet.companyID,
	PODet.supplierID,
	erp_purchaseordermaster.supplierPrimaryCode,
	erp_purchaseordermaster.supplierName,
	PODet.POlocalAmount,
	PODet.PORptAmount,
	warehousemaster.wareHouseDescription
FROM
	erp_purchaseordermaster
INNER JOIN (
	SELECT
		erp_purchaseorderdetails.purchaseOrderMasterID,
		erp_purchaseordermaster.companyID,
		erp_purchaseordermaster.supplierID,
		erp_purchaseordermaster.approvedDate,
		sum(
			GRVcostPerUnitLocalCur * noQty
		) AS POlocalAmount,
		sum(
			GRVcostPerUnitComRptCur * noQty
		) AS PORptAmount
	FROM
		erp_purchaseorderdetails
	INNER JOIN erp_purchaseordermaster ON erp_purchaseordermaster.purchaseOrderID = erp_purchaseorderdetails.purchaseOrderMasterID
	WHERE
		erp_purchaseordermaster.approved = - 1
	AND erp_purchaseordermaster.poCancelledYN = 0
	GROUP BY
		erp_purchaseorderdetails.purchaseOrderMasterID
) AS PODet ON erp_purchaseordermaster.purchaseOrderID = PODet.purchaseOrderMasterID
INNER JOIN (
	SELECT
		erp_grvdetails.purchaseOrderMastertID,
		erp_grvdetails.itemPrimaryCode,
        erp_grvdetails.itemDescription,
		erp_grvmaster.grvDate,
		erp_grvmaster.grvPrimaryCode,
		erp_grvmaster.grvConfirmedYN,
		erp_grvmaster.approved as grvApproved,
		erp_grvmaster.companySystemID,
		erp_grvmaster.grvLocation,
		supplierID,
		approvedDate,
		GRVcostPerUnitLocalCur,
		GRVcostPerUnitComRptCur,
		noQty,
		sum(
			GRVcostPerUnitLocalCur * noQty
		) AS LinelocalTotal,
		sum(
			GRVcostPerUnitComRptCur * noQty
		) AS LineRptTotal
	FROM
		erp_grvdetails
	INNER JOIN erp_grvmaster ON erp_grvmaster.grvAutoID = erp_grvdetails.grvAutoID
	WHERE
		erp_grvmaster.approved = - 1
	AND erp_grvmaster.grvCancelledYN = 0
	AND YEAR (erp_grvmaster.grvDate) = (' . $expYear . ') AND MONTH (erp_grvmaster.grvDate) = (' . $expMonth . ')
	GROUP BY
		erp_grvdetails.purchaseOrderMastertID
) AS GRVDet ON GRVDet.purchaseOrderMastertID = erp_purchaseordermaster.purchaseOrderID
INNER JOIN warehousemaster ON GRVDet.grvLocation = warehousemaster.wareHouseSystemCode
WHERE
	erp_purchaseordermaster.approved = -1
AND erp_purchaseordermaster.poCancelledYN = 0
AND GRVDet.companySystemID IN (' . $commaSeperatedCompany . ')
AND PODet.supplierID = ' . $supplierID . '');

            $data = array();
            foreach ($detail as $order) {
                $testArray['GRV Date'] = date("d/m/Y", strtotime($order->grvDate));
                $testArray['GRV Number'] = $order->grvPrimaryCode;
                $testArray['Supplier Name'] = $order->supplierName;
                $testArray['WareHouse Name'] = $order->wareHouseDescription;
                $testArray['Item Code'] = $order->itemPrimaryCode;
                $testArray['Item Description'] = $order->itemDescription;
                if ($input['currency'] == 1) {
                    $testArray['Amount'] = number_format($order->LinelocalTotal, 2);
                } else {
                    $testArray['Amount'] = number_format($order->LineRptTotal, 2);
                }
                array_push($data, $testArray);
            }


        } else if ($input['documentId'] == 2) {
            $detail = DB::select('SELECT
	erp_bookinvsuppmaster.bookingSuppMasInvAutoID,
	erp_bookinvsuppmaster.companyID,
	erp_bookinvsuppdet.purchaseOrderID,
	erp_bookinvsuppmaster.documentID,
	erp_grvmaster.grvPrimaryCode,
	erp_bookinvsuppmaster.bookingInvCode,
	erp_bookinvsuppmaster.bookingDate,
	erp_bookinvsuppmaster.comments,
	erp_bookinvsuppmaster.supplierInvoiceNo,
	erp_bookinvsuppmaster.confirmedYN,
	erp_bookinvsuppmaster.confirmedByName,
	erp_bookinvsuppmaster.approved,
	currencymaster.CurrencyCode,
	erp_bookinvsuppdet.totLocalAmount,
	erp_bookinvsuppdet.totRptAmount
FROM
	erp_bookinvsuppmaster
INNER JOIN erp_bookinvsuppdet ON erp_bookinvsuppmaster.bookingSuppMasInvAutoID = erp_bookinvsuppdet.bookingSuppMasInvAutoID
LEFT JOIN currencymaster ON erp_bookinvsuppmaster.supplierTransactionCurrencyID = currencymaster.currencyID
LEFT JOIN erp_grvmaster ON erp_bookinvsuppdet.grvAutoID = erp_grvmaster.grvAutoID
WHERE
	erp_bookinvsuppmaster.supplierID =  ' . $supplierID . '
		AND YEAR (erp_bookinvsuppmaster.postedDate) = (' . $expYear . ') AND MONTH (erp_bookinvsuppmaster.postedDate) = (' . $expMonth . ')');

            $data = array();
            foreach ($detail as $order) {
                $testArray['GRV Code'] = $order->grvPrimaryCode;
                $testArray['Invoice Doc Code'] = $order->bookingInvCode;
                $testArray['Document Date'] = date("d/m/Y", strtotime($order->bookingDate));
                $testArray['Supplier Invoice No'] = $order->supplierInvoiceNo;
                $testArray['Comments'] = $order->comments;
                $testArray['Currency'] = $order->CurrencyCode;
                if ($input['currency'] == 1) {
                    $testArray['Amount'] = number_format($order->totLocalAmount, 2);
                } else {
                    $testArray['Amount'] = number_format($order->totRptAmount, 2);
                }
                array_push($data, $testArray);
            }
        }


        $csv = \Excel::create('item_wise_po_analysis', function ($excel) use ($data) {

            $excel->sheet('sheet name', function ($sheet) use ($data) {
                $sheet->fromArray($data);
                //$sheet->getStyle('A1')->getAlignment()->setWrapText(true);
                $sheet->setAutoSize(true);
                $sheet->getStyle('C1:C2')->getAlignment()->setWrapText(true);
            });
            $lastrow = $excel->getActiveSheet()->getHighestRow();
            $excel->getActiveSheet()->getStyle('A1:J' . $lastrow)->getAlignment()->setWrapText(true);
        })->download($type);
    }

    /**
     * manual Close Purchase Order
     * Post /manualCloseProcurementOrderPrecheck
     *
     * @param $request
     *
     * @return Response
     */

    public function manualCloseProcurementOrderPrecheck(Request $request)
    {
        $input = $request->all();
        $procumentOrder = $this->procumentOrderRepository->with(['created_by', 'confirmed_by'])->findWithoutFail($input['purchaseOrderID']);


        if (empty($procumentOrder)) {
            return $this->sendError('Procurement Order not found');
        }

        if ($procumentOrder->grvRecieved == 2) {
            return $this->sendError('You cannot close this order, this is already fully received');
        }

        if ($procumentOrder->manuallyClosed == 1) {
            return $this->sendError('You cannot close this order, this order already manually closed');
        }

        if ($procumentOrder->approved != -1 || $procumentOrder->poCancelledYN == -1) {
            return $this->sendError('You cannot close this order, this order is only approved');
        }

        if ($procumentOrder->approved != -1 || $procumentOrder->grvRecieved == 0) {
            return $this->sendError('You cannot close this order, You can only close partially received order');
        }


        return $this->sendResponse($procumentOrder, 'Details retrieved successfully');
    }

    public function getGRVDrilldownSpentAnalysisTotal(Request $request)
    {
        $input = $request->all();

        $commaSeperatedYears = join($input['years'], ",");
        $commaSeperatedCompany = join($input['companySystemID'], ",");

        $supplierID = $input['supplierID'];

        if ($input['documentId'] == 1) {

            $detail = DB::select('SELECT
	GRVDet.*, PODet.purchaseOrderMasterID,
	PODet.companyID,
	PODet.supplierID,
	erp_purchaseordermaster.supplierPrimaryCode,
	erp_purchaseordermaster.supplierName,
	PODet.POlocalAmount,
	PODet.PORptAmount,
	warehousemaster.wareHouseDescription
FROM
	erp_purchaseordermaster
INNER JOIN (
	SELECT
		erp_purchaseorderdetails.purchaseOrderMasterID,
		erp_purchaseordermaster.companyID,
		erp_purchaseordermaster.supplierID,
		erp_purchaseordermaster.approvedDate,
		sum(
			GRVcostPerUnitLocalCur * noQty
		) AS POlocalAmount,
		sum(
			GRVcostPerUnitComRptCur * noQty
		) AS PORptAmount
	FROM
		erp_purchaseorderdetails
	INNER JOIN erp_purchaseordermaster ON erp_purchaseordermaster.purchaseOrderID = erp_purchaseorderdetails.purchaseOrderMasterID
	WHERE
		erp_purchaseordermaster.approved = - 1
	AND erp_purchaseordermaster.poCancelledYN = 0
	GROUP BY
		erp_purchaseorderdetails.purchaseOrderMasterID
) AS PODet ON erp_purchaseordermaster.purchaseOrderID = PODet.purchaseOrderMasterID
INNER JOIN (
	SELECT
		erp_grvdetails.purchaseOrderMastertID,
		erp_grvdetails.itemPrimaryCode,
        erp_grvdetails.itemDescription,
		erp_grvmaster.grvDate,
		erp_grvmaster.grvPrimaryCode,
		erp_grvmaster.grvConfirmedYN,
		erp_grvmaster.approved as grvApproved,
		erp_grvmaster.companySystemID,
		erp_grvmaster.grvLocation,
		supplierID,
		approvedDate,
		GRVcostPerUnitLocalCur,
		GRVcostPerUnitComRptCur,
		noQty,
		sum(
			GRVcostPerUnitLocalCur * noQty
		) AS LinelocalTotal,
		sum(
			GRVcostPerUnitComRptCur * noQty
		) AS LineRptTotal
	FROM
		erp_grvdetails
	INNER JOIN erp_grvmaster ON erp_grvmaster.grvAutoID = erp_grvdetails.grvAutoID
	WHERE
		erp_grvmaster.approved = - 1
	AND erp_grvmaster.grvCancelledYN = 0
	AND YEAR (erp_grvmaster.grvDate) IN (' . $commaSeperatedYears . ')
	GROUP BY
		erp_grvdetails.purchaseOrderMastertID
) AS GRVDet ON GRVDet.purchaseOrderMastertID = erp_purchaseordermaster.purchaseOrderID
INNER JOIN warehousemaster ON GRVDet.grvLocation = warehousemaster.wareHouseSystemCode
WHERE
	erp_purchaseordermaster.approved = -1
AND erp_purchaseordermaster.poCancelledYN = 0
AND GRVDet.companySystemID IN (' . $commaSeperatedCompany . ')
AND PODet.supplierID = ' . $supplierID . '');
        } else if ($input['documentId'] == 2) {
            $detail = DB::select('SELECT
	erp_bookinvsuppmaster.bookingSuppMasInvAutoID,
	erp_bookinvsuppmaster.companyID,
	erp_bookinvsuppdet.purchaseOrderID,
	erp_bookinvsuppmaster.documentID,
	erp_grvmaster.grvPrimaryCode,
	erp_bookinvsuppmaster.bookingInvCode,
	erp_bookinvsuppmaster.bookingDate,
	erp_bookinvsuppmaster.comments,
	erp_bookinvsuppmaster.supplierInvoiceNo,
	erp_bookinvsuppmaster.confirmedYN,
	erp_bookinvsuppmaster.confirmedByName,
	erp_bookinvsuppmaster.approved,
	currencymaster.CurrencyCode,
	erp_bookinvsuppdet.totLocalAmount,
	erp_bookinvsuppdet.totRptAmount
FROM
	erp_bookinvsuppmaster
INNER JOIN erp_bookinvsuppdet ON erp_bookinvsuppmaster.bookingSuppMasInvAutoID = erp_bookinvsuppdet.bookingSuppMasInvAutoID
LEFT JOIN currencymaster ON erp_bookinvsuppmaster.supplierTransactionCurrencyID = currencymaster.currencyID
LEFT JOIN erp_grvmaster ON erp_bookinvsuppdet.grvAutoID = erp_grvmaster.grvAutoID
WHERE
	erp_bookinvsuppmaster.supplierID =  ' . $supplierID . ' AND erp_bookinvsuppmaster.companySystemID IN (' . $commaSeperatedCompany . ')
		AND YEAR (erp_bookinvsuppmaster.postedDate) IN (' . $commaSeperatedYears . ')');
        }
        return $this->sendResponse($detail, 'Details retrieved successfully');

    }

    /**
     * amend Procurement Order
     * Post /amendProcurementOrder
     *
     * @param $request
     *
     * @return Response
     */

    public function amendProcurementOrder(Request $request)
    {

        $input = $request->all();
        $procurementOrder = ProcumentOrder::with(['created_by', 'confirmed_by'])
            ->where('purchaseOrderID', $input['purchaseOrderID'])
            ->first();

        if (empty($procurementOrder)) {
            return $this->sendError('Procurement Order not found');
        }

        if ($procurementOrder->poConfirmedYN != 1) {
            return $this->sendError('You cannot amend this order, this is not confirm', 500);
        }

        if ($procurementOrder->poClosedYN == 1) {
            return $this->sendError('You cannot amend this order, this is already closed', 500);
        }

        if ($procurementOrder->manuallyClosed == 1) {
            return $this->sendError('You cannot amend this order, this order manually closed');
        }

        if ($procurementOrder->grvRecieved != 0) {
            return $this->sendError('You cannot amend this order. GRV is fully or partially received.', 500);
        }

        if ($procurementOrder->poCancelledYN == -1) {
            return $this->sendError('You cannot amend this order, this is already canceled', 500);
        }

        $employee = \Helper::getEmployeeInfo();

        if ($procurementOrder->WO_amendYN == -1 && $procurementOrder->WO_amendRequestedByEmpID != $employee->empID) {

            $amendEmpName = $procurementOrder->WO_amendRequestedByEmpID;
            $amendEmp = Employee::where('empID', '=', $amendEmpName)->first();

            if ($amendEmp) {
                $amendEmpName = $amendEmp->empName;
                return $this->sendError('You cannot amend this order, this is already amending by ' . $amendEmpName, 500);
            }

            return $this->sendError('You cannot amend this order, this is already amending.', 500);
        }

        $procurementOrder->WO_amendYN = -1;
        $procurementOrder->WO_confirmedYN = 0;
        $procurementOrder->WO_amendRequestedByEmpSystemID = $employee->employeeSystemID;
        $procurementOrder->WO_amendRequestedByEmpID = $employee->empID;
        $procurementOrder->WO_amendRequestedDate = now();
        $procurementOrder->save();

        return $this->sendResponse($procurementOrder, 'Order updated successfully');
    }


    /**
     * amend Procurement Order pre check
     * Post /amendProcurementOrder
     *
     * @param $request
     *
     * @return Response
     */

    public function amendProcurementOrderPreCheck(Request $request)
    {

        $input = $request->all();
        $procurementOrder = ProcumentOrder::with(['created_by', 'confirmed_by'])
            ->where('purchaseOrderID', $input['purchaseOrderID'])
            ->first();

        if (empty($procurementOrder)) {
            return $this->sendError('Procurement Order not found');
        }

        if ($procurementOrder->poConfirmedYN != 1) {
            return $this->sendError('You cannot amend this order, this is not confirm', 500);
        }

        if ($procurementOrder->poClosedYN == 1) {
            return $this->sendError('You cannot amend this order, this is already closed', 500);
        }

        if ($procurementOrder->manuallyClosed == 1) {
            return $this->sendError('You cannot amend this order, this order manually closed');
        }

        if ($procurementOrder->grvRecieved != 0) {
            return $this->sendError('You cannot amend this order. GRV is fully or partially received.', 500);
        }

        if ($procurementOrder->poCancelledYN == -1) {
            return $this->sendError('You cannot amend this order, this is already canceled', 500);
        }

        $employee = \Helper::getEmployeeInfo();
        if ($procurementOrder->WO_amendYN == -1 && $procurementOrder->WO_amendRequestedByEmpID != $employee->empID) {

            $amendEmpName = $procurementOrder->WO_amendRequestedByEmpID;
            $amendEmp = Employee::where('empID', '=', $amendEmpName)->first();

            if ($amendEmp) {
                $amendEmpName = $amendEmp->empName;
                return $this->sendError('You cannot amend this order, this is already amending by ' . $amendEmpName, 500);
            }

            return $this->sendError('You cannot amend this order, this is already amending.', 500);
        }

        return $this->sendResponse($procurementOrder, 'Order updated successfully');
    }

    /**
     * Display the specified Procument Order Pr history.
     * GET|HEAD /procumentOrderPrHistory
     *
     * @param $request
     *
     * @return Response
     */

    public function procumentOrderPrHistory(Request $request)
    {
        $id = $request->get('id');

        /** @var ProcumentOrder $procumentOrder */
        $procumentOrder = $this->procumentOrderRepository->with(['created_by', 'confirmed_by', 'segment', 'company', 'detail' => function ($q) {

            $q->with(['unit', 'requestDetail.purchase_request.confirmed_by']);

        }])->findWithoutFail($id);

        if (empty($procumentOrder)) {
            return $this->sendError('Procurement Order not found');
        }

        return $this->sendResponse($procumentOrder->toArray(), 'Procurement Order retrieved successfully');
    }

    /**
     * changes the Po supplier
     * Post /procumentOrderChangeSupplier
     * @param $request
     *
     * @return Response
     */


    public function procumentOrderChangeSupplier(Request $request)
    {
        $id = $request->get('id');

        $input = $request->all();
        $input = $this->convertArrayToValue($input);


        /** @var ProcumentOrder $procumentOrder */
        $purchaseOrder = ProcumentOrder::where('purchaseOrderID', $id)->first();

        if (empty($purchaseOrder)) {
            return $this->sendError('Procurement Order not found');
        }

        $input['companySystemID'] = $purchaseOrder->companySystemID;

        $supplier = SupplierMaster::where('supplierCodeSystem', $input['supplierID'])->first();

        if (empty($supplier)) {
            return $this->sendError('Supplier not found');
        }

        if ($supplier) {
            $purchaseOrder->supplierID = $input['supplierID'];
            $purchaseOrder->supplierPrimaryCode = $supplier->primarySupplierCode;
            $purchaseOrder->supplierName = $supplier->supplierName;
            $purchaseOrder->supplierAddress = $supplier->address;
            $purchaseOrder->supplierTelephone = $supplier->telephone;
            $purchaseOrder->supplierFax = $supplier->fax;
            $purchaseOrder->supplierEmail = $supplier->supEmail;
            $purchaseOrder->creditPeriod = $supplier->creditPeriod;
        }

        $currency = SupplierCurrency::where('supplierCodeSystem', $input['supplierTransactionCurrencyID'])->first();

        if (empty($currency)) {
            return $this->sendError('Currency not found');
        }

        $purchaseOrder->supplierTransactionCurrencyID = $input['supplierTransactionCurrencyID'];


        $supplierCurrency = SupplierCurrency::where('supplierCodeSystem', $input['supplierID'])
            ->where('isDefault', -1)
            ->first();

        if ($supplierCurrency) {
            $purchaseOrder->supplierDefaultCurrencyID = $supplierCurrency->currencyID;
            $purchaseOrder->supplierTransactionER = 1;
            $erCurrency = CurrencyMaster::where('currencyID', $supplierCurrency->currencyID)->first();
            $purchaseOrder->supplierDefaultER = $erCurrency->ExchangeRate;
        }

        $supplierAssignedDetai = SupplierAssigned::where('supplierCodeSytem', $input['supplierID'])
            ->where('companySystemID', $purchaseOrder->companySystemID)
            ->first();

        if ($supplierAssignedDetai) {
            $purchaseOrder->supplierVATEligible = $supplierAssignedDetai->vatEligible;
            $purchaseOrder->VATPercentage = $supplierAssignedDetai->vatPercentage;
        }

        if ($purchaseOrder->supplierVATEligible == 1) {
            $currencyConversionVatAmount = \Helper::currencyConversion($input['companySystemID'], $purchaseOrder->supplierTransactionCurrencyID, $purchaseOrder->supplierTransactionCurrencyID, $purchaseOrder->VATAmount);
            $purchaseOrder->VATAmountLocal = round($currencyConversionVatAmount['localAmount'], 8);
            $purchaseOrder->VATAmountRpt = round($currencyConversionVatAmount['reportingAmount'], 8);
        } else {
            $purchaseOrder->VATAmountLocal = 0;
            $purchaseOrder->VATAmountRpt = 0;
        }

        //getting total sum of PO detail Amount
        $poMasterSum = PurchaseOrderDetails::select(DB::raw('COALESCE(SUM(netAmount),0) as masterTotalSum'))
            ->where('purchaseOrderMasterID', $purchaseOrder->purchaseOrderID)
            ->first();

        $poMasterSumDeducted = ($poMasterSum['masterTotalSum'] - $purchaseOrder->poDiscountAmount) + $purchaseOrder->VATAmount;

        $currencyConversionMaster = \Helper::currencyConversion($input["companySystemID"], $purchaseOrder->supplierTransactionCurrencyID, $purchaseOrder->supplierTransactionCurrencyID, $poMasterSumDeducted);

        $purchaseOrder->poTotalComRptCurrency = round($currencyConversionMaster['reportingAmount'], 8);
        $purchaseOrder->poTotalLocalCurrency = round($currencyConversionMaster['localAmount'], 8);
        $purchaseOrder->poTotalSupplierDefaultCurrency = 0;
        $purchaseOrder->poTotalSupplierTransactionCurrency = round($poMasterSumDeducted, 8);
        $purchaseOrder->companyReportingER = round($currencyConversionMaster['trasToRptER'], 8);
        $purchaseOrder->localCurrencyER = round($currencyConversionMaster['trasToLocER'], 8);


        $purchaseOrder->save();

        foreach ($purchaseOrder->detail as $item) {

            $purchaseOrderDetail = PurchaseOrderDetails::where('purchaseOrderDetailsID', $item->purchaseOrderDetailsID)->first();

            $purchaseOrderDetail->supplierItemCurrencyID = $purchaseOrder->supplierTransactionCurrencyID;
            $purchaseOrderDetail->foreignToLocalER = $purchaseOrder->supplierTransactionER;

            $purchaseOrderDetail->supplierDefaultCurrencyID = $purchaseOrder->supplierDefaultCurrencyID;
            $purchaseOrderDetail->supplierDefaultER = $purchaseOrder->supplierDefaultER;

            $purchaseOrderDetail->companyReportingER = $purchaseOrder->companyReportingER;
            $purchaseOrderDetail->localCurrencyER = $purchaseOrder->localCurrencyER;


            if ($purchaseOrder->poDiscountAmount > 0) {
                $calculateItemDiscount = (($purchaseOrderDetail->netAmount - (($purchaseOrder->poDiscountAmount / $purchaseOrder->poTotalSupplierTransactionCurrency) * $purchaseOrderDetail->netAmount)) / $purchaseOrderDetail->noQty);
            } else {
                $calculateItemDiscount = $purchaseOrderDetail->unitCost - $purchaseOrderDetail->discountAmount;
            }
            $calculateItemTax = (($purchaseOrder->VATPercentage / 100) * $calculateItemDiscount) + $calculateItemDiscount;

            $currencyConversion = \Helper::currencyConversion($purchaseOrderDetail->companySystemID, $purchaseOrder->supplierTransactionCurrencyID, $purchaseOrder->supplierTransactionCurrencyID, $calculateItemTax);

            $vatLineAmount = ($calculateItemTax - $calculateItemDiscount);


            $purchaseOrderDetail->GRVcostPerUnitLocalCur = round($currencyConversion['localAmount'], 8);
            $purchaseOrderDetail->GRVcostPerUnitSupTransCur = round($calculateItemTax, 8);
            $purchaseOrderDetail->GRVcostPerUnitComRptCur = round($currencyConversion['reportingAmount'], 8);
            $purchaseOrderDetail->purchaseRetcostPerUnitLocalCur = round($currencyConversion['localAmount'], 8);
            $purchaseOrderDetail->purchaseRetcostPerUnitTranCur = round($calculateItemTax, 8);
            $purchaseOrderDetail->purchaseRetcostPerUnitRptCur = round($currencyConversion['reportingAmount'], 8);

            if ($purchaseOrder->supplierVATEligible == 1) {
                $currencyConversionForLineAmount = \Helper::currencyConversion($purchaseOrderDetail->companySystemID, $purchaseOrder->supplierTransactionCurrencyID, $purchaseOrder->supplierTransactionCurrencyID, $vatLineAmount);
                $purchaseOrderDetail->VATPercentage = round($purchaseOrder->VATPercentage, 8);
                $purchaseOrderDetail->VATAmount = round($vatLineAmount, 8);
                $purchaseOrderDetail->VATAmountLocal = round($currencyConversionForLineAmount['localAmount'], 8);
                $purchaseOrderDetail->VATAmountRpt = round($currencyConversionForLineAmount['reportingAmount'], 8);
            } else {
                $purchaseOrderDetail->VATPercentage = 0;
                $purchaseOrderDetail->VATAmount = 0;
                $purchaseOrderDetail->VATAmountLocal = 0;
                $purchaseOrderDetail->VATAmountRpt = 0;
            }

            // adding supplier Default CurrencyID base currency conversion
            if ($purchaseOrderDetail->unitCost > 0) {
                $currencyConversionDefault = \Helper::currencyConversion($input['companySystemID'], $purchaseOrder->supplierTransactionCurrencyID, $purchaseOrder->supplierDefaultCurrencyID, $purchaseOrderDetail->unitCost);
                $purchaseOrderDetail->GRVcostPerUnitSupDefaultCur = $currencyConversionDefault['documentAmount'];
                $purchaseOrderDetail->purchaseRetcostPerUniSupDefaultCur = $currencyConversionDefault['documentAmount'];
            }

            $purchaseOrderDetail->save();
        }


        return $this->sendResponse($purchaseOrder->toArray(), 'Procurement Order retrieved successfully');
    }

    /**
     * Display the specified Procurement Order Audit.
     * GET|HEAD /ProcurementOrderAudit
     * @param $request
     *
     * @return Response
     */
    public function ProcurementOrderAudit(Request $request)
    {

        $id = $request->get('id');

        $procumentOrder = $this->procumentOrderRepository->with(['created_by', 'confirmed_by',
            'cancelled_by', 'manually_closed_by', 'modified_by', 'sent_supplier_by', 'approved_by' => function ($query) {
                $query->with('employee')
                    ->whereIn('documentSystemID', [2, 5, 52]);
            }])->findWithoutFail($id);

        if (empty($procumentOrder)) {
            return $this->sendError('Procurement Order not found');
        }

        return $this->sendResponse($procumentOrder->toArray(), 'Purchase Order retrieved successfully');
    }

    public function getGRVBasedPODropdowns(Request $request)
    {
        $input = $request->all();

        $detail = DB::select('SELECT
	erp_grvmaster.grvAutoID,
	erp_grvmaster.grvPrimaryCode,
	erp_grvdetails.purchaseOrderMastertID
FROM
	erp_grvmaster
INNER JOIN erp_grvdetails ON erp_grvmaster.grvAutoID = erp_grvdetails.grvAutoID
WHERE
	erp_grvmaster.grvConfirmedYN = 1
AND erp_grvmaster.approved = 0
GROUP BY
	erp_grvmaster.grvAutoID,
	erp_grvmaster.grvPrimaryCode,
	erp_grvdetails.purchaseOrderMastertID
HAVING
	erp_grvdetails.purchaseOrderMastertID = ' . $input['poID'] . '
ORDER BY
	erp_grvmaster.grvAutoID DESC');

        return $this->sendResponse($detail, 'GRV Currencies retrieved successfully');
    }


}
