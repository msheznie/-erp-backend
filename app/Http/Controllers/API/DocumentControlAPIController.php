<?php

/**
 * =============================================
 * -- File Name : DocumentControlAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Document Control
 * -- Author : Fayas
 * -- Create date : 13-December
 * -- Description : This file contains the all the report generation
 * -- REVISION HISTORY
 * -- Date: 13-December 2018 By: Fayas Description: Added new functions named as getDocumentControlFilterFormData(),generateDocumentControlReport()
 */
namespace App\Http\Controllers\API;

use App\Http\Controllers\AppBaseController;
use App\Models\CompanyFinanceYear;
use App\Models\DocumentMaster;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DocumentControlAPIController extends AppBaseController
{
    public function getDocumentControlFilterFormData(Request $request)
    {
        $selectedCompanyId = $request['selectedCompanyId'];
        $companiesByGroup = "";
        if (\Helper::checkIsCompanyGroup($selectedCompanyId)) {
            $companiesByGroup = \Helper::getGroupCompany($selectedCompanyId);
        } else {
            $companiesByGroup = (array)$selectedCompanyId;
        }

        $listOfDocuments = [3,7,8,10,12,13,24,61,4,11,15,19,20,21,17 ];
        $documents = DocumentMaster::whereIn('documentSystemID',$listOfDocuments)->get();

        $years = CompanyFinanceYear::select(DB::raw("companyFinanceYearID,DATE_FORMAT(bigginingDate, '%Y') as financeYear"))
                                     ->where('companySystemID', '=', $selectedCompanyId)
                                     ->orderby('companyFinanceYearID', 'desc')->get();

        $output = array(
            'documents' => $documents,
            'years' => $years,
        );

        return $this->sendResponse($output, trans('custom.record_retrieved_successfully_1'));
    }

    public function generateDocumentControlReport(Request $request)
    {

        $validator = \Validator::make($request->all(), [
            'yearID' => 'required',
            'documents' => 'required',
            'companySystemID' => 'required',
            'reportTypeID' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        $request = $this->convertArrayToSelectedValue($request,array('yearID','reportTypeID'));

        $selectedCompanyId = $request['companySystemID'];
        $documents = (array)$request->documents;
        $financialYearID = $request->yearID;
        $companiesByGroup = "";
        if (\Helper::checkIsCompanyGroup($selectedCompanyId)) {
            $companiesByGroup = \Helper::getGroupCompany($selectedCompanyId);
        } else {
            $companiesByGroup = (array)$selectedCompanyId;
        }
        $finalArray = array();
        foreach ($documents as $document){
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
                    $documentArray["documentExist"] = 1;
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
                    //Log::info('Document ID Not Found' . date('H:i:s'));
            }

            if($documentArray['documentExist'] == 1) {
                $nameSpacedModel = 'App\Models\\' . $documentArray["modelName"];
                if ($request['reportTypeID'] == 1) {
                    $dataRange = array();
                    $finalRange = array();
                    $previousDoc = null;
                    $listOfDoc = $nameSpacedModel::where('companySystemID', $selectedCompanyId)
                        ->where('documentSystemID', $document['documentSystemID'])
                        ->where($documentArray['companyFinanceYearID'], $financialYearID)
                        ->selectRaw($documentArray["primaryKey"] . "," . $documentArray['documentCodeColumnName'] . ",RIGHT(" . $documentArray['documentCodeColumnName'] . ",6) as 'serialNo'")
                        ->orderBy($documentArray['documentCodeColumnName'], 'ASC')
                        ->get();

                    $count = 0;
                    $calTotal = 0;
                    $totalCount = count($listOfDoc);
                    foreach ($listOfDoc as $doc) {

                        if ($count == 0 && !$previousDoc) {
                            $dataRange['start'] = $doc[$documentArray['documentCodeColumnName']];
                            $dataRange['start_serialNo'] = $doc['serialNo'];
                        }

                        $count = $count + 1;
                        if ($previousDoc) {
                            if ((((int)$doc['serialNo']) - ((int)$previousDoc['serialNo'])) != 1) {
                                $dataRange['end'] = $previousDoc[$documentArray['documentCodeColumnName']];
                                $dataRange['end_serialNo'] = $previousDoc['serialNo'];
                                $dataRange['count'] = $count - 1;
                                array_push($finalRange, $dataRange);
                                $calTotal = $calTotal + $dataRange['count'];
                                $count = 1;
                                $dataRange = array();
                                $dataRange['start'] = $doc[$documentArray['documentCodeColumnName']];
                                $dataRange['start_serialNo'] = $doc['serialNo'];
                            }
                        }
                        $previousDoc = $doc;
                        $totalCount = $totalCount - 1;

                        if ($totalCount == 0) {
                            $dataRange['end'] = $previousDoc[$documentArray['documentCodeColumnName']];
                            $dataRange['end_serialNo'] = $previousDoc['serialNo'];
                            $dataRange['count'] = $count;
                            $calTotal = $calTotal + $dataRange['count'];
                            array_push($finalRange, $dataRange);
                        }
                    }

                    $temArray = array('document' => $document,
                        'analysisData' => $finalRange,
                        'total' => count($listOfDoc),
                        'calTotal' => $calTotal);
                    array_push($finalArray, $temArray);
                }else if($request['reportTypeID'] == 2){
                    $finalRange = $nameSpacedModel::where('companySystemID', $selectedCompanyId)
                        ->where('documentSystemID', $document['documentSystemID'])
                        ->where($documentArray['companyFinanceYearID'], $financialYearID)
                        ->selectRaw($documentArray["primaryKey"] . "," . $documentArray['documentCodeColumnName']." as documentCode,COUNT(".$documentArray['documentCodeColumnName'].") as count")
                        ->groupBy($documentArray['documentCodeColumnName'])
                        ->orderBy($documentArray['documentCodeColumnName'], 'ASC')
                        ->havingRaw('count > 1')
                        ->get();

                    $temArray = array('document' => $document,
                        'analysisData' => $finalRange);
                    array_push($finalArray, $temArray);
                }
            }
        }
        return $this->sendResponse($finalArray, trans('custom.successfully_generated_report'));
    }

}
