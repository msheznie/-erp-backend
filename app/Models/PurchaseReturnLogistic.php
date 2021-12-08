<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="PurchaseReturnLogistic",
 *      required={""},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="grvAutoID",
 *          description="grvAutoID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="grvDetailID",
 *          description="grvDetailID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="purchaseReturnID",
 *          description="purchaseReturnID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="purchaseReturnDetailID",
 *          description="purchaseReturnDetailID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="logisticAmountTrans",
 *          description="logisticAmountTrans",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="logisticAmountRpt",
 *          description="logisticAmountRpt",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="logisticAmountLocal",
 *          description="logisticAmountLocal",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="logisticVATAmount",
 *          description="logisticVATAmount",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="logisticVATAmountLocal",
 *          description="logisticVATAmountLocal",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="logisticVATAmountRpt",
 *          description="logisticVATAmountRpt",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="UnbilledGRVAccountSystemID",
 *          description="UnbilledGRVAccountSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="supplierID",
 *          description="supplierID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="supplierTransactionCurrencyID",
 *          description="supplierTransactionCurrencyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="created_at",
 *          description="created_at",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="updated_at",
 *          description="updated_at",
 *          type="string",
 *          format="date-time"
 *      )
 * )
 */
class PurchaseReturnLogistic extends Model
{

    public $table = 'purchase_return_logistic';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';




    public $fillable = [
        'grvAutoID',
        'grvDetailID',
        'purchaseReturnID',
        'purchaseReturnDetailID',
        'logisticAmountTrans',
        'logisticAmountRpt',
        'logisticAmountLocal',
        'logisticVATAmount',
        'logisticVATAmountLocal',
        'logisticVATAmountRpt',
        'UnbilledGRVAccountSystemID',
        'supplierID',
        'vatSubCategoryID',
        'poAdvPaymentID',
        'supplierTransactionCurrencyID'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'grvAutoID' => 'integer',
        'grvDetailID' => 'integer',
        'purchaseReturnID' => 'integer',
        'purchaseReturnDetailID' => 'integer',
        'logisticAmountTrans' => 'float',
        'logisticAmountRpt' => 'float',
        'logisticAmountLocal' => 'float',
        'logisticVATAmount' => 'float',
        'logisticVATAmountLocal' => 'float',
        'logisticVATAmountRpt' => 'float',
        'UnbilledGRVAccountSystemID' => 'integer',
        'vatSubCategoryID' => 'integer',
        'supplierID' => 'integer',
        'poAdvPaymentID' => 'integer',
        'supplierTransactionCurrencyID' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

     public function logistic_data()
    {
        return $this->belongsTo('App\Models\PoAdvancePayment', 'poAdvPaymentID', 'poAdvPaymentID');
    }
}
