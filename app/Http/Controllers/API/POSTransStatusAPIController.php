<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreatePOSTransStatusAPIRequest;
use App\Http\Requests\API\UpdatePOSTransStatusAPIRequest;
use App\Models\POSTransStatus;
use App\Repositories\POSTransStatusRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class POSTransStatusController
 * @package App\Http\Controllers\API
 */

class POSTransStatusAPIController extends AppBaseController
{
    /** @var  POSTransStatusRepository */
    private $pOSTransStatusRepository;

    public function __construct(POSTransStatusRepository $pOSTransStatusRepo)
    {
        $this->pOSTransStatusRepository = $pOSTransStatusRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/pOSTransStatuses",
     *      summary="Get a listing of the POSTransStatuses.",
     *      tags={"POSTransStatus"},
     *      description="Get all POSTransStatuses",
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
     *                  @SWG\Items(ref="#/definitions/POSTransStatus")
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
        $this->pOSTransStatusRepository->pushCriteria(new RequestCriteria($request));
        $this->pOSTransStatusRepository->pushCriteria(new LimitOffsetCriteria($request));
        $pOSTransStatuses = $this->pOSTransStatusRepository->all();

        return $this->sendResponse($pOSTransStatuses->toArray(), trans('custom.p_o_s_trans_statuses_retrieved_successfully'));
    }

    /**
     * @param CreatePOSTransStatusAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/pOSTransStatuses",
     *      summary="Store a newly created POSTransStatus in storage",
     *      tags={"POSTransStatus"},
     *      description="Store POSTransStatus",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="POSTransStatus that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/POSTransStatus")
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
     *                  ref="#/definitions/POSTransStatus"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreatePOSTransStatusAPIRequest $request)
    {
        $input = $request->all();

        $pOSTransStatus = $this->pOSTransStatusRepository->create($input);

        return $this->sendResponse($pOSTransStatus->toArray(), trans('custom.p_o_s_trans_status_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/pOSTransStatuses/{id}",
     *      summary="Display the specified POSTransStatus",
     *      tags={"POSTransStatus"},
     *      description="Get POSTransStatus",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of POSTransStatus",
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
     *                  ref="#/definitions/POSTransStatus"
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
        /** @var POSTransStatus $pOSTransStatus */
        $pOSTransStatus = $this->pOSTransStatusRepository->findWithoutFail($id);

        if (empty($pOSTransStatus)) {
            return $this->sendError(trans('custom.p_o_s_trans_status_not_found'));
        }

        return $this->sendResponse($pOSTransStatus->toArray(), trans('custom.p_o_s_trans_status_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdatePOSTransStatusAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/pOSTransStatuses/{id}",
     *      summary="Update the specified POSTransStatus in storage",
     *      tags={"POSTransStatus"},
     *      description="Update POSTransStatus",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of POSTransStatus",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="POSTransStatus that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/POSTransStatus")
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
     *                  ref="#/definitions/POSTransStatus"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdatePOSTransStatusAPIRequest $request)
    {
        $input = $request->all();

        /** @var POSTransStatus $pOSTransStatus */
        $pOSTransStatus = $this->pOSTransStatusRepository->findWithoutFail($id);

        if (empty($pOSTransStatus)) {
            return $this->sendError(trans('custom.p_o_s_trans_status_not_found'));
        }

        $pOSTransStatus = $this->pOSTransStatusRepository->update($input, $id);

        return $this->sendResponse($pOSTransStatus->toArray(), trans('custom.postransstatus_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/pOSTransStatuses/{id}",
     *      summary="Remove the specified POSTransStatus from storage",
     *      tags={"POSTransStatus"},
     *      description="Delete POSTransStatus",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of POSTransStatus",
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
        /** @var POSTransStatus $pOSTransStatus */
        $pOSTransStatus = $this->pOSTransStatusRepository->findWithoutFail($id);

        if (empty($pOSTransStatus)) {
            return $this->sendError(trans('custom.p_o_s_trans_status_not_found'));
        }

        $pOSTransStatus->delete();

        return $this->sendSuccess('P O S Trans Status deleted successfully');
    }
}
