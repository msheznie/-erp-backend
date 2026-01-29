<?php
/**
 * =============================================
 * -- File Name : Employee.php
 * -- Project Name : ERP
 * -- Module Name : Employee
 * -- Author : Mohamed Fayas
 * -- Create date : 04- May 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 */
namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\helper\Helper;
/**
 * Class Employee
 * @package App\Models
 * @version February 13, 2018, 8:41 am UTC
 *
 * @property string empID
 * @property integer serial
 * @property string empLeadingText
 * @property string empUserName
 * @property string empTitle
 * @property string empInitial
 * @property string empName
 * @property string empName_O
 * @property string empFullName
 * @property string empSurname
 * @property string empSurname_O
 * @property string empFirstName
 * @property string empFirstName_O
 * @property string empFamilyName
 * @property string empFamilyName_O
 * @property string empFatherName
 * @property string empFatherName_O
 * @property string empManagerAttached
 * @property date empDateRegistered
 * @property string empTelOffice
 * @property string empTelMobile
 * @property string empLandLineNo
 * @property integer extNo
 * @property string empFax
 * @property string empEmail
 * @property integer empLocation
 * @property string|\Carbon\Carbon empDateTerminated
 * @property integer empLoginActive
 * @property integer empActive
 * @property integer userGroupID
 * @property string empCompanyID
 * @property integer religion
 * @property integer isLoggedIn
 * @property integer isLoggedOutFailYN
 * @property integer logingFlag
 * @property integer isSuperAdmin
 * @property integer discharegedYN
 * @property string hrusergroupID
 * @property integer isConsultant
 * @property integer isTrainee
 * @property integer is3rdParty
 * @property string 3rdPartyCompanyName
 * @property integer gender
 * @property integer designation
 * @property string nationality
 * @property integer isManager
 * @property integer isApproval
 * @property integer isDashBoard
 * @property integer isAdmin
 * @property integer isBasicUser
 * @property string ActivationCode
 * @property integer ActivationFlag
 * @property integer isHR_admin
 * @property integer isLock
 * @property integer opRptManagerAccess
 * @property integer isSupportAdmin
 * @property integer isHSEadmin
 * @property integer excludeObjectivesYN
 * @property integer machineID
 * @property string|\Carbon\Carbon timestamp
 */
class Employee extends Model
{
    //use SoftDeletes;

    public $table = 'employees';
    
    const CREATED_AT = 'timestamp';
    const UPDATED_AT = 'timestamp';

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */

    protected $dates = ['deleted_at'];
    protected $primaryKey = 'employeeSystemID';

    // protected $attributes = $this->getAttributes();


    public $fillable = [
        'empID',
        'serial',
        'empLeadingText',
        'empUserName',
        'empTitle',
        'empInitial',
        'empName',
        'empName_O',
        'empFullName',
        'empSurname',
        'empSurname_O',
        'empFirstName',
        'empFirstName_O',
        'empFamilyName',
        'empFamilyName_O',
        'empFatherName',
        'empFatherName_O',
        'empManagerAttached',
        'empDateRegistered',
        'empTelOffice',
        'empTelMobile',
        'empLandLineNo',
        'extNo',
        'empFax',
        'empEmail',
        'empLocation',
        'empDateTerminated',
        'empLoginActive',
        'empActive',
        'userGroupID',
        'empCompanyID',
        'religion',
        'isLoggedIn',
        'isLoggedOutFailYN',
        'logingFlag',
        'isSuperAdmin',
        'discharegedYN',
        'hrusergroupID',
        'isConsultant',
        'isTrainee',
        'is3rdParty',
        '3rdPartyCompanyName',
        'gender',
        'designation',
        'nationality',
        'isManager',
        'isApproval',
        'isDashBoard',
        'isAdmin',
        'isBasicUser',
        'ActivationCode',
        'ActivationFlag',
        'isHR_admin',
        'isLock',
        'opRptManagerAccess',
        'isSupportAdmin',
        'isHSEadmin',
        'excludeObjectivesYN',
        'empCompanySystemID',
        'machineID',
        'timestamp',
        'isEmailVerified'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'employeeSystemID' => 'integer',
        'empID' => 'string',
        'serial' => 'integer',
        'empLeadingText' => 'string',
        'empUserName' => 'string',
        'empTitle' => 'string',
        'empInitial' => 'string',
        'empName' => 'string',
        'empName_O' => 'string',
        'empFullName' => 'string',
        'empSurname' => 'string',
        'empSurname_O' => 'string',
        'empFirstName' => 'string',
        'empFirstName_O' => 'string',
        'empFamilyName' => 'string',
        'empFamilyName_O' => 'string',
        'empFatherName' => 'string',
        'empFatherName_O' => 'string',
        'empManagerAttached' => 'string',
        'empDateRegistered' => 'date',
        'empTelOffice' => 'string',
        'empTelMobile' => 'string',
        'empLandLineNo' => 'string',
        'extNo' => 'integer',
        'empCompanySystemID' => 'integer',
        'empFax' => 'string',
        'empEmail' => 'string',
        'empLocation' => 'integer',
        'empLoginActive' => 'integer',
        'empActive' => 'integer',
        'userGroupID' => 'integer',
        'empCompanyID' => 'string',
        'religion' => 'integer',
        'isLoggedIn' => 'integer',
        'isLoggedOutFailYN' => 'integer',
        'logingFlag' => 'integer',
        'isSuperAdmin' => 'integer',
        'discharegedYN' => 'integer',
        'hrusergroupID' => 'string',
        'isConsultant' => 'integer',
        'isTrainee' => 'integer',
        'is3rdParty' => 'integer',
        '3rdPartyCompanyName' => 'string',
        'gender' => 'integer',
        'designation' => 'integer',
        'nationality' => 'string',
        'isManager' => 'integer',
        'isApproval' => 'integer',
        'isDashBoard' => 'integer',
        'isAdmin' => 'integer',
        'isBasicUser' => 'integer',
        'ActivationCode' => 'string',
        'ActivationFlag' => 'integer',
        'isHR_admin' => 'integer',
        'isLock' => 'integer',
        'opRptManagerAccess' => 'integer',
        'isSupportAdmin' => 'integer',
        'isHSEadmin' => 'integer',
        'isEmailVerified' => 'integer',
        'excludeObjectivesYN' => 'integer',
        'machineID' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function user_data()
    {
        return $this->hasOne('App\Models\User','employee_id','employeeSystemID');
    }

    public function companies(){
        return $this->belongsToMany('App\Models\Company', 'employeesdepartments','CompanyID','employeeID');
    }

    public function profilepic(){
        return $this->belongsTo('App\Models\EmployeeProfile', 'employeeSystemID','employeeSystemID');
    }

    public function details(){
        return $this->hasOne('App\Models\EmployeeDetails', 'employeeSystemID','employeeSystemID');
    }

    public function emp_company(){
        return $this->belongsTo('App\Models\Company', 'empCompanySystemID','companySystemID');
    }

    public function manager(){
        return $this->belongsTo('App\Models\Employee', 'empManagerAttached','empID');
    }
 
    public function manager_hrms(){
        return $this->belongsTo('App\Models\HrmsEmployeeManager', 'employeeSystemID','empID');
    }
 
    public function desi_master(){
        return $this->belongsTo('App\Models\EmployeeDetails', 'designation','designationID');
    }

    public function erp_designation(){
        return $this->belongsTo('App\Models\HrmsDesignation', 'designation','DesignationID');
    }

    public function employee_designation(){
        return $this->hasOne('App\Models\Designation', 'designation','designationID');
    }

     public function desi_master_hrms(){
        return $this->belongsTo('App\Models\EmployeeDesignation', 'employeeSystemID','EmpID');
    }

    public function outlet()
    {
        return $this->hasMany('App\Models\OutletUsers','userID','employeeSystemID');
    }

    public function company()
    {
        return $this->belongsTo('App\Models\Company','empCompanySystemID','companySystemID');
    }

    public function religions()
    {
        return $this->hasOne('App\Models\Religion','religionID','religion');
    }

    public function genders()
    {
        return $this->hasOne('App\Models\Gender','genderID','gender');
    }

    public function personaldoc()
    {
        return $this->hasMany('App\Models\HRMSPersonalDocuments','empID','empID');
       // return $this->hasMany('App\Models\HRMSPersonalDocuments','employeeSystemID','employeeSystemID');  // change insert and update function and use employeeSystemID
    }

    public function employee_managers()
    {
        return $this->hasMany('App\Models\EmployeeManagers','empID','empID');
        // return $this->hasMany('App\Models\HRMSPersonalDocuments','employeeSystemID','employeeSystemID');  // change insert and update function and use employeeSystemID
    }

    public function custom_reports()
    {
        return $this->hasMany('App\Models\CustomReportEmployees','user_id','employeeSystemID');
    }

    public function hr_emp()
    {
        return $this->hasOne(SrpEmployeeDetails::class, 'EIdNo', 'employeeSystemID');
    }

    public function language()
    {
        return $this->hasOne('App\Models\EmployeeLanguage','employeeID','employeeSystemID');
    }

    public function invoice()
    {
        return $this->hasMany('App\Models\BookInvSuppMaster','employeeID','employeeSystemID');
    }

    public function tenderUserAccess()
    {
        return $this->hasOne('App\Models\SRMTenderUserAccess','user_id','employeeSystemID');
    }


    public function supplier_invoice()
    {
        return $this->hasMany('App\Models\BookInvSuppMaster','createdUserSystemID','employeeSystemID');
    }

    public function debit_note()
    {
        return $this->hasMany('App\Models\DebitNote', 'createdUserSystemID', 'employeeSystemID');
    }

    public function expense_claim()
    {
        return $this->hasMany('App\Models\ExpenseClaimMaster', 'createdUserID', 'employeeSystemID');
    }

    public function monthly_addition()
    {
        return $this->hasMany('App\Models\MonthlyAdditionsMaster', 'createdUserSystemID', 'employeeSystemID');
    }

    public function supplier_master()
    {
        return $this->hasMany('App\Models\SupplierMaster', 'createdUserSystemID', 'employeeSystemID');
    }

    public function item_master()
    {
        return $this->hasMany('App\Models\ItemMaster', 'createdUserSystemID', 'employeeSystemID');
    }

    public function customer_master()
    {
        return $this->hasMany('App\Models\CustomerMaster', 'createdUserID', 'empID');
    }

    public function segment()
    {
        return $this->hasMany('App\Models\SegmentMaster', 'createdUserSystemID', 'employeeSystemID');
    }

    public function material_request()
    {
        return $this->hasMany('App\Models\MaterielRequest', 'createdUserSystemID', 'employeeSystemID');
    }

    public function material_issue()
    {
        return $this->hasMany('App\Models\ItemIssueMaster', 'createdUserSystemID', 'employeeSystemID');
    }

    public function material_return()
    {
        return $this->hasMany('App\Models\ItemReturnMaster', 'createdUserSystemID', 'employeeSystemID');
    }

    public function stock_transfer()
    {
        return $this->hasMany('App\Models\StockTransfer', 'createdUserSystemID', 'employeeSystemID');
    }

    public function stock_receive()
    {
        return $this->hasMany('App\Models\StockReceive', 'createdUserSystemID', 'employeeSystemID');
    }

    public function stock_adjustment()
    {
        return $this->hasMany('App\Models\StockAdjustment', 'createdUserSystemID', 'employeeSystemID');
    }

    public function purchase_return()
    {
        return $this->hasMany('App\Models\PurchaseReturn', 'createdUserSystemID', 'employeeSystemID');
    }

    public function stock_count()
    {
        return $this->hasMany('App\Models\StockCount', 'createdUserSystemID', 'employeeSystemID');
    }

    public function budget_master()
    {
        return $this->hasMany('App\Models\BudgetMaster', 'createdByUserSystemID', 'employeeSystemID');
    }

    public function credit_note()
    {
        return $this->hasMany('App\Models\CreditNote', 'createdUserSystemID', 'employeeSystemID');
    }

    public function batch_submission()
    {
        return $this->hasMany('App\Models\CustomerInvoiceTracking', 'createdUserID', 'empID');
    }

    public function quotation()
    {
        return $this->hasMany('App\Models\QuotationMaster', 'createdUserSystemID', 'employeeSystemID');
    }

    public function sales_order()
    {
        return $this->hasMany('App\Models\QuotationMaster', 'createdUserSystemID', 'employeeSystemID');
    }

    public function delivery_order()
    {
        return $this->hasMany('App\Models\DeliveryOrder', 'createdUserSystemID', 'employeeSystemID');
    }

    public function sales_return()
    {
        return $this->hasMany('App\Models\SalesReturn', 'createdUserSystemID', 'employeeSystemID');
    }

    public function console_jv()
    {
        return $this->hasMany('App\Models\ConsoleJVMaster', 'createdUserSystemID', 'employeeSystemID');
    }

    public function recurring_voucher()
    {
        return $this->hasMany('App\Models\RecurringVoucherSetup', 'createdUserSystemID', 'employeeSystemID');
    }

    public function bank_reconciliation()
    {
        return $this->hasMany('App\Models\BankReconciliation', 'createdUserSystemID', 'employeeSystemID');
    }

    public function bank_transfer()
    {
        return $this->hasMany('App\Models\PaymentBankTransfer', 'createdUserSystemID', 'employeeSystemID');
    }

    public function budget_transfer()
    {
        return $this->hasMany('App\Models\BudgetTransferForm', 'createdUserSystemID', 'employeeSystemID');
    }

    public function budget_addition()
    {
        return $this->hasMany('App\Models\ErpBudgetAddition', 'createdUserSystemID', 'employeeSystemID');
    }

    public function asset_depreciation()
    {
        return $this->hasMany('App\Models\FixedAssetDepreciationMaster', 'createdUserSystemID', 'employeeSystemID');
    }

    public function asset_disposal()
    {
        return $this->hasMany('App\Models\AssetDisposalMaster', 'createdUserSystemID', 'employeeSystemID');
    }

    public function asset_capitalization()
    {
        return $this->hasMany('App\Models\AssetCapitalization', 'createdUserSystemID', 'employeeSystemID');
    }

    public function asset_verification()
    {
        return $this->hasMany('App\Models\AssetVerification', 'createdUserSystemID', 'employeeSystemID');
    }

    public function purchase_request()
    {
        return $this->hasMany('App\Models\PurchaseRequest', 'createdUserSystemID', 'employeeSystemID');
    }

    public function purchase_order()
    {
        return $this->hasMany('App\Models\ProcumentOrder', 'createdUserSystemID', 'employeeSystemID');
    }

    public function work_order()
    {
        return $this->hasMany('App\Models\ProcumentOrder', 'createdUserSystemID', 'employeeSystemID');
    }

    public function work_request()
    {
        return $this->hasMany('App\Models\PurchaseRequest', 'createdUserSystemID', 'employeeSystemID');
    }

    public function direct_request()
    {
        return $this->hasMany('App\Models\PurchaseRequest', 'createdUserSystemID', 'employeeSystemID');
    }

    public function direct_order()
    {
        return $this->hasMany('App\Models\ProcumentOrder', 'createdUserSystemID', 'employeeSystemID');
    }

    public function grv()
    {
        return $this->hasMany('App\Models\GRVMaster','createdUserSystemID','employeeSystemID');;
    }

    public function payment_voucher()
    {
        return $this->hasMany('App\Models\PaySupplierInvoiceMaster','createdUserSystemID','employeeSystemID');;
    }

    public function customer_invoice()
    {
        return $this->hasMany('App\Models\CustomerInvoiceDirect','createdUserSystemID','employeeSystemID');;
    }

    public function asset_costing()
    {
        return $this->hasMany('App\Models\FixedAssetMaster','createdUserSystemID','employeeSystemID');;
    }

    public function jv()
    {
        return $this->hasMany('App\Models\JvMaster','createdUserSystemID','employeeSystemID');;
    }

    public function receiptVoucher()
    {
        return $this->hasMany('App\Models\CustomerReceivePayment','createdUserSystemID','employeeSystemID');;
    }

    public static function getEmployeeCode($employeeSystemID)
    {
        $data = self::where('employeeSystemID', $employeeSystemID)->first();
        return $data ? $data->empID : '';
    }

    public static function getDesignation($employeeSystemID)
    {
        $empMaster = Employee::with(['emp_company', 'desi_master' => function ($query) {
            $query->with('designation');
        }, 'hr_emp' => function ($query) {
            $query->with('designation');
        }])->where('employeeSystemID', $employeeSystemID)->first();
    
        if($empMaster && $empMaster->emp_company && $empMaster->emp_company->isHrmsIntergrated){
            return $empMaster->hr_emp->designation->designation ?? '';
        } else {    
            return $empMaster->desi_master->designation->designation ?? '';
        }
    }
    
}
