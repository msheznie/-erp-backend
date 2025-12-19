<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExchangeSetupDocumentType extends Model
{

    protected $appends = ['name'];

    public $fillable = [
        'exchangeSetupDocumentId',
        'name',
        'slug',
        'sort',
        'isActive',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'exchangeSetupDocumentId' => 'integer',
        'sort' => 'integer',
        'name' => 'string',
        'slug' => 'string',
        'isActive' => 'boolean'
    ];

    protected $dates = [
        'created_at',
        'updated_at'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [

    ];

    public function document()
    {
        return $this->belongsTo('App\Models\ExchangeSetupDocument');
    }

    public function translations()
    {
        return $this->hasMany('App\Models\ExchangeSetupDocumentTypeTranslations', 'slug', 'slug');
    }

    public function translation($languageCode = null)
    {
        if (!$languageCode) {
            $languageCode = app()->getLocale() ?: 'en';
        }
        return $this->translations()->where('languageCode', $languageCode)->first();
    }

    public function getNameAttribute($value)
    {
        $currentLanguage = app()->getLocale() ?: 'en';
        $translation = $this->translation($currentLanguage);
        if ($translation && $translation->description) {
            return $translation->description;
        }
        return $this->attributes['name'] ?? '';
    }
}
