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
        'reqDate',
        'narration',
        'currencyID',
        'reqAmount',
        'reqAmountTransCur_amount',
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
        'createdDateTime',
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
        'companyID' => 'string',
        'serviceLineSystemID' => 'integer',
        'serviceLineID' => 'string',
        'poID' => 'integer',
        'grvAutoID' => 'integer',
        'poCode' => 'string',
        'poTermID' => 'integer',
        'supplierID' => 'integer',
        'SupplierPrimaryCode' => 'string',
        'narration' => 'string',
        'currencyID' => 'integer',
        'reqAmount' => 'float',
        'reqAmountTransCur_amount' => 'float',
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
        'reqAmountInPORptCur' => 'float'
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


}
