<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="POSSourcePaymentGlConfig",
 *      required={""},
 *      @SWG\Property(
 *          property="autoID",
 *          description="autoID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="description",
 *          description="description",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="glAccountType",
 *          description="1 - Bank 2 - Card 3 - Liability  4 - expense (from srp_erp_chartofaccount)",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="queryString",
 *          description="queryString",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="image",
 *          description="image",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="isActive",
 *          description="1- Active 0- Inactive",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="sortOrder",
 *          description="sortOrder",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="selectBoxName",
 *          description="selectBoxName",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="timesstamp",
 *          description="timesstamp",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="transaction_log_id",
 *          description="transaction_log_id",
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class POSSourcePaymentGlConfig extends Model
{

    public $table = 'pos_source_paymentglconfigmaster';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';




    public $fillable = [
        'description',
        'glAccountType',
        'queryString',
        'image',
        'isActive',
        'sortOrder',
        'selectBoxName',
        'timesstamp',
        'transaction_log_id'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'autoID' => 'integer',
        'description' => 'string',
        'glAccountType' => 'integer',
        'queryString' => 'string',
        'image' => 'string',
        'isActive' => 'integer',
        'sortOrder' => 'integer',
        'selectBoxName' => 'string',
        'timesstamp' => 'datetime',
        'transaction_log_id' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'description' => 'required',
        'glAccountType' => 'required'
    ];

    
}
