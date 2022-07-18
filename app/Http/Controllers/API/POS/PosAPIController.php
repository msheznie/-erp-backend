<?php

namespace App\Http\Controllers\API\POS;

use App\Http\Controllers\AppBaseController;
use App\Models\CustomerMasterCategory;
use App\Models\ErpLocation;
use App\Models\ItemMaster;
use Illuminate\Support\Facades\DB;
use App\Models\SegmentMaster;
use App\Models\ChartOfAccount;
use App\Models\Unit;
use App\Models\UnitConversion;
use App\Models\WarehouseMaster;
use App\Models\WarehouseItems;
use App\Models\WarehouseBinLocation;
use Illuminate\Http\Request;
use App\Models\FinanceItemCategorySub;
use App\Models\Employee;
use App\Models\FinanceItemCategoryMaster;
use App\Services\POSService;

class PosAPIController extends AppBaseController
{
    private $POSService = null;

    public function __construct(POSService $POSService)
    {
        $this->POSService = $POSService;
    }

    function pullCustomerCategory(Request $request)
    {

        DB::beginTransaction();
        try {
            $company_id = $request->get('company_id');
            $customerCategories = CustomerMasterCategory::where('companySystemID', '=', $company_id)->get();
            $customerCategoryArray = array();
            foreach ($customerCategories as $item) {
                $data = array('id' => $item->categoryID, 'party_type' => 1, 'description' => $item->categoryDescription);
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
           approvedBySystemID as approvedbyEmpID,approvedBy as approved_user_name,approvedComment as approved_comment')
                ->join('chartofaccountsassigned', 'chartofaccountsassigned.chartOfAccountSystemID', '=', 'chartofaccounts.chartOfAccountSystemID')
                ->where('chartofaccountsassigned.companySystemID', '=', $company_id)
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
            $financeItemCategorySub = FinanceItemCategorySub::selectRaw('financeitemcategorysub.itemCategorySubID As id,financeitemcategorysub.categoryDescription As description,itemCategoryID As master_id ,
            financeitemcategorysub.financeGLcodeRevenueSystemID as revenue_gl,financeitemcategorysub.financeGLcodePLSystemID as cost_gl')
                ->join('financeitemcategorysubassigned', 'financeitemcategorysubassigned.itemCategorySubID', '=', 'financeitemcategorysub.itemCategorySubID')
                ->where('financeitemcategorysub.itemCategorySubID', '!=', '')
                ->where('financeitemcategorysub.categoryDescription', '!=', '')
                ->where('financeitemcategorysubassigned.companySystemID', '=', $company_id)
                ->get();

            DB::commit();
            return $this->sendResponse($financeItemCategorySub, 'Data Retrieved successfully');
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage());
        }
    }

    public function pullItem(Request $request)
    {
        DB::beginTransaction();
        try {

            $company_id = $request->get('company_id');


            $items = ItemMaster::selectRaw('itemmaster.itemCodeSystem as id, primaryCode as system_code, itemmaster.documentID as document_id, 
            itemmaster.secondaryItemCode as secondary_code, "" as image,(case when itemShortDescription = "" or isnull(itemShortDescription) then itemmaster.itemDescription else itemShortDescription end) as name,itemmaster.itemDescription as description,
            itemmaster.financeCategoryMaster as category_id, financeitemcategorymaster.categoryDescription as category_description, itemmaster.financeCategorySub as sub_category_id, "" as sub_sub_category_id, itemmaster.barcode as barcode, financeitemcategorymaster.categoryDescription as finance_category, itemmaster.secondaryItemCode as part_number, unit as unit_id, units.UnitShortCode as unit_description, "" as reorder_point, "" as maximum_qty,
            rev.chartOfAccountSystemID as revenue_gl,rev.AccountDescription as revenue_description,
            cost.chartOfAccountSystemID as cost_gl,cost.AccountDescription as cost_description,"" as asset_gl,"" as asset_description,"" as sales_tax_id, "" as purchase_tax_id,
            vatSubCategory as vat_sub_category_id,itemmaster.isActive as is_active,itemApprovedComment as comment, "" as is_sub_item_exist,"" as is_sub_item_applicable,
            "" as local_currency_id,"" as local_currency,"" as local_exchange_rate,"" as local_selling_price,"" as local_decimal_place,
            "" as reporting_currency_id,"" as reporting_currency,"" as reporting_exchange_rate,"" as reporting_selling_price,"" as reporting_decimal_place,
            "" as is_deleted,"" as deleted_by,"" as deleted_date_time')
                ->join('financeitemcategorymaster', 'financeitemcategorymaster.itemCategoryID', '=', 'itemmaster.financeCategoryMaster')
                ->join('financeitemcategorysub', 'financeitemcategorysub.itemCategorySubID', '=', 'itemmaster.financeCategorySub')
                ->join('units', 'units.UnitID', '=', 'itemmaster.unit')
                ->leftJoin('chartofaccounts as rev', 'rev.chartOfAccountSystemID', '=', 'financeitemcategorysub.financeGLcodeRevenueSystemID')
                ->leftJoin('chartofaccounts as cost', 'cost.chartOfAccountSystemID', '=', 'financeitemcategorysub.financeGLcodePLSystemID')
                ->join('itemassigned', 'itemassigned.itemCodeSystem', '=', 'itemmaster.itemCodeSystem')
                ->where('itemassigned.companySystemID', '=', $company_id)
                ->where('primaryCode', '!=', '')
                ->where('itemmaster.documentID', '!=', '')
                ->where('itemmaster.secondaryItemCode', '!=', '')
                ->where('itemmaster.financeCategoryMaster', '!=', '')
                ->where('itemmaster.financeCategorySub', '!=', '')
                ->where('itemmaster.itemDescription', '!=', '')
                ->where('financeitemcategorymaster.categoryDescription', '!=', '')
                ->where('units.UnitShortCode', '!=', '')
                ->where('itemmaster.financeCategoryMaster', '!=', 3)
                ->get();

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
            $company_id = $request->get('company_id');
            $employee = Employee::selectRaw('employeeSystemID as id,empID as system_code,empName As name,empUserName As user_name,empEmail As email ,
            empActive as is_active')
                ->where('discharegedYN', 0)
                ->where('empCompanySystemID', '=', $company_id)
                ->get();

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

    public function handleRequest(Request $request)
    {
        define('INVOICE', 'INVOICE');

        switch ($request->input('request')) {
            case INVOICE:
                return $this->POSService->posInvoice($request);
            default:
                return [
                    'success'   => false,
                    'message'   => 'Requested API not available, please recheck!',
                    'data'      => null
                ];
        }
        
    }
}
