<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="NotificationUserDayCheck",
 *      required={""},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="notificationUserID",
 *          description="notificationUserID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="notificationDaySetupID",
 *          description="notificationDaySetupID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="pushNotification",
 *          description="pushNotification",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="emailNotification",
 *          description="emailNotification",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="webNotification",
 *          description="webNotification",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="createdBy",
 *          description="createdBy",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="updatedBy",
 *          description="updatedBy",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="created_at",
 *          description="created_at",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="updated_at",
 *          description="updated_at",
 *          type="string",
 *          format="date-time"
 *      )
 * )
 */
class NotificationUserDayCheck extends Model
{

    public $table = 'notificationuserdaycheck';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';




    public $fillable = [
        'notificationUserID',
        'notificationDaySetupID',
        'pushNotification',
        'emailNotification',
        'webNotification',
        'createdBy',
        'updatedBy'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'notificationUserID' => 'integer',
        'notificationDaySetupID' => 'integer',
        'pushNotification' => 'integer',
        'emailNotification' => 'integer',
        'webNotification' => 'integer',
        'createdBy' => 'integer',
        'updatedBy' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function notification_user(){ 
        return $this->hasOne(NotificationUser::class, 'id', 'notificationUserID');
    }

}
