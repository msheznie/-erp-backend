<?php
/**
 * =============================================
 * -- File Name : GeneralLedger.php
 * -- Project Name : ERP
 * -- Module Name :  General Ledger
 * -- Author : Mohamed Nazir
 * -- Create date : 02 - July 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 */

namespace App\Models;

use App\helper\Helper;
use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="GeneralLedger",
 *      required={""},
 *      @SWG\Property(
 *          property="GeneralLedgerID",
 *          description="GeneralLedgerID",
 *          type="integer",
 *          format="int32"
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
 *          property="serviceLineSystemID",
 *          description="serviceLineSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="serviceLineCode",
 *          description="serviceLineCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="masterCompanyID",
 *          description="masterCompanyID",
 *          type="string"
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
 *          property="documentSystemCode",
 *          description="documentSystemCode",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="documentCode",
 *          description="documentCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="documentYear",
 *          description="documentYear",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="documentMonth",
 *          description="documentMonth",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="chequeNumber",
 *          description="chequeNumber",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="invoiceNumber",
 *          description="invoiceNumber",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="chartOfAccountSystemID",
 *          description="chartOfAccountSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="glCode",
 *          description="glCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="glAccountType",
 *          description="glAccountType",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="holdingShareholder",
 *          description="holdingShareholder",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="holdingPercentage",
 *          description="holdingPercentage",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="nonHoldingPercentage",
 *          description="nonHoldingPercentage",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="documentConfirmedBy",
 *          description="documentConfirmedBy",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="documentFinalApprovedBy",
 *          description="documentFinalApprovedBy",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="documentNarration",
 *          description="documentNarration",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="contractUID",
 *          description="contractUID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="clientContractID",
 *          description="clientContractID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="supplierCodeSystem",
 *          description="supplierCodeSystem",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="venderName",
 *          description="venderName",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="documentTransCurrencyID",
 *          description="documentTransCurrencyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="documentTransCurrencyER",
 *          description="documentTransCurrencyER",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="documentTransAmount",
 *          description="documentTransAmount",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="documentLocalCurrencyID",
 *          description="documentLocalCurrencyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="documentLocalCurrencyER",
 *          description="documentLocalCurrencyER",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="documentLocalAmount",
 *          description="documentLocalAmount",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="documentRptCurrencyID",
 *          description="documentRptCurrencyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="documentRptCurrencyER",
 *          description="documentRptCurrencyER",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="documentRptAmount",
 *          description="documentRptAmount",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="empID",
 *          description="empID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="employeePaymentYN",
 *          description="employeePaymentYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isRelatedPartyYN",
 *          description="isRelatedPartyYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="hideForTax",
 *          description="hideForTax",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="documentType",
 *          description="documentType",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="advancePaymentTypeID",
 *          description="advancePaymentTypeID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isPdcChequeYN",
 *          description="isPdcChequeYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isAddon",
 *          description="isAddon",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isAllocationJV",
 *          description="isAllocationJV",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="createdUserID",
 *          description="createdUserID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="createdUserPC",
 *          description="createdUserPC",
 *          type="string"
 *      )
 * )
 */
class GeneralLedger extends Model
{

    public $table = 'erp_generalledger';

    const CREATED_AT = 'createdDateTime';
    const UPDATED_AT = 'timestamp';

    protected $primaryKey  = 'GeneralLedgerID';

    public $fillable = [
        'companySystemID',
        'companyID',
        'serviceLineSystemID',
        'serviceLineCode',
        'masterCompanyID',
        'documentSystemID',
        'documentID',
        'documentSystemCode',
        'documentCode',
        'documentDate',
        'documentYear',
        'documentMonth',
        'chequeNumber',
        'invoiceNumber',
        'invoiceDate',
        'chartOfAccountSystemID',
        'glCode',
        'glAccountType',
        'holdingShareholder',
        'holdingPercentage',
        'nonHoldingPercentage',
        'documentConfirmedDate',
        'documentConfirmedBy',
        'documentConfirmedByEmpSystemID',
        'documentFinalApprovedDate',
        'documentFinalApprovedBy',
        'documentFinalApprovedByEmpSystemID',
        'documentNarration',
        'contractUID',
        'clientContractID',
        'supplierCodeSystem',
        'venderName',
        'documentTransCurrencyID',
        'documentTransCurrencyER',
        'documentTransAmount',
        'documentLocalCurrencyID',
        'documentLocalCurrencyER',
        'documentLocalAmount',
        'documentRptCurrencyID',
        'documentRptCurrencyER',
        'documentRptAmount',
        'empID',
        'employeePaymentYN',
        'isRelatedPartyYN',
        'hideForTax',
        'documentType',
        'advancePaymentTypeID',
        'isPdcChequeYN',
        'isAddon',
        'isAllocationJV',
        'contraYN',
        'contracDocCode',
        'createdDateTime',
        'createdUserID',
        'createdUserSystemID',
        'createdUserPC',
        'timestamp',
        'glAccountTypeID'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'GeneralLedgerID' => 'integer',
        'companySystemID' => 'integer',
        'companyID' => 'string',
        'serviceLineSystemID' => 'integer',
        'serviceLineCode' => 'string',
        'masterCompanyID' => 'string',
        'documentSystemID' => 'integer',
        'documentID' => 'string',
        'documentSystemCode' => 'integer',
        'documentCode' => 'string',
        'documentYear' => 'integer',
        'documentMonth' => 'integer',
        'chequeNumber' => 'integer',
        'invoiceNumber' => 'string',
        'chartOfAccountSystemID' => 'integer',
        'glCode' => 'string',
        'glAccountType' => 'string',
        'holdingShareholder' => 'string',
        'holdingPercentage' => 'float',
        'nonHoldingPercentage' => 'float',
        'documentConfirmedBy' => 'string',
        'documentFinalApprovedBy' => 'string',
        'documentFinalApprovedByEmpSystemID' => 'integer',
        'documentConfirmedByEmpSystemID' => 'integer',
        'documentNarration' => 'string',
        'contractUID' => 'integer',
        'clientContractID' => 'string',
        'supplierCodeSystem' => 'integer',
        'venderName' => 'string',
        'documentTransCurrencyID' => 'integer',
        'documentTransCurrencyER' => 'float',
        'documentTransAmount' => 'float',
        'documentLocalCurrencyID' => 'integer',
        'documentLocalCurrencyER' => 'float',
        'documentLocalAmount' => 'float',
        'documentRptCurrencyID' => 'integer',
        'documentRptCurrencyER' => 'float',
        'documentRptAmount' => 'float',
        'empID' => 'string',
        'employeePaymentYN' => 'integer',
        'isRelatedPartyYN' => 'integer',
        'hideForTax' => 'integer',
        'documentType' => 'integer',
        'advancePaymentTypeID' => 'integer',
        'isPdcChequeYN' => 'integer',
        'isAddon' => 'integer',
        'isAllocationJV' => 'integer',
        'contraYN' => 'integer',
        'contracDocCode' => 'string',
        'createdUserID' => 'string',
        'createdUserSystemID' => 'integer',
        'createdUserPC' => 'string',
        'glAccountTypeID' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function supplier(){
        return $this->belongsTo('App\Models\SupplierMaster', 'supplierCodeSystem','supplierCodeSystem');
    }

    public function customer(){
        return $this->belongsTo('App\Models\CustomerMaster', 'supplierCodeSystem','customerCodeSystem');
    }

    public function charofaccount(){
        return $this->belongsTo('App\Models\ChartOfAccount', 'chartOfAccountSystemID','chartOfAccountSystemID');
    }

    public function localcurrency(){
        return $this->belongsTo('App\Models\CurrencyMaster', 'documentLocalCurrencyID','currencyID');
    }

    public function transcurrency(){
        return $this->belongsTo('App\Models\CurrencyMaster', 'documentTransCurrencyID','currencyID');
    }

    public function rptcurrency(){
        return $this->belongsTo('App\Models\CurrencyMaster', 'documentRptCurrencyID','currencyID');
    }

    public function setDocumentDateAttribute($value)
    {
        $this->attributes['documentDate'] = Helper::dateAddTime($value);
    }
}
