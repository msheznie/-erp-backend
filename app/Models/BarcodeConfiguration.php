<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="BarcodeConfiguration",
 *      required={""},
 *      @SWG\Property(
 *          property="barcode_font",
 *          description="barcode_font",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="created_at",
 *          description="created_at",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="height",
 *          description="height",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="no_of_coulmns",
 *          description="no_of_coulmns",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="no_of_rows",
 *          description="no_of_rows",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="page_size",
 *          description="page_size",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="updated_at",
 *          description="updated_at",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="width",
 *          description="width",
 *          type="string"
 *      )
 * )
 */
class BarcodeConfiguration extends Model
{

    public $table = 'erp_barcode_configuration';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';




    public $fillable = [
        'barcode_font',
        'height',
        'no_of_coulmns',
        'no_of_rows',
        'page_size',
        'width',
        'companySystemID'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'barcode_font' => 'integer',
        'height' => 'string',
        'id' => 'integer',
        'no_of_coulmns' => 'string',
        'no_of_rows' => 'string',
        'page_size' => 'integer',
        'width' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'barcode_font' => 'required',
        'page_size' => 'required'
    ];

    public function company()
    {
        return $this->belongsTo('App\Models\Company', 'companySystemID', 'companySystemID');
    }
}
