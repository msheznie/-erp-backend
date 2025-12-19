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
 * -- Date: 24-July 2018 By: Fayas Description: Added new functions named as storeReceiveDetailsFromTransfer()
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateStockReceiveDetailsAPIRequest;
use App\Http\Requests\API\UpdateStockReceiveDetailsAPIRequest;
use App\Models\StockReceive;
use App\Models\StockReceiveDetails;
use App\Models\WarehouseMaster;
use App\Models\StockTransfer;
use App\Models\StockTransferDetails;
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

        return $this->sendResponse($stockReceiveDetails->toArray(), trans('custom.stock_receive_details_retrieved_successfully'));
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

        return $this->sendResponse($stockReceiveDetails->toArray(), trans('custom.stock_receive_details_saved_successfully'));
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
            return $this->sendError(trans('custom.stock_receive_details_not_found'));
        }

        return $this->sendResponse($stockReceiveDetails->toArray(), trans('custom.stock_receive_details_retrieved_successfully'));
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

        $input = array_except($request->all(), ['unit_by','item_by']);
        $input = $this->convertArrayToValue($input);
        $qtyError = array('type' => 'qty');
        $stockReceiveDetails = $this->stockReceiveDetailsRepository->findWithoutFail($id);

        if (empty($stockReceiveDetails)) {
            return $this->sendError(trans('custom.stock_receive_details_not_found'));
        }

        if ($stockReceiveDetails->unitCostLocal == 0 || $stockReceiveDetails->unitCostRpt == 0) {
            $input['qty'] = 0;
            $this->stockReceiveDetailsRepository->update($input, $id);
            return $this->sendError("Cost is 0. You cannot issue", 500);
        }

        if ($stockReceiveDetails->unitCostLocal < 0 || $stockReceiveDetails->unitCostRpt < 0) {
            $input['qty'] = 0;
            $this->stockReceiveDetailsRepository->update($input, $id);
            return $this->sendError("Cost is negative. You cannot issue", 500);
        }

        $stdTotalPullSum = StockTransferDetails::where('itemCodeSystem', $stockReceiveDetails->itemCodeSystem)
                                                    ->where('stockTransferAutoID', $stockReceiveDetails->stockTransferAutoID)
                                                    ->groupBy('itemCodeSystem')
                                                    ->sum('qty');

        $stDetail = StockTransferDetails::where('itemCodeSystem', $stockReceiveDetails->itemCodeSystem)
                                        ->where('stockTransferAutoID', $stockReceiveDetails->stockTransferAutoID)
                                        ->first();

        $total = $stdTotalPullSum + $input['qty'] - $stockReceiveDetails->qty;

        if ($total > $stDetail->qty) {
            $input['qty'] = 0;
            $this->stockReceiveDetailsRepository->update($input, $id);
            return $this->sendError("You cannot return more than the issued Qty.", 500,$qtyError);
        }

        $stockReceiveDetails = $this->stockReceiveDetailsRepository->update($input, $id);

        $stdTotalPullCount = StockTransferDetails::where('itemCodeSystem', $stockReceiveDetails->itemCodeSystem)
                                                    ->where('stockTransferAutoID', $stockReceiveDetails->stockTransferAutoID)
                                                    ->groupBy('itemCodeSystem')
                                                    ->sum('qty');

        if (!empty($stDetail)) {
            $stDetail->addedToRecieved = -1;

            if ($stDetail->qty == $stdTotalPullCount) {
                $stDetail->stockRecieved = -1;
            } else {
                $stDetail->stockRecieved = 0;
            }
            $stDetail->save();
        }

        $stMasterCheck = StockTransferDetails::where('stockTransferAutoID', $stockReceiveDetails->stockTransferAutoID)
            ->where('stockRecieved', 0)
            ->count();

        $stockTransfer = StockTransfer::find($stockReceiveDetails->stockTransferAutoID);

        if (!empty($stockTransfer)) {
            if ($stMasterCheck == 0) {
                $stockTransfer->fullyReceived = -1;
            } else {
                $stockTransfer->fullyReceived = 0;
            }
            $stockTransfer->save();
        }

        return $this->sendResponse($stockReceiveDetails->toArray(), trans('custom.stockreceivedetails_updated_successfully'));
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
            return $this->sendError(trans('custom.stock_receive_details_not_found'));
        }

        $stockTransferDetail = StockTransferDetails::where('stockTransferAutoID', $stockReceiveDetails->stockTransferAutoID)
            ->where('itemCodeSystem', $stockReceiveDetails->itemCodeSystem)
            ->first();
        if (!empty($stockTransferDetail)) {

            $stdTotalPullCount = StockTransferDetails::where('stockTransferAutoID', $stockReceiveDetails->stockTransferAutoID)
                ->where('itemCodeSystem', $stockReceiveDetails->itemCodeSystem)
                ->groupBy('itemCodeSystem')
                ->sum('qty');

            $stockTransferDetail->stockRecieved = 0;

            if ($stdTotalPullCount == 0) {
                $stockTransferDetail->addedToRecieved = 0;
            }
            $stockTransferDetail->save();
        }

        $stockTransfer = StockTransfer::where('stockTransferAutoID', $stockReceiveDetails->stockTransferAutoID)->first();

        if (!empty($stockTransfer)) {
            $stockTransfer->fullyReceived = 0;
            $stockTransfer->save();
        }

        $stockReceiveDetails->delete();

        return $this->sendResponse($id, trans('custom.stock_receive_details_deleted_successfully'));
    }

    public function getStockReceiveDetailsByMaster(Request $request)
    {
        $input = $request->all();
        $stockTransferAutoID = $input['stockReceiveAutoID'];

        $items = StockReceiveDetails::select('stockReceiveDetailsID', 'unitCostRpt', 'unitOfMeasure',
                                            'itemCodeSystem', 'itemPrimaryCode', 'itemDescription',
                                            'qty', 'stockTransferCode', 'comments')
                                            ->where('stockReceiveAutoID', $stockTransferAutoID)
                                            ->with(['unit_by','item_by'])
                                            ->get();

        return $this->sendResponse($items->toArray(), trans('custom.stock_receive_details_retrieved_successfully_1'));
    }


    public function storeReceiveDetailsFromTransfer(Request $request)
    {
        $input = $request->all();
        $stockReceiveAutoID = $input['stockReceiveAutoID'];

        $employee = \Helper::getEmployeeInfo();


        foreach ($input['detailTable'] as $newValidation) {
            if ($newValidation['isChecked']) {

                if ($newValidation['rQty'] <= 0) {
                    return $this->sendError("Received Qty required", 500);
                }
                if ($newValidation['rQty'] > $newValidation['qty']) {
                    return $this->sendError("Receive qty cannot be greater than transfer qty", 500);
                }
            }
        }

        $stockReceive = StockReceive::where('stockReceiveAutoID', $stockReceiveAutoID)->first();

        if (empty($stockReceive)) {
            return $this->sendError(trans('custom.stock_receive_not_found'));
        }

        $stockTransfer = StockTransfer::find($input['stockTransferAutoID']);

        if (empty($stockTransfer)) {
            return $this->sendError(trans('custom.stock_transfer_not_found'));
        }

        foreach ($input['detailTable'] as $new) {

            if ($new['isChecked'] && $new['rQty'] > 0) {
                $srDetailExistSameItem = StockReceiveDetails::where('stockReceiveAutoID', $stockReceiveAutoID)
                    ->where('itemCodeSystem', $new['itemCodeSystem'])
                    ->where('stockTransferAutoID', $new['stockTransferAutoID'])
                    ->count();

                if ($srDetailExistSameItem > 0) {
                    return $this->sendError(trans('custom.same_inventory_item_cannot_be_added_more_than_once'),500);
                }

                if ($new['unitCostLocal'] == 0 || $new['unitCostRpt'] == 0) {
                    return $this->sendError("Cost is 0. You cannot issue", 500);
                }

                if ($new['unitCostLocal'] < 0 || $new['unitCostRpt'] < 0) {
                    return $this->sendError("Cost is negative. You cannot issue", 500);
                }
                $item = array();
                $item['stockReceiveAutoID'] = $stockReceiveAutoID;
                $item['stockReceiveCode'] = $stockReceive->stockReceiveCode;
                $item['createdPCID'] = gethostname();
                $item['createdUserID'] = $employee->empID;
                $item['createdUserSystemID'] = $employee->employeeSystemID;

                $item['stockTransferAutoID'] = $stockTransfer->stockTransferAutoID;
                $item['stockTransferCode'] = $stockTransfer->stockTransferCode;
                $item['stockTransferDate'] = $stockTransfer->tranferDate;

                $item['itemCodeSystem'] = $new['itemCodeSystem'];
                $item['itemPrimaryCode'] = $new['itemPrimaryCode'];
                $item['itemDescription'] = $new['itemDescription'];
                $item['unitOfMeasure'] = $new['unitOfMeasure'];
                $item['itemFinanceCategoryID'] = $new['itemFinanceCategoryID'];
                $item['itemFinanceCategorySubID'] = $new['itemFinanceCategorySubID'];
                $item['financeGLcodebBS'] = $new['financeGLcodebBS'];
                $item['financeGLcodebBSSystemID'] = $new['financeGLcodebBSSystemID'];
                $item['localCurrencyID'] = $new['localCurrencyID'];
                $item['unitCostLocal'] = $new['unitCostLocal'];
                $item['reportingCurrencyID'] = $new['reportingCurrencyID'];
                $item['unitCostRpt'] = $new['unitCostRpt'];
                $item['qty'] = $new['qty'];

                $srdItem = $this->stockReceiveDetailsRepository->create($item);

                $stDetail = StockTransferDetails::where('stockTransferDetailsID', $new['stockTransferDetailsID'])->first();
                $stDetail->addedToRecieved = -1;

                $stdTotalPullCount = StockTransferDetails::where('itemCodeSystem', $new['itemCodeSystem'])
                                                            ->where('stockTransferAutoID', $new['stockTransferAutoID'])
                                                            ->groupBy('itemCodeSystem')
                                                            ->sum('qty');

                if ($stDetail->qty == $stdTotalPullCount) {
                    $stDetail->stockRecieved = -1;
                } else {
                    $stDetail->stockRecieved = 0;
                }
                $stDetail->save();
            }
        }

        $stMasterCheck = StockTransferDetails::where('stockTransferAutoID', $input['stockTransferAutoID'])
            ->where('stockRecieved', 0)
            ->count();

        if ($stMasterCheck == 0) {
            $stockTransfer->fullyReceived = -1;
        } else {
            $stockTransfer->fullyReceived = 0;
        }
        $stockTransfer->save();

        return $this->sendResponse('', trans('custom.receive_details_saved_successfully'));
    }
}
