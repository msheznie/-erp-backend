<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreatePoBulkUploadErrorLogAPIRequest;
use App\Http\Requests\API\UpdatePoBulkUploadErrorLogAPIRequest;
use App\Models\PoBulkUploadErrorLog;
use App\Models\ProcumentOrder;
use App\Repositories\PoBulkUploadErrorLogRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class PoBulkUploadErrorLogController
 * @package App\Http\Controllers\API
 */

class PoBulkUploadErrorLogAPIController extends AppBaseController
{
    /** @var  PoBulkUploadErrorLogRepository */
    private $poBulkUploadErrorLogRepository;

    public function __construct(PoBulkUploadErrorLogRepository $poBulkUploadErrorLogRepo)
    {
        $this->poBulkUploadErrorLogRepository = $poBulkUploadErrorLogRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/poBulkUploadErrorLogs",
     *      summary="getPoBulkUploadErrorLogList",
     *      tags={"PoBulkUploadErrorLog"},
     *      description="Get all PoBulkUploadErrorLogs",
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
     *                  @OA\Items(ref="#/definitions/PoBulkUploadErrorLog")
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
        $this->poBulkUploadErrorLogRepository->pushCriteria(new RequestCriteria($request));
        $this->poBulkUploadErrorLogRepository->pushCriteria(new LimitOffsetCriteria($request));
        $poBulkUploadErrorLogs = $this->poBulkUploadErrorLogRepository->all();

        return $this->sendResponse($poBulkUploadErrorLogs->toArray(), trans('custom.po_bulk_upload_error_logs_retrieved_successfully'));
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/poBulkUploadErrorLogs",
     *      summary="createPoBulkUploadErrorLog",
     *      tags={"PoBulkUploadErrorLog"},
     *      description="Create PoBulkUploadErrorLog",
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
     *                  ref="#/definitions/PoBulkUploadErrorLog"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreatePoBulkUploadErrorLogAPIRequest $request)
    {
        $input = $request->all();

        $poBulkUploadErrorLog = $this->poBulkUploadErrorLogRepository->create($input);

        return $this->sendResponse($poBulkUploadErrorLog->toArray(), trans('custom.po_bulk_upload_error_log_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/poBulkUploadErrorLogs/{id}",
     *      summary="getPoBulkUploadErrorLogItem",
     *      tags={"PoBulkUploadErrorLog"},
     *      description="Get PoBulkUploadErrorLog",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of PoBulkUploadErrorLog",
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
     *                  ref="#/definitions/PoBulkUploadErrorLog"
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
        /** @var PoBulkUploadErrorLog $poBulkUploadErrorLog */
        $poBulkUploadErrorLog = $this->poBulkUploadErrorLogRepository->findWithoutFail($id);

        if (empty($poBulkUploadErrorLog)) {
            return $this->sendError(trans('custom.po_bulk_upload_error_log_not_found'));
        }

        return $this->sendResponse($poBulkUploadErrorLog->toArray(), trans('custom.po_bulk_upload_error_log_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/poBulkUploadErrorLogs/{id}",
     *      summary="updatePoBulkUploadErrorLog",
     *      tags={"PoBulkUploadErrorLog"},
     *      description="Update PoBulkUploadErrorLog",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of PoBulkUploadErrorLog",
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
     *                  ref="#/definitions/PoBulkUploadErrorLog"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdatePoBulkUploadErrorLogAPIRequest $request)
    {
        $input = $request->all();

        /** @var PoBulkUploadErrorLog $poBulkUploadErrorLog */
        $poBulkUploadErrorLog = $this->poBulkUploadErrorLogRepository->findWithoutFail($id);

        if (empty($poBulkUploadErrorLog)) {
            return $this->sendError(trans('custom.po_bulk_upload_error_log_not_found'));
        }

        $poBulkUploadErrorLog = $this->poBulkUploadErrorLogRepository->update($input, $id);

        return $this->sendResponse($poBulkUploadErrorLog->toArray(), trans('custom.pobulkuploaderrorlog_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/poBulkUploadErrorLogs/{id}",
     *      summary="deletePoBulkUploadErrorLog",
     *      tags={"PoBulkUploadErrorLog"},
     *      description="Delete PoBulkUploadErrorLog",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of PoBulkUploadErrorLog",
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
        /** @var PoBulkUploadErrorLog $poBulkUploadErrorLog */
        $poBulkUploadErrorLog = $this->poBulkUploadErrorLogRepository->findWithoutFail($id);

        if (empty($poBulkUploadErrorLog)) {
            return $this->sendError(trans('custom.po_bulk_upload_error_log_not_found'));
        }

        $poBulkUploadErrorLog->delete();

        return $this->sendSuccess(trans('custom.po_bulk_upload_error_log_deleted_successfully'));
    }

    public function getItemBulkUploadError(Request $request)
    {
        $purchaseOrderID = $request['purchaseOrderID'];
        $errorMsg = $this->poBulkUploadErrorLogRepository->getBulkUploadErrors($purchaseOrderID);

        return $this->sendResponse($errorMsg, trans('custom.purchase_order_item_upload_error_log_status_fetche'));
    }
    public function deletePoItemUploadErrorLog($id)
    {
        PoBulkUploadErrorLog::where('documentSystemID', trim($id))->delete();
        return $this->sendResponse([], trans('custom.po_bulk_upload_error_log_deleted_successfully'));
    }
}
