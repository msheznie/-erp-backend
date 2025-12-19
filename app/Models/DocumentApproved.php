<?php
/**
 * =============================================
 * -- File Name : DocumentApproved.php
 * -- Project Name : ERP
 * -- Module Name :  Approval
 * -- Author : Mubashir
 * -- Create date : 14 - March 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 * --
 */
namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Awobaz\Compoships\Compoships;
/**
 * Class DocumentApproved
 * @package App\Models
 * @version March 29, 2018, 6:31 am UTC
 *
 * @property integer companySystemID
 * @property string companyID
 * @property integer departmentSystemID
 * @property string departmentID
 * @property integer serviceLineSystemID
 * @property string serviceLineCode
 * @property integer documentSystemID
 * @property string documentID
 * @property integer documentSystemCode
 * @property string documentCode
 * @property string|\Carbon\Carbon documentDate
 * @property integer approvalLevelID
 * @property integer rollID
 * @property integer approvalGroupID
 * @property integer rollLevelOrder
 * @property string employeeID
 * @property integer employeeSystemID
 * @property string|\Carbon\Carbon docConfirmedDate
 * @property string docConfirmedByEmpID
 * @property string|\Carbon\Carbon preRollApprovedDate
 * @property integer approvedYN
 * @property string|\Carbon\Carbon approvedDate
 * @property string approvedComments
 * @property integer rejectedYN
 * @property string|\Carbon\Carbon rejectedDate
 * @property string rejectedComments
 * @property integer myApproveFlag
 * @property integer isDeligationApproval
 * @property string approvedForEmpID
 * @property integer isApprovedFromPC
 * @property string approvedPCID
 * @property string|\Carbon\Carbon timeStamp
 */
class DocumentApproved extends Model
{
    //use SoftDeletes;
    use Compoships;
    public $table = 'erp_documentapproved';
    
    const CREATED_AT = 'timeStamp';
    const UPDATED_AT = 'timeStamp';
    protected $primaryKey = 'documentApprovedID';


    protected $dates = ['deleted_at'];


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
        'employeeID',
        'employeeSystemID',
        'docConfirmedDate',
        'docConfirmedByEmpID',
        'preRollApprovedDate',
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
        'reference_email',
        'timeStamp',
        'status'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'documentApprovedID' => 'integer',
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
        'employeeID' => 'string',
        'employeeSystemID' => 'integer',
        'docConfirmedByEmpID' => 'string',
        'approvedYN' => 'integer',
        'approvedComments' => 'string',
        'rejectedYN' => 'integer',
        'rejectedComments' => 'string',
        'myApproveFlag' => 'integer',
        'isDeligationApproval' => 'integer',
        'approvedForEmpID' => 'string',
        'isApprovedFromPC' => 'integer',
        'approvedPCID' => 'string',
        'reference_email' => 'string',
        'status' => 'integer'
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


     public static function levelWiseDocumentApprover($documentSystemID, $documentSystemCode, $rollLevelOrder, $companySystemID = null)
    {
        $res = new DocumentApproved();
        $result =  $res->where("documentSystemID", $documentSystemID)
                            ->where("documentSystemCode", $documentSystemCode)
                            ->where("rollLevelOrder", $rollLevelOrder);

        if (!is_null($companySystemID)) {
            $result = $result->where('companySystemID', $companySystemID);
        }

        return $result->first();
    }

     public static function deleteApproval($documentSystemCode, $companySystemID, $documentSystemID)
    {
        $res = new DocumentApproved();
        $result =  $res->where('documentSystemCode', $documentSystemCode)
                       ->where('documentSystemID', $documentSystemID);

        if (!is_null($companySystemID)) {
            $result = $result->where('companySystemID', $companySystemID);
        }

        return $result->delete();
    }
    public function employeeDepartments()
    {
        return $this->hasOne('App\Models\EmployeesDepartment',['employeeGroupID', 'documentSystemID','companySystemID'], ['approvalGroupID', 'documentSystemID','companySystemID']);
    } 
    public function employeeRole(){ 
        return $this->hasOne('App\Models\Appointment',['id', 'RollLevForApp_curr'],['documentSystemCode', 'rollLevelOrder']);
    }

    public function suppliername(){
        return $this->belongsTo('App\Models\SupplierRegistrationLink','documentSystemCode','id');
    }

    public static function getDocumentApprovedData($docuumentApprovedId)
    {
        return self::where('documentApprovedID', $docuumentApprovedId)
            ->first();
    }

    public static function getAllDocumentApprovedData($id,$documentSystemId,$companySystemID)
    {
        return self::where('documentSystemCode', $id)
            ->where('companySystemID', $companySystemID)
            ->where('documentSystemID', $documentSystemId)
            ->get();
    }
}
