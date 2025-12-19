<?php

namespace App\Models;

use Eloquent as Model;
use App\Models\NotificationUser;
/**
 * @SWG\Definition(
 *      definition="NotificationCompanyScenario",
 *      required={""},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="scenarioID",
 *          description="scenarioID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="companyID",
 *          description="companyID",
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
class NotificationCompanyScenario extends Model
{

    public $table = 'notificationcompanyscenario';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';




    public $fillable = [
        'scenarioID',
        'companyID',
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
        'scenarioID' => 'integer',
        'companyID' => 'integer',
        'isActive' => 'integer',
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

    public function notification_Scenario(){ 
        return $this->hasOne(NotificationScenarios::class, 'id', 'scenarioID');
    }

    public function notification_day_setup(){ 
        return $this->hasMany(NotificationDaySetup::class, 'companyScenarionID', 'id');
    } 

    public function user(){ 
        return $this->hasMany(NotificationUser::class, 'companyScenarionID', 'id');
    } 
    public function company()
    {
        return $this->belongsTo('App\Models\Company', 'companyID', 'companySystemID');
    }
    public static function getCompanyScenario(){
        return self::select('id', 'scenarioID', 'companyID', 'isActive')
            ->where('isActive', 1)
            ->whereIn('scenarioID', [45, 46])
            ->with([
                'notification_day_setup' => function ($q) {
                    $q->select('id', 'companyScenarionID', 'beforeAfter', 'days', 'isActive', 'frequency')
                        ->where('isActive', 1);
                }
            ])
            ->whereHas('notification_day_setup', function ($q) {
                $q->where('isActive', 1);
            })
            ->get();
    }
}
