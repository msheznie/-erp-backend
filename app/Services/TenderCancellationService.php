<?php

namespace App\Services;

use App\Constants\TenderConstants;
use App\helper\email as Email;
use App\Models\Company;
use App\Models\SrmItemWiseTenderAwarding;
use App\Models\TenderCancellation;
use App\Models\TenderMaster;
use App\Models\TenderSupplierAssignee;
use App\helper\Helper;
use App\helper\Workflow\DocumentConfirm;
use Illuminate\Support\Facades\DB;
use App\Services\WebPushNotificationService;

class TenderCancellationService
{
    public function createCancellationRequest(array $input): array
    {
        $data = $this->normalizeCreateInput($input);
        if ($data === null) {
            return ['success' => false, 'code' => 422, 'message' => trans('srm_tender_rfx.cancellation_validation_required')];
        }

        $tender = TenderMaster::getByIdAndCompany($data['tender_id'], $data['company_id']);
        $validation = $this->validateTenderForCancellation($tender, $data['tender_id']);
        if ($validation !== null) {
            return $validation;
        }

        if (TenderCancellation::getPendingApprovalRequest($data['tender_id'], $data['company_id'])) {
            return ['success' => false, 'code' => 422, 'message' => trans('srm_tender_rfx.cancellation_request_already_pending')];
        }

        return $this->createAndConfirmRequest($tender, $data);
    }

    public function finalizeIfApproved(int $cancellationId): array
    {
        $cancellation = TenderCancellation::getByIdWithTender($cancellationId);
        if (!$cancellation) {
            return ['success' => false, 'code' => 404, 'message' => trans('srm_tender_rfx.cancellation_record_not_found')];
        }

        if ((int) $cancellation->approved !== -1 || !$cancellation->tender) {
            return ['success' => true, 'message' => trans('srm_tender_rfx.cancellation_no_finalization_required')];
        }
        if ((int) $cancellation->tender->cancelled_yn === 1) {
            return ['success' => true, 'message' => trans('srm_tender_rfx.cancellation_already_finalized')];
        }

        return $this->finalizeCancellation($cancellation);
    }

    public function getCancellationStatus(int $tenderId, int $companyId): array
    {
        $record = TenderCancellation::getLatestByTenderAndCompany($tenderId, $companyId);
        return [
            'has_request' => (bool) $record,
            'status' => $record ? $this->mapStatus($record) : null,
        ];
    }

    public function notifySuppliersAfterFinalApproval(TenderMaster $tender, string $externalComment): void
    {
        if ((int) $tender->published_yn !== 1) {
            return;
        }

        $suppliers = TenderSupplierAssignee::getCancellationNotificationSuppliers(
            (int) $tender->id,
            (int) $tender->company_id
        );
        $subject = $this->buildSubject($tender);
        $body = $this->buildDefaultBody($tender, $externalComment);

        foreach ($suppliers as $supplier) {
            Email::sendEmailSRM([
                'empEmail' => $supplier->supplier_email,
                'companySystemID' => $tender->company_id,
                'alertMessage' => $subject,
                'emailAlertMessage' => $body,
            ]);
            $this->sendWebPush($supplier->registration_link_id, $tender->tender_code, $tender);
        }
    }

    public function sendScheduleAndItemwiseSupplierEmail(TenderMaster $tender, string $supplierComment): void
    {
        if (!$this->isScheduleOrItemWise($tender)) {
            return;
        }
        $company= Company::find($input['companySystemID']);
        $companyName = $company->CompanyName;

        $subject = $this->buildSubject($tender);
        $body = view('email.tender_cancellation_supplier_notice', [
            'tenderCode' => $tender->tender_code,
            'tenderTitle' => $tender->title,
            'tenderDescription' => $tender->description,
            'supplierComment' => $supplierComment,
            'companyName' => $companyName,
        ])->render();

        $suppliers = TenderSupplierAssignee::getCancellationNotificationSuppliers((int) $tender->id, (int) $tender->company_id);
        foreach ($suppliers as $supplier) {
            Email::sendEmailSRM([
                'empEmail' => $supplier->supplier_email,
                'companySystemID' => $tender->company_id,
                'alertMessage' => $subject,
                'emailAlertMessage' => $body,
            ]);
        }
    }

    private function normalizeCreateInput(array $input): ?array
    {
        $data = [
            'tender_id' => (int) ($input['tender_id'] ?? 0),
            'company_id' => (int) ($input['company_id'] ?? 0),
            'internal_comment' => trim((string) ($input['internal_comment'] ?? '')),
            'external_comment' => trim((string) ($input['external_comment'] ?? '')),
        ];
        if ($data['tender_id'] <= 0 || $data['company_id'] <= 0 || $data['internal_comment'] === '' || $data['external_comment'] === '') {
            return null;
        }
        return $data;
    }

    private function validateTenderForCancellation(?TenderMaster $tender, int $tenderId): ?array
    {
        $documentType = $this->resolveDocumentTypeLabel($tender, $tenderId);

        if (!$tender) {
            return [
                'success' => false,
                'code' => 404,
                'message' => trans('srm_tender_rfx.cancellation_document_not_found', ['document_type' => $documentType])
            ];
        }
        if ((int) $tender->cancelled_yn === 1) {
            return [
                'success' => false,
                'code' => 422,
                'message' => trans('srm_tender_rfx.cancellation_already_cancelled', ['document_type' => $documentType])
            ];
        }
        if ((int) $tender->approved !== -1) {
            return [
                'success' => false,
                'code' => 422,
                'message' => trans('srm_tender_rfx.cancellation_only_fully_approved_allowed', ['document_type' => $documentType])
            ];
        }
        if ((int) $tender->final_tender_awarded === 1) {
            return [
                'success' => false,
                'code' => 422,
                'message' => trans('srm_tender_rfx.cancellation_schedule_wise_awarded_not_allowed', ['document_type' => $documentType])
            ];
        }
        if ((int) $tender->evaluation_type_id === TenderConstants::EVALUATION_ITEM_WISE && SrmItemWiseTenderAwarding::hasAwardedItemsForTender($tenderId)) {
            return [
                'success' => false,
                'code' => 422,
                'message' => trans('srm_tender_rfx.cancellation_item_wise_awarded_not_allowed', ['document_type' => $documentType])
            ];
        }
        return null;
    }

    private function resolveDocumentTypeLabel(?TenderMaster $tender, int $tenderId): string
    {
        $documentSystemId = $tender ? (int) $tender->document_system_id : 0;

        if ($documentSystemId <= 0 && $tenderId > 0) {
            $documentSystemId = (int) TenderMaster::where('id', $tenderId)->value('document_system_id');
        }

        return $documentSystemId === 113
            ? trans('srm_tender_rfx.rfx')
            : trans('srm_tender_rfx.tender');
    }

    private function createAndConfirmRequest(TenderMaster $tender, array $data): array
    {
        DB::beginTransaction();
        try {
            $employee = Helper::getEmployeeInfo();
            $cancellation = $this->createCancellationRecord($tender, $data, (int) $employee->employeeSystemID);
            $confirm = $this->confirmCancellation($cancellation->id, $tender, $data, (int) $employee->employeeSystemID);
            if (!$confirm['success']) {
                DB::rollBack();
                return ['success' => false, 'code' => 422, 'message' => $confirm['message']];
            }
            DB::commit();
            return [
                'success' => true,
                'message' => trans('srm_tender_rfx.cancellation_request_submitted_for_approval', [
                    'document_type' => $this->resolveDocumentTypeLabel($tender, (int) $tender->id),
                ]),
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['success' => false, 'code' => 500, 'message' => $e->getMessage()];
        }
    }

    private function createCancellationRecord(TenderMaster $tender, array $data, int $employeeId): TenderCancellation
    {
        return TenderCancellation::create([
            'tender_id' => $tender->id,
            'internal_comment' => $data['internal_comment'],
            'external_comment' => $data['external_comment'],
            'document_system_id' => 134,
            'document_id' => 'TRC',
            'company_id' => $data['company_id'],
            'created_by' => $employeeId,
            'updated_by' => $employeeId,
        ]);
    }

    private function confirmCancellation(int $cancellationId, TenderMaster $tender, array $data, int $employeeId): array
    {
        return DocumentConfirm::confirmDocument([
            'autoID' => $cancellationId,
            'company' => $data['company_id'],
            'document' => 134,
            'reference_document_id' => null,
            'employee_id' => $employeeId,
            'tender_title' => $tender->tender_code . ' | ' . $tender->title,
            'tender_description' => $tender->description ?? '',
            'document_type' => $tender->document_type ?? 0,
            'internal_comment' => $data['internal_comment'],
            'external_comment' => $data['external_comment'],
        ]);
    }

    private function finalizeCancellation(TenderCancellation $cancellation): array
    {
        DB::beginTransaction();
        try {
            $employee = Helper::getEmployeeInfo();
            $cancellation->tender->markAsCancelled((int) $employee->employeeSystemID, (string) $employee->empName);
            $externalComment = (string) $cancellation->external_comment;
            $this->sendScheduleAndItemwiseSupplierEmail($cancellation->tender, $externalComment);
            $this->notifySuppliersAfterFinalApproval($cancellation->tender, $externalComment);
            DB::commit();
            return [
                'success' => true,
                'message' => trans('srm_tender_rfx.cancellation_finalized', [
                    'document_type' => $this->resolveDocumentTypeLabel($cancellation->tender, (int) $cancellation->tender->id),
                ]),
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['success' => false, 'code' => 500, 'message' => $e->getMessage()];
        }
    }

    private function mapStatus(TenderCancellation $record): array
    {
        return [
            'id' => $record->id,
            'confirmed_yn' => $record->confirmed_yn,
            'approved' => $record->approved,
            'refferedBackYN' => $record->refferedBackYN,
            'timesReferred' => $record->timesReferred,
            'internal_comment' => $record->internal_comment,
            'external_comment' => $record->external_comment,
            'approved_date' => $record->approved_date,
        ];
    }

    private function isScheduleOrItemWise(TenderMaster $tender): bool
    {
        return in_array((int) $tender->evaluation_type_id, [
            TenderConstants::EVALUATION_SCHEDULE_WISE,
            TenderConstants::EVALUATION_ITEM_WISE,
        ], true);
    }

    private function buildSubject(TenderMaster $tender): string
    {
        return 'Cancellation of Tender - ' . $tender->tender_code;
    }

    private function buildDefaultBody(TenderMaster $tender, string $externalComment): string
    {
        $company= Company::find($input['companySystemID']);
        $companyName = $company->CompanyName;

        return view('email.tender_cancellation_supplier_notice', [
            'tenderCode' => $tender->tender_code,
            'tenderTitle' => $tender->title,
            'tenderDescription' => $tender->description,
            'supplierComment' => $externalComment,
            'companyName' => $companyName,
        ])->render();
    }

    private function sendWebPush($registrationLinkId, string $tenderCode, TenderMaster $tender): void
    {
        if (empty($registrationLinkId)) {
            return;
        }
        $documentType = $this->resolveDocumentTypeLabel($tender, (int) $tender->id);
        WebPushNotificationService::sendNotification(
            [
                'title' => trans('srm_tender_rfx.cancellation_webpush_title', ['document_type' => $documentType]),
                'body' => trans('srm_tender_rfx.cancellation_webpush_body', ['document_type' => $documentType, 'tender_code' => $tenderCode]),
                'url' => '',
            ],
            4,
            $registrationLinkId,
            '',
            'supplier'
        );
    }
}

