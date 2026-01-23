<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateAllocationMasterAPIRequest;
use App\Http\Requests\API\UpdateAllocationMasterAPIRequest;
use App\Models\AllocationMaster;
use App\Repositories\AllocationMasterRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class AllocationMasterController
 * @package App\Http\Controllers\API
 */

class AllocationMasterAPIController extends AppBaseController
{
    /** @var  AllocationMasterRepository */
    private $allocationMasterRepository;

    public function __construct(AllocationMasterRepository $allocationMasterRepo)
    {
        $this->allocationMasterRepository = $allocationMasterRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/allocationMasters",
     *      summary="Get a listing of the AllocationMasters.",
     *      tags={"AllocationMaster"},
     *      description="Get all AllocationMasters",
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
     *                  @SWG\Items(ref="#/definitions/AllocationMaster")
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
        $this->allocationMasterRepository->pushCriteria(new RequestCriteria($request));
        $this->allocationMasterRepository->pushCriteria(new LimitOffsetCriteria($request));
        $allocationMasters = $this->allocationMasterRepository->all();

        return $this->sendResponse($allocationMasters->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.allocation_masters')]));
    }

    /**
     * @param CreateAllocationMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/allocationMasters",
     *      summary="Store a newly created AllocationMaster in storage",
     *      tags={"AllocationMaster"},
     *      description="Store AllocationMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="AllocationMaster that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/AllocationMaster")
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
     *                  ref="#/definitions/AllocationMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateAllocationMasterAPIRequest $request)
    {
        $input = $request->all();

        $allocationMaster = $this->allocationMasterRepository->create($input);

        return $this->sendResponse($allocationMaster->toArray(), trans('custom.save', ['attribute' => trans('custom.allocation_masters')]));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/allocationMasters/{id}",
     *      summary="Display the specified AllocationMaster",
     *      tags={"AllocationMaster"},
     *      description="Get AllocationMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of AllocationMaster",
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
     *                  ref="#/definitions/AllocationMaster"
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
        /** @var AllocationMaster $allocationMaster */
        $allocationMaster = $this->allocationMasterRepository->findWithoutFail($id);

        if (empty($allocationMaster)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.allocation_masters')]));
        }

        return $this->sendResponse($allocationMaster->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.allocation_masters')]));
    }

    /**
     * @param int $id
     * @param UpdateAllocationMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/allocationMasters/{id}",
     *      summary="Update the specified AllocationMaster in storage",
     *      tags={"AllocationMaster"},
     *      description="Update AllocationMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of AllocationMaster",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="AllocationMaster that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/AllocationMaster")
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
     *                  ref="#/definitions/AllocationMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateAllocationMasterAPIRequest $request)
    {
        $input = $request->all();

        /** @var AllocationMaster $allocationMaster */
        $allocationMaster = $this->allocationMasterRepository->findWithoutFail($id);

        if (empty($allocationMaster)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.allocation_masters')]));
        }

        $allocationMaster = $this->allocationMasterRepository->update($input, $id);

        return $this->sendResponse($allocationMaster->toArray(), trans('custom.update', ['attribute' => trans('custom.allocation_masters')]));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/allocationMasters/{id}",
     *      summary="Remove the specified AllocationMaster from storage",
     *      tags={"AllocationMaster"},
     *      description="Delete AllocationMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of AllocationMaster",
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
        /** @var AllocationMaster $allocationMaster */
        $allocationMaster = $this->allocationMasterRepository->findWithoutFail($id);

        if (empty($allocationMaster)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.allocation_masters')]));
        }

        $allocationMaster->delete();

        return $this->sendResponse($id, trans('custom.delete', ['attribute' => trans('custom.allocation_masters')]));
    }
}
