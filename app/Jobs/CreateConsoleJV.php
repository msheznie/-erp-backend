<?php

namespace App\Jobs;

use App\Models\AccountsReceivableLedger;
use App\Models\CustomerInvoice;
use App\Models\CustomerMaster;
use App\Models\CustomerReceivePaymentDetail;
use App\Models\InterCompanyAssetDisposal;
use App\Models\PaySupplierInvoiceDetail;
use App\Models\SupplierMaster;
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
use App\Models\InterCompanyStockTransfer;
use App\Models\Company;
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
    public function handle() {
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

    public function failed($exception) {
        return $exception->getMessage();
    }

    public function getAccountReceivableLedgerBalance($customerInvoiceID) {
        $customerInvoice = CustomerInvoice::find($customerInvoiceID);

        $total = 0;
        if ($customerInvoice) {
            $accountReceivableLedgerBalance = AccountsReceivableLedger::where('documentSystemID', 20)
                ->where('companySystemID', $customerInvoice->companySystemID)
                ->where('documentCodeSystem', $customerInvoice->custInvoiceDirectAutoID)
                ->get();

            $total = $accountReceivableLedgerBalance ? collect($accountReceivableLedgerBalance)->sum('custInvoiceAmount') : 0;
        }

        return $total;
    }

    public function createConsoleJVForStockTransfer($dataset) {
        $data = $dataset['docData'];
        $from = $dataset['from'];

        switch ($from) {
            case "AFTER_CUSTOMER_INVOICE":
                $this->processConsoleJVForStockTransfer($data->stockTransferAutoID, $from);
                break;
            case "AFTER_STOCK_RECEIVE":
                $this->processConsoleJVForStockTransfer($data->stockTransferID, $from);
                break;
            case "AFTER_PAYMENT_VOUCHER":
                if ($data->invoiceType == 2) {
                    $pvDetails = PaySupplierInvoiceDetail::where('companySystemID',$data->companySystemID)
                        ->where('PayMasterAutoId', $data->PayMasterAutoId)
                        ->where('addedDocumentSystemID', 11)
                        ->get();
                    foreach ($pvDetails as $pvDetail) {
                        $stockTransfer = InterCompanyStockTransfer::where('supplierInvoiceID', $pvDetail->bookingInvSystemCode)->first();
                        if ($stockTransfer) {
                            $this->processConsoleJVForStockTransfer($stockTransfer->stockTransferID, $from);
                        }
                    }
                }
                break;
            case "AFTER_RECEIPT_VOUCHER":
                if ($data->documentType == 13) {
                    $rvDetails = CustomerReceivePaymentDetail::where('companySystemID',$data->companySystemID)
                        ->where('custReceivePaymentAutoID', $data->custReceivePaymentAutoID)
                        ->where('addedDocumentSystemID', 20)
                        ->get();
                    foreach ($rvDetails as $rvDetail) {
                        $stockTransfer = InterCompanyStockTransfer::where('customerInvoiceID', $rvDetail->bookingInvCodeSystem)->first();
                        if ($stockTransfer) {
                            $this->processConsoleJVForStockTransfer($stockTransfer->stockTransferID, $from);
                        }
                    }
                }
                break;
            default:
                break;
        }
    }

    public function processConsoleJVForStockTransfer($stockTransferAutoID, $from) {

        $interCompanyStockTransfer = InterCompanyStockTransfer::where('stockTransferID', $stockTransferAutoID)->first();

        $stMaster = StockTransfer::where('stockTransferAutoID', $stockTransferAutoID)->first();
        $stDetails = StockTransferDetails::where("stockTransferAutoID", $stockTransferAutoID)->get();

        if (!$stMaster || !$stDetails) {
            return;
        }

        $bookingAmountLocal = 0;
        $bookingAmountRpt = 0;
        $totalLocal = 0;
        $totalRpt = 0;
        $revenueTotalLocal = 0;
        $revenueTotalRpt = 0;
        $totalQty = 0;

        $fromCompany = Company::where('companySystemID', $stMaster->companyFromSystemID)->first();
        $revenuePercentageForInterCompanyInventoryTransfer = 0;

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

        $debitAmount = 0;

        switch ($from) {
            case "AFTER_CUSTOMER_INVOICE":
                $debitAmount = $this->getAccountReceivableLedgerBalance($interCompanyStockTransfer->customerInvoiceID);

                $consoleJVDetailData['companySystemID'] = $stMaster->companyToSystemID;
                $consoleJVDetailData['companyID'] = $stMaster->companyTo;

                $supplierMaster = SupplierMaster::where('companyLinkedToSystemID', $stMaster->companyFromSystemID)->first();
                if ($supplierMaster) {
                    $consoleJVDetailData['glAccountSystemID'] = $supplierMaster->liabilityAccountSysemID;
                    $consoleJVDetailData['glAccount'] = $supplierMaster->liabilityAccount;
                    $consoleJVDetailData['glAccountDescription'] = ChartOfAccount::getAccountDescription($supplierMaster->liabilityAccountSysemID);
                }
                break;
            case "AFTER_STOCK_RECEIVE":
                foreach ($stDetails as $new) {
                    $bookingAmountLocal = $bookingAmountLocal + (($new['unitCostLocal'] * ((100+$revenuePercentageForInterCompanyInventoryTransfer)/100)) * $new['qty']);
                    $bookingAmountRpt = $bookingAmountRpt + (($new['unitCostRpt'] * ((100+$revenuePercentageForInterCompanyInventoryTransfer)/100)) * $new['qty']);

                    $totalLocal = $totalLocal + (($new['unitCostLocal']) * $new['qty']);
                    $totalRpt = $totalRpt + (($new['unitCostRpt']) * $new['qty']);

                    $revenueTotalLocal = $revenueTotalLocal + (($new['unitCostLocal'] * ($revenuePercentageForInterCompanyInventoryTransfer/100)) * $new['qty']);
                    $revenueTotalRpt = $revenueTotalRpt + (($new['unitCostRpt'] * ($revenuePercentageForInterCompanyInventoryTransfer/100)) * $new['qty']);

                    $totalQty = $totalQty + $new['qty'];
                }

                $debitAmount = $revenueTotalRpt + $totalRpt;

                $consoleJVDetailData['companySystemID'] = $stMaster->companyFromSystemID;
                $consoleJVDetailData['companyID'] = $stMaster->companyFrom;

                $consoleJVDetailData['glAccountSystemID'] = SystemGlCodeScenarioDetail::getGlByScenario($fromCompany->companySystemID, null, "inter-company-transfer-revenue");
                $consoleJVDetailData['glAccount'] = SystemGlCodeScenarioDetail::getGlCodeByScenario($fromCompany->companySystemID, null, "inter-company-transfer-revenue");
                $consoleJVDetailData['glAccountDescription'] = SystemGlCodeScenarioDetail::getGlDescriptionByScenario($fromCompany->companySystemID, null, "inter-company-transfer-revenue");
                break;
            case "AFTER_PAYMENT_VOUCHER":
            case "AFTER_RECEIPT_VOUCHER":
                $debitAmount = $this->getAccountReceivableLedgerBalance($interCompanyStockTransfer->customerInvoiceID);

                $consoleJVDetailData['companySystemID'] = $stMaster->companyFromSystemID;
                $consoleJVDetailData['companyID'] = $stMaster->companyFrom;

                $customerMaster = CustomerMaster::where('companyLinkedToSystemID', $stMaster->companyToSystemID)->first();
                if ($customerMaster) {
                    $consoleJVDetailData['glAccountSystemID'] = $customerMaster->custGLAccountSystemID;
                    $consoleJVDetailData['glAccount'] = $customerMaster->custGLaccount;
                    $consoleJVDetailData['glAccountDescription'] = ChartOfAccount::getAccountDescription($customerMaster->custGLAccountSystemID);
                }
                break;
            default:
                break;
        }

        $consoleJVDetailData['debitAmount'] = $debitAmount;
        $conversionAmount = \Helper::convertAmountToLocalRpt(69, $jvMaster->consoleJvMasterAutoId, $consoleJVDetailData['debitAmount']);
        $consoleJVDetailData["localDebitAmount"] = $conversionAmount["localAmount"];
        $consoleJVDetailData["rptDebitAmount"] = $conversionAmount["reportingAmount"];
        $consoleJVDetailData['creditAmount'] = 0;
        $consoleJVDetailData['localCreditAmount'] = 0;
        $consoleJVDetailData['rptCreditAmount'] = 0;

        array_push($finalJvDetailData, $consoleJVDetailData);

        switch ($from) {
            case "AFTER_CUSTOMER_INVOICE":
                $creditAmount = $debitAmount;

                $consoleJVDetailData['companySystemID'] = $stMaster->companyFromSystemID;
                $consoleJVDetailData['companyID'] = $stMaster->companyFrom;

                $customerMaster = CustomerMaster::where('companyLinkedToSystemID', $stMaster->companyToSystemID)->first();
                if ($customerMaster) {
                    $consoleJVDetailData['glAccountSystemID'] = $customerMaster->custGLAccountSystemID;
                    $consoleJVDetailData['glAccount'] = $customerMaster->custGLaccount;
                    $consoleJVDetailData['glAccountDescription'] = ChartOfAccount::getAccountDescription($customerMaster->custGLAccountSystemID);
                }

                $consoleJVDetailData['creditAmount'] = $creditAmount;
                $conversionAmount = \Helper::convertAmountToLocalRpt(69, $jvMaster->consoleJvMasterAutoId, $consoleJVDetailData['creditAmount']);
                $consoleJVDetailData["localCreditAmount"] = $conversionAmount["localAmount"];
                $consoleJVDetailData["rptCreditAmount"] = $conversionAmount["reportingAmount"];
                $consoleJVDetailData['debitAmount'] = 0;
                $consoleJVDetailData["localDebitAmount"] = 0;
                $consoleJVDetailData["rptDebitAmount"] = 0;

                array_push($finalJvDetailData, $consoleJVDetailData);
                break;
            case "AFTER_STOCK_RECEIVE":
                $costGoodData = StockTransferDetails::selectRaw("SUM(qty* unitCostLocal) as localAmount, SUM(qty* unitCostRpt) as rptAmount,financeitemcategorysubassigned.financeGLcodePLSystemID,financeitemcategorysubassigned.financeGLcodePL,localCurrencyID,reportingCurrencyID")
                    ->WHERE('stockTransferAutoID', $stMaster->stockTransferAutoID)
                    ->join('financeitemcategorysubassigned', 'financeitemcategorysubassigned.itemCategorySubID', '=', 'erp_stocktransferdetails.itemFinanceCategorySubID')
                    ->where('financeitemcategorysubassigned.companySystemID', $stMaster->companySystemID)
                    ->whereNotNull('financeitemcategorysubassigned.financeGLcodePLSystemID')
                    ->where('financeitemcategorysubassigned.financeGLcodePLSystemID', '>', 0)
                    ->groupBy('financeitemcategorysubassigned.financeGLcodePLSystemID')
                    ->get();

                foreach ($costGoodData as $key => $value) {
                    $revenueTotalLocalC = $value->localAmount * ($revenuePercentageForInterCompanyInventoryTransfer/100);
                    $revenueTotalRptC = $value->rptAmount * ($revenuePercentageForInterCompanyInventoryTransfer/100);

                    $creditAmount = $revenueTotalRptC + $value->rptAmount;

                    $consoleJVDetailData['companySystemID'] = $stMaster->companyToSystemID;
                    $consoleJVDetailData['companyID'] = $stMaster->companyTo;

                    $consoleJVDetailData['glAccountSystemID'] = $value->financeGLcodePLSystemID;
                    $consoleJVDetailData['glAccount'] = $value->financeGLcodePL;
                    $consoleJVDetailData['glAccountDescription'] = ChartOfAccount::getAccountDescription($value->financeGLcodePLSystemID);

                    $consoleJVDetailData['creditAmount'] = $creditAmount;
                    $conversionAmount = \Helper::convertAmountToLocalRpt(69, $jvMaster->consoleJvMasterAutoId, $consoleJVDetailData['creditAmount']);
                    $consoleJVDetailData["localCreditAmount"] = $conversionAmount["localAmount"];
                    $consoleJVDetailData["rptCreditAmount"] = $conversionAmount["reportingAmount"];
                    $consoleJVDetailData['debitAmount'] = 0;
                    $consoleJVDetailData["localDebitAmount"] = 0;
                    $consoleJVDetailData["rptDebitAmount"] = 0;

                    array_push($finalJvDetailData, $consoleJVDetailData);
                }
                break;
            case "AFTER_PAYMENT_VOUCHER":
            case "AFTER_RECEIPT_VOUCHER":
                $creditAmount = $debitAmount;

                $consoleJVDetailData['companySystemID'] = $stMaster->companyToSystemID;
                $consoleJVDetailData['companyID'] = $stMaster->companyTo;

                $supplierMaster = SupplierMaster::where('companyLinkedToSystemID', $stMaster->companyFromSystemID)->first();
                if ($supplierMaster) {
                    $consoleJVDetailData['glAccountSystemID'] = $supplierMaster->liabilityAccountSysemID;
                    $consoleJVDetailData['glAccount'] = $supplierMaster->liabilityAccount;
                    $consoleJVDetailData['glAccountDescription'] = ChartOfAccount::getAccountDescription($supplierMaster->liabilityAccountSysemID);
                }

                $consoleJVDetailData['creditAmount'] = $creditAmount;
                $conversionAmount = \Helper::convertAmountToLocalRpt(69, $jvMaster->consoleJvMasterAutoId, $consoleJVDetailData['creditAmount']);
                $consoleJVDetailData["localCreditAmount"] = $conversionAmount["localAmount"];
                $consoleJVDetailData["rptCreditAmount"] = $conversionAmount["reportingAmount"];
                $consoleJVDetailData['debitAmount'] = 0;
                $consoleJVDetailData["localDebitAmount"] = 0;
                $consoleJVDetailData["rptDebitAmount"] = 0;

                array_push($finalJvDetailData, $consoleJVDetailData);
                break;
            default:
                break;
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

    public function createConsoleJVForAssetDisposal($dataset) {

        $data = $dataset['docData'];
        $from = $dataset['from'];

        switch ($from) {
            case "AFTER_GRV_VOUCHER":
            case "AFTER_CUSTOMER_INVOICE":
                $this->processConsoleJVForAssetDisposal($data->assetDisposalID, $from);
                break;
            case "AFTER_PAYMENT_VOUCHER":
                if ($data->invoiceType == 2) {
                    $pvDetails = PaySupplierInvoiceDetail::where('companySystemID',$data->companySystemID)
                        ->where('PayMasterAutoId', $data->PayMasterAutoId)
                        ->where('addedDocumentSystemID', 11)
                        ->get();
                    foreach ($pvDetails as $pvDetail) {
                        $assetDisposal = InterCompanyAssetDisposal::where('supplierInvoiceID', $pvDetail->bookingInvSystemCode)->first();
                        if ($assetDisposal) {
                            $this->processConsoleJVForAssetDisposal($assetDisposal->assetDisposalID, $from);
                        }
                    }
                }
                break;
            case "AFTER_RECEIPT_VOUCHER":
                if ($data->documentType == 13) {
                    $rvDetails = CustomerReceivePaymentDetail::where('companySystemID',$data->companySystemID)
                        ->where('custReceivePaymentAutoID', $data->custReceivePaymentAutoID)
                        ->where('addedDocumentSystemID', 20)
                        ->get();
                    foreach ($rvDetails as $rvDetail) {
                        $assetDisposal = InterCompanyAssetDisposal::where('customerInvoiceID', $rvDetail->bookingInvCodeSystem)->first();
                        if ($assetDisposal) {
                            $this->processConsoleJVForAssetDisposal($assetDisposal->assetDisposalID, $from);
                        }
                    }
                }
                break;
            default:
                break;
        }
    }

    public function processConsoleJVForAssetDisposal($assetDisposalID, $from) {

        $interCompanyAssetDisposal = InterCompanyAssetDisposal::where('assetDisposalID', $assetDisposalID)->first();

        $assetDisposalMaster = AssetDisposalMaster::with(['disposal_type' => function ($query) {
            $query->with('chartofaccount');
        }])->find($assetDisposalID);

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

            switch ($from) {
                case "AFTER_GRV_VOUCHER":
                    $disposalData = AssetDisposalDetail::selectRaw('SUM(sellingPriceRpt) as sellingPriceRpt,SUM(netBookValueRpt) as netBookValueRpt,serviceLineSystemID,serviceLineCode')
                        ->OfMaster($assetDisposalID)
                        ->groupBy('assetdisposalMasterAutoID','serviceLineSystemID')
                        ->get();

                    if ($disposalData) {
                        foreach ($disposalData as $val) {
                            $difference = $val->sellingPriceRpt - $val->netBookValueRpt;
                            if ($difference != 0) {
                                $consoleJVDetailData['companySystemID'] = $assetDisposalMaster->companySystemID;
                                $consoleJVDetailData['companyID'] = $assetDisposalMaster->companyID;
                                $consoleJVDetailData['serviceLineSystemID'] = $val->serviceLineSystemID;
                                $consoleJVDetailData['serviceLineCode'] = $val->serviceLineCode;

                                $consoleJVDetailData['glAccountSystemID'] = $assetDisposalMaster->disposal_type->chartOfAccountID;
                                $consoleJVDetailData['glAccount'] = $assetDisposalMaster->disposal_type->glCode;
                                $consoleJVDetailData['glAccountDescription'] = ChartOfAccount::getAccountDescription($assetDisposalMaster->disposal_type->chartOfAccountID);

                                if ($difference > 0) {
                                    $consoleJVDetailData['debitAmount'] = ABS($difference);
                                    $conversionAmount = \Helper::convertAmountToLocalRpt(69, $jvMaster->consoleJvMasterAutoId, $consoleJVDetailData['debitAmount']);
                                    $consoleJVDetailData["localDebitAmount"] = $conversionAmount["localAmount"];
                                    $consoleJVDetailData["rptDebitAmount"] = $conversionAmount["reportingAmount"];
                                    $consoleJVDetailData['creditAmount'] = 0;
                                    $consoleJVDetailData['localCreditAmount'] = 0;
                                    $consoleJVDetailData['rptCreditAmount'] = 0;

                                    array_push($finalJvDetailData, $consoleJVDetailData);

                                    $AssetDisposalData = AssetDisposalDetail::with(['item_by.asset_category'])->OfMaster($assetDisposalID)->get();

                                    foreach ($AssetDisposalData as $assetDisposalDetail) {
                                        $difference = $assetDisposalDetail->sellingPriceRpt - $assetDisposalDetail->netBookValueRpt;
                                        if ($difference != 0) {

                                            $consoleJVDetailData['companySystemID'] = $assetDisposalMaster->toCompanySystemID;
                                            $consoleJVDetailData['companyID'] = $assetDisposalMaster->toCompanyID;
                                            $consoleJVDetailData['serviceLineSystemID'] = $assetDisposalDetail->serviceLineSystemID;
                                            $consoleJVDetailData['serviceLineCode'] = $assetDisposalDetail->serviceLineCode;

                                            $consoleJVDetailData['glAccountSystemID'] = $assetDisposalDetail->item_by->asset_category->COSTGLCODESystemID;
                                            $consoleJVDetailData['glAccount'] = $assetDisposalDetail->item_by->asset_category->COSTGLCODE;
                                            $consoleJVDetailData['glAccountDescription'] = ChartOfAccount::getAccountDescription($assetDisposalDetail->item_by->asset_category->COSTGLCODESystemID);

                                            if ($difference > 0) {
                                                $consoleJVDetailData['creditAmount'] = ABS($difference);
                                                $conversionAmount = \Helper::convertAmountToLocalRpt(69, $jvMaster->consoleJvMasterAutoId, $consoleJVDetailData['creditAmount']);
                                                $consoleJVDetailData['localCreditAmount'] = $conversionAmount["localAmount"];
                                                $consoleJVDetailData['rptCreditAmount'] = $conversionAmount["reportingAmount"];
                                                $consoleJVDetailData['debitAmount'] = 0;
                                                $consoleJVDetailData["localDebitAmount"] = 0;
                                                $consoleJVDetailData["rptDebitAmount"] = 0;
                                            }
                                            else {
                                                $consoleJVDetailData['debitAmount'] = ABS($difference);
                                                $conversionAmount = \Helper::convertAmountToLocalRpt(69, $jvMaster->consoleJvMasterAutoId, $consoleJVDetailData['debitAmount']);
                                                $consoleJVDetailData["localDebitAmount"] = $conversionAmount["localAmount"];
                                                $consoleJVDetailData["rptDebitAmount"] = $conversionAmount["reportingAmount"];
                                                $consoleJVDetailData['creditAmount'] = 0;
                                                $consoleJVDetailData['localCreditAmount'] = 0;
                                                $consoleJVDetailData['rptCreditAmount'] = 0;
                                            }

                                            array_push($finalJvDetailData, $consoleJVDetailData);
                                        }
                                    }
                                }
                                else {
                                    $consoleJVDetailData['creditAmount'] = ABS($difference);
                                    $conversionAmount = \Helper::convertAmountToLocalRpt(69, $jvMaster->consoleJvMasterAutoId, $consoleJVDetailData['creditAmount']);
                                    $consoleJVDetailData['localCreditAmount'] = $conversionAmount["localAmount"];
                                    $consoleJVDetailData['rptCreditAmount'] = $conversionAmount["reportingAmount"];
                                    $consoleJVDetailData['debitAmount'] = 0;
                                    $consoleJVDetailData["localDebitAmount"] = 0;
                                    $consoleJVDetailData["rptDebitAmount"] = 0;

                                    array_push($finalJvDetailData, $consoleJVDetailData);

                                    $AssetDisposalData = AssetDisposalDetail::with(['item_by.asset_category'])->OfMaster($assetDisposalID)->get();

                                    foreach ($AssetDisposalData as $assetDisposalDetail) {
                                        $difference = $assetDisposalDetail->sellingPriceRpt - $assetDisposalDetail->netBookValueRpt;
                                        if ($difference != 0) {

                                            $consoleJVDetailData['companySystemID'] = $assetDisposalMaster->toCompanySystemID;
                                            $consoleJVDetailData['companyID'] = $assetDisposalMaster->toCompanyID;
                                            $consoleJVDetailData['serviceLineSystemID'] = $assetDisposalDetail->serviceLineSystemID;
                                            $consoleJVDetailData['serviceLineCode'] = $assetDisposalDetail->serviceLineCode;

                                            $consoleJVDetailData['glAccountSystemID'] = $assetDisposalDetail->item_by->asset_category->COSTGLCODESystemID;
                                            $consoleJVDetailData['glAccount'] = $assetDisposalDetail->item_by->asset_category->COSTGLCODE;
                                            $consoleJVDetailData['glAccountDescription'] = ChartOfAccount::getAccountDescription($assetDisposalDetail->item_by->asset_category->COSTGLCODESystemID);

                                            if ($difference > 0) {
                                                $consoleJVDetailData['creditAmount'] = ABS($difference);
                                                $conversionAmount = \Helper::convertAmountToLocalRpt(69, $jvMaster->consoleJvMasterAutoId, $consoleJVDetailData['creditAmount']);
                                                $consoleJVDetailData['localCreditAmount'] = $conversionAmount["localAmount"];
                                                $consoleJVDetailData['rptCreditAmount'] = $conversionAmount["reportingAmount"];
                                                $consoleJVDetailData['debitAmount'] = 0;
                                                $consoleJVDetailData["localDebitAmount"] = 0;
                                                $consoleJVDetailData["rptDebitAmount"] = 0;
                                            }
                                            else {
                                                $consoleJVDetailData['debitAmount'] = ABS($difference);
                                                $conversionAmount = \Helper::convertAmountToLocalRpt(69, $jvMaster->consoleJvMasterAutoId, $consoleJVDetailData['debitAmount']);
                                                $consoleJVDetailData["localDebitAmount"] = $conversionAmount["localAmount"];
                                                $consoleJVDetailData["rptDebitAmount"] = $conversionAmount["reportingAmount"];
                                                $consoleJVDetailData['creditAmount'] = 0;
                                                $consoleJVDetailData['localCreditAmount'] = 0;
                                                $consoleJVDetailData['rptCreditAmount'] = 0;
                                            }

                                            array_push($finalJvDetailData, $consoleJVDetailData);
                                        }
                                    }
                                }
                            }
                        }
                    }
                    break;
                case "AFTER_CUSTOMER_INVOICE":
                    $transactionAmount = $this->getAccountReceivableLedgerBalance($interCompanyAssetDisposal->customerInvoiceID);

                    $consoleJVDetailData['companySystemID'] = $assetDisposalMaster->companySystemID;
                    $consoleJVDetailData['companyID'] = $assetDisposalMaster->companyID;

                    $customerMaster = CustomerMaster::where('companyLinkedToSystemID', $assetDisposalMaster->toCompanySystemID)->first();
                    if ($customerMaster) {
                        $consoleJVDetailData['glAccountSystemID'] = $customerMaster->custGLAccountSystemID;
                        $consoleJVDetailData['glAccount'] = $customerMaster->custGLaccount;
                        $consoleJVDetailData['glAccountDescription'] = ChartOfAccount::getAccountDescription($customerMaster->custGLAccountSystemID);
                    }

                    $consoleJVDetailData['creditAmount'] = $transactionAmount;
                    $conversionAmount = \Helper::convertAmountToLocalRpt(69, $jvMaster->consoleJvMasterAutoId, $consoleJVDetailData['creditAmount']);
                    $consoleJVDetailData['localCreditAmount'] = $conversionAmount["localAmount"];
                    $consoleJVDetailData['rptCreditAmount'] = $conversionAmount["reportingAmount"];
                    $consoleJVDetailData['debitAmount'] = 0;
                    $consoleJVDetailData["localDebitAmount"] = 0;
                    $consoleJVDetailData["rptDebitAmount"] = 0;

                    array_push($finalJvDetailData, $consoleJVDetailData);

                    $consoleJVDetailData['companySystemID'] = $assetDisposalMaster->toCompanySystemID;
                    $consoleJVDetailData['companyID'] = $assetDisposalMaster->toCompanyID;

                    $supplierMaster = SupplierMaster::where('companyLinkedToSystemID', $assetDisposalMaster->companySystemID)->first();
                    if ($supplierMaster) {
                        $consoleJVDetailData['glAccountSystemID'] = $supplierMaster->liabilityAccountSysemID;
                        $consoleJVDetailData['glAccount'] = $supplierMaster->liabilityAccount;
                        $consoleJVDetailData['glAccountDescription'] = ChartOfAccount::getAccountDescription($supplierMaster->liabilityAccountSysemID);
                    }

                    $consoleJVDetailData['debitAmount'] = $transactionAmount;
                    $conversionAmount = \Helper::convertAmountToLocalRpt(69, $jvMaster->consoleJvMasterAutoId, $consoleJVDetailData['debitAmount']);
                    $consoleJVDetailData["localDebitAmount"] = $conversionAmount["localAmount"];
                    $consoleJVDetailData["rptDebitAmount"] = $conversionAmount["reportingAmount"];
                    $consoleJVDetailData['creditAmount'] = 0;
                    $consoleJVDetailData['localCreditAmount'] = 0;
                    $consoleJVDetailData['rptCreditAmount'] = 0;

                    array_push($finalJvDetailData, $consoleJVDetailData);
                    break;
                case "AFTER_PAYMENT_VOUCHER":
                case "AFTER_RECEIPT_VOUCHER":
                    $transactionAmount = $this->getAccountReceivableLedgerBalance($interCompanyAssetDisposal->customerInvoiceID);

                    $consoleJVDetailData['companySystemID'] = $assetDisposalMaster->toCompanySystemID;
                    $consoleJVDetailData['companyID'] = $assetDisposalMaster->toCompanyID;

                    $supplierMaster = SupplierMaster::where('companyLinkedToSystemID', $assetDisposalMaster->companySystemID)->first();
                    if ($supplierMaster) {
                        $consoleJVDetailData['glAccountSystemID'] = $supplierMaster->liabilityAccountSysemID;
                        $consoleJVDetailData['glAccount'] = $supplierMaster->liabilityAccount;
                        $consoleJVDetailData['glAccountDescription'] = ChartOfAccount::getAccountDescription($supplierMaster->liabilityAccountSysemID);
                    }

                    $consoleJVDetailData['creditAmount'] = $transactionAmount;
                    $conversionAmount = \Helper::convertAmountToLocalRpt(69, $jvMaster->consoleJvMasterAutoId, $consoleJVDetailData['creditAmount']);
                    $consoleJVDetailData['localCreditAmount'] = $conversionAmount["localAmount"];
                    $consoleJVDetailData['rptCreditAmount'] = $conversionAmount["reportingAmount"];
                    $consoleJVDetailData['debitAmount'] = 0;
                    $consoleJVDetailData["localDebitAmount"] = 0;
                    $consoleJVDetailData["rptDebitAmount"] = 0;

                    array_push($finalJvDetailData, $consoleJVDetailData);

                    $consoleJVDetailData['companySystemID'] = $assetDisposalMaster->companySystemID;
                    $consoleJVDetailData['companyID'] = $assetDisposalMaster->companyID;

                    $customerMaster = CustomerMaster::where('companyLinkedToSystemID', $assetDisposalMaster->toCompanySystemID)->first();
                    if ($customerMaster) {
                        $consoleJVDetailData['glAccountSystemID'] = $customerMaster->custGLAccountSystemID;
                        $consoleJVDetailData['glAccount'] = $customerMaster->custGLaccount;
                        $consoleJVDetailData['glAccountDescription'] = ChartOfAccount::getAccountDescription($customerMaster->custGLAccountSystemID);
                    }

                    $consoleJVDetailData['debitAmount'] = $transactionAmount;
                    $conversionAmount = \Helper::convertAmountToLocalRpt(69, $jvMaster->consoleJvMasterAutoId, $consoleJVDetailData['debitAmount']);
                    $consoleJVDetailData["localDebitAmount"] = $conversionAmount["localAmount"];
                    $consoleJVDetailData["rptDebitAmount"] = $conversionAmount["reportingAmount"];
                    $consoleJVDetailData['creditAmount'] = 0;
                    $consoleJVDetailData['localCreditAmount'] = 0;
                    $consoleJVDetailData['rptCreditAmount'] = 0;

                    array_push($finalJvDetailData, $consoleJVDetailData);
                    break;
                default:
                    break;
            }

            if (count($finalJvDetailData) > 0) {
                foreach ($finalJvDetailData as $cData) {
                    ConsoleJVDetail::create($cData);
                }
            }
        }
    }
}
