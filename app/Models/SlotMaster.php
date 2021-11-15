<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="SlotMaster",
 *      required={""},
 *      @SWG\Property(
 *          property="company_id",
 *          description="company_id",
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
 *          property="from_date",
 *          description="from_date",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="no_of_deliveries",
 *          description="no_of_deliveries",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="time_from",
 *          description="time_from",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="time_to",
 *          description="time_to",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="to_date",
 *          description="to_date",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="updated_at",
 *          description="updated_at",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="warehouse_id",
 *          description="warehouse_id",
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class SlotMaster extends Model
{

    public $table = 'slot_master';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';




    public $fillable = [
        'company_id',
        'created_by',
        'from_date',
        'no_of_deliveries',
        'time_from',
        'time_to',
        'to_date',
        'warehouse_id'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'company_id' => 'integer',
        'created_by' => 'integer',
        'from_date' => 'datetime',
        'id' => 'integer',
        'no_of_deliveries' => 'integer',
        'time_from' => 'float',
        'time_to' => 'float',
        'to_date' => 'datetime',
        'warehouse_id' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [];

    public function slot_details()
    {
        return $this->hasMany('App\Models\SlotDetails', 'slot_master_id', 'id');
    }

    public function getSlotData($companyID, $wareHouseID)
    {
        return SlotMaster::with(['slot_details'])
        /* ->where('company_id', $companyID) */
       /*  ->where('warehouse_id', $wareHouseID) */
        ->get();
    }
}
