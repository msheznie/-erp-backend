<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\AppBaseController;
use App\Models\Tenant;
use App\Models\TenantConfiguration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;

class ConfigurationAPIController extends AppBaseController
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */

    public function getConfigurationInfo(Request $request){

        $isLang = 0;
        $environment = 'Local';
        $version = $this->getVersion();
        if (env('IS_MULTI_TENANCY', false)) {


            $url = $request->getHttpHost();
            $url_array = explode('.', $url);

            $subDomain = $url_array[0];

            if ($subDomain == 'www') {
                $subDomain = $url_array[1];
            }

            if ($subDomain != 'localhost:8000') {
                if (!$subDomain) {
                    return $subDomain . "Not found";
                }

                $tenant = Tenant::where('sub_domain', 'like', $subDomain)->first();
                if($tenant){
                    $isLang = TenantConfiguration::orderBy('id', 'desc')->where('tenant_id', $tenant->id)->where('application_id', 0)->where('configuration_id', 3)->first();
                    if($isLang){
                        $isLang = $isLang->value;
                    }
                }

                $environment = TenantConfiguration::orderBy('id', 'desc')->where('configuration_id', 1)->where('application_id', 0)->first();
                if($environment){
                    $environment = $environment->value;
                }
            }
        }

        $configuration = array('environment' => $environment, 'isLang' => $isLang, 'version' => $version);

        return $this->sendResponse($configuration, 'Configurations retrieved successfully');

    }

    public function getVersion()
    {
        $packageJsonPath = base_path('package.json');

        if (File::exists($packageJsonPath)) {
            $packageJsonContent = File::get($packageJsonPath);

            $packageJsonData = json_decode($packageJsonContent, true);

            $versionNumber = $packageJsonData['version'];

            return $versionNumber;
        } else {
            return null;
        }
    }


    public function updateWrongEnrty()
    {
        $updateDataFinal = [];
        $wrongGlDaata = DB::table('erp_generalledger')
                          ->whereDate('createdDateTime', '>=', '2024-09-02')
                          ->whereColumn('documentTransCurrencyID', '!=', 'documentRptCurrencyID')
                          ->whereColumn('documentRptCurrencyER', '<=', 'documentTransCurrencyER')
                          ->where('documentRptCurrencyER', '>', 0)
                          ->get();

        $wrong = [];
        foreach ($wrongGlDaata as $key => $value) {
            $updateDataGl = [];
            if ($value->documentLocalCurrencyID == $value->documentRptCurrencyID) {
                $updateDataGl = ['documentRptAmount' => $value->documentLocalAmount];
            } else {
                $newRpt = $this->convertAmountToLocalRpt($value->documentTransAmount, $value->documentRptCurrencyER);

                if (round($newRpt, 7) != $value->documentRptAmount) {
                    $updateDataGl = ['documentRptAmount' => round($newRpt, 7)];
                } 
            }

            if (count($updateDataGl) > 0) {
                $updateDataFinal[] = ['value' => $value, 'updateDataGl' => $updateDataGl];
                // DB::table('erp_generalledger')->where('GeneralLedgerID', $value->GeneralLedgerID)->update($updateDataGl);
            }
        }

        $wrongARData = DB::table('erp_accountsreceivableledger')
                          ->whereDate('createdDateTime', '>=', '2024-09-02')
                          ->whereColumn('custTransCurrencyID', '!=', 'comRptCurrencyID')
                          ->whereColumn('comRptER', '<=', 'custTransER')
                          ->where('comRptER', '>', 0)
                          ->get();

        $wrong = [];
        foreach ($wrongARData as $key => $value) {
            $updateDataAR = [];
            if ($value->localCurrencyID == $value->comRptCurrencyID) {
                $updateDataAR = ['comRptAmount' => $value->localAmount];
            } else {
                $newRpt = $this->convertAmountToLocalRpt($value->custInvoiceAmount, $value->comRptER);

                if (round($newRpt, 7) != $value->comRptAmount) {
                    $updateDataAR = ['comRptAmount' => round($newRpt, 7)];
                } 
            }

            if (count($updateDataAR) > 0) {
                $updateDataFinal[] = ['value' => $value, 'updateDataAR' => $updateDataAR];
                // DB::table('erp_accountsreceivableledger')->where('arAutoID', $value->arAutoID)->update($updateDataAR);
            }
        }


        $wrongAPData = DB::table('erp_accountspayableledger')
                          ->whereDate('createdDateTime', '>=', '2024-09-02')
                          ->whereColumn('supplierTransCurrencyID', '!=', 'comRptCurrencyID')
                          ->whereColumn('comRptER', '<=', 'supplierTransER')
                          ->where('comRptER', '>', 0)
                          ->get();

        $wrong = [];
        foreach ($wrongAPData as $key => $value) {
            $updateDataAP = [];
            if ($value->localCurrencyID == $value->comRptCurrencyID) {
                $updateDataAP = ['comRptAmount' => $value->localAmount];
            } else {
                $newRpt = $this->convertAmountToLocalRpt($value->supplierInvoiceAmount, $value->comRptER);

                if (round($newRpt, 7) != $value->comRptAmount) {
                    $updateDataAP = ['comRptAmount' => round($newRpt, 7)];
                } 
            }

            if (count($updateDataAP) > 0) {
                $updateDataFinal[] = ['value' => $value, 'updateDataAP' => $updateDataAP];
                // DB::table('erp_accountspayableledger')->where('apAutoID', $value->apAutoID)->update($updateDataAP);
            }
        }

        $wrongUnbilledData = DB::table('erp_unbilledgrvgroupby')
                          ->whereDate('timeStamp', '>=', '2024-09-02')
                          ->whereColumn('supplierTransactionCurrencyID', '!=', 'companyReportingCurrencyID')
                          ->whereColumn('companyReportingER', '<=', 'supplierTransactionCurrencyER')
                          ->where('companyReportingER', '>', 0)
                          ->get();

        $wrong = [];
        foreach ($wrongUnbilledData as $key => $value) {
            $updateDataUnbilled = [];
            if ($value->localCurrencyID == $value->companyReportingCurrencyID) {
                $updateDataUnbilled = ['totRptAmount' => $value->totLocalAmount, 'totalVATAmountRpt' => $value->totalVATAmountLocal];
            } else {
                $newRpt = $this->convertAmountToLocalRpt($value->totTransactionAmount, $value->companyReportingER);

                if (round($newRpt, 7) != $value->totRptAmount) {
                    $updateDataUnbilled['totRptAmount'] = round($newRpt, 7);
                } 

                $newRptVat = $this->convertAmountToLocalRpt($value->totalVATAmount, $value->companyReportingER);

                if (round($newRptVat, 7) != $value->totalVATAmountRpt) {
                    $updateDataUnbilled['totalVATAmountRpt'] = round($newRptVat, 7);
                } 
            }

            if (count($updateDataUnbilled) > 0) {
                $updateDataFinal[] = ['value' => $value, 'updateDataUnbilled' => $updateDataUnbilled];
                // DB::table('erp_unbilledgrvgroupby')->where('unbilledgrvAutoID', $value->unbilledgrvAutoID)->update($updateDataUnbilled);
            }
        }

        $wrongTaxLedgerData = DB::table('erp_tax_ledger')
                          ->whereDate('createdDateTime', '>=', '2024-09-02')
                          ->whereColumn('transCurrencyID', '!=', 'rptCurrencyID')
                          ->whereColumn('comRptER', '<=', 'transER')
                          ->where('comRptER', '>', 0)
                          ->get();

        $wrong = [];
        foreach ($wrongTaxLedgerData as $key => $value) {
            $updateDataTaxLedger = [];
            if ($value->localCurrencyID == $value->rptCurrencyID) {
                $updateDataTaxLedger = ['rptAmount' => $value->localAmount, 'documentReportingAmount' => $value->documentLocalAmount];
            } else {
                $newRpt = $this->convertAmountToLocalRpt($value->transAmount, $value->comRptER);

                if (round($newRpt, 7) != $value->rptAmount) {
                    $updateDataTaxLedger['rptAmount'] = round($newRpt, 7);
                } 

                $newRptVat = $this->convertAmountToLocalRpt($value->documentTransAmount, $value->comRptER);

                if (round($newRptVat, 7) != $value->documentReportingAmount) {
                    $updateDataTaxLedger['documentReportingAmount'] = round($newRptVat, 7);
                } 
            }

            if (count($updateDataTaxLedger) > 0) {
                $updateDataFinal[] = ['value' => $value, 'updateDataTaxLedger' => $updateDataTaxLedger];
                // DB::table('erp_tax_ledger')->where('taxLedgerID', $value->taxLedgerID)->update($updateDataTaxLedger);
            }
        }

        $wrongTaxLedgerDetailData = DB::table('tax_ledger_details')
                          ->whereDate('createdDateTime', '>=', '2024-09-02')
                          ->whereColumn('transactionCurrencyID', '!=', 'rptCurrencyID')
                          ->where('reportingER', '<=', 1)
                          ->where('reportingER', '>', 0)
                          ->get();

        $wrong = [];
        foreach ($wrongTaxLedgerDetailData as $key => $value) {
            $updateDataTaxLedgerDetail = [];
            if ($value->localCurrencyID == $value->rptCurrencyID) {
                $updateDataTaxLedgerDetail = ['taxableAmountReporting' => $value->taxableAmountLocal, 'VATAmountRpt' => $value->VATAmountLocal];
            } else {
                $newRpt = $this->convertAmountToLocalRpt($value->taxableAmount, $value->reportingER);

                if (round($newRpt, 7) != $value->taxableAmountReporting) {
                    $updateDataTaxLedgerDetail['taxableAmountReporting'] = round($newRpt, 7);
                } 

                $newRptVat = $this->convertAmountToLocalRpt($value->VATAmount, $value->reportingER);

                if (round($newRptVat, 7) != $value->VATAmountRpt) {
                    $updateDataTaxLedgerDetail['VATAmountRpt'] = round($newRptVat, 7);
                } 
            }

            if (count($updateDataTaxLedgerDetail) > 0) {
                $updateDataFinal[] = ['value' => $value, 'updateDataTaxLedgerDetail' => $updateDataTaxLedgerDetail];
                // DB::table('tax_ledger_details')->where('id', $value->id)->update($updateDataTaxLedgerDetail);
            }
        }


        $wrongBLDaata = DB::table('erp_bankledger')
                          ->whereDate('createdDateTime', '>=', '2024-09-02')
                          ->whereColumn('supplierTransCurrencyID', '!=', 'companyRptCurrencyID')
                          ->whereColumn('companyRptCurrencyER', '<=', 'supplierTransCurrencyER')
                          ->where('companyRptCurrencyER', '>', 0)
                          ->get();

        $wrong = [];
        foreach ($wrongBLDaata as $key => $value) {
            $updateDataBL = [];
            if ($value->localCurrencyID == $value->companyRptCurrencyID) {
                $updateDataBL = ['payAmountCompRpt' => $value->payAmountCompLocal];
            } else {
                $newRpt = $this->convertAmountToLocalRpt($value->payAmountSuppTrans, $value->companyRptCurrencyER);

                if (round($newRpt, 7) != $value->payAmountCompRpt) {
                    $updateDataBL = ['payAmountCompRpt' => round($newRpt, 7)];
                } 
            }

            if (count($updateDataBL) > 0) {
                $updateDataFinal[] = ['value' => $value, 'updateDataBL' => $updateDataBL];
                // DB::table('erp_bankledger')->where('bankLedgerAutoID', $value->bankLedgerAutoID)->update($updateDataBL);
            }
        }

        //GRV
        $grvData = DB::table('erp_grvmaster')
            ->whereDate('createdDateTime', '>=', '2024-09-02')
            ->whereColumn('supplierTransactionCurrencyID', '!=', 'companyReportingCurrencyID')
            ->whereColumn('companyReportingER', '<=', 'supplierTransactionER')
            ->get();

        foreach ($grvData as $key => $value)
        {
            $updateDataGrv = array();
            if ($value->localCurrencyID == $value->companyReportingCurrencyID) {
                $updateDataGrv = ['grvTotalComRptCurrency' => $value->grvTotalLocalCurrency];
            } else {
                $newRpt = $this->convertAmountToLocalRpt($value->grvTotalSupplierTransactionCurrency, $value->companyReportingER);

                if (round($newRpt, 7) !== $value->grvTotalComRptCurrency) {
                    $updateDataGrv = ['grvTotalComRptCurrency' => round($newRpt, 7)];
                }
            }

            if (!empty($updateDataGrv)) {
                $updateDataFinal[] = ['value' => $value, 'updateDataGrv' => $updateDataGrv];
                // DB::table('erp_grvmaster')->where('grvAutoID', $value->grvAutoID)->update($updateDataGrv);
            }

            $grvDetails = DB::table('erp_grvdetails')
                ->where('grvAutoID',$value->grvAutoID)
                ->whereColumn('supplierDefaultCurrencyID', '!=', 'companyReportingCurrencyID')
                ->whereColumn('companyReportingER', '<=', 'supplierDefaultER')
                ->get();

            foreach ($grvDetails as $keyDetail => $valueDetail) {
                $updateDataGrvDetail = array();

                if ($valueDetail->localCurrencyID == $valueDetail->companyReportingCurrencyID) {
                    $updateDataGrvDetail = ['GRVcostPerUnitComRptCur' => $valueDetail->GRVcostPerUnitLocalCur, 'landingCost_RptCur' => $valueDetail->landingCost_LocalCur];
                } else {

                    $newRptGRVcostPerUnitSupTransCur = $this->convertAmountToLocalRpt($valueDetail->GRVcostPerUnitSupTransCur, $valueDetail->companyReportingER);

                    if (round($newRptGRVcostPerUnitSupTransCur, 7) !== $valueDetail->GRVcostPerUnitComRptCur) {
                        $updateDataGrvDetail['GRVcostPerUnitComRptCur'] = round($newRptGRVcostPerUnitSupTransCur, 7);
                    }

                    $newRptlandingCost_RptCur = $this->convertAmountToLocalRpt($valueDetail->landingCost_TransCur, $valueDetail->companyReportingER);

                    if (round($newRptlandingCost_RptCur, 7) !== $valueDetail->landingCost_RptCur) {
                        $updateDataGrvDetail['landingCost_RptCur'] = round($newRptlandingCost_RptCur, 7);
                    }
                }


                if (!empty($updateDataGrvDetail)) {
                    $updateDataFinal[] = ['valueDetail' => $valueDetail, 'updateDataGrvDetail' => $updateDataGrvDetail];
                    // DB::table('erp_grvdetails')->where('grvDetailsID', $valueDetail->grvDetailsID)->update($updateDataGrvDetail);
                }
            }
        }

        //Payment Voucher
        $payData = DB::table('erp_paysupplierinvoicemaster')
            ->whereDate('createdDateTime', '>=', '2024-09-02')
            ->whereColumn('supplierTransCurrencyID', '!=', 'companyRptCurrencyID')
            ->whereColumn('companyRptCurrencyER', '<=', 'supplierTransCurrencyER')
            ->get();

        foreach ($payData as $key => $value) {
            $updatePaySupplier = array();
            if ($value->localCurrencyID == $value->companyRptCurrencyID) {
                $updatePaySupplier = ['payAmountCompRpt' => $value->payAmountCompLocal, 'vatAmountRpt' => $value->VATAmountLocal, 'netAmountRpt' => $value->netAmountLocal];
            } else {
                $newRpt = $this->convertAmountToLocalRpt($value->payAmountSuppTrans, $value->companyRptCurrencyER);
                $vatRpt = $this->convertAmountToLocalRpt($value->VATAmount, $value->companyRptCurrencyER);
                $netRpt = $this->convertAmountToLocalRpt($value->netAmount, $value->companyRptCurrencyER);

                if (round($newRpt, 7) !== $value->payAmountCompRpt) {
                    $updatePaySupplier['payAmountCompRpt'] = round($newRpt, 7);
                }

                if (round($vatRpt, 7) !== $value->vatAmountRpt) {
                    $updatePaySupplier['vatAmountRpt'] = round($vatRpt, 7);
                }

                if (round($netRpt, 7) !== $value->netAmountRpt) {
                    $updatePaySupplier['netAmountRpt'] = round($netRpt, 7);
                }
            }

            if (!empty($updatePaySupplier)) {
                $updateDataFinal[] = ['value' => $value, 'updatePaySupplier' => $updatePaySupplier];
                // DB::table('erp_paysupplierinvoicemaster')->where('PayMasterAutoId', $value->PayMasterAutoId)->update($updatePaySupplier);
            }
        }

        //Pay supplier detail
        $payDetails = DB::table('erp_paysupplierinvoicedetail')
            ->whereDate('createdDateTime', '>=', '2024-09-02')
            ->whereColumn('supplierTransCurrencyID', '!=', 'comRptCurrencyID')
            ->whereColumn('comRptER', '<=', 'supplierTransER')
            ->get();

        foreach ($payDetails as $keyDetail => $valueDetail) {
            $updateDataPayDetail = array();

            if ($valueDetail->localCurrencyID == $valueDetail->comRptCurrencyID) {
                $updateDataPayDetail = ['paymentComRptAmount' => $valueDetail->paymentLocalAmount, 'VATAmountRpt' => $valueDetail->VATAmountLocal];
            } else {
                $newRptCom = $this->convertAmountToLocalRpt($valueDetail->paymentSupplierDefaultAmount, $valueDetail->comRptER);

                if ($newRptCom !== $valueDetail->paymentComRptAmount) {
                    $updateDataPayDetail['paymentComRptAmount'] = round($newRptCom,7);
                }

                $newRptVat = $this->convertAmountToLocalRpt($valueDetail->VATAmount, $valueDetail->comRptER);

                if ($newRptVat !== $valueDetail->VATAmountRpt) {
                    $updateDataPayDetail['VATAmountRpt'] = round($newRptVat, 7);
                }
            }

            if (!empty($updateDataPayDetail)) {
                $updateDataFinal[] = ['valueDetail' => $valueDetail, 'updateDataPayDetail' => $updateDataPayDetail];
                // DB::table('erp_paysupplierinvoicedetail')->where('payDetailAutoID', $valueDetail->payDetailAutoID)->update($updateDataPayDetail);
            }
        }

        //Pay supplier advance detail
        $advPayDetails = DB::table('erp_advancepaymentdetails')
            ->whereDate('timeStamp', '>=', '2024-09-02')
            ->whereColumn('supplierTransCurrencyID', '!=', 'comRptCurrencyID')
            ->whereColumn('comRptER', '<=', 'supplierTransER')
            ->get();

        foreach ($advPayDetails as $keyDetail => $valueDetail) {
            $updateDataPayAdvDetail = array();

            if ($valueDetail->localCurrencyID == $valueDetail->comRptCurrencyID) {
                $updateDataPayAdvDetail = ['comRptAmount' => $valueDetail->localAmount, 'VATAmountRpt' => $valueDetail->VATAmountLocal];
            } else {
                $newRptCom = $this->convertAmountToLocalRpt($valueDetail->supplierTransAmount, $valueDetail->comRptER);

                if ($newRptCom !== $valueDetail->comRptAmount) {
                    $updateDataPayAdvDetail['comRptAmount'] = round($newRptCom,7);
                }

                $newRptVat = $this->convertAmountToLocalRpt($valueDetail->VATAmount, $valueDetail->comRptER);

                if ($newRptVat !== $valueDetail->VATAmountRpt) {
                    $updateDataPayAdvDetail['VATAmountRpt'] = round($newRptVat,7);
                }
            }

            if (!empty($updateDataPayAdvDetail)) {
                $updateDataFinal[] = ['valueDetail' => $valueDetail, 'updateDataPayAdvDetail' => $updateDataPayAdvDetail];
                // DB::table('erp_advancepaymentdetails')->where('advancePaymentDetailAutoID', $valueDetail->advancePaymentDetailAutoID)->update($updateDataPayAdvDetail);
            }
        }

        //direct payment details
        $directPayDetails = DB::table('erp_directpaymentdetails')
            ->whereDate('timeStamp', '>=', '2024-09-02')
            ->whereColumn('supplierTransCurrencyID', '!=', 'comRptCurrency')
            ->whereColumn('comRptCurrencyER', '<=', 'supplierTransER')
            ->get();

        foreach ($directPayDetails as $keyDetail => $valueDetail) {
            $updateDataPayDirDetail = array();

            if ($valueDetail->localCurrency == $valueDetail->comRptCurrency) {
                $updateDataPayDirDetail = ['comRptAmount' => $valueDetail->localAmount];
            } else {
                $newRptCom = $this->convertAmountToLocalRpt($valueDetail->DPAmount, $valueDetail->comRptCurrencyER);

                if ($newRptCom !== $valueDetail->comRptAmount) {
                    $updateDataPayDirDetail['comRptAmount'] = round($newRptCom,7);
                }
            }

            if (!empty($updateDataPayDirDetail)) {
                $updateDataFinal[] = ['valueDetail' => $valueDetail, 'updateDataPayDirDetail' => $updateDataPayDirDetail];
                // DB::table('erp_directpaymentdetails')->where('directPaymentDetailsID', $valueDetail->directPaymentDetailsID)->update($updateDataPayDirDetail);
            }
        }




        //Console JV
        $consoleJvData = DB::table('erp_consolejvmaster')
            ->whereDate('createdDateTime', '>=', '2024-09-02')
            ->whereColumn('currencyID', '!=', 'rptCurrencyID')
            ->whereColumn('rptCurrencyER', '<=', 'currencyER')
            ->get();

        foreach ($consoleJvData as $key => $value) {
            $jvDetails = DB::table('erp_consolejvdetail')
                ->where('consoleJvMasterAutoId', '=', $value->consoleJvMasterAutoId)
                ->get();

            foreach ($jvDetails as $keyDetail => $valueDetail) {
                $updateDataJvDetail = array();

                if ($value->localCurrencyID == $value->rptCurrencyID) {
                    $updateDataJvDetail = ['rptDebitAmount' => $valueDetail->localDebitAmount, 'rptCreditAmount' => $valueDetail->localCreditAmount];
                } else {
                    $newRptDebit = $this->convertAmountToLocalRpt($valueDetail->debitAmount, $value->rptCurrencyER);

                    if ($newRptDebit !== $valueDetail->rptDebitAmount) {
                        $updateDataJvDetail['rptDebitAmount'] = round($newRptDebit,7);
                    }

                    $newRptCredit = $this->convertAmountToLocalRpt($valueDetail->creditAmount, $value->rptCurrencyER);

                    if ($newRptCredit !== $valueDetail->rptCreditAmount) {
                        $updateDataJvDetail['rptCreditAmount'] = round($newRptCredit,7);
                    }
                }

                if (!empty($updateDataJvDetail)) {
                     $updateDataFinal[] = ['valueDetail' => $valueDetail, 'updateDataJvDetail' => $updateDataJvDetail];
                    // DB::table('erp_consolejvdetail')->where('consoleJvDetailAutoID', $valueDetail->consoleJvDetailAutoID)->update($updateDataJvDetail);
                }
            }
        }

        //credit note
        $creditNote = DB::table('erp_creditnote')
            ->whereDate('createdDateTime', '>=', '2024-09-02')
            ->whereColumn('customerCurrencyID', '!=', 'companyReportingCurrencyID')
            ->whereColumn('companyReportingER', '<=', 'customerCurrencyER')
            ->get();

        foreach ($creditNote as $key => $value) {
            $updateDataCreditNote = array();
            if ($value->localCurrencyID == $value->companyReportingCurrencyID) {
                $updateDataCreditNote = ['creditAmountRpt' => $value->creditAmountLocal];
            } else {
                $newRpt = $this->convertAmountToLocalRpt($value->creditAmountTrans, $value->companyReportingER);

                if (round($newRpt, 7) !== $value->creditAmountRpt) {
                    $updateDataCreditNote = ['creditAmountRpt' => round($newRpt, 7)];
                }
            }

            if (!empty($updateDataCreditNote)) {
                $updateDataFinal[] = ['value' => $value, 'updateDataJvDetail' => $updateDataCreditNote];
                // DB::table('erp_creditnote')->where('creditNoteAutoID', $value->creditNoteAutoID)->update($updateDataCreditNote);
            }

            $creditNoteDetails = DB::table('erp_creditnotedetails')
                ->where('creditNoteAutoID',$value->creditNoteAutoID)
                ->whereColumn('creditAmountCurrency', '!=', 'comRptCurrency')
                ->whereColumn('comRptCurrencyER', '<=', 'creditAmountCurrencyER')
                ->get();

            foreach ($creditNoteDetails as $keyDetail => $valueDetail) {
                $updateCreditNoteDetail = array();

                if ($valueDetail->localCurrency == $valueDetail->comRptCurrency) {
                    $updateCreditNoteDetail = ['comRptAmount' => $valueDetail->localAmount, 'VATAmountRpt' => $valueDetail->VATAmountLocal, 'netAmountRpt' => $valueDetail->netAmountLocal];
                } else {
                    $newRptMark1 = $this->convertAmountToLocalRpt($valueDetail->creditAmount, $valueDetail->comRptCurrencyER);

                    if (round($newRptMark1, 7) !== $valueDetail->comRptAmount) {
                        $updateCreditNoteDetail['comRptAmount'] = round($newRptMark1, 7);
                    }

                    $newRptVatAm = $this->convertAmountToLocalRpt($valueDetail->VATAmount, $valueDetail->comRptCurrencyER);

                    if (round($newRptVatAm, 7) !== $valueDetail->VATAmountRpt) {
                        $updateCreditNoteDetail['VATAmountRpt'] = round($newRptVatAm, 7);
                    }

                    $newRptNetAmount = $this->convertAmountToLocalRpt($valueDetail->netAmount, $valueDetail->comRptCurrencyER);

                    if (round($newRptNetAmount, 7) !== $valueDetail->netAmountRpt) {
                        $updateCreditNoteDetail['netAmountRpt'] = round($newRptNetAmount, 7);
                    }
                }


                if (!empty($updateCreditNoteDetail)) {
                    $updateDataFinal[] = ['valueDetail' => $valueDetail, 'updateCreditNoteDetail' => $updateCreditNoteDetail];
                    // DB::table('erp_creditnotedetails')->where('creditNoteAutoID', $valueDetail->creditNoteAutoID)->update($updateCreditNoteDetail);
                }
            }
        }

        //Receipt voucher
        $receiptVoucherMaster = DB::table('erp_customerreceivepayment')
            ->whereDate('createdDateTime', '>=', '2024-09-02')
            ->whereColumn('custTransactionCurrencyID', '!=', 'companyRptCurrencyID')
            ->whereColumn('companyRptCurrencyER', '<=', 'custTransactionCurrencyER')
            ->get();

        foreach ($receiptVoucherMaster as $key => $value) {
            $updatePaySupplier = array();
            if ($value->localCurrencyID == $value->companyRptCurrencyID) {
                $updatePaySupplier = ['companyRptAmount' => $value->localAmount,'VATAmountRpt' => $value->VATAmountLocal,'netAmountRpt' => $value->netAmountLocal];
            } else {
                $newRpt = $this->convertAmountToLocalRpt($value->receivedAmount, $value->companyRptCurrencyER);
                $vatChanged = $this->convertAmountToLocalRpt($value->VATAmount, $value->companyRptCurrencyER);
                $netAmountNewRpt = $this->convertAmountToLocalRpt($value->netAmount, $value->companyRptCurrencyER);

                if (round($newRpt, 7) !== $value->companyRptAmount) {
                    $updatePaySupplier['companyRptAmount'] = round($newRpt, 7);
                }

                if (round($vatChanged, 7) !== $value->VATAmountRpt) {
                    $updatePaySupplier['VATAmountRpt'] = round($vatChanged, 7);
                }

                if ($netAmountNewRpt !== $value->netAmountRpt) {
                    $updatePaySupplier['netAmountRpt'] = $netAmountNewRpt;
                }
            }

            if (!empty($updatePaySupplier)) {
                $updateDataFinal[] = ['value' => $value, 'updatePaySupplier' => $updatePaySupplier];
                // DB::table('erp_customerreceivepayment')->where('custReceivePaymentAutoID', $value->custReceivePaymentAutoID)->update($updatePaySupplier);
            }
        }

        //receipt payment details
        $payDetails = DB::table('erp_custreceivepaymentdet')
            ->whereDate('timestamp', '>=', '2024-09-02')
            ->whereColumn('custReceiveCurrencyID', '!=', 'companyReportingCurrencyID')
            ->whereColumn('companyReportingER', '<=', 'custReceiveCurrencyER')
            ->get();

        foreach ($payDetails as $keyDetail => $valueDetail) {
            $updateDataPayDetail = array();

            if ($valueDetail->localCurrencyID == $valueDetail->companyReportingCurrencyID) {
                $updateDataPayDetail = ['receiveAmountRpt' => $valueDetail->receiveAmountLocal,'bookingAmountRpt' => $valueDetail->bookingAmountLocal, 'VATAmountRpt' => $valueDetail->VATAmountLocal];
            } else {
                $newRptCom = $this->convertAmountToLocalRpt($valueDetail->receiveAmountTrans, $valueDetail->companyReportingER);

                if ($newRptCom !== $valueDetail->receiveAmountRpt) {
                    $updateDataPayDetail['receiveAmountRpt'] = $newRptCom;
                }

                $newRptCom2 = $this->convertAmountToLocalRpt($valueDetail->bookingAmountTrans, $valueDetail->companyReportingER);

                if ($newRptCom2 !== $valueDetail->receiveAmountRpt) {
                    $updateDataPayDetail['bookingAmountRpt'] = $newRptCom2;
                }

                $newRptVat = $this->convertAmountToLocalRpt($valueDetail->VATAmount, $valueDetail->companyReportingER);

                if ($newRptVat !== $valueDetail->VATAmountRpt) {
                    $updateDataPayDetail['VATAmountRpt'] = round($newRptVat, 7);
                }

            }


            if (!empty($updateDataPayDetail)) {
                $updateDataFinal[] = ['valueDetail' => $valueDetail, 'updateDataPayDetail' => $updateDataPayDetail];
                // DB::table('erp_custreceivepaymentdet')->where('custRecivePayDetAutoID', $valueDetail->custRecivePayDetAutoID)->update($updateDataPayDetail);
            }
        }

        //direct receipt details
        $directDetails = DB::table('erp_directreceiptdetails')
            ->whereDate('timestamp', '>=', '2024-09-02')
            ->whereColumn('DRAmountCurrency', '!=', 'comRptCurrency')
            ->whereColumn('comRptCurrencyER', '<=', 'DDRAmountCurrencyER')
            ->get();

        foreach ($directDetails as $keyDetail => $valueDetail) {
            $updateDataDirectPayDetail = array();

            if ($valueDetail->localCurrency == $valueDetail->comRptCurrency) {
                $updateDataDirectPayDetail = ['comRptAmount' => $valueDetail->localAmount, 'VATAmountRpt' => $valueDetail->VATAmountLocal];
            } else {
                $newRptCom = $this->convertAmountToLocalRpt($valueDetail->DRAmount, $valueDetail->comRptCurrencyER);

                if ($newRptCom !== $valueDetail->comRptAmount) {
                    $updateDataDirectPayDetail['comRptAmount'] = round($newRptCom,7);
                }

                $newRptVat = $this->convertAmountToLocalRpt($valueDetail->VATAmount, $valueDetail->comRptCurrencyER);

                if ($newRptVat !== $valueDetail->VATAmountRpt) {
                    $updateDataDirectPayDetail['VATAmountRpt'] = round($newRptVat, 7);
                }

            }


            if (!empty($updateDataDirectPayDetail)) {
                $updateDataFinal[] = ['valueDetail' => $valueDetail, 'updateDataDirectPayDetail' => $updateDataDirectPayDetail];
                // DB::table('erp_directreceiptdetails')->where('directReceiptDetailsID', $valueDetail->directReceiptDetailsID)->update($updateDataDirectPayDetail);
            }
        }

        //advance receipt details
        $advancedDetails = DB::table('erp_advancereceiptdetails')
            ->whereDate('timestamp', '>=', '2024-09-02')
            ->whereColumn('customerTransCurrencyID', '!=', 'comRptCurrencyID')
            ->whereColumn('comRptER', '<=', 'customerTransER')
            ->get();

        foreach ($advancedDetails as $keyDetail => $valueDetail) {
            $updateDataAdvPayDetail = array();

            if ($valueDetail->localCurrencyID == $valueDetail->comRptCurrencyID) {
                $updateDataAdvPayDetail = ['comRptAmount' => $valueDetail->localAmount, 'VATAmountRpt' => $valueDetail->VATAmountLocal];
            } else {
                $newRptCom = $this->convertAmountToLocalRpt($valueDetail->paymentAmount, $valueDetail->comRptER);

                if ($newRptCom !== $valueDetail->comRptAmount) {
                    $updateDataAdvPayDetail['comRptAmount'] = round($newRptCom,7);
                }

                $newRptVat = $this->convertAmountToLocalRpt($valueDetail->VATAmount, $valueDetail->comRptER);

                if ($newRptVat !== $valueDetail->VATAmountRpt) {
                    $updateDataAdvPayDetail['VATAmountRpt'] = round($newRptVat, 7);
                }
            }

            if (!empty($updateDataAdvPayDetail)) {
                $updateDataFinal[] = ['valueDetail' => $valueDetail, 'updateDataAdvPayDetail' => $updateDataAdvPayDetail];
                // DB::table('erp_advancereceiptdetails')->where('advanceReceiptDetailAutoID', $valueDetail->advanceReceiptDetailAutoID)->update($updateDataAdvPayDetail);
            }
        }

        //Supplier invoice master
        $supplierMaster = DB::table('erp_bookinvsuppmaster')
            ->whereDate('createdDateTime', '>=', '2024-09-02')
            ->whereColumn('supplierTransactionCurrencyID', '!=', 'companyReportingCurrencyID')
            ->whereColumn('companyReportingER', '<=', 'supplierTransactionCurrencyER')
            ->get();

        foreach ($supplierMaster as $key => $value) {
            $updateSupplierMaster= array();
            if ($value->localCurrencyID == $value->companyReportingCurrencyID) {
                $updateSupplierMaster = ['bookingAmountRpt' => $value->bookingAmountLocal];
            } else {
                $newRpt = $this->convertAmountToLocalRpt($value->bookingAmountTrans, $value->companyReportingER);

                if (round($newRpt, 7) !== $value->bookingAmountRpt) {
                    $updateSupplierMaster = ['bookingAmountRpt' => round($newRpt, 7)];
                }
            }

            if (!empty($updateSupplierMaster)) {
                $updateDataFinal[] = ['value' => $value, 'updateSupplierMaster' => $updateSupplierMaster];
                // DB::table('erp_bookinvsuppmaster')->where('bookingSuppMasInvAutoID', $value->bookingSuppMasInvAutoID)->update($updateSupplierMaster);
            }

            $supplierMasterDetails = DB::table('erp_bookinvsuppdet')
                ->where('bookingSuppMasInvAutoID',$value->bookingSuppMasInvAutoID)
                ->get();

            foreach ($supplierMasterDetails as $keyDetail => $valueDetail) {
                $updateSupplierMasterDetail = array();

                if ($value->localCurrencyID == $value->companyReportingCurrencyID) {
                    $updateSupplierMasterDetail = ['totRptAmount' => $valueDetail->totLocalAmount, 'VATAmountRpt' => $valueDetail->VATAmountLocal];
                } else {
                    $newRptSupplier = $this->convertAmountToLocalRpt($valueDetail->totTransactionAmount, $valueDetail->companyReportingER);

                    if (round($newRptSupplier, 7) !== $valueDetail->totRptAmount) {
                        $updateSupplierMasterDetail['totRptAmount'] = round($newRptSupplier, 7);
                    }

                    $newRptVatSupplier= $this->convertAmountToLocalRpt($valueDetail->VATAmount, $valueDetail->companyReportingER);

                    if (round($newRptVatSupplier, 7) !== $valueDetail->VATAmountRpt) {
                        $updateSupplierMasterDetail['VATAmountRpt'] = round($newRptVatSupplier, 7);
                    }

                }

                if (!empty($updateSupplierMasterDetail)) {
                    $updateDataFinal[] = ['valueDetail' => $valueDetail, 'updateSupplierMasterDetail' => $updateSupplierMasterDetail];
                    // DB::table('erp_bookinvsuppdet')->where('bookingSuppMasInvAutoID', $valueDetail->bookingSuppMasInvAutoID)->update($updateSupplierMasterDetail);
                }
            }

        }

        //Match document
        $matchMaster = DB::table('erp_matchdocumentmaster')
            ->whereDate('createdDateTime', '>=', '2024-09-02')
            ->whereColumn('supplierTransCurrencyID', '!=', 'companyRptCurrencyID')
            ->whereColumn('companyRptCurrencyER', '<=', 'supplierTransCurrencyER')
            ->get();

        foreach ($matchMaster as $key => $value) {
            $updateMatchDoc = array();
            if ($value->localCurrencyID == $value->companyRptCurrencyID) {
                $updateMatchDoc = ['payAmountCompRpt' => $value->payAmountCompLocal];
            } else {
                $newRpt = $this->convertAmountToLocalRpt($value->payAmountSuppTrans, $value->companyRptCurrencyER);

                if (round($newRpt, 7) !== $value->payAmountCompRpt) {
                    $updateMatchDoc = ['payAmountCompRpt' => round($newRpt, 7)];
                }
            }

            if (!empty($updateMatchDoc)) {
                $updateDataFinal[] = ['value' => $value, 'updateMatchDoc' => $updateMatchDoc];
                // DB::table('erp_matchdocumentmaster')->where('matchDocumentMasterAutoID', $value->matchDocumentMasterAutoID)->update($updateMatchDoc);
            }
        }

        return $updateDataFinal;
    }


    public function convertAmountToLocalRpt($transactionAmount, $trasToRptER)
    {
        $reportingAmount = $transactionAmount;
        if ($trasToRptER > 1) {
            if (is_numeric($transactionAmount) && is_numeric($trasToRptER)) {
                $reportingAmount = $transactionAmount * $trasToRptER;
            } else {
                $reportingAmount = 0; 
            }
        } else {
            if (is_numeric($transactionAmount) && is_numeric($trasToRptER)) {
                $reportingAmount = $transactionAmount / $trasToRptER;
            } else {
                $reportingAmount = 0; 
            }
        }

        return $reportingAmount;
    }
}
