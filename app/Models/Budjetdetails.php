<?php
/**
 * =============================================
 * -- File Name : Budjetdetails.php
 * -- Project Name : ERP
 * -- Module Name :  Budget
 * -- Author : Fayas
 * -- Create date : 16 - October 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 */
namespace App\Models;

use Eloquent as Model;
use Awobaz\Compoships\Compoships;

/**
 * @SWG\Definition(
 *      definition="Budjetdetails",
 *      required={""},
 *      @SWG\Property(
 *          property="budjetDetailsID",
 *          description="budjetDetailsID",
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
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="budjetAmtRpt",
 *          description="budjetAmtRpt",
 *          type="number",
 *          format="float"
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
 *      )
 * )
 */
class Budjetdetails extends Model
{
    use Compoships;
    public $table = 'erp_budjetdetails';
    
    const CREATED_AT = 'createdDateTime';
    const UPDATED_AT = 'timestamp';
    protected $primaryKey = 'budjetDetailsID';


    public $fillable = [
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
        'modifiedByUserID' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function chart_of_account()
    {
        return $this->belongsTo('App\Models\ChartOfAccount', 'chartOfAccountID','chartOfAccountSystemID');
    }

    public function budget_master()
    {
        return $this->belongsTo('App\Models\BudgetMaster', 'budgetmasterID','budgetmasterID');
    }
}
