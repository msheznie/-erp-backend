<?php
/**
 * =============================================
 * -- File Name : ItemMasterAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Item Master
 * -- Author : Mohamed Fayas
 * -- Create date : 14 - March 2018
 * -- Description : This file contains the all CRUD for Item Master
 * -- REVISION HISTORY
 * -- Date: 14-March 2018 By: Fayas Description: Added new functions named as getAllItemsMaster(),getItemMasterFormData(),
 * updateItemMaster(),getAssignedCompaniesByItem()
 * -- Date: 03-April 2018 By: Mubashir Description: Added a new function getAllItemsMasterApproval() to display items to be approved
 * -- Date: 10-April 2018 By: Fayas Description: Added a new function itemMasterBulkCreate().
 * -- Date: 05-June 2018 By: Mubashir Description: Modified getAllItemsMaster() to handle filters from local storage
 * -- Date: 17-July 2018 By: Fayas Description: Added new functions named as getItemMasterAudit()
 * -- Date: 30-October 2018 By: Fayas Description: Added new functions named as exportItemMaster()
 * -- Date: 14-December 2018 By: Fayas Description: Added new functions named as itemReferBack()
 * -- Date: 11-January 2019 By: Fayas Description: Added new functions named as getPosItemSearch()
 */


namespace App\Http\Controllers\API;

use App\helper\Helper;
use App\helper\ReopenDocument;
use App\Http\Requests\API\CreateItemMasterAPIRequest;
use App\Http\Requests\API\UpdateItemMasterAPIRequest;
use App\Models\DeliveryOrder;
use App\Models\DocumentApproved;
use App\Models\DocumentReferedHistory;
use App\Models\ErpItemLedger;
use App\Models\FinanceItemcategorySubAssigned;
use App\Models\ItemCategoryTypeMaster;
use App\Models\ItemMaster;
use App\Models\Company;
use App\Models\FinanceItemCategoryMaster;
use App\Models\AssetFinanceCategory;
use App\Models\FinanceItemCategorySub;
use App\Models\FixedAssetCategory;
use App\Models\DocumentMaster;
use App\Models\ItemAssigned;
use App\Models\ItemMasterCategoryType;
use App\Models\ItemMasterRefferedBack;
use App\Models\SupplierCatalogMaster;
use App\Models\TaxVatCategories;
use App\Models\Unit;
use App\Models\UnitConversion;
use App\Models\WarehouseBinLocation;
use App\Models\WarehouseMaster;
use App\Models\YesNoSelection;
use App\Models\UserActivityLogger;
use App\Repositories\ItemMasterRepository;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use Illuminate\Support\Facades\DB;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Config;
use App\helper\CreateExcel;
use App\Models\CompanyPolicyMaster;
use App\Models\PurchaseRequestDetails;
use App\Models\PurchaseOrderDetails;
use App\Models\GRVDetails;
use App\Models\MaterielRequestDetails;
use App\Models\StockAdjustmentDetails;
use App\Models\QuotationDetails;
use App\Models\DeliveryOrderDetail;
use App\Models\CustomerInvoiceItemDetails;
use App\Repositories\UnitConversionRepository;
use App\Traits\AuditLogsTrait;

/**
 * Class ItemMasterController
 * @package App\Http\Controllers\API
 */
class ItemMasterAPIController extends AppBaseController
{
    /** @var  ItemMasterRepository */
    private $itemMasterRepository;
    private $userRepository;
    private $unitConversionRepository;
    use AuditLogsTrait;

    public function __construct(ItemMasterRepository $itemMasterRepo, UserRepository $userRepo, UnitConversionRepository $unitConversionRepo)
    {
        $this->itemMasterRepository = $itemMasterRepo;
        $this->userRepository = $userRepo;
        $this->unitConversionRepository = $unitConversionRepo;
    }

    /**
     * Display a listing of the ItemMaster.
     * GET|HEAD /itemMasters
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->itemMasterRepository->pushCriteria(new RequestCriteria($request));
        $this->itemMasterRepository->pushCriteria(new LimitOffsetCriteria($request));
        $itemMasters = $this->itemMasterRepository->all();

        return $this->sendResponse($itemMasters->toArray(), 'Item Masters retrieved successfully');
    }

    /**
     * Item Master Bulk Create.
     * POST|HEAD /itemMasterBulkCreate
     *
     * @param Request $request
     * @return Response
     */
    public function itemMasterBulkCreate(Request $request)
    {

        $input = $request->all();
        //$input = $this->convertArrayToValue($input);
        $id = Auth::id();
        $user = $this->userRepository->with(['employee'])->findWithoutFail($id);
        $empId = $user->employee['empID'];
        $employee = Helper::getEmployeeInfo();
        $company = Company::where('companySystemID', $input['primaryCompanySystemID'])->first();

        if (empty($company)) {
            return $this->sendError('Primary Company not found');
        }

        $document = DocumentMaster::where('documentID', 'ITMM')->first();

        $financeCategoryMaster = FinanceItemCategoryMaster::where('itemCategoryID', $input['financeCategoryMaster'])->first();

        if (empty($financeCategoryMaster)) {
            return $this->sendError('Finance Item Category not found');
        }

        $runningSerialOrder = $financeCategoryMaster->lastSerialOrder;
        $code = $financeCategoryMaster->itemCodeDef;
        $count = $financeCategoryMaster->numberOfDigits;

        $createdItems = array();

        DB::beginTransaction();

        try {
            foreach ($input['items'] as $item) {

                $partNo = isset($item['secondaryItemCode']) ? $item['secondaryItemCode'] : '';

                $messages = array('secondaryItemCode.unique' => 'Part No / Ref.Number ' . $partNo . ' already exists');
                $validator = \Validator::make((array)$item, ['secondaryItemCode' => 'unique:itemmaster'], $messages);

                if ($validator->fails()) {
                    return $this->sendError($validator->messages(), 422);
                }

                $runningSerialOrder = $runningSerialOrder + 1;
                $primaryCode = $code . str_pad($runningSerialOrder, $count, '0', STR_PAD_LEFT);

                $item['runningSerialOrder'] = $runningSerialOrder;
                $item['primaryCode'] = $primaryCode;
                $item['primaryItemCode'] = $code;

                $financeCategorySub = FinanceItemCategorySub::where('itemCategorySubID', $item['financeCategorySub'])->first();

                if (empty($financeCategorySub)) {
                    return $this->sendError('Finance Item Sub Category not found');
                }

                if ($document) {
                    $item['documentSystemID'] = $document->documentSystemID;
                    $item['documentID'] = $document->documentID;
                }

                $item['trackingType'] = (is_null($financeCategorySub->trackingType)) ? 0 : $financeCategorySub->trackingType;
                $item['isActive'] = 1;
                $input['createdPcID'] = gethostname();
                $input['createdUserID'] = $employee->empID;
                $input['createdUserSystemID'] = $employee->employeeSystemID;
                $item['itemShortDescription'] = $item['itemDescription'];
                $item['primaryCompanyID'] = $company->CompanyID;
                $item['primaryCompanySystemID'] = $input['primaryCompanySystemID'];
                $item['financeCategoryMaster'] = $input['financeCategoryMaster'];

                $itemType = $item['itemType'];

                $itemMaster = $this->itemMasterRepository->create($item);

                foreach ($itemType as $key => $value) {
                    $itemMasterCategoryType = new ItemMasterCategoryType();
                    $itemMasterCategoryType->itemCodeSystem = $itemMaster->itemCodeSystem;
                    $itemMasterCategoryType->categoryTypeID = $value['id'];
                    $itemMasterCategoryType->save();
                }

                if ($input['itemConfirmedYN'] == true) {
                    $params = array('autoID' => $itemMaster->itemCodeSystem, 'company' => $item["primaryCompanySystemID"], 'document' => $item["documentSystemID"]);
                    $confirm = \Helper::confirmDocument($params);
                    if (!$confirm["success"]) {
                        return $this->sendError($confirm["message"], 500);
                    }
                }
                array_push($createdItems, $itemMaster);
            }

            $financeCategoryMaster->lastSerialOrder = $runningSerialOrder;
            $financeCategoryMaster->modifiedPc = gethostname();
            $financeCategoryMaster->modifiedUser = $empId;
            $financeCategoryMaster->save();

            DB::commit();

            return $this->sendResponse($createdItems, 'Item Master saved successfully');
        } catch (\Exception $e) {
            DB::rollBack();
        }
    }

    /**
     * Display a listing of the ItemMaster.
     * POST /getAllItemsMaster
     *
     * @param Request $request
     * @return Response
     */

    public function getAllItemsMaster(Request $request)
    {

        $input = $request->all();

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }
        $search = $request->input('search.value');

        $financeCategorySub = $request['financeCategorySub'];
        $financeCategorySub = (array)$financeCategorySub;
        $financeCategorySub = collect($financeCategorySub)->pluck('id');

        $itemMasters = ($this->getAllItemsQry($input, $search, $financeCategorySub));

        return \DataTables::eloquent($itemMasters)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('itemCodeSystem', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->addColumn('Actions', 'Actions', "Actions")
            ->make(true);
        ///return $this->sendResponse($itemMasters->toArray(), 'Item Masters retrieved successfully');*/
    }

    public function exportItemMaster(Request $request)
    {
        $input = $request->all();

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $search = $request->input('search.value');

        $financeCategorySub = $request['financeCategorySub'];
        $financeCategorySub = (array)$financeCategorySub;
        $financeCategorySub = collect($financeCategorySub)->pluck('id');

        $items = ($this->getAllItemsQry($input, $search, $financeCategorySub))->orderBy('itemCodeSystem', $sort)->get();

        $type = $request->get('type');
        if ($items) {
            $x = 0;
            foreach ($items as $val) {
                $itemTypes = [];
                foreach ($val['item_category_type'] as $type) {
                    $itemTypes[] = $type['category_type_master']['name'];
                }
                $itemTypesString = implode(', ', $itemTypes);
                $data[$x]['Item Code'] = $val['primaryCode'];
                $data[$x]['Item Type'] = $itemTypesString;
                $data[$x]['Part No / Ref.Number'] = $val['secondaryItemCode'];
                $data[$x]['Item Description'] = $val['itemDescription'];

                if ($val['unit_by']) {
                    $data[$x]['UOM'] = $val['unit_by']['UnitShortCode'];
                } else {
                    $data[$x]['UOM'] = '-';
                }

                if ($val['financeMainCategory']) {
                    $data[$x]['Category'] = $val['financeMainCategory']['categoryDescription'];
                } else {
                    $data[$x]['Category'] = '-';
                }

                if ($val['financeSubCategory']) {
                    $data[$x]['Sub Category'] = $val['financeSubCategory']['categoryDescription'];
                    $data[$x]['Gl Code'] = $val['financeSubCategory']['financeGLcodePL'];
                } else {
                    $data[$x]['Sub Category'] = '-';
                    $data[$x]['Gl Code'] = '-';
                }
                $x++;
            }
        } else {
            $data = array();
        }
        $companyMaster = Company::find(isset($input['companyId'])?$input['companyId']: null);
        $companyCode = isset($companyMaster->CompanyID)?$companyMaster->CompanyID:'common';
        $detail_array = array(
            'company_code'=>$companyCode,
        );


        $fileName = 'item_master';
        $path = 'system/item_master/excel/';
        $type = 'xls';
        $basePath = CreateExcel::process($data,$type,$fileName,$path,$detail_array);

        if($basePath == '')
        {
             return $this->sendError('Unable to export excel');
        }
        else
        {
             return $this->sendResponse($basePath, trans('custom.success_export'));
        }


        ///return $this->sendResponse($itemMasters->toArray(), 'Item Masters retrieved successfully');*/
    }

    public function getSubcategoriesByItemType(Request $request)
    {

        $itemType = $request->itemTypeID;
        $companyId = $request->primaryCompanySystemID;
        $isGroup = \Helper::checkIsCompanyGroup($companyId);

        if ($isGroup) {
            $companyID = \Helper::getGroupCompany($companyId);
        } else {
            $companyID = [$companyId];
        }

        if (is_array($itemType)) {
            if (count($itemType) != 0) {
                if (count($itemType) > 1) {
                    $subCategories = FinanceItemcategorySubAssigned::where('mainItemCategoryID', $request->financeCategoryID)
                        ->where('isActive', 1)
                        ->whereHas('finance_item_category_sub', function ($query) {
                            $query->where('isActive', 1);
                        })
                        ->whereIn('companySystemID', $companyID)
                        ->where('isAssigned', -1)
                        ->with(['finance_gl_code_bs', 'finance_gl_code_pl'])
                        ->groupBy('itemCategorySubID')
                        ->get();
                } else {
                    $subCategories = FinanceItemcategorySubAssigned::where('mainItemCategoryID', $request->financeCategoryID)
                        ->where('isActive', 1)
                        ->whereHas('finance_item_category_sub', function ($query) {
                            $query->where('isActive', 1);
                        })
                        ->whereIn('companySystemID', $companyID)
                        ->where('isAssigned', -1)
                        ->with(['finance_gl_code_bs', 'finance_gl_code_pl'])
                        ->groupBy('itemCategorySubID');

                    $itemType = collect($itemType);

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
            } else {
                $subCategories = [];
            }

            return $subCategories;
        }

    }

    public function getAllItemsQry($request, $search, $financeCategorySub)
    {

        $input = $request;
        $input = $this->convertArrayToSelectedValue($input, array('financeCategoryMaster', 'financeCategorySub', 'isActive', 'itemApprovedYN', 'itemConfirmedYN'));

        $companyId = $input['companyId'];
        $isGroup = \Helper::checkIsCompanyGroup($companyId);

        if ($isGroup) {
            $childCompanies = \Helper::getGroupCompany($companyId);
        } else {
            $childCompanies = [$companyId];
        }


        $itemMasters = ItemMaster::with(['unit', 'unit_by', 'financeMainCategory', 'financeSubCategory', 'item_category_type']);
        //->whereIn('primaryCompanySystemID',$childCompanies);

        if (array_key_exists('financeCategoryMaster', $input)) {
            if ($input['financeCategoryMaster'] > 0 && !is_null($input['financeCategoryMaster'])) {
                $itemMasters->where('financeCategoryMaster', $input['financeCategoryMaster']);
            }
        }

        if (array_key_exists('financeCategorySub', $input)) {
            if ($input['financeCategorySub'] > 0 && !is_null($input['financeCategorySub'])) {
                $itemMasters->whereIn('financeCategorySub', $financeCategorySub);
            }
        }

        if (array_key_exists('isActive', $input)) {
            if (($input['isActive'] == 0 || $input['isActive'] == 1) && !is_null($input['isActive'])) {
                $itemMasters->where('isActive', $input['isActive']);
            }
        }
        if (array_key_exists('itemApprovedYN', $input)) {
            if (($input['itemApprovedYN'] == 0 || $input['itemApprovedYN'] == 1) && !is_null($input['itemApprovedYN'])) {
                $itemMasters->where('itemApprovedYN', $input['itemApprovedYN']);
            }
        }

        if (array_key_exists('itemConfirmedYN', $input)) {
            if (($input['itemConfirmedYN'] == 0 || $input['itemConfirmedYN'] == 1) && !is_null($input['itemConfirmedYN'])) {
                $itemMasters->where('itemConfirmedYN', $input['itemConfirmedYN']);
            }
        }

        if ($search) {
            $itemMasters = $itemMasters->where(function ($query) use ($search) {
                $query->where('primaryCode', 'LIKE', "%{$search}%")
                    ->orWhere('secondaryItemCode', 'LIKE', "%{$search}%")
                    ->orWhere('itemDescription', 'LIKE', "%{$search}%");
            });
        }

        $itemType = isset($input['categoryType']) ? $input['categoryType']: null;

        if (is_array($itemType)){
            $categoryTypeID = collect($itemType)->pluck('id');
            $itemMasters = $itemMasters->whereHas('item_category_type', function ($query) use ($categoryTypeID) {
                $query->whereIn('categoryTypeID', $categoryTypeID);
            });
        }

        return $itemMasters;
    }

    /**
     * Display items from ItemMaster for approval.
     * POST /getAllItemsMasterApproval
     *
     * @param Request $request
     * @return Response
     */

    public function getAllItemsMasterApproval(Request $request)
    {
        $input = $request->all();

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $companyId = $request->selectedCompanyID;

        $isGroup = \Helper::checkIsCompanyGroup($companyId);

        if ($isGroup) {
            $companyID = \Helper::getGroupCompany($companyId);
        } else {
            $companyID = [$companyId];
        }


        $empID = \Helper::getEmployeeSystemID();
        $search = $request->input('search.value');
        $itemMasters = DB::table('erp_documentapproved')->select( 'employeesdepartments.approvalDeligated','itemmaster.*', 'erp_documentapproved.documentApprovedID', 'financeitemcategorymaster.categoryDescription as financeitemcategorydescription', 'financeitemcategorysub.categoryDescription as financeitemcategorysubdescription', 'units.UnitShortCode', 'rollLevelOrder', 'financeGLcodePL', 'approvalLevelID', 'documentSystemCode', DB::raw('GROUP_CONCAT(item_category_type_master.name SEPARATOR ", ") as category_descriptions'))->join('employeesdepartments', function ($query) use ($companyID, $empID) {
            $query->on('erp_documentapproved.approvalGroupID', '=', 'employeesdepartments.employeeGroupID')
                ->on('erp_documentapproved.documentSystemID', '=', 'employeesdepartments.documentSystemID')
                ->on('erp_documentapproved.companySystemID', '=', 'employeesdepartments.companySystemID')
                ->where('employeesdepartments.documentSystemID', 57)
                ->whereIn('employeesdepartments.companySystemID', $companyID)
                ->where('employeesdepartments.employeeSystemID', $empID)
                ->where('employeesdepartments.isActive', 1)
                ->where('employeesdepartments.removedYN', 0);
        })
            ->join('itemmaster', function ($query) use ($companyID, $empID, $search) {
                $query->on('itemCodeSystem', '=', 'documentSystemCode')
                    ->on('erp_documentapproved.rollLevelOrder', '=', 'RollLevForApp_curr')
                    ->whereIn('itemmaster.primaryCompanySystemID', $companyID)
                    ->where('itemApprovedYN', 0)
                    ->when($search != "", function ($q) use ($search) {
                        $q->where(function ($query) use ($search) {
                            $query->where('primaryCode', 'LIKE', "%{$search}%")
                                ->orWhere('secondaryItemCode', 'LIKE', "%{$search}%")
                                ->orWhere('itemDescription', 'LIKE', "%{$search}%");
                        });
                    });
            })
            ->leftJoin('item_master_category_types', 'itemmaster.itemCodeSystem', '=', 'item_master_category_types.itemCodeSystem')
            ->leftJoin('item_category_type_master', 'item_master_category_types.categoryTypeID', '=', 'item_category_type_master.id')
            ->leftJoin('units', 'UnitID', '=', 'unit')
            ->leftJoin('financeitemcategorymaster', 'itemCategoryID', '=', 'financeCategoryMaster')
            ->leftJoin('financeitemcategorysub', 'itemCategorySubID', '=', 'financeCategorySub')
            ->where('erp_documentapproved.approvedYN', 0)
            ->where('erp_documentapproved.rejectedYN', 0)
            ->where('erp_documentapproved.documentSystemID', 57)
            ->whereIn('erp_documentapproved.companySystemID', $companyID)
            ->groupBy('itemmaster.itemCodeSystem');

        $isEmployeeDischarched = \Helper::checkEmployeeDischarchedYN();

        if ($isEmployeeDischarched == 'true') {
            $itemMasters = [];
        }

        $data['order'] = [];
        $data['search']['value'] = '';
        $request->merge($data);

        if(isset($input['order'][0]['dir'])) {
            $itemMasters = $itemMasters->orderBy('documentApprovedID', $input['order'][0]['dir']);
        }

        $itemMasters = $itemMasters->get();

        return \DataTables::of($itemMasters)
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->addColumn('Actions', 'Actions', "Actions")
            ->make(true);
    }

    /**
     * get form data for Item Master.
     * GET /getItemMasterFormData
     *
     * @param Request $request
     * @return Response
     */
    public function getItemMasterFormData(Request $request)
    {

        $input = $request->all();
        $selectedCompanyId = $request['selectedCompanyId'];

        $isPosIntegratedPolicy = CompanyPolicyMaster::where('companyPolicyCategoryID', 69)
                            ->where('companySystemID', $selectedCompanyId)
                            ->first();
        if(!empty($isPosIntegratedPolicy->isYesNO)){
            $isPosIntegrated = $isPosIntegratedPolicy->isYesNO;
        } else {
            $isPosIntegrated = false;
        }

        $isSubItemEnabledPolicy = CompanyPolicyMaster::where('companyPolicyCategoryID', 72)
            ->where('companySystemID', $selectedCompanyId)
            ->first();
        if(!empty($isSubItemEnabledPolicy->isYesNO)){
            $isSubItemEnabled = $isSubItemEnabledPolicy->isYesNO;
        } else {
            $isSubItemEnabled = false;
        }


        $warehouseSystemCode = isset($input['warehouseSystemCode']) ? $input['warehouseSystemCode'] : 0;

        $warehouse = WarehouseMaster::find($warehouseSystemCode);

        if(!empty($warehouse)){
            $selectedCompanyId = $warehouse->companySystemID;
        }

        $masterCompany = Company::where("companySystemID", $selectedCompanyId)->first();


        $isGroup = \Helper::checkIsCompanyGroup($selectedCompanyId);

        if ($isGroup) {
            //$subCompanies = \Helper::getGroupCompany($selectedCompanyId);
            $subCompanies = \Helper::getSubCompaniesByGroupCompany($selectedCompanyId);
        } else {
            $subCompanies = [$selectedCompanyId];
        }

        /**  Fixed Assets  Drop Down */
        $fixedAssetCategory = FixedAssetCategory::ofCompany($subCompanies)->get();

        /**  Companies by group  Drop Down */
        $companiesByGroup = Company::whereIn("companySystemID", $subCompanies)->where("isGroup",0)->get();

        /** all Company  Drop Down */
        $allCompanies = Company::whereIn("companySystemID", $subCompanies)->get();

        /** all FinanceItemCategoryMaster Drop Down */
        $itemCategory = FinanceItemCategoryMaster::all();

        /** all FinanceItemCategorySub Drop Down */
        $itemCategorySub = FinanceItemCategorySub::with(['finance_gl_code_bs','finance_gl_code_pl'])->where('isActive',1)->get();
        $itemCategorySubArray = [];
        $i=0;
        foreach ($itemCategorySub as $value){
            $itemCategorySubArray[$i] = array_except($value,['finance_gl_code_bs','finance_gl_code_pl']);
            if($value->financeGLcodePLSystemID && $value->finance_gl_code_pl != null){
                $itemCategorySubArray[$i]['AccountCode'] = isset($value->finance_gl_code_pl->AccountCode)?$value->finance_gl_code_pl->AccountCode:'';
                $itemCategorySubArray[$i]['AccountDescription'] = isset($value->finance_gl_code_pl->AccountDescription)?$value->finance_gl_code_pl->AccountDescription:'';
            }else if($value->financeGLcodebBSSystemID && $value->finance_gl_code_bs != null){

                $itemCategorySubArray[$i]['AccountCode'] = isset($value->finance_gl_code_bs->AccountCode)?$value->finance_gl_code_bs->AccountCode:'';
                $itemCategorySubArray[$i]['AccountDescription'] = isset($value->finance_gl_code_bs->AccountDescription)?$value->finance_gl_code_bs->AccountDescription:'';

            }else{
                $itemCategorySubArray[$i]['AccountCode'] = '';
                $itemCategorySubArray[$i]['AccountDescription'] = '';
            }
            $i++;
        }
        /** Yes and No Selection */
        $yesNoSelection = YesNoSelection::all();

        /** all Units*/
        $units = Unit::all();

        $wareHouseBinLocations = [];
        if (isset($request['warehouseSystemCode'])) {
            $wareHouseBinLocations = WarehouseBinLocation::where('companySystemID', $selectedCompanyId)
                ->where('wareHouseSystemCode', $request['warehouseSystemCode'])
                ->get();
        }

        $id = isset($input['id'])?$input['id']:0;
        $isVatRegisteredYN = false;
        $vatSubCategory = [];
        if($id > 0){
            $itemMaster = ItemMaster::find($id);
            if($itemMaster){
                $primaryCompany = Company::find($itemMaster->primaryCompanySystemID);
                if($primaryCompany && $primaryCompany->vatRegisteredYN == 1){
                    $isVatRegisteredYN = true;
                    $vatSubCategory = TaxVatCategories::selectRaw('taxVatSubCategoriesAutoID,CONCAT(erp_tax_vat_main_categories.mainCategoryDescription, " - " ,erp_tax_vat_sub_categories.subCategoryDescription) as label')
                        ->join('erp_tax_vat_main_categories','erp_tax_vat_sub_categories.mainCategory','=','erp_tax_vat_main_categories.taxVatMainCategoriesAutoID')
                        ->where('erp_tax_vat_sub_categories.isActive',1)
                        ->groupBy('taxVatSubCategoriesAutoID')
                        ->get();
                }
            }
        }

        $assetFinanceCategory = AssetFinanceCategory::all();

        $categoryTypeData = ItemCategoryTypeMaster::all();

        $output = array('companiesByGroup' => $companiesByGroup,
            'fixedAssetCategory' => $fixedAssetCategory,
            'allCompanies' => $allCompanies,
            'financeItemCategoryMaster' => $itemCategory,
            'assetFinanceCategory' => $assetFinanceCategory,
            'financeItemCategorySub' => $itemCategorySubArray,
            'yesNoSelection' => $yesNoSelection,
            'units' => $units,
            'isVatRegisteredYN' => $isVatRegisteredYN,
            'wareHouseBinLocations' => $wareHouseBinLocations,
            'vatSubCategory' => $vatSubCategory,
            'masterCompany' => $masterCompany,
            'isPosIntegrated' => $isPosIntegrated,
            'isSubItemEnabled' => $isSubItemEnabled,
            'categoryTypeData' => $categoryTypeData
        );

        return $this->sendResponse($output, 'Record retrieved successfully');

    }

    /**
     * Store a newly created ItemMaster in storage.
     * POST /itemMasters
     *
     * @param CreateItemMasterAPIRequest $request
     *
     * @return Response
     */






    private function storeImage($imageData, $picName, $picBasePath,$disk)
    {
        if (preg_match('/^data:image\/(\w+);base64,/', $imageData, $type)) {
            $imageData = substr($imageData, strpos($imageData, ',') + 1);
            $type = strtolower($type[1]); 

            if (!in_array($type, ['jpg', 'jpeg', 'gif', 'png'])) {
                throw new Exception('invalid image type');
            }

            $imageData = base64_decode($imageData);

            if ($imageData === false) {
                throw new Exception('image decode failed');
            }

            $picNameExtension = "{$picName}.{$type}";
            $picFullPath = $picBasePath . $picNameExtension;
            Storage::disk($disk)->put($picFullPath, $imageData);
        } else if (preg_match('/^https/', $imageData)) {
            $imageData = basename($imageData);
            $picFullPath = $picBasePath;
        } else {
            throw new Exception('did not match data URI with image data');
        }

        return $picFullPath;
    }

    public static function quickRandom($length = 6)
    {
        $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    
        return substr(str_shuffle(str_repeat($pool, 2)), 0, $length);
    }

    public function store(CreateItemMasterAPIRequest $request)
    {
        $input = $request->all();

        $imageData = (array)($input['images']);

        if(isset($input['categoryType'])) {
            $categoryTypes = $input['categoryType'];
            unset($input['categoryType']);
        }
        else {
            $categoryTypes = '';
        }

        $input = $this->convertArrayToValue($input);

        $financeCategorySubID = $input['financeCategorySub'];
        $itemCategorySubExpirystatus = FinanceItemcategorySub::select('expiryYN')
                                        ->where('itemCategorySubID', $financeCategorySubID)->first();

        // $input['expiryYN'] = $itemCategorySubExpirystatus->expiryYN;

        $partNo = isset($input['secondaryItemCode']) ? $input['secondaryItemCode'] : '';
        $input['isPOSItem'] = isset($input['isPOSItem']) ? $input['isPOSItem'] : 0;

        $validatorResult = \Helper::checkCompanyForMasters($input['primaryCompanySystemID']);
        if (!$validatorResult['success']) {
            return $this->sendError($validatorResult['message']);
        }

        $messages = array('secondaryItemCode.unique' => 'Part No / Ref.Number ' . $partNo . ' already exists');
        $ruleArray = array(
            'primaryCompanySystemID' => 'required|numeric|min:1',
            'itemDescription' => 'required',
            'unit' => 'required|numeric|min:1',
            'financeCategoryMaster' => 'required|numeric|min:1',
            'financeCategorySub' => 'required|numeric|min:1',
            'isActive' => 'required|numeric|min:1',
        );
        if ($input['isPOSItem'] == 1) {
            $ruleArray = array_merge($ruleArray, ['sellingCost' => 'required|numeric|min:0.001']);
        }

        $validator = \Validator::make($input, $ruleArray, $messages);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        if (isset($input['financeCategoryMaster']) && $input['financeCategoryMaster'] == 3 && (!isset($input['faFinanceCatID']) || (isset($input['faFinanceCatID']) && is_null($input['faFinanceCatID'])))) {
            return $this->sendError('Finance Audit category is required.');
        }

        $employee = Helper::getEmployeeInfo();
        $input['createdPcID'] = gethostname();
        $input['createdUserID'] = $employee->empID;
        $input['createdUserSystemID'] = $employee->employeeSystemID;

        $financeCategoryMaster = FinanceItemCategoryMaster::where('itemCategoryID', $input['financeCategoryMaster'])->first();

        if (empty($financeCategoryMaster)) {
            return $this->sendError('Finance category not found');
        }

        $runningSerialOrder = $financeCategoryMaster->lastSerialOrder + 1;
        $code = $financeCategoryMaster->itemCodeDef;
        $count = $financeCategoryMaster->numberOfDigits;
        $primaryCode = $code . str_pad($runningSerialOrder, $count, '0', STR_PAD_LEFT);
        $input['runningSerialOrder'] = $runningSerialOrder;
        $input['primaryCode'] = $primaryCode;
        $input['primaryItemCode'] = $code;

        if(!(isset($input['barcode']) && $input['barcode'] != null)){
            $input['barcode'] = $primaryCode;
        }

        if (isset($input['financeCategoryMaster']) && $input['financeCategoryMaster'] != 3) {
            $input['faFinanceCatID'] = null;
        }

        $financeCategorySub = FinanceItemCategorySub::where('itemCategorySubID', $input['financeCategorySub'])->first();

        $company = Company::where('companySystemID', $input['primaryCompanySystemID'])->first();

        if (empty($company)) {
            return $this->sendError('Company not found');
        }

        $input['primaryCompanyID'] = $company->CompanyID;
        $document = DocumentMaster::where('documentID', 'ITMM')->first();
        $input['documentSystemID'] = $document->documentSystemID;
        $input['documentID'] = $document->documentID;
        $input['isActive'] = 1;

        if ($input['isPOSItem'] == 1) {
            $input['itemConfirmedYN'] = 1;
            $input['itemConfirmedByEMPSystemID'] = $employee->employeeSystemID;
            $input['itemConfirmedByEMPID'] = $employee->empID;
            $input['itemConfirmedByEMPName'] = $employee->empName;
            $input['itemConfirmedDate'] = now();

            $input['itemApprovedBySystemID'] = $employee->employeeSystemID;
            $input['itemApprovedBy'] = $employee->empID;
            $input['itemApprovedYN'] = 1;
            $input['itemApprovedDate'] = now();
            $input['itemApprovedComment'] = '';
        }

        $itemMasters = $this->itemMasterRepository->create($input);

        foreach ($categoryTypes as $categoryType) {
            $itemCategoryType = new ItemMasterCategoryType();
            $itemCategoryType->itemCodeSystem = $itemMasters['itemCodeSystem'];
            $itemCategoryType->categoryTypeID = $categoryType['id'];
            $itemCategoryType->save();
        }

        $count = 0;
        $image_path = [];

        $disk = Helper::policyWiseDisk($input['primaryCompanySystemID'], 'public');

        foreach($imageData as $key=>$val)
        {
            $path_dir['path'] = '';
            $t=time();
            $tem = substr($t,5);
            $valtt = $this->quickRandom();
            $random_words = $itemMasters['itemCodeSystem'].'_'.$valtt.'_'.$tem;
            //$base_path = 'item/'.$itemMasters['itemCodeSystem'].'/'.$count.'/';
            if (Helper::checkPolicy($input['primaryCompanySystemID'], 50)) {
                $base_path = $input['primaryCompanySystemID'].'/G_ERP/item-master/images/'.$itemMasters['itemCodeSystem'] . '/';
            }
            else
            {
                $base_path = 'item-master/images/'.$itemMasters['itemCodeSystem'] . '/';
            }

            $path_dir['path'] = $this->storeImage($val, $random_words, $base_path,$disk);
            array_push($image_path,$path_dir);
        }

        if($imageData == null || empty($imageData))
        {
            $pic['pic'] = null;
        }
        else
        {
            $pic['pic'] = json_encode($image_path);
        }
        
        $this->itemMasterRepository->update(['itemPicture' => $pic['pic']], $itemMasters['itemCodeSystem']);

        $financeCategoryMaster->lastSerialOrder = $runningSerialOrder;
        $financeCategoryMaster->modifiedPc = gethostname();
        $financeCategoryMaster->modifiedUser = $employee->empID;
        $financeCategoryMaster->save();

        if ($input['isPOSItem'] == 1) {
            $itemMaster = DB::table('itemmaster')
                ->selectRaw('itemCodeSystem,primaryCode as itemPrimaryCode,secondaryItemCode,barcode,itemDescription,unit as itemUnitOfMeasure,itemUrl,primaryCompanySystemID as companySystemID,primaryCompanyID as companyID,financeCategoryMaster,financeCategorySub, -1 as isAssigned,companymaster.localCurrencyID as wacValueLocalCurrencyID,companymaster.reportingCurrency as wacValueReportingCurrencyID,NOW() as timeStamp,isPOSItem, faFinanceCatID')
                ->join('companymaster', 'companySystemID', '=', 'primaryCompanySystemID')
                ->where('itemCodeSystem', $itemMasters->itemCodeSystem)
                ->first();
            if (!empty($itemMaster)) {
                $itemMaster = collect($itemMaster)->toArray();
                $itemMaster['sellingCost'] = $input['sellingCost'];

                if (isset($input['rolQuantity'])) {
                    $itemMaster['rolQuantity'] = $input['rolQuantity'];
                }

                if (isset($input['maximunQty'])) {
                    $itemMaster['maximunQty'] = $input['maximunQty'];
                }

                if (isset($input['rolQuantity'])) {
                    $itemMaster['minimumQty'] = $input['minimumQty'];
                }

                $itemAssign = ItemAssigned::insert($itemMaster);
            }
        }

        return $this->sendResponse($itemMasters->toArray(), 'Item Master saved successfully');
    }

    /**
     * Update the specified ItemMaster in storage.
     * PUT/PATCH /updateItemMaster
     *
     * @param Request $request
     *
     * @return Response
     */


    public function updateItemMaster(Request $request)
    {
        $input = $request->all();

        $id = $input['itemCodeSystem'];
        $imageData = $input['item_path'];
        $remove_items = $input['remove_items'];
        $categoryType = $input['categoryType'];

        unset($input['item_path']);
        unset($input['specification']);
        unset($input['remove_items']);

        if(isset($input['categoryType']) && empty($input['categoryType'])){
            return $this->sendError('Please select Item Type');
        }
        unset($input['categoryType']);

        $input = array_except($input,['finance_sub_category','company','specification','final_approved_by']);

        $employee = Helper::getEmployeeInfo();
        $input['modifiedPc'] = gethostname();
        $input['modifiedUser'] = $employee->empID;
        $input['modifiedUserSystemID'] = $employee->employeeSystemID;

        unset($input['final_approved_by']);
        $itemMaster = ItemMaster::with('item_category_type')->where("itemCodeSystem", $id)->first();
        $pic_item = $itemMaster->itemPicture;
  
        if (empty($itemMaster)) {
            return $this->sendError('Item Master not found');
        }

        foreach ($input as $key => $value) {
            if (is_array($input[$key])) {
                if (count($input[$key]) > 0) {
                    $input[$key] = $input[$key][0];
                } else {
                    $input[$key] = 0;
                }
            }
        }

        if(isset($input['financeCategorySub']) && empty($input['financeCategorySub'])){
            return $this->sendError('Please select Finance Sub Category');
        }

        if (isset($input['financeCategoryMaster']) && $input['financeCategoryMaster'] == 3 && (!isset($input['faFinanceCatID']) || (isset($input['faFinanceCatID']) && is_null($input['faFinanceCatID'])))) {
            return $this->sendError('Finance Audit category is required.');
        }

        if (isset($input['financeCategoryMaster']) && $input['financeCategoryMaster'] != 3) {
            $input['faFinanceCatID'] = null;
        }

        if (isset($input['isSubItem']) && $input['isSubItem'] == 1) {
            $mainItemID = isset($input['mainItemID'][0]) ? $input['mainItemID'][0] : $input['mainItemID'];
            if (!$mainItemID) {
                return $this->sendError('Main Item field is required.');
            }
        }
             
        $disk = Helper::policyWiseDisk($input['primaryCompanySystemID'], 'public');
 
        $count = 0;
        $image_path = [];
        $path_dir['path'] = '';
       
        if($remove_items != null || !empty($remove_items))
        {
            foreach($remove_items as $key=>$val)
            { 
                $re = Storage::disk($disk)->delete($val['db_path']);
             
            }
        }
      
        if($imageData != null || !empty($imageData))
        {
            foreach($imageData as $key=>$val)
            {
                $path_dir['path'] = '';
                 if (preg_match('/^https/', $val['path']))
                 {
                    $path_dir['path'] = $val['db_path'];
                 }
                 else
                 {
                    $t=time();
                    $tem = substr($t,5);
                    $valtt = $this->quickRandom();
                    $random_words = $id.'_'.$valtt.'_'.$tem;
                    if (Helper::checkPolicy($input['primaryCompanySystemID'], 50)) {
                        $base_path = $input['primaryCompanySystemID'].'/G_ERP/item-master/images/'.$id . '/';
                    }   
                    else
                    {
                        $base_path = 'item-master/images/'.$id . '/';
                    }
                    $path_dir['path'] = $this->storeImage($val['path'], $random_words, $base_path,$disk);
                 }
  
                array_push($image_path,$path_dir);                   
            }

            $itemMaster->itemPicture = json_encode($image_path);
        }

        $previosValue = $itemMaster->toArray();
        $newValue = $input;

        $uuid = isset($input['tenant_uuid']) ? $input['tenant_uuid'] : 'local';
        $db = isset($input['db']) ? $input['db'] : '';

        if(isset($input['tenant_uuid']) ){
            unset($input['tenant_uuid']);
        }

        if(isset($input['db']) ){
            unset($input['db']);
        }

        if($itemMaster->itemApprovedYN == 1){
            //check policy 9
            $policy = Helper::checkRestrictionByPolicy($input['primaryCompanySystemID'],9);
            if($policy){

                $input['itemPicture'] = $itemMaster->itemPicture;
                $itemMaster->itemUrl = $input['itemUrl'];
                $itemMaster->isActive = $input['isActive'];
                $itemMaster->isSubItem = $input['isSubItem'];
                $itemMaster->mainItemID = $input['mainItemID'];
                $itemMaster->itemPicture = $input['itemPicture'];
                $itemMaster->pos_type = $input['pos_type'];
                $itemMaster->itemDescription = $input['itemDescription'];
                $itemMaster->itemShortDescription = $input['itemShortDescription'];
                $itemMaster->financeCategorySub = $input['financeCategorySub'];
                $itemMaster->unit = $input['unit'];
                $itemMaster->barcode = $input['barcode'];
                $itemMaster->secondaryItemCode = $input['secondaryItemCode'];

                $itemMaster->save();

                ItemMasterCategoryType::where('itemCodeSystem', $id)->delete();

                foreach ($categoryType as $key => $value) {
                    $itemMasterCategoryType = new ItemMasterCategoryType();
                    $itemMasterCategoryType->itemCodeSystem = $id;
                    $itemMasterCategoryType->categoryTypeID = $value['id'];
                    $itemMasterCategoryType->save();
                }

                $this->auditLog($db, $input['itemCodeSystem'],$uuid, "itemmaster", $newValue['primaryCode']." has updated", "U", $newValue, $previosValue);
           
                $updateData = [
                    'itemUrl' => $input['itemUrl'],
                    'isActive' => $input['isActive'],
                    'pos_type' => $input['pos_type'],
                    'itemDescription' => $input['itemDescription'],
                    'financeCategorySub' => $input['financeCategorySub'],
                    'itemUnitOfMeasure' => $input['unit'],
                    'barcode' => $input['barcode'],
                    'secondaryItemCode' => $input['secondaryItemCode']
                ];

                $itemMasterOld = $itemMaster->toArray();
                ItemAssigned::where('itemCodeSystem', $id)->update($updateData);
                $old_array = array_only($itemMasterOld,['itemUrl', 'isActive', 'itemPicture','pos_type']);
                $modified_array = array_only($input,['itemUrl', 'isActive', 'itemPicture','pos_type']);
            
                // update in to user log table
                foreach ($old_array as $key => $old){
                    if($old != $modified_array[$key]){
                        $description = $employee->empName." Updated item master (".$itemMaster->itemCodeSystem.") from ".$old." To ".$modified_array[$key]."";
                        UserActivityLogger::createUserActivityLogArray($employee->employeeSystemID,$itemMaster->documentSystemID,$itemMaster->primaryCompanySystemID,$itemMaster->itemCodeSystem,$description,$modified_array[$key],$old,$key);
                    }
                }

                return $this->sendResponse([], 'Item Master updated successfully');
            }

            return $this->sendError('Item Master already approved. You cannot edit');
        }
    
        $company = Company::where('companySystemID', $input['primaryCompanySystemID'])->first();

        if ($company) {
            $input['primaryCompanyID'] = $company->CompanyID;
        }

        if ($itemMaster->itemConfirmedYN == 0 && $input['itemConfirmedYN'] == 1) {

            $validator = \Validator::make($input, [
                'primaryCompanySystemID' => 'required|numeric|min:1',
                'financeCategoryMaster' => 'required|numeric|min:1',
                'financeCategorySub' => 'required|numeric|min:1',
                'unit' => 'required|numeric|min:1'
            ]);

            if ($validator->fails()) {
                return $this->sendError($validator->messages(), 422);
            }

            $checkSubCategory = FinanceItemcategorySubAssigned::where('mainItemCategoryID', $input['financeCategoryMaster'])
                ->where('itemCategorySubID', $input['financeCategorySub'])
                ->where('companySystemID', $input['primaryCompanySystemID'])
                ->first();

            if (empty($checkSubCategory)) {
                return $this->sendError('The Finance Sub Category field is required.', 500);
            }

            $params = array('autoID' => $id, 'company' => $input["primaryCompanySystemID"], 'document' => $input["documentSystemID"]);
            $confirm = \Helper::confirmDocument($params);
            if (!$confirm["success"]) {
                return $this->sendError($confirm["message"], 500);
            }
        }
        
        if($itemMaster->itemConfirmedYN == 1){
            $checkSubCategory = FinanceItemcategorySubAssigned::where('mainItemCategoryID', $itemMaster->financeCategoryMaster)
                ->where('itemCategorySubID', $input['financeCategorySub'])
                ->where('companySystemID', $input['primaryCompanySystemID'])
                ->first();

            if (empty($checkSubCategory)) {
                return $this->sendError('The Finance Sub Category field is required.', 500);
            }
        }

        unset($input['item_category_type']);
        
        $afterConfirm = array('secondaryItemCode', 'barcode', 'itemDescription', 'itemShortDescription', 'itemUrl', 'unit', 'itemPicture', 'isActive', 'itemConfirmedYN', 'modifiedPc', 'modifiedUser','financeCategorySub','modifiedUserSystemID','faFinanceCatID','pos_type','isSubItem','mainItemID');
                       
        foreach ($input as $key => $value) {
            if ($itemMaster->itemConfirmedYN == 1) {
                if(in_array($key,$afterConfirm)){
                    $itemMaster->$key = $value;
                }
            }else{
                if($key != 'itemPicture')
                {
                    $itemMaster->$key = $value;
                }
               
            }
        }
    
        $itemMaster->save();

        ItemMasterCategoryType::where('itemCodeSystem', $id)->delete();

        foreach ($categoryType as $key => $value) {
            $itemMasterCategoryType = new ItemMasterCategoryType();
            $itemMasterCategoryType->itemCodeSystem = $id;
            $itemMasterCategoryType->categoryTypeID = $value['id'];
            $itemMasterCategoryType->save();
        }

        return $this->sendResponse($itemMaster->refresh()->toArray(), 'Itemmaster updated successfully d');
    }

    /**
     * Display all assigned itemAssigned for specific Item Master.
     * GET|HEAD /getAssignedCompaniesByItem}
     *
     * @param  int itemCodeSystem
     *
     * @return Response
     */

    public function categoryType(Request $request){

        $id = $request->id;
        $data = array();
        $categoryTypes = ItemMaster::selectRaw('financeitemcategorysub.categoryType')
            ->join('financeitemcategorysub', 'itemmaster.financeCategorySub', '=', 'financeitemcategorysub.itemCategorySubID')
            ->where('itemmaster.itemCodeSystem', $id)
            ->first();

        return $this->sendResponse(json_decode($categoryTypes->categoryType), 'Category types retrieved successfully');
    }
    public function getAssignedCompaniesByItem(Request $request)
    {

        $itemId = $request['itemCodeSystem'];

        $selectedCompanyId = $request['selectedCompanyId'];
        $isGroup = \Helper::checkIsCompanyGroup($selectedCompanyId);

        if($isGroup){
            $subCompanies = \Helper::getGroupCompany($selectedCompanyId);
        }else{
            $subCompanies = [$selectedCompanyId];
        }

        $item = ItemMaster::where('itemCodeSystem', '=', $itemId)->first();

        if ($item) {
            $itemCompanies = ItemAssigned::where('itemCodeSystem', $itemId)->with(['company'])
                ->whereIn("companySystemID",$subCompanies)
                ->orderBy('idItemAssigned', 'DESC')
                ->get();
        } else {
            $itemCompanies = [];
        }

        return $this->sendResponse($itemCompanies, 'Companies retrieved successfully');

    }


    public function checkUnitConversions(Request $request){

        $mainItemID = $request->mainItemID;
        $unitID = $request->unitID;
        $unitID = isset($unitID[0]) ? $unitID[0] : $unitID;

        $isConversion = 1;

            $mainItem = ItemMaster::where('itemCodeSystem', $mainItemID)->first();
            if($mainItem){
                $mainItemUnitID = $mainItem->unit;

                $conversion = UnitConversion::where('masterUnitID', $mainItemUnitID)->where('subUnitID', $unitID)->first();
                if($conversion){
                    $isConversion = 1;
                }else{
                    $isConversion = 0;
                }
            } else {
                return $this->sendError('Item not found');
            }
        $subItemUnit = Unit::find($unitID);
        $mainItemUnit = Unit::find($mainItemUnitID);


        $output = array('isConversion' => $isConversion, 'subItemUnit'=>$subItemUnit, 'mainItemUnit' => $mainItemUnit);

        return $this->sendResponse($output, 'pre check unit conversion retrieved successfully');

    }

    public function updateUnitConversion(Request $request)
    {
        $input = $request->all();

        $validator = \Validator::make($input, [
            'conversion' => 'required | numeric'
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422 );
        }

        $unitConversions = $this->unitConversionRepository->create($input);

        return $this->sendResponse($unitConversions->toArray(), 'Unit Conversion saved successfully');
    }

        /**
     * Display the specified ItemMaster.
     * GET|HEAD /itemMasters/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var ItemMaster $itemMaster */
        //$itemMaster = $this->itemMasterRepository->findWithoutFail($id);
        $itemMaster = ItemMaster::where("itemCodeSystem", $id)->with(['company','specification','finalApprovedBy','item_category_type','financeSubCategory'=> function($q){
            $q->with(['finance_gl_code_bs','finance_gl_code_pl','finance_gl_code_revenue']);
        }])->first();

      
        $image_data = $itemMaster->itemPicture;
        $storagePath  = Storage::disk('s3')->getDriver()->getAdapter()->getPathPrefix();


    
        if($image_data != null || !empty($image_data))
        {
         
            $decode_images = json_decode($image_data);
         
            
         
            $itemMaster->itemPicture = null;
            $data = [];
            if(isset($decode_images) && !empty($decode_images))
            {
                foreach($decode_images as $decode_image)
                {
                    $baseimg = '';
    
    
                
                    if (Storage::disk('s3')->exists($decode_image->path))
                    {
                        // $type = pathinfo($path, PATHINFO_EXTENSION);
                        // $data_info = file_get_contents($path);
                    
                        // $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data_info);
                        $baseimg = \Helper::getFileUrlFromS3($decode_image->path);
    
                        $info['flag'] = true;
                        $info['path'] = $baseimg;
                        $info['db_path'] = $decode_image->path;
    
                        array_push($data,$info);
                    }
                
                }
            }

      
                if(count($data) == 0)
                {
                    $itemMaster['item_path'] = null;
                }
                else
                {
                    $itemMaster['item_path'] = collect($data);
                }
              
        }
        else
        {
            $itemMaster['item_path'] = null;
        }


        if (empty($itemMaster)) {
            return $this->sendError('Item Master not found');
        }

        return $this->sendResponse($itemMaster, 'Item Master retrieved successfully');
    }

    /**
     * Update the specified ItemMaster in storage.
     * PUT/PATCH /itemMasters/{id}
     *
     * @param  int $id
     * @param UpdateItemMasterAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateItemMasterAPIRequest $request)
    {
        $input = $request->all();
        

        /** @var ItemMaster $itemMaster */
        $itemMaster = $this->itemMasterRepository->findWithoutFail($id);

        if (empty($itemMaster)) {
            return $this->sendError('Item Master not found');
        }

        $itemMaster = $this->itemMasterRepository->update($input, $id);

        return $this->sendResponse($itemMaster->toArray(), 'ItemMaster updated successfully');
    }

    /**
     * Remove the specified ItemMaster from storage.
     * DELETE /itemMasters/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var ItemMaster $itemMaster */
        $itemMaster = $this->itemMasterRepository->findWithoutFail($id);

        if (empty($itemMaster)) {
            return $this->sendError('Item Master not found');
        }

        $itemMaster->delete();

        return $this->sendResponse($id, 'Item Master deleted successfully');
    }

    public function getAllMainItemsByCompany(Request $request){

        $selectedCompanyId = $request->selectedCompanyId;
        $itemCodeSystem = $request->itemCodeSystem;

        $isSubItem = ItemMaster::selectRaw('primaryCode, itemmaster.itemDescription')->where('mainItemID', $itemCodeSystem)->first();

        $subItems = ItemMaster::selectRaw('primaryCode, isActive, refferedBackYN, itemConfirmedYN, itemApprovedYN,  itemmaster.itemDescription, units.UnitShortCode, units.unitID, SUM(IFNULL(erp_itemledger.inOutQty,0)) as availableQty, itemCodeSystem')->where('mainItemID', $itemCodeSystem)->leftJoin('units', 'UnitID', '=', 'unit')->leftJoin('erp_itemledger', 'itemCodeSystem', '=', 'itemSystemCode')->groupBy('itemCodeSystem')->get();


        $mainItemUOM =  ItemMaster::selectRaw('units.UnitShortCode, units.unitID, SUM(IFNULL(erp_itemledger.inOutQty,0)) as availableQty')->where('itemCodeSystem', $itemCodeSystem)->join('units', 'UnitID', '=', 'unit')->leftjoin('erp_itemledger', 'itemCodeSystem', '=', 'itemSystemCode')->groupBy('itemSystemCode')->first();

        foreach ($subItems as $subItem){
            $subItem->conversion = null;
            $conversion = UnitConversion::where('masterUnitID', $mainItemUOM->unitID)->where('subUnitID', $subItem->unitID)->first();
            if($conversion){
                $subItem->conversion = $conversion->conversion;

            }
        }

        $mainItems = ItemAssigned::selectRaw('CONCAT(itemassigned.itemPrimaryCode, " - " ,itemassigned.itemDescription, " - ", units.UnitShortCode) as itemCode, itemassigned.itemCodeSystem')->join('itemmaster', 'itemmaster.itemCodeSystem', '=', 'itemassigned.itemCodeSystem')->join('units', 'UnitID', '=', 'unit')->where('companySystemID', $selectedCompanyId)->where('itemmaster.isSubItem', 0)->where('itemassigned.isActive', 1)->where('itemassigned.isAssigned', -1)->get();

        $output = array('subItems' => $subItems, 'isSubItem'=>$isSubItem, 'mainItemUOM' => $mainItemUOM, 'mainItems' => $mainItems);

        return $this->sendResponse($output, 'Main item details retrieved successfully');

    }


    public function approveItem(Request $request)
    {
        $approve = \Helper::approveDocument($request);
        if (!$approve["success"]) {
            return $this->sendError($approve["message"]);
        } else {
            return $this->sendResponse(array(), $approve["message"]);
        }

    }

    public function rejectItem(Request $request)
    {
        $reject = \Helper::rejectDocument($request);
        if (!$reject["success"]) {
            return $this->sendError($reject["message"]);
        } else {
            return $this->sendResponse(array(), $reject["message"]);
        }

    }

    /**
     * Display the specified Item Master Audit.
     * GET|HEAD /getItemMasterAudit
     *
     * @param  int $id
     *
     * @return Response
     */
    public function getItemMasterAudit(Request $request)
    {
        $id = $request->get('id');

        $itemMaster = $this->itemMasterRepository
            ->with(['created_by', 'confirmed_by', 'modified_by', 'approved_by' => function ($query) {
                $query->with('employee')
                    ->where('documentSystemID', 57);
            }])
            ->findWithoutFail($id);

        if (empty($itemMaster)) {
            return $this->sendError('Item Master not found');
        }

        return $this->sendResponse($itemMaster->toArray(), 'Item Master retrieved successfully');
    }

    public function getAllFixedAssetItems(Request $request)
    {
        $input = $request->all();
        $companyId = $input['companyID'];

        $items = ItemAssigned::where('companySystemID', $companyId)
            ->where('financeCategoryMaster', 3)
            ->where('isActive', 1)
            ->select(['itemPrimaryCode', 'itemDescription', 'idItemAssigned', 'secondaryItemCode', 'itemCodeSystem']);

        if (array_key_exists('search', $input)) {
            $search = $input['search'];
            $items = $items->where(function ($query) use ($search) {
                $query->where('itemPrimaryCode', 'LIKE', "%{$search}%")
                    ->orWhere('itemDescription', 'LIKE', "%{$search}%")
                    ->orWhere('secondaryItemCode', 'LIKE', "%{$search}%");
            });
        }

        $items = $items->take(20)->get();
        return $this->sendResponse($items->toArray(), 'Data retrieved successfully');

    }

    public function itemReferBack(Request $request)
    {
        $input = $request->all();

        $id = $input['id'];

        $item = $this->itemMasterRepository->find($id);
        if (empty($item)) {
            return $this->sendError('Item Master not found');
        }

        if ($item->refferedBackYN != -1) {
            return $this->sendError('You cannot refer back this item');
        }

        $itemArray = $item->toArray();

        $storeHistory = ItemMasterRefferedBack::insert($itemArray);

        $fetchDocumentApproved = DocumentApproved::where('documentSystemCode', $id)
            ->where('companySystemID', $item->primaryCompanySystemID)
            ->where('documentSystemID', $item->documentSystemID)
            ->get();

        if (!empty($fetchDocumentApproved)) {
            foreach ($fetchDocumentApproved as $DocumentApproved) {
                $DocumentApproved['refTimes'] = $item->timesReferred;
            }
        }

        $documentApprovedArray = $fetchDocumentApproved->toArray();

        $storeDocumentRefereedHistory = DocumentReferedHistory::insert($documentApprovedArray);

        $deleteApproval = DocumentApproved::where('documentSystemCode', $id)
            ->where('companySystemID', $item->primaryCompanySystemID)
            ->where('documentSystemID', $item->documentSystemID)
            ->delete();

        if ($deleteApproval) {
            $updateArray = ['refferedBackYN' => 0, 'itemConfirmedYN' => 0, 'itemConfirmedByEMPSystemID' => null,
                'itemConfirmedByEMPID' => null, 'itemConfirmedByEMPName' => null, 'itemConfirmedDate' => null, 'RollLevForApp_curr' => 1];

            $this->itemMasterRepository->update($updateArray, $id);
        }

        return $this->sendResponse($item->toArray(), 'Item Master Amend successfully');
    }


    public function getPosItemSearch(Request $request)
    {
        $input = $request->all();
        $input['warehouseSystemCode'] = isset($input['warehouseSystemCode']) ? $input['warehouseSystemCode'] : 0;
        $companyId = isset($input['companyId']) ? $input['companyId'] : 0;
        $items = ItemAssigned::where('companySystemID', $companyId)
            ->where('financeCategoryMaster', 1)
            ->where('isPOSItem', 1)
            ->with(['unit', 'outlet_items' => function ($q) use ($input) {
                $q->where('warehouseSystemCode', $input['warehouseSystemCode']);
            }, 'item_ledger' => function ($q) use ($input) {
                $q->where('warehouseSystemCode', $input['warehouseSystemCode'])
                    ->groupBy('itemSystemCode')
                    ->selectRaw('sum(inOutQty) AS stock,itemSystemCode');
            }])
            ->whereHas('outlet_items', function ($q) use ($input) {
                $q->where('warehouseSystemCode', $input['warehouseSystemCode']);
            })
            ->whereHas('item_ledger', function ($q) use ($input) {
                $q->where('warehouseSystemCode', $input['warehouseSystemCode'])
                    ->groupBy('itemSystemCode')
                    ->havingRaw('sum(inOutQty) > 0 ');
            })
            ->select(['itemPrimaryCode', 'itemDescription', 'itemCodeSystem', 'idItemAssigned', 'secondaryItemCode', 'itemUnitOfMeasure', 'sellingCost', 'barcode']);

        if (array_key_exists('search', $input)) {
            $search = $input['search'];
            $items = $items->where(function ($query) use ($search) {
                $query->where('itemPrimaryCode', 'LIKE', "%{$search}%")
                    ->orWhere('itemDescription', 'LIKE', "%{$search}%")
                    ->orWhere('secondaryItemCode', 'LIKE', "%{$search}%");
            });
        }

        $items = $items->take(10)->get();

        foreach ($items as $item) {
            if (count($item['item_ledger']) > 0) {
                $item['current_stock'] = $item['item_ledger'][0]['stock'];
            }
        }

        return $this->sendResponse($items->toArray(), 'Data retrieved successfully');
    }

    public function checkLedgerQty(Request $request)
    {
        $wareHouseSystemCode = $request->wareHouseSystemCode;
        $itemSystemCode = $request->itemSystemCode;
        $companySystemID = $request->companySystemID;
        $sumArray = array();
        foreach ($itemSystemCode as $item) {
            $sumWarehouse = null;
            if ($wareHouseSystemCode != null) {
                $sumWarehouse = ErpItemLedger::where('wareHouseSystemCode', $wareHouseSystemCode)->where('itemSystemCode', $item)->where('companySystemID', $companySystemID)->sum('inOutQty');
            }
            $sumGlobal = ErpItemLedger::where('itemSystemCode', $item)->where('companySystemID', $companySystemID)->sum('inOutQty');

            $sum = array("itemCode" => $item, "sumWarehouse" => $sumWarehouse, "sumGlobal" => $sumGlobal);
            array_push($sumArray, $sum);
        }

        return $this->sendResponse($sumArray, 'Data retrieved successfully');
    }

    public function getSupplierByCatalogItemDetail(Request $request) {

        $input = $request->all();
        $itemCode = isset($input['itemCodeSystem'])?$input['itemCodeSystem']:0;

        $supplierCatalog = SupplierCatalogMaster::with(['supplier','company','data'=> function($query) use($itemCode){
                                                    $query->with(['uom_default','local_currency'])
                                                        ->where('itemCodeSystem',$itemCode);
                                                }])
                                                ->whereHas('details', function ($query) use($itemCode){
                                                    $query->where('itemCodeSystem',$itemCode)
                                                        ->where(function ($q){
                                                            $q->whereNull('isDeleted')
                                                                ->orWhere('isDeleted',0);
                                                        });
                                                })
                                                ->paginate(15);
        return $this->sendResponse($supplierCatalog, 'Data retrieved successfully');

//        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
//            $sort = 'asc';
//        } else {
//            $sort = 'desc';
//        }
        //        $search = $request->input('search.value');
//        if ($search) {
//            $search = str_replace("\\", "\\\\", $search);
//            $supplierCatalog = $supplierCatalog->where(function ($query) use ($search) {
//                $query->where('catalogID', 'LIKE', "%{$search}%")
//                    ->orWhere('catalogName', 'LIKE', "%{$search}%")
//                    ->orWhereHas('supplier', function($q) use ($search){
//                        $q->where('primarySupplierCode', 'LIKE', "%{$search}%")
//                            ->orWhere('supplierName', 'LIKE', "%{$search}%");
//                    });
//            });
//        }
//
//
//        return \DataTables::eloquent($supplierCatalog)
//            ->addColumn('Actions', 'Actions', "Actions")
//            ->order(function ($query) use ($input) {
//                if (request()->has('order')) {
//                    if ($input['order'][0]['column'] == 0) {
//                        $query->orderBy('supplierCatalogMasterID', $input['order'][0]['dir']);
//                    }
//                }
//            })
//            ->addIndexColumn()
//            ->with('orderCondition', $sort)
//            ->make(true);


    }


     public function itemReOpen(Request $request)
    {
        $reopen = ReopenDocument::reopenDocument($request);
        if (!$reopen["success"]) {
            return $this->sendError($reopen["message"]);
        } else {
            return $this->sendResponse(array(), $reopen["message"]);
        }
    }


    public function getAssignedItemsForCompany(Request $request) {
        $input = $request->all();
        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }
        $search = $request->input('search.value');
        $input = $this->convertArrayToSelectedValue($input, array('financeCategoryMaster', 'financeCategorySub', 'isActive', 'itemApprovedYN', 'itemConfirmedYN'));

        $companyId = $input['companyId'];
        $isGroup = \Helper::checkIsCompanyGroup($companyId);

        if ($isGroup) {
            $childCompanies = \Helper::getGroupCompany($companyId);
        } else {
            $childCompanies = [$companyId];
        }


        $itemMasters = ItemMaster::whereHas('itemAssigned', function ($query) use ($companyId) {
            return $query->where('companySystemID', '=', $companyId)->where('isAssigned', '=', -1);
        })->with(['unit', 'unit_by', 'financeMainCategory', 'financeSubCategory'])
        ->when((isset($input['PurchaseRequestID']) && $input['PurchaseRequestID'] > 0), function($query) use ($input) {
            $query->whereHas('item_category_type', function ($query) {
                        $query->whereIn('categoryTypeID', ItemCategoryTypeMaster::purchaseItems());
                    });
            $query->whereDoesntHave('purchase_request_details', function($query) use ($input) {
                $query->where('purchaseRequestID', $input['PurchaseRequestID']);
            });
        })->when((isset($input['purchaseOrderID']) && $input['purchaseOrderID'] > 0), function($query) use ($input) {
            $query->whereHas('item_category_type', function ($query) {
                        $query->whereIn('categoryTypeID', ItemCategoryTypeMaster::purchaseItems());
                    });
            $query->whereDoesntHave('purchase_order_details', function($query) use ($input) {
                $query->where('purchaseOrderMasterID', $input['purchaseOrderID']);
            });
        })->when((isset($input['materialReqeuestID']) && $input['materialReqeuestID'] > 0), function($query) use ($input) {
            $query->whereDoesntHave('erp_requestdetails', function($query) use ($input) {
                $query->where('requestDetailsID', $input['materialReqeuestID']);
            });
        })->when((isset($input['RequestID']) && $input['RequestID'] > 0), function($query) use ($input) {
                $query->whereHas('item_category_type', function ($query) {
                        $query->whereIn('categoryTypeID', ItemCategoryTypeMaster::purchaseItems());
                    });
                $query->whereDoesntHave('material_request_details', function($query) use ($input) {
                    $query->where('RequestID', $input['RequestID']);
                });
        })->when((isset($input['itemIssueAutoID']) && $input['itemIssueAutoID'] > 0), function($query) use ($input) {
            $query->whereHas('item_category_type', function ($query) {
                        $query->whereIn('categoryTypeID', ItemCategoryTypeMaster::purchaseItems());
                    });
            $query->whereDoesntHave('material_issue_details', function($query) use ($input) {
                $query->where('itemIssueAutoID', $input['itemIssueAutoID']);
            });
        });

        if (array_key_exists('financeCategoryMaster', $input)) {
            if ($input['financeCategoryMaster'] > 0 && !is_null($input['financeCategoryMaster'])) {
                $itemMasters->where('financeCategoryMaster', $input['financeCategoryMaster']);
            }
        }

        if (array_key_exists('financeCategorySub', $input)) {
            if ($input['financeCategorySub'] > 0 && !is_null($input['financeCategorySub'])) {
                $itemMasters->where('financeCategorySub', $input['financeCategorySub']);
            }
        }

        if (array_key_exists('isActive', $input)) {
            if (($input['isActive'] == 0 || $input['isActive'] == 1) && !is_null($input['isActive'])) {
                $itemMasters->where('isActive', $input['isActive']);
            }
        }
        if (array_key_exists('itemApprovedYN', $input)) {
            if (($input['itemApprovedYN'] == 0 || $input['itemApprovedYN'] == 1) && !is_null($input['itemApprovedYN'])) {
                $itemMasters->where('itemApprovedYN', $input['itemApprovedYN']);
            }
        }

        if (array_key_exists('itemConfirmedYN', $input)) {
            if (($input['itemConfirmedYN'] == 0 || $input['itemConfirmedYN'] == 1) && !is_null($input['itemConfirmedYN'])) {
                $itemMasters->where('itemConfirmedYN', $input['itemConfirmedYN']);
            }
        }

        if ($search) {
            $itemMasters = $itemMasters->where(function ($query) use ($search) {
                $query->where('primaryCode', 'LIKE', "%{$search}%")
                    ->orWhere('secondaryItemCode', 'LIKE', "%{$search}%")
                    ->orWhere('barcode', 'LIKE', "%{$search}%")
                    ->orWhere('itemDescription', 'LIKE', "%{$search}%");
            });
        }

        
        return \DataTables::eloquent($itemMasters)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('itemCodeSystem', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->addColumn('Actions', 'Actions', "Actions")
            ->make(true);

    }

    public function getAllAssignedItemsForCompany(Request $request) {
        $input = $request->all();

        $companyId = $input['companyId'];
        $isGroup = \Helper::checkIsCompanyGroup($companyId);

        if ($isGroup) {
            $childCompanies = \Helper::getGroupCompany($companyId);
        } else {
            $childCompanies = [$companyId];
        }

        $itemMasters = ItemMaster::whereHas('itemAssigned', function ($query) use ($companyId) {
            return $query->where('companySystemID', '=', $companyId);
        })->with(['unit', 'unit_by', 'financeMainCategory', 'financeSubCategory'])->get();

        return $this->sendResponse($itemMasters, 'Data retrieved successfully');


    }

    public function getInventorySubCat(Request $request)
    {
        $itemCategorySub = FinanceItemCategorySub::with(['finance_gl_code_bs','finance_gl_code_pl'])->where('itemCategoryID','=',1)->where('isActive',1)->get();
        $itemCategorySubArray = [];
        $i=0;
        foreach ($itemCategorySub as $value){
            $itemCategorySubArray[$i] = array_except($value,['finance_gl_code_bs','finance_gl_code_pl']);
            if($value->financeGLcodePLSystemID && $value->finance_gl_code_pl != null){
                $itemCategorySubArray[$i]['AccountCode'] = isset($value->finance_gl_code_pl->AccountCode)?$value->finance_gl_code_pl->AccountCode:'';
                $itemCategorySubArray[$i]['AccountDescription'] = isset($value->finance_gl_code_pl->AccountDescription)?$value->finance_gl_code_pl->AccountDescription:'';
            }else if($value->financeGLcodebBSSystemID && $value->finance_gl_code_bs != null){

                $itemCategorySubArray[$i]['AccountCode'] = isset($value->finance_gl_code_bs->AccountCode)?$value->finance_gl_code_bs->AccountCode:'';
                $itemCategorySubArray[$i]['AccountDescription'] = isset($value->finance_gl_code_bs->AccountDescription)?$value->finance_gl_code_bs->AccountDescription:'';

            }else{
                $itemCategorySubArray[$i]['AccountCode'] = '';
                $itemCategorySubArray[$i]['AccountDescription'] = '';
            }
            $i++;
        }

        $output = array(
        'financeItemCategorySub' => $itemCategorySubArray,
    );

    return $this->sendResponse($output, 'Record retrieved successfully');
    }

    public function getItemSubCategory(Request $request)
    {
        $input = $request->all();

        $subCategoryData = [];
        if (isset($input['itemSystemCode']) && $input['itemSystemCode'] > 0) {
            $itemMaster = ItemMaster::find($input['itemSystemCode']);

            if ($itemMaster) {
                $subCategoryData = FinanceItemCategorySub::with(['finance_gl_code_bs','finance_gl_code_pl','cogs_gl_code_pl','finance_gl_code_revenue'])
                                                         ->where('itemCategorySubID',$itemMaster->financeCategorySub)
                                                         ->first();
            }
        }

        return $this->sendResponse($subCategoryData, 'Record retrieved successfully');
    }

    public function validateItemAmend(Request $request)
    {
        $input = $request->all();

        $itemMaster = ItemMaster::find($input['itemID']);

        if (!$itemMaster) {
            return $this->sendError('Item Data not found');
        }
      $errorMessages = "Finance Sub Category,PartNO,Barcode,Unit Of measure cannot be amended ,the item used in ";
      $successMessages = "Use of Finance Sub Category,PartNO,Barcode,Unit Of measure checking is done in ";
      $purchase_request = PurchaseRequestDetails::where('itemCode', $input['itemID'])->first();


      $is_amend = true;
      $is_amend_suc = true;

      if ($purchase_request) {
        $errorMessages = $errorMessages."purchase request,";
        $amendable['amendable'] = false;
        $is_amend = false;
        } else {
            $successMessages = $successMessages."purchase request,";
            $amendable['amendable'] = true;
            $is_amend_suc = false;
        }

       $direct_purchased_order = PurchaseOrderDetails::where('itemCode', $input['itemID'])->first();
            
       if ($direct_purchased_order) {
        $errorMessages = $errorMessages."direct purchase order,";
        $amendable['amendable'] = false;
        $is_amend = false;
        } else {
            $successMessages = $successMessages."purchase order,";
            $amendable['amendable'] = (!$amendable['amendable']) ? false : true;
            $is_amend_suc = false;
        }


        
       $direct_grv= GRVDetails::where('itemCode', $input['itemID'])->first();
            
       if ($direct_grv) {
        $errorMessages = $errorMessages."direct grv,";
        $amendable['amendable'] = false;
        $is_amend = false;
        } else {
            $successMessages = $successMessages."direct grv,";
            $amendable['amendable'] = (!$amendable['amendable']) ? false : true;
            $is_amend_suc = false;
        }
                
       $material_request= MaterielRequestDetails::where('itemCode', $input['itemID'])->first();
            
       if ($material_request) {
        $errorMessages = $errorMessages."material request,";
        $amendable['amendable'] = false;
        $is_amend = false;
        } else {
            $successMessages = $successMessages."material request,";
            $amendable['amendable'] = (!$amendable['amendable']) ? false : true;
            $is_amend_suc = false;
        }


        $stock_adjustment = StockAdjustmentDetails::where('itemCodeSystem', $input['itemID'])->first();
            
        if ($stock_adjustment) {
            $errorMessages = $errorMessages."stock adjustment,";
         $amendable['amendable'] = false;
         $is_amend = false;
         } else {
            $successMessages = $successMessages."stock adjustment,";
             $amendable['amendable'] = (!$amendable['amendable']) ? false : true;
             $is_amend_suc = false;
         }


         $quatation = QuotationDetails::where('itemAutoID', $input['itemID'])->first();
            
         if ($quatation) {
            $errorMessages = $errorMessages."quotation/sales order,";
          $amendable['amendable'] = false;
          $is_amend = false;
          } else {
            $successMessages = $successMessages."quotation/sales order,";
              $amendable['amendable'] = (!$amendable['amendable']) ? false : true;
              $is_amend_suc = false;
          }

          $delivery_order = DeliveryOrderDetail::where('itemCodeSystem', $input['itemID'])->first();
            
          if ($quatation) {
            $errorMessages = $errorMessages."delivery order,";
         $amendable['amendable'] = false;
         $is_amend = false;
         } else {
            $successMessages = $successMessages."delivery order,";
             $amendable['amendable'] = (!$amendable['amendable']) ? false : true;
             $is_amend_suc = false;
         }

         
          $customer_invoice_details = CustomerInvoiceItemDetails::where('itemCodeSystem', $input['itemID'])->first();
            
          if ($customer_invoice_details) {
            $errorMessages = $errorMessages."customer invoice";
           $amendable['amendable'] = false;
           $is_amend = false;
           } else {
            $successMessages = $successMessages."customer invoice";
               $amendable['amendable'] = (!$amendable['amendable']) ? false : true;
               $is_amend_suc = false;
           }

           $erro_msg = [];
           if(!$is_amend)
           {
            array_push($erro_msg,$errorMessages);
           }
           
           $succes_msg = [];
           if(!$is_amend_suc)
           {
            array_push($succes_msg,$successMessages);
           }
           

            return $this->sendResponse(['errorMessages' => $erro_msg, 'successMessages' => $succes_msg, 'amendable'=> $amendable], "validated successfully");



    }
}
