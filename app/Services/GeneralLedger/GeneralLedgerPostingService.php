<?php

namespace App\Services\GeneralLedger;

use App\helper\TaxService;
use App\Models\AdvancePaymentDetails;
use App\Models\AdvanceReceiptDetails;
use App\Models\AssetCapitalization;
use App\Models\AssetDisposalDetail;
use App\Models\AssetDisposalMaster;
use App\Models\BookInvSuppDet;
use App\Models\BookInvSuppMaster;
use App\Models\CreditNote;
use App\Models\CreditNoteDetails;
use App\Models\CurrencyConversion;
use App\Models\StockCount;
use App\Models\StockCountDetail;
use App\Models\CustomerInvoiceItemDetails;
use App\Models\CustomerMaster;
use App\Models\CustomerReceivePayment;
use App\Models\CustomerReceivePaymentDetail;
use App\Models\DebitNote;
use App\Models\DebitNoteDetails;
use App\Models\DeliveryOrder;
use App\Models\DeliveryOrderDetail;
use App\Models\DirectInvoiceDetails;
use App\Models\DirectPaymentDetails;
use App\Models\DirectReceiptDetail;
use App\Models\Employee;
use App\Models\FixedAssetDepreciationMaster;
use App\Models\FixedAssetDepreciationPeriod;
use App\Models\FixedAssetMaster;
use App\Models\PurchaseReturnLogistic;
use App\Models\GeneralLedger;
use App\Models\GRVDetails;
use App\Models\GRVMaster;
use App\Models\InventoryReclassification;
use App\Models\InventoryReclassificationDetail;
use App\Models\ItemIssueDetails;
use App\Models\ItemIssueMaster;
use App\Models\ItemReturnDetails;
use App\Models\ItemReturnMaster;
use App\Models\JvDetail;
use App\Models\JvMaster;
use App\Models\PaySupplierInvoiceDetail;
use App\Models\PaySupplierInvoiceMaster;
use App\Models\PoAdvancePayment;
use App\Models\PurchaseReturn;
use App\Models\PurchaseReturnDetails;
use App\Models\SegmentMaster;
use App\Models\StockAdjustment;
use App\Models\StockAdjustmentDetails;
use App\Models\StockReceive;
use App\Models\StockReceiveDetails;
use App\Models\StockTransfer;
use App\Models\StockTransferDetails;
use App\Models\CustomerInvoiceDirect;
use App\Models\CustomerInvoiceDirectDetail;
use App\Models\Taxdetail;
use App\Models\SupplierInvoiceDirectItem;
use App\Models\Company;
use App\Models\SupplierAssigned;
use App\Models\ChartOfAccountsAssigned;
use App\Models\ChartOfAccount;
use App\Models\SalesReturn;
use App\Models\SystemGlCodeScenarioDetail;
use App\Models\SalesReturnDetail;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Jobs\UnbilledGRVInsert;
use App\Jobs\TaxLedgerInsert;

class GeneralLedgerPostingService
{
	public static function postGeneralLedgerData($masterModel, $finalData, $taxLedgerData, $dataBase)
	{
        if ($finalData) {
            if (in_array($masterModel["documentSystemID"], [3, 8, 12, 13, 10, 20, 61, 24, 7, 19, 15, 11, 4, 21, 22, 17, 23, 41, 71, 87, 97])) { // already GL entry passed Check
                $outputGL = GeneralLedger::where('documentSystemCode', $masterModel["autoID"])->where('documentSystemID', $masterModel["documentSystemID"])->first();
                if ($outputGL) {
                    return ['status' => true];
                }
            }

            foreach ($finalData as $data) {
                GeneralLedger::create($data);
            }
            $generalLedgerInsert = true;

            if ($generalLedgerInsert) {
                // getting general ledger document date
                $glDocumentDate = GeneralLedger::selectRaw('documentDate')
                    ->where('documentSystemID', $masterModel["documentSystemID"])
                    ->where('companySystemID', $masterModel["companySystemID"])
                    ->where('documentSystemCode', $masterModel["autoID"])
                    ->first();

                switch ($masterModel["documentSystemID"]) {
                    case 11: // Supplier Invoice
                        $documentUpdateData = BookInvSuppMaster::find($masterModel["autoID"]);

                        if ($glDocumentDate) {
                            $documentUpdateData->postedDate = $glDocumentDate->documentDate;
                            $documentUpdateData->save();
                        }
                        break;
                    case 15: // Debit note
                        $documentUpdateData = DebitNote::find($masterModel["autoID"]);

                        if ($glDocumentDate) {
                            $documentUpdateData->postedDate = $glDocumentDate->documentDate;
                            $documentUpdateData->save();
                        }
                        break;
                    case 4: // Payment Voucher
                        $documentUpdateData = PaySupplierInvoiceMaster::find($masterModel["autoID"]);

                        if ($glDocumentDate) {
                            $documentUpdateData->postedDate = $glDocumentDate->documentDate;
                            $documentUpdateData->save();
                        }
                        break;
                    case 20: // Customer Invoice
                        $documentUpdateData = CustomerInvoiceDirect::find($masterModel["autoID"]);

                        if ($glDocumentDate) {
                            $documentUpdateData->postedDate = $glDocumentDate->documentDate;
                            $documentUpdateData->save();
                        }
                        break;
                    case 19: // Credit Note
                        $documentUpdateData = CreditNote::find($masterModel["autoID"]);

                        if ($glDocumentDate) {
                            $documentUpdateData->postedDate = $glDocumentDate->documentDate;
                            $documentUpdateData->save();
                        }
                        break;
                    case 21: //  Customer Receipt Voucher
                        $documentUpdateData = CustomerReceivePayment::find($masterModel["autoID"]);

                        if ($glDocumentDate) {
                            $documentUpdateData->postedDate = $glDocumentDate->documentDate;
                            $documentUpdateData->save();
                        }
                        break;
                    case 17: //  Journal Voucher
                        $documentUpdateData = JvMaster::find($masterModel["autoID"]);

                        if ($glDocumentDate) {
                            $documentUpdateData->postedDate = $glDocumentDate->documentDate;
                            $documentUpdateData->save();
                        }
                        break;
                    case 10: //  Stock Receive
                        $documentUpdateData = StockReceive::find($masterModel["autoID"]);

                        if ($glDocumentDate) {
                            $documentUpdateData->postedDate = $glDocumentDate->documentDate;
                            $documentUpdateData->save();
                        }
                        break;
                    case 13: //  Stock Transfer
                        $documentUpdateData = StockTransfer::find($masterModel["autoID"]);

                        if ($glDocumentDate) {
                            $documentUpdateData->postedDate = $glDocumentDate->documentDate;
                            $documentUpdateData->save();
                        }
                        break;
                    case 22: //  Fixed Asset
                        $documentUpdateData = FixedAssetMaster::find($masterModel["autoID"]);

                        if ($glDocumentDate) {
                            $documentUpdateData->postedDate = $glDocumentDate->documentDate;
                            $documentUpdateData->save();
                        }
                        break;

                    case 71: // Delivery Order
                        $documentUpdateData = DeliveryOrder::find($masterModel["autoID"]);

                        if ($glDocumentDate) {
                            $documentUpdateData->postedDate = $glDocumentDate->documentDate;
                            $documentUpdateData->save();
                        }
                        break;

                    case 8: // Material Issue
                        $documentUpdateData = ItemIssueMaster::find($masterModel["autoID"]);

                        if ($glDocumentDate) {
                            $documentUpdateData->postedDate = $glDocumentDate->documentDate;
                            $documentUpdateData->save();
                        }
                        break;

                    case 3: // Good Receipt Voucher
                        $documentUpdateData = GRVMaster::find($masterModel["autoID"]);

                        if ($glDocumentDate) {
                            $documentUpdateData->postedDate = $glDocumentDate->documentDate;
                            $documentUpdateData->save();
                        }
                        break;

                    default:
                        Log::warning('Posted date document id not found ' . date('H:i:s'));
                }
                if (in_array($masterModel["documentSystemID"], [15, 11, 4, 24])) {

                    if ($masterModel["documentSystemID"] == 24) {
                        $prData = PurchaseReturn::find($masterModel["autoID"]);
                        if ($prData->isInvoiceCreatedForGrv == 1) {
                            $apLedgerInsert = \App\Jobs\AccountPayableLedgerInsert::dispatch($masterModel, $dataBase);
                        } else {
                            $prnDetails = PurchaseReturnDetails::where('purhaseReturnAutoID', $masterModel["autoID"])->first();
                            $unbilledModel['supplierID'] = $prData->supplierID;
                            $unbilledModel['forPrn'] = true;
                            $unbilledModel['purhaseReturnAutoID'] = $prData->purhaseReturnAutoID;
                            $unbilledModel['autoID'] = $prnDetails->grvAutoID;
                            $unbilledModel['companySystemID'] = $prData->companySystemID;
                            $unbilledModel['documentSystemID'] = $masterModel['documentSystemID'];
                            $jobUGRV = UnbilledGRVInsert::dispatch($unbilledModel, $dataBase);
                        }
                    } else if ($masterModel["documentSystemID"] == 11) {
                        $suppInvData = BookInvSuppMaster::find($masterModel["autoID"]);
                        if ($suppInvData->documentType == 4) {
                            $apLedgerInsert = \App\Jobs\EmployeeLedgerInsert::dispatch($masterModel, $dataBase);
                        } else {
                            $apLedgerInsert = \App\Jobs\AccountPayableLedgerInsert::dispatch($masterModel, $dataBase);
                        }
                    } else if ($masterModel["documentSystemID"] == 15) {
                        $debitNoteData = DebitNote::find($masterModel["autoID"]);
                        if ($debitNoteData->type == 2) {
                            $apLedgerInsert = \App\Jobs\EmployeeLedgerInsert::dispatch($masterModel,$dataBase);
                        } else {
                            $apLedgerInsert = \App\Jobs\AccountPayableLedgerInsert::dispatch($masterModel, $dataBase);
                        }
                    } else if ($masterModel["documentSystemID"] == 4) {
                        $suppInvData = PaySupplierInvoiceMaster::find($masterModel["autoID"]);
                        if ($suppInvData->invoiceType == 6 || $suppInvData->invoiceType == 7) {
                            $apLedgerInsert = \App\Jobs\EmployeeLedgerInsert::dispatch($masterModel, $dataBase);
                        } else {
                            if ($suppInvData->invoiceType != 3) {
                                $apLedgerInsert = \App\Jobs\AccountPayableLedgerInsert::dispatch($masterModel, $dataBase);
                            }
                        }
                    } else {
                        $apLedgerInsert = \App\Jobs\AccountPayableLedgerInsert::dispatch($masterModel, $dataBase);
                    }
                }
                if (in_array($masterModel["documentSystemID"], [19, 20, 21, 87])) {
                    if ($masterModel["documentSystemID"] == 87) {
                        $salesReturnDataData = SalesReturn::find($masterModel["autoID"]);
                        if ($salesReturnDataData->returnType != 1) {
                            $arLedgerInsert = \App\Jobs\AccountReceivableLedgerInsert::dispatch($masterModel, $dataBase);
                        }
                    } else {
                        $arLedgerInsert = \App\Jobs\AccountReceivableLedgerInsert::dispatch($masterModel, $dataBase);
                    }
                }


                if (!empty($taxLedgerData)) {
                    $updateVATLedger = TaxLedgerInsert::dispatch($masterModel, $taxLedgerData, $dataBase);
                }
            }
            
            Log::info('---- GL End Successfully -----' . date('H:i:s'));
            return ['status' => true];
        }
	}
}
