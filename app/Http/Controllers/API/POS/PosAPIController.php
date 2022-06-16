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

}
