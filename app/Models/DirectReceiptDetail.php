<?php
/**
 * =============================================
 * -- File Name : DirectReceiptDetail.php
 * -- Project Name : ERP
 * -- Module Name :  Accounts receivable
 * -- Author : Mubashir
 * -- Create date : 24 - August 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 * --
 */
namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="DirectReceiptDetail",
 *      required={""},
 *      @SWG\Property(
 *          property="directReceiptDetailsID",
 *          description="directReceiptDetailsID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="directReceiptAutoID",
 *          description="directReceiptAutoID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="companyID",
 *          description="companyID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="serviceLineCode",
 *          description="serviceLineCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="glCode",
 *          description="glCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="glCodeDes",
 *          description="glCodeDes",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="contractID",
 *          description="contractID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="comments",
 *          description="comments",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="DRAmountCurrency",
 *          description="DRAmountCurrency",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="DDRAmountCurrencyER",
 *          description="DDRAmountCurrencyER",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="DRAmount",
 *          description="DRAmount",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="localCurrency",
 *          description="localCurrency",
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
 *          property="localAmount",
 *          description="localAmount",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="comRptCurrency",
 *          description="comRptCurrency",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="comRptCurrencyER",
 *          description="comRptCurrencyER",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="comRptAmount",
 *          description="comRptAmount",
 *          type="number",
 *          format="float"
 *      )
 * )
 */
class DirectReceiptDetail extends Model
{

    public $table = 'erp_directreceiptdetails';
    
    const CREATED_AT = 'timeStamp';
    const UPDATED_AT = 'timeStamp';
    protected $primaryKey = 'directReceiptDetailsID';


    public $fillable = [
        'directReceiptAutoID',
        'companyID',
        'companySystemID',
        'serviceLineSystemID',
        'serviceLineCode',
        'chartOfAccountSystemID',
        'glCode',
        'glCodeDes',
        'contractUID',
        'contractID',
        'comments',
        'DRAmountCurrency',
        'DDRAmountCurrencyER',
        'DRAmount',
        'localCurrency',
        'localCurrencyER',
        'localAmount',
        'comRptCurrency',
        'comRptCurrencyER',
        'comRptAmount',
        'timeStamp',
        'VATAmount',
        'VATAmountLocal',
        'VATAmountRpt',
        'netAmount',
        'netAmountLocal',
        'netAmountRpt',
        'detail_project_id',
        'VATPercentage',
        'vatMasterCategoryID',
        'vatSubCategoryID',
        'amountBeforeVAT'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'directReceiptDetailsID' => 'integer',
        'directReceiptAutoID' => 'integer',
        'companySystemID' => 'integer',
        'companyID' => 'string',
        'serviceLineSystemID' => 'integer',
        'serviceLineCode' => 'string',
        'chartOfAccountSystemID' => 'integer',
        'glCode' => 'string',
        'glCodeDes' => 'string',
        'contractUID' => 'integer',
        'contractID' => 'string',
        'comments' => 'string',
        'DRAmountCurrency' => 'integer',
        'DDRAmountCurrencyER' => 'float',
        'DRAmount' => 'float',
        'localCurrency' => 'integer',
        'localCurrencyER' => 'float',
        'localAmount' => 'float',
        'comRptCurrency' => 'integer',
        'comRptCurrencyER' => 'float',
        'comRptAmount' => 'float',
        'VATAmount' => 'float',
        'VATAmountLocal' => 'float',
        'VATAmountRpt' => 'float',
        'netAmount' => 'float',
        'netAmountLocal' => 'float',
        'netAmountRpt' => 'float',
        'detail_project_id' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function chartofaccount()
    {
        return $this->belongsTo('App\Models\ChartOfAccount', 'chartOfAccountSystemID','chartOfAccountSystemID');
    }

    public function segment()
    {
        return $this->belongsTo('App\Models\SegmentMaster', 'serviceLineSystemID', 'serviceLineSystemID');
    }

    public function master()
    {
        return $this->belongsTo(CustomerReceivePayment::class, 'directReceiptAutoID', 'custReceivePaymentAutoID');
    }
    
    public function project()
    {
        return $this->belongsTo('App\Models\ErpProjectMaster', 'detail_project_id', 'id');
    }
}
