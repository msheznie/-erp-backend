<?php
/**
 * =============================================
 * -- File Name : DepreciationPeriodsReferredHistory.php
 * -- Project Name : ERP
 * -- Module Name :  Asset Management
 * -- Author : Nazir
 * -- Create date : 7 - December 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 * --
 */
namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="DepreciationPeriodsReferredHistory",
 *      required={""},
 *      @SWG\Property(
 *          property="DepreciationPeriodsReferredID",
 *          description="DepreciationPeriodsReferredID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="DepreciationPeriodsID",
 *          description="DepreciationPeriodsID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="depMasterAutoID",
 *          description="depMasterAutoID",
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
 *          property="faFinanceCatID",
 *          description="faFinanceCatID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="faMainCategory",
 *          description="faMainCategory",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="faSubCategory",
 *          description="faSubCategory",
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
 *          property="assetDescription",
 *          description="assetDescription",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="depMonth",
 *          description="depMonth",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="depPercent",
 *          description="depPercent",
 *          type="number",
 *          format="float"
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
 *          property="FYID",
 *          description="FYID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="FYperiodID",
 *          description="FYperiodID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="depMonthYear",
 *          description="depMonthYear",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="depAmountLocalCurr",
 *          description="depAmountLocalCurr",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="depAmountLocal",
 *          description="depAmountLocal",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="depAmountRptCurr",
 *          description="depAmountRptCurr",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="depAmountRpt",
 *          description="depAmountRpt",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="depDoneYN",
 *          description="depDoneYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="timesReferred",
 *          description="timesReferred",
 *          type="boolean"
 *      ),
 *      @SWG\Property(
 *          property="createdUserSystemID",
 *          description="createdUserSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="createdBy",
 *          description="createdBy",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="createdPCid",
 *          description="createdPCid",
 *          type="string"
 *      )
 * )
 */
class DepreciationPeriodsReferredHistory extends Model
{

    public $table = 'erp_fa_assetdepreciationperiodsreferredhistory';

    const CREATED_AT = 'createdDateTime';
    const UPDATED_AT = 'timestamp';

    protected $primaryKey = 'DepreciationPeriodsReferredID';

    public $fillable = [
        'DepreciationPeriodsID',
        'depMasterAutoID',
        'companySystemID',
        'companyID',
        'serviceLineSystemID',
        'serviceLineCode',
        'faFinanceCatID',
        'faMainCategory',
        'faSubCategory',
        'faID',
        'faCode',
        'assetDescription',
        'depMonth',
        'depPercent',
        'COSTUNIT',
        'costUnitRpt',
        'FYID',
        'depForFYStartDate',
        'depForFYEndDate',
        'FYperiodID',
        'depForFYperiodStartDate',
        'depForFYperiodEndDate',
        'depMonthYear',
        'depAmountLocalCurr',
        'depAmountLocal',
        'depAmountRptCurr',
        'depAmountRpt',
        'depDoneYN',
        'timesReferred',
        'createdUserSystemID',
        'createdBy',
        'createdPCid',
        'createdDateTime',
        'timestamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'DepreciationPeriodsReferredID' => 'integer',
        'DepreciationPeriodsID' => 'integer',
        'depMasterAutoID' => 'integer',
        'companySystemID' => 'integer',
        'companyID' => 'string',
        'serviceLineSystemID' => 'integer',
        'serviceLineCode' => 'string',
        'faFinanceCatID' => 'integer',
        'faMainCategory' => 'integer',
        'faSubCategory' => 'integer',
        'faID' => 'integer',
        'faCode' => 'string',
        'assetDescription' => 'string',
        'depMonth' => 'integer',
        'depPercent' => 'float',
        'COSTUNIT' => 'float',
        'costUnitRpt' => 'float',
        'FYID' => 'integer',
        'FYperiodID' => 'integer',
        'depMonthYear' => 'string',
        'depAmountLocalCurr' => 'integer',
        'depAmountLocal' => 'float',
        'depAmountRptCurr' => 'integer',
        'depAmountRpt' => 'float',
        'depDoneYN' => 'integer',
        'timesReferred' => 'boolean',
        'createdUserSystemID' => 'integer',
        'createdBy' => 'string',
        'createdPCid' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    /**
     * Scope a query to only include users of a given type.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param mixed $type
     * @return \Illuminate\Database\Eloquent\Builder
     */

    public function scopeOfCompany($query, $type)
    {
        return $query->whereIN('companySystemID',  $type);
    }

    /**
     * Scope a query to only include users of a given type.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param mixed $type
     * @return \Illuminate\Database\Eloquent\Builder
     */

    public function scopeOfAsset($query, $faID)
    {
        return $query->where('faID',  $faID);
    }

    /**
     * Scope a query to only include users of a given type.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param mixed $type
     * @return \Illuminate\Database\Eloquent\Builder
     */

    public function scopeOfDepreciation($query, $depMasterAutoID)
    {
        return $query->where('depMasterAutoID',  $depMasterAutoID);
    }

    public function master_by(){
        return $this->belongsTo('App\Models\FixedAssetDepreciationMaster','depMasterAutoID','depMasterAutoID');
    }

    public function maincategory_by(){
        return $this->belongsTo('App\Models\FixedAssetCategory','faMainCategory','faCatID');
    }

    public function financecategory_by(){
        return $this->belongsTo('App\Models\AssetFinanceCategory','faFinanceCatID','faFinanceCatID');
    }

    public function serviceline_by(){
        return $this->belongsTo('App\Models\SegmentMaster','serviceLineSystemID','serviceLineSystemID');
    }

    
}
