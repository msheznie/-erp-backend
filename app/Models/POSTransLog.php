<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="POSTransLog",
 *      required={""},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="pos_mapping_id",
 *          description="pos_mapping_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="created_by",
 *          description="created_by",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="created_date",
 *          description="created_date",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="updated_by",
 *          description="updated_by",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="updated_date",
 *          description="updated_date",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="status",
 *          description="status",
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class POSTransLog extends Model
{

    public $table = 'pos_transaction_log';
    
    const CREATED_AT = 'created_date';
    const UPDATED_AT = 'updated_date';




    public $fillable = [
        'pos_mapping_id',
        'created_by',
        'created_date',
        'updated_by',
        'updated_date',
        'status',
        'transaction_log_id'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'pos_mapping_id' => 'integer',
        'created_by' => 'integer',
        'created_date' => 'datetime',
        'updated_by' => 'integer',
        'updated_date' => 'datetime',
        'status' => 'integer',
        'transaction_log_id' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'pos_mapping_id' => 'required'
    ];

    public function posMappingMaster(){ 
        return $this->hasOne('App\Models\POSMappingMaster', 'id', 'pos_mapping_id');
    }
    
}
