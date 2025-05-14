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
 * -- Date: 07-February 2020 By: Zakeeul Description: Added new functions named as getReportSavingFliterData(),
 * -- Date: 29-June 2020 By: Rilwan Description: Added new functions named as checkEOSPolicyAndSupplier(),
 */

namespace App\Http\Controllers\API;

use App\Exports\Procument\PoToPaymentReport;
use App\helper\Helper;
use App\helper\TaxService;
use App\Http\Controllers\AppBaseController;
use App\Http\Requests\API\CreateProcumentOrderAPIRequest;
use App\Http\Requests\API\UpdateProcumentOrderAPIRequest;
use App\Jobs\CreateSupplierTransactions;
use App\Models\AddonCostCategories;
use App\Models\AdvancePaymentDetails;
use App\Models\AdvanceReceiptDetails;
use App\Models\BookInvSuppDet;
use App\Models\BookInvSuppMaster;
use App\Models\BudgetConsumedData;
use App\Models\CompanyDigitalStamp;
use App\Models\ItemCategoryTypeMaster;
use App\Models\TaxVatCategories;
use App\Models\Company;
use App\Models\CompanyDocumentAttachment;
use App\Models\CompanyPolicyMaster;
use App\Models\CurrencyMaster;
use App\Models\CustomerInvoiceDirect;
use App\Models\BudgetMaster;
use App\Models\CustomerInvoiceDirectDetail;
use App\Models\CustomerInvoiceItemDetails;
use App\Models\CustomerReceivePayment;
use App\Models\CustomerReceivePaymentDetail;
use App\Models\DebitNote;
use App\Models\DeliveryOrder;
use App\Models\DeliveryOrderDetail;
use App\Models\DirectReceiptDetail;
use App\Models\DocumentApproved;
use App\Models\DocumentAttachments;
use App\Models\DocumentMaster;
use App\Models\DocumentReferedHistory;
use App\Models\Employee;
use App\Models\EmployeesDepartment;
use App\Models\ErpAddress;
use App\Models\ErpItemLedger;
use App\Models\ErpProjectMaster;
use App\Models\FinanceItemCategoryMaster;
use App\Models\FinanceItemcategorySubAssigned;
use App\Models\GRVDetails;
use App\Models\GRVMaster;
use App\Models\ItemAssigned;
use App\Models\ItemIssueMaster;
use App\Models\Location;
use App\Models\MatchDocumentMaster;
use App\Models\MaterielRequest;
use App\Models\Months;
use App\Models\PaySupplierInvoiceDetail;
use App\Models\PaySupplierInvoiceMaster;
use App\Models\PoAddons;
use App\Models\PoAddonsRefferedBack;
use App\Models\CompanyFinanceYear;
use App\Models\PoAdvancePayment;
use App\Models\PoPaymentTerms;
use App\Models\PoPaymentTermsRefferedback;
use App\Models\ProcumentOrder;
use App\Models\ProcumentOrderDetail;
use App\Models\PurchaseOrderAdvPaymentRefferedback;
use App\Models\PurchaseOrderDetails;
use App\Models\PurchaseOrderDetailsRefferedHistory;
use App\Models\PurchaseOrderMasterRefferedHistory;
use App\Models\PurchaseRequest;
use App\Models\PurchaseReturnDetails;
use App\Models\QuotationDetails;
use App\Models\QuotationMaster;
use App\Models\SalesReturn;
use App\Models\SalesReturnDetail;
use App\Models\SecondaryCompany;
use App\Models\SegmentMaster;
use App\Models\SupplierAssigned;
use App\Models\SupplierCategoryICVMaster;
use App\Models\SupplierContactDetails;
use App\Models\SupplierCurrency;
use App\Models\SupplierMaster;
use App\Models\TenderMaster;
use App\Models\WorkOrderGenerationLog;
use App\Models\Year;
use App\Models\YesNoSelection;
use App\Models\YesNoSelectionForMinus;
use App\Models\PoCategory;
use App\Models\PaymentTermTemplateAssigned;
use App\Models\PaymentTermConfig;
use App\Models\PaymentTermTemplate;
use App\Repositories\ProcumentOrderRepository;
use App\Repositories\SegmentAllocatedItemRepository;
use App\Repositories\PoDetailExpectedDeliveryDateRepository;
use App\Repositories\TenderMasterRepository;
use App\Repositories\UserRepository;
use App\Repositories\SrmTenderPoRepository;
use App\Services\Currency\CurrencyService;
use App\Services\Excel\ExportReportToExcelService;
use App\Services\PrintTemplateService;
use App\Traits\AuditTrial;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use App\Models\ERPAssetTransfer;
use Illuminate\Support\Facades\Storage;
use App\Jobs\AddMultipleItems;
use App\helper\CancelDocument;
use App\Jobs\GeneralLedgerInsert;
use App\Models\GeneralLedger;
use App\helper\CreateExcel;
use App\helper\BudgetConsumptionService;
use App\Jobs\DocumentAttachments\PoSentToSupplierJob;
use App\Models\DocumentCodeMaster;
use App\Models\DocumentCodeTransaction;
use App\Models\SupplierBlock;
use App\Services\DocumentCodeConfigurationService;

/**
 * Class ProcumentOrderController
 * @package App\Http\Controllers\API
 */
class ProcumentOrderAPIController extends AppBaseController
{
    /** @var  ProcumentOrderRepository */
    private $procumentOrderRepository;
    private $userRepository;
    private $segmentAllocatedItemRepository;
    private $poDetailExpectedDeliveryDateRepository;
    private $printTemplateService;

    private $tenderPoRepository;
    private $documentCodeConfigurationService;

    private $tenderMasterRepository;

    public function __construct(DocumentCodeConfigurationService $documentCodeConfigurationService ,ProcumentOrderRepository $procumentOrderRepo, UserRepository $userRepo, SegmentAllocatedItemRepository $segmentAllocatedItemRepo,PoDetailExpectedDeliveryDateRepository $poDetailExpectedDeliveryDateRepo, PrintTemplateService $printTemplateService, SrmTenderPoRepository $tenderPoRepository, TenderMasterRepository $tenderMasterRepository)
    {
        $this->procumentOrderRepository = $procumentOrderRepo;
        $this->userRepository = $userRepo;
        $this->segmentAllocatedItemRepository = $segmentAllocatedItemRepo;
        $this->poDetailExpectedDeliveryDateRepository = $poDetailExpectedDeliveryDateRepo;
        $this->printTemplateService = $printTemplateService;
        $this->tenderPoRepository = $tenderPoRepository;
        $this->tenderMasterRepository = $tenderMasterRepository;
        $this->documentCodeConfigurationService = $documentCodeConfigurationService;
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
        DB::beginTransaction();
        try {
            $input = $request->all();

            $input = $this->convertArrayToValue($input);

            if (!isset($input['supplierID']) || (isset($input['supplierID']) && is_null($input['supplierID']))) {
                DB::rollBack();
                return $this->sendError('Please select a supplier', 500);
            }

            if (isset($input['preCheck']) && $input['preCheck']) {
                $company = Company::where('companySystemID', $input['companySystemID'])->first();
                if (!empty($company) && $company->vatRegisteredYN == 1 && !Helper::isLocalSupplier($input['supplierID'], $input['companySystemID'])) {   //  (isset($input['rcmActivated']) && $input['rcmActivated'])
                    DB::rollBack();
                    return $this->sendError('Do you want to activate Reverse Charge Mechanism for this PO', 500, array('type' => 'rcm_confirm'));
                }
            }

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
                    DB::rollBack();
                    return $this->sendError('WO Period From cannot be greater than WO Period To', 500);
                }
            }

            $poDate = now();

            $input['budgetYear'] = CompanyFinanceYear::budgetYearByDate($poDate, $input['companySystemID']);

            $input['createdPcID'] = gethostname();
            $input['createdUserID'] = $user->employee['empID'];
            $input['createdUserSystemID'] = $user->employee['employeeSystemID'];
            $input['departmentID'] = 'PROC';

            if ($input['documentSystemID'] == 5 && $input['poType_N'] == 5) {
                $lastSerial = ProcumentOrder::where('companySystemID', $input['companySystemID'])
                    ->where('documentSystemID', $input['documentSystemID'])
                    ->where('poType_N', 5)
                    ->orderBy('purchaseOrderID', 'desc')
                    ->lockForUpdate()
                    ->first();
            } else {
                $lastSerial = ProcumentOrder::where('companySystemID', $input['companySystemID'])
                    ->where('documentSystemID', $input['documentSystemID'])
                    ->orderBy('purchaseOrderID', 'desc')
                    ->lockForUpdate()
                    ->first();
            }

            $input['POOrderedDate'] = now();

            $lastSerialNumber = 1;
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
                        $input['vat_number'] = $address['vat_number'];
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

                    $input['WO_NoOfAutoGenerationTimes'] = abs((($year2 - $year1) * 12) + ($month2 - $month1) + 1);
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

            $documentCodeTransaction = DocumentCodeTransaction::where('document_system_id', $input['documentSystemID'])
                ->where('company_id', $input['companySystemID'])
                ->first();

            if ($documentCodeTransaction) {
                $transactionID = $documentCodeTransaction->id;
                $documentCodeMaster = DocumentCodeMaster::where('document_transaction_id', $transactionID)
                    ->where('company_id', $input['companySystemID'])
                    ->first();

                if ($documentCodeMaster) {
                    $documentCodeMasterID = $documentCodeMaster->id;
                    $purchaseOrderCode = $this->documentCodeConfigurationService->getDocumentCodeConfiguration($input['documentSystemID'],$input['companySystemID'],$input,$lastSerialNumber,$documentCodeMasterID,$input['serviceLine']);
                }
            }

            if($purchaseOrderCode && $purchaseOrderCode['status'] == true){
                $input['purchaseOrderCode'] = $purchaseOrderCode['documentCode'];
                $input['serialNumber'] = $purchaseOrderCode['docLastSerialNumber'];
            } else {
                if ($documentMaster) {
                    $poCode = ($company->CompanyID . '\\' . $documentMaster['documentID'] . str_pad($lastSerialNumber, 6, '0', STR_PAD_LEFT));
                    $input['purchaseOrderCode'] = $poCode;
                }
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
                $input['VATPercentage'] = 0; // $supplierAssignedDetai->vatPercentage;
            }

            $allocateItemToSegment = CompanyPolicyMaster::where('companyPolicyCategoryID', 57)
                ->where('companySystemID', $input['companySystemID'])
                ->first();

            if ($allocateItemToSegment && $allocateItemToSegment->isYesNO == 1) {
                $input['allocateItemToSegment'] = 1;
            }

            $procumentOrders = $this->procumentOrderRepository->create($input);

            if(isset($input["tenderUUID"])){
                $tender = TenderMaster::getTenderByUuid($input["tenderUUID"]);
                $po['po_id'] = $procumentOrders->purchaseOrderID;
                $po['tender_id'] = $tender->id;
                $po['company_id'] = $input["companySystemID"];
                $po['status'] = 1;
                $this->tenderPoRepository->create($po);
            }

            DB::commit();
            return $this->sendResponse($procumentOrders->toArray(), 'Procurement Order saved successfully');
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage(), 500);
        }
    }

    /**
     * Display the specified ProcumentOrder.
     * GET|HEAD /procumentOrders/{id}
     *
     * @param int $id
     *
     * @return Response
     */

    public function show($id)
    {
        /** @var ProcumentOrder $procumentOrder */
        $procumentOrder = $this->procumentOrderRepository->with(['created_by', 'confirmed_by', 'segment', 'supplier' => function ($query) {
            $query->selectRaw('CONCAT(primarySupplierCode," | ",supplierName) as supplierName,supplierCodeSystem, vatNumber');
        }, 'currency' => function ($query) {
            $query->selectRaw('CONCAT(CurrencyCode," | ",CurrencyName) as CurrencyName,currencyID,CurrencyCode');
        }, 'location'])->findWithoutFail($id);

        if (empty($procumentOrder)) {
            return $this->sendError('Procurement Order not found');
        }

        $employee = \Helper::getEmployeeInfo();
        $procumentOrder->isAmendAccess = 0;
        if (
            $procumentOrder->WO_amendYN == -1 && $procumentOrder->WO_amendRequestedByEmpID == $employee->empID
            && $procumentOrder->documentSystemID != 5 && $procumentOrder->poType_N != 6
        ) {
            $procumentOrder->isAmendAccess = 1;
        }

        $procumentOrder->isLocalSupplier = Helper::isLocalSupplier($procumentOrder->supplierID, $procumentOrder->companySystemID);

        $isExpectedDeliveryDateEnabled = CompanyPolicyMaster::where('companyPolicyCategoryID', 71)
            ->where('companySystemID', $procumentOrder->companySystemID)
            ->where('isYesNO', 1)
            ->exists();
        $procumentOrder->isExpectedDeliveryDateEnabled = $isExpectedDeliveryDateEnabled;
        $procumentOrder['poDiscountPercentageToTooltip'] = $procumentOrder->poDiscountPercentage;
        $procumentOrder->poDiscountPercentage = round($procumentOrder->poDiscountPercentage,2);

        return $this->sendResponse($procumentOrder->toArray(), 'Procurement Order retrieved successfully');
    }

    /**
     * Update the specified ProcumentOrder in storage.
     * PUT/PATCH /procumentOrders/{id}
     *
     * @param int $id
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

        $poDiscountPercenrtageToUpdate = 0;
        if( strlen((string) $input['poDiscountPercentage'] ) > strlen((string)$input['poDiscountPercentageToTooltip']))
        {
            $poDiscountPercenrtageToUpdate = $input['poDiscountPercentage'];
        }else {
            if(round($input['poDiscountPercentageToTooltip'],2) != $input['poDiscountPercentage'])
            {
                $poDiscountPercenrtageToUpdate = $input['poDiscountPercentage'];
            }else {
                $poDiscountPercenrtageToUpdate = $input['poDiscountPercentageToTooltip'];
            }
        }

        $input['poDiscountPercentage'] = $poDiscountPercenrtageToUpdate;

        $input = array_except($input, ['rcmAvailable', 'isVatEligible', 'isWoAmendAccess', 'created_by', 'confirmed_by', 'totalOrderAmount', 'segment', 'isAmendAccess', 'supplier', 'currency', 'isLocalSupplier', 'location','poDiscountPercentageToTooltip']);
        $input = $this->convertArrayToValue($input);


        if (isset($input['isSupplierBlocked'])) {
            $isSupplierBlocked = $input['isSupplierBlocked'];
            unset($input['isSupplierBlocked']);
        }

        if (isset($input['VATAmountPreview'])) {
            unset($input['VATAmountPreview']);
        }

        if (isset($input['totalSubOrderAmountPreview'])) {
            unset($input['totalSubOrderAmountPreview']);
        }

        if (isset($input['totalAddonAmountPreview'])) {
            unset($input['totalAddonAmountPreview']);
        }

        if (isset($input['totalOrderAmountPreview'])) {
            unset($input['totalOrderAmountPreview']);
        }

        if (isset($input['isExpectedDeliveryDateEnabled'])) {
            unset($input['isExpectedDeliveryDateEnabled']);
        }

        // po total vat
        $poMasterVATSum = PurchaseOrderDetails::select(DB::raw('COALESCE(SUM(VATAmount * noQty),0) as masterTotalVATSum'))
            ->where('purchaseOrderMasterID', $input['purchaseOrderID'])
            ->first();


        $procumentOrder = $this->procumentOrderRepository->findWithoutFail($id);
        //getting addon Total for PO
        $poAddonMasterSum = PoAddons::select(DB::raw('COALESCE(SUM(amount),0) as addonTotalSum'))
            ->where('poId', $input['purchaseOrderID'])
            ->first();
        //getting total sum of PO detail Amount
        $poMasterSum = PurchaseOrderDetails::select(DB::raw('COALESCE(SUM(netAmount),0) as masterTotalSum'))
            ->where('purchaseOrderMasterID', $input['purchaseOrderID'])
            ->first();
        $advancedPayment = PoPaymentTerms::where('poID',$id)->sum('comAmount');
        $supplierCurrencyDecimalPlace = \Helper::getCurrencyDecimalPlace($procumentOrder->supplierTransactionCurrencyID);
        $newlyUpdatedPoTotalAmountWithoutRound = $poMasterSum['masterTotalSum'] + $poAddonMasterSum['addonTotalSum']+ ($procumentOrder->rcmActivated ? 0 : $poMasterVATSum['masterTotalVATSum']);
        $newlyUpdatedPoTotalAmountWithoutRoundForComp = $poMasterSum['masterTotalSum'] + $poAddonMasterSum['addonTotalSum']+ ($procumentOrder->rcmActivated ? 0 : $poMasterVATSum['masterTotalVATSum']) - $input['poDiscountAmount'];
        // $newlyUpdatedPoTotalAmount = round($newlyUpdatedPoTotalAmountWithoutRound, $supplierCurrencyDecimalPlace);
        // $newlyUpdatedPoTotalAmount = bcdiv($newlyUpdatedPoTotalAmountWithoutRound,1,$supplierCurrencyDecimalPlace);
        $newlyUpdatedPoTotalAmount = floatval(sprintf("%.".$supplierCurrencyDecimalPlace."f", $newlyUpdatedPoTotalAmountWithoutRound));
        $newlyUpdatedPoTotalAmountCheck = floatval(sprintf("%.".$supplierCurrencyDecimalPlace."f", $newlyUpdatedPoTotalAmountWithoutRoundForComp));
        $advancedPaymentCheckAmount = floatval(sprintf("%.".$supplierCurrencyDecimalPlace."f", $advancedPayment));

        $this->recalculateTermsAndConditionsPercentage($input['purchaseOrderID'],$newlyUpdatedPoTotalAmountWithoutRoundForComp);

        $advancedPaymentPercentage = PoPaymentTerms::where('poID',$id)->sum('comPercentage');
        if(isset($input['isConfirm']) && $input['isConfirm']) {
            $epsilon = 0.00001;
            if(abs(100 - $advancedPaymentPercentage) > $epsilon) {
                return $this->sendError('Total of Payment terms amount is not equal to PO amount');
            }
        }


        if(isset($input['isConfirm'])) {
            unset($input['isConfirm']);
        }

        $procumentOrderUpdate = ProcumentOrder::where('purchaseOrderID', '=', $id)->first();

        if (isset($input['expectedDeliveryDate']) && $input['expectedDeliveryDate']) {
            $input['expectedDeliveryDate'] = new Carbon($input['expectedDeliveryDate']);
        }

        if (isset($input['WO_PeriodFrom']) && $input['WO_PeriodFrom']) {
            $input['WO_PeriodFrom'] = new Carbon($input['WO_PeriodFrom']);
        }

        if (isset($input['WO_PeriodTo']) && $input['WO_PeriodTo']) {
            $input['WO_PeriodTo'] = new Carbon($input['WO_PeriodTo']);
        }



        if (empty($procumentOrder)) {
            return $this->sendError('Procurement Order not found');
        }


        if ($input['documentSystemID'] == 5 && $input['poType_N'] == 5) {
            if ($input['WO_PeriodFrom'] > $input['WO_PeriodTo']) {
                return $this->sendError('WO Period From cannot be greater than WO Period To');
            }
        }

        $this->poDetailExpectedDeliveryDateRepository->checkAndUpdateExpectedDeliveryDate($id, $input['expectedDeliveryDate']);

        $oldPoTotalSupplierTransactionCurrency = $procumentOrder->poTotalSupplierTransactionCurrency;

        $employee = \Helper::getEmployeeInfo();
        $supplierCurrencyDecimalPlace = \Helper::getCurrencyDecimalPlace($procumentOrder->supplierTransactionCurrencyID);

        if ($procumentOrder->WO_amendYN == -1 && $isAmendAccess == 1 && $procumentOrder->WO_amendRequestedByEmpID != $employee->empID) {
            return $this->sendError('You cannot amend this order, this is already amended by ' . $procumentOrder->WO_amendRequestedByEmpID, 500);
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


        foreach ($input as $key => $value) {
            $procumentOrderUpdate->$key = $value;
        }

        $segment = SegmentMaster::where('serviceLineSystemID', $input['serviceLineSystemID'])->first();
        if ($segment) {
            $procumentOrderUpdate->serviceLine = $segment->ServiceLineCode;
        }


        $procumentOrderUpdate->modifiedPc = gethostname();
        $procumentOrderUpdate->modifiedUser = $user->employee['empID'];
        $procumentOrderUpdate->modifiedUserSystemID = $user->employee['employeeSystemID'];
        $procumentOrderUpdate->approval_remarks = $input['approval_remarks'];
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

            $currencyConversionDefaultMaster = \Helper::currencyConversion($input['companySystemID'], $input['supplierTransactionCurrencyID'], $supplierCurrency->currencyID, 0);

            if ($currencyConversionDefaultMaster) {
                $procumentOrderUpdate->supplierDefaultER = $currencyConversionDefaultMaster['transToDocER'];
            }
        }



        $poMasterSumRounded = round($poMasterSum['masterTotalSum'], $supplierCurrencyDecimalPlace);
        $poAddonMasterSumRounded = round($poAddonMasterSum['addonTotalSum'], $supplierCurrencyDecimalPlace);
        $poVATMasterSumRounded = round($poMasterVATSum['masterTotalVATSum'], $supplierCurrencyDecimalPlace);


        if ($procumentOrder->rcmActivated) {
            $poVATMasterSumRounded = 0;
        }

        if(isset($input['isConfirm'])) {
            unset($input['isConfirm']);
        }


        if ($input['poDiscountAmount'] > $newlyUpdatedPoTotalAmount) {
            return $this->sendError('Discount Amount should be less than order amount.', 500);
        }

        $poMasterSumDeducted = ($newlyUpdatedPoTotalAmount - $input['poDiscountAmount']);
        $poMasterSumDeductedNotRounded = ($poMasterSum['masterTotalSum'] + $poAddonMasterSum['addonTotalSum'] + $poMasterVATSum['masterTotalVATSum'] - $input['poDiscountAmount']);



        $input['poTotalSupplierTransactionCurrency'] = \Helper::roundValue($poMasterSumDeductedNotRounded);

        $currencyConversionMaster = \Helper::currencyConversion($input["companySystemID"], $input['supplierTransactionCurrencyID'], $input['supplierTransactionCurrencyID'], $poMasterSumDeductedNotRounded);

        $procumentOrderUpdate->poTotalComRptCurrency = \Helper::roundValue($currencyConversionMaster['reportingAmount']);
        $procumentOrderUpdate->poTotalLocalCurrency = \Helper::roundValue($currencyConversionMaster['localAmount']);
        $procumentOrderUpdate->poTotalSupplierTransactionCurrency = \Helper::roundValue($poMasterSumDeductedNotRounded);
        $procumentOrderUpdate->companyReportingER = \Helper::roundValue($currencyConversionMaster['trasToRptER']);
        $procumentOrderUpdate->localCurrencyER = \Helper::roundValue($currencyConversionMaster['trasToLocER']);


        // updating coloum
        if ($input['documentSystemID'] != 5 && $input['poType_N'] != 5) {
            $procumentOrderUpdate->WO_PeriodFrom = null;
            $procumentOrderUpdate->WO_PeriodTo = null;
        }

        //calculate total months in WO type
        if ($input['documentSystemID'] == 5) {
            if (isset($input['WO_PeriodFrom'])) {
                $ts1 = strtotime($input['WO_PeriodFrom']);
                $ts2 = strtotime($input['WO_PeriodTo']);

                $year1 = date('Y', $ts1);
                $year2 = date('Y', $ts2);

                $month1 = date('n', $ts1);
                $month2 = date('n', $ts2);

                $procumentOrderUpdate->WO_NoOfAutoGenerationTimes = abs((($year2 - $year1) * 12) + ($month2 - $month1) + 1);
            }
        }

        // calculating total Supplier Default currency

        $currencyConversionMaster = \Helper::currencyConversion($input["companySystemID"], $input['supplierTransactionCurrencyID'], $supplierCurrency->currencyID, $poMasterSumDeducted);

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
                //$procumentOrderUpdate->VATPercentage = $supplierAssignedDetai->vatPercentage;
            }
        }

        if ($procumentOrder->companySystemID != $input['companySystemID']) {

            $company = Company::where('companySystemID', $input['companySystemID'])->first();
            if ($company) {
                $procumentOrderUpdate->vatRegisteredYN = $company->vatRegisteredYN;
            }
        }
        //updating PO Master
        $updateDetailDiscount = PurchaseOrderDetails::where('purchaseOrderMasterID', $purchaseOrderID)
            ->get();

        // calculate total Tax for item if
        //$input['supplierVATEligible'] == 1 && $input['vatRegisteredYN'] == 0
        if (TaxService::checkPOVATEligible($input['supplierVATEligible'], $input['vatRegisteredYN'])) {

            if (!empty($updateDetailDiscount)) {
                foreach ($updateDetailDiscount as $itemDiscont) {
                    $calculateItemDiscount = 0;
                    if ($input['poDiscountAmount'] > 0 && $poMasterSumRounded > 0 && $itemDiscont['noQty']) {
                        $calculateItemDiscount = ((($itemDiscont['netAmount'] - (($itemDiscont['netAmount'] / $poMasterSumRounded) * $input['poDiscountAmount']))) / $itemDiscont['noQty']);
                    } else {
                        $calculateItemDiscount = $itemDiscont['unitCost'] - $itemDiscont['discountAmount'];
                    }

                    if (!$input['vatRegisteredYN']) {
                        $calculateItemDiscount = $calculateItemDiscount + $itemDiscont['VATAmount'];
                    } else {
                        $checkVATCategory = TaxVatCategories::with(['type'])->find($itemDiscont['vatSubCategoryID']);
                        if ($checkVATCategory) {
                            if (isset($checkVATCategory->type->id) && $checkVATCategory->type->id == 1 && $itemDiscont['exempt_vat_portion'] > 0 && $itemDiscont['VATAmount'] > 0) {
                                $exemptVAT = $itemDiscont['VATAmount'] * ($itemDiscont['exempt_vat_portion'] / 100);

                                $calculateItemDiscount = $calculateItemDiscount + $exemptVAT;
                            } else if (isset($checkVATCategory->type->id) && $checkVATCategory->type->id == 3) {
                                $calculateItemDiscount = $calculateItemDiscount + $itemDiscont['VATAmount'];
                            }
                        }
                    }

                    // $calculateItemTax = (($itemDiscont['VATPercentage'] / 100) * $calculateItemDiscount) + $calculateItemDiscount;
                    $vatLineAmount = $itemDiscont['VATAmount']; //($calculateItemTax - $calculateItemDiscount);

                    $currencyConversion = \Helper::currencyConversion($itemDiscont['companySystemID'], $input['supplierTransactionCurrencyID'], $input['supplierTransactionCurrencyID'], $calculateItemDiscount);

                    $currencyConversionForLineAmount = \Helper::currencyConversion($itemDiscont['companySystemID'], $input['supplierTransactionCurrencyID'], $input['supplierTransactionCurrencyID'], $vatLineAmount);

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
                            'VATPercentage' => $itemDiscont['VATPercentage'],
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

                    $currencyConversion = \Helper::currencyConversion(
                        $itemDiscont['companySystemID'],
                        $input['supplierTransactionCurrencyID'],
                        $input['supplierTransactionCurrencyID'],
                        $calculateItemDiscount
                    );

                    $currencyConversionLineDefault = \Helper::currencyConversion($input['companySystemID'], $input['supplierTransactionCurrencyID'], $input['supplierDefaultCurrencyID'], $calculateItemDiscount);

                    $vatLineAmount = 0;
                    $vatAmountLocal = 0;
                    $vatAmountRpt = 0;
                    if (isset($input['rcmActivated']) && $input['rcmActivated']) {
                        $vatLineAmount = $itemDiscont['VATAmount'];
                        $currencyConversionForLineAmount = \Helper::currencyConversion($itemDiscont['companySystemID'], $input['supplierTransactionCurrencyID'], $input['supplierTransactionCurrencyID'], $vatLineAmount);
                        $vatLineAmount =  \Helper::roundValue($vatLineAmount);
                        $vatAmountLocal = \Helper::roundValue($currencyConversionForLineAmount['localAmount']);
                        $vatAmountRpt = \Helper::roundValue($currencyConversionForLineAmount['reportingAmount']);
                    }


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
                            //'VATPercentage' => 0,
                            'VATAmount' => $vatLineAmount,
                            'VATAmountLocal' => $vatAmountLocal,
                            'VATAmountRpt' => $vatAmountRpt
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

                            $calculateAddonLineAmount = \Helper::roundFloatValue((($poAddonMasterSumRounded / $poMasterSumRounded) * $AddonDeta['netAmount']) / $AddonDeta['noQty']);

                            $currencyConversionForLineAmountAddon = \Helper::currencyConversion($input['companySystemID'], $input['supplierTransactionCurrencyID'], $input['supplierTransactionCurrencyID'], $calculateAddonLineAmount);

                            $currencyConversionLineAmountAddonDefault = \Helper::currencyConversion($input['companySystemID'], $input['supplierTransactionCurrencyID'], $input['supplierDefaultCurrencyID'], $calculateAddonLineAmount);

                            $updatePoDetailAddonDetail = PurchaseOrderDetails::find($AddonDeta['purchaseOrderDetailsID']);


                            $GRVcostPerUnitLocalCurAddon = ($AddonDeta['GRVcostPerUnitLocalCur'] + \Helper::roundValue($currencyConversionForLineAmountAddon['localAmount']));
                            $updatePoDetailAddonDetail->GRVcostPerUnitLocalCur = \Helper::roundValue($GRVcostPerUnitLocalCurAddon);

                            $GRVcostPerUnitSupDefaultCurAddon = ($AddonDeta['GRVcostPerUnitSupDefaultCur'] + $currencyConversionLineAmountAddonDefault['documentAmount']);
                            $updatePoDetailAddonDetail->GRVcostPerUnitSupDefaultCur = \Helper::roundValue($GRVcostPerUnitSupDefaultCurAddon);

                            $GRVcostPerUnitSupTransCurAddon = ($AddonDeta['GRVcostPerUnitSupTransCur'] + $calculateAddonLineAmount);
                            $updatePoDetailAddonDetail->GRVcostPerUnitSupTransCur = \Helper::roundValue($GRVcostPerUnitSupTransCurAddon);

                            $GRVcostPerUnitComRptCurAddon = ($AddonDeta['GRVcostPerUnitComRptCur'] + \Helper::roundValue($currencyConversionForLineAmountAddon['reportingAmount']));
                            $updatePoDetailAddonDetail->GRVcostPerUnitComRptCur = \Helper::roundValue($GRVcostPerUnitComRptCurAddon);

                            $purchaseRetcostPerUniSupDefaultCurAddon = ($AddonDeta['purchaseRetcostPerUniSupDefaultCur'] + $currencyConversionLineAmountAddonDefault['documentAmount']);
                            $updatePoDetailAddonDetail->purchaseRetcostPerUniSupDefaultCur = \Helper::roundValue($purchaseRetcostPerUniSupDefaultCurAddon);

                            $purchaseRetcostPerUnitLocalCurAddon = ($AddonDeta['purchaseRetcostPerUnitLocalCur'] + \Helper::roundValue($currencyConversionForLineAmountAddon['localAmount']));
                            $updatePoDetailAddonDetail->purchaseRetcostPerUnitLocalCur = \Helper::roundValue($purchaseRetcostPerUnitLocalCurAddon);

                            $purchaseRetcostPerUnitTranCurAddon = ($AddonDeta['purchaseRetcostPerUnitTranCur'] + $calculateAddonLineAmount);
                            $updatePoDetailAddonDetail->purchaseRetcostPerUnitTranCur = \Helper::roundValue($purchaseRetcostPerUnitTranCurAddon);

                            $purchaseRetcostPerUnitRptCur = ($AddonDeta['purchaseRetcostPerUnitRptCur'] + \Helper::roundValue($currencyConversionForLineAmountAddon['reportingAmount']));
                            $updatePoDetailAddonDetail->purchaseRetcostPerUnitRptCur = \Helper::roundValue($purchaseRetcostPerUnitRptCur);

                            $updatePoDetailAddonDetail->addonDistCost = \Helper::roundValue($calculateAddonLineAmount);
                            $updatePoDetailAddonDetail->addonPurchaseReturnCost = \Helper::roundValue($calculateAddonLineAmount);
                            $updatePoDetailAddonDetail->save();
                        }
                    }
                }
            }
        }


        //calculate tax amount according to the percantage for tax update

        //if($purchaseOrder->VATPercentage > 0 && $purchaseOrder->supplierVATEligible == 1 && $purchaseOrder->vatRegisteredYN == 0){
        //$input['VATPercentage'] > 0 && $input['supplierVATEligible'] == 1
        if (TaxService::checkPOVATEligible($input['supplierVATEligible'], $input['vatRegisteredYN']) || (isset($input['rcmActivated']) && $input['rcmActivated'])) {
            TaxService::updatePOVAT($id);
        } else {
            $procumentOrderUpdate->VATAmount = 0;
            $procumentOrderUpdate->VATAmountLocal = 0;
            $procumentOrderUpdate->VATAmountRpt = 0;
        }

        if (($procumentOrder->poConfirmedYN == 0 && $input['poConfirmedYN'] == 1) || $isAmendAccess == 1) {

            if((isset($isSupplierBlocked) && $isSupplierBlocked) && ($procumentOrderUpdate->poTypeID == 1))
            {

                $block_date = Carbon::parse(now())->format('Y-m-d');

                $validatorResult = \Helper::checkBlockSuppliers($block_date,$input['supplierID']);

                if (!$validatorResult['success']) {
                    return $this->sendError('The selected supplier has been blocked. Are you sure you want to proceed ?', 500,['type' => 'blockSupplier']);

                }
            }

            $allowFinanceCategory = CompanyPolicyMaster::where('companyPolicyCategoryID', 20)
                ->where('companySystemID', $procumentOrder->companySystemID)
                ->first();


            if ($allowFinanceCategory) {
                $policy = $allowFinanceCategory->isYesNO;
                //checking if item category is same or not
                $pRDetailExistSameItem = ProcumentOrderDetail::select(DB::raw('DISTINCT(itemFinanceCategoryID) as itemFinanceCategoryID'))
                    ->where('purchaseOrderMasterID', $input['purchaseOrderID'])
                    ->get();
                if ($policy == 0) {
                    if ($procumentOrder->financeCategory == null || $procumentOrder->financeCategory == 0) {
                        return $this->sendError('Category is not found.', 500);
                    }

                    if (sizeof($pRDetailExistSameItem) > 1) {
                        return $this->sendError('You cannot add different category item', 500);
                    }
                } else {
                    if (sizeof($pRDetailExistSameItem) == 1) {
                        $updateFinanceCategory = $pRDetailExistSameItem[0]['itemFinanceCategoryID'];
                    } else {
                        $updateFinanceCategory = null;
                    }

                    ProcumentOrder::where('purchaseOrderID', $procumentOrder->purchaseOrderID)
                        ->update(['financeCategory' => $updateFinanceCategory]);
                }
            }



            $poDetailExist = PurchaseOrderDetails::select(DB::raw('purchaseOrderDetailsID'))
                ->where('purchaseOrderMasterID', $input['purchaseOrderID'])
                ->first();

            if (empty($poDetailExist)) {
                return $this->sendError('Order cannot be confirmed without any details');
            }

            $poDetails = PurchaseOrderDetails::where('purchaseOrderMasterID', $input['purchaseOrderID'])
                ->get();

            $accountValidationArray = [];
            foreach ($poDetails as $key => $value) {
                if (is_null($value->itemFinanceCategoryID)) {
                    $accountValidationArray[3][] = "Finance category of " . $value->itemPrimaryCode . " not found";
                } else {
                    switch ($value->itemFinanceCategoryID) {
                        case 1:
                            if (is_null($value->financeGLcodebBSSystemID) || is_null($value->financeGLcodePLSystemID) || $value->financeGLcodebBSSystemID == 0 || $value->financeGLcodePLSystemID == 0) {

                                $accountValidationArray[1][] = $value->itemPrimaryCode;
                            }
                            break;
                        case 2:
                        case 3:
                        case 4:
                            if ((is_null($value->financeGLcodebBSSystemID) || $value->financeGLcodebBSSystemID == 0) && (is_null($value->financeGLcodePLSystemID) || $value->financeGLcodePLSystemID == 0)) {
                                $accountValidationArray[1][] = "Finance category accounts are not updated correctly. Please check the finance category configurations for the item " . $value->itemPrimaryCode;
                            }

                            if ((is_null($value->financeGLcodebBSSystemID) || $value->financeGLcodebBSSystemID == 0) && !is_null($value->financeGLcodePLSystemID) && $value->financeGLcodePLSystemID != 0 && $value->includePLForGRVYN != -1) {
                                $accountValidationArray[2][] = $value->itemPrimaryCode;
                            }
                            break;

                        default:
                            # code...
                            break;
                    }
                }
            }


            if (!empty($accountValidationArray)) {
                $accountValidationErrrArray = [];
                if (isset($accountValidationArray[1])) {
                    $itemsA = implode(", ", $accountValidationArray[1]);
                    $accountValidationErrrArray[] = "Finance category accounts are not updated correctly. Please check the finance category configurations for the item(s) " . $itemsA;
                }

                if (isset($accountValidationArray[2])) {
                    $itemsB = implode(", ", $accountValidationArray[2]);
                    $accountValidationErrrArray[] = "Expense account configuration is not done correctly. Activate includePLforGRVYN for the item(s) " . $itemsB;
                }

                if (isset($accountValidationArray[3])) {
                    $itemsC = implode(", ", $accountValidationArray[3]);
                    $accountValidationErrrArray[] = $itemsC;
                }
                return $this->sendError($accountValidationErrrArray, 420);
            }


            $checkQuantity = PurchaseOrderDetails::where('purchaseOrderMasterID', $id)
                ->where('noQty', '<', 0.1)
                ->count();


            $checkAltUnit = PurchaseOrderDetails::where('purchaseOrderMasterID', $id)->where('altUnit','!=',0)->where('altUnitValue',0)->count();

            $allAltUOM = CompanyPolicyMaster::where('companyPolicyCategoryID', 60)
                ->where('companySystemID',  $procumentOrder->companySystemID)
                ->first();

            if ($checkAltUnit > 0 && $allAltUOM->isYesNO) {
                return $this->sendError('Every Alternative UOM should have Alternative UOM Qty', 500);
            }

            $validateAllocatedQuantity = $this->segmentAllocatedItemRepository->validatePurchaseRequestAllocatedQuantity($id);
            if (!$validateAllocatedQuantity['status']) {
                return $this->sendError($validateAllocatedQuantity['message'], 500);
            }

            $validateAllocatedEDD = $this->poDetailExpectedDeliveryDateRepository->validateAllocatedExpectedDeliveryDate($id);
            if (!$validateAllocatedEDD['status']) {
                return $this->sendError($validateAllocatedEDD['message'], 500);
            }

            if ($checkQuantity > 0) {
                return $this->sendError('Every item should have at least one minimum qty requested', 500);
            }

            //check unit cost should be greater than zero
            $checkQuantity = PurchaseOrderDetails::where('purchaseOrderMasterID', $id)
                ->where('unitCost', '<=', 0)
                ->count();

            //check PO and PR service line

            $details = PurchaseOrderDetails::where('purchaseOrderMasterID', $id)
                ->get();

            foreach ($details as $detail) {
                $PRMaster = PurchaseRequest::find($detail['purchaseRequestID']);
                if ($PRMaster && ($procumentOrder->serviceLineSystemID != $PRMaster->serviceLineSystemID)) {
                    return $this->sendError("Added Request department is different from order");
                }
            }

            if ($checkQuantity > 0) {
                return $this->sendError('Every item unit cost should be greater than zero ', 500);
            }

            //checking atleast one po payment terms should exist
            $PoPaymentTerms = PoPaymentTerms::where('poID', $input['purchaseOrderID'])
                ->first();

            if (empty($PoPaymentTerms)) {
                return $this->sendError('PO should have at least one payment term');
            }

            // checking payment term amount value 0

            $checkPoPaymentTermsAmount = PoPaymentTerms::where('poID', $id)
                ->where('comAmount', '<=', 0)
                ->count();

            if ($checkPoPaymentTermsAmount > 0) {
                // return $this->sendError('You cannot confirm payment term with 0 amount', 500);
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
            $paymentTotalSum = PoPaymentTerms::select(DB::raw('IFNULL(SUM(comAmount),0) as paymentTotalSum, IFNULL(SUM(comPercentage),0) as paymentTotalPercentage'))
                ->where('poID', $input['purchaseOrderID'])
                ->first();

            $paymentTotalSumComp = floatval(sprintf("%.".$supplierCurrencyDecimalPlace."f", $paymentTotalSum['paymentTotalSum']));
            if ($paymentTotalSumComp > 0) {
                if (abs(($poMasterSumDeducted - $paymentTotalSumComp) / $paymentTotalSumComp) < 0.00001) {
                } else {
                    return $this->sendError('Payment terms total is not matching with the PO total');
                }
            }


            $poAdvancePaymentType = PoPaymentTerms::where("poID", $input['purchaseOrderID'])
                ->get();

            $detailSum = PurchaseOrderDetails::select(DB::raw('sum(netAmount) as total'))
                ->where('purchaseOrderMasterID', $input['purchaseOrderID'])
                ->first();

            if (!empty($poAdvancePaymentType)) {
                foreach ($poAdvancePaymentType as $payment) {
                    if($payment['comAmount']) {
                        $paymentPercentageAmount = floatval(sprintf("%.".$supplierCurrencyDecimalPlace."f", $payment['comAmount']));
                    }else {
                        $paymentPercentageAmount = round(($payment['comPercentage'] / 100) * (($newlyUpdatedPoTotalAmountWithoutRound - $input['poDiscountAmount'])), $supplierCurrencyDecimalPlace);
                    }
                    // $payAdCompAmount = round($payment['comAmount'], $supplierCurrencyDecimalPlace);
                    $payAdCompAmount = floatval(sprintf("%.".$supplierCurrencyDecimalPlace."f", $payment['comAmount']));

                    if ($paymentPercentageAmount > 0) {
                        if (abs(($payAdCompAmount - $paymentPercentageAmount) / $paymentPercentageAmount) < 0.00001) {
                        } else {
                            return $this->sendError('Payment term calculation is mismatched');
                        }
                    }
                }
            }

            unset($input['poConfirmedYN']);
            unset($input['poConfirmedByEmpSystemID']);
            unset($input['poConfirmedByEmpID']);
            unset($input['poConfirmedByName']);
            unset($input['poConfirmedDate']);

            $validateAllocatedQuantity = $this->segmentAllocatedItemRepository->validatePurchaseOrderAllocatedQuantity($id);
            if (!$validateAllocatedQuantity['status']) {
                return $this->sendError($validateAllocatedQuantity['message'], 500);
            }


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
                    $emails[] = array(
                        'empSystemID' => $procumentOrder->poConfirmedByEmpSystemID,
                        'companySystemID' => $procumentOrder->companySystemID,
                        'docSystemID' => $procumentOrder->documentSystemID,
                        'alertMessage' => $subject,
                        'emailAlertMessage' => $body,
                        'docSystemCode' => $procumentOrder->purchaseOrderID
                    );
                }

                $documentApproval = DocumentApproved::where('companySystemID', $procumentOrder->companySystemID)
                    ->where('documentSystemCode', $procumentOrder->purchaseOrderID)
                    ->where('documentSystemID', $procumentOrder->documentSystemID)
                    ->get();

                foreach ($documentApproval as $da) {
                    if ($da->approvedYN == -1) {
                        $emails[] = array(
                            'empSystemID' => $da->employeeSystemID,
                            'companySystemID' => $procumentOrder->companySystemID,
                            'docSystemID' => $procumentOrder->documentSystemID,
                            'alertMessage' => $subject,
                            'emailAlertMessage' => $body,
                            'docSystemCode' => $procumentOrder->purchaseOrderID
                        );
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
                    $poDetail = \DB::select('SELECT SUM(erp_purchaseorderdetails.GRVcostPerUnitLocalCur*erp_purchaseorderdetails.noQty) as GRVcostPerUnitLocalCur,SUM(erp_purchaseorderdetails.GRVcostPerUnitComRptCur*erp_purchaseorderdetails.noQty) as GRVcostPerUnitComRptCur,erp_purchaseorderdetails.companyReportingCurrencyID,erp_purchaseorderdetails.financeGLcodePLSystemID,erp_purchaseorderdetails.financeGLcodePL,erp_purchaseorderdetails.companyID,erp_purchaseorderdetails.companySystemID,erp_purchaseorderdetails.serviceLineSystemID,erp_purchaseorderdetails.serviceLineCode,erp_purchaseordermaster.budgetYear,erp_purchaseorderdetails.localCurrencyID FROM erp_purchaseorderdetails INNER JOIN erp_purchaseordermaster ON erp_purchaseordermaster.purchaseOrderID = erp_purchaseorderdetails.purchaseOrderMasterID  WHERE erp_purchaseorderdetails.purchaseOrderMasterID = ' . $procumentOrder->purchaseOrderID . ' AND erp_purchaseordermaster.poType_N IN(1,2,3,4,5) GROUP BY erp_purchaseorderdetails.companySystemID,erp_purchaseorderdetails.serviceLineSystemID,erp_purchaseorderdetails.budgetYear');
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
                    $poDetail = \DB::select('SELECT SUM(erp_purchaseorderdetails.GRVcostPerUnitLocalCur*erp_purchaseorderdetails.noQty) as GRVcostPerUnitLocalCur,SUM(erp_purchaseorderdetails.GRVcostPerUnitComRptCur*erp_purchaseorderdetails.noQty) as GRVcostPerUnitComRptCur,erp_purchaseorderdetails.companyReportingCurrencyID,erp_purchaseorderdetails.financeGLcodePLSystemID,erp_purchaseorderdetails.financeGLcodePL,erp_purchaseorderdetails.companyID,erp_purchaseorderdetails.companySystemID,erp_purchaseorderdetails.serviceLineSystemID,erp_purchaseorderdetails.serviceLineCode,erp_purchaseordermaster.budgetYear,erp_purchaseorderdetails.localCurrencyID FROM erp_purchaseorderdetails INNER JOIN erp_purchaseordermaster ON erp_purchaseordermaster.purchaseOrderID = erp_purchaseorderdetails.purchaseOrderMasterID  WHERE erp_purchaseorderdetails.purchaseOrderMasterID = ' . $procumentOrder->purchaseOrderID . ' AND erp_purchaseordermaster.poType_N IN(1,2,3,4,5) GROUP BY erp_purchaseorderdetails.companySystemID,erp_purchaseorderdetails.serviceLineSystemID,erp_purchaseorderdetails.financeGLcodePLSystemID,erp_purchaseorderdetails.budgetYear');
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
            } // closing budget consume if condition

        } // closing amend if condition

        TaxService::updatePOVAT($id);

        return $this->sendReponseWithDetails($procumentOrder->toArray(), 'Procurement Order updated successfully',1,$confirm['data'] ?? null);
    }

    /**
     * Remove the specified ProcumentOrder from storage.
     * DELETE /procumentOrders/{id}
     *
     * @param int $id
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

    private function recalculateTermsAndConditionsPercentage($purchaseOrderID,$netAmount)
    {

        if(empty($purchaseOrderID))
            return $this->sendError("Procument Order not found");

        $procumentOrder = ProcumentOrder::where('purchaseOrderID',$purchaseOrderID)->with('paymentTerms_by')->first();

        $paymentTerms = $procumentOrder->paymentTerms_by;
        $totalPercentage = 0;
        foreach ($paymentTerms as $paymentTerm)
        {
            if($paymentTerm->comAmount > 0)
            {
                $paymentTerm->comPercentage = ($netAmount > 0) ? ($paymentTerm->comAmount) / ($netAmount) * 100 : 0;
                $totalPercentage += $paymentTerm->comPercentage;

                if((float) sprintf("%.7f", $totalPercentage) > 100)
                {
                    $paymentTerm->delete();
                }else {
                    $paymentTerm->save();
                }

            }
        }
    }
    public function getProcumentOrderByDocumentType(Request $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input, array('serviceLineSystemID', 'poCancelledYN', 'poConfirmedYN', 'approved', 'grvRecieved', 'month', 'year', 'invoicedBooked', 'supplierID', 'sentToSupplier', 'logisticsAvailable', 'financeCategory', 'poTypeID'));
        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $supplierID = $request['supplierID'];
        $supplierID = (array)$supplierID;
        $supplierID = collect($supplierID)->pluck('id');

        $serviceLineSystemID = $request['serviceLineSystemID'];
        $serviceLineSystemID = (array)$serviceLineSystemID;
        $serviceLineSystemID = collect($serviceLineSystemID)->pluck('id');

        $procumentOrders = ProcumentOrder::where('documentSystemID', $input['documentId']);
        if ($input['poType_N'] != 1) {
            $procumentOrders->where('poType_N', $input['poType_N']);
        }
        $procumentOrders->with(['created_by' => function ($query) {
            //$query->select(['empName']);
        }, 'category' => function ($query) {
        }, 'location' => function ($query) {
        }, 'supplier' => function ($query) {
        }, 'currency' => function ($query) {
        }, 'fcategory' => function ($query) {
        }, 'segment' => function ($query) {
        }]);

        if (array_key_exists('companyId', $input)) {
            if ($input['companyId'] && !is_null($input['companyId'])) {
                $procumentOrders->where('companySystemID', $input['companyId']);
            }
        }

        if (array_key_exists('serviceLineSystemID', $input)) {
            if ($input['serviceLineSystemID'] && !is_null($input['serviceLineSystemID'])) {
                $procumentOrders->whereIn('serviceLineSystemID', $serviceLineSystemID);
            }
        }

        if (array_key_exists('workOrderGenerateID', $input)) {
            if (isset($input['workOrderGenerateID']) && !is_null($input['workOrderGenerateID'])) {
                $procumentOrders->where('workOrderGenerateID', $input['workOrderGenerateID']);
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
                $procumentOrders->whereIn('supplierID', $supplierID);
            }
        }

        if (array_key_exists('financeCategory', $input)) {
            if ($input['financeCategory'] && !is_null($input['financeCategory'])) {
                $procumentOrders->where('financeCategory', $input['financeCategory']);
            }
        }

        if (array_key_exists('poTypeID', $input)) {
            if ($input['poTypeID'] && !is_null($input['poTypeID'])) {
                $procumentOrders->where('poTypeID', $input['poTypeID']);
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
            [
                'erp_purchaseordermaster.purchaseOrderID',
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
                'erp_purchaseordermaster.supplierPrimaryCode',
                'erp_purchaseordermaster.expectedDeliveryDate',
                'erp_purchaseordermaster.referenceNumber',
                'erp_purchaseordermaster.supplierTransactionCurrencyID',
                'erp_purchaseordermaster.poTotalSupplierTransactionCurrency',
                'erp_purchaseordermaster.financeCategory',
                'erp_purchaseordermaster.grvRecieved',
                'erp_purchaseordermaster.invoicedBooked',
                'erp_purchaseordermaster.poTypeID',
                'erp_purchaseordermaster.rcmActivated',
                'erp_purchaseordermaster.sentToSupplier',
                'erp_purchaseordermaster.categoryID',
            ]
        );

        $search = $request->input('search.value');
        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $procumentOrders = $procumentOrders->where(function ($query) use ($search) {
                $query->where('purchaseOrderCode', 'LIKE', "%{$search}%")
                    ->orWhere('narration', 'LIKE', "%{$search}%")
                    ->orWhere('referenceNumber', 'LIKE', "%{$search}%")
                    ->orWhere('supplierPrimaryCode', 'LIKE', "%{$search}%")
                    ->orWhere('supplierName', 'LIKE', "%{$search}%");
            });
        }


        $policy = 0;
        if (isset($input['companyId'])) {
            $historyPolicy = CompanyPolicyMaster::where('companyPolicyCategoryID', 29)
                ->where('companySystemID', $input['companyId'])->first();


            if (!empty($historyPolicy)) {
                $policy = $historyPolicy->isYesNO;
            }
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

        $po_category = PoCategory::where('isActive',true)->get();

        $po_category_default = PoCategory::where('isActive',true)->where('isDefault',true)->pluck('id');

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

        $locations = Location::where('is_deleted',0)->get();

        $financialYears = array(
            array('value' => intval(date("Y")), 'label' => date("Y")),
            array('value' => intval(date("Y", strtotime("-1 year"))), 'label' => date("Y", strtotime("-1 year")))
        );

        $checkBudget = CompanyPolicyMaster::where('companyPolicyCategoryID', 17)
            ->where('companySystemID', $companyId)
            ->first();

        $allowFinanceCategory = CompanyPolicyMaster::where('companyPolicyCategoryID', 20)
            ->where('companySystemID', $companyId)
            ->first();

        $allowPRinPO = CompanyPolicyMaster::where('companyPolicyCategoryID', 29)
            ->where('companySystemID', $companyId)
            ->first();

        $allowItemToType = CompanyPolicyMaster::where('companyPolicyCategoryID', 53)
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

        $conditions = array('checkBudget' => 0, 'allowFinanceCategory' => 0, 'detailExist' => 0, 'pullPRPolicy' => 0, 'allowItemToType' => 0);

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

        if ($allowItemToType) {
            $conditions['allowItemToType'] = $allowItemToType->isYesNO;
        }

        if (!empty($purchaseOrderID)) {
            $checkDetailExist = PurchaseOrderDetails::where('purchaseOrderMasterID', $purchaseOrderID)
                ->where('companySystemID', $companyId)
                ->first();

            if (!empty($checkDetailExist)) {
                $conditions['detailExist'] = 1;
            }
        }

        $icvCategories = SupplierCategoryICVMaster::all();

        $hasPolicy = false;
        $hasEEOSSPolicy = false;
        if ($purchaseOrderID) {
            $purchaseOrder = ProcumentOrder::find($purchaseOrderID);
            $sup = SupplierMaster::find($purchaseOrder->supplierID);
            if ($sup) {
                $hasPolicy = CompanyPolicyMaster::where('companySystemID', $sup->primaryCompanySystemID)
                    ->where('companyPolicyCategoryID', 38)
                    ->where('isYesNO', 1)
                    ->exists();
            }

            $supAssigned = SupplierAssigned::where('supplierCodeSytem', $purchaseOrder->supplierID)
                ->where('companySystemID', $companyId)
                ->where('isActive', 1)
                ->where('isAssigned', -1)
                ->first();
            if (!empty($supAssigned) && $supAssigned->isMarkupPercentage) {
                $hasEEOSSPolicy = CompanyPolicyMaster::where('companySystemID', $companyId)
                    ->where('companyPolicyCategoryID', 41)
                    ->where('isYesNO', 1)
                    ->exists();
            }
        }


        $isProject_base = CompanyPolicyMaster::where('companyPolicyCategoryID', 56)
            ->where('companySystemID', $companyId)
            ->where('isYesNO', 1)
            ->exists();
        $projects = [];
        if ($isProject_base) {
            $projects = ErpProjectMaster::where('companySystemID', $companyId)->get();
        }

        $contractEnablePolicy = Helper::checkPolicy($companyId, 93);

        $output = array(
            'segments' => $segments,
            'category' => $po_category,
            'category_default' => $po_category_default,
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
            'poAddonCategoryDrop' => $poAddonCategoryDrop,
            'icvCategories' => $icvCategories,
            'isSupplierCatalogPolicy' => $hasPolicy,
            'isEEOSSPolicy' => $hasEEOSSPolicy,
            'isProjectBase' => $isProject_base,
            'projects' => $projects,
            'contractEnablePolicy' => $contractEnablePolicy
        );

        return $this->sendResponse($output, 'Record retrieved successfully');
    }


    public function getProjectsBySegment(Request $request)
    {

        $serviceLineSystemID = $request['serviceLineSystemID'];

        $projects = ErpProjectMaster::where('serviceLineSystemID', $serviceLineSystemID)->get();

        return $this->sendResponse($projects, 'Segments Projects retrieved successfully');
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

        $items = ItemAssigned::where('companySystemID', $companyId)->where('isActive', 1)->where('isAssigned', -1)
            ->whereHas('item_category_type', function ($query) {
                $query->whereIn('categoryTypeID', ItemCategoryTypeMaster::purchaseItems());
            });


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
        $poBasicData = ProcumentOrder::find($request->purchaseOrderID);

        $createdDateTime = ($poBasicData) ? Carbon::parse($poBasicData->createdDateTime) : null;

        $output = ProcumentOrder::where('purchaseOrderID', $request->purchaseOrderID)->with(['sold_to','segment', 'created_by',
            'detail' => function ($query) {
                $query->with(['project','unit','altUom','item'=>function($query1){
                    $query1->select('itemCodeSystem','itemDescription')->with('specification');
                }]);
            }, 'supplier' => function ($query) {
                $query->select('vatNumber', 'supplierCodeSystem');
            }, 'approved' => function ($query) {
                $query->with(['employee'=>function($query2){
                    $query2->with(['hr_emp'=>function($query3){
                        $query3->with(['designation']);
                    }]);
                }]);
                $query->where('rejectedYN', 0);
                $query->whereIN('documentSystemID', [2, 5, 52]);
            }, 'suppliercontact' => function ($query) {
                $query->where('isDefault', -1);
            }, 'paymentTerms_by' => function ($query) {
                $query->with('type');
            }, 'advance_detail' => function ($query) {
                $query->with(['category_by', 'grv_by', 'currency', 'supplier_by'])
                    ->where('poTermID', 0)
                    ->where('confirmedYN', 1)
                    ->where('isAdvancePaymentYN', 1)
                    ->where('approvedYN', -1);
            }, 'company',
            'secondarycompany' => function ($query) use ($createdDateTime) {
                $query->whereDate('cutOffDate', '<=', $createdDateTime);
            }, 'transactioncurrency', 'localcurrency', 'reportingcurrency', 'companydocumentattachment', 'project'
        ])->first();


        $is_specification = false;

        if (!empty($output)) {

            foreach ($output->detail as $item) {

                if(isset($item->item->specification) || (isset($item->item->specification) && $item->item->specification != null))
                {
                    $is_specification = true;
                }

                $date = $output->createdDateTime;

                $item->inhand = ErpItemLedger::where('itemSystemCode', $item->itemCode)
                    ->where('companySystemID', $item->companySystemID)
                    ->sum('inOutQty');

                $dt = new Carbon($date);
                $from = $dt->subMonths(3);;
                $to = new Carbon($date);

                $item->lastThreeMonthIssued = (ErpItemLedger::where('itemSystemCode', $item->itemCode)
                        ->where('companySystemID', $item->companySystemID)
                        ->where('documentSystemID', 8)
                        ->whereBetween('transactionDate', [$from, $to])
                        ->sum('inOutQty')) * -1;
            }
        }
        $output['is_specification'] = $is_specification;

        $isProjectBase = CompanyPolicyMaster::where('companyPolicyCategoryID', 56)
            ->where('companySystemID', $output->companySystemID)
            ->where('isYesNO', 1)
            ->exists();

        $output['isProjectBase'] = $isProjectBase;

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
            'employeesdepartments.approvalDeligated',
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
            'erp_purchaseordermaster.approval_remarks',
            'erp_purchaseordermaster.budgetYear',
            'erp_purchaseordermaster.rcmActivated',
            'erp_purchaseordermaster.amended',
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
                ->where('employeesdepartments.employeeSystemID', $empID)
                ->where('employeesdepartments.isActive', 1)
                ->where('employeesdepartments.removedYN', 0);
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

        $isEmployeeDischarched = \Helper::checkEmployeeDischarchedYN();

        if ($isEmployeeDischarched == 'true') {
            $purchaseRequests = [];
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
            $documentSystemCode = $request->input('documentSystemCode');
            $updateData = ['status' => 0];
            $this->tenderPoRepository->update($documentSystemCode, $updateData);
            return $this->sendResponse(array(), $reject["message"]);
        }
    }

    public function getGoodReceivedNoteDetailsForPO(Request $request)
    {
        $input = $request->all();

        $purchaseOrderID = $input['purchaseOrderID'];

        $detail = DB::select('SELECT erp_grvdetails.grvAutoID,erp_grvdetails.companyID,erp_grvdetails.purchaseOrderMastertID,erp_grvmaster.grvDate,erp_grvmaster.grvPrimaryCode,erp_grvmaster.grvDoRefNo,erp_grvmaster.grvCancelledYN,erp_grvdetails.itemPrimaryCode,
erp_grvdetails.itemDescription,warehousemaster.wareHouseDescription,erp_grvmaster.grvNarration,erp_grvmaster.supplierName,erp_grvdetails.poQty AS POQty,erp_grvdetails.noQty,erp_grvmaster.approved,erp_grvmaster.grvConfirmedYN,currencymaster.CurrencyCode,currencymaster.DecimalPlaces as transDeci,erp_grvdetails.GRVcostPerUnitSupTransCur,erp_grvdetails.unitCost,erp_grvdetails.GRVcostPerUnitSupTransCur*erp_grvdetails.noQty AS total,erp_grvdetails.GRVcostPerUnitSupTransCur*erp_grvdetails.noQty AS totalCost, erp_grvmaster.refferedBackYN FROM erp_grvdetails INNER JOIN erp_grvmaster ON erp_grvdetails.grvAutoID = erp_grvmaster.grvAutoID INNER JOIN warehousemaster ON erp_grvmaster.grvLocation = warehousemaster.wareHouseSystemCode INNER JOIN currencymaster ON erp_grvdetails.supplierItemCurrencyID = currencymaster.currencyID WHERE purchaseOrderMastertID = ' . $purchaseOrderID . ' ');

        return $this->sendResponse($detail, 'Details retrieved successfully');
    }

    function getInvoiceDetailsForPO(Request $request)
    {
        $input = $request->all();

        $purchaseOrderID = $input['purchaseOrderID'];

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
                                currencymaster.DecimalPlaces AS transDeci,
                                erp_bookinvsuppdet.totTransactionAmount,
                                rptCurrency.CurrencyCode As rptCurrencyCode,
                                rptCurrency.DecimalPlaces AS rptDecimalPlaces,
                                erp_bookinvsuppdet.totRptAmount,
                                erp_bookinvsuppdet.grvAutoID,
                                erp_bookinvsuppmaster.bookingSuppMasInvAutoID 
                            FROM
                                erp_bookinvsuppmaster
                                INNER JOIN erp_bookinvsuppdet ON erp_bookinvsuppmaster.bookingSuppMasInvAutoID = erp_bookinvsuppdet.bookingSuppMasInvAutoID
                                LEFT JOIN currencymaster ON erp_bookinvsuppmaster.supplierTransactionCurrencyID = currencymaster.currencyID
                                LEFT JOIN currencymaster rptCurrency ON erp_bookinvsuppmaster.companyReportingCurrencyID = rptCurrency.currencyID
                                LEFT JOIN erp_grvmaster ON erp_bookinvsuppdet.grvAutoID = erp_grvmaster.grvAutoID 
                            WHERE
                                purchaseOrderID = ' . $purchaseOrderID . ' ');

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
            //            ->where('poType_N','!=',5)
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
            [
                'erp_purchaseordermaster.purchaseOrderID',
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
                'erp_purchaseordermaster.poType_N',
                'erp_purchaseordermaster.partiallyGRVAllowed',
                'erp_purchaseordermaster.logisticsAvailable'
            ]
        );

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

        $fullyRetuned = false;

        $detailExistGRV = GRVDetails::where('purchaseOrderMastertID', $purchaseOrderID)
            ->first();
        if($purchaseOrder->grvRecieved == 2) {
            $puchaseReturnDetails = PurchaseReturnDetails::where('grvAutoID',$detailExistGRV->grvAutoID)->get();
            foreach($puchaseReturnDetails as $puchaseReturnDetail) {
                $fullyRetuned = ($puchaseReturnDetail->GRVQty == $puchaseReturnDetail->noQty) ? true : false;
            }
            if($fullyRetuned) {
                return $this->sendResponse($purchaseOrderID, 'Details retrieved successfully');
            }else {
                return $this->sendError('Cannot cancel, GRV is created for this PO');
            }
        }
        if (!empty($detailExistGRV)) {
            if ($type == 1) {
                if($purchaseOrder->grvRecieved == 0) {
                    $fullyRetuned = true;
                }
                if($fullyRetuned) {
                    return $this->sendResponse($purchaseOrderID, 'Details retrieved successfully');
                }else {
                    return $this->sendError('Cannot cancel, GRV is created for this PO');
                }
            } else {
                return $this->sendError('Cannot revert it back to amend. GRV is created for this PO');
            }
        }

        // check main work order has subwork order
        if ($type != 1) {
            if ($purchaseOrder->poType_N == 6) {
                return $this->sendError('Sub work order Cannot revert it back to amend');
            } elseif ($purchaseOrder->poType_N == 5) {
                $hasSubWorkOrder = ProcumentOrder::where('poType_N', 6)->where('WO_purchaseOrderID', $purchaseOrder->purchaseOrderID)
                    ->count();
                if ($hasSubWorkOrder > 0) {
                    return $this->sendError('Cannot revert it back to amend. Sub Work Order is created for this WO');
                }
            }
        }


        $detailExistAPD = AdvancePaymentDetails::where('purchaseOrderID', $purchaseOrderID)
            ->first();

        if (!empty($detailExistAPD)) {
            return $this->sendError('Cannot ' . $comment . '. Advance payment is created for this PO', 404, ['advancePaymentError' => true]);
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

        $poAdvancePaymentType = PoPaymentTerms::where('poID', $purchaseOrderID)
            ->where('LCPaymentYN', 2)
            ->first();

        if ($poAdvancePaymentType) {
            $advancePayment = PoAdvancePayment::where('poTermID', $poAdvancePaymentType->paymentTermID)->first();

            if ($advancePayment && isset($advancePayment->selectedToPayment) && $advancePayment->selectedToPayment == 0) {
                $advancePayment->cancelledYN = 1;
                $advancePayment->cancelledComment = $input['cancelComments'];
                $advancePayment->cancelledByEmployeeSystemID = \Helper::getEmployeeSystemID();
                $advancePayment->cancelledDate = Carbon::now();

                $advancePayment->save();
            }
        }

        AuditTrial::createAuditTrial($purchaseOrder->documentSystemID, $purchaseOrderID, $input['cancelComments'], 'cancelled');

        $emails = array();
        $document = DocumentMaster::where('documentSystemID', $purchaseOrder->documentSystemID)->first();

        $cancelDocNameBody = $document->documentDescription . ' <b>' . $purchaseOrder->purchaseOrderCode . '</b>';
        $cancelDocNameSubject = $document->documentDescription . ' ' . $purchaseOrder->purchaseOrderCode;

        $body = '<p>' . $cancelDocNameBody . ' is cancelled due to below reason.</p><p>Comment : ' . $input['cancelComments'] . '</p>';
        $subject = $cancelDocNameSubject . ' is cancelled';

        if ($purchaseOrder->poConfirmedYN == 1) {
            $emails[] = array(
                'empSystemID' => $purchaseOrder->poConfirmedByEmpSystemID,
                'companySystemID' => $purchaseOrder->companySystemID,
                'docSystemID' => $purchaseOrder->documentSystemID,
                'alertMessage' => $subject,
                'emailAlertMessage' => $body,
                'docSystemCode' => $purchaseOrder->purchaseOrderID
            );
        }

        $documentApproval = DocumentApproved::where('companySystemID', $purchaseOrder->companySystemID)
            ->where('documentSystemCode', $purchaseOrder->purchaseOrderID)
            ->where('documentSystemID', $purchaseOrder->documentSystemID)
            ->where('approvedYN', -1)
            ->get();

        foreach ($documentApproval as $da) {
            $emails[] = array(
                'empSystemID' => $da->employeeSystemID,
                'companySystemID' => $purchaseOrder->companySystemID,
                'docSystemID' => $purchaseOrder->documentSystemID,
                'alertMessage' => $subject,
                'emailAlertMessage' => $body,
                'docSystemCode' => $purchaseOrder->purchaseOrderID
            );
        }

        $sendEmail = \Email::sendEmail($emails);
        if (!$sendEmail["success"]) {
            return $this->sendError($sendEmail["message"], 500);
        }

        CancelDocument::sendEmail($input);

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
            $emails[] = array(
                'empSystemID' => $purchaseOrder->poConfirmedByEmpSystemID,
                'companySystemID' => $purchaseOrder->companySystemID,
                'docSystemID' => $purchaseOrder->documentSystemID,
                'alertMessage' => $subject,
                'emailAlertMessage' => $body,
                'docSystemCode' => $purchaseOrder->purchaseOrderID
            );
        }

        $documentApproval = DocumentApproved::where('companySystemID', $purchaseOrder->companySystemID)
            ->where('documentSystemCode', $purchaseOrder->purchaseOrderID)
            ->where('documentSystemID', $purchaseOrder->documentSystemID)
            //->where('approvedYN', -1)
            ->get();

        foreach ($documentApproval as $da) {

            if ($da->approvedYN == -1) {
                $emails[] = array(
                    'empSystemID' => $da->employeeSystemID,
                    'companySystemID' => $purchaseOrder->companySystemID,
                    'docSystemID' => $purchaseOrder->documentSystemID,
                    'alertMessage' => $subject,
                    'emailAlertMessage' => $body,
                    'docSystemCode' => $purchaseOrder->purchaseOrderID
                );
            }
        }

        $deleteApproval = DocumentApproved::where('documentSystemCode', $purchaseOrderID)
            ->where('companySystemID', $purchaseOrder->companySystemID)
            ->where('documentSystemID', $input['documentSystemID'])
            ->delete();

        BudgetConsumedData::where('documentSystemCode', $purchaseOrderID)
            ->where('companySystemID', $purchaseOrder->companySystemID)
            ->where('documentSystemID', $input['documentSystemID'])
            ->delete();

        if ($deleteApproval) {
            $update = ProcumentOrder::where('purchaseOrderID', $purchaseOrderID)
                ->update([
                    'refferedBackYN' => 0,
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

        AuditTrial::createAuditTrial($purchaseOrder->documentSystemID, $purchaseOrderID, $input['returnComment'], 'returned back to amend');

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
                $months[$i]["id"] = $start->format('Y-m');
                $months[$i]["value"] = $start->format('F Y');
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

        if ($validator->fails()) {
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
        if ($input['documentId'] == 1) {
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
	AND year(erp_bookinvsuppmaster.postedDate) IN (' . $commaSeperatedYears . ')
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

            //echo $doc2_query;
            //exit();
            $supplierReportGRVBase = DB::select($doc2_query);
        }
        $alltotal = array();
        $i = 0;
        if (!empty($months)) {
            foreach ($months as $key => $val) {
                if ($input['currency'] == 1) {
                    $tot = collect($supplierReportGRVBase)->pluck($key)->toArray();
                    $alltotal[$i]["id"] = $key;
                    $alltotal[$i]["value"] = array_sum($tot);
                } else {
                    $tot = collect($supplierReportGRVBase)->pluck($key)->toArray();
                    $alltotal[$i]["id"] = $key;
                    $alltotal[$i]["value"] = array_sum($tot);
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

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
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
	AND year(erp_bookinvsuppmaster.postedDate) IN (' . $commaSeperatedYears . ')
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
                    $alltotal[$i]["id"] = $key;
                    $alltotal[$i]["value"] = array_sum($tot);
                } else {
                    $tot = collect($supplierReportGRVBase)->pluck($key)->toArray();
                    $alltotal[$i]["id"] = $key;
                    $alltotal[$i]["value"] = array_sum($tot);
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

        \Excel::create('item_wise_po_analysis', function ($excel) use ($data) {

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
            $emails[] = array(
                'empSystemID' => $procumentOrder->poConfirmedByEmpSystemID,
                'companySystemID' => $procumentOrder->companySystemID,
                'docSystemID' => $procumentOrder->documentSystemID,
                'alertMessage' => $subject,
                'emailAlertMessage' => $body,
                'docSystemCode' => $procumentOrder->purchaseOrderID
            );
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

        AuditTrial::createAuditTrial($procumentOrder->documentSystemID, $input['purchaseOrderID'], $input['manuallyClosedComment'], 'manually closed');

        $documentApproval = DocumentApproved::where('companySystemID', $procumentOrder->companySystemID)
            ->where('documentSystemCode', $procumentOrder->purchaseOrderID)
            ->where('documentSystemID', $procumentOrder->documentSystemID)
            ->get();

        foreach ($documentApproval as $da) {
            if ($da->approvedYN == -1) {
                $emails[] = array(
                    'empSystemID' => $da->employeeSystemID,
                    'companySystemID' => $procumentOrder->companySystemID,
                    'docSystemID' => $procumentOrder->documentSystemID,
                    'alertMessage' => $subject,
                    'emailAlertMessage' => $body,
                    'docSystemCode' => $procumentOrder->purchaseOrderID
                );
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
     * @param int $request
     *
     * @return string
     * @throws \Throwable
     */

    public function getProcumentOrderPrintPDF(Request $request)
    {

        $id = $request->get('id');
        $paymentTerms = $request->get('paymentTerms');
        $detailsComment = $request->get('detailsComment');
        $digitalStamp = $request->get('digitalStamp');
        $spec_id = 1;
        $procumentOrder = $this->procumentOrderRepository->findWithoutFail($id);

        if (empty($procumentOrder)) {
            return $this->sendError('Procurement Order not found');
        }

        $outputRecord = ProcumentOrder::where('purchaseOrderID', $procumentOrder->purchaseOrderID)->with(['project','segment','created_by','detail' => function ($query) {
            $query->with(['project','unit','altUom','item'=>function($query1){
                $query1->select('itemCodeSystem','itemDescription')->with('specification');
            }]);
        }, 'approved_by' => function ($query) {
            $query->with(['employee'=>function($query2){
                $query2->with(['hr_emp'=>function($query3){
                    $query3->with(['designation']);
                }]);
            }]);
            $query->where('rejectedYN', 0);
            $query->whereIN('documentSystemID', [2, 5, 52]);
        }, 'supplier' => function ($query) {
            $query->select('vatNumber', 'supplierCodeSystem');
        }, 'suppliercontact' => function ($query) {
            $query->where('isDefault', -1);
        }, 'company', 'transactioncurrency', 'companydocumentattachment', 'paymentTerms_by'])->get();

        $is_specification = 0;

        if (!empty($outputRecord)) {

            foreach ($outputRecord as $item) {

                foreach ($item->detail as $val) {
                    if(isset($val->item->specification) || $val->item->specification != null)
                    {
                        $is_specification = 1;
                        break;
                    }
                }
            }
        }

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

        $checkCompanyIsMerged = SecondaryCompany::where('companySystemID', $procumentOrder->companySystemID)
            ->whereDate('cutOffDate', '<=', Carbon::parse($procumentOrder->createdDateTime))
            ->first();

        $isMergedCompany = false;
        if ($checkCompanyIsMerged) {
            $isMergedCompany = true;
        }

        $checkAltUOM = CompanyPolicyMaster::where('companyPolicyCategoryID', 60)
            ->where('companySystemID', $procumentOrder->companySystemID)
            ->first();

        $isProjectBase = CompanyPolicyMaster::where('companyPolicyCategoryID', 56)
            ->where('companySystemID', $procumentOrder->companySystemID)
            ->where('isYesNO', 1)
            ->exists();

        $supplierID = $outputRecord[0]->supplierID;
        $purchaseOrderID = $outputRecord[0]->purchaseOrderID;
        $purchaseOrderPaymentTermConfigs = [];

        if ($paymentTerms == 1) {
            $assignedTemplateId = PaymentTermTemplateAssigned::where('supplierID', $supplierID)->value('templateID');
            $isActiveTemplate = PaymentTermTemplate::where('id', $assignedTemplateId)->value('isActive');

            $approvedPoConfigs = DB::table('po_wise_payment_term_config')->where('purchaseOrderID', $purchaseOrderID)
                ->where(function ($query) {
                    $query->where('isApproved', true)
                        ->orWhere('isRejected', true);
                })
                ->orderBy('sortOrder')->get();
            if ($approvedPoConfigs->isNotEmpty())
            {
                $purchaseOrderPaymentTermConfigs = $approvedPoConfigs->where('isSelected', true);
            }
            else if ($assignedTemplateId != null && $isActiveTemplate)
            {
                $poAssignedTemplateConfigs = DB::table('po_wise_payment_term_config')->where('purchaseOrderID', $purchaseOrderID)->where('templateID', $assignedTemplateId)->first();
                if (!$poAssignedTemplateConfigs) {
                    $paymentTermConfigs = PaymentTermConfig::where('templateId', $assignedTemplateId)->get();
                    $isDefaultAssign = false;
                    $this->createProcumentOrderPaymentTermConfigs($assignedTemplateId, $purchaseOrderID, $supplierID, $paymentTermConfigs, $isDefaultAssign);
                }
                $purchaseOrderPaymentTermConfigs = DB::table('po_wise_payment_term_config')->where('purchaseOrderID', $purchaseOrderID)->where('templateID', $assignedTemplateId)->where('isSelected', true)->orderBy('sortOrder')->get();
            } else
            {
                $poDefaultConfigUpdate = DB::table('po_wise_payment_term_config')->where('purchaseOrderID', $purchaseOrderID)->where('isDefaultAssign', true)->where('isConfigUpdate', true)->first();
                if ($poDefaultConfigUpdate) {
                    $purchaseOrderPaymentTermConfigs = DB::table('po_wise_payment_term_config')->where('purchaseOrderID', $purchaseOrderID)->where('templateID', $poDefaultConfigUpdate->templateID)
                        ->where('isDefaultAssign', true)->where('isSelected', true)->orderBy('sortOrder')->get();
                } else {
                    $defaultTemplateID = PaymentTermTemplate::where('isDefault', true)->value('id');
                    $poDefaultTemplateConfigs = DB::table('po_wise_payment_term_config')->where('purchaseOrderID', $purchaseOrderID)->where('templateID', $defaultTemplateID)->first();
                    if (!$poDefaultTemplateConfigs) {
                        $paymentTermConfigs = PaymentTermConfig::where('templateId', $defaultTemplateID)->get();
                        $isDefaultAssign = true;
                        $this->createProcumentOrderPaymentTermConfigs($defaultTemplateID, $purchaseOrderID, $supplierID, $paymentTermConfigs, $isDefaultAssign);
                    }
                    $purchaseOrderPaymentTermConfigs = DB::table('po_wise_payment_term_config')->where('purchaseOrderID', $purchaseOrderID)->where('templateID', $defaultTemplateID)->where('isSelected', true)->orderBy('sortOrder')->get();
                }
            }
        }

        $digitalStampDetails = array();
        if ($digitalStamp == 1) {
            $digitalStampDetails = CompanyDigitalStamp::where('company_system_id', $procumentOrder->companySystemID)->where('is_default', 1)->get();
            if(count($digitalStampDetails) > 0) {
                $digitalStampDetails = $digitalStampDetails[0];
            }
        }

        $order = array(
            'podata' => $outputRecord[0],
            'docRef' => $refernaceDoc,
            'numberFormatting' => $decimal,
            'isMergedCompany' => $isMergedCompany,
            'secondaryCompany' => $checkCompanyIsMerged,
            'title' => $documentTitle,
            'termsCond' => $paymentTerms,
            'detailComment' => $detailsComment,
            'digitalStamp' => $digitalStamp,
            'digitalStampDetails' => $digitalStampDetails,
            'specification' => $is_specification,
            'paymentTermsView' => $paymentTermsView,
            'addons' => $orderAddons,
            'isProjectBase' => $isProjectBase,
            'allowAltUom' => ($checkAltUOM) ? $checkAltUOM->isYesNO : false,
            'paymentTermConfigs' => $purchaseOrderPaymentTermConfigs
        );

        try {
            // check document type has set template as default, then get rendered html with data
            $data = $this->printTemplateService->getDefaultTemplateSource($procumentOrder['documentSystemID'], $order);

            if($data){
                $html = $data;
            }else{
                $html = view('print.purchase_order_print_pdf', $order);
            }
        }catch(\Exception $e) {
            Log::debug('=============== START PRINT TEMPLATE ERROR ==============');
            Log::info([
                'function' => 'getProcumentOrderPrintPDF->getDefaultTemplateSource',
                'request' => $request->all(),
                'data' => $order
            ]);
            Log::error($e);
            Log::debug('=============== END PRINT TEMPLATE ERROR ==============');

            // if failed to show dynamically created template then show static template
            $html = view('print.purchase_order_print_pdf', $order);
        }
        $time = strtotime("now");
        $fileName = 'procument_order' . $id . '_' . $time . '.pdf';
        $mpdf = new \Mpdf\Mpdf(['tempDir' => public_path('tmp'), 'mode' => 'utf-8', 'format' => 'A4-P', 'setAutoTopMargin' => 'stretch', 'autoMarginPadding' => -10]);
        $mpdf->AddPage('P');
        $mpdf->setAutoBottomMargin = 'stretch';
        $mpdf->WriteHTML($html);
        return $mpdf->Output($fileName, 'I');
    }

    public function createProcumentOrderPaymentTermConfigs($templateID, $purchaseOrderID, $supplierID, $paymentTermConfigs, $isDefaultAssign) {
        foreach ($paymentTermConfigs as $paymentTermConfig) {
            DB::table('po_wise_payment_term_config')->insert([
                'templateID' => $templateID,
                'purchaseOrderID' => $purchaseOrderID,
                'supplierID' => $supplierID,
                'term' => $paymentTermConfig->term,
                'description' => $paymentTermConfig->description,
                'sortOrder' => $paymentTermConfig->sortOrder,
                'isSelected' => $paymentTermConfig->isSelected,
                'isDefaultAssign' => $isDefaultAssign
            ]);
        }
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
            'erp_purchaseordermaster.approval_remarks',
            'erp_purchaseordermaster.serviceLine',
            'erp_purchaseordermaster.createdDateTime',
            'erp_purchaseordermaster.poConfirmedDate',
            'erp_purchaseordermaster.poTotalSupplierTransactionCurrency',
            'erp_purchaseordermaster.budgetYear',
            'erp_purchaseordermaster.rcmActivated',
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


        \Excel::create('item_wise_po_analysis', function ($excel) use ($data) {

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
                return $this->sendError('You cannot amend this order, this is already amended by ' . $amendEmpName, 500);
            }

            return $this->sendError('You cannot amend this order, this is already amended.', 500);
        }

        $procurementOrder->WO_amendYN = -1;
        $procurementOrder->WO_confirmedYN = 0;
        $procurementOrder->WO_amendRequestedByEmpSystemID = $employee->employeeSystemID;
        $procurementOrder->WO_amendRequestedByEmpID = $employee->empID;
        $procurementOrder->WO_amendRequestedDate = now();


        // $company = Company::where('companySystemID', $procurementOrder->companySystemID)->first();
        // if ($company) {
        //     $procurementOrder->vatRegisteredYN = $company->vatRegisteredYN;
        // }


        // $supplierAssignedDetai = SupplierAssigned::where('supplierCodeSytem', $procurementOrder->supplierID)
        //                                         ->where('companySystemID', $procurementOrder->companySystemID)
        //                                         ->first();

        // if ($supplierAssignedDetai) {
        //     $procurementOrder->supplierVATEligible = $supplierAssignedDetai->vatEligible;
        //     $procurementOrder->VATPercentage = 0; // $supplierAssignedDetai->vatPercentage;
        // }

        $procurementOrder->save();

        AuditTrial::createAuditTrial($procurementOrder->documentSystemID, $input['purchaseOrderID'], '', 'amended');

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
                return $this->sendError('You cannot amend this order, this is already amended by ' . $amendEmpName, 500);
            }

            return $this->sendError('You cannot amend this order, this is already amended.', 500);
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

        $oldVaTEligible = ($purchaseOrder->supplierVATEligible || $purchaseOrder->vatRegisteredYN) ? 1 : 0;

        $supplierCurrencyDecimalPlace = \Helper::getCurrencyDecimalPlace($input['supplierTransactionCurrencyID']);

        $input['companySystemID'] = $purchaseOrder->companySystemID;

        $supplier = SupplierMaster::where('supplierCodeSystem', $input['supplierID'])->first();

        if (empty($supplier)) {
            return $this->sendError('Supplier not found');
        }

        if (!isset($input['fromAmend'])) {
            $purchaseOrder->rcmActivated = isset($input['rcmActivated']) ? $input['rcmActivated'] : 0;
        }

        if (Helper::isLocalSupplier($input['supplierID'], $input['companySystemID'])) {
            if (!isset($input['fromAmend'])) {
                $purchaseOrder->rcmActivated = 0;
            }
        } else if (isset($input['preCheck']) && $input['preCheck']) {
            if ($purchaseOrder->vatRegisteredYN == 1) {   //  (isset($input['rcmActivated']) && $input['rcmActivated'])
                return $this->sendError('Do you want to activate Reverse Charge Mechanism for this PO', 500, array('type' => 'rcm_confirm'));
            }
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

        $currency = SupplierCurrency::where('supplierCodeSystem', $input['supplierID'])->where('currencyID', $input['supplierTransactionCurrencyID'])->first();



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
            //$purchaseOrder->VATPercentage = $supplierAssignedDetai->vatPercentage;
        }

        if ($purchaseOrder->supplierVATEligible == 1 || $purchaseOrder->rcmActivated) {
            $currencyConversionVatAmount = \Helper::currencyConversion($input['companySystemID'], $purchaseOrder->supplierTransactionCurrencyID, $purchaseOrder->supplierTransactionCurrencyID, $purchaseOrder->VATAmount);
            $purchaseOrder->VATAmountLocal = \Helper::roundValue($currencyConversionVatAmount['localAmount']);
            $purchaseOrder->VATAmountRpt = \Helper::roundValue($currencyConversionVatAmount['reportingAmount']);
        } else {
            $purchaseOrder->VATAmount = 0;
            $purchaseOrder->VATAmountLocal = 0;
            $purchaseOrder->VATAmountRpt = 0;
            $purchaseOrder->VATPercentage = 0;
        }

        //getting total sum of PO detail Amount
        $poMasterSum = PurchaseOrderDetails::select(DB::raw('COALESCE(SUM(netAmount),0) as masterTotalSum'))
            ->where('purchaseOrderMasterID', $purchaseOrder->purchaseOrderID)
            ->first();


        // update decimal places

        $poAddons = PoAddons::where('poId', $purchaseOrder->purchaseOrderID)
            ->get();

        foreach ($poAddons as $addon) {
            $addon->amount = round($addon->amount, $supplierCurrencyDecimalPlace);
            $addon->save();
        }

        //getting addon Total for PO
        $poAddonMasterSum = PoAddons::select(DB::raw('COALESCE(SUM(amount),0) as addonTotalSum'))
            ->where('poId', $purchaseOrder->purchaseOrderID)
            ->first();

        // po total vat
        $poMasterVATSum = PurchaseOrderDetails::select(DB::raw('COALESCE(SUM(VATAmount * noQty),0) as masterTotalVATSum'))
            ->where('purchaseOrderMasterID', $purchaseOrder->purchaseOrderID)
            ->first();

        $poMasterSumRounded = round($poMasterSum['masterTotalSum'], $supplierCurrencyDecimalPlace);
        $poAddonMasterSumRounded = round($poAddonMasterSum['addonTotalSum'], $supplierCurrencyDecimalPlace);
        $poVATMasterSumRounded = round($poMasterVATSum['masterTotalVATSum'], $supplierCurrencyDecimalPlace);


        if ($purchaseOrder->rcmActivated) {
            $poVATMasterSumRounded = 0;
        }

        $newlyUpdatedPoTotalAmount = $poMasterSumRounded + $poAddonMasterSumRounded + $poVATMasterSumRounded;

        $poMasterSumDeducted = ($newlyUpdatedPoTotalAmount - $purchaseOrder->poDiscountAmount);

        $currencyConversionMaster = \Helper::currencyConversion($input["companySystemID"], $purchaseOrder->supplierTransactionCurrencyID, $purchaseOrder->supplierTransactionCurrencyID, $poMasterSumDeducted);

        // calculating total Supplier Default currency total

        $currencyConversionMasterDefault = \Helper::currencyConversion($input["companySystemID"], $input['supplierTransactionCurrencyID'], $supplierCurrency->currencyID, $poMasterSumDeducted);

        $purchaseOrder->poTotalComRptCurrency = \Helper::roundValue($currencyConversionMaster['reportingAmount']);
        $purchaseOrder->poTotalLocalCurrency = \Helper::roundValue($currencyConversionMaster['localAmount']);
        $purchaseOrder->poTotalSupplierDefaultCurrency = \Helper::roundValue($currencyConversionMasterDefault['documentAmount']);
        $purchaseOrder->poTotalSupplierTransactionCurrency = \Helper::roundValue($poMasterSumDeducted);
        $purchaseOrder->companyReportingER = round($currencyConversionMaster['trasToRptER'], 8);
        $purchaseOrder->localCurrencyER = round($currencyConversionMaster['trasToLocER'], 8);


        // check local supplier and update logistic


        $isLocalSupplier = Helper::isLocalSupplier($purchaseOrder->supplierID, $purchaseOrder->companySystemID);
        if ($isLocalSupplier) {
            $purchaseOrder->logisticsAvailable = 0;
        } else {
            $purchaseOrder->logisticsAvailable = -1;
        }

        $company = Company::where('companySystemID', $purchaseOrder->companySystemID)->first();
        if ($company) {
            $purchaseOrder->vatRegisteredYN = $company->vatRegisteredYN;
        }

        $purchaseOrder->save();

        foreach ($purchaseOrder->detail as $item) {

            $purchaseOrderDetail = PurchaseOrderDetails::where('purchaseOrderDetailsID', $item->purchaseOrderDetailsID)->first();

            $purchaseOrderDetail->supplierItemCurrencyID = $purchaseOrder->supplierTransactionCurrencyID;
            $purchaseOrderDetail->foreignToLocalER = $purchaseOrder->supplierTransactionER;

            $purchaseOrderDetail->supplierDefaultCurrencyID = $purchaseOrder->supplierDefaultCurrencyID;
            $purchaseOrderDetail->supplierDefaultER = $purchaseOrder->supplierDefaultER;

            $purchaseOrderDetail->companyReportingER = $purchaseOrder->companyReportingER;
            $purchaseOrderDetail->localCurrencyER = $purchaseOrder->localCurrencyER;

            $purchaseOrderDetail->unitCost = round($purchaseOrderDetail->unitCost, $supplierCurrencyDecimalPlace);

            if ($purchaseOrderDetail->discountPercentage > 0) {
                $purchaseOrderDetail->discountAmount = round($purchaseOrderDetail->unitCost * ($purchaseOrderDetail->discountPercentage / 100), $supplierCurrencyDecimalPlace);
            }

            if (TaxService::checkPOVATEligible($purchaseOrder->supplierVATEligible, $purchaseOrder->vatRegisteredYN)) {
                $netUnitAmount = 0;
                if (!$oldVaTEligible) {
                    $vatDetails = TaxService::getVATDetailsByItem($purchaseOrder->companySystemID, $item['itemCode'], $purchaseOrder->supplierID);
                    $purchaseOrderDetail->VATPercentage = $vatDetails['percentage'];
                    $purchaseOrderDetail->VATApplicableOn = $vatDetails['applicableOn'];
                    $purchaseOrderDetail->vatMasterCategoryID = $vatDetails['vatMasterCategoryID'];
                    $purchaseOrderDetail->vatSubCategoryID = $vatDetails['vatSubCategoryID'];
                    $purchaseOrderDetail->VATAmount = 0;
                }

                if ($purchaseOrderDetail->VATApplicableOn === 1) { // before discount
                    $netUnitAmount = floatval($purchaseOrderDetail->unitCost);
                } else {
                    $netUnitAmount = floatval($purchaseOrderDetail->unitCost) - floatval($purchaseOrderDetail->discountAmount);
                }

                if ($netUnitAmount > 0 && $purchaseOrderDetail->VATPercentage > 0) {
                    $purchaseOrderDetail->VATAmount = (($netUnitAmount / 100) * $purchaseOrderDetail->VATPercentage);
                }
                $purchaseOrderDetail->netAmount = ($purchaseOrderDetail->unitCost - $purchaseOrderDetail->discountAmount) * $purchaseOrderDetail->noQty;

                $currencyConversionVAT = \Helper::currencyConversion($purchaseOrder->companySystemID, $purchaseOrder->supplierTransactionCurrencyID, $purchaseOrder->supplierTransactionCurrencyID, $purchaseOrderDetail->VATAmount);

                $purchaseOrderDetail->VATAmountLocal = \Helper::roundValue($currencyConversionVAT['localAmount']);
                $purchaseOrderDetail->VATAmountRpt = \Helper::roundValue($currencyConversionVAT['reportingAmount']);
                $calculateItemDiscount = 0;

                if ($purchaseOrder->poDiscountAmount > 0 && $poMasterSumRounded > 0 && $purchaseOrderDetail->noQty > 0) {
                    $calculateItemDiscount = ((($purchaseOrderDetail->netAmount - (($purchaseOrderDetail->netAmount / $poMasterSumRounded) * $purchaseOrder->poDiscountAmount))) / $purchaseOrderDetail->noQty);
                } else {
                    $calculateItemDiscount = $purchaseOrderDetail->unitCost - $purchaseOrderDetail->discountAmount;
                }

                if (!$purchaseOrder->vatRegisteredYN) {
                    $calculateItemDiscount = $calculateItemDiscount + $purchaseOrderDetail->VATAmount;
                }
                $calculateItemTax = $calculateItemDiscount;

                $currencyConversion = \Helper::currencyConversion($purchaseOrderDetail->companySystemID, $purchaseOrder->supplierTransactionCurrencyID, $purchaseOrder->supplierTransactionCurrencyID, $calculateItemTax);

                $currencyConversionDefaultW = \Helper::currencyConversion($purchaseOrderDetail->companySystemID, $purchaseOrder->supplierTransactionCurrencyID, $purchaseOrder->supplierDefaultCurrencyID, $calculateItemTax);

                $purchaseOrderDetail->GRVcostPerUnitSupTransCur = \Helper::roundValue($calculateItemTax);
                $purchaseOrderDetail->GRVcostPerUnitComRptCur = \Helper::roundValue($currencyConversion['reportingAmount']);
                $purchaseOrderDetail->GRVcostPerUnitLocalCur = \Helper::roundValue($currencyConversion['localAmount']);

                $purchaseOrderDetail->purchaseRetcostPerUnitTranCur = \Helper::roundValue($calculateItemTax);
                $purchaseOrderDetail->purchaseRetcostPerUnitRptCur = \Helper::roundValue($currencyConversion['reportingAmount']);
                $purchaseOrderDetail->purchaseRetcostPerUnitLocalCur = \Helper::roundValue($currencyConversion['localAmount']);

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

                if ($purchaseOrder->rcmActivated) {
                    $currencyConversionVAT = \Helper::currencyConversion($purchaseOrder->companySystemID, $purchaseOrder->supplierTransactionCurrencyID, $purchaseOrder->supplierTransactionCurrencyID, $purchaseOrderDetail->VATAmount);
                    $purchaseOrderDetail->VATAmountLocal = \Helper::roundValue($currencyConversionVAT['localAmount']);
                    $purchaseOrderDetail->VATAmountRpt = \Helper::roundValue($currencyConversionVAT['reportingAmount']);
                } else {
                    $purchaseOrderDetail->VATPercentage = 0;
                    $purchaseOrderDetail->VATAmount = 0;
                    $purchaseOrderDetail->VATAmountLocal = 0;
                    $purchaseOrderDetail->VATAmountRpt = 0;
                }
            }

            $purchaseOrderDetail->netAmount = ($purchaseOrderDetail->unitCost - $purchaseOrderDetail->discountAmount) * $purchaseOrderDetail->noQty;

            // adding supplier Default CurrencyID base currency conversion
            if ($purchaseOrderDetail->unitCost > 0) {
                $currencyConversionDefault = \Helper::currencyConversion($input['companySystemID'], $purchaseOrder->supplierTransactionCurrencyID, $purchaseOrder->supplierDefaultCurrencyID, $purchaseOrderDetail->unitCost);
                //$purchaseOrderDetail->GRVcostPerUnitSupDefaultCur = $currencyConversionDefault['documentAmount'];
                //$purchaseOrderDetail->purchaseRetcostPerUniSupDefaultCur = $currencyConversionDefault['documentAmount'];
            }

            $purchaseOrderDetail->madeLocallyYN = 0;
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

                    $calculateAddonLineAmount = 0;

                    if ($poMasterSumRounded > 0 && $AddonDeta['noQty'] > 0) {
                        $calculateAddonLineAmount = \Helper::roundFloatValue((($poAddonMasterSumRounded / $poMasterSumRounded) * $AddonDeta['netAmount']) / $AddonDeta['noQty']);
                    }

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
        //calculate tax amount according to the percentage for tax update

        //getting total sum of PO detail Amount
        $poMasterSum = PurchaseOrderDetails::select(DB::raw('COALESCE(SUM(netAmount),0) as masterTotalSum'))
            ->where('purchaseOrderMasterID', $purchaseOrder->purchaseOrderID)
            ->first();

        $poDiscountPercentage = 0;

        if ($poMasterSum && $poMasterSum['masterTotalSum'] && $purchaseOrder->poDiscountAmount > 0) {
            $poDiscountPercentage = ($purchaseOrder->poDiscountAmount / $poMasterSum['masterTotalSum']) * 100;
        }

        ProcumentOrder::find($purchaseOrder->purchaseOrderID)
            ->update([
                'poDiscountPercentage' => round($poDiscountPercentage, 2)
            ]);

        if ($purchaseOrder->isVatEligible) {
            TaxService::updatePOVAT($id);
        }

        //update request payment
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

        $procumentOrder = $this->procumentOrderRepository->with([
            'created_by', 'confirmed_by',
            'cancelled_by', 'manually_closed_by', 'modified_by', 'sent_supplier_by', 'amend_by', 'approved_by' => function ($query) {
                $query->with('employee')
                    ->whereIn('documentSystemID', [2, 5, 52]);
            }, 'audit_trial.modified_by'
        ])->findWithoutFail($id);

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
                        $emails[] = array(
                            'empSystemID' => $da->employee->employeeSystemID,
                            'companySystemID' => $documentApproval->companySystemID,
                            'docSystemID' => $documentApproval->documentSystemID,
                            'alertMessage' => $subject,
                            'emailAlertMessage' => $body,
                            'docSystemCode' => $documentApproval->documentSystemCode
                        );
                    }
                }

                $sendEmail = \Email::sendEmail($emails);
                if (!$sendEmail["success"]) {
                    return ['success' => false, 'message' => $sendEmail["message"]];
                }
            }
        }

        DocumentApproved::where('documentSystemCode', $purchaseOrderID)
            ->where('companySystemID', $purchaseOrder->companySystemID)
            ->where('documentSystemID', $purchaseOrder->documentSystemID)
            ->delete();

        /*Audit entry*/
        AuditTrial::createAuditTrial($purchaseOrder->documentSystemID, $purchaseOrderID, $input['reopenComments'], 'Reopened');

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

        $emailSentTo = 0;
        $procumentOrderUpdate = ProcumentOrder::where('purchaseOrderID', '=', $purchaseOrderID)->first();

        $fetchSupEmail = SupplierContactDetails::where('supplierID', $procumentOrderUpdate->supplierID)
            ->get();

        $supplierMaster = SupplierMaster::find($procumentOrderUpdate->supplierID);

        if ($fetchSupEmail) {
            foreach ($fetchSupEmail as $row) {
                if (!empty($row->contactPersonEmail)) {
                    $emailSentTo = 1;
                }
            }
        }

        if ($emailSentTo == 0) {
            if ($supplierMaster) {
                if (!empty($supplierMaster->supEmail)) {
                    $emailSentTo = 1;
                }
            }
        }

        $employee = \Helper::getEmployeeInfo();
        $empEmail = $employee ? $employee->empEmail : "";

        PoSentToSupplierJob::dispatch($input['db'], $purchaseOrderID, $empEmail);

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

        $purchaseOrderArray = array_except($purchaseOrder->toArray(), ['isWoAmendAccess', 'isVatEligible', 'rcmAvailable']);

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
            $purchaseOrder->amended = 1;
            $purchaseOrder->poConfirmedByEmpSystemID = null;
            $purchaseOrder->poConfirmedByEmpID = null;
            $purchaseOrder->poConfirmedByName = null;
            $purchaseOrder->poConfirmedDate = null;
            $purchaseOrder->RollLevForApp_curr = 1;
            $purchaseOrder->save();
        }

        DB::table('po_wise_payment_term_config')->where('purchaseOrderID', $purchaseOrderID)->update(['isRejected' => false]);

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
        \Excel::create('payment_suppliers_by_year', function ($excel) use ($data) {
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

        $supplierID = $request['supplierID'];
        $supplierID = (array)$supplierID;
        $supplierID = collect($supplierID)->pluck('id');

        $serviceLineSystemID = $request['serviceLineSystemID'];
        $serviceLineSystemID = (array)$serviceLineSystemID;
        $serviceLineSystemID = collect($serviceLineSystemID)->pluck('id');

        $type = $input['type'];
        $data = [];

        $output = ProcumentOrder::where('companySystemID', $input['companyId']);
        $output->where('documentSystemID', $input['documentId']);

        if (array_key_exists('serviceLineSystemID', $input)) {
            if ($input['serviceLineSystemID'] && !is_null($input['serviceLineSystemID'])) {
                $output->whereIn('serviceLineSystemID', $serviceLineSystemID);
            }
        }

        if (array_key_exists('poType_N', $input)) {
            if (($input['poType_N'] == 5 || $input['poType_N'] == 6) && !is_null($input['poType_N'])) {
                $output->where('poType_N', $input['poType_N']);
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
                $output->whereIn('supplierID', $supplierID);
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
                } else {
                    $data[$x]['Company Name'] = "";
                }

                $data[$x]['Order Code'] = $val->purchaseOrderCode;
                if ($val->segment) {
                    $data[$x]['Segment'] = $val->segment->ServiceLineDes;
                } else {
                    $data[$x]['Segment'] = "";
                }

                $data[$x]['Created at'] = \Helper::dateFormat($val->createdDateTime);
                if ($val->created_by) {
                    $data[$x]['Created By'] = $val->created_by->empName;
                } else {
                    $data[$x]['Created By'] = "";
                }

                if ($val->fcategory) {
                    $data[$x]['Category'] = $val->fcategory->categoryDescription;
                } else {
                    $data[$x]['Category'] = "Other";
                }

                $data[$x]['Narration'] = ($val->narration == "" || $val->narration == null) ? "-" : $val->narration;
                $data[$x]['Supplier Code'] = $val->supplierPrimaryCode;
                $data[$x]['Supplier Name'] = $val->supplierName;
                $data[$x]['Credit Period'] = $val->creditPeriod;

                if ($val->supplier) {
                    $data[$x][' Supplier Country'] = $val->supplier->country->countryName;
                } else {
                    $data[$x]['Supplier Country'] = "";
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
                } else {
                    $data[$x]['Transaction Currency'] = "";
                }

                if ($val->currency) {
                    $data[$x]['Transaction Amount'] = $val->poTotalSupplierTransactionCurrency;
                } else {
                    $data[$x]['Transaction Amount'] = "";
                }

                if ($val->localcurrency) {
                    $data[$x]['Local Amount (' . $val->localcurrency->CurrencyCode . ')'] = $val->poTotalLocalCurrency;
                } else {
                    $data[$x]['Local Amount (' . $val->localcurrency->CurrencyCode . ')'] = 0;
                }

                if ($val->reportingcurrency) {
                    $data[$x]['Reporting Amount (' . $val->reportingcurrency->CurrencyCode . ')'] = $val->poTotalComRptCurrency;
                } else {
                    $data[$x]['Reporting Amount (' . $val->reportingcurrency->CurrencyCode . ')'] = 0;
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

        // \Excel::create('po_master', function ($excel) use ($data) {
        //     $excel->sheet('sheet name', function ($sheet) use ($data) {
        //         $sheet->fromArray($data, null, 'A1', true);
        //         $sheet->setAutoSize(true);
        //         $sheet->getStyle('C1:C2')->getAlignment()->setWrapText(true);
        //     });
        //     $lastrow = $excel->getActiveSheet()->getHighestRow();
        //     $excel->getActiveSheet()->getStyle('A1:J' . $lastrow)->getAlignment()->setWrapText(true);
        // })->download($type);

        // return $this->sendResponse(array(), 'successfully export');

        $doc_name = 'purchase_order';
        $doc_name_path = 'purchase_order/';
        if($input['documentId'] == 52)
        {
            $doc_name = 'purchase_direct_order';
            $doc_name_path = 'purchase_direct_order/';
        }
        else if($input['documentId'] == 5)
        {
            $doc_name = 'purchase_work_order';
            $doc_name_path = 'purchase_work_order/';
        }
        $companyID = isset($input['companyId']) ? $input['companyId']: null;
        $companyMaster = Company::find($companyID);
        $companyCode = isset($companyMaster->CompanyID)?$companyMaster->CompanyID:'common';
        $detail_array = array(
            'company_code'=>$companyCode
        );

        $path = 'procurement/'.$doc_name_path.'excel/';
        $basePath = CreateExcel::process($data,$type,$doc_name,$path,$detail_array);

        if($basePath == '')
        {
            return $this->sendError('Unable to export excel');
        }
        else
        {
            return $this->sendResponse($basePath, trans('custom.success_export'));
        }
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
            ->addColumn('logisticTotal', function ($row) {
                return $this->getPoLogisticTotal($row);
            })
            ->addColumn('poTotalComRptCurrency', function ($row) {
                if ($row->manuallyClosed == 1) {
                    return floatval(array_sum(collect($this->getPOtoPaymentChain($row))->pluck('rptAmount')->toArray()));
                } else {
                    return $row->poTotalComRptCurrency;
                }
            })
            ->make(true);

        return $data;
    }

    function getPoLogisticTotal($row)
    {

        return PoAdvancePayment::where('poID', $row->purchaseOrderID)
            ->where('poTermID', 0)
            ->where('confirmedYN', 1)
            ->where('isAdvancePaymentYN', 1)
            ->where('approvedYN', -1)
            ->sum('reqAmountInPORptCur');
    }

    function getPOtoPaymentChain($row)
    {
        $grvMasters = GRVDetails::selectRaw('sum(noQty*GRVcostPerUnitLocalCur) as localAmount,
                                        sum(noQty*landingCost_RptCur) as rptAmount,
                                        purchaseOrderMastertID,grvAutoID')
            ->where('purchaseOrderMastertID', $row->purchaseOrderID)
            ->with(['grv_master' => function ($query) {
                $query->with(['currency_by']);
            }])
            ->groupBy('grvAutoID')
            ->get();

        foreach ($grvMasters as $grv) {
            $invoices = BookInvSuppDet::selectRaw('sum(totLocalAmount) as localAmount,
                                                 sum(totRptAmount) as rptAmount,grvAutoID,bookingSuppMasInvAutoID')
                ->where('grvAutoID', $grv->grvAutoID)
                ->where('purchaseOrderID', $row->purchaseOrderID)
                ->with(['suppinvmaster' => function ($query) {
                    $query->with(['transactioncurrency']);
                }])
                ->groupBy('bookingSuppMasInvAutoID')
                ->get();

            foreach ($invoices as $invoice) {
                //supplierPaymentAmount
                $paymentsInvoice = PaySupplierInvoiceDetail::selectRaw('sum(paymentLocalAmount) as localAmount,
                                                 sum(paymentComRptAmount) as rptAmount,bookingInvSystemCode,PayMasterAutoId,matchingDocID')
                    ->where('bookingInvSystemCode', $invoice->bookingSuppMasInvAutoID)
                    //->where('addedDocumentSystemID', 11)
                    ->where('matchingDocID', 0)
                    ->with(['payment_master' => function ($query) {
                        $query->with(['transactioncurrency']);
                    }])
                    ->groupBy('PayMasterAutoId')
                    ->get();

                $paymentsInvoiceMatch = PaySupplierInvoiceDetail::selectRaw('sum(paymentLocalAmount) as localAmount,
                                                 sum(paymentComRptAmount) as rptAmount,bookingInvSystemCode,matchingDocID')
                    ->where('bookingInvSystemCode', $invoice->bookingSuppMasInvAutoID)
                    //->where('addedDocumentSystemID', 11)
                    ->where('matchingDocID', '>', 0)
                    ->with(['matching_master' => function ($query) {
                        $query->with(['transactioncurrency']);
                    }])
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

        $supplierID = $request['supplierID'];
        $supplierID = (array)$supplierID;
        $supplierID = collect($supplierID)->pluck('id');

        if (array_key_exists('fromDate', $input) && $input['fromDate']) {
            $from = ((new Carbon($input['fromDate']))->format('Y-m-d'));
        }

        if (array_key_exists('toDate', $input) && $input['toDate']) {
            $to = ((new Carbon($input['toDate']))->format('Y-m-d'));
        }

        if (
            array_key_exists('toDate', $input) && array_key_exists('fromDate', $input) &&
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
            ->where('poType_N', '!=', 5)
            ->when($from && $to == "", function ($q) use ($from, $to) {
                return $q->where('approvedDate', '>=', $from);
            })
            ->when($from == "" && $to, function ($q) use ($from, $to) {
                return $q->where('approvedDate', '<=', $to);
            })
            ->when($from && $to, function ($q) use ($from, $to) {
                return $q->whereBetween('approvedDate', [$from, $to]);
            })
            ->when(request('supplierID', false), function ($q) use ($input,$supplierID) {
                return $q->whereIn('supplierID', $supplierID);
            })
            ->when(request('financeCategory', false), function ($q) use ($input) {
                return $q->where('financeCategory', $input['financeCategory']);
            })
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('purchaseOrderCode', 'LIKE', "%{$search}%")
                        ->orWhere('narration', 'LIKE', "%{$search}%");
                });
            })
            ->with(['supplier', 'fcategory']);
        /*->with(['supplier', 'detail' => function ($poDetail) {
            $poDetail->with([
                'grv_details' => function ($q) {
                    $q->with(['grv_master']);
                }]);
        }]);*/

        return $purchaseOrder;
    }


    public function exportPoToPaymentReport(Request $request,ExportReportToExcelService $exportReportToExcelService)
    {
        $input = $request->all();
        $data = array();
        $output = ($this->getPoToPaymentQry($input))->orderBy('purchaseOrderID', 'DES')->get();

        foreach ($output as $row) {
            $row->grvMasters = $this->getPOtoPaymentChain($row);

            if ($row->manuallyClosed == 1) {
                $row->poTotalComRptCurrency = floatval(array_sum(collect($row->grvMasters)->pluck('rptAmount')->toArray()));
            }

            $row->logisticTotal = $this->getPoLogisticTotal($row);
        }

        if(empty($data))
        {
            $poToPaymentReportHeader = new PoToPaymentReport();
            array_push($data, collect($poToPaymentReportHeader->getHeader())->toArray());
        }

        $type = $request->type;
        if (!empty($output)) {
            $x = 0;
            foreach ($output as $value) {
                $category = ($value->fcategor) ? $value->fcategory->categoryDescription : '';
                $supplierCode = ($value->supplier) ? $value->supplier->primarySupplierCode : '';
                $supplierName = ($value->supplier) ? $value->supplier->supplierName : '';
                $poToPaymentReport = new PoToPaymentReport();
                $poToPaymentReport->setCompanyID($value->companyID);
                $poToPaymentReport->setPoNumber($value->purchaseOrderCode);
                $poToPaymentReport->setCategory($category);
                $poToPaymentReport->setPoApprovedDate($value->approvedDate);
                $poToPaymentReport->setNarration($value->narration);
                $poToPaymentReport->setSupplierCode($supplierCode);
                $poToPaymentReport->setSupplierName($supplierName);
                $poToPaymentReport->setPoAmount(CurrencyService::convertNumberFormatToNumber(number_format($value->poTotalComRptCurrency, 2)));
                $poToPaymentReport->setLogisticAmount(CurrencyService::convertNumberFormatToNumber(number_format($value->logisticTotal, 2)));

                if (count($value->grvMasters) > 0) {
                    $grvMasterCount = 0;
                    foreach ($value->grvMasters as $grv) {
                        if ($grvMasterCount != 0) {
                            $x++;
                            $poToPaymentReport->setCompanyID("");
                            $poToPaymentReport->setPoNumber("");
                            $poToPaymentReport->setCategory("");
                            $poToPaymentReport->setPoApprovedDate("");
                            $poToPaymentReport->setNarration("");
                            $poToPaymentReport->setSupplierCode("");
                            $poToPaymentReport->setSupplierName("");
                            $poToPaymentReport->setPoAmount("");
                            $poToPaymentReport->setLogisticAmount("");
                        }

                        ($grv['grv_master']) ? $poToPaymentReport->setGrvCode($grv['grv_master']['grvPrimaryCode']) : $poToPaymentReport->setGrvCode("");
                        ($grv['grv_master']) ? $poToPaymentReport->setGrvDate($grv['grv_master']['grvDate']) : $poToPaymentReport->setGrvDate("");

                        $poToPaymentReport->setGrvAmount(CurrencyService::convertNumberFormatToNumber(number_format($grv['rptAmount'], 2)));

                        if (count($grv['invoices']) > 0) {
                            $invoicesCount = 0;
                            foreach ($grv['invoices'] as $invoice) {
                                if ($invoicesCount != 0) {
                                    $x++;
                                    $poToPaymentReport->setCompanyID("");
                                    $poToPaymentReport->setPoNumber("");
                                    $poToPaymentReport->setCategory("");
                                    $poToPaymentReport->setPoApprovedDate("");
                                    $poToPaymentReport->setNarration("");
                                    $poToPaymentReport->setSupplierCode("");
                                    $poToPaymentReport->setSupplierName("");
                                    $poToPaymentReport->setPoAmount("");
                                    $poToPaymentReport->setLogisticAmount("");
                                    $poToPaymentReport->setGrvCode("");
                                    $poToPaymentReport->setGrvDate("");
                                    $poToPaymentReport->setGrvAmount("");
                                }

                                ($invoice['suppinvmaster']) ? $poToPaymentReport->setInvoiceCode($invoice['suppinvmaster']['bookingInvCode']) : $poToPaymentReport->setInvoiceCode(null);
                                ($invoice['suppinvmaster']) ? $poToPaymentReport->setInvoiceDate($invoice['suppinvmaster']['supplierInvoiceDate']) : $poToPaymentReport->setInvoiceDate(null);
                                $poToPaymentReport->setInvoiceAmount(CurrencyService::convertNumberFormatToNumber(number_format($invoice['rptAmount'], 2)));

                                if (count($invoice['payments']) > 0) {
                                    $paymentsCount = 0;
                                    foreach ($invoice['payments'] as $payment) {
                                        if ($paymentsCount != 0) {
                                            $x++;
                                            $poToPaymentReport->setCompanyID("");
                                            $poToPaymentReport->setPoNumber("");
                                            $poToPaymentReport->setCategory("");
                                            $poToPaymentReport->setPoApprovedDate("");
                                            $poToPaymentReport->setNarration("");
                                            $poToPaymentReport->setSupplierCode("");
                                            $poToPaymentReport->setSupplierName("");
                                            $poToPaymentReport->setPoAmount("");
                                            $poToPaymentReport->setLogisticAmount("");
                                            $poToPaymentReport->setGrvCode("");
                                            $poToPaymentReport->setGrvDate("");
                                            $poToPaymentReport->setGrvAmount("");
                                            $poToPaymentReport->setInvoiceCode("");
                                            $poToPaymentReport->setInvoiceDate("");
                                            $poToPaymentReport->setInvoiceAmount("");
                                        }

                                        if ($payment['matchingDocID'] == 0) {
                                            if (!empty($payment['payment_master'])) {
                                                $poToPaymentReport->setPaymentCode( $payment['payment_master']['BPVcode']);
                                                $poToPaymentReport->setPaymentDate($payment['payment_master']['BPVdate']);
                                                $poToPaymentReport->setPaymentPostedDate($payment['payment_master']['postedDate']);
                                            } else {

                                                $poToPaymentReport->setPaymentCode("");
                                                $poToPaymentReport->setPaymentDate("");
                                                $poToPaymentReport->setPaymentPostedDate("");

                                            }
                                        } else if ($payment['matchingDocID'] > 0) {
                                            if (!empty($payment['matching_master'])) {
                                                $poToPaymentReport->setPaymentCode($payment['matching_master']['matchingDocCode']);
                                                $poToPaymentReport->setPaymentDate($payment['matching_master']['matchingDocdate']);
                                                $poToPaymentReport->setPaymentPostedDate("");
                                            } else {
                                                $poToPaymentReport->setPaymentCode("");
                                                $poToPaymentReport->setPaymentDate("");
                                                $poToPaymentReport->setPaymentPostedDate("");
                                            }
                                        } else {
                                            $poToPaymentReport->setPaymentCode("");
                                            $poToPaymentReport->setPaymentDate("");
                                            $poToPaymentReport->setPaymentPostedDate("");
                                        }
                                        $poToPaymentReport->setPaidAmount(CurrencyService::convertNumberFormatToNumber(number_format($payment['rptAmount'], 2)));
                                        $paymentsCount++;
                                    }
                                } else {
                                    $poToPaymentReport->setPaymentCode("");
                                    $poToPaymentReport->setPaymentDate("");
                                    $poToPaymentReport->setPaymentPostedDate("");
                                    $poToPaymentReport->setPaidAmount("");

                                }
                                $invoicesCount++;
                            }
                        } else {
                            $poToPaymentReport->setInvoiceCode("");
                            $poToPaymentReport->setInvoiceDate("");
                            $poToPaymentReport->setInvoiceAmount("");
                            $poToPaymentReport->setPaymentCode("");
                            $poToPaymentReport->setPaymentDate("");
                            $poToPaymentReport->setPaymentPostedDate("");
                            $poToPaymentReport->setPaidAmount("");
                        }
                        $grvMasterCount++;
                    }
                } else {

                    $poToPaymentReport->setGrvCode("");
                    $poToPaymentReport->setGrvDate("");
                    $poToPaymentReport->setGrvAmount("");
                    $poToPaymentReport->setInvoiceCode("");
                    $poToPaymentReport->setInvoiceDate("");
                    $poToPaymentReport->setInvoiceAmount("");
                    $poToPaymentReport->setPaymentCode("");
                    $poToPaymentReport->setPaymentDate("");
                    $poToPaymentReport->setPaymentPostedDate("");
                    $poToPaymentReport->setPaidAmount("");
                }
                array_push($data,collect($poToPaymentReport)->toArray());
                $x++;
            }
        }

        $companyMaster = Company::find(isset($request->companyId)?$request->companyId:null);
        $companyCode = isset($companyMaster->CompanyID)?$companyMaster->CompanyID:'common';
        $company_name = $companyMaster->CompanyName;
        $cur = null;
        $fileName = 'po_to_payment';
        $doc_name_path = 'po_to_payment/';
        $path = 'procurement/report/'.$doc_name_path.'excel/';
        $report = new PoToPaymentReport();
        $excelColumnFormat = $report->getColumnFormat();
        $startDate = $request->fromDate;
        $endDate = $request->toDate;
        $title = "PO to Payment Report";

        $exportToExcel = $exportReportToExcelService
            ->setTitle($title)
            ->setFileName($fileName)
            ->setPath($path)
            ->setCompanyCode($companyCode)
            ->setCompanyName($company_name)
            ->setFromDate($startDate)
            ->setToDate($endDate)
            ->setReportType(1)
            ->setData($data)
            ->setType('xls')
            ->setDateType(2)
            ->setExcelFormat($excelColumnFormat)
            ->setCurrency($cur)
            ->setColumnAutoSize(false)
            ->setDetails()
            ->generateExcel();

        if(!$exportToExcel['success'])
            return $this->sendError('Unable to export excel');

        return $this->sendResponse($exportToExcel['data'], trans('custom.success_export'));

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

        $categories = FinanceItemCategoryMaster::selectRaw('itemCategoryID as value,categoryDescription label')->get();

        $suppliers = $suppliers->take(15)->get(['companySystemID', 'primarySupplierCode', 'supplierName', 'supplierCodeSytem']);
        $output = array('suppliers' => $suppliers, 'categories' => $categories);

        return $this->sendResponse($output, 'Record retrieved successfully');
    }

    public function getReportSavingFliterData(Request $request)
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

        $subCategories = FinanceItemcategorySubAssigned::whereIN('companySystemID', $companyID)->groupBy('itemCategorySubID')->get();

        $categories = FinanceItemCategoryMaster::selectRaw('itemCategoryID as value,categoryDescription label')->get();

        $years = Year::orderby('year', 'desc')->get();

        $output = array('suppliers' => $supplierMaster, 'categories' => $categories, 'years' => $years, 'subCategories' => $subCategories);

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

        AuditTrial::createAuditTrial($purchaseOrderMasterData->documentSystemID, $input['purchaseOrderID'], '', 'expected delivery date amended to ' . $input['deliveryDate']);

        return $this->sendResponse($purchaseOrderMasterData->toArray(), 'Record updated successfully');
    }

    public function amendProcumentSubWorkOrder(Request $request)
    {
        $input = $request->all();
        $id = isset($input['id']) ? $input['id'] : 0;
        $employee = Helper::getEmployeeInfo();
        $purchaseOrder = ProcumentOrder::where('documentSystemID', 5)
            ->where('poType_N', 6)
            ->where('purchaseOrderID', $id)
            ->first();

        if (empty($purchaseOrder)) {
            return $this->sendError('Purchase Order not found');
        }

        if (!$purchaseOrder->isWoAmendAccess) {
            return $this->sendError('You cannot amend this sub work order.');
        }

        $mainWoTotal = ProcumentOrderDetail::where('purchaseOrderMasterID', $purchaseOrder->WO_purchaseOrderID)
            ->sum('netAmount');

        $subWoTotal = ProcumentOrderDetail::where('WO_purchaseOrderMasterID', $purchaseOrder->WO_purchaseOrderID)
            ->sum('netAmount');

        if ($subWoTotal > $mainWoTotal) {
            return $this->sendError('Sub work order is exceeding the main work order total amount. Cannot amend.');
        }

        $supplierCurrencyDecimalPlace = \Helper::getCurrencyDecimalPlace($purchaseOrder->supplierTransactionCurrencyID);
        //getting total sum of PO detail Amount
        $poMasterSum = PurchaseOrderDetails::select(DB::raw('COALESCE(SUM(netAmount),0) as masterTotalSum'))
            ->where('purchaseOrderMasterID', $id)
            ->first();

        //getting addon Total for PO
        $poAddonMasterSum = PoAddons::select(DB::raw('COALESCE(SUM(amount),0) as addonTotalSum'))
            ->where('poId', $id)
            ->first();

        $poMasterSumRounded = round($poMasterSum['masterTotalSum'], $supplierCurrencyDecimalPlace);
        $poAddonMasterSumRounded = round($poAddonMasterSum['addonTotalSum'], $supplierCurrencyDecimalPlace);


        $newlyUpdatedPoTotalAmount = $poMasterSumRounded + $poAddonMasterSumRounded;

        if ($purchaseOrder->poDiscountAmount > $newlyUpdatedPoTotalAmount) {
            return $this->sendError('Discount Amount should be less than order amount.', 500);
        }

        $poMasterSumDeducted = ($newlyUpdatedPoTotalAmount - $purchaseOrder->poDiscountAmount);
        $currencyConversionMaster = \Helper::currencyConversion($purchaseOrder->companySystemID, $purchaseOrder->supplierTransactionCurrencyID, $purchaseOrder->supplierDefaultCurrencyID, $poMasterSumDeducted);

        ProcumentOrder::where('purchaseOrderID', $id)
            ->update([
                'WO_confirmedYN' => 1,
                'WO_confirmedDate' => now(),
                'WO_confirmedByEmpID' => $employee->employeeSystemID,
                'poTotalSupplierTransactionCurrency' => \Helper::roundValue($poMasterSumDeducted),
                'poTotalSupplierDefaultCurrency' => \Helper::roundValue($currencyConversionMaster['documentAmount']),
                'poTotalComRptCurrency' => \Helper::roundValue($currencyConversionMaster['reportingAmount']),
                'poTotalLocalCurrency' => \Helper::roundValue($currencyConversionMaster['localAmount']),
                'companyReportingER' => round($currencyConversionMaster['trasToRptER'], 8),
                'localCurrencyER' => round($currencyConversionMaster['trasToLocER'], 8)
            ]);

        return $this->sendResponse($purchaseOrder, 'Sub work order amend successfully ');
    }

    public function amendProcumentSubWorkOrderReview(Request $request)
    {

        $input = $request->all();
        $id = $input['purchaseOrderID'];
        $employee = Helper::getEmployeeInfo();
        $masterData = ProcumentOrder::where('documentSystemID', 5)
            ->where('poType_N', 6)
            ->where('purchaseOrderID', $id)
            ->first();
        $documentName = "Sub Work Order";

        if (empty($masterData)) {
            return $this->sendError($documentName . ' not found');
        }

        if ($masterData->poConfirmedYN == 0 || $masterData->approved == 0) {
            return $this->sendError($documentName . ' is not approved. You cannot amend.');
        }

        if ($masterData->poConfirmedYN == 1 && $masterData->approved == -1 && $masterData->poCancelledYN == -1) {
            return $this->sendError($documentName . ' is cancelled. You cannot amend.');
        }

        if (
            $masterData->poConfirmedYN == 1 && $masterData->approved == -1 &&
            $masterData->poCancelledYN == 0 && $masterData->grvRecieved != 0
        ) {
            return $this->sendError($documentName . ' is received. You cannot amend');
        }


        if (
            $masterData->poConfirmedYN == 1 && $masterData->approved == -1 &&
            $masterData->poCancelledYN == 0 && $masterData->grvRecieved == 0 &&
            $masterData->WO_amendYN == -1 && $masterData->WO_confirmedYN != 1
        ) {
            return $this->sendError($documentName . ' is already amended. You cannot amend again.');
        }

        if (
            $masterData->poConfirmedYN != 1 || $masterData->approved != -1 ||
            $masterData->poCancelledYN != 0 || $masterData->grvRecieved != 0 ||
            $masterData->WO_amendYN != 0 || $masterData->WO_confirmedYN != 1
        ) {
            return $this->sendError('You cannot amend this ' . $documentName);
        }

        DB::beginTransaction();
        try {
            $masterData->WO_confirmedYN = 0;
            $masterData->WO_amendYN = -1;
            $masterData->WO_amendRequestedDate = now();
            $masterData->WO_amendRequestedByEmpID = $employee->empID;
            $masterData->WO_amendRequestedByEmpSystemID = $employee->employeeSystemID;
            $masterData->WO_confirmedDate = null;
            $masterData->WO_confirmedByEmpID = null;
            $masterData->save();

            DB::commit();
            return $this->sendResponse($masterData->toArray(), $documentName . ' amend saved successfully');
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage());
        }
    }

    public function updateGRVLogistic(Request $request)
    {

        $input = $request->all();

        $validator = \Validator::make($input, [
            'purchaseOrderID' => 'required',
            'type' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        $procumentOrder = ProcumentOrder::find($input['purchaseOrderID']);
        if (empty($procumentOrder)) {
            return $this->sendError('Order Detail not found');
        }

        $update_array = null;
        $is_update = null;
        if ($input['type'] == 1) {   // logistic

            $array = $this->procumentOrderRepository->swapValue($procumentOrder->logisticsAvailable);

            $message = "Logistic " . $array['text'];
            $update_array = array(
                'logisticsAvailable' => $array['value']
            );
        } else if ($input['type'] == 2) { //grv

            $array = $this->procumentOrderRepository->swapValue($procumentOrder->partiallyGRVAllowed);
            $message = "Partially GRV Allowed " . $array['text'];
            $update_array = array(
                'partiallyGRVAllowed' => $array['value']
            );
        } else {
            $array = [];
        }

        // Don't allow user to update above option, if Purchase order \ Work order \ Direct order added partially or fully in the GRV
        if ($procumentOrder->grvRecieved == 1) { // Default 0, partially received =1 and fully received 2
            return $this->sendError("Selected order partially grv received. Cannot be " . $array['text'], 500);
        } elseif ($procumentOrder->grvRecieved == 2) {
            return $this->sendError("Selected order fully grv received. Cannot be " . $array['text'], 500);
        }

        if ($update_array != null) {
            $is_update = $this->procumentOrderRepository->update($update_array, $input['purchaseOrderID']);
            AuditTrial::createAuditTrial($procumentOrder->documentSystemID, $input['purchaseOrderID'], '', $message);
            return $this->sendResponse($is_update, $message . " successfully");
        }
    }

    public function checkEOSPolicyAndSupplier(Request $request)
    {
        $input = $request->all();
        $companyId = $input['companySystemID'];
        $purchaseOrderID = $input['purchaseOrderID'];
        $hasEEOSSPolicy = false;
        if ($purchaseOrderID) {
            $purchaseOrder = ProcumentOrder::find($purchaseOrderID);
            $supAssigned = SupplierAssigned::where('supplierCodeSytem', $purchaseOrder->supplierID)
                ->where('companySystemID', $companyId)
                ->where('isActive', 1)
                ->where('isAssigned', -1)
                ->first();
            if (!empty($supAssigned) && $supAssigned->isMarkupPercentage) {
                $hasEEOSSPolicy = CompanyPolicyMaster::where('companySystemID', $companyId)
                    ->where('companyPolicyCategoryID', 41)
                    ->where('isYesNO', 1)
                    ->exists();

                // change markup percentage when changing supplier
                if ($hasEEOSSPolicy) {
                    $detail = PurchaseOrderDetails::where('purchaseOrderMasterID', $purchaseOrderID)->get();
                    foreach ($detail as $row) {

                        $discountedUnitPrice = $row->unitCost - $row->discountAmount;

                        if ($discountedUnitPrice > 0 && $supAssigned->markupPercentage > 0) {

                            $markupTransactionAmount = $supAssigned->markupPercentage * $discountedUnitPrice / 100;
                            $markupLocalAmount = 0;
                            $markupReportingAmount = 0;

                            if ($purchaseOrder->supplierTransactionCurrencyID != $purchaseOrder->localCurrencyID) {
                                $currencyConversion = Helper::currencyConversion($purchaseOrder->companySystemID, $purchaseOrder->supplierTransactionCurrencyID, $purchaseOrder->localCurrencyID, $markupTransactionAmount);
                                if (!empty($currencyConversion)) {
                                    $markupLocalAmount = $currencyConversion['documentAmount'];
                                }
                            } else {
                                $markupLocalAmount = $markupTransactionAmount;
                            }

                            if ($purchaseOrder->supplierTransactionCurrencyID != $purchaseOrder->companyReportingCurrencyID) {
                                $currencyConversion = Helper::currencyConversion($purchaseOrder->companySystemID, $purchaseOrder->supplierTransactionCurrencyID, $purchaseOrder->companyReportingCurrencyID, $markupTransactionAmount);
                                if (!empty($currencyConversion)) {
                                    $markupReportingAmount = $currencyConversion['documentAmount'];
                                }
                            } else {
                                $markupReportingAmount = $markupTransactionAmount;
                            }

                            /*round to 7 decimals*/
                            $markupTransactionAmount = Helper::roundValue($markupTransactionAmount);
                            $markupLocalAmount = Helper::roundValue($markupLocalAmount);
                            $markupReportingAmount = Helper::roundValue($markupReportingAmount);

                            $updateArray = [
                                'markupPercentage' => $supAssigned->markupPercentage,
                                'markupTransactionAmount' => $markupTransactionAmount,
                                'markupLocalAmount' => $markupLocalAmount,
                                'markupReportingAmount' => $markupReportingAmount
                            ];
                        } else {
                            $updateArray = [
                                'markupPercentage' => $supAssigned->markupPercentage,
                                'markupTransactionAmount' => 0,
                                'markupLocalAmount' => 0,
                                'markupReportingAmount' => 0
                            ];
                        }
                        PurchaseOrderDetails::where('purchaseOrderDetailsID', $row->purchaseOrderDetailsID)->update($updateArray);
                    }
                }
            }

            if (!$hasEEOSSPolicy) {
                PurchaseOrderDetails::where('purchaseOrderMasterID', $purchaseOrderID)->update([
                    'markupPercentage' => 0,
                    'markupTransactionAmount' => 0,
                    'markupLocalAmount' => 0,
                    'markupReportingAmount' => 0
                ]);
            }
        }

        $output = array('isEEOSSPolicy' => $hasEEOSSPolicy);
        return $this->sendResponse($output, 'Record retrieved successfully');
    }

    public function generateWorkOrder(Request $request)
    {
        $input = $request->all();
        $startDate = Carbon::parse($input['workOrderGenerateDate']);
        $date = Carbon::parse($input['workOrderGenerateDate'])->format('Y-m-d H:m:s');
        $firstDayOfMonth = $startDate->firstOfMonth()->format('Y-m-d H:m:s');

        $firstDayOfMonthPlusOne = $startDate->firstOfMonth()->addMonths(1)->format('Y-m-d H:m:s');

        $dhDaysInMonth = Carbon::parse($firstDayOfMonthPlusOne)->diffInDays(Carbon::parse($firstDayOfMonth));

        $checkSubPoCode = Carbon::parse($input['workOrderGenerateDate'])->format('m') . "_" . Carbon::parse($input['workOrderGenerateDate'])->format('Y');

        $checkPOMasters = ProcumentOrder::with(['detail'])
            ->whereDoesntHave('sub_work_orders', function ($query) use ($checkSubPoCode) {
                $query->where('purchaseOrderCode', 'LIKE', "%{$checkSubPoCode}%");
            })
            ->where('poType_N', 5)
            ->where('approved', -1)
            ->where('WO_fullyGenerated', 0)
            ->where('poCancelledYN', 0)
            ->whereRaw('WO_NoOfAutoGenerationTimes > WO_NoOfGeneratedTimes')
            ->whereDate('WO_PeriodFrom', '<=', $date)
            ->whereDate('WO_PeriodTo', '>=', $date)
            ->whereDate('approvedDate', '<=', $date)
            ->get();

        DB::beginTransaction();
        try {
            if (sizeof($checkPOMasters) > 0) {
                $logData = [
                    'date' => $date,
                    'createdUser' => \Helper::getEmployeeSystemID(),
                    'companySystemID' => $input['companySystemID']
                ];

                $workOrderLogRes = WorkOrderGenerationLog::create($logData);
                if (!$workOrderLogRes) {
                    return $this->sendError("Error occured while generating work order");
                }
            }

            foreach ($checkPOMasters as $key => $checkPOMaster) {
                if ($checkPOMaster->WO_terminateYN == 0) {
                    $noOfAutoGenerationTimes = $checkPOMaster->WO_NoOfAutoGenerationTimes;
                    $currentGeneratedTimes = $checkPOMaster->WO_NoOfGeneratedTimes + 1;

                    $poMasterData = [
                        "poProcessId" => $checkPOMaster->poProcessId,
                        "workOrderGenerateID" => $workOrderLogRes->id,
                        "companySystemID" => $checkPOMaster->companySystemID,
                        "companyID" => $checkPOMaster->companyID,
                        "departmentID" => $checkPOMaster->departmentID,
                        "serviceLineSystemID" => $checkPOMaster->serviceLineSystemID,
                        "serviceLine" => $checkPOMaster->serviceLine,
                        "companyAddress" => $checkPOMaster->companyAddress,
                        "documentSystemID" => $checkPOMaster->documentSystemID,
                        "documentID" => $checkPOMaster->documentID,
                        "purchaseOrderCode" => $checkPOMaster->purchaseOrderCode . "\\" . Carbon::parse($input['workOrderGenerateDate'])->format('d') . "_" . Carbon::parse($input['workOrderGenerateDate'])->format('m') . "_" . Carbon::parse($input['workOrderGenerateDate'])->format('Y'),
                        "serialNumber" => $checkPOMaster->serialNumber,
                        "supplierID" => $checkPOMaster->supplierID,
                        "supplierPrimaryCode" => $checkPOMaster->supplierPrimaryCode,
                        "supplierName" => $checkPOMaster->supplierName,
                        "supplierAddress" => $checkPOMaster->supplierAddress,
                        "supplierTelephone" => $checkPOMaster->supplierTelephone,
                        "supplierFax" => $checkPOMaster->supplierFax,
                        "supplierEmail" => $checkPOMaster->supplierEmail,
                        "creditPeriod" => $checkPOMaster->creditPeriod,
                        "expectedDeliveryDate" => Carbon::now(),
                        "narration" => $checkPOMaster->narration,
                        "poLocation" => $checkPOMaster->poLocation,
                        "financeCategory" => $checkPOMaster->financeCategory,
                        "referenceNumber" => $checkPOMaster->referenceNumber,
                        "shippingAddressID" => $checkPOMaster->shippingAddressID,
                        "shippingAddressDescriprion" => $checkPOMaster->shippingAddressDescriprion,
                        "invoiceToAddressID" => $checkPOMaster->invoiceToAddressID,
                        "invoiceToAddressDescription" => $checkPOMaster->invoiceToAddressDescription,
                        "soldToAddressID" => $checkPOMaster->soldToAddressID,
                        "soldToAddressDescriprion" => $checkPOMaster->soldToAddressDescriprion,
                        "paymentTerms" => $checkPOMaster->paymentTerms,
                        "deliveryTerms" => $checkPOMaster->deliveryTerms,
                        "panaltyTerms" => $checkPOMaster->panaltyTerms,
                        "localCurrencyID" => $checkPOMaster->localCurrencyID,
                        "localCurrencyER" => $checkPOMaster->localCurrencyER,
                        "companyReportingCurrencyID" => $checkPOMaster->companyReportingCurrencyID,
                        "companyReportingER" => $checkPOMaster->companyReportingER,
                        "supplierDefaultCurrencyID" => $checkPOMaster->supplierDefaultCurrencyID,
                        "supplierDefaultER" => $checkPOMaster->supplierDefaultER,
                        "supplierTransactionCurrencyID" => $checkPOMaster->supplierTransactionCurrencyID,
                        "supplierTransactionER" => $checkPOMaster->supplierTransactionER,
                        "poConfirmedYN" => $checkPOMaster->poConfirmedYN,
                        "poConfirmedByEmpSystemID" => $checkPOMaster->poConfirmedByEmpSystemID,
                        "poConfirmedByEmpID" => $checkPOMaster->poConfirmedByEmpID,
                        "poConfirmedByName" => $checkPOMaster->poConfirmedByName,
                        "poConfirmedDate" => $checkPOMaster->poConfirmedDate,
                        "poCancelledYN" => $checkPOMaster->poCancelledYN,
                        "poCancelledBy" => $checkPOMaster->poCancelledBy,
                        "poCancelledByName" => $checkPOMaster->poCancelledByName,
                        "poCancelledDate" => $checkPOMaster->poCancelledDate,
                        "cancelledComments" => $checkPOMaster->cancelledComments,
                        "poTotalComRptCurrency" => $checkPOMaster->poTotalComRptCurrency,
                        "poTotalLocalCurrency" => $checkPOMaster->poTotalLocalCurrency,
                        "poTotalSupplierDefaultCurrency" => $checkPOMaster->poTotalSupplierDefaultCurrency,
                        "poTotalSupplierTransactionCurrency" => $checkPOMaster->poTotalSupplierTransactionCurrency,
                        "poDiscountPercentage" => 0,
                        "poDiscountAmount" => 0,
                        "shipTocontactPersonID" => $checkPOMaster->shipTocontactPersonID,
                        "shipTocontactPersonTelephone" => $checkPOMaster->shipTocontactPersonTelephone,
                        "shipTocontactPersonFaxNo" => $checkPOMaster->shipTocontactPersonFaxNo,
                        "shipTocontactPersonEmail" => $checkPOMaster->shipTocontactPersonEmail,
                        "invoiceTocontactPersonID" => $checkPOMaster->invoiceTocontactPersonID,
                        "invoiceTocontactPersonTelephone" => $checkPOMaster->invoiceTocontactPersonTelephone,
                        "invoiceTocontactPersonFaxNo" => $checkPOMaster->invoiceTocontactPersonFaxNo,
                        "invoiceTocontactPersonEmail" => $checkPOMaster->invoiceTocontactPersonEmail,
                        "soldTocontactPersonID" => $checkPOMaster->soldTocontactPersonID,
                        "soldTocontactPersonTelephone" => $checkPOMaster->soldTocontactPersonTelephone,
                        "soldTocontactPersonFaxNo" => $checkPOMaster->soldTocontactPersonFaxNo,
                        "soldTocontactPersonEmail" => $checkPOMaster->soldTocontactPersonEmail,
                        "priority" => $checkPOMaster->priority,
                        "approved" => $checkPOMaster->approved,
                        "approvedDate" => $checkPOMaster->approvedDate,
                        "addOnPercent" => $checkPOMaster->addOnPercent,
                        "addOnDefaultPercent" => $checkPOMaster->addOnDefaultPercent,
                        "GRVTrackingID" => $checkPOMaster->GRVTrackingID,
                        "logisticDoneYN" => $checkPOMaster->logisticDoneYN,
                        "poClosedYN" => $checkPOMaster->poClosedYN,
                        "grvRecieved" => $checkPOMaster->grvRecieved,
                        "invoicedBooked" => $checkPOMaster->invoicedBooked,
                        "timesReferred" => $checkPOMaster->timesReferred,
                        "poType" => "Standard",
                        "poType_N" => 6,
                        "docRefNo" => $checkPOMaster->docRefNo,
                        "RollLevForApp_curr" => 1,
                        "sentToSupplier" => $checkPOMaster->sentToSupplier,
                        "sentToSupplierByEmpSystemID" => $checkPOMaster->sentToSupplierByEmpSystemID,
                        "sentToSupplierByEmpID" => $checkPOMaster->sentToSupplierByEmpID,
                        "sentToSupplierByEmpName" => $checkPOMaster->sentToSupplierByEmpName,
                        "sentToSupplierDate" => $checkPOMaster->sentToSupplierDate,
                        "budgetBlockYN" => $checkPOMaster->budgetBlockYN,
                        "hidePOYN" => $checkPOMaster->hidePOYN,
                        "hideByEmpID" => $checkPOMaster->hideByEmpID,
                        "hideByEmpName" => $checkPOMaster->hideByEmpName,
                        "hideDate" => $checkPOMaster->hideDate,
                        "hideComments" => $checkPOMaster->hideComments,
                        "WO_purchaseOrderID" => $checkPOMaster->purchaseOrderID,
                        "WO_PeriodFrom" => $checkPOMaster->WO_PeriodFrom,
                        "WO_PeriodTo" => $checkPOMaster->WO_PeriodTo,
                        "WO_NoOfAutoGenerationTimes" => $checkPOMaster->WO_NoOfAutoGenerationTimes,
                        "WO_NoOfGeneratedTimes" => $currentGeneratedTimes,
                        "WO_fullyGenerated" => $checkPOMaster->WO_fullyGenerated,
                        "WO_confirmedYN" => 1,
                        "WO_confirmedDate" => $checkPOMaster->WO_confirmedDate,
                        "WO_confirmedByEmpID" => $checkPOMaster->WO_confirmedByEmpID,
                        "partiallyGRVAllowed" => $checkPOMaster->partiallyGRVAllowed,
                        "logisticsAvailable" => $checkPOMaster->logisticsAvailable,
                        "createdUserGroup" => $checkPOMaster->createdUserGroup,
                        "createdPcID" => $checkPOMaster->createdPcID,
                        "createdUserSystemID" => $checkPOMaster->createdUserSystemID,
                        "createdUserID" => $checkPOMaster->createdUserID,
                        "modifiedPc" => $checkPOMaster->modifiedPc,
                        "modifiedUserSystemID" => $checkPOMaster->modifiedUserSystemID,
                        "modifiedUser" => $checkPOMaster->modifiedUser,
                        "supplierVATEligible" => $checkPOMaster->supplierVATEligible,
                        "VATPercentage" => $checkPOMaster->VATPercentage,
                        "vatRegisteredYN" => $checkPOMaster->vatRegisteredYN,
                        "createdDateTime" => Carbon::now(),
                        "timeStamp" => Carbon::now(),
                        "vat_number" => $checkPOMaster->vat_number,
                    ];

                    $poMasterDataRes = ProcumentOrder::create($poMasterData);

                    $checkPOMaster->WO_NoOfGeneratedTimes = $currentGeneratedTimes;

                    if ($checkPOMaster->WO_NoOfAutoGenerationTimes == $currentGeneratedTimes) {
                        $checkPOMaster->WO_fullyGenerated = -1;
                    } else {
                        $checkPOMaster->WO_fullyGenerated = 0;
                    }

                    $checkPOMaster->timeStamp = Carbon::now();

                    $checkPOMaster->save();

                    $updateNewWOMasterDetails = ProcumentOrder::find($poMasterDataRes->purchaseOrderID);

                    $totalTransAmount = 0;
                    $totalLocalAmount = 0;
                    $totalRptAmount = 0;


                    foreach ($checkPOMaster->detail as $key => $checkWODetail) {
                        $previousSubWorkOrderSummary = PurchaseOrderDetails::selectRaw('companyID, WO_purchaseOrderMasterID, WP_purchaseOrderDetailsID, itemCode, SUM(noQty) as SumOfnoQty, SUM(netAmount) as SumOfnetAmount, Sum( GRVcostPerUnitLocalCur * noQty ) AS LocalCur, Sum( GRVcostPerUnitSupDefaultCur * noQty ) AS DefCur, Sum( GRVcostPerUnitSupTransCur * noQty ) AS transCur, Sum( GRVcostPerUnitComRptCur * noQty ) AS RptCur')
                            ->where('WO_purchaseOrderMasterID', $checkPOMaster->purchaseOrderID)
                            ->where('WP_purchaseOrderDetailsID', $checkWODetail->purchaseOrderDetailsID)
                            ->where('itemCode', $checkWODetail->itemCode)
                            ->where('WO_purchaseOrderMasterID', '>', 0)
                            ->groupBy('companyID', 'WO_purchaseOrderMasterID', 'WP_purchaseOrderDetailsID', 'itemCode')
                            ->first();
                        if (!$previousSubWorkOrderSummary) {
                            $sumOfQtyOfPreviousSubWorkOrder = 0;
                            $sumOfNetAmountOfPreviousSubWorkOrder = 0;

                            $subWorkOrderQty = $checkWODetail->noQty / $noOfAutoGenerationTimes;
                            $subWorkOrderUnitCost = $checkWODetail->unitCost;
                            $subWordkOrderNetAmount = $checkWODetail->netAmount / $noOfAutoGenerationTimes;
                            $subWordkOrderLocalCurrency = $checkWODetail->GRVcostPerUnitLocalCur;
                            $subWordkOrderDefCurrency = $checkWODetail->GRVcostPerUnitSupDefaultCur;
                            $subWordkOrderTransCurrency = $checkWODetail->GRVcostPerUnitSupTransCur;
                            $subWordkOrderRptCurrency = $checkWODetail->GRVcostPerUnitComRptCur;
                        } else {

                            if ($previousSubWorkOrderSummary->SumOfnoQty > $checkWODetail->noQty) {
                                $subWorkOrderQty = 0;
                                $subWorkOrderUnitCost = 0;
                                $subWordkOrderNetAmount = 0;
                                $subWordkOrderLocalCurrency = 0;
                                $subWordkOrderDefCurrency = 0;
                                $subWordkOrderTransCurrency = 0;
                                $subWordkOrderRptCurrency = 0;
                            } else {
                                $sumOfQtyOfPreviousSubWorkOrder = $previousSubWorkOrderSummary->SumOfnoQty;
                                $sumOfNetAmountOfPreviousSubWorkOrder = $previousSubWorkOrderSummary->SumOfnetAmount;

                                $subWorkOrderQty = round((($checkWODetail->noQty - $sumOfQtyOfPreviousSubWorkOrder) / (($noOfAutoGenerationTimes - $currentGeneratedTimes) + 1)), 9);

                                if (($checkWODetail->noQty - $sumOfQtyOfPreviousSubWorkOrder) == 0) {
                                    $subWorkOrderUnitCost = 0;
                                    $subWordkOrderNetAmount = 0;
                                    $subWordkOrderLocalCurrency = 0;
                                    $subWordkOrderDefCurrency = 0;
                                    $subWordkOrderTransCurrency = 0;
                                    $subWordkOrderRptCurrency = 0;
                                } else {
                                    $subWorkOrderUnitCost = $checkWODetail->unitCost; //($checkWODetail->netAmount - $sumOfNetAmountOfPreviousSubWorkOrder) / ($checkWODetail->noQty - $sumOfQtyOfPreviousSubWorkOrder);
                                    $subWordkOrderNetAmount = (($checkWODetail->netAmount - $sumOfNetAmountOfPreviousSubWorkOrder) / ($checkWODetail->noQty - $sumOfQtyOfPreviousSubWorkOrder)) * $subWorkOrderQty;
                                    $subWordkOrderLocalCurrency = ((($checkWODetail->GRVcostPerUnitLocalCur * $checkWODetail->noQty) - $previousSubWorkOrderSummary->LocalCur) / ($checkWODetail->noQty - $sumOfQtyOfPreviousSubWorkOrder));
                                    $subWordkOrderDefCurrency = ((($checkWODetail->GRVcostPerUnitSupDefaultCur * $checkWODetail->noQty) - $previousSubWorkOrderSummary->DefCur) / ($checkWODetail->noQty - $sumOfQtyOfPreviousSubWorkOrder));
                                    $subWordkOrderTransCurrency = ((($checkWODetail->GRVcostPerUnitSupTransCur * $checkWODetail->noQty) - $previousSubWorkOrderSummary->transCur) / ($checkWODetail->noQty - $sumOfQtyOfPreviousSubWorkOrder));
                                    $subWordkOrderRptCurrency = ((($checkWODetail->GRVcostPerUnitComRptCur * $checkWODetail->noQty) - $previousSubWorkOrderSummary->RptCur) / ($checkWODetail->noQty - $sumOfQtyOfPreviousSubWorkOrder));
                                }
                            }
                        }

                        $addNewSubWorkOrderDetail = [
                            "companySystemID" => $checkWODetail->companySystemID,
                            "companyID" => $checkWODetail->companyID,
                            "departmentID" => $checkWODetail->departmentID,
                            "serviceLineSystemID" => $checkWODetail->serviceLineSystemID,
                            "serviceLineCode" => $checkWODetail->serviceLineCode,
                            "purchaseOrderMasterID" => $poMasterDataRes->purchaseOrderID,
                            "POProcessMasterID" => $checkWODetail->POProcessMasterID,
                            "WO_purchaseOrderMasterID" => $checkWODetail->purchaseOrderMasterID,
                            "WP_purchaseOrderDetailsID" => $checkWODetail->purchaseOrderDetailsID,
                            "purchaseProcessDetailID" => $checkWODetail->purchaseProcessDetailID,
                            "purchaseRequestDetailsID" => $checkWODetail->purchaseRequestDetailsID,
                            "purchaseRequestID" => $checkWODetail->purchaseRequestID,
                            "itemCode" => $checkWODetail->itemCode,
                            "itemPrimaryCode" => $checkWODetail->itemPrimaryCode,
                            "itemDescription" => $checkWODetail->itemDescription,
                            "itemFinanceCategoryID" => $checkWODetail->itemFinanceCategoryID,
                            "itemFinanceCategorySubID" => $checkWODetail->itemFinanceCategorySubID,
                            "financeGLcodebBSSystemID" => $checkWODetail->financeGLcodebBSSystemID,
                            "financeGLcodebBS" => $checkWODetail->financeGLcodebBS,
                            "financeGLcodePLSystemID" => $checkWODetail->financeGLcodePLSystemID,
                            "financeGLcodePL" => $checkWODetail->financeGLcodePL,
                            "includePLForGRVYN" => $checkWODetail->includePLForGRVYN,
                            "supplierPartNumber" => $checkWODetail->supplierPartNumber,
                            "unitOfMeasure" => $checkWODetail->unitOfMeasure,
                            "noQty" => $subWorkOrderQty,
                            "noOfDays" => $dhDaysInMonth,
                            "unitCost" => $subWorkOrderUnitCost,
                            "discountPercentage" => $checkWODetail->discountPercentage,
                            "discountAmount" => $checkWODetail->discountAmount,
                            "netAmount" => $subWordkOrderNetAmount,
                            "budgetYear" => $checkWODetail->budgetYear,
                            "prBelongsYear" => $checkWODetail->prBelongsYear,
                            "isAccrued" => $checkWODetail->isAccrued,
                            "budjetAmtLocal" => $checkWODetail->budjetAmtLocal,
                            "budjetAmtRpt" => $checkWODetail->budjetAmtRpt,
                            "comment" => $checkWODetail->comment,
                            "supplierDefaultCurrencyID" => $checkWODetail->supplierDefaultCurrencyID,
                            "supplierDefaultER" => $checkWODetail->supplierDefaultER,
                            "supplierItemCurrencyID" => $checkWODetail->supplierItemCurrencyID,
                            "foreignToLocalER" => $checkWODetail->foreignToLocalER,
                            "companyReportingCurrencyID" => $checkWODetail->companyReportingCurrencyID,
                            "companyReportingER" => $checkWODetail->companyReportingER,
                            "localCurrencyID" => $checkWODetail->localCurrencyID,
                            "localCurrencyER" => $checkWODetail->localCurrencyER,
                            "addonDistCost" => $checkWODetail->addonDistCost,
                            "GRVcostPerUnitLocalCur" => $subWordkOrderLocalCurrency,
                            "GRVcostPerUnitSupDefaultCur" => $subWordkOrderDefCurrency,
                            "GRVcostPerUnitSupTransCur" => $subWordkOrderTransCurrency,
                            "GRVcostPerUnitComRptCur" => $subWordkOrderRptCurrency,
                            "addonPurchaseReturnCost" => $checkWODetail->addonPurchaseReturnCost,
                            "purchaseRetcostPerUnitLocalCur" => $subWordkOrderLocalCurrency,
                            "purchaseRetcostPerUniSupDefaultCur" => $subWordkOrderDefCurrency,
                            "purchaseRetcostPerUnitTranCur" => $subWordkOrderTransCurrency,
                            "purchaseRetcostPerUnitRptCur" => $subWordkOrderRptCurrency,
                            "GRVSelectedYN" => $checkWODetail->GRVSelectedYN,
                            "goodsRecievedYN" => $checkWODetail->goodsRecievedYN,
                            "logisticSelectedYN" => $checkWODetail->logisticSelectedYN,
                            "logisticRecievedYN" => $checkWODetail->logisticRecievedYN,
                            "isAccruedYN" => $checkWODetail->isAccruedYN,
                            "accrualJVID" => $checkWODetail->accrualJVID,
                            "timesReferred" => $checkWODetail->timesReferred,
                            "createdUserGroup" => $checkWODetail->createdUserGroup,
                            "createdPcID" => $checkWODetail->createdPcID,
                            "createdUserID" => $checkWODetail->createdUserID,
                            "modifiedPc" => $checkWODetail->modifiedPc,
                            "modifiedUser" => $checkWODetail->modifiedUser,
                            "VATPercentage" => $checkWODetail->VATPercentage,
                            "VATAmount" => $checkWODetail->VATAmount,
                            "VATAmountLocal" => $checkWODetail->VATAmountLocal,
                            "VATAmountRpt" => $checkWODetail->VATAmountRpt,
                            "VATApplicableOn" => $checkWODetail->VATApplicableOn,
                            "createdDateTime" => Carbon::now(),
                            "timeStamp" => Carbon::now()
                        ];

                        $purchaseOrderDetailCreateRes = PurchaseOrderDetails::create($addNewSubWorkOrderDetail);

                        $totalTransAmount = $totalTransAmount + ($subWorkOrderQty * $subWordkOrderTransCurrency);
                        $totalLocalAmount = $totalLocalAmount + ($subWorkOrderQty * $subWordkOrderLocalCurrency);
                        $totalRptAmount = $totalRptAmount + ($subWorkOrderQty * $subWordkOrderRptCurrency);
                    }

                    $updateNewWOMasterDetails->poTotalComRptCurrency = $totalRptAmount + ($checkPOMaster->VATAmountRpt / $noOfAutoGenerationTimes);
                    $updateNewWOMasterDetails->poTotalLocalCurrency = $totalLocalAmount + ($checkPOMaster->VATAmountLocal / $noOfAutoGenerationTimes);
                    $updateNewWOMasterDetails->poTotalSupplierDefaultCurrency = $totalTransAmount + ($checkPOMaster->VATAmount / $noOfAutoGenerationTimes);
                    $updateNewWOMasterDetails->poTotalSupplierTransactionCurrency = $totalTransAmount + ($checkPOMaster->VATAmount / $noOfAutoGenerationTimes);
                    $updateNewWOMasterDetails->VATAmount = $checkPOMaster->VATAmount / $noOfAutoGenerationTimes;
                    $updateNewWOMasterDetails->VATAmountLocal = $checkPOMaster->VATAmountLocal / $noOfAutoGenerationTimes;
                    $updateNewWOMasterDetails->VATAmountRpt = $checkPOMaster->VATAmountRpt / $noOfAutoGenerationTimes;
                    $updateNewWOMasterDetails->poDiscountPercentage = $checkPOMaster->poDiscountPercentage;
                    $updateNewWOMasterDetails->poDiscountAmount = $checkPOMaster->poDiscountAmount / $noOfAutoGenerationTimes;
                    $updateNewWOMasterDetails->timeStamp = Carbon::now();

                    $updateNewWOMasterDetails->save();
                }
            }
            DB::commit();
            return $this->sendResponse([], 'Work order generated successfully');
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage() . $exception->getLine());
        }
    }

    public function workOrderLog(Request $request)
    {
        $input = $request->all();

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $search = $request->input('search.value');

        $logData = WorkOrderGenerationLog::with(['generated_by']);

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $logData = $logData->where(function ($query) use ($search) {
                $query->where('date', 'LIKE', "%{$search}%")
                    ->orWhereHas('generated_by', function ($query) use ($search) {
                        $query->where('empName', 'LIKE', "%{$search}%")
                            ->where('empID', 'LIKE', "%{$search}%");
                    });
            });
        }

        return \DataTables::eloquent($logData)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('id', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }


    public function getDocumentTracingData(Request $request)
    {
        try{

            $input = $request->all();
            if (!isset($input['documentSystemID'])) {
                return $this->sendError("Document System ID not found");
            }

            $tracingData = [];
            switch ($input['documentSystemID']) {
                case 2:
                case 5:
                case 52:
                    $tracingData[] = $this->getPurchaseOrderTracingData($input['id']);
                    break;
                case 3:
                    $tracingData[] = $this->getGrvTracingData($input['id']);
                    break;
                case 11:
                    $tracingData[] = $this->getSupplierInvoiceTracingData($input['id']);
                    break;
                case 1:
                    $tracingData[][] = $this->getPurchaseRequestTracingData($input['id'], 'pr');
                    break;
                case 9:
                    $tracingData[][] = $this->getMaterialRequestTracingData($input['id']);
                    break;
                case 67:
                    $tracingData[][] = $this->getQuotationTracingData($input['id'], $input['documentSystemID']);
                    break;
                case 68:
                    $tracingData[] = $this->getSalesOrderTracingData($input['id']);
                    break;
                case 71:
                    $tracingData[] = $this->getDeliveryOrderTracingData($input['id']);
                    break;
                case 20:
                    $tracingData[] = $this->getCustomerInvoiceTracingData($input['id']);
                    break;
                case 21:
                    $tracingData = $this->getReciptVoucherTracingData($input['id']);
                    break;
                case 87:
                    $tracingData[] = $this->getSalesReturnTracingData($input['id']);
                    break;
                case 70:
                    $tracingData[] = $this->getReciptMatchingTracingData($input['id']);
                    break;
                case 19:
                    $tracingData[] = $this->getCreditNoteTracingData($input['id']);
                    break;
                case 4:
                    $tracingData[] = $this->getPaymentVoucherTracingData($input['id']);
                    break;
                case 15:
                    $tracingData[] = $this->getDebitNoteTracingData($input['id']);
                    break;
                case 103:
                    $tracingData[] = $this->getAssetTransferTracingData($input['id']);
                    break;
                default:
                    # code...
                    break;
            }

            return $this->sendResponse($tracingData, 'Document tracing data retrived successfully');

        }  catch (\Exception $exception) {
            return $this->sendError($exception->getMessage(), 500);
        }
    }


    public function getMaterialRequestTracingData($RequestID)
    {
        $tracingData = [];
        $materialRequest = MaterielRequest::where('RequestID', $RequestID)
            ->first();

        $issues = ItemIssueMaster::where('reqDocID', $RequestID)
            ->get();

        $issueArray = $issues->toArray();

        $tracingData['name'] = "Material Request";
        $tracingData['cssClass'] = "ngx-org-step-one root-tracing-node";
        $tracingData['documentSystemID'] = $materialRequest->documentSystemID;
        $tracingData['docAutoID'] = $materialRequest->RequestID;
        $tracingData['title'] = "{Doc Code :} " . $materialRequest->RequestCode . " -- {Doc Date :} " . Carbon::parse($materialRequest->RequestedDate)->format('Y-m-d') . " -- {Currency :} - -- {Amount :} -";

        foreach ($issueArray as $key2 => $value2) {
            $temp2 = [];
            $temp2['name'] = "Material Issue";
            $temp2['cssClass'] = "ngx-org-step-two";
            $temp2['documentSystemID'] = $value2['documentSystemID'];
            $temp2['docAutoID'] = $value2['itemIssueAutoID'];
            $temp2['title'] = "{Doc Code :} " . $value2['itemIssueCode'] . " -- {Doc Date :} " . Carbon::parse($value2['issueDate'])->format('Y-m-d') . " -- {Currency :} - -- {Amount :} -";

            $tracingData['childs'][] = $temp2;
        }

        return $tracingData;
    }

    public function getPaymentVoucherTracingData($PayMasterAutoId, $type = 'pv', $debitNoteID = null)
    {
        $paymentVocherData = PaySupplierInvoiceMaster::find($PayMasterAutoId);

        if ($paymentVocherData->invoiceType == 2  || $paymentVocherData->invoiceType == 6) {
            $PayMasterAutoIdArray = (is_array($PayMasterAutoId)) ? $PayMasterAutoId : [$PayMasterAutoId];
            $prDetails = PaySupplierInvoiceDetail::whereIn('PayMasterAutoId', $PayMasterAutoIdArray)
                ->where('addedDocumentSystemID', 11)
                ->groupBy('bookingInvSystemCode')
                ->get();

            $supplierInvoiceIds = $prDetails->pluck('bookingInvSystemCode')->toArray();
            return $this->getSupplierInvoiceTracingData($supplierInvoiceIds, $type, $PayMasterAutoId, $debitNoteID);
        } else if ($paymentVocherData->invoiceType == 3) {
            // return [];
            $type = 'PV';
            $paymount_vaoucher = PaySupplierInvoiceMaster::with('transactioncurrency')->find($PayMasterAutoId);

            if ($type == 'PV') {
                $temp2['cssClass'] = "ngx-org-step-three root-tracing-node";
            } else {
                $temp2['cssClass'] = "ngx-org-step-five";
            }

            $cancelStatus = ($paymount_vaoucher->cancelYN == -1) ? " -- @Cancelled@" : "";
            $temp2['name'] = "Payment Voucher";
            $temp2['documentSystemID'] = $paymount_vaoucher->documentSystemID;
            $temp2['docAutoID'] = $paymount_vaoucher->PayMasterAutoId;
            $temp2['title'] = "{Doc Code :} " . $paymount_vaoucher->BPVcode . " -- {Doc Date :} " . Carbon::parse($paymount_vaoucher->BPVdate)->format('Y-m-d') . " -- {Currency :} " . $paymount_vaoucher->transactioncurrency->CurrencyCode . " -- {Amount :} " . number_format($paymount_vaoucher->payAmountSuppTrans, $paymount_vaoucher->transactioncurrency->DecimalPlaces) . $cancelStatus;

            $tracingData[] = $temp2;
            return $tracingData;
        } else if ($paymentVocherData->invoiceType == 5) {
            $PayMasterAutoIdArray = (is_array($PayMasterAutoId)) ? $PayMasterAutoId : [$PayMasterAutoId];
            $prDetails = AdvancePaymentDetails::whereIn('PayMasterAutoId', $PayMasterAutoIdArray)
                ->groupBy('purchaseOrderID')
                ->get();

            $poIds = $prDetails->pluck('purchaseOrderID')->toArray();

            return $this->getPurchaseOrderTracingData($poIds, $type, null, null, $PayMasterAutoId, $debitNoteID);
        } else if ($paymentVocherData->invoiceType == 7) {
            $PayMasterAutoIdArray = (is_array($PayMasterAutoId)) ? $PayMasterAutoId : [$PayMasterAutoId];
            $prDetails = AdvancePaymentDetails::whereIn('PayMasterAutoId', $PayMasterAutoIdArray)
                ->groupBy('purchaseOrderID')
                ->get();

            $poIds = $prDetails->pluck('purchaseOrderID')->toArray();

            return $this->getPurchaseOrderTracingData($poIds, $type, null, null, $PayMasterAutoId, $debitNoteID);
        }
    }

    public function getDebitNoteTracingData($debitNoteID, $type = 'debit')
    {
        $matchDocMasters = MatchDocumentMaster::where('documentSystemID', 15)
            ->where('PayMasterAutoId', $debitNoteID)
            ->get();

        $matchMasterIds = collect($matchDocMasters)->pluck('matchDocumentMasterAutoID');


        $debitNotes = PaySupplierInvoiceDetail::where('addedDocumentType', 4)
            ->where('matchingDocID', 0)
            ->where('bookingInvSystemCode', $debitNoteID)
            ->groupBy('PayMasterAutoId')
            ->get();


        $paymentVoucherIds = collect($debitNotes)->pluck('PayMasterAutoId');




        if (sizeof($matchMasterIds) > 0 || sizeof($paymentVoucherIds) > 0) {
            $finalData = [];

            if (sizeof($matchMasterIds) > 0) {
                $supplierInvoiceDetails = PaySupplierInvoiceDetail::whereIn('matchingDocID', $matchMasterIds)
                    ->where('matchingDocID', '>', 0)
                    ->groupBy('PayMasterAutoId')
                    ->get();

                $supplierInvoiceIds = collect($supplierInvoiceDetails)->pluck('bookingInvSystemCode')->toArray();

                $finalData[] = $this->getSupplierInvoiceTracingData($supplierInvoiceIds, $type, null, $debitNoteID);
            }


            if (sizeof($paymentVoucherIds) > 0) {
                foreach ($paymentVoucherIds as $key => $value) {
                    $finalData[] = $this->getPaymentVoucherTracingData($value, $type, $debitNoteID);
                }
            }


            $finalResData = [];
            foreach ($finalData as $key => $value) {
                foreach ($value as $key1 => $value1) {
                    $finalResData[] = $value1;
                }
            }

            return $finalResData;
        } else {
            $debitNoteData = DebitNote::with(['transactioncurrency'])->find($debitNoteID);

            $tracingData['name'] = "Debit Note";
            $tracingData['cssClass'] = "ngx-org-step-one root-tracing-node";
            $tracingData['documentSystemID'] = $debitNoteData->documentSystemID;
            $tracingData['docAutoID'] = $debitNoteData->debitNoteAutoID;
            $tracingData['title'] = "{Doc Code :} " . $debitNoteData->debitNoteCode . " -- {Doc Date :} " . Carbon::parse($debitNoteData->debitNoteDate)->format('Y-m-d') . " -- {Currency :} " . $debitNoteData->transactioncurrency->CurrencyCode . "-- {Amount :} " . number_format($debitNoteData->debitAmountTrans, $debitNoteData->transactioncurrency->DecimalPlaces);

            return [$tracingData];
        }
    }

    public function getPurchaseRequestTracingData($purchaseRequestID, $type, $purchaseOrderID = null, $grvAutoID = null, $bookingSuppMasInvAutoID = null, $PayMasterAutoId = null, $debitNoteID = null)
    {
        $tracingData = [];
        $purchaseRequest = PurchaseRequest::where('purchaseRequestID', $purchaseRequestID)
            ->with(['currency_by'])
            ->first();

        $poMasters = PurchaseOrderDetails::selectRaw('sum(netAmount) as totalAmount,
                                        purchaseRequestID,purchaseOrderMasterID')
            ->where('purchaseRequestID', $purchaseRequest->purchaseRequestID)
            ->with(['order' => function ($query) {
                $query->with(['currency']);
            }])
            ->groupBy('purchaseOrderMasterID');

        if (!is_null($purchaseOrderID)) {
            if (is_array($purchaseOrderID)) {
                $poMasters = $poMasters->whereIn('purchaseOrderMasterID', $purchaseOrderID);
            } else {
                $poMasters = $poMasters->where('purchaseOrderMasterID', $purchaseOrderID);
            }
        }

        $poMasters = $poMasters->get();
        $totalPo = 0;
        foreach ($poMasters as $po) {
            if(isset($po->order))
            {
                $totalPo+=$po->order->poTotalSupplierTransactionCurrency;
            }
            $po->grv = $this->getPOtoPaymentChainForTracing($po->order, $grvAutoID, $bookingSuppMasInvAutoID, $type, $debitNoteID);
        }
        $poData = $poMasters->toArray();

        $cancelStatus = ($purchaseRequest->cancelledYN == -1) ? " -- @Cancelled@" : "";
        $tracingData['name'] = "Purchase Request";
        if ($type == 'pr' && ($purchaseRequest->purchaseRequestID == $purchaseRequestID)) {
            $tracingData['cssClass'] = "ngx-org-step-one root-tracing-node";
        } else {
            $tracingData['cssClass'] = "ngx-org-step-one";
        }
        $tracingData['documentSystemID'] = $purchaseRequest->documentSystemID;
        $tracingData['docAutoID'] = $purchaseRequest->purchaseRequestID;
        $tracingData['title'] = "{Doc Code :} " . $purchaseRequest->purchaseRequestCode . " -- {Doc Date :} " . Carbon::parse($purchaseRequest->PRRequestedDate)->format('Y-m-d') . " -- {Currency :} " . $purchaseRequest->currency_by ? $purchaseRequest->currency_by->CurrencyCode : "" . "-- {Amount :} " . number_format($purchaseRequest->poTotalSupplierTransactionCurrency, $purchaseRequest->currency_by ? $purchaseRequest->currency_by->DecimalPlaces : 2) . $cancelStatus;


        foreach ($poData as $keyPo => $valuePo) {
            // if($valuePo['purchaseOrderMasterID'] == 1165)
            // {
            //     continue;
            // }
            $cancelStatus = ($valuePo['order']['poCancelledYN'] == -1) ? " -- @Cancelled@" : "";
            $tempPo = [];
            $tempPo['name'] = "Purchase Order";
            if ($type == 'po' && ($valuePo['order']['purchaseOrderID'] == $purchaseOrderID)) {
                $tempPo['cssClass'] = "ngx-org-step-two root-tracing-node";
            } else {
                $tempPo['cssClass'] = "ngx-org-step-two";
            }

            $tempPo['documentSystemID'] = $valuePo['order']['documentSystemID'];
            $tempPo['docAutoID'] = $valuePo['order']['purchaseOrderID'];
            $tempPo['title'] = "{Doc Code :} " . $valuePo['order']['purchaseOrderCode'] . " -- {Doc Date :} " . Carbon::parse($valuePo['order']['expectedDeliveryDate'])->format('Y-m-d') . " -- {Currency :} " . $valuePo['order']['currency']['CurrencyCode'] . " -- {Document Amount :} " . number_format($valuePo['order']['poTotalSupplierTransactionCurrency'], $valuePo['order']['currency']['DecimalPlaces'])." -- {Total Amount :} " . number_format($totalPo, $valuePo['order']['currency']['DecimalPlaces']) . $cancelStatus;

            $grvTototal = 0;

            foreach ($valuePo['grv'] as $key => $value) {
                $cancelStatus = ($value['grv_master']['grvCancelledYN'] == -1) ? " -- @Cancelled@" : "";

                $temp = [];
                $temp['name'] = "Good Receipt Voucher";
                if ($type == 'grv' && ($value['grv_master']['grvAutoID'] == $grvAutoID)) {
                    $temp['cssClass'] = "ngx-org-step-three root-tracing-node";
                } else {
                    $temp['cssClass'] = "ngx-org-step-three";
                }
                $detailTotalAmount = 0;
                foreach($value['grv_master']['details'] as $detail)
                {
                    if($valuePo['purchaseOrderMasterID'] == $detail['purchaseOrderMastertID'])
                    {
                        $detailTotalAmount+=$detail['netAmount'];
                    }

                }

                $temp['documentSystemID'] = $value['grv_master']['documentSystemID'];
                $temp['docAutoID'] = $value['grv_master']['grvAutoID'];
                $temp['title'] = "{Doc Code :} " . $value['grv_master']['grvPrimaryCode'] . " -- {Doc Date :} " . Carbon::parse($value['grv_master']['grvDate'])->format('Y-m-d') . " -- {Currency :} " . $value['grv_master']['currency_by']['CurrencyCode'] . " -- {Document Amount :} " . number_format($detailTotalAmount, $value['grv_master']['currency_by']['DecimalPlaces']) ." -- {Total Amount  :} " . number_format($value['grv_master']['grvTotalSupplierTransactionCurrency'], $value['grv_master']['currency_by']['DecimalPlaces']) . $cancelStatus;

                foreach ($value['invoices'] as $key1 => $value1) {

                    $cancelStatus = ($value1['suppinvmaster']['cancelYN'] == -1) ? " -- @Cancelled@" : "";
                    $temp1 = [];
                    $temp1['name'] = "Supplier Invoice";
                    if ($type == 'supInv' && ($value1['suppinvmaster']['bookingSuppMasInvAutoID'] == $bookingSuppMasInvAutoID)) {
                        $temp1['cssClass'] = "ngx-org-step-four root-tracing-node";
                    } else {
                        $temp1['cssClass'] = "ngx-org-step-four";
                    }
                    $detailInvoiceTotalAmount = 0;
                    foreach($value1['suppinvmaster']['detail'] as $detail)
                    {
                        if($value['grvAutoID'] == $detail['grvAutoID'])
                        {
                            $detailInvoiceTotalAmount+=$detail['totLocalAmount'];

                        }
                    }
                    $temp1['documentSystemID'] = $value1['suppinvmaster']['documentSystemID'];
                    $temp1['docAutoID'] = $value1['suppinvmaster']['bookingSuppMasInvAutoID'];
                    $temp1['title'] = "{Doc Code :} " . $value1['suppinvmaster']['bookingInvCode'] . " -- {Doc Date :} " . Carbon::parse($value1['suppinvmaster']['bookingDate'])->format('Y-m-d') . " -- {Currency :} " . $value1['suppinvmaster']['transactioncurrency']['CurrencyCode'] . " -- {Document Amount :} " . number_format($detailInvoiceTotalAmount, $value1['suppinvmaster']['transactioncurrency']['DecimalPlaces']) . " -- {Total Amount  :} " . number_format($value1['suppinvmaster']['bookingAmountLocal'], $value1['suppinvmaster']['transactioncurrency']['DecimalPlaces']).$cancelStatus;


                    foreach ($value1['payments'] as $key2 => $value2) {
                        $temp2 = [];
                        if (isset($value2['payment_master'])) {
                            if ($type == 'pv' && $PayMasterAutoId == $value2['payment_master']['PayMasterAutoId']) {
                                $temp2['cssClass'] = "ngx-org-step-five root-tracing-node";
                            } else {
                                $temp2['cssClass'] = "ngx-org-step-five";
                            }
                            $cancelStatus = ($value2['payment_master']['cancelYN'] == -1) ? " -- @Cancelled@" : "";
                            $temp2['name'] = "Payment";
                            $temp2['documentSystemID'] = $value2['payment_master']['documentSystemID'];
                            $temp2['docAutoID'] = $value2['payment_master']['PayMasterAutoId'];
                            $temp2['title'] = "{Doc Code :} " . $value2['payment_master']['BPVcode'] . " -- {Doc Date :} " . Carbon::parse($value2['payment_master']['BPVdate'])->format('Y-m-d') . " -- {Currency :} " . $value2['payment_master']['transactioncurrency']['CurrencyCode'] . " -- {Amount :} " . number_format($value2['payment_master']['payAmountSuppTrans'], $value2['payment_master']['transactioncurrency']['DecimalPlaces']) . $cancelStatus;

                            $debitNotes = PaySupplierInvoiceDetail::selectRaw('sum(supplierDefaultAmount) as supplierDefaultAmount,bookingInvSystemCode,PayMasterAutoId,matchingDocID, bookingInvDocCode, bookingInvoiceDate')
                                ->where('PayMasterAutoId', $value2['payment_master']['PayMasterAutoId'])
                                ->where('addedDocumentType', 4)
                                ->where('matchingDocID', 0)
                                ->with(['payment_master' => function ($query) {
                                    $query->with(['transactioncurrency']);
                                }])
                                ->groupBy('bookingInvSystemCode')
                                ->get();


                            foreach ($debitNotes as $key => $value) {
                                $tempDeb = [];

                                if ($type == "debit" && ($debitNoteID == $value->bookingInvSystemCode)) {
                                    $tempDeb['cssClass'] = "ngx-org-step-six root-tracing-node";
                                } else {
                                    $tempDeb['cssClass'] = "ngx-org-step-six";
                                }
                                $tempDeb['name'] = "Debit Note";
                                $tempDeb['documentSystemID'] = 15;
                                $tempDeb['docAutoID'] = $value->bookingInvSystemCode;
                                $tempDeb['title'] = "{Doc Code :} " . $value->bookingInvDocCode . " -- {Doc Date :} " . Carbon::parse($value->bookingInvoiceDate)->format('Y-m-d') . " -- {Currency :} " . $value['payment_master']['transactioncurrency']['CurrencyCode'] . " -- {Amount :} " . number_format(ABS($value->supplierDefaultAmount), $value['payment_master']['transactioncurrency']['DecimalPlaces']);

                                $temp2['childs'][] = $tempDeb;
                            }
                        }

                        if (isset($value2['matching_master'])) {
                            if ($type == 'debit' && $debitNoteID == $value2['matching_master']['PayMasterAutoId']) {
                                $temp2['cssClass'] = "ngx-org-step-five root-tracing-node";
                            } else {
                                $temp2['cssClass'] = "ngx-org-step-five";
                            }
                            $temp2['name'] = "Debit Note";
                            $temp2['documentSystemID'] = $value2['matching_master']['documentSystemID'];
                            $temp2['docAutoID'] = $value2['matching_master']['PayMasterAutoId'];
                            $temp2['title'] = "{Doc Code :} " . $value2['matching_master']['BPVcode'] . " -- {Doc Date :} " . Carbon::parse($value2['matching_master']['BPVdate'])->format('Y-m-d') . " -- {Currency :} " . $value2['matching_master']['transactioncurrency']['CurrencyCode'] . " -- {Amount :} " . number_format($value2['matching_master']['payAmountSuppTrans'], $value2['matching_master']['transactioncurrency']['DecimalPlaces']);
                        }

                        $temp1['childs'][] = $temp2;
                    }

                    $temp['childs'][] = $temp1;
                }


                $returnes = PurchaseReturnDetails::selectRaw('sum(netAmountLocal) as localAmount,
                                                 sum(netAmountRpt) as rptAmount, sum(netAmount) as transAmount,grvAutoID,purhaseReturnAutoID')
                    ->where('grvAutoID', $value['grv_master']['grvAutoID'])
                    ->with(['master' => function ($query) {
                        $query->with(['currency_by']);
                    }])
                    ->groupBy('purhaseReturnAutoID')
                    ->get()
                    ->toArray();

                foreach ($returnes as $key1 => $value1) {
                    if (isset($value1['master'])) {
                        $temp1 = [];
                        $temp1['name'] = "Purchase Return";
                        $temp1['cssClass'] = "ngx-org-step-four";
                        $temp1['documentSystemID'] = $value1['master']['documentSystemID'];
                        $temp1['docAutoID'] = $value1['master']['purhaseReturnAutoID'];
                        $temp1['title'] = "{Doc Code :} " . $value1['master']['purchaseReturnCode'] . " -- {Doc Date :} " . Carbon::parse($value1['master']['purchaseReturnDate'])->format('Y-m-d') . " -- {Currency :} " . $value1['master']['currency_by']['CurrencyCode'] . " -- {Amount :} " . number_format($value1['transAmount'], $value1['master']['currency_by']['DecimalPlaces']);

                        $temp['childs'][] = $temp1;
                    }
                }

                $tempPo['childs'][] = $temp;
            }

            $tempAdPay = [];

            $advancePayments = AdvancePaymentDetails::selectRaw('PayMasterAutoId, supplierTransCurrencyID, SUM(supplierTransAmount) as transAmount')
                ->with(['pay_invoice', 'supplier_currency'])
                ->where('purchaseOrderID', $valuePo['order']['purchaseOrderID'])
                ->groupBy('purchaseOrderID');

            if (!is_null($PayMasterAutoId)) {
                $advancePayments = $advancePayments->where('PayMasterAutoId', $PayMasterAutoId);
            }

            $advancePayments = $advancePayments->get();

            foreach ($advancePayments as $keyAd => $valueAd) {
                $tempAdPay['name'] = "Payment Voucher";
                if ($type == 'pv' && ($valueAd->PayMasterAutoId == $PayMasterAutoId)) {
                    $tempAdPay['cssClass'] = "ngx-org-step-three root-tracing-node";
                } else {
                    $tempAdPay['cssClass'] = "ngx-org-step-three";
                }

                $tempAdPay['documentSystemID'] = $valueAd->pay_invoice->documentSystemID;
                $tempAdPay['docAutoID'] = $valueAd->pay_invoice->PayMasterAutoId;
                $tempAdPay['title'] = "{Doc Code :} " . $valueAd->pay_invoice->BPVcode . " -- {Doc Date :} " . Carbon::parse($valueAd->pay_invoice->BPVdate)->format('Y-m-d') . " -- {Currency :} " . $valueAd->supplier_currency->CurrencyCode . " -- {Amount :} " . number_format($valueAd->transAmount, $valueAd->supplier_currency->DecimalPlaces);

                $tempPo['childs'][] = $tempAdPay;
            }

            $tracingData['childs'][] = $tempPo;
        }

        return $tracingData;
    }

    public function getPurchaseOrderTracingData($purchaseOrderID, $type = 'po', $grvAutoID = null, $bookingSuppMasInvAutoID = null, $PayMasterAutoId = null, $debitNoteID = null)
    {
        $tracingData = [];


        if (!is_array($purchaseOrderID) || (is_array($purchaseOrderID) && sizeof($purchaseOrderID) == 1)) {
            $poID = is_array($purchaseOrderID) ? $purchaseOrderID[0] : $purchaseOrderID;

            $poSelectId = !is_array($purchaseOrderID) ? $purchaseOrderID : null;
            return $this->singlePoTracingData($poID, $type, $grvAutoID, $bookingSuppMasInvAutoID, $PayMasterAutoId, $debitNoteID, $poSelectId);
        } else {
            $poData = ProcumentOrder::whereIn('purchaseOrderID', $purchaseOrderID)
                ->get();

            $poTrData = [];
            foreach ($poData as $key => $value) {
                $poTrData[] = $this->singlePoTracingData($value->purchaseOrderID, $type, $grvAutoID, $bookingSuppMasInvAutoID, $PayMasterAutoId, $debitNoteID);
            }

            foreach ($poTrData as $key => $value) {
                foreach ($value as $key1 => $value1) {
                    $tracingData[] = $value1;
                }
            }

            return $tracingData;
        }
    }

    public function singlePoTracingData($poID, $type = 'po', $grvAutoID = null, $bookingSuppMasInvAutoID = null, $PayMasterAutoId = null, $debitNoteID = null, $purchaseOrderID = null)
    {
        $procumentOrder = ProcumentOrder::find($poID);

        if(isset($procumentOrder))
        {
            if ($procumentOrder->poTypeID == 1) {
                $poIdsArray = (is_array($poID)) ? $poID : [$poID];

                $prDetails = PurchaseOrderDetails::whereIn('purchaseOrderMasterID', $poIdsArray)
                    ->groupBy('purchaseRequestID')
                    ->get();

                $prIDS = $prDetails->pluck('purchaseRequestID');

                $trData = [];
                foreach ($prIDS as $key => $value) {
                    $trData[] = $this->getPurchaseRequestTracingData($value, $type, $poID, $grvAutoID, $bookingSuppMasInvAutoID, $PayMasterAutoId, $debitNoteID);
                }

                return $trData;
            } else {
                $procumentOrderData = ProcumentOrder::with(['currency'])
                    ->find($poID)
                    ->toArray();
                $cancelStatus = ($procumentOrder->poCancelledYN == -1) ? " -- @Cancelled@" : "";
                $tempPo = [];
                $tempPo['name'] = "Purchase Order";
                if ($type == 'po' && ($procumentOrder->purchaseOrderID == $purchaseOrderID)) {
                    $tempPo['cssClass'] = "ngx-org-step-two root-tracing-node";
                } else {
                    $tempPo['cssClass'] = "ngx-org-step-two";
                }

                $tempPo['documentSystemID'] = $procumentOrderData['documentSystemID'];
                $tempPo['docAutoID'] = $procumentOrderData['purchaseOrderID'];
                $tempPo['title'] = "{Doc Code :} " . $procumentOrderData['purchaseOrderCode'] . " -- {Doc Date :} " . Carbon::parse($procumentOrderData['expectedDeliveryDate'])->format('Y-m-d') . " -- {Currency :} " . $procumentOrderData['currency']['CurrencyCode'] . " -- {Amount :} " . number_format($procumentOrderData['poTotalSupplierTransactionCurrency'], $procumentOrderData['currency']['DecimalPlaces']) . $cancelStatus;

                $grvData = $this->getPOtoPaymentChainForTracing($procumentOrder, $grvAutoID, $bookingSuppMasInvAutoID, $type, $debitNoteID);
                if (sizeof($grvData) > 0) {
                    foreach ($grvData as $key => $value) {
                        $cancelStatus = ($value['grv_master']['grvCancelledYN'] == -1) ? " -- @Cancelled@" : "";

                        $temp = [];
                        $temp['name'] = "Good Receipt Voucher";
                        if ($type == 'grv' && ($value['grv_master']['grvAutoID'] == $grvAutoID)) {
                            $temp['cssClass'] = "ngx-org-step-three root-tracing-node";
                        } else {
                            $temp['cssClass'] = "ngx-org-step-three";
                        }
                        $detailTotalAmount = 0;
                        foreach($value['grv_master']['details'] as $detail)
                        {
                            if($value['purchaseOrderMastertID'] == $detail['purchaseOrderMastertID'])
                            {
                                $detailTotalAmount+=$detail['netAmount'];
                            }

                        }

                        $temp['documentSystemID'] = $value['grv_master']['documentSystemID'];
                        $temp['docAutoID'] = $value['grv_master']['grvAutoID'];
                        $temp['title'] = "{Doc Code :} " . $value['grv_master']['grvPrimaryCode'] . " -- {Doc Date :} " . Carbon::parse($value['grv_master']['grvDate'])->format('Y-m-d') . " -- {Currency :} " . $value['grv_master']['currency_by']['CurrencyCode'] . " -- {Document Amount :} " . number_format($detailTotalAmount, $value['grv_master']['currency_by']['DecimalPlaces']) ." -- {Total Amount :} " . number_format($value['grv_master']['grvTotalSupplierTransactionCurrency'], $value['grv_master']['currency_by']['DecimalPlaces']). $cancelStatus;

                        foreach ($value['invoices'] as $key1 => $value1) {
                            $cancelStatus = ($value1['suppinvmaster']['cancelYN'] == -1) ? " -- @Cancelled@" : "";
                            $temp1 = [];
                            $temp1['name'] = "Supplier Invoice";
                            if ($type == 'supInv' && ($value1['suppinvmaster']['bookingSuppMasInvAutoID'] == $bookingSuppMasInvAutoID)) {
                                $temp1['cssClass'] = "ngx-org-step-four root-tracing-node";
                            } else {
                                $temp1['cssClass'] = "ngx-org-step-four";
                            }
                            $detailInvoiceTotalAmount = 0;
                            foreach($value1['suppinvmaster']['detail'] as $detail)
                            {
                                if($value['grvAutoID'] == $detail['grvAutoID'])
                                {
                                    $detailInvoiceTotalAmount+=$detail['totLocalAmount'];

                                }
                            }

                            $temp1['documentSystemID'] = $value1['suppinvmaster']['documentSystemID'];
                            $temp1['docAutoID'] = $value1['suppinvmaster']['bookingSuppMasInvAutoID'];
                            $temp1['title'] = "{Doc Code :} " . $value1['suppinvmaster']['bookingInvCode'] . " -- {Doc Date :} " . Carbon::parse($value1['suppinvmaster']['bookingDate'])->format('Y-m-d') . " -- {Currency :} " . $value1['suppinvmaster']['transactioncurrency']['CurrencyCode'] . " -- {Amount :} " . number_format($detailInvoiceTotalAmount, $value1['suppinvmaster']['transactioncurrency']['DecimalPlaces']) ." -- {Total Amount :} " . number_format($value1['suppinvmaster']['bookingAmountTrans'], $value1['suppinvmaster']['transactioncurrency']['DecimalPlaces']). $cancelStatus;

                            foreach ($value1['payments'] as $key2 => $value2) {
                                $temp2 = [];
                                if (isset($value2['payment_master'])) {
                                    if ($type == 'pv' && $PayMasterAutoId == $value2['payment_master']['PayMasterAutoId']) {
                                        $temp2['cssClass'] = "ngx-org-step-five root-tracing-node";
                                    } else {
                                        $temp2['cssClass'] = "ngx-org-step-five";
                                    }

                                    $cancelStatus = ($value2['payment_master']['cancelYN'] == -1) ? " -- @Cancelled@" : "";
                                    $temp2['name'] = "Payment";
                                    $temp2['documentSystemID'] = $value2['payment_master']['documentSystemID'];
                                    $temp2['docAutoID'] = $value2['payment_master']['PayMasterAutoId'];
                                    $temp2['title'] = "{Doc Code :} " . $value2['payment_master']['BPVcode'] . " -- {Doc Date :} " . Carbon::parse($value2['payment_master']['BPVdate'])->format('Y-m-d') . " -- {Currency :} " . $value2['payment_master']['transactioncurrency']['CurrencyCode'] . " -- {Amount :} " . number_format($value2['payment_master']['payAmountSuppTrans'], $value2['payment_master']['transactioncurrency']['DecimalPlaces']) . $cancelStatus;


                                    $debitNotes = PaySupplierInvoiceDetail::selectRaw('sum(supplierDefaultAmount) as supplierDefaultAmount,bookingInvSystemCode,PayMasterAutoId,matchingDocID, bookingInvDocCode, bookingInvoiceDate')
                                        ->where('PayMasterAutoId', $value2['payment_master']['PayMasterAutoId'])
                                        ->where('addedDocumentType', 4)
                                        ->where('matchingDocID', 0)
                                        ->with(['payment_master' => function ($query) {
                                            $query->with(['transactioncurrency']);
                                        }])
                                        ->groupBy('bookingInvSystemCode')
                                        ->get();


                                    foreach ($debitNotes as $key => $value) {
                                        $tempDeb = [];

                                        if ($type == "debit" && ($debitNoteID == $value->bookingInvSystemCode)) {
                                            $tempDeb['cssClass'] = "ngx-org-step-six root-tracing-node";
                                        } else {
                                            $tempDeb['cssClass'] = "ngx-org-step-six";
                                        }
                                        $tempDeb['name'] = "Debit Note";
                                        $tempDeb['documentSystemID'] = 15;
                                        $tempDeb['docAutoID'] = $value->bookingInvSystemCode;
                                        $tempDeb['title'] = "{Doc Code :} " . $value->bookingInvDocCode . " -- {Doc Date :} " . Carbon::parse($value->bookingInvoiceDate)->format('Y-m-d') . " -- {Currency :} " . $value['payment_master']['transactioncurrency']['CurrencyCode'] . " -- {Amount :} " . number_format(ABS($value->supplierDefaultAmount), $value['payment_master']['transactioncurrency']['DecimalPlaces']);

                                        $temp2['childs'][] = $tempDeb;
                                    }
                                }

                                if (isset($value2['matching_master'])) {
                                    if ($type == 'debit' && $debitNoteID == $value2['matching_master']['PayMasterAutoId']) {
                                        $temp2['cssClass'] = "ngx-org-step-five root-tracing-node";
                                    } else {
                                        $temp2['cssClass'] = "ngx-org-step-five";
                                    }
                                    $temp2['name'] = "Payment Matching";
                                    $temp2['documentSystemID'] = $value2['matching_master']['documentSystemID'];
                                    $temp2['docAutoID'] = $value2['matching_master']['PayMasterAutoId'];
                                    $temp2['title'] = "{Doc Code :} " . $value2['matching_master']['matchingDocCode'] . " -- {Doc Date :} " . Carbon::parse($value2['matching_master']['matchingDocdate'])->format('Y-m-d') . " -- {Currency :} " . $value2['matching_master']['transactioncurrency']['CurrencyCode'] . " -- {Amount :} " . number_format($value2['matching_master']['matchingAmount'], $value2['matching_master']['transactioncurrency']['DecimalPlaces']);
                                }

                                $temp1['childs'][] = $temp2;
                            }

                            $temp['childs'][] = $temp1;
                        }


                        $returnes = PurchaseReturnDetails::selectRaw('sum(netAmountLocal) as localAmount,
                                                         sum(netAmountRpt) as rptAmount, sum(netAmount) as transAmount,grvAutoID,purhaseReturnAutoID')
                            ->where('grvAutoID', $value['grv_master']['grvAutoID'])
                            ->with(['master' => function ($query) {
                                $query->with(['currency_by']);
                            }])
                            ->groupBy('purhaseReturnAutoID')
                            ->get()
                            ->toArray();

                        foreach ($returnes as $key1 => $value1) {
                            if (isset($value1['master'])) {
                                $temp1 = [];
                                $temp1['name'] = "Purchase Return";
                                $temp1['cssClass'] = "ngx-org-step-four";
                                $temp1['documentSystemID'] = $value1['master']['documentSystemID'];
                                $temp1['docAutoID'] = $value1['master']['purhaseReturnAutoID'];
                                $temp1['title'] = "{Doc Code :} " . $value1['master']['purchaseReturnCode'] . " -- {Doc Date :} " . Carbon::parse($value1['master']['purchaseReturnDate'])->format('Y-m-d') . " -- {Currency :} " . $value1['master']['currency_by']['CurrencyCode'] . " -- {Amount :} " . number_format($value1['transAmount'], $value1['master']['currency_by']['DecimalPlaces']);

                                $temp['childs'][] = $temp1;
                            }
                        }

                        $tempPo['childs'][] = $temp;
                    }
                }

                $tempAdPay = [];

                $advancePayments = AdvancePaymentDetails::selectRaw('PayMasterAutoId, supplierTransCurrencyID, SUM(supplierTransAmount) as transAmount')
                    ->with(['pay_invoice', 'supplier_currency'])
                    ->where('purchaseOrderID', $procumentOrderData['purchaseOrderID'])
                    ->groupBy('purchaseOrderID');

                if (!is_null($PayMasterAutoId)) {
                    $advancePayments = $advancePayments->where('PayMasterAutoId', $PayMasterAutoId);
                }

                $advancePayments = $advancePayments->get();

                foreach ($advancePayments as $keyAd => $valueAd) {
                    $tempAdPay['name'] = "Payment Voucher";
                    if ($type == 'pv' && ($valueAd->PayMasterAutoId == $PayMasterAutoId)) {
                        $tempAdPay['cssClass'] = "ngx-org-step-three root-tracing-node";
                    } else {
                        $tempAdPay['cssClass'] = "ngx-org-step-three";
                    }

                    $tempAdPay['documentSystemID'] = $valueAd->pay_invoice->documentSystemID;
                    $tempAdPay['docAutoID'] = $valueAd->pay_invoice->PayMasterAutoId;
                    $tempAdPay['title'] = "{Doc Code :} " . $valueAd->pay_invoice->BPVcode . " -- {Doc Date :} " . Carbon::parse($valueAd->pay_invoice->BPVdate)->format('Y-m-d') . " -- {Currency :} " . $valueAd->supplier_currency->CurrencyCode . " -- {Amount :} " . number_format($valueAd->transAmount, $valueAd->supplier_currency->DecimalPlaces);

                    $tempPo['childs'][] = $tempAdPay;
                }

                $tracingData[] = $tempPo;
                return $tracingData;
            }
        }

        else
        {
            $type = 'PV';
            $paymount_vaoucher = PaySupplierInvoiceMaster::with('transactioncurrency')->find($PayMasterAutoId);

            if ($type == 'PV') {
                $temp2['cssClass'] = "ngx-org-step-three root-tracing-node";
            } else {
                $temp2['cssClass'] = "ngx-org-step-five";
            }

            $cancelStatus = ($paymount_vaoucher->cancelYN == -1) ? " -- @Cancelled@" : "";
            $temp2['name'] = "Payment Voucher";
            $temp2['documentSystemID'] = $paymount_vaoucher->documentSystemID;
            $temp2['docAutoID'] = $paymount_vaoucher->PayMasterAutoId;
            $temp2['title'] = "{Doc Code :} " . $paymount_vaoucher->BPVcode . " -- {Doc Date :} " . Carbon::parse($paymount_vaoucher->BPVdate)->format('Y-m-d') . " -- {Currency :} " . $paymount_vaoucher->transactioncurrency->CurrencyCode . " -- {Amount :} " . number_format($paymount_vaoucher->payAmountSuppTrans, $paymount_vaoucher->transactioncurrency->DecimalPlaces) . $cancelStatus;

            if ($paymount_vaoucher->invoiceType == 7) {
                $matchingData = MatchDocumentMaster::where('PayMasterAutoId', $PayMasterAutoId)
                    ->where('documentSystemID', 4)
                    ->get();

                foreach ($matchingData as $matchKey => $matchValue) {
                    $tempDeb = [];

                    $tempDeb['cssClass'] = "ngx-org-step-four";
                    $tempDeb['name'] = "Payment Voucher Matching";
                    $tempDeb['documentSystemID'] = 70;
                    $tempDeb['docAutoID'] = $matchValue->matchDocumentMasterAutoID;
                    $tempDeb['title'] = "{Doc Code :} " . $matchValue->matchingDocCode . " -- {Doc Date :} " . Carbon::parse($matchValue->matchingDocdate)->format('Y-m-d') . " -- {Currency :} " . $paymount_vaoucher->transactioncurrency->CurrencyCode . " -- {Amount :} " . number_format(ABS($matchValue->matchingAmount), $paymount_vaoucher->transactioncurrency->DecimalPlaces);

                    $paySupplierInvoiceDetail = PaySupplierInvoiceDetail::where('matchingDocID', $matchValue->matchDocumentMasterAutoID)->get();

                    $bookingIds = collect($paySupplierInvoiceDetail)->pluck('bookingInvSystemCode')->toArray();

                    // $tempDeb['childs'][] = $this->getSupplierInvoiceTracingData($bookingIds, $type, $PayMasterAutoId);;

                    $temp2['childs'][] = $tempDeb;
                }
            }


            $tracingData[] = $temp2;
            return $tracingData;
        }

    }

    public function getPOtoPaymentChainForTracing($row, $grvAutoID = null, $bookingSuppMasInvAutoID = null, $type = null, $debitNoteID = null)
    {
        $grvMasters = GRVDetails::selectRaw('sum(noQty*GRVcostPerUnitLocalCur) as localAmount,
                                        sum(noQty*landingCost_RptCur) as rptAmount,
                                        purchaseOrderMastertID,grvAutoID')
            ->where('purchaseOrderMastertID', $row->purchaseOrderID)
            ->with(['grv_master' => function ($query) {
                $query->with(['currency_by','details']);
            }])
            ->groupBy('grvAutoID');

        if (!is_null($grvAutoID)) {
            if (is_array($grvAutoID)) {
                $grvMasters = $grvMasters->whereIn('grvAutoID', $grvAutoID);
            } else {
                $grvMasters = $grvMasters->where('grvAutoID', $grvAutoID);
            }
        }

        $grvMasters = $grvMasters->get();

        foreach ($grvMasters as $grv) {
            $invoices = BookInvSuppDet::selectRaw('sum(totLocalAmount) as localAmount,
                                                 sum(totRptAmount) as rptAmount,grvAutoID,bookingSuppMasInvAutoID')
                ->where('grvAutoID', $grv->grvAutoID)
                ->where('purchaseOrderID', $row->purchaseOrderID)
                ->with(['suppinvmaster' => function ($query) {
                    $query->with(['transactioncurrency','detail']);
                }])
                ->groupBy('bookingSuppMasInvAutoID');

            if (!is_null($bookingSuppMasInvAutoID)) {
                if (is_array($bookingSuppMasInvAutoID)) {
                    $invoices = $invoices->whereIn('bookingSuppMasInvAutoID', $bookingSuppMasInvAutoID);
                } else {
                    $invoices = $invoices->where('bookingSuppMasInvAutoID', $bookingSuppMasInvAutoID);
                }
            }
            $invoices = $invoices->get();

            foreach ($invoices as $invoice) {
                //supplierPaymentAmount
                $paymentsInvoice = PaySupplierInvoiceDetail::selectRaw('sum(paymentLocalAmount) as localAmount,
                                                 sum(paymentComRptAmount) as rptAmount,bookingInvSystemCode,PayMasterAutoId,matchingDocID')
                    ->where('bookingInvSystemCode', $invoice->bookingSuppMasInvAutoID)
                    //->where('addedDocumentSystemID', 11)
                    ->where('matchingDocID', 0)
                    ->with(['payment_master' => function ($query) {
                        $query->with(['transactioncurrency']);
                    }])
                    ->groupBy('PayMasterAutoId')
                    ->get();

                $paymentsInvoiceMatch = PaySupplierInvoiceDetail::selectRaw('sum(paymentLocalAmount) as localAmount,
                                                 sum(paymentComRptAmount) as rptAmount,bookingInvSystemCode,matchingDocID')
                    ->where('bookingInvSystemCode', $invoice->bookingSuppMasInvAutoID)
                    //->where('addedDocumentSystemID', 11)
                    ->where('matchingDocID', '>', 0)
                    ->with(['matching_master' => function ($query) {
                        $query->with(['transactioncurrency']);
                    }])
                    ->groupBy('PayMasterAutoId');

                if (!is_null($debitNoteID)) {
                    $paymentsInvoiceMatch = $paymentsInvoiceMatch->whereHas('matching_master', function ($query) use ($debitNoteID) {
                        $query->where('PayMasterAutoId', $debitNoteID);
                    });
                }

                $paymentsInvoiceMatch = $paymentsInvoiceMatch->get();

                $totalInvoices = $paymentsInvoice->toArray() + $paymentsInvoiceMatch->toArray();

                $invoice->payments = $totalInvoices;
            }

            $grv->invoices = $invoices->toArray();
        }

        return $grvMasters->toArray();
    }


    public function getGrvTracingData($grvAutoID, $type = 'grv', $bookingSuppMasInvAutoID = null, $PayMasterAutoId = null, $debitNoteID = null)
    {
        if (!is_array($grvAutoID) || (is_array($grvAutoID) && sizeof($grvAutoID) == 1)) {
            $grvID = is_array($grvAutoID) ? $grvAutoID[0] : $grvAutoID;
            $grvMasterData = GRVMaster::find($grvID);

            if ($grvMasterData->grvTypeID == 1) {
                $res[] = $this->grvForwardTracing($grvID, $type, $bookingSuppMasInvAutoID, $PayMasterAutoId, $debitNoteID);

                return $res;
            }
        }

        $grvAutoIDs = (is_array($grvAutoID)) ? $grvAutoID : [$grvAutoID];
        $prDetails = GRVDetails::whereIn('grvAutoID', $grvAutoIDs)
            ->groupBy('purchaseOrderMastertID')
            ->get();

        $poIDS = $prDetails->pluck('purchaseOrderMastertID')->toArray();


        return $this->getPurchaseOrderTracingData($poIDS, $type, $grvAutoID, $bookingSuppMasInvAutoID, $PayMasterAutoId, $debitNoteID);
    }


    public function getSupplierInvoiceTracingData($bookingSuppMasInvAutoID, $type = 'supInv', $PayMasterAutoId = null, $debitNoteID = null)
    {
        $tracingData = [];
        if (!is_array($bookingSuppMasInvAutoID) || (is_array($bookingSuppMasInvAutoID) && sizeof($bookingSuppMasInvAutoID) == 1)) {
            $poID = is_array($bookingSuppMasInvAutoID) ? $bookingSuppMasInvAutoID[0] : $bookingSuppMasInvAutoID;
            return $this->singleSupInvoiceTracingData($poID, $type, $PayMasterAutoId, $debitNoteID);
        } else {
            $poData = BookInvSuppMaster::whereIn('bookingSuppMasInvAutoID', $bookingSuppMasInvAutoID)
                ->get();

            foreach ($poData as $key => $value) {
                $tracingData[] = $this->singleSupInvoiceTracingData($value->bookingSuppMasInvAutoID, $type, $PayMasterAutoId, $debitNoteID);
            }

            $finalData = [];
            foreach ($tracingData as $key => $value) {
                foreach ($value as $key1 => $value1) {
                    $finalData[] = $value1;
                }
            }

            return $finalData;
        }
    }

    public function singleSupInvoiceTracingData($bookingSuppMasInvAutoID, $type = 'supInv', $PayMasterAutoId = null, $debitNoteID = null)
    {
        $masterData = BookInvSuppMaster::find($bookingSuppMasInvAutoID);

        if ($masterData && ($masterData->documentType == 1 || $masterData->documentType == 3)) {
            $res[] = $this->supplierInvoiceForwaredTracing($bookingSuppMasInvAutoID, $type, $PayMasterAutoId, $debitNoteID);

            return $res;
        }


        if ($masterData && $masterData->documentType == 4) {
            $res[] = $this->supplierInvoiceForwaredTracingEmpInv($bookingSuppMasInvAutoID, $type, $PayMasterAutoId, $debitNoteID);

            return $res;
        }

        $invoices = BookInvSuppDet::where('bookingSuppMasInvAutoID', $bookingSuppMasInvAutoID)
            ->groupBy('grvAutoID')
            ->get();

        $grvIDS = $invoices->pluck('grvAutoID')->toArray();

        return $this->getGrvTracingData($grvIDS, $type, $bookingSuppMasInvAutoID, $PayMasterAutoId, $debitNoteID);
    }

    public function grvForwardTracing($grvAutoID, $type = 'grv', $bookingSuppMasInvAutoID = null, $PayMasterAutoId = null, $debitNoteID = null)
    {
        $tracingData = [];
        $grvMaster = GRVMaster::where('grvAutoID', $grvAutoID)
            ->with(['currency_by'])
            ->first();

        $invoices = BookInvSuppDet::selectRaw('sum(totLocalAmount) as localAmount,
                                                 sum(totRptAmount) as rptAmount,grvAutoID,bookingSuppMasInvAutoID')
            ->where('grvAutoID', $grvMaster->grvAutoID)
            ->with(['suppinvmaster' => function ($query) {
                $query->with(['transactioncurrency']);
            }])
            ->groupBy('bookingSuppMasInvAutoID')
            ->get();

        foreach ($invoices as $invoice) {
            //supplierPaymentAmount
            $paymentsInvoice = PaySupplierInvoiceDetail::selectRaw('sum(paymentLocalAmount) as localAmount,
                                             sum(paymentComRptAmount) as rptAmount,bookingInvSystemCode,PayMasterAutoId,matchingDocID')
                ->where('bookingInvSystemCode', $invoice->bookingSuppMasInvAutoID)
                //->where('addedDocumentSystemID', 11)
                ->where('matchingDocID', 0)
                ->with(['payment_master' => function ($query) {
                    $query->with(['transactioncurrency']);
                }])
                ->groupBy('PayMasterAutoId')
                ->get();

            $paymentsInvoiceMatch = PaySupplierInvoiceDetail::selectRaw('sum(paymentLocalAmount) as localAmount,
                                             sum(paymentComRptAmount) as rptAmount,bookingInvSystemCode,matchingDocID')
                ->where('bookingInvSystemCode', $invoice->bookingSuppMasInvAutoID)
                //->where('addedDocumentSystemID', 11)
                ->where('matchingDocID', '>', 0)
                ->with(['matching_master' => function ($query) {
                    $query->with(['transactioncurrency']);
                }])
                ->groupBy('PayMasterAutoId');

            if (!is_null($debitNoteID)) {
                $paymentsInvoiceMatch = $paymentsInvoiceMatch->whereHas('matching_master', function ($query) use ($debitNoteID) {
                    $query->where('PayMasterAutoId', $debitNoteID);
                });
            }

            $paymentsInvoiceMatch = $paymentsInvoiceMatch->get();

            $totalInvoices = $paymentsInvoice->toArray() + $paymentsInvoiceMatch->toArray();

            $invoice->payments = $totalInvoices;
        }

        $invoiceData = $invoices->toArray();
        $cancelStatus = ($grvMaster->grvCancelledYN == -1) ? " -- @Cancelled@" : "";

        $tracingData['name'] = "Good Received Voucher";
        $tracingData['cssClass'] = "ngx-org-step-one root-tracing-node";
        $tracingData['documentSystemID'] = $grvMaster->documentSystemID;
        $tracingData['docAutoID'] = $grvMaster->grvAutoID;
        $tracingData['title'] = "{Doc Code :} " . $grvMaster->grvPrimaryCode . " -- {Doc Date :} " . Carbon::parse($grvMaster->grvDate)->format('Y-m-d') . " -- {Currency :} " . $grvMaster->currency_by->CurrencyCode . "-- {Amount :} " . number_format($grvMaster->grvTotalSupplierTransactionCurrency, $grvMaster->currency_by->DecimalPlaces) . $cancelStatus;


        foreach ($invoiceData as $key1 => $value1) {
            $cancelStatus = ($value1['suppinvmaster']['cancelYN'] == -1) ? " -- @Cancelled@" : "";
            $temp1 = [];
            $temp1['name'] = "Supplier Invoice";
            $temp1['cssClass'] = "ngx-org-step-two";
            $temp1['documentSystemID'] = $value1['suppinvmaster']['documentSystemID'];
            $temp1['docAutoID'] = $value1['suppinvmaster']['bookingSuppMasInvAutoID'];
            $temp1['title'] = "{Doc Code :} " . $value1['suppinvmaster']['bookingInvCode'] . " -- {Doc Date :} " . Carbon::parse($value1['suppinvmaster']['bookingDate'])->format('Y-m-d') . " -- {Currency :} " . $value1['suppinvmaster']['transactioncurrency']['CurrencyCode'] . " -- {Amount :} " . number_format($value1['suppinvmaster']['bookingAmountTrans'], $value1['suppinvmaster']['transactioncurrency']['DecimalPlaces']) . $cancelStatus;

            foreach ($value1['payments'] as $key2 => $value2) {
                $temp2 = [];
                if (isset($value2['payment_master'])) {
                    if ($type == 'pv' && $PayMasterAutoId == $value2['payment_master']['PayMasterAutoId']) {
                        $temp2['cssClass'] = "ngx-org-step-three root-tracing-node";
                    } else {
                        $temp2['cssClass'] = "ngx-org-step-three";
                    }
                    $temp2['name'] = "Payment";
                    $temp2['documentSystemID'] = $value2['payment_master']['documentSystemID'];
                    $temp2['docAutoID'] = $value2['payment_master']['PayMasterAutoId'];
                    $temp2['title'] = "{Doc Code :} " . $value2['payment_master']['BPVcode'] . " -- {Doc Date :} " . Carbon::parse($value2['payment_master']['BPVdate'])->format('Y-m-d') . " -- {Currency :} " . $value2['payment_master']['transactioncurrency']['CurrencyCode'] . " -- {Amount :} " . number_format($value2['payment_master']['payAmountSuppTrans'], $value2['payment_master']['transactioncurrency']['DecimalPlaces']);


                    $debitNotes = PaySupplierInvoiceDetail::selectRaw('sum(supplierDefaultAmount) as supplierDefaultAmount,bookingInvSystemCode,PayMasterAutoId,matchingDocID, bookingInvDocCode, bookingInvoiceDate')
                        ->where('PayMasterAutoId', $value2['payment_master']['PayMasterAutoId'])
                        ->where('addedDocumentType', 4)
                        ->where('matchingDocID', 0)
                        ->with(['payment_master' => function ($query) {
                            $query->with(['transactioncurrency']);
                        }])
                        ->groupBy('bookingInvSystemCode')
                        ->get();


                    foreach ($debitNotes as $key => $value) {
                        $tempDeb = [];

                        if ($type == "debit" && ($debitNoteID == $value->bookingInvSystemCode)) {
                            $tempDeb['cssClass'] = "ngx-org-step-four root-tracing-node";
                        } else {
                            $tempDeb['cssClass'] = "ngx-org-step-four";
                        }
                        $tempDeb['name'] = "Debit Note";
                        $tempDeb['documentSystemID'] = 15;
                        $tempDeb['docAutoID'] = $value->bookingInvSystemCode;
                        $tempDeb['title'] = "{Doc Code :} " . $value->bookingInvDocCode . " -- {Doc Date :} " . Carbon::parse($value->bookingInvoiceDate)->format('Y-m-d') . " -- {Currency :} " . $value['payment_master']['transactioncurrency']['CurrencyCode'] . " -- {Amount :} " . number_format(ABS($value->supplierDefaultAmount), $value['payment_master']['transactioncurrency']['DecimalPlaces']);

                        $temp2['childs'][] = $tempDeb;
                    }
                }

                if (isset($value2['matching_master'])) {
                    if ($type == 'debit' && $debitNoteID == $value2['matching_master']['PayMasterAutoId']) {
                        $temp2['cssClass'] = "ngx-org-step-three root-tracing-node";
                    } else {
                        $temp2['cssClass'] = "ngx-org-step-three";
                    }
                    $temp2['name'] = "Debit Note";
                    $temp2['documentSystemID'] = $value2['matching_master']['documentSystemID'];
                    $temp2['docAutoID'] = $value2['matching_master']['PayMasterAutoId'];
                    $temp2['title'] = "{Doc Code :} " . $value2['matching_master']['BPVcode'] . " -- {Doc Date :} " . Carbon::parse($value2['matching_master']['BPVdate'])->format('Y-m-d') . " -- {Currency :} " . $value2['matching_master']['transactioncurrency']['CurrencyCode'] . " -- {Amount :} " . number_format($value2['matching_master']['payAmountSuppTrans'], $value2['matching_master']['transactioncurrency']['DecimalPlaces']);
                }

                $temp1['childs'][] = $temp2;
            }

            $tracingData['childs'][] = $temp1;
        }


        $returnes = PurchaseReturnDetails::selectRaw('sum(netAmountLocal) as localAmount,
                                                 sum(netAmountRpt) as rptAmount, sum(netAmount) as transAmount,grvAutoID,purhaseReturnAutoID')
            ->where('grvAutoID', $grvMaster->grvAutoID)
            ->with(['master' => function ($query) {
                $query->with(['currency_by']);
            }])
            ->groupBy('purhaseReturnAutoID')
            ->get()
            ->toArray();

        foreach ($returnes as $key1 => $value1) {
            if (isset($value1['master'])) {
                $temp1 = [];
                $temp1['name'] = "Purchase Return";
                $temp1['cssClass'] = "ngx-org-step-two";
                $temp1['documentSystemID'] = $value1['master']['documentSystemID'];
                $temp1['docAutoID'] = $value1['master']['purhaseReturnAutoID'];
                $temp1['title'] = "{Doc Code :} " . $value1['master']['purchaseReturnCode'] . " -- {Doc Date :} " . Carbon::parse($value1['master']['purchaseReturnDate'])->format('Y-m-d') . " -- {Currency :} " . $value1['master']['currency_by']['CurrencyCode'] . " -- {Amount :} " . number_format($value1['transAmount'], $value1['master']['currency_by']['DecimalPlaces']);

                $tracingData['childs'][] = $temp1;
            }
        }


        return $tracingData;
    }

    public function supplierInvoiceForwaredTracing($bookingSuppMasInvAutoID, $type, $PayMasterAutoId = null, $debitNoteID = null)
    {
        $tracingData = [];
        $invoiceMaster = BookInvSuppMaster::where('bookingSuppMasInvAutoID', $bookingSuppMasInvAutoID)
            ->with(['transactioncurrency'])
            ->first();

        //supplierPaymentAmount
        $paymentsInvoice = PaySupplierInvoiceDetail::selectRaw('sum(paymentLocalAmount) as localAmount,
                                         sum(paymentComRptAmount) as rptAmount,bookingInvSystemCode,PayMasterAutoId,matchingDocID')
            ->where('bookingInvSystemCode', $invoiceMaster->bookingSuppMasInvAutoID)
            //->where('addedDocumentSystemID', 11)
            ->where('matchingDocID', 0)
            ->with(['payment_master' => function ($query) {
                $query->with(['transactioncurrency']);
            }])
            ->groupBy('PayMasterAutoId')
            ->get();

        $paymentsInvoiceMatch = PaySupplierInvoiceDetail::selectRaw('sum(paymentLocalAmount) as localAmount,
                                         sum(paymentComRptAmount) as rptAmount,bookingInvSystemCode,matchingDocID')
            ->where('bookingInvSystemCode', $invoiceMaster->bookingSuppMasInvAutoID)
            //->where('addedDocumentSystemID', 11)
            ->where('matchingDocID', '>', 0)
            ->with(['matching_master' => function ($query) {
                $query->with(['transactioncurrency']);
            }])
            ->groupBy('PayMasterAutoId');

        if (!is_null($debitNoteID)) {
            $paymentsInvoiceMatch = $paymentsInvoiceMatch->whereHas('matching_master', function ($query) use ($debitNoteID) {
                $query->where('PayMasterAutoId', $debitNoteID);
            });
        }

        $paymentsInvoiceMatch = $paymentsInvoiceMatch->get();

        //$totalInvoices = $paymentsInvoice->toArray() + $paymentsInvoiceMatch->toArray();
        $totalInvoices = array_merge($paymentsInvoice->toArray(), $paymentsInvoiceMatch->toArray());
        $cancelStatus = ($invoiceMaster->cancelYN == -1) ? " -- @Cancelled@" : "";
        $tracingData['name'] = "Supplier Invoice";
        if ($type == "supInv" && ($bookingSuppMasInvAutoID == $invoiceMaster->bookingSuppMasInvAutoID)) {
            $tracingData['cssClass'] = "ngx-org-step-one root-tracing-node";
        } else {
            $tracingData['cssClass'] = "ngx-org-step-one";
        }
        $tracingData['documentSystemID'] = $invoiceMaster->documentSystemID;
        $tracingData['docAutoID'] = $invoiceMaster->bookingSuppMasInvAutoID;
        $tracingData['title'] = "{Doc Code :} " . $invoiceMaster->bookingInvCode . " -- {Doc Date :} " . Carbon::parse($invoiceMaster->bookingDate)->format('Y-m-d') . " -- {Currency :} " . $invoiceMaster->transactioncurrency->CurrencyCode . "-- {Amount :} " . number_format($invoiceMaster->bookingAmountTrans, $invoiceMaster->transactioncurrency->DecimalPlaces) . $cancelStatus;

        foreach ($totalInvoices as $key2 => $value2) {
            $temp2 = [];
            if (isset($value2['payment_master'])) {
                if ($type == "pv" && ($PayMasterAutoId == $value2['payment_master']['PayMasterAutoId'])) {
                    $temp2['cssClass'] = "ngx-org-step-two root-tracing-node";
                } else {
                    $temp2['cssClass'] = "ngx-org-step-two";
                }
                $temp2['name'] = "Payment";
                $temp2['documentSystemID'] = $value2['payment_master']['documentSystemID'];
                $temp2['docAutoID'] = $value2['payment_master']['PayMasterAutoId'];
                $temp2['title'] = "{Doc Code :} " . $value2['payment_master']['BPVcode'] . " -- {Doc Date :} " . Carbon::parse($value2['payment_master']['BPVdate'])->format('Y-m-d') . " -- {Currency :} " . $value2['payment_master']['transactioncurrency']['CurrencyCode'] . " -- {Amount :} " . number_format($value2['payment_master']['payAmountSuppTrans'], $value2['payment_master']['transactioncurrency']['DecimalPlaces']);


                $debitNotes = PaySupplierInvoiceDetail::selectRaw('sum(supplierDefaultAmount) as supplierDefaultAmount,bookingInvSystemCode,PayMasterAutoId,matchingDocID, bookingInvDocCode, bookingInvoiceDate')
                    ->where('PayMasterAutoId', $value2['payment_master']['PayMasterAutoId'])
                    ->where('addedDocumentType', 4)
                    ->where('matchingDocID', 0)
                    ->with(['payment_master' => function ($query) {
                        $query->with(['transactioncurrency']);
                    }])
                    ->groupBy('bookingInvSystemCode')
                    ->get();

                foreach ($debitNotes as $key => $value) {
                    $tempDeb = [];
                    if ($type == "debit" && ($debitNoteID == $value->bookingInvSystemCode)) {
                        $tempDeb['cssClass'] = "ngx-org-step-three root-tracing-node";
                    } else {
                        $tempDeb['cssClass'] = "ngx-org-step-three";
                    }
                    $tempDeb['name'] = "Debit Note";
                    $tempDeb['documentSystemID'] = 15;
                    $tempDeb['docAutoID'] = $value->bookingInvSystemCode;
                    $tempDeb['title'] = "{Doc Code :} " . $value->bookingInvDocCode . " -- {Doc Date :} " . Carbon::parse($value->bookingInvoiceDate)->format('Y-m-d') . " -- {Currency :} " . $value['payment_master']['transactioncurrency']['CurrencyCode'] . " -- {Amount :} " . number_format(ABS($value->supplierDefaultAmount), $value['payment_master']['transactioncurrency']['DecimalPlaces']);

                    $temp2['childs'][] = $tempDeb;
                }
            }

            if (isset($value2['matching_master'])) {
                if ($type == "debit" && ($debitNoteID == $value2['matching_master']['PayMasterAutoId'])) {
                    $temp2['cssClass'] = "ngx-org-step-two root-tracing-node";
                } else {
                    $temp2['cssClass'] = "ngx-org-step-two";
                }
                if($value2['matching_master']['documentSystemID'] == 4)
                {
                    $temp2['name'] = "Payment";
                }
                else if($value2['matching_master']['documentSystemID'] == 15)
                {
                    $temp2['name'] = "Debit Note";
                }
                $temp2['documentSystemID'] = $value2['matching_master']['documentSystemID'];
                $temp2['docAutoID'] = $value2['matching_master']['PayMasterAutoId'];
                $temp2['title'] = "{Doc Code :} " . $value2['matching_master']['BPVcode'] . " -- {Doc Date :} " . Carbon::parse($value2['matching_master']['BPVdate'])->format('Y-m-d') . " -- {Currency :} " . $value2['matching_master']['transactioncurrency']['CurrencyCode'] . " -- {Amount :} " . number_format($value2['matching_master']['matchedAmount'], $value2['matching_master']['transactioncurrency']['DecimalPlaces']);
            }

            $tracingData['childs'][] = $temp2;
        }

        return $tracingData;
    }

    public function supplierInvoiceForwaredTracingEmpInv($bookingSuppMasInvAutoID, $type, $PayMasterAutoId = null, $debitNoteID = null)
    {
        $tracingData = [];
        $invoiceMaster = BookInvSuppMaster::where('bookingSuppMasInvAutoID', $bookingSuppMasInvAutoID)
            ->with(['transactioncurrency'])
            ->first();

        //supplierPaymentAmount
        $paymentsInvoice = PaySupplierInvoiceDetail::selectRaw('sum(paymentLocalAmount) as localAmount,
                                         sum(paymentComRptAmount) as rptAmount,bookingInvSystemCode,PayMasterAutoId,matchingDocID')
            ->where('bookingInvSystemCode', $invoiceMaster->bookingSuppMasInvAutoID)
            //->where('addedDocumentSystemID', 11)
            ->where('matchingDocID', 0)
            ->with(['payment_master' => function ($query) {
                $query->with(['transactioncurrency']);
            }])
            ->groupBy('PayMasterAutoId')
            ->get();

        $paymentsInvoiceMatch = PaySupplierInvoiceDetail::selectRaw('sum(paymentLocalAmount) as localAmount,
                                         sum(paymentComRptAmount) as rptAmount,bookingInvSystemCode,matchingDocID')
            ->where('bookingInvSystemCode', $invoiceMaster->bookingSuppMasInvAutoID)
            //->where('addedDocumentSystemID', 11)
            ->where('matchingDocID', '>', 0)
            ->with(['matching_master' => function ($query) {
                $query->with(['transactioncurrency']);
            }])
            ->groupBy('PayMasterAutoId');

        if (!is_null($debitNoteID)) {
            $paymentsInvoiceMatch = $paymentsInvoiceMatch->whereHas('matching_master', function ($query) use ($debitNoteID) {
                $query->where('PayMasterAutoId', $debitNoteID);
            });
        }

        $paymentsInvoiceMatch = $paymentsInvoiceMatch->get();

        // $totalInvoices = $paymentsInvoice->toArray() + $paymentsInvoiceMatch->toArray();
        $totalInvoices = array_merge($paymentsInvoice->toArray(), $paymentsInvoiceMatch->toArray());
        $cancelStatus = ($invoiceMaster->cancelYN == -1) ? " -- @Cancelled@" : "";
        $tracingData['name'] = "Supplier Invoice";
        if ($type == "supInv" && ($bookingSuppMasInvAutoID == $invoiceMaster->bookingSuppMasInvAutoID)) {
            $tracingData['cssClass'] = "ngx-org-step-one root-tracing-node";
        } else {
            $tracingData['cssClass'] = "ngx-org-step-one";
        }
        $tracingData['documentSystemID'] = $invoiceMaster->documentSystemID;
        $tracingData['docAutoID'] = $invoiceMaster->bookingSuppMasInvAutoID;
        $tracingData['title'] = "{Doc Code :} " . $invoiceMaster->bookingInvCode . " -- {Doc Date :} " . Carbon::parse($invoiceMaster->bookingDate)->format('Y-m-d') . " -- {Currency :} " . $invoiceMaster->transactioncurrency->CurrencyCode . "-- {Amount :} " . number_format($invoiceMaster->bookingAmountTrans, $invoiceMaster->transactioncurrency->DecimalPlaces) . $cancelStatus;

        foreach ($totalInvoices as $key2 => $value2) {
            $temp2 = [];
            if (isset($value2['payment_master'])) {
                if ($type == "pv" && ($PayMasterAutoId == $value2['payment_master']['PayMasterAutoId'])) {
                    $temp2['cssClass'] = "ngx-org-step-two root-tracing-node";
                } else {
                    $temp2['cssClass'] = "ngx-org-step-two";
                }
                $temp2['name'] = "Payment";
                $temp2['documentSystemID'] = $value2['payment_master']['documentSystemID'];
                $temp2['docAutoID'] = $value2['payment_master']['PayMasterAutoId'];
                $temp2['title'] = "{Doc Code :} " . $value2['payment_master']['BPVcode'] . " -- {Doc Date :} " . Carbon::parse($value2['payment_master']['BPVdate'])->format('Y-m-d') . " -- {Currency :} " . $value2['payment_master']['transactioncurrency']['CurrencyCode'] . " -- {Amount :} " . number_format($value2['payment_master']['payAmountSuppTrans'], $value2['payment_master']['transactioncurrency']['DecimalPlaces']);

            }

            if (isset($value2['matching_master'])) {
                if ($type == "debit" && ($debitNoteID == $value2['matching_master']['PayMasterAutoId'])) {
                    $temp2['cssClass'] = "ngx-org-step-two root-tracing-node";
                } else {
                    $temp2['cssClass'] = "ngx-org-step-two";
                }


                if($value2['matching_master']['documentSystemID'] == 4)
                {
                    $temp2['name'] = "Payment";
                }
                else if($value2['matching_master']['documentSystemID'] == 15)
                {
                    $temp2['name'] = "Debit Note";
                }

                $temp2['documentSystemID'] = $value2['matching_master']['documentSystemID'];
                $temp2['docAutoID'] = $value2['matching_master']['PayMasterAutoId'];
                $temp2['title'] = "{Doc Code :} " . $value2['matching_master']['BPVcode'] . " -- {Doc Date :} " . Carbon::parse($value2['matching_master']['BPVdate'])->format('Y-m-d') . " -- {Currency :} " . $value2['matching_master']['transactioncurrency']['CurrencyCode'] . " -- {Amount :} " . number_format($value2['matching_master']['matchedAmount'], $value2['matching_master']['transactioncurrency']['DecimalPlaces']);
            }

            $tracingData['childs'][] = $temp2;
        }

        return $tracingData;
    }

    public function getSalesReturnTracingData($salesReturnID, $type = 'sr')
    {
        $salesReturnData = SalesReturn::find($salesReturnID);
        $tracingData = [];
        if ($salesReturnData->returnType == 1) {
            $soDetails = SalesReturnDetail::where('salesReturnID', $salesReturnID)
                ->groupBy('deliveryOrderID')
                ->get();

            $quoIds = $soDetails->pluck('deliveryOrderID');
            foreach ($quoIds as $key => $value) {
                $tracingData = $this->getDeliveryOrderTracingData($value, $type, null, null, $salesReturnID);
            }
        } else {
            $soDetails = SalesReturnDetail::where('salesReturnID', $salesReturnID)
                ->groupBy('custInvoiceDirectAutoID')
                ->get();

            $quoIds = $soDetails->pluck('custInvoiceDirectAutoID');
            foreach ($quoIds as $key => $value) {
                $tracingData = $this->getCustomerInvoiceTracingData($value, $type, null, $salesReturnID);
            }
        }

        return $tracingData;
    }

    public function getReciptMatchingTracingData($matchDocumentMasterAutoID, $type = 'reciptVoucherMatching', $creditNoteAutoID = null)
    {
        $recieptVouchersMatch = CustomerReceivePaymentDetail::where('matchingDocID', $matchDocumentMasterAutoID)
            ->where('addedDocumentSystemID', 20)
            ->groupBy('bookingInvCodeSystem')
            ->get();


        $tracingData = [];
        foreach ($recieptVouchersMatch as $key => $value) {
            $tracingData = $this->getCustomerInvoiceTracingData($value->bookingInvCodeSystem, $type, null, null, $matchDocumentMasterAutoID, $creditNoteAutoID);
        }

        return $tracingData;
    }

    public function getCreditNoteTracingData($creditNoteAutoID, $type = 'cn')
    {
        $creditNotes = MatchDocumentMaster::where('PayMasterAutoId', $creditNoteAutoID)
            ->where('documentSystemID', 19)
            ->groupBy('matchDocumentMasterAutoID')
            ->get();


        $tracingData = [];
        foreach ($creditNotes as $key => $value) {
            $tracingData[] = $this->getReciptMatchingTracingData($value->matchDocumentMasterAutoID, $type, $creditNoteAutoID);
        }

        $creditNotesFromRec = CustomerReceivePaymentDetail::where('bookingInvCodeSystem', $creditNoteAutoID)
            ->where('addedDocumentSystemID', 19)
            ->where('matchingDocID', 0)
            ->groupBY('custReceivePaymentAutoID')
            ->get();
        foreach ($creditNotesFromRec as $key => $value) {
            $tracingData[] = $this->getReciptVoucherTracingData($value->custReceivePaymentAutoID, $type, $creditNoteAutoID);
        }

        $finalData = [];

        foreach ($tracingData as $key => $value) {
            foreach ($value as $key1 => $value1) {
                $finalData[] = $value1;
            }
        }


        return $finalData;
    }

    public function getSalesOrderTracingData($salesOrderID, $type = 'so', $deliveryOrderID = null, $custInvoiceDirectAutoID = null, $custReceivePaymentAutoID = null, $salesReturnID = null, $matchDocumentMasterAutoID = null, $creditNoteAutoID = null)
    {
        $quotationMaster = QuotationMaster::find($salesOrderID);

        $tracingData = [];
        if ($quotationMaster->quotationType == 1) {
            $tracingData[] = $this->getQuotationTracingData($salesOrderID, $quotationMaster->documentSystemID, $type, null, $deliveryOrderID, $custInvoiceDirectAutoID, $custReceivePaymentAutoID, $salesReturnID, $matchDocumentMasterAutoID, $creditNoteAutoID);
        } else {
            $soDetails = QuotationDetails::where('quotationMasterID', $salesOrderID)
                ->groupBy('soQuotationMasterID')
                ->get();

            $quoIds = $soDetails->pluck('soQuotationMasterID');
            foreach ($quoIds as $key => $value) {
                $tracingData[] = $this->getQuotationTracingData($value, 67, $type, $salesOrderID, $deliveryOrderID, $custInvoiceDirectAutoID, $custReceivePaymentAutoID, $salesReturnID, $matchDocumentMasterAutoID, $creditNoteAutoID);
            }
        }

        return $tracingData;
    }

    public function getDeliveryOrderTracingData($deliveryOrderID, $type = 'do', $custInvoiceDirectAutoID = null, $custReceivePaymentAutoID = null, $salesReturnID = null, $matchDocumentMasterAutoID = null, $creditNoteAutoID = null)
    {
        $deliveryOrderData = DeliveryOrder::find($deliveryOrderID);

        $tracingData = [];
        if ($deliveryOrderData->orderType == 1) {
            $deliveryOrderDetails = DeliveryOrderDetail::selectRaw('sum(companyLocalAmount) as localAmount,
                                                 sum(companyReportingAmount) as rptAmount, sum(transactionAmount) as transAmount,quotationMasterID,deliveryOrderID')
                ->where('deliveryOrderID', $deliveryOrderID)
                ->with(['master' => function ($query) {
                    $query->with(['transaction_currency']);
                }])
                ->groupBy('deliveryOrderID')
                ->first();

            $deliveryOrderDetails->invoices = new \stdClass();
            $deliveryOrderDetails->invoices = $this->customerInvoiceChainData($deliveryOrderID, null, $custInvoiceDirectAutoID, $custReceivePaymentAutoID, $salesReturnID, $matchDocumentMasterAutoID, $creditNoteAutoID);

            $deliveryOrderDetails = $deliveryOrderDetails->toArray();

            $tracingData[] = $this->setDeliveryOrderChainData($deliveryOrderDetails, $type, $deliveryOrderID, $custInvoiceDirectAutoID, $custReceivePaymentAutoID, $salesReturnID, $matchDocumentMasterAutoID, $creditNoteAutoID);
        } else if ($deliveryOrderData->orderType == 2) {
            $soDetails = DeliveryOrderDetail::where('deliveryOrderID', $deliveryOrderID)
                ->groupBy('quotationMasterID')
                ->get();

            $quoIds = $soDetails->pluck('quotationMasterID');
            foreach ($quoIds as $key => $value) {
                $tracingData[] = $this->getQuotationTracingData($value, 67, $type, null, $deliveryOrderID, $custInvoiceDirectAutoID, $custReceivePaymentAutoID, $salesReturnID, $matchDocumentMasterAutoID, $creditNoteAutoID);
            }
        } else {
            $soDetails = DeliveryOrderDetail::where('deliveryOrderID', $deliveryOrderID)
                ->groupBy('quotationMasterID')
                ->get();

            $soIds = $soDetails->pluck('quotationMasterID');
            foreach ($soIds as $key => $value) {
                $tracingData = $this->getSalesOrderTracingData($value, $type, $deliveryOrderID, $custInvoiceDirectAutoID, $custReceivePaymentAutoID, $salesReturnID, $matchDocumentMasterAutoID, $creditNoteAutoID);
            }
        }

        return $tracingData;
    }

    public function getCustomerInvoiceTracingData($custInvoiceDirectAutoID, $type = 'inv', $custReceivePaymentAutoID = null, $salesReturnID = null, $matchDocumentMasterAutoID = null, $creditNoteAutoID = null)
    {
        $ciData = CustomerInvoiceDirect::find($custInvoiceDirectAutoID);

        $tracingData = [];
        if ($ciData->isPerforma == 3) {
            $soDetails = CustomerInvoiceItemDetails::where('custInvoiceDirectAutoID', $custInvoiceDirectAutoID)
                ->groupBy('deliveryOrderID')
                ->get();

            $quoIds = $soDetails->pluck('deliveryOrderID');
            foreach ($quoIds as $key => $value) {
                $tracingData = $this->getDeliveryOrderTracingData($value, $type, $custInvoiceDirectAutoID, $custReceivePaymentAutoID, $salesReturnID, $matchDocumentMasterAutoID, $creditNoteAutoID);
            }
        } else if ($ciData->isPerforma == 4) {
            $soDetails = CustomerInvoiceItemDetails::where('custInvoiceDirectAutoID', $custInvoiceDirectAutoID)
                ->groupBy('quotationMasterID')
                ->get();

            $quoIds = $soDetails->pluck('quotationMasterID');
            foreach ($quoIds as $key => $value) {
                $tracingData = $this->getSalesOrderTracingData($value, $type, null, $custInvoiceDirectAutoID, $custReceivePaymentAutoID, $salesReturnID, $matchDocumentMasterAutoID, $creditNoteAutoID);
            }
        } else if ($ciData->isPerforma == 5) {
            $soDetails = CustomerInvoiceItemDetails::where('custInvoiceDirectAutoID', $custInvoiceDirectAutoID)
                ->groupBy('quotationMasterID')
                ->get();

            $quoIds = $soDetails->pluck('quotationMasterID');
            foreach ($quoIds as $key => $value) {
                $tracingData[] = $this->getQuotationTracingData($value, 67, $type, null, null, $custInvoiceDirectAutoID, $custReceivePaymentAutoID, $salesReturnID, $matchDocumentMasterAutoID, $creditNoteAutoID);
            }
        } else if ($ciData->isPerforma == 0 || $ciData->isPerforma == 1) {
            $invoice = CustomerInvoiceDirectDetail::selectRaw('sum(localAmount) as localAmount,
                                                 sum(comRptAmount) as rptAmount, sum(invoiceAmount) as transAmount,custInvoiceDirectID')
                ->where('custInvoiceDirectID', $custInvoiceDirectAutoID)
                ->with(['master' => function ($query) {
                    $query->with(['currency']);
                }])
                ->groupBy('custInvoiceDirectID')
                ->first();



            $recieptVouchers = CustomerReceivePaymentDetail::selectRaw('sum(receiveAmountLocal) as localAmount,
                                             sum(receiveAmountRpt) as rptAmount, SUM(receiveAmountTrans) as transAmount,bookingInvCodeSystem,addedDocumentSystemID,matchingDocID, custReceivePaymentAutoID')
                ->where('bookingInvCodeSystem', $custInvoiceDirectAutoID)
                ->where('addedDocumentSystemID', 20)
                ->where('matchingDocID', 0)
                ->with(['master' => function ($query) {
                    $query->with(['currency']);
                }])
                ->groupBy('custReceivePaymentAutoID')
                ->get();

            $totalInvoices = $recieptVouchers->toArray();

            $invoice->payments = $totalInvoices;

            $invoice = $invoice->toArray();


            $tracingData[] = $this->setCustomerInvoiceChainData($invoice, $type, $custInvoiceDirectAutoID, $custReceivePaymentAutoID, $salesReturnID, $matchDocumentMasterAutoID, $creditNoteAutoID);
        } else if ($ciData->isPerforma == 2) {
            $invoice = CustomerInvoiceItemDetails::selectRaw('sum(issueCostLocalTotal) as localAmount,
                                                 sum(issueCostRptTotal) as rptAmount, sum(sellingTotal) as transAmount,custInvoiceDirectAutoID')
                ->where('custInvoiceDirectAutoID', $custInvoiceDirectAutoID)
                ->with(['master' => function ($query) {
                    $query->with(['currency']);
                }])
                ->groupBy('custInvoiceDirectAutoID')
                ->first();

            if ($invoice) {
                $recieptVouchers = CustomerReceivePaymentDetail::selectRaw('sum(receiveAmountLocal) as localAmount,
                                                 sum(receiveAmountRpt) as rptAmount, SUM(receiveAmountTrans) as transAmount,bookingInvCodeSystem,addedDocumentSystemID,matchingDocID, custReceivePaymentAutoID')
                    ->where('bookingInvCodeSystem', $custInvoiceDirectAutoID)
                    ->where('addedDocumentSystemID', 20)
                    ->where('matchingDocID', 0)
                    ->with(['master' => function ($query) {
                        $query->with(['currency']);
                    }])
                    ->groupBy('custReceivePaymentAutoID')
                    ->get();

                $totalInvoices = $recieptVouchers->toArray();

                $invoice->payments = $totalInvoices;

                $invoice = $invoice->toArray();


                $tracingData[] = $this->setCustomerInvoiceChainData($invoice, $type, $custInvoiceDirectAutoID, $custReceivePaymentAutoID, $salesReturnID, $matchDocumentMasterAutoID, $creditNoteAutoID);
            }
        }

        return $tracingData;
    }

    public function getReciptVoucherTracingData($custReceivePaymentAutoID, $type = 'reciptVoucher', $creditNoteAutoID = null)
    {
        $ciData = CustomerReceivePayment::find($custReceivePaymentAutoID);

        $tracingData = [];
        if ($ciData->documentType == 13) {
            $soDetails = CustomerReceivePaymentDetail::where('custReceivePaymentAutoID', $custReceivePaymentAutoID)
                ->where('addedDocumentSystemID', 20)
                ->where('matchingDocID', 0)
                ->groupBy('bookingInvCodeSystem')
                ->get();

            $quoIds = $soDetails->pluck('bookingInvCodeSystem');
            foreach ($quoIds as $key => $value) {
                $tracingData[] = $this->getCustomerInvoiceTracingData($value, $type, $custReceivePaymentAutoID, null, null, $creditNoteAutoID);
            }
        } else if ($ciData->documentType == 14) {
            $recieptVouchers = DirectReceiptDetail::selectRaw('sum(netAmountLocal) as localAmount,
                                             sum(netAmountRpt) as rptAmount, SUM(netAmount) as transAmount,directReceiptAutoID')
                ->with(['master' => function ($query) {
                    $query->with(['currency']);
                }])
                ->groupBy('directReceiptAutoID')
                ->where('directReceiptAutoID', $custReceivePaymentAutoID)
                ->first();

            if ($recieptVouchers) {
                $recieptVouchers = $recieptVouchers->toArray();
            } else {
                $recieptVouchers = [];
            }


            $tracingData[][] = $this->setReceiptPaymentChain($recieptVouchers, $type, $custReceivePaymentAutoID, null, null, $creditNoteAutoID);
        } else if ($ciData->documentType == 15) {
            $recieptVouchers = AdvanceReceiptDetails::selectRaw('sum(localAmount) as localAmount,
                                             sum(comRptAmount) as rptAmount, SUM(supplierTransAmount) as transAmount,custReceivePaymentAutoID, salesOrderID')
                ->with(['master' => function ($query) {
                    $query->with(['currency']);
                }])
                ->groupBy('custReceivePaymentAutoID', 'salesOrderID')
                ->where('custReceivePaymentAutoID', $custReceivePaymentAutoID)
                ->get();

            if (count($recieptVouchers) > 0) {
                foreach ($recieptVouchers as $key => $value) {
                    $tracingData[] = $this->getSalesOrderTracingData($value->salesOrderID, $type, null, null, $custReceivePaymentAutoID);
                }
            } else {
                $recieptVouchers = DirectReceiptDetail::selectRaw('sum(netAmountLocal) as localAmount,
                                             sum(netAmountRpt) as rptAmount, SUM(netAmount) as transAmount,directReceiptAutoID')
                    ->with(['master' => function ($query) {
                        $query->with(['currency']);
                    }])
                    ->groupBy('directReceiptAutoID')
                    ->where('directReceiptAutoID', $custReceivePaymentAutoID)
                    ->first();

                if ($recieptVouchers) {
                    $recieptVouchers = $recieptVouchers->toArray();
                } else {
                    $recieptVouchers = [];
                }

                $tracingData[][] = $this->setReceiptPaymentChain($recieptVouchers, $type, $custReceivePaymentAutoID, null, null, $creditNoteAutoID);
            }
        }

        return $tracingData;
    }

    public function getQuotationTracingData($quotationMasterID, $documentSystemID, $type = 'quo', $salesOrderID = null, $deliveryOrderID = null, $custInvoiceDirectAutoID = null, $custReceivePaymentAutoID = null, $salesReturnID = null, $matchDocumentMasterAutoID = null, $creditNoteAutoID = null)
    {
        $tracingData = [];
        $quotationMaster = QuotationMaster::where('quotationMasterID', $quotationMasterID)
            ->with(['transaction_currency', 'detail' => function ($query) {
                $query->selectRaw('sum(companyLocalAmount) as localAmount,
                                                 sum(companyReportingAmount) as rptAmount, sum(transactionAmount) as transAmount,quotationMasterID, sum(VATAmount * requestedQty) as transVATAmount')
                    ->groupBy('quotationMasterID');
            }])
            ->first();

        $salesOrderDeatils = QuotationDetails::selectRaw('sum(companyLocalAmount) as localAmount,
                                                 sum(companyReportingAmount) as rptAmount, sum(transactionAmount) as transAmount, sum(VATAmount * requestedQty) as transVATAmount,quotationMasterID,soQuotationMasterID')
            ->where('soQuotationMasterID', $quotationMasterID)
            ->with(['master' => function ($query) {
                $query->with(['transaction_currency']);
            }])
            ->groupBy('quotationMasterID');

        if (!is_null($salesOrderID)) {
            $salesOrderDeatils = $salesOrderDeatils->where('quotationMasterID', $salesOrderID);
        }

        $salesOrderDeatils = $salesOrderDeatils->get();

        foreach ($salesOrderDeatils as $so) {
            $so->delivery_order = $this->getSOToCNChainForTracing($so->master, $deliveryOrderID, $custInvoiceDirectAutoID, $custReceivePaymentAutoID, $salesReturnID, $matchDocumentMasterAutoID, $creditNoteAutoID) + $this->customerInvoiceChainData(null, $so->quotationMasterID, $custInvoiceDirectAutoID, $custReceivePaymentAutoID, $salesReturnID, $matchDocumentMasterAutoID, $creditNoteAutoID);
        }

        $deliveryOrderData = $this->getSOToCNChainForTracing($quotationMaster, $deliveryOrderID, $custInvoiceDirectAutoID, $custReceivePaymentAutoID, $salesReturnID, $matchDocumentMasterAutoID, $creditNoteAutoID);

        $customerInvoiceData = $this->customerInvoiceChainData(null, $quotationMasterID, $custInvoiceDirectAutoID, $custReceivePaymentAutoID, $salesReturnID, $matchDocumentMasterAutoID, $creditNoteAutoID);

        $salesOrderData = $salesOrderDeatils->toArray() + $deliveryOrderData + $customerInvoiceData;

        $cancelStatus = ($quotationMaster->isDeleted != 0) ? " -- @Cancelled@" : "";
        $tracingData['name'] = ($documentSystemID == 67) ? "Quotation" : "Sales Order";
        if (($type == 'quo' && ($quotationMaster->quotationMasterID == $quotationMasterID && $documentSystemID == 67)) || ($type == 'so' && ($quotationMaster->quotationMasterID == $quotationMasterID && $documentSystemID == 68))) {
            $tracingData['cssClass'] = "ngx-org-step-one root-tracing-node";
        } else {
            $tracingData['cssClass'] = "ngx-org-step-one";
        }
        $tracingData['documentSystemID'] = $quotationMaster->documentSystemID;
        $tracingData['docAutoID'] = $quotationMaster->quotationMasterID;
        $tracingData['title'] = "{Doc Code :} " . $quotationMaster->quotationCode . " -- {Doc Date :} " . Carbon::parse($quotationMaster->documentDate)->format('Y-m-d') . " -- {Currency :} " . $quotationMaster->transaction_currency->CurrencyCode . "-- {Amount :} " . number_format(($quotationMaster->detail[0]->transAmount + $quotationMaster->detail[0]->transVATAmount), $quotationMaster->transaction_currency->DecimalPlaces) . $cancelStatus;


        foreach ($salesOrderData as $keySo => $valueSo) {
            $tempSo = [];
            if (isset($valueSo['master']['documentSystemID']) && $valueSo['master']['documentSystemID'] == 68) {
                $tempSo = $this->setSalesOrderChainData($valueSo, $type, $salesOrderID, $deliveryOrderID, $custInvoiceDirectAutoID, $custReceivePaymentAutoID, $salesReturnID, $matchDocumentMasterAutoID, $creditNoteAutoID);
            }

            if (isset($valueSo['master']['documentSystemID']) && $valueSo['master']['documentSystemID'] == 71) {
                $tempSo = $this->setDeliveryOrderChainData($valueSo, $type, $deliveryOrderID, $custInvoiceDirectAutoID, $custReceivePaymentAutoID, $salesReturnID, $matchDocumentMasterAutoID, $creditNoteAutoID);
            }


            if (isset($valueSo['master']['documentSystemiD']) && $valueSo['master']['documentSystemiD'] == 20) {
                $tempSo = $this->setCustomerInvoiceChainData($valueSo, $type, $custInvoiceDirectAutoID, $custReceivePaymentAutoID, $salesReturnID, $matchDocumentMasterAutoID, $creditNoteAutoID);
            }

            $tracingData['childs'][] = $tempSo;
        }

        // xxxxxxx

        $advancePayments = AdvanceReceiptDetails::selectRaw('custReceivePaymentAutoID, customerTransCurrencyID, SUM(supplierTransAmount) as transAmount')
            ->with(['master', 'customer_currency'])
            ->where('salesOrderID', $quotationMasterID)
            ->groupBy('salesOrderID');

        if (!is_null($custReceivePaymentAutoID)) {
            $advancePayments = $advancePayments->where('custReceivePaymentAutoID', $custReceivePaymentAutoID);
        }

        $advancePayments = $advancePayments->get();
        $tempAdbv = [];
        foreach ($advancePayments as $keyAd => $valueAd) {
            $tempAdPay['name'] = "Receipt Voucher";
            if ($type == 'reciptVoucher' && ($valueAd->custReceivePaymentAutoID == $custReceivePaymentAutoID)) {
                $tempAdPay['cssClass'] = "ngx-org-step-four root-tracing-node";
            } else {
                $tempAdPay['cssClass'] = "ngx-org-step-four";
            }

            $tempAdPay['documentSystemID'] = $valueAd->master->documentSystemID;
            $tempAdPay['docAutoID'] = $valueAd->master->custReceivePaymentAutoID;
            $tempAdPay['title'] = "{Doc Code :} " . $valueAd->master->custPaymentReceiveCode . " -- {Doc Date :} " . Carbon::parse($valueAd->master->custPaymentReceiveDate)->format('Y-m-d') . " -- {Currency :} " . $valueAd->customer_currency->CurrencyCode . " -- {Amount :} " . number_format($valueAd->transAmount, $valueAd->customer_currency->DecimalPlaces);

            $tracingData['childs'][] = $tempAdPay;
        }


        return $tracingData;
    }

    public function setSalesOrderChainData($valueSo, $type, $salesOrderID = null, $deliveryOrderID = null, $custInvoiceDirectAutoID = null, $custReceivePaymentAutoID = null, $salesReturnID = null, $matchDocumentMasterAutoID = null, $creditNoteAutoID = null)
    {
        $tempSo = [];
        $cancelStatus = ($valueSo['master']['isDeleted'] != 0) ? " -- @Cancelled@" : "";
        $tempSo['name'] = "Sales Order";
        if ($type == 'so' && ($valueSo['master']['quotationMasterID'] == $salesOrderID)) {
            $tempSo['cssClass'] = "ngx-org-step-two root-tracing-node";
        } else {
            $tempSo['cssClass'] = "ngx-org-step-two";
        }

        $tempSo['documentSystemID'] = $valueSo['master']['documentSystemID'];
        $tempSo['docAutoID'] = $valueSo['master']['quotationMasterID'];
        $tempSo['title'] = "{Doc Code :} " . $valueSo['master']['quotationCode'] . " -- {Doc Date :} " . Carbon::parse($valueSo['master']['documentDate'])->format('Y-m-d') . " -- {Currency :} " . $valueSo['master']['transaction_currency']['CurrencyCode'] . " -- {Amount :} " . number_format(($valueSo['transAmount'] + $valueSo['transVATAmount']), $valueSo['master']['transaction_currency']['DecimalPlaces']) . $cancelStatus;

        foreach ($valueSo['delivery_order'] as $key => $value) {
            $temp = [];

            if (isset($value['master']['documentSystemID']) && $value['master']['documentSystemID'] == 71) {
                $temp = $this->setDeliveryOrderChainData($value, $type, $deliveryOrderID, $custInvoiceDirectAutoID, $custReceivePaymentAutoID, $salesReturnID, $matchDocumentMasterAutoID, $creditNoteAutoID);
            }

            if (isset($value['master']['documentSystemiD']) && $value['master']['documentSystemiD'] == 20) {
                $temp = $this->setCustomerInvoiceChainData($value, $type, $custInvoiceDirectAutoID, $custReceivePaymentAutoID, $salesReturnID, $matchDocumentMasterAutoID, $creditNoteAutoID);
            }

            $tempSo['childs'][] = $temp;
        }

        return $tempSo;
    }


    public function setDeliveryOrderChainData($value, $type, $deliveryOrderID = null, $custInvoiceDirectAutoID = null, $custReceivePaymentAutoID = null, $salesReturnID = null, $matchDocumentMasterAutoID = null, $creditNoteAutoID = null)
    {
        $temp = [];
        $cancelStatus = "";
        $temp['name'] = "Delivery Order";
        if ($type == 'do' && ($value['master']['deliveryOrderID'] == $deliveryOrderID)) {
            $temp['cssClass'] = "ngx-org-step-three root-tracing-node";
        } else {
            $temp['cssClass'] = "ngx-org-step-three";
        }
        $temp['documentSystemID'] = $value['master']['documentSystemID'];
        $temp['docAutoID'] = $value['master']['deliveryOrderID'];
        $temp['title'] = "{Doc Code :} " . $value['master']['deliveryOrderCode'] . " -- {Doc Date :} " . Carbon::parse($value['master']['deliveryOrderDate'])->format('Y-m-d') . " -- {Currency :} " . $value['master']['transaction_currency']['CurrencyCode'] . " -- {Amount :} " . number_format(($value['transAmount'] + $value['master']['VATAmount']), $value['master']['transaction_currency']['DecimalPlaces']) . $cancelStatus;


        $salesReturnDetails = SalesReturnDetail::selectRaw('sum(companyLocalAmount) as localAmount,
                                             sum(companyReportingAmount) as rptAmount, SUM(transactionAmount) as transAmount,salesReturnID, deliveryOrderID')
            ->where('deliveryOrderID', $value['master']['deliveryOrderID'])
            ->with(['master' => function ($query) {
                $query->with(['transaction_currency']);
            }])
            ->groupBy('salesReturnID');

        if (!is_null($salesReturnID)) {
            $salesReturnDetails = $salesReturnDetails->where('salesReturnID', $salesReturnID);
        }

        $salesReturnDetails = $salesReturnDetails->get()
            ->toArray();

        if (isset($value['invoices'])) {
            foreach ($value['invoices'] as $key1 => $value1) {
                $temp1 = [];
                $temp1 = $this->setCustomerInvoiceChainData($value1, $type, $custInvoiceDirectAutoID, $custReceivePaymentAutoID, $salesReturnID, $matchDocumentMasterAutoID, $creditNoteAutoID);
                $temp['childs'][] = $temp1;
            }
        }

        foreach ($salesReturnDetails as $keySR => $valueSR) {
            $temp1 = [];
            $temp1 = $this->setSalesReturnChainData($valueSR, $type, 1, $salesReturnID, $matchDocumentMasterAutoID, $creditNoteAutoID);
            $temp['childs'][] = $temp1;
        }

        return $temp;
    }

    public function setSalesReturnChainData($value1, $type, $from, $salesReturnID = null, $matchDocumentMasterAutoID = null, $creditNoteAutoID = null)
    {
        $temp1 = [];
        $cancelStatus = "";
        $temp1['name'] = "Sales Return";
        if ($type == 'sr' && ($value1['master']['id'] == $salesReturnID)) {
            $temp1['cssClass'] = ($from == 1) ? "ngx-org-step-four root-tracing-node" : "ngx-org-step-five root-tracing-node";
        } else {
            $temp1['cssClass'] = ($from == 1) ? "ngx-org-step-four" : "ngx-org-step-five";
        }
        $temp1['documentSystemID'] = $value1['master']['documentSystemID'];
        $temp1['docAutoID'] = $value1['master']['id'];
        $temp1['title'] = "{Doc Code :} " . $value1['master']['salesReturnCode'] . " -- {Doc Date :} " . Carbon::parse($value1['master']['salesReturnDate'])->format('Y-m-d') . " -- {Currency :} " . $value1['master']['transaction_currency']['CurrencyCode'] . " -- {Amount :} " . number_format($value1['transAmount'], $value1['master']['transaction_currency']['DecimalPlaces']) . $cancelStatus;

        return $temp1;
    }

    public function setCustomerInvoiceChainData($value1, $type, $custInvoiceDirectAutoID = null, $custReceivePaymentAutoID = null, $salesReturnID = null, $matchDocumentMasterAutoID = null, $creditNoteAutoID = null)
    {
        $temp1 = [];
        $cancelStatus = ($value1['master']['canceledYN'] != 0) ? " -- @Cancelled@" : "";
        $temp1['name'] = "Customer Invoice";
        if ($type == 'inv' && ($value1['master']['custInvoiceDirectAutoID'] == $custInvoiceDirectAutoID)) {
            $temp1['cssClass'] = "ngx-org-step-four root-tracing-node";
        } else {
            $temp1['cssClass'] = "ngx-org-step-four";
        }
        $temp1['documentSystemID'] = $value1['master']['documentSystemiD'];
        $temp1['docAutoID'] = $value1['master']['custInvoiceDirectAutoID'];
        $temp1['title'] = "{Doc Code :} " . $value1['master']['bookingInvCode'] . " -- {Doc Date :} " . Carbon::parse($value1['master']['bookingDate'])->format('Y-m-d') . " -- {Currency :} " . $value1['master']['currency']['CurrencyCode'] . " -- {Amount :} " . number_format(($value1['transAmount'] + $value1['master']['VATAmount']), $value1['master']['currency']['DecimalPlaces']) . $cancelStatus;

        foreach ($value1['payments'] as $key2 => $value2) {
            $temp2 = [];
            $temp2 = $this->setReceiptPaymentChain($value2, $type, $custReceivePaymentAutoID, $salesReturnID, $matchDocumentMasterAutoID, $creditNoteAutoID);

            $temp1['childs'][] = $temp2;
        }

        $recieptVouchersMatch = CustomerReceivePaymentDetail::selectRaw('sum(receiveAmountLocal) as localAmount,
                                             sum(receiveAmountRpt) as rptAmount, SUM(receiveAmountTrans) as transAmount,bookingInvCodeSystem,matchingDocID')
            ->where('bookingInvCodeSystem', $value1['master']['custInvoiceDirectAutoID'])
            ->where('addedDocumentSystemID', 20)
            ->where('matchingDocID', '>', 0)
            ->with(['matching_master' => function ($query) {
                $query->with(['transactioncurrency']);
            }])
            ->groupBy('matchingDocID');
        if (!is_null($matchDocumentMasterAutoID)) {
            $recieptVouchersMatch = $recieptVouchersMatch->where('matchingDocID', $matchDocumentMasterAutoID);
        }

        $recieptVouchersMatch = $recieptVouchersMatch->get()
            ->toArray();

        foreach ($recieptVouchersMatch as $keyMatch => $valueMatch) {
            if (isset($valueMatch['matching_master'])) {
                $temp2x = [];
                $temp2x = $this->setReciptMatchingChain($valueMatch, $type, $custReceivePaymentAutoID, $salesReturnID, $matchDocumentMasterAutoID, $creditNoteAutoID);
                $temp1['childs'][] = $temp2x;
            }
        }


        $salesReturnDetails = SalesReturnDetail::selectRaw('sum(companyLocalAmount) as localAmount,
                                             sum(companyReportingAmount) as rptAmount, SUM(transactionAmount) as transAmount,salesReturnID, custInvoiceDirectAutoID')
            ->where('custInvoiceDirectAutoID', $value1['master']['custInvoiceDirectAutoID'])
            ->with(['master' => function ($query) {
                $query->with(['transaction_currency']);
            }])
            ->groupBy('salesReturnID');
        if (!is_null($salesReturnID)) {
            $salesReturnDetails = $salesReturnDetails->where('salesReturnID', $salesReturnID);
        }

        $salesReturnDetails = $salesReturnDetails->get()
            ->toArray();
        foreach ($salesReturnDetails as $keySR => $valueSR) {
            $temp3 = [];
            $temp3 = $this->setSalesReturnChainData($valueSR, $type, 2, $salesReturnID, $matchDocumentMasterAutoID, $creditNoteAutoID);
            $temp1['childs'][] = $temp3;
        }

        return $temp1;
    }

    public function setReceiptPaymentChain($value2, $type, $custReceivePaymentAutoID = null, $salesReturnID = null, $matchDocumentMasterAutoID = null, $creditNoteAutoID = null)
    {
        $temp2 = [];
        if ($type == 'reciptVoucher' && isset($value2['master']) && ($value2['master']['custReceivePaymentAutoID'] == $custReceivePaymentAutoID)) {
            $temp2['cssClass'] = "ngx-org-step-five root-tracing-node";
        } else {
            $temp2['cssClass'] = "ngx-org-step-five";
        }
        if (isset($value2['master'])) {
            $cancelStatus = ($value2['master']['cancelYN'] == -1) ? " -- @Cancelled@" : "";
            $temp2['name'] = "Receipt Voucher";
            $temp2['documentSystemID'] = $value2['master']['documentSystemID'];
            $temp2['docAutoID'] = $value2['master']['custReceivePaymentAutoID'];
            $temp2['title'] = "{Doc Code :} " . $value2['master']['custPaymentReceiveCode'] . " -- {Doc Date :} " . Carbon::parse($value2['master']['custPaymentReceiveDate'])->format('Y-m-d') . " -- {Currency :} " . $value2['master']['currency']['CurrencyCode'] . " -- {Amount :} " . number_format($value2['transAmount'], $value2['master']['currency']['DecimalPlaces']) . $cancelStatus;

            $creditNotes = CustomerReceivePaymentDetail::where('custReceivePaymentAutoID', $value2['master']['custReceivePaymentAutoID'])
                ->where('addedDocumentSystemID', 19)
                ->where('matchingDocID', 0)
                ->with(['credit_note' => function ($query) {
                    $query->with(['currency']);
                }]);

            if (!is_null($creditNoteAutoID)) {
                $creditNotes = $creditNotes->where('bookingInvCodeSystem', $creditNoteAutoID);
            }

            $creditNotes = $creditNotes->get();

            foreach ($creditNotes as $keyCN => $valueCN) {
                if (isset($valueCN['credit_note'])) {
                    $tempCN = [];
                    $tempCN['name'] = "Credit Note";
                    if ($type == 'cn' && ($valueCN['credit_note']['creditNoteAutoID'] == $creditNoteAutoID)) {
                        $tempCN['cssClass'] = "ngx-org-step-six root-tracing-node";
                    } else {
                        $tempCN['cssClass'] = "ngx-org-step-six";
                    }
                    $tempCN['documentSystemID'] = $valueCN['credit_note']['documentSystemiD'];
                    $tempCN['docAutoID'] = $valueCN['credit_note']['creditNoteAutoID'];
                    $tempCN['title'] = "{Doc Code :} " . $valueCN['credit_note']['creditNoteCode'] . " -- {Doc Date :} " . Carbon::parse($valueCN['credit_note']['creditNoteDate'])->format('Y-m-d') . " -- {Currency :} " . $valueCN['credit_note']['currency']['CurrencyCode'] . " -- {Amount :} " . number_format($valueCN['credit_note']['creditAmountTrans'], $valueCN['credit_note']['currency']['DecimalPlaces']);

                    $temp2['childs'][] = $tempCN;
                }
            }
        }

        return $temp2;
    }

    public function setReciptMatchingChain($value2, $type, $custReceivePaymentAutoID = null, $salesReturnID = null, $matchDocumentMasterAutoID = null, $creditNoteAutoID = null)
    {
        $temp2 = [];
        if (isset($value2['matching_master'])) {
            if ($type == 'reciptVoucherMatching' && ($value2['matching_master']['matchDocumentMasterAutoID'] == $matchDocumentMasterAutoID)) {
                $temp2['cssClass'] = "ngx-org-step-five root-tracing-node";
            } else {
                $temp2['cssClass'] = "ngx-org-step-five";
            }
            $cancelStatus = "";
            $temp2['name'] = "Receipt Matching";
            $temp2['documentSystemID'] = 70;
            $temp2['docAutoID'] = $value2['matching_master']['matchDocumentMasterAutoID'];
            $temp2['title'] = "{Doc Code :} " . $value2['matching_master']['matchingDocCode'] . " -- {Doc Date :} " . Carbon::parse($value2['matching_master']['matchingDocdate'])->format('Y-m-d') . " -- {Currency :} " . $value2['matching_master']['transactioncurrency']['CurrencyCode'] . " -- {Amount :} " . number_format($value2['transAmount'], $value2['matching_master']['transactioncurrency']['DecimalPlaces']) . $cancelStatus;

            $creditNotes = MatchDocumentMaster::where('matchDocumentMasterAutoID', $value2['matching_master']['matchDocumentMasterAutoID'])
                ->where('documentSystemID', 19)
                ->with(['credit_note' => function ($query) {
                    $query->with(['currency']);
                }]);

            if (!is_null($creditNoteAutoID)) {
                $creditNotes = $creditNotes->where('PayMasterAutoId', $creditNoteAutoID);
            }

            $creditNotes = $creditNotes->get();

            foreach ($creditNotes as $keyCN => $valueCN) {
                if (isset($valueCN['credit_note'])) {
                    $tempCN = [];
                    $tempCN['name'] = "Credit Note";
                    if ($type == 'cn' && ($valueCN['credit_note']['creditNoteAutoID'] == $creditNoteAutoID)) {
                        $tempCN['cssClass'] = "ngx-org-step-six root-tracing-node";
                    } else {
                        $tempCN['cssClass'] = "ngx-org-step-six";
                    }
                    $tempCN['documentSystemID'] = $valueCN['credit_note']['documentSystemiD'];
                    $tempCN['docAutoID'] = $valueCN['credit_note']['creditNoteAutoID'];
                    $tempCN['title'] = "{Doc Code :} " . $valueCN['credit_note']['creditNoteCode'] . " -- {Doc Date :} " . Carbon::parse($valueCN['credit_note']['creditNoteDate'])->format('Y-m-d') . " -- {Currency :} " . $valueCN['credit_note']['currency']['CurrencyCode'] . " -- {Amount :} " . number_format($valueCN['credit_note']['creditAmountTrans'], $valueCN['credit_note']['currency']['DecimalPlaces']);

                    $temp2['childs'][] = $tempCN;
                }
            }

            $reciptVouchers = MatchDocumentMaster::where('matchDocumentMasterAutoID', $value2['matching_master']['matchDocumentMasterAutoID'])
                ->where('documentSystemID', 21)
                ->with(['reciept_voucher' => function ($query) {
                    $query->with(['currency']);
                }]);

            if (!is_null($custReceivePaymentAutoID)) {
                $reciptVouchers = $reciptVouchers->where('PayMasterAutoId', $custReceivePaymentAutoID);
            }

            $reciptVouchers = $reciptVouchers->get();

            foreach ($reciptVouchers as $keyCN => $valueCN) {
                if (isset($valueCN['reciept_voucher'])) {
                    $tempCN = [];
                    $tempCN['name'] = "Receipt Voucher";
                    if ($type == 'reciptVoucher' && ($valueCN['reciept_voucher']['custReceivePaymentAutoID'] == $custReceivePaymentAutoID)) {
                        $tempCN['cssClass'] = "ngx-org-step-six root-tracing-node";
                    } else {
                        $tempCN['cssClass'] = "ngx-org-step-six";
                    }
                    $tempCN['documentSystemID'] = $valueCN['reciept_voucher']['documentSystemID'];
                    $tempCN['docAutoID'] = $valueCN['reciept_voucher']['custReceivePaymentAutoID'];
                    $tempCN['title'] = "{Doc Code :} " . $valueCN['reciept_voucher']['custPaymentReceiveCode'] . " -- {Doc Date :} " . Carbon::parse($valueCN['reciept_voucher']['custPaymentReceiveDate'])->format('Y-m-d') . " -- {Currency :} " . $valueCN['reciept_voucher']['currency']['CurrencyCode'] . " -- {Amount :} " . number_format($valueCN['reciept_voucher']['netAmount'], $valueCN['reciept_voucher']['currency']['DecimalPlaces']);

                    // $temp2['childs'][] = $tempCN;
                }
            }
        }

        return $temp2;
    }

    public function getSOToCNChainForTracing($salesOrder, $deliveryOrderID = null, $custInvoiceDirectAutoID = null, $custReceivePaymentAutoID = null, $salesReturnID = null, $matchDocumentMasterAutoID = null, $creditNoteAutoID = null)
    {
        $deliveryOrderDetails = DeliveryOrderDetail::selectRaw('sum(companyLocalAmount) as localAmount,
                                                 sum(companyReportingAmount) as rptAmount, sum(transactionAmount) as transAmount,quotationMasterID,deliveryOrderID')
            ->where('quotationMasterID', $salesOrder->quotationMasterID)
            ->with(['master' => function ($query) {
                $query->with(['transaction_currency']);
            }])
            ->groupBy('deliveryOrderID');

        if (!is_null($deliveryOrderID)) {
            $deliveryOrderDetails = $deliveryOrderDetails->where('deliveryOrderID', $deliveryOrderID);
        }

        $deliveryOrderDetails = $deliveryOrderDetails->get();

        foreach ($deliveryOrderDetails as $key1 => $deliveryOrder) {
            $deliveryOrder->invoices = $this->customerInvoiceChainData($deliveryOrder->deliveryOrderID, null, $custInvoiceDirectAutoID, $custReceivePaymentAutoID, $salesReturnID, $matchDocumentMasterAutoID, $creditNoteAutoID);
        }

        return $deliveryOrderDetails->toArray();
    }

    public function customerInvoiceChainData($deliveryOrderID = null, $quotationID = null, $custInvoiceDirectAutoID = null, $custReceivePaymentAutoID = null, $salesReturnID = null, $matchDocumentMasterAutoID = null, $creditNoteAutoID = null)
    {
        $invoices = CustomerInvoiceItemDetails::selectRaw('sum(issueCostLocalTotal) as localAmount,
                                                 sum(issueCostRptTotal) as rptAmount, sum(sellingTotal) as transAmount,custInvoiceDirectAutoID,deliveryOrderID');

        if (!is_null($deliveryOrderID)) {
            $invoices = $invoices->where('deliveryOrderID', $deliveryOrderID);
        } else if (!is_null($quotationID)) {
            $invoices = $invoices->where('quotationMasterID', $quotationID);
        }

        if (!is_null($custInvoiceDirectAutoID)) {
            $invoices = $invoices->where('custInvoiceDirectAutoID', $custInvoiceDirectAutoID);
        }

        $invoices = $invoices->with(['master' => function ($query) {
            $query->with(['currency']);
        }])
            ->groupBy('custInvoiceDirectAutoID')
            ->get();


        foreach ($invoices as $invoice) {
            $recieptVouchers = CustomerReceivePaymentDetail::selectRaw('sum(receiveAmountLocal) as localAmount,
                                             sum(receiveAmountRpt) as rptAmount, SUM(receiveAmountTrans) as transAmount,bookingInvCodeSystem,addedDocumentSystemID,matchingDocID, custReceivePaymentAutoID')
                ->where('bookingInvCodeSystem', $invoice->custInvoiceDirectAutoID)
                ->where('addedDocumentSystemID', 20)
                ->where('matchingDocID', 0)
                ->with(['master' => function ($query) {
                    $query->with(['currency']);
                }])
                ->groupBy('custReceivePaymentAutoID');


            if (!is_null($custReceivePaymentAutoID)) {
                $recieptVouchers = $recieptVouchers->where('custReceivePaymentAutoID', $custReceivePaymentAutoID);
            }

            $recieptVouchers = $recieptVouchers->get();

            $totalInvoices = $recieptVouchers->toArray();

            $invoice->payments = $totalInvoices;
        }

        return $invoices->toArray();
    }
    public function getAssetTransferTracingData($assetTransferID)
    {

        $assetTransfer = ERPAssetTransfer::where('id', $assetTransferID)->first();

        $tracingData = [];
        $tracingData['name'] = "Asset Transfer";
        $tracingData['documentSystemID'] = 103;
        $tracingData['docAutoID'] = $assetTransferID;
        if (($assetTransfer->id == $assetTransferID)) {
            $tracingData['cssClass'] = "ngx-org-step-one root-tracing-node";
        } else {
            $tracingData['cssClass'] = "ngx-org-step-one";
        }
        $tracingData['title'] = "{Doc Code :} " . $assetTransfer->document_code . " -- {Doc Date :} " . Carbon::parse($assetTransfer->document_date)->format('Y-m-d') . " -- {Currency :} " . "-- {Amount :} ";

        $purchaseRequestID = $assetTransfer->purchaseRequestID;

        $purchaseRequest = PurchaseRequest::where('purchaseRequestID', $purchaseRequestID)
            ->with(['currency_by'])
            ->first();

        if (!empty($purchaseRequest)) {
            $cancelStatus =   (isset($purchaseRequest->cancelledYN) && $purchaseRequest->cancelledYN == -1)  ? " -- @Cancelled@" : "";
            $tempPR['name'] = "Purchase Request";
            $tempPR['cssClass'] = "ngx-org-step-two";

            $tempPR['documentSystemID'] = $purchaseRequest->documentSystemID;
            $tempPR['docAutoID'] = $purchaseRequest->purchaseRequestID;
            $tempPR['title'] = "{Doc Code :} " . $purchaseRequest->purchaseRequestCode . " -- {Doc Date :} " . Carbon::parse($purchaseRequest->PRRequestedDate)->format('Y-m-d') . " -- {Currency :} " . $purchaseRequest->currency_by->CurrencyCode . "-- {Amount :} " . number_format($purchaseRequest->poTotalSupplierTransactionCurrency, $purchaseRequest->currency_by->DecimalPlaces) . $cancelStatus;

            $poMasters = PurchaseOrderDetails::selectRaw('sum(netAmount) as totalAmount,
             purchaseRequestID,purchaseOrderMasterID')
                ->where('purchaseRequestID', $purchaseRequest->purchaseRequestID)
                ->with(['order' => function ($query) {
                    $query->with(['currency']);
                }])
                ->groupBy('purchaseOrderMasterID');
            $poMasters = $poMasters->get();
            foreach ($poMasters as $po) {
                $po->grv = $this->getPOtoPaymentChainForTracing($po->order, null, null, 'PR', null);
            }

            $poData = $poMasters->toArray();

            foreach ($poData as $keyPo => $valuePo) {
                $cancelStatus = ($valuePo['order']['poCancelledYN'] == -1) ? " -- @Cancelled@" : "";
                $tempPO = [];
                $tempPO['name'] = "Purchase Order";
                $tempPO['cssClass'] = "ngx-org-step-three";
                $tempPO['documentSystemID'] = $valuePo['order']['documentSystemID'];
                $tempPO['docAutoID'] = $valuePo['order']['purchaseOrderID'];
                $tempPO['title'] = "{Doc Code :} " . $valuePo['order']['purchaseOrderCode'] . " -- {Doc Date :} " . Carbon::parse($valuePo['order']['expectedDeliveryDate'])->format('Y-m-d') . " -- {Currency :} " . $valuePo['order']['currency']['CurrencyCode'] . " -- {Amount :} " . number_format($valuePo['order']['poTotalSupplierTransactionCurrency'], $valuePo['order']['currency']['DecimalPlaces']) . $cancelStatus;

                foreach ($valuePo['grv'] as $key => $value) {
                    $cancelStatus = ($value['grv_master']['grvCancelledYN'] == -1) ? " -- @Cancelled@" : "";
                    $tempGRV = [];
                    $tempGRV['name'] = "Good Receipt Voucher";
                    $tempGRV['cssClass'] = "ngx-org-step-four";
                    $tempGRV['documentSystemID'] = $value['grv_master']['documentSystemID'];
                    $tempGRV['docAutoID'] = $value['grv_master']['grvAutoID'];
                    $tempGRV['title'] = "{Doc Code :} " . $value['grv_master']['grvPrimaryCode'] . " -- {Doc Date :} " . Carbon::parse($value['grv_master']['grvDate'])->format('Y-m-d') . " -- {Currency :} " . $value['grv_master']['currency_by']['CurrencyCode'] . " -- {Amount :} " . number_format($value['grv_master']['grvTotalSupplierTransactionCurrency'], $value['grv_master']['currency_by']['DecimalPlaces']) . $cancelStatus;

                    foreach ($value['invoices'] as $key1 => $value1) {
                        $cancelStatus = ($value1['suppinvmaster']['cancelYN'] == -1) ? " -- @Cancelled@" : "";
                        $suppINV = [];
                        $suppINV['name'] = "Supplier Invoice";
                        $suppINV['cssClass'] = "ngx-org-step-five";
                        $suppINV['documentSystemID'] = $value1['suppinvmaster']['documentSystemID'];
                        $suppINV['docAutoID'] = $value1['suppinvmaster']['bookingSuppMasInvAutoID'];
                        $suppINV['title'] = "{Doc Code :} " . $value1['suppinvmaster']['bookingInvCode'] . " -- {Doc Date :} " . Carbon::parse($value1['suppinvmaster']['bookingDate'])->format('Y-m-d') . " -- {Currency :} " . $value1['suppinvmaster']['transactioncurrency']['CurrencyCode'] . " -- {Amount :} " . number_format($value1['suppinvmaster']['bookingAmountTrans'], $value1['suppinvmaster']['transactioncurrency']['DecimalPlaces']) . $cancelStatus;

                        foreach ($value1['payments'] as $key2 => $value2) {
                            $temp2 = [];
                            if (isset($value2['payment_master'])) {
                                $temp2['cssClass'] = "ngx-org-step-six";
                                $cancelStatus = ($value2['payment_master']['cancelYN'] == -1) ? " -- @Cancelled@" : "";
                                $temp2['name'] = "Payment";
                                $temp2['documentSystemID'] = $value2['payment_master']['documentSystemID'];
                                $temp2['docAutoID'] = $value2['payment_master']['PayMasterAutoId'];
                                $temp2['title'] = "{Doc Code :} " . $value2['payment_master']['BPVcode'] . " -- {Doc Date :} " . Carbon::parse($value2['payment_master']['BPVdate'])->format('Y-m-d') . " -- {Currency :} " . $value2['payment_master']['transactioncurrency']['CurrencyCode'] . " -- {Amount :} " . number_format($value2['payment_master']['payAmountSuppTrans'], $value2['payment_master']['transactioncurrency']['DecimalPlaces']) . $cancelStatus;
                            }
                            $suppINV['childs'][] = $temp2;
                        }
                        $tempGRV['childs'][] =  $suppINV;
                    }
                    $tempPO['childs'][] =  $tempGRV;
                }
                $tempPR['childs'][] = $tempPO;
            }
            $tracingData['childs'][] = $tempPR;
        }
        return [$tracingData];
    }

    public function downloadPoItemUploadTemplate(Request $request)
    {
        $input = $request->all();
        $disk = Helper::policyWiseDisk($input['companySystemID']);
        $isProject_base = CompanyPolicyMaster::where('companyPolicyCategoryID', 56)
            ->where('companySystemID', $input['companySystemID'])
            ->where('isYesNO', 1)
            ->exists();
        if ($isProject_base) {
            if ($exists = Storage::disk($disk)->exists('procument_order_item_upload_template/procument_order_item_upload_project_template.xlsx')) {
                return Storage::disk($disk)->download('procument_order_item_upload_template/procument_order_item_upload_project_template.xlsx', 'procument_order_item_upload_template.xlsx');
            } else {
                return $this->sendError('Attachments not found', 500);
            }
        } else {
            if ($exists = Storage::disk($disk)->exists('procument_order_item_upload_template/procument_order_item_upload_template.xlsx')) {
                return Storage::disk($disk)->download('procument_order_item_upload_template/procument_order_item_upload_template.xlsx', 'procument_order_item_upload_template.xlsx');
            } else {
                return $this->sendError('Attachments not found', 500);
            }
        }
    }

    public function poItemsUpload(request $request)
    {
        DB::beginTransaction();
        try {
            $input = $request->all();
            $excelUpload = $input['itemExcelUpload'];
            $input = array_except($request->all(), 'itemExcelUpload');
            $input = $this->convertArrayToValue($input);

            $decodeFile = base64_decode($excelUpload[0]['file']);
            $originalFileName = $excelUpload[0]['filename'];
            $extension = $excelUpload[0]['filetype'];
            $size = $excelUpload[0]['size'];

            $purchaseOrder = ProcumentOrder::where('purchaseOrderID', $input['requestID'])
                ->first();

            if (empty($purchaseOrder)) {
                return $this->sendError('Procument Order not found', 500);
            }

            $allowedExtensions = ['xlsx','xls'];

            if (!in_array($extension, $allowedExtensions))
            {
                return $this->sendError('This type of file not allow to upload.you can only upload .xlsx (or) .xls',500);
            }

            if ($size > 20000000) {
                return $this->sendError('The maximum size allow to upload is 20 MB',500);
            }

            $disk = 'local';
            Storage::disk($disk)->put($originalFileName, $decodeFile);

            $filePath = Storage::disk($disk)->path($originalFileName);
            $spreadsheet = IOFactory::load($filePath);

            $sheet = $spreadsheet->getActiveSheet();

            $sheet->removeRow(1, 6);

            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save($filePath);

            $formatChk = \Excel::selectSheetsByIndex(0)->load($filePath, function ($reader) {})->get();

            $uniqueData = array_filter(collect($formatChk)->toArray());

            $validateHeaderCode = false;
            $totalItemCount = 0;

            $allowItemToTypePolicy = false;
            $allowItemToType = CompanyPolicyMaster::where('companyPolicyCategoryID', 64)
                ->where('companySystemID', $purchaseOrder->companySystemID)
                ->first();

            if ($allowItemToType) {
                if ($allowItemToType->isYesNO) {
                    $allowItemToTypePolicy = true;
                }
            }

            $excelHeaders = array_keys(array_merge(...$uniqueData));
            $isProject_base = CompanyPolicyMaster::where('companyPolicyCategoryID', 56)
                ->where('companySystemID', $purchaseOrder->companySystemID)
                ->where('isYesNO', 1)
                ->exists();
            if ($isProject_base) {
                $templateHeaders = ['item_code', 'no_qty', 'unit_cost', 'comments', 'dis_percentage', 'vat_percentage', 'project', 'client_ref_no'];
            } else {
                $templateHeaders = ['item_code', 'no_qty', 'unit_cost', 'comments', 'dis_percentage', 'vat_percentage', 'client_ref_no'];
            }
            $unexpectedHeader = array_diff($excelHeaders, $templateHeaders);

            if ($unexpectedHeader) {
                return $this->sendError('Upload failed due to changes made in the Excel template', 500);
            }

            foreach ($uniqueData as $key => $value) {
                if (isset($value['item_code']) ||  $allowItemToTypePolicy) {
                    $validateHeaderCode = true;
                }

                if ((isset($value['item_code']) && !is_null($value['item_code'])) || isset($value['no_qty']) && !is_null($value['no_qty']) || isset($value['unit_cost']) && !is_null($value['unit_cost'])) {
                    $totalItemCount = $totalItemCount + 1;
                }
            }

            if (!$validateHeaderCode || !$validateHeaderCode) {
                return $this->sendError('Items cannot be uploaded, as there are null values found', 500);
            }

            $record = \Excel::selectSheetsByIndex(0)->load(Storage::disk($disk)->url('app/' . $originalFileName), function ($reader) {
            })->select(array('item_code', 'no_qty', 'unit_cost', 'comments', 'dis_percentage', 'vat_percentage', 'project', 'client_ref_no'))->get()->toArray();

            if ($purchaseOrder->cancelledYN == -1) {
                return $this->sendError('This Purchase Order already closed. You can not add.', 500);
            }

            if ($purchaseOrder->approved == 1) {
                return $this->sendError('This Purchase Order fully approved. You can not add.', 500);
            }

            if (count($record) > 0) {
                $data['isBulkItemJobRun'] = 1;
                ProcumentOrder::where('purchaseOrderID', $purchaseOrder->purchaseOrderID)->update($data);

                $db = isset($input['db']) ? $input['db'] : "";
                AddMultipleItems::dispatch(array_filter($record),($purchaseOrder->toArray()),$db,Auth::id());
            } else {
                return $this->sendError('No Records found!', 500);
            }

            DB::commit();
            return $this->sendResponse([], 'Items uploaded Successfully!!');
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage());
        }
    }

    public function updateExemptVATPos(Request $request)
    {
        $input = $request->all();

        $exemptVATPO = PurchaseOrderDetails::with(['order'])
            ->whereHas('vat_sub_category', function($query) {
                $query->where('subCatgeoryType', 3);
            })
            ->whereHas('order')
            ->get();

        DB::beginTransaction();
        try {
            $grvIds = [];
            foreach ($exemptVATPO as $key => $value) {
                if (TaxService::checkPOVATEligible($value->order->supplierVATEligible, $value->order->vatRegisteredYN)) {

                    $supplierCurrencyDecimalPlace = \Helper::getCurrencyDecimalPlace($value->order->supplierTransactionCurrencyID);
                    //getting total sum of PO detail Amount
                    $poMasterSum = PurchaseOrderDetails::select(DB::raw('COALESCE(SUM(netAmount),0) as masterTotalSum'))
                        ->where('purchaseOrderMasterID', $value->order->purchaseOrderID)
                        ->first();

                    $poMasterSumRounded = round($poMasterSum['masterTotalSum'], $supplierCurrencyDecimalPlace);



                    $calculateItemDiscount = 0;
                    if ($value->order->poDiscountAmount > 0 && $poMasterSumRounded > 0 && $value->noQty) {
                        $calculateItemDiscount = ((($value->netAmount - (($value->netAmount / $poMasterSumRounded) * $value->order->poDiscountAmount))) / $value->noQty);
                    } else {
                        $calculateItemDiscount = $value->unitCost - $value->discountAmount;
                    }

                    if (!$value->order->vatRegisteredYN) {
                        $calculateItemDiscount = $calculateItemDiscount + $value->VATAmount;
                    } else {
                        $checkVATCategory = TaxVatCategories::with(['type'])->find($value->vatSubCategoryID);
                        if ($checkVATCategory) {
                            if (isset($checkVATCategory->type->id) && $checkVATCategory->type->id == 1 && $value->exempt_vat_portion > 0 && $value->VATAmount > 0) {
                                $exemptVAT = $value->VATAmount * ($value->exempt_vat_portion / 100);

                                $calculateItemDiscount = $calculateItemDiscount + $exemptVAT;
                            } else if (isset($checkVATCategory->type->id) && $checkVATCategory->type->id == 3) {
                                $calculateItemDiscount = $calculateItemDiscount + $value->VATAmount;
                            }
                        }
                    }

                    // $calculateItemTax = (($itemDiscont['VATPercentage'] / 100) * $calculateItemDiscount) + $calculateItemDiscount;
                    $vatLineAmount = $value->VATAmount; //($calculateItemTax - $calculateItemDiscount);

                    $currencyConversion = \Helper::currencyConversion($value->companySystemID, $value->order->supplierTransactionCurrencyID, $value->order->supplierTransactionCurrencyID, $calculateItemDiscount);

                    $currencyConversionForLineAmount = \Helper::currencyConversion($value->companySystemID, $value->order->supplierTransactionCurrencyID, $value->order->supplierTransactionCurrencyID, $vatLineAmount);

                    $currencyConversionLineDefault = \Helper::currencyConversion($value->order->companySystemID, $value->order->supplierTransactionCurrencyID, $value->order->supplierDefaultCurrencyID, $calculateItemDiscount);


                    $poUpdateData = [
                        'GRVcostPerUnitLocalCur' => \Helper::roundValue($currencyConversion['localAmount']),
                        'GRVcostPerUnitSupDefaultCur' => \Helper::roundValue($currencyConversionLineDefault['documentAmount']),
                        'GRVcostPerUnitSupTransCur' => \Helper::roundValue($calculateItemDiscount),
                        'GRVcostPerUnitComRptCur' => \Helper::roundValue($currencyConversion['reportingAmount']),
                        'purchaseRetcostPerUniSupDefaultCur' => \Helper::roundValue($currencyConversionLineDefault['documentAmount']),
                        'purchaseRetcostPerUnitLocalCur' => \Helper::roundValue($currencyConversion['localAmount']),
                        'purchaseRetcostPerUnitTranCur' => \Helper::roundValue($calculateItemDiscount),
                        'purchaseRetcostPerUnitRptCur' => \Helper::roundValue($currencyConversion['reportingAmount'])
                    ];


                    PurchaseOrderDetails::where('purchaseOrderDetailsID', $value->purchaseOrderDetailsID)
                        ->update($poUpdateData);


                    $grvDetail = GRVDetails::where('purchaseOrderDetailsID', $value->purchaseOrderDetailsID)
                        ->first();

                    if ($grvDetail) {
                        if ($grvDetail->landingCost_TransCur >= $grvDetail->GRVcostPerUnitSupTransCur) {

                            $oldLandingTrans = $grvDetail->landingCost_TransCur - $grvDetail->GRVcostPerUnitSupTransCur;
                            $oldLandingLocal = $grvDetail->landingCost_LocalCur - $grvDetail->GRVcostPerUnitLocalCur;
                            $oldLandingRpt = $grvDetail->landingCost_RptCur - $grvDetail->GRVcostPerUnitComRptCur;

                            $totalNetcost = $poUpdateData['GRVcostPerUnitSupTransCur'] * $grvDetail->noQty;

                            $grvDetail->unitCost = $poUpdateData['GRVcostPerUnitSupTransCur'];

                            $grvDetail->GRVcostPerUnitLocalCur = $poUpdateData['GRVcostPerUnitLocalCur'];
                            $grvDetail->GRVcostPerUnitSupDefaultCur = $poUpdateData['GRVcostPerUnitSupDefaultCur'];
                            $grvDetail->GRVcostPerUnitSupTransCur = $poUpdateData['GRVcostPerUnitSupTransCur'];
                            $grvDetail->GRVcostPerUnitComRptCur = $poUpdateData['GRVcostPerUnitComRptCur'];

                            $grvDetail->netAmount = $totalNetcost;

                            $grvDetail->landingCost_TransCur = \Helper::roundValue($oldLandingTrans) + $poUpdateData['GRVcostPerUnitSupTransCur'];
                            $grvDetail->landingCost_LocalCur = \Helper::roundValue($oldLandingLocal) + $poUpdateData['GRVcostPerUnitLocalCur'];
                            $grvDetail->landingCost_RptCur = \Helper::roundValue($oldLandingRpt) + $poUpdateData['GRVcostPerUnitComRptCur'];

                            $grvDetail->save();

                            $grvIds[] = $grvDetail->grvAutoID;

                        }
                    }

                }
            }

            $uniqueGrvIds = array_unique($grvIds);

            foreach ($uniqueGrvIds as $key => $value) {

                $grvData = GRVMaster::find($value);

                if ($grvData) {
                    $generalLedger = GeneralLedger::where('documentSystemID', 3)
                        ->where('documentSystemCode', $value)
                        ->first();

                    if ($generalLedger) {
                        $updateData = [
                            'documentDate' => $generalLedger->documentDate,
                            'createdDateTime' => $generalLedger->createdDateTime,
                            'timestamp' => $generalLedger->timestamp,
                            'createdUserSystemID' => $generalLedger->createdUserSystemID
                        ];

                        $deleteGL = GeneralLedger::where('documentSystemID', 3)
                            ->where('documentSystemCode', $value)
                            ->delete();

                        $masterData = [
                            'documentSystemID' => $grvData->documentSystemID,
                            'autoID' => $value,
                            'companySystemID' => $grvData->companySystemID,
                            'employeeSystemID' => $updateData['createdUserSystemID']
                        ];

                        GeneralLedgerInsert::dispatch($masterData);

                        $generalLedger = GeneralLedger::where('documentSystemID', 3)
                            ->where('documentSystemCode', $value)
                            ->update($updateData);
                    }
                }
            }

            DB::commit();
            return $this->sendResponse([], 'Exempt VAT Gl uploaded Successfully!!');
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage());
        }
    }

    public function checkBudgetCutOffForPo(Request $request)
    {
        $input = $request->all();

        $purchaseOrder = ProcumentOrder::find($input['purchaseOrderID']);

        if (!$purchaseOrder) {
            return $this->sendError("Purchase Order not found");
        }

        $checkBudget = CompanyPolicyMaster::where('companyPolicyCategoryID', 17)
            ->where('companySystemID', $purchaseOrder->companySystemID)
            ->first();


        $notifyCutOffDate = false;
        $notifyCutOffDateMessages = [];
        if ($checkBudget && $checkBudget->isYesNO) {
            $budgetConsumedData = BudgetConsumptionService::getConsumptionData($input['documentSystemID'], $input['purchaseOrderID']);

            if (count($budgetConsumedData['budgetmasterIDs']) > 0) {
                $budgetIds = array_unique($budgetConsumedData['budgetmasterIDs']);

                foreach ($budgetIds as $key => $value) {
                    $budgetMaster = BudgetMaster::with(['finance_year_by'])->find($value);

                    if ($budgetMaster && $budgetMaster->finance_year_by) {
                        $cutOffDate = Carbon::parse($budgetMaster->finance_year_by->endingDate)->addMonthsNoOverflow($budgetMaster->cutOffPeriod);

                        if (Carbon::parse($purchaseOrder->expectedDeliveryDate) > $cutOffDate) {
                            $notifyCutOffDate = true;
                            $notifyCutOffDateMessages[] = "Expected delivery date ".Carbon::parse($purchaseOrder->expectedDeliveryDate)->format('d/m/Y')." of this document is greater than budget cutoff date ".$cutOffDate->format('d/m/Y');
                        }
                    }
                }
            }
        }

        return $this->sendResponse(['notifyCutOffDate' => $notifyCutOffDate, 'messages' => $notifyCutOffDateMessages], 'cut off date checked successfully!!');
    }

    public function procumentOrderTotals(Request $request)
    {
        $input = $request->all();

        $purchaseOrderID = $input['purchaseOrderID'];

        $totalSubOrderAmountPreview = PurchaseOrderDetails::where('purchaseOrderMasterID', $purchaseOrderID)
            ->sum('netAmount');

        $totalVat = PurchaseOrderDetails::selectRaw('SUM(VATAmount * noQty) as totalVat')
            ->where('purchaseOrderMasterID', $purchaseOrderID)->first()
            ->totalVat;
        if(empty($totalVat)){
            $totalVat = 0;
        }

        $procumentArray = (['totalSubOrderAmountPreview' => $totalSubOrderAmountPreview, 'totalVat' => $totalVat]);

        return $this->sendResponse($procumentArray, 'Data retrieved successfully');
    }

    public function poConfigDescriptionUpdate($id, Request $request){

        $input = $request->all();

        $paymentTermConfig = DB::table('po_wise_payment_term_config')->find($id);

        if (empty($paymentTermConfig)) {
            return $this->sendError('Payment Term Config not found');
        }

        $paymentTermConfig = DB::table('po_wise_payment_term_config')
            ->where('id', $id)
            ->update(['description' => $input['description'], 'isConfigUpdate' => true]);

        return $this->sendResponse($paymentTermConfig, 'Description updated successfully');

    }

    public function updatePoConfigSelection(Request $request){

        $input = $request->all();

        $paymentTermConfig = DB::table('po_wise_payment_term_config')->find($input['id']);

        if (empty($paymentTermConfig)) {
            return $this->sendError('Payment Term Config not found');
        }

        $paymentTermConfig = DB::table('po_wise_payment_term_config')
            ->where('id', $input['id'])
            ->update(['isSelected' => $input['isSelected']]);

        return $this->sendResponse($paymentTermConfig, 'Payment term config updated successfully');

    }
}
