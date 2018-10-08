<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="AssetType",
 *      required={""},
 *      @SWG\Property(
 *          property="typeID",
 *          description="typeID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="typeDes",
 *          description="typeDes",
 *          type="string"
 *      )
 * )
 */
class AssetType extends Model
{

    public $table = 'erp_fa_assettype';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';



    public $fillable = [
        'typeDes',
        'timestamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'typeID' => 'integer',
        'typeDes' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
