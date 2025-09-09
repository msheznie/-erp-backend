<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateCircularSuppliersEditLogAPIRequest;
use App\Http\Requests\API\UpdateCircularSuppliersEditLogAPIRequest;
use App\Models\CircularSuppliersEditLog;
use App\Repositories\CircularSuppliersEditLogRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class CircularSuppliersEditLogController
 * @package App\Http\Controllers\API
 */

class CircularSuppliersEditLogAPIController extends AppBaseController
{
    /** @var  CircularSuppliersEditLogRepository */
    private $circularSuppliersEditLogRepository;

    public function __construct(CircularSuppliersEditLogRepository $circularSuppliersEditLogRepo)
    {
        $this->circularSuppliersEditLogRepository = $circularSuppliersEditLogRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/circularSuppliersEditLogs",
     *      summary="getCircularSuppliersEditLogList",
     *      tags={"CircularSuppliersEditLog"},
     *      description="Get all CircularSuppliersEditLogs",
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
     *                  @OA\Items(ref="#/definitions/CircularSuppliersEditLog")
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
        $this->circularSuppliersEditLogRepository->pushCriteria(new RequestCriteria($request));
        $this->circularSuppliersEditLogRepository->pushCriteria(new LimitOffsetCriteria($request));
        $circularSuppliersEditLogs = $this->circularSuppliersEditLogRepository->all();

        return $this->sendResponse($circularSuppliersEditLogs->toArray(), trans('custom.circular_suppliers_edit_logs_retrieved_successfull'));
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/circularSuppliersEditLogs",
     *      summary="createCircularSuppliersEditLog",
     *      tags={"CircularSuppliersEditLog"},
     *      description="Create CircularSuppliersEditLog",
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
     *                  ref="#/definitions/CircularSuppliersEditLog"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateCircularSuppliersEditLogAPIRequest $request)
    {
        $input = $request->all();

        $circularSuppliersEditLog = $this->circularSuppliersEditLogRepository->create($input);

        return $this->sendResponse($circularSuppliersEditLog->toArray(), trans('custom.circular_suppliers_edit_log_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/circularSuppliersEditLogs/{id}",
     *      summary="getCircularSuppliersEditLogItem",
     *      tags={"CircularSuppliersEditLog"},
     *      description="Get CircularSuppliersEditLog",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of CircularSuppliersEditLog",
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
     *                  ref="#/definitions/CircularSuppliersEditLog"
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
        /** @var CircularSuppliersEditLog $circularSuppliersEditLog */
        $circularSuppliersEditLog = $this->circularSuppliersEditLogRepository->findWithoutFail($id);

        if (empty($circularSuppliersEditLog)) {
            return $this->sendError(trans('custom.circular_suppliers_edit_log_not_found'));
        }

        return $this->sendResponse($circularSuppliersEditLog->toArray(), trans('custom.circular_suppliers_edit_log_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/circularSuppliersEditLogs/{id}",
     *      summary="updateCircularSuppliersEditLog",
     *      tags={"CircularSuppliersEditLog"},
     *      description="Update CircularSuppliersEditLog",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of CircularSuppliersEditLog",
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
     *                  ref="#/definitions/CircularSuppliersEditLog"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateCircularSuppliersEditLogAPIRequest $request)
    {
        $input = $request->all();

        /** @var CircularSuppliersEditLog $circularSuppliersEditLog */
        $circularSuppliersEditLog = $this->circularSuppliersEditLogRepository->findWithoutFail($id);

        if (empty($circularSuppliersEditLog)) {
            return $this->sendError(trans('custom.circular_suppliers_edit_log_not_found'));
        }

        $circularSuppliersEditLog = $this->circularSuppliersEditLogRepository->update($input, $id);

        return $this->sendResponse($circularSuppliersEditLog->toArray(), trans('custom.circularsupplierseditlog_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/circularSuppliersEditLogs/{id}",
     *      summary="deleteCircularSuppliersEditLog",
     *      tags={"CircularSuppliersEditLog"},
     *      description="Delete CircularSuppliersEditLog",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of CircularSuppliersEditLog",
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
        /** @var CircularSuppliersEditLog $circularSuppliersEditLog */
        $circularSuppliersEditLog = $this->circularSuppliersEditLogRepository->findWithoutFail($id);

        if (empty($circularSuppliersEditLog)) {
            return $this->sendError(trans('custom.circular_suppliers_edit_log_not_found'));
        }

        $circularSuppliersEditLog->delete();

        return $this->sendSuccess('Circular Suppliers Edit Log deleted successfully');
    }
}
