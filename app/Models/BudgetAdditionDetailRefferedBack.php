<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="BudgetAdditionDetailRefferedBack",
 *      required={""},
 *      @SWG\Property(
 *          property="budgetAdditionDetailReferredbackID",
 *          description="budgetAdditionDetailReferredbackID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="budgetAdditionFormAutoID",
 *          description="budgetAdditionFormAutoID",
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
 *          property="templateDetailID",
 *          description="templateDetailID",
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
 *          property="budjetDetailsID",
 *          description="budjetDetailsID",
 *          type="integer",
 *          format="int32"
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
 *          property="timesReferred",
 *          description="timesReferred",
 *          type="integer",
 *          format="int32"
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
 *      ),
 *      @SWG\Property(
 *          property="createdDateTime",
 *          description="createdDateTime",
 *          type="string",
 *          format="date-time"
 *      )
 * )
 */
class BudgetAdditionDetailRefferedBack extends Model
{

    public $table = 'erp_budgetadditiondetail_referredback';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';




    public $fillable = [
        'id',
        'budgetAdditionFormAutoID',
        'year',
        'templateDetailID',
        'serviceLineSystemID',
        'serviceLineCode',
        'budjetDetailsID',
        'chartOfAccountSystemID',
        'gLCode',
        'gLCodeDescription',
        'adjustmentAmountLocal',
        'adjustmentAmountRpt',
        'timesReferred',
        'remarks',
        'timestamp',
        'createdDateTime'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'budgetAdditionDetailReferredbackID' => 'integer',
        'id' => 'integer',
        'budgetAdditionFormAutoID' => 'integer',
        'year' => 'integer',
        'templateDetailID' => 'integer',
        'serviceLineSystemID' => 'integer',
        'serviceLineCode' => 'string',
        'budjetDetailsID' => 'integer',
        'chartOfAccountSystemID' => 'integer',
        'gLCode' => 'string',
        'gLCodeDescription' => 'string',
        'adjustmentAmountLocal' => 'float',
        'adjustmentAmountRpt' => 'float',
        'timesReferred' => 'integer',
        'remarks' => 'string',
        'timestamp' => 'datetime',
        'createdDateTime' => 'datetime'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'id' => 'required'
    ];

    public function segment()
    {
        return $this->belongsTo('App\Models\SegmentMaster', 'serviceLineSystemID', 'serviceLineSystemID');
    }

    public function template()
    {
        return $this->belongsTo('App\Models\ReportTemplateDetails', 'templateDetailID', 'detID');
    }
}
