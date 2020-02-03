<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="LptPermission",
 *      required={""},
 *      @SWG\Property(
 *          property="autoID",
 *          description="autoID",
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
 *          property="companyID",
 *          description="companyID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="isLPTReview",
 *          description="0 - denied, 1 - access  ",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isLPTClose",
 *          description="0-denied, 1-active",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="createdBy",
 *          description="createdBy",
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
class LptPermission extends Model
{

    public $table = 'qhse_lpt_permission';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';



    public $fillable = [
        'empID',
        'employeeSystemID',
        'companyID',
        'isLPTReview',
        'isLPTClose',
        'createdBy',
        'timestamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'autoID' => 'integer',
        'empID' => 'string',
        'employeeSystemID' => 'integer',
        'companyID' => 'string',
        'isLPTReview' => 'integer',
        'isLPTClose' => 'integer',
        'createdBy' => 'string',
        'timestamp' => 'datetime'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'autoID' => 'required'
    ];

    public function employee(){
        return $this->belongsTo('App\Models\Employee','employeeSystemID','employeeSystemID');
    }
}
