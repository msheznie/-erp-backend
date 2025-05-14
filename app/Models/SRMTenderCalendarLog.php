<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @OA\Schema(
 *      schema="SRMTenderCalendarLog",
 *      required={""},
 *      @OA\Property(
 *          property="id",
 *          description="id",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="filed_description",
 *          description="filed_description",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="old_value",
 *          description="old_value",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="new_value",
 *          description="new_value",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string"
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
 *          property="company_id",
 *          description="company_id",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="created_by",
 *          description="created_by",
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
 *          property="updated_at",
 *          description="updated_at",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string",
 *          format="date-time"
 *      )
 * )
 */
class SRMTenderCalendarLog extends Model
{

    public $table = 'srm_tender_calendar_dates_edit_log';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';




    public $fillable = [
        'filed_description',
        'narration',
        'old_value',
        'new_value',
        'tender_id',
        'company_id',
        'created_by'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'filed_description' => 'string',
        'narration' => 'string',
        'old_value' => 'string',
        'new_value' => 'string',
        'tender_id' => 'integer',
        'company_id' => 'integer',
        'created_by' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'filed_description' => 'required',
        'old_value' => 'required',
        'new_value' => 'required',
        'tender_id' => 'required',
        'company_id' => 'required',
        'created_by' => 'required'
    ];

    public function createdBy()
    {
        return $this->belongsTo('App\Models\Employee', 'created_by', 'employeeSystemID');
    }

    public static function getNarration($tenderId, $companyId){
        return self::where('tender_id', $tenderId)
            ->where('company_id', $companyId)
            ->orderBy('id', 'DESC')
            ->pluck('narration')
            ->first();

    }

    public static function getCalenderDatesEditLogs($tenderId, $companyId, $sort = null, $isGrouped = false)
    {
        return self::select(
            'id',
            'filed_description',
            'narration',
            'old_value',
            'new_value',
            'created_at',
            'created_by',
            'sort',
            'tender_id',
            'company_id'
        )
            ->with(['createdBy' => function ($q) {
                $q->select('employeeSystemID', 'empName');
            }])
            ->where('tender_id', $tenderId)
            ->where('company_id', $companyId)
            ->when($sort, function ($query) use ($sort) {
                return $query->where('sort', $sort);
            })
            ->when($isGrouped, function ($query) {
                return $query->groupBy('sort');
            })
            ->get();
    }

    public static function checkCalendarDatesExists($tenderId, $companyId)
    {
        return SRMTenderCalendarLog::select('sort')
            ->where('tender_id', $tenderId)
            ->where('company_id', $companyId)
            ->latest('created_at')
            ->first();
    }

}
