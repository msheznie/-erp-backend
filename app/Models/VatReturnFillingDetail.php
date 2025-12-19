<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="VatReturnFillingDetail",
 *      required={""},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="vatReturnFilledCategoryID",
 *          description="vatReturnFilledCategoryID",
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
 *          property="vatReturnFillingSubCatgeoryID",
 *          description="vatReturnFillingSubCatgeoryID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="taxAmount",
 *          description="taxAmount",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="taxableAmount",
 *          description="taxableAmount",
 *          type="number",
 *          format="number"
 *      )
 * )
 */
class VatReturnFillingDetail extends Model
{

    public $table = 'vat_return_filling_details';
    
    const CREATED_AT = 'timestamp';
    const UPDATED_AT = 'timestamp';




    public $fillable = [
        'vatReturnFilledCategoryID',
        'vatReturnFillingID',
        'vatReturnFillingSubCatgeoryID',
        'taxAmount',
        'taxableAmount'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'vatReturnFilledCategoryID' => 'integer',
        'vatReturnFillingID' => 'integer',
        'vatReturnFillingSubCatgeoryID' => 'integer',
        'taxAmount' => 'float',
        'taxableAmount' => 'float'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

     public function category(){
        return $this->belongsTo('App\Models\VatReturnFillingCategory', 'vatReturnFillingSubCatgeoryID','id');
    }
}
