<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="DocumentSubProduct",
 *      required={""},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="documentSystemID",
 *          description="documentSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="documentSystemCode",
 *          description="documentSystemCode",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="documentDetailID",
 *          description="documentDetailID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="productSerialID",
 *          description="productSerialID",
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
 *          property="quantity",
 *          description="quantity",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="sold",
 *          description="sold",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="soldQty",
 *          description="soldQty",
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
class DocumentSubProduct extends Model
{

    public $table = 'document_sub_products';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';




    public $fillable = [
        'documentSystemID',
        'documentSystemCode',
        'documentDetailID',
        'productSerialID',
        'productBatchID',
        'quantity',
        'sold',
        'soldQty'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'documentSystemID' => 'integer',
        'documentSystemCode' => 'integer',
        'documentDetailID' => 'integer',
        'productSerialID' => 'integer',
        'productBatchID' => 'integer',
        'quantity' => 'float',
        'sold' => 'integer',
        'soldQty' => 'float'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];


    public function serial_data()
    {
        return $this->belongsTo('App\Models\ItemSerial', 'productSerialID', 'id');
    }
    
}
