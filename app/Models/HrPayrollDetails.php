<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="HrPayrollDetails",
 *      required={""},
 *      @SWG\Property(
 *          property="payrollDetailID",
 *          description="payrollDetailID",
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
 *          property="empID",
 *          description="empID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="detailTBID",
 *          description="detailTBID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="fromTB",
 *          description="fromTB",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="calculationTB",
 *          description="calculationTB",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="detailType",
 *          description="detailType",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="salCatID",
 *          description="salCatID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="percentage",
 *          description="percentage",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="GLCode",
 *          description="Employee Contribution GL Code",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="liabilityGL",
 *          description="liabilityGL",
 *          type="integer",
 *          format="int32"
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
 *          property="pasiActualAmount",
 *          description="pasiActualAmount",
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
class HrPayrollDetails extends Model
{

    public $table = 'srp_erp_payrolldetail';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';




    public $fillable = [
        'payrollMasterID',
        'empID',
        'detailTBID',
        'fromTB',
        'calculationTB',
        'detailType',
        'salCatID',
        'percentage',
        'GLCode',
        'liabilityGL',
        'transactionCurrencyID',
        'transactionCurrency',
        'transactionER',
        'transactionCurrencyDecimalPlaces',
        'transactionAmount',
        'pasiActualAmount',
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
        'payrollDetailID' => 'integer',
        'payrollMasterID' => 'integer',
        'empID' => 'integer',
        'detailTBID' => 'integer',
        'fromTB' => 'string',
        'calculationTB' => 'string',
        'detailType' => 'string',
        'salCatID' => 'integer',
        'percentage' => 'float',
        'GLCode' => 'integer',
        'liabilityGL' => 'integer',
        'transactionCurrencyID' => 'integer',
        'transactionCurrency' => 'string',
        'transactionER' => 'float',
        'transactionCurrencyDecimalPlaces' => 'float',
        'transactionAmount' => 'float',
        'pasiActualAmount' => 'float',
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
        'payrollMasterID' => 'required',
        'transactionCurrencyID' => 'required',
        'companyLocalCurrencyID' => 'required',
        'companyReportingCurrencyID' => 'required'
    ];

    
}
