<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateRigMasterAPIRequest;
use App\Http\Requests\API\UpdateRigMasterAPIRequest;
use App\Models\RigMaster;
use App\Repositories\RigMasterRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class RigMasterController
 * @package App\Http\Controllers\API
 */

class RigMasterAPIController extends AppBaseController
{
    /** @var  RigMasterRepository */
    private $rigMasterRepository;

    public function __construct(RigMasterRepository $rigMasterRepo)
    {
        $this->rigMasterRepository = $rigMasterRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/rigMasters",
     *      summary="Get a listing of the RigMasters.",
     *      tags={"RigMaster"},
     *      description="Get all RigMasters",
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
     *                  @SWG\Items(ref="#/definitions/RigMaster")
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
        $this->rigMasterRepository->pushCriteria(new RequestCriteria($request));
        $this->rigMasterRepository->pushCriteria(new LimitOffsetCriteria($request));
        $rigMasters = $this->rigMasterRepository->all();

        return $this->sendResponse($rigMasters->toArray(), trans('custom.rig_masters_retrieved_successfully'));
    }

    /**
     * @param CreateRigMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/rigMasters",
     *      summary="Store a newly created RigMaster in storage",
     *      tags={"RigMaster"},
     *      description="Store RigMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="RigMaster that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/RigMaster")
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
     *                  ref="#/definitions/RigMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateRigMasterAPIRequest $request)
    {
        $input = $request->all();

        $rigMasters = $this->rigMasterRepository->create($input);

        return $this->sendResponse($rigMasters->toArray(), trans('custom.rig_master_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/rigMasters/{id}",
     *      summary="Display the specified RigMaster",
     *      tags={"RigMaster"},
     *      description="Get RigMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of RigMaster",
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
     *                  ref="#/definitions/RigMaster"
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
        /** @var RigMaster $rigMaster */
        $rigMaster = $this->rigMasterRepository->findWithoutFail($id);

        if (empty($rigMaster)) {
            return $this->sendError(trans('custom.rig_master_not_found'));
        }

        return $this->sendResponse($rigMaster->toArray(), trans('custom.rig_master_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateRigMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/rigMasters/{id}",
     *      summary="Update the specified RigMaster in storage",
     *      tags={"RigMaster"},
     *      description="Update RigMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of RigMaster",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="RigMaster that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/RigMaster")
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
     *                  ref="#/definitions/RigMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateRigMasterAPIRequest $request)
    {
        $input = $request->all();

        /** @var RigMaster $rigMaster */
        $rigMaster = $this->rigMasterRepository->findWithoutFail($id);

        if (empty($rigMaster)) {
            return $this->sendError(trans('custom.rig_master_not_found'));
        }

        $rigMaster = $this->rigMasterRepository->update($input, $id);

        return $this->sendResponse($rigMaster->toArray(), trans('custom.rigmaster_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/rigMasters/{id}",
     *      summary="Remove the specified RigMaster from storage",
     *      tags={"RigMaster"},
     *      description="Delete RigMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of RigMaster",
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
        /** @var RigMaster $rigMaster */
        $rigMaster = $this->rigMasterRepository->findWithoutFail($id);

        if (empty($rigMaster)) {
            return $this->sendError(trans('custom.rig_master_not_found'));
        }

        $rigMaster->delete();

        return $this->sendResponse($id, trans('custom.rig_master_deleted_successfully'));
    }
}
