<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreatePricingScheduleMasterEditLogAPIRequest;
use App\Http\Requests\API\UpdatePricingScheduleMasterEditLogAPIRequest;
use App\Models\PricingScheduleMasterEditLog;
use App\Repositories\PricingScheduleMasterEditLogRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class PricingScheduleMasterEditLogController
 * @package App\Http\Controllers\API
 */

class PricingScheduleMasterEditLogAPIController extends AppBaseController
{
    /** @var  PricingScheduleMasterEditLogRepository */
    private $pricingScheduleMasterEditLogRepository;

    public function __construct(PricingScheduleMasterEditLogRepository $pricingScheduleMasterEditLogRepo)
    {
        $this->pricingScheduleMasterEditLogRepository = $pricingScheduleMasterEditLogRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/pricingScheduleMasterEditLogs",
     *      summary="getPricingScheduleMasterEditLogList",
     *      tags={"PricingScheduleMasterEditLog"},
     *      description="Get all PricingScheduleMasterEditLogs",
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
     *                  @OA\Items(ref="#/definitions/PricingScheduleMasterEditLog")
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
        $this->pricingScheduleMasterEditLogRepository->pushCriteria(new RequestCriteria($request));
        $this->pricingScheduleMasterEditLogRepository->pushCriteria(new LimitOffsetCriteria($request));
        $pricingScheduleMasterEditLogs = $this->pricingScheduleMasterEditLogRepository->all();

        return $this->sendResponse($pricingScheduleMasterEditLogs->toArray(), trans('custom.pricing_schedule_master_edit_logs_retrieved_succes'));
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/pricingScheduleMasterEditLogs",
     *      summary="createPricingScheduleMasterEditLog",
     *      tags={"PricingScheduleMasterEditLog"},
     *      description="Create PricingScheduleMasterEditLog",
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
     *                  ref="#/definitions/PricingScheduleMasterEditLog"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreatePricingScheduleMasterEditLogAPIRequest $request)
    {
        $input = $request->all();

        $pricingScheduleMasterEditLog = $this->pricingScheduleMasterEditLogRepository->create($input);

        return $this->sendResponse($pricingScheduleMasterEditLog->toArray(), trans('custom.pricing_schedule_master_edit_log_saved_successfull'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/pricingScheduleMasterEditLogs/{id}",
     *      summary="getPricingScheduleMasterEditLogItem",
     *      tags={"PricingScheduleMasterEditLog"},
     *      description="Get PricingScheduleMasterEditLog",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of PricingScheduleMasterEditLog",
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
     *                  ref="#/definitions/PricingScheduleMasterEditLog"
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
        /** @var PricingScheduleMasterEditLog $pricingScheduleMasterEditLog */
        $pricingScheduleMasterEditLog = $this->pricingScheduleMasterEditLogRepository->findWithoutFail($id);

        if (empty($pricingScheduleMasterEditLog)) {
            return $this->sendError(trans('custom.pricing_schedule_master_edit_log_not_found'));
        }

        return $this->sendResponse($pricingScheduleMasterEditLog->toArray(), trans('custom.pricing_schedule_master_edit_log_retrieved_success'));
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/pricingScheduleMasterEditLogs/{id}",
     *      summary="updatePricingScheduleMasterEditLog",
     *      tags={"PricingScheduleMasterEditLog"},
     *      description="Update PricingScheduleMasterEditLog",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of PricingScheduleMasterEditLog",
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
     *                  ref="#/definitions/PricingScheduleMasterEditLog"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdatePricingScheduleMasterEditLogAPIRequest $request)
    {
        $input = $request->all();

        /** @var PricingScheduleMasterEditLog $pricingScheduleMasterEditLog */
        $pricingScheduleMasterEditLog = $this->pricingScheduleMasterEditLogRepository->findWithoutFail($id);

        if (empty($pricingScheduleMasterEditLog)) {
            return $this->sendError(trans('custom.pricing_schedule_master_edit_log_not_found'));
        }

        $pricingScheduleMasterEditLog = $this->pricingScheduleMasterEditLogRepository->update($input, $id);

        return $this->sendResponse($pricingScheduleMasterEditLog->toArray(), trans('custom.pricingschedulemastereditlog_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/pricingScheduleMasterEditLogs/{id}",
     *      summary="deletePricingScheduleMasterEditLog",
     *      tags={"PricingScheduleMasterEditLog"},
     *      description="Delete PricingScheduleMasterEditLog",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of PricingScheduleMasterEditLog",
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
        /** @var PricingScheduleMasterEditLog $pricingScheduleMasterEditLog */
        $pricingScheduleMasterEditLog = $this->pricingScheduleMasterEditLogRepository->findWithoutFail($id);

        if (empty($pricingScheduleMasterEditLog)) {
            return $this->sendError(trans('custom.pricing_schedule_master_edit_log_not_found'));
        }

        $pricingScheduleMasterEditLog->delete();

        return $this->sendSuccess('Pricing Schedule Master Edit Log deleted successfully');
    }
}
