<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateAddonCostCategoriesAPIRequest;
use App\Http\Requests\API\UpdateAddonCostCategoriesAPIRequest;
use App\Models\AddonCostCategories;
use App\Models\ItemMaster;
use App\Repositories\AddonCostCategoriesRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class AddonCostCategoriesController
 * @package App\Http\Controllers\API
 */

class AddonCostCategoriesAPIController extends AppBaseController
{
    /** @var  AddonCostCategoriesRepository */
    private $addonCostCategoriesRepository;

    public function __construct(AddonCostCategoriesRepository $addonCostCategoriesRepo)
    {
        $this->addonCostCategoriesRepository = $addonCostCategoriesRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/addonCostCategories",
     *      summary="Get a listing of the AddonCostCategories.",
     *      tags={"AddonCostCategories"},
     *      description="Get all AddonCostCategories",
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
     *                  @SWG\Items(ref="#/definitions/AddonCostCategories")
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
        $this->addonCostCategoriesRepository->pushCriteria(new RequestCriteria($request));
        $this->addonCostCategoriesRepository->pushCriteria(new LimitOffsetCriteria($request));
        $addonCostCategories = $this->addonCostCategoriesRepository->all();

        return $this->sendResponse($addonCostCategories->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.addon_cost_categories')]));
    }

    /**
     * @param CreateAddonCostCategoriesAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/addonCostCategories",
     *      summary="Store a newly created AddonCostCategories in storage",
     *      tags={"AddonCostCategories"},
     *      description="Store AddonCostCategories",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="AddonCostCategories that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/AddonCostCategories")
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
     *                  ref="#/definitions/AddonCostCategories"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateAddonCostCategoriesAPIRequest $request)
    {
        $input = $request->all();

        $addonCostCategories = $this->addonCostCategoriesRepository->create($input);

        return $this->sendResponse($addonCostCategories->toArray(), trans('custom.save', ['attribute' => trans('custom.addon_cost_categories')]));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/addonCostCategories/{id}",
     *      summary="Display the specified AddonCostCategories",
     *      tags={"AddonCostCategories"},
     *      description="Get AddonCostCategories",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of AddonCostCategories",
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
     *                  ref="#/definitions/AddonCostCategories"
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
        /** @var AddonCostCategories $addonCostCategories */
        $addonCostCategories = $this->addonCostCategoriesRepository->findWithoutFail($id);

        if (empty($addonCostCategories)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.addon_cost_categories')]));
        }

        return $this->sendResponse($addonCostCategories->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.addon_cost_categories')]));
    }

    /**
     * @param int $id
     * @param UpdateAddonCostCategoriesAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/addonCostCategories/{id}",
     *      summary="Update the specified AddonCostCategories in storage",
     *      tags={"AddonCostCategories"},
     *      description="Update AddonCostCategories",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of AddonCostCategories",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="AddonCostCategories that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/AddonCostCategories")
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
     *                  ref="#/definitions/AddonCostCategories"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateAddonCostCategoriesAPIRequest $request)
    {
        $input = $request->all();

        /** @var AddonCostCategories $addonCostCategories */
        $addonCostCategories = $this->addonCostCategoriesRepository->findWithoutFail($id);

        if (empty($addonCostCategories)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.addon_cost_categories')]));
        }

        $addonCostCategories = $this->addonCostCategoriesRepository->update($input, $id);

        return $this->sendResponse($addonCostCategories->toArray(), trans('custom.update', ['attribute' => trans('custom.addon_cost_categories')]));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/addonCostCategories/{id}",
     *      summary="Remove the specified AddonCostCategories from storage",
     *      tags={"AddonCostCategories"},
     *      description="Delete AddonCostCategories",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of AddonCostCategories",
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
        /** @var AddonCostCategories $addonCostCategories */
        $addonCostCategories = $this->addonCostCategoriesRepository->findWithoutFail($id);

        if (empty($addonCostCategories)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.addon_cost_categories')]));
        }

        $addonCostCategories->delete();

        return $this->sendResponse($id, trans('custom.delete', ['attribute' => trans('custom.addon_cost_categories')]));
    }

    public function getLogisticCategories(Request $request)
    {
        $input = $request->all();
        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $categories = AddonCostCategories::with(['item_by']);

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $categories = $categories->where(function ($query) use ($search) {
                $query->whereHas('item_by', function($q)use ($search){
                    $q->where('itemDescription','LIKE', "%{$search}%")
                    ->orWhere('primaryCode','LIKE', "%{$search}%");
                })->orWhere('costCatDes','LIKE', "%{$search}%");
            });
        }

        return \DataTables::eloquent($categories)
            ->addColumn('Actions', 'Actions', "Actions")
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('idaddOnCostCategories', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    public function getItemsOptionForLogistic(Request $request)
    {
        $input = $request->all();
        $financeCategoryId = 2;

        $items = ItemMaster::where('financeCategoryMaster', $financeCategoryId);
        if (array_key_exists('search', $input)) {

            $search = $input['search'];

            $items = $items->where(function ($query) use ($search) {
                $query->where('primaryCode', 'LIKE', "%{$search}%")
                    ->orWhere('itemDescription', 'LIKE', "%{$search}%");
            });
        }

        $items = $items
            ->take(20)
            ->get();

        return $this->sendResponse($items->toArray(), trans('custom.data_retrieved_successfully'));
    }

}
