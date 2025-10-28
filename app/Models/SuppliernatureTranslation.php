<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class SuppliernatureTranslation
 * @package App\Models
 * @version January 15, 2025, 12:00 pm UTC
 *
 * @property integer $supplierNatureID
 * @property string $languageCode
 * @property string $natureDescription
 */
class SuppliernatureTranslation extends Model
{
    public $table = 'suppliernature_translation';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public $fillable = [
        'supplierNatureID',
        'languageCode',
        'natureDescription'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'supplierNatureID' => 'integer',
        'languageCode' => 'string',
        'natureDescription' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'supplierNatureID' => 'required|integer',
        'languageCode' => 'required|string|max:10',
        'natureDescription' => 'required|string|max:255'
    ];

    /**
     * Get the suppliernature that owns the translation.
     */
    public function suppliernature()
    {
        return $this->belongsTo(Suppliernature::class, 'supplierNatureID', 'supplierNatureID');
    }
}
