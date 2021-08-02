<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="HrPayrollHeaderDetails",
 *      required={""},
 *      @SWG\Property(
 *          property="payrollHeaderDetID",
 *          description="payrollHeaderDetID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="payrollMasterID",
 *          description="payrollMasterID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="EmpID",
 *          description="EmpID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="accessGroupID",
 *          description="accessGroupID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="ECode",
 *          description="ECode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="Ename1",
 *          description="Ename1",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="Ename2",
 *          description="Ename2",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="Ename3",
 *          description="Ename3",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="Ename4",
 *          description="Ename4",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="EmpShortCode",
 *          description="EmpShortCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="secondaryCode",
 *          description="secondaryCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="Designation",
 *          description="Designation",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="Gender",
 *          description="Gender",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="Tel",
 *          description="Tel",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="Mobile",
 *          description="Mobile",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="DOJ",
 *          description="DOJ",
 *          type="string",
 *          format="date"
 *      ),
 *      @SWG\Property(
 *          property="payCurrencyID",
 *          description="payCurrencyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="payCurrency",
 *          description="payCurrency",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="nationality",
 *          description="nationality",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="totDayAbsent",
 *          description="totDayAbsent",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="totDayPresent",
 *          description="totDayPresent",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="totOTHours",
 *          description="totOTHours",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="civilOrPassport",
 *          description="civilOrPassport",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="salaryArrearsDays",
 *          description="salaryArrearsDays",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="transactionCurrencyID",
 *          description="transactionCurrencyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="transactionCurrency",
 *          description="transactionCurrency",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="transactionER",
 *          description="transactionER",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="transactionCurrencyDecimalPlaces",
 *          description="transactionCurrencyDecimalPlaces",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="transactionAmount",
 *          description="transactionAmount",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="companyLocalCurrencyID",
 *          description="companyLocalCurrencyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="companyLocalCurrency",
 *          description="companyLocalCurrency",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="companyLocalER",
 *          description="companyLocalER",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="companyLocalCurrencyDecimalPlaces",
 *          description="companyLocalCurrencyDecimalPlaces",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="companyLocalAmount",
 *          description="companyLocalAmount",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="companyReportingCurrencyID",
 *          description="companyReportingCurrencyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="companyReportingCurrency",
 *          description="companyReportingCurrency",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="companyReportingER",
 *          description="companyReportingER",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="companyReportingCurrencyDecimalPlaces",
 *          description="companyReportingCurrencyDecimalPlaces",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="companyReportingAmount",
 *          description="companyReportingAmount",
 *          type="number",
 *          format="number"
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
 *          property="payComment",
 *          description="payComment",
 *          type="string"
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
 *      )
 * )
 */
class HrPayrollHeaderDetails extends Model
{

    public $table = 'srp_erp_payrollheaderdetails';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';




    public $fillable = [
        'payrollMasterID',
        'EmpID',
        'accessGroupID',
        'ECode',
        'Ename1',
        'Ename2',
        'Ename3',
        'Ename4',
        'EmpShortCode',
        'secondaryCode',
        'Designation',
        'Gender',
        'Tel',
        'Mobile',
        'DOJ',
        'payCurrencyID',
        'payCurrency',
        'nationality',
        'totDayAbsent',
        'totDayPresent',
        'totOTHours',
        'civilOrPassport',
        'salaryArrearsDays',
        'transactionCurrencyID',
        'transactionCurrency',
        'transactionER',
        'transactionCurrencyDecimalPlaces',
        'transactionAmount',
        'companyLocalCurrencyID',
        'companyLocalCurrency',
        'companyLocalER',
        'companyLocalCurrencyDecimalPlaces',
        'companyLocalAmount',
        'companyReportingCurrencyID',
        'companyReportingCurrency',
        'companyReportingER',
        'companyReportingCurrencyDecimalPlaces',
        'companyReportingAmount',
        'segmentID',
        'segmentCode',
        'payComment',
        'companyID',
        'companyCode'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'payrollHeaderDetID' => 'integer',
        'payrollMasterID' => 'integer',
        'EmpID' => 'integer',
        'accessGroupID' => 'integer',
        'ECode' => 'string',
        'Ename1' => 'string',
        'Ename2' => 'string',
        'Ename3' => 'string',
        'Ename4' => 'string',
        'EmpShortCode' => 'string',
        'secondaryCode' => 'string',
        'Designation' => 'string',
        'Gender' => 'string',
        'Tel' => 'string',
        'Mobile' => 'string',
        'DOJ' => 'date',
        'payCurrencyID' => 'integer',
        'payCurrency' => 'string',
        'nationality' => 'string',
        'totDayAbsent' => 'string',
        'totDayPresent' => 'string',
        'totOTHours' => 'string',
        'civilOrPassport' => 'string',
        'salaryArrearsDays' => 'string',
        'transactionCurrencyID' => 'integer',
        'transactionCurrency' => 'string',
        'transactionER' => 'float',
        'transactionCurrencyDecimalPlaces' => 'float',
        'transactionAmount' => 'float',
        'companyLocalCurrencyID' => 'integer',
        'companyLocalCurrency' => 'string',
        'companyLocalER' => 'float',
        'companyLocalCurrencyDecimalPlaces' => 'float',
        'companyLocalAmount' => 'float',
        'companyReportingCurrencyID' => 'integer',
        'companyReportingCurrency' => 'string',
        'companyReportingER' => 'float',
        'companyReportingCurrencyDecimalPlaces' => 'float',
        'companyReportingAmount' => 'float',
        'segmentID' => 'integer',
        'segmentCode' => 'string',
        'payComment' => 'string',
        'companyID' => 'integer',
        'companyCode' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'payCurrencyID' => 'required'
    ];

    function master(){
        return $this->belongsTo(HrPayrollMaster::class, 'payrollMasterID', 'payrollMasterID');
    }
    
}
