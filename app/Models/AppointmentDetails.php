<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="AppointmentDetails",
 *      required={""},
 *      @SWG\Property(
 *          property="appointment_id",
 *          description="appointment_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="created_at",
 *          description="created_at",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="created_by",
 *          description="created_by",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="item_id",
 *          description="item_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="po_master_id",
 *          description="po_master_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="qty",
 *          description="qty",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="updated_at",
 *          description="updated_at",
 *          type="string",
 *          format="date-time"
 *      )
 * )
 */
class AppointmentDetails extends Model
{

    public $table = 'appointment_details';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';




    public $fillable = [
        'appointment_id',
        'created_by',
        'item_id',
        'po_master_id',
        'qty',
        'po_detail_id',
        'foc_qty',
        'total_amount_after_foc',
        'expiry_date',
        'batch_no',
        'manufacturer',
        'brand',
        'remarks'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'appointment_id' => 'integer',
        'created_by' => 'integer',
        'id' => 'integer',
        'item_id' => 'integer',
        'po_master_id' => 'integer',
        'qty' => 'float',
        'po_detail_id' => 'integer'

    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];
    public function getPoMaster(){ 
        return $this->hasOne('App\Models\ProcumentOrder', 'purchaseOrderID', 'po_master_id');
    }
    public function getPoDetails(){ 
        return $this->hasOne('App\Models\PurchaseOrderDetails', 'purchaseOrderDetailsID', 'po_detail_id');
    }
    public function appointment(){
        return $this->belongsTo('App\Models\Appointment', 'appointment_id', 'id');
    }
    public function item(){
        return $this->belongsTo('App\Models\ItemMaster', 'item_id', 'itemCodeSystem');
    }

    public function po_master(){ 
        return $this->belongsTo('App\Models\ProcumentOrder', 'po_master_id', 'purchaseOrderID');
    }
    
}
