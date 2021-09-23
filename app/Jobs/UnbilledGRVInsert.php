<?php

namespace App\Jobs;

use App\helper\TaxService;
use App\Models\GRVDetails;
use App\Models\PoAdvancePayment;
use App\Models\UnbilledGrvGroupBy;
use App\Models\Company;
use App\Models\SupplierAssigned;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UnbilledGRVInsert implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $masterModel;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($masterModel)
    {
        //
        $this->masterModel = $masterModel;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::useFiles(storage_path().'/logs/unbilled_grv_jobs.log');
        $masterModel = $this->masterModel;
        if (!empty($masterModel)) {
            if (!isset($masterModel['documentSystemID'])) {
                Log::warning('Parameter document id is missing' . date('H:i:s'));
            }
            DB::beginTransaction();
            try {
                $company = Company::where('companySystemID', $masterModel['companySystemID'])->first();
                $supplierAssignedDetail = SupplierAssigned::where('supplierCodeSytem', $masterModel['supplierID'])
                                                            ->where('companySystemID', $masterModel['companySystemID'])
                                                            ->first();
                $valEligible = false;
                if ($company->vatRegisteredYN == 1 || $supplierAssignedDetail->vatEligible == 1) {
                    $valEligible = true;
                }

                if ($masterModel['documentSystemID'] == 3) {

                    $rcmActivated = TaxService::isGRVRCMActivation($masterModel["autoID"]);

                    if ($valEligible && !$rcmActivated) {
                        $output = GRVDetails::selectRaw("erp_grvmaster.companySystemID,erp_grvmaster.companyID,erp_grvmaster.supplierID,purchaseOrderMastertID as purchaseOrderID,erp_grvdetails.grvAutoID,NOW() as grvDate,supplierItemCurrencyID as supplierTransactionCurrencyID,foreignToLocalER as supplierTransactionCurrencyER,erp_grvdetails.companyReportingCurrencyID,erp_grvdetails.companyReportingER,erp_grvdetails.localCurrencyID,erp_grvdetails.localCurrencyER,ROUND(SUM((GRVcostPerUnitSupTransCur*noQty) + (VATAmount*noQty)),7) as totTransactionAmount,ROUND(SUM((GRVcostPerUnitLocalCur*noQty) + (VATAmountLocal*noQty)),7) as totLocalAmount, ROUND(SUM((GRVcostPerUnitComRptCur*noQty) + (VATAmountRpt*noQty)),7) as totRptAmount,ROUND(SUM(VATAmount*noQty),7) as totalVATAmount,ROUND(SUM(VATAmountLocal*noQty),7) as totalVATAmountLocal,ROUND(SUM(VATAmountRpt*noQty),7) as totalVATAmountRpt,'POG' as grvType,NOW() as timeStamp")
                            ->leftJoin('erp_grvmaster', 'erp_grvdetails.grvAutoID', '=', 'erp_grvmaster.grvAutoID')
                            ->WHERE('erp_grvdetails.grvAutoID', $masterModel["autoID"])
                            ->groupBy('purchaseOrderMastertID')
                            ->get();
                    } else {
                        $output = GRVDetails::selectRaw("erp_grvmaster.companySystemID,erp_grvmaster.companyID,erp_grvmaster.supplierID,purchaseOrderMastertID as purchaseOrderID,erp_grvdetails.grvAutoID,NOW() as grvDate,supplierItemCurrencyID as supplierTransactionCurrencyID,foreignToLocalER as supplierTransactionCurrencyER,erp_grvdetails.companyReportingCurrencyID,erp_grvdetails.companyReportingER,erp_grvdetails.localCurrencyID,erp_grvdetails.localCurrencyER,ROUND(SUM(GRVcostPerUnitSupTransCur*noQty),7) as totTransactionAmount,ROUND(SUM(GRVcostPerUnitLocalCur*noQty),7) as totLocalAmount, ROUND(SUM(GRVcostPerUnitComRptCur*noQty),7) as totRptAmount,ROUND(SUM(VATAmount*noQty),7) as totalVATAmount,ROUND(SUM(VATAmountLocal*noQty),7) as totalVATAmountLocal,ROUND(SUM(VATAmountRpt*noQty),7) as totalVATAmountRpt,'POG' as grvType,NOW() as timeStamp")
                            ->leftJoin('erp_grvmaster', 'erp_grvdetails.grvAutoID', '=', 'erp_grvmaster.grvAutoID')
                            ->WHERE('erp_grvdetails.grvAutoID', $masterModel["autoID"])
                            ->groupBy('purchaseOrderMastertID')
                            ->get();
                    }

                    if ($output) {
                        $unbillRes = UnbilledGrvGroupBy::insert($output->toArray());

                        $output = PoAdvancePayment::selectRaw("erp_grvmaster.companySystemID,erp_grvmaster.companyID,erp_purchaseorderadvpayment.supplierID,poID as purchaseOrderID,erp_purchaseorderadvpayment.grvAutoID,NOW() as grvDate,erp_purchaseorderadvpayment.currencyID as supplierTransactionCurrencyID,'1' as supplierTransactionCurrencyER,erp_purchaseordermaster.companyReportingCurrencyID, ROUND((SUM(reqAmountTransCur_amount)/SUM(reqAmountInPORptCur)),7) as companyReportingER,erp_purchaseordermaster.localCurrencyID,ROUND((SUM(reqAmountTransCur_amount)/SUM(reqAmountInPOLocalCur)),7) as localCurrencyER,ROUND(SUM(reqAmountTransCur_amount + erp_purchaseorderadvpayment.VATAmount),7) as totTransactionAmount,ROUND(SUM(reqAmountInPOLocalCur + erp_purchaseorderadvpayment.VATAmountLocal),7) as totLocalAmount, ROUND(SUM(reqAmountInPORptCur + erp_purchaseorderadvpayment.VATAmountRpt),7) as totRptAmount,'POG' as grvType,NOW() as timeStamp, ROUND(SUM(erp_purchaseorderadvpayment.VATAmount),7) as totalVATAmount, ROUND(SUM(erp_purchaseorderadvpayment.VATAmountLocal),7) as totalVATAmountLocal, ROUND(SUM(erp_purchaseorderadvpayment.VATAmountRpt),7) as totalVATAmountRpt")
                                                    ->leftJoin('erp_grvmaster', 'erp_purchaseorderadvpayment.grvAutoID', '=', 'erp_grvmaster.grvAutoID')
                                                    ->leftJoin('erp_purchaseordermaster', 'erp_purchaseorderadvpayment.poID', '=', 'erp_purchaseordermaster.purchaseOrderID')
                                                    ->where('erp_purchaseorderadvpayment.grvAutoID',$masterModel["autoID"])
                                                    ->groupBy('erp_purchaseorderadvpayment.UnbilledGRVAccountSystemID','erp_purchaseorderadvpayment.supplierID')
                                                    ->get();
                        if($output){
                            $unbillRes1 = UnbilledGrvGroupBy::insert($output->toArray());
                        }
                        DB::commit();
                        Log::info('Successfully updated to unbilled grv table' . date('H:i:s'));
                    }else{
                        DB::rollback();
                        Log::info('No records found in unbilled grv table' . date('H:i:s'));
                    }
                } else if ($masterModel['documentSystemID'] == 24) {
                    if ($valEligible) {
                        $output = GRVDetails::selectRaw("erp_grvmaster.companySystemID,erp_grvmaster.companyID,erp_grvmaster.supplierID,purchaseOrderMastertID as purchaseOrderID,erp_grvdetails.grvAutoID,NOW() as grvDate,supplierItemCurrencyID as supplierTransactionCurrencyID,foreignToLocalER as supplierTransactionCurrencyER,erp_grvdetails.companyReportingCurrencyID,erp_grvdetails.companyReportingER,erp_grvdetails.localCurrencyID,erp_grvdetails.localCurrencyER,ROUND(SUM((erp_grvdetails.GRVcostPerUnitSupTransCur*erp_purchasereturndetails.noQty) + (erp_grvdetails.VATAmount*erp_purchasereturndetails.noQty)),7) as totTransactionAmount,ROUND(SUM((erp_grvdetails.GRVcostPerUnitLocalCur*erp_purchasereturndetails.noQty) + (erp_grvdetails.VATAmountLocal*erp_purchasereturndetails.noQty)),7) as totLocalAmount, ROUND(SUM((erp_grvdetails.GRVcostPerUnitComRptCur*erp_purchasereturndetails.noQty) + (erp_grvdetails.VATAmountRpt*erp_purchasereturndetails.noQty)),7) as totRptAmount,ROUND(SUM(erp_grvdetails.VATAmount*erp_purchasereturndetails.noQty),7) as totalVATAmount,ROUND(SUM(erp_grvdetails.VATAmountLocal*erp_purchasereturndetails.noQty),7) as totalVATAmountLocal,ROUND(SUM(erp_grvdetails.VATAmountRpt*erp_purchasereturndetails.noQty),7) as totalVATAmountRpt,'POG' as grvType,NOW() as timeStamp, erp_purchasereturndetails.purhaseReturnAutoID as purhaseReturnAutoID")
                            ->leftJoin('erp_grvmaster', 'erp_grvdetails.grvAutoID', '=', 'erp_grvmaster.grvAutoID')
                            ->join('erp_purchasereturndetails', 'erp_grvdetails.grvDetailsID', '=', 'erp_purchasereturndetails.grvDetailsID')
                            ->WHERE('erp_grvdetails.grvAutoID', $masterModel["autoID"])
                            ->WHERE('erp_purchasereturndetails.purhaseReturnAutoID', $masterModel["purhaseReturnAutoID"])
                            ->groupBy('purchaseOrderMastertID')
                            ->get();
                    } else {
                        $output = GRVDetails::selectRaw("erp_grvmaster.companySystemID,erp_grvmaster.companyID,erp_grvmaster.supplierID,purchaseOrderMastertID as purchaseOrderID,erp_grvdetails.grvAutoID,NOW() as grvDate,supplierItemCurrencyID as supplierTransactionCurrencyID,foreignToLocalER as supplierTransactionCurrencyER,erp_grvdetails.companyReportingCurrencyID,erp_grvdetails.companyReportingER,erp_grvdetails.localCurrencyID,erp_grvdetails.localCurrencyER,ROUND(SUM(erp_grvdetails.GRVcostPerUnitSupTransCur*erp_purchasereturndetails.noQty),7) as totTransactionAmount,ROUND(SUM(erp_grvdetails.GRVcostPerUnitLocalCur*erp_purchasereturndetails.noQty),7) as totLocalAmount, ROUND(SUM(erp_grvdetails.GRVcostPerUnitComRptCur*erp_purchasereturndetails.noQty),7) as totRptAmount,ROUND(SUM(erp_grvdetails.VATAmount*erp_purchasereturndetails.noQty),7) as totalVATAmount,ROUND(SUM(erp_grvdetails.VATAmountLocal*erp_purchasereturndetails.noQty),7) as totalVATAmountLocal,ROUND(SUM(erp_grvdetails.VATAmountRpt*erp_purchasereturndetails.noQty),7) as totalVATAmountRpt,'POG' as grvType,NOW() as timeStamp, erp_purchasereturndetails.purhaseReturnAutoID as purhaseReturnAutoID")
                            ->leftJoin('erp_grvmaster', 'erp_grvdetails.grvAutoID', '=', 'erp_grvmaster.grvAutoID')
                            ->join('erp_purchasereturndetails', 'erp_grvdetails.grvDetailsID', '=', 'erp_purchasereturndetails.grvDetailsID')
                            ->WHERE('erp_grvdetails.grvAutoID', $masterModel["autoID"])
                            ->WHERE('erp_purchasereturndetails.purhaseReturnAutoID', $masterModel["purhaseReturnAutoID"])
                            ->groupBy('purchaseOrderMastertID')
                            ->get();
                    }

                    if ($output) {
                        $unbillRes = UnbilledGrvGroupBy::insert($output->toArray());

                        DB::commit();
                        Log::info('Successfully updated to unbilled grv table' . date('H:i:s'));
                    }else{
                        DB::rollback();
                        Log::info('No records found in unbilled grv table' . date('H:i:s'));
                    }
                }
            } catch (\Exception $e) {
                DB::rollback();
                Log::info($e->getMessage());
                Log::error('Error occurred when updating to unbilled grv table' . date('H:i:s'));
            }
        }
    }
}
