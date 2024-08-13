<?php
/**
 * =============================================
 * -- File Name : CompanyFinancePeriod.php
 * -- Project Name : ERP
 * -- Module Name :  Company Finance Period
 * -- Author : Mohamed Nazir
 * -- Create date : 12 - June 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 */
namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="CompanyFinancePeriod",
 *      required={""},
 *      @SWG\Property(
 *          property="companyFinancePeriodID",
 *          description="companyFinancePeriodID",
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
 *          property="departmentSystemID",
 *          description="departmentSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="departmentID",
 *          description="departmentID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="companyFinanceYearID",
 *          description="companyFinanceYearID",
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
 *          property="closedByEmpID",
 *          description="closedByEmpID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="closedByEmpSystemID",
 *          description="closedByEmpSystemID",
 *          type="integer",
 *          format="int32"
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
class CompanyFinancePeriod extends Model
{

    public $table = 'companyfinanceperiod';
    
    const CREATED_AT = 'createdDateTime';
    const UPDATED_AT = 'timeStamp';

    protected $primaryKey='companyFinancePeriodID';



    public $fillable = [
        'companySystemID',
        'companyID',
        'departmentSystemID',
        'departmentID',
        'companyFinanceYearID',
        'dateFrom',
        'dateTo',
        'isActive',
        'isCurrent',
        'isClosed',
        'closedByEmpID',
        'closedByEmpSystemID',
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
        'companyFinancePeriodID' => 'integer',
        'companySystemID' => 'integer',
        'companyID' => 'string',
        'departmentSystemID' => 'integer',
        'departmentID' => 'string',
        'companyFinanceYearID' => 'integer',
        'isActive' => 'integer',
        'isCurrent' => 'integer',
        'isClosed' => 'integer',
        'closedByEmpID' => 'string',
        'closedByEmpSystemID' => 'integer',
        'closedByEmpName' => 'string',
        'comments' => 'string',
        'createdUserGroup' => 'string',
        'createdUserID' => 'string',
        'createdPcID' => 'string',
        'modifiedUser' => 'string',
        'modifiedPc' => 'string'
    ];

    /**
     * Scope a query to only include users of a given type.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param mixed $type
     * @return \Illuminate\Database\Eloquent\Builder
     */

    public function scopeOfCompany($query, $type)
    {
        return $query->where('companySystemID',  $type);
    }

    /**
     * Scope a query to only include users of a given type.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param mixed $type
     * @return \Illuminate\Database\Eloquent\Builder
     */

    public function scopeOfDepartment($query, $type)
    {
        return $query->where('departmentSystemID',  $type);
    }

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function finance_year_by()
    {
        return $this->belongsTo('App\Models\CompanyFinanceYear', 'companyFinanceYearID', 'companyFinanceYearID');
    }

    public static function activeFinancePeriod($company, $departmentSystemID, $date){
        return CompanyFinancePeriod::selectRaw("companySystemID, DATE(dateFrom) AS startDate, DATE(dateTo) AS endDate,companyFinancePeriodID")
            ->where('companySystemID', $company)
            ->where('departmentSystemID', $departmentSystemID)
            ->where('isActive', -1)
            ->whereRaw("('{$date}' BETWEEN DATE(dateFrom) AND DATE(dateTo))")
            ->first();
    }
}
