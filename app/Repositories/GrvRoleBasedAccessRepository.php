<?php

namespace App\Repositories;

use App\Models\CompanyDocumentAttachment;
use App\Models\DocumentAccessEmployee;
use App\Models\DocumentAccessRole;
use App\Models\DocumentAccessRoleOwner;
use App\Models\DocumentApproved;
use App\Models\Employee;
use App\Models\EmployeeNavigation;
use App\Models\EmployeesDepartment;
use App\Models\HrmsEmployeeManager;
use App\Models\SegmentRights;
use App\Models\UserGroupAssign;
use App\Models\WarehouseRights;
use Illuminate\Support\Collection;

class GrvRoleBasedAccessRepository
{
    public function findAttachment(int $companySystemID, int $documentSystemID): ?CompanyDocumentAttachment
    {
        return CompanyDocumentAttachment::where('companySystemID', $companySystemID)
            ->where('documentSystemID', $documentSystemID)
            ->first();
    }

    public function findDocumentAccessRoleByAttachmentID(int $attachmentID): ?DocumentAccessRole
    {
        return DocumentAccessRole::where('document_attachment_id', $attachmentID)->first();
    }

    public function getOwnerRowsByRoleID(int $documentAccessRoleID): Collection
    {
        return DocumentAccessRoleOwner::where('document_access_role_id', $documentAccessRoleID)->get();
    }

    public function getReportingManagerReporteeIDs(int $managerEmployeeSystemID): array
    {
        $reporteeEmployeeIDs = HrmsEmployeeManager::where('managerID', $managerEmployeeSystemID)
            ->where('active', 1)
            ->pluck('empID')
            ->toArray();

        if (empty($reporteeEmployeeIDs)) {
            return [];
        }

        return Employee::whereIn('employeeSystemID', $reporteeEmployeeIDs)
            ->where('discharegedYN', 0)
            ->pluck('employeeSystemID')
            ->toArray();
    }

    public function getDepartmentIDsForEmployee(int $companySystemID, int $employeeSystemID, int $documentSystemID): array
    {
        return EmployeesDepartment::where('companySystemID', $companySystemID)
            ->where('employeeSystemID', $employeeSystemID)
            ->where('documentSystemID', $documentSystemID)
            ->where('dischargedYN', 0)
            ->where('removedYN', 0)
            ->where('isActive', 1)
            ->pluck('departmentSystemID')
            ->toArray();
    }

    public function getActiveDepartmentEmployeeIDs(int $companySystemID, int $documentSystemID, array $departmentIDs): array
    {
        if (empty($departmentIDs)) {
            return [];
        }

        $employeeIDs = EmployeesDepartment::where('companySystemID', $companySystemID)
            ->where('documentSystemID', $documentSystemID)
            ->whereIn('departmentSystemID', $departmentIDs)
            ->where('dischargedYN', 0)
            ->where('removedYN', 0)
            ->where('isActive', 1)
            ->pluck('employeeSystemID')
            ->toArray();

        if (empty($employeeIDs)) {
            return [];
        }

        return Employee::whereIn('employeeSystemID', $employeeIDs)
            ->where('discharegedYN', 0)
            ->pluck('employeeSystemID')
            ->toArray();
    }

    public function isAdminAssignedForType(int $documentAccessRoleID, int $employeeSystemID, int $accessType): bool
    {
        if (empty($documentAccessRoleID)) {
            return false;
        }

        return DocumentAccessEmployee::where('document_access_role_id', $documentAccessRoleID)
            ->where('employee_id', $employeeSystemID)
            ->where('document_access_type', $accessType)
            ->exists();
    }

    public function getAllowedSegmentIDsForView(int $companySystemID, int $employeeSystemID): array
    {
        return array_map('intval', SegmentRights::where('companySystemID', $companySystemID)
            ->where('employeeSystemID', $employeeSystemID)
            ->pluck('serviceLineSystemID')
            ->toArray());
    }

    public function getAllowedWarehouseIDsForView(int $companySystemID, int $employeeSystemID): array
    {
        return array_map('intval', WarehouseRights::where('companySystemID', $companySystemID)
            ->where('employeeSystemID', $employeeSystemID)
            ->pluck('wareHouseSystemCode')
            ->toArray());
    }

    public function isApproverForGrv(int $companySystemID, int $employeeSystemID, int $documentSystemID, int $grvAutoID): bool
    {
        return DocumentApproved::where('companySystemID', $companySystemID)
            ->where('documentSystemID', $documentSystemID)
            ->where('documentSystemCode', $grvAutoID)
            ->where('employeeSystemID', $employeeSystemID)
            ->exists();
    }

    public function getNavigationUserGroupIDs(int $companySystemID, int $employeeSystemID): array
    {
        return EmployeeNavigation::where('employeeSystemID', $employeeSystemID)
            ->where('companyID', $companySystemID)
            ->pluck('userGroupID')
            ->toArray();
    }

    public function getNavigationAssignments(int $companySystemID, int $navigationMenuID, array $userGroupIDs): Collection
    {
        if (empty($userGroupIDs)) {
            return collect();
        }

        return UserGroupAssign::where('companyID', $companySystemID)
            ->where('navigationMenuID', $navigationMenuID)
            ->whereIn('userGroupID', $userGroupIDs)
            ->get();
    }
}
