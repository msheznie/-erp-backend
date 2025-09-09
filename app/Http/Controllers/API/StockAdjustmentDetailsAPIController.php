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
 * -- Date: 21 - August 2018 By: Fayas Description: Added new functions named as getItemsByStockAdjustment(),getItemsOptionsStockAdjustment()
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateStockAdjustmentDetailsAPIRequest;
use App\Http\Requests\API\UpdateStockAdjustmentDetailsAPIRequest;
use App\Models\Company;
use App\Models\CompanyPolicyMaster;
use App\Models\FinanceItemcategorySubAssigned;
use App\Models\ItemAssigned;
use App\Models\SegmentMaster;
use App\Models\StockAdjustment;
use App\Models\StockAdjustmentDetails;
use App\Models\Unit;
use App\Models\WarehouseMaster;
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

        return $this->sendResponse($stockAdjustmentDetails->toArray(), trans('custom.stock_adjustment_details_retrieved_successfully'));
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

        $input = $this->convertArrayToValue($input);

        $companySystemID = $input['companySystemID'];

        $stockAdjustment = StockAdjustment::where('stockAdjustmentAutoID', $input['stockAdjustmentAutoID'])->first();

        if (empty($stockAdjustment)) {
            return $this->sendError(trans('custom.stock_adjustment_not_found'), 500);
        }

        if ($stockAdjustment->location) {
            $wareHouse = WarehouseMaster::where("wareHouseSystemCode", $stockAdjustment->location)->first();
            if (empty($wareHouse)) {
                return $this->sendError(trans('custom.location_not_found'), 500);
            }
            if ($wareHouse->isActive == 0) {
                return $this->sendError('Please select a active location.', 500);
            }
        } else {
            return $this->sendError('Please select a location.', 500);
        }

        if ($stockAdjustment->serviceLineSystemID) {
            $checkDepartmentActive = SegmentMaster::find($stockAdjustment->serviceLineSystemID);
            if (empty($checkDepartmentActive)) {
                return $this->sendError(trans('custom.segment_not_found'));
            }
            if ($checkDepartmentActive->isActive == 0) {
                return $this->sendError('Please select a active Segment', 500);
            }
        } else {
            return $this->sendError('Please select a Segment.', 500);
        }

        $item = ItemAssigned::where('itemCodeSystem', $input['itemCodeSystem'])
            ->where('companySystemID', $companySystemID)
            ->first();

        if (empty($item)) {
            return $this->sendError(trans('custom.item_not_found'));
        }


        $input['stockAdjustmentAutoIDCode'] = $stockAdjustment->stockAdjustmentCode;
        $input['comments'] = null;
        $input['noQty'] = 0;

        $company = Company::where('companySystemID', $companySystemID)->first();

        if (empty($company)) {
            return $this->sendError(trans('custom.company_not_found'));
        }

        $input['itemCodeSystem'] = $item->itemCodeSystem;
        $input['itemPrimaryCode'] = $item->itemPrimaryCode;
        $input['itemDescription'] = $item->itemDescription;
        $input['itemUnitOfMeasure'] = $item->itemUnitOfMeasure;
        $input['partNumber'] = $item->secondaryItemCode;
        $input['itemFinanceCategoryID'] = $item->financeCategoryMaster;
        $input['itemFinanceCategorySubID'] = $item->financeCategorySub;

        $checkWhether = StockAdjustment::where('stockAdjustmentAutoID', '!=', $stockAdjustment->stockAdjustmentAutoID)
            ->where('companySystemID', $companySystemID)
            ->where('location', $stockAdjustment->location)
            ->select([
                'stockAdjustmentAutoID',
                'companySystemID',
                'location',
                'stockAdjustmentCode',
                'approved'
            ])
            ->whereHas('details', function ($query) use ($companySystemID, $input) {
                $query->where('itemCodeSystem', $input['itemCodeSystem']);
            })
            ->where('approved', 0)
            ->first();
        /* approved=0*/

        if (!empty($checkWhether)) {
            return $this->sendError("There is a Stock Adjustment (" . $checkWhether->stockAdjustmentCode . ") pending for approval for the item you are trying to add. Please check again.", 500);
        }

        $data = array('companySystemID' => $companySystemID,
            'itemCodeSystem' => $input['itemCodeSystem'],
            'wareHouseId' => $stockAdjustment->location);

        $input['currentWacLocalCurrencyID'] = $item->wacValueLocalCurrencyID;
        $input['currentWacRptCurrencyID'] = $item->wacValueReportingCurrencyID;

        $itemCurrentCostAndQty = \Inventory::itemCurrentCostAndQty($data);

        if ($stockAdjustment->stockAdjustmentType == 2) {
            $input['currenctStockQty'] = $itemCurrentCostAndQty['currentStockQty'];
        } else {
            $input['currenctStockQty'] = $itemCurrentCostAndQty['currentWareHouseStockQty'];
        }

        $input['wacAdjRpt'] = $itemCurrentCostAndQty['wacValueReporting'];
        $input['currentWacRpt'] = $itemCurrentCostAndQty['wacValueReporting'];
        $companyCurrencyConversion = \Helper::currencyConversion($stockAdjustment->companySystemID,

            $item->wacValueReportingCurrencyID,
            $item->wacValueReportingCurrencyID,
            $itemCurrentCostAndQty['wacValueReporting']);





        $input['currentWaclocal'] = $companyCurrencyConversion['localAmount'];
        $input['wacAdjLocal'] = $companyCurrencyConversion['localAmount'];
        $input['wacAdjRptER'] = $companyCurrencyConversion['trasToRptER'];
        $input['wacAdjLocalER'] = 1;

        $financeItemCategorySubAssigned = FinanceItemcategorySubAssigned::where('companySystemID', $companySystemID)
            ->where('mainItemCategoryID', $input['itemFinanceCategoryID'])
            ->where('itemCategorySubID', $input['itemFinanceCategorySubID'])
            ->first();

        if (!empty($financeItemCategorySubAssigned)) {
            $input['financeGLcodebBS'] = $financeItemCategorySubAssigned->financeGLcodebBS;
            $input['financeGLcodebBSSystemID'] = $financeItemCategorySubAssigned->financeGLcodebBSSystemID;
            $input['financeGLcodePL'] = $financeItemCategorySubAssigned->financeGLcodePL;
            $input['financeGLcodePLSystemID'] = $financeItemCategorySubAssigned->financeGLcodePLSystemID;
            $input['includePLForGRVYN'] = $financeItemCategorySubAssigned->includePLForGRVYN;
        } else {
            return $this->sendError("Account code not updated.", 500);
        }

        if (!$input['financeGLcodebBS'] || !$input['financeGLcodebBSSystemID'] || !$input['financeGLcodePL'] || !$input['financeGLcodePLSystemID']) {
            return $this->sendError("Account code not updated.", 500);
        }

        if ($input['itemFinanceCategoryID'] == 1) {
            $alreadyAdded = StockAdjustment::where('stockAdjustmentAutoID', $input['stockAdjustmentAutoID'])
                ->whereHas('details', function ($query) use ($input) {
                    $query->where('itemCodeSystem', $input['itemCodeSystem']);
                })
                ->first();

            if ($alreadyAdded) {
                return $this->sendError("Selected item is already added. Please check again", 500);
            }
        }

        $stockAdjustmentDetails = $this->stockAdjustmentDetailsRepository->create($input);

        return $this->sendResponse($stockAdjustmentDetails->toArray(), trans('custom.stock_adjustment_details_saved_successfully'));
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
            return $this->sendError(trans('custom.stock_adjustment_details_not_found'));
        }

        return $this->sendResponse($stockAdjustmentDetails->toArray(), trans('custom.stock_adjustment_details_retrieved_successfully'));
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
        $input = array_except($input, ['uom', 'local_currency', 'rpt_currency']);
        $input = $this->convertArrayToValue($input);
        /** @var StockAdjustmentDetails $stockAdjustmentDetails */
        $stockAdjustmentDetails = $this->stockAdjustmentDetailsRepository->findWithoutFail($id);

        if (empty($stockAdjustmentDetails)) {
            return $this->sendError(trans('custom.stock_adjustment_details_not_found'));
        }

        $stockAdjustment = StockAdjustment::find($stockAdjustmentDetails->stockAdjustmentAutoID);

        if (empty($stockAdjustmentDetails)) {
            return $this->sendError(trans('custom.stock_adjustment_not_found'));
        }


        $companyCurrencyConversion = \Helper::currencyConversion($stockAdjustment->companySystemID,
            $stockAdjustmentDetails->currentWacLocalCurrencyID,
            $stockAdjustmentDetails->currentWacLocalCurrencyID,
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
        }

        $data = array('companySystemID' => $stockAdjustment->companySystemID,
            'itemCodeSystem' => $input['itemCodeSystem'],
            'wareHouseId' => $stockAdjustment->location);

        $itemCurrentCostAndQty = \Inventory::itemCurrentCostAndQty($data);

        $currenStockQty = ($stockAdjustment->stockAdjustmentType == 2) ? $itemCurrentCostAndQty['currentStockQty'] : $itemCurrentCostAndQty['currentWareHouseStockQty'];

        $input['currenctStockQty'] = $currenStockQty;

        if ($stockAdjustmentDetails->noQty != $input['noQty']) {
            $balanceQty = $input['currenctStockQty'] + $input['noQty'];

            if ($balanceQty < 0) {
                  if ($currenStockQty != $stockAdjustmentDetails->currenctStockQty) {
                        $stockAdjustmentDetailsRes = $this->stockAdjustmentDetailsRepository->update(['currenctStockQty' => $input['currenctStockQty']], $id);

                        return $this->sendError(trans('custom.current_stock_quantity_has_been_updated_from').$stockAdjustmentDetails->currenctStockQty.' to '.$currenStockQty.'. Adjusted quantity cannot be less than current stock quantity');
                  } else {
                        return $this->sendError(trans('custom.adjusted_quantity_cannot_be_less_than_current_stoc'));
                  }
            } 
        }

        $stockAdjustmentDetails = $this->stockAdjustmentDetailsRepository->update($input, $id);

        return $this->sendResponse($stockAdjustmentDetails->toArray(), trans('custom.stockadjustmentdetails_updated_successfully'));
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
            return $this->sendError(trans('custom.stock_adjustment_details_not_found'));
        }

        $stockAdjustmentDetails->delete();

        return $this->sendResponse($id, trans('custom.stock_adjustment_details_deleted_successfully'));
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
            ->with(['uom', 'local_currency', 'rpt_currency'])
            ->get();

        return $this->sendResponse($items->toArray(), trans('custom.request_details_retrieved_successfully'));
    }

    /**
     * get Items Options Stock Adjustment
     * GET|HEAD /getItemsOptionsStockAdjustment
     *
     * @param Request $request
     * @return Response
     */
    public function getItemsOptionsStockAdjustment(Request $request)
    {
        $input = $request->all();
        $companyId = $input['companyId'];

        $items = ItemAssigned::where('companySystemID', $companyId)
            ->where('isActive', 1)->where('isAssigned', -1)
            ->where('financeCategoryMaster', 1)
            ->select(['itemPrimaryCode', 'itemDescription', 'itemCodeSystem', 'secondaryItemCode']);

        if (array_key_exists('search', $input)) {
            $search = $input['search'];
            $items = $items->where(function ($query) use ($search) {
                $query->where('itemPrimaryCode', 'LIKE', "%{$search}%")
                    ->orWhere('itemDescription', 'LIKE', "%{$search}%")
                    ->orWhere('secondaryItemCode', 'LIKE', "%{$search}%");
            });
        }

        $items = $items->take(20)->get();
        return $this->sendResponse($items->toArray(), trans('custom.data_retrieved_successfully'));
    }
}
