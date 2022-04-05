<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateItemBatchAPIRequest;
use App\Http\Requests\API\UpdateItemBatchAPIRequest;
use App\Models\ItemBatch;
use App\Models\DocumentSubProduct;
use App\Repositories\ItemBatchRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use Carbon\Carbon;

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
        $input = $this->convertArrayToValue($input);
        
        $checkBatchCode = ItemBatch::where('id', '!=', $input['id'])
                                     ->where('batchCode', $input['batchCode'])
                                     ->where('itemSystemCode', $input['itemSystemCode'])
                                     ->first();

        if ($checkBatchCode) {
            return $this->sendError('Batch code cannot be duplicate');
        }

        if (isset($input['batchCode']) && strlen($input['batchCode']) > 20) {
            return $this->sendError('Batch code length cannot greater than 20');
        }

        if (!preg_match('/^[a-zA-Z0-9\-\/]*$/', $input['batchCode'])) {
            return $this->sendError('Batch code can contain only / and - in special character');
        }


        $subProducts = DocumentSubProduct::where('documentDetailID', $input['documentDetailID'])
                                          ->where('documentSystemID', $input['documentSystemID'])
                                          ->where('productBatchID', '!=', $input['id'])
                                          ->sum('quantity');
        
        $newTotalQty = $subProducts + floatval($input['quantity']);

        if ($newTotalQty > $input['noQty']) {
            return $this->sendError('Batch quantity cannot be greater than total quantity');
        }

        if (!is_null($input['expireDate'])) {
            $input['expireDate'] = new Carbon($input['expireDate']);
        }

        /** @var ItemBatch $itemBatch */
        $itemBatch = $this->itemBatchRepository->findWithoutFail($id);

        if (empty($itemBatch)) {
            return $this->sendError('Item Batch not found');
        }


        $subProducts = DocumentSubProduct::where('documentDetailID', $input['documentDetailID'])
                                          ->where('documentSystemID', $input['documentSystemID'])
                                          ->where('productBatchID', $input['id'])
                                          ->update(['quantity' => floatval($input['quantity'])]);

        $itemBatch = $this->itemBatchRepository->update($input, $id);

        return $this->sendResponse($itemBatch->toArray(), 'Item Batch updated successfully');
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

        if ($itemBatch->copiedQty > 0) {
            return $this->sendError('Item Batch cannot be deleted. It has been sold');
        }

        $delteSubProduct = DocumentSubProduct::where('productBatchID', $itemBatch->id)
                                             ->whereNull('productInID')
                                             ->delete();

        $itemBatch->delete();

        return $this->sendResponse([],'Item Batch deleted successfully');
    }
}
