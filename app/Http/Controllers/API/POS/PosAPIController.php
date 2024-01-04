<?php

namespace App\Http\Controllers\API\POS;

use App\helper\inventory;
use App\Http\Controllers\AppBaseController;
use App\Models\Company;
use App\Models\CustomerMasterCategory;
use App\Models\ErpLocation;
use App\Models\ItemMaster;
use App\Models\POSFinanceLog;
use App\Models\POSSOURCEShiftDetails;
use Illuminate\Support\Facades\DB;
use App\Models\SegmentMaster;
use App\Models\ChartOfAccount;
use App\Models\CustomerMaster;
use App\Models\Unit;
use App\Models\UnitConversion;
use App\Models\WarehouseMaster;
use App\Models\WarehouseItems;
use App\Models\WarehouseBinLocation;
use Illuminate\Http\Request;
use App\Models\FinanceItemCategorySub;
use App\Models\Employee;
use App\Models\FinanceItemCategoryMaster;
use App\Models\POSInvoiceSource;
use App\Models\POSSourceMenuSalesMaster;
use App\Models\POSSourceSalesReturn;
use App\Models\POSSTAGInvoice;
use App\Models\POSSTAGInvoiceDetail;
use App\Models\SupplierMaster;
use App\Services\POSService;

class PosAPIController extends AppBaseController
{
    private $POSService = null;

    public function __construct(POSService $POSService)
    {
        $this->POSService = $POSService;
    }

    function pullCompanyDetails(Request $request){
        DB::beginTransaction();
        try {
            $input = $request->all();

            $posType = isset($input['pos_type']) ? $input['pos_type']: 1;

                $companyDetails = Company::selectRaw('companySystemID, CompanyID, companyShortCode, CompanyName, registrationNumber, group_two as masterComapanySystemID, group_type, holding_percentage, holding_updated_date, companyCountry, CompanyAddress, CompanyEmail, localCurrencyID, reportingCurrency, vatRegisteredYN, vatRegistratonNumber, isActive, third_party_integration_keys.api_key, CompanyURL, CompanyTelephone, companyCountry,countrymaster.countryName, reportingCurrencyMaster.CurrencyCode as reportingCurrencyCode, localCurrencyMaster.CurrencyCode as localCurrencyCode')
                    ->join('third_party_integration_keys', 'companymaster.companySystemID', '=', 'third_party_integration_keys.company_id')
                    ->leftjoin('countrymaster', 'companymaster.companyCountry', '=', 'countrymaster.countryID')
                    ->leftjoin('currencymaster as reportingCurrencyMaster', 'companymaster.reportingCurrency', '=', 'reportingCurrencyMaster.currencyID')
                    ->leftjoin('currencymaster as localCurrencyMaster', 'companymaster.localCurrencyID', '=', 'localCurrencyMaster.currencyID')
                    ->where('third_party_integration_keys.third_party_system_id', $posType)
                    ->where('third_party_integration_keys.api_key', '!=', null)
                    ->groupBy('third_party_integration_keys.company_id')
                    ->get();

            DB::commit();
            return $this->sendResponse($companyDetails, 'Data Retrieved successfully');
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage());
        }
    }

    function pullCustomerCategory(Request $request)
    {

        DB::beginTransaction();
        try {
            $company_id = $request->get('company_id');
            $customerCategories = CustomerMasterCategory::whereHas('category_assigned', function($query) use($company_id){
                $query->where('companySystemID',$company_id);
                $query->where('isAssigned',1);
            })->with(['category_assigned' => function ($q) use($company_id) {
                $q->where('companySystemID',$company_id);
                $q->where('isAssigned',1);
            }])->get();

            $customerCategoryArray = array();
            foreach ($customerCategories as $item) {
                $data = array('id' => $item->categoryID, 'party_type' => 1, 'description' => $item->categoryDescription, 'isActive' => isset($item->category_assigned[0]->isActive) ? $item->category_assigned[0]->isActive : false);
                array_push($customerCategoryArray, $data);
            }

            DB::commit();
            return $this->sendResponse($customerCategoryArray, 'Data Retrieved successfully');
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage());
        }
    }

    public function pullLocation(Request $request)
    {

        DB::beginTransaction();
        try {
            $company_id = $request->get('company_id');
            $location = ErpLocation::selectRaw('locationID as id,locationName as description')
                ->where('locationName', '!=', '')
                ->get();
            DB::commit();
            return $this->sendResponse($location, 'Data Retrieved successfully');
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage());
        }
    }

    public function pullSegment(Request $request)
    {
        DB::beginTransaction();
        try {
            $company_id = $request->get('company_id');
            $segments = SegmentMaster::selectRaw('serviceLineSystemID As id,ServiceLineCode As segment_code ,ServiceLineDes as description,isActive as status')
                ->where('ServiceLineCode', '!=', '')
                ->where('companySystemID', '=', $company_id)
                ->where('ServiceLineDes', '!=', '')
                ->get();

            DB::commit();
            return $this->sendResponse($segments, 'Data Retrieved successfully');
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage());
        }
    }

    public function pullChartOfAccount(Request $request)
    {
        DB::beginTransaction();
        try {
            $company_id = $request->get('company_id');
            $chartOfAccount = ChartOfAccount::selectRaw('chartofaccounts.chartOfAccountSystemID As id,chartofaccounts.AccountCode As system_code,chartofaccounts.AccountCode As secondary_code,chartofaccounts.AccountDescription as description,
           isMasterAccount as is_master_account,chartofaccounts.masterAccount as master_account_id , "" as master_system_code,chartofaccounts.catogaryBLorPL as master_category,
           "" as category_id,"" as category_description,chartofaccounts.controlAccounts as sub_category,chartofaccounts.controllAccountYN as is_control_account,chartofaccounts.isActive as is_active,"" as default_type,
           "" as is_auto,"" as is_card,chartofaccounts.isBank as is_bank,"" as is_cash,"" as is_default_bank,"" as bank_name,"" as bank_branch,"" as bank_short_code,"" as bank_swift_code,"" as bank_cheque_number,
           "" as bank_account_number, "" as bank_currency_id,"" as bank_currency_code, "" as bank_currency_decimal,"" as is_deleted,"" as deleted_userID,"" as deleted_dateTime,
           confirmedYN as is_confirmed,confirmedEmpDate as confirmed_date,confirmedEmpID as confirmedbyEmpID,confirmedEmpName as confirmed_user_name,isApproved as is_approved,approvedDate as approved_date,
           approvedBySystemID as approvedbyEmpID,approvedBy as approved_user_name,approvedComment as approved_comment, chartofaccountsassigned.isActive as isActive')
                ->join('chartofaccountsassigned', 'chartofaccountsassigned.chartOfAccountSystemID', '=', 'chartofaccounts.chartOfAccountSystemID')
                ->where('chartofaccountsassigned.companySystemID', '=', $company_id)
                ->where('chartofaccountsassigned.isAssigned', '=', -1)
                ->where('chartofaccounts.chartOfAccountSystemID', '!=', '')
                ->where('chartofaccounts.AccountCode', '!=', '')
                ->where('chartofaccounts.AccountDescription', '!=', '')
                ->where('chartofaccounts.catogaryBLorPL', '!=', '')
                ->where('chartofaccounts.controlAccounts', '!=', '')
                ->get();

            DB::commit();
            return $this->sendResponse($chartOfAccount, 'Data Retrieved successfully');
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage());
        }
    }

    public function pullChartOfAccountMaster(Request $request)
    {
        DB::beginTransaction();
        try {
            $company_id = $request->get('company_id');

            $isGroup = \Helper::checkIsCompanyGroup($company_id);
    
            if ($isGroup) {
                $childCompanies = \Helper::getGroupCompany($company_id);
            } else {
                $childCompanies = [$company_id];
            }

            $input = $request->all();
            $input = $this->convertArrayToSelectedValue($input, array());

            if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
                $sort = 'asc';
            } else {
                $sort = 'desc';
            }

            if (isset($input['per_page'])) {
                $per_page = $input['per_page'];
            } else {
                $per_page = 10;
            }

            $chartOfAccount = ChartOfAccount::selectRaw('chartofaccounts.chartOfAccountSystemID As id,
                                                        chartofaccounts.AccountCode As accountCode,
                                                        chartofaccounts.AccountCode As secondary_code,
                                                        chartofaccounts.AccountDescription as AccountDescription,
                                                        chartofaccounts.controllAccountYN as is_control_account,
                                                        chartofaccounts.isActive as is_active,
                                                        chartofaccounts.isBank as is_bank, 
                                                        accountstype.description as category,
                                                        accountstype.accountsType as category_id, 
                                                        controlaccounts.description as controlAccount,
                                                        controlaccounts.controlAccountsSystemID as control_account_id')
                ->join('accountstype', 'accountstype.accountsType', '=', 'chartofaccounts.catogaryBLorPLID')
                ->join('controlaccounts', 'controlaccounts.controlAccountsSystemID', '=', 'chartofaccounts.controlAccountsSystemID')
                ->where('chartofaccounts.chartOfAccountSystemID', '!=', '')
                ->where('chartofaccounts.AccountCode', '!=', '')
                ->where('chartofaccounts.AccountDescription', '!=', '')
                ->where('chartofaccounts.catogaryBLorPL', '!=', '')
                ->where('chartofaccounts.controlAccounts', '!=', '')
                ->where('chartofaccounts.isApproved', '=', 1)
                ->where('chartofaccounts.isActive', '=', 1);
                
                
                if (isset($input['control_account_id'])) {
                    $control_account_id = $input['control_account_id'];
                    $chartOfAccount = $chartOfAccount->where('chartofaccounts.controlAccountsSystemID', '=', $control_account_id);
                }
    
                if (isset($input['category_id'])) {
                    $category_id = $input['category_id'];
                    $chartOfAccount = $chartOfAccount->where('chartofaccounts.catogaryBLorPLID', '=', $category_id);
                }

                if (isset($input['is_control_account'])) {
                    $is_control_account = $input['is_control_account'];
                    $chartOfAccount = $chartOfAccount->where('chartofaccounts.controllAccountYN', '=', $is_control_account);
                }
                
                if (isset($input['is_bank'])) {
                    $is_bank = $input['is_bank'];
                    $chartOfAccount = $chartOfAccount->where('chartofaccounts.isBank', '=', $is_bank);
                }
                
                if (isset($input['chart_of_account_system_id'])) {
                    $chart_of_account_system_id = $input['chart_of_account_system_id'];
                    $chartOfAccount = $chartOfAccount->where('chartofaccounts.chartOfAccountSystemID', '=', $chart_of_account_system_id);
                }

                if (isset($input['coa_search'])) {
                    $search = $input['coa_search'];
                    $search = str_replace("\\", "\\\\", $search);
                    $chartOfAccount = $chartOfAccount->where(function ($query) use ($search) {
                        $query->where('chartofaccounts.AccountDescription', 'LIKE', "%{$search}%")
                            ->orWhere('chartofaccounts.AccountCode', 'LIKE', "%{$search}%");
                    });
                }

                $chartOfAccount = $chartOfAccount->paginate($per_page);

            DB::commit();
            return $this->sendResponse($chartOfAccount, 'Data Retrieved successfully');

        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage());
        }
    }

    public function pullUnitOfMeasure(Request $request)
    {
        DB::beginTransaction();
        try {
            $units = Unit::selectRaw('UnitID As id,UnitShortCode As short_code ,UnitDes as description')
                ->where('UnitShortCode', '!=', '')
                ->get();
            DB::commit();
            return $this->sendResponse($units, 'Data Retrieved successfully');
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage());
        }
    }

    public function pullUnitConversion(Request $request)
    {
        DB::beginTransaction();
        try {
            $unitConvertion = UnitConversion::selectRaw('unitsConversionAutoID As id,masterUnitID As master_id ,subUnitID as sub_id,conversion as conversion')
                ->where('masterUnitID', '!=', '')
                ->where('subUnitID', '!=', '')
                ->where('conversion', '!=', '')
                ->get();
            DB::commit();
            return $this->sendResponse($unitConvertion, 'Data Retrieved successfully');
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage());
        }
    }



    public function pullWarehouse(Request $request)
    {
        DB::beginTransaction();
        try {
            $company_id = $request->get('company_id');
            $warehouse = WarehouseMaster::selectRaw('wareHouseSystemCode As id,wareHouseCode As system_code ,wareHouseDescription as description,wareHouseLocation as location_id,
                erp_location.locationName as location,isPosLocation as is_pos_location, isDefault as is_default ,warehouseType as warehouse_type,WIPGLCode as gl_id,"" as address,
                "" as phone_number,isActive as is_active,"" as warehouse_image,
                "" as footer_note')
                ->join('erp_location', 'erp_location.locationID', '=', 'warehousemaster.wareHouseLocation')
                ->where('wareHouseCode', '!=', '')
                ->where('wareHouseDescription', '!=', '')
                ->where('wareHouseLocation', '!=', '')
                ->where('companySystemID', '=', $company_id)
                ->get();

            DB::commit();
            return $this->sendResponse($warehouse, 'Data Retrieved successfully');
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage());
        }
    }

    public function pullWarehouseItem(Request $request)
    {
        DB::beginTransaction();
        try {
            $company_id = $request->get('company_id');
            $warehouseItems = WarehouseItems::selectRaw('warehouseItemsID As id,warehouseitems.warehouseSystemCode As warehouse_id ,
             erp_location.locationName as location,erp_location.locationName as description,itemmaster.itemCodeSystem as item_id,itemmaster.primaryCode as item_code,
             itemmaster.itemDescription as item_description,"" as is_active,"" as sales_price,unitOfMeasure as unit_id')
                ->join('warehousemaster', 'warehousemaster.warehouseSystemCode', '=', 'warehouseitems.warehouseSystemCode')
                ->join('erp_location', 'erp_location.locationID', '=', 'warehousemaster.wareHouseLocation')
                ->join('itemmaster', 'itemmaster.itemCodeSystem', '=', 'warehouseitems.itemSystemCode')
                ->where('warehouseitems.warehouseSystemCode', '!=', '')
                ->where('erp_location.locationName', '!=', '')
                ->where('itemmaster.primaryCode', '!=', '')
                ->where('itemmaster.itemDescription', '!=', '')
                ->where('warehouseitems.companySystemID', '=', $company_id)
                ->get();

            DB::commit();
            return $this->sendResponse($warehouseItems, 'Data Retrieved successfully');
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage());
        }
    }

    public function pullWarehouseBinLocation(Request $request)
    {
        DB::beginTransaction();
        try {
            $company_id = $request->get('company_id');
            $warehousebin = WarehouseBinLocation::selectRaw('binLocationID As binLocationID,warehousemaster.wareHouseSystemCode As warehouseAutoID,binLocationDes As Description')
                ->join('warehousemaster', 'warehousemaster.warehouseSystemCode', '=', 'warehousebinlocationmaster.warehouseSystemCode')
                ->where('binLocationID', '!=', '')
                ->where('warehousemaster.wareHouseSystemCode', '!=', '')
                ->where('binLocationDes', '!=', '')
                ->where('warehousebinlocationmaster.companySystemID', '=', $company_id)
                ->get();

            DB::commit();
            return $this->sendResponse($warehousebin, 'Data Retrieved successfully');
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage());
        }
    }


    public function pullItemSubCategory(Request $request)
    {
        DB::beginTransaction();
        try {
            $company_id = $request->get('company_id');
            $input = $request->all();
            $financeItemCategorySub = FinanceItemCategorySub::selectRaw('financeitemcategorysub.itemCategorySubID As id,financeitemcategorysub.categoryDescription As description,itemCategoryID As master_id ,
            financeitemcategorysub.financeGLcodeRevenueSystemID as revenue_gl,financeitemcategorysub.financeGLcodePLSystemID as cost_gl, financeitemcategorysubassigned.isActive as isActive')
                ->join('financeitemcategorysubassigned', 'financeitemcategorysubassigned.itemCategorySubID', '=', 'financeitemcategorysub.itemCategorySubID')
                ->where('financeitemcategorysub.itemCategorySubID', '!=', '')
                ->where('financeitemcategorysub.categoryDescription', '!=', '')
                ->where('financeitemcategorysubassigned.companySystemID', '=', $company_id)
                ->where('financeitemcategorysubassigned.isAssigned', '=', -1);

                if(isset($input['category_id'])){
                    $financeItemCategorySub = $financeItemCategorySub->where('financeitemcategorysub.itemCategoryID', '=', $input['category_id']);
                }
                $financeItemCategorySub = $financeItemCategorySub->get();

            DB::commit();
            return $this->sendResponse($financeItemCategorySub, 'Data Retrieved successfully');
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage());
        }
    }
    
    public function getItemMasters($company_id, $third_party_system_id){
        $data = ItemMaster::selectRaw('itemmaster.itemCodeSystem as id, primaryCode as system_code, itemmaster.documentID as document_id, 
            (case when itemmaster.secondaryItemCode = "" or isnull(itemmaster.secondaryItemCode) then primaryCode else itemmaster.secondaryItemCode end) as secondary_code, "" as image,(case when itemShortDescription = "" or isnull(itemShortDescription) then itemmaster.itemDescription else itemShortDescription end) as name,itemmaster.itemDescription as description,
            itemmaster.financeCategoryMaster as category_id, financeitemcategorymaster.categoryDescription as category_description, itemmaster.financeCategorySub as sub_category_id, "" as sub_sub_category_id, itemmaster.barcode as barcode, financeitemcategorymaster.categoryDescription as finance_category, itemmaster.secondaryItemCode as part_number, unit as unit_id, units.UnitShortCode as unit_description, "" as reorder_point, "" as maximum_qty,
            rev.chartOfAccountSystemID as revenue_gl,rev.AccountDescription as revenue_description,
            cost.chartOfAccountSystemID as cost_gl,cost.AccountDescription as cost_description,"" as asset_gl,"" as asset_description,"" as sales_tax_id, "" as purchase_tax_id,
            vatSubCategory as vat_sub_category_id,itemmaster.isActive as is_active,itemApprovedComment as comment, "" as is_sub_item_exist,"" as is_sub_item_applicable,
            "" as local_currency_id,"" as local_currency,"" as local_exchange_rate,"" as local_selling_price,"" as local_decimal_place,
            "" as reporting_currency_id,"" as reporting_currency,"" as reporting_exchange_rate,"" as reporting_selling_price,"" as reporting_decimal_place,
            "" as is_deleted,"" as deleted_by,"" as deleted_date_time,itemmaster.pos_type, itemassigned.isActive')
            ->join('financeitemcategorymaster', 'financeitemcategorymaster.itemCategoryID', '=', 'itemmaster.financeCategoryMaster')
            ->join('financeitemcategorysub', 'financeitemcategorysub.itemCategorySubID', '=', 'itemmaster.financeCategorySub')
            ->join('units', 'units.UnitID', '=', 'itemmaster.unit')
            ->leftJoin('chartofaccounts as rev', 'rev.chartOfAccountSystemID', '=', 'financeitemcategorysub.financeGLcodeRevenueSystemID')
            ->leftJoin('chartofaccounts as cost', 'cost.chartOfAccountSystemID', '=', 'financeitemcategorysub.financeGLcodePLSystemID')
            ->join('itemassigned', 'itemassigned.itemCodeSystem', '=', 'itemmaster.itemCodeSystem')
            ->where('itemassigned.companySystemID', '=', $company_id)
            ->where('itemassigned.isAssigned', '=', -1)
            ->where('primaryCode', '!=', '')
            ->where('itemmaster.documentID', '!=', '')
            ->where('itemmaster.financeCategoryMaster', '!=', '')
            ->where('itemmaster.financeCategorySub', '!=', '')
            ->where('itemmaster.itemDescription', '!=', '')
            ->where('financeitemcategorymaster.categoryDescription', '!=', '')
            ->where('units.UnitShortCode', '!=', '');
            // ->where('itemmaster.financeCategoryMaster', '!=', 3)


        if ($third_party_system_id == 1) {
            $data = $data->whereIn('itemmaster.pos_type', [1,3]);
        } else if ($third_party_system_id == 2) {
            $data = $data->whereIn('itemmaster.pos_type', [2,3]);
        }

        $data = $data->get();
        return $data;
    }

    public function pullItem(Request $request)
    {
        DB::beginTransaction();
        try {

            $company_id = $request->get('company_id');
            $third_party_system_id = $request->get('third_party_system_id');

            $items = $this->getItemMasters($company_id, $third_party_system_id);

            DB::commit();
            return $this->sendResponse($items, 'Data Retrieved successfully');
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage());
        }
    }

    public function pullItemsBySubCategory(Request $request)
    {
        DB::beginTransaction();
        try {
            $company_id = $request->get('company_id');
            $input = $request->all();
            $input = $this->convertArrayToSelectedValue($input, array());

            if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
                $sort = 'asc';
            } else {
                $sort = 'desc';
            }


            if (isset($input['sub_category_id'])) {
                $sub_category_id = $input['sub_category_id'];
            } else {
                $sub_category_id = 0;
            }

            if (isset($input['per_page'])) {
                $per_page = $input['per_page'];
            } else {
                $per_page = 10;
            }

            $items = ItemMaster::selectRaw('itemmaster.itemCodeSystem as id, primaryCode as system_code, itemmaster.documentID as document_id, 
            (case when itemmaster.secondaryItemCode = "" or isnull(itemmaster.secondaryItemCode) then primaryCode else itemmaster.secondaryItemCode end) as secondary_code, "" as image,(case when itemShortDescription = "" or isnull(itemShortDescription) then itemmaster.itemDescription else itemShortDescription end) as name,itemmaster.itemDescription as description,
            itemmaster.financeCategoryMaster as category_id, financeitemcategorymaster.categoryDescription as category_description, itemmaster.financeCategorySub as sub_category_id, "" as sub_sub_category_id, itemmaster.barcode as barcode, financeitemcategorymaster.categoryDescription as finance_category, itemmaster.secondaryItemCode as part_number, unit as unit_id, units.UnitShortCode as unit_description, "" as reorder_point, "" as maximum_qty,
            rev.chartOfAccountSystemID as revenue_gl,rev.AccountDescription as revenue_description,
            cost.chartOfAccountSystemID as cost_gl,cost.AccountDescription as cost_description,"" as asset_gl,"" as asset_description,"" as sales_tax_id, "" as purchase_tax_id,
            vatSubCategory as vat_sub_category_id,itemmaster.isActive as is_active,itemApprovedComment as comment, "" as is_sub_item_exist,"" as is_sub_item_applicable,
            "" as local_currency_id,"" as local_currency,"" as local_exchange_rate,"" as local_selling_price,"" as local_decimal_place,
            "" as reporting_currency_id,"" as reporting_currency,"" as reporting_exchange_rate,"" as reporting_selling_price,"" as reporting_decimal_place,
            "" as is_deleted,"" as deleted_by,"" as deleted_date_time,itemmaster.pos_type, itemassigned.wacValueLocal as item_cost, itemassigned.wacValueLocal as selling_price , 0 as VAT')
                ->join('financeitemcategorymaster', 'financeitemcategorymaster.itemCategoryID', '=', 'itemmaster.financeCategoryMaster')
                ->join('financeitemcategorysub', 'financeitemcategorysub.itemCategorySubID', '=', 'itemmaster.financeCategorySub')
                ->join('units', 'units.UnitID', '=', 'itemmaster.unit')
                ->leftJoin('chartofaccounts as rev', 'rev.chartOfAccountSystemID', '=', 'financeitemcategorysub.financeGLcodeRevenueSystemID')
                ->leftJoin('chartofaccounts as cost', 'cost.chartOfAccountSystemID', '=', 'financeitemcategorysub.financeGLcodePLSystemID')
                ->join('itemassigned', 'itemassigned.itemCodeSystem', '=', 'itemmaster.itemCodeSystem')
                ->where('itemassigned.companySystemID', '=', $company_id)
                ->where('primaryCode', '!=', '')
                ->where('itemmaster.documentID', '!=', '')
                ->where('itemmaster.financeCategoryMaster', '!=', '')
                ->where('itemmaster.financeCategorySub', '!=', '')
                ->where('itemmaster.itemDescription', '!=', '')
                ->where('financeitemcategorymaster.categoryDescription', '!=', '')
                ->where('units.UnitShortCode', '!=', '')
                // ->where('itemmaster.financeCategoryMaster', '!=', 3)
                ->where('financeitemcategorysub.itemCategorySubID', '=', $sub_category_id);
                

                if (isset($input['item_search'])) {
                    $search = $input['item_search'];
                    $search = str_replace("\\", "\\\\", $search);
                    $items = $items->where(function ($query) use ($search) {
                        $query->where('itemmaster.itemDescription', 'LIKE', "%{$search}%")
                            ->orWhere('itemmaster.primaryCode', 'LIKE', "%{$search}%");
                    });
                }

                $items = $items->paginate($per_page);

            DB::commit();
            return $this->sendResponse($items, 'Data Retrieved successfully');

        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage());
        }
    }


    public function pullItemBinLocation()
    {
        DB::beginTransaction();
        try {


            DB::commit();
            return $this->sendResponse($warehousebin, 'Data Retrieved successfully');
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage());
        }
    }


    public function pullUser(Request $request)
    {
        DB::beginTransaction();

        try {
            $input = $request->all();
            $isPos = isset($input['is_pos']) ? $input['is_pos']: 0;

            if($isPos == 1) {
                $employee = Employee::selectRaw('employees.employeeSystemID as id,empID as system_code,empName As name,empUserName As user_name,empEmail As email, empActive as is_active, discharegedYN as isDischarged')
                    ->join('srp_erp_employeenavigation', 'employees.employeeSystemID', '=', 'srp_erp_employeenavigation.employeeSystemID')
                    ->join('srp_erp_navigationusergroupsetup', 'srp_erp_employeenavigation.userGroupID', '=', 'srp_erp_navigationusergroupsetup.userGroupID')
                    ->where('srp_erp_navigationusergroupsetup.navigationMenuID', 371)
                    ->groupBy('employees.empID')
                    ->get();

            } else {
                $company_id = $request->get('company_id');
                $employee = Employee::selectRaw('employeeSystemID as id,empID as system_code,empName As name,empUserName As user_name,empEmail As email ,
            empActive as is_active')
                    ->where('discharegedYN', 0)
                    ->where('empCompanySystemID', '=', $company_id)
                    ->get();

            }
            DB::commit();
            return $this->sendResponse($employee, 'Data Retrieved successfully');

        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage());
        }
    }

    public function pullItemCategory(Request $request)
    {
        DB::beginTransaction();
        try {
            $financeItemCategory = FinanceItemCategoryMaster::selectRaw('itemCategoryID As id,categoryDescription As description,itemCodeDef As item_code ')
                ->where('itemCategoryID', '!=', '')
                ->where('categoryDescription', '!=', '')
                ->where('itemCodeDef', '!=', '')
                ->get();

            DB::commit();
            return $this->sendResponse($financeItemCategory, 'Data Retrieved successfully');
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage());
        }
    }

    public function pullSupplierMaster(Request $request)
    {
        DB::beginTransaction();
        try {
            $input = $request->all();
            $input = $this->convertArrayToSelectedValue($input, array());

            if (isset($input['per_page'])) {
                $per_page = $input['per_page'];
            } else {
                $per_page = 10;
            }

            $supplierMaster = SupplierMaster::selectRaw('supplierCodeSystem As supplier_id,
                                                        supplierName As supplier_name,
                                                        primarySupplierCode As supplier_code,
                                                        liabilityAccount as gl_code,
                                                        UnbilledGRVAccount,
                                                        address,
                                                        telephone,
                                                        fax,
                                                        supEmail
                                                        ')
                ->where('supplierCodeSystem', '!=', '')
                ->where('isActive', '=', 1);

            if (isset($input['supplier_id'])) {
                $supplier_id = $input['supplier_id'];
                $supplierMaster = $supplierMaster->where('suppliermaster.supplierCodeSystem', '=', $supplier_id);
            }
            if (isset($input['supplier_search'])) {
                $search = $input['supplier_search'];
                $search = str_replace("\\", "\\\\", $search);
                $supplierMaster = $supplierMaster->where(function ($query) use ($search) {
                    $query->where('suppliermaster.supplierName', 'LIKE', "%{$search}%")
                        ->orWhere('suppliermaster.primarySupplierCode', 'LIKE', "%{$search}%");
                });
            }

            $supplierMaster = $supplierMaster->paginate($per_page);

            DB::commit();
            return $this->sendResponse($supplierMaster, 'Data Retrieved successfully');
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage());
        }
    }

    public function handleRequest(Request $request)
    {
        define('INVOICE', 'INVOICE');
        define('INVOICE_RPOS', 'INVOICE_RPOS');
        switch ($request->input('request')) {
            case INVOICE:
            case INVOICE_RPOS:
                return $this->POSService->getMappingData($request);
            default:
                return [
                    'success'   => false,
                    'message'   => 'Requested API not available, please recheck!',
                    'data'      => null
                ];
        }
    }

    public function getAllInvoicesPos(Request $request)
    {
        $input = $request->all();

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $search = $request->input('search.value');

        $posData = POSInvoiceSource::withCount([
            'invoiceDetailSource AS qtyTotal' => function ($query) {
                $query->select(DB::raw("SUM(qty) as qtyTotal"));
            }
        ])
            ->with(['invoiceDetailSource', 'employee', 'invoicePaymentSource' => function ($q) {
                $q->with(['paymentConfigMaster']);
            }])
            ->whereHas('invoiceDetailSource')
            ->whereHas('invoicePaymentSource')
            ->where('isVoid', 0);

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $posData = $posData->where(function ($query) use ($search) {
                $query->where('invoiceCode', 'LIKE', "%{$search}%");
            });
        }

        return \DataTables::eloquent($posData)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('invoiceID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    public function getAllShiftsRPOS(Request $request){

        $input = $request->all();
        $isCompleted = isset($input['isCompleted']) ? $input['isCompleted']: 0;

        $companyId = $request->companyId;

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $postedShifts = POSFinanceLog::groupBy('shiftId')->where('status', 2)->pluck('shiftId');

        if ($isCompleted == 1){
            $shifts = POSSOURCEShiftDetails::where('posType', 2)
                ->leftjoin('warehousemaster', 'warehousemaster.wareHouseSystemCode', '=', 'pos_source_shiftdetails.wareHouseID')
                ->leftjoin('pos_source_menusalesmaster', 'pos_source_menusalesmaster.shiftID', '=', 'pos_source_shiftdetails.shiftID')
                ->whereIn('pos_source_shiftdetails.shiftID', $postedShifts)
                ->where('pos_source_shiftdetails.companyID', $companyId)
                ->select('pos_source_shiftdetails.shiftID', 'pos_source_shiftdetails.createdUserName', 'pos_source_shiftdetails.startTime', 'pos_source_shiftdetails.endTime', 'warehousemaster.wareHouseDescription', 'pos_source_shiftdetails.transactionCurrencyDecimalPlaces')
                ->selectRaw('SUM(pos_source_menusalesmaster.grossTotal) as totalBillAmount')
                ->selectRaw('COUNT(pos_source_menusalesmaster.shiftID) as noOfBills')->groupBy('pos_source_menusalesmaster.shiftID');
        } else {
            $shifts = POSSOURCEShiftDetails::where('posType', 2)
                ->leftjoin('warehousemaster', 'warehousemaster.wareHouseSystemCode', '=', 'pos_source_shiftdetails.wareHouseID')
                ->leftjoin('pos_source_menusalesmaster', 'pos_source_menusalesmaster.shiftID', '=', 'pos_source_shiftdetails.shiftID')
                ->whereNotIn('pos_source_shiftdetails.shiftID', $postedShifts)
                ->where('pos_source_shiftdetails.companyID', $companyId)
                ->select('pos_source_shiftdetails.shiftID', 'pos_source_shiftdetails.createdUserName', 'pos_source_shiftdetails.startTime', 'pos_source_shiftdetails.endTime', 'warehousemaster.wareHouseDescription', 'pos_source_shiftdetails.transactionCurrencyDecimalPlaces')
                ->selectRaw('SUM(pos_source_menusalesmaster.grossTotal) as totalBillAmount')
                ->selectRaw('COUNT(pos_source_menusalesmaster.shiftID) as noOfBills')->groupBy('pos_source_menusalesmaster.shiftID');
        }

        return \DataTables::eloquent($shifts)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('shiftID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    public function getAllShiftsGPOS(Request $request){

        $input = $request->all();
        $isCompleted = isset($input['isCompleted']) ? $input['isCompleted']: 0;

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }
        $companyId = $request->companyId;
        $postedShifts = POSFinanceLog::groupBy('shiftId')->where('status', 2)->pluck('shiftId');

        if ($isCompleted == 1){
            $shifts = POSSOURCEShiftDetails::where('posType', 1)
                ->leftjoin('warehousemaster', 'warehousemaster.wareHouseSystemCode', '=', 'pos_source_shiftdetails.wareHouseID')
                ->leftjoin('pos_source_invoice', 'pos_source_invoice.shiftID', '=', 'pos_source_shiftdetails.shiftID')
                ->whereIn('pos_source_shiftdetails.shiftID', $postedShifts)
                ->where('pos_source_shiftdetails.companyID', $companyId)
                ->select('pos_source_shiftdetails.shiftID', 'pos_source_shiftdetails.createdUserName', 'pos_source_shiftdetails.startTime', 'pos_source_shiftdetails.endTime', 'warehousemaster.wareHouseDescription', 'pos_source_shiftdetails.transactionCurrencyDecimalPlaces')
                ->selectRaw('SUM(pos_source_invoice.netTotal) as totalBillAmount')
                ->selectRaw('COUNT(pos_source_invoice.shiftID) as noOfBills')->groupBy('pos_source_invoice.shiftID');
        } else {

            $shifts = POSSOURCEShiftDetails::where('posType', 1)
                ->leftjoin('warehousemaster', 'warehousemaster.wareHouseSystemCode', '=', 'pos_source_shiftdetails.wareHouseID')
                ->leftjoin('pos_source_invoice', 'pos_source_invoice.shiftID', '=', 'pos_source_shiftdetails.shiftID')
                ->whereNotIn('pos_source_shiftdetails.shiftID', $postedShifts)
                ->where('pos_source_shiftdetails.companyID', $companyId)
                ->select('pos_source_shiftdetails.shiftID', 'pos_source_shiftdetails.createdUserName', 'pos_source_shiftdetails.startTime', 'pos_source_shiftdetails.endTime', 'warehousemaster.wareHouseDescription', 'pos_source_shiftdetails.transactionCurrencyDecimalPlaces')
                ->selectRaw('SUM(pos_source_invoice.netTotal) as totalBillAmount')
                ->selectRaw('COUNT(pos_source_invoice.shiftID) as noOfBills')->groupBy('pos_source_invoice.shiftID');
        }

        return \DataTables::eloquent($shifts)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('shiftID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    public function getPosInvoiceData(Request $request)
    {
        $input = $request->all();
        $invoiceId = $input['invoiceId'];

        $data['invoiceData'] = POSInvoiceSource::withCount(['invoiceDetailSource AS qtyTotal' => function ($query) {
            $query->select(DB::raw("SUM(qty) as qtyTotal"));
        }, 'invoiceDetailSource AS transactionAmountBeforeDiscountTotal' => function ($query) {
            $query->select(DB::raw("SUM(transactionAmountBeforeDiscount) as transactionAmountBeforeDiscount"));
        }, 'invoiceDetailSource AS taxAmountTotal' => function ($query) {
            $query->select(DB::raw("SUM(taxAmount) as taxAmount"));
        }])
            ->with(['invoiceDetailSource' => function ($q) {
                $q->with(['item_assigned' => function ($q1) {
                    $q1->with(['item_master' => function ($q2) {
                        $q2->with(['unit']);
                    }]);
                }]);
            }, 'employee', 'invoicePaymentSource' => function ($q) {
                $q->with(['paymentConfigMaster']);
            }])
            ->where('invoiceID', $invoiceId)->first();

        return $data;
    }

    public function getAllInvoicesPosReturn(Request $request)
    {
        $input = $request->all();

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $search = $request->input('search.value');



        $posDataReturn = POSSourceSalesReturn::with(['invoice', 'invoiceReturn' => function ($q) {
            $q->with(['item_assigned' => function ($q1) {
                $q1->with(['item_master' => function ($q2) {
                    $q2->with(['unit']);
                }]);
            }]);
        }, 'employee']);

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $posDataReturn = $posDataReturn->where(function ($query) use ($search) {
                $query->whereHas('invoice', function ($q) use ($search) {
                    $q->where('invoiceCode', 'LIKE', "%{$search}%");
                });
                $query->orWhere('documentSystemCode', 'LIKE', "%{$search}%");
            });
        }

        return \DataTables::eloquent($posDataReturn)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('salesReturnID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    public function getPosInvoiceReturnData(Request $request)
    {
        $input = $request->all();
        $salesReturnId = $input['salesReturnId'];

        $data['invoiceReturnData'] =  POSSourceSalesReturn::withCount(['invoiceReturn AS taxAmountTotal' => function ($query) {
            $query->select(DB::raw("SUM(taxAmount) as taxAmountTotal"));
        }])
            ->with(['invoice', 'invoiceReturn' => function ($q) {
                $q->with(['item_assigned' => function ($q1) {
                    $q1->with(['item_master' => function ($q2) {
                        $q2->with(['unit']);
                    }]);
                }]);
            }, 'employee'])
            ->where('salesReturnID', $salesReturnId)
            ->first();

        return $data;
    }

    public function getAllInvoicesRPos(Request $request)
    {
        $input = $request->all();

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $search = $request->input('search.value'); 

        $posDataRpos= POSSourceMenuSalesMaster::with(['wareHouseMaster'])
        ->where('isVoid', 0)
        ->where('isHold', 0)
        ->groupBy('menuSalesID')
        ->groupBy('wareHouseAutoID');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $posDataRpos = $posDataRpos->where(function ($query) use ($search) {
                $query->whereHas('wareHouseMaster', function ($q) use ($search) {
                    $q->where('wareHouseCode', 'LIKE', "%{$search}%");
                });
                $query->Orwhere('invoiceCode', 'LIKE', "%{$search}%");
            });
        }

        return \DataTables::eloquent($posDataRpos)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('menuSalesID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }
    public function getRPOSInvoiceData(Request $request){ 
        $input = $request->all(); 
        $menuSalesID = $input['menuSalesID'];
        $data['rposInvoiceData'] = POSSourceMenuSalesMaster::withCount(['menuSalesPayment AS menuSalesPaymentTotal' => function ($query) {
            $query->select(DB::raw("SUM(amount) as menuSalesPaymentTotal"));
        }]) 
        ->with(['wareHouseMaster','menuSalesItems' => function ($q){ 
            $q->with(['menuMaster']);
        },'customerTypeMaster','menuSalesPayment' => function ($q2){ 
            $q2->with(['paymentConfig']);
        }])
        ->where('menuSalesID',$menuSalesID)
        ->first();
        return $data;
    }

    public function pullCustomerMaster(Request $request)
    {
        DB::beginTransaction();
        try {
            $company_id = $request->get('company_id');
            $input = $request->all();
            $input = $this->convertArrayToSelectedValue($input, array());

            if (isset($input['per_page'])) {
                $per_page = $input['per_page'];
            } else {
                $per_page = 10;
            }

            $customerMaster = CustomerMaster::selectRaw('   customermaster.customerCodeSystem as id,
                                                            customermaster.CustomerName,
                                                            customermaster.CutomerCode,
                                                            customermaster.customerShortCode as secondaryCode,
                                                            customermaster.customerCategoryID,
                                                            customermaster.customerAddress1,
                                                            customermaster.customerAddress2,
                                                            customermaster.customerCity,
                                                            customermaster.customerCountry as customerCountryID,
                                                            countrymaster.countryName as customerCountryName,
                                                            customercontactdetails.contactPersonTelephone as customerTelephone,
                                                            customercontactdetails.contactPersonEmail as customerEmail,

                                                            customermaster.custGLaccount as gl_code,
                                                            customermaster.custUnbilledAccount,

                                                            currencymaster.currencyID as customerCurrencyID,
                                                            currencymaster.CurrencyCode as customerCurrencyCode,
                                                            currencymaster.CurrencyName as customerCurrencyName,
                                                            currencymaster.DecimalPlaces as customerCurrencyDecimalPlaces,

                                                            customermaster.creditDays as customerCreditPeriod,
                                                            customermaster.creditLimit as customerCreditLimit,
                                                            customermaster.isCustomerActive,
                                                            customermaster.primaryCompanySystemID,
                                                            customermaster.primaryCompanyID,
                                                            customerassigned.isActive as isActive
                                                        ')
                                                ->join('customerassigned', 'customerassigned.customerCodeSystem', '=', 'customermaster.customerCodeSystem')
                                                ->join('countrymaster', 'countrymaster.countryID', '=', 'customermaster.customerCountry')
                                                ->join('customercontactdetails', 'customercontactdetails.customerID', '=', 'customermaster.customerCodeSystem')
                                                ->join('customercurrency as custcur', 'custcur.customerCodeSystem', '=', 'customermaster.customerCodeSystem')
                                                ->join('currencymaster', 'currencymaster.currencyID', '=', 'custcur.currencyID')
                                                ->where('customermaster.customerCodeSystem', '!=', '')
                                                ->where('isCustomerActive', '=', 1)
                                                ->where('customerassigned.companySystemID', '=', $company_id)
                                                ->where('customerassigned.isAssigned', '=', -1);

            if (isset($input['customer_id'])) {
                $customer_id = $input['customer_id'];
                $customerMaster = $customerMaster->where('customermaster.customerCodeSystem', '=', $customer_id);
            }
            if (isset($input['customer_search'])) {
                $search = $input['customer_search'];
                $search = str_replace("\\", "\\\\", $search);
                $customerMaster = $customerMaster->where(function ($query) use ($search) {
                    $query->where('customermaster.CustomerName', 'LIKE', "%{$search}%")
                        ->orWhere('customermaster.CutomerCode', 'LIKE', "%{$search}%");
                });
            }
            if (isset($input['is_paginate'])){
                if($input['is_paginate']==1){
                    $customerMaster = $customerMaster->paginate($per_page);
                } else {
                    $customerMaster = $customerMaster->get();
                }
            } else {
                $customerMaster = $customerMaster->paginate($per_page);
            }

            DB::commit();
            return $this->sendResponse($customerMaster, 'Data Retrieved successfully');
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage());
        }
    }

    public function getAllBills(Request $request){

        $input = $request->all();

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }
        $shiftDetails = POSSOURCEShiftDetails::where('shiftID',$input['shiftId'])->first();

        if($shiftDetails->posType == 1) {
            $bills = POSInvoiceSource::with(['wareHouseMaster'])->where('shiftID', $input['shiftId']);
        } else if ($shiftDetails->posType == 2){
            $bills = POSSourceMenuSalesMaster::with(['wareHouseMaster'])->where('shiftID', $input['shiftId']);
        }

        return \DataTables::eloquent($bills)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('shiftID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
  }

    public function fetchItemWacAmount(Request $request)
    {
        DB::beginTransaction();
        try {
            $company_id = $request->get('company_id');
            $third_party_system_id = $request->get('third_party_system_id');

            $items = $this->getItemMasters($company_id, $third_party_system_id);

            $itemData = $items->map(function ($item) use ($company_id) {
                $data = array(
                    'companySystemID' => $company_id,
                    'itemCodeSystem' => $item->id,
                    'wareHouseId' => null
                );

                $inventoryData = Inventory::itemCurrentCostAndQty($data);
                return [
                    'itemAutoID' => $item->id,
                    'companyWacAmount' => $inventoryData['wacValueLocal'],
                ];
            });

            DB::commit();
            return \Response::json([
                "type" => "success",
                "status" => 200,
                "message" => "Data Retreived Sucessfully!",
                "data" => $itemData
            ]);
        } catch (\Exception $exception) {
            DB::rollBack();
            return \Response::json([
                "type" => "error", 
                "status" => 404, 
                "message" => "No records fetched!"
            ]);
        }
    }
}
