<?php

namespace App\Services\API;

use App\helper\Helper;
use App\Repositories\JvMasterRepository;
use App\Traits\AuditTrial;

class JournalVoucherCancelService
{
    private $jvMasterRepository;

    public function __construct(JvMasterRepository $jvMasterRepository)
    {
        $this->jvMasterRepository = $jvMasterRepository;
    }

    public function cancelJournalVoucher(array $input): array
    {
        $jvMasterAutoId = (int) ($input['jvMasterAutoId'] ?? 0);
        $cancelComments = trim((string) ($input['cancelComments'] ?? ''));

        $jvMasterData = $this->jvMasterRepository->findByAutoId($jvMasterAutoId);
        if (empty($jvMasterData)) {
            return [
                'status' => false,
                'message' => trans('custom.journal_voucher_not_found'),
                'httpCode' => 500
            ];
        }

        if ($jvMasterData->cancelYN == -1) {
            return [
                'status' => false,
                'message' => trans('custom.document_already_cancelled', ['type' => 'journal voucher']),
                'httpCode' => 500
            ];
        }

        if ($jvMasterData->confirmedYN == 1 || $jvMasterData->approved == -1) {
            return [
                'status' => false,
                'message' => 'You can cancel only non confirmed journal vouchers.',
                'httpCode' => 500
            ];
        }

        if ($this->jvMasterRepository->hasDetailRecords($jvMasterAutoId)) {
            return [
                'status' => false,
                'message' => 'You cannot cancel the document as there are records in detail',
                'httpCode' => 500
            ];
        }

        $employee = Helper::getEmployeeInfo();
        $jvMasterData = $this->jvMasterRepository->cancelJournalVoucher($jvMasterData, $cancelComments, $employee);

        AuditTrial::createAuditTrial($jvMasterData->documentSystemID, $jvMasterAutoId, $cancelComments, 'Cancelled');

        return [
            'status' => true,
            'data' => $jvMasterData->toArray(),
            'message' => trans('custom.successfully_cancelled'),
            'httpCode' => 200
        ];
    }
}
