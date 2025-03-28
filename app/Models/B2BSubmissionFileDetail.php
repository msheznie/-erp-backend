<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @OA\Schema(
 *      schema="B2BSubmissionFileDetail",
 *      required={""},
 *      @OA\Property(
 *          property="bank_transfer_id",
 *          description="bank_transfer_id",
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
 *          property="document_date",
 *          description="document_date",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string",
 *          format="date"
 *      ),
 *      @OA\Property(
 *          property="latest_downloaded_id",
 *          description="latest_downloaded_id",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="latest_submitted_id",
 *          description="latest_submitted_id",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
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
 *          property="uuid",
 *          description="uuid",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string"
 *      )
 * )
 */
class B2BSubmissionFileDetail extends Model
{

    public $table = 'b2b_submission_file_detail';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';




    public $fillable = [
        'bank_transfer_id',
        'document_date',
        'latest_downloaded_id',
        'latest_submitted_id',
        'submittedBy'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'bank_transfer_id' => 'integer',
        'document_date' => 'date',
        'latest_downloaded_id' => 'integer',
        'latest_submitted_id' => 'integer',
        'uuid' => 'interger',
        'submittedBy' => 'interger'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'bank_transfer_id' => 'required',
        'document_date' => 'required',
        'latest_downloaded_id' => 'required',
        'latest_submitted_id' => 'required'
    ];

    
}
