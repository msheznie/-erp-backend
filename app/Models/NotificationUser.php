<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="NotificationUser",
 *      required={""},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="empID",
 *          description="empID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="companyScenarionID",
 *          description="companyScenarionID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="applicableCategoryID",
 *          description="applicableCategoryID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isActive",
 *          description="isActive",
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
class NotificationUser extends Model
{

    public $table = 'notificationuser';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';




    public $fillable = [
        'empID',
        'companyScenarionID',
        'applicableCategoryID',
        'isActive',
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
        'empID' => 'integer',
        'companyScenarionID' => 'integer',
        'applicableCategoryID' => 'integer',
        'isActive' => 'integer',
        'createdBy' => 'integer',
        'updatedBy' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [];

    public static function get_notification_users_setup($comScenarioID){
        $setup = NotificationUser::selectRaw('empID, applicableCategoryID')
            ->where('companyScenarionID', $comScenarioID)
            ->where('isActive', 1);

        return $setup->get();
    }

    public function employee(){
        return $this->belongsTo('App\Models\Employee','empID','employeeSystemID');
    }
    
    public function notificationUserDayCheck(){ 
        return $this->hasOne('App\Models\NotificationUserDayCheck', 'notificationUserID', 'id');
    }

    public static function getUsers($comScenarioID)
    {
        return self::selectRaw('empID, applicableCategoryID')
            ->where('companyScenarionID', $comScenarioID)
            ->where('isActive', 1)
            ->whereHas('notificationUserDayCheck', function ($q) {
                $q->where('emailNotification', 1);
            })
            ->with('notificationUserDayCheck')
            ->get();
    }


}
