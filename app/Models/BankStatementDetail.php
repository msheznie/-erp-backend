<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @OA\Schema(
 *      schema="BankStatementDetail",
 *      required={""},
 *      @OA\Property(
 *          property="statementDetailId",
 *          description="statementDetailId",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="statementId",
 *          description="statementId",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="transactionNumber",
 *          description="transactionNumber",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="transactionDate",
 *          description="transactionDate",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string",
 *          format="date"
 *      ),
 *      @OA\Property(
 *          property="debit",
 *          description="debit",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="number",
 *          format="number"
 *      ),
 *      @OA\Property(
 *          property="credit",
 *          description="credit",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="number",
 *          format="number"
 *      ),
 *      @OA\Property(
 *          property="description",
 *          description="description",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="category",
 *          description="category",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="createdDateTime",
 *          description="createdDateTime",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string",
 *          format="date-time"
 *      ),
 *      @OA\Property(
 *          property="timeStamp",
 *          description="timeStamp",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string",
 *          format="date-time"
 *      )
 * )
 */
class BankStatementDetail extends Model
{

    public $table = 'bank_statement_detail';

    const CREATED_AT = 'createdDateTime';
    const UPDATED_AT = 'timeStamp';

    protected $primaryKey = 'statementDetailId';

    public $fillable = [
        'statementId',
        'transactionNumber',
        'transactionDate',
        'debit',
        'credit',
        'description',
        'category',
        'createdDateTime',
        'timeStamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'statementDetailId' => 'integer',
        'statementId' => 'integer',
        'transactionNumber' => 'string',
        'transactionDate' => 'date',
        'debit' => 'float',
        'credit' => 'float',
        'description' => 'string',
        'category' => 'string',
        'createdDateTime' => 'datetime',
        'timeStamp' => 'datetime'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
