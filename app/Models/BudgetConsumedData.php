<?php
/**
 * =============================================
 * -- File Name : BudgetConsumedData.php
 * -- Project Name : ERP
 * -- Module Name :  Budget Consumed Data
 * -- Author : Nazir
 * -- Create date : 30 - May 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 * --
 */

namespace App\Models;

use Eloquent as Model;
use Awobaz\Compoships\Compoships;

/**
 * @SWG\Definition(
 *      definition="BudgetConsumedData",
 *      required={""},
 *      @SWG\Property(
 *          property="budgetConsumedDataAutoID",
 *          description="budgetConsumedDataAutoID",
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
 *          property="chartOfAccountID",
 *          description="chartOfAccountID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="GLCode",
 *          description="GLCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="year",
 *          description="year",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="month",
 *          description="month",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="consumedLocalCurrencyID",
 *          description="consumedLocalCurrencyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="consumedLocalAmount",
 *          description="consumedLocalAmount",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="consumedRptCurrencyID",
 *          description="consumedRptCurrencyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="consumedRptAmount",
 *          description="consumedRptAmount",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="consumeYN",
 *          description="consumeYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="timestamp",
 *          description="timestamp",
 *          type="string"
 *      )
 * )
 */
class BudgetConsumedData extends Model
{
    use Compoships;
    public $table = 'erp_budgetconsumeddata';
    
    const CREATED_AT = NULL; //'timestamp';
    const UPDATED_AT = NULL; //'timestamp';

    protected $primaryKey = 'budgetConsumedDataAutoID';

    public $fillable = [
        'companySystemID',
        'companyID',
        'serviceLineSystemID',
        'serviceLineCode',
        'documentSystemID',
        'documentID',
        'documentSystemCode',
        'documentCode',
        'chartOfAccountID',
        'GLCode',
        'year',
        'month',
        'consumedLocalCurrencyID',
        'consumedLocalAmount',
        'consumedRptCurrencyID',
        'consumedRptAmount',
        'consumeYN',
        'timestamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'budgetConsumedDataAutoID' => 'integer',
        'companySystemID' => 'integer',
        'companyID' => 'string',
        'serviceLineSystemID' => 'integer',
        'serviceLineCode' => 'string',
        'documentSystemID' => 'integer',
        'documentID' => 'string',
        'documentSystemCode' => 'integer',
        'documentCode' => 'string',
        'chartOfAccountID' => 'integer',
        'GLCode' => 'string',
        'year' => 'integer',
        'month' => 'integer',
        'consumedLocalCurrencyID' => 'integer',
        'consumedLocalAmount' => 'float',
        'consumedRptCurrencyID' => 'integer',
        'consumedRptAmount' => 'float',
        'consumeYN' => 'integer',
        'timestamp' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function document_by()
    {
        return $this->belongsTo('App\Models\DocumentMaster', 'documentSystemID', 'documentSystemID');
    }

    public function budget_detail()
    {
        return $this->belongsTo('App\Models\Budjetdetails', ['chartOfAccountID', 'year'], ['chartOfAccountID', 'Year']);
    }
}
