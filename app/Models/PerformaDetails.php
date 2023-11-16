<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="PerformaDetails",
 *      required={""},
 *      @SWG\Property(
 *          property="idperformaDetails",
 *          description="idperformaDetails",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="companyID",
 *          description="companyID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="serviceLine",
 *          description="serviceLine",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="customerID",
 *          description="customerID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="contractID",
 *          description="contractID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="performaMasterID",
 *          description="performaMasterID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="performaCode",
 *          description="performaCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="ticketNo",
 *          description="ticketNo",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="currencyID",
 *          description="currencyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="totAmount",
 *          description="totAmount",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="financeGLcode",
 *          description="financeGLcode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="invoiceSsytemCode",
 *          description="invoiceSsytemCode",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="vendorCode",
 *          description="vendorCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="bankID",
 *          description="bankID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="accountID",
 *          description="accountID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="paymentPeriodDays",
 *          description="paymentPeriodDays",
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class PerformaDetails extends Model
{

    public $table = 'erp_performadetails';
    
    const CREATED_AT = 'timestamp';
    const UPDATED_AT = 'timestamp';

    protected $primaryKey  = 'idperformaDetails';

    public $fillable = [
        'companyID',
        'serviceLine',
        'customerID',
        'contractID',
        'performaMasterID',
        'performaCode',
        'ticketNo',
        'currencyID',
        'totAmount',
        'financeGLcode',
        'invoiceSsytemCode',
        'vendorCode',
        'bankID',
        'accountID',
        'paymentPeriodDays',
        'timestamp',
        'isDiscount'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'idperformaDetails' => 'integer',
        'companyID' => 'string',
        'serviceLine' => 'string',
        'customerID' => 'integer',
        'contractID' => 'string',
        'performaMasterID' => 'integer',
        'performaCode' => 'string',
        'ticketNo' => 'integer',
        'currencyID' => 'integer',
        'totAmount' => 'float',
        'financeGLcode' => 'string',
        'invoiceSsytemCode' => 'integer',
        'vendorCode' => 'string',
        'bankID' => 'integer',
        'accountID' => 'integer',
        'paymentPeriodDays' => 'integer',
        'isDiscount'  => 'integer'

    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];


    public function freebillingmaster()
    {
        return $this->belongsTo('App\Models\FreeBillingMasterPerforma','performaMasterID',  'performaMasterID');
    }

    public function PerformaTemp() {
        return $this->belongsTo('App\Models\PerformaTemp','performaMasterID',  'performaMasterID');
    }



    
}
