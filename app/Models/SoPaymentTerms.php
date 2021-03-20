<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="SoPaymentTerms",
 *      required={""},
 *      @SWG\Property(
 *          property="paymentTermID",
 *          description="paymentTermID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="paymentTermsCategory",
 *          description="paymentTermsCategory",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="soID",
 *          description="soID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="paymentTemDes",
 *          description="paymentTemDes",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="comAmount",
 *          description="comAmount",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="comPercentage",
 *          description="comPercentage",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="inDays",
 *          description="inDays",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="comDate",
 *          description="comDate",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="LCPaymentYN",
 *          description="0 is not an LC payment. 1 is an LC payment",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isRequested",
 *          description="isRequested",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="createdDateTime",
 *          description="createdDateTime",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="timestamp",
 *          description="timestamp",
 *          type="string",
 *          format="date-time"
 *      )
 * )
 */
class SoPaymentTerms extends Model
{

    public $table = 'erp_sopaymentterms';

    const CREATED_AT = 'createdDateTime';
    const UPDATED_AT = 'timeStamp';

    protected $primaryKey = 'paymentTermID';




    public $fillable = [
        'paymentTermsCategory',
        'soID',
        'paymentTemDes',
        'comAmount',
        'comPercentage',
        'inDays',
        'comDate',
        'LCPaymentYN',
        'isRequested',
        'createdDateTime',
        'timestamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'paymentTermID' => 'integer',
        'paymentTermsCategory' => 'integer',
        'soID' => 'integer',
        'paymentTemDes' => 'string',
        'comAmount' => 'float',
        'comPercentage' => 'float',
        'inDays' => 'integer',
        'comDate' => 'datetime',
        'LCPaymentYN' => 'integer',
        'isRequested' => 'integer',
        'createdDateTime' => 'datetime',
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
