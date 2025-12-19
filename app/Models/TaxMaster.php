<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="TaxMaster",
 *      required={""},
 *      @SWG\Property(
 *          property="taxMasterAutoID",
 *          description="taxMasterAutoID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="companySystemID",
 *          description="companySystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="companyID",
 *          description="companyID",
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
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="payeeSystemCode",
 *          description="payeeSystemCode",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="taxType",
 *          description="taxType",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="selectForPayment",
 *          description="selectForPayment",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="timestamp",
 *          description="timestamp",
 *          type="string",
 *          format="date-time"
 *      )
 * )
 */
class TaxMaster extends Model
{

    public $table = 'erp_taxmaster';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';




    public $fillable = [
        'companySystemID',
        'companyID',
        'taxShortCode',
        'taxDescription',
        'taxPercent',
        'payeeSystemCode',
        'taxType',
        'selectForPayment',
        'timestamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'taxMasterAutoID' => 'integer',
        'companySystemID' => 'integer',
        'companyID' => 'string',
        'taxShortCode' => 'string',
        'taxDescription' => 'string',
        'taxPercent' => 'float',
        'payeeSystemCode' => 'integer',
        'taxType' => 'integer',
        'selectForPayment' => 'integer',
        'timestamp' => 'datetime'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
