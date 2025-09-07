<?php
/**
 * =============================================
 * -- File Name : LogisticDetailsAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Logistic
 * -- Author : Mohamed Fayas
 * -- Create date : 12 - September 2018
 * -- Description : This file contains the all CRUD for Logistic Details
 * -- REVISION HISTORY
 * -- Date: 14-September 2018 By: Fayas Description: Added new functions named as getItemsByLogistic(),addLogisticDetails(),
 *   getPurchaseOrdersForLogistic(),getGrvDetailsByGrvForLogistic(),getGrvDetailsByGrvForLogistic()
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateLogisticDetailsAPIRequest;
use App\Http\Requests\API\UpdateLogisticDetailsAPIRequest;
use App\Models\GRVMaster;
use App\Models\Logistic;
use App\Models\LogisticDetails;
use App\Models\ProcumentOrder;
use App\Models\PurchaseOrderDetails;
use App\Repositories\LogisticDetailsRepository;
use App\Repositories\LogisticRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class LogisticDetailsController
 * @package App\Http\Controllers\API
 */
class LogisticDetailsAPIController extends AppBaseController
{
    /** @var  LogisticDetailsRepository */
    private $logisticDetailsRepository;
    private $logisticRepository;

    public function __construct(LogisticDetailsRepository $logisticDetailsRepo, LogisticRepository $logisticRepo)
    {
        $this->logisticDetailsRepository = $logisticDetailsRepo;
        $this->logisticRepository = $logisticRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/logisticDetails",
     *      summary="Get a listing of the LogisticDetails.",
     *      tags={"LogisticDetails"},
     *      description="Get all LogisticDetails",
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
     *                  @SWG\Items(ref="#/definitions/LogisticDetails")
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
        $this->logisticDetailsRepository->pushCriteria(new RequestCriteria($request));
        $this->logisticDetailsRepository->pushCriteria(new LimitOffsetCriteria($request));
        $logisticDetails = $this->logisticDetailsRepository->all();

        return $this->sendResponse($logisticDetails->toArray(), trans('custom.logistic_details_retrieved_successfully'));
    }

    /**
     * @param CreateLogisticDetailsAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/logisticDetails",
     *      summary="Store a newly created LogisticDetails in storage",
     *      tags={"LogisticDetails"},
     *      description="Store LogisticDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="LogisticDetails that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/LogisticDetails")
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
     *                  ref="#/definitions/LogisticDetails"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateLogisticDetailsAPIRequest $request)
    {
        $input = $request->all();

        $logisticDetails = $this->logisticDetailsRepository->create($input);

        return $this->sendResponse($logisticDetails->toArray(), trans('custom.logistic_details_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/logisticDetails/{id}",
     *      summary="Display the specified LogisticDetails",
     *      tags={"LogisticDetails"},
     *      description="Get LogisticDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of LogisticDetails",
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
     *                  ref="#/definitions/LogisticDetails"
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
        /** @var LogisticDetails $logisticDetails */
        $logisticDetails = $this->logisticDetailsRepository->findWithoutFail($id);

        if (empty($logisticDetails)) {
            return $this->sendError(trans('custom.logistic_details_not_found'));
        }

        return $this->sendResponse($logisticDetails->toArray(), trans('custom.logistic_details_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateLogisticDetailsAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/logisticDetails/{id}",
     *      summary="Update the specified LogisticDetails in storage",
     *      tags={"LogisticDetails"},
     *      description="Update LogisticDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of LogisticDetails",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="LogisticDetails that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/LogisticDetails")
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
     *                  ref="#/definitions/LogisticDetails"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateLogisticDetailsAPIRequest $request)
    {
        $input = $request->all();

        /** @var LogisticDetails $logisticDetails */
        $logisticDetails = $this->logisticDetailsRepository->findWithoutFail($id);

        if (empty($logisticDetails)) {
            return $this->sendError(trans('custom.logistic_details_not_found'));
        }

        $logisticDetails = $this->logisticDetailsRepository->update(array_only($input, ['itemShippingQty']), $id);

        return $this->sendResponse($logisticDetails->toArray(), trans('custom.logistic_details_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/logisticDetails/{id}",
     *      summary="Remove the specified LogisticDetails from storage",
     *      tags={"LogisticDetails"},
     *      description="Delete LogisticDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of LogisticDetails",
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
        /** @var LogisticDetails $logisticDetails */
        $logisticDetails = $this->logisticDetailsRepository->findWithoutFail($id);

        if (empty($logisticDetails)) {
            return $this->sendError(trans('custom.logistic_details_not_found'));
        }

        $poDetail = PurchaseOrderDetails::find($logisticDetails->POdetailID);
        if (!empty($poDetail)) {
            $poDetail->logisticSelectedYN = 0;
            $poDetail->save();
        }

        $checkAllSelected = PurchaseOrderDetails::where('purchaseOrderMasterID', $logisticDetails->POid)
            ->where('logisticSelectedYN', 1)
            ->count();

        if ($checkAllSelected == 0) {
            $po = ProcumentOrder::find($logisticDetails->POid);
            if (!empty($po)) {
                $po->logisticDoneYN = 0;
                $po->save();
            }
        }


        $logisticDetails->delete();

        return $this->sendResponse($id, trans('custom.logistic_details_deleted_successfully'));
    }

    /**
     * Display a listing of the items by Logistic.
     * GET|HEAD /getItemsByLogistic
     *
     * @param Request $request
     * @return Response
     */
    public function getItemsByLogistic(Request $request)
    {
        $input = $request->all();
        $rId = $input['logisticMasterID'];

        $items = LogisticDetails::where('logisticMasterID', $rId)
            ->with(['uom', 'supplier_by', 'warehouse_by', 'po'])
            ->get();

        return $this->sendResponse($items->toArray(), trans('custom.logistic_details_retrieved_successfully'));
    }

    public function getPurchaseOrdersForLogistic(Request $request)
    {
        $input = $request->all();
        $id = $input['id'];

        $logistic = Logistic::find($id);

        if (empty($logistic)) {
            return $this->sendError(trans('custom.logistic_not_found'));
        }

        $validator = \Validator::make($logistic->toArray(), [
            'supplierID' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        $purchaseOrders = ProcumentOrder::where('companySystemID', $logistic->companySystemID)
            ->whereHas('detail', function ($q) {
                $q->where("logisticSelectedYN", 0)
                    ->where("logisticRecievedYN", 0)
                    ->whereHas('grv_details');
            })
            ->where('supplierID', $logistic->supplierID)
            ->where('poConfirmedYN', 1)
            ->where('poCancelledYN', 0)
            ->where('logisticDoneYN', 0)
            ->where('approved', -1)
            ->get();

        return $this->sendResponse($purchaseOrders->toArray(), trans('custom.purchase_orders_retrieved_successfully'));
    }

    public function getGrvByPOForLogistic(Request $request)
    {
        $input = $request->all();
        $id = $input['id'];

        $purchaseOrder = ProcumentOrder::with('detail')->find($id);

        if (empty($purchaseOrder)) {
            return $this->sendError(trans('custom.purchase_order_not_found'));
        }

        $grvs = GRVMaster::where('companySystemID', $purchaseOrder->companySystemID)
            ->where("approved", -1)
            ->whereHas('details', function ($q) use ($purchaseOrder) {
                $q->whereHas('po_detail', function ($q) {
                    $q->whereHas('order', function ($q) {
                            $q->where('logisticDoneYN', 0);
                         })
                        ->where("logisticSelectedYN", 0)
                        ->where("logisticRecievedYN", 0);
                })
                    ->where('purchaseOrderMastertID', $purchaseOrder->purchaseOrderID);
            })
            ->get();

        return $this->sendResponse($grvs->toArray(), trans('custom.grv_retrieved_successfully_1'));
    }

    public function getGrvDetailsByGrvForLogistic(Request $request)
    {
        $input = $request->all();
        $id = $input['id'];
        $poId = $input['poId'];

        $grv = GRVMaster::with(['details' => function ($q) use ($poId) {
            $q->where('purchaseOrderMastertID', $poId)
                ->whereHas('po_detail', function ($q) {
                    $q->whereHas('order', function ($q) {
                        $q->where('logisticDoneYN', 0);
                    })
                        ->where("logisticSelectedYN", 0)
                        ->where("logisticRecievedYN", 0);
                })
                ->with(['item_by']);
        }])->find($id);

        if (empty($grv)) {
            return $this->sendError(trans('custom.good_receipt_voucher_not_found_1'));
        }

        return $this->sendResponse($grv->details, trans('custom.good_receipt_voucher_details_retrieved_successfull'));
    }

    public function addLogisticDetails(Request $request)
    {
        $input = $request->all();

        $employee = \Helper::getEmployeeInfo();

        $logistic = $this->logisticRepository->findWithoutFail($input['logisticMasterID']);

        if (empty($logistic)) {
            return $this->sendError(trans('custom.logistic_not_found'), 500);
        }

        $finalError = array('same_item' => array());
        $error_count = 0;
        $createArray = array();

        foreach ($input['grvDetails'] as $new) {

            if ($new['isChecked']) {
                $detailExistSameItem = LogisticDetails::where('logisticMasterID', $input['logisticMasterID'])
                    ->where('GRVsystemCode', $new['grvAutoID'])
                    ->where('itemcodeSystem', $new['itemCode'])
                    ->where('POid', $new['purchaseOrderMastertID'])
                    ->where('POdetailID', $new['purchaseOrderDetailsID'])
                    ->count();

                if ($detailExistSameItem > 0) {
                    array_push($finalError['same_item'], $new['itemPrimaryCode']);
                    $error_count++;
                }

                if ($detailExistSameItem == 0) {
                    $item = array();
                    $item['logisticMasterID'] = $input['logisticMasterID'];
                    $item['companySystemID'] = $logistic->companySystemID;
                    $item['companyID'] = $logistic->companyID;
                    $item['supplierID'] = $logistic->supplierID;
                    $item['POid'] = $new['purchaseOrderMastertID'];
                    $item['POdetailID'] = $new['purchaseOrderDetailsID'];
                    $item['itemcodeSystem'] = $new['itemCode'];
                    $item['itemPrimaryCode'] = $new['itemPrimaryCode'];
                    $item['itemDescription'] = $new['itemDescription'];
                    if ($new['item_by']) {
                        $item['partNo'] = $new['item_by']['secondaryItemCode'];
                    } else {
                        $item['partNo'] = '';
                    }
                    $item['itemUOM'] = $new['unitOfMeasure'];
                    $item['itemPOQtry'] = $new['noQty'];
                    $item['itemShippingQty'] = $new['noQty'];
                    $item['POdeliveryWarehousLocation'] = $logistic->agentDeliveryLocationID;
                    $item['GRVsystemCode'] = $new['grvAutoID'];
                    $item['GRVStatus'] = -1;
                    array_push($createArray, $item);
                }
            }
        }


        $confirm_error = array('type' => 'confirm_error', 'data' => $finalError);

        if ($error_count > 0) {
            return $this->sendError("You cannot add this items.", 500, $confirm_error);
        } else {

            if (count($createArray) > 0) {
                foreach ($createArray as $item) {
                    $newRow = $this->logisticDetailsRepository->create($item);
                    if (!empty($newRow)) {

                        $poDetail = PurchaseOrderDetails::find($newRow->POdetailID);
                        if (!empty($poDetail)) {
                            $poDetail->logisticSelectedYN = 1;
                            $poDetail->save();
                        }

                        $checkAllSelected = PurchaseOrderDetails::where('purchaseOrderMasterID', $newRow->POid)
                                                                ->where('logisticSelectedYN', 1)
                                                                ->count();

                        if ($checkAllSelected == 0) {
                            $po = ProcumentOrder::fiind($newRow->POid);
                            if (!empty($po)) {
                                $po->logisticDoneYN = 1;
                                $po->save();
                            }
                        }
                    }

                }
            } else {
                return $this->sendError("Please select the items.", 500);
            }
        }

        return $this->sendResponse($logistic, trans('custom.logistic_details_added_successfully'));

    }

}
