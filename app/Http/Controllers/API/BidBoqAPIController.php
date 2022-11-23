<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateBidBoqAPIRequest;
use App\Http\Requests\API\UpdateBidBoqAPIRequest;
use App\Models\BidBoq;
use App\Repositories\BidBoqRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class BidBoqController
 * @package App\Http\Controllers\API
 */

class BidBoqAPIController extends AppBaseController
{
    /** @var  BidBoqRepository */
    private $bidBoqRepository;

    public function __construct(BidBoqRepository $bidBoqRepo)
    {
        $this->bidBoqRepository = $bidBoqRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/bidBoqs",
     *      summary="Get a listing of the BidBoqs.",
     *      tags={"BidBoq"},
     *      description="Get all BidBoqs",
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
     *                  @SWG\Items(ref="#/definitions/BidBoq")
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
        $this->bidBoqRepository->pushCriteria(new RequestCriteria($request));
        $this->bidBoqRepository->pushCriteria(new LimitOffsetCriteria($request));
        $bidBoqs = $this->bidBoqRepository->all();

        return $this->sendResponse($bidBoqs->toArray(), 'Bid Boqs retrieved successfully');
    }

    /**
     * @param CreateBidBoqAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/bidBoqs",
     *      summary="Store a newly created BidBoq in storage",
     *      tags={"BidBoq"},
     *      description="Store BidBoq",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="BidBoq that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/BidBoq")
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
     *                  ref="#/definitions/BidBoq"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateBidBoqAPIRequest $request)
    {
        $input = $request->all();

        $bidBoq = $this->bidBoqRepository->create($input);

        return $this->sendResponse($bidBoq->toArray(), 'Bid Boq saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/bidBoqs/{id}",
     *      summary="Display the specified BidBoq",
     *      tags={"BidBoq"},
     *      description="Get BidBoq",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of BidBoq",
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
     *                  ref="#/definitions/BidBoq"
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
        /** @var BidBoq $bidBoq */
        $bidBoq = $this->bidBoqRepository->findWithoutFail($id);

        if (empty($bidBoq)) {
            return $this->sendError('Bid Boq not found');
        }

        return $this->sendResponse($bidBoq->toArray(), 'Bid Boq retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateBidBoqAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/bidBoqs/{id}",
     *      summary="Update the specified BidBoq in storage",
     *      tags={"BidBoq"},
     *      description="Update BidBoq",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of BidBoq",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="BidBoq that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/BidBoq")
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
     *                  ref="#/definitions/BidBoq"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateBidBoqAPIRequest $request)
    {
        $input = $request->all();

        /** @var BidBoq $bidBoq */
        $bidBoq = $this->bidBoqRepository->findWithoutFail($id);

        if (empty($bidBoq)) {
            return $this->sendError('Bid Boq not found');
        }

        $bidBoq = $this->bidBoqRepository->update($input, $id);

        return $this->sendResponse($bidBoq->toArray(), 'BidBoq updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/bidBoqs/{id}",
     *      summary="Remove the specified BidBoq from storage",
     *      tags={"BidBoq"},
     *      description="Delete BidBoq",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of BidBoq",
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
        /** @var BidBoq $bidBoq */
        $bidBoq = $this->bidBoqRepository->findWithoutFail($id);

        if (empty($bidBoq)) {
            return $this->sendError('Bid Boq not found');
        }

        $bidBoq->delete();

        return $this->sendSuccess('Bid Boq deleted successfully');
    }
}
