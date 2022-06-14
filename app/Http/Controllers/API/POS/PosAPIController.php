<?php

namespace App\Http\Controllers\API\POS;

use App\Http\Controllers\AppBaseController;
use App\Models\CustomerMasterCategory;
use App\Models\ErpLocation;
use Illuminate\Support\Facades\DB;
use App\Models\SegmentMaster;
use App\Models\Unit;
use App\Models\UnitConversion;

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

}
