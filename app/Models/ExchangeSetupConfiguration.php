<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExchangeSetupConfiguration extends Model
{
    public $fillable = [
        'exchangeSetupDocumentTypeId',
        'companyId',
        'isActive',
        'allowErChanges',
        'allowGainOrLossCal',
        'createdBy'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'exchangeSetupDocumentTypeId' => 'integer',
        'companyId' => 'integer',
        'isActive' => 'Boolean',
        'allowErChanges' => 'Boolean',
        'allowGainOrLossCal' => 'Boolean',
        'createdBy' => 'integer'
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
}
