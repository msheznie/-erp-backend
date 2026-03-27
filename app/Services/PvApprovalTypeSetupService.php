<?php

namespace App\Services;

use App\Criteria\LimitOffsetCriteria;
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
            $validatedData['is_general_approval'] = (int) $validatedData['is_amount_approval'] === 1 ? 0 : 1;
        } 
        elseif (isset($validatedData['is_general_approval'])) {
            if ((int) $validatedData['is_general_approval'] === 1) {
                $validatedData['is_amount_approval'] = 0;
            } 
            elseif ((int) $setup->is_amount_approval === 0) {
                $validatedData['is_general_approval'] = 1;
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

        $setup->delete();

        return ServiceResponse::success($setup, trans('custom.pv_approval_type_setup_deleted_successfully'));
    }
}

