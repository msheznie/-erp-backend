<?php

namespace App\Models;

use Eloquent as Model;

class ErpAttributeValues extends Model
{
    public $table = 'erp_attribute_values';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $primaryKey = 'id';


    public $fillable = [
        'attribute_id',
        'document_master_id',
        'is_active',
        'value',
        'color',
        'is_mendatory'
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

    public function dropdownValues()
    {
        return $this->belongsTo('App\Models\ErpAttributesDropdown', 'attribute_id', 'attributes_id');
    }
}
