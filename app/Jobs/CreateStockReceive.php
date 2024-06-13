<?php

namespace App\Jobs;

use App\Models\Company;
use App\Models\CompanyFinancePeriod;
use App\Models\CompanyFinanceYear;
use App\Models\CompanyPolicyMaster;
use App\Models\CustomerInvoiceDirect;
use App\Models\WarehouseMaster;
use App\Models\CustomerMaster;
use App\Models\DocumentApproved;
use App\Models\SystemGlCodeScenarioDetail;
use App\Models\DocumentMaster;
use App\Models\GeneralLedger;
use App\Models\ChartOfAccount;
use App\Models\SegmentMaster;
use App\Models\StockReceive;
use App\Models\StockReceiveDetails;
use App\Models\StockTransfer;
use App\Models\StockTransferDetails;
use App\Models\InterCompanyStockTransfer;
use App\Repositories\AccountsReceivableLedgerRepository;
use App\Repositories\CustomerInvoiceDirectDetailRepository;
use App\Repositories\CustomerInvoiceDirectRepository;
use App\Repositories\StockReceiveDetailsRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CreateStockReceive implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $stMaster;
    protected $dataBase;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($stMaster, $dataBase)
    {
        $this->stMaster = $stMaster;
        $this->dataBase = $dataBase;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(CustomerInvoiceDirectRepository $customerInvoiceRep,
                           CustomerInvoiceDirectDetailRepository $customerInvoiceDetailRep,
                           AccountsReceivableLedgerRepository $accountsReceivableLedgerRep,
                           StockReceiveDetailsRepository $stockReceiveDetailsRepo)
    {
        Log::useFiles(storage_path() . '/logs/create_stock_receive_jobs.log');
        $st = $this->stMaster;
        $stMaster = StockTransfer::where('stockTransferAutoID', $st->stockTransferAutoID)->first();
        if (!empty($stMaster)) {
            DB::beginTransaction();
            try {
                Log::info('Successfully start  stock_receive' . date('H:i:s'));
                $today = date('Y-m-d H:i:s');
                $stDetails = StockTransferDetails::where("stockTransferAutoID", $stMaster->stockTransferAutoID)->get();

                if ($stMaster->interCompanyTransferYN == -1 && $stMaster->approved == -1) {
                    $customerInvoiceData = array();
                    $customerInvoiceData['transactionMode'] = null;
                    $customerInvoiceData['companySystemID'] = $stMaster->companyFromSystemID;
                    $customerInvoiceData['companyID'] = $stMaster->companyFrom;
                    $customerInvoiceData['documentSystemiD'] = 20;
                    $customerInvoiceData['documentID'] = 'INV';
                    $fromCompanyFinancePeriod = CompanyFinancePeriod::where('companySystemID', $stMaster->companyFromSystemID)
                        ->where('departmentSystemID', 4)
                        ->where('isActive', -1)
                        //->where('dateFrom', '<', $stMaster->tranferDate)
                        //->where('dateTo', '>', $stMaster->tranferDate)
                        ->where('isCurrent', -1)
                        ->first();

                    $comment = "Inter Company Stock Transfer from " . $stMaster->companyFrom . " to " . $stMaster->companyTo . " " . $stMaster->stockTransferCode;

                    if (!empty($fromCompanyFinancePeriod)) {
                        $fromCompanyFinanceYear = CompanyFinanceYear::where('companyFinanceYearID', $fromCompanyFinancePeriod->companyFinanceYearID)
                            ->where('companySystemID', $stMaster->companyFromSystemID)
                            ->first();

                        if (!empty($fromCompanyFinanceYear)) {
                            $customerInvoiceData['FYBiggin'] = $fromCompanyFinanceYear->bigginingDate;
                            $customerInvoiceData['FYEnd'] = $fromCompanyFinanceYear->endingDate;
                        }

                        $customerInvoiceData['companyFinanceYearID'] = $fromCompanyFinancePeriod->companyFinanceYearID;
                        $customerInvoiceData['companyFinancePeriodID'] = $fromCompanyFinancePeriod->companyFinancePeriodID;
                        $customerInvoiceData['FYPeriodDateFrom'] = $fromCompanyFinancePeriod->dateFrom;
                        $customerInvoiceData['FYPeriodDateTo'] = $fromCompanyFinancePeriod->dateTo;


                        $cusInvLastSerial = CustomerInvoiceDirect::where('companySystemID', $stMaster->companyFromSystemID)
                            ->where('companyFinanceYearID', $fromCompanyFinancePeriod->companyFinanceYearID)
                            ->where('serialNo', '>', 0)
                            ->orderBy('custInvoiceDirectAutoID', 'desc')
                            ->first();

                        $cusInvLastSerialNumber = 1;
                        if ($cusInvLastSerial) {
                            $cusInvLastSerialNumber = intval($cusInvLastSerial->serialNo) + 1;
                        }
                        $customerInvoiceData['serialNo'] = $cusInvLastSerialNumber;
                        $customerInvoiceData['serviceLineSystemID'] = $stMaster->serviceLineSystemID;
                        $customerInvoiceData['serviceLineCode'] = $stMaster->serviceLineCode;
                        $customerInvoiceData['wareHouseSystemCode'] = $stMaster->locationFrom;

                        if ($fromCompanyFinancePeriod) {
                            $cusStartYear = $fromCompanyFinanceYear->bigginingDate;
                            $cusFinYearExp = explode('-', $cusStartYear);
                            $cusFinYear = $cusFinYearExp[0];
                        } else {
                            $cusFinYear = date("Y");
                        }
                        $bookingInvCode = ($stMaster->companyID . '\\' . $cusFinYear . '\\' . $customerInvoiceData['documentID'] . str_pad($cusInvLastSerialNumber, 6, '0', STR_PAD_LEFT));
                        $customerInvoiceData['bookingInvCode'] = $bookingInvCode;
                        $customerInvoiceData['bookingDate'] = $today;

                        $customerInvoiceData['comments'] = $comment;

                        $customer = CustomerMaster::where('companyLinkedToSystemID', $stMaster->companyToSystemID)->first();

                        if (!empty($customer)) {
                            $customerInvoiceData['customerID'] = $customer->customerCodeSystem;
                            $customerInvoiceData['customerGLCode'] = $customer->custGLaccount;
                            $customerInvoiceData['customerInvoiceNo'] = $stMaster->stockTransferCode;
                            $customerInvoiceData['customerInvoiceDate'] = $today;
                        }
                        $customerInvoiceData['invoiceDueDate'] = $today;
                        $customerInvoiceData['serviceStartDate'] = $today;
                        $customerInvoiceData['serviceEndDate'] = $today;
                        $customerInvoiceData['performaDate'] = $today;

                        $fromCompany = Company::where('companySystemID', $stMaster->companyFromSystemID)->first();

                        if ($fromCompany) {
                            $companyCurrencyConversion = \Helper::currencyConversion($stMaster->companyFromSystemID, $fromCompany->reportingCurrency, $fromCompany->reportingCurrency, 0);
                            $customerInvoiceData['companyReportingCurrencyID'] = $fromCompany->reportingCurrency;
                            $customerInvoiceData['companyReportingER'] = 1;

                            $customerInvoiceData['localCurrencyID'] = $fromCompany->localCurrencyID;
                            $customerInvoiceData['localCurrencyER'] = $companyCurrencyConversion['trasToLocER'];

                            $customerInvoiceData['custTransactionCurrencyID'] = $fromCompany->reportingCurrency;
                            $customerInvoiceData['custTransactionCurrencyER'] = 1;
                        }

                        $revenuePercentageForInterCompanyInventoryTransfer = ($fromCompany) ? $fromCompany->revenuePercentageForInterCompanyInventoryTransfer : 3;

                        $bookingAmountLocal = 0;
                        $bookingAmountRpt = 0;
                        $totalLocal = 0;
                        $totalRpt = 0;
                        $revenueTotalLocal = 0;
                        $revenueTotalRpt = 0;
                        $totalQty = 0;

                        foreach ($stDetails as $new) {
                            $bookingAmountLocal = $bookingAmountLocal + (($new['unitCostLocal'] * ((100+$revenuePercentageForInterCompanyInventoryTransfer)/100)) * $new['qty']);
                            $bookingAmountRpt = $bookingAmountRpt + (($new['unitCostRpt'] * ((100+$revenuePercentageForInterCompanyInventoryTransfer)/100)) * $new['qty']);

                            $totalLocal = $totalLocal + (($new['unitCostLocal']) * $new['qty']);
                            $totalRpt = $totalRpt + (($new['unitCostRpt']) * $new['qty']);

                            $revenueTotalLocal = $revenueTotalLocal + (($new['unitCostLocal'] * ($revenuePercentageForInterCompanyInventoryTransfer/100)) * $new['qty']);
                            $revenueTotalRpt = $revenueTotalRpt + (($new['unitCostRpt'] * ($revenuePercentageForInterCompanyInventoryTransfer/100)) * $new['qty']);

                            $totalQty = $totalQty + $new['qty'];
                        }

                        $customerInvoiceData['bookingAmountTrans'] = $bookingAmountRpt;
                        $customerInvoiceData['bookingAmountLocal'] = $bookingAmountLocal;
                        $customerInvoiceData['bookingAmountRpt'] = $bookingAmountRpt;
                        $customerInvoiceData['confirmedYN'] = 1;
                        $customerInvoiceData['confirmedByEmpSystemID'] = $stMaster->confirmedByEmpSystemID;
                        $customerInvoiceData['confirmedByEmpID'] = $stMaster->confirmedByEmpID;
                        $customerInvoiceData['confirmedByName'] = $stMaster->confirmedByName;
                        $customerInvoiceData['confirmedDate'] = $stMaster->confirmedDate;
                        $customerInvoiceData['approved'] = -1;
                        $customerInvoiceData['approvedDate'] = $stMaster->approvedDate;
                        $customerInvoiceData['documentType'] = 11;
                        $customerInvoiceData['interCompanyTransferYN'] = $stMaster->interCompanyTransferYN;
                        $customerInvoiceData['createdUserSystemID'] = $stMaster->confirmedByEmpSystemID;
                        $customerInvoiceData['createdUserID'] = $stMaster->confirmedByEmpID;
                        $customerInvoiceData['createdPcID'] = $stMaster->modifiedPc;

                        $customerInvoice = $customerInvoiceRep->create($customerInvoiceData);

                        $cusInvoiceDetails = array();
                        $cusInvoiceDetails['custInvoiceDirectID'] = $customerInvoice->custInvoiceDirectAutoID;
                        $cusInvoiceDetails['companyID'] = $stMaster->companyID;
                        $cusInvoiceDetails['serviceLineCode'] = $stMaster->serviceLineCode;

                        if ($cusInvoiceDetails['serviceLineCode']) {
                            $cusInvDelServiceLine = SegmentMaster::where("ServiceLineCode", $cusInvoiceDetails['serviceLineCode'])->first();
                            if (!empty($cusInvDelServiceLine)) {
                                $cusInvoiceDetails['serviceLineSystemID'] = $cusInvDelServiceLine->serviceLineSystemID;
                            }
                        }

                        $cusInvoiceDetails['customerID'] = $customer->customerCodeSystem;
                        $cusInvoiceDetails['comments'] = $comment;
                        $cusInvoiceDetails['unitOfMeasure'] = 7;
                        $cusInvoiceDetails['invoiceQty'] = 1;
                        $cusInvoiceDetails['invoiceAmountCurrency'] = $fromCompany->reportingCurrency;;
                        $cusInvoiceDetails['invoiceAmountCurrencyER'] = 1;
                        $cusInvoiceDetails['localCurrency'] = $fromCompany->localCurrencyID;
                        $cusInvoiceDetails['localCurrencyER'] = $companyCurrencyConversion['trasToLocER'];
                        $cusInvoiceDetails['comRptCurrency'] = $fromCompany->reportingCurrency;;
                        $cusInvoiceDetails['comRptCurrencyER'] = 1;


                        $glBS = $cusInvoiceDetails;
                        $glPL = $cusInvoiceDetails;
                        $glCost = $cusInvoiceDetails;

                        $glPL['glCode'] = SystemGlCodeScenarioDetail::getGlCodeByScenario($fromCompany->companySystemID, null, "inter-company-transfer-revenue");
                        $glPL['glSystemID'] = SystemGlCodeScenarioDetail::getGlByScenario($fromCompany->companySystemID, null, "inter-company-transfer-revenue");
                        $glPL['glCodeDes'] = SystemGlCodeScenarioDetail::getGlDescriptionByScenario($fromCompany->companySystemID, null, "inter-company-transfer-revenue");
                        $glPL['accountType'] = 'PL';
                        $glPL['invoiceAmount'] = $revenueTotalRpt + $totalRpt;
                        $glPL['unitCost'] = $revenueTotalRpt + $totalRpt;
                        $glPL['localAmount'] = $revenueTotalLocal + $totalLocal;
                        $glPL['comRptAmount'] = $revenueTotalRpt + $totalRpt;

                        $glBS['glCode'] = SystemGlCodeScenarioDetail::getGlCodeByScenario($fromCompany->companySystemID, null, "stock-transfer-pl-account-for-inter-company-transfer");
                        $glBS['glSystemID'] = SystemGlCodeScenarioDetail::getGlByScenario($fromCompany->companySystemID, null, "stock-transfer-pl-account-for-inter-company-transfer");
                        $glBS['glCodeDes'] = SystemGlCodeScenarioDetail::getGlDescriptionByScenario($fromCompany->companySystemID, null, "stock-transfer-pl-account-for-inter-company-transfer");
                        $glBS['accountType'] = 'BS';
                        $glBS['invoiceAmount'] = $totalRpt;
                        $glBS['unitCost'] = $totalRpt;
                        $glBS['localAmount'] = $totalLocal;
                        $glBS['comRptAmount'] = $totalRpt;

                        $customerInvoiceDetailPL = $customerInvoiceDetailRep->create($glPL);
                        $customerInvoiceDetailBS = $customerInvoiceDetailRep->create($glBS);

                        Log::info($customerInvoice);
                        Log::info($customerInvoiceDetailPL);
                        Log::info($customerInvoiceDetailBS);

                        $costGoodData = StockTransferDetails::selectRaw("SUM(qty* unitCostLocal) as localAmount, SUM(qty* unitCostRpt) as rptAmount,financeitemcategorysubassigned.financeGLcodePLSystemID,financeitemcategorysubassigned.financeGLcodePL,localCurrencyID,reportingCurrencyID")->WHERE('stockTransferAutoID', $stMaster->stockTransferAutoID)
                                                            ->join('financeitemcategorysubassigned', 'financeitemcategorysubassigned.itemCategorySubID', '=', 'erp_stocktransferdetails.itemFinanceCategorySubID')
                                                            ->where('financeitemcategorysubassigned.companySystemID', $stMaster->companySystemID)
                                                            ->whereNotNull('financeitemcategorysubassigned.financeGLcodePLSystemID')
                                                            ->where('financeitemcategorysubassigned.financeGLcodePLSystemID', '>', 0)
                                                            ->groupBy('financeitemcategorysubassigned.financeGLcodePLSystemID')
                                                            ->get();


                        // GL start entry

                        $data = [];
                        $finalData = [];
                        if ($customerInvoice) {
                            $data['companySystemID'] = $customerInvoice->companySystemID;
                            $data['companyID'] = $customerInvoice->companyID;
                            $data['serviceLineSystemID'] = $customerInvoice->serviceLineSystemID;
                            $data['serviceLineCode'] = $customerInvoice->serviceLineCode;
                            $data['masterCompanyID'] = null;
                            $data['documentSystemID'] = $customerInvoice->documentSystemiD;
                            $data['documentID'] = $customerInvoice->documentID;
                            $data['documentSystemCode'] = $customerInvoice->custInvoiceDirectAutoID;
                            $data['documentCode'] = $customerInvoice->bookingInvCode;
                            $data['documentDate'] = $today;
                            $data['documentYear'] = \Helper::dateYear($customerInvoice->bookingDate);
                            $data['documentMonth'] = \Helper::dateMonth($customerInvoice->bookingDate);
                            $data['documentConfirmedDate'] = $customerInvoice->confirmedDate;
                            $data['documentConfirmedBy'] = $customerInvoice->confirmedByEmpID;
                            $data['documentConfirmedByEmpSystemID'] = $customerInvoice->confirmedByEmpSystemID;
                            $data['documentFinalApprovedDate'] = $customerInvoice->approvedDate;
                            $data['documentFinalApprovedBy'] = $stMaster->approvedByUserID;
                            $data['documentFinalApprovedByEmpSystemID'] = $stMaster->approvedByUserSystemID;
                            $data['documentNarration'] = $customerInvoice->comments;
                            $data['clientContractID'] = 'X';
                            $data['contractUID'] = 159;
                            $data['supplierCodeSystem'] = $customerInvoice->customerID;
                            $data['holdingShareholder'] = null;
                            $data['holdingPercentage'] = null;
                            $data['nonHoldingPercentage'] = null;
                            $data['createdDateTime'] = \Helper::currentDateTime();
                            $data['createdUserID'] = $stMaster->approvedByUserID;
                            $data['createdUserSystemID'] = $stMaster->approvedByUserSystemID;
                            $data['createdUserPC'] = gethostname();
                            $data['timestamp'] = \Helper::currentDateTime();
                            $data['invoiceNumber'] = $customerInvoice->customerInvoiceNo;
                            $data['invoiceDate'] = $customerInvoice->customerInvoiceDate;

                            $glAR = $data;
                            $glBS = $data;
                            $glPL = $data;
                            $glCostData = $data;

                            if ($customerInvoiceDetailBS) {
                                $glBS['chartOfAccountSystemID'] = $customerInvoiceDetailBS->glSystemID;
                                $glBS['glCode'] = $customerInvoiceDetailBS->glCode;
                                $glBS['glAccountType'] = $customerInvoiceDetailBS->accountType;
                                $glBS['glAccountTypeID'] = 1;
                                $glBS['documentLocalCurrencyID'] = $customerInvoiceDetailBS->localCurrency;
                                $glBS['documentLocalCurrencyER'] = $companyCurrencyConversion['trasToLocER'];
                                $glBS['documentLocalAmount'] = ABS($customerInvoiceDetailBS->localAmount) * -1;
                                $glBS['documentRptCurrencyID'] = $customerInvoiceDetailBS->comRptCurrency;
                                $glBS['documentRptCurrencyER'] = 1;
                                $glBS['documentRptAmount'] = ABS($customerInvoiceDetailBS->comRptAmount) * -1;
                                $glBS['documentTransCurrencyID'] = $customerInvoiceDetailBS->invoiceAmountCurrency;
                                $glBS['documentTransCurrencyER'] = 1;
                                $glBS['documentTransAmount'] = ABS($customerInvoiceDetailBS->invoiceAmount) * -1;
                                array_push($finalData, $glBS);
                            }

                            if ($customerInvoiceDetailPL) {
                                $glPL['chartOfAccountSystemID'] = $customerInvoiceDetailPL->glSystemID;
                                $glPL['glCode'] = $customerInvoiceDetailPL->glCode;
                                $glPL['glAccountType'] = $customerInvoiceDetailPL->accountType;
                                $glPL['glAccountTypeID'] = 2;
                                $glPL['documentLocalCurrencyID'] = $customerInvoiceDetailPL->localCurrency;
                                $glPL['documentLocalCurrencyER'] = $companyCurrencyConversion['trasToLocER'];
                                $glPL['documentLocalAmount'] = ABS($customerInvoiceDetailPL->localAmount) * -1;
                                $glPL['documentRptCurrencyID'] = $customerInvoiceDetailPL->comRptCurrency;
                                $glPL['documentRptCurrencyER'] = 1;
                                $glPL['documentRptAmount'] = ABS($customerInvoiceDetailPL->comRptAmount) * -1;
                                $glPL['documentTransCurrencyID'] = $customerInvoiceDetailPL->invoiceAmountCurrency;
                                $glPL['documentTransCurrencyER'] = 1;
                                $glPL['documentTransAmount'] = ABS($customerInvoiceDetailPL->invoiceAmount) * -1;
                                array_push($finalData, $glPL);
                            }

                            foreach ($costGoodData as $keyCost => $costValue) {
                                $glCost['glCode'] = $costValue->financeGLcodePL;
                                $glCost['glSystemID'] = $costValue->financeGLcodePLSystemID;
                                $glCost['glCodeDes'] = ChartOfAccount::getAccountDescription($costValue->financeGLcodePLSystemID);
                                $glCost['accountType'] = 'PL';
                                $glCost['invoiceAmount'] = abs($costValue->rptAmount) * -1;
                                $glCost['unitCost'] = abs($costValue->rptAmount) * -1;
                                $glCost['localAmount'] = abs($costValue->localAmount) * -1;
                                $glCost['comRptAmount'] = abs($costValue->rptAmount) * -1;

                                $customerInvoiceDetailCost = $customerInvoiceDetailRep->create($glCost);
                                Log::info($customerInvoiceDetailCost);


                                $glCostData['chartOfAccountSystemID'] = $costValue->financeGLcodePLSystemID;
                                $glCostData['glCode'] = $costValue->financeGLcodePL;
                                $glCostData['glAccountType'] = 'PL';
                                $glCostData['glAccountTypeID'] = 2;
                                $glCostData['documentLocalCurrencyID'] = $customerInvoiceDetailCost->localCurrency;
                                $glCostData['documentLocalCurrencyER'] = $companyCurrencyConversion['trasToLocER'];
                                $glCostData['documentLocalAmount'] = ABS($customerInvoiceDetailCost->localAmount);
                                $glCostData['documentRptCurrencyID'] = $customerInvoiceDetailCost->comRptCurrency;
                                $glCostData['documentRptCurrencyER'] = 1;
                                $glCostData['documentRptAmount'] = ABS($customerInvoiceDetailCost->comRptAmount);
                                $glCostData['documentTransCurrencyID'] = $customerInvoiceDetailCost->invoiceAmountCurrency;
                                $glCostData['documentTransCurrencyER'] = 1;
                                $glCostData['documentTransAmount'] = ABS($customerInvoiceDetailCost->invoiceAmount);
                                array_push($finalData, $glCostData);
                            }

                            if ($customerInvoice) {
                                $glAR['chartOfAccountSystemID'] = $customer->custGLAccountSystemID;
                                $glAR['glCode'] = $customer->custGLaccount;
                                $glAR['glAccountType'] = 'BS';
                                $glAR['glAccountTypeID'] = 1;
                                $glAR['documentLocalCurrencyID'] = $customerInvoice->localCurrencyID;
                                $glAR['documentLocalCurrencyER'] = $companyCurrencyConversion['trasToLocER'];
                                $glAR['documentLocalAmount'] = ABS($customerInvoice->bookingAmountLocal);
                                $glAR['documentRptCurrencyID'] = $customerInvoice->companyReportingCurrencyID;
                                $glAR['documentRptCurrencyER'] = 1;
                                $glAR['documentRptAmount'] = ABS($customerInvoice->bookingAmountRpt);
                                $glAR['documentTransCurrencyID'] = $customerInvoice->custTransactionCurrencyID;
                                $glAR['documentTransCurrencyER'] = 1;
                                $glAR['documentTransAmount'] = ABS($customerInvoice->bookingAmountTrans);
                                array_push($finalData, $glAR);
                            }

                            $generalLedgerInsert = GeneralLedger::insert($finalData);
                        }
                        // GL end

                        // ARL Start

                        if ($customerInvoice) {

                            $arLedger = array();
                            $arLedger['companySystemID'] = $customerInvoice->companySystemID;
                            $arLedger['companyID'] = $customerInvoice->companyID;
                            $arLedger['documentSystemID'] = $customerInvoice->documentSystemiD;
                            $arLedger['documentID'] = $customerInvoice->documentID;
                            $arLedger['documentCodeSystem'] = $customerInvoice->custInvoiceDirectAutoID;
                            $arLedger['documentCode'] = $customerInvoice->bookingInvCode;
                            $arLedger['customerID'] = $customerInvoice->customerID;
                            $arLedger['documentDate'] = $today;
                            $arLedger['InvoiceNo'] = $customerInvoice->customerInvoiceNo;
                            $arLedger['InvoiceDate'] = $customerInvoice->customerInvoiceDate;
                            $arLedger['custTransCurrencyID'] = $customerInvoice->custTransactionCurrencyID;
                            $arLedger['custTransER'] = 1;
                            $arLedger['custInvoiceAmount'] = $customerInvoice->bookingAmountTrans;
                            $arLedger['custDefaultCurrencyID'] = 0;
                            $arLedger['custDefaultCurrencyER'] = 0;
                            $arLedger['custDefaultAmount'] = 0;
                            $arLedger['localCurrencyID'] = $customerInvoice->localCurrencyID;
                            $arLedger['localER'] = $companyCurrencyConversion['trasToLocER'];
                            $arLedger['localAmount'] = $customerInvoice->bookingAmountLocal;
                            $arLedger['comRptCurrencyID'] = $customerInvoice->companyReportingCurrencyID;
                            $arLedger['comRptER'] = 1;
                            $arLedger['comRptAmount'] = $customerInvoice->bookingAmountRpt;
                            $arLedger['isInvoiceLockedYN'] = 0;
                            $arLedger['selectedToPaymentInv'] = 0;
                            $arLedger['fullyInvoiced'] = 0;
                            $arLedger['createdUserID'] = $stMaster->approvedByUserID;
                            $arLedger['createdPcID'] = gethostname();
                            $arLedger['documentType'] = 11;

                            $accountsReceivableLedger = $accountsReceivableLedgerRep->create($arLedger);

                            Log::info($accountsReceivableLedger);
                            // ARL End
                        }

                        // stock receive create start
                        if ($customerInvoice) {

                            $stockReceive = new StockReceive();
                            $stockReceive->documentSystemID = 10;
                            $documentMaster = DocumentMaster::where('documentSystemID', $stockReceive->documentSystemID)->first();
                            if ($documentMaster) {
                                $stockReceive->documentID = $documentMaster->documentID;
                            }

                            $stockReceive->companySystemID = $stMaster->companyToSystemID;
                            $stockReceive->companyID = $stMaster->companyTo;
                            $stockReceive->serviceLineSystemID = NULl;
                            $stockReceive->serviceLineCode = NULl;
                            $stockReceive->refNo = $customerInvoice->bookingInvCode;
                            $stockReceive->comment = $customerInvoice->comments . ', ' . $customerInvoice->bookingInvCode;
                            $stockReceive->companyFromSystemID = $stMaster->companyFromSystemID;
                            $stockReceive->companyFrom = $stMaster->companyFrom;
                            $stockReceive->companyToSystemID = $stMaster->companyToSystemID;
                            $stockReceive->companyTo = $stMaster->companyTo;
                            $stockReceive->locationTo = $stMaster->locationTo;
                            $stockReceive->locationFrom = $stMaster->locationFrom;
                            $stockReceive->confirmedYN = 0;
                            $stockReceive->approved = 0;
                            $stockReceive->interCompanyTransferYN = $stMaster->interCompanyTransferYN;
                            $stockReceive->RollLevForApp_curr = 1;
                            $stockReceive->createdDateTime = $stMaster->createdDateTime;
                            $stockReceive->createdUserGroup = $stMaster->createdUserGroup;
                            $stockReceive->createdPCID = $stMaster->createdPCID;
                            $stockReceive->createdUserSystemID = $stMaster->createdUserSystemID;
                            $stockReceive->createdUserID = $stMaster->createdUserID;
                            
                            $toCompanyFinancePeriod = CompanyFinancePeriod::where('companySystemID', $stMaster->companyToSystemID)
                                ->where('departmentSystemID', 10)
                                ->where('isActive', -1)
                                ->whereHas('finance_year_by', function($query) {
                                    $query->where('isCurrent', -1);
                                })
                                //->where('dateFrom', '<', $stMaster->tranferDate)
                                //->where('dateTo', '>', $stMaster->tranferDate)
                                ->where('isCurrent', -1)
                                ->first();
                            $lastSerialNumber = 1;
                            if (!empty($toCompanyFinancePeriod)) {
                                $companyFinanceYear = CompanyFinanceYear::where('companyFinanceYearID', $toCompanyFinancePeriod->companyFinanceYearID)
                                    ->where('companySystemID', $stMaster->companyToSystemID)
                                    ->first();

                                $lastSerial = StockReceive::where('companySystemID', $stMaster->companyToSystemID)
                                    ->where('companyFinanceYearID', $toCompanyFinancePeriod->companyFinanceYearID)
                                    ->where('serialNo', '>', 0)
                                    ->orderBy('stockReceiveAutoID', 'desc')
                                    ->first();
                                if ($lastSerial) {
                                    $lastSerialNumber = intval($lastSerial->serialNo) + 1;
                                }

                                if (!empty($companyFinanceYear)) {
                                    $stockReceive->FYBiggin = $companyFinanceYear->bigginingDate;
                                    $stockReceive->FYEnd = $companyFinanceYear->endingDate;
                                }

                                if (!empty($toCompanyFinancePeriod)) {
                                    $stockReceive->companyFinanceYearID = $toCompanyFinancePeriod->companyFinanceYearID;
                                    $stockReceive->companyFinancePeriodID = $toCompanyFinancePeriod->companyFinancePeriodID;
                                    $stockReceive->receivedDate = $today;
                                }

                                if ($companyFinanceYear) {
                                    $startYear = $companyFinanceYear['bigginingDate'];
                                    $finYearExp = explode('-', $startYear);
                                    $finYear = $finYearExp[0];
                                } else {
                                    $finYear = date("Y");
                                }
                            }

                            $stockReceive->serialNo = $lastSerialNumber;

                            $stockReceiveCode = ($stockReceive->companyID . '\\' . $finYear . '\\' . $stockReceive->documentID . str_pad($lastSerialNumber, 6, '0', STR_PAD_LEFT));
                            $stockReceive->stockReceiveCode = $stockReceiveCode;
                            $stockReceive->save();

                            $toCompany = Company::where('companySystemID', $stMaster->companyToSystemID)->first();
                            $revenuePercentageForInterCompanyInventoryTransfer = ($toCompany) ? $toCompany->revenuePercentageForInterCompanyInventoryTransfer : 3;
                            $stockReceiveAutoID = $stockReceive->stockReceiveAutoID;
                            foreach ($stDetails as $new) {

                                $item = array();
                                $item['stockReceiveAutoID'] = $stockReceiveAutoID;
                                $item['stockReceiveCode'] = $stockReceive->stockReceiveCode;
                                $item['stockTransferAutoID'] = $stMaster->stockTransferAutoID;
                                $item['stockTransferCode'] = $stMaster->stockTransferCode;
                                $item['stockTransferDate'] = $today;

                                $item['itemCodeSystem'] = $new['itemCodeSystem'];
                                $item['itemPrimaryCode'] = $new['itemPrimaryCode'];
                                $item['itemDescription'] = $new['itemDescription'];
                                $item['unitOfMeasure'] = $new['unitOfMeasure'];
                                $item['itemFinanceCategoryID'] = $new['itemFinanceCategoryID'];
                                $item['itemFinanceCategorySubID'] = $new['itemFinanceCategorySubID'];
                                $item['financeGLcodebBS'] = $new['financeGLcodebBS'];
                                $item['financeGLcodebBSSystemID'] =  $new['financeGLcodebBSSystemID'];
                                $item['localCurrencyID'] = $toCompany->localCurrencyID;
                                // $temUnitCostLocal        = $new['unitCostLocal'] * 1.03;
                                $temUnitCostRpt = $new['unitCostRpt'] * ((100+$revenuePercentageForInterCompanyInventoryTransfer)/100);
                                $convertCurrencyConversion = \Helper::currencyConversion($stMaster->companyToSystemID, $fromCompany->reportingCurrency, $fromCompany->reportingCurrency, $temUnitCostRpt);
                                $item['unitCostLocal'] = $convertCurrencyConversion['localAmount'];
                                $item['reportingCurrencyID'] = $toCompany->reportingCurrency;
                                $item['unitCostRpt'] = $convertCurrencyConversion['reportingAmount'];
                                $item['qty'] = $new['qty'];

                                if ($item['unitCostLocal'] <= 0 || $item['unitCostRpt'] <= 0) {
                                    // return $this->sendError("Cost is not updated", 500);
                                } else {
                                    $srdItem = $stockReceiveDetailsRepo->create($item);
                                    Log::info($srdItem);
                                    $stDetail = StockTransferDetails::where('stockTransferDetailsID', $new['stockTransferDetailsID'])->first();
                                    $stDetail->addedToRecieved = -1;
                                    $stDetail->stockRecieved = -1;
                                    $stDetail->save();
                                }
                            }

                            $stMaster->fullyReceived = -1;
                            $stMaster->save();


                            $InterCompanyStockTransfer = InterCompanyStockTransfer::where('stockTransferID', $stMaster->stockTransferAutoID)->delete();

                            $interCompanySTData = [
                                'stockTransferID' => $stMaster->stockTransferAutoID,
                                'customerInvoiceID' => $customerInvoice->custInvoiceDirectAutoID,
                                'stockReceiveID' => $stockReceiveAutoID,
                            ];

                            $resST = InterCompanyStockTransfer::create($interCompanySTData);


                            Log::info('Successfully created  stock_receive' . date('H:i:s'));
                        }
                    }
                } else if ($stMaster->interCompanyTransferYN == 0 && $stMaster->approved == -1) {

                    $checkPolicy = CompanyPolicyMaster::where('companySystemID', $stMaster->companySystemID)
                        ->where('companyPolicyCategoryID', 34)
                        ->where('isYesNO', 1)
                        ->first();

                    if (!empty($checkPolicy)) {
                        Log::info('Policy Enabled' . date('H:i:s'));

                        $stockReceive = new StockReceive();
                        $stockReceive->documentSystemID = 10;
                        $documentMaster = DocumentMaster::where('documentSystemID', $stockReceive->documentSystemID)->first();
                        if ($documentMaster) {
                            $stockReceive->documentID = $documentMaster->documentID;
                        }

                        $stockReceive->companySystemID = $stMaster->companyToSystemID;
                        $stockReceive->companyID = $stMaster->companyTo;
                        $stockReceive->serviceLineSystemID = $stMaster->serviceLineSystemID;
                        $stockReceive->serviceLineCode = $stMaster->serviceLineCode;
                        $stockReceive->refNo = $stMaster->refNo;
                        $stockReceive->comment = $stMaster->comment;
                        $stockReceive->companyFromSystemID = $stMaster->companyFromSystemID;
                        $stockReceive->companyFrom = $stMaster->companyFrom;
                        $stockReceive->companyToSystemID = $stMaster->companyToSystemID;
                        $stockReceive->companyTo = $stMaster->companyTo;
                        $stockReceive->locationTo = $stMaster->locationTo;
                        $stockReceive->locationFrom = $stMaster->locationFrom;
                        $stockReceive->confirmedYN = $stMaster->confirmedYN;
                        $stockReceive->confirmedByEmpSystemID = $stMaster->confirmedByEmpSystemID;
                        $stockReceive->confirmedByEmpID = $stMaster->confirmedByEmpID;
                        $stockReceive->confirmedByName = $stMaster->confirmedByName;
                        $stockReceive->confirmedDate = $stMaster->confirmedDate;
                        $stockReceive->approved = $stMaster->approved;
                        $stockReceive->approvedDate = $stMaster->approvedDate;
                        $stockReceive->approvedByUserID = $stMaster->approvedByUserID;
                        $stockReceive->approvedByUserSystemID = $stMaster->approvedByUserSystemID;
                        $stockReceive->postedDate = $stMaster->postedDate;
                        $stockReceive->interCompanyTransferYN = $stMaster->interCompanyTransferYN;
                        $stockReceive->RollLevForApp_curr = 1;
                        $stockReceive->createdDateTime = $stMaster->createdDateTime;
                        $stockReceive->createdUserGroup = $stMaster->createdUserGroup;
                        $stockReceive->createdPCID = $stMaster->createdPCID;
                        $stockReceive->createdUserSystemID = $stMaster->createdUserSystemID;
                        $stockReceive->createdUserID = $stMaster->createdUserID;


                        $toCompanyFinancePeriod = CompanyFinancePeriod::where('companySystemID', $stMaster->companyToSystemID)
                            ->where('departmentSystemID', 10)
                            ->where('isActive', -1)
                            //->where('dateFrom', '<', $stMaster->tranferDate)
                            //->where('dateTo', '>', $stMaster->tranferDate)
                            ->where('isCurrent', -1)
                            ->first();
                        $lastSerialNumber = 1;
                        if (!empty($toCompanyFinancePeriod)) {
                            $companyFinanceYear = CompanyFinanceYear::where('companyFinanceYearID', $toCompanyFinancePeriod->companyFinanceYearID)
                                ->where('companySystemID', $stMaster->companyToSystemID)
                                ->first();

                            $lastSerial = StockReceive::where('companySystemID', $stMaster->companyToSystemID)
                                ->where('companyFinanceYearID', $toCompanyFinancePeriod->companyFinanceYearID)
                                ->where('serialNo', '>', 0)
                                ->orderBy('stockReceiveAutoID', 'desc')
                                ->first();
                            if ($lastSerial) {
                                $lastSerialNumber = intval($lastSerial->serialNo) + 1;
                            }

                            if (!empty($companyFinanceYear)) {
                                $stockReceive->FYBiggin = $companyFinanceYear->bigginingDate;
                                $stockReceive->FYEnd = $companyFinanceYear->endingDate;
                            }

                            if (!empty($toCompanyFinancePeriod)) {
                                $stockReceive->companyFinanceYearID = $toCompanyFinancePeriod->companyFinanceYearID;
                                $stockReceive->companyFinancePeriodID = $toCompanyFinancePeriod->companyFinancePeriodID;
                                $stockReceive->receivedDate = $stMaster->tranferDate;
                            }

                            if ($companyFinanceYear) {
                                $startYear = $companyFinanceYear['bigginingDate'];
                                $finYearExp = explode('-', $startYear);
                                $finYear = $finYearExp[0];
                            } else {
                                $finYear = date("Y");
                            }
                        }

                        $stockReceive->serialNo = $lastSerialNumber;

                        $stockReceiveCode = ($stockReceive->companyID . '\\' . $finYear . '\\' . $stockReceive->documentID . str_pad($lastSerialNumber, 6, '0', STR_PAD_LEFT));
                        $stockReceive->stockReceiveCode = $stockReceiveCode;
                        $stockReceive->save();

                        $toCompany = Company::where('companySystemID', $stMaster->companyToSystemID)->first();

                        foreach ($stDetails as $new) {

                            $item = array();
                            $item['stockReceiveAutoID'] = $stockReceive->stockReceiveAutoID;
                            $item['stockReceiveCode'] = $stockReceive->stockReceiveCode;
                            $item['stockTransferAutoID'] = $stMaster->stockTransferAutoID;
                            $item['stockTransferCode'] = $stMaster->stockTransferCode;
                            $item['stockTransferDate'] = $today;

                            $item['itemCodeSystem'] = $new['itemCodeSystem'];
                            $item['itemPrimaryCode'] = $new['itemPrimaryCode'];
                            $item['itemDescription'] = $new['itemDescription'];
                            $item['unitOfMeasure'] = $new['unitOfMeasure'];
                            $item['itemFinanceCategoryID'] = $new['itemFinanceCategoryID'];
                            $item['itemFinanceCategorySubID'] = $new['itemFinanceCategorySubID'];
                            $item['financeGLcodebBS'] = $new['financeGLcodebBS'];
                            $item['financeGLcodebBSSystemID'] = $new['financeGLcodebBSSystemID'];
                            $item['localCurrencyID'] = $toCompany->localCurrencyID;
                            $item['unitCostLocal'] = $new['unitCostLocal'];
                            $item['reportingCurrencyID'] = $toCompany->reportingCurrency;
                            $item['unitCostRpt'] = $new['unitCostRpt'];
                            $item['qty'] = $new['qty'];

                            if ($item['unitCostLocal'] <= 0 || $item['unitCostRpt'] <= 0) {
                                // return $this->sendError("Cost is not updated", 500);
                            } else {
                                $srdItem = $stockReceiveDetailsRepo->create($item);
                                Log::info($srdItem);
                                $stDetail = StockTransferDetails::where('stockTransferDetailsID', $new['stockTransferDetailsID'])->first();
                                $stDetail->addedToRecieved = -1;
                                $stDetail->stockRecieved = -1;
                                $stDetail->save();
                            }
                        }

                        $stMaster->fullyReceived = -1;
                        $stMaster->save();

                        $approval = DocumentApproved::where('companySystemID',$stMaster->companySystemID)
                                                     ->where('documentSystemID',$stMaster->documentSystemID)
                                                     ->where('documentSystemCode',$stMaster->stockTransferAutoID)
                                                     ->where('approvedYN',-1)
                                                     ->orderBy('rollLevelOrder','desc')
                                                     ->first();

                        Log::info('Approval Data' . date('H:i:s'));
                        Log::info($approval);

                        if(!empty($approval)){
                            $approval->documentSystemCode = $stockReceive->stockReceiveAutoID;
                            $approval->documentSystemID = $stockReceive->documentSystemID;
                            $approval->documentID = $stockReceive->documentID;
                            unset($approval->documentApprovedID);
                            DocumentApproved::insert($approval->toArray());
                            Log::info($approval);

                            $masterData = ['documentSystemID' => $stockReceive->documentSystemID,
                                'autoID' => $stockReceive->stockReceiveAutoID,
                                'companySystemID' => $stockReceive->companySystemID,
                                'employeeSystemID' => $approval->employeeSystemID];

                            $jobIL = ItemLedgerInsert::dispatch($masterData, $dataBase);
                            $jobGL = GeneralLedgerInsert::dispatch($masterData, $this->dataBase);
                            //$jobSI = CreateSupplierInvoice::dispatch($stockReceive);
                        }

                        Log::info('Successfully created  stock_receive' . date('H:i:s'));

                    } else {
                        Log::info('Policy Disabled' . date('H:i:s'));
                    }
                }
                DB::commit();
            } catch (\Exception $e) {
                DB::rollback();
                Log::error($this->failed($e));
            }
        }
    }

    public function failed($exception)
    {
        return $exception->getMessage();
    }
}
