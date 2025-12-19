<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class BirthdayTemplate extends Model
{
    public $table = 'hr_birthday_templates';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public $timestamps = false;

    public $fillable = [
        'template',
        'client_code',
        'is_default'
    ];

    protected $casts = [
        'template' => 'string',
        'client_code' => 'string',
        'is_default' => 'boolean'
    ];


    public static $rules = [

    ];
}
