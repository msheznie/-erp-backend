<?php

namespace App\Jobs;

use App\Models\Company;
use App\Models\CompanyFinancePeriod;
use App\Models\CompanyFinanceYear;
use App\Models\CustomerInvoiceDirect;
use App\Models\CustomerMaster;
use App\Models\DocumentMaster;
use App\Models\GeneralLedger;
use App\Models\StockReceive;
use App\Models\StockReceiveDetails;
use App\Models\StockTransfer;
use App\Models\StockTransferDetails;
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

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($stMaster)
    {
        $this->stMaster = $stMaster;
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
                if ($stMaster->interCompanyTransferYN == -1 && $stMaster->approved == -1) {

                    $stDetails = StockTransferDetails::where("stockTransferAutoID", $stMaster->stockTransferAutoID)->get();
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

                    $today = date('Y-m-d');
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
                                                                    ->where('serialNo','>',0)
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

                        $bookingAmountLocal = 0;
                        $bookingAmountRpt = 0;
                        $totalLocal = 0;
                        $totalRpt = 0;
                        $revenueTotalLocal = 0;
                        $revenueTotalRpt = 0;
                        $totalQty = 0;

                        foreach ($stDetails as $new) {
                            $bookingAmountLocal = $bookingAmountLocal + (($new['unitCostLocal'] * 1.03) * $new['qty']);
                            $bookingAmountRpt = $bookingAmountRpt + (($new['unitCostRpt'] * 1.03) * $new['qty']);

                            $totalLocal = $totalLocal + (($new['unitCostLocal']) * $new['qty']);
                            $totalRpt = $totalRpt + (($new['unitCostRpt']) * $new['qty']);

                            $revenueTotalLocal = $revenueTotalLocal + (($new['unitCostLocal'] * 0.03) * $new['qty']);
                            $revenueTotalRpt = $revenueTotalRpt + (($new['unitCostRpt'] * 0.03) * $new['qty']);

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

                        $glPL['glCode'] = '91582';
                        $glPL['glCodeDes'] = 'Product Revenue -Intercompany';
                        $glPL['accountType'] = 'PL';
                        $glPL['invoiceAmount'] = $revenueTotalRpt;
                        $glPL['unitCost'] = $revenueTotalRpt;
                        $glPL['localAmount'] = $revenueTotalLocal;
                        $glPL['comRptAmount'] = $revenueTotalRpt;

                        $glBS['glCode'] = '20023';
                        $glBS['glCodeDes'] = 'Intercompany stock transfer';
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

                            $glAR = $data;
                            $glBS = $data;
                            $glPL = $data;

                            if ($customerInvoiceDetailBS) {
                                $glBS['chartOfAccountSystemID'] = 747;
                                $glBS['glCode'] = $customerInvoiceDetailBS->glCode;
                                $glBS['glAccountType'] = $customerInvoiceDetailBS->accountType;
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
                                $glPL['chartOfAccountSystemID'] = 693;
                                $glPL['glCode'] = $customerInvoiceDetailPL->glCode;
                                $glPL['glAccountType'] = $customerInvoiceDetailPL->accountType;
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

                            if ($customerInvoice) {
                                $glAR['chartOfAccountSystemID'] = $customer->custGLAccountSystemID;
                                $glAR['glCode'] = $customer->custGLaccount;
                                $glAR['glAccountType'] = 'BS';
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

                        if($customerInvoice) {

                            $arLedger = array();
                            $arLedger['companySystemID'] = $customerInvoice->companySystemID;
                            $arLedger['companyID']       = $customerInvoice->companyID;
                            $arLedger['documentSystemID'] =  $customerInvoice->documentSystemiD;
                            $arLedger['documentID'] = $customerInvoice->documentID;
                            $arLedger['documentCodeSystem'] = $customerInvoice->custInvoiceDirectAutoID;
                            $arLedger['documentCode'] = $customerInvoice->bookingInvCode;
                            $arLedger['customerID'] =   $customerInvoice->customerID;
                            $arLedger['documentDate'] = $today;
                            $arLedger['InvoiceNo'] =   $customerInvoice->customerInvoiceNo;
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
                            $arLedger['createdUserID'] =  $stMaster->approvedByUserID;
                            $arLedger['createdPcID'] = gethostname();
                            $arLedger['documentType'] = 11;

                            $accountsReceivableLedger = $accountsReceivableLedgerRep->create($arLedger);

                            Log::info($accountsReceivableLedger);
                            // ARL End
                        }
                    }


                    // stock receive create start
                    $push = true;
                    if ($push) {

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
                        $stockReceive->comment = $customerInvoice->comments .', '.$customerInvoice->bookingInvCode;
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
                                ->where('serialNo','>',0)
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
                            // $temUnitCostLocal        = $new['unitCostLocal'] * 1.03;
                            $temUnitCostRpt              = $new['unitCostRpt'] * 1.03;
                            $convertCurrencyConversion   = \Helper::currencyConversion($stMaster->companyToSystemID, $fromCompany->reportingCurrency, $fromCompany->reportingCurrency, $temUnitCostRpt);
                            $item['unitCostLocal']       = $convertCurrencyConversion['localAmount'];
                            $item['reportingCurrencyID'] = $toCompany->reportingCurrency;
                            $item['unitCostRpt']         = $convertCurrencyConversion['reportingAmount'];
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
                        Log::info('Successfully created  stock_receive' . date('H:i:s'));
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
