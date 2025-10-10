<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateEvaluationCriteriaDetailsEditLogAPIRequest;
use App\Http\Requests\API\UpdateEvaluationCriteriaDetailsEditLogAPIRequest;
use App\Models\EvaluationCriteriaDetailsEditLog;
use App\Repositories\EvaluationCriteriaDetailsEditLogRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class EvaluationCriteriaDetailsEditLogController
 * @package App\Http\Controllers\API
 */

class EvaluationCriteriaDetailsEditLogAPIController extends AppBaseController
{
    /** @var  EvaluationCriteriaDetailsEditLogRepository */
    private $evaluationCriteriaDetailsEditLogRepository;

    public function __construct(EvaluationCriteriaDetailsEditLogRepository $evaluationCriteriaDetailsEditLogRepo)
    {
        $this->evaluationCriteriaDetailsEditLogRepository = $evaluationCriteriaDetailsEditLogRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/evaluationCriteriaDetailsEditLogs",
     *      summary="getEvaluationCriteriaDetailsEditLogList",
     *      tags={"EvaluationCriteriaDetailsEditLog"},
     *      description="Get all EvaluationCriteriaDetailsEditLogs",
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
     *                  @OA\Items(ref="#/definitions/EvaluationCriteriaDetailsEditLog")
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
        $this->evaluationCriteriaDetailsEditLogRepository->pushCriteria(new RequestCriteria($request));
        $this->evaluationCriteriaDetailsEditLogRepository->pushCriteria(new LimitOffsetCriteria($request));
        $evaluationCriteriaDetailsEditLogs = $this->evaluationCriteriaDetailsEditLogRepository->all();

        return $this->sendResponse($evaluationCriteriaDetailsEditLogs->toArray(), trans('custom.evaluation_criteria_details_edit_logs_retrieved_su'));
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/evaluationCriteriaDetailsEditLogs",
     *      summary="createEvaluationCriteriaDetailsEditLog",
     *      tags={"EvaluationCriteriaDetailsEditLog"},
     *      description="Create EvaluationCriteriaDetailsEditLog",
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
     *                  ref="#/definitions/EvaluationCriteriaDetailsEditLog"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateEvaluationCriteriaDetailsEditLogAPIRequest $request)
    {
        $input = $request->all();

        $evaluationCriteriaDetailsEditLog = $this->evaluationCriteriaDetailsEditLogRepository->create($input);

        return $this->sendResponse($evaluationCriteriaDetailsEditLog->toArray(), trans('custom.evaluation_criteria_details_edit_log_saved_success'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/evaluationCriteriaDetailsEditLogs/{id}",
     *      summary="getEvaluationCriteriaDetailsEditLogItem",
     *      tags={"EvaluationCriteriaDetailsEditLog"},
     *      description="Get EvaluationCriteriaDetailsEditLog",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of EvaluationCriteriaDetailsEditLog",
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
     *                  ref="#/definitions/EvaluationCriteriaDetailsEditLog"
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
        /** @var EvaluationCriteriaDetailsEditLog $evaluationCriteriaDetailsEditLog */
        $evaluationCriteriaDetailsEditLog = $this->evaluationCriteriaDetailsEditLogRepository->findWithoutFail($id);

        if (empty($evaluationCriteriaDetailsEditLog)) {
            return $this->sendError(trans('custom.evaluation_criteria_details_edit_log_not_found'));
        }

        return $this->sendResponse($evaluationCriteriaDetailsEditLog->toArray(), trans('custom.evaluation_criteria_details_edit_log_retrieved_suc'));
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/evaluationCriteriaDetailsEditLogs/{id}",
     *      summary="updateEvaluationCriteriaDetailsEditLog",
     *      tags={"EvaluationCriteriaDetailsEditLog"},
     *      description="Update EvaluationCriteriaDetailsEditLog",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of EvaluationCriteriaDetailsEditLog",
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
     *                  ref="#/definitions/EvaluationCriteriaDetailsEditLog"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateEvaluationCriteriaDetailsEditLogAPIRequest $request)
    {
        $input = $request->all();

        /** @var EvaluationCriteriaDetailsEditLog $evaluationCriteriaDetailsEditLog */
        $evaluationCriteriaDetailsEditLog = $this->evaluationCriteriaDetailsEditLogRepository->findWithoutFail($id);

        if (empty($evaluationCriteriaDetailsEditLog)) {
            return $this->sendError(trans('custom.evaluation_criteria_details_edit_log_not_found'));
        }

        $evaluationCriteriaDetailsEditLog = $this->evaluationCriteriaDetailsEditLogRepository->update($input, $id);

        return $this->sendResponse($evaluationCriteriaDetailsEditLog->toArray(), trans('custom.evaluationcriteriadetailseditlog_updated_successfu'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/evaluationCriteriaDetailsEditLogs/{id}",
     *      summary="deleteEvaluationCriteriaDetailsEditLog",
     *      tags={"EvaluationCriteriaDetailsEditLog"},
     *      description="Delete EvaluationCriteriaDetailsEditLog",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of EvaluationCriteriaDetailsEditLog",
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
        /** @var EvaluationCriteriaDetailsEditLog $evaluationCriteriaDetailsEditLog */
        $evaluationCriteriaDetailsEditLog = $this->evaluationCriteriaDetailsEditLogRepository->findWithoutFail($id);

        if (empty($evaluationCriteriaDetailsEditLog)) {
            return $this->sendError(trans('custom.evaluation_criteria_details_edit_log_not_found'));
        }

        $evaluationCriteriaDetailsEditLog->delete();

        return $this->sendSuccess('Evaluation Criteria Details Edit Log deleted successfully');
    }
}
