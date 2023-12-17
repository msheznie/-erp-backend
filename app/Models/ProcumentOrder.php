<?php
/**
 * =============================================
 * -- File Name : ProcumentOrder.php
 * -- Project Name : ERP
 * -- Module Name :  Procument Order
 * -- Author : Nazir
 * -- Create date : 18 - April 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 * --
 */
namespace App\Models;

use App\helper\Helper;
use App\helper\TaxService;
use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Awobaz\Compoships\Compoships;

/**
 * Class ProcumentOrder
 * @package App\Models
 * @version March 28, 2018, 7:42 am UTC
 *
 * @property \Illuminate\Database\Eloquent\Collection ErpPurchaseorderdetail
 * @property integer poProcessId
 * @property string companyID
 * @property string departmentID
 * @property string serviceLine
 * @property string companyAddress
 * @property string documentID
 * @property string purchaseOrderCode
 * @property integer serialNumber
 * @property integer supplierID
 * @property string supplierPrimaryCode
 * @property string supplierName
 * @property string supplierAddress
 * @property string supplierTelephone
 * @property string supplierFax
 * @property string supplierEmail
 * @property integer creditPeriod
 * @property string|\Carbon\Carbon expectedDeliveryDate
 * @property string narration
 * @property integer poLocation
 * @property integer financeCategory
 * @property string referenceNumber
 * @property integer shippingAddressID
 * @property string shippingAddressDescriprion
 * @property integer invoiceToAddressID
 * @property string invoiceToAddressDescription
 * @property integer soldToAddressID
 * @property string soldToAddressDescriprion
 * @property string paymentTerms
 * @property string deliveryTerms
 * @property string panaltyTerms
 * @property integer localCurrencyID
 * @property float localCurrencyER
 * @property integer companyReportingCurrencyID
 * @property float companyReportingER
 * @property integer supplierDefaultCurrencyID
 * @property float supplierDefaultER
 * @property integer supplierTransactionCurrencyID
 * @property float supplierTransactionER
 * @property integer poConfirmedYN
 * @property string poConfirmedByEmpID
 * @property string poConfirmedByName
 * @property string|\Carbon\Carbon poConfirmedDate
 * @property integer poCancelledYN
 * @property string poCancelledBy
 * @property string poCancelledByName
 * @property string|\Carbon\Carbon poCancelledDate
 * @property string cancelledComments
 * @property float poTotalComRptCurrency
 * @property float poTotalLocalCurrency
 * @property float poTotalSupplierDefaultCurrency
 * @property float poTotalSupplierTransactionCurrency
 * @property float poDiscountPercentage
 * @property float poDiscountAmount
 * @property integer supplierVATEligible
 * @property float VATPercentage
 * @property float VATAmount
 * @property float VATAmountLocal
 * @property float VATAmountRpt
 * @property string shipTocontactPersonID
 * @property string shipTocontactPersonTelephone
 * @property string shipTocontactPersonFaxNo
 * @property string shipTocontactPersonEmail
 * @property string invoiceTocontactPersonID
 * @property string invoiceTocontactPersonTelephone
 * @property string invoiceTocontactPersonFaxNo
 * @property string invoiceTocontactPersonEmail
 * @property string soldTocontactPersonID
 * @property string soldTocontactPersonTelephone
 * @property string soldTocontactPersonFaxNo
 * @property string soldTocontactPersonEmail
 * @property integer priority
 * @property integer approved
 * @property string|\Carbon\Carbon approvedDate
 * @property float addOnPercent
 * @property float addOnDefaultPercent
 * @property integer GRVTrackingID
 * @property integer logisticDoneYN
 * @property integer poClosedYN
 * @property integer grvRecieved
 * @property integer invoicedBooked
 * @property integer timesReferred
 * @property string poType
 * @property integer poType_N
 * @property string docRefNo
 * @property integer RollLevForApp_curr
 * @property integer sentToSupplier
 * @property string sentToSupplierByEmpID
 * @property string sentToSupplierByEmpName
 * @property string|\Carbon\Carbon sentToSupplierDate
 * @property integer budgetBlockYN
 * @property integer budgetYear
 * @property integer hidePOYN
 * @property string hideByEmpID
 * @property string hideByEmpName
 * @property string|\Carbon\Carbon hideDate
 * @property string hideComments
 * @property integer WO_purchaseOrderID
 * @property string|\Carbon\Carbon WO_PeriodFrom
 * @property string|\Carbon\Carbon WO_PeriodTo
 * @property integer WO_NoOfAutoGenerationTimes
 * @property integer WO_NoOfGeneratedTimes
 * @property integer WO_fullyGenerated
 * @property integer WO_amendYN
 * @property string|\Carbon\Carbon WO_amendRequestedDate
 * @property string WO_amendRequestedByEmpID
 * @property integer WO_confirmedYN
 * @property string|\Carbon\Carbon WO_confirmedDate
 * @property string WO_confirmedByEmpID
 * @property integer WO_terminateYN
 * @property string|\Carbon\Carbon WO_terminatedDate
 * @property string WO_terminatedByEmpID
 * @property string WO_terminateComments
 * @property integer partiallyGRVAllowed
 * @property integer logisticsAvailable
 * @property integer vatRegisteredYN
 * @property string createdUserGroup
 * @property string createdPcID
 * @property string createdUserID
 * @property string modifiedPc
 * @property string modifiedUser
 * @property string|\Carbon\Carbon createdDateTime
 * @property boolean isSelected
 * @property string|\Carbon\Carbon timeStamp
 */
class ProcumentOrder extends Model
{
    use Compoships;
    //use SoftDeletes;

    public $table = 'erp_purchaseordermaster';

    const CREATED_AT = 'createdDateTime';
    const UPDATED_AT = 'timeStamp';

    protected $primaryKey = 'purchaseOrderID';

    protected $dates = ['deleted_at'];
    protected $appends = ['isWoAmendAccess','isVatEligible','rcmAvailable'];

    public $fillable = [
        'poProcessId',
        'companySystemID',
        'companyID',
        'departmentID',
        'serviceLineSystemID',
        'serviceLine',
        'companyAddress',
        'documentID',
        'documentSystemID',
        'purchaseOrderCode',
        'amended',
        'serialNumber',
        'supplierID',
        'supplierPrimaryCode',
        'supplierName',
        'supplierAddress',
        'supplierTelephone',
        'supplierFax',
        'supplierEmail',
        'creditPeriod',
        'expectedDeliveryDate',
        'narration',
        'poLocation',
        'financeCategory',
        'referenceNumber',
        'shippingAddressID',
        'shippingAddressDescriprion',
        'invoiceToAddressID',
        'invoiceToAddressDescription',
        'soldToAddressID',
        'soldToAddressDescriprion',
        'vat_number',
        'paymentTerms',
        'deliveryTerms',
        'panaltyTerms',
        'localCurrencyID',
        'localCurrencyER',
        'companyReportingCurrencyID',
        'companyReportingER',
        'supplierDefaultCurrencyID',
        'supplierDefaultER',
        'supplierTransactionCurrencyID',
        'supplierTransactionER',
        'poConfirmedYN',
        'poConfirmedByEmpID',
        'poConfirmedByEmpSystemID',
        'poConfirmedByName',
        'poConfirmedDate',
        'poCancelledYN',
        'poCancelledBy',
        'poCancelledByName',
        'poCancelledDate',
        'cancelledComments',
        'poTotalComRptCurrency',
        'poTotalLocalCurrency',
        'poTotalSupplierDefaultCurrency',
        'poTotalSupplierTransactionCurrency',
        'poDiscountPercentage',
        'poDiscountAmount',
        'supplierVATEligible',
        'VATPercentage',
        'VATAmount',
        'VATAmountLocal',
        'VATAmountRpt',
        'shipTocontactPersonID',
        'shipTocontactPersonTelephone',
        'shipTocontactPersonFaxNo',
        'shipTocontactPersonEmail',
        'invoiceTocontactPersonID',
        'invoiceTocontactPersonTelephone',
        'invoiceTocontactPersonFaxNo',
        'invoiceTocontactPersonEmail',
        'soldTocontactPersonID',
        'soldTocontactPersonTelephone',
        'soldTocontactPersonFaxNo',
        'soldTocontactPersonEmail',
        'priority',
        'approved',
        'approvedDate',
        'approvedByUserID',
        'approvedByUserSystemID',
        'addOnPercent',
        'addOnDefaultPercent',
        'GRVTrackingID',
        'logisticDoneYN',
        'poClosedYN',
        'grvRecieved',
        'invoicedBooked',
        'timesReferred',
        'refferedBackYN',
        'poType',
        'poType_N',
        'poTypeID',
        'docRefNo',
        'RollLevForApp_curr',
        'sentToSupplier',
        'sentToSupplierByEmpID',
        'sentToSupplierByEmpName',
        'sentToSupplierDate',
        'budgetBlockYN',
        'budgetYear',
        'hidePOYN',
        'hideByEmpID',
        'hideByEmpName',
        'hideDate',
        'hideComments',
        'WO_purchaseOrderID',
        'WO_PeriodFrom',
        'WO_PeriodTo',
        'WO_NoOfAutoGenerationTimes',
        'WO_NoOfGeneratedTimes',
        'WO_fullyGenerated',
        'WO_amendYN',
        'WO_amendRequestedDate',
        'WO_amendRequestedByEmpID',
        'WO_confirmedYN',
        'WO_confirmedDate',
        'WO_confirmedByEmpID',
        'WO_terminateYN',
        'WO_terminatedDate',
        'WO_terminatedByEmpID',
        'WO_terminateComments',
        'partiallyGRVAllowed',
        'logisticsAvailable',
        'vatRegisteredYN',
        'manuallyClosed',
        'manuallyClosedByEmpSystemID',
        'manuallyClosedByEmpID',
        'manuallyClosedByEmpName',
        'manuallyClosedDate',
        'manuallyClosedComment',
        'createdUserGroup',
        'createdPcID',
        'createdUserID',
        'createdUserSystemID',
        'modifiedPc',
        'modifiedUser',
        'createdDateTime',
        'isSelected',
        'timeStamp',
        'supCategoryICVMasterID',
        'workOrderGenerateID',
        'supCategorySubICVID',
        'allocateItemToSegment',
        'rcmActivated',
        'orderType',
        'projectID',
        'approval_remarks',
        'categoryID',
        'upload_job_status'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'purchaseOrderID' => 'integer',
        'allocateItemToSegment' => 'integer',
        'amended' => 'integer',
        'poProcessId' => 'integer',
        'companySystemID' => 'integer',
        'companyID' => 'string',
        'departmentID' => 'string',
        'serviceLineSystemID' => 'integer',
        'serviceLine' => 'string',
        'companyAddress' => 'string',
        'documentID' => 'string',
        'documentSystemID' => 'integer',
        'purchaseOrderCode' => 'string',
        'serialNumber' => 'integer',
        'supplierID' => 'integer',
        'supplierPrimaryCode' => 'string',
        'supplierName' => 'string',
        'supplierAddress' => 'string',
        'supplierTelephone' => 'string',
        'supplierFax' => 'string',
        'supplierEmail' => 'string',
        'creditPeriod' => 'integer',
        'narration' => 'string',
        'poLocation' => 'integer',
        'financeCategory' => 'integer',
        'referenceNumber' => 'string',
        'shippingAddressID' => 'integer',
        'shippingAddressDescriprion' => 'string',
        'invoiceToAddressID' => 'integer',
        'invoiceToAddressDescription' => 'string',
        'soldToAddressID' => 'integer',
        'soldToAddressDescriprion' => 'string',
        'vat_number' => 'string',
        'paymentTerms' => 'string',
        'deliveryTerms' => 'string',
        'panaltyTerms' => 'string',
        'localCurrencyID' => 'integer',
        'localCurrencyER' => 'float',
        'companyReportingCurrencyID' => 'integer',
        'companyReportingER' => 'float',
        'supplierDefaultCurrencyID' => 'integer',
        'supplierDefaultER' => 'float',
        'supplierTransactionCurrencyID' => 'integer',
        'supplierTransactionER' => 'float',
        'poConfirmedYN' => 'integer',
        'poConfirmedByEmpSystemID' => 'integer',
        'poConfirmedByEmpID' => 'string',
        'poConfirmedByName' => 'string',
        'poCancelledYN' => 'integer',
        'poCancelledBy' => 'string',
        'poCancelledByName' => 'string',
        'cancelledComments' => 'string',
        'poTotalComRptCurrency' => 'float',
        'poTotalLocalCurrency' => 'float',
        'poTotalSupplierDefaultCurrency' => 'float',
        'poTotalSupplierTransactionCurrency' => 'float',
        'poDiscountPercentage' => 'float',
        'poDiscountAmount' => 'float',
        'supplierVATEligible' => 'integer',
        'VATPercentage' => 'float',
        'VATAmount' => 'float',
        'VATAmountLocal' => 'float',
        'VATAmountRpt' => 'float',
        'shipTocontactPersonID' => 'string',
        'shipTocontactPersonTelephone' => 'string',
        'shipTocontactPersonFaxNo' => 'string',
        'shipTocontactPersonEmail' => 'string',
        'invoiceTocontactPersonID' => 'string',
        'invoiceTocontactPersonTelephone' => 'string',
        'invoiceTocontactPersonFaxNo' => 'string',
        'invoiceTocontactPersonEmail' => 'string',
        'soldTocontactPersonID' => 'string',
        'soldTocontactPersonTelephone' => 'string',
        'soldTocontactPersonFaxNo' => 'string',
        'soldTocontactPersonEmail' => 'string',
        'priority' => 'integer',
        'approved' => 'integer',
        'approvedByUserID' => 'string',
        'approvedByUserSystemID' => 'integer',
        'addOnPercent' => 'float',
        'addOnDefaultPercent' => 'float',
        'GRVTrackingID' => 'integer',
        'logisticDoneYN' => 'integer',
        'poClosedYN' => 'integer',
        'grvRecieved' => 'integer',
        'invoicedBooked' => 'integer',
        'timesReferred' => 'integer',
        'refferedBackYN' => 'integer',
        'poType' => 'string',
        'poType_N' => 'integer',
        'poTypeID' => 'integer',
        'docRefNo' => 'string',
        'RollLevForApp_curr' => 'integer',
        'sentToSupplier' => 'integer',
        'sentToSupplierByEmpID' => 'string',
        'sentToSupplierByEmpName' => 'string',
        'budgetBlockYN' => 'integer',
        'budgetYear' => 'integer',
        'hidePOYN' => 'integer',
        'hideByEmpID' => 'string',
        'hideByEmpName' => 'string',
        'hideComments' => 'string',
        'WO_purchaseOrderID' => 'integer',
        'WO_NoOfAutoGenerationTimes' => 'integer',
        'WO_NoOfGeneratedTimes' => 'integer',
        'WO_fullyGenerated' => 'integer',
        'WO_amendYN' => 'integer',
        'WO_amendRequestedByEmpID' => 'string',
        'WO_confirmedYN' => 'integer',
        'WO_confirmedByEmpID' => 'string',
        'WO_terminateYN' => 'integer',
        'WO_terminatedByEmpID' => 'string',
        'WO_terminateComments' => 'string',
        'partiallyGRVAllowed' => 'integer',
        'logisticsAvailable' => 'integer',
        'vatRegisteredYN' => 'integer',
        'manuallyClosed' => 'integer',
        'manuallyClosedByEmpSystemID' => 'integer',
        'manuallyClosedByEmpID' => 'string',
        'manuallyClosedByEmpName' => 'string',
        'manuallyClosedComment' => 'string',
        'createdUserGroup' => 'string',
        'createdPcID' => 'string',
        'createdUserSystemID' => 'integer',
        'createdUserID' => 'string',
        'modifiedPc' => 'string',
        'modifiedUser' => 'string',
        'isSelected' => 'boolean',
        'supCategoryICVMasterID' => 'integer',
        'workOrderGenerateID' => 'integer',
        'supCategorySubICVID' => 'integer',
        'rcmActivated' => 'integer',
        'orderType' => 'boolean',
        'projectID' => 'integer',
        'approval_remarks' => 'string',
        'categoryID'  => 'integer',
        'upload_job_status'  => 'integer',
        
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

    public function sold_to()
    {
        return $this->belongsTo('App\Models\Address', 'soldToAddressID', 'addressID');
    }

    public function sub_work_orders()
    {
        return $this->hasMany('App\Models\ProcumentOrder', 'WO_purchaseOrderID', 'purchaseOrderID');
    }

    public function confirmed_by()
    {
        return $this->belongsTo('App\Models\Employee', 'poConfirmedByEmpSystemID', 'employeeSystemID');
    }

    public function cancelled_by()
    {
        return $this->belongsTo('App\Models\Employee', 'poCancelledBySystemID', 'employeeSystemID');
    }

    public function manually_closed_by()
    {
        return $this->belongsTo('App\Models\Employee', 'manuallyClosedByEmpSystemID', 'employeeSystemID');
    }

    public function modified_by()
    {
        return $this->belongsTo('App\Models\Employee', 'modifiedUserSystemID', 'employeeSystemID');
    }

    public function amend_by()
    {
        return $this->belongsTo('App\Models\Employee', 'WO_amendRequestedByEmpSystemID', 'employeeSystemID');
    }

    public function sent_supplier_by()
    {
        return $this->belongsTo('App\Models\Employee', 'sentToSupplierByEmpSystemID', 'employeeSystemID');
    }

    public function location()
    {
        return $this->belongsTo('App\Models\Location', 'poLocation', 'locationID');
    }

    public function segment()
    {
        return $this->belongsTo('App\Models\SegmentMaster', 'serviceLineSystemID', 'serviceLineSystemID');
    }

    public function supplier()
    {
        return $this->belongsTo('App\Models\SupplierMaster', 'supplierID', 'supplierCodeSystem');
    }

    public function currency()
    {
        return $this->belongsTo('App\Models\CurrencyMaster', 'supplierTransactionCurrencyID', 'currencyID');
    }

    public function fcategory()
    {
        return $this->belongsTo('App\Models\FinanceItemCategoryMaster', 'financeCategory', 'itemCategoryID');
    }

    public function financeCategory(){
       return $this->fcategory();
   }

    public function detail()
    {
        return $this->hasMany('App\Models\PurchaseOrderDetails', 'purchaseOrderMasterID', 'purchaseOrderID');
    }

    public function approved()
    {
        return $this->hasMany('App\Models\DocumentApproved', 'documentSystemCode', 'purchaseOrderID');
    }

    public function approved_by()
    {
        return $this->hasMany('App\Models\DocumentApproved', 'documentSystemCode', 'purchaseOrderID');
    }

    public function suppliercontact()
    {
        return $this->belongsTo('App\Models\SupplierContactDetails', 'supplierID', 'supplierID');
    }

    public function company()
    {
        return $this->belongsTo('App\Models\Company', 'companySystemID', 'companySystemID');
    }

    public function secondarycompany()
    {
        return $this->belongsTo('App\Models\SecondaryCompany', 'companySystemID', 'companySystemID');
    }

    public function transactioncurrency()
    {
        return $this->belongsTo('App\Models\CurrencyMaster', 'supplierTransactionCurrencyID', 'currencyID');
    }

    public function localcurrency()
    {
        return $this->belongsTo('App\Models\CurrencyMaster', 'localCurrencyID', 'currencyID');
    }

    public function reportingcurrency()
    {
        return $this->belongsTo('App\Models\CurrencyMaster', 'companyReportingCurrencyID', 'currencyID');
    }

    public function companydocumentattachment()
    {
        return $this->hasMany('App\Models\CompanyDocumentAttachment', 'documentSystemID', 'documentSystemID');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     **/
    public function erpPurchaseorderdetails()
    {
        return $this->hasMany(\App\Models\ErpPurchaseorderdetail::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     **/
    public function status()
    {
        return $this->hasMany('\App\Models\PurchaseOrderStatus','purchaseOrderID','purchaseOrderID'); //->orderBy('purchaseOrderID', 'desc');
    }

    public function status_one(){
        return $this->hasOne('\App\Models\PurchaseOrderStatus','purchaseOrderID','purchaseOrderID')->orderBy('POStatusID', 'desc');
    }

    public function paymentTerms_by()
    {
        return $this->hasMany('\App\Models\PoPaymentTerms','poID','purchaseOrderID');
    }

    public function advance_detail()
    {
        return $this->hasMany('\App\Models\PoAdvancePayment','poID','purchaseOrderID');
    }


    public function document_by()
    {
        return $this->belongsTo('App\Models\DocumentMaster', 'documentSystemID', 'documentSystemID');
    }

    public function icv_category()
    {
        return $this->belongsTo('App\Models\SupplierCategoryICVMaster', 'supCategoryICVMasterID', 'supCategoryICVMasterID');
    }

    public function icv_sub_category()
    {
        return $this->belongsTo('App\Models\SupplierCategoryICVSub', 'supCategorySubICVID', 'supCategorySubICVID');
    }

    public function project()
    {
        return $this->belongsTo('App\Models\ErpProjectMaster', 'projectID', 'id');
    }

    public function budget_consumed()
    {
        return $this->hasMany('App\Models\BudgetConsumedData', 'documentSystemID', 'purchaseOrderID');
    }

    
    public function category()
    {
        return $this->belongsTo('App\Models\PoCategory', 'categoryID', 'id');
    }

    public function getIsWoAmendAccessAttribute()
    {
        $value = false;
//        $empId = Helper::getEmployeeSystemID();// && $this->WO_amendRequestedByEmpSystemID == $empId
        if($this->documentSystemID == 5 && $this->poType_N == 6 && $this->WO_confirmedYN == 0 && $this->WO_amendYN == -1 ){
            $value = true;
        }
        return $value;
    }

    public function getIsVatEligibleAttribute()
    {
        return TaxService::checkPOVATEligible($this->supplierVATEligible,$this->vatRegisteredYN);
    }

    public function getRcmAvailableAttribute()
    {
        return TaxService::getRCMAvailable($this->companySystemID,$this->supplierID);
    }

    public function audit_trial()
    {
        return $this->hasMany('App\Models\AuditTrail', 'documentSystemCode', 'purchaseOrderID')->whereIn('documentSystemID',[2,5,52]);
    }

    public function scopeEmployeeJoin($q,$as = 'employees' ,$column = 'createdUserSystemID',$columnAs = 'empName'){
        $q->leftJoin('employees as '. $as, $as.'.employeeSystemID', '=', 'erp_purchaseordermaster.'.$column)
            ->addSelect($as.".empName as ".$columnAs);
    }

    public function scopeDepartmentJoin($q,$as = 'department', $column = 'serviceLineSystemID' , $columnAs = 'ServiceLineDes')
    {
        return $q->leftJoin('serviceline as '.$as,$as.'.serviceLineSystemID','erp_purchaseordermaster.'.$column);
    }

    public function scopeCategoryJoin($q,$as = 'category', $column = 'financeCategory' , $columnAs = 'categoryDescription')
    {
        return $q->leftJoin('financeitemcategorymaster as '.$as,$as.'.itemCategoryID','erp_purchaseordermaster.'.$column);
    }

    public function scopeSupplierJoin($q,$as = 'supplier', $column = 'supplierID' , $columnAs = 'primarySupplierCode')
    {
        return $q->leftJoin('suppliermaster as '.$as,$as.'.supplierCodeSystem','erp_purchaseordermaster.'.$column);
    }

    public function scopeCurrencyJoin($q,$as = 'cu', $column = 'supplierTransactionCurrencyID' , $columnAs = 'currencyCode', $decimalPlaceAs = ['poTotalSupplierTransactionCurrency'])
    {
        $selectedColumns = [];
        foreach ($decimalPlaceAs as $d){
            $dColumn = $as.".DecimalPlaces as ".$d."DecimalPlaces";
            array_push($selectedColumns,$dColumn);
        }
        $code = $as.".currencyCode as ".$columnAs;
        array_push($selectedColumns,$code);
        return $q->leftJoin('currencymaster as '.$as,$as.'.currencyID','erp_purchaseordermaster.'.$column)
            ->addSelect($selectedColumns);
    }

    public function scopeDetailJoin($q)
    {
        return $q->join('erp_purchaseorderdetails','erp_purchaseorderdetails.purchaseOrderMasterID','erp_purchaseordermaster.purchaseOrderID');
    }

    public function scopeUnitJoin($q,$as = 'unit', $column = 'unitOfMeasure' , $columnAs = 'primarySupplierCode')
    {
        return $q->leftJoin('units as '.$as,$as.'.UnitID','erp_purchaseorderdetails.'.$column);
    }

    public function scopeSupplierCurrencyJoin($q,$as = 'cu', $column = 'supplierTransactionCurrencyID' , $columnAs = 'currencyCode', $decimalPlaceAs = 'poTotalSupplierTransactionCurrency')
    {
        return $q->leftJoin('currencymaster as '.$as,$as.'.currencyID','supplier.'.$column)
            ->addSelect($as.".DecimalPlaces as ".$decimalPlaceAs."DecimalPlaces",$as.".currencyCode as ".$columnAs);
    }

    public function scopeSupplierCountryJoin($q,$as = 'supplier_country', $column = 'countryID' , $columnAs = 'countryID')
    {
        return $q->leftJoin('countrymaster as '.$as,$as.'.countryID','supplier.'.$column);
    }

    public function budget_transfer_addition()
    {
        return $this->hasMany('App\Models\BudgetReviewTransferAddition', ['documentSystemCode', 'documentSystemID'], ['purchaseOrderID', 'documentSystemID']);
    }

     public function budget_consumed_data()
    {
        return $this->hasMany('App\Models\BudgetConsumedData', ['documentSystemCode', 'documentSystemID'], ['purchaseOrderID', 'documentSystemID']);
    }

    public function grv_details(){
        return $this->hasMany('\App\Models\GRVDetails','purchaseOrderMastertID','purchaseOrderID');
    }
}
