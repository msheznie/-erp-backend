<?php
/**
 * =============================================
 * -- File Name : PurchaseReturnDetails.php
 * -- Project Name : ERP
 * -- Module Name : Purchase Return Details
 * -- Author : Mohamed Fayas
 * -- Create date : 31- July 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 */
namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="PurchaseReturnDetails",
 *      required={""},
 *      @SWG\Property(
 *          property="purhasereturnDetailID",
 *          description="purhasereturnDetailID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="purhaseReturnAutoID",
 *          description="purhaseReturnAutoID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="companyID",
 *          description="companyID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="grvAutoID",
 *          description="grvAutoID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="grvDetailsID",
 *          description="grvDetailsID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="itemCode",
 *          description="itemCode",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="itemPrimaryCode",
 *          description="itemPrimaryCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="itemDescription",
 *          description="itemDescription",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="supplierPartNumber",
 *          description="supplierPartNumber",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="unitOfMeasure",
 *          description="unitOfMeasure",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="GRVQty",
 *          description="GRVQty",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="comment",
 *          description="comment",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="noQty",
 *          description="noQty",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="supplierDefaultCurrencyID",
 *          description="supplierDefaultCurrencyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="supplierDefaultER",
 *          description="supplierDefaultER",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="supplierTransactionCurrencyID",
 *          description="supplierTransactionCurrencyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="supplierTransactionER",
 *          description="supplierTransactionER",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="companyReportingCurrencyID",
 *          description="companyReportingCurrencyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="companyReportingER",
 *          description="companyReportingER",
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
 *          property="localCurrencyER",
 *          description="localCurrencyER",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="GRVcostPerUnitLocalCur",
 *          description="GRVcostPerUnitLocalCur",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="GRVcostPerUnitSupDefaultCur",
 *          description="GRVcostPerUnitSupDefaultCur",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="GRVcostPerUnitSupTransCur",
 *          description="GRVcostPerUnitSupTransCur",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="GRVcostPerUnitComRptCur",
 *          description="GRVcostPerUnitComRptCur",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="netAmount",
 *          description="netAmount",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="netAmountLocal",
 *          description="netAmountLocal",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="netAmountRpt",
 *          description="netAmountRpt",
 *          type="number",
 *          format="float"
 *      )
 * )
 */
class PurchaseReturnDetails extends Model
{

    public $table = 'erp_purchasereturndetails';
    
    const CREATED_AT = 'timeStamp';
    const UPDATED_AT = 'timeStamp';
    protected $primaryKey  = 'purhasereturnDetailID';


    public $fillable = [
        'purhaseReturnAutoID',
        'companyID',
        'grvAutoID',
        'grvDetailsID',
        'itemCode',
        'itemPrimaryCode',
        'itemDescription',
        'supplierPartNumber',
        'unitOfMeasure',
        'GRVQty',
        'comment',
        'noQty',
        'supplierDefaultCurrencyID',
        'supplierDefaultER',
        'supplierTransactionCurrencyID',
        'supplierTransactionER',
        'companyReportingCurrencyID',
        'companyReportingER',
        'localCurrencyID',
        'localCurrencyER',
        'GRVcostPerUnitLocalCur',
        'GRVcostPerUnitSupDefaultCur',
        'GRVcostPerUnitSupTransCur',
        'GRVcostPerUnitComRptCur',
        'netAmount',
        'netAmountLocal',
        'netAmountRpt',
        'timeStamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'purhasereturnDetailID' => 'integer',
        'purhaseReturnAutoID' => 'integer',
        'companyID' => 'string',
        'grvAutoID' => 'integer',
        'grvDetailsID' => 'integer',
        'itemCode' => 'integer',
        'itemPrimaryCode' => 'string',
        'itemDescription' => 'string',
        'supplierPartNumber' => 'string',
        'unitOfMeasure' => 'integer',
        'GRVQty' => 'float',
        'comment' => 'string',
        'noQty' => 'float',
        'supplierDefaultCurrencyID' => 'integer',
        'supplierDefaultER' => 'float',
        'supplierTransactionCurrencyID' => 'integer',
        'supplierTransactionER' => 'float',
        'companyReportingCurrencyID' => 'integer',
        'companyReportingER' => 'float',
        'localCurrencyID' => 'integer',
        'localCurrencyER' => 'float',
        'GRVcostPerUnitLocalCur' => 'float',
        'GRVcostPerUnitSupDefaultCur' => 'float',
        'GRVcostPerUnitSupTransCur' => 'float',
        'GRVcostPerUnitComRptCur' => 'float',
        'netAmount' => 'float',
        'netAmountLocal' => 'float',
        'netAmountRpt' => 'float'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
