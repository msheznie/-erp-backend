<?php
/**
 * =============================================
 * -- File Name : MonthlyAdditionDetail.php
 * -- Project Name : ERP
 * -- Module Name : Monthly Addition Detail
 * -- Author : Mohamed Fayas
 * -- Create date : 07 - November 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 */
namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="MonthlyAdditionDetail",
 *      required={""},
 *      @SWG\Property(
 *          property="monthlyAdditionDetailID",
 *          description="monthlyAdditionDetailID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="monthlyAdditionsMasterID",
 *          description="monthlyAdditionsMasterID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="expenseClaimMasterAutoID",
 *          description="expenseClaimMasterAutoID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="empSystemID",
 *          description="empSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="empID",
 *          description="empID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="empdepartment",
 *          description="empdepartment",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="description",
 *          description="description",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="declareCurrency",
 *          description="declareCurrency",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="declareAmount",
 *          description="declareAmount",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="amountMA",
 *          description="amountMA",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="currencyMAID",
 *          description="currencyMAID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="approvedYN",
 *          description="approvedYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="glCode",
 *          description="glCode",
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
 *          property="localCurrencyER",
 *          description="localCurrencyER",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="localAmount",
 *          description="localAmount",
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
 *          property="rptCurrencyER",
 *          description="rptCurrencyER",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="rptAmount",
 *          description="rptAmount",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="IsSSO",
 *          description="IsSSO",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="IsTax",
 *          description="IsTax",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="createdpc",
 *          description="createdpc",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="createdUserGroup",
 *          description="createdUserGroup",
 *          type="string"
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
 *      )
 * )
 */
class MonthlyAdditionDetail extends Model
{

    public $table = 'hrms_monthlyadditiondetail';
    
    const CREATED_AT = 'timestamp';
    const UPDATED_AT = 'timestamp';
    protected $primaryKey  = 'monthlyAdditionDetailID';



    public $fillable = [
        'monthlyAdditionsMasterID',
        'expenseClaimMasterAutoID',
        'empSystemID',
        'empID',
        'empdepartment',
        'description',
        'declareCurrency',
        'declareAmount',
        'amountMA',
        'currencyMAID',
        'approvedYN',
        'glCode',
        'localCurrencyID',
        'localCurrencyER',
        'localAmount',
        'rptCurrencyID',
        'rptCurrencyER',
        'rptAmount',
        'IsSSO',
        'IsTax',
        'createdpc',
        'createdUserGroup',
        'modifiedUserSystemID',
        'modifieduser',
        'modifiedpc',
        'timestamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'monthlyAdditionDetailID' => 'integer',
        'monthlyAdditionsMasterID' => 'integer',
        'expenseClaimMasterAutoID' => 'integer',
        'empSystemID' => 'integer',
        'empID' => 'string',
        'empdepartment' => 'integer',
        'description' => 'string',
        'declareCurrency' => 'integer',
        'declareAmount' => 'float',
        'amountMA' => 'float',
        'currencyMAID' => 'integer',
        'approvedYN' => 'integer',
        'glCode' => 'integer',
        'localCurrencyID' => 'integer',
        'localCurrencyER' => 'float',
        'localAmount' => 'float',
        'rptCurrencyID' => 'integer',
        'rptCurrencyER' => 'float',
        'rptAmount' => 'float',
        'IsSSO' => 'integer',
        'IsTax' => 'integer',
        'createdpc' => 'string',
        'createdUserGroup' => 'string',
        'modifiedUserSystemID' => 'integer',
        'modifieduser' => 'string',
        'modifiedpc' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function employee()
    {
        return $this->belongsTo('App\Models\Employee', 'empSystemID', 'employeeSystemID');
    }

    public function department()
    {
        return $this->belongsTo('App\Models\HRMSDepartmentMaster','empdepartment','DepartmentID');
    }

    public function currency_ma()
    {
        return $this->belongsTo('App\Models\CurrencyMaster', 'currencyMAID','currencyID');
    }

    public function expense_claim()
    {
        return $this->belongsTo('App\Models\ExpenseClaim', 'expenseClaimMasterAutoID', 'expenseClaimMasterAutoID');
    }

    public function chart_of_account()
    {
        return $this->belongsTo('App\Models\HRMSChartOfAccounts', 'glCode','charofAccAutoID');
    }

    public function master()
    {
        return $this->belongsTo('App\Models\MonthlyAdditionsMaster', 'monthlyAdditionsMasterID', 'monthlyAdditionsMasterID');
    }
}
