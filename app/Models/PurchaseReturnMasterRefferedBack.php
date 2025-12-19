<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="PurchaseReturnMasterRefferedBack",
 *      required={""},
 *      @SWG\Property(
 *          property="purhaseReturnAutoID",
 *          description="purhaseReturnAutoID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="purhaseReturnRefferedBackID",
 *          description="purhaseReturnRefferedBackID",
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
 *          property="FYBiggin",
 *          description="FYBiggin",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="FYEnd",
 *          description="FYEnd",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="serialNo",
 *          description="serialNo",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="purchaseReturnDate",
 *          description="purchaseReturnDate",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="purchaseReturnCode",
 *          description="purchaseReturnCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="purchaseReturnRefNo",
 *          description="purchaseReturnRefNo",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="narration",
 *          description="narration",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="purchaseReturnLocation",
 *          description="purchaseReturnLocation",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="supplierID",
 *          description="supplierID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="supplierPrimaryCode",
 *          description="supplierPrimaryCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="supplierName",
 *          description="supplierName",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="liabilityAccountSysemID",
 *          description="liabilityAccountSysemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="liabilityAccount",
 *          description="liabilityAccount",
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
 *          property="supplierDefaultCurrencyID",
 *          description="supplierDefaultCurrencyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="supplierDefaultER",
 *          description="supplierDefaultER",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="supplierTransactionCurrencyID",
 *          description="supplierTransactionCurrencyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="supplierTransactionER",
 *          description="supplierTransactionER",
 *          type="number",
 *          format="number"
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
 *          format="number"
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
 *          format="number"
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
 *          property="confirmedDate",
 *          description="confirmedDate",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="totalSupplierDefaultAmount",
 *          description="totalSupplierDefaultAmount",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="totalSupplierTransactionAmount",
 *          description="totalSupplierTransactionAmount",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="totalLocalAmount",
 *          description="totalLocalAmount",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="totalComRptAmount",
 *          description="totalComRptAmount",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="approved",
 *          description="approved",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="approvedDate",
 *          description="approvedDate",
 *          type="string",
 *          format="date-time"
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
 *          property="timesReferred",
 *          description="timesReferred",
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
 *          property="createdPcID",
 *          description="createdPcID",
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
 *          property="modifiedPc",
 *          description="modifiedPc",
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
 *          property="createdDateTime",
 *          description="createdDateTime",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="timeStamp",
 *          description="timeStamp",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="isInvoiceCreatedForGrv",
 *          description="isInvoiceCreatedForGrv",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="grvRecieved",
 *          description="grvRecieved",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="prClosedYN",
 *          description="prClosedYN",
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class PurchaseReturnMasterRefferedBack extends Model
{

    public $table = 'erp_purchasereturnmaster_refferedback';
    
    const CREATED_AT = null;
    const UPDATED_AT = null;

    protected $primaryKey  = 'purhaseReturnRefferedBackID';

    public $fillable = [
        'purhaseReturnAutoID',
        'companySystemID',
        'companyID',
        'serviceLineSystemID',
        'serviceLineCode',
        'documentSystemID',
        'documentID',
        'companyFinanceYearID',
        'companyFinancePeriodID',
        'FYBiggin',
        'FYEnd',
        'serialNo',
        'purchaseReturnDate',
        'purchaseReturnCode',
        'purchaseReturnRefNo',
        'narration',
        'purchaseReturnLocation',
        'supplierID',
        'supplierPrimaryCode',
        'supplierName',
        'liabilityAccountSysemID',
        'liabilityAccount',
        'UnbilledGRVAccountSystemID',
        'UnbilledGRVAccount',
        'supplierDefaultCurrencyID',
        'supplierDefaultER',
        'supplierTransactionCurrencyID',
        'supplierTransactionER',
        'localCurrencyID',
        'localCurrencyER',
        'companyReportingCurrencyID',
        'companyReportingER',
        'confirmedYN',
        'confirmedByEmpSystemID',
        'confirmedByEmpID',
        'confirmedByName',
        'confirmedDate',
        'totalSupplierDefaultAmount',
        'totalSupplierTransactionAmount',
        'totalLocalAmount',
        'totalComRptAmount',
        'approved',
        'approvedDate',
        'approvedByUserID',
        'approvedByUserSystemID',
        'timesReferred',
        'refferedBackYN',
        'RollLevForApp_curr',
        'createdUserGroup',
        'createdPcID',
        'createdUserSystemID',
        'createdUserID',
        'modifiedPc',
        'modifiedUserSystemID',
        'modifiedUser',
        'createdDateTime',
        'timeStamp',
        'isInvoiceCreatedForGrv',
        'grvRecieved',
        'prClosedYN',
         'isDelegation'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'purhaseReturnAutoID' => 'integer',
        'purhaseReturnRefferedBackID' => 'integer',
        'companySystemID' => 'integer',
        'companyID' => 'string',
        'serviceLineSystemID' => 'integer',
        'serviceLineCode' => 'string',
        'documentSystemID' => 'integer',
        'documentID' => 'string',
        'companyFinanceYearID' => 'integer',
        'companyFinancePeriodID' => 'integer',
        'FYBiggin' => 'datetime',
        'FYEnd' => 'datetime',
        'serialNo' => 'integer',
        'purchaseReturnDate' => 'datetime',
        'purchaseReturnCode' => 'string',
        'purchaseReturnRefNo' => 'string',
        'narration' => 'string',
        'purchaseReturnLocation' => 'integer',
        'supplierID' => 'integer',
        'supplierPrimaryCode' => 'string',
        'supplierName' => 'string',
        'liabilityAccountSysemID' => 'integer',
        'liabilityAccount' => 'string',
        'UnbilledGRVAccountSystemID' => 'integer',
        'UnbilledGRVAccount' => 'string',
        'supplierDefaultCurrencyID' => 'integer',
        'supplierDefaultER' => 'float',
        'supplierTransactionCurrencyID' => 'integer',
        'supplierTransactionER' => 'float',
        'localCurrencyID' => 'integer',
        'localCurrencyER' => 'float',
        'companyReportingCurrencyID' => 'integer',
        'companyReportingER' => 'float',
        'confirmedYN' => 'integer',
        'confirmedByEmpSystemID' => 'integer',
        'confirmedByEmpID' => 'string',
        'confirmedByName' => 'string',
        'confirmedDate' => 'datetime',
        'totalSupplierDefaultAmount' => 'float',
        'totalSupplierTransactionAmount' => 'float',
        'totalLocalAmount' => 'float',
        'totalComRptAmount' => 'float',
        'approved' => 'integer',
        'approvedDate' => 'datetime',
        'approvedByUserID' => 'string',
        'approvedByUserSystemID' => 'integer',
        'timesReferred' => 'integer',
        'refferedBackYN' => 'integer',
        'RollLevForApp_curr' => 'integer',
        'createdUserGroup' => 'string',
        'createdPcID' => 'string',
        'createdUserSystemID' => 'integer',
        'createdUserID' => 'string',
        'modifiedPc' => 'string',
        'modifiedUserSystemID' => 'integer',
        'modifiedUser' => 'string',
        'createdDateTime' => 'datetime',
        'timeStamp' => 'datetime',
        'isInvoiceCreatedForGrv' => 'integer',
        'grvRecieved' => 'integer',
        'prClosedYN' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'purhaseReturnAutoID' => 'required'
    ];

    public function created_by()
    {
        return $this->belongsTo('App\Models\Employee', 'createdUserSystemID', 'employeeSystemID');
    }

    public function confirmed_by()
    {
        return $this->belongsTo('App\Models\Employee', 'confirmedByEmpSystemID', 'employeeSystemID');
    }

    public function segment_by()
    {
        return $this->belongsTo('App\Models\SegmentMaster', 'serviceLineSystemID', 'serviceLineSystemID');
    }

    public function modified_by()
    {
        return $this->belongsTo('App\Models\Employee', 'modifiedUserSystemID', 'employeeSystemID');
    }

    public function location_by()
    {
        return $this->belongsTo('App\Models\WarehouseMaster', 'purchaseReturnLocation', 'wareHouseSystemCode');
    }

    public function supplier_by()
    {
        return $this->belongsTo('App\Models\SupplierMaster', 'supplierID', 'supplierCodeSystem');
    }

    public function currency_by()
    {
        return $this->belongsTo('App\Models\CurrencyMaster', 'supplierTransactionCurrencyID', 'currencyID');
    }

    public function approved_by()
    {
        return $this->hasMany('App\Models\DocumentApproved', 'documentSystemCode', 'purhaseReturnAutoID');
    }

    public function details()
    {
        return $this->hasMany('App\Models\PurchaseReturnDetails', 'purhaseReturnAutoID', 'purhaseReturnAutoID');
    }

    public function company_by()
    {
        return $this->belongsTo('App\Models\Company', 'companySystemID', 'companySystemID');
    }

    public function companydocumentattachment_by()
    {
        return $this->hasMany('App\Models\CompanyDocumentAttachment', 'companySystemID', 'companySystemID');
    }

    public function finance_period_by()
    {
        return $this->belongsTo('App\Models\CompanyFinancePeriod', 'companyFinancePeriodID', 'companyFinancePeriodID');
    }

    public function finance_year_by()
    {
        return $this->belongsTo('App\Models\CompanyFinanceYear', 'companyFinanceYearID', 'companyFinanceYearID');
    }

    public function setPurchaseReturnDateAttribute($value)
    {
        $this->attributes['purchaseReturnDate'] = Helper::dateAddTime($value);
    }

    public function audit_trial()
    {
        return $this->hasMany('App\Models\AuditTrail', 'documentSystemCode', 'purhaseReturnAutoID')->where('documentSystemID',24);
    }
}
