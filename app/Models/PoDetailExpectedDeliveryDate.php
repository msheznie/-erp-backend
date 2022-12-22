<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="PoDetailExpectedDeliveryDate",
 *      required={""},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="po_detail_auto_id",
 *          description="po_detail_auto_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="expected_delivery_date",
 *          description="expected_delivery_date",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="allocated_qty",
 *          description="allocated_qty",
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
class PoDetailExpectedDeliveryDate extends Model
{

    public $table = 'po_detail_expected_delivery_date';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';




    public $fillable = [
        'po_detail_auto_id',
        'expected_delivery_date',
        'allocated_qty'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'po_detail_auto_id' => 'integer',
        'expected_delivery_date' => 'datetime',
        'allocated_qty' => 'float'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function po_detail(){
        return $this->belongsTo('App\Models\PurchaseOrderDetails','po_detail_auto_id','purchaseOrderDetailsID');
    }

    
}
