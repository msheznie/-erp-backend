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
use App\Models\FinanceItemCategorySub;
use App\Models\FinanceItemcategorySubAssigned;
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

        return $this->sendResponse($financeItemCategorySubs->toArray(), 'Finance Item Category Subs retrieved successfully');
    }


    public function getSubcategoriesBymainCategory(Request $request){

        if($request->get('itemCategoryID')) {
            if($request->get('primaryCompanySystemID')){
                $companyId = $request->get('primaryCompanySystemID');

                $isGroup = \Helper::checkIsCompanyGroup($companyId);

                if ($isGroup) {
                    $companyID = \Helper::getGroupCompany($companyId);
                } else {
                    $companyID = [$companyId];
                }

                 $subCategories = FinanceItemcategorySubAssigned::where('mainItemCategoryID',$request->get('itemCategoryID'))
                                                    ->where('isActive',1)
                                                    ->whereHas('finance_item_category_sub', function ($query){
                                                        $query->where('isActive',1);
                                                    })
                                                    ->whereIn('companySystemID',$companyID)
                                                    ->where('isAssigned',-1)
                                                    ->with(['finance_gl_code_bs','finance_gl_code_pl'])
                                                    ->groupBy('itemCategorySubID')
                                                    ->get();
            }else{
                return $subCategories = FinanceItemCategorySub::where('itemCategoryID',$request->get('itemCategoryID'))
                    ->with(['finance_gl_code_bs','finance_gl_code_pl'])
                    ->where('isActive',1)
                    ->get();
            }

            $itemCategorySubArray = [];
            $i=0;
            foreach ($subCategories as $value){
                $itemCategorySubArray[$i] = array_except($value,['finance_gl_code_bs','finance_gl_code_pl','finance_gl_code_revenue']);
                if($value->financeGLcodePLSystemID && $value->finance_gl_code_pl != null){
                    $accountCode = isset($value->finance_gl_code_pl->AccountCode)?$value->finance_gl_code_pl->AccountCode:'';
                    $accountDescription = isset($value->finance_gl_code_pl->AccountDescription)?$value->finance_gl_code_pl->AccountDescription:'';

                }else if($value->financeGLcodebBSSystemID && $value->finance_gl_code_bs != null){

                    $accountCode = isset($value->finance_gl_code_bs->AccountCode)?$value->finance_gl_code_bs->AccountCode:'';
                    $accountDescription = isset($value->finance_gl_code_bs->AccountDescription)?$value->finance_gl_code_bs->AccountDescription:'';

                }else if($value->financeGLcodeRevenueSystemID && $value->finance_gl_code_revenue != null){

                    $accountCode = isset($value->finance_gl_code_revenue->AccountCode)?$value->finance_gl_code_revenue->AccountCode:'';
                    $accountDescription = isset($value->finance_gl_code_revenue->AccountDescription)?$value->finance_gl_code_revenue->AccountDescription:'';

                }else{
                    $accountCode = '';
                    $accountDescription = '';
                }
                $itemCategorySubArray[$i]['labelkey'] = $value->categoryDescription." - ".$accountCode."  ".$accountDescription;
                $i++;
            }

        }else{
            $itemCategorySubArray = [];
        }

        return $this->sendResponse($itemCategorySubArray, 'Finance Item Category Subs retrieved successfully');
    }

    public function getSubcategoryExpiryStatus(Request $request){
        $input = $request->all();
        $expiryStatus = FinanceItemCategorySub::where('itemCategorySubID',$input)->Select('expiryYN', 'trackingType')->first();
        return $this->sendResponse($expiryStatus, 'Finance Item Category Subs retrieved successfully');

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

        return $this->sendResponse($subCategories, 'Finance Item Category Subs retrieved successfully');
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
                return $this->sendError('Sub category not found');
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

        return $this->sendResponse($financeItemCategorySubs->toArray(), 'Finance Item Category Sub saved successfully');
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
            return $this->sendError('Finance Item Category Sub not found');
        }

        return $this->sendResponse($financeItemCategorySub->toArray(), 'Finance Item Category Sub retrieved successfully');
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
            return $this->sendError('Finance Item Category Sub not found');
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

        return $this->sendResponse($financeItemCategorySub->toArray(), 'Finance Item Category Sub updated successfully');
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
       $include_pl_for_grvn = (isset($data['includePLForGRVYN'])) ? $data['includePLForGRVYN'] : null;

       $input =  $this->convertArrayToSelectedValue($input,['itemCategoryID','financeGLcodebBSSystemID','financeGLcodePLSystemID','financeGLcodeRevenueSystemID','trackingType','financeCogsGLcodePLSystemID']);
       if($input['itemCategoryID'] != 3){
           if(!$include_pl_for_grvn) {
               if(!$balance_sheet_gl_code) {
                   if($consumption_gl_code) {
                       return $this->sendError("Please check 'Include PL For GRV YN'",500);
                   }else {
                       return $this->sendError("Please select 'Consumption GL Code'",500);
                   }
               }else {
                   if(!$consumption_gl_code) {
                       return $this->sendError("Please select 'Consumption GL Code'",500);
                   }
               }

           }else {
               if(!$balance_sheet_gl_code) {
                   if(!$consumption_gl_code) {
                       return $this->sendError("Please select 'Consumption GL Code'",500);
                   }
               }
           }

           if(!$cogs_gl_code) {
               return $this->sendError("Please select 'COGS GL Code'",500);
           }
       } else{
           if(!$balance_sheet_gl_code){
               return $this->sendError("Please select 'Balance Sheet GL Code'",500);
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
        ];


        $uuid = isset($input['tenant_uuid']) ? $input['tenant_uuid'] : 'local';
        $db = isset($input['db']) ? $input['db'] : '';
        $newValue = $masterData;
        
        if (isset($input['itemCategorySubID'])){
            $originalData = FinanceItemCategorySub::where('itemCategorySubID', $input['itemCategorySubID'])->first();

            $itemCategorySubUpdate = FinanceItemCategorySub::where('itemCategorySubID', $input['itemCategorySubID'])
                                    ->update($masterData);

            $previosValue = $originalData->toArray();


            unset($masterData['itemCategoryID'],$masterData['trackingType'],$masterData['modifiedPc'],$masterData['modifiedUser']);
            $financeItemcategorySubAssigned  = FinanceItemcategorySubAssigned::where('itemCategorySubID', $input['itemCategorySubID'])
                                    ->update($masterData);

            \Artisan::call('reset:sub-category-values');

            $this->auditLog($db, $input['itemCategorySubID'],$uuid, "financeitemcategorysub", $input['categoryDescription']." has updated", "U", $newValue, $previosValue);
            
            return $this->sendResponse($itemCategorySubUpdate, 'Finance Item Category Sub updated successfully');
        } else {
            $itemCategorySubCreate = FinanceItemCategorySub::create($masterData);
            
            $this->auditLog($db, $itemCategorySubCreate['itemCategorySubID'],$uuid, "financeitemcategorysub", $input['categoryDescription']." has created", "C", $newValue, []);
            
            return $this->sendResponse($itemCategorySubCreate, 'Finance Item Category Sub Created successfully');
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

        return $this->sendResponse($itemCategorySubExpiryUpdate, 'Finance Item Category Sub updated successfully');

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

        return $this->sendResponse($itemCategorySubExpiryUpdate, 'Finance Item Category Sub updated successfully');
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
            return $this->sendError('Finance Item Category Sub not found');
        }

        $financeItemCategorySub->delete();

        return $this->sendResponse($id, 'Finance Item Category Sub deleted successfully');
    }
}
