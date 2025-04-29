<?php
/**
 * =============================================
 * -- File Name : PurchaseReturnDetailsAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Purchase Return Details
 * -- Author : Mohamed Fayas
 * -- Create date : 31 - July 2018
 * -- Description : This file contains the all CRUD for Purchase Return
 * -- REVISION HISTORY
 * -- Date: 10-August 2018 By: Fayas Description: Added new functions named as getPurchaseRequestByDocumentType()
 * -- Date: 10-August 2018 By: Fayas Description: Added new functions named as getItemsByPurchaseReturnMaster(),storePurchaseReturnDetailsFromGRV()
 * -- Date: 31-August 2018 By: Fayas Description: Added new functions named as purchaseReturnDeleteAllDetails()
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreatePurchaseReturnDetailsAPIRequest;
use App\Http\Requests\API\UpdatePurchaseReturnDetailsAPIRequest;
use App\Models\CustomerInvoiceDirect;
use App\Models\DeliveryOrder;
use App\Models\GRVMaster;
use App\Models\ItemIssueMaster;
use App\Models\BookInvSuppDet;
use App\Models\PurchaseReturn;
use App\Models\GRVDetails;
use App\Models\PurchaseReturnDetails;
use App\Models\StockTransfer;
use App\Models\TaxVatCategories;
use App\Repositories\PurchaseReturnDetailsRepository;
use App\Repositories\PurchaseReturnRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use Illuminate\Support\Facades\DB;
use App\helper\ItemTracking;
use App\Models\PurchaseReturnLogistic;

/**
 * Class PurchaseReturnDetailsController
 * @package App\Http\Controllers\API
 */
class PurchaseReturnDetailsAPIController extends AppBaseController
{
    /** @var  PurchaseReturnDetailsRepository */
    private $purchaseReturnDetailsRepository;
    private $purchaseReturnRepository;

    public function __construct(PurchaseReturnDetailsRepository $purchaseReturnDetailsRepo, PurchaseReturnRepository $purchaseReturnRepository)
    {
        $this->purchaseReturnDetailsRepository = $purchaseReturnDetailsRepo;
        $this->purchaseReturnRepository = $purchaseReturnRepository;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/purchaseReturnDetails",
     *      summary="Get a listing of the PurchaseReturnDetails.",
     *      tags={"PurchaseReturnDetails"},
     *      description="Get all PurchaseReturnDetails",
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
     *                  @SWG\Items(ref="#/definitions/PurchaseReturnDetails")
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
        $this->purchaseReturnDetailsRepository->pushCriteria(new RequestCriteria($request));
        $this->purchaseReturnDetailsRepository->pushCriteria(new LimitOffsetCriteria($request));
        $purchaseReturnDetails = $this->purchaseReturnDetailsRepository->all();

        return $this->sendResponse($purchaseReturnDetails->toArray(), 'Purchase Return Details retrieved successfully');
    }

    /**
     * @param CreatePurchaseReturnDetailsAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/purchaseReturnDetails",
     *      summary="Store a newly created PurchaseReturnDetails in storage",
     *      tags={"PurchaseReturnDetails"},
     *      description="Store PurchaseReturnDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="PurchaseReturnDetails that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/PurchaseReturnDetails")
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
     *                  ref="#/definitions/PurchaseReturnDetails"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreatePurchaseReturnDetailsAPIRequest $request)
    {
        $input = $request->all();

        $purchaseReturnDetails = $this->purchaseReturnDetailsRepository->create($input);

        return $this->sendResponse($purchaseReturnDetails->toArray(), 'Purchase Return Details saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/purchaseReturnDetails/{id}",
     *      summary="Display the specified PurchaseReturnDetails",
     *      tags={"PurchaseReturnDetails"},
     *      description="Get PurchaseReturnDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PurchaseReturnDetails",
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
     *                  ref="#/definitions/PurchaseReturnDetails"
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
        /** @var PurchaseReturnDetails $purchaseReturnDetails */
        $purchaseReturnDetails = $this->purchaseReturnDetailsRepository->findWithoutFail($id);

        if (empty($purchaseReturnDetails)) {
            return $this->sendError('Purchase Return Details not found');
        }

        return $this->sendResponse($purchaseReturnDetails->toArray(), 'Purchase Return Details retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdatePurchaseReturnDetailsAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/purchaseReturnDetails/{id}",
     *      summary="Update the specified PurchaseReturnDetails in storage",
     *      tags={"PurchaseReturnDetails"},
     *      description="Update PurchaseReturnDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PurchaseReturnDetails",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="PurchaseReturnDetails that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/PurchaseReturnDetails")
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
     *                  ref="#/definitions/PurchaseReturnDetails"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdatePurchaseReturnDetailsAPIRequest $request)
    {
        $input = $request->all();
        $qtyError = array('type' => 'qty');
        /** @var PurchaseReturnDetails $purchaseReturnDetails */
        $purchaseReturnDetails = $this->purchaseReturnDetailsRepository->findWithoutFail($id);

        if (empty($purchaseReturnDetails)) {
            return $this->sendError('Purchase Return Details not found');
        }

        $grvDetails = GRVDetails::find($purchaseReturnDetails->grvDetailsID);

        if (!$grvDetails) {
            return $this->sendError('GRV Details not found');
        }

        $purchaseReturn = PurchaseReturn::find($purchaseReturnDetails->purhaseReturnAutoID);

        if (empty($purchaseReturn)) {
            return $this->sendError('Purchase Return not found');
        }


        if ($input['noQty'] > ($grvDetails->noQty - $grvDetails->returnQty)) {
             return $this->sendError("GRV balance Qty is ".($grvDetails->noQty - $grvDetails->returnQty).". You cannot return more than balance Qty.", 500,$qtyError);
        }


        $data = array('companySystemID' => $purchaseReturn->companySystemID,
            'itemCodeSystem' => $purchaseReturnDetails->itemCode,
            'wareHouseId' => $purchaseReturn->purchaseReturnLocation);
        $itemCurrentCostAndQty = \Inventory::itemCurrentCostAndQty($data);

        if ($itemCurrentCostAndQty['currentWareHouseStockQty'] <= 0) {
            $this->purchaseReturnDetailsRepository->update(['noQty' => 0,'netAmount' => 0,'netAmountLocal' => 0,'netAmountRpt' => 0], $id);
            return $this->sendError("Warehouse stock Qty is 0. You cannot return.", 500,$qtyError);
        }
        if ($itemCurrentCostAndQty['currentStockQty'] <= 0) {
            $this->purchaseReturnDetailsRepository->update(['noQty' => 0,'netAmount' => 0,'netAmountLocal' => 0,'netAmountRpt' => 0], $id);
            return $this->sendError("Stock Qty is 0. You cannot return.", 500,$qtyError);
        }
        if ($input['noQty'] > $itemCurrentCostAndQty['currentStockQty']) {
            $this->purchaseReturnDetailsRepository->update(['noQty' => 0,'netAmount' => 0,'netAmountLocal' => 0,'netAmountRpt' => 0], $id);
            return $this->sendError("Current stock Qty is: " . $itemCurrentCostAndQty['currentStockQty'] . " .You cannot return more than the current stock qty.", 500, $qtyError);
        }

        if ($input['noQty'] > $itemCurrentCostAndQty['currentWareHouseStockQty']) {
            $this->purchaseReturnDetailsRepository->update(['noQty' => 0,'netAmount' => 0,'netAmountLocal' => 0,'netAmountRpt' => 0], $id);
            return $this->sendError("Current warehouse stock Qty is: " . $itemCurrentCostAndQty['currentWareHouseStockQty'] . " .You cannot return more than the current warehouse stock qty.", 500, $qtyError);
        }

        if ($input['noQty'] > $purchaseReturnDetails->GRVQty) {
            $this->purchaseReturnDetailsRepository->update(['noQty' => 0,'netAmount' => 0,'netAmountLocal' => 0,'netAmountRpt' => 0], $id);
            return $this->sendError("Return qty cannot be greater than GRV qty.", 500, $qtyError);
        }

        $input['netAmount'] = $input['noQty'] * $purchaseReturnDetails->GRVcostPerUnitSupTransCur;
        $input['netAmountLocal'] = $input['noQty'] * $purchaseReturnDetails->GRVcostPerUnitLocalCur;
        $input['netAmountRpt'] = $input['noQty'] * $purchaseReturnDetails->GRVcostPerUnitComRptCur;
        
        if (isset($input['unit'])) {
            unset($input['unit']);
        }

        if (isset($input['grv_master'])) {
            unset($input['grv_master']);
        }

        if (isset($input['grv_detail_master'])) {
            unset($input['grv_detail_master']);
        }

        if (isset($input['item_by'])) {
            unset($input['item_by']);
        }

        DB::beginTransaction();
        try {
            $purchaseReturnDetails = $this->purchaseReturnDetailsRepository->update($input, $id);

            $res = $this->purchaseReturnDetailsRepository->savePrnLogistics($id);
            if (!$res['status']) {
                DB::rollback();
                return $this->sendError($res['message'], 500);
            }

            DB::commit();
            return $this->sendResponse($purchaseReturnDetails->toArray(), 'PurchaseReturnDetails updated successfully');
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError('Error Occurred', 500);
        }

    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/purchaseReturnDetails/{id}",
     *      summary="Remove the specified PurchaseReturnDetails from storage",
     *      tags={"PurchaseReturnDetails"},
     *      description="Delete PurchaseReturnDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PurchaseReturnDetails",
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
        /** @var PurchaseReturnDetails $purchaseReturnDetails */
        $purchaseReturnDetails = $this->purchaseReturnDetailsRepository->findWithoutFail($id);

        if (empty($purchaseReturnDetails)) {
            return $this->sendError('Purchase Return Details not found');
        }

        $purchaseReturn = PurchaseReturn::where('purhaseReturnAutoID', $purchaseReturnDetails->purhaseReturnAutoID)->first();

        if (empty($purchaseReturn)) {
            return $this->sendError('Purchase Return not found');
        }

        if ($purchaseReturnDetails->trackingType == 2) {
            $validateSubProductSold = DocumentSubProduct::where('documentSystemID', $purchaseReturn->documentSystemID)
                                                         ->where('documentDetailID', $id)
                                                         ->where('sold', 1)
                                                         ->first();

            if ($validateSubProductSold) {
                return $this->sendError('You cannot delete this line item. Serial details are sold already.', 422);
            }

            $subProduct = DocumentSubProduct::where('documentSystemID', $purchaseReturn->documentSystemID)
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
        } else if ($purchaseReturnDetails->trackingType == 1) {
            $deleteBatch = ItemTracking::revertBatchTrackingSoldStatus($purchaseReturn->documentSystemID, $id);

            if (!$deleteBatch['status']) {
                return $this->sendError($deleteBatch['message'], 422);
            }
        }

        $purchaseReturnDetails->delete();


        $remainingPrnDetails = PurchaseReturnDetails::where('purhaseReturnAutoID', $purchaseReturnDetails->purhaseReturnAutoID)
                                                    ->first();

        if (!$remainingPrnDetails) {
            $masterData = PurchaseReturn::find($purchaseReturnDetails->purhaseReturnAutoID);  

            if (!$masterData) {
                return $this->sendError('Purchase Return not found');
            } 

            $masterData->isInvoiceCreatedForGrv = 0;     
            $masterData->save();
        } 

        return $this->sendResponse($id, 'Purchase Return Details deleted successfully');
    }

    public function getItemsByPurchaseReturnMaster(Request $request)
    {

        $input = $request->all();
        /** @var PurchaseReturn $purchaseReturn */
        $purchaseReturn = $this->purchaseReturnRepository->findWithoutFail($input['id']);

        if (empty($purchaseReturn)) {
            return $this->sendError('Purchase Return  not found');
        }

        $purchaseReturnDetails = PurchaseReturnDetails::where('purhaseReturnAutoID', $input['id'])->with(['unit', 'grv_master', 'grv_detail_master', 'item_by'])->get();

        return $this->sendResponse($purchaseReturnDetails, 'Purchase Return Details retrieved successfully');
    }

    public function storePurchaseReturnDetailsFromGRV(Request $request)
    {

        $input = $request->all();

        $employee = \Helper::getEmployeeInfo();

        /** @var PurchaseReturn $purchaseReturn */
        $purchaseReturn = $this->purchaseReturnRepository->findWithoutFail($input['purhaseReturnAutoID']);

        if (empty($purchaseReturn)) {
            return $this->sendError('Purchase Return  not found');
        }


        $prDetails = PurchaseReturnDetails::where('purhaseReturnAutoID', $input['purhaseReturnAutoID'])
                                          ->get();

        $checkDuplicateGrv = false;
        foreach ($prDetails as $key => $value) {
            if ($value->grvAutoID != $input['grvAutoID']) {
                $checkDuplicateGrv = true;
            }
        }

        if ($checkDuplicateGrv) {
            return $this->sendError('Different GRV cannot be added to Purchase Return');
        }


        $checkOtherPrns = PurchaseReturnDetails::with(['master' => function ($query) {
                                                    $query->where('approved', 0);
                                               }])
                                               ->where('purhaseReturnAutoID','!=', $input['purhaseReturnAutoID'])
                                               ->where('grvAutoID', $input['grvAutoID'])
                                               ->whereHas('master', function($query) {
                                                    $query->where('approved', 0);
                                               })
                                               ->first();

        if ($checkOtherPrns) {
            return $this->sendError("There is a Purchase Return (" . $checkOtherPrns->master->purchaseReturnCode . ") pending for approval for the GRV you are trying to add. Please check again.", 500);
        }

        $grv = GRVMaster::find($input['grvAutoID']);

        if (empty($grv)) {
            return $this->sendError('GRV not found');
        }


        $checkGrvAddedToIncoice = BookInvSuppDet::where('grvAutoID', $input['grvAutoID'])
                                                ->with(['suppinvmaster'])
                                                ->whereHas('suppinvmaster', function($query) {
                                                    $query->where('approved', 0);
                                                })
                                                ->first();

        if ($checkGrvAddedToIncoice) {
            $supInvCode = (isset($checkGrvAddedToIncoice->suppinvmaster->bookingInvCode)) ? $checkGrvAddedToIncoice->suppinvmaster->bookingInvCode : "";
            return $this->sendError('Selected GRV is been added to a draft supplier invoice '.$supInvCode.'. Delete the GRV from the invoice and try again.', 500);
        }

        $finalError = array('cost_zero' => array(),
            'cost_neg' => array(),
            'same_item' => array(),
            'qty_zero' => array(),
            'more_then_grv_qty' => array(),
            'currentStockQty_zero' => array(),
            'currentWareHouseStockQty_zero' => array(),
            'currentStockQty_more' => array(),
            'currentWareHouseStockQty_more' => array());

        $error_count = 0;

        $createArray = array();

        foreach ($input['detailTable'] as $new) {

            if ($new['isChecked']) {
                $detailExistSameItem = PurchaseReturnDetails::where('purhaseReturnAutoID', $input['purhaseReturnAutoID'])
                    ->where('itemCode', $new['itemCode'])
                    ->where('grvAutoID', $new['grvAutoID'])
                    ->count();

                if ($detailExistSameItem > 0) {
                    // return $this->sendError('Same inventory item cannot be added more than once', 500);
                    array_push($finalError['same_item'], $new['itemPrimaryCode']);
                    $error_count++;
                }
                if ($new['rnoQty'] <= 0) {
                    // return $this->sendError('Cannot add item without qty', 500);
                    array_push($finalError['qty_zero'], $new['itemPrimaryCode']);
                    $error_count++;
                }

                $data = array('companySystemID' => $purchaseReturn->companySystemID,
                    'itemCodeSystem' => $new['itemCode'],
                    'wareHouseId' => $purchaseReturn->purchaseReturnLocation);
                $itemCurrentCostAndQty = \Inventory::itemCurrentCostAndQty($data);

                if ($itemCurrentCostAndQty['currentWareHouseStockQty'] <= 0) {
                    array_push($finalError['currentStockQty_zero'], $new['itemPrimaryCode']);
                    $error_count++;
                }
                if ($itemCurrentCostAndQty['currentStockQty'] <= 0) {
                    array_push($finalError['currentWareHouseStockQty_zero'], $new['itemPrimaryCode']);
                    $error_count++;
                }
                if ($new['rnoQty'] > $itemCurrentCostAndQty['currentStockQty']) {
                    array_push($finalError['currentStockQty_more'], $new['itemPrimaryCode']);
                    $error_count++;
                }

                if ($new['rnoQty'] > $itemCurrentCostAndQty['currentWareHouseStockQty']) {
                    array_push($finalError['currentWareHouseStockQty_more'], $new['itemPrimaryCode']);
                    $error_count++;
                }

                if ($new['rnoQty'] > $new['noQty']) {
                    array_push($finalError['more_then_grv_qty'], $new['itemPrimaryCode']);
                    $error_count++;
                }

                /*Pending Approval Check in purchase return*/
                $checkWhether = PurchaseReturn::where('purhaseReturnAutoID', '!=', $input['purhaseReturnAutoID'])
                    ->where('companySystemID', $purchaseReturn->companySystemID)
                    ->select([
                        'erp_purchasereturnmaster.purhaseReturnAutoID',
                        'erp_purchasereturnmaster.companySystemID',
                        'erp_purchasereturnmaster.purchaseReturnLocation',
                        'erp_purchasereturnmaster.purchaseReturnCode',
                    ])
                    ->groupBy(
                        'erp_purchasereturnmaster.purhaseReturnAutoID',
                        'erp_purchasereturnmaster.companySystemID',
                        'erp_purchasereturnmaster.purchaseReturnLocation'
                    )
                    ->whereHas('details', function ($query) use ($new) {
                        $query->where('itemCode', $new['itemCode']);
                    })
                    ->where('approved', 0)
                    ->first();
                /* approved=0*/

                if (!empty($checkWhether)) {
                    return $this->sendError("There is a Purchase Return (" . $checkWhether->purchaseReturnCode . ") pending for approval for the item you are trying to add. Please check again.", 500);
                }
                /*check item Stock Transfer*/
                $checkWhetherStockTransfer = StockTransfer::where('companySystemID', $purchaseReturn->companySystemID)
                    ->where('locationFrom', $purchaseReturn->purchaseReturnLocation)
                    ->select([
                        'erp_stocktransfer.stockTransferAutoID',
                        'erp_stocktransfer.companySystemID',
                        'erp_stocktransfer.locationFrom',
                        'erp_stocktransfer.stockTransferCode',
                        'erp_stocktransfer.approved'
                    ])
                    ->groupBy(
                        'erp_stocktransfer.stockTransferAutoID',
                        'erp_stocktransfer.companySystemID',
                        'erp_stocktransfer.locationFrom',
                        'erp_stocktransfer.stockTransferCode',
                        'erp_stocktransfer.approved'
                    )
                    ->whereHas('details', function ($query) use ($new) {
                        $query->where('itemCodeSystem', $new['itemCode']);
                    })
                    ->where('approved', 0)
                    ->first();
                /* approved=0*/

                if (!empty($checkWhetherStockTransfer)) {
                    return $this->sendError("There is a Stock Transfer (" . $checkWhetherStockTransfer->stockTransferCode . ") pending for approval for the item you are trying to add. Please check again.", 500);
                }

                /*check item sales invoice*/
                $checkWhetherInvoice = CustomerInvoiceDirect::where('companySystemID', $purchaseReturn->companySystemID)
                    ->select([
                        'erp_custinvoicedirect.custInvoiceDirectAutoID',
                        'erp_custinvoicedirect.bookingInvCode',
                        'erp_custinvoicedirect.wareHouseSystemCode',
                        'erp_custinvoicedirect.approved'
                    ])
                    ->groupBy(
                        'erp_custinvoicedirect.custInvoiceDirectAutoID',
                        'erp_custinvoicedirect.companySystemID',
                        'erp_custinvoicedirect.bookingInvCode',
                        'erp_custinvoicedirect.wareHouseSystemCode',
                        'erp_custinvoicedirect.approved'
                    )
                    ->whereHas('issue_item_details', function ($query) use ($new) {
                        $query->where('itemCodeSystem', $new['itemCode']);
                    })
                    ->where('approved', 0)
                    ->where('canceledYN', 0)
                    ->first();
                /* approved=0*/

                if (!empty($checkWhetherInvoice)) {
                    return $this->sendError("There is a Customer Invoice (" . $checkWhetherInvoice->bookingInvCode . ") pending for approval for the item you are trying to add. Please check again.", 500);
                }

                // check in delivery order
                $checkWhetherDeliveryOrder = DeliveryOrder::where('companySystemID', $purchaseReturn->companySystemID)
                    ->select([
                        'erp_delivery_order.deliveryOrderID',
                        'erp_delivery_order.deliveryOrderCode'
                    ])
                    ->groupBy(
                        'erp_delivery_order.deliveryOrderID',
                        'erp_delivery_order.companySystemID'
                    )
                    ->whereHas('detail', function ($query) use ($new) {
                        $query->where('itemCodeSystem', $new['itemCode']);
                    })
                    ->where('approvedYN', 0)
                    ->first();

                if (!empty($checkWhetherDeliveryOrder)) {
                    return $this->sendError("There is a Delivery Order (" . $checkWhetherDeliveryOrder->deliveryOrderCode . ") pending for approval for the item you are trying to add. Please check again.", 500);
                }

                // check in Material Issue
                $checkWhetherItemIssueMaster = ItemIssueMaster::where('companySystemID', $purchaseReturn->companySystemID)
                    ->select([
                        'erp_itemissuemaster.itemIssueAutoID',
                        'erp_itemissuemaster.companySystemID',
                        'erp_itemissuemaster.wareHouseFromCode',
                        'erp_itemissuemaster.itemIssueCode',
                        'erp_itemissuemaster.approved'
                    ])
                    ->groupBy(
                        'erp_itemissuemaster.itemIssueAutoID',
                        'erp_itemissuemaster.companySystemID',
                        'erp_itemissuemaster.wareHouseFromCode',
                        'erp_itemissuemaster.itemIssueCode',
                        'erp_itemissuemaster.approved'
                    )
                    ->whereHas('details', function ($query) use ($new) {
                        $query->where('itemCodeSystem', $new['itemCode']);
                    })
                    ->where('approved', 0)
                    ->first();
                /* approved=0*/

                if (!empty($checkWhetherItemIssueMaster)) {
                    return $this->sendError("There is a Materiel Issue (" . $checkWhetherItemIssueMaster->itemIssueCode . ") pending for approval for the item you are trying to add. Please check again.", 500);
                }


                $item = array();

                $item['createdPCID'] = gethostname();
                $item['createdUserID'] = $employee->empID;
                $item['createdUserSystemID'] = $employee->employeeSystemID;

                $item['purhaseReturnAutoID'] = $input['purhaseReturnAutoID'];
                $item['companyID'] = 'string';
                $item['grvAutoID'] = $new['grvAutoID'];
                $item['grvDetailsID'] = $new['grvDetailsID'];
                $item['itemCode'] = $new['itemCode'];
                $item['trackingType'] = $new['trackingType'];
                $item['itemPrimaryCode'] = $new['itemPrimaryCode'];
                $item['itemDescription'] = $new['itemDescription'];
                $item['supplierPartNumber'] = $new['supplierPartNumber'];
                $item['unitOfMeasure'] = $new['unitOfMeasure'];
                $item['GRVQty'] = $new['noQty'];
                $item['comment'] = $new['comment'];
                $item['noQty'] = $new['rnoQty'];

                $item['supplierDefaultCurrencyID'] = $new['supplierDefaultCurrencyID'];
                $item['supplierDefaultER'] = $new['supplierDefaultER'];

                $item['supplierTransactionCurrencyID'] = $new['supplierDefaultCurrencyID'];
                $item['supplierTransactionER'] = $new['supplierDefaultER'];

                $item['companyReportingCurrencyID'] = $new['companyReportingCurrencyID'];
                $item['companyReportingER'] = $new['companyReportingER'];
                $item['localCurrencyID'] = $new['localCurrencyID'];
                $item['localCurrencyER'] = $new['localCurrencyER'];

                $expenseCOA = TaxVatCategories::with(['tax'])->where('taxVatSubCategoriesAutoID', $new['vatSubCategoryID'])->where('subCatgeoryType', 3)->whereHas('tax', function ($query) use ($purchaseReturn) {
                    $query->where('companySystemID', $purchaseReturn->companySystemID);
                })->where('isActive', 1)->first();

                if(!empty($expenseCOA) && $expenseCOA->recordType == 1){
                    $item['GRVcostPerUnitLocalCur'] = $new['GRVcostPerUnitLocalCur'] - $new['VATAmountLocal'];
                    $item['GRVcostPerUnitSupDefaultCur'] = $new['GRVcostPerUnitSupDefaultCur'] - ($new['VATAmount']/$new['supplierDefaultER']);
                    $item['GRVcostPerUnitSupTransCur'] = $new['GRVcostPerUnitSupTransCur'] - $new['VATAmount'];
                    $item['GRVcostPerUnitComRptCur'] = $new['GRVcostPerUnitComRptCur'] - $new['VATAmountRpt'];

                    $item['netAmount'] = $new['rnoQty'] * ($new['GRVcostPerUnitSupTransCur'] - $new['VATAmount']);
                    $item['netAmountLocal'] = $new['rnoQty'] * ($new['GRVcostPerUnitLocalCur'] - $new['VATAmountLocal']);
                    $item['netAmountRpt'] = $new['rnoQty'] * ($new['GRVcostPerUnitComRptCur'] - $new['VATAmountRpt']);
                } else {
                    $item['GRVcostPerUnitLocalCur'] = $new['GRVcostPerUnitLocalCur'];
                    $item['GRVcostPerUnitSupDefaultCur'] = $new['GRVcostPerUnitSupDefaultCur'];
                    $item['GRVcostPerUnitSupTransCur'] = $new['GRVcostPerUnitSupTransCur'];
                    $item['GRVcostPerUnitComRptCur'] = $new['GRVcostPerUnitComRptCur'];

                    $item['netAmount'] = $new['rnoQty'] * $new['GRVcostPerUnitSupTransCur'];
                    $item['netAmountLocal'] = $new['rnoQty'] * $new['GRVcostPerUnitLocalCur'];
                    $item['netAmountRpt'] = $new['rnoQty'] * $new['GRVcostPerUnitComRptCur'];
                }


                $item['itemFinanceCategoryID'] = $new['itemFinanceCategoryID'];
                $item['itemFinanceCategorySubID'] = $new['itemFinanceCategorySubID'];
                $item['financeGLcodebBSSystemID'] = $new['financeGLcodebBSSystemID'];
                $item['financeGLcodebBS'] = $new['financeGLcodebBS'];
                $item['financeGLcodePLSystemID'] = $new['financeGLcodePLSystemID'];
                $item['financeGLcodePL'] = $new['financeGLcodePL'];
                $item['includePLForGRVYN'] = $new['includePLForGRVYN'];


                $item['vatRegisteredYN'] = $new['vatRegisteredYN'];
                $item['supplierVATEligible'] = $new['supplierVATEligible'];
                $item['VATPercentage'] = $new['VATPercentage'];
                $item['VATAmount'] = $new['VATAmount'];
                $item['VATAmountLocal'] = $new['VATAmountLocal'];
                $item['VATAmountRpt'] = $new['VATAmountRpt'];
                $item['vatMasterCategoryID'] = $new['vatMasterCategoryID'];
                $item['vatSubCategoryID'] = $new['vatSubCategoryID'];
                $item['exempt_vat_portion'] = $new['exempt_vat_portion'];

                array_push($createArray, $item);
            }
        }

        DB::beginTransaction();
        try {

            $confirm_error = array('type' => 'confirm_error', 'data' => $finalError);

            if ($error_count > 0) {
                return $this->sendError("You cannot confirm this document.", 500, $confirm_error);
            } else {
                foreach ($createArray as $item) {
                    $resPrDetail = $this->purchaseReturnDetailsRepository->create($item);

                    $res = $this->purchaseReturnDetailsRepository->savePrnLogistics($resPrDetail->purhasereturnDetailID);
                    if (!$res['status']) {
                        DB::rollback();
                        return $this->sendError($res['message'], 500);
                    }
                }
            }

            $this->updateGrvInvoiceStatus($input['purhaseReturnAutoID'], $input['grvAutoID']);

            DB::commit();
            return $this->sendResponse($purchaseReturn, 'Purchase Return Details added successfully');
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError('Error Occurred', 500);
        }
    }


    public function updateGrvInvoiceStatus($purhaseReturnAutoID, $grvAutoID)
    {
        $purchaseReturn = $this->purchaseReturnRepository->findWithoutFail($purhaseReturnAutoID);

        if (empty($purchaseReturn)) {
            return false;
        }

        $checkGrvAddedToIncoice = BookInvSuppDet::where('grvAutoID', $grvAutoID)
                                                ->whereHas('suppinvmaster', function($query) {
                                                    $query->where('approved', -1);
                                                })
                                                ->first();

        if ($checkGrvAddedToIncoice) {
            $purchaseReturn->isInvoiceCreatedForGrv = 1;
        } else {
            $purchaseReturn->isInvoiceCreatedForGrv = 0;
        }

        $purchaseReturn->save();

        return true;
    }


    public function purchaseReturnDeleteAllDetails(Request $request)
    {
        $input = $request->all();

        $purchaseReturnAutoID = $input['purhaseReturnAutoID'];

        $detailExistAll = PurchaseReturnDetails::where('purhaseReturnAutoID', $purchaseReturnAutoID)->get();

        if (count($detailExistAll) == 0) {
            return $this->sendError('There are no details to delete');
        }

        $purchaseReturn = PurchaseReturn::find($input['purhaseReturnAutoID']);

        if (!$purchaseReturn) {
            return $this->sendError('Purchase Return not found');
        }

        if (!empty($detailExistAll)) {
            foreach ($detailExistAll as $cvDetail) {

                if ($cvDetail->trackingType == 2) {
                    $validateSubProductSold = DocumentSubProduct::where('documentSystemID', $purchaseReturn->documentSystemID)
                                                                 ->where('documentDetailID', $cvDetail->purhasereturnDetailID)
                                                                 ->where('sold', 1)
                                                                 ->first();

                    if ($validateSubProductSold) {
                        return $this->sendError('You cannot delete this line item. Serial details are sold already.', 422);
                    }

                    $subProduct = DocumentSubProduct::where('documentSystemID', $purchaseReturn->documentSystemID)
                                                     ->where('documentDetailID', $cvDetail->purhasereturnDetailID);

                    $productInIDs = ($subProduct->count() > 0) ? $subProduct->get()->pluck('productInID')->toArray() : [];
                    $serialIds = ($subProduct->count() > 0) ? $subProduct->get()->pluck('productSerialID')->toArray() : [];

                    if (count($productInIDs) > 0) {
                        $updateSerial = ItemSerial::whereIn('id', $serialIds)
                                                  ->update(['soldFlag' => 0]);

                        $updateSerial = DocumentSubProduct::whereIn('id', $productInIDs)
                                                  ->update(['sold' => 0, 'soldQty' => 0]);

                        $subProduct->delete();
                    }
                } else if ($cvDetail->trackingType == 1) {

                    $deleteBatch = ItemTracking::revertBatchTrackingSoldStatus($purchaseReturn->documentSystemID, $cvDetail->purhasereturnDetailID);

                    if (!$deleteBatch['status']) {
                        return $this->sendError($deleteBatch['message'], 422);
                    }
                }


                $deleteDetail = PurchaseReturnDetails::where('purhasereturnDetailID', $cvDetail['purhasereturnDetailID'])->delete();
                PurchaseReturnLogistic::where('purchaseReturnID', $cvDetail['purhaseReturnAutoID'])->delete();
            }
        }
        return $this->sendResponse($purchaseReturnAutoID, 'Purchase Return details deleted successfully');
    }

    public function grvReturnDetails(Request $request)
    {
        $input = $request->all();

        $grvDetailsID = $input['grvDetailsID'];

        $detailExistAll = PurchaseReturnDetails::with(['master' => function($query) {
                                                    $query->with(['currency_by']);
                                                }, 'unit'])
                                               ->where('grvDetailsID', $grvDetailsID)
                                               ->whereHas('master', function($query) {
                                                    $query->where('approved', -1); 
                                               })
                                               ->get();

        return $this->sendResponse($detailExistAll, 'Purchase Return details deleted successfully');
    }

}
