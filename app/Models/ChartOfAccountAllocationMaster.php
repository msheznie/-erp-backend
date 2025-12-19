<?php
/**
 * =============================================
 * -- File Name : ChartOfAccountAllocationMaster.php
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
 *      definition="ChartOfAccountAllocationMaster",
 *      required={""},
 *      @SWG\Property(
 *          property="chartOfAccountAllocationMasterID",
 *          description="chartOfAccountAllocationMasterID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="companyID",
 *          description="companyID",
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
 *          property="serviceLineCode",
 *          description="serviceLineCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="serviceLineSystemID",
 *          description="serviceLineSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="chartOfAccountCode",
 *          description="chartOfAccountCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="chartOfAccountSystemID",
 *          description="chartOfAccountSystemID",
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
class ChartOfAccountAllocationMaster extends Model
{

    public $table = 'erp_chartofaccountallocationmaster';
    protected $primaryKey  = 'chartOfAccountAllocationMasterID';
    public $timestamps = false;

    public $fillable = [
        'companyID',
        'companySystemID',
        'allocationmaid',
        'serviceLineCode',
        'serviceLineSystemID',
        'chartOfAccountCode',
        'chartOfAccountSystemID',
        'timestamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'chartOfAccountAllocationMasterID' => 'integer',
        'companyID' => 'string',
        'companySystemID' => 'integer',
        'allocationmaid' => 'integer',
        'serviceLineCode' => 'string',
        'serviceLineSystemID' => 'integer',
        'chartOfAccountCode' => 'string',
        'chartOfAccountSystemID' => 'integer',
        'timestamp' => 'string'
    ];

    public static $rules = [

    ];

    public function detail() {
        return $this->hasMany('App\Models\ChartOfAccountAllocationDetail', 'chartOfAccountAllocationMasterID', 'chartOfAccountAllocationMasterID');
    }

    public function segment()
    {
        return $this->belongsTo('App\Models\SegmentMaster', 'serviceLineSystemID', 'serviceLineSystemID');
    }
}
