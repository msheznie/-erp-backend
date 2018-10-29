<?php

namespace App\Jobs;

use App\Models\AssetDisposalDetail;
use App\Models\ChartOfAccount;
use App\Models\Company;
use App\Models\CompanyFinancePeriod;
use App\Models\CompanyFinanceYear;
use App\Models\CustomerInvoiceDirect;
use App\Models\CustomerMaster;
use App\Models\SegmentMaster;
use App\Repositories\CustomerInvoiceDirectDetailRepository;
use App\Repositories\CustomerInvoiceDirectRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CreateCustomerInvoice implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $disposalMaster;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($disposalMaster)
    {
        $this->disposalMaster = $disposalMaster;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(CustomerInvoiceDirectRepository $customerInvoiceRep,
                           CustomerInvoiceDirectDetailRepository $customerInvoiceDetailRep)
    {
        Log::useFiles(storage_path() . '/logs/create_customer_invoice_jobs.log');
        $dpMaster = $this->disposalMaster;
        DB::beginTransaction();
        try {
            $customerInvoiceData = array();
            $customerInvoiceData['transactionMode'] = null;
            $customerInvoiceData['companySystemID'] = $dpMaster->companySystemID;
            $customerInvoiceData['companyID'] = $dpMaster->companyID;
            $customerInvoiceData['documentSystemiD'] = 20;
            $customerInvoiceData['documentID'] = 'INV';
            $fromCompanyFinancePeriod = CompanyFinancePeriod::where('companySystemID', $dpMaster->companySystemID)
                ->where('departmentSystemID', 4)
                ->where('isActive', -1)
                ->where('isCurrent', -1)
                ->first();

            $today = NOW();
            $comment = "Inter Company Asset transfer " . $dpMaster->disposalDocumentCode;

            if (!empty($fromCompanyFinancePeriod)) {
                $fromCompanyFinanceYear = CompanyFinanceYear::where('companyFinanceYearID', $fromCompanyFinancePeriod->companyFinanceYearID)
                    ->where('companySystemID', $dpMaster->companySystemID)
                    ->first();

                if (!empty($fromCompanyFinanceYear)) {
                    $customerInvoiceData['FYBiggin'] = $fromCompanyFinanceYear->bigginingDate;
                    $customerInvoiceData['FYEnd'] = $fromCompanyFinanceYear->endingDate;
                }

                $customerInvoiceData['companyFinanceYearID'] = $fromCompanyFinancePeriod->companyFinanceYearID;
                $customerInvoiceData['companyFinancePeriodID'] = $fromCompanyFinancePeriod->companyFinancePeriodID;
                $customerInvoiceData['FYPeriodDateFrom'] = $fromCompanyFinancePeriod->dateFrom;
                $customerInvoiceData['FYPeriodDateTo'] = $fromCompanyFinancePeriod->dateTo;

                $cusInvLastSerial = CustomerInvoiceDirect::where('companySystemID', $dpMaster->companySystemID)
                    ->where('companyFinanceYearID', $fromCompanyFinancePeriod->companyFinanceYearID)
                    ->where('serialNo', '>', 0)
                    ->orderBy('serialNo', 'desc')
                    ->first();

                $cusInvLastSerialNumber = 1;
                if ($cusInvLastSerial) {
                    $cusInvLastSerialNumber = intval($cusInvLastSerial->serialNo) + 1;
                }
                $customerInvoiceData['serialNo'] = $cusInvLastSerialNumber;

                $serviceLine = SegmentMaster::ofCompany([$dpMaster->companySystemID])->isPublic()->first();

                if ($serviceLine) {
                    $customerInvoiceData['serviceLineSystemID'] = $serviceLine->serviceLineSystemID;
                    $customerInvoiceData['serviceLineCode'] = $serviceLine->serviceLineCode;
                }

                if ($fromCompanyFinancePeriod) {
                    $cusStartYear = $fromCompanyFinanceYear->bigginingDate;
                    $cusFinYearExp = explode('-', $cusStartYear);
                    $cusFinYear = $cusFinYearExp[0];
                } else {
                    $cusFinYear = date("Y");
                }
                $bookingInvCode = ($dpMaster->companyID . '\\' . $cusFinYear . '\\' . $customerInvoiceData['documentID'] . str_pad($cusInvLastSerialNumber, 6, '0', STR_PAD_LEFT));
                $customerInvoiceData['bookingInvCode'] = $bookingInvCode;
                $customerInvoiceData['bookingDate'] = $today;

                $customerInvoiceData['comments'] = $comment;

                $customer = CustomerMaster::where('companyLinkedToSystemID', $dpMaster->companySystemID)->first();

                if (!empty($customer)) {
                    $customerInvoiceData['customerID'] = $customer->customerCodeSystem;
                    $customerInvoiceData['customerGLCode'] = $customer->custGLaccount;
                    $customerInvoiceData['customerInvoiceNo'] = $dpMaster->disposalDocumentCode;
                    $customerInvoiceData['customerInvoiceDate'] = $today;
                }
                $customerInvoiceData['invoiceDueDate'] = $today;
                $customerInvoiceData['serviceStartDate'] = $today;
                $customerInvoiceData['serviceEndDate'] = $today;
                $customerInvoiceData['performaDate'] = $today;

                $fromCompany = Company::where('companySystemID', $dpMaster->companySystemID)->first();

                if ($fromCompany) {
                    $companyCurrencyConversion = \Helper::currencyConversion($dpMaster->companySystemID, $fromCompany->localCurrencyID, $fromCompany->localCurrencyID, 0);
                    $customerInvoiceData['companyReportingCurrencyID'] = $fromCompany->reportingCurrency;
                    $customerInvoiceData['companyReportingER'] = $companyCurrencyConversion['trasToRptER'];

                    $customerInvoiceData['localCurrencyID'] = $fromCompany->localCurrencyID;
                    $customerInvoiceData['localCurrencyER'] = $companyCurrencyConversion['trasToLocER'];

                    $customerInvoiceData['custTransactionCurrencyID'] = $fromCompany->localCurrencyID;
                    $customerInvoiceData['custTransactionCurrencyER'] = 1;
                }

                $disposalDetailSUM = AssetDisposalDetail::selectRaw('SUM(COSTUNIT) as ‌sumOfCOSTUNIT,SUM(costUnitRpt) as ‌sumOfCOSTUNITRPT,SUM(depAmountLocal) as sumOfdepAmountLocal‌,SUM(depAmountRpt) as sumOfdepAmountRpt‌')->OfMaster($dpMaster->assetdisposalMasterAutoID)->first();

                $disposalDetail = AssetDisposalDetail::OfMaster($dpMaster->assetdisposalMasterAutoID)->get();

                $localAmount = 0;
                $comRptAmount = 0;

                if (count($disposalDetail) > 0) {
                    foreach ($disposalDetail as $val) {
                        if ($val->netBookValueLocal == 0) {
                            $localAmount += $val->COSTUNIT * ($dpMaster->revenuePercentage / 100);
                        } else {
                            $localAmount += (($disposalDetailSUM->‌sumOfCOSTUNIT - $disposalDetailSUM->sumOfdepAmountLocal‌) + ((($disposalDetailSUM->‌sumOfCOSTUNIT - $disposalDetailSUM->sumOfdepAmountLocal‌)) * ($dpMaster->revenuePercentage / 100)));
                        }
                        if ($val->netBookValueRpt == 0) {
                            $comRptAmount += $val->costUnitRpt * ($dpMaster->revenuePercentage / 100);
                        } else {
                            $comRptAmount += (($disposalDetailSUM->‌sumOfCOSTUNITRPT - $disposalDetailSUM->sumOfdepAmountRpt‌) + ((($disposalDetailSUM->‌sumOfCOSTUNITRPT - $disposalDetailSUM->sumOfdepAmountRpt‌)) * ($dpMaster->revenuePercentage / 100)));
                        }
                    }
                }

                $customerInvoiceData['bookingAmountTrans'] = $localAmount;
                $customerInvoiceData['bookingAmountLocal'] = $localAmount;
                $customerInvoiceData['bookingAmountRpt'] = $comRptAmount;
                $customerInvoiceData['confirmedYN'] = 1;
                $customerInvoiceData['confirmedByEmpSystemID'] = $dpMaster->confimedByEmpSystemID;
                $customerInvoiceData['confirmedByEmpID'] = $dpMaster->confirmedByEmpID;
                $customerInvoiceData['confirmedByName'] = $dpMaster->confirmedByEmpName;
                $customerInvoiceData['confirmedDate'] = $dpMaster->confirmedDate;
                $customerInvoiceData['approved'] = -1;
                $customerInvoiceData['approvedDate'] = $dpMaster->approvedDate;
                $customerInvoiceData['documentType'] = 11;
                $customerInvoiceData['interCompanyTransferYN'] = -1;
                $customerInvoiceData['createdUserSystemID'] = $dpMaster->confirmedByEmpSystemID;
                $customerInvoiceData['createdUserID'] = $dpMaster->confirmedByEmpID;
                $customerInvoiceData['createdPcID'] = $dpMaster->modifiedPc;

                $customerInvoice = $customerInvoiceRep->create($customerInvoiceData);

                $cusInvoiceDetails = array();
                $cusInvoiceDetails['custInvoiceDirectID'] = $customerInvoice->custInvoiceDirectAutoID;
                $cusInvoiceDetails['companyID'] = $dpMaster->toCompanyID;
                if ($serviceLine) {
                    $cusInvoiceDetails['serviceLineSystemID'] = $serviceLine->serviceLineSystemID;
                    $cusInvoiceDetails['serviceLineCode'] = $serviceLine->serviceLineCode;
                }
                $cusInvoiceDetails['customerID'] = $customer->customerCodeSystem;
                $chartofAccount = ChartOfAccount::find(557);
                $cusInvoiceDetails['glSystemID'] = 557;
                $cusInvoiceDetails['glCode'] = $chartofAccount->AccountCode;
                $cusInvoiceDetails['glCodeDes'] = $chartofAccount->AccountDescription;
                $cusInvoiceDetails['accountType'] = $chartofAccount->catogaryBLorPL;
                $cusInvoiceDetails['comments'] = $comment;
                $cusInvoiceDetails['unitOfMeasure'] = 7;
                $cusInvoiceDetails['invoiceQty'] = 1;
                $cusInvoiceDetails['invoiceAmountCurrency'] = $fromCompany->localCurrencyID;;
                $cusInvoiceDetails['invoiceAmountCurrencyER'] = 1;
                $cusInvoiceDetails['localCurrency'] = $fromCompany->localCurrencyID;
                $cusInvoiceDetails['localCurrencyER'] = $companyCurrencyConversion['trasToLocER'];
                $cusInvoiceDetails['comRptCurrency'] = $fromCompany->reportingCurrency;;
                $cusInvoiceDetails['comRptCurrencyER'] = $companyCurrencyConversion['trasToRptER'];
                $cusInvoiceDetails['clientContractID'] = 0;
                $cusInvoiceDetails['performaMasterID'] = 0;

                $cusInvoiceDetails['localAmount'] = $localAmount;
                $cusInvoiceDetails['comRptAmount'] = $comRptAmount;
                $cusInvoiceDetails['invoiceAmount'] = $localAmount;
                $cusInvoiceDetails['unitCost'] = $localAmount;

                $customerInvoiceDet = $customerInvoiceDetailRep->create($cusInvoiceDetails);

                $masterModel = ['documentSystemID' => 20, 'autoID' => $customerInvoice->custInvoiceDirectAutoID, 'companySystemID' => $dpMaster->companySystemID, 'employeeSystemID' => $dpMaster->confirmedByEmpSystemID];
                $generalLedgerInsert = GeneralLedgerInsert::dispatch($masterModel);

                DB::commit();
            }
        } catch
        (\Exception $e) {
            DB::rollback();
            Log::error($e->getMessage());
        }
    }
}
