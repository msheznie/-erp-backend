<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BankConfig extends Model
{

    protected $table = 'bank_configs';

    protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'uuid',
        'slug',
        'bank_master_id',
        'details',
    ];

    protected $casts = [
        'details' => 'json',
    ];


}
