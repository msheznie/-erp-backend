<?php
/**
=============================================
-- File Name : FinanceItemCategorySubAPIController.php
-- Project Name : ERP
-- Module Name :  Finance Item Category Sub
-- Author : Mohamed Fayas
-- Create date : 14 - March 2018
-- Description : This file contains the all CRUD for Finance Item Category Sub
-- REVISION HISTORY
 */
namespace App\Http\Controllers\API;

use App\helper\Helper;
use App\Http\Requests\API\CreateFinanceItemCategorySubAPIRequest;
use App\Http\Requests\API\UpdateFinanceItemCategorySubAPIRequest;
use App\Models\ChartOfAccount;
use App\Models\CustomerInvoiceItemDetails;
use App\Models\DeliveryOrderDetail;
use App\Models\FinanceItemCategorySub;
use App\Models\FinanceItemcategorySubAssigned;
use App\Models\FinanceItemCategoryTypes;
use App\Models\GRVDetails;
use App\Models\ItemCategoryTypeMaster;
use App\Models\ItemIssueDetails;
use App\Models\ItemMaster;
use App\Models\PurchaseOrderDetails;
use App\Models\PurchaseReturnDetails;
use App\Models\QuotationDetails;
use App\Models\SalesReturnDetail;
use App\Models\SupplierInvoiceDirectItem;
use App\Models\SupplierInvoiceItemDetail;
use App\Models\User;
use App\Repositories\FinanceItemcategorySubAssignedRepository;
use App\Repositories\FinanceItemCategorySubRepository;
use App\Traits\AuditLogsTrait;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\Log;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\UserRepository;
use Response;
use Illuminate\Support\Facades\Auth;
use Mpdf\Tag\Select;
use App\Jobs\ResetFinaceSubCategoryValuesInAllDocuments;
use Artisan;
use GuzzleHttp\Client;
use Carbon\Carbon;
use App\Services\AuditLog\ItemFinanceCategoryAuditService;

use Illuminate\Support\Facades\Validator;
/**
 * Class FinanceItemCategorySubController
 * @package App\Http\Controllers\API
 */

class FinanceItemCategorySubAPIController extends AppBaseController
{
    /** @var  FinanceItemCategorySubRepository */
    private $financeItemCategorySubRepository;
    private $userRepository;
    private $financeItemcategorySubAssignedRepository;

    use AuditLogsTrait;
    public function __construct(FinanceItemCategorySubRepository $financeItemCategorySubRepo,UserRepository $userRepo,
                                FinanceItemcategorySubAssignedRepository $financeItemcategorySubAssignedRepo)
    {
        $this->financeItemCategorySubRepository = $financeItemCategorySubRepo;
        $this->userRepository = $userRepo;
        $this->financeItemcategorySubAssignedRepository = $financeItemcategorySubAssignedRepo;
    }

    /**
     * Display a listing of the FinanceItemCategorySub.
     * GET|HEAD /financeItemCategorySubs
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->financeItemCategorySubRepository->pushCriteria(new RequestCriteria($request));
        $this->financeItemCategorySubRepository->pushCriteria(new LimitOffsetCriteria($request));
        $financeItemCategorySubs = $this->financeItemCategorySubRepository->all();

        return $this->sendResponse($financeItemCategorySubs->toArray(), trans('custom.finance_item_category_subs_retrieved_successfully'));
    }


    public function getSubcategoriesBymainCategory(Request $request) {

        if($request->itemCategoryID) {
            if($request->primaryCompanySystemID) {
                $companyId = $request->primaryCompanySystemID;

                $isGroup = \Helper::checkIsCompanyGroup($companyId);

                if ($isGroup) {
                    $companyID = \Helper::getGroupCompany($companyId);
                }
                else {
                    $companyID = [$companyId];
                }

                $itemType = collect($request->itemType);
                $isChangeItemType = $request->isChangeItemType;
                $itemCodeSystem = $request->itemCodeSystem;

                $companySystemID = isset($request->primaryCompanySystemID[0]) ? $request->primaryCompanySystemID[0] : $request->primaryCompanySystemID;

                if($isChangeItemType == 1) {

                    $transactions = [];

                    $itemMaster = ItemMaster::with('item_category_type')->where('itemCodeSystem', $itemCodeSystem)->first();
                    $categoryType = $itemMaster->item_category_type ? collect($itemMaster->item_category_type) : null;

                    if (is_object($itemType) && is_object($categoryType)) {
                        $oldData = $categoryType->pluck('categoryTypeID');
                        $newData = $itemType->pluck('id');

                        $diffOldToNew = $oldData->diff($newData);

                        $purchasePresent = $diffOldToNew->contains(1);
                        $salesPresent = $diffOldToNew->contains(2);

                        if($purchasePresent) {
                            $poDetails = PurchaseOrderDetails::where('itemCode', $itemCodeSystem)->join('erp_purchaseordermaster', 'erp_purchaseorderdetails.purchaseOrderMasterID', '=', 'erp_purchaseordermaster.purchaseOrderID')->where('erp_purchaseorderdetails.companySystemID', $companySystemID)->groupBy('erp_purchaseordermaster.purchaseOrderID')->get();
                            foreach ($poDetails as $poDetail) {
                                $transactions[] = ['code' => $poDetail->purchaseOrderCode, 'timestamp' => $poDetail->createdDateTime];
                            }

                            $issueDetails = ItemIssueDetails::where('itemCodeSystem', $itemCodeSystem)->join('erp_itemissuemaster', 'erp_itemissuedetails.itemIssueAutoID', '=', 'erp_itemissuemaster.itemIssueAutoID')->where('erp_itemissuemaster.companySystemID', $companySystemID)->groupBy('erp_itemissuemaster.itemIssueAutoID')->get();
                            foreach ($issueDetails as $issueDetail) {
                                $transactions[] = ['code' => $issueDetail->itemIssueCode, 'timestamp' => $issueDetail->timestamp];
                            }

                            $grvDetails = GRVDetails::where('itemCode', $itemCodeSystem)->join('erp_grvmaster', 'erp_grvdetails.grvAutoID', '=', 'erp_grvmaster.grvAutoID')->where('erp_grvmaster.companySystemID', $companySystemID)->where('grvTypeID', 1)->groupBy('erp_grvmaster.grvAutoID')->get();
                            foreach ($grvDetails as $grvDetail) {
                                $transactions[] = ['code' => $grvDetail->grvPrimaryCode, 'timestamp' => $grvDetail->createdDateTime];
                            }

                            $sis = SupplierInvoiceDirectItem::where('itemCode', $itemCodeSystem)->join('erp_bookinvsuppmaster', 'supplier_invoice_items.bookingSuppMasInvAutoID', '=', 'erp_bookinvsuppmaster.bookingSuppMasInvAutoID')->where('erp_bookinvsuppmaster.companySystemID', $companySystemID)->groupBy('erp_bookinvsuppmaster.bookingSuppMasInvAutoID')->get();
                            foreach ($sis as $si) {
                                $transactions[] = ['code' => $si->bookingInvCode, 'timestamp' => $si->created_at];

                            }

                            $prs = PurchaseReturnDetails::where('itemCode', $itemCodeSystem)->join('erp_purchasereturnmaster', 'erp_purchasereturndetails.purhaseReturnAutoID', '=', 'erp_purchasereturnmaster.purhaseReturnAutoID')->where('erp_purchasereturnmaster.companySystemID', $companySystemID)->groupBy('erp_purchasereturnmaster.purhaseReturnAutoID')->get();
                            foreach ($prs as $pr) {
                                $transactions[] = ['code' => $pr->purchaseReturnCode, 'timestamp' => $pr->timeStamp];

                            }
                        }

                        if($salesPresent) {

                            $qus = QuotationDetails::where('itemAutoID', $itemCodeSystem)->join('erp_quotationmaster', 'erp_quotationdetails.quotationMasterID', '=', 'erp_quotationmaster.quotationMasterID')->where('erp_quotationmaster.companySystemID', $companySystemID)->groupBy('erp_quotationmaster.quotationMasterID')->get();
                            foreach ($qus as $qu) {
                                $transactions[] = ['code' => $qu->quotationCode, 'timestamp' => $qu->createdDateTime];
                            }

                            $deos = DeliveryOrderDetail::where('itemCodeSystem', $itemCodeSystem)->join('erp_delivery_order', 'erp_delivery_order_detail.deliveryOrderID', '=', 'erp_delivery_order.deliveryOrderID')->where('erp_delivery_order.companySystemID', $companySystemID)->groupBy('erp_delivery_order.deliveryOrderID')->get();
                            foreach ($deos as $deo) {
                                $transactions[] = ['code' => $deo->deliveryOrderCode, 'timestamp' => $deo->timestamp];
                            }

                            $cus = CustomerInvoiceItemDetails::where('itemCodeSystem', $itemCodeSystem)->join('erp_custinvoicedirect', 'erp_customerinvoiceitemdetails.custInvoiceDirectAutoID', '=', 'erp_custinvoicedirect.custInvoiceDirectAutoID')->where('erp_custinvoicedirect.companySystemID', $companySystemID)->groupBy('erp_custinvoicedirect.custInvoiceDirectAutoID')->get();
                            foreach ($cus as $cu) {
                                $transactions[] = ['code' => $cu->bookingInvCode, 'timestamp' => $cu->timestamp];
                            }

                            $srs = SalesReturnDetail::where('itemCodeSystem', $itemCodeSystem)->join('salesreturn', 'salesreturndetails.salesReturnID', '=', 'salesreturn.id')->where('salesreturn.companySystemID', $companySystemID)->groupBy('salesreturn.id')->get();
                            foreach ($srs as $sr) {
                                $transactions[] = ['code' => $sr->salesReturnCode, 'timestamp' => $sr->timestamp];
                            }
                        }

                        usort($transactions, function ($a, $b) {
                            return $a['timestamp'] <=> $b['timestamp'];
                        });

                        $transactions = array_slice($transactions, 0, 10);

                        $transactionCodes = array_map(function ($transaction) {
                            return $transaction['code'];
                        }, $transactions);
                        if (count($transactionCodes) > 0) {
                            return $this->sendError($transactionCodes, 422);
                        }
                    }
                }

                if (is_object($itemType)) {
                    if (count($itemType) > 1) {
                        $subCategories = FinanceItemcategorySubAssigned::where('mainItemCategoryID', $request->itemCategoryID)
                            ->where('isActive', 1)
                            ->whereHas('finance_item_category_sub', function ($query) {
                                $query->where('isActive', 1);
                            })
                            ->whereIn('companySystemID', $companyID)
                            ->where('isAssigned', -1)
                            ->with(['finance_gl_code_bs', 'finance_gl_code_pl'])
                            ->groupBy('itemCategorySubID')
                            ->get();
                    }
                    else {
                        $subCategories = FinanceItemcategorySubAssigned::where('mainItemCategoryID', $request->itemCategoryID)
                            ->where('isActive', 1)
                            ->whereHas('finance_item_category_sub', function ($query) {
                                $query->where('isActive', 1);
                            })
                            ->whereIn('companySystemID', $companyID)
                            ->where('isAssigned', -1)
                            ->with(['finance_gl_code_bs', 'finance_gl_code_pl'])
                            ->groupBy('itemCategorySubID');

                        if (isset($itemType->first()['id']) && ($itemType->first()['id'] == 2)) {
                            $subCategories = $subCategories->whereHas('finance_item_category_type', function ($query) {
                                $query->whereIn('categoryTypeID', ItemCategoryTypeMaster::salesItems());
                            })->get();
                        }

                        if (isset($itemType->first()['id']) && $itemType->first()['id'] == 1) {
                            $subCategories = $subCategories->whereHas('finance_item_category_type', function ($query) {
                                $query->whereIn('categoryTypeID', ItemCategoryTypeMaster::purchaseItems());
                            })->get();
                        }
                    }
                }
            }
            else {
                return FinanceItemCategorySub::where('itemCategoryID',$request->itemCategoryID)
                    ->with(['finance_gl_code_bs','finance_gl_code_pl'])
                    ->where('isActive',1)
                    ->get();
            }

            $itemCategorySubArray = [];
            $i=0;
            foreach ($subCategories as $value) {
                $itemCategorySubArray[$i] = array_except($value,['finance_gl_code_bs','finance_gl_code_pl','finance_gl_code_revenue']);
                if($value->financeGLcodePLSystemID && $value->finance_gl_code_pl != null) {
                    $accountCode = isset($value->finance_gl_code_pl->AccountCode)?$value->finance_gl_code_pl->AccountCode:'';
                    $accountDescription = isset($value->finance_gl_code_pl->AccountDescription)?$value->finance_gl_code_pl->AccountDescription:'';

                }
                else if($value->financeGLcodebBSSystemID && $value->finance_gl_code_bs != null) {

                    $accountCode = isset($value->finance_gl_code_bs->AccountCode)?$value->finance_gl_code_bs->AccountCode:'';
                    $accountDescription = isset($value->finance_gl_code_bs->AccountDescription)?$value->finance_gl_code_bs->AccountDescription:'';

                }
                else if($value->financeGLcodeRevenueSystemID && $value->finance_gl_code_revenue != null) {

                    $accountCode = isset($value->finance_gl_code_revenue->AccountCode)?$value->finance_gl_code_revenue->AccountCode:'';
                    $accountDescription = isset($value->finance_gl_code_revenue->AccountDescription)?$value->finance_gl_code_revenue->AccountDescription:'';

                }
                else {
                    $accountCode = '';
                    $accountDescription = '';
                }
                $itemCategorySubArray[$i]['labelkey'] = $value->categoryDescription." - ".$accountCode."  ".$accountDescription;
                $i++;
            }
        }
        else {
            $itemCategorySubArray = [];
        }

        return $this->sendResponse($itemCategorySubArray, trans('custom.finance_item_category_subs_retrieved_successfully'));
    }

    public function getSubcategoryExpiryStatus(Request $request){
        $input = $request->all();
        $expiryStatus = FinanceItemCategorySub::where('itemCategorySubID',$input)->Select('expiryYN', 'trackingType')->first();
        return $this->sendResponse($expiryStatus, trans('custom.finance_item_category_subs_retrieved_successfully'));

    }

     public function getSubcategoriesBymainCategories(Request $request){

        $companyId = $request->get('selectedCompanyId');
        $mainCategory = $request->get('mainCategory');

        $mainCategoryIds = collect($mainCategory)->pluck('id');

        $isGroup = \Helper::checkIsCompanyGroup($companyId);

        if ($isGroup) {
            $companyID = \Helper::getGroupCompany($companyId);
        } else {
            $companyID = [$companyId];
        }

        $subCategories = FinanceItemcategorySubAssigned::whereIn('mainItemCategoryID',$mainCategoryIds)
                                            ->where('isActive',1)
                                            ->whereHas('finance_item_category_sub', function ($query){
                                                $query->where('isActive',1);
                                            })
                                            ->whereIn('companySystemID',$companyID)
                                            ->where('isAssigned',-1)
                                            ->groupBy('itemCategorySubID')
                                            ->get();

        return $this->sendResponse($subCategories, trans('custom.finance_item_category_subs_retrieved_successfully'));
    }

    /**
     * Store a newly created FinanceItemCategorySub in storage.
     * POST /financeItemCategorySubs
     *
     * @param CreateFinanceItemCategorySubAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateFinanceItemCategorySubAPIRequest $request)
    {
        $input = $request->all();

        if(array_key_exists ('DT_Row_Index' , $input )){
            unset($input['DT_Row_Index']);
        }

        if(array_key_exists ('finance_gl_code_bs' , $input )){
            unset($input['finance_gl_code_bs']);
        }

        if(array_key_exists ('finance_gl_code_pl' , $input )){
            unset($input['finance_gl_code_pl']);
        }

        if(array_key_exists ('finance_gl_code_revenue' , $input )){
            unset($input['finance_gl_code_revenue']);
        }

        if(array_key_exists ('Index' , $input )){
            unset($input['Index']);
        }

        if(array_key_exists ('Actions' , $input )){
            unset($input['Actions']);
        }

        /*foreach ($input as $key => $value) {
            if (is_array($input[$key])) {
                if (count($input[$key]) > 0) {
                    $input[$key] = $input[$key][0];
                } else {
                    $input[$key] = 0;
                }
            }
        }*/
        $input =  $this->convertArrayToSelectedValue($input,['itemCategoryID','financeGLcodebBSSystemID','financeGLcodePLSystemID','financeGLcodeRevenueSystemID']);

        //

        if(isset($input['financeGLcodebBSSystemID']) && !$input['financeGLcodebBSSystemID']){
            $input['financeGLcodebBSSystemID'] = null;
            $input['financeGLcodebBS'] = null;
        }
        if(isset($input['financeGLcodePLSystemID']) && !$input['financeGLcodePLSystemID']){
            $input['financeGLcodePLSystemID'] = null;
            $input['financeGLcodePL'] = null;
        }
        if(isset($input['financeGLcodeRevenueSystemID']) && !$input['financeGLcodeRevenueSystemID']){
            $input['financeGLcodeRevenueSystemID'] = null;
            $input['financeGLcodeRevenue'] = null;
        }

        $id = Auth::id();
        $user = $this->userRepository->with(['employee'])->findWithoutFail($id);
        $empId = $user->employee['empID'];

        if(array_key_exists ('financeGLcodebBSSystemID' , $input )){
            $financeBS = ChartOfAccount::where('chartOfAccountSystemID',$input['financeGLcodebBSSystemID'])->first();

            if($financeBS){
                $input['financeGLcodebBS'] = $financeBS->AccountCode ;
            }else{
                $input['financeGLcodebBSSystemID'] = null;
                $input['financeGLcodebBS'] = null;
            }

        }else{
            $input['financeGLcodebBSSystemID'] = null;
            $input['financeGLcodebBS'] = null;
        }

        if(array_key_exists ('financeGLcodePLSystemID' , $input )){
            $financePL = ChartOfAccount::where('chartOfAccountSystemID',$input['financeGLcodePLSystemID'])->first();
            if($financePL){
                $input['financeGLcodePL'] = $financePL->AccountCode ;
            }else{
                $input['financeGLcodePLSystemID'] = null;
                $input['financeGLcodePL'] = null;
            }
        }else{
            $input['financeGLcodePLSystemID'] = null;
            $input['financeGLcodePL'] = null;
        }

        if(array_key_exists ('financeGLcodeRevenueSystemID' , $input )){
            $financePL = ChartOfAccount::where('chartOfAccountSystemID',$input['financeGLcodeRevenueSystemID'])->first();
            if($financePL){
                $input['financeGLcodeRevenue'] = $financePL->AccountCode ;
            }else{
                $input['financeGLcodeRevenueSystemID'] = null;
                $input['financeGLcodeRevenue'] = null;
            }
        }else{
            $input['financeGLcodeRevenueSystemID'] = null;
            $input['financeGLcodeRevenue'] = null;
        }

        if(isset($input['includePLForGRVYN']) && $input['includePLForGRVYN'] == 1 || $input['includePLForGRVYN'] == true){
            $input['includePLForGRVYN'] = -1;
        }

        if( array_key_exists ('itemCategorySubID' , $input )){

            $financeItemCategorySubs = FinanceItemCategorySub::where('itemCategorySubID', $input['itemCategorySubID'])->first();

            $input = array_except($input,['companySystemID','finance_item_category_master']);

            if (empty($financeItemCategorySubs)) {
                return $this->sendError(trans('custom.sub_category_not_found'));
            }

            foreach ($input as $key => $value) {
                $financeItemCategorySubs->$key = $value;
            }
            
            $financeItemCategorySubs->modifiedPc = gethostname();
            $financeItemCategorySubs->modifiedUser = $empId;
            $financeItemCategorySubs->enableSpecification = $input['enableSpecification'];
            $financeItemCategorySubs->save();

            $this->financeItemcategorySubAssignedRepository->where(
                'itemCategorySubID', $input['itemCategorySubID']
            )->update(
                array(
                    'financeGLcodePL' => $financeItemCategorySubs->financeGLcodePL,
                    'financeGLcodePLSystemID' => $financeItemCategorySubs->financeGLcodePLSystemID,
                    'financeGLcodeRevenue' => $financeItemCategorySubs->financeGLcodeRevenue,
                    'financeGLcodeRevenueSystemID' => $financeItemCategorySubs->financeGLcodeRevenueSystemID,
                    'financeGLcodebBS' => $financeItemCategorySubs->financeGLcodebBS,
                    'financeGLcodebBSSystemID' => $financeItemCategorySubs->financeGLcodebBSSystemID,
                    'includePLForGRVYN' => $financeItemCategorySubs->includePLForGRVYN,
                    'categoryDescription' => $financeItemCategorySubs->categoryDescription
                )
            );

        }else{
            $input['createdPcID'] = gethostname();
            $input['createdUserID'] = $empId;
            $financeItemCategorySubs = $this->financeItemCategorySubRepository->create($input);
        }

        return $this->sendResponse($financeItemCategorySubs->toArray(), trans('custom.finance_item_category_sub_saved_successfully'));
    }

    /**
     * Display the specified FinanceItemCategorySub.
     * GET|HEAD /financeItemCategorySubs/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var FinanceItemCategorySub $financeItemCategorySub */
        $financeItemCategorySub = $this->financeItemCategorySubRepository->findWithoutFail($id);

        if (empty($financeItemCategorySub)) {
            return $this->sendError(trans('custom.finance_item_category_sub_not_found'));
        }

        return $this->sendResponse($financeItemCategorySub->toArray(), trans('custom.finance_item_category_sub_retrieved_successfully'));
    }

    /**
     * Update the specified FinanceItemCategorySub in storage.
     * PUT/PATCH /financeItemCategorySubs/{id}
     *
     * @param  int $id
     * @param UpdateFinanceItemCategorySubAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateFinanceItemCategorySubAPIRequest $request)
    {
        $input = $request->all();

        /** @var FinanceItemCategorySub $financeItemCategorySub */
        $financeItemCategorySub = $this->financeItemCategorySubRepository->findWithoutFail($id);

        if (empty($financeItemCategorySub)) {
            return $this->sendError(trans('custom.finance_item_category_sub_not_found'));
        }

        $previosValue = $financeItemCategorySub->toArray();

        $employee = Helper::getEmployeeInfo();
        $input['modifiedPc'] = gethostname();
        $input['modifiedUser'] = $employee->empID;

        $this->financeItemCategorySubRepository->update($input, $id);

         if(isset($input['isActive'])) {
            $uuid = isset($input['tenant_uuid']) ? $input['tenant_uuid'] : 'local';
            $db = isset($input['db']) ? $input['db'] : '';
            $newValue = ['isActive' => $input['isActive']];

            $this->auditLog($db, $id,$uuid, "financeitemcategorysub", $financeItemCategorySub->categoryDescription." has updated", "U", $newValue, $previosValue);
         }

        return $this->sendResponse($financeItemCategorySub->toArray(), trans('custom.finance_item_category_sub_updated_successfully'));
    }

    public function finance_item_category_subs_update(Request $request)
    {

       $input = $request->all();
       // financeGLcodebBSSystemID - balance sheet code 
       // financeGLcodePLSystemID - cost gl

        
       $data = $this->convertArrayToSelectedValue($input,['financeGLcodebBSSystemID','financeGLcodePLSystemID','financeGLcodeRevenueSystemID','financeCogsGLcodePLSystemID']);
        
       $balance_sheet_gl_code = (isset($data['financeGLcodebBSSystemID'])) ? $data['financeGLcodebBSSystemID'] : null;
       $consumption_gl_code = (isset($data['financeGLcodePLSystemID'])) ? $data['financeGLcodePLSystemID'] : null;
       $cogs_gl_code = (isset($data['financeCogsGLcodePLSystemID'])) ? $data['financeCogsGLcodePLSystemID'] : null;
       $revenue_gl_code = (isset($data['financeGLcodeRevenueSystemID'])) ? $data['financeGLcodeRevenueSystemID'] : null;
       $include_pl_for_grvn = (isset($data['includePLForGRVYN'])) ? $data['includePLForGRVYN'] : null;

       $input =  $this->convertArrayToSelectedValue($input,['itemCategoryID','financeGLcodebBSSystemID','financeGLcodePLSystemID','financeGLcodeRevenueSystemID','trackingType','financeCogsGLcodePLSystemID']);
       if($input['itemCategoryID'] != 3){
           if(!$include_pl_for_grvn) {
               if(!$balance_sheet_gl_code) {
                   if($consumption_gl_code) {
                       return $this->sendError("Please check 'Include PL For GRV YN'",500);
                   }
               }
           }
       }


       $collectCategoryType = collect($input['categoryType']);
       $categoryTypeID = $collectCategoryType->pluck('id');
       $categoryTypeIDLength = $categoryTypeID->count();
       $itemCategory = $input['itemCategoryID'];

        if ($itemCategory == 1 && $categoryTypeIDLength == 1 && $categoryTypeID->contains(2)) {
            if(!$cogs_gl_code) {
                return $this->sendError("Please select 'COGS GL Code'",500);
            }
            if(!$balance_sheet_gl_code){
                return $this->sendError("Please select 'Balance Sheet GL Code'",500);
            }
            if(!$revenue_gl_code){
                return $this->sendError("Please select 'Revenue GL Code'",500);
            }

        } elseif ($itemCategory == 1 && $categoryTypeIDLength == 1 && $categoryTypeID->contains(1)) {
            if(!$balance_sheet_gl_code){
                return $this->sendError("Please select 'Balance Sheet GL Code'",500);
            }
            if(!$consumption_gl_code) {
                return $this->sendError("Please select 'Consumption GL Code'",500);
            }

        } elseif ($itemCategory == 1 && $categoryTypeIDLength == 2 && $categoryTypeID->contains(1) && $categoryTypeID->contains(2)) {
            if(!$balance_sheet_gl_code){
                return $this->sendError("Please select 'Balance Sheet GL Code'",500);
            }
            if(!$cogs_gl_code) {
                return $this->sendError("Please select 'COGS GL Code'",500);
            }
            if(!$consumption_gl_code) {
                return $this->sendError("Please select 'Consumption GL Code'",500);
            }
            if(!$revenue_gl_code){
                return $this->sendError("Please select 'Revenue GL Code'",500);
            }

        } elseif ($itemCategory == 2 && $categoryTypeIDLength == 1 && $categoryTypeID->contains(2)) {
            if(!$cogs_gl_code) {
                return $this->sendError("Please select 'COGS GL Code'",500);
            }
            if(!$revenue_gl_code){
                return $this->sendError("Please select 'Revenue GL Code'",500);
            }
        } elseif ($itemCategory == 2 && $categoryTypeIDLength == 1 && $categoryTypeID->contains(1)) {
            if(!$consumption_gl_code) {
                return $this->sendError("Please select 'Consumption GL Code'",500);
            }
        } elseif ($itemCategory == 2 && $categoryTypeIDLength == 2 && $categoryTypeID->contains(1) && $categoryTypeID->contains(2)) {
            if(!$cogs_gl_code) {
                return $this->sendError("Please select 'COGS GL Code'",500);
            }
            if(!$consumption_gl_code) {
                return $this->sendError("Please select 'Consumption GL Code'",500);
            }
            if(!$revenue_gl_code){
                return $this->sendError("Please select 'Revenue GL Code'",500);
            }
        } elseif ($itemCategory == 3 && ($categoryTypeID->contains(2) || $categoryTypeID->contains(1) || $categoryTypeID->contains(1) && $categoryTypeID->contains(2))) {
            if(!$balance_sheet_gl_code){
                return $this->sendError("Please select 'Balance Sheet GL Code'",500);
            }
        } elseif ($itemCategory == 4  && $categoryTypeIDLength == 1 && $categoryTypeID->contains(2)) {
            if(!$revenue_gl_code){
                return $this->sendError("Please select 'Revenue GL Code'",500);
            }
            if(!$cogs_gl_code) {
                return $this->sendError("Please select 'COGS GL Code'",500);
            }
        } elseif ($itemCategory == 4 && $categoryTypeIDLength == 1 && $categoryTypeID->contains(1)) {
            if(!$consumption_gl_code) {
                return $this->sendError("Please select 'Consumption GL Code'",500);
            }
        } elseif ($itemCategory == 4 && $categoryTypeIDLength == 2 && $categoryTypeID->contains(1) && $categoryTypeID->contains(2)) {
            if(!$cogs_gl_code) {
                return $this->sendError("Please select 'COGS GL Code'",500);
            }
            if(!$consumption_gl_code) {
                return $this->sendError("Please select 'Consumption GL Code'",500);
            }
            if(!$revenue_gl_code){
                return $this->sendError("Please select 'Revenue GL Code'",500);
            }
        }
        
        $financeGLcodebBS = ChartOfAccount::find(isset($input['financeGLcodebBSSystemID']) ? $input['financeGLcodebBSSystemID'] : null);
        $financeGLcodePL = ChartOfAccount::find(isset($input['financeGLcodePLSystemID']) ? $input['financeGLcodePLSystemID'] : null);
        $financeGLcodeRevenue = ChartOfAccount::find(isset($input['financeGLcodeRevenueSystemID']) ? $input['financeGLcodeRevenueSystemID'] : null);
        $financeCogsGLcodePL = ChartOfAccount::find($cogs_gl_code);

        if( ($input['isBSGlSelected']) && (($financeGLcodePL && ($financeGLcodePL->controlAccountsSystemID == 3 || $financeGLcodePL->controlAccountsSystemID == 4)) || ($financeCogsGLcodePL && ($financeCogsGLcodePL->controlAccountsSystemID == 3 || $financeCogsGLcodePL->controlAccountsSystemID == 4))) )
        {
            return $this->sendError('Balance sheet GL code/s has been selected for Consumption/COGS GL code selection. Generally these two fields correspond to expense GL codes. Are you sure you want to proceed ?', 500,['type' => 'BSGlSelected']);
        }


        $input['financeGLcodebBS'] = isset($financeGLcodebBS->AccountCode) ? $financeGLcodebBS->AccountCode : null;
        $input['financeGLcodePL'] = isset($financeGLcodePL->AccountCode) ? $financeGLcodePL->AccountCode : null;
        $input['financeCogsGLcodePL'] = isset($financeCogsGLcodePL->AccountCode) ? $financeCogsGLcodePL->AccountCode : null;
        $input['financeGLcodeRevenue'] = isset($financeGLcodeRevenue->AccountCode) ? $financeGLcodeRevenue->AccountCode : null;

        $employee = Helper::getEmployeeInfo();
        $input['modifiedPc'] = gethostname();
        $input['modifiedUser'] = $employee->empID;


        $categoryTypeNew = collect($input['categoryType']);
        $itemCategorySubID = isset($input['itemCategorySubID']) ? $input['itemCategorySubID']: null;

        $financeItemSubCategory = FinanceItemCategorySub::with('finance_item_category_type')->where('itemCategorySubID', $itemCategorySubID)->first();
        if (!empty($financeItemSubCategory)) {
            $categoryTypeOld = $financeItemSubCategory->finance_item_category_type->pluck('categoryTypeID');
            if (is_object($categoryTypeOld) && is_object($categoryTypeNew)) {
                $oldJson = $categoryTypeOld->toArray();
                $newJson = $categoryTypeNew->pluck('id')->toArray();
                $diff = array_diff($oldJson, $newJson);
                if (!empty($diff)) {
                    $isItemMaster = ItemMaster::with('item_category_type')->where('financeCategorySub', $input['itemCategorySubID'])->first();
                    if (!empty($isItemMaster)) {
                        $itemArr = $isItemMaster->item_category_type->pluck('categoryTypeID')->toArray();
                        $newItemArr = $categoryTypeNew->pluck('id')->toArray();
                        foreach ($itemArr as $item) {
                            if (!in_array($item, $newItemArr)) {
                                return $this->sendError("Cannot change the Category Type. This Item Finance Sub Category is already selected for Item/Items.'", 500);
                            }
                        }
                    }
                }
            }
        }


        $masterData = [
            'categoryDescription' => $input['categoryDescription'],
            'enableSpecification' => isset($input['enableSpecification']) ? $input['enableSpecification'] : null,
            'itemCategoryID' => $input['itemCategoryID'],
            'financeGLcodebBSSystemID' => isset($input['financeGLcodebBSSystemID']) ? $input['financeGLcodebBSSystemID'] : null,
            'financeGLcodePLSystemID' => isset($input['financeGLcodePLSystemID']) ? $input['financeGLcodePLSystemID'] :null ,
            'financeCogsGLcodePLSystemID' => $cogs_gl_code ,
            'financeGLcodeRevenueSystemID' => isset($input['financeGLcodeRevenueSystemID']) ? $input['financeGLcodeRevenueSystemID'] :null,
            'includePLForGRVYN' => (isset($input['includePLForGRVYN']) && $input['includePLForGRVYN']) ? -1 : 0,
            'trackingType' => isset($input['trackingType']) ? $input['trackingType'] :null,
            'financeGLcodebBS' => $input['financeGLcodebBS'],
            'financeGLcodePL' => $input['financeGLcodePL'],
            'financeCogsGLcodePL' => $input['financeCogsGLcodePL'],
            'financeGLcodeRevenue' => $input['financeGLcodeRevenue'],
            'modifiedPc' => $input['modifiedPc'],
            'modifiedUser' => $input['modifiedUser'],
            'categoryType' => implode(',', $categoryTypeNew->pluck('itemName')->toArray())
        ];


        $uuid = isset($input['tenant_uuid']) ? $input['tenant_uuid'] : 'local';
        $db = isset($input['db']) ? $input['db'] : '';
        $newValue = $masterData;

        if(isset($masterData['categoryType'])) {
            unset($masterData['categoryType']);
        }
        
        if (isset($input['itemCategorySubID'])){
            $originalData = FinanceItemCategorySub::with('finance_item_category_type')->where('itemCategorySubID', $input['itemCategorySubID'])->first();

            $itemCategorySubUpdate = FinanceItemCategorySub::where('itemCategorySubID', $input['itemCategorySubID'])->update($masterData);

            FinanceItemCategoryTypes::where('itemCategorySubID', $input['itemCategorySubID'])->delete();

            foreach ($categoryTypeNew as $key => $value) {
                $financeItemCategoryType = new FinanceItemCategoryTypes();
                $financeItemCategoryType->itemCategorySubID = $input['itemCategorySubID'];
                $financeItemCategoryType->categoryTypeID = $value['id'];
                $financeItemCategoryType->save();
            }

            $previosValue = $originalData->toArray();
            $previousCategoryType = [];
            foreach ($originalData['finance_item_category_type'] as $key => $value) {
                $previousCategoryType[] = $value['category_type_master']['name'];
            }

            $previosValue['categoryType'] = implode(',', $previousCategoryType);

            if(isset($previosValue['finance_item_category_type'])) {
                unset($previosValue['finance_item_category_type']);
            }

            unset($masterData['itemCategoryID'],$masterData['trackingType'],$masterData['modifiedPc'],$masterData['modifiedUser']);
            $financeItemcategorySubAssigned  = FinanceItemcategorySubAssigned::where('itemCategorySubID', $input['itemCategorySubID'])
                                    ->update($masterData);

            \Artisan::call('reset:sub-category-values');

            $this->auditLog($db, $input['itemCategorySubID'],$uuid, "financeitemcategorysub", $input['categoryDescription']." has updated", "U", $newValue, $previosValue);
            
            return $this->sendResponse($itemCategorySubUpdate, trans('custom.finance_item_category_sub_updated_successfully'));
        } else {
            $itemCategorySubCreate = FinanceItemCategorySub::create($masterData);

            foreach ($categoryTypeNew as $key => $value) {
                $financeItemCategoryType = new FinanceItemCategoryTypes();
                $financeItemCategoryType->itemCategorySubID = $itemCategorySubCreate->itemCategorySubID;
                $financeItemCategoryType->categoryTypeID = $value['id'];
                $financeItemCategoryType->save();
            }
            
            $this->auditLog($db, $itemCategorySubCreate['itemCategorySubID'],$uuid, "financeitemcategorysub", $input['categoryDescription']." has created", "C", $newValue, []);
            
            return $this->sendResponse($itemCategorySubCreate, trans('custom.finance_item_category_sub_created_successfully'));
        }
    }

    public function financeItemCategorySubsExpiryUpdate(Request $request){

        $input = $request->all();

        $originalData = FinanceItemCategorySub::where('itemCategorySubID', $input['itemCategorySubID'])->first();
        $previosValue = $originalData->toArray();
        
        $itemCategorySubAssignedExpiryUpdate = FinanceItemcategorySubAssigned::where('itemCategorySubID', $input['itemCategorySubID'])
                                                ->update(['expiryYN' => $input['expiryYN']]);

        $itemCategorySubExpiryUpdate = FinanceItemCategorySub::where('itemCategorySubID', $input['itemCategorySubID'])
                                                ->update(['expiryYN' => $input['expiryYN']]);

        $uuid = isset($input['tenant_uuid']) ? $input['tenant_uuid'] : 'local';
        $db = isset($input['db']) ? $input['db'] : '';
        $newValue = ['expiryYN' => $input['expiryYN']];

        $this->auditLog($db, $input['itemCategorySubID'],$uuid, "financeitemcategorysub", $originalData->categoryDescription." has updated", "U", $newValue, $previosValue);

        return $this->sendResponse($itemCategorySubExpiryUpdate, trans('custom.finance_item_category_sub_updated_successfully'));

    }


    public function financeItemCategorySubsAttributesUpdate(Request $request){
        $input = $request->all();

        $originalData = FinanceItemCategorySub::where('itemCategorySubID', $input['itemCategorySubID'])->first();
        $previosValue = $originalData->toArray();

        $itemCategorySubExpiryUpdate = FinanceItemCategorySub::where('itemCategorySubID', $input['itemCategorySubID'])
                                                ->update(['attributesYN' => $input['attributesYN']]);

        $uuid = isset($input['tenant_uuid']) ? $input['tenant_uuid'] : 'local';
        $db = isset($input['db']) ? $input['db'] : '';
        $newValue = ['attributesYN' => $input['attributesYN']];

        $this->auditLog($db, $input['itemCategorySubID'],$uuid, "financeitemcategorysub", $originalData->categoryDescription." has updated", "U", $newValue, $previosValue);

        return $this->sendResponse($itemCategorySubExpiryUpdate, trans('custom.finance_item_category_sub_updated_successfully'));
    }




    /**
     * Remove the specified FinanceItemCategorySub from storage.
     * DELETE /financeItemCategorySubs/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var FinanceItemCategorySub $financeItemCategorySub */
        $financeItemCategorySub = $this->financeItemCategorySubRepository->findWithoutFail($id);

        if (empty($financeItemCategorySub)) {
            return $this->sendError(trans('custom.finance_item_category_sub_not_found'));
        }

        $financeItemCategorySub->delete();

        return $this->sendResponse($id, trans('custom.finance_item_category_sub_deleted_successfully'));
    }
}
