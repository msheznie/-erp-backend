<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateSalesReturnDetailAPIRequest;
use App\Http\Requests\API\UpdateSalesReturnDetailAPIRequest;
use App\Models\SalesReturnDetail;
use App\Models\DeliveryOrder;
use App\Models\DeliveryOrderDetail;
use App\Models\CustomerInvoiceDirect;
use App\Models\CustomerInvoiceItemDetails;
use App\Models\SalesReturn;
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

        return $this->sendResponse($salesReturnDetails->toArray(), 'Sales Return Details retrieved successfully');
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

        return $this->sendResponse($salesReturnDetail->toArray(), 'Sales Return Detail saved successfully');
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
            return $this->sendError('Sales Return Detail not found');
        }

        return $this->sendResponse($salesReturnDetail->toArray(), 'Sales Return Detail retrieved successfully');
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
            return $this->sendError('Sales Return Detail not found');
        }

        $salesReturnData = SalesReturn::find($input['salesReturnID']);
        if (!$salesReturnData) {
            return $this->sendError('Sales Return Data not found');
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

            DB::commit();
            return $this->sendResponse([], 'Sales Return Item Details updated successfully');
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError('Error Occurred'. $exception->getMessage() . 'Line :' . $exception->getLine());
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

            DB::commit();
            return $this->sendResponse([], 'Sales Return Item Details updated successfully');
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError('Error Occurred'. $exception->getMessage() . 'Line :' . $exception->getLine());
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
            return $this->sendError('Sales Return Detail not found');
        }

        $salesReturn = SalesReturn::find($salesReturnDetail->salesReturnID);

        if (!$salesReturn) {
            return $this->sendError('Sales Return not found');
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

        return $this->sendResponse([], 'Sales Return Detail deleted successfully');
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
