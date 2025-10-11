<?php
/**
 * =============================================
 * -- File Name : StockTransferDetailsRefferedBackAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Stock Transfer Details Referred Back
 * -- Author : Mohamed Fayas
 * -- Create date : 29 - November 2018
 * -- Description : This file contains the all CRUD for Stock Transfer
 * -- REVISION HISTORY
 * -- Date: 29-November 2018 By: Fayas Description: Added new functions named as getStockTransferDetailsReferBack()
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateStockTransferDetailsRefferedBackAPIRequest;
use App\Http\Requests\API\UpdateStockTransferDetailsRefferedBackAPIRequest;
use App\Models\StockTransferDetailsRefferedBack;
use App\Repositories\StockTransferDetailsRefferedBackRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\DB;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class StockTransferDetailsRefferedBackController
 * @package App\Http\Controllers\API
 */

class StockTransferDetailsRefferedBackAPIController extends AppBaseController
{
    /** @var  StockTransferDetailsRefferedBackRepository */
    private $stockTransferDetailsRefferedBackRepository;

    public function __construct(StockTransferDetailsRefferedBackRepository $stockTransferDetailsRefferedBackRepo)
    {
        $this->stockTransferDetailsRefferedBackRepository = $stockTransferDetailsRefferedBackRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/stockTransferDetailsRefferedBacks",
     *      summary="Get a listing of the StockTransferDetailsRefferedBacks.",
     *      tags={"StockTransferDetailsRefferedBack"},
     *      description="Get all StockTransferDetailsRefferedBacks",
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
     *                  @SWG\Items(ref="#/definitions/StockTransferDetailsRefferedBack")
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
        $this->stockTransferDetailsRefferedBackRepository->pushCriteria(new RequestCriteria($request));
        $this->stockTransferDetailsRefferedBackRepository->pushCriteria(new LimitOffsetCriteria($request));
        $stockTransferDetailsRefferedBacks = $this->stockTransferDetailsRefferedBackRepository->all();

        return $this->sendResponse($stockTransferDetailsRefferedBacks->toArray(), trans('custom.stock_transfer_details_reffered_backs_retrieved_su'));
    }

    /**
     * @param CreateStockTransferDetailsRefferedBackAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/stockTransferDetailsRefferedBacks",
     *      summary="Store a newly created StockTransferDetailsRefferedBack in storage",
     *      tags={"StockTransferDetailsRefferedBack"},
     *      description="Store StockTransferDetailsRefferedBack",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="StockTransferDetailsRefferedBack that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/StockTransferDetailsRefferedBack")
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
     *                  ref="#/definitions/StockTransferDetailsRefferedBack"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateStockTransferDetailsRefferedBackAPIRequest $request)
    {
        $input = $request->all();

        $stockTransferDetailsRefferedBacks = $this->stockTransferDetailsRefferedBackRepository->create($input);

        return $this->sendResponse($stockTransferDetailsRefferedBacks->toArray(), trans('custom.stock_transfer_details_reffered_back_saved_success'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/stockTransferDetailsRefferedBacks/{id}",
     *      summary="Display the specified StockTransferDetailsRefferedBack",
     *      tags={"StockTransferDetailsRefferedBack"},
     *      description="Get StockTransferDetailsRefferedBack",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of StockTransferDetailsRefferedBack",
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
     *                  ref="#/definitions/StockTransferDetailsRefferedBack"
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
        /** @var StockTransferDetailsRefferedBack $stockTransferDetailsRefferedBack */
        $stockTransferDetailsRefferedBack = $this->stockTransferDetailsRefferedBackRepository->findWithoutFail($id);

        if (empty($stockTransferDetailsRefferedBack)) {
            return $this->sendError(trans('custom.stock_transfer_details_reffered_back_not_found'));
        }

        return $this->sendResponse($stockTransferDetailsRefferedBack->toArray(), trans('custom.stock_transfer_details_reffered_back_retrieved_suc'));
    }

    /**
     * @param int $id
     * @param UpdateStockTransferDetailsRefferedBackAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/stockTransferDetailsRefferedBacks/{id}",
     *      summary="Update the specified StockTransferDetailsRefferedBack in storage",
     *      tags={"StockTransferDetailsRefferedBack"},
     *      description="Update StockTransferDetailsRefferedBack",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of StockTransferDetailsRefferedBack",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="StockTransferDetailsRefferedBack that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/StockTransferDetailsRefferedBack")
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
     *                  ref="#/definitions/StockTransferDetailsRefferedBack"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateStockTransferDetailsRefferedBackAPIRequest $request)
    {
        $input = $request->all();

        /** @var StockTransferDetailsRefferedBack $stockTransferDetailsRefferedBack */
        $stockTransferDetailsRefferedBack = $this->stockTransferDetailsRefferedBackRepository->findWithoutFail($id);

        if (empty($stockTransferDetailsRefferedBack)) {
            return $this->sendError(trans('custom.stock_transfer_details_reffered_back_not_found'));
        }

        $stockTransferDetailsRefferedBack = $this->stockTransferDetailsRefferedBackRepository->update($input, $id);

        return $this->sendResponse($stockTransferDetailsRefferedBack->toArray(), trans('custom.stocktransferdetailsrefferedback_updated_successfu'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/stockTransferDetailsRefferedBacks/{id}",
     *      summary="Remove the specified StockTransferDetailsRefferedBack from storage",
     *      tags={"StockTransferDetailsRefferedBack"},
     *      description="Delete StockTransferDetailsRefferedBack",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of StockTransferDetailsRefferedBack",
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
        /** @var StockTransferDetailsRefferedBack $stockTransferDetailsRefferedBack */
        $stockTransferDetailsRefferedBack = $this->stockTransferDetailsRefferedBackRepository->findWithoutFail($id);

        if (empty($stockTransferDetailsRefferedBack)) {
            return $this->sendError(trans('custom.stock_transfer_details_reffered_back_not_found'));
        }

        $stockTransferDetailsRefferedBack->delete();

        return $this->sendResponse($id, trans('custom.stock_transfer_details_reffered_back_deleted_succe'));
    }

    public function getStockTransferDetailsReferBack(Request $request)
    {
        $input = $request->all();
        $stockTransferAutoID = $input['stockTransferAutoID'];
        $timesReferred = $input['timesReferred'];

        $items = StockTransferDetailsRefferedBack::select(DB::raw('stockTransferDetailsID,"" as totalCost,unitCostRpt,unitOfMeasure,itemCodeSystem,itemPrimaryCode,itemDescription,qty, currentStockQty,warehouseStockQty'))
            ->where('stockTransferAutoID', $stockTransferAutoID)
            ->where('timesReferred', $timesReferred)
            ->with(['unit_by'])
            ->get();

        return $this->sendResponse($items->toArray(), trans('custom.stock_transfer_details_retrieved_successfully'));
    }

}
