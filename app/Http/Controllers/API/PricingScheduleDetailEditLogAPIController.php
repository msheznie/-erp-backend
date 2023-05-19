<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreatePricingScheduleDetailEditLogAPIRequest;
use App\Http\Requests\API\UpdatePricingScheduleDetailEditLogAPIRequest;
use App\Models\PricingScheduleDetailEditLog;
use App\Repositories\PricingScheduleDetailEditLogRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class PricingScheduleDetailEditLogController
 * @package App\Http\Controllers\API
 */

class PricingScheduleDetailEditLogAPIController extends AppBaseController
{
    /** @var  PricingScheduleDetailEditLogRepository */
    private $pricingScheduleDetailEditLogRepository;

    public function __construct(PricingScheduleDetailEditLogRepository $pricingScheduleDetailEditLogRepo)
    {
        $this->pricingScheduleDetailEditLogRepository = $pricingScheduleDetailEditLogRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/pricingScheduleDetailEditLogs",
     *      summary="getPricingScheduleDetailEditLogList",
     *      tags={"PricingScheduleDetailEditLog"},
     *      description="Get all PricingScheduleDetailEditLogs",
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
     *                  @OA\Items(ref="#/definitions/PricingScheduleDetailEditLog")
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
        $this->pricingScheduleDetailEditLogRepository->pushCriteria(new RequestCriteria($request));
        $this->pricingScheduleDetailEditLogRepository->pushCriteria(new LimitOffsetCriteria($request));
        $pricingScheduleDetailEditLogs = $this->pricingScheduleDetailEditLogRepository->all();

        return $this->sendResponse($pricingScheduleDetailEditLogs->toArray(), 'Pricing Schedule Detail Edit Logs retrieved successfully');
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/pricingScheduleDetailEditLogs",
     *      summary="createPricingScheduleDetailEditLog",
     *      tags={"PricingScheduleDetailEditLog"},
     *      description="Create PricingScheduleDetailEditLog",
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
     *                  ref="#/definitions/PricingScheduleDetailEditLog"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreatePricingScheduleDetailEditLogAPIRequest $request)
    {
        $input = $request->all();

        $pricingScheduleDetailEditLog = $this->pricingScheduleDetailEditLogRepository->create($input);

        return $this->sendResponse($pricingScheduleDetailEditLog->toArray(), 'Pricing Schedule Detail Edit Log saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/pricingScheduleDetailEditLogs/{id}",
     *      summary="getPricingScheduleDetailEditLogItem",
     *      tags={"PricingScheduleDetailEditLog"},
     *      description="Get PricingScheduleDetailEditLog",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of PricingScheduleDetailEditLog",
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
     *                  ref="#/definitions/PricingScheduleDetailEditLog"
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
        /** @var PricingScheduleDetailEditLog $pricingScheduleDetailEditLog */
        $pricingScheduleDetailEditLog = $this->pricingScheduleDetailEditLogRepository->findWithoutFail($id);

        if (empty($pricingScheduleDetailEditLog)) {
            return $this->sendError('Pricing Schedule Detail Edit Log not found');
        }

        return $this->sendResponse($pricingScheduleDetailEditLog->toArray(), 'Pricing Schedule Detail Edit Log retrieved successfully');
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/pricingScheduleDetailEditLogs/{id}",
     *      summary="updatePricingScheduleDetailEditLog",
     *      tags={"PricingScheduleDetailEditLog"},
     *      description="Update PricingScheduleDetailEditLog",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of PricingScheduleDetailEditLog",
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
     *                  ref="#/definitions/PricingScheduleDetailEditLog"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdatePricingScheduleDetailEditLogAPIRequest $request)
    {
        $input = $request->all();

        /** @var PricingScheduleDetailEditLog $pricingScheduleDetailEditLog */
        $pricingScheduleDetailEditLog = $this->pricingScheduleDetailEditLogRepository->findWithoutFail($id);

        if (empty($pricingScheduleDetailEditLog)) {
            return $this->sendError('Pricing Schedule Detail Edit Log not found');
        }

        $pricingScheduleDetailEditLog = $this->pricingScheduleDetailEditLogRepository->update($input, $id);

        return $this->sendResponse($pricingScheduleDetailEditLog->toArray(), 'PricingScheduleDetailEditLog updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/pricingScheduleDetailEditLogs/{id}",
     *      summary="deletePricingScheduleDetailEditLog",
     *      tags={"PricingScheduleDetailEditLog"},
     *      description="Delete PricingScheduleDetailEditLog",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of PricingScheduleDetailEditLog",
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
        /** @var PricingScheduleDetailEditLog $pricingScheduleDetailEditLog */
        $pricingScheduleDetailEditLog = $this->pricingScheduleDetailEditLogRepository->findWithoutFail($id);

        if (empty($pricingScheduleDetailEditLog)) {
            return $this->sendError('Pricing Schedule Detail Edit Log not found');
        }

        $pricingScheduleDetailEditLog->delete();

        return $this->sendSuccess('Pricing Schedule Detail Edit Log deleted successfully');
    }
}
