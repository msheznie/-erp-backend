<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateReasonCodeMasterAPIRequest;
use App\Http\Requests\API\UpdateReasonCodeMasterAPIRequest;
use App\Models\ReasonCodeMaster;
use App\Repositories\ReasonCodeMasterRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class ReasonCodeMasterController
 * @package App\Http\Controllers\API
 */

class ReasonCodeMasterAPIController extends AppBaseController
{
    /** @var  ReasonCodeMasterRepository */
    private $reasonCodeMasterRepository;

    public function __construct(ReasonCodeMasterRepository $reasonCodeMasterRepo)
    {
        $this->reasonCodeMasterRepository = $reasonCodeMasterRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/reasonCodeMasters",
     *      summary="Get a listing of the ReasonCodeMasters.",
     *      tags={"ReasonCodeMaster"},
     *      description="Get all ReasonCodeMasters",
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
     *                  @SWG\Items(ref="#/definitions/ReasonCodeMaster")
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
        $this->reasonCodeMasterRepository->pushCriteria(new RequestCriteria($request));
        $this->reasonCodeMasterRepository->pushCriteria(new LimitOffsetCriteria($request));
        $reasonCodeMasters = $this->reasonCodeMasterRepository->all();

        return $this->sendResponse($reasonCodeMasters->toArray(), 'Reason Code Masters retrieved successfully');
    }

    /**
     * @param CreateReasonCodeMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/reasonCodeMasters",
     *      summary="Store a newly created ReasonCodeMaster in storage",
     *      tags={"ReasonCodeMaster"},
     *      description="Store ReasonCodeMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ReasonCodeMaster that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ReasonCodeMaster")
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
     *                  ref="#/definitions/ReasonCodeMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateReasonCodeMasterAPIRequest $request)
    {
        $input = $request->all();

        $reasonCodeMaster = $this->reasonCodeMasterRepository->create($input);

        return $this->sendResponse($reasonCodeMaster->toArray(), 'Reason Code Master saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/reasonCodeMasters/{id}",
     *      summary="Display the specified ReasonCodeMaster",
     *      tags={"ReasonCodeMaster"},
     *      description="Get ReasonCodeMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ReasonCodeMaster",
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
     *                  ref="#/definitions/ReasonCodeMaster"
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
        /** @var ReasonCodeMaster $reasonCodeMaster */
        $reasonCodeMaster = $this->reasonCodeMasterRepository->findWithoutFail($id);

        if (empty($reasonCodeMaster)) {
            return $this->sendError('Reason Code Master not found');
        }

        return $this->sendResponse($reasonCodeMaster->toArray(), 'Reason Code Master retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateReasonCodeMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/reasonCodeMasters/{id}",
     *      summary="Update the specified ReasonCodeMaster in storage",
     *      tags={"ReasonCodeMaster"},
     *      description="Update ReasonCodeMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ReasonCodeMaster",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ReasonCodeMaster that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ReasonCodeMaster")
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
     *                  ref="#/definitions/ReasonCodeMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateReasonCodeMasterAPIRequest $request)
    {
        $input = $request->all();

        /** @var ReasonCodeMaster $reasonCodeMaster */
        $reasonCodeMaster = $this->reasonCodeMasterRepository->findWithoutFail($id);

        if (empty($reasonCodeMaster)) {
            return $this->sendError('Reason Code Master not found');
        }

        $reasonCodeMaster = $this->reasonCodeMasterRepository->update($input, $id);

        return $this->sendResponse($reasonCodeMaster->toArray(), 'ReasonCodeMaster updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/reasonCodeMasters/{id}",
     *      summary="Remove the specified ReasonCodeMaster from storage",
     *      tags={"ReasonCodeMaster"},
     *      description="Delete ReasonCodeMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ReasonCodeMaster",
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
        /** @var ReasonCodeMaster $reasonCodeMaster */
        $reasonCodeMaster = $this->reasonCodeMasterRepository->findWithoutFail($id);

        if (empty($reasonCodeMaster)) {
            return $this->sendError('Reason Code Master not found');
        }

        $reasonCodeMaster->delete();

        return $this->sendSuccess('Reason Code Master deleted successfully');
    }
}
