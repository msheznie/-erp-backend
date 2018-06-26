<?php
/**
 * =============================================
 * -- File Name : CompanyFinanceYear.php
 * -- Project Name : ERP
 * -- Module Name :  Company Finance Year
 * -- Author : Mohamed Nazir
 * -- Create date : 12 - June 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 */
namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="CompanyFinanceYear",
 *      required={""},
 *      @SWG\Property(
 *          property="companyFinanceYearID",
 *          description="companyFinanceYearID",
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
 *          property="isActive",
 *          description="isActive",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isCurrent",
 *          description="isCurrent",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isClosed",
 *          description="isClosed",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="closedByEmpSystemID",
 *          description="closedByEmpSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="closedByEmpID",
 *          description="closedByEmpID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="closedByEmpName",
 *          description="closedByEmpName",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="comments",
 *          description="comments",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="createdUserGroup",
 *          description="createdUserGroup",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="createdUserID",
 *          description="createdUserID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="createdPcID",
 *          description="createdPcID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="modifiedUser",
 *          description="modifiedUser",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="modifiedPc",
 *          description="modifiedPc",
 *          type="string"
 *      )
 * )
 */
class CompanyFinanceYear extends Model
{

    public $table = 'companyfinanceyear';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';



    public $fillable = [
        'companySystemID',
        'companyID',
        'bigginingDate',
        'endingDate',
        'isActive',
        'isCurrent',
        'isClosed',
        'closedByEmpSystemID',
        'closedByEmpID',
        'closedByEmpName',
        'closedDate',
        'comments',
        'createdUserGroup',
        'createdUserID',
        'createdPcID',
        'createdDateTime',
        'modifiedUser',
        'modifiedPc',
        'timeStamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'companyFinanceYearID' => 'integer',
        'companySystemID' => 'integer',
        'companyID' => 'string',
        'isActive' => 'integer',
        'isCurrent' => 'integer',
        'isClosed' => 'integer',
        'closedByEmpSystemID' => 'integer',
        'closedByEmpID' => 'string',
        'closedByEmpName' => 'string',
        'comments' => 'string',
        'createdUserGroup' => 'string',
        'createdUserID' => 'string',
        'createdPcID' => 'string',
        'modifiedUser' => 'string',
        'modifiedPc' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
