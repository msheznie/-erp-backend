<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="POSItemGLEntries",
 *      required={""},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="shiftId",
 *          description="shiftId",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="itemAutoId",
 *          description="itemAutoId",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="uom",
 *          description="uom",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="qty",
 *          description="qty",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isReturnYN",
 *          description="isReturnYN",
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class POSItemGLEntries extends Model
{

    public $table = 'pos_item_gl';
    public $timestamps = false;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';




    public $fillable = [
        'shiftId',
        'invoiceID',
        'itemAutoId',
        'uom',
        'qty',
        'isReturnYN',
        'wareHouseId'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'shiftId' => 'integer',
        'itemAutoId' => 'integer',
        'uom' => 'integer',
        'qty' => 'double',
        'isReturnYN' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function warehouse(){
        return $this->belongsTo('App\Models\WarehouseMaster', 'wareHouseId', 'wareHouseSystemCode');
    }

}
