<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class suppliernature
 * @package App\Models
 * @version February 28, 2018, 4:18 am UTC
 *
 * @property string natureDescription
 */
class suppliernature extends Model
{
    //use SoftDeletes;

    public $table = 'suppliernature';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';


    protected $dates = ['deleted_at'];


    public $fillable = [
        'natureDescription'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'supplierNatureID' => 'integer',
        'natureDescription' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
