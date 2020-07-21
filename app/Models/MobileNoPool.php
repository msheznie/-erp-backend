<?php
/**
 * =============================================
 * -- File Name : MobileNoPool.php
 * -- Project Name : ERP
 * -- Module Name : MobileNoPool
 * -- Author : Mohamed Rilwan
 * -- Create date : 09- July 2020
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 */
namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="MobileNoPool",
 *      required={""},
 *      @SWG\Property(
 *          property="mobilenopoolID",
 *          description="mobilenopoolID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="mobileNo",
 *          description="mobileNo",
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
 *          property="isRoaming",
 *          description="isRoaming",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isIDD",
 *          description="isIDD",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="mobilePlan",
 *          description="mobilePlan",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isMobileDataActivated",
 *          description="isMobileDataActivated",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isDataRoaming",
 *          description="isDataRoaming",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="DataLimit",
 *          description="DataLimit",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="isAssigned",
 *          description="isAssigned",
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
class MobileNoPool extends Model
{

    public $table = 'hrms_mobilenopool';
    
    const CREATED_AT = 'timestamp';
    const UPDATED_AT = 'timestamp';

    protected $primaryKey  = 'mobilenopoolID';


    public $fillable = [
        'mobileNo',
        'companySystemID',
        'companyID',
        'isRoaming',
        'isIDD',
        'mobilePlan',
        'isMobileDataActivated',
        'isDataRoaming',
        'DataLimit',
        'isAssigned',
        'timestamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'mobilenopoolID' => 'integer',
        'mobileNo' => 'integer',
        'companySystemID' => 'integer',
        'companyID' => 'string',
        'isRoaming' => 'integer',
        'isIDD' => 'integer',
        'mobilePlan' => 'integer',
        'isMobileDataActivated' => 'integer',
        'isDataRoaming' => 'integer',
        'DataLimit' => 'string',
        'isAssigned' => 'integer',
        'timestamp' => 'datetime'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function mobile_master()
    {
        return $this->hasOne('App\Models\MobileMaster', 'mobileNo','mobileNo');
    }
}
