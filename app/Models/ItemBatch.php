<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="ItemBatch",
 *      required={""},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="itemSystemCode",
 *          description="itemSystemCode",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="batchCode",
 *          description="batchCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="expireDate",
 *          description="expireDate",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="wareHouseSystemID",
 *          description="wareHouseSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="binLocation",
 *          description="binLocation",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="soldFlag",
 *          description="soldFlag",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="quantity",
 *          description="quantity",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="copiedQty",
 *          description="copiedQty",
 *          type="number",
 *          format="number"
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
class ItemBatch extends Model
{

    public $table = 'item_batch';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';




    public $fillable = [
        'itemSystemCode',
        'batchCode',
        'expireDate',
        'wareHouseSystemID',
        'binLocation',
        'soldFlag',
        'quantity',
        'copiedQty'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'itemSystemCode' => 'integer',
        'batchCode' => 'string',
        'expireDate' => 'datetime',
        'wareHouseSystemID' => 'integer',
        'binLocation' => 'integer',
        'soldFlag' => 'integer',
        'quantity' => 'float',
        'copiedQty' => 'float'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
