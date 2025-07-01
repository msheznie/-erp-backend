<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="TenderBoqItems",
 *      required={""},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="main_work_id",
 *          description="main_work_id",
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
 *          property="uom",
 *          description="uom",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="qty",
 *          description="qty",
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
class TenderBoqItems extends Model
{

    public $table = 'srm_tender_boq_items';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';




    public $fillable = [
        'main_work_id',
        'item_name',
        'description',
        'uom',
        'qty',
        'created_by',
        'updated_by',
        'company_id',
        'tender_ranking_line_item',
        'tender_id',
        'item_primary_code',
        'origin',
        'purchase_request_id'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'main_work_id' => 'integer',
        'item_name' => 'string',
        'description' => 'string',
        'uom' => 'integer',
        'qty' => 'float',
        'created_by' => 'integer',
        'updated_by' => 'integer',
        'company_id' => 'integer',
        'item_primary_code' => 'string',
        'origin' => 'integer',
        'purchase_request_id' => 'string',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function bid_boq()
    {
        return $this->hasOne('App\Models\BidBoq', 'boq_id', 'id');
    }

    public function bid_boqs()
    {
        return $this->hasMany('App\Models\BidBoq', 'boq_id', 'id');
    }

    public function unit()
    {
        return $this->belongsTo('App\Models\Unit','uom','UnitID');
    }

    public function ranking_items()
    {
        return $this->hasOne('App\Models\CommercialBidRankingItems', 'id', 'tender_ranking_line_item');
    }

    public static function getTenderBoqItemsAmd($tender_id, $mainWorkID){
        return self::where('tender_id', $tender_id)->where('main_work_id', $mainWorkID)->get();
    }
    public static function checkExistsBoqItem($scheduleDetailID){
        return self::where('main_work_id', $scheduleDetailID)->exists();
    }
    public static function getTenderBoqItemList($main_work_id){
        return self::where('main_work_id', $main_work_id)->get();
    }
    public static function checkItemNameExists($itemName, $amd_mainWorkID){
        return self::where('item_name',$itemName)
            ->where('main_work_id', $amd_mainWorkID)->first();
    }
    public static function checkPRAlreadyAdded($tender_id, $purchaseRequestIDToCheck, $main_work_id){
        return self::where('tender_id', $tender_id)
            ->whereRaw("FIND_IN_SET('$purchaseRequestIDToCheck', purchase_request_id) > 0")
            ->where('main_work_id', $main_work_id)
            ->first();
    }
}
