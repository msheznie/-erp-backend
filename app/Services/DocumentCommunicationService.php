<?php

namespace App\Services;

use App\helper\Helper;
use App\Models\Company;
use App\Models\DocumentCommunicationMessage;
use App\Repositories\DocumentCommunicationRepository;
use App\Traits\AuditLogsTrait;
use Illuminate\Support\Facades\Validator;

class DocumentCommunicationService
{
    use AuditLogsTrait;

    private $repository;

    public function __construct(DocumentCommunicationRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getThreadMeta(array $input): array
    {
        $validator = Validator::make($input, [
            'companySystemID' => 'required|integer',
            'documentSystemID' => 'required|integer',
            'documentSystemCode' => 'required|integer',
        ], [
            'companySystemID.required' => 'companySystemID is required',
            'companySystemID.integer' => 'companySystemID must be an integer',
            'documentSystemID.required' => 'documentSystemID is required',
            'documentSystemID.integer' => 'documentSystemID must be an integer',
            'documentSystemCode.required' => 'documentSystemCode is required',
            'documentSystemCode.integer' => 'documentSystemCode must be an integer',
        ]);

        if ($validator->fails()) {
            return ['success' => false, 'code' => 422, 'message' => $validator->errors()->first()];
        }

        $thread = $this->repository->getThread([
            'documentSystemID' => (int)$input['documentSystemID'],
            'companySystemID' => (int)$input['companySystemID'],
        ]);

        $company = Company::find((int)$input['companySystemID']);
        $companyData = $company ? [
            'companySystemID' => (int)$company->companySystemID,
            'companyName' => $company->CompanyName,
            'logo_url' => $company->logo_url,
        ] : null;

        return [
            'success' => true,
            'data' => [
                'thread' => $thread,
                'company' => $companyData,
            ],
        ];
    }

    public function listMessages(array $input): array
    {
        $validator = Validator::make($input, [
            'thread_id' => 'required|string',
            'documentSystemCode' => 'required|integer',
            'sort' => 'nullable|in:newest,oldest',
        ], [
            'thread_id.required' => 'thread_id is required',
            'thread_id.string' => 'thread_id must be a string',
            'documentSystemCode.required' => 'documentSystemCode is required',
            'documentSystemCode.integer' => 'documentSystemCode must be an integer',
            'sort.in' => 'sort must be either newest or oldest',
        ]);

        if ($validator->fails()) {
            return ['success' => false, 'code' => 422, 'message' => $validator->errors()->first()];
        }

        $messages = $this->repository->getMessages((string)$input['thread_id'], (int)$input['documentSystemCode'], $input['sort'] ?? 'newest');
        return ['success' => true, 'data' => $messages];
    }

    public function createMessage(array $input, bool $isReply = false): array
    {
        $rules = [
            'thread_id' => 'required|string',
            'documentSystemCode' => 'required|integer',
            'body' => 'required|string',
            'author_type' => 'nullable|in:1,2',
            'author_id' => 'nullable|integer',
        ];
        $messages = [
            'thread_id.required' => 'thread_id is required',
            'thread_id.string' => 'thread_id must be a string',
            'documentSystemCode.required' => 'documentSystemCode is required',
            'documentSystemCode.integer' => 'documentSystemCode must be an integer',
            'body.required' => 'body is required',
            'body.string' => 'body must be a string',
            'author_type.in' => 'author_type must be either 1 or 2',
            'author_id.integer' => 'author_id must be an integer',
        ];
        if ($isReply) {
            $rules['parent_message_id'] = 'required|string';
            $messages['parent_message_id.required'] = 'parent_message_id is required';
            $messages['parent_message_id.string'] = 'parent_message_id must be a string';
        }

        $validator = Validator::make($input, $rules, $messages);
        if ($validator->fails()) {
            return ['success' => false, 'code' => 422, 'message' => $validator->errors()->first()];
        }

        $authorType = isset($input['author_type']) ? (int)$input['author_type'] : 1;
        $authorId = isset($input['author_id']) ? (int)$input['author_id'] : (int)Helper::getEmployeeSystemID();

        $parentUuid = null;
        if ($isReply) {
            $parentRef = (string)$input['parent_message_id'];
            $parentMessage = null;

            // Backward compatibility: allow numeric id as parent reference.
            if (ctype_digit($parentRef)) {
                $parentMessage = $this->repository->getMessageById((int)$parentRef);
            } else {
                $parentMessage = $this->repository->getMessageByUuid($parentRef);
            }

            if (!$parentMessage) {
                return ['success' => false, 'code' => 404, 'message' => 'Parent comment not found.'];
            }

            $parentUuid = $parentMessage->uuid;
        }

        $message = $this->repository->createMessage([
            'thread_id' => (string)$input['thread_id'],
            'documentSystemCode' => (int)$input['documentSystemCode'],
            'parent_message_id' => $isReply ? $parentUuid : null,
            'authortype' => $authorType,
            'authorId' => $authorId,
            'body' => $input['body'],
        ]);

        if (!empty($input['attachments']) && is_array($input['attachments'])) {
            foreach ($input['attachments'] as $item) {
                $this->repository->addAttachment([
                    'message_id' => $message->id,
                    'attachmentDescription' => $item['attachmentDescription'] ?? '',
                    'originalFileName' => $item['originalFileName'] ?? ($item['fileName'] ?? ''),
                    'fileStoragePath' => $item['fileStoragePath'] ?? '',
                    'fileSize' => $item['fileSize'] ?? null,
                    'createdBy' => $authorId,
                ]);
            }
        }

        $this->writeAudit('C', $message, [], $input);
        return ['success' => true, 'data' => $message->fresh('attachments')];
    }

    public function updateMessage(int $id, array $input): array
    {
        $validator = Validator::make($input, [
            'body' => 'required|string',
            'expected_version' => 'required|integer',
            'author_type' => 'nullable|in:1,2',
            'author_id' => 'nullable|integer',
        ], [
            'body.required' => 'body is required',
            'body.string' => 'body must be a string',
            'expected_version.required' => 'expected_version is required',
            'expected_version.integer' => 'expected_version must be an integer',
            'author_type.in' => 'author_type must be either 1 or 2',
            'author_id.integer' => 'author_id must be an integer',
        ]);
        if ($validator->fails()) {
            return ['success' => false, 'code' => 422, 'message' => $validator->errors()->first()];
        }

        $message = $this->repository->getMessageById($id);
        if (!$message || $message->deleted_at) {
            return ['success' => false, 'code' => 404, 'message' => 'Comment not found.'];
        }

        $authorType = isset($input['author_type']) ? (int)$input['author_type'] : 1;
        $authorId = isset($input['author_id']) ? (int)$input['author_id'] : (int)Helper::getEmployeeSystemID();
        if ((int)$message->authortype !== $authorType || (int)$message->authorId !== $authorId) {
            return ['success' => false, 'code' => 403, 'message' => 'You can edit only your own comments.'];
        }

        if ((int)$message->version !== (int)$input['expected_version']) {
            return ['success' => false, 'code' => 409, 'message' => 'Comment updated by another user. Please refresh.'];
        }

        $previous = $message->toArray();
        $updated = $this->repository->updateMessage($message, [
            'body' => $input['body'],
            'edited_at' => now(),
            'version' => (int)$message->version + 1,
        ]);

        $this->writeAudit('U', $updated, $previous, $input);
        return ['success' => true, 'data' => $updated];
    }

    public function deleteMessage(int $id, array $input): array
    {
        $message = $this->repository->getMessageById($id);
        if (!$message || $message->deleted_at) {
            return ['success' => false, 'code' => 404, 'message' => 'Comment not found.'];
        }

        $authorType = isset($input['author_type']) ? (int)$input['author_type'] : 1;
        $authorId = isset($input['author_id']) ? (int)$input['author_id'] : (int)Helper::getEmployeeSystemID();
        if ((int)$message->authortype !== $authorType || (int)$message->authorId !== $authorId) {
            return ['success' => false, 'code' => 403, 'message' => 'You can delete only your own comments.'];
        }

        if ($this->repository->hasReplies((string)$message->uuid)) {
            return ['success' => false, 'code' => 422, 'message' => 'Cannot delete comment with replies.'];
        }

        $previous = $message->toArray();
        $message->deleted_by = $authorId;
        $message->save();
        $message->delete();

        $this->writeAudit('D', $message, $previous, $input);
        return ['success' => true, 'data' => $message];
    }

    private function writeAudit(string $crudType, DocumentCommunicationMessage $message, array $previous, array $input): void
    {
        $db = $input['db'] ?? '';
        $uuid = $input['tenant_uuid'] ?? 'local';
        $narration = (int)$message->documentSystemCode;
        $newValue = $message->toArray();
        $this->auditLog(
            $db,
            (int)$message->documentSystemCode,
            $uuid,
            'erp_document_communication_messages',
            $narration,
            $crudType,
            $newValue,
            $previous
        );
    }
}

