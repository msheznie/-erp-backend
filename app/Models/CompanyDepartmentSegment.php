<?php
namespace App\Models;
use Eloquent as Model;

class CompanyDepartmentSegment extends Model
{
    public $table = 'company_departments_segments';
    public $primaryKey = 'departmentSegmentSystemID';

    public $fillable = [
        'departmentSystemID',
        'serviceLineSystemID',
        'isActive'
    ];

    protected $casts = [
        'departmentSegmentSystemID' => 'integer',
        'departmentSystemID' => 'integer',
        'serviceLineSystemID' => 'integer',
        'isActive' => 'integer'
    ];

    public static $rules = [
        'departmentSystemID' => 'required|integer',
        'serviceLineSystemID' => 'required|integer',
        'isActive' => 'integer|in:0,1'
    ];

    public function department()
    {
        return $this->belongsTo('App\Models\CompanyDepartment', 'departmentSystemID', 'departmentSystemID');
    }

    public function segment()
    {
        return $this->belongsTo('App\Models\SegmentMaster', 'serviceLineSystemID', 'serviceLineSystemID');
    }

    public function company()
    {
        return $this->hasOneThrough('App\Models\Company', 'App\Models\CompanyDepartment', 'departmentSystemID', 'companySystemID', 'departmentSystemID', 'companySystemID');
    }
} 