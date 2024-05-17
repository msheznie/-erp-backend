<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="TaxVatCategories",
 *      required={""},
 *      @SWG\Property(
 *          property="taxVatSubCategoriesAutoID",
 *          description="taxVatSubCategoriesAutoID",
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
 *          property="mainCategory",
 *          description="mainCategory",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="subCategory",
 *          description="subCategory",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="percentage",
 *          description="percentage",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="applicableOn",
 *          description="applicableOn",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="createdPCID",
 *          description="createdPCID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="createdUserID",
 *          description="createdUserID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="createdUserSystemID",
 *          description="createdUserSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="createdDateTime",
 *          description="createdDateTime",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="modifiedPCID",
 *          description="modifiedPCID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="modifiedUserID",
 *          description="modifiedUserID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="modifiedUserSystemID",
 *          description="modifiedUserSystemID",
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
class TaxVatCategories extends Model
{

    public $table = 'erp_tax_vat_sub_categories';
    
    const CREATED_AT = 'createdDateTime';
    const UPDATED_AT = 'timestamp';

    protected $primaryKey = 'taxVatSubCategoriesAutoID';


    public $fillable = [
        'taxMasterAutoID',
        'mainCategory',
        'subCategoryDescription',
        'percentage',
        'expenseGL',
        'recordType',
        'applicableOn',
        'isActive',
        'createdPCID',
        'createdUserID',
        'createdUserSystemID',
        'createdDateTime',
        'modifiedPCID',
        'modifiedUserID',
        'subCatgeoryType',
        'modifiedUserSystemID',
        'isDefault',
        'timestamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'taxVatSubCategoriesAutoID' => 'integer',
        'taxMasterAutoID' => 'integer',
        'mainCategory' => 'integer',
        'subCategoryDescription' => 'string',
        'percentage' => 'float',
        'applicableOn' => 'integer',
        'isActive' => 'integer',
        'createdPCID' => 'string',
        'createdUserID' => 'string',
        'createdUserSystemID' => 'integer',
        'createdDateTime' => 'datetime',
        'modifiedPCID' => 'string',
        'modifiedUserID' => 'string',
        'modifiedUserSystemID' => 'integer',
        'subCatgeoryType' => 'integer',
        'isDefault' => 'boolean',
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

    public function created_by()
    {
        return $this->belongsTo('App\Models\Employee', 'createdUserSystemID', 'employeeSystemID');
    }

    public function main()
    {
        return $this->belongsTo('App\Models\TaxVatMainCategories', 'mainCategory', 'taxVatMainCategoriesAutoID');
    }
 
    public function type()
    {
        return $this->belongsTo('App\Models\VatSubCategoryType', 'subCatgeoryType', 'id');
    }

    public function items()
    {
        return $this->belongsTo('App\Models\ItemMaster', 'taxVatSubCategoriesAutoID', 'vatSubCategory');
    }

    public static function getMainCategory($taxVatSubCategoriesAutoID)
    {

        $category = TaxVatCategories::find($taxVatSubCategoriesAutoID);

        return ($category) ? $category->mainCategory : null;

    }

}
