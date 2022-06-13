<?php

namespace App\Http\Controllers\API\POS;

use App\Http\Controllers\AppBaseController;
use App\Models\CustomerMasterCategory;
use App\Models\ErpLocation;
use Illuminate\Support\Facades\DB;
use App\Models\SegmentMaster;
use App\Models\ChartOfAccount;

class PosAPIController extends AppBaseController
{
 function pullCustomerCategory(){

     $customerCategories = CustomerMasterCategory::all();
     $customerCategoryArray = array();
     foreach($customerCategories as $item){
         $data = array('id'=>$item->categoryID, 'party_type'=>1, 'description'=>$item->categoryDescription);
         array_push($customerCategoryArray,$data);
     }
     return response($customerCategoryArray,200);
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
            $chartOfAccount = ChartOfAccount::selectRaw('chartOfAccountSystemID As id,AccountCode As system_code')
            ->where('AccountCode','!=','')
            ->get();
    
            DB::commit();
            return $this->sendResponse($chartOfAccount, 'Data Retrieved successfully');
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage());
        }
    }

}
