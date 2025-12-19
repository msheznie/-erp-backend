<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="WarehouseSubLevels",
 *      required={""},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="company_id",
 *          description="company_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="warehouse_id",
 *          description="warehouse_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="level",
 *          description="level",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="parent_id",
 *          description="parent_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="name",
 *          description="name",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="description",
 *          description="description",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="isFinalLevel",
 *          description="isFinalLevel",
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
 *          property="created_at",
 *          description="created_at",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="created_pc",
 *          description="created_pc",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="updated_by",
 *          description="updated_by",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="updated_at",
 *          description="updated_at",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="updated_pc",
 *          description="updated_pc",
 *          type="string"
 *      )
 * )
 */
class WarehouseSubLevels extends Model
{

    public $table = 'warehoussublevels';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';




    public $fillable = [
        'company_id',
        'warehouse_id',
        'level',
        'parent_id',
        'name',
        'description',
        'isFinalLevel',
        'created_by',
        'created_pc',
        'updated_by',
        'updated_pc',
        'isActive',
        'is_deleted',
        'deleted_by',
        'deleted_at'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'company_id' => 'integer',
        'warehouse_id' => 'integer',
        'level' => 'integer',
        'parent_id' => 'integer',
        'name' => 'string',
        'description' => 'string',
        'isFinalLevel' => 'integer',
        'created_by' => 'integer',
        'created_pc' => 'string',
        'updated_by' => 'integer',
        'updated_pc' => 'string',
        'isActive' => 'integer',
        'is_deleted' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function parent(){
        return $this->belogndTo(WarehouseSubLevels::class,'parent_id');
    }

    public function children(){
        return $this->hasMany(WarehouseSubLevels::class,'parent_id');
    }

    public function bin_locations(){
        return $this->hasMany(WarehouseBinLocation::class,'warehouseSubLevelId');
    }
}
