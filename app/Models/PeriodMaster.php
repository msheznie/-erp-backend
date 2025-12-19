<?php
/**
 * =============================================
 * -- File Name : PeriodMaster.php
 * -- Project Name : ERP
 * -- Module Name : Period Master
 * -- Author : Mohamed Fayas
 * -- Create date : 07 - November 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 */
namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="PeriodMaster",
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
 *      )
 * )
 */
class PeriodMaster extends Model
{

    public $table = 'hrms_periodmaster';
    
    const CREATED_AT = 'timestamp';
    const UPDATED_AT = 'timestamp';
    protected $primaryKey  = 'periodMasterID';


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
        'noOfDays' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
