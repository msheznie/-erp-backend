<?php
/**
 * =============================================
 * -- File Name : SegmentMaster.php
 * -- Project Name : ERP
 * -- Module Name :  Segment Master
 * -- Author : Nazir
 * -- Create date : 18 - April 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 * --
 */
namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class SegmentMaster
 * @package App\Models
 * @version March 19, 2018, 10:57 am UTC
 *
 * @property string ServiceLineCode
 * @property string serviceLineMasterCode
 * @property string companyID
 * @property string ServiceLineDes
 * @property integer locationID
 * @property integer isActive
 * @property integer isPublic
 * @property integer isServiceLine
 * @property integer isDepartment
 * @property string createdUserGroup
 * @property string createdPcID
 * @property string createdUserID
 * @property string modifiedPc
 * @property string modifiedUser
 * @property string|\Carbon\Carbon createdDateTime
 * @property string|\Carbon\Carbon timeStamp
 */
class SegmentMaster extends Model
{
    //use SoftDeletes;
    public $table = 'serviceline';

    const CREATED_AT = 'createdDateTime';
    const UPDATED_AT = 'timeStamp';
    protected $primaryKey  = 'serviceLineSystemID';


    protected $dates = ['deleted_at'];


    public $fillable = [
        'ServiceLineCode',
        'serviceLineMasterCode',
        'companySystemID',
        'companyID',
        'ServiceLineDes',
        'locationID',
        'isActive',
        'isMaster',
        'isPublic',
        'isServiceLine',
        'isDepartment',
        'createdUserGroup',
        'createdPcID',
        'createdUserID',
        'isDeleted',
        'modifiedPc',
        'modifiedUser',
        'createdDateTime',
        'createdUserSystemID',
        'modifiedUserSystemID',
        'timeStamp',
        'consoleCode',
        'isFinalLevel',
        'masterID',
        'consoleDescription',
        'isDelegation',
        'approved_yn',
        'approved_date',
        'RollLevForApp_curr',
        'approved_emp_system_id',
        'approved_by_emp_id',
        'approved_by_emp_name',
        'refferedBackYN',
        'timesReferred'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'serviceLineSystemID' => 'integer',
        'ServiceLineCode' => 'string',
        'serviceLineMasterCode' => 'string',
        'companySystemID' => 'integer',
        'companyID' => 'string',
        'ServiceLineDes' => 'string',
        'locationID' => 'integer',
        'isActive' => 'integer',
        'isMaster' => 'integer',
        'isPublic' => 'integer',
        'isServiceLine' => 'integer',
        'masterID' => 'integer',
        'isFinalLevel' => 'boolean',
        'isDeleted' => 'boolean',
        'isDepartment' => 'integer',
        'createdUserGroup' => 'string',
        'createdPcID' => 'string',
        'createdUserID' => 'string',
        'modifiedPc' => 'string',
        'modifiedUser' => 'string',
        'consoleCode' => 'string',
        'consoleDescription' => 'string',
        'createdUserSystemID' => 'integer',
        'RollLevForApp_curr' => 'integer',
        'documentSystemID' => 'integer',
        'modifiedUserSystemID' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    /**
     * Scope a query to only include active serviceline.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */

    public function scopeIsActive($query)
    {
        return $query->where('isActive',  1);
    }

    /**
     * Scope a query to only include active serviceline.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */

    public function scopeIsPublic($query)
    {
        return $query->where('isPublic',  1);
    }

    public function scopeApproved($query)
    {
        return $query->where('approved_yn', 1); 
    }

    public function scopeWithAssigned($query, $companyId)
    {
        return $query->whereHas('assignedSegments', function ($q) use ($companyId) {
            $q->where('companySystemID', $companyId)->where('isActive', 1)
              ->where('isAssigned', 1);
        });
    }

    /**
     * Scope a query to only include users of a given type.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param mixed $type
     * @return \Illuminate\Database\Eloquent\Builder
     */

    public function scopeOfCompany($query, $type)
    {
        return $query->whereIN('companySystemID',  $type);
    }

    public static function isSegmentUsedInDepartment($id) {
        return \App\Models\CompanyDepartmentSegment::where('serviceLineSystemID', $id)->exists();
    }
    
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('final_level', function (Builder $builder) {
            $builder->where('isFinalLevel', 1);
        });

        static::addGlobalScope('deleted_status', function (Builder $builder) {
            $builder->where('isDeleted', 0);
        });
    }

    /**
     * joining the company with serviceline table.
     */

    public function company()
    {
        return $this->belongsTo('App\Models\Company', 'companySystemID', 'companySystemID');
    }

    public function department()
    {
        return $this->hasMany('App\Models\HrmsDepartmentMaster','serviceLineSystemID','serviceLineSystemID');
    }

    public function parent()
    {
        return $this->belongsTo('App\Models\SegmentMaster', 'masterID', 'serviceLineSystemID')->withoutGlobalScope('final_level');
    }


    public function sub_levels()
    {
        return $this->hasMany('App\Models\SegmentMaster', 'masterID', 'serviceLineSystemID')->with('sub_levels')->where('isDeleted', 0)->withoutGlobalScope('final_level');
    }

    public function sub_level_deleted()
    {
        return $this->hasMany('App\Models\SegmentMaster', 'masterID', 'serviceLineSystemID')->with('sub_level_deleted')->withoutGlobalScope('final_level')->withoutGlobalScope('deleted_status');
    }

    public static function getSegmentCode($serviceLineSystemID)
    {
        $segment = SegmentMaster::find($serviceLineSystemID);

        return ($segment) ? $segment->ServiceLineCode : null;
    }

    public static function getAllChildSegmentIds($segmentId)
    {
        $ids = [];
        $children = self::withoutGlobalScopes()
            ->where('masterID', $segmentId)
            ->pluck('serviceLineSystemID')
            ->toArray();

        foreach ($children as $childId) {
            $ids[] = $childId;
            $ids = array_merge($ids, self::getAllChildSegmentIds($childId));
        }

        return $ids;
    }

    public static function getSegmentX()
    {
        return self::where('ServiceLineCode', 'X')->get();
    }

    public function created_by()
    {
        return $this->belongsTo('App\Models\Employee', 'modifiedUserSystemID', 'employeeSystemID');
    }

    public function confirmed_by()
    {
        return $this->belongsTo('App\Models\Employee', 'confirmed_by_emp_system_id', 'employeeSystemID');
    }

    public function modified_by()
    {
        return $this->belongsTo('App\Models\Employee', 'modifiedUserSystemID', 'employeeSystemID');
    }

    public function approved_by(){
        return $this->hasMany('App\Models\DocumentApproved','documentSystemCode','serviceLineSystemID');
    }

    public function approved_by_emp()
    {
        return $this->belongsTo('App\Models\Employee','approved_emp_system_id','employeeSystemID');
    }

    public function assignedSegments()
    {
        return $this->hasMany('App\Models\SegmentAssigned', 'serviceLineSystemID', 'serviceLineSystemID');
    }
}
