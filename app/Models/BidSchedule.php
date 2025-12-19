<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="BidSchedule",
 *      required={""},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="schedule_id",
 *          description="schedule_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="bid_master_id",
 *          description="bid_master_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="tender_id",
 *          description="tender_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="supplier_registration_id",
 *          description="supplier_registration_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="remarks",
 *          description="remarks",
 *          type="string"
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
class BidSchedule extends Model
{

    public $table = 'srm_bid_schedule';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public $timestamps = false;

    public $fillable = [
        'schedule_id',
        'bid_master_id',
        'tender_id',
        'supplier_registration_id',
        'remarks',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'schedule_id' => 'integer',
        'bid_master_id' => 'integer',
        'tender_id' => 'integer',
        'supplier_registration_id' => 'integer',
        'remarks' => 'string',
        'created_by' => 'integer',
        'updated_by' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function tenderMainWorks(){ 
        return $this->hasMany('App\Models\TenderMainWorks', 'schedule_id', 'schedule_id'); 
    }

    public function SupplierRegistrationLink(){
        return $this->belongsTo(SupplierRegistrationLink::class, 'supplier_registration_id','id');
    }



}
