<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateCircularAmendmentsEditLogAPIRequest;
use App\Http\Requests\API\UpdateCircularAmendmentsEditLogAPIRequest;
use App\Models\CircularAmendmentsEditLog;
use App\Repositories\CircularAmendmentsEditLogRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class CircularAmendmentsEditLogController
 * @package App\Http\Controllers\API
 */

class CircularAmendmentsEditLogAPIController extends AppBaseController
{
    /** @var  CircularAmendmentsEditLogRepository */
    private $circularAmendmentsEditLogRepository;

    public function __construct(CircularAmendmentsEditLogRepository $circularAmendmentsEditLogRepo)
    {
        $this->circularAmendmentsEditLogRepository = $circularAmendmentsEditLogRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/circularAmendmentsEditLogs",
     *      summary="getCircularAmendmentsEditLogList",
     *      tags={"CircularAmendmentsEditLog"},
     *      description="Get all CircularAmendmentsEditLogs",
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
     *                  @OA\Items(ref="#/definitions/CircularAmendmentsEditLog")
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
        $this->circularAmendmentsEditLogRepository->pushCriteria(new RequestCriteria($request));
        $this->circularAmendmentsEditLogRepository->pushCriteria(new LimitOffsetCriteria($request));
        $circularAmendmentsEditLogs = $this->circularAmendmentsEditLogRepository->all();

        return $this->sendResponse($circularAmendmentsEditLogs->toArray(), trans('custom.circular_amendments_edit_logs_retrieved_successful'));
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/circularAmendmentsEditLogs",
     *      summary="createCircularAmendmentsEditLog",
     *      tags={"CircularAmendmentsEditLog"},
     *      description="Create CircularAmendmentsEditLog",
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
     *                  ref="#/definitions/CircularAmendmentsEditLog"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateCircularAmendmentsEditLogAPIRequest $request)
    {
        $input = $request->all();

        $circularAmendmentsEditLog = $this->circularAmendmentsEditLogRepository->create($input);

        return $this->sendResponse($circularAmendmentsEditLog->toArray(), trans('custom.circular_amendments_edit_log_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/circularAmendmentsEditLogs/{id}",
     *      summary="getCircularAmendmentsEditLogItem",
     *      tags={"CircularAmendmentsEditLog"},
     *      description="Get CircularAmendmentsEditLog",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of CircularAmendmentsEditLog",
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
     *                  ref="#/definitions/CircularAmendmentsEditLog"
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
        /** @var CircularAmendmentsEditLog $circularAmendmentsEditLog */
        $circularAmendmentsEditLog = $this->circularAmendmentsEditLogRepository->findWithoutFail($id);

        if (empty($circularAmendmentsEditLog)) {
            return $this->sendError(trans('custom.circular_amendments_edit_log_not_found'));
        }

        return $this->sendResponse($circularAmendmentsEditLog->toArray(), trans('custom.circular_amendments_edit_log_retrieved_successfull'));
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/circularAmendmentsEditLogs/{id}",
     *      summary="updateCircularAmendmentsEditLog",
     *      tags={"CircularAmendmentsEditLog"},
     *      description="Update CircularAmendmentsEditLog",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of CircularAmendmentsEditLog",
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
     *                  ref="#/definitions/CircularAmendmentsEditLog"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateCircularAmendmentsEditLogAPIRequest $request)
    {
        $input = $request->all();

        /** @var CircularAmendmentsEditLog $circularAmendmentsEditLog */
        $circularAmendmentsEditLog = $this->circularAmendmentsEditLogRepository->findWithoutFail($id);

        if (empty($circularAmendmentsEditLog)) {
            return $this->sendError(trans('custom.circular_amendments_edit_log_not_found'));
        }

        $circularAmendmentsEditLog = $this->circularAmendmentsEditLogRepository->update($input, $id);

        return $this->sendResponse($circularAmendmentsEditLog->toArray(), trans('custom.circularamendmentseditlog_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/circularAmendmentsEditLogs/{id}",
     *      summary="deleteCircularAmendmentsEditLog",
     *      tags={"CircularAmendmentsEditLog"},
     *      description="Delete CircularAmendmentsEditLog",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of CircularAmendmentsEditLog",
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
        /** @var CircularAmendmentsEditLog $circularAmendmentsEditLog */
        $circularAmendmentsEditLog = $this->circularAmendmentsEditLogRepository->findWithoutFail($id);

        if (empty($circularAmendmentsEditLog)) {
            return $this->sendError(trans('custom.circular_amendments_edit_log_not_found'));
        }

        $circularAmendmentsEditLog->delete();

        return $this->sendSuccess('Circular Amendments Edit Log deleted successfully');
    }
}
