<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateMiBulkUploadErrorLogAPIRequest;
use App\Http\Requests\API\UpdateMiBulkUploadErrorLogAPIRequest;
use App\Models\MiBulkUploadErrorLog;
use App\Repositories\MiBulkUploadErrorLogRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class MiBulkUploadErrorLogController
 * @package App\Http\Controllers\API
 */

class MiBulkUploadErrorLogAPIController extends AppBaseController
{
    /** @var  MiBulkUploadErrorLogRepository */
    private $miBulkUploadErrorLogRepository;

    public function __construct(MiBulkUploadErrorLogRepository $miBulkUploadErrorLogRepo)
    {
        $this->miBulkUploadErrorLogRepository = $miBulkUploadErrorLogRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/miBulkUploadErrorLogs",
     *      summary="getMiBulkUploadErrorLogList",
     *      tags={"MiBulkUploadErrorLog"},
     *      description="Get all MiBulkUploadErrorLogs",
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
     *                  @OA\Items(ref="#/definitions/MiBulkUploadErrorLog")
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
        $this->miBulkUploadErrorLogRepository->pushCriteria(new RequestCriteria($request));
        $this->miBulkUploadErrorLogRepository->pushCriteria(new LimitOffsetCriteria($request));
        $miBulkUploadErrorLogs = $this->miBulkUploadErrorLogRepository->all();

        return $this->sendResponse($miBulkUploadErrorLogs->toArray(), trans('custom.mi_bulk_upload_error_logs_retrieved_successfully'));
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/miBulkUploadErrorLogs",
     *      summary="createMiBulkUploadErrorLog",
     *      tags={"MiBulkUploadErrorLog"},
     *      description="Create MiBulkUploadErrorLog",
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
     *                  ref="#/definitions/MiBulkUploadErrorLog"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateMiBulkUploadErrorLogAPIRequest $request)
    {
        $input = $request->all();

        $miBulkUploadErrorLog = $this->miBulkUploadErrorLogRepository->create($input);

        return $this->sendResponse($miBulkUploadErrorLog->toArray(), trans('custom.mi_bulk_upload_error_log_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/miBulkUploadErrorLogs/{id}",
     *      summary="getMiBulkUploadErrorLogItem",
     *      tags={"MiBulkUploadErrorLog"},
     *      description="Get MiBulkUploadErrorLog",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of MiBulkUploadErrorLog",
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
     *                  ref="#/definitions/MiBulkUploadErrorLog"
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
        /** @var MiBulkUploadErrorLog $miBulkUploadErrorLog */
        $miBulkUploadErrorLog = $this->miBulkUploadErrorLogRepository->findWithoutFail($id);

        if (empty($miBulkUploadErrorLog)) {
            return $this->sendError(trans('custom.mi_bulk_upload_error_log_not_found'));
        }

        return $this->sendResponse($miBulkUploadErrorLog->toArray(), trans('custom.mi_bulk_upload_error_log_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/miBulkUploadErrorLogs/{id}",
     *      summary="updateMiBulkUploadErrorLog",
     *      tags={"MiBulkUploadErrorLog"},
     *      description="Update MiBulkUploadErrorLog",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of MiBulkUploadErrorLog",
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
     *                  ref="#/definitions/MiBulkUploadErrorLog"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateMiBulkUploadErrorLogAPIRequest $request)
    {
        $input = $request->all();

        /** @var MiBulkUploadErrorLog $miBulkUploadErrorLog */
        $miBulkUploadErrorLog = $this->miBulkUploadErrorLogRepository->findWithoutFail($id);

        if (empty($miBulkUploadErrorLog)) {
            return $this->sendError(trans('custom.mi_bulk_upload_error_log_not_found'));
        }

        $miBulkUploadErrorLog = $this->miBulkUploadErrorLogRepository->update($input, $id);

        return $this->sendResponse($miBulkUploadErrorLog->toArray(), trans('custom.mibulkuploaderrorlog_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/miBulkUploadErrorLogs/{id}",
     *      summary="deleteMiBulkUploadErrorLog",
     *      tags={"MiBulkUploadErrorLog"},
     *      description="Delete MiBulkUploadErrorLog",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of MiBulkUploadErrorLog",
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
        /** @var MiBulkUploadErrorLog $miBulkUploadErrorLog */
        $miBulkUploadErrorLog = $this->miBulkUploadErrorLogRepository->findWithoutFail($id);

        if (empty($miBulkUploadErrorLog)) {
            return $this->sendError(trans('custom.mi_bulk_upload_error_log_not_found'));
        }

        $miBulkUploadErrorLog->delete();

        return $this->sendSuccess('Mi Bulk Upload Error Log deleted successfully');
    }

    public function getMiItemBulkUploadError(Request $request) {
        $materialIssueID = $request['materialIssueID'];
        $errorMsg = $this->miBulkUploadErrorLogRepository->getBulkUploadErrors($materialIssueID);

        return $this->sendResponse($errorMsg, trans('custom.material_issue_item_upload_error_log_status_fetche'));
    }

    public function deleteMiItemUploadErrorLog($id) {
        MiBulkUploadErrorLog::where('documentSystemID', trim($id))->delete();
        return $this->sendResponse([], trans('custom.material_issue_bulk_upload_error_log_deleted_succe'));
    }
}
