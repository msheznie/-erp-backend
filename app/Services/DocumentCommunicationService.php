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
            'companySystemID.required' => trans('custom.company_field_is_required'),
            'companySystemID.integer' => trans('custom.company_field_must_be_an_integer'),
            'documentSystemID.required' => trans('custom.document_system_id_is_required'),
            'documentSystemID.integer' => trans('custom.document_system_id_must_be_an_integer'),
            'documentSystemCode.required' => trans('custom.document_system_code_is_required'),
            'documentSystemCode.integer' => trans('custom.document_system_code_must_be_an_integer'),
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
            'thread_id.required' => trans('custom.thread_id_is_required'),
            'thread_id.string' => trans('custom.thread_id_must_be_a_string'),
            'documentSystemCode.required' => trans('custom.document_system_code_is_required'),
            'documentSystemCode.integer' => trans('custom.document_system_code_must_be_an_integer'),
            'sort.in' => trans('custom.sort_must_be_either_newest_or_oldest'),
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
            'thread_id.required' => trans('custom.thread_id_is_required'),
            'thread_id.string' => trans('custom.thread_id_must_be_a_string'),
            'documentSystemCode.required' => trans('custom.document_system_code_is_required'),
            'documentSystemCode.integer' => trans('custom.document_system_code_must_be_an_integer'),
            'body.required' => trans('custom.body_is_required'),
            'body.string' => trans('custom.body_must_be_a_string'),
            'author_type.in' => trans('custom.author_type_must_be_either_1_or_2'),
            'author_id.integer' => trans('custom.author_id_must_be_an_integer'),
        ];
        if ($isReply) {
            $rules['parent_message_id'] = 'required|string';
            $messages['parent_message_id.required'] = trans('custom.parent_message_id_is_required');
            $messages['parent_message_id.string'] = trans('custom.parent_message_id_must_be_a_string');
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
                return ['success' => false, 'code' => 404, 'message' => trans('custom.parent_comment_not_found')];
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
            'body.required' => trans('custom.body_is_required'),
            'body.string' => trans('custom.body_must_be_a_string'),
            'expected_version.required' => trans('custom.expected_version_is_required'),
            'expected_version.integer' => trans('custom.expected_version_must_be_an_integer'),
            'author_type.in' => trans('custom.author_type_must_be_either_1_or_2'),
            'author_id.integer' => trans('custom.author_id_must_be_an_integer'),
        ]);
        if ($validator->fails()) {
            return ['success' => false, 'code' => 422, 'message' => $validator->errors()->first()];
        }

        $message = $this->repository->getMessageById($id);
        if (!$message || $message->deleted_at) {
            return ['success' => false, 'code' => 404, 'message' => trans('custom.comment_not_found')];
        }

        $authorType = isset($input['author_type']) ? (int)$input['author_type'] : 1;
        $authorId = isset($input['author_id']) ? (int)$input['author_id'] : (int)Helper::getEmployeeSystemID();
        if ((int)$message->authortype !== $authorType || (int)$message->authorId !== $authorId) {
            return ['success' => false, 'code' => 403, 'message' => trans('custom.you_can_edit_only_your_own_comments')];
        }

        if ((int)$message->version !== (int)$input['expected_version']) {
            return ['success' => false, 'code' => 409, 'message' => trans('custom.comment_updated_by_another_user_please_refresh')];
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
            return ['success' => false, 'code' => 404, 'message' => trans('custom.comment_not_found')];
        }

        $authorType = isset($input['author_type']) ? (int)$input['author_type'] : 1;
        $authorId = isset($input['author_id']) ? (int)$input['author_id'] : (int)Helper::getEmployeeSystemID();
        if ((int)$message->authortype !== $authorType || (int)$message->authorId !== $authorId) {
            return ['success' => false, 'code' => 403, 'message' => trans('custom.you_can_delete_only_your_own_comments')];
        }

        if ($this->repository->hasReplies((string)$message->uuid)) {
            return ['success' => false, 'code' => 422, 'message' => trans('custom.cannot_delete_comment_with_replies')];
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

