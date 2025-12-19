<?php
/**
 * =============================================
 * -- File Name : HRMSLeaveAccrualPolicyType.php
 * -- Project Name : ERP
 * -- Module Name : Leave
 * -- Author : Mohamed Rilwan
 * -- Create date : 25- November 2019
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 */
namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="HRMSLeaveAccrualPolicyType",
 *      required={""},
 *      @SWG\Property(
 *          property="leaveaccrualpolicyTypeID",
 *          description="leaveaccrualpolicyTypeID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="description",
 *          description="description",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="isOnlyFemale",
 *          description="isOnlyFemale",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isOnlyMuslim",
 *          description="isOnlyMuslim",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isExpat",
 *          description="isExpat",
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
class HRMSLeaveAccrualPolicyType extends Model
{

    public $table = 'hrms_leaveaccrualpolicytype';
    
    const CREATED_AT = 'timestamp';
    const UPDATED_AT = 'timestamp';



    public $fillable = [
        'description',
        'isOnlyFemale',
        'isOnlyMuslim',
        'isExpat',
        'timestamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'leaveaccrualpolicyTypeID' => 'integer',
        'description' => 'string',
        'isOnlyFemale' => 'integer',
        'isOnlyMuslim' => 'integer',
        'isExpat' => 'integer',
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
