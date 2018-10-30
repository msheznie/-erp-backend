<?php

namespace App\Jobs;

use App\Models\BankAccount;
use App\Models\ChartOfAccount;
use App\Models\Company;
use App\Models\CompanyFinancePeriod;
use App\Models\CompanyFinanceYear;
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

                        $companyFinanceYear = CompanyFinanceYear::where('companySystemID', $pvMaster->companySystemID)->whereRaw('YEAR(bigginingDate) = ?', [date('Y')])->first();

                        $receivePayment['companyFinanceYearID'] = $companyFinanceYear->companyFinanceYearID;
                        $receivePayment['FYBiggin'] = $companyFinanceYear->bigginingDate;
                        $receivePayment['FYEnd'] = $companyFinanceYear->endingDate;

                        $companyFinancePeriod = CompanyFinancePeriod::where('companySystemID', $pvMaster->companySystemID)->where('departmentSystemID', 4)->where('companyFinanceYearID', $companyFinanceYear->companyFinanceYearID)->whereRaw('DATE_FORMAT(dateFrom,"%Y-%m") = ?', [date('Y-m')])->first();
                        $receivePayment['FYPeriodDateFrom'] = $companyFinancePeriod->dateFrom;
                        $receivePayment['FYPeriodDateTo'] = $companyFinancePeriod->dateTo;

                        $receivePayment['PayMasterAutoId'] = $pvMaster->PayMasterAutoId;
                        $receivePayment['serialNo'] = $pvMaster->serialNo;
                        $receivePayment['custPaymentReceiveCode'] = $pvMaster->BPVcode;
                        $receivePayment['custPaymentReceiveDate'] = $pvMaster->BPVdate;
                        $receivePayment['narration'] = $pvMaster->BPVNarration;

                        $receivePayment['bankID'] = $pvMaster->BPVbank;
                        $receivePayment['bankAccount'] = $pvMaster->BPVAccount;
                        $receivePayment['bankCurrency'] = $pvMaster->BPVbankCurrency;
                        $receivePayment['bankCurrencyER'] = $pvMaster->BPVbankCurrencyER;

                        $receivePayment['localCurrencyID'] = $pvMaster->localCurrencyID;
                        $receivePayment['localCurrencyER'] = $pvMaster->localCurrencyER;

                        $receivePayment['companyRptCurrencyID'] = $pvMaster->companyRptCurrencyID;
                        $receivePayment['companyRptCurrencyER'] = $pvMaster->companyRptCurrencyER;

                        $receivePayment['bankAmount'] = $pvMaster->payAmountBank;
                        $receivePayment['localAmount'] = $pvMaster->payAmountCompLocal;
                        $receivePayment['companyRptAmount'] = $pvMaster->payAmountCompRpt;

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

                        Log::info($receivePayment);

                        $custRecMaster = $crp->create($receivePayment);

                        if ($custRecMaster) {
                            foreach ($dpdetails as $val) {
                                $chartofAccount = ChartOfAccount::where('interCompanySystemID', $pvMaster->companySystemID)->first();
                                $receivePaymentDetail['directReceiptAutoID'] = $custRecMaster->custReceivePaymentAutoID;
                                $receivePaymentDetail['companySystemID'] = $pvMaster->interCompanyToSystemID;
                                $receivePaymentDetail['companyID'] = $company->CompanyID;
                                $receivePaymentDetail['serviceLineSystemID'] = $val->serviceLineSystemID;
                                $receivePaymentDetail['serviceLineCode'] = $val->serviceLineCode;

                                $receivePaymentDetail['chartOfAccountSystemID'] = $chartofAccount->chartOfAccountSystemID;
                                $receivePaymentDetail['glCode'] = $chartofAccount->AccountCode;
                                $receivePaymentDetail['glCodeDes'] = $chartofAccount->AccountDescription;
                                $receivePaymentDetail['contractID'] = null;
                                $receivePaymentDetail['comments'] = $pvMaster->BPVNarration;
                                $receivePaymentDetail['DRAmountCurrency'] = $val->toBankCurrencyID;
                                $receivePaymentDetail['DDRAmountCurrencyER'] = $val->toBankCurrencyER;
                                $receivePaymentDetail['DRAmount'] = $val->toBankAmount;
                                $receivePaymentDetail['localCurrency'] = $val->toCompanyLocalCurrencyID;
                                $receivePaymentDetail['localCurrencyER'] = $val->toCompanyLocalCurrencyER;
                                $receivePaymentDetail['localAmount'] = $val->toCompanyLocalCurrencyAmount;
                                $receivePaymentDetail['comRptCurrency'] = $val->toCompanyRptCurrencyID;
                                $receivePaymentDetail['comRptCurrencyER'] = $val->toCompanyRptCurrencyER;
                                $receivePaymentDetail['comRptAmount'] = $val->toCompanyRptCurrencyAmount;
                                Log::info($receivePaymentDetail);
                                $custRecMaster = $ddr->create($receivePaymentDetail);
                            }
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
                                $receivePayment['FYPeriodDateFrom'] = $companyFinancePeriod->dateFrom;
                                $receivePayment['FYPeriodDateTo'] = $companyFinancePeriod->dateTo;

                                $receivePayment['PayMasterAutoId'] = $pvMaster->PayMasterAutoId;
                                $receivePayment['serialNo'] = $pvMaster->serialNo;
                                $receivePayment['custPaymentReceiveCode'] = $pvMaster->BPVcode;
                                $receivePayment['custPaymentReceiveDate'] = $pvMaster->BPVdate;
                                $receivePayment['narration'] = $pvMaster->BPVNarration;

                                $account = BankAccount::where('chartOfAccountSystemID', $val->chartOfAccountSystemID)->where('companySystemID', $pvMaster->companySystemID)->first();

                                $receivePayment['bankID'] = $account->bankmasterAutoID;
                                $receivePayment['bankAccount'] = $account->bankAccountAutoID;
                                $receivePayment['bankCurrency'] = $val->bankCurrencyID;
                                $receivePayment['bankCurrencyER'] = $val->bankCurrencyER;

                                $receivePayment['localCurrencyID'] = $val->localCurrency;
                                $receivePayment['localCurrencyER'] = $val->localCurrencyER;

                                $receivePayment['companyRptCurrencyID'] = $val->comRptCurrency;
                                $receivePayment['companyRptCurrencyER'] = $val->comRptCurrencyER;

                                $receivePayment['bankAmount'] = $val->bankAmount;

                                if ($val->bankCurrencyID == $val->localCurrency) {
                                    $receivePayment['localAmount'] = $val->bankAmount;
                                } else {
                                    $trasToDefaultER = $pvMaster->companyRptCurrencyER;
                                    $amount = 0;
                                    if ($trasToDefaultER > $val->bankCurrencyER) {
                                        if ($trasToDefaultER > 1) {
                                            $amount = $val->bankAmount / $trasToDefaultER;
                                        } else {
                                            $amount = $val->bankAmount * $trasToDefaultER;
                                        }
                                    } else {
                                        If ($trasToDefaultER > 1) {
                                            $amount = $val->bankAmount * $trasToDefaultER;
                                        } else {
                                            $amount = $val->bankAmount / $trasToDefaultER;
                                        }
                                    }
                                    $receivePayment['localAmount'] = $amount;
                                }

                                if ($val->bankCurrencyID == $val->comRptCurrency) {
                                    $receivePayment['companyRptAmount'] = $val->comRptAmount;
                                } else {
                                    $trasToDefaultER = $pvMaster->localCurrencyER;
                                    $amount = 0;
                                    if ($trasToDefaultER > $val->bankCurrencyER) {
                                        if ($trasToDefaultER > 1) {
                                            $amount = $val->bankAmount / $trasToDefaultER;
                                        } else {
                                            $amount = $val->bankAmount * $trasToDefaultER;
                                        }
                                    } else {
                                        If ($trasToDefaultER > 1) {
                                            $amount = $val->bankAmount * $trasToDefaultER;
                                        } else {
                                            $amount = $val->bankAmount / $trasToDefaultER;
                                        }
                                    }
                                    $receivePayment['companyRptAmount'] = $amount;
                                }

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

                                Log::info($receivePayment);
                                $custRecMaster = $crp->create($receivePayment);
                            }
                        }
                    }
                }

                Log::info('Successfully inserted to Customer receive voucher ' . date('H:i:s'));
                DB::commit();
            } catch (\Exception $e) {
                DB::rollback();
                Log::error($e->getMessage());
            }
        }
    }
}
