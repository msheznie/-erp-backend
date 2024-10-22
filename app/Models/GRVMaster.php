<?php
/**
 * =============================================
 * -- File Name : GRVMaster.php
 * -- Project Name : ERP
 * -- Module Name :  GRV Master
 * -- Author : Mohamed Nazir
 * -- Create date : 11 - June 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 */
namespace App\Models;

use App\helper\Helper;
use Awobaz\Compoships\Compoships;
use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class GRVMaster
 * @package App\Models
 * @version April 11, 2018, 12:12 pm UTC
 *
 * @property integer grvTypeID
 * @property string grvType
 * @property integer companySystemID
 * @property string companyID
 * @property integer serviceLineSystemID
 * @property string serviceLineCode
 * @property string companyAddress
 * @property integer companyFinanceYearID
 * @property integer companyFinancePeriodID
 * @property string|\Carbon\Carbon FYBiggin
 * @property string|\Carbon\Carbon FYEnd
 * @property integer documentSystemID
 * @property string documentID
 * @property string|\Carbon\Carbon grvDate
 * @property integer grvSerialNo
 * @property string grvPrimaryCode
 * @property string grvDoRefNo
 * @property string grvNarration
 * @property integer grvLocation
 * @property string grvDOpersonName
 * @property string grvDOpersonResID
 * @property string grvDOpersonTelNo
 * @property string grvDOpersonVehicleNo
 * @property integer supplierID
 * @property string supplierPrimaryCode
 * @property string supplierName
 * @property string supplierAddress
 * @property string supplierTelephone
 * @property string supplierFax
 * @property string supplierEmail
 * @property integer liabilityAccountSysemID
 * @property string liabilityAccount
 * @property integer UnbilledGRVAccountSystemID
 * @property string UnbilledGRVAccount
 * @property integer localCurrencyID
 * @property float localCurrencyER
 * @property integer companyReportingCurrencyID
 * @property float companyReportingER
 * @property integer supplierDefaultCurrencyID
 * @property float supplierDefaultER
 * @property integer supplierTransactionCurrencyID
 * @property float supplierTransactionER
 * @property integer grvConfirmedYN
 * @property string grvConfirmedByEmpID
 * @property string grvConfirmedByName
 * @property string|\Carbon\Carbon grvConfirmedDate
 * @property integer grvCancelledYN
 * @property string grvCancelledBy
 * @property string grvCancelledByName
 * @property string|\Carbon\Carbon grvCancelledDate
 * @property float grvTotalComRptCurrency
 * @property float grvTotalLocalCurrency
 * @property float grvTotalSupplierDefaultCurrency
 * @property float grvTotalSupplierTransactionCurrency
 * @property float grvDiscountPercentage
 * @property float grvDiscountAmount
 * @property integer approved
 * @property string|\Carbon\Carbon approvedDate
 * @property integer timesReferred
 * @property integer RollLevForApp_curr
 * @property integer invoiceBeforeGRVYN
 * @property integer deliveryConfirmedYN
 * @property integer interCompanyTransferYN
 * @property string FromCompanyID
 * @property string createdUserGroup
 * @property string createdPcID
 * @property string createdUserID
 * @property string modifiedPc
 * @property string modifiedUser
 * @property string|\Carbon\Carbon createdDateTime
 * @property string|\Carbon\Carbon timeStamp
 */
class GRVMaster extends Model
{
    //use SoftDeletes;
    use Compoships;
    public $table = 'erp_grvmaster';
    
    const CREATED_AT = 'createdDateTime';
    const UPDATED_AT = 'timeStamp';

    protected $primaryKey  = 'grvAutoID';

    protected $dates = ['deleted_at'];


    public $fillable = [
        'grvTypeID',
        'grvType',
        'companySystemID',
        'pullType',
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
        'projectID',
        'grvDate',
        'grvSerialNo',
        'grvPrimaryCode',
        'grvDoRefNo',
        'grvNarration',
        'grvLocation',
        'grvDOpersonName',
        'grvDOpersonResID',
        'grvDOpersonTelNo',
        'vatRegisteredYN',
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
        'grvConfirmedByEmpID',
        'grvConfirmedByName',
        'grvConfirmedByEmpSystemID',
        'grvConfirmedDate',
        'grvCancelledYN',
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
        'refferedBackYN',
        'timesReferred',
        'RollLevForApp_curr',
        'invoiceBeforeGRVYN',
        'deliveryConfirmedYN',
        'interCompanyTransferYN',
        'FromCompanySystemID',
        'FromCompanyID',
        'capitalizedYN',
        'isMarkupUpdated',
        'createdUserGroup',
        'createdPcID',
        'createdUserSystemID',
        'createdUserID',
        'modifiedPc',
        'modifiedUser',
        'modifiedUserSystemID',
        'createdDateTime',
        'timeStamp',
        'stampDate',
        'approvedByUserID',
        'approvedByUserSystemID',
        'postedDate',
        'deliveryAppoinmentID',
        'isDelegation'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'grvAutoID' => 'integer',
        'grvTypeID' => 'integer',
        'pullType' => 'integer',
        'grvType' => 'string',
        'companySystemID' => 'integer',
        'vatRegisteredYN' => 'integer',
        'companyID' => 'string',
        'serviceLineSystemID' => 'integer',
        'serviceLineCode' => 'string',
        'companyAddress' => 'string',
        'companyFinanceYearID' => 'integer',
        'companyFinancePeriodID' => 'integer',
        'documentSystemID' => 'integer',
        'documentID' => 'string',
        'projectID' => 'integer',
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
        'grvConfirmedByEmpID' => 'string',
        'grvConfirmedByName' => 'string',
        'grvConfirmedByEmpSystemID' => 'integer',
        'grvCancelledYN' => 'integer',
        'grvCancelledBy' => 'string',
        'grvCancelledByName' => 'string',
        'grvTotalComRptCurrency' => 'float',
        'grvTotalLocalCurrency' => 'float',
        'grvTotalSupplierDefaultCurrency' => 'float',
        'grvTotalSupplierTransactionCurrency' => 'float',
        'grvDiscountPercentage' => 'float',
        'grvDiscountAmount' => 'float',
        'approved' => 'integer',
        'refferedBackYN' => 'integer',
        'timesReferred' => 'integer',
        'RollLevForApp_curr' => 'integer',
        'invoiceBeforeGRVYN' => 'integer',
        'deliveryConfirmedYN' => 'integer',
        'interCompanyTransferYN' => 'integer',
        'FromCompanySystemID' => 'integer',
        'FromCompanyID' => 'string',
        'capitalizedYN' => 'integer',
        'isMarkupUpdated' => 'integer',
        'createdUserGroup' => 'string',
        'createdPcID' => 'string',
        'createdUserSystemID' => 'integer',
        'createdUserID' => 'string',
        'modifiedPc' => 'string',
        'modifiedUser' => 'string',
        'modifiedUserSystemID' => 'integer',
        'stampDate' => 'string',
        'approvedByUserID' => 'string',
        'approvedByUserSystemID' => 'integer'
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

    public function project()
    {
        return $this->belongsTo('App\Models\ErpProjectMaster', 'projectID', 'id');
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

    public function reporting_currency_by()
    {
        return $this->belongsTo('App\Models\CurrencyMaster', 'companyReportingCurrencyID', 'currencyID');
    }

    public function local_currency_by()
    {
        return $this->belongsTo('App\Models\CurrencyMaster', 'localCurrencyID', 'currencyID');
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

    public function setGrvDateAttribute($value)
    {
        $this->attributes['grvDate'] = Helper::dateAddTime($value);
    }

    public function audit_trial()
    {
        return $this->hasMany('App\Models\AuditTrail', 'documentSystemCode', 'grvAutoID')->where('documentSystemID',3);
    }

    public function scopeDetailJoin($q)
    {
        return $q->join('erp_grvdetails','erp_grvdetails.grvAutoID','erp_grvmaster.grvAutoID');
    }

    public function scopeCurrencyJoin($q,$as = 'currencymaster' ,$column = 'currency',$columnAs = 'currencyByName'){
        return $q->leftJoin('currencymaster as '.$as,$as.'.currencyID','=','erp_grvmaster.'.$column)
        ->addSelect($as.".CurrencyName as ".$columnAs);

    }

    public function scopeCompanyJoin($q,$as = 'companymaster', $column = 'companySystemID' , $columnAs = 'CompanyName')
    {
        return $q->leftJoin('companymaster as '.$as,$as.'.companySystemID','erp_grvmaster.'.$column)
        ->addSelect($as.".CompanyName as ".$columnAs);
    }
    public function scopeCustomerJoin($q,$as = 'customermaster', $column = 'customerID' , $columnAs = 'CustomerName')
    {
        return $q->leftJoin('customermaster as '.$as,$as.'.customerCodeSystem','erp_grvmaster.'.$column)
        ->addSelect($as.".CustomerName as ".$columnAs);
    }

    
    public function scopeEmployeeJoin($q,$as = 'employees' ,$column = 'createdUserSystemID',$columnAs = 'empName'){
        $q->leftJoin('employees as '. $as, $as.'.employeeSystemID', '=', 'erp_grvmaster.'.$column)
            ->addSelect($as.".empName as ".$columnAs);
    }

    
    public function scopeWareHouseJoin($q,$as = 'warehousemaster', $column = 'wareHouseSystemCode' , $columnAs = 'wareHouseDescription')
    {
        return $q->leftJoin('warehousemaster as '.$as,$as.'.wareHouseSystemCode','erp_grvmaster.'.$column)
        ->addSelect($as.".wareHouseDescription as ".$columnAs);
    }
    
    public function scopeSupplierJoin($q,$as = 'supplier', $column = 'supplierID' , $columnAs = 'primarySupplierCode')
    {
        return $q->leftJoin('suppliermaster as '.$as,$as.'.supplierCodeSystem','erp_grvmaster.'.$column)
        ->addSelect($as.".supplierName as ".$columnAs);
    }

    public function evaluationMaster()
    {
        return $this->belongsTo('App\Models\SupplierEvaluation', 'grvAutoID', 'documentId');
    }
}
