<?php

namespace App\Jobs;

use App\Models\Company;
use App\Models\CompanyFinancePeriod;
use App\Models\CompanyFinanceYear;
use App\Models\CustomerInvoiceDirect;
use App\Models\CustomerMaster;
use App\Models\DocumentMaster;
use App\Models\StockReceive;
use App\Models\StockReceiveDetails;
use App\Models\StockTransfer;
use App\Models\StockTransferDetails;
use App\Repositories\CustomerInvoiceDirectDetailRepository;
use App\Repositories\CustomerInvoiceDirectRepository;
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
                           CustomerInvoiceDirectDetailRepository $customerInvoiceDetailRep)
    {
        Log::useFiles(storage_path() . '/logs/create_stock_receive_jobs.log');
        $st = $this->stMaster;
        $stMaster = StockTransfer::where('stockTransferAutoID', $st->stockTransferAutoID)->first();
        if (!empty($stMaster)) {
            DB::beginTransaction();
            try {
                Log::info('Successfully start  stock_receive' . date('H:i:s'));
                if ($stMaster->interCompanyTransferYN == -1) {

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
                        $bookingInvCode = ($stMaster->companyID . '\\' . $cusFinYear . '\\' . $customerInvoiceData['documentID'] . str_pad($cusInvLastSerialNumber, 6, '0', STR_PAD_LEFT));;
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
                            $customerInvoiceData['companyReportingCurrencyID'] = $fromCompany->reportingCurrency;
                            $customerInvoiceData['companyReportingER'] = 1;

                            $customerInvoiceData['localCurrencyID'] = $fromCompany->localCurrencyID;
                            $customerInvoiceData['localCurrencyER'] = 1;

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
                        $cusInvoiceDetails['localCurrencyER'] = 1;
                        $cusInvoiceDetails['comRptCurrency'] = $fromCompany->reportingCurrency;;
                        $cusInvoiceDetails['comRptCurrencyER'] = 1;


                        $glBS = $cusInvoiceDetails;
                        $glPL = $cusInvoiceDetails;

                        $glBS['glCode'] = '91582';
                        $glBS['glCodeDes'] = 'Product Revenue -Intercompany';
                        $glBS['accountType'] = 'PL';
                        $glBS['invoiceAmount'] = $revenueTotalRpt;
                        $glBS['unitCost']      = $revenueTotalRpt;
                        $glBS['localAmount']   =   $revenueTotalLocal;
                        $glBS['comRptAmount']  =  $revenueTotalRpt;

                        $glPL['glCode']        = '20023';
                        $glPL['glCodeDes']     = 'Intercompany stock transfer';
                        $glPL['accountType']   = 'BS';
                        $glPL['invoiceAmount'] = $totalRpt;
                        $glPL['unitCost']      = $totalRpt;
                        $glPL['localAmount']   = $totalLocal;
                        $glPL['comRptAmount']  = $totalRpt;

                        $customerInvoiceDetailPL = $customerInvoiceDetailRep->create($glPL);
                        $customerInvoiceDetailBS = $customerInvoiceDetailRep->create($glBS);

                        Log::info($customerInvoice);
                        Log::info($customerInvoiceDetailPL);
                        Log::info($customerInvoiceDetailBS);
                    }

                    $push = true;
                    if ($push) {
                        $lastSerial = StockReceive::where('companySystemID', $stMaster->companyToSystemID)
                            ->where('companyFinanceYearID', $stMaster->companyFinanceYearID)
                            ->orderBy('stockReceiveAutoID', 'desc')
                            ->first();

                        $lastSerialNumber = 1;
                        if ($lastSerial) {
                            $lastSerialNumber = intval($lastSerial->serialNo) + 1;
                        }

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
                        $stockReceive->serialNo = $lastSerialNumber;
                        $stockReceive->refNo = $stMaster->refNo;
                        $stockReceive->comment = $stMaster->comment;
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
                                                                        ->where('dateFrom', '<', $stMaster->tranferDate)
                                                                        ->where('dateTo', '>', $stMaster->tranferDate)
                                                                        ->where('isCurrent', -1)
                                                                        ->first();

                        if (!empty($toCompanyFinancePeriod)) {
                            $companyFinanceYear = CompanyFinanceYear::where('companyFinanceYearID', $toCompanyFinancePeriod->companyFinanceYearID)
                                ->where('companySystemID', $stMaster->companyToSystemID)
                                ->first();

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

                        $stockTransferCode = ($stockReceive->companyID . '\\' . $finYear . '\\' . $stockReceive->documentID . str_pad($lastSerialNumber, 6, '0', STR_PAD_LEFT));
                        $stockReceive->stockReceiveCode = $stockTransferCode;
                        $stockReceive->save();
                        foreach ($stDetails as $new) {

                            $item = array();
                            $item['stockReceiveAutoID'] = $stockReceive->stockReceiveAutoID;
                            $item['stockReceiveCode'] = $stockReceive->stockReceiveCode;
                            $item['stockTransferAutoID'] = $stMaster->stockTransferAutoID;
                            $item['stockTransferCode'] = $stMaster->stockTransferCode;
                            $item['stockTransferDate'] = $stMaster->tranferDate;

                            $item['itemCodeSystem'] = $new['itemCodeSystem'];
                            $item['itemPrimaryCode'] = $new['itemPrimaryCode'];
                            $item['itemDescription'] = $new['itemDescription'];
                            $item['unitOfMeasure'] = $new['unitOfMeasure'];
                            $item['itemFinanceCategoryID'] = $new['itemFinanceCategoryID'];
                            $item['itemFinanceCategorySubID'] = $new['itemFinanceCategorySubID'];
                            $item['financeGLcodebBS'] = $new['financeGLcodebBS'];
                            $item['financeGLcodebBSSystemID'] = $new['financeGLcodebBSSystemID'];
                            $item['localCurrencyID'] = $new['localCurrencyID'];
                            $item['unitCostLocal'] = $new['unitCostLocal'] * 1.03;
                            $item['reportingCurrencyID'] = $new['reportingCurrencyID'];
                            $item['unitCostRpt'] = $new['unitCostRpt'] * 1.03;
                            $item['qty'] = $new['qty'];

                            if ($item['unitCostLocal'] <= 0 || $item['unitCostRpt'] <= 0) {
                                // return $this->sendError("Cost is not updated", 500);
                            } else {
                                $srdItem = StockReceiveDetails::insert($item);
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
