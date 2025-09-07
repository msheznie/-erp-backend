<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateStockCountDetailAPIRequest;
use App\Http\Requests\API\UpdateStockCountDetailAPIRequest;
use App\Models\StockCountDetail;
use App\Models\StockCount;
use App\Models\ItemAssigned;
use App\Repositories\StockCountDetailRepository;
use App\Repositories\StockCountRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Class StockCountDetailController
 * @package App\Http\Controllers\API
 */

class StockCountDetailAPIController extends AppBaseController
{
    /** @var  StockCountDetailRepository */
    private $stockCountDetailRepository;
    private $stockCountRepository;

    public function __construct(StockCountDetailRepository $stockCountDetailRepo, StockCountRepository $stockCountRepo)
    {
        $this->stockCountRepository = $stockCountRepo;
        $this->stockCountDetailRepository = $stockCountDetailRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/stockCountDetails",
     *      summary="Get a listing of the StockCountDetails.",
     *      tags={"StockCountDetail"},
     *      description="Get all StockCountDetails",
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
     *                  @SWG\Items(ref="#/definitions/StockCountDetail")
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
        $this->stockCountDetailRepository->pushCriteria(new RequestCriteria($request));
        $this->stockCountDetailRepository->pushCriteria(new LimitOffsetCriteria($request));
        $stockCountDetails = $this->stockCountDetailRepository->all();

        return $this->sendResponse($stockCountDetails->toArray(), trans('custom.stock_count_details_retrieved_successfully'));
    }

    /**
     * @param CreateStockCountDetailAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/stockCountDetails",
     *      summary="Store a newly created StockCountDetail in storage",
     *      tags={"StockCountDetail"},
     *      description="Store StockCountDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="StockCountDetail that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/StockCountDetail")
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
     *                  ref="#/definitions/StockCountDetail"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateStockCountDetailAPIRequest $request)
    {
        $input = $request->all();

        $stockCount = StockCount::find($input['stockCountAutoID']);

        if (empty($stockCount)) {
            return $this->sendError(trans('custom.stock_count_not_found'));
        }

        $input['location'] = $stockCount->location;

        $items = ItemAssigned::where('companySystemID', $input['companySystemID'])
                            ->where('itemCodeSystem', $input['itemCodeSystem'])
                            ->select(['itemPrimaryCode', 'itemDescription', 'itemCodeSystem', 'secondaryItemCode'])
                            ->get();

        $checkProducts = $this->stockCountRepository->validateProductsForStockCount($input, $items);

        if (count($checkProducts['usedItems']) > 0) {
            return $this->sendError(trans('custom.you_cannot_used_this_item_this_items_has_been_pull'), 500, array('type' => 'used_items', 'used_items' => $checkProducts['usedItems']));
        }

        DB::beginTransaction();
        try {
            $errorMessage = "";
            foreach ($items as $key => $value) {
                $stockCountRes = $this->stockCountDetailRepository->addStockCountItems($value->itemCodeSystem, $stockCount, $input['companySystemID']);
                if (!$stockCountRes['status']) {
                    DB::rollBack();
                    return $this->sendError($stockCountRes['message'], 500);
                } else {
                    if (isset($stockCountRes['message']) && $stockCountRes['message']) {
                        DB::rollBack();
                        return $this->sendError($stockCountRes['message'], 500);
                    }
                }
            }

            DB::commit();
            return $this->sendResponse([], trans('custom.stock_count_detail_saved_successfully'));
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage()." ".$exception->getLine());
        }
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/stockCountDetails/{id}",
     *      summary="Display the specified StockCountDetail",
     *      tags={"StockCountDetail"},
     *      description="Get StockCountDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of StockCountDetail",
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
     *                  ref="#/definitions/StockCountDetail"
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
        /** @var StockCountDetail $stockCountDetail */
        $stockCountDetail = $this->stockCountDetailRepository->findWithoutFail($id);

        if (empty($stockCountDetail)) {
            return $this->sendError(trans('custom.stock_count_detail_not_found'));
        }

        return $this->sendResponse($stockCountDetail->toArray(), trans('custom.stock_count_detail_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateStockCountDetailAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/stockCountDetails/{id}",
     *      summary="Update the specified StockCountDetail in storage",
     *      tags={"StockCountDetail"},
     *      description="Update StockCountDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of StockCountDetail",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="StockCountDetail that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/StockCountDetail")
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
     *                  ref="#/definitions/StockCountDetail"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateStockCountDetailAPIRequest $request)
    {
        $input = $request->all();
        $input = array_except($input, ['uom', 'local_currency', 'rpt_currency']);
        $input = $this->convertArrayToValue($input);

        /** @var StockCountDetail $stockCountDetail */
        $stockCountDetail = $this->stockCountDetailRepository->findWithoutFail($id);

        if (empty($stockCountDetail)) {
            return $this->sendError(trans('custom.stock_count_detail_not_found'));
        }

        $stockCount = StockCount::find($stockCountDetail->stockCountAutoID);

        if (empty($stockCount)) {
            return $this->sendError(trans('custom.stock_count_not_found'));
        }

        $item = ItemAssigned::where('itemCodeSystem', $input['itemCodeSystem'])
            ->where('companySystemID', $stockCount->companySystemID)
            ->first();

        if (empty($item)) {
            return $this->sendError(trans('custom.item_not_found'));
        }

        $companyCurrencyConversion = \Helper::currencyConversion($stockCount->companySystemID,
            $stockCountDetail->currentWacLocalCurrencyID,
            $stockCountDetail->currentWacLocalCurrencyID,
            $input['wacAdjLocal']);

        if (is_null($input['wacAdjLocal'])) {
            $input['wacAdjRpt'] = 0;
            $input['wacAdjLocal'] = 0;
        }else{
            $input['wacAdjRpt'] = $companyCurrencyConversion['reportingAmount'];
        }

        $input['wacAdjRptER'] = $companyCurrencyConversion['trasToRptER'];

        if (is_null($input['noQty'])) {
            $input['noQty'] = 0;
        } else {
            $data = array('companySystemID' => $stockCount->companySystemID,
                        'itemCodeSystem' => $input['itemCodeSystem'],
                        'wareHouseId' => $stockCount->location);

            $itemCurrentCostAndQty = \Inventory::itemCurrentCostAndQty($data);
            $input['systemQty'] = $itemCurrentCostAndQty['currentWareHouseStockQty'];
            $input['adjustedQty'] = $input['noQty'] - $itemCurrentCostAndQty['currentWareHouseStockQty'];

            $input['wacAdjRpt'] = $itemCurrentCostAndQty['wacValueReporting'];
            $input['currentWacRpt'] = $itemCurrentCostAndQty['wacValueReporting'];


            $companyCurrencyConversion = \Helper::currencyConversion($stockCount->companySystemID,$item->wacValueReportingCurrencyID,$item->wacValueReportingCurrencyID,$itemCurrentCostAndQty['wacValueReporting']);

            $input['currentWaclocal'] = $companyCurrencyConversion['localAmount'];
            $input['wacAdjLocal'] = $companyCurrencyConversion['localAmount'];
            $input['wacAdjRptER'] = $companyCurrencyConversion['trasToRptER'];
            $input['wacAdjLocalER'] = 1;
        }

        $input['updatedFlag'] = 1;

        $stockCountDetail = $this->stockCountDetailRepository->update($input, $id);

        return $this->sendResponse($stockCountDetail->toArray(), trans('custom.stock_count_detail_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/stockCountDetails/{id}",
     *      summary="Remove the specified StockCountDetail from storage",
     *      tags={"StockCountDetail"},
     *      description="Delete StockCountDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of StockCountDetail",
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
        /** @var StockCountDetail $stockCountDetail */
        $stockCountDetail = $this->stockCountDetailRepository->findWithoutFail($id);

        if (empty($stockCountDetail)) {
            return $this->sendError(trans('custom.stock_count_detail_not_found'));
        }

        $stockCountDetail->delete();

        return $this->sendResponse($id, trans('custom.stock_count_detail_deleted_successfully'));
    }

     public function getItemsByStockCount(Request $request)
    {
        $input = $request->all();
        $id = $input['stockCountAutoID'];
        $financeSubCategoryID = isset($input['financeSubCategoryID']) ? $input['financeSubCategoryID'] : 0;

        $items = StockCountDetail::where('stockCountAutoID', $id)
                                ->with(['uom', 'local_currency', 'rpt_currency'])
                                ->when($financeSubCategoryID > 0, function($query) use ($financeSubCategoryID) {
                                    $query->where('itemFinanceCategorySubID',$financeSubCategoryID);
                                })
                                ->get();

        return $this->sendResponse($items->toArray(), trans('custom.request_details_retrieved_successfully'));
    }
 

    public function removeAllStockCountItems(Request $request)
    {
        $input = $request->all();
        $id = $input['stockCountAutoID'];
        $financeSubCategoryID = isset($input['financeSubCategoryID']) ? $input['financeSubCategoryID'] : 0;

        $items = StockCountDetail::where('stockCountAutoID', $id)
                                ->with(['uom', 'local_currency', 'rpt_currency'])
                                ->when($financeSubCategoryID > 0, function($query) use ($financeSubCategoryID) {
                                    $query->where('itemFinanceCategorySubID',$financeSubCategoryID);
                                })
                                ->delete();

        return $this->sendResponse([], trans('custom.item_deleted_successfully_1'));
    }
}
