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

    protected $dispatchesEvents = [
        //'created' => logHistory::class
    ];

    public static function boot() {
        parent::boot();
        static::creating(function($accessToken) {
            // Generate session_id if not already set
            if (empty($accessToken->session_id)) {
                $accessToken->session_id = self::generateSessionId();
            }
        });
    }

    /**
     * Generate a unique session ID starting with SID followed by incrementing number
     *
     * @return string
     */
    public static function generateSessionId()
    {
        // Get the highest session ID number
        $lastSession = self::where('session_id', 'LIKE', 'SID%')
            ->selectRaw('CAST(SUBSTRING(session_id, 4) AS UNSIGNED) as session_num')
            ->orderBy('session_num', 'desc')
            ->first();
        
        $nextNumber = 1;
        if ($lastSession && isset($lastSession->session_num)) {
            $nextNumber = (int)$lastSession->session_num + 1;
        }
        
        return 'SID' . $nextNumber;
    }

    public $fillable = [
        'id',
        'user_id',
        'client_id',
        'name',
        'scopes',
        'revoked',
        'expires_at',
        'created_at',
        'updated_at',
        'session_id'
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
        'updated_at' => 'string',
        'session_id' => 'string',
        'deleted_at' => 'datetime',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
