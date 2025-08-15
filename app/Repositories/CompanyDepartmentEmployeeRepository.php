<?php

namespace App\Repositories;

use App\Models\CompanyDepartmentEmployee;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class CompanyDepartmentEmployeeRepository
 * @package App\Repositories
 * @version January 2, 2024, 12:00 am UTC
*/

class CompanyDepartmentEmployeeRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'departmentSystemID',
        'employeeSystemID',
        'isHOD',
        'isActive'
    ];

    /**
     * Return searchable fields
     *
     * @return array
     */
    public function getFieldsSearchable()
    {
        return $this->fieldSearchable;
    }

    /**
     * Configure the Model
     **/
    public function model()
    {
        return CompanyDepartmentEmployee::class;
    }

    /**
     * Get all employees for a specific department
     *
     * @param int $departmentSystemID
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getDepartmentEmployees($departmentSystemID)
    {
        return $this->model()::where('departmentSystemID', $departmentSystemID)
                              ->with(['employee', 'department'])
                              ->get();
    }

    /**
     * Check if employee is already HOD in another department
     *
     * @param int $employeeSystemID
     * @param int $excludeDepartmentSystemID
     * @return bool
     */
    public function isEmployeeHODInAnotherDepartment($employeeSystemID, $excludeDepartmentSystemID = null)
    {
        $query = $this->model()::where('employeeSystemID', $employeeSystemID)
                                ->where('isHOD', 1);
        
        if ($excludeDepartmentSystemID) {
            $query->where('departmentSystemID', '!=', $excludeDepartmentSystemID);
        }
        
        return $query->exists();
    }

    /**
     * Check if department already has HOD
     *
     * @param int $departmentSystemID
     * @param int $excludeEmployeeSystemID
     * @return bool
     */
    public function departmentHasHOD($departmentSystemID, $excludeEmployeeSystemID = null)
    {
        $query = $this->model()::where('departmentSystemID', $departmentSystemID)
                                ->where('isHOD', 1);
        
        if ($excludeEmployeeSystemID) {
            $query->where('employeeSystemID', '!=', $excludeEmployeeSystemID);
        }
        
        return $query->exists();
    }

    /**
     * Get employee's department assignments
     *
     * @param int $employeeSystemID
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getEmployeeDepartments($employeeSystemID)
    {
        return $this->model()::where('employeeSystemID', $employeeSystemID)
                              ->with(['department'])
                              ->get();
    }
} 