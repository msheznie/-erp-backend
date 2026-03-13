<?php

namespace App\Services;

use App\Models\CompanyDocumentAttachment;

class CompanyDocumentAttachmentService
{
    private const GRV_DOCUMENT_SYSTEM_ID = 3;
    private const ERROR_STATUS = 500;

    /**
     * @param CompanyDocumentAttachment $companyDocumentAttachment
     * @param array $input
     * @return array{valid: bool, input?: array, status?: int, message?: string, errors?: array}
     */
    public function validateAndNormalizeGrvApprovalUpdate(CompanyDocumentAttachment $companyDocumentAttachment, array $input): array
    {
        if ((int) $companyDocumentAttachment->documentSystemID !== self::GRV_DOCUMENT_SYSTEM_ID) {
            return ['valid' => true, 'input' => $input];
        }

        $inputCategory = $input['isCategoryApproval'] ?? $companyDocumentAttachment->isCategoryApproval;
        $inputSubcategory = $input['isSubcategoryApproval'] ?? $companyDocumentAttachment->isSubcategoryApproval;
        $categoryEnabled = self::isApprovalEnabled($inputCategory);
        $subcategoryEnabled = self::isApprovalEnabled($inputSubcategory);
        $categoryWasOn = self::isApprovalEnabled($companyDocumentAttachment->isCategoryApproval);

        $userDisablingCategory = array_key_exists('isCategoryApproval', $input)
            && !self::isApprovalEnabled($input['isCategoryApproval']);

        if ($userDisablingCategory && $categoryWasOn && $subcategoryEnabled) {
            return [
                'valid' => false,
                'status' => self::ERROR_STATUS,
                'message' => trans('custom.disable_subcategory_before_category'),
                'errors' => ['isCategoryApproval' => [trans('custom.disable_subcategory_before_category')]],
            ];
        }

        if ($subcategoryEnabled && !$categoryEnabled) {
            $input['isSubcategoryApproval'] = 0;
            return [
                'valid' => false,
                'status' => self::ERROR_STATUS,
                'message' => trans('custom.subcategory_approval_requires_category_enabled'),
                'errors' => ['isSubcategoryApproval' => [trans('custom.subcategory_approval_requires_category_enabled')]],
            ];
        }

        if (!$categoryEnabled) {
            $input['isSubcategoryApproval'] = 0;
        }

        return ['valid' => true, 'input' => $input];
    }

    /**
     * Whether the given value is considered "approval enabled" (truthy).
     *
     * @param mixed $value
     * @return bool
     */
    public static function isApprovalEnabled($value): bool
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
        return false;
    }
}
