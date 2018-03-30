<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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
    //use SoftDeletes;

    public $table = 'erp_purchaseordermaster';
    
    const CREATED_AT = 'createdDateTime';
    const UPDATED_AT = 'timeStamp';

    protected $primaryKey  = 'purchaseOrderID';

    protected $dates = ['deleted_at'];

    public $fillable = [
        'poProcessId',
        'companySystemID',
        'companyID',
        'departmentID',
        'serviceLineSystemID',
        'serviceLine',
        'companyAddress',
        'documentID',
        'purchaseOrderCode',
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
        'addOnPercent',
        'addOnDefaultPercent',
        'GRVTrackingID',
        'logisticDoneYN',
        'poClosedYN',
        'grvRecieved',
        'invoicedBooked',
        'timesReferred',
        'poType',
        'poType_N',
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
        'createdUserGroup',
        'createdPcID',
        'createdUserID',
        'createdUserSystemID',
        'modifiedPc',
        'modifiedUser',
        'createdDateTime',
        'isSelected',
        'timeStamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'purchaseOrderID' => 'integer',
        'poProcessId' => 'integer',
        'companySystemID' => 'integer',
        'companyID' => 'string',
        'departmentID' => 'string',
        'serviceLineSystemID' => 'integer',
        'serviceLine' => 'string',
        'companyAddress' => 'string',
        'documentID' => 'string',
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
        'addOnPercent' => 'float',
        'addOnDefaultPercent' => 'float',
        'GRVTrackingID' => 'integer',
        'logisticDoneYN' => 'integer',
        'poClosedYN' => 'integer',
        'grvRecieved' => 'integer',
        'invoicedBooked' => 'integer',
        'timesReferred' => 'integer',
        'poType' => 'string',
        'poType_N' => 'integer',
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
        'createdUserGroup' => 'string',
        'createdPcID' => 'string',
        'createdUserSystemID' => 'integer',
        'createdUserID' => 'string',
        'modifiedPc' => 'string',
        'modifiedUser' => 'string',
        'isSelected' => 'boolean'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function created_by(){
        return $this->belongsTo('App\Models\Employee','createdUserSystemID','employeeSystemID');
    }

    public function location(){
        return $this->belongsTo('App\Models\Location','poLocation','locationID');
    }

    public function segment(){
        return $this->belongsTo('App\Models\SegmentMaster','serviceLineSystemID','serviceLineSystemID');
    }

    public function supplier(){
        return $this->belongsTo('App\Models\SupplierMaster','supplierID','supplierCodeSystem');
    }

    public function currency(){
        return $this->belongsTo('App\Models\CurrencyMaster','supplierTransactionCurrencyID','currencyID');
    }

    public function fcategory(){
        return $this->belongsTo('App\Models\FinanceItemCategoryMaster','financeCategory','itemCategoryID');
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     **/
    public function erpPurchaseorderdetails()
    {
        return $this->hasMany(\App\Models\ErpPurchaseorderdetail::class);
    }
}
