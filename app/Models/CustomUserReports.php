<?php

namespace App\Models;

use App\helper\Helper;
use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="CustomUserReports",
 *      required={""},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="user_id",
 *          description="user_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="report_master_id",
 *          description="report_master_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="name",
 *          description="name",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="is_private",
 *          description="is_private",
 *          type="boolean"
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
class CustomUserReports extends Model
{

    public $table = 'erp_custom_user_reports';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $appends = ['is_edit_access'];



    public $fillable = [
        'user_id',
        'report_master_id',
        'name',
        'is_private'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
        'report_master_id' => 'integer',
        'name' => 'string',
        'is_private' => 'boolean',
        'is_edit_access' => 'boolean'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function getIsEditAccessAttribute()
    {
        $userId = Helper::getEmployeeSystemID();
        if($this->user_id === $userId){
          return  true;
        }
        return false;
    }

    public function created_by()
    {
        return $this->belongsTo('App\Models\Employee', 'user_id', 'employeeSystemID');
    }

    public function columns()
    {
        return $this->hasMany(CustomUserReportColumns::class, 'user_report_id');
    }

    public function default_columns()
    {
        return $this->hasMany(CustomReportColumns::class, 'report_master_id','report_master_id');
    }

    public function filter_columns()
    {
        return $this->hasMany(CustomFiltersColumn::class, 'user_report_id');
    }

    public function assigned_employees()
    {
        return $this->hasMany(CustomReportEmployees::class,'user_report_id');
    }

    public function summarize(){
        return $this->hasMany(CustomUserReportSummarize::class,'user_report_id');
    }
}
