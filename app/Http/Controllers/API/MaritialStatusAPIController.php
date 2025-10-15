<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateMaritialStatusAPIRequest;
use App\Http\Requests\API\UpdateMaritialStatusAPIRequest;
use App\Models\MaritialStatus;
use App\Repositories\MaritialStatusRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class MaritialStatusController
 * @package App\Http\Controllers\API
 */

class MaritialStatusAPIController extends AppBaseController
{
    /** @var  MaritialStatusRepository */
    private $maritialStatusRepository;

    public function __construct(MaritialStatusRepository $maritialStatusRepo)
    {
        $this->maritialStatusRepository = $maritialStatusRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/maritialStatuses",
     *      summary="Get a listing of the MaritialStatuses.",
     *      tags={"MaritialStatus"},
     *      description="Get all MaritialStatuses",
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
     *                  @SWG\Items(ref="#/definitions/MaritialStatus")
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
        $this->maritialStatusRepository->pushCriteria(new RequestCriteria($request));
        $this->maritialStatusRepository->pushCriteria(new LimitOffsetCriteria($request));
        $maritialStatuses = $this->maritialStatusRepository->all();

        return $this->sendResponse($maritialStatuses->toArray(), trans('custom.maritial_statuses_retrieved_successfully'));
    }

    /**
     * @param CreateMaritialStatusAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/maritialStatuses",
     *      summary="Store a newly created MaritialStatus in storage",
     *      tags={"MaritialStatus"},
     *      description="Store MaritialStatus",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="MaritialStatus that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/MaritialStatus")
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
     *                  ref="#/definitions/MaritialStatus"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateMaritialStatusAPIRequest $request)
    {
        $input = $request->all();

        $maritialStatus = $this->maritialStatusRepository->create($input);

        return $this->sendResponse($maritialStatus->toArray(), trans('custom.maritial_status_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/maritialStatuses/{id}",
     *      summary="Display the specified MaritialStatus",
     *      tags={"MaritialStatus"},
     *      description="Get MaritialStatus",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of MaritialStatus",
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
     *                  ref="#/definitions/MaritialStatus"
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
        /** @var MaritialStatus $maritialStatus */
        $maritialStatus = $this->maritialStatusRepository->findWithoutFail($id);

        if (empty($maritialStatus)) {
            return $this->sendError(trans('custom.maritial_status_not_found'));
        }

        return $this->sendResponse($maritialStatus->toArray(), trans('custom.maritial_status_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateMaritialStatusAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/maritialStatuses/{id}",
     *      summary="Update the specified MaritialStatus in storage",
     *      tags={"MaritialStatus"},
     *      description="Update MaritialStatus",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of MaritialStatus",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="MaritialStatus that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/MaritialStatus")
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
     *                  ref="#/definitions/MaritialStatus"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateMaritialStatusAPIRequest $request)
    {
        $input = $request->all();

        /** @var MaritialStatus $maritialStatus */
        $maritialStatus = $this->maritialStatusRepository->findWithoutFail($id);

        if (empty($maritialStatus)) {
            return $this->sendError(trans('custom.maritial_status_not_found'));
        }

        $maritialStatus = $this->maritialStatusRepository->update($input, $id);

        return $this->sendResponse($maritialStatus->toArray(), trans('custom.maritialstatus_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/maritialStatuses/{id}",
     *      summary="Remove the specified MaritialStatus from storage",
     *      tags={"MaritialStatus"},
     *      description="Delete MaritialStatus",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of MaritialStatus",
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
        /** @var MaritialStatus $maritialStatus */
        $maritialStatus = $this->maritialStatusRepository->findWithoutFail($id);

        if (empty($maritialStatus)) {
            return $this->sendError(trans('custom.maritial_status_not_found'));
        }

        $maritialStatus->delete();

        return $this->sendResponse($id, trans('custom.maritial_status_deleted_successfully'));
    }
}
