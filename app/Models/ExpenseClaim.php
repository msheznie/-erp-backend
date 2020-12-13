<?php
/**
 * =============================================
 * -- File Name : ExpenseClaim.php
 * -- Project Name : ERP
 * -- Module Name :  Expense Claim
 * -- Author : Fayas
 * -- Create date : 10 - September 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 */
namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="ExpenseClaim",
 *      required={""},
 *      @SWG\Property(
 *          property="expenseClaimMasterAutoID",
 *          description="expenseClaimMasterAutoID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="companyID",
 *          description="companyID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="departmentID",
 *          description="departmentID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="documentID",
 *          description="documentID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="serialNo",
 *          description="serialNo",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="expenseClaimCode",
 *          description="expenseClaimCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="clamiedByName",
 *          description="clamiedByName",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="comments",
 *          description="comments",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="confirmedYN",
 *          description="confirmedYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="confirmedByEmpID",
 *          description="confirmedByEmpID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="confirmedByName",
 *          description="confirmedByName",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="approved",
 *          description="approved",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="glCodeAssignedYN",
 *          description="glCodeAssignedYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="addedForPayment",
 *          description="addedForPayment",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="rejectedYN",
 *          description="rejectedYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="rejectedComment",
 *          description="rejectedComment",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="seniorManager",
 *          description="seniorManager",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="pettyCashYN",
 *          description="pettyCashYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="addedToSalary",
 *          description="addedToSalary",
 *          type="integer",
 *          format="int32"
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
class ExpenseClaim extends Model
{

    public $table = 'erp_expenseclaimmaster';

    const CREATED_AT = 'createdDateTime';
    const UPDATED_AT = 'timestamp';
    protected $primaryKey = 'expenseClaimMasterAutoID';


    public $fillable = [
        'companyID',
        'departmentID',
        'documentID',
        'serialNo',
        'expenseClaimCode',
        'expenseClaimDate',
        'clamiedByName',
        'comments',
        'confirmedYN',
        'confirmedByEmpID',
        'confirmedByName',
        'confirmedDate',
        'approved',
        'approvedDate',
        'glCodeAssignedYN',
        'addedForPayment',
        'rejectedYN',
        'rejectedComment',
        'seniorManager',
        'pettyCashYN',
        'addedToSalary',
        'createdUserID',
        'createdPcID',
        'modifiedUser',
        'modifiedPc',
        'createdDateTime',
        'timestamp',
        'companySystemID',
        'departmentSystemID',
        'documentSystemID',
        'clamiedByNameSystemID',
        'confirmedByEmpSystemID',
        'approvedByUserSystemID',
        'createdUserSystemID',
        'modifiedUserSystemID'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'expenseClaimMasterAutoID' => 'integer',
        'companyID' => 'string',
        'departmentID' => 'string',
        'documentID' => 'string',
        'serialNo' => 'integer',
        'expenseClaimCode' => 'string',
        'clamiedByName' => 'string',
        'comments' => 'string',
        'confirmedYN' => 'integer',
        'confirmedByEmpID' => 'string',
        'confirmedByName' => 'string',
        'approved' => 'integer',
        'glCodeAssignedYN' => 'integer',
        'addedForPayment' => 'integer',
        'rejectedYN' => 'integer',
        'rejectedComment' => 'string',
        'seniorManager' => 'string',
        'pettyCashYN' => 'integer',
        'addedToSalary' => 'integer',
        'createdUserID' => 'string',
        'createdPcID' => 'string',
        'modifiedUser' => 'string',
        'modifiedPc' => 'string',
        'companySystemID' => 'integer',
        'departmentSystemID' => 'integer',
        'documentSystemID' => 'integer',
        'clamiedByNameSystemID' => 'integer',
        'confirmedByEmpSystemID' => 'integer',
        'approvedByUserSystemID' => 'integer',
        'createdUserSystemID' => 'integer',
        'modifiedUserSystemID' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [

    ];

    public function created_by()
    {
        return $this->belongsTo('App\Models\Employee', 'createdUserSystemID', 'employeeSystemID');
    }

    public function confirmed_by()
    {
        return $this->belongsTo('App\Models\Employee', 'confirmedByEmpSystemID', 'employeeSystemID');
    }

    public function modified_by()
    {
        return $this->belongsTo('App\Models\Employee', 'modifiedUserSystemID', 'employeeSystemID');
    }

    public function details()
    {
        return $this->hasMany('App\Models\ExpenseClaimDetails','expenseClaimMasterAutoID','expenseClaimMasterAutoID');
    }
    public function expense_claim_type()
    {
        return $this->hasOne('App\Models\ExpenseClaimType','expenseClaimTypeID','pettyCashYN');
    }

    public function approved_by(){
        return $this->hasMany('App\Models\DocumentApproved','documentSystemCode','expenseClaimMasterAutoID');
    }
    public function company(){
        return $this->belongsTo('App\Models\Company','companySystemID','companySystemID');
    }

    public function scopeEmployeeJoin($q,$as = 'employees' ,$column = 'createdUserSystemID',$columnAs = 'empName'){
        $q->leftJoin('employees as '. $as, $as.'.employeeSystemID', '=', 'erp_expenseclaimmaster.'.$column)
            ->addSelect($as.".empName as ".$columnAs);
    }

    public function scopeDetailJoin($q)
    {
        return $q->join('erp_expenseclaimdetails','erp_expenseclaimdetails.expenseClaimMasterAutoID','erp_expenseclaimmaster.expenseClaimMasterAutoID');
    }

    public function scopeCurrencyJoin($q,$as = 'cu', $column = 'currencyID' , $columnAs = 'currencyCode', $decimalPlaceAs = 'amount')
    {
        return $q->leftJoin('currencymaster as '.$as,$as.'.currencyID','erp_expenseclaimdetails.'.$column)
                ->addSelect($as.".DecimalPlaces as ".$decimalPlaceAs."DecimalPlaces",$as.".currencyCode as ".$columnAs);
    }

    public function scopeDepartmentJoin($q,$as = 'department', $column = 'serviceLineSystemID' , $columnAs = 'ServiceLineDes')
    {
        return $q->leftJoin('serviceline as '.$as,$as.'.serviceLineSystemID','erp_expenseclaimdetails.'.$column);
    }

    public function scopeCategoryJoin($q,$as = 'category', $column = 'expenseClaimCategoriesAutoID' , $columnAs = 'claimcategoriesDescription')
    {
        return $q->leftJoin('erp_expenseclaimcategories as '.$as,$as.'.expenseClaimCategoriesAutoID','erp_expenseclaimdetails.'.$column);
    }

    public function scopeChartOfAccountJoin($q,$as = 'chartOfAccount', $column = 'chartOfAccountSystemID' , $columnAs = 'AccountCode')
    {
        return $q->leftJoin('chartofaccounts as '.$as,$as.'.chartOfAccountSystemID','erp_expenseclaimdetails.'.$column);
    }

    public function audit_trial()
    {
        return $this->hasMany('App\Models\AuditTrail', 'documentSystemCode', 'expenseClaimMasterAutoID')->where('documentSystemID',6);
    }
}
