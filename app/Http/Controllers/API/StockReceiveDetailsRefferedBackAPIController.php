<?php
/**
 * =============================================
 * -- File Name : StockReceiveDetailsRefferedBackAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Stock Receive Details Reffered Back
 * -- Author : Mohamed Fayas
 * -- Create date : 29 - November 2018
 * -- Description : This file contains the all CRUD for Stock Receive Details Reffered Back
 * -- REVISION HISTORY
 * -- Date: 29-November 2018 By: Fayas Description: Added new functions named as getStockReceiveDetailsReferBack()
 */


namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateStockReceiveDetailsRefferedBackAPIRequest;
use App\Http\Requests\API\UpdateStockReceiveDetailsRefferedBackAPIRequest;
use App\Models\StockReceiveDetailsRefferedBack;
use App\Repositories\StockReceiveDetailsRefferedBackRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class StockReceiveDetailsRefferedBackController
 * @package App\Http\Controllers\API
 */

class StockReceiveDetailsRefferedBackAPIController extends AppBaseController
{
    /** @var  StockReceiveDetailsRefferedBackRepository */
    private $stockReceiveDetailsRefferedBackRepository;

    public function __construct(StockReceiveDetailsRefferedBackRepository $stockReceiveDetailsRefferedBackRepo)
    {
        $this->stockReceiveDetailsRefferedBackRepository = $stockReceiveDetailsRefferedBackRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/stockReceiveDetailsRefferedBacks",
     *      summary="Get a listing of the StockReceiveDetailsRefferedBacks.",
     *      tags={"StockReceiveDetailsRefferedBack"},
     *      description="Get all StockReceiveDetailsRefferedBacks",
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
     *                  @SWG\Items(ref="#/definitions/StockReceiveDetailsRefferedBack")
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
        $this->stockReceiveDetailsRefferedBackRepository->pushCriteria(new RequestCriteria($request));
        $this->stockReceiveDetailsRefferedBackRepository->pushCriteria(new LimitOffsetCriteria($request));
        $stockReceiveDetailsRefferedBacks = $this->stockReceiveDetailsRefferedBackRepository->all();

        return $this->sendResponse($stockReceiveDetailsRefferedBacks->toArray(), trans('custom.stock_receive_details_reffered_backs_retrieved_suc'));
    }

    /**
     * @param CreateStockReceiveDetailsRefferedBackAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/stockReceiveDetailsRefferedBacks",
     *      summary="Store a newly created StockReceiveDetailsRefferedBack in storage",
     *      tags={"StockReceiveDetailsRefferedBack"},
     *      description="Store StockReceiveDetailsRefferedBack",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="StockReceiveDetailsRefferedBack that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/StockReceiveDetailsRefferedBack")
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
     *                  ref="#/definitions/StockReceiveDetailsRefferedBack"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateStockReceiveDetailsRefferedBackAPIRequest $request)
    {
        $input = $request->all();

        $stockReceiveDetailsRefferedBacks = $this->stockReceiveDetailsRefferedBackRepository->create($input);

        return $this->sendResponse($stockReceiveDetailsRefferedBacks->toArray(), trans('custom.stock_receive_details_reffered_back_saved_successf'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/stockReceiveDetailsRefferedBacks/{id}",
     *      summary="Display the specified StockReceiveDetailsRefferedBack",
     *      tags={"StockReceiveDetailsRefferedBack"},
     *      description="Get StockReceiveDetailsRefferedBack",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of StockReceiveDetailsRefferedBack",
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
     *                  ref="#/definitions/StockReceiveDetailsRefferedBack"
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
        /** @var StockReceiveDetailsRefferedBack $stockReceiveDetailsRefferedBack */
        $stockReceiveDetailsRefferedBack = $this->stockReceiveDetailsRefferedBackRepository->findWithoutFail($id);

        if (empty($stockReceiveDetailsRefferedBack)) {
            return $this->sendError(trans('custom.stock_receive_details_reffered_back_not_found'));
        }

        return $this->sendResponse($stockReceiveDetailsRefferedBack->toArray(), trans('custom.stock_receive_details_reffered_back_retrieved_succ'));
    }

    /**
     * @param int $id
     * @param UpdateStockReceiveDetailsRefferedBackAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/stockReceiveDetailsRefferedBacks/{id}",
     *      summary="Update the specified StockReceiveDetailsRefferedBack in storage",
     *      tags={"StockReceiveDetailsRefferedBack"},
     *      description="Update StockReceiveDetailsRefferedBack",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of StockReceiveDetailsRefferedBack",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="StockReceiveDetailsRefferedBack that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/StockReceiveDetailsRefferedBack")
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
     *                  ref="#/definitions/StockReceiveDetailsRefferedBack"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateStockReceiveDetailsRefferedBackAPIRequest $request)
    {
        $input = $request->all();

        /** @var StockReceiveDetailsRefferedBack $stockReceiveDetailsRefferedBack */
        $stockReceiveDetailsRefferedBack = $this->stockReceiveDetailsRefferedBackRepository->findWithoutFail($id);

        if (empty($stockReceiveDetailsRefferedBack)) {
            return $this->sendError(trans('custom.stock_receive_details_reffered_back_not_found'));
        }

        $stockReceiveDetailsRefferedBack = $this->stockReceiveDetailsRefferedBackRepository->update($input, $id);

        return $this->sendResponse($stockReceiveDetailsRefferedBack->toArray(), trans('custom.stockreceivedetailsrefferedback_updated_successful'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/stockReceiveDetailsRefferedBacks/{id}",
     *      summary="Remove the specified StockReceiveDetailsRefferedBack from storage",
     *      tags={"StockReceiveDetailsRefferedBack"},
     *      description="Delete StockReceiveDetailsRefferedBack",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of StockReceiveDetailsRefferedBack",
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
        /** @var StockReceiveDetailsRefferedBack $stockReceiveDetailsRefferedBack */
        $stockReceiveDetailsRefferedBack = $this->stockReceiveDetailsRefferedBackRepository->findWithoutFail($id);

        if (empty($stockReceiveDetailsRefferedBack)) {
            return $this->sendError(trans('custom.stock_receive_details_reffered_back_not_found'));
        }

        $stockReceiveDetailsRefferedBack->delete();

        return $this->sendResponse($id, trans('custom.stock_receive_details_reffered_back_deleted_succes'));
    }

    public function getStockReceiveDetailsReferBack(Request $request)
    {
        $input = $request->all();
        $stockTransferAutoID = $input['stockReceiveAutoID'];
        $timesReferred = $input['timesReferred'];

        $items = StockReceiveDetailsRefferedBack::select('stockReceiveDetailsID', 'unitCostRpt', 'unitOfMeasure',
            'itemCodeSystem', 'itemPrimaryCode', 'itemDescription',
            'qty', 'stockTransferCode', 'comments')
            ->where('stockReceiveAutoID', $stockTransferAutoID)
            ->where('timesReferred', $timesReferred)
            ->with(['unit_by'])
            ->get();

        return $this->sendResponse($items->toArray(), trans('custom.stock_receive_details_retrieved_successfully_1'));
    }
}
