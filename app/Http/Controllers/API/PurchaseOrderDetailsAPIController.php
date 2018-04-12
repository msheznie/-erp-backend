<?php
/**
 * =============================================
 * -- File Name : PurchaseOrderDetails.php
 * -- Project Name : ERP
 * -- Module Name :  Purchase Order Details
 * -- Author : Mohamed Fayas
 * -- Create date : 14 - March 2018
 * -- Description : This file contains the all CRUD for Purchase Order Details(item )
 * -- REVISION HISTORY
 * -- Date: 14-March 2018 By: Fayas Description: Added new functions named as getItemMasterPurchaseHistory(),exportPurchaseHistory(),
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreatePurchaseOrderDetailsAPIRequest;
use App\Http\Requests\API\UpdatePurchaseOrderDetailsAPIRequest;
use App\Http\Requests\API\storePurchaseOrderDetailsFromPR;
use App\Models\PurchaseOrderDetails;
use App\Models\ItemAssigned;
use App\Models\ProcumentOrder;
use App\Models\CompanyPolicyMaster;
use App\Models\PurchaseRequestDetails;
use App\Models\PurchaseRequest;
use App\Repositories\PurchaseOrderDetailsRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


/**
 * Class PurchaseOrderDetailsController
 * @package App\Http\Controllers\API
 */
class PurchaseOrderDetailsAPIController extends AppBaseController
{
    /** @var  PurchaseOrderDetailsRepository */
    private $purchaseOrderDetailsRepository;

    public function __construct(PurchaseOrderDetailsRepository $purchaseOrderDetailsRepo)
    {
        $this->purchaseOrderDetailsRepository = $purchaseOrderDetailsRepo;
    }

    /**
     * Display a listing of the PurchaseOrderDetails.
     * GET|HEAD /purchaseOrderDetails
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->purchaseOrderDetailsRepository->pushCriteria(new RequestCriteria($request));
        $this->purchaseOrderDetailsRepository->pushCriteria(new LimitOffsetCriteria($request));
        $purchaseOrderDetails = $this->purchaseOrderDetailsRepository->all();

        return $this->sendResponse($purchaseOrderDetails->toArray(), 'Purchase Order Details retrieved successfully');
    }

    /**
     * Display a listing of the PurchaseOrderDetails by Item master.
     * GET /getItemMasterPurchaseHistory
     *
     * @param Request $request
     * @return Response
     */

    public function getItemMasterPurchaseHistory(Request $request)
    {

        $purchaseOrderDetails = DB::table('erp_purchaseorderdetails')
            ->leftJoin('units', 'erp_purchaseorderdetails.unitOfMeasure', '=', 'units.UnitID')
            ->leftJoin('currencymaster', 'erp_purchaseorderdetails.supplierItemCurrencyID', '=', 'currencymaster.currencyID')
            ->Join('companymaster', 'erp_purchaseorderdetails.companyID', '=', 'companymaster.CompanyID')
            ->Join('erp_purchaseordermaster', 'erp_purchaseorderdetails.purchaseOrderMasterID', '=', 'erp_purchaseordermaster.purchaseOrderID')
            ->leftJoin('erp_location', 'erp_purchaseordermaster.poLocation', '=', 'erp_location.locationID')
            ->where('erp_purchaseordermaster.approved', -1)
            ->where('erp_purchaseorderdetails.itemCode', $request['itemCodeSystem'])
            ->select('erp_purchaseorderdetails.purchaseOrderMasterID',
                'erp_purchaseorderdetails.companyID',
                'companymaster.CompanyName',
                'erp_purchaseordermaster.purchaseOrderCode',
                'erp_purchaseordermaster.supplierPrimaryCode',
                'erp_purchaseordermaster.supplierName',
                'erp_purchaseordermaster.poLocation',
                'erp_location.locationName AS Location',
                'erp_purchaseorderdetails.itemCode',
                'erp_purchaseorderdetails.itemPrimaryCode',
                'erp_purchaseorderdetails.itemDescription',
                'erp_purchaseorderdetails.supplierPartNumber',
                'erp_purchaseorderdetails.unitOfMeasure',
                'erp_purchaseorderdetails.noQty',
                'units.UnitShortCode',
                'erp_purchaseorderdetails.unitCost',
                'currencymaster.CurrencyCode',
                'currencymaster.DecimalPlaces',
                'erp_purchaseorderdetails.GRVcostPerUnitSupTransCur',
                'erp_purchaseordermaster.approvedDate',
                'erp_purchaseordermaster.approved')
            ->paginate(15);


        return $this->sendResponse($purchaseOrderDetails, 'Purchase Order Details retrieved successfully');
    }

    /**
     * Export cvs - list of PurchaseOrderDetails by Item.
     * GET /getItemMasterPurchaseHistory
     *
     * @param Request $request
     * @return Response
     */
    public function exportPurchaseHistory(Request $request)
    {

        $type = $request['type'];
        $purchaseOrderDetails = DB::table('erp_purchaseorderdetails')
            ->leftJoin('units', 'erp_purchaseorderdetails.unitOfMeasure', '=', 'units.UnitID')
            ->leftJoin('currencymaster', 'erp_purchaseorderdetails.supplierItemCurrencyID', '=', 'currencymaster.currencyID')
            ->Join('companymaster', 'erp_purchaseorderdetails.companyID', '=', 'companymaster.CompanyID')
            ->Join('erp_purchaseordermaster', 'erp_purchaseorderdetails.purchaseOrderMasterID', '=', 'erp_purchaseordermaster.purchaseOrderID')
            ->leftJoin('erp_location', 'erp_purchaseordermaster.poLocation', '=', 'erp_location.locationID')
            ->where('erp_purchaseordermaster.approved', -1)
            ->where('erp_purchaseorderdetails.itemCode', $request['itemCodeSystem'])
            ->select('erp_purchaseorderdetails.purchaseOrderMasterID',
                'erp_purchaseorderdetails.companyID',
                'companymaster.CompanyName',
                'erp_purchaseordermaster.purchaseOrderCode',
                'erp_purchaseordermaster.supplierPrimaryCode',
                'erp_purchaseordermaster.supplierName',
                'erp_purchaseordermaster.poLocation',
                'erp_location.locationName AS Location',
                'erp_purchaseorderdetails.itemCode',
                'erp_purchaseorderdetails.itemPrimaryCode',
                'erp_purchaseorderdetails.itemDescription',
                'erp_purchaseorderdetails.supplierPartNumber',
                'erp_purchaseorderdetails.unitOfMeasure',
                'erp_purchaseorderdetails.noQty',
                'units.UnitShortCode',
                'erp_purchaseorderdetails.unitCost',
                'currencymaster.CurrencyCode',
                'currencymaster.DecimalPlaces',
                'erp_purchaseorderdetails.GRVcostPerUnitSupTransCur',
                'erp_purchaseordermaster.approvedDate',
                'erp_purchaseordermaster.approved')
            ->get();

        foreach ($purchaseOrderDetails as $order) {
            $data[] = array(
                //'purchaseOrderMasterID' => $order->purchaseOrderMasterID,
                'Company Name' => $order->CompanyName,
                'PO Code' => $order->purchaseOrderCode,
                'Supplier Code' => $order->supplierPrimaryCode,
                'Approved Date' => date("d/m/Y", strtotime($order->approvedDate)),
                'supplier Name' => $order->supplierName,
                'Part Number' => $order->supplierPartNumber,
                'UOM' => $order->UnitShortCode,
                'Currency' => $order->CurrencyCode,
                'PO Qty' => $order->noQty,
                'Unit Cost' => $order->unitCost,
            );
        }

        $csv = \Excel::create('purchaseHistory', function ($excel) use ($data) {

            $excel->sheet('sheet name', function ($sheet) use ($data) {
                $sheet->fromArray($data);
                //$sheet->getStyle('A1')->getAlignment()->setWrapText(true);
                $sheet->setAutoSize(true);
                $sheet->getStyle('C1:C2')->getAlignment()->setWrapText(true);
            });
            $lastrow = $excel->getActiveSheet()->getHighestRow();
            $excel->getActiveSheet()->getStyle('A1:J' . $lastrow)->getAlignment()->setWrapText(true);
        })->download($type);

        return $this->sendResponse($csv, 'successfully export');
    }

    public function getItemsByProcumentOrder(Request $request)
    {
        $input = $request->all();
        $poID = $input['purchaseOrderID'];

        $items = PurchaseOrderDetails::where('purchaseOrderMasterID', $poID)
            ->with(['unit' => function ($query) {
            }])
            ->get();

        return $this->sendResponse($items->toArray(), 'Purchase Order Details retrieved successfully');
    }

    /**
     * Store a newly created PurchaseOrderDetails in storage.
     * POST /purchaseOrderDetails
     *
     * @param CreatePurchaseOrderDetailsAPIRequest $request
     *
     * @return Response
     */
    public function store(CreatePurchaseOrderDetailsAPIRequest $request)
    {
        $input = array_except($request->all(), 'unit');
        $input = $this->convertArrayToValue($input);
        $itemCode = $input['itemCode'];

        $companySystemID = $input['companySystemID'];

        $item = ItemAssigned::where('itemCodeSystem', $input['itemCode'])
            ->where('companySystemID', $companySystemID)
            ->first();

        $itemExist = PurchaseOrderDetails::where('itemCode', $input['itemCode'])
            ->where('purchaseOrderMasterID', $input['purchaseOrderID'])
            ->first();

        if (!empty($itemExist)) {
            return $this->sendError('Added Item All Ready Exist');
        }

        if (empty($item)) {
            return $this->sendError('Item not found');
        }

        $purchaseOrder = ProcumentOrder::where('purchaseOrderID', $input['purchaseOrderID'])
            ->first();

        if (empty($purchaseOrder)) {
            return $this->sendError('Purchase Order not found');
        }

        $companyPolicyMaster = CompanyPolicyMaster::where('companyPolicyCategoryID', 18)
            ->where('companySystemID', $companySystemID)
            ->first();

        if ($companyPolicyMaster) {
            if (($companyPolicyMaster->isYesNO == 0) && ($purchaseOrder->financeCategory == 1)) {

                $procumentOrdersExist = ProcumentOrder::where('companySystemID', $companySystemID)
                    ->where('serviceLineSystemID', $purchaseOrder->serviceLineSystemID)
                    ->where('approved', 0)
                    ->where('poCancelledYN', 0)
                    ->with(['detail' => function ($query) use ($itemCode) {
                        $query->where('itemCode', $itemCode);
                    }])->first();

                if (!empty($procumentOrdersExist)) {
                    return $this->sendError('The item you are trying to add is pending for approval in PO ' . $procumentOrdersExist->purchaseOrderCode);
                }
            }
        }

        $input['localCurrencyID'] = $purchaseOrder->localCurrencyID;
        $input['localCurrencyER'] = $purchaseOrder->localCurrencyER;

        $input['companyReportingCurrencyID'] = $purchaseOrder->companyReportingCurrencyID;
        $input['companyReportingER'] = $purchaseOrder->companyReportingER;

        $input['supplierDefaultCurrencyID'] = $purchaseOrder->supplierDefaultCurrencyID;
        $input['supplierDefaultER'] = $purchaseOrder->supplierDefaultER;

        if($input['unitCost'] > 0){
            $currencyConversion = \Helper::currencyConversion($input['companySystemID'], $purchaseOrder->supplierTransactionCurrencyID, $purchaseOrder->supplierTransactionCurrencyID, $input['unitCost']);

            $input['GRVcostPerUnitLocalCur'] = $currencyConversion['localAmount'];
            $input['GRVcostPerUnitSupTransCur'] = $input['unitCost'];
            $input['GRVcostPerUnitComRptCur'] = $currencyConversion['reportingAmount'];

            $input['purchaseRetcostPerUnitLocalCur'] = $currencyConversion['localAmount'];
            $input['purchaseRetcostPerUnitTranCur'] = $input['unitCost'];
            $input['purchaseRetcostPerUnitRptCur'] = $currencyConversion['reportingAmount'];
        }

        // adding supplier Default CurrencyID base currency conversion
        if($input['unitCost'] > 0){
            $currencyConversionDefault = \Helper::currencyConversion($input['companySystemID'], $purchaseOrder->supplierTransactionCurrencyID, $purchaseOrder->supplierDefaultCurrencyID, $input['unitCost']);

            $prDetail_arr['GRVcostPerUnitSupDefaultCur'] = $currencyConversionDefault['documentAmount'];
            $prDetail_arr['purchaseRetcostPerUniSupDefaultCur'] = $currencyConversionDefault['documentAmount'];
        }

        $input['purchaseOrderMasterID'] = $input['purchaseOrderID'];
        $input['itemCode'] = $item->itemCodeSystem;
        $input['itemPrimaryCode'] = $item->itemPrimaryCode;
        $input['itemDescription'] = $item->itemDescription;
        $input['unitOfMeasure'] = $item->itemUnitOfMeasure;
        $input['itemFinanceCategoryID'] = $item->financeCategoryMaster;
        $input['itemFinanceCategorySubID'] = $item->financeCategorySub;

        $input['serviceLineSystemID'] = $purchaseOrder->serviceLineSystemID;
        $input['serviceLineCode'] = $purchaseOrder->serviceLine;
        $input['companySystemID'] = $item->companySystemID;
        $input['companyID'] = $item->companyID;

        $purchaseOrderDetails = $this->purchaseOrderDetailsRepository->create($input);

        return $this->sendResponse($purchaseOrderDetails->toArray(), 'Purchase Order Details saved successfully');
    }

    public function storePurchaseOrderDetailsFromPR(Request $request)
    {
        $input = $request->all();
        $prDetail_arr = array();
        $prDetail_insert = array();
        $item = array();
        $purchaseOrderID = $input['purchaseOrderID'];

        foreach ($input['detailTable'] as $new) {
            if ($new['isChecked'] && $new['poQty'] > 0) {

                //checking the fullyOrdered or partial in po
                $detailSum = PurchaseOrderDetails::select(DB::raw('COALESCE(SUM(noQty),0) as totalPoqty'))
                    ->where('purchaseRequestDetailsID', $new['purchaseRequestDetailsID'])
                    ->first();

                $totalAddedQty = $new['poQty'] + $detailSum['totalPoqty'];

                if ($new['quantityRequested'] == $totalAddedQty) {
                    $fullyOrdered = 2;
                } else {
                    $fullyOrdered = 1;
                }

                $purchaseOrder = ProcumentOrder::where('purchaseOrderID', $purchaseOrderID)
                    ->first();

                // checking the qty request is matching with sum total
                //if ($new['quantityRequested'] >= $totalAddedQty) {
                if ($new['quantityRequested'] >= $new['poQty']) {

                    $prDetail_arr['companySystemID'] = $new['companySystemID'];
                    $prDetail_arr['companyID'] = $new['companyID'];
                    $prDetail_arr['purchaseRequestDetailsID'] = $new['purchaseRequestDetailsID'];
                    $prDetail_arr['purchaseRequestID'] = $new['purchaseRequestID'];

                    $prDetail_arr['itemCode'] = $new['itemCode'];
                    $prDetail_arr['itemPrimaryCode'] = $new['itemPrimaryCode'];
                    $prDetail_arr['itemDescription'] = $new['itemDescription'];
                    $prDetail_arr['comment'] = $new['comments'];
                    $prDetail_arr['unitOfMeasure'] = $new['unitOfMeasure'];

                    $prDetail_arr['purchaseOrderMasterID'] = $purchaseOrderID;
                    $prDetail_arr['noQty'] = $new['poQty'];
                    $prDetail_arr['requestedQty'] = $new['quantityRequested'];
                    $prDetail_arr['requestedQty'] = $new['quantityRequested'];

                    $prDetail_arr['itemFinanceCategoryID'] = $new['itemFinanceCategoryID'];
                    $prDetail_arr['itemFinanceCategorySubID'] = $new['itemFinanceCategorySubID'];

                    $prDetail_arr['localCurrencyID'] = $purchaseOrder->localCurrencyID;
                    $prDetail_arr['localCurrencyER'] = $purchaseOrder->localCurrencyER;

                    $prDetail_arr['companyReportingCurrencyID'] = $purchaseOrder->companyReportingCurrencyID;
                    $prDetail_arr['companyReportingER'] = $purchaseOrder->companyReportingER;

                    $prDetail_arr['supplierDefaultCurrencyID'] = $purchaseOrder->supplierDefaultCurrencyID;
                    $prDetail_arr['supplierDefaultER'] = $purchaseOrder->supplierDefaultER;

                    $prDetail_arr['companySystemID'] = $purchaseOrder->companySystemID;
                    $prDetail_arr['companyID'] = $purchaseOrder->companyID;
                    $prDetail_arr['serviceLineSystemID'] = $purchaseOrder->serviceLineSystemID;
                    $prDetail_arr['serviceLineCode'] = $purchaseOrder->serviceLineCode;

                    $prDetail_arr['unitCost'] = $new['poUnitAmount'];

                    if($new['poUnitAmount'] > 0){
                        $currencyConversion = \Helper::currencyConversion($purchaseOrder->companySystemID, $purchaseOrder->supplierTransactionCurrencyID, $purchaseOrder->supplierTransactionCurrencyID, $new['poUnitAmount']);

                        $prDetail_arr['GRVcostPerUnitLocalCur'] = $currencyConversion['localAmount'];
                        $prDetail_arr['GRVcostPerUnitSupTransCur'] = $new['poUnitAmount'];
                        $prDetail_arr['GRVcostPerUnitComRptCur'] = $currencyConversion['reportingAmount'];

                        $prDetail_arr['purchaseRetcostPerUnitLocalCur'] = $currencyConversion['localAmount'];
                        $prDetail_arr['purchaseRetcostPerUnitTranCur'] = $new['poUnitAmount'];
                        $prDetail_arr['purchaseRetcostPerUnitRptCur'] = $currencyConversion['reportingAmount'];
                    }

                    // adding supplier Default CurrencyID base currency conversion
                    if($new['poUnitAmount'] > 0){
                        $currencyConversionDefault = \Helper::currencyConversion($purchaseOrder->companySystemID, $purchaseOrder->supplierTransactionCurrencyID, $purchaseOrder->supplierDefaultCurrencyID, $new['poUnitAmount']);

                        $prDetail_arr['GRVcostPerUnitSupDefaultCur'] = $currencyConversionDefault['documentAmount'];
                        $prDetail_arr['purchaseRetcostPerUniSupDefaultCur'] = $currencyConversionDefault['documentAmount'];
                    }

                    $item = $this->purchaseOrderDetailsRepository->create($prDetail_arr);

                    $update = PurchaseRequestDetails::where('purchaseRequestDetailsID', $new['purchaseRequestDetailsID'])
                        ->update(['selectedForPO' => -1, 'fullyOrdered' => $fullyOrdered, 'poQuantity' => $totalAddedQty]);
                }

                // fetching the total count records from purchase Request Details table
                $purchaseRequestDetailTotalcount = PurchaseRequestDetails::select(DB::raw('count(purchaseRequestDetailsID) as detailCount'))
                    ->where('purchaseRequestID', $new['purchaseRequestID'])
                    ->first();

                // fetching the total count records from purchase Request Details table where fullyOrdered = 2
                $purchaseRequestDetailExist = PurchaseRequestDetails::select(DB::raw('count(purchaseRequestDetailsID) as count'))
                    ->where('purchaseRequestID', $new['purchaseRequestID'])
                    ->where('fullyOrdered', 2)
                    ->where('selectedForPO', -1)
                    ->first();

                // Updating PR Master Table After All Detail Table records updated
                if ($purchaseRequestDetailTotalcount['detailCount'] == $purchaseRequestDetailExist['count']) {
                    $updatePR = PurchaseRequest::find($new['purchaseRequestID'])
                        ->update(['selectedForPO' => -1, 'prClosedYN' => -1]);
                }
                return $this->sendResponse('', 'Purchase Order Details saved successfully');

            }else{
                return $this->sendError('Please Check Item Is Selected ');
            }

        }

    }

    /**
     * Display the specified PurchaseOrderDetails.
     * GET|HEAD /purchaseOrderDetails/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var PurchaseOrderDetails $purchaseOrderDetails */
        $purchaseOrderDetails = $this->purchaseOrderDetailsRepository->findWithoutFail($id);

        if (empty($purchaseOrderDetails)) {
            return $this->sendError('Purchase Order Details not found');
        }

        return $this->sendResponse($purchaseOrderDetails->toArray(), 'Purchase Order Details retrieved successfully');
    }

    /**
     * Update the specified PurchaseOrderDetails in storage.
     * PUT/PATCH /purchaseOrderDetails/{id}
     *
     * @param  int $id
     * @param UpdatePurchaseOrderDetailsAPIRequest $request
     *
     * @return Response
     */

    public function update($id, UpdatePurchaseOrderDetailsAPIRequest $request)
    {
        $input = array_except($request->all(), 'unit');
        $input = $this->convertArrayToValue($input);

        /** @var PurchaseOrderDetails $purchaseOrderDetails */
        $purchaseOrderDetails = $this->purchaseOrderDetailsRepository->findWithoutFail($id);

        if (empty($purchaseOrderDetails)) {
            return $this->sendError('Purchase Order Details not found');
        }

        $purchaseOrder = ProcumentOrder::where('purchaseOrderID', $input['purchaseOrderMasterID'])
            ->first();

        if (empty($purchaseOrder)) {
            return $this->sendError('Purchase Order not found');
        }

        if($input['unitCost'] > 0){
            $currencyConversion = \Helper::currencyConversion($input['companySystemID'], $purchaseOrder->supplierTransactionCurrencyID, $purchaseOrder->supplierTransactionCurrencyID, $input['unitCost']);

            $input['GRVcostPerUnitLocalCur'] = $currencyConversion['localAmount'];
            $input['GRVcostPerUnitSupTransCur'] = $input['unitCost'];
            $input['GRVcostPerUnitComRptCur'] = $currencyConversion['reportingAmount'];

            $input['purchaseRetcostPerUnitLocalCur'] = $currencyConversion['localAmount'];
            $input['purchaseRetcostPerUnitTranCur'] = $input['unitCost'];
            $input['purchaseRetcostPerUnitRptCur'] = $currencyConversion['reportingAmount'];
        }

        // adding supplier Default CurrencyID base currency conversion
        if($input['unitCost'] > 0){
            $currencyConversionDefault = \Helper::currencyConversion($input['companySystemID'], $purchaseOrder->supplierTransactionCurrencyID, $purchaseOrder->supplierDefaultCurrencyID, $input['unitCost']);

            $input['GRVcostPerUnitSupDefaultCur'] = $currencyConversionDefault['documentAmount'];
            $input['purchaseRetcostPerUniSupDefaultCur'] = $currencyConversionDefault['documentAmount'];
        }

        $purchaseOrderDetails = $this->purchaseOrderDetailsRepository->update($input, $id);

        return $this->sendResponse($purchaseOrderDetails->toArray(), 'Purchase Order Details updated successfully');
    }

    /**
     * Remove the specified PurchaseOrderDetails from storage.
     * DELETE /purchaseOrderDetails/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var PurchaseOrderDetails $purchaseOrderDetails */
        $purchaseOrderDetails = $this->purchaseOrderDetailsRepository->findWithoutFail($id);

        if (empty($purchaseOrderDetails)) {
            return $this->sendError('Purchase Order Details not found');
        }

        $purchaseOrderDetails->delete();

        return $this->sendResponse($id, 'Purchase Order Details deleted successfully');
    }
}
