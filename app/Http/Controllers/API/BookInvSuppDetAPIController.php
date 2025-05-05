<?php
/**
 * =============================================
 * -- File Name : BookInvSuppDetAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  BookInvSuppDet
 * -- Author : Mohamed Nazir
 * -- Create date : 08 - August 2018
 * -- Description : This file contains the all CRUD for Purchase Order
 * -- REVISION HISTORY
 * -- Date: 10-September 2018 By: Nazir Description: Added new functions named as storePOBaseDetail(),
 * -- Date: 10-September 2018 By: Nazir Description: Added new functions named as getSupplierInvoiceGRVItems(),
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateBookInvSuppDetAPIRequest;
use App\Http\Requests\API\UpdateBookInvSuppDetAPIRequest;
use App\Models\BookInvSuppDet;
use App\helper\CurrencyConversionService;
use App\Models\BookInvSuppMaster;
use App\Models\SupplierAssigned;
use App\Models\Company;
use App\Models\GRVMaster;
use App\Models\CompanyPolicyMaster;
use App\Models\DirectInvoiceDetails;
use App\Models\GeneralLedger;
use App\Models\SupplierInvoiceItemDetail;
use App\Models\GRVDetails;
use App\Models\PoAdvancePayment;
use App\Models\ProcumentOrder;
use App\Models\UnbilledGrvGroupBy;
use App\Models\PurchaseReturnDetails;
use App\Models\PurchaseReturn;
use App\Repositories\BookInvSuppDetRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Illuminate\Support\Facades\DB;
use Response;
use App\helper\TaxService;
use App\Models\SupplierInvoiceDirectItem;
use App\Models\CurrencyMaster;
use App\helper\Helper;
/**
 * Class BookInvSuppDetController
 * @package App\Http\Controllers\API
 */
class BookInvSuppDetAPIController extends AppBaseController
{
    /** @var  BookInvSuppDetRepository */
    private $bookInvSuppDetRepository;

    public function __construct(BookInvSuppDetRepository $bookInvSuppDetRepo)
    {
        $this->bookInvSuppDetRepository = $bookInvSuppDetRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/bookInvSuppDets",
     *      summary="Get a listing of the BookInvSuppDets.",
     *      tags={"BookInvSuppDet"},
     *      description="Get all BookInvSuppDets",
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
     *                  @SWG\Items(ref="#/definitions/BookInvSuppDet")
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
        $this->bookInvSuppDetRepository->pushCriteria(new RequestCriteria($request));
        $this->bookInvSuppDetRepository->pushCriteria(new LimitOffsetCriteria($request));
        $bookInvSuppDets = $this->bookInvSuppDetRepository->all();

        return $this->sendResponse($bookInvSuppDets->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.supplier_invoice_details')]));
    }

    /**
     * @param CreateBookInvSuppDetAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/bookInvSuppDets",
     *      summary="Store a newly created BookInvSuppDet in storage",
     *      tags={"BookInvSuppDet"},
     *      description="Store BookInvSuppDet",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="BookInvSuppDet that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/BookInvSuppDet")
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
     *                  ref="#/definitions/BookInvSuppDet"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateBookInvSuppDetAPIRequest $request)
    {
        $input = $request->all();

        $bookInvSuppDets = $this->bookInvSuppDetRepository->create($input);

        return $this->sendResponse($bookInvSuppDets->toArray(), trans('custom.save', ['attribute' => trans('custom.supplier_invoice_details')]));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/bookInvSuppDets/{id}",
     *      summary="Display the specified BookInvSuppDet",
     *      tags={"BookInvSuppDet"},
     *      description="Get BookInvSuppDet",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of BookInvSuppDet",
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
     *                  ref="#/definitions/BookInvSuppDet"
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
        /** @var BookInvSuppDet $bookInvSuppDet */
        $bookInvSuppDet = $this->bookInvSuppDetRepository->findWithoutFail($id);

        if (empty($bookInvSuppDet)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.supplier_invoice_details')]));
        }

        return $this->sendResponse($bookInvSuppDet->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.supplier_invoice_details')]));
    }

    /**
     * @param int $id
     * @param UpdateBookInvSuppDetAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/bookInvSuppDets/{id}",
     *      summary="Update the specified BookInvSuppDet in storage",
     *      tags={"BookInvSuppDet"},
     *      description="Update BookInvSuppDet",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of BookInvSuppDet",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="BookInvSuppDet that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/BookInvSuppDet")
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
     *                  ref="#/definitions/BookInvSuppDet"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateBookInvSuppDetAPIRequest $request)
    {
        $input = array_except($request->all(), ['grvmaster', 'pomaster']);
        $input = $this->convertArrayToValue($input);

        $result = $this->updateDetail($input, $id);

        if ($result['status']) {
            return $this->sendResponse($result['data'], trans('custom.update', ['attribute' => trans('custom.book_inv_supp_det')]));
        } else {
            return $this->sendError($result['message'], 500);
        }
    }


    public function updateDetail($input, $id)
    {
        /** @var BookInvSuppDet $bookInvSuppDet */
        $bookInvSuppDet = $this->bookInvSuppDetRepository->findWithoutFail($id);

        if (empty($bookInvSuppDet)) {
            return ['status' => false, 'message' => trans('custom.not_found', ['attribute' => trans('custom.supplier_invoice_details')])];
        }

        if($bookInvSuppDet->suppinvmaster && $bookInvSuppDet->suppinvmaster->confirmedYN){
            return ['status' => false, 'message' => trans('custom.you_cannot_update_supplier_invoice_detail_this_document_already_confirmed')];
        }

        $unbilledGrvGroupByMaster = UnbilledGrvGroupBy::where('unbilledgrvAutoID', $bookInvSuppDet->unbilledgrvAutoID)
            ->first();

        if (empty($unbilledGrvGroupByMaster)) {
            return ['status' => false, 'message' => trans('custom.not_found', ['attribute' => trans('custom.supplier_invoice_details')])];
        }

        if ($input['supplierInvoAmount'] == "") {
            $input['supplierInvoAmount'] = 0;
        }

        $balanceAmount = collect(\DB::select('SELECT erp_bookinvsuppdet.unbilledgrvAutoID, Sum(erp_bookinvsuppdet.totTransactionAmount) AS SumOftotTransactionAmount FROM erp_bookinvsuppdet WHERE unbilledgrvAutoID = ' . $bookInvSuppDet->unbilledgrvAutoID . ' AND erp_bookinvsuppdet.bookingSupInvoiceDetAutoID != ' . $bookInvSuppDet->bookingSupInvoiceDetAutoID . ' GROUP BY erp_bookinvsuppdet.unbilledgrvAutoID;'))->first();


        $returnAmount = 0;

        if (!$unbilledGrvGroupByMaster->logisticYN) {
            $bookInvSuppMaster = BookInvSuppMaster::find($bookInvSuppDet->bookingSuppMasInvAutoID);

            $company = Company::where('companySystemID', $bookInvSuppMaster->companySystemID)->first();
            $supplierAssignedDetail = SupplierAssigned::where('supplierCodeSytem', $bookInvSuppMaster->supplierID)
                                                        ->where('companySystemID', $bookInvSuppMaster->companySystemID)
                                                        ->first();
            $valEligible = false;
            if ($company->vatRegisteredYN == 1 || $supplierAssignedDetail->vatEligible == 1) {
                $valEligible = true;
            }

            $rcmActivated = TaxService::isGRVRCMActivation($unbilledGrvGroupByMaster->grvAutoID);

            $grvDetailData = GRVDetails::where('grvAutoID', $unbilledGrvGroupByMaster->grvAutoID)
                                       ->get();

            $returnAmount = 0;
            if ($valEligible && !$rcmActivated) {
                foreach ($grvDetailData as $key => $value) {
                    $grvProcessData = TaxService::processGRVDetailVATForUnbilled($value->grvDetailsID);
                    $returnAmount += round((($grvProcessData['totalTransAmount'] / $value->noQty) * $value->returnQty), 7);
                }

            } else {
                foreach ($grvDetailData as $key => $value) {
                    $returnAmount += round(($value->GRVcostPerUnitSupTransCur * $value->returnQty), 7);
                }
            }
        }


        if ($balanceAmount) {
            $totalPendingAmount = ($unbilledGrvGroupByMaster->totTransactionAmount - $balanceAmount->SumOftotTransactionAmount) - $returnAmount;
        } else {
            $totalPendingAmount = $unbilledGrvGroupByMaster->totTransactionAmount - $returnAmount;
        }

        $input['supplierInvoOrderedAmount'] = $totalPendingAmount - $input['supplierInvoAmount'];

        $currency = \Helper::convertAmountToLocalRpt(200, $bookInvSuppDet->unbilledgrvAutoID, $input['supplierInvoAmount']);

        $input['totTransactionAmount'] = $input['supplierInvoAmount'];
        $input['totLocalAmount'] = \Helper::roundValue($currency['localAmount']);
        $input['totRptAmount'] = \Helper::roundValue($currency['reportingAmount']);

        $bookInvSuppDet = $this->bookInvSuppDetRepository->update($input, $id);

        //update vat

        if($unbilledGrvGroupByMaster->totalVATAmount > 0 && $unbilledGrvGroupByMaster->totTransactionAmount > 0){
            $bookInvSuppDet = $this->bookInvSuppDetRepository->findWithoutFail($id);
            $percentage =  ($bookInvSuppDet->totTransactionAmount/$unbilledGrvGroupByMaster->totTransactionAmount);
            $VATAmount = $unbilledGrvGroupByMaster->totalVATAmount * $percentage;
            $currencyVat = \Helper::convertAmountToLocalRpt(200, $bookInvSuppDet->unbilledgrvAutoID, $VATAmount);
            $vatData = array(
                'VATAmount' => \Helper::roundValue($VATAmount),
                'VATAmountLocal' => \Helper::roundValue($currencyVat['localAmount']),
                'VATAmountRpt' =>  \Helper::roundValue($currencyVat['reportingAmount'])
            );

            $this->bookInvSuppDetRepository->update($vatData, $id);
        }


        // balance Amount

        $getTotal = BookInvSuppDet::where('unbilledgrvAutoID', $bookInvSuppDet->unbilledgrvAutoID)
            ->sum('totTransactionAmount');

        $updateUnbilledGrvGroupByMaster = UnbilledGrvGroupBy::find($bookInvSuppDet->unbilledgrvAutoID);

        if ($unbilledGrvGroupByMaster->totTransactionAmount == $getTotal) {

            $updateUnbilledGrvGroupByMaster->selectedForBooking = -1;
            $updateUnbilledGrvGroupByMaster->fullyBooked = 2;

        } else if ($getTotal != 0) {

            $updateUnbilledGrvGroupByMaster->selectedForBooking = -1;
            $updateUnbilledGrvGroupByMaster->fullyBooked = 1;

        } else if ($getTotal == 0) {

            $updateUnbilledGrvGroupByMaster->selectedForBooking = -1;
            $updateUnbilledGrvGroupByMaster->fullyBooked = 0;

        }
        $updateUnbilledGrvGroupByMaster->save();


        return ['status' => true, 'data' => $bookInvSuppDet->toArray()];
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/bookInvSuppDets/{id}",
     *      summary="Remove the specified BookInvSuppDet from storage",
     *      tags={"BookInvSuppDet"},
     *      description="Delete BookInvSuppDet",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of BookInvSuppDet",
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
        /** @var BookInvSuppDet $bookInvSuppDet */
        $bookInvSuppDet = $this->bookInvSuppDetRepository->findWithoutFail($id);

        if (empty($bookInvSuppDet)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.supplier_invoice_details')]),500);
        }

        if($bookInvSuppDet->suppinvmaster && $bookInvSuppDet->suppinvmaster->confirmedYN){
            return $this->sendError(trans('custom.you_cannot_delete_supplier_invoice_detail_this_document_already_confirmed'),500);
        }

        $unbilledSum = UnbilledGrvGroupBy::find($bookInvSuppDet->unbilledgrvAutoID);

        if (empty($unbilledSum)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.un_billed_grv_id')]),500);
        }

        $unbilledgrvAutoID = $bookInvSuppDet->unbilledgrvAutoID;
        $poMasterAutoID = $bookInvSuppDet->purchaseOrderID;
        $documentCurrencyDecimalPlace =  $bookInvSuppDet->supplierTransactionCurrencyID;


        if ($poMasterAutoID > 0) {
            $checkExtraCharges = DirectInvoiceDetails::where('directInvoiceAutoID', $bookInvSuppDet->bookingSuppMasInvAutoID)
                                                     ->where('purchaseOrderID', $poMasterAutoID)
                                                     ->first();

            if ($checkExtraCharges) {
                return $this->sendError("Extra charges has been liked with this PO. Please delete extra charge and continue",500);
            }
        }

        $bookInvSuppDet->delete();

        $getTotal = BookInvSuppDet::where('unbilledgrvAutoID', $unbilledgrvAutoID)
            ->sum('totTransactionAmount');

        if ($getTotal == 0) {
            $updatePRMaster = UnbilledGrvGroupBy::find($bookInvSuppDet->unbilledgrvAutoID)
                ->update([
                    'selectedForBooking' => 0,
                    'fullyBooked' => 0
                ]);
        } else {
            $updatePRMaster = UnbilledGrvGroupBy::find($bookInvSuppDet->unbilledgrvAutoID)
                ->update([
                    'selectedForBooking' => 0,
                    'fullyBooked' => 1
                ]);
        }

        if ($poMasterAutoID > 0) {
            // updating po master flag
            $poMasterTableTotal = ProcumentOrder::find($poMasterAutoID);

            $getTotal = BookInvSuppDet::where('purchaseOrderID', $poMasterAutoID)
                ->sum('totTransactionAmount');

            if (round($poMasterTableTotal->poTotalSupplierTransactionCurrency, $documentCurrencyDecimalPlace) == round($getTotal, $documentCurrencyDecimalPlace)) {
                $poMasterTableTotal->invoicedBooked = 2;
            } else if(round($poMasterTableTotal->poTotalSupplierTransactionCurrency, $documentCurrencyDecimalPlace) <= round($getTotal, $documentCurrencyDecimalPlace)){
                $poMasterTableTotal->invoicedBooked = 2;
            } else if ($getTotal != 0) {
                $poMasterTableTotal->invoicedBooked = 1;
            } else if ($getTotal == 0) {
                $poMasterTableTotal->invoicedBooked = 0;
            }
            $poMasterTableTotal->save();
        }


        SupplierInvoiceItemDetail::where('bookingSupInvoiceDetAutoID', $id)->delete();

        $this->deleteReturnUnbilledGrvs($unbilledSum->grvAutoID, $bookInvSuppDet->bookingSuppMasInvAutoID);

        $bookInvSuppMaster = BookInvSuppMaster::find($bookInvSuppDet->bookingSuppMasInvAutoID);
        $bookInvSuppMaster->whtEdited = false;
        $bookInvSuppMaster->save();

        \Helper::updateSupplierRetentionAmount($bookInvSuppDet->bookingSuppMasInvAutoID,$bookInvSuppMaster);
        \Helper::updateSupplierWhtAmount($bookInvSuppDet->bookingSuppMasInvAutoID,$bookInvSuppMaster);

        return $this->sendResponse($id, trans('custom.delete', ['attribute' => trans('custom.supplier_invoice_details')]));
    }

    public function deleteReturnUnbilledGrvs($grvAutoID, $bookingSuppMasInvAutoID)
    {
        $unbilledDatas = BookInvSuppDet::where('bookingSuppMasInvAutoID', $bookingSuppMasInvAutoID)
                      ->where('grvAutoID',  $grvAutoID)
                      ->whereHas('unbilled_grv', function($query) {
                        $query->whereNotNull('purhaseReturnAutoID');
                      })
                      ->get();

        foreach ($unbilledDatas as $key => $value) {
            $updatePRMaster = UnbilledGrvGroupBy::find($value->unbilledgrvAutoID)
                ->update([
                    'selectedForBooking' => 0,
                    'fullyBooked' => 0
                ]);
        }

        $res = BookInvSuppDet::where('bookingSuppMasInvAutoID', $bookingSuppMasInvAutoID)
                      ->where('grvAutoID',  $grvAutoID)
                      ->whereHas('unbilled_grv', function($query) {
                        $query->whereNotNull('purhaseReturnAutoID');
                      })
                      ->delete();

        return true;
    }

    public function storePOBaseDetail(Request $request)
    {
        
        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input, array('type'));

        $prDetail_arr = array();
        $validator = array();
        $bookingSuppMasInvAutoID = $input['bookingSuppMasInvAutoID'];
        $isCheckArr = collect($input['detailTable'])->pluck('isChecked')->toArray();
        if (!in_array(true, $isCheckArr)) {
            return $this->sendError(trans('custom.no_grv_selected_to_add'));
        }

        $bookInvSuppMaster = BookInvSuppMaster::find($bookingSuppMasInvAutoID);
        $bookInvSuppMaster->whtEdited = false;
        $bookInvSuppMaster->save();
        if (empty($bookInvSuppMaster)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.supplier_invoice')]));
        }

        if($bookInvSuppMaster->confirmedYN){
            return $this->sendError(trans('custom.you_cannot_add_supplier_invoice_detail_this_document_already_confirmed'),500);
        }

        if(isset($input['type']) &&  $input['type'] != $bookInvSuppMaster->documentType)
        {
            return $this->sendError('The invoice type and details have already been modified by another user');
        }

        DB::beginTransaction();
        try {

            $itemExistArray = array();
            $prExistArray = array();
            //check added item exist
            foreach ($input['detailTable'] as $itemExist) {

                if (isset($itemExist['isChecked']) && $itemExist['isChecked']) {
                    $siDetailExist = BookInvSuppDet::with(['grvmaster'])
                        ->where('bookingSuppMasInvAutoID', $bookingSuppMasInvAutoID)
                        ->where('unbilledgrvAutoID', $itemExist['unbilledgrvAutoID'])
                        ->get();

                    if (!empty($siDetailExist)) {
                        foreach ($siDetailExist as $row) {
                            $itemDrt = "The GRV you are trying to add already added to this invoice";
                            $itemExistArray[] = [$itemDrt];
                        }
                    }


                    $checkPurchaseReurn = PurchaseReturnDetails::with(['master' => function($query){
                                                                    $query->where('approved', 0);
                                                               }])
                                                               ->where('grvAutoID', $itemExist['grvAutoID'])
                                                               ->whereHas('master', function($query){
                                                                    $query->where('approved', 0);
                                                               })
                                                               ->first();

                    if ($checkPurchaseReurn) {
                        $prExistArray[] = [$checkPurchaseReurn->master->purchaseReturnCode. "is pending for approval."];
                    }
                }

            }

            if (!empty($prExistArray)) {
                return $this->sendError($prExistArray, 422);
            }

            /*
            * GWL-713
             * documentType == 0  -   invoice type - PO
            *  check policy 11 - Allow multiple GRV in One Invoice
            * if policy 11 is 1 allow to add multiple different PO's
            * if policy 11 is 0 do not allow multiple different PO's
             */
            if($bookInvSuppMaster->documentType==0){
                $policy = CompanyPolicyMaster::where('companyPolicyCategoryID', 11)
                    ->where('companySystemID', $bookInvSuppMaster->companySystemID)
                    ->first();

                if(empty($policy) || (!empty($policy) && !$policy->isYesNO)) {
                    $poId = 0;

                    $details = BookInvSuppDet::where('bookingSuppMasInvAutoID', $bookingSuppMasInvAutoID)->get();
                    if(count($details)){

                        $poIdArray = $details->pluck('purchaseOrderID')->toArray();

                        if (count(array_unique($poIdArray)) > 1) {
                            return $this->sendError(trans('custom.multiple_pos_cannot_be_added_different_po_found_on_saved_details'));
                        }
                        $poId = $poIdArray[0];
                    }

                    $inputDetails = $input['detailTable'];
                    $inputPoIdArray = collect($inputDetails)->pluck('purchaseOrderID')->toArray();
                    if (count(array_unique($inputPoIdArray)) > 1) {
                        return $this->sendError(trans('custom.multiple_pos_cannot_be_added_different_po_found_on_selected_details'));
                    }
                    $inputPoId = $inputPoIdArray[0];

                    if($poId != 0 && $poId != $inputPoId){
                        return $this->sendError('multiple_pos_cannot_be_added_different_po_found_on_selected_and_already_saved_details');
                    }
                }
            }


            //check record total in General Ledger table
            foreach ($input['detailTable'] as $itemExist) {

                if (isset($itemExist['isChecked']) && $itemExist['isChecked']) {

                    $glCheck = GeneralLedger::selectRaw('Sum(erp_generalledger.documentLocalAmount) AS SumOfdocumentLocalAmount, Sum(erp_generalledger.documentRptAmount) AS SumOfdocumentRptAmount,erp_generalledger.documentSystemID, erp_generalledger.documentSystemCode,documentCode,documentID')->where('documentSystemID', 3)->where('companySystemID', $itemExist['companySystemID'])->where('documentSystemCode', $itemExist['grvAutoID'])->groupBY('companySystemID', 'documentSystemID', 'documentSystemCode')->first();

                    if ($glCheck) {
                        if (round($glCheck->SumOfdocumentLocalAmount, 0) != 0 || round($glCheck->SumOfdocumentRptAmount, 0) != 0) {
                            $itemDrt = "Selected GRV " . $itemExist['grvPrimaryCode'] . " is not updated in general ledger. Please check again";
                            $itemExistArray[] = [$itemDrt];
                        }
                    } else {
                        $itemDrt = "Selected GRV " . $itemExist['grvPrimaryCode'] . " is not updated in general ledger. Please check again";
                        $itemExistArray[] = [$itemDrt];
                    }
                }
            }

            //check total matching
            foreach ($input['detailTable'] as $temp) {

                $groupMasterCheck = UnbilledGrvGroupBy::find($temp['unbilledgrvAutoID']);

                if (isset($temp['isChecked']) && $temp['isChecked']) {

                    $balanceAmount = collect(\DB::select('SELECT erp_bookinvsuppdet.unbilledgrvAutoID, Sum(erp_bookinvsuppdet.totTransactionAmount) AS SumOftotTransactionAmount FROM erp_bookinvsuppdet WHERE unbilledgrvAutoID = ' . $temp['unbilledgrvAutoID'] . ' GROUP BY erp_bookinvsuppdet.unbilledgrvAutoID;'))->first();

                    if ($balanceAmount) {
                        if (($groupMasterCheck->totTransactionAmount == $balanceAmount->SumOftotTransactionAmount) || ($balanceAmount->SumOftotTransactionAmount > $groupMasterCheck->totTransactionAmount)) {
                            $itemDrt = "Selected " . $temp['grvPrimaryCode'] . " GRV has been fully booked. Please check again";
                            $itemExistArray[] = [$itemDrt];
                        }
                    }
                }
            }

            // check with po table
            foreach ($input['detailTable'] as $temp) {

                if (isset($temp['isChecked']) && $temp['isChecked']) {

                    if ($bookInvSuppMaster->documentType == 0) {
                        $poMasterTotal = ProcumentOrder::find($temp['purchaseOrderID']);
                        //erp_purchaseorderadvpayment
                        //reqAmountInPOTransCur
                        $padpTotal = PoAdvancePayment::where('poID',$temp['purchaseOrderID'])
                                                  ->where('supplierID',$temp['supplierID'])
                                                  ->sum('reqAmountTransCur_amount');

                        $checkPreTotal = BookInvSuppDet::where('purchaseOrderID', $temp['purchaseOrderID'])
                            ->where('supplierID', $temp['supplierID'])
                            ->sum('totTransactionAmount');

                        if ($checkPreTotal > ($poMasterTotal->poTotalSupplierTransactionCurrency + $padpTotal)) {
                            $itemDrt = 'Supplier Invoice amount is greater than ' . $poMasterTotal->purchaseOrderCode . ' PO amount. Please check again.';
                            $itemExistArray[] = [$itemDrt];
                        }
                    } else {
                        $grvMasterTotal = GRVMaster::find($temp['grvAutoID']);
                        //erp_purchaseorderadvpayment
                        //reqAmountInPOTransCur
                        $padpTotal = PoAdvancePayment::where('grvAutoID',$temp['grvAutoID'])
                                                  ->where('supplierID',$temp['supplierID'])
                                                  ->sum('reqAmountInPOTransCur');

                        $checkPreTotal = BookInvSuppDet::where('grvAutoID', $temp['grvAutoID'])
                            ->where('supplierID', $temp['supplierID'])
                            ->sum('totTransactionAmount');

                        if ($checkPreTotal > ($grvMasterTotal->grvTotalSupplierTransactionCurrency + $padpTotal)) {
                            $itemDrt = 'Supplier Invoice amount is greater than ' . $grvMasterTotal->grvPrimaryCode . ' GRV amount. Please check again.';
                            $itemExistArray[] = [$itemDrt];
                        }
                    }
                }
            }

            if (!empty($itemExistArray)) {
                return $this->sendError($itemExistArray, 422);
            }

            $pullAmount = 0;
            foreach ($input['detailTable'] as $new) {

                $groupMaster = UnbilledGrvGroupBy::find($new['unbilledgrvAutoID']);

                if (isset($new['isChecked']) && $new['isChecked']) {

                    $totalPendingAmount = 0;
                    // balance Amount
                    $balanceAmount = collect(\DB::select('SELECT erp_bookinvsuppdet.unbilledgrvAutoID, Sum(erp_bookinvsuppdet.totTransactionAmount) AS SumOftotTransactionAmount FROM erp_bookinvsuppdet WHERE unbilledgrvAutoID = ' . $new['unbilledgrvAutoID'] . ' GROUP BY erp_bookinvsuppdet.unbilledgrvAutoID;'))->first();

                    if ($balanceAmount) {
                        $totalPendingAmount = ($groupMaster->totTransactionAmount - $balanceAmount->SumOftotTransactionAmount);
                    } else {
                        $totalPendingAmount = $groupMaster->totTransactionAmount;
                    }

                    $prDetail_arr['bookingSuppMasInvAutoID'] = $bookingSuppMasInvAutoID;
                    $prDetail_arr['unbilledgrvAutoID'] = $new['unbilledgrvAutoID'];
                    $prDetail_arr['companySystemID'] = $groupMaster->companySystemID;
                    $prDetail_arr['companyID'] = $groupMaster->companyID;
                    $prDetail_arr['supplierID'] = $groupMaster->supplierID;
                    $prDetail_arr['purchaseOrderID'] = $groupMaster->purchaseOrderID;
                    $prDetail_arr['grvAutoID'] = $groupMaster->grvAutoID;
                    $prDetail_arr['grvType'] = $groupMaster->grvType;
                    $prDetail_arr['supplierTransactionCurrencyID'] = $groupMaster->supplierTransactionCurrencyID;
                    $prDetail_arr['supplierTransactionCurrencyER'] = $groupMaster->supplierTransactionCurrencyER;
                    $prDetail_arr['companyReportingCurrencyID'] = $groupMaster->companyReportingCurrencyID;
                    $prDetail_arr['companyReportingER'] = $groupMaster->companyReportingER;
                    $prDetail_arr['localCurrencyID'] = $groupMaster->localCurrencyID;
                    $prDetail_arr['localCurrencyER'] = $groupMaster->localCurrencyER;
                    $prDetail_arr['supplierInvoOrderedAmount'] = $totalPendingAmount;
                    $prDetail_arr['transSupplierInvoAmount'] = $groupMaster->totTransactionAmount;
                    $prDetail_arr['localSupplierInvoAmount'] = $groupMaster->totLocalAmount;
                    $prDetail_arr['rptSupplierInvoAmount'] = $groupMaster->totRptAmount;
                    //$prDetail_arr['supplierInvoAmount'] = $groupMaster->totTransactionAmount;
                    //$prDetail_arr['totTransactionAmount'] = $groupMaster->totTransactionAmount;
                    //$prDetail_arr['totLocalAmount'] = $groupMaster->totLocalAmount;
                    //$prDetail_arr['totRptAmount'] = $groupMaster->totRptAmount;
                    $item = $this->bookInvSuppDetRepository->create($prDetail_arr);

                    $updatePRMaster = UnbilledGrvGroupBy::find($new['unbilledgrvAutoID'])
                        ->update([
                            'selectedForBooking' => -1
                        ]);


                    // $this->checkPurchaseReturnsAndUpdateBookInvDetail($new['grvAutoID'], $bookingSuppMasInvAutoID);


                    $resDetail = $this->storeSupplierInvoiceGrvDetails($new, $item->bookingSupInvoiceDetAutoID, $bookingSuppMasInvAutoID, $groupMaster);
                        

                    if (!$resDetail['status']) {
                        return $this->sendError($resDetail['message'], 500);
                    }

                    $pullAmount = $resDetail['data'];
                    
                    if ($pullAmount > 0) {
                        $supplierInvoiceDetail = $item->toArray();

                        $supplierInvoiceDetail['supplierInvoAmount'] = $pullAmount;      
                        
                        $resultUpdateDetail = $this->updateDetail($supplierInvoiceDetail, $supplierInvoiceDetail['bookingSupInvoiceDetAutoID']);

                        if (!$resultUpdateDetail['status']) {
                            return $this->sendError($result['message'], 500);
                        } 
                    }
                }
            }


            \Helper::updateSupplierRetentionAmount($bookingSuppMasInvAutoID,$bookInvSuppMaster);
            \Helper::updateSupplierWhtAmount($bookingSuppMasInvAutoID,$bookInvSuppMaster);

            DB::commit();
            return $this->sendResponse('', trans('custom.save', ['attribute' => trans('custom.purchase_order_details')]));
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage().$exception->getLine());
        }

    }


    public function editPOBaseDetail(Request $request)
    {
        $input = $request->all();

        $bookInvSuppDetail = BookInvSuppDet::find($input['bookingSupInvoiceDetAutoID']);

        $groupMaster = UnbilledGrvGroupBy::find($bookInvSuppDetail->unbilledgrvAutoID);

        $bookingSuppMasInvAutoID = $input['bookingSuppMasInvAutoID'];

        $bookInvSuppMaster = BookInvSuppMaster::find($bookingSuppMasInvAutoID);

        if (empty($bookInvSuppMaster)) {
            return $this->sendError('Supplier Invoice not found');
        }

        DB::beginTransaction();
        try {

            $totalPullAmount = 0;
            foreach ($input['detailTable'] as $key => $value) {

                $totalPullAmount += ((isset($value['supplierInvoAmount']) && $value['supplierInvoAmount'] > 0) ? $value['supplierInvoAmount'] : 0); 

                $invoiceItem = SupplierInvoiceItemDetail::when($groupMaster->logisticYN != 1, function($query) use ($value) {
                                                            $query->where('grvDetailsID', $value['grvDetailsID']);  
                                                        })
                                                        ->when($groupMaster->logisticYN == 1, function($query) use ($value) {
                                                            $query->where('logisticID', $value['logisticID']);  
                                                        })
                                                        ->where('bookingSupInvoiceDetAutoID', $input['bookingSupInvoiceDetAutoID'])
                                                        ->first();

                $invoiceItemID = ($invoiceItem) ? $invoiceItem->id : 0;
                
                if ($value['supplierInvoAmount'] == "") {
                    $updateData['supplierInvoAmount'] = 0;
                } else {
                    $updateData['supplierInvoAmount'] = $value['supplierInvoAmount'];
                }

                $grvDetail = ($groupMaster->logisticYN) ? PoAdvancePayment::find($value['logisticID']) : GRVDetails::find($value['grvDetailsID']);

                $totalPendingAmount = 0;
                // balance Amount
                if ($groupMaster->logisticYN) {
                    $balanceAmount = collect(\DB::select('SELECT erp_bookinvsupp_item_det.grvDetailsID, Sum(erp_bookinvsupp_item_det.totTransactionAmount) AS SumOftotTransactionAmount FROM erp_bookinvsupp_item_det WHERE logisticID = ' . $value['logisticID'] . ' AND erp_bookinvsupp_item_det.id !='.$invoiceItemID.' GROUP BY erp_bookinvsupp_item_det.logisticID;'))->first();
                    
                } else {
                    $balanceAmount = collect(\DB::select('SELECT erp_bookinvsupp_item_det.grvDetailsID, Sum(erp_bookinvsupp_item_det.totTransactionAmount) AS SumOftotTransactionAmount FROM erp_bookinvsupp_item_det WHERE grvDetailsID = ' . $value['grvDetailsID'] . ' AND erp_bookinvsupp_item_det.id !='.$invoiceItemID.' GROUP BY erp_bookinvsupp_item_det.grvDetailsID;'))->first();
                }


                if ($balanceAmount) {
                    $totalPendingAmount = ($value['transactionAmount'] - $balanceAmount->SumOftotTransactionAmount);
                } else {
                    $totalPendingAmount = $value['transactionAmount'];
                }


                $updateData['supplierInvoOrderedAmount'] = $totalPendingAmount - $value['supplierInvoAmount'];

                $currency = \Helper::currencyConversion($bookInvSuppMaster->companySystemID, $bookInvSuppDetail->supplierTransactionCurrencyID, $bookInvSuppDetail->supplierTransactionCurrencyID, $updateData['supplierInvoAmount']);

                $updateData['totTransactionAmount'] = $updateData['supplierInvoAmount'];
                $updateData['totLocalAmount'] = \Helper::roundValue($currency['localAmount']);
                $updateData['totRptAmount'] = \Helper::roundValue($currency['reportingAmount']);

                $totalVATAmount = ($groupMaster->logisticYN) ? TaxService::poLogisticVATDistributionForGRV($grvDetail->grvAutoID,0,$grvDetail->supplierID)['vatOnPOTotalAmountTrans'] : TaxService::processGRVDetailVATForUnbilled($grvDetail->grvDetailsID)['totalTransVATAmount'];

                if($totalVATAmount > 0 && $value['transactionAmount'] > 0){
                    $percentage =  (floatval($updateData['totTransactionAmount'])/$value['transactionAmount']);
                    $VATAmount = $totalVATAmount * $percentage;
                    $currencyVat = \Helper::currencyConversion($bookInvSuppMaster->companySystemID, $bookInvSuppDetail->supplierTransactionCurrencyID, $bookInvSuppDetail->supplierTransactionCurrencyID, $VATAmount);
                        $updateData['VATAmount'] = \Helper::roundValue($VATAmount);
                        $updateData['VATAmountLocal'] = \Helper::roundValue($currencyVat['localAmount']);
                        $updateData['VATAmountRpt'] = \Helper::roundValue($currencyVat['reportingAmount']);
                }

                SupplierInvoiceItemDetail::when($groupMaster->logisticYN != 1, function($query) use ($value) {
                                            $query->where('grvDetailsID', $value['grvDetailsID']);  
                                        })
                                        ->when($groupMaster->logisticYN == 1, function($query) use ($value) {
                                            $query->where('logisticID', $value['logisticID']);  
                                        })
                                        ->where('bookingSupInvoiceDetAutoID', $input['bookingSupInvoiceDetAutoID'])
                                        ->update($updateData);
            }

            if ($totalPullAmount > 0) {
                $supplierInvoiceDetail = $bookInvSuppDetail->toArray();

                $supplierInvoiceDetail['supplierInvoAmount'] = $totalPullAmount;      
                
                $resultUpdateDetail = $this->updateDetail($supplierInvoiceDetail, $supplierInvoiceDetail['bookingSupInvoiceDetAutoID']);

                if (!$resultUpdateDetail['status']) {
                    return $this->sendError($result['message'], 500);
                } 
            }
            \Helper::updateSupplierRetentionAmount($bookingSuppMasInvAutoID,$bookInvSuppMaster);
            \Helper::updateSupplierWhtAmount($bookingSuppMasInvAutoID,$bookInvSuppMaster);
            DB::commit();
            return $this->sendResponse('', trans('custom.save', ['attribute' => trans('custom.purchase_order_details')]));
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage().$exception->getLine());
        }
    }

    public function storeSupplierInvoiceGrvDetails($unbilledData, $bookingSupInvoiceDetAutoID, $bookingSuppMasInvAutoID, $groupMaster)
    {
        $totalPullAmount = 0;
        foreach ($unbilledData['grv_details'] as $key => $value) {

            $totalPullAmount += ((isset($value['supplierInvoAmount']) && $value['supplierInvoAmount'] > 0) ? $value['supplierInvoAmount'] : 0); 

            $totalPendingAmount = 0;
            if ($unbilledData['logisticYN']) {
                $grvDetail = PoAdvancePayment::find($value['logisticID']);

                // balance Amount
                $balanceAmount = collect(\DB::select('SELECT erp_bookinvsupp_item_det.logisticID, Sum(erp_bookinvsupp_item_det.totTransactionAmount) AS SumOftotTransactionAmount FROM erp_bookinvsupp_item_det WHERE logisticID = ' . $value['logisticID'] . ' GROUP BY erp_bookinvsupp_item_det.logisticID;'))->first();
            } else {
                $grvDetail = GRVDetails::find($value['grvDetailsID']);
                // balance Amount
                $balanceAmount = collect(\DB::select('SELECT erp_bookinvsupp_item_det.grvDetailsID, Sum(erp_bookinvsupp_item_det.totTransactionAmount) AS SumOftotTransactionAmount FROM erp_bookinvsupp_item_det WHERE grvDetailsID = ' . $value['grvDetailsID'] . ' GROUP BY erp_bookinvsupp_item_det.grvDetailsID;'))->first();
            }

            if ($balanceAmount) {
                $totalPendingAmount = ($value['transactionAmount'] - $balanceAmount->SumOftotTransactionAmount);
            } else {
                $totalPendingAmount = $value['transactionAmount'];
            }

            $details = [
                'bookingSupInvoiceDetAutoID' => $bookingSupInvoiceDetAutoID,
                'bookingSuppMasInvAutoID' => $bookingSuppMasInvAutoID,
                'unbilledgrvAutoID' => $unbilledData['unbilledgrvAutoID'],
                'companySystemID' => $unbilledData['companySystemID'],
                'grvDetailsID' => $value['grvDetailsID'],
                'logisticID' => $value['logisticID'],
                'vatMasterCategoryID' => $value['vatMasterCategoryID'],
                'vatSubCategoryID' => $value['vatSubCategoryID'],
                'exempt_vat_portion' => $value['exempt_vat_portion'],
                'purchaseOrderID' => $unbilledData['purchaseOrderID'],
                'grvAutoID' => $unbilledData['grvAutoID'],
                'supplierTransactionCurrencyID' => $groupMaster->supplierTransactionCurrencyID,
                'supplierTransactionCurrencyER' => $groupMaster->supplierTransactionCurrencyER,
                'companyReportingCurrencyID' => $groupMaster->companyReportingCurrencyID,
                'companyReportingER' => $groupMaster->companyReportingER,
                'localCurrencyID' => $groupMaster->localCurrencyID,
                'localCurrencyER' => $groupMaster->localCurrencyER,
                'supplierInvoOrderedAmount' => ($totalPendingAmount - floatval(((isset($value['supplierInvoAmount']) && $value['supplierInvoAmount'] > 0) ? $value['supplierInvoAmount'] : 0))),
                'transSupplierInvoAmount' => $value['transactionAmount'],
                'localSupplierInvoAmount' => $value['localAmount'],
                'rptSupplierInvoAmount' => $value['rptAmount']
            ];

            if (isset($value['supplierInvoAmount']) && $value['supplierInvoAmount'] > 0) {
                $details['supplierInvoAmount'] = floatval($value['supplierInvoAmount']);
            } else {
                $details['supplierInvoAmount'] = 0;
            }



            $details['totTransactionAmount'] = $details['supplierInvoAmount'];

            $totLocalAmount = CurrencyConversionService::localAndReportingConversionByER($groupMaster->supplierTransactionCurrencyID, $groupMaster->localCurrencyID, $details['supplierInvoAmount'], $groupMaster->localCurrencyER);
            $details['totLocalAmount'] = \Helper::roundValue($totLocalAmount);
            
            $totRptAmount = CurrencyConversionService::localAndReportingConversionByER($groupMaster->supplierTransactionCurrencyID, $groupMaster->companyReportingCurrencyID, $details['supplierInvoAmount'], $groupMaster->companyReportingER);
            $details['totRptAmount'] = \Helper::roundValue($totRptAmount);

            $grvDetailsInfo = GRVDetails::with(['vat_sub_category'])->find($grvDetail->grvDetailsID);
            if(!$unbilledData['logisticYN'])
            {
                if(isset($grvDetailsInfo->vat_sub_category->subCatgeoryType) && $grvDetailsInfo->vat_sub_category->subCatgeoryType == 3)
                {
                    $examptVal = TaxService::processGRVDetailVATForUnbilled($grvDetail->grvDetailsID)['exemptVATTrans'];
                }
                else
                {
                    $examptVal = TaxService::processGRVDetailVATForUnbilled($grvDetail->grvDetailsID)['totalTransVATAmount'];
                }
            }

            if($value['logisticID'] > 0){
                $details['VATAmount'] = \Helper::roundValue($value['VATAmount']);
                $details['VATAmountLocal'] = \Helper::roundValue($value['VATAmountLocal']);
                $details['VATAmountRpt'] = \Helper::roundValue($value['VATAmountRpt']);
            } else {
                $totalVATAmount = ($unbilledData['logisticYN']) ? TaxService::poLogisticVATDistributionForGRV($grvDetail->grvAutoID,0,$grvDetail->supplierID)['vatOnPOTotalAmountTrans'] : $examptVal;
                 if($totalVATAmount > 0 && $value['transactionAmount'] > 0){
                    $percentage =  (floatval($details['totTransactionAmount'])/$value['transactionAmount']);
                    $VATAmount = $totalVATAmount * $percentage;
                        $details['VATAmount'] = \Helper::roundValue($VATAmount);

                        $VATAmountLocal = CurrencyConversionService::localAndReportingConversionByER($groupMaster->supplierTransactionCurrencyID, $groupMaster->localCurrencyID, $VATAmount, $groupMaster->localCurrencyER);
                        $details['VATAmountLocal'] = \Helper::roundValue($VATAmountLocal);


                        $VATAmountRpt = CurrencyConversionService::localAndReportingConversionByER($groupMaster->supplierTransactionCurrencyID, $groupMaster->companyReportingCurrencyID, $VATAmount, $groupMaster->companyReportingER);
                        $details['VATAmountRpt'] = \Helper::roundValue($VATAmountRpt);
                }
            }

            $createRes = SupplierInvoiceItemDetail::create($details);

        }

        return ['status' => true, 'data' => $totalPullAmount];
    }

    public function checkPurchaseReturnsAndUpdateBookInvDetail($grvAutoID, $bookingSuppMasInvAutoID)
    {
        $unbilledData = UnbilledGrvGroupBy::where('grvAutoID', $grvAutoID)
                                          ->whereNotNull('purhaseReturnAutoID')
                                          ->get();

        foreach ($unbilledData as $key => $groupMaster) {
            $totalPendingAmount = 0;
            // balance Amount
            $balanceAmount = collect(\DB::select('SELECT erp_bookinvsuppdet.unbilledgrvAutoID, Sum(erp_bookinvsuppdet.totTransactionAmount) AS SumOftotTransactionAmount FROM erp_bookinvsuppdet WHERE unbilledgrvAutoID = ' . $groupMaster->unbilledgrvAutoID . ' GROUP BY erp_bookinvsuppdet.unbilledgrvAutoID;'))->first();

            if ($balanceAmount) {
                $totalPendingAmount = ($groupMaster->totTransactionAmount - $balanceAmount->SumOftotTransactionAmount);
            } else {
                $totalPendingAmount = $groupMaster->totTransactionAmount;
            }

            $prDetail_arr['bookingSuppMasInvAutoID'] = $bookingSuppMasInvAutoID;
            $prDetail_arr['unbilledgrvAutoID'] = $groupMaster->unbilledgrvAutoID;
            $prDetail_arr['companySystemID'] = $groupMaster->companySystemID;
            $prDetail_arr['companyID'] = $groupMaster->companyID;
            $prDetail_arr['supplierID'] = $groupMaster->supplierID;
            $prDetail_arr['purchaseOrderID'] = $groupMaster->purchaseOrderID;
            $prDetail_arr['grvAutoID'] = $groupMaster->grvAutoID;
            $prDetail_arr['grvType'] = $groupMaster->grvType;
            $prDetail_arr['supplierTransactionCurrencyID'] = $groupMaster->supplierTransactionCurrencyID;
            $prDetail_arr['supplierTransactionCurrencyER'] = $groupMaster->supplierTransactionCurrencyER;
            $prDetail_arr['companyReportingCurrencyID'] = $groupMaster->companyReportingCurrencyID;
            $prDetail_arr['companyReportingER'] = $groupMaster->companyReportingER;
            $prDetail_arr['localCurrencyID'] = $groupMaster->localCurrencyID;
            $prDetail_arr['localCurrencyER'] = $groupMaster->localCurrencyER;
            $prDetail_arr['supplierInvoOrderedAmount'] = $totalPendingAmount * -1;
            $prDetail_arr['transSupplierInvoAmount'] = $groupMaster->totTransactionAmount * -1;
            $prDetail_arr['localSupplierInvoAmount'] = $groupMaster->totLocalAmount * -1;
            $prDetail_arr['rptSupplierInvoAmount'] = $groupMaster->totRptAmount * -1;
            $item = $this->bookInvSuppDetRepository->create($prDetail_arr);

            $totTransactionAmount = $groupMaster->totTransactionAmount * -1;

            $this->updateInvoiceAmountOfReturn($item->bookingSupInvoiceDetAutoID, $totTransactionAmount);
        }
    }


    public function updateInvoiceAmountOfReturn($id, $totTransactionAmount)
    {
        $bookInvSuppDet = $this->bookInvSuppDetRepository->findWithoutFail($id);

        if (empty($bookInvSuppDet)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.supplier_invoice_details')]));
        }

        if($bookInvSuppDet->suppinvmaster && $bookInvSuppDet->suppinvmaster->confirmedYN){
            return $this->sendError(trans('custom.you_cannot_update_supplier_invoice_detail_this_document_already_confirmed'),500);
        }

        $unbilledGrvGroupByMaster = UnbilledGrvGroupBy::where('unbilledgrvAutoID', $bookInvSuppDet->unbilledgrvAutoID)
            ->first();

        if (empty($unbilledGrvGroupByMaster)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.supplier_invoice')]));
        }

        $input['supplierInvoAmount'] = $totTransactionAmount;

        $balanceAmount = collect(\DB::select('SELECT erp_bookinvsuppdet.unbilledgrvAutoID, Sum(erp_bookinvsuppdet.totTransactionAmount) AS SumOftotTransactionAmount FROM erp_bookinvsuppdet WHERE unbilledgrvAutoID = ' . $bookInvSuppDet->unbilledgrvAutoID . ' AND erp_bookinvsuppdet.bookingSupInvoiceDetAutoID != ' . $bookInvSuppDet->bookingSupInvoiceDetAutoID . ' GROUP BY erp_bookinvsuppdet.unbilledgrvAutoID;'))->first();

        if ($balanceAmount) {
            $totalPendingAmount = ((-1 * $unbilledGrvGroupByMaster->totTransactionAmount) - $balanceAmount->SumOftotTransactionAmount);
        } else {
            $totalPendingAmount = $unbilledGrvGroupByMaster->totTransactionAmount * -1;
        }

        $input['supplierInvoOrderedAmount'] = $totalPendingAmount - $input['supplierInvoAmount'];

        $currency = \Helper::convertAmountToLocalRpt(200, $bookInvSuppDet->unbilledgrvAutoID, $input['supplierInvoAmount']);

        $input['totTransactionAmount'] = $input['supplierInvoAmount'];
        $input['totLocalAmount'] = \Helper::roundValue($currency['localAmount']);
        $input['totRptAmount'] = \Helper::roundValue($currency['reportingAmount']);

        $bookInvSuppDet = $this->bookInvSuppDetRepository->update($input, $id);

        if($unbilledGrvGroupByMaster->totalVATAmount > 0 && $unbilledGrvGroupByMaster->totTransactionAmount > 0){
            $bookInvSuppDet = $this->bookInvSuppDetRepository->findWithoutFail($id);
            $percentage =  ($bookInvSuppDet->totTransactionAmount/$unbilledGrvGroupByMaster->totTransactionAmount);
            $VATAmount = $unbilledGrvGroupByMaster->totalVATAmount * $percentage;
            $currencyVat = \Helper::convertAmountToLocalRpt(200, $bookInvSuppDet->unbilledgrvAutoID, $VATAmount);
            $vatData = array(
                'VATAmount' => \Helper::roundValue($VATAmount),
                'VATAmountLocal' => \Helper::roundValue($currencyVat['localAmount']),
                'VATAmountRpt' =>  \Helper::roundValue($currencyVat['reportingAmount'])
            );

            $this->bookInvSuppDetRepository->update($vatData, $id);
        }

        $unbilledGrvGroupByMaster->fullyBooked = 2;
        $unbilledGrvGroupByMaster->selectedForBooking = -1;

        $unbilledGrvGroupByMaster->save();
    }

    public function getSupplierInvoiceGRVItems(Request $request)
    {
        $input = $request->all();
        $invoiceID = $input['invoiceID'];

        $items = BookInvSuppDet::where('bookingSuppMasInvAutoID', $invoiceID)
            ->with(['grvmaster' => function($q){
                $q->with('details');
            }, 'pomaster','suppinvmaster'=>function($q){
                $q->select('bookingSuppMasInvAutoID','documentType');
            }])
            ->get();

        return $this->sendResponse($items->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.grv_invoice_details')]));
    }
}
