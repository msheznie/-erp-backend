<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="LeaveDocumentApproved",
 *      required={""},
 *      @SWG\Property(
 *          property="documentApprovedID",
 *          description="documentApprovedID",
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
 *          property="serviceLineCode",
 *          description="serviceLineCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="documentID",
 *          description="documentID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="documentSystemCode",
 *          description="documentSystemCode",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="documentCode",
 *          description="documentCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="approvalLevelID",
 *          description="approvalLevelID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="rollID",
 *          description="rollID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="rollLevelOrder",
 *          description="rollLevelOrder",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="employeeID",
 *          description="Who actually approved",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="Approver",
 *          description="Who suppose to Approve",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="docConfirmedDate",
 *          description="docConfirmedDate",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="docConfirmedByEmpID",
 *          description="docConfirmedByEmpID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="preRollApprovedDate",
 *          description="preRollApprovedDate",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="requesterID",
 *          description="requesterID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="approvedYN",
 *          description="approvedYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="approvedDate",
 *          description="approvedDate",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="approvedComments",
 *          description="approvedComments",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="rejectedYN",
 *          description="rejectedYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="rejectedDate",
 *          description="rejectedDate",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="rejectedComments",
 *          description="rejectedComments",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="myApproveFlag",
 *          description="to seperate different operation in code when approve and unapprove",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isDeligationApproval",
 *          description="isDeligationApproval",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="approvedForEmpID",
 *          description="approvedForEmpID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="isApprovedFromPC",
 *          description="PC =1  web=0",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="approvedPCID",
 *          description="approvedPCID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="timeStamp",
 *          description="timeStamp",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="approvalGroupID",
 *          description="approvalGroupID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="hrApproval",
 *          description="hrApproval",
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class LeaveDocumentApproved extends Model
{

    public $table = 'hrms_leavedocumentapproved';
    
    const CREATED_AT = 'timeStamp';
    const UPDATED_AT = 'timeStamp';
    protected $primaryKey = 'documentApprovedID';


    public $fillable = [
        'companyID',
        'departmentID',
        'serviceLineCode',
        'documentID',
        'documentSystemCode',
        'companySystemID',
        'documentSystemID',
        'documentCode',
        'approvalLevelID',
        'rollID',
        'rollLevelOrder',
        'employeeID',
        'Approver',
        'docConfirmedDate',
        'docConfirmedByEmpID',
        'preRollApprovedDate',
        'requesterID',
        'approvedYN',
        'approvedDate',
        'approvedComments',
        'rejectedYN',
        'rejectedDate',
        'rejectedComments',
        'myApproveFlag',
        'isDeligationApproval',
        'approvedForEmpID',
        'isApprovedFromPC',
        'approvedPCID',
        'timeStamp',
        'approvalGroupID',
        'hrApproval',
        'empSystemID'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'documentApprovedID' => 'integer',
        'companyID' => 'string',
        'departmentID' => 'string',
        'serviceLineCode' => 'string',
        'documentID' => 'string',
        'documentSystemCode' => 'integer',
        'documentSystemID' => 'integer',
        'companySystemID' => 'integer',
        'empSystemID' => 'integer',
        'documentCode' => 'string',
        'approvalLevelID' => 'integer',
        'rollID' => 'integer',
        'rollLevelOrder' => 'integer',
        'employeeID' => 'string',
        'Approver' => 'string',
        'docConfirmedDate' => 'datetime',
        'docConfirmedByEmpID' => 'string',
        'preRollApprovedDate' => 'datetime',
        'requesterID' => 'string',
        'approvedYN' => 'integer',
        'approvedDate' => 'datetime',
        'approvedComments' => 'string',
        'rejectedYN' => 'integer',
        'rejectedDate' => 'datetime',
        'rejectedComments' => 'string',
        'myApproveFlag' => 'integer',
        'isDeligationApproval' => 'integer',
        'approvedForEmpID' => 'string',
        'isApprovedFromPC' => 'integer',
        'approvedPCID' => 'string',
        'timeStamp' => 'datetime',
        'approvalGroupID' => 'integer',
        'hrApproval' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        //'documentApprovedID' => 'required'
    ];

    public function employee(){
        return $this->belongsTo('App\Models\Employee','empSystemID','employeeSystemID');
    }

    public function leave()
    {
        return $this->belongsTo('App\Models\LeaveDataMaster','documentSystemCode','leavedatamasterID')
            ->where('documentID','LA');
    }

    public function expenseClaim()
    {
        return $this->belongsTo('App\Models\ExpenseClaim','documentSystemCode','expenseClaimMasterAutoID');
    }

    public function company()
    {
        return $this->belongsTo('App\Models\Company','companySystemID','companySystemID');
    }

}
