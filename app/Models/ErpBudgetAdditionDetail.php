<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="ErpBudgetAdditionDetail",
 *      required={""},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
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
 *          property="fromTemplateDetailID",
 *          description="fromTemplateDetailID",
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
 *          property="gLCode",
 *          description="gLCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="gLCodeDescription",
 *          description="gLCodeDescription",
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
class ErpBudgetAdditionDetail extends Model
{

    public $table = 'erp_budgetadditiondetail';

    const CREATED_AT = 'createdDateTime';
    const UPDATED_AT = 'timestamp';

    public $guarded = [];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'budgetAdditionFormAutoID' => 'integer',
        'year' => 'integer',
        'fromTemplateDetailID' => 'integer',
        'serviceLineSystemID' => 'integer',
        'serviceLineCode' => 'string',
        'chartOfAccountSystemID' => 'integer',
        'gLCode' => 'string',
        'gLCodeDescription' => 'string',
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

    ];

    public function segment()
    {
        return $this->belongsTo('App\Models\SegmentMaster', 'serviceLineSystemID', 'serviceLineSystemID');
    }

    public function template()
    {
        return $this->belongsTo('App\Models\ReportTemplateDetails', 'templateDetailID', 'detID');
    }


    public function master()
    {
        return $this->belongsTo('App\Models\ErpBudgetAddition', 'id', 'budgetTransferFormAutoID');
    }
}
