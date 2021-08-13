<?php
/**
 * =============================================
 * -- File Name : BudgetAdjustment.php
 * -- Project Name : ERP
 * -- Module Name :  Budget Adjustment
 * -- Author : Fayas
 * -- Create date : 22 - October 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 */
namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="BudgetAdjustment",
 *      required={""},
 *      @SWG\Property(
 *          property="budgetAdjustmentID",
 *          description="budgetAdjustmentID",
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
 *          property="adjustedGLCodeSystemID",
 *          description="adjustedGLCodeSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="adjustedGLCode",
 *          description="adjustedGLCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="fromGLCodeSystemID",
 *          description="fromGLCodeSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="fromGLCode",
 *          description="fromGLCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="toGLCodeSystemID",
 *          description="toGLCodeSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="toGLCode",
 *          description="toGLCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="Year",
 *          description="Year",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="adjustmedLocalAmount",
 *          description="adjustmedLocalAmount",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="adjustmentRptAmount",
 *          description="adjustmentRptAmount",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="createdUserSystemID",
 *          description="createdUserSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="createdByUserID",
 *          description="createdByUserID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="modifiedUserSystemID",
 *          description="modifiedUserSystemID",
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
class BudgetAdjustment extends Model
{

    public $table = 'erp_budgetadjustment';
    
    const CREATED_AT = 'createdDateTime';
    const UPDATED_AT = 'timestamp';

    protected $primaryKey = 'budgetAdjustmentID';

    public $fillable = [
        'companySystemID',
        'companyId',
        'companyFinanceYearID',
        'serviceLineSystemID',
        'budgetMasterID',
        'serviceLine',
        'adjustedGLCodeSystemID',
        'adjustedGLCode',
        'fromGLCodeSystemID',
        'fromGLCode',
        'toGLCodeSystemID',
        'toGLCode',
        'Year',
        'adjustmedLocalAmount',
        'adjustmentRptAmount',
        'createdUserSystemID',
        'createdByUserID',
        'modifiedUserSystemID',
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
        'budgetAdjustmentID' => 'integer',
        'companySystemID' => 'integer',
        'companyId' => 'string',
        'companyFinanceYearID' => 'integer',
        'budgetMasterID' => 'integer',
        'serviceLineSystemID' => 'integer',
        'serviceLine' => 'string',
        'adjustedGLCodeSystemID' => 'integer',
        'adjustedGLCode' => 'string',
        'fromGLCodeSystemID' => 'integer',
        'fromGLCode' => 'string',
        'toGLCodeSystemID' => 'integer',
        'toGLCode' => 'string',
        'Year' => 'integer',
        'adjustmedLocalAmount' => 'float',
        'adjustmentRptAmount' => 'float',
        'createdUserSystemID' => 'integer',
        'createdByUserID' => 'string',
        'modifiedUserSystemID' => 'integer',
        'modifiedByUserID' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function from_account()
    {
        return $this->belongsTo('App\Models\ChartOfAccount', 'fromGLCodeSystemID','chartOfAccountSystemID');
    }

     public function to_account()
    {
        return $this->belongsTo('App\Models\ChartOfAccount', 'toGLCodeSystemID','chartOfAccountSystemID');
    }
}
