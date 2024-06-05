<?php

namespace App\Http\Controllers\API;

use App\helper\Helper;
use App\Http\Requests\API\CreateTaxVatCategoriesAPIRequest;
use App\Http\Requests\API\UpdateTaxVatCategoriesAPIRequest;
use App\Models\ChartOfAccount;
use App\Models\FinanceItemCategoryMaster;
use App\Models\VatSubCategoryType;
use App\Models\ItemAssigned;
use App\Models\ItemMaster;
use App\Models\PurchaseOrderDetails;
use App\Models\DirectInvoiceDetails;
use App\Models\DebitNoteDetails;
use App\Models\CreditNoteDetails;
use App\Models\CustomerInvoiceDirect;
use App\Models\CustomerInvoiceDirectDetail;
use App\Models\QuotationDetails;
use App\Models\CustomerInvoiceItemDetails;
use App\Models\DeliveryOrderDetail;
use App\Models\TaxVatCategories;
use App\Models\Tax;
use App\Models\TaxVatMainCategories;
use App\Models\YesNoSelection;
use App\Repositories\TaxVatCategoriesRepository;
use function foo\func;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\DB;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class TaxVatCategoriesController
 * @package App\Http\Controllers\API
 */

class TaxVatCategoriesAPIController extends AppBaseController
{
    /** @var  TaxVatCategoriesRepository */
    private $taxVatCategoriesRepository;

    public function __construct(TaxVatCategoriesRepository $taxVatCategoriesRepo)
    {
        $this->taxVatCategoriesRepository = $taxVatCategoriesRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/taxVatCategories",
     *      summary="Get a listing of the TaxVatCategories.",
     *      tags={"TaxVatCategories"},
     *      description="Get all TaxVatCategories",
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
     *                  @SWG\Items(ref="#/definitions/TaxVatCategories")
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
        $this->taxVatCategoriesRepository->pushCriteria(new RequestCriteria($request));
        $this->taxVatCategoriesRepository->pushCriteria(new LimitOffsetCriteria($request));
        $taxVatCategories = $this->taxVatCategoriesRepository->all();

        return $this->sendResponse($taxVatCategories->toArray(), 'Tax Vat Categories retrieved successfully');
    }

    /**
     * @param CreateTaxVatCategoriesAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/taxVatCategories",
     *      summary="Store a newly created TaxVatCategories in storage",
     *      tags={"TaxVatCategories"},
     *      description="Store TaxVatCategories",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="TaxVatCategories that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/TaxVatCategories")
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
     *                  ref="#/definitions/TaxVatCategories"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateTaxVatCategoriesAPIRequest $request)
    {
        $input = $request->all();
//
        $input = $this->convertArrayToSelectedValue($input, array('recordType'));
        if(!(isset($input['taxMasterAutoID']) && $input['taxMasterAutoID'])){
            return $this->sendError('Tax Master Auto ID is not found',500);
        }
        $messages = [
            'mainCategory.required' => 'Main Category is required.',
            'subCategoryDescription.required' => 'Sub Category is required.',
            'subCatgeoryType.required' => 'Sub Category type is required.',
            'percentage.required' => 'Percentage is required.',
            'percentage.min' => 'You cannot enter negative values for percentage',
            'percentage.numeric' => 'You can only enter numbers',
            'applicableOn.required' => 'Applicable On is required.',

        ];
        $validator = \Validator::make($input, [
            'mainCategory' => 'required',
            'subCategoryDescription' => 'required',
            'subCatgeoryType' => 'required',
            'percentage' => 'required|numeric|min:0',
            'applicableOn' => 'required',

        ], $messages);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        // check duplicated subcategory
        $isDuplicated = TaxVatCategories::where('subCategoryDescription',$input['subCategoryDescription'])->where('taxMasterAutoID',$input['taxMasterAutoID'])->exists();
        if($isDuplicated){
           return $this->sendError('Subcategory is already taken',500);
        }

        $employee = Helper::getEmployeeInfo();
        $input['createdPCID'] = gethostname();
        $input['createdUserID'] = $employee->empID;
        $input['createdUserSystemID'] = $employee->employeeSystemID;

        $taxVatCategories = $this->taxVatCategoriesRepository->create($input);

        return $this->sendResponse($taxVatCategories->toArray(), 'Tax Vat Categories saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/taxVatCategories/{id}",
     *      summary="Display the specified TaxVatCategories",
     *      tags={"TaxVatCategories"},
     *      description="Get TaxVatCategories",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of TaxVatCategories",
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
     *                  ref="#/definitions/TaxVatCategories"
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
        /** @var TaxVatCategories $taxVatCategories */
        $taxVatCategories = $this->taxVatCategoriesRepository->findWithoutFail($id);

        if (empty($taxVatCategories)) {
            return $this->sendError('Tax Vat Categories not found');
        }

        return $this->sendResponse($taxVatCategories->toArray(), 'Tax Vat Categories retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateTaxVatCategoriesAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/taxVatCategories/{id}",
     *      summary="Update the specified TaxVatCategories in storage",
     *      tags={"TaxVatCategories"},
     *      description="Update TaxVatCategories",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of TaxVatCategories",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="TaxVatCategories that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/TaxVatCategories")
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
     *                  ref="#/definitions/TaxVatCategories"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateTaxVatCategoriesAPIRequest $request)
    {
        $input = $request->all();
        $input = array_except($input,['main','tax','created_by', 'Actions', 'type', 'DT_Row_Index']);
        $input = $this->convertArrayToSelectedValue($input, array('applicableOn', 'mainCategory', 'subCatgeoryType', 'recordType', 'expenseGL'));

        $input['expenseGL'] = ($input['recordType'] == 1) ? $input['expenseGL'] : null;

        /** @var TaxVatCategories $taxVatCategories */
        $taxVatCategories = $this->taxVatCategoriesRepository->findWithoutFail($id);

        if (empty($taxVatCategories)) {
            return $this->sendError('Tax Vat Categories not found');
        }

        if(!(isset($input['taxMasterAutoID']) && $input['taxMasterAutoID'])){
            return $this->sendError('Tax Master Auto ID is not found',500);
        }
        $messages = [
            'mainCategory.required' => 'Main Category is required.',
            'subCategoryDescription.required' => 'Sub Category is required.',
            'subCatgeoryType.required' => 'Sub Category type is required.',
            'percentage.required' => 'Percentage is required.',
            'percentage.min' => 'You cannot enter negative values for percentage',
            'percentage.numeric' => 'You can only enter numbers',
            'applicableOn.required' => 'Applicable On is required.',

        ];
        $validator = \Validator::make($input, [
            'mainCategory' => 'required',
            'subCatgeoryType' => 'required',
            'subCategoryDescription' => 'required',
            'percentage' => 'required|numeric|min:0',
            'applicableOn' => 'required',

        ], $messages);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        $taxData = Tax::find($input['taxMasterAutoID']);

        if (!$taxData) {
            return $this->sendError('Tax Master not found',500);
        }

        if (isset($input['isDefault']) && $input['isDefault']) {
            $checkAnyOtherActive = TaxVatCategories::where('isDefault', 1)
                                                   ->where('taxVatSubCategoriesAutoID', '!=', $id)
                                                   ->whereHas('tax', function($query) use ($taxData) {
                                                        $query->where('companySystemID', $taxData->companySystemID);
                                                   })
                                                   ->first();

            if ($checkAnyOtherActive) {
                return $this->sendError('Only one catgeory can be default',500);
            }
        }

        $isDuplicated = TaxVatCategories::where('subCategoryDescription',$input['subCategoryDescription'])->where('taxMasterAutoID',$input['taxMasterAutoID'])->where('taxVatSubCategoriesAutoID','!=',$id)->exists();
        if($isDuplicated){
            return $this->sendError('Subcategory is already taken',500);
        }

        $employee = Helper::getEmployeeInfo();
        $input['modifiedPCID'] = gethostname();
        $input['modifiedUserID'] = $employee->empID;
        $input['modifiedUserSystemID'] = $employee->employeeSystemID;
        $taxVatCategories = TaxVatCategories::where('taxVatSubCategoriesAutoID', $id)->update($input);

        return $this->sendResponse([], 'TaxVatCategories updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/taxVatCategories/{id}",
     *      summary="Remove the specified TaxVatCategories from storage",
     *      tags={"TaxVatCategories"},
     *      description="Delete TaxVatCategories",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of TaxVatCategories",
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
        /** @var TaxVatCategories $taxVatCategories */
        $taxVatCategories = $this->taxVatCategoriesRepository->findWithoutFail($id);

        if (empty($taxVatCategories)) {
            return $this->sendError('Tax Vat Categories not found');
        }

        $isExists = ItemMaster::where('vatSubCategory',$id)->exists();
        if ($isExists) {
            return $this->sendError('You cannot delete. this sub category has assigned to item master');
        }

        $taxVatCategories->delete();

        return $this->sendResponse([],'Tax Vat Categories deleted successfully');
    }

    public function getAllVatCategories(Request $request)
    {

        $input = $request->all();
        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $taxMasterAutoID = $request['taxMasterAutoID'];

        $vatCategories = TaxVatCategories::where('taxMasterAutoID', $taxMasterAutoID)
            ->with(['tax', 'created_by','main', 'type']);

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $vatCategories = $vatCategories->where(function ($query) use ($search) {
                $query->whereHas('tax', function($q)use ($search){
                    $q->where('taxShortCode','LIKE', "%{$search}%")
                    ->orWhere('taxDescription','LIKE', "%{$search}%");
                })
                ->orWhereHas('main',function($q)use ($search){
                    $q->where('mainCategoryDescription','LIKE', "%{$search}%");
                })->orWhere('subCategoryDescription','LIKE', "%{$search}%");
            });
        }

        return \DataTables::eloquent($vatCategories)
            ->addColumn('Actions', 'Actions', "Actions")
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('taxVatSubCategoriesAutoID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    public function getVatCategoriesFormData(Request $request){

        $input = $request->all();
        $companyID = isset($input['companyId']) ? $input['companyId'] : null;
        $main = TaxVatMainCategories::where('taxMasterAutoID',$input['taxMasterAutoID'])->where('isActive',1)->get();
        $applicable = array(array('value' => 1, 'label' => 'Gross Amount'), array('value' => 2, 'label' => 'Net Amount'));

        $chartOfAccount = ChartOfAccount::where('isApproved', 1)->where('controlAccountsSystemID', 2)
            ->whereHas('chartofaccount_assigned', function($query) use ($companyID){
                $query->where('companySystemID', $companyID)
                    ->where('isAssigned', -1);
            })->get();

        $output = array(
            'mainCategories' => $main,
            'applicableOns' => $applicable,
            'chartOfAccount' => $chartOfAccount,
            'subCategoryTypes' => VatSubCategoryType::all(),
        );

        return $this->sendResponse($output, 'Record retrieved successfully');
    }

    public function getVatCategoryFormData(Request $request){

        $input = $request->all();
        $main = TaxVatMainCategories::whereHas('tax',function($query) use ($input) {
                                        $query->where('companySystemID', $input['companyId']);
                                    })
                                    ->where('isActive',1)
                                    ->get();
        $subCategories = TaxVatCategories::whereHas('tax',function($query) use ($input) {
                                        $query->where('companySystemID', $input['companyId']);
                                    })
                                    ->where('isActive',1)
                                    ->get();
        $output = array(
            'mainCategories' => $main,
            'subCategories' => $subCategories
        );

        return $this->sendResponse($output, 'Record retrieved successfully');
    }

    public function getVatSubCategoryItemAssignFromData(Request $request){
        $input = $request->all();
        $seachText = isset($input['seachText'])?$input['seachText']:'';
        $output['items'] = ItemMaster::select(DB::Raw("itemCodeSystem,primaryCode,itemDescription,CONCAT(primaryCode, ' | ' ,itemDescription) as label"));
        if($seachText != ''){
            $output['items'] = $output['items']->where(function ($query) use ($seachText) {
                $query->where('primaryCode','LIKE', "%{$seachText}%")
                ->orWhere('itemDescription','LIKE', "%{$seachText}%");
            });
        }
        $output['items'] = $output['items']->take(500)->get();

        return $this->sendResponse($output, 'Record retrieved successfully');
    }

    public function getAllVatSubCategoryItemAssign(Request $request){
        $input = $request->all();
        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $vatSubID = $request['id'];
        $companyId = $request['companyId'];

        $output = ItemMaster::where('vatSubCategory',$vatSubID)->with(['unit_by']);

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $output = $output->where(function ($query) use ($search) {
                $query->where('primaryCode','LIKE', "%{$search}%")
                    ->orWhere('itemDescription','LIKE', "%{$search}%");
            });
        }
        return \DataTables::eloquent($output)
            ->addColumn('Actions', 'Actions', "Actions")
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('itemCodeSystem', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    public function assignVatSubCategoryToItem(Request $request){
        $input = $request->all();
        $selected = isset($input['selectedItems'])?$input['selectedItems']:[];

        $id = isset($input['id'])?$input['id']:[];
        $error = [];
        if(count($selected)>0 && $id){

            DB::beginTransaction();
            try{
                foreach ($selected as $row){

                    $item = ItemMaster::where('itemCodeSystem',$row['itemCodeSystem'])
                        ->where('vatSubCategory','>',0)
                        ->where('vatSubCategory','!=',$id)
                        ->with(['vat_sub_category'])
                        ->first();
                    if($item && isset($item->vat_sub_category->taxMasterAutoID)){
                        $error[] = $item->primaryCode.' has already assigned to '.$item->vat_sub_category->subCategoryDescription;
                    }else{
                        ItemMaster::where('itemCodeSystem',$row['itemCodeSystem'])->update(['vatSubCategory'=>$id]);
                    }
                }

                if(!empty($error) && count($error)>0){
                    return $this->sendError($error,422);
                }
                DB::commit();
                return $this->sendResponse([], 'Successfully assigned');
            } catch (\Exception $exception) {
                DB::rollBack();
                return $this->sendError($exception->getMessage());
            }

        }
        return $this->sendError('Error Occurred',500);
    }

    public function removeAssignedItemFromVATSubCategory(Request $request){
        $input = $request->all();
        $id = isset($input['itemCodeSystem'])?$input['itemCodeSystem']:0;

        if($id>0){


            $itemMaster = ItemMaster::find($id);
            if(empty($itemMaster)){
                return $this->sendError('Item Master Not found');
            }

            //If the item is in Fully Approved status do not allow to remove.
            if($itemMaster->itemApprovedYN == 1){
                return $this->sendError('Item is fully approved. You cannot remove');
            }

            $isUpdate = ItemMaster::where('itemCodeSystem',$id)->update(['vatSubCategory'=>0]);
            if($isUpdate){
                return $this->sendResponse($isUpdate, 'Successfully removed');
            }
        }
        return $this->sendError('Error Occured',500);
    }

    public function updateItemVatCategories(Request $request)
    {
        $input = $request->all();

        if (!isset($input['documentSystemID'])) {
            return $this->sendError('Document System ID not found');
        }

        DB::beginTransaction();
        try{
            switch ($input['documentSystemID']) {
                case 2:
                    $res = $this->updatePurchaseOrderDetailVATCategories($input['items']);
                    break;
                case 68:
                    $res = $this->updateSalesOrderDetailVATCategories($input['items']);
                    break;
                case 71:
                    $res = $this->updateDeliveryOrderDetailVATCategories($input['items']);
                    break;
                case 20:
                    $res = $this->updateCustomerInvoiceDetailVATCategories($input['items']);
                    break;
                case 19:
                    $res = $this->updateCreditNoteDetailVATCategories($input['items']);
                    break;
                case 15:
                    $res = $this->updateDebitNoteDetailVATCategories($input['items']);
                    break;
                case 11:
                    $res = $this->updateDirectSupplierInvoiceDetailVATCategories($input['items']);
                    break;
                
                default:
                    # code...
                    break;
            }

            DB::commit();
            return $this->sendResponse([], 'VAT Categories updated successfully');
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage());
        }
    }

    public function updateDirectSupplierInvoiceDetailVATCategories($items)
    {
        foreach ($items as $key => $value) {
            $value = $this->convertArrayToSelectedValue($value, ['vatMasterCategoryID', 'vatSubCategoryID']);

            $updateData = [
                'vatMasterCategoryID' => $value['vatMasterCategoryID'],
                'vatSubCategoryID' => $value['vatSubCategoryID']
            ];

            $res = DirectInvoiceDetails::where('directInvoiceDetailsID', $value['directInvoiceDetailsID'])
                                    ->update($updateData);
        }

        return ['status' => true];
    }

   public function updateDebitNoteDetailVATCategories($items)
    {
        foreach ($items as $key => $value) {
            $value = $this->convertArrayToSelectedValue($value, ['vatMasterCategoryID', 'vatSubCategoryID']);

            $updateData = [
                'vatMasterCategoryID' => $value['vatMasterCategoryID'],
                'vatSubCategoryID' => $value['vatSubCategoryID']
            ];

            $res = DebitNoteDetails::where('debitNoteDetailsID', $value['debitNoteDetailsID'])
                                    ->update($updateData);
        }

        return ['status' => true];
    }

   public function updatePurchaseOrderDetailVATCategories($items)
    {
        foreach ($items as $key => $value) {
            $value = $this->convertArrayToSelectedValue($value, ['vatMasterCategoryID', 'vatSubCategoryID']);

            $subcategory = TaxVatCategories::find($value['vatSubCategoryID']);

            $updateData = [
                'vatMasterCategoryID' => $value['vatMasterCategoryID'],
                'vatSubCategoryID' => $value['vatSubCategoryID'],
                'exempt_vat_portion' => (isset($value['exempt_vat_portion']) && $subcategory && $subcategory->subCatgeoryType == 1) ? $value['exempt_vat_portion'] : 0,
            ];

            $res = PurchaseOrderDetails::where('purchaseOrderDetailsID', $value['purchaseOrderDetailsID'])
                                    ->update($updateData);
        }

        return ['status' => true];
    }

    public function updateCreditNoteDetailVATCategories($items)
    {
        foreach ($items as $key => $value) {
            $value = $this->convertArrayToSelectedValue($value, ['vatMasterCategoryID', 'vatSubCategoryID']);

            $updateData = [
                'vatMasterCategoryID' => $value['vatMasterCategoryID'],
                'vatSubCategoryID' => $value['vatSubCategoryID']
            ];

            $res = CreditNoteDetails::where('creditNoteDetailsID', $value['creditNoteDetailsID'])
                                    ->update($updateData);
        }

        return ['status' => true];
    }

    public function updateSalesOrderDetailVATCategories($items)
    {
        foreach ($items as $key => $value) {
            $value = $this->convertArrayToSelectedValue($value, ['vatMasterCategoryID', 'vatSubCategoryID']);

            $updateData = [
                'vatMasterCategoryID' => $value['vatMasterCategoryID'],
                'vatSubCategoryID' => $value['vatSubCategoryID']
            ];

            $res = QuotationDetails::where('quotationDetailsID', $value['quotationDetailsID'])
                                    ->update($updateData);
        }

        return ['status' => true];
    }

    public function updateDeliveryOrderDetailVATCategories($items)
    {
        foreach ($items as $key => $value) {
            $value = $this->convertArrayToSelectedValue($value, ['vatMasterCategoryID', 'vatSubCategoryID']);

            $updateData = [
                'vatMasterCategoryID' => $value['vatMasterCategoryID'],
                'vatSubCategoryID' => $value['vatSubCategoryID']
            ];

            $res = DeliveryOrderDetail::where('deliveryOrderDetailID', $value['deliveryOrderDetailID'])
                                    ->update($updateData);
        }

        return ['status' => true];
    }

    public function updateCustomerInvoiceDetailVATCategories($items)
    {
        $custInvoiceDirectID = (sizeof($items) > 0) ? $items[0]['custInvoiceDirectID'] : 0; 

        $customerInvoice = CustomerInvoiceDirect::find($custInvoiceDirectID);

        if ($customerInvoice && $customerInvoice->isPerforma == 0) {
            foreach ($items as $key => $value) {
                $value = $this->convertArrayToSelectedValue($value, ['vatMasterCategoryID', 'vatSubCategoryID']);

                $updateData = [
                    'vatMasterCategoryID' => $value['vatMasterCategoryID'],
                    'vatSubCategoryID' => $value['vatSubCategoryID']
                ];

                $res = CustomerInvoiceDirectDetail::where('custInvDirDetAutoID', $value['custInvDirDetAutoID'])
                                        ->update($updateData);
            }
        } else {
            foreach ($items as $key => $value) {
                $value = $this->convertArrayToSelectedValue($value, ['vatMasterCategoryID', 'vatSubCategoryID']);

                $updateData = [
                    'vatMasterCategoryID' => $value['vatMasterCategoryID'],
                    'vatSubCategoryID' => $value['vatSubCategoryID']
                ];

                $res = CustomerInvoiceItemDetails::where('customerItemDetailID', $value['customerItemDetailID'])
                                        ->update($updateData);
            }
        }


        return ['status' => true];
    }
}
