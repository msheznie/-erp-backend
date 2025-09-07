<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateEvacuationCriteriaScoreConfigLogAPIRequest;
use App\Http\Requests\API\UpdateEvacuationCriteriaScoreConfigLogAPIRequest;
use App\Models\EvacuationCriteriaScoreConfigLog;
use App\Repositories\EvacuationCriteriaScoreConfigLogRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class EvacuationCriteriaScoreConfigLogController
 * @package App\Http\Controllers\API
 */

class EvacuationCriteriaScoreConfigLogAPIController extends AppBaseController
{
    /** @var  EvacuationCriteriaScoreConfigLogRepository */
    private $evacuationCriteriaScoreConfigLogRepository;

    public function __construct(EvacuationCriteriaScoreConfigLogRepository $evacuationCriteriaScoreConfigLogRepo)
    {
        $this->evacuationCriteriaScoreConfigLogRepository = $evacuationCriteriaScoreConfigLogRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/evacuationCriteriaScoreConfigLogs",
     *      summary="getEvacuationCriteriaScoreConfigLogList",
     *      tags={"EvacuationCriteriaScoreConfigLog"},
     *      description="Get all EvacuationCriteriaScoreConfigLogs",
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
     *                  @OA\Items(ref="#/definitions/EvacuationCriteriaScoreConfigLog")
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
        $this->evacuationCriteriaScoreConfigLogRepository->pushCriteria(new RequestCriteria($request));
        $this->evacuationCriteriaScoreConfigLogRepository->pushCriteria(new LimitOffsetCriteria($request));
        $evacuationCriteriaScoreConfigLogs = $this->evacuationCriteriaScoreConfigLogRepository->all();

        return $this->sendResponse($evacuationCriteriaScoreConfigLogs->toArray(), trans('custom.evacuation_criteria_score_config_logs_retrieved_su'));
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/evacuationCriteriaScoreConfigLogs",
     *      summary="createEvacuationCriteriaScoreConfigLog",
     *      tags={"EvacuationCriteriaScoreConfigLog"},
     *      description="Create EvacuationCriteriaScoreConfigLog",
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
     *                  ref="#/definitions/EvacuationCriteriaScoreConfigLog"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateEvacuationCriteriaScoreConfigLogAPIRequest $request)
    {
        $input = $request->all();

        $evacuationCriteriaScoreConfigLog = $this->evacuationCriteriaScoreConfigLogRepository->create($input);

        return $this->sendResponse($evacuationCriteriaScoreConfigLog->toArray(), trans('custom.evacuation_criteria_score_config_log_saved_success'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/evacuationCriteriaScoreConfigLogs/{id}",
     *      summary="getEvacuationCriteriaScoreConfigLogItem",
     *      tags={"EvacuationCriteriaScoreConfigLog"},
     *      description="Get EvacuationCriteriaScoreConfigLog",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of EvacuationCriteriaScoreConfigLog",
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
     *                  ref="#/definitions/EvacuationCriteriaScoreConfigLog"
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
        /** @var EvacuationCriteriaScoreConfigLog $evacuationCriteriaScoreConfigLog */
        $evacuationCriteriaScoreConfigLog = $this->evacuationCriteriaScoreConfigLogRepository->findWithoutFail($id);

        if (empty($evacuationCriteriaScoreConfigLog)) {
            return $this->sendError(trans('custom.evacuation_criteria_score_config_log_not_found'));
        }

        return $this->sendResponse($evacuationCriteriaScoreConfigLog->toArray(), trans('custom.evacuation_criteria_score_config_log_retrieved_suc'));
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/evacuationCriteriaScoreConfigLogs/{id}",
     *      summary="updateEvacuationCriteriaScoreConfigLog",
     *      tags={"EvacuationCriteriaScoreConfigLog"},
     *      description="Update EvacuationCriteriaScoreConfigLog",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of EvacuationCriteriaScoreConfigLog",
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
     *                  ref="#/definitions/EvacuationCriteriaScoreConfigLog"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateEvacuationCriteriaScoreConfigLogAPIRequest $request)
    {
        $input = $request->all();

        /** @var EvacuationCriteriaScoreConfigLog $evacuationCriteriaScoreConfigLog */
        $evacuationCriteriaScoreConfigLog = $this->evacuationCriteriaScoreConfigLogRepository->findWithoutFail($id);

        if (empty($evacuationCriteriaScoreConfigLog)) {
            return $this->sendError(trans('custom.evacuation_criteria_score_config_log_not_found'));
        }

        $evacuationCriteriaScoreConfigLog = $this->evacuationCriteriaScoreConfigLogRepository->update($input, $id);

        return $this->sendResponse($evacuationCriteriaScoreConfigLog->toArray(), trans('custom.evacuationcriteriascoreconfiglog_updated_successfu'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/evacuationCriteriaScoreConfigLogs/{id}",
     *      summary="deleteEvacuationCriteriaScoreConfigLog",
     *      tags={"EvacuationCriteriaScoreConfigLog"},
     *      description="Delete EvacuationCriteriaScoreConfigLog",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of EvacuationCriteriaScoreConfigLog",
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
        /** @var EvacuationCriteriaScoreConfigLog $evacuationCriteriaScoreConfigLog */
        $evacuationCriteriaScoreConfigLog = $this->evacuationCriteriaScoreConfigLogRepository->findWithoutFail($id);

        if (empty($evacuationCriteriaScoreConfigLog)) {
            return $this->sendError(trans('custom.evacuation_criteria_score_config_log_not_found'));
        }

        $evacuationCriteriaScoreConfigLog->delete();

        return $this->sendSuccess('Evacuation Criteria Score Config Log deleted successfully');
    }
}
