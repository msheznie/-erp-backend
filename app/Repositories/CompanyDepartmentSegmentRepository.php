<?php
namespace App\Repositories;
use App\Models\CompanyDepartmentSegment;
use InfyOm\Generator\Common\BaseRepository;

class CompanyDepartmentSegmentRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'departmentSystemID',
        'serviceLineSystemID',
        'isActive'
    ];

    public function model()
    {
        return CompanyDepartmentSegment::class;
    }

    public function getDepartmentSegments($departmentSystemID)
    {
        return $this->model()::where('departmentSystemID', $departmentSystemID)
                              ->with(['segment', 'department'])
                              ->get();
    }

    public function isSegmentAssignedToDepartment($departmentSystemID, $serviceLineSystemID)
    {
        return $this->model()::where('departmentSystemID', $departmentSystemID)
                              ->where('serviceLineSystemID', $serviceLineSystemID)
                              ->exists();
    }
} 