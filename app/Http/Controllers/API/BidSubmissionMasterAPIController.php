<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateBidSubmissionMasterAPIRequest;
use App\Http\Requests\API\UpdateBidSubmissionMasterAPIRequest;
use App\Models\BidSubmissionMaster;
use App\Repositories\BidSubmissionMasterRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class BidSubmissionMasterController
 * @package App\Http\Controllers\API
 */

class BidSubmissionMasterAPIController extends AppBaseController
{
    /** @var  BidSubmissionMasterRepository */
    private $bidSubmissionMasterRepository;

    public function __construct(BidSubmissionMasterRepository $bidSubmissionMasterRepo)
    {
        $this->bidSubmissionMasterRepository = $bidSubmissionMasterRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/bidSubmissionMasters",
     *      summary="Get a listing of the BidSubmissionMasters.",
     *      tags={"BidSubmissionMaster"},
     *      description="Get all BidSubmissionMasters",
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
     *                  @SWG\Items(ref="#/definitions/BidSubmissionMaster")
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
        $this->bidSubmissionMasterRepository->pushCriteria(new RequestCriteria($request));
        $this->bidSubmissionMasterRepository->pushCriteria(new LimitOffsetCriteria($request));
        $bidSubmissionMasters = $this->bidSubmissionMasterRepository->all();

        return $this->sendResponse($bidSubmissionMasters->toArray(), 'Bid Submission Masters retrieved successfully');
    }

    /**
     * @param CreateBidSubmissionMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/bidSubmissionMasters",
     *      summary="Store a newly created BidSubmissionMaster in storage",
     *      tags={"BidSubmissionMaster"},
     *      description="Store BidSubmissionMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="BidSubmissionMaster that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/BidSubmissionMaster")
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
     *                  ref="#/definitions/BidSubmissionMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateBidSubmissionMasterAPIRequest $request)
    {
        $input = $request->all();

        $bidSubmissionMaster = $this->bidSubmissionMasterRepository->create($input);

        return $this->sendResponse($bidSubmissionMaster->toArray(), 'Bid Submission Master saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/bidSubmissionMasters/{id}",
     *      summary="Display the specified BidSubmissionMaster",
     *      tags={"BidSubmissionMaster"},
     *      description="Get BidSubmissionMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of BidSubmissionMaster",
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
     *                  ref="#/definitions/BidSubmissionMaster"
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
        /** @var BidSubmissionMaster $bidSubmissionMaster */
        $bidSubmissionMaster = $this->bidSubmissionMasterRepository->findWithoutFail($id);

        if (empty($bidSubmissionMaster)) {
            return $this->sendError('Bid Submission Master not found');
        }

        return $this->sendResponse($bidSubmissionMaster->toArray(), 'Bid Submission Master retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateBidSubmissionMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/bidSubmissionMasters/{id}",
     *      summary="Update the specified BidSubmissionMaster in storage",
     *      tags={"BidSubmissionMaster"},
     *      description="Update BidSubmissionMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of BidSubmissionMaster",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="BidSubmissionMaster that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/BidSubmissionMaster")
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
     *                  ref="#/definitions/BidSubmissionMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateBidSubmissionMasterAPIRequest $request)
    {
        $input = $request->all();

        /** @var BidSubmissionMaster $bidSubmissionMaster */
        $bidSubmissionMaster = $this->bidSubmissionMasterRepository->findWithoutFail($id);

        if (empty($bidSubmissionMaster)) {
            return $this->sendError('Bid Submission Master not found');
        }

        $bidSubmissionMaster = $this->bidSubmissionMasterRepository->update($input, $id);

        return $this->sendResponse($bidSubmissionMaster->toArray(), 'BidSubmissionMaster updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/bidSubmissionMasters/{id}",
     *      summary="Remove the specified BidSubmissionMaster from storage",
     *      tags={"BidSubmissionMaster"},
     *      description="Delete BidSubmissionMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of BidSubmissionMaster",
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
        /** @var BidSubmissionMaster $bidSubmissionMaster */
        $bidSubmissionMaster = $this->bidSubmissionMasterRepository->findWithoutFail($id);

        if (empty($bidSubmissionMaster)) {
            return $this->sendError('Bid Submission Master not found');
        }

        $bidSubmissionMaster->delete();

        return $this->sendSuccess('Bid Submission Master deleted successfully');
    }
}
