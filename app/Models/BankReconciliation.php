<?php
/**
 * =============================================
 * -- File Name : BankReconciliation.php
 * -- Project Name : ERP
 * -- Module Name :  Bank Reconciliation
 * -- Author : Mohamed Fayas
 * -- Create date : 18- September 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 */
namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="BankReconciliation",
 *      required={""},
 *      @SWG\Property(
 *          property="bankRecAutoID",
 *          description="bankRecAutoID",
 *          type="integer",
 *          format="int32"
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
 *          property="bankGLAutoID",
 *          description="bankGLAutoID",
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
 *          property="bankRecPrimaryCode",
 *          description="bankRecPrimaryCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="year",
 *          description="year",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="openingBalance",
 *          description="openingBalance",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="closingBalance",
 *          description="closingBalance",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="description",
 *          description="description",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="confirmedYN",
 *          description="confirmedYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="confirmedByEmpSystemID",
 *          description="confirmedByEmpSystemID",
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
 *          property="approvedYN",
 *          description="approvedYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="approvedByUserID",
 *          description="approvedByUserID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="approvedByUserSystemID",
 *          description="approvedByUserSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="RollLevForApp_curr",
 *          description="RollLevForApp_curr",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="createdPcID",
 *          description="createdPcID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="createdUserSystemID",
 *          description="createdUserSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="createdUserID",
 *          description="createdUserID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="modifiedPc",
 *          description="modifiedPc",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="modifiedUserSystemID",
 *          description="modifiedUserSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="modifiedUser",
 *          description="modifiedUser",
 *          type="string"
 *      )
 * )
 */
class BankReconciliation extends Model
{

    public $table = 'erp_bankrecmaster';
    
    const CREATED_AT = 'createdDateTime';
    const UPDATED_AT = 'timeStamp';
    protected $primaryKey  = 'bankRecAutoID';


    public $fillable = [
        'documentSystemID',
        'documentID',
        'companySystemID',
        'companyID',
        'bankGLAutoID',
        'bankAccountAutoID',
        'month',
        'serialNo',
        'bankRecPrimaryCode',
        'year',
        'bankRecAsOf',
        'openingBalance',
        'closingBalance',
        'description',
        'confirmedYN',
        'confirmedByEmpSystemID',
        'confirmedByEmpID',
        'confirmedByName',
        'confirmedDate',
        'approvedYN',
        'approvedDate',
        'approvedByUserID',
        'approvedByUserSystemID',
        'RollLevForApp_curr',
        'createdPcID',
        'createdUserSystemID',
        'createdUserID',
        'modifiedPc',
        'modifiedUserSystemID',
        'modifiedUser',
        'createdDateTime',
        'timeStamp',
        'bankMasterID',
        'timesReferred',
        'refferedBackYN'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'bankRecAutoID' => 'integer',
        'documentSystemID' => 'integer',
        'documentID' => 'string',
        'companySystemID' => 'integer',
        'companyID' => 'string',
        'bankGLAutoID' => 'integer',
        'bankAccountAutoID' => 'integer',
        'month' => 'integer',
        'serialNo' => 'integer',
        'bankRecPrimaryCode' => 'string',
        'year' => 'integer',
        'openingBalance' => 'float',
        'closingBalance' => 'float',
        'description' => 'string',
        'confirmedYN' => 'integer',
        'confirmedByEmpSystemID' => 'integer',
        'confirmedByEmpID' => 'string',
        'confirmedByName' => 'string',
        'approvedYN' => 'integer',
        'bankMasterID' => 'integer',
        'approvedByUserID' => 'string',
        'approvedByUserSystemID' => 'integer',
        'RollLevForApp_curr' => 'integer',
        'createdPcID' => 'string',
        'createdUserSystemID' => 'integer',
        'createdUserID' => 'string',
        'modifiedPc' => 'string',
        'modifiedUserSystemID' => 'integer',
        'modifiedUser' => 'string',
        'timesReferred' => 'integer',
        'refferedBackYN' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function month(){
        return $this->belongsTo('App\Models\Months','month','monthID');
    }
    public function month_by(){
        return $this->belongsTo('App\Models\Months','month','monthID');
    }
    public function created_by()
    {
        return $this->belongsTo('App\Models\Employee', 'createdUserSystemID', 'employeeSystemID');
    }
    public function bank_account()
    {
        return $this->belongsTo('App\Models\BankAccount', 'bankAccountAutoID', 'bankAccountAutoID');
    }

    public function confirmed_by()
    {
        return $this->belongsTo('App\Models\Employee', 'confirmedByEmpSystemID', 'employeeSystemID');
    }

    public function company(){
        return $this->belongsTo('App\Models\Company','companySystemID','companySystemID');
    }

    public function approved_by(){
        return $this->hasMany('App\Models\DocumentApproved','documentSystemCode','bankRecAutoID');
    }

     public function ledger_data()
    {
        return $this->hasMany('App\Models\BankLedger', 'bankRecAutoID', 'bankRecAutoID');
    }

    public function audit_trial()
    {
        return $this->hasMany('App\Models\AuditTrail', 'documentSystemCode', 'bankRecAutoID')->where('documentSystemID',62);
    }
}
