<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="POSMappingDetail",
 *      required={""},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="master_id",
 *          description="master_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="table",
 *          description="table",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="key",
 *          description="key",
 *          type="string"
 *      )
 * )
 */
class POSMappingDetail extends Model
{

    public $table = 'pos_mapping_detail';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';




    public $fillable = [
        'master_id',
        'table',
        'key'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'master_id' => 'integer',
        'table' => 'string',
        'key' => 'string',
        'model_name' => 'string' ,
        'source_table_name' => 'string',
        'source_model_name' => 'string' 
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
