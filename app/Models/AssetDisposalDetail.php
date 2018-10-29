<?php
/**
 * =============================================
 * -- File Name : AssetDisposalDetail.php
 * -- Project Name : ERP
 * -- Module Name :  Asset Management
 * -- Author : Mubashir
 * -- Create date : 27 - September 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 * --
 */

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="AssetDisposalDetail",
 *      required={""},
 *      @SWG\Property(
 *          property="assetDisposalDetailAutoID",
 *          description="assetDisposalDetailAutoID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="assetdisposalMasterAutoID",
 *          description="assetdisposalMasterAutoID",
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
 *          property="itemCode",
 *          description="itemCode",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="faID",
 *          description="faID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="faCode",
 *          description="faCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="faUnitSerialNo",
 *          description="faUnitSerialNo",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="assetDescription",
 *          description="assetDescription",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="COSTUNIT",
 *          description="COSTUNIT",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="costUnitRpt",
 *          description="costUnitRpt",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="netBookValueLocal",
 *          description="netBookValueLocal",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="depAmountLocal",
 *          description="depAmountLocal",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="depAmountRpt",
 *          description="depAmountRpt",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="netBookValueRpt",
 *          description="netBookValueRpt",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="COSTGLCODE",
 *          description="COSTGLCODE",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="ACCDEPGLCODE",
 *          description="ACCDEPGLCODE",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="DISPOGLCODE",
 *          description="DISPOGLCODE",
 *          type="string"
 *      )
 * )
 */
class AssetDisposalDetail extends Model
{

    public $table = 'erp_fa_asset_disposaldetail';

    const CREATED_AT = 'timestamp';
    const UPDATED_AT = 'timestamp';

    protected $primaryKey = 'assetDisposalDetailAutoID';

    public $fillable = [
        'assetdisposalMasterAutoID',
        'companySystemID',
        'companyID',
        'serviceLineSystemID',
        'serviceLineCode',
        'itemCode',
        'faID',
        'faCode',
        'faUnitSerialNo',
        'assetDescription',
        'COSTUNIT',
        'costUnitRpt',
        'netBookValueLocal',
        'depAmountLocal',
        'depAmountRpt',
        'netBookValueRpt',
        'COSTGLCODE',
        'ACCDEPGLCODE',
        'DISPOGLCODE',
        'timestamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'assetDisposalDetailAutoID' => 'integer',
        'assetdisposalMasterAutoID' => 'integer',
        'companySystemID' => 'integer',
        'companyID' => 'string',
        'serviceLineSystemID' => 'integer',
        'serviceLineCode' => 'string',
        'itemCode' => 'integer',
        'faID' => 'integer',
        'faCode' => 'string',
        'faUnitSerialNo' => 'string',
        'assetDescription' => 'string',
        'COSTUNIT' => 'float',
        'costUnitRpt' => 'float',
        'netBookValueLocal' => 'float',
        'depAmountLocal' => 'float',
        'depAmountRpt' => 'float',
        'netBookValueRpt' => 'float',
        'COSTGLCODE' => 'string',
        'ACCDEPGLCODE' => 'string',
        'DISPOGLCODE' => 'string'
    ];

    /**
     * Scope a query to only include users of a given type.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param mixed $type
     * @return \Illuminate\Database\Eloquent\Builder
     */

    public function scopeOfMaster($query, $id)
    {
        return $query->where('assetdisposalMasterAutoID', $id);
    }

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [

    ];

    public function segment_by()
    {
        return $this->belongsTo('App\Models\SegmentMaster', 'serviceLineSystemID', 'serviceLineSystemID');
    }

    public function asset_by()
    {
        return $this->belongsTo('App\Models\FixedAssetMaster', 'faID', 'faID');
    }

    public function item_by()
    {
        return $this->belongsTo('App\Models\ItemMaster', 'itemCode', 'itemCodeSystem');
    }


}
