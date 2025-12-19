<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="GrvDetailsPrn",
 *      required={""},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="grvDetailsID",
 *          description="grvDetailsID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="purhasereturnDetailID",
 *          description="purhasereturnDetailID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="prnQty",
 *          description="prnQty",
 *          type="number",
 *          format="number"
 *      )
 * )
 */
class GrvDetailsPrn extends Model
{

    public $table = 'erp_grvdetails_prn';
    
    const CREATED_AT = null;
    const UPDATED_AT = null;

    protected $primarykey = 'id';

    public $fillable = [
        'grvDetailsID',
        'purhasereturnDetailID',
        'prnQty'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'grvDetailsID' => 'integer',
        'purhasereturnDetailID' => 'integer',
        'prnQty' => 'float'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
