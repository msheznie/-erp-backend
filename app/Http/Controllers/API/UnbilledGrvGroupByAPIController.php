<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateUnbilledGrvGroupByAPIRequest;
use App\Http\Requests\API\UpdateUnbilledGrvGroupByAPIRequest;
use App\Models\GRVDetails;
use App\Models\PoAdvancePayment;
use App\Models\BookInvSuppMaster;
use App\Models\UnbilledGrvGroupBy;
use App\Models\Company;
use App\Models\SupplierAssigned;
use App\Repositories\UnbilledGrvGroupByRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Response;
use App\helper\TaxService;

/**
 * Class UnbilledGrvGroupByController
 * @package App\Http\Controllers\API
 */
class UnbilledGrvGroupByAPIController extends AppBaseController
{
    /** @var  UnbilledGrvGroupByRepository */
    private $unbilledGrvGroupByRepository;

    public function __construct(UnbilledGrvGroupByRepository $unbilledGrvGroupByRepo)
    {
        $this->unbilledGrvGroupByRepository = $unbilledGrvGroupByRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/unbilledGrvGroupBies",
     *      summary="Get a listing of the UnbilledGrvGroupBies.",
     *      tags={"UnbilledGrvGroupBy"},
     *      description="Get all UnbilledGrvGroupBies",
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
     *                  @SWG\Items(ref="#/definitions/UnbilledGrvGroupBy")
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
        $this->unbilledGrvGroupByRepository->pushCriteria(new RequestCriteria($request));
        $this->unbilledGrvGroupByRepository->pushCriteria(new LimitOffsetCriteria($request));
        $unbilledGrvGroupBies = $this->unbilledGrvGroupByRepository->all();

        return $this->sendResponse($unbilledGrvGroupBies->toArray(), trans('custom.unbilled_grv_group_bies_retrieved_successfully'));
    }

    /**
     * @param CreateUnbilledGrvGroupByAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/unbilledGrvGroupBies",
     *      summary="Store a newly created UnbilledGrvGroupBy in storage",
     *      tags={"UnbilledGrvGroupBy"},
     *      description="Store UnbilledGrvGroupBy",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="UnbilledGrvGroupBy that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/UnbilledGrvGroupBy")
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
     *                  ref="#/definitions/UnbilledGrvGroupBy"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateUnbilledGrvGroupByAPIRequest $request)
    {
        $input = $request->all();

        $unbilledGrvGroupBies = $this->unbilledGrvGroupByRepository->create($input);

        return $this->sendResponse($unbilledGrvGroupBies->toArray(), trans('custom.unbilled_grv_group_by_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/unbilledGrvGroupBies/{id}",
     *      summary="Display the specified UnbilledGrvGroupBy",
     *      tags={"UnbilledGrvGroupBy"},
     *      description="Get UnbilledGrvGroupBy",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of UnbilledGrvGroupBy",
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
     *                  ref="#/definitions/UnbilledGrvGroupBy"
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
        /** @var UnbilledGrvGroupBy $unbilledGrvGroupBy */
        $unbilledGrvGroupBy = $this->unbilledGrvGroupByRepository->findWithoutFail($id);

        if (empty($unbilledGrvGroupBy)) {
            return $this->sendError(trans('custom.unbilled_grv_group_by_not_found'));
        }

        return $this->sendResponse($unbilledGrvGroupBy->toArray(), trans('custom.unbilled_grv_group_by_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateUnbilledGrvGroupByAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/unbilledGrvGroupBies/{id}",
     *      summary="Update the specified UnbilledGrvGroupBy in storage",
     *      tags={"UnbilledGrvGroupBy"},
     *      description="Update UnbilledGrvGroupBy",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of UnbilledGrvGroupBy",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="UnbilledGrvGroupBy that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/UnbilledGrvGroupBy")
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
     *                  ref="#/definitions/UnbilledGrvGroupBy"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateUnbilledGrvGroupByAPIRequest $request)
    {
        $input = $request->all();

        /** @var UnbilledGrvGroupBy $unbilledGrvGroupBy */
        $unbilledGrvGroupBy = $this->unbilledGrvGroupByRepository->findWithoutFail($id);

        if (empty($unbilledGrvGroupBy)) {
            return $this->sendError(trans('custom.unbilled_grv_group_by_not_found'));
        }

        $unbilledGrvGroupBy = $this->unbilledGrvGroupByRepository->update($input, $id);

        return $this->sendResponse($unbilledGrvGroupBy->toArray(), trans('custom.unbilledgrvgroupby_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/unbilledGrvGroupBies/{id}",
     *      summary="Remove the specified UnbilledGrvGroupBy from storage",
     *      tags={"UnbilledGrvGroupBy"},
     *      description="Delete UnbilledGrvGroupBy",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of UnbilledGrvGroupBy",
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
        /** @var UnbilledGrvGroupBy $unbilledGrvGroupBy */
        $unbilledGrvGroupBy = $this->unbilledGrvGroupByRepository->findWithoutFail($id);

        if (empty($unbilledGrvGroupBy)) {
            return $this->sendError(trans('custom.unbilled_grv_group_by_not_found'));
        }

        $unbilledGrvGroupBy->delete();

        return $this->sendResponse($id, trans('custom.unbilled_grv_group_by_deleted_successfully'));
    }

    public function getPurchaseOrderForSI(Request $request)
    {
        $input = $request->all();
        $companyID = $input['companySystemID'];

        $bookingSuppMasInvAutoID = $input['bookingSuppMasInvAutoID'];

        $bookInvSuppMaster = BookInvSuppMaster::find($bookingSuppMasInvAutoID);

        if (empty($bookInvSuppMaster)) {
            return $this->sendError(trans('custom.supplier_invoice_not_found'));
        }

        $bookingDate = Carbon::parse($bookInvSuppMaster->bookingDate)->format('Y-m-d');

        $unbilledGrvGroupBy = UnbilledGrvGroupBy::whereHas('grvmaster', function ($query) use ($bookInvSuppMaster){
                                                    $query->where('approved', -1);
                                                    $query->where('grvCancelledYN', 0)
                                                          ->when($bookInvSuppMaster->documentType == 2, function($query) {
                                                            $query->where('grvTypeID', 1);
                                                          });
                                                })->when($bookInvSuppMaster->documentType == 0, function($query) {
                                                            $query->whereHas('pomaster', function ($query) {
                                                                $query->where('approved', -1);
                                                                $query->where('poCancelledYN', 0);
                                                            });
                                                    })->with(['pomaster', 'grvmaster'])
                                                    ->where('companySystemID', $companyID)
                                                    ->where('fullyBooked', '<>', 2)
                                                    ->where('supplierID', $bookInvSuppMaster->supplierID)
                                                    ->where('supplierTransactionCurrencyID', $bookInvSuppMaster->supplierTransactionCurrencyID)
                                                    ->whereDate('grvDate', '<=', $bookingDate)
                                                    ->whereNull('purhaseReturnAutoID')
                                                    ->when($bookInvSuppMaster->documentType == 0, function($query) {
                                                        $query->groupBy('purchaseOrderID')
                                                              ->orderBy('purchaseOrderID', 'DESC');
                                                    })
                                                    ->when($bookInvSuppMaster->documentType == 2, function($query) {
                                                        $query->groupBy('grvAutoID')
                                                              ->orderBy('grvAutoID', 'DESC');
                                                    })
                                                    ->get();

        return $this->sendResponse($unbilledGrvGroupBy->toArray(), trans('custom.masters_retrieved_successfully'));
    }


    public function getUnbilledGRVDetailsForSI(Request $request)
    {
        $input = $request->all();
        $companyID = $input['companyId'];
        $purchaseOrderID = isset($input['poID']) ? $input['poID'] : 0;
        $grvAutoID = isset($input['grvAutoID']) ? $input['grvAutoID'] : 0;

        $bookingSuppMasInvAutoID = $input['bookingSuppMasInvAutoID'];

        $bookInvSuppMaster = BookInvSuppMaster::find($bookingSuppMasInvAutoID);

        if (empty($bookInvSuppMaster)) {
            return $this->sendError(trans('custom.supplier_invoice_not_found'));
        }

        if(isset($input['type']) &&  $input['type'] != $bookInvSuppMaster->documentType)
        {
            return $this->sendError(trans('custom.the_invoice_type_and_details_have_already_been_mod'));

        }
        $unbilledFilter = "";
        if ($purchaseOrderID > 0) {
            $unbilledFilter = 'AND unbilledMaster.purchaseOrderID = ' . $purchaseOrderID ;
        } else if ($grvAutoID > 0) {
            $unbilledFilter = 'AND unbilledMaster.grvAutoID = ' . $grvAutoID ;
        }

        $bookingDate = Carbon::parse($bookInvSuppMaster->bookingDate)->format('Y-m-d');

        $unbilledGrvGroupBy = DB::select('SELECT
	grvmaster.grvPrimaryCode,
	unbilledMaster.unbilledgrvAutoID,
	unbilledMaster.totTransactionAmount,
	unbilledMaster.companySystemID,
	unbilledMaster.grvAutoID,
	unbilledMaster.purchaseOrderID,
	unbilledMaster.supplierID,
    unbilledMaster.logisticYN,
    currency.DecimalPlaces,
    IFNULL(bookdetail.SumOftotTransactionAmount,0) as invoicedAmount,
    (unbilledMaster.totTransactionAmount - (IFNULL(bookdetail.SumOftotTransactionAmount,0))) as balanceAmount,
    (unbilledMaster.totTransactionAmount - (IFNULL(bookdetail.SumOftotTransactionAmount,0))) as balanceAmountCheck
FROM
	erp_unbilledgrvgroupby AS unbilledMaster
INNER JOIN currencymaster AS currency ON unbilledMaster.supplierTransactionCurrencyID = currency.currencyID
INNER JOIN erp_grvmaster AS grvmaster ON unbilledMaster.grvAutoID = grvmaster.grvAutoID
AND grvmaster.interCompanyTransferYN = 0
LEFT JOIN (
	SELECT
		erp_bookinvsuppdet.unbilledgrvAutoID,
		IFNULL(
			Sum(
				erp_bookinvsuppdet.totTransactionAmount
			),
			0
		) AS SumOftotTransactionAmount
	FROM
		erp_bookinvsuppdet
	GROUP BY
		erp_bookinvsuppdet.unbilledgrvAutoID
) AS bookdetail ON bookdetail.unbilledgrvAutoID = unbilledMaster.unbilledgrvAutoID
WHERE
	unbilledMaster.companySystemID = ' . $companyID . '
AND unbilledMaster.fullyBooked <> 2
AND unbilledMaster.selectedForBooking = 0
AND unbilledMaster.purhaseReturnAutoID IS NULL
AND unbilledMaster.supplierID = ' . $bookInvSuppMaster->supplierID . '
AND unbilledMaster.supplierTransactionCurrencyID = ' . $bookInvSuppMaster->supplierTransactionCurrencyID . '
AND DATE_FORMAT(unbilledMaster.grvDate,"%Y-%m-%d") <= "' . $bookingDate . '"
' . $unbilledFilter . '
HAVING ROUND(
			unbilledMaster.totTransactionAmount,
			currency.DecimalPlaces
		) > 0 AND ROUND(balanceAmount,currency.DecimalPlaces) > 0');

        $company = Company::where('companySystemID', $bookInvSuppMaster->companySystemID)->first();
        $supplierAssignedDetail = SupplierAssigned::where('supplierCodeSytem', $bookInvSuppMaster->supplierID)
                                                    ->where('companySystemID', $bookInvSuppMaster->companySystemID)
                                                    ->first();
        $valEligible = false;
        if ($company->vatRegisteredYN == 1 || $supplierAssignedDetail->vatEligible == 1) {
            $valEligible = true;
        }

        foreach ($unbilledGrvGroupBy as $key => $value) {
            if ($value->logisticYN) {
                $pulledQry = DB::table('erp_bookinvsupp_item_det')
                                ->selectRaw("SUM(totTransactionAmount) as SumOftotTransactionAmount, logisticID")
                                ->where(function($query) {
                                    $query->where('logisticID', 0)
                                          ->orWhereNotNull('logisticID');
                                })
                                ->groupBy('logisticID');

                $returnedLogistic = DB::table('purchase_return_logistic')
                                        ->selectRaw("(SUM(logisticAmountTrans) + SUM(logisticVATAmount)) as prLogisticAmount, poAdvPaymentID")
                                        ->groupBy('poAdvPaymentID');

                $grvDetails = PoAdvancePayment::selectRaw("0 as grvDetailsID, erp_purchaseorderadvpayment.poAdvPaymentID as logisticID, 
                                                    itemmaster.primaryCode as itemPrimaryCode, itemmaster.itemDescription as itemDescription, 
                                                    erp_tax_vat_sub_categories.mainCategory as vatMasterCategoryID, 
                                                    erp_purchaseorderadvpayment.vatSubCategoryID, 0 as exempt_vat_portion, 
                                                    ROUND(((reqAmountTransCur_amount)),7) as transactionAmount, 
                                                    ROUND(((reqAmountInPORptCur)),7) as rptAmount, 
                                                    ROUND(((reqAmountInPOLocalCur)),7) as localAmount, 
                                                    ROUND(((reqAmountTransCur_amount) - IFNULL(pulledQry.SumOftotTransactionAmount,0)
                                                     - IFNULL(returnedLogistic.prLogisticAmount,0)),7) as balanceAmount, 
                                                     ROUND(((reqAmountTransCur_amount) - IFNULL(pulledQry.SumOftotTransactionAmount,0)
                                                      - IFNULL(returnedLogistic.prLogisticAmount,0)),7) as balanceAmountCheck, 
                                                      IFNULL(pulledQry.SumOftotTransactionAmount,0) as invoicedAmount,
                                                      erp_purchaseorderadvpayment.VATAmount,
                                                      erp_purchaseorderadvpayment.VATAmountLocal,
                                                      erp_purchaseorderadvpayment.VATAmountRpt")
                                                    ->leftJoin('erp_grvmaster', 'erp_purchaseorderadvpayment.grvAutoID', '=', 'erp_grvmaster.grvAutoID')
                                                    ->leftJoin('erp_tax_vat_sub_categories', 'erp_purchaseorderadvpayment.vatSubCategoryID', '=', 'erp_tax_vat_sub_categories.taxVatSubCategoriesAutoID')
                                                    ->leftJoin('erp_purchaseordermaster', 'erp_purchaseorderadvpayment.poID', '=', 'erp_purchaseordermaster.purchaseOrderID')
                                                    ->join('erp_addoncostcategories', 'erp_purchaseorderadvpayment.logisticCategoryID', '=', 'erp_addoncostcategories.idaddOnCostCategories')
                                                    ->join('itemmaster', 'itemmaster.itemCodeSystem', '=', 'erp_addoncostcategories.itemSystemCode')
                                                    ->leftJoin(\DB::raw("({$pulledQry->toSql()}) as pulledQry"), function($join) use ($pulledQry){
                                                        $join->mergeBindings($pulledQry)
                                                             ->on('pulledQry.logisticID', '=', 'erp_purchaseorderadvpayment.poAdvPaymentID');
                                                   })
                                                    ->leftJoin(\DB::raw("({$returnedLogistic->toSql()}) as returnedLogistic"), function($join) use ($returnedLogistic){
                                                        $join->mergeBindings($returnedLogistic)
                                                             ->on('returnedLogistic.poAdvPaymentID', '=', 'erp_purchaseorderadvpayment.poAdvPaymentID');
                                                   })
                                                    ->where('erp_purchaseorderadvpayment.grvAutoID', $value->grvAutoID)
                                                    ->when($purchaseOrderID > 0, function($query) use($value){
                                                        $query->where('poID', $value->purchaseOrderID);
                                                    })
                                                    ->where('erp_purchaseorderadvpayment.supplierID',$value->supplierID)
                                                    ->where('erp_purchaseorderadvpayment.currencyID',$bookInvSuppMaster->supplierTransactionCurrencyID)
                                                    ->groupBy('erp_purchaseorderadvpayment.poAdvPaymentID');

                $grv_details = $grvDetails->get();

                foreach ($grv_details as $key => $valueGrv) {
                    $valueGrv->transactionAmount = $valueGrv->transactionAmount + $valueGrv->VATAmount;
                    $valueGrv->rptAmount = $valueGrv->rptAmount + $valueGrv->VATAmountRpt;
                    $valueGrv->localAmount = $valueGrv->localAmount + $valueGrv->VATAmountLocal;
                    
                    $valueGrv->balanceAmount = $valueGrv->balanceAmount + $valueGrv->VATAmount;
                    $valueGrv->balanceAmountCheck = $valueGrv->balanceAmountCheck + $valueGrv->VATAmount;
                }

                $value->balanceAmount = collect($grv_details)->sum('balanceAmount');
                $value->balanceAmountCheck = collect($grv_details)->sum('balanceAmount');

                $value->grv_details = $grv_details;
            } else {
                $rcmActivated = TaxService::isGRVRCMActivation($value->grvAutoID);

                $pulledQry = DB::table('erp_bookinvsupp_item_det')
                                ->selectRaw("SUM(totTransactionAmount) as SumOftotTransactionAmount, grvDetailsID")
                                ->groupBy('grvDetailsID');



                $grvDetails = GRVDetails::when($purchaseOrderID > 0, function($query) use($value){
                                            $query->where('purchaseOrderMastertID', $value->purchaseOrderID);
                                        })
                                       ->where('grvAutoID', $value->grvAutoID)
                                       ->leftJoin(\DB::raw("({$pulledQry->toSql()}) as pulledQry"), function($join) use ($pulledQry){
                                            $join->mergeBindings($pulledQry)
                                                 ->on('pulledQry.grvDetailsID', '=', 'erp_grvdetails.grvDetailsID');
                                       });

                if ($valEligible && !$rcmActivated) {
                    $grvDetails = $grvDetails->selectRaw('erp_grvdetails.grvDetailsID, itemPrimaryCode, itemDescription, vatMasterCategoryID, vatSubCategoryID, exempt_vat_portion, ROUND(((GRVcostPerUnitSupTransCur*noQty) + (VATAmount*noQty)),7) as transactionAmount, ROUND(((GRVcostPerUnitComRptCur*noQty) + (VATAmountRpt*noQty)),7) as rptAmount, ROUND(((GRVcostPerUnitLocalCur*noQty) + (VATAmountRpt*noQty)),7) as localAmount, ROUND((((GRVcostPerUnitSupTransCur*noQty) + (VATAmount*noQty) - IFNULL(pulledQry.SumOftotTransactionAmount,0)) - ((ROUND(((GRVcostPerUnitSupTransCur*noQty) + (VATAmount*noQty)),7) / noQty) * returnQty)),7) as balanceAmount, ROUND((((GRVcostPerUnitSupTransCur*noQty) + (VATAmount*noQty) - IFNULL(pulledQry.SumOftotTransactionAmount,0)) - ((ROUND(((GRVcostPerUnitSupTransCur*noQty) + (VATAmount*noQty)),7) / noQty) * returnQty)),7) as balanceAmountCheck, 0 as logisticID, IFNULL(pulledQry.SumOftotTransactionAmount,0) as invoicedAmount, noQty, returnQty');
                    
                    $grv_details = $grvDetails->get();
                
                    foreach ($grv_details as $key1 => $value1) {
                        $res = TaxService::processGRVDetailVATForUnbilled($value1->grvDetailsID);

                        $value1->transactionAmount = $res['totalTransAmount'];
                        $value1->rptAmount = $res['totalRptAmount'];
                        $value1->localAmount = $res['totalLocalAmount'];

                        $value1->balanceAmount = $value1->transactionAmount - $value1->invoicedAmount - round(($value1->transactionAmount / $value1->noQty) * $value1->returnQty, 7);
                        $value1->balanceAmountCheck = $value1->transactionAmount - $value1->invoicedAmount - round(($value1->transactionAmount / $value1->noQty) * $value1->returnQty, 7);
                    }
                } else {
                    $grvDetails = $grvDetails->selectRaw('erp_grvdetails.grvDetailsID, itemPrimaryCode, itemDescription, vatMasterCategoryID, vatSubCategoryID, exempt_vat_portion, ROUND(((GRVcostPerUnitSupTransCur*noQty)),7) as transactionAmount, ROUND(((GRVcostPerUnitComRptCur*noQty)),7) as rptAmount, ROUND(((GRVcostPerUnitLocalCur*noQty)),7) as localAmount, ROUND((((GRVcostPerUnitSupTransCur*noQty) - IFNULL(pulledQry.SumOftotTransactionAmount,0)) - ((ROUND(((GRVcostPerUnitSupTransCur*noQty)),7) / noQty) * returnQty)),7) as balanceAmount, ROUND((((GRVcostPerUnitSupTransCur*noQty) - IFNULL(pulledQry.SumOftotTransactionAmount,0)) - ((ROUND(((GRVcostPerUnitSupTransCur*noQty)),7) / noQty) * returnQty)),7) as balanceAmountCheck, 0 as logisticID, IFNULL(pulledQry.SumOftotTransactionAmount,0) as invoicedAmount, noQty, returnQty');
                    
                    $grv_details = $grvDetails->get();


                    foreach ($grv_details as $key1 => $value1) {
                        $res = TaxService::processGRVDetailVATForUnbilled($value1->grvDetailsID);

                        $value1->transactionAmount = $value1->transactionAmount - $res['exemptVATTrans'];
                        $value1->rptAmount = $value1->rptAmount - $res['exemptVATRpt'];
                        $value1->localAmount = $value1->localAmount - $res['exemptVATLocal'];

                        $value1->balanceAmount = $value1->transactionAmount - $value1->invoicedAmount - round(($value1->transactionAmount / $value1->noQty) * $value1->returnQty, 7);
                        $value1->balanceAmountCheck = $value1->transactionAmount - $value1->invoicedAmount - round(($value1->transactionAmount / $value1->noQty) * $value1->returnQty, 7);
                    }
                }
                


                $value->balanceAmount = collect($grv_details)->sum('balanceAmount');
                $value->balanceAmountCheck = collect($grv_details)->sum('balanceAmount');

                $grv_details = collect($grv_details)->where('balanceAmount', '>', 0)->all();

                $value->grv_details = $grv_details;
            }
        }


        $unbilledData = [];
        foreach ($unbilledGrvGroupBy as $key => $value) {
            $temp = [];
            $temp = $value;

            if (isset($value->grv_details)) {
                $grvDetailsData = [];
                foreach ($value->grv_details as $key1 => $value1) {
                    $grvDetailsData[] = $value1;
                }

                $temp->grv_details = $grvDetailsData;
            }

            $unbilledData[] = $temp;
        }

        return $this->sendResponse($unbilledData, trans('custom.purchase_request_details_retrieved_successfully'));

    }
}
