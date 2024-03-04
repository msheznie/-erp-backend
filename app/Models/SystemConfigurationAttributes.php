<?php

namespace App\Models;

use Awobaz\Compoships\Database\Eloquent\Model;

class SystemConfigurationAttributes extends Model
{
    public $table = 'system_configuration_attributes';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $fillable = ['systemConfigurationId', 'name', 'slug', 'created_at', 'updated_at'];

    protected $casts = [
        'id' => 'integer',
        'systemConfigurationId' => 'integer',
        'name' => 'string',
        'slug' => 'string'
    ];

    public function systemConfigurationDetail()
    {
        return $this->hasOne('App\Models\SystemConfigurationDetail', 'attributeId', 'id');
    }
}