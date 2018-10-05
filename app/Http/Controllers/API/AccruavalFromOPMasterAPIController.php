<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateAccruavalFromOPMasterAPIRequest;
use App\Http\Requests\API\UpdateAccruavalFromOPMasterAPIRequest;
use App\Models\AccruavalFromOPMaster;
use App\Repositories\AccruavalFromOPMasterRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class AccruavalFromOPMasterController
 * @package App\Http\Controllers\API
 */

class AccruavalFromOPMasterAPIController extends AppBaseController
{
    /** @var  AccruavalFromOPMasterRepository */
    private $accruavalFromOPMasterRepository;

    public function __construct(AccruavalFromOPMasterRepository $accruavalFromOPMasterRepo)
    {
        $this->accruavalFromOPMasterRepository = $accruavalFromOPMasterRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/accruavalFromOPMasters",
     *      summary="Get a listing of the AccruavalFromOPMasters.",
     *      tags={"AccruavalFromOPMaster"},
     *      description="Get all AccruavalFromOPMasters",
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
     *                  @SWG\Items(ref="#/definitions/AccruavalFromOPMaster")
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
        $this->accruavalFromOPMasterRepository->pushCriteria(new RequestCriteria($request));
        $this->accruavalFromOPMasterRepository->pushCriteria(new LimitOffsetCriteria($request));
        $accruavalFromOPMasters = $this->accruavalFromOPMasterRepository->all();

        return $this->sendResponse($accruavalFromOPMasters->toArray(), 'Accruaval From O P Masters retrieved successfully');
    }

    /**
     * @param CreateAccruavalFromOPMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/accruavalFromOPMasters",
     *      summary="Store a newly created AccruavalFromOPMaster in storage",
     *      tags={"AccruavalFromOPMaster"},
     *      description="Store AccruavalFromOPMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="AccruavalFromOPMaster that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/AccruavalFromOPMaster")
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
     *                  ref="#/definitions/AccruavalFromOPMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateAccruavalFromOPMasterAPIRequest $request)
    {
        $input = $request->all();

        $accruavalFromOPMasters = $this->accruavalFromOPMasterRepository->create($input);

        return $this->sendResponse($accruavalFromOPMasters->toArray(), 'Accruaval From O P Master saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/accruavalFromOPMasters/{id}",
     *      summary="Display the specified AccruavalFromOPMaster",
     *      tags={"AccruavalFromOPMaster"},
     *      description="Get AccruavalFromOPMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of AccruavalFromOPMaster",
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
     *                  ref="#/definitions/AccruavalFromOPMaster"
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
        /** @var AccruavalFromOPMaster $accruavalFromOPMaster */
        $accruavalFromOPMaster = $this->accruavalFromOPMasterRepository->findWithoutFail($id);

        if (empty($accruavalFromOPMaster)) {
            return $this->sendError('Accruaval From O P Master not found');
        }

        return $this->sendResponse($accruavalFromOPMaster->toArray(), 'Accruaval From O P Master retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateAccruavalFromOPMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/accruavalFromOPMasters/{id}",
     *      summary="Update the specified AccruavalFromOPMaster in storage",
     *      tags={"AccruavalFromOPMaster"},
     *      description="Update AccruavalFromOPMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of AccruavalFromOPMaster",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="AccruavalFromOPMaster that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/AccruavalFromOPMaster")
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
     *                  ref="#/definitions/AccruavalFromOPMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateAccruavalFromOPMasterAPIRequest $request)
    {
        $input = $request->all();

        /** @var AccruavalFromOPMaster $accruavalFromOPMaster */
        $accruavalFromOPMaster = $this->accruavalFromOPMasterRepository->findWithoutFail($id);

        if (empty($accruavalFromOPMaster)) {
            return $this->sendError('Accruaval From O P Master not found');
        }

        $accruavalFromOPMaster = $this->accruavalFromOPMasterRepository->update($input, $id);

        return $this->sendResponse($accruavalFromOPMaster->toArray(), 'AccruavalFromOPMaster updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/accruavalFromOPMasters/{id}",
     *      summary="Remove the specified AccruavalFromOPMaster from storage",
     *      tags={"AccruavalFromOPMaster"},
     *      description="Delete AccruavalFromOPMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of AccruavalFromOPMaster",
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
        /** @var AccruavalFromOPMaster $accruavalFromOPMaster */
        $accruavalFromOPMaster = $this->accruavalFromOPMasterRepository->findWithoutFail($id);

        if (empty($accruavalFromOPMaster)) {
            return $this->sendError('Accruaval From O P Master not found');
        }

        $accruavalFromOPMaster->delete();

        return $this->sendResponse($id, 'Accruaval From O P Master deleted successfully');
    }
}
