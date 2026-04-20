<?php

namespace App\Services;

use App\Models\ApprovalLevel;
use App\Models\ApprovalRole;
use App\Models\CompanyDocumentAttachment;
use App\Models\FinanceItemcategorySubAssigned;
use App\Models\PvApprovalTypeSetup;

class ApprovalLevelService
{
    private const GRV_DOCUMENT_SYSTEM_ID = 3;
    private const PAYMENT_VOUCHER_DOCUMENT_SYSTEM_ID = 4;

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
        return ApprovalLevel::where('is_deleted', 0)
            ->where('subcategoryID', $subcategoryID)
            ->where('companySystemID', $companySystemID)
            ->exists();
    }

    /**
     * Get assigned and active subcategories for a company + main category.
     *
     * @param int $companySystemID
     * @param int $categoryID
     * @return array<int, array{value:int,label:string}>
     */
    public function getAssignedActiveSubcategories(int $companySystemID, int $categoryID): array
    {
        $assigned = FinanceItemcategorySubAssigned::where('companySystemID', $companySystemID)
            ->where('mainItemCategoryID', $categoryID)
            ->where('isAssigned', -1)
            ->where('isActive', 1)
            ->whereHas('finance_item_category_sub', function ($q) {
                $q->where('isActive', 1);
            })
            ->with('finance_item_category_sub:itemCategorySubID,categoryDescription')
            ->get();

        return $assigned->map(function ($row) {
            return [
                'value' => $row->itemCategorySubID,
                'label' => $row->finance_item_category_sub ? $row->finance_item_category_sub->categoryDescription : $row->categoryDescription,
            ];
        })->values()->toArray();
    }

    public function validatePaymentVoucherActivation(ApprovalLevel $level, $input): array
    {
        $pvTypeWise = $level->pvTypeWise;
        $companySystemID = $level->companySystemID;
        $currentId = $level->approvalLevelID;

        if ($pvTypeWise == 0) {
            // Common approval level
            $query = ApprovalLevel::where('companySystemID', $companySystemID)
                ->where('documentSystemID', self::PAYMENT_VOUCHER_DOCUMENT_SYSTEM_ID)
                ->where('pvTypeWise', 0)
                ->where('isActive', -1)
                ->where('approvalLevelID', '!=', $currentId);

            $this->constrainQueryToSamePaymentVoucherValueBand($query, $level);

            if ($query->exists()) {
                return ['status' => false, 'message' => trans('custom.approval_level_already_exists')];
            }

            $documentConf = CompanyDocumentAttachment::where('companySystemID', $companySystemID)
                ->where('documentSystemID', self::PAYMENT_VOUCHER_DOCUMENT_SYSTEM_ID)
                ->first();

            if ($documentConf) {
                $valueWise = isset($input['valueWise']) && ($input['valueWise'] || $input['valueWise'] == 1) ? -1 : 0;

                if ($valueWise != $documentConf->isAmountApproval) {
                    return ['status' => false, 'message' => trans('custom.approval_level_criteria_differ')];
                }

                $approvalTypeSetups = PvApprovalTypeSetup::where('document_attachment_id', $documentConf->companyDocumentAttachmentID)
                    ->where('company_system_id', $companySystemID)
                    ->where('is_active', 1)
                    ->exists();

                if ($approvalTypeSetups) {
                    return ['status' => false, 'message' => trans('custom.approval_level_criteria_differ')];
                }
            }

            // check if any type-based approval level exists for this company
            $query2 = ApprovalLevel::where('companySystemID', $companySystemID)
                ->where('documentSystemID', self::PAYMENT_VOUCHER_DOCUMENT_SYSTEM_ID)
                ->where('pvTypeWise', 1)
                ->where('isActive', -1)
                ->where('approvalLevelID', '!=', $currentId)
                ->exists();

            if ($query2) {
                return ['status' => false, 'message' => trans('custom.type_based_approval_level_already_exists')];
            }
        }
        else {
            // Type-based approval level
            $pvTypeSetupID = $level->pvTypeSetupID;

            $query = ApprovalLevel::where('companySystemID', $companySystemID)
                ->where('documentSystemID', self::PAYMENT_VOUCHER_DOCUMENT_SYSTEM_ID)
                ->where('pvTypeWise', 1)
                ->where('isActive', -1)
                ->where('approvalLevelID', '!=', $currentId)
                ->where('pvTypeSetupID', $pvTypeSetupID);

            $this->constrainQueryToSamePaymentVoucherValueBand($query, $level);

            if ($query->exists()) {
                return ['status' => false, 'message' => trans('custom.approval_level_already_exists')];
            }

            $valueWise = isset($input['valueWise']) && ($input['valueWise'] || $input['valueWise'] == 1) ? 1 : 0;

            $documentConf = CompanyDocumentAttachment::where('companySystemID', $companySystemID)
                ->where('documentSystemID', self::PAYMENT_VOUCHER_DOCUMENT_SYSTEM_ID)
                ->first();

            if ($documentConf) {
                $pvTypeSetups = PvApprovalTypeSetup::where('document_attachment_id', $documentConf->companyDocumentAttachmentID)
                    ->where('company_system_id', $companySystemID)
                    ->where('is_active', 1)
                    ->where('is_amount_approval', $valueWise)
                    ->where('id', $pvTypeSetupID);

                if (!$pvTypeSetups->exists()) {
                    return ['status' => false, 'message' => trans('custom.approval_level_criteria_differ')];
                }

                $approvalTypeSetups = PvApprovalTypeSetup::where('document_attachment_id', $documentConf->companyDocumentAttachmentID)
                    ->where('company_system_id', $companySystemID)
                    ->where('is_active', 0)
                    ->exists();

                if ($approvalTypeSetups) {
                    return ['status' => false, 'message' => trans('custom.approval_level_criteria_differ')];
                }
            }

            $query2 = ApprovalLevel::where('companySystemID', $companySystemID)
                ->where('documentSystemID', self::PAYMENT_VOUCHER_DOCUMENT_SYSTEM_ID)
                ->where('pvTypeWise', 0)
                ->where('isActive', -1)
                ->where('approvalLevelID', '!=', $currentId)
                ->exists();

            if ($query2) {
                return ['status' => false, 'message' => trans('custom.common_approval_level_already_exists')];
            }
        }

        return ['status' => true];
    }

    private function constrainQueryToSamePaymentVoucherValueBand($query, ApprovalLevel $level): void
    {
        $valueWise = $level->valueWise;
        $valueFrom = $level->valueFrom;
        $valueTo = $level->valueTo;

        if ($valueWise == 0) {
            $query->where('valueWise', 0)
                ->where('valueFrom', 0)
                ->where('valueTo', 0);
        } 
        else {
            $query->where('valueWise', 1)
                ->where('valueFrom', $valueFrom)
                ->where('valueTo', $valueTo);
        }
    }
}
