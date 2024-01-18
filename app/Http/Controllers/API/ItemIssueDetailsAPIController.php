<?php
/**
 * =============================================
 * -- File Name : ItemIssueDetailsAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Item Issue Details
 * -- Author : Mohamed Fayas
 * -- Create date : 20 - June 2018
 * -- Description : This file contains the all CRUD for Item Issue Details
 * -- REVISION HISTORY
 * -- Date: 22-June 2018 By: Fayas Description: Added new functions named as getItemsByMaterielIssue()
 * -- Date: 25-June 2018 By: Fayas Description: Added new functions named as getItemsOptionsMaterielIssue()
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateItemIssueDetailsAPIRequest;
use App\Http\Requests\API\UpdateItemIssueDetailsAPIRequest;
use App\Models\Company;
use App\Models\CompanyPolicyMaster;
use App\Models\CustomerInvoiceDirect;
use App\Models\DocumentSubProduct;
use App\Models\ItemSerial;
use App\Models\PurchaseOrderDetails;
use App\Models\DeliveryOrder;
use App\Models\ErpItemLedger;
use App\Models\FinanceItemcategorySubAssigned;
use App\Models\ItemAssigned;
use App\Models\ItemClientReferenceNumberMaster;
use App\Models\ItemIssueDetails;
use App\Models\ItemMaster;
use App\Models\ItemIssueMaster;
use App\Models\GRVDetails;
use App\Models\MaterielRequest;
use App\Models\MaterielRequestDetails;
use App\Models\PurchaseReturn;
use App\Models\SegmentMaster;
use App\Models\StockTransfer;
use App\Models\Unit;
use App\Models\UnitConversion;
use App\Models\WarehouseMaster;
use App\Repositories\ItemIssueDetailsRepository;
use App\Services\Inventory\MaterialIssueService;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use App\helper\ItemTracking;
use App\Jobs\AddBulkItem\ItemIssueBulkItemsJob;
use App\Services\MaterialRequestService;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\ExpenseAssetAllocation;
use App\Models\ExpenseEmployeeAllocation;

/**
 * Class ItemIssueDetailsController
 * @package App\Http\Controllers\API
 */
class ItemIssueDetailsAPIController extends AppBaseController
{
    /** @var  ItemIssueDetailsRepository */
    private $itemIssueDetailsRepository;
    private $userRepository;

    public function __construct(ItemIssueDetailsRepository $itemIssueDetailsRepo,UserRepository $userRepo)
    {
        $this->userRepository = $userRepo;
        $this->itemIssueDetailsRepository = $itemIssueDetailsRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/itemIssueDetails",
     *      summary="Get a listing of the ItemIssueDetails.",
     *      tags={"ItemIssueDetails"},
     *      description="Get all ItemIssueDetails",
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
     *                  @SWG\Items(ref="#/definitions/ItemIssueDetails")
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
        $this->itemIssueDetailsRepository->pushCriteria(new RequestCriteria($request));
        $this->itemIssueDetailsRepository->pushCriteria(new LimitOffsetCriteria($request));
        $itemIssueDetails = $this->itemIssueDetailsRepository->all();

        return $this->sendResponse($itemIssueDetails->toArray(), 'Materiel Issue Details retrieved successfully');
    }

    /**
     * @param CreateItemIssueDetailsAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/itemIssueDetails",
     *      summary="Store a newly created ItemIssueDetails in storage",
     *      tags={"ItemIssueDetails"},
     *      description="Store ItemIssueDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ItemIssueDetails that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ItemIssueDetails")
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
     *                  ref="#/definitions/ItemIssueDetails"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateItemIssueDetailsAPIRequest $request)
    {
        $input = $request->all();

        $input = $this->convertArrayToValue($input);


        $companySystemID = $input['companySystemID'];

        $itemIssue = ItemIssueMaster::where('itemIssueAutoID', $input['itemIssueAutoID'])->first();


        if (empty($itemIssue)) {
            return $this->sendError('Materiel Issue not found', 500);
        }

        if(isset($input['type']) && $input["type"] == "MRFROMMI") {  
            $validator = \Validator::make($itemIssue->toArray(), [
                'serviceLineSystemID' => 'required|numeric|min:1',
                'issueType' => 'required|numeric|min:1',
            ]);
        }else {
            $validator = \Validator::make($itemIssue->toArray(), [
                'serviceLineSystemID' => 'required|numeric|min:1',
                'wareHouseFrom' => 'required|numeric|min:1',
                'issueType' => 'required|numeric|min:1',
            ]);
        }


        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        if(isset($input['type']) && $input["type"] != "MRFROMMI") {  
            if ($itemIssue->wareHouseFrom) {
                $wareHouse = WarehouseMaster::where("wareHouseSystemCode", $itemIssue->wareHouseFrom)->first();
    
                if (empty($wareHouse)) {
                    return $this->sendError('Warehouse not found', 500);
                }
                if ($wareHouse->isActive == 0) {
                    return $this->sendError('Please select an active warehouse.', 500);
                }
            } else {
                return $this->sendError('Please select a warehouse.', 500);
            }
        }


        if ($itemIssue->serviceLineSystemID) {
            $checkDepartmentActive = SegmentMaster::find($itemIssue->serviceLineSystemID);
            if (empty($checkDepartmentActive)) {
                return $this->sendError('Department not found');
            }
            if ($checkDepartmentActive->isActive == 0) {
                return $this->sendError('Please select an active department', 500);
            }
        } else {
            return $this->sendError('Please select a department.', 500);
        }

        if (isset($input['issueType'])) {
            if ($input['issueType'] == 1) {
                $item = ItemAssigned::where('idItemAssigned', $input['itemCode'])
                    ->where('companySystemID', $companySystemID)
                    ->first();
            } else if ($input['issueType'] == 2) {
                $item = MaterielRequestDetails::where('RequestDetailsID', $input['itemCode'])->with(['item_by'])->first();

                if ($item && is_null($item->itemCode)) {
                    if (isset($input['mappingItemCode']) && $input['mappingItemCode'] > 0) {
                        $itemMap = $this->matchRequestItem($item->RequestID, $input['mappingItemCode'], $companySystemID, $item->toArray());
                        if (!$itemMap['status']) {
                            return $this->sendError($itemMap['message'], 500);
                        } else {
                            $item = $itemMap['data'];
                        }
                    } else {
                        return $this->sendError('Item not found, Please map this item with a original item', 500, ["type" => 'itemMap']);
                    }
                }
            }
        } else {
            return $this->sendError('Please select Issue Type', 500);
        }

        if (empty($item)) {
            return $this->sendError('Item not found');
        }

        if (isset($input['itemIssueAutoID'])) {
            if ($input['itemIssueAutoID'] > 0) {
                $itemIssueMaster = ItemIssueMaster::where('itemIssueAutoID', $input['itemIssueAutoID'])->first();

                if (empty($itemIssueMaster)) {
                    return $this->sendError('Materiel Issue not found');
                }
            } else {
                return $this->sendError('Materiel Issue not found');
            }
        } else {
            return $this->sendError('Materiel Issue not found');
        }


        $input['itemIssueCode'] = $itemIssueMaster->itemIssueCode;
        $input['p1'] =  $itemIssueMaster->purchaseOrderNo;
        $input['comments'] = null;


        $company = Company::where('companySystemID', $companySystemID)->first();

        if (empty($company)) {
            return $this->sendError('Company not found');
        }

        $input['localCurrencyID'] = $company->localCurrencyID;
        $input['reportingCurrencyID'] = $company->reportingCurrency;
        $input['clientReferenceNumber'] = NULL;
        $input['selectedForBillingOP'] = 0;
        $input['selectedForBillingOPtemp'] = 0;
        $input['opTicketNo'] = 0;
        $input['issueCostRpt'] = 0;


        if ($input['issueType'] == 1) {

            $input['itemCodeSystem'] = $item->itemCodeSystem;
            $input['itemPrimaryCode'] = $item->itemPrimaryCode;
            $input['itemDescription'] = $item->itemDescription;
            $input['itemUnitOfMeasure'] = $item->itemUnitOfMeasure;
            $input['unitOfMeasureIssued'] = $item->itemUnitOfMeasure;

            if ($item->maximunQty) {
                $input['maxQty'] = $item->maximunQty;
            } else {
                $input['maxQty'] = 0;
            }

            if ($item->minimumQty) {
                $input['minQty'] = $item->minimumQty;
            } else {
                $input['minQty'] = 0;
            }

            $input['itemFinanceCategoryID'] = $item->financeCategoryMaster;
            $input['itemFinanceCategorySubID'] = $item->financeCategorySub;

            $input['convertionMeasureVal'] = 1;
            $input['qtyRequested'] = 0;
            $input['qtyIssued'] = 0;
            $input['qtyIssuedDefaultMeasure'] = 0;

        } else if ($input['issueType'] == 2) {


            $input['itemCodeSystem'] = $item->itemCode;
            $input['itemDescription'] = $item->itemDescription;

            $input['itemUnitOfMeasure'] = $item->unitOfMeasure;
            $input['unitOfMeasureIssued'] = $item->unitOfMeasureIssued;

            if ($item->maxQty) {
                $input['maxQty'] = $item->maxQty;
            } else {
                $input['maxQty'] = 0;
            }

            if ($item->minQty) {
                $input['minQty'] = $item->minQty;
            } else {
                $input['minQty'] = 0;
            }

            $input['itemFinanceCategoryID'] = $item->itemFinanceCategoryID;
            $input['itemFinanceCategorySubID'] = $item->itemFinanceCategorySubID;

            $input['convertionMeasureVal'] = $item->convertionMeasureVal;
            $input['qtyRequested'] = (isset($input['qntyMaterialIssue'])) ? $input['qntyMaterialIssue'] :$item->quantityRequested;
            $input['qtyIssued'] = (isset($input['qntyMaterialIssue'])) ? $input['qntyMaterialIssue'] : $item->quantityRequested;
            $input['qtyIssuedDefaultMeasure'] =  (isset($input['qntyMaterialIssue'])) ? $input['qntyMaterialIssue'] : $item->quantityRequested;
            $input['itemPrimaryCode'] = $item->item_by->primaryCode;
        }


        $itemMaster = ItemMaster::find($input['itemCodeSystem']);
        
        $mfq_no = $itemIssueMaster->mfqJobID;

        $input['trackingType'] = (isset($itemMaster->trackingType)) ? $itemMaster->trackingType : null;
      
        if(isset($input['type']) && $input["type"] == "MRFROMMI") {
            $data = array('companySystemID' => $companySystemID,
                'itemCodeSystem' => $input['itemCodeSystem'],
                'wareHouseId' => $input['wareHouseFrom']);

            $itemCurrentCostAndQty = \Inventory::itemCurrentCostAndQty($data);


            $input['currentStockQty'] = $itemCurrentCostAndQty['currentStockQty'];
            $input['currentWareHouseStockQty'] = $itemCurrentCostAndQty['currentWareHouseStockQty'];
            $input['currentStockQtyInDamageReturn'] = $itemCurrentCostAndQty['currentStockQtyInDamageReturn'];
            $input['issueCostLocal'] = $itemCurrentCostAndQty['wacValueLocal'];
            $input['issueCostRpt'] = $itemCurrentCostAndQty['wacValueReporting'];
            $input['issueCostLocalTotal'] = $input['issueCostLocal'] * $input['qtyIssuedDefaultMeasure'];
            $input['issueCostRptTotal'] = $input['issueCostRpt'] * $input['qtyIssuedDefaultMeasure'];

            if($input['qtyRequested'] > $input['currentStockQty']){
                ItemIssueMaster::where('itemIssueAutoID', $input['itemIssueAutoID'])->delete();
                return $this->sendError("Requested stock qty is greater than the current stock qty.", 500);
            }

            if ($input['currentStockQty'] <= 0) {
                ItemIssueMaster::where('itemIssueAutoID', $input['itemIssueAutoID'])->delete();
                return $this->sendError("Stock Qty is 0. You cannot issue.", 500);

            }

            if ($input['currentWareHouseStockQty'] <= 0) {
                ItemIssueMaster::where('itemIssueAutoID', $input['itemIssueAutoID'])->delete();
                return $this->sendError("Warehouse stock Qty is 0. You cannot issue.", 500);

            }

            if ($input['issueCostLocal'] == 0 || $input['issueCostRpt'] == 0) {
                // return $this->sendError("Cost is 0. You cannot issue.", 500);
            }

            if ($input['issueCostLocal'] < 0 || $input['issueCostRpt'] < 0) {
                ItemIssueMaster::where('itemIssueAutoID', $input['itemIssueAutoID'])->delete();
                return $this->sendError("Cost is negative. You cannot issue.", 500);
            }
        }else {
            $data = array('companySystemID' => $companySystemID,
            'itemCodeSystem' => $input['itemCodeSystem'],
            'wareHouseId' => $itemIssue->wareHouseFrom);

            $itemCurrentCostAndQty = \Inventory::itemCurrentCostAndQty($data);


            $input['currentStockQty'] = $itemCurrentCostAndQty['currentStockQty'];
            $input['currentWareHouseStockQty'] = $itemCurrentCostAndQty['currentWareHouseStockQty'];
            $input['currentStockQtyInDamageReturn'] = $itemCurrentCostAndQty['currentStockQtyInDamageReturn'];
            $input['issueCostLocal'] = $itemCurrentCostAndQty['wacValueLocal'];
            $input['issueCostRpt'] = $itemCurrentCostAndQty['wacValueReporting'];
            $input['issueCostLocalTotal'] = $input['issueCostLocal'] * $input['qtyIssuedDefaultMeasure'];
            $input['issueCostRptTotal'] = $input['issueCostRpt'] * $input['qtyIssuedDefaultMeasure'];


            if ($input['currentStockQty'] <= 0) {
                return $this->sendError("Stock Qty is 0. You cannot issue.", 500);
            }

            if ($input['currentWareHouseStockQty'] <= 0) {
                return $this->sendError("Warehouse stock Qty is 0. You cannot issue.", 500);
            }

            if ($input['issueCostLocal'] == 0 || $input['issueCostRpt'] == 0) {
                // return $this->sendError("Cost is 0. You cannot issue.", 500);
            }

            if ($input['issueCostLocal'] < 0 || $input['issueCostRpt'] < 0) {
                return $this->sendError("Cost is negative. You cannot issue.", 500);
            }
        }
        

        $financeItemCategorySubAssigned = FinanceItemcategorySubAssigned::where('companySystemID', $companySystemID)
            ->where('mainItemCategoryID', $input['itemFinanceCategoryID'])
            ->where('itemCategorySubID', $input['itemFinanceCategorySubID'])
            ->first();



        if (!empty($financeItemCategorySubAssigned)) {


            if(!empty($mfq_no) && WarehouseMaster::checkManuefactoringWareHouse($itemIssueMaster->wareHouseFrom))
            {
                $input['financeGLcodebBSSystemID'] = $financeItemCategorySubAssigned->financeGLcodebBSSystemID;
                $input['financeGLcodebBS'] = $financeItemCategorySubAssigned->financeGLcodebBS;
                $input['financeGLcodePLSystemID'] = WarehouseMaster::getWIPGLSystemID($itemIssueMaster->wareHouseFrom);
                $input['financeGLcodePL'] = WarehouseMaster::getWIPGLCode($itemIssueMaster->wareHouseFrom);

            }   
            else
            {
                
                $input['financeGLcodebBS'] = $financeItemCategorySubAssigned->financeGLcodebBS;
                $input['financeGLcodebBSSystemID'] = $financeItemCategorySubAssigned->financeGLcodebBSSystemID;
                $input['financeGLcodePL'] = $financeItemCategorySubAssigned->financeGLcodePL;
                $input['financeGLcodePLSystemID'] = $financeItemCategorySubAssigned->financeGLcodePLSystemID;
            }

            $input['includePLForGRVYN'] = $financeItemCategorySubAssigned->includePLForGRVYN;

        } else {
            return $this->sendError("Account code not updated.", 500);
        }

        if (!$input['financeGLcodebBS'] || !$input['financeGLcodebBSSystemID'] || !$input['financeGLcodePL'] || !$input['financeGLcodePLSystemID']) {
            return $this->sendError("Account code not updated.", 500);
        }
//
//        if ($input['itemFinanceCategoryID'] == 1) {
//            $alreadyAdded = ItemIssueMaster::where('itemIssueAutoID', $input['itemIssueAutoID'])
//                ->whereHas('details', function ($query) use ($input) {
//                    $query->where('itemCodeSystem', $input['itemCodeSystem']);
//                })
//                ->first();
//
//            if ($alreadyAdded) {
//                return $this->sendError("Selected item is already added. Please check again", 500);
//            }
//        }

        // check policy 18

        $allowPendingApproval = CompanyPolicyMaster::where('companyPolicyCategoryID', 18)
            ->where('companySystemID', $companySystemID)
            ->first();

        $checkWhether = ItemIssueMaster::where('itemIssueAutoID', '!=', $itemIssueMaster->itemIssueAutoID)
            ->where('companySystemID', $companySystemID)
            ->where('wareHouseFrom', $itemIssueMaster->wareHouseFrom)
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
            ->whereHas('details', function ($query) use ($companySystemID, $input) {
                $query->where('itemCodeSystem', $input['itemCodeSystem']);
            })
            ->where('approved', 0)
            ->first();
        /* approved=0*/

        if (!empty($checkWhether)) {
            return $this->sendError("There is a Materiel Issue (" . $checkWhether->itemIssueCode . ") pending for approval for the item you are trying to add. Please check again.", 500);
        }

        $checkWhetherStockTransfer = StockTransfer::where('companySystemID', $companySystemID)
            ->where('locationFrom', $itemIssueMaster->wareHouseFrom)
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
            ->whereHas('details', function ($query) use ($companySystemID, $input) {
                $query->where('itemCodeSystem', $input['itemCodeSystem']);
            })
            ->where('approved', 0)
            ->first();
        /* approved=0*/

        if (!empty($checkWhetherStockTransfer)) {
            return $this->sendError("There is a Stock Transfer (" . $checkWhetherStockTransfer->stockTransferCode . ") pending for approval for the item you are trying to add. Please check again.", 500);
        }

        /*check item sales invoice*/
        $checkWhetherInvoice = CustomerInvoiceDirect::where('companySystemID', $companySystemID)
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
            ->whereHas('issue_item_details', function ($query) use ($companySystemID, $input) {
                $query->where('itemCodeSystem', $input['itemCodeSystem']);
            })
            ->where('approved', 0)
            ->where('canceledYN', 0)
            ->first();
        /* approved=0*/

        if (!empty($checkWhetherInvoice)) {
            return $this->sendError("There is a Customer Invoice (" . $checkWhetherInvoice->bookingInvCode . ") pending for approval for the item you are trying to add. Please check again.", 500);
        }

        // check in delivery order
        $checkWhetherDeliveryOrder = DeliveryOrder::where('companySystemID', $companySystemID)
            ->select([
                'erp_delivery_order.deliveryOrderID',
                'erp_delivery_order.deliveryOrderCode'
            ])
            ->groupBy(
                'erp_delivery_order.deliveryOrderID',
                'erp_delivery_order.companySystemID'
            )
            ->whereHas('detail', function ($query) use ($companySystemID, $input) {
                $query->where('itemCodeSystem', $input['itemCodeSystem']);
            })
            ->where('approvedYN', 0)
            ->first();

        if (!empty($checkWhetherDeliveryOrder)) {
            return $this->sendError("There is a Delivery Order (" . $checkWhetherDeliveryOrder->deliveryOrderCode . ") pending for approval for the item you are trying to add. Please check again.", 500);
        }

        /*Check in purchase return*/
        $checkWhetherPR = PurchaseReturn::where('companySystemID', $companySystemID)
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
            ->whereHas('details', function ($query) use ($input) {
                $query->where('itemCode', $input['itemCodeSystem']);
            })
            ->where('approved', 0)
            ->first();
        /* approved=0*/

        if (!empty($checkWhetherPR)) {
            return $this->sendError("There is a Purchase Return (" . $checkWhetherPR->purchaseReturnCode . ") pending for approval for the item you are trying to add. Please check again.", 500);
        }


        if ($itemIssue->customerSystemID && $itemIssue->companySystemID && $itemIssue->contractUIID) {

            $clientReferenceNumber = ItemClientReferenceNumberMaster::where('companySystemID', $itemIssue->companySystemID)
                ->where('itemSystemCode', $input['itemCodeSystem'])
                ->where('customerID', $itemIssue->customerSystemID)
                ->where('contractUIID', $itemIssue->contractUIID)
                ->first();

            if (!empty($clientReferenceNumber)) {
                $input['clientReferenceNumber'] = $clientReferenceNumber->clientReferenceNumber;
            }
        }
        $input = MaterialIssueService::getItemDetailsForMaterialIssue($input);
        $itemIssueDetails = $this->itemIssueDetailsRepository->create($input);


        if ($itemIssue->issueType == 2) {

            if ($itemIssue->reqDocID > 0) {
                $detailExistMRDetail = MaterielRequestDetails::where('itemCode', $itemIssueDetails->itemCodeSystem)
                    ->where('RequestID', $itemIssue->reqDocID)
                    ->first();

                if (!empty($detailExistMRDetail)) {

                    $checkQuentity = ($detailExistMRDetail->qtyIssuedDefaultMeasure - $itemIssueDetails->qtyIssuedDefaultMeasure);

                    if ($checkQuentity > 0) {
                        $detailExistMRDetail->selectedForIssue = 0;
                    } else {
                        $detailExistMRDetail->selectedForIssue = -1;
                    }

                    $detailExistMRDetail->save();

                    $checkMRD = MaterielRequestDetails::where('selectedForIssue', 0)
                        ->where('RequestID', $itemIssue->reqDocID)
                        ->count();

                    $materielRequest = MaterielRequest::where('RequestID', $itemIssue->reqDocID)->first();

                    if (!empty($materielRequest)) {

                        if ($checkMRD == 0) {
                            $materielRequest->selectedForIssue = -1;
                        } else {
                            $materielRequest->selectedForIssue = 0;
                        }
                        $materielRequest->save();
                    }
                }
            }
        }


        $message = 'Materiel Issue Details saved successfully';
        if (($itemIssueDetails->currentStockQty - $itemIssueDetails->qtyIssuedDefaultMeasure) < $itemIssueDetails->minQty) {
            $minQtyPolicy = CompanyPolicyMaster::where('companySystemID', $itemIssue->companySystemID)
                ->where('companyPolicyCategoryID', 6)
                ->first();
            if (!empty($minQtyPolicy)) {
                if ($minQtyPolicy->isYesNO == 1) {
                    $itemIssueDetails->warningMsg = 1;
                    $message = 'Quantity is falling below the minimum inventory level.';
                }
            }
        }

        return $this->sendResponse($itemIssueDetails->toArray(), $message);
    }


    public function matchRequestItem($requestID, $itemCode, $companySystemID, $input)
    {
        $item = ItemAssigned::where('itemCodeSystem', $itemCode)
                            ->where('companySystemID', $companySystemID)
                            ->first();

        if (empty($item)) {
            return ['status' => false, 'message' => 'Item not found'];
        }

        $materielRequest = MaterielRequest::where('RequestID', $requestID)->first();


        if (empty($materielRequest)) {
            return ['status' => false, 'message' => 'Materiel Request Details not found'];
        }


        $input['itemCode'] = $item->itemCodeSystem;
        $input['item_by'] = ItemMaster::find($item->itemCodeSystem);
        $input['itemDescription'] = $item->itemDescription;
        $input['partNumber'] = $item->secondaryItemCode;
        $input['itemFinanceCategoryID'] = $item->financeCategoryMaster;
        $input['itemFinanceCategorySubID'] = $item->financeCategorySub;
        $input['unitOfMeasure'] = $item->itemUnitOfMeasure;
        $input['unitOfMeasureIssued'] = $item->itemUnitOfMeasure;
        if($item->maximunQty){
            $input['maxQty'] = $item->maximunQty;
        }else{
            $input['maxQty'] = 0;
        }

        if($item->minimumQty){
            $input['minQty'] = $item->minimumQty;
        }else{
            $input['minQty'] = 0;
        }

        $financeItemCategorySubAssigned = FinanceItemcategorySubAssigned::where('companySystemID', $item->companySystemID)
        ->where('mainItemCategoryID', $item->financeCategoryMaster)
        ->where('itemCategorySubID', $item->financeCategorySub)
        ->first();

        if (empty($financeItemCategorySubAssigned)) {
            return ['status' => false, 'message' => 'Finance Category not found'];
        }

        if ($item->financeCategoryMaster == 1) {

            $alreadyAdded = MaterielRequest::where('RequestID', $input['RequestID'])
                ->whereHas('details', function ($query) use ($item) {
                    $query->where('itemCode', $item->itemCodeSystem);
                })
                ->first();

            if ($alreadyAdded) {
                return ['status' => false, 'message' => 'Selected item is already added to above material request. Please check again'];
            }
        }

        $input['financeGLcodebBS']  = $financeItemCategorySubAssigned->financeGLcodebBS;
        $input['financeGLcodePL']   = $financeItemCategorySubAssigned->financeGLcodePL;
        $input['includePLForGRVYN'] = $financeItemCategorySubAssigned->includePLForGRVYN;


         $poQty = PurchaseOrderDetails::whereHas('order' , function ($query) use ($companySystemID) {
                                            $query->where('companySystemID', $companySystemID)
                                                ->where('approved', -1)
                                                ->where('poCancelledYN', 0);
                                     })
                                    ->where('itemCode', $input['itemCode'])
                                    ->groupBy('erp_purchaseorderdetails.companySystemID',
                                        'erp_purchaseorderdetails.itemCode')
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

        $grvQty = GRVDetails::whereHas('grv_master' , function ($query) use ($companySystemID) {
                            $query->where('companySystemID', $companySystemID)
                                ->where('grvTypeID', 2)
                                ->groupBy('erp_grvmaster.companySystemID');
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
        $input['quantityOnOrder'] = $quantityOnOrder;
        $input['quantityInHand']  = $quantityInHand;

        if($input['qtyIssuedDefaultMeasure'] > $input['quantityInHand']){
            return ['status' => false, 'message' => 'No stock Qty. Please check again'];
        }
       
        return ['status' => true, 'data' => (object)$input];
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/itemIssueDetails/{id}",
     *      summary="Display the specified ItemIssueDetails",
     *      tags={"ItemIssueDetails"},
     *      description="Get ItemIssueDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ItemIssueDetails",
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
     *                  ref="#/definitions/ItemIssueDetails"
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
        /** @var ItemIssueDetails $itemIssueDetails */
        $itemIssueDetails = $this->itemIssueDetailsRepository->findWithoutFail($id);

        if (empty($itemIssueDetails)) {
            return $this->sendError('Materiel Issue Details not found');
        }

        return $this->sendResponse($itemIssueDetails->toArray(), 'Materiel Issue Details retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateItemIssueDetailsAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/itemIssueDetails/{id}",
     *      summary="Update the specified ItemIssueDetails in storage",
     *      tags={"ItemIssueDetails"},
     *      description="Update ItemIssueDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ItemIssueDetails",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ItemIssueDetails that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ItemIssueDetails")
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
     *                  ref="#/definitions/ItemIssueDetails"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateItemIssueDetailsAPIRequest $request)
    {
        $message = "Item updated successfully";
        $input = array_except($request->all(), ['uom_default', 'uom_issuing','item_by']);
        $input = $this->convertArrayToValue($input);
        $qtyError = array('type' => 'qty','status' => "stock");
        /** @var ItemIssueDetails $itemIssueDetails */
        $itemIssueDetails = $this->itemIssueDetailsRepository->findWithoutFail($id);


        if (empty($itemIssueDetails)) {
            return $this->sendError('Materiel Issue Details not found');
        }

        if(isset($input['deliveryPrint'])){
            if($input['deliveryPrint'] == 1){
                if(isset($input['p1'])) {
                    $input['p1'] = intval($input['p1']);
                }
                $this->itemIssueDetailsRepository->update(array_only($input, ['backLoad','p1','pl10','pl3','grvDocumentNO',
                    'clientReferenceNumber','deliveryComments']), $id);
                return $this->sendResponse($itemIssueDetails->toArray(), $message);
            }
        }

        $itemIssue = ItemIssueMaster::where('itemIssueAutoID', $input['itemIssueAutoID'])->first();

        if (empty($itemIssue)) {
            return $this->sendError('Materiel Issue not found', 500);
        }


        if ($input['itemUnitOfMeasure'] != $input['unitOfMeasureIssued']) {
            $unitConvention = UnitConversion::where('masterUnitID', $input['itemUnitOfMeasure'])
                ->where('subUnitID', $input['unitOfMeasureIssued'])
                ->first();
            if (empty($unitConvention)) {
                return $this->sendError("Unit conversion isn't valid or configured", 500);
            }

            if ($unitConvention) {
                $convention = $unitConvention->conversion;
                $input['convertionMeasureVal'] = $convention;
                if ($convention > 0) {
                    $input['qtyIssuedDefaultMeasure'] = round(($input['qtyIssued'] / $convention), 2);
                } else {
                    $input['qtyIssuedDefaultMeasure'] = round(($input['qtyIssued'] * $convention), 2);
                }
            }
        } else {
            $input['qtyIssuedDefaultMeasure'] = $input['qtyIssued'];
        }

        if ($itemIssueDetails->issueCostLocal == 0 || $itemIssueDetails->issueCostRpt == 0) {
            $this->itemIssueDetailsRepository->update(['issueCostRptTotal' => 0,'qtyIssuedDefaultMeasure' => 0, 'qtyIssued' => 0], $id);
            return $this->sendError("Cost is 0. You cannot issue.", 500);
        }

        if ($itemIssueDetails->issueCostLocal < 0 || $itemIssueDetails->issueCostRpt < 0) {
            $this->itemIssueDetailsRepository->update(['issueCostRptTotal' => 0,'qtyIssuedDefaultMeasure' => 0, 'qtyIssued' => 0], $id);
            return $this->sendError("Cost is negative. You cannot issue.", 500);
        }

        if ($itemIssueDetails->currentStockQty <= 0) {
            $this->itemIssueDetailsRepository->update(['issueCostRptTotal' => 0,'qtyIssuedDefaultMeasure' => 0, 'qtyIssued' => 0], $id);
            return $this->sendError("Stock Qty is 0. You cannot issue.", 500);
        }

        if ($itemIssueDetails->currentWareHouseStockQty <= 0) {
            $this->itemIssueDetailsRepository->update(['issueCostRptTotal' => 0,'qtyIssuedDefaultMeasure' => 0, 'qtyIssued' => 0], $id);
            return $this->sendError("Warehouse stock Qty is 0. You cannot issue.", 500);
        }

        if ($input['qtyIssuedDefaultMeasure'] > $itemIssueDetails->currentStockQty) {
            $this->itemIssueDetailsRepository->update(['issueCostRptTotal' => 0,'qtyIssuedDefaultMeasure' => 0, 'qtyIssued' => 0], $id);
            return $this->sendError("Current stock Qty is: " . $itemIssueDetails->currentStockQty . " .You cannot issue more than the current stock qty.", 500, $qtyError);
        }

        if ($input['qtyIssuedDefaultMeasure'] > $itemIssueDetails->currentWareHouseStockQty) {
            $this->itemIssueDetailsRepository->update(['issueCostRptTotal' => 0,'qtyIssuedDefaultMeasure' => 0, 'qtyIssued' => 0], $id);
            $qtyError = array('type' => 'qty','status' => 'warehouse');
            $qtyError['diff_item'] = ["item_id" => $id,"diff_qnty" => ($input['qtyIssuedDefaultMeasure'] - $itemIssueDetails->currentWareHouseStockQty)];
            return $this->sendError("Current warehouse stock Qty is: " . $itemIssueDetails->currentWareHouseStockQty . " .You cannot issue more than the current warehouse stock qty.", 500, $qtyError);
        }

        $input['issueCostLocalTotal'] = $itemIssueDetails->issueCostLocal * $input['qtyIssuedDefaultMeasure'];
        $input['issueCostRptTotal'] = $itemIssueDetails->issueCostRpt * $input['qtyIssuedDefaultMeasure'];


        if ($input['qtyIssued'] == '' || is_null($input['qtyIssued'])) {
            $input['qtyIssued'] = 0;
            $input['qtyIssuedDefaultMeasure'] = 0;
        }
        if ($itemIssue->issueType == 2) {
            if($input['qtyIssuedDefaultMeasure'] > $itemIssueDetails->qtyAvailableToIssue){
                $this->itemIssueDetailsRepository->update(['issueCostRptTotal' => 0,'qtyIssuedDefaultMeasure' => 0, 'qtyIssued' => 0], $id);
                // $qtyError['diff_item'] = ["item_id" => $id,"diff_qnty" => ($input['qtyIssuedDefaultMeasure'] - $itemIssueDetails->qtyRequested)];
                if($itemIssueDetails->qtyAvailableToIssue == 0) {
                    return $this->sendError("Qty fully issued for this item", 500, $qtyError);
                }else {
                    return $this->sendError("Issuing qty cannot be more than requested qty/remaining qty", 500, $qtyError);
                }
            }
        }
 

        if ($itemIssue->issueType == 1) 
            {
                $allocatedSum = ExpenseAssetAllocation::where('documentDetailID', $input['itemIssueDetailID'])
                ->where('documentSystemID', $itemIssue->documentSystemID)
                ->where('documentSystemCode', $input['itemIssueAutoID'])
                ->sum('allocation_qty');

                if ($allocatedSum > $input['qtyIssued']) {
                    return $this->sendError("Allocated quantity cannot be greater than the detail quantity.");
                }

                $allocatedQtySum = ExpenseEmployeeAllocation::where('documentDetailID', $input['itemIssueDetailID'])
                ->where('documentSystemID', $itemIssue->documentSystemID)
                ->where('documentSystemCode', $input['itemIssueAutoID'])
                ->sum('assignedQty');

                
               
                if ($allocatedQtySum > $input['qtyIssued']) {
                    return $this->sendError("Allocated quantity cannot be greater than the detail quantity.");
                }
            }

        $input =  MaterialIssueService::getItemDetailsForMaterialIssueUpdate($input);
        $itemIssueDetails = $this->itemIssueDetailsRepository->update($input, $id);


        if ($itemIssue->issueType == 2) {

            if ($itemIssue->reqDocID > 0) {
                $detailExistMRDetail = MaterielRequestDetails::where('itemCode', $itemIssueDetails->itemCodeSystem)
                    ->where('RequestID', $itemIssue->reqDocID)
                    ->first();

                if (!empty($detailExistMRDetail)) {

                    $checkQuentity = ($detailExistMRDetail->qtyIssuedDefaultMeasure - $itemIssueDetails->qtyIssuedDefaultMeasure);

                    if ($checkQuentity > 0) {
                        $detailExistMRDetail->selectedForIssue = 0;
                    } else {
                        $detailExistMRDetail->selectedForIssue = -1;
                    }

                    $detailExistMRDetail->save();

                    $checkMRD = MaterielRequestDetails::where('selectedForIssue', 0)
                        ->where('RequestID', $itemIssue->reqDocID)
                        ->count();

                    $materielRequest = MaterielRequest::where('RequestID', $itemIssue->reqDocID)->first();
                    if (!empty($materielRequest)) {

                        if ($checkMRD == 0) {
                            $materielRequest->selectedForIssue = -1;
                        } else {
                            $materielRequest->selectedForIssue = 0;
                        }
                        $materielRequest->save();
                    }
                }
            }
        }

        $itemIssueDetails->warningMsg = 0;

        if (($itemIssueDetails->currentStockQty - $itemIssueDetails->qtyIssuedDefaultMeasure) < $itemIssueDetails->minQty) {
            $minQtyPolicy = CompanyPolicyMaster::where('companySystemID', $itemIssue->companySystemID)
                ->where('companyPolicyCategoryID', 6)
                ->first();
            if (!empty($minQtyPolicy)) {
                if ($minQtyPolicy->isYesNO == 1) {
                    $itemIssueDetails->warningMsg = 1;
                    $message = 'Quantity is falling below the minimum inventory level.';
                }
            }
        }
        return $this->sendResponse($itemIssueDetails->toArray(), $message);
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/itemIssueDetails/{id}",
     *      summary="Remove the specified ItemIssueDetails from storage",
     *      tags={"ItemIssueDetails"},
     *      description="Delete ItemIssueDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ItemIssueDetails",
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
        /** @var ItemIssueDetails $itemIssueDetails */
        $itemIssueDetails = $this->itemIssueDetailsRepository->with(['item_by'])->findWithoutFail($id);

        if (empty($itemIssueDetails)) {
            return $this->sendError('Materiel Issue Details not found');
        }

        $itemIssue = ItemIssueMaster::where('itemIssueAutoID', $itemIssueDetails->itemIssueAutoID)->first();

        if (empty($itemIssue)) {
            return $this->sendError('Materiel Issue not found');
        }

        if ($itemIssue->issueType == 2) {

            if ($itemIssue->reqDocID > 0) {
                $detailExistMRDetail = MaterielRequestDetails::where('itemCode', $itemIssueDetails->itemCodeSystem)
                    ->where('RequestID', $itemIssue->reqDocID)
                    ->first();

                if (!empty($detailExistMRDetail)) {

                    $detailExistMRDetail->selectedForIssue = 0;
                    $detailExistMRDetail->save();

                    $materielRequest = MaterielRequest::where('RequestID', $itemIssue->reqDocID)->first();
                    if (!empty($materielRequest)) {
                        $materielRequest->selectedForIssue = 0;
                        $materielRequest->save();
                    }
                }
            }
        }

        if ($itemIssueDetails->trackingType == 2) {
            $validateSubProductSold = DocumentSubProduct::where('documentSystemID', $itemIssue->documentSystemID)
                                                         ->where('documentDetailID', $id)
                                                         ->where('sold', 1)
                                                         ->first();

            if ($validateSubProductSold) {
                return $this->sendError('You cannot delete this line item. Serial details are sold already.', 422);
            }

            $subProduct = DocumentSubProduct::where('documentSystemID', $itemIssue->documentSystemID)
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
        } else if ($itemIssueDetails->trackingType == 1) {
            $deleteBatch = ItemTracking::revertBatchTrackingSoldStatus($itemIssue->documentSystemID, $id);

            if (!$deleteBatch['status']) {
                return $this->sendError($deleteBatch['message'], 422);
            }
        }


        $itemIssueDetails->delete();

        return $this->sendResponse($id, 'Materiel Issue Details deleted successfully');
    }

    /**
     * Display a listing of the items by Materiel Issue.
     * GET|HEAD /getItemsByMaterielIssue
     *
     * @param Request $request
     * @return Response
     */
    public function getItemsByMaterielIssue(Request $request)
    {
        $input = $request->all();
        $rId = $input['itemIssueAutoID'];

        $items = ItemIssueDetails::where('itemIssueAutoID', $rId)
            ->with(['uom_default', 'uom_issuing','item_by'])
            ->get();

        
        foreach ($items as $item) {

            $issueUnit = Unit::all();

            $issueUnits = array();
            foreach ($issueUnit as $unit) {
                $temArray = array('value' => $unit->UnitID, 'label' => $unit->UnitShortCode);
                array_push($issueUnits, $temArray);
            }

            $item->issueUnits = $issueUnits;
        }

        return $this->sendResponse($items->toArray(), 'Request Details retrieved successfully');
    }

    public function removeAllItems($id)
    {
        $material_request = ItemIssueMaster::find($id);
        if($material_request){

            ItemIssueDetails::where('itemIssueAutoID', $id)->delete();
            ItemIssueMaster::where('itemIssueAutoID', $id)->update(['counter' => 0]);
            return $this->sendResponse([], 'Items Deleted Successfully');

        } else {
            return $this->sendError('Material Issue not found');

        }

    }

    public function getItemsByMaterielIssueByLimit(Request $request)
    {
        $input = $request->all();
        $rId = $input['itemIssueAutoID'];

        $materialIssue = ItemIssueMaster::where('itemIssueAutoID',$rId)->first();
        $materielRequestId = $materialIssue->reqDocID;

        $items = ItemIssueDetails::where('itemIssueAutoID', $rId)
            ->with(['uom_default', 'uom_issuing','item_by'])
            ->skip($input['skip'])->take($input['limit'])->get();
        $index = $input['skip'] + 1;
        $materialIssueObj = ItemIssueMaster::where('reqDocID',$materielRequestId)->whereNotIn('itemIssueAutoID',[$rId])->get();
        $itemIdArray = array();

            foreach ($items as $item) {
                $item['index'] = $index;
                $issuedTotal = 0;
                $index++;
                $issueUnit = Unit::all();
                $issueUnits = array();
                if($materialIssue->issueType == 2) {
                    foreach ($materialIssueObj as $mi) {
                        if($mi->itemIssueAutoID < $rId) {
                            $issuedItem = $mi->details()->where('itemCodeSystem',$item->itemCodeSystem)->first();
                            if(isset($issuedItem)) {
                                if(!collect($itemIdArray)->contains($issuedItem->itemIssueDetailID)) {
                                    array_push($itemIdArray,$issuedItem->itemIssueDetailID);
                                }
                                if($issuedItem->itemCodeSystem == $item->itemCodeSystem) {
                                    $issuedTotal += $issuedItem->qtyIssued;
                                }

                            }

                        }
                    }
                    $item['prev_issued_qnty'] = $issuedTotal;
                }
                foreach ($issueUnit as $unit) {
                    $temArray = array('value' => $unit->UnitID, 'label' => $unit->UnitShortCode);
                    array_push($issueUnits, $temArray);
                }
                $item->issueUnits = $issueUnits;
            }

        return $this->sendResponse($items->toArray(), 'Request Details retrieved successfully');
    }

    /**
     * get Items Options Materiel Issue
     * GET|HEAD /getItemsOptionsMaterielIssue
     *
     * @param Request $request
     * @return Response
     */
    public function getItemsOptionsMaterielIssue(Request $request)
    {
        $input = $request->all();
        $companyId = $input['companyId'];
        $allowOtherCategory = isset($input['allowOtherCategory'])?$input['allowOtherCategory']:0;
        if($allowOtherCategory == 1){
            $categories = [1,2,4];
        }else{
            $categories = [1];
        }

        if (array_key_exists('issueType', $input)) {

            if ($input['issueType'] == 1) {
                $items = ItemAssigned::where('companySystemID', $companyId)
                    ->where('isActive', 1)->where('isAssigned', -1)
                    ->whereIn('financeCategoryMaster', $categories)
                    ->select(['itemPrimaryCode', 'itemDescription', 'idItemAssigned', 'secondaryItemCode','itemCodeSystem']);

                if (array_key_exists('search', $input)) {
                    $search = $input['search'];
                    $items = $items->where(function ($query) use ($search) {
                        $query->where('itemPrimaryCode', 'LIKE', "%{$search}%")
                            ->orWhere('itemDescription', 'LIKE', "%{$search}%")
                            ->orWhere('secondaryItemCode', 'LIKE', "%{$search}%");
                    });
                }

                $items = $items->take(20)->get();
                return $this->sendResponse($items->toArray(), 'Data retrieved successfully');

            } else if ($input['issueType'] == 2) {
                if (array_key_exists('reqDocID', $input)) {
                    if ($input['reqDocID'] != 0) {
                        $items = MaterielRequestDetails::where('RequestID', $input['reqDocID'])
                            ->with(['item_by'])
                            ->select(['itemCode', 'itemDescription', 'RequestDetailsID',]);


                        if (array_key_exists('search', $input)) {
                            $search = $input['search'];
                            $items = $items->where(function ($query) use ($search) {
                                $query->whereHas('item_by', function ($q) use ($search) {
                                    $q->where(function ($query) use ($search) {
                                        $query->where('primaryCode', 'LIKE', "%{$search}%")
                                            ->orWhere('secondaryItemCode', 'LIKE', "%{$search}%");
                                    });
                                })->orWhere('itemDescription', 'LIKE', "%{$search}%");
                            });
                        }

                        $items = $items->take(20)->get();
                        $temArray = array();
                        foreach ($items as $item) {
                            $materialRequestQty = MaterielRequestDetails::where('RequestID', $input['reqDocID'])->where('itemCode',$item->itemCode)->first();
                            $materielIssue = ItemIssueMaster::with(['details'])->where('reqDocID',$input['reqDocID'])->get();
                            $totalIssuedQty = 0;
                            $totalQuantityRequested = $materialRequestQty->quantityRequested;
                            foreach ($materielIssue as $mi) {
                                $totalIssuedQty += $mi->details()->where('itemCodeSystem',$item->itemCode)->sum('qtyIssued');
                            }

                            $temp = array(
                                'itemDescription' => $item->itemDescription,
                                'RequestDetailsID' => $item->RequestDetailsID,
                                'itemPrimaryCode' => isset($item->item_by->primaryCode) ? $item->item_by->primaryCode : "",
                                'secondaryItemCode' => isset($item->item_by->secondaryItemCode) ? $item->item_by->secondaryItemCode : ""
                            );
                            if($totalQuantityRequested != 0 && ($totalQuantityRequested != $totalIssuedQty)) {
                                array_push($temArray, $temp);
                            }
                        }
                        return $this->sendResponse($temArray, 'Data retrieved successfully');

                    } else {
                        return $this->sendError('Please select the Request', 500);
                    }
                } else {
                    return $this->sendError('Please select the Request', 500);
                }
            }

        } else {
            return $this->sendError('Please select Issues Type', 500);
        }
    }

    public function materialIssuetDetailsAddAllItems(Request $request)
    {
        $input = $request->all();
        $id = Auth::id();
        $user = $this->userRepository->with(['employee'])->findWithoutFail($id);

        $input['employeeSystemID'] = $user ? $user->employee['employeeSystemID'] : null;
        $input['empID'] = $user ? $user->employee['empID'] : null;

        if (isset($input['addAllItems']) && $input['addAllItems']) {
            $db = isset($input['db']) ? $input['db'] : "";    

            $companySystemID = $input['companySystemID'];

            $itemIssue = ItemIssueMaster::where('itemIssueAutoID', $input['itemIssueAutoID'])->first();
    
    
            if (empty($itemIssue)) {
                return $this->sendError('Materiel Issue not found', 500);
            }
    
            if(isset($input['type']) && $input["type"] == "MRFROMMI") {  
                $validator = \Validator::make($itemIssue->toArray(), [
                    'serviceLineSystemID' => 'required|numeric|min:1',
                    'issueType' => 'required|numeric|min:1',
                ]);
            }else {
                $validator = \Validator::make($itemIssue->toArray(), [
                    'serviceLineSystemID' => 'required|numeric|min:1',
                    'wareHouseFrom' => 'required|numeric|min:1',
                    'issueType' => 'required|numeric|min:1',
                ]);
            }
    
    
            if ($validator->fails()) {
                return $this->sendError($validator->messages(), 422);
            }
    
            if(isset($input['type']) && $input["type"] != "MRFROMMI") {  
                if ($itemIssue->wareHouseFrom) {
                    $wareHouse = WarehouseMaster::where("wareHouseSystemCode", $itemIssue->wareHouseFrom)->first();
        
                    if (empty($wareHouse)) {
                        return $this->sendError('Warehouse not found', 500);
                    }
                    if ($wareHouse->isActive == 0) {
                        return $this->sendError('Please select an active warehouse.', 500);
                    }
                } else {
                    return $this->sendError('Please select a warehouse.', 500);
                }
            }
    
    
            if ($itemIssue->serviceLineSystemID) {
                $checkDepartmentActive = SegmentMaster::find($itemIssue->serviceLineSystemID);
                if (empty($checkDepartmentActive)) {
                    return $this->sendError('Department not found');
                }
                if ($checkDepartmentActive->isActive == 0) {
                    return $this->sendError('Please select an active department', 500);
                }
            } else {
                return $this->sendError('Please select a department.', 500);
            }
    
            if (isset($input['itemIssueAutoID'])) {
                if ($input['itemIssueAutoID'] > 0) {
                    $itemIssueMaster = ItemIssueMaster::where('itemIssueAutoID', $input['itemIssueAutoID'])->first();
    
                    if (empty($itemIssueMaster)) {
                        return $this->sendError('Materiel Issue not found');
                    }
                } else {
                    return $this->sendError('Materiel Issue not found');
                }
            } else {
                return $this->sendError('Materiel Issue not found');
            }
    
            $company = Company::where('companySystemID', $companySystemID)->first();

            if (empty($company)) {
                return $this->sendError('Company not found');
            }


            $data['isBulkItemJobRun'] = 1;

            $itemIssueMaster = ItemIssueMaster::where('itemIssueAutoID', $input['itemIssueAutoID'])->update($data);
            ItemIssueBulkItemsJob::dispatch($db, $input);

            return $this->sendResponse('', 'Items Added to Queue Please wait some minutes to process');
        } else {
            DB::beginTransaction();
            try {
                $invalidItems = [];
                foreach ($input['itemArray'] as $key => $value) {
            
                    $res = MaterialRequestService::validateMaterialIssueItem($value['itemCodeSystem'], $input['companySystemID'], $input['itemIssueAutoID']);
                    
                    if ($res['status']) {
                        MaterialRequestService::saveMaterialIssueItem($value['itemCodeSystem'], $input['companySystemID'], $input['itemIssueAutoID'], $input['empID'], $input['employeeSystemID']);
                    } else {
                        $invalidItems[] = ['itemCodeSystem' => $value['itemCodeSystem'], 'message' => $res['message']];
                    }
                }
                DB::commit();
                return $this->sendResponse('', 'Material Issue Items saved successfully');
            } catch (\Exception $exception) {
                DB::rollBack();
                return $this->sendError($exception->getMessage(), 500);
            }
        }
    }

    public function materialIssueValidateItem(Request $request)
    {
        $input = $request->all();

        return MaterialRequestService::validateMaterialIssueItem($input['itemCodeSystem'], $input['companySystemID'], $input['itemIssueAutoID']);
    }

}
