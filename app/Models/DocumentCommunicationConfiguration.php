<?php

namespace App\Models;

use Eloquent as Model;

class DocumentCommunicationConfiguration extends Model
{
    
    public $table = 'erp_communication_configuration_by_company';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $fillable = [
        'uuid',
        'companySystemID',
        'isActive',
        'communication_configuration_master_id',
    ];

    protected $casts = [
        'id' => 'integer',
        'uuid' => 'string',
        'companySystemID' => 'integer',
        'isActive' => 'integer',
        'communication_configuration_master_id' => 'array',
    ];
}

