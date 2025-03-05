<?php

namespace App\Models;

use Eloquent as Model;

class BankConfig extends Model
{

    public $table = 'bank_configs';

    protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $keyType = 'string';

    public $fillable = [
        'uuid',
        'slug',
        'bank_master_id',
        'details',
    ];

    protected $casts = [
        'details' => 'json',
    ];


}
