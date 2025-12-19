<?php
/**
 * =============================================
 * -- File Name : ItemReturnDetailsAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Item Return Details
 * -- Author : Mohamed Fayas
 * -- Create date : 16 - July 2018
 * -- Description : This file contains the all CRUD for Document Attachments
 * -- REVISION HISTORY
 * -- Date: 16 - July 2018 By: Fayas Description: Added new functions named as getItemsByMaterielReturn(),getItemsOptionsMaterielReturn()
 *
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateItemReturnDetailsAPIRequest;
use App\Http\Requests\API\UpdateItemReturnDetailsAPIRequest;
use App\Models\CompanyPolicyMaster;
use App\Models\FinanceItemcategorySubAssigned;
use App\Models\ItemAssigned;
use App\Models\ItemIssueDetails;
use App\Models\ItemIssueMaster;
use App\Models\ItemSerial;
use App\Models\DocumentSubProduct;
use App\Models\ItemReturnDetails;
use App\Models\ItemReturnMaster;
use App\Models\SegmentMaster;
use App\Models\Unit;
use App\Models\UnitConversion;
use App\Models\WarehouseMaster;
use App\Repositories\ItemReturnDetailsRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use App\helper\ItemTracking;
Use App\Models\UserToken;
use GuzzleHttp\Client;
use Carbon\Carbon;
/**
 * Class ItemReturnDetailsController
 * @package App\Http\Controllers\API
 */
class ItemReturnDetailsAPIController extends AppBaseController
{
    /** @var  ItemReturnDetailsRepository */
    private $itemReturnDetailsRepository;

    public function __construct(ItemReturnDetailsRepository $itemReturnDetailsRepo)
    {
        $this->itemReturnDetailsRepository = $itemReturnDetailsRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/itemReturnDetails",
     *      summary="Get a listing of the ItemReturnDetails.",
     *      tags={"ItemReturnDetails"},
     *      description="Get all ItemReturnDetails",
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
     *                  @SWG\Items(ref="#/definitions/ItemReturnDetails")
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
        $this->itemReturnDetailsRepository->pushCriteria(new RequestCriteria($request));
        $this->itemReturnDetailsRepository->pushCriteria(new LimitOffsetCriteria($request));
        $itemReturnDetails = $this->itemReturnDetailsRepository->all();

        return $this->sendResponse($itemReturnDetails->toArray(), trans('custom.item_return_details_retrieved_successfully'));
    }

    /**
     * @param CreateItemReturnDetailsAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/itemReturnDetails",
     *      summary="Store a newly created ItemReturnDetails in storage",
     *      tags={"ItemReturnDetails"},
     *      description="Store ItemReturnDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ItemReturnDetails that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ItemReturnDetails")
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
     *                  ref="#/definitions/ItemReturnDetails"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateItemReturnDetailsAPIRequest $request)
    {
        $input = $request->all();

        $input = $this->convertArrayToValue($input);

        $companySystemID = $input['companySystemID'];

        $itemReturn = ItemReturnMaster::where('itemReturnAutoID', $input['itemReturnAutoID'])->first();

        if (empty($itemReturn)) {
            return $this->sendError(trans('custom.item_return_not_found'), 500);
        }

        $validator = \Validator::make($itemReturn->toArray(), [
            'serviceLineSystemID' => 'required|numeric|min:1',
            'wareHouseLocation' => 'required|numeric|min:1',
            'ReturnType' => 'required|numeric|min:1'
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        if ($itemReturn->wareHouseLocation) {
            $wareHouse = WarehouseMaster::where("wareHouseSystemCode", $itemReturn->wareHouseLocation)->first();
            if (empty($wareHouse)) {
                return $this->sendError(trans('custom.warehouse_not_found'), 500);
            }
            if ($wareHouse->isActive == 0) {
                return $this->sendError(trans('custom.please_select_active_warehouse'), 500);
            }
        } else {
            return $this->sendError(trans('custom.please_select_warehouse'), 500);
        }

        if ($itemReturn->serviceLineSystemID) {
            $checkDepartmentActive = SegmentMaster::find($itemReturn->serviceLineSystemID);
            if (empty($checkDepartmentActive)) {
                return $this->sendError(trans('custom.department_not_found'),500);
            }

            if ($checkDepartmentActive->isActive == 0) {
                return $this->sendError(trans('custom.please_select_active_department'), 500);
            }
        }else {
            return $this->sendError(trans('custom.please_select_department'), 500);
        }


        $input['itemReturnCode'] = $itemReturn->itemReturnCode;


        $itemAssign = ItemAssigned::with(['item_master'])
            ->where('itemCodeSystem', $input['itemCodeSystem'])
            ->where('companySystemID', $companySystemID)
            ->first();

        if (empty($itemAssign)) {
            return $this->sendError(trans('custom.item_not_found'), 500);
        }

        $input['itemPrimaryCode'] = $itemAssign->itemPrimaryCode;
        $input['itemDescription'] = $itemAssign->itemDescription;
        $input['trackingType'] = isset($itemAssign->item_master->trackingType) ? $itemAssign->item_master->trackingType : null;


        $itemIssuesCount = ItemIssueMaster::whereHas('details', function ($q) use ($input) {
                     $q->where('itemCodeSystem', $input['itemCodeSystem']);
              })
            ->where('companySystemID', $companySystemID)
            ->where('approved', -1)
            ->where('serviceLineSystemID', $itemReturn->serviceLineSystemID)
            ->where('wareHouseFrom', $itemReturn->wareHouseLocation)
            ->count();

        if ($itemIssuesCount == 0) {
            return $this->sendError(trans('custom.selected_item_not_issued_please_check_again'), 500);
        }

        $input['itemUnitOfMeasure'] = $itemAssign->itemUnitOfMeasure;
        $input['unitOfMeasureIssued'] = $itemAssign->itemUnitOfMeasure;

        $input['unitCostLocal'] = $itemAssign->wacValueLocal;
        $input['unitCostRpt'] = $itemAssign->wacValueReporting;

        $input['itemFinanceCategoryID'] = $itemAssign->financeCategoryMaster;
        $input['itemFinanceCategorySubID'] = $itemAssign->financeCategorySub;
        $input['convertionMeasureVal'] = 1;
        $input['localCurrencyID'] = $itemAssign->wacValueLocalCurrencyID;
        $input['reportingCurrencyID'] = $itemAssign->wacValueReportingCurrencyID;

        if ($input['unitCostLocal'] == 0 || $input['unitCostRpt'] == 0) {
            //return $this->sendError("Cost is 0. You cannot issue", 500);
        }

        if ($input['unitCostLocal'] < 0 || $input['unitCostRpt'] < 0) {
            return $this->sendError(trans('custom.cost_is_negative_you_cannot_issue'), 500);
        }

        $financeItemCategorySubAssigned = FinanceItemcategorySubAssigned::where('companySystemID', $companySystemID)
            ->where('mainItemCategoryID', $input['itemFinanceCategoryID'])
            ->where('itemCategorySubID', $input['itemFinanceCategorySubID'])
            ->first();


        if (!empty($financeItemCategorySubAssigned)) {

            if(WarehouseMaster::checkManuefactoringWareHouse($itemReturn->wareHouseLocation))
            {
                $input['financeGLcodebBSSystemID'] = $financeItemCategorySubAssigned->financeGLcodebBSSystemID;
                $input['financeGLcodebBS'] = $financeItemCategorySubAssigned->financeGLcodebBS;
                $input['financeGLcodePLSystemID'] = WarehouseMaster::getWIPGLSystemID($itemReturn->wareHouseLocation);
                $input['financeGLcodePL'] = WarehouseMaster::getWIPGLCode($itemReturn->wareHouseLocation);
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
            return $this->sendError(trans('custom.account_code_not_updated'), 500);
        }


        if (!$input['financeGLcodebBS'] || !$input['financeGLcodebBSSystemID'] || !$input['financeGLcodePL'] || !$input['financeGLcodePLSystemID']) {
            return $this->sendError(trans('custom.account_code_not_updated'), 500);
        }

        if ($input['itemFinanceCategoryID'] == 1) {
            $alreadyAdded = ItemReturnMaster::where('itemReturnAutoID', $input['itemReturnAutoID'])
                ->whereHas('details', function ($query) use ($input) {
                    $query->where('itemCodeSystem', $input['itemCodeSystem']);
                })
                ->first();

            if ($alreadyAdded) {
                return $this->sendError(trans('custom.selected_item_already_added_please_check_again'), 500);
            }
        }

        // check policy 18

        $allowPendingApproval = CompanyPolicyMaster::where('companyPolicyCategoryID', 18)
            ->where('companySystemID', $companySystemID)
            ->first();

        // if ($allowPendingApproval->isYesNO == 0) {

        $checkWhether = ItemReturnMaster::where('itemReturnAutoID', '!=', $itemReturn->itemReturnAutoID)
            ->where('companySystemID', $companySystemID)
            ->where('wareHouseLocation', $itemReturn->wareHouseLocation)
            ->select([
                'erp_itemreturnmaster.itemReturnAutoID',
                'erp_itemreturnmaster.companySystemID',
                'erp_itemreturnmaster.wareHouseLocation',
                'erp_itemreturnmaster.itemReturnCode',
                'erp_itemreturnmaster.approved'
            ])
            ->groupBy(
                'erp_itemreturnmaster.itemReturnAutoID',
                'erp_itemreturnmaster.companySystemID',
                'erp_itemreturnmaster.wareHouseLocation',
                'erp_itemreturnmaster.itemReturnCode',
                'erp_itemreturnmaster.approved'
            )->whereHas('details', function ($query) use ($companySystemID, $input) {
                $query->where('itemCodeSystem', $input['itemCodeSystem']);
            })
            ->where('approved', 0)
            ->first();
        /* approved=0*/

        if (!empty($checkWhether)) {
            return $this->sendError(trans('custom.material_return_pending_approval_for_item', ['code' => $checkWhether->itemReturnCode]), 500);
        }

        //}

        $itemReturnDetails = $this->itemReturnDetailsRepository->create($input);
        return $this->sendResponse($itemReturnDetails->toArray(), trans('custom.item_return_details_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/itemReturnDetails/{id}",
     *      summary="Display the specified ItemReturnDetails",
     *      tags={"ItemReturnDetails"},
     *      description="Get ItemReturnDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ItemReturnDetails",
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
     *                  ref="#/definitions/ItemReturnDetails"
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
        /** @var ItemReturnDetails $itemReturnDetails */
        $itemReturnDetails = $this->itemReturnDetailsRepository->findWithoutFail($id);

        if (empty($itemReturnDetails)) {
            return $this->sendError(trans('custom.item_return_details_not_found'));
        }

        return $this->sendResponse($itemReturnDetails->toArray(), trans('custom.item_return_details_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateItemReturnDetailsAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/itemReturnDetails/{id}",
     *      summary="Update the specified ItemReturnDetails in storage",
     *      tags={"ItemReturnDetails"},
     *      description="Update ItemReturnDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ItemReturnDetails",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ItemReturnDetails that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ItemReturnDetails")
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
     *                  ref="#/definitions/ItemReturnDetails"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateItemReturnDetailsAPIRequest $request)
    {
       
       
        $api_key = $request['api_key'];
        $input = array_except($request->all(), ['uom_issued', 'uom_receiving', 'issue','item_by','api_key']);
        $input = $this->convertArrayToValue($input);
        $qtyError = array('type' => 'qty');
        
      
   
        /** @var ItemReturnDetails $itemReturnDetails */
        $itemReturnDetails = $this->itemReturnDetailsRepository->findWithoutFail($id);

        if (empty($itemReturnDetails)) {
            return $this->sendError(trans('custom.item_return_details_not_found'));
        }

        $itemReturnMaster = ItemReturnMaster::find($input['itemReturnAutoID']);

        if (empty($itemReturnMaster)) {
            return $this->sendError(trans('custom.item_return_not_found'));
        }

        $isse_code = $input['issueCodeSystem'];
        if($isse_code != 0)
        {
            $bytes = random_bytes(10);
            $hashKey = bin2hex($bytes);
            $empID = \Helper::getEmployeeSystemID();


            $item_issue = ItemIssueMaster::find($isse_code);

            if (empty($item_issue)) {
                return $this->sendError(trans('custom.item_issue_not_found'));
            }
            $isManufacturing = WarehouseMaster::where('wareHouseSystemCode', $item_issue->wareHouseFrom)->where('manufacturingYN', 1)->first();

            if(!empty($isManufacturing)) {
                $insertData = [
                    'employee_id' => $empID,
                    'token' => $hashKey,
                    'expire_time' => Carbon::now()->addDays(1),
                    'module_id' => 1
                ];

                $resData = UserToken::create($insertData);
                $client = new Client();
                $res = $client->request('GET', env('MANUFACTURING_URL') . '/getAllocatedJobs?companyID=' . $item_issue->companySystemID . '&documentID=' . $item_issue->documentID . '&documentsystemcode=' . $item_issue->itemIssueAutoID . '&itemautoID=' . $itemReturnDetails->itemCodeSystem, [
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'token' => $hashKey,
                        'api_key' => $api_key
                    ]
                ]);

                if ($res->getStatusCode() == 200) {
                    $job = json_decode($res->getBody(), true);


                    if (count($job) > 0) {

                        $update_item['issueCodeSystem'] = null;
                        $this->itemReturnDetailsRepository->update($update_item, $id);

                        return $this->sendError(trans('custom.selected_material_issue_allocated_following_jobs'), 500, ['type' => 'allocated_job', 'data' => $job]);
                    }
                } else {
                    $update_item['issueCodeSystem'] = null;
                    $this->itemReturnDetailsRepository->update($update_item, $id);
                    return $this->sendError(trans('custom.unable_get_response_allocated_job_material_issue'));
                }
            }
        }

   

        if ($input['itemUnitOfMeasure'] != $input['unitOfMeasureIssued']) {
            $unitConvention = UnitConversion::where('masterUnitID', $input['itemUnitOfMeasure'])
                ->where('subUnitID', $input['unitOfMeasureIssued'])
                ->first();
            if (empty($unitConvention)) {
                return $this->sendError(trans('custom.unit_convention_not_found'), 500);
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

        if ($input['issueCodeSystem'] != $itemReturnDetails->issueCodeSystem) {

            $itemIssueDetail = ItemIssueDetails::where('itemIssueAutoID', $input['issueCodeSystem'])
                ->where('itemCodeSystem', $input['itemCodeSystem'])->first();

            if (!empty($itemIssueDetail)) {
                $input['itemUnitOfMeasure'] = $itemIssueDetail->itemUnitOfMeasure;
                $input['unitOfMeasureIssued'] = $itemIssueDetail->unitOfMeasureIssued;
                $input['unitCostLocal'] = $itemIssueDetail->issueCostLocal;
                $input['unitCostRpt'] = $itemIssueDetail->issueCostRpt;
                $input['qtyFromIssue'] = $itemIssueDetail->qtyIssuedDefaultMeasure;
                $input['qtyIssued'] = 0;
                $input['qtyIssuedDefaultMeasure'] = 0;
            } else {
                return $this->sendError(trans('custom.materiel_issue_not_found'), 500);
            }
        }

        if ($input['unitCostLocal'] == 0 || $input['unitCostRpt'] == 0) {
            $input['qtyIssued'] = 0;
            $input['qtyIssuedDefaultMeasure'] = 0;
            $this->itemReturnDetailsRepository->update($input, $id);
            return $this->sendError(trans('custom.cost_is_zero_you_cannot_issue'), 500);
        }

        if ($input['unitCostLocal'] < 0 || $input['unitCostRpt'] < 0) {
            $input['qtyIssued'] = 0;
            $input['qtyIssuedDefaultMeasure'] = 0;
            $this->itemReturnDetailsRepository->update($input, $id);
            return $this->sendError(trans('custom.cost_is_negative_you_cannot_issue'), 500);
        }

        if ($input['qtyIssuedDefaultMeasure'] > $input['qtyFromIssue']) {
            $input['qtyIssued'] = 0;
            $input['qtyIssuedDefaultMeasure'] = 0;
            $this->itemReturnDetailsRepository->update($input, $id);
            return $this->sendError(trans('custom.you_cannot_return_more_than_issued_qty'), 500, $qtyError);
        }

        if ($input['qtyIssued'] == '' || is_null($input['qtyIssued'])) {
            $input['qtyIssued'] = 0;
            $input['qtyIssuedDefaultMeasure'] = 0;
        }
       
        $itemReturnDetails = $this->itemReturnDetailsRepository->update($input, $id);

        return $this->sendResponse($itemReturnDetails->toArray(), trans('custom.itemreturndetails_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/itemReturnDetails/{id}",
     *      summary="Remove the specified ItemReturnDetails from storage",
     *      tags={"ItemReturnDetails"},
     *      description="Delete ItemReturnDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ItemReturnDetails",
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
        /** @var ItemReturnDetails $itemReturnDetails */
        $itemReturnDetails = $this->itemReturnDetailsRepository->findWithoutFail($id);

        if (empty($itemReturnDetails)) {
            return $this->sendError(trans('custom.item_return_details_not_found'));
        }

        $itemReturn = ItemReturnMaster::where('itemReturnAutoID', $itemReturnDetails->itemReturnAutoID)->first();

        if (empty($itemReturn)) {
            return $this->sendError(trans('custom.materiel_return_not_found'));
        }

        if ($itemReturnDetails->trackingType == 2) {
            $validateSubProductSold = DocumentSubProduct::where('documentSystemID', $itemReturn->documentSystemID)
                                                         ->where('documentDetailID', $id)
                                                         ->where('sold', 1)
                                                         ->first();

            if ($validateSubProductSold) {
                return $this->sendError(trans('custom.you_cannot_delete_this_line_item_serial_details_ar'), 422);
            }

            $subProduct = DocumentSubProduct::where('documentSystemID', $itemReturn->documentSystemID)
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
        } else if ($itemReturnDetails->trackingType == 1) {
            $deleteBatch = ItemTracking::revertBatchTrackingReturnStatus($itemReturn->documentSystemID, $id);

            if (!$deleteBatch['status']) {
                return $this->sendError($deleteBatch['message'], 422);
            }
        }

        $itemReturnDetails->delete();

        return $this->sendResponse($id, trans('custom.item_return_details_deleted_successfully'));
    }

    /**
     * Display a listing of the items by Materiel Return.
     * GET|HEAD /getItemsByMaterielReturn
     *
     * @param Request $request
     * @return Response
     */
    public function getItemsByMaterielReturn(Request $request)
    {
        $input = $request->all();
        $rId = $input['itemReturnAutoID'];

        $itemReturnMaster = ItemReturnMaster::find($rId);

        if (empty($itemReturnMaster)) {
            return $this->sendError(trans('custom.item_return_not_found'));
        }

        $items = ItemReturnDetails::where('itemReturnAutoID', $rId)
            ->with(['uom_issued', 'uom_receiving', 'issue','item_by'])
            ->get();

        foreach ($items as $item) {

            $issueUnit = Unit::where('UnitID', $item['itemUnitOfMeasure'])->with(['unitConversion.sub_unit'])->first();

            $issueUnits = array();
            foreach ($issueUnit->unitConversion as $unit) {
                $temArray = array('value' => $unit->sub_unit->UnitID, 'label' => $unit->sub_unit->UnitShortCode);
                array_push($issueUnits, $temArray);
            }

            $item->issueUnits = $issueUnits;

            if ($item['itemCodeSystem']) {
                $itemIssues = ItemIssueMaster::whereHas('details', function ($q) use ($item) {
                       $q->where('itemCodeSystem', $item['itemCodeSystem']);
                     })
                    ->where('companySystemID', $itemReturnMaster->companySystemID)
                    ->where('serviceLineSystemID', $itemReturnMaster->serviceLineSystemID)
                    ->where('wareHouseFrom', $itemReturnMaster->wareHouseLocation)
                    ->where('approved', -1)
                    ->select('itemIssueAutoID AS value', 'itemIssueCode AS label')
                    ->get();

                $item->issues = $itemIssues;
            } else {
                $item->issues = [];
            }
        }

        return $this->sendResponse($items->toArray(), trans('custom.material_return_details_retrieved_successfully'));
    }

    /**
     * get Items Options Materiel Return
     * GET|HEAD /getItemsOptionsMaterielReturn
     *
     * @param Request $request
     * @return Response
     */
    public function getItemsOptionsMaterielReturn(Request $request)
    {
        $input = $request->all();
        $companyId = $input['companyId'];

        $items = ItemAssigned::where('companySystemID', $companyId)
            ->where('financeCategoryMaster', 1)
            ->where('isActive', 1)
            ->where('isAssigned', -1)
            ->select(['itemPrimaryCode', 'itemDescription', 'itemCodeSystem', 'secondaryItemCode']);

        if (array_key_exists('search', $input)) {
            $search = $input['search'];
            $items = $items->where(function ($query) use ($search) {
                $query->where('itemPrimaryCode', 'LIKE', "%{$search}%")
                    ->orWhere('itemDescription', 'LIKE', "%{$search}%")
                    ->orWhere('secondaryItemCode', 'LIKE', "%{$search}%");
            });
        }

        $items = $items->take(20)->get();
        return $this->sendResponse($items->toArray(), trans('custom.data_retrieved_successfully'));

    }
}
