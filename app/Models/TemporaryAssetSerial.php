<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TemporaryAssetSerial extends Model
{
    public $table = 'temporary_asset_serial';
    protected $primaryKey = 'id';





    public $fillable = [
        'serialID',
        'lastSerialNo'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'serialID' => 'integer',
        'lastSerialNo' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [

    ];

}
