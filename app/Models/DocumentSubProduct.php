<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="DocumentSubProduct",
 *      required={""},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="documentSystemID",
 *          description="documentSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="documentSystemCode",
 *          description="documentSystemCode",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="documentDetailID",
 *          description="documentDetailID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="productSerialID",
 *          description="productSerialID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="productBatchID",
 *          description="productBatchID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="quantity",
 *          description="quantity",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="sold",
 *          description="sold",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="soldQty",
 *          description="soldQty",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="created_at",
 *          description="created_at",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="updated_at",
 *          description="updated_at",
 *          type="string",
 *          format="date-time"
 *      )
 * )
 */
class DocumentSubProduct extends Model
{

    public $table = 'document_sub_products';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';




    public $fillable = [
        'documentSystemID',
        'documentSystemCode',
        'documentDetailID',
        'productSerialID',
        'wareHouseSystemID',
        'productBatchID',
        'quantity',
        'sold',
        'productInID',
        'soldQty'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'documentSystemID' => 'integer',
        'documentSystemCode' => 'integer',
        'documentDetailID' => 'integer',
        'productSerialID' => 'integer',
        'productBatchID' => 'integer',
        'productInID' => 'integer',
        'wareHouseSystemID' => 'integer',
        'quantity' => 'float',
        'sold' => 'integer',
        'soldQty' => 'float'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];


    public function serial_data()
    {
        return $this->belongsTo('App\Models\ItemSerial', 'productSerialID', 'id');
    }

    public function batch_data()
    {
        return $this->belongsTo('App\Models\ItemBatch', 'productBatchID', 'id');
    }

    public function grv_master()
    {
        return $this->belongsTo('App\Models\GRVMaster', 'documentSystemCode', 'grvAutoID')->where('documentSystemID', 3);
    }

     public function material_issue()
    {
        return $this->belongsTo('App\Models\ItemIssueMaster', 'documentSystemCode', 'itemIssueAutoID')->where('documentSystemID', 8);
    } 

    public function material_return()
    {
        return $this->belongsTo('App\Models\ItemReturnMaster', 'documentSystemCode', 'itemReturnAutoID')->where('documentSystemID', 12);
    }

    public function purchase_return()
    {
        return $this->belongsTo('App\Models\PurchaseReturn', 'documentSystemCode', 'purhaseReturnAutoID')->where('documentSystemID', 24);
    }

     public function delivery_order()
    {
        return $this->belongsTo('App\Models\DeliveryOrder', 'documentSystemCode', 'deliveryOrderID')->where('documentSystemID', 71);
    }

     public function customer_invoice()
    {
        return $this->belongsTo('App\Models\CustomerInvoiceDirect', 'documentSystemCode', 'custInvoiceDirectAutoID')->where('documentSystemID', 20);
    }

     public function sales_return()
    {
        return $this->belongsTo('App\Models\SalesReturn', 'documentSystemCode', 'id')->where('documentSystemID', 87);
    }
}
