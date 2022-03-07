<?php

namespace App\Models;

use App\Traits\ActiveTrait;
use App\Traits\ApproveTrait;
use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="FixedAssetCategorySub",
 *      required={""},
 *      @SWG\Property(
 *          property="faCatSubID",
 *          description="faCatSubID",
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
 *          property="catDescription",
 *          description="catDescription",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="faCatID",
 *          description="faCatID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="mainCatDescription",
 *          description="mainCatDescription",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="suCatLevel",
 *          description="suCatLevel",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isActive",
 *          description="isActive",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="createdPcID",
 *          description="createdPcID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="createdUserGroup",
 *          description="createdUserGroup",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="createdUserID",
 *          description="createdUserID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="modifiedPc",
 *          description="modifiedPc",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="modifiedUser",
 *          description="modifiedUser",
 *          type="string"
 *      )
 * )
 */
class FixedAssetCategorySub extends Model
{

    use ActiveTrait;

    public $table = 'erp_fa_categorysub';
    
    const CREATED_AT = 'createdDateTime';
    const UPDATED_AT = 'timestamp';
    protected $primaryKey = 'faCatSubID';


    public $fillable = [
        'companySystemID',
        'companyID',
        'suCatCode',
        'catDescription',
        'faCatID',
        'mainCatDescription',
        'suCatLevel',
        'isActive',
        'createdPcID',
        'createdUserGroup',
        'createdUserID',
        'createdDateTime',
        'modifiedPc',
        'modifiedUser',
        'timestamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'faCatSubID' => 'integer',
        'companySystemID' => 'integer',
        'companyID' => 'string',
        'suCatCode' => 'string',
        'catDescription' => 'string',
        'faCatID' => 'integer',
        'mainCatDescription' => 'string',
        'suCatLevel' => 'integer',
        'isActive' => 'integer',
        'createdPcID' => 'string',
        'createdUserGroup' => 'string',
        'createdUserID' => 'string',
        'modifiedPc' => 'string',
        'modifiedUser' => 'string'
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

    public function scopeByFaCatID($query, $type)
    {
        return $query->where('faCatID',  $type);
    }

    public function scopeByFaCatIDMultiSelect($query, $type)
    {
        return $query->whereIn('faCatID',  $type);
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
        return $this->belongsTo('App\Models\Company', 'companySystemID', 'companySystemID');
    }
}
