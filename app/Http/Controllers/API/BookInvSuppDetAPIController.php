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
use App\Models\BookInvSuppMaster;
use App\Models\CompanyPolicyMaster;
use App\Models\GeneralLedger;
use App\Models\PoAdvancePayment;
use App\Models\ProcumentOrder;
use App\Models\UnbilledGrvGroupBy;
use App\Repositories\BookInvSuppDetRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Illuminate\Support\Facades\DB;
use Response;

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

        return $this->sendResponse($bookInvSuppDets->toArray(), 'Book Inv Supp Dets retrieved successfully');
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

        return $this->sendResponse($bookInvSuppDets->toArray(), 'Book Inv Supp Det saved successfully');
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
            return $this->sendError('Book Inv Supp Det not found');
        }

        return $this->sendResponse($bookInvSuppDet->toArray(), 'Book Inv Supp Det retrieved successfully');
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

        /** @var BookInvSuppDet $bookInvSuppDet */
        $bookInvSuppDet = $this->bookInvSuppDetRepository->findWithoutFail($id);

        if (empty($bookInvSuppDet)) {
            return $this->sendError('Book Inv Supp Det not found');
        }

        $unbilledGrvGroupByMaster = UnbilledGrvGroupBy::where('unbilledgrvAutoID', $bookInvSuppDet->unbilledgrvAutoID)
            ->first();

        if (empty($unbilledGrvGroupByMaster)) {
            return $this->sendError('Supplier Invoice not found');
        }

        if ($input['supplierInvoAmount'] == "") {
            $input['supplierInvoAmount'] = 0;
        }

        $balanceAmount = collect(\DB::select('SELECT erp_bookinvsuppdet.unbilledgrvAutoID, Sum(erp_bookinvsuppdet.totTransactionAmount) AS SumOftotTransactionAmount FROM erp_bookinvsuppdet WHERE unbilledgrvAutoID = ' . $bookInvSuppDet->unbilledgrvAutoID . ' AND erp_bookinvsuppdet.bookingSupInvoiceDetAutoID != ' . $bookInvSuppDet->bookingSupInvoiceDetAutoID . ' GROUP BY erp_bookinvsuppdet.unbilledgrvAutoID;'))->first();

        if ($balanceAmount) {
            $totalPendingAmount = ($unbilledGrvGroupByMaster->totTransactionAmount - $balanceAmount->SumOftotTransactionAmount);
        } else {
            $totalPendingAmount = $unbilledGrvGroupByMaster->totTransactionAmount;
        }

        $input['supplierInvoOrderedAmount'] = $totalPendingAmount - $input['supplierInvoAmount'];

        $currency = \Helper::convertAmountToLocalRpt(200, $bookInvSuppDet->unbilledgrvAutoID, $input['supplierInvoAmount']);

        $input['totTransactionAmount'] = $input['supplierInvoAmount'];
        $input['totLocalAmount'] = \Helper::roundValue($currency['localAmount']);
        $input['totRptAmount'] = \Helper::roundValue($currency['reportingAmount']);

        $bookInvSuppDet = $this->bookInvSuppDetRepository->update($input, $id);

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

        return $this->sendResponse($bookInvSuppDet->toArray(), 'BookInvSuppDet updated successfully');
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
            return $this->sendError('Book Inv Supp Det not found',500);
        }

        if($bookInvSuppDet->suppinvmaster && $bookInvSuppDet->suppinvmaster->confirmedYN){
            return $this->sendError('You cannot delete book Inv Supp Det , this document confirmed',500);
        }

        $unbilledSum = UnbilledGrvGroupBy::find($bookInvSuppDet->unbilledgrvAutoID);

        if (empty($unbilledSum)) {
            return $this->sendError('Un billed Grv id not found',500);
        }

        $unbilledgrvAutoID = $bookInvSuppDet->unbilledgrvAutoID;
        $poMasterAutoID = $bookInvSuppDet->purchaseOrderID;
        $documentCurrencyDecimalPlace =  $bookInvSuppDet->supplierTransactionCurrencyID;

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

        return $this->sendResponse($id, 'Book Inv Supp Det deleted successfully');
    }

    public function storePOBaseDetail(Request $request)
    {
        $input = $request->all();
        $prDetail_arr = array();
        $validator = array();
        $bookingSuppMasInvAutoID = $input['bookingSuppMasInvAutoID'];
        $isCheckArr = collect($input['detailTable'])->pluck('isChecked')->toArray();
        if (!in_array(true, $isCheckArr)) {
            return $this->sendError("No GRV selected to add.");
        }

        $bookInvSuppMaster = BookInvSuppMaster::find($bookingSuppMasInvAutoID);

        if (empty($bookInvSuppMaster)) {
            return $this->sendError('Supplier Invoice not found');
        }

        $itemExistArray = array();
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

            }

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

                    $poIdArray = $details->pluck('purchaseOrderID')->toArray();;

                    if (count(array_unique($poIdArray)) > 1) {
                        return $this->sendError('Multiple PO\'s cannot be added. Different PO found on saved details.');
                    }
                    $poId = $poIdArray[0];
                }

                $inputDetails = $input['detailTable'];
                $inputPoIdArray = collect($inputDetails)->pluck('purchaseOrderID')->toArray();;
                if (count(array_unique($inputPoIdArray)) > 1) {
                    return $this->sendError('Multiple PO\'s cannot be added. Different PO found on selected details');
                }
                $inputPoId = $inputPoIdArray[0];

                if($poId != 0 && $poId != $inputPoId){
                    return $this->sendError('Multiple PO\'s cannot be added. Different PO found on selected and already saved details');
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

                $poMasterTotal = ProcumentOrder::find($temp['purchaseOrderID']);
                //erp_purchaseorderadvpayment
                //reqAmountInPOTransCur
                $padpTotal = PoAdvancePayment::where('poID',$temp['purchaseOrderID'])
                                          ->where('supplierID',$temp['supplierID'])
                                          ->sum('reqAmountInPOTransCur');

                $checkPreTotal = BookInvSuppDet::where('purchaseOrderID', $temp['purchaseOrderID'])
                    ->where('supplierID', $temp['supplierID'])
                    ->sum('totTransactionAmount');

                if ($checkPreTotal > ($poMasterTotal->poTotalSupplierTransactionCurrency + $padpTotal)) {
                    $itemDrt = 'Supplier Invoice amount is greater than ' . $poMasterTotal->purchaseOrderCode . ' PO amount. Please check again.';
                    $itemExistArray[] = [$itemDrt];
                }
            }
        }

        if (!empty($itemExistArray)) {
            return $this->sendError($itemExistArray, 422);
        }

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
            }


        }


        return $this->sendResponse('', 'Purchase Order Details saved successfully');

    }

    public function getSupplierInvoiceGRVItems(Request $request)
    {
        $input = $request->all();
        $invoiceID = $input['invoiceID'];

        $items = BookInvSuppDet::where('bookingSuppMasInvAutoID', $invoiceID)
            ->with(['grvmaster', 'pomaster'])
            ->get();

        return $this->sendResponse($items->toArray(), 'GRV Invoice Details retrieved successfully');
    }
}
