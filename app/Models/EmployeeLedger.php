<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="EmployeeLedger",
 *      required={""},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
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
 *          property="companyID",
 *          description="companyID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="documentSystemID",
 *          description="documentSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="documentID",
 *          description="documentID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="documentSystemCode",
 *          description="documentSystemCode",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="documentCode",
 *          description="documentCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="documentDate",
 *          description="documentDate",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="employeeSystemID",
 *          description="employeeSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="supplierInvoiceNo",
 *          description="supplierInvoiceNo",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="supplierInvoiceDate",
 *          description="supplierInvoiceDate",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="supplierTransCurrencyID",
 *          description="supplierTransCurrencyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="supplierTransER",
 *          description="supplierTransER",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="supplierInvoiceAmount",
 *          description="supplierInvoiceAmount",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="supplierDefaultCurrencyID",
 *          description="supplierDefaultCurrencyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="supplierDefaultCurrencyER",
 *          description="supplierDefaultCurrencyER",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="supplierDefaultAmount",
 *          description="supplierDefaultAmount",
 *          type="number",
 *          format="number"
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
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="localAmount",
 *          description="localAmount",
 *          type="number",
 *          format="number"
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
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="comRptAmount",
 *          description="comRptAmount",
 *          type="number",
 *          format="number"
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
 *          property="lockedDate",
 *          description="lockedDate",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="lockedComments",
 *          description="lockedComments",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="invoiceType",
 *          description="invoiceType",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="selectedToPaymentInv",
 *          description="selectedToPaymentInv",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="fullyInvoice",
 *          description="fullyInvoice",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="advancePaymentTypeID",
 *          description="advancePaymentTypeID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="createdDateTime",
 *          description="createdDateTime",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="createdUserSystemID",
 *          description="createdUserSystemID",
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
 *          property="timeStamp",
 *          description="timeStamp",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="created_at",
 *          description="created_at",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="updated_at",
 *          description="updated_at",
 *          type="string",
 *          format="date-time"
 *      )
 * )
 */
class EmployeeLedger extends Model
{

    public $table = 'employee_ledger';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';




    public $fillable = [
        'companySystemID',
        'companyID',
        'documentSystemID',
        'documentID',
        'documentSystemCode',
        'documentCode',
        'documentDate',
        'employeeSystemID',
        'supplierInvoiceNo',
        'supplierInvoiceDate',
        'supplierTransCurrencyID',
        'supplierTransER',
        'supplierInvoiceAmount',
        'supplierDefaultCurrencyID',
        'supplierDefaultCurrencyER',
        'supplierDefaultAmount',
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
        'invoiceType',
        'selectedToPaymentInv',
        'fullyInvoice',
        'advancePaymentTypeID',
        'createdDateTime',
        'createdUserSystemID',
        'createdUserID',
        'createdPcID',
        'timeStamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'companySystemID' => 'integer',
        'companyID' => 'string',
        'documentSystemID' => 'integer',
        'documentID' => 'string',
        'documentSystemCode' => 'integer',
        'documentCode' => 'string',
        'documentDate' => 'datetime',
        'employeeSystemID' => 'integer',
        'supplierInvoiceNo' => 'string',
        'supplierInvoiceDate' => 'datetime',
        'supplierTransCurrencyID' => 'integer',
        'supplierTransER' => 'float',
        'supplierInvoiceAmount' => 'float',
        'supplierDefaultCurrencyID' => 'integer',
        'supplierDefaultCurrencyER' => 'float',
        'supplierDefaultAmount' => 'float',
        'localCurrencyID' => 'integer',
        'localER' => 'float',
        'localAmount' => 'float',
        'comRptCurrencyID' => 'integer',
        'comRptER' => 'float',
        'comRptAmount' => 'float',
        'isInvoiceLockedYN' => 'integer',
        'lockedBy' => 'string',
        'lockedByEmpName' => 'string',
        'lockedDate' => 'datetime',
        'lockedComments' => 'string',
        'invoiceType' => 'integer',
        'selectedToPaymentInv' => 'integer',
        'fullyInvoice' => 'integer',
        'advancePaymentTypeID' => 'integer',
        'createdDateTime' => 'datetime',
        'createdUserSystemID' => 'integer',
        'createdUserID' => 'string',
        'createdPcID' => 'string',
        'timeStamp' => 'datetime'
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
        $this->attributes['documentDate'] = \Helper::dateAddTime($value);
    }

    public function local_currency()
    {
        return $this->belongsTo('App\Models\CurrencyMaster', 'localCurrencyID', 'currencyID');
    }

    public function transaction_currency()
    {
        return $this->belongsTo('App\Models\CurrencyMaster', 'supplierTransCurrencyID', 'currencyID');
    }

    public function reporting_currency()
    {
        return $this->belongsTo('App\Models\CurrencyMaster', 'comRptCurrencyID', 'currencyID');
    }

     public function employee()
    {
        return $this->belongsTo('App\Models\Employee', 'employeeSystemID', 'employeeSystemID');
    }
}
