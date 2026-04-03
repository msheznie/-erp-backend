<?php

namespace App\Services\API;

use App\Models\Company;
use App\Models\CompanyDepartment;
use App\Repositories\CompanyDepartmentRepository;
use App\Utils\ServiceResponse;
use Illuminate\Pagination\LengthAwarePaginator;

class DepartmentAPIService
{
    public function __construct(
        private CompanyDepartmentRepository $companyDepartmentRepository
    ) {
    }

    public function searchDepartments(array $input): ServiceResponse
    {
        $companyId = $input['company_id'] ?? null;
        if (empty($companyId)) {
            return ServiceResponse::failure(trans('custom.companySystemID_is_required'));
        }

        $company = Company::find($companyId);
        if (!$company) {
            return ServiceResponse::failure(trans('custom.company_not_found'));
        }

        $codeFilters = [];
        if (isset($input['code']) && is_array($input['code'])) {
            $codeFilters = collect($input['code'])
                ->map(fn ($c) => trim((string) $c))
                ->filter(fn ($c) => $c !== '')
                ->unique()
                ->values()
                ->all();
        }

        $filters = [
            'code' => $codeFilters,
            'status' => $input['status'] ?? '',
            'type' => $input['type'] ?? '',
        ];

        $departments = $this->companyDepartmentRepository
            ->departmentsSearchQuery((int) $companyId, $filters)
            ->get();

        if (!empty($codeFilters)) {
            $returnedCodes = $departments
                ->pluck('departmentCode')
                ->map(fn ($c) => trim((string) $c))
                ->filter(fn ($c) => $c !== '')
                ->unique()
                ->values();

            $missing = collect($codeFilters)->diff($returnedCodes)->values()->all();
            if (!empty($missing)) {
                return ServiceResponse::failure(trans('custom.department_code_not_matching_with_system'));
            }
        }

        $mappedRows = $departments->map(fn (CompanyDepartment $department) => $this->mapDepartmentToApiRow($department));

        $usePagination = !empty($input['page']);
        $page = (int) ($input['page'] ?? 1);
        $perPage = (int) ($input['per_page'] ?? 10);
        $perPage = min(max($perPage, 1), 500);

        if ($usePagination) {
            $allRows = $mappedRows->values()->all();
            $total = count($allRows);
            $data = new LengthAwarePaginator(
                array_values(array_slice($allRows, ($page - 1) * $perPage, $perPage)),
                $total,
                $perPage,
                $page,
                ['path' => request()->url(), 'query' => request()->query()]
            );
        } else {
            $data = $mappedRows->values()->all();
        }

        return ServiceResponse::success($data, trans('custom.data_retrieved_successfully_3'));
    }

    private function mapDepartmentToApiRow(CompanyDepartment $department): array
    {
        $typeLabel = ((int) $department->type) === 1 ? 'Parent' : 'Final';

        $employeesPayload = [];
        foreach ($department->employees as $departmentEmployee) {
            $employee = $departmentEmployee->employee;
            $employeesPayload[] = [
                'employeeCode' => $employee ? (string) $employee->empID : '',
                'employeeName' => $employee ? (string) ($employee->empFullName ?? $employee->empName ?? '') : '',
                'isHod' => (bool) $departmentEmployee->isHOD,
            ];
        }

        $segmentsPayload = [];
        foreach ($department->companyDepartmentSegments as $departmentSegment) {
            $segment = $departmentSegment->segment;
            $segmentsPayload[] = [
                'segmentCode' => $segment ? (string) $segment->ServiceLineCode : '',
                'segmentName' => $segment ? (string) $segment->ServiceLineDes : '',
            ];
        }

        $parentDepartment = $department->parent
            ? (string) $department->parent->departmentDescription
            : ($department->company ? (string) $department->company->CompanyName : '-');

        return [
            'departmentCode' => (string) $department->departmentCode,
            'departmentDescription' => (string) $department->departmentDescription,
            'type' => $typeLabel,
            'parentDepartment' => $parentDepartment,
            'isFinance' => (bool) $department->isFinance,
            'isActive' => (bool) $department->isActive,
            'employees' => $employeesPayload,
            'segments' => $segmentsPayload,
        ];
    }

   
}
