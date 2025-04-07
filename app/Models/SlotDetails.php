<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="SlotDetails",
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
 *          property="date",
 *          description="date",
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
 *          property="slot_master_id",
 *          description="slot_master_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="status",
 *          description="0 - Available",
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
 *          property="updated_at",
 *          description="updated_at",
 *          type="string",
 *          format="date-time"
 *      )
 * )
 */
class SlotDetails extends Model
{

    public $table = 'slot_details';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';




    public $fillable = [
        'company_id',
        'created_by',
        'start_date',
        'end_date',
        'slot_master_id',
        'status'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'company_id' => 'integer',
        'created_by' => 'integer',
        'id' => 'integer',
        'slot_master_id' => 'integer',
        'status' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [

    ];

    public function slot_master()
    {
        return $this->hasOne('App\Models\SlotMaster', 'id', 'slot_master_id');
    }

    public function appointment()
    {
        return $this->hasmany('App\Models\Appointment', 'slot_detail_id', 'id');
    }

    public static function getSlotDetails($dateFrom, $dateTo, $companyID, $warehouseID, $id = 0){
        return SlotDetails::select('id', 'start_date', 'end_date')
            ->with([
                'appointment' => function ($q) {
                    $q->select('id', 'slot_detail_id', 'confirmed_yn');
                }
            ])
            ->where('company_id', $companyID)
            ->where(function ($q) use ($dateFrom, $dateTo) {
                $q->whereDate('start_date', '>=', $dateFrom)
                    ->whereDate('start_date', '<=', $dateTo);
            })
            ->where(function ($q) use ($dateFrom, $dateTo) {
                $q->whereDate('end_date', '>=', $dateFrom)
                    ->whereDate('end_date', '<=', $dateTo);
            })
            ->when($warehouseID > 0, function ($q) use ($warehouseID, $id) {
                $q->whereHas('slot_master', function ($q) use($warehouseID, $id) {
                    $q->where('warehouse_id', $warehouseID)
                        ->when($id > 0, function ($q) use ($id) {
                            $q->where('id', '!=', $id);
                        });
                });
            })->get();

    }
    public static function getSlotDetailCompanyID($slotDetailID)
    {
        return SlotDetails::select('id', 'company_id')->where('id', $slotDetailID)->first();
    }
}
