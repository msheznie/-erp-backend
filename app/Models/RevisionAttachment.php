<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class RevisionAttachment
 * @package App\Models
 * @version January 15, 2025
 *
 * @property integer revisionId
 * @property string fileName
 * @property string filePath
 * @property string fileType
 * @property integer fileSize
 * @property string fileContent
 * @property integer created_by
 * @property integer modified_by
 * @property string created_at
 * @property string modified_at
 * @property string deleted_at
 */
class RevisionAttachment extends Model
{

    public $table = 'revision_attachments';

    protected $primaryKey = 'id';

    protected $dates = ['deleted_at'];

    public $fillable = [
        'revisionId',
        'fileName',
        'filePath',
        'fileType',
        'fileSize',
        'fileContent',
        'created_by',
        'modified_by',
        'created_at',
        'modified_at'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'revisionId' => 'integer',
        'fileName' => 'string',
        'filePath' => 'string',
        'fileType' => 'string',
        'fileSize' => 'integer',
        'fileContent' => 'string',
        'created_by' => 'integer',
        'modified_by' => 'integer',
        'created_at' => 'datetime',
        'modified_at' => 'datetime'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'revisionId' => 'required|integer|exists:revisions,id',
        'fileName' => 'required|string|max:255',
        'filePath' => 'string|max:500',
        'fileType' => 'string|max:100',
        'fileSize' => 'required|integer|min:1',
        'fileContent' => 'string'
    ];

    /**
     * Get the revision that owns the attachment.
     */
    public function revision()
    {
        return $this->belongsTo('App\Models\Revision', 'revisionId', 'id');
    }

    /**
     * Get the employee who created the attachment.
     */
    public function createdBy()
    {
        return $this->belongsTo('App\Models\Employee', 'created_by', 'employeeSystemID');
    }

    /**
     * Get the employee who last modified the attachment.
     */
    public function modifiedBy()
    {
        return $this->belongsTo('App\Models\Employee', 'modified_by', 'employeeSystemID');
    }

    /**
     * Get file size in human readable format
     */
    public function getFileSizeHumanAttribute()
    {
        $bytes = $this->fileSize;
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Get file extension
     */
    public function getFileExtensionAttribute()
    {
        return pathinfo($this->fileName, PATHINFO_EXTENSION);
    }

    /**
     * Check if file is an image
     */
    public function getIsImageAttribute()
    {
        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];
        return in_array(strtolower($this->file_extension), $imageExtensions);
    }

    /**
     * Check if file is a document
     */
    public function getIsDocumentAttribute()
    {
        $documentExtensions = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt'];
        return in_array(strtolower($this->file_extension), $documentExtensions);
    }
}
