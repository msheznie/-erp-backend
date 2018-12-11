<?php
/**
 * =============================================
 * -- File Name : AssetCapitalizatioDetReferred.php
 * -- Project Name : ERP
 * -- Module Name :  Asset Management
 * -- Author : Mubashir
 * -- Create date : 12 - December 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 * --
 */
namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="AssetCapitalizatioDetReferred",
 *      required={""},
 *      @SWG\Property(
 *          property="capitalizationDetailReferredID",
 *          description="capitalizationDetailReferredID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="capitalizationDetailID",
 *          description="capitalizationDetailID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="capitalizationID",
 *          description="capitalizationID",
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
 *          property="dateAQ",
 *          description="dateAQ",
 *          type="string",
 *          format="date"
 *      ),
 *      @SWG\Property(
 *          property="assetNBVLocal",
 *          description="assetNBVLocal",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="assetNBVRpt",
 *          description="assetNBVRpt",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="allocatedAmountLocal",
 *          description="allocatedAmountLocal",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="allocatedAmountRpt",
 *          description="allocatedAmountRpt",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="timesReferred",
 *          description="timesReferred",
 *          type="integer"
 *      ),
 *      @SWG\Property(
 *          property="createdUserGroup",
 *          description="createdUserGroup",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="createdUserSystemID",
 *          description="createdUserSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="createdUserID",
 *          description="createdUserID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="createdPcID",
 *          description="createdPcID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="modifiedUserSystemID",
 *          description="modifiedUserSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="modifiedUser",
 *          description="modifiedUser",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="modifiedPc",
 *          description="modifiedPc",
 *          type="string"
 *      )
 * )
 */
class AssetCapitalizatioDetReferred extends Model
{

    public $table = 'erp_fa_assetcapitalizationdetailreferredback';
    
    const CREATED_AT = 'createdDateTime';
    const UPDATED_AT = 'timestamp';

    protected $primaryKey = 'capitalizationDetailReferredID';

    public $fillable = [
        'capitalizationDetailID',
        'capitalizationID',
        'faID',
        'faCode',
        'assetDescription',
        'companySystemID',
        'companyID',
        'serviceLineSystemID',
        'serviceLineCode',
        'dateAQ',
        'assetNBVLocal',
        'assetNBVRpt',
        'allocatedAmountLocal',
        'allocatedAmountRpt',
        'timesReferred',
        'createdUserGroup',
        'createdUserSystemID',
        'createdUserID',
        'createdPcID',
        'modifiedUserSystemID',
        'modifiedUser',
        'modifiedPc',
        'createdDateTime',
        'timestamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'capitalizationDetailReferredID' => 'integer',
        'capitalizationDetailID' => 'integer',
        'capitalizationID' => 'integer',
        'faID' => 'integer',
        'faCode' => 'string',
        'assetDescription' => 'string',
        'companySystemID' => 'integer',
        'companyID' => 'string',
        'serviceLineSystemID' => 'integer',
        'serviceLineCode' => 'string',
        'dateAQ' => 'date',
        'assetNBVLocal' => 'float',
        'assetNBVRpt' => 'float',
        'allocatedAmountLocal' => 'float',
        'allocatedAmountRpt' => 'float',
        'timesReferred' => 'integer',
        'createdUserGroup' => 'string',
        'createdUserSystemID' => 'integer',
        'createdUserID' => 'string',
        'createdPcID' => 'string',
        'modifiedUserSystemID' => 'integer',
        'modifiedUser' => 'string',
        'modifiedPc' => 'string'
    ];

    /**
     * Scope a query to only include users of a given type.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param mixed $type
     * @return \Illuminate\Database\Eloquent\Builder
     */

    public function scopeOfCapitalization($query, $capitalizationID)
    {
        return $query->where('capitalizationID',  $capitalizationID);
    }

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [

    ];

    public function company()
    {
        return $this->belongsTo('App\Models\Company', 'companySystemID');
    }

    public function segment()
    {
        return $this->belongsTo('App\Models\SegmentMaster', 'serviceLineSystemID');
    }

    public function master()
    {
        return $this->belongsTo('App\Models\AssetCapitalization', 'capitalizationID');
    }

    
}
