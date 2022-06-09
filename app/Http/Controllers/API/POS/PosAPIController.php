<?php

namespace App\Http\Controllers\API\POS;

use App\Http\Controllers\AppBaseController;
use App\Models\CustomerMasterCategory;
use App\Models\SegmentMaster;
use Illuminate\Support\Facades\DB;

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

}
