<?php

namespace App\Models;

use App\helper\Helper;
use Awobaz\Compoships\Compoships;
use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="CreditNote",
 *      required={""},
 *      @SWG\Property(
 *          property="creditNoteAutoID",
 *          description="creditNoteAutoID",
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
 *          property="documentSystemiD",
 *          description="documentSystemiD",
 *          type="integer",
 *          format="int32"
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
 *          property="companyFinanceYearID",
 *          description="companyFinanceYearID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="companyFinancePeriodID",
 *          description="companyFinancePeriodID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="creditNoteCode",
 *          description="creditNoteCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="comments",
 *          description="comments",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="customerID",
 *          description="customerID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="customerGLCodeSystemID",
 *          description="customerGLCodeSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="customerGLCode",
 *          description="customerGLCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="customerCurrencyID",
 *          description="customerCurrencyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="customerCurrencyER",
 *          description="customerCurrencyER",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="companyReportingCurrencyID",
 *          description="companyReportingCurrencyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="companyReportingER",
 *          description="companyReportingER",
 *          type="number",
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
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="creditAmountTrans",
 *          description="creditAmountTrans",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="creditAmountLocal",
 *          description="creditAmountLocal",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="creditAmountRpt",
 *          description="creditAmountRpt",
 *          type="number",
 *          format="float"
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
 *          property="approved",
 *          description="approved",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="secondaryLogoCompID",
 *          description="secondaryLogoCompID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="secondaryLogo",
 *          description="secondaryLogo",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="matchInvoice",
 *          description="matchInvoice",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="matchingConfirmedYN",
 *          description="matchingConfirmedYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="matchingConfirmedByEmpSystemID",
 *          description="matchingConfirmedByEmpSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="matchingConfirmedByEmpID",
 *          description="matchingConfirmedByEmpID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="matchingConfirmedByName",
 *          description="matchingConfirmedByName",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="documentType",
 *          description="documentType",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="timesReferred",
 *          description="timesReferred",
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
 *          property="createdUserGroup",
 *          description="createdUserGroup",
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
 *          property="createdPcID",
 *          description="createdPcID",
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
 *      ),
 *      @SWG\Property(
 *          property="modifiedPc",
 *          description="modifiedPc",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="createdDateTime",
 *          description="createdDateTime",
 *          type="string"
 *      )
 * )
 */
class CreditNote extends Model
{
    use Compoships;
    public $table = 'erp_creditnote';

    const CREATED_AT = 'createdDateAndTime';
    const UPDATED_AT = 'timestamp';

    protected $primaryKey = "creditNoteAutoID";

    public $fillable = [
        'companySystemID',
        'companyID',
        'documentSystemiD',
        'documentID',
        'projectID',
        'serialNo',
        'companyFinanceYearID',
        'FYBiggin',
        'FYEnd',
        'companyFinancePeriodID',
        'FYPeriodDateFrom',
        'FYPeriodDateTo',
        'creditNoteCode',
        'creditNoteDate',
        'comments',
        'customerID',
        'customerGLCodeSystemID',
        'customerGLCode',
        'debitNoteAutoID',
        'customerCurrencyID',
        'customerCurrencyER',
        'companyReportingCurrencyID',
        'companyReportingER',
        'localCurrencyID',
        'localCurrencyER',
        'creditAmountTrans',
        'creditAmountLocal',
        'creditAmountRpt',
        'confirmedYN',
        'confirmedByEmpSystemID',
        'confirmedByEmpID',
        'confirmedByName',
        'confirmedDate',
        'approved',
        'approvedDate',
        'approvedByUserID',
        'approvedByUserSystemID',
        'postedDate',
        'secondaryLogoCompanySystemID',
        'secondaryLogoCompID',
        'secondaryLogo',
        'matchInvoice',
        'matchingConfirmedYN',
        'matchingConfirmedByEmpSystemID',
        'matchingConfirmedByEmpID',
        'matchingConfirmedByName',
        'matchingConfirmedDate',
        'documentType',
        'refferedBackYN',
        'timesReferred',
        'RollLevForApp_curr',
        'createdUserGroup',
        'createdUserSystemID',
        'createdUserID',
        'createdPcID',
        'modifiedUserSystemID',
        'modifiedUser',
        'modifiedPc',
        'createdDateTime',
        'timestamp',
        'createdDateAndTime',
        'isVATApplicable',
        'VATPercentage',
        'VATAmount',
        'VATAmountLocal',
        'VATAmountRpt',
        'netAmount',
        'netAmountLocal',
        'netAmountRpt',
        'isDelegation'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'creditNoteAutoID' => 'integer',
        'companySystemID' => 'integer',
        'companyID' => 'string',
        'documentSystemiD' => 'integer',
        'documentID' => 'string',
        'projectID' => 'integer',
        'serialNo' => 'integer',
        'companyFinanceYearID' => 'integer',
        'companyFinancePeriodID' => 'integer',
        'creditNoteCode' => 'string',
        'comments' => 'string',
        'customerID' => 'integer',
        'customerGLCodeSystemID' => 'integer',
        'customerGLCode' => 'string',
        'debitNoteAutoID' => 'integer',
        'customerCurrencyID' => 'integer',
        'customerCurrencyER' => 'float',
        'companyReportingCurrencyID' => 'integer',
        'companyReportingER' => 'float',
        'localCurrencyID' => 'integer',
        'localCurrencyER' => 'float',
        'creditAmountTrans' => 'float',
        'creditAmountLocal' => 'float',
        'creditAmountRpt' => 'float',
        'confirmedYN' => 'integer',
        'confirmedByEmpSystemID' => 'integer',
        'confirmedByEmpID' => 'string',
        'confirmedByName' => 'string',
        'approved' => 'integer',
        'secondaryLogoCompanySystemID' => 'integer',
        'approvedByUserID' => 'string',
        'approvedByUserSystemID' => 'integer',
        'secondaryLogoCompID' => 'string',
        'secondaryLogo' => 'string',
        'matchInvoice' => 'integer',
        'matchingConfirmedYN' => 'integer',
        'matchingConfirmedByEmpSystemID' => 'integer',
        'matchingConfirmedByEmpID' => 'string',
        'matchingConfirmedByName' => 'string',
        'documentType' => 'integer',
        'refferedBackYN' => 'integer',
        'timesReferred' => 'integer',
        'RollLevForApp_curr' => 'integer',
        'createdUserGroup' => 'string',
        'createdUserSystemID' => 'integer',
        'createdUserID' => 'string',
        'createdPcID' => 'string',
        'modifiedUserSystemID' => 'integer',
        'modifiedUser' => 'string',
        'modifiedPc' => 'string',
        'createdDateTime' => 'string',
        'createdDateAndTime' => 'integer',
        'isVATApplicable' => 'integer',
        'VATPercentage' => 'float',
        'VATAmount' => 'float',
        'VATAmountLocal' => 'float',
        'VATAmountRpt' => 'float',
        'netAmount' => 'float',
        'netAmountLocal' => 'float',
        'netAmountRpt' => 'float'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [

    ];

    public function details()
    {
        return $this->hasMany('App\Models\CreditNoteDetails', 'creditNoteAutoID', 'creditNoteAutoID');
    }

    public function project()
    {
        return $this->belongsTo('App\Models\ErpProjectMaster', 'projectID', 'id');
    }


    public function approved_by()
    {
        return $this->hasMany('App\Models\DocumentApproved', 'documentSystemCode', 'creditNoteAutoID');
    }

    public function company()
    {
        return $this->belongsTo('App\Models\Company', 'companySystemID', 'companySystemID');
    }

    public function currency()
    {
        return $this->belongsTo('App\Models\CurrencyMaster', 'customerCurrencyID', 'currencyID');
    }

     public function local_currency()
    {
        return $this->belongsTo('App\Models\CurrencyMaster', 'localCurrencyID', 'currencyID');
    }


    public function customer()
    {
        return $this->belongsTo('App\Models\CustomerMaster', 'customerID', 'customerCodeSystem');

    }

    public function confirmed_by()
    {
        return $this->belongsTo('App\Models\Employee', 'confirmedByEmpSystemID', 'employeeSystemID');
    }

    public function createduser()
    {
        return $this->belongsTo('App\Models\Employee', 'createdUserSystemID', 'employeeSystemID');
    }

    public function finance_period_by()
    {
        return $this->belongsTo('App\Models\CompanyFinancePeriod', 'companyFinancePeriodID', 'companyFinancePeriodID');
    }

    public function finance_year_by()
    {
        return $this->belongsTo('App\Models\CompanyFinanceYear', 'companyFinanceYearID', 'companyFinanceYearID');
    }

    public function modified_by()
    {
        return $this->belongsTo('App\Models\Employee', 'modifiedUserSystemID', 'employeeSystemID');
    }

    public function companydocumentattachment_by()
    {
        return $this->hasMany('App\Models\CompanyDocumentAttachment', 'companySystemID', 'companySystemID');
    }

    public function setCreditNoteDateAttribute($value)
    {
        $this->attributes['creditNoteDate'] = Helper::dateAddTime($value);
    }
    public function setPostedDateAttribute($value)
    {
        $this->attributes['postedDate'] = Helper::dateAddTime($value);
    }

    public function debitNote()
    {
        return $this->belongsTo('App\Models\DebitNote', 'debitNoteAutoID', 'debitNoteAutoID');
    }

    public function audit_trial()
    {
        return $this->hasMany('App\Models\AuditTrail', 'documentSystemCode', 'creditNoteAutoID')->where('documentSystemID',19);
    }

    public function scopeDetailJoin($q)
    {
        return $q->join('erp_creditnotedetails','erp_creditnotedetails.creditNoteAutoID','erp_creditnote.creditNoteAutoID');
    }

    public function scopeCurrencyJoin($q,$as = 'currencymaster' ,$column = 'currency',$columnAs = 'currencyByName'){
        return $q->leftJoin('currencymaster as '.$as,$as.'.currencyID','=','erp_creditnote.'.$column)
        ->addSelect($as.".CurrencyName as ".$columnAs);

    }

    public function scopeCompanyJoin($q,$as = 'companymaster', $column = 'companySystemID' , $columnAs = 'CompanyName')
    {
        return $q->leftJoin('companymaster as '.$as,$as.'.companySystemID','erp_creditnote.'.$column)
        ->addSelect($as.".CompanyName as ".$columnAs);
    }
    public function scopeCustomerJoin($q,$as = 'customermaster', $column = 'customerID' , $columnAs = 'CustomerName')
    {
        return $q->leftJoin('customermaster as '.$as,$as.'.customerCodeSystem','erp_creditnote.'.$column)
        ->addSelect($as.".CustomerName as ".$columnAs);
    }

    
    public function scopeEmployeeJoin($q,$as = 'employees' ,$column = 'createdUserSystemID',$columnAs = 'empName'){
        $q->leftJoin('employees as '. $as, $as.'.employeeSystemID', '=', 'erp_creditnote.'.$column)
            ->addSelect($as.".empName as ".$columnAs);
    }
}
