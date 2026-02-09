<?php
/**
 * =============================================
 * -- File Name : PoPaymentTerms.php
 * -- Project Name : ERP
 * -- Module Name :  Po Payment Terms
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
 * Class PoPaymentTerms
 * @package App\Models
 * @version April 10, 2018, 11:05 am UTC
 *
 * @property integer paymentTermsCategory
 * @property integer poID
 * @property string paymentTemDes
 * @property float comAmount
 * @property float comPercentage
 * @property integer inDays
 * @property string|\Carbon\Carbon comDate
 * @property integer LCPaymentYN
 * @property string|\Carbon\Carbon timestamp
 */
class PoPaymentTerms extends Model
{
    //use SoftDeletes;

    public $table = 'erp_popaymentterms';

    const CREATED_AT = 'createdDateTime';
    const UPDATED_AT = 'timeStamp';

    protected $primaryKey = 'paymentTermID';


    public $fillable = [
        'paymentTermsCategory',
        'poID',
        'paymentTemDes',
        'comAmount',
        'comPercentage',
        'inDays',
        'comDate',
        'LCPaymentYN',
        'timestamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'paymentTermID' => 'integer',
        'paymentTermsCategory' => 'integer',
        'poID' => 'integer',
        'paymentTemDes' => 'string',
        'comAmount' => 'float',
        'comPercentage' => 'float',
        'inDays' => 'integer',
        'LCPaymentYN' => 'integer',
        'deleted_at' => 'datetime',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function type()
    {
        return $this->belongsTo('App\Models\PoPaymentTermTypes', 'LCPaymentYN', 'paymentTermsCategoryID');
    }

    public function purchase_order_master(){ 
        return $this->hasOne(ProcumentOrder::class, 'purchaseOrderID', 'poID');
    }

    public function advance_payment_request()
    {
        return $this->belongsTo('App\Models\PoAdvancePayment', 'paymentTermID', 'poTermID');
    }
    
}
