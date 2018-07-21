<?php
/**
 * =============================================
 * -- File Name : PurchaseOrderDetailsAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Purchase Order Details
 * -- Author : Mohamed Fayas
 * -- Create date : 14 - March 2018
 * -- Description : This file contains the all CRUD for Purchase Order Details(item)
 * -- REVISION HISTORY
 * -- Date: 14-March 2018 By: Fayas Description: Added new functions named as getItemMasterPurchaseHistory(),exportPurchaseHistory(),
 * -- Date: 21-March 2018 By: Nazir Description: Added new functions named as getItemsByProcumentOrder(),
 * -- Date: 28-March 2018 By: Nazir Description: Added new functions named as storePurchaseOrderDetailsFromPR(),
 * -- Date: 10-April 2018 By: Nazir Description: Added new functions named as procumentOrderDeleteAllDetails(),
 * -- Date: 12-April 2018 By: Nazir Description: Added new functions named as procumentOrderTotalDiscountUD(),
 * -- Date: 13-April 2018 By: Nazir Description: Added new functions named as procumentOrderTotalTaxUD(),
 * -- Date: 14-June 2018 By: Nazir Description: Added new functions named as getPurchaseOrderDetailForGRV(),
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreatePurchaseOrderDetailsAPIRequest;
use App\Http\Requests\API\UpdatePurchaseOrderDetailsAPIRequest;
use App\Models\PurchaseOrderDetails;
use App\Models\ItemAssigned;
use App\Models\ProcumentOrder;
use App\Models\CompanyPolicyMaster;
use App\Models\PurchaseRequestDetails;
use App\Models\PurchaseRequest;
use App\Models\SupplierAssigned;
use App\Repositories\UserRepository;
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
    private $userRepository;

    public function __construct(PurchaseOrderDetailsRepository $purchaseOrderDetailsRepo, UserRepository $userRepo)
    {
        $this->purchaseOrderDetailsRepository = $purchaseOrderDetailsRepo;
        $this->userRepository = $userRepo;
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
            ->orderBy('erp_purchaseordermaster.approvedDate', 'DESC')
            ->select('erp_purchaseorderdetails.purchaseOrderMasterID',
                'erp_purchaseorderdetails.companyID',
                'companymaster.CompanyName',
                'erp_purchaseordermaster.purchaseOrderCode',
                'erp_purchaseordermaster.purchaseOrderID',
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
        $id = Auth::id();
        $user = $this->userRepository->with(['employee'])->findWithoutFail($id);

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

                $checkWhether = ProcumentOrder::where('purchaseOrderID', '!=', $purchaseOrder->purchaseOrderID)
                    ->where('companySystemID', $companySystemID)
                    ->where('serviceLineSystemID', $purchaseOrder->serviceLineSystemID)
                    ->select(['erp_purchaseordermaster.purchaseOrderID', 'erp_purchaseordermaster.companySystemID',
                        'erp_purchaseordermaster.serviceLine', 'erp_purchaseordermaster.purchaseOrderCode', 'erp_purchaseordermaster.poConfirmedYN', 'erp_purchaseordermaster.approved', 'erp_purchaseordermaster.poCancelledYN'])
                    ->groupBy('erp_purchaseordermaster.purchaseOrderID', 'erp_purchaseordermaster.companySystemID', 'erp_purchaseordermaster.serviceLine', 'erp_purchaseordermaster.purchaseOrderCode', 'erp_purchaseordermaster.poConfirmedYN', 'erp_purchaseordermaster.approved', 'erp_purchaseordermaster.poCancelledYN'
                    );

                $anyPendingApproval = $checkWhether->whereHas('detail', function ($query) use ($companySystemID, $purchaseOrder, $item) {
                    $query->where('itemPrimaryCode', $item->itemPrimaryCode);
                })
                    ->where('approved', 0)
                    ->where('poCancelledYN', 0)
                    ->first();

                if (!empty($anyPendingApproval)) {
                    return $this->sendError("There is a purchase order (" . $anyPendingApproval->purchaseOrderCode . ") pending for approval for the item you are trying to add. Please check again.", 500);
                }

            }
        }

        $input['localCurrencyID'] = $purchaseOrder->localCurrencyID;
        $input['localCurrencyER'] = $purchaseOrder->localCurrencyER;

        $input['supplierItemCurrencyID'] = $purchaseOrder->supplierTransactionCurrencyID;
        $input['foreignToLocalER'] = $purchaseOrder->supplierTransactionER;

        $input['companyReportingCurrencyID'] = $purchaseOrder->companyReportingCurrencyID;
        $input['companyReportingER'] = $purchaseOrder->companyReportingER;

        $input['supplierDefaultCurrencyID'] = $purchaseOrder->supplierDefaultCurrencyID;
        $input['supplierDefaultER'] = $purchaseOrder->supplierDefaultER;

        if ($input['unitCost'] > 0) {
            $currencyConversion = \Helper::currencyConversion($input['companySystemID'], $purchaseOrder->supplierTransactionCurrencyID, $purchaseOrder->supplierTransactionCurrencyID, $input['unitCost']);

            $input['GRVcostPerUnitLocalCur'] = $currencyConversion['localAmount'];
            $input['GRVcostPerUnitSupTransCur'] = $input['unitCost'];
            $input['GRVcostPerUnitComRptCur'] = $currencyConversion['reportingAmount'];

            $input['purchaseRetcostPerUnitLocalCur'] = $currencyConversion['localAmount'];
            $input['purchaseRetcostPerUnitTranCur'] = $input['unitCost'];
            $input['purchaseRetcostPerUnitRptCur'] = $currencyConversion['reportingAmount'];
        }

        // adding supplier Default CurrencyID base currency conversion
        if ($input['unitCost'] > 0) {
            $currencyConversionDefault = \Helper::currencyConversion($input['companySystemID'], $purchaseOrder->supplierTransactionCurrencyID, $purchaseOrder->supplierDefaultCurrencyID, $input['unitCost']);

            $prDetail_arr['GRVcostPerUnitSupDefaultCur'] = $currencyConversionDefault['documentAmount'];
            $prDetail_arr['purchaseRetcostPerUniSupDefaultCur'] = $currencyConversionDefault['documentAmount'];
        }

        $input['purchaseOrderMasterID'] = $input['purchaseOrderID'];
        $input['itemCode'] = $item->itemCodeSystem;
        $input['itemPrimaryCode'] = $item->itemPrimaryCode;
        $input['supplierPartNumber'] = $item->secondaryItemCode;
        $input['itemDescription'] = $item->itemDescription;
        $input['unitOfMeasure'] = $item->itemUnitOfMeasure;
        $input['itemFinanceCategoryID'] = $item->financeCategoryMaster;
        $input['itemFinanceCategorySubID'] = $item->financeCategorySub;

        $input['serviceLineSystemID'] = $purchaseOrder->serviceLineSystemID;
        $input['serviceLineCode'] = $purchaseOrder->serviceLine;
        $input['companySystemID'] = $item->companySystemID;
        $input['companyID'] = $item->companyID;

        $input['createdPcID'] = gethostname();
        $input['createdUserID'] = $user->employee['empID'];
        $input['createdUserSystemID'] = $user->employee['employeeSystemID'];

        $purchaseOrderDetails = $this->purchaseOrderDetailsRepository->create($input);

        //calculate tax amount according to the percantage for tax update

        //getting total sum of PO detail Amount
        $poMasterSum = PurchaseOrderDetails::select(DB::raw('COALESCE(SUM(netAmount),0) as masterTotalSum'))
            ->where('purchaseOrderMasterID', $input['purchaseOrderID'])
            ->first();
        //if($purchaseOrder->VATPercentage > 0 && $purchaseOrder->supplierVATEligible == 1 && $purchaseOrder->vatRegisteredYN == 0){
        if ($purchaseOrder->VATPercentage > 0 && $purchaseOrder->supplierVATEligible == 1) {
            $calculatVatAmount = ($poMasterSum['masterTotalSum'] - $purchaseOrder->poDiscountAmount) * ($purchaseOrder->VATPercentage / 100);

            $currencyConversionVatAmount = \Helper::currencyConversion($companySystemID, $purchaseOrder->supplierTransactionCurrencyID, $purchaseOrder->supplierTransactionCurrencyID, $calculatVatAmount);

            $updatePOMaster = ProcumentOrder::find($input['purchaseOrderID'])
                ->update([
                    'VATAmount' => $calculatVatAmount,
                    'VATAmountLocal' => round($currencyConversionVatAmount['localAmount'], 8),
                    'VATAmountRpt' => round($currencyConversionVatAmount['reportingAmount'], 8)
                ]);

        }

        return $this->sendResponse($purchaseOrderDetails->toArray(), 'Purchase Order Details saved successfully');
    }

    public function storePurchaseOrderDetailsFromPR(Request $request)
    {
        $input = $request->all();
        $prDetail_arr = array();
        $item = array();
        $purchaseOrderID = $input['purchaseOrderID'];

        $id = Auth::id();
        $user = $this->userRepository->with(['employee'])->findWithoutFail($id);

        foreach ($input['detailTable'] as $newValidation) {
            if (($newValidation['isChecked'] && $newValidation['poQty'] == '') || ($newValidation['isChecked'] == '' && $newValidation['poQty'] > 0)) {
                $validator = \Validator::make($newValidation, [
                    'poQty' => 'required',
                    'isChecked' => 'required',
                ]);
            } else {
                $validator = \Validator::make($newValidation, [
                ]);
            }
        }

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        $purchaseOrder = ProcumentOrder::where('purchaseOrderID', $purchaseOrderID)
            ->first();

        foreach ($input['detailTable'] as $new) {

            $PRMaster = PurchaseRequest::find($new['purchaseRequestID']);

            $prDetailExist = PurchaseOrderDetails::select(DB::raw('purchaseOrderDetailsID'))
                ->where('purchaseOrderMasterID', $purchaseOrderID)
                ->where('purchaseRequestDetailsID', $new['purchaseRequestDetailsID'])
                ->first();

            if (empty($prDetailExist)) {

                if ($new['isChecked'] && $new['poQty'] > 0) {

                    //checking the fullyOrdered or partial in po
                    $detailSum = PurchaseOrderDetails::select(DB::raw('COALESCE(SUM(noQty),0) as totalPoqty'))
                        ->where('purchaseRequestDetailsID', $new['purchaseRequestDetailsID'])
                        ->first();

                    $totalAddedQty = $new['poQty'] + $detailSum['totalPoqty'];

                    if ($new['quantityRequested'] == $totalAddedQty) {
                        $fullyOrdered = 2;
                        $prClosedYN = -1;
                        $selectedForPO = -1;
                    } else {
                        $fullyOrdered = 1;
                        $prClosedYN = 0;
                        $selectedForPO = 0;
                    }

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

                        $pobalanceQty = $new['quantityRequested'] - $new['poTakenQty'];
                        $prDetail_arr['balanceQty'] = $pobalanceQty;
                        $prDetail_arr['requestedQty'] = $new['quantityRequested'];

                        $prDetail_arr['itemFinanceCategoryID'] = $new['itemFinanceCategoryID'];
                        $prDetail_arr['itemFinanceCategorySubID'] = $new['itemFinanceCategorySubID'];

                        $prDetail_arr['localCurrencyID'] = $purchaseOrder->localCurrencyID;
                        $prDetail_arr['localCurrencyER'] = $purchaseOrder->localCurrencyER;

                        $prDetail_arr['companyReportingCurrencyID'] = $purchaseOrder->companyReportingCurrencyID;
                        $prDetail_arr['companyReportingER'] = $purchaseOrder->companyReportingER;

                        $prDetail_arr['supplierItemCurrencyID'] = $purchaseOrder->supplierTransactionCurrencyID;
                        $prDetail_arr['foreignToLocalER'] = $purchaseOrder->supplierTransactionER;

                        $prDetail_arr['supplierDefaultCurrencyID'] = $purchaseOrder->supplierDefaultCurrencyID;
                        $prDetail_arr['supplierDefaultER'] = $purchaseOrder->supplierDefaultER;

                        $prDetail_arr['companySystemID'] = $purchaseOrder->companySystemID;
                        $prDetail_arr['companyID'] = $purchaseOrder->companyID;
                        $prDetail_arr['serviceLineSystemID'] = $purchaseOrder->serviceLineSystemID;
                        $prDetail_arr['serviceLineCode'] = $purchaseOrder->serviceLine;

                        $prDetail_arr['createdPcID'] = gethostname();
                        $prDetail_arr['createdUserID'] = $user->employee['empID'];
                        $prDetail_arr['createdUserSystemID'] = $user->employee['employeeSystemID'];

                        $prDetail_arr['unitCost'] = $new['poUnitAmount'];
                        $prDetail_arr['netAmount'] = ($new['poUnitAmount'] * $new['poQty']);

                        $prDetail_arr['financeGLcodebBSSystemID'] = $new['financeGLcodebBSSystemID'];
                        $prDetail_arr['financeGLcodebBS'] = $new['financeGLcodebBS'];
                        $prDetail_arr['financeGLcodePLSystemID'] = $new['financeGLcodePLSystemID'];
                        $prDetail_arr['financeGLcodebBSSystemID'] = $new['financeGLcodebBSSystemID'];
                        $prDetail_arr['financeGLcodePL'] = $new['financeGLcodePL'];
                        $prDetail_arr['includePLForGRVYN'] = $new['includePLForGRVYN'];
                        $prDetail_arr['supplierPartNumber'] = $new['partNumber'];
                        $prDetail_arr['budgetYear'] = $new['budgetYear'];
                        $prDetail_arr['prBelongsYear'] = $PRMaster->prBelongsYear;
                        $prDetail_arr['budjetAmtLocal'] = $new['budjetAmtLocal'];
                        $prDetail_arr['budjetAmtRpt'] = $new['budjetAmtRpt'];

                        if ($new['poUnitAmount'] > 0) {
                            $currencyConversion = \Helper::currencyConversion($purchaseOrder->companySystemID, $purchaseOrder->supplierTransactionCurrencyID, $purchaseOrder->supplierTransactionCurrencyID, $new['poUnitAmount']);

                            $prDetail_arr['GRVcostPerUnitLocalCur'] = $currencyConversion['localAmount'];
                            $prDetail_arr['GRVcostPerUnitSupTransCur'] = $new['poUnitAmount'];
                            $prDetail_arr['GRVcostPerUnitComRptCur'] = $currencyConversion['reportingAmount'];

                            $prDetail_arr['purchaseRetcostPerUnitLocalCur'] = $currencyConversion['localAmount'];
                            $prDetail_arr['purchaseRetcostPerUnitTranCur'] = $new['poUnitAmount'];
                            $prDetail_arr['purchaseRetcostPerUnitRptCur'] = $currencyConversion['reportingAmount'];
                        }

                        // adding supplier Default CurrencyID base currency conversion
                        if ($new['poUnitAmount'] > 0) {
                            $currencyConversionDefault = \Helper::currencyConversion($purchaseOrder->companySystemID, $purchaseOrder->supplierTransactionCurrencyID, $purchaseOrder->supplierDefaultCurrencyID, $new['poUnitAmount']);

                            $prDetail_arr['GRVcostPerUnitSupDefaultCur'] = $currencyConversionDefault['documentAmount'];
                            $prDetail_arr['purchaseRetcostPerUniSupDefaultCur'] = $currencyConversionDefault['documentAmount'];
                        }

                        $item = $this->purchaseOrderDetailsRepository->create($prDetail_arr);

                        $update = PurchaseRequestDetails::where('purchaseRequestDetailsID', $new['purchaseRequestDetailsID'])
                            ->update(['selectedForPO' => $selectedForPO, 'fullyOrdered' => $fullyOrdered, 'poQuantity' => $totalAddedQty, 'prClosedYN' => $prClosedYN]);
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
                            ->update(['selectedForPO' => -1, 'prClosedYN' => -1, 'supplyChainOnGoing' => -1]);
                    }
                }
            }

            //check all details fullyOrdered in PR Master
            $prMasterfullyOrdered = PurchaseRequestDetails::where('purchaseRequestID', $new['purchaseRequestID'])
                ->whereIn('fullyOrdered', [1, 0])
                ->get()->toArray();

            if (empty($prMasterfullyOrdered)) {
                $updatePRMaster = PurchaseRequest::find($new['purchaseRequestID'])
                    ->update([
                        'selectedForPO' => -1,
                        'prClosedYN' => -1,
                        'supplyChainOnGoing' => -1,
                        'selectedForPOByEmpID' => $user->employee['empID']
                    ]);
            } else {
                $updatePRMaster = PurchaseRequest::find($new['purchaseRequestID'])
                    ->update([
                        'selectedForPO' => 0,
                        'prClosedYN' => 0,
                        'supplyChainOnGoing' => 0,
                        'selectedForPOByEmpID' => $user->employee['empID']
                    ]);
            }

        }

        //calculate tax amount according to the percantage for tax update

        //getting total sum of PO detail Amount
        $poMasterSum = PurchaseOrderDetails::select(DB::raw('COALESCE(SUM(netAmount),0) as masterTotalSum'))
            ->where('purchaseOrderMasterID', $purchaseOrderID)
            ->first();
        //if($purchaseOrder->VATPercentage > 0 && $purchaseOrder->supplierVATEligible == 1 && $purchaseOrder->vatRegisteredYN == 0){
        if ($purchaseOrder->VATPercentage > 0 && $purchaseOrder->supplierVATEligible == 1) {
            $calculatVatAmount = ($poMasterSum['masterTotalSum'] - $purchaseOrder->poDiscountAmount) * ($purchaseOrder->VATPercentage / 100);

            $currencyConversionVatAmount = \Helper::currencyConversion($purchaseOrder->companySystemID, $purchaseOrder->supplierTransactionCurrencyID, $purchaseOrder->supplierTransactionCurrencyID, $calculatVatAmount);

            $updatePOMaster = ProcumentOrder::find($purchaseOrderID)
                ->update([
                    'VATAmount' => $calculatVatAmount,
                    'VATAmountLocal' => round($currencyConversionVatAmount['localAmount'], 8),
                    'VATAmountRpt' => round($currencyConversionVatAmount['reportingAmount'], 8)
                ]);
        }


        return $this->sendResponse('', 'Purchase Order Details saved successfully');

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

        //$empInfo = self::getEmployeeInfo();
        $userId = Auth::id();
        $user = $this->userRepository->with(['employee'])->findWithoutFail($userId);

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

        $discountedUnitPrice = $input['unitCost'] - $input['discountAmount'];

        if ($discountedUnitPrice > 0) {
            $currencyConversion = \Helper::currencyConversion($input['companySystemID'], $purchaseOrder->supplierTransactionCurrencyID, $purchaseOrder->supplierTransactionCurrencyID, $discountedUnitPrice);

            $input['GRVcostPerUnitLocalCur'] = round($currencyConversion['localAmount'], 8);
            $input['GRVcostPerUnitSupTransCur'] = $discountedUnitPrice;
            $input['GRVcostPerUnitComRptCur'] = round($currencyConversion['reportingAmount'], 8);

            $input['purchaseRetcostPerUnitLocalCur'] = round($currencyConversion['localAmount'], 8);
            $input['purchaseRetcostPerUnitTranCur'] = $discountedUnitPrice;
            $input['purchaseRetcostPerUnitRptCur'] = round($currencyConversion['reportingAmount'], 8);
        }

        // adding supplier Default CurrencyID base currency conversion
        if ($discountedUnitPrice > 0) {
            $currencyConversionDefault = \Helper::currencyConversion($input['companySystemID'], $purchaseOrder->supplierTransactionCurrencyID, $purchaseOrder->supplierDefaultCurrencyID, $discountedUnitPrice);

            $input['GRVcostPerUnitSupDefaultCur'] = round($currencyConversionDefault['documentAmount'], 8);
            $input['purchaseRetcostPerUniSupDefaultCur'] = round($currencyConversionDefault['documentAmount'], 8);
        }

        $input['modifiedPc'] = gethostname();
        $input['modifiedUser'] = $user->employee['empID'];

        $purchaseOrderDetails = $this->purchaseOrderDetailsRepository->update($input, $id);

        //calculate tax amount according to the percantage for tax update

        //getting total sum of PO detail Amount
        $poMasterSum = PurchaseOrderDetails::select(DB::raw('COALESCE(SUM(netAmount),0) as masterTotalSum'))
            ->where('purchaseOrderMasterID', $input['purchaseOrderMasterID'])
            ->first();

        // updating master and detail table number of qty

        if (!empty($purchaseOrderDetails->purchaseRequestDetailsID) && !empty($purchaseOrderDetails->purchaseRequestID)) {

            $detailExistPRDetail = PurchaseRequestDetails::find($purchaseOrderDetails->purchaseRequestDetailsID);

            $checkQuentity = ($detailExistPRDetail->quantityRequested - $input['noQty']);

            if ($checkQuentity == 0) {
                $fullyOrdered = 2;
                $prClosedYN = -1;
                $selectedForPO = -1;
            } else {
                $fullyOrdered = 1;
                $prClosedYN = 0;
                $selectedForPO = 0;
            }

            $updateDetail = PurchaseRequestDetails::where('purchaseRequestDetailsID', $purchaseOrderDetails->purchaseRequestDetailsID)
                ->update(['selectedForPO' => $selectedForPO, 'fullyOrdered' => $fullyOrdered, 'poQuantity' => $input['noQty'], 'prClosedYN' => $prClosedYN]);

            //check all details fullyOrdered in PR Master
            $prMasterfullyOrdered = PurchaseRequestDetails::where('purchaseRequestID', $purchaseOrderDetails->purchaseRequestID)
                ->whereIn('fullyOrdered', [1, 0])
                ->get()->toArray();

            if (empty($prMasterfullyOrdered)) {
                $updatePRMaster = PurchaseRequest::find($purchaseOrderDetails->purchaseRequestID)
                    ->update(['selectedForPO' => -1, 'prClosedYN' => -1, 'supplyChainOnGoing' => -1]);
            } else {
                $updatePRMaster = PurchaseRequest::find($purchaseOrderDetails->purchaseRequestID)
                    ->update(['selectedForPO' => 0, 'prClosedYN' => 0, 'supplyChainOnGoing' => 0]);
            }

        }

/*        //calculate tax amount according to the percantage for tax update

        //getting total sum of PO detail Amount
        $poMasterSum = PurchaseOrderDetails::select(DB::raw('COALESCE(SUM(netAmount),0) as masterTotalSum'))
            ->where('purchaseOrderMasterID', $input['purchaseOrderMasterID'])
            ->first();

        //if($purchaseOrder->VATPercentage > 0 && $purchaseOrder->supplierVATEligible == 1 && $purchaseOrder->vatRegisteredYN == 0){
        if ($purchaseOrder->VATPercentage > 0 && $purchaseOrder->supplierVATEligible == 1) {
            $calculatVatAmount = ($poMasterSum['masterTotalSum'] - $purchaseOrder->poDiscountAmount) * ($purchaseOrder->VATPercentage / 100);

            $currencyConversionVatAmount = \Helper::currencyConversion($input['companySystemID'], $purchaseOrder->supplierTransactionCurrencyID, $purchaseOrder->supplierTransactionCurrencyID, $calculatVatAmount);

            $updatePOMaster = ProcumentOrder::find($input['purchaseOrderMasterID'])
                ->update([
                    'VATAmount' => $calculatVatAmount,
                    'VATAmountLocal' => round($currencyConversionVatAmount['localAmount'], 8),
                    'VATAmountRpt' => round($currencyConversionVatAmount['reportingAmount'], 8)
                ]);
        }*/

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

        $purchaseOrder = ProcumentOrder::where('purchaseOrderID', $purchaseOrderDetails->purchaseOrderMasterID)
            ->first();

        if (empty($purchaseOrder)) {
            return $this->sendError('Procurement Order not found');
        }

        $purchaseOrderDetails->delete();

        // updating master and detail table number of qty

        if (!empty($purchaseOrderDetails->purchaseRequestDetailsID) && !empty($purchaseOrderDetails->purchaseRequestID)) {
            $updatePRMaster = PurchaseRequest::find($purchaseOrderDetails->purchaseRequestID)
                ->update([
                    'selectedForPO' => 0,
                    'prClosedYN' => 0,
                    'supplyChainOnGoing' => 0,
                    'selectedForPOByEmpID' => null
                ]);

            $detailExistPRDetail = PurchaseRequestDetails::find($purchaseOrderDetails->purchaseRequestDetailsID);

            $poQty = $detailExistPRDetail->poQuantity - $purchaseOrderDetails->noQty;

            if ($poQty == 0) {
                $fullyOrdered = 0;
            } else {
                $fullyOrdered = 1;
            }

            $updateDetail = PurchaseRequestDetails::where('purchaseRequestDetailsID', $purchaseOrderDetails->purchaseRequestDetailsID)
                ->update(['selectedForPO' => 0, 'fullyOrdered' => $fullyOrdered, 'poQuantity' => $poQty, 'prClosedYN' => 0]);
        }

        //calculate tax amount according to the percantage for tax update

        //getting total sum of PO detail Amount
        $poMasterSum = PurchaseOrderDetails::select(DB::raw('COALESCE(SUM(netAmount),0) as masterTotalSum'))
            ->where('purchaseOrderMasterID', $purchaseOrder->purchaseOrderID)
            ->first();

        //if($purchaseOrder->VATPercentage > 0 && $purchaseOrder->supplierVATEligible == 1 && $purchaseOrder->vatRegisteredYN == 0){
        if ($purchaseOrder->VATPercentage > 0 && $purchaseOrder->supplierVATEligible == 1) {
            $calculatVatAmount = ($poMasterSum['masterTotalSum'] - $purchaseOrder->poDiscountAmount) * ($purchaseOrder->VATPercentage / 100);

            $currencyConversionVatAmount = \Helper::currencyConversion($purchaseOrder->companySystemID, $purchaseOrder->supplierTransactionCurrencyID, $purchaseOrder->supplierTransactionCurrencyID, $calculatVatAmount);

            $updatePOMaster = ProcumentOrder::find($purchaseOrder->purchaseOrderID)
                ->update([
                    'VATAmount' => $calculatVatAmount,
                    'VATAmountLocal' => round($currencyConversionVatAmount['localAmount'], 8),
                    'VATAmountRpt' => round($currencyConversionVatAmount['reportingAmount'], 8)
                ]);
        }

        return $this->sendResponse($id, 'Purchase Order details deleted successfully');
    }

    public function procumentOrderDeleteAllDetails(Request $request)
    {
        $input = $request->all();

        $purchaseOrderID = $input['purchaseOrderID'];

        $detailExist = PurchaseOrderDetails::where('purchaseOrderMasterID', $purchaseOrderID)
            ->first();

        $detailExistAll = PurchaseOrderDetails::where('purchaseOrderMasterID', $purchaseOrderID)
            ->get();

        if (empty($detailExist)) {
            return $this->sendError('There are no details to delete');
        }
        if (!empty($detailExistAll)) {

            foreach ($detailExistAll as $cvDeatil) {

                $deleteDetails = PurchaseOrderDetails::where('purchaseOrderDetailsID', $cvDeatil['purchaseOrderDetailsID'])->delete();

                if (!empty($cvDeatil['purchaseRequestDetailsID']) && !empty($cvDeatil['purchaseRequestID'])) {
                    $updatePRMaster = PurchaseRequest::find($cvDeatil['purchaseRequestID'])
                        ->update([
                            'selectedForPO' => 0,
                            'prClosedYN' => 0,
                            'supplyChainOnGoing' => 0,
                            'selectedForPOByEmpID' => null
                        ]);

                    $detailExistPRDetail = PurchaseRequestDetails::find($cvDeatil['purchaseRequestDetailsID']);

                    $poQty = ($detailExistPRDetail->poQuantity - $cvDeatil['noQty']);

                    if ($poQty == 0) {
                        $fullyOrdered = 0;
                    } else {
                        $fullyOrdered = 1;
                    }

                    $updateDetail = PurchaseRequestDetails::where('purchaseRequestDetailsID', $cvDeatil['purchaseRequestDetailsID'])
                        ->update(['selectedForPO' => 0, 'fullyOrdered' => $fullyOrdered, 'poQuantity' => $poQty, 'prClosedYN' => 0]);
                }
            }
        }

        //update po master
        $updateMaster = ProcumentOrder::where('purchaseOrderID', $purchaseOrderID)
            ->update(['poTotalLocalCurrency' => 0,
                'poTotalSupplierDefaultCurrency' => 0,
                'poTotalSupplierTransactionCurrency' => 0,
                'poTotalComRptCurrency' => 0,
                'poDiscountAmount' => 0,
                'VATAmount' => 0,
                'VATAmountLocal' => 0,
                'VATAmountRpt' => 0
            ]);

        return $this->sendResponse($purchaseOrderID, 'Purchase Order Details deleted successfully');
    }

    public function procumentOrderTotalDiscountUD(Request $request)
    {
        $input = $request->all();

        $purchaseOrderID = $input['purchaseOrderID'];

        $purchaseOrder = ProcumentOrder::where('purchaseOrderID', $purchaseOrderID)
            ->first();

        if (empty($purchaseOrder)) {
            return $this->sendError('Purchase Order not found');
        }

        $updateDetailDiscount = PurchaseOrderDetails::where('purchaseOrderMasterID', $purchaseOrderID)
            ->get();
        if (!empty($updateDetailDiscount)) {

            foreach ($updateDetailDiscount as $itemDiscont) {

                $calculateItemDiscount = (($itemDiscont['netAmount'] - (($purchaseOrder->poDiscountAmount / $purchaseOrder->poTotalSupplierTransactionCurrency) * $itemDiscont['netAmount'])) / $itemDiscont['noQty']);

                $currencyConversion = \Helper::currencyConversion($itemDiscont['companySystemID'], $purchaseOrder->supplierTransactionCurrencyID, $purchaseOrder->supplierTransactionCurrencyID, $calculateItemDiscount);

                $detail['GRVcostPerUnitLocalCur'] = $currencyConversion['localAmount'];
                $detail['GRVcostPerUnitSupTransCur'] = $calculateItemDiscount;
                $detail['GRVcostPerUnitComRptCur'] = $currencyConversion['reportingAmount'];

                $detail['purchaseRetcostPerUnitLocalCur'] = $currencyConversion['localAmount'];
                $detail['purchaseRetcostPerUnitTranCur'] = $calculateItemDiscount;
                $detail['purchaseRetcostPerUnitRptCur'] = $currencyConversion['reportingAmount'];

                //$detail['netAmount'] = $calculateItemDiscount * $itemDiscont['noQty'];

                $this->purchaseOrderDetailsRepository->update($detail, $itemDiscont['purchaseOrderDetailsID']);
            }

            return $this->sendResponse($purchaseOrderID, 'Total discount updated successfully');

        } else {
            return $this->sendResponse($purchaseOrderID, 'Total discount updated successfully');
        }

    }

    public function procumentOrderTotalTaxUD(Request $request)
    {
        $input = $request->all();

        $purchaseOrderID = $input['purchaseOrderID'];

        $purchaseOrder = ProcumentOrder::where('purchaseOrderID', $purchaseOrderID)
            ->first();

        if (empty($purchaseOrder)) {
            return $this->sendError('Purchase Order not found');
        }

        if ($purchaseOrder->vatRegisteredYN == 0) {

            $updateDetailVat = PurchaseOrderDetails::where('purchaseOrderMasterID', $purchaseOrderID)
                ->get();

            if (!empty($updateDetailVat)) {

                foreach ($updateDetailVat as $itemDiscont) {

                    $calculateItemTax = (($purchaseOrder->VATPercentage / 100) * $itemDiscont['GRVcostPerUnitSupTransCur']) + $itemDiscont['GRVcostPerUnitSupTransCur'];

                    $currencyConversion = \Helper::currencyConversion($itemDiscont['companySystemID'], $purchaseOrder->supplierTransactionCurrencyID, $purchaseOrder->supplierTransactionCurrencyID, $calculateItemTax);

                    $detail['GRVcostPerUnitLocalCur'] = $currencyConversion['localAmount'];
                    $detail['GRVcostPerUnitSupTransCur'] = $calculateItemTax;
                    $detail['GRVcostPerUnitComRptCur'] = $currencyConversion['reportingAmount'];

                    $detail['purchaseRetcostPerUnitLocalCur'] = $currencyConversion['localAmount'];
                    $detail['purchaseRetcostPerUnitTranCur'] = $calculateItemTax;
                    $detail['purchaseRetcostPerUnitRptCur'] = $currencyConversion['reportingAmount'];

                    $detail['VATPercentage'] = $purchaseOrder->VATPercentage;
                    //$detail['netAmount'] = $calculateItemTax * $itemDiscont['noQty'];

                    $this->purchaseOrderDetailsRepository->update($detail, $itemDiscont['purchaseOrderDetailsID']);
                }

                return $this->sendResponse($purchaseOrderID, 'Total tax updated successfully');

            } else {
                return $this->sendResponse($purchaseOrderID, 'Total Tax updated successfully');
            }
        } else {
            return $this->sendResponse($purchaseOrderID, 'Total Tax updated successfully');
        }
    }

    public function getPurchaseOrderDetailForGRV(Request $request)
    {
        $input = $request->all();
        $poID = $input['purchaseOrderID'];

        $detail = PurchaseOrderDetails::select(DB::raw('itemPrimaryCode,itemDescription,supplierPartNumber,"" as isChecked, "" as noQty,noQty as poQty,unitOfMeasure,purchaseOrderMasterID,purchaseOrderDetailsID,serviceLineCode,itemCode,companySystemID,companyID,serviceLineCode,itemPrimaryCode,itemDescription,itemFinanceCategoryID,itemFinanceCategorySubID,financeGLcodebBSSystemID,financeGLcodebBS,financeGLcodePLSystemID,financeGLcodePL,includePLForGRVYN,supplierPartNumber,unitOfMeasure,unitCost,discountPercentage,discountAmount,netAmount,comment,supplierDefaultCurrencyID,supplierDefaultER,supplierItemCurrencyID,foreignToLocalER,companyReportingCurrencyID,companyReportingER,localCurrencyID,localCurrencyER,addonDistCost,GRVcostPerUnitLocalCur,GRVcostPerUnitSupDefaultCur,GRVcostPerUnitSupTransCur,GRVcostPerUnitComRptCur,VATPercentage,VATAmount,VATAmountLocal,VATAmountRpt,receivedQty'))
            ->with(['unit' => function ($query) {
            }])
            ->where('purchaseOrderMasterID', $poID)
            ->where('GRVSelectedYN', 0)
            ->where('goodsRecievedYN','<>', 2)
            ->where('manuallyClosed', 0)
            ->get();

        return $this->sendResponse($detail, 'Purchase Order Details retrieved successfully');

    }


}
