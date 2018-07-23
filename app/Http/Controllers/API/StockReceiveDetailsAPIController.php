<?php
/**
 * =============================================
 * -- File Name : StockReceiveDetailsAPIController.php
 * -- Project Name : ERP
 * -- Module Name : Stock Receive Details
 * -- Author : Mohamed Fayas
 * -- Create date : 23 - July 2018
 * -- Description : This file contains the all CRUD for Stock Receive Details
 * -- REVISION HISTORY
 * -- Date: 23-July 2018 By: Fayas Description: Added new functions named as getStockReceiveDetailsByMaster()
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateStockReceiveDetailsAPIRequest;
use App\Http\Requests\API\UpdateStockReceiveDetailsAPIRequest;
use App\Models\StockReceiveDetails;
use App\Repositories\StockReceiveDetailsRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class StockReceiveDetailsController
 * @package App\Http\Controllers\API
 */

class StockReceiveDetailsAPIController extends AppBaseController
{
    /** @var  StockReceiveDetailsRepository */
    private $stockReceiveDetailsRepository;

    public function __construct(StockReceiveDetailsRepository $stockReceiveDetailsRepo)
    {
        $this->stockReceiveDetailsRepository = $stockReceiveDetailsRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/stockReceiveDetails",
     *      summary="Get a listing of the StockReceiveDetails.",
     *      tags={"StockReceiveDetails"},
     *      description="Get all StockReceiveDetails",
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
     *                  @SWG\Items(ref="#/definitions/StockReceiveDetails")
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
        $this->stockReceiveDetailsRepository->pushCriteria(new RequestCriteria($request));
        $this->stockReceiveDetailsRepository->pushCriteria(new LimitOffsetCriteria($request));
        $stockReceiveDetails = $this->stockReceiveDetailsRepository->all();

        return $this->sendResponse($stockReceiveDetails->toArray(), 'Stock Receive Details retrieved successfully');
    }

    /**
     * @param CreateStockReceiveDetailsAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/stockReceiveDetails",
     *      summary="Store a newly created StockReceiveDetails in storage",
     *      tags={"StockReceiveDetails"},
     *      description="Store StockReceiveDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="StockReceiveDetails that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/StockReceiveDetails")
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
     *                  ref="#/definitions/StockReceiveDetails"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateStockReceiveDetailsAPIRequest $request)
    {
        $input = $request->all();

        $stockReceiveDetails = $this->stockReceiveDetailsRepository->create($input);

        return $this->sendResponse($stockReceiveDetails->toArray(), 'Stock Receive Details saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/stockReceiveDetails/{id}",
     *      summary="Display the specified StockReceiveDetails",
     *      tags={"StockReceiveDetails"},
     *      description="Get StockReceiveDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of StockReceiveDetails",
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
     *                  ref="#/definitions/StockReceiveDetails"
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
        /** @var StockReceiveDetails $stockReceiveDetails */
        $stockReceiveDetails = $this->stockReceiveDetailsRepository->findWithoutFail($id);

        if (empty($stockReceiveDetails)) {
            return $this->sendError('Stock Receive Details not found');
        }

        return $this->sendResponse($stockReceiveDetails->toArray(), 'Stock Receive Details retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateStockReceiveDetailsAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/stockReceiveDetails/{id}",
     *      summary="Update the specified StockReceiveDetails in storage",
     *      tags={"StockReceiveDetails"},
     *      description="Update StockReceiveDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of StockReceiveDetails",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="StockReceiveDetails that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/StockReceiveDetails")
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
     *                  ref="#/definitions/StockReceiveDetails"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateStockReceiveDetailsAPIRequest $request)
    {
        $input = $request->all();

        /** @var StockReceiveDetails $stockReceiveDetails */
        $stockReceiveDetails = $this->stockReceiveDetailsRepository->findWithoutFail($id);

        if (empty($stockReceiveDetails)) {
            return $this->sendError('Stock Receive Details not found');
        }

        $stockReceiveDetails = $this->stockReceiveDetailsRepository->update($input, $id);

        return $this->sendResponse($stockReceiveDetails->toArray(), 'StockReceiveDetails updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/stockReceiveDetails/{id}",
     *      summary="Remove the specified StockReceiveDetails from storage",
     *      tags={"StockReceiveDetails"},
     *      description="Delete StockReceiveDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of StockReceiveDetails",
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
        /** @var StockReceiveDetails $stockReceiveDetails */
        $stockReceiveDetails = $this->stockReceiveDetailsRepository->findWithoutFail($id);

        if (empty($stockReceiveDetails)) {
            return $this->sendError('Stock Receive Details not found');
        }

        $stockReceiveDetails->delete();

        return $this->sendResponse($id, 'Stock Receive Details deleted successfully');
    }

    public function getStockReceiveDetailsByMaster(Request $request)
    {
        $input = $request->all();
        $stockTransferAutoID = $input['stockReceiveAutoID'];

        $items = StockReceiveDetails::select('stockReceiveDetailsID','unitCostRpt','unitOfMeasure',
                                              'itemCodeSystem','itemPrimaryCode','itemDescription',
                                              'qty','stockTransferCode','comments')
                                        ->where('stockReceiveAutoID', $stockTransferAutoID)
                                        ->with(['unit_by'])
                                        ->get();

        return $this->sendResponse($items->toArray(), 'Stock Receive details retrieved successfully');
    }
}
