<?php

namespace App\Http\Controllers\API;

use App\helper\Helper;
use App\helper\TaxService;
use App\helper\inventory;
use App\helper\ItemTracking;
use App\Http\Requests\API\CreateCustomerInvoiceItemDetailsAPIRequest;
use App\Http\Requests\API\UpdateCustomerInvoiceItemDetailsAPIRequest;
use App\Models\CompanyPolicyMaster;
use App\Models\CustomerCatalogDetail;
use App\Models\CustomerInvoiceDirect;
use App\Models\CustomerInvoiceItemDetails;
use App\Models\DeliveryOrder;
use App\Models\DeliveryOrderDetail;
use App\Models\FinanceItemcategorySubAssigned;
use App\Models\ItemAssigned;
use App\Models\ItemClientReferenceNumberMaster;
use App\Models\ItemIssueMaster;
use App\Models\ItemMaster;
use App\Models\PurchaseReturn;
use App\Models\DocumentSubProduct;
use App\Models\ItemSerial;
use App\Models\QuotationDetails;
use App\Models\QuotationMaster;
use App\Models\Company;
use App\Models\StockTransfer;
use App\Models\Taxdetail;
use App\Models\Unit;
use App\Models\UnitConversion;
use App\Repositories\CustomerInvoiceItemDetailsRepository;
use App\Services\API\CustomerInvoiceAPIService;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Models\CustomerInvoiceLogistic;
use App\Models\DeliveryTermsMaster;
use Illuminate\Support\Facades\DB;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class CustomerInvoiceItemDetailsController
 * @package App\Http\Controllers\API
 */

class CustomerInvoiceItemDetailsAPIController extends AppBaseController
{
    /** @var  CustomerInvoiceItemDetailsRepository */
    private $customerInvoiceItemDetailsRepository;

    public function __construct(CustomerInvoiceItemDetailsRepository $customerInvoiceItemDetailsRepo)
    {
        $this->customerInvoiceItemDetailsRepository = $customerInvoiceItemDetailsRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/customerInvoiceItemDetails",
     *      summary="Get a listing of the CustomerInvoiceItemDetails.",
     *      tags={"CustomerInvoiceItemDetails"},
     *      description="Get all CustomerInvoiceItemDetails",
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
     *                  @SWG\Items(ref="#/definitions/CustomerInvoiceItemDetails")
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
        $this->customerInvoiceItemDetailsRepository->pushCriteria(new RequestCriteria($request));
        $this->customerInvoiceItemDetailsRepository->pushCriteria(new LimitOffsetCriteria($request));
        $customerInvoiceItemDetails = $this->customerInvoiceItemDetailsRepository->all();

        return $this->sendResponse($customerInvoiceItemDetails->toArray(), trans('custom.customer_invoice_item_details_retrieved_successful'));
    }

    /**
     * @param CreateCustomerInvoiceItemDetailsAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/customerInvoiceItemDetails",
     *      summary="Store a newly created CustomerInvoiceItemDetails in storage",
     *      tags={"CustomerInvoiceItemDetails"},
     *      description="Store CustomerInvoiceItemDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="CustomerInvoiceItemDetails that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/CustomerInvoiceItemDetails")
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
     *                  ref="#/definitions/CustomerInvoiceItemDetails"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(Request $request)
    {
        $input = $request->all();

        $returnData = CustomerInvoiceAPIService::customerInvoiceItemDetailsStore($input);
        if($returnData['status']){
            return $this->sendResponse($returnData['data'],$returnData['message']);
        }
        else{
            return $this->sendError($returnData['message'], (isset($returnData['code']) && $returnData['code'] == 500) ? $returnData['code'] : 404);
        }
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/customerInvoiceItemDetails/{id}",
     *      summary="Display the specified CustomerInvoiceItemDetails",
     *      tags={"CustomerInvoiceItemDetails"},
     *      description="Get CustomerInvoiceItemDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CustomerInvoiceItemDetails",
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
     *                  ref="#/definitions/CustomerInvoiceItemDetails"
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
        /** @var CustomerInvoiceItemDetails $customerInvoiceItemDetails */
        $customerInvoiceItemDetails = $this->customerInvoiceItemDetailsRepository->findWithoutFail($id);

        if (empty($customerInvoiceItemDetails)) {
            return $this->sendError(trans('custom.customer_invoice_item_details_not_found'));
        }

        return $this->sendResponse($customerInvoiceItemDetails->toArray(), trans('custom.customer_invoice_item_details_retrieved_successful'));
    }

    /**
     * @param int $id
     * @param UpdateCustomerInvoiceItemDetailsAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/customerInvoiceItemDetails/{id}",
     *      summary="Update the specified CustomerInvoiceItemDetails in storage",
     *      tags={"CustomerInvoiceItemDetails"},
     *      description="Update CustomerInvoiceItemDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CustomerInvoiceItemDetails",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="CustomerInvoiceItemDetails that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/CustomerInvoiceItemDetails")
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
     *                  ref="#/definitions/CustomerInvoiceItemDetails"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, Request $request)
    {
        $input = $request->all();
        $input = array_except($request->all(), ['uom_default', 'uom_issuing','item_by','issueUnits','delivery_order','sales_quotation', 'issueCostTransTotal', 'issueCostTrans']);
        $input = $this->convertArrayToValue($input);
        $input['customerItemDetailID'] = $id;

        $returnData = CustomerInvoiceAPIService::customerInvoiceItemDetailsUpdate($input);
        if($returnData['status']){
            return $this->sendResponse($returnData['data'],$returnData['message']);
        }
        else{
            return $this->sendError(
                $returnData['message'],
                (isset($returnData['code']) && $returnData['code'] == 500) ? $returnData['code'] : 404,
                isset($returnData['type']) ? $returnData['type'] : array('type' => '')
            );
        }
    }

    public function custItemDetailUpdate($id, UpdateCustomerInvoiceItemDetailsAPIRequest $request){
        $comments = $request->comments;

        $input = array();
        $input['comments'] = $comments;
        $message = "Item updated successfully";

        $customerInvoiceItemDetails = $this->customerInvoiceItemDetailsRepository->update($input, $id);

        return $this->sendResponse($customerInvoiceItemDetails->toArray(), $message);
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/customerInvoiceItemDetails/{id}",
     *      summary="Remove the specified CustomerInvoiceItemDetails from storage",
     *      tags={"CustomerInvoiceItemDetails"},
     *      description="Delete CustomerInvoiceItemDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CustomerInvoiceItemDetails",
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
        /** @var CustomerInvoiceItemDetails $customerInvoiceItemDetails */
        $customerInvoiceItemDetails = $this->customerInvoiceItemDetailsRepository->findWithoutFail($id);

        if (empty($customerInvoiceItemDetails)) {
            return $this->sendError(trans('custom.customer_invoice_item_details_not_found'));
        }

        $customerInvoice = CustomerInvoiceDirect::find($customerInvoiceItemDetails->custInvoiceDirectAutoID);
        if(!empty($customerInvoice)){
            if($customerInvoice->confirmedYN == 1){
                return $this->sendError(trans('custom.invoice_was_already_confirmed_you_cannot_delete'),500);
            }
            $taxExist = Taxdetail::where('documentSystemCode', $customerInvoice->custInvoiceDirectAutoID)
                ->where('documentSystemID', $customerInvoice->documentSystemiD)
                ->exists();
            if($taxExist && $customerInvoice->isPerforma != 4 && $customerInvoice->isPerforma != 5 && $customerInvoice->isPerforma != 3 &&  $customerInvoice->isPerforma != 2){
                return $this->sendError(trans('custom.vat_added_delete_tax'),500);
            }

        }

        if ($customerInvoiceItemDetails->trackingType == 2) {
            $validateSubProductSold = DocumentSubProduct::where('documentSystemID', $customerInvoice->documentSystemiD)
                                                         ->where('documentDetailID', $id)
                                                         ->where('sold', 1)
                                                         ->first();

            if ($validateSubProductSold) {
                return $this->sendError(trans('custom.you_cannot_delete_this_line_item_serial_details_ar'), 422);
            }

            $subProduct = DocumentSubProduct::where('documentSystemID', $customerInvoice->documentSystemiD)
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
        } else if ($customerInvoiceItemDetails->trackingType == 1) {
            $deleteBatch = ItemTracking::revertBatchTrackingSoldStatus($customerInvoice->documentSystemID, $id);

            if (!$deleteBatch['status']) {
                return $this->sendError($deleteBatch['message'], 422);
            }
        }


        $customerInvoiceItemDetails->delete();

        /*for Customer Invoice type -> From Delivery Note*/

        if($customerInvoice->isPerforma == 3){

            if (!empty($customerInvoiceItemDetails->deliveryOrderDetailID) && !empty($customerInvoiceItemDetails->deliveryOrderID)) {
                DeliveryOrder::find($customerInvoiceItemDetails->deliveryOrderID)
                    ->update([
                        'selectedForCustomerInvoice' => 0,
                        'closedYN' => 0
                    ]);


                //checking the fullyOrdered or partial in po
                $detailSum = CustomerInvoiceItemDetails::select(DB::raw('COALESCE(SUM(qtyIssuedDefaultMeasure),0) as totalQty'))
                    ->where('deliveryOrderDetailID', $customerInvoiceItemDetails->deliveryOrderDetailID)
                    ->first();

                $updatedQuoQty = $detailSum['totalQty'];

                if ($updatedQuoQty == 0) {
                    $fullyReceived = 0;
                } else {
                    $fullyReceived = 1;
                }

                $updateDetail = DeliveryOrderDetail::where('deliveryOrderDetailID', $customerInvoiceItemDetails->deliveryOrderDetailID)
                    ->update([ 'fullyReceived' => $fullyReceived, 'invQty' => $updatedQuoQty]);

                $taxDelete = Taxdetail::where('documentSystemCode', $customerInvoiceItemDetails->custInvoiceDirectAutoID)
                                  ->where('documentSystemID', 20)
                                  ->delete();

                $resVat = $this->updateVatFromSalesDeliveryOrder($customerInvoiceItemDetails->custInvoiceDirectAutoID);
                if (!$resVat['status']) {
                   return $this->sendError($resVat['message']); 
                } 

                /*$resVat = $this->updateVatEligibilityOfCustomerInvoiceFromDO($customerInvoiceItemDetails->custInvoiceDirectAutoID);
                if (!$resVat['status']) {
                   return $this->sendError($resVat['message']); 
                } */
            }
            $this->updateDOInvoicedStatus($customerInvoiceItemDetails->deliveryOrderID);

        }elseif ($customerInvoice->isPerforma == 4 || $customerInvoice->isPerforma == 5){    /*for Customer Invoice type -> From Sales Order, Quotation*/
            if (!empty($customerInvoiceItemDetails->quotationMasterID) && !empty($customerInvoiceItemDetails->quotationDetailsID)) {
                QuotationMaster::find($customerInvoiceItemDetails->quotationMasterID)
                    ->update([
                        'selectedForDeliveryOrder' => 0,
                        'closedYN' => 0
                    ]);

                //checking the fullyOrdered or partial in po
                $detailSum = CustomerInvoiceItemDetails::select(DB::raw('COALESCE(SUM(qtyIssuedDefaultMeasure),0) as totalQty'))
                    ->where('quotationDetailsID', $customerInvoiceItemDetails->quotationDetailsID)
                    ->first();

                $updatedQuoQty = $detailSum['totalQty'];

                if ($updatedQuoQty == 0) {
                    $fullyOrdered = 0;
                } else {
                    $fullyOrdered = 1;
                }

                QuotationDetails::where('quotationDetailsID', $customerInvoiceItemDetails->quotationDetailsID)
                    ->update([ 'fullyOrdered' => $fullyOrdered, 'doQuantity' => $updatedQuoQty]);

                $this->updateSalesQuotationInvoicedStatus($customerInvoiceItemDetails->quotationMasterID);
                $taxDelete = Taxdetail::where('documentSystemCode', $customerInvoiceItemDetails->custInvoiceDirectAutoID)
                                  ->where('documentSystemID', 20)
                                  ->delete();

                $resVat = CustomerInvoiceAPIService::updateVatFromSalesQuotation($customerInvoiceItemDetails->custInvoiceDirectAutoID);
                if (!$resVat['status']) {
                   return $this->sendError($resVat['message']); 
                } 

                /*$resVat = $this->updateVatEligibilityOfCustomerInvoice($customerInvoiceItemDetails->custInvoiceDirectAutoID);
                if (!$resVat['status']) {
                   return $this->sendError($resVat['message']); 
                } */
            }

        } else if ($customerInvoice->isPerforma == 2) {
            $resVat = CustomerInvoiceAPIService::updateVatFromSalesQuotation($customerInvoiceItemDetails->custInvoiceDirectAutoID);
            if (!$resVat['status']) {
               return $this->sendError($resVat['message']); 
            } 
        }

        return $this->sendResponse($id, trans('custom.customer_invoice_item_details_deleted_successfully'));
    }

    public function getItemByCustomerInvoiceItemDetail(Request $request)
    {
        $input = $request->all();
        $id = $input['custInvoiceDirectAutoID'];

        $items = CustomerInvoiceItemDetails::where('custInvoiceDirectAutoID', $id)
            ->with(['uom_default', 'uom_issuing','item_by','delivery_order','sales_quotation'])
            ->get();

        foreach ($items as $item) {

            $issueUnit = Unit::all();
            $issueUnits = array();

            if ($issueUnit) {
                foreach ($issueUnit as $unit){
                    $temArray = array('value' => $unit->UnitID, 'label' => $unit->UnitShortCode);
                    array_push($issueUnits,$temArray);
                }
            }
            
            $item->issueUnits = $issueUnits;
        }

        return $this->sendResponse($items->toArray(), trans('custom.item_details_retrieved_successfully'));
    }

    public function getDeliveryTerms(Request $request)
    {
        $items = DeliveryTermsMaster::where('is_deleted', 0)
            ->get();

        return $this->sendResponse($items->toArray(), trans('custom.delivery_terms_retrieved_successfully'));
    }

    public function getDeliveryTermsFormData(Request $request)
    {
        $input = $request->all();
        $id = $input[0];
        $items = CustomerInvoiceLogistic::where('custInvoiceDirectAutoID', $id)->first();

        return $this->sendResponse($items, trans('custom.delivery_terms_retrieved_successfully'));
    }

    public function deliveryOrderForCustomerInvoice(Request $request){
        $input = $request->all();
        $invoice = CustomerInvoiceDirect::find($input['custInvoiceDirectAutoID']);

        $master = DeliveryOrder::where('companySystemID',$input['companySystemID'])
            ->where('approvedYN', -1)
            ->where('selectedForCustomerInvoice', 0)
            ->where('closedYN',0)
            ->where('serviceLineSystemID', $invoice->serviceLineSystemID)
            ->where('wareHouseSystemCode', $invoice->wareHouseSystemCode)
            ->where('customerID', $invoice->customerID)
            ->where('transactionCurrencyID', $invoice->custTransactionCurrencyID)
            ->whereDate("postedDate", '<=', $invoice->bookingDate)
            ->orderBy('deliveryOrderID','DESC')
            ->get();

        return $this->sendResponse($master->toArray(), trans('custom.delivery_order_retrieved_successfully_1'));
    }

    public function getDeliveryOrderDetailForInvoice(Request $request){
        $input = $request->all();
        $id = $input['deliveryOrderID'];

        $detail = DB::select('SELECT
	dodetail.*,
	erp_delivery_order.serviceLineSystemID,
	"" AS isChecked,
	"" AS noQty,
	IFNULL(sum(invdetails.invTakenQty),0) as invTakenQty 
FROM
	erp_delivery_order_detail dodetail
	INNER JOIN erp_delivery_order ON dodetail.deliveryOrderID = erp_delivery_order.deliveryOrderID
	LEFT JOIN ( SELECT erp_customerinvoiceitemdetails.customerItemDetailID,deliveryOrderDetailID, SUM( qtyIssuedDefaultMeasure ) AS invTakenQty FROM erp_customerinvoiceitemdetails GROUP BY customerItemDetailID, itemCodeSystem ) AS invdetails ON dodetail.deliveryOrderDetailID = invdetails.deliveryOrderDetailID 
WHERE
	dodetail.deliveryOrderID = ' . $id . ' 
	AND fullyReceived != 2 
	GROUP BY dodetail.deliveryOrderDetailID');

        return $this->sendResponse($detail, trans('custom.delivery_order_details_retrieved_successfully'));
    }

    public function storeInvoiceDetailFromDeliveryOrder(Request $request){
        
        $input = $request->all();
        $invDetail_arr = array();
        $validator = array();
        $custInvoiceDirectAutoID = $input['custInvoiceDirectAutoID'];

        $isCheckArr = collect($input['detailTable'])->pluck('isChecked')->toArray();
        if (!in_array(true, $isCheckArr)) {
            return $this->sendError(trans('custom.no_items_selected_to_add'));
        }

        $inputDetails = $input['detailTable'];
        $inputDetails = collect($inputDetails)->where('isChecked',1)->toArray();
        $financeCategories = collect($inputDetails)->pluck('itemFinanceCategoryID')->toArray();
        if (count(array_unique($financeCategories)) > 1) {
            return $this->sendError(trans('custom.multiple_finance_category_cannot_be_added_differen_1'),500);
        }

        foreach ($input['detailTable'] as $newValidation) {
            if (($newValidation['isChecked'] && $newValidation['noQty'] == "") || ($newValidation['isChecked'] && $newValidation['noQty'] == 0) || ($newValidation['isChecked'] == '' && $newValidation['noQty'] > 0)) {

                $messages = [
                    'required' => trans('custom.invoice_quantity_required'),
                ];

                $validator = \Validator::make($newValidation, [
                    'noQty' => 'required',
                    'isChecked' => 'required',
                ], $messages);

                if ($validator->fails()) {
                    return $this->sendError($validator->messages(), 422);
                }

                if($newValidation['noQty'] == 0){
                    return $this->sendError(trans('custom.invoice_quantity_greater_than_zero'), 500);
                }

            }
            
            $balanceQty = floatval($newValidation['qtyIssuedDefaultMeasure']) - floatval($newValidation['invTakenQty']) - floatval($newValidation['returnQty']);

            if ($newValidation['noQty'] > $balanceQty) {
                return $this->sendError(trans('custom.invoice_quantity_cannot_be_greater_than_do_balance'), 500);
            }
        }


        $customerInvoioce = CustomerInvoiceDirect::where('custInvoiceDirectAutoID', $custInvoiceDirectAutoID)->first(); 
        $is_pref = $customerInvoioce->isPerforma;

       
        $itemExistArray = array();
        //check added item exist
        foreach ($input['detailTable'] as $itemExist) {
            $item = ItemAssigned::with(['item_master'])
            ->where('itemCodeSystem', $itemExist['itemCodeSystem'])
            ->where('companySystemID', $itemExist['companySystemID'])
            ->first();
            
           

            $item = ItemAssigned::with(['item_master'])
            ->where('itemCodeSystem', $itemExist['itemCodeSystem'])
            ->where('companySystemID', $itemExist['companySystemID'])
            ->first();

            if ($itemExist['isChecked'] && $itemExist['noQty'] > 0) {
                $doDetailExist = CustomerInvoiceItemDetails::select(DB::raw('itemPrimaryCode'))
                    ->where('custInvoiceDirectAutoID', $custInvoiceDirectAutoID)
                    ->where('itemCodeSystem', $itemExist['itemCodeSystem'])
                    ->get();


                if($item->financeCategoryMaster != 2 && $item->financeCategoryMaster != 4 )
                {
                    if (!empty($doDetailExist)) {
                        foreach ($doDetailExist as $row) {
                            $itemDrt = $row['itemPrimaryCode'] . " is already added";
                            $itemExistArray[] = [$itemDrt];
                        }
                    }
                }

          
            }
        }
        

        if (!empty($itemExistArray)) {
            return $this->sendError($itemExistArray, 422);
        }
        

        foreach ($input['detailTable'] as $itemExist) {

            if ($itemExist['isChecked'] && $itemExist['noQty'] > 0) {

                $deliveryOrder = DeliveryOrder::find($itemExist['deliveryOrderID']);

                if($deliveryOrder->serviceLineSystemID != $customerInvoioce->serviceLineSystemID){
//                    return $this->sendError("Segment is different from order");
                }
            }
        }

        // We are not check stock qty. bcz delivery order already made gl and item ledger entry

        DB::beginTransaction();
        try {

            foreach ($input['detailTable'] as $new) {

                $deliveryOrder = DeliveryOrder::find($new['deliveryOrderID']);

                $doDetailExist = CustomerInvoiceItemDetails::select(DB::raw('customerItemDetailID'))
                    ->where('custInvoiceDirectAutoID', $custInvoiceDirectAutoID)
                    ->where('deliveryOrderDetailID', $new['deliveryOrderDetailID'])
                    ->first();

                if (empty($doDetailExist)) {

                    if ($new['isChecked'] && $new['noQty'] > 0) {

                        //checking the fullyOrdered or partial in delivery order
                        $detailSum = CustomerInvoiceItemDetails::select(DB::raw('COALESCE(SUM(qtyIssuedDefaultMeasure),0) as totalNoQty'))
                            ->where('deliveryOrderDetailID', $new['deliveryOrderDetailID'])
                            ->first();

                        $totalAddedQty = $new['noQty'] + $detailSum['totalNoQty'];

                        if ($new['qtyIssuedDefaultMeasure'] == $totalAddedQty) {
                            $fullyReceived = 2;
                            $closedYN = -1;
                            $selectedForCustomerInvoice= -1;
                        } else {
                            $fullyReceived = 1;
                            $closedYN = 0;
                            $selectedForCustomerInvoice = 0;
                        }

                        // checking the qty request is matching with sum total
                        if ($new['qtyIssuedDefaultMeasure'] >= $new['noQty']) {

                            $invDetail_arr['custInvoiceDirectAutoID'] = $custInvoiceDirectAutoID;

                            $invDetail_arr['deliveryOrderID'] = $new['deliveryOrderID'];
                            $invDetail_arr['deliveryOrderDetailID'] = $new['deliveryOrderDetailID'];
                            $invDetail_arr['itemCodeSystem'] = $new['itemCodeSystem'];
                            $invDetail_arr['itemPrimaryCode'] = $new['itemPrimaryCode'];
                            $invDetail_arr['itemDescription'] = $new['itemDescription'];

                            $invDetail_arr['VATPercentage'] = $new['VATPercentage'];
                            $invDetail_arr['VATAmount'] = $new['VATAmount'];
                            $invDetail_arr['VATAmountLocal'] = $new['VATAmountLocal'];
                            $invDetail_arr['VATAmountRpt'] = $new['VATAmountRpt'];
                            $invDetail_arr['VATApplicableOn'] = $new['VATApplicableOn'];
                            $invDetail_arr['vatMasterCategoryID'] = $new['vatMasterCategoryID'];
                            $invDetail_arr['vatSubCategoryID'] = $new['vatSubCategoryID'];

                            $item = ItemMaster::find($new['itemCodeSystem']);
                            if(empty($item)){
                                return $this->sendError(trans('custom.item_not_found'),500);
                            }

                            $data = array(
                                'companySystemID' => $deliveryOrder->companySystemID,
                                'itemCodeSystem' => $new['itemCodeSystem'],
                                'wareHouseId' => $deliveryOrder->wareHouseSystemCode
                            );

                            $itemCurrentCostAndQty = inventory::itemCurrentCostAndQty($data);

                            $invDetail_arr['currentStockQty'] = $itemCurrentCostAndQty['currentStockQty'];
                            $invDetail_arr['currentWareHouseStockQty'] = $itemCurrentCostAndQty['currentWareHouseStockQty'];
                            $invDetail_arr['issueCostLocal'] = $itemCurrentCostAndQty['wacValueLocal'];
                            $invDetail_arr['issueCostRpt'] = $itemCurrentCostAndQty['wacValueReporting'];
                            $invDetail_arr['convertionMeasureVal'] = 1;

                            $invDetail_arr['itemFinanceCategoryID'] = $item->financeCategoryMaster;
                            $invDetail_arr['itemFinanceCategorySubID'] = $item->financeCategorySub;

                            $financeItemCategorySubAssigned = FinanceItemcategorySubAssigned::where('companySystemID', $new['companySystemID'])
                                ->where('mainItemCategoryID', $invDetail_arr['itemFinanceCategoryID'])
                                ->where('itemCategorySubID', $invDetail_arr['itemFinanceCategorySubID'])
                                ->first();

                            if (!empty($financeItemCategorySubAssigned)) {
                                $invDetail_arr['financeGLcodebBS'] = $financeItemCategorySubAssigned->financeGLcodebBS;
                                $invDetail_arr['financeGLcodebBSSystemID'] = $financeItemCategorySubAssigned->financeGLcodebBSSystemID;
                                $invDetail_arr['financeGLcodePL'] = $financeItemCategorySubAssigned->financeGLcodePL;
                                $invDetail_arr['financeGLcodePLSystemID'] = $financeItemCategorySubAssigned->financeGLcodePLSystemID;
                                $invDetail_arr['financeGLcodeRevenueSystemID'] = $financeItemCategorySubAssigned->financeGLcodeRevenueSystemID;
                                $invDetail_arr['financeGLcodeRevenue'] = $financeItemCategorySubAssigned->financeGLcodeRevenue;
                            } else {
                                return $this->sendError(trans('custom.finance_item_category_sub_assigned_not_found'), 500);
//                                return $this->sendError("Account code not updated for ".$new['itemSystemCode'].".", 500);
                            }

                            if((!$invDetail_arr['financeGLcodebBS'] || !$invDetail_arr['financeGLcodebBSSystemID']) && $item->financeCategoryMaster!=2){
                                return $this->sendError(trans('custom.bs_account_cannot_be_null_for') . $new['itemSystemCode'], 500);
                            }elseif (!$invDetail_arr['financeGLcodePL'] || !$invDetail_arr['financeGLcodePLSystemID']){
                                return $this->sendError(trans('custom.cost_account_cannot_be_null_for') . $new['itemSystemCode'], 500);
                            }elseif (!$invDetail_arr['financeGLcodeRevenueSystemID'] || !$invDetail_arr['financeGLcodeRevenue']){
                                return $this->sendError(trans('custom.revenue_account_cannot_be_null_for') . $new['itemSystemCode'], 500);
                            }

                            /*if (!$invDetail_arr['financeGLcodebBS'] || !$invDetail_arr['financeGLcodebBSSystemID']
                                || !$invDetail_arr['financeGLcodePL'] || !$invDetail_arr['financeGLcodePLSystemID']
                                || !$invDetail_arr['financeGLcodeRevenueSystemID'] || !$invDetail_arr['financeGLcodeRevenue']) {
                                return $this->sendError("Account code not updated for ".$new['itemSystemCode'].".", 500);
                            }*/


                            $invDetail_arr['sellingCurrencyID'] = $deliveryOrder->transactionCurrencyID;
                            $invDetail_arr['sellingCurrencyER'] = $deliveryOrder->transactionCurrencyER;
                            $invDetail_arr['localCurrencyID'] = $deliveryOrder->companyLocalCurrencyID;
                            $invDetail_arr['localCurrencyER'] = $deliveryOrder->companyLocalCurrencyER;
                            $invDetail_arr['reportingCurrencyID'] = $deliveryOrder->companyReportingCurrencyID;
                            $invDetail_arr['reportingCurrencyER'] = $deliveryOrder->companyReportingCurrencyER;

                            $invDetail_arr['itemUnitOfMeasure'] = $new['itemUnitOfMeasure'];
                            $invDetail_arr['unitOfMeasureIssued'] = $new['unitOfMeasureIssued'];
                            $invDetail_arr['qtyIssued'] = $new['noQty'];
                            $invDetail_arr['qtyIssuedDefaultMeasure'] = $new['noQty'];

                            $invDetail_arr['marginPercentage'] = 0;
                            if (isset($new['discountPercentage']) && $new['discountPercentage'] != 0){
                                $invDetail_arr['sellingCost'] = ($new['unitTransactionAmount']) - ($new['unitTransactionAmount']*$new['discountPercentage']/100);
                            }else{
                                $invDetail_arr['sellingCost'] = $new['unitTransactionAmount'];
                            }
                            $invDetail_arr['sellingCostAfterMargin'] = $invDetail_arr['sellingCost'];

                            $costs = CustomerInvoiceAPIService::updateCostBySellingCost($invDetail_arr,$customerInvoioce);
                            $invDetail_arr['sellingCostAfterMarginLocal'] = $costs['sellingCostAfterMarginLocal'];
                            $invDetail_arr['sellingCostAfterMarginRpt'] = $costs['sellingCostAfterMarginRpt'];

                            $invDetail_arr['issueCostLocalTotal'] = $invDetail_arr['issueCostLocal'] * $invDetail_arr['qtyIssuedDefaultMeasure'];
                            $invDetail_arr['issueCostRptTotal'] = $invDetail_arr['issueCostRpt'] * $invDetail_arr['qtyIssuedDefaultMeasure'];
                            $invDetail_arr['sellingTotal'] = $invDetail_arr['sellingCostAfterMargin'] * $invDetail_arr['qtyIssuedDefaultMeasure'];

                            $invDetail_arr['issueCostLocal'] = Helper::roundValue($invDetail_arr['issueCostLocal']);
                            $invDetail_arr['issueCostLocalTotal'] = Helper::roundValue($invDetail_arr['issueCostLocalTotal']);
                            $invDetail_arr['issueCostRpt'] = Helper::roundValue($invDetail_arr['issueCostRpt']);
                            $invDetail_arr['issueCostRptTotal'] = Helper::roundValue($invDetail_arr['issueCostRptTotal']);
                            $invDetail_arr['sellingCost'] = Helper::roundValue($invDetail_arr['sellingCost']);
                            $invDetail_arr['sellingCostAfterMargin'] = Helper::roundValue($invDetail_arr['sellingCostAfterMargin']);
                            $invDetail_arr['sellingTotal'] = Helper::roundValue($invDetail_arr['sellingTotal']);
                            $invDetail_arr['sellingCostAfterMarginLocal'] = Helper::roundValue($invDetail_arr['sellingCostAfterMarginLocal']);
                            $invDetail_arr['sellingCostAfterMarginRpt'] = Helper::roundValue($invDetail_arr['sellingCostAfterMarginRpt']);

                            $item = $this->customerInvoiceItemDetailsRepository->create($invDetail_arr);

                            $update = DeliveryOrderDetail::where('deliveryOrderDetailID', $new['deliveryOrderDetailID'])
                                ->update(['fullyReceived' => $fullyReceived, 'invQty' => $totalAddedQty]);
                        }

                        // fetching the total count records from purchase Request Details table
                        $doDetailTotalcount = DeliveryOrderDetail::select(DB::raw('count(deliveryOrderDetailID) as detailCount'))
                            ->where('deliveryOrderID', $new['deliveryOrderID'])
                            ->first();

                        // fetching the total count records from purchase Request Details table where fullyOrdered = 2
                        $doDetailExist = DeliveryOrderDetail::select(DB::raw('count(deliveryOrderDetailID) as count'))
                            ->where('deliveryOrderID', $new['deliveryOrderID'])
                            ->where('fullyReceived', 2)
//                        ->where('selectedForPO', -1)
                            ->first();

                        // Updating PR Master Table After All Detail Table records updated
                        if ($doDetailTotalcount['detailCount'] == $doDetailExist['count']) {
                            $updatedo = DeliveryOrder::find($new['deliveryOrderID'])
                                ->update(['selectedForCustomerInvoice' => -1, 'closedYN' => -1]);
                        }
                    }
                }

                //check all details fullyOrdered in DO Master
                $doMasterfullyOrdered = DeliveryOrderDetail::where('deliveryOrderID', $new['deliveryOrderID'])
                    ->whereIn('fullyReceived', [1, 0])
                    ->get()->toArray();

                if (empty($doMasterfullyOrdered)) {
                    DeliveryOrder::find($new['deliveryOrderID'])
                        ->update([
                            'selectedForCustomerInvoice' => -1,
                            'closedYN' => -1,
                        ]);
                } else {
                    DeliveryOrder::find($new['deliveryOrderID'])
                        ->update([
                            'selectedForCustomerInvoice' => 0,
                            'closedYN' => 0,
                        ]);
                }

                $this->updateDOInvoicedStatus($new['deliveryOrderID']);

            }

            $resVat = $this->updateVatFromSalesDeliveryOrder($custInvoiceDirectAutoID);
            if (!$resVat['status']) {
               return $this->sendError($resVat['message']); 
            } 

            /*$resVat = $this->updateVatEligibilityOfCustomerInvoiceFromDO($custInvoiceDirectAutoID);
            if (!$resVat['status']) {
               return $this->sendError($resVat['message']); 
            } */

            DB::commit();
            return $this->sendResponse([], trans('custom.customer_invoice_item_details_saved_successfully'));
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError(trans('custom.error_occurred'). $exception->getMessage() . 'Line :' . $exception->getLine());
        }
        
    }

    public function getDeliveryOrderRecord(Request $request){

        $input = $request->all();
        $id = $input['deliveryOrderID'];
        $companySystemID = $input['companySystemID'];
        $deliveryOrder = DeliveryOrder::with(['company','customer','transaction_currency', 'tax','sales_person','detail' => function($query){
            $query->with(['quotation','uom_default','uom_issuing','item_by']);
        },'approved_by' => function($query) use($companySystemID){
            $query->where('companySystemID',$companySystemID)
                ->where('documentSystemID',71)
            ->with(['employee']);
        }])->find($id);

        if (empty($deliveryOrder)) {
            return $this->sendError(trans('custom.delivery_order_not_found'));
        }

        return $this->sendResponse($deliveryOrder->toArray(), trans('custom.delivery_order_retrieved_successfully'));
    }

    private function updateDOInvoicedStatus($deliveryOrderID){

        $status = 0;
        $invQty = CustomerInvoiceItemDetails::where('deliveryOrderID',$deliveryOrderID)->sum('qtyIssuedDefaultMeasure');

        if($invQty!=0) {
            $doQty = DeliveryOrderDetail::where('deliveryOrderID',$deliveryOrderID)->sum('qtyIssuedDefaultMeasure');
            if($invQty == $doQty){
                $status = 2;    // fully invoiced
            }else{
                $status = 1;    // partially invoiced
            }
        }
        return DeliveryOrder::where('deliveryOrderID',$deliveryOrderID)->update(['invoiceStatus'=>$status]);

    }

    public function storeInvoiceDetailFromSalesQuotation(Request $request){

        $input = $request->all();
        $invDetail_arr = array();
        $custInvoiceDirectAutoID = $input['custInvoiceDirectAutoID'];
        $isCheckArr = collect($input['detailTable'])->pluck('isChecked')->toArray();
        if (!in_array(true, $isCheckArr)) {
            return $this->sendError(trans('custom.no_items_selected_to_add'));
        }

        $inputDetails = $input['detailTable'];
        $inputDetails = collect($inputDetails)->where('isChecked',1)->toArray();
        $financeCategories = collect($inputDetails)->pluck('itemCategory')->toArray();
        if (count(array_unique($financeCategories)) > 1) {
            return $this->sendError(trans('custom.multiple_finance_category_cannot_be_added_differen_1'),500);
        }

        foreach ($input['detailTable'] as $newValidation) {
            if (($newValidation['isChecked'] && $newValidation['noQty'] == "") || ($newValidation['isChecked'] && $newValidation['noQty'] == 0) || ($newValidation['isChecked'] == '' && $newValidation['noQty'] > 0)) {

                $messages = [
                    'required' => trans('custom.invoice_quantity_required'),
                ];

                $validator = \Validator::make($newValidation, [
                    'noQty' => 'required',
                    'isChecked' => 'required',
                ], $messages);

                if ($validator->fails()) {
                    return $this->sendError($validator->messages(), 422);
                }

                if($newValidation['noQty'] == 0){
                    return $this->sendError(trans('custom.invoice_quantity_greater_than_zero'), 500);
                }
            }
        }

        $customerInvoioce = CustomerInvoiceDirect::where('custInvoiceDirectAutoID', $custInvoiceDirectAutoID)->first(); 
        $is_pref = $customerInvoioce->isPerforma;
   

        $itemExistArray = array();
        //check added item exist
        foreach ($input['detailTable'] as $itemExist) {


             $item = ItemAssigned::with(['item_master'])
            ->where('itemCodeSystem', $itemExist['itemAutoID'])
            ->where('companySystemID', $itemExist['companySystemID'])
            ->first();


            if ($itemExist['isChecked'] && $itemExist['noQty'] > 0) {
                $doDetailExist = CustomerInvoiceItemDetails::select(DB::raw('itemPrimaryCode'))
                    ->where('custInvoiceDirectAutoID', $custInvoiceDirectAutoID)
                    ->where('itemCodeSystem', $itemExist['itemAutoID'])
                    ->get();

                    if(isset($item->financeCategoryMaster) && $item->financeCategoryMaster != 2 && $item->financeCategoryMaster != 4 )
                    {
                        if (!empty($doDetailExist)) {
                            foreach ($doDetailExist as $row) {
                                $itemDrt = $row['itemPrimaryCode'] . " is already added";
                                $itemExistArray[] = [$itemDrt];
                            }
                        }
                    }
            }
        }

        if (!empty($itemExistArray)) {
            return $this->sendError($itemExistArray, 422);
        }

    
       

        // check qty and validations

        foreach ($input['detailTable'] as $row) {

            if ($row['isChecked'] && $row['noQty'] > 0) {

                if($row['itemCategory'] == 1){
                    $data = array(
                        'companySystemID' => $customerInvoioce->companySystemID,
                        'itemCodeSystem' => $row['itemAutoID'],
                        'wareHouseId' => $customerInvoioce->wareHouseSystemCode
                    );

                    $itemCurrentCostAndQty = inventory::itemCurrentCostAndQty($data);
                    $currentStockQty = $itemCurrentCostAndQty['currentStockQty'];
                    $currentWareHouseStockQty = $itemCurrentCostAndQty['currentWareHouseStockQty'];
                    $wacValueLocal = $itemCurrentCostAndQty['wacValueLocal'];
                    $wacValueReporting = $itemCurrentCostAndQty['wacValueReporting'];

                    if ($currentStockQty <= 0) {
                        return $this->sendError(trans('custom.stock_qty_is_0_for_item', ['itemCode' => $row['itemSystemCode']]), 500);
                    }

                    if ($currentWareHouseStockQty <= 0) {
                        return $this->sendError(trans('custom.warehouse_stock_qty_is_0_for_item', ['itemCode' => $row['itemSystemCode']]), 500);
                    }

                    if ($wacValueLocal == 0 || $wacValueReporting == 0) {
                        return $this->sendError(trans('custom.wac_cost_zero_cannot_issue', ['itemCode' => $row['itemSystemCode']]), 500);
                    }

                    if ($wacValueLocal < 0 || $wacValueReporting < 0) {
                        return $this->sendError(trans('custom.wac_cost_negative_cannot_issue', ['itemCode' => $row['itemSystemCode']]), 500);
                    }

                    if ($row['noQty'] > $currentStockQty) {
                        return $this->sendError(trans('custom.insufficient_stock_qty_for_item', ['itemCode' => $row['itemSystemCode']]), 500);
                    }

                    if ($row['noQty'] > $currentWareHouseStockQty) {
                        return $this->sendError(trans('custom.insufficient_warehouse_qty_for_item', ['itemCode' => $row['itemSystemCode']]), 500);
                    }

                    /*pending approval checking*/
                    // check the item pending pending for approval in other delivery orders

                    $checkWhether = DeliveryOrder::where('companySystemID', $row['companySystemID'])
                        ->select([
                            'erp_delivery_order.deliveryOrderID',
                            'erp_delivery_order.deliveryOrderCode'
                        ])
                        ->groupBy(
                            'erp_delivery_order.deliveryOrderID',
                            'erp_delivery_order.companySystemID'
                        )
                        ->whereHas('detail', function ($query) use ($row) {
                            $query->where('itemCodeSystem', $row['itemAutoID']);
                        })
                        ->where('approvedYN', 0)
                        ->first();

                    if (!empty($checkWhether)) {
                        return $this->sendError(trans('custom.delivery_order_pending_approval_for_item_with_code', ['orderCode' => $checkWhether->deliveryOrderCode, 'itemCode' => $row['itemSystemCode']]), 500);
                    }


                    // check the item pending pending for approval in other modules
                    $checkWhetherItemIssueMaster = ItemIssueMaster::where('companySystemID', $row['companySystemID'])
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
                        ->whereHas('details', function ($query) use ($row) {
                            $query->where('itemCodeSystem', $row['itemAutoID']);
                        })
                        ->where('approved', 0)
                        ->first();
                    /* approved=0*/

                    if (!empty($checkWhetherItemIssueMaster)) {
                        return $this->sendError(trans('custom.material_issue_pending_approval_for_item_with_code', ['issueCode' => $checkWhetherItemIssueMaster->itemIssueCode, 'itemCode' => $row['itemSystemCode']]), 500);
                    }

                    $checkWhetherStockTransfer = StockTransfer::where('companySystemID', $row['companySystemID'])
//            ->where('locationFrom', $customerInvoiceDirect->wareHouseSystemCode)
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
                        ->whereHas('details', function ($query) use ($row) {
                            $query->where('itemCodeSystem', $row['itemAutoID']);
                        })
                        ->where('approved', 0)
                        ->first();
                    /* approved=0*/

                    if (!empty($checkWhetherStockTransfer)) {
                        return $this->sendError(trans('custom.stock_transfer_pending_approval_for_item_with_code', ['transferCode' => $checkWhetherStockTransfer->stockTransferCode, 'itemCode' => $row['itemSystemCode']]), 500);
                    }

                    /*Check in purchase return*/
                    $checkWhetherPR = PurchaseReturn::where('companySystemID', $row['companySystemID'])
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
                        ->whereHas('details', function ($query) use ($row) {
                            $query->where('itemCode', $row['itemAutoID']);
                        })
                        ->where('approved', 0)
                        ->first();
                    /* approved=0*/

                    if (!empty($checkWhetherPR)) {
                        return $this->sendError(trans('custom.purchase_return_pending_approval_for_item_with_code', ['returnCode' => $checkWhetherPR->purchaseReturnCode]), 500);
                    }

                    $checkWhetherInvoice = CustomerInvoiceDirect::where('custInvoiceDirectAutoID', '!=', $customerInvoioce->custInvoiceDirectAutoID)
                        ->where('companySystemID', $row['companySystemID'])
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
                        ->whereHas('issue_item_details', function ($query) use ($row) {
                            $query->where('itemCodeSystem', $row['itemAutoID']);
                        })
                        ->where('approved', 0)
                        ->where('canceledYN', 0)
                        ->first();
                    /* approved=0*/

                    if (!empty($checkWhetherInvoice)) {
                        return $this->sendError(trans('custom.customer_invoice_pending_approval_for_item_with_code', ['invoiceCode' => $checkWhetherInvoice->bookingInvCode, 'itemCode' => $row['itemSystemCode']]), 500);
                    }

                }

            }
        }



        DB::beginTransaction();
        try {

            foreach ($input['detailTable'] as $new) {

                $quotationMaster = QuotationMaster::find($new['quotationMasterID']);

                $quotationDetailExist = CustomerInvoiceItemDetails::select(DB::raw('customerItemDetailID'))
                    ->where('custInvoiceDirectAutoID', $custInvoiceDirectAutoID)
                    ->where('quotationDetailsID', $new['quotationDetailsID'])
                    ->first();

                if (empty($quotationDetailExist)) {

                    if ($new['isChecked'] && $new['noQty'] > 0) {

                        //checking the fullyOrdered or partial in delivery order
                        $detailSum = CustomerInvoiceItemDetails::select(DB::raw('COALESCE(SUM(qtyIssuedDefaultMeasure),0) as totalNoQty'))
                            ->where('quotationDetailsID', $new['quotationDetailsID'])
                            ->first();

                        $totalAddedQty = $new['noQty'] + $detailSum['totalNoQty'];

                        if ($new['requestedQty'] == $totalAddedQty) {
                            $fullyOrdered = 2;
                        } else {
                            $fullyOrdered = 1;
                        }

                        // checking the qty request is matching with sum total
                        if ($new['requestedQty'] >= $new['noQty']) {

                            $invDetail_arr['custInvoiceDirectAutoID'] = $custInvoiceDirectAutoID;

                            $invDetail_arr['quotationMasterID'] = $new['quotationMasterID'];
                            $invDetail_arr['quotationDetailsID'] = $new['quotationDetailsID'];
                            $invDetail_arr['itemCodeSystem'] = $new['itemAutoID'];
                            $invDetail_arr['itemPrimaryCode'] = $new['itemSystemCode'];
                            $invDetail_arr['itemDescription'] = $new['itemDescription'];
                            $invDetail_arr['salesPrice'] = $new['unittransactionAmount'];
                            $invDetail_arr['sellingCost'] = ($new['unittransactionAmount'] - $new['discountAmount']);
                            $invDetail_arr['discountAmount'] = $new['discountAmount'];
                            $invDetail_arr['discountPercentage'] = $new['discountPercentage'];
                            if ($quotationMaster->documentSystemID == 67) {
                                $vatDetails = TaxService::getVATDetailsByItem($customerInvoioce->companySystemID, $new['itemAutoID'], $customerInvoioce->customerID,0);
                                $invDetail_arr['VATApplicableOn'] = $vatDetails['applicableOn'];
                                $invDetail_arr['vatMasterCategoryID'] = $vatDetails['vatMasterCategoryID'];
                                $invDetail_arr['vatSubCategoryID'] = $vatDetails['vatSubCategoryID'];
                                $invDetail_arr['VATPercentage'] = $vatDetails['percentage'];
                                $invDetail_arr['VATAmount'] = 0;

                                $unitCostForCalculation = ($vatDetails['applicableOn'] == 1) ? $invDetail_arr['salesPrice'] : $invDetail_arr['sellingCost'];
                                if ($unitCostForCalculation > 0) {
                                    $invDetail_arr['VATAmount'] = (($unitCostForCalculation / 100) * $vatDetails['percentage']);
                                }
                                $currencyConversionVAT = \Helper::currencyConversion($customerInvoioce->companySystemID, $customerInvoioce->custTransactionCurrencyID, $customerInvoioce->custTransactionCurrencyID, $invDetail_arr['VATAmount']);

                                $invDetail_arr['VATAmountLocal'] = \Helper::roundValue($currencyConversionVAT['localAmount']);
                                $invDetail_arr['VATAmountRpt'] = \Helper::roundValue($currencyConversionVAT['reportingAmount']);
                            } else {
                                $invDetail_arr['VATPercentage'] = $new['VATPercentage'];
                                $invDetail_arr['VATAmount'] = $new['VATAmount'];
                                $invDetail_arr['VATAmountLocal'] = $new['VATAmountLocal'];
                                $invDetail_arr['VATAmountRpt'] = $new['VATAmountRpt'];
                                $invDetail_arr['VATApplicableOn'] = $new['VATApplicableOn'];
                                $invDetail_arr['vatMasterCategoryID'] = $new['vatMasterCategoryID'];
                                $invDetail_arr['vatSubCategoryID'] = $new['vatSubCategoryID'];
                            }

                            $item = ItemMaster::find($new['itemAutoID']);
                            if(empty($item)){
                                return $this->sendError(trans('custom.item_not_found'),500);
                            }

                            $data = array(
                                'companySystemID' => $customerInvoioce->companySystemID,
                                'itemCodeSystem' => $new['itemAutoID'],
                                'wareHouseId' => $customerInvoioce->wareHouseSystemCode
                            );

                            $itemCurrentCostAndQty = inventory::itemCurrentCostAndQty($data);

                            $invDetail_arr['currentStockQty'] = $itemCurrentCostAndQty['currentStockQty'];
                            $invDetail_arr['currentWareHouseStockQty'] = $itemCurrentCostAndQty['currentWareHouseStockQty'];
                            $invDetail_arr['issueCostLocal'] = $itemCurrentCostAndQty['wacValueLocal'];
                            $invDetail_arr['issueCostRpt'] = $itemCurrentCostAndQty['wacValueReporting'];
                            $invDetail_arr['convertionMeasureVal'] = 1;

                            $invDetail_arr['itemFinanceCategoryID'] = $item->financeCategoryMaster;
                            $invDetail_arr['itemFinanceCategorySubID'] = $item->financeCategorySub;

                            $financeItemCategorySubAssigned = FinanceItemcategorySubAssigned::where('companySystemID', $new['companySystemID'])
                                ->where('mainItemCategoryID', $invDetail_arr['itemFinanceCategoryID'])
                                ->where('itemCategorySubID', $invDetail_arr['itemFinanceCategorySubID'])
                                ->first();

                            if (!empty($financeItemCategorySubAssigned)) {
                                $invDetail_arr['financeGLcodebBS'] = $financeItemCategorySubAssigned->financeGLcodebBS;
                                $invDetail_arr['financeGLcodebBSSystemID'] = $financeItemCategorySubAssigned->financeGLcodebBSSystemID;
                                $invDetail_arr['financeGLcodePL'] = $financeItemCategorySubAssigned->financeGLcodePL;
                                $invDetail_arr['financeGLcodePLSystemID'] = $financeItemCategorySubAssigned->financeGLcodePLSystemID;
                                $invDetail_arr['financeCogsGLcodePLSystemID'] = $financeItemCategorySubAssigned->financeCogsGLcodePLSystemID;
                                $invDetail_arr['financeCogsGLcodePL'] = $financeItemCategorySubAssigned->financeCogsGLcodePL;
                                $invDetail_arr['financeGLcodeRevenueSystemID'] = $financeItemCategorySubAssigned->financeGLcodeRevenueSystemID;
                                $invDetail_arr['financeGLcodeRevenue'] = $financeItemCategorySubAssigned->financeGLcodeRevenue;
                            } else {
                                return $this->sendError(trans('custom.finance_item_category_sub_assigned_not_found'), 500);
                            }

                            if((!$invDetail_arr['financeGLcodebBS'] || !$invDetail_arr['financeGLcodebBSSystemID']) && $item->financeCategoryMaster!=2){
                                return $this->sendError(trans('custom.bs_account_cannot_be_null_for') . $new['itemSystemCode'], 500);
                            }elseif (!$invDetail_arr['financeGLcodePL'] || !$invDetail_arr['financeGLcodePLSystemID']){
                                return $this->sendError(trans('custom.cost_account_cannot_be_null_for') . $new['itemSystemCode'], 500);
                            }elseif (!$invDetail_arr['financeCogsGLcodePL'] || !$invDetail_arr['financeCogsGLcodePLSystemID']){
                                return $this->sendError(trans('custom.cogs_gl_account_cannot_be_null_for_1') . $new['itemSystemCode'], 500);
                            }elseif (!$invDetail_arr['financeGLcodeRevenueSystemID'] || !$invDetail_arr['financeGLcodeRevenue']){
                                return $this->sendError(trans('custom.revenue_account_cannot_be_null_for') . $new['itemSystemCode'], 500);
                            }

                            /*if (!$invDetail_arr['financeGLcodebBS'] || !$invDetail_arr['financeGLcodebBSSystemID']
                                || !$invDetail_arr['financeGLcodePL'] || !$invDetail_arr['financeGLcodePLSystemID']
                                || !$invDetail_arr['financeGLcodeRevenueSystemID'] || !$invDetail_arr['financeGLcodeRevenue']) {
                                return $this->sendError("Account code not updated for ".$new['itemSystemCode'].".", 500);
                            }*/


                            $invDetail_arr['sellingCurrencyID'] = $quotationMaster->transactionCurrencyID;
                            $invDetail_arr['sellingCurrencyER'] = $quotationMaster->transactionExchangeRate;
                            $invDetail_arr['localCurrencyID'] = $quotationMaster->companyLocalCurrencyID;
                            $invDetail_arr['localCurrencyER'] = $quotationMaster->companyLocalExchangeRate;
                            $invDetail_arr['reportingCurrencyID'] = $quotationMaster->companyReportingCurrencyID;
                            $invDetail_arr['reportingCurrencyER'] = $quotationMaster->companyReportingExchangeRate;
                            $invDetail_arr['part_no'] = $item->secondaryItemCode;


                            $invDetail_arr['itemUnitOfMeasure'] = $new['unitOfMeasureID'];
                            $invDetail_arr['unitOfMeasureIssued'] = $new['unitOfMeasureID'];
                            $invDetail_arr['qtyIssued'] = $new['noQty'];
                            $invDetail_arr['qtyIssuedDefaultMeasure'] = $new['noQty'];

                            $invDetail_arr['marginPercentage'] = 0;
                            /*if (isset($new['discountPercentage']) && $new['discountPercentage'] != 0){
                                $invDetail_arr['sellingCost'] = ($new['unittransactionAmount']) - ($new['unittransactionAmount']*$new['discountPercentage']/100);
                            }else{
                                $invDetail_arr['sellingCost'] = $new['unittransactionAmount'];
                            }*/

                            
                            $invDetail_arr['sellingCostAfterMargin'] = $invDetail_arr['sellingCost'];

                            $costs = CustomerInvoiceAPIService::updateCostBySellingCost($invDetail_arr,$customerInvoioce);
                            $invDetail_arr['sellingCostAfterMarginLocal'] = $costs['sellingCostAfterMarginLocal'];
                            $invDetail_arr['sellingCostAfterMarginRpt'] = $costs['sellingCostAfterMarginRpt'];

                            $invDetail_arr['issueCostLocalTotal'] = $invDetail_arr['issueCostLocal'] * $invDetail_arr['qtyIssuedDefaultMeasure'];
                            $invDetail_arr['issueCostRptTotal'] = $invDetail_arr['issueCostRpt'] * $invDetail_arr['qtyIssuedDefaultMeasure'];
                            $invDetail_arr['sellingTotal'] = $invDetail_arr['sellingCostAfterMargin'] * $invDetail_arr['qtyIssuedDefaultMeasure'];

                            $invDetail_arr['issueCostLocal'] = Helper::roundValue($invDetail_arr['issueCostLocal']);
                            $invDetail_arr['issueCostLocalTotal'] = Helper::roundValue($invDetail_arr['issueCostLocalTotal']);
                            $invDetail_arr['issueCostRpt'] = Helper::roundValue($invDetail_arr['issueCostRpt']);
                            $invDetail_arr['issueCostRptTotal'] = Helper::roundValue($invDetail_arr['issueCostRptTotal']);
                            $invDetail_arr['sellingCost'] = Helper::roundValue($invDetail_arr['sellingCost']);
                            $invDetail_arr['sellingCostAfterMargin'] = Helper::roundValue($invDetail_arr['sellingCostAfterMargin']);
                            $invDetail_arr['sellingTotal'] = Helper::roundValue($invDetail_arr['sellingTotal']);
                            $invDetail_arr['sellingCostAfterMarginLocal'] = Helper::roundValue($invDetail_arr['sellingCostAfterMarginLocal']);
                            $invDetail_arr['sellingCostAfterMarginRpt'] = Helper::roundValue($invDetail_arr['sellingCostAfterMarginRpt']);

                            $this->customerInvoiceItemDetailsRepository->create($invDetail_arr);

                            QuotationDetails::where('quotationDetailsID', $new['quotationDetailsID'])
                                ->update(['fullyOrdered' => $fullyOrdered, 'doQuantity' => $totalAddedQty]);
                        }

                    }
                }

                //check all details fullyOrdered in Quotation Master
                $QuotationMasterfullyOrdered = QuotationDetails::where('quotationMasterID', $new['quotationMasterID'])
                    ->whereIn('fullyOrdered', [1, 0])
                    ->get()->toArray();

                if (empty($QuotationMasterfullyOrdered)) {
                    QuotationMaster::find($new['quotationMasterID'])
                        ->update([
                            'selectedForDeliveryOrder' => -1,
                            'closedYN' => -1,
                        ]);
                } else {
                    QuotationMaster::find($new['quotationMasterID'])
                        ->update([
                            'selectedForDeliveryOrder' => 0,
                            'closedYN' => 0,
                        ]);
                }

                $this->updateSalesQuotationInvoicedStatus($new['quotationMasterID']);

            }

            $resVat = CustomerInvoiceAPIService::updateVatFromSalesQuotation($custInvoiceDirectAutoID);
            if (!$resVat['status']) {
               return $this->sendError($resVat['message']); 
            } 

            /*$resVat = $this->updateVatEligibilityOfCustomerInvoice($custInvoiceDirectAutoID);
            if (!$resVat['status']) {
               return $this->sendError($resVat['message']); 
            }*/

            DB::commit();
            return $this->sendResponse([], trans('custom.customer_invoice_item_details_saved_successfully'));
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError(trans('custom.error_occurred'). $exception->getMessage() . 'Line :' . $exception->getLine());
        }

    }

    public function updateVatEligibilityOfCustomerInvoice($custInvoiceDirectAutoID)
    { 
        $doDetailData = CustomerInvoiceItemDetails::where('custInvoiceDirectAutoID', $custInvoiceDirectAutoID)
                                           ->groupBy('quotationMasterID')
                                           ->get();

        $quMasterIds = $doDetailData->pluck('quotationMasterID');

        $quotaionVatEligibleCheck = QuotationMaster::whereIn('quotationMasterID', $quMasterIds)
                                                   ->where('vatRegisteredYN', 1)
                                                   ->where('customerVATEligible', 1)
                                                   ->first();
        $vatRegisteredYN = 0;
        $customerVATEligible = 0;
        if ($quotaionVatEligibleCheck) {
            $customerVATEligible = 1;
            $vatRegisteredYN = 1;
        } 

        $updateRes = CustomerInvoiceDirect::where('custInvoiceDirectAutoID', $custInvoiceDirectAutoID)
                                  ->update(['vatRegisteredYN' => $vatRegisteredYN, 'customerVATEligible' => $customerVATEligible]);

        return ['status' => true];
    }

    public function updateVatEligibilityOfCustomerInvoiceFromDO($custInvoiceDirectAutoID)
    { 
        $doDetailData = CustomerInvoiceItemDetails::where('custInvoiceDirectAutoID', $custInvoiceDirectAutoID)
                                           ->groupBy('quotationMasterID')
                                           ->get();

        $quMasterIds = $doDetailData->pluck('deliveryOrderID');

        $quotaionVatEligibleCheck = DeliveryOrder::whereIn('deliveryOrderID', $quMasterIds)
                                                   ->where('vatRegisteredYN', 1)
                                                   ->where('customerVATEligible', 1)
                                                   ->first();
        $vatRegisteredYN = 0;
        $customerVATEligible = 0;
        if ($quotaionVatEligibleCheck) {
            $customerVATEligible = 1;
            $vatRegisteredYN = 1;
        } 

        $updateRes = CustomerInvoiceDirect::where('custInvoiceDirectAutoID', $custInvoiceDirectAutoID)
                                  ->update(['vatRegisteredYN' => $vatRegisteredYN, 'customerVATEligible' => $customerVATEligible]);

        return ['status' => true];
    }

    public function updateVatFromSalesDeliveryOrder($custInvoiceDirectAutoID)
    {
        $invoiceDetails = CustomerInvoiceItemDetails::where('custInvoiceDirectAutoID', $custInvoiceDirectAutoID)
                                                    ->with(['delivery_order_detail'])
                                                    ->get();

        $totalVATAmount = 0;
        foreach ($invoiceDetails as $key => $value) {
            $totalVATAmount += $value->qtyIssued * (isset($value->delivery_order_detail->VATAmount) ? $value->delivery_order_detail->VATAmount : 0);
        }

        $taxDelete = Taxdetail::where('documentSystemCode', $custInvoiceDirectAutoID)
                              ->where('documentSystemID', 20)
                              ->delete();
        if ($totalVATAmount > 0) {
            $res = CustomerInvoiceAPIService::savecustomerInvoiceItemTaxDetails($custInvoiceDirectAutoID, $totalVATAmount);

            if (!$res['status']) {
               return ['status' => false, 'message' => $res['message']]; 
            } 
        } else {
            $vatAmount['vatOutputGLCodeSystemID'] = null;
            $vatAmount['vatOutputGLCode'] = null;
            $vatAmount['VATPercentage'] = 0;
            $vatAmount['VATAmount'] = 0;
            $vatAmount['VATAmountLocal'] = 0;
            $vatAmount['VATAmountRpt'] = 0;

            CustomerInvoiceDirect::where('custInvoiceDirectAutoID', $custInvoiceDirectAutoID)->update($vatAmount);
        }

        return ['status' => true];
    }

    private function updateSalesQuotationInvoicedStatus($quotationMasterID){

        $status = 0;
        $isInDO = 0;
        $invQty = CustomerInvoiceItemDetails::where('quotationMasterID',$quotationMasterID)->sum('qtyIssuedDefaultMeasure');

        if($invQty!=0) {
            $quotationQty = QuotationDetails::where('quotationMasterID',$quotationMasterID)->sum('requestedQty');
            if($invQty == $quotationQty){
                $status = 2;    // fully invoiced
            }else{
                $status = 1;    // partially invoiced
            }
            $isInDO = 2;
        }
        return QuotationMaster::where('quotationMasterID',$quotationMasterID)->update(['invoiceStatus'=>$status,'isInDOorCI'=>$isInDO]);

    }

    public function validateCustomerInvoiceDetails(Request $request) {
        $rows = $request['detailTable'];
            foreach($rows[0] as $row) {
                        /*pending approval checking*/
                        // check the item pending pending for approval in other delivery orders

                        $checkWhether = DeliveryOrder::where('companySystemID', $row['companySystemID'])
                            ->select([
                                'erp_delivery_order.deliveryOrderID',
                                'erp_delivery_order.deliveryOrderCode'
                            ])
                            ->groupBy(
                                'erp_delivery_order.deliveryOrderID',
                                'erp_delivery_order.companySystemID'
                            )
                            ->whereHas('detail', function ($query) use ($row) {
                                $query->where('itemCodeSystem', $row['itemAutoID']);
                            })
                            ->where('approvedYN', 0)
                            ->first();

                        if (!empty($checkWhether)) {
                            return $this->sendError("There is a Delivery Order (" . $checkWhether->deliveryOrderCode . ") pending for approval for ".$row['itemSystemCode'].". Please check again.", 500);
                        }


                        // check the item pending pending for approval in other modules
                        $checkWhetherItemIssueMaster = ItemIssueMaster::where('companySystemID', $row['companySystemID'])
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
                            ->whereHas('details', function ($query) use ($row) {
                                $query->where('itemCodeSystem', $row['itemAutoID']);
                            })
                            ->where('approved', 0)
                            ->first();
                        /* approved=0*/

                        if (!empty($checkWhetherItemIssueMaster)) {
                            return $this->sendError(trans('custom.material_issue_pending_approval_for_item_with_code', ['issueCode' => $checkWhetherItemIssueMaster->itemIssueCode, 'itemCode' => $row['itemSystemCode']]), 500);
                        }

                        $checkWhetherStockTransfer = StockTransfer::where('companySystemID', $row['companySystemID'])
                        //            ->where('locationFrom', $customerInvoiceDirect->wareHouseSystemCode)
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
                            ->whereHas('details', function ($query) use ($row) {
                                $query->where('itemCodeSystem', $row['itemAutoID']);
                            })
                            ->where('approved', 0)
                            ->first();
                        /* approved=0*/

                        if (!empty($checkWhetherStockTransfer)) {
                            return $this->sendError(trans('custom.stock_transfer_pending_approval_for_item_with_code', ['transferCode' => $checkWhetherStockTransfer->stockTransferCode, 'itemCode' => $row['itemSystemCode']]), 500);
                        }

                        /*Check in purchase return*/
                        $checkWhetherPR = PurchaseReturn::where('companySystemID', $row['companySystemID'])
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
                            ->whereHas('details', function ($query) use ($row) {
                                $query->where('itemCode', $row['itemAutoID']);
                            })
                            ->where('approved', 0)
                            ->first();
                        /* approved=0*/

                        if (!empty($checkWhetherPR)) {
                            return $this->sendError(trans('custom.purchase_return_pending_approval_for_item_with_code', ['returnCode' => $checkWhetherPR->purchaseReturnCode]), 500);
                        }

                        // check policy 18

                        $allowPendingApproval = CompanyPolicyMaster::where('companyPolicyCategoryID', 18)
                            ->where('companySystemID', $row['companySystemID'])
                            ->first();
                        $item = ItemMaster::find($row['itemAutoID']);
                        if($item->financeCategoryMaster == 1){
                            $checkWhether = CustomerInvoiceDirect::where('companySystemID', $row['companySystemID'])
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
                                ->whereHas('issue_item_details', function ($query) use ($row) {
                                    $query->where('itemCodeSystem', $row['itemAutoID']);
                                })
                                ->where('approved', 0)
                                ->where('canceledYN', 0)
                                ->first();
                            /* approved=0*/
                            if (!empty($checkWhether)) {
                                return $this->sendError("There is a Customer Invoice (" . $checkWhether->bookingInvCode . ") pending for approval for the item you are trying to add. Please check again.", 500);
                            }

                        }        
            }
    }

}
