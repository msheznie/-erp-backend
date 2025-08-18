<?php
/**
 * =============================================
 * -- File Name : User.php
 * -- Project Name : ERP
 * -- Module Name : User
 * -- Author : Mohamed Fayas
 * -- Create date : 04- May 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 */
namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * Class User
 * @package App\Models
 * @version February 15, 2018, 9:14 am UTC
 *
 * @property string name
 * @property string email
 * @property string password
 * @property string remember_token
 */
class User extends Authenticatable
{
    //use SoftDeletes;
    use HasApiTokens, Notifiable;

    public $table = 'users';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';


    protected $dates = ['deleted_at'];


    public $fillable = [
        'employee_id',
        'empID',
        'name',
        'email',
        'password',
        'uuid',
        'remember_token',
        'login_token',
        'userType'
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
        'name' => 'string',
        'email' => 'string',
        'uuid' => 'string',
        'password' => 'string',
        'userType' => 'integer',
        'remember_token' => 'string',
        'login_token'=> 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function employee()
    {
        return $this->hasOne('App\Models\Employee','employeeSystemID','employee_id');
    }

    public function getUuidAttribute($value)
    {
        return env("WEB_PUSH_APP_NAME")."_".$value;
    }

    public function user_type()
    {
        return $this->hasOne('App\Models\UserType','id','userType');
    }

    /**
     * Get the comments created by this user.
     */
    public function comments()
    {
        return $this->hasMany(\App\Models\BudgetTemplateComment::class, 'user_id', 'id');
    }
}
