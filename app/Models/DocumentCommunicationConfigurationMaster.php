<?php

namespace App\Models;

use Eloquent as Model;

class DocumentCommunicationConfigurationMaster extends Model
{
    public $table = 'erp_communication_configuration_master';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';


    protected $fillable = [
        'feature_key',
    ];

    protected $casts = [
        'id' => 'integer',
        'feature_key' => 'string',
    ];
}
