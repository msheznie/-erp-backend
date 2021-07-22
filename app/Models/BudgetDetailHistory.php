<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="BudgetDetailHistory",
 *      required={""},
 *      @SWG\Property(
 *          property="budjetDetailsID",
 *          description="budjetDetailsID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="budjetDetailsHistoryID",
 *          description="budjetDetailsHistoryID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="budgetmasterID",
 *          description="budgetmasterID",
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
 *          property="companyId",
 *          description="companyId",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="companyFinanceYearID",
 *          description="companyFinanceYearID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="serviceLineSystemID",
 *          description="serviceLineSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="serviceLine",
 *          description="serviceLine",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="templateDetailID",
 *          description="templateDetailID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="chartOfAccountID",
 *          description="chartOfAccountID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="glCode",
 *          description="glCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="glCodeType",
 *          description="glCodeType",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="Year",
 *          description="Year",
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
 *          property="budjetAmtLocal",
 *          description="budjetAmtLocal",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="budjetAmtRpt",
 *          description="budjetAmtRpt",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="createdByUserSystemID",
 *          description="createdByUserSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="createdByUserID",
 *          description="createdByUserID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="modifiedByUserSystemID",
 *          description="modifiedByUserSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="modifiedByUserID",
 *          description="modifiedByUserID",
 *          type="string"
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
class BudgetDetailHistory extends Model
{

    public $table = 'erp_budjetdetails_history';
    
    const CREATED_AT = 'timestamp';
    const UPDATED_AT = 'timestamp';

    protected $primaryKey = 'budjetDetailsHistoryID';


    public $fillable = [
        'budjetDetailsID',
        'budgetmasterID',
        'companySystemID',
        'companyId',
        'companyFinanceYearID',
        'serviceLineSystemID',
        'serviceLine',
        'templateDetailID',
        'chartOfAccountID',
        'glCode',
        'glCodeType',
        'Year',
        'month',
        'budjetAmtLocal',
        'budjetAmtRpt',
        'createdByUserSystemID',
        'createdByUserID',
        'modifiedByUserSystemID',
        'modifiedByUserID',
        'createdDateTime',
        'timestamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'budjetDetailsID' => 'integer',
        'budjetDetailsHistoryID' => 'integer',
        'budgetmasterID' => 'integer',
        'companySystemID' => 'integer',
        'companyId' => 'string',
        'companyFinanceYearID' => 'integer',
        'serviceLineSystemID' => 'integer',
        'serviceLine' => 'string',
        'templateDetailID' => 'integer',
        'chartOfAccountID' => 'integer',
        'glCode' => 'string',
        'glCodeType' => 'string',
        'Year' => 'integer',
        'month' => 'integer',
        'budjetAmtLocal' => 'float',
        'budjetAmtRpt' => 'float',
        'createdByUserSystemID' => 'integer',
        'createdByUserID' => 'string',
        'modifiedByUserSystemID' => 'integer',
        'modifiedByUserID' => 'string',
        'createdDateTime' => 'datetime',
        'timestamp' => 'datetime'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'budjetDetailsID' => 'required'
    ];

    
}
