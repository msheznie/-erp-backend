<?php

namespace App\Repositories;

use App\Models\SegmentAllocatedItem;
use App\Models\PurchaseRequest;
use App\Models\PurchaseRequestDetails;
use App\Models\PurchaseOrderDetails;
use App\Models\ProcumentOrder;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class SegmentAllocatedItemRepository
 * @package App\Repositories
 * @version July 9, 2021, 3:38 pm +04
 *
 * @method SegmentAllocatedItem findWithoutFail($id, $columns = ['*'])
 * @method SegmentAllocatedItem find($id, $columns = ['*'])
 * @method SegmentAllocatedItem first($columns = ['*'])
*/
class SegmentAllocatedItemRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'documentSystemID',
        'documentMasterAutoID',
        'documentDetailAutoID',
        'detailQty',
        'allocatedQty',
        'pulledDocumentSystemID',
        'pulledDocumentDetailID'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return SegmentAllocatedItem::class;
    }

    public function allocateSegmentWiseItem($input)
    {
        if (!isset($input['documentSystemID'])) {
            return ['status' => false, 'message' => 'Document not found'];
        }

        if (!isset($input['serviceLineSystemID'])) {
            return ['status' => false, 'message' => 'Please select a segment'];
        }

        if (!isset($input['docDetailID'])) {
            return ['status' => false, 'message' => 'Item line not found'];
        }

        $checkAlreadyAllocated = SegmentAllocatedItem::where('serviceLineSystemID', $input['serviceLineSystemID'])
                                                     ->where('documentSystemID', $input['documentSystemID'])
                                                     ->where('documentMasterAutoID', $input['docAutoID'])
                                                     ->where('documentDetailAutoID', $input['docDetailID'])
                                                     ->first();

        if ($checkAlreadyAllocated) {
            return ['status' => false, 'message' => 'Item already allocated for selected segment'];
        }

        if (in_array($input['documentSystemID'], [1,50,51])) {
            return $this->allocatePurchaseRequestItems($input);   
        } else {
            return $this->allocatePurchaseOrderItems($input);   
        }
    }


    public function allocatePurchaseRequestItems($input)
    {
        $itemData = PurchaseRequestDetails::find($input['docDetailID']);

        if (!$itemData) {
            return ['status' => false, 'message' => 'Item detail not found'];
        }

        $allocatedQty = SegmentAllocatedItem::where('documentSystemID', $input['documentSystemID'])
                                             ->where('documentMasterAutoID', $input['docAutoID'])
                                             ->where('documentDetailAutoID', $input['docDetailID'])
                                             ->sum('allocatedQty');

        if ($allocatedQty == $itemData->quantityRequested) {
            return ['status' => false, 'message' => 'No remaining quantity to allocate'];
        }

        $allocationData = [
            'documentSystemID' => $input['documentSystemID'],
            'documentMasterAutoID' => $input['docAutoID'],
            'documentDetailAutoID' => $input['docDetailID'],
            'detailQty' => $itemData->quantityRequested,
            'allocatedQty' => $itemData->quantityRequested - $allocatedQty,
            'serviceLineSystemID' => $input['serviceLineSystemID']
        ];

        $createRes = SegmentAllocatedItem::create($allocationData);

        if (!$createRes) {
            return ['status' => false, 'message' => 'Error occured while allocating'];
        }

        return ['status' => true];
    }

    public function allocatePurchaseOrderItems($input)
    {
        $procumentOrder = ProcumentOrder::find($input['docAutoID']);

        $itemData = PurchaseOrderDetails::find($input['docDetailID']);

        if (!$itemData) {
            return ['status' => false, 'message' => 'Item detail not found'];
        }

        $allocatedQty = SegmentAllocatedItem::where('documentSystemID', $input['documentSystemID'])
                                             ->where('documentMasterAutoID', $input['docAutoID'])
                                             ->where('documentDetailAutoID', $input['docDetailID'])
                                             ->sum('allocatedQty');
        if ($allocatedQty == $itemData->noQty) {
            return ['status' => false, 'message' => 'No remaining quantity to allocate'];
        }

        if ($procumentOrder && in_array($procumentOrder->poTypeID, [2, 3])) {

            $allocationData = [
                'documentSystemID' => $input['documentSystemID'],
                'documentMasterAutoID' => $input['docAutoID'],
                'documentDetailAutoID' => $input['docDetailID'],
                'detailQty' => $itemData->noQty,
                'allocatedQty' => $itemData->noQty - $allocatedQty,
                'serviceLineSystemID' => $input['serviceLineSystemID']
            ];

            $createRes = SegmentAllocatedItem::create($allocationData);

            if (!$createRes) {
                return ['status' => false, 'message' => 'Error occured while allocating'];
            }
        } else {
            $purchaseOrderDetail = PurchaseOrderDetails::with(['requestDetail' => function($query) {
                                                                $query->with(['purchase_request']);
                                                            }])->find($input['docDetailID']);
            $allocatedData = [
                                'docDetailID' => $input['docDetailID'],
                                'documentSystemID' => $input['documentSystemID'],
                                'docAutoID' => $input['docAutoID'],
                                'serviceLineSystemID' => $input['serviceLineSystemID'],
                                'allocatedQty' => $itemData->noQty - $allocatedQty,
                                'pulledDocumentSystemID' => $purchaseOrderDetail->requestDetail->purchase_request->documentSystemID,
                                'pulledDocumentDetailID' => $purchaseOrderDetail->purchaseRequestDetailsID,
                            ];      

            return $this->allocatePurchaseOrderItemsFromPR($allocatedData);      
        }

        return ['status' => true];
    }

    public function allocatePurchaseOrderItemsFromPR($input)
    {
        $itemData = PurchaseOrderDetails::find($input['docDetailID']);

        if (!$itemData) {
            return ['status' => false, 'message' => 'Item detail not found'];
        }

        $allocatedQty = SegmentAllocatedItem::where('documentSystemID', $input['pulledDocumentSystemID'])
                                             ->where('documentDetailAutoID', $input['pulledDocumentDetailID'])
                                             ->where('serviceLineSystemID', $input['serviceLineSystemID'])
                                             ->first();

        $remaining = $allocatedQty->allocatedQty - $allocatedQty->copiedQty;
        
        if ($remaining < floatval($input['allocatedQty'])) {
            return ['status' => false, 'message' => 'No remaining quantity to allocate'];
        }

        $allocationData = [
            'documentSystemID' => $input['documentSystemID'],
            'documentMasterAutoID' => $input['docAutoID'],
            'documentDetailAutoID' => $input['docDetailID'],
            'detailQty' => $itemData->noQty,
            'allocatedQty' => $input['allocatedQty'],
            'pulledDocumentSystemID' => $input['pulledDocumentSystemID'],
            'pulledDocumentDetailID' => $input['pulledDocumentDetailID'],
            'serviceLineSystemID' => $input['serviceLineSystemID']
        ];

        $createRes = SegmentAllocatedItem::create($allocationData);

        if (!$createRes) {
            return ['status' => false, 'message' => 'Error occured while allocating'];
        }

        $allocatedQty->copiedQty = $allocatedQty->copiedQty + $input['allocatedQty'];

        $allocatedQty->save();

        return ['status' => true];
    }

    public function allocateWholeItemsInPRToPO($input, $allocatedQtyFromPR = 0)
    {
        $itemData = PurchaseOrderDetails::find($input['docDetailID']);

        if (!$itemData) {
            return ['status' => false, 'message' => 'Item detail not found'];
        }

        $requestDetailData = PurchaseRequestDetails::with(['purchase_request'])->find($input['pulledDocumentDetailID']);

        if (!$requestDetailData) {
            return ['status' => false, 'message' => 'request detail detail not found'];
        }

        $allocatedData = SegmentAllocatedItem::where('documentSystemID', $requestDetailData->purchase_request->documentSystemID)
                                             ->where('documentDetailAutoID', $input['pulledDocumentDetailID'])
                                             ->get();

        foreach ($allocatedData as $key => $value) {
            $allocationData = [
                'documentSystemID' => $input['documentSystemID'],
                'documentMasterAutoID' => $input['docAutoID'],
                'documentDetailAutoID' => $input['docDetailID'],
                'detailQty' => $itemData->noQty,
                'allocatedQty' => ($allocatedQtyFromPR > 0) ? $allocatedQtyFromPR : $value->allocatedQty,
                'pulledDocumentSystemID' => $requestDetailData->purchase_request->documentSystemID,
                'pulledDocumentDetailID' => $input['pulledDocumentDetailID'],
                'serviceLineSystemID' => $value->serviceLineSystemID
            ];

            $createRes = SegmentAllocatedItem::create($allocationData);

            if (!$createRes) {
                return ['status' => false, 'message' => 'Error occured while allocating'];
            }

            $newCopiedQty = ($allocatedQtyFromPR > 0) ? $allocatedQtyFromPR : $value->allocatedQty;

            SegmentAllocatedItem::where('id', $value->id)->update(['copiedQty' => $newCopiedQty]);
        }


        return ['status' => true];
    }

    public function updateAlllocationValidation($input)
    {
        if (in_array($input['documentSystemID'], [1,50,51])) {
            $itemData = PurchaseRequestDetails::find($input['documentDetailAutoID']);

            if (!$itemData) {
                return ['status' => false, 'message' => 'Item detail not found'];
            }

            $allocatedQty = SegmentAllocatedItem::where('documentSystemID', $input['documentSystemID'])
                                                 ->where('documentMasterAutoID', $input['documentMasterAutoID'])
                                                 ->where('documentDetailAutoID', $input['documentDetailAutoID'])
                                                 ->where('id', '!=',$input['id'])
                                                 ->sum('allocatedQty');

            $remainingQty = $itemData->quantityRequested - $allocatedQty;

            if ($input['allocatedQty'] > $remainingQty) {
                return ['status' => false, 'message' => 'quantity cannot be greater than total requested quantity'];
            }
        } else {
            $itemData = PurchaseOrderDetails::find($input['documentDetailAutoID']);

            if (!$itemData) {
                return ['status' => false, 'message' => 'Item detail not found'];
            }

            $allocatedQty = SegmentAllocatedItem::where('documentSystemID', $input['documentSystemID'])
                                                 ->where('documentMasterAutoID', $input['documentMasterAutoID'])
                                                 ->where('documentDetailAutoID', $input['documentDetailAutoID'])
                                                 ->where('id', '!=',$input['id'])
                                                 ->sum('allocatedQty');

            $remainingQty = $itemData->noQty - $allocatedQty;

            if ($input['allocatedQty'] > $remainingQty) {
                return ['status' => false, 'message' => 'quantity cannot be greater than total ordered quantity'];
            }
        }

        return ['status' => true];
    }

    public function validatePurchaseRequestAllocatedQuantity($purchaseRequestID)
    {
        $purchaseRequest = PurchaseRequest::find($purchaseRequestID);
        $items = PurchaseRequestDetails::where('purchaseRequestID', $purchaseRequestID)
                                       ->get();

        foreach ($items as $key => $value) {
            $allocatedQty = SegmentAllocatedItem::where('documentSystemID', $purchaseRequest->documentSystemID)
                                                 ->where('documentMasterAutoID', $purchaseRequestID)
                                                 ->where('documentDetailAutoID', $value->purchaseRequestDetailsID)
                                                 ->sum('allocatedQty');

            if ($allocatedQty != $value->quantityRequested) {
                return ['status' => false, 'message' => $value->itemPrimaryCode." is not fully allocated. please allocate the item quantity to segments"];
            }
        }

        return ['status' => true];
    }

    public function validatePurchaseOrderAllocatedQuantity($purchaseOrderID)
    {
        $purchaseOrder = ProcumentOrder::find($purchaseOrderID);
        $items = PurchaseOrderDetails::where('purchaseOrderMasterID', $purchaseOrderID)
                                       ->get();

        foreach ($items as $key => $value) {
            $allocatedQty = SegmentAllocatedItem::where('documentSystemID', $purchaseOrder->documentSystemID)
                                                 ->where('documentMasterAutoID', $purchaseOrderID)
                                                 ->where('documentDetailAutoID', $value->purchaseOrderDetailsID)
                                                 ->sum('allocatedQty');

            if ($allocatedQty != $value->noQty) {
                return ['status' => false, 'message' => $value->itemPrimaryCode." is not fully allocated. please allocate the item quantity to segments"];
            }
        }

        return ['status' => true];
    }
}
