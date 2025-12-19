<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="HrMonthlyDeductionDetail",
 *      required={""},
 *      @SWG\Property(
 *          property="monthlyDeductionDetailID",
 *          description="monthlyDeductionDetailID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="monthlyDeductionMasterID",
 *          description="monthlyDeductionMasterID",
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
 *          property="accessGroupID",
 *          description="accessGroupID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="description",
 *          description="description",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="declarationID",
 *          description="declarationID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="GLCode",
 *          description="GLCode",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="categoryID",
 *          description="categoryID",
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
 *          property="transactionExchangeRate",
 *          description="transactionExchangeRate",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="transactionCurrencyDecimalPlaces",
 *          description="transactionCurrencyDecimalPlaces",
 *          type="integer",
 *          format="int32"
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
 *          property="companyLocalExchangeRate",
 *          description="companyLocalExchangeRate",
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
 *          property="companyLocalCurrencyDecimalPlaces",
 *          description="companyLocalCurrencyDecimalPlaces",
 *          type="integer",
 *          format="int32"
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
 *          property="companyReportingExchangeRate",
 *          description="companyReportingExchangeRate",
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
 *          property="companyReportingCurrencyDecimalPlaces",
 *          description="companyReportingCurrencyDecimalPlaces",
 *          type="integer",
 *          format="int32"
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
class HrMonthlyDeductionDetail extends Model
{

    public $table = 'srp_erp_pay_monthlydeductiondetail';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';




    public $fillable = [
        'monthlyDeductionMasterID',
        'empID',
        'accessGroupID',
        'description',
        'declarationID',
        'GLCode',
        'categoryID',
        'transactionCurrencyID',
        'transactionCurrency',
        'transactionExchangeRate',
        'transactionCurrencyDecimalPlaces',
        'transactionAmount',
        'companyLocalCurrencyID',
        'companyLocalCurrency',
        'companyLocalExchangeRate',
        'companyLocalAmount',
        'companyLocalCurrencyDecimalPlaces',
        'companyReportingCurrencyID',
        'companyReportingCurrency',
        'companyReportingExchangeRate',
        'companyReportingAmount',
        'companyReportingCurrencyDecimalPlaces',
        'IsSSO',
        'IsTax',
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
        'monthlyDeductionDetailID' => 'integer',
        'monthlyDeductionMasterID' => 'integer',
        'empID' => 'integer',
        'accessGroupID' => 'integer',
        'description' => 'string',
        'declarationID' => 'integer',
        'GLCode' => 'integer',
        'categoryID' => 'integer',
        'transactionCurrencyID' => 'integer',
        'transactionCurrency' => 'string',
        'transactionExchangeRate' => 'float',
        'transactionCurrencyDecimalPlaces' => 'integer',
        'transactionAmount' => 'float',
        'companyLocalCurrencyID' => 'integer',
        'companyLocalCurrency' => 'string',
        'companyLocalExchangeRate' => 'float',
        'companyLocalAmount' => 'float',
        'companyLocalCurrencyDecimalPlaces' => 'integer',
        'companyReportingCurrencyID' => 'integer',
        'companyReportingCurrency' => 'string',
        'companyReportingExchangeRate' => 'float',
        'companyReportingAmount' => 'float',
        'companyReportingCurrencyDecimalPlaces' => 'integer',
        'IsSSO' => 'integer',
        'IsTax' => 'integer',
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
        'transactionCurrencyID' => 'required',
        'companyLocalCurrencyID' => 'required',
        'companyReportingCurrencyID' => 'required',
        'IsTax' => 'required',
        'timestamp' => 'required'
    ];

    
}
