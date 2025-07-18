<?php

namespace App\Models;

use Carbon\Carbon;
use Eloquent as Model;

/**
 * @OA\Schema(
 *      schema="CalendarDatesDetailEditLog",
 *      required={""},
 *      @OA\Property(
 *          property="calendar_date_id",
 *          description="calendar_date_id",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="company_id",
 *          description="company_id",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="created_at",
 *          description="created_at",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string",
 *          format="date-time"
 *      ),
 *      @OA\Property(
 *          property="from_date",
 *          description="from_date",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string",
 *          format="date-time"
 *      ),
 *      @OA\Property(
 *          property="id",
 *          description="id",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="master_id",
 *          description="master_id",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="modify_type",
 *          description="modify_type",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="ref_log_id",
 *          description="ref_log_id",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="tender_id",
 *          description="tender_id",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="to_date",
 *          description="to_date",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string",
 *          format="date-time"
 *      ),
 *      @OA\Property(
 *          property="updated_at",
 *          description="updated_at",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string",
 *          format="date-time"
 *      ),
 *      @OA\Property(
 *          property="version_id",
 *          description="version_id",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class CalendarDatesDetailEditLog extends Model
{

    public $table = 'srm_calendar_dates_detail_edit_log';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $primaryKey = 'amd_id';

    public $timestamps = false;

    protected $appends = array(
        'from_time',
        'to_time'
    );


    public $fillable = [
        'id',
        'calendar_date_id',
        'company_id',
        'from_date',
        'master_id',
        'modify_type',
        'ref_log_id',
        'tender_id',
        'to_date',
        'version_id',
        'tender_edit_version_id',
        'updated_by',
        'level_no',
        'is_deleted'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'calendar_date_id' => 'integer',
        'company_id' => 'integer',
        'from_date' => 'datetime',
        'id' => 'integer',
        'master_id' => 'integer',
        'modify_type' => 'integer',
        'ref_log_id' => 'integer',
        'tender_id' => 'integer',
        'to_date' => 'datetime',
        'version_id' => 'integer',
        'level_no' => 'integer',
        'is_deleted' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];
    public function getFromTimeAttribute(): ?string
    {
        return $this->from_date
            ? (new Carbon($this->from_date))->format('Y-m-d H:i:s')
            : null;
    }

    public function getToTimeAttribute(): ?string {
        return $this->to_date
            ? (new Carbon($this->to_date))->format('Y-m-d H:i:s')
            : null;
    }
    public function calendarDates(){
        return $this->hasOne('App\Models\CalendarDates', 'id', 'calendar_date_id');
    }
    public static function getLevelNo($id){
        return max(1, (self::where('id', $id)->max('level_no') ?? 0) + 1);
    }
    public static function getDefaultID($tenderMasterID, $versionID){
        $defaultID = self::where('tender_id', $tenderMasterID)
                ->where('version_id', $versionID)
                ->where('level_no', 1)
                ->max('ref_log_id') + 1;

        return  max(1, $defaultID);
    }
    public static function getCalenderDateDetailEdit($tenderMasterId, $versionID, $companySystemID = 0, $calendarDateID = 0)
    {
        $record =  self::select('amd_id', 'id', 'version_id', 'level_no', 'tender_id', 'calendar_date_id', 'from_date', 'to_date')
            ->when($companySystemID > 0, function ($q) use ($companySystemID) {
                $q->where('company_id', $companySystemID);
            })
            ->where('tender_id', $tenderMasterId)
            ->where('version_id', $versionID)
            ->where('is_deleted', 0)
            ->when($calendarDateID > 0, function ($q) use ($calendarDateID) {
                $q->where('calendar_date_id', $calendarDateID);
            });
        if($calendarDateID > 0) {
            return $record->count();
        }
        return $record->get();
    }
    public static function getCalendarDateDetailForAmd($tenderId, $companyId, $calendarDateId, $versionID)
    {
        return self::where('tender_id', $tenderId)
            ->where('company_id', $companyId)
            ->where('version_id', $versionID)
            ->where('is_deleted', 0)
            ->where('calendar_date_id', $calendarDateId)
            ->first();
    }
    public static function getCalendarDateDetailsRecord($id, $versionID){
        return self::where('tender_id', $id)
            ->whereHas('calendarDates', function ($query) {
                $query->where('is_default', 0);
            })
            ->where('version_id', $versionID)
            ->where('is_deleted', 0)
            ->get()
            ->toArray();
    }
    public static function getAmendRecords($versionID, $tenderMasterID, $onlyNullRecords){
        return self::where('version_id', $versionID)
            ->where('tender_id', $tenderMasterID)
            ->where('is_deleted', 0)
            ->when($onlyNullRecords, function ($q) {
                $q->whereNull('id');
            })
            ->when(!$onlyNullRecords, function ($q) {
                $q->whereNotNull('id');
            })
            ->get();
    }
}
