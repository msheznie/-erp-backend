<?php

namespace App\Jobs;

use App\Jobs\CreateConsoleJV;
use App\Models\BookInvSuppMaster;
use App\Models\Company;
use App\Models\CompanyFinancePeriod;
use App\Models\CompanyFinanceYear;
use App\Models\StockReceive;
use App\Models\SystemGlCodeScenarioDetail;
use App\Models\StockReceiveDetails;
use App\Models\SupplierMaster;
use App\Models\InterCompanyStockTransfer;
use App\Repositories\AccountsPayableLedgerRepository;
use App\Repositories\BookInvSuppDetRepository;
use App\Repositories\BookInvSuppMasterRepository;
use App\Repositories\GeneralLedgerRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CreateSupplierInvoice implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $srMaster;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($srMaster)
    {
        $this->srMaster = $srMaster;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(BookInvSuppMasterRepository $bookInvSuppMasterRepo, BookInvSuppDetRepository $bookInvSuppDetRepo,
                           GeneralLedgerRepository $generalLedgerRepo, AccountsPayableLedgerRepository $accountsPayableLedgerRepo)
    {
        Log::useFiles(storage_path() . '/logs/create_supplier_invoice_jobs.log');
        $sr = $this->srMaster;
        $srMaster = StockReceive::where('stockReceiveAutoID', $sr->stockReceiveAutoID)->first();
        if (!empty($srMaster)) {
            DB::beginTransaction();
            try {
                Log::info('Successfully start  supplier_invoice' . date('H:i:s'));
                if ($srMaster->interCompanyTransferYN == -1 && $srMaster->approved == -1) {

                    // supplier Invoice master start

                    $toCompanyFinancePeriod = CompanyFinancePeriod::where('companySystemID', $srMaster->companyToSystemID)
                        ->where('departmentSystemID', 1)
                        ->where('isActive', -1)
                        ->where('isCurrent', -1)
                        ->first();

                    $today = date('Y-m-d H:i:s');

                    $bookingInvLastSerial = BookInvSuppMaster::where('companySystemID', $srMaster->companyToSystemID)
                        ->where('companyFinanceYearID', $toCompanyFinancePeriod->companyFinanceYearID)
                        ->where('serialNo', '>', 0)
                        ->orderBy('bookingSuppMasInvAutoID', 'desc')
                        ->first();

                    $supInvLastSerialNumber = 1;
                    if ($bookingInvLastSerial) {
                        $supInvLastSerialNumber = intval($bookingInvLastSerial->serialNo) + 1;
                    }

                    $toCompany = Company::where('companySystemID', $srMaster->companyToSystemID)->first();
                    $supplier = SupplierMaster::where('companyLinkedToSystemID', $srMaster->companyFromSystemID)->first();

                    if ($toCompany) {
                        $companyCurrencyConversion = \Helper::currencyConversion($srMaster->companyToSystemID, $toCompany->reportingCurrency, $toCompany->reportingCurrency, 0);
                    }

                    $supplierInvoiceData['serialNo'] = $supInvLastSerialNumber;
                    $supplierInvoiceData['companySystemID'] = $srMaster->companyToSystemID;
                    $supplierInvoiceData['companyID'] = $srMaster->companyTo;
                    $supplierInvoiceData['documentSystemID'] = 11;
                    $supplierInvoiceData['documentID'] = 'SI';
                    $supplierInvoiceData['companyFinanceYearID'] = '';

                    if (!(empty($toCompanyFinancePeriod))) {

                        $toCompanyFinanceYear = CompanyFinanceYear::where('companyFinanceYearID', $toCompanyFinancePeriod->companyFinanceYearID)
                            ->where('companySystemID', $srMaster->companyToSystemID)
                            ->first();

                        if (!empty($toCompanyFinanceYear)) {
                            $supplierInvoiceData['companyFinanceYearID'] = $toCompanyFinanceYear->companyFinanceYearID;
                            $supplierInvoiceData['FYBiggin'] = $toCompanyFinanceYear->bigginingDate;
                            $supplierInvoiceData['FYEnd'] = $toCompanyFinanceYear->endingDate;

                            $supStartYear = $toCompanyFinanceYear->bigginingDate;
                            $supFinYearExp = explode('-', $supStartYear);
                            $supFinYear = $supFinYearExp[0];
                        }

                        $supplierInvoiceData['companyFinancePeriodID'] = $toCompanyFinancePeriod->companyFinancePeriodID;
                        $supplierInvoiceData['FYPeriodDateFrom'] = $toCompanyFinancePeriod->dateFrom;
                        $supplierInvoiceData['FYPeriodDateTo'] = $toCompanyFinancePeriod->dateTo;
                    } else {
                        $supFinYear = date("Y");
                    }

                    $comment = $srMaster->comment . ", " . $srMaster->stockReceiveCode;

                    $srDetails = StockReceiveDetails::where('stockReceiveAutoID', $srMaster->stockReceiveAutoID)->get();
                    $bookingAmountRpt = 0;
                    $bookingAmountLocal = 0;

                    foreach ($srDetails as $new) {
                        $bookingAmountLocal = $bookingAmountLocal + ($new['unitCostLocal'] * $new['qty']);
                        $bookingAmountRpt = $bookingAmountRpt + ($new['unitCostRpt'] * $new['qty']);
                    }

                    $bookingInvCode = ($srMaster->companyID . '\\' . $supFinYear . '\\' . 'BSI' . str_pad($supInvLastSerialNumber, 6, '0', STR_PAD_LEFT));;
                    $supplierInvoiceData['bookingInvCode'] = $bookingInvCode;
                    $supplierInvoiceData['bookingDate'] = $today;
                    $supplierInvoiceData['comments'] = $comment;
                    $supplierInvoiceData['secondaryRefNo'] = null;
                    $supplierInvoiceData['supplierID'] = $supplier->supplierCodeSystem;
                    $supplierInvoiceData['supplierGLCode'] = $supplier->liabilityAccount;
                    $supplierInvoiceData['supplierGLCodeSystemID'] = $supplier->liabilityAccountSysemID;
                    $supplierInvoiceData['UnbilledGRVAccountSystemID'] = $supplier->UnbilledGRVAccountSystemID;
                    $supplierInvoiceData['UnbilledGRVAccount'] = $supplier->UnbilledGRVAccount;
                    $supplierInvoiceData['supplierInvoiceNo'] = $srMaster->refNo;
                    $supplierInvoiceData['supplierInvoiceDate'] = $today;
                    $supplierInvoiceData['supplierTransactionCurrencyID'] = $toCompany->reportingCurrency;
                    $supplierInvoiceData['supplierTransactionCurrencyER'] = 1;
                    $supplierInvoiceData['companyReportingCurrencyID'] = $toCompany->reportingCurrency;
                    $supplierInvoiceData['companyReportingER'] = 1;
                    $supplierInvoiceData['localCurrencyID'] = $toCompany->localCurrencyID;
                    $supplierInvoiceData['localCurrencyER'] = $companyCurrencyConversion['trasToLocER'];
                    $supplierInvoiceData['bookingAmountTrans'] = $bookingAmountRpt;
                    $supplierInvoiceData['bookingAmountLocal'] = $bookingAmountLocal;
                    $supplierInvoiceData['bookingAmountRpt'] = $bookingAmountRpt;
                    $supplierInvoiceData['confirmedYN'] = 1;
                    $supplierInvoiceData['confirmedByEmpSystemID'] = $srMaster->confirmedByEmpSystemID;
                    $supplierInvoiceData['confirmedByEmpID'] = $srMaster->confirmedByEmpID;;
                    $supplierInvoiceData['confirmedByName'] = $srMaster->confirmedByName;;
                    $supplierInvoiceData['confirmedDate'] = $today;
                    $supplierInvoiceData['approved'] = -1;
                    $supplierInvoiceData['approvedDate'] = $today;
                    $supplierInvoiceData['postedDate'] = $today;
                    $supplierInvoiceData['documentType'] = 0;
                    $supplierInvoiceData['RollLevForApp_curr'] = 1;
                    $supplierInvoiceData['interCompanyTransferYN'] = $srMaster->interCompanyTransferYN;
                    $supplierInvoiceData['createdUserSystemID'] = $srMaster->confirmedByEmpSystemID;
                    $supplierInvoiceData['createdUserID'] = $srMaster->confirmedByEmpID;
                    $supplierInvoiceData['createdPcID'] = gethostname();
                    $bookInvSuppMaster = $bookInvSuppMasterRepo->create($supplierInvoiceData);
                    Log::info($bookInvSuppMaster);
                    // supplier Invoice master end

                    if (!empty($bookInvSuppMaster)) {
                        // supplier Invoice details start
                        $supplierInvoiceDetail = array();
                        $supplierInvoiceDetail['bookingSuppMasInvAutoID'] = $bookInvSuppMaster->bookingSuppMasInvAutoID;
                        $supplierInvoiceDetail['unbilledgrvAutoID'] = 0;
                        $supplierInvoiceDetail['companySystemID'] = $bookInvSuppMaster->companySystemID;
                        $supplierInvoiceDetail['companyID'] = $bookInvSuppMaster->companyID;
                        $supplierInvoiceDetail['supplierID'] = $supplier->supplierCodeSystem;
                        $supplierInvoiceDetail['purchaseOrderID'] = 0;
                        $supplierInvoiceDetail['grvAutoID'] = 0;
                        $supplierInvoiceDetail['grvType'] = 0;
                        $supplierInvoiceDetail['supplierTransactionCurrencyID'] = $toCompany->reportingCurrency;
                        $supplierInvoiceDetail['supplierTransactionCurrencyER'] = 1;
                        $supplierInvoiceDetail['companyReportingCurrencyID'] = $toCompany->reportingCurrency;
                        $supplierInvoiceDetail['companyReportingER'] = 1;
                        $supplierInvoiceDetail['localCurrencyID'] = $toCompany->localCurrencyID;
                        $supplierInvoiceDetail['localCurrencyER'] = $companyCurrencyConversion['trasToLocER'];
                        $supplierInvoiceDetail['supplierInvoOrderedAmount'] = $bookingAmountRpt;
                        $supplierInvoiceDetail['supplierInvoAmount'] = $bookingAmountRpt;
                        $supplierInvoiceDetail['transSupplierInvoAmount'] = $bookingAmountRpt;
                        $supplierInvoiceDetail['localSupplierInvoAmount'] = $bookingAmountLocal;
                        $supplierInvoiceDetail['rptSupplierInvoAmount'] = $bookingAmountRpt;
                        $supplierInvoiceDetail['totTransactionAmount'] = $bookingAmountRpt;
                        $supplierInvoiceDetail['totLocalAmount'] = $bookingAmountLocal;
                        $supplierInvoiceDetail['totRptAmount'] = $bookingAmountRpt;
                        $supplierInvoiceDetail['isAddon'] = 0;
                        $supplierInvoiceDetail['invoiceBeforeGRVYN'] = 0;
                        $supplierInvoiceDetail['timesReferred'] = 0;
                        $supplierInvoiceDetail['timeStamp'] = $today;
                        $bookInvSuppDet = $bookInvSuppDetRepo->create($supplierInvoiceDetail);
                        Log::info($bookInvSuppDet);

                        // supplier Invoice details end
                        // GL Start
                        $data = [];
                        $glComment = $srMaster->comment . ", " . $srMaster->stockReceiveCode;

                        $data['companySystemID'] = $bookInvSuppMaster->companySystemID;
                        $data['companyID'] = $bookInvSuppMaster->companyID;
                        $data['serviceLineSystemID'] = $srMaster->serviceLineSystemID;
                        $data['serviceLineCode'] = $srMaster->serviceLineCode;
                        $data['masterCompanyID'] = $toCompany->masterComapanyID;
                        $data['documentSystemID'] = $bookInvSuppMaster->documentSystemID;
                        $data['documentID'] = $bookInvSuppMaster->documentID;
                        $data['documentSystemCode'] = $bookInvSuppMaster->bookingSuppMasInvAutoID;
                        $data['documentCode'] = $bookInvSuppMaster->bookingInvCode;
                        $data['documentDate'] = $today;
                        $data['documentYear'] = \Helper::dateYear($today);
                        $data['documentMonth'] = \Helper::dateMonth($today);
                        $data['invoiceNumber'] = $srMaster->stockReceiveCode;
                        $data['invoiceDate'] = $today;
                        $data['documentConfirmedDate'] = $today;
                        $data['documentConfirmedBy'] = $bookInvSuppMaster->confirmedByEmpID;
                        $data['documentConfirmedByEmpSystemID'] = $bookInvSuppMaster->confirmedByEmpSystemID;
                        $data['documentFinalApprovedDate'] = $today;
                        $data['documentFinalApprovedBy'] = $srMaster->approvedByUserID;
                        $data['documentFinalApprovedByEmpSystemID'] = $srMaster->approvedByUserSystemID;
                        $data['documentNarration'] = $glComment;
                        $data['clientContractID'] = 'X';
                        $data['contractUID'] = 159;
                        $data['supplierCodeSystem'] = $bookInvSuppMaster->supplierID;
                        $data['holdingShareholder'] = null;
                        $data['holdingPercentage'] = 0;
                        $data['nonHoldingPercentage'] = 0;
                        $data['createdUserID'] = $srMaster->approvedByUserID;
                        $data['createdUserSystemID'] = $srMaster->approvedByUserSystemID;
                        $data['createdUserPC'] = gethostname();
                        $data['documentRptCurrencyID'] = $toCompany->reportingCurrency;;
                        $data['documentRptCurrencyER'] = 1;
                        $data['documentTransCurrencyID'] = $toCompany->reportingCurrency;
                        $data['documentTransCurrencyER'] = 1;
                        $data['documentLocalCurrencyID'] = $toCompany->localCurrencyID;
                        $data['documentLocalCurrencyER'] = $companyCurrencyConversion['trasToLocER'];

                        $glAP = $data;  // AP control account GL Update
                        $glINC = $data;  // Inter Company Stock Transfer GL Update

                        if ($glAP) {
                            $glAP['chartOfAccountSystemID'] = $supplier->liabilityAccountSysemID;
                            $glAP['glCode'] = $supplier->liabilityAccount;
                            $glAP['glAccountType'] = 'BS';
                            $glAP['glAccountTypeID'] = 1;
                            $glAP['documentLocalAmount'] = ABS($bookingAmountLocal) * -1;
                            $glAP['documentRptAmount'] = ABS($bookingAmountRpt) * -1;
                            $glAP['documentTransAmount'] = ABS($bookingAmountRpt) * -1;
                            $glAP = $generalLedgerRepo->create($glAP);
                            Log::info($glAP);
                        }

                        if ($glINC) {
                            $glINC['chartOfAccountSystemID'] = SystemGlCodeScenarioDetail::getGlByScenario($srMaster->companySystemID, $srMaster->documentSystemID, "stock-transfer-pl-account-for-inter-company-transfer");
                            $glINC['glCode'] = SystemGlCodeScenarioDetail::getGlCodeByScenario($srMaster->companySystemID, $srMaster->documentSystemID, "stock-transfer-pl-account-for-inter-company-transfer");
                            $glINC['glAccountType'] = 'BS';
                            $glINC['glAccountTypeID'] = 1;
                            $glINC['documentLocalAmount'] = ABS($bookingAmountLocal);
                            $glINC['documentRptAmount'] = ABS($bookingAmountRpt);
                            $glINC['documentTransAmount'] = ABS($bookingAmountRpt);
                            $interCompanyGL = $generalLedgerRepo->create($glINC);
                            Log::info($interCompanyGL);
                        }
                        // GL end

                        // AP update start

                        $apLedger = array();
                        $apLedger['companySystemID'] = $bookInvSuppMaster->companySystemID;
                        $apLedger['companyID'] = $bookInvSuppMaster->companyID;
                        $apLedger['documentSystemID'] = $bookInvSuppMaster->documentSystemID;
                        $apLedger['documentID'] = $bookInvSuppMaster->documentID;
                        $apLedger['documentSystemCode'] = $bookInvSuppMaster->bookingSuppMasInvAutoID;
                        $apLedger['documentCode'] = $bookInvSuppMaster->bookingInvCode;
                        $apLedger['documentDate'] = $today;
                        $apLedger['supplierCodeSystem'] = $supplier->supplierCodeSystem;
                        $apLedger['supplierInvoiceNo'] = $bookInvSuppMaster->supplierInvoiceNo;
                        $apLedger['supplierInvoiceDate'] = $bookInvSuppMaster->supplierInvoiceDate;
                        $apLedger['supplierTransCurrencyID'] = $toCompany->reportingCurrency;
                        $apLedger['supplierTransER'] = 1;
                        $apLedger['supplierInvoiceAmount'] = ABS($bookingAmountRpt);
                        $apLedger['supplierDefaultCurrencyID'] = $toCompany->reportingCurrency;
                        $apLedger['supplierDefaultCurrencyER'] = 1;
                        $apLedger['supplierDefaultAmount'] = ABS($bookingAmountRpt);
                        $apLedger['localCurrencyID'] = $toCompany->localCurrencyID;
                        $apLedger['localER'] = $companyCurrencyConversion['trasToLocER'];
                        $apLedger['localAmount'] = ABS($bookingAmountLocal);
                        $apLedger['comRptCurrencyID'] = $toCompany->reportingCurrency;
                        $apLedger['comRptER'] = 1;
                        $apLedger['comRptAmount'] = ABS($bookingAmountRpt);
                        $apLedger['isInvoiceLockedYN'] = 0;
                        $apLedger['invoiceType'] = 0;
                        $apLedger['selectedToPaymentInv'] = 0;
                        $apLedger['fullyInvoice'] = 0;
                        $apLedger['createdUserID'] = $srMaster->approvedByUserID;
                        $apLedger['createdPcID'] = gethostname();

                        $accountsPayableLedger = $accountsPayableLedgerRepo->create($apLedger);

                        Log::info($accountsPayableLedger);
                        // AP update end

                        $checkStockReceive = InterCompanyStockTransfer::where('stockReceiveID', $sr->stockReceiveAutoID)->first();

                        if ($checkStockReceive) {
                            $checkStockReceive->supplierInvoiceID = $bookInvSuppMaster->bookingSuppMasInvAutoID;
                            $checkStockReceive->save();
                        }

                        if ($checkStockReceive) {
                            $consoleJVData = [
                                'data' => $checkStockReceive,
                                'type' => "STOCK_TRANSFER"
                            ];

                            CreateConsoleJV::dispatch($consoleJVData);
                        }
                    }


                    Log::info('Successfully end  supplier_invoice' . date('H:i:s'));
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
