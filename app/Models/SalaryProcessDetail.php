<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="SalaryProcessDetail",
 *      required={""},
 *      @SWG\Property(
 *          property="salaryProcessDetail",
 *          description="salaryProcessDetail",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="salaryProcessMasterID",
 *          description="salaryProcessMasterID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="CompanyID",
 *          description="CompanyID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="location",
 *          description="location",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="designationID",
 *          description="designationID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="departmentID",
 *          description="departmentID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="schedulemasterID",
 *          description="schedulemasterID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="empGrade",
 *          description="empGrade",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="empGroup",
 *          description="empGroup",
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
 *          property="startDate",
 *          description="startDate",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="endDate",
 *          description="endDate",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="empID",
 *          description="empID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="currency",
 *          description="currency",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="noOfDays",
 *          description="Fixed payment will calucate according to         number of days. For an example by default it will calucate for 30 days. ",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="bankMasterID",
 *          description="bankMasterID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="bankName",
 *          description="bankName",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="SwiftCode",
 *          description="SwiftCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="accountNo",
 *          description="accountNo",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="fixedPayments",
 *          description="fixedPayments",
 *          type="float",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="fixedPaymentAdjustments",
 *          description="fixedPaymentAdjustments",
 *          type="float",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="radioactiveBenifits",
 *          description="radioactiveBenifits",
 *          type="float",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="OverTime",
 *          description="OverTime",
 *          type="float",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="extraDayPay",
 *          description="extraDayPay",
 *          type="float",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="noPay",
 *          description="noPay",
 *          type="float",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="jobBonus",
 *          description="jobBonus",
 *          type="float",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="desertAllowance",
 *          description="desertAllowance",
 *          type="float",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="monthlyAddition",
 *          description="monthlyAddition",
 *          type="float",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="MA_IsSSO",
 *          description="MA_IsSSO",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="monthlyDedcution",
 *          description="monthlyDedcution",
 *          type="float",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="balancePayments",
 *          description="balancePayments",
 *          type="float",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="loanDeductions",
 *          description="loanDeductions",
 *          type="float",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="mobileCharges",
 *          description="mobileCharges",
 *          type="float",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="passiEmployee",
 *          description="passiEmployee",
 *          type="float",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="passiEmployer",
 *          description="passiEmployer",
 *          type="float",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="pasiEmployerUE",
 *          description="pasiEmployerUE",
 *          type="float",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="splitSalary",
 *          description="splitSalary",
 *          type="float",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="taxAmount",
 *          description="taxAmount",
 *          type="float",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="expenseClaimAmount",
 *          description="expenseClaimAmount",
 *          type="float",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="netSalary",
 *          description="netSalary",
 *          type="float",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="grossSalary",
 *          description="grossSalary",
 *          type="float",
 *          format="float"
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
 *          type="float",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="localAmount",
 *          description="localAmount",
 *          type="float",
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
 *          type="float",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="rptAmount",
 *          description="rptAmount",
 *          type="float",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="isRA",
 *          description="isRA",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isHold",
 *          description="-1 hold , 0 not hold",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isSettled",
 *          description="0 nothing, 1 partly hold, 2 fully settled",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="holdSalary",
 *          description="isSettled",
 *          type="float",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="heldSalaryPay",
 *          description="holdSalary",
 *          type="float",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="finalsettlementmasterID",
 *          description="finalsettlementmasterID",
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
 *          property="createduserGroup",
 *          description="createduserGroup",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="createdpc",
 *          description="createdpc",
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
class SalaryProcessDetail extends Model
{

    public $table = 'hrms_salaryprocessdetail';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';



    public $fillable = [
        'salaryProcessMasterID',
        'CompanyID',
        'location',
        'designationID',
        'departmentID',
        'schedulemasterID',
        'empGrade',
        'empGroup',
        'processPeriod',
        'startDate',
        'endDate',
        'empID',
        'currency',
        'noOfDays',
        'bankMasterID',
        'bankName',
        'SwiftCode',
        'accountNo',
        'fixedPayments',
        'fixedPaymentAdjustments',
        'radioactiveBenifits',
        'OverTime',
        'extraDayPay',
        'noPay',
        'jobBonus',
        'desertAllowance',
        'monthlyAddition',
        'MA_IsSSO',
        'monthlyDedcution',
        'balancePayments',
        'loanDeductions',
        'mobileCharges',
        'passiEmployee',
        'passiEmployer',
        'pasiEmployerUE',
        'splitSalary',
        'taxAmount',
        'expenseClaimAmount',
        'netSalary',
        'grossSalary',
        'localCurrencyID',
        'localCurrencyER',
        'localAmount',
        'rptCurrencyID',
        'rptCurrencyER',
        'rptAmount',
        'isRA',
        'isHold',
        'isSettled',
        'holdSalary',
        'heldSalaryPay',
        'finalsettlementmasterID',
        'modifieduser',
        'modifiedpc',
        'createduserGroup',
        'createdpc',
        'timestamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'salaryProcessDetail' => 'integer',
        'salaryProcessMasterID' => 'integer',
        'CompanyID' => 'string',
        'location' => 'integer',
        'designationID' => 'integer',
        'departmentID' => 'integer',
        'schedulemasterID' => 'integer',
        'empGrade' => 'integer',
        'empGroup' => 'integer',
        'processPeriod' => 'integer',
        'startDate' => 'datetime',
        'endDate' => 'datetime',
        'empID' => 'string',
        'currency' => 'integer',
        'noOfDays' => 'integer',
        'bankMasterID' => 'integer',
        'bankName' => 'string',
        'SwiftCode' => 'string',
        'accountNo' => 'string',
        'fixedPayments' => 'float',
        'fixedPaymentAdjustments' => 'float',
        'radioactiveBenifits' => 'float',
        'OverTime' => 'float',
        'extraDayPay' => 'float',
        'noPay' => 'float',
        'jobBonus' => 'float',
        'desertAllowance' => 'float',
        'monthlyAddition' => 'float',
        'MA_IsSSO' => 'integer',
        'monthlyDedcution' => 'float',
        'balancePayments' => 'float',
        'loanDeductions' => 'float',
        'mobileCharges' => 'float',
        'passiEmployee' => 'float',
        'passiEmployer' => 'float',
        'pasiEmployerUE' => 'float',
        'splitSalary' => 'float',
        'taxAmount' => 'float',
        'expenseClaimAmount' => 'float',
        'netSalary' => 'float',
        'grossSalary' => 'float',
        'localCurrencyID' => 'integer',
        'localCurrencyER' => 'float',
        'localAmount' => 'float',
        'rptCurrencyID' => 'integer',
        'rptCurrencyER' => 'float',
        'rptAmount' => 'float',
        'isRA' => 'integer',
        'isHold' => 'integer',
        'isSettled' => 'integer',
        'holdSalary' => 'float',
        'heldSalaryPay' => 'float',
        'finalsettlementmasterID' => 'integer',
        'modifieduser' => 'string',
        'modifiedpc' => 'string',
        'createduserGroup' => 'string',
        'createdpc' => 'string',
        'timestamp' => 'datetime'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'salaryProcessDetail' => 'required'
    ];

    
}
