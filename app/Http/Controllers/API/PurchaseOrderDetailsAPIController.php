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

use App\helper\Helper;
use App\helper\TaxService;
use App\Jobs\AddBulkItem\PoAddBulkItemJob;
use App\Services\ProcurementOrder\ProcurementOrderService;
use App\Http\Requests\API\CreatePurchaseOrderDetailsAPIRequest;
use App\Http\Requests\API\UpdatePurchaseOrderDetailsAPIRequest;
use App\Models\ProcumentOrderDetail;
use App\Models\PurchaseOrderDetails;
use App\Models\ItemAssigned;
use App\Models\ProcumentOrder;
use App\Models\TaxVatCategories;
use App\Models\AssetFinanceCategory;
use App\Models\FinanceItemcategorySubAssigned;
use App\Models\CompanyPolicyMaster;
use App\Models\PurchaseRequestDetails;
use App\Models\PurchaseRequest;
use App\Models\SupplierAssigned;
use App\Models\SegmentAllocatedItem;
use App\Models\SupplierMaster;
use App\Repositories\UserRepository;
use App\Repositories\PurchaseOrderDetailsRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Models\PoDetailExpectedDeliveryDate;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Repositories\SegmentAllocatedItemRepository;
use App\Models\GRVMaster;
use App\Models\AppointmentDetails;

/**
 * Class PurchaseOrderDetailsController
 * @package App\Http\Controllers\API
 */
class PurchaseOrderDetailsAPIController extends AppBaseController
{
    /** @var  PurchaseOrderDetailsRepository */
    private $purchaseOrderDetailsRepository;
    private $userRepository;
    private $segmentAllocatedItemRepository;

    public function __construct(PurchaseOrderDetailsRepository $purchaseOrderDetailsRepo, UserRepository $userRepo, SegmentAllocatedItemRepository $segmentAllocatedItemRepo)
    {
        $this->purchaseOrderDetailsRepository = $purchaseOrderDetailsRepo;
        $this->userRepository = $userRepo;
        $this->segmentAllocatedItemRepository = $segmentAllocatedItemRepo;
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
        $selectedCompanyId = $request['selectedCompanyId'];
        $isGroup = \Helper::checkIsCompanyGroup($selectedCompanyId);

        if($isGroup){
            $subCompanies = \Helper::getGroupCompany($selectedCompanyId);
        }else{
            $subCompanies = [$selectedCompanyId];
        }

        $purchaseOrderDetails = DB::table('erp_purchaseorderdetails')
            ->leftJoin('units', 'erp_purchaseorderdetails.unitOfMeasure', '=', 'units.UnitID')
            ->leftJoin('currencymaster', 'erp_purchaseorderdetails.supplierItemCurrencyID', '=', 'currencymaster.currencyID')
            ->Join('companymaster', 'erp_purchaseorderdetails.companyID', '=', 'companymaster.CompanyID')
            ->Join('erp_purchaseordermaster', 'erp_purchaseorderdetails.purchaseOrderMasterID', '=', 'erp_purchaseordermaster.purchaseOrderID')
            ->leftJoin('erp_location', 'erp_purchaseordermaster.poLocation', '=', 'erp_location.locationID')
            ->leftJoin('financeitemcategorymaster', 'erp_purchaseorderdetails.itemFinanceCategoryID', '=', 'financeitemcategorymaster.itemCategoryID')
            ->leftJoin('financeitemcategorysub', 'erp_purchaseorderdetails.itemFinanceCategorySubID', '=', 'financeitemcategorysub.itemCategorySubID')
            ->where('erp_purchaseordermaster.approved', -1)
            ->where('erp_purchaseorderdetails.manuallyClosed', 0)
            ->where('erp_purchaseorderdetails.itemCode', $request['itemCodeSystem'])
            ->whereIn('erp_purchaseordermaster.companySystemID', $subCompanies)
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
                'erp_purchaseorderdetails.financeGLcodebBS',
                'erp_purchaseorderdetails.financeGLcodePL',
                'units.UnitShortCode',
                'erp_purchaseorderdetails.unitCost',
                'currencymaster.CurrencyCode',
                'currencymaster.DecimalPlaces',
                'financeitemcategorymaster.categoryDescription',
                'financeitemcategorysub.categoryDescription as subCategoryDescription',
                'erp_purchaseorderdetails.GRVcostPerUnitSupTransCur',
                'erp_purchaseordermaster.approvedDate',
                'erp_purchaseordermaster.approved')
            ->paginate(15);

        return $this->sendResponse($purchaseOrderDetails, 'Purchase Order Details retrieved successfully');
    }

    public function exportPurchaseHistory(Request $request)
    {

        $type = $request['type'];

        $selectedCompanyId = $request['selectedCompanyId'];
        $isGroup = \Helper::checkIsCompanyGroup($selectedCompanyId);

        if($isGroup){
            $subCompanies = \Helper::getGroupCompany($selectedCompanyId);
        }else{
            $subCompanies = [$selectedCompanyId];
        }
        $data = [];
        $purchaseOrderDetails = DB::table('erp_purchaseorderdetails')
            ->leftJoin('units', 'erp_purchaseorderdetails.unitOfMeasure', '=', 'units.UnitID')
            ->leftJoin('currencymaster', 'erp_purchaseorderdetails.supplierItemCurrencyID', '=', 'currencymaster.currencyID')
            ->Join('companymaster', 'erp_purchaseorderdetails.companyID', '=', 'companymaster.CompanyID')
            ->Join('erp_purchaseordermaster', 'erp_purchaseorderdetails.purchaseOrderMasterID', '=', 'erp_purchaseordermaster.purchaseOrderID')
            ->leftJoin('erp_location', 'erp_purchaseordermaster.poLocation', '=', 'erp_location.locationID')
            ->where('erp_purchaseordermaster.approved', -1)
            ->where('erp_purchaseorderdetails.itemCode', $request['itemCodeSystem'])
            ->whereIn('erp_purchaseordermaster.companySystemID', $subCompanies)
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


            if($order->noQty == 0)
            {
              $qua_req = '0';
            }
            else
            {
              $qua_req = $order->noQty;
            }
 
            if($order->unitCost == 0)
            {
              $tran_amount = '0';
            }
            else
            {
              //$tran_amount = round($order->unitCost,$order->DecimalPlaces);

              $tran_amount = number_format((float)$order->unitCost, $order->DecimalPlaces, '.', ',');
            }

            $data[] = array(
                //'purchaseOrderMasterID' => $order->purchaseOrderMasterID,
                'Company Name' => $order->CompanyName,
                'PO Code' => $order->purchaseOrderCode,
                'Supplier Code' => $order->supplierPrimaryCode,
                'Approved Date' => date("Y-m-d", strtotime($order->approvedDate)),
                'supplier Name' => $order->supplierName,
                'Part No / Ref.Number' => $order->supplierPartNumber,
                'UOM' => $order->UnitShortCode,
                'Currency' => $order->CurrencyCode,
                'PO Qty' => $qua_req,
                'Unit Cost' => $tran_amount,
            );
        }

        \Excel::create('purchaseHistory', function ($excel) use ($data) {

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
            }, 'vat_sub_category','altUom'])->skip($input['skip'])->take($input['limit'])->get();
        
        $index = $input['skip'] + 1;
        foreach($items as $item) {
            $item['index'] = $index;
            $index++;
        }

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
        $poType = $input['poTypeID'];

        $companySystemID = $input['companySystemID'];

        $item = ItemAssigned::where('itemCodeSystem', $input['itemCode'])
            ->where('companySystemID', $companySystemID)
            ->first();

        $itemExist = PurchaseOrderDetails::where('itemCode', $input['itemCode'])
            ->where('purchaseOrderMasterID', $input['purchaseOrderID'])
            ->first();

       if($poType == 2) {
           if ($item->financeCategoryMaster == 1) {
               if (!empty($itemExist)) {
                   return $this->sendError('Added item already exist');
               }
           }
       }

        if($poType != 2) {
            if (!empty($itemExist)) {
                return $this->sendError('Added item already exist');
            }
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

        $allowFinanceCategory = CompanyPolicyMaster::where('companyPolicyCategoryID', 20)
            ->where('companySystemID', $purchaseOrder->companySystemID)
            ->first();
        if ($allowFinanceCategory) {
            $policy = $allowFinanceCategory->isYesNO;
            if ($policy == 0) {
                if ($purchaseOrder->financeCategory == null || $purchaseOrder->financeCategory == 0) {
                    return $this->sendError('Category is not found.', 500);
                }

                //checking if item category is same or not
                $pRDetailExistSameItem = ProcumentOrderDetail::select(DB::raw('DISTINCT(itemFinanceCategoryID) as itemFinanceCategoryID'))
                    ->where('purchaseOrderMasterID', $input['purchaseOrderID'])
                    ->first();

                if ($pRDetailExistSameItem) {
                    if ($item->financeCategoryMaster != $pRDetailExistSameItem["itemFinanceCategoryID"]) {
                        return $this->sendError('You cannot add different category item', 500);
                    }
                }
            }
        }

        $financeItemCategorySubAssigned = FinanceItemcategorySubAssigned::where('companySystemID', $item->companySystemID)
            ->where('mainItemCategoryID', $item->financeCategoryMaster)
            ->where('itemCategorySubID', $item->financeCategorySub)
            ->first();

        if (empty($financeItemCategorySubAssigned)) {
            return $this->sendError('Finance category not assigned for the selected item.');
        }

    
        $input['financeGLcodebBSSystemID'] = $financeItemCategorySubAssigned->financeGLcodebBSSystemID;
        $input['financeGLcodebBS'] = $financeItemCategorySubAssigned->financeGLcodebBS;
        if ($item->financeCategoryMaster == 3) {
            $assetCategory = AssetFinanceCategory::find($item->faFinanceCatID);
            if (!$assetCategory) {
                return $this->sendError('Asset category not assigned for the selected item.');
            }
            $input['financeGLcodePLSystemID'] = $assetCategory->COSTGLCODESystemID;
            $input['financeGLcodePL'] = $assetCategory->COSTGLCODE;
        } else {
            $input['financeGLcodePLSystemID'] = $financeItemCategorySubAssigned->financeGLcodePLSystemID;
            $input['financeGLcodePL'] = $financeItemCategorySubAssigned->financeGLcodePL;
        }
        $input['includePLForGRVYN'] = $financeItemCategorySubAssigned->includePLForGRVYN;
        $input['budgetYear'] = $purchaseOrder->budgetYear;

        $currencyConversion = \Helper::currencyConversion($item->companySystemID, $item->wacValueLocalCurrencyID, $purchaseOrder->supplierTransactionCurrencyID, $item->wacValueLocal);

        $input['unitCost'] =  \Helper::roundValue($currencyConversion['documentAmount']);

        $input['localCurrencyID'] = $purchaseOrder->localCurrencyID;
        $input['localCurrencyER'] = $purchaseOrder->localCurrencyER;

        $input['supplierItemCurrencyID'] = $purchaseOrder->supplierTransactionCurrencyID;
        $input['foreignToLocalER'] = $purchaseOrder->supplierTransactionER;

        $input['companyReportingCurrencyID'] = $purchaseOrder->companyReportingCurrencyID;
        $input['companyReportingER'] = $purchaseOrder->companyReportingER;

        $input['supplierDefaultCurrencyID'] = $purchaseOrder->supplierDefaultCurrencyID;
        $input['supplierDefaultER'] = $purchaseOrder->supplierDefaultER;
        $input['VATAmount'] = 0;
        if ($purchaseOrder->isVatEligible) {
            $vatDetails = TaxService::getVATDetailsByItem($purchaseOrder->companySystemID, $input['itemCode'], $purchaseOrder->supplierID);
            $input['VATPercentage'] = $vatDetails['percentage'];
            $input['VATApplicableOn'] = $vatDetails['applicableOn'];
            $input['vatMasterCategoryID'] = $vatDetails['vatMasterCategoryID'];
            $input['vatSubCategoryID'] = $vatDetails['vatSubCategoryID'];
            $input['VATAmount'] = 0;
            if ($input['unitCost'] > 0) {
                $input['VATAmount'] = (($input['unitCost'] / 100) * $vatDetails['percentage']);
            }
            $prDetail_arr['netAmount'] = ($input['unitCost'] + $input['VATAmount']) * $input['noQty'];
            $currencyConversionVAT = \Helper::currencyConversion($purchaseOrder->companySystemID, $purchaseOrder->supplierTransactionCurrencyID, $purchaseOrder->supplierTransactionCurrencyID, $input['VATAmount']);

            $input['VATAmount'] = 0;
            $input['VATAmountLocal'] = 0;
            $input['VATAmountRpt'] = 0;

        }

        $grvCost = $input['unitCost'];

        if ($grvCost > 0) {
            $currencyConversion = \Helper::currencyConversion($input['companySystemID'], $purchaseOrder->supplierTransactionCurrencyID, $purchaseOrder->supplierTransactionCurrencyID, $grvCost);

            $input['GRVcostPerUnitLocalCur'] = \Helper::roundValue($currencyConversion['localAmount']);
            $input['GRVcostPerUnitSupTransCur'] = $grvCost;
            $input['GRVcostPerUnitComRptCur'] = \Helper::roundValue($currencyConversion['reportingAmount']);

            $input['purchaseRetcostPerUnitLocalCur'] = \Helper::roundValue($currencyConversion['localAmount']);
            $input['purchaseRetcostPerUnitTranCur'] = $input['unitCost'];
            $input['purchaseRetcostPerUnitRptCur'] = \Helper::roundValue($currencyConversion['reportingAmount']);
        }

        // adding supplier Default CurrencyID base currency conversion
        if ($grvCost > 0) {
            $currencyConversionDefault = \Helper::currencyConversion($input['companySystemID'], $purchaseOrder->supplierTransactionCurrencyID, $purchaseOrder->supplierDefaultCurrencyID, $grvCost);

            $input['GRVcostPerUnitSupDefaultCur'] = \Helper::roundValue($currencyConversionDefault['documentAmount']);
            $input['purchaseRetcostPerUniSupDefaultCur'] = \Helper::roundValue($currencyConversionDefault['documentAmount']);
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
        $input['companyID'] =  \Helper::getCompanyById($item->companySystemID);

        $input['createdPcID'] = gethostname();
        $input['createdUserID'] = $user->employee['empID'];
        $input['createdUserSystemID'] = $user->employee['employeeSystemID'];

        $markupArray = $this->setMarkupPercentage($input['unitCost'], $purchaseOrder);
        if(!$markupArray['success']) {
            return $this->sendError($markupArray['data'],500);
        }else {
            $markupArray = $markupArray['data'];
        }
        $input['markupPercentage'] = $markupArray['markupPercentage'];
        $input['markupTransactionAmount'] = $markupArray['markupTransactionAmount'];
        $input['markupLocalAmount'] = $markupArray['markupLocalAmount'];
        $input['markupReportingAmount'] = $markupArray['markupReportingAmount'];

        $purchaseOrderDetails = $this->purchaseOrderDetailsRepository->create($input);



        //calculate tax amount according to the percantage for tax update

        // //getting total sum of PO detail Amount
        // $poMasterSum = PurchaseOrderDetails::select(DB::raw('COALESCE(SUM(netAmount),0) as masterTotalSum'))
        //     ->where('purchaseOrderMasterID', $input['purchaseOrderID'])
        //     ->first();
        // //if($purchaseOrder->VATPercentage > 0 && $purchaseOrder->supplierVATEligible == 1 && $purchaseOrder->vatRegisteredYN == 0){
        // if ($purchaseOrder->VATPercentage > 0 && $purchaseOrder->supplierVATEligible == 1) {
        //     $calculatVatAmount = ($poMasterSum['masterTotalSum'] - $purchaseOrder->poDiscountAmount) * ($purchaseOrder->VATPercentage / 100);

        //     $currencyConversionVatAmount = \Helper::currencyConversion($companySystemID, $purchaseOrder->supplierTransactionCurrencyID, $purchaseOrder->supplierTransactionCurrencyID, $calculatVatAmount);

        //     $updatePOMaster = ProcumentOrder::find($input['purchaseOrderID'])
        //         ->update([
        //             'VATAmount' => $calculatVatAmount,
        //             'VATAmountLocal' => round($currencyConversionVatAmount['localAmount'], 8),
        //             'VATAmountRpt' => round($currencyConversionVatAmount['reportingAmount'], 8)
        //         ]);

        // }

        return $this->sendResponse($purchaseOrderDetails->toArray(), 'Purchase Order Details saved successfully');
    }

    public function storePurchaseOrderDetailsFromPR(Request $request)
    {
        $input = $request->all();
        $prDetail_arr = array();
        $validator = array();
        $purchaseOrderID = $input['purchaseOrderID'];
        $id = Auth::id();
        $user = $this->userRepository->with(['employee'])->findWithoutFail($id);

        $isCheckArr = collect($input['detailTable'])->pluck('isChecked')->toArray();
        if (!in_array(true, $isCheckArr)) {
            return $this->sendError("No items selected to add.");
        }

        foreach ($input['detailTable'] as $newValidation) {
            if (($newValidation['isChecked'] && $newValidation['poQty'] == "") || ($newValidation['isChecked'] && $newValidation['poQty'] == 0) || ($newValidation['isChecked'] == '' && $newValidation['poQty'] > 0)) {

                $messages = [
                    'required' => 'PO quantity field is required.',
                ];

                $validator = \Validator::make($newValidation, [
                    'poQty' => 'required',
                    'isChecked' => 'required',
                ], $messages);

                if ($validator->fails()) {
                    return $this->sendError($validator->messages(), 422);
                }
            }
        }

        $itemExistArray = array();
        //check added item exist
        foreach ($input['detailTable'] as $itemExist) {

            if ($itemExist['isChecked'] && $itemExist['poQty'] > 0) {
                $prDetailExist = PurchaseOrderDetails::select(DB::raw('purchaseOrderDetailsID,itemPrimaryCode'))
                    ->where('purchaseOrderMasterID', $purchaseOrderID)
                    ->where('purchaseRequestDetailsID', $itemExist['purchaseRequestDetailsID'])
                    ->get();

                if (!empty($prDetailExist)) {
                    foreach ($prDetailExist as $row) {
                        $itemDrt = $row['itemPrimaryCode'] . " already exist";
                        $itemExistArray[] = [$itemDrt];
                    }
                }
            }
        }

        if (!empty($itemExistArray)) {
            return $this->sendError($itemExistArray, 422);
        }

        $purchaseOrder = ProcumentOrder::where('purchaseOrderID', $purchaseOrderID)->first();

        if (empty($purchaseOrder)) {
            return $this->sendError("Request department is different from order");
        }

        $allowFinanceCategory = CompanyPolicyMaster::where('companyPolicyCategoryID', 20)
            ->where('companySystemID', $purchaseOrder->companySystemID)
            ->first();

        if ($allowFinanceCategory) {
            $policy = $allowFinanceCategory->isYesNO;


            if ($policy == 0) {
                if ($purchaseOrder->financeCategory == null || $purchaseOrder->financeCategory == 0) {
                    return $this->sendError('Category is not found.', 500);
                }
            }
        }

        //check PO segment is correct with PR pull segment

        foreach ($input['detailTable'] as $itemExist) {

            if ($itemExist['isChecked'] && $itemExist['poQty'] > 0) {

                $PRMaster = PurchaseRequest::find($itemExist['purchaseRequestID']);

                if ($purchaseOrder->serviceLineSystemID != $PRMaster->serviceLineSystemID) {
                    return $this->sendError("Request department is different from order");
                }
            }
        }

        // check different budget year
        $prDetailsBY = isset($input['detailTable'][0]['budgetYear']) ? $input['detailTable'][0]['budgetYear'] : 0;
        $poDetails = PurchaseOrderDetails::where('purchaseOrderMasterID', $purchaseOrderID)->orderBy('purchaseOrderDetailsID', 'DESC')->first();
        if (!empty($poDetails)) {
            if (isset($poDetails->budgetYear) && $poDetails->budgetYear && ($prDetailsBY != $poDetails->budgetYear)) {
                return $this->sendError("Different Budget Year Found. You can not pull different budget year PR for same PO");
            }
        }

        DB::beginTransaction();
        try {
            foreach ($input['detailTable'] as $new) {

                $PRMaster = PurchaseRequest::find($new['purchaseRequestID']);

                $prDetailExist = PurchaseOrderDetails::select(DB::raw('purchaseOrderDetailsID'))
                    ->where('purchaseOrderMasterID', $purchaseOrderID)
                    ->where('purchaseRequestDetailsID', $new['purchaseRequestDetailsID'])
                    ->first();

                if (empty($prDetailExist)) {

                    if ($new['isChecked'] && $new['poQty'] > 0) {

                        //checking the fullyOrdered or partial in po
                        $totalAddedQty = PurchaseOrderDetails::RequestDetailSum($new['purchaseRequestDetailsID']);
                        $totalAddedQty = $new['poQty'] + $totalAddedQty;
                        if ($totalAddedQty > $new['quantityRequested']) {
                            return $this->sendError($new['itemPrimaryCode']." item PO qty cannot be greater than balance qty", 500);
                        }

                        if ($new['quantityRequested'] == $totalAddedQty) {
                            $fullyOrdered = 2;
                            $prClosedYN = -1;
                            $selectedForPO = -1;
                        } else {
                            $fullyOrdered = 1;
                            $prClosedYN = 0;
                            $selectedForPO = 0;
                        }

                        $new['poUnitAmount'] = $new['estimatedCost'];

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
                            $prDetail_arr['altUnit'] = $new['altUnit'];
                            $prDetail_arr['altUnitValue'] = $new['altUnitValue'];
                            $prDetail_arr['purchaseOrderMasterID'] = $purchaseOrderID;
                            $prDetail_arr['noQty'] = $new['poQty'];

                            $pobalanceQty = ($new['quantityRequested'] - $new['poTakenQty']);
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
                            // Get VAT percentage for item

                            if ($purchaseOrder->isVatEligible) {
                                $vatDetails = TaxService::getVATDetailsByItem($purchaseOrder->companySystemID, $new['itemCode'], $purchaseOrder->supplierID);
                                $prDetail_arr['VATPercentage'] = $vatDetails['percentage'];
                                $prDetail_arr['VATApplicableOn'] = $vatDetails['applicableOn'];
                                $prDetail_arr['vatMasterCategoryID'] = $vatDetails['vatMasterCategoryID'];
                                $prDetail_arr['vatSubCategoryID'] = $vatDetails['vatSubCategoryID'];
                                $prDetail_arr['VATAmount'] = 0;
                                if ($prDetail_arr['unitCost'] > 0) {
                                    $prDetail_arr['VATAmount'] = (($prDetail_arr['unitCost'] / 100) * $vatDetails['percentage']);
                                }
                                // $prDetail_arr['netAmount'] = ($prDetail_arr['unitCost'] + $prDetail_arr['VATAmount']) * $new['poQty'];
                                $currencyConversionVAT = \Helper::currencyConversion($purchaseOrder->companySystemID, $purchaseOrder->supplierTransactionCurrencyID, $purchaseOrder->supplierTransactionCurrencyID, $prDetail_arr['VATAmount']);

                                $prDetail_arr['VATAmountLocal'] = \Helper::roundValue($currencyConversionVAT['localAmount']);
                                $prDetail_arr['VATAmountRpt'] = \Helper::roundValue($currencyConversionVAT['reportingAmount']);

                            }

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

                            $prDetail_arr['supplierCatalogDetailID'] = isset($new['supplierCatalogDetailID']) ? $new['supplierCatalogDetailID'] : 0;
                            $prDetail_arr['supplierCatalogMasterID'] = isset($new['supplierCatalogMasterID']) ? $new['supplierCatalogMasterID'] : 0;

                            if ($new['poUnitAmount'] > 0) {
                                $currencyConversion = \Helper::currencyConversion($purchaseOrder->companySystemID, $purchaseOrder->supplierTransactionCurrencyID, $purchaseOrder->supplierTransactionCurrencyID, $new['poUnitAmount']);

                                $prDetail_arr['GRVcostPerUnitLocalCur'] = \Helper::roundValue($currencyConversion['localAmount']);
                                $prDetail_arr['GRVcostPerUnitSupTransCur'] = $new['poUnitAmount'];
                                $prDetail_arr['GRVcostPerUnitComRptCur'] = \Helper::roundValue($currencyConversion['reportingAmount']);

                                $prDetail_arr['purchaseRetcostPerUnitLocalCur'] = \Helper::roundValue($currencyConversion['localAmount']);
                                $prDetail_arr['purchaseRetcostPerUnitTranCur'] = $new['poUnitAmount'];
                                $prDetail_arr['purchaseRetcostPerUnitRptCur'] = \Helper::roundValue($currencyConversion['reportingAmount']);
                            }

                            // adding supplier Default CurrencyID base currency conversion
                            if ($new['poUnitAmount'] > 0) {
                                $currencyConversionDefault = \Helper::currencyConversion($purchaseOrder->companySystemID, $purchaseOrder->supplierTransactionCurrencyID, $purchaseOrder->supplierDefaultCurrencyID, $new['poUnitAmount']);

                                $prDetail_arr['GRVcostPerUnitSupDefaultCur'] = \Helper::roundValue($currencyConversionDefault['documentAmount']);
                                $prDetail_arr['purchaseRetcostPerUniSupDefaultCur'] = \Helper::roundValue($currencyConversionDefault['documentAmount']);
                            }

                            $markupArray = $this->setMarkupPercentage($prDetail_arr['unitCost'], $purchaseOrder);

                            if(!$markupArray['success']) {
                                return $this->sendError($markupArray['data'],500);
                            }else {
                                $markupArray = $markupArray['data'];
                            }

                            $prDetail_arr['markupPercentage'] = $markupArray['markupPercentage'];
                            $prDetail_arr['markupTransactionAmount'] = $markupArray['markupTransactionAmount'];
                            $prDetail_arr['markupLocalAmount'] = $markupArray['markupLocalAmount'];
                            $prDetail_arr['markupReportingAmount'] = $markupArray['markupReportingAmount'];

                            $item = $this->purchaseOrderDetailsRepository->create($prDetail_arr);

                            $update = PurchaseRequestDetails::where('purchaseRequestDetailsID', $new['purchaseRequestDetailsID'])
                                ->update(['selectedForPO' => $selectedForPO, 'fullyOrdered' => $fullyOrdered, 'poQuantity' => $totalAddedQty, 'prClosedYN' => $prClosedYN]);


                            if (isset($input['allocationMappingData'])) {
                                $mappingPurchaseRequestIDs = collect($input['allocationMappingData'])->pluck('purchaseRequestDetailsID')->toArray();

                                if (in_array($new['purchaseRequestDetailsID'], $mappingPurchaseRequestIDs)) {
                                    foreach ($input['allocationMappingData'] as $keyMap => $valueMap) {
                                        if ($new['purchaseRequestDetailsID'] == $valueMap['purchaseRequestDetailsID']) {
                                            if (isset($valueMap['allocationMappingArray']) && count($valueMap['allocationMappingArray']) > 0) {

                                                foreach ($valueMap['allocationMappingArray'] as $key1 => $value1) {
                                                    if (isset($value1['allocatedQty']) && floatval($value1['allocatedQty']) > 0) {
                                                        $allocatedData = [
                                                            'docDetailID' => $item->purchaseOrderDetailsID,
                                                            'documentSystemID' => $purchaseOrder->documentSystemID,
                                                            'docAutoID' => $purchaseOrder->purchaseOrderID,
                                                            'serviceLineSystemID' => $value1['serviceLineSystemID'],
                                                            'allocatedQty' => $value1['allocatedQty'],
                                                            'pulledDocumentSystemID' => $value1['documentSystemID'],
                                                            'pulledDocumentDetailID' => $value1['documentDetailAutoID'],
                                                        ];

                                                        $segmentAllocatedItem = $this->segmentAllocatedItemRepository->allocatePurchaseOrderItemsFromPR($allocatedData);

                                                        if (!$segmentAllocatedItem['status']) {
                                                            return $this->sendError($segmentAllocatedItem['message']);
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                } else {
                                    $allocatedData = [
                                        'docDetailID' => $item->purchaseOrderDetailsID,
                                        'documentSystemID' => $purchaseOrder->documentSystemID,
                                        'docAutoID' => $purchaseOrder->purchaseOrderID,
                                        'pulledDocumentDetailID' => $new['purchaseRequestDetailsID'],
                                    ];
                                    if ($new['quantityRequested'] == $new['poQty']) {
                                        $segmentAllocatedItem = $this->segmentAllocatedItemRepository->allocateWholeItemsInPRToPO($allocatedData);
                                        if (!$segmentAllocatedItem['status']) {
                                            return $this->sendError($segmentAllocatedItem['message']);
                                        }
                                    } else {
                                        $segmentAllocatedItem = $this->segmentAllocatedItemRepository->allocateWholeItemsInPRToPO($allocatedData, $new['poQty']);
                                        if (!$segmentAllocatedItem['status']) {
                                            return $this->sendError($segmentAllocatedItem['message']);
                                        }
                                    }
                                }
                            } else {
                                $allocatedData = [
                                    'docDetailID' => $item->purchaseOrderDetailsID,
                                    'documentSystemID' => $purchaseOrder->documentSystemID,
                                    'docAutoID' => $purchaseOrder->purchaseOrderID,
                                    'pulledDocumentDetailID' => $new['purchaseRequestDetailsID'],
                                ];
                                if ($new['quantityRequested'] == $new['poQty']) {
                                    $segmentAllocatedItem = $this->segmentAllocatedItemRepository->allocateWholeItemsInPRToPO($allocatedData);
                                    if (!$segmentAllocatedItem['status']) {
                                        return $this->sendError($segmentAllocatedItem['message']);
                                    }
                                } else {
                                    $segmentAllocatedItem = $this->segmentAllocatedItemRepository->allocateWholeItemsInPRToPO($allocatedData, $new['poQty']);
                                    if (!$segmentAllocatedItem['status']) {
                                        return $this->sendError($segmentAllocatedItem['message']);
                                    }
                                }
                            }
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

                if ($purchaseOrder->isVatEligible) {
                    TaxService::updatePOVAT($purchaseOrderID);
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

            $poUpdate = TaxService::updatePOVAT($purchaseOrderID);
            if (!$poUpdate) {
                return $this->sendError("PO VAT update error", 500);
            }

            DB::commit();
            return $this->sendResponse('', 'Purchase Order Details saved successfully');
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage(), 500);
        }
    }

    /**
     * Display the specified PurchaseOrderDetails.
     * GET|HEAD /purchaseOrderDetails/{id}
     *
     * @param int $id
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

        return $this->sendResponse($purchaseOrderDetails->toArray(), 'Purchase Order details retrieved successfully');
    }

    /**
     * Update the specified PurchaseOrderDetails in storage.
     * PUT/PATCH /purchaseOrderDetails/{id}
     *
     * @param int $id
     * @param UpdatePurchaseOrderDetailsAPIRequest $request
     *
     * @return Response
     */

    public function update($id, UpdatePurchaseOrderDetailsAPIRequest $request)
    {
        $input = array_except($request->all(), 'unit', 'vat_sub_category');

        if (isset($input['vat_sub_category'])) {
            unset($input['vat_sub_category']);
        }

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

        if (isset($input['madeLocallyYN']) && $input['madeLocallyYN']) {
            $input['madeLocallyYN'] = -1;
        } else {
            $input['madeLocallyYN'] = 0;
        }

        $purchaseOrderDetailsData = $this->purchaseOrderDetailsRepository->findWithoutFail($id);
        DB::beginTransaction();
        try {
            $validateVATCategories = TaxService::validateVatCategoriesInDocumentDetails($purchaseOrder->documentSystemID, $purchaseOrder->companySystemID, $id, $input);

            if (!$validateVATCategories['status']) {
                return $this->sendError($validateVATCategories['message'], 500,array('type' => 'no_qty_issues'));
            } else {
                $input['vatMasterCategoryID'] = $validateVATCategories['vatMasterCategoryID'];        
                $input['vatSubCategoryID'] = $validateVATCategories['vatSubCategoryID'];        
            }

            if (isset($input['vatSubCategoryID']) && $input['vatSubCategoryID'] > 0) {
                $subcategoryVAT = TaxVatCategories::find($input['vatSubCategoryID']);
                $input['exempt_vat_portion'] = (isset($input['exempt_vat_portion']) && $subcategoryVAT && $subcategoryVAT->subCatgeoryType == 1) ? $input['exempt_vat_portion'] : 0;
            }

            $input['VATAmount'] = isset($input['VATAmount']) ? $input['VATAmount'] : 0;
            $input['discountAmount'] = isset($input['discountAmount']) ? \Helper::roundValue($input['discountAmount']) : 0;
            $discountedUnitPrice = $input['unitCost']  - $input['discountAmount'];
            if(TaxService::checkPOVATEligible($purchaseOrder->supplierVATEligible, $purchaseOrder->vatRegisteredYN)){
                $discountedUnitPrice =  $discountedUnitPrice + $input['VATAmount'];
            }

            if ($discountedUnitPrice > 0) {
                $currencyConversion = \Helper::currencyConversion($input['companySystemID'], $purchaseOrder->supplierTransactionCurrencyID, $purchaseOrder->supplierTransactionCurrencyID, $discountedUnitPrice);

                $input['GRVcostPerUnitLocalCur'] = \Helper::roundValue($currencyConversion['localAmount']);
                $input['GRVcostPerUnitSupTransCur'] = $discountedUnitPrice;
                $input['GRVcostPerUnitComRptCur'] = \Helper::roundValue($currencyConversion['reportingAmount']);

                $input['purchaseRetcostPerUnitLocalCur'] = \Helper::roundValue($currencyConversion['localAmount']);
                $input['purchaseRetcostPerUnitTranCur'] = $discountedUnitPrice;
                $input['purchaseRetcostPerUnitRptCur'] = \Helper::roundValue($currencyConversion['reportingAmount']);
            }

            if (isset($input['VATAmount']) && $input['VATAmount'] > 0) {
                $currencyConversionVAT = \Helper::currencyConversion($input['companySystemID'], $purchaseOrder->supplierTransactionCurrencyID, $purchaseOrder->supplierTransactionCurrencyID, $input['VATAmount']);
                $input['VATAmountLocal'] = \Helper::roundValue($currencyConversionVAT['localAmount']);
                $input['VATAmountRpt'] = \Helper::roundValue($currencyConversionVAT['reportingAmount']);
                $input['VATAmount'] = \Helper::roundValue($input['VATAmount']);
            } else {
                $input['VATAmount'] = 0;
                $input['VATAmountLocal'] = 0;
                $input['VATAmountRpt'] = 0;
            }

            // adding supplier Default CurrencyID base currency conversion
            if ($discountedUnitPrice > 0) {
                $currencyConversionDefault = \Helper::currencyConversion($input['companySystemID'], $purchaseOrder->supplierTransactionCurrencyID, $purchaseOrder->supplierDefaultCurrencyID, $discountedUnitPrice);

                $input['GRVcostPerUnitSupDefaultCur'] = \Helper::roundValue($currencyConversionDefault['documentAmount']);
                $input['purchaseRetcostPerUniSupDefaultCur'] = \Helper::roundValue($currencyConversionDefault['documentAmount']);
            }

            $input['modifiedPc'] = gethostname();
            $input['modifiedUser'] = $user->employee['empID'];
            $updateMarkupBy = isset($input['updateMarkupBy']) ? $input['updateMarkupBy'] : '';

            $markupArray = $this->setMarkupPercentage($discountedUnitPrice, $purchaseOrder, $input['markupPercentage'], $input['markupTransactionAmount'], $updateMarkupBy);
            if(!$markupArray['success']) {
                return $this->sendError($markupArray['data'],500);
            }else {
                $markupArray = $markupArray['data'];
            }

            $input['markupPercentage'] = $markupArray['markupPercentage'];
            $input['markupTransactionAmount'] = $markupArray['markupTransactionAmount'];
            $input['markupLocalAmount'] = $markupArray['markupLocalAmount'];
            $input['markupReportingAmount'] = $markupArray['markupReportingAmount'];

            $purchaseOrderDetails = $this->purchaseOrderDetailsRepository->update($input, $id);
            $validateVATCategories = TaxService::validateVatCategoriesInDocumentDetails($purchaseOrder->documentSystemID, $purchaseOrder->companySystemID, $id, $input);

            TaxService::updatePOVAT($input['purchaseOrderMasterID']);
            //calculate tax amount according to the percantage for tax update

            //getting total sum of PO detail Amount
            $poMasterSum = PurchaseOrderDetails::select(DB::raw('COALESCE(SUM(netAmount),0) as masterTotalSum'))
                ->where('purchaseOrderMasterID', $input['purchaseOrderMasterID'])
                ->first();

            // updating master and detail table number of qty

            if (!empty($purchaseOrderDetails->purchaseRequestDetailsID) && !empty($purchaseOrderDetails->purchaseRequestID)) {

                //checking the fullyOrdered or partial in po
                $updatedPRQty = PurchaseOrderDetails::RequestDetailSum($purchaseOrderDetails->purchaseRequestDetailsID);


                $detailExistPRDetail = PurchaseRequestDetails::find($purchaseOrderDetails->purchaseRequestDetailsID);

                $checkQuentity = ($detailExistPRDetail->quantityRequested - $updatedPRQty);

                if ($checkQuentity < 0) {
                    return $this->sendError("PO qty cannot be greater than requested qty", 500,array('type' => 'no_qty_issues'));
                }

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
                    ->update(['selectedForPO' => $selectedForPO, 'fullyOrdered' => $fullyOrdered, 'poQuantity' => $updatedPRQty, 'prClosedYN' => $prClosedYN]);

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

            if ($purchaseOrder->documentSystemID == 5 && $purchaseOrder->poType_N == 6) {

                $mainWoTotal = ProcumentOrderDetail::where('purchaseOrderDetailsID', $purchaseOrderDetails->WP_purchaseOrderDetailsID)
                    ->sum('noQty');

                $subWoTotal = ProcumentOrderDetail::where('WO_purchaseOrderMasterID', $purchaseOrder->WO_purchaseOrderID)
                    ->where('itemCode', $purchaseOrderDetails->itemCode)
                    ->where('WP_purchaseOrderDetailsID', $purchaseOrderDetails->WP_purchaseOrderDetailsID)
                    ->sum('noQty');

                if ($subWoTotal > $mainWoTotal) {
                    DB::rollback();
                    return $this->sendError('Sub work order is exceeding the main work order total qty. Cannot amend.', 500);
                }

                $mainWoTotal = ProcumentOrderDetail::where('purchaseOrderDetailsID', $purchaseOrderDetails->WP_purchaseOrderDetailsID)
                    ->sum('netAmount');

                $subWoTotal = ProcumentOrderDetail::where('WO_purchaseOrderMasterID', $purchaseOrder->WO_purchaseOrderID)
                    ->where('itemCode', $purchaseOrderDetails->itemCode)
                    ->where('WP_purchaseOrderDetailsID', $purchaseOrderDetails->WP_purchaseOrderDetailsID)
                    ->sum('netAmount');

                if ($subWoTotal > $mainWoTotal) {
                    DB::rollback();
                    return $this->sendError('Sub work order is exceeding the main work order total amount. Cannot amend.', 500);
                }
            }

            if ($purchaseOrderDetailsData->noQty != $input['noQty']) {
                $checkAlreadyAllocated = SegmentAllocatedItem::where('serviceLineSystemID', '!=',$purchaseOrder->serviceLineSystemID)
                                                         ->where('documentSystemID', $purchaseOrder->documentSystemID)
                                                         ->where('documentMasterAutoID', $input['purchaseOrderMasterID'])
                                                         ->where('documentDetailAutoID', $id)
                                                         ->get();

                if (sizeof($checkAlreadyAllocated) == 0) {
                    $checkAlreadyAllocated = SegmentAllocatedItem::where('serviceLineSystemID',$purchaseOrder->serviceLineSystemID)
                                                         ->where('documentSystemID', $purchaseOrder->documentSystemID)
                                                         ->where('documentMasterAutoID', $input['purchaseOrderMasterID'])
                                                         ->where('documentDetailAutoID', $id)
                                                         ->delete();

                    $allocationData = [
                        'serviceLineSystemID' => $purchaseOrder->serviceLineSystemID,
                        'documentSystemID' => $purchaseOrder->documentSystemID,
                        'docAutoID' => $input['purchaseOrderMasterID'],
                        'docDetailID' => $id
                    ];

                    $segmentAllocatedItem = $this->segmentAllocatedItemRepository->allocateSegmentWiseItem($allocationData);

                    if (!$segmentAllocatedItem['status']) {
                        return $this->sendError($segmentAllocatedItem['message']);
                    }
                } else {
                     $allocatedQty = SegmentAllocatedItem::where('documentSystemID', $purchaseOrder->documentSystemID)
                                                 ->where('documentMasterAutoID', $input['purchaseOrderMasterID'])
                                                 ->where('documentDetailAutoID', $id)
                                                 ->sum('allocatedQty');

                    if ($allocatedQty > $input['noQty']) {
                        return $this->sendError("You cannot update the order quantity. since quantity has been allocated to segments", 500);
                    }
                }

                $checkAlreadyAssignedDeliveryDate = PoDetailExpectedDeliveryDate::where('po_detail_auto_id', $purchaseOrderDetailsData->purchaseOrderDetailsID)
                                                                                ->get();
                if (sizeof($checkAlreadyAssignedDeliveryDate) == 0) {
                    $checkAlreadyAssignedDeliveryDate = PoDetailExpectedDeliveryDate::where('po_detail_auto_id', $purchaseOrderDetailsData->purchaseOrderDetailsID)
                                                                                ->delete();
                    $deliveryDateData = [
                        'po_detail_auto_id' => $purchaseOrderDetailsData->purchaseOrderDetailsID,
                        'allocated_qty' => $input['noQty'],
                        'expected_delivery_date' => $purchaseOrder->expectedDeliveryDate,
                    ];

                    $createExpectedDeliveryDate = PoDetailExpectedDeliveryDate::create($deliveryDateData);
                } elseif (sizeof($checkAlreadyAssignedDeliveryDate) == 1) {

                    $deliveryDateData = [
                        'allocated_qty' => $input['noQty'],
                        'expected_delivery_date' => $purchaseOrder->expectedDeliveryDate,
                    ];
                    $getAlreadyAssignedDeliveryDate = PoDetailExpectedDeliveryDate::where('po_detail_auto_id', $purchaseOrderDetailsData->purchaseOrderDetailsID)
                                                                                ->first();
                    $updateExpectedDeliveryDate = PoDetailExpectedDeliveryDate::where('id', $getAlreadyAssignedDeliveryDate->id)
                                                                                ->update($deliveryDateData);
                }  else {
                    
                    $allocatedQty = PoDetailExpectedDeliveryDate::where('po_detail_auto_id', $purchaseOrderDetailsData->purchaseOrderDetailsID)
                                                                ->sum('allocated_qty');

                   if ($allocatedQty > $input['noQty']) {
                       return $this->sendError("You cannot update the order quantity. since quantity has been allocated to expected delivery dates", 500);
                   }
               }
            }

            DB::commit();
            return $this->sendResponse($purchaseOrderDetails->toArray(), 'Purchase Order Details updated successfully');
        } catch (\Exception $ex) {
            DB::rollback();
            return $this->sendError($ex->getMessage(), 500);
        }

    }

    /**
     * Remove the specified PurchaseOrderDetails from storage.
     * DELETE /purchaseOrderDetails/{id}
     *
     * @param int $id
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

        $checkSegmentAllocation = SegmentAllocatedItem::where('documentDetailAutoID', $id)
                                                      ->where('documentSystemID', $purchaseOrder->documentSystemID)
                                                      ->get();

        foreach ($checkSegmentAllocation as $key => $value) {
            if (!is_null($value->pulledDocumentDetailID)) {
                $segmentData = SegmentAllocatedItem::where('documentDetailAutoID', $value->pulledDocumentDetailID)
                                                   ->where('documentSystemID', $value->pulledDocumentSystemID)
                                                   ->where('serviceLineSystemID', $value->serviceLineSystemID)
                                                   ->first();

                if ($segmentData) {
                    $segmentData->copiedQty = floatval($segmentData->copiedQty) - floatval($value->allocatedQty);
                    $segmentData->save();
                }
            }
        }


        $purchaseOrderDetails->delete();

        $checkSegmentAllocation = SegmentAllocatedItem::where('documentDetailAutoID', $id)
                                                      ->where('documentSystemID', $purchaseOrder->documentSystemID)
                                                      ->delete();
        // updating master and detail table number of qty

        if (!empty($purchaseOrderDetails->purchaseRequestDetailsID) && !empty($purchaseOrderDetails->purchaseRequestID)) {
            $updatePRMaster = PurchaseRequest::find($purchaseOrderDetails->purchaseRequestID)
                ->update([
                    'selectedForPO' => 0,
                    'prClosedYN' => 0,
                    'supplyChainOnGoing' => 0,
                    'selectedForPOByEmpID' => null
                ]);

            // $detailExistPRDetail = PurchaseRequestDetails::find($purchaseOrderDetails->purchaseRequestDetailsID);

            //checking the fullyOrdered or partial in po
            $updatedPRQty = PurchaseOrderDetails::RequestDetailSum($purchaseOrderDetails->purchaseRequestDetailsID);

            //$poQty = $detailExistPRDetail->poQuantity - $purchaseOrderDetails->noQty;

            if ($updatedPRQty == 0) {
                $fullyOrdered = 0;
            } else {
                $fullyOrdered = 1;
            }

             PurchaseRequestDetails::where('purchaseRequestDetailsID', $purchaseOrderDetails->purchaseRequestDetailsID)
                ->update(['selectedForPO' => 0, 'fullyOrdered' => $fullyOrdered, 'poQuantity' => $updatedPRQty, 'prClosedYN' => 0]);
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

             ProcumentOrder::find($purchaseOrder->purchaseOrderID)
                ->update([
                    'VATAmount' => $calculatVatAmount,
                    'VATAmountLocal' => round($currencyConversionVatAmount['localAmount'], 8),
                    'VATAmountRpt' => round($currencyConversionVatAmount['reportingAmount'], 8)
                ]);
        }

        $detailsCount = PurchaseOrderDetails::where('purchaseOrderMasterID', $purchaseOrder->purchaseOrderID)
                                             ->count();

        if($detailsCount == 0){
            ProcumentOrder::find($purchaseOrder->purchaseOrderID)
                ->update([
                    'poDiscountPercentage' => 0,
                    'poDiscountAmount' => 0
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

        $purchaseOrder = ProcumentOrder::where('purchaseOrderID', $purchaseOrderID)
            ->first();

        if (empty($purchaseOrder)) {
            return $this->sendError('Procurement Order not found');
        }

        if (empty($detailExist)) {
            return $this->sendError('There are no details to delete');
        }
        if (!empty($detailExistAll)) {

            foreach ($detailExistAll as $cvDeatil) {

                $checkSegmentAllocation = SegmentAllocatedItem::where('documentDetailAutoID', $cvDeatil['purchaseOrderDetailsID'])
                                                      ->where('documentSystemID', $purchaseOrder->documentSystemID)
                                                      ->get();

                foreach ($checkSegmentAllocation as $key => $value) {
                    if (!is_null($value->pulledDocumentDetailID)) {
                        $segmentData = SegmentAllocatedItem::where('documentDetailAutoID', $value->pulledDocumentDetailID)
                                                           ->where('documentSystemID', $value->pulledDocumentSystemID)
                                                           ->where('serviceLineSystemID', $value->serviceLineSystemID)
                                                           ->first();

                        if ($segmentData) {
                            $segmentData->copiedQty = floatval($segmentData->copiedQty) - floatval($value->allocatedQty);
                            $segmentData->save();
                        }
                    }
                }


                $checkSegmentAllocation = SegmentAllocatedItem::where('documentDetailAutoID', $cvDeatil['purchaseOrderDetailsID'])
                                                              ->where('documentSystemID', $purchaseOrder->documentSystemID)
                                                              ->delete();

                $deleteDetails = PurchaseOrderDetails::where('purchaseOrderDetailsID', $cvDeatil['purchaseOrderDetailsID'])->delete();

                if (!empty($cvDeatil['purchaseRequestDetailsID']) && !empty($cvDeatil['purchaseRequestID'])) {
                    $updatePRMaster = PurchaseRequest::find($cvDeatil['purchaseRequestID'])
                        ->update([
                            'selectedForPO' => 0,
                            'prClosedYN' => 0,
                            'supplyChainOnGoing' => 0,
                            'selectedForPOByEmpID' => null
                        ]);

                    //$detailExistPRDetail = PurchaseRequestDetails::find($cvDeatil['purchaseRequestDetailsID']);

                    //$poQty = ($detailExistPRDetail->poQuantity - $cvDeatil['noQty']);

                    //checking the fullyOrdered or partial in po
                    $updatedPRQty = PurchaseOrderDetails::RequestDetailSum($cvDeatil['purchaseRequestDetailsID']);;

                    if ($updatedPRQty == 0) {
                        $fullyOrdered = 0;
                    } else {
                        $fullyOrdered = 1;
                    }

                    $updateDetail = PurchaseRequestDetails::where('purchaseRequestDetailsID', $cvDeatil['purchaseRequestDetailsID'])
                        ->update(['selectedForPO' => 0, 'fullyOrdered' => $fullyOrdered, 'poQuantity' => $updatedPRQty, 'prClosedYN' => 0]);
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
                'poDiscountPercentage' => 0,
                'VATAmount' => 0,
                'VATAmountLocal' => 0,
                'VATAmountRpt' => 0
            ]);

        return $this->sendResponse($purchaseOrderID, 'Purchase Order Details deleted successfully');
    }

    public function procumentOrderTotalDiscountUD(Request $request)
    {
        $input = $request->all();

        $purchaseOrderID = isset($input['purchaseOrderID']) ? $input['purchaseOrderID'] : 0;
        $discountAmount = isset($input['discount']) ? $input['discount'] : 0;
        $poDiscountPercentage = isset($input['poDiscountPercentage']) ? $input['poDiscountPercentage'] : 0;
        $purchaseOrder = ProcumentOrder::where('purchaseOrderID', $purchaseOrderID)
            ->first();

        if (empty($purchaseOrder)) {
            return $this->sendError('Purchase Order not found');
        }

        $purchaseOrder->update(
            [
                'poDiscountAmount' => $discountAmount,
                'poDiscountPercentage' => $poDiscountPercentage
            ]
        );

        $updateDetailDiscount = PurchaseOrderDetails::where('purchaseOrderMasterID', $purchaseOrderID)
            ->get();
        if (!empty($updateDetailDiscount)) {

            foreach ($updateDetailDiscount as $itemDiscont) {

                if ($itemDiscont['noQty'] != 0 && $input['netTotal'] != 0) {
                    $calculateItemDiscount = (($itemDiscont['netAmount'] - (($input['discount'] / $input['netTotal']) * $itemDiscont['netAmount'])) / $itemDiscont['noQty']);

                    $currencyConversion = \Helper::currencyConversion($itemDiscont['companySystemID'], $purchaseOrder->supplierTransactionCurrencyID, $purchaseOrder->supplierTransactionCurrencyID, $calculateItemDiscount);

                    $detail['GRVcostPerUnitLocalCur'] = \Helper::roundValue($currencyConversion['localAmount']);
                    $detail['GRVcostPerUnitSupTransCur'] = $calculateItemDiscount;
                    $detail['GRVcostPerUnitComRptCur'] = \Helper::roundValue($currencyConversion['reportingAmount']);

                    $detail['purchaseRetcostPerUnitLocalCur'] = \Helper::roundValue($currencyConversion['localAmount']);
                    $detail['purchaseRetcostPerUnitTranCur'] = $calculateItemDiscount;
                    $detail['purchaseRetcostPerUnitRptCur'] = \Helper::roundValue($currencyConversion['reportingAmount']);
                   // $this->purchaseOrderDetailsRepository->update($detail, $itemDiscont['purchaseOrderDetailsID']);
                }

                //$detail['netAmount'] = $calculateItemDiscount * $itemDiscont['noQty'];


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

                return $this->sendResponse($purchaseOrderID, 'Total VAT updated successfully');

            } else {
                return $this->sendResponse($purchaseOrderID, 'Total VAT updated successfully');
            }
        } else {
            return $this->sendResponse($purchaseOrderID, 'Total VAT updated successfully');
        }
    }

    public function getPurchaseOrderDetailForGRV(Request $request)
    {
        $input = $request->all();
        $poID = $input['purchaseOrderID'];

        $detail = PurchaseOrderDetails::select(DB::raw('itemPrimaryCode,itemDescription,supplierPartNumber,"" as isChecked, "" as noQty,noQty as poQty,unitOfMeasure,purchaseOrderMasterID,purchaseOrderDetailsID,serviceLineCode,itemCode,companySystemID,companyID,serviceLineCode,itemPrimaryCode,itemDescription,itemFinanceCategoryID,itemFinanceCategorySubID,financeGLcodebBSSystemID,financeGLcodebBS,financeGLcodePLSystemID,financeGLcodePL,includePLForGRVYN,supplierPartNumber,unitOfMeasure,unitCost,discountPercentage,discountAmount,netAmount,comment,supplierDefaultCurrencyID,supplierDefaultER,supplierItemCurrencyID,foreignToLocalER,companyReportingCurrencyID,companyReportingER,localCurrencyID,localCurrencyER,addonDistCost,GRVcostPerUnitLocalCur,GRVcostPerUnitSupDefaultCur,GRVcostPerUnitSupTransCur,GRVcostPerUnitComRptCur,VATPercentage,VATAmount,VATAmountLocal,VATAmountRpt,receivedQty,markupPercentage,markupTransactionAmount,markupLocalAmount,markupReportingAmount, vatMasterCategoryID,vatSubCategoryID, exempt_vat_portion'))
            ->with(['unit' => function ($query) {
            }])
            ->where('purchaseOrderMasterID', $poID)
            ->where('GRVSelectedYN', 0)
            ->where('goodsRecievedYN', '<>', 2)
            ->where('manuallyClosed', 0)
            ->whereIn('itemFinanceCategoryID',[1,2,4])
            ->get();




        return $this->sendResponse($detail, 'Purchase Order Details retrieved successfully');

    }

    public function setMarkupPercentage($unitCost, $poData, $markupPercentage = 0, $markupTransAmount = 0, $by = '')
    {

        $output['markupPercentage'] = 0;
        $output['markupTransactionAmount'] = 0;
        $output['markupLocalAmount'] = 0;
        $output['markupReportingAmount'] = 0;

        if (isset($poData->supplierID) && $poData->supplierID) {

            $supplierMaster = SupplierMaster::where('supplierCodeSystem', $poData->supplierID)->first();
            $supplier = SupplierAssigned::where('supplierCodeSytem', $poData->supplierID)
                ->where('companySystemID', $poData->companySystemID)
                ->where('isActive', 1)
                ->where('isAssigned', -1)
                ->first();


            if(!$supplierMaster->isActive)
                return ["success" => false , 'data' => "Supplier is not active"];

            if(!isset($supplier))
                return ["success" => false , 'data' => "Supplier is not assigned to the company"];

            if ($supplier->companySystemID && $supplier->isMarkupPercentage) {
                $hasEEOSSPolicy = CompanyPolicyMaster::where('companySystemID', $supplier->companySystemID)
                    ->where('companyPolicyCategoryID', 41)
                    ->where('isYesNO', 1)
                    ->exists();

                if ($hasEEOSSPolicy) {

                    if ($by == 'amount') {
                        $output['markupTransactionAmount'] = $markupTransAmount;
                        if ($unitCost > 0 && $markupTransAmount > 0) {
                            $output['markupPercentage'] = $markupTransAmount * 100 / $unitCost;
                        }
                    } else {
                        $percentage = ($markupPercentage != 0) ? $markupPercentage : $supplier->markupPercentage;
                        if ($percentage != 0) {
                            $output['markupPercentage'] = $percentage;
                            if ($unitCost > 0) {
                                $output['markupTransactionAmount'] = $percentage * $unitCost / 100;
                            }
                        }
                    }

                    if ($output['markupTransactionAmount'] > 0) {
                        if ($poData->supplierTransactionCurrencyID != $poData->localCurrencyID) {
                            $currencyConversion = Helper::currencyConversion($poData->companySystemID, $poData->supplierTransactionCurrencyID, $poData->localCurrencyID, $output['markupTransactionAmount']);
                            if (!empty($currencyConversion)) {
                                $output['markupLocalAmount'] = $currencyConversion['documentAmount'];
                            }
                        } else {
                            $output['markupLocalAmount'] = $output['markupTransactionAmount'];
                        }

                        if ($poData->supplierTransactionCurrencyID != $poData->companyReportingCurrencyID) {
                            $currencyConversion = Helper::currencyConversion($poData->companySystemID, $poData->supplierTransactionCurrencyID, $poData->companyReportingCurrencyID, $output['markupTransactionAmount']);
                            if (!empty($currencyConversion)) {
                                $output['markupReportingAmount'] = $currencyConversion['documentAmount'];
                            }
                        } else {
                            $output['markupReportingAmount'] = $output['markupTransactionAmount'];
                        }

                        /*round to 7 decimals*/
                        $output['markupTransactionAmount'] = Helper::roundValue($output['markupTransactionAmount']);
                        $output['markupLocalAmount'] = Helper::roundValue($output['markupLocalAmount']);
                        $output['markupReportingAmount'] = Helper::roundValue($output['markupReportingAmount']);

                    }


                }

            }

        }

        return ["success" => true , 'data' => $output];
    }


    public function validateItemAlllocationInPO(Request $request)
    {
        $input = $request->all();
        $validated = true;
        $finalArray = [];
        foreach ($input['detailTable'] as $key => $value) {
            $allocationMappingArray = [];
            if (isset($value['isChecked']) && $value['isChecked']) {
                $purchaseRequest = PurchaseRequest::find($value['purchaseRequestID']);

                if ($purchaseRequest && $value['quantityRequested'] > $value['poQty']) {
                    $checkAllocation = SegmentAllocatedItem::selectRaw('(allocatedQty - copiedQty) as remaingQty, serviceLineSystemID, documentMasterAutoID, id, documentDetailAutoID, documentSystemID')
                                                           ->with(['segment'])
                                                           ->where('documentMasterAutoID', $value['purchaseRequestID'])
                                                           ->where('documentSystemID', $purchaseRequest->documentSystemID)
                                                           ->where('documentDetailAutoID', $value['purchaseRequestDetailsID'])
                                                           ->get();

                    if (count($checkAllocation) > 1) {
                        foreach ($checkAllocation as $key1 => $value1) {
                            $allocationMappingArray[] = $value1;
                            $validated = false;
                        }
                    }
                }
            }

            if (count($allocationMappingArray) > 0) {
                $value['allocationMappingArray'] = $allocationMappingArray;

                $finalArray[] = $value;
            }
        }

        return $this->sendResponse(['allocationMappingArray' => $finalArray, 'validated' => $validated], "Allocation validated successfully");
    }

    public function purchaseOrderValidateItem(Request $request)
    {
        $input = $request->all();

        return ProcurementOrderService::validatePoItem($input['itemCodeSystem'], $input['companySystemID'], $input['purchaseOrderID']);
    }

    public function purchaseOrderDetailsAddAllItems(Request $request)
    {
        $input = $request->all();
        $id = Auth::id();
        $user = $this->userRepository->with(['employee'])->findWithoutFail($id);

        $input['employeeSystemID'] = $user ? $user->employee['employeeSystemID'] : null;
        $input['empID'] = $user ? $user->employee['empID'] : null;

        if (isset($input['addAllItems']) && $input['addAllItems']) {
            $db = isset($input['db']) ? $input['db'] : "";    

            $purchaseOrder = ProcumentOrder::where('purchaseOrderID', $input['purchaseOrderID'])
                                            ->first();

            if (empty($purchaseOrder)) {
                return $this->sendError('Purchase Order not found', 500);
            }
            $allowFinanceCategory = CompanyPolicyMaster::where('companyPolicyCategoryID', 20)
                ->where('companySystemID', $purchaseOrder->companySystemID)
                ->first();
            if ($allowFinanceCategory) {
                $policy = $allowFinanceCategory->isYesNO;
                if ($policy == 0) {
                    if ($purchaseOrder->financeCategory == null || $purchaseOrder->financeCategory == 0) {
                        return $this->sendError('Category is not found', 500);
                    }

                    //checking if item category is same or not
                    $pRDetailExistSameItem = ProcumentOrderDetail::select(DB::raw('DISTINCT(itemFinanceCategoryID) as itemFinanceCategoryID'))
                        ->where('purchaseOrderMasterID', $input['purchaseOrderID'])
                        ->first();

                    if ($pRDetailExistSameItem) {
                        if ($item->financeCategoryMaster != $pRDetailExistSameItem["itemFinanceCategoryID"]) {
                            return $this->sendError('You cannot add different category item', 500);
                        }
                    }
                }
            }


            $data['isBulkItemJobRun'] = 1;

            $purchaseRequest = ProcumentOrder::where('purchaseOrderID', $input['purchaseOrderID'])->update($data);
            PoAddBulkItemJob::dispatch($db, $input);

            return $this->sendResponse('', 'Items Added to Queue Please wait some minutes to process');
        } else {
            DB::beginTransaction();
            try {
                $invalidItems = [];
                foreach ($input['itemArray'] as $key => $value) {
                    $res = ProcurementOrderService::validatePoItem($value['itemCodeSystem'], $input['companySystemID'], $input['purchaseOrderID']);
                    
                    if ($res['status']) {
                        ProcurementOrderService::savePoItem($value['itemCodeSystem'], $input['companySystemID'], $input['purchaseOrderID'], $input['empID'], $input['employeeSystemID']);
                    } else {
                        $invalidItems[] = ['itemCodeSystem' => $value['itemCodeSystem'], 'message' => $res['message']];
                    }
                }
                DB::commit();
                return $this->sendResponse('', 'Purchase Order Items saved successfully');
            } catch (\Exception $exception) {
                DB::rollBack();
                return $this->sendError($exception->getMessage(), 500);
            }
        }
    }
}
