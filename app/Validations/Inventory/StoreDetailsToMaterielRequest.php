<?php

namespace App\Validations\Inventory;
use App\Http\Controllers\AppBaseController;
use App\Models\Company;
use App\Models\CompanyPolicyMaster;
use App\Models\CustomerInvoiceDirect;
use App\Models\DeliveryOrder;
use App\Models\ErpItemLedger;
use App\Models\FinanceItemcategorySubAssigned;
use App\Models\GRVDetails;
use App\Models\ItemAssigned;
use App\Models\ItemIssueMaster;
use App\Models\ItemMaster;
use App\Models\MaterielRequest;
use App\Models\MaterielRequestDetails;
use App\Models\PurchaseOrderDetails;
use App\Models\PurchaseReturn;
use App\Models\StockTransfer;
use App\Services\Inventory\MaterialIssueService;
use Illuminate\Http\Request;

class StoreDetailsToMaterielRequest extends AppBaseController
{

    public function validate(Request $request) {

        $input = $this->convertArrayToValue($request->input('item'));

        $detail = $input;
        $companySystemID = 1;

        $company = Company::where('companySystemID', $companySystemID)->first();

        if (empty($company)) {
            return $this->sendError('Company not found');
        }

        $item = MaterielRequestDetails::where('RequestDetailsID', $detail['itemCode'])->with(['item_by'])->first();

        if (empty($item)) {
            return $this->sendError('Item not found');
        }


        if (isset($detail['itemIssueAutoID'])) {
            if ($detail['itemIssueAutoID'] > 0) {
                $itemIssueMaster = ItemIssueMaster::where('itemIssueAutoID', $detail['itemIssueAutoID'])->first();

                if (empty($itemIssueMaster)) {
                    return $this->sendError('Materiel Issue not found');
                }
            } else {
                return $this->sendError('Materiel Issue not found');
            }
        } else {
            return $this->sendError('Materiel Issue not found');
        }

        if((isset($itemIssueMaster->reqDocID) && $itemIssueMaster->reqDocID > 0) && ($itemIssueMaster->reqDocID != $detail['RequestID']))
            return $this->sendError('Cannot select items from multiple material requests');

        if(!isset($detail['itemCodeSystem']) && (!($detail['mappingItemCode']) ||$detail['mappingItemCode'] == 0))
            return $this->sendError('Please map the original item.');

        if(isset($detail['qtyIssued']) && $detail['qtyIssued'] == 0)
            return $this->sendError('Issuing quantity cannot be zero');

        if(!isset($detail['qtyIssued'])  || $detail['qtyIssued'] == '')
            return $this->sendError('Issuing quantity cannot be empty');


        if(isset($detail['mappingItemCode']) && $detail['mappingItemCode'][0] > 0)
        {
           $originalItem = ItemMaster::where('itemCodeSystem',$detail['mappingItemCode'])->first();
           $detail['itemFinanceCategoryID'] = $originalItem->financeCategoryMaster;
           $detail['itemFinanceCategorySubID'] = $originalItem->financeCategorySub;
           $detail['itemCodeSystem'] = $originalItem->itemCodeSystem;
           $detail['itemPrimaryCode'] = $originalItem->primaryCode;
           $detail['itemDescription'] = $originalItem->itemDescription;
           $detail['partNumber'] = $originalItem->itemPrimaryCode;
        }
        $financeItemCategorySubAssigned = FinanceItemcategorySubAssigned::where('companySystemID', $companySystemID)
                ->where('mainItemCategoryID', $detail['itemFinanceCategoryID'])
            ->where('itemCategorySubID', $detail['itemFinanceCategorySubID'])
            ->first();

        if(empty($financeItemCategorySubAssigned))
            return $this->sendError("Account code not updated.", 500);

        // check policy 18

        $allowPendingApproval = CompanyPolicyMaster::where('companyPolicyCategoryID', 18)
            ->where('companySystemID', $companySystemID)
            ->first();

        $checkWhether = ItemIssueMaster::where('itemIssueAutoID', '!=', $itemIssueMaster->itemIssueAutoID)
            ->where('companySystemID', $companySystemID)
            ->where('wareHouseFrom', $itemIssueMaster->wareHouseFrom)
            ->select([
                'erp_itemissuemaster.itemIssueAutoID',
                'erp_itemissuemaster.companySystemID',
                'erp_itemissuemaster.wareHouseFromCode',
                'erp_itemissuemaster.itemIssueCode',
                'erp_itemissuemaster.approved'
            ])
            ->groupBy(
                'erp_itemissuemaster.itemIssueAutoID',
                'erp_itemissuemaster.companySystemID',
                'erp_itemissuemaster.wareHouseFromCode',
                'erp_itemissuemaster.itemIssueCode',
                'erp_itemissuemaster.approved'
            )
            ->whereHas('details', function ($query) use ($companySystemID, $detail) {
                $query->where('itemCodeSystem', $detail['itemCodeSystem']);
            })
            ->where('approved', 0)
            ->first();
        /* approved=0*/

        if (!empty($checkWhether)) {
            return $this->sendError("There is a Materiel Issue (" . $checkWhether->itemIssueCode . ") pending for approval for the item you are trying to add. Please check again.", 500);
        }

        $checkWhetherStockTransfer = StockTransfer::where('companySystemID', $companySystemID)
            ->where('locationFrom', $itemIssueMaster->wareHouseFrom)
            ->select([
                'erp_stocktransfer.stockTransferAutoID',
                'erp_stocktransfer.companySystemID',
                'erp_stocktransfer.locationFrom',
                'erp_stocktransfer.stockTransferCode',
                'erp_stocktransfer.approved'
            ])
            ->groupBy(
                'erp_stocktransfer.stockTransferAutoID',
                'erp_stocktransfer.companySystemID',
                'erp_stocktransfer.locationFrom',
                'erp_stocktransfer.stockTransferCode',
                'erp_stocktransfer.approved'
            )
            ->whereHas('details', function ($query) use ($companySystemID, $detail) {
                $query->where('itemCodeSystem', $detail['itemCodeSystem']);
            })
            ->where('approved', 0)
            ->first();
        /* approved=0*/

        if (!empty($checkWhetherStockTransfer)) {
            return $this->sendError("There is a Stock Transfer (" . $checkWhetherStockTransfer->stockTransferCode . ") pending for approval for the item you are trying to add. Please check again.", 500);
        }

        /*check item sales invoice*/
        $checkWhetherInvoice = CustomerInvoiceDirect::where('companySystemID', $companySystemID)
            ->select([
                'erp_custinvoicedirect.custInvoiceDirectAutoID',
                'erp_custinvoicedirect.bookingInvCode',
                'erp_custinvoicedirect.wareHouseSystemCode',
                'erp_custinvoicedirect.approved'
            ])
            ->groupBy(
                'erp_custinvoicedirect.custInvoiceDirectAutoID',
                'erp_custinvoicedirect.companySystemID',
                'erp_custinvoicedirect.bookingInvCode',
                'erp_custinvoicedirect.wareHouseSystemCode',
                'erp_custinvoicedirect.approved'
            )
            ->whereHas('issue_item_details', function ($query) use ($companySystemID, $detail) {
                $query->where('itemCodeSystem', $detail['itemCodeSystem']);
            })
            ->where('approved', 0)
            ->where('canceledYN', 0)
            ->first();
        /* approved=0*/

        if (!empty($checkWhetherInvoice)) {
            return $this->sendError("There is a Customer Invoice (" . $checkWhetherInvoice->bookingInvCode . ") pending for approval for the item you are trying to add. Please check again.", 500);
        }

        // check in delivery order
        $checkWhetherDeliveryOrder = DeliveryOrder::where('companySystemID', $companySystemID)
            ->select([
                'erp_delivery_order.deliveryOrderID',
                'erp_delivery_order.deliveryOrderCode'
            ])
            ->groupBy(
                'erp_delivery_order.deliveryOrderID',
                'erp_delivery_order.companySystemID'
            )
            ->whereHas('detail', function ($query) use ($companySystemID, $detail) {
                $query->where('itemCodeSystem', $detail['itemCodeSystem']);
            })
            ->where('approvedYN', 0)
            ->first();

        if (!empty($checkWhetherDeliveryOrder)) {
            return $this->sendError("There is a Delivery Order (" . $checkWhetherDeliveryOrder->deliveryOrderCode . ") pending for approval for the item you are trying to add. Please check again.", 500);
        }

        /*Check in purchase return*/
        $checkWhetherPR = PurchaseReturn::where('companySystemID', $companySystemID)
            ->select([
                'erp_purchasereturnmaster.purhaseReturnAutoID',
                'erp_purchasereturnmaster.companySystemID',
                'erp_purchasereturnmaster.purchaseReturnLocation',
                'erp_purchasereturnmaster.purchaseReturnCode',
            ])
            ->groupBy(
                'erp_purchasereturnmaster.purhaseReturnAutoID',
                'erp_purchasereturnmaster.companySystemID',
                'erp_purchasereturnmaster.purchaseReturnLocation'
            )
            ->whereHas('details', function ($query) use ($detail) {
                $query->where('itemCode', $detail['itemCodeSystem']);
            })
            ->where('approved', 0)
            ->first();
        /* approved=0*/

        if (!empty($checkWhetherPR)) {
            return $this->sendError("There is a Purchase Return (" . $checkWhetherPR->purchaseReturnCode . ") pending for approval for the item you are trying to add. Please check again.", 500);
        }


        $data = array('companySystemID' => $companySystemID,
            'itemCodeSystem' => $detail['itemCodeSystem'],
            'wareHouseId' =>  $itemIssueMaster->wareHouseFrom);
        $itemCurrentCostAndQty = \Inventory::itemCurrentCostAndQty($data);


        $detail['currentStockQty'] = $itemCurrentCostAndQty['currentStockQty'];
        $detail['currentWareHouseStockQty'] = $itemCurrentCostAndQty['currentWareHouseStockQty'];
        $detail['currentStockQtyInDamageReturn'] = $itemCurrentCostAndQty['currentStockQtyInDamageReturn'];
        $detail['issueCostLocal'] = $itemCurrentCostAndQty['wacValueLocal'];
        $detail['issueCostRpt'] = $itemCurrentCostAndQty['wacValueReporting'];

        $detail['reqDocID'] = $detail['RequestID'];
        $detail['issueType'] = 2;
        $detail['qtyRequested'] = $detail['quantityRequested'];
        $qntyDetails = MaterialIssueService::getItemDetailsForMaterialIssue($detail);


        if((int)$detail['qtyIssued'] > $qntyDetails['qtyAvailableToIssue']) {
            return $this->sendError("Quantity Issuing is greater than the available quantity", 500);
        }


        if((int)$detail['qtyIssued'] >  $detail['currentWareHouseStockQty']) {
            $qtyError = array('type' => 'qty','status' => 'warehouse');
            return $this->sendError("Current warehouse stock Qty is: " .  $detail['currentWareHouseStockQty'] . " .You cannot issue more than the current warehouse stock qty.", 500, $qtyError);
        }


        if ($item && is_null($item->itemCode)) {
            if (isset($detail['mappingItemCode']) && $detail['mappingItemCode'] > 0) {
                $itemMap = $this->matchRequestItem($item->RequestID, $detail['mappingItemCode'], $companySystemID, $item->toArray());
                if (!$itemMap['status']) {
                    return $this->sendError($itemMap['message'], 500);
                } else {
                    $item = $itemMap['data'];
                }
            } else {
                return $this->sendError('Item not found, Please map this item with a original item', 500, ["type" => 'itemMap']);
            }
        }
        return $this->sendResponse($detail,'success');
    }

    public function matchRequestItem($requestID, $itemCode, $companySystemID, $input)
    {
        $item = ItemAssigned::where('itemCodeSystem', $itemCode)
            ->where('companySystemID', $companySystemID)
            ->first();
        if (empty($item)) {
            return ['status' => false, 'message' => 'Item not found'];
        }

        $materielRequest = MaterielRequest::where('RequestID', $requestID)->first();


        if (empty($materielRequest)) {
            return ['status' => false, 'message' => 'Materiel Request Details not found'];
        }


        $input['itemCode'] = $item->itemCodeSystem;
        $input['item_by'] = ItemMaster::find($item->itemCodeSystem);
        $input['itemDescription'] = $item->itemDescription;
        $input['partNumber'] = $item->secondaryItemCode;
        $input['itemFinanceCategoryID'] = $item->financeCategoryMaster;
        $input['itemFinanceCategorySubID'] = $item->financeCategorySub;
        $input['unitOfMeasure'] = $item->itemUnitOfMeasure;
        $input['unitOfMeasureIssued'] = $item->itemUnitOfMeasure;
        if($item->maximunQty){
            $input['maxQty'] = $item->maximunQty;
        }else{
            $input['maxQty'] = 0;
        }

        if($item->minimumQty){
            $input['minQty'] = $item->minimumQty;
        }else{
            $input['minQty'] = 0;
        }

        $financeItemCategorySubAssigned = FinanceItemcategorySubAssigned::where('companySystemID', $item->companySystemID)
            ->where('mainItemCategoryID', $item->financeCategoryMaster)
            ->where('itemCategorySubID', $item->financeCategorySub)
            ->first();

        if (empty($financeItemCategorySubAssigned)) {
            return ['status' => false, 'message' => 'Finance Category not found'];
        }

        if ($item->financeCategoryMaster == 1) {

            $alreadyAdded = MaterielRequest::where('RequestID', $input['RequestID'])
                ->whereHas('details', function ($query) use ($item) {
                    $query->where('itemCode', $item->itemCodeSystem);
                })
                ->first();

            if ($alreadyAdded) {
                return ['status' => false, 'message' => 'Selected item is already added to above material request. Please check again'];
            }
        }

        $input['financeGLcodebBS']  = $financeItemCategorySubAssigned->financeGLcodebBS;
        $input['financeGLcodePL']   = $financeItemCategorySubAssigned->financeGLcodePL;
        $input['includePLForGRVYN'] = $financeItemCategorySubAssigned->includePLForGRVYN;


        $poQty = PurchaseOrderDetails::whereHas('order' , function ($query) use ($companySystemID) {
            $query->where('companySystemID', $companySystemID)
                ->where('approved', -1)
                ->where('poCancelledYN', 0);
        })
            ->where('itemCode', $input['itemCode'])
            ->groupBy('erp_purchaseorderdetails.companySystemID',
                'erp_purchaseorderdetails.itemCode')
            ->select(
                [
                    'erp_purchaseorderdetails.companySystemID',
                    'erp_purchaseorderdetails.itemCode',
                    'erp_purchaseorderdetails.itemPrimaryCode'
                ]
            )
            ->sum('noQty');

        $quantityInHand = ErpItemLedger::where('itemSystemCode', $input['itemCode'])
            ->where('companySystemID', $companySystemID)
            ->groupBy('itemSystemCode')
            ->sum('inOutQty');

        $grvQty = GRVDetails::whereHas('grv_master' , function ($query) use ($companySystemID) {
            $query->where('companySystemID', $companySystemID)
                ->where('grvTypeID', 2)
                ->groupBy('erp_grvmaster.companySystemID');
        })
            ->where('itemCode', $input['itemCode'])
            ->groupBy('erp_grvdetails.itemCode')
            ->select(
                [
                    'erp_grvdetails.companySystemID',
                    'erp_grvdetails.itemCode'
                ])
            ->sum('noQty');

        $quantityOnOrder = $poQty - $grvQty;
        $input['quantityOnOrder'] = $quantityOnOrder;
        $input['quantityInHand']  = $quantityInHand;

        if($input['qtyIssuedDefaultMeasure'] > $input['quantityInHand']){
            return ['status' => false, 'message' => 'No stock Qty. Please check again'];
        }

        return ['status' => true, 'data' => (object)$input];
    }

}
