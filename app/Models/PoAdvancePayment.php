<?php
/**
 * =============================================
 * -- File Name : PoAdvancePayment.php
 * -- Project Name : ERP
 * -- Module Name :  Po Advance Payment
 * -- Author : Nazir
 * -- Create date : 18 - April 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 * --
 */
namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class PoAdvancePayment
 * @package App\Models
 * @version April 10, 2018, 11:09 am UTC
 *
 * @property integer companySystemID
 * @property string companyID
 * @property integer serviceLineSystemID
 * @property string serviceLineID
 * @property integer poID
 * @property integer grvAutoID
 * @property string poCode
 * @property integer poTermID
 * @property integer supplierID
 * @property string SupplierPrimaryCode
 * @property string|\Carbon\Carbon reqDate
 * @property string narration
 * @property integer currencyID
 * @property float reqAmount
 * @property float reqAmountTransCur_amount
 * @property integer confirmedYN
 * @property integer approvedYN
 * @property integer selectedToPayment
 * @property integer fullyPaid
 * @property integer isAdvancePaymentYN
 * @property string|\Carbon\Carbon dueDate
 * @property integer LCPaymentYN
 * @property string requestedByEmpID
 * @property string requestedByEmpName
 * @property float reqAmountInPOTransCur
 * @property float reqAmountInPOLocalCur
 * @property float reqAmountInPORptCur
 * @property string|\Carbon\Carbon timestamp
 */
class PoAdvancePayment extends Model
{
    //use SoftDeletes;

    public $table = 'erp_purchaseorderadvpayment';

    const CREATED_AT = 'createdDateTime';
    const UPDATED_AT = 'timeStamp';

    protected $primaryKey = 'poAdvPaymentID';

    protected $dates = ['deleted_at'];


    public $fillable = [
        'companySystemID',
        'companyID',
        'serviceLineSystemID',
        'serviceLineID',
        'poID',
        'grvAutoID',
        'poCode',
        'poTermID',
        'supplierID',
        'SupplierPrimaryCode',
        'liabilityAccountSysemID',
        'liabilityAccount',
        'UnbilledGRVAccountSystemID',
        'UnbilledGRVAccount',
        'reqDate',
        'narration',
        'currencyID',
        'reqAmount',
        'reqAmountTransCur_amount',
        'logisticCategoryID',
        'confirmedYN',
        'approvedYN',
        'selectedToPayment',
        'fullyPaid',
        'isAdvancePaymentYN',
        'dueDate',
        'LCPaymentYN',
        'requestedByEmpID',
        'requestedByEmpName',
        'reqAmountInPOTransCur',
        'reqAmountInPOLocalCur',
        'reqAmountInPORptCur',
        'cancelledYN',
        'cancelledByEmployeeSystemID',
        'cancelledDate',
        'createdDateTime',
        'vatSubCategoryID',
        'VATAmount',
        'VATPercentage',
        'cancelledComment',
        'VATAmountLocal',
        'addVatOnPO',
        'VATAmountRpt',
        'timestamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'poAdvPaymentID' => 'integer',
        'companySystemID' => 'integer',
        'cancelledYN' => 'integer',
        'cancelledByEmployeeSystemID' => 'integer',
        'companyID' => 'string',
        'serviceLineSystemID' => 'integer',
        'serviceLineID' => 'string',
        'poID' => 'integer',
        'grvAutoID' => 'integer',
        'poCode' => 'string',
        'poTermID' => 'integer',
        'supplierID' => 'integer',
        'SupplierPrimaryCode' => 'string',
        'cancelledComment' => 'string',
        'liabilityAccountSysemID' => 'integer',
        'liabilityAccount'  => 'string',
        'UnbilledGRVAccountSystemID' => 'integer',
        'UnbilledGRVAccount'  => 'string',
        'narration' => 'string',
        'currencyID' => 'integer',
        'reqAmount' => 'float',
        'reqAmountTransCur_amount' => 'float',
        'logisticCategoryID' => 'integer',
        'confirmedYN' => 'integer',
        'approvedYN' => 'integer',
        'selectedToPayment' => 'integer',
        'fullyPaid' => 'integer',
        'isAdvancePaymentYN' => 'integer',
        'LCPaymentYN' => 'integer',
        'requestedByEmpID' => 'string',
        'requestedByEmpName' => 'string',
        'reqAmountInPOTransCur' => 'float',
        'reqAmountInPOLocalCur' => 'float',
        'reqAmountInPORptCur' => 'float',
        'vatSubCategoryID' => 'integer',
        'VATAmount' => 'float',
        'VATAmountLocal' => 'float',
        'VATPercentage' => 'float',
        'VATAmountRpt' => 'float',
        'addVatOnPO' => 'boolean',
        'sum_payment' => 'float'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [

    ];

    public function poterms()
    {
        return $this->belongsTo('App\Models\PoPaymentTerms', 'poTermID', 'paymentTermID');
    }

    public function supplier_by()
    {
        return $this->belongsTo('App\Models\SupplierMaster', 'supplierID', 'supplierCodeSystem');
    }

    public function currency()
    {
        return $this->belongsTo('App\Models\CurrencyMaster', 'currencyID', 'currencyID');
    }

    public function company()
    {
        return $this->belongsTo('App\Models\Company', 'companySystemID', 'companySystemID');
    }

    public function grv_by()
    {
        return $this->belongsTo('App\Models\GRVMaster', 'grvAutoID', 'grvAutoID');
    }

    public function category_by()
    {
        return $this->belongsTo('App\Models\AddonCostCategories', 'logisticCategoryID', 'idaddOnCostCategories');
    }

    public function po_master()
    {
        return $this->belongsTo('App\Models\ProcumentOrder', 'poID', 'purchaseOrderID');
    }
    public function last_detail()
    {
        return $this->hasOne('App\Models\AdvancePaymentDetails','poAdvPaymentID','poAdvPaymentID')->orderBy('advancePaymentDetailAutoID', 'desc');
    }
    public function details()
    {
        return $this->hasMany('App\Models\AdvancePaymentDetails','poAdvPaymentID','poAdvPaymentID');
    }

    public function scopeSumOfPaymentAmount($q){

        return $q->leftJoin('erp_advancepaymentdetails', 'erp_purchaseorderadvpayment.poAdvPaymentID', '=', 'erp_advancepaymentdetails.poAdvPaymentID')
                        ->selectRaw('erp_purchaseorderadvpayment.*,
                                     erp_advancepaymentdetails.PayMasterAutoId,
                                     Sum( erp_advancepaymentdetails.paymentAmount ) AS SumOfpaymentAmount')
                        ->groupBy('erp_advancepaymentdetails.poAdvPaymentID')
                        ->whereNotNull('erp_advancepaymentdetails.purchaseOrderID');
    }

    public function getSumPaymentAttribute()
    {
        return $this->details->sum('paymentAmount');
    }

    public function setSumPaymentAttribute($value)
    {
        $this->attributes['sum_payment'] = $this->details->sum('paymentAmount');
    }

    public function vat_sub_category()
    {
        return $this->belongsTo('App\Models\TaxVatCategories', 'vatSubCategoryID', 'taxVatSubCategoriesAutoID');
    }

     public function cancelled_by()
    {
        return $this->belongsTo('App\Models\Employee', 'cancelledByEmployeeSystemID', 'employeeSystemID');
    }

}
