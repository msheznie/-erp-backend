<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class YesNoSelection
 * @package App\Models
 * @version March 5, 2018, 12:29 pm UTC
 *
 * @property string YesNo
 */
class YesNoSelection extends Model
{
    //use SoftDeletes;

    public $table = 'yesnoselection';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';


    protected $dates = ['deleted_at'];


    public $fillable = [
        'YesNo'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'idyesNoselection' => 'integer',
        'YesNo' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
