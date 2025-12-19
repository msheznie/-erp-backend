<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateJobErrorLogAPIRequest;
use App\Http\Requests\API\UpdateJobErrorLogAPIRequest;
use App\Models\JobErrorLog;
use App\Repositories\JobErrorLogRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class JobErrorLogController
 * @package App\Http\Controllers\API
 */

class JobErrorLogAPIController extends AppBaseController
{
    /** @var  JobErrorLogRepository */
    private $jobErrorLogRepository;

    public function __construct(JobErrorLogRepository $jobErrorLogRepo)
    {
        $this->jobErrorLogRepository = $jobErrorLogRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/jobErrorLogs",
     *      summary="Get a listing of the JobErrorLogs.",
     *      tags={"JobErrorLog"},
     *      description="Get all JobErrorLogs",
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
     *                  @SWG\Items(ref="#/definitions/JobErrorLog")
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
        $this->jobErrorLogRepository->pushCriteria(new RequestCriteria($request));
        $this->jobErrorLogRepository->pushCriteria(new LimitOffsetCriteria($request));
        $jobErrorLogs = $this->jobErrorLogRepository->all();

        return $this->sendResponse($jobErrorLogs->toArray(), trans('custom.job_error_logs_retrieved_successfully'));
    }

    /**
     * @param CreateJobErrorLogAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/jobErrorLogs",
     *      summary="Store a newly created JobErrorLog in storage",
     *      tags={"JobErrorLog"},
     *      description="Store JobErrorLog",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="JobErrorLog that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/JobErrorLog")
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
     *                  ref="#/definitions/JobErrorLog"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateJobErrorLogAPIRequest $request)
    {
        $input = $request->all();

        $jobErrorLog = $this->jobErrorLogRepository->create($input);

        return $this->sendResponse($jobErrorLog->toArray(), trans('custom.job_error_log_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/jobErrorLogs/{id}",
     *      summary="Display the specified JobErrorLog",
     *      tags={"JobErrorLog"},
     *      description="Get JobErrorLog",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of JobErrorLog",
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
     *                  ref="#/definitions/JobErrorLog"
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
        /** @var JobErrorLog $jobErrorLog */
        $jobErrorLog = $this->jobErrorLogRepository->findWithoutFail($id);

        if (empty($jobErrorLog)) {
            return $this->sendError(trans('custom.job_error_log_not_found'));
        }

        return $this->sendResponse($jobErrorLog->toArray(), trans('custom.job_error_log_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateJobErrorLogAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/jobErrorLogs/{id}",
     *      summary="Update the specified JobErrorLog in storage",
     *      tags={"JobErrorLog"},
     *      description="Update JobErrorLog",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of JobErrorLog",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="JobErrorLog that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/JobErrorLog")
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
     *                  ref="#/definitions/JobErrorLog"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateJobErrorLogAPIRequest $request)
    {
        $input = $request->all();

        /** @var JobErrorLog $jobErrorLog */
        $jobErrorLog = $this->jobErrorLogRepository->findWithoutFail($id);

        if (empty($jobErrorLog)) {
            return $this->sendError(trans('custom.job_error_log_not_found'));
        }

        $jobErrorLog = $this->jobErrorLogRepository->update($input, $id);

        return $this->sendResponse($jobErrorLog->toArray(), trans('custom.joberrorlog_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/jobErrorLogs/{id}",
     *      summary="Remove the specified JobErrorLog from storage",
     *      tags={"JobErrorLog"},
     *      description="Delete JobErrorLog",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of JobErrorLog",
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
        /** @var JobErrorLog $jobErrorLog */
        $jobErrorLog = $this->jobErrorLogRepository->findWithoutFail($id);

        if (empty($jobErrorLog)) {
            return $this->sendError(trans('custom.job_error_log_not_found'));
        }

        $jobErrorLog->delete();

        return $this->sendSuccess('Job Error Log deleted successfully');
    }
}
