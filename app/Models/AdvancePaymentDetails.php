<?php

namespace App\Models;

use Eloquent as Model;
//use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class AdvancePaymentDetails
 * @package App\Models
 * @version April 25, 2018, 7:33 am UTC
 *
 * @property integer PayMasterAutoId
 * @property integer poAdvPaymentID
 * @property string companyID
 * @property integer purchaseOrderID
 * @property string purchaseOrderCode
 * @property string comments
 * @property float paymentAmount
 * @property integer supplierTransCurrencyID
 * @property float supplierTransER
 * @property integer supplierDefaultCurrencyID
 * @property float supplierDefaultCurrencyER
 * @property integer localCurrencyID
 * @property float localER
 * @property integer comRptCurrencyID
 * @property float comRptER
 * @property float supplierDefaultAmount
 * @property float supplierTransAmount
 * @property float localAmount
 * @property float comRptAmount
 * @property integer timesReferred
 * @property string|\Carbon\Carbon timeStamp
 */
class AdvancePaymentDetails extends Model
{
    //use SoftDeletes;

    public $table = 'erp_advancepaymentdetails';
    
    const CREATED_AT = 'createdDateTime';
    const UPDATED_AT = 'timeStamp';

    protected $primaryKey = 'advancePaymentDetailAutoID';

    protected $dates = ['deleted_at'];


    public $fillable = [
        'PayMasterAutoId',
        'poAdvPaymentID',
        'companyID',
        'purchaseOrderID',
        'purchaseOrderCode',
        'comments',
        'paymentAmount',
        'supplierTransCurrencyID',
        'supplierTransER',
        'supplierDefaultCurrencyID',
        'supplierDefaultCurrencyER',
        'localCurrencyID',
        'localER',
        'comRptCurrencyID',
        'comRptER',
        'supplierDefaultAmount',
        'supplierTransAmount',
        'localAmount',
        'comRptAmount',
        'timesReferred',
        'timeStamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'advancePaymentDetailAutoID' => 'integer',
        'PayMasterAutoId' => 'integer',
        'poAdvPaymentID' => 'integer',
        'companyID' => 'string',
        'purchaseOrderID' => 'integer',
        'purchaseOrderCode' => 'string',
        'comments' => 'string',
        'paymentAmount' => 'float',
        'supplierTransCurrencyID' => 'integer',
        'supplierTransER' => 'float',
        'supplierDefaultCurrencyID' => 'integer',
        'supplierDefaultCurrencyER' => 'float',
        'localCurrencyID' => 'integer',
        'localER' => 'float',
        'comRptCurrencyID' => 'integer',
        'comRptER' => 'float',
        'supplierDefaultAmount' => 'float',
        'supplierTransAmount' => 'float',
        'localAmount' => 'float',
        'comRptAmount' => 'float',
        'timesReferred' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
