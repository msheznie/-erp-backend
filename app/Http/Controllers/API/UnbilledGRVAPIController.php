<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateUnbilledGRVAPIRequest;
use App\Http\Requests\API\UpdateUnbilledGRVAPIRequest;
use App\Models\UnbilledGRV;
use App\Repositories\UnbilledGRVRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class UnbilledGRVController
 * @package App\Http\Controllers\API
 */

class UnbilledGRVAPIController extends AppBaseController
{
    /** @var  UnbilledGRVRepository */
    private $unbilledGRVRepository;

    public function __construct(UnbilledGRVRepository $unbilledGRVRepo)
    {
        $this->unbilledGRVRepository = $unbilledGRVRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/unbilledGRVs",
     *      summary="Get a listing of the UnbilledGRVs.",
     *      tags={"UnbilledGRV"},
     *      description="Get all UnbilledGRVs",
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
     *                  @SWG\Items(ref="#/definitions/UnbilledGRV")
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
        $this->unbilledGRVRepository->pushCriteria(new RequestCriteria($request));
        $this->unbilledGRVRepository->pushCriteria(new LimitOffsetCriteria($request));
        $unbilledGRVs = $this->unbilledGRVRepository->all();

        return $this->sendResponse($unbilledGRVs->toArray(), trans('custom.unbilled_g_r_vs_retrieved_successfully'));
    }

    /**
     * @param CreateUnbilledGRVAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/unbilledGRVs",
     *      summary="Store a newly created UnbilledGRV in storage",
     *      tags={"UnbilledGRV"},
     *      description="Store UnbilledGRV",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="UnbilledGRV that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/UnbilledGRV")
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
     *                  ref="#/definitions/UnbilledGRV"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateUnbilledGRVAPIRequest $request)
    {
        $input = $request->all();

        $unbilledGRVs = $this->unbilledGRVRepository->create($input);

        return $this->sendResponse($unbilledGRVs->toArray(), trans('custom.unbilled_g_r_v_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/unbilledGRVs/{id}",
     *      summary="Display the specified UnbilledGRV",
     *      tags={"UnbilledGRV"},
     *      description="Get UnbilledGRV",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of UnbilledGRV",
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
     *                  ref="#/definitions/UnbilledGRV"
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
        /** @var UnbilledGRV $unbilledGRV */
        $unbilledGRV = $this->unbilledGRVRepository->findWithoutFail($id);

        if (empty($unbilledGRV)) {
            return $this->sendError(trans('custom.unbilled_g_r_v_not_found'));
        }

        return $this->sendResponse($unbilledGRV->toArray(), trans('custom.unbilled_g_r_v_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateUnbilledGRVAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/unbilledGRVs/{id}",
     *      summary="Update the specified UnbilledGRV in storage",
     *      tags={"UnbilledGRV"},
     *      description="Update UnbilledGRV",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of UnbilledGRV",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="UnbilledGRV that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/UnbilledGRV")
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
     *                  ref="#/definitions/UnbilledGRV"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateUnbilledGRVAPIRequest $request)
    {
        $input = $request->all();

        /** @var UnbilledGRV $unbilledGRV */
        $unbilledGRV = $this->unbilledGRVRepository->findWithoutFail($id);

        if (empty($unbilledGRV)) {
            return $this->sendError(trans('custom.unbilled_g_r_v_not_found'));
        }

        $unbilledGRV = $this->unbilledGRVRepository->update($input, $id);

        return $this->sendResponse($unbilledGRV->toArray(), trans('custom.unbilledgrv_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/unbilledGRVs/{id}",
     *      summary="Remove the specified UnbilledGRV from storage",
     *      tags={"UnbilledGRV"},
     *      description="Delete UnbilledGRV",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of UnbilledGRV",
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
        /** @var UnbilledGRV $unbilledGRV */
        $unbilledGRV = $this->unbilledGRVRepository->findWithoutFail($id);

        if (empty($unbilledGRV)) {
            return $this->sendError(trans('custom.unbilled_g_r_v_not_found'));
        }

        $unbilledGRV->delete();

        return $this->sendResponse($id, trans('custom.unbilled_g_r_v_deleted_successfully'));
    }
}
