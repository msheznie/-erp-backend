<?php

namespace App\Repositories;

use App\Models\ItemSerial;
use App\Models\ItemBatch;
use App\Models\GRVDetails;
use App\Models\ItemIssueDetails;
use App\Models\StockTransferDetails;
use App\Models\ItemReturnDetails;
use App\Models\DocumentSubProduct;
use App\Models\PurchaseReturnDetails;
use App\Models\DeliveryOrderDetail;
use App\Models\SalesReturnDetail;
use App\Models\CustomerInvoiceItemDetails;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class ItemSerialRepository
 * @package App\Repositories
 * @version December 23, 2021, 11:01 am +04
 *
 * @method ItemSerial findWithoutFail($id, $columns = ['*'])
 * @method ItemSerial find($id, $columns = ['*'])
 * @method ItemSerial first($columns = ['*'])
*/
class ItemSerialRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'itemSystemCode',
        'productBatchID',
        'serialCode',
        'expireDate',
        'wareHouseSystemID',
        'binLocation',
        'soldFlag'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return ItemSerial::class;
    }


    public function mapSubProducts($productSerialId, $documentSystemID, $documentDetailID, $productInID = null)
    {
        $subProduct = [
            'documentSystemID' => $documentSystemID,
            'documentDetailID' => $documentDetailID,
            'productSerialID' => $productSerialId,
            'quantity' => 1,
            'sold' => 0,
            'soldQty' => 0
        ];

        if (!is_null($productInID)) {
            $subProduct['productInID'] = $productInID;
        }

        switch ($documentSystemID) {
            case 3:
                $grvDetail = GRVDetails::find($documentDetailID);
                $subProduct['documentSystemCode'] = ($grvDetail) ? $grvDetail->grvAutoID : null;

                break;
            case 8:
                $issueDedtail = ItemIssueDetails::find($documentDetailID);
                $subProduct['documentSystemCode'] = ($issueDedtail) ? $issueDedtail->itemIssueAutoID : null;

                break;
            case 12:
                $issueDedtail = ItemReturnDetails::find($documentDetailID);
                $subProduct['documentSystemCode'] = ($issueDedtail) ? $issueDedtail->itemReturnAutoID : null;

                break; 
            case 24:
                $returnDedtail = PurchaseReturnDetails::find($documentDetailID);
                $subProduct['documentSystemCode'] = ($returnDedtail) ? $returnDedtail->purhaseReturnAutoID : null;

                break;
            case 13:
                $returnDedtail = StockTransferDetails::find($documentDetailID);
                $subProduct['documentSystemCode'] = ($returnDedtail) ? $returnDedtail->stockTransferAutoID : null;

                break;
            case 71:
                $deliveryOrderDetail = DeliveryOrderDetail::find($documentDetailID);
                $subProduct['documentSystemCode'] = ($deliveryOrderDetail) ? $deliveryOrderDetail->deliveryOrderID : null;

                break;
            case 20:
                $deliveryOrderDetail = CustomerInvoiceItemDetails::find($documentDetailID);
                $subProduct['documentSystemCode'] = ($deliveryOrderDetail) ? $deliveryOrderDetail->custInvoiceDirectAutoID : null;

                break;
            case 87:
                $salesReturnDetail = SalesReturnDetail::find($documentDetailID);
                $subProduct['documentSystemCode'] = ($salesReturnDetail) ? $salesReturnDetail->salesReturnID : null;

                break;
            default:
                # code...
                break;
        }

        $res = DocumentSubProduct::create($subProduct);

        return ['status' => true];
    }

    public function mapBatchSubProducts($productBatchID, $documentSystemID, $documentDetailID, $productInID = null, $quantity = 0, $wareHouseSystemID = null)
    {
        $productBatch = ItemBatch::find($productBatchID);

        $subProduct = [
            'documentSystemID' => $documentSystemID,
            'documentDetailID' => $documentDetailID,
            'productBatchID' => $productBatchID,
            'wareHouseSystemID' => $wareHouseSystemID,
            'quantity' => ($productBatch && $quantity == 0) ? $productBatch->quantity : $quantity,
            'sold' => 0,
            'soldQty' => 0
        ];

        if (!is_null($productInID)) {
            $subProduct['productInID'] = $productInID;

            if (is_null($wareHouseSystemID)) {
                $documentProduct = DocumentSubProduct::find($productInID);
                $subProduct['wareHouseSystemID'] = $documentProduct ? $documentProduct->wareHouseSystemID : null;
            }
        }

        switch ($documentSystemID) {
            case 3:
                $grvDetail = GRVDetails::find($documentDetailID);
                $subProduct['documentSystemCode'] = ($grvDetail) ? $grvDetail->grvAutoID : null;

                break;
            case 8:
                $issueDedtail = ItemIssueDetails::find($documentDetailID);
                $subProduct['documentSystemCode'] = ($issueDedtail) ? $issueDedtail->itemIssueAutoID : null;

                break;
            case 12:
                $issueDedtail = ItemReturnDetails::find($documentDetailID);
                $subProduct['documentSystemCode'] = ($issueDedtail) ? $issueDedtail->itemReturnAutoID : null;

                break; 
            case 24:
                $returnDedtail = PurchaseReturnDetails::find($documentDetailID);
                $subProduct['documentSystemCode'] = ($returnDedtail) ? $returnDedtail->purhaseReturnAutoID : null;

                break;
            case 13:
                $returnDedtail = StockTransferDetails::find($documentDetailID);
                $subProduct['documentSystemCode'] = ($returnDedtail) ? $returnDedtail->stockTransferAutoID : null;

                break;
            case 71:
                $deliveryOrderDetail = DeliveryOrderDetail::find($documentDetailID);
                $subProduct['documentSystemCode'] = ($deliveryOrderDetail) ? $deliveryOrderDetail->deliveryOrderID : null;

                break;
            case 20:
                $deliveryOrderDetail = CustomerInvoiceItemDetails::find($documentDetailID);
                $subProduct['documentSystemCode'] = ($deliveryOrderDetail) ? $deliveryOrderDetail->custInvoiceDirectAutoID : null;

                break;
            case 87:
                $salesReturnDetail = SalesReturnDetail::find($documentDetailID);
                $subProduct['documentSystemCode'] = ($salesReturnDetail) ? $salesReturnDetail->salesReturnID : null;

                break;
            default:
                # code...
                break;
        }

        $res = DocumentSubProduct::create($subProduct);

        return ['status' => true];
    }
}
