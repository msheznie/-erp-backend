<?php
/**
 * =============================================
 * -- File Name : LogisticShippingStatusAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Logistic
 * -- Author : Mohamed Fayas
 * -- Create date : 12 - September 2018
 * -- Description : This file contains the all CRUD for Logistic Shipping Status
 * -- REVISION HISTORY
 * -- Date: 12-September 2018 By: Fayas Description: Added new functions named as
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateLogisticShippingStatusAPIRequest;
use App\Http\Requests\API\UpdateLogisticShippingStatusAPIRequest;
use App\Models\LogisticShippingStatus;
use App\Repositories\LogisticShippingStatusRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class LogisticShippingStatusController
 * @package App\Http\Controllers\API
 */

class LogisticShippingStatusAPIController extends AppBaseController
{
    /** @var  LogisticShippingStatusRepository */
    private $logisticShippingStatusRepository;

    public function __construct(LogisticShippingStatusRepository $logisticShippingStatusRepo)
    {
        $this->logisticShippingStatusRepository = $logisticShippingStatusRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/logisticShippingStatuses",
     *      summary="Get a listing of the LogisticShippingStatuses.",
     *      tags={"LogisticShippingStatus"},
     *      description="Get all LogisticShippingStatuses",
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
     *                  @SWG\Items(ref="#/definitions/LogisticShippingStatus")
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
        $this->logisticShippingStatusRepository->pushCriteria(new RequestCriteria($request));
        $this->logisticShippingStatusRepository->pushCriteria(new LimitOffsetCriteria($request));
        $logisticShippingStatuses = $this->logisticShippingStatusRepository->all();

        return $this->sendResponse($logisticShippingStatuses->toArray(), trans('custom.logistic_shipping_statuses_retrieved_successfully'));
    }

    /**
     * @param CreateLogisticShippingStatusAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/logisticShippingStatuses",
     *      summary="Store a newly created LogisticShippingStatus in storage",
     *      tags={"LogisticShippingStatus"},
     *      description="Store LogisticShippingStatus",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="LogisticShippingStatus that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/LogisticShippingStatus")
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
     *                  ref="#/definitions/LogisticShippingStatus"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateLogisticShippingStatusAPIRequest $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToValue($input);

        $employee = \Helper::getEmployeeInfo();
        $input['createdPCID'] = gethostname();
        $input['createdUserID'] = $employee->empID;

        $logisticShippingStatuses = $this->logisticShippingStatusRepository->create($input);

        return $this->sendResponse($logisticShippingStatuses->toArray(), trans('custom.logistic_shipping_status_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/logisticShippingStatuses/{id}",
     *      summary="Display the specified LogisticShippingStatus",
     *      tags={"LogisticShippingStatus"},
     *      description="Get LogisticShippingStatus",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of LogisticShippingStatus",
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
     *                  ref="#/definitions/LogisticShippingStatus"
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
        /** @var LogisticShippingStatus $logisticShippingStatus */
        $logisticShippingStatus = $this->logisticShippingStatusRepository->findWithoutFail($id);

        if (empty($logisticShippingStatus)) {
            return $this->sendError(trans('custom.logistic_shipping_status_not_found'));
        }

        return $this->sendResponse($logisticShippingStatus->toArray(), trans('custom.logistic_shipping_status_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateLogisticShippingStatusAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/logisticShippingStatuses/{id}",
     *      summary="Update the specified LogisticShippingStatus in storage",
     *      tags={"LogisticShippingStatus"},
     *      description="Update LogisticShippingStatus",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of LogisticShippingStatus",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="LogisticShippingStatus that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/LogisticShippingStatus")
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
     *                  ref="#/definitions/LogisticShippingStatus"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateLogisticShippingStatusAPIRequest $request)
    {
        $input = $request->all();

        /** @var LogisticShippingStatus $logisticShippingStatus */
        $logisticShippingStatus = $this->logisticShippingStatusRepository->findWithoutFail($id);

        if (empty($logisticShippingStatus)) {
            return $this->sendError(trans('custom.logistic_shipping_status_not_found'));
        }

        if (isset($input['statusDate']) && $input['statusDate']) {
            $input['statusDate'] = new Carbon($input['statusDate']);
        }

        $logisticShippingStatus = $this->logisticShippingStatusRepository->update(array_only($input, ['statusDate','statusComment']), $id);

        return $this->sendResponse($logisticShippingStatus->toArray(), trans('custom.logisticshippingstatus_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/logisticShippingStatuses/{id}",
     *      summary="Remove the specified LogisticShippingStatus from storage",
     *      tags={"LogisticShippingStatus"},
     *      description="Delete LogisticShippingStatus",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of LogisticShippingStatus",
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
        /** @var LogisticShippingStatus $logisticShippingStatus */
        $logisticShippingStatus = $this->logisticShippingStatusRepository->findWithoutFail($id);

        if (empty($logisticShippingStatus)) {
            return $this->sendError(trans('custom.logistic_shipping_status_not_found'));
        }

        $logisticShippingStatus->delete();

        return $this->sendResponse($id, trans('custom.logistic_shipping_status_deleted_successfully'));
    }
}
