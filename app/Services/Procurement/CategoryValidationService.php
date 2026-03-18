<?php

namespace App\Services\Procurement;

use App\Models\CompanyDocumentAttachment;
use App\Models\CompanyPolicyMaster;

class CategoryValidationService
{
    /**
     * Policy 20 = "Allow item with different categories in Request, Order and GRV"
     */
    private const POLICY_CATEGORY_ALLOW_DIFFERENT_CATEGORIES = 20;

    /**
     * Whether the system must enforce a single item category per document.
     * Main goal: When isCategoryApproval is enabled, override Policy 20 and do NOT allow adding a different category item
     * (even if Policy 20 "Allow different categories" is enabled, i.e. isYesNO = 1).
     * True when: Category-Based Approval (isCategoryApproval) is enabled for the document, OR policy 20 restricts (isYesNO = 0).
     */
    public static function shouldEnforceSingleCategory(int $companySystemID, int $documentSystemID): bool
    {
        $docAttachment = CompanyDocumentAttachment::query()
            ->where('companySystemID', $companySystemID)
            ->where('documentSystemID', $documentSystemID)
            ->first();

        if ($docAttachment?->isCategoryApproval) {
            return true;
        }

        $policy = CompanyPolicyMaster::query()
            ->where('companyPolicyCategoryID', self::POLICY_CATEGORY_ALLOW_DIFFERENT_CATEGORIES)
            ->where('companySystemID', $companySystemID)
            ->first();

        return $policy && (int) $policy->isYesNO === 0;
    }

    /**
     * Message to return when adding an item of a different category is blocked.
     * Uses category_approval_mixed_category_blocked when Category-Based Approval is the reason.
     */
    public static function getCategoryRestrictionMessage(int $companySystemID, int $documentSystemID): string
    {
        $docAttachment = CompanyDocumentAttachment::query()
            ->where('companySystemID', $companySystemID)
            ->where('documentSystemID', $documentSystemID)
            ->first();

        if ($docAttachment?->isCategoryApproval) {
            return trans('custom.category_approval_mixed_category_blocked');
        }

        return trans('custom.you_cannot_add_different_category_item');
    }

    public static function isCategoryApprovalEnabled(int $companySystemID, int $documentSystemID): bool
    {
        $docAttachment = CompanyDocumentAttachment::query()
            ->where('companySystemID', $companySystemID)
            ->where('documentSystemID', $documentSystemID)
            ->first();

        return (bool) ($docAttachment?->isCategoryApproval ?? false);
    }
}
