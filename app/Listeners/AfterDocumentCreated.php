<?php

namespace App\Listeners;

use App\Models\Alert;
use App\Models\DocumentMaster;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class AfterDocumentCreated
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object $event
     * @return void
     */
    public function handle($event)
    {
        $document = $event->document;

        Log::useFiles(storage_path() . '/logs/after_document_created.log');
        if (!empty($document)) {
            $documentArray = array(
                'modelName' => '',
                'primaryKey' => '',
                'documentCodeColumnName' => '',
                'companyFinanceYearID' => '',
                'documentExist' => 0,
            );

            switch ($document["documentSystemID"]) { // check the document id and set relevant parameters
                case 3: // GRV
                    $documentArray["modelName"] = 'GRVMaster';
                    $documentArray["primaryKey"] = 'grvAutoID';
                    $documentArray["documentCodeColumnName"] = 'grvPrimaryCode';
                    $documentArray["companyFinanceYearID"] = 'companyFinanceYearID';
                    $documentArray['documentExist'] = 1;
                    break;
                case 7: // stock adjustment
                    $documentArray["modelName"] = 'StockAdjustment';
                    $documentArray["primaryKey"] = 'stockAdjustmentAutoID';
                    $documentArray["documentCodeColumnName"] = 'stockAdjustmentCode';
                    $documentArray["companyFinanceYearID"] = 'companyFinanceYearID';
                    $documentArray["documentExist"] = 1;
                    break;
                case 8: // material issue
                    $documentArray["modelName"] = 'ItemIssueMaster';
                    $documentArray["primaryKey"] = 'itemIssueAutoID';
                    $documentArray["documentCodeColumnName"] = 'itemIssueCode';
                    $documentArray["companyFinanceYearID"] = 'companyFinanceYearID';
                    $documentArray['documentExist'] = 1;
                    break;
                case 10: // stock receive
                    $documentArray["modelName"] = 'StockReceive';
                    $documentArray["primaryKey"] = 'stockReceiveAutoID';
                    $documentArray["documentCodeColumnName"] = 'stockReceiveCode';
                    $documentArray["companyFinanceYearID"] = 'companyFinanceYearID';
                    $documentArray["documentExist"] = 1;
                    break;
                case 12: // stock return
                    $documentArray["modelName"] = 'ItemReturnMaster';
                    $documentArray["primaryKey"] = 'itemReturnAutoID';
                    $documentArray["documentCodeColumnName"] = 'itemReturnCode';
                    $documentArray["companyFinanceYearID"] = 'companyFinanceYearID';
                    $documentArray["documentExist"] = 1;
                    break;
                case 13: // stock transfer
                    $documentArray["modelName"] = 'StockTransfer';
                    $documentArray["primaryKey"] = 'stockTransferAutoID';
                    $documentArray["documentCodeColumnName"] = 'stockTransferCode';
                    $documentArray["companyFinanceYearID"] = 'companyFinanceYearID';
                    $documentArray["documentExist"] = 1;
                    break;
                case 24: // purchase return
                    $documentArray["modelName"] = 'PurchaseReturn';
                    $documentArray["primaryKey"] = 'purhaseReturnAutoID';
                    $documentArray["documentCodeColumnName"] = 'purchaseReturnCode';
                    $documentArray["companyFinanceYearID"] = 'companyFinanceYearID';
                    $documentArray["documentExist"] = 1;
                    break;
                case 61: // Inventory reclassification
                    $documentArray["modelName"] = 'InventoryReclassification';
                    $documentArray["primaryKey"] = 'inventoryreclassificationID';
                    $documentArray["documentCodeColumnName"] = 'documentCode';
                    $documentArray["companyFinanceYearID"] = 'companyFinanceYearID';
                    $documentArray["documentExist"] = 1;
                    break;
                case 4: // Payment voucher
                    $documentArray["modelName"] = 'PaySupplierInvoiceMaster';
                    $documentArray["primaryKey"] = 'PayMasterAutoId';
                    $documentArray["documentCodeColumnName"] = 'BPVcode';
                    $documentArray["companyFinanceYearID"] = 'companyFinanceYearID';
                    if (array_key_exists('BPVcode', $document)) {
                        $documentArray["documentExist"] = 1;
                    } else {
                        $documentArray["documentExist"] = 0;
                    }
                    break;
                case 11: // supplier invoice
                    $documentArray["modelName"] = 'BookInvSuppMaster';
                    $documentArray["primaryKey"] = 'bookingSuppMasInvAutoID';
                    $documentArray["documentCodeColumnName"] = 'bookingInvCode';
                    $documentArray["companyFinanceYearID"] = 'companyFinanceYearID';
                    $documentArray["documentExist"] = 1;
                    break;
                case 15: // debit note
                    $documentArray["modelName"] = 'DebitNote';
                    $documentArray["primaryKey"] = 'debitNoteAutoID';
                    $documentArray["documentCodeColumnName"] = 'debitNoteCode';
                    $documentArray["companyFinanceYearID"] = 'companyFinanceYearID';
                    $documentArray["documentExist"] = 1;
                    break;
                case 19: // credit note
                    $documentArray["modelName"] = 'CreditNote';
                    $documentArray["primaryKey"] = 'creditNoteAutoID';
                    $documentArray["documentCodeColumnName"] = 'creditNoteCode';
                    $documentArray["companyFinanceYearID"] = 'companyFinanceYearID';
                    $documentArray["documentExist"] = 1;
                    break;
                case 20: // customer invoice
                    $documentArray["modelName"] = 'CustomerInvoiceDirect';
                    $documentArray["primaryKey"] = 'custInvoiceDirectAutoID';
                    $documentArray["documentCodeColumnName"] = 'bookingInvCode';
                    $documentArray["companyFinanceYearID"] = 'companyFinanceYearID';
                    $documentArray["documentExist"] = 1;
                    break;
                case 21: // Bank Receipt Voucher
                    $documentArray["modelName"] = 'CustomerReceivePayment';
                    $documentArray["primaryKey"] = 'custReceivePaymentAutoID';
                    $documentArray["documentCodeColumnName"] = 'custPaymentReceiveCode';
                    $documentArray["companyFinanceYearID"] = 'companyFinanceYearID';
                    $documentArray["documentExist"] = 1;
                    break;
                case 17: // Journal Voucher
                    $documentArray["modelName"] = 'JvMaster';
                    $documentArray["primaryKey"] = 'jvMasterAutoId';
                    $documentArray["documentCodeColumnName"] = 'JVcode';
                    $documentArray["companyFinanceYearID"] = 'companyFinanceYearID';
                    $documentArray["documentExist"] = 1;
                    break;
                default:
                    Log::info('Document ID Not Found' . date('H:i:s'));
            }


            if ($documentArray['documentExist'] == 1) {
                $nameSpacedModel = 'App\Models\\' . $documentArray["modelName"];
                $document = $document->toArray();
                $missingRecodes = array();
                $range = "";
                $previousDoc = $nameSpacedModel::where('companySystemID', $document['companySystemID'])
                    ->where('documentSystemID', $document['documentSystemID'])
                    ->where($documentArray["primaryKey"], '!=', $document[$documentArray["primaryKey"]])
                    ->where($documentArray['companyFinanceYearID'], $document[$documentArray['companyFinanceYearID']])
                    ->selectRaw($documentArray["primaryKey"] . "," . $documentArray['documentCodeColumnName'] . ",RIGHT(" . $documentArray['documentCodeColumnName'] . ",6) as 'serialNo'")
                    ->orderBy($documentArray['documentCodeColumnName'], 'desc')
                    ->first();



                if (!empty($previousDoc)) {
                    $different = (((int)substr($document[$documentArray["documentCodeColumnName"]], -6)) - ((int)$previousDoc['serialNo']));

                    if ($different != 1) {

                        array_push($missingRecodes, array('start' => $previousDoc[$documentArray['documentCodeColumnName']], 'end' => $document[$documentArray['documentCodeColumnName']]));

                        if ($different != 0) {
                            $range = $range . '<br> This document is getting jumped from ' . $previousDoc[$documentArray['documentCodeColumnName']] . ' to ' . $document[$documentArray['documentCodeColumnName']];
                        } else {
                            $range = $range . '<br> This document is getting duplicated ' . $document[$documentArray['documentCodeColumnName']];
                        }
                    }
                }
       
                if ($range) {

                    $footer = "<font size='1.5'><i><p><br><br><br>SAVE PAPER - THINK BEFORE YOU PRINT!" . "<br>This is an auto generated email. Please do not reply to this email because we are not" . "monitoring this inbox.</font>";
                    $email_id = 'gearssupport@pbs-int.net';
                    $empName = 'Admin';
                    $employeeSystemID = 11;
                    $empID = '8888';

                    $systemDocument = DocumentMaster::find($document["documentSystemID"]);

                    $dataEmail = array();
                    $dataEmail['empName'] = $empName;
                    $dataEmail['empEmail'] = $email_id;
                    $dataEmail['empSystemID'] = $employeeSystemID;
                    $dataEmail['empID'] = $empID;
                    $dataEmail['companySystemID'] = $document['companySystemID'];
                    $dataEmail['companyID'] = $document['companyID'];
                    $dataEmail['docID'] = $systemDocument->documentID;
                    $dataEmail['docSystemID'] = $document["documentSystemID"];
                    $dataEmail['docSystemCode'] = null;
                    $dataEmail['docApprovedYN'] = 0;
                    $dataEmail['docCode'] = null;
                    $dataEmail['ccEmailID'] = $email_id;

                    $temp = "Following document is jumped/duplicated for " . $systemDocument->documentDescription . " - " . $document['companyID'] . "<p>" . $range . "<p>" . $footer;

                    $dataEmail['isEmailSend'] = 0;
                    $dataEmail['attachmentFileName'] = null;
                    $dataEmail['alertMessage'] = $systemDocument->documentDescription . " - " . $document['companyID'] . " (Document code Jumped/Duplicated)";
                    $dataEmail['emailAlertMessage'] = $temp;

                    $sendEmail = \Email::sendEmailErp($dataEmail);

                }
            }

        } else {
            Log::info('Document Not Found' . date('H:i:s'));
        }
    }

}
