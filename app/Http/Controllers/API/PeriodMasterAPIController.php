<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreatePeriodMasterAPIRequest;
use App\Http\Requests\API\UpdatePeriodMasterAPIRequest;
use App\Models\PeriodMaster;
use App\Repositories\PeriodMasterRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class PeriodMasterController
 * @package App\Http\Controllers\API
 */

class PeriodMasterAPIController extends AppBaseController
{
    /** @var  PeriodMasterRepository */
    private $periodMasterRepository;

    public function __construct(PeriodMasterRepository $periodMasterRepo)
    {
        $this->periodMasterRepository = $periodMasterRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/periodMasters",
     *      summary="Get a listing of the PeriodMasters.",
     *      tags={"PeriodMaster"},
     *      description="Get all PeriodMasters",
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
     *                  @SWG\Items(ref="#/definitions/PeriodMaster")
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
        $this->periodMasterRepository->pushCriteria(new RequestCriteria($request));
        $this->periodMasterRepository->pushCriteria(new LimitOffsetCriteria($request));
        $periodMasters = $this->periodMasterRepository->all();

        return $this->sendResponse($periodMasters->toArray(), trans('custom.period_masters_retrieved_successfully'));
    }

    /**
     * @param CreatePeriodMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/periodMasters",
     *      summary="Store a newly created PeriodMaster in storage",
     *      tags={"PeriodMaster"},
     *      description="Store PeriodMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="PeriodMaster that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/PeriodMaster")
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
     *                  ref="#/definitions/PeriodMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreatePeriodMasterAPIRequest $request)
    {
        $input = $request->all();

        $periodMasters = $this->periodMasterRepository->create($input);

        return $this->sendResponse($periodMasters->toArray(), trans('custom.period_master_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/periodMasters/{id}",
     *      summary="Display the specified PeriodMaster",
     *      tags={"PeriodMaster"},
     *      description="Get PeriodMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PeriodMaster",
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
     *                  ref="#/definitions/PeriodMaster"
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
        /** @var PeriodMaster $periodMaster */
        $periodMaster = $this->periodMasterRepository->findWithoutFail($id);

        if (empty($periodMaster)) {
            return $this->sendError(trans('custom.period_master_not_found'));
        }

        return $this->sendResponse($periodMaster->toArray(), trans('custom.period_master_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdatePeriodMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/periodMasters/{id}",
     *      summary="Update the specified PeriodMaster in storage",
     *      tags={"PeriodMaster"},
     *      description="Update PeriodMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PeriodMaster",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="PeriodMaster that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/PeriodMaster")
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
     *                  ref="#/definitions/PeriodMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdatePeriodMasterAPIRequest $request)
    {
        $input = $request->all();

        /** @var PeriodMaster $periodMaster */
        $periodMaster = $this->periodMasterRepository->findWithoutFail($id);

        if (empty($periodMaster)) {
            return $this->sendError(trans('custom.period_master_not_found'));
        }

        $periodMaster = $this->periodMasterRepository->update($input, $id);

        return $this->sendResponse($periodMaster->toArray(), trans('custom.periodmaster_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/periodMasters/{id}",
     *      summary="Remove the specified PeriodMaster from storage",
     *      tags={"PeriodMaster"},
     *      description="Delete PeriodMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PeriodMaster",
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
        /** @var PeriodMaster $periodMaster */
        $periodMaster = $this->periodMasterRepository->findWithoutFail($id);

        if (empty($periodMaster)) {
            return $this->sendError(trans('custom.period_master_not_found'));
        }

        $periodMaster->delete();

        return $this->sendResponse($id, trans('custom.period_master_deleted_successfully'));
    }
}
