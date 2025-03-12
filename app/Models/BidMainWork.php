<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="BidMainWork",
 *      required={""},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="main_works_id",
 *          description="main_works_id",
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
 *          property="bid_format_detail_id",
 *          description="bid_format_detail_id",
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
 *          property="amount",
 *          description="amount",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="remarks",
 *          description="remarks",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="supplier_registration_id",
 *          description="supplier_registration_id",
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
 *      )
 * )
 */
class BidMainWork extends Model
{

    public $table = 'srm_bid_main_work';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public $timestamps = false;

    public $fillable = [
        'main_works_id',
        'bid_master_id',
        'tender_id',
        'bid_format_detail_id',
        'qty',
        'amount',
        'total_amount',
        'remarks',
        'supplier_registration_id',
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
        'main_works_id' => 'integer',
        'bid_master_id' => 'integer',
        'tender_id' => 'integer',
        'bid_format_detail_id' => 'integer',
        'qty' => 'integer',
        'amount' => 'float',
        'total_amount' => 'float',
        'remarks' => 'string',
        'supplier_registration_id' => 'integer',
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

    public function tender_boq_items(){
        return $this->hasMany('App\Models\TenderBoqItems', 'main_work_id', 'main_works_id');
    }

    public function srm_bid_submission_master()
    {
        return $this->belongsTo('App\Models\BidSubmissionMaster', 'bid_master_id', 'id');
    }

    public static function deleteNullBidMainWorkRecords($tenderId)
    {
        return self::where('tender_id', $tenderId)
            ->where(function ($query) {
                $query->whereNull('qty')
                    ->orWhereNull('amount');
            })
            ->delete();
    }

}
