<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateWorkOrderGenerationLogAPIRequest;
use App\Http\Requests\API\UpdateWorkOrderGenerationLogAPIRequest;
use App\Models\WorkOrderGenerationLog;
use App\Repositories\WorkOrderGenerationLogRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class WorkOrderGenerationLogController
 * @package App\Http\Controllers\API
 */

class WorkOrderGenerationLogAPIController extends AppBaseController
{
    /** @var  WorkOrderGenerationLogRepository */
    private $workOrderGenerationLogRepository;

    public function __construct(WorkOrderGenerationLogRepository $workOrderGenerationLogRepo)
    {
        $this->workOrderGenerationLogRepository = $workOrderGenerationLogRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/workOrderGenerationLogs",
     *      summary="Get a listing of the WorkOrderGenerationLogs.",
     *      tags={"WorkOrderGenerationLog"},
     *      description="Get all WorkOrderGenerationLogs",
     *      produces={"application/json"},
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  type="array",
     *                  @SWG\Items(ref="#/definitions/WorkOrderGenerationLog")
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function index(Request $request)
    {
        $this->workOrderGenerationLogRepository->pushCriteria(new RequestCriteria($request));
        $this->workOrderGenerationLogRepository->pushCriteria(new LimitOffsetCriteria($request));
        $workOrderGenerationLogs = $this->workOrderGenerationLogRepository->all();

        return $this->sendResponse($workOrderGenerationLogs->toArray(), trans('custom.work_order_generation_logs_retrieved_successfully'));
    }

    /**
     * @param CreateWorkOrderGenerationLogAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/workOrderGenerationLogs",
     *      summary="Store a newly created WorkOrderGenerationLog in storage",
     *      tags={"WorkOrderGenerationLog"},
     *      description="Store WorkOrderGenerationLog",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="WorkOrderGenerationLog that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/WorkOrderGenerationLog")
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/WorkOrderGenerationLog"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateWorkOrderGenerationLogAPIRequest $request)
    {
        $input = $request->all();

        $workOrderGenerationLog = $this->workOrderGenerationLogRepository->create($input);

        return $this->sendResponse($workOrderGenerationLog->toArray(), trans('custom.work_order_generation_log_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/workOrderGenerationLogs/{id}",
     *      summary="Display the specified WorkOrderGenerationLog",
     *      tags={"WorkOrderGenerationLog"},
     *      description="Get WorkOrderGenerationLog",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of WorkOrderGenerationLog",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/WorkOrderGenerationLog"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function show($id)
    {
        /** @var WorkOrderGenerationLog $workOrderGenerationLog */
        $workOrderGenerationLog = $this->workOrderGenerationLogRepository->findWithoutFail($id);

        if (empty($workOrderGenerationLog)) {
            return $this->sendError(trans('custom.work_order_generation_log_not_found'));
        }

        return $this->sendResponse($workOrderGenerationLog->toArray(), trans('custom.work_order_generation_log_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateWorkOrderGenerationLogAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/workOrderGenerationLogs/{id}",
     *      summary="Update the specified WorkOrderGenerationLog in storage",
     *      tags={"WorkOrderGenerationLog"},
     *      description="Update WorkOrderGenerationLog",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of WorkOrderGenerationLog",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="WorkOrderGenerationLog that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/WorkOrderGenerationLog")
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/WorkOrderGenerationLog"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateWorkOrderGenerationLogAPIRequest $request)
    {
        $input = $request->all();

        /** @var WorkOrderGenerationLog $workOrderGenerationLog */
        $workOrderGenerationLog = $this->workOrderGenerationLogRepository->findWithoutFail($id);

        if (empty($workOrderGenerationLog)) {
            return $this->sendError(trans('custom.work_order_generation_log_not_found'));
        }

        $workOrderGenerationLog = $this->workOrderGenerationLogRepository->update($input, $id);

        return $this->sendResponse($workOrderGenerationLog->toArray(), trans('custom.workordergenerationlog_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/workOrderGenerationLogs/{id}",
     *      summary="Remove the specified WorkOrderGenerationLog from storage",
     *      tags={"WorkOrderGenerationLog"},
     *      description="Delete WorkOrderGenerationLog",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of WorkOrderGenerationLog",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function destroy($id)
    {
        /** @var WorkOrderGenerationLog $workOrderGenerationLog */
        $workOrderGenerationLog = $this->workOrderGenerationLogRepository->findWithoutFail($id);

        if (empty($workOrderGenerationLog)) {
            return $this->sendError(trans('custom.work_order_generation_log_not_found'));
        }

        $workOrderGenerationLog->delete();

        return $this->sendSuccess('Work Order Generation Log deleted successfully');
    }
}
