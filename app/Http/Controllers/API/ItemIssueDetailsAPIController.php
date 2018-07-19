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
use App\Models\ErpItemLedger;
use App\Models\FinanceItemcategorySubAssigned;
use App\Models\ItemAssigned;
use App\Models\ItemIssueDetails;
use App\Models\ItemIssueMaster;
use App\Models\MaterielRequest;
use App\Models\MaterielRequestDetails;
use App\Models\Unit;
use App\Models\UnitConversion;
use App\Repositories\ItemIssueDetailsRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class ItemIssueDetailsController
 * @package App\Http\Controllers\API
 */
class ItemIssueDetailsAPIController extends AppBaseController
{
    /** @var  ItemIssueDetailsRepository */
    private $itemIssueDetailsRepository;

    public function __construct(ItemIssueDetailsRepository $itemIssueDetailsRepo)
    {
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

        return $this->sendResponse($itemIssueDetails->toArray(), 'Item Issue Details retrieved successfully');
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
            return $this->sendError('Item Issue not found', 500);
        }

        if (isset($input['issueType'])) {
            if ($input['issueType'] == 1) {
                $item = ItemAssigned::where('itemCodeSystem', $input['itemCode'])
                    ->where('companySystemID', $companySystemID)
                    ->first();
            } else if ($input['issueType'] == 2) {
                $item = MaterielRequestDetails::where('RequestDetailsID', $input['itemCode'])->with(['item_by'])->first();;
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
                    return $this->sendError('Item Issue Master not found');
                }
            } else {
                return $this->sendError('Item Issue Master not found');
            }
        } else {
            return $this->sendError('Item Issue Master not found');
        }


        $input['itemIssueCode'] = $itemIssueMaster->itemIssueCode;
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

            $input['issueCostLocal'] = $item->wacValueLocal;
            $input['issueCostLocalTotal'] = $item->wacValueLocal * $input['qtyIssuedDefaultMeasure'];
            $input['issueCostRpt'] = $item->wacValueReporting;
            $input['issueCostRptTotal'] = $item->wacValueReporting * $input['qtyIssuedDefaultMeasure'];

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
            $input['qtyRequested'] = $item->quantityRequested;
            $input['qtyIssued'] = $item->quantityRequested;
            $input['qtyIssuedDefaultMeasure'] = $item->qtyIssuedDefaultMeasure;
            $input['itemPrimaryCode'] = $item->item_by->primaryCode;

            $itemAssigned = ItemAssigned::where('itemCodeSystem', $input['itemCodeSystem'])
                ->where('companySystemID', $companySystemID)
                ->first();

            $input['issueCostLocal'] = $itemAssigned->wacValueLocal;
            $input['issueCostLocalTotal'] = $itemAssigned->wacValueLocal * $input['qtyIssuedDefaultMeasure'];
            $input['issueCostRpt'] = $itemAssigned->wacValueReporting;
            $input['issueCostRptTotal'] = $itemAssigned->wacValueReporting * $input['qtyIssuedDefaultMeasure'];


        }

        // check policy 18

        $allowPendingApproval = CompanyPolicyMaster::where('companyPolicyCategoryID', 18)
            ->where('companySystemID', $companySystemID)
            ->first();

        if ($allowPendingApproval->isYesNO == 0) {

            $checkWhether = ItemIssueMaster::where('itemIssueAutoID', '!=', $itemIssueMaster->itemIssueAutoID)
                ->where('companySystemID', $companySystemID)
                ->where('wareHouseFromCode', $itemIssueMaster->wareHouseFromCode)
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
                )->whereHas('details', function ($query) use ($companySystemID, $input) {
                    $query->where('itemCodeSystem', $input['itemCodeSystem']);
                })
                ->where('approved', 0)
                ->first();
            /* approved=0*/

            if (!empty($checkWhether)) {
                return $this->sendError("There is a Materiel Issue (" . $checkWhether->itemIssueCode . ") pending for approval for the item you are trying to add. Please check again.", 500);
            }

        }

        $currentStockQty = ErpItemLedger::where('itemSystemCode', $input['itemCodeSystem'])
                                        ->where('companySystemID', $companySystemID)
                                        ->groupBy('itemSystemCode')
                                        ->sum('inOutQty');

        $currentWareHouseStockQty = ErpItemLedger::where('itemSystemCode', $input['itemCodeSystem'])
            ->where('companySystemID', $companySystemID)
            ->where('wareHouseSystemCode', $itemIssue->wareHouseFrom)
            ->groupBy('itemSystemCode')
            ->sum('inOutQty');
        $currentStockQtyInDamageReturn = ErpItemLedger::where('itemSystemCode', $input['itemCodeSystem'])
            ->where('companySystemID', $companySystemID)
            ->where('fromDamagedTransactionYN', 1)
            ->groupBy('itemSystemCode')
            ->sum('inOutQty');


        $input['currentStockQty'] = $currentStockQty;
        $input['currentWareHouseStockQty'] = $currentWareHouseStockQty;
        $input['currentStockQtyInDamageReturn'] = $currentStockQtyInDamageReturn;

        $financeItemCategorySubAssigned = FinanceItemcategorySubAssigned::where('companySystemID', $companySystemID)
            ->where('mainItemCategoryID', $input['itemFinanceCategoryID'])
            ->where('itemCategorySubID', $input['itemFinanceCategorySubID'])
            ->first();

        if (empty($financeItemCategorySubAssigned)) {
            return $this->sendError('Finance Category not found');
        }

        $input['financeGLcodebBS'] = $financeItemCategorySubAssigned->financeGLcodebBS;
        $input['financeGLcodebBSSystemID'] = $financeItemCategorySubAssigned->financeGLcodebBSSystemID;
        $input['financeGLcodePL'] = $financeItemCategorySubAssigned->financeGLcodePL;
        $input['financeGLcodePLSystemID'] = $financeItemCategorySubAssigned->financeGLcodePLSystemID;
        $input['includePLForGRVYN'] = $financeItemCategorySubAssigned->includePLForGRVYN;

        if ($input['itemFinanceCategoryID'] == 1) {
            $alreadyAdded = ItemIssueMaster::where('itemIssueAutoID', $input['itemIssueAutoID'])
                ->whereHas('details', function ($query) use ($input) {
                    $query->where('itemCodeSystem', $input['itemCodeSystem']);
                })
                ->first();

            if ($alreadyAdded) {
                return $this->sendError("Selected item is already added. Please check again", 500);
            }
        }

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
                        }else{
                            $materielRequest->selectedForIssue = 0;
                        }
                        $materielRequest->save();
                    }
                }
            }
        }


        return $this->sendResponse($itemIssueDetails->toArray(), 'Item Issue Details saved successfully');
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
            return $this->sendError('Item Issue Details not found');
        }

        return $this->sendResponse($itemIssueDetails->toArray(), 'Item Issue Details retrieved successfully');
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

        $input = array_except($request->all(), ['uom_default', 'uom_issuing']);
        $input = $this->convertArrayToValue($input);

        /** @var ItemIssueDetails $itemIssueDetails */
        $itemIssueDetails = $this->itemIssueDetailsRepository->findWithoutFail($id);

        if (empty($itemIssueDetails)) {
            return $this->sendError('Item Issue Details not found');
        }

        $itemIssue = ItemIssueMaster::where('itemIssueAutoID', $input['itemIssueAutoID'])->first();

        if (empty($itemIssue)) {
            return $this->sendError('Item Issue not found', 500);
        }


        if ($input['itemUnitOfMeasure'] != $input['unitOfMeasureIssued']) {
            $unitConvention = UnitConversion::where('masterUnitID', $input['itemUnitOfMeasure'])
                ->where('subUnitID', $input['unitOfMeasureIssued'])
                ->first();
            if (empty($unitConvention)) {
                return $this->sendError('Unit Convention not found', 500);
            }

            if ($unitConvention) {
                $convention = $unitConvention->conversion;
                $input['convertionMeasureVal'] = $convention;
                if ($convention > 0) {
                    $input['qtyIssuedDefaultMeasure'] = $input['qtyIssued'] / $convention;
                } else {
                    $input['qtyIssuedDefaultMeasure'] = $input['qtyIssued'] * $convention;
                }
            }
        } else {
            $input['qtyIssuedDefaultMeasure'] = $input['qtyIssued'];
        }


        if ((float)$input['qtyIssuedDefaultMeasure'] > $itemIssueDetails->maxQty) {
            return $this->sendError("No stock Qty. Please check again.", 500);
        }


        $input['issueCostLocalTotal'] = $itemIssueDetails->issueCostLocal * $input['qtyIssuedDefaultMeasure'];
        $input['issueCostRptTotal']   = $itemIssueDetails->issueCostRpt * $input['qtyIssuedDefaultMeasure'];

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
                        }else{
                            $materielRequest->selectedForIssue = 0;
                        }
                        $materielRequest->save();
                    }
                }
            }
        }

        return $this->sendResponse($itemIssueDetails->toArray(), 'ItemIssueDetails updated successfully');
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
        $itemIssueDetails = $this->itemIssueDetailsRepository->findWithoutFail($id);

        if (empty($itemIssueDetails)) {
            return $this->sendError('Item Issue Details not found');
        }

        $itemIssue = ItemIssueMaster::where('itemIssueAutoID', $itemIssueDetails->itemIssueAutoID)->first();

        if (empty($itemIssue)) {
            return $this->sendError('Item Issue not found');
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

        $itemIssueDetails->delete();

        return $this->sendResponse($id, 'Item Issue Details deleted successfully');
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
            ->with(['uom_default', 'uom_issuing'])
            ->get();


        foreach ($items as $item) {

            $issueUnit = Unit::where('UnitID', $item['itemUnitOfMeasure'])->with(['unitConversion.sub_unit'])->first();

            $issueUnits = array();
            foreach ($issueUnit->unitConversion as $unit) {
                $temArray = array('value' => $unit->sub_unit->UnitID, 'label' => $unit->sub_unit->UnitShortCode);
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

        if (array_key_exists('issueType', $input)) {

            if ($input['issueType'] == 1) {
                $items = ItemAssigned::where('companySystemID', $companyId)
                    ->where('financeCategoryMaster', 1)
                    ->select(['itemPrimaryCode', 'itemDescription', 'idItemAssigned']);

                if (array_key_exists('search', $input)) {
                    $search = $input['search'];
                    $items = $items->where(function ($query) use ($search) {
                        $query->where('itemPrimaryCode', 'LIKE', "%{$search}%")
                            ->orWhere('itemDescription', 'LIKE', "%{$search}%");
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
                                    $q->where('primaryCode', 'LIKE', "%{$search}%");
                                })
                                    ->orWhere('itemDescription', 'LIKE', "%{$search}%");
                            });
                        }

                        $items = $items->take(20)->get();
                        $temArray = array();
                        foreach ($items as $item) {
                            $temp = array(
                                'itemDescription' => $item->itemDescription,
                                'RequestDetailsID' => $item->RequestDetailsID,
                                'itemPrimaryCode' => $item->item_by->primaryCode);

                            array_push($temArray, $temp);
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

}
