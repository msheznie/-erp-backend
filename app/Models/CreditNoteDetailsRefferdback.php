<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="CreditNoteDetailsRefferdback",
 *      required={""},
 *      @SWG\Property(
 *          property="creditNoteRefferedBackDetailsID",
 *          description="creditNoteRefferedBackDetailsID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="creditNoteDetailsID",
 *          description="creditNoteDetailsID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="creditNoteAutoID",
 *          description="creditNoteAutoID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="companyID",
 *          description="companyID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="customerID",
 *          description="customerID",
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
 *          property="serviceLineCode",
 *          description="serviceLineCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="comments",
 *          description="comments",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="creditAmountCurrency",
 *          description="creditAmountCurrency",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="creditAmountCurrencyER",
 *          description="creditAmountCurrencyER",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="creditAmount",
 *          description="creditAmount",
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
 *          property="timesReferred",
 *          description="timesReferred",
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class CreditNoteDetailsRefferdback extends Model
{

    public $table = 'erp_creditnotedetailsrefferdback';

    const CREATED_AT = 'timeStamp';
    const UPDATED_AT = 'timestamp';

    protected $primaryKey = 'creditNoteRefferedBackDetailsID';

    public $fillable = [
        'creditNoteDetailsID',
        'creditNoteAutoID',
        'companySystemID',
        'companyID',
        'customerID',
        'chartOfAccountSystemID',
        'glCode',
        'glCodeDes',
        'serviceLineSystemID',
        'serviceLineCode',
        'contractUID',
        'clientContractID',
        'comments',
        'creditAmountCurrency',
        'creditAmountCurrencyER',
        'creditAmount',
        'localCurrency',
        'localCurrencyER',
        'localAmount',
        'comRptCurrency',
        'comRptCurrencyER',
        'comRptAmount',
        'budgetYear',
        'timesReferred',
        'timeStamp',
        'detail_project_id'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'creditNoteRefferedBackDetailsID' => 'integer',
        'creditNoteDetailsID' => 'integer',
        'creditNoteAutoID' => 'integer',
        'companySystemID' => 'integer',
        'companyID' => 'string',
        'customerID' => 'integer',
        'chartOfAccountSystemID' => 'integer',
        'glCode' => 'string',
        'glCodeDes' => 'string',
        'serviceLineSystemID' => 'integer',
        'serviceLineCode' => 'string',
        'contractUID' => 'integer',
        'clientContractID' => 'string',
        'comments' => 'string',
        'creditAmountCurrency' => 'integer',
        'creditAmountCurrencyER' => 'float',
        'creditAmount' => 'float',
        'localCurrency' => 'integer',
        'localCurrencyER' => 'float',
        'localAmount' => 'float',
        'comRptCurrency' => 'integer',
        'comRptCurrencyER' => 'float',
        'comRptAmount' => 'float',
        'budgetYear' => 'integer',
        'timesReferred' => 'integer',
        'detail_project_id' => 'integer'
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
