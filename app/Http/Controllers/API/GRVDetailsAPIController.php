<?php
/**
 * =============================================
 * -- File Name : GRVDetailsAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  GRV Details
 * -- Author : Mohamed Fayas
 * -- Create date : 04- May 2018
 * -- Description : This file contains the all CRUD for GRV Details
 * -- REVISION HISTORY
 * -- Date: 13-June 2018 By: Nazir Description: Added new functions named as getItemsByGRVMaster(),
 * -- Date: 18-June 2018 By: Nazir Description: Added new functions named as storeGRVDetailsFromPO(),
 * -- Date: 19-June 2018 By: Nazir Description: Added new functions named as grvDeleteAllDetails(),
 */

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateGRVDetailsAPIRequest;
use App\Http\Requests\API\UpdateGRVDetailsAPIRequest;
use App\Models\FinanceItemCategorySub;
use App\Models\GRVDetails;
use App\Models\GRVMaster;
use App\Models\ItemAssigned;
use App\Models\ProcumentOrder;
use App\Models\CompanyPolicyMaster;
use App\Models\ProcumentOrderDetail;
use App\Models\PurchaseOrderDetails;
use App\Models\SegmentMaster;
use App\Models\WarehouseItems;
use App\Models\WarehouseMaster;
use App\Repositories\GRVDetailsRepository;
use App\Repositories\GRVMasterRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Response;

/**
 * Class GRVDetailsController
 * @package App\Http\Controllers\API
 */
class GRVDetailsAPIController extends AppBaseController
{
    /** @var  GRVDetailsRepository */
    private $gRVDetailsRepository;
    private $gRVMasterRepository;
    private $userRepository;

    public function __construct(GRVDetailsRepository $gRVDetailsRepo, UserRepository $userRepo, GRVMasterRepository $gRVMasterRepository)
    {
        $this->gRVDetailsRepository = $gRVDetailsRepo;
        $this->gRVMasterRepository = $gRVMasterRepository;
        $this->userRepository = $userRepo;
    }

    /**
     * Display a listing of the GRVDetails.
     * GET|HEAD /gRVDetails
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->gRVDetailsRepository->pushCriteria(new RequestCriteria($request));
        $this->gRVDetailsRepository->pushCriteria(new LimitOffsetCriteria($request));
        $gRVDetails = $this->gRVDetailsRepository->all();

        return $this->sendResponse($gRVDetails->toArray(), 'GRV details retrieved successfully');
    }

    /**
     * Store a newly created GRVDetails in storage.
     * POST /gRVDetails
     *
     * @param CreateGRVDetailsAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateGRVDetailsAPIRequest $request)
    {
        $input = $request->all();

        $gRVDetails = $this->gRVDetailsRepository->create($input);

        return $this->sendResponse($gRVDetails->toArray(), 'GRV details saved successfully');
    }

    /**
     * Display the specified GRVDetails.
     * GET|HEAD /gRVDetails/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var GRVDetails $gRVDetails */
        $gRVDetails = $this->gRVDetailsRepository->findWithoutFail($id);

        if (empty($gRVDetails)) {
            return $this->sendError('GRV Details not found');
        }

        return $this->sendResponse($gRVDetails->toArray(), 'GRV details retrieved successfully');
    }

    /**
     * Update the specified GRVDetails in storage.
     * PUT/PATCH /gRVDetails/{id}
     *
     * @param  int $id
     * @param UpdateGRVDetailsAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateGRVDetailsAPIRequest $request)
    {
        DB::beginTransaction();
        try {
            $input = $request->all();
            $input = array_except($input, ['unit', 'po_master']);
            $input = $this->convertArrayToValue($input);

            //$empInfo = self::getEmployeeInfo();
            $userId = Auth::id();
            $user = $this->userRepository->with(['employee'])->findWithoutFail($userId);

            /** @var GRVDetails $gRVDetails */
            $gRVDetails = $this->gRVDetailsRepository->findWithoutFail($id);

            if (empty($gRVDetails)) {
                return $this->sendError('GRV details not found');
            }

            $grvMaster = GRVMaster::find($input['grvAutoID']);

            if (empty($grvMaster)) {
                return $this->sendError('GRV not found');
            }

            if (is_null($input['noQty'])) {
                $input['noQty'] = 0;
            }

            if(!isset($input['binNumber'])) {
                $input['binNumber'] = 0;
            }

            $gRVDetails = $this->gRVDetailsRepository->update($input, $id);

            $input['modifiedPc'] = gethostname();
            $input['modifiedUser'] = $user->employee['empID'];

            $allowPartialGRVPolicy = CompanyPolicyMaster::where('companyPolicyCategoryID', 23)
                ->where('companySystemID', $grvMaster->companySystemID)
                ->first();

            $POMaster = ProcumentOrder::find($input['purchaseOrderMastertID']);

            if (!empty($input['purchaseOrderDetailsID']) && !empty($input['purchaseOrderMastertID'])) {
                $detailExistPODetail = PurchaseOrderDetails::find($input['purchaseOrderDetailsID']);
                if ($allowPartialGRVPolicy->isYesNO == 0 && $POMaster->partiallyGRVAllowed == 0) {
                    if (($input['poQty'] - $input['prvRecievedQty']) != $input['noQty']) {
                        return $this->sendError('GRV qty should be equal to PO qty', 422);
                    }
                }

                if ($input['noQty'] > ($input['poQty'] - $input['prvRecievedQty'])) {
                    return $this->sendError('Number of quantity should not be greater than received qty', 422);
                }

                $detailPOSUM = GRVDetails::WHERE('purchaseOrderMastertID', $input['purchaseOrderMastertID'])->WHERE('companySystemID', $grvMaster->companySystemID)->WHERE('purchaseOrderDetailsID', $input['purchaseOrderDetailsID'])->sum('noQty');
                $masterPOSUM = GRVDetails::WHERE('purchaseOrderMastertID', $input['purchaseOrderMastertID'])->WHERE('companySystemID', $grvMaster->companySystemID)->sum('noQty');

                $receivedQty = 0;
                $goodsRecievedYN = 0;
                $GRVSelectedYN = 0;
                if ($detailPOSUM > 0) {
                    $receivedQty = $detailPOSUM;
                }

                $checkQuantity = $detailExistPODetail->noQty - $receivedQty;
                if ($receivedQty == 0) {
                    $goodsRecievedYN = 0;
                    $GRVSelectedYN = 0;
                } else {
                    if ($checkQuantity == 0) {
                        $goodsRecievedYN = 2;
                        $GRVSelectedYN = 1;
                    } else {
                        $goodsRecievedYN = 1;
                        $GRVSelectedYN = 0;
                    }
                }

                $updateDetail = PurchaseOrderDetails::where('purchaseOrderDetailsID', $detailExistPODetail->purchaseOrderDetailsID)
                    ->update(['GRVSelectedYN' => $GRVSelectedYN, 'goodsRecievedYN' => $goodsRecievedYN, 'receivedQty' => $receivedQty]);

                $balanceQty = PurchaseOrderDetails::selectRaw('SUM(noQty) as noQty,SUM(receivedQty) as receivedQty,SUM(noQty) - SUM(receivedQty) as balanceQty')->WHERE('purchaseOrderMasterID', $input['purchaseOrderMastertID'])->WHERE('companySystemID', $grvMaster->companySystemID)->first();


                if ($balanceQty["balanceQty"] == 0) {
                    $updatePO = ProcumentOrder::find($gRVDetails->purchaseOrderMastertID)
                        ->update(['poClosedYN' => 1, 'grvRecieved' => 2]);
                } else {
                    if ($masterPOSUM > 0) {
                        $updatePO = ProcumentOrder::find($gRVDetails->purchaseOrderMastertID)
                            ->update(['poClosedYN' => 0, 'grvRecieved' => 1]);
                    } else {
                        $updatePO = ProcumentOrder::find($gRVDetails->purchaseOrderMastertID)
                            ->update(['poClosedYN' => 0, 'grvRecieved' => 0]);
                    }
                }

            }
            DB::commit();
            return $this->sendResponse($gRVDetails->toArray(), 'GRV details updated successfully');
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError('Error Occurred', 422);
        }
    }

    /**
     * Remove the specified GRVDetails from storage.
     * DELETE /gRVDetails/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var GRVDetails $gRVDetails */
        $gRVDetails = $this->gRVDetailsRepository->findWithoutFail($id);
        $purchaseOrderDetailsID = $gRVDetails->purchaseOrderDetailsID;
        $purchaseOrderMastertID = $gRVDetails->purchaseOrderMastertID;

        if (empty($gRVDetails)) {
            return $this->sendError('GRV Details not found');
        }
        $grvMaster = GRVMaster::find($gRVDetails->grvAutoID);

        if($grvMaster->grvTypeID == 2) {
            $allowPartialGRVPolicy = CompanyPolicyMaster::where('companyPolicyCategoryID', 23)
                ->where('companySystemID', $grvMaster->companySystemID)
                ->first();
            $POMaster = ProcumentOrder::find($purchaseOrderMastertID);

            if ($allowPartialGRVPolicy->isYesNO == 0 && $POMaster->partiallyGRVAllowed == 0) {
                return $this->sendError('You cannot delete one line item as partial GRV is disabled.', 422);
            }
        }

        // delete the grv detail
        $gRVDetails->delete();


        // updating master and detail table number of qty
        if (!empty($purchaseOrderDetailsID) && !empty($purchaseOrderMastertID)) {
            $detailExistPODetail = PurchaseOrderDetails::find($purchaseOrderDetailsID);
            // get the total received qty for a specific item
            $detailPOSUM = GRVDetails::WHERE('purchaseOrderMastertID', $purchaseOrderMastertID)->WHERE('companySystemID', $grvMaster->companySystemID)->WHERE('purchaseOrderDetailsID', $purchaseOrderDetailsID)->sum('noQty');
            // get the total received qty
            $masterPOSUM = GRVDetails::WHERE('purchaseOrderMastertID', $purchaseOrderMastertID)->WHERE('companySystemID', $grvMaster->companySystemID)->sum('noQty');

            $receivedQty = 0;
            $goodsRecievedYN = 0;
            $GRVSelectedYN = 0;
            if ($detailPOSUM > 0) {
                $receivedQty = $detailPOSUM;
            }

            $checkQuantity = $detailExistPODetail->noQty - $receivedQty;
            if ($receivedQty == 0) {
                $goodsRecievedYN = 0;
                $GRVSelectedYN = 0;
            } else {
                if ($checkQuantity == 0) {
                    $goodsRecievedYN = 2;
                    $GRVSelectedYN = 1;
                } else {
                    $goodsRecievedYN = 1;
                    $GRVSelectedYN = 0;
                }
            }

            $updateDetail = PurchaseOrderDetails::where('purchaseOrderDetailsID', $detailExistPODetail->purchaseOrderDetailsID)
                ->update(['GRVSelectedYN' => $GRVSelectedYN, 'goodsRecievedYN' => $goodsRecievedYN, 'receivedQty' => $receivedQty]);

            if ($masterPOSUM > 0) {
                $updatePO = ProcumentOrder::find($gRVDetails->purchaseOrderMastertID)
                    ->update(['poClosedYN' => 0, 'grvRecieved' => 1]);
            } else {
                $updatePO = ProcumentOrder::find($gRVDetails->purchaseOrderMastertID)
                    ->update(['poClosedYN' => 0, 'grvRecieved' => 0]);
            }
        }

        return $this->sendResponse($id, 'GRV details deleted successfully');
    }

    public function getItemsByGRVMaster(Request $request)
    {
        $input = $request->all();
        $grvAutoID = $input['grvAutoID'];

        $items = GRVDetails::where('grvAutoID', $grvAutoID)
            ->with(['unit' => function ($query) {
            }, 'po_master' => function ($query) {
            }])
            ->get();

        return $this->sendResponse($items->toArray(), 'GRV details retrieved successfully');
    }

    public function storeGRVDetailsFromPO(Request $request)
    {
        $input = $request->all();
        $GRVDetail_arr = array();

        $grvAutoID = $input['grvAutoID'];

        $id = Auth::id();
        $user = $this->userRepository->with(['employee'])->findWithoutFail($id);

        $GRVMaster = GRVMaster::where('grvAutoID', $grvAutoID)
            ->first();

        if(empty($GRVMaster)){
            $this->sendError('GRV not found',500);
        }

        $allowMultiplePO = CompanyPolicyMaster::where('companyPolicyCategoryID', 10)
            ->where('companySystemID', $GRVMaster->companySystemID)
            ->first();

        $allowPartialGRVPolicy = CompanyPolicyMaster::where('companyPolicyCategoryID', 23)
            ->where('companySystemID', $GRVMaster->companySystemID)
            ->first();

        $POMaster = ProcumentOrder::find($input['purchaseOrderMastertID']);

        $size = array_column($input['detailTable'], 'isChecked');
        $frontDetailcount = count(array_filter($size));

        if ($allowPartialGRVPolicy->isYesNO == 0 && $POMaster->partiallyGRVAllowed == 0) {
            $poDetailTotal = PurchaseOrderDetails::where('purchaseOrderMasterID', $input['purchaseOrderMastertID'])
                                                    ->where('goodsRecievedYN', '<>', 2)
                                                    ->count();
            if ($poDetailTotal != $frontDetailcount) {
                return $this->sendError('All PO detail items should be pulled for this grv', 422);
            }
        }

        DB::beginTransaction();
        try {

            $warehouseBinLocationPolicy = CompanyPolicyMaster::where('companyPolicyCategoryID', 40)
                                                                ->where('companySystemID', $GRVMaster->companySystemID)
                                                                ->where('isYesNO', 1)
                                                                ->exists();

            foreach ($input['detailTable'] as $new) {
                if ($new['isChecked']) {

                    //check whether the item is already added to another GRV
                    $alreadyItemPulledCheck = GRVDetails::with('grv_master')->select('*')
                        ->where('grvAutoID','<>', $grvAutoID)
                        ->where('purchaseOrderDetailsID', $new['purchaseOrderDetailsID'])
                        ->whereHas('grv_master',function ($q) use ($input) {
                            $q->where('approved', 0);
                        })
                        ->first();

                    if($alreadyItemPulledCheck){
                        return $this->sendError('Cannot proceed. Selected item is already added in '.$alreadyItemPulledCheck->grv_master->grvPrimaryCode.' and it is not approved yet', 422);
                    }


                    $grvDetailItem = ProcumentOrderDetail::select('receivedQty')
                        ->where('purchaseOrderDetailsID', $new['purchaseOrderDetailsID'])
                        ->first();

                    $new['receivedQty'] = $grvDetailItem['receivedQty'];

                    if (($new['noQty'] == '' || $new['noQty'] == 0)) {
                        return $this->sendError('Qty cannot be zero', 422);
                    } else {
                        if ($allowPartialGRVPolicy->isYesNO == 0 && $POMaster->partiallyGRVAllowed == 0) {
                            // pre check for all items qty pulled
                            if ($new['isChecked'] && ((float)$new['noQty'] != ($new['poQty'] - (float)$new['receivedQty']))) {
                                return $this->sendError('Full order quantity should be received', 422);
                            }
                        }
                    }


                    if ($new['noQty'] > ($new['poQty'] - $new['receivedQty'])) {
                        return $this->sendError('Number of quantity should not be greater than received qty', 422);
                    }

                    if ($allowMultiplePO->isYesNO == 0) {
                        $grvDetailExistSameItem = GRVDetails::select(DB::raw('purchaseOrderMastertID'))
                            ->where('grvAutoID', $grvAutoID)
                            ->first();

                        if (!empty($grvDetailExistSameItem)) {
                            if ($grvDetailExistSameItem['purchaseOrderMastertID'] != $new['purchaseOrderMasterID']) {
                                return $this->sendError('You cannot add details from multiple PO', 422);
                            }
                        }
                    }

                    //checking if item category is same or not

                    $grvDetailExistSameItem = GRVDetails::select(DB::raw('DISTINCT(itemFinanceCategoryID) as itemFinanceCategoryID'))
                        ->where('grvAutoID', $grvAutoID)
                        ->first();

                    if ($grvDetailExistSameItem) {
                        if ($new['itemFinanceCategoryID'] != $grvDetailExistSameItem["itemFinanceCategoryID"]) {
                            return $this->sendError('You cannot add different category item', 422);
                        }
                    }

                    //checking if item is inventory item cannot be added more than one

                    $grvDetailExistSameItem = GRVDetails::select(DB::raw('itemCode'))
                        ->where('grvAutoID', $grvAutoID)
                        ->where('purchaseOrderDetailsID', $new['purchaseOrderDetailsID'])
                        ->first();

                    if ($grvDetailExistSameItem) {
                        return $this->sendError('Selected item is already added from the same order.', 422);
                    }

                    $totalAddedQty = $new['noQty'] + $new['receivedQty'];

                    if ($new['poQty'] == $totalAddedQty) {
                        $goodsRecievedYN = 2;
                        $GRVSelectedYN = 1;
                    } else {
                        $goodsRecievedYN = 1;
                        $GRVSelectedYN = 0;
                    }
                    $warehouseItem = array();
                    if($warehouseBinLocationPolicy && $new['itemFinanceCategoryID']){
                        $warehouseItemTemp = WarehouseItems::where('warehouseSystemCode',$GRVMaster->grvLocation)
                                                             ->where('companySystemID' , $GRVMaster->companySystemID)
                                                             ->where('itemSystemCode',$new['itemCode'])
                                                             ->first();
                        if(!empty($warehouseItemTemp)){
                            $warehouseItem = $warehouseItemTemp;
                        }
                    }


                    // checking the qty request is matching with sum total
                    if ($new['poQty'] >= $new['noQty']) {
                        $GRVDetail_arr['grvAutoID'] = $grvAutoID;
                        $GRVDetail_arr['companySystemID'] = $new['companySystemID'];
                        $GRVDetail_arr['companyID'] = $new['companyID'];
                        $GRVDetail_arr['serviceLineCode'] = $new['serviceLineCode'];
                        $GRVDetail_arr['purchaseOrderMastertID'] = $new['purchaseOrderMasterID'];
                        $GRVDetail_arr['purchaseOrderDetailsID'] = $new['purchaseOrderDetailsID'];
                        $GRVDetail_arr['itemCode'] = $new['itemCode'];
                        $GRVDetail_arr['itemPrimaryCode'] = $new['itemPrimaryCode'];
                        $GRVDetail_arr['itemDescription'] = $new['itemDescription'];
                        $GRVDetail_arr['itemFinanceCategoryID'] = $new['itemFinanceCategoryID'];
                        $GRVDetail_arr['itemFinanceCategorySubID'] = $new['itemFinanceCategorySubID'];
                        $GRVDetail_arr['financeGLcodebBSSystemID'] = $new['financeGLcodebBSSystemID'];
                        $GRVDetail_arr['financeGLcodebBS'] = $new['financeGLcodebBS'];
                        $GRVDetail_arr['financeGLcodePLSystemID'] = $new['financeGLcodePLSystemID'];
                        $GRVDetail_arr['financeGLcodePL'] = $new['financeGLcodePL'];
                        $GRVDetail_arr['includePLForGRVYN'] = $new['includePLForGRVYN'];
                        $GRVDetail_arr['supplierPartNumber'] = $new['supplierPartNumber'];
                        $GRVDetail_arr['unitOfMeasure'] = $new['unitOfMeasure'];
                        $GRVDetail_arr['noQty'] = $new['noQty'];
                        $GRVDetail_arr['prvRecievedQty'] = $new['receivedQty'];
                        $GRVDetail_arr['poQty'] = $new['poQty'];
                        $totalNetcost = $new['GRVcostPerUnitSupTransCur'] * $new['noQty'];
                        $GRVDetail_arr['unitCost'] = $new['GRVcostPerUnitSupTransCur'];
                        $GRVDetail_arr['discountPercentage'] = $new['discountPercentage'];
                        $GRVDetail_arr['discountAmount'] = $new['discountAmount'];
                        $GRVDetail_arr['netAmount'] = $totalNetcost;
                        $GRVDetail_arr['comment'] = $new['comment'];
                        $GRVDetail_arr['supplierDefaultCurrencyID'] = $new['supplierDefaultCurrencyID'];
                        $GRVDetail_arr['supplierDefaultER'] = $new['supplierDefaultER'];
                        $GRVDetail_arr['supplierItemCurrencyID'] = $new['supplierItemCurrencyID'];
                        $GRVDetail_arr['foreignToLocalER'] = $new['foreignToLocalER'];
                        $GRVDetail_arr['companyReportingCurrencyID'] = $new['companyReportingCurrencyID'];
                        $GRVDetail_arr['companyReportingER'] = $new['companyReportingER'];
                        $GRVDetail_arr['localCurrencyID'] = $new['localCurrencyID'];
                        $GRVDetail_arr['localCurrencyER'] = $new['localCurrencyER'];
                        $GRVDetail_arr['addonDistCost'] = $new['addonDistCost'];
                        $GRVDetail_arr['GRVcostPerUnitLocalCur'] = $new['GRVcostPerUnitLocalCur'];
                        $GRVDetail_arr['GRVcostPerUnitSupDefaultCur'] = $new['GRVcostPerUnitSupDefaultCur'];
                        $GRVDetail_arr['GRVcostPerUnitSupTransCur'] = $new['GRVcostPerUnitSupTransCur'];
                        $GRVDetail_arr['GRVcostPerUnitComRptCur'] = $new['GRVcostPerUnitComRptCur'];

                        $GRVDetail_arr['landingCost_LocalCur'] = $new['GRVcostPerUnitLocalCur'];
                        $GRVDetail_arr['landingCost_TransCur'] = $new['GRVcostPerUnitSupTransCur'];
                        $GRVDetail_arr['landingCost_RptCur'] = $new['GRVcostPerUnitComRptCur'];

                        $GRVDetail_arr['vatRegisteredYN'] = $POMaster->vatRegisteredYN;
                        $GRVDetail_arr['supplierVATEligible'] = $POMaster->supplierVATEligible;
                        $GRVDetail_arr['VATPercentage'] = $new['VATPercentage'];
                        $GRVDetail_arr['VATAmount'] = $new['VATAmount'];
                        $GRVDetail_arr['VATAmountLocal'] = $new['VATAmountLocal'];
                        $GRVDetail_arr['VATAmountRpt'] = $new['VATAmountRpt'];
                        $GRVDetail_arr['logisticsAvailable'] = $POMaster->logisticsAvailable;
                        $GRVDetail_arr['binNumber'] = $warehouseItem ? $warehouseItem->binNumber : 0;

                        $GRVDetail_arr['createdPcID'] = gethostname();
                        $GRVDetail_arr['createdUserID'] = $user->employee['empID'];
                        $GRVDetail_arr['createdUserSystemID'] = $user->employee['employeeSystemID'];

                        $item = $this->gRVDetailsRepository->create($GRVDetail_arr);

                        $update = PurchaseOrderDetails::where('purchaseOrderDetailsID', $new['purchaseOrderDetailsID'])
                            ->update(['GRVSelectedYN' => $GRVSelectedYN, 'goodsRecievedYN' => $goodsRecievedYN, 'receivedQty' => $totalAddedQty]);
                    }
                }

                // fetching the total count records from purchase Request Details table
                $purchaseOrderDetailTotalAmount = PurchaseOrderDetails::select(DB::raw('SUM(noQty) as detailQty,SUM(receivedQty) as receivedQty'))
                    ->where('purchaseOrderMasterID', $new['purchaseOrderMasterID'])
                    ->first();

                // Updating PO Master Table After All Detail Table records updated
                if ($purchaseOrderDetailTotalAmount['detailQty'] == $purchaseOrderDetailTotalAmount['receivedQty']) {
                    $updatePO = ProcumentOrder::find($new['purchaseOrderMasterID'])
                        ->update(['poClosedYN' => 1, 'grvRecieved' => 2]);
                } else {
                    $updatePO = ProcumentOrder::find($new['purchaseOrderMasterID'])
                        ->update(['poClosedYN' => 0, 'grvRecieved' => 1]);
                }
            }
            DB::commit();
            return $this->sendResponse('', 'GRV details saved successfully');
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError('Error Occurred'. $exception->getMessage() . 'Line :' . $exception->getLine());
        }

    }

    public function storeGRVDetailsDirect(Request $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToValue($input);

        $grvAutoID = $input['grvAutoID'];

        $grvMaster = $this->gRVMasterRepository->findWithoutFail($grvAutoID);
        if (empty($grvMaster)) {
            return $this->sendError('GRV Master not found');
        }

        if ($grvMaster->serviceLineSystemID) {
            $checkDepartmentActive = SegmentMaster::find($grvMaster->serviceLineSystemID);
            if (empty($checkDepartmentActive)) {
                return $this->sendError('Department not found');
            }
            if ($checkDepartmentActive->isActive == 0) {
                return $this->sendError('Please select a active department', 500);
            }
        } else {
            return $this->sendError('Please select a department.', 500);
        }

        if ($grvMaster->grvLocation) {
            $checkWarehouseActive = WarehouseMaster::find($grvMaster->grvLocation);
            if (empty($checkWarehouseActive)) {
                return $this->sendError('Warehouse not found');
            }
            if ($checkWarehouseActive->isActive == 0) {
                return $this->sendError('Please select an active warehouse', 500);
            }
        }
        else {
            return $this->sendError('Please select a warehouse.', 500);
        }

        DB::beginTransaction();
        try {
            $itemAssign = ItemAssigned::find($input['itemCode']);
            if (empty($itemAssign)) {
                return $this->sendError('Item not assigned');
            }

            //checking if item category is same or not
            $grvDetailExistSameItem = GRVDetails::select(DB::raw('DISTINCT(itemFinanceCategoryID) as itemFinanceCategoryID'))
                ->where('grvAutoID', $grvAutoID)
                ->first();

            if ($grvDetailExistSameItem) {
                if ($itemAssign->financeCategoryMaster != $grvDetailExistSameItem["itemFinanceCategoryID"]) {
                    return $this->sendError('You cannot add different category item', 422);
                }
            }

            $user = \Helper::getEmployeeInfo();

            //checking if item is inventory item cannot be added more than one
            $grvDetailExistSameItem = GRVDetails::select(DB::raw('itemCode'))
                ->where('grvAutoID', $grvAutoID)
                ->where('itemCode', $input["itemCode"])
                ->first();

            if ($grvDetailExistSameItem) {
                return $this->sendError('Selected item is already added from the same grv.', 422);
            }

            $financeCategorySub = FinanceItemCategorySub::find($itemAssign->financeCategorySub);

            $currency = \Helper::convertAmountToLocalRpt($grvMaster->documentSystemID,$grvAutoID,$input['unitCost']);

            // checking the qty request is matching with sum total
            $GRVDetail_arr['grvAutoID'] = $grvAutoID;
            $GRVDetail_arr['companySystemID'] = $grvMaster->companySystemID;
            $GRVDetail_arr['companyID'] = $grvMaster->companyID;
            $GRVDetail_arr['serviceLineCode'] =$grvMaster->serviceLineCode;
            $GRVDetail_arr['purchaseOrderMastertID'] = 0;
            $GRVDetail_arr['purchaseOrderDetailsID'] = 0;
            $GRVDetail_arr['itemCode'] = $itemAssign->itemCodeSystem;
            $GRVDetail_arr['itemPrimaryCode'] = $itemAssign->itemPrimaryCode;
            $GRVDetail_arr['itemDescription'] = $itemAssign->itemDescription;
            $GRVDetail_arr['itemFinanceCategoryID'] = $itemAssign->financeCategoryMaster;
            $GRVDetail_arr['itemFinanceCategorySubID'] = $itemAssign->financeCategorySub;
            $GRVDetail_arr['financeGLcodebBSSystemID'] = $financeCategorySub->financeGLcodebBSSystemID;
            $GRVDetail_arr['financeGLcodebBS'] = $financeCategorySub->financeGLcodebBS;
            $GRVDetail_arr['financeGLcodePLSystemID'] = $financeCategorySub->financeGLcodePLSystemID;
            $GRVDetail_arr['financeGLcodePL'] = $financeCategorySub->financeGLcodePL;
            $GRVDetail_arr['includePLForGRVYN'] = $financeCategorySub->includePLForGRVYN;
            $GRVDetail_arr['supplierPartNumber'] = $itemAssign->secondaryItemCode;
            $GRVDetail_arr['unitOfMeasure'] = $itemAssign->itemUnitOfMeasure;
            $GRVDetail_arr['noQty'] = $input['noQty'];
            $GRVDetail_arr['prvRecievedQty'] = 0;
            $GRVDetail_arr['poQty'] = 0;
            $totalNetcost = $input['unitCost'] * $input['noQty'];
            $GRVDetail_arr['unitCost'] = $input['unitCost'];
            $GRVDetail_arr['discountPercentage'] = 0;
            $GRVDetail_arr['discountAmount'] = 0;
            $GRVDetail_arr['netAmount'] = $totalNetcost;
            $GRVDetail_arr['comment'] = $input['comment'];
            $GRVDetail_arr['supplierDefaultCurrencyID'] = $grvMaster->supplierDefaultCurrencyID;
            $GRVDetail_arr['supplierDefaultER'] = $grvMaster->supplierDefaultER;
            $GRVDetail_arr['supplierItemCurrencyID'] = $grvMaster->supplierTransactionCurrencyID;
            $GRVDetail_arr['foreignToLocalER'] = $grvMaster->supplierTransactionER;
            $GRVDetail_arr['companyReportingCurrencyID'] = $grvMaster->companyReportingCurrencyID;
            $GRVDetail_arr['companyReportingER'] = $grvMaster->companyReportingER;
            $GRVDetail_arr['localCurrencyID'] = $grvMaster->localCurrencyID;
            $GRVDetail_arr['localCurrencyER'] = $grvMaster->localCurrencyER;
            $GRVDetail_arr['addonDistCost'] = 0;
            $GRVDetail_arr['GRVcostPerUnitLocalCur'] = \Helper::roundValue($currency['localAmount']);
            $GRVDetail_arr['GRVcostPerUnitSupDefaultCur'] = \Helper::roundValue($currency['defaultAmount']);
            $GRVDetail_arr['GRVcostPerUnitSupTransCur'] = \Helper::roundValue($input['unitCost']);
            $GRVDetail_arr['GRVcostPerUnitComRptCur'] = \Helper::roundValue($currency['reportingAmount']);
            $GRVDetail_arr['landingCost_LocalCur'] = \Helper::roundValue($currency['localAmount']);
            $GRVDetail_arr['landingCost_TransCur'] = \Helper::roundValue($input['unitCost']);
            $GRVDetail_arr['landingCost_RptCur'] = \Helper::roundValue($currency['reportingAmount']);
            $GRVDetail_arr['vatRegisteredYN'] = 0;
            $GRVDetail_arr['supplierVATEligible'] = 0;
            $GRVDetail_arr['VATPercentage'] = 0;
            $GRVDetail_arr['VATAmount'] = 0;
            $GRVDetail_arr['VATAmountLocal'] = 0;
            $GRVDetail_arr['VATAmountRpt'] = 0;
            $GRVDetail_arr['logisticsAvailable'] = 0;
            $GRVDetail_arr['createdPcID'] = gethostname();
            $GRVDetail_arr['createdUserID'] = $user->empID;
            $GRVDetail_arr['createdUserSystemID'] = $user->employeeSystemID;

            $item = $this->gRVDetailsRepository->create($GRVDetail_arr);

            DB::commit();
            return $this->sendResponse('', 'GRV details saved successfully');
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError('Error Occurred');
        }

    }

    public function updateGRVDetailsDirect(Request $request)
    {
        $input = $request->all();
        $input = array_except($input, ['unit', 'po_master']);
        $input = $this->convertArrayToValue($input);

        $id=$input['grvDetailsID'];
        $grvAutoID = $input['grvAutoID'];

        $grvMaster = $this->gRVMasterRepository->findWithoutFail($grvAutoID);
        if (empty($grvMaster)) {
            return $this->sendError('GRV Master not found');
        }

        if ($grvMaster->serviceLineSystemID) {
            $checkDepartmentActive = SegmentMaster::find($grvMaster->serviceLineSystemID);
            if (empty($checkDepartmentActive)) {
                return $this->sendError('Department not found');
            }
            if ($checkDepartmentActive->isActive == 0) {
                return $this->sendError('Please select a active department', 500);
            }
        } else {
            return $this->sendError('Please select a department.', 500);
        }

        if ($grvMaster->grvLocation) {
            $checkWarehouseActive = WarehouseMaster::find($grvMaster->grvLocation);
            if (empty($checkWarehouseActive)) {
                return $this->sendError('Warehouse not found');
            }
            if ($checkWarehouseActive->isActive == 0) {
                return $this->sendError('Please select an active warehouse', 500);
            }
        }
        else {
            return $this->sendError('Please select a warehouse.', 500);
        }

        DB::beginTransaction();
        try {
            $itemAssign = ItemAssigned::find($input['itemCode']);
            if (empty($itemAssign)) {
                return $this->sendError('Item not assigned');
            }

            $user = \Helper::getEmployeeInfo();
            $financeCategorySub = FinanceItemCategorySub::find($itemAssign->financeCategorySub);
            $currency = \Helper::convertAmountToLocalRpt($grvMaster->documentSystemID,$grvAutoID,$input['unitCost']);

            // checking the qty request is matching with sum total
            $GRVDetail_arr['grvAutoID'] = $grvAutoID;
            $GRVDetail_arr['noQty'] = $input['noQty'];
            $totalNetcost = $input['unitCost'] * $input['noQty'];
            $GRVDetail_arr['unitCost'] = $input['unitCost'];
            $GRVDetail_arr['netAmount'] = $totalNetcost;
            $GRVDetail_arr['comment'] = $input['comment'];
            $GRVDetail_arr['GRVcostPerUnitLocalCur'] = \Helper::roundValue($currency['localAmount']);
            $GRVDetail_arr['GRVcostPerUnitSupDefaultCur'] = \Helper::roundValue($currency['defaultAmount']);
            $GRVDetail_arr['GRVcostPerUnitSupTransCur'] = \Helper::roundValue($input['unitCost']);
            $GRVDetail_arr['GRVcostPerUnitComRptCur'] = \Helper::roundValue($currency['reportingAmount']);
            $GRVDetail_arr['landingCost_LocalCur'] = \Helper::roundValue($currency['localAmount']);
            $GRVDetail_arr['landingCost_TransCur'] = \Helper::roundValue($input['unitCost']);
            $GRVDetail_arr['landingCost_RptCur'] = \Helper::roundValue($currency['reportingAmount']);
            $GRVDetail_arr['modifiedPc'] = gethostname();
            $GRVDetail_arr['modifiedUser'] = $user->empID;

            $item = $this->gRVDetailsRepository->update($GRVDetail_arr,$id);

            DB::commit();
            return $this->sendResponse('', 'GRV details saved successfully');
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError('Error Occurred');
        }

    }



    public function grvDeleteAllDetails(Request $request)
    {
        $input = $request->all();

        $grvAutoID = $input['grvAutoID'];

        $detailExistAll = GRVDetails::where('grvAutoID', $grvAutoID)
            ->get();

        if (empty($detailExistAll)) {
            return $this->sendError('There are no details to delete');
        }

        if (!empty($detailExistAll)) {
            foreach ($detailExistAll as $cvDeatil) {
                $deleteDetails = GRVDetails::where('grvDetailsID', $cvDeatil['grvDetailsID'])->delete();
                if (!empty($cvDeatil['purchaseOrderDetailsID']) && !empty($cvDeatil['purchaseOrderMastertID'])) {

                    $detailExistPODetail = PurchaseOrderDetails::find($cvDeatil->purchaseOrderDetailsID);
                    // get the total received qty for a specific item
                    $detailPOSUM = GRVDetails::WHERE('purchaseOrderMastertID', $cvDeatil['purchaseOrderMastertID'])->WHERE('companySystemID', $cvDeatil['companySystemID'])->WHERE('purchaseOrderDetailsID', $cvDeatil['purchaseOrderDetailsID'])->sum('noQty');
                    // get the total received qty
                    $masterPOSUM = GRVDetails::WHERE('purchaseOrderMastertID', $cvDeatil['purchaseOrderMastertID'])->WHERE('companySystemID', $cvDeatil['companySystemID'])->sum('noQty');
                    $poQty = $detailExistPODetail->receivedQty - $cvDeatil->noQty;

                    $receivedQty = 0;
                    $goodsRecievedYN = 0;
                    $GRVSelectedYN = 0;
                    if ($detailPOSUM > 0) {
                        $receivedQty = $detailPOSUM;
                    }

                    $checkQuantity = $detailExistPODetail->noQty - $receivedQty;

                    if ($receivedQty == 0) {
                        $goodsRecievedYN = 0;
                        $GRVSelectedYN = 0;
                    } else {
                        if ($checkQuantity == 0) {
                            $goodsRecievedYN = 2;
                            $GRVSelectedYN = 1;
                        } else {
                            $goodsRecievedYN = 1;
                            $GRVSelectedYN = 0;
                        }
                    }

                    $updateDetail = PurchaseOrderDetails::where('purchaseOrderDetailsID', $detailExistPODetail->purchaseOrderDetailsID)
                        ->update(['GRVSelectedYN' => $GRVSelectedYN, 'goodsRecievedYN' => $goodsRecievedYN, 'receivedQty' => $receivedQty]);

                    if ($masterPOSUM > 0) {
                        $updatePO = ProcumentOrder::find($cvDeatil['purchaseOrderMastertID'])
                            ->update(['poClosedYN' => 0, 'grvRecieved' => 1]);
                    } else {
                        $updatePO = ProcumentOrder::find($cvDeatil['purchaseOrderMastertID'])
                            ->update(['poClosedYN' => 0, 'grvRecieved' => 0]);
                    }
                }
            }
        }

        //update po master
        $updateMaster = GRVMaster::where('grvAutoID', $grvAutoID)
            ->update(['grvTotalComRptCurrency' => 0,
                'grvTotalLocalCurrency' => 0,
                'grvTotalSupplierDefaultCurrency' => 0,
                'grvTotalSupplierTransactionCurrency' => 0
            ]);

        return $this->sendResponse($grvAutoID, 'GRV details deleted successfully');
    }
}
