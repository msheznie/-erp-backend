<?php
/**
=============================================
-- File Name : ItemIssueDetailsAPIController.php
-- Project Name : ERP
-- Module Name :  Item Issue Details
-- Author : Mohamed Fayas
-- Create date : 20 - June 2018
-- Description : This file contains the all CRUD for Item Issue Details
-- REVISION HISTORY
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateItemIssueDetailsAPIRequest;
use App\Http\Requests\API\UpdateItemIssueDetailsAPIRequest;
use App\Models\ItemIssueDetails;
use App\Repositories\ItemIssueDetailsRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class ItemIssueDetailsController
 * @package App\Http\Controllers\API
 */

class ItemIssueDetailsAPIController extends AppBaseController
{
    /** @var  ItemIssueDetailsRepository */
    private $itemIssueDetailsRepository;

    public function __construct(ItemIssueDetailsRepository $itemIssueDetailsRepo)
    {
        $this->itemIssueDetailsRepository = $itemIssueDetailsRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/itemIssueDetails",
     *      summary="Get a listing of the ItemIssueDetails.",
     *      tags={"ItemIssueDetails"},
     *      description="Get all ItemIssueDetails",
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
     *                  @SWG\Items(ref="#/definitions/ItemIssueDetails")
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
        $this->itemIssueDetailsRepository->pushCriteria(new RequestCriteria($request));
        $this->itemIssueDetailsRepository->pushCriteria(new LimitOffsetCriteria($request));
        $itemIssueDetails = $this->itemIssueDetailsRepository->all();

        return $this->sendResponse($itemIssueDetails->toArray(), 'Item Issue Details retrieved successfully');
    }

    /**
     * @param CreateItemIssueDetailsAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/itemIssueDetails",
     *      summary="Store a newly created ItemIssueDetails in storage",
     *      tags={"ItemIssueDetails"},
     *      description="Store ItemIssueDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ItemIssueDetails that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ItemIssueDetails")
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
     *                  ref="#/definitions/ItemIssueDetails"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateItemIssueDetailsAPIRequest $request)
    {
        $input = $request->all();

        $itemIssueDetails = $this->itemIssueDetailsRepository->create($input);

        return $this->sendResponse($itemIssueDetails->toArray(), 'Item Issue Details saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/itemIssueDetails/{id}",
     *      summary="Display the specified ItemIssueDetails",
     *      tags={"ItemIssueDetails"},
     *      description="Get ItemIssueDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ItemIssueDetails",
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
     *                  ref="#/definitions/ItemIssueDetails"
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
        /** @var ItemIssueDetails $itemIssueDetails */
        $itemIssueDetails = $this->itemIssueDetailsRepository->findWithoutFail($id);

        if (empty($itemIssueDetails)) {
            return $this->sendError('Item Issue Details not found');
        }

        return $this->sendResponse($itemIssueDetails->toArray(), 'Item Issue Details retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateItemIssueDetailsAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/itemIssueDetails/{id}",
     *      summary="Update the specified ItemIssueDetails in storage",
     *      tags={"ItemIssueDetails"},
     *      description="Update ItemIssueDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ItemIssueDetails",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ItemIssueDetails that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ItemIssueDetails")
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
     *                  ref="#/definitions/ItemIssueDetails"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateItemIssueDetailsAPIRequest $request)
    {
        $input = $request->all();

        /** @var ItemIssueDetails $itemIssueDetails */
        $itemIssueDetails = $this->itemIssueDetailsRepository->findWithoutFail($id);

        if (empty($itemIssueDetails)) {
            return $this->sendError('Item Issue Details not found');
        }

        $itemIssueDetails = $this->itemIssueDetailsRepository->update($input, $id);

        return $this->sendResponse($itemIssueDetails->toArray(), 'ItemIssueDetails updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/itemIssueDetails/{id}",
     *      summary="Remove the specified ItemIssueDetails from storage",
     *      tags={"ItemIssueDetails"},
     *      description="Delete ItemIssueDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ItemIssueDetails",
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
        /** @var ItemIssueDetails $itemIssueDetails */
        $itemIssueDetails = $this->itemIssueDetailsRepository->findWithoutFail($id);

        if (empty($itemIssueDetails)) {
            return $this->sendError('Item Issue Details not found');
        }

        $itemIssueDetails->delete();

        return $this->sendResponse($id, 'Item Issue Details deleted successfully');
    }
}
