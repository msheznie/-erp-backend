<?php
/**
 * =============================================
 * -- File Name : DocumentReferedHistory.php
 * -- Project Name : ERP
 * -- Module Name :  Approval
 * -- Author : Nazir
 * -- Create date : 23 - July 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 * --
 */
namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="DocumentReferedHistory",
 *      required={""},
 *      @SWG\Property(
 *          property="documentReferedID",
 *          description="documentReferedID",
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
 *          property="documentSystemID",
 *          description="documentSystemID",
 *          type="integer",
 *          format="int32"
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
 *          property="approvalGroupID",
 *          description="approvalGroupID",
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
 *          property="employeeSystemID",
 *          description="employeeSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="employeeID",
 *          description="employeeID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="docConfirmedByEmpSystemID",
 *          description="docConfirmedByEmpSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="docConfirmedByEmpID",
 *          description="docConfirmedByEmpID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="approvedYN",
 *          description="approvedYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="approvedComments",
 *          description="approvedComments",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="rejectedYN",
 *          description="rejectedYN",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="rejectedComments",
 *          description="rejectedComments",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="approvedPCID",
 *          description="approvedPCID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="refTimes",
 *          description="refTimes",
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class DocumentReferedHistory extends Model
{

    public $table = 'erp_documentreferedhistory';

    const CREATED_AT = 'timeStamp';
    const UPDATED_AT = 'timeStamp';

    protected $primaryKey = 'documentApprovedID';

    public $fillable = [
        'companySystemID',
        'companyID',
        'departmentSystemID',
        'departmentID',
        'serviceLineSystemID',
        'serviceLineCode',
        'documentSystemID',
        'documentID',
        'documentSystemCode',
        'documentCode',
        'documentDate',
        'approvalLevelID',
        'rollID',
        'approvalGroupID',
        'rollLevelOrder',
        'employeeSystemID',
        'employeeID',
        'docConfirmedDate',
        'docConfirmedByEmpSystemID',
        'docConfirmedByEmpID',
        'preRollApprovedDate',
        'approvedYN',
        'approvedDate',
        'approvedComments',
        'rejectedYN',
        'rejectedDate',
        'rejectedComments',
        'approvedPCID',
        'timeStamp',
        'refTimes'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'documentReferedID' => 'integer',
        'companySystemID' => 'integer',
        'companyID' => 'string',
        'departmentSystemID' => 'integer',
        'departmentID' => 'string',
        'serviceLineSystemID' => 'integer',
        'serviceLineCode' => 'string',
        'documentSystemID' => 'integer',
        'documentID' => 'string',
        'documentSystemCode' => 'integer',
        'documentCode' => 'string',
        'approvalLevelID' => 'integer',
        'rollID' => 'integer',
        'approvalGroupID' => 'integer',
        'rollLevelOrder' => 'integer',
        'employeeSystemID' => 'integer',
        'employeeID' => 'string',
        'docConfirmedByEmpSystemID' => 'integer',
        'docConfirmedByEmpID' => 'string',
        'approvedYN' => 'integer',
        'approvedComments' => 'string',
        'rejectedYN' => 'string',
        'rejectedComments' => 'string',
        'approvedPCID' => 'string',
        'refTimes' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function employeesdepartment(){
        return $this->belongsTo('App\Models\EmployeesDepartment','approvalGroupID','employeeGroupID');
    }

    public function supplier(){
        return $this->HasOne('App\Models\SupplierMaster','supplierCodeSystem','documentSystemCode');
    }

    public function item(){
        return $this->HasOne('App\Models\ItemMaster','itemCodeSystem','documentSystemCode');
    }

    public function customer(){
        return $this->HasOne('App\Models\CustomerMaster','customerCodeSystem','documentSystemCode');
    }

    public function account(){
        return $this->HasOne('App\Models\ChartOfAccount','chartOfAccountSystemID','documentSystemCode');
    }

    public function employee(){
        return $this->HasOne('App\Models\Employee','employeeSystemID','employeeSystemID');
    }

    public function approved_by(){
        return $this->belongsTo('App\Models\Employee','employeeSystemID','employeeSystemID');
    }


    public function employee_department_approve(){
        return $this->belongsTo('App\Models\EmployeesDepartment','approvalGroupID','employeeGroupID')
            ->where('documentSystemID',$this->documentSystemID)
            ->where('companySystemID',$this->companySystemID);
    }
    
}
