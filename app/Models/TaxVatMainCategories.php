<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="TaxVatMainCategories",
 *      required={""},
 *      @SWG\Property(
 *          property="taxVatMainCategoriesAutoID",
 *          description="taxVatMainCategoriesAutoID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="taxMasterAutoID",
 *          description="taxMasterAutoID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="mainCategoryDescription",
 *          description="mainCategoryDescription",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="timestamp",
 *          description="timestamp",
 *          type="string",
 *          format="date-time"
 *      )
 * )
 */
class TaxVatMainCategories extends Model
{

    public $table = 'erp_tax_vat_main_categories';
    
    const CREATED_AT = 'timestamp';
    const UPDATED_AT = 'timestamp';

    protected $primaryKey = 'taxVatMainCategoriesAutoID';


    public $fillable = [
        'taxMasterAutoID',
        'mainCategoryDescription',
        'isActive',
        'timestamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'taxVatMainCategoriesAutoID' => 'integer',
        'taxMasterAutoID' => 'integer',
        'mainCategoryDescription' => 'string',
        'isActive' => 'integer',
        'timestamp' => 'datetime'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function tax()
    {
        return $this->belongsTo('App\Models\Tax', 'taxMasterAutoID', 'taxMasterAutoID');
    }

    public function sub_categories()
    {
        return $this->hasMany('App\Models\TaxVatCategories', 'mainCategory', 'taxVatMainCategoriesAutoID');
    }
}
