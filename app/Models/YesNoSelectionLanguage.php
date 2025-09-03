<?php

namespace App\Models;

use Eloquent as Model;

/**
 * Class YesNoSelectionLanguage
 * @package App\Models
 * @version March 5, 2018, 12:29 pm UTC
 *
 * @property integer yesNoSelectionID
 * @property string languageCode
 * @property string YesNo
 */
class YesNoSelectionLanguage extends Model
{
    public $table = 'yesnoselection_languages';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public $fillable = [
        'yesNoSelectionID',
        'languageCode',
        'YesNo'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'yesNoSelectionID' => 'integer',
        'languageCode' => 'string',
        'YesNo' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'yesNoSelectionID' => 'required|integer',
        'languageCode' => 'required|string|max:10',
        'YesNo' => 'required|string|max:255'
    ];

    /**
     * Relationship to YesNoSelection
     */
    public function yesNoSelection()
    {
        return $this->belongsTo(YesNoSelection::class, 'yesNoSelectionID', 'idyesNoselection');
    }
}

