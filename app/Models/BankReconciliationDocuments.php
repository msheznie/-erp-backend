<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @OA\Schema(
 *      schema="BankReconciliationDocuments",
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
 *          property="bankRecAutoID",
 *          description="bankRecAutoID",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="documentSystemID",
 *          description="documentSystemID",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="documentAutoId",
 *          description="documentAutoId",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class BankReconciliationDocuments extends Model
{

    public $table = 'bank_reconciliation_documents';

    public $timestamps = false;

    protected $primaryKey = 'id';

    public $fillable = [
        'bankRecAutoID',
        'documentSystemID',
        'documentAutoId'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'bankRecAutoID' => 'integer',
        'documentSystemID' => 'integer',
        'documentAutoId' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function paymentVoucherDetails()
    {
        return $this->hasOne('App\Models\PaySupplierInvoiceMaster', 'PayMasterAutoId', 'documentAutoId')
            ->where('documentSystemID', '4');
    }

    public function receiveVoucherDetails()
    {
        return $this->hasOne('App\Models\CustomerReceivePayment', 'custReceivePaymentAutoID', 'documentAutoId')
            ->where('documentSystemID', '21');
    }
}
