<?php
/**
 * =============================================
 * -- File Name : SalaryProcessMaster.php
 * -- Project Name : ERP
 * -- Module Name : Salary Process Master
 * -- Author : Mohamed Fayas
 * -- Create date : 07 - November 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 */
namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="SalaryProcessMaster",
 *      required={""},
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
 *          property="salaryProcessCode",
 *          description="salaryProcessCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="documentID",
 *          description="documentID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="serialNo",
 *          description="serialNo",
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
 *          property="Currency",
 *          description="Currency",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="salaryMonth",
 *          description="salaryMonth",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="description",
 *          description="description",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="RollLevForApp_curr",
 *          description="RollLevForApp_curr",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isReferredBack",
 *          description="isReferredBack",
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
 *          property="approvedby",
 *          description="approvedby",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="approvedDate",
 *          description="approvedDate",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="isRGLConfirm",
 *          description="isRGLConfirm",
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
 *          property="updateNoOfDaysBtnFlag",
 *          description="updateNoOfDaysBtnFlag",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="updateSalaryBtnFlag",
 *          description="updateSalaryBtnFlag",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="getEmployeeBtnFlag",
 *          description="getEmployeeBtnFlag",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="updateSSOBtnFlag",
 *          description="updateSSOBtnFlag",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="updateRABenefitBtnFlag",
 *          description="updateRABenefitBtnFlag",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="updateTaxStep1BtnFlag",
 *          description="updateTaxStep1BtnFlag",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="updateTaxStep2BtnFlag",
 *          description="updateTaxStep2BtnFlag",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="updateTaxStep3BtnFlag",
 *          description="updateTaxStep3BtnFlag",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="updateTaxStep4BtnFlag",
 *          description="updateTaxStep4BtnFlag",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="updateHeldSalaryBtnFlag",
 *          description="updateHeldSalaryBtnFlag",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isHeldSalary",
 *          description="isHeldSalary",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="showpaySlip",
 *          description="showpaySlip",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="paymentGenerated",
 *          description="paymentGenerated",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="PayMasterAutoId",
 *          description="PayMasterAutoId",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="bankIDForPayment",
 *          description="bankIDForPayment",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="bankAccountIDForPayment",
 *          description="bankAccountIDForPayment",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="salaryProcessType",
 *          description="salaryProcessType",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="thirteenMonthJVID",
 *          description="thirteenMonthJVID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="gratuityJVID",
 *          description="gratuityJVID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="gratuityReversalJVID",
 *          description="gratuityReversalJVID",
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
 *      )
 * )
 */
class SalaryProcessMaster extends Model
{

    public $table = 'hrms_salaryprocessmaster';
    
    const CREATED_AT = 'timestamp';
    const UPDATED_AT = 'timestamp';
    protected $primaryKey  = 'salaryProcessMasterID';


    public $fillable = [
        'CompanyID',
        'salaryProcessCode',
        'documentID',
        'serialNo',
        'processPeriod',
        'startDate',
        'endDate',
        'Currency',
        'salaryMonth',
        'description',
        'createDate',
        'RollLevForApp_curr',
        'isReferredBack',
        'confirmedYN',
        'confirmedby',
        'approvedYN',
        'approvedby',
        'approvedDate',
        'confirmedDate',
        'isRGLConfirm',
        'localCurrencyID',
        'localCurrencyExchangeRate',
        'rptCurrencyID',
        'rptCurrencyExchangeRate',
        'updateNoOfDaysBtnFlag',
        'updateSalaryBtnFlag',
        'getEmployeeBtnFlag',
        'updateSSOBtnFlag',
        'updateRABenefitBtnFlag',
        'updateTaxStep1BtnFlag',
        'updateTaxStep2BtnFlag',
        'updateTaxStep3BtnFlag',
        'updateTaxStep4BtnFlag',
        'updateHeldSalaryBtnFlag',
        'isHeldSalary',
        'showpaySlip',
        'paymentGenerated',
        'PayMasterAutoId',
        'bankIDForPayment',
        'bankAccountIDForPayment',
        'salaryProcessType',
        'thirteenMonthJVID',
        'gratuityJVID',
        'gratuityReversalJVID',
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
        'salaryProcessMasterID' => 'integer',
        'CompanyID' => 'string',
        'salaryProcessCode' => 'string',
        'documentID' => 'string',
        'serialNo' => 'integer',
        'processPeriod' => 'integer',
        'Currency' => 'integer',
        'salaryMonth' => 'integer',
        'description' => 'string',
        'RollLevForApp_curr' => 'integer',
        'isReferredBack' => 'integer',
        'confirmedYN' => 'integer',
        'confirmedby' => 'string',
        'approvedYN' => 'integer',
        'approvedby' => 'string',
        'approvedDate' => 'string',
        'isRGLConfirm' => 'integer',
        'localCurrencyID' => 'integer',
        'localCurrencyExchangeRate' => 'float',
        'rptCurrencyID' => 'integer',
        'rptCurrencyExchangeRate' => 'float',
        'updateNoOfDaysBtnFlag' => 'integer',
        'updateSalaryBtnFlag' => 'integer',
        'getEmployeeBtnFlag' => 'integer',
        'updateSSOBtnFlag' => 'integer',
        'updateRABenefitBtnFlag' => 'integer',
        'updateTaxStep1BtnFlag' => 'integer',
        'updateTaxStep2BtnFlag' => 'integer',
        'updateTaxStep3BtnFlag' => 'integer',
        'updateTaxStep4BtnFlag' => 'integer',
        'updateHeldSalaryBtnFlag' => 'integer',
        'isHeldSalary' => 'integer',
        'showpaySlip' => 'integer',
        'paymentGenerated' => 'integer',
        'PayMasterAutoId' => 'integer',
        'bankIDForPayment' => 'integer',
        'bankAccountIDForPayment' => 'integer',
        'salaryProcessType' => 'integer',
        'thirteenMonthJVID' => 'integer',
        'gratuityJVID' => 'integer',
        'gratuityReversalJVID' => 'integer',
        'modifieduser' => 'string',
        'modifiedpc' => 'string',
        'createduserGroup' => 'string',
        'createdpc' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
