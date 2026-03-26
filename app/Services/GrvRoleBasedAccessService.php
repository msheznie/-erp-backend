<?php

namespace App\Services;

use App\Models\CompanyDocumentAttachment;
use App\Models\DocumentAccessEmployee;
use App\Models\DocumentAccessRole;
use App\Models\DocumentAccessRoleOwner;
use App\Models\EmployeesDepartment;
use App\Models\GRVMaster;
use App\Models\HrmsEmployeeManager;
use App\Models\Employee;

class GrvRoleBasedAccessService
{
    private const GRV_DOCUMENT_SYSTEM_ID = 3;

    private const DOC_ACCESS_TYPE_VIEW = 1;
    private const DOC_ACCESS_TYPE_CREATE = 2;

    private static function isEnabledValue($value): bool
    {
        if ($value === null) {
            return false;
        }
        if ($value === true || $value === -1 || $value === 1) {
            return true;
        }
        if (is_string($value) && ($value === '1' || $value === 'true' || $value === '-1')) {
            return true;
        }
        if (is_int($value) || is_float($value)) {
            return (int)$value !== 0;
        }
        return false;
    }

    public static function isRoleBasedAccessEnabledForCompany(int $companySystemID): bool
    {
        $attachment = CompanyDocumentAttachment::where('companySystemID', $companySystemID)
            ->where('documentSystemID', self::GRV_DOCUMENT_SYSTEM_ID)
            ->first();

        if (empty($attachment)) {
            // No configuration row => keep legacy behavior.
            return false;
        }

        return self::isEnabledValue($attachment->role_based_access ?? 0);
    }

    /**
     * @return array{reporting_manager_view: bool, reporting_manager_create: bool, hod_view: bool, hod_create: bool, admin_view: bool, admin_create: bool, document_access_role_id: ?int}
     */
    private static function getOwnerToggles(int $companySystemID): array
    {
        $attachment = CompanyDocumentAttachment::where('companySystemID', $companySystemID)
            ->where('documentSystemID', self::GRV_DOCUMENT_SYSTEM_ID)
            ->first();

        // GRV UI defaults (used when doc_access_role row doesn't exist yet).
        $recommended = [
            'reporting_manager_view' => true,
            'reporting_manager_create' => true,
            'hod_view' => true,
            'hod_create' => true,
            'admin_view' => true,
            'admin_create' => true,
        ];

        if (empty($attachment)) {
            return [
                'reporting_manager_view' => false,
                'reporting_manager_create' => false,
                'hod_view' => false,
                'hod_create' => false,
                'admin_view' => false,
                'admin_create' => false,
                'document_access_role_id' => null,
            ];
        }

        $documentAccessRole = DocumentAccessRole::where('document_attachment_id', $attachment->companyDocumentAttachmentID)->first();

        if (empty($documentAccessRole)) {
            return $recommended + ['document_access_role_id' => null];
        }

        // Prefer normalized GRV owner table when available.
        $ownerRows = DocumentAccessRoleOwner::where('document_access_role_id', $documentAccessRole->id)->get();
        $ownerMap = [];
        foreach ($ownerRows as $r) {
            $ownerMap[$r->owner_key] = [
                'view' => self::isEnabledValue($r->can_view ?? 0),
                'edit' => self::isEnabledValue($r->can_edit ?? 0),
            ];
        }

        $rm = $ownerMap['reporting_manager'] ?? null;
        $hod = $ownerMap['hod'] ?? null;
        $admin = $ownerMap['admin'] ?? null;

        return [
            'reporting_manager_view' => $rm ? (bool)$rm['view'] : self::isEnabledValue($documentAccessRole->reportingManager_view ?? 0),
            'reporting_manager_create' => $rm ? (bool)$rm['edit'] : self::isEnabledValue($documentAccessRole->reportingManager_create ?? 0),
            'hod_view' => $hod ? (bool)$hod['view'] : self::isEnabledValue($documentAccessRole->hod_view ?? 0),
            'hod_create' => $hod ? (bool)$hod['edit'] : self::isEnabledValue($documentAccessRole->hod_create ?? 0),
            'admin_view' => $admin ? (bool)$admin['view'] : self::isEnabledValue($documentAccessRole->admin_view ?? 0),
            'admin_create' => $admin ? (bool)$admin['edit'] : self::isEnabledValue($documentAccessRole->admin_create ?? 0),
            'document_access_role_id' => (int)$documentAccessRole->id,
        ];
    }

    /**
     * Direct reportees for a reporting-manager.
     *
     * @return int[]
     */
    private static function getReportingManagerReportees(int $managerEmployeeSystemID): array
    {
        $reporteeEmployeeIDs = HrmsEmployeeManager::where('managerID', $managerEmployeeSystemID)
            ->where('active', 1)
            ->pluck('empID')
            ->toArray();

        if (empty($reporteeEmployeeIDs)) {
            return [];
        }

        // Only active employees should be considered.
        $activeEmployeeIDs = Employee::whereIn('employeeSystemID', $reporteeEmployeeIDs)
            ->where('discharegedYN', 0)
            ->pluck('employeeSystemID')
            ->toArray();

        return $activeEmployeeIDs;
    }

    /**
     * @return int[]
     */
    private static function getHodEmployeesForCurrentEmployee(int $companySystemID, int $hodEmployeeSystemID): array
    {
        // Determine departments for current employee (HOD is tied to departments for this doc).
        $departmentIDs = EmployeesDepartment::where('companySystemID', $companySystemID)
            ->where('employeeSystemID', $hodEmployeeSystemID)
            ->where('documentSystemID', self::GRV_DOCUMENT_SYSTEM_ID)
            ->where('dischargedYN', 0)
            ->where('removedYN', 0)
            ->where('isActive', 1)
            ->pluck('departmentSystemID')
            ->toArray();

        if (empty($departmentIDs)) {
            return [];
        }

        $hodEmployeeIDs = EmployeesDepartment::where('companySystemID', $companySystemID)
            ->where('documentSystemID', self::GRV_DOCUMENT_SYSTEM_ID)
            ->whereIn('departmentSystemID', $departmentIDs)
            ->where('dischargedYN', 0)
            ->where('removedYN', 0)
            ->where('isActive', 1)
            ->pluck('employeeSystemID')
            ->toArray();

        if (empty($hodEmployeeIDs)) {
            return [];
        }

        $activeEmployeeIDs = Employee::whereIn('employeeSystemID', $hodEmployeeIDs)
            ->where('discharegedYN', 0)
            ->pluck('employeeSystemID')
            ->toArray();

        return $activeEmployeeIDs;
    }

    private static function isAdminAssignedForType(int $documentAccessRoleID, int $employeeSystemID, int $accessType): bool
    {
        if (empty($documentAccessRoleID)) {
            return false;
        }

        return DocumentAccessEmployee::where('document_access_role_id', $documentAccessRoleID)
            ->where('employee_id', $employeeSystemID)
            ->where('document_access_type', $accessType)
            ->exists();
    }

    public static function isAdminFullViewEnabled(int $companySystemID, int $employeeSystemID): bool
    {
        if (!self::isRoleBasedAccessEnabledForCompany((int)$companySystemID)) {
            return false;
        }

        $toggles = self::getOwnerToggles((int)$companySystemID);

        return self::isAdminAssignedForType(
                (int)($toggles['document_access_role_id'] ?? 0),
                $employeeSystemID,
                self::DOC_ACCESS_TYPE_VIEW
            ) && $toggles['admin_view'] === true;
    }

    public static function getAllowedCreatedEmployeeIDsForView(int $companySystemID, int $employeeSystemID): array
    {
        if (!self::isRoleBasedAccessEnabledForCompany((int)$companySystemID)) {
            return [(int)$employeeSystemID];
        }

        $toggles = self::getOwnerToggles((int)$companySystemID);

        $allowedCreatedEmployeeIDs = [(int)$employeeSystemID]; // creator always can view own docs.

        if ($toggles['reporting_manager_view'] === true) {
            $allowedCreatedEmployeeIDs = array_merge($allowedCreatedEmployeeIDs, self::getReportingManagerReportees($employeeSystemID));
        }

        if ($toggles['hod_view'] === true) {
            $allowedCreatedEmployeeIDs = array_merge($allowedCreatedEmployeeIDs, self::getHodEmployeesForCurrentEmployee($companySystemID, $employeeSystemID));
        }

        return array_values(array_unique(array_filter($allowedCreatedEmployeeIDs)));
    }

    public static function getAllowedCreatedEmployeeIDsForEdit(int $companySystemID, int $employeeSystemID): array
    {
        if (!self::isRoleBasedAccessEnabledForCompany((int)$companySystemID)) {
            return [(int)$employeeSystemID];
        }

        $toggles = self::getOwnerToggles((int)$companySystemID);

        $allowedCreatedEmployeeIDs = [(int)$employeeSystemID]; // creator always can edit own docs.

        if ($toggles['reporting_manager_create'] === true) {
            $allowedCreatedEmployeeIDs = array_merge($allowedCreatedEmployeeIDs, self::getReportingManagerReportees($employeeSystemID));
        }

        if ($toggles['hod_create'] === true) {
            $allowedCreatedEmployeeIDs = array_merge($allowedCreatedEmployeeIDs, self::getHodEmployeesForCurrentEmployee($companySystemID, $employeeSystemID));
        }

        return array_values(array_unique(array_filter($allowedCreatedEmployeeIDs)));
    }

    public static function canViewGrv(GRVMaster $grvMaster, int $employeeSystemID): bool
    {
        if (!self::isRoleBasedAccessEnabledForCompany((int)$grvMaster->companySystemID)) {
            return true;
        }

        $companySystemID = (int)$grvMaster->companySystemID;
        $toggles = self::getOwnerToggles($companySystemID);

        // Creator (always view own documents).
        if ((int)$grvMaster->createdUserSystemID === (int)$employeeSystemID) {
            return true;
        }

        // Approver (GRV confirmed-by user can view).
        if (!empty($grvMaster->grvConfirmedByEmpSystemID) && (int)$grvMaster->grvConfirmedByEmpSystemID === (int)$employeeSystemID) {
            return true;
        }

        // Admin (full view for configured admin users).
        $adminHasFullView = self::isAdminAssignedForType(
            (int)($toggles['document_access_role_id'] ?? 0),
            $employeeSystemID,
            self::DOC_ACCESS_TYPE_VIEW
        ) && $toggles['admin_view'] === true;

        if ($adminHasFullView) {
            return true;
        }

        // RM/HOD (view depends on created-by employee).
        $allowedCreatedEmployeeIDs = [];
        if ($toggles['reporting_manager_view'] === true) {
            $allowedCreatedEmployeeIDs = array_merge($allowedCreatedEmployeeIDs, self::getReportingManagerReportees($employeeSystemID));
        }
        if ($toggles['hod_view'] === true) {
            $allowedCreatedEmployeeIDs = array_merge($allowedCreatedEmployeeIDs, self::getHodEmployeesForCurrentEmployee($companySystemID, $employeeSystemID));
        }

        $allowedCreatedEmployeeIDs = array_values(array_unique(array_filter($allowedCreatedEmployeeIDs)));

        return in_array((int)$grvMaster->createdUserSystemID, $allowedCreatedEmployeeIDs, true);
    }

    public static function canEditGrv(GRVMaster $grvMaster, int $employeeSystemID): bool
    {
        if (!self::isRoleBasedAccessEnabledForCompany((int)$grvMaster->companySystemID)) {
            return true;
        }

        $companySystemID = (int)$grvMaster->companySystemID;
        $toggles = self::getOwnerToggles($companySystemID);

        // Creator can edit own documents.
        if ((int)$grvMaster->createdUserSystemID === (int)$employeeSystemID) {
            return true;
        }

        // Admin edit (configured admin users can edit all).
        $adminCanEdit = self::isAdminAssignedForType(
                (int)($toggles['document_access_role_id'] ?? 0),
                $employeeSystemID,
                self::DOC_ACCESS_TYPE_CREATE
            ) && $toggles['admin_create'] === true;

        if ($adminCanEdit) {
            return true;
        }

        $allowedCreatedEmployeeIDs = self::getAllowedCreatedEmployeeIDsForEdit($companySystemID, $employeeSystemID);
        return in_array((int)$grvMaster->createdUserSystemID, $allowedCreatedEmployeeIDs, true);
    }

    public static function requireCanViewOrFail(GRVMaster $grvMaster, int $employeeSystemID): void
    {
        if (!self::canViewGrv($grvMaster, $employeeSystemID)) {
            abort(403);
        }
    }

    public static function requireCanEditOrFail(GRVMaster $grvMaster, int $employeeSystemID): void
    {
        if (!self::canEditGrv($grvMaster, $employeeSystemID)) {
            abort(403);
        }
    }
}

