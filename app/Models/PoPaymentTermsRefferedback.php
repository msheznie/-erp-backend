<?php
/**
 * =============================================
 * -- File Name : PoPaymentTermsRefferedback.php
 * -- Project Name : ERP
 * -- Module Name :  PoPaymentTermsRefferedback
 * -- Author : Nazir
 * -- Create date : 23 - July 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 * --
 */
namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="PoPaymentTermsRefferedback",
 *      required={""},
 *      @SWG\Property(
 *          property="POTermsRefferedBackID",
 *          description="POTermsRefferedBackID",
 *          type="integer",
 *          format="int32"
 *      ),
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
 *          property="poID",
 *          description="poID",
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
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="comPercentage",
 *          description="comPercentage",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="inDays",
 *          description="inDays",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="LCPaymentYN",
 *          description="LCPaymentYN",
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
 *          property="timesReferred",
 *          description="timesReferred",
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class PoPaymentTermsRefferedback extends Model
{

    public $table = 'erp_popaymenttermsrefferedback';

    const CREATED_AT = 'createdDateTime';
    const UPDATED_AT = 'timeStamp';

    protected $primaryKey = 'POTermsRefferedBackID';

    public $fillable = [
        'paymentTermID',
        'paymentTermsCategory',
        'poID',
        'paymentTemDes',
        'comAmount',
        'comPercentage',
        'inDays',
        'comDate',
        'LCPaymentYN',
        'isRequested',
        'timesReferred',
        'createdDateTime',
        'timestamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'POTermsRefferedBackID' => 'integer',
        'paymentTermID' => 'integer',
        'paymentTermsCategory' => 'integer',
        'poID' => 'integer',
        'paymentTemDes' => 'string',
        'comAmount' => 'float',
        'comPercentage' => 'float',
        'inDays' => 'integer',
        'LCPaymentYN' => 'integer',
        'isRequested' => 'integer',
        'timesReferred' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];


    
}
