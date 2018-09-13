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
use App\Models\GeneralLedger;
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

        $currency = \Helper::convertAmountToLocalRpt(200, $bookInvSuppDet->unbilledgrvAutoID, $input['supplierInvoAmount']);

        $input['totTransactionAmount'] = $input['supplierInvoAmount'];
        $input['totLocalAmount'] = \Helper::roundValue($currency['localAmount']);
        $input['totRptAmount'] = \Helper::roundValue($currency['reportingAmount']);

        $bookInvSuppDet = $this->bookInvSuppDetRepository->update($input, $id);

        // balance Amount
        $balanceAmount = collect(\DB::select('SELECT erp_bookinvsuppdet.unbilledgrvAutoID, Sum(erp_bookinvsuppdet.totTransactionAmount) AS SumOftotTransactionAmount FROM erp_bookinvsuppdet WHERE unbilledgrvAutoID = ' . $bookInvSuppDet->unbilledgrvAutoID . ' GROUP BY erp_bookinvsuppdet.unbilledgrvAutoID'))->first();

        if ($unbilledGrvGroupByMaster->totTransactionAmount == $balanceAmount->SumOftotTransactionAmount) {

            $updatePRMaster = UnbilledGrvGroupBy::find($bookInvSuppDet->unbilledgrvAutoID)
                ->update([
                    'selectedForBooking' => -1,
                    'fullyBooked' => 2
                ]);
        } else {
            $updatePRMaster = UnbilledGrvGroupBy::find($bookInvSuppDet->unbilledgrvAutoID)
                ->update([
                    'selectedForBooking' => 0,
                    'fullyBooked' => 1
                ]);
        }

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
            return $this->sendError('Book Inv Supp Det not found');
        }

        /*        $updatePRMaster = UnbilledGrvGroupBy::find($bookInvSuppDet->unbilledgrvAutoID)
                    ->update([
                        'selectedForBooking' => 0,
                        'fullyBooked' => 1
                    ]);*/

        $bookInvSuppDet->delete();

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
                        $itemDrt = $row['grvmaster']['grvPrimaryCode'] . " all ready exist";
                        $itemExistArray[] = [$itemDrt];
                    }
                }
            }
        }

        //check record exist in General Ledger table
        foreach ($input['detailTable'] as $itemExist) {

            if (isset($itemExist['isChecked']) && $itemExist['isChecked']) {
                $siDetailExistGL = GeneralLedger::where('documentSystemID', 3)
                    ->where('documentSystemCode', $itemExist['grvAutoID'])
                    ->first();

                if (empty($siDetailExistGL)) {
                    $itemDrt = "Selected GRV ".$itemExist['grvmaster']['grvPrimaryCode']." is not updated in general ledger. Please check again";
                    $itemExistArray[] = [$itemDrt];
                }
            }
        }

        //check total matching
        foreach ($input['detailTable'] as $temp) {

            $groupMasterCheck = UnbilledGrvGroupBy::find($temp['unbilledgrvAutoID']);

            if (isset($temp['isChecked']) && $temp['isChecked']) {

                $balanceAmount = collect(\DB::select('SELECT erp_bookinvsuppdet.unbilledgrvAutoID, Sum(erp_bookinvsuppdet.totTransactionAmount) AS SumOftotTransactionAmount FROM erp_bookinvsuppdet WHERE unbilledgrvAutoID = ' . $temp['unbilledgrvAutoID'] . ' GROUP BY erp_bookinvsuppdet.unbilledgrvAutoID;'))->first();

                if($balanceAmount){
                    if (($groupMasterCheck->totTransactionAmount == $balanceAmount->SumOftotTransactionAmount) ||  ($balanceAmount->SumOftotTransactionAmount > $groupMasterCheck->totTransactionAmount)) {
                        $itemDrt = "Selected ".$temp['grvmaster']['grvPrimaryCode']." GRV has been booked fully. Please check again";
                        $itemExistArray[] = [$itemDrt];
                    }
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
                    $totalPendingAmount = ($groupMaster->totTransactionAmount - $balanceAmount['SumOftotTransactionAmount']);
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
