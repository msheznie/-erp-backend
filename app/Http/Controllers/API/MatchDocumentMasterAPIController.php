<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateMatchDocumentMasterAPIRequest;
use App\Http\Requests\API\UpdateMatchDocumentMasterAPIRequest;
use App\Models\MatchDocumentMaster;
use App\Repositories\MatchDocumentMasterRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class MatchDocumentMasterController
 * @package App\Http\Controllers\API
 */

class MatchDocumentMasterAPIController extends AppBaseController
{
    /** @var  MatchDocumentMasterRepository */
    private $matchDocumentMasterRepository;

    public function __construct(MatchDocumentMasterRepository $matchDocumentMasterRepo)
    {
        $this->matchDocumentMasterRepository = $matchDocumentMasterRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/matchDocumentMasters",
     *      summary="Get a listing of the MatchDocumentMasters.",
     *      tags={"MatchDocumentMaster"},
     *      description="Get all MatchDocumentMasters",
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
     *                  @SWG\Items(ref="#/definitions/MatchDocumentMaster")
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
        $this->matchDocumentMasterRepository->pushCriteria(new RequestCriteria($request));
        $this->matchDocumentMasterRepository->pushCriteria(new LimitOffsetCriteria($request));
        $matchDocumentMasters = $this->matchDocumentMasterRepository->all();

        return $this->sendResponse($matchDocumentMasters->toArray(), 'Match Document Masters retrieved successfully');
    }

    /**
     * @param CreateMatchDocumentMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/matchDocumentMasters",
     *      summary="Store a newly created MatchDocumentMaster in storage",
     *      tags={"MatchDocumentMaster"},
     *      description="Store MatchDocumentMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="MatchDocumentMaster that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/MatchDocumentMaster")
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
     *                  ref="#/definitions/MatchDocumentMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateMatchDocumentMasterAPIRequest $request)
    {
        $input = $request->all();

        $matchDocumentMasters = $this->matchDocumentMasterRepository->create($input);

        return $this->sendResponse($matchDocumentMasters->toArray(), 'Match Document Master saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/matchDocumentMasters/{id}",
     *      summary="Display the specified MatchDocumentMaster",
     *      tags={"MatchDocumentMaster"},
     *      description="Get MatchDocumentMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of MatchDocumentMaster",
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
     *                  ref="#/definitions/MatchDocumentMaster"
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
        /** @var MatchDocumentMaster $matchDocumentMaster */
        $matchDocumentMaster = $this->matchDocumentMasterRepository->findWithoutFail($id);

        if (empty($matchDocumentMaster)) {
            return $this->sendError('Match Document Master not found');
        }

        return $this->sendResponse($matchDocumentMaster->toArray(), 'Match Document Master retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateMatchDocumentMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/matchDocumentMasters/{id}",
     *      summary="Update the specified MatchDocumentMaster in storage",
     *      tags={"MatchDocumentMaster"},
     *      description="Update MatchDocumentMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of MatchDocumentMaster",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="MatchDocumentMaster that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/MatchDocumentMaster")
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
     *                  ref="#/definitions/MatchDocumentMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateMatchDocumentMasterAPIRequest $request)
    {
        $input = $request->all();

        /** @var MatchDocumentMaster $matchDocumentMaster */
        $matchDocumentMaster = $this->matchDocumentMasterRepository->findWithoutFail($id);

        if (empty($matchDocumentMaster)) {
            return $this->sendError('Match Document Master not found');
        }

        $matchDocumentMaster = $this->matchDocumentMasterRepository->update($input, $id);

        return $this->sendResponse($matchDocumentMaster->toArray(), 'MatchDocumentMaster updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/matchDocumentMasters/{id}",
     *      summary="Remove the specified MatchDocumentMaster from storage",
     *      tags={"MatchDocumentMaster"},
     *      description="Delete MatchDocumentMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of MatchDocumentMaster",
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
        /** @var MatchDocumentMaster $matchDocumentMaster */
        $matchDocumentMaster = $this->matchDocumentMasterRepository->findWithoutFail($id);

        if (empty($matchDocumentMaster)) {
            return $this->sendError('Match Document Master not found');
        }

        $matchDocumentMaster->delete();

        return $this->sendResponse($id, 'Match Document Master deleted successfully');
    }
}
