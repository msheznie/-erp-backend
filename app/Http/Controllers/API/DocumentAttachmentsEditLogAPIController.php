<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateDocumentAttachmentsEditLogAPIRequest;
use App\Http\Requests\API\UpdateDocumentAttachmentsEditLogAPIRequest;
use App\Models\DocumentAttachmentsEditLog;
use App\Repositories\DocumentAttachmentsEditLogRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class DocumentAttachmentsEditLogController
 * @package App\Http\Controllers\API
 */

class DocumentAttachmentsEditLogAPIController extends AppBaseController
{
    /** @var  DocumentAttachmentsEditLogRepository */
    private $documentAttachmentsEditLogRepository;

    public function __construct(DocumentAttachmentsEditLogRepository $documentAttachmentsEditLogRepo)
    {
        $this->documentAttachmentsEditLogRepository = $documentAttachmentsEditLogRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/documentAttachmentsEditLogs",
     *      summary="getDocumentAttachmentsEditLogList",
     *      tags={"DocumentAttachmentsEditLog"},
     *      description="Get all DocumentAttachmentsEditLogs",
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  type="array",
     *                  @OA\Items(ref="#/definitions/DocumentAttachmentsEditLog")
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function index(Request $request)
    {
        $this->documentAttachmentsEditLogRepository->pushCriteria(new RequestCriteria($request));
        $this->documentAttachmentsEditLogRepository->pushCriteria(new LimitOffsetCriteria($request));
        $documentAttachmentsEditLogs = $this->documentAttachmentsEditLogRepository->all();

        return $this->sendResponse($documentAttachmentsEditLogs->toArray(), trans('custom.document_attachments_edit_logs_retrieved_successfu'));
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/documentAttachmentsEditLogs",
     *      summary="createDocumentAttachmentsEditLog",
     *      tags={"DocumentAttachmentsEditLog"},
     *      description="Create DocumentAttachmentsEditLog",
     *      @OA\RequestBody(
     *        required=true,
     *        @OA\MediaType(
     *            mediaType="application/x-www-form-urlencoded",
     *            @OA\Schema(
     *                type="object",
     *                required={""},
     *                @OA\Property(
     *                    property="name",
     *                    description="desc",
     *                    type="string"
     *                )
     *            )
     *        )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  ref="#/definitions/DocumentAttachmentsEditLog"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateDocumentAttachmentsEditLogAPIRequest $request)
    {
        $input = $request->all();

        $documentAttachmentsEditLog = $this->documentAttachmentsEditLogRepository->create($input);

        return $this->sendResponse($documentAttachmentsEditLog->toArray(), trans('custom.document_attachments_edit_log_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/documentAttachmentsEditLogs/{id}",
     *      summary="getDocumentAttachmentsEditLogItem",
     *      tags={"DocumentAttachmentsEditLog"},
     *      description="Get DocumentAttachmentsEditLog",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of DocumentAttachmentsEditLog",
     *           @OA\Schema(
     *             type="integer"
     *          ),
     *          required=true,
     *          in="path"
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  ref="#/definitions/DocumentAttachmentsEditLog"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function show($id)
    {
        /** @var DocumentAttachmentsEditLog $documentAttachmentsEditLog */
        $documentAttachmentsEditLog = $this->documentAttachmentsEditLogRepository->findWithoutFail($id);

        if (empty($documentAttachmentsEditLog)) {
            return $this->sendError(trans('custom.document_attachments_edit_log_not_found'));
        }

        return $this->sendResponse($documentAttachmentsEditLog->toArray(), trans('custom.document_attachments_edit_log_retrieved_successful'));
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/documentAttachmentsEditLogs/{id}",
     *      summary="updateDocumentAttachmentsEditLog",
     *      tags={"DocumentAttachmentsEditLog"},
     *      description="Update DocumentAttachmentsEditLog",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of DocumentAttachmentsEditLog",
     *           @OA\Schema(
     *             type="integer"
     *          ),
     *          required=true,
     *          in="path"
     *      ),
     *      @OA\RequestBody(
     *        required=true,
     *        @OA\MediaType(
     *            mediaType="application/x-www-form-urlencoded",
     *            @OA\Schema(
     *                type="object",
     *                required={""},
     *                @OA\Property(
     *                    property="name",
     *                    description="desc",
     *                    type="string"
     *                )
     *            )
     *        )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  ref="#/definitions/DocumentAttachmentsEditLog"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateDocumentAttachmentsEditLogAPIRequest $request)
    {
        $input = $request->all();

        /** @var DocumentAttachmentsEditLog $documentAttachmentsEditLog */
        $documentAttachmentsEditLog = $this->documentAttachmentsEditLogRepository->findWithoutFail($id);

        if (empty($documentAttachmentsEditLog)) {
            return $this->sendError(trans('custom.document_attachments_edit_log_not_found'));
        }

        $documentAttachmentsEditLog = $this->documentAttachmentsEditLogRepository->update($input, $id);

        return $this->sendResponse($documentAttachmentsEditLog->toArray(), trans('custom.documentattachmentseditlog_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/documentAttachmentsEditLogs/{id}",
     *      summary="deleteDocumentAttachmentsEditLog",
     *      tags={"DocumentAttachmentsEditLog"},
     *      description="Delete DocumentAttachmentsEditLog",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of DocumentAttachmentsEditLog",
     *           @OA\Schema(
     *             type="integer"
     *          ),
     *          required=true,
     *          in="path"
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  type="string"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function destroy($id)
    {
        /** @var DocumentAttachmentsEditLog $documentAttachmentsEditLog */
        $documentAttachmentsEditLog = $this->documentAttachmentsEditLogRepository->findWithoutFail($id);

        if (empty($documentAttachmentsEditLog)) {
            return $this->sendError(trans('custom.document_attachments_edit_log_not_found'));
        }
        $deleteResp = $this->documentAttachmentsEditLogRepository->deleteAttachment($documentAttachmentsEditLog);
        if(!$deleteResp['success']){
            return $this->sendError($deleteResp['message']);
        }

        return $this->sendResponse([], trans('custom.document_attachments_deleted_successfully'));
    }


}
