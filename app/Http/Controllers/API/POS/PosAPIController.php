<?php

namespace App\Http\Controllers\API\POS;

use App\Http\Controllers\AppBaseController;
use App\Models\CustomerMasterCategory;
use App\Models\ErpLocation;
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

class PosAPIController extends AppBaseController
{
 function pullCustomerCategory(Request $request){

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

   public function pullChartOfAccount()
   {
       DB::beginTransaction();
       try {
           $chartOfAccount = ChartOfAccount::selectRaw('chartOfAccountSystemID As id,AccountCode As system_code,AccountCode As secondary_code,AccountDescription as description,
           isMasterAccount as is_master_account,masterAccount as master_account_id , "" as master_system_code,catogaryBLorPL as master_category,
           "" as category_id,"" as category_description,"" as sub_category,controllAccountYN as is_control_account,isActive as is_active,"" as default_type,
           "" as is_auto,"" as is_card,isBank as is_bank,"" as is_cash,"" as is_default_bank,"" as bank_name,"" as bank_branch,"" as bank_short_code,"" as bank_swift_code,"" as bank_cheque_number,
           "" as bank_account_number, "" as bank_currency_id,"" as bank_currency_code, "" as bank_currency_decimal,"" as is_deleted,"" as deleted_userID,"" as deleted_dateTime,
           confirmedYN as confirmedYN,"" as confirmedDate,confirmedEmpID as confirmedbyEmpID,confirmedEmpName as confirmedbyName,isApproved as approvedYN,approvedDate as approvedDate,
           approvedBySystemID as approvedbyEmpID,approvedBy as approvedbyEmpName,approvedComment as approvedComment')
           ->where('AccountCode','!=','')
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
            ->where('UnitShortCode','!=','')
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


    
    public function pullWarehouse(Request $request)
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

    public function pullWarehouseItem(Request $request)
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

    public function pullWarehouseBinLocation(Request $request)
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

    
    public function pullItemSubCategory(Request $request)
    {
        DB::beginTransaction();
        try {
            $financeItemCategorySub = FinanceItemCategorySub::selectRaw('itemCategorySubID As id,categoryDescription As description,itemCategoryID As master_id ,
            financeGLcodeRevenue as revenue_gl,financeGLcodePL as cost_gl')
            ->get();

            DB::commit();
            return $this->sendResponse($financeItemCategorySub, 'Data Retrieved successfully');
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage());
        }
    }

}
