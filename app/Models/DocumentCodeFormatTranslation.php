<?php

namespace App\Models;

use Eloquent as Model;

/**
 * Class DocumentCodeFormatTranslation
 * @package App\Models
 * @version October 14, 2025, 11:33 am UTC
 *
 * @property integer document_code_format_id
 * @property string languageCode
 * @property string description
 */
class DocumentCodeFormatTranslation extends Model
{
    public $table = 'document_code_format_translation';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public $fillable = [
        'document_code_format_id',
        'languageCode',
        'description'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'document_code_format_id' => 'integer',
        'languageCode' => 'string',
        'description' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'document_code_format_id' => 'required|integer',
        'languageCode' => 'required|string|max:10',
        'description' => 'required|string|max:255'
    ];

    /**
     * Get the document code format that owns the translation.
     */
    public function documentCodeFormat()
    {
        return $this->belongsTo(DocumentCodeFormat::class, 'document_code_format_id', 'id');
    }
}
