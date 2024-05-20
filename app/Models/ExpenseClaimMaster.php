<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="ExpenseClaimMaster",
 *      required={""},
 *      @SWG\Property(
 *          property="expenseClaimMasterAutoID",
 *          description="expenseClaimMasterAutoID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="documentID",
 *          description="document short code",
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
 *          property="expenseClaimDate",
 *          description="expenseClaimDate",
 *          type="string",
 *          format="date"
 *      ),
 *      @SWG\Property(
 *          property="claimedByEmpID",
 *          description="claimedByEmpID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="claimedByEmpName",
 *          description="claimedByEmpName",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="comments",
 *          description="comments",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="isCRM",
 *          description="isCRM",
 *          type="integer",
 *          format="int32"
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
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="confirmedByName",
 *          description="confirmedByName",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="confirmedDate",
 *          description="confirmedDate",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="approvedYN",
 *          description="approvedYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="approvedByEmpID",
 *          description="approvedByEmpID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="approvedByEmpName",
 *          description="approvedByEmpName",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="approvedDate",
 *          description="approvedDate",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="approvalComments",
 *          description="approvalComments",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="glCodeAssignedYN",
 *          description="once the gl code assigned for all the expense claims this will become -1",
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
 *          property="addedToSalary",
 *          description="addedToSalary",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="companyID",
 *          description="companyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="companyCode",
 *          description="companyCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="segmentID",
 *          description="segmentID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="segmentCode",
 *          description="segmentCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="createdUserGroup",
 *          description="createdUserGroup",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="createdPCID",
 *          description="createdPCID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="createdUserID",
 *          description="createdUserID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="createdDateTime",
 *          description="createdDateTime",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="createdUserName",
 *          description="createdUserName",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="modifiedPCID",
 *          description="modifiedPCID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="modifiedUserID",
 *          description="modifiedUserID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="modifiedDateTime",
 *          description="modifiedDateTime",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="modifiedUserName",
 *          description="modifiedUserName",
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
class ExpenseClaimMaster extends Model
{

    public $timestamps = false;
    public $table = 'srp_erp_expenseclaimmaster';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $primaryKey = 'expenseClaimMasterAutoID';





    public $fillable = [
        'documentID',
        'serialNo',
        'expenseClaimCode',
        'expenseClaimDate',
        'claimedByEmpID',
        'claimedByEmpName',
        'comments',
        'isCRM',
        'confirmedYN',
        'confirmedByEmpID',
        'confirmedByName',
        'confirmedDate',
        'approvedYN',
        'approvedByEmpID',
        'approvedByEmpName',
        'approvedDate',
        'approvalComments',
        'glCodeAssignedYN',
        'addedForPayment',
        'addedToSalary',
        'companyID',
        'companyCode',
        'segmentID',
        'segmentCode',
        'createdUserGroup',
        'createdPCID',
        'createdUserID',
        'createdDateTime',
        'createdUserName',
        'modifiedPCID',
        'modifiedUserID',
        'modifiedDateTime',
        'modifiedUserName',
        'timestamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'expenseClaimMasterAutoID' => 'integer',
        'documentID' => 'string',
        'serialNo' => 'integer',
        'expenseClaimCode' => 'string',
        'expenseClaimDate' => 'date',
        'claimedByEmpID' => 'integer',
        'claimedByEmpName' => 'string',
        'comments' => 'string',
        'isCRM' => 'integer',
        'confirmedYN' => 'integer',
        'confirmedByEmpID' => 'integer',
        'confirmedByName' => 'string',
        'confirmedDate' => 'datetime',
        'approvedYN' => 'integer',
        'approvedByEmpID' => 'integer',
        'approvedByEmpName' => 'string',
        'approvedDate' => 'datetime',
        'approvalComments' => 'string',
        'glCodeAssignedYN' => 'integer',
        'addedForPayment' => 'integer',
        'addedToSalary' => 'integer',
        'companyID' => 'integer',
        'companyCode' => 'string',
        'segmentID' => 'integer',
        'segmentCode' => 'string',
        'createdUserGroup' => 'integer',
        'createdPCID' => 'string',
        'createdUserID' => 'string',
        'createdDateTime' => 'datetime',
        'createdUserName' => 'string',
        'modifiedPCID' => 'string',
        'modifiedUserID' => 'string',
        'modifiedDateTime' => 'datetime',
        'modifiedUserName' => 'string',
        'timestamp' => 'datetime'
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
        return $this->belongsTo('App\Models\Employee', 'createdUserID', 'employeeSystemID');
    }

    public function confirmed_by()
    {
        return $this->belongsTo('App\Models\Employee', 'confirmedByEmpID', 'employeeSystemID');
    }

    public function modified_by()
    {
        return $this->belongsTo('App\Models\Employee', 'modifiedUserID', 'employeeSystemID');
    }

    public function details()
    {
        return $this->hasMany('App\Models\ExpenseClaimDetailsMaster','expenseClaimMasterAutoID','expenseClaimMasterAutoID');
    }
    public function expense_claim_type()
    {
        return $this->hasOne('App\Models\ExpenseClaimType','expenseClaimTypeID','pettyCashYN');
    }

    public function approved_by(){
        return $this->hasMany('App\Models\DocumentApproved','documentSystemCode','expenseClaimMasterAutoID');
    }
    public function company(){
        return $this->belongsTo('App\Models\Company','companyID','companySystemID');
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
