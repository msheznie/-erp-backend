<?php

namespace App\Http\Controllers\API\POS;

use App\Http\Controllers\AppBaseController;
use App\Models\CustomerMasterCategory;
use App\Models\ErpLocation;
use App\Models\ItemMaster;
use Illuminate\Support\Facades\DB;
use App\Models\SegmentMaster;
use App\Models\Unit;
use App\Models\UnitConversion;
use App\Models\WarehouseMaster;
use App\Models\WarehouseItems;
use App\Models\WarehouseBinLocation;

class PosAPIController extends AppBaseController
{
 function pullCustomerCategory(){

     DB::beginTransaction();
     try {
     $customerCategories = CustomerMasterCategory::all();
     $customerCategoryArray = array();
     foreach($customerCategories as $item){
         $data = array('id'=>$item->categoryID, 'party_type'=>1, 'description'=>$item->categoryDescription);
         array_push($customerCategoryArray,$data);
     }

         DB::commit();
         return $this->sendResponse($customerCategoryArray, 'Data Retrieved successfully');
     } catch (\Exception $exception) {
         DB::rollBack();
         return $this->sendError($exception->getMessage());
     }
 }

   public function pullLocation()
   {
        DB::beginTransaction();
        try {
            $location = ErpLocation::selectRaw('locationID as id,locationName as description')
            ->where('locationName','!=','')
            ->get();
             DB::commit();
            return $this->sendResponse($location, 'Data Retrieved successfully');
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage());
        }
   }
    public function pullSegment()
    {
        DB::beginTransaction();
        try {
            $segments = SegmentMaster::selectRaw('serviceLineSystemID As id,ServiceLineCode As segment_code ,ServiceLineDes as description,isActive as status')
            ->where('ServiceLineCode','!=','')
            ->where('ServiceLineDes','!=','')
            ->get();
            DB::commit();
            return $this->sendResponse($segments, 'Data Retrieved successfully');
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage());
        }

    }

    public function pullUnitOfMeasure()
    {
        DB::beginTransaction();
        try {
            $units = Unit::selectRaw('UnitID As id,UnitShortCode As short_code ,UnitDes as description')
            ->where('UnitShortCode','!=','')
            ->get();
            DB::commit();
            return $this->sendResponse($units, 'Data Retrieved successfully');
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage());
        }
    }

    public function pullUnitConversion()
    {
        DB::beginTransaction();
        try {
            $unitConvertion = UnitConversion::selectRaw('unitsConversionAutoID As id,masterUnitID As master_id ,subUnitID as sub_id,conversion as conversion')
            ->where('masterUnitID','!=','')
            ->where('subUnitID','!=','')
            ->where('conversion','!=','')
            ->get();
            DB::commit();
            return $this->sendResponse($unitConvertion, 'Data Retrieved successfully');
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage());
        }
    }


    
    public function pullWarehouse()
    {
        DB::beginTransaction();
        try {
            $warehouse = WarehouseMaster::selectRaw('wareHouseSystemCode As id,wareHouseCode As system_code ,wareHouseDescription as description,wareHouseLocation as location_id,
                erp_location.locationName as location,isPosLocation as is_pos_location, isDefault as is_default ,warehouseType as warehouse_type,WIPGLCode as gl_id,"" as address,
                "" as phone_number,isActive as is_active,"" as warehouse_image,
                "" as footer_note')
            ->join('erp_location', 'erp_location.locationID', '=', 'warehousemaster.wareHouseLocation')
            ->where('wareHouseCode','!=','')
            ->where('wareHouseDescription','!=','')
            ->where('wareHouseLocation','!=','')
            ->get();

            DB::commit();
            return $this->sendResponse($warehouse, 'Data Retrieved successfully');
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage());
        }
    }

    public function pullWarehouseItem()
    {
        DB::beginTransaction();
        try {
            $warehouseItems = WarehouseItems::selectRaw('warehouseItemsID As id,warehouseitems.warehouseSystemCode As warehouse_id ,
             erp_location.locationName as location,erp_location.locationName as description,itemmaster.itemCodeSystem as item_id,itemmaster.primaryCode as item_code,
             itemmaster.itemDescription as item_description,"" as is_active,"" as sales_price,unitOfMeasure as unit_id')
            ->join('warehousemaster', 'warehousemaster.warehouseSystemCode', '=', 'warehouseitems.warehouseSystemCode')
            ->join('erp_location', 'erp_location.locationID', '=', 'warehousemaster.wareHouseLocation')
            ->join('itemmaster', 'itemmaster.itemCodeSystem', '=', 'warehouseitems.itemSystemCode')
            ->get();

            DB::commit();
            return $this->sendResponse($warehouseItems, 'Data Retrieved successfully');
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage());
        }
    }

    public function pullWarehouseBinLocation()
    {
        DB::beginTransaction();
        try {
            $warehousebin = WarehouseBinLocation::selectRaw('binLocationID As binLocationID,warehousemaster.wareHouseSystemCode As warehouseAutoID,binLocationDes As Description')
            ->join('warehousemaster', 'warehousemaster.warehouseSystemCode', '=', 'warehousebinlocationmaster.warehouseSystemCode')
            ->get();

            DB::commit();
            return $this->sendResponse($warehousebin, 'Data Retrieved successfully');
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage());
        }
    }

    public function pullItem(){
        DB::beginTransaction();
        try {
            $items = ItemMaster::selectRaw('itemCodeSystem as id, primaryCode as system_code, itemmaster.documentID as document_id, 
             secondaryItemCode as secondary_code, "" as image, itemShortDescription as name, itemDescription as description,
            financeCategoryMaster as category_id, "" as category_description, "" as sub_category_id, "" as sub_sub_category_id, barcode as barcode, financeitemcategorymaster.categoryDescription as finance_category, secondaryItemCode as part_number, unit as unit_id, units.UnitShortCode as unit_description, "" as reorder_point, "" as maximum_qty,
            rev.AccountCode as revenue_gl,rev.AccountDescription as revenue_description,
            cost.AccountCode as cost_gl,cost.AccountDescription as cost_description,"" as asset_gl,"" as asset_description,"" as sales_tax_id, "" as purchase_tax_id,
            vatSubCategory as vat_sub_category_id,itemmaster.isActive as active,itemApprovedComment as comment, "" as is_sub_item_exist,"" as is_sub_item_applicable,
            "" as local_currency_id,"" as local_currency,"" as local_exchange_rate,"" as local_selling_price,"" as local_decimal_place,
            "" as reporting_currency_id,"" as reporting_currency,"" as reporting_exchange_rate,"" as reporting_selling_price,"" as reporting_decimal_place,
            "" as is_deleted,"" as deleted_by,"" as deleted_date_time')
                ->join('financeitemcategorymaster', 'financeitemcategorymaster.itemCategoryID', '=', 'itemmaster.financeCategoryMaster')
                ->join('financeitemcategorysub', 'financeitemcategorysub.itemCategorySubID', '=', 'itemmaster.financeCategorySub')
                ->join('units', 'units.UnitID', '=', 'itemmaster.unit')
                ->join('chartofaccounts as rev', 'rev.chartOfAccountSystemID', '=', 'financeitemcategorysub.financeGLcodeRevenueSystemID')
                ->join('chartofaccounts as cost', 'cost.chartOfAccountSystemID', '=', 'financeitemcategorysub.financeGLcodePLSystemID')
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

}
