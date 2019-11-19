<?php
/**
 * =============================================
 * -- File Name : HRMSPeriodMaster.php
 * -- Project Name : ERP
 * -- Module Name : Leave Application
 * -- Author : Mohamed Rilwan
 * -- Create date : 19- November 2019
 * -- Description :
 * -- REVISION HISTORY
 */
namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="HRMSPeriodMaster",
 *      required={""},
 *      @SWG\Property(
 *          property="periodMasterID",
 *          description="periodMasterID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="periodMonth",
 *          description="periodMonth",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="periodYear",
 *          description="periodYear",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="clientMonth",
 *          description="clientMonth",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="clientStartDate",
 *          description="clientStartDate",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="clientEndDate",
 *          description="clientEndDate",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="noOfDays",
 *          description="noOfDays",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="startDate",
 *          description="startDate",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="endDate",
 *          description="endDate",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="timestamp",
 *          description="timestamp",
 *          type="string",
 *          format="date-time"
 *      )
 * )
 */
class HRMSPeriodMaster extends Model
{

    public $table = 'hrms_periodmaster';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';



    public $fillable = [
        'periodMonth',
        'periodYear',
        'clientMonth',
        'clientStartDate',
        'clientEndDate',
        'noOfDays',
        'startDate',
        'endDate',
        'timestamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'periodMasterID' => 'integer',
        'periodMonth' => 'string',
        'periodYear' => 'integer',
        'clientMonth' => 'string',
        'clientStartDate' => 'string',
        'clientEndDate' => 'string',
        'noOfDays' => 'integer',
        'startDate' => 'datetime',
        'endDate' => 'datetime',
        'timestamp' => 'datetime'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'periodMasterID' => 'required'
    ];

    
}
