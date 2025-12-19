<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateBidMainWorkAPIRequest;
use App\Http\Requests\API\UpdateBidMainWorkAPIRequest;
use App\Models\BidMainWork;
use App\Repositories\BidMainWorkRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class BidMainWorkController
 * @package App\Http\Controllers\API
 */

class BidMainWorkAPIController extends AppBaseController
{
    /** @var  BidMainWorkRepository */
    private $bidMainWorkRepository;

    public function __construct(BidMainWorkRepository $bidMainWorkRepo)
    {
        $this->bidMainWorkRepository = $bidMainWorkRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/bidMainWorks",
     *      summary="Get a listing of the BidMainWorks.",
     *      tags={"BidMainWork"},
     *      description="Get all BidMainWorks",
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
     *                  @SWG\Items(ref="#/definitions/BidMainWork")
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
        $this->bidMainWorkRepository->pushCriteria(new RequestCriteria($request));
        $this->bidMainWorkRepository->pushCriteria(new LimitOffsetCriteria($request));
        $bidMainWorks = $this->bidMainWorkRepository->all();

        return $this->sendResponse($bidMainWorks->toArray(), trans('custom.bid_main_works_retrieved_successfully'));
    }

    /**
     * @param CreateBidMainWorkAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/bidMainWorks",
     *      summary="Store a newly created BidMainWork in storage",
     *      tags={"BidMainWork"},
     *      description="Store BidMainWork",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="BidMainWork that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/BidMainWork")
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
     *                  ref="#/definitions/BidMainWork"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateBidMainWorkAPIRequest $request)
    {
        $input = $request->all();

        $bidMainWork = $this->bidMainWorkRepository->create($input);

        return $this->sendResponse($bidMainWork->toArray(), trans('custom.bid_main_work_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/bidMainWorks/{id}",
     *      summary="Display the specified BidMainWork",
     *      tags={"BidMainWork"},
     *      description="Get BidMainWork",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of BidMainWork",
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
     *                  ref="#/definitions/BidMainWork"
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
        /** @var BidMainWork $bidMainWork */
        $bidMainWork = $this->bidMainWorkRepository->findWithoutFail($id);

        if (empty($bidMainWork)) {
            return $this->sendError(trans('custom.bid_main_work_not_found'));
        }

        return $this->sendResponse($bidMainWork->toArray(), trans('custom.bid_main_work_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateBidMainWorkAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/bidMainWorks/{id}",
     *      summary="Update the specified BidMainWork in storage",
     *      tags={"BidMainWork"},
     *      description="Update BidMainWork",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of BidMainWork",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="BidMainWork that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/BidMainWork")
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
     *                  ref="#/definitions/BidMainWork"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateBidMainWorkAPIRequest $request)
    {
        $input = $request->all();

        /** @var BidMainWork $bidMainWork */
        $bidMainWork = $this->bidMainWorkRepository->findWithoutFail($id);

        if (empty($bidMainWork)) {
            return $this->sendError(trans('custom.bid_main_work_not_found'));
        }

        $bidMainWork = $this->bidMainWorkRepository->update($input, $id);

        return $this->sendResponse($bidMainWork->toArray(), trans('custom.bidmainwork_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/bidMainWorks/{id}",
     *      summary="Remove the specified BidMainWork from storage",
     *      tags={"BidMainWork"},
     *      description="Delete BidMainWork",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of BidMainWork",
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
        /** @var BidMainWork $bidMainWork */
        $bidMainWork = $this->bidMainWorkRepository->findWithoutFail($id);

        if (empty($bidMainWork)) {
            return $this->sendError(trans('custom.bid_main_work_not_found'));
        }

        $bidMainWork->delete();

        return $this->sendSuccess('Bid Main Work deleted successfully');
    }
}
