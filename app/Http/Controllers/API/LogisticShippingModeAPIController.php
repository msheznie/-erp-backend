<?php
/**
 * =============================================
 * -- File Name : LogisticShippingModeAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Logistic
 * -- Author : Mohamed Fayas
 * -- Create date : 12 - September 2018
 * -- Description : This file contains the all CRUD for Logistic Shipping Mode
 * -- REVISION HISTORY
 * -- Date: 12-September 2018 By: Fayas Description: Added new functions named as
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateLogisticShippingModeAPIRequest;
use App\Http\Requests\API\UpdateLogisticShippingModeAPIRequest;
use App\Models\LogisticShippingMode;
use App\Repositories\LogisticShippingModeRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class LogisticShippingModeController
 * @package App\Http\Controllers\API
 */

class LogisticShippingModeAPIController extends AppBaseController
{
    /** @var  LogisticShippingModeRepository */
    private $logisticShippingModeRepository;

    public function __construct(LogisticShippingModeRepository $logisticShippingModeRepo)
    {
        $this->logisticShippingModeRepository = $logisticShippingModeRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/logisticShippingModes",
     *      summary="Get a listing of the LogisticShippingModes.",
     *      tags={"LogisticShippingMode"},
     *      description="Get all LogisticShippingModes",
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
     *                  @SWG\Items(ref="#/definitions/LogisticShippingMode")
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
        $this->logisticShippingModeRepository->pushCriteria(new RequestCriteria($request));
        $this->logisticShippingModeRepository->pushCriteria(new LimitOffsetCriteria($request));
        $logisticShippingModes = $this->logisticShippingModeRepository->all();

        return $this->sendResponse($logisticShippingModes->toArray(), trans('custom.logistic_shipping_modes_retrieved_successfully'));
    }

    /**
     * @param CreateLogisticShippingModeAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/logisticShippingModes",
     *      summary="Store a newly created LogisticShippingMode in storage",
     *      tags={"LogisticShippingMode"},
     *      description="Store LogisticShippingMode",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="LogisticShippingMode that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/LogisticShippingMode")
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
     *                  ref="#/definitions/LogisticShippingMode"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateLogisticShippingModeAPIRequest $request)
    {
        $input = $request->all();

        $logisticShippingModes = $this->logisticShippingModeRepository->create($input);

        return $this->sendResponse($logisticShippingModes->toArray(), trans('custom.logistic_shipping_mode_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/logisticShippingModes/{id}",
     *      summary="Display the specified LogisticShippingMode",
     *      tags={"LogisticShippingMode"},
     *      description="Get LogisticShippingMode",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of LogisticShippingMode",
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
     *                  ref="#/definitions/LogisticShippingMode"
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
        /** @var LogisticShippingMode $logisticShippingMode */
        $logisticShippingMode = $this->logisticShippingModeRepository->findWithoutFail($id);

        if (empty($logisticShippingMode)) {
            return $this->sendError(trans('custom.logistic_shipping_mode_not_found'));
        }

        return $this->sendResponse($logisticShippingMode->toArray(), trans('custom.logistic_shipping_mode_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateLogisticShippingModeAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/logisticShippingModes/{id}",
     *      summary="Update the specified LogisticShippingMode in storage",
     *      tags={"LogisticShippingMode"},
     *      description="Update LogisticShippingMode",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of LogisticShippingMode",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="LogisticShippingMode that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/LogisticShippingMode")
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
     *                  ref="#/definitions/LogisticShippingMode"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateLogisticShippingModeAPIRequest $request)
    {
        $input = $request->all();

        /** @var LogisticShippingMode $logisticShippingMode */
        $logisticShippingMode = $this->logisticShippingModeRepository->findWithoutFail($id);

        if (empty($logisticShippingMode)) {
            return $this->sendError(trans('custom.logistic_shipping_mode_not_found'));
        }

        $logisticShippingMode = $this->logisticShippingModeRepository->update($input, $id);

        return $this->sendResponse($logisticShippingMode->toArray(), trans('custom.logisticshippingmode_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/logisticShippingModes/{id}",
     *      summary="Remove the specified LogisticShippingMode from storage",
     *      tags={"LogisticShippingMode"},
     *      description="Delete LogisticShippingMode",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of LogisticShippingMode",
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
        /** @var LogisticShippingMode $logisticShippingMode */
        $logisticShippingMode = $this->logisticShippingModeRepository->findWithoutFail($id);

        if (empty($logisticShippingMode)) {
            return $this->sendError(trans('custom.logistic_shipping_mode_not_found'));
        }

        $logisticShippingMode->delete();

        return $this->sendResponse($id, trans('custom.logistic_shipping_mode_deleted_successfully'));
    }
}
