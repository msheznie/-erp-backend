<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="Taxdetail",
 *      required={""},
 *      @SWG\Property(
 *          property="taxDetailID",
 *          description="taxDetailID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="taxMasterAutoID",
 *          description="taxMasterAutoID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="companyID",
 *          description="companyID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="documentID",
 *          description="documentID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="documentSystemCode",
 *          description="documentSystemCode",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="documentCode",
 *          description="documentCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="taxShortCode",
 *          description="taxShortCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="taxDescription",
 *          description="taxDescription",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="taxPercent",
 *          description="taxPercent",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="payeeSystemCode",
 *          description="payeeSystemCode",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="payeeCode",
 *          description="payeeCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="payeeName",
 *          description="payeeName",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="currency",
 *          description="currency",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="currencyER",
 *          description="currencyER",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="amount",
 *          description="amount",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="payeeDefaultCurrencyID",
 *          description="payeeDefaultCurrencyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="payeeDefaultCurrencyER",
 *          description="payeeDefaultCurrencyER",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="payeeDefaultAmount",
 *          description="payeeDefaultAmount",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="localCurrencyID",
 *          description="localCurrencyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="localCurrencyER",
 *          description="localCurrencyER",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="localAmount",
 *          description="localAmount",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="rptCurrencyID",
 *          description="rptCurrencyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="rptCurrencyER",
 *          description="rptCurrencyER",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="rptAmount",
 *          description="rptAmount",
 *          type="number",
 *          format="float"
 *      )
 * )
 */
class Taxdetail extends Model
{

    public $table = 'erp_taxdetail';
    
    const CREATED_AT = 'timestamp';
    const UPDATED_AT = 'timestamp';

    protected $primaryKey  = 'taxDetailID';

    public $fillable = [
        'taxMasterAutoID',
        'companyID',
        'companySystemID',
        'documentID',
        'documentSystemID',
        'documentSystemCode',
        'documentCode',
        'taxShortCode',
        'taxDescription',
        'taxPercent',
        'payeeSystemCode',
        'payeeCode',
        'payeeName',
        'currency',
        'currencyER',
        'amount',
        'payeeDefaultCurrencyID',
        'payeeDefaultCurrencyER',
        'payeeDefaultAmount',
        'localCurrencyID',
        'localCurrencyER',
        'localAmount',
        'rptCurrencyID',
        'rptCurrencyER',
        'rptAmount',
        'timestamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'taxDetailID' => 'integer',
        'taxMasterAutoID' => 'integer',
        'companyID' => 'string',
        'companySystemID' => 'integer',
        'documentID' => 'string',
        'documentSystemID' => 'integer',
        'documentSystemCode' => 'integer',
        'documentCode' => 'string',
        'taxShortCode' => 'string',
        'taxDescription' => 'string',
        'taxPercent' => 'float',
        'payeeSystemCode' => 'integer',
        'payeeCode' => 'string',
        'payeeName' => 'string',
        'currency' => 'integer',
        'currencyER' => 'float',
        'amount' => 'float',
        'payeeDefaultCurrencyID' => 'integer',
        'payeeDefaultCurrencyER' => 'float',
        'payeeDefaultAmount' => 'float',
        'localCurrencyID' => 'integer',
        'localCurrencyER' => 'float',
        'localAmount' => 'float',
        'rptCurrencyID' => 'integer',
        'rptCurrencyER' => 'float',
        'rptAmount' => 'float'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
