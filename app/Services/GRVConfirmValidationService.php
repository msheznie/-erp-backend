<?php

namespace App\Services;

use App\Models\ApprovalLevel;
use App\Models\CompanyDocumentAttachment;
use App\Models\GRVDetails;
use App\Services\CompanyDocumentAttachmentService;

class GRVConfirmValidationService
{
    private const GRV_DOCUMENT_SYSTEM_ID = 3;

    /**
     *
     * @param int $companySystemID
     * @param int $grvAutoID
     * @return array{valid: bool, message?: string}
     */
    public function validateForConfirmation(int $companySystemID, int $grvAutoID): array
    {
        $docConfig = CompanyDocumentAttachment::where('companySystemID', $companySystemID)
            ->where('documentSystemID', self::GRV_DOCUMENT_SYSTEM_ID)
            ->first();

        if (!$docConfig) {
            return ['valid' => true];
        }

        $isSubcategoryApproval = CompanyDocumentAttachmentService::isApprovalEnabled($docConfig->isSubcategoryApproval ?? 0);
        if (!$isSubcategoryApproval) {
            return ['valid' => true];
        }

        $rule1 = $this->validateSingleCategoryType($grvAutoID);
        if (!$rule1['valid']) {
            return $rule1;
        }

        $rule2 = $this->validateSingleSubcategory($grvAutoID);
        if (!$rule2['valid']) {
            return $rule2;
        }

        $rule3 = $this->validateActiveApprovalSetupExists($companySystemID, $grvAutoID, $docConfig);
        if (!$rule3['valid']) {
            return $rule3;
        }

        return ['valid' => true];
    }

    /**
     *
     * @param int $grvAutoID
     * @return array{valid: bool, message?: string}
     */
    public function validateSingleCategoryType(int $grvAutoID): array
    {
        $count = (int) GRVDetails::where('grvAutoID', $grvAutoID)
            ->selectRaw('COUNT(DISTINCT itemFinanceCategoryID) as cnt')
            ->value('cnt');

        if ($count > 1) {
            return [
                'valid' => false,
                'message' => trans('custom.grv_mixed_category_types'),
            ];
        }

        return ['valid' => true];
    }

    /**
     *
     * @param int $grvAutoID
     * @return array{valid: bool, message?: string}
     */
    public function validateSingleSubcategory(int $grvAutoID): array
    {
        $count = (int) GRVDetails::where('grvAutoID', $grvAutoID)
            ->whereNotNull('itemFinanceCategorySubID')
            ->selectRaw('COUNT(DISTINCT itemFinanceCategorySubID) as cnt')
            ->value('cnt');

        if ($count > 1) {
            return [
                'valid' => false,
                'message' => trans('custom.grv_multiple_subcategories'),
            ];
        }

        return ['valid' => true];
    }

    /**
     *
     * @param int $companySystemID
     * @param int $grvAutoID
     * @param \App\Models\CompanyDocumentAttachment $docConfig
     * @return array{valid: bool, message?: string}
     */
    public function validateActiveApprovalSetupExists(int $companySystemID, int $grvAutoID, CompanyDocumentAttachment $docConfig): array
    {
        $subcategoryID = $this->getSingleSubcategoryFromGrv($grvAutoID);
        $categoryID = $this->getSingleCategoryFromGrv($grvAutoID);

        $query = ApprovalLevel::where('companySystemID', $companySystemID)
            ->where('documentSystemID', self::GRV_DOCUMENT_SYSTEM_ID)
            ->where('isActive', -1);

        if (CompanyDocumentAttachmentService::isApprovalEnabled($docConfig->isSubcategoryApproval ?? 0) && $subcategoryID) {
            $query->where('subcategoryID', $subcategoryID);
        }

        if (CompanyDocumentAttachmentService::isApprovalEnabled($docConfig->isCategoryApproval ?? 0) && $categoryID) {
            $query->where('categoryID', $categoryID)->where('isCategoryWiseApproval', -1);
        }

        if (!$query->exists()) {
            return [
                'valid' => false,
                'message' => trans('custom.no_approval_setup_created'),
            ];
        }

        return ['valid' => true];
    }

    /**
     *
     * @param int $grvAutoID
     * @param bool $isSubcategoryApproval
     * @return array{category: int|null, subCategory: int|null}
     */
    public function resolveCategoryAndSubcategoryForParams(int $grvAutoID, bool $isSubcategoryApproval): array
    {
        $detail = GRVDetails::where('grvAutoID', $grvAutoID)
            ->select('itemFinanceCategoryID', 'itemFinanceCategorySubID')
            ->first();

        if (!$detail) {
            return ['category' => null, 'subCategory' => null];
        }

        $category = $detail->itemFinanceCategoryID ? (int) $detail->itemFinanceCategoryID : null;
        $subCategory = $isSubcategoryApproval && $detail->itemFinanceCategorySubID
            ? (int) $detail->itemFinanceCategorySubID
            : null;

        return ['category' => $category, 'subCategory' => $subCategory];
    }

    private function getSingleSubcategoryFromGrv(int $grvAutoID): ?int
    {
        $ids = GRVDetails::where('grvAutoID', $grvAutoID)
            ->whereNotNull('itemFinanceCategorySubID')
            ->distinct()
            ->pluck('itemFinanceCategorySubID');

        return $ids->count() === 1 ? (int) $ids->first() : null;
    }

    private function getSingleCategoryFromGrv(int $grvAutoID): ?int
    {
        $ids = GRVDetails::where('grvAutoID', $grvAutoID)
            ->whereNotNull('itemFinanceCategoryID')
            ->distinct()
            ->pluck('itemFinanceCategoryID');

        return $ids->count() === 1 ? (int) $ids->first() : null;
    }
}
