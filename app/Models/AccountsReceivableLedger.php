<?php
/**
 * =============================================
 * -- File Name : AccountsReceivableLedger.php
 * -- Project Name : ERP
 * -- Module Name : Accounts Receivable
 * -- Author : Mohamed Fayas
 * -- Create date : 12- June 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 */
namespace App\Models;

use App\helper\Helper;
use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="AccountsReceivableLedger",
 *      required={""},
 *      @SWG\Property(
 *          property="arAutoID",
 *          description="arAutoID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="companyID",
 *          description="companyID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="documentID",
 *          description="documentID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="documentCodeSystem",
 *          description="documentCodeSystem",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="documentCode",
 *          description="documentCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="customerID",
 *          description="customerID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="InvoiceNo",
 *          description="InvoiceNo",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="custTransCurrencyID",
 *          description="custTransCurrencyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="custTransER",
 *          description="custTransER",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="custInvoiceAmount",
 *          description="custInvoiceAmount",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="custDefaultCurrencyID",
 *          description="custDefaultCurrencyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="custDefaultCurrencyER",
 *          description="custDefaultCurrencyER",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="custDefaultAmount",
 *          description="custDefaultAmount",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="localCurrencyID",
 *          description="localCurrencyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="localER",
 *          description="localER",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="localAmount",
 *          description="localAmount",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="comRptCurrencyID",
 *          description="comRptCurrencyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="comRptER",
 *          description="comRptER",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="comRptAmount",
 *          description="comRptAmount",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="isInvoiceLockedYN",
 *          description="isInvoiceLockedYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="lockedBy",
 *          description="lockedBy",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="lockedByEmpName",
 *          description="lockedByEmpName",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="lockedComments",
 *          description="lockedComments",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="selectedToPaymentInv",
 *          description="selectedToPaymentInv",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="fullyInvoiced",
 *          description="fullyInvoiced",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="createdUserID",
 *          description="createdUserID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="createdPcID",
 *          description="createdPcID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="documentType",
 *          description="documentType",
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class AccountsReceivableLedger extends Model
{

    public $table = 'erp_accountsreceivableledger';
    
    const CREATED_AT = 'createdDateTime';
    const UPDATED_AT = 'timeStamp';

    protected $primaryKey  = 'arAutoID';

    public $fillable = [
        'companySystemID',
        'companyID',
        'documentSystemID',
        'documentID',
        'documentCodeSystem',
        'serviceLineSystemID',
        'serviceLineCode',
        'documentCode',
        'documentDate',
        'customerID',
        'InvoiceNo',
        'InvoiceDate',
        'custTransCurrencyID',
        'custTransER',
        'custInvoiceAmount',
        'custDefaultCurrencyID',
        'custDefaultCurrencyER',
        'custDefaultAmount',
        'localCurrencyID',
        'localER',
        'localAmount',
        'comRptCurrencyID',
        'comRptER',
        'comRptAmount',
        'isInvoiceLockedYN',
        'lockedBy',
        'lockedByEmpName',
        'lockedDate',
        'lockedComments',
        'selectedToPaymentInv',
        'fullyInvoiced',
        'createdDateTime',
        'createdUserSystemID',
        'createdUserID',
        'createdPcID',
        'documentType',
        'timeStamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'arAutoID' => 'integer',
        'companySystemID' => 'integer',
        'serviceLineSystemID' => 'integer',
        'serviceLineCode' => 'string',
        'companyID' => 'string',
        'documentSystemID' => 'integer',
        'documentID' => 'string',
        'documentCodeSystem' => 'integer',
        'documentCode' => 'string',
        'customerID' => 'integer',
        'InvoiceNo' => 'string',
        'custTransCurrencyID' => 'integer',
        'custTransER' => 'float',
        'custInvoiceAmount' => 'float',
        'custDefaultCurrencyID' => 'integer',
        'custDefaultCurrencyER' => 'float',
        'custDefaultAmount' => 'float',
        'localCurrencyID' => 'integer',
        'localER' => 'float',
        'localAmount' => 'float',
        'comRptCurrencyID' => 'integer',
        'comRptER' => 'float',
        'comRptAmount' => 'float',
        'isInvoiceLockedYN' => 'integer',
        'lockedBy' => 'string',
        'lockedByEmpName' => 'string',
        'lockedComments' => 'string',
        'selectedToPaymentInv' => 'integer',
        'fullyInvoiced' => 'integer',
        'createdUserSystemID' => 'integer',
        'createdUserID' => 'string',
        'createdPcID' => 'string',
        'documentType' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function setDocumentDateAttribute($value)
    {
        $this->attributes['documentDate'] = Helper::dateAddTime($value);
    }

     public function local_currency()
    {
        return $this->belongsTo('App\Models\CurrencyMaster', 'localCurrencyID', 'currencyID');
    }

    public function transaction_currency()
    {
        return $this->belongsTo('App\Models\CurrencyMaster', 'custTransCurrencyID', 'currencyID');
    }

    public function reporting_currency()
    {
        return $this->belongsTo('App\Models\CurrencyMaster', 'comRptCurrencyID', 'currencyID');
    }


     public function customer()
    {
        return $this->belongsTo('App\Models\CustomerMaster', 'customerID', 'customerCodeSystem');
    }

     public function customer_invoice()
    {
        return $this->belongsTo('App\Models\CustomerInvoiceDirect', 'documentCodeSystem', 'custInvoiceDirectAutoID')
                    ->where('documentSystemID', 20);
    }
}
