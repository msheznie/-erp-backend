<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @OA\Schema(
 *      schema="BankStatementMaster",
 *      required={""},
 *      @OA\Property(
 *          property="statementId",
 *          description="statementId",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="bankAccountAutoID",
 *          description="bankAccountAutoID",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="bankmasterAutoID",
 *          description="bankmasterAutoID",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="companySystemID",
 *          description="companySystemID",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="companyID",
 *          description="companyID",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="transactionCount",
 *          description="transactionCount",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="statementStartDate",
 *          description="statementStartDate",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string",
 *          format="date"
 *      ),
 *      @OA\Property(
 *          property="statementEndDate",
 *          description="statementEndDate",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string",
 *          format="date"
 *      ),
 *      @OA\Property(
 *          property="bankReconciliationMonth",
 *          description="bankReconciliationMonth",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="bankStatementDate",
 *          description="bankStatementDate",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string",
 *          format="date"
 *      ),
 *      @OA\Property(
 *          property="openingBalance",
 *          description="openingBalance",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="number",
 *          format="number"
 *      ),
 *      @OA\Property(
 *          property="endingBalance",
 *          description="endingBalance",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="number",
 *          format="number"
 *      ),
 *      @OA\Property(
 *          property="documentStatus",
 *          description="documentStatus",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="importStatus",
 *          description="importStatus",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="importError",
 *          description="importError",
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
class BankStatementMaster extends Model
{

    public $table = 'bank_statement_master';

    const CREATED_AT = 'createdDateTime';
    const UPDATED_AT = 'timeStamp';

    protected $primaryKey = 'statementId';

    public $fillable = [
        'bankAccountAutoID',
        'bankmasterAutoID',
        'companySystemID',
        'companyID',
        'transactionCount',
        'statementStartDate',
        'statementEndDate',
        'bankReconciliationMonth',
        'bankStatementDate',
        'openingBalance',
        'endingBalance',
        'filePath',
        'documentStatus',
        'matchingInprogress',
        'importStatus',
        'importError',
        'createdDateTime',
        'timeStamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'statementId' => 'integer',
        'bankAccountAutoID' => 'integer',
        'bankmasterAutoID' => 'integer',
        'companySystemID' => 'integer',
        'companyID' => 'string',
        'transactionCount' => 'integer',
        'statementStartDate' => 'date',
        'statementEndDate' => 'date',
        'bankReconciliationMonth' => 'string',
        'bankStatementDate' => 'date',
        'openingBalance' => 'float',
        'endingBalance' => 'float',
        'filePath' => 'string',
        'documentStatus' => 'integer',
        'matchingInprogress' => 'integer',
        'importStatus' => 'integer',
        'importError' => 'string',
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

    public function bankAccount()
    {
        return $this->belongsTo('App\Models\BankAccount', 'bankAccountAutoID', 'bankAccountAutoID');
    }
    
}
