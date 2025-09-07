<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateBidSubmissionDetailAPIRequest;
use App\Http\Requests\API\UpdateBidSubmissionDetailAPIRequest;
use App\Models\BidSubmissionDetail;
use App\Repositories\BidSubmissionDetailRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class BidSubmissionDetailController
 * @package App\Http\Controllers\API
 */

class BidSubmissionDetailAPIController extends AppBaseController
{
    /** @var  BidSubmissionDetailRepository */
    private $bidSubmissionDetailRepository;

    public function __construct(BidSubmissionDetailRepository $bidSubmissionDetailRepo)
    {
        $this->bidSubmissionDetailRepository = $bidSubmissionDetailRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/bidSubmissionDetails",
     *      summary="Get a listing of the BidSubmissionDetails.",
     *      tags={"BidSubmissionDetail"},
     *      description="Get all BidSubmissionDetails",
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
     *                  @SWG\Items(ref="#/definitions/BidSubmissionDetail")
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
        $this->bidSubmissionDetailRepository->pushCriteria(new RequestCriteria($request));
        $this->bidSubmissionDetailRepository->pushCriteria(new LimitOffsetCriteria($request));
        $bidSubmissionDetails = $this->bidSubmissionDetailRepository->all();

        return $this->sendResponse($bidSubmissionDetails->toArray(), trans('custom.bid_submission_details_retrieved_successfully'));
    }

    /**
     * @param CreateBidSubmissionDetailAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/bidSubmissionDetails",
     *      summary="Store a newly created BidSubmissionDetail in storage",
     *      tags={"BidSubmissionDetail"},
     *      description="Store BidSubmissionDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="BidSubmissionDetail that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/BidSubmissionDetail")
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
     *                  ref="#/definitions/BidSubmissionDetail"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateBidSubmissionDetailAPIRequest $request)
    {
        $input = $request->all();

        $bidSubmissionDetail = $this->bidSubmissionDetailRepository->create($input);

        return $this->sendResponse($bidSubmissionDetail->toArray(), trans('custom.bid_submission_detail_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/bidSubmissionDetails/{id}",
     *      summary="Display the specified BidSubmissionDetail",
     *      tags={"BidSubmissionDetail"},
     *      description="Get BidSubmissionDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of BidSubmissionDetail",
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
     *                  ref="#/definitions/BidSubmissionDetail"
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
        /** @var BidSubmissionDetail $bidSubmissionDetail */
        $bidSubmissionDetail = $this->bidSubmissionDetailRepository->findWithoutFail($id);

        if (empty($bidSubmissionDetail)) {
            return $this->sendError(trans('custom.bid_submission_detail_not_found'));
        }

        return $this->sendResponse($bidSubmissionDetail->toArray(), trans('custom.bid_submission_detail_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateBidSubmissionDetailAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/bidSubmissionDetails/{id}",
     *      summary="Update the specified BidSubmissionDetail in storage",
     *      tags={"BidSubmissionDetail"},
     *      description="Update BidSubmissionDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of BidSubmissionDetail",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="BidSubmissionDetail that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/BidSubmissionDetail")
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
     *                  ref="#/definitions/BidSubmissionDetail"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateBidSubmissionDetailAPIRequest $request)
    {
        $input = $request->all();

        /** @var BidSubmissionDetail $bidSubmissionDetail */
        $bidSubmissionDetail = $this->bidSubmissionDetailRepository->findWithoutFail($id);

        if (empty($bidSubmissionDetail)) {
            return $this->sendError(trans('custom.bid_submission_detail_not_found'));
        }

        $bidSubmissionDetail = $this->bidSubmissionDetailRepository->update($input, $id);

        return $this->sendResponse($bidSubmissionDetail->toArray(), trans('custom.bidsubmissiondetail_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/bidSubmissionDetails/{id}",
     *      summary="Remove the specified BidSubmissionDetail from storage",
     *      tags={"BidSubmissionDetail"},
     *      description="Delete BidSubmissionDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of BidSubmissionDetail",
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
        /** @var BidSubmissionDetail $bidSubmissionDetail */
        $bidSubmissionDetail = $this->bidSubmissionDetailRepository->findWithoutFail($id);

        if (empty($bidSubmissionDetail)) {
            return $this->sendError(trans('custom.bid_submission_detail_not_found'));
        }

        $bidSubmissionDetail->delete();

        return $this->sendSuccess('Bid Submission Detail deleted successfully');
    }
}
