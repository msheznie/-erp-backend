<?php

namespace App\Services;

use App\Models\ApprovalLevel;
use App\Models\DocumentAttachments;
use Illuminate\Support\Collection;

class PoApprovalConfirmValidationService
{
    private const PO_DOCUMENT_SYSTEM_ID = 2;

    /**
     *
     * @param Collection<int, ApprovalLevel> $approvalLevels
     * @param array<string, mixed> $params
     * @return array{valid: bool, messages: array<int, string>, approvalLevel: ?ApprovalLevel}
     */
    public function validateAndResolveFromLevels(Collection $approvalLevels, array $params): array
    {
        $documentSystemId = (int) ($params['document'] ?? 0);
        if ($documentSystemId !== self::PO_DOCUMENT_SYSTEM_ID) {
            return [
                'valid' => true,
                'messages' => [],
                'approvalLevel' => $approvalLevels->first(),
            ];
        }

        if ($approvalLevels->isEmpty()) {
            return ['valid' => false, 'messages' => [trans('custom.no_approval_setup_created')], 'approvalLevel' => null];
        }

        $companySystemId = (int) ($params['company'] ?? 0);
        $autoId = (int) ($params['autoID'] ?? 0);

        $totalAttachmentCount = (int) DocumentAttachments::where('companySystemID', $companySystemId)
            ->where('documentSystemID', $documentSystemId)
            ->where('documentSystemCode', $autoId)
            ->count();

        if ($totalAttachmentCount <= 0) {
            return [
                'valid' => false,
                'messages' => [trans('custom.attachment_is_mandatory_because_approval_enabled_based_on_attachment')],
                'approvalLevel' => null,
            ];
        }

        $matchingCountsByType = DocumentAttachments::where('companySystemID', $companySystemId)
            ->where('documentSystemID', $documentSystemId)
            ->where('documentSystemCode', $autoId)
            ->selectRaw('attachmentType, COUNT(*) as total')
            ->groupBy('attachmentType')
            ->pluck('total', 'attachmentType')
            ->toArray();

        foreach ($approvalLevels as $approvalLevel) {
            $attachmentTypeId = (int) ($approvalLevel->attachmentTypeID ?? 0);
            $requiredCount = (int) ($approvalLevel->attachmentDocumentCount ?? 0);

            if ($attachmentTypeId <= 0 || $requiredCount <= 0) {
                continue;
            }

            $matchingCount = (int) ($matchingCountsByType[$attachmentTypeId] ?? 0);
            if ($matchingCount >= $requiredCount) {
                return ['valid' => true, 'messages' => [], 'approvalLevel' => $approvalLevel];
            }
        }

        return [
            'valid' => false,
            'messages' => [trans('custom.no_approval_setup_created')],
            'approvalLevel' => null,
        ];
    }
}
