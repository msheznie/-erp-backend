<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="UserRights",
 *      required={""},
 *      @SWG\Property(
 *          property="userRightsID",
 *          description="userRightsID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="employeeID",
 *          description="employeeID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="groupMasterID",
 *          description="groupMasterID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="pageMasterID",
 *          description="pageMasterID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="moduleMasterID",
 *          description="moduleMasterID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="companyID",
 *          description="companyID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="V",
 *          description="view",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="A",
 *          description="Add ",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="E",
 *          description="Edit",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="D",
 *          description="Delete",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="P",
 *          description="Print",
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
class UserRights extends Model
{

    public $table = 'ar_userrights';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';



    public $fillable = [
        'employeeID',
        'employeeSystemID',
        'groupMasterID',
        'pageMasterID',
        'moduleMasterID',
        'companyID',
        'V',
        'A',
        'E',
        'D',
        'P',
        'timestamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'userRightsID' => 'integer',
        'employeeSystemID' => 'integer',
        'employeeID' => 'string',
        'groupMasterID' => 'integer',
        'pageMasterID' => 'integer',
        'moduleMasterID' => 'integer',
        'companyID' => 'string',
        'V' => 'integer',
        'A' => 'integer',
        'E' => 'integer',
        'D' => 'integer',
        'P' => 'integer',
        'timestamp' => 'datetime'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'userRightsID' => 'required',
        'timestamp' => 'required'
    ];

    public function employee(){
        return $this->belongsTo('App\Models\Employee','employeeSystemID','employeeSystemID');
    }
}
