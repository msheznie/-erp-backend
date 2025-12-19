<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="VatReturnFilledCategory",
 *      required={""},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="categoryID",
 *          description="categoryID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="vatReturnFillingID",
 *          description="vatReturnFillingID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="created_at",
 *          description="created_at",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="updated_at",
 *          description="updated_at",
 *          type="string",
 *          format="date-time"
 *      )
 * )
 */
class VatReturnFilledCategory extends Model
{

    public $table = 'vat_returned_filled_catgeories';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';




    public $fillable = [
        'categoryID',
        'vatReturnFillingID'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'categoryID' => 'integer',
        'vatReturnFillingID' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function filled_details(){
        return $this->hasMany('App\Models\VatReturnFillingDetail', 'vatReturnFilledCategoryID','id');
    }

    public function category(){
        return $this->belongsTo('App\Models\VatReturnFillingCategory', 'categoryID','id');
    }
}
