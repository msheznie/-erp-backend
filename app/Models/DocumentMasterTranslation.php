<?php

namespace App\Models;

use Eloquent as Model;

/**
 * Class DocumentMasterTranslation
 * @package App\Models
 * @version December 19, 2024
 *
 * @property integer $id
 * @property integer $documentSystemID
 * @property string $languageCode
 * @property string $description
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class DocumentMasterTranslation extends Model
{

    public $table = 'erp_documentmaster_translations';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public $fillable = [
        'documentSystemID',
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
        'documentSystemID' => 'integer',
        'languageCode' => 'string',
        'description' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'documentSystemID' => 'required|integer',
        'languageCode' => 'required|string|max:10',
        'description' => 'required|string|max:255'
    ];

    /**
     * Get the document master that owns the translation.
     */
    public function documentMaster()
    {
        return $this->belongsTo(DocumentMaster::class, 'documentSystemID', 'documentSystemID');
    }
}
