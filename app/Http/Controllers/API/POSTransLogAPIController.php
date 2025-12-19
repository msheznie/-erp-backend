<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreatePOSTransLogAPIRequest;
use App\Http\Requests\API\UpdatePOSTransLogAPIRequest;
use App\Models\POSTransLog;
use App\Repositories\POSTransLogRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class POSTransLogController
 * @package App\Http\Controllers\API
 */

class POSTransLogAPIController extends AppBaseController
{
    /** @var  POSTransLogRepository */
    private $pOSTransLogRepository;

    public function __construct(POSTransLogRepository $pOSTransLogRepo)
    {
        $this->pOSTransLogRepository = $pOSTransLogRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/pOSTransLogs",
     *      summary="Get a listing of the POSTransLogs.",
     *      tags={"POSTransLog"},
     *      description="Get all POSTransLogs",
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
     *                  @SWG\Items(ref="#/definitions/POSTransLog")
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
        $this->pOSTransLogRepository->pushCriteria(new RequestCriteria($request));
        $this->pOSTransLogRepository->pushCriteria(new LimitOffsetCriteria($request));
        $pOSTransLogs = $this->pOSTransLogRepository->all();

        return $this->sendResponse($pOSTransLogs->toArray(), trans('custom.p_o_s_trans_logs_retrieved_successfully'));
    }

    /**
     * @param CreatePOSTransLogAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/pOSTransLogs",
     *      summary="Store a newly created POSTransLog in storage",
     *      tags={"POSTransLog"},
     *      description="Store POSTransLog",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="POSTransLog that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/POSTransLog")
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
     *                  ref="#/definitions/POSTransLog"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreatePOSTransLogAPIRequest $request)
    {
        $input = $request->all();

        $pOSTransLog = $this->pOSTransLogRepository->create($input);

        return $this->sendResponse($pOSTransLog->toArray(), trans('custom.p_o_s_trans_log_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/pOSTransLogs/{id}",
     *      summary="Display the specified POSTransLog",
     *      tags={"POSTransLog"},
     *      description="Get POSTransLog",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of POSTransLog",
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
     *                  ref="#/definitions/POSTransLog"
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
        /** @var POSTransLog $pOSTransLog */
        $pOSTransLog = $this->pOSTransLogRepository->findWithoutFail($id);

        if (empty($pOSTransLog)) {
            return $this->sendError(trans('custom.p_o_s_trans_log_not_found'));
        }

        return $this->sendResponse($pOSTransLog->toArray(), trans('custom.p_o_s_trans_log_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdatePOSTransLogAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/pOSTransLogs/{id}",
     *      summary="Update the specified POSTransLog in storage",
     *      tags={"POSTransLog"},
     *      description="Update POSTransLog",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of POSTransLog",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="POSTransLog that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/POSTransLog")
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
     *                  ref="#/definitions/POSTransLog"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdatePOSTransLogAPIRequest $request)
    {
        $input = $request->all();

        /** @var POSTransLog $pOSTransLog */
        $pOSTransLog = $this->pOSTransLogRepository->findWithoutFail($id);

        if (empty($pOSTransLog)) {
            return $this->sendError(trans('custom.p_o_s_trans_log_not_found'));
        }

        $pOSTransLog = $this->pOSTransLogRepository->update($input, $id);

        return $this->sendResponse($pOSTransLog->toArray(), trans('custom.postranslog_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/pOSTransLogs/{id}",
     *      summary="Remove the specified POSTransLog from storage",
     *      tags={"POSTransLog"},
     *      description="Delete POSTransLog",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of POSTransLog",
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
        /** @var POSTransLog $pOSTransLog */
        $pOSTransLog = $this->pOSTransLogRepository->findWithoutFail($id);

        if (empty($pOSTransLog)) {
            return $this->sendError(trans('custom.p_o_s_trans_log_not_found'));
        }

        $pOSTransLog->delete();

        return $this->sendSuccess('P O S Trans Log deleted successfully');
    }
}
