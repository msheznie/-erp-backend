<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="FinanceCategorySerial",
 *      required={""},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="faFinanceCatID",
 *          description="faFinanceCatID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="lastSerialNo",
 *          description="lastSerialNo",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="companySystemID",
 *          description="companySystemID",
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class FinanceCategorySerial extends Model
{

    public $table = 'finance_category_serial';
    
    const CREATED_AT = null;
    const UPDATED_AT = null;




    public $fillable = [
        'faFinanceCatID',
        'lastSerialNo',
        'companySystemID'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'faFinanceCatID' => 'integer',
        'lastSerialNo' => 'integer',
        'companySystemID' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
