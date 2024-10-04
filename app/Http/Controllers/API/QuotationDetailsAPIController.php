<?php
/**
 * =============================================
 * -- File Name : QuotationDetailsAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  QuotationDetails
 * -- Author : Mohamed Nazir
 * -- Create date : 24 - January 2019
 * -- Description : This file contains the all CRUD for Sales Quotation Details
 * -- REVISION HISTORY
 * -- Date: 24-January 2019 By: Nazir Description: Added new function getSalesQuotationDetails(),
 */

namespace App\Http\Controllers\API;

use App\helper\TaxService;
use App\Http\Requests\API\CreateQuotationDetailsAPIRequest;
use App\Http\Requests\API\UpdateQuotationDetailsAPIRequest;
use App\Models\ItemAssigned;
use App\Models\QuotationDetails;
use App\Models\QuotationMaster;
use App\Models\CompanyPolicyMaster;
use App\Models\FinanceItemcategorySubAssigned;
use App\Models\Unit;
use App\Models\Company;
use App\Models\ItemMaster;
use App\Repositories\QuotationDetailsRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\DB;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Carbon\Carbon;
use Response;

/**
 * Class QuotationDetailsController
 * @package App\Http\Controllers\API
 */
class QuotationDetailsAPIController extends AppBaseController
{
    /** @var  QuotationDetailsRepository */
    private $quotationDetailsRepository;

    public function __construct(QuotationDetailsRepository $quotationDetailsRepo)
    {
        $this->quotationDetailsRepository = $quotationDetailsRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/quotationDetails",
     *      summary="Get a listing of the QuotationDetails.",
     *      tags={"QuotationDetails"},
     *      description="Get all QuotationDetails",
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
     *                  @SWG\Items(ref="#/definitions/QuotationDetails")
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
        $this->quotationDetailsRepository->pushCriteria(new RequestCriteria($request));
        $this->quotationDetailsRepository->pushCriteria(new LimitOffsetCriteria($request));
        $quotationDetails = $this->quotationDetailsRepository->all();

        return $this->sendResponse($quotationDetails->toArray(), 'Quotation Details retrieved successfully');
    }

    /**
     * @param CreateQuotationDetailsAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/quotationDetails",
     *      summary="Store a newly created QuotationDetails in storage",
     *      tags={"QuotationDetails"},
     *      description="Store QuotationDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="QuotationDetails that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/QuotationDetails")
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
     *                  ref="#/definitions/QuotationDetails"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateQuotationDetailsAPIRequest $request)
    {
        $input = array_except($request->all(), 'unit');
        $input = $this->convertArrayToValue($input);

     

        $employee = \Helper::getEmployeeInfo();
        $input['itemAutoID'] = isset( $input['itemAutoID']) ?  $input['itemAutoID'] : 0;

        $companySystemID = isset($input['companySystemID']) ? $input['companySystemID'] : 0;

        if(isset($input['itemCode']['id'])) {
                $item = ItemAssigned::where('itemCodeSystem', $input['itemCode']['id'])
                ->where('companySystemID', $companySystemID)
                ->first();

        }else {
            $item = ItemAssigned::where('itemCodeSystem', $input['itemAutoID'])
            ->where('companySystemID', $companySystemID)
            ->first();
        }


        $category = isset($item->financeCategoryMaster) ? $item->financeCategoryMaster: null;

        if($input['itemAutoID']) {
            $itemExist = QuotationDetails::where('itemAutoID', $input['itemAutoID'])
            ->where('quotationMasterID', $input['quotationMasterID'])
            ->first();

            if(($category != 2 )&& ($category != 4 ))
            {
                if (!empty($itemExist)) {
                    return $this->sendError('Added item already exist');
                }
            }
        }
        else if(isset($input['itemCode']['id']) && $input['itemCode']['id']) 
        {
            $itemExist = QuotationDetails::where('itemAutoID', $input['itemCode']['id'])
            ->where('quotationMasterID', $input['quotationMasterID'])
            ->first();

            if(($category != 2 )&& ($category != 4 ))
            {
                if (!empty($itemExist)) {
                    return $this->sendError('Added item already exist');
                }
            }
        }


        $quotationMasterData = QuotationMaster::find($input['quotationMasterID']);

        if (empty($quotationMasterData)) {
            return $this->sendError('Quotation Master not found');
        }


        if($item) {
            $unitMasterData = Unit::find($item->itemUnitOfMeasure);
            if (empty($unitMasterData)) {
                return $this->sendError('Unit of Measure not found');
            }
            $input['unitOfMeasure'] = $unitMasterData->UnitShortCode;
        }


        $company = Company::where('companySystemID', $input['companySystemID'])->first();
        if (empty($company)) {
            return $this->sendError('Company not found');
        }

        $input['companyID'] = $company->CompanyID;

        $input['itemSystemCode'] = ($item) ? $item->itemPrimaryCode : null;
        $input['itemDescription'] = ($item) ? $item->itemDescription : $input['itemCode'];
        $input['itemCategory'] = ($item) ? $item->financeCategoryMaster : null;
        $input['itemReferenceNo'] = ($item) ? $item->secondaryItemCode : null;
        $input['unitOfMeasureID'] = ($item) ? $item->itemUnitOfMeasure : null;
        $input['wacValueLocal'] = ($item) ? $item->wacValueLocal : null;

        if ($quotationMasterData->documentSystemID == 68) {
            $input['unittransactionAmount'] = round(\Helper::currencyConversion($quotationMasterData->companySystemID, $quotationMasterData->companyLocalCurrencyID, $quotationMasterData->transactionCurrencyID, $item->wacValueLocal)['documentAmount'], $quotationMasterData->transactionCurrencyDecimalPlaces);
        }

        // Get VAT percentage for item
        if ($quotationMasterData->isVatEligible) {
            $vatDetails = TaxService::getVATDetailsByItem($quotationMasterData->companySystemID, $input['itemAutoID'], $quotationMasterData->customerSystemCode,0);
            $input['VATPercentage'] = $vatDetails['percentage'];
            $input['VATApplicableOn'] = $vatDetails['applicableOn'];
            $input['vatMasterCategoryID'] = $vatDetails['vatMasterCategoryID'];
            $input['vatSubCategoryID'] = $vatDetails['vatSubCategoryID'];
            $input['VATAmount'] = 0;
            if (isset($input['unittransactionAmount']) && $input['unittransactionAmount'] > 0) {
                $input['VATAmount'] = (($input['unittransactionAmount'] / 100) * $vatDetails['percentage']);
            }
            $currencyConversionVAT = \Helper::currencyConversion($quotationMasterData->companySystemID, $quotationMasterData->transactionCurrencyID, $quotationMasterData->transactionCurrencyID, $input['VATAmount']);

            $input['VATAmountLocal'] = \Helper::roundValue($currencyConversionVAT['localAmount']);
            $input['VATAmountRpt'] = \Helper::roundValue($currencyConversionVAT['reportingAmount']);
        }

        $input['wacValueReporting'] = ($item) ? $item->wacValueReporting : null;
        $input['createdPCID'] = gethostname();
        $input['createdUserID'] = $employee->empID;
        $input['createdUserName'] = $employee->empName;

        /* check add new item policy */
        $addNewItem = CompanyPolicyMaster::where('companyPolicyCategoryID', 64)
        ->where('companySystemID', $quotationMasterData->companySystemID)
        ->first();

        if(isset($input['itemCode']['id'])) {
            $item = ItemMaster::find($input['itemCode']['id']);
            unset($input['itemCode'], $input['itemDescription']);
            $input['itemAutoID'] = $item->itemCodeSystem;
            $input['itemSystemCode'] = $item->primaryCode;
            $input['itemDescription'] = $item->itemDescription;
            $input['itemCategory'] = $item->financeCategoryMaster;
            $input['defaultUOM'] = $item->unit;
            $input['unitOfMeasureID'] = $item->unit;
        }


        $quotationDetails = $this->quotationDetailsRepository->create($input);

        return $this->sendResponse($quotationDetails->toArray(), 'Quotation Details saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/quotationDetails/{id}",
     *      summary="Display the specified QuotationDetails",
     *      tags={"QuotationDetails"},
     *      description="Get QuotationDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of QuotationDetails",
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
     *                  ref="#/definitions/QuotationDetails"
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
        /** @var QuotationDetails $quotationDetails */
        $quotationDetails = $this->quotationDetailsRepository->findWithoutFail($id);

        if (empty($quotationDetails)) {
            return $this->sendError('Quotation Details not found');
        }

        return $this->sendResponse($quotationDetails->toArray(), 'Quotation Details retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateQuotationDetailsAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/quotationDetails/{id}",
     *      summary="Update the specified QuotationDetails in storage",
     *      tags={"QuotationDetails"},
     *      description="Update QuotationDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of QuotationDetails",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="QuotationDetails that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/QuotationDetails")
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
     *                  ref="#/definitions/QuotationDetails"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateQuotationDetailsAPIRequest $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input, ['vatMasterCategoryID', 'vatSubCategoryID']);
        $employee = \Helper::getEmployeeInfo();

        /** @var QuotationDetails $quotationDetails */
        $quotationDetails = $this->quotationDetailsRepository->findWithoutFail($id);

        
        if (empty($quotationDetails)) {
            return $this->sendError('Quotation Details not found');
        }

        $quotationMasterData = QuotationMaster::find($input['quotationMasterID']);

        if (empty($quotationMasterData)) {
            return $this->sendError('Quotation Master not found');
        }


        if ($quotationMasterData->documentSystemID == 68 && $quotationMasterData->quotationType == 2) {
            $detailSum = QuotationDetails::select(DB::raw('COALESCE(SUM(requestedQty),0) as totalQty'))
                                        ->where('soQuotationDetailID', $quotationDetails->soQuotationDetailID)
                                        ->first();
        }

        // updating transaction amount for local and reporting
        $currencyConversion = \Helper::currencyConversion($input['companySystemID'], $quotationMasterData->transactionCurrencyID, $quotationMasterData->transactionCurrencyID, $input['transactionAmount']);

        $input['companyLocalAmount'] = \Helper::roundValue($currencyConversion['localAmount']);
        $input['companyReportingAmount'] = \Helper::roundValue($currencyConversion['reportingAmount']);

        // adding customer default currencyID base currency conversion

        $currencyConversionDefault = \Helper::currencyConversion($input['companySystemID'], $quotationMasterData->customerCurrencyID, $quotationMasterData->customerCurrencyID, $input['transactionAmount']);

        $input['customerAmount'] = \Helper::roundValue($currencyConversionDefault['documentAmount']);

        $currencyConversionVAT = \Helper::currencyConversion($input['companySystemID'], $quotationMasterData->transactionCurrencyID, $quotationMasterData->transactionCurrencyID, $input['VATAmount']);

        $input['VATAmountLocal'] = \Helper::roundValue($currencyConversionVAT['localAmount']);
        $input['VATAmountRpt'] = \Helper::roundValue($currencyConversionVAT['reportingAmount']);
        $input['VATAmount'] = \Helper::roundValue($input['VATAmount']);

        $validateVATCategories = TaxService::validateVatCategoriesInDocumentDetails($quotationMasterData->documentSystemID, $quotationMasterData->companySystemID, $id, $input);

        if (!$validateVATCategories['status']) {
            return $this->sendError($validateVATCategories['message']);
        } else {
            $input['vatMasterCategoryID'] = $validateVATCategories['vatMasterCategoryID'];        
            $input['vatSubCategoryID'] = $validateVATCategories['vatSubCategoryID'];        
        }

        $input['modifiedDateTime'] = Carbon::now();
        $input['modifiedPCID'] = gethostname();
        $input['modifiedUserID'] = $employee->empID;
        $input['modifiedUserName'] = $employee->empName;

        DB::beginTransaction();
        try {
            $quotationDetailss = $this->quotationDetailsRepository->update($input, $id);


            if ($quotationMasterData->documentSystemID == 68 && $quotationMasterData->quotationType == 2 && ($quotationDetails->requestedQty != $input['requestedQty'])) {
                $this->updateCopiedQty($input);
            }

            DB::commit();
            return $this->sendResponse($quotationDetailss->toArray(), 'Quotation Details updated successfully');
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError('Error Occurred'. $exception->getMessage() . 'Line :' . $exception->getLine());
        }
    }



    public function updateCopiedQty($input)
    {
        $salesOrderID = $input['quotationMasterID'];

        $salesOrder = QuotationMaster::where('quotationMasterID', $salesOrderID)->first();
        $employee = \Helper::getEmployeeInfo();

        DB::beginTransaction();
        try {
            $qoMaster = QuotationMaster::find($input['soQuotationMasterID']);

            $soQuotationMasterID = $input['soQuotationMasterID'];

            //checking the fullyOrdered or partial in po
            $detailSum = QuotationDetails::select(DB::raw('COALESCE(SUM(requestedQty),0) as totalNoQty'))
                                        ->where('soQuotationDetailID', $input['soQuotationDetailID'])
                                        ->first();

            $totalAddedQty = $detailSum['totalNoQty'];


            $quotationDetailData = QuotationDetails::find($input['soQuotationDetailID']);

            if ($quotationDetailData->requestedQty == $totalAddedQty) {
                $fullyOrdered = 2;
            } else {
                $fullyOrdered = 1;
            }

            $new = [];
            $new['qtyIssuedDefaultMeasure'] = $input['requestedQty'];

            $totalNetcost = ($quotationDetailData->unittransactionAmount - $quotationDetailData->discountAmount) * $input['requestedQty'];

            $new['transactionAmount'] = \Helper::roundValue($totalNetcost);

           
            $quotationDetails = $this->quotationDetailsRepository->update($new, $input['quotationDetailsID']);

            QuotationDetails::where('quotationDetailsID', $input['soQuotationDetailID'])
                            ->update(['fullyOrdered' => $fullyOrdered, 'soQuantity' => $totalAddedQty]);

            //check all details fullyOrdered in PR Master
            $quoMasterfullyOrdered = QuotationDetails::where('quotationMasterID', $soQuotationMasterID)
                ->whereIn('fullyOrdered', [1, 0])
                ->get()->toArray();

            if (empty($quoMasterfullyOrdered)) {
                $updateQuotation = QuotationMaster::find($soQuotationMasterID)
                    ->update([
                        'selectedForSalesOrder' => -1,
                        'closedYN' => -1,
                    ]);
            } else {
                $updateQuotation = QuotationMaster::find($soQuotationMasterID)
                    ->update([
                        'selectedForSalesOrder' => 0,
                        'closedYN' => 0,
                    ]);
            }

            $this->updateSalesQuotationOrderStatus($soQuotationMasterID);

            DB::commit();
            return $this->sendResponse([], 'Sales Order Details saved successfully');
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError('Error Occurred'. $exception->getMessage() . 'Line :' . $exception->getLine());
        }

    }
    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/quotationDetails/{id}",
     *      summary="Remove the specified QuotationDetails from storage",
     *      tags={"QuotationDetails"},
     *      description="Delete QuotationDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of QuotationDetails",
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
        /** @var QuotationDetails $quotationDetails */
        $quotationDetails = $this->quotationDetailsRepository->findWithoutFail($id);

        if (empty($quotationDetails)) {
            return $this->sendError('Quotation Details not found');
        }

        $quotationMaster = QuotationMaster::find($quotationDetails->quotationMasterID);
        if(!empty($quotationMaster)){
            $quotationDetails->delete();

            if($quotationMaster->quotationType == 2 && $quotationMaster->documentSystemID == 68){

                if (!empty($quotationDetails->quotationDetailsID) && !empty($quotationDetails->quotationMasterID)) {
                    $updateQuotationMaster = QuotationMaster::find($quotationDetails->quotationMasterID)
                                                            ->update([
                                                                'selectedForSalesOrder' => 0,
                                                                'closedYN' => 0
                                                            ]);


                    //checking the fullyOrdered or partial in po
                    $detailSum = QuotationDetails::select(DB::raw('COALESCE(SUM(requestedQty),0) as totalQty'))
                        ->where('soQuotationDetailID', $quotationDetails->soQuotationDetailID)
                        ->first();

                    $updatedQuoQty = $detailSum['totalQty'];

                    if ($updatedQuoQty == 0) {
                        $fullyOrdered = 0;
                    } else {
                        $fullyOrdered = 1;
                    }

                    QuotationDetails::where('quotationDetailsID', $quotationDetails->soQuotationDetailID)
                        ->update([ 'fullyOrdered' => $fullyOrdered, 'soQuantity' => $updatedQuoQty]);

                    $this->updateSalesQuotationOrderStatus($quotationDetails->soQuotationMasterID);

                }
            }
        }

        return $this->sendResponse($id, 'Quotation Details deleted successfully');
    }

    public function getSalesQuotationDetails(Request $request)
    {
        $input = $request->all();
        $quotationMasterID = $input['quotationMasterID'];

        $items = QuotationDetails::leftjoin('units','UnitID','unitOfMeasureID')->where('quotationMasterID', $quotationMasterID)
              ->skip($input['skip'])->take($input['limit'])->get();

        $index = $input['skip'] + 1;
        foreach($items as $item) {
            $item['index'] = $index;
            $index++;
        }
        
        return $this->sendResponse($items->toArray(), 'Quotation Details retrieved successfully');
    }

    public function salesQuotationDetailsDeleteAll(Request $request)
    {
        $input = $request->all();

        $quotationMasterID = $input['quotationMasterID'];

        $detailExistAll = QuotationDetails::where('quotationMasterID', $quotationMasterID)
            ->get();

        if (empty($detailExistAll)) {
            return $this->sendError('There are no details to delete');
        }

        if (!empty($detailExistAll)) {

            foreach ($detailExistAll as $cvDeatil) {

                 $quotationMaster = QuotationMaster::find($cvDeatil['quotationMasterID']);
                if(!empty($quotationMaster)){
                    $deleteDetails = QuotationDetails::where('quotationDetailsID', $cvDeatil['quotationDetailsID'])->delete();

                    if($quotationMaster->quotationType == 2 && $quotationMaster->documentSystemID == 68){

                        if (!empty($cvDeatil['quotationDetailsID']) && !empty($cvDeatil['quotationMasterID'])) {
                            $updateQuotationMaster = QuotationMaster::find($cvDeatil['soQuotationMasterID'])
                                                                    ->update([
                                                                        'selectedForSalesOrder' => 0,
                                                                        'closedYN' => 0
                                                                    ]);


                            //checking the fullyOrdered or partial in po
                            $detailSum = QuotationDetails::select(DB::raw('COALESCE(SUM(requestedQty),0) as totalQty'))
                                ->where('soQuotationDetailID', $cvDeatil['soQuotationDetailID'])
                                ->first();

                            $updatedQuoQty = $detailSum['totalQty'];

                            if ($updatedQuoQty == 0) {
                                $fullyOrdered = 0;
                            } else {
                                $fullyOrdered = 1;
                            }

                            QuotationDetails::where('quotationDetailsID', $cvDeatil['soQuotationDetailID'])
                                ->update([ 'fullyOrdered' => $fullyOrdered, 'soQuantity' => $updatedQuoQty]);

                            $this->updateSalesQuotationOrderStatus($cvDeatil['soQuotationMasterID']);

                        }
                    }
                }
            }
        }

        return $this->sendResponse($quotationMasterID, 'Quotation details deleted successfully');
    }

    public function getSalesQuotationDetailForInvoice(Request $request){
        $input = $request->all();
        $id = $input['quotationMasterID'];

        $detail = DB::select('SELECT
	quotationdetails.*,
	erp_quotationmaster.serviceLineSystemID,
	"" AS isChecked,
	"" AS noQty,
	IFNULL(dodetails.invTakenQty,0) as invTakenQty 
FROM
	erp_quotationdetails quotationdetails
	INNER JOIN erp_quotationmaster ON quotationdetails.quotationMasterID = erp_quotationmaster.quotationMasterID
	LEFT JOIN ( SELECT erp_customerinvoiceitemdetails.customerItemDetailID,quotationDetailsID, SUM( qtyIssuedDefaultMeasure ) AS invTakenQty FROM erp_customerinvoiceitemdetails GROUP BY customerItemDetailID, itemCodeSystem ) AS dodetails ON quotationdetails.quotationDetailsID = dodetails.quotationDetailsID 
WHERE
	quotationdetails.quotationMasterID = ' . $id . ' 
	AND fullyOrdered != 2 AND erp_quotationmaster.isInDOorCI != 1 AND erp_quotationmaster.isInSO != 1');

        return $this->sendResponse($detail, 'Quotation Details retrieved successfully');
    }


    public function mapLineItemQo(Request $request)
    {
        $input = $request->all();

        $checkItem = QuotationDetails::where('quotationMasterID',$input['quotationMasterID'])
                                         ->where('itemSystemCode', $input['itemCodeNew'])
                                         ->where('quotationDetailsID', '!=', $input['quotationDetailsID'])
                                         ->first();

        if ($checkItem) {
            return $this->sendError('This item has already maped with another item of this purchase request');
        }

        $checkForPoItem = QuotationDetails::where('quotationDetailsID', $input['quotationDetailsID'])
                                         ->first();

        $companySystemID = $input['companySystemID'];
        $item = ItemAssigned::where('itemCodeSystem', $input['itemCodeNew'])
            ->where('companySystemID', $companySystemID)
            ->first();

        if (empty($item)) {
            return $this->sendError('Item not found');
        }

        $qoMaster = QuotationMaster::find($input['quotationMasterID']);

        if (empty($qoMaster)) {
            return $this->sendError('Quotation Details not found');
        }


        $input['itemCode'] = $input['itemCodeNew'];


        $input['itemSystemCode'] = $item->itemPrimaryCode;
        $input['itemReferenceNo'] = $item->itemPrimaryCode;
        $input['itemDescription'] = $item->itemDescription;
        $input['itemFinanceCategoryID'] =  $item->financeCategoryMaster;
        $input['itemFinanceCategorySubID'] = $item->financeCategorySub;

        $input['companySystemID'] = $item->companySystemID;
        $input['companyID'] = $item->companyID;
        $input['unitOfMeasureID'] = $item->itemUnitOfMeasure;
        $unit = Unit::find( $item->itemUnitOfMeasure);
        $input['unitOfMeasure'] = ($unit) ? $unit->UnitShortCode : null;
        $input['itemCategory'] =  $item->financeCategoryMaster;


        if ($item->financeCategoryMaster == 1) {

            $alreadyAdded = QuotationMaster::where('quotationMasterID', $input['soQuotationMasterID'])
                ->whereHas('detail', function ($query) use ($companySystemID, $qoMaster, $item) {
                    $query->where('itemSystemCode', $item->itemCodeSystem);
                })
                ->first();

            if ($alreadyAdded) {
                return $this->sendError("Selected item is already added. Please check again", 500);
            }
        }

        DB::beginTransaction();
        try {
            $quotationDetailss = $this->quotationDetailsRepository->update($input, $input['quotationDetailsID']);
            DB::commit();
            return $this->sendResponse($input, 'Quotation item maped successfully');
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError('Error Occurred'. $exception->getMessage() . 'Line :' . $exception->getLine());
        }


    }


    public function storeSalesOrderFromSalesQuotation(Request $request)
    {
        $input = $request->all();
        $DODetail_arr = array();
        $salesOrderID = $input['salesOrderID'];

        $isCheckArr = collect($input['detailTable'])->pluck('isChecked')->toArray();
        if (!in_array(true, $isCheckArr)) {
            return $this->sendError("No items selected to add.");
        }

        foreach ($input['detailTable'] as $newValidation) {
            if (($newValidation['isChecked'] && $newValidation['noQty'] == "") || ($newValidation['isChecked'] && $newValidation['noQty'] == 0) || ($newValidation['isChecked'] == '' && $newValidation['noQty'] > 0)) {

                $messages = [
                    'required' => 'SO quantity field is required.',
                ];

                $validator = \Validator::make($newValidation, [
                    'noQty' => 'required',
                    'isChecked' => 'required',
                ], $messages);

                if ($validator->fails()) {
                    return $this->sendError($validator->messages(), 422);
                }
            }


            $remaingQty = $newValidation['requestedQty'] - $newValidation['soTakenQty'];

            if ($remaingQty < $newValidation['noQty']) {
                return $this->sendError("SO Qty cannot be greater than SO balance Qty");
            }
        }

        $itemExistArray = array();
        //check added item exist
        foreach ($input['detailTable'] as $itemExist) {

                $item = ItemAssigned::with(['item_master'])
                ->where('itemCodeSystem', $itemExist['itemAutoID'])
                ->where('companySystemID', $itemExist['companySystemID'])
                ->first();


            if ($itemExist['isChecked'] && $itemExist['noQty'] > 0) {
                $QuoDetailExist = QuotationDetails::select(DB::raw('soQuotationDetailID,itemSystemCode'))
                    ->where('quotationMasterID', $salesOrderID)
                    ->where('itemAutoID', $itemExist['itemAutoID'])
                    ->get();

                    $item = ItemAssigned::with(['item_master'])
                    ->where('itemCodeSystem', $itemExist['itemAutoID'])
                    ->where('companySystemID', $itemExist['companySystemID'])
                    ->first();

                if (!empty($QuoDetailExist)) {
                    if($item->financeCategoryMaster != 2 && $item->financeCategoryMaster != 4 )
                    {
                        foreach ($QuoDetailExist as $row) {
                            $itemDrt = $row['itemSystemCode'] . " already exist";
                            $itemExistArray[] = [$itemDrt];
                        }
                    }
         
                }
            }
        }

        if (!empty($itemExistArray)) {
            return $this->sendError($itemExistArray, 422);
        }

        $salesOrder = QuotationMaster::where('quotationMasterID', $salesOrderID)->first();
        $employee = \Helper::getEmployeeInfo();

        DB::beginTransaction();
        try {

            foreach ($input['detailTable'] as $new) {

                $qoMaster = QuotationMaster::find($new['quotationMasterID']);

                $qoDetailExist = QuotationDetails::select(DB::raw('quotationDetailsID'))
                    ->where('quotationMasterID', $salesOrderID)
                    ->where('soQuotationDetailID', $new['quotationDetailsID'])
                    ->first();

                if (empty($qoDetailExist)) {
                    $soQuotationMasterID = $new['quotationMasterID'];
                    if ($new['isChecked'] && $new['noQty'] > 0) {

                        //checking the fullyOrdered or partial in po
                        $detailSum = QuotationDetails::select(DB::raw('COALESCE(SUM(requestedQty),0) as totalNoQty'))
                            ->where('soQuotationDetailID', $new['quotationDetailsID'])
                            ->first();

                        $totalAddedQty = $new['noQty'] + $detailSum['totalNoQty'];

                        if ($new['requestedQty'] == $totalAddedQty) {
                            $fullyOrdered = 2;
                        } else {
                            $fullyOrdered = 1;
                        }


                        // checking the qty request is matching with sum total
                        if ($new['requestedQty'] >= $new['noQty']) {

                            if($new['itemAutoID'] != 0) {
                                $item = ItemAssigned::where('itemCodeSystem', $new['itemAutoID'])
                                ->where('companySystemID', $salesOrder->companySystemID)
                                ->first();


                                if (empty($item)) {
                                    return $this->sendError('Added item not found in item master');
                                }
                            }

                            $new['qtyIssuedDefaultMeasure'] = $new['noQty'];
                            $new['requestedQty'] = $new['noQty'];
                            $new['soQuotationMasterID'] = $new['quotationMasterID'];
                            $new['quotationMasterID'] = $salesOrderID;

                            $totalNetcost = ($new['unittransactionAmount'] - $new['discountAmount']) * $new['noQty'];

                            $new['transactionAmount'] = \Helper::roundValue($totalNetcost);


                             // updating transaction amount for local and reporting
                            $currencyConversion = \Helper::currencyConversion($salesOrder->companySystemID, $salesOrder->transactionCurrencyID, $salesOrder->transactionCurrencyID, $new['transactionAmount']);

                            $new['companyLocalAmount'] = \Helper::roundValue($currencyConversion['localAmount']);
                            $new['companyReportingAmount'] = \Helper::roundValue($currencyConversion['reportingAmount']);

                            // adding customer default currencyID base currency conversion
                            $currencyConversionDefault = \Helper::currencyConversion($salesOrder->companySystemID, $salesOrder->customerCurrencyID, $salesOrder->customerCurrencyID, $new['transactionAmount']);

                            $new['customerAmount'] = \Helper::roundValue($currencyConversionDefault['documentAmount']);

                            unset($new['isChecked']);
                            unset($new['modifiedDateTime']);
                            unset($new['modifiedPCID']);
                            unset($new['modifiedUserID']);
                            unset($new['modifiedUserName']);
                            unset($new['noQty']);
                            unset($new['soTakenQty']);
                            $new['soQuotationDetailID'] = $new['quotationDetailsID'];
                            
                            $new['createdPCID'] = gethostname();
                            $new['createdUserID'] = $employee->empID;
                            $new['createdUserName'] = $employee->empName;

                             // Get VAT percentage for item
                            if ($salesOrder->isVatEligible) {
                                $vatDetails = TaxService::getVATDetailsByItem($salesOrder->companySystemID, $new['itemAutoID'], $salesOrder->customerSystemCode,0);
                                $new['VATPercentage'] = $vatDetails['percentage'];
                                $new['VATApplicableOn'] = $vatDetails['applicableOn'];
                                $new['vatMasterCategoryID'] = $vatDetails['vatMasterCategoryID'];
                                $new['vatSubCategoryID'] = $vatDetails['vatSubCategoryID'];
                                $new['VATAmount'] = 0;

                                if($new['VATApplicableOn'] == 1){
                                    if (isset($new['unittransactionAmount']) && $new['unittransactionAmount'] > 0) {
                                        $new['VATAmount'] = (($new['unittransactionAmount'] / 100) * $vatDetails['percentage']);
                                    }
                                } else {
                                    if ($totalNetcost > 0) {
                                        $new['VATAmount'] = (($totalNetcost / 100) * $vatDetails['percentage']);
                                    }
                                }

                                $currencyConversionVAT = \Helper::currencyConversion($salesOrder->companySystemID, $salesOrder->transactionCurrencyID, $salesOrder->transactionCurrencyID, $new['VATAmount']);

                                $new['VATAmountLocal'] = \Helper::roundValue($currencyConversionVAT['localAmount']);
                                $new['VATAmountRpt'] = \Helper::roundValue($currencyConversionVAT['reportingAmount']);
                            }
                           
                            $this->quotationDetailsRepository->create($new);

                            QuotationDetails::where('quotationDetailsID', $new['quotationDetailsID'])
                                ->update(['fullyOrdered' => $fullyOrdered, 'soQuantity' => $totalAddedQty]);

                        }

                    }
                }

                //check all details fullyOrdered in PR Master
                $quoMasterfullyOrdered = QuotationDetails::where('quotationMasterID', $soQuotationMasterID)
                    ->whereIn('fullyOrdered', [1, 0])
                    ->get()->toArray();

                if (empty($quoMasterfullyOrdered)) {
                    $updateQuotation = QuotationMaster::find($soQuotationMasterID)
                        ->update([
                            'selectedForSalesOrder' => -1,
                            'closedYN' => -1,
                        ]);
                } else {
                    $updateQuotation = QuotationMaster::find($soQuotationMasterID)
                        ->update([
                            'selectedForSalesOrder' => -1,
                            'closedYN' => 0,
                        ]);
                }

                $this->updateSalesQuotationOrderStatus($soQuotationMasterID);

            }

            DB::commit();
            return $this->sendResponse([], 'Sales Order Details saved successfully');
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError('Error Occurred'. $exception->getMessage() . 'Line :' . $exception->getLine());
        }

    }

    private function updateSalesQuotationOrderStatus($quotationMasterID){

        $status = 0;
        $isInDO = 0;
        $invQty = QuotationDetails::where('soQuotationMasterID',$quotationMasterID)->sum('requestedQty');

        if($invQty!=0) {
            $quotationQty = QuotationDetails::where('quotationMasterID',$quotationMasterID)->sum('requestedQty');
            if($invQty == $quotationQty){
                $status = 2;    // fully invoiced
            }else{
                $status = 1;    // partially invoiced
            }
            $isInDO = 1;
        }
        return QuotationMaster::where('quotationMasterID',$quotationMasterID)->update(['orderStatus' => $status,'isInSO'=>$isInDO]);

    }

    
}
