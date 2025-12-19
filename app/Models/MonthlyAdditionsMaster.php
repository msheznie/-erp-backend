<?php
/**
 * =============================================
 * -- File Name : MonthlyAdditionsMaster.php
 * -- Project Name : ERP
 * -- Module Name : Monthly Additions Master
 * -- Author : Mohamed Fayas
 * -- Create date : 07 - November 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 */
namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="MonthlyAdditionsMaster",
 *      required={""},
 *      @SWG\Property(
 *          property="monthlyAdditionsMasterID",
 *          description="monthlyAdditionsMasterID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="monthlyAdditionsCode",
 *          description="monthlyAdditionsCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="serialNo",
 *          description="serialNo",
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
 *          property="CompanyID",
 *          description="CompanyID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="description",
 *          description="description",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="currency",
 *          description="currency",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="processPeriod",
 *          description="processPeriod",
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
 *          property="confirmedByEmpSystemID",
 *          description="confirmedByEmpSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="confirmedby",
 *          description="confirmedby",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="approvedYN",
 *          description="approvedYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="approvedByUserSystemID",
 *          description="approvedByUserSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="approvedby",
 *          description="approvedby",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="RollLevForApp_curr",
 *          description="RollLevForApp_curr",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="localCurrencyID",
 *          description="localCurrencyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="localCurrencyExchangeRate",
 *          description="localCurrencyExchangeRate",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="rptCurrencyID",
 *          description="rptCurrencyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="rptCurrencyExchangeRate",
 *          description="rptCurrencyExchangeRate",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="expenseClaimAdditionYN",
 *          description="expenseClaimAdditionYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="modifiedUserSystemID",
 *          description="modifiedUserSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="modifieduser",
 *          description="modifieduser",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="modifiedpc",
 *          description="modifiedpc",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="createdUserSystemID",
 *          description="createdUserSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="createduserGroup",
 *          description="createduserGroup",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="createdpc",
 *          description="createdpc",
 *          type="string"
 *      )
 * )
 */
class MonthlyAdditionsMaster extends Model
{

    public $table = 'hrms_monthlyadditionsmaster';
    
    const CREATED_AT = 'timestamp';
    const UPDATED_AT = 'timestamp';
    protected $primaryKey  = 'monthlyAdditionsMasterID';



    public $fillable = [
        'monthlyAdditionsCode',
        'serialNo',
        'documentSystemID',
        'documentID',
        'companySystemID',
        'CompanyID',
        'description',
        'currency',
        'processPeriod',
        'dateMA',
        'confirmedYN',
        'confirmedByEmpSystemID',
        'confirmedby',
        'confirmedDate',
        'approvedYN',
        'approvedByUserSystemID',
        'approvedby',
        'approvedDate',
        'RollLevForApp_curr',
        'localCurrencyID',
        'localCurrencyExchangeRate',
        'rptCurrencyID',
        'rptCurrencyExchangeRate',
        'expenseClaimAdditionYN',
        'modifiedUserSystemID',
        'modifieduser',
        'modifiedpc',
        'createdUserSystemID',
        'createduserGroup',
        'createdpc',
        'empType',
        'timestamp',
        'isDelegation'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'monthlyAdditionsMasterID' => 'integer',
        'monthlyAdditionsCode' => 'string',
        'serialNo' => 'integer',
        'documentSystemID' => 'integer',
        'documentID' => 'string',
        'companySystemID' => 'integer',
        'CompanyID' => 'string',
        'description' => 'string',
        'currency' => 'integer',
        'processPeriod' => 'integer',
        'confirmedYN' => 'integer',
        'confirmedByEmpSystemID' => 'integer',
        'confirmedby' => 'string',
        'approvedYN' => 'integer',
        'approvedByUserSystemID' => 'integer',
        'approvedby' => 'string',
        'RollLevForApp_curr' => 'integer',
        'localCurrencyID' => 'integer',
        'localCurrencyExchangeRate' => 'float',
        'rptCurrencyID' => 'integer',
        'rptCurrencyExchangeRate' => 'float',
        'expenseClaimAdditionYN' => 'integer',
        'modifiedUserSystemID' => 'integer',
        'modifieduser' => 'string',
        'modifiedpc' => 'string',
        'createdUserSystemID' => 'integer',
        'createduserGroup' => 'string',
        'empType' => 'integer',
        'createdpc' => 'string',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function currency_by()
    {
        return $this->hasOne('App\Models\CurrencyMaster', 'currencyID', 'currency');
    }

    public function employment_type()
    {
        return $this->hasOne('App\Models\EmploymentType', 'id', 'empType');
    }

    public function confirmed_by()
    {
        return $this->belongsTo('App\Models\Employee', 'confirmedByEmpSystemID', 'employeeSystemID');
    }

    public function details()
    {
        return $this->hasMany('App\Models\MonthlyAdditionDetail','monthlyAdditionsMasterID','monthlyAdditionsMasterID');
    }

    public function created_by()
    {
        return $this->belongsTo('App\Models\Employee', 'createdUserSystemID', 'employeeSystemID');
    }

    public function modified_by()
    {
        return $this->belongsTo('App\Models\Employee', 'modifiedUserSystemID', 'employeeSystemID');
    }
    public function approved_by(){
        return $this->hasMany('App\Models\DocumentApproved','documentSystemCode','monthlyAdditionsMasterID');
    }
    public function company(){
        return $this->belongsTo('App\Models\Company','companySystemID','companySystemID');
    }

    public function audit_trial()
    {
        return $this->hasMany('App\Models\AuditTrail', 'documentSystemCode', 'monthlyAdditionsMasterID')->where('documentSystemID',28);
    }
}
