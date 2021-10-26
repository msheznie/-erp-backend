<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateSupplierInvoiceItemDetailAPIRequest;
use App\Http\Requests\API\UpdateSupplierInvoiceItemDetailAPIRequest;
use App\Models\SupplierInvoiceItemDetail;
use App\Models\BookInvSuppMaster;
use App\Models\BookInvSuppDet;
use App\Models\UnbilledGrvGroupBy;
use App\Models\PoAdvancePayment;
use App\Models\GRVDetails;
use App\Models\Company;
use App\Models\SupplierAssigned;
use App\Repositories\SupplierInvoiceItemDetailRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use Illuminate\Support\Facades\DB;
use App\helper\TaxService;

/**
 * Class SupplierInvoiceItemDetailController
 * @package App\Http\Controllers\API
 */

class SupplierInvoiceItemDetailAPIController extends AppBaseController
{
    /** @var  SupplierInvoiceItemDetailRepository */
    private $supplierInvoiceItemDetailRepository;

    public function __construct(SupplierInvoiceItemDetailRepository $supplierInvoiceItemDetailRepo)
    {
        $this->supplierInvoiceItemDetailRepository = $supplierInvoiceItemDetailRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/supplierInvoiceItemDetails",
     *      summary="Get a listing of the SupplierInvoiceItemDetails.",
     *      tags={"SupplierInvoiceItemDetail"},
     *      description="Get all SupplierInvoiceItemDetails",
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
     *                  @SWG\Items(ref="#/definitions/SupplierInvoiceItemDetail")
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
        $this->supplierInvoiceItemDetailRepository->pushCriteria(new RequestCriteria($request));
        $this->supplierInvoiceItemDetailRepository->pushCriteria(new LimitOffsetCriteria($request));
        $supplierInvoiceItemDetails = $this->supplierInvoiceItemDetailRepository->all();

        return $this->sendResponse($supplierInvoiceItemDetails->toArray(), 'Supplier Invoice Item Details retrieved successfully');
    }

    /**
     * @param CreateSupplierInvoiceItemDetailAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/supplierInvoiceItemDetails",
     *      summary="Store a newly created SupplierInvoiceItemDetail in storage",
     *      tags={"SupplierInvoiceItemDetail"},
     *      description="Store SupplierInvoiceItemDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="SupplierInvoiceItemDetail that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/SupplierInvoiceItemDetail")
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
     *                  ref="#/definitions/SupplierInvoiceItemDetail"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateSupplierInvoiceItemDetailAPIRequest $request)
    {
        $input = $request->all();

        $supplierInvoiceItemDetail = $this->supplierInvoiceItemDetailRepository->create($input);

        return $this->sendResponse($supplierInvoiceItemDetail->toArray(), 'Supplier Invoice Item Detail saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/supplierInvoiceItemDetails/{id}",
     *      summary="Display the specified SupplierInvoiceItemDetail",
     *      tags={"SupplierInvoiceItemDetail"},
     *      description="Get SupplierInvoiceItemDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SupplierInvoiceItemDetail",
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
     *                  ref="#/definitions/SupplierInvoiceItemDetail"
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
        /** @var SupplierInvoiceItemDetail $supplierInvoiceItemDetail */
        $supplierInvoiceItemDetail = $this->supplierInvoiceItemDetailRepository->findWithoutFail($id);

        if (empty($supplierInvoiceItemDetail)) {
            return $this->sendError('Supplier Invoice Item Detail not found');
        }

        return $this->sendResponse($supplierInvoiceItemDetail->toArray(), 'Supplier Invoice Item Detail retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateSupplierInvoiceItemDetailAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/supplierInvoiceItemDetails/{id}",
     *      summary="Update the specified SupplierInvoiceItemDetail in storage",
     *      tags={"SupplierInvoiceItemDetail"},
     *      description="Update SupplierInvoiceItemDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SupplierInvoiceItemDetail",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="SupplierInvoiceItemDetail that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/SupplierInvoiceItemDetail")
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
     *                  ref="#/definitions/SupplierInvoiceItemDetail"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateSupplierInvoiceItemDetailAPIRequest $request)
    {
        $input = $request->all();

        /** @var SupplierInvoiceItemDetail $supplierInvoiceItemDetail */
        $supplierInvoiceItemDetail = $this->supplierInvoiceItemDetailRepository->findWithoutFail($id);

        if (empty($supplierInvoiceItemDetail)) {
            return $this->sendError('Supplier Invoice Item Detail not found');
        }

        $supplierInvoiceItemDetail = $this->supplierInvoiceItemDetailRepository->update($input, $id);

        return $this->sendResponse($supplierInvoiceItemDetail->toArray(), 'SupplierInvoiceItemDetail updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/supplierInvoiceItemDetails/{id}",
     *      summary="Remove the specified SupplierInvoiceItemDetail from storage",
     *      tags={"SupplierInvoiceItemDetail"},
     *      description="Delete SupplierInvoiceItemDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SupplierInvoiceItemDetail",
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
        /** @var SupplierInvoiceItemDetail $supplierInvoiceItemDetail */
        $supplierInvoiceItemDetail = $this->supplierInvoiceItemDetailRepository->findWithoutFail($id);

        if (empty($supplierInvoiceItemDetail)) {
            return $this->sendError('Supplier Invoice Item Detail not found');
        }

        $supplierInvoiceItemDetail->delete();

        return $this->sendSuccess('Supplier Invoice Item Detail deleted successfully');
    }

    public function getGRVDetailsForSupplierInvoice(Request $request)
    {
        $input = $request->all();

        $bookingSupInvoiceDetAutoID = $input['bookingSupInvoiceDetAutoID'];

        $bookInvSuppDetail = BookInvSuppDet::find($bookingSupInvoiceDetAutoID);

        $groupMaster = UnbilledGrvGroupBy::find($bookInvSuppDetail->unbilledgrvAutoID);

        $bookingSuppMasInvAutoID = $input['bookingSuppMasInvAutoID'];

        $bookInvSuppMaster = BookInvSuppMaster::find($bookingSuppMasInvAutoID);

        if (empty($bookInvSuppMaster)) {
            return $this->sendError('Supplier Invoice not found');
        }

        $company = Company::where('companySystemID', $bookInvSuppMaster->companySystemID)->first();
        $supplierAssignedDetail = SupplierAssigned::where('supplierCodeSytem', $bookInvSuppMaster->supplierID)
                                                    ->where('companySystemID', $bookInvSuppMaster->companySystemID)
                                                    ->first();
        $valEligible = false;
        if ($company->vatRegisteredYN == 1 || $supplierAssignedDetail->vatEligible == 1) {
            $valEligible = true;
        }

        $rcmActivated = TaxService::isGRVRCMActivation($bookInvSuppDetail->grvAutoID);

        if ($groupMaster->logisticYN) {
            $pulledQry = DB::table('erp_bookinvsupp_item_det')
                                ->selectRaw("SUM(totTransactionAmount) as SumOftotTransactionAmount, logisticID")
                                ->where('erp_bookinvsupp_item_det.bookingSupInvoiceDetAutoID', '!=', $bookingSupInvoiceDetAutoID)
                                ->where(function($query) {
                                    $query->where('logisticID', 0)
                                          ->orWhereNotNull('logisticID');
                                })
                                ->groupBy('logisticID');

            $grvDetails = PoAdvancePayment::selectRaw("0 as grvDetailsID, poAdvPaymentID as logisticID, itemmaster.primaryCode as itemPrimaryCode, itemmaster.itemDescription as itemDescription, erp_tax_vat_sub_categories.mainCategory as vatMasterCategoryID, erp_purchaseorderadvpayment.vatSubCategoryID, 0 as exempt_vat_portion, ROUND(((reqAmountTransCur_amount) + (erp_purchaseorderadvpayment.VATAmount)),7) as transactionAmount, ROUND(((reqAmountInPORptCur) + (erp_purchaseorderadvpayment.VATAmountRpt)),7) as rptAmount, ROUND(((reqAmountInPOLocalCur) + (erp_purchaseorderadvpayment.VATAmountLocal)),7) as localAmount, ROUND(((reqAmountTransCur_amount) + (erp_purchaseorderadvpayment.VATAmount) - IFNULL(pulledQry.SumOftotTransactionAmount,0)),7) as balanceAmount, ROUND(((reqAmountTransCur_amount) + (erp_purchaseorderadvpayment.VATAmount) - IFNULL(pulledQry.SumOftotTransactionAmount,0)),7) as balanceAmountCheck, erp_bookinvsupp_item_det.supplierInvoAmount")
                                                    ->leftJoin('erp_grvmaster', 'erp_purchaseorderadvpayment.grvAutoID', '=', 'erp_grvmaster.grvAutoID')
                                                    ->leftJoin('erp_tax_vat_sub_categories', 'erp_purchaseorderadvpayment.vatSubCategoryID', '=', 'erp_tax_vat_sub_categories.taxVatSubCategoriesAutoID')
                                                    ->leftJoin('erp_purchaseordermaster', 'erp_purchaseorderadvpayment.poID', '=', 'erp_purchaseordermaster.purchaseOrderID')
                                                    ->join('erp_addoncostcategories', 'erp_purchaseorderadvpayment.logisticCategoryID', '=', 'erp_addoncostcategories.idaddOnCostCategories')
                                                    ->join('itemmaster', 'itemmaster.itemCodeSystem', '=', 'erp_addoncostcategories.itemSystemCode')
                                                    ->leftJoin(\DB::raw("({$pulledQry->toSql()}) as pulledQry"), function($join) use ($pulledQry){
                                                        $join->mergeBindings($pulledQry)
                                                             ->on('pulledQry.logisticID', '=', 'erp_purchaseorderadvpayment.poAdvPaymentID');
                                                   })
                                                    ->leftJoin('erp_bookinvsupp_item_det', function($join) use ($bookingSupInvoiceDetAutoID) {
                                                        $join->on('erp_bookinvsupp_item_det.logisticID', '=', 'erp_purchaseorderadvpayment.poAdvPaymentID')
                                                             ->where('erp_bookinvsupp_item_det.bookingSupInvoiceDetAutoID', $bookingSupInvoiceDetAutoID);
                                                   })
                                                    ->where('erp_purchaseorderadvpayment.grvAutoID', $bookInvSuppDetail->grvAutoID)
                                                    ->where('erp_purchaseorderadvpayment.supplierID',$bookInvSuppMaster->supplierID)
                                                    ->groupBy('erp_purchaseorderadvpayment.poAdvPaymentID');            
        } else {

            $pulledQry = DB::table('erp_bookinvsupp_item_det')
                            ->selectRaw("SUM(totTransactionAmount) as SumOftotTransactionAmount, grvDetailsID")
                            ->where('erp_bookinvsupp_item_det.bookingSupInvoiceDetAutoID', '!=', $bookingSupInvoiceDetAutoID)
                            ->groupBy('grvDetailsID');



            $grvDetails = GRVDetails::where('erp_grvdetails.grvAutoID', $bookInvSuppDetail->grvAutoID)
                                   ->leftJoin(\DB::raw("({$pulledQry->toSql()}) as pulledQry"), function($join) use ($pulledQry){
                                        $join->mergeBindings($pulledQry)
                                             ->on('pulledQry.grvDetailsID', '=', 'erp_grvdetails.grvDetailsID');
                                   })
                                   ->leftJoin('erp_bookinvsupp_item_det', function($join) use ($bookingSupInvoiceDetAutoID) {
                                        $join->on('erp_bookinvsupp_item_det.grvDetailsID', '=', 'erp_grvdetails.grvDetailsID')
                                             ->where('erp_bookinvsupp_item_det.bookingSupInvoiceDetAutoID', $bookingSupInvoiceDetAutoID);
                                   });

            if ($valEligible && !$rcmActivated) {
                $grvDetails = $grvDetails->selectRaw('erp_bookinvsupp_item_det.supplierInvoAmount, erp_grvdetails.grvDetailsID, itemPrimaryCode, itemDescription, erp_grvdetails.vatMasterCategoryID, erp_grvdetails.vatSubCategoryID, erp_grvdetails.exempt_vat_portion, ROUND(((GRVcostPerUnitSupTransCur*noQty) + (erp_grvdetails.VATAmount*noQty)),7) as transactionAmount, ROUND(((GRVcostPerUnitComRptCur*noQty) + (erp_grvdetails.VATAmountRpt*noQty)),7) as rptAmount, ROUND(((GRVcostPerUnitLocalCur*noQty) + (erp_grvdetails.VATAmountRpt*noQty)),7) as localAmount, ROUND(((GRVcostPerUnitSupTransCur*noQty) + (erp_grvdetails.VATAmount*noQty) - IFNULL(pulledQry.SumOftotTransactionAmount,0)),7) as balanceAmount, ROUND(((GRVcostPerUnitSupTransCur*noQty) + (erp_grvdetails.VATAmount*noQty) - IFNULL(pulledQry.SumOftotTransactionAmount,0)),7) as balanceAmountCheck');
            } else {
                $grvDetails = $grvDetails->selectRaw('erp_bookinvsupp_item_det.supplierInvoAmount, erp_grvdetails.grvDetailsID, itemPrimaryCode, itemDescription, erp_grvdetails.vatMasterCategoryID, erp_grvdetails.vatSubCategoryID, erp_grvdetails.exempt_vat_portion, ROUND(((GRVcostPerUnitSupTransCur*noQty)),7) as transactionAmount, ROUND(((GRVcostPerUnitComRptCur*noQty)),7) as rptAmount, ROUND(((GRVcostPerUnitLocalCur*noQty)),7) as localAmount, ROUND(((GRVcostPerUnitSupTransCur*noQty) - IFNULL(pulledQry.SumOftotTransactionAmount,0)),7) as balanceAmount, ROUND(((GRVcostPerUnitSupTransCur*noQty) - IFNULL(pulledQry.SumOftotTransactionAmount,0)),7) as balanceAmountCheck');
            }
        }

        $grvDetails = $grvDetails->get(); 

        return $this->sendResponse(['grvDetails' => $grvDetails, 'logisticYN' => $groupMaster->logisticYN], 'Supplier Invoice Item Detail retrieved successfully');
    }
}
