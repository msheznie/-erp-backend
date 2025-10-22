<?php

namespace App\Models;

use Eloquent as Model;

/**
 * Class MonthsLanguage
 * @package App\Models
 * @version March 5, 2018, 12:29 pm UTC
 *
 * @property integer monthID
 * @property string languageCode
 * @property string monthDes
 */
class MonthsLanguage extends Model
{
    public $table = 'months_languages';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public $fillable = [
        'monthID',
        'languageCode',
        'monthDes'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'monthID' => 'integer',
        'languageCode' => 'string',
        'monthDes' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'monthID' => 'required|integer',
        'languageCode' => 'required|string|max:10',
        'monthDes' => 'required|string|max:255'
    ];

    /**
     * Relationship to Months
     */
    public function months()
    {
        return $this->belongsTo(Months::class, 'monthID', 'monthID');
    }
}