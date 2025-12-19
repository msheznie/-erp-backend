<?php
/**
 * =============================================
 * -- File Name : BudgetMaster.php
 * -- Project Name : ERP
 * -- Module Name :  Budget
 * -- Author : Fayas
 * -- Create date : 16 - October 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 */
namespace App\Models;

use Eloquent as Model;
use Awobaz\Compoships\Compoships;

/**
 * @SWG\Definition(
 *      definition="BudgetMaster",
 *      required={""},
 *      @SWG\Property(
 *          property="budgetmasterID",
 *          description="budgetmasterID",
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
 *          property="serviceLineSystemID",
 *          description="serviceLineSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="serviceLineCode",
 *          description="serviceLineCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="templateMasterID",
 *          description="templateMasterID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="Year",
 *          description="Year",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="month",
 *          description="month",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="createdByUserSystemID",
 *          description="createdByUserSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="createdByUserID",
 *          description="createdByUserID",
 *          type="string"
 *      )
 * )
 */
class BudgetMaster extends Model
{
    use Compoships;
    public $table = 'erp_budgetmaster';
    
    const CREATED_AT = 'createdDateTime';
    const UPDATED_AT = 'timestamp';

    protected $primaryKey = 'budgetmasterID';

    public $fillable = [
        'companySystemID',
        'companyID',
        'documentSystemID',
        'documentID',
        'companyFinanceYearID',
        'serviceLineSystemID',
        'serviceLineCode',
        'templateMasterID',
        'cutOffPeriod',
        'Year',
        'month',
        'createdByUserSystemID',
        'createdByUserID',
        'createdDateTime',
        'generateStatus',
        'timestamp',
        'confirmedYN',
        'confirmedByEmpSystemID',
        'sentNotificationAt',
        'confirmedByEmpID',
        'confirmedDate',
        'approvedYN',
        'refferedBackYN',
        'timesReferred',
        'approvedByUserID',
        'approvedByUserSystemID',
        'approvedDate',
        'RollLevForApp_curr',
        'budgetUploadID',
          'isDelegation'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'budgetmasterID' => 'integer',
        'companySystemID' => 'integer',
        'timesReferred' => 'integer',
        'refferedBackYN' => 'integer',
        'companyID' => 'string',
        'companyFinanceYearID' => 'integer',
        'documentSystemID'  => 'integer',
        'documentID' => 'string',
        'serviceLineSystemID' => 'integer',
        'serviceLineCode' => 'string',
        'templateMasterID' => 'integer',
        'cutOffPeriod' => 'integer',
        'sentNotificationAt' => 'float',
        'Year' => 'integer',
        'month' => 'integer',
        'createdByUserSystemID' => 'integer',
        'generateStatus' => 'integer',
        'createdByUserID' => 'string',
        'confirmedYN' => 'integer',
        'confirmedByEmpSystemID' => 'integer',
        'confirmedByEmpID' => 'string',
        'confirmedDate' => 'string',
        'approvedYN' => 'integer',
        'approvedByUserID' => 'string',
        'approvedByUserSystemID' => 'integer',
        'approvedDate' => 'string',
        'RollLevForApp_curr' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function segment_by()
    {
        return $this->belongsTo('App\Models\SegmentMaster', 'serviceLineSystemID', 'serviceLineSystemID');
    }

    public function template_master()
    {
        return $this->belongsTo('App\Models\ReportTemplate', 'templateMasterID', 'companyReportTemplateID');
    }

    public function finance_year_by()
    {
        return $this->belongsTo('App\Models\CompanyFinanceYear', 'companyFinanceYearID', 'companyFinanceYearID');
    }
    public function created_by()
    {
        return $this->belongsTo('App\Models\Employee', 'createdByUserSystemID', 'employeeSystemID');
    }

    public function confirmed_by()
    {
        return $this->belongsTo('App\Models\Employee', 'confirmedByEmpSystemID', 'employeeSystemID');
    }

    public function approved_by(){
        return $this->hasMany('App\Models\DocumentApproved','documentSystemCode','budgetmasterID');
    }

    public function company()
    {
        return $this->belongsTo('App\Models\Company', 'companySystemID', 'companySystemID');
    }

    public function modified_by()
    {
        return $this->belongsTo('App\Models\Employee', 'modifiedUserSystemID', 'employeeSystemID');
    }

    public function audit_trial()
    {
        return $this->hasMany('App\Models\AuditTrail', 'documentSystemCode', 'budgetmasterID')->where('documentSystemID',65);
    }

    public function scopeEmployeeJoin($q,$as = 'employees' ,$column = 'createdUserSystemID',$columnAs = 'empName'){
        $q->leftJoin('employees as '. $as, $as.'.employeeSystemID', '=', 'erp_budgetmaster.'.$column)
            ->addSelect($as.".empName as ".$columnAs);
    }

    public function scopeCompanyJoin($q,$as = 'companymaster', $column = 'companySystemID' , $columnAs = 'CompanyName')
    {
        return $q->leftJoin('companymaster as '.$as,$as.'.companySystemID','erp_budgetmaster.'.$column)
        ->addSelect($as.".CompanyName as ".$columnAs);
    }

    public function scopeDetailJoin($q)
    {
        return $q->join('erp_budjetdetails_history','erp_budjetdetails_history.budgetmasterID','erp_budgetmaster.budgetmasterID');
    }

    public function scopeTemplateJoin($q,$as = 'erp_companyreporttemplate' ,$column = 'templateMasterID',$columnAs = 'reportName'){
        $q->leftJoin('erp_companyreporttemplate as '. $as, $as.'.companyReportTemplateID', '=', 'erp_budgetmaster.'.$column)
            ->addSelect($as.".reportName as ".$columnAs);
    }

    public function scopeSegmentJoin($q,$as = 'serviceline', $column = 'serviceLineSystemID' , $columnAs = 'ServiceLineDes')
    {
        return $q->leftJoin('serviceline as '.$as,$as.'.serviceLineSystemID','erp_budgetmaster.'.$column)
        ->addSelect($as.".ServiceLineDes as ".$columnAs);
    }

    public function budget_details()
    {
        return $this->hasMany('App\Models\Budjetdetails', 'budgetmasterID','budgetmasterID');
    }
    public static function getBudgetMasterByFYear($financialYearID, $companySystemID){
        return self::where('companyFinanceYearID', $financialYearID)->where('companySystemID', $companySystemID)->first();
    }

}
