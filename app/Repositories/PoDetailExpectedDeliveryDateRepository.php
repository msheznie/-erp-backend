<?php

namespace App\Repositories;

use App\Models\PoDetailExpectedDeliveryDate;
use InfyOm\Generator\Common\BaseRepository;
use App\Models\PurchaseOrderDetails;
use App\Models\CompanyPolicyMaster;
use App\Models\ProcumentOrder;

/**
 * Class PoDetailExpectedDeliveryDateRepository
 * @package App\Repositories
 * @version December 20, 2022, 2:25 pm +04
 *
 * @method PoDetailExpectedDeliveryDate findWithoutFail($id, $columns = ['*'])
 * @method PoDetailExpectedDeliveryDate find($id, $columns = ['*'])
 * @method PoDetailExpectedDeliveryDate first($columns = ['*'])
*/
class PoDetailExpectedDeliveryDateRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'po_detail_auto_id',
        'expected_delivery_date',
        'allocated_qty'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return PoDetailExpectedDeliveryDate::class;
    }

     public function validateAllocatedExpectedDeliveryDate($purchaseOrderID)
    {
        $purchaseOrder = ProcumentOrder::find($purchaseOrderID);

        $isExpectedDeliveryDateEnabled = CompanyPolicyMaster::where('companyPolicyCategoryID', 71)
                                                            ->where('companySystemID', $purchaseOrder->companySystemID)
                                                            ->where('isYesNO', 1)
                                                            ->exists();

        if (!$isExpectedDeliveryDateEnabled) {
            return ['status' => true];
        }

        $items = PurchaseOrderDetails::where('purchaseOrderMasterID', $purchaseOrderID)
                                       ->get();

        foreach ($items as $key => $value) {
            $allocatedQty = PoDetailExpectedDeliveryDate::where('po_detail_auto_id', $value->purchaseOrderDetailsID)
                                                 ->sum('allocated_qty');

            if ($allocatedQty != $value->noQty) {
                return ['status' => false, 'message' => $value->itemPrimaryCode." is not fully allocated. please allocate the item quantity to expected delivery dates"];
            }
        }

        return ['status' => true];
    } 

    public function checkAndUpdateExpectedDeliveryDate($purchaseOrderID, $expectedDeliveryDate)
    {
        $purchaseOrder = ProcumentOrder::find($purchaseOrderID);

        $isExpectedDeliveryDateEnabled = CompanyPolicyMaster::where('companyPolicyCategoryID', 71)
                                                            ->where('companySystemID', $purchaseOrder->companySystemID)
                                                            ->where('isYesNO', 1)
                                                            ->exists();

        if ($isExpectedDeliveryDateEnabled) {
            return ['status' => true];
        }

        $items = PurchaseOrderDetails::where('purchaseOrderMasterID', $purchaseOrderID)
                                       ->get();

        foreach ($items as $key => $value) {
            $allocatedQty = PoDetailExpectedDeliveryDate::where('po_detail_auto_id', $value->purchaseOrderDetailsID)
                                                 ->update(['expected_delivery_date' => $expectedDeliveryDate]);
        }

        return ['status' => true];
    }
}
