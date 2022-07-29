<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="StagCustomerTypeMaster",
 *      required={""},
 *      @SWG\Property(
 *          property="customerTypeID",
 *          description="customerTypeID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="customerDescription",
 *          description="customerDescription",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="displayDescription",
 *          description="displayDescription",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="isThirdPartyDelivery",
 *          description="this flag=1 will disable the delivery popup and thirdparty delivery information in payment widnow",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isDineIn",
 *          description="this falg=1 if it dine-in or eat-in order, we used this in service charge include template to remove the service charge from dine-in or eat-in order - so this most important for calculations",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isDefault",
 *          description="drop down option, 1 = > default selected value",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="company_id",
 *          description="FK of company_id  company table ",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="createdBy",
 *          description="createdBy",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="createdDatetime",
 *          description="createdDatetime",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="createdPc",
 *          description="createdPc",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="timestamp",
 *          description="timestamp",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="imageName",
 *          description="imageName",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="transaction_log_id",
 *          description="transaction_log_id",
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class StagCustomerTypeMaster extends Model
{

    public $table = 'stag_customertypemaster';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';




    public $fillable = [
        'customerDescription',
        'displayDescription',
        'isThirdPartyDelivery',
        'isDineIn',
        'isDefault',
        'company_id',
        'createdBy',
        'createdDatetime',
        'createdPc',
        'timestamp',
        'imageName',
        'transaction_log_id'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'customerTypeID' => 'integer',
        'customerDescription' => 'string',
        'displayDescription' => 'string',
        'isThirdPartyDelivery' => 'integer',
        'isDineIn' => 'integer',
        'isDefault' => 'integer',
        'company_id' => 'integer',
        'createdBy' => 'string',
        'createdDatetime' => 'datetime',
        'createdPc' => 'string',
        'timestamp' => 'datetime',
        'imageName' => 'string',
        'transaction_log_id' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
