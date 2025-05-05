<?php

namespace App\Services;

use App\helper\TaxService;
use App\Models\GRVDetails;
use App\Models\PoAdvancePayment;
use App\Models\PurchaseReturnLogistic;
use App\Models\UnbilledGrvGroupBy;
use App\Models\Company;
use App\Models\GRVMaster;
use App\Models\SupplierAssigned;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\GeneralLedger\GlPostedDateService;

class UnbilledGRVService
{
	public static function postLedgerEntry($masterModel)
	{
        $company = Company::where('companySystemID', $masterModel['companySystemID'])->first();
        $supplierAssignedDetail = SupplierAssigned::where('supplierCodeSytem', $masterModel['supplierID'])
                                                    ->where('companySystemID', $masterModel['companySystemID'])
                                                    ->first();

        $validatePostedDate = GlPostedDateService::validatePostedDate($masterModel["autoID"], $masterModel["documentSystemID"]);

        if (!$validatePostedDate['status']) {
            return ['status' => false, 'message' => $validatePostedDate['message']];
        }

        $postedDateGl = $validatePostedDate['postedDate'];

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

                foreach ($output as $key => $value) {
                        $res = TaxService::processGRVVATForUnbilled($value);

                        $value->totTransactionAmount = $res['totalTransAmount'];
                        $value->totRptAmount = $res['totalRptAmount'];
                        $value->totLocalAmount = $res['totalLocalAmount'];

                        $value->totalVATAmount = $res['totalTransVATAmount'];
                        $value->totalVATAmountLocal = $res['totalLocalVATAmount'];
                        $value->totalVATAmountRpt = $res['totalRptVATAmount'];
                        $value->grvDate = $postedDateGl;
                }

            } else {
                $output = GRVDetails::selectRaw("erp_grvmaster.companySystemID,erp_grvmaster.companyID,erp_grvmaster.supplierID,purchaseOrderMastertID as purchaseOrderID,erp_grvdetails.grvAutoID,NOW() as grvDate,supplierItemCurrencyID as supplierTransactionCurrencyID,foreignToLocalER as supplierTransactionCurrencyER,erp_grvdetails.companyReportingCurrencyID,erp_grvdetails.companyReportingER,erp_grvdetails.localCurrencyID,erp_grvdetails.localCurrencyER,ROUND(SUM(GRVcostPerUnitSupTransCur*noQty),7) as totTransactionAmount,ROUND(SUM(GRVcostPerUnitLocalCur*noQty),7) as totLocalAmount, ROUND(SUM(GRVcostPerUnitComRptCur*noQty),7) as totRptAmount,ROUND(SUM(VATAmount*noQty),7) as totalVATAmount,ROUND(SUM(VATAmountLocal*noQty),7) as totalVATAmountLocal,ROUND(SUM(VATAmountRpt*noQty),7) as totalVATAmountRpt,'POG' as grvType,NOW() as timeStamp")
                    ->leftJoin('erp_grvmaster', 'erp_grvdetails.grvAutoID', '=', 'erp_grvmaster.grvAutoID')
                    ->WHERE('erp_grvdetails.grvAutoID', $masterModel["autoID"])
                    ->groupBy('purchaseOrderMastertID')
                    ->get();

                foreach ($output as $key => $value) {
                        $res = TaxService::processGRVVATForUnbilled($value);

                        $value->grvDate = $postedDateGl;
                        $value->totTransactionAmount = $value->totTransactionAmount - $res['exemptVATTrans'];
                        $value->totRptAmount = $value->totRptAmount - $res['exemptVATRpt'];
                        $value->totLocalAmount = $value->totLocalAmount - $res['exemptVATLocal'];

                        $value->totalVATAmount = $res['totalTransVATAmount'] + $res['exemptVATTrans'];
                        $value->totalVATAmountLocal = $res['totalLocalVATAmount'] + $res['exemptVATLocal'];
                        $value->totalVATAmountRpt = $res['totalRptVATAmount'] + $res['exemptVATRpt'];
                }
            }

            if ($output) {
                $unbillRes = UnbilledGrvGroupBy::insert($output->toArray());

                $lastUnbilledGrvGroupBy = UnbilledGrvGroupBy::orderBy('unbilledgrvAutoID', 'DESC')->first();
                $output = PoAdvancePayment::selectRaw("erp_grvmaster.companySystemID,erp_grvmaster.companyID,erp_purchaseorderadvpayment.supplierID,poID as purchaseOrderID,erp_purchaseorderadvpayment.grvAutoID,NOW() as grvDate,erp_purchaseorderadvpayment.currencyID as supplierTransactionCurrencyID,'1' as supplierTransactionCurrencyER,erp_purchaseordermaster.companyReportingCurrencyID, ROUND((SUM(reqAmountTransCur_amount)/SUM(reqAmountInPORptCur)),7) as companyReportingER,erp_purchaseordermaster.localCurrencyID,ROUND((SUM(reqAmountTransCur_amount)/SUM(reqAmountInPOLocalCur)),7) as localCurrencyER,ROUND(SUM(reqAmountTransCur_amount),7) as totTransactionAmount,ROUND(SUM(reqAmountInPOLocalCur),7) as totLocalAmount, ROUND(SUM(reqAmountInPORptCur),7) as totRptAmount,'POG' as grvType,NOW() as timeStamp, ROUND(SUM(erp_purchaseorderadvpayment.VATAmount),7) as totalVATAmount, ROUND(SUM(erp_purchaseorderadvpayment.VATAmountLocal),7) as totalVATAmountLocal, ROUND(SUM(erp_purchaseorderadvpayment.VATAmountRpt),7) as totalVATAmountRpt, 1 as logisticYN")
                                            ->leftJoin('erp_grvmaster', 'erp_purchaseorderadvpayment.grvAutoID', '=', 'erp_grvmaster.grvAutoID')
                                            ->leftJoin('erp_purchaseordermaster', 'erp_purchaseorderadvpayment.poID', '=', 'erp_purchaseordermaster.purchaseOrderID')
                                            ->where('erp_purchaseorderadvpayment.grvAutoID',$masterModel["autoID"])
                                            ->groupBy('erp_purchaseorderadvpayment.UnbilledGRVAccountSystemID','erp_purchaseorderadvpayment.supplierID','erp_purchaseorderadvpayment.currencyID')
                                            ->get();
                if($output){
                    foreach ($output as $key => $value) {
                        $vatData = TaxService::poLogisticVATDistributionForGRV($masterModel["autoID"],0,$value->supplierID);

                        $value->grvDate = $postedDateGl;

                        $value->totTransactionAmount = $value->totTransactionAmount + $value->totalVATAmount;
                        $value->totRptAmount = $value->totRptAmount + $value->totalVATAmountRpt;
                        $value->totLocalAmount = $value->totLocalAmount + $value->totalVATAmountLocal;

                        $value->totalVATAmount = $vatData['vatOnPOTotalAmountTrans'];
                        $value->totalVATAmountLocal = $vatData['vatOnPOTotalAmountLocal'];
                        $value->totalVATAmountRpt = $vatData['vatOnPOTotalAmountRpt'];
                    }

                    $unbillRes1 = UnbilledGrvGroupBy::insert($output->toArray());
                }

                return ['status' => true];
            }else{
                return ['status' => false, 'error' => ['message' => 'No records found in unbilled grv table']];
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

                $grvMaster = GRVMaster::find($masterModel["autoID"]);

                if ($grvMaster && $grvMaster->grvTypeID != 1) {
                    foreach ($output as $key => $value) {
                            $res = TaxService::processPRNVATForUnbilled($masterModel["autoID"], $masterModel["purhaseReturnAutoID"]);

                            $value->totTransactionAmount = $res['totalTransAmount'];
                            $value->totRptAmount = $res['totalRptAmount'];
                            $value->totLocalAmount = $res['totalLocalAmount'];

                            $value->grvDate = $postedDateGl;
                            $value->totalVATAmount = $res['totalTransVATAmount'];
                            $value->totalVATAmountLocal = $res['totalLocalVATAmount'];
                            $value->totalVATAmountRpt = $res['totalRptVATAmount'];
                    }
                } 


            } else {
                $output = GRVDetails::selectRaw("erp_grvmaster.companySystemID,erp_grvmaster.companyID,erp_grvmaster.supplierID,purchaseOrderMastertID as purchaseOrderID,erp_grvdetails.grvAutoID,NOW() as grvDate,supplierItemCurrencyID as supplierTransactionCurrencyID,foreignToLocalER as supplierTransactionCurrencyER,erp_grvdetails.companyReportingCurrencyID,erp_grvdetails.companyReportingER,erp_grvdetails.localCurrencyID,erp_grvdetails.localCurrencyER,ROUND(SUM(erp_grvdetails.GRVcostPerUnitSupTransCur*erp_purchasereturndetails.noQty),7) as totTransactionAmount,ROUND(SUM(erp_grvdetails.GRVcostPerUnitLocalCur*erp_purchasereturndetails.noQty),7) as totLocalAmount, ROUND(SUM(erp_grvdetails.GRVcostPerUnitComRptCur*erp_purchasereturndetails.noQty),7) as totRptAmount,ROUND(SUM(erp_grvdetails.VATAmount*erp_purchasereturndetails.noQty),7) as totalVATAmount,ROUND(SUM(erp_grvdetails.VATAmountLocal*erp_purchasereturndetails.noQty),7) as totalVATAmountLocal,ROUND(SUM(erp_grvdetails.VATAmountRpt*erp_purchasereturndetails.noQty),7) as totalVATAmountRpt,'POG' as grvType,NOW() as timeStamp, erp_purchasereturndetails.purhaseReturnAutoID as purhaseReturnAutoID")
                    ->leftJoin('erp_grvmaster', 'erp_grvdetails.grvAutoID', '=', 'erp_grvmaster.grvAutoID')
                    ->join('erp_purchasereturndetails', 'erp_grvdetails.grvDetailsID', '=', 'erp_purchasereturndetails.grvDetailsID')
                    ->WHERE('erp_grvdetails.grvAutoID', $masterModel["autoID"])
                    ->WHERE('erp_purchasereturndetails.purhaseReturnAutoID', $masterModel["purhaseReturnAutoID"])
                    ->groupBy('purchaseOrderMastertID')
                    ->get();

                 foreach ($output as $key => $value) {
                        $res = TaxService::processPRNVATForUnbilled($masterModel["autoID"], $masterModel["purhaseReturnAutoID"]);

                        $value->totTransactionAmount = $value->totTransactionAmount - $res['exemptVATTrans'];
                        $value->totRptAmount = $value->totRptAmount - $res['exemptVATRpt'];
                        $value->totLocalAmount = $value->totLocalAmount - $res['exemptVATLocal'];

                        $value->totalVATAmount = $res['totalTransVATAmount'];
                        $value->totalVATAmountLocal = $res['totalLocalVATAmount'];
                        $value->totalVATAmountRpt = $res['totalRptVATAmount'];
                        $value->grvDate = $postedDateGl;
                }
            }

            if ($output) {
                $unbillRes = UnbilledGrvGroupBy::insert($output->toArray());

                $outputLogistic = PurchaseReturnLogistic::selectRaw("erp_grvmaster.companySystemID,
                                                      erp_grvmaster.companyID,
                                                      purchase_return_logistic.supplierID,
                                                      erp_purchaseorderadvpayment.poID as purchaseOrderID,
                                                      purchase_return_logistic.grvAutoID,
                                                      purchase_return_logistic.purchaseReturnID as purhaseReturnAutoID,
                                                      NOW() as grvDate,
                                                      purchase_return_logistic.supplierTransactionCurrencyID as supplierTransactionCurrencyID,
                                                      '1' as supplierTransactionCurrencyER,
                                                      erp_purchaseordermaster.companyReportingCurrencyID,
                                                      ROUND((SUM(reqAmountTransCur_amount)/SUM(reqAmountInPORptCur)),7) as companyReportingER,
                                                      erp_purchaseordermaster.localCurrencyID,
                                                      ROUND((SUM(reqAmountTransCur_amount)/SUM(reqAmountInPOLocalCur)),7) as localCurrencyER,
                                                      ROUND(SUM(logisticAmountTrans + purchase_return_logistic.logisticVATAmount),7) as totTransactionAmount,
                                                      ROUND(SUM(logisticAmountLocal + purchase_return_logistic.logisticVATAmountLocal),7) as totLocalAmount, 
                                                      ROUND(SUM(logisticAmountRpt + purchase_return_logistic.logisticVATAmountRpt),7) as totRptAmount,
                                                      'POG' as grvType,NOW() as timeStamp, 
                                                      ROUND(SUM(purchase_return_logistic.logisticVATAmount),7) as totalVATAmount, 
                                                      ROUND(SUM(purchase_return_logistic.logisticVATAmountLocal),7) as totalVATAmountLocal,
                                                      ROUND(SUM(purchase_return_logistic.logisticVATAmountRpt),7) as totalVATAmountRpt,
                                                      1 as logisticYN")
                                            ->leftJoin('erp_grvmaster', 'purchase_return_logistic.grvAutoID', '=', 'erp_grvmaster.grvAutoID')
                                            ->leftJoin('erp_purchaseorderadvpayment', 'purchase_return_logistic.poAdvPaymentID', '=', 'erp_purchaseorderadvpayment.poAdvPaymentID')
                                            ->leftJoin('erp_purchaseordermaster', 'erp_purchaseorderadvpayment.poID', '=', 'erp_purchaseordermaster.purchaseOrderID')
                                            ->where('purchase_return_logistic.purchaseReturnID',$masterModel["purhaseReturnAutoID"])
                                            ->groupBy('purchase_return_logistic.UnbilledGRVAccountSystemID','purchase_return_logistic.supplierID')
                                            ->get();

                if($outputLogistic){
                    foreach ($outputLogistic as $key => $value) {
                        $value->grvDate = $postedDateGl;
                    }
                    
                    $unbillRes1 = UnbilledGrvGroupBy::insert($outputLogistic->toArray());
                }

                return ['status' => true];
            }else{
                return ['status' => false, 'error' => ['message' => 'No records found in unbilled grv table']];
            }
        }
	}
}
