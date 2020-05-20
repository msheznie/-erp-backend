<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="FcmToken",
 *      required={""},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="userID",
 *          description="userID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="fcm_token",
 *          description="fcm_token",
 *          type="string"
 *      )
 * )
 */
class FcmToken extends Model
{

    public $table = 'fcmtoken';
    
    const CREATED_AT = null;
    const UPDATED_AT = null;




    public $fillable = [
        'userID',
        'fcm_token',
        'deviceType',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'userID' => 'integer',
        'fcm_token' => 'string',
        'deviceType' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
