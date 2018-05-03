<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class UsersLogHistory
 * @package App\Models
 * @version May 1, 2018, 9:29 am UTC
 *
 * @property integer employee_id
 * @property string empID
 * @property string loginPCId
 */
class UsersLogHistory extends Model
{
    use SoftDeletes;

    public $table = 'usersloghistory';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';


    protected $dates = ['deleted_at'];


    public $fillable = [
        'employee_id',
        'empID',
        'loginPCId'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'employee_id' => 'integer',
        'empID' => 'string',
        'loginPCId' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
