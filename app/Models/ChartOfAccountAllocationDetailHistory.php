<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="ChartOfAccountAllocationDetailHistory",
 *      required={""},
 *      @SWG\Property(
 *          property="jvMasterAutoId",
 *          description="jvMasterAutoId",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="timestamp",
 *          description="timestamp",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="percentage",
 *          description="percentage",
 *          type="float",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="productLineID",
 *          description="productLineID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="productLineCode",
 *          description="productLineCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="allocationmaid",
 *          description="allocationmaid",
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
 *          property="companyid",
 *          description="companyid",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="chartOfAccountAllocationMasterID",
 *          description="chartOfAccountAllocationMasterID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="chartOfAccountAllocationDetailHistoryID",
 *          description="chartOfAccountAllocationDetailHistoryID",
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class ChartOfAccountAllocationDetailHistory extends Model
{

    public $table = 'erp_chartofaccountallocationdetailhistory';
    protected $primaryKey  = 'chartOfAccountAllocationDetailHistoryID';
    public $timestamps = false;


    public $fillable = [
        'jvMasterAutoId',
        'timestamp',
        'percentage',
        'productLineID',
        'productLineCode',
        'allocationmaid',
        'companySystemID',
        'companyid',
        'chartOfAccountAllocationMasterID'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'jvMasterAutoId' => 'integer',
        'timestamp' => 'varchar',
        'percentage' => 'float',
        'productLineID' => 'integer',
        'productLineCode' => 'string',
        'allocationmaid' => 'integer',
        'companySystemID' => 'integer',
        'companyid' => 'string',
        'chartOfAccountAllocationMasterID' => 'integer',
        'chartOfAccountAllocationDetailHistoryID' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
    ];

    
}
