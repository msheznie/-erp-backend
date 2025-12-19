<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="PdcLog",
 *      required={""},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
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
 *          property="documentmasterAutoID",
 *          description="documentmasterAutoID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="paymentBankID",
 *          description="paymentBankID",
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
 *          property="currencyID",
 *          description="currencyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="chequeRegisterAutoID",
 *          description="chequeRegisterAutoID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="chequeNo",
 *          description="chequeNo",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="chequeDate",
 *          description="chequeDate",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="chequeStatus",
 *          description="chequeStatus",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="amount",
 *          description="amount",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="timestamp",
 *          description="timestamp",
 *          type="string",
 *          format="date-time"
 *      )
 * )
 */
class PdcLog extends Model
{

    public $table = 'erp_pdc_logs';
    
    const CREATED_AT = 'timestamp';
    const UPDATED_AT = 'timestamp';



    protected $appends = ['chequeStatusValue'];

    public $fillable = [
        'documentSystemID',
        'documentmasterAutoID',
        'paymentBankID',
        'companySystemID',
        'currencyID',
        'chequeRegisterAutoID',
        'chequeNo',
        'comments',
        'chequeDate',
        'chequeStatus',
        'amount',
        'chequePrinted',
        'chequePrintedDate',
        'chequePrintedBy',
        'referenceChequeID',
        'timestamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'documentSystemID' => 'integer',
        'chequePrintedBy' => 'integer',
        'chequePrintedDate' => 'datetime',
        'chequePrinted' => 'integer',
        'referenceChequeID' => 'integer',
        'documentmasterAutoID' => 'string',
        'paymentBankID' => 'integer',
        'companySystemID' => 'integer',
        'currencyID' => 'integer',
        'chequeRegisterAutoID' => 'integer',
        'chequeNo' => 'string',
        'comments' => 'string',
        'chequeDate' => 'datetime',
        'chequeStatus' => 'integer',
        'amount' => 'float',
        'timestamp' => 'datetime'
    ];

    protected $statuses = array(
        "0" => 'open',
        "1" => 'deposited',
        "2" => 'returned',
        "3" => 'done'
    );

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [

    ];

    public function getChequeStatusValueAttribute() {
       $status = $this->statuses[$this->chequeStatus];

       return $status ? trans("custom.$status") : null;
    }

    public function currency()
    {
        return $this->belongsTo('App\Models\CurrencyMaster','currencyID',  'currencyID');
    }

    public function printed_history()
    {
        return $this->hasMany('App\Models\PdcLogPrintedHistory','pdcLogID',  'id');
    }

    public function cheque_printed_by()
    {
        return $this->belongsTo('App\Models\Employee','chequePrintedBy',  'employeeSystemID');
    }

    public function bank() {
        return $this->belongsTo('App\Models\BankMaster','paymentBankID',  'bankmasterAutoID');
    }

    public function pay_supplier() {
        return $this->belongsTo('App\Models\PaySupplierInvoiceMaster','documentmasterAutoID',  'PayMasterAutoId');
    }

    public function customer_receive() {
        return $this->belongsTo('App\Models\CustomerReceivePayment','documentmasterAutoID',  'custReceivePaymentAutoID');
    }

    // public function scopeDocument($query) {
    //     return $query
    //     ->when($this->documentSystemID == 4,function($q){
    //         return $q->with('pay_supplier');
    //    },function($q){
    //         return $q->with('customer_receive');
    //    });
    // }

}
