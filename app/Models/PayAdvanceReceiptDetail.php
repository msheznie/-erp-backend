<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @OA\Schema(
 *      schema="PayAdvanceReceiptDetail",
 *      required={""},
 *      @OA\Property(
 *          property="payAdvanceReceiptDetailAutoID",
 *          description="payAdvanceReceiptDetailAutoID",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="PayMasterAutoId",
 *          description="PayMasterAutoId",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="advanceReceiptAutoID",
 *          description="advanceReceiptAutoID",
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
 *          property="advanceReceiptAmount",
 *          description="advanceReceiptAmount",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="number",
 *          format="number"
 *      ),
 *      @OA\Property(
 *          property="advanceReceiptAmountLocal",
 *          description="advanceReceiptAmountLocal",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="number",
 *          format="number"
 *      ),
 *      @OA\Property(
 *          property="advanceReceiptAmountRpt",
 *          description="advanceReceiptAmountRpt",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="number",
 *          format="number"
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
class PayAdvanceReceiptDetail extends Model
{
    public $table = 'erp_pay_advance_receipt_details';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $primaryKey = 'payAdvanceReceiptDetailAutoID';

    public $fillable = [
        'PayMasterAutoId',
        'advanceReceiptAutoID',
        'companySystemID',
        'advanceReceiptAmount',
        'advanceReceiptAmountLocal',
        'advanceReceiptAmountRpt',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'payAdvanceReceiptDetailAutoID' => 'integer',
        'PayMasterAutoId' => 'integer',
        'advanceReceiptAutoID' => 'integer',
        'companySystemID' => 'integer',
        'advanceReceiptAmount' => 'float',
        'advanceReceiptAmountLocal' => 'float',
        'advanceReceiptAmountRpt' => 'float',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'PayMasterAutoId' => 'required',
        'advanceReceiptAutoID' => 'required',
        'companySystemID' => 'required',
        'advanceReceiptAmount' => 'required',
        'advanceReceiptAmountLocal' => 'required',
        'advanceReceiptAmountRpt' => 'required',
    ];

    public function master(): BelongsTo
    {
        return $this->belongsTo(PaySupplierInvoiceMaster::class, 'PayMasterAutoId', 'PayMasterAutoId');
    }

    public function advanceReceipt(): BelongsTo
    {
        return $this->belongsTo(CustomerReceivePayment::class, 'advanceReceiptAutoID', 'custReceivePaymentAutoID');
    }
}

