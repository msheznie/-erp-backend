<?php

namespace App\Repositories;

use App\Models\SegmentAllocatedItem;
use App\Models\PurchaseRequestDetails;
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

        }

        return ['status' => true];
    }
}
