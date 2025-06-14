<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="ServiceLine",
 *      required={""},
 *      @SWG\Property(
 *          property="serviceLineSystemID",
 *          description="serviceLineSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="ServiceLineCode",
 *          description="ServiceLineCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="serviceLineMasterCode",
 *          description="serviceLineMasterCode",
 *          type="string"
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
 *          property="ServiceLineDes",
 *          description="ServiceLineDes",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="locationID",
 *          description="locationID",
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
 *          property="isPublic",
 *          description="isPublic",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isServiceLine",
 *          description="0 serviceline 1- Product Line",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isDepartment",
 *          description="isDepartment",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isMaster",
 *          description="isMaster",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="consoleCode",
 *          description="consoleCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="consoleDescription",
 *          description="consoleDescription",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="createdUserGroup",
 *          description="createdUserGroup",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="createdPcID",
 *          description="createdPcID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="createdUserID",
 *          description="createdUserID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="modifiedPc",
 *          description="modifiedPc",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="modifiedUser",
 *          description="modifiedUser",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="createdDateTime",
 *          description="createdDateTime",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="timeStamp",
 *          description="timeStamp",
 *          type="string",
 *          format="date-time"
 *      )
 * )
 */
class ServiceLine extends Model
{

    public $table = 'serviceline';
    
    const CREATED_AT = 'timeStamp';
    const UPDATED_AT = 'timeStamp';


   /* protected $dates = ['deleted_at'];*/


    public $fillable = [
        'ServiceLineCode',
        'serviceLineMasterCode',
        'companySystemID',
        'companyID',
        'ServiceLineDes',
        'locationID',
        'isActive',
        'isPublic',
        'isServiceLine',
        'isDepartment',
        'isMaster',
        'createdUserGroup',
        'createdPcID',
        'createdUserID',
        'modifiedPc',
        'modifiedUser',
        'createdDateTime',
        'timeStamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'serviceLineSystemID' => 'integer',
        'ServiceLineCode' => 'string',
        'serviceLineMasterCode' => 'string',
        'companySystemID' => 'integer',
        'companyID' => 'string',
        'ServiceLineDes' => 'string',
        'locationID' => 'integer',
        'isActive' => 'integer',
        'isPublic' => 'integer',
        'isServiceLine' => 'integer',
        'isDepartment' => 'integer',
        'isMaster' => 'integer',
        'createdUserGroup' => 'string',
        'createdPcID' => 'string',
        'createdUserID' => 'string',
        'modifiedPc' => 'string',
        'modifiedUser' => 'string',
        'createdUserSystemID' => 'integer',
        'RollLevForApp_curr' => 'integer',
        'documentSystemID' => 'integer',
        'modifiedUserSystemID' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];
}
