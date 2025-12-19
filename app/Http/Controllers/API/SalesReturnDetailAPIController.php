<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateSalesReturnDetailAPIRequest;
use App\Http\Requests\API\UpdateSalesReturnDetailAPIRequest;
use App\Models\ChartOfAccountsAssigned;
use App\Models\ReasonCodeMaster;
use App\Models\SalesReturnDetail;
use App\Models\DeliveryOrder;
use App\Models\DeliveryOrderDetail;
use App\Models\DocumentSubProduct;
use App\Models\ItemSerial;
use App\Models\CustomerInvoiceDirect;
use App\Models\CustomerInvoiceItemDetails;
use App\Models\SalesReturn;
use App\Models\Company;
use App\Models\Taxdetail;
use App\Repositories\SalesReturnDetailRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use Carbon\Carbon;
use App\helper\inventory;
use App\helper\Helper;
use Illuminate\Support\Facades\DB;
use App\helper\ItemTracking;

/**
 * Class SalesReturnDetailController
 * @package App\Http\Controllers\API
 */

class SalesReturnDetailAPIController extends AppBaseController
{
    /** @var  SalesReturnDetailRepository */
    private $salesReturnDetailRepository;

    public function __construct(SalesReturnDetailRepository $salesReturnDetailRepo)
    {
        $this->salesReturnDetailRepository = $salesReturnDetailRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/salesReturnDetails",
     *      summary="Get a listing of the SalesReturnDetails.",
     *      tags={"SalesReturnDetail"},
     *      description="Get all SalesReturnDetails",
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
     *                  @SWG\Items(ref="#/definitions/SalesReturnDetail")
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
        $this->salesReturnDetailRepository->pushCriteria(new RequestCriteria($request));
        $this->salesReturnDetailRepository->pushCriteria(new LimitOffsetCriteria($request));
        $salesReturnDetails = $this->salesReturnDetailRepository->all();

        return $this->sendResponse($salesReturnDetails->toArray(), trans('custom.sales_return_details_retrieved_successfully'));
    }

    /**
     * @param CreateSalesReturnDetailAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/salesReturnDetails",
     *      summary="Store a newly created SalesReturnDetail in storage",
     *      tags={"SalesReturnDetail"},
     *      description="Store SalesReturnDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="SalesReturnDetail that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/SalesReturnDetail")
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
     *                  ref="#/definitions/SalesReturnDetail"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateSalesReturnDetailAPIRequest $request)
    {
        $input = $request->all();

        $salesReturnDetail = $this->salesReturnDetailRepository->create($input);

        return $this->sendResponse($salesReturnDetail->toArray(), trans('custom.sales_return_detail_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/salesReturnDetails/{id}",
     *      summary="Display the specified SalesReturnDetail",
     *      tags={"SalesReturnDetail"},
     *      description="Get SalesReturnDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SalesReturnDetail",
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
     *                  ref="#/definitions/SalesReturnDetail"
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
        /** @var SalesReturnDetail $salesReturnDetail */
        $salesReturnDetail = $this->salesReturnDetailRepository->findWithoutFail($id);

        if (empty($salesReturnDetail)) {
            return $this->sendError(trans('custom.sales_return_detail_not_found'));
        }

        return $this->sendResponse($salesReturnDetail->toArray(), trans('custom.sales_return_detail_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateSalesReturnDetailAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/salesReturnDetails/{id}",
     *      summary="Update the specified SalesReturnDetail in storage",
     *      tags={"SalesReturnDetail"},
     *      description="Update SalesReturnDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SalesReturnDetail",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="SalesReturnDetail that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/SalesReturnDetail")
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
     *                  ref="#/definitions/SalesReturnDetail"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateSalesReturnDetailAPIRequest $request)
    {
        $input = $request->all();

        /** @var SalesReturnDetail $salesReturnDetail */
        $salesReturnDetail = $this->salesReturnDetailRepository->findWithoutFail($id);

        if (empty($salesReturnDetail)) {
            return $this->sendError(trans('custom.sales_return_detail_not_found'));
        }

        $salesReturnData = SalesReturn::find($input['salesReturnID']);
        if (!$salesReturnData) {
            return $this->sendError(trans('custom.sales_return_data_not_found'));
        }

        $remaingQty = 0;
        $pullItemData = [];
        if ($salesReturnData->returnType == 1) {
            $remainingData = DB::select('SELECT
                                            dodetail.*,
                                            erp_delivery_order.serviceLineSystemID,
                                            "" AS isChecked,
                                            "" AS noQty,
                                            IFNULL(sum(rtnDetails.rtnTakenQty),0) as rtnTakenQty 
                                        FROM
                                            erp_delivery_order_detail dodetail
                                            INNER JOIN erp_delivery_order ON dodetail.deliveryOrderID = erp_delivery_order.deliveryOrderID
                                            LEFT JOIN ( SELECT salesreturndetails.salesReturnDetailID,deliveryOrderDetailID, SUM( qtyReturnedDefaultMeasure ) AS rtnTakenQty FROM salesreturndetails GROUP BY salesReturnDetailID, itemCodeSystem ) AS rtnDetails ON dodetail.deliveryOrderDetailID = rtnDetails.deliveryOrderDetailID 
                                        WHERE
                                            dodetail.deliveryOrderID = ' . $input['deliveryOrderID'] . ' 
                                            AND dodetail.deliveryOrderDetailID = ' . $input['deliveryOrderDetailID'] . ' 
                                            AND fullyReturned != 2
                                            GROUP BY dodetail.deliveryOrderDetailID');

            if (sizeof($remainingData) > 0) {
                $pullItemData = $remainingData[0];
                $remaingQty = floatval($pullItemData->qtyIssuedDefaultMeasure) - floatval($pullItemData->rtnTakenQty) + floatval($salesReturnDetail->qtyReturnedDefaultMeasure);
            }  else {
                $remaingQty = floatval($salesReturnDetail->qtyReturnedDefaultMeasure);
            }
        } else {
            $remainingData = DB::select('SELECT
                                            invDetails.*,
                                            erp_custinvoicedirect.serviceLineSystemID,
                                            "" AS isChecked,
                                            "" AS noQty,
                                            IFNULL(sum(rtnDetails.rtnTakenQty),0) as rtnTakenQty 
                                        FROM
                                            erp_customerinvoiceitemdetails invDetails
                                            INNER JOIN erp_custinvoicedirect ON invDetails.custInvoiceDirectAutoID = erp_custinvoicedirect.custInvoiceDirectAutoID
                                            LEFT JOIN ( SELECT salesreturndetails.salesReturnDetailID,customerItemDetailID, SUM( qtyReturnedDefaultMeasure ) AS rtnTakenQty FROM salesreturndetails GROUP BY salesReturnDetailID, itemCodeSystem ) AS rtnDetails ON invDetails.customerItemDetailID = rtnDetails.customerItemDetailID 
                                        WHERE
                                            invDetails.custInvoiceDirectAutoID = ' . $input['custInvoiceDirectAutoID'] . ' 
                                            AND invDetails.customerItemDetailID = ' . $input['customerItemDetailID'] . ' 
                                            AND fullyReturned != 2
                                            GROUP BY invDetails.customerItemDetailID');

            if (sizeof($remainingData) > 0) {
                $pullItemData = $remainingData[0];
                $remaingQty = floatval($pullItemData->qtyIssuedDefaultMeasure) - floatval($pullItemData->rtnTakenQty) + floatval($salesReturnDetail->qtyReturnedDefaultMeasure);

                
            }  else {
                $remaingQty = floatval($salesReturnDetail->qtyReturnedDefaultMeasure);
            }
        }

        $requestedQty = floatval($input['qtyReturned']);

        if ($remaingQty < $requestedQty) {
            return $this->sendError("Remaining quantity is less than return quantity", 500);
        }

        if ($salesReturnData->returnType == 1) {
            return $this->storeReturnDetailFromSIDO($pullItemData, $input, $salesReturnDetail->qtyReturnedDefaultMeasure);
        } else {
            return $this->storeReturnDetailFromSalesInvoice($pullItemData, $input, $salesReturnDetail->qtyReturnedDefaultMeasure);
        }
    }



    public function storeReturnDetailFromSIDO($pullItemData, $currentItemData, $oldQty)
    {
        $invDetail_arr = array();
        $salesReturnID = $currentItemData['salesReturnID'];
       
        $salesReturn = SalesReturn::where('id', $salesReturnID)->first();

        DB::beginTransaction();
        try {

            //checking the fullyOrdered or partial in delivery order
            $detailSum = SalesReturnDetail::select(DB::raw('COALESCE(SUM(qtyReturnedDefaultMeasure),0) as totalNoQty'))
                                        ->where('deliveryOrderDetailID', $currentItemData['deliveryOrderDetailID'])
                                        ->first();

            $totalAddedQty = (floatval($currentItemData['qtyReturned']) - floatval($oldQty)) + $detailSum['totalNoQty'];

            $deliveryOrderDetailData = DeliveryOrderDetail::find($currentItemData['deliveryOrderDetailID']);

            $remaingQty = $deliveryOrderDetailData->qtyIssuedDefaultMeasure; 
            if ($remaingQty == $totalAddedQty) {
                $fullyReturned = 2;
                $closedYN = -1;
                $selectedForSalesReturn= -1;
            } else {
                $fullyReturned = 1;
                $closedYN = 0;
                $selectedForSalesReturn = 0;
            }

            // checking the qty request is matching with sum total
            if ($remaingQty >= $currentItemData['qtyReturned']) {

                // $invDetail_arr['doInvRemainingQty'] = floatval($new['qtyIssuedDefaultMeasure']) - floatval($new['rtnTakenQty']);

                $invDetail_arr['qtyReturned'] = $currentItemData['qtyReturned'];
                $currentItemData['reasonCode'] = isset($currentItemData['reasonCode'][0]) ? $currentItemData['reasonCode'][0] : $currentItemData['reasonCode'];
                $invDetail_arr['reasonCode'] = $currentItemData['reasonCode'];
                $reasonCode = ReasonCodeMaster::find($currentItemData['reasonCode']);
                if($reasonCode){
                    $invDetail_arr['isPostItemLedger'] = $reasonCode->isPost;
                    if($reasonCode->isPost == 0){
                        $chartOfAccountAssigned = ChartOfAccountsAssigned::where('chartOfAccountSystemID',$reasonCode->glCode)->where('companySystemID',$currentItemData['companySystemID'])->where('isActive', 1)->where('isAssigned', -1)->first();
                        if($chartOfAccountAssigned){
                            $invDetail_arr['reasonGLCode'] = $reasonCode->glCode;
                        }
                        else{
                            return $this->sendError('Reason Code Master GL Code is not assigned to the company');
                        }
                    } else {
                        $invDetail_arr['reasonGLCode'] = null;
                    }
                }
                else{
                    $invDetail_arr['reasonGLCode'] = null;
                }

                $invDetail_arr['qtyReturnedDefaultMeasure'] = $currentItemData['qtyReturned'];

                $totalNetcost = ($currentItemData['unitTransactionAmount'] - $currentItemData['discountAmount']) * $currentItemData['qtyReturned'];

                $invDetail_arr['transactionAmount'] = \Helper::roundValue($totalNetcost);
                
                $item = SalesReturnDetail::where('salesReturnDetailID', $currentItemData['salesReturnDetailID'])->update($invDetail_arr);

                $update = DeliveryOrderDetail::where('deliveryOrderDetailID', $currentItemData['deliveryOrderDetailID'])
                                             ->update(['fullyReturned' => $fullyReturned, 'returnQty' => $totalAddedQty]);
            }

            // fetching the total count records from purchase Request Details table
            $doDetailTotalcount = DeliveryOrderDetail::select(DB::raw('count(deliveryOrderDetailID) as detailCount'))
                                                    ->where('deliveryOrderID', $currentItemData['deliveryOrderID'])
                                                    ->first();

            // fetching the total count records from purchase Request Details table where fullyOrdered = 2
            $doDetailExist = DeliveryOrderDetail::select(DB::raw('count(deliveryOrderDetailID) as count'))
                                                ->where('deliveryOrderID', $currentItemData['deliveryOrderID'])
                                                ->where('fullyReturned', 2)
                                                ->first();

            // Updating PR Master Table After All Detail Table records updated
            if ($doDetailTotalcount['detailCount'] == $doDetailExist['count']) {
                $updatedo = DeliveryOrder::find($currentItemData['deliveryOrderID'])
                                        ->update(['selectedForSalesReturn' => -1, 'closedYN' => -1]);
            }
       
            //check all details fullyOrdered in DO Master
            $doMasterfullyOrdered = DeliveryOrderDetail::where('deliveryOrderID', $currentItemData['deliveryOrderID'])
                                                        ->whereIn('fullyReturned', [1, 0])
                                                        ->get()->toArray();

            if (empty($doMasterfullyOrdered)) {
                DeliveryOrder::find($currentItemData['deliveryOrderID'])
                    ->update([
                        'selectedForSalesReturn' => -1,
                        'closedYN' => -1,
                    ]);
            } else {
                DeliveryOrder::find($currentItemData['deliveryOrderID'])
                    ->update([
                        'selectedForSalesReturn' => 0,
                        'closedYN' => 0,
                    ]);
            }

            $this->updateDOReturnedStatus($currentItemData['deliveryOrderID']);

            $resVat = $this->updateVatOfSalesReturn($salesReturnID);
            if (!$resVat['status']) {
               return $this->sendError($resVat['message']); 
            } 
            DB::commit();
            return $this->sendResponse([], trans('custom.sales_return_item_details_updated_successfully'));
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError(trans('custom.error_occurred'). $exception->getMessage() . 'Line :' . $exception->getLine());
        }
        
    }


    public function storeReturnDetailFromSalesInvoice($pullItemData, $currentItemData, $oldQty)
    {
        $invDetail_arr = array();
        $salesReturnID = $currentItemData['salesReturnID'];

        $salesReturn = SalesReturn::where('id', $salesReturnID)->first();

        DB::beginTransaction();
        try {
            $customerInvoice = CustomerInvoiceDirect::find($currentItemData['custInvoiceDirectAutoID']);

            //checking the fullyOrdered or partial in delivery order
            $detailSum = SalesReturnDetail::select(DB::raw('COALESCE(SUM(qtyReturnedDefaultMeasure),0) as totalNoQty'))
                                        ->where('customerItemDetailID', $currentItemData['customerItemDetailID'])
                                        ->first();

            $totalAddedQty = (floatval($currentItemData['qtyReturned']) - floatval($oldQty)) + $detailSum['totalNoQty'];

            $customerInvDetailData = CustomerInvoiceItemDetails::find($currentItemData['customerItemDetailID']);

            $remaingQty = $customerInvDetailData->qtyIssuedDefaultMeasure; 

            if ($remaingQty == $totalAddedQty) {
                $fullyReturned = 2;
                $closedYN = -1;
                $selectedForSalesReturn= -1;
            } else {
                $fullyReturned = 1;
                $closedYN = 0;
                $selectedForSalesReturn = 0;
            }

            // checking the qty request is matching with sum total
            if ($remaingQty >= $currentItemData['qtyReturned']) {

                // $invDetail_arr['doInvRemainingQty'] = floatval($new['qtyIssuedDefaultMeasure']) - floatval($new['rtnTakenQty']);
                $invDetail_arr['qtyReturned'] = $currentItemData['qtyReturned'];
                $currentItemData['reasonCode'] = isset($currentItemData['reasonCode'][0]) ? $currentItemData['reasonCode'][0] : $currentItemData['reasonCode'];
                $invDetail_arr['reasonCode'] = $currentItemData['reasonCode'];
                $reasonCode = ReasonCodeMaster::find($currentItemData['reasonCode']);
                if($reasonCode){
                    $invDetail_arr['isPostItemLedger'] = $reasonCode->isPost;
                    if($reasonCode->isPost == 0){
                        $chartOfAccountAssigned = ChartOfAccountsAssigned::where('chartOfAccountSystemID',$reasonCode->glCode)->where('companySystemID',$currentItemData['companySystemID'])->where('isActive', 1)->where('isAssigned', -1)->first();
                        if($chartOfAccountAssigned){
                            $invDetail_arr['reasonGLCode'] = $reasonCode->glCode;
                        }
                        else{
                            return $this->sendError('Reason Code Master GL Code is not assigned to the company');
                        }

                    } else {
                        $invDetail_arr['reasonGLCode'] = null;
                    }
                }
                else {
                    $invDetail_arr['reasonGLCode'] = null;
                }
                $invDetail_arr['qtyReturnedDefaultMeasure'] = $currentItemData['qtyReturned'];

                $totalNetcost = $currentItemData['unitTransactionAmount'] * $currentItemData['qtyReturned'];

                $invDetail_arr['transactionAmount'] = \Helper::roundValue($totalNetcost);
                
                $item = SalesReturnDetail::where('salesReturnDetailID', $currentItemData['salesReturnDetailID'])->update($invDetail_arr);

                $update = CustomerInvoiceItemDetails::where('customerItemDetailID', $currentItemData['customerItemDetailID'])
                                                    ->update(['fullyReturned' => $fullyReturned, 'returnQty' => $totalAddedQty]);
            }

            $doDetailTotalcount = CustomerInvoiceItemDetails::select(DB::raw('count(customerItemDetailID) as detailCount'))
                                                            ->where('custInvoiceDirectAutoID', $currentItemData['custInvoiceDirectAutoID'])
                                                            ->first();

            // fetching the total count records from purchase Request Details table where fullyOrdered = 2
            $doDetailExist = CustomerInvoiceItemDetails::select(DB::raw('count(deliveryOrderDetailID) as count'))
                                                ->where('custInvoiceDirectAutoID', $currentItemData['custInvoiceDirectAutoID'])
                                                ->where('fullyReturned', 2)
                                                ->first();

            // Updating PR Master Table After All Detail Table records updated
            if ($doDetailTotalcount['detailCount'] == $doDetailExist['count']) {
                $updatedo = CustomerInvoiceDirect::find($currentItemData['custInvoiceDirectAutoID'])
                                        ->update(['selectedForSalesReturn' => -1, 'closedYN' => -1]);
            }
            //check all details fullyOrdered in DO Master
            $doMasterfullyOrdered = CustomerInvoiceItemDetails::where('custInvoiceDirectAutoID', $currentItemData['custInvoiceDirectAutoID'])
                                                        ->whereIn('fullyReturned', [1, 0])
                                                        ->get()->toArray();

            if (empty($doMasterfullyOrdered)) {
                CustomerInvoiceDirect::find($currentItemData['custInvoiceDirectAutoID'])
                    ->update([
                        'selectedForSalesReturn' => -1,
                        'closedYN' => -1,
                    ]);
            } else {
                CustomerInvoiceDirect::find($currentItemData['custInvoiceDirectAutoID'])
                    ->update([
                        'selectedForSalesReturn' => 0,
                        'closedYN' => 0,
                    ]);
            }

            $this->updateInvoiceReturnedStatus($currentItemData['custInvoiceDirectAutoID']);

            $resVat = $this->updateVatOfSalesReturn($salesReturnID);
            if (!$resVat['status']) {
               return $this->sendError($resVat['message']); 
            } 

            DB::commit();
            return $this->sendResponse([], trans('custom.sales_return_item_details_updated_successfully'));
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError(trans('custom.error_occurred'). $exception->getMessage() . 'Line :' . $exception->getLine());
        }
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/salesReturnDetails/{id}",
     *      summary="Remove the specified SalesReturnDetail from storage",
     *      tags={"SalesReturnDetail"},
     *      description="Delete SalesReturnDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SalesReturnDetail",
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
        /** @var SalesReturnDetail $salesReturnDetail */
        $salesReturnDetail = $this->salesReturnDetailRepository->findWithoutFail($id);

        if (empty($salesReturnDetail)) {
            return $this->sendError(trans('custom.sales_return_detail_not_found'));
        }

        $salesReturn = SalesReturn::find($salesReturnDetail->salesReturnID);

        if (!$salesReturn) {
            return $this->sendError(trans('custom.sales_return_not_found'));
        }

        if ($salesReturnDetail->trackingType == 2) {
            $validateSubProductSold = DocumentSubProduct::where('documentSystemID', $salesReturn->documentSystemID)
                                                         ->where('documentDetailID', $id)
                                                         ->where('sold', 1)
                                                         ->first();

            if ($validateSubProductSold) {
                return $this->sendError(trans('custom.you_cannot_delete_this_line_item_serial_details_ar'), 422);
            }

            $subProduct = DocumentSubProduct::where('documentSystemID', $salesReturn->documentSystemID)
                                             ->where('documentDetailID', $id);

            $productInIDs = ($subProduct->count() > 0) ? $subProduct->get()->pluck('productInID')->toArray() : [];
            $serialIds = ($subProduct->count() > 0) ? $subProduct->get()->pluck('productSerialID')->toArray() : [];

            if (count($productInIDs) > 0) {
                $updateSerial = ItemSerial::whereIn('id', $serialIds)
                                          ->update(['soldFlag' => 0]);

                $updateSerial = DocumentSubProduct::whereIn('id', $productInIDs)
                                          ->update(['sold' => 0, 'soldQty' => 0]);

                $subProduct->delete();
            }
        } else if ($salesReturnDetail->trackingType == 1) {
            $deleteBatch = ItemTracking::revertBatchTrackingReturnStatus($salesReturn->documentSystemID, $id);

            if (!$deleteBatch['status']) {
                return $this->sendError($deleteBatch['message'], 422);
            }
        }

        $salesReturnDetail->delete();


        if ($salesReturn->returnType == 1) {
            if (!empty($salesReturnDetail->deliveryOrderDetailID) && !empty($salesReturnDetail->deliveryOrderID)) {
                DeliveryOrder::find($salesReturnDetail->deliveryOrderID)
                    ->update([
                        'selectedForSalesReturn' => 0,
                        'closedYN' => 0
                    ]);


                //checking the fullyOrdered or partial in po
                $detailSum = SalesReturnDetail::select(DB::raw('COALESCE(SUM(qtyReturnedDefaultMeasure),0) as totalQty'))
                    ->where('deliveryOrderDetailID', $salesReturnDetail->deliveryOrderDetailID)
                    ->first();

                $updatedQuoQty = $detailSum['totalQty'];

                if ($updatedQuoQty == 0) {
                    $fullyReturned = 0;
                } else {
                    $fullyReturned = 1;
                }

                $updateDetail = DeliveryOrderDetail::where('deliveryOrderDetailID', $salesReturnDetail->deliveryOrderDetailID)
                    ->update([ 'fullyReturned' => $fullyReturned, 'returnQty' => $updatedQuoQty]);
            }
            $this->updateDOReturnedStatus($salesReturnDetail->deliveryOrderID);
        } else {
            if (!empty($salesReturnDetail->customerItemDetailID) && !empty($salesReturnDetail->custInvoiceDirectAutoID)) {
                CustomerInvoiceDirect::find($salesReturnDetail->custInvoiceDirectAutoID)
                    ->update([
                        'selectedForSalesReturn' => 0,
                        'closedYN' => 0
                    ]);


                //checking the fullyOrdered or partial in po
                $detailSum = SalesReturnDetail::select(DB::raw('COALESCE(SUM(qtyReturnedDefaultMeasure),0) as totalQty'))
                    ->where('customerItemDetailID', $salesReturnDetail->customerItemDetailID)
                    ->first();

                $updatedQuoQty = $detailSum['totalQty'];

                if ($updatedQuoQty == 0) {
                    $fullyReturned = 0;
                } else {
                    $fullyReturned = 1;
                }

                $updateDetail = CustomerInvoiceItemDetails::where('customerItemDetailID', $salesReturnDetail->customerItemDetailID)
                    ->update([ 'fullyReturned' => $fullyReturned, 'returnQty' => $updatedQuoQty]);
            }
            $this->updateInvoiceReturnedStatus($salesReturnDetail->custInvoiceDirectAutoID);
        }

        $resVat = $this->updateVatOfSalesReturn($salesReturnDetail->salesReturnID);
        if (!$resVat['status']) {
           return $this->sendError($resVat['message']); 
        } 

        return $this->sendResponse([], trans('custom.sales_return_detail_deleted_successfully'));
    }


    public function updateVatOfSalesReturn($salesReturnID)
    {
        $salesReturnMasterData = SalesReturn::find($salesReturnID);

        $totalAmount = 0;
        $totalTaxAmount = 0;
        if ($salesReturnMasterData->returnType == 1) {
            $invoiceDetails = SalesReturnDetail::where('salesReturnID', $salesReturnID)
                                                ->with(['delivery_order_detail'])
                                                ->get();

            foreach ($invoiceDetails as $key => $value) {
                 $totalTaxAmount += $value->qtyReturned * ((isset($value->delivery_order_detail->VATAmount) && !is_null($value->delivery_order_detail->VATAmount)) ? $value->delivery_order_detail->VATAmount : 0);
            }
        } else {
            $invoiceDetails = SalesReturnDetail::where('salesReturnID', $salesReturnID)
                                                ->with(['sales_invoice_detail'])
                                                ->get();

            foreach ($invoiceDetails as $key => $value) {
                $totalTaxAmount += $value->qtyReturned * ((isset($value->sales_invoice_detail->VATAmount) && !is_null($value->sales_invoice_detail->VATAmount)) ? $value->sales_invoice_detail->VATAmount : 0);
            }
        }

        if ($totalTaxAmount > 0) {
            $taxDelete = Taxdetail::where('documentSystemCode', $salesReturnID)
                                  ->where('documentSystemID', 87)
                                  ->delete();

            $res = $this->saveSalesReturnTaxDetails($salesReturnID, $totalTaxAmount);

            if (!$res['status']) {
               return ['status' => false, 'message' => $res['message']]; 
            } 
        } else {
            $taxDelete = Taxdetail::where('documentSystemCode', $salesReturnID)
                                  ->where('documentSystemID', 87)
                                  ->delete();

            $vatAmount['vatOutputGLCodeSystemID'] = 0;
            $vatAmount['vatOutputGLCode'] = null;
            $vatAmount['VATPercentage'] = 0;
            $vatAmount['VATAmount'] = 0;
            $vatAmount['VATAmountLocal'] = 0;
            $vatAmount['VATAmountRpt'] = 0;

            SalesReturn::where('id', $salesReturnID)->update($vatAmount);

        }

        return ['status' => true];
    }


     public function saveSalesReturnTaxDetails($salesReturnID, $totalVATAmount)
    {
        $percentage = 0;
        $taxMasterAutoID = 0;

        $master = SalesReturn::where('id', $salesReturnID)->first();

        if (empty($master)) {
            return ['status' => false, 'message' => 'Sales Return not found.'];
        }

        $invoiceDetail = SalesReturnDetail::where('salesReturnID', $salesReturnID)->first();
      
        if (empty($invoiceDetail)) {
            return ['status' => false, 'message' => 'Sales Return Details not found.'];
        }

        $totalAmount = 0;
        $decimal = \Helper::getCurrencyDecimalPlace($master->transactionCurrencyID);

        $totalDetail = SalesReturnDetail::select(DB::raw("SUM(transactionAmount) as amount"))
                                          ->where('salesReturnID', $salesReturnID)
                                          ->groupBy('salesReturnID')
                                          ->first();

        if (!empty($totalDetail)) {
            $totalAmount = $totalDetail->amount;
        }

        if ($totalAmount > 0) {
            $percentage = ($totalVATAmount / $totalAmount) * 100;
        }

        $Taxdetail = Taxdetail::where('documentSystemCode', $salesReturnID)
                                ->where('documentSystemID', 87)
                                ->first();

        if (!empty($Taxdetail)) {
            return ['status' => false, 'message' => 'VAT Detail Already exist.'];
        }

        $currencyConversion = \Helper::currencyConversion($master->companySystemID, $master->transactionCurrencyID, $master->transactionCurrencyID, $totalVATAmount);


        $_post['taxMasterAutoID'] = $taxMasterAutoID;
        $_post['companyID'] = $master->companyID;
        $_post['companySystemID'] = $master->companySystemID;
        $_post['documentID'] = 'SLR';
        $_post['documentSystemID'] = $master->documentSystemID;
        $_post['documentSystemCode'] = $salesReturnID;
        $_post['documentCode'] = $master->salesReturnCode;
        $_post['taxShortCode'] = ''; //$taxMaster->taxShortCode;
        $_post['taxDescription'] = ''; //$taxMaster->taxDescription;
        $_post['taxPercent'] = $percentage; //$taxMaster->taxPercent;
        $_post['payeeSystemCode'] = $master->customerID; //$taxMaster->payeeSystemCode;
        $_post['currency'] = $master->transactionCurrencyID;
        $_post['currencyER'] = $master->transactionCurrencyER;
        $_post['amount'] = round($totalVATAmount, $decimal);
        $_post['payeeDefaultCurrencyID'] = $master->transactionCurrencyID;
        $_post['payeeDefaultCurrencyER'] = $master->transactionCurrencyER;
        $_post['payeeDefaultAmount'] = round($totalVATAmount, $decimal);
        $_post['localCurrencyID'] = $master->companyLocalCurrencyID;
        $_post['localCurrencyER'] = $master->companyLocalCurrencyER;

        $_post['rptCurrencyID'] = $master->companyReportingCurrencyID;
        $_post['rptCurrencyER'] = $master->companyReportingCurrencyER;

        if ($_post['currency'] == $_post['rptCurrencyID']) {
            $MyRptAmount = $totalVATAmount;
        } else {
            if ($_post['rptCurrencyER'] > $_post['currencyER']) {
                if ($_post['rptCurrencyER'] > 1) {
                    $MyRptAmount = ($totalVATAmount / $_post['rptCurrencyER']);
                } else {
                    $MyRptAmount = ($totalVATAmount * $_post['rptCurrencyER']);
                }
            } else {
                if ($_post['rptCurrencyER'] > 1) {
                    $MyRptAmount = ($totalVATAmount * $_post['rptCurrencyER']);
                } else {
                    $MyRptAmount = ($totalVATAmount / $_post['rptCurrencyER']);
                }
            }
        }
        $_post["rptAmount"] = \Helper::roundValue($MyRptAmount);
        if ($_post['currency'] == $_post['localCurrencyID']) {
            $MyLocalAmount = $totalVATAmount;
        } else {
            if ($_post['localCurrencyER'] > $_post['currencyER']) {
                if ($_post['localCurrencyER'] > 1) {
                    $MyLocalAmount = ($totalVATAmount / $_post['localCurrencyER']);
                } else {
                    $MyLocalAmount = ($totalVATAmount * $_post['localCurrencyER']);
                }
            } else {
                if ($_post['localCurrencyER'] > 1) {
                    $MyLocalAmount = ($totalVATAmount * $_post['localCurrencyER']);
                } else {
                    $MyLocalAmount = ($totalVATAmount / $_post['localCurrencyER']);
                }
            }
        }

        $_post["localAmount"] = \Helper::roundValue($MyLocalAmount);
       
        Taxdetail::create($_post);
        $company = Company::select('vatOutputGLCode', 'vatOutputGLCodeSystemID')->where('companySystemID', $master->companySystemID)->first();

        $vatAmount['vatOutputGLCodeSystemID'] = $company->vatOutputGLCodeSystemID;
        $vatAmount['vatOutputGLCode'] = $company->vatOutputGLCode;
        $vatAmount['VATPercentage'] = $percentage;
        $vatAmount['VATAmount'] = $_post['amount'];
        $vatAmount['VATAmountLocal'] = $_post["localAmount"];
        $vatAmount['VATAmountRpt'] = $_post["rptAmount"];

        SalesReturn::where('id', $salesReturnID)->update($vatAmount);

        return ['status' => true];
    }


     private function updateInvoiceReturnedStatus($custInvoiceDirectAutoID){

        $status = 0;
        $invQty = SalesReturnDetail::where('custInvoiceDirectAutoID',$custInvoiceDirectAutoID)->sum('qtyReturnedDefaultMeasure');

        if($invQty!=0) {
            $doQty = SalesReturnDetail::where('custInvoiceDirectAutoID',$custInvoiceDirectAutoID)->sum('qtyReturnedDefaultMeasure');
            if($invQty == $doQty){
                $status = 2;    // fully invoiced
            }else{
                $status = 1;    // partially invoiced
            }
        }
        return CustomerInvoiceDirect::where('custInvoiceDirectAutoID',$custInvoiceDirectAutoID)->update(['returnStatus'=>$status]);

    }

     private function updateDOReturnedStatus($deliveryOrderID){

        $status = 0;
        $invQty = SalesReturnDetail::where('deliveryOrderID',$deliveryOrderID)->sum('qtyReturnedDefaultMeasure');

        if($invQty!=0) {
            $doQty = SalesReturnDetail::where('deliveryOrderID',$deliveryOrderID)->sum('qtyReturnedDefaultMeasure');
            if($invQty == $doQty){
                $status = 2;    // fully invoiced
            }else{
                $status = 1;    // partially invoiced
            }
        }
        return DeliveryOrder::where('deliveryOrderID',$deliveryOrderID)->update(['returnStatus'=>$status]);

    }
}
