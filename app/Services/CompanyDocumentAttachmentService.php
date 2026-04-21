<?php

namespace App\Services;

use App\Models\ApprovalLevel;
use App\Models\CompanyDocumentAttachment;

class CompanyDocumentAttachmentService
{
    private const PO_DOCUMENT_SYSTEM_ID = 2;
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
     *
     * @param CompanyDocumentAttachment $companyDocumentAttachment
     * @param array $input
     * @return array{allowed: bool, message?: string, status?: int}
     */
    public function validateApprovalConfigChange(CompanyDocumentAttachment $companyDocumentAttachment, array $input): array
    {
        $approvalChanged = $companyDocumentAttachment->isServiceLineApproval != ($input['isServiceLineApproval'] ?? $companyDocumentAttachment->isServiceLineApproval)
            || $companyDocumentAttachment->isAmountApproval != ($input['isAmountApproval'] ?? $companyDocumentAttachment->isAmountApproval)
            || $companyDocumentAttachment->isCategoryApproval != ($input['isCategoryApproval'] ?? $companyDocumentAttachment->isCategoryApproval)
            || (isset($input['isPRTypeApproval']) && $companyDocumentAttachment->isPRTypeApproval != $input['isPRTypeApproval'])
            || (isset($input['isAttachmentApproval']) && $companyDocumentAttachment->isAttachmentApproval != $input['isAttachmentApproval'])
            || (isset($input['isSubcategoryApproval']) && $companyDocumentAttachment->isSubcategoryApproval != $input['isSubcategoryApproval']);

        if (!$approvalChanged) {
            return ['allowed' => true];
        }

        $activeLevel = ApprovalLevel::where('companySystemID', $companyDocumentAttachment->companySystemID)
            ->where('documentSystemID', $companyDocumentAttachment->documentSystemID)
            ->where('isActive', -1)
            ->first();

        if ($activeLevel) {
            return [
                'allowed' => false,
                'message' => trans('custom.there_is_an_approval_level_created_for_this_docume'),
                'status' => self::ERROR_STATUS,
            ];
        }

        return ['allowed' => true];
    }

    /**
     * @param CompanyDocumentAttachment $companyDocumentAttachment
     * @param array $input
     * @return array{valid: bool, input?: array, status?: int, message?: string, errors?: array}
     */
    public function validatePoAttachmentApprovalUpdate(CompanyDocumentAttachment $companyDocumentAttachment, array $input): array
    {
        if ((int) $companyDocumentAttachment->documentSystemID !== self::PO_DOCUMENT_SYSTEM_ID) {
            return ['valid' => true, 'input' => $input];
        }

        $currentAttachmentApproval = $companyDocumentAttachment->isAttachmentApproval ?? 0;
        $requestedAttachmentApproval = $input['isAttachmentApproval'] ?? $currentAttachmentApproval;
        $attachmentApprovalEnabled = self::isApprovalEnabled($requestedAttachmentApproval);
        if ($attachmentApprovalEnabled) {
            $input['enableAttachmentAfterApproval'] = 0;
        }

        return ['valid' => true, 'input' => $input];
    }

    /**
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
