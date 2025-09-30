<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateSupplierInvoiceDirectItemAPIRequest;
use App\Http\Requests\API\UpdateSupplierInvoiceDirectItemAPIRequest;
use App\Jobs\SupplierInvoiceAddBulkItemJob;
use App\Models\SupplierInvoiceDirectItem;
use App\Models\ItemAssigned;
use App\Models\FinanceItemCategorySub;
use App\Models\BookInvSuppMaster;
use App\Models\TaxVatCategories;
use App\Models\CompanyPolicyMaster;
use App\Repositories\SupplierInvoiceDirectItemRepository;
use App\Repositories\BookInvSuppMasterRepository;
use App\Repositories\UserRepository;
use App\Services\SupplierInvoiceService;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\helper\Helper;
use App\helper\TaxService;

/**
 * Class SupplierInvoiceDirectItemController
 * @package App\Http\Controllers\API
 */

class SupplierInvoiceDirectItemAPIController extends AppBaseController
{
    /** @var  SupplierInvoiceDirectItemRepository */
    private $supplierInvoiceDirectItemRepository;
    private $bookInvSuppMasterRepository;
    private $userRepository;

    public function __construct(SupplierInvoiceDirectItemRepository $supplierInvoiceDirectItemRepo, BookInvSuppMasterRepository $bookInvSuppMasterRepo, UserRepository $userRepo)
    {
        $this->supplierInvoiceDirectItemRepository = $supplierInvoiceDirectItemRepo;
        $this->bookInvSuppMasterRepository = $bookInvSuppMasterRepo;
        $this->userRepository = $userRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/supplierInvoiceDirectItems",
     *      summary="Get a listing of the SupplierInvoiceDirectItems.",
     *      tags={"SupplierInvoiceDirectItem"},
     *      description="Get all SupplierInvoiceDirectItems",
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
     *                  @SWG\Items(ref="#/definitions/SupplierInvoiceDirectItem")
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
        $this->supplierInvoiceDirectItemRepository->pushCriteria(new RequestCriteria($request));
        $this->supplierInvoiceDirectItemRepository->pushCriteria(new LimitOffsetCriteria($request));
        $supplierInvoiceDirectItems = $this->supplierInvoiceDirectItemRepository->all();

        return $this->sendResponse($supplierInvoiceDirectItems->toArray(), trans('custom.supplier_invoice_direct_items_retrieved_successful'));
    }

    /**
     * @param CreateSupplierInvoiceDirectItemAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/supplierInvoiceDirectItems",
     *      summary="Store a newly created SupplierInvoiceDirectItem in storage",
     *      tags={"SupplierInvoiceDirectItem"},
     *      description="Store SupplierInvoiceDirectItem",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="SupplierInvoiceDirectItem that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/SupplierInvoiceDirectItem")
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
     *                  ref="#/definitions/SupplierInvoiceDirectItem"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateSupplierInvoiceDirectItemAPIRequest $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToValue($input);

        $bookingSuppMasInvAutoID = $input['bookingSuppMasInvAutoID'];

        $companySystemID = $input['companySystemID'];

        $invoice = $this->bookInvSuppMasterRepository->findWithoutFail($bookingSuppMasInvAutoID);
        if (empty($invoice)) {
            return $this->sendError(trans('custom.supplier_invoice_not_found'));
        }
        
        if(isset($input['type']) &&  $input['type'] != $invoice->documentType)
        {
             return $this->sendError(trans('custom.the_supplier_invoice_type_has_changed'));
        }

        if (empty($invoice->supplierTransactionCurrencyID)) {
            return $this->sendError('Please select a document currency');
        }

        DB::beginTransaction();
        try {
            $itemAssign = ItemAssigned::with(['item_master'])->find($input['itemCode']);

            if (empty($itemAssign)) {
                return $this->sendError('Item not assigned');
            }

            $user = \Helper::getEmployeeInfo();

            $item = ItemAssigned::where('itemCodeSystem', $itemAssign->itemCodeSystem)
                                ->where('companySystemID', $companySystemID)
                                ->first();

            //checking if item is inventory item cannot be added more than one
            $sameItem = SupplierInvoiceDirectItem::select(DB::raw('itemCode'))
                                                            ->where('bookingSuppMasInvAutoID', $bookingSuppMasInvAutoID)
                                                            ->where('itemCode', $itemAssign->itemCodeSystem)
                                                            ->first();


           if ($item->financeCategoryMaster == 1) {
               if ($sameItem) {
                   return $this->sendError(trans('custom.selected_item_is_already_added_from_the_same_suppl'), 422);
               }
           }
 
            $financeCategorySub = FinanceItemCategorySub::find($itemAssign->financeCategorySub);

            $input['noQty'] = isset($input['noQty']) ? $input['noQty'] : 0;

            $currency = \Helper::currencyConversion($invoice->companySystemID,$invoice->supplierTransactionCurrencyID, $invoice->supplierTransactionCurrencyID ,$input['unitCost']);
    
            // checking the qty request is matching with sum total
            $detailArray['bookingSuppMasInvAutoID'] = $bookingSuppMasInvAutoID;
            $detailArray['companySystemID'] = $invoice->companySystemID;
            $detailArray['itemCode'] = $itemAssign->itemCodeSystem;
            $detailArray['trackingType'] = (isset($itemAssign->item_master->trackingType)) ? $itemAssign->item_master->trackingType : null;
            $detailArray['itemPrimaryCode'] = $itemAssign->itemPrimaryCode;
            $detailArray['itemDescription'] = $itemAssign->itemDescription;
            $detailArray['itemFinanceCategoryID'] = $itemAssign->financeCategoryMaster;
            $detailArray['itemFinanceCategorySubID'] = $itemAssign->financeCategorySub;
            $detailArray['financeGLcodebBSSystemID'] = $financeCategorySub->financeGLcodebBSSystemID;
            $detailArray['financeGLcodePLSystemID'] = $financeCategorySub->financeGLcodePLSystemID;
            $detailArray['includePLForGRVYN'] = $financeCategorySub->includePLForGRVYN;
            $detailArray['supplierPartNumber'] = $itemAssign->secondaryItemCode;
            $detailArray['unitOfMeasure'] = $itemAssign->itemUnitOfMeasure;
            $detailArray['noQty'] = $input['noQty'];
            $totalNetcost = $input['unitCost'] * $input['noQty'];
            $detailArray['unitCost'] = $input['unitCost'];
            $detailArray['netAmount'] = $totalNetcost;
            $detailArray['comment'] = $input['comment'];
            $detailArray['supplierDefaultCurrencyID'] = $invoice->supplierTransactionCurrencyID;
            $detailArray['supplierDefaultER'] = $invoice->supplierTransactionCurrencyER;
            $detailArray['supplierItemCurrencyID'] = $invoice->supplierTransactionCurrencyID;
            $detailArray['foreignToLocalER'] = $invoice->supplierTransactionCurrencyER;
            $detailArray['companyReportingCurrencyID'] = $invoice->companyReportingCurrencyID;
            $detailArray['companyReportingER'] = $invoice->companyReportingER;
            $detailArray['localCurrencyID'] = $invoice->localCurrencyID;
            $detailArray['localCurrencyER'] = $invoice->localCurrencyER;

            $detailArray['costPerUnitLocalCur'] = \Helper::roundValue($currency['localAmount']);
            $detailArray['costPerUnitSupDefaultCur'] = \Helper::roundValue($input['unitCost']);
            $detailArray['costPerUnitSupTransCur'] = \Helper::roundValue($input['unitCost']);
            $detailArray['costPerUnitComRptCur'] = \Helper::roundValue($currency['reportingAmount']);

            $detailArray['VATAmount'] = 0;
            if ($invoice->isVatEligible) {
                $vatDetails = TaxService::getVATDetailsByItem($invoice->companySystemID, $detailArray['itemCode'], $invoice->supplierID);
                $detailArray['VATPercentage'] = $vatDetails['percentage'];
                $detailArray['VATApplicableOn'] = $vatDetails['applicableOn'];
                $detailArray['vatMasterCategoryID'] = $vatDetails['vatMasterCategoryID'];
                $detailArray['vatSubCategoryID'] = $vatDetails['vatSubCategoryID'];
                $detailArray['VATAmount'] = 0;
                $detailArray['VATAmountLocal'] = 0;
                $detailArray['VATAmountRpt'] = 0;
            }

            $detailArray['createdPcID'] = gethostname();
            $detailArray['createdUserID'] = $user->employeeSystemID;


            $item = $this->supplierInvoiceDirectItemRepository->create($detailArray);

            DB::commit();
            return $this->sendResponse('', trans('custom.supplier_invoice_item_details_saved_successfully'));
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError(trans('custom.error_occurred'));
        }
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/supplierInvoiceDirectItems/{id}",
     *      summary="Display the specified SupplierInvoiceDirectItem",
     *      tags={"SupplierInvoiceDirectItem"},
     *      description="Get SupplierInvoiceDirectItem",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SupplierInvoiceDirectItem",
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
     *                  ref="#/definitions/SupplierInvoiceDirectItem"
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
        /** @var SupplierInvoiceDirectItem $supplierInvoiceDirectItem */
        $supplierInvoiceDirectItem = $this->supplierInvoiceDirectItemRepository->findWithoutFail($id);

        if (empty($supplierInvoiceDirectItem)) {
            return $this->sendError(trans('custom.supplier_invoice_direct_item_not_found'));
        }

        return $this->sendResponse($supplierInvoiceDirectItem->toArray(), trans('custom.supplier_invoice_direct_item_retrieved_successfull'));
    }

    /**
     * @param int $id
     * @param UpdateSupplierInvoiceDirectItemAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/supplierInvoiceDirectItems/{id}",
     *      summary="Update the specified SupplierInvoiceDirectItem in storage",
     *      tags={"SupplierInvoiceDirectItem"},
     *      description="Update SupplierInvoiceDirectItem",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SupplierInvoiceDirectItem",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="SupplierInvoiceDirectItem that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/SupplierInvoiceDirectItem")
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
     *                  ref="#/definitions/SupplierInvoiceDirectItem"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateSupplierInvoiceDirectItemAPIRequest $request)
    {
        $input = array_except($request->all(), 'unit', 'vat_sub_category');

        if (isset($input['vat_sub_category'])) {
            unset($input['vat_sub_category']);
        }

        $input = $this->convertArrayToValue($input);

        $userId = Auth::id();
        $user = $this->userRepository->with(['employee'])->findWithoutFail($userId);

        $itemDetail = $this->supplierInvoiceDirectItemRepository->findWithoutFail($id);

        if (empty($itemDetail)) {
            return $this->sendError(trans('custom.supplier_invoice_details_not_found'));
        }

        $supplierInvoice = BookInvSuppMaster::where('bookingSuppMasInvAutoID', $input['bookingSuppMasInvAutoID'])
                                          ->first();

        if (empty($supplierInvoice)) {
            return $this->sendError(trans('custom.supplier_invoice_not_found'));
        }

        DB::beginTransaction();
        try {
            $validateVATCategories = TaxService::validateVatCategoriesInDocumentDetails($supplierInvoice->documentSystemID, $supplierInvoice->companySystemID, $id, $input, 0, $supplierInvoice->documentType);

            if (!$validateVATCategories['status']) {
                return $this->sendError($validateVATCategories['message'], 500, array('type' => 'no_qty_issues'));
            } else {
                $input['vatMasterCategoryID'] = $validateVATCategories['vatMasterCategoryID'];        
                $input['vatSubCategoryID'] = $validateVATCategories['vatSubCategoryID'];        
            }


            if (isset($input['vatSubCategoryID']) && $input['vatSubCategoryID'] > 0) {
                $subcategoryVAT = TaxVatCategories::find($input['vatSubCategoryID']);
                $input['exempt_vat_portion'] = (isset($input['exempt_vat_portion']) && $subcategoryVAT && $subcategoryVAT->subCatgeoryType == 1) ? $input['exempt_vat_portion'] : 0;
            }

            $input['VATAmount'] = isset($input['VATAmount']) ? $input['VATAmount'] : 0;
            $input['discountAmount'] = isset($input['discountAmount']) ? \Helper::roundValue($input['discountAmount']) : 0;
            $discountedUnitPrice = $input['unitCost']  - $input['discountAmount'];
            if(TaxService::checkPOVATEligible($supplierInvoice->supplierVATEligible, $supplierInvoice->vatRegisteredYN)){
                $discountedUnitPrice =  $discountedUnitPrice + $input['VATAmount'];
            }

            if ($discountedUnitPrice > 0) {
                $currencyConversion = \Helper::currencyConversion($input['companySystemID'], $supplierInvoice->supplierTransactionCurrencyID, $supplierInvoice->supplierTransactionCurrencyID, $discountedUnitPrice);

                $input['costPerUnitLocalCur'] = \Helper::roundValue($currencyConversion['localAmount']);
                $input['costPerUnitSupTransCur'] = $discountedUnitPrice;
                $input['costPerUnitComRptCur'] = \Helper::roundValue($currencyConversion['reportingAmount']);
            }

            if (isset($input['VATAmount']) && $input['VATAmount'] > 0) {
                $currencyConversionVAT = \Helper::currencyConversion($input['companySystemID'], $supplierInvoice->supplierTransactionCurrencyID, $supplierInvoice->supplierTransactionCurrencyID, $input['VATAmount']);
                $input['VATAmountLocal'] = \Helper::roundValue($currencyConversionVAT['localAmount']);
                $input['VATAmountRpt'] = \Helper::roundValue($currencyConversionVAT['reportingAmount']);
                $input['VATAmount'] = \Helper::roundValue($input['VATAmount']);
            } else {
                $input['VATAmount'] = 0;
                $input['VATAmountLocal'] = 0;
                $input['VATAmountRpt'] = 0;
            }

            // adding supplier Default CurrencyID base currency conversion
            if ($discountedUnitPrice > 0) {
                $currencyConversionDefault = \Helper::currencyConversion($input['companySystemID'], $supplierInvoice->supplierTransactionCurrencyID, $supplierInvoice->supplierDefaultCurrencyID, $discountedUnitPrice);

                $input['costPerUnitSupDefaultCur'] = \Helper::roundValue($currencyConversionDefault['documentAmount']);
            }

            $input['modifiedPc'] = gethostname();
            $input['modifiedUser'] = $user->employee['employeeSystemID'];
            $updateMarkupBy = isset($input['updateMarkupBy']) ? $input['updateMarkupBy'] : '';

            $suppItemDetails = $this->supplierInvoiceDirectItemRepository->update($input, $id);
            $validateVATCategories = TaxService::validateVatCategoriesInDocumentDetails($supplierInvoice->documentSystemID, $supplierInvoice->companySystemID, $id, $input, 0, $supplierInvoice->documentType);

            \Helper::updateSupplierRetentionAmount($input['bookingSuppMasInvAutoID'],$supplierInvoice);
            \Helper::updateSupplierItemWhtAmount($input['bookingSuppMasInvAutoID'],$supplierInvoice);
            DB::commit();
            return $this->sendResponse($suppItemDetails->toArray(), trans('custom.supplier_invoice_details_updated_successfully'));
        } catch (\Exception $ex) {
            DB::rollback();
            return $this->sendError($ex->getMessage(), 500);
        }
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/supplierInvoiceDirectItems/{id}",
     *      summary="Remove the specified SupplierInvoiceDirectItem from storage",
     *      tags={"SupplierInvoiceDirectItem"},
     *      description="Delete SupplierInvoiceDirectItem",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SupplierInvoiceDirectItem",
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
        /** @var SupplierInvoiceDirectItem $supplierInvoiceDirectItem */
        $supplierInvoiceDirectItem = $this->supplierInvoiceDirectItemRepository->findWithoutFail($id);

        $supplierInvoice = BookInvSuppMaster::where('bookingSuppMasInvAutoID', $supplierInvoiceDirectItem->bookingSuppMasInvAutoID)
        ->first();
        
        if (empty($supplierInvoiceDirectItem)) {
            return $this->sendError(trans('custom.supplier_invoice_direct_item_not_found'));
        }

        $supplierInvoiceDirectItem->delete();

    
        \Helper::updateSupplierRetentionAmount($supplierInvoiceDirectItem->bookingSuppMasInvAutoID,$supplierInvoice);
        \Helper::updateSupplierItemWhtAmount($supplierInvoiceDirectItem->bookingSuppMasInvAutoID,$supplierInvoice);

        return $this->sendResponse([], trans('custom.supplier_invoice_direct_item_deleted_successfully'));
    }

    public function getSupplierInvDirectItems(Request $request)
    {
        $input = $request->all();


        $bookingSuppMasInvAutoID = $input['bookingSuppMasInvAutoID'];

        $items = SupplierInvoiceDirectItem::where('bookingSuppMasInvAutoID', $bookingSuppMasInvAutoID)
                 // ->join('erp_accountspayableledger','documentSystemCode','=','bookingSuppMasInvAutoID')
            ->with(['unit' => function ($query) {
            }, 'vat_sub_category'])->get();

        if(count($items) == 0) {
            $items = SupplierInvoiceDirectItem::where('bookingSuppMasInvAutoID', $bookingSuppMasInvAutoID)
            ->with(['unit' => function ($query) {
            }, 'vat_sub_category'])->get();
        }
        
        return $this->sendResponse($items->toArray(), trans('custom.item_details_retrieved_successfully'));
    }

    public function deleteAllSIDirectItemDetail(Request $request)
    {
        $input = $request->all();

        $bookingSuppMasInvAutoID = isset($input['bookingSuppMasInvAutoID']) ? $input['bookingSuppMasInvAutoID'] : 0;

        $supInvoice = BookInvSuppMaster::find($bookingSuppMasInvAutoID);

        if (empty($supInvoice)) {
            return $this->sendError(trans('custom.supplier_invoice_not_found'));
        }

        if($supInvoice->confirmedYN){
            return $this->sendError(trans('custom.you_cannot_delete_supplier_invoice_details_this_do'),500);
        }


        $detailExistAll = SupplierInvoiceDirectItem::where('bookingSuppMasInvAutoID', $bookingSuppMasInvAutoID)
            ->get();

        if (empty($detailExistAll)) {
            return $this->sendError('There are no details to delete',500);
        }

        if (!empty($detailExistAll)) {
            $deleteDetails = SupplierInvoiceDirectItem::where('bookingSuppMasInvAutoID', $bookingSuppMasInvAutoID)->delete();
        }

        \Helper::updateSupplierRetentionAmount($bookingSuppMasInvAutoID,$supInvoice);
        \Helper::updateSupplierItemWhtAmount($bookingSuppMasInvAutoID,$supInvoice);
        return $this->sendResponse($bookingSuppMasInvAutoID, trans('custom.details_deleted_successfully'));
    }

    public function supplierInvoiceValidateItem(Request $request) {
        $input = $request->all();

        return SupplierInvoiceService::validateSupplierInvoiceItem($input['itemCodeSystem'], $input['companySystemID'], $input['supplierInvoiceId']);
    }

    public function supplierInvoiceDetailsAddAllItems(Request $request) {
        $input = $request->all();
        $id = Auth::id();
        $user = $this->userRepository->with(['employee'])->findWithoutFail($id);

        $input['employeeSystemID'] = $user ? $user->employee['employeeSystemID'] : null;

        if (isset($input['addAllItems']) && $input['addAllItems']) {
            $db = $input['db'] ?? "";

            $supplierInvoice = BookInvSuppMaster::where('bookingSuppMasInvAutoID', $input['supplierInvoiceId'])->first();
            if (empty($supplierInvoice)) {
                return $this->sendError(trans('custom.supplier_invoice_not_found'), 500);
            }

            $data['isBulkItemJobRun'] = 1;
            BookInvSuppMaster::where('bookingSuppMasInvAutoID', $input['supplierInvoiceId'])->update($data);

            SupplierInvoiceAddBulkItemJob::dispatch($db, $input);

            return $this->sendResponse('', 'Items Added to Queue Please wait some minutes to process');
        }
        else {
            DB::beginTransaction();
            try {
                $invalidItems = [];
                foreach ($input['itemArray'] as $key => $value) {
                    $response = SupplierInvoiceService::validateSupplierInvoiceItem($value['itemCodeSystem'], $input['companySystemID'], $input['supplierInvoiceId']);

                    if ($response['status']) {
                        SupplierInvoiceService::saveSupplierInvoiceItem($value['itemCodeSystem'], $input['supplierInvoiceId'], $input['employeeSystemID']);
                    } else {
                        $invalidItems[] = ['itemCodeSystem' => $value['itemCodeSystem'], 'message' => $response['message']];
                    }
                }
                DB::commit();
                return $this->sendResponse('', trans('custom.supplier_invoice_items_saved_successfully'));
            } catch (\Exception $exception) {
                DB::rollBack();
                return $this->sendError($exception->getMessage(), 500);
            }
        }
    }
}
