<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateScheduleBidFormatDetailsAPIRequest;
use App\Http\Requests\API\UpdateScheduleBidFormatDetailsAPIRequest;
use App\Models\ScheduleBidFormatDetails;
use App\Repositories\ScheduleBidFormatDetailsRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class ScheduleBidFormatDetailsController
 * @package App\Http\Controllers\API
 */

class ScheduleBidFormatDetailsAPIController extends AppBaseController
{
    /** @var  ScheduleBidFormatDetailsRepository */
    private $scheduleBidFormatDetailsRepository;

    public function __construct(ScheduleBidFormatDetailsRepository $scheduleBidFormatDetailsRepo)
    {
        $this->scheduleBidFormatDetailsRepository = $scheduleBidFormatDetailsRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/scheduleBidFormatDetails",
     *      summary="Get a listing of the ScheduleBidFormatDetails.",
     *      tags={"ScheduleBidFormatDetails"},
     *      description="Get all ScheduleBidFormatDetails",
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
     *                  @SWG\Items(ref="#/definitions/ScheduleBidFormatDetails")
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
        $this->scheduleBidFormatDetailsRepository->pushCriteria(new RequestCriteria($request));
        $this->scheduleBidFormatDetailsRepository->pushCriteria(new LimitOffsetCriteria($request));
        $scheduleBidFormatDetails = $this->scheduleBidFormatDetailsRepository->all();

        return $this->sendResponse($scheduleBidFormatDetails->toArray(), trans('custom.schedule_bid_format_details_retrieved_successfully'));
    }

    /**
     * @param CreateScheduleBidFormatDetailsAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/scheduleBidFormatDetails",
     *      summary="Store a newly created ScheduleBidFormatDetails in storage",
     *      tags={"ScheduleBidFormatDetails"},
     *      description="Store ScheduleBidFormatDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ScheduleBidFormatDetails that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ScheduleBidFormatDetails")
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
     *                  ref="#/definitions/ScheduleBidFormatDetails"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateScheduleBidFormatDetailsAPIRequest $request)
    {
        $input = $request->all();

        $scheduleBidFormatDetails = $this->scheduleBidFormatDetailsRepository->create($input);

        return $this->sendResponse($scheduleBidFormatDetails->toArray(), trans('custom.schedule_bid_format_details_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/scheduleBidFormatDetails/{id}",
     *      summary="Display the specified ScheduleBidFormatDetails",
     *      tags={"ScheduleBidFormatDetails"},
     *      description="Get ScheduleBidFormatDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ScheduleBidFormatDetails",
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
     *                  ref="#/definitions/ScheduleBidFormatDetails"
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
        /** @var ScheduleBidFormatDetails $scheduleBidFormatDetails */
        $scheduleBidFormatDetails = $this->scheduleBidFormatDetailsRepository->findWithoutFail($id);

        if (empty($scheduleBidFormatDetails)) {
            return $this->sendError(trans('custom.schedule_bid_format_details_not_found'));
        }

        return $this->sendResponse($scheduleBidFormatDetails->toArray(), trans('custom.schedule_bid_format_details_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateScheduleBidFormatDetailsAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/scheduleBidFormatDetails/{id}",
     *      summary="Update the specified ScheduleBidFormatDetails in storage",
     *      tags={"ScheduleBidFormatDetails"},
     *      description="Update ScheduleBidFormatDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ScheduleBidFormatDetails",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ScheduleBidFormatDetails that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ScheduleBidFormatDetails")
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
     *                  ref="#/definitions/ScheduleBidFormatDetails"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateScheduleBidFormatDetailsAPIRequest $request)
    {
        $input = $request->all();

        /** @var ScheduleBidFormatDetails $scheduleBidFormatDetails */
        $scheduleBidFormatDetails = $this->scheduleBidFormatDetailsRepository->findWithoutFail($id);

        if (empty($scheduleBidFormatDetails)) {
            return $this->sendError(trans('custom.schedule_bid_format_details_not_found'));
        }

        $scheduleBidFormatDetails = $this->scheduleBidFormatDetailsRepository->update($input, $id);

        return $this->sendResponse($scheduleBidFormatDetails->toArray(), trans('custom.schedulebidformatdetails_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/scheduleBidFormatDetails/{id}",
     *      summary="Remove the specified ScheduleBidFormatDetails from storage",
     *      tags={"ScheduleBidFormatDetails"},
     *      description="Delete ScheduleBidFormatDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ScheduleBidFormatDetails",
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
        /** @var ScheduleBidFormatDetails $scheduleBidFormatDetails */
        $scheduleBidFormatDetails = $this->scheduleBidFormatDetailsRepository->findWithoutFail($id);

        if (empty($scheduleBidFormatDetails)) {
            return $this->sendError(trans('custom.schedule_bid_format_details_not_found'));
        }

        $scheduleBidFormatDetails->delete();

        return $this->sendSuccess('Schedule Bid Format Details deleted successfully');
    }
}
