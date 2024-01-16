<?php
/**
 * =============================================
 * -- File Name : MaterielRequestDetailsAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Materiel Request Details
 * -- Author : Mohamed Fayas
 * -- Create date : 12 - June 2018
 * -- Description : This file contains the all CRUD for Materiel Request Details
 * -- REVISION HISTORY
 * -- Date: 14-June 2018 By: Fayas Description: Added new functions named as getItemsByMaterielRequest()
 * -- Date: 19-June 2018 By: Fayas Description: Added new functions named as getItemsOptionForMaterielRequest()
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateMaterielRequestDetailsAPIRequest;
use App\Http\Requests\API\UpdateMaterielRequestDetailsAPIRequest;
use App\Models\ErpItemLedger;
use App\Models\FinanceItemcategorySubAssigned;
use App\Models\GRVDetails;
use App\Models\ItemAssigned;
use App\Models\MaterielRequest;
use App\Models\MaterielRequestDetails;
use App\Models\CompanyPolicyMaster;
use App\Models\PurchaseOrderDetails;
use App\Models\Unit;
use App\Models\UnitConversion;
use App\Repositories\MaterielRequestDetailsRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Jobs\AddBulkItem\MaterialRequestAddBulkItemJob;
use App\Repositories\UserRepository;
use App\Services\MaterialRequestService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class MaterielRequestDetailsController
 * @package App\Http\Controllers\API
 */

class MaterielRequestDetailsAPIController extends AppBaseController
{
    /** @var  MaterielRequestDetailsRepository */
    private $materielRequestDetailsRepository;
    private $userRepository;

    public function __construct(MaterielRequestDetailsRepository $materielRequestDetailsRepo,UserRepository $userRepo)
    {
        $this->userRepository = $userRepo;
        $this->materielRequestDetailsRepository = $materielRequestDetailsRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/materielRequestDetails",
     *      summary="Get a listing of the MaterielRequestDetails.",
     *      tags={"MaterielRequestDetails"},
     *      description="Get all MaterielRequestDetails",
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
     *                  @SWG\Items(ref="#/definitions/MaterielRequestDetails")
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
        $this->materielRequestDetailsRepository->pushCriteria(new RequestCriteria($request));
        $this->materielRequestDetailsRepository->pushCriteria(new LimitOffsetCriteria($request));
        $materielRequestDetails = $this->materielRequestDetailsRepository->all();

        return $this->sendResponse($materielRequestDetails->toArray(), 'Materiel Request Details retrieved successfully');
    }

    /**
     * @param CreateMaterielRequestDetailsAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/materielRequestDetails",
     *      summary="Store a newly created MaterielRequestDetails in storage",
     *      tags={"MaterielRequestDetails"},
     *      description="Store MaterielRequestDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="MaterielRequestDetails that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/MaterielRequestDetails")
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
     *                  ref="#/definitions/MaterielRequestDetails"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateMaterielRequestDetailsAPIRequest $request)
    {


        $input = array_except($request->all(), 'uom_default');
        $input = $this->convertArrayToValue($input);


        $companySystemID = $input['companySystemID'];

        $allowItemToTypePolicy = false;
        $itemNotound = false;
        $allowItemToType = CompanyPolicyMaster::where('companyPolicyCategoryID', 54)
                                            ->where('companySystemID', $companySystemID)
                                            ->first();

        if ($allowItemToType) {
            $allowItemToTypePolicy = true;
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

        $materielRequest = MaterielRequest::where('RequestID', $input['RequestID'])->first();


        if (empty($materielRequest)) {
            return $this->sendError('Materiel Request Details not found');
        }

        if($materielRequest->ClosedYN == -1){
            return $this->sendError('This Materiel Request already closed. You can not add.',500);
        }

        if($materielRequest->approved == -1){
            return $this->sendError('This Materiel Request fully approved. You can not add.',500);
        }

        $input['qtyIssuedDefaultMeasure'] = 0;
        if (!$itemNotound) {
            $input['itemCode'] = $item->itemCodeSystem;
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
                return $this->sendError('Finance Category not found');
            }

            if ($item->financeCategoryMaster == 1) {

                $alreadyAdded = MaterielRequest::where('RequestID', $input['RequestID'])
                    ->whereHas('details', function ($query) use ($item) {
                        $query->where('itemCode', $item->itemCodeSystem);
                    })
                    ->first();

                if ($alreadyAdded) {
                    return $this->sendError("Selected item is already added. Please check again", 500);
                }
            }

            $input['financeGLcodebBS']  = $financeItemCategorySubAssigned->financeGLcodebBS;
            $input['financeGLcodePL']   = $financeItemCategorySubAssigned->financeGLcodePL;
            $input['includePLForGRVYN'] = $financeItemCategorySubAssigned->includePLForGRVYN;


             $poQty = PurchaseOrderDetails::whereHas('order' , function ($query) use ($companySystemID,$materielRequest) {
                                                $query->where('companySystemID', $companySystemID)
                                                    ->where('poLocation', $materielRequest->location)
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

            $grvQty = GRVDetails::whereHas('grv_master' , function ($query) use ($companySystemID,$materielRequest) {
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

        } else {
            $input['itemDescription'] = $input['itemCode'];
            $input['itemCode'] = null;
            $input['partNumber'] = null;
            $input['itemFinanceCategoryID'] = null;
            $input['itemFinanceCategorySubID'] = null;
            $input['unitOfMeasure'] = null;
            $input['unitOfMeasureIssued'] = null;
            $input['maxQty'] = 0;
            $input['minQty'] = 0;
            $input['quantityOnOrder'] = 0;
            $input['quantityInHand'] = 0;

        }

        $input['estimatedCost'] = 0;
        $input['quantityRequested'] = 0;
        
        $input['ClosedYN'] = 0;
        $input['selectedForIssue'] = 0;
        $input['comments'] = null;
        $input['convertionMeasureVal'] = 1;

        $input['allowCreatePR']      = 0;
        $input['selectedToCreatePR'] = 0;


        $materielRequestDetails = $this->materielRequestDetailsRepository->create($input);

        return $this->sendResponse($materielRequestDetails->toArray(), 'Materiel Request Details saved successfully');
    }

    public function requestDetailsAddAllItems(Request $request)
    {
        $input = $request->all();
        $id = Auth::id();
        $user = $this->userRepository->with(['employee'])->findWithoutFail($id);

        $input['employeeSystemID'] = $user ? $user->employee['employeeSystemID'] : null;
        $input['empID'] = $user ? $user->employee['empID'] : null;

        if (isset($input['addAllItems']) && $input['addAllItems']) {
            $db = isset($input['db']) ? $input['db'] : "";    

            $materielRequest = MaterielRequest::where('RequestID', $input['RequestID'])->first();


            if (empty($materielRequest)) {
                return $this->sendError('Materiel Request Details not found');
            }
    
            if($materielRequest->ClosedYN == -1){
                return $this->sendError('This Materiel Request already closed. You can not add.',500);
            }
    
            if($materielRequest->approved == -1){
                return $this->sendError('This Materiel Request fully approved. You can not add.',500);
            }

            $companySystemID = $input['companySystemID'];

            $allowItemToTypePolicy = false;
            $itemNotound = false;
            $allowItemToType = CompanyPolicyMaster::where('companyPolicyCategoryID', 54)
                                                ->where('companySystemID', $companySystemID)
                                                ->first();
    
            if ($allowItemToType) {
                $allowItemToTypePolicy = true;
            }

            $data['isBulkItemJobRun'] = 1;

            $materielRequest = MaterielRequest::where('RequestID', $input['RequestID'])->update($data);
            MaterialRequestAddBulkItemJob::dispatch($db, $input);

            return $this->sendResponse('', 'Items Added to Queue Please wait some minutes to process');
        } else {
            DB::beginTransaction();
            try {
                $invalidItems = [];
                foreach ($input['itemArray'] as $key => $value) {
                    $res = MaterialRequestService::validateMaterialRequestItem($value['itemCodeSystem'], $input['companySystemID'], $input['RequestID']);
                    
                    if ($res['status']) {
                        MaterialRequestService::saveMaterialRequestItem($value['itemCodeSystem'], $input['companySystemID'], $input['RequestID'], $input['empID'], $input['employeeSystemID']);
                    } else {
                        $invalidItems[] = ['itemCodeSystem' => $value['itemCodeSystem'], 'message' => $res['message']];
                    }
                }
                DB::commit();
                return $this->sendResponse('', 'Material Request Items saved successfully');
            } catch (\Exception $exception) {
                DB::rollBack();
                return $this->sendError($exception->getMessage(), 500);
            }
        }
    }


    public function materialRequestValidateItem(Request $request)
    {
        $input = $request->all();

        return MaterialRequestService::validateMaterialRequestItem($input['itemCodeSystem'], $input['companySystemID'], $input['RequestID']);
    }
    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/materielRequestDetails/{id}",
     *      summary="Display the specified MaterielRequestDetails",
     *      tags={"MaterielRequestDetails"},
     *      description="Get MaterielRequestDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of MaterielRequestDetails",
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
     *                  ref="#/definitions/MaterielRequestDetails"
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
        /** @var MaterielRequestDetails $materielRequestDetails */
        $materielRequestDetails = $this->materielRequestDetailsRepository->findWithoutFail($id);

        if (empty($materielRequestDetails)) {
            return $this->sendError('Materiel Request Details not found');
        }

        return $this->sendResponse($materielRequestDetails->toArray(), 'Materiel Request Details retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateMaterielRequestDetailsAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/materielRequestDetails/{id}",
     *      summary="Update the specified MaterielRequestDetails in storage",
     *      tags={"MaterielRequestDetails"},
     *      description="Update MaterielRequestDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of MaterielRequestDetails",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="MaterielRequestDetails that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/MaterielRequestDetails")
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
     *                  ref="#/definitions/MaterielRequestDetails"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateMaterielRequestDetailsAPIRequest $request)
    {
        $input = array_except($request->all(), ['uom_default', 'uom_issuing', 'item_by']);
        $input = $this->convertArrayToValue($input);


        /** @var MaterielRequestDetails $materielRequestDetails */
        $materielRequestDetails = $this->materielRequestDetailsRepository->findWithoutFail($id);
        if (empty($materielRequestDetails)) {
            return $this->sendError('Materiel Request Details not found');
        }

        $materielRequest = MaterielRequest::where('RequestID', $input['RequestID'])->first();
        if ($materielRequest->approved == -1) {
            return $this->sendError('This Materiel Request fully approved. You can not edit.', 500);
        }

        if ($input['itemCode'] != null) {
            if ($input['unitOfMeasure'] != $input['unitOfMeasureIssued']) {
                $unitConvention = UnitConversion::where('masterUnitID', $input['unitOfMeasure'])
                    ->where('subUnitID', $input['unitOfMeasureIssued'])
                    ->first();

                if (empty($unitConvention)) {
                    return $this->sendError("Unit conversion isn't valid or configured", 500);
                }

                if ($unitConvention) {
                    $convention = $unitConvention->conversion;
                    $input['convertionMeasureVal'] = $convention;
                    if ($convention > 0) {
                        $input['qtyIssuedDefaultMeasure'] = $input['quantityRequested'] / $convention;
                    } else {
                        $input['qtyIssuedDefaultMeasure'] = $input['quantityRequested'] * $convention;
                    }
                }
            } else {
                $input['qtyIssuedDefaultMeasure'] = $input['quantityRequested'];
            }



            if((($input['quantityInHand'] - $input['quantityRequested']) + $input['quantityOnOrder']) <= $input['minQty']){
                $input['allowCreatePR'] =  -1;
            }else{
                $input['allowCreatePR']   =  0;
            }
        }

        $materielRequestDetails = $this->materielRequestDetailsRepository->update($input, $id);

        return $this->sendResponse($materielRequestDetails->toArray(), 'MaterielRequestDetails updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/materielRequestDetails/{id}",
     *      summary="Remove the specified MaterielRequestDetails from storage",
     *      tags={"MaterielRequestDetails"},
     *      description="Delete MaterielRequestDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of MaterielRequestDetails",
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
        /** @var MaterielRequestDetails $materielRequestDetails */
        $materielRequestDetails = $this->materielRequestDetailsRepository->findWithoutFail($id);

        if (empty($materielRequestDetails)) {
            return $this->sendError('Materiel Request Details not found');
        }

        $materielRequestDetails->delete();

        return $this->sendResponse($id, 'Materiel Request Details deleted successfully');
    }

    /**
     * Display a listing of the items by Request.
     * GET|HEAD /getItemsByMaterielRequest
     *
     * @param Request $request
     * @return Response
     */

    public function removeAllItems($id)
    {
        $material_request = MaterielRequest::find($id);
        if($material_request){

           MaterielRequestDetails::where('RequestID', $id)->delete();
           MaterielRequest::where('RequestID', $id)->update(['counter' => 0]);

            return $this->sendResponse([], 'Items Deleted Successfully');

        } else {
            return $this->sendError('Material Request not found');

        }

    }

        public function getItemsByMaterielRequest(Request $request)
    {
        $input = $request->all();
        $rId = $input['RequestID'];

        $items = MaterielRequestDetails::where('RequestID', $rId)
                                        ->with(['uom_default','uom_issuing','item_by'])
                                        ->get();

        foreach ($items as $item){

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

        return $this->sendResponse($items->toArray(), 'Request Details retrieved successfully');
    }

    public function getItemsByMaterielRequestByLimit(Request $request)
    {
        $input = $request->all();
        $rId = $input['RequestID'];

        $items = MaterielRequestDetails::where('RequestID', $rId)
            ->with(['uom_default','uom_issuing','item_by'])
            ->skip($input['skip'])->take($input['limit'])->get();

        $index = $input['skip'] + 1;

        foreach ($items as $item){
            $item['index'] = $index;
            $index++;
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

        return $this->sendResponse($items->toArray(), 'Request Details retrieved successfully');
    }

    /**
     * get Items Option For Materiel Request
     * get /getItemsOptionForMaterielRequest
     *
     * @param Request $request
     *
     * @return Response
     */

    public function getItemsOptionForMaterielRequest(Request $request)
    {
        $input = $request->all();

        $companyId = $input['companyId'];

        $location =  $input['location'];

        $items = ItemAssigned::where('companySystemID', $companyId)
                               ->where('financeCategoryMaster',1)->where('isActive', 1)->where('isAssigned', -1);

        if (array_key_exists('search', $input)) {

            $search = $input['search'];

            $items = $items->where(function ($query) use ($search) {
                $query->where('itemPrimaryCode', 'LIKE', "%{$search}%")
                    ->orWhere('itemDescription', 'LIKE', "%{$search}%")
                    ->orWhere('secondaryItemCode', 'LIKE', "%{$search}%");
            });
        }

        $items = $items->take(20)->get();

        foreach($items as $item) {
            $data = array('companySystemID' => $companyId,
            'itemCodeSystem' => $item->itemCodeSystem,
            'wareHouseId' => $location);
            $itemCurrentCostAndQty = \Inventory::itemCurrentCostAndQty($data);
            $item['currentWareHouseStockQty'] = $itemCurrentCostAndQty['currentWareHouseStockQty'];
        }

        return $this->sendResponse($items->toArray(), 'Data retrieved successfully');
    }


    public function getItemWarehouseQnty(Request $request) {
        $input = $request->all();
        $companyId = $input['companyId'];
        $location =  $input['location'];

        $data = array('companySystemID' => $companyId,
            'itemCodeSystem' => $input['itemCode'],
            'wareHouseId' => $location);
        $itemCurrentCostAndQty = \Inventory::itemCurrentCostAndQty($data);
       
        return $this->sendResponse($itemCurrentCostAndQty, 'Data retrieved successfully');

    }
}
