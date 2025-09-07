<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateProcumentActivityEditLogAPIRequest;
use App\Http\Requests\API\UpdateProcumentActivityEditLogAPIRequest;
use App\Models\ProcumentActivityEditLog;
use App\Repositories\ProcumentActivityEditLogRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class ProcumentActivityEditLogController
 * @package App\Http\Controllers\API
 */

class ProcumentActivityEditLogAPIController extends AppBaseController
{
    /** @var  ProcumentActivityEditLogRepository */
    private $procumentActivityEditLogRepository;

    public function __construct(ProcumentActivityEditLogRepository $procumentActivityEditLogRepo)
    {
        $this->procumentActivityEditLogRepository = $procumentActivityEditLogRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/procumentActivityEditLogs",
     *      summary="getProcumentActivityEditLogList",
     *      tags={"ProcumentActivityEditLog"},
     *      description="Get all ProcumentActivityEditLogs",
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
     *                  @OA\Items(ref="#/definitions/ProcumentActivityEditLog")
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
        $this->procumentActivityEditLogRepository->pushCriteria(new RequestCriteria($request));
        $this->procumentActivityEditLogRepository->pushCriteria(new LimitOffsetCriteria($request));
        $procumentActivityEditLogs = $this->procumentActivityEditLogRepository->all();

        return $this->sendResponse($procumentActivityEditLogs->toArray(), trans('custom.procument_activity_edit_logs_retrieved_successfull'));
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/procumentActivityEditLogs",
     *      summary="createProcumentActivityEditLog",
     *      tags={"ProcumentActivityEditLog"},
     *      description="Create ProcumentActivityEditLog",
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
     *                  ref="#/definitions/ProcumentActivityEditLog"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateProcumentActivityEditLogAPIRequest $request)
    {
        $input = $request->all();

        $procumentActivityEditLog = $this->procumentActivityEditLogRepository->create($input);

        return $this->sendResponse($procumentActivityEditLog->toArray(), trans('custom.procument_activity_edit_log_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/procumentActivityEditLogs/{id}",
     *      summary="getProcumentActivityEditLogItem",
     *      tags={"ProcumentActivityEditLog"},
     *      description="Get ProcumentActivityEditLog",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of ProcumentActivityEditLog",
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
     *                  ref="#/definitions/ProcumentActivityEditLog"
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
        /** @var ProcumentActivityEditLog $procumentActivityEditLog */
        $procumentActivityEditLog = $this->procumentActivityEditLogRepository->findWithoutFail($id);

        if (empty($procumentActivityEditLog)) {
            return $this->sendError(trans('custom.procument_activity_edit_log_not_found'));
        }

        return $this->sendResponse($procumentActivityEditLog->toArray(), trans('custom.procument_activity_edit_log_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/procumentActivityEditLogs/{id}",
     *      summary="updateProcumentActivityEditLog",
     *      tags={"ProcumentActivityEditLog"},
     *      description="Update ProcumentActivityEditLog",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of ProcumentActivityEditLog",
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
     *                  ref="#/definitions/ProcumentActivityEditLog"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateProcumentActivityEditLogAPIRequest $request)
    {
        $input = $request->all();

        /** @var ProcumentActivityEditLog $procumentActivityEditLog */
        $procumentActivityEditLog = $this->procumentActivityEditLogRepository->findWithoutFail($id);

        if (empty($procumentActivityEditLog)) {
            return $this->sendError(trans('custom.procument_activity_edit_log_not_found'));
        }

        $procumentActivityEditLog = $this->procumentActivityEditLogRepository->update($input, $id);

        return $this->sendResponse($procumentActivityEditLog->toArray(), trans('custom.procumentactivityeditlog_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/procumentActivityEditLogs/{id}",
     *      summary="deleteProcumentActivityEditLog",
     *      tags={"ProcumentActivityEditLog"},
     *      description="Delete ProcumentActivityEditLog",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of ProcumentActivityEditLog",
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
        /** @var ProcumentActivityEditLog $procumentActivityEditLog */
        $procumentActivityEditLog = $this->procumentActivityEditLogRepository->findWithoutFail($id);

        if (empty($procumentActivityEditLog)) {
            return $this->sendError(trans('custom.procument_activity_edit_log_not_found'));
        }

        $procumentActivityEditLog->delete();

        return $this->sendSuccess('Procument Activity Edit Log deleted successfully');
    }
}
