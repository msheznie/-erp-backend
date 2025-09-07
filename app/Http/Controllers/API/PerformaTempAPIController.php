<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreatePerformaTempAPIRequest;
use App\Http\Requests\API\UpdatePerformaTempAPIRequest;
use App\Models\PerformaTemp;
use App\Repositories\PerformaTempRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class PerformaTempController
 * @package App\Http\Controllers\API
 */

class PerformaTempAPIController extends AppBaseController
{
    /** @var  PerformaTempRepository */
    private $performaTempRepository;

    public function __construct(PerformaTempRepository $performaTempRepo)
    {
        $this->performaTempRepository = $performaTempRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/performaTemps",
     *      summary="Get a listing of the PerformaTemps.",
     *      tags={"PerformaTemp"},
     *      description="Get all PerformaTemps",
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
     *                  @SWG\Items(ref="#/definitions/PerformaTemp")
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
        $this->performaTempRepository->pushCriteria(new RequestCriteria($request));
        $this->performaTempRepository->pushCriteria(new LimitOffsetCriteria($request));
        $performaTemps = $this->performaTempRepository->all();

        return $this->sendResponse($performaTemps->toArray(), trans('custom.performa_temps_retrieved_successfully'));
    }

    /**
     * @param CreatePerformaTempAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/performaTemps",
     *      summary="Store a newly created PerformaTemp in storage",
     *      tags={"PerformaTemp"},
     *      description="Store PerformaTemp",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="PerformaTemp that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/PerformaTemp")
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
     *                  ref="#/definitions/PerformaTemp"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreatePerformaTempAPIRequest $request)
    {
        $input = $request->all();

        $performaTemps = $this->performaTempRepository->create($input);

        return $this->sendResponse($performaTemps->toArray(), trans('custom.performa_temp_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/performaTemps/{id}",
     *      summary="Display the specified PerformaTemp",
     *      tags={"PerformaTemp"},
     *      description="Get PerformaTemp",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PerformaTemp",
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
     *                  ref="#/definitions/PerformaTemp"
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
        /** @var PerformaTemp $performaTemp */
        $performaTemp = $this->performaTempRepository->findWithoutFail($id);

        if (empty($performaTemp)) {
            return $this->sendError(trans('custom.performa_temp_not_found'));
        }

        return $this->sendResponse($performaTemp->toArray(), trans('custom.performa_temp_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdatePerformaTempAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/performaTemps/{id}",
     *      summary="Update the specified PerformaTemp in storage",
     *      tags={"PerformaTemp"},
     *      description="Update PerformaTemp",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PerformaTemp",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="PerformaTemp that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/PerformaTemp")
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
     *                  ref="#/definitions/PerformaTemp"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdatePerformaTempAPIRequest $request)
    {
        $input = $request->all();

        /** @var PerformaTemp $performaTemp */
        $performaTemp = $this->performaTempRepository->findWithoutFail($id);

        if (empty($performaTemp)) {
            return $this->sendError(trans('custom.performa_temp_not_found'));
        }

        $performaTemp = $this->performaTempRepository->update($input, $id);

        return $this->sendResponse($performaTemp->toArray(), trans('custom.performatemp_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/performaTemps/{id}",
     *      summary="Remove the specified PerformaTemp from storage",
     *      tags={"PerformaTemp"},
     *      description="Delete PerformaTemp",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PerformaTemp",
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
        /** @var PerformaTemp $performaTemp */
        $performaTemp = $this->performaTempRepository->findWithoutFail($id);

        if (empty($performaTemp)) {
            return $this->sendError(trans('custom.performa_temp_not_found'));
        }

        $performaTemp->delete();

        return $this->sendResponse($id, trans('custom.performa_temp_deleted_successfully'));
    }
}
