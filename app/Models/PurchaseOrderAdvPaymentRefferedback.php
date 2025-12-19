<?php
/**
 * =============================================
 * -- File Name : PurchaseOrderAdvPaymentRefferedback.php
 * -- Project Name : ERP
 * -- Module Name :  PurchaseOrderAdvPaymentRefferedback
 * -- Author : Nazir
 * -- Create date : 23 - July 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 * --
 */
namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="PurchaseOrderAdvPaymentRefferedback",
 *      required={""},
 *      @SWG\Property(
 *          property="poAdvPaymentReffredBackID",
 *          description="poAdvPaymentReffredBackID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="poAdvPaymentID",
 *          description="poAdvPaymentID",
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
 *          property="poID",
 *          description="poID",
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
 *          property="poCode",
 *          description="poCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="poTermID",
 *          description="poTermID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="supplierID",
 *          description="supplierID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="SupplierPrimaryCode",
 *          description="SupplierPrimaryCode",
 *          type="string"
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
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="reqAmountTransCur_amount",
 *          description="reqAmountTransCur_amount",
 *          type="number",
 *          format="float"
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
 *          description="selectedToPayment",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="fullyPaid",
 *          description="fullyPaid",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isAdvancePaymentYN",
 *          description="isAdvancePaymentYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="LCPaymentYN",
 *          description="LCPaymentYN",
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
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="reqAmountInPOLocalCur",
 *          description="reqAmountInPOLocalCur",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="reqAmountInPORptCur",
 *          description="reqAmountInPORptCur",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="timesReferred",
 *          description="timesReferred",
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class PurchaseOrderAdvPaymentRefferedback extends Model
{

    public $table = 'erp_purchaseorderadvpaymentrefferedback';

    const CREATED_AT = 'createdDateTime';
    const UPDATED_AT = 'timeStamp';

    protected $primaryKey = 'poAdvPaymentReffredBackID';


    public $fillable = [
        'poAdvPaymentID',
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
        'timesReferred',
        'createdDateTime',
        'timestamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'poAdvPaymentReffredBackID' => 'integer',
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
        'reqAmountInPORptCur' => 'float',
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
