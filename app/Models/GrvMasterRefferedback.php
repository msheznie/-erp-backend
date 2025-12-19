<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="GrvMasterRefferedback",
 *      required={""},
 *      @SWG\Property(
 *          property="grvRefferedBackID",
 *          description="grvRefferedBackID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="grvAutoID",
 *          description="grvAutoID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="grvTypeID",
 *          description="grvTypeID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="grvType",
 *          description="grvType",
 *          type="string"
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
 *          property="companyAddress",
 *          description="companyAddress",
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
 *          property="grvSerialNo",
 *          description="grvSerialNo",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="grvPrimaryCode",
 *          description="grvPrimaryCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="grvDoRefNo",
 *          description="grvDoRefNo",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="grvNarration",
 *          description="grvNarration",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="grvLocation",
 *          description="grvLocation",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="grvDOpersonName",
 *          description="grvDOpersonName",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="grvDOpersonResID",
 *          description="grvDOpersonResID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="grvDOpersonTelNo",
 *          description="grvDOpersonTelNo",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="grvDOpersonVehicleNo",
 *          description="grvDOpersonVehicleNo",
 *          type="string"
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
 *          property="supplierAddress",
 *          description="supplierAddress",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="supplierTelephone",
 *          description="supplierTelephone",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="supplierFax",
 *          description="supplierFax",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="supplierEmail",
 *          description="supplierEmail",
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
 *          property="supplierDefaultCurrencyID",
 *          description="supplierDefaultCurrencyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="supplierDefaultER",
 *          description="supplierDefaultER",
 *          type="number",
 *          format="float"
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
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="grvConfirmedYN",
 *          description="grvConfirmedYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="grvConfirmedByEmpSystemID",
 *          description="grvConfirmedByEmpSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="grvConfirmedByEmpID",
 *          description="grvConfirmedByEmpID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="grvConfirmedByName",
 *          description="grvConfirmedByName",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="grvCancelledYN",
 *          description="grvCancelledYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="grvCancelledBySystemID",
 *          description="grvCancelledBySystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="grvCancelledBy",
 *          description="grvCancelledBy",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="grvCancelledByName",
 *          description="grvCancelledByName",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="grvTotalComRptCurrency",
 *          description="grvTotalComRptCurrency",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="grvTotalLocalCurrency",
 *          description="grvTotalLocalCurrency",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="grvTotalSupplierDefaultCurrency",
 *          description="grvTotalSupplierDefaultCurrency",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="grvTotalSupplierTransactionCurrency",
 *          description="grvTotalSupplierTransactionCurrency",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="grvDiscountPercentage",
 *          description="grvDiscountPercentage",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="grvDiscountAmount",
 *          description="grvDiscountAmount",
 *          type="number",
 *          format="float"
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
 *          property="invoiceBeforeGRVYN",
 *          description="invoiceBeforeGRVYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="deliveryConfirmedYN",
 *          description="deliveryConfirmedYN",
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
 *          property="FromCompanySystemID",
 *          description="FromCompanySystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="FromCompanyID",
 *          description="FromCompanyID",
 *          type="string"
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
 *      )
 * )
 */
class GrvMasterRefferedback extends Model
{

    public $table = 'erp_grvmasterrefferedback';

    const CREATED_AT = 'createdDateTime';
    const UPDATED_AT = 'timeStamp';

    protected $primaryKey  = 'grvRefferedBackID';


    public $fillable = [
        'grvAutoID',
        'grvTypeID',
        'grvType',
        'companySystemID',
        'companyID',
        'serviceLineSystemID',
        'serviceLineCode',
        'companyAddress',
        'companyFinanceYearID',
        'companyFinancePeriodID',
        'FYBiggin',
        'FYEnd',
        'documentSystemID',
        'documentID',
        'grvDate',
        'stampDate',
        'grvSerialNo',
        'grvPrimaryCode',
        'grvDoRefNo',
        'grvNarration',
        'grvLocation',
        'grvDOpersonName',
        'grvDOpersonResID',
        'grvDOpersonTelNo',
        'grvDOpersonVehicleNo',
        'supplierID',
        'supplierPrimaryCode',
        'supplierName',
        'supplierAddress',
        'supplierTelephone',
        'supplierFax',
        'supplierEmail',
        'liabilityAccountSysemID',
        'liabilityAccount',
        'UnbilledGRVAccountSystemID',
        'UnbilledGRVAccount',
        'localCurrencyID',
        'localCurrencyER',
        'companyReportingCurrencyID',
        'companyReportingER',
        'supplierDefaultCurrencyID',
        'supplierDefaultER',
        'supplierTransactionCurrencyID',
        'supplierTransactionER',
        'grvConfirmedYN',
        'grvConfirmedByEmpSystemID',
        'grvConfirmedByEmpID',
        'grvConfirmedByName',
        'grvConfirmedDate',
        'grvCancelledYN',
        'grvCancelledBySystemID',
        'grvCancelledBy',
        'grvCancelledByName',
        'grvCancelledDate',
        'grvTotalComRptCurrency',
        'grvTotalLocalCurrency',
        'grvTotalSupplierDefaultCurrency',
        'grvTotalSupplierTransactionCurrency',
        'grvDiscountPercentage',
        'grvDiscountAmount',
        'approved',
        'approvedDate',
        'approvedByUserID',
        'approvedByUserSystemID',
        'refferedBackYN',
        'timesReferred',
        'RollLevForApp_curr',
        'invoiceBeforeGRVYN',
        'deliveryConfirmedYN',
        'interCompanyTransferYN',
        'FromCompanySystemID',
        'FromCompanyID',
        'isMarkupUpdated',
        'createdUserGroup',
        'createdPcID',
        'createdUserSystemID',
        'createdUserID',
        'modifiedPc',
        'modifiedUserSystemID',
        'modifiedUser',
        'createdDateTime',
        'TIMESTAMP',
        'deliveryAppoinmentID',
        'isDelegation'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'grvRefferedBackID' => 'integer',
        'grvAutoID' => 'integer',
        'grvTypeID' => 'integer',
        'grvType' => 'string',
        'companySystemID' => 'integer',
        'companyID' => 'string',
        'serviceLineSystemID' => 'integer',
        'serviceLineCode' => 'string',
        'companyAddress' => 'string',
        'companyFinanceYearID' => 'integer',
        'companyFinancePeriodID' => 'integer',
        'documentSystemID' => 'integer',
        'documentID' => 'string',
        'grvSerialNo' => 'integer',
        'grvPrimaryCode' => 'string',
        'grvDoRefNo' => 'string',
        'grvNarration' => 'string',
        'grvLocation' => 'integer',
        'grvDOpersonName' => 'string',
        'grvDOpersonResID' => 'string',
        'grvDOpersonTelNo' => 'string',
        'grvDOpersonVehicleNo' => 'string',
        'supplierID' => 'integer',
        'supplierPrimaryCode' => 'string',
        'supplierName' => 'string',
        'supplierAddress' => 'string',
        'supplierTelephone' => 'string',
        'supplierFax' => 'string',
        'supplierEmail' => 'string',
        'liabilityAccountSysemID' => 'integer',
        'liabilityAccount' => 'string',
        'UnbilledGRVAccountSystemID' => 'integer',
        'UnbilledGRVAccount' => 'string',
        'localCurrencyID' => 'integer',
        'localCurrencyER' => 'float',
        'companyReportingCurrencyID' => 'integer',
        'companyReportingER' => 'float',
        'supplierDefaultCurrencyID' => 'integer',
        'supplierDefaultER' => 'float',
        'supplierTransactionCurrencyID' => 'integer',
        'supplierTransactionER' => 'float',
        'grvConfirmedYN' => 'integer',
        'grvConfirmedByEmpSystemID' => 'integer',
        'grvConfirmedByEmpID' => 'string',
        'grvConfirmedByName' => 'string',
        'grvCancelledYN' => 'integer',
        'grvCancelledBySystemID' => 'integer',
        'grvCancelledBy' => 'string',
        'grvCancelledByName' => 'string',
        'grvTotalComRptCurrency' => 'float',
        'grvTotalLocalCurrency' => 'float',
        'grvTotalSupplierDefaultCurrency' => 'float',
        'grvTotalSupplierTransactionCurrency' => 'float',
        'grvDiscountPercentage' => 'float',
        'grvDiscountAmount' => 'float',
        'approved' => 'integer',
        'approvedByUserID' => 'string',
        'approvedByUserSystemID' => 'integer',
        'refferedBackYN' => 'integer',
        'timesReferred' => 'integer',
        'RollLevForApp_curr' => 'integer',
        'invoiceBeforeGRVYN' => 'integer',
        'deliveryConfirmedYN' => 'integer',
        'interCompanyTransferYN' => 'integer',
        'FromCompanySystemID' => 'integer',
        'FromCompanyID' => 'string',
        'isMarkupUpdated' => 'integer',
        'createdUserGroup' => 'string',
        'createdPcID' => 'string',
        'createdUserSystemID' => 'integer',
        'createdUserID' => 'string',
        'modifiedPc' => 'string',
        'modifiedUserSystemID' => 'integer',
        'modifiedUser' => 'string'
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
        return $this->belongsTo('App\Models\Employee', 'grvConfirmedByEmpSystemID', 'employeeSystemID');
    }

    public function cancelled_by()
    {
        return $this->belongsTo('App\Models\Employee', 'grvCancelledBySystemID', 'employeeSystemID');
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
        return $this->belongsTo('App\Models\WarehouseMaster', 'grvLocation', 'wareHouseSystemCode');
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
        return $this->hasMany('App\Models\DocumentApproved', 'documentSystemCode', 'grvAutoID');
    }

    public function details()
    {
        return $this->hasMany('App\Models\GRVDetails', 'grvAutoID', 'grvAutoID');
    }

    public function company_by()
    {
        return $this->belongsTo('App\Models\Company', 'companySystemID', 'companySystemID');
    }

    public function companydocumentattachment_by()
    {
        return $this->hasMany('App\Models\CompanyDocumentAttachment', 'companySystemID', 'companySystemID');
    }

    public function financeperiod_by()
    {
        return $this->belongsTo('App\Models\CompanyFinancePeriod', 'companyFinancePeriodID', 'companyFinancePeriodID');
    }

    public function financeyear_by()
    {
        return $this->belongsTo('App\Models\CompanyFinanceYear', 'companyFinanceYearID', 'companyFinanceYearID');
    }

    public function grvtype_by()
    {
        return $this->belongsTo('App\Models\GRVTypes', 'grvTypeID', 'grvTypeID');
    }



}
