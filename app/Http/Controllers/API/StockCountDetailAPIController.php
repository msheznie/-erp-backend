<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateStockCountDetailAPIRequest;
use App\Http\Requests\API\UpdateStockCountDetailAPIRequest;
use App\Models\StockCountDetail;
use App\Models\StockCount;
use App\Repositories\StockCountDetailRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class StockCountDetailController
 * @package App\Http\Controllers\API
 */

class StockCountDetailAPIController extends AppBaseController
{
    /** @var  StockCountDetailRepository */
    private $stockCountDetailRepository;

    public function __construct(StockCountDetailRepository $stockCountDetailRepo)
    {
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

        return $this->sendResponse($stockCountDetails->toArray(), 'Stock Count Details retrieved successfully');
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

        $stockCountDetail = $this->stockCountDetailRepository->create($input);

        return $this->sendResponse($stockCountDetail->toArray(), 'Stock Count Detail saved successfully');
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
            return $this->sendError('Stock Count Detail not found');
        }

        return $this->sendResponse($stockCountDetail->toArray(), 'Stock Count Detail retrieved successfully');
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
            return $this->sendError('Stock Count Detail not found');
        }

        $stockCount = StockCount::find($stockCountDetail->stockCountAutoID);

        if (empty($stockCount)) {
            return $this->sendError('Stock Count not found');
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
            $input['systemQty'] = $itemCurrentCostAndQty['currentStockQty'];
            $input['adjustedQty'] = $input['noQty'] - $itemCurrentCostAndQty['currentStockQty'];
        }

        $input['updatedFlag'] = 1;

        $stockCountDetail = $this->stockCountDetailRepository->update($input, $id);

        return $this->sendResponse($stockCountDetail->toArray(), 'Stock Count Detail updated successfully');
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
            return $this->sendError('Stock Count Detail not found');
        }

        $stockCountDetail->delete();

        return $this->sendResponse($id, 'Stock Count Detail deleted successfully');
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

        return $this->sendResponse($items->toArray(), 'Request Details retrieved successfully');
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

        return $this->sendResponse([], 'Item deleted successfully');
    }
}
