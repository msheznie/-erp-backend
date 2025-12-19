<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="VatReturnFillingDetailsRefferedback",
 *      required={""},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="returnFillingDetailID",
 *          description="returnFillingDetailID",
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
 *      ),
 *      @SWG\Property(
 *          property="timestamp",
 *          description="timestamp",
 *          type="string",
 *          format="date-time"
 *      )
 * )
 */
class VatReturnFillingDetailsRefferedback extends Model
{

    public $table = 'vat_return_filling_details_refferedback';
    
    const CREATED_AT = 'timestamp';
    const UPDATED_AT = 'timestamp';




    public $fillable = [
        'returnFillingDetailID',
        'vatReturnFilledCategoryID',
        'vatReturnFillingID',
        'vatReturnFillingSubCatgeoryID',
        'taxAmount',
        'taxableAmount',
        'timestamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'returnFillingDetailID' => 'integer',
        'vatReturnFilledCategoryID' => 'integer',
        'vatReturnFillingID' => 'integer',
        'vatReturnFillingSubCatgeoryID' => 'integer',
        'taxAmount' => 'float',
        'taxableAmount' => 'float',
        'timestamp' => 'datetime'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
