<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentAccessRoleOwner extends Model
{
    protected $table = 'document_access_role_owners';

    protected $primaryKey = 'id';

    public $fillable = [
        'document_access_role_id',
        'owner_key',
        'can_view',
        'can_edit',
    ];

    protected $casts = [
        'id' => 'integer',
        'document_access_role_id' => 'integer',
        'owner_key' => 'string',
        'can_view' => 'integer',
        'can_edit' => 'integer',
    ];

    public function documentAccessRole()
    {
        return $this->belongsTo(DocumentAccessRole::class, 'document_access_role_id', 'id');
    }
}

