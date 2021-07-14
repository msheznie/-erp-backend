<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateSegmentAllocatedItemAPIRequest;
use App\Http\Requests\API\UpdateSegmentAllocatedItemAPIRequest;
use App\Models\SegmentAllocatedItem;
use App\Models\PurchaseRequestDetails;
use App\Repositories\SegmentAllocatedItemRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class SegmentAllocatedItemController
 * @package App\Http\Controllers\API
 */

class SegmentAllocatedItemAPIController extends AppBaseController
{
    /** @var  SegmentAllocatedItemRepository */
    private $segmentAllocatedItemRepository;

    public function __construct(SegmentAllocatedItemRepository $segmentAllocatedItemRepo)
    {
        $this->segmentAllocatedItemRepository = $segmentAllocatedItemRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/segmentAllocatedItems",
     *      summary="Get a listing of the SegmentAllocatedItems.",
     *      tags={"SegmentAllocatedItem"},
     *      description="Get all SegmentAllocatedItems",
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
     *                  @SWG\Items(ref="#/definitions/SegmentAllocatedItem")
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
        $this->segmentAllocatedItemRepository->pushCriteria(new RequestCriteria($request));
        $this->segmentAllocatedItemRepository->pushCriteria(new LimitOffsetCriteria($request));
        $segmentAllocatedItems = $this->segmentAllocatedItemRepository->all();

        return $this->sendResponse($segmentAllocatedItems->toArray(), 'Segment Allocated Items retrieved successfully');
    }

    /**
     * @param CreateSegmentAllocatedItemAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/segmentAllocatedItems",
     *      summary="Store a newly created SegmentAllocatedItem in storage",
     *      tags={"SegmentAllocatedItem"},
     *      description="Store SegmentAllocatedItem",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="SegmentAllocatedItem that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/SegmentAllocatedItem")
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
     *                  ref="#/definitions/SegmentAllocatedItem"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateSegmentAllocatedItemAPIRequest $request)
    {
        $input = $request->all();

        $segmentAllocatedItem = $this->segmentAllocatedItemRepository->create($input);

        return $this->sendResponse($segmentAllocatedItem->toArray(), 'Segment Allocated Item saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/segmentAllocatedItems/{id}",
     *      summary="Display the specified SegmentAllocatedItem",
     *      tags={"SegmentAllocatedItem"},
     *      description="Get SegmentAllocatedItem",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SegmentAllocatedItem",
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
     *                  ref="#/definitions/SegmentAllocatedItem"
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
        /** @var SegmentAllocatedItem $segmentAllocatedItem */
        $segmentAllocatedItem = $this->segmentAllocatedItemRepository->findWithoutFail($id);

        if (empty($segmentAllocatedItem)) {
            return $this->sendError('Segment Allocated Item not found');
        }

        return $this->sendResponse($segmentAllocatedItem->toArray(), 'Segment Allocated Item retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateSegmentAllocatedItemAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/segmentAllocatedItems/{id}",
     *      summary="Update the specified SegmentAllocatedItem in storage",
     *      tags={"SegmentAllocatedItem"},
     *      description="Update SegmentAllocatedItem",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SegmentAllocatedItem",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="SegmentAllocatedItem that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/SegmentAllocatedItem")
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
     *                  ref="#/definitions/SegmentAllocatedItem"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateSegmentAllocatedItemAPIRequest $request)
    {
        $input = $request->all();

        /** @var SegmentAllocatedItem $segmentAllocatedItem */
        $segmentAllocatedItem = $this->segmentAllocatedItemRepository->findWithoutFail($id);

        if (empty($segmentAllocatedItem)) {
            return $this->sendError('Segment Allocated Item not found');
        }
        
        $segmentAllocatedItem = $this->segmentAllocatedItemRepository->updateAlllocationValidation($input);
        if (!$segmentAllocatedItem['status']) {
            return $this->sendError($segmentAllocatedItem['message'], 500);
        }

        if (isset($input['segment'])) {
            unset($input['segment']);
        }

        $segmentAllocatedItem = $this->segmentAllocatedItemRepository->update($input, $id);

        if ($input['allocatedQty'] == 0) {
            $segmentAllocatedItem->delete();
        }

        return $this->sendResponse($segmentAllocatedItem->toArray(), 'SegmentAllocatedItem updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/segmentAllocatedItems/{id}",
     *      summary="Remove the specified SegmentAllocatedItem from storage",
     *      tags={"SegmentAllocatedItem"},
     *      description="Delete SegmentAllocatedItem",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SegmentAllocatedItem",
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
        /** @var SegmentAllocatedItem $segmentAllocatedItem */
        $segmentAllocatedItem = $this->segmentAllocatedItemRepository->findWithoutFail($id);

        if (empty($segmentAllocatedItem)) {
            return $this->sendError('Segment Allocated Item not found');
        }

        $segmentAllocatedItem->delete();

        return $this->sendResponse([], 'Segment Allocated Item deleted successfully');
    }

    public function allocateSegmentWiseItem(Request $request)
    {
        $input = $request->all();

        $segmentAllocatedItem = $this->segmentAllocatedItemRepository->allocateSegmentWiseItem($input);

        if (!$segmentAllocatedItem['status']) {
            return $this->sendError($segmentAllocatedItem['message']);
        }
        
        return $this->sendResponse([], 'Segment Allocated Item updated successfully');
    }

    public function getSegmentAllocatedItems(Request $request)
    {
        $input = $request->all();

        $allocatedItems = SegmentAllocatedItem::with(['segment'])
                                             ->where('documentSystemID', $input['documentSystemID'])
                                             ->where('documentMasterAutoID', $input['docAutoID'])
                                             ->where('documentDetailAutoID', $input['docDetailID'])
                                             ->get();
        
        
        return $this->sendResponse($allocatedItems, 'Segment Allocated Item updated successfully');
    }
}
