<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateTenderBidFormatDetailAPIRequest;
use App\Http\Requests\API\UpdateTenderBidFormatDetailAPIRequest;
use App\Models\TenderBidFormatDetail;
use App\Repositories\TenderBidFormatDetailRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class TenderBidFormatDetailController
 * @package App\Http\Controllers\API
 */

class TenderBidFormatDetailAPIController extends AppBaseController
{
    /** @var  TenderBidFormatDetailRepository */
    private $tenderBidFormatDetailRepository;

    public function __construct(TenderBidFormatDetailRepository $tenderBidFormatDetailRepo)
    {
        $this->tenderBidFormatDetailRepository = $tenderBidFormatDetailRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/tenderBidFormatDetails",
     *      summary="Get a listing of the TenderBidFormatDetails.",
     *      tags={"TenderBidFormatDetail"},
     *      description="Get all TenderBidFormatDetails",
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
     *                  @SWG\Items(ref="#/definitions/TenderBidFormatDetail")
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
        $this->tenderBidFormatDetailRepository->pushCriteria(new RequestCriteria($request));
        $this->tenderBidFormatDetailRepository->pushCriteria(new LimitOffsetCriteria($request));
        $tenderBidFormatDetails = $this->tenderBidFormatDetailRepository->all();

        return $this->sendResponse($tenderBidFormatDetails->toArray(), trans('custom.tender_bid_format_details_retrieved_successfully'));
    }

    /**
     * @param CreateTenderBidFormatDetailAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/tenderBidFormatDetails",
     *      summary="Store a newly created TenderBidFormatDetail in storage",
     *      tags={"TenderBidFormatDetail"},
     *      description="Store TenderBidFormatDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="TenderBidFormatDetail that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/TenderBidFormatDetail")
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
     *                  ref="#/definitions/TenderBidFormatDetail"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateTenderBidFormatDetailAPIRequest $request)
    {
        $input = $request->all();

        $tenderBidFormatDetail = $this->tenderBidFormatDetailRepository->create($input);

        return $this->sendResponse($tenderBidFormatDetail->toArray(), trans('custom.tender_bid_format_detail_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/tenderBidFormatDetails/{id}",
     *      summary="Display the specified TenderBidFormatDetail",
     *      tags={"TenderBidFormatDetail"},
     *      description="Get TenderBidFormatDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of TenderBidFormatDetail",
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
     *                  ref="#/definitions/TenderBidFormatDetail"
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
        /** @var TenderBidFormatDetail $tenderBidFormatDetail */
        $tenderBidFormatDetail = $this->tenderBidFormatDetailRepository->findWithoutFail($id);

        if (empty($tenderBidFormatDetail)) {
            return $this->sendError(trans('custom.tender_bid_format_detail_not_found'));
        }

        return $this->sendResponse($tenderBidFormatDetail->toArray(), trans('custom.tender_bid_format_detail_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateTenderBidFormatDetailAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/tenderBidFormatDetails/{id}",
     *      summary="Update the specified TenderBidFormatDetail in storage",
     *      tags={"TenderBidFormatDetail"},
     *      description="Update TenderBidFormatDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of TenderBidFormatDetail",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="TenderBidFormatDetail that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/TenderBidFormatDetail")
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
     *                  ref="#/definitions/TenderBidFormatDetail"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateTenderBidFormatDetailAPIRequest $request)
    {
        $input = $request->all();

        /** @var TenderBidFormatDetail $tenderBidFormatDetail */
        $tenderBidFormatDetail = $this->tenderBidFormatDetailRepository->findWithoutFail($id);

        if (empty($tenderBidFormatDetail)) {
            return $this->sendError(trans('custom.tender_bid_format_detail_not_found'));
        }

        $tenderBidFormatDetail = $this->tenderBidFormatDetailRepository->update($input, $id);

        return $this->sendResponse($tenderBidFormatDetail->toArray(), trans('custom.tenderbidformatdetail_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/tenderBidFormatDetails/{id}",
     *      summary="Remove the specified TenderBidFormatDetail from storage",
     *      tags={"TenderBidFormatDetail"},
     *      description="Delete TenderBidFormatDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of TenderBidFormatDetail",
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
        /** @var TenderBidFormatDetail $tenderBidFormatDetail */
        $tenderBidFormatDetail = $this->tenderBidFormatDetailRepository->findWithoutFail($id);

        if (empty($tenderBidFormatDetail)) {
            return $this->sendError(trans('custom.tender_bid_format_detail_not_found'));
        }

        $tenderBidFormatDetail->delete();

        return $this->sendSuccess('Tender Bid Format Detail deleted successfully');
    }
}
