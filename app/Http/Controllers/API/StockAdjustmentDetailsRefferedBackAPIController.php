<?php
/**
 * =============================================
 * -- File Name : StockAdjustmentDetailsRefferedBackAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Stock Adjustment Details Referred Back
 * -- Author : Mohamed Fayas
 * -- Create date : 6 - February 2019
 * -- Description : This file contains the all CRUD for Stock Adjustment Details
 * -- REVISION HISTORY
 * -- Date: 6 - February 2019 By: Fayas Description: Added new functions named as getSADetailsReferBack()
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateStockAdjustmentDetailsRefferedBackAPIRequest;
use App\Http\Requests\API\UpdateStockAdjustmentDetailsRefferedBackAPIRequest;
use App\Models\StockAdjustmentDetailsRefferedBack;
use App\Repositories\StockAdjustmentDetailsRefferedBackRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class StockAdjustmentDetailsRefferedBackController
 * @package App\Http\Controllers\API
 */

class StockAdjustmentDetailsRefferedBackAPIController extends AppBaseController
{
    /** @var  StockAdjustmentDetailsRefferedBackRepository */
    private $stockAdjustmentDetailsRefferedBackRepository;

    public function __construct(StockAdjustmentDetailsRefferedBackRepository $stockAdjustmentDetailsRefferedBackRepo)
    {
        $this->stockAdjustmentDetailsRefferedBackRepository = $stockAdjustmentDetailsRefferedBackRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/stockAdjustmentDetailsRefferedBacks",
     *      summary="Get a listing of the StockAdjustmentDetailsRefferedBacks.",
     *      tags={"StockAdjustmentDetailsRefferedBack"},
     *      description="Get all StockAdjustmentDetailsRefferedBacks",
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
     *                  @SWG\Items(ref="#/definitions/StockAdjustmentDetailsRefferedBack")
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
        $this->stockAdjustmentDetailsRefferedBackRepository->pushCriteria(new RequestCriteria($request));
        $this->stockAdjustmentDetailsRefferedBackRepository->pushCriteria(new LimitOffsetCriteria($request));
        $stockAdjustmentDetailsRefferedBacks = $this->stockAdjustmentDetailsRefferedBackRepository->all();

        return $this->sendResponse($stockAdjustmentDetailsRefferedBacks->toArray(), trans('custom.stock_adjustment_details_reffered_backs_retrieved_'));
    }

    /**
     * @param CreateStockAdjustmentDetailsRefferedBackAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/stockAdjustmentDetailsRefferedBacks",
     *      summary="Store a newly created StockAdjustmentDetailsRefferedBack in storage",
     *      tags={"StockAdjustmentDetailsRefferedBack"},
     *      description="Store StockAdjustmentDetailsRefferedBack",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="StockAdjustmentDetailsRefferedBack that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/StockAdjustmentDetailsRefferedBack")
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
     *                  ref="#/definitions/StockAdjustmentDetailsRefferedBack"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateStockAdjustmentDetailsRefferedBackAPIRequest $request)
    {
        $input = $request->all();

        $stockAdjustmentDetailsRefferedBacks = $this->stockAdjustmentDetailsRefferedBackRepository->create($input);

        return $this->sendResponse($stockAdjustmentDetailsRefferedBacks->toArray(), trans('custom.stock_adjustment_details_reffered_back_saved_succe'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/stockAdjustmentDetailsRefferedBacks/{id}",
     *      summary="Display the specified StockAdjustmentDetailsRefferedBack",
     *      tags={"StockAdjustmentDetailsRefferedBack"},
     *      description="Get StockAdjustmentDetailsRefferedBack",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of StockAdjustmentDetailsRefferedBack",
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
     *                  ref="#/definitions/StockAdjustmentDetailsRefferedBack"
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
        /** @var StockAdjustmentDetailsRefferedBack $stockAdjustmentDetailsRefferedBack */
        $stockAdjustmentDetailsRefferedBack = $this->stockAdjustmentDetailsRefferedBackRepository->findWithoutFail($id);

        if (empty($stockAdjustmentDetailsRefferedBack)) {
            return $this->sendError(trans('custom.stock_adjustment_details_reffered_back_not_found'));
        }

        return $this->sendResponse($stockAdjustmentDetailsRefferedBack->toArray(), trans('custom.stock_adjustment_details_reffered_back_retrieved_s'));
    }

    /**
     * @param int $id
     * @param UpdateStockAdjustmentDetailsRefferedBackAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/stockAdjustmentDetailsRefferedBacks/{id}",
     *      summary="Update the specified StockAdjustmentDetailsRefferedBack in storage",
     *      tags={"StockAdjustmentDetailsRefferedBack"},
     *      description="Update StockAdjustmentDetailsRefferedBack",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of StockAdjustmentDetailsRefferedBack",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="StockAdjustmentDetailsRefferedBack that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/StockAdjustmentDetailsRefferedBack")
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
     *                  ref="#/definitions/StockAdjustmentDetailsRefferedBack"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateStockAdjustmentDetailsRefferedBackAPIRequest $request)
    {
        $input = $request->all();

        /** @var StockAdjustmentDetailsRefferedBack $stockAdjustmentDetailsRefferedBack */
        $stockAdjustmentDetailsRefferedBack = $this->stockAdjustmentDetailsRefferedBackRepository->findWithoutFail($id);

        if (empty($stockAdjustmentDetailsRefferedBack)) {
            return $this->sendError(trans('custom.stock_adjustment_details_reffered_back_not_found'));
        }

        $stockAdjustmentDetailsRefferedBack = $this->stockAdjustmentDetailsRefferedBackRepository->update($input, $id);

        return $this->sendResponse($stockAdjustmentDetailsRefferedBack->toArray(), trans('custom.stockadjustmentdetailsrefferedback_updated_success'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/stockAdjustmentDetailsRefferedBacks/{id}",
     *      summary="Remove the specified StockAdjustmentDetailsRefferedBack from storage",
     *      tags={"StockAdjustmentDetailsRefferedBack"},
     *      description="Delete StockAdjustmentDetailsRefferedBack",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of StockAdjustmentDetailsRefferedBack",
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
        /** @var StockAdjustmentDetailsRefferedBack $stockAdjustmentDetailsRefferedBack */
        $stockAdjustmentDetailsRefferedBack = $this->stockAdjustmentDetailsRefferedBackRepository->findWithoutFail($id);

        if (empty($stockAdjustmentDetailsRefferedBack)) {
            return $this->sendError(trans('custom.stock_adjustment_details_reffered_back_not_found'));
        }

        $stockAdjustmentDetailsRefferedBack->delete();

        return $this->sendResponse($id, trans('custom.stock_adjustment_details_reffered_back_deleted_suc'));
    }

    public function getSADetailsReferBack(Request $request)
    {
        $input = $request->all();
        $id = $input['stockAdjustmentAutoID'];

        $items = StockAdjustmentDetailsRefferedBack::where('stockAdjustmentAutoID', $id)
            ->where('timesReferred',$input['timesReferred'])
            ->with(['uom', 'local_currency', 'rpt_currency'])
            ->get();

        return $this->sendResponse($items->toArray(), trans('custom.request_details_retrieved_successfully'));
    }
}
