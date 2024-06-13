<?php

namespace App\Jobs;

use App\Models\AssetDisposalDetail;
use App\Models\ChartOfAccount;
use App\Models\Company;
use App\Models\CompanyFinancePeriod;
use App\Models\CompanyFinanceYear;
use App\Models\CustomerInvoiceDirect;
use App\Models\SystemGlCodeScenarioDetail;
use App\Models\CustomerMaster;
use App\Models\InterCompanyAssetDisposal;
use App\Models\SegmentMaster;
use App\Repositories\CustomerInvoiceDirectDetailRepository;
use App\Repositories\CustomerInvoiceDirectRepository;
use Carbon\Carbon;
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
    protected $dataBase;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($disposalMaster, $dataBase)
    {
        $this->disposalMaster = $disposalMaster;
        $this->dataBase = $dataBase;
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

            $disposalDocumentDate = (new Carbon($dpMaster->disposalDocumentDate))->format('Y-m-d');

            $fromCompanyFinanceYear = CompanyFinanceYear::where('companySystemID', $dpMaster->companySystemID)
                ->whereDate('bigginingDate', '<=', $disposalDocumentDate)
                ->whereDate('endingDate', '>=', $disposalDocumentDate)
                ->first();

            if (!empty($fromCompanyFinanceYear)) {

                $fromCompanyFinancePeriod = CompanyFinancePeriod::where('companySystemID', $dpMaster->companySystemID)
                    ->where('departmentSystemID', 4)
                    ->where('companyFinanceYearID', $fromCompanyFinanceYear->companyFinanceYearID)
                    ->whereDate('dateFrom', '<=', $disposalDocumentDate)
                    ->whereDate('dateTo', '>=', $disposalDocumentDate)
                    ->first();


                if (!empty($fromCompanyFinancePeriod)) {


                    $today = $dpMaster->disposalDocumentDate;
                    $comment = "Inter Company Asset transfer from " . $dpMaster->companyID . " to " . $dpMaster->toCompanyID . " - " . $dpMaster->disposalDocumentCode;

                    if (!empty($fromCompanyFinanceYear)) {

                        $customerInvoiceData['FYBiggin'] = $fromCompanyFinanceYear->bigginingDate;
                        $customerInvoiceData['FYEnd'] = $fromCompanyFinanceYear->endingDate;

                        if (!empty($fromCompanyFinancePeriod)) {
                            $customerInvoiceData['companyFinanceYearID'] = $fromCompanyFinancePeriod->companyFinanceYearID;
                            $customerInvoiceData['companyFinancePeriodID'] = $fromCompanyFinancePeriod->companyFinancePeriodID;
                            $customerInvoiceData['FYPeriodDateFrom'] = $fromCompanyFinancePeriod->dateFrom;
                            $customerInvoiceData['FYPeriodDateTo'] = $fromCompanyFinancePeriod->dateTo;
                        }
                    }

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

                    $customer = CustomerMaster::where('companyLinkedToSystemID', $dpMaster->toCompanySystemID)->first();

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

                    $disposalDetail = AssetDisposalDetail::selectRaw('SUM(netBookValueLocal) as netBookValueLocal, SUM(netBookValueRpt) as netBookValueRpt, SUM(COSTUNIT) as COSTUNIT, SUM(depAmountLocal) as depAmountLocal, SUM(costUnitRpt) as costUnitRpt, SUM(depAmountRpt) as depAmountRpt, serviceLineSystemID, ServiceLineCode, 
            SUM(if(ROUND(netBookValueLocal,2) = 0,COSTUNIT + COSTUNIT * (revenuePercentage/100),netBookValueLocal + (netBookValueLocal * (revenuePercentage/100)))) as localAmountDetail, 
            SUM(if(ROUND(netBookValueRpt,2) = 0,costUnitRpt + costUnitRpt * (revenuePercentage/100),netBookValueRpt + (netBookValueRpt * (revenuePercentage/100)))) as comRptAmountDetail')->OfMaster($dpMaster->assetdisposalMasterAutoID)->groupBy('assetDisposalDetailAutoID')->get();

                    $localAmount = 0;
                    $comRptAmount = 0;

                    if (count($disposalDetail) > 0) {
                        foreach ($disposalDetail as $val) {
                            $localAmount += $val->localAmountDetail;
                            $comRptAmount += $val->comRptAmountDetail;
                        }
                    }

                    $customerInvoiceData['bookingAmountTrans'] = \Helper::roundValue($localAmount);
                    $customerInvoiceData['bookingAmountLocal'] = \Helper::roundValue($localAmount);
                    $customerInvoiceData['bookingAmountRpt'] = \Helper::roundValue($comRptAmount);
                    $customerInvoiceData['confirmedYN'] = 1;
                    $customerInvoiceData['confirmedByEmpSystemID'] = $dpMaster->confimedByEmpSystemID;
                    $customerInvoiceData['confirmedByEmpID'] = $dpMaster->confimedByEmpID;
                    $customerInvoiceData['confirmedByName'] = $dpMaster->confirmedByEmpName;
                    $customerInvoiceData['confirmedDate'] = $today;
                    $customerInvoiceData['approved'] = -1;
                    $customerInvoiceData['approvedDate'] = $today;
                    $customerInvoiceData['postedDate'] = $today;
                    $customerInvoiceData['documentType'] = 11;
                    $customerInvoiceData['interCompanyTransferYN'] = -1;
                    $customerInvoiceData['createdUserSystemID'] = $dpMaster->confimedByEmpSystemID;
                    $customerInvoiceData['createdUserID'] = $dpMaster->confimedByEmpID;
                    $customerInvoiceData['createdPcID'] = $dpMaster->modifiedPc;
                    $customerInvoiceData['createdDateAndTime'] = NOW();
                    Log::info($customerInvoiceData);
                    $customerInvoice = $customerInvoiceRep->create($customerInvoiceData);

                    $interComAssetDisposal = [
                        'assetDisposalID' => $dpMaster->assetdisposalMasterAutoID,
                        'customerInvoiceID' => $customerInvoice->custInvoiceDirectAutoID
                    ];

                    InterCompanyAssetDisposal::create($interComAssetDisposal);

                    $disposalDetail = AssetDisposalDetail::selectRaw('SUM(netBookValueLocal) as netBookValueLocal, SUM(netBookValueRpt) as netBookValueRpt, SUM(COSTUNIT) as COSTUNIT, SUM(depAmountLocal) as depAmountLocal, SUM(costUnitRpt) as costUnitRpt, SUM(depAmountRpt) as depAmountRpt, serviceLineSystemID, ServiceLineCode, 
            SUM(if(ROUND(netBookValueLocal,2) = 0,COSTUNIT + COSTUNIT * (revenuePercentage/100),netBookValueLocal + (netBookValueLocal * (revenuePercentage/100)))) as localAmountDetail, 
            SUM(if(ROUND(netBookValueRpt,2) = 0,costUnitRpt + costUnitRpt * (revenuePercentage/100),netBookValueRpt + (netBookValueRpt * (revenuePercentage/100)))) as comRptAmountDetail,SUM(sellingPriceLocal) as sellingPriceLocal,SUM(sellingPriceRpt) as sellingPriceRpt')->OfMaster($dpMaster->assetdisposalMasterAutoID)->groupBy('serviceLineSystemID')->get();

                    if ($disposalDetail) {
                        $accID = SystemGlCodeScenarioDetail::getGlByScenario($dpMaster->companySystemID, $dpMaster->documentSystemID, "asset-disposal-inter-company-sales");
                        $chartofAccount = ChartOfAccount::find($accID);
                        $comment = "Inter Company Asset transfer " . $dpMaster->disposalDocumentCode;
                        foreach ($disposalDetail as $val) {
                            $cusInvoiceDetails['custInvoiceDirectID'] = $customerInvoice->custInvoiceDirectAutoID;
                            $cusInvoiceDetails['companyID'] = $dpMaster->companyID;
                            $cusInvoiceDetails['serviceLineSystemID'] = $val->serviceLineSystemID;
                            $cusInvoiceDetails['serviceLineCode'] = $val->ServiceLineCode;
                            if ($customer) {
                                $cusInvoiceDetails['customerID'] = $customer->customerCodeSystem;
                            }
                            $cusInvoiceDetails['glSystemID'] = $accID;
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
                            $cusInvoiceDetails['clientContractID'] = 'X';
                            $cusInvoiceDetails['contractID'] = 159;
                            $cusInvoiceDetails['performaMasterID'] = 0;

                            $localAmountDetail = $val->sellingPriceLocal;
                            $comRptAmountDetail = $val->sellingPriceRpt;

                            /*$localAmountDetail = $val->localAmountDetail;
                            $comRptAmountDetail = $val->comRptAmountDetail;*/

                            /*if (round($val->netBookValueLocal, 2) == 0) {
                                $localAmountDetail = $val->COSTUNIT * ($dpMaster->revenuePercentage / 100);
                            } else {
                                $localAmountDetail = ($val->netBookValueLocal‌ + (($val->netBookValueLocal) * ($dpMaster->revenuePercentage / 100)));
                            }
                            if (round($val->netBookValueRpt, 2) == 0) {
                                $comRptAmountDetail = $val->costUnitRpt * ($dpMaster->revenuePercentage / 100);
                            } else {
                                $comRptAmountDetail = ($val->netBookValueRpt + (($val->netBookValueRpt) * ($dpMaster->revenuePercentage / 100)));
                            }*/

                            $cusInvoiceDetails['localAmount'] = \Helper::roundValue($localAmountDetail);
                            $cusInvoiceDetails['comRptAmount'] = \Helper::roundValue($comRptAmountDetail);
                            $cusInvoiceDetails['invoiceAmount'] = \Helper::roundValue($localAmountDetail);
                            $cusInvoiceDetails['unitCost'] = \Helper::roundValue($localAmountDetail);
                            Log::info($cusInvoiceDetails);
                            $customerInvoiceDet = $customerInvoiceDetailRep->create($cusInvoiceDetails);
                        }
                    }

                    $masterModel = ['documentSystemID' => 20, 'autoID' => $customerInvoice->custInvoiceDirectAutoID, 'companySystemID' => $dpMaster->companySystemID, 'employeeSystemID' => $dpMaster->confimedByEmpSystemID];
                    $generalLedgerInsert = GeneralLedgerInsert::dispatch($masterModel, $this->dataBase);


                    $dpMaster2['invoiceCode'] = $bookingInvCode;
                    $dpMaster2['assetdisposalMasterAutoID'] = $dpMaster->assetdisposalMasterAutoID;
                    $grvInsert = CreateDirectGRV::dispatch($dpMaster2);

                    DB::commit();
                    Log::info('Customer invoice created successfully');
                }
                else {
                    Log::info('From Company Finance Period not found, date : '. $dpMaster->disposalDocumentDate);
                    Log::info('From Company Finance Year Id : '. $fromCompanyFinanceYear->companyFinanceYearID);
                }
            }else {
                Log::info('From Company Finance Year not found, date : '. $dpMaster->disposalDocumentDate);
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
