<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="SupplierCategoryICVSubTranslation",
 *      required={""},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="supCategorySubICVID",
 *          description="supCategorySubICVID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="languageCode",
 *          description="languageCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="categoryDescription",
 *          description="categoryDescription",
 *          type="string"
 *      )
 * )
 */
class SupplierCategoryICVSubTranslation extends Model
{
    public $table = 'suppliercategoryicvsub_translation';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $primaryKey = 'id';

    public $fillable = [
        'supCategorySubICVID',
        'languageCode',
        'categoryDescription'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'supCategorySubICVID' => 'integer',
        'languageCode' => 'string',
        'categoryDescription' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    /**
     * Get the supplier category ICV sub that owns the translation.
     */
    public function supplierCategoryICVSub()
    {
        return $this->belongsTo('App\Models\SupplierCategoryICVSub', 'supCategorySubICVID', 'supCategorySubICVID');
    }
}


