<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="DirectInvoiceDetailsRefferedBack",
 *      required={""},
 *      @SWG\Property(
 *          property="directInvoiceDetRefferedBackID",
 *          description="directInvoiceDetRefferedBackID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="directInvoiceDetailsID",
 *          description="directInvoiceDetailsID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="directInvoiceAutoID",
 *          description="directInvoiceAutoID",
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
 *          property="serviceLineCode",
 *          description="serviceLineCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="chartOfAccountSystemID",
 *          description="chartOfAccountSystemID",
 *          type="integer",
 *          format="int32"
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
 *          property="comments",
 *          description="comments",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="percentage",
 *          description="percentage",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="DIAmountCurrency",
 *          description="DIAmountCurrency",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="DIAmountCurrencyER",
 *          description="DIAmountCurrencyER",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="DIAmount",
 *          description="DIAmount",
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
 *      ),
 *      @SWG\Property(
 *          property="budgetYear",
 *          description="budgetYear",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isExtraAddon",
 *          description="isExtraAddon",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="timesReferred",
 *          description="timesReferred",
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class DirectInvoiceDetailsRefferedBack extends Model
{

    public $table = 'erp_directinvoicedetailsrefferedback';
    
    const CREATED_AT = 'timeStamp';
    const UPDATED_AT = 'timeStamp';

    protected $primaryKey = 'directInvoiceDetRefferedBackID';

    public $fillable = [
        'directInvoiceDetailsID',
        'directInvoiceAutoID',
        'companySystemID',
        'companyID',
        'serviceLineSystemID',
        'serviceLineCode',
        'chartOfAccountSystemID',
        'glCode',
        'glCodeDes',
        'comments',
        'percentage',
        'DIAmountCurrency',
        'DIAmountCurrencyER',
        'DIAmount',
        'localCurrency',
        'localCurrencyER',
        'localAmount',
        'comRptCurrency',
        'comRptCurrencyER',
        'comRptAmount',
        'budgetYear',
        'isExtraAddon',
        'timesReferred',
        'timeStamp',
        'VATAmount',
        'VATAmountLocal',
        'VATAmountRpt',
        'netAmount',
        'netAmountLocal',
        'netAmountRpt',
        'detail_project_id',
        'whtApplicable',
        'whtAmount',
        'whtEdited',
        'contractID'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'directInvoiceDetRefferedBackID' => 'integer',
        'directInvoiceDetailsID' => 'integer',
        'directInvoiceAutoID' => 'integer',
        'companySystemID' => 'integer',
        'companyID' => 'string',
        'serviceLineSystemID' => 'integer',
        'serviceLineCode' => 'string',
        'chartOfAccountSystemID' => 'integer',
        'glCode' => 'string',
        'glCodeDes' => 'string',
        'comments' => 'string',
        'percentage' => 'float',
        'DIAmountCurrency' => 'integer',
        'DIAmountCurrencyER' => 'float',
        'DIAmount' => 'float',
        'localCurrency' => 'integer',
        'localCurrencyER' => 'float',
        'localAmount' => 'float',
        'comRptCurrency' => 'integer',
        'comRptCurrencyER' => 'float',
        'comRptAmount' => 'float',
        'budgetYear' => 'integer',
        'isExtraAddon' => 'integer',
        'timesReferred' => 'integer',
        'VATAmount' => 'float',
        'VATAmountLocal' => 'float',
        'VATAmountRpt' => 'float',
        'netAmount' => 'float',
        'netAmountLocal' => 'float',
        'netAmountRpt' => 'float',
        'detail_project_id' => 'integer',
        'contractID' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function segment()
    {
        return $this->belongsTo('App\Models\SegmentMaster', 'serviceLineSystemID', 'serviceLineSystemID');
    }

    public function chartofaccount()
    {
        return $this->belongsTo('App\Models\ChartOfAccount', 'chartOfAccountSystemID','chartOfAccountSystemID');
    }


}
