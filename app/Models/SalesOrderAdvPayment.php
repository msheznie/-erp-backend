<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="SalesOrderAdvPayment",
 *      required={""},
 *      @SWG\Property(
 *          property="soAdvPaymentID",
 *          description="soAdvPaymentID",
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
 *          property="serviceLineSystemID",
 *          description="serviceLineSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="serviceLineID",
 *          description="serviceLineID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="soID",
 *          description="soID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="grvAutoID",
 *          description="grvAutoID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="soCode",
 *          description="soCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="soTermID",
 *          description="soTermID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="customerId",
 *          description="customerId",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="customerCode",
 *          description="customerCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="liabilityAccountSysemID",
 *          description="liabilityAccountSysemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="liabilityAccount",
 *          description="liabilityAccount",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="UnbilledGRVAccountSystemID",
 *          description="UnbilledGRVAccountSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="UnbilledGRVAccount",
 *          description="UnbilledGRVAccount",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="reqDate",
 *          description="reqDate",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="narration",
 *          description="narration",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="currencyID",
 *          description="currencyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="reqAmount",
 *          description="reqAmount",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="reqAmountTransCur_amount",
 *          description="reqAmountTransCur_amount",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="logisticCategoryID",
 *          description="logisticCategoryID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="confirmedYN",
 *          description="confirmedYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="approvedYN",
 *          description="approvedYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="selectedToPayment",
 *          description="this becomes -1 when this advance payment is added to a payment",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="fullyPaid",
 *          description="this becomes 2 when this advance payment is added to a payment and paid fully, 1 paid partially",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isAdvancePaymentYN",
 *          description="if this is 0 it's an advance payment for a PO else its customs or logistic charges",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="dueDate",
 *          description="dueDate",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="LCPaymentYN",
 *          description="0 is not an LC payment. 1 is an LC payment",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="requestedByEmpID",
 *          description="requestedByEmpID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="requestedByEmpName",
 *          description="requestedByEmpName",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="reqAmountInPOTransCur",
 *          description="reqAmountInPOTransCur",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="reqAmountInPOLocalCur",
 *          description="reqAmountInPOLocalCur",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="reqAmountInPORptCur",
 *          description="reqAmountInPORptCur",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="createdDateTime",
 *          description="createdDateTime",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="timestamp",
 *          description="timestamp",
 *          type="string",
 *          format="date-time"
 *      )
 * )
 */
class SalesOrderAdvPayment extends Model
{

    public $table = 'erp_salesorderadvpayment';

    const CREATED_AT = 'createdDateTime';
    const UPDATED_AT = 'timeStamp';

    protected $primaryKey = 'soAdvPaymentID';




    public $fillable = [
        'companySystemID',
        'companyID',
        'serviceLineSystemID',
        'serviceLineID',
        'soID',
        'grvAutoID',
        'soCode',
        'soTermID',
        'customerId',
        'customerCode',
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
        'createdDateTime',
        'timestamp',
        'VATAmount',
        'VATAmountLocal',
        'VATAmountRpt'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'soAdvPaymentID' => 'integer',
        'companySystemID' => 'integer',
        'companyID' => 'string',
        'serviceLineSystemID' => 'integer',
        'serviceLineID' => 'string',
        'soID' => 'integer',
        'grvAutoID' => 'integer',
        'soCode' => 'string',
        'soTermID' => 'integer',
        'customerId' => 'integer',
        'customerCode' => 'string',
        'liabilityAccountSysemID' => 'integer',
        'liabilityAccount' => 'string',
        'UnbilledGRVAccountSystemID' => 'integer',
        'UnbilledGRVAccount' => 'string',
        'reqDate' => 'datetime',
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
        'dueDate' => 'datetime',
        'LCPaymentYN' => 'integer',
        'requestedByEmpID' => 'string',
        'requestedByEmpName' => 'string',
        'reqAmountInPOTransCur' => 'float',
        'reqAmountInPOLocalCur' => 'float',
        'reqAmountInPORptCur' => 'float',
        'createdDateTime' => 'datetime',
        'timestamp' => 'datetime',
        'VATAmount' => 'float',
        'VATAmountLocal' => 'float',
        'VATAmountRpt' => 'float'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
