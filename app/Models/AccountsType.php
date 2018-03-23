<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class AccountsType
 * @package App\Models
 * @version March 16, 2018, 8:44 am UTC
 *
 * @property string description
 * @property string code
 */
class AccountsType extends Model
{
    //use SoftDeletes;

    public $table = 'accountstype';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $primaryKey  = 'accountsType';

    protected $dates = ['deleted_at'];


    public $fillable = [
        'description',
        'code'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'accountsType' => 'integer',
        'description' => 'string',
        'code' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
