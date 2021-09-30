<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="SrpErpFormCategory",
 *      required={""},
 *      @SWG\Property(
 *          property="FormCatID",
 *          description="FormCatID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="Category",
 *          description="Category",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="navigationMenuID",
 *          description="navigationMenuID",
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class SrpErpFormCategory extends Model
{

    public $table = 'srp_erp_formcategory';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';




    public $fillable = [
        'Category',
        'navigationMenuID'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'FormCatID' => 'integer',
        'Category' => 'string',
        'navigationMenuID' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'Category' => 'required',
        'navigationMenuID' => 'required'
    ];

    function template(){
        return $this->hasOne(SrpErpTemplateMaster::class, 'FormCatID', 'FormCatID');
    }
}
