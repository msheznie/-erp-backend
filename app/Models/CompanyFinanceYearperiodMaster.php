<?php
/**
 * =============================================
 * -- File Name : CompanyFinanceYearperiodMaster.php
 * -- Project Name : ERP
 * -- Module Name :  Company Finance Year period Master
 * -- Author : Mohamed Fayas
 * -- Create date : 28 - December 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 */
namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="CompanyFinanceYearperiodMaster",
 *      required={""},
 *      @SWG\Property(
 *          property="companyFinanceYearPeriodMasterID",
 *          description="companyFinanceYearPeriodMasterID",
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
 *          property="companyFinanceYearID",
 *          description="companyFinanceYearID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="timeStamp",
 *          description="timeStamp",
 *          type="string"
 *      )
 * )
 */
class CompanyFinanceYearperiodMaster extends Model
{

    public $table = 'companyfinanceyearperiodmaster';
    
    const CREATED_AT = 'timeStamp';
    const UPDATED_AT = 'timeStamp';
    protected $primaryKey  = 'companyFinanceYearPeriodMasterID';


    public $fillable = [
        'companySystemID',
        'companyID',
        'companyFinanceYearID',
        'dateFrom',
        'dateTo',
        'timeStamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'companyFinanceYearPeriodMasterID' => 'integer',
        'companySystemID' => 'integer',
        'companyID' => 'string',
        'companyFinanceYearID' => 'integer',
        'timeStamp' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
