<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DocumentCommunicationAttachment extends Model
{
    public $table = 'erp_document_communication_attachments';

    use SoftDeletes;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'uuid',
        'message_id',
        'attachmentDescription',
        'originalFileName',
        'fileStoragePath',
        'fileSize',
        'createdBy',
    ];

    protected $casts = [
        'id' => 'integer',
        'uuid' => 'string',
        'message_id' => 'string',
        'attachmentDescription' => 'string',
        'originalFileName' => 'string',
        'fileStoragePath' => 'string',
        'fileSize' => 'float',
        'createdBy' => 'integer',
    ];

    public function message()
    {
        return $this->belongsTo(DocumentCommunicationMessage::class, 'message_id', 'id');
    }
}

