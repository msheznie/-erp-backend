<?php

namespace App\Repositories;

use App\Models\DocumentCommunicationAttachment;
use App\Models\DocumentCommunicationConfiguration;
use App\Models\DocumentCommunicationConfigurationMaster;
use App\Models\DocumentCommunicationMessage;
use App\Models\DocumentCommunicationThread;
use App\Models\ProcumentOrder;
use App\Models\SupplierAssigned;

class DocumentCommunicationRepository extends BaseRepository
{
    protected $fieldSearchable = [];

    public function model()
    {
        return DocumentCommunicationThread::class;
    }

    public function getThread(array $data): DocumentCommunicationThread
    {
        $thread = DocumentCommunicationThread::where('documentSystemID', $data['documentSystemID'])
            ->with('communicationConfigurationByCompany')
            ->whereHas('communicationConfigurationByCompany', function ($query) use ($data) {
                $query->where('companySystemID', $data['companySystemID'])->where('isActive', 1);
            })
            ->first();

        if ($thread) {
            return $thread;
        }
    }

    public function getMessages(string $threadId, int $documentSystemCode, string $sort = 'newest')
    {
        $direction = $sort === 'oldest' ? 'asc' : 'desc';

        return DocumentCommunicationMessage::with([
            'attachments',
            'author' => function ($q) {
                $q->select(['employeeSystemID', 'empName', 'empFullName', 'empUserName']);
            },
            'replies.attachments',
            'replies.author' => function ($q) {
                $q->select(['employeeSystemID', 'empName', 'empFullName', 'empUserName']);
            }
        ])
            ->where('thread_id', $threadId)
            ->where('documentSystemCode', $documentSystemCode)
            ->whereNull('parent_message_id')
            ->whereNull('deleted_at')
            ->orderBy('created_at', $direction)
            ->get();
    }

    public function createMessage(array $data): DocumentCommunicationMessage
    {
        $uuid = $data['uuid'] ?? $this->generateUuid(16);
        $message = DocumentCommunicationMessage::create($data);

        // Ensure uuid is set even if caller didn't provide it.
        if (empty($message->uuid)) {
            $message->uuid = $uuid;
            $message->save();
        }

        return $message->load('attachments');
    }

    public function updateMessage(DocumentCommunicationMessage $message, array $data): DocumentCommunicationMessage
    {
        $message->fill($data);
        $message->save();
        return $message->load('attachments');
    }

    public function addAttachment(array $data): DocumentCommunicationAttachment
    {
        $data['uuid'] = $data['uuid'] ?? $this->generateUuid(16);
        return DocumentCommunicationAttachment::create($data);
    }

    public function getMessageById(int $id): ?DocumentCommunicationMessage
    {
        return DocumentCommunicationMessage::with('attachments')->find($id);
    }

    public function getMessageByUuid(string $uuid): ?DocumentCommunicationMessage
    {
        return DocumentCommunicationMessage::with('attachments')->where('uuid', $uuid)->first();
    }

    public function hasReplies(string $messageUuid): bool
    {
        return DocumentCommunicationMessage::where('parent_message_id', $messageUuid)
            ->whereNull('deleted_at')
            ->exists();
    }

    private function generateUuid($length = 16): string
    {
        return bin2hex(random_bytes($length));
    }
}

