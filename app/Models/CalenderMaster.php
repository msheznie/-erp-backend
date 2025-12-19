<?php
/**
 * =============================================
 * -- File Name : CalenderMaster.php
 * -- Project Name : ERP
 * -- Module Name : Leave Application
 * -- Author : Mohamed Rilwan
 * -- Create date : 01 - September 2019
 * -- Description :
 * -- REVISION HISTORY
 */
namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="CalenderMaster",
 *      required={""},
 *      @SWG\Property(
 *          property="calenderID",
 *          description="calenderID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="calDate",
 *          description="calDate",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="calMonth",
 *          description="calMonth",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="calYear",
 *          description="calYear",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isWorkingDay",
 *          description="Working Day = -1
Non Working Day =0",
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
class CalenderMaster extends Model
{

    public $table = 'hrms_calender';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';



    public $fillable = [
        'calDate',
        'calMonth',
        'calYear',
        'isWorkingDay',
        'timestamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'calenderID' => 'integer',
        'calDate' => 'datetime',
        'calMonth' => 'integer',
        'calYear' => 'integer',
        'isWorkingDay' => 'integer',
        'timestamp' => 'datetime'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'calenderID' => 'required'
    ];

    
}
