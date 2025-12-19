<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @OA\Schema(
 *      schema="BankReconciliationRules",
 *      required={""},
 *      @OA\Property(
 *          property="ruleId",
 *          description="ruleId",
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
 *          property="ruleDescription",
 *          description="ruleDescription",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="transactionType",
 *          description="1 => Payment Voucher, 2 => Receipt Voucher",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="matchType",
 *          description="1 => Exact Match, 2 => Partial Match",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="isMatchAmount",
 *          description="0 => No, 1 => Yes",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="amountDifference",
 *          description="amountDifference",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="number",
 *          format="number"
 *      ),
 *      @OA\Property(
 *          property="isMatchDate",
 *          description="0 => No, 1 => Yes",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="dateDifference",
 *          description="dateDifference",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="isMatchDocument",
 *          description="0 => No, 1 => Yes",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="systemDocumentColumn",
 *          description="1 => Document Number, 2 => Narration",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="statementDocumentColumn",
 *          description="1 => Transaction Number, 2 => Description",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="statementReferenceFrom",
 *          description="statementReferenceFrom",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="statementReferenceTo",
 *          description="statementReferenceTo",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="isMatchChequeNo",
 *          description="0 => No, 1 => Yes",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="statementChqueColumn",
 *          description="1 => Transaction Number, 2 => Description",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="isDefault",
 *          description="0 => No, 1 => Yes",
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
class BankReconciliationRules extends Model
{

    public $table = 'bank_reconciliation_rules';
    
    const CREATED_AT = 'createdDateTime';
    const UPDATED_AT = 'timeStamp';

    protected $primaryKey = 'ruleId';
    protected  $appends = ['transactionTypeDescription'];

    public $fillable = [
        'bankAccountAutoID',
        'ruleDescription',
        'transactionType',
        'matchType',
        'isMatchAmount',
        'amountDifference',
        'isMatchDate',
        'dateDifference',
        'isMatchDocument',
        'systemDocumentColumn',
        'statementDocumentColumn',
        'isUseReference',
        'statementReferenceFrom',
        'statementReferenceTo',
        'isMatchChequeNo',
        'statementChqueColumn',
        'isDefault',
        'companySystemID',
        'companyID',
        'createdDateTime',
        'timeStamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'ruleId' => 'integer',
        'bankAccountAutoID' => 'integer',
        'ruleDescription' => 'string',
        'transactionType' => 'integer',
        'matchType' => 'integer',
        'isMatchAmount' => 'integer',
        'amountDifference' => 'float',
        'isMatchDate' => 'integer',
        'dateDifference' => 'integer',
        'isMatchDocument' => 'integer',
        'systemDocumentColumn' => 'integer',
        'statementDocumentColumn' => 'integer',
        'isUseReference' => 'integer',
        'statementReferenceFrom' => 'integer',
        'statementReferenceTo' => 'integer',
        'isMatchChequeNo' => 'integer',
        'statementChqueColumn' => 'integer',
        'isDefault' => 'integer',
        'companySystemID' => 'integer',
        'companyID' => 'string',
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

    public function gettransactionTypeDescriptionAttribute()
    {
        switch ($this->transactionType) {
            case 1 :
                return "Payment Voucher";
                break;
            case 2 :
                return "Receipt Voucher";
                break;
            default :
                return null;
        }
    }
    
}
