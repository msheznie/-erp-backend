<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateScheduleMasterAPIRequest;
use App\Http\Requests\API\UpdateScheduleMasterAPIRequest;
use App\Models\ScheduleMaster;
use App\Repositories\ScheduleMasterRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class ScheduleMasterController
 * @package App\Http\Controllers\API
 */

class ScheduleMasterAPIController extends AppBaseController
{
    /** @var  ScheduleMasterRepository */
    private $scheduleMasterRepository;

    public function __construct(ScheduleMasterRepository $scheduleMasterRepo)
    {
        $this->scheduleMasterRepository = $scheduleMasterRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/scheduleMasters",
     *      summary="Get a listing of the ScheduleMasters.",
     *      tags={"ScheduleMaster"},
     *      description="Get all ScheduleMasters",
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
     *                  @SWG\Items(ref="#/definitions/ScheduleMaster")
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
        $this->scheduleMasterRepository->pushCriteria(new RequestCriteria($request));
        $this->scheduleMasterRepository->pushCriteria(new LimitOffsetCriteria($request));
        $scheduleMasters = $this->scheduleMasterRepository->all();

        return $this->sendResponse($scheduleMasters->toArray(), trans('custom.schedule_masters_retrieved_successfully'));
    }

    /**
     * @param CreateScheduleMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/scheduleMasters",
     *      summary="Store a newly created ScheduleMaster in storage",
     *      tags={"ScheduleMaster"},
     *      description="Store ScheduleMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ScheduleMaster that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ScheduleMaster")
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
     *                  ref="#/definitions/ScheduleMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateScheduleMasterAPIRequest $request)
    {
        $input = $request->all();

        $scheduleMaster = $this->scheduleMasterRepository->create($input);

        return $this->sendResponse($scheduleMaster->toArray(), trans('custom.schedule_master_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/scheduleMasters/{id}",
     *      summary="Display the specified ScheduleMaster",
     *      tags={"ScheduleMaster"},
     *      description="Get ScheduleMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ScheduleMaster",
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
     *                  ref="#/definitions/ScheduleMaster"
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
        /** @var ScheduleMaster $scheduleMaster */
        $scheduleMaster = $this->scheduleMasterRepository->findWithoutFail($id);

        if (empty($scheduleMaster)) {
            return $this->sendError(trans('custom.schedule_master_not_found'));
        }

        return $this->sendResponse($scheduleMaster->toArray(), trans('custom.schedule_master_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateScheduleMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/scheduleMasters/{id}",
     *      summary="Update the specified ScheduleMaster in storage",
     *      tags={"ScheduleMaster"},
     *      description="Update ScheduleMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ScheduleMaster",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ScheduleMaster that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ScheduleMaster")
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
     *                  ref="#/definitions/ScheduleMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateScheduleMasterAPIRequest $request)
    {
        $input = $request->all();

        /** @var ScheduleMaster $scheduleMaster */
        $scheduleMaster = $this->scheduleMasterRepository->findWithoutFail($id);

        if (empty($scheduleMaster)) {
            return $this->sendError(trans('custom.schedule_master_not_found'));
        }

        $scheduleMaster = $this->scheduleMasterRepository->update($input, $id);

        return $this->sendResponse($scheduleMaster->toArray(), trans('custom.schedulemaster_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/scheduleMasters/{id}",
     *      summary="Remove the specified ScheduleMaster from storage",
     *      tags={"ScheduleMaster"},
     *      description="Delete ScheduleMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ScheduleMaster",
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
        /** @var ScheduleMaster $scheduleMaster */
        $scheduleMaster = $this->scheduleMasterRepository->findWithoutFail($id);

        if (empty($scheduleMaster)) {
            return $this->sendError(trans('custom.schedule_master_not_found'));
        }

        $scheduleMaster->delete();

        return $this->sendResponse($id, trans('custom.schedule_master_deleted_successfully'));
    }
}
