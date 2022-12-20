<?php

namespace App\Models;

use Eloquent as Model;


class TenantConfiguration extends Model
{
    public $table = 'tenant_configuration';


    public $fillable = [
        'configuration_id',
        'tenant_id',
        'value'
    ];
}
