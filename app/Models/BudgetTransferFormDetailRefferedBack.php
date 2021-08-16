<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="BudgetTransferFormDetailRefferedBack",
 *      required={""},
 *      @SWG\Property(
 *          property="budgetTransferFormDetailRefferedBackID",
 *          description="budgetTransferFormDetailRefferedBackID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="budgetTransferFormDetailAutoID",
 *          description="budgetTransferFormDetailAutoID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="budgetTransferFormAutoID",
 *          description="budgetTransferFormAutoID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="year",
 *          description="year",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="timesReferred",
 *          description="timesReferred",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isFromContingency",
 *          description="isFromContingency",
 *          type="boolean"
 *      ),
 *      @SWG\Property(
 *          property="contingencyBudgetID",
 *          description="Fk=> erp_budget_contingency.ID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="fromTemplateDetailID",
 *          description="fromTemplateDetailID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="fromServiceLineSystemID",
 *          description="fromServiceLineSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="fromServiceLineCode",
 *          description="fromServiceLineCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="fromChartOfAccountSystemID",
 *          description="fromChartOfAccountSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="FromGLCode",
 *          description="FromGLCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="FromGLCodeDescription",
 *          description="FromGLCodeDescription",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="toTemplateDetailID",
 *          description="toTemplateDetailID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="toServiceLineSystemID",
 *          description="toServiceLineSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="toServiceLineCode",
 *          description="toServiceLineCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="toChartOfAccountSystemID",
 *          description="toChartOfAccountSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="toGLCode",
 *          description="toGLCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="toGLCodeDescription",
 *          description="toGLCodeDescription",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="adjustmentAmountLocal",
 *          description="adjustmentAmountLocal",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="adjustmentAmountRpt",
 *          description="adjustmentAmountRpt",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="remarks",
 *          description="remarks",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="timestamp",
 *          description="timestamp",
 *          type="string",
 *          format="date-time"
 *      )
 * )
 */
class BudgetTransferFormDetailRefferedBack extends Model
{

    public $table = 'erp_budgettransferformdetail_referredback';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';




    public $fillable = [
        'budgetTransferFormDetailAutoID',
        'budgetTransferFormAutoID',
        'year',
        'timesReferred',
        'isFromContingency',
        'contingencyBudgetID',
        'fromTemplateDetailID',
        'fromServiceLineSystemID',
        'fromServiceLineCode',
        'fromChartOfAccountSystemID',
        'FromGLCode',
        'FromGLCodeDescription',
        'toTemplateDetailID',
        'toServiceLineSystemID',
        'toServiceLineCode',
        'toChartOfAccountSystemID',
        'toGLCode',
        'toGLCodeDescription',
        'adjustmentAmountLocal',
        'adjustmentAmountRpt',
        'remarks',
        'timestamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'budgetTransferFormDetailRefferedBackID' => 'integer',
        'budgetTransferFormDetailAutoID' => 'integer',
        'budgetTransferFormAutoID' => 'integer',
        'year' => 'integer',
        'timesReferred' => 'integer',
        'isFromContingency' => 'boolean',
        'contingencyBudgetID' => 'integer',
        'fromTemplateDetailID' => 'integer',
        'fromServiceLineSystemID' => 'integer',
        'fromServiceLineCode' => 'string',
        'fromChartOfAccountSystemID' => 'integer',
        'FromGLCode' => 'string',
        'FromGLCodeDescription' => 'string',
        'toTemplateDetailID' => 'integer',
        'toServiceLineSystemID' => 'integer',
        'toServiceLineCode' => 'string',
        'toChartOfAccountSystemID' => 'integer',
        'toGLCode' => 'string',
        'toGLCodeDescription' => 'string',
        'adjustmentAmountLocal' => 'float',
        'adjustmentAmountRpt' => 'float',
        'remarks' => 'string',
        'timestamp' => 'datetime'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'budgetTransferFormDetailAutoID' => 'required'
    ];

    public function from_segment()
    {
        return $this->belongsTo('App\Models\SegmentMaster', 'fromServiceLineSystemID', 'serviceLineSystemID');
    }

    public function to_segment()
    {
        return $this->belongsTo('App\Models\SegmentMaster', 'toServiceLineSystemID', 'serviceLineSystemID');
    }

    public function from_template()
    {
        return $this->belongsTo('App\Models\ReportTemplateDetails', 'fromTemplateDetailID', 'detID');
    }

    public function to_template()
    {
        return $this->belongsTo('App\Models\ReportTemplateDetails', 'toTemplateDetailID', 'detID');
    }

    public function master()
    {
        return $this->belongsTo('App\Models\BudgetTransferForm', 'budgetTransferFormAutoID', 'budgetTransferFormAutoID');
    }

    public function contingency()
    {
        return $this->belongsTo(ContingencyBudgetPlan::class, 'contingencyBudgetID', 'ID');
    }
}
