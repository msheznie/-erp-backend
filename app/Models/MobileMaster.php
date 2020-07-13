<?php
/**
 * =============================================
 * -- File Name : MobileMaster.php
 * -- Project Name : ERP
 * -- Module Name : MobileMaster
 * -- Author : Mohamed Rilwan
 * -- Create date : 09- July 2020
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 */
namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="MobileMaster",
 *      required={""},
 *      @SWG\Property(
 *          property="mobilemasterID",
 *          description="mobilemasterID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="empID",
 *          description="empID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="employeeSystemID",
 *          description="employeeSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="assignDate",
 *          description="assignDate",
 *          type="string",
 *          format="date"
 *      ),
 *      @SWG\Property(
 *          property="mobileNoPoolID",
 *          description="mobileNoPoolID",
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
 *          property="description",
 *          description="description",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="currentPlan",
 *          description="currentPlan",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isIDDActive",
 *          description="isIDDActive",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isRoamingActive",
 *          description="isRoamingActive",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="currency",
 *          description="currency",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="creditlimit",
 *          description="creditlimit",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="isDataRoaming",
 *          description="isDataRoaming",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isActive",
 *          description="isActive",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="datedeactivated",
 *          description="datedeactivated",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="recoverYN",
 *          description="recoverYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isInternetSim",
 *          description="isInternetSim",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="createDate",
 *          description="createDate",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="createUserID",
 *          description="createUserID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="createPCID",
 *          description="createPCID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="modifiedpc",
 *          description="modifiedpc",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="modifiedUser",
 *          description="modifiedUser",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="timestamp",
 *          description="timestamp",
 *          type="string",
 *          format="date-time"
 *      )
 * )
 */
class MobileMaster extends Model
{

    public $table = 'hrms_mobilemaster';
    
    const CREATED_AT = 'createDate';
    const UPDATED_AT = 'timestamp';

    protected $primaryKey  = 'mobilemasterID';


    public $fillable = [
        'empID',
        'employeeSystemID',
        'assignDate',
        'mobileNoPoolID',
        'mobileNo',
        'description',
        'currentPlan',
        'isIDDActive',
        'isRoamingActive',
        'currency',
        'creditlimit',
        'isDataRoaming',
        'isActive',
        'datedeactivated',
        'recoverYN',
        'isInternetSim',
        'createDate',
        'createUserID',
        'createPCID',
        'modifiedpc',
        'modifiedUser',
        'timestamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'mobilemasterID' => 'integer',
        'empID' => 'string',
        'employeeSystemID' => 'integer',
        'assignDate' => 'date',
        'mobileNoPoolID' => 'integer',
        'mobileNo' => 'integer',
        'description' => 'string',
        'currentPlan' => 'integer',
        'isIDDActive' => 'integer',
        'isRoamingActive' => 'integer',
        'currency' => 'integer',
        'creditlimit' => 'float',
        'isDataRoaming' => 'integer',
        'isActive' => 'integer',
        'datedeactivated' => 'datetime',
        'recoverYN' => 'integer',
        'isInternetSim' => 'integer',
        'createDate' => 'datetime',
        'createUserID' => 'string',
        'createPCID' => 'string',
        'modifiedpc' => 'string',
        'modifiedUser' => 'string',
        'timestamp' => 'datetime'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function mobile_no()
    {
        return $this->belongsTo('App\Models\MobileNoPool', 'mobileNoPoolID','mobilenopoolID');
    }

    public function employee()
    {
        return $this->belongsTo('App\Models\Employee', 'employeeSystemID', 'employeeSystemID');
    }
}
