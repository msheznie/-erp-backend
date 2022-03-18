<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateItemBatchAPIRequest;
use App\Http\Requests\API\UpdateItemBatchAPIRequest;
use App\Models\ItemBatch;
use App\Repositories\ItemBatchRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class ItemBatchController
 * @package App\Http\Controllers\API
 */

class ItemBatchAPIController extends AppBaseController
{
    /** @var  ItemBatchRepository */
    private $itemBatchRepository;

    public function __construct(ItemBatchRepository $itemBatchRepo)
    {
        $this->itemBatchRepository = $itemBatchRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/itemBatches",
     *      summary="Get a listing of the ItemBatches.",
     *      tags={"ItemBatch"},
     *      description="Get all ItemBatches",
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
     *                  @SWG\Items(ref="#/definitions/ItemBatch")
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
        $this->itemBatchRepository->pushCriteria(new RequestCriteria($request));
        $this->itemBatchRepository->pushCriteria(new LimitOffsetCriteria($request));
        $itemBatches = $this->itemBatchRepository->all();

        return $this->sendResponse($itemBatches->toArray(), 'Item Batches retrieved successfully');
    }

    /**
     * @param CreateItemBatchAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/itemBatches",
     *      summary="Store a newly created ItemBatch in storage",
     *      tags={"ItemBatch"},
     *      description="Store ItemBatch",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ItemBatch that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ItemBatch")
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
     *                  ref="#/definitions/ItemBatch"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateItemBatchAPIRequest $request)
    {
        $input = $request->all();

        $itemBatch = $this->itemBatchRepository->create($input);

        return $this->sendResponse($itemBatch->toArray(), 'Item Batch saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/itemBatches/{id}",
     *      summary="Display the specified ItemBatch",
     *      tags={"ItemBatch"},
     *      description="Get ItemBatch",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ItemBatch",
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
     *                  ref="#/definitions/ItemBatch"
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
        /** @var ItemBatch $itemBatch */
        $itemBatch = $this->itemBatchRepository->findWithoutFail($id);

        if (empty($itemBatch)) {
            return $this->sendError('Item Batch not found');
        }

        return $this->sendResponse($itemBatch->toArray(), 'Item Batch retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateItemBatchAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/itemBatches/{id}",
     *      summary="Update the specified ItemBatch in storage",
     *      tags={"ItemBatch"},
     *      description="Update ItemBatch",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ItemBatch",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ItemBatch that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ItemBatch")
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
     *                  ref="#/definitions/ItemBatch"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateItemBatchAPIRequest $request)
    {
        $input = $request->all();

        /** @var ItemBatch $itemBatch */
        $itemBatch = $this->itemBatchRepository->findWithoutFail($id);

        if (empty($itemBatch)) {
            return $this->sendError('Item Batch not found');
        }

        $itemBatch = $this->itemBatchRepository->update($input, $id);

        return $this->sendResponse($itemBatch->toArray(), 'ItemBatch updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/itemBatches/{id}",
     *      summary="Remove the specified ItemBatch from storage",
     *      tags={"ItemBatch"},
     *      description="Delete ItemBatch",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ItemBatch",
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
        /** @var ItemBatch $itemBatch */
        $itemBatch = $this->itemBatchRepository->findWithoutFail($id);

        if (empty($itemBatch)) {
            return $this->sendError('Item Batch not found');
        }

        $itemBatch->delete();

        return $this->sendSuccess('Item Batch deleted successfully');
    }
}
