<?php

namespace App\Services;

use App\Models\ApprovalLevel;
use App\Models\ApprovalRole;
use App\Models\CompanyDocumentAttachment;

class ApprovalLevelService
{
    private const GRV_DOCUMENT_SYSTEM_ID = 3;

    /**
     *
     * @param int $companySystemID
     * @param int $documentSystemID
     * @param array $input
     * @return array{valid: bool, message?: string}
     */
    public function validateGrvSubcategoryRequired(int $companySystemID, int $documentSystemID, array $input): array
    {
        if ((int) $documentSystemID !== self::GRV_DOCUMENT_SYSTEM_ID) {
            return ['valid' => true];
        }

        $docConfig = CompanyDocumentAttachment::where('companySystemID', $companySystemID)
            ->where('documentSystemID', $documentSystemID)
            ->first();

        if (!$docConfig) {
            return ['valid' => true];
        }

        $categoryEnabled = CompanyDocumentAttachmentService::isApprovalEnabled($docConfig->isCategoryApproval ?? 0);
        $subcategoryEnabled = CompanyDocumentAttachmentService::isApprovalEnabled($docConfig->isSubcategoryApproval ?? 0);

        if (!$categoryEnabled || !$subcategoryEnabled) {
            return ['valid' => true];
        }

        $categoryWise = !empty($input['isCategoryWiseApproval']) || (isset($input['isCategoryWiseApproval']) && $input['isCategoryWiseApproval'] == -1);
        if (!$categoryWise) {
            return ['valid' => true];
        }

        $subcategoryID = $input['subcategoryID'] ?? null;
        if (empty($subcategoryID)) {
            return [
                'valid' => false,
                'message' => trans('custom.subcategory_is_required_for_grv_approval'),
            ];
        }

        return ['valid' => true];
    }

    /**
     * Block update if roles are assigned to this approval level.
     *
     * @param int $approvalLevelID
     * @return array{allowed: bool, message?: string}
     */
    public function validateEditAllowed(int $approvalLevelID): array
    {
        $hasRoles = ApprovalRole::where('approvalLevelID', $approvalLevelID)->exists();
        if ($hasRoles) {
            return [
                'allowed' => false,
                'message' => trans('custom.rolls_created_cannot_edit'),
            ];
        }
        return ['allowed' => true];
    }

    /**
     * Check if any active approval level uses this category (categoryID).
     *
     * @param int $categoryID
     * @return bool
     */
    public function isCategoryUsedInActiveApprovalLevel(int $categoryID): bool
    {
        return ApprovalLevel::where('isActive', -1)
            ->where('categoryID', $categoryID)
            ->exists();
    }

    /**
     * Check if any active approval level uses this subcategory (subcategoryID).
     *
     * @param int $subcategoryID
     * @return bool
     */
    public function isSubcategoryUsedInActiveApprovalLevel(int $subcategoryID): bool
    {
        return ApprovalLevel::where('isActive', -1)
            ->where('subcategoryID', $subcategoryID)
            ->exists();
    }

    /**
     * Check if any active approval level uses this subcategory for this company.
     *
     * @param int $subcategoryID
     * @param int $companySystemID
     * @return bool
     */
    public function isSubcategoryUsedInActiveApprovalLevelForCompany(int $subcategoryID, int $companySystemID): bool
    {
        return ApprovalLevel::where('isActive', -1)
            ->where('subcategoryID', $subcategoryID)
            ->where('companySystemID', $companySystemID)
            ->exists();
    }
}
