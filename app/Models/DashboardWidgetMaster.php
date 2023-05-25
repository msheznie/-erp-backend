<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="DashboardWidgetMaster",
 *      required={""},
 *      @SWG\Property(
 *          property="widgetMasterID",
 *          description="widgetMasterID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="WidgetMasterName",
 *          description="WidgetMasterName",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="departmentID",
 *          description="departmentID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="sortOrder",
 *          description="sortOrder",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="widgetMasterIcon",
 *          description="widgetMasterIcon",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="isActive",
 *          description="isActive",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="timestamp",
 *          description="timestamp",
 *          type="string",
 *          format="date-time"
 *      )
 * )
 */
class DashboardWidgetMaster extends Model
{

    public $table = 'erp_dashboard_widget_master';
    
    const CREATED_AT = 'timestamp';
    const UPDATED_AT = 'timestamp';

    protected $primaryKey = 'widgetMasterID';


    public $fillable = [
        'WidgetMasterName',
        'departmentID',
        'sortOrder',
        'widgetMasterIcon',
        'isActive',
        'isDefault',
        'widgetTypeID',
        'timestamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'widgetMasterID' => 'integer',
        'WidgetMasterName' => 'string',
        'departmentID' => 'integer',
        'sortOrder' => 'string',
        'widgetMasterIcon' => 'string',
        'isActive' => 'integer',
        'isDefault' => 'integer',
        'widgetTypeID' => 'integer',
        'timestamp' => 'datetime'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
//        'widgetMasterID' => 'required'
    ];

    public function department(){
        return $this->belongsTo('App\Models\DepartmentMaster', 'departmentID', 'departmentSystemID');
    }

    
}
