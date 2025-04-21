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

use App\helper\TaxService;
use App\helper\Helper;
use App\helper\ItemTracking;
use App\Http\Requests\API\CreateGRVDetailsAPIRequest;
use App\Http\Requests\API\UpdateGRVDetailsAPIRequest;
use App\Models\FinanceItemCategorySub;
use App\Models\GRVDetails;
use App\Models\TaxVatCategories;
use App\Models\GRVMaster;
use App\Models\ItemSerial;
use App\Models\ItemBatch;
use App\Models\DocumentSubProduct;
use App\Models\ItemAssigned;
use App\Models\ItemMaster;
use App\Models\PoAdvancePayment;
use App\Models\PurchaseReturn;
use App\Models\ProcumentOrder;
use App\Models\CompanyPolicyMaster;
use App\Models\ProcumentOrderDetail;
use App\Models\PurchaseReturnDetails;
use App\Models\PurchaseOrderDetails;
use App\Models\SegmentMaster;
use App\Models\SupplierAssigned;
use App\Models\SupplierMaster;
use App\Models\GrvDetailsPrn;
use App\Models\WarehouseItems;
use App\Models\WarehouseMaster;
use App\Repositories\ExpenseAssetAllocationRepository;
use App\Repositories\GRVDetailsRepository;
use App\Repositories\GRVMasterRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Models\ChartOfAccountsAssigned;
use App\Models\Company;
use App\Models\CompanyFinanceYear;
use App\Models\CurrencyMaster;
use App\Models\DocumentMaster;
use App\Models\GRVTypes;
use App\Models\SupplierCurrency;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\UserRepository;
use Carbon\Carbon;
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
    private $expenseAssetAllocationRepo;

    public function __construct(
        GRVDetailsRepository $gRVDetailsRepo,
        UserRepository $userRepo,
        GRVMasterRepository $gRVMasterRepository,
        ExpenseAssetAllocationRepository $expenseAssetAllocationRepo
    )
    {
        $this->gRVDetailsRepository = $gRVDetailsRepo;
        $this->gRVMasterRepository = $gRVMasterRepository;
        $this->userRepository = $userRepo;
        $this->expenseAssetAllocationRepo = $expenseAssetAllocationRepo;
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
            $input = array_except($input, ['unit', 'po_master', 'prn_master', 'item_by']);
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

            $itemTracking = ItemTracking::validateTrackingQuantity($input['noQty'], $id, $grvMaster->documentSystemID);
            if (!$itemTracking['status']) {
                return $this->sendError($itemTracking['message']);
            }

            $markupUpdatedBy=isset($input['by'])?$input['by']:'';

            $markupArray = $this->setMarkupPercentage($input['unitCost'],$grvMaster,$input['markupPercentage'],$input['markupTransactionAmount'],$markupUpdatedBy);

            $input['markupPercentage'] = $markupArray['markupPercentage'];
            $input['markupTransactionAmount'] = $markupArray['markupTransactionAmount'];
            $input['markupLocalAmount'] = $markupArray['markupLocalAmount'];
            $input['markupReportingAmount'] = $markupArray['markupReportingAmount'];

            $gRVDetails = $this->gRVDetailsRepository->update($input, $id);

            $input['modifiedPc'] = gethostname();
            $input['modifiedUser'] = $user->employee['empID'];

            $allowPartialGRVPolicy = CompanyPolicyMaster::where('companyPolicyCategoryID', 23)
                ->where('companySystemID', $grvMaster->companySystemID)
                ->first();

            if ($grvMaster->pullType == 2) {
                $PRMaster = PurchaseReturn::find($input['purhaseReturnAutoID']);

                if (!empty($input['purhasereturnDetailID']) && !empty($input['purhaseReturnAutoID'])) {
                    $detailExistPODetail = PurchaseReturnDetails::find($input['purhasereturnDetailID']);
                    // if ($allowPartialGRVPolicy->isYesNO == 0 && $POMaster->partiallyGRVAllowed == 0) {
                    //     if (($input['poQty'] - $input['prvRecievedQty']) != $input['noQty']) {
                    //         return $this->sendError('GRV qty should be equal to PO qty', 422);
                    //     }
                    // }

                    if ($input['noQty'] == 0) {
                        return $this->sendError('Number of quantity should not be greater than zero', 422);
                    }

                    if ($input['noQty'] > ($input['poQty'] - $input['prvRecievedQty'])) {
                        return $this->sendError('Number of quantity should not be greater than received qty', 422);
                    }

                    $detailPOSUM = GRVDetails::WHERE('purhaseReturnAutoID', $input['purhaseReturnAutoID'])->WHERE('companySystemID', $grvMaster->companySystemID)->WHERE('purhasereturnDetailID', $input['purhasereturnDetailID'])->sum('noQty');
                    $masterPOSUM = GRVDetails::WHERE('purhaseReturnAutoID', $input['purhaseReturnAutoID'])->WHERE('companySystemID', $grvMaster->companySystemID)->sum('noQty');

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

                    $updateDetail = PurchaseReturnDetails::where('purhasereturnDetailID', $detailExistPODetail->purhasereturnDetailID)
                        ->update(['GRVSelectedYN' => $GRVSelectedYN, 'goodsRecievedYN' => $goodsRecievedYN, 'receivedQty' => $receivedQty]);

                    $balanceQty = PurchaseReturnDetails::selectRaw('SUM(noQty) as noQty,SUM(receivedQty) as receivedQty,SUM(noQty) - SUM(receivedQty) as balanceQty')->WHERE('purhaseReturnAutoID', $input['purhaseReturnAutoID'])->first();


                    if ($balanceQty["balanceQty"] == 0) {
                        $updatePO = PurchaseReturn::find($gRVDetails->purhaseReturnAutoID)
                            ->update(['prClosedYN' => 1, 'grvRecieved' => 2]);
                    } else {
                        if ($masterPOSUM > 0) {
                            $updatePO = PurchaseReturn::find($gRVDetails->purhaseReturnAutoID)
                                ->update(['prClosedYN' => 0, 'grvRecieved' => 1]);
                        } else {
                            $updatePO = PurchaseReturn::find($gRVDetails->purhaseReturnAutoID)
                                ->update(['prClosedYN' => 0, 'grvRecieved' => 0]);
                        }
                    }

                }
            } else {
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

                    $detailPOSUM = GRVDetails::selectRaw('SUM(noQty - returnQty) as newNoQty')
                                             ->WHERE('purchaseOrderMastertID', $input['purchaseOrderMastertID'])
                                             ->whereHas('grv_master', function($query) {
                                                $query->where('grvCancelledYN', '!=', -1);
                                             })
                                             ->WHERE('companySystemID', $grvMaster->companySystemID)
                                             ->WHERE('purchaseOrderDetailsID', $input['purchaseOrderDetailsID'])->first();
                    // get the total received qty
                    $masterPOSUM = GRVDetails::selectRaw('SUM(noQty - returnQty) as newNoQty')
                                             ->WHERE('purchaseOrderMastertID', $input['purchaseOrderMastertID'])
                                             ->whereHas('grv_master', function($query) {
                                                $query->where('grvCancelledYN', '!=', -1);
                                             })
                                             ->WHERE('companySystemID', $grvMaster->companySystemID)->first();

                    $receivedQty = 0;
                    $goodsRecievedYN = 0;
                    $GRVSelectedYN = 0;
                    if ($detailPOSUM->newNoQty > 0) {
                        $receivedQty = $detailPOSUM->newNoQty;
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
                        if ($masterPOSUM->newNoQty > 0) {
                            $updatePO = ProcumentOrder::find($gRVDetails->purchaseOrderMastertID)
                                ->update(['poClosedYN' => 0, 'grvRecieved' => 1]);
                        } else {
                            $updatePO = ProcumentOrder::find($gRVDetails->purchaseOrderMastertID)
                                ->update(['poClosedYN' => 0, 'grvRecieved' => 0]);
                        }
                    }

                    $this->updatePrnRelatedData($id, $input['noQty']);
                }
            }
           
            $this->updatePullType($input['grvAutoID']);
            DB::commit();
            return $this->sendResponse($gRVDetails->toArray(), 'GRV details updated successfully');
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage(), 422);
        }
    }

    public function updatePrnRelatedData($grvDetailsID, $newNoQty)
    {
        $prnRelatedGrvDetails = GrvDetailsPrn::where('grvDetailsID', $grvDetailsID)
                                             ->get();


        foreach ($prnRelatedGrvDetails as $key => $value) {
            if ($newNoQty > 0) {
                if ($value->prnQty < $newNoQty) {
                    $newNoQty = $newNoQty - $value->prnQty;
                    $newPrnQty = $value->prnQty;
                } else {
                    $newPrnQty = $newNoQty;
                    $newNoQty = 0;
                }
            } else {
                $newPrnQty = 0;
            }

            $updatePrnRes = GrvDetailsPrn::where('id', $value->id)
                                         ->update(['prnQty' => $newPrnQty]);
        }


        foreach ($prnRelatedGrvDetails as $key => $value) {
            $detailExistPODetail = PurchaseReturnDetails::find($value->purhasereturnDetailID);

            $detailPOSUM = GrvDetailsPrn::where('grvDetailsID', $grvDetailsID)
                                        ->WHERE('purhasereturnDetailID', $value->purhasereturnDetailID)
                                        ->sum('prnQty');

            $prnDetailsData = PurchaseReturnDetails::where('purhaseReturnAutoID', $detailExistPODetail->purhaseReturnAutoID)
                                                   ->get();

            $prnDetailsIds = collect($prnDetailsData)->pluck('purhasereturnDetailID');

            // get the total received qty
            $masterPOSUM = GrvDetailsPrn::whereIn('purhasereturnDetailID', $prnDetailsIds)
                                        ->sum('prnQty');
         
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

            $updateDetail = PurchaseReturnDetails::where('purhasereturnDetailID', $detailExistPODetail->purhasereturnDetailID)
                ->update(['GRVSelectedYN' => $GRVSelectedYN, 'goodsRecievedYN' => $goodsRecievedYN, 'receivedQty' => $receivedQty]);

            $balanceQty = PurchaseReturnDetails::selectRaw('SUM(noQty) as noQty,SUM(receivedQty) as receivedQty,SUM(noQty) - SUM(receivedQty) as balanceQty')->WHERE('purhaseReturnAutoID', $detailExistPODetail->purhaseReturnAutoID)->first();


            if ($balanceQty["balanceQty"] == 0) {
                $updatePO = PurchaseReturn::find($detailExistPODetail->purhaseReturnAutoID)
                    ->update(['prClosedYN' => 1, 'grvRecieved' => 2]);
            } else {
                if ($masterPOSUM > 0) {
                    $updatePO = PurchaseReturn::find($detailExistPODetail->purhaseReturnAutoID)
                        ->update(['prClosedYN' => 0, 'grvRecieved' => 1]);
                } else {
                    $updatePO = PurchaseReturn::find($detailExistPODetail->purhaseReturnAutoID)
                        ->update(['prClosedYN' => 0, 'grvRecieved' => 0]);
                }
            }
        }
    }

    public function updatePullType($grvAutoID)
    {
        $gRVDetails = GRVDetails::where('grvAutoID', $grvAutoID)->first();

        if (!$gRVDetails) {
            GRVMaster::where('grvAutoID', $grvAutoID)->update(['pullType' => 0]);
        }

        return true;
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
        $gRVDetails = $this->gRVDetailsRepository->with(['item_by'])->findWithoutFail($id);

        if (empty($gRVDetails)) {
            return $this->sendError('GRV Details not found');
        }

        $purchaseOrderDetailsID = $gRVDetails->purchaseOrderDetailsID;
        $purchaseOrderMastertID = $gRVDetails->purchaseOrderMastertID;

        // check logistic item exist
        $logisticItems = PoAdvancePayment::where('grvAutoID', $gRVDetails->grvAutoID)
                                        ->where('confirmedYN', 1)
                                        ->where('approvedYN', -1)
                                        ->count();

        if($logisticItems){
            return $this->sendError('GRV details cannot be deleted as this GRV is linked with logistics. Unlink the logistic data and try again.',500);
        }

        $grvMaster = GRVMaster::find($gRVDetails->grvAutoID);

        if($grvMaster->grvTypeID == 2 && $grvMaster->pullType != 2) {
            $allowPartialGRVPolicy = CompanyPolicyMaster::where('companyPolicyCategoryID', 23)
                ->where('companySystemID', $grvMaster->companySystemID)
                ->first();
            $POMaster = ProcumentOrder::find($purchaseOrderMastertID);

            if ($allowPartialGRVPolicy->isYesNO == 0 && $POMaster->partiallyGRVAllowed == 0) {
                return $this->sendError('You cannot delete one line item as partial GRV is disabled.', 422);
            }
        }

        DB::beginTransaction();
        try {

            if ($gRVDetails->trackingType == 2) {
                $validateSubProductSold = DocumentSubProduct::where('documentSystemID', $grvMaster->documentSystemID)
                                                             ->where('documentDetailID', $id)
                                                             ->where('sold', 1)
                                                             ->first();

                if ($validateSubProductSold) {
                    return $this->sendError('You cannot delete this line item. Serial details are sold already.', 422);
                }

                $subProduct = DocumentSubProduct::where('documentSystemID', $grvMaster->documentSystemID)
                                                 ->where('documentDetailID', $id);

                $serialIds = ($subProduct->count() > 0) ? $subProduct->get()->pluck('productSerialID')->toArray() : [];

                if (count($serialIds) > 0) {
                    $deleteSerial = ItemSerial::whereIn('id', $serialIds)
                                              ->delete();

                    $subProduct->delete();
                }
            } else if ($gRVDetails->trackingType == 1) {
                $validateSubProductSold = DocumentSubProduct::where('documentSystemID', $grvMaster->documentSystemID)
                                                             ->where('documentDetailID', $id)
                                                             ->where('soldQty', '>', 0)
                                                             ->first();

                if ($validateSubProductSold) {
                    return $this->sendError('You cannot delete this line item. batch details are sold already.', 422);
                }

                $subProduct = DocumentSubProduct::where('documentSystemID', $grvMaster->documentSystemID)
                                                 ->where('documentDetailID', $id);

                $productBatchIDs = ($subProduct->count() > 0) ? $subProduct->get()->pluck('productBatchID')->toArray() : [];

                if (count($productBatchIDs) > 0) {
                    $deleteBatch = ItemBatch::whereIn('id', $productBatchIDs)
                                              ->delete();

                    $subProduct->delete();
                }
            }

            $this->expenseAssetAllocationRepo->deleteExpenseAssetAllocation($gRVDetails->grvAutoID, $grvMaster->documentSystemID, $id);

            // delete the grv detail
            $gRVDetails->delete();

            if ($grvMaster->pullType == 2) {
                // updating master and detail table number of qty
                if (!empty($gRVDetails->purhasereturnDetailID) && !empty($gRVDetails->purhaseReturnAutoID)) {
                    $detailExistPODetail = PurchaseReturnDetails::find($gRVDetails->purhasereturnDetailID);
                    // get the total received qty for a specific item
                    $detailPOSUM = GRVDetails::WHERE('purhaseReturnAutoID', $gRVDetails->purhaseReturnAutoID)->WHERE('companySystemID', $grvMaster->companySystemID)->WHERE('purhasereturnDetailID', $gRVDetails->purhasereturnDetailID)->sum('noQty');
                    // get the total received qty
                    $masterPOSUM = GRVDetails::WHERE('purhaseReturnAutoID', $gRVDetails->purhaseReturnAutoID)->WHERE('companySystemID', $grvMaster->companySystemID)->sum('noQty');

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

                    $updateDetail = PurchaseReturnDetails::where('purhasereturnDetailID', $detailExistPODetail->purhasereturnDetailID)
                        ->update(['GRVSelectedYN' => $GRVSelectedYN, 'goodsRecievedYN' => $goodsRecievedYN, 'receivedQty' => $receivedQty]);

                    if ($masterPOSUM > 0) {
                        $updatePO = PurchaseReturn::find($gRVDetails->purhaseReturnAutoID)
                            ->update(['prClosedYN' => 0, 'grvRecieved' => 1]);
                    } else {
                        $updatePO = PurchaseReturn::find($gRVDetails->purhaseReturnAutoID)
                            ->update(['prClosedYN' => 0, 'grvRecieved' => 0]);
                    }
                } 
            } else {
                // updating master and detail table number of qty
                if (!empty($purchaseOrderDetailsID) && !empty($purchaseOrderMastertID)) {
                    $detailExistPODetail = PurchaseOrderDetails::find($purchaseOrderDetailsID);
                    // get the total received qty for a specific item
                    $detailPOSUM = GRVDetails::selectRaw('SUM(noQty - returnQty) as newNoQty')
                                             ->WHERE('purchaseOrderMastertID', $purchaseOrderMastertID)
                                             ->whereHas('grv_master', function($query) {
                                                $query->where('grvCancelledYN', '!=', -1);
                                             })
                                             ->WHERE('companySystemID', $grvMaster->companySystemID)
                                             ->WHERE('purchaseOrderDetailsID', $purchaseOrderDetailsID)->first();
                    // get the total received qty
                    $masterPOSUM = GRVDetails::selectRaw('SUM(noQty - returnQty) as newNoQty')
                                              ->WHERE('purchaseOrderMastertID', $purchaseOrderMastertID)
                                              ->whereHas('grv_master', function($query) {
                                                $query->where('grvCancelledYN', '!=', -1);
                                             })
                                              ->WHERE('companySystemID', $grvMaster->companySystemID)->first();

                    $receivedQty = 0;
                    $goodsRecievedYN = 0;
                    $GRVSelectedYN = 0;
                    if ($detailPOSUM->newNoQty > 0) {
                        $receivedQty = $detailPOSUM->newNoQty;
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

                    if ($masterPOSUM->newNoQty > 0) {
                        $updatePO = ProcumentOrder::find($gRVDetails->purchaseOrderMastertID)
                            ->update(['poClosedYN' => 0, 'grvRecieved' => 1]);
                    } else {
                        $updatePO = ProcumentOrder::find($gRVDetails->purchaseOrderMastertID)
                            ->update(['poClosedYN' => 0, 'grvRecieved' => 0]);
                    }

                    $this->checkAndUpdatePrn($id);
                } 
            }

            $this->updatePullType($gRVDetails->grvAutoID);

            DB::commit();
            return $this->sendResponse($id, 'GRV details deleted successfully');
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError('Error Occurred'. $exception->getMessage() . 'Line :' . $exception->getLine());
        }

    }


    public function checkAndUpdatePrn($grvDetailsID)
    {
        $grvPrnData = GrvDetailsPrn::where('grvDetailsID', $grvDetailsID)
                                   ->get();

        foreach ($grvPrnData as $key => $value) {
            $detailExistPODetail = PurchaseReturnDetails::find($value->purhasereturnDetailID);
            // get the total received qty for a specific item
            $detailPOSUM = GrvDetailsPrn::WHERE('grvDetailsID', '!=',$value->grvDetailsID)
                                        ->WHERE('purhasereturnDetailID', $detailExistPODetail->purhasereturnDetailID)
                                        ->sum('prnQty');

            $prnDetailsData = PurchaseReturnDetails::where('purhaseReturnAutoID', $detailExistPODetail->purhaseReturnAutoID)
                                                   ->get();

            $prnDetailsIds = collect($prnDetailsData)->pluck('purhasereturnDetailID');

            // get the total received qty
            $masterPOSUM = GrvDetailsPrn::WHERE('grvDetailsID', '!=',$value->grvDetailsID)
                                        ->whereIn('purhasereturnDetailID', $prnDetailsIds)
                                        ->sum('prnQty');

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

            $updateDetail = PurchaseReturnDetails::where('purhasereturnDetailID', $value->purhasereturnDetailID)
                ->update(['GRVSelectedYN' => $GRVSelectedYN, 'goodsRecievedYN' => $goodsRecievedYN, 'receivedQty' => $receivedQty]);

            if ($masterPOSUM > 0) {
                $updatePO = PurchaseReturn::find($detailExistPODetail->purhaseReturnAutoID)
                    ->update(['prClosedYN' => 0, 'grvRecieved' => 1]);
            } else {
                $updatePO = PurchaseReturn::find($detailExistPODetail->purhaseReturnAutoID)
                    ->update(['prClosedYN' => 0, 'grvRecieved' => 0]);
            }
        }

        $grvPrnData = GrvDetailsPrn::where('grvDetailsID', $grvDetailsID)
                                   ->delete();
    }

    public function getItemsByGRVMaster(Request $request)
    {
        $input = $request->all();
        $grvAutoID = $input['grvAutoID'];

        $items = GRVDetails::where('grvAutoID', $grvAutoID)
            ->with(['unit' => function ($query) {
            }, 'po_master' => function ($query) {
            }, 'prn_master', 'item_by'])
            ->get();

        return $this->sendResponse($items->toArray(), 'GRV details retrieved successfully');
    }

    public function storeGRVDetailsFromPO(Request $request)
    {
        $input = $request->all();
        $GRVDetail_arr = array();

        DB::beginTransaction();
        try {

            
            $id = Auth::id();
            $user = $this->userRepository->with(['employee'])->findWithoutFail($id);

            if (isset($input['grvMasterData'])){

                $input['grvMasterData'] = $this->convertArrayToValue($input['grvMasterData']);
                $grvMasterData = $input['grvMasterData'];

                $companyFinanceYear = \Helper::companyFinanceYearCheck($grvMasterData);
                if (!$companyFinanceYear["success"]) {
                    return $this->sendError($companyFinanceYear["message"], 500);
                }

                $inputParam = $grvMasterData;
                $inputParam["departmentSystemID"] = 10;
                $companyFinancePeriod = \Helper::companyFinancePeriodCheck($inputParam);
                if (!$companyFinancePeriod["success"]) {
                    return $this->sendError($companyFinancePeriod["message"], 500);
                } else {
                    $grvMasterData['FYBiggin'] = $companyFinancePeriod["message"]->dateFrom;
                    $grvMasterData['FYEnd'] = $companyFinancePeriod["message"]->dateTo;
                }
        
                unset($inputParam);

                $currentDate = Carbon::parse(now())->format('Y-m-d') . ' 00:00:00';
                if (isset($grvMasterData['grvDate'])) {
                    if ($grvMasterData['grvDate']) {
                        $grvMasterData['grvDate'] = Carbon::parse($grvMasterData['grvDate'])->format('Y-m-d') . ' 00:00:00';
                        if ($grvMasterData['grvDate'] > $currentDate) {
                            return $this->sendError('GRV date can not be greater than current date', 500);
                        }
                    }
                } else {
                    return $this->sendError('GRV Date Not Selected', 500);
                }

                if (isset($grvMasterData['stampDate'])) {
                    if ($grvMasterData['stampDate']) {
                        $grvMasterData['stampDate'] = Carbon::parse($grvMasterData['stampDate'])->format('Y-m-d') . ' 00:00:00';
                    }
        
                    if ($grvMasterData['stampDate'] > $currentDate) {
                        return $this->sendError('Stamp date can not be greater than current date', 500);
                    }
                } else {
                    return $this->sendError('Stamp Date Not Selected', 500);
                }

                if(isset($grvMasterData['grvLocation'])){

                    $warehouse = WarehouseMaster::where("wareHouseSystemCode", $grvMasterData['grvLocation'])
                    ->where('companySystemID', $grvMasterData['companySystemID'])
                    ->first();
    
                    if (!$warehouse) {
                    return $this->sendError('Location not found', 500);
                    }
    
                    if ($warehouse->manufacturingYN == 1) {
                        if (is_null($warehouse->WIPGLCode)) {
                            return $this->sendError('Please assigned WIP GLCode for this warehouse', 500);
                        } else {
                            $checkGLIsAssigned = ChartOfAccountsAssigned::checkCOAAssignedStatus($warehouse->WIPGLCode, $input['companySystemID']);
                            if (!$checkGLIsAssigned) {
                                return $this->sendError('Assigned WIP GL Code is not assigned to this company!', 500);
                            }
                        }
                    }
                } else {
                    return $this->sendError('Location Not Selected', 500);
                }



                $documentDate = $grvMasterData['grvDate'];
                $monthBegin = $grvMasterData['FYBiggin'];
                $monthEnd = $grvMasterData['FYEnd'];
        
                if (($documentDate >= $monthBegin) && ($documentDate <= $monthEnd)) {
                } else {
                    return $this->sendError('GRV date is not within the financial period!');
                }

                $grvMasterData['createdPcID'] = gethostname();
                $grvMasterData['createdUserID'] = $user->employee['empID'];
                $grvMasterData['createdUserSystemID'] = $user->employee['employeeSystemID'];
                $grvMasterData['documentSystemID'] = '3';
                $grvMasterData['documentID'] = 'GRV';

                $grvType = GRVTypes::where('grvTypeID', $grvMasterData['grvTypeID'])->first();
                if ($grvType) {
                    $grvMasterData['grvType'] = $grvType->idERP_GrvTpes;
                }

                $segment = SegmentMaster::where('serviceLineSystemID', $grvMasterData['serviceLineSystemID'])->first();
                if ($segment) {
                    $grvMasterData['serviceLineCode'] = $segment->ServiceLineCode;
                }

                $companyCurrencyConversion = \Helper::currencyConversion($grvMasterData['companySystemID'], $grvMasterData['supplierTransactionCurrencyID'], $grvMasterData['supplierTransactionCurrencyID'], 0);

                $company = Company::where('companySystemID', $grvMasterData['companySystemID'])->first();
                if ($company) {
                    $grvMasterData['companyID'] = $company->CompanyID;
                    $grvMasterData['localCurrencyID'] = $company->localCurrencyID;
                    $grvMasterData['companyReportingCurrencyID'] = $company->reportingCurrency;
                    $grvMasterData['vatRegisteredYN'] = $company->vatRegisteredYN;
                    $grvMasterData['companyReportingER'] = $companyCurrencyConversion['trasToRptER'];
                    $grvMasterData['localCurrencyER'] = $companyCurrencyConversion['trasToLocER'];
                }
        
                $grvMasterData['supplierTransactionER'] = 1;
        
                $supplier = SupplierMaster::where('supplierCodeSystem', $grvMasterData['supplierID'])->first();
                if ($supplier) {
                    $grvMasterData['supplierPrimaryCode'] = $supplier->primarySupplierCode;
                    $grvMasterData['supplierName'] = $supplier->supplierName;
                    $grvMasterData['supplierAddress'] = $supplier->address;
                    $grvMasterData['supplierTelephone'] = $supplier->telephone;
                    $grvMasterData['supplierFax'] = $supplier->fax;
                    $grvMasterData['supplierEmail'] = $supplier->supEmail;
                }

                    // get last serial number by company financial year
                $lastSerial = GRVMaster::where('companySystemID', $grvMasterData['companySystemID'])
                ->where('companyFinanceYearID', $grvMasterData['companyFinanceYearID'])
                ->orderBy('grvSerialNo', 'desc')
                ->lockForUpdate()
                ->first();

                $lastSerialNumber = 1;
                if ($lastSerial) {
                    $lastSerialNumber = intval($lastSerial->grvSerialNo) + 1;
                }
                $grvMasterData['grvSerialNo'] = $lastSerialNumber;
                // get document code
                $documentMaster = DocumentMaster::where('documentSystemID', $grvMasterData['documentSystemID'])->first();

                $companyfinanceyear = CompanyFinanceYear::where('companyFinanceYearID', $grvMasterData['companyFinanceYearID'])
                    ->where('companySystemID', $grvMasterData['companySystemID'])
                    ->first();

                if ($companyfinanceyear) {
                    $startYear = $companyfinanceyear['bigginingDate'];
                    $finYearExp = explode('-', $startYear);
                    $finYear = $finYearExp[0];
                } else {
                    $finYear = date("Y");
                }
                if ($documentMaster) { // generate document code
                    $grvCode = ($company->CompanyID . '\\' . $finYear . '\\' . $documentMaster['documentID'] . str_pad($lastSerialNumber, 6, '0', STR_PAD_LEFT));
                    $grvMasterData['grvPrimaryCode'] = $grvCode;
                }

                $supplierCurrency = SupplierCurrency::where('supplierCodeSystem', $grvMasterData['supplierID'])
                    ->where('isDefault', -1)
                    ->first();

                if ($supplierCurrency) {

                    $erCurrency = CurrencyMaster::where('currencyID', $supplierCurrency->currencyID)->first();

                    $grvMasterData['supplierDefaultCurrencyID'] = $supplierCurrency->currencyID;

                    if ($erCurrency) {
                        $grvMasterData['supplierDefaultER'] = $erCurrency->ExchangeRate;
                    }
                }

                // adding supplier grv details
                $supplierAssignedDetail = SupplierAssigned::where('supplierCodeSytem', $grvMasterData['supplierID'])
                    ->where('companySystemID', $grvMasterData['companySystemID'])
                    ->first();

                if ($supplierAssignedDetail) {
                    $grvMasterData['liabilityAccountSysemID'] = $supplierAssignedDetail->liabilityAccountSysemID;
                    $grvMasterData['liabilityAccount'] = $supplierAssignedDetail->liabilityAccount;
                    $grvMasterData['UnbilledGRVAccountSystemID'] = $supplierAssignedDetail->UnbilledGRVAccountSystemID;
                    $grvMasterData['UnbilledGRVAccount'] = $supplierAssignedDetail->UnbilledGRVAccount;
                }

                $gRVMasters = $this->gRVMasterRepository->create($grvMasterData);

                $grvPrimaryCode = $gRVMasters->grvPrimaryCode;
                $grvAutoID = $gRVMasters->grvAutoID;

            } else {
                $grvAutoID = $input['grvAutoID'];
            }

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

                    $epsilon = 0.000001;
                    if ($new['noQty'] - ($new['poQty'] - $new['receivedQty']) > $epsilon) {
                        return $this->sendError('Number of quantity should not be greater than received qty for item '.$new['itemPrimaryCode']. ' - ' .$new['itemDescription'], 422);
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

                    $allowFinanceCategory = CompanyPolicyMaster::where('companyPolicyCategoryID', 20)
                                                                ->where('companySystemID', $GRVMaster->companySystemID)
                                                                ->first();

                    if ($allowFinanceCategory) {
                        $policy = $allowFinanceCategory->isYesNO;

                        if ($policy == 0) {
                            $grvDetailExistSameItem = GRVDetails::select(DB::raw('DISTINCT(itemFinanceCategoryID) as itemFinanceCategoryID'))
                                ->where('grvAutoID', $grvAutoID)
                                ->first();

                            if ($grvDetailExistSameItem) {
                                if ($new['itemFinanceCategoryID'] != $grvDetailExistSameItem["itemFinanceCategoryID"]) {
                                    return $this->sendError('You cannot add different category item', 422);
                                }
                            }

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
                    $financeCategorySub = FinanceItemCategorySub::find($new['itemFinanceCategorySubID']);
                    if(isset($financeCategorySub))
                    {
                        $financeGLcodePL = $financeCategorySub->financeGLcodePL;
                        $financeGLcodePLSystemID = $financeCategorySub->financeGLcodePLSystemID;
                        $financeGLcodebBS = $financeCategorySub->financeGLcodebBS;
                        $financeGLcodebBSSystemID = $financeCategorySub->financeGLcodebBSSystemID;
                    }
                    else
                    {
                        $financeGLcodePL = null;
                        $financeGLcodePLSystemID = null;
                        $financeGLcodebBS = null;
                        $financeGLcodebBSSystemID = null;
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
                        // if ($new['itemFinanceCategoryID'] == 1 && WarehouseMaster::checkManuefactoringWareHouse($GRVMaster->grvLocation)) {

                        //     if($financeCategorySub->includePLForGRVYN == -1)
                        //     {
                        //         $GRVDetail_arr['financeGLcodebBSSystemID'] = null;
                        //         $GRVDetail_arr['financeGLcodebBS'] = null;
                        //         $GRVDetail_arr['financeGLcodePLSystemID'] = WarehouseMaster::getWIPGLSystemID($GRVMaster->grvLocation);
                        //         $GRVDetail_arr['financeGLcodePL'] = WarehouseMaster::getWIPGLCode($GRVMaster->grvLocation);
                        //     }
                        //     else
                        //     {
                        //         $GRVDetail_arr['financeGLcodebBSSystemID'] = WarehouseMaster::getWIPGLSystemID($GRVMaster->grvLocation);
                        //         $GRVDetail_arr['financeGLcodebBS'] = WarehouseMaster::getWIPGLCode($GRVMaster->grvLocation);
                        //         $GRVDetail_arr['financeGLcodePLSystemID'] = $financeGLcodePLSystemID;
                        //         $GRVDetail_arr['financeGLcodePL'] = $financeGLcodePL;
                        //     }

                        
                        // }  
                        
                       

                        $GRVDetail_arr['financeGLcodebBSSystemID'] = $financeGLcodebBSSystemID;
                        $GRVDetail_arr['financeGLcodebBS'] = $financeGLcodebBS;
                        $GRVDetail_arr['financeGLcodePLSystemID'] = $financeGLcodePLSystemID;
                        $GRVDetail_arr['financeGLcodePL'] = $financeGLcodePL;
                        
                    
                        $GRVDetail_arr['itemFinanceCategoryID'] = $new['itemFinanceCategoryID'];
                        $GRVDetail_arr['itemFinanceCategorySubID'] = $new['itemFinanceCategorySubID'];
                        $GRVDetail_arr['includePLForGRVYN'] = $new['includePLForGRVYN'];
                        $GRVDetail_arr['supplierPartNumber'] = $new['supplierPartNumber'];
                        $GRVDetail_arr['unitOfMeasure'] = $new['unitOfMeasure'];
                        $GRVDetail_arr['noQty'] = $new['noQty'];
                        $GRVDetail_arr['wasteQty'] = 0;

                        $itemMaster = ItemMaster::find($new['itemCode']);

                        $GRVDetail_arr['trackingType'] = (isset($itemMaster->trackingType)) ? $itemMaster->trackingType : null;
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
                        $GRVDetail_arr['vatMasterCategoryID'] = $new['vatMasterCategoryID'];
                        $GRVDetail_arr['vatSubCategoryID'] = $new['vatSubCategoryID'];
                        $GRVDetail_arr['exempt_vat_portion'] = $new['exempt_vat_portion'];
                        $GRVDetail_arr['logisticsAvailable'] = $POMaster->logisticsAvailable;
                        $GRVDetail_arr['binNumber'] = $warehouseItem ? $warehouseItem->binNumber : 0;

                        $GRVDetail_arr['createdPcID'] = gethostname();
                        $GRVDetail_arr['createdUserID'] = $user->employee['empID'];
                        $GRVDetail_arr['createdUserSystemID'] = $user->employee['employeeSystemID'];

                        $mp = isset($new['markupPercentage'])?$new['markupPercentage']:0;
                        $markupArray = $this->setMarkupPercentage($GRVDetail_arr['unitCost'],$GRVMaster,$mp);

                        $GRVDetail_arr['markupPercentage'] = $markupArray['markupPercentage'];
                        $GRVDetail_arr['markupTransactionAmount'] = $markupArray['markupTransactionAmount'];
                        $GRVDetail_arr['markupLocalAmount'] = $markupArray['markupLocalAmount'];
                        $GRVDetail_arr['markupReportingAmount'] = $markupArray['markupReportingAmount'];

                        $item = $this->gRVDetailsRepository->create($GRVDetail_arr);

                        $update = PurchaseOrderDetails::where('purchaseOrderDetailsID', $new['purchaseOrderDetailsID'])
                            ->update(['GRVSelectedYN' => $GRVSelectedYN, 'goodsRecievedYN' => $goodsRecievedYN, 'receivedQty' => $totalAddedQty]);


                        $this->checkPrnAndUpdateAsReturnedUsed($new['purchaseOrderDetailsID'], $new['noQty'], $item->grvDetailsID);
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


            // DB::rollBack();
            // return $this->sendError('Error Occurred');
            
            $updateGrvMaster = GRVMaster::where('grvAutoID', $grvAutoID)
                                        ->update(['pullType' => 1]);


            DB::commit();
                if(isset($grvPrimaryCode) && $grvPrimaryCode != null){
                    return $this->sendResponse('', 'GRV created successfully ' . $grvPrimaryCode);
                } else {
                    return $this->sendResponse('', 'GRV details saved successfully');
                }
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError('Error Occurred'. $exception->getMessage() . 'Line :' . $exception->getLine());
        }

    }


    public function checkPrnAndUpdateAsReturnedUsed($purchaseOrderDetailsID, $newGrvQty, $grvDetailsID)
    {
        $getGrvDetails = GRVDetails::with(['prn_details' => function($query) {
                                        $query->whereRaw('noQty - receivedQty != ?', [0]);
                                   }])
                                   ->whereHas('prn_details', function($query) {
                                        $query->whereRaw('noQty - receivedQty != ?', [0]);
                                   })
                                   ->where('returnQty', '>', 0)
                                   ->where('purchaseOrderDetailsID', $purchaseOrderDetailsID)
                                   ->get();

        foreach ($getGrvDetails as $key => $value) {
            foreach ($value->prn_details as $key1 => $value1) {
                if ($newGrvQty > 0) {
                    if (($value1->noQty - $value1->receivedQty) > $newGrvQty) {
                        $receivedQty = $newGrvQty;

                        $newGrvQty = 0;
                    } else {
                        $receivedQty = $value1->noQty - $value1->receivedQty;

                        $newGrvQty = $newGrvQty - $receivedQty;
                    }


                    if ($value1->noQty == $receivedQty) {
                        $goodsRecievedYN = 2;
                        $GRVSelectedYN = 1;
                    } else {
                        $goodsRecievedYN = 1;
                        $GRVSelectedYN = 0;
                    }


                    $update = PurchaseReturnDetails::where('purhasereturnDetailID', $value1->purhasereturnDetailID)
                            ->update(['GRVSelectedYN' => $GRVSelectedYN, 'goodsRecievedYN' => $goodsRecievedYN, 'receivedQty' => $receivedQty]);

                    $grvDetailsPrnData = [
                        'grvDetailsID' => $grvDetailsID,
                        'purhasereturnDetailID' => $value1->purhasereturnDetailID,
                        'prnQty' => $receivedQty
                    ];

                    $createRespo = GrvDetailsPrn::insert($grvDetailsPrnData);
                    
                    $this->checkPurchaseReturnAndUpdateReturnStatus($value1->purhaseReturnAutoID);
                }
            }
        }
    }

    public function checkPurchaseReturnAndUpdateReturnStatus($purhaseReturnAutoID)
    {
        // fetching the total count records from purchase Request Details table
        $purchaseOrderDetailTotalAmount = PurchaseReturnDetails::select(DB::raw('SUM(noQty) as detailQty,SUM(receivedQty) as receivedQty'))
                                                                ->where('purhaseReturnAutoID', $purhaseReturnAutoID)
                                                                ->first();

        // Updating PO Master Table After All Detail Table records updated
        if ($purchaseOrderDetailTotalAmount['detailQty'] == $purchaseOrderDetailTotalAmount['receivedQty']) {
            $updatePO = PurchaseReturn::find($purhaseReturnAutoID)
                ->update(['prClosedYN' => 1, 'grvRecieved' => 2]);
        } else {
            $updatePO = PurchaseReturn::find($purhaseReturnAutoID)
                ->update(['prClosedYN' => 0, 'grvRecieved' => 1]);
        }
    }


    public function storeGRVDetailsDirect(Request $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToValue($input);

        $grvAutoID = $input['grvAutoID'];

        $grvTypeID = $input['grvTypeID'];

        $companySystemID = $input['companySystemID'];

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
            $itemAssign = ItemAssigned::with(['item_master'])->find($input['itemCode']);

            if (empty($itemAssign)) {
                return $this->sendError('Item not assigned');
            }

            $allowFinanceCategory = CompanyPolicyMaster::where('companyPolicyCategoryID', 20)
                ->where('companySystemID', $grvMaster->companySystemID)
                ->first();

            if ($allowFinanceCategory) {
                $policy = $allowFinanceCategory->isYesNO;

                if ($policy == 0) {
                    //checking if item category is same or not
                    $grvDetailExistSameItem = GRVDetails::select(DB::raw('DISTINCT(itemFinanceCategoryID) as itemFinanceCategoryID'))
                        ->where('grvAutoID', $grvAutoID)
                        ->first();

                    if ($grvDetailExistSameItem) {
                        if ($itemAssign->financeCategoryMaster != $grvDetailExistSameItem["itemFinanceCategoryID"]) {
                            return $this->sendError('You cannot add different category item', 422);
                        }
                    }
                }
            }

            $user = \Helper::getEmployeeInfo();
            
            $item = ItemAssigned::where('itemCodeSystem', $itemAssign->itemCodeSystem)
                ->where('companySystemID', $companySystemID)
                ->first();
            //checking if item is inventory item cannot be added more than one
            $grvDetailExistSameItem = GRVDetails::select(DB::raw('itemCode'))
                ->where('grvAutoID', $grvAutoID)
                ->where('itemCode', $itemAssign->itemCodeSystem)
                ->first();


                   if($grvTypeID == 1) {
                       if ($item->financeCategoryMaster == 1) {
                           if ($grvDetailExistSameItem) {
                               return $this->sendError('Selected item is already added from the same grv.', 422);
                           }
                       }
                   }
              if($grvTypeID != 1) {
                if ($grvDetailExistSameItem) {
                    return $this->sendError('Selected item is already added from the same grv.', 422);
                }
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
            $GRVDetail_arr['trackingType'] = (isset($itemAssign->item_master->trackingType)) ? $itemAssign->item_master->trackingType : null;
            $GRVDetail_arr['itemPrimaryCode'] = $itemAssign->itemPrimaryCode;
            $GRVDetail_arr['itemDescription'] = $itemAssign->itemDescription;
            $GRVDetail_arr['itemFinanceCategoryID'] = $itemAssign->financeCategoryMaster;
            $GRVDetail_arr['itemFinanceCategorySubID'] = $itemAssign->financeCategorySub;
            
            // $GRVDetail_arr['financeGLcodebBSSystemID'] = $financeCategorySub->financeGLcodebBSSystemID;
            // $GRVDetail_arr['financeGLcodebBS'] = $financeCategorySub->financeGLcodebBS;
            // $GRVDetail_arr['financeGLcodePLSystemID'] = $financeCategorySub->financeGLcodePLSystemID;
            // $GRVDetail_arr['financeGLcodePL'] = $financeCategorySub->financeGLcodePL;

            //return WarehouseMaster::checkManuefactoringWareHouse($grvMaster->grvLocation);

           
            // if ($itemAssign->financeCategoryMaster == 1 && WarehouseMaster::checkManuefactoringWareHouse($grvMaster->grvLocation)) // check inventory and manufacturing
            // {
            //     if($financeCategorySub->includePLForGRVYN == -1)
            //     {
            //         $GRVDetail_arr['financeGLcodePLSystemID'] = WarehouseMaster::getWIPGLSystemID($grvMaster->grvLocation);
            //         $GRVDetail_arr['financeGLcodePL'] = WarehouseMaster::getWIPGLCode($grvMaster->grvLocation);
            //         $GRVDetail_arr['financeGLcodebBSSystemID'] = null;
            //         $GRVDetail_arr['financeGLcodebBS'] = null;
            //     }
            //     else
            //     {
            //         $GRVDetail_arr['financeGLcodebBSSystemID'] = WarehouseMaster::getWIPGLSystemID($grvMaster->grvLocation);
            //         $GRVDetail_arr['financeGLcodebBS'] = WarehouseMaster::getWIPGLCode($grvMaster->grvLocation);
            //         $GRVDetail_arr['financeGLcodePLSystemID'] = $financeCategorySub->financeGLcodePLSystemID;
            //         $GRVDetail_arr['financeGLcodePL'] = $financeCategorySub->financeGLcodePL;
            //     }
                
    
            // } else {
          
                $GRVDetail_arr['financeGLcodebBSSystemID'] = $financeCategorySub->financeGLcodebBSSystemID;
                $GRVDetail_arr['financeGLcodebBS'] = $financeCategorySub->financeGLcodebBS;
                $GRVDetail_arr['financeGLcodePLSystemID'] = $financeCategorySub->financeGLcodePLSystemID;
                $GRVDetail_arr['financeGLcodePL'] = $financeCategorySub->financeGLcodePL;
          //  }

        
        
            $GRVDetail_arr['includePLForGRVYN'] = $financeCategorySub->includePLForGRVYN;
            $GRVDetail_arr['supplierPartNumber'] = $itemAssign->secondaryItemCode;
            $GRVDetail_arr['unitOfMeasure'] = $itemAssign->itemUnitOfMeasure;
            $GRVDetail_arr['wasteQty'] = $input['wasteQty'];
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

            $GRVDetail_arr['VATAmount'] = 0;
            if ($grvMaster->vatRegisteredYN) {
                $vatDetails = TaxService::getVATDetailsByItem($grvMaster->companySystemID, $GRVDetail_arr['itemCode'], $grvMaster->supplierID);
                $GRVDetail_arr['VATPercentage'] = $vatDetails['percentage'];
                $GRVDetail_arr['vatMasterCategoryID'] = $vatDetails['vatMasterCategoryID'];
                $GRVDetail_arr['vatSubCategoryID'] = $vatDetails['vatSubCategoryID'];
                $GRVDetail_arr['VATAmount'] = 0;
                if ($GRVDetail_arr['unitCost'] > 0) {
                    $GRVDetail_arr['VATAmount'] = (($GRVDetail_arr['unitCost'] / 100) * $vatDetails['percentage']);
                }
                $prDetail_arr['netAmount'] = ($GRVDetail_arr['unitCost'] + $GRVDetail_arr['VATAmount']) * $GRVDetail_arr['noQty'];
                $currencyConversionVAT = \Helper::currencyConversion($grvMaster->companySystemID, $grvMaster->supplierTransactionCurrencyID, $grvMaster->supplierTransactionCurrencyID, $GRVDetail_arr['VATAmount']);

                $GRVDetail_arr['VATAmount'] = 0;
                $GRVDetail_arr['VATAmountLocal'] = 0;
                $GRVDetail_arr['VATAmountRpt'] = 0;
            }


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
        $input = array_except($input, ['unit', 'po_master', 'item_by', 'vat_sub_category']);
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
            $itemAssign = ItemMaster::find($input['itemCode']);
            if (empty($itemAssign)) {
                return $this->sendError('Item not found');
            }

            $validateVATCategories = TaxService::validateVatCategoriesInDocumentDetails($grvMaster->documentSystemID, $grvMaster->companySystemID, $id, $input);

            if (!$validateVATCategories['status']) {
                return $this->sendError($validateVATCategories['message'], 500,array('type' => 'no_qty_issues'));
            } else {
                $GRVDetail_arr['vatMasterCategoryID'] = $validateVATCategories['vatMasterCategoryID'];        
                $GRVDetail_arr['vatSubCategoryID'] = $validateVATCategories['vatSubCategoryID'];        
            }

            if (isset($GRVDetail_arr['vatSubCategoryID']) && $GRVDetail_arr['vatSubCategoryID'] > 0) {
                $subcategoryVAT = TaxVatCategories::find($GRVDetail_arr['vatSubCategoryID']);
                $GRVDetail_arr['exempt_vat_portion'] = (isset($input['exempt_vat_portion']) && $subcategoryVAT && $subcategoryVAT->subCatgeoryType == 1) ? $input['exempt_vat_portion'] : 0;
            }

            $input['VATAmount'] = isset($input['VATAmount']) ? $input['VATAmount'] : 0;
            $GRVDetail_arr['VATPercentage'] = isset($input['VATPercentage']) ? $input['VATPercentage'] : 0;

            if (isset($input['VATAmount']) && $input['VATAmount'] > 0) {
                $currencyConversionVAT = \Helper::currencyConversion($grvMaster->companySystemID, $grvMaster->supplierTransactionCurrencyID, $grvMaster->supplierTransactionCurrencyID, $input['VATAmount']);
                $GRVDetail_arr['VATAmountLocal'] = \Helper::roundValue($currencyConversionVAT['localAmount']);
                $GRVDetail_arr['VATAmountRpt'] = \Helper::roundValue($currencyConversionVAT['reportingAmount']);
                $GRVDetail_arr['VATAmount'] = \Helper::roundValue($input['VATAmount']);
            } else {
                $GRVDetail_arr['VATAmount'] = 0;
                $GRVDetail_arr['VATAmountLocal'] = 0;
                $GRVDetail_arr['VATAmountRpt'] = 0;
            }


            $user = \Helper::getEmployeeInfo();
            $financeCategorySub = FinanceItemCategorySub::find($itemAssign->financeCategorySub);

            // checking the qty request is matching with sum total
            $GRVDetail_arr['grvAutoID'] = $grvAutoID;
            $GRVDetail_arr['noQty'] = $input['noQty'];
            $GRVDetail_arr['wasteQty'] = $input['wasteQty'];
            $totalNetcost = (floatval($input['unitCost']) + floatval($input['VATAmount'])) * $input['noQty'];
            $GRVDetail_arr['unitCost'] = $input['unitCost'];
            $GRVDetail_arr['netAmount'] = $totalNetcost;
            $GRVDetail_arr['comment'] = $input['comment'];

            $calculateItemDiscount = $input['unitCost'];
            if (!$grvMaster->vatRegisteredYN) {
                $calculateItemDiscount = $input['unitCost'];
            } else {
                $checkVATCategory = TaxVatCategories::with(['type'])->find($GRVDetail_arr['vatSubCategoryID']);
                if ($checkVATCategory) {
                    if (isset($checkVATCategory->type->id) && $checkVATCategory->type->id == 1 && $GRVDetail_arr['exempt_vat_portion'] > 0 && $GRVDetail_arr['VATAmount'] > 0) {
                       $exemptVAT = $GRVDetail_arr['VATAmount'] * ($GRVDetail_arr['exempt_vat_portion'] / 100);

                       $calculateItemDiscount = $calculateItemDiscount + $exemptVAT;
                    } else if (isset($checkVATCategory->type->id) && $checkVATCategory->type->id == 3) {
                        $calculateItemDiscount = $calculateItemDiscount + $GRVDetail_arr['VATAmount'];
                    }
                }
            }

            $currency = \Helper::convertAmountToLocalRpt($grvMaster->documentSystemID,$grvAutoID,$calculateItemDiscount);

            $GRVDetail_arr['GRVcostPerUnitLocalCur'] = \Helper::roundValue($currency['localAmount']);
            $GRVDetail_arr['GRVcostPerUnitSupDefaultCur'] = \Helper::roundValue($currency['defaultAmount']);
            $GRVDetail_arr['GRVcostPerUnitSupTransCur'] = \Helper::roundValue($calculateItemDiscount);
            $GRVDetail_arr['GRVcostPerUnitComRptCur'] = \Helper::roundValue($currency['reportingAmount']);
            
            $GRVDetail_arr['landingCost_LocalCur'] = \Helper::roundValue($currency['localAmount']);
            $GRVDetail_arr['landingCost_TransCur'] = \Helper::roundValue($calculateItemDiscount);
            $GRVDetail_arr['landingCost_RptCur'] = \Helper::roundValue($currency['reportingAmount']);
            $GRVDetail_arr['modifiedPc'] = gethostname();
            $GRVDetail_arr['modifiedUser'] = $user->empID;
            $GRVDetail_arr['detail_project_id'] = $input['detail_project_id'];


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

        $grvMasterData = GRVMaster::find($grvAutoID);

        if (!$grvMasterData) {
            return $this->sendError('GRV Master not found');
        }

        // check logistic item exist
        $logisticItems = PoAdvancePayment::where('grvAutoID', $grvAutoID)
            ->where('confirmedYN', 1)
            ->where('approvedYN', -1)
            ->count();

        if($logisticItems){
            return $this->sendError('GRV details cannot be deleted as this GRV is linked with logistics. Unlink the logistic data and try again.',500);
        }

        $this->expenseAssetAllocationRepo->deleteExpenseAssetAllocation($grvAutoID, $grvMasterData->documentSystemID);

        if (!empty($detailExistAll)) {
            foreach ($detailExistAll as $cvDeatil) {
                $deleteDetails = GRVDetails::where('grvDetailsID', $cvDeatil['grvDetailsID'])->delete();

                if ($grvMasterData->pullType == 2) {
                    if (!empty($cvDeatil['purhasereturnDetailID']) && !empty($cvDeatil['purhaseReturnAutoID'])) {
                        $detailExistPODetail = PurchaseReturnDetails::find($cvDeatil->purhasereturnDetailID);
                        // get the total received qty for a specific item
                        $detailPOSUM = GRVDetails::WHERE('purhaseReturnAutoID', $cvDeatil['purhaseReturnAutoID'])->WHERE('companySystemID', $cvDeatil['companySystemID'])->WHERE('purhasereturnDetailID', $cvDeatil['purhasereturnDetailID'])->sum('noQty');
                        // get the total received qty
                        $masterPOSUM = GRVDetails::WHERE('purhaseReturnAutoID', $cvDeatil['purhaseReturnAutoID'])->WHERE('companySystemID', $cvDeatil['companySystemID'])->sum('noQty');
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

                        $updateDetail = PurchaseReturnDetails::where('purhasereturnDetailID', $detailExistPODetail->purhasereturnDetailID)
                            ->update(['GRVSelectedYN' => $GRVSelectedYN, 'goodsRecievedYN' => $goodsRecievedYN, 'receivedQty' => $receivedQty]);

                        if ($masterPOSUM > 0) {
                            $updatePO = PurchaseReturn::find($cvDeatil['purhaseReturnAutoID'])
                                ->update(['prClosedYN' => 0, 'grvRecieved' => 1]);
                        } else {
                            $updatePO = PurchaseReturn::find($cvDeatil['purhaseReturnAutoID'])
                                ->update(['prClosedYN' => 0, 'grvRecieved' => 0]);
                        }
                    }
                } else {
                    if (!empty($cvDeatil['purchaseOrderDetailsID']) && !empty($cvDeatil['purchaseOrderMastertID'])) {
                        $detailExistPODetail = PurchaseOrderDetails::find($cvDeatil->purchaseOrderDetailsID);
                        // get the total received qty for a specific item
                        $detailPOSUM = GRVDetails::selectRaw('SUM(noQty - returnQty) as newNoQty')
                                                 ->WHERE('purchaseOrderMastertID', $cvDeatil['purchaseOrderMastertID'])
                                                 ->whereHas('grv_master', function($query) {
                                                    $query->where('grvCancelledYN', '!=', -1);
                                                 })
                                                 ->WHERE('companySystemID', $cvDeatil['companySystemID'])
                                                 ->WHERE('purchaseOrderDetailsID', $cvDeatil['purchaseOrderDetailsID'])
                                                 ->first();
                        // get the total received qty
                        $masterPOSUM = GRVDetails::selectRaw('SUM(noQty - returnQty) as newNoQty')
                                                  ->WHERE('purchaseOrderMastertID', $cvDeatil['purchaseOrderMastertID'])
                                                  ->WHERE('companySystemID', $cvDeatil['companySystemID'])
                                                   ->whereHas('grv_master', function($query) {
                                                    $query->where('grvCancelledYN', '!=', -1);
                                                 })
                                                  ->first();

                        $poQty = $detailExistPODetail->receivedQty - $cvDeatil->noQty;

                        $receivedQty = 0;
                        $goodsRecievedYN = 0;
                        $GRVSelectedYN = 0;
                        if ($detailPOSUM->newNoQty > 0) {
                            $receivedQty = $detailPOSUM->newNoQty;
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

                        if ($masterPOSUM->newNoQty > 0) {
                            $updatePO = ProcumentOrder::find($cvDeatil['purchaseOrderMastertID'])
                                ->update(['poClosedYN' => 0, 'grvRecieved' => 1]);
                        } else {
                            $updatePO = ProcumentOrder::find($cvDeatil['purchaseOrderMastertID'])
                                ->update(['poClosedYN' => 0, 'grvRecieved' => 0]);
                        }
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

    public function setMarkupPercentage($unitCost, $grvData , $markupPercentage=0, $markupTransAmount=0, $by = ''){
        $output['markupPercentage'] = 0;
        $output['markupTransactionAmount'] = 0;
        $output['markupLocalAmount'] = 0;
        $output['markupReportingAmount'] = 0;

        $markupAmendRestrictionPolicy = Helper::checkRestrictionByPolicy($grvData->companySystemID,6);

        if(isset($grvData->supplierID) && $grvData->supplierID && $markupAmendRestrictionPolicy){

            $supplier= SupplierAssigned::where('supplierCodeSytem',$grvData->supplierID)
                ->where('companySystemID',$grvData->companySystemID)
                ->where('isActive', 1)
                ->where('isAssigned', -1)
                ->first();

            if($supplier->companySystemID && $supplier->isMarkupPercentage){
                $hasEEOSSPolicy = CompanyPolicyMaster::where('companySystemID', $supplier->companySystemID)
                    ->where('companyPolicyCategoryID', 41)
                    ->where('isYesNO',1)
                    ->exists();

                if($hasEEOSSPolicy){

                    if($by == 'amount'){
                        $output['markupTransactionAmount'] = $markupTransAmount;
                        if($unitCost > 0 && $markupTransAmount > 0){
                            $output['markupPercentage'] = $markupTransAmount*100/$unitCost;
                        }
                    }else{
                        $percentage = ($markupPercentage != 0)? $markupPercentage:$supplier->markupPercentage;
                        if ($by == 'percentage'){
                            $percentage = $markupPercentage;
                            $output['markupPercentage'] = $percentage;
                        }
                        if($percentage != 0){
                            $output['markupPercentage'] = $percentage;
                            if($unitCost>0){

                                $output['markupTransactionAmount'] = $percentage*$unitCost/100;
                            }
                        }
                    }

                    if($output['markupTransactionAmount']>0){

                        if($grvData->supplierDefaultCurrencyID != $grvData->localCurrencyID){
                            $currencyConversion = Helper::currencyConversion($grvData->companySystemID,$grvData->supplierDefaultCurrencyID,$grvData->localCurrencyID,$output['markupTransactionAmount']);
                            if(!empty($currencyConversion)){
                                $output['markupLocalAmount'] = $currencyConversion['documentAmount'];
                            }
                        }else{
                            $output['markupLocalAmount'] = $output['markupTransactionAmount'];
                        }

                        if($grvData->supplierDefaultCurrencyID != $grvData->companyReportingCurrencyID){
                            $currencyConversion = Helper::currencyConversion($grvData->companySystemID,$grvData->supplierDefaultCurrencyID,$grvData->companyReportingCurrencyID,$output['markupTransactionAmount']);
                            if(!empty($currencyConversion)){
                                $output['markupReportingAmount'] = $currencyConversion['documentAmount'];
                            }
                        }else{
                            $output['markupReportingAmount'] =$output['markupTransactionAmount'];
                        }

                        /*round to 7 decimals*/
                        $output['markupTransactionAmount'] = Helper::roundValue($output['markupTransactionAmount']);
                        $output['markupLocalAmount'] = Helper::roundValue($output['markupLocalAmount']);
                        $output['markupReportingAmount'] = Helper::roundValue($output['markupReportingAmount']);

                    }

                }

            }

        }

        return $output;
    }

    public function grvMarkupUpdate(Request $request){
        $input = $request->all();
        $markupUpdatedBy=isset($input['by'])?$input['by']:'';
        $companyId=isset($input['companyId'])?$input['companyId']:'';
        $grvMaster = GRVMaster::find($input['grvAutoID']);

        if (empty($grvMaster)) {
            return $this->sendError('GRV not found');
        }

        if ($grvMaster->isMarkupUpdated==1) {
            return $this->sendError('GRV markup update process restricted',500);
        }

        $markupAmendRestrictionPolicy = Helper::checkRestrictionByPolicy($companyId,6);
        if(!$markupAmendRestrictionPolicy){
            return $this->sendError('Document already confirmed. You cannot update.');
        }

        $markupArray = $this->setMarkupPercentage($input['unitCost'],$grvMaster,$input['markupPercentage'],$input['markupTransactionAmount'],$markupUpdatedBy);
        $GRVDetail_arr['markupPercentage'] = $markupArray['markupPercentage'];
        $GRVDetail_arr['markupTransactionAmount'] = $markupArray['markupTransactionAmount'];
        $GRVDetail_arr['markupLocalAmount'] = $markupArray['markupLocalAmount'];
        $GRVDetail_arr['markupReportingAmount'] = $markupArray['markupReportingAmount'];

        $gRVDetails = $this->gRVDetailsRepository->update($GRVDetail_arr, $input['grvDetailsID']);

        return $this->sendResponse($gRVDetails, 'GRV markup details updated successfully');
    }


    public function storeGRVDetailsFromPR(Request $request)
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

        $PRMaster = PurchaseReturn::find($input['purhaseReturnAutoID']);

        $size = array_column($input['detailTable'], 'isChecked');
        $frontDetailcount = count(array_filter($size));

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
                                                        ->where('purhasereturnDetailID', $new['purhasereturnDetailID'])
                                                        ->whereHas('grv_master',function ($q) use ($input) {
                                                            $q->where('approved', 0);
                                                        })
                                                        ->first();

                    if($alreadyItemPulledCheck){
                        return $this->sendError('Cannot proceed. Selected item is already added in '.$alreadyItemPulledCheck->grv_master->grvPrimaryCode.' and it is not approved yet', 422);
                    }


                    $grvDetailItem = PurchaseReturnDetails::select('receivedQty')
                                                        ->where('purhasereturnDetailID', $new['purhasereturnDetailID'])
                                                        ->first();

                    $new['receivedQty'] = $grvDetailItem['receivedQty'];

                    if (($new['noQty'] == '' || $new['noQty'] == 0)) {
                        return $this->sendError('Qty cannot be zero', 422);
                    } else {
                        // if ($allowPartialGRVPolicy->isYesNO == 0 && $PRMaster->partiallyGRVAllowed == 0) {
                            // pre check for all items qty pulled
                            // if ($new['isChecked'] && ((float)$new['noQty'] != ($new['prnQty'] - (float)$new['receivedQty']))) {
                            //     return $this->sendError('Full order quantity should be received', 422);
                            // }
                        // }
                    }


                    if ($new['noQty'] > ($new['prnQty'] - $new['receivedQty'])) {
                        return $this->sendError('Number of quantity should not be greater than received qty', 422);
                    }

                    // if ($allowMultiplePO->isYesNO == 0) {
                    //     $grvDetailExistSameItem = GRVDetails::select(DB::raw('purchaseOrderMastertID'))
                    //         ->where('grvAutoID', $grvAutoID)
                    //         ->first();

                    //     if (!empty($grvDetailExistSameItem)) {
                    //         if ($grvDetailExistSameItem['purchaseOrderMastertID'] != $new['purchaseOrderMasterID']) {
                    //             return $this->sendError('You cannot add details from multiple PO', 422);
                    //         }
                    //     }
                    // }

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
                                                        ->where('purhasereturnDetailID', $new['purhasereturnDetailID'])
                                                        ->first();

                    if ($grvDetailExistSameItem) {
                        return $this->sendError('Selected item is already added from the same purchase return.', 422);
                    }

                    $totalAddedQty = $new['noQty'] + $new['receivedQty'];

                    if ($new['prnQty'] == $totalAddedQty) {
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

                    $grvDetailsOfPrDetail = GRVDetails::find($new['grvDetailsID']);

                    if (!$grvDetailsOfPrDetail) {
                        return $this->sendError('GRV Details Of PR Detail not found.', 422);
                    }


                    $grvMasterOfPrDetail = GRVMaster::find($new['grvAutoID']);

                    if (!$grvMasterOfPrDetail) {
                        return $this->sendError('GRV Master Of PR Detail not found.', 422);
                    }

                    // checking the qty request is matching with sum total
                    if ($new['prnQty'] >= $new['noQty']) {
                        $GRVDetail_arr['grvAutoID'] = $grvAutoID;
                        $GRVDetail_arr['companySystemID'] = $PRMaster->companySystemID;
                        $GRVDetail_arr['companyID'] = $PRMaster->companyID;
                        $GRVDetail_arr['serviceLineCode'] = $PRMaster->serviceLineCode;
                        $GRVDetail_arr['purhaseReturnAutoID'] = $new['purhaseReturnAutoID'];
                        $GRVDetail_arr['purhasereturnDetailID'] = $new['purhasereturnDetailID'];
                        $GRVDetail_arr['itemCode'] = $new['itemCode'];

                        $itemMaster = ItemMaster::find($new['itemCode']);
                        $GRVDetail_arr['trackingType'] = (isset($itemMaster->trackingType)) ? $itemMaster->trackingType : null;
                        
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
                        $GRVDetail_arr['wasteQty'] = 0;
                        $GRVDetail_arr['prvRecievedQty'] = $new['receivedQty'];
                        $GRVDetail_arr['poQty'] = $new['prnQty'];
                        $totalNetcost = $new['GRVcostPerUnitSupTransCur'] * $new['noQty'];
                        $GRVDetail_arr['unitCost'] = $new['GRVcostPerUnitSupTransCur'];
                        $GRVDetail_arr['discountPercentage'] = $grvDetailsOfPrDetail->discountPercentage;
                        $GRVDetail_arr['discountAmount'] = $grvDetailsOfPrDetail->discountAmount;
                        $GRVDetail_arr['netAmount'] = $totalNetcost;
                        $GRVDetail_arr['comment'] = $new['comment'];
                        $GRVDetail_arr['supplierDefaultCurrencyID'] = $new['supplierDefaultCurrencyID'];
                        $GRVDetail_arr['supplierDefaultER'] = $new['supplierDefaultER'];
                        $GRVDetail_arr['supplierItemCurrencyID'] = $grvDetailsOfPrDetail->supplierItemCurrencyID;
                        $GRVDetail_arr['foreignToLocalER'] = $grvDetailsOfPrDetail->foreignToLocalER;
                        $GRVDetail_arr['purchaseOrderMastertID'] = $grvDetailsOfPrDetail->purchaseOrderMastertID;
                        $GRVDetail_arr['purchaseOrderDetailsID'] = $grvDetailsOfPrDetail->purchaseOrderDetailsID;
                        $GRVDetail_arr['companyReportingCurrencyID'] = $new['companyReportingCurrencyID'];
                        $GRVDetail_arr['companyReportingER'] = $new['companyReportingER'];
                        $GRVDetail_arr['localCurrencyID'] = $new['localCurrencyID'];
                        $GRVDetail_arr['localCurrencyER'] = $new['localCurrencyER'];
                        $GRVDetail_arr['addonDistCost'] = $grvDetailsOfPrDetail->addonDistCost;
                        $GRVDetail_arr['GRVcostPerUnitLocalCur'] = $new['GRVcostPerUnitLocalCur'];
                        $GRVDetail_arr['GRVcostPerUnitSupDefaultCur'] = $new['GRVcostPerUnitSupDefaultCur'];
                        $GRVDetail_arr['GRVcostPerUnitSupTransCur'] = $new['GRVcostPerUnitSupTransCur'];
                        $GRVDetail_arr['GRVcostPerUnitComRptCur'] = $new['GRVcostPerUnitComRptCur'];

                        $GRVDetail_arr['landingCost_LocalCur'] = $new['GRVcostPerUnitLocalCur'];
                        $GRVDetail_arr['landingCost_TransCur'] = $new['GRVcostPerUnitSupTransCur'];
                        $GRVDetail_arr['landingCost_RptCur'] = $new['GRVcostPerUnitComRptCur'];

                        $GRVDetail_arr['vatRegisteredYN'] = $grvMasterOfPrDetail->vatRegisteredYN;
                        $GRVDetail_arr['supplierVATEligible'] = $grvMasterOfPrDetail->supplierVATEligible;
                        $GRVDetail_arr['VATPercentage'] = $grvDetailsOfPrDetail->VATPercentage;
                        $GRVDetail_arr['VATAmount'] = $grvDetailsOfPrDetail->VATAmount;
                        $GRVDetail_arr['VATAmountLocal'] = $grvDetailsOfPrDetail->VATAmountLocal;
                        $GRVDetail_arr['VATAmountRpt'] = $grvDetailsOfPrDetail->VATAmountRpt;
                        $GRVDetail_arr['logisticsAvailable'] = $grvMasterOfPrDetail->logisticsAvailable;
                        $GRVDetail_arr['binNumber'] = $warehouseItem ? $warehouseItem->binNumber : 0;

                        $GRVDetail_arr['createdPcID'] = gethostname();
                        $GRVDetail_arr['createdUserID'] = $user->employee['empID'];
                        $GRVDetail_arr['createdUserSystemID'] = $user->employee['employeeSystemID'];

                        $mp = isset($new['markupPercentage'])?$new['markupPercentage']:0;
                        $markupArray = $this->setMarkupPercentage($GRVDetail_arr['unitCost'],$GRVMaster,$mp);

                        $GRVDetail_arr['markupPercentage'] = $markupArray['markupPercentage'];
                        $GRVDetail_arr['markupTransactionAmount'] = $markupArray['markupTransactionAmount'];
                        $GRVDetail_arr['markupLocalAmount'] = $markupArray['markupLocalAmount'];
                        $GRVDetail_arr['markupReportingAmount'] = $markupArray['markupReportingAmount'];

                        $item = $this->gRVDetailsRepository->create($GRVDetail_arr);

                        $update = PurchaseReturnDetails::where('purhasereturnDetailID', $new['purhasereturnDetailID'])
                            ->update(['GRVSelectedYN' => $GRVSelectedYN, 'goodsRecievedYN' => $goodsRecievedYN, 'receivedQty' => $totalAddedQty]);
                    }
                }

                // fetching the total count records from purchase Request Details table
                $purchaseOrderDetailTotalAmount = PurchaseReturnDetails::select(DB::raw('SUM(noQty) as detailQty,SUM(receivedQty) as receivedQty'))
                                                                        ->where('purhaseReturnAutoID', $new['purhaseReturnAutoID'])
                                                                        ->first();

                // Updating PO Master Table After All Detail Table records updated
                if ($purchaseOrderDetailTotalAmount['detailQty'] == $purchaseOrderDetailTotalAmount['receivedQty']) {
                    $updatePO = PurchaseReturn::find($new['purhaseReturnAutoID'])
                        ->update(['prClosedYN' => 1, 'grvRecieved' => 2]);
                } else {
                    $updatePO = PurchaseReturn::find($new['purhaseReturnAutoID'])
                        ->update(['prClosedYN' => 0, 'grvRecieved' => 1]);
                }

            }

            $updateGrvMaster = GRVMaster::where('grvAutoID', $grvAutoID)
                                        ->update(['pullType' => 2]);

            DB::commit();
            return $this->sendResponse('', 'GRV details saved successfully');
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError('Error Occurred'. $exception->getMessage() . 'Line :' . $exception->getLine());
        }

    }
}
