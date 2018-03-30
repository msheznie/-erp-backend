<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Priority
 * @package App\Models
 * @version March 26, 2018, 10:51 am UTC
 *
 * @property string priorityDescription
 */
class Priority extends Model
{
    //use SoftDeletes;

    public $table = 'erp_priority';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';


    protected $dates = ['deleted_at'];


    public $fillable = [
        'priorityDescription'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'priorityID' => 'integer',
        'priorityDescription' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
