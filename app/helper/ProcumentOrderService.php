<?php
/**
 * =============================================
 * -- File Name : inventory.php
 * -- Project Name : ERP
 * -- Module Name :  email class
 * -- Author : Mohamed Fayas
 * -- Create date : 15 - August 2018
 * -- Description : This file contains the all the common inventory function
 * -- REVISION HISTORY
 */

namespace App\helper;

use App\Repositories\PurchaseRequestDetailsRepository;
use App\Models\Company;
use App\Models\CompanyPolicyMaster;
use App\Models\ErpItemLedger;
use App\Models\AssetFinanceCategory;
use App\Models\FinanceItemcategorySubAssigned;
use App\Models\GRVDetails;
use App\Models\SegmentAllocatedItem;
use App\Models\ItemAssigned;
use App\Models\ItemMaster;
use App\Models\ProcumentOrder;
use App\Models\PurchaseOrderDetails;
use App\Models\PurchaseRequest;
use App\helper\CommonJobService;
use App\Models\PurchaseRequestDetails;
use Illuminate\Support\Facades\DB;
use Response;
use App\Repositories\SegmentAllocatedItemRepository;

use Illuminate\Support\Facades\Log;

class ProcumentOrderService
{
    private $purchaseRequestDetailsRepository;
    private $segmentAllocatedItemRepository;
    
    public function __construct(PurchaseRequestDetailsRepository $purchaseRequestDetailsRepo, SegmentAllocatedItemRepository $segmentAllocatedItemRepository)
    {
        $this->$segmentAllocatedItemRepository = $segmentAllocatedItemRepository;
    }

    public static function  addMultipleItems($records,$purchaseOrder,$db) {

        CommonJobService::db_switch($db);

        $items = $records;
        $valiatedItems = self::validateItem($items,$purchaseOrder);
        $procumentOrder = ProcumentOrder::find($purchaseOrder['purchaseOrderID']);
        $procumentOrder->upload_job_status = 0;
        $procumentOrder->save();
        self::allocateSegments($valiatedItems,$procumentOrder['documentSystemID']);
        Log::info('Add Mutiple Items End');
        $procumentOrder = ProcumentOrder::find($purchaseOrder['purchaseOrderID']);
        $procumentOrder->upload_job_status = 1;
        $procumentOrder->save();
    }

    public static function allocateSegments($items,$documentSystemID) {
        foreach($items as $item) {
            $procumentOrderDetails = PurchaseOrderDetails::create($item);

            $allocationData = [
                'serviceLineSystemID' =>  $procumentOrderDetails['serviceLineSystemID'],
                'documentSystemID' => $documentSystemID,
                'docAutoID' =>  $procumentOrderDetails['purchaseOrderMasterID'],
                'docDetailID' => $procumentOrderDetails['purchaseOrderDetailsID']
            ];

            $checkAlreadyAllocated = SegmentAllocatedItem::where('serviceLineSystemID', $allocationData['serviceLineSystemID'])
            ->where('documentSystemID', $allocationData['documentSystemID'])
            ->where('documentMasterAutoID', $allocationData['docAutoID'])
            ->where('documentDetailAutoID', $allocationData['docDetailID'])
            ->first();

            if ($checkAlreadyAllocated) {
                return ['status' => false, 'message' => 'Item already allocated for selected segment'];
            }

            $procumentOrder = ProcumentOrder::find($allocationData['docAutoID']);

            $itemData = PurchaseOrderDetails::find($allocationData['docDetailID']);

            $allocatedQty = SegmentAllocatedItem::where('documentSystemID', $allocationData['documentSystemID'])
            ->where('documentMasterAutoID', $allocationData['docAutoID'])
            ->where('documentDetailAutoID', $allocationData['docDetailID'])
            ->sum('allocatedQty');

            $allocationData = [
                'documentSystemID' => $allocationData['documentSystemID'],
                'documentMasterAutoID' => $allocationData['docAutoID'],
                'documentDetailAutoID' => $allocationData['docDetailID'],
                'detailQty' => $itemData->noQty,
                'allocatedQty' => $itemData->noQty - $allocatedQty,
                'serviceLineSystemID' => $allocationData['serviceLineSystemID']
            ];

            $createRes = SegmentAllocatedItem::create($allocationData);

        }
    }

    public static function validateItem($items,$purchaseOrder) {
        $validatedItemsArray = [];
        foreach($items as $item) {
            if(array_key_exists('item_code',$item)) {
                $orgItem = ItemMaster::where('primaryCode',trim($item['item_code']))->first();
                if($orgItem) {
                    $item['purchaseOrderMasterID'] = $purchaseOrder['purchaseOrderID'];
                    $item['companyID'] = $purchaseOrder['companyID'];
                    $item['companySystemID'] = $purchaseOrder['companySystemID'];
                    $item['serviceLineSystemID'] = $purchaseOrder['serviceLineSystemID'];
                    $item['serviceLineCode'] = $purchaseOrder['serviceLine'];
                    $item['itemCode'] = $orgItem['itemCodeSystem'];
                    $item['unitCost'] = trim($item['unit_cost']);
                    $item['noQty'] = trim($item['no_qty']);
                    $item['itemPrimaryCode'] = trim($orgItem['primaryCode']);
                    $item['itemDescription'] = trim($orgItem['itemDescription']);
                    $item['netAmount'] =   $item['unitCost'] * $item['noQty'];
                    $item['itemCode'] = $item['item_code'];
                    unset($item['item_code'], $item['unit_cost'], $item['no_qty']);
                    $item['unitOfMeasure'] = trim($orgItem['unit']);
                    $item['altUnit'] = trim($orgItem['unit']);
                    $item['altUnitValue'] = trim($item['noQty']);



                    array_push($validatedItemsArray,$item);
                }
            }
        }

        return $validatedItemsArray;
    }

}