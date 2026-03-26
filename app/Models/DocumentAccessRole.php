<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentAccessRole extends Model
{
    protected $table = 'document_access_role';
    
    protected $primaryKey = 'id';
    
    public $fillable = [
        'document_attachment_id',
        'reportingManager_view',
        'reportingManager_create',
        'hod_view',
        'hod_create',
        'admin_view',
        'admin_create',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'document_attachment_id' => 'integer',
        'reportingManager_view' => 'integer',
        'reportingManager_create' => 'integer',
        'hod_view' => 'integer',
        'hod_create' => 'integer',
        'admin_view' => 'integer',
        'admin_create' => 'integer',
    ];

    /**
     * Get the company document attachment that owns this access role.
     */
    public function companyDocumentAttachment()
    {
        return $this->belongsTo('App\Models\CompanyDocumentAttachment', 'document_attachment_id', 'companyDocumentAttachmentID');
    }

    /**
     * Get the employees associated with this access role.
     */
    public function employees()
    {
        return $this->hasMany('App\Models\DocumentAccessEmployee', 'document_access_role_id', 'id');
    }

    public function owners()
    {
        return $this->hasMany(DocumentAccessRoleOwner::class, 'document_access_role_id', 'id');
    }
}
