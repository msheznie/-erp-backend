<?php
/**
=============================================
-- File Name : ItemIssueTypeAPIController.php
-- Project Name : ERP
-- Module Name :  Item Issue Type
-- Author : Mohamed Fayas
-- Create date : 20 - June 2018
-- Description : This file contains the all CRUD for Item Issue Type
-- REVISION HISTORY
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateItemIssueTypeAPIRequest;
use App\Http\Requests\API\UpdateItemIssueTypeAPIRequest;
use App\Models\ItemIssueType;
use App\Repositories\ItemIssueTypeRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class ItemIssueTypeController
 * @package App\Http\Controllers\API
 */

class ItemIssueTypeAPIController extends AppBaseController
{
    /** @var  ItemIssueTypeRepository */
    private $itemIssueTypeRepository;

    public function __construct(ItemIssueTypeRepository $itemIssueTypeRepo)
    {
        $this->itemIssueTypeRepository = $itemIssueTypeRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/itemIssueTypes",
     *      summary="Get a listing of the ItemIssueTypes.",
     *      tags={"ItemIssueType"},
     *      description="Get all ItemIssueTypes",
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
     *                  @SWG\Items(ref="#/definitions/ItemIssueType")
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
        $this->itemIssueTypeRepository->pushCriteria(new RequestCriteria($request));
        $this->itemIssueTypeRepository->pushCriteria(new LimitOffsetCriteria($request));
        $itemIssueTypes = $this->itemIssueTypeRepository->all();

        return $this->sendResponse($itemIssueTypes->toArray(), trans('custom.item_issue_types_retrieved_successfully'));
    }

    /**
     * @param CreateItemIssueTypeAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/itemIssueTypes",
     *      summary="Store a newly created ItemIssueType in storage",
     *      tags={"ItemIssueType"},
     *      description="Store ItemIssueType",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ItemIssueType that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ItemIssueType")
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
     *                  ref="#/definitions/ItemIssueType"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateItemIssueTypeAPIRequest $request)
    {
        $input = $request->all();

        $itemIssueTypes = $this->itemIssueTypeRepository->create($input);

        return $this->sendResponse($itemIssueTypes->toArray(), trans('custom.item_issue_type_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/itemIssueTypes/{id}",
     *      summary="Display the specified ItemIssueType",
     *      tags={"ItemIssueType"},
     *      description="Get ItemIssueType",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ItemIssueType",
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
     *                  ref="#/definitions/ItemIssueType"
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
        /** @var ItemIssueType $itemIssueType */
        $itemIssueType = $this->itemIssueTypeRepository->findWithoutFail($id);

        if (empty($itemIssueType)) {
            return $this->sendError(trans('custom.item_issue_type_not_found'));
        }

        return $this->sendResponse($itemIssueType->toArray(), trans('custom.item_issue_type_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateItemIssueTypeAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/itemIssueTypes/{id}",
     *      summary="Update the specified ItemIssueType in storage",
     *      tags={"ItemIssueType"},
     *      description="Update ItemIssueType",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ItemIssueType",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ItemIssueType that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ItemIssueType")
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
     *                  ref="#/definitions/ItemIssueType"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateItemIssueTypeAPIRequest $request)
    {
        $input = $request->all();

        /** @var ItemIssueType $itemIssueType */
        $itemIssueType = $this->itemIssueTypeRepository->findWithoutFail($id);

        if (empty($itemIssueType)) {
            return $this->sendError(trans('custom.item_issue_type_not_found'));
        }

        $itemIssueType = $this->itemIssueTypeRepository->update($input, $id);

        return $this->sendResponse($itemIssueType->toArray(), trans('custom.itemissuetype_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/itemIssueTypes/{id}",
     *      summary="Remove the specified ItemIssueType from storage",
     *      tags={"ItemIssueType"},
     *      description="Delete ItemIssueType",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ItemIssueType",
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
        /** @var ItemIssueType $itemIssueType */
        $itemIssueType = $this->itemIssueTypeRepository->findWithoutFail($id);

        if (empty($itemIssueType)) {
            return $this->sendError(trans('custom.item_issue_type_not_found'));
        }

        $itemIssueType->delete();

        return $this->sendResponse($id, trans('custom.item_issue_type_deleted_successfully'));
    }
}
