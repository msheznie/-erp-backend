<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreatePerformaMasterAPIRequest;
use App\Http\Requests\API\UpdatePerformaMasterAPIRequest;
use App\Models\PerformaMaster;
use App\Repositories\PerformaMasterRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class PerformaMasterController
 * @package App\Http\Controllers\API
 */

class PerformaMasterAPIController extends AppBaseController
{
    /** @var  PerformaMasterRepository */
    private $performaMasterRepository;

    public function __construct(PerformaMasterRepository $performaMasterRepo)
    {
        $this->performaMasterRepository = $performaMasterRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/performaMasters",
     *      summary="Get a listing of the PerformaMasters.",
     *      tags={"PerformaMaster"},
     *      description="Get all PerformaMasters",
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
     *                  @SWG\Items(ref="#/definitions/PerformaMaster")
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
        $this->performaMasterRepository->pushCriteria(new RequestCriteria($request));
        $this->performaMasterRepository->pushCriteria(new LimitOffsetCriteria($request));
        $performaMasters = $this->performaMasterRepository->all();

        return $this->sendResponse($performaMasters->toArray(), trans('custom.performa_masters_retrieved_successfully'));
    }

    /**
     * @param CreatePerformaMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/performaMasters",
     *      summary="Store a newly created PerformaMaster in storage",
     *      tags={"PerformaMaster"},
     *      description="Store PerformaMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="PerformaMaster that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/PerformaMaster")
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
     *                  ref="#/definitions/PerformaMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreatePerformaMasterAPIRequest $request)
    {
        $input = $request->all();

        $performaMasters = $this->performaMasterRepository->create($input);

        return $this->sendResponse($performaMasters->toArray(), trans('custom.performa_master_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/performaMasters/{id}",
     *      summary="Display the specified PerformaMaster",
     *      tags={"PerformaMaster"},
     *      description="Get PerformaMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PerformaMaster",
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
     *                  ref="#/definitions/PerformaMaster"
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
        /** @var PerformaMaster $performaMaster */
        $performaMaster = $this->performaMasterRepository->findWithoutFail($id);

        if (empty($performaMaster)) {
            return $this->sendError(trans('custom.performa_master_not_found'));
        }

        return $this->sendResponse($performaMaster->toArray(), trans('custom.performa_master_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdatePerformaMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/performaMasters/{id}",
     *      summary="Update the specified PerformaMaster in storage",
     *      tags={"PerformaMaster"},
     *      description="Update PerformaMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PerformaMaster",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="PerformaMaster that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/PerformaMaster")
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
     *                  ref="#/definitions/PerformaMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdatePerformaMasterAPIRequest $request)
    {
        $input = $request->all();

        /** @var PerformaMaster $performaMaster */
        $performaMaster = $this->performaMasterRepository->findWithoutFail($id);

        if (empty($performaMaster)) {
            return $this->sendError(trans('custom.performa_master_not_found'));
        }

        $performaMaster = $this->performaMasterRepository->update($input, $id);

        return $this->sendResponse($performaMaster->toArray(), trans('custom.performamaster_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/performaMasters/{id}",
     *      summary="Remove the specified PerformaMaster from storage",
     *      tags={"PerformaMaster"},
     *      description="Delete PerformaMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PerformaMaster",
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
        /** @var PerformaMaster $performaMaster */
        $performaMaster = $this->performaMasterRepository->findWithoutFail($id);

        if (empty($performaMaster)) {
            return $this->sendError(trans('custom.performa_master_not_found'));
        }

        $performaMaster->delete();

        return $this->sendResponse($id, trans('custom.performa_master_deleted_successfully'));
    }
}
