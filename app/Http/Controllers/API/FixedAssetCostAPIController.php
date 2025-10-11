<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateFixedAssetCostAPIRequest;
use App\Http\Requests\API\UpdateFixedAssetCostAPIRequest;
use App\Models\FixedAssetCost;
use App\Repositories\FixedAssetCostRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class FixedAssetCostController
 * @package App\Http\Controllers\API
 */

class FixedAssetCostAPIController extends AppBaseController
{
    /** @var  FixedAssetCostRepository */
    private $fixedAssetCostRepository;

    public function __construct(FixedAssetCostRepository $fixedAssetCostRepo)
    {
        $this->fixedAssetCostRepository = $fixedAssetCostRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/fixedAssetCosts",
     *      summary="Get a listing of the FixedAssetCosts.",
     *      tags={"FixedAssetCost"},
     *      description="Get all FixedAssetCosts",
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
     *                  @SWG\Items(ref="#/definitions/FixedAssetCost")
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
        $this->fixedAssetCostRepository->pushCriteria(new RequestCriteria($request));
        $this->fixedAssetCostRepository->pushCriteria(new LimitOffsetCriteria($request));
        $fixedAssetCosts = $this->fixedAssetCostRepository->all();

        return $this->sendResponse($fixedAssetCosts->toArray(), trans('custom.fixed_asset_costs_retrieved_successfully'));
    }

    /**
     * @param CreateFixedAssetCostAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/fixedAssetCosts",
     *      summary="Store a newly created FixedAssetCost in storage",
     *      tags={"FixedAssetCost"},
     *      description="Store FixedAssetCost",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="FixedAssetCost that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/FixedAssetCost")
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
     *                  ref="#/definitions/FixedAssetCost"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateFixedAssetCostAPIRequest $request)
    {
        $input = $request->all();

        $fixedAssetCosts = $this->fixedAssetCostRepository->create($input);

        return $this->sendResponse($fixedAssetCosts->toArray(), trans('custom.fixed_asset_cost_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/fixedAssetCosts/{id}",
     *      summary="Display the specified FixedAssetCost",
     *      tags={"FixedAssetCost"},
     *      description="Get FixedAssetCost",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of FixedAssetCost",
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
     *                  ref="#/definitions/FixedAssetCost"
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
        /** @var FixedAssetCost $fixedAssetCost */
        $fixedAssetCost = $this->fixedAssetCostRepository->findWithoutFail($id);

        if (empty($fixedAssetCost)) {
            return $this->sendError(trans('custom.fixed_asset_cost_not_found'));
        }

        return $this->sendResponse($fixedAssetCost->toArray(), trans('custom.fixed_asset_cost_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateFixedAssetCostAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/fixedAssetCosts/{id}",
     *      summary="Update the specified FixedAssetCost in storage",
     *      tags={"FixedAssetCost"},
     *      description="Update FixedAssetCost",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of FixedAssetCost",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="FixedAssetCost that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/FixedAssetCost")
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
     *                  ref="#/definitions/FixedAssetCost"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateFixedAssetCostAPIRequest $request)
    {
        $input = $request->all();

        /** @var FixedAssetCost $fixedAssetCost */
        $fixedAssetCost = $this->fixedAssetCostRepository->findWithoutFail($id);

        if (empty($fixedAssetCost)) {
            return $this->sendError(trans('custom.fixed_asset_cost_not_found'));
        }

        $fixedAssetCost = $this->fixedAssetCostRepository->update($input, $id);

        return $this->sendResponse($fixedAssetCost->toArray(), trans('custom.fixedassetcost_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/fixedAssetCosts/{id}",
     *      summary="Remove the specified FixedAssetCost from storage",
     *      tags={"FixedAssetCost"},
     *      description="Delete FixedAssetCost",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of FixedAssetCost",
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
        /** @var FixedAssetCost $fixedAssetCost */
        $fixedAssetCost = $this->fixedAssetCostRepository->findWithoutFail($id);

        if (empty($fixedAssetCost)) {
            return $this->sendError(trans('custom.fixed_asset_cost_not_found'));
        }

        $fixedAssetCost->delete();

        return $this->sendResponse($id, trans('custom.fixed_asset_cost_deleted_successfully'));
    }
}
