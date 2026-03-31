<?php

namespace App\Services;

use App\Models\GRVMaster;
use App\Repositories\GrvRoleBasedAccessRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class GrvRoleBasedAccessService
{
    private const GRV_DOCUMENT_SYSTEM_ID = 3;

    private const DOC_ACCESS_TYPE_VIEW = 1;
    private const DOC_ACCESS_TYPE_CREATE = 2;

    /** @var GrvRoleBasedAccessRepository */
    private $repository;

    public function __construct(GrvRoleBasedAccessRepository $repository)
    {
        $this->repository = $repository;
    }

    private function isEnabledValue($value): bool
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

    public function isRoleBasedAccessEnabledForCompany(int $companySystemID): bool
    {
        $attachment = $this->repository->findAttachment($companySystemID, self::GRV_DOCUMENT_SYSTEM_ID);

        if (empty($attachment)) {
            // No configuration row => keep legacy behavior.
            return false;
        }

        return $this->isEnabledValue($attachment->role_based_access ?? 0);
    }

    /**
     * @return array{
     *   reporting_manager_view: bool,
     *   reporting_manager_create: bool,
     *   hod_view: bool,
     *   hod_create: bool,
     *   admin_view: bool,
     *   admin_create: bool,
     *   creator_view: bool,
     *   approver_view: bool,
     *   segment_view: bool,
     *   warehouse_view: bool,
     *   document_access_role_id: ?int
     * }
     */
    private function getOwnerToggles(int $companySystemID): array
    {
        $attachment = $this->repository->findAttachment($companySystemID, self::GRV_DOCUMENT_SYSTEM_ID);

        // GRV UI defaults (used when doc_access_role row doesn't exist yet).
        $recommended = [
            'reporting_manager_view' => true,
            'reporting_manager_create' => true,
            'hod_view' => true,
            'hod_create' => true,
            'admin_view' => true,
            'admin_create' => true,
            'creator_view' => true,
            'approver_view' => true,
            'segment_view' => true,
            'warehouse_view' => true,
        ];

        if (empty($attachment)) {
            return [
                'reporting_manager_view' => false,
                'reporting_manager_create' => false,
                'hod_view' => false,
                'hod_create' => false,
                'admin_view' => false,
                'admin_create' => false,
                'creator_view' => false,
                'approver_view' => false,
                'segment_view' => false,
                'warehouse_view' => false,
                'document_access_role_id' => null,
            ];
        }

        $documentAccessRole = $this->repository->findDocumentAccessRoleByAttachmentID((int)$attachment->companyDocumentAttachmentID);

        if (empty($documentAccessRole)) {
            return $recommended + ['document_access_role_id' => null];
        }

        // Prefer normalized GRV owner table when available.
        $ownerRows = $this->repository->getOwnerRowsByRoleID((int)$documentAccessRole->id);
        $ownerMap = [];
        foreach ($ownerRows as $r) {
            $ownerMap[$r->owner_key] = [
                'view' => $this->isEnabledValue($r->can_view ?? 0),
                'edit' => $this->isEnabledValue($r->can_edit ?? 0),
            ];
        }

        $rm = $ownerMap['reporting_manager'] ?? null;
        $hod = $ownerMap['hod'] ?? null;
        $admin = $ownerMap['admin'] ?? null;
        $creator = $ownerMap['creator'] ?? null;
        $approver = $ownerMap['approver'] ?? null;
        $segment = $ownerMap['segment'] ?? null;
        $warehouse = $ownerMap['warehouse'] ?? null;

        return [
            'reporting_manager_view' => $rm ? (bool)$rm['view'] : $this->isEnabledValue($documentAccessRole->reportingManager_view ?? 0),
            'reporting_manager_create' => $rm ? (bool)$rm['edit'] : $this->isEnabledValue($documentAccessRole->reportingManager_create ?? 0),
            'hod_view' => $hod ? (bool)$hod['view'] : $this->isEnabledValue($documentAccessRole->hod_view ?? 0),
            'hod_create' => $hod ? (bool)$hod['edit'] : $this->isEnabledValue($documentAccessRole->hod_create ?? 0),
            'admin_view' => $admin ? (bool)$admin['view'] : $this->isEnabledValue($documentAccessRole->admin_view ?? 0),
            'admin_create' => $admin ? (bool)$admin['edit'] : $this->isEnabledValue($documentAccessRole->admin_create ?? 0),
            'creator_view' => $creator ? (bool)$creator['view'] : true,
            'approver_view' => $approver ? (bool)$approver['view'] : true,
            'segment_view' => $segment ? (bool)$segment['view'] : true,
            'warehouse_view' => $warehouse ? (bool)$warehouse['view'] : true,
            'document_access_role_id' => (int)$documentAccessRole->id,
        ];
    }

    /**
     * Direct reportees for a reporting-manager.
     *
     * @return int[]
     */
    private function getReportingManagerReportees(int $managerEmployeeSystemID): array
    {
        return $this->repository->getReportingManagerReporteeIDs($managerEmployeeSystemID);
    }

    /**
     * @return int[]
     */
    private function getHodEmployeesForCurrentEmployee(int $companySystemID, int $hodEmployeeSystemID): array
    {
        $departmentIDs = $this->repository->getDepartmentIDsForEmployee(
            $companySystemID,
            $hodEmployeeSystemID,
            self::GRV_DOCUMENT_SYSTEM_ID
        );

        return $this->repository->getActiveDepartmentEmployeeIDs(
            $companySystemID,
            self::GRV_DOCUMENT_SYSTEM_ID,
            $departmentIDs
        );
    }

    private function isAdminAssignedForType(int $documentAccessRoleID, int $employeeSystemID, int $accessType): bool
    {
        return $this->repository->isAdminAssignedForType($documentAccessRoleID, $employeeSystemID, $accessType);
    }

    private function hasAdminFullView(array $toggles, int $employeeSystemID): bool
    {
        return $this->isAdminAssignedForType(
            (int)($toggles['document_access_role_id'] ?? 0),
            $employeeSystemID,
            self::DOC_ACCESS_TYPE_VIEW
        ) && $toggles['admin_view'] === true;
    }

    private function hasAdminFullEdit(array $toggles, int $employeeSystemID): bool
    {
        return $this->isAdminAssignedForType(
            (int)($toggles['document_access_role_id'] ?? 0),
            $employeeSystemID,
            self::DOC_ACCESS_TYPE_CREATE
        ) && $toggles['admin_create'] === true;
    }

    /**
     * @return int[]
     */
    private function getAllowedSegmentIDsForView(int $companySystemID, int $employeeSystemID): array
    {
        return $this->repository->getAllowedSegmentIDsForView($companySystemID, $employeeSystemID);
    }

    /**
     * @return int[]
     */
    private function getAllowedWarehouseIDsForView(int $companySystemID, int $employeeSystemID): array
    {
        return $this->repository->getAllowedWarehouseIDsForView($companySystemID, $employeeSystemID);
    }

    private function isApproverForGrv(int $companySystemID, int $employeeSystemID, int $grvAutoID): bool
    {
        return $this->repository->isApproverForGrv($companySystemID, $employeeSystemID, self::GRV_DOCUMENT_SYSTEM_ID, $grvAutoID);
    }

    /**
     * @return int[]
     */
    private function createdEmployeeIdsForRmHod(
        int $companySystemID,
        int $employeeSystemID,
        bool $reportingManagerOn,
        bool $hodOn
    ): array {
        $ids = [];
        if ($reportingManagerOn) {
            $ids = array_merge($ids, $this->getReportingManagerReportees($employeeSystemID));
        }
        if ($hodOn) {
            $ids = array_merge($ids, $this->getHodEmployeesForCurrentEmployee($companySystemID, $employeeSystemID));
        }

        return array_values(array_unique(array_filter($ids)));
    }

    public function getAllowedCreatedEmployeeIDsForEdit(int $companySystemID, int $employeeSystemID): array
    {
        if (!$this->isRoleBasedAccessEnabledForCompany((int)$companySystemID)) {
            return [(int)$employeeSystemID];
        }

        $toggles = $this->getOwnerToggles((int)$companySystemID);

        $allowed = [(int)$employeeSystemID];
        $allowed = array_merge(
            $allowed,
            $this->createdEmployeeIdsForRmHod(
                $companySystemID,
                $employeeSystemID,
                $toggles['reporting_manager_create'] === true,
                $toggles['hod_create'] === true
            )
        );

        return array_values(array_unique(array_filter($allowed)));
    }

    public function canViewGrv(GRVMaster $grvMaster, int $employeeSystemID): bool
    {
        if (!$this->isRoleBasedAccessEnabledForCompany((int)$grvMaster->companySystemID)) {
            return true;
        }

        $companySystemID = (int)$grvMaster->companySystemID;
        $toggles = $this->getOwnerToggles($companySystemID);

        // Creator.
        if ($toggles['creator_view'] === true && (int)$grvMaster->createdUserSystemID === (int)$employeeSystemID) {
            return true;
        }

        // Approver.
        if ($toggles['approver_view'] === true && $this->isApproverForGrv($companySystemID, $employeeSystemID, (int)$grvMaster->grvAutoID)) {
            return true;
        }

        if ($this->hasAdminFullView($toggles, $employeeSystemID)) {
            return true;
        }

        $allowedCreatedEmployeeIDs = $this->createdEmployeeIdsForRmHod(
            $companySystemID,
            $employeeSystemID,
            $toggles['reporting_manager_view'] === true,
            $toggles['hod_view'] === true
        );

        if (in_array((int)$grvMaster->createdUserSystemID, $allowedCreatedEmployeeIDs, true)) {
            return true;
        }

        // Segment.
        if ($toggles['segment_view'] === true) {
            $allowedSegmentIDs = $this->getAllowedSegmentIDsForView($companySystemID, $employeeSystemID);
            if (!empty($allowedSegmentIDs) && in_array((int)$grvMaster->serviceLineSystemID, $allowedSegmentIDs, true)) {
                return true;
            }
        }

        // Warehouse.
        if ($toggles['warehouse_view'] === true) {
            $allowedWarehouseIDs = $this->getAllowedWarehouseIDsForView($companySystemID, $employeeSystemID);
            if (!empty($allowedWarehouseIDs) && in_array((int)$grvMaster->grvLocation, $allowedWarehouseIDs, true)) {
                return true;
            }
        }

        return false;
    }

    public function canEditGrv(GRVMaster $grvMaster, int $employeeSystemID): bool
    {
        if (!$this->isRoleBasedAccessEnabledForCompany((int)$grvMaster->companySystemID)) {
            return true;
        }

        $companySystemID = (int)$grvMaster->companySystemID;
        $toggles = $this->getOwnerToggles($companySystemID);

        // Creator can edit own documents.
        if ((int)$grvMaster->createdUserSystemID === (int)$employeeSystemID) {
            return true;
        }

        if ($this->hasAdminFullEdit($toggles, $employeeSystemID)) {
            return true;
        }

        $allowedCreatedEmployeeIDs = $this->getAllowedCreatedEmployeeIDsForEdit($companySystemID, $employeeSystemID);
        return in_array((int)$grvMaster->createdUserSystemID, $allowedCreatedEmployeeIDs, true);
    }

    public function requireCanViewOrFail(GRVMaster $grvMaster, int $employeeSystemID): void
    {
        if (!$this->canViewGrv($grvMaster, $employeeSystemID)) {
            abort(403);
        }
    }

    public function requireCanEditOrFail(GRVMaster $grvMaster, int $employeeSystemID): void
    {
        if (!$this->canEditGrv($grvMaster, $employeeSystemID)) {
            abort(403);
        }
    }

    /**
     * @return array{R: bool, C: bool, E: bool, P: bool, Ex: bool}
     */
    public function getNavigationRights(int $companySystemID, int $employeeSystemID, ?int $navigationMenuID): array
    {
        if (empty($navigationMenuID)) {
            return ['R' => true, 'C' => true, 'E' => true, 'P' => true, 'Ex' => true];
        }

        $userGroupIDs = $this->repository->getNavigationUserGroupIDs($companySystemID, $employeeSystemID);

        if (empty($userGroupIDs)) {
            return ['R' => false, 'C' => false, 'E' => false, 'P' => false, 'Ex' => false];
        }

        $assignments = $this->repository->getNavigationAssignments($companySystemID, (int)$navigationMenuID, $userGroupIDs);

        if ($assignments->isEmpty()) {
            return ['R' => false, 'C' => false, 'E' => false, 'P' => false, 'Ex' => false];
        }
        return [
            'R' => (bool)$assignments->max('readonly'),
            'C' => (bool)$assignments->max('create'),
            'E' => (bool)$assignments->max('update'),
            'P' => (bool)$assignments->max('print'),
            'Ex' => (bool)$assignments->max('export'),
        ];
    }

    public function requireReadNavigationOrFail(Request $request, int $companySystemID, int $employeeSystemID): void
    {
        $navID = (int)$request->header('X-nav-ID', 0);
        $rights = $this->getNavigationRights($companySystemID, $employeeSystemID, $navID);
        if ($rights['R'] !== true) {
            abort(403);
        }
    }

    public function getUiModeForGrv(Request $request, GRVMaster $grvMaster, int $employeeSystemID): string
    {
        $navID = (int)$request->header('X-nav-ID', 0);
        $rights = $this->getNavigationRights((int)$grvMaster->companySystemID, $employeeSystemID, $navID);

        if ($rights['R'] !== true || !$this->canViewGrv($grvMaster, $employeeSystemID)) {
            return 'none';
        }

        if ($rights['E'] === true && $this->canEditGrv($grvMaster, $employeeSystemID)) {
            return 'edit';
        }

        return 'view';
    }

    public function applyViewScope(Builder $query, int $companySystemID, int $employeeSystemID): Builder
    {
        if (!$this->isRoleBasedAccessEnabledForCompany($companySystemID)) {
            return $query;
        }

        $toggles = $this->getOwnerToggles($companySystemID);

        if ($this->hasAdminFullView($toggles, $employeeSystemID)) {
            return $query;
        }

        $allowedCreatedEmployeeIDs = [];
        if ($toggles['creator_view'] === true) {
            $allowedCreatedEmployeeIDs[] = (int)$employeeSystemID;
        }
        $allowedCreatedEmployeeIDs = array_merge(
            $allowedCreatedEmployeeIDs,
            $this->createdEmployeeIdsForRmHod(
                $companySystemID,
                $employeeSystemID,
                $toggles['reporting_manager_view'] === true,
                $toggles['hod_view'] === true
            )
        );
        $allowedCreatedEmployeeIDs = array_values(array_unique(array_filter($allowedCreatedEmployeeIDs)));

        $allowedSegmentIDs = $toggles['segment_view'] === true
            ? $this->getAllowedSegmentIDsForView($companySystemID, $employeeSystemID)
            : [];
        $allowedWarehouseIDs = $toggles['warehouse_view'] === true
            ? $this->getAllowedWarehouseIDsForView($companySystemID, $employeeSystemID)
            : [];

        $query->where(function (Builder $union) use (
            $allowedCreatedEmployeeIDs,
            $allowedSegmentIDs,
            $allowedWarehouseIDs,
            $companySystemID,
            $employeeSystemID,
            $toggles
        ) {
            if (!empty($allowedCreatedEmployeeIDs)) {
                $union->orWhereIn('erp_grvmaster.createdUserSystemID', $allowedCreatedEmployeeIDs);
            }

            if ($toggles['approver_view'] === true) {
                $union->orWhereExists(function ($sub) use ($companySystemID, $employeeSystemID) {
                    $sub->selectRaw('1')
                        ->from('erp_documentapproved')
                        ->whereColumn('erp_documentapproved.documentSystemCode', 'erp_grvmaster.grvAutoID')
                        ->where('erp_documentapproved.companySystemID', $companySystemID)
                        ->where('erp_documentapproved.documentSystemID', self::GRV_DOCUMENT_SYSTEM_ID)
                        ->where('erp_documentapproved.employeeSystemID', $employeeSystemID);
                });
            }

            if (!empty($allowedSegmentIDs)) {
                $union->orWhereIn('erp_grvmaster.serviceLineSystemID', $allowedSegmentIDs);
            }

            if (!empty($allowedWarehouseIDs)) {
                $union->orWhereIn('erp_grvmaster.grvLocation', $allowedWarehouseIDs);
            }
        });

        return $query;
    }
}

