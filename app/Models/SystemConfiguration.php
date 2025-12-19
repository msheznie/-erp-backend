<?php

namespace App\Models;

use Awobaz\Compoships\Database\Eloquent\Model;

class SystemConfiguration extends Model
{
    public $table = 'system_configuration';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $fillable = ['name', 'created_at', 'updated_at'];

    protected $casts = [
        'id' => 'integer',
        'name' => 'string'
    ];
}