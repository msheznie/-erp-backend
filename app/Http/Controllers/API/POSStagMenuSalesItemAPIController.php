<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreatePOSStagMenuSalesItemAPIRequest;
use App\Http\Requests\API\UpdatePOSStagMenuSalesItemAPIRequest;
use App\Models\POSStagMenuSalesItem;
use App\Repositories\POSStagMenuSalesItemRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class POSStagMenuSalesItemController
 * @package App\Http\Controllers\API
 */

class POSStagMenuSalesItemAPIController extends AppBaseController
{
    /** @var  POSStagMenuSalesItemRepository */
    private $pOSStagMenuSalesItemRepository;

    public function __construct(POSStagMenuSalesItemRepository $pOSStagMenuSalesItemRepo)
    {
        $this->pOSStagMenuSalesItemRepository = $pOSStagMenuSalesItemRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/pOSStagMenuSalesItems",
     *      summary="Get a listing of the POSStagMenuSalesItems.",
     *      tags={"POSStagMenuSalesItem"},
     *      description="Get all POSStagMenuSalesItems",
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
     *                  @SWG\Items(ref="#/definitions/POSStagMenuSalesItem")
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
        $this->pOSStagMenuSalesItemRepository->pushCriteria(new RequestCriteria($request));
        $this->pOSStagMenuSalesItemRepository->pushCriteria(new LimitOffsetCriteria($request));
        $pOSStagMenuSalesItems = $this->pOSStagMenuSalesItemRepository->all();

        return $this->sendResponse($pOSStagMenuSalesItems->toArray(), trans('custom.p_o_s_stag_menu_sales_items_retrieved_successfully'));
    }

    /**
     * @param CreatePOSStagMenuSalesItemAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/pOSStagMenuSalesItems",
     *      summary="Store a newly created POSStagMenuSalesItem in storage",
     *      tags={"POSStagMenuSalesItem"},
     *      description="Store POSStagMenuSalesItem",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="POSStagMenuSalesItem that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/POSStagMenuSalesItem")
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
     *                  ref="#/definitions/POSStagMenuSalesItem"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreatePOSStagMenuSalesItemAPIRequest $request)
    {
        $input = $request->all();

        $pOSStagMenuSalesItem = $this->pOSStagMenuSalesItemRepository->create($input);

        return $this->sendResponse($pOSStagMenuSalesItem->toArray(), trans('custom.p_o_s_stag_menu_sales_item_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/pOSStagMenuSalesItems/{id}",
     *      summary="Display the specified POSStagMenuSalesItem",
     *      tags={"POSStagMenuSalesItem"},
     *      description="Get POSStagMenuSalesItem",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of POSStagMenuSalesItem",
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
     *                  ref="#/definitions/POSStagMenuSalesItem"
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
        /** @var POSStagMenuSalesItem $pOSStagMenuSalesItem */
        $pOSStagMenuSalesItem = $this->pOSStagMenuSalesItemRepository->findWithoutFail($id);

        if (empty($pOSStagMenuSalesItem)) {
            return $this->sendError(trans('custom.p_o_s_stag_menu_sales_item_not_found'));
        }

        return $this->sendResponse($pOSStagMenuSalesItem->toArray(), trans('custom.p_o_s_stag_menu_sales_item_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdatePOSStagMenuSalesItemAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/pOSStagMenuSalesItems/{id}",
     *      summary="Update the specified POSStagMenuSalesItem in storage",
     *      tags={"POSStagMenuSalesItem"},
     *      description="Update POSStagMenuSalesItem",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of POSStagMenuSalesItem",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="POSStagMenuSalesItem that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/POSStagMenuSalesItem")
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
     *                  ref="#/definitions/POSStagMenuSalesItem"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdatePOSStagMenuSalesItemAPIRequest $request)
    {
        $input = $request->all();

        /** @var POSStagMenuSalesItem $pOSStagMenuSalesItem */
        $pOSStagMenuSalesItem = $this->pOSStagMenuSalesItemRepository->findWithoutFail($id);

        if (empty($pOSStagMenuSalesItem)) {
            return $this->sendError(trans('custom.p_o_s_stag_menu_sales_item_not_found'));
        }

        $pOSStagMenuSalesItem = $this->pOSStagMenuSalesItemRepository->update($input, $id);

        return $this->sendResponse($pOSStagMenuSalesItem->toArray(), trans('custom.posstagmenusalesitem_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/pOSStagMenuSalesItems/{id}",
     *      summary="Remove the specified POSStagMenuSalesItem from storage",
     *      tags={"POSStagMenuSalesItem"},
     *      description="Delete POSStagMenuSalesItem",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of POSStagMenuSalesItem",
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
        /** @var POSStagMenuSalesItem $pOSStagMenuSalesItem */
        $pOSStagMenuSalesItem = $this->pOSStagMenuSalesItemRepository->findWithoutFail($id);

        if (empty($pOSStagMenuSalesItem)) {
            return $this->sendError(trans('custom.p_o_s_stag_menu_sales_item_not_found'));
        }

        $pOSStagMenuSalesItem->delete();

        return $this->sendSuccess('P O S Stag Menu Sales Item deleted successfully');
    }
}
