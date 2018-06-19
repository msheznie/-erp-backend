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
use App\Models\GRVDetails;
use App\Models\GRVMaster;
use App\Models\ProcumentOrder;
use App\Models\CompanyPolicyMaster;
use App\Models\PurchaseOrderDetails;
use App\Repositories\GRVDetailsRepository;
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
    private $userRepository;

    public function __construct(GRVDetailsRepository $gRVDetailsRepo, UserRepository $userRepo)
    {
        $this->gRVDetailsRepository = $gRVDetailsRepo;
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

        return $this->sendResponse($gRVDetails->toArray(), 'GRV Details retrieved successfully');
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

        return $this->sendResponse($gRVDetails->toArray(), 'GRV Details saved successfully');
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
            return $this->sendError('G R V Details not found');
        }

        return $this->sendResponse($gRVDetails->toArray(), 'GRV Details retrieved successfully');
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

        $grvMaster= GRVMaster::find($input['grvAutoID']);

        if (empty($grvMaster)) {
            return $this->sendError('GRV not found');
        }

        $input['modifiedPc'] = gethostname();
        $input['modifiedUser'] = $user->employee['empID'];

        if (!empty($input['purchaseOrderDetailsID']) && !empty($input['purchaseOrderMastertID'])) {

            $detailExistPRDetail = PurchaseOrderDetails::find($input['purchaseOrderDetailsID']);

            $checkQuentity = $detailExistPRDetail->noQty - $input['noQty'];

            if ($checkQuentity == 0) {
                $goodsRecievedYN = 2;
                $GRVSelectedYN = 1;
            } else {
                $goodsRecievedYN = 1;
                $GRVSelectedYN = 0;
            }

            $update = PurchaseOrderDetails::where('purchaseOrderDetailsID', $input['purchaseOrderDetailsID'])
                ->update(['GRVSelectedYN' => $GRVSelectedYN, 'goodsRecievedYN' => $goodsRecievedYN, 'receivedQty' => $input['noQty']]);

            //check all details fullyOrdered in PR Master
            $prMasterfullyOrdered = PurchaseOrderDetails::where('purchaseOrderMasterID', $input['purchaseOrderMastertID'])
                ->whereIn('goodsRecievedYN', [1, 0])
                ->get()->toArray();

            if (empty($prMasterfullyOrdered)) {
                $updatePRMaster = ProcumentOrder::find($input['purchaseOrderMastertID'])
                    ->update(['goodsRecievedYN' => 2, 'GRVSelectedYN' => 1]);
            } else {
                $updatePRMaster = ProcumentOrder::find($input['purchaseOrderMastertID'])
                    ->update(['goodsRecievedYN' => 0, 'GRVSelectedYN' => 0]);
            }

        }

        $gRVDetails = $this->gRVDetailsRepository->update($input, $id);

        return $this->sendResponse($gRVDetails->toArray(), 'GRV Details updated successfully');
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

        if (empty($gRVDetails)) {
            return $this->sendError('GRV Details not found');
        }

        $gRVDetails->delete();

        $grvMaster = GRVMaster::find($gRVDetails->grvAutoID);

        // updating master and detail table number of qty

        if (!empty($gRVDetails->purchaseOrderDetailsID) && !empty($gRVDetails->purchaseOrderMastertID)) {
            $updatePOMaster = ProcumentOrder::find($gRVDetails->purchaseOrderMastertID)
                ->update([
                    'poClosedYN' => 0,
                    'grvRecieved' => 1
                ]);

            $detailExistPODetail = PurchaseOrderDetails::find($gRVDetails->purchaseOrderDetailsID);

            $poQty = $detailExistPODetail->receivedQty - $gRVDetails->noQty;

            if ($poQty == 0) {
                $goodsRecievedYN = 0;
            } else {
                $goodsRecievedYN = 1;
            }

            $updateDetail = PurchaseOrderDetails::where('purchaseOrderDetailsID', $detailExistPODetail->purchaseOrderDetailsID)
                ->update(['GRVSelectedYN' => 0, 'goodsRecievedYN' => $goodsRecievedYN, 'receivedQty' => $poQty]);
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
        $item = array();

        $grvAutoID = $input['grvAutoID'];

        $id = Auth::id();
        $user = $this->userRepository->with(['employee'])->findWithoutFail($id);

        foreach ($input['detailTable'] as $newValidation) {
            if (($newValidation['isChecked'] && $newValidation['noQty'] == '') || ($newValidation['isChecked'] == '' && $newValidation['noQty'] > 0)) {
                $validator = \Validator::make($newValidation, [
                    'noQty' => 'required',
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

        $GRVMaster = GRVMaster::where('grvAutoID', $grvAutoID)
            ->first();

        $allowMultiplePO = CompanyPolicyMaster::where('companyPolicyCategoryID', 10)
            ->where('companySystemID', $GRVMaster->companySystemID)
            ->first();

        $allowPartialGRVPolicy = CompanyPolicyMaster::where('companyPolicyCategoryID', 23)
            ->where('companySystemID', $GRVMaster->companySystemID)
            ->first();

        $sizeofDetail = sizeof($input['detailTable']);

        foreach ($input['detailTable'] as $new) {

            $POMaster = ProcumentOrder::find($new['purchaseOrderMasterID']);

            if ($allowPartialGRVPolicy->isYesNO == 0 && $POMaster->partiallyGRVAllowed == 0) {

                $poDetailTotal = DB::select('SELECT COUNT(*) as tot FROM erp_purchaseorderdetails WHERE purchaseOrderMasterID = ' . $new['purchaseOrderMasterID'] . '');

                if ($poDetailTotal['tot'] != $sizeofDetail) {
                    return $this->sendError('PO all detail items should be pulled');
                }

            }

            if ($allowMultiplePO->isYesNO == 0) {
                $grvDetailExistSameItem = GRVDetails::select(DB::raw('purchaseOrderMastertID'))
                    ->where('grvAutoID', $grvAutoID)
                    ->first();

                if (!empty($grvDetailExistSameItem)) {
                    if ($grvDetailExistSameItem['purchaseOrderMastertID'] != $new['purchaseOrderMasterID']) {
                        return $this->sendError('You cannot add multiple PO');
                    }
                }
            }

            if ($new['isChecked'] && $new['noQty'] > 0) {

                //checking if item is inventory item cannot be added more than one
                if ($POMaster->financeCategory == 1) {

                    $grvDetailExistSameItem = GRVDetails::select(DB::raw('itemCode'))
                        ->where('grvAutoID', $grvAutoID)
                        ->where('itemCode', $new['itemCode'])
                        ->first();

                    if ($grvDetailExistSameItem) {
                        return $this->sendError('Same inventory item cannot be added more than once');
                    }
                }

                $totalAddedQty = $new['noQty'] + $new['receivedQty'];

                if ($new['poQty'] == $totalAddedQty) {
                    $goodsRecievedYN = 2;
                    $GRVSelectedYN = 1;
                } else {
                    $goodsRecievedYN = 1;
                    $GRVSelectedYN = 0;
                }

                // checking the qty request is matching with sum total
                //if ($new['quantityRequested'] >= $totalAddedQty) {
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
                    $GRVDetail_arr['prvRecievedQty'] = $new['noQty'];
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
                    $GRVDetail_arr['vatRegisteredYN'] = $POMaster->vatRegisteredYN;
                    $GRVDetail_arr['supplierVATEligible'] = $POMaster->supplierVATEligible;
                    $GRVDetail_arr['VATPercentage'] = $new['VATPercentage'];
                    $GRVDetail_arr['VATAmount'] = $new['VATAmount'];
                    $GRVDetail_arr['VATAmountLocal'] = $new['VATAmountLocal'];
                    $GRVDetail_arr['VATAmountRpt'] = $new['VATAmountRpt'];
                    $GRVDetail_arr['logisticsAvailable'] = $POMaster->logisticsAvailable;

                    $GRVDetail_arr['createdPcID'] = gethostname();
                    $GRVDetail_arr['createdUserID'] = $user->employee['empID'];
                    $GRVDetail_arr['createdUserSystemID'] = $user->employee['employeeSystemID'];

                    $item = $this->gRVDetailsRepository->create($GRVDetail_arr);

                    $update = PurchaseOrderDetails::where('purchaseOrderDetailsID', $new['purchaseOrderDetailsID'])
                        ->update(['GRVSelectedYN' => $GRVSelectedYN, 'goodsRecievedYN' => $goodsRecievedYN, 'receivedQty' => $totalAddedQty]);
                }

                // fetching the total count records from purchase Request Details table
                $purchaseRequestDetailTotalcount = PurchaseOrderDetails::select(DB::raw('count(purchaseOrderDetailsID) as detailCount'))
                    ->where('purchaseOrderMasterID', $new['purchaseOrderMasterID'])
                    ->first();

                // fetching the total count records from purchase Request Details table where fullyOrdered = 2
                $purchaseRequestDetailExist = PurchaseOrderDetails::select(DB::raw('count(purchaseOrderDetailsID) as count'))
                    ->where('purchaseOrderMasterID', $new['purchaseOrderMasterID'])
                    ->where('goodsRecievedYN', 2)
                    ->where('GRVSelectedYN', 1)
                    ->first();

                // Updating PR Master Table After All Detail Table records updated
                if ($purchaseRequestDetailTotalcount['detailCount'] == $purchaseRequestDetailExist['count']) {
                    $updatePR = ProcumentOrder::find($new['purchaseOrderMasterID'])
                        ->update(['poClosedYN' => 1, 'grvRecieved' => 2]);
                } else {
                    $updatePR = ProcumentOrder::find($new['purchaseOrderMasterID'])
                        ->update(['grvRecieved' => 1]);
                }
            }

        }

        //check all details fullyOrdered in PR Master
        $prMasterfullyOrdered = PurchaseOrderDetails::where('purchaseOrderMasterID', $new['purchaseOrderMasterID'])
            ->whereIn('goodsRecievedYN', [1, 0])
            ->get()->toArray();

        if (empty($prMasterfullyOrdered)) {
            $updatePRMaster = ProcumentOrder::find($new['purchaseOrderMasterID'])
                ->update([
                    'poClosedYN' => 1,
                    'grvRecieved' => 2
                ]);
        } else {
            $updatePRMaster = ProcumentOrder::find($new['purchaseOrderMasterID'])
                ->update([
                    'grvRecieved' => 1,
                ]);
        }

        return $this->sendResponse('', 'GRV Details saved successfully');

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
                    $updatePOMaster = ProcumentOrder::find($cvDeatil['purchaseOrderMastertID'])
                        ->update([
                            'goodsRecievedYN' => 0,
                            'GRVSelectedYN' => 0
                        ]);

                    $detailExistPODetail = PurchaseOrderDetails::find($cvDeatil->purchaseOrderDetailsID);

                    $poQty = $detailExistPODetail->receivedQty - $cvDeatil->noQty;

                    if ($poQty == 0) {
                        $goodsRecievedYN = 0;
                    } else {
                        $goodsRecievedYN = 1;
                    }

                    $updateDetail = PurchaseOrderDetails::where('purchaseOrderDetailsID', $detailExistPODetail->purchaseOrderDetailsID)
                        ->update(['GRVSelectedYN' => 0, 'goodsRecievedYN' => $goodsRecievedYN, 'receivedQty' => $poQty]);
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
