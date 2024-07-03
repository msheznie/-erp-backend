<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExchangeSetupDocumentType extends Model
{

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
}
