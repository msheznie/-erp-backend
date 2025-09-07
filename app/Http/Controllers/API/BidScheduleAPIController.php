<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateBidScheduleAPIRequest;
use App\Http\Requests\API\UpdateBidScheduleAPIRequest;
use App\Models\BidSchedule;
use App\Repositories\BidScheduleRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class BidScheduleController
 * @package App\Http\Controllers\API
 */

class BidScheduleAPIController extends AppBaseController
{
    /** @var  BidScheduleRepository */
    private $bidScheduleRepository;

    public function __construct(BidScheduleRepository $bidScheduleRepo)
    {
        $this->bidScheduleRepository = $bidScheduleRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/bidSchedules",
     *      summary="Get a listing of the BidSchedules.",
     *      tags={"BidSchedule"},
     *      description="Get all BidSchedules",
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
     *                  @SWG\Items(ref="#/definitions/BidSchedule")
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
        $this->bidScheduleRepository->pushCriteria(new RequestCriteria($request));
        $this->bidScheduleRepository->pushCriteria(new LimitOffsetCriteria($request));
        $bidSchedules = $this->bidScheduleRepository->all();

        return $this->sendResponse($bidSchedules->toArray(), trans('custom.bid_schedules_retrieved_successfully'));
    }

    /**
     * @param CreateBidScheduleAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/bidSchedules",
     *      summary="Store a newly created BidSchedule in storage",
     *      tags={"BidSchedule"},
     *      description="Store BidSchedule",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="BidSchedule that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/BidSchedule")
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
     *                  ref="#/definitions/BidSchedule"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateBidScheduleAPIRequest $request)
    {
        $input = $request->all();

        $bidSchedule = $this->bidScheduleRepository->create($input);

        return $this->sendResponse($bidSchedule->toArray(), trans('custom.bid_schedule_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/bidSchedules/{id}",
     *      summary="Display the specified BidSchedule",
     *      tags={"BidSchedule"},
     *      description="Get BidSchedule",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of BidSchedule",
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
     *                  ref="#/definitions/BidSchedule"
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
        /** @var BidSchedule $bidSchedule */
        $bidSchedule = $this->bidScheduleRepository->findWithoutFail($id);

        if (empty($bidSchedule)) {
            return $this->sendError(trans('custom.bid_schedule_not_found'));
        }

        return $this->sendResponse($bidSchedule->toArray(), trans('custom.bid_schedule_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateBidScheduleAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/bidSchedules/{id}",
     *      summary="Update the specified BidSchedule in storage",
     *      tags={"BidSchedule"},
     *      description="Update BidSchedule",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of BidSchedule",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="BidSchedule that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/BidSchedule")
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
     *                  ref="#/definitions/BidSchedule"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateBidScheduleAPIRequest $request)
    {
        $input = $request->all();

        /** @var BidSchedule $bidSchedule */
        $bidSchedule = $this->bidScheduleRepository->findWithoutFail($id);

        if (empty($bidSchedule)) {
            return $this->sendError(trans('custom.bid_schedule_not_found'));
        }

        $bidSchedule = $this->bidScheduleRepository->update($input, $id);

        return $this->sendResponse($bidSchedule->toArray(), trans('custom.bidschedule_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/bidSchedules/{id}",
     *      summary="Remove the specified BidSchedule from storage",
     *      tags={"BidSchedule"},
     *      description="Delete BidSchedule",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of BidSchedule",
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
        /** @var BidSchedule $bidSchedule */
        $bidSchedule = $this->bidScheduleRepository->findWithoutFail($id);

        if (empty($bidSchedule)) {
            return $this->sendError(trans('custom.bid_schedule_not_found'));
        }

        $bidSchedule->delete();

        return $this->sendSuccess('Bid Schedule deleted successfully');
    }
}
