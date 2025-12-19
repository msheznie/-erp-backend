<?php
/**
 * =============================================
 * -- File Name : LogisticStatusAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Logistic
 * -- Author : Mohamed Fayas
 * -- Create date : 12 - September 2018
 * -- Description : This file contains the all CRUD for Logistic Status
 * -- REVISION HISTORY
 * -- Date: 12-September 2018 By: Fayas Description: Added new functions named as
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateLogisticStatusAPIRequest;
use App\Http\Requests\API\UpdateLogisticStatusAPIRequest;
use App\Models\LogisticStatus;
use App\Repositories\LogisticStatusRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class LogisticStatusController
 * @package App\Http\Controllers\API
 */

class LogisticStatusAPIController extends AppBaseController
{
    /** @var  LogisticStatusRepository */
    private $logisticStatusRepository;

    public function __construct(LogisticStatusRepository $logisticStatusRepo)
    {
        $this->logisticStatusRepository = $logisticStatusRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/logisticStatuses",
     *      summary="Get a listing of the LogisticStatuses.",
     *      tags={"LogisticStatus"},
     *      description="Get all LogisticStatuses",
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
     *                  @SWG\Items(ref="#/definitions/LogisticStatus")
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
        $this->logisticStatusRepository->pushCriteria(new RequestCriteria($request));
        $this->logisticStatusRepository->pushCriteria(new LimitOffsetCriteria($request));
        $logisticStatuses = $this->logisticStatusRepository->all();

        return $this->sendResponse($logisticStatuses->toArray(), trans('custom.logistic_statuses_retrieved_successfully'));
    }

    /**
     * @param CreateLogisticStatusAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/logisticStatuses",
     *      summary="Store a newly created LogisticStatus in storage",
     *      tags={"LogisticStatus"},
     *      description="Store LogisticStatus",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="LogisticStatus that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/LogisticStatus")
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
     *                  ref="#/definitions/LogisticStatus"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateLogisticStatusAPIRequest $request)
    {
        $input = $request->all();

        $logisticStatuses = $this->logisticStatusRepository->create($input);

        return $this->sendResponse($logisticStatuses->toArray(), trans('custom.logistic_status_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/logisticStatuses/{id}",
     *      summary="Display the specified LogisticStatus",
     *      tags={"LogisticStatus"},
     *      description="Get LogisticStatus",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of LogisticStatus",
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
     *                  ref="#/definitions/LogisticStatus"
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
        /** @var LogisticStatus $logisticStatus */
        $logisticStatus = $this->logisticStatusRepository->findWithoutFail($id);

        if (empty($logisticStatus)) {
            return $this->sendError(trans('custom.logistic_status_not_found'));
        }

        return $this->sendResponse($logisticStatus->toArray(), trans('custom.logistic_status_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateLogisticStatusAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/logisticStatuses/{id}",
     *      summary="Update the specified LogisticStatus in storage",
     *      tags={"LogisticStatus"},
     *      description="Update LogisticStatus",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of LogisticStatus",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="LogisticStatus that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/LogisticStatus")
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
     *                  ref="#/definitions/LogisticStatus"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateLogisticStatusAPIRequest $request)
    {
        $input = $request->all();

        /** @var LogisticStatus $logisticStatus */
        $logisticStatus = $this->logisticStatusRepository->findWithoutFail($id);

        if (empty($logisticStatus)) {
            return $this->sendError(trans('custom.logistic_status_not_found'));
        }

        $logisticStatus = $this->logisticStatusRepository->update($input, $id);

        return $this->sendResponse($logisticStatus->toArray(), trans('custom.logisticstatus_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/logisticStatuses/{id}",
     *      summary="Remove the specified LogisticStatus from storage",
     *      tags={"LogisticStatus"},
     *      description="Delete LogisticStatus",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of LogisticStatus",
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
        /** @var LogisticStatus $logisticStatus */
        $logisticStatus = $this->logisticStatusRepository->findWithoutFail($id);

        if (empty($logisticStatus)) {
            return $this->sendError(trans('custom.logistic_status_not_found'));
        }

        $logisticStatus->delete();

        return $this->sendResponse($id, trans('custom.logistic_status_deleted_successfully'));
    }
}
