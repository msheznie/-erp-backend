<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="AddonCostCategoriesTranslation",
 *      required={""},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="idaddOnCostCategories",
 *          description="idaddOnCostCategories",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="languageCode",
 *          description="languageCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="costCatDes",
 *          description="costCatDes",
 *          type="string"
 *      )
 * )
 */
class AddonCostCategoriesTranslation extends Model
{
    public $table = 'erp_addoncostcategories_translation';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $primaryKey = 'id';

    public $fillable = [
        'idaddOnCostCategories',
        'languageCode',
        'costCatDes'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'idaddOnCostCategories' => 'integer',
        'languageCode' => 'string',
        'costCatDes' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    /**
     * Get the addon cost category that owns the translation.
     */
    public function addonCostCategory()
    {
        return $this->belongsTo('App\Models\AddonCostCategories', 'idaddOnCostCategories', 'idaddOnCostCategories');
    }
}
