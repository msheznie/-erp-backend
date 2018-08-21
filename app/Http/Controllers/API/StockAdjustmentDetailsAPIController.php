<?php
/**
 * =============================================
 * -- File Name : StockAdjustmentDetailsAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Stock Adjustment Details
 * -- Author : Mohamed Fayas
 * -- Create date : 20 - August 2018
 * -- Description : This file contains the all CRUD for Stock Adjustment Details
 * -- REVISION HISTORY
 * -- Date: 21 - August 2018 By: Fayas Description: Added new functions named as getItemsByStockAdjustment()
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateStockAdjustmentDetailsAPIRequest;
use App\Http\Requests\API\UpdateStockAdjustmentDetailsAPIRequest;
use App\Models\StockAdjustmentDetails;
use App\Models\Unit;
use App\Repositories\StockAdjustmentDetailsRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class StockAdjustmentDetailsController
 * @package App\Http\Controllers\API
 */

class StockAdjustmentDetailsAPIController extends AppBaseController
{
    /** @var  StockAdjustmentDetailsRepository */
    private $stockAdjustmentDetailsRepository;

    public function __construct(StockAdjustmentDetailsRepository $stockAdjustmentDetailsRepo)
    {
        $this->stockAdjustmentDetailsRepository = $stockAdjustmentDetailsRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/stockAdjustmentDetails",
     *      summary="Get a listing of the StockAdjustmentDetails.",
     *      tags={"StockAdjustmentDetails"},
     *      description="Get all StockAdjustmentDetails",
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
     *                  @SWG\Items(ref="#/definitions/StockAdjustmentDetails")
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
        $this->stockAdjustmentDetailsRepository->pushCriteria(new RequestCriteria($request));
        $this->stockAdjustmentDetailsRepository->pushCriteria(new LimitOffsetCriteria($request));
        $stockAdjustmentDetails = $this->stockAdjustmentDetailsRepository->all();

        return $this->sendResponse($stockAdjustmentDetails->toArray(), 'Stock Adjustment Details retrieved successfully');
    }

    /**
     * @param CreateStockAdjustmentDetailsAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/stockAdjustmentDetails",
     *      summary="Store a newly created StockAdjustmentDetails in storage",
     *      tags={"StockAdjustmentDetails"},
     *      description="Store StockAdjustmentDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="StockAdjustmentDetails that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/StockAdjustmentDetails")
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
     *                  ref="#/definitions/StockAdjustmentDetails"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateStockAdjustmentDetailsAPIRequest $request)
    {
        $input = $request->all();

        $stockAdjustmentDetails = $this->stockAdjustmentDetailsRepository->create($input);

        return $this->sendResponse($stockAdjustmentDetails->toArray(), 'Stock Adjustment Details saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/stockAdjustmentDetails/{id}",
     *      summary="Display the specified StockAdjustmentDetails",
     *      tags={"StockAdjustmentDetails"},
     *      description="Get StockAdjustmentDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of StockAdjustmentDetails",
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
     *                  ref="#/definitions/StockAdjustmentDetails"
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
        /** @var StockAdjustmentDetails $stockAdjustmentDetails */
        $stockAdjustmentDetails = $this->stockAdjustmentDetailsRepository->findWithoutFail($id);

        if (empty($stockAdjustmentDetails)) {
            return $this->sendError('Stock Adjustment Details not found');
        }

        return $this->sendResponse($stockAdjustmentDetails->toArray(), 'Stock Adjustment Details retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateStockAdjustmentDetailsAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/stockAdjustmentDetails/{id}",
     *      summary="Update the specified StockAdjustmentDetails in storage",
     *      tags={"StockAdjustmentDetails"},
     *      description="Update StockAdjustmentDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of StockAdjustmentDetails",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="StockAdjustmentDetails that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/StockAdjustmentDetails")
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
     *                  ref="#/definitions/StockAdjustmentDetails"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateStockAdjustmentDetailsAPIRequest $request)
    {
        $input = $request->all();

        /** @var StockAdjustmentDetails $stockAdjustmentDetails */
        $stockAdjustmentDetails = $this->stockAdjustmentDetailsRepository->findWithoutFail($id);

        if (empty($stockAdjustmentDetails)) {
            return $this->sendError('Stock Adjustment Details not found');
        }

        $stockAdjustmentDetails = $this->stockAdjustmentDetailsRepository->update($input, $id);

        return $this->sendResponse($stockAdjustmentDetails->toArray(), 'StockAdjustmentDetails updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/stockAdjustmentDetails/{id}",
     *      summary="Remove the specified StockAdjustmentDetails from storage",
     *      tags={"StockAdjustmentDetails"},
     *      description="Delete StockAdjustmentDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of StockAdjustmentDetails",
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
        /** @var StockAdjustmentDetails $stockAdjustmentDetails */
        $stockAdjustmentDetails = $this->stockAdjustmentDetailsRepository->findWithoutFail($id);

        if (empty($stockAdjustmentDetails)) {
            return $this->sendError('Stock Adjustment Details not found');
        }

        $stockAdjustmentDetails->delete();

        return $this->sendResponse($id, 'Stock Adjustment Details deleted successfully');
    }

    /**
     * get Items By Stock Adjustment
     * GET|HEAD /getItemsByStockAdjustment
     *
     * @param Request $request
     * @return Response
     */
    public function getItemsByStockAdjustment(Request $request)
    {
        $input = $request->all();
        $id = $input['stockAdjustmentAutoID'];

        $items = StockAdjustmentDetails::where('stockAdjustmentAutoID', $id)
                                    ->with(['uom','local_currency','rpt_currency'])
                                    ->get();

        return $this->sendResponse($items->toArray(), 'Request Details retrieved successfully');
    }
}
