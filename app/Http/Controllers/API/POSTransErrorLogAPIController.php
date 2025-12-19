<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreatePOSTransErrorLogAPIRequest;
use App\Http\Requests\API\UpdatePOSTransErrorLogAPIRequest;
use App\Models\POSTransErrorLog;
use App\Repositories\POSTransErrorLogRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class POSTransErrorLogController
 * @package App\Http\Controllers\API
 */

class POSTransErrorLogAPIController extends AppBaseController
{
    /** @var  POSTransErrorLogRepository */
    private $pOSTransErrorLogRepository;

    public function __construct(POSTransErrorLogRepository $pOSTransErrorLogRepo)
    {
        $this->pOSTransErrorLogRepository = $pOSTransErrorLogRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/pOSTransErrorLogs",
     *      summary="Get a listing of the POSTransErrorLogs.",
     *      tags={"POSTransErrorLog"},
     *      description="Get all POSTransErrorLogs",
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
     *                  @SWG\Items(ref="#/definitions/POSTransErrorLog")
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
        $this->pOSTransErrorLogRepository->pushCriteria(new RequestCriteria($request));
        $this->pOSTransErrorLogRepository->pushCriteria(new LimitOffsetCriteria($request));
        $pOSTransErrorLogs = $this->pOSTransErrorLogRepository->all();

        return $this->sendResponse($pOSTransErrorLogs->toArray(), trans('custom.p_o_s_trans_error_logs_retrieved_successfully'));
    }

    /**
     * @param CreatePOSTransErrorLogAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/pOSTransErrorLogs",
     *      summary="Store a newly created POSTransErrorLog in storage",
     *      tags={"POSTransErrorLog"},
     *      description="Store POSTransErrorLog",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="POSTransErrorLog that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/POSTransErrorLog")
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
     *                  ref="#/definitions/POSTransErrorLog"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreatePOSTransErrorLogAPIRequest $request)
    {
        $input = $request->all();

        $pOSTransErrorLog = $this->pOSTransErrorLogRepository->create($input);

        return $this->sendResponse($pOSTransErrorLog->toArray(), trans('custom.p_o_s_trans_error_log_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/pOSTransErrorLogs/{id}",
     *      summary="Display the specified POSTransErrorLog",
     *      tags={"POSTransErrorLog"},
     *      description="Get POSTransErrorLog",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of POSTransErrorLog",
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
     *                  ref="#/definitions/POSTransErrorLog"
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
        /** @var POSTransErrorLog $pOSTransErrorLog */
        $pOSTransErrorLog = $this->pOSTransErrorLogRepository->findWithoutFail($id);

        if (empty($pOSTransErrorLog)) {
            return $this->sendError(trans('custom.p_o_s_trans_error_log_not_found'));
        }

        return $this->sendResponse($pOSTransErrorLog->toArray(), trans('custom.p_o_s_trans_error_log_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdatePOSTransErrorLogAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/pOSTransErrorLogs/{id}",
     *      summary="Update the specified POSTransErrorLog in storage",
     *      tags={"POSTransErrorLog"},
     *      description="Update POSTransErrorLog",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of POSTransErrorLog",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="POSTransErrorLog that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/POSTransErrorLog")
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
     *                  ref="#/definitions/POSTransErrorLog"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdatePOSTransErrorLogAPIRequest $request)
    {
        $input = $request->all();

        /** @var POSTransErrorLog $pOSTransErrorLog */
        $pOSTransErrorLog = $this->pOSTransErrorLogRepository->findWithoutFail($id);

        if (empty($pOSTransErrorLog)) {
            return $this->sendError(trans('custom.p_o_s_trans_error_log_not_found'));
        }

        $pOSTransErrorLog = $this->pOSTransErrorLogRepository->update($input, $id);

        return $this->sendResponse($pOSTransErrorLog->toArray(), trans('custom.postranserrorlog_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/pOSTransErrorLogs/{id}",
     *      summary="Remove the specified POSTransErrorLog from storage",
     *      tags={"POSTransErrorLog"},
     *      description="Delete POSTransErrorLog",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of POSTransErrorLog",
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
        /** @var POSTransErrorLog $pOSTransErrorLog */
        $pOSTransErrorLog = $this->pOSTransErrorLogRepository->findWithoutFail($id);

        if (empty($pOSTransErrorLog)) {
            return $this->sendError(trans('custom.p_o_s_trans_error_log_not_found'));
        }

        $pOSTransErrorLog->delete();

        return $this->sendSuccess('P O S Trans Error Log deleted successfully');
    }
}
