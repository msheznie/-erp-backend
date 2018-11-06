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
            $comment = "Inter Company Asset transfer from " . $dpMaster->companyID . " to " . $dpMaster->toCompanyID . " - " . $dpMaster->disposalDocumentCode;

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
                    $customerInvoiceData['serviceLineCode'] = $serviceLine->ServiceLineCode;
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
                    $customerInvoiceData['customerGLSystemID'] = $customer->custGLAccountSystemID;
                    $customerInvoiceData['customerInvoiceNo'] = $dpMaster->disposalDocumentCode;
                    $customerInvoiceData['customerInvoiceDate'] = $today;
                }
                $customerInvoiceData['invoiceDueDate'] = $today;
                $customerInvoiceData['serviceStartDate'] = $today;
                $customerInvoiceData['serviceEndDate'] = $today;
                $customerInvoiceData['performaDate'] = $today;

                $fromCompany = Company::where('companySystemID', $dpMaster->companySystemID)->first();

                $companyCurrencyConversion = \Helper::currencyConversion($dpMaster->companySystemID, $fromCompany->localCurrencyID, $fromCompany->localCurrencyID, 0);
                $customerInvoiceData['companyReportingCurrencyID'] = $fromCompany->reportingCurrency;
                $customerInvoiceData['companyReportingER'] = $companyCurrencyConversion['trasToRptER'];

                $customerInvoiceData['localCurrencyID'] = $fromCompany->localCurrencyID;
                $customerInvoiceData['localCurrencyER'] = $companyCurrencyConversion['trasToLocER'];

                $customerInvoiceData['custTransactionCurrencyID'] = $fromCompany->localCurrencyID;
                $customerInvoiceData['custTransactionCurrencyER'] = 1;

                $disposalDetail = AssetDisposalDetail::OfMaster($dpMaster->assetdisposalMasterAutoID)->get();

                $localAmount = 0;
                $comRptAmount = 0;

                if (count($disposalDetail) > 0) {
                    foreach ($disposalDetail as $val) {
                        if ($val->netBookValueLocal == 0) {
                            $localAmount += $val->COSTUNIT * ($dpMaster->revenuePercentage / 100);
                        } else {
                            $localAmount += (($val->COSTUNIT - $val->depAmountLocal‌) + ((($val->COSTUNIT - $val->depAmountLocal‌)) * ($dpMaster->revenuePercentage / 100)));
                        }
                        if ($val->netBookValueRpt == 0) {
                            $comRptAmount += $val->costUnitRpt * ($dpMaster->revenuePercentage / 100);
                        } else {
                            $comRptAmount += (($val->costUnitRpt - $val->depAmountRpt‌) + ((($val->costUnitRpt - $val->depAmountRpt‌)) * ($dpMaster->revenuePercentage / 100)));
                        }
                    }
                }

                $customerInvoiceData['bookingAmountTrans'] = \Helper::roundValue($localAmount);
                $customerInvoiceData['bookingAmountLocal'] = \Helper::roundValue($localAmount);
                $customerInvoiceData['bookingAmountRpt'] = \Helper::roundValue($comRptAmount);
                $customerInvoiceData['confirmedYN'] = 1;
                $customerInvoiceData['confirmedByEmpSystemID'] = $dpMaster->confimedByEmpSystemID;
                $customerInvoiceData['confirmedByEmpID'] = $dpMaster->confimedByEmpID;
                $customerInvoiceData['confirmedByName'] = $dpMaster->confirmedByEmpName;
                $customerInvoiceData['confirmedDate'] = $dpMaster->confirmedDate;
                $customerInvoiceData['approved'] = -1;
                $customerInvoiceData['approvedDate'] = $dpMaster->approvedDate;
                $customerInvoiceData['documentType'] = 11;
                $customerInvoiceData['interCompanyTransferYN'] = -1;
                $customerInvoiceData['createdUserSystemID'] = $dpMaster->confirmedByEmpSystemID;
                $customerInvoiceData['createdUserID'] = $dpMaster->confirmedByEmpID;
                $customerInvoiceData['createdPcID'] = $dpMaster->modifiedPc;
                Log::info($customerInvoiceData);
                $customerInvoice = $customerInvoiceRep->create($customerInvoiceData);

                $cusInvoiceDetails = array();
                $cusInvoiceDetails['custInvoiceDirectID'] = $customerInvoice->custInvoiceDirectAutoID;
                $cusInvoiceDetails['companyID'] = $dpMaster->companyID;
                if ($serviceLine) {
                    $cusInvoiceDetails['serviceLineSystemID'] = $serviceLine->serviceLineSystemID;
                    $cusInvoiceDetails['serviceLineCode'] = $serviceLine->ServiceLineCode;
                }
                $cusInvoiceDetails['customerID'] = $customer->customerCodeSystem;
                $chartofAccount = ChartOfAccount::find(557);
                $cusInvoiceDetails['glSystemID'] = 557;
                $cusInvoiceDetails['glCode'] = $chartofAccount->AccountCode;
                $cusInvoiceDetails['glCodeDes'] = $chartofAccount->AccountDescription;
                $cusInvoiceDetails['accountType'] = $chartofAccount->catogaryBLorPL;
                $comment = "Inter Company Asset transfer " . $dpMaster->disposalDocumentCode;
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

                $cusInvoiceDetails['localAmount'] = \Helper::roundValue($localAmount);
                $cusInvoiceDetails['comRptAmount'] = \Helper::roundValue($comRptAmount);
                $cusInvoiceDetails['invoiceAmount'] = \Helper::roundValue($localAmount);
                $cusInvoiceDetails['unitCost'] = \Helper::roundValue($localAmount);
                Log::info($cusInvoiceDetails);
                $customerInvoiceDet = $customerInvoiceDetailRep->create($cusInvoiceDetails);

                $masterModel = ['documentSystemID' => 20, 'autoID' => $customerInvoice->custInvoiceDirectAutoID, 'companySystemID' => $dpMaster->companySystemID, 'employeeSystemID' => $dpMaster->confimedByEmpSystemID];
                $generalLedgerInsert = GeneralLedgerInsert::dispatch($masterModel);
                $dpMaster['bookingInvCode'] = $bookingInvCode;
                $grvInsert = CreateDirectGRV::dispatch($dpMaster);

                DB::commit();
            }
        } catch
        (\Exception $e) {
            DB::rollback();
            Log::error($this->failed($e));
        }
    }

    public function failed($exception)
    {
        return $exception->getMessage();
    }
}
