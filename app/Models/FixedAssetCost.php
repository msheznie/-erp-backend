<?php

namespace App\Models;

use App\helper\Helper;
use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="FixedAssetCost",
 *      required={""},
 *      @SWG\Property(
 *          property="assetCostAutoID",
 *          description="assetCostAutoID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="originDocumentSystemCode",
 *          description="originDocumentSystemCode",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="originDocumentID",
 *          description="originDocumentID",
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
 *          property="assetID",
 *          description="assetID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="assetDescription",
 *          description="assetDescription",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="localCurrencyID",
 *          description="localCurrencyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="localAmount",
 *          description="localAmount",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="rptCurrencyID",
 *          description="rptCurrencyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="rptAmount",
 *          description="rptAmount",
 *          type="number",
 *          format="float"
 *      )
 * )
 */
class FixedAssetCost extends Model
{

    public $table = 'erp_fa_assetcost';
    
    const CREATED_AT = 'timeStamp';
    const UPDATED_AT = 'timeStamp';

    protected $primaryKey = 'assetCostAutoID';

    public $fillable = [
        'originDocumentSystemCode',
        'originDocumentID',
        'itemCode',
        'faID',
        'assetID',
        'assetDescription',
        'costDate',
        'localCurrencyID',
        'localAmount',
        'rptCurrencyID',
        'rptAmount',
        'timeStamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'assetCostAutoID' => 'integer',
        'originDocumentSystemCode' => 'integer',
        'originDocumentID' => 'string',
        'itemCode' => 'integer',
        'faID' => 'integer',
        'assetID' => 'string',
        'assetDescription' => 'string',
        'localCurrencyID' => 'integer',
        'localAmount' => 'float',
        'rptCurrencyID' => 'integer',
        'rptAmount' => 'float'
    ];

    /**
     * Scope a query to only include users of a given type.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param mixed $type
     * @return \Illuminate\Database\Eloquent\Builder
     */

    public function scopeOfFixedAsset($query, $faID)
    {
        return $query->where('faID',  $faID);
    }

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function localcurrency()
    {
        return $this->hasOne('App\Models\CurrencyMaster', 'currencyID', 'localCurrencyID');
    }

    public function rptcurrency()
    {
        return $this->hasOne('App\Models\CurrencyMaster',  'currencyID', 'rptCurrencyID');
    }

    public function setCostDateAttribute($value)
    {
        $this->attributes['costDate'] = Helper::dateAddTime($value);
    }
    
}
