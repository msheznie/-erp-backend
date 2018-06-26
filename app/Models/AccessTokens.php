<?php

namespace App\Models;
/**
 * =============================================
 * -- File Name : AccessTokens.php
 * -- Project Name : ERP
 * -- Module Name :  Access Tokens
 * -- Author : Mohamed Fayas
 * -- Create date : 04- May 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 */
use App\Events\logHistory;
use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Event;
use Illuminate\Support\Facades\Log;

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

    protected $dispatchesEvents = [
        //'created' => logHistory::class
    ];

 /*   public static function boot() {
        parent::boot();
        static::created(function($accessToken) {
            Log::info("Access Tokens Created Event Fire: ".$accessToken);
            Event:fire('accessTokens.created', $accessToken);
        });
    }*/

    public $fillable = [
        'user_id',
        'client_id',
        'name',
        'scopes',
        'revoked',
        'expires_at',
        'created_at',
        'updated_at'
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
        'revoked' => 'boolean',
        'created_at' => 'string',
        'updated_at' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
