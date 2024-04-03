<?php

namespace App\Services;

use App\helper\Helper;
use App\Models\Employee;
use Exception;
use App\Models\ErpItemLedger;
use App\Models\StockCount;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\GeneralLedger\GlPostedDateService;
use App\Models\ItemMaster;
use App\Models\UnitConversion;


class ItemLedgerService
{
	public static function postLedgerEntry($masterModel)
	{
        $validatePostedDate = GlPostedDateService::validatePostedDate($masterModel["autoID"], $masterModel["documentSystemID"]);

        if (!$validatePostedDate['status']) {
            return ['status' => false, 'message' => $validatePostedDate['message']];
        }

        $postedDateGl = $validatePostedDate['postedDate'];

        $docInforArr = array(
            'confirmColumnName' => '',
            'approvedColumnName' => '',
            'modelName' => '',
            'childRelation' => '',
            'autoID' => ''
        );

        $detailColumnArray = array();
        $masterColumnArray = array();

        $empID = Employee::find($masterModel['employeeSystemID']);
        switch ($masterModel["documentSystemID"]) { // check the document id and set relevant parameters
            case 3: // GRV
                $docInforArr["approvedColumnName"] = 'approved';
                $docInforArr["modelName"] = 'GRVMaster';
                $docInforArr["childRelation"] = 'details';
                $docInforArr["autoID"] = 'grvAutoID';
                $docInforArr["approvedYN"] = -1;
                $masterColumnArray = array(
                    'companySystemID' => 'companySystemID',
                    'companyID' => 'companyID',
                    'serviceLineSystemID' => 'serviceLineSystemID',
                    'serviceLineCode' => 'serviceLineCode',
                    'documentSystemID' => 'documentSystemID',
                    'documentID' => 'documentID',
                    'documentCode' => 'grvPrimaryCode',
                    'wareHouseSystemCode' => 'grvLocation',
                    'referenceNumber' => 'grvDoRefNo');

                $detailColumnArray = array(
                    'itemSystemCode' => 'itemCode',
                    'itemPrimaryCode' => 'itemPrimaryCode',
                    'itemDescription' => 'itemDescription',
                    'unitOfMeasure' => 'unitOfMeasure',
                    'inOutQty' => 'noQty',
                    'wacLocalCurrencyID' => 'localCurrencyID',
                    'wacLocal' => 'landingCost_LocalCur',
                    'wacRptCurrencyID' => 'companyReportingCurrencyID',
                    'wacRpt' => 'landingCost_RptCur',
                    'comments' => 'comment');

                break;
            case 8: // Material Issue
                $docInforArr["approvedColumnName"] = 'approved';
                $docInforArr["modelName"] = 'ItemIssueMaster';
                $docInforArr["childRelation"] = 'details';
                $docInforArr["autoID"] = 'itemIssueAutoID';
                $docInforArr["approvedYN"] = -1;
                $masterColumnArray = array(
                    'companySystemID' => 'companySystemID',
                    'companyID' => 'companyID',
                    'serviceLineSystemID' => 'serviceLineSystemID',
                    'serviceLineCode' => 'serviceLineCode',
                    'documentSystemID' => 'documentSystemID',
                    'documentID' => 'documentID',
                    'documentCode' => 'itemIssueCode',
                    'wareHouseSystemCode' => 'wareHouseFrom',
                    'referenceNumber' => 'issueRefNo');

                $detailColumnArray = array(
                    'itemSystemCode' => 'itemCodeSystem',
                    'itemPrimaryCode' => 'itemPrimaryCode',
                    'itemDescription' => 'itemDescription',
                    'unitOfMeasure' => 'itemUnitOfMeasure',
                    'inOutQty' => 'qtyIssuedDefaultMeasure',
                    'wacLocalCurrencyID' => 'localCurrencyID',
                    'wacLocal' => 'issueCostLocal',
                    'wacRptCurrencyID' => 'reportingCurrencyID',
                    'wacRpt' => 'issueCostRpt',
                    'comments' => 'comments');

                break;
            case 12: //Material Return
                $docInforArr["approvedColumnName"] = 'approved';
                $docInforArr["modelName"] = 'ItemReturnMaster';
                $docInforArr["childRelation"] = 'details';
                $docInforArr["autoID"] = 'itemReturnAutoID';
                $docInforArr["approvedYN"] = -1;
                $masterColumnArray = array(
                    'companySystemID' => 'companySystemID',
                    'companyID' => 'companyID',
                    'serviceLineSystemID' => 'serviceLineSystemID',
                    'serviceLineCode' => 'serviceLineCode',
                    'documentSystemID' => 'documentSystemID',
                    'documentID' => 'documentID',
                    'documentCode' => 'itemReturnCode',
                    'wareHouseSystemCode' => 'wareHouseLocation',
                    'referenceNumber' => 'ReturnRefNo');

                $detailColumnArray = array(
                    'itemSystemCode' => 'itemCodeSystem',
                    'itemPrimaryCode' => 'itemPrimaryCode',
                    'itemDescription' => 'itemDescription',
                    'unitOfMeasure' => 'itemUnitOfMeasure',
                    'inOutQty' => 'qtyIssuedDefaultMeasure',
                    'wacLocalCurrencyID' => 'localCurrencyID',
                    'wacLocal' => 'unitCostLocal',
                    'wacRptCurrencyID' => 'reportingCurrencyID',
                    'wacRpt' => 'unitCostRpt',
                    'comments' => 'comments');

                break;
            case 13: //Stock Transfer
                $docInforArr["approvedColumnName"] = 'approved';
                $docInforArr["modelName"] = 'StockTransfer';
                $docInforArr["childRelation"] = 'details';
                $docInforArr["autoID"] = 'stockTransferAutoID';
                $docInforArr["approvedYN"] = -1;
                $masterColumnArray = array(
                    'companySystemID' => 'companySystemID',
                    'companyID' => 'companyID',
                    'serviceLineSystemID' => 'serviceLineSystemID',
                    'serviceLineCode' => 'serviceLineCode',
                    'documentSystemID' => 'documentSystemID',
                    'documentID' => 'documentID',
                    'documentCode' => 'stockTransferCode',
                    'wareHouseSystemCode' => 'locationFrom',
                    'referenceNumber' => 'refNo');

                $detailColumnArray = array(
                    'itemSystemCode' => 'itemCodeSystem',
                    'itemPrimaryCode' => 'itemPrimaryCode',
                    'itemDescription' => 'itemDescription',
                    'unitOfMeasure' => 'unitOfMeasure',
                    'inOutQty' => 'qty',
                    'wacLocalCurrencyID' => 'localCurrencyID',
                    'wacLocal' => 'unitCostLocal',
                    'wacRptCurrencyID' => 'reportingCurrencyID',
                    'wacRpt' => 'unitCostRpt',
                    'comments' => 'comments');
                break;
            case 10: //Stock Receive
                $docInforArr["approvedColumnName"] = 'approved';
                $docInforArr["modelName"] = 'StockReceive';
                $docInforArr["childRelation"] = 'details';
                $docInforArr["autoID"] = 'stockReceiveAutoID';
                $docInforArr["approvedYN"] = -1;
                $masterColumnArray = array(
                    'companySystemID' => 'companySystemID',
                    'companyID' => 'companyID',
                    'serviceLineSystemID' => 'serviceLineSystemID',
                    'serviceLineCode' => 'serviceLineCode',
                    'documentSystemID' => 'documentSystemID',
                    'documentID' => 'documentID',
                    'documentCode' => 'stockReceiveCode',
                    'wareHouseSystemCode' => 'locationTo',
                    'referenceNumber' => 'refNo');

                $detailColumnArray = array(
                    'itemSystemCode' => 'itemCodeSystem',
                    'itemPrimaryCode' => 'itemPrimaryCode',
                    'itemDescription' => 'itemDescription',
                    'unitOfMeasure' => 'unitOfMeasure',
                    'inOutQty' => 'qty',
                    'wacLocalCurrencyID' => 'localCurrencyID',
                    'wacLocal' => 'unitCostLocal',
                    'wacRptCurrencyID' => 'reportingCurrencyID',
                    'wacRpt' => 'unitCostRpt',
                    'comments' => 'comments');
                break;
            case 61: //Inventory Reclassification
                $docInforArr["approvedColumnName"] = 'approved';
                $docInforArr["modelName"] = 'InventoryReclassification';
                $docInforArr["childRelation"] = 'details';
                $docInforArr["autoID"] = 'inventoryreclassificationID';
                $docInforArr["approvedYN"] = -1;
                $masterColumnArray = array(
                    'companySystemID' => 'companySystemID',
                    'companyID' => 'companyID',
                    'serviceLineSystemID' => 'serviceLineSystemID',
                    'serviceLineCode' => 'serviceLineCode',
                    'documentSystemID' => 'documentSystemID',
                    'documentID' => 'documentID',
                    'wareHouseSystemCode' => 'wareHouseSystemCode',
                    'documentCode' => 'documentCode');

                $detailColumnArray = array(
                    'itemSystemCode' => 'itemSystemCode',
                    'itemPrimaryCode' => 'itemPrimaryCode',
                    'itemDescription' => 'itemDescription',
                    'unitOfMeasure' => 'unitOfMeasure',
                    'inOutQty' => 'currentStockQty',
                    'wacLocalCurrencyID' => 'localCurrencyID',
                    'wacLocal' => 'unitCostLocal',
                    'wacRptCurrencyID' => 'reportingCurrencyID',
                    'wacRpt' => 'unitCostRpt');
                break;
            case 20: // Customer Invoice
                $docInforArr["approvedColumnName"] = 'approved';
                $docInforArr["modelName"] = 'CustomerInvoiceDirect';
                $docInforArr["childRelation"] = 'issue_item_details';
                $docInforArr["autoID"] = 'custInvoiceDirectAutoID';
                $docInforArr["approvedYN"] = -1;
                $masterColumnArray = array(
                    'companySystemID' => 'companySystemID',
                    'companyID' => 'companyID',
                    'serviceLineSystemID' => 'serviceLineSystemID',
                    'serviceLineCode' => 'serviceLineCode',
                    'documentSystemID' => 'documentSystemiD',
                    'documentID' => 'documentID',
                    'documentCode' => 'bookingInvCode',
                    'wareHouseSystemCode' => 'wareHouseSystemCode',
                    'referenceNumber' => 'customerInvoiceNo');

                $detailColumnArray = array(
                    'itemSystemCode' => 'itemCodeSystem',
                    'itemPrimaryCode' => 'itemPrimaryCode',
                    'itemDescription' => 'itemDescription',
                    'unitOfMeasure' => 'itemUnitOfMeasure',
                    'inOutQty' => 'qtyIssuedDefaultMeasure',
                    'wacLocalCurrencyID' => 'localCurrencyID',
                    'wacLocal' => 'sellingCostAfterMarginLocal',
                    'wacRptCurrencyID' => 'reportingCurrencyID',
                    'wacRpt' => 'sellingCostAfterMarginRpt',
                    'comments' => 'comments');

                break;
            case 24: // Purchase Return
                $docInforArr["approvedColumnName"] = 'approved';
                $docInforArr["modelName"] = 'PurchaseReturn';
                $docInforArr["childRelation"] = 'details';
                $docInforArr["autoID"] = 'purhaseReturnAutoID';
                $docInforArr["approvedYN"] = -1;
                $masterColumnArray = array(
                    'companySystemID' => 'companySystemID',
                    'companyID' => 'companyID',
                    'serviceLineSystemID' => 'serviceLineSystemID',
                    'serviceLineCode' => 'serviceLineCode',
                    'documentSystemID' => 'documentSystemID',
                    'documentID' => 'documentID',
                    'wareHouseSystemCode' => 'purchaseReturnLocation',
                    'documentCode' => 'purchaseReturnCode',
                    'referenceNumber' => 'purchaseReturnRefNo');

                $detailColumnArray = array(
                    'itemSystemCode' => 'itemCode',
                    'itemPrimaryCode' => 'itemPrimaryCode',
                    'itemDescription' => 'itemDescription',
                    'unitOfMeasure' => 'unitOfMeasure',
                    'inOutQty' => 'noQty',
                    'wacLocalCurrencyID' => 'localCurrencyID',
                    'wacLocal' => 'GRVcostPerUnitLocalCur',
                    'wacRptCurrencyID' => 'companyReportingCurrencyID',
                    'wacRpt' => 'GRVcostPerUnitComRptCur',
                    'comments' => 'comment');
                break;
            case 7: //Stock Adjustment
                $docInforArr["approvedColumnName"] = 'approved';
                $docInforArr["modelName"] = 'StockAdjustment';
                $docInforArr["childRelation"] = 'details';
                $docInforArr["autoID"] = 'stockAdjustmentAutoID';
                $docInforArr["approvedYN"] = -1;
                $masterColumnArray = array(
                    'companySystemID' => 'companySystemID',
                    'companyID' => 'companyID',
                    'serviceLineSystemID' => 'serviceLineSystemID',
                    'serviceLineCode' => 'serviceLineCode',
                    'documentSystemID' => 'documentSystemID',
                    'documentID' => 'documentID',
                    'wareHouseSystemCode' => 'location',
                    'documentCode' => 'stockAdjustmentCode',
                    'referenceNumber' => 'refNo');

                $detailColumnArray = array(
                    'itemSystemCode' => 'itemCodeSystem',
                    'itemPrimaryCode' => 'itemPrimaryCode',
                    'itemDescription' => 'itemDescription',
                    'unitOfMeasure' => 'itemUnitOfMeasure',
                    'inOutQty' => 'noQty',
                    'wacLocalCurrencyID' => 'currentWacLocalCurrencyID',
                    'wacLocal' => 'wacAdjLocal',
                    'wacRptCurrencyID' => 'currentWacRptCurrencyID',
                    'wacRpt' => 'wacAdjRpt');
                break;
            case 71: //Delivery Order
                $docInforArr["approvedColumnName"] = 'approvedYN';
                $docInforArr["modelName"] = 'DeliveryOrder';
                $docInforArr["childRelation"] = 'detail';
                $docInforArr["autoID"] = 'deliveryOrderID';
                $docInforArr["approvedYN"] = -1;
                $masterColumnArray = array(
                    'companySystemID' => 'companySystemID',
                    'companyID' => 'companyID',
                    'serviceLineSystemID' => 'serviceLineSystemID',
                    'serviceLineCode' => 'serviceLineCode',
                    'documentSystemID' => 'documentSystemID',
                    'documentID' => 'documentID',
                    'wareHouseSystemCode' => 'wareHouseSystemCode',
                    'documentCode' => 'deliveryOrderCode',
                    'referenceNumber' => 'referenceNo');

                $detailColumnArray = array(
                    'itemSystemCode' => 'itemCodeSystem',
                    'itemPrimaryCode' => 'itemPrimaryCode',
                    'itemDescription' => 'itemDescription',
                    'unitOfMeasure' => 'itemUnitOfMeasure',
                    'inOutQty' => 'qtyIssuedDefaultMeasure',
                    'wacLocalCurrencyID' => 'companyLocalCurrencyID',
                    'wacLocal' => 'wacValueLocal',
                    'wacRptCurrencyID' => 'companyReportingCurrencyID',
                    'wacRpt' => 'wacValueReporting');
                break;
            case 87: //sales return
                $docInforArr["approvedColumnName"] = 'approvedYN';
                $docInforArr["modelName"] = 'SalesReturn';
                $docInforArr["childRelation"] = 'detail';
                $docInforArr["autoID"] = 'id';
                $docInforArr["approvedYN"] = -1;
                $masterColumnArray = array(
                    'companySystemID' => 'companySystemID',
                    'companyID' => 'companyID',
                    'serviceLineSystemID' => 'serviceLineSystemID',
                    'serviceLineCode' => 'serviceLineCode',
                    'documentSystemID' => 'documentSystemID',
                    'documentID' => 'documentID',
                    'wareHouseSystemCode' => 'wareHouseSystemCode',
                    'documentCode' => 'salesReturnCode',
                    'referenceNumber' => 'referenceNo');

                $detailColumnArray = array(
                    'itemSystemCode' => 'itemCodeSystem',
                    'itemPrimaryCode' => 'itemPrimaryCode',
                    'itemDescription' => 'itemDescription',
                    'unitOfMeasure' => 'itemUnitOfMeasure',
                    'inOutQty' => 'qtyReturnedDefaultMeasure',
                    'wacLocalCurrencyID' => 'companyLocalCurrencyID',
                    'wacLocal' => 'wacValueLocal',
                    'wacRptCurrencyID' => 'companyReportingCurrencyID',
                    'wacRpt' => 'wacValueReporting');
                break;
            case 97: //Stock Count
                $docInforArr["approvedColumnName"] = 'approved';
                $docInforArr["modelName"] = 'StockCount';
                $docInforArr["childRelation"] = 'details';
                $docInforArr["autoID"] = 'stockCountAutoID';
                $docInforArr["approvedYN"] = -1;
                $masterColumnArray = array(
                    'companySystemID' => 'companySystemID',
                    'companyID' => 'companyID',
                    'serviceLineSystemID' => 'serviceLineSystemID',
                    'serviceLineCode' => 'serviceLineCode',
                    'documentSystemID' => 'documentSystemID',
                    'documentID' => 'documentID',
                    'wareHouseSystemCode' => 'location',
                    'documentCode' => 'stockCountCode',
                    'referenceNumber' => 'refNo');

                $detailColumnArray = array(
                    'itemSystemCode' => 'itemCodeSystem',
                    'itemPrimaryCode' => 'itemPrimaryCode',
                    'itemDescription' => 'itemDescription',
                    'unitOfMeasure' => 'itemUnitOfMeasure',
                    'inOutQty' => 'adjustedQty',
                    'wacLocalCurrencyID' => 'currentWacLocalCurrencyID',
                    'wacLocal' => 'wacAdjLocal',
                    'wacRptCurrencyID' => 'currentWacRptCurrencyID',
                    'wacRpt' => 'wacAdjRpt');
                break;
            case 11: // Supplier Invocie
                $docInforArr["approvedColumnName"] = 'approved';
                $docInforArr["modelName"] = 'BookInvSuppMaster';
                $docInforArr["childRelation"] = 'item_details';
                $docInforArr["autoID"] = 'bookingSuppMasInvAutoID';
                $docInforArr["approvedYN"] = -1;
                $masterColumnArray = array(
                    'companySystemID' => 'companySystemID',
                    'companyID' => 'companyID',
                    'serviceLineSystemID' => 'serviceLineSystemID',
                    'serviceLineCode' => '',
                    'documentSystemID' => 'documentSystemID',
                    'documentID' => 'documentID',
                    'documentCode' => 'bookingInvCode',
                    'wareHouseSystemCode' => 'wareHouseSystemCode',
                    'referenceNumber' => 'secondaryRefNo');

                $detailColumnArray = array(
                    'itemSystemCode' => 'itemCode',
                    'itemPrimaryCode' => 'itemPrimaryCode',
                    'itemDescription' => 'itemDescription',
                    'unitOfMeasure' => 'unitOfMeasure',
                    'inOutQty' => 'noQty',
                    'wacLocalCurrencyID' => 'localCurrencyID',
                    'wacLocal' => 'costPerUnitLocalCur',
                    'wacRptCurrencyID' => 'companyReportingCurrencyID',
                    'wacRpt' => 'costPerUnitComRptCur',
                    'comments' => 'comment');

                break;
            default:
                Log::error('Document ID Not Found' . date('H:i:s'));
                exit;
                break;
        }
        $nameSpacedModel = 'App\Models\\' . $docInforArr["modelName"]; // Model name
        $masterRec = $nameSpacedModel::with([$docInforArr["childRelation"] => function($query) use ($masterModel) {
            if($masterModel["documentSystemID"] == 3 || $masterModel["documentSystemID"] == 11){
                $query->where('itemFinanceCategoryID',1);
            }
            if($masterModel["documentSystemID"] == 20 || $masterModel["documentSystemID"] == 71){
                $query->where('itemFinanceCategoryID',1);   // Only Inventory Item
            }
        }])
            ->where($docInforArr["approvedColumnName"],$docInforArr["approvedYN"])
            ->when($masterModel["documentSystemID"] == 20, function ($q) {
                return $q->where('isPerforma','!=',3);
            })
            ->find($masterModel["autoID"]);
        if ($masterRec) {
            if ($masterRec[$docInforArr["childRelation"]]) {
                $data = [];
                $i = 0;
                if($masterModel["documentSystemID"] == 87) {
                    foreach ($masterRec[$docInforArr["childRelation"]] as $detail) {
                        if($detail->isPostItemLedger == 1) {

                            foreach ($detailColumnArray as $column => $value) {
                                if ($column == 'inOutQty') {
                                    if ($masterModel["documentSystemID"] == 3 || $masterModel["documentSystemID"] == 12 || $masterModel["documentSystemID"] == 10 || $masterModel["documentSystemID"] == 87 || $masterModel["documentSystemID"] == 11) {
                                        $data[$i][$column] = ABS($detail[$value]); // make qty always plus
                                    } else if ($masterModel["documentSystemID"] == 8 || $masterModel["documentSystemID"] == 13 || $masterModel["documentSystemID"] == 61 || $masterModel["documentSystemID"] == 24 || $masterModel["documentSystemID"] == 20 || $masterModel["documentSystemID"] == 71) {
                                        $data[$i][$column] = ABS($detail[$value]) * -1; // make qty always minus
                                    } else if ($masterModel["documentSystemID"] == 7) {    // stock adjustment
                                        if ($masterRec['stockAdjustmentType'] == 2) {       // cost adjustment
                                            $data[$i][$column] = 1;
                                            Log::info('qty is' . $data[$i][$column]);
                                        } else {
                                            $data[$i][$column] = $detail[$value];
                                        }
                                    } else if ($masterModel["documentSystemID"] == 97) {    // stock count
                                        $stockCountWacData = array('companySystemID' => $masterRec['companySystemID'],
                                            'itemCodeSystem' => $detail['itemCodeSystem'],
                                            'wareHouseId' => $masterRec['location']);

                                        $itemCurrentCostAndQty = \Inventory::itemCurrentCostAndQty($stockCountWacData);

                                        if ($masterRec['stockCountType'] == 2) {       // cost count
                                            $data[$i][$column] = 1;
                                            Log::info('qty is' . $data[$i][$column]);
                                        } else {
                                            $data[$i][$column] = $detail['noQty'] - $itemCurrentCostAndQty['currentWareHouseStockQty'];
                                        }
                                    } else {
                                        $data[$i][$column] = $detail[$value];
                                    }
                                } else if ($column == 'wacLocal') {
                                    if ($masterModel["documentSystemID"] == 7 && $masterRec['stockAdjustmentType'] == 2) { // stock adjustment, cost adjustment
                                        $data[$i][$column] = (($detail['wacAdjLocal']) - ($detail['currentWaclocal'])) * $detail['currenctStockQty'];

                                    } elseif ($masterModel["documentSystemID"] == 20 && ($masterRec["isPerforma"] == 2 || $masterRec["isPerforma"] == 4 || $masterRec["isPerforma"] == 5)) {
                                        $data[$i][$column] = $detail['issueCostLocal'];
                                    } else if ($masterModel["documentSystemID"] == 97) {    // stock count
                                        $stockCountWacData = array('companySystemID' => $masterRec['companySystemID'],
                                            'itemCodeSystem' => $detail['itemCodeSystem'],
                                            'wareHouseId' => $masterRec['location']);

                                        $itemCurrentCostAndQty = \Inventory::itemCurrentCostAndQty($stockCountWacData);

                                        $companyCurrencyConversion = \Helper::currencyConversion($masterRec['companySystemID'], $detail['wacValueReportingCurrencyID'], $detail['wacValueReportingCurrencyID'], $itemCurrentCostAndQty['wacValueReporting']);

                                        $data[$i][$column] = $companyCurrencyConversion['localAmount'];
                                    } else {
                                        $data[$i][$column] = $detail[$value];
                                    }
                                } else if ($column == 'wacRpt') {
                                    if ($masterModel["documentSystemID"] == 7 && $masterRec['stockAdjustmentType'] == 2) { // stock adjustment, cost adjustment
                                        $data[$i][$column] = (($detail['wacAdjRpt']) - ($detail['currentWacRpt'])) * $detail['currenctStockQty'];

                                    } elseif ($masterModel["documentSystemID"] == 20 && ($masterRec["isPerforma"] == 2 || $masterRec["isPerforma"] == 4 || $masterRec["isPerforma"] == 5)) {
                                        $data[$i][$column] = $data[$i][$column] = $detail['issueCostRpt'];
                                    } else if ($masterModel["documentSystemID"] == 97) {    // stock count
                                        $stockCountWacData = array('companySystemID' => $masterRec['companySystemID'],
                                            'itemCodeSystem' => $detail['itemCodeSystem'],
                                            'wareHouseId' => $masterRec['location']);

                                        $itemCurrentCostAndQty = \Inventory::itemCurrentCostAndQty($stockCountWacData);

                                        $data[$i][$column] = $itemCurrentCostAndQty['wacValueReporting'];
                                    } else {
                                        $data[$i][$column] = $detail[$value];
                                    }
                                } else {
                                    $data[$i][$column] = $detail[$value];
                                }

                            }

                            foreach ($masterColumnArray as $column => $value) {
                                $data[$i][$column] = isset($masterRec[$value]) ? $masterRec[$value] : null;
                            }
                            $data[$i]['documentSystemCode'] = $masterModel["autoID"];
                            $data[$i]['createdUserSystemID'] = $empID->employeeSystemID;
                            $data[$i]['createdUserID'] = $empID->empID;
                            $data[$i]['fromDamagedTransactionYN'] = 0;
                            $data[$i]['transactionDate'] = $postedDateGl;
                            $data[$i]['timestamp'] = date('Y-m-d H:i:s');
                            $i++;

                        }
                    }
                }
                else{
                    foreach ($masterRec[$docInforArr["childRelation"]] as $detail) {
                        Log::info($detail . date('H:i:s'));

                        foreach ($detailColumnArray as $column => $value) {
                            if($masterModel["documentSystemID"] == 13 || $masterModel["documentSystemID"] == 10)
                            {
                                $iemDefaultUnit = ItemMaster::where('itemCodeSystem',$detail['itemCodeSystem'])->select('unit')->first();
                                $convertionUnit = UnitConversion::where('masterUnitID',$iemDefaultUnit->unit)->where('subUnitID',$detail['unitOfMeasure'])->first();
                            }
                    
                            if ($column == 'inOutQty') {
                                if ($masterModel["documentSystemID"] == 3 || $masterModel["documentSystemID"] == 12 || $masterModel["documentSystemID"] == 87 || $masterModel["documentSystemID"] == 11) {
                                    $data[$i][$column] = ABS($detail[$value]); // make qty always plus
                                } else if ($masterModel["documentSystemID"] == 8 || $masterModel["documentSystemID"] == 61 || $masterModel["documentSystemID"] == 24 || $masterModel["documentSystemID"] == 20 || $masterModel["documentSystemID"] == 71) {
                                    $data[$i][$column] = ABS($detail[$value]) * -1; // make qty always minus
                                } else if ($masterModel["documentSystemID"] == 7) {    // stock adjustment
                                    if ($masterRec['stockAdjustmentType'] == 2) {       // cost adjustment
                                        $data[$i][$column] = 1;
                                        Log::info('qty is' . $data[$i][$column]);
                                    } else {
                                        $data[$i][$column] = $detail[$value];
                                    }
                                } else if ($masterModel["documentSystemID"] == 97) {    // stock count
                                    $stockCountWacData = array('companySystemID' => $masterRec['companySystemID'],
                                        'itemCodeSystem' => $detail['itemCodeSystem'],
                                        'wareHouseId' => $masterRec['location']);

                                    $itemCurrentCostAndQty = \Inventory::itemCurrentCostAndQty($stockCountWacData);

                                    if ($masterRec['stockCountType'] == 2) {       // cost count
                                        $data[$i][$column] = 1;
                                        Log::info('qty is' . $data[$i][$column]);
                                    } else {
                                        $data[$i][$column] = $detail['noQty'] - $itemCurrentCostAndQty['currentWareHouseStockQty'];
                                    }
                                }else if($masterModel["documentSystemID"] == 13 || $masterModel["documentSystemID"] == 10) // stock transfer /recive
                                {
                                    $amounVal = $masterModel["documentSystemID"] == 13?ABS($detail[$value]) * -1:ABS($detail[$value]);
                                    if(isset($convertionUnit))
                                    {
                                        $convertionValue = $masterModel["documentSystemID"] == 13?ABS(($detail[$value]/$convertionUnit->conversion)) * -1:ABS(($detail[$value]/$convertionUnit->conversion));
                                    }
                                    $data[$i][$column] = $iemDefaultUnit->unit != $detail['unitOfMeasure'] && isset($convertionUnit) ?$convertionValue:$amounVal;
                                    //$data[$i][$column] = ABS($detail[$value]) * -1; // make qty always minus
                                } 
                                
                                else {
                                    $data[$i][$column] = $detail[$value];
                                }
                            } else if ($column == 'wacLocal') {
                                if ($masterModel["documentSystemID"] == 7 && $masterRec['stockAdjustmentType'] == 2) { // stock adjustment, cost adjustment
                                    $data[$i][$column] = (($detail['wacAdjLocal']) - ($detail['currentWaclocal'])) * $detail['currenctStockQty'];

                                } elseif ($masterModel["documentSystemID"] == 20 && ($masterRec["isPerforma"] == 2 || $masterRec["isPerforma"] == 4 || $masterRec["isPerforma"] == 5)) {
                                    $data[$i][$column] = $detail['issueCostLocal'];
                                } else if ($masterModel["documentSystemID"] == 97) {    // stock count
                                    $stockCountWacData = array('companySystemID' => $masterRec['companySystemID'],
                                        'itemCodeSystem' => $detail['itemCodeSystem'],
                                        'wareHouseId' => $masterRec['location']);

                                    $itemCurrentCostAndQty = \Inventory::itemCurrentCostAndQty($stockCountWacData);

                                    $companyCurrencyConversion = \Helper::currencyConversion($masterRec['companySystemID'], $detail['wacValueReportingCurrencyID'], $detail['wacValueReportingCurrencyID'], $itemCurrentCostAndQty['wacValueReporting']);

                                    $data[$i][$column] = $companyCurrencyConversion['localAmount'];
                                }
                                else if($masterModel["documentSystemID"] == 13 || $masterModel["documentSystemID"] == 10)
                                {
                                    $data[$i][$column] = $iemDefaultUnit->unit != $detail['unitOfMeasure'] && isset($convertionUnit) ?($detail[$value]*$convertionUnit->conversion):($detail[$value]);

                                } 
                                else {
                                    $data[$i][$column] = $detail[$value];
                                }
                            } else if ($column == 'wacRpt') {
                                if ($masterModel["documentSystemID"] == 7 && $masterRec['stockAdjustmentType'] == 2) { // stock adjustment, cost adjustment
                                    $data[$i][$column] = (($detail['wacAdjRpt']) - ($detail['currentWacRpt'])) * $detail['currenctStockQty'];

                                } elseif ($masterModel["documentSystemID"] == 20 && ($masterRec["isPerforma"] == 2 || $masterRec["isPerforma"] == 4 || $masterRec["isPerforma"] == 5)) {
                                    $data[$i][$column] = $data[$i][$column] = $detail['issueCostRpt'];
                                } else if ($masterModel["documentSystemID"] == 97) {    // stock count
                                    $stockCountWacData = array('companySystemID' => $masterRec['companySystemID'],
                                        'itemCodeSystem' => $detail['itemCodeSystem'],
                                        'wareHouseId' => $masterRec['location']);

                                    $itemCurrentCostAndQty = \Inventory::itemCurrentCostAndQty($stockCountWacData);

                                    $data[$i][$column] = $itemCurrentCostAndQty['wacValueReporting'];
                                } 
                                else if($masterModel["documentSystemID"] == 13 || $masterModel["documentSystemID"] == 10)
                                {
                                    $data[$i][$column] = $iemDefaultUnit->unit != $detail['unitOfMeasure'] && isset($convertionUnit) ?($detail[$value]*$convertionUnit->conversion):($detail[$value]);

                                } else {
                                    $data[$i][$column] = $detail[$value];
                                }
                            } 
                            else if ($column == 'unitOfMeasure')
                            {
                                if($masterModel["documentSystemID"] == 13 || $masterModel["documentSystemID"] == 10)
                                {
                                    $data[$i][$column] = $iemDefaultUnit->unit;
                                }
                            }
                            else {
                                $data[$i][$column] = $detail[$value];
                            }

                        }

                        foreach ($masterColumnArray as $column => $value) {
                            $data[$i][$column] = isset($masterRec[$value]) ? $masterRec[$value] : null;
                        }
                        $data[$i]['documentSystemCode'] = $masterModel["autoID"];
                        $data[$i]['createdUserSystemID'] = $empID->employeeSystemID;
                        $data[$i]['createdUserID'] = $empID->empID;
                        $data[$i]['fromDamagedTransactionYN'] = 0;
                        $data[$i]['transactionDate'] = $postedDateGl;
                        $data[$i]['timestamp'] = date('Y-m-d H:i:s');
                        $i++;

                        /*
                         * stock adjustment
                         * cost adjustment
                         * if cost adjustment, add one more row to balance qty
                         *
                         * */

                        if ($masterModel["documentSystemID"] == 7 && $masterRec['stockAdjustmentType'] == 2) {

                            foreach ($detailColumnArray as $column => $value) {
                                if ($column == 'inOutQty') {
                                    $data[$i][$column] = -1;
                                } elseif ($column == 'wacLocal') {
                                    $data[$i][$column] = 0;
                                } elseif ($column == 'wacRpt') {
                                    $data[$i][$column] = 0;
                                } else {
                                    $data[$i][$column] = $detail[$value];
                                }

                            }

                            foreach ($masterColumnArray as $column => $value) {
                                $data[$i][$column] = $masterRec[$value];
                            }
                            $data[$i]['documentSystemCode'] = $masterModel["autoID"];
                            $data[$i]['createdUserSystemID'] = $empID->employeeSystemID;
                            $data[$i]['createdUserID'] = $empID->empID;
                            $data[$i]['fromDamagedTransactionYN'] = 0;
                            $data[$i]['transactionDate'] = $postedDateGl;
                            $data[$i]['timestamp'] = date('Y-m-d H:i:s');
                            $i++;
                        }
                        // end stock adjustment
                    }

                }
                if($data){
                    Log::info($data);
                    $items = collect($data)->pluck("itemSystemCode")->toArray();
                    $itemLedgerInsert = ErpItemLedger::insert($data);
                    if($items) {
                        Log::info($items);
                        $masterModel["items"] = $items;
                        $itemassignInsert = \App\Jobs\ItemAssignInsert::dispatch($masterModel);
                    }
                }
            }
            return ['status' => true];
        }else{
            return ['status' => false, 'error' => ['message' => "No records found"]];
        }
	}
}
