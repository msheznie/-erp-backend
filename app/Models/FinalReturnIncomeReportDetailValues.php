<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @OA\Schema(
 *      schema="FinalReturnIncomeReportDetailValues",
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
 *          property="report_detail_id",
 *          description="report_detail_id",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="column_id",
 *          description="column_id",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="amount",
 *          description="amount",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="number",
 *          format="number"
 *      ),
 *      @OA\Property(
 *          property="timestamp",
 *          description="timestamp",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string",
 *          format="date-time"
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
class FinalReturnIncomeReportDetailValues extends Model
{

    public $table = 'final_return_income_report_detail_values';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';




    public $fillable = [
        'report_detail_id',
        'column_id',
        'amount',
        'timestamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'report_detail_id' => 'integer',
        'column_id' => 'integer',
        'amount' => 'float',
        'timestamp' => 'datetime'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'report_detail_id' => 'required',
        'column_id' => 'required'
    ];

    public function reportDetail()
    {
        return $this->belongsTo(FinalReturnIncomeReportDetails::class, 'report_detail_id', 'id');
    }
    
}
