<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="ItemSerial",
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
 *          property="productBatchID",
 *          description="productBatchID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="serialCode",
 *          description="serialCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="expireDate",
 *          description="expireDate",
 *          type="string",
 *          format="date"
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
class ItemSerial extends Model
{

    public $table = 'item_serial';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';




    public $fillable = [
        'itemSystemCode',
        'productBatchID',
        'serialCode',
        'expireDate',
        'wareHouseSystemID',
        'binLocation',
        'soldFlag'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'itemSystemCode' => 'integer',
        'productBatchID' => 'integer',
        'serialCode' => 'string',
        'expireDate' => 'date',
        'wareHouseSystemID' => 'integer',
        'binLocation' => 'integer',
        'soldFlag' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
