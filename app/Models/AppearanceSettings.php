<?php

namespace App\Models;

use App\helper\Helper;
use Eloquent as Model;

class AppearanceSettings extends Model
{
    public $table = 'appearance_settings';

    const UPDATED_AT = 'updated_at';




    public $fillable = [
        'appearance_system_id',
        'appearance_element_id',
        'value'
    ];

    protected $casts = [
        'id' => 'integer',
        'appearance_system_id' => 'integer',
        'appearance_element_id' => 'integer',
        'value' => 'string'
    ];


    public function elements()
    {
        return $this->belongsTo('App\Models\AppearanceElements', 'appearance_element_id');
    }
}
