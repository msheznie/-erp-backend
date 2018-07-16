<?php
/**
 * =============================================
 * -- File Name : ItemReturnDetailsAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Item Return Details
 * -- Author : Mohamed Fayas
 * -- Create date : 16 - July 2018
 * -- Description : This file contains the all CRUD for Document Attachments
 * -- REVISION HISTORY
 * -- Date: 16 - July 2018 By: Fayas Description: Added new functions named as
 *
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateItemReturnDetailsAPIRequest;
use App\Http\Requests\API\UpdateItemReturnDetailsAPIRequest;
use App\Models\ItemReturnDetails;
use App\Repositories\ItemReturnDetailsRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class ItemReturnDetailsController
 * @package App\Http\Controllers\API
 */

class ItemReturnDetailsAPIController extends AppBaseController
{
    /** @var  ItemReturnDetailsRepository */
    private $itemReturnDetailsRepository;

    public function __construct(ItemReturnDetailsRepository $itemReturnDetailsRepo)
    {
        $this->itemReturnDetailsRepository = $itemReturnDetailsRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/itemReturnDetails",
     *      summary="Get a listing of the ItemReturnDetails.",
     *      tags={"ItemReturnDetails"},
     *      description="Get all ItemReturnDetails",
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
     *                  @SWG\Items(ref="#/definitions/ItemReturnDetails")
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
        $this->itemReturnDetailsRepository->pushCriteria(new RequestCriteria($request));
        $this->itemReturnDetailsRepository->pushCriteria(new LimitOffsetCriteria($request));
        $itemReturnDetails = $this->itemReturnDetailsRepository->all();

        return $this->sendResponse($itemReturnDetails->toArray(), 'Item Return Details retrieved successfully');
    }

    /**
     * @param CreateItemReturnDetailsAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/itemReturnDetails",
     *      summary="Store a newly created ItemReturnDetails in storage",
     *      tags={"ItemReturnDetails"},
     *      description="Store ItemReturnDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ItemReturnDetails that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ItemReturnDetails")
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
     *                  ref="#/definitions/ItemReturnDetails"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateItemReturnDetailsAPIRequest $request)
    {
        $input = $request->all();

        $itemReturnDetails = $this->itemReturnDetailsRepository->create($input);

        return $this->sendResponse($itemReturnDetails->toArray(), 'Item Return Details saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/itemReturnDetails/{id}",
     *      summary="Display the specified ItemReturnDetails",
     *      tags={"ItemReturnDetails"},
     *      description="Get ItemReturnDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ItemReturnDetails",
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
     *                  ref="#/definitions/ItemReturnDetails"
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
        /** @var ItemReturnDetails $itemReturnDetails */
        $itemReturnDetails = $this->itemReturnDetailsRepository->findWithoutFail($id);

        if (empty($itemReturnDetails)) {
            return $this->sendError('Item Return Details not found');
        }

        return $this->sendResponse($itemReturnDetails->toArray(), 'Item Return Details retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateItemReturnDetailsAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/itemReturnDetails/{id}",
     *      summary="Update the specified ItemReturnDetails in storage",
     *      tags={"ItemReturnDetails"},
     *      description="Update ItemReturnDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ItemReturnDetails",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ItemReturnDetails that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ItemReturnDetails")
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
     *                  ref="#/definitions/ItemReturnDetails"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateItemReturnDetailsAPIRequest $request)
    {
        $input = $request->all();

        /** @var ItemReturnDetails $itemReturnDetails */
        $itemReturnDetails = $this->itemReturnDetailsRepository->findWithoutFail($id);

        if (empty($itemReturnDetails)) {
            return $this->sendError('Item Return Details not found');
        }

        $itemReturnDetails = $this->itemReturnDetailsRepository->update($input, $id);

        return $this->sendResponse($itemReturnDetails->toArray(), 'ItemReturnDetails updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/itemReturnDetails/{id}",
     *      summary="Remove the specified ItemReturnDetails from storage",
     *      tags={"ItemReturnDetails"},
     *      description="Delete ItemReturnDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ItemReturnDetails",
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
        /** @var ItemReturnDetails $itemReturnDetails */
        $itemReturnDetails = $this->itemReturnDetailsRepository->findWithoutFail($id);

        if (empty($itemReturnDetails)) {
            return $this->sendError('Item Return Details not found');
        }

        $itemReturnDetails->delete();

        return $this->sendResponse($id, 'Item Return Details deleted successfully');
    }
}
