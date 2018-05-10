<?php
/**
 * =============================================
 * -- File Name : GRVMaster.php
 * -- Project Name : ERP
 * -- Module Name :  GRV Master
 * -- Author : Mohamed Fayas
 * -- Create date : 04- May 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 */
namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class GRVMaster
 * @package App\Models
 * @version April 11, 2018, 12:12 pm UTC
 *
 * @property string grvType
 * @property integer companySystemID
 * @property string companyID
 * @property integer serviceLineSystemID
 * @property string serviceLineCode
 * @property string companyAddress
 * @property integer companyFinanceYearID
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

    public $table = 'erp_grvmaster';
    
    const CREATED_AT = 'createdDateTime';
    const UPDATED_AT = 'timeStamp';
    protected $primaryKey  = 'grvAutoID';

    protected $dates = ['deleted_at'];


    public $fillable = [
        'grvType',
        'companySystemID',
        'companyID',
        'serviceLineSystemID',
        'serviceLineCode',
        'companyAddress',
        'companyFinanceYearID',
        'FYBiggin',
        'FYEnd',
        'documentSystemID',
        'documentID',
        'grvDate',
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
        'grvConfirmedByEmpID',
        'grvConfirmedByName',
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
        'timesReferred',
        'RollLevForApp_curr',
        'invoiceBeforeGRVYN',
        'deliveryConfirmedYN',
        'interCompanyTransferYN',
        'FromCompanyID',
        'createdUserGroup',
        'createdPcID',
        'createdUserID',
        'modifiedPc',
        'modifiedUser',
        'createdDateTime',
        'timeStamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'grvAutoID' => 'integer',
        'grvType' => 'string',
        'companySystemID' => 'integer',
        'companyID' => 'string',
        'serviceLineSystemID' => 'integer',
        'serviceLineCode' => 'string',
        'companyAddress' => 'string',
        'companyFinanceYearID' => 'integer',
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
        'grvConfirmedByEmpID' => 'string',
        'grvConfirmedByName' => 'string',
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
        'timesReferred' => 'integer',
        'RollLevForApp_curr' => 'integer',
        'invoiceBeforeGRVYN' => 'integer',
        'deliveryConfirmedYN' => 'integer',
        'interCompanyTransferYN' => 'integer',
        'FromCompanyID' => 'string',
        'createdUserGroup' => 'string',
        'createdPcID' => 'string',
        'createdUserID' => 'string',
        'modifiedPc' => 'string',
        'modifiedUser' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
