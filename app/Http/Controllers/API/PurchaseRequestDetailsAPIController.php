<?php
/**
 * =============================================
 * -- File Name : PurchaseRequestDetailsAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Purchase Request Details
 * -- Author : Mohamed Fayas
 * -- Create date : 29 - March 2018
 * -- Description : This file contains the all CRUD for Purchase Request Details
 * -- REVISION HISTORY
 * -- Date: 29-March 2018 By: Fayas Description: Added new functions named as getItemsByPurchaseRequest()
 * -- Date: 29-Oct 2019 By: Rilwan Description: Added new functions named as getQtyOrderDetails()
 */
namespace App\Http\Controllers\API;

use App\helper\Helper;
use App\Http\Requests\API\CreatePurchaseRequestDetailsAPIRequest;
use App\Http\Requests\API\UpdatePurchaseRequestDetailsAPIRequest;
use App\Models\Company;
use App\Models\CompanyPolicyMaster;
use App\Models\ErpItemLedger;
use App\Models\AssetFinanceCategory;
use App\Models\FinanceItemcategorySubAssigned;
use App\Models\GRVDetails;
use App\Models\SegmentAllocatedItem;
use App\Models\ItemAssigned;
use App\Models\ProcumentOrder;
use App\Models\PurchaseOrderDetails;
use App\Models\PurchaseRequest;
use App\Models\PurchaseRequestDetails;
use App\Repositories\SegmentAllocatedItemRepository;
use App\Repositories\PurchaseRequestDetailsRepository;
use App\Repositories\PurchaseRequestRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;


/**
 * Class PurchaseRequestDetailsController
 * @package App\Http\Controllers\API
 */
class PurchaseRequestDetailsAPIController extends AppBaseController
{
    /** @var  PurchaseRequestDetailsRepository */
    private $purchaseRequestDetailsRepository;
    private $segmentAllocatedItemRepository;
    private $purchaseRequestRepository;

    public function __construct(PurchaseRequestDetailsRepository $purchaseRequestDetailsRepo, PurchaseRequestRepository $purchaseRequestRepo, SegmentAllocatedItemRepository $segmentAllocatedItemRepo)
    {
        $this->purchaseRequestDetailsRepository = $purchaseRequestDetailsRepo;
        $this->purchaseRequestRepository = $purchaseRequestRepo;
        $this->segmentAllocatedItemRepository = $segmentAllocatedItemRepo;
    }

    /**
     * Display a listing of the PurchaseRequestDetails.
     * GET|HEAD /purchaseRequestDetails
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->purchaseRequestDetailsRepository->pushCriteria(new RequestCriteria($request));
        $this->purchaseRequestDetailsRepository->pushCriteria(new LimitOffsetCriteria($request));
        $purchaseRequestDetails = $this->purchaseRequestDetailsRepository->all();

        return $this->sendResponse($purchaseRequestDetails->toArray(), 'Purchase Request Details retrieved successfully');
    }


    /**
     * Display a listing of the items by Purchase Request.
     * GET|HEAD /getItemsByPurchaseRequest
     *
     * @param Request $request
     * @return Response
     */
    public function getItemsByPurchaseRequest(Request $request)
    {
        $input = $request->all();
        $prId = $input['purchaseRequestId'];

        $items = PurchaseRequestDetails::where('purchaseRequestID', $prId)
            ->with(['uom'])
            ->get();

        return $this->sendResponse($items->toArray(), 'Purchase Request Details retrieved successfully');
    }

    /**
     * Store a newly created PurchaseRequestDetails in storage.
     * POST /purchaseRequestDetails
     *
     * @param CreatePurchaseRequestDetailsAPIRequest $request
     *
     * @return Response
     */
    public function store(CreatePurchaseRequestDetailsAPIRequest $request)
    {
        $input = array_except($request->all(), 'uom');
        $input = $this->convertArrayToValue($input);


        $companySystemID = $input['companySystemID'];

        $allowItemToTypePolicy = false;
        $itemNotound = false;
        $allowItemToType = CompanyPolicyMaster::where('companyPolicyCategoryID', 53)
                                            ->where('companySystemID', $companySystemID)
                                            ->first();

        if ($allowItemToType) {
            if ($allowItemToType->isYesNO) {
                $allowItemToTypePolicy = true;
            }
        }


        if ($allowItemToTypePolicy) {
            $input['itemCode'] = isset($input['itemCode']['id']) ? $input['itemCode']['id'] : $input['itemCode'];
        }

        $item = ItemAssigned::where('itemCodeSystem', $input['itemCode'])
            ->where('companySystemID', $companySystemID)
            ->first();

        if (empty($item)) {
            if (!$allowItemToTypePolicy) {
                return $this->sendError('Item not found');
            } else {
                $itemNotound = true;
            }
        }

        $purchaseRequest = PurchaseRequest::where('purchaseRequestID', $input['purchaseRequestID'])
            ->first();


        if (empty($purchaseRequest)) {
            return $this->sendError('Purchase Request Details not found');
        }

        if ($purchaseRequest->cancelledYN == -1) {
            return $this->sendError('This Purchase Request already closed. You can not add.', 500);
        }

        if ($purchaseRequest->approved == 1) {
            return $this->sendError('This Purchase Request fully approved. You can not add.', 500);
        }

        $input['budgetYear'] = $purchaseRequest->budgetYear;
        $input['itemPrimaryCode'] = (!$itemNotound) ? $item->itemPrimaryCode : null;
        $input['itemDescription'] = (!$itemNotound) ? $item->itemDescription : $input['itemCode'];
        $input['partNumber'] = (!$itemNotound) ? $item->secondaryItemCode : null;
        $input['itemFinanceCategoryID'] = (!$itemNotound) ? $item->financeCategoryMaster : null;
        $input['itemFinanceCategorySubID'] = (!$itemNotound) ? $item->financeCategorySub : null;
        //$input['estimatedCost'] = $item->wacValueLocal;

        if (!$itemNotound) {
            $currencyConversion = \Helper::currencyConversion($item->companySystemID, $item->wacValueLocalCurrencyID, $purchaseRequest->currency, $item->wacValueLocal);
            $input['estimatedCost'] = $currencyConversion['documentAmount'];
            $input['companySystemID'] = $item->companySystemID;
            $input['companyID'] = $item->companyID;
            $input['unitOfMeasure'] = $item->itemUnitOfMeasure;
            $input['maxQty'] = $item->maximunQty;
            $input['minQty'] = $item->minimumQty;
            
            $financeItemCategorySubAssigned = FinanceItemcategorySubAssigned::where('companySystemID', $item->companySystemID)
                ->where('mainItemCategoryID', $item->financeCategoryMaster)
                ->where('itemCategorySubID', $item->financeCategorySub)
                ->first();

            if (empty($financeItemCategorySubAssigned)) {
                return $this->sendError('Finance category not assigned for the selected item.');
            }

            if ($item->financeCategoryMaster == 1) {

                $alreadyAdded = PurchaseRequest::where('purchaseRequestID', $input['purchaseRequestID'])
                    ->whereHas('details', function ($query) use ($companySystemID, $purchaseRequest, $item) {
                        $query->where('itemPrimaryCode', $item->itemPrimaryCode);
                    })
                    ->first();

                if ($alreadyAdded) {
                    return $this->sendError("Selected item is already added. Please check again", 500);
                }
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
            
            $allowFinanceCategory = CompanyPolicyMaster::where('companyPolicyCategoryID', 20)
                    ->where('companySystemID', $purchaseRequest->companySystemID)
                    ->first();

            if ($allowFinanceCategory) {
                $policy = $allowFinanceCategory->isYesNO;

                if ($policy == 0) {
                    if ($purchaseRequest->financeCategory == null || $purchaseRequest->financeCategory == 0) {
                        return $this->sendError('Category is not found.', 500);
                    }

                    //checking if item category is same or not
                    $pRDetailExistSameItem = PurchaseRequestDetails::select(DB::raw('DISTINCT(itemFinanceCategoryID) as itemFinanceCategoryID'))
                        ->where('purchaseRequestID', $purchaseRequest->purchaseRequestID)
                        ->first();

                    if ($pRDetailExistSameItem) {
                        if ($item->financeCategoryMaster != $pRDetailExistSameItem["itemFinanceCategoryID"]) {
                            return $this->sendError('You cannot add different category item', 500);
                        }
                    }
                }
            }

              // check policy 18

            $allowPendingApproval = CompanyPolicyMaster::where('companyPolicyCategoryID', 18)
                ->where('companySystemID', $companySystemID)
                ->first();

            if ($allowPendingApproval && $item->financeCategoryMaster == 1) {

                if ($allowPendingApproval->isYesNO == 0) {

                    $checkWhether = PurchaseRequest::where('purchaseRequestID', '!=', $purchaseRequest->purchaseRequestID)
                        ->where('companySystemID', $companySystemID)
                        ->where('serviceLineSystemID', $purchaseRequest->serviceLineSystemID)
                        ->select([
                            'erp_purchaserequest.purchaseRequestID',
                            'erp_purchaserequest.companySystemID',
                            'erp_purchaserequest.serviceLineCode',
                            'erp_purchaserequest.purchaseRequestCode',
                            'erp_purchaserequest.PRConfirmedYN',
                            'erp_purchaserequest.approved',
                            'erp_purchaserequest.cancelledYN'
                        ])
                        ->groupBy(
                            'erp_purchaserequest.purchaseRequestID',
                            'erp_purchaserequest.companySystemID',
                            'erp_purchaserequest.serviceLineCode',
                            'erp_purchaserequest.purchaseRequestCode',
                            'erp_purchaserequest.PRConfirmedYN',
                            'erp_purchaserequest.approved',
                            'erp_purchaserequest.cancelledYN'
                        );

                    $anyPendingApproval = $checkWhether->whereHas('details', function ($query) use ($companySystemID, $purchaseRequest, $item) {
                        $query->where('itemPrimaryCode', $item->itemPrimaryCode)
                                       ->where('manuallyClosed', 0);
                        /* $query->groupBy(
                             'erp_purchaserequestdetails.itemCode',
                             'erp_purchaserequestdetails.itemPrimaryCode',
                             'erp_purchaserequestdetails.selectedForPO',
                             'erp_purchaserequestdetails.prClosedYN',
                             'erp_purchaserequestdetails.fullyOrdered'
                         )->select([
                         'erp_purchaserequestdetails.itemCode',
                         'erp_purchaserequestdetails.itemPrimaryCode',
                         'erp_purchaserequestdetails.selectedForPO',
                         'erp_purchaserequestdetails.prClosedYN',
                         'erp_purchaserequestdetails.fullyOrdered'
                      ]);*/
                    })
                        ->where('approved', 0)
                        ->where('cancelledYN', 0)
                        ->first();
                    /* approved=0 And cancelledYN=0*/

                    if (!empty($anyPendingApproval)) {
                        return $this->sendError("There is a purchase request (" . $anyPendingApproval->purchaseRequestCode . ") pending for approval for the item you are trying to add. Please check again.", 500);
                    }

                    $anyApprovedPRButPONotProcessed = PurchaseRequest::where('purchaseRequestID', '!=', $purchaseRequest->purchaseRequestID)
                        ->where('companySystemID', $companySystemID)
                        ->where('serviceLineSystemID', $purchaseRequest->serviceLineSystemID)
                        ->select([
                            'erp_purchaserequest.purchaseRequestID',
                            'erp_purchaserequest.companySystemID',
                            'erp_purchaserequest.serviceLineCode',
                            'erp_purchaserequest.purchaseRequestCode',
                            'erp_purchaserequest.PRConfirmedYN',
                            'erp_purchaserequest.approved',
                            'erp_purchaserequest.cancelledYN'
                        ])
                        ->groupBy(
                            'erp_purchaserequest.purchaseRequestID',
                            'erp_purchaserequest.companySystemID',
                            'erp_purchaserequest.serviceLineCode',
                            'erp_purchaserequest.purchaseRequestCode',
                            'erp_purchaserequest.PRConfirmedYN',
                            'erp_purchaserequest.approved',
                            'erp_purchaserequest.cancelledYN'
                        )
                        ->whereHas('details', function ($query) use ($companySystemID, $purchaseRequest, $item) {
                            $query->where('itemPrimaryCode', $item->itemPrimaryCode)
                                ->where('selectedForPO', 0)
                                ->where('prClosedYN', 0)
                                ->where('fullyOrdered', 0)
                                ->where('manuallyClosed', 0);

                            /* $query->groupBy(
                                 'erp_purchaserequestdetails.itemCode',
                                 'erp_purchaserequestdetails.itemPrimaryCode',
                                 'erp_purchaserequestdetails.selectedForPO',
                                 'erp_purchaserequestdetails.prClosedYN',
                                 'erp_purchaserequestdetails.fullyOrdered'
                             )->select([
                             'erp_purchaserequestdetails.itemCode',
                             'erp_purchaserequestdetails.itemPrimaryCode',
                             'erp_purchaserequestdetails.selectedForPO',
                             'erp_purchaserequestdetails.prClosedYN',
                             'erp_purchaserequestdetails.fullyOrdered'
                          ]);*/
                        })
                        ->where('approved', -1)
                        ->where('cancelledYN', 0)
                        ->first();
                    /* approved=-1 And cancelledYN=0 And selectedForPO=0 And prClosedYN=0 And fullyOrdered=0*/

                    if (!empty($anyApprovedPRButPONotProcessed)) {
                        return $this->sendError("There is a purchase request (" . $anyApprovedPRButPONotProcessed->purchaseRequestCode . ") approved hense PO is not processed for the item you are trying to add. Please check again", 500);
                    }

                    $anyApprovedPRButPOPartiallyProcessed = PurchaseRequest::where('purchaseRequestID', '!=', $purchaseRequest->purchaseRequestID)
                        ->where('companySystemID', $companySystemID)
                        ->where('serviceLineSystemID', $purchaseRequest->serviceLineSystemID)
                        ->select([
                            'erp_purchaserequest.purchaseRequestID',
                            'erp_purchaserequest.companySystemID',
                            'erp_purchaserequest.serviceLineCode',
                            'erp_purchaserequest.purchaseRequestCode',
                            'erp_purchaserequest.PRConfirmedYN',
                            'erp_purchaserequest.approved',
                            'erp_purchaserequest.cancelledYN'
                        ])
                        ->groupBy(
                            'erp_purchaserequest.purchaseRequestID',
                            'erp_purchaserequest.companySystemID',
                            'erp_purchaserequest.serviceLineCode',
                            'erp_purchaserequest.purchaseRequestCode',
                            'erp_purchaserequest.PRConfirmedYN',
                            'erp_purchaserequest.approved',
                            'erp_purchaserequest.cancelledYN'
                        )->whereHas('details', function ($query) use ($companySystemID, $purchaseRequest, $item) {
                            $query->where('itemPrimaryCode', $item->itemPrimaryCode)
                                ->where('selectedForPO', 0)
                                ->where('prClosedYN', 0)
                                ->where('fullyOrdered', 1)
                                ->where('manuallyClosed', 0);
                            /* $query->groupBy(
                                 'erp_purchaserequestdetails.itemCode',
                                 'erp_purchaserequestdetails.itemPrimaryCode',
                                 'erp_purchaserequestdetails.selectedForPO',
                                 'erp_purchaserequestdetails.prClosedYN',
                                 'erp_purchaserequestdetails.fullyOrdered'
                             )->select([
                             'erp_purchaserequestdetails.itemCode',
                             'erp_purchaserequestdetails.itemPrimaryCode',
                             'erp_purchaserequestdetails.selectedForPO',
                             'erp_purchaserequestdetails.prClosedYN',
                             'erp_purchaserequestdetails.fullyOrdered'
                          ]);*/
                        })
                        ->where('approved', -1)
                        ->where('cancelledYN', 0)
                        ->first();
                    /* approved=-1 And cancelledYN=0 And selectedForPO=0 And prClosedYN=0 And fullyOrdered=1*/

                    if (!empty($anyApprovedPRButPOPartiallyProcessed)) {
                        return $this->sendError("There is a purchase request (" . $anyApprovedPRButPOPartiallyProcessed->purchaseRequestCode . ") approved and PO is partially processed for the item you are trying to add. Please check again", 500);
                    }

                    /* PO check*/

                    $checkPOPending = ProcumentOrder::where('companySystemID', $companySystemID)
                        ->where('serviceLineSystemID', $purchaseRequest->serviceLineSystemID)
                        ->whereHas('detail', function ($query) use ($item) {
                            $query->where('itemPrimaryCode', $item->itemPrimaryCode)
                                   ->where('manuallyClosed', 0);
                        })
                        ->where('approved', 0)
                        ->where('poCancelledYN', 0)
                        ->first();

                    if (!empty($checkPOPending)) {
                        return $this->sendError("There is a purchase order (" . $checkPOPending->purchaseOrderCode . ") pending for approval for the item you are trying to add. Please check again.", 500);
                    }
                    /* PO --> approved=-1 And cancelledYN=0 */

                }
            }


            $group_companies = Helper::getSimilarGroupCompanies($companySystemID);
            $poQty = PurchaseOrderDetails::whereHas('order', function ($query) use ($group_companies) {
                $query->whereIn('companySystemID', $group_companies)
                    ->where('approved', -1)
                    ->where('poType_N', '!=',5)// poType_N = 5 =>work order
                    ->where('poCancelledYN', 0)
                    ->where('manuallyClosed', 0);
                 })
                ->where('itemCode', $input['itemCode'])
                ->where('manuallyClosed',0)
                ->groupBy('erp_purchaseorderdetails.itemCode')
                ->select(
                    [
                        'erp_purchaseorderdetails.companySystemID',
                        'erp_purchaseorderdetails.itemCode',
                        'erp_purchaseorderdetails.itemPrimaryCode'
                    ]
                )
                ->sum('noQty');

            $quantityInHand = ErpItemLedger::where('itemSystemCode', $input['itemCode'])
                ->where('companySystemID', $companySystemID)
                ->groupBy('itemSystemCode')
                ->sum('inOutQty');

            $grvQty = GRVDetails::whereHas('grv_master', function ($query) use ($group_companies) {
                $query->whereIn('companySystemID', $group_companies)
                    ->where('grvTypeID', 2)
                    ->where('approved', -1)
                    ->groupBy('erp_grvmaster.companySystemID');
            })->whereHas('po_detail', function ($query){
                $query->where('manuallyClosed',0)
                ->whereHas('order', function ($query){
                    $query->where('manuallyClosed',0);
                });
            })
                ->where('itemCode', $input['itemCode'])
                ->groupBy('erp_grvdetails.itemCode')
                ->select(
                    [
                        'erp_grvdetails.companySystemID',
                        'erp_grvdetails.itemCode'
                    ])
                ->sum('noQty');

            $quantityOnOrder = $poQty - $grvQty;
            $input['poQuantity'] = $poQty;
            $input['quantityOnOrder'] = $quantityOnOrder;
            $input['quantityInHand'] = $quantityInHand;


        } else {
            $input['estimatedCost'] = 0;
            $input['companySystemID'] = $companySystemID;
            $input['companyID'] = $purchaseRequest->companyID;
            $input['unitOfMeasure'] = null;
            $input['maxQty'] = 0;
            $input['minQty'] = 0;
            $input['poQuantity'] = 0;
            $input['quantityOnOrder'] = 0;
            $input['quantityInHand'] = 0;
            $input['itemCode'] = null;
        }

        $input['itemCategoryID'] = 0;

        $purchaseRequestDetails = $this->purchaseRequestDetailsRepository->create($input);

        return $this->sendResponse($purchaseRequestDetails->toArray(), 'Purchase Request Details saved successfully');
    }


    public function mapLineItemPr(Request $request)
    {
        $input = $request->all();

        $checkItem = PurchaseOrderDetails::where('itemCode', $input['itemCodeNew'])
                                         ->where('purchaseRequestDetailsID', '!=', $input['purchaseRequestDetailsID'])
                                         ->where('purchaseOrderMasterID', $input['purchaseOrderID'])
                                         ->first();

        if ($checkItem) {
            return $this->sendError('This item has already maped with another item of this purchase request');
        }

        $checkForPoItem = PurchaseOrderDetails::where('purchaseRequestDetailsID', $input['purchaseRequestDetailsID'])
                                         ->first();

        if ($checkForPoItem) {
            $input['itemCodeNew'] = $checkForPoItem->itemCode;
        }

        $companySystemID = $input['companySystemID'];
        $item = ItemAssigned::where('itemCodeSystem', $input['itemCodeNew'])
            ->where('companySystemID', $companySystemID)
            ->first();

        if (empty($item)) {
            return $this->sendError('Item not found');
        }

        $purchaseRequest = PurchaseRequest::where('purchaseRequestID', $input['purchaseRequestID'])
            ->first();


        if (empty($purchaseRequest)) {
            return $this->sendError('Purchase Request Details not found');
        }


        $input['itemCode'] = $input['itemCodeNew'];

        $input['itemPrimaryCode'] = $item->itemPrimaryCode;
        $input['itemDescription'] = $item->itemDescription;
        $input['partNumber'] = $item->secondaryItemCode;
        $input['itemFinanceCategoryID'] =  $item->financeCategoryMaster;
        $input['itemFinanceCategorySubID'] = $item->financeCategorySub;

        $input['companySystemID'] = $item->companySystemID;
        $input['companyID'] = $item->companyID;
        $input['unitOfMeasure'] = $item->itemUnitOfMeasure;
        $input['maxQty'] = $item->maximunQty;
        $input['minQty'] = $item->minimumQty;
        
        $financeItemCategorySubAssigned = FinanceItemcategorySubAssigned::where('companySystemID', $item->companySystemID)
            ->where('mainItemCategoryID', $item->financeCategoryMaster)
            ->where('itemCategorySubID', $item->financeCategorySub)
            ->first();

        if (empty($financeItemCategorySubAssigned)) {
            return $this->sendError('Finance category not assigned for the selected item.');
        }

        if ($item->financeCategoryMaster == 1) {

            $alreadyAdded = PurchaseRequest::where('purchaseRequestID', $input['purchaseRequestID'])
                ->whereHas('details', function ($query) use ($companySystemID, $purchaseRequest, $item) {
                    $query->where('itemPrimaryCode', $item->itemPrimaryCode);
                })
                ->first();

            if ($alreadyAdded) {
                return $this->sendError("Selected item is already added. Please check again", 500);
            }
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
        
        $allowFinanceCategory = CompanyPolicyMaster::where('companyPolicyCategoryID', 20)
                ->where('companySystemID', $purchaseRequest->companySystemID)
                ->first();

        if ($allowFinanceCategory) {
            $policy = $allowFinanceCategory->isYesNO;

            if ($policy == 0) {
                if ($purchaseRequest->financeCategory == null || $purchaseRequest->financeCategory == 0) {
                    return $this->sendError('Category is not found.', 500);
                }

                //checking if item category is same or not
                $pRDetailExistSameItem = PurchaseRequestDetails::select(DB::raw('DISTINCT(itemFinanceCategoryID) as itemFinanceCategoryID'))
                    ->where('purchaseRequestID', $purchaseRequest->purchaseRequestID)
                    ->whereNotNull('itemFinanceCategoryID')
                    ->first();

                if ($pRDetailExistSameItem) {
                    if ($item->financeCategoryMaster != $pRDetailExistSameItem["itemFinanceCategoryID"]) {
                        return $this->sendError('You cannot add different category item', 500);
                    }
                }
            }
        }

          // check policy 18

        $allowPendingApproval = CompanyPolicyMaster::where('companyPolicyCategoryID', 18)
            ->where('companySystemID', $companySystemID)
            ->first();

        if ($allowPendingApproval && $item->financeCategoryMaster == 1) {

            if ($allowPendingApproval->isYesNO == 0) {

                $checkWhether = PurchaseRequest::where('purchaseRequestID', '!=', $purchaseRequest->purchaseRequestID)
                    ->where('companySystemID', $companySystemID)
                    ->where('serviceLineSystemID', $purchaseRequest->serviceLineSystemID)
                    ->select([
                        'erp_purchaserequest.purchaseRequestID',
                        'erp_purchaserequest.companySystemID',
                        'erp_purchaserequest.serviceLineCode',
                        'erp_purchaserequest.purchaseRequestCode',
                        'erp_purchaserequest.PRConfirmedYN',
                        'erp_purchaserequest.approved',
                        'erp_purchaserequest.cancelledYN'
                    ])
                    ->groupBy(
                        'erp_purchaserequest.purchaseRequestID',
                        'erp_purchaserequest.companySystemID',
                        'erp_purchaserequest.serviceLineCode',
                        'erp_purchaserequest.purchaseRequestCode',
                        'erp_purchaserequest.PRConfirmedYN',
                        'erp_purchaserequest.approved',
                        'erp_purchaserequest.cancelledYN'
                    );

                $anyPendingApproval = $checkWhether->whereHas('details', function ($query) use ($companySystemID, $purchaseRequest, $item) {
                    $query->where('itemPrimaryCode', $item->itemPrimaryCode);
                })
                    ->where('approved', 0)
                    ->where('cancelledYN', 0)
                    ->first();

                if (!empty($anyPendingApproval)) {
                    return $this->sendError("There is a purchase request (" . $anyPendingApproval->purchaseRequestCode . ") pending for approval for the item you are trying to add. Please check again.", 500);
                }

                $anyApprovedPRButPONotProcessed = PurchaseRequest::where('purchaseRequestID', '!=', $purchaseRequest->purchaseRequestID)
                    ->where('companySystemID', $companySystemID)
                    ->where('serviceLineSystemID', $purchaseRequest->serviceLineSystemID)
                    ->select([
                        'erp_purchaserequest.purchaseRequestID',
                        'erp_purchaserequest.companySystemID',
                        'erp_purchaserequest.serviceLineCode',
                        'erp_purchaserequest.purchaseRequestCode',
                        'erp_purchaserequest.PRConfirmedYN',
                        'erp_purchaserequest.approved',
                        'erp_purchaserequest.cancelledYN'
                    ])
                    ->groupBy(
                        'erp_purchaserequest.purchaseRequestID',
                        'erp_purchaserequest.companySystemID',
                        'erp_purchaserequest.serviceLineCode',
                        'erp_purchaserequest.purchaseRequestCode',
                        'erp_purchaserequest.PRConfirmedYN',
                        'erp_purchaserequest.approved',
                        'erp_purchaserequest.cancelledYN'
                    )
                    ->whereHas('details', function ($query) use ($companySystemID, $purchaseRequest, $item) {
                        $query->where('itemPrimaryCode', $item->itemPrimaryCode)
                            ->where('selectedForPO', 0)
                            ->where('prClosedYN', 0)
                            ->where('fullyOrdered', 0)
                            ->where('manuallyClosed', 0);
                    })
                    ->where('approved', -1)
                    ->where('cancelledYN', 0)
                    ->first();
                /* approved=-1 And cancelledYN=0 And selectedForPO=0 And prClosedYN=0 And fullyOrdered=0*/

                if (!empty($anyApprovedPRButPONotProcessed)) {
                    return $this->sendError("There is a purchase request (" . $anyApprovedPRButPONotProcessed->purchaseRequestCode . ") approved hense PO is not processed for the item you are trying to add. Please check again", 500);
                }

                $anyApprovedPRButPOPartiallyProcessed = PurchaseRequest::where('purchaseRequestID', '!=', $purchaseRequest->purchaseRequestID)
                    ->where('companySystemID', $companySystemID)
                    ->where('serviceLineSystemID', $purchaseRequest->serviceLineSystemID)
                    ->select([
                        'erp_purchaserequest.purchaseRequestID',
                        'erp_purchaserequest.companySystemID',
                        'erp_purchaserequest.serviceLineCode',
                        'erp_purchaserequest.purchaseRequestCode',
                        'erp_purchaserequest.PRConfirmedYN',
                        'erp_purchaserequest.approved',
                        'erp_purchaserequest.cancelledYN'
                    ])
                    ->groupBy(
                        'erp_purchaserequest.purchaseRequestID',
                        'erp_purchaserequest.companySystemID',
                        'erp_purchaserequest.serviceLineCode',
                        'erp_purchaserequest.purchaseRequestCode',
                        'erp_purchaserequest.PRConfirmedYN',
                        'erp_purchaserequest.approved',
                        'erp_purchaserequest.cancelledYN'
                    )->whereHas('details', function ($query) use ($companySystemID, $purchaseRequest, $item) {
                        $query->where('itemPrimaryCode', $item->itemPrimaryCode)
                            ->where('selectedForPO', 0)
                            ->where('prClosedYN', 0)
                            ->where('fullyOrdered', 1)
                            ->where('manuallyClosed', 0);
                    })
                    ->where('approved', -1)
                    ->where('cancelledYN', 0)
                    ->first();
                /* approved=-1 And cancelledYN=0 And selectedForPO=0 And prClosedYN=0 And fullyOrdered=1*/

                if (!empty($anyApprovedPRButPOPartiallyProcessed)) {
                    return $this->sendError("There is a purchase request (" . $anyApprovedPRButPOPartiallyProcessed->purchaseRequestCode . ") approved and PO is partially processed for the item you are trying to add. Please check again", 500);
                }

                /* PO check*/

                $checkPOPending = ProcumentOrder::where('companySystemID', $companySystemID)
                    ->where('serviceLineSystemID', $purchaseRequest->serviceLineSystemID)
                    ->whereHas('detail', function ($query) use ($item) {
                        $query->where('itemPrimaryCode', $item->itemPrimaryCode)
                               ->where('manuallyClosed', 0);
                    })
                    ->where('approved', 0)
                    ->where('poCancelledYN', 0)
                    ->first();

                if (!empty($checkPOPending)) {
                    return $this->sendError("There is a purchase order (" . $checkPOPending->purchaseOrderCode . ") pending for approval for the item you are trying to add. Please check again.", 500);
                }
                /* PO --> approved=-1 And cancelledYN=0 */

            }
        }


        $group_companies = Helper::getSimilarGroupCompanies($companySystemID);
        $poQty = PurchaseOrderDetails::whereHas('order', function ($query) use ($group_companies) {
            $query->whereIn('companySystemID', $group_companies)
                ->where('approved', -1)
                ->where('poType_N', '!=',5)// poType_N = 5 =>work order
                ->where('poCancelledYN', 0)
                ->where('manuallyClosed', 0);
             })
            ->where('itemCode', $input['itemCode'])
            ->where('manuallyClosed',0)
            ->groupBy('erp_purchaseorderdetails.itemCode')
            ->select(
                [
                    'erp_purchaseorderdetails.companySystemID',
                    'erp_purchaseorderdetails.itemCode',
                    'erp_purchaseorderdetails.itemPrimaryCode'
                ]
            )
            ->sum('noQty');

        $quantityInHand = ErpItemLedger::where('itemSystemCode', $input['itemCode'])
            ->where('companySystemID', $companySystemID)
            ->groupBy('itemSystemCode')
            ->sum('inOutQty');

        $grvQty = GRVDetails::whereHas('grv_master', function ($query) use ($group_companies) {
            $query->whereIn('companySystemID', $group_companies)
                ->where('grvTypeID', 2)
                ->where('approved', -1)
                ->groupBy('erp_grvmaster.companySystemID');
        })->whereHas('po_detail', function ($query){
            $query->where('manuallyClosed',0)
            ->whereHas('order', function ($query){
                $query->where('manuallyClosed',0);
            });
        })
            ->where('itemCode', $input['itemCode'])
            ->groupBy('erp_grvdetails.itemCode')
            ->select(
                [
                    'erp_grvdetails.companySystemID',
                    'erp_grvdetails.itemCode'
                ])
            ->sum('noQty');

        $quantityOnOrder = $poQty - $grvQty;
        $input['poQuantity'] = $poQty;
        $input['quantityOnOrder'] = $quantityOnOrder;
        $input['quantityInHand'] = $quantityInHand;
        unset($input['itemCodeNew']);

        return $this->sendResponse($input, 'Purchase Request item maped successfully');
    }

    /**
     * Display the specified PurchaseRequestDetails.
     * GET|HEAD /purchaseRequestDetails/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var PurchaseRequestDetails $purchaseRequestDetails */
        $purchaseRequestDetails = $this->purchaseRequestDetailsRepository->findWithoutFail($id);

        if (empty($purchaseRequestDetails)) {
            return $this->sendError('Purchase Request Details not found');
        }

        return $this->sendResponse($purchaseRequestDetails->toArray(), 'Purchase Request Details retrieved successfully');
    }

    /**
     * Update the specified PurchaseRequestDetails in storage.
     * PUT/PATCH /purchaseRequestDetails/{id}
     *
     * @param  int $id
     * @param UpdatePurchaseRequestDetailsAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdatePurchaseRequestDetailsAPIRequest $request)
    {
        $input = array_except($request->all(), 'uom');

        $input = $this->convertArrayToValue($input);



        /** @var PurchaseRequestDetails $purchaseRequestDetails */
        $purchaseRequestDetails = $this->purchaseRequestDetailsRepository->findWithoutFail($id);

        if (empty($purchaseRequestDetails)) {
            return $this->sendError('Purchase Request Details not found');
        }
        $purchaseRequest = $this->purchaseRequestRepository->findWithoutFail($input['purchaseRequestID']);
        if (empty($purchaseRequest)) {
            return $this->sendError('Purchase Request not found');
        }
        if ($purchaseRequest->cancelledYN == -1) {
            return $this->sendError('This Purchase Request already closed. You can not edit.', 500);
        }
        if ($purchaseRequest->approved == 1) {
            return $this->sendError('This Purchase Request fully approved. You can not edit.', 500);
        }

        if (!empty($input['purchase_issue_qnty'])) {
            $input['quantityRequested'] = $input['purchase_issue_qnty'];
        }else {
            if (empty($input['quantityRequested'])) {
                $input['quantityRequested'] = 0;
            }
        }

        if (empty($input['estimatedCost'])) {
            $input['estimatedCost'] = 0;
        }

        DB::beginTransaction();
        try {
            $purchaseRequestDetailsRes = $this->purchaseRequestDetailsRepository->update($input, $id);

            if ($purchaseRequestDetails->quantityRequested != $input['quantityRequested']) {
                $checkAlreadyAllocated = SegmentAllocatedItem::where('serviceLineSystemID', '!=',$purchaseRequest->serviceLineSystemID)
                                                         ->where('documentSystemID', $purchaseRequest->documentSystemID)
                                                         ->where('documentMasterAutoID', $input['purchaseRequestID'])
                                                         ->where('documentDetailAutoID', $id)
                                                         ->get();

                if (sizeof($checkAlreadyAllocated) == 0) {
                    $checkAlreadyAllocated = SegmentAllocatedItem::where('serviceLineSystemID',$purchaseRequest->serviceLineSystemID)
                                                         ->where('documentSystemID', $purchaseRequest->documentSystemID)
                                                         ->where('documentMasterAutoID', $input['purchaseRequestID'])
                                                         ->where('documentDetailAutoID', $id)
                                                         ->delete();

                    $allocationData = [
                        'serviceLineSystemID' => $purchaseRequest->serviceLineSystemID,
                        'documentSystemID' => $purchaseRequest->documentSystemID,
                        'docAutoID' => $input['purchaseRequestID'],
                        'docDetailID' => $id
                    ];

                    $segmentAllocatedItem = $this->segmentAllocatedItemRepository->allocateSegmentWiseItem($allocationData);

                    if (!$segmentAllocatedItem['status']) {
                        return $this->sendError($segmentAllocatedItem['message']);
                    }
                } else {
                     $allocatedQty = SegmentAllocatedItem::where('documentSystemID', $purchaseRequest->documentSystemID)
                                                 ->where('documentMasterAutoID', $input['purchaseRequestID'])
                                                 ->where('documentDetailAutoID', $id)
                                                 ->sum('allocatedQty');

                    if ($allocatedQty > $input['quantityRequested']) {
                        return $this->sendError("You cannot update the requested quantity. since quantity has been allocated to segments", 500);
                    }
                }
            }

            DB::commit();
            return $this->sendResponse($purchaseRequestDetailsRes->toArray(), 'PurchaseRequestDetails updated successfully');
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage());
        }

    }

    /**
     * Remove the specified PurchaseRequestDetails from storage.
     * DELETE /purchaseRequestDetails/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var PurchaseRequestDetails $purchaseRequestDetails */
        $purchaseRequestDetails = $this->purchaseRequestDetailsRepository->findWithoutFail($id);

        if (empty($purchaseRequestDetails)) {
            return $this->sendError('Purchase Request Details not found');
        }

        $purchaseRequest = $this->purchaseRequestRepository->findWithoutFail($purchaseRequestDetails->purchaseRequestID);
        if (empty($purchaseRequest)) {
            return $this->sendError('Purchase Request not found');
        }
        if ($purchaseRequest->cancelledYN == -1) {
            return $this->sendError('This Purchase Request already closed. You can not delete.', 500);
        }
        if ($purchaseRequest->approved == 1) {
            return $this->sendError('This Purchase Request fully approved. You can not delete.', 500);
        }

        $purchaseRequestDetails->delete();

        return $this->sendResponse($id, 'Purchase Request Details deleted successfully');
    }

    /**
     * Display a listing all items for PO based on Purchase Request.
     * GET|HEAD /getPurchaseRequestDetailForPO
     *
     * @param Request $request
     * @return Response
     */

    public function getPurchaseRequestDetailForPO(Request $request)
    {
        $input = $request->all();
        $prID = $input['purchaseRequestID'];

        $detail = DB::select('SELECT prdetails.*,"" as isChecked, "" as poQty,podetails.poTakenQty FROM erp_purchaserequestdetails prdetails LEFT JOIN (SELECT erp_purchaseorderdetails.purchaseRequestDetailsID, SUM(noQty) AS poTakenQty FROM erp_purchaseorderdetails GROUP BY purchaseRequestDetailsID,itemCode) as podetails ON prdetails.purchaseRequestDetailsID = podetails.purchaseRequestDetailsID WHERE purchaseRequestID = ' . $prID . ' AND prClosedYN = 0 AND fullyOrdered != 2 AND manuallyClosed = 0');

        return $this->sendResponse($detail, 'Purchase Request Details retrieved successfully');

    }

    public function getQtyOrderDetails(Request $request){

        $input = $request->all();

        $validator = \Validator::make($input, [
            'companySystemID' => 'required',
            'itemCode' => 'required',
            'requestId' =>'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }


        $pr = PurchaseRequest::where('purchaseRequestID',$input['requestId'])->first();
        if(empty($pr)){
            return $this->sendError('Purchase Request Not Found',404);
        }

        $companySystemID = $input['companySystemID'];

//        $isGroup = \Helper::checkIsCompanyGroup($companySystemID);
//
//        if ($isGroup) {
//            $childCompanies = Helper::getGroupCompany($companySystemID);
//
//        } else {
//            $childCompanies = [$companySystemID];
//        }

        $childCompanies = Helper::getSimilarGroupCompanies($companySystemID);


        $itemCode = isset($input['itemCode']) ? $input['itemCode'] : 0;

        $detail = PurchaseRequestDetails::where('purchaseRequestID',$input['requestId'])
                                       ->where('itemCode',$itemCode)
                                       ->first();

        if(empty($detail)){
            return $this->sendError('Purchase Request Detail Not Found',404);
        }


        $PRRequestedDate = $pr->timeStamp;
        $result['history'] = PurchaseOrderDetails::whereHas('order', function ($query) use ($childCompanies,$PRRequestedDate) {
            $query->whereIn('companySystemID', $childCompanies)
                ->where('approved', -1)
                ->whereIn('goodsRecievedYN', [0,1])
                ->where('poType_N', '!=',5)// poType_N = 5 =>work order
                ->where('approvedDate', '<=',$PRRequestedDate)
                ->where('poCancelledYN', 0)
                ->where(function ($query) use($PRRequestedDate) {
                    $query->where(function ($q) use($PRRequestedDate) {
                        $q->where('manuallyClosed',1)
                            ->where('manuallyClosedDate','<=',$PRRequestedDate);
                    })->orWhere('manuallyClosed',0);
                });
        })
            ->where('manuallyClosed',0)
            ->where('itemCode', $itemCode)
            ->withCount(['grv_details AS grv_qty'=> function($query) use($childCompanies,$PRRequestedDate){
                $query->select(DB::raw("COALESCE(SUM(noQty),0) as grvNoQty"))
                    ->whereHas('grv_master',function ($query) use($childCompanies, $PRRequestedDate){
                        $query->whereIn('companySystemID', $childCompanies)
                            ->where('grvTypeID', 2)
                            ->where('approvedDate', '<=',$PRRequestedDate);
                    })
                    ->whereHas('po_detail', function($query) use($PRRequestedDate) {
                        $query->whereHas('order', function ($q) use($PRRequestedDate){
                            $q->where(function ($q1) use($PRRequestedDate){
                                $q1->where('manuallyClosed',1)
                                    ->where('manuallyClosedDate','<=',$PRRequestedDate);
                            })->orWhere('manuallyClosed',0);
                        })
                            ->where(function ($q) use($PRRequestedDate) {
                            $q->where('manuallyClosed',1)
                                ->where('manuallyClosedDate','<=',$PRRequestedDate);
                        })->orWhere('manuallyClosed',0);
                    });
            }])
            ->with(['order.currency','unit','order.location'])->get();

        $result['item'] = ItemAssigned::whereIn('companySystemID',$childCompanies)->where('itemCodeSystem',$itemCode)->first();

        return $this->sendResponse($result, 'Purchase Request Details retrieved successfully');
    }


    public function prItemsUpload(request $request)
    {
        DB::beginTransaction();
        try {
            $input = $request->all();
            $excelUpload = $input['itemExcelUpload'];
            $input = array_except($request->all(), 'itemExcelUpload');
            $input = $this->convertArrayToValue($input);

            $decodeFile = base64_decode($excelUpload[0]['file']);
            $originalFileName = $excelUpload[0]['filename'];
            $extension = $excelUpload[0]['filetype'];
            $size = $excelUpload[0]['size'];

            $purchaseRequest = PurchaseRequest::where('purchaseRequestID', $input['requestID'])
                                               ->first();


            if (empty($purchaseRequest)) {
                return $this->sendError('Purchase Request not found', 500);
            }


            $allowedExtensions = ['xlsx','xls'];

            if (!in_array($extension, $allowedExtensions))
            {
                return $this->sendError('This type of file not allow to upload.you can only upload .xlsx (or) .xls',500);
            }

            if ($size > 20000000) {
                return $this->sendError('The maximum size allow to upload is 20 MB',500);
            }

            $disk = 'local';
            Storage::disk($disk)->put($originalFileName, $decodeFile);

            $finalData = [];
            $formatChk = \Excel::selectSheetsByIndex(0)->load(Storage::disk($disk)->url('app/' . $originalFileName), function ($reader) {
            })->get()->toArray();

            $uniqueData = array_filter(collect($formatChk)->toArray());

            $validateHeaderCode = false;
            $validateHeaderQty = false;
            $totalItemCount = 0;

            $allowItemToTypePolicy = false;
            $itemNotound = false;
            $allowItemToType = CompanyPolicyMaster::where('companyPolicyCategoryID', 53)
                                                ->where('companySystemID', $purchaseRequest->companySystemID)
                                                ->first();

            if ($allowItemToType) {
                if ($allowItemToType->isYesNO) {
                    $allowItemToTypePolicy = true;
                }
            }


            foreach ($uniqueData as $key => $value) {
                if (isset($value['item_code']) || (isset($value['item_description']) && $allowItemToTypePolicy)) {
                    $validateHeaderCode = true;
                }

                if (isset($value['qty'])) {
                    $validateHeaderQty = true;
                }

                if ((isset($value['item_code']) && !is_null($value['item_code'])) || isset($value['item_description']) && !is_null($value['item_description']) || isset($value['comment']) && !is_null($value['comment']) || isset($value['qty']) && !is_null($value['qty'])) {
                    $totalItemCount = $totalItemCount + 1;
                }
            }

            if (!$validateHeaderCode || !$validateHeaderCode) {
                return $this->sendError('Items cannot be uploaded, as there are null values found', 500);
            }

            // if (count($formatChk) > 0) {
            //     if (!isset($formatChk['item_code']) || !isset($formatChk['qty'])) {
            //     }
            // }

            $record = \Excel::selectSheetsByIndex(0)->load(Storage::disk($disk)->url('app/' . $originalFileName), function ($reader) {
            })->select(array('item_code', 'item_description', 'comment', 'qty'))->get()->toArray();

            $uploadSerialNumber = array_filter(collect($record)->toArray());

           


            if ($purchaseRequest->cancelledYN == -1) {
                return $this->sendError('This Purchase Request already closed. You can not add.', 500);
            }

            if ($purchaseRequest->approved == 1) {
                return $this->sendError('This Purchase Request fully approved. You can not add.', 500);
            }


            if (count($record) > 0) {
                $res = $this->purchaseRequestDetailsRepository->storePrDetails($record, $input['requestID'], $totalItemCount);             
            } else {
                return $this->sendError('No Records found!', 500);
            }

            Storage::disk($disk)->delete('app/' . $originalFileName);
            DB::commit();
            return $this->sendResponse([], $res);
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage());
        }
    }
}
