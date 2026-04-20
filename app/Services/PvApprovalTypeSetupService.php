<?php

namespace App\Services;

use App\Criteria\LimitOffsetCriteria;
use App\Models\ApprovalLevel;
use App\Models\PvApprovalTypeSetup;
use App\Repositories\PvApprovalTypeSetupRepository;
use App\Utils\ServiceResponse;
use Illuminate\Http\Request;
use Prettus\Repository\Criteria\RequestCriteria;

class PvApprovalTypeSetupService
{
    /** @var PvApprovalTypeSetupRepository */
    private $pvApprovalTypeSetupRepository;

    public function __construct(PvApprovalTypeSetupRepository $pvApprovalTypeSetupRepository)
    {
        $this->pvApprovalTypeSetupRepository = $pvApprovalTypeSetupRepository;
    }

    public function isDuplicateDescription(
        string $description,
        int $companySystemId,
        int $documentAttachmentId,
        ?int $ignoreId = null
    ): bool {
        $query = PvApprovalTypeSetup::query()
            ->where('company_system_id', $companySystemId)
            ->where('document_attachment_id', $documentAttachmentId)
            ->whereRaw('LOWER(TRIM(setup_description)) = ?', [mb_strtolower(trim($description))]);

        if ($ignoreId !== null) {
            $query->where('id', '!=', $ignoreId);
        }

        return $query->exists();
    }

    public function store(array $input): ServiceResponse {
        if ($this->isDuplicateDescription(
            $input['setup_description'] ?? '',
            (int) ($input['company_system_id']),
            (int) ($input['document_attachment_id'])
        )) {
            return ServiceResponse::failure(trans('custom.description_cannot_be_duplicated'));
        }

        if (!isset($input['is_amount_approval'])) {
            $input['is_amount_approval'] = 0;
        }

        if (isset($input['is_amount_approval'])) {
            $input['is_general_approval'] = (int) $input['is_amount_approval'] === 1 ? 0 : 1;
        }

        if (!isset($input['is_active'])) {
            $input['is_active'] = 0;
        }

        $setup = $this->pvApprovalTypeSetupRepository->create($input);

        return ServiceResponse::success($setup, 'PV approval type setup created successfully');
    }

    public function updatePvTypeBaseApprovalRow($id, array $validatedData): ServiceResponse {
        $setup = PvApprovalTypeSetup::where('id', $id)->first();

        if (empty($setup)) {
            return ServiceResponse::failure(trans('custom.pv_approval_type_setup_not_found'));
        }

        if (isset($validatedData['setup_description'])) {
            $companySystemId = $validatedData['company_system_id'];
            $documentAttachmentId = $validatedData['document_attachment_id'];

            if ($this->isDuplicateDescription(
                $validatedData['setup_description'],
                $companySystemId,
                $documentAttachmentId,
                (int) $setup->id
            )) {
                return ServiceResponse::failure(trans('custom.description_cannot_be_duplicated'));
            }
        }

        if (isset($validatedData['is_amount_approval'])) {
            $result = $this->checkApprovalLevelConflict($validatedData, $setup);
            if (!$result['status']) {
                return ServiceResponse::failure($result['message']);
            }
            else {
                $validatedData['is_general_approval'] = (int) $validatedData['is_amount_approval'] === 1 ? 0 : 1;
            }
        } 
        elseif (isset($validatedData['is_general_approval'])) {
            if ((int) $validatedData['is_general_approval'] === 1) {
                $result = $this->checkApprovalLevelConflict($validatedData, $setup);
                if (!$result['status']) {
                    return ServiceResponse::failure($result['message']);
                }
                else {
                    $validatedData['is_amount_approval'] = 0;
                }
            } 
            elseif ((int) $setup->is_amount_approval === 0) {
                $result = $this->checkApprovalLevelConflict($validatedData, $setup);
                if (!$result['status']) {
                    return ServiceResponse::failure($result['message']);
                }
                else {
                    $validatedData['is_general_approval'] = 1;
                }
            }

        }

        if (isset($validatedData['is_active'])) {
            if ((int) $validatedData['is_active'] === 1) {
                $result = $this->checkApprovalLevelConflict($validatedData, $setup);
                if (!$result['status']) {
                    return ServiceResponse::failure($result['message']);
                }
            }
        }

        $setup->fill($validatedData);
        $setup->save();

        return ServiceResponse::success($setup, 'PV approval type setup updated successfully');
    }

    public function deletePvTypeBaseApproval($id): ServiceResponse {
        $setup = PvApprovalTypeSetup::where('id', $id)->first();

        if (empty($setup)) {
            return ServiceResponse::failure(trans('custom.pv_approval_type_setup_not_found'));
        }

        $approvalLevels = ApprovalLevel::where('companySystemID', $setup->company_system_id)
            ->where('pvTypeWise', 1)
            ->where('pvTypeSetupID', $id)
            ->where('is_deleted', 0)
            ->get();

        if (count($approvalLevels) > 0) {
            return ServiceResponse::failure(trans('custom.pv_approval_type_setup_has_approval_levels'));
        }

        $setup->delete();

        return ServiceResponse::success($setup, trans('custom.pv_approval_type_setup_deleted_successfully'));
    }

    private function checkApprovalLevelConflict($validatedData, $setup): array {
        $companySystemId = $validatedData['company_system_id'];
        $documentAttachmentId = $validatedData['document_attachment_id'];

        if ($validatedData['is_amount_approval'] || $validatedData['is_general_approval']) {
            $activeLevel = ApprovalLevel::where('companySystemID', $companySystemId)
                ->where('documentSystemID', 4)
                ->where('pvTypeWise', 1)
                ->where('pvTypeSetupID', $setup->id)
                ->where('isActive', -1)
                ->first();

            if ($activeLevel) {
                return [
                    'status' => false,
                    'message' => trans('custom.there_is_an_approval_level_created_for_this_docume'),
                ];
            }
        }

        $typeValues = $this->getPvTypeFlagValues($validatedData, $setup);
        $query = PvApprovalTypeSetup::where('company_system_id', $companySystemId)
            ->where('document_attachment_id', $documentAttachmentId)
            ->where('is_active', 1)
            ->where('id', '!=', (int) $setup->id);

        $selectedTypeColumns = array_keys(array_filter($typeValues, function ($value) {
            return (int) $value === 1;
        }));

        if (empty($selectedTypeColumns)) {
            $conflictingSetup = null;
        }
        else {
            // If any selected type already exists in another active setup, it's a conflict.
            $query->where(function ($q) use ($selectedTypeColumns) {
                foreach ($selectedTypeColumns as $column) {
                    $q->orWhere($column, 1);
                }
            });

            $conflictingSetup = $query->first();
        }

        if (!empty($conflictingSetup)) {
            $existingActiveSetupDescription = $conflictingSetup->setup_description;
            return [
                'status' => false,
                'message' => trans('custom.pv_type_setup_active_conflict', ['description' => $existingActiveSetupDescription]),
            ];
        }
        else {
            return [
                'status' => true,
                'message' => 'No conflict found',
            ];
        }
    }

    private function getPvTypeFlagValues($data, $fallbackSetup = null): array {
        $values = [];

        foreach (PvApprovalTypeSetup::TYPE_FINGERPRINT_COLUMNS as $column) {
            if (array_key_exists($column, $data)) {
                $values[$column] = (int) $data[$column];
            } 
            elseif (!empty($fallbackSetup) && isset($fallbackSetup->{$column})) {
                $values[$column] = (int) $fallbackSetup->{$column};
            } 
            else {
                $values[$column] = 0;
            }
        }

        return $values;
    }

    public static function getPVDocumentTypeForApproval($masterRec): array {
        $invoiceType = $masterRec->invoiceType ?? null;
        $expenseClaimOrPettyCash = $masterRec->expenseClaimOrPettyCash ?? null;

        $invoiceTypeColumnName = null;
        $expenseClaimOrPettyCashColumnName = null;

        switch ($invoiceType) {
            case 2:
                $invoiceTypeColumnName = 'is_supplier_payment';
                break;
            case 3:
                $invoiceTypeColumnName = 'is_direct_payment_general';
                switch ($expenseClaimOrPettyCash) {
                    case 1:
                        $expenseClaimOrPettyCashColumnName = 'is_expense_claim';
                        break;
                    case 2:
                        $expenseClaimOrPettyCashColumnName = 'is_petty_cash';
                        break;
                    case 3:
                        $expenseClaimOrPettyCashColumnName = 'is_cash';
                        break;
                    case 6:
                        $expenseClaimOrPettyCashColumnName = 'is_inter_company_funds_transfer';
                        break;
                    case 7:
                        $expenseClaimOrPettyCashColumnName = 'is_collection_on_behalf';
                        break;
                    case 15:
                        $expenseClaimOrPettyCashColumnName = 'is_inter_bank_account_transfer';
                        break;
                    default:
                        $expenseClaimOrPettyCashColumnName = null;
                        break;
                }

                if (is_null($expenseClaimOrPettyCashColumnName)) {
                    if ((in_array($masterRec->finalSettlementYN, [1,5])) && ($masterRec->partyTblID != 0)) {
                        if ($masterRec->finalSettlementYN == 1) {
                            $expenseClaimOrPettyCashColumnName = 'is_salary_transfer';
                        }
                        else {
                            $expenseClaimOrPettyCashColumnName = 'is_iou_voucher';
                        }
                    }
                }

                break;
            case 5:
                $invoiceTypeColumnName = 'is_supplier_advance_payment';
                break;
            case 6:
                $invoiceTypeColumnName = 'is_employee_payment';
                break;
            case 7:
                $invoiceTypeColumnName = 'is_employee_advance_payment';
                break;
            case 8:
                $invoiceTypeColumnName = 'is_refund';
                break;
            default:
                $invoiceTypeColumnName = null;
                break;
        }

        return [
            'invoiceTypeColumnName' => $invoiceTypeColumnName,
            'expenseClaimOrPettyCashColumnName' => $expenseClaimOrPettyCashColumnName,
        ];
    }

    public static function checkApprovalLevelExists($data): ServiceResponse {
        $companySystemId = $data['company_system_id'];
        $levelType = $data['level_type'];

        $query = ApprovalLevel::where('companySystemID', $companySystemId)
            ->where('documentSystemID', 4)
            ->where('isActive', -1)
            ->when($levelType == 'common_setup', function ($query) {
                $query->where('pvTypeWise', 0);
            })
            ->when($levelType == 'type_based_setup', function ($query) {
                $query->where('pvTypeWise', 1);
            })
            ->exists();

        if ($query) {
            return ServiceResponse::failure(trans('custom.there_is_an_approval_level_created_for_this_docume'));
        }
        else {
            return ServiceResponse::success(null, trans('custom.approval_level_does_not_exist'));
        }
    }
}

