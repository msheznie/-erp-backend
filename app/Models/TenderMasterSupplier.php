<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="TenderMasterSupplier",
 *      required={""},
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
 *          property="purchased_by",
 *          description="purchased_by",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="purchased_date",
 *          description="purchased_date",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="status",
 *          description="status",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="tender_master_id",
 *          description="tender_master_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="updated_at",
 *          description="updated_at",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="updated_by",
 *          description="updated_by",
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class TenderMasterSupplier extends Model
{

    public $table = 'srm_tender_master_supplier';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';




    public $fillable = [
        'created_by',
        'purchased_by',
        'purchased_date',
        'status',
        'tender_master_id',
        'updated_by'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'created_by' => 'integer',
        'id' => 'integer',
        'purchased_by' => 'integer',
        'purchased_date' => 'datetime',
        'status' => 'integer',
        'tender_master_id' => 'integer',
        'updated_by' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'created_by' => 'required',
        'purchased_by' => 'required',
        'purchased_date' => 'required',
        'status' => 'required',
        'updated_by' => 'required'
    ];


    public function tender_master()
    {
        return $this->belongsTo('App\Models\TenderMaster', 'tender_master_id', 'id');
    }

    public function supplierDetails(){
        return $this->hasOne(SupplierRegistrationLink::class,'id','purchased_by');
    }

    public static function getSupplierTender($tenderID, $supplier_id){
        return self::where('tender_master_id', $tenderID)->where('purchased_by', $supplier_id)->exists();
    }

    public static function checkTenderPurchased($tender_id){
        return self::where('tender_master_id', $tender_id)->exists();
    }
}
