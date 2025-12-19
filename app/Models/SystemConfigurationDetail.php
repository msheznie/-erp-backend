<?php

namespace App\Models;

use Awobaz\Compoships\Database\Eloquent\Model;

class SystemConfigurationDetail extends Model
{
    public $table = 'system_configuration_detail';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $fillable = ['attributeId', 'value', 'created_at', 'updated_at'];

    protected $casts = [
        'id' => 'integer',
        'attributeId' => 'integer',
        'value' => 'string'
    ];
}