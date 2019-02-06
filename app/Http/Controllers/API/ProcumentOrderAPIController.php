<?php
/*/**
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
 * -- Date: 05-June 2018 By: Mubashir Description: Modified getProcumentOrderByDocumentType() to handle filters from local storage
 * -- Date: 14-june 2018 By: Nazir Description: Added new functions named as purchaseOrderForGRV(),
 * -- Date: 25-june 2018 By: Nazir Description: Added new functions named as getPurchasePaymentStatusHistory(),
 * -- Date: 26-june 2018 By: Nazir Description: Added new functions named as getProcurementOrderReopen(),
 * -- Date: 18-july 2018 By: Nazir Description: Added new functions named as procumentOrderPRAttachment(),
 * -- Date: 18-july 2018 By: Nazir Description: Added new functions named as updateSentSupplierDetail(),
 * -- Date: 20-July 2018 By: Nazir Description: Added new functions named as getProcurementOrderReferBack(),
 * -- Date: 30-July 2018 By: Nazir Description: Added new functions named as reportPoEmployeePerformance(),
 * -- Date: 31-July 2018 By: Nazir Description: Added new functions named as exportPoEmployeePerformance()
 * -- Date: 21-September 2018 By: fayas Description: Added new functions named as reportPoToPaymentFilterOptions(),reportPoToPayment(),
 *                                                   exportPoToPaymentReport()
 * -- Date: 21-September 2018 By: Nazir Description: Added new functions named as exportProcumentOrderMaster()
 * -- Date: 13-November 2018 By: Fayas Description: Added new functions named as getAdvancePaymentRequestStatusHistory(),
 */

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateProcumentOrderAPIRequest;
use App\Http\Requests\API\UpdateProcumentOrderAPIRequest;
use App\Models\AddonCostCategories;
use App\Models\Alert;
use App\Models\BookInvSuppDet;
use App\Models\DocumentAttachments;
use App\Models\DocumentReferedHistory;
use App\Models\Employee;
use App\Models\EmployeesDepartment;
use App\Models\Months;
use App\Models\Company;
use App\Models\PaySupplierInvoiceDetail;
use App\Models\PoAddons;
use App\Models\PoAddonsRefferedBack;
use App\Models\PoAdvancePayment;
use App\Models\PoPaymentTermsRefferedback;
use App\Models\PurchaseOrderAdvPaymentRefferedback;
use App\Models\PurchaseOrderDetailsRefferedHistory;
use App\Models\PurchaseOrderMasterRefferedHistory;
use App\Models\PurchaseRequest;
use App\Models\SupplierContactDetails;
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
use App\Models\BudgetConsumedData;
use App\Models\GRVMaster;
use App\Repositories\ProcumentOrderRepository;
use Illuminate\Http\Request;
use App\Repositories\UserRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\Storage;
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

        $input = $this->convertArrayToValue($input);
        if (isset($input['WO_PeriodFrom'])) {
            if ($input['WO_PeriodFrom']) {
                $input['WO_PeriodFrom'] = new Carbon($input['WO_PeriodFrom']);
                $WO_PeriodFrom = $input['WO_PeriodFrom'];
            }
        }

        if (isset($input['WO_PeriodTo'])) {
            if ($input['WO_PeriodTo']) {
                $input['WO_PeriodTo'] = new Carbon($input['WO_PeriodTo']);
                $WO_PeriodTo = $input['WO_PeriodTo'];
            }
        }

        $id = Auth::id();
        $user = $this->userRepository->with(['employee'])->findWithoutFail($id);

        if ($input['documentSystemID'] == 5) {
            if ($input['WO_PeriodFrom'] > $input['WO_PeriodTo']) {
                return $this->sendError('WO Period From cannot be greater than WO Period To', 500);
            }
        }

        $input['createdPcID'] = gethostname();
        $input['createdUserID'] = $user->employee['empID'];
        $input['createdUserSystemID'] = $user->employee['employeeSystemID'];
        $input['departmentID'] = 'PROC';

        if ($input['documentSystemID'] == 5 && $input['poType_N'] == 5) {
            $lastSerial = ProcumentOrder::where('companySystemID', $input['companySystemID'])
                ->where('documentSystemID', $input['documentSystemID'])
                ->where('poType_N', 5)
                ->orderBy('purchaseOrderID', 'desc')
                ->first();
        } else {
            $lastSerial = ProcumentOrder::where('companySystemID', $input['companySystemID'])
                ->where('documentSystemID', $input['documentSystemID'])
                ->orderBy('purchaseOrderID', 'desc')
                ->first();
        }


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

        $currencyConversionDefaultMaster = \Helper::currencyConversion($input['companySystemID'], $input['supplierTransactionCurrencyID'], $supplierCurrency->currencyID, 0);

        if ($currencyConversionDefaultMaster) {
            $input['supplierDefaultER'] = $currencyConversionDefaultMaster['transToDocER'];
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

        $input = array_except($input, ['created_by', 'confirmed_by', 'totalOrderAmount', 'segment', 'isAmendAccess']);
        $input = $this->convertArrayToValue($input);

        $procumentOrderUpdate = ProcumentOrder::where('purchaseOrderID', '=', $id)->first();

        if (isset($input['expectedDeliveryDate'])) {
            if ($input['expectedDeliveryDate']) {
                $input['expectedDeliveryDate'] = new Carbon($input['expectedDeliveryDate']);
            }
        }

        if (isset($input['WO_PeriodFrom'])) {
            if ($input['WO_PeriodFrom']) {
                $input['WO_PeriodFrom'] = new Carbon($input['WO_PeriodFrom']);
            }
        }

        if (isset($input['WO_PeriodTo'])) {
            if ($input['WO_PeriodTo']) {
                $input['WO_PeriodTo'] = new Carbon($input['WO_PeriodTo']);
            }
        }

        /** @var ProcumentOrder $procumentOrder */
        $procumentOrder = $this->procumentOrderRepository->findWithoutFail($id);

        if (empty($procumentOrder)) {
            return $this->sendError('Procurement Order not found');
        }

        if ($input['documentSystemID'] == 5 && $input['poType_N'] == 5) {
            if ($input['WO_PeriodFrom'] > $input['WO_PeriodTo']) {
                return $this->sendError('WO Period From cannot be greater than WO Period To');
            }

        }

        $oldPoTotalSupplierTransactionCurrency = $procumentOrder->poTotalSupplierTransactionCurrency;

        $employee = \Helper::getEmployeeInfo();
        $supplierCurrencyDecimalPlace = \Helper::getCurrencyDecimalPlace($procumentOrder->supplierTransactionCurrencyID);

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

        $currencyConversionDefaultMaster = \Helper::currencyConversion($input['companySystemID'], $input['supplierTransactionCurrencyID'], $supplierCurrency->currencyID, 0);

        if ($currencyConversionDefaultMaster) {
            $procumentOrderUpdate->supplierDefaultER = $currencyConversionDefaultMaster['transToDocER'];
        }

        //getting total sum of PO detail Amount
        $poMasterSum = PurchaseOrderDetails::select(DB::raw('COALESCE(SUM(netAmount),0) as masterTotalSum'))
            ->where('purchaseOrderMasterID', $input['purchaseOrderID'])
            ->first();

        //getting addon Total for PO
        $poAddonMasterSum = PoAddons::select(DB::raw('COALESCE(SUM(amount),0) as addonTotalSum'))
            ->where('poId', $input['purchaseOrderID'])
            ->first();

        $poMasterSumRounded = round($poMasterSum['masterTotalSum'], $supplierCurrencyDecimalPlace);
        $poAddonMasterSumRounded = round($poAddonMasterSum['addonTotalSum'], $supplierCurrencyDecimalPlace);


        $newlyUpdatedPoTotalAmount = $poMasterSumRounded + $poAddonMasterSumRounded;

        if ($input['poDiscountAmount'] > $newlyUpdatedPoTotalAmount) {
            return $this->sendError('Discount Amount should be less than order amount.', 500);
        }

        $poMasterSumDeducted = ($newlyUpdatedPoTotalAmount - $input['poDiscountAmount']) + $input['VATAmount'];

        $input['poTotalSupplierTransactionCurrency'] = $poMasterSumDeducted;

        $currencyConversionMaster = \Helper::currencyConversion($input["companySystemID"], $input['supplierTransactionCurrencyID'], $input['supplierTransactionCurrencyID'], $poMasterSumDeducted);

        $procumentOrderUpdate->poTotalComRptCurrency = \Helper::roundValue($currencyConversionMaster['reportingAmount']);
        $procumentOrderUpdate->poTotalLocalCurrency = \Helper::roundValue($currencyConversionMaster['localAmount']);
        $procumentOrderUpdate->poTotalSupplierTransactionCurrency = $poMasterSumDeducted;
        $procumentOrderUpdate->companyReportingER = round($currencyConversionMaster['trasToRptER'], 8);
        $procumentOrderUpdate->localCurrencyER = round($currencyConversionMaster['trasToLocER'], 8);


        // updating coloum
        if ($input['documentSystemID'] != 5 && $input['poType_N'] != 5) {
            $procumentOrderUpdate->WO_PeriodFrom = null;
            $procumentOrderUpdate->WO_PeriodTo = null;
        }

        // calculating total Supplier Default currency

        $currencyConversionMaster = \Helper::currencyConversion($input["companySystemID"], $supplierCurrency->currencyID, $input['supplierTransactionCurrencyID'], $poMasterSumDeducted);

        $procumentOrderUpdate->poTotalSupplierDefaultCurrency = \Helper::roundValue($currencyConversionMaster['documentAmount']);

        if ($procumentOrder->supplierID != $input['supplierID']) {
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
                $procumentOrderUpdate->VATPercentage = $supplierAssignedDetai->vatPercentage;
            }
        }

        if ($procumentOrder->companySystemID != $input['companySystemID']) {

            $company = Company::where('companySystemID', $input['companySystemID'])->first();
            if ($company) {
                $procumentOrderUpdate->vatRegisteredYN = $company->vatRegisteredYN;
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


        // calculate total Tax for item if
        if ($input['supplierVATEligible'] == 1 && $input['vatRegisteredYN'] == 0) {

            if (!empty($updateDetailDiscount)) {
                foreach ($updateDetailDiscount as $itemDiscont) {

                    if ($input['poDiscountAmount'] > 0) {

                        $calculateItemDiscount = (($itemDiscont['netAmount'] - (($input['poDiscountAmount'] / $poMasterSumRounded) * $itemDiscont['netAmount'])) / $itemDiscont['noQty']);
                    } else {
                        $calculateItemDiscount = $itemDiscont['unitCost'] - $itemDiscont['discountAmount'];
                    }
                    $calculateItemTax = (($input['VATPercentage'] / 100) * $calculateItemDiscount) + $calculateItemDiscount;

                    $currencyConversion = \Helper::currencyConversion($itemDiscont['companySystemID'], $input['supplierTransactionCurrencyID']
                        , $input['supplierTransactionCurrencyID'], $calculateItemTax);

                    //$detail['netAmount'] = $calculateItemTax * $itemDiscont['noQty'];

                    $vatLineAmount = ($calculateItemTax - $calculateItemDiscount);

                    $currencyConversionForLineAmount = \Helper::currencyConversion($itemDiscont['companySystemID'], $input['supplierTransactionCurrencyID'], $input['supplierTransactionCurrencyID'], $vatLineAmount);

                    $currencyConversionLineDefault = \Helper::currencyConversion($input['companySystemID'], $input['supplierTransactionCurrencyID'], $input['supplierDefaultCurrencyID'], $calculateItemTax);


                    PurchaseOrderDetails::where('purchaseOrderDetailsID', $itemDiscont['purchaseOrderDetailsID'])
                        ->update([
                            'GRVcostPerUnitLocalCur' => \Helper::roundValue($currencyConversion['localAmount']),
                            'GRVcostPerUnitSupDefaultCur' => \Helper::roundValue($currencyConversionLineDefault['documentAmount']),
                            'GRVcostPerUnitSupTransCur' => \Helper::roundValue($calculateItemTax),
                            'GRVcostPerUnitComRptCur' => \Helper::roundValue($currencyConversion['reportingAmount']),
                            'purchaseRetcostPerUniSupDefaultCur' => \Helper::roundValue($currencyConversionLineDefault['documentAmount']),
                            'purchaseRetcostPerUnitLocalCur' => \Helper::roundValue($currencyConversion['localAmount']),
                            'purchaseRetcostPerUnitTranCur' => \Helper::roundValue($calculateItemTax),
                            'purchaseRetcostPerUnitRptCur' => \Helper::roundValue($currencyConversion['reportingAmount']),
                            'VATPercentage' => $input['VATPercentage'],
                            'VATAmount' => \Helper::roundValue($vatLineAmount),
                            'VATAmountLocal' => \Helper::roundValue($currencyConversionForLineAmount['localAmount']),
                            'VATAmountRpt' => \Helper::roundValue($currencyConversionForLineAmount['reportingAmount'])
                        ]);
                }
            }

        } else {
            if (!empty($updateDetailDiscount)) {
                foreach ($updateDetailDiscount as $itemDiscont) {

                    if ($input['poDiscountAmount'] > 0) {

                        $calculateItemDiscount = (($itemDiscont['netAmount'] - (($input['poDiscountAmount'] / $poMasterSumRounded) * $itemDiscont['netAmount'])) / $itemDiscont['noQty']);
                    } else {
                        $calculateItemDiscount = $itemDiscont['unitCost'] - $itemDiscont['discountAmount'];
                    }

                    $currencyConversion = \Helper::currencyConversion($itemDiscont['companySystemID'], $input['supplierTransactionCurrencyID']
                        , $input['supplierTransactionCurrencyID'], $calculateItemDiscount);

                    $currencyConversionLineDefault = \Helper::currencyConversion($input['companySystemID'], $input['supplierTransactionCurrencyID'], $input['supplierDefaultCurrencyID'], $calculateItemDiscount);

                    PurchaseOrderDetails::where('purchaseOrderDetailsID', $itemDiscont['purchaseOrderDetailsID'])
                        ->update([
                            'GRVcostPerUnitLocalCur' => \Helper::roundValue($currencyConversion['localAmount']),
                            'GRVcostPerUnitSupDefaultCur' => \Helper::roundValue($currencyConversionLineDefault['documentAmount']),
                            'GRVcostPerUnitSupTransCur' => \Helper::roundValue($calculateItemDiscount),
                            'GRVcostPerUnitComRptCur' => \Helper::roundValue($currencyConversion['reportingAmount']),
                            'purchaseRetcostPerUniSupDefaultCur' => \Helper::roundValue($currencyConversionLineDefault['documentAmount']),
                            'purchaseRetcostPerUnitLocalCur' => \Helper::roundValue($currencyConversion['localAmount']),
                            'purchaseRetcostPerUnitTranCur' => \Helper::roundValue($calculateItemDiscount),
                            'purchaseRetcostPerUnitRptCur' => \Helper::roundValue($currencyConversion['reportingAmount']),
                            //'VATPercentage' => $procumentOrder->VATPercentage,
                            'VATPercentage' => 0,
                            'VATAmount' => 0,
                            'VATAmountLocal' => 0,
                            'VATAmountRpt' => 0
                        ]);
                }
            }
        }

        //updating detail level exchange rate
        if (!empty($updateDetailDiscount)) {
            foreach ($updateDetailDiscount as $itemDiscont) {
                PurchaseOrderDetails::where('purchaseOrderDetailsID', $itemDiscont['purchaseOrderDetailsID'])
                    ->update([
                        'supplierDefaultER' => $procumentOrderUpdate->supplierDefaultER,
                        'companyReportingER' => $procumentOrderUpdate->companyReportingER,
                        'localCurrencyER' => $procumentOrderUpdate->localCurrencyER,
                    ]);
            }
        }

        //updating addons detail for line item
        $getPoDetailForAddon = PurchaseOrderDetails::where('purchaseOrderMasterID', $purchaseOrderID)
            ->get();

        $getPoAllAddons = PoAddons::where('poId', $purchaseOrderID)
            ->get();

        if ($poMasterSumRounded > 0) {

            if (!empty($getPoAllAddons)) {

                if (!empty($getPoDetailForAddon)) {
                    foreach ($getPoDetailForAddon as $AddonDeta) {

                        if ($AddonDeta['noQty'] > 0) {

                            $calculateAddonLineAmount = (($poAddonMasterSumRounded / $poMasterSumRounded) * $AddonDeta['netAmount']) / $AddonDeta['noQty'];

                            $currencyConversionForLineAmountAddon = \Helper::currencyConversion($input['companySystemID'], $input['supplierTransactionCurrencyID'], $input['supplierTransactionCurrencyID'], $calculateAddonLineAmount);

                            $currencyConversionLineAmountAddonDefault = \Helper::currencyConversion($input['companySystemID'], $input['supplierTransactionCurrencyID'], $input['supplierDefaultCurrencyID'], $calculateAddonLineAmount);

                            $updatePoDetailAddonDetail = PurchaseOrderDetails::find($AddonDeta['purchaseOrderDetailsID']);

                            $GRVcostPerUnitLocalCurAddon = ($AddonDeta['GRVcostPerUnitLocalCur'] + $currencyConversionForLineAmountAddon['localAmount']);
                            $updatePoDetailAddonDetail->GRVcostPerUnitLocalCur = \Helper::roundValue($GRVcostPerUnitLocalCurAddon);

                            $GRVcostPerUnitSupDefaultCurAddon = ($AddonDeta['GRVcostPerUnitSupDefaultCur'] + $currencyConversionLineAmountAddonDefault['documentAmount']);
                            $updatePoDetailAddonDetail->GRVcostPerUnitSupDefaultCur = \Helper::roundValue($GRVcostPerUnitSupDefaultCurAddon);

                            $GRVcostPerUnitSupTransCurAddon = ($AddonDeta['GRVcostPerUnitSupTransCur'] + $calculateAddonLineAmount);
                            $updatePoDetailAddonDetail->GRVcostPerUnitSupTransCur = \Helper::roundValue($GRVcostPerUnitSupTransCurAddon);

                            $GRVcostPerUnitComRptCurAddon = ($AddonDeta['GRVcostPerUnitComRptCur'] + $currencyConversionForLineAmountAddon['reportingAmount']);
                            $updatePoDetailAddonDetail->GRVcostPerUnitComRptCur = \Helper::roundValue($GRVcostPerUnitComRptCurAddon);

                            $purchaseRetcostPerUniSupDefaultCurAddon = ($AddonDeta['purchaseRetcostPerUniSupDefaultCur'] + $currencyConversionLineAmountAddonDefault['documentAmount']);
                            $updatePoDetailAddonDetail->purchaseRetcostPerUniSupDefaultCur = \Helper::roundValue($purchaseRetcostPerUniSupDefaultCurAddon);

                            $purchaseRetcostPerUnitLocalCurAddon = ($AddonDeta['purchaseRetcostPerUnitLocalCur'] + $currencyConversionForLineAmountAddon['localAmount']);
                            $updatePoDetailAddonDetail->purchaseRetcostPerUnitLocalCur = \Helper::roundValue($purchaseRetcostPerUnitLocalCurAddon);

                            $purchaseRetcostPerUnitTranCurAddon = ($AddonDeta['purchaseRetcostPerUnitTranCur'] + $calculateAddonLineAmount);
                            $updatePoDetailAddonDetail->purchaseRetcostPerUnitTranCur = \Helper::roundValue($purchaseRetcostPerUnitTranCurAddon);

                            $purchaseRetcostPerUnitRptCur = ($AddonDeta['purchaseRetcostPerUnitRptCur'] + $currencyConversionForLineAmountAddon['reportingAmount']);
                            $updatePoDetailAddonDetail->purchaseRetcostPerUnitRptCur = \Helper::roundValue($purchaseRetcostPerUnitRptCur);

                            $updatePoDetailAddonDetail->addonDistCost = \Helper::roundValue($calculateAddonLineAmount);
                            $updatePoDetailAddonDetail->addonPurchaseReturnCost = \Helper::roundValue($calculateAddonLineAmount);
                            $updatePoDetailAddonDetail->save();
                        }
                    }

                }

            }
        }

        if ($input['supplierVATEligible'] == 1) {

            if (!empty($updateDetailDiscount)) {
                foreach ($updateDetailDiscount as $itemDiscont) {

                    if ($input['poDiscountAmount'] > 0) {

                        $calculateItemDiscount = (($itemDiscont['netAmount'] - (($input['poDiscountAmount'] / $poMasterSumRounded) * $itemDiscont['netAmount'])) / $itemDiscont['noQty']);
                    } else {
                        $calculateItemDiscount = $itemDiscont['unitCost'] - $itemDiscont['discountAmount'];
                    }

                    $calculateItemTax = (($input['VATPercentage'] / 100) * $calculateItemDiscount) + $calculateItemDiscount;

                    $vatLineAmount = ($calculateItemTax - $calculateItemDiscount);

                    $currencyConversionForLineAmount = \Helper::currencyConversion($itemDiscont['companySystemID'], $input['supplierTransactionCurrencyID'], $input['supplierTransactionCurrencyID'], $vatLineAmount);

                    PurchaseOrderDetails::where('purchaseOrderDetailsID', $itemDiscont['purchaseOrderDetailsID'])
                        ->update([
                            'VATPercentage' => $input['VATPercentage'],
                            'VATAmount' => \Helper::roundValue($vatLineAmount),
                            'VATAmountLocal' => \Helper::roundValue($currencyConversionForLineAmount['localAmount']),
                            'VATAmountRpt' => \Helper::roundValue($currencyConversionForLineAmount['reportingAmount'])
                        ]);
                }
            }
        }
        //calculate tax amount according to the percantage for tax update


        //if($purchaseOrder->VATPercentage > 0 && $purchaseOrder->supplierVATEligible == 1 && $purchaseOrder->vatRegisteredYN == 0){
        if ($input['VATPercentage'] > 0 && $input['supplierVATEligible'] == 1) {
            $calculatVatAmount = ($poMasterSum['masterTotalSum'] - $input['poDiscountAmount']) * ($input['VATPercentage'] / 100);

            $currencyConversionVatAmount = \Helper::currencyConversion($input['companySystemID'], $input['supplierTransactionCurrencyID'], $input['supplierTransactionCurrencyID'], $input['VATAmount']);

            $procumentOrderUpdate->VATAmount = $input['VATAmount'];
            $procumentOrderUpdate->VATAmountLocal = \Helper::roundValue($currencyConversionVatAmount['localAmount']);
            $procumentOrderUpdate->VATAmountRpt = \Helper::roundValue($currencyConversionVatAmount['reportingAmount']);

        } else {
            $procumentOrderUpdate->VATAmount = 0;
            $procumentOrderUpdate->VATAmountLocal = 0;
            $procumentOrderUpdate->VATAmountRpt = 0;
        }

        if (($procumentOrder->poConfirmedYN == 0 && $input['poConfirmedYN'] == 1) || $isAmendAccess == 1) {

            $poDetailExist = PurchaseOrderDetails::select(DB::raw('purchaseOrderDetailsID'))
                ->where('purchaseOrderMasterID', $input['purchaseOrderID'])
                ->first();

            if (empty($poDetailExist)) {
                return $this->sendError('Order cannot be confirmed without any details');
            }

            $checkQuantity = PurchaseOrderDetails::where('purchaseOrderMasterID', $id)
                ->where('noQty', '<', 0.1)
                ->count();

            if ($checkQuantity > 0) {
                return $this->sendError('Every item should have at least one minimum qty requested', 500);
            }

            //checking atleast one po payment terms should exist
            $PoPaymentTerms = PoPaymentTerms::where('poID', $input['purchaseOrderID'])
                ->first();

            if (empty($PoPaymentTerms)) {
                return $this->sendError('PO should have at least one payment term');
            }

            // checking payment term amount value 0

            $checkPoPaymentTermsAmount = PoPaymentTerms::where('poID', $id)
                ->where('comAmount', '<', 1)
                ->count();

            if ($checkPoPaymentTermsAmount > 0) {
                return $this->sendError('You cannot confirm payment term with 0 amount', 500);
            }

            //po payment terms exist
            $PoPaymentTerms = PoPaymentTerms::where('poID', $input['purchaseOrderID'])
                ->where('LCPaymentYN', 2)
                ->where('isRequested', 0)
                ->first();

            if (!empty($PoPaymentTerms)) {
                return $this->sendError('Advance payment request is pending');
            }

            //getting total sum of Po Payment Terms
            $paymentTotalSum = PoPaymentTerms::select(DB::raw('IFNULL(SUM(comAmount),0) as paymentTotalSum'))
                ->where('poID', $input['purchaseOrderID'])
                ->first();

            //return floatval($poMasterSumDeducted)." - ".floatval($paymentTotalSum['paymentTotalSum']);

            //return $poMasterSumDeducted.'-'.$paymentTotalSum['paymentTotalSum'];
            if (abs(($poMasterSumDeducted - $paymentTotalSum['paymentTotalSum']) / $paymentTotalSum['paymentTotalSum']) < 0.00001) {

            } else {
                return $this->sendError('Payment terms total is not matching with the PO total');
            }

            $poAdvancePaymentType = PoPaymentTerms::where("poID", $input['purchaseOrderID'])
                ->get();

            $detailSum = PurchaseOrderDetails::select(DB::raw('sum(netAmount) as total'))
                ->where('purchaseOrderMasterID', $input['purchaseOrderID'])
                ->first();


            if (!empty($poAdvancePaymentType)) {
                foreach ($poAdvancePaymentType as $payment) {
                    $paymentPercentageAmount = ($payment['comPercentage'] / 100) * (($newlyUpdatedPoTotalAmount - $input['poDiscountAmount']) + $input['VATAmount']);

                    if (abs(($payment['comAmount'] - $paymentPercentageAmount) / $paymentPercentageAmount) < 0.00001) {

                    } else {
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
                $params = array('autoID' => $id, 'company' => $input["companySystemID"], 'document' => $input["documentSystemID"], 'segment' => $input["serviceLineSystemID"], 'category' => $input["financeCategory"], 'amount' => $procumentOrderUpdate->poTotalLocalCurrency);
                $confirm = \Helper::confirmDocument($params);
                if (!$confirm["success"]) {
                    return $this->sendError($confirm["message"]);
                } else {
                    $procumentOrderUpdate->WO_confirmedYN = 1;
                    $procumentOrderUpdate->WO_confirmedDate = now();
                    $procumentOrderUpdate->WO_confirmedByEmpID = $employee->empID;
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

            //adding budget consume table
            $idsDeleted = array();
            if ($procumentOrder->approved == -1) {

                $budgetDetail = BudgetConsumedData::where('companySystemID', $procumentOrder->companySystemID)
                    ->where('documentSystemCode', $procumentOrder->purchaseOrderID)
                    ->where('documentSystemID', $procumentOrder->documentSystemID)
                    ->get();

                if (!empty($budgetDetail)) {
                    foreach ($budgetDetail as $bd) {
                        array_push($idsDeleted, $bd->budgetConsumedDataAutoID);
                    }
                    BudgetConsumedData::destroy($idsDeleted);
                }


                // insert the record to budget consumed data
                $budgetConsumeData = array();
                $poMaster = ProcumentOrder::selectRaw('MONTH(createdDateTime) as month, purchaseOrderCode,documentID,documentSystemID, financeCategory')->find($procumentOrder->purchaseOrderID);

                if ($poMaster->financeCategory == 3) {
                    $poDetail = \DB::select('SELECT SUM(erp_purchaseorderdetails.GRVcostPerUnitLocalCur*erp_purchaseorderdetails.noQty) as GRVcostPerUnitLocalCur,SUM(erp_purchaseorderdetails.GRVcostPerUnitComRptCur*erp_purchaseorderdetails.noQty) as GRVcostPerUnitComRptCur,erp_purchaseorderdetails.companyReportingCurrencyID,erp_purchaseorderdetails.financeGLcodePLSystemID,erp_purchaseorderdetails.financeGLcodePL,erp_purchaseorderdetails.companyID,erp_purchaseorderdetails.companySystemID,erp_purchaseorderdetails.serviceLineSystemID,erp_purchaseorderdetails.serviceLineCode,erp_purchaseorderdetails.budgetYear,erp_purchaseorderdetails.localCurrencyID FROM erp_purchaseorderdetails INNER JOIN erp_purchaseordermaster ON erp_purchaseordermaster.purchaseOrderID = erp_purchaseorderdetails.purchaseOrderMasterID  WHERE erp_purchaseorderdetails.purchaseOrderMasterID = ' . $procumentOrder->purchaseOrderID . ' AND erp_purchaseordermaster.poType_N IN(1,2,3,4,5) GROUP BY erp_purchaseorderdetails.companySystemID,erp_purchaseorderdetails.serviceLineSystemID,erp_purchaseorderdetails.budgetYear');
                    if (!empty($poDetail)) {
                        foreach ($poDetail as $value) {
                            $budgetConsumeData[] = array(
                                "companySystemID" => $value->companySystemID,
                                "companyID" => $value->companyID,
                                "serviceLineSystemID" => $value->serviceLineSystemID,
                                "serviceLineCode" => $value->serviceLineCode,
                                "documentSystemID" => $poMaster["documentSystemID"],
                                "documentID" => $poMaster["documentID"],
                                "documentSystemCode" => $procumentOrder->purchaseOrderID,
                                "documentCode" => $poMaster["purchaseOrderCode"],
                                "chartOfAccountID" => 9,
                                "GLCode" => 10000,
                                "year" => $value->budgetYear,
                                "month" => $poMaster["month"],
                                "consumedLocalCurrencyID" => $value->localCurrencyID,
                                "consumedLocalAmount" => $value->GRVcostPerUnitLocalCur,
                                "consumedRptCurrencyID" => $value->companyReportingCurrencyID,
                                "consumedRptAmount" => $value->GRVcostPerUnitComRptCur,
                                "timestamp" => date('d/m/Y H:i:s A')
                            );
                        }
                        $budgetConsume = BudgetConsumedData::insert($budgetConsumeData);
                    }
                } else {
                    $poDetail = \DB::select('SELECT SUM(erp_purchaseorderdetails.GRVcostPerUnitLocalCur*erp_purchaseorderdetails.noQty) as GRVcostPerUnitLocalCur,SUM(erp_purchaseorderdetails.GRVcostPerUnitComRptCur*erp_purchaseorderdetails.noQty) as GRVcostPerUnitComRptCur,erp_purchaseorderdetails.companyReportingCurrencyID,erp_purchaseorderdetails.financeGLcodePLSystemID,erp_purchaseorderdetails.financeGLcodePL,erp_purchaseorderdetails.companyID,erp_purchaseorderdetails.companySystemID,erp_purchaseorderdetails.serviceLineSystemID,erp_purchaseorderdetails.serviceLineCode,erp_purchaseorderdetails.budgetYear,erp_purchaseorderdetails.localCurrencyID FROM erp_purchaseorderdetails INNER JOIN erp_purchaseordermaster ON erp_purchaseordermaster.purchaseOrderID = erp_purchaseorderdetails.purchaseOrderMasterID  WHERE erp_purchaseorderdetails.purchaseOrderMasterID = ' . $procumentOrder->purchaseOrderID . ' AND erp_purchaseordermaster.poType_N IN(1,2,3,4,5) GROUP BY erp_purchaseorderdetails.companySystemID,erp_purchaseorderdetails.serviceLineSystemID,erp_purchaseorderdetails.financeGLcodePLSystemID,erp_purchaseorderdetails.budgetYear');
                    if (!empty($poDetail)) {
                        foreach ($poDetail as $value) {
                            if ($value->financeGLcodePLSystemID != "") {
                                $budgetConsumeData[] = array(
                                    "companySystemID" => $value->companySystemID,
                                    "companyID" => $value->companyID,
                                    "serviceLineSystemID" => $value->serviceLineSystemID,
                                    "serviceLineCode" => $value->serviceLineCode,
                                    "documentSystemID" => $poMaster["documentSystemID"],
                                    "documentID" => $poMaster["documentID"],
                                    "documentSystemCode" => $procumentOrder->purchaseOrderID,
                                    "documentCode" => $poMaster["purchaseOrderCode"],
                                    "chartOfAccountID" => $value->financeGLcodePLSystemID,
                                    "GLCode" => $value->financeGLcodePL,
                                    "year" => $value->budgetYear,
                                    "month" => $poMaster["month"],
                                    "consumedLocalCurrencyID" => $value->localCurrencyID,
                                    "consumedLocalAmount" => $value->GRVcostPerUnitLocalCur,
                                    "consumedRptCurrencyID" => $value->companyReportingCurrencyID,
                                    "consumedRptAmount" => $value->GRVcostPerUnitComRptCur,
                                    "timestamp" => date('d/m/Y H:i:s A')
                                );
                            }
                        }
                        $budgetConsume = BudgetConsumedData::insert($budgetConsumeData);
                    }
                }
            }// closing budget consume if condition

        }// closing amend if condition


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
        $input = $this->convertArrayToSelectedValue($input, array('serviceLineSystemID', 'poCancelledYN', 'poConfirmedYN', 'approved', 'grvRecieved', 'month', 'year', 'invoicedBooked', 'supplierID', 'sentToSupplier', 'logisticsAvailable', 'financeCategory'));
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
            if ($input['serviceLineSystemID'] && !is_null($input['serviceLineSystemID'])) {
                $procumentOrders->where('serviceLineSystemID', $input['serviceLineSystemID']);
            }
        }

        if (array_key_exists('poCancelledYN', $input)) {
            if (($input['poCancelledYN'] == 0 || $input['poCancelledYN'] == -1) && !is_null($input['poCancelledYN'])) {
                $procumentOrders->where('poCancelledYN', $input['poCancelledYN']);
            }
        }

        if (array_key_exists('poConfirmedYN', $input)) {
            if (($input['poConfirmedYN'] == 0 || $input['poConfirmedYN'] == 1) && !is_null($input['poConfirmedYN'])) {
                $procumentOrders->where('poConfirmedYN', $input['poConfirmedYN']);
            }
        }

        if (array_key_exists('approved', $input)) {
            if (($input['approved'] == 0 || $input['approved'] == -1) && !is_null($input['approved'])) {
                $procumentOrders->where('approved', $input['approved']);
            }
        }

        if (array_key_exists('grvRecieved', $input)) {
            if (($input['grvRecieved'] == 0 || $input['grvRecieved'] == 1 || $input['grvRecieved'] == 2) && !is_null($input['grvRecieved'])) {
                $procumentOrders->where('grvRecieved', $input['grvRecieved']);
            }
        }

        if (array_key_exists('invoicedBooked', $input)) {
            if (($input['invoicedBooked'] == 0 || $input['invoicedBooked'] == 1 || $input['invoicedBooked'] == 2) && !is_null($input['invoicedBooked'])) {
                $procumentOrders->where('invoicedBooked', $input['invoicedBooked']);
            }
        }

        if (array_key_exists('month', $input)) {
            if ($input['month'] && !is_null($input['month'])) {
                $procumentOrders->whereMonth('createdDateTime', '=', $input['month']);
            }
        }

        if (array_key_exists('year', $input)) {
            if ($input['year'] && !is_null($input['year'])) {
                $procumentOrders->whereYear('createdDateTime', '=', $input['year']);
            }
        }

        if (array_key_exists('supplierID', $input)) {
            if ($input['supplierID'] && !is_null($input['supplierID'])) {
                $procumentOrders->where('supplierID', $input['supplierID']);
            }
        }

        if (array_key_exists('financeCategory', $input)) {
            if ($input['financeCategory'] && !is_null($input['financeCategory'])) {
                $procumentOrders->where('financeCategory', $input['financeCategory']);
            }
        }

        if (array_key_exists('sentToSupplier', $input)) {
            if (($input['sentToSupplier'] == 0 || $input['sentToSupplier'] == -1) && !is_null($input['sentToSupplier'])) {
                $procumentOrders->where('sentToSupplier', $input['sentToSupplier']);
            }
        }

        if (array_key_exists('logisticsAvailable', $input)) {
            if (($input['logisticsAvailable'] == 0 || $input['logisticsAvailable'] == -1) && !is_null($input['logisticsAvailable'])) {
                $procumentOrders->where('logisticsAvailable', $input['logisticsAvailable']);
            }
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
                'erp_purchaseordermaster.refferedBackYN',
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
                'erp_purchaseordermaster.sentToSupplier'
            ]);

        $search = $request->input('search.value');
        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $procumentOrders = $procumentOrders->where(function ($query) use ($search) {
                $query->where('purchaseOrderCode', 'LIKE', "%{$search}%")
                    ->orWhere('narration', 'LIKE', "%{$search}%")
                    ->orWhere('referenceNumber', 'LIKE', "%{$search}%")
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

        $PoPaymentTermTypes = DB::table("erp_popaymenttermstype")
            ->select('paymentTermsCategoryID', 'categoryDescription')
            ->get();

        if (!empty($purchaseOrderID)) {
            $checkDetailExist = PurchaseOrderDetails::where('purchaseOrderMasterID', $purchaseOrderID)
                ->where('companySystemID', $companyId)
                ->first();

            if (!empty($checkDetailExist)) {
                $detail = 1;
            }
        }

        $poAddonCategoryDrop = AddonCostCategories::all();

        $conditions = array('checkBudget' => 0, 'allowFinanceCategory' => 0, 'detailExist' => 0, 'pullPRPolicy' => 0);

        $grvRecieved = array(['id' => 0, 'value' => 'Not Received'], ['id' => 1, 'value' => 'Partial Received'], ['id' => 2, 'value' => 'Fully Received']);

        $invoiceBooked = array(['id' => 0, 'value' => 'Not Invoiced'], ['id' => 1, 'value' => 'Partial Invoiced'], ['id' => 2, 'value' => 'Fully Invoiced']);

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
            'poPaymentTermsDrop' => $PoPaymentTermTypes,
            'detailSum' => $detailSum,
            'grvRecieved' => $grvRecieved,
            'invoiceBooked' => $invoiceBooked,
            'poAddonCategoryDrop' => $poAddonCategoryDrop
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
            $query->where('rejectedYN', 0);
            $query->whereIN('documentSystemID', [2, 5, 52]);
        }, 'suppliercontact' => function ($query) {
            $query->where('isDefault', -1);
        }, 'paymentTerms_by' => function ($query) {
            $query->with('type');
        }, 'advance_detail' => function ($query) {
            $query->with(['category_by', 'grv_by', 'currency', 'supplier_by']);
        }, 'company', 'transactioncurrency', 'localcurrency', 'reportingcurrency', 'companydocumentattachment'])->first();

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
            'erp_purchaseordermaster.poType_N',
            'erp_purchaseordermaster.budgetYear',
            'erp_documentapproved.documentApprovedID',
            'erp_documentapproved.rollLevelOrder',
            'currencymaster.CurrencyCode',
            'approvalLevelID',
            'documentSystemCode',
            'employees.empName As created_user',
            'serviceline.ServiceLineDes as serviceLineDescription'
        )->join('employeesdepartments', function ($query) use ($companyID, $empID, $serviceLinePolicy) {
            $query->on('erp_documentapproved.approvalGroupID', '=', 'employeesdepartments.employeeGroupID')
                ->on('erp_documentapproved.documentSystemID', '=', 'employeesdepartments.documentSystemID')
                ->on('erp_documentapproved.departmentSystemID', '=', 'employeesdepartments.departmentSystemID')
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
                ->where('erp_purchaseordermaster.poCancelledYN', 0)
                ->where('erp_purchaseordermaster.poConfirmedYN', 1);
        })->where('erp_documentapproved.approvedYN', 0)
            ->join('currencymaster', 'supplierTransactionCurrencyID', '=', 'currencyID')
            ->join('employees', 'createdUserSystemID', 'employees.employeeSystemID')
            ->join('serviceline', 'erp_purchaseordermaster.serviceLineSystemID', 'serviceline.serviceLineSystemID')
            ->where('erp_documentapproved.rejectedYN', 0)
            ->whereIn('erp_documentapproved.documentSystemID', [2, 5, 52])
            ->where('erp_documentapproved.companySystemID', $companyID);

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
erp_grvdetails.itemDescription,warehousemaster.wareHouseDescription,erp_grvmaster.grvNarration,erp_grvmaster.supplierName,erp_grvdetails.poQty AS POQty,erp_grvdetails.noQty,erp_grvmaster.approved,erp_grvmaster.grvConfirmedYN,currencymaster.CurrencyCode,currencymaster.DecimalPlaces as transDeci,erp_grvdetails.GRVcostPerUnitSupTransCur,erp_grvdetails.unitCost,erp_grvdetails.GRVcostPerUnitSupTransCur*erp_grvdetails.noQty AS total,erp_grvdetails.GRVcostPerUnitSupTransCur*erp_grvdetails.noQty AS totalCost FROM erp_grvdetails INNER JOIN erp_grvmaster ON erp_grvdetails.grvAutoID = erp_grvmaster.grvAutoID INNER JOIN warehousemaster ON erp_grvmaster.grvLocation = warehousemaster.wareHouseSystemCode INNER JOIN currencymaster ON erp_grvdetails.supplierItemCurrencyID = currencymaster.currencyID WHERE purchaseOrderMastertID = ' . $purchaseOrderID . ' ');

        return $this->sendResponse($detail, 'Details retrieved successfully');

    }

    function getInvoiceDetailsForPO(Request $request)
    {
        $input = $request->all();

        $purchaseOrderID = $input['purchaseOrderID'];

        $detail = DB::select('SELECT erp_bookinvsuppmaster.bookingSuppMasInvAutoID,erp_bookinvsuppmaster.companyID,erp_bookinvsuppdet.purchaseOrderID,erp_bookinvsuppmaster.documentID,erp_grvmaster.grvPrimaryCode,erp_bookinvsuppmaster.bookingInvCode,erp_bookinvsuppmaster.bookingDate,erp_bookinvsuppmaster.comments,erp_bookinvsuppmaster.supplierInvoiceNo,erp_bookinvsuppmaster.confirmedYN,erp_bookinvsuppmaster.confirmedByName,erp_bookinvsuppmaster.approved,currencymaster.CurrencyCode,currencymaster.DecimalPlaces as transDeci,erp_bookinvsuppdet.totTransactionAmount,	erp_bookinvsuppdet.grvAutoID,erp_bookinvsuppmaster.bookingSuppMasInvAutoID FROM erp_bookinvsuppmaster INNER JOIN erp_bookinvsuppdet ON erp_bookinvsuppmaster.bookingSuppMasInvAutoID = erp_bookinvsuppdet.bookingSuppMasInvAutoID LEFT JOIN currencymaster ON erp_bookinvsuppmaster.supplierTransactionCurrencyID = currencymaster.currencyID LEFT JOIN erp_grvmaster ON erp_bookinvsuppdet.grvAutoID = erp_grvmaster.grvAutoID WHERE purchaseOrderID = ' . $purchaseOrderID . ' ');

        return $this->sendResponse($detail, 'Details retrieved successfully');
    }

    public function getProcumentOrderAllAmendments(Request $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input, array('serviceLineSystemID', 'poCancelledYN', 'poConfirmedYN', 'approved', 'grvRecieved', 'month', 'year', 'invoicedBooked', 'supplierID', 'sentToSupplier', 'logisticsAvailable', 'financeCategory'));
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
            if ($input['serviceLineSystemID'] && !is_null($input['serviceLineSystemID'])) {
                $procumentOrders->where('serviceLineSystemID', $input['serviceLineSystemID']);
            }
        }

        if (array_key_exists('poCancelledYN', $input)) {
            if (($input['poCancelledYN'] == 0 || $input['poCancelledYN'] == -1) && !is_null($input['poCancelledYN'])) {
                $procumentOrders->where('poCancelledYN', $input['poCancelledYN']);
            }
        }

        if (array_key_exists('poConfirmedYN', $input)) {
            if (($input['poConfirmedYN'] == 0 || $input['poConfirmedYN'] == 1) && !is_null($input['poConfirmedYN'])) {
                $procumentOrders->where('poConfirmedYN', $input['poConfirmedYN']);
            }
        }

        if (array_key_exists('approved', $input)) {
            if (($input['approved'] == 0 || $input['approved'] == -1) && !is_null($input['approved'])) {
                $procumentOrders->where('approved', $input['approved']);
            }
        }

        if (array_key_exists('grvRecieved', $input)) {
            if (($input['grvRecieved'] == 0 || $input['grvRecieved'] == 1 || $input['grvRecieved'] == 2) && !is_null($input['grvRecieved'])) {
                $procumentOrders->where('grvRecieved', $input['grvRecieved']);
            }
        }

        if (array_key_exists('invoicedBooked', $input)) {
            if (($input['invoicedBooked'] == 0 || $input['invoicedBooked'] == 1 || $input['invoicedBooked'] == 2) && !is_null($input['invoicedBooked'])) {
                $procumentOrders->where('invoicedBooked', $input['invoicedBooked']);
            }
        }

        if (array_key_exists('month', $input)) {
            if ($input['month'] && !is_null($input['month'])) {
                $procumentOrders->whereMonth('createdDateTime', '=', $input['month']);
            }
        }

        if (array_key_exists('year', $input)) {
            if ($input['year'] && !is_null($input['year'])) {
                $procumentOrders->whereYear('createdDateTime', '=', $input['year']);
            }
        }

        if (array_key_exists('supplierID', $input)) {
            if ($input['supplierID'] && !is_null($input['supplierID'])) {
                $procumentOrders->where('supplierID', $input['supplierID']);
            }
        }

        if (array_key_exists('financeCategory', $input)) {
            if ($input['financeCategory'] && !is_null($input['financeCategory'])) {
                $procumentOrders->where('financeCategory', $input['financeCategory']);
            }
        }


        if (array_key_exists('sentToSupplier', $input)) {
            if (($input['sentToSupplier'] == 0 || $input['sentToSupplier'] == -1) && !is_null($input['sentToSupplier'])) {
                $procumentOrders->where('sentToSupplier', $input['sentToSupplier']);
            }
        }

        if (array_key_exists('logisticsAvailable', $input)) {
            if (($input['logisticsAvailable'] == 0 || $input['logisticsAvailable'] == -1) && !is_null($input['logisticsAvailable'])) {
                $procumentOrders->where('logisticsAvailable', $input['logisticsAvailable']);
            }
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
                'erp_purchaseordermaster.refferedBackYN',
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
                'erp_purchaseordermaster.sentToSupplier',
                'erp_purchaseordermaster.poType_N'
            ]);

        $search = $request->input('search.value');
        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $procumentOrders = $procumentOrders->where(function ($query) use ($search) {
                $query->where('purchaseOrderCode', 'LIKE', "%{$search}%")
                    ->orWhere('referenceNumber', 'LIKE', "%{$search}%")
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
            return $this->sendError('Cannot ' . $comment . '. Advance payment is created for this PO');
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

        $idsDeleted = array();
        if ($purchaseOrder->approved == -1) {

            $budgetDetail = BudgetConsumedData::where('companySystemID', $purchaseOrder->companySystemID)
                ->where('documentSystemCode', $purchaseOrderID)
                ->where('documentSystemID', $purchaseOrder->documentSystemID)
                ->get();

            if (!empty($budgetDetail)) {
                foreach ($budgetDetail as $bd) {
                    array_push($idsDeleted, $bd->budgetConsumedDataAutoID);
                }
                BudgetConsumedData::destroy($idsDeleted);
            }

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
            ->where('companySystemID', $purchaseOrder->companySystemID)
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

        $idsDeleted = array();
        if ($purchaseOrder->approved == -1) {

            $budgetDetail = BudgetConsumedData::where('companySystemID', $purchaseOrder->companySystemID)
                ->where('documentSystemCode', $purchaseOrderID)
                ->where('documentSystemID', $purchaseOrder->documentSystemID)
                ->get();

            if (!empty($budgetDetail)) {
                foreach ($budgetDetail as $bd) {
                    array_push($idsDeleted, $bd->budgetConsumedDataAutoID);
                }
                BudgetConsumedData::destroy($idsDeleted);
            }
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
        $currencyField = "";
        $decimalField = "";
        IF ($input['documentId'] == 1) {
            if ($input['currency'] == 1) {
                $currencyField = 'GRVcostPerUnitLocalCur';
                $decimalField = 'localCurrencyDet.DecimalPlaces,';
            } else if ($input['currency'] == 2) {
                $currencyField = 'GRVcostPerUnitComRptCur';
                $decimalField = 'rptCurrencyDet.DecimalPlaces,';
            }
        } else if ($input['documentId'] == 2) {
            if ($input['currency'] == 1) {
                $currencyField = 'totLocalAmount';
                $decimalField = 'localCurrencyDet.DecimalPlaces,';
            } else if ($input['currency'] == 2) {
                $currencyField = 'totRptAmount';
                $decimalField = 'rptCurrencyDet.DecimalPlaces,';
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
            $doc1_query = 'SELECT
    ' . $decimalField . '
	GRVDet.*,
	erp_purchaseordermaster.purchaseOrderID,
	erp_purchaseordermaster.supplierPrimaryCode,
	erp_purchaseordermaster.supplierName,
	countrymaster.countryName
FROM
	erp_purchaseordermaster
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
	sum( GRVcostPerUnitLocalCur * noQty ) AS LinelocalTotal,
	sum( GRVcostPerUnitComRptCur * noQty ) AS LineRptTotal 
FROM
	erp_grvdetails
	INNER JOIN erp_grvmaster ON erp_grvmaster.grvAutoID = erp_grvdetails.grvAutoID 
WHERE
	erp_grvmaster.approved = - 1 
	AND erp_grvmaster.grvCancelledYN = 0 
	AND YEAR(erp_grvmaster.grvDate) IN (' . $commaSeperatedYears . ') 
	AND erp_grvmaster.companySystemID IN (' . $commaSeperatedCompany . ') 
GROUP BY
	erp_grvmaster.supplierID 
	) AS GRVDet ON GRVDet.supplierID = erp_purchaseordermaster.supplierID
	INNER JOIN suppliermaster ON suppliermaster.supplierCodeSystem = erp_purchaseordermaster.supplierID
    LEFT JOIN countrymaster ON suppliermaster.supplierCountryID = countrymaster.countryID 
    LEFT JOIN currencymaster as localCurrencyDet ON localCurrencyDet.currencyID=erp_purchaseordermaster.localCurrencyID
    LEFT JOIN currencymaster as rptCurrencyDet ON rptCurrencyDet.currencyID=erp_purchaseordermaster.companyReportingCurrencyID
WHERE
	erp_purchaseordermaster.approved = - 1 
	AND erp_purchaseordermaster.poCancelledYN = 0 
	AND erp_purchaseordermaster.companySystemID IN (' . $commaSeperatedCompany . ')
GROUP BY
	erp_purchaseordermaster.supplierID';
            /*echo $doc1_query;
            exit();*/
            $supplierReportGRVBase = DB::select($doc1_query);
        } else if ($input['documentId'] == 2) {
            $doc2_query = 'SELECT
' . $decimalField . '
         InvoiceDet.*,
	erp_purchaseordermaster.purchaseOrderID,
	erp_purchaseordermaster.supplierPrimaryCode,
	erp_purchaseordermaster.supplierName,
	countrymaster.countryName
FROM
	erp_purchaseordermaster
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
	AND erp_bookinvsuppmaster.companySystemID IN (' . $commaSeperatedCompany . ') 
	GROUP BY
		erp_bookinvsuppmaster.supplierID
) AS InvoiceDet ON InvoiceDet.supplierID = erp_purchaseordermaster.supplierID
INNER JOIN suppliermaster ON suppliermaster.supplierCodeSystem = erp_purchaseordermaster.supplierID
    LEFT JOIN countrymaster ON suppliermaster.supplierCountryID = countrymaster.countryID 
    LEFT JOIN currencymaster as localCurrencyDet ON localCurrencyDet.currencyID=erp_purchaseordermaster.localCurrencyID
    LEFT JOIN currencymaster as rptCurrencyDet ON rptCurrencyDet.currencyID=erp_purchaseordermaster.companyReportingCurrencyID
WHERE
	erp_purchaseordermaster.approved = - 1
AND erp_purchaseordermaster.poCancelledYN = 0
AND erp_purchaseordermaster.companySystemID IN (' . $commaSeperatedCompany . ') AND year(InvoiceDet.postedDate) IN (' . $commaSeperatedYears . ') GROUP BY erp_purchaseordermaster.supplierID';
            $supplierReportGRVBase = DB::select($doc2_query);
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

        $decimalPlace = collect($supplierReportGRVBase)->pluck('DecimalPlaces')->toArray();
        $decimalPlace = array_unique($decimalPlace);

        $dataRec = \DataTables::of($supplierReportGRVBase)
            ->addIndexColumn()
            ->with('totalAmount', $alltotal)
            ->with('pageTotal', $pageTotal)
            ->with('decimalPlace', $decimalPlace)
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
	erp_purchaseordermaster.purchaseOrderID,
	erp_purchaseordermaster.supplierPrimaryCode,
	erp_purchaseordermaster.supplierName,
	countrymaster.countryName
FROM
	erp_purchaseordermaster
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
	sum( GRVcostPerUnitLocalCur * noQty ) AS LinelocalTotal,
	sum( GRVcostPerUnitComRptCur * noQty ) AS LineRptTotal 
FROM
	erp_grvdetails
	INNER JOIN erp_grvmaster ON erp_grvmaster.grvAutoID = erp_grvdetails.grvAutoID 
WHERE
	erp_grvmaster.approved = - 1 
	AND erp_grvmaster.grvCancelledYN = 0 
	AND YEAR(erp_grvmaster.grvDate) IN (' . $commaSeperatedYears . ') 
	AND erp_grvmaster.companySystemID IN (' . $commaSeperatedCompany . ') 
GROUP BY
	erp_grvmaster.supplierID 
	) AS GRVDet ON GRVDet.supplierID = erp_purchaseordermaster.supplierID
	INNER JOIN suppliermaster ON suppliermaster.supplierCodeSystem = erp_purchaseordermaster.supplierID
    LEFT JOIN countrymaster ON suppliermaster.supplierCountryID = countrymaster.countryID 
WHERE
	erp_purchaseordermaster.approved = - 1 
	AND erp_purchaseordermaster.poCancelledYN = 0 
	AND erp_purchaseordermaster.companySystemID IN (' . $commaSeperatedCompany . ')
GROUP BY
	erp_purchaseordermaster.supplierID');
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
	InvoiceDet.LineRptTotal,
	countrymaster.countryName
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
	AND erp_bookinvsuppmaster.companySystemID IN (' . $commaSeperatedCompany . ')
	GROUP BY
		erp_bookinvsuppmaster.supplierID
) AS InvoiceDet ON InvoiceDet.supplierID = erp_purchaseordermaster.supplierID
INNER JOIN suppliermaster ON suppliermaster.supplierCodeSystem = erp_purchaseordermaster.supplierID
    LEFT JOIN countrymaster ON suppliermaster.supplierCountryID = countrymaster.countryID
    LEFT JOIN currencymaster as localCurrencyDet ON localCurrencyDet.currencyID=erp_purchaseordermaster.localCurrencyID
    LEFT JOIN currencymaster as rptCurrencyDet ON rptCurrencyDet.currencyID=erp_purchaseordermaster.companyReportingCurrencyID
WHERE
	erp_purchaseordermaster.approved = - 1
AND erp_purchaseordermaster.poCancelledYN = 0
AND erp_purchaseordermaster.companySystemID IN (' . $commaSeperatedCompany . ') AND year(InvoiceDet.postedDate) IN (' . $commaSeperatedYears . ') GROUP BY erp_purchaseordermaster.supplierID');
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
            $test = array('CompanyID' => $val->companyID, 'Supplier Code' => $val->supplierPrimaryCode, 'Supplier Name' => $val->supplierName, 'Supplier Country' => $val->countryName);

            if (!empty($months)) {
                foreach ($months as $key => $row) {
                    if (!empty($val->$key)) {
                        $test[$row] = $val->$key;
                    } else {
                        $test[$row] = '0';
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
        $typeID = $request->get('typeID');

        $procumentOrder = $this->procumentOrderRepository->findWithoutFail($id);

        if (empty($procumentOrder)) {
            return $this->sendError('Procurement Order not found');
        }

        $outputRecord = ProcumentOrder::where('purchaseOrderID', $procumentOrder->purchaseOrderID)->with(['detail' => function ($query) {
            $query->with('unit');
        }, 'approved_by' => function ($query) {
            $query->with('employee');
            $query->where('rejectedYN', 0);
            $query->whereIN('documentSystemID', [2, 5, 52]);
        }, 'suppliercontact' => function ($query) {
            $query->where('isDefault', -1);
        }, 'company', 'transactioncurrency', 'companydocumentattachment', 'paymentTerms_by'])->get();

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

        $poPaymentTerms = PoPaymentTerms::where('poID', $procumentOrder->purchaseOrderID)
            ->get();

        $paymentTermsView = '';

        if ($poPaymentTerms) {
            foreach ($poPaymentTerms as $val) {
                $paymentTermsView .= $val['paymentTemDes'] . ', ';
            }
        }

        $orderAddons = PoAddons::where('poId', $procumentOrder->purchaseOrderID)
            ->with(['category'])
            ->orderBy('idpoAddons', 'DESC')
            ->get();

        $order = array(
            'podata' => $outputRecord[0],
            'docRef' => $refernaceDoc,
            'numberFormatting' => $decimal,
            'title' => $documentTitle,
            'termsCond' => $typeID,
            'paymentTermsView' => $paymentTermsView,
            'addons' => $orderAddons

        );

        $html = view('print.purchase_order_print_pdf', $order);

        // echo $html;
        //exit();

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
            'erp_purchaseordermaster.budgetYear',
            'erp_documentapproved.documentApprovedID',
            'erp_documentapproved.rollLevelOrder',
            'currencymaster.CurrencyCode',
            'approvalLevelID',
            'documentSystemCode',
            'employees.empName As created_user',
            'serviceline.ServiceLineDes as serviceLineDescription'
        )->join('erp_purchaseordermaster', function ($query) use ($companyID, $empID) {
            $query->on('erp_documentapproved.documentSystemCode', '=', 'purchaseOrderID')
                ->where('erp_purchaseordermaster.companySystemID', $companyID)
                ->where('erp_purchaseordermaster.approved', -1)
                ->where('erp_purchaseordermaster.poConfirmedYN', 1);
        })->where('erp_documentapproved.approvedYN', -1)
            ->join('currencymaster', 'supplierTransactionCurrencyID', '=', 'currencyID')
            ->join('employees', 'createdUserSystemID', 'employees.employeeSystemID')
            ->join('serviceline', 'erp_purchaseordermaster.serviceLineSystemID', 'serviceline.serviceLineSystemID')
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
	erp_purchaseordermaster.purchaseOrderCode,
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
	erp_purchaseordermaster.purchaseOrderCode,
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
                $testArray['PO Number'] = $order->purchaseOrderCode;
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

        $detailExistGRV = GRVDetails::where('purchaseOrderMastertID', $input['purchaseOrderID'])
            ->first();

        $detailExistAPD = AdvancePaymentDetails::where('purchaseOrderID', $input['purchaseOrderID'])
            ->first();

        if (empty($procurementOrder)) {
            return $this->sendError('Procurement Order not found');
        }

        if ($procurementOrder->poConfirmedYN != 1) {
            return $this->sendError('You cannot amend this order, this is not confirm', 500);
        }

        if ($detailExistGRV) {
            return $this->sendError('You cannot amend, GRV is created for this PO');
        }

        if ($detailExistAPD) {
            return $this->sendError('You cannot amend advance payment is created for this PO');
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

        $supplierCurrencyDecimalPlace = \Helper::getCurrencyDecimalPlace($purchaseOrder->supplierTransactionCurrencyID);

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
        }

        $currencyConversionDefaultMaster = \Helper::currencyConversion($input['companySystemID'], $input['supplierTransactionCurrencyID'], $supplierCurrency->currencyID, 0);

        if ($currencyConversionDefaultMaster) {
            $purchaseOrder->supplierDefaultER = $currencyConversionDefaultMaster['transToDocER'];
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
            $purchaseOrder->VATAmountLocal = \Helper::roundValue($currencyConversionVatAmount['localAmount']);
            $purchaseOrder->VATAmountRpt = \Helper::roundValue($currencyConversionVatAmount['reportingAmount']);
        } else {
            $purchaseOrder->VATAmount = 0;
            $purchaseOrder->VATAmountLocal = 0;
            $purchaseOrder->VATAmountRpt = 0;
        }

        //getting total sum of PO detail Amount
        $poMasterSum = PurchaseOrderDetails::select(DB::raw('COALESCE(SUM(netAmount),0) as masterTotalSum'))
            ->where('purchaseOrderMasterID', $purchaseOrder->purchaseOrderID)
            ->first();

        //getting addon Total for PO
        $poAddonMasterSum = PoAddons::select(DB::raw('COALESCE(SUM(amount),0) as addonTotalSum'))
            ->where('poId', $purchaseOrder->purchaseOrderID)
            ->first();

        $poMasterSumRounded = round($poMasterSum['masterTotalSum'], $supplierCurrencyDecimalPlace);
        $poAddonMasterSumRounded = round($poAddonMasterSum['addonTotalSum'], $supplierCurrencyDecimalPlace);

        $newlyUpdatedPoTotalAmount = $poMasterSumRounded + $poAddonMasterSumRounded;

        $poMasterSumDeducted = ($newlyUpdatedPoTotalAmount - $purchaseOrder->poDiscountAmount) + $purchaseOrder->VATAmount;

        $currencyConversionMaster = \Helper::currencyConversion($input["companySystemID"], $purchaseOrder->supplierTransactionCurrencyID, $purchaseOrder->supplierTransactionCurrencyID, $newlyUpdatedPoTotalAmount);

        // calculating total Supplier Default currency total

        $currencyConversionMasterDefault = \Helper::currencyConversion($input["companySystemID"], $supplierCurrency->currencyID, $input['supplierTransactionCurrencyID'], $poMasterSumDeducted);

        $purchaseOrder->poTotalComRptCurrency = \Helper::roundValue($currencyConversionMaster['reportingAmount']);
        $purchaseOrder->poTotalLocalCurrency = \Helper::roundValue($currencyConversionMaster['localAmount']);
        $purchaseOrder->poTotalSupplierDefaultCurrency = \Helper::roundValue($currencyConversionMasterDefault['documentAmount']);
        $purchaseOrder->poTotalSupplierTransactionCurrency = $poMasterSumDeducted;
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


            if ($purchaseOrder->supplierVATEligible == 1 && $purchaseOrder->vatRegisteredYN == 0) {
                if ($purchaseOrder->poDiscountAmount > 0) {
                    $calculateItemDiscount = (($purchaseOrderDetail->netAmount - (($purchaseOrder->poDiscountAmount / $poMasterSumRounded) * $purchaseOrderDetail->netAmount)) / $purchaseOrderDetail->noQty);
                } else {
                    $calculateItemDiscount = $purchaseOrderDetail->unitCost - $purchaseOrderDetail->discountAmount;
                }
                $calculateItemTax = (($purchaseOrder->VATPercentage / 100) * $calculateItemDiscount) + $calculateItemDiscount;

                $currencyConversion = \Helper::currencyConversion($purchaseOrderDetail->companySystemID, $purchaseOrder->supplierTransactionCurrencyID, $purchaseOrder->supplierTransactionCurrencyID, $calculateItemTax);

                $vatLineAmount = ($calculateItemTax - $calculateItemDiscount);

                $currencyConversionDefaultW = \Helper::currencyConversion($purchaseOrderDetail->companySystemID, $purchaseOrder->supplierTransactionCurrencyID, $purchaseOrder->supplierDefaultCurrencyID, $calculateItemTax);

                $purchaseOrderDetail->GRVcostPerUnitSupTransCur = \Helper::roundValue($calculateItemTax);
                $purchaseOrderDetail->GRVcostPerUnitComRptCur = \Helper::roundValue($currencyConversion['reportingAmount']);
                $purchaseOrderDetail->purchaseRetcostPerUnitLocalCur = \Helper::roundValue($currencyConversion['localAmount']);
                $purchaseOrderDetail->purchaseRetcostPerUnitTranCur = \Helper::roundValue($calculateItemTax);
                $purchaseOrderDetail->purchaseRetcostPerUnitRptCur = \Helper::roundValue($currencyConversion['reportingAmount']);

                $purchaseOrderDetail->GRVcostPerUnitSupDefaultCur = \Helper::roundValue($currencyConversionDefaultW['documentAmount']);
                $purchaseOrderDetail->purchaseRetcostPerUniSupDefaultCur = \Helper::roundValue($currencyConversionDefaultW['documentAmount']);
            } else {
                if ($purchaseOrder->poDiscountAmount > 0) {
                    $calculateItemDiscount = (($purchaseOrderDetail->netAmount - (($purchaseOrder->poDiscountAmount / $poMasterSumRounded) * $purchaseOrderDetail->netAmount)) / $purchaseOrderDetail->noQty);
                } else {
                    $calculateItemDiscount = $purchaseOrderDetail->unitCost - $purchaseOrderDetail->discountAmount;
                }

                $currencyConversion = \Helper::currencyConversion($purchaseOrderDetail->companySystemID, $purchaseOrder->supplierTransactionCurrencyID, $purchaseOrder->supplierTransactionCurrencyID, $calculateItemDiscount);

                $currencyConversionLineDefault = \Helper::currencyConversion($purchaseOrderDetail->companySystemID, $purchaseOrder->supplierTransactionCurrencyID, $purchaseOrder->supplierDefaultCurrencyID, $calculateItemDiscount);


                $purchaseOrderDetail->GRVcostPerUnitLocalCur = \Helper::roundValue($currencyConversion['localAmount']);
                $purchaseOrderDetail->GRVcostPerUnitSupTransCur = \Helper::roundValue($calculateItemDiscount);
                $purchaseOrderDetail->GRVcostPerUnitComRptCur = \Helper::roundValue($currencyConversion['reportingAmount']);
                $purchaseOrderDetail->purchaseRetcostPerUnitLocalCur = \Helper::roundValue($currencyConversion['localAmount']);
                $purchaseOrderDetail->purchaseRetcostPerUnitTranCur = \Helper::roundValue($calculateItemDiscount);
                $purchaseOrderDetail->purchaseRetcostPerUnitRptCur = \Helper::roundValue($currencyConversion['reportingAmount']);

                $purchaseOrderDetail->GRVcostPerUnitSupDefaultCur = \Helper::roundValue($currencyConversionLineDefault['documentAmount']);
                $purchaseOrderDetail->purchaseRetcostPerUniSupDefaultCur = \Helper::roundValue($currencyConversionLineDefault['documentAmount']);
            }


            if ($purchaseOrder->supplierVATEligible == 1) {

                if ($purchaseOrder->poDiscountAmount > 0) {
                    $calculateItemDiscount = (($purchaseOrderDetail->netAmount - (($purchaseOrder->poDiscountAmount / $purchaseOrder->poTotalSupplierTransactionCurrency) * $purchaseOrderDetail->netAmount)) / $purchaseOrderDetail->noQty);
                } else {
                    $calculateItemDiscount = $purchaseOrderDetail->unitCost - $purchaseOrderDetail->discountAmount;
                }
                $calculateItemTax = (($purchaseOrder->VATPercentage / 100) * $calculateItemDiscount) + $calculateItemDiscount;

                $vatLineAmount = ($calculateItemTax - $calculateItemDiscount);

                $currencyConversionForLineAmount = \Helper::currencyConversion($purchaseOrderDetail->companySystemID, $purchaseOrder->supplierTransactionCurrencyID, $purchaseOrder->supplierTransactionCurrencyID, $vatLineAmount);

                $purchaseOrderDetail->VATPercentage = round($purchaseOrder->VATPercentage, 2);
                $purchaseOrderDetail->VATAmount = \Helper::roundValue($vatLineAmount);
                $purchaseOrderDetail->VATAmountLocal = \Helper::roundValue($currencyConversionForLineAmount['localAmount']);
                $purchaseOrderDetail->VATAmountRpt = \Helper::roundValue($currencyConversionForLineAmount['reportingAmount']);
            } else {
                $purchaseOrderDetail->VATPercentage = 0;
                $purchaseOrderDetail->VATAmount = 0;
                $purchaseOrderDetail->VATAmountLocal = 0;
                $purchaseOrderDetail->VATAmountRpt = 0;
            }

            // adding supplier Default CurrencyID base currency conversion
            if ($purchaseOrderDetail->unitCost > 0) {
                $currencyConversionDefault = \Helper::currencyConversion($input['companySystemID'], $purchaseOrder->supplierTransactionCurrencyID, $purchaseOrder->supplierDefaultCurrencyID, $purchaseOrderDetail->unitCost);
                //$purchaseOrderDetail->GRVcostPerUnitSupDefaultCur = $currencyConversionDefault['documentAmount'];
                //$purchaseOrderDetail->purchaseRetcostPerUniSupDefaultCur = $currencyConversionDefault['documentAmount'];
            }

            $purchaseOrderDetail->save();
        }

        //updating addons detail for line item
        $getPoDetailForAddon = PurchaseOrderDetails::where('purchaseOrderMasterID', $purchaseOrder->purchaseOrderID)
            ->get();

        $getPoAllAddons = PoAddons::where('poId', $purchaseOrder->purchaseOrderID)
            ->get();

        if (!empty($getPoAllAddons)) {

            if (!empty($getPoDetailForAddon)) {
                foreach ($getPoDetailForAddon as $AddonDeta) {

                    $calculateAddonLineAmount = (($poAddonMasterSumRounded / $poMasterSumRounded) * $AddonDeta['netAmount']) / $AddonDeta['noQty'];

                    $currencyConversionForLineAmountAddon = \Helper::currencyConversion($input['companySystemID'], $purchaseOrder->supplierTransactionCurrencyID, $purchaseOrder->supplierTransactionCurrencyID, $calculateAddonLineAmount);

                    $currencyConversionLineAmountAddonDefault = \Helper::currencyConversion($input['companySystemID'], $purchaseOrder->supplierTransactionCurrencyID, $purchaseOrder->supplierDefaultCurrencyID, $calculateAddonLineAmount);

                    $updatePoDetailAddonDetail = PurchaseOrderDetails::find($AddonDeta['purchaseOrderDetailsID']);

                    $GRVcostPerUnitLocalCurAddon = ($AddonDeta['GRVcostPerUnitLocalCur'] + $currencyConversionForLineAmountAddon['localAmount']);
                    $updatePoDetailAddonDetail->GRVcostPerUnitLocalCur = \Helper::roundValue($GRVcostPerUnitLocalCurAddon);

                    $GRVcostPerUnitSupDefaultCurAddon = ($AddonDeta['GRVcostPerUnitSupDefaultCur'] + $currencyConversionLineAmountAddonDefault['documentAmount']);
                    $updatePoDetailAddonDetail->GRVcostPerUnitSupDefaultCur = \Helper::roundValue($GRVcostPerUnitSupDefaultCurAddon);

                    $GRVcostPerUnitSupTransCurAddon = ($AddonDeta['GRVcostPerUnitSupTransCur'] + $calculateAddonLineAmount);
                    $updatePoDetailAddonDetail->GRVcostPerUnitSupTransCur = \Helper::roundValue($GRVcostPerUnitSupTransCurAddon);

                    $GRVcostPerUnitComRptCurAddon = ($AddonDeta['GRVcostPerUnitComRptCur'] + $currencyConversionForLineAmountAddon['reportingAmount']);
                    $updatePoDetailAddonDetail->GRVcostPerUnitComRptCur = \Helper::roundValue($GRVcostPerUnitComRptCurAddon);

                    $purchaseRetcostPerUniSupDefaultCurAddon = ($AddonDeta['purchaseRetcostPerUniSupDefaultCur'] + $currencyConversionLineAmountAddonDefault['documentAmount']);
                    $updatePoDetailAddonDetail->purchaseRetcostPerUniSupDefaultCur = \Helper::roundValue($purchaseRetcostPerUniSupDefaultCurAddon);

                    $purchaseRetcostPerUnitLocalCurAddon = ($AddonDeta['purchaseRetcostPerUnitLocalCur'] + $currencyConversionForLineAmountAddon['localAmount']);
                    $updatePoDetailAddonDetail->purchaseRetcostPerUnitLocalCur = \Helper::roundValue($purchaseRetcostPerUnitLocalCurAddon);

                    $purchaseRetcostPerUnitTranCurAddon = ($AddonDeta['purchaseRetcostPerUnitTranCur'] + $calculateAddonLineAmount);
                    $updatePoDetailAddonDetail->purchaseRetcostPerUnitTranCur = \Helper::roundValue($purchaseRetcostPerUnitTranCurAddon);

                    $purchaseRetcostPerUnitRptCur = ($AddonDeta['purchaseRetcostPerUnitRptCur'] + $currencyConversionForLineAmountAddon['reportingAmount']);
                    $updatePoDetailAddonDetail->purchaseRetcostPerUnitRptCur = \Helper::roundValue($purchaseRetcostPerUnitRptCur);

                    $updatePoDetailAddonDetail->addonDistCost = \Helper::roundValue($calculateAddonLineAmount);
                    $updatePoDetailAddonDetail->addonPurchaseReturnCost = \Helper::roundValue($calculateAddonLineAmount);
                    $updatePoDetailAddonDetail->save();

                }

            }

        }
        //calculate tax amount according to the percantage for tax update

        //getting total sum of PO detail Amount
        $poMasterSum = PurchaseOrderDetails::select(DB::raw('COALESCE(SUM(netAmount),0) as masterTotalSum'))
            ->where('purchaseOrderMasterID', $purchaseOrder->purchaseOrderID)
            ->first();

        //if($purchaseOrder->VATPercentage > 0 && $purchaseOrder->supplierVATEligible == 1 && $purchaseOrder->vatRegisteredYN == 0){
        if ($purchaseOrder->VATPercentage > 0 && $purchaseOrder->supplierVATEligible == 1) {
            $calculatVatAmount = ($poMasterSum['masterTotalSum'] - $purchaseOrder->poDiscountAmount) * ($purchaseOrder->VATPercentage / 100);

            $currencyConversionVatAmount = \Helper::currencyConversion($purchaseOrder->companySystemID, $purchaseOrder->supplierTransactionCurrencyID, $purchaseOrder->supplierTransactionCurrencyID, $calculatVatAmount);

            $updatePOMaster = ProcumentOrder::find($purchaseOrder->purchaseOrderID)
                ->update([
                    'VATAmount' => round($calculatVatAmount, $supplierCurrencyDecimalPlace),
                    'VATAmountLocal' => \Helper::roundValue($currencyConversionVatAmount['localAmount']),
                    'VATAmountRpt' => \Helper::roundValue($currencyConversionVatAmount['reportingAmount'])
                ]);
        }

        //Update request payment
        $PoAdvancePaymentFetch = PoAdvancePayment::where('poID', $purchaseOrder->purchaseOrderID)
            ->get();

        if (!empty($PoAdvancePaymentFetch)) {
            foreach ($PoAdvancePaymentFetch as $advance) {
                $advancePaymentTermUpdate = PoAdvancePayment::find($advance->poAdvPaymentID);

                $advancePaymentTermUpdate->supplierID = $purchaseOrder->supplierID;
                $advancePaymentTermUpdate->SupplierPrimaryCode = $purchaseOrder->supplierPrimaryCode;
                $advancePaymentTermUpdate->currencyID = $purchaseOrder->supplierTransactionCurrencyID;

                $companyCurrencyConversionAD = \Helper::currencyConversion($purchaseOrder->companySystemID, $purchaseOrder->supplierTransactionCurrencyID, $purchaseOrder->supplierTransactionCurrencyID, $advance['reqAmount']);

                $advancePaymentTermUpdate->reqAmountInPOLocalCur = $companyCurrencyConversionAD['localAmount'];
                $advancePaymentTermUpdate->reqAmountInPORptCur = $companyCurrencyConversionAD['reportingAmount'];

                $advancePaymentTermUpdate->save();
            }
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
	erp_grvmaster.grvConfirmedYN = 0
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

    public function purchaseOrderForGRV(Request $request)
    {
        $input = $request->all();
        $companyID = $input['companyId'];
        $grvAutoID = $input['grvAutoID'];

        $grvMaster = GRVMaster::where('grvAutoID', $grvAutoID)
            ->first();

        if (empty($grvMaster)) {
            return $this->sendError('Good Receipt Voucher not found');
        }

        //checking segment is active
        $segments = SegmentMaster::where("serviceLineSystemID", $grvMaster->serviceLineSystemID)
            ->where('companySystemID', $companyID)
            ->where('isActive', 1)
            ->first();

        if (empty($segments)) {
            return $this->sendError('Selected segment is not active. Please select an active segment');
        }

        $ProcumentOrder = ProcumentOrder::where('companySystemID', $companyID)
            ->where('serviceLineSystemID', $grvMaster->serviceLineSystemID)
            ->where('supplierID', $grvMaster->supplierID)
            ->where('supplierTransactionCurrencyID', $grvMaster->supplierTransactionCurrencyID)
            ->where('approved', -1)
            ->where('poConfirmedYN', 1)
            ->where('poCancelledYN', 0)
            ->where('poClosedYN', 0)
            ->where('grvRecieved', '<>', 2)
            ->where('WO_confirmedYN', 1)
            ->where('manuallyClosed', 0)
            ->where('poType_N', '<>', 5)
            ->orderBy('purchaseOrderID', 'DESC')
            ->get();

        return $this->sendResponse($ProcumentOrder->toArray(), 'Purchase Order Details retrieved successfully');
    }

    public function getPurchasePaymentStatusHistory(Request $request)
    {
        $input = $request->all();

        $companySystemID = $input['companySystemID'];
        $purchaseOrderID = $input['purchaseOrderID'];

        $detail = DB::select('SELECT
	*
FROM
	(
		SELECT
			erp_paysupplierinvoicedetail.PayMasterAutoId AS PayMasterAutoId,
			erp_paysupplierinvoicemaster.documentID,
			erp_paysupplierinvoicedetail.companyID,
			"Invoice Payment" AS paymentType,
			erp_paysupplierinvoicemaster.BPVcode,
			erp_paysupplierinvoicemaster.BPVdate,
			erp_paysupplierinvoicedetail.supplierInvoiceNo,
			erp_paysupplierinvoicedetail.supplierInvoiceDate,
			erp_bookinvsuppdet.purchaseOrderID,
			erp_paysupplierinvoicedetail.supplierPaymentAmount AS TransAmount,
			erp_paysupplierinvoicedetail.paymentLocalAmount AS LocalAmount,
			erp_paysupplierinvoicedetail.paymentComRptAmount AS RptAmount,
			erp_paysupplierinvoicemaster.trsClearedDate,
			erp_paysupplierinvoicemaster.bankClearedDate,
			erp_paysupplierinvoicemaster.approvedDate,
			erp_paysupplierinvoicemaster.invoiceType,
			erp_paysupplierinvoicemaster.confirmedYN,
			erp_paysupplierinvoicemaster.approved,
			cm1.CurrencyCode AS transactionCurrency,
			cm2.CurrencyCode AS localCurrency,
			cm3.CurrencyCode AS reportingCurrency,
			cm1.DecimalPlaces AS transactionDeci,
			cm2.DecimalPlaces AS localDeci,
			cm3.DecimalPlaces AS reportingDec
		FROM
			erp_paysupplierinvoicedetail
		INNER JOIN erp_bookinvsuppdet ON erp_paysupplierinvoicedetail.bookingInvSystemCode = erp_bookinvsuppdet.bookingSuppMasInvAutoID
		INNER JOIN erp_paysupplierinvoicemaster ON erp_paysupplierinvoicedetail.PayMasterAutoId = erp_paysupplierinvoicemaster.PayMasterAutoId
		INNER JOIN currencymaster cm1 ON cm1.currencyID = erp_bookinvsuppdet.supplierTransactionCurrencyID
		INNER JOIN currencymaster cm2 ON cm2.currencyID = erp_bookinvsuppdet.localCurrencyID
		INNER JOIN currencymaster cm3 ON cm3.currencyID = erp_bookinvsuppdet.companyReportingCurrencyID
		WHERE
			erp_paysupplierinvoicemaster.companySystemID = ' . $companySystemID . '
		AND erp_bookinvsuppdet.purchaseOrderID = ' . $purchaseOrderID . '
		AND erp_paysupplierinvoicemaster.invoiceType = 2
		UNION ALL
			SELECT
				erp_paysupplierinvoicemaster.PayMasterAutoId AS PayMasterAutoId,
				erp_paysupplierinvoicemaster.documentID,
				erp_paysupplierinvoicemaster.companyID,
				"Advance Payment" AS paymentType,
				erp_paysupplierinvoicemaster.BPVcode,
				erp_paysupplierinvoicemaster.BPVdate,
				"-" AS supplierInvoiceNo,
				"-" AS supplierInvoiceDate,
				erp_advancepaymentdetails.purchaseOrderID,
				erp_advancepaymentdetails.supplierTransAmount AS TransAmount,
				erp_advancepaymentdetails.localAmount AS LocalAmount,
				erp_advancepaymentdetails.comRptAmount AS RptAmount,
				erp_paysupplierinvoicemaster.trsClearedDate,
				erp_paysupplierinvoicemaster.bankClearedDate,
				erp_paysupplierinvoicemaster.approvedDate,
				erp_paysupplierinvoicemaster.invoiceType,
				erp_paysupplierinvoicemaster.confirmedYN,
				erp_paysupplierinvoicemaster.approved,
				cm1.CurrencyCode AS transactionCurrency,
			    cm2.CurrencyCode AS localCurrency,
			    cm3.CurrencyCode AS reportingCurrency,
			    cm1.DecimalPlaces AS transactionDeci,
				cm2.DecimalPlaces AS localDeci,
				cm3.DecimalPlaces AS reportingDec
			FROM
				erp_paysupplierinvoicemaster
			INNER JOIN erp_advancepaymentdetails ON erp_paysupplierinvoicemaster.PayMasterAutoId = erp_advancepaymentdetails.PayMasterAutoId
			INNER JOIN currencymaster cm1 ON cm1.currencyID = erp_advancepaymentdetails.supplierTransCurrencyID
			INNER JOIN currencymaster cm2 ON cm2.currencyID = erp_advancepaymentdetails.localCurrencyID
			INNER JOIN currencymaster cm3 ON cm3.currencyID = erp_advancepaymentdetails.comRptCurrencyID
			WHERE
				erp_paysupplierinvoicemaster.companySystemID = ' . $companySystemID . '
			AND erp_advancepaymentdetails.purchaseOrderID = ' . $purchaseOrderID . '
	) AS POPaymentDetails');

        return $this->sendResponse($detail, 'payment status retrieved successfully');
    }

    public function getProcurementOrderReopen(Request $request)
    {
        $input = $request->all();

        $purchaseOrderID = $input['purchaseOrderID'];

        $purchaseOrder = ProcumentOrder::find($purchaseOrderID);
        $emails = array();
        if (empty($purchaseOrder)) {
            return $this->sendError('Purchase Order not found');
        }

        if ($purchaseOrder->RollLevForApp_curr > 1) {
            return $this->sendError('You cannot reopen this PO it is already partially approved');
        }

        if ($purchaseOrder->approved == -1) {
            return $this->sendError('You cannot reopen this PO it is already fully approved');
        }

        if ($purchaseOrder->poConfirmedYN == 0) {
            return $this->sendError('You cannot reopen this PO, it is not confirmed');
        }

        // updating fields

        $purchaseOrder->poConfirmedYN = 0;
        $purchaseOrder->poConfirmedByEmpSystemID = null;
        $purchaseOrder->poConfirmedByEmpID = null;
        $purchaseOrder->poConfirmedByName = null;
        $purchaseOrder->poConfirmedDate = null;
        $purchaseOrder->RollLevForApp_curr = 1;
        $purchaseOrder->save();

        $employee = \Helper::getEmployeeInfo();

        $document = DocumentMaster::where('documentSystemID', $purchaseOrder->documentSystemID)->first();

        $cancelDocNameBody = $document->documentDescription . ' <b>' . $purchaseOrder->purchaseOrderCode . '</b>';
        $cancelDocNameSubject = $document->documentDescription . ' ' . $purchaseOrder->purchaseOrderCode;

        $subject = $cancelDocNameSubject . ' is reopened';

        $body = '<p>' . $cancelDocNameBody . ' is reopened by ' . $employee->empID . ' - ' . $employee->empFullName . '</p><p>Comment : ' . $input['reopenComments'] . '</p>';

        $documentApproval = DocumentApproved::where('companySystemID', $purchaseOrder->companySystemID)
            ->where('documentSystemCode', $purchaseOrder->purchaseOrderID)
            ->where('documentSystemID', $purchaseOrder->documentSystemID)
            ->where('rollLevelOrder', 1)
            ->first();

        if ($documentApproval) {
            if ($documentApproval->approvedYN == 0) {
                $companyDocument = CompanyDocumentAttachment::where('companySystemID', $purchaseOrder->companySystemID)
                    ->where('documentSystemID', $purchaseOrder->documentSystemID)
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

        $deleteApproval = DocumentApproved::where('documentSystemCode', $purchaseOrderID)
            ->where('companySystemID', $purchaseOrder->companySystemID)
            ->where('documentSystemID', $purchaseOrder->documentSystemID)
            ->delete();

        return $this->sendResponse($purchaseOrder->toArray(), 'Purchase Order reopened successfully');
    }

    public function procumentOrderPRAttachment(Request $request)
    {
        $input = $request->all();

        $attachmentFound = 0;

        $purchaseOrderID = $input['purchaseOrderID'];
        $companySystemID = $input['companySystemID'];
        $documentSystemID = $input['documentSystemID'];

        $prIDS = PurchaseOrderDetails::where('purchaseOrderMasterID', $purchaseOrderID)
            ->where('companySystemID', $companySystemID)
            ->groupBy('purchaseRequestID')
            ->get(['purchaseRequestID']);

        $company = Company::where('companySystemID', $companySystemID)->first();
        if ($company) {
            $companyName = $company->CompanyID;
        }

        $document = DocumentMaster::where('documentSystemID', $documentSystemID)->first();
        if ($document) {
            $documentID = $document->documentID;
        }

        if (!empty($prIDS)) {
            foreach ($prIDS as $poDetail) {

                $purchaseRequest = PurchaseRequest::find($poDetail['purchaseRequestID']);

                $docAttachement = DocumentAttachments::where('documentSystemCode', $poDetail['purchaseRequestID'])
                    ->where('companySystemID', $companySystemID)
                    ->where('documentSystemID', $purchaseRequest['documentSystemID'])
                    ->get();

                if (!empty($docAttachement->toArray())) {
                    $attachmentFound = 1;
                    foreach ($docAttachement as $doc) {

                        $documentAttachments = new DocumentAttachments;
                        $documentAttachments->companySystemID = $companySystemID;
                        $documentAttachments->companyID = $companyName;
                        $documentAttachments->documentID = $documentID;
                        $documentAttachments->documentSystemID = $documentSystemID;
                        $documentAttachments->documentSystemCode = $purchaseOrderID;
                        $documentAttachments->attachmentDescription = $doc['attachmentDescription'];
                        $documentAttachments->path = $doc['path'];
                        $documentAttachments->originalFileName = $doc['originalFileName'];
                        $documentAttachments->myFileName = $doc['myFileName'];
                        $documentAttachments->docExpirtyDate = $doc['docExpirtyDate'];
                        $documentAttachments->attachmentType = $doc['attachmentType'];
                        $documentAttachments->sizeInKbs = $doc['sizeInKbs'];
                        $documentAttachments->isUploaded = $doc['isUploaded'];
                        $documentAttachments->pullFromAnotherDocument = -1;
                        $documentAttachments->save();
                    }
                }

            }
        }
        if ($attachmentFound == 0) {
            return $this->sendError('No Attachments Found', 500);
        } else {
            return $this->sendResponse($purchaseOrderID, 'PR attachments pulled successfully');
        }


    }

    public function updateSentSupplierDetail(Request $request)
    {
        $input = $request->all();

        $purchaseOrderID = $input['purchaseOrderID'];

        $typeID = $request->get('typeID');

        $employee = \Helper::getEmployeeInfo();

        $emailSentTo = 0;

        $procumentOrderUpdate = ProcumentOrder::where('purchaseOrderID', '=', $purchaseOrderID)->first();

        $company = Company::where('companySystemID', $procumentOrderUpdate->companySystemID)->first();

        $outputRecord = ProcumentOrder::where('purchaseOrderID', $procumentOrderUpdate->purchaseOrderID)->with(['detail' => function ($query) {
            $query->with('unit');
        }, 'approved_by' => function ($query) {
            $query->with('employee');
            $query->whereIN('documentSystemID', [2, 5, 52]);
        }, 'suppliercontact' => function ($query) {
            $query->where('isDefault', -1);
        }, 'company', 'transactioncurrency', 'companydocumentattachment', 'paymentTerms_by'])->get();

        $refernaceDoc = CompanyDocumentAttachment::where('companySystemID', $procumentOrderUpdate->companySystemID)
            ->where('documentSystemID', $procumentOrderUpdate->documentSystemID)
            ->first();

        $currencyDecimal = CurrencyMaster::select('DecimalPlaces')->where('currencyID', $procumentOrderUpdate->supplierTransactionCurrencyID)
            ->first();

        $decimal = 2;
        if (!empty($currencyDecimal)) {
            $decimal = $currencyDecimal['DecimalPlaces'];
        }

        $documentTitle = 'Purchase Order';
        if ($procumentOrderUpdate->documentSystemID == 2) {
            $documentTitle = 'Purchase Order';
        } else if ($procumentOrderUpdate->documentSystemID == 5 && $procumentOrderUpdate->poType_N == 5) {
            $documentTitle = 'Work Order';
        } else if ($procumentOrderUpdate->documentSystemID == 5 && $procumentOrderUpdate->poType_N == 6) {
            $documentTitle = 'Sub Work Order';
        } else if ($procumentOrderUpdate->documentSystemID == 52) {
            $documentTitle = 'Direct Order';
        }

        $poPaymentTerms = PoPaymentTerms::where('poID', $procumentOrderUpdate->purchaseOrderID)
            ->get();

        $paymentTermsView = '';

        if ($poPaymentTerms) {
            foreach ($poPaymentTerms as $val) {
                $paymentTermsView .= $val['paymentTemDes'] . ', ';
            }
        }

        $nowTime = time();

        $orderAddons = PoAddons::where('poId', $procumentOrderUpdate->purchaseOrderID)
            ->with(['category'])
            ->orderBy('idpoAddons', 'DESC')
            ->get();

        $order = array(
            'podata' => $outputRecord[0],
            'docRef' => $refernaceDoc,
            'termsCond' => $typeID,
            'numberFormatting' => $decimal,
            'title' => $documentTitle,
            'paymentTermsView' => $paymentTermsView,
            'addons' => $orderAddons

        );
        $html = view('print.purchase_order_print_pdf', $order);
        $pdf = \App::make('dompdf.wrapper');

        $pdf->loadHTML($html)->save('C:/inetpub/wwwroot/GEARSERP/GEARSWEBPORTAL/Portal/uploads/emailAttachment/po_print_' . $nowTime . '.pdf');

        $fetchSupEmail = SupplierContactDetails::where('supplierID', $procumentOrderUpdate->supplierID)
            ->get();

        $supplierMaster = SupplierMaster::find($procumentOrderUpdate->supplierID);

        $footer = "<font size='1.5'><i><p><br><br><br>SAVE PAPER - THINK BEFORE YOU PRINT!" .
            "<br>This is an auto generated email. Please do not reply to this email because we are not" .
            "monitoring this inbox. To get in touch with us, email us to systems@gulfenergy-int.com.</font>";

        if ($fetchSupEmail) {
            foreach ($fetchSupEmail as $row) {
                if (!empty($row->contactPersonEmail)) {
                    $emailSentTo = 1;
                    $dataEmail['empName'] = $procumentOrderUpdate->supplierName;
                    $dataEmail['empEmail'] = $row->contactPersonEmail;

                    $dataEmail['companySystemID'] = $procumentOrderUpdate->companySystemID;
                    $dataEmail['companyID'] = $procumentOrderUpdate->companyID;

                    $dataEmail['docID'] = $procumentOrderUpdate->documentID;
                    $dataEmail['docSystemID'] = $procumentOrderUpdate->documentSystemID;
                    $dataEmail['docSystemCode'] = $procumentOrderUpdate->purchaseOrderID;

                    $dataEmail['docApprovedYN'] = $procumentOrderUpdate->approved;
                    $dataEmail['docCode'] = $procumentOrderUpdate->purchaseOrderCode;
                    $dataEmail['ccEmailID'] = $employee->empEmail;

                    $temp = "Dear " . $procumentOrderUpdate->supplierName . ',<p> New Order has been released from ' . $company->CompanyName . $footer;

                    $location = \DB::table('systemmanualfolder')->first();
                    $pdfName = $location->folderDes . "emailAttachment\\po_print_" . $nowTime . ".pdf";

                    $dataEmail['isEmailSend'] = 0;
                    $dataEmail['attachmentFileName'] = $pdfName;
                    $dataEmail['alertMessage'] = "New order from " . $company->CompanyName . " " . $procumentOrderUpdate->purchaseOrderCode;
                    $dataEmail['emailAlertMessage'] = $temp;
                    Alert::create($dataEmail);
                }
            }
        }

        if ($emailSentTo == 0) {
            if ($supplierMaster) {
                if (!empty($supplierMaster->supEmail)) {
                    $emailSentTo = 1;
                    $dataEmail['empName'] = $procumentOrderUpdate->supplierName;
                    $dataEmail['empEmail'] = $supplierMaster->supEmail;

                    $dataEmail['companySystemID'] = $procumentOrderUpdate->companySystemID;
                    $dataEmail['companyID'] = $procumentOrderUpdate->companyID;

                    $dataEmail['docID'] = $procumentOrderUpdate->documentID;
                    $dataEmail['docSystemID'] = $procumentOrderUpdate->documentSystemID;
                    $dataEmail['docSystemCode'] = $procumentOrderUpdate->purchaseOrderID;

                    $dataEmail['docApprovedYN'] = $procumentOrderUpdate->approved;
                    $dataEmail['docCode'] = $procumentOrderUpdate->purchaseOrderCode;
                    $dataEmail['ccEmailID'] = $employee->empEmail;

                    $temp = "Dear " . $procumentOrderUpdate->supplierName . ',<p> New Order has been released from ' . $company->CompanyName . $footer;

                    $location = \DB::table('systemmanualfolder')->first();
                    $pdfName = $location->folderDes . "emailAttachment\\po_print_" . $nowTime . ".pdf";

                    $dataEmail['isEmailSend'] = 0;
                    $dataEmail['attachmentFileName'] = $pdfName;
                    $dataEmail['alertMessage'] = "New order from " . $company->CompanyName . " " . $procumentOrderUpdate->purchaseOrderCode;
                    $dataEmail['emailAlertMessage'] = $temp;
                    Alert::create($dataEmail);
                }
            }

        }

        if ($emailSentTo == 1) {
            $procumentOrderUpdate->sentToSupplier = -1;
            $procumentOrderUpdate->sentToSupplierByEmpSystemID = $employee->employeeSystemID;
            $procumentOrderUpdate->sentToSupplierByEmpID = $employee->empID;
            $procumentOrderUpdate->sentToSupplierByEmpName = $employee->empName;
            $procumentOrderUpdate->sentToSupplierDate = now();
            $procumentOrderUpdate->save();
        }


        if ($emailSentTo == 0) {
            return $this->sendResponse($emailSentTo, 'Supplier email is not updated. Email notification is not sent');
        } else {
            return $this->sendResponse($emailSentTo, 'Supplier notification email sent');
        }


    }

    public function getProcurementOrderReferBack(Request $request)
    {
        $input = $request->all();

        $purchaseOrderID = $input['purchaseOrderID'];

        $purchaseOrder = ProcumentOrder::find($purchaseOrderID);
        if (empty($purchaseOrder)) {
            return $this->sendError('Purchase Order not found');
        }

        if ($purchaseOrder->refferedBackYN != -1) {
            return $this->sendError('You cannot refer Back this PO');
        }

        $purchaseOrderArray = $purchaseOrder->toArray();

        $storePOMasterHistory = PurchaseOrderMasterRefferedHistory::insert($purchaseOrderArray);

        $fetchPurchaseOrderDetail = PurchaseOrderDetails::where('purchaseOrderMasterID', $purchaseOrderID)
            ->get();

        if (!empty($fetchPurchaseOrderDetail)) {
            foreach ($fetchPurchaseOrderDetail as $poDetail) {
                $poDetail['timesReferred'] = $purchaseOrder->timesReferred;
            }
        }

        $purchaseOrderDetailArray = $fetchPurchaseOrderDetail->toArray();

        $storePODetailHistory = PurchaseOrderDetailsRefferedHistory::insert($purchaseOrderDetailArray);

        $fetchAdvancePaymentDetails = AdvancePaymentDetails::where('purchaseOrderID', $purchaseOrderID)
            ->get();

        if (!empty($fetchAdvancePaymentDetails)) {
            foreach ($fetchAdvancePaymentDetails as $poAdvancePaymentDetails) {
                $poAdvancePaymentDetails['timesReferred'] = $purchaseOrder->timesReferred;
            }
        }

        $advancePaymentDetailsArray = $fetchAdvancePaymentDetails->toArray();

        $storePOAdvPaymentHistory = PurchaseOrderAdvPaymentRefferedback::insert($advancePaymentDetailsArray);

        $fetchPoPaymentTerms = PoPaymentTerms::where('poID', $input['purchaseOrderID'])
            ->get();

        if (!empty($fetchPoPaymentTerms)) {
            foreach ($fetchPoPaymentTerms as $poPoPaymentTerms) {
                $poPoPaymentTerms['timesReferred'] = $purchaseOrder->timesReferred;
            }
        }

        $PoPaymentTermsArray = $fetchPoPaymentTerms->toArray();

        $storePOAdvPaymentHistory = PoPaymentTermsRefferedback::insert($PoPaymentTermsArray);

        $fetchPoAddons = PoAddons::where('poId', $input['purchaseOrderID'])
            ->get();

        if (!empty($fetchPoAddons)) {
            foreach ($fetchPoAddons as $PoAddons) {
                $PoAddons['timesReferred'] = $purchaseOrder->timesReferred;
            }
        }

        $PoAddonsArray = $fetchPoAddons->toArray();

        $storePOPoAddonsHistory = PoAddonsRefferedBack::insert($PoAddonsArray);

        $fetchDocumentApproved = DocumentApproved::where('documentSystemCode', $purchaseOrderID)
            ->where('companySystemID', $purchaseOrder->companySystemID)
            ->where('documentSystemID', $purchaseOrder->documentSystemID)
            ->get();

        if (!empty($fetchDocumentApproved)) {
            foreach ($fetchDocumentApproved as $DocumentApproved) {
                $DocumentApproved['refTimes'] = $purchaseOrder->timesReferred;
            }
        }

        $DocumentApprovedArray = $fetchDocumentApproved->toArray();

        $storeDocumentReferedHistory = DocumentReferedHistory::insert($DocumentApprovedArray);

        $deleteApproval = DocumentApproved::where('documentSystemCode', $purchaseOrderID)
            ->where('companySystemID', $purchaseOrder->companySystemID)
            ->where('documentSystemID', $purchaseOrder->documentSystemID)
            ->delete();

        if ($deleteApproval) {
            $purchaseOrder->refferedBackYN = 0;
            $purchaseOrder->poConfirmedYN = 0;
            $purchaseOrder->poConfirmedByEmpSystemID = null;
            $purchaseOrder->poConfirmedByEmpID = null;
            $purchaseOrder->poConfirmedByName = null;
            $purchaseOrder->poConfirmedDate = null;
            $purchaseOrder->RollLevForApp_curr = 1;
            $purchaseOrder->save();
        }

        return $this->sendResponse($purchaseOrder->toArray(), 'Purchase Order Amend successfully');
    }

    public function reportPoEmployeePerformance(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'companySystemID' => 'required',
            'years' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        $request = (object)$this->convertArrayToSelectedValue($request->all(), array('companySystemID'));

        $companyID = "";
        $checkIsGroup = Company::find($request->companySystemID);
        if ($checkIsGroup->isGroup) {
            $companyID = \Helper::getGroupCompany($request->companySystemID);
        } else {
            $companyID = (array)$request->companySystemID;
        }

        $year = $request->years;

        $sumMonthWise = '';
        $countMonthWise = '';

        $monthArray = array(1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Aug', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dece');

        foreach ($monthArray as $key => $mon) {
            $sumMonthWise .= " SUM(IF(DocMONTH = $key, POValue , 0)) as Tot$mon ,";
            $countMonthWise .= " SUM(IF(DocMONTH = $key, 1 , 0)) as Cou$mon ,";
        }

        $output = \DB::select('SELECT
	poConfirmedByEmpID,
	POConfirmedEmpName,
	designation,
	count(poConfirmedByEmpSystemID) as totalCount,
	sum(POValue) as totalValue,
	' . $sumMonthWise . '
	' . $countMonthWise . '
	DocMONTH
FROM
(
SELECT
	erp_purchaseordermaster.purchaseOrderID,
	erp_purchaseordermaster.companySystemID,
	erp_purchaseordermaster.companyID,
	erp_purchaseordermaster.purchaseOrderCode,
	erp_purchaseordermaster.narration,
	erp_purchaseordermaster.supplierPrimaryCode,
	erp_purchaseordermaster.supplierName,
	YEAR (erp_purchaseordermaster.approvedDate) AS YEAR,
	erp_purchaseordermaster.poConfirmedByEmpSystemID,
	erp_purchaseordermaster.poConfirmedByEmpID,
	employees.empName as POConfirmedEmpName,
	hrms_designation.designation,
	sum(erp_purchaseorderdetails.GRVcostPerUnitComRptCur*erp_purchaseorderdetails.noQty) AS POVALUE,
	MONTH ( erp_purchaseordermaster.approvedDate ) AS DocMONTH
FROM
	erp_purchaseordermaster
	INNER JOIN erp_purchaseorderdetails ON erp_purchaseordermaster.purchaseOrderID = erp_purchaseorderdetails.purchaseOrderMasterID
	INNER JOIN employees ON erp_purchaseordermaster.poConfirmedByEmpSystemID = employees.employeeSystemID
	INNER JOIN hrms_employeedetails ON employees.employeeSystemID = hrms_employeedetails.employeeSystemID
	INNER JOIN hrms_designation ON hrms_designation.designationID = hrms_employeedetails.designationID
WHERE
	year(erp_purchaseordermaster.approvedDate) = "' . $year . '"
	AND erp_purchaseordermaster.companySystemID IN (' . join(',', $companyID) . ')
	AND erp_purchaseordermaster.poConfirmedYN = 1
	AND erp_purchaseordermaster.poCancelledYN = 0
	AND erp_purchaseordermaster.approved =-1
	AND erp_purchaseordermaster.poType_N <> 6
group by purchaseOrderID,companySystemID) as pocountfnal
	group by pocountfnal.poConfirmedByEmpSystemID ORDER BY totalCount DESC;');
        //dd(DB::getQueryLog());

        return $this->sendResponse($output, 'Data retrieved successfully');

    }

    public function exportPoEmployeePerformance(Request $request)
    {
        $request = (object)$this->convertArrayToSelectedValue($request->all(), array('companySystemID'));

        $companyID = "";
        $checkIsGroup = Company::find($request->companySystemID);
        if ($checkIsGroup->isGroup) {
            $companyID = \Helper::getGroupCompany($request->companySystemID);
        } else {
            $companyID = (array)$request->companySystemID;
        }

        $year = $request->years;
        $type = $request->type;
        $tempType = $request->temp;

        $sumMonthWise = '';
        $countMonthWise = '';

        $monthArray = array(1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Aug', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dece');

        foreach ($monthArray as $key => $mon) {
            $sumMonthWise .= " SUM(IF(DocMONTH = $key, POValue , 0)) as Tot$mon ,";
            $countMonthWise .= " SUM(IF(DocMONTH = $key, 1 , 0)) as Cou$mon ,";
        }

        $output = \DB::select('select
	poConfirmedByEmpID,
	POConfirmedEmpName,
	designation,
	count(poConfirmedByEmpSystemID) as totalCount,
	sum(POValue) as totalValue,
	' . $sumMonthWise . '
	' . $countMonthWise . '
	DocMONTH
from
(
SELECT
	erp_purchaseordermaster.purchaseOrderID,
	erp_purchaseordermaster.companySystemID,
	erp_purchaseordermaster.companyID,
	erp_purchaseordermaster.purchaseOrderCode,
	erp_purchaseordermaster.narration,
	erp_purchaseordermaster.supplierPrimaryCode,
	erp_purchaseordermaster.supplierName,
	YEAR (erp_purchaseordermaster.approvedDate) AS YEAR,
	erp_purchaseordermaster.poConfirmedByEmpSystemID,
	erp_purchaseordermaster.poConfirmedByEmpID,
	employees.empName as POConfirmedEmpName,
	hrms_designation.designation,
	sum(erp_purchaseorderdetails.GRVcostPerUnitComRptCur*erp_purchaseorderdetails.noQty) AS POVALUE,
	MONTH ( erp_purchaseordermaster.approvedDate ) AS DocMONTH
FROM
	erp_purchaseordermaster
	INNER JOIN erp_purchaseorderdetails ON erp_purchaseordermaster.purchaseOrderID = erp_purchaseorderdetails.purchaseOrderMasterID
	INNER JOIN employees ON erp_purchaseordermaster.poConfirmedByEmpSystemID = employees.employeeSystemID
	INNER JOIN hrms_employeedetails ON employees.employeeSystemID = hrms_employeedetails.employeeSystemID
	INNER JOIN hrms_designation ON hrms_designation.designationID = hrms_employeedetails.designationID
WHERE
	year(erp_purchaseordermaster.approvedDate) = "' . $year . '"
	AND erp_purchaseordermaster.companySystemID IN (' . join(',', $companyID) . ')
	AND erp_purchaseordermaster.poConfirmedYN = 1
	AND erp_purchaseordermaster.poCancelledYN = 0
	AND erp_purchaseordermaster.approved =-1
	AND erp_purchaseordermaster.poType_N <> 6
group by purchaseOrderID,companySystemID) as pocountfnal
	group by pocountfnal.poConfirmedByEmpSystemID ORDER BY totalCount DESC;');

        if ($tempType == 1) {
            if ($output) {
                $x = 0;
                foreach ($output as $val) {
                    $data[$x]['Emp ID'] = $val->poConfirmedByEmpID;
                    $data[$x]['Employee Name'] = $val->POConfirmedEmpName;
                    $data[$x]['Designation'] = $val->designation;
                    $data[$x]['Year'] = $year;
                    $data[$x]['Jan-Count'] = $val->CouJan;
                    $data[$x]['Jan-Amt'] = $val->TotJan;
                    $data[$x]['Feb-Count'] = $val->CouFeb;
                    $data[$x]['Feb-Amt'] = $val->TotFeb;
                    $data[$x]['Mar-Count'] = $val->CouMar;
                    $data[$x]['Mar-Amt'] = $val->TotMar;
                    $data[$x]['Apr-Count'] = $val->CouApr;
                    $data[$x]['Apr-Amt'] = $val->TotApr;
                    $data[$x]['May-Count'] = $val->CouMay;
                    $data[$x]['May-Amt'] = $val->TotMay;
                    $data[$x]['Jun-Count'] = $val->CouJun;
                    $data[$x]['Jun-Amt'] = $val->TotJun;
                    $data[$x]['Jul-Count'] = $val->CouJul;
                    $data[$x]['Jul-Amt'] = $val->TotJul;
                    $data[$x]['Aug-Count'] = $val->CouAug;
                    $data[$x]['Aug-Amt'] = $val->TotAug;
                    $data[$x]['Sept-Count'] = $val->CouSep;
                    $data[$x]['Sept-Amt'] = $val->TotSep;
                    $data[$x]['Oct-Count'] = $val->CouOct;
                    $data[$x]['Oct-Amt'] = $val->TotOct;
                    $data[$x]['Nov-Count'] = $val->CouNov;
                    $data[$x]['Nov-Amt'] = $val->TotNov;
                    $data[$x]['Dec-Count'] = $val->CouDece;
                    $data[$x]['Dec-Amt'] = $val->TotDece;
                    $data[$x]['Total Count'] = $val->totalCount;
                    $data[$x]['Total Amount'] = $val->totalValue;
                    $x++;
                }
            } else {
                $data = array();
            }

        } else if ($tempType == 2) {
            if ($output) {
                $x = 0;
                foreach ($output as $val) {
                    $data[$x]['Emp ID'] = $val->poConfirmedByEmpID;
                    $data[$x]['Employee Name'] = $val->POConfirmedEmpName;
                    $data[$x]['Designation'] = $val->designation;
                    $data[$x]['Year'] = $year;
                    $data[$x]['Count'] = $val->totalCount;
                    $data[$x]['Total'] = $val->totalValue;
                    $x++;
                }
            } else {
                $data = array();
            }
        }
        $csv = \Excel::create('payment_suppliers_by_year', function ($excel) use ($data) {
            $excel->sheet('sheet name', function ($sheet) use ($data) {
                $sheet->fromArray($data, null, 'A1', true);
                $sheet->setAutoSize(true);
                $sheet->getStyle('C1:C2')->getAlignment()->setWrapText(true);
            });
            $lastrow = $excel->getActiveSheet()->getHighestRow();
            $excel->getActiveSheet()->getStyle('A1:J' . $lastrow)->getAlignment()->setWrapText(true);
        })->download($type);

        return $this->sendResponse(array(), 'successfully export');

    }

    public function exportProcumentOrderMaster(Request $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input, array('serviceLineSystemID', 'poCancelledYN', 'poConfirmedYN', 'approved', 'grvRecieved', 'month', 'year', 'invoicedBooked', 'supplierID', 'sentToSupplier', 'logisticsAvailable'));

        $type = $input['type'];

        $output = ProcumentOrder::where('companySystemID', $input['companyId']);
        $output->where('documentSystemID', $input['documentId']);

        if (array_key_exists('serviceLineSystemID', $input)) {
            if ($input['serviceLineSystemID'] && !is_null($input['serviceLineSystemID'])) {
                $output->where('serviceLineSystemID', $input['serviceLineSystemID']);
            }
        }

        if (array_key_exists('poCancelledYN', $input)) {
            if (($input['poCancelledYN'] == 0 || $input['poCancelledYN'] == -1) && !is_null($input['poCancelledYN'])) {
                $output->where('poCancelledYN', $input['poCancelledYN']);
            }
        }

        if (array_key_exists('poConfirmedYN', $input)) {
            if (($input['poConfirmedYN'] == 0 || $input['poConfirmedYN'] == 1) && !is_null($input['poConfirmedYN'])) {
                $output->where('poConfirmedYN', $input['poConfirmedYN']);
            }
        }

        if (array_key_exists('approved', $input)) {
            if (($input['approved'] == 0 || $input['approved'] == -1) && !is_null($input['approved'])) {
                $output->where('approved', $input['approved']);
            }
        }

        if (array_key_exists('grvRecieved', $input)) {
            if (($input['grvRecieved'] == 0 || $input['grvRecieved'] == 1 || $input['grvRecieved'] == 2) && !is_null($input['grvRecieved'])) {
                $output->where('grvRecieved', $input['grvRecieved']);
            }
        }

        if (array_key_exists('invoicedBooked', $input)) {
            if (($input['invoicedBooked'] == 0 || $input['invoicedBooked'] == 1 || $input['invoicedBooked'] == 2) && !is_null($input['invoicedBooked'])) {
                $output->where('invoicedBooked', $input['invoicedBooked']);
            }
        }

        if (array_key_exists('month', $input)) {
            if ($input['month'] && !is_null($input['month'])) {
                $output->whereMonth('createdDateTime', '=', $input['month']);
            }
        }

        if (array_key_exists('year', $input)) {
            if ($input['year'] && !is_null($input['year'])) {
                $output->whereYear('createdDateTime', '=', $input['year']);
            }
        }

        if (array_key_exists('supplierID', $input)) {
            if ($input['supplierID'] && !is_null($input['supplierID'])) {
                $output->where('supplierID', $input['supplierID']);
            }
        }

        if (array_key_exists('sentToSupplier', $input)) {
            if (($input['sentToSupplier'] == 0 || $input['sentToSupplier'] == -1) && !is_null($input['sentToSupplier'])) {
                $output->where('sentToSupplier', $input['sentToSupplier']);
            }
        }

        if (array_key_exists('logisticsAvailable', $input)) {
            if (($input['logisticsAvailable'] == 0 || $input['logisticsAvailable'] == -1) && !is_null($input['logisticsAvailable'])) {
                $output->where('logisticsAvailable', $input['logisticsAvailable']);
            }
        }

        $output->with(['created_by', 'confirmed_by', 'currency', 'localcurrency', 'reportingcurrency', 'fcategory', 'segment', 'supplier', 'company', 'detail' => function ($query) {
            $query->selectRaw('COALESCE(SUM(GRVcostPerUnitSupTransCur),0) as transactionSum,COALESCE(SUM(GRVcostPerUnitLocalCur*noQty),0) as localSum,COALESCE(SUM(GRVcostPerUnitComRptCur*noQty),0) as rptSum,purchaseOrderMasterID');
            $query->groupBy('purchaseOrderMasterID');
        }, 'supplier' => function ($query) {
            $query->with('country');
        }, 'advance_detail' => function ($query) {
            $query->selectRaw('COALESCE(SUM(reqAmount),0) as advanceSum,poID');
            $query->groupBy('poID');
        }]);
        $output->orderBy('purchaseOrderID', 'desc');
        $output = $output->get();

        if ($output) {
            $x = 0;
            foreach ($output as $val) {
                $data[$x]['Company ID'] = $val->companyID;
                if ($val->company) {
                    $data[$x]['Company Name'] = $val->company->CompanyName;
                }
                $data[$x]['Order Code'] = $val->purchaseOrderCode;
                if ($val->segment) {
                    $data[$x]['Service Line'] = $val->segment->ServiceLineDes;
                }
                $data[$x]['Created at'] = \Helper::dateFormat($val->createdDateTime);
                if ($val->created_by) {
                    $data[$x]['Created By'] = $val->created_by->empName;
                }
                if ($val->fcategory) {
                    $data[$x]['Category'] = $val->fcategory->categoryDescription;
                }
                $data[$x]['Narration'] = $val->narration;
                $data[$x]['Supplier Code'] = $val->supplierPrimaryCode;
                $data[$x]['Supplier Name'] = $val->supplierName;
                $data[$x]['Credit Period'] = $val->creditPeriod;
                if ($val->supplier) {
                    $data[$x][' Supplier Country'] = $val->supplier->country->countryName;
                }
                $data[$x]['Expected Delivery Date'] = \Helper::dateFormat($val->expectedDeliveryDate);
                $data[$x]['Delivery Terms'] = $val->deliveryTerms;
                $data[$x]['Penalty Terms'] = $val->panaltyTerms;
                if ($val->poConfirmedYN == 1) {
                    $data[$x]['Confirmed Status'] = 'Yes';
                } else {
                    $data[$x]['Confirmed Status'] = 'No';
                }
                $data[$x]['Confirmed Date'] = \Helper::dateFormat($val->poConfirmedDate);
                $data[$x]['Confirmed By'] = $val->poConfirmedByName;
                if ($val->approved == -1) {
                    $data[$x]['Approved Status'] = 'Yes';
                } else {
                    $data[$x]['Approved Status'] = 'No';
                }
                $data[$x]['Approved Date'] = \Helper::dateFormat($val->approvedDate);
                if ($val->currency) {
                    $data[$x]['Transaction Currency'] = $val->currency->CurrencyCode;
                }
                if ($val->currency) {
                    $data[$x]['Transaction Amount'] = $val->poTotalSupplierTransactionCurrency;
                }
                if ($val->localcurrency) {
                    $data[$x]['Local Amount (' . $val->localcurrency->CurrencyCode . ')'] = $val->poTotalLocalCurrency;;
                }
                if ($val->reportingcurrency) {
                    $data[$x]['Reporting Amount (' . $val->reportingcurrency->CurrencyCode . ')'] = $val->poTotalComRptCurrency;;
                }
                if ($val->advance_detail) {
                    if (isset($val->advance_detail[0]->advanceSum)) {
                        $data[$x]['Advance Payment Available'] = 'Yes';
                    } else {
                        $data[$x]['Advance Payment Available'] = 'No';
                    }
                }
                if ($val->advance_detail) {
                    if (isset($val->advance_detail[0]->advanceSum)) {
                        $data[$x]['Total Advance Payment Amount'] = $val->advance_detail[0]->advanceSum;
                    } else {
                        $data[$x]['Total Advance Payment Amount'] = '0';
                    }
                }

                /*              if ($val->detail) {
                                  $data[$x]['Transaction Total'] = $val->detail[0]->transactionSum;
                              }*/

                /*       if ($val->detail) {
                           $data[$x]['Local Total'] = $val->detail[0]->localSum;
                       }*/
                /*  if ($val->detail) {
                      $data[$x]['Reporting Total'] = $val->detail[0]->rptSum;
                  }*/
                $x++;
            }
        } else {
            $data = array();
        }

        $csv = \Excel::create('po_master', function ($excel) use ($data) {
            $excel->sheet('sheet name', function ($sheet) use ($data) {
                $sheet->fromArray($data, null, 'A1', true);
                $sheet->setAutoSize(true);
                $sheet->getStyle('C1:C2')->getAlignment()->setWrapText(true);
            });
            $lastrow = $excel->getActiveSheet()->getHighestRow();
            $excel->getActiveSheet()->getStyle('A1:J' . $lastrow)->getAlignment()->setWrapText(true);
        })->download($type);

        return $this->sendResponse(array(), 'successfully export');
    }


    public function reportPoToPayment(Request $request)
    {
        $input = $request->all();
        $purchaseOrder = $this->getPoToPaymentQry($input);
        $data = \DataTables::of($purchaseOrder)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('purchaseOrderID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->addColumn('grvMasters', function ($row) {
                return $this->getPOtoPaymentChain($row);
            })
            ->make(true);

        return $data;
    }

    function getPOtoPaymentChain($row)
    {
        $grvMasters = GRVDetails::selectRaw('sum(noQty*GRVcostPerUnitLocalCur) as localAmount,
                                        sum(noQty*GRVcostPerUnitComRptCur) as rptAmount,purchaseOrderMastertID,grvAutoID')
            ->where('purchaseOrderMastertID', $row->purchaseOrderID)
            ->with(['grv_master'])
            ->groupBy('grvAutoID')
            ->get();

        foreach ($grvMasters as $grv) {
            $invoices = BookInvSuppDet::selectRaw('sum(totLocalAmount) as localAmount,
                                                 sum(totRptAmount) as rptAmount,grvAutoID,bookingSuppMasInvAutoID')
                ->where('grvAutoID', $grv->grvAutoID)
                ->where('purchaseOrderID', $row->purchaseOrderID)
                ->with(['suppinvmaster'])
                ->groupBy('bookingSuppMasInvAutoID')
                ->get();

            foreach ($invoices as $invoice) {
                //supplierPaymentAmount
                $paymentsInvoice = PaySupplierInvoiceDetail::selectRaw('sum(paymentLocalAmount) as localAmount,
                                                 sum(paymentComRptAmount) as rptAmount,bookingInvSystemCode,PayMasterAutoId,matchingDocID')
                    ->where('bookingInvSystemCode', $invoice->bookingSuppMasInvAutoID)
                    //->where('addedDocumentSystemID', 11)
                    ->where('matchingDocID', 0)
                    ->with(['payment_master'])
                    ->groupBy('PayMasterAutoId')
                    ->get();

                $paymentsInvoiceMatch = PaySupplierInvoiceDetail::selectRaw('sum(paymentLocalAmount) as localAmount,
                                                 sum(paymentComRptAmount) as rptAmount,bookingInvSystemCode,matchingDocID')
                    ->where('bookingInvSystemCode', $invoice->bookingSuppMasInvAutoID)
                    //->where('addedDocumentSystemID', 11)
                    ->where('matchingDocID', '>', 0)
                    ->with(['matching_master'])
                    ->groupBy('PayMasterAutoId')
                    ->get();

                $totalInvoices = $paymentsInvoice->toArray() + $paymentsInvoiceMatch->toArray();

                $invoice->payments = $totalInvoices;
            }

            $grv->invoices = $invoices->toArray();
        }

        return $grvMasters->toArray();
    }

    public function getPoToPaymentQry($request)
    {
        $input = $request;
        $from = "";
        $to = "";

        if (array_key_exists('fromDate', $input) && $input['fromDate']) {
            $from = ((new Carbon($input['fromDate']))->format('Y-m-d'));
        }

        if (array_key_exists('toDate', $input) && $input['toDate']) {
            $to = ((new Carbon($input['toDate']))->format('Y-m-d'));
        }

        if (array_key_exists('toDate', $input) && array_key_exists('fromDate', $input) &&
            $input['toDate'] && $input['fromDate'] && $to <= $from
        ) {
            //$from = "";
            //$to = "";
        }

        $search = $input['search']['value'];
        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
        }

        $purchaseOrder = ProcumentOrder::where('companySystemID', $input['companyId'])
            ->where('poConfirmedYN', 1)
            ->where('poCancelledYN', 0)
            ->where('approved', -1)
            ->where('documentSystemID', '!=', 5)
            ->when($from && $to == "", function ($q) use ($from, $to) {
                return $q->where('approvedDate', '>=', $from);
            })
            ->when($from == "" && $to, function ($q) use ($from, $to) {
                return $q->where('approvedDate', '<=', $to);
            })
            ->when($from && $to, function ($q) use ($from, $to) {
                return $q->whereBetween('approvedDate', [$from, $to]);
            })
            ->when(request('supplierID', false), function ($q) use ($input) {
                return $q->where('supplierID', $input['supplierID']);
            })
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('purchaseOrderCode', 'LIKE', "%{$search}%")
                        ->orWhere('narration', 'LIKE', "%{$search}%");
                });
            })
            ->with(['supplier','fcategory']);
        /*->with(['supplier', 'detail' => function ($poDetail) {
            $poDetail->with([
                'grv_details' => function ($q) {
                    $q->with(['grv_master']);
                }]);
        }]);*/

        return $purchaseOrder;
    }


    public function exportPoToPaymentReport(Request $request)
    {
        $input = $request->all();
        $data = array();
        $output = ($this->getPoToPaymentQry($input))->orderBy('purchaseOrderID', 'DES')->get();

        foreach ($output as $row) {
            $row->grvMasters = $this->getPOtoPaymentChain($row);
        }

        $type = $request->type;
        if (!empty($output)) {
            $x = 0;
            foreach ($output as $value) {
                $data[$x]['Company ID'] = $value->companyID;
                //$data[$x]['Company Name'] = $val->CompanyName;
                $data[$x]['PO Number'] = $value->purchaseOrderCode;

                if($value->fcategory){
                    $data[$x]['Category'] = $value->fcategory->categoryDescription;
                }else{
                    $data[$x]['Category'] = '';
                }

                $data[$x]['PO Approved Date'] = \Helper::dateFormat($value->approvedDate);
                $data[$x]['Narration'] = $value->narration;
                if ($value->supplier) {
                    $data[$x]['Supplier Code'] = $value->supplier->primarySupplierCode;
                    $data[$x]['Supplier Name'] = $value->supplier->supplierName;
                } else {
                    $data[$x]['Supplier Code'] = '';
                    $data[$x]['Supplier Name'] = '';
                }
                $data[$x]['PO Amount'] = number_format($value->poTotalComRptCurrency, 2);

                if (count($value->grvMasters) > 0) {
                    $grvMasterCount = 0;
                    foreach ($value->grvMasters as $grv) {
                        if ($grvMasterCount != 0) {
                            $x++;
                            $data[$x]['Company ID'] = '';
                            //$data[$x]['Company Name'] = $val->CompanyName;
                            $data[$x]['PO Number'] = '';
                            $data[$x]['PO Approved Date'] = '';
                            $data[$x]['Narration'] = '';
                            $data[$x]['Supplier Code'] = '';
                            $data[$x]['Supplier Name'] = '';
                            $data[$x]['PO Amount'] = '';
                        }

                        if ($grv['grv_master']) {
                            $data[$x]['GRV Code'] = $grv['grv_master']['grvPrimaryCode'];
                            $data[$x]['GRV Date'] = \Helper::dateFormat($grv['grv_master']['grvDate']);
                        } else {
                            $data[$x]['GRV Code'] = '';
                            $data[$x]['GRV Date'] = '';
                        }

                        $data[$x]['GRV Amount'] = number_format($grv['rptAmount'], 2);

                        if (count($grv['invoices']) > 0) {
                            $invoicesCount = 0;
                            foreach ($grv['invoices'] as $invoice) {
                                if ($invoicesCount != 0) {
                                    $x++;
                                    $data[$x]['Company ID'] = '';
                                    //$data[$x]['Company Name'] = $val->CompanyName;
                                    $data[$x]['PO Number'] = '';
                                    $data[$x]['PO Approved Date'] = '';
                                    $data[$x]['Narration'] = '';
                                    $data[$x]['Supplier Code'] = '';
                                    $data[$x]['Supplier Name'] = '';
                                    $data[$x]['Amount'] = '';
                                    $data[$x]['GRV Code'] = '';
                                    $data[$x]['GRV Date'] = '';
                                    $data[$x]['GRV Amount'] = '';
                                }

                                if ($invoice['suppinvmaster']) {
                                    $data[$x]['Invoice Code'] = $invoice['suppinvmaster']['bookingInvCode'];
                                    $data[$x]['Invoice Date'] = \Helper::dateFormat($invoice['suppinvmaster']['supplierInvoiceDate']);
                                } else {
                                    $data[$x]['Invoice Code'] = '';
                                    $data[$x]['Invoice Date'] = '';
                                }
                                $data[$x]['Invoice Amount'] = number_format($invoice['rptAmount'], 2);

                                if (count($invoice['payments']) > 0) {
                                    $paymentsCount = 0;
                                    foreach ($invoice['payments'] as $payment) {
                                        if ($paymentsCount != 0) {
                                            $x++;
                                            $data[$x]['Company ID'] = '';
                                            //$data[$x]['Company Name'] = $val->CompanyName;
                                            $data[$x]['PO Number'] = '';
                                            $data[$x]['PO Approved Date'] = '';
                                            $data[$x]['Narration'] = '';
                                            $data[$x]['Supplier Code'] = '';
                                            $data[$x]['Supplier Name'] = '';
                                            $data[$x]['Amount'] = '';
                                            $data[$x]['GRV Code'] = '';
                                            $data[$x]['GRV Date'] = '';
                                            $data[$x]['GRV Amount'] = '';
                                            $data[$x]['Invoice Code'] = '';
                                            $data[$x]['Invoice Date'] = '';
                                            $data[$x]['Invoice Amount'] = '';
                                        }

                                        if ($payment['matchingDocID'] == 0) {
                                            if (!empty($payment['payment_master'])) {
                                                $data[$x]['Payment Code'] = $payment['payment_master']['BPVcode'];
                                                $data[$x]['Payment Date'] = \Helper::dateFormat($payment['payment_master']['BPVdate']);
                                            } else {
                                                $data[$x]['Payment Code'] = '';
                                                $data[$x]['Payment Date'] = '';
                                            }
                                        } else if ($payment['matchingDocID'] > 0) {
                                            if (!empty($payment['matching_master'])) {
                                                $data[$x]['Payment Code'] = $payment['matching_master']['matchingDocCode'];
                                                $data[$x]['Payment Date'] = \Helper::dateFormat($payment['matching_master']['matchingDocdate']);
                                            } else {
                                                $data[$x]['Payment Code'] = '';
                                                $data[$x]['Payment Date'] = '';
                                            }
                                        } else {
                                            $data[$x]['Payment Code'] = '';
                                            $data[$x]['Payment Date'] = '';
                                        }
                                        $data[$x]['Paid Amount'] = number_format($payment['rptAmount'], 2);
                                        $paymentsCount++;
                                    }
                                } else {
                                    $data[$x]['Payment Code'] = '';
                                    $data[$x]['Payment Date'] = '';
                                    $data[$x]['Paid Amount'] = '';
                                }
                                $invoicesCount++;
                            }
                        } else {
                            $data[$x]['Invoice Code'] = '';
                            $data[$x]['Invoice Date'] = '';
                            $data[$x]['Invoice Amount'] = '';
                            $data[$x]['Payment Code'] = '';
                            $data[$x]['Payment Date'] = '';
                            $data[$x]['Paid Amount'] = '';
                        }
                        $grvMasterCount++;
                    }
                } else {
                    $data[$x]['GRV Code'] = '';
                    $data[$x]['GRV Date'] = '';
                    $data[$x]['GRV Amount'] = '';
                    $data[$x]['Invoice Code'] = '';
                    $data[$x]['Invoice Date'] = '';
                    $data[$x]['Invoice Amount'] = '';
                    $data[$x]['Payment Code'] = '';
                    $data[$x]['Payment Date'] = '';
                    $data[$x]['Paid Amount'] = '';
                }
                $x++;
            }
        }

        $csv = \Excel::create('po_to_payment', function ($excel) use ($data) {
            $excel->sheet('sheet name', function ($sheet) use ($data) {
                $sheet->fromArray($data, null, 'A1', true);
                $sheet->setAutoSize(true);
                $sheet->getStyle('C1:C2')->getAlignment()->setWrapText(true);
            });
            $lastrow = $excel->getActiveSheet()->getHighestRow();
            $excel->getActiveSheet()->getStyle('A1:J' . $lastrow)->getAlignment()->setWrapText(true);
        })->download($type);

        return $this->sendResponse(array(), 'successfully export');
    }

    public function reportPoToPaymentFilterOptions(Request $request)
    {
        $input = $request->all();

        $companyId = $input['companyId'];

        $suppliers = SupplierAssigned::where('companySystemID', $companyId);

        if (array_key_exists('search', $input)) {
            $search = $input['search'];
            $suppliers = $suppliers->where(function ($query) use ($search) {
                $query->where('primarySupplierCode', 'LIKE', "%{$search}%")
                    ->orWhere('supplierName', 'LIKE', "%{$search}%");
            });
        }
        $suppliers = $suppliers->take(15)->get(['companySystemID', 'primarySupplierCode', 'supplierName', 'supplierCodeSytem']);
        $output = array('suppliers' => $suppliers);

        return $this->sendResponse($output, 'Record retrieved successfully');
    }

    public function getAdvancePaymentRequestStatusHistory(Request $request)
    {
        $input = $request->all();

        $companySystemID = $input['companySystemID'];
        $purchaseOrderID = $input['purchaseOrderID'];
        $advancePaymentId = $input['poAdvPaymentID'];

        $detail = DB::select('SELECT
				erp_paysupplierinvoicemaster.PayMasterAutoId AS PayMasterAutoId,
				suppliermaster.primarySupplierCode,
			    suppliermaster.supplierName,
			    erp_paysupplierinvoicemaster.BPVsupplierID,
				erp_paysupplierinvoicemaster.documentID,
				erp_paysupplierinvoicemaster.companyID,
				"Advance Payment" AS paymentType,
				erp_paysupplierinvoicemaster.BPVcode,
				erp_paysupplierinvoicemaster.BPVdate,
				"-" AS supplierInvoiceNo,
				"-" AS supplierInvoiceDate,
				erp_advancepaymentdetails.purchaseOrderID,
				erp_advancepaymentdetails.supplierTransAmount AS TransAmount,
				erp_advancepaymentdetails.localAmount AS LocalAmount,
				erp_advancepaymentdetails.comRptAmount AS RptAmount,
				erp_paysupplierinvoicemaster.trsClearedDate,
				erp_paysupplierinvoicemaster.bankClearedDate,
				erp_paysupplierinvoicemaster.approvedDate,
				erp_paysupplierinvoicemaster.invoiceType,
				erp_paysupplierinvoicemaster.confirmedYN,
				erp_paysupplierinvoicemaster.approved,
				cm1.CurrencyCode AS transactionCurrency,
			    cm2.CurrencyCode AS localCurrency,
			    cm3.CurrencyCode AS reportingCurrency,
			    cm1.DecimalPlaces AS transactionDeci,
				cm2.DecimalPlaces AS localDeci,
				cm3.DecimalPlaces AS reportingDec
			FROM
				erp_paysupplierinvoicemaster
			INNER JOIN erp_advancepaymentdetails ON erp_paysupplierinvoicemaster.PayMasterAutoId = erp_advancepaymentdetails.PayMasterAutoId
			INNER JOIN suppliermaster ON erp_paysupplierinvoicemaster.BPVsupplierID = suppliermaster.supplierCodeSystem
			INNER JOIN currencymaster cm1 ON cm1.currencyID = erp_advancepaymentdetails.supplierTransCurrencyID
			INNER JOIN currencymaster cm2 ON cm2.currencyID = erp_advancepaymentdetails.localCurrencyID
			INNER JOIN currencymaster cm3 ON cm3.currencyID = erp_advancepaymentdetails.comRptCurrencyID
			WHERE
				erp_paysupplierinvoicemaster.companySystemID = ' . $companySystemID . '
			AND erp_advancepaymentdetails.purchaseOrderID = ' . $purchaseOrderID . '
			AND erp_advancepaymentdetails.poAdvPaymentID = ' . $advancePaymentId . '
	');

        return $this->sendResponse($detail, 'payment status retrieved successfully');
    }

    public function poExpectedDeliveryDateAmend(Request $request)
    {

        $input = $request->all();

        $purchaseOrderID = $input['purchaseOrderID'];

        $purchaseOrderMasterData = ProcumentOrder::find($purchaseOrderID);

        if (empty($purchaseOrderMasterData)) {
            return $this->sendError('Purchase Order not found');
        }

        if (isset($input['deliveryDate'])) {
            if ($input['deliveryDate']) {
                $input['deliveryDate'] = new Carbon($input['deliveryDate']);
            }
        }
        $purchaseOrderMasterData->expectedDeliveryDate = $input['deliveryDate'];
        $purchaseOrderMasterData->save();

        return $this->sendResponse($purchaseOrderMasterData->toArray(), 'Record updated successfully');
    }
}
