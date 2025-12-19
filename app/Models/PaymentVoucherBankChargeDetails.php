<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @OA\Schema(
 *      schema="PaymentVoucherBankChargeDetails",
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
 *          property="payMasterAutoID",
 *          description="payMasterAutoID",
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
 *          property="companySystemID",
 *          description="companySystemID",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="chartOfAccountSystemID",
 *          description="chartOfAccountSystemID",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="glCode",
 *          description="glCode",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="glCodeDescription",
 *          description="glCodeDes",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="serviceLineSystemID",
 *          description="serviceLineSystemID",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="serviceLineCode",
 *          description="serviceLineCode",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="dpAmountCurrency",
 *          description="dpAmountCurrency",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="dpAmountCurrencyER",
 *          description="dpAmountCurrencyER",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="dpAmount",
 *          description="dpAmount",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="number",
 *          format="number"
 *      ),
 *      @OA\Property(
 *          property="localCurrency",
 *          description="localCurrency",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="localCurrencyER",
 *          description="localCurrencyER",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="localAmount",
 *          description="localAmount",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="number",
 *          format="number"
 *      ),
 *      @OA\Property(
 *          property="comRptCurrency",
 *          description="comRptCurrency",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="comRptCurrencyER",
 *          description="comRptCurrencyER",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="comRptAmount",
 *          description="comRptAmount",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="number",
 *          format="number"
 *      ),
 *      @OA\Property(
 *          property="comment",
 *          description="comment",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string"
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
class PaymentVoucherBankChargeDetails extends Model
{

    public $table = 'pv_bank_charges';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $primaryKey  = 'id';


    public $fillable = [
        'payMasterAutoID',
        'companyID',
        'companySystemID',
        'chartOfAccountSystemID',
        'glCode',
        'glCodeDescription',
        'serviceLineSystemID',
        'serviceLineCode',
        'dpAmountCurrency',
        'dpAmountCurrencyER',
        'dpAmount',
        'localCurrency',
        'localCurrencyER',
        'localAmount',
        'comRptCurrency',
        'comRptCurrencyER',
        'comRptAmount',
        'comment'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'payMasterAutoID' => 'integer',
        'companyID' => 'string',
        'companySystemID' => 'integer',
        'chartOfAccountSystemID' => 'string',
        'glCode' => 'string',
        'glCodeDescription' => 'string',
        'serviceLineSystemID' => 'integer',
        'serviceLineCode' => 'string',
        'dpAmountCurrency' => 'integer',
        'dpAmountCurrencyER' => 'string',
        'dpAmount' => 'float',
        'localCurrency' => 'integer',
        'localCurrencyER' => 'string',
        'localAmount' => 'float',
        'comRptCurrency' => 'integer',
        'comRptCurrencyER' => 'string',
        'comRptAmount' => 'float',
        'comment' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function master(){
        return $this->belongsTo('App\Models\PaySupplierInvoiceMaster', 'payMasterAutoID', 'PayMasterAutoId');
    }

    public function segment(){
        return $this->belongsTo('App\Models\SegmentMaster','serviceLineSystemID','serviceLineSystemID');
    }
    
}
