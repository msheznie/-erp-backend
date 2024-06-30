<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Models\StockTransfer;
use App\Models\AssetDisposalMaster;
use App\Models\AssetDisposalDetail;
use App\Models\StockTransferDetails;
use App\Models\PaySupplierInvoiceMaster;
use App\Models\CustomerInvoiceDirectDetail;
use App\Models\InterCompanyStockTransfer;
use App\Models\Company;
use App\Models\GRVMaster;
use App\Models\DirectPaymentDetails;
use App\Models\DirectReceiptDetail;
use App\Models\DocumentMaster;
use App\Models\ChartOfAccount;
use App\Models\ConsoleJVMaster;
use App\Models\SystemGlCodeScenarioDetail;
use App\Models\ConsoleJVDetail;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CreateConsoleJV implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $consoleJVData;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($consoleJVData)
    {
        $this->consoleJVData = $consoleJVData;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::useFiles(storage_path() . '/logs/create_console_jv_jobs.log');
        DB::beginTransaction();
        try {
            switch ($this->consoleJVData['type']) {
                case "STOCK_TRANSFER":
                    $this->createConsoleJVForStockTransfer($this->consoleJVData['data']);
                    break;
                case "FUND_TRANSFER":
                    $this->createConsoleJVForFundTransfer($this->consoleJVData['data']);
                    break;
                case "INTER_ASSET_DISPOSAL":
                    $this->createConsoleJVForAssetDisposal($this->consoleJVData['data']);
                    break;
                default:
                    // code...
                    break;
            }



            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($this->failed($e));
        }
    }

    public function failed($exception)
    {
        return $exception->getMessage();
    }


    public function createConsoleJVForStockTransfer($data)
    {
        $stMaster = StockTransfer::where('stockTransferAutoID', $data->stockTransferID)->first();
        $stDetails = StockTransferDetails::where("stockTransferAutoID", $data->stockTransferID)->get();

        $bookingAmountLocal = 0;
        $bookingAmountRpt = 0;
        $totalLocal = 0;
        $totalRpt = 0;
        $revenueTotalLocal = 0;
        $revenueTotalRpt = 0;
        $totalQty = 0;

        $fromCompany = Company::where('companySystemID', $stMaster->companyFromSystemID)->first();
        $revenuePercentageForInterCompanyInventoryTransfer = ($fromCompany) ? $fromCompany->revenuePercentageForInterCompanyInventoryTransfer : 3;


        $comment = "Inter Company Stock Transfer from " . $stMaster->companyFrom . " to " . $stMaster->companyTo . " " . $stMaster->stockTransferCode;


        $groupCompany = Company::find($fromCompany->masterCompanySystemIDReorting);

        $consoleJVMasterData = [
            'companySystemID' => $fromCompany->masterCompanySystemIDReorting,
            'companyID' => ($groupCompany) ? $groupCompany->CompanyID : null,
            'documentSystemID' => 69,
            'consoleJVdate' => Carbon::now(),
            'consoleJVNarration' => $comment,
            'jvType' => 1,
            'currencyID' => (count($stDetails) > 0) ? $stDetails[0]['reportingCurrencyID'] : null,
            'currencyER' => 1
        ];

        $documentMaster = DocumentMaster::find($consoleJVMasterData['documentSystemID']);
        if ($documentMaster) {
            $consoleJVMasterData['documentID'] = $documentMaster->documentID;
        }

        $lastSerial = ConsoleJVMaster::orderBy('serialNo', 'desc')->first();

        $lastSerialNumber = 1;
        if ($lastSerial) {
            $lastSerialNumber = intval($lastSerial->serialNo) + 1;
        }

        if ($documentMaster) {
            $documentCode = ($groupCompany->CompanyID . '\\' . $documentMaster->documentID . str_pad($lastSerialNumber, 6, '0', STR_PAD_LEFT));
            $consoleJVMasterData['consoleJVcode'] = $documentCode;
        }
        $consoleJVMasterData['serialNo'] = $lastSerialNumber;

        $companyCurrency = \Helper::companyCurrency($fromCompany->masterCompanySystemIDReorting);
        if ($companyCurrency) {
            $consoleJVMasterData['localCurrencyID'] = $companyCurrency->localcurrency->currencyID;
            $consoleJVMasterData['rptCurrencyID'] = $companyCurrency->reportingcurrency->currencyID;
            $companyCurrencyConversion = \Helper::currencyConversion($fromCompany->masterCompanySystemIDReorting, $consoleJVMasterData['currencyID'], $consoleJVMasterData['currencyID'], 0);
            if ($companyCurrencyConversion) {
                $consoleJVMasterData['localCurrencyER'] = $companyCurrencyConversion['trasToLocER'];
                $consoleJVMasterData['rptCurrencyER'] = $companyCurrencyConversion['trasToRptER'];
            }
        }

        $consoleJVMasterData['createdUserID'] = \Helper::getEmployeeID();
        $consoleJVMasterData['createdUserSystemID'] = \Helper::getEmployeeSystemID();
        $consoleJVMasterData['createdPcID'] = gethostname();

        $jvMaster = ConsoleJVMaster::create($consoleJVMasterData);

        $finalJvDetailData = [];

        $consoleJVDetailData = [
            'consoleJvMasterAutoId' => $jvMaster->consoleJvMasterAutoId,
            'serviceLineSystemID' => $stMaster->serviceLineSystemID,
            'serviceLineCode' => $stMaster->serviceLineCode,
            'currencyID' => $jvMaster->currencyID,
            'currencyER' => $jvMaster->currencyER,
            'debitAmount' => 0,
            'creditAmount' => 0,
            'localDebitAmount' => 0,
            'rptDebitAmount' => 0,
            'localCreditAmount' => 0,
            'rptCreditAmount' => 0,
            'createdUserSystemID' => \Helper::getEmployeeSystemID(),
            'createdUserID' => \Helper::getEmployeeID(),
            'createdPcID' => gethostname()
        ];


        foreach ($stDetails as $new) {
            $bookingAmountLocal = $bookingAmountLocal + (($new['unitCostLocal'] * ((100+$revenuePercentageForInterCompanyInventoryTransfer)/100)) * $new['qty']);
            $bookingAmountRpt = $bookingAmountRpt + (($new['unitCostRpt'] * ((100+$revenuePercentageForInterCompanyInventoryTransfer)/100)) * $new['qty']);

            $totalLocal = $totalLocal + (($new['unitCostLocal']) * $new['qty']);
            $totalRpt = $totalRpt + (($new['unitCostRpt']) * $new['qty']);

            $revenueTotalLocal = $revenueTotalLocal + (($new['unitCostLocal'] * ($revenuePercentageForInterCompanyInventoryTransfer/100)) * $new['qty']);
            $revenueTotalRpt = $revenueTotalRpt + (($new['unitCostRpt'] * ($revenuePercentageForInterCompanyInventoryTransfer/100)) * $new['qty']);

            $totalQty = $totalQty + $new['qty'];
        }

        $consoleJVDetailData['companySystemID'] = $stMaster->companyFromSystemID;
        $consoleJVDetailData['companyID'] = $stMaster->companyFrom;
        $consoleJVDetailData['glAccountSystemID'] = SystemGlCodeScenarioDetail::getGlByScenario($fromCompany->companySystemID, null, "inter-company-transfer-revenue");
        $consoleJVDetailData['glAccount'] = SystemGlCodeScenarioDetail::getGlCodeByScenario($fromCompany->companySystemID, null, "inter-company-transfer-revenue");
        $consoleJVDetailData['glAccountDescription'] = SystemGlCodeScenarioDetail::getGlDescriptionByScenario($fromCompany->companySystemID, null, "inter-company-transfer-revenue");

        $consoleJVDetailData['debitAmount'] = $revenueTotalRpt + $totalRpt;
        $conversionAmount = \Helper::convertAmountToLocalRpt(69, $jvMaster->consoleJvMasterAutoId, $consoleJVDetailData['debitAmount']);
        $consoleJVDetailData["localDebitAmount"] = $conversionAmount["localAmount"];
        $consoleJVDetailData["rptDebitAmount"] = $conversionAmount["reportingAmount"];

        array_push($finalJvDetailData, $consoleJVDetailData);

        $costGoodData = StockTransferDetails::selectRaw("SUM(qty* unitCostLocal) as localAmount, SUM(qty* unitCostRpt) as rptAmount,financeitemcategorysubassigned.financeGLcodePLSystemID,financeitemcategorysubassigned.financeGLcodePL,localCurrencyID,reportingCurrencyID")->WHERE('stockTransferAutoID', $stMaster->stockTransferAutoID)
                                                            ->join('financeitemcategorysubassigned', 'financeitemcategorysubassigned.itemCategorySubID', '=', 'erp_stocktransferdetails.itemFinanceCategorySubID')
                                                            ->where('financeitemcategorysubassigned.companySystemID', $stMaster->companySystemID)
                                                            ->whereNotNull('financeitemcategorysubassigned.financeGLcodePLSystemID')
                                                            ->where('financeitemcategorysubassigned.financeGLcodePLSystemID', '>', 0)
                                                            ->groupBy('financeitemcategorysubassigned.financeGLcodePLSystemID')
                                                            ->get();


        foreach ($costGoodData as $key => $value) {
            $revenueTotalLocalC = $value->localAmount * ($revenuePercentageForInterCompanyInventoryTransfer/100);
            $revenueTotalRptC = $value->rptAmount * ($revenuePercentageForInterCompanyInventoryTransfer/100);

            $consoleJVDetailData['companySystemID'] = $stMaster->companyToSystemID;
            $consoleJVDetailData['companyID'] = $stMaster->companyTo;
            $consoleJVDetailData['glAccountSystemID'] = $value->financeGLcodePLSystemID;
            $consoleJVDetailData['glAccount'] = $value->financeGLcodePL;
            $consoleJVDetailData['glAccountDescription'] = ChartOfAccount::getAccountDescription($value->financeGLcodePLSystemID);

            $consoleJVDetailData['debitAmount'] = 0;
            $consoleJVDetailData['creditAmount'] = $revenueTotalRptC + $value->rptAmount;
            $conversionAmount = \Helper::convertAmountToLocalRpt(69, $jvMaster->consoleJvMasterAutoId, $consoleJVDetailData['creditAmount']);
            $consoleJVDetailData["localDebitAmount"] = 0;
            $consoleJVDetailData["localCreditAmount"] = $conversionAmount["localAmount"];
            $consoleJVDetailData["rptDebitAmount"] = 0;
            $consoleJVDetailData["rptCreditAmount"] = $conversionAmount["reportingAmount"];

            array_push($finalJvDetailData, $consoleJVDetailData);
        }

        if (count($finalJvDetailData) > 0) {
            foreach ($finalJvDetailData as $cData) {
                ConsoleJVDetail::create($cData);
            }   
        }

    }


    public function createConsoleJVForFundTransfer($receiptVoucherData)
    {

        $paymentVoucher = PaySupplierInvoiceMaster::find($receiptVoucherData->intercompanyPaymentID);

        if ($paymentVoucher) {
            $comment = "Inter Company fund Transfer from " . $paymentVoucher->companyID . " to " . $receiptVoucherData->companyID . " " . $paymentVoucher->BPVcode;
            $fromCompany = Company::where('companySystemID', $paymentVoucher->companySystemID)->first();

            $groupCompany = Company::find($fromCompany->masterCompanySystemIDReorting);

            $consoleJVMasterData = [
                'companySystemID' => $fromCompany->masterCompanySystemIDReorting,
                'companyID' => ($groupCompany) ? $groupCompany->CompanyID : null,
                'documentSystemID' => 69,
                'consoleJVdate' => Carbon::now(),
                'consoleJVNarration' => $comment,
                'jvType' => 1,
                'currencyID' => $paymentVoucher->supplierTransCurrencyID,
                'currencyER' => 1
            ];

            $documentMaster = DocumentMaster::find($consoleJVMasterData['documentSystemID']);
            if ($documentMaster) {
                $consoleJVMasterData['documentID'] = $documentMaster->documentID;
            }

            $lastSerial = ConsoleJVMaster::orderBy('serialNo', 'desc')->first();

            $lastSerialNumber = 1;
            if ($lastSerial) {
                $lastSerialNumber = intval($lastSerial->serialNo) + 1;
            }

            if ($documentMaster) {
                $documentCode = ($groupCompany->CompanyID . '\\' . $documentMaster->documentID . str_pad($lastSerialNumber, 6, '0', STR_PAD_LEFT));
                $consoleJVMasterData['consoleJVcode'] = $documentCode;
            }
            $consoleJVMasterData['serialNo'] = $lastSerialNumber;

            $companyCurrency = \Helper::companyCurrency($fromCompany->masterCompanySystemIDReorting);
            if ($companyCurrency) {
                $consoleJVMasterData['localCurrencyID'] = $companyCurrency->localcurrency->currencyID;
                $consoleJVMasterData['rptCurrencyID'] = $companyCurrency->reportingcurrency->currencyID;
                $companyCurrencyConversion = \Helper::currencyConversion($fromCompany->masterCompanySystemIDReorting, $consoleJVMasterData['currencyID'], $consoleJVMasterData['currencyID'], 0);
                if ($companyCurrencyConversion) {
                    $consoleJVMasterData['localCurrencyER'] = $companyCurrencyConversion['trasToLocER'];
                    $consoleJVMasterData['rptCurrencyER'] = $companyCurrencyConversion['trasToRptER'];
                }
            }

            $consoleJVMasterData['createdUserID'] = \Helper::getEmployeeID();
            $consoleJVMasterData['createdUserSystemID'] = \Helper::getEmployeeSystemID();
            $consoleJVMasterData['createdPcID'] = gethostname();

            $jvMaster = ConsoleJVMaster::create($consoleJVMasterData);


            $finalJvDetailData = [];
            $consoleJVDetailData = [
                'consoleJvMasterAutoId' => $jvMaster->consoleJvMasterAutoId,
                'currencyID' => $jvMaster->currencyID,
                'currencyER' => $jvMaster->currencyER,
                'debitAmount' => 0,
                'creditAmount' => 0,
                'localDebitAmount' => 0,
                'rptDebitAmount' => 0,
                'localCreditAmount' => 0,
                'rptCreditAmount' => 0,
                'createdUserSystemID' => \Helper::getEmployeeSystemID(),
                'createdUserID' => \Helper::getEmployeeID(),
                'createdPcID' => gethostname()
            ];


            $paymentVoucherDetail = DirectPaymentDetails::where('directPaymentAutoID', $paymentVoucher->PayMasterAutoId)->first();

            if ($paymentVoucherDetail) {
                $consoleJVDetailData['companySystemID'] = $paymentVoucher->companySystemID;
                $consoleJVDetailData['companyID'] = $paymentVoucher->companyID;
                $consoleJVDetailData['serviceLineSystemID'] = $paymentVoucherDetail->serviceLineSystemID;
                $consoleJVDetailData['serviceLineCode'] = $paymentVoucherDetail->serviceLineCode;
                $consoleJVDetailData['glAccountSystemID'] = $paymentVoucherDetail->chartOfAccountSystemID;
                $consoleJVDetailData['glAccount'] = $paymentVoucherDetail->glCode;
                $consoleJVDetailData['glAccountDescription'] = $paymentVoucherDetail->glCodeDes;

                $consoleJVDetailData['debitAmount'] = $paymentVoucherDetail->DPAmount;
                $conversionAmount = \Helper::convertAmountToLocalRpt(69, $jvMaster->consoleJvMasterAutoId, $consoleJVDetailData['debitAmount']);
                $consoleJVDetailData["localDebitAmount"] = $conversionAmount["localAmount"];
                $consoleJVDetailData["rptDebitAmount"] = $conversionAmount["reportingAmount"];

                array_push($finalJvDetailData, $consoleJVDetailData);
            }

             $receiptVocherDetail = DirectReceiptDetail::where('directReceiptAutoID', $receiptVoucherData->custReceivePaymentAutoID)->first();

            if ($receiptVocherDetail) {
                $consoleJVDetailData['companySystemID'] = $receiptVoucherData->companySystemID;
                $consoleJVDetailData['companyID'] = $receiptVoucherData->companyID;
                $consoleJVDetailData['serviceLineSystemID'] = $receiptVocherDetail->serviceLineSystemID;
                $consoleJVDetailData['serviceLineCode'] = $receiptVocherDetail->serviceLineCode;
                $consoleJVDetailData['glAccountSystemID'] = $receiptVocherDetail->chartOfAccountSystemID;
                $consoleJVDetailData['glAccount'] = $receiptVocherDetail->glCode;
                $consoleJVDetailData['glAccountDescription'] = $receiptVocherDetail->glCodeDes;

                $consoleJVDetailData['debitAmount'] = 0;
                $consoleJVDetailData["localDebitAmount"] = 0;
                $consoleJVDetailData["rptDebitAmount"] = 0;
                $consoleJVDetailData['creditAmount'] = $receiptVocherDetail->DRAmount;
                $conversionAmount = \Helper::convertAmountToLocalRpt(69, $jvMaster->consoleJvMasterAutoId, $consoleJVDetailData['creditAmount']);
                $consoleJVDetailData["localDebitAmount"] = $conversionAmount["localAmount"];
                $consoleJVDetailData["rptDebitAmount"] = $conversionAmount["reportingAmount"];

                array_push($finalJvDetailData, $consoleJVDetailData);
            }

            if (count($finalJvDetailData) > 0) {
                foreach ($finalJvDetailData as $cData) {
                    ConsoleJVDetail::create($cData);
                }   
            }

        }

    }


    public function createConsoleJVForAssetDisposal($interComapnyData)
    {
        $assetDisposalMaster = AssetDisposalMaster::with(['disposal_type' => function ($query) {
                            $query->with('chartofaccount');
                        }])->find($interComapnyData->assetDisposalID);

        if ($assetDisposalMaster) {
            $comment = "Inter Company Asset disposal from " . $assetDisposalMaster->companyID . " to " . $assetDisposalMaster->toCompanyID . " " . $assetDisposalMaster->disposalDocumentCode;
            $fromCompany = Company::where('companySystemID', $assetDisposalMaster->companySystemID)->first();

            $groupCompany = Company::find($fromCompany->masterCompanySystemIDReorting);

            $consoleJVMasterData = [
                'companySystemID' => $fromCompany->masterCompanySystemIDReorting,
                'companyID' => ($groupCompany) ? $groupCompany->CompanyID : null,
                'documentSystemID' => 69,
                'consoleJVdate' => Carbon::now(),
                'consoleJVNarration' => $comment,
                'jvType' => 1,
                'currencyID' => $fromCompany->reportingCurrency,
                'currencyER' => 1
            ];

            $documentMaster = DocumentMaster::find($consoleJVMasterData['documentSystemID']);
            if ($documentMaster) {
                $consoleJVMasterData['documentID'] = $documentMaster->documentID;
            }

            $lastSerial = ConsoleJVMaster::orderBy('serialNo', 'desc')->first();

            $lastSerialNumber = 1;
            if ($lastSerial) {
                $lastSerialNumber = intval($lastSerial->serialNo) + 1;
            }

            if ($documentMaster) {
                $documentCode = ($groupCompany->CompanyID . '\\' . $documentMaster->documentID . str_pad($lastSerialNumber, 6, '0', STR_PAD_LEFT));
                $consoleJVMasterData['consoleJVcode'] = $documentCode;
            }
            $consoleJVMasterData['serialNo'] = $lastSerialNumber;

            $companyCurrency = \Helper::companyCurrency($fromCompany->masterCompanySystemIDReorting);
            if ($companyCurrency) {
                $consoleJVMasterData['localCurrencyID'] = $companyCurrency->localcurrency->currencyID;
                $consoleJVMasterData['rptCurrencyID'] = $companyCurrency->reportingcurrency->currencyID;
                $companyCurrencyConversion = \Helper::currencyConversion($fromCompany->masterCompanySystemIDReorting, $consoleJVMasterData['currencyID'], $consoleJVMasterData['currencyID'], 0);
                if ($companyCurrencyConversion) {
                    $consoleJVMasterData['localCurrencyER'] = $companyCurrencyConversion['trasToLocER'];
                    $consoleJVMasterData['rptCurrencyER'] = $companyCurrencyConversion['trasToRptER'];
                }
            }

            $consoleJVMasterData['createdUserID'] = \Helper::getEmployeeID();
            $consoleJVMasterData['createdUserSystemID'] = \Helper::getEmployeeSystemID();
            $consoleJVMasterData['createdPcID'] = gethostname();

            $jvMaster = ConsoleJVMaster::create($consoleJVMasterData);


            $finalJvDetailData = [];
            $consoleJVDetailData = [
                'consoleJvMasterAutoId' => $jvMaster->consoleJvMasterAutoId,
                'currencyID' => $jvMaster->currencyID,
                'currencyER' => $jvMaster->currencyER,
                'debitAmount' => 0,
                'creditAmount' => 0,
                'localDebitAmount' => 0,
                'rptDebitAmount' => 0,
                'localCreditAmount' => 0,
                'rptCreditAmount' => 0,
                'createdUserSystemID' => \Helper::getEmployeeSystemID(),
                'createdUserID' => \Helper::getEmployeeID(),
                'createdPcID' => gethostname()
            ];


            $disposal = AssetDisposalDetail::with('disposal_account')
                                           ->selectRaw('SUM(netBookValueLocal) as netBookValueLocal, SUM(netBookValueRpt) as netBookValueRpt,DISPOGLCODESystemID,DISPOGLCODE,serviceLineSystemID,serviceLineCode')
                                           ->OfMaster($interComapnyData->assetDisposalID)
                                           ->groupBy('DISPOGLCODESystemID', 'serviceLineSystemID')
                                           ->get();

             if ($disposal) {
                foreach ($disposal as $val) {
                    $consoleJVDetailData['companySystemID'] = $assetDisposalMaster->companySystemID;
                    $consoleJVDetailData['companyID'] = $assetDisposalMaster->companyID;
                    $consoleJVDetailData['serviceLineSystemID'] = $val->serviceLineSystemID;
                    $consoleJVDetailData['serviceLineCode'] = $val->serviceLineCode;
                    $consoleJVDetailData['glAccountSystemID'] = $assetDisposalMaster->disposal_type->chartOfAccountID;
                    $consoleJVDetailData['glAccount'] = $assetDisposalMaster->disposal_type->glCode;
                    $consoleJVDetailData['glAccountDescription'] = ChartOfAccount::getAccountDescription($assetDisposalMaster->disposal_type->chartOfAccountID);

                    $consoleJVDetailData['debitAmount'] = ABS($val->netBookValueRpt);
                    $conversionAmount = \Helper::convertAmountToLocalRpt(69, $jvMaster->consoleJvMasterAutoId, $consoleJVDetailData['debitAmount']);
                    $consoleJVDetailData["localDebitAmount"] = $conversionAmount["localAmount"];
                    $consoleJVDetailData["rptDebitAmount"] = $conversionAmount["reportingAmount"];

                    array_push($finalJvDetailData, $consoleJVDetailData);
                }
            }



            $grvMaster = GRVMaster::find($interComapnyData->grvID);

            $cost = AssetDisposalDetail::with(['cost_account'])->selectRaw('SUM(COSTUNIT) as COSTUNIT, SUM(sellingPriceRpt) as sellingPriceRpt,SUM(netBookValueRpt) as netBookValueRpt, COSTGLCODESystemID,serviceLineSystemID,COSTGLCODE,serviceLineCode')
                                       ->OfMaster($interComapnyData->assetDisposalID)
                                       ->groupBy('COSTGLCODESystemID', 'serviceLineSystemID')->get();


            if ($cost && $grvMaster) {
                foreach ($cost as $val) {
                    $consoleJVDetailData['companySystemID'] = $grvMaster->companySystemID;
                    $consoleJVDetailData['companyID'] = $grvMaster->companyID;
                    $consoleJVDetailData['serviceLineSystemID'] = $val->serviceLineSystemID;
                    $consoleJVDetailData['serviceLineCode'] = $val->serviceLineCode;
                    $consoleJVDetailData['glAccountSystemID'] = $val->COSTGLCODESystemID;
                    $consoleJVDetailData['glAccount'] = $val->COSTGLCODE;
                    $consoleJVDetailData['glAccountDescription'] = ChartOfAccount::getAccountDescription($val->COSTGLCODESystemID);

                    $consoleJVDetailData['debitAmount'] = ABS(ABS($val->sellingPriceRpt) - ABS($val->netBookValueRpt));
                    $conversionAmount = \Helper::convertAmountToLocalRpt(69, $jvMaster->consoleJvMasterAutoId, $consoleJVDetailData['debitAmount']);
                    $consoleJVDetailData["localDebitAmount"] = $conversionAmount["localAmount"];
                    $consoleJVDetailData["rptDebitAmount"] = $conversionAmount["reportingAmount"];

                    array_push($finalJvDetailData, $consoleJVDetailData);
                }
            }                  


            $cusDetail = CustomerInvoiceDirectDetail::selectRaw("sum(comRptAmount) as comRptAmount, comRptCurrency, sum(localAmount) as localAmount , localCurrencyER, localCurrency, sum(invoiceAmount) as invoiceAmount, invoiceAmountCurrencyER, invoiceAmountCurrency,comRptCurrencyER, customerID, clientContractID, comments, glSystemID,   serviceLineSystemID,serviceLineCode, sum(VATAmount) as VATAmount, sum(VATAmountLocal) as VATAmountLocal, sum(VATAmountRpt) as VATAmountRpt")
                                                 ->WHERE('custInvoiceDirectID', $interComapnyData->customerInvoiceID)
                                                 ->groupBy('glCode', 'serviceLineCode', 'comments')->get();


            if ($cusDetail) {
                foreach ($cusDetail as $valCustInvoice) {
                    $consoleJVDetailData['companySystemID'] = $assetDisposalMaster->companySystemID;
                    $consoleJVDetailData['companyID'] = $assetDisposalMaster->companyID;
                    $consoleJVDetailData['serviceLineSystemID'] = $valCustInvoice->serviceLineSystemID;
                    $consoleJVDetailData['serviceLineCode'] = $valCustInvoice->serviceLineCode;
                    $consoleJVDetailData['glAccountSystemID'] = $valCustInvoice->glSystemID;
                    $consoleJVDetailData['glAccount'] = ChartOfAccount::getAccountCode($valCustInvoice->glSystemID);
                    $consoleJVDetailData['glAccountDescription'] = ChartOfAccount::getAccountDescription($valCustInvoice->glSystemID);

                    $consoleJVDetailData['debitAmount'] = 0;
                    $consoleJVDetailData["localDebitAmount"] = 0;
                    $consoleJVDetailData["rptDebitAmount"] = 0;
                    $consoleJVDetailData['creditAmount'] = $valCustInvoice->comRptAmount;
                    $conversionAmount = \Helper::convertAmountToLocalRpt(69, $jvMaster->consoleJvMasterAutoId, $consoleJVDetailData['creditAmount']);
                    $consoleJVDetailData["localDebitAmount"] = $conversionAmount["localAmount"];
                    $consoleJVDetailData["rptDebitAmount"] = $conversionAmount["reportingAmount"];

                    array_push($finalJvDetailData, $consoleJVDetailData);
                }
            }

             if (count($finalJvDetailData) > 0) {
                foreach ($finalJvDetailData as $cData) {
                    ConsoleJVDetail::create($cData);
                }   
            }


        }
    }
}
