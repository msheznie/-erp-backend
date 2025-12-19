<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @SWG\Definition(
 *      definition="ErpAttributes",
 *      required={""},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="description",
 *          description="description",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="field_type",
 *          description="Text = 1 , Numeric= 2, Dropdown = 3, ",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="document_id",
 *          description="document_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="document_master_id",
 *          description="finance_sub_category_ID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="is_mendatory",
 *          description="is_mendatory",
 *          type="boolean"
 *      ),
 *      @SWG\Property(
 *          property="created_by",
 *          description="created_by",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="updated_by",
 *          description="updated_by",
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
class ErpAttributes extends Model
{
    use SoftDeletes;

    public $table = 'erp_attributes';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';




    public $fillable = [
        'description',
        'field_type_id',
        'document_id',
        'document_master_id',
        'is_mendatory',
        'is_active',
        'value',
        'color',
        'inactivated_at',
        'created_by',
        'updated_by'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'description' => 'string',
        'field_type_id' => 'integer',
        'document_id' => 'string',
        'value' => 'string',
        'document_master_id' => 'integer',
        'is_mendatory' => 'boolean',
        'is_active' => 'boolean',
        'created_by' => 'integer',
        'updated_by' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function field_type()
    {
        return $this->belongsTo('App\Models\ErpAttributesFieldType', 'field_type_id', 'id');
    }

    public function fieldOptions()
    {
        return $this->hasMany('App\Models\ErpAttributesDropdown', 'attributes_id');
    }

    public function attributeValues(){
        return $this->hasMany('App\Models\ErpAttributeValues', 'attribute_id');
    }

}
