<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\AppBaseController;
use App\Services\DocumentCommunicationService;
use Illuminate\Http\Request;

class DocumentCommunicationAPIController extends AppBaseController
{
    private $service;

    public function __construct(DocumentCommunicationService $service)
    {
        $this->service = $service;
    }

    public function thread(Request $request)
    {
        try
        {
            $result = $this->service->getThreadMeta($request->all());
            if (!$result['success']) {
                return $this->sendError($result['message'], $result['code'] ?? 422);
            }
            return $this->sendResponse($result['data'], trans('custom.thread_details_retrieved_successfully'));
        }
        catch(\Exception $e)
        {
            return $this->sendError(trans('custom.unexpected_error') . $e->getMessage());
        }
    }

    public function messages(Request $request)
    {
        try
        {
            $result = $this->service->listMessages($request->all());
            if (!$result['success']) {
                return $this->sendError($result['message'], $result['code'] ?? 422);
            }
            return $this->sendResponse($result['data'], trans('custom.messages_retrieved_successfully'));
        }
        catch(\Exception $e)
        {
            return $this->sendError(trans('custom.unexpected_error') . $e->getMessage());
        }
    }

    public function storeMessage(Request $request)
    {
        try
        {
            $result = $this->service->createMessage($request->all(), false);
            if (!$result['success']) {
                return $this->sendError($result['message'], $result['code'] ?? 422);
            }
            return $this->sendResponse($result['data'], trans('custom.comment_added_successfully'));
        }
        catch(\Exception $e)
        {
            return $this->sendError(trans('custom.unexpected_error') . $e->getMessage());
        }
    }

    public function replyMessage(Request $request)
    {
        try
        {
            $result = $this->service->createMessage($request->all(), true);
            if (!$result['success']) {
                return $this->sendError($result['message'], $result['code'] ?? 422);
            }
            return $this->sendResponse($result['data'], trans('custom.reply_added_successfully'));
        }
        catch(\Exception $e)
        {
            return $this->sendError(trans('custom.unexpected_error') . $e->getMessage());
        }
    }

    public function updateMessage($id, Request $request)
    {
        try
        {
            $result = $this->service->updateMessage((int)$id, $request->all());
            if (!$result['success']) {
                return $this->sendError($result['message'], $result['code'] ?? 422);
            }
            return $this->sendResponse($result['data'], trans('custom.comment_updated_successfully'));
        }
        catch(\Exception $e)
        {
            return $this->sendError(trans('custom.unexpected_error') . $e->getMessage());
        }
    }

    public function deleteMessage($id, Request $request)
    {
        try
        {
            $result = $this->service->deleteMessage((int)$id, $request->all());
            if (!$result['success']) {
                return $this->sendError($result['message'], $result['code'] ?? 422);
            }
            return $this->sendResponse($result['data'], trans('custom.comment_deleted_successfully'));
        }
        catch(\Exception $e)
        {
            return $this->sendError(trans('custom.unexpected_error') . $e->getMessage());
        }
    }
}

