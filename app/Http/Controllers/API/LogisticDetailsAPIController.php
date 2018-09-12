<?php
/**
 * =============================================
 * -- File Name : LogisticDetailsAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Logistic
 * -- Author : Mohamed Fayas
 * -- Create date : 12 - September 2018
 * -- Description : This file contains the all CRUD for Logistic Details
 * -- REVISION HISTORY
 * -- Date: 12-September 2018 By: Fayas Description: Added new functions named as
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateLogisticDetailsAPIRequest;
use App\Http\Requests\API\UpdateLogisticDetailsAPIRequest;
use App\Models\LogisticDetails;
use App\Repositories\LogisticDetailsRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class LogisticDetailsController
 * @package App\Http\Controllers\API
 */

class LogisticDetailsAPIController extends AppBaseController
{
    /** @var  LogisticDetailsRepository */
    private $logisticDetailsRepository;

    public function __construct(LogisticDetailsRepository $logisticDetailsRepo)
    {
        $this->logisticDetailsRepository = $logisticDetailsRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/logisticDetails",
     *      summary="Get a listing of the LogisticDetails.",
     *      tags={"LogisticDetails"},
     *      description="Get all LogisticDetails",
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
     *                  @SWG\Items(ref="#/definitions/LogisticDetails")
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
        $this->logisticDetailsRepository->pushCriteria(new RequestCriteria($request));
        $this->logisticDetailsRepository->pushCriteria(new LimitOffsetCriteria($request));
        $logisticDetails = $this->logisticDetailsRepository->all();

        return $this->sendResponse($logisticDetails->toArray(), 'Logistic Details retrieved successfully');
    }

    /**
     * @param CreateLogisticDetailsAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/logisticDetails",
     *      summary="Store a newly created LogisticDetails in storage",
     *      tags={"LogisticDetails"},
     *      description="Store LogisticDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="LogisticDetails that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/LogisticDetails")
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
     *                  ref="#/definitions/LogisticDetails"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateLogisticDetailsAPIRequest $request)
    {
        $input = $request->all();

        $logisticDetails = $this->logisticDetailsRepository->create($input);

        return $this->sendResponse($logisticDetails->toArray(), 'Logistic Details saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/logisticDetails/{id}",
     *      summary="Display the specified LogisticDetails",
     *      tags={"LogisticDetails"},
     *      description="Get LogisticDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of LogisticDetails",
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
     *                  ref="#/definitions/LogisticDetails"
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
        /** @var LogisticDetails $logisticDetails */
        $logisticDetails = $this->logisticDetailsRepository->findWithoutFail($id);

        if (empty($logisticDetails)) {
            return $this->sendError('Logistic Details not found');
        }

        return $this->sendResponse($logisticDetails->toArray(), 'Logistic Details retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateLogisticDetailsAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/logisticDetails/{id}",
     *      summary="Update the specified LogisticDetails in storage",
     *      tags={"LogisticDetails"},
     *      description="Update LogisticDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of LogisticDetails",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="LogisticDetails that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/LogisticDetails")
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
     *                  ref="#/definitions/LogisticDetails"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateLogisticDetailsAPIRequest $request)
    {
        $input = $request->all();

        /** @var LogisticDetails $logisticDetails */
        $logisticDetails = $this->logisticDetailsRepository->findWithoutFail($id);

        if (empty($logisticDetails)) {
            return $this->sendError('Logistic Details not found');
        }

        $logisticDetails = $this->logisticDetailsRepository->update($input, $id);

        return $this->sendResponse($logisticDetails->toArray(), 'LogisticDetails updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/logisticDetails/{id}",
     *      summary="Remove the specified LogisticDetails from storage",
     *      tags={"LogisticDetails"},
     *      description="Delete LogisticDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of LogisticDetails",
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
        /** @var LogisticDetails $logisticDetails */
        $logisticDetails = $this->logisticDetailsRepository->findWithoutFail($id);

        if (empty($logisticDetails)) {
            return $this->sendError('Logistic Details not found');
        }

        $logisticDetails->delete();

        return $this->sendResponse($id, 'Logistic Details deleted successfully');
    }
}
