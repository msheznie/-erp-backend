<?php

namespace App\Models;

use App\Events\logHistory;
use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class AccessTokens
 * @package App\Models
 * @version May 1, 2018, 6:43 am UTC
 *
 * @property integer user_id
 * @property integer client_id
 * @property string name
 * @property string scopes
 * @property boolean revoked
 * @property string|\Carbon\Carbon expires_at
 */
class AccessTokens extends Model
{
    //use SoftDeletes;

    public $table = 'oauth_access_tokens';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';


    protected $dates = ['deleted_at'];

   /* protected $dispatchesEvents = [
        'created' => logHistory::class,
    ];*/


    public $fillable = [
        'user_id',
        'client_id',
        'name',
        'scopes',
        'revoked',
        'expires_at'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'string',
        'user_id' => 'integer',
        'client_id' => 'integer',
        'name' => 'string',
        'scopes' => 'string',
        'revoked' => 'boolean'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
