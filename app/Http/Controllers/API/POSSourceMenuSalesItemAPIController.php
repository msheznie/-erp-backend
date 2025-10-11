<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreatePOSSourceMenuSalesItemAPIRequest;
use App\Http\Requests\API\UpdatePOSSourceMenuSalesItemAPIRequest;
use App\Models\POSSourceMenuSalesItem;
use App\Repositories\POSSourceMenuSalesItemRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class POSSourceMenuSalesItemController
 * @package App\Http\Controllers\API
 */

class POSSourceMenuSalesItemAPIController extends AppBaseController
{
    /** @var  POSSourceMenuSalesItemRepository */
    private $pOSSourceMenuSalesItemRepository;

    public function __construct(POSSourceMenuSalesItemRepository $pOSSourceMenuSalesItemRepo)
    {
        $this->pOSSourceMenuSalesItemRepository = $pOSSourceMenuSalesItemRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/pOSSourceMenuSalesItems",
     *      summary="Get a listing of the POSSourceMenuSalesItems.",
     *      tags={"POSSourceMenuSalesItem"},
     *      description="Get all POSSourceMenuSalesItems",
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
     *                  @SWG\Items(ref="#/definitions/POSSourceMenuSalesItem")
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
        $this->pOSSourceMenuSalesItemRepository->pushCriteria(new RequestCriteria($request));
        $this->pOSSourceMenuSalesItemRepository->pushCriteria(new LimitOffsetCriteria($request));
        $pOSSourceMenuSalesItems = $this->pOSSourceMenuSalesItemRepository->all();

        return $this->sendResponse($pOSSourceMenuSalesItems->toArray(), trans('custom.p_o_s_source_menu_sales_items_retrieved_successful'));
    }

    /**
     * @param CreatePOSSourceMenuSalesItemAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/pOSSourceMenuSalesItems",
     *      summary="Store a newly created POSSourceMenuSalesItem in storage",
     *      tags={"POSSourceMenuSalesItem"},
     *      description="Store POSSourceMenuSalesItem",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="POSSourceMenuSalesItem that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/POSSourceMenuSalesItem")
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
     *                  ref="#/definitions/POSSourceMenuSalesItem"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreatePOSSourceMenuSalesItemAPIRequest $request)
    {
        $input = $request->all();

        $pOSSourceMenuSalesItem = $this->pOSSourceMenuSalesItemRepository->create($input);

        return $this->sendResponse($pOSSourceMenuSalesItem->toArray(), trans('custom.p_o_s_source_menu_sales_item_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/pOSSourceMenuSalesItems/{id}",
     *      summary="Display the specified POSSourceMenuSalesItem",
     *      tags={"POSSourceMenuSalesItem"},
     *      description="Get POSSourceMenuSalesItem",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of POSSourceMenuSalesItem",
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
     *                  ref="#/definitions/POSSourceMenuSalesItem"
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
        /** @var POSSourceMenuSalesItem $pOSSourceMenuSalesItem */
        $pOSSourceMenuSalesItem = $this->pOSSourceMenuSalesItemRepository->findWithoutFail($id);

        if (empty($pOSSourceMenuSalesItem)) {
            return $this->sendError(trans('custom.p_o_s_source_menu_sales_item_not_found'));
        }

        return $this->sendResponse($pOSSourceMenuSalesItem->toArray(), trans('custom.p_o_s_source_menu_sales_item_retrieved_successfull'));
    }

    /**
     * @param int $id
     * @param UpdatePOSSourceMenuSalesItemAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/pOSSourceMenuSalesItems/{id}",
     *      summary="Update the specified POSSourceMenuSalesItem in storage",
     *      tags={"POSSourceMenuSalesItem"},
     *      description="Update POSSourceMenuSalesItem",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of POSSourceMenuSalesItem",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="POSSourceMenuSalesItem that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/POSSourceMenuSalesItem")
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
     *                  ref="#/definitions/POSSourceMenuSalesItem"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdatePOSSourceMenuSalesItemAPIRequest $request)
    {
        $input = $request->all();

        /** @var POSSourceMenuSalesItem $pOSSourceMenuSalesItem */
        $pOSSourceMenuSalesItem = $this->pOSSourceMenuSalesItemRepository->findWithoutFail($id);

        if (empty($pOSSourceMenuSalesItem)) {
            return $this->sendError(trans('custom.p_o_s_source_menu_sales_item_not_found'));
        }

        $pOSSourceMenuSalesItem = $this->pOSSourceMenuSalesItemRepository->update($input, $id);

        return $this->sendResponse($pOSSourceMenuSalesItem->toArray(), trans('custom.possourcemenusalesitem_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/pOSSourceMenuSalesItems/{id}",
     *      summary="Remove the specified POSSourceMenuSalesItem from storage",
     *      tags={"POSSourceMenuSalesItem"},
     *      description="Delete POSSourceMenuSalesItem",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of POSSourceMenuSalesItem",
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
        /** @var POSSourceMenuSalesItem $pOSSourceMenuSalesItem */
        $pOSSourceMenuSalesItem = $this->pOSSourceMenuSalesItemRepository->findWithoutFail($id);

        if (empty($pOSSourceMenuSalesItem)) {
            return $this->sendError(trans('custom.p_o_s_source_menu_sales_item_not_found'));
        }

        $pOSSourceMenuSalesItem->delete();

        return $this->sendSuccess('P O S Source Menu Sales Item deleted successfully');
    }
}
