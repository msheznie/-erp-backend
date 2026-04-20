<?php

namespace App\Repositories;

use App\Models\Employee;
use App\Models\TenderConfirmationDetail;
use App\Models\TenderMaster;
use App\Models\TenderNegotiation;
use App\Models\SrmBidDocumentattachments;
use App\Repositories\BaseRepository;

/**
 * Class SrmBidDocumentattachmentsRepository
 * @package App\Repositories
 * @version October 24, 2022, 9:04 am +04
 *
 * @method SrmBidDocumentattachments findWithoutFail($id, $columns = ['*'])
 * @method SrmBidDocumentattachments find($id, $columns = ['*'])
 * @method SrmBidDocumentattachments first($columns = ['*'])
*/
class SrmBidDocumentattachmentsRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'tender_id',
        'companySystemID',
        'companyID',
        'documentSystemID',
        'documentID',
        'documentSystemCode',
        'attachmentDescription',
        'originalFileName',
        'myFileName',
        'path',
        'sizeInKbs'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return SrmBidDocumentattachments::class;
    }

    public function getNegotiationPayload(int $tenderId, int $companySystemId, int $documentSystemId, int $type): array
    {
        $module = $this->getModuleByType($type);
        if (!$module || !$tenderId) {
            return [
                'history' => [],
                'current_round_comment' => null
            ];
        }

        $latestNegotiation = TenderNegotiation::getTenderLatestNegotiations($tenderId);
        $currentRoundComment = null;
        if ($latestNegotiation) {
            $row = TenderConfirmationDetail::getLatestByTenderModuleReference($tenderId, $module, (int)$latestNegotiation->id);
            $currentRoundComment = $row ? $row->comment : null;
        }

        return [
            'history' => $this->buildNegotiationHistory($tenderId, $companySystemId, $documentSystemId, $type, $module),
            'current_round_comment' => $currentRoundComment
        ];
    }

    private function getModuleByType(int $type): ?int
    {
        if ($type === 1) {
            return TenderConfirmationDetail::MODULE_BID_OPENING_APPROVAL;
        }
        if ($type === 2) {
            return TenderConfirmationDetail::MODULE_COMBINED_RANKING;
        }
        return null;
    }

    private function buildNegotiationHistory(int $tenderId, int $companySystemId, int $documentSystemId, int $type, int $module): array
    {
        $historyRows = [];
        $tender = TenderMaster::select('id', 'doc_verifiy_comment', 'award_comment', 'updated_at')->find($tenderId);
        $originalAttachments = SrmBidDocumentattachments::getOriginalByContextAll($companySystemId, $documentSystemId, $tenderId, $type);

        $originalComment = null;
        if ($tender) {
            $originalComment = ($type === 1) ? $tender->doc_verifiy_comment : $tender->award_comment;
        }
        if (($originalComment !== null && $originalComment !== '') || ($originalAttachments && $originalAttachments->count() > 0)) {
            if ($originalAttachments && $originalAttachments->count() > 0) {
                foreach ($originalAttachments as $originalAttachment) {
                    $historyRows[] = [
                        'scope' => 'original',
                        'round_no' => 0,
                        'comment' => $originalComment ?: '',
                        'user_name' => '-',
                        'commented_at' => optional($tender)->updated_at,
                        'attachment_description' => $originalAttachment ? $originalAttachment->attachmentDescription : '-',
                        'attachment_id' => $originalAttachment ? $originalAttachment->id : null,
                        'original_file_name' => $originalAttachment ? $originalAttachment->originalFileName : null,
                    ];
                }
            } else {
                $historyRows[] = [
                    'scope' => 'original',
                    'round_no' => 0,
                    'comment' => $originalComment ?: '',
                    'user_name' => '-',
                    'commented_at' => optional($tender)->updated_at,
                    'attachment_description' => '-',
                    'attachment_id' => null,
                    'original_file_name' => null,
                ];
            }
        }

        $negotiations = TenderNegotiation::getRoundsByTender($tenderId);
        $negIds = $negotiations->pluck('id')->toArray();
        if (empty($negIds)) {
            return $historyRows;
        }

        $confirmRows = TenderConfirmationDetail::getByTenderModuleReferences($tenderId, $module, $negIds);
        $attachmentByRound = SrmBidDocumentattachments::getAttachmentsByRounds($companySystemId, $documentSystemId, $tenderId, $type);

        foreach ($negotiations as $neg) {
            $confirm = $confirmRows->where('reference_id', $neg->id)->last();
            $attachments = $attachmentByRound->get($neg->version);
            if (!$confirm && (!$attachments || $attachments->count() === 0)) {
                continue;
            }

            $employeeName = '-';
            if ($confirm && $confirm->action_by) {
                $emp = Employee::select('empFullName')->where('employeeSystemID', $confirm->action_by)->first();
                $employeeName = $emp ? $emp->empFullName : '-';
            }

            if ($attachments && $attachments->count() > 0) {
                foreach ($attachments as $attachment) {
                    $historyRows[] = [
                        'scope' => 'negotiation',
                        'round_no' => (int)$neg->version,
                        'comment' => $confirm ? ($confirm->comment ?: '') : '',
                        'user_name' => $employeeName,
                        'commented_at' => $confirm ? $confirm->action_at : optional($attachment)->created_at,
                        'attachment_description' => $attachment ? $attachment->attachmentDescription : '-',
                        'attachment_id' => $attachment ? $attachment->id : null,
                        'original_file_name' => $attachment ? $attachment->originalFileName : null,
                    ];
                }
            } else {
                $historyRows[] = [
                    'scope' => 'negotiation',
                    'round_no' => (int)$neg->version,
                    'comment' => $confirm ? ($confirm->comment ?: '') : '',
                    'user_name' => $employeeName,
                    'commented_at' => $confirm ? $confirm->action_at : null,
                    'attachment_description' => '-',
                    'attachment_id' => null,
                    'original_file_name' => null,
                ];
            }
        }

        return $historyRows;
    }
}
