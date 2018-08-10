<?php
/**
=============================================
-- File Name : InventoryReclassificationDetailAPIController.php
-- Project Name : ERP
-- Module Name :  Inventory
-- Author : Mohamed Mubashir
-- Create date : 10 - August 2018
-- Description : This file contains the all CRUD for Inventory Reclassification Detail
-- REVISION HISTORY
-- Date: 14-March 2018 By: Description: Added new functions named as checkUser(),userCompanies()
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateInventoryReclassificationDetailAPIRequest;
use App\Http\Requests\API\UpdateInventoryReclassificationDetailAPIRequest;
use App\Models\InventoryReclassificationDetail;
use App\Repositories\InventoryReclassificationDetailRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class InventoryReclassificationDetailController
 * @package App\Http\Controllers\API
 */

class InventoryReclassificationDetailAPIController extends AppBaseController
{
    /** @var  InventoryReclassificationDetailRepository */
    private $inventoryReclassificationDetailRepository;

    public function __construct(InventoryReclassificationDetailRepository $inventoryReclassificationDetailRepo)
    {
        $this->inventoryReclassificationDetailRepository = $inventoryReclassificationDetailRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/inventoryReclassificationDetails",
     *      summary="Get a listing of the InventoryReclassificationDetails.",
     *      tags={"InventoryReclassificationDetail"},
     *      description="Get all InventoryReclassificationDetails",
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
     *                  @SWG\Items(ref="#/definitions/InventoryReclassificationDetail")
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
        $this->inventoryReclassificationDetailRepository->pushCriteria(new RequestCriteria($request));
        $this->inventoryReclassificationDetailRepository->pushCriteria(new LimitOffsetCriteria($request));
        $inventoryReclassificationDetails = $this->inventoryReclassificationDetailRepository->all();

        return $this->sendResponse($inventoryReclassificationDetails->toArray(), 'Inventory Reclassification Details retrieved successfully');
    }

    /**
     * @param CreateInventoryReclassificationDetailAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/inventoryReclassificationDetails",
     *      summary="Store a newly created InventoryReclassificationDetail in storage",
     *      tags={"InventoryReclassificationDetail"},
     *      description="Store InventoryReclassificationDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="InventoryReclassificationDetail that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/InventoryReclassificationDetail")
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
     *                  ref="#/definitions/InventoryReclassificationDetail"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateInventoryReclassificationDetailAPIRequest $request)
    {
        $input = $request->all();

        $inventoryReclassificationDetails = $this->inventoryReclassificationDetailRepository->create($input);

        return $this->sendResponse($inventoryReclassificationDetails->toArray(), 'Inventory Reclassification Detail saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/inventoryReclassificationDetails/{id}",
     *      summary="Display the specified InventoryReclassificationDetail",
     *      tags={"InventoryReclassificationDetail"},
     *      description="Get InventoryReclassificationDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of InventoryReclassificationDetail",
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
     *                  ref="#/definitions/InventoryReclassificationDetail"
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
        /** @var InventoryReclassificationDetail $inventoryReclassificationDetail */
        $inventoryReclassificationDetail = $this->inventoryReclassificationDetailRepository->findWithoutFail($id);

        if (empty($inventoryReclassificationDetail)) {
            return $this->sendError('Inventory Reclassification Detail not found');
        }

        return $this->sendResponse($inventoryReclassificationDetail->toArray(), 'Inventory Reclassification Detail retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateInventoryReclassificationDetailAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/inventoryReclassificationDetails/{id}",
     *      summary="Update the specified InventoryReclassificationDetail in storage",
     *      tags={"InventoryReclassificationDetail"},
     *      description="Update InventoryReclassificationDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of InventoryReclassificationDetail",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="InventoryReclassificationDetail that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/InventoryReclassificationDetail")
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
     *                  ref="#/definitions/InventoryReclassificationDetail"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateInventoryReclassificationDetailAPIRequest $request)
    {
        $input = $request->all();

        /** @var InventoryReclassificationDetail $inventoryReclassificationDetail */
        $inventoryReclassificationDetail = $this->inventoryReclassificationDetailRepository->findWithoutFail($id);

        if (empty($inventoryReclassificationDetail)) {
            return $this->sendError('Inventory Reclassification Detail not found');
        }

        $inventoryReclassificationDetail = $this->inventoryReclassificationDetailRepository->update($input, $id);

        return $this->sendResponse($inventoryReclassificationDetail->toArray(), 'InventoryReclassificationDetail updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/inventoryReclassificationDetails/{id}",
     *      summary="Remove the specified InventoryReclassificationDetail from storage",
     *      tags={"InventoryReclassificationDetail"},
     *      description="Delete InventoryReclassificationDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of InventoryReclassificationDetail",
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
        /** @var InventoryReclassificationDetail $inventoryReclassificationDetail */
        $inventoryReclassificationDetail = $this->inventoryReclassificationDetailRepository->findWithoutFail($id);

        if (empty($inventoryReclassificationDetail)) {
            return $this->sendError('Inventory Reclassification Detail not found');
        }

        $inventoryReclassificationDetail->delete();

        return $this->sendResponse($id, 'Inventory Reclassification Detail deleted successfully');
    }
}
