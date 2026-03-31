<?php

namespace App\Repositories;

use App\Models\CompanyDepartment;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class CompanyDepartmentRepository
 * @package App\Repositories
 * @version December 18, 2024
 *
 * @method CompanyDepartment findWithoutFail($id, $columns = ['*'])
 * @method CompanyDepartment find($id, $columns = ['*'])
 * @method CompanyDepartment first($columns = ['*'])
*/
class CompanyDepartmentRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'departmentCode',
        'departmentDescription',
        'companySystemID',
        'type',
        'parentDepartmentID',
        'isFinance',
        'isActive',
        'createdUserSystemID',
        'modifiedUserSystemID'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return CompanyDepartment::class;
    }

    /**
     * Get departments by company
     */
    public function getDepartmentsByCompany($companySystemID)
    {
        return $this->model()::where('companySystemID', $companySystemID)
                              ->where('isActive', 1)
                              ->get();
    }

    /**
     * Get parent departments for dropdown
     */
    public function getParentDepartments($companySystemID)
    {
        return $this->model()::where('companySystemID', $companySystemID)
                              ->where('type', 1) // Parent type
                              ->where('isActive', 1)
                              ->get();
    }

    /**
     * Check if department has children
     */
    public function hasChildren($departmentSystemID)
    {
        return $this->model()::where('parentDepartmentID', $departmentSystemID)
                              ->exists();
    }

    /**
     * Check if there's already a finance department
     */
    public function hasFinanceDepartment($companySystemID, $excludeId = null)
    {
        $query = $this->model()::where('companySystemID', $companySystemID)
                               ->where('isFinance', 1);
        
        if ($excludeId) {
            $query->where('departmentSystemID', '!=', $excludeId);
        }
        
        return $query->exists();
    }

    /**
     * Get department structure for company
     */
    public function getDepartmentStructure($companySystemID)
    {
        return $this->model()::where('companySystemID', $companySystemID)
                              ->whereNull('parentDepartmentID') // Start with departments directly under company
                              ->with('children')
                              ->get();
    }

    /**
     * Eloquent query for external integration department search 
     *
     * @param  array  $filters  Keys: code (optional exact), status (ACTIVE|INACTIVE), type (Parent|Final)
     */
    public function departmentsSearchQuery(int $companySystemID, array $filters): Builder
    {
        $query = $this->model()::query()
            ->select([
                'departmentSystemID',
                'companySystemID',
                'parentDepartmentID',
                'departmentCode',
                'departmentDescription',
                'isFinance',
                'isActive',
            ])
            ->where('companySystemID', $companySystemID)
            ->with([
                'parent:departmentSystemID,departmentDescription',
                'company:companySystemID,CompanyName',
                'employees:departmentEmployeeSystemID,departmentSystemID,employeeSystemID,isHOD',
                'employees.employee:employeeSystemID,empID,empFullName,empName',
                'companyDepartmentSegments:departmentSegmentSystemID,departmentSystemID,serviceLineSystemID',
                'companyDepartmentSegments.segment:serviceLineSystemID,ServiceLineCode,ServiceLineDes',
                    ])
            ->withCount('children');

        $codes = $filters['code'] ?? [];
        if (!empty($codes)) {
            $query->whereIn('departmentCode', $codes);
        }

        if (!empty($filters['status'])) {
            $query->where('isActive', $filters['status'] === 'ACTIVE' ? 1 : 0);
        }

        if (!empty($filters['type'])) {
            if ($filters['type'] === 'Parent') {
                $query->whereHas('children');
            } else {
                $query->whereDoesntHave('children');
            }
        }

        return $query->orderBy('departmentCode');
    }
}
