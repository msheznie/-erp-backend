<?php
/**
 * =============================================
 * -- File Name : StockAdjustmentAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Stock Adjustment
 * -- Author : Mohamed Fayas
 * -- Create date : 20 - August 2018
 * -- Description : This file contains the all CRUD for Stock Adjustment
 * -- REVISION HISTORY
 * -- Date: 20 - August 2018 By: Fayas Description: Added new functions named as
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateStockAdjustmentAPIRequest;
use App\Http\Requests\API\UpdateStockAdjustmentAPIRequest;
use App\Models\StockAdjustment;
use App\Repositories\StockAdjustmentRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class StockAdjustmentController
 * @package App\Http\Controllers\API
 */

class StockAdjustmentAPIController extends AppBaseController
{
    /** @var  StockAdjustmentRepository */
    private $stockAdjustmentRepository;

    public function __construct(StockAdjustmentRepository $stockAdjustmentRepo)
    {
        $this->stockAdjustmentRepository = $stockAdjustmentRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/stockAdjustments",
     *      summary="Get a listing of the StockAdjustments.",
     *      tags={"StockAdjustment"},
     *      description="Get all StockAdjustments",
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
     *                  @SWG\Items(ref="#/definitions/StockAdjustment")
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
        $this->stockAdjustmentRepository->pushCriteria(new RequestCriteria($request));
        $this->stockAdjustmentRepository->pushCriteria(new LimitOffsetCriteria($request));
        $stockAdjustments = $this->stockAdjustmentRepository->all();

        return $this->sendResponse($stockAdjustments->toArray(), 'Stock Adjustments retrieved successfully');
    }

    /**
     * @param CreateStockAdjustmentAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/stockAdjustments",
     *      summary="Store a newly created StockAdjustment in storage",
     *      tags={"StockAdjustment"},
     *      description="Store StockAdjustment",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="StockAdjustment that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/StockAdjustment")
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
     *                  ref="#/definitions/StockAdjustment"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateStockAdjustmentAPIRequest $request)
    {
        $input = $request->all();

        $stockAdjustments = $this->stockAdjustmentRepository->create($input);

        return $this->sendResponse($stockAdjustments->toArray(), 'Stock Adjustment saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/stockAdjustments/{id}",
     *      summary="Display the specified StockAdjustment",
     *      tags={"StockAdjustment"},
     *      description="Get StockAdjustment",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of StockAdjustment",
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
     *                  ref="#/definitions/StockAdjustment"
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
        /** @var StockAdjustment $stockAdjustment */
        $stockAdjustment = $this->stockAdjustmentRepository->findWithoutFail($id);

        if (empty($stockAdjustment)) {
            return $this->sendError('Stock Adjustment not found');
        }

        return $this->sendResponse($stockAdjustment->toArray(), 'Stock Adjustment retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateStockAdjustmentAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/stockAdjustments/{id}",
     *      summary="Update the specified StockAdjustment in storage",
     *      tags={"StockAdjustment"},
     *      description="Update StockAdjustment",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of StockAdjustment",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="StockAdjustment that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/StockAdjustment")
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
     *                  ref="#/definitions/StockAdjustment"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateStockAdjustmentAPIRequest $request)
    {
        $input = $request->all();

        /** @var StockAdjustment $stockAdjustment */
        $stockAdjustment = $this->stockAdjustmentRepository->findWithoutFail($id);

        if (empty($stockAdjustment)) {
            return $this->sendError('Stock Adjustment not found');
        }

        $stockAdjustment = $this->stockAdjustmentRepository->update($input, $id);

        return $this->sendResponse($stockAdjustment->toArray(), 'StockAdjustment updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/stockAdjustments/{id}",
     *      summary="Remove the specified StockAdjustment from storage",
     *      tags={"StockAdjustment"},
     *      description="Delete StockAdjustment",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of StockAdjustment",
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
        /** @var StockAdjustment $stockAdjustment */
        $stockAdjustment = $this->stockAdjustmentRepository->findWithoutFail($id);

        if (empty($stockAdjustment)) {
            return $this->sendError('Stock Adjustment not found');
        }

        $stockAdjustment->delete();

        return $this->sendResponse($id, 'Stock Adjustment deleted successfully');
    }
}
