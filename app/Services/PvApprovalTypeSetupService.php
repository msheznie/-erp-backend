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
            else {
                $companySystemId = $validatedData['company_system_id'];
                $activeLevel = ApprovalLevel::where('companySystemID', $companySystemId)
                    ->where('documentSystemID', 4)
                    ->where('pvTypeWise', 1)
                    ->where('pvTypeSetupID', $id)
                    ->where('isActive', -1)
                    ->first();

                if ($activeLevel) {
                    return ServiceResponse::failure(trans('custom.there_is_an_approval_level_created_for_this_docume'));
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

    private function checkApprovalLevelConflict($validatedData, $setup) {
        $companySystemId = $validatedData['company_system_id'];
        $documentAttachmentId = $validatedData['document_attachment_id'];

        $typeValues = $this->getPvTypeFlagValues($validatedData, $setup);
        $query = PvApprovalTypeSetup::where('company_system_id', $companySystemId)
            ->where('document_attachment_id', $documentAttachmentId)
            ->where('is_active', 1)
            ->where('id', '!=', (int) $setup->id);

        foreach ($typeValues as $column => $value) {
            $query->where($column, $value);
        }

        $conflictingSetup = $query->first();

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

    private function getPvTypeFlagValues($data, $fallbackSetup = null): array
    {
        $columns = [
            'is_amount_approval',
            'is_general_approval',
            'is_supplier_payment',
            'is_supplier_advance_payment',
            'is_employee_payment',
            'is_employee_advance_payment',
            'is_direct_payment_general',
            'is_iou_voucher',
            'is_salary_transfer',
            'is_expense_claim',
            'is_petty_cash',
            'is_cash',
            'is_inter_company_funds_transfer',
            'is_collection_on_behalf',
            'is_inter_bank_account_transfer',
        ];

        $values = [];

        foreach ($columns as $column) {
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
}

