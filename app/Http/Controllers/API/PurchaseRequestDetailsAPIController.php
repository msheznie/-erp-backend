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
use App\helper\PurcahseRequestDetail;
use App\Http\Requests\API\CreatePurchaseRequestDetailsAPIRequest;
use App\Http\Requests\API\UpdatePurchaseRequestDetailsAPIRequest;
use App\Models\Company;
use App\Models\ItemMaster;
use App\Models\CompanyPolicyMaster;
use App\Models\ErpItemLedger;
use App\Models\AssetFinanceCategory;
use App\Models\FinanceItemcategorySubAssigned;
use App\Models\GRVDetails;
use App\Models\MaterielRequest;
use App\Models\SegmentAllocatedItem;
use App\Models\ItemAssigned;
use App\Models\ProcumentOrder;
use App\Models\PurchaseOrderDetails;
use App\Models\PurchaseRequest;
use App\Models\PurchaseRequestDetails;
use App\Models\PulledItemFromMR;
use App\Repositories\SegmentAllocatedItemRepository;
use App\Repositories\PurchaseRequestDetailsRepository;
use App\Repositories\PurchaseRequestRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\SegmentMaster;
use App\Jobs\PrBulkBulkItem;
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

        return $this->sendResponse($purchaseRequestDetails->toArray(), trans('custom.purchase_request_details_retrieved_successfully'));
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
            ->with(['uom','altUom'])
            ->skip($input['skip'])->take($input['limit'])->get();
        $index = $input['skip'] + 1;
        foreach($items as $item) {
            $item['index'] = $index;
            $index++;
        }
        return $this->sendResponse($items->toArray(), trans('custom.purchase_request_details_retrieved_successfully'));
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
        }else {
            if(isset($input['itemCode']['id']))  {
                $input['itemCode'] = $input['itemCode']['id'];
            }
        }


        $item = ItemAssigned::where('itemCodeSystem', $input['itemCode'])
            ->where('companySystemID', $companySystemID)
            ->first();

        if (empty($item)) {
            if (!$allowItemToTypePolicy) {
                return $this->sendError(trans('custom.item_not_found'));
            } else {
                $itemNotound = true;
            }
        }


        $purchaseRequest = PurchaseRequest::where('purchaseRequestID', $input['purchaseRequestID'])
            ->first();


        if (empty($purchaseRequest)) {
            return $this->sendError(trans('custom.purchase_request_details_not_found'));
        }

        if ($purchaseRequest->cancelledYN == -1) {
            return $this->sendError(trans('custom.this_purchase_request_already_closed_you_can_not_a'), 500);
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
                        return $this->sendError(trans('custom.category_is_not_found'), 500);
                    }

                    //checking if item category is same or not
                    $pRDetailExistSameItem = PurchaseRequestDetails::select(DB::raw('DISTINCT(itemFinanceCategoryID) as itemFinanceCategoryID'))
                        ->where('purchaseRequestID', $purchaseRequest->purchaseRequestID)
                        ->first();

                    if ($pRDetailExistSameItem) {
                        if ($item->financeCategoryMaster != $pRDetailExistSameItem["itemFinanceCategoryID"]) {
                            return $this->sendError(trans('custom.you_cannot_add_different_category_item'), 500);
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
                        return $this->sendError("There is a purchase request (" . $anyApprovedPRButPONotProcessed->purchaseRequestCode . ") approved hense PO is not processed for the item you are trying to add. Please check againn", 500);
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
            $poQty = PurchaseOrderDetails::whereHas('order', function ($query) use ($companySystemID) {
                $query->where('companySystemID', $companySystemID)
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

            $grvQty = GRVDetails::whereHas('grv_master', function ($query) use ($companySystemID) {
                $query->where('companySystemID', $companySystemID)
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

        return $this->sendResponse($purchaseRequestDetails->toArray(), trans('custom.purchase_request_details_saved_successfully'));
    }

    public function updateQtyOnOrder(request $request){

        $itemCode = $request->itemCode;
        $companySystemID = $request->companyId;

        $group_companies = Helper::getSimilarGroupCompanies($companySystemID);
            $poQty = PurchaseOrderDetails::whereHas('order', function ($query) use ($companySystemID) {
                $query->where('companySystemID', $companySystemID)
                    ->where('approved', -1)
                    ->where('poType_N', '!=',5)// poType_N = 5 =>work order
                    ->where('poCancelledYN', 0)
                    ->where('manuallyClosed', 0);
                 })
                ->where('itemCode', $itemCode)
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
            $grvQty = GRVDetails::whereHas('grv_master', function ($query) use ($companySystemID) {
                $query->where('companySystemID', $companySystemID)
                    ->where('grvTypeID', 2)
                    ->where('approved', -1)
                    ->groupBy('erp_grvmaster.companySystemID');
            })->whereHas('po_detail', function ($query){
                $query->where('manuallyClosed',0)
                ->whereHas('order', function ($query){
                    $query->where('manuallyClosed',0);
                });
            })
                ->where('itemCode', $itemCode)
                ->groupBy('erp_grvdetails.itemCode')
                ->select(
                    [
                        'erp_grvdetails.companySystemID',
                        'erp_grvdetails.itemCode'
                    ])
                ->sum('noQty');

            $quantityOnOrder = $poQty - $grvQty;
            $updateQtyOnOrder = PurchaseRequestDetails::where('itemCode', $itemCode)->update(['quantityOnOrder'=> $quantityOnOrder]);
            return $this->sendResponse($updateQtyOnOrder, trans('custom.quantity_on_order_updated_successfully'));

            
    }


    public function mapLineItemPr(Request $request)
    {
        $input = $request->all();

        $checkItem = PurchaseOrderDetails::where('itemCode', $input['itemCodeNew'])
                                         ->where('purchaseRequestDetailsID', '!=', $input['purchaseRequestDetailsID'])
                                         ->where('purchaseOrderMasterID', $input['purchaseOrderID'])
                                         ->first();

        if ($checkItem) {
            return $this->sendError(trans('custom.this_item_has_already_maped_with_another_item_of_t'));
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
            return $this->sendError(trans('custom.item_not_found'));
        }

        $purchaseRequest = PurchaseRequest::where('purchaseRequestID', $input['purchaseRequestID'])
            ->first();


        if (empty($purchaseRequest)) {
            return $this->sendError(trans('custom.purchase_request_details_not_found'));
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
                    return $this->sendError(trans('custom.category_is_not_found'), 500);
                }

                //checking if item category is same or not
                $pRDetailExistSameItem = PurchaseRequestDetails::select(DB::raw('DISTINCT(itemFinanceCategoryID) as itemFinanceCategoryID'))
                    ->where('purchaseRequestID', $purchaseRequest->purchaseRequestID)
                    ->whereNotNull('itemFinanceCategoryID')
                    ->first();

                if ($pRDetailExistSameItem) {
                    if ($item->financeCategoryMaster != $pRDetailExistSameItem["itemFinanceCategoryID"]) {
                        return $this->sendError(trans('custom.you_cannot_add_different_category_item'), 500);
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

        return $this->sendResponse($input, trans('custom.purchase_request_item_maped_successfully'));
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
            return $this->sendError(trans('custom.purchase_request_details_not_found'));
        }

        return $this->sendResponse($purchaseRequestDetails->toArray(), trans('custom.purchase_request_details_retrieved_successfully'));
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

        $purchaseRequestID = $input['purchaseRequestID'];
        $itemCode = $input['itemCode'];
        $itemFinanceCategoryID = $input['itemFinanceCategoryID'];
        $isMRPulled = PulledItemFromMR::where('itemCodeSystem', $itemCode)->where('purcahseRequestID',$purchaseRequestID)->first();
        $total_requested_qnty =  PulledItemFromMR::where('purcahseRequestID',$purchaseRequestID)->where('itemCodeSystem',$itemCode)->groupBy('itemCodeSystem')->selectRaw('sum(mr_qnty) as sum')->first();

        $quantityInHand = $input['quantityInHand'];
        $requestedQty = $input['quantityRequested'];
        $reorderQty = ItemAssigned::where('itemCodeSystem', $itemCode)->sum('rolQuantity');
        $requestAndReorderTotal = $requestedQty + $reorderQty;

        if(isset($total_requested_qnty)) {
            if($total_requested_qnty->sum <  $input['quantityRequested'] ) {
                return $this->sendError(trans('custom.quantity_cannot_be_greater_than_total_materiel_req'));
            }
        }
        
        $purchaseRequestDetails = $this->purchaseRequestDetailsRepository->findWithoutFail($id);

        if (empty($purchaseRequestDetails)) {
            return $this->sendError(trans('custom.purchase_request_details_not_found'));
        }
        $purchaseRequest = $this->purchaseRequestRepository->findWithoutFail($input['purchaseRequestID']);
        if (empty($purchaseRequest)) {
            return $this->sendError(trans('custom.purchase_request_not_found'));
        }
        if ($purchaseRequest->cancelledYN == -1) {
            return $this->sendError(trans('custom.this_purchase_request_already_closed_you_can_not_e'), 500);
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
            if($quantityInHand > $requestAndReorderTotal && !$isMRPulled && $itemFinanceCategoryID==1){
                $input['is_eligible_mr'] = 1;
            } else {
                $input['is_eligible_mr'] = 0;
            }

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
            return $this->sendResponse($purchaseRequestDetailsRes->toArray(), trans('custom.purchaserequestdetails_updated_successfully'));
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
            return $this->sendError(trans('custom.purchase_request_details_not_found'));
        }

        $purchaseRequest = $this->purchaseRequestRepository->findWithoutFail($purchaseRequestDetails->purchaseRequestID);
        if (empty($purchaseRequest)) {
            return $this->sendError(trans('custom.purchase_request_not_found'));
        }
        if ($purchaseRequest->cancelledYN == -1) {
            return $this->sendError(trans('custom.this_purchase_request_already_closed_you_can_not_d'), 500);
        }
        if ($purchaseRequest->approved == 1) {
            return $this->sendError('This Purchase Request fully approved. You can not delete.', 500);
        }

        $datas = PulledItemFromMR::where('purcahseRequestID',$purchaseRequestDetails->purchaseRequestID)->where('itemCodeSystem',$purchaseRequestDetails->itemCode)->get();
        if(isset($datas)) {
            foreach($datas as $data) {
                $materialRequestID = $data->RequestID;
                $request = MaterielRequest::find($materialRequestID);
                $request->isSelectedToPR =  false;
                $request->save();
            }
        }
        PulledItemFromMR::where('purcahseRequestID',$purchaseRequestDetails->purchaseRequestID)->where('itemCodeSystem',$purchaseRequestDetails->itemCode)->delete();

        $purchaseRequestDetails->delete();

        return $this->sendResponse($id, trans('custom.purchase_request_details_deleted_successfully'));
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

        return $this->sendResponse($detail, trans('custom.purchase_request_details_retrieved_successfully'));

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
            return $this->sendError(trans('custom.purchase_request_not_found_1'),404);
        }

        $companySystemID = $input['companySystemID'];
        $childCompanies = Helper::getSimilarGroupCompanies($companySystemID);


        $itemCode = isset($input['itemCode']) ? $input['itemCode'] : 0;

        $detail = PurchaseRequestDetails::where('purchaseRequestID',$input['requestId'])
                                       ->where('itemCode',$itemCode)
                                       ->first();

        if(empty($detail)){
            return $this->sendError(trans('custom.purchase_request_detail_not_found'),404);
        }


        $PRRequestedDate = $pr->timeStamp;
        $result['history'] = PurchaseOrderDetails::whereHas('order', function ($query) use ($companySystemID,$PRRequestedDate) {
            $query->where('companySystemID', $companySystemID)
                ->where('approved', -1)
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

            
            ->withCount(['grv_details AS grv_qty'=> function($query) use($companySystemID,$PRRequestedDate){
                $query->select(DB::raw("COALESCE(SUM(noQty),0) as grvNoQty"))
                    ->whereHas('grv_master',function ($query) use($companySystemID, $PRRequestedDate){
                        $query->where('companySystemID', $companySystemID)
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

        $result['item'] = ItemAssigned::where('companySystemID',$companySystemID)->where('itemCodeSystem',$itemCode)->first();

        return $this->sendResponse($result, trans('custom.purchase_request_details_retrieved_successfully'));
    }

    public function getWarehouseStockDetails(Request $request){
        $input = $request->all();
        $purchaseRequest = PurchaseRequest::where('purchaseRequestID', $input['requestId'])
                                               ->first();
        $prRequestedDate = $purchaseRequest->PRRequestedDate;
        
        $itemMaster = ItemAssigned::with('unit')
                                    ->where('itemCodeSystem',$input['itemCode'])
                                    ->where('companySystemID',$input['companySystemID'])
                                    ->first();

        $item = ErpItemLedger::with('warehouse')
                                     ->where('itemSystemCode',$input['itemCode'])
                                     ->where('companySystemID',$input['companySystemID'])
                                     ->whereDate('transactionDate', '<=', $prRequestedDate)
                                     ->selectRaw('SUM(inOutQty) AS stockAmount, wareHouseSystemCode')
                                     ->groupBy('wareHouseSystemCode')
                                     ->get();

        $result = [ 'item'=>$item,
                    'itemMaster'=>$itemMaster];

        return $this->sendResponse($result, trans('custom.warehouse_stock_details_retrieved_successfully'));
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
                return $this->sendError(trans('custom.purchase_request_not_found'), 500);
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

            $filePath = Storage::disk($disk)->path($originalFileName);
            $spreadsheet = IOFactory::load($filePath);

            $sheet = $spreadsheet->getActiveSheet();

            $sheet->removeRow(1, 2);

            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save($filePath);
            $formatChk = \Excel::selectSheetsByIndex(0)->load($filePath, function ($reader) {})->get();

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
                } else {
                    if (isset($value['qty']) && isset($value['estimated_unit_cost']) && isset($value['comment'])) {
                        return $this->sendError(trans('custom.items_cannot_be_uploaded_as_there_are_null_values__1') . ($totalItemCount + 4), 500);
                    }
                }

                if (isset($value['qty'])) {
                    $validateHeaderQty = true;
                }
                if (isset($value['estimated_unit_cost'])) {
                    $validateEstimatedUnitCost = true;
                }

                if ((isset($value['item_code']) && !is_null($value['item_code'])) || isset($value['item_description']) && !is_null($value['item_description']) || isset($value['comment']) && !is_null($value['comment']) || isset($value['qty']) && !is_null($value['qty']) || isset($value['estimated_unit_cost']) && !is_null($value['estimated_unit_cost'])) {
                    $totalItemCount = $totalItemCount + 1;
                }
            }

            if (!$validateHeaderCode || !$validateHeaderCode || !$validateEstimatedUnitCost) {
                return $this->sendError(trans('custom.items_cannot_be_uploaded_as_there_are_null_values_'), 500);
            }

            // if (count($formatChk) > 0) {
            //     if (!isset($formatChk['item_code']) || !isset($formatChk['qty'])) {
            //     }
            // }

            $record = \Excel::selectSheetsByIndex(0)->load(Storage::disk($disk)->url('app/' . $originalFileName), function ($reader) {
            })->select(array('item_code', 'item_description', 'comment', 'qty','estimated_unit_cost'))->get()->toArray();

            $uploadSerialNumber = array_filter(collect($record)->toArray());

            if ($purchaseRequest->cancelledYN == -1) {
                return $this->sendError(trans('custom.this_purchase_request_already_closed_you_can_not_a'), 500);
            }

            if ($purchaseRequest->approved == 1) {
                return $this->sendError('This Purchase Request fully approved. You can not add.', 500);
            }

            foreach ($record as $key => $data) {
                if (isset($data['estimated_unit_cost'])) {
                    if (!is_numeric($data['estimated_unit_cost'])) {
                        return $this->sendError('Records with alpha numeric values for the estimated unit cost can not be uploaded.', 500);
                    }

                    if ($data['estimated_unit_cost'] < 0) {
                        return $this->sendError('Estimated unit cost value can not be less than zero.', 500);
                    }
                }
            }

            if (count($record) > 0) {
                $res = $this->purchaseRequestDetailsRepository->storePrDetails($record, $input['requestID'], $totalItemCount,$this->segmentAllocatedItemRepository);            
            } else {
                return $this->sendError('No Records found!', 500);
            }

            if ($res['status'] === false) {
                DB::rollBack();
                return $this->sendError($res['message'], 500);
            }

            Storage::disk($disk)->delete('app/' . $originalFileName);
            DB::commit();
            return $this->sendResponse([], $res['message']);
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage());
        }
    }

    public function addAllItemsToPurchaseRequest(Request $request) {

        $input = $request->all();

        $db = isset($input['db']) ? $input['db'] : "";    
        $data['isBulkItemJobRun'] = true;
        $id = $input['purchaseRequestID'];
        $purchaseRequest = $this->purchaseRequestRepository->update($data, $id);
        if(isset($purchaseRequest))
        {
            PrBulkBulkItem::dispatch($input,$db);
            return ['status' => true , 'message' => 'Items Added to Queue Please wait some minutes to process'];
        }
        else
        {
            return $this->sendError('Unable to upload items', 422);
        }
        

    }

    public function getItemMasterPurchaseRequestHistory(Request $request)
    {
        $selectedCompanyId = $request['selectedCompanyId'];
        $isGroup = \Helper::checkIsCompanyGroup($selectedCompanyId);

        if($isGroup){
            $subCompanies = \Helper::getGroupCompany($selectedCompanyId);
        }else{
            $subCompanies = [$selectedCompanyId];
        }


        $purchaseRequestDetails = DB::table('erp_purchaserequestdetails')
          ->leftJoin('units', 'erp_purchaserequestdetails.unitOfMeasure', '=', 'units.UnitID')
          ->Join('companymaster', 'erp_purchaserequestdetails.companyID', '=', 'companymaster.CompanyID')
          ->Join('erp_purchaserequest', 'erp_purchaserequestdetails.purchaseRequestID', '=', 'erp_purchaserequest.purchaseRequestID')
          ->leftJoin('currencymaster', 'erp_purchaserequest.currency', '=', 'currencymaster.currencyID')
          ->where('erp_purchaserequestdetails.itemCode', $request['itemCodeSystem'])
          ->whereIn('erp_purchaserequest.companySystemID', $subCompanies)
        ->select('erp_purchaserequestdetails.purchaseRequestID',
            'erp_purchaserequestdetails.companyID',
            'companymaster.CompanyName',
            'erp_purchaserequest.purchaseRequestCode',
            'erp_purchaserequest.supplierCodeSystem',
            'erp_purchaserequest.supplierName',
             'erp_purchaserequestdetails.partNumber',
             'erp_purchaserequestdetails.unitOfMeasure',
             'erp_purchaserequestdetails.quantityRequested',
             'units.UnitShortCode',
             'erp_purchaserequestdetails.totalCost',
             'currencymaster.CurrencyCode',
             'currencymaster.DecimalPlaces',
             'erp_purchaserequest.PRRequestedDate'
            )
        ->paginate(15);

    return $this->sendResponse($purchaseRequestDetails, trans('custom.purchase_request_details_retrieved_successfully'));



    }


    public function exportPurchaseRequestHistory(Request $request)
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
        $purchaseRequestDetails = DB::table('erp_purchaserequestdetails')
        ->leftJoin('units', 'erp_purchaserequestdetails.unitOfMeasure', '=', 'units.UnitID')
        ->Join('companymaster', 'erp_purchaserequestdetails.companyID', '=', 'companymaster.CompanyID')
        ->Join('erp_purchaserequest', 'erp_purchaserequestdetails.purchaseRequestID', '=', 'erp_purchaserequest.purchaseRequestID')
        ->leftJoin('currencymaster', 'erp_purchaserequest.currency', '=', 'currencymaster.currencyID')
        ->where('erp_purchaserequestdetails.itemCode', $request['itemCodeSystem'])
        ->whereIn('erp_purchaserequest.companySystemID', $subCompanies)
      ->select('erp_purchaserequestdetails.purchaseRequestID',
          'erp_purchaserequestdetails.companyID',
          'companymaster.CompanyName',
          'erp_purchaserequest.purchaseRequestCode',
          'erp_purchaserequest.supplierCodeSystem',
          'erp_purchaserequest.supplierName',
           'erp_purchaserequestdetails.partNumber',
           'erp_purchaserequestdetails.unitOfMeasure',
           'erp_purchaserequestdetails.quantityRequested',
           'units.UnitShortCode',
           'erp_purchaserequestdetails.totalCost',
           'currencymaster.CurrencyCode',
           'currencymaster.DecimalPlaces',
           'erp_purchaserequest.PRRequestedDate'
          )
      ->get();

        foreach ($purchaseRequestDetails as $order) {


        
          if($order->quantityRequested == 0)
          {
            $qua_req = '0';
          }
          else
          {
            $qua_req = $order->quantityRequested;
          }

          if($order->totalCost == 0)
          {
            $qua_tot = '0';
          }
          else
          {
           
            $qua_tot = number_format((float)$order->totalCost, $order->DecimalPlaces, '.', ',');

          }


            $data[] = array(
                //'purchaseOrderMasterID' => $order->purchaseOrderMasterID,
                trans('custom.company_name') => $order->CompanyName,
                trans('custom.request_code') => $order->purchaseRequestCode,
                trans('custom.requested_date') => date("Y-m-d", strtotime($order->PRRequestedDate)),
                trans('custom.part_no_ref_number') => $order->partNumber,
                trans('custom.uom') => $order->UnitShortCode,
                trans('custom.currency') => $order->CurrencyCode,
                trans('custom.requested_qty') => $qua_req,
                trans('custom.total_cost') => $qua_tot,
            );
        }

        \Excel::create('purchaseRequestHistory', function ($excel) use ($data) {

            $excel->sheet('sheet name', function ($sheet) use ($data) {
                $sheet->fromArray($data);
                //$sheet->getStyle('A1')->getAlignment()->setWrapText(true);
                $sheet->setAutoSize(true);
                $sheet->getStyle('C1:C2')->getAlignment()->setWrapText(true);
            });
            $lastrow = $excel->getActiveSheet()->getHighestRow();
            $excel->getActiveSheet()->getStyle('A1:J' . $lastrow)->getAlignment()->setWrapText(true);
        })->download($type);

        return $this->sendResponse($csv, trans('custom.success_export'));
    }

    public function copyPr($id)
    {
    

            $items = PurchaseRequestDetails::where('purchaseRequestID', $id)
            ->with(['uom'])
            ->orderBy('purchaseRequestDetailsID', 'desc')
            ->get();
            


         
        $item_count_obj = count($items);
  
          // DB::beginTransaction();
            try {

                    $purchaseRequest = $this->purchaseRequestRepository->with(['created_by', 'confirmed_by','currency_by',
                    'priority_pdf', 'location_pdf', 'details.uom', 'company', 'segment', 'approved_by' => function ($query) {
                        $query->with('employee')
                            ->where('rejectedYN', 0)
                            ->whereIn('documentSystemID', [1, 50, 51]);
                    }
                    ])->findWithoutFail($id);
    
                 
                    $request_data['documentSystemID'] = $purchaseRequest->documentSystemID;  
                    $request_data['companySystemID'] = $purchaseRequest->companySystemID;      
                    $request_data['budgetYearID'] = $purchaseRequest->budgetYearID;  
                    $request_data['prBelongsYearID'] = $purchaseRequest->prBelongsYearID;  
                    $request_data['currency'] = $purchaseRequest->currency;    
                    $request_data['serviceLineSystemID'] = $purchaseRequest->serviceLineSystemID;  
                    $request_data['comments'] = $purchaseRequest->comments;  
                    $request_data['location'] = $purchaseRequest->location;  
                    $request_data['priority'] = $purchaseRequest->priority;  
                    $request_data['createdPcID'] = $purchaseRequest->createdPcID;  
                    $request_data['createdUserID'] = $purchaseRequest->createdUserID;  
                    $request_data['createdUserSystemID'] = $purchaseRequest->createdUserSystemID;  
                    $request_data['PRRequestedDate'] = $purchaseRequest->PRRequestedDate;  
                    $request_data['budgetYear'] = $purchaseRequest->budgetYear;  
                    $request_data['prBelongsYear'] = $purchaseRequest->prBelongsYear;  
                    $request_data['departmentID'] = $purchaseRequest->departmentID;  
                    $request_data['serialNumber'] = $purchaseRequest->serialNumber;  
                    $request_data['serviceLineCode'] = $purchaseRequest->serviceLineCode;  
                    $request_data['documentID'] = $purchaseRequest->documentID;  
                    $request_data['docRefNo'] = $purchaseRequest->docRefNo;  
                    $request_data['companyID'] = $purchaseRequest->companyID;  
                    $request_data['financeCategory'] = $purchaseRequest->financeCategory;   


                    $serivice_line_code = '';
                    $segment = SegmentMaster::where('serviceLineSystemID', $purchaseRequest->serviceLineSystemID)->first();
                    if ($segment) {
                        $serivice_line_code = $segment->ServiceLineCode;
                    }
                    
              
              
                    $lastSerial = PurchaseRequest::where('companySystemID', $purchaseRequest->companySystemID)
                    ->where('documentSystemID', $purchaseRequest->documentSystemID)
                    ->orderBy('purchaseRequestID', 'desc')
                    ->first();
               
                    $lastSerialNumber = 1;
                    if ($lastSerial) {
                        $lastSerialNumber = intval($lastSerial->serialNumber) + 1;
                    }

                 
                    $dep_id = 'PROC';
                    $code = str_pad($lastSerialNumber, 6, '0', STR_PAD_LEFT);
                    $request_data['purchaseRequestCode'] = $purchaseRequest->companyID . '\\' . $dep_id . '\\' . $serivice_line_code . '\\' . $purchaseRequest->documentID . $code;
                    
                    $request_data['serialNumber'] = $lastSerialNumber;

                    $new_purchaseRequests = $this->purchaseRequestRepository->create($request_data);

                    
               

                    $succes_item = 0;
                    $valid_items = [];


                foreach($items as $itemVal)
                {   
                    
               
               
                    $is_failed= false;

                    $allowItemToTypePolicy = false;
                    $itemNotound = false;
                    $companySystemID = $itemVal->companySystemID;
                    // $allowItemToType = CompanyPolicyMaster::where('companyPolicyCategoryID', 53)
                    // ->where('companySystemID', $itemVal->companySystemID)
                    // ->first();

                    
                    // if ($allowItemToType) {
                    //     if ($allowItemToType->isYesNO) {
                    //         $allowItemToTypePolicy = true;
                    //     }
                    // }

                    // if ($allowItemToTypePolicy) {
                    //     $request_data_details['itemCode'] = $itemVal->itemCode;
                    // }

                    $request_data_details['itemCode'] = $itemVal->itemCode;
                    $item = ItemAssigned::where('itemCodeSystem', $itemVal->itemCode)
                    ->where('companySystemID', $itemVal->companySystemID)
                    ->first();
        
                    if (empty($item)) {
                        if (!$allowItemToTypePolicy) {
                            //return $this->sendError(trans('custom.item_not_found'));
                            $is_failed= true;
                           // continue;
                        } else {
                            $itemNotound = true;
                        }
                    }


                    // $purchaseRequest = PurchaseRequest::where('purchaseRequestID', $itemVal->purchaseRequestID)
                    // ->first();
        
            
                    if (empty($purchaseRequest)) {
                        $is_failed= true;
                        //continue;
                        //return $this->sendError(trans('custom.purchase_request_details_not_found'));
                    }
            
                    if ($purchaseRequest->cancelledYN == -1) {
                        $is_failed= true;
                        //continue;
                        //return $this->sendError(trans('custom.this_purchase_request_already_closed_you_can_not_a'), 500);
                    }
            
                    if ($purchaseRequest->approved == 1) {
                        $is_failed= true;
                        //continue;
                        //return $this->sendError('This Purchase Request fully approved. You can not add.', 500);
                    }
                
            

                    $request_data_details['budgetYear'] = $purchaseRequest->budgetYear;
                    $request_data_details['itemPrimaryCode'] = $itemVal->itemPrimaryCode;
                    $request_data_details['itemDescription'] = $itemVal->itemDescription;
                    $request_data_details['partNumber'] = $itemVal->partNumber;
                    $request_data_details['itemFinanceCategoryID'] = $itemVal->itemFinanceCategoryID;
                    $request_data_details['itemFinanceCategorySubID'] = $itemVal->itemFinanceCategorySubID;

            
                    //start

                    if (!$itemNotound) {

                        
   
                        $currencyConversion = \Helper::currencyConversion($item->companySystemID, $item->wacValueLocalCurrencyID, $purchaseRequest->currency, $item->wacValueLocal);
              
                        $request_data_details['estimatedCost'] = $itemVal->estimatedCost;
                        $request_data_details['companySystemID'] = $item->companySystemID;
                        $request_data_details['companyID'] = $item->companyID;
                        $request_data_details['unitOfMeasure'] = $item->itemUnitOfMeasure;
                        $request_data_details['maxQty'] = $item->maximunQty;
                        $request_data_details['minQty'] = $item->minimumQty;
                        
                        $financeItemCategorySubAssigned = FinanceItemcategorySubAssigned::where('companySystemID', $item->companySystemID)
                            ->where('mainItemCategoryID', $item->financeCategoryMaster)
                            ->where('itemCategorySubID', $item->financeCategorySub)
                            ->first();
            
                        if (empty($financeItemCategorySubAssigned)) {
                            $is_failed= true;
                            //continue;
                           // return $this->sendError('Finance category not assigned for the selected item.');
                        }

                 
                        
                        // if ($item->financeCategoryMaster == 1) {
            
                        //     $alreadyAdded = PurchaseRequest::where('purchaseRequestID', $itemVal->purchaseRequestID)
                        //         ->whereHas('details', function ($query) use ($companySystemID, $purchaseRequest, $item) {
                        //             $query->where('itemPrimaryCode', $item->itemPrimaryCode);
                        //         })
                        //         ->first();

                        //         return $this->sendResponse($alreadyAdded, trans('custom.purchaserequestdetails_copied_successfully'));
                        //         die();

                        //     if ($alreadyAdded) {
                        //         $is_failed= true;
                             
                        //     }
                        // }
                     
                        $request_data_details['financeGLcodebBSSystemID'] = $financeItemCategorySubAssigned->financeGLcodebBSSystemID;
                        $request_data_details['financeGLcodebBS'] = $financeItemCategorySubAssigned->financeGLcodebBS;
                        
                        if ($item->financeCategoryMaster == 3) {
                            $assetCategory = AssetFinanceCategory::find($item->faFinanceCatID);
                            if (!$assetCategory) {
                                $is_failed= true;
                               // continue;
                                //return $this->sendError('Asset category not assigned for the selected item.');
                            }
                            $request_data_details['financeGLcodePLSystemID'] = $assetCategory->COSTGLCODESystemID;
                            $request_data_details['financeGLcodePL'] = $assetCategory->COSTGLCODE;
                        } else {
                            $request_data_details['financeGLcodePLSystemID'] = $financeItemCategorySubAssigned->financeGLcodePLSystemID;
                            $request_data_details['financeGLcodePL'] = $financeItemCategorySubAssigned->financeGLcodePL;
                        }
                        
                        $request_data_details['includePLForGRVYN'] = $financeItemCategorySubAssigned->includePLForGRVYN;
                        
                        $allowFinanceCategory = CompanyPolicyMaster::where('companyPolicyCategoryID', 20)
                                ->where('companySystemID', $purchaseRequest->companySystemID)
                                ->first();

                         
                        if ($allowFinanceCategory) {
                            $policy = $allowFinanceCategory->isYesNO;
            
                            if ($policy == 0) {
                                if ($purchaseRequest->financeCategory == null || $purchaseRequest->financeCategory == 0) {
                                    $is_failed= true;
                                    //continue;
                                   // return $this->sendError(trans('custom.category_is_not_found'), 500);
                                }
            
                                //checking if item category is same or not
                                $pRDetailExistSameItem = PurchaseRequestDetails::select(DB::raw('DISTINCT(itemFinanceCategoryID) as itemFinanceCategoryID'))
                                    ->where('purchaseRequestID', $purchaseRequest->purchaseRequestID)
                                    ->first();
            
                                if ($pRDetailExistSameItem) {
                                    if ($item->financeCategoryMaster != $pRDetailExistSameItem["itemFinanceCategoryID"]) {
                                        $is_failed= true;
                                       // continue;
                                       // return $this->sendError(trans('custom.you_cannot_add_different_category_item'), 500);
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
            
                                $checkWhether = PurchaseRequest::where('purchaseRequestID', '!=', $new_purchaseRequests->purchaseRequestID)
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
                              
                                })
                                    ->where('approved', 0)
                                    ->where('cancelledYN', 0)
                                    ->first();
                                /* approved=0 And cancelledYN=0*/

                            
            
                                if (!empty($anyPendingApproval)) {
                                    $is_failed= true;
                                   // continue;
                                    //return $this->sendError("There is a purchase request (" . $anyPendingApproval->purchaseRequestCode . ") pending for approval for the item you are trying to add. Please check again.", 500);
                                }
                                

                            
                                $anyApprovedPRButPONotProcessed = PurchaseRequest::where('purchaseRequestID', '!=', $new_purchaseRequests->purchaseRequestID)
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
                                // return $this->sendResponse($anyApprovedPRButPONotProcessed, trans('custom.success_export'));
                                // die();
                                if (!empty($anyApprovedPRButPONotProcessed)) {
                                    $is_failed= true;
                                    //continue;
                                   // return $this->sendError("There is a purchase request (" . $anyApprovedPRButPONotProcessed->purchaseRequestCode . ") approved hense PO is not processed for the item you are trying to add. Please check again", 500);
                                }
                                
                                $anyApprovedPRButPOPartiallyProcessed = PurchaseRequest::where('purchaseRequestID', '!=', $new_purchaseRequests->purchaseRequestID)
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
                                    $is_failed= true;
                                   // continue;
                                   // return $this->sendError("There is a purchase request (" . $anyApprovedPRButPOPartiallyProcessed->purchaseRequestCode . ") approved and PO is partially processed for the item you are trying to add. Please check again", 500);
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
                                    $is_failed= true;
                                   // continue;
                                   // return $this->sendError("There is a purchase order (" . $checkPOPending->purchaseOrderCode . ") pending for approval for the item you are trying to add. Please check again.", 500);
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
                            ->where('itemCode', $itemVal->itemCode)
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

                        
                                    
                        $quantityInHand = ErpItemLedger::where('itemSystemCode', $itemVal->itemCode)
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
                            ->where('itemCode', $itemVal->itemCode)
                            ->groupBy('erp_grvdetails.itemCode')
                            ->select(
                                [
                                    'erp_grvdetails.companySystemID',
                                    'erp_grvdetails.itemCode'
                                ])
                            ->sum('noQty');
            
                        $quantityOnOrder = $poQty - $grvQty;
                        $request_data_details['poQuantity'] = $poQty;
                        $request_data_details['quantityOnOrder'] = $quantityOnOrder;
                        $request_data_details['quantityInHand'] = $quantityInHand;
                                    
            
                    } else {
                        $request_data_details['estimatedCost'] = 0;
                        $request_data_details['companySystemID'] = $companySystemID;
                        $request_data_details['companyID'] = $purchaseRequest->companyID;
                        $request_data_details['unitOfMeasure'] = null;
                        $request_data_details['maxQty'] = 0;
                        $request_data_details['minQty'] = 0;
                        $request_data_details['poQuantity'] = 0;
                        $request_data_details['quantityOnOrder'] = 0;
                        $request_data_details['quantityInHand'] = 0;
                        $request_data_details['itemCode'] = null;
                    }

                   
             
                    $request_data_details['quantityRequested'] = $itemVal->quantityRequested;  
                    $request_data_details['totalCost'] = $itemVal->totalCost;  
                    $request_data_details['comments'] = $itemVal->comments;  
                    $request_data_details['itemCategoryID'] = $itemVal->itemCategoryID;
                    $request_data_details['isMRPulled'] = $itemVal->isMRPulled;  
               

                    if(!$is_failed)
                    {
                        $succes_item++;
                        array_push($valid_items,$request_data_details);
                       // 
                    }
                   
              
              
                }
                //$request_data_details['purchaseRequestID'] = $purchaseRequests->purchaseRequestID;  
         
            //DB::commit();


            // return $this->sendResponse($purchaseRequests->toArray(), trans('custom.purchase_request_saved_successfully'));
            //  die();
      
            $segment_success = true;;
            if($item_count_obj > 0)
            {
                if($succes_item == 0)
                {   
                    $new_purchaseRequests->delete();
                    return $this->sendError("Cannot copy this purchase request. Because all the items included in this document are pulled from pending PR/PO documents", 501);
                }
                else
                {   
      
                    foreach($valid_items as $valid_item)
                    {
                      
                        $valid_item['purchaseRequestID'] = $new_purchaseRequests['purchaseRequestID'];

                        $purchaseRequestDetails = $this->purchaseRequestDetailsRepository->create($valid_item);
                                         
                        $checkAlreadyAllocated = SegmentAllocatedItem::where('serviceLineSystemID', '!=',$new_purchaseRequests->serviceLineSystemID)
                                                                 ->where('documentSystemID', $new_purchaseRequests->documentSystemID)
                                                                 ->where('documentMasterAutoID', $new_purchaseRequests->purchaseRequestID)
                                                                 ->where('documentDetailAutoID', $purchaseRequestDetails->purchaseRequestDetailsID)
                                                                 ->get();


                                                                         
        
                        if (sizeof($checkAlreadyAllocated) == 0) {
                            $checkAlreadyAllocated = SegmentAllocatedItem::where('serviceLineSystemID',$new_purchaseRequests->serviceLineSystemID)
                                                                 ->where('documentSystemID', $new_purchaseRequests->documentSystemID)
                                                                 ->where('documentMasterAutoID', $new_purchaseRequests->purchaseRequestID)
                                                                 ->where('documentDetailAutoID', $purchaseRequestDetails->purchaseRequestDetailsID)
                                                                 ->delete();

                                                                         
        
                            $allocationData = [
                                'serviceLineSystemID' => $new_purchaseRequests->serviceLineSystemID,
                                'documentSystemID' => $new_purchaseRequests->documentSystemID,
                                'docAutoID' => $new_purchaseRequests->purchaseRequestID,
                                'docDetailID' => $purchaseRequestDetails->purchaseRequestDetailsID
                            ];
                            

                         
                            $segmentAllocatedItem = $this->segmentAllocatedItemRepository->allocateSegmentWiseItem($allocationData);
        
                            if (!$segmentAllocatedItem['status']) {
                                $segment_success = false;
                            }
                        } else {
                             $allocatedQty = SegmentAllocatedItem::where('documentSystemID', $new_purchaseRequests->documentSystemID)
                                                         ->where('documentMasterAutoID', $new_purchaseRequests->purchaseRequestID)
                                                         ->where('documentDetailAutoID', $purchaseRequestDetails->purchaseRequestDetailsID)
                                                         ->sum('allocatedQty');
        
                            if ($allocatedQty > $purchaseRequestDetails->quantityRequested) {
                                $segment_success = false;
                            }
                        }
                  

                       
                    }
              
                    if($is_failed)
                    { 
                        if(!$segment_success)
                        {
                            return $this->sendResponse($items, 'Out of '.$item_count_obj.' Items, '.$succes_item .' Items copied some items segment allocation failed');
                        }
                        else
                        {
                            return $this->sendResponse($items, 'Out of '.$item_count_obj.' Items, '.$succes_item .' Items are copied');
                        }
                        
                    }
                    else
                    {
                        if(!$segment_success)
                        {
                            return $this->sendResponse($items, trans('custom.purchaserequest_copied_successfully'));
                        }
                        else
                        {
                            return $this->sendResponse($items, trans('custom.purchaserequest_copied_successfully'));
                        }
                        
                    }
                }
 
                
            }
            else 
            {
                $purchaseRequests = $this->purchaseRequestRepository->create($request_data);
                return $this->sendResponse($items, trans('custom.purchaserequest_copied_successfully'));

            }
        } catch (\Exception $exception) {
            //DB::rollBack();
            return $this->sendError("Unable to copy purchase request", 501);
        }

    }


    public function removeAllItems($id) {
        $purchase_request = PurchaseRequest::find($id);
        if($purchase_request) {
            $purchase_request_details = PurchaseRequestDetails::where('purchaseRequestID',$id)->get();

            foreach($purchase_request_details as $purchase_request_detail) {
                    $data = PulledItemFromMR::where('purcahseRequestID',$id)->where('itemCodeSystem',$purchase_request_detail->itemCode)->first();
                    if(isset($data)) {
                            $m_id =$data->RequestID;
                            $request = MaterielRequest::find($m_id);
                            $request->isSelectedToPR =  false;
                            $request->save();
                            $data->delete();
                    }
            }
            PurchaseRequestDetails::where('purchaseRequestID',$id)->delete();
            PurchaseRequest::where('purchaseRequestID', $id)->update(['counter' => 0]);
    
            SegmentAllocatedItem::where('documentMasterAutoID', $id)
            ->where('documentSystemID', $purchase_request->documentSystemID)
            ->delete();
    
    
            return $this->sendResponse([], trans('custom.item_deleted_successfully'));
        }else {
            return $this->sendError(trans('custom.purchase_request_not_found'));
        }
        
    }

}
