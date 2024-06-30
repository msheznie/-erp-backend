<?php

namespace App\Jobs;

use App\Models\BankAccount;
use App\Models\ChartOfAccount;
use App\Models\Company;
use App\Models\CompanyFinancePeriod;
use App\Models\CompanyFinanceYear;
use App\Models\CustomerReceivePayment;
use App\Models\DirectPaymentDetails;
use App\Models\SegmentMaster;
use App\Repositories\CustomerReceivePaymentRepository;
use App\Repositories\DirectPaymentDetailsRepository;
use App\Repositories\DirectReceiptDetailRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CreateReceiptVoucher implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $master;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($master)
    {
        $this->master = $master;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(DirectPaymentDetailsRepository $dpdetail, CustomerReceivePaymentRepository $crp, DirectReceiptDetailRepository $ddr)
    {
        Log::useFiles(storage_path() . '/logs/create_receipt_voucher_jobs.log');
        $pvMaster = $this->master;
        if ($pvMaster->invoiceType == 3) {
            DB::beginTransaction();
            try {
                $dpdetails = $dpdetail->findWhere(['directPaymentAutoID' => $pvMaster->PayMasterAutoId]);
                if (count($dpdetails) > 0) {
                    if ($pvMaster->expenseClaimOrPettyCash == 6 || $pvMaster->expenseClaimOrPettyCash == 7) {
                        $company = Company::find($pvMaster->interCompanyToSystemID);
                        $receivePayment['companySystemID'] = $pvMaster->interCompanyToSystemID;
                        $receivePayment['companyID'] = $company->CompanyID;
                        $receivePayment['documentSystemID'] = 21;
                        $receivePayment['documentID'] = 'BRV';

                        $companyFinanceYear = CompanyFinanceYear::where('companySystemID', $pvMaster->interCompanyToSystemID)->whereRaw('YEAR(bigginingDate) = ?', [date('Y')])->first();

                        $receivePayment['companyFinanceYearID'] = $companyFinanceYear->companyFinanceYearID;
                        $receivePayment['FYBiggin'] = $companyFinanceYear->bigginingDate;
                        $receivePayment['FYEnd'] = $companyFinanceYear->endingDate;

                        $companyFinancePeriod = CompanyFinancePeriod::where('companySystemID', $pvMaster->interCompanyToSystemID)->where('departmentSystemID', 4)->where('companyFinanceYearID', $companyFinanceYear->companyFinanceYearID)->whereRaw('DATE_FORMAT(dateFrom,"%Y-%m") = ?', [date('Y-m')])->first();
                        if($companyFinancePeriod) {
                            $receivePayment['FYPeriodDateFrom'] = $companyFinancePeriod->dateFrom;
                            $receivePayment['FYPeriodDateTo'] = $companyFinancePeriod->dateTo;
                        }

                        $BRVLastSerial = CustomerReceivePayment::where('companySystemID', $pvMaster->interCompanyToSystemID)
                            ->where('companyFinanceYearID', $companyFinancePeriod->companyFinanceYearID)
                            ->where('documentSystemID', 21)
                            ->where('serialNo', '>', 0)
                            ->orderBy('serialNo', 'desc')
                            ->first();

                        $cusInvLastSerialNumber = 1;
                        if ($BRVLastSerial) {
                            $cusInvLastSerialNumber = intval($BRVLastSerial->serialNo) + 1;
                        }
                        $receivePayment['serialNo'] = $cusInvLastSerialNumber;

                        if ($companyFinanceYear) {
                            $cusStartYear = $companyFinanceYear->bigginingDate;
                            $cusFinYearExp = explode('-', $cusStartYear);
                            $cusFinYear = $cusFinYearExp[0];
                        } else {
                            $cusFinYear = date("Y");
                        }
                        $docCode = ($company->CompanyID . '\\' . $cusFinYear . '\\' . $receivePayment['documentID'] . str_pad($cusInvLastSerialNumber, 6, '0', STR_PAD_LEFT));

                        $receivePayment['custPaymentReceiveCode'] = $docCode;
                        $receivePayment['custPaymentReceiveDate'] = $pvMaster->BPVdate;
                        $receivePayment['narration'] = $pvMaster->BPVNarration;
                        $receivePayment['intercompanyPaymentID'] = $pvMaster->PayMasterAutoId;
                        $receivePayment['intercompanyPaymentCode'] = $pvMaster->BPVcode;
                        $receivePayment['expenseClaimOrPettyCash'] = $pvMaster->expenseClaimOrPettyCash;

                        $dpdetails2 = DirectPaymentDetails::where('directPaymentAutoID',$pvMaster->PayMasterAutoId)->first();
                        if($dpdetails2) {
                            $receivePayment['custTransactionCurrencyID'] = $dpdetails2->toBankCurrencyID;
                            $receivePayment['custTransactionCurrencyER'] = 1;
                            $receivePayment['localCurrencyID'] = $dpdetails2->toCompanyLocalCurrencyID;
                            $receivePayment['localCurrencyER'] = $dpdetails2->toCompanyLocalCurrencyER;
                            $receivePayment['companyRptCurrencyID'] = $dpdetails2->toCompanyRptCurrencyID;
                            $receivePayment['companyRptCurrencyER'] = $dpdetails2->toCompanyRptCurrencyER;
                            $receivePayment['bankAmount'] = $dpdetails2->toBankAmount;
                            $receivePayment['receivedAmount'] = $dpdetails2->toBankAmount;
                            $receivePayment['localAmount'] = $dpdetails2->toCompanyLocalCurrencyAmount;
                            $receivePayment['companyRptAmount'] = $dpdetails2->toCompanyRptCurrencyAmount;
                            $receivePayment['bankID'] = $dpdetails2->toBankID;
                            $receivePayment['bankAccount'] = $dpdetails2->toBankAccountID;
                            $receivePayment['bankCurrency'] = $dpdetails2->toBankCurrencyID;
                            $receivePayment['bankCurrencyER'] = 1;
                        }

                        $receivePayment['documentType'] = 14;
                        $receivePayment['createdUserSystemID'] = $pvMaster->confirmedByEmpSystemID;
                        $receivePayment['createdUserID'] = $pvMaster->confirmedByEmpID;
                        $receivePayment['createdPcID'] = gethostname();


                        $custRecMaster = $crp->create($receivePayment);

                        if ($custRecMaster) {
                            foreach ($dpdetails as $val) {
                                $chartofAccount = ChartOfAccount::where('interCompanySystemID', $pvMaster->companySystemID)->first();
                                $receivePaymentDetail['directReceiptAutoID'] = $custRecMaster->custReceivePaymentAutoID;
                                $receivePaymentDetail['companySystemID'] = $pvMaster->interCompanyToSystemID;
                                $receivePaymentDetail['companyID'] = $company->CompanyID;

                                $serviceLine = SegmentMaster::ofCompany([$pvMaster->interCompanyToSystemID])->isPublic()->first();
                                if ($serviceLine) {
                                    $receivePaymentDetail['serviceLineSystemID'] = $serviceLine->serviceLineSystemID;
                                    $receivePaymentDetail['serviceLineCode'] = $serviceLine->ServiceLineCode;
                                }

                                $receivePaymentDetail['chartOfAccountSystemID'] = $chartofAccount->chartOfAccountSystemID;
                                $receivePaymentDetail['glCode'] = $chartofAccount->AccountCode;
                                $receivePaymentDetail['glCodeDes'] = $chartofAccount->AccountDescription;
                                $receivePaymentDetail['contractID'] = null;
                                $receivePaymentDetail['comments'] = $pvMaster->BPVNarration;
                                $receivePaymentDetail['DRAmountCurrency'] = $val->toBankCurrencyID;
                                $receivePaymentDetail['DDRAmountCurrencyER'] = 1;
                                $receivePaymentDetail['DRAmount'] = $val->toBankAmount;
                                $receivePaymentDetail['localCurrency'] = $val->toCompanyLocalCurrencyID;
                                $receivePaymentDetail['localCurrencyER'] = $val->toCompanyLocalCurrencyER;
                                $receivePaymentDetail['localAmount'] = $val->toCompanyLocalCurrencyAmount;
                                $receivePaymentDetail['comRptCurrency'] = $val->toCompanyRptCurrencyID;
                                $receivePaymentDetail['comRptCurrencyER'] = $val->toCompanyRptCurrencyER;
                                $receivePaymentDetail['comRptAmount'] = $val->toCompanyRptCurrencyAmount;
                                $custRecMaster = $ddr->create($receivePaymentDetail);
                            }

                            $params = array('autoID' => $custRecMaster->custReceivePaymentAutoID, 'company' => $pvMaster->interCompanyToSystemID, 'document' => 21, 'segment' => '', 'category' => '', 'amount' => 0);
                            $confirm = \Helper::confirmDocument($params);
                        }
                    } else {
                        $dpdetails = $dpdetail->findWhere(['directPaymentAutoID' => $pvMaster->PayMasterAutoId, 'glCodeIsBank' => 1]);
                        if (count($dpdetails) > 0) {
                            foreach ($dpdetails as $val) {
                                $receivePayment['companySystemID'] = $pvMaster->companySystemID;
                                $receivePayment['companyID'] = $pvMaster->companyID;
                                $receivePayment['documentSystemID'] = $pvMaster->documentSystemID;
                                $receivePayment['documentID'] = $pvMaster->documentID;

                                $companyFinanceYear = CompanyFinanceYear::where('companySystemID', $pvMaster->companySystemID)->whereRaw('YEAR(bigginingDate) = ?', [date('Y')])->first();

                                $receivePayment['companyFinanceYearID'] = $companyFinanceYear->companyFinanceYearID;
                                $receivePayment['FYBiggin'] = $companyFinanceYear->bigginingDate;
                                $receivePayment['FYEnd'] = $companyFinanceYear->endingDate;

                                $companyFinancePeriod = CompanyFinancePeriod::where('companySystemID', $pvMaster->companySystemID)->where('departmentSystemID', 4)->where('companyFinanceYearID', $companyFinanceYear->companyFinanceYearID)->whereRaw('DATE_FORMAT(dateFrom,"%Y-%m") = ?', [date('Y-m')])->first();
                                if($companyFinancePeriod) {
                                    $receivePayment['FYPeriodDateFrom'] = $companyFinancePeriod->dateFrom;
                                    $receivePayment['FYPeriodDateTo'] = $companyFinancePeriod->dateTo;
                                }

                                $receivePayment['PayMasterAutoId'] = $pvMaster->PayMasterAutoId;
                                $receivePayment['serialNo'] = $pvMaster->serialNo;
                                $receivePayment['custPaymentReceiveCode'] = $pvMaster->BPVcode;
                                $receivePayment['custPaymentReceiveDate'] = $pvMaster->BPVdate;
                                $receivePayment['narration'] = $pvMaster->BPVNarration;

                                $receivePayment['custTransactionCurrencyID'] = $val->bankCurrencyID;
                                $receivePayment['custTransactionCurrencyER'] = 1;

                                $account = BankAccount::where('chartOfAccountSystemID', $val->chartOfAccountSystemID)->where('companySystemID', $pvMaster->companySystemID)->first();

                                $receivePayment['bankID'] = $account->bankmasterAutoID;
                                $receivePayment['bankAccount'] = $account->bankAccountAutoID;
                                $receivePayment['bankCurrency'] = $val->bankCurrencyID;
                                $receivePayment['bankCurrencyER'] = 1;

                                $companyCurrencyConversion = \Helper::currencyConversion($pvMaster->companySystemID, $val->bankCurrencyID,$val->bankCurrencyID, $val->bankAmount);

                                $receivePayment['localCurrencyID'] = $val->localCurrency;
                                $receivePayment['localCurrencyER'] = $companyCurrencyConversion['trasToLocER'];
                                $receivePayment['companyRptCurrencyID'] = $val->comRptCurrency;
                                $receivePayment['companyRptCurrencyER'] = $companyCurrencyConversion['trasToRptER'];
                                $receivePayment['bankAmount'] = $val->bankAmount;
                                $receivePayment['localAmount'] = \Helper::roundValue($companyCurrencyConversion['localAmount']);
                                $receivePayment['companyRptAmount'] = \Helper::roundValue($companyCurrencyConversion['reportingAmount']);
                                $receivePayment['receivedAmount'] = $val->bankAmount;

                                $receivePayment['confirmedYN'] = 1;
                                $receivePayment['confirmedByEmpSystemID'] = $pvMaster->confirmedByEmpSystemID;
                                $receivePayment['confirmedByEmpID'] = $pvMaster->confirmedByEmpID;;
                                $receivePayment['confirmedByName'] = $pvMaster->confirmedByName;;
                                $receivePayment['confirmedDate'] = NOW();
                                $receivePayment['approved'] = -1;
                                $receivePayment['approvedDate'] = NOW();
                                $receivePayment['postedDate'] = NOW();
                                $receivePayment['createdUserSystemID'] = $pvMaster->confirmedByEmpSystemID;
                                $receivePayment['createdUserID'] = $pvMaster->confirmedByEmpID;
                                $receivePayment['createdPcID'] = gethostname();

                                $custRecMaster = $crp->create($receivePayment);
                            }
                        }
                    }
                    $masterData = ['documentSystemID' => $pvMaster->documentSystemID, 'autoID' => $pvMaster->PayMasterAutoId, 'companySystemID' => $pvMaster->companySystemID, 'employeeSystemID' => $pvMaster->confirmedByEmpSystemID];
                    //$jobPV = BankLedgerInsert::dispatch($masterData);
                }

                DB::commit();
            } catch (\Exception $e) {
                DB::rollback();
                Log::error($e->getMessage());
            }
        }
    }
}
