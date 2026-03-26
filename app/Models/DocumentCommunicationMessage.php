<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DocumentCommunicationMessage extends Model
{
    public $table = 'erp_document_communication_messages';

    use SoftDeletes;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'uuid',
        'thread_id',
        'parent_message_id',
        'documentSystemCode',
        'authortype',
        'authorId',
        'body',
        'version',
        'edited_at',
        'deleted_by',
    ];

    protected $casts = [
        'id' => 'integer',
        'uuid' => 'string',
        'thread_id' => 'string',
        'parent_message_id' => 'string',
        'documentSystemCode' => 'integer',
        'authortype' => 'integer',
        'authorId' => 'integer',
        'body' => 'string',
        'version' => 'integer',
        'edited_at' => 'datetime',
        'deleted_by' => 'integer',
    ];

    public function thread()
    {
        // messages.thread_id references threads.uuid
        return $this->belongsTo(DocumentCommunicationThread::class, 'thread_id', 'uuid');
    }

    public function replies()
    {
        // replies.parent_message_id references messages.uuid
        return $this->hasMany(DocumentCommunicationMessage::class, 'parent_message_id', 'uuid');
    }

    public function attachments()
    {
        return $this->hasMany(DocumentCommunicationAttachment::class, 'message_id', 'id');
    }

    public function author()
    {
        // authorId in this table stores employeeSystemID for author type = 1.
        return $this->belongsTo(Employee::class, 'authorId', 'employeeSystemID');
    }
}

