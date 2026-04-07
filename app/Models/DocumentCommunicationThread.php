<?php

namespace App\Models;

use Eloquent as Model;

class DocumentCommunicationThread extends Model
{
    public $table = 'erp_document_communication_threads';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $fillable = [
        'uuid',
        'documentSystemID',
        'communication_configuration_by_company_id',
        'createdUserSystemID',
    ];

    protected $casts = [
        'id' => 'integer',
        'uuid' => 'string',
        'documentSystemID' => 'integer',
        'communication_configuration_by_company_id' => 'string',
        'createdUserSystemID' => 'integer',
    ];

    public function communicationConfigurationByCompany()
    {
        return $this->belongsTo(DocumentCommunicationConfiguration::class, 'communication_configuration_by_company_id', 'uuid');
    }

    public function messages()
    {
        return $this->hasMany(DocumentCommunicationMessage::class, 'thread_id', 'id');
    }
}

