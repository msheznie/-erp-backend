<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="PricingScheduleMaster",
 *      required={""},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
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
 *          property="scheduler_name",
 *          description="scheduler_name",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="price_bid_format_id",
 *          description="price_bid_format_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="schedule_mandatory",
 *          description="schedule_mandatory",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="items_mandatory",
 *          description="items_mandatory",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="status",
 *          description="if 1 Complete if 0 Pending",
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
 *      ),
 *      @SWG\Property(
 *          property="company_id",
 *          description="company_id",
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class PricingScheduleMaster extends Model
{

    public $table = 'srm_pricing_schedule_master';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';




    public $fillable = [
        'tender_id',
        'scheduler_name',
        'price_bid_format_id',
        'schedule_mandatory',
        'items_mandatory',
        'status',
        'created_by',
        'updated_by',
        'company_id'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'tender_id' => 'integer',
        'scheduler_name' => 'string',
        'price_bid_format_id' => 'integer',
        'schedule_mandatory' => 'integer',
        'items_mandatory' => 'integer',
        'status' => 'integer',
        'created_by' => 'integer',
        'updated_by' => 'integer',
        'company_id' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function tender_master()
    {
        return $this->hasOne('App\Models\TenderMaster', 'id', 'tender_id');
    }

    public function tender_bid_format_master()
    {
        return $this->hasOne('App\Models\TenderBidFormatMaster', 'id', 'price_bid_format_id');
    }

    
}
