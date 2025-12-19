<?php
/**
 * =============================================
 * -- File Name : ChartOfAccountAllocationDetail.php
 * -- Project Name : ERP
 * -- Module Name : Chart Of Account
 * -- Author : Mohamed Rilwan
 * -- Create date : 08- Nov 2019
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 */
namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="ChartOfAccountAllocationDetail",
 *      required={""},
 *      @SWG\Property(
 *          property="chartOfAccountAllocationDetailID",
 *          description="chartOfAccountAllocationDetailID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="chartOfAccountAllocationMasterID",
 *          description="chartOfAccountAllocationMasterID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="companyid",
 *          description="companyid",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="companySystemID",
 *          description="companySystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="allocationmaid",
 *          description="allocationmaid",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="productLineCode",
 *          description="productLineCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="productLineID",
 *          description="productLineID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="percentage",
 *          description="percentage",
 *          type="float",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="timestamp",
 *          description="timestamp",
 *          type="string"
 *      )
 * )
 */
class ChartOfAccountAllocationDetail extends Model
{

    public $table = 'erp_chartofaccountallocationdetail';
    protected $primaryKey  = 'chartOfAccountAllocationDetailID';
    public $timestamps = false;

    public $fillable = [
        'chartOfAccountAllocationMasterID',
        'companyid',
        'companySystemID',
        'allocationmaid',
        'productLineCode',
        'productLineID',
        'percentage',
        'timestamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'chartOfAccountAllocationDetailID' => 'integer',
        'chartOfAccountAllocationMasterID' => 'integer',
        'companyid' => 'string',
        'companySystemID' => 'integer',
        'allocationmaid' => 'integer',
        'productLineCode' => 'string',
        'productLineID' => 'integer',
        'percentage' => 'float',
        'timestamp' => 'string'
    ];

    public static $rules = [

    ];

    public function segment()
    {
        return $this->belongsTo('App\Models\SegmentMaster', 'productLineID', 'serviceLineSystemID');
    }
}
