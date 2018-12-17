<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateItemMasterRefferedBackAPIRequest;
use App\Http\Requests\API\UpdateItemMasterRefferedBackAPIRequest;
use App\Models\ItemMasterRefferedBack;
use App\Repositories\ItemMasterRefferedBackRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class ItemMasterRefferedBackController
 * @package App\Http\Controllers\API
 */

class ItemMasterRefferedBackAPIController extends AppBaseController
{
    /** @var  ItemMasterRefferedBackRepository */
    private $itemMasterRefferedBackRepository;

    public function __construct(ItemMasterRefferedBackRepository $itemMasterRefferedBackRepo)
    {
        $this->itemMasterRefferedBackRepository = $itemMasterRefferedBackRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/itemMasterRefferedBacks",
     *      summary="Get a listing of the ItemMasterRefferedBacks.",
     *      tags={"ItemMasterRefferedBack"},
     *      description="Get all ItemMasterRefferedBacks",
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
     *                  @SWG\Items(ref="#/definitions/ItemMasterRefferedBack")
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
        $this->itemMasterRefferedBackRepository->pushCriteria(new RequestCriteria($request));
        $this->itemMasterRefferedBackRepository->pushCriteria(new LimitOffsetCriteria($request));
        $itemMasterRefferedBacks = $this->itemMasterRefferedBackRepository->all();

        return $this->sendResponse($itemMasterRefferedBacks->toArray(), 'Item Master Reffered Backs retrieved successfully');
    }

    /**
     * @param CreateItemMasterRefferedBackAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/itemMasterRefferedBacks",
     *      summary="Store a newly created ItemMasterRefferedBack in storage",
     *      tags={"ItemMasterRefferedBack"},
     *      description="Store ItemMasterRefferedBack",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ItemMasterRefferedBack that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ItemMasterRefferedBack")
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
     *                  ref="#/definitions/ItemMasterRefferedBack"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateItemMasterRefferedBackAPIRequest $request)
    {
        $input = $request->all();

        $itemMasterRefferedBacks = $this->itemMasterRefferedBackRepository->create($input);

        return $this->sendResponse($itemMasterRefferedBacks->toArray(), 'Item Master Reffered Back saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/itemMasterRefferedBacks/{id}",
     *      summary="Display the specified ItemMasterRefferedBack",
     *      tags={"ItemMasterRefferedBack"},
     *      description="Get ItemMasterRefferedBack",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ItemMasterRefferedBack",
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
     *                  ref="#/definitions/ItemMasterRefferedBack"
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
        /** @var ItemMasterRefferedBack $itemMasterRefferedBack */
        $itemMasterRefferedBack = $this->itemMasterRefferedBackRepository->findWithoutFail($id);

        if (empty($itemMasterRefferedBack)) {
            return $this->sendError('Item Master Reffered Back not found');
        }

        return $this->sendResponse($itemMasterRefferedBack->toArray(), 'Item Master Reffered Back retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateItemMasterRefferedBackAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/itemMasterRefferedBacks/{id}",
     *      summary="Update the specified ItemMasterRefferedBack in storage",
     *      tags={"ItemMasterRefferedBack"},
     *      description="Update ItemMasterRefferedBack",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ItemMasterRefferedBack",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ItemMasterRefferedBack that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ItemMasterRefferedBack")
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
     *                  ref="#/definitions/ItemMasterRefferedBack"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateItemMasterRefferedBackAPIRequest $request)
    {
        $input = $request->all();

        /** @var ItemMasterRefferedBack $itemMasterRefferedBack */
        $itemMasterRefferedBack = $this->itemMasterRefferedBackRepository->findWithoutFail($id);

        if (empty($itemMasterRefferedBack)) {
            return $this->sendError('Item Master Reffered Back not found');
        }

        $itemMasterRefferedBack = $this->itemMasterRefferedBackRepository->update($input, $id);

        return $this->sendResponse($itemMasterRefferedBack->toArray(), 'ItemMasterRefferedBack updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/itemMasterRefferedBacks/{id}",
     *      summary="Remove the specified ItemMasterRefferedBack from storage",
     *      tags={"ItemMasterRefferedBack"},
     *      description="Delete ItemMasterRefferedBack",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ItemMasterRefferedBack",
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
        /** @var ItemMasterRefferedBack $itemMasterRefferedBack */
        $itemMasterRefferedBack = $this->itemMasterRefferedBackRepository->findWithoutFail($id);

        if (empty($itemMasterRefferedBack)) {
            return $this->sendError('Item Master Reffered Back not found');
        }

        $itemMasterRefferedBack->delete();

        return $this->sendResponse($id, 'Item Master Reffered Back deleted successfully');
    }
}
