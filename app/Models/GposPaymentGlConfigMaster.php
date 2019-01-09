<?php
/**
 * =============================================
 * -- File Name : GposPaymentGlConfigMaster.php
 * -- Project Name : ERP
 * -- Module Name :  General pos Payment Gl Config Master
 * -- Author : Fayas
 * -- Create date : 08 - January 2019
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 */
namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="GposPaymentGlConfigMaster",
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
 *          description="glAccountType",
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
 *          description="isActive",
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
 *      )
 * )
 */
class GposPaymentGlConfigMaster extends Model
{

    public $table = 'erp_gpos_paymentglconfigmaster';

    const CREATED_AT = 'timesstamp';
    const UPDATED_AT = 'timesstamp';

    protected $primaryKey = 'autoID';


    public $fillable = [
        'description',
        'glAccountType',
        'queryString',
        'image',
        'isActive',
        'sortOrder',
        'selectBoxName',
        'timesstamp'
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
        'selectBoxName' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
