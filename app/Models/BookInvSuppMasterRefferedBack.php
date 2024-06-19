<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="BookInvSuppMasterRefferedBack",
 *      required={""},
 *      @SWG\Property(
 *          property="bookInvoiceRefferedBackID",
 *          description="bookInvoiceRefferedBackID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="bookingSuppMasInvAutoID",
 *          description="bookingSuppMasInvAutoID",
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
 *          property="bookingInvCode",
 *          description="bookingInvCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="comments",
 *          description="comments",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="secondaryRefNo",
 *          description="secondaryRefNo",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="supplierID",
 *          description="supplierID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="supplierGLCodeSystemID",
 *          description="supplierGLCodeSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="supplierGLCode",
 *          description="supplierGLCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="UnbilledGRVAccountSystemID",
 *          description="UnbilledGRVAccountSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="UnbilledGRVAccount",
 *          description="UnbilledGRVAccount",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="supplierInvoiceNo",
 *          description="supplierInvoiceNo",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="supplierTransactionCurrencyID",
 *          description="supplierTransactionCurrencyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="supplierTransactionCurrencyER",
 *          description="supplierTransactionCurrencyER",
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
 *          property="bookingAmountTrans",
 *          description="bookingAmountTrans",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="bookingAmountLocal",
 *          description="bookingAmountLocal",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="bookingAmountRpt",
 *          description="bookingAmountRpt",
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
 *          property="documentType",
 *          description="documentType",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="refferedBackYN",
 *          description="refferedBackYN",
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
 *          property="interCompanyTransferYN",
 *          description="interCompanyTransferYN",
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
 *      ),
 *      @SWG\Property(
 *          property="cancelYN",
 *          description="cancelYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="cancelComment",
 *          description="cancelComment",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="canceledByEmpSystemID",
 *          description="canceledByEmpSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="canceledByEmpID",
 *          description="canceledByEmpID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="canceledByEmpName",
 *          description="canceledByEmpName",
 *          type="string"
 *      )
 * )
 */
class BookInvSuppMasterRefferedBack extends Model
{

    public $table = 'erp_bookinvsuppmasterrefferedback';
    
    const CREATED_AT = 'createdDateAndTime';
    const UPDATED_AT = 'timestamp';

    protected $primaryKey = 'bookInvoiceRefferedBackID';

    public $fillable = [
        'bookingSuppMasInvAutoID',
        'companySystemID',
        'companyID',
        'documentSystemID',
        'documentID',
        'serialNo',
        'companyFinanceYearID',
        'FYBiggin',
        'FYEnd',
        'companyFinancePeriodID',
        'FYPeriodDateFrom',
        'FYPeriodDateTo',
        'bookingInvCode',
        'bookingDate',
        'comments',
        'secondaryRefNo',
        'supplierID',
        'supplierGLCodeSystemID',
        'supplierGLCode',
        'UnbilledGRVAccountSystemID',
        'UnbilledGRVAccount',
        'supplierInvoiceNo',
        'supplierInvoiceDate',
        'custInvoiceDirectAutoID',
        'supplierTransactionCurrencyID',
        'supplierTransactionCurrencyER',
        'companyReportingCurrencyID',
        'companyReportingER',
        'localCurrencyID',
        'localCurrencyER',
        'bookingAmountTrans',
        'bookingAmountLocal',
        'bookingAmountRpt',
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
        'documentType',
        'refferedBackYN',
        'timesReferred',
        'RollLevForApp_curr',
        'interCompanyTransferYN',
        'createdUserGroup',
        'createdUserSystemID',
        'createdUserID',
        'createdPcID',
        'modifiedUserSystemID',
        'modifiedUser',
        'modifiedPc',
        'createdDateTime',
        'createdDateAndTime',
        'cancelYN',
        'cancelComment',
        'cancelDate',
        'canceledByEmpSystemID',
        'canceledByEmpID',
        'canceledByEmpName',
        'timestamp',
        'VATAmount',
        'VATAmountLocal',
        'VATAmountRpt',
        'netAmount',
        'netAmountLocal',
        'netAmountRpt',
        'VATPercentage',
        'deliveryAppoinmentID',
        'whtApplicableYN',
        'whtType',
        'whtApplicable',
        'whtAmount',
        'whtPercentage',
        'isWHTApplicableVat'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'bookInvoiceRefferedBackID' => 'integer',
        'bookingSuppMasInvAutoID' => 'integer',
        'companySystemID' => 'integer',
        'companyID' => 'string',
        'documentSystemID' => 'integer',
        'documentID' => 'string',
        'serialNo' => 'integer',
        'companyFinanceYearID' => 'integer',
        'companyFinancePeriodID' => 'integer',
        'bookingInvCode' => 'string',
        'comments' => 'string',
        'secondaryRefNo' => 'string',
        'supplierID' => 'integer',
        'supplierGLCodeSystemID' => 'integer',
        'supplierGLCode' => 'string',
        'UnbilledGRVAccountSystemID' => 'integer',
        'UnbilledGRVAccount' => 'string',
        'supplierInvoiceNo' => 'string',
        'supplierTransactionCurrencyID' => 'integer',
        'supplierTransactionCurrencyER' => 'float',
        'companyReportingCurrencyID' => 'integer',
        'companyReportingER' => 'float',
        'localCurrencyID' => 'integer',
        'localCurrencyER' => 'float',
        'bookingAmountTrans' => 'float',
        'bookingAmountLocal' => 'float',
        'bookingAmountRpt' => 'float',
        'confirmedYN' => 'integer',
        'confirmedByEmpSystemID' => 'integer',
        'confirmedByEmpID' => 'string',
        'confirmedByName' => 'string',
        'approved' => 'integer',
        'approvedByUserID' => 'string',
        'approvedByUserSystemID' => 'integer',
        'documentType' => 'integer',
        'refferedBackYN' => 'integer',
        'timesReferred' => 'integer',
        'RollLevForApp_curr' => 'integer',
        'interCompanyTransferYN' => 'integer',
        'createdUserGroup' => 'string',
        'createdUserSystemID' => 'integer',
        'createdUserID' => 'string',
        'createdPcID' => 'string',
        'modifiedUserSystemID' => 'integer',
        'modifiedUser' => 'string',
        'modifiedPc' => 'string',
        'createdDateTime' => 'string',
        'cancelYN' => 'integer',
        'cancelComment' => 'string',
        'canceledByEmpSystemID' => 'integer',
        'canceledByEmpID' => 'string',
        'canceledByEmpName' => 'string',
        'custInvoiceDirectAutoID' => 'integer',
        'VATAmount' => 'float',
        'VATAmountLocal' => 'float',
        'VATAmountRpt' => 'float',
        'netAmount' => 'float',
        'netAmountLocal' => 'float',
        'netAmountRpt' => 'float',
        'VATPercentage' => 'float'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function created_by()
    {
        return $this->belongsTo('App\Models\Employee', 'createdUserSystemID', 'employeeSystemID');
    }

    public function confirmed_by()
    {
        return $this->belongsTo('App\Models\Employee', 'confirmedByEmpSystemID', 'employeeSystemID');
    }

    public function modified_by()
    {
        return $this->belongsTo('App\Models\Employee', 'modifiedUserSystemID', 'employeeSystemID');
    }

    public function supplier()
    {
        return $this->belongsTo('App\Models\SupplierMaster', 'supplierID', 'supplierCodeSystem');
    }

    public function approved_by()
    {
        return $this->hasMany('App\Models\DocumentApproved', 'documentSystemCode', 'bookingSuppMasInvAutoID');
    }

    public function cancelled_by()
    {
        return $this->belongsTo('App\Models\Employee', 'canceledByEmpSystemID', 'employeeSystemID');
    }

    public function company()
    {
        return $this->belongsTo('App\Models\Company', 'companySystemID', 'companySystemID');
    }

    public function transactioncurrency()
    {
        return $this->belongsTo('App\Models\CurrencyMaster', 'supplierTransactionCurrencyID', 'currencyID');
    }

    public function localcurrency()
    {
        return $this->belongsTo('App\Models\CurrencyMaster', 'localCurrencyID', 'currencyID');
    }

    public function rptcurrency()
    {
        return $this->belongsTo('App\Models\CurrencyMaster', 'companyReportingCurrencyID', 'currencyID');
    }

    public function grvdetail()
    {
        return $this->hasMany('App\Models\BookInvSuppDet', 'bookingSuppMasInvAutoID', 'bookingSuppMasInvAutoID');
    }

    public function detail()
    {
        return $this->hasMany('App\Models\BookInvSuppDet', 'bookingSuppMasInvAutoID', 'bookingSuppMasInvAutoID');
    }

    public function directdetail()
    {
        return $this->hasMany('App\Models\DirectInvoiceDetails', 'directInvoiceAutoID', 'bookingSuppMasInvAutoID');
    }

    public function suppliergrv()
    {
        return $this->belongsTo('App\Models\ChartOfAccount', 'supplierGLCodeSystemID', 'chartOfAccountSystemID');
    }

    public function financeperiod_by()
    {
        return $this->belongsTo('App\Models\CompanyFinancePeriod', 'companyFinancePeriodID', 'companyFinancePeriodID');
    }

    public function financeyear_by()
    {
        return $this->belongsTo('App\Models\CompanyFinanceYear', 'companyFinanceYearID', 'companyFinanceYearID');
    }

    public function paysuppdetail()
    {
        return $this->hasMany('App\Models\PaySupplierInvoiceDetail', 'bookingInvSystemCode', 'bookingSuppMasInvAutoID');
    }

    public function direct_customer_invoice(){
        return $this->belongsTo('App\Models\CustomerInvoice','custInvoiceDirectAutoID','custInvoiceDirectAutoID');
    }

    
}
