<?php

namespace App\Services;

use App\Models\SrmTenderBidEmployeeDetails;
use App\Models\SRMTenderUserAccess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Helper\Helper;
use Illuminate\Support\Facades\Log;

class TenderBidEmployeeService
{
    public function storeTenderBidEmployees($input){
        try{
            return DB::transaction(function () use ($input) {
                $insertData = [];

                if(isset($input['rfx'])){
                    $insertData[0] = [
                        'emp_id' => $input['emp_id'],
                        'tender_id' => $input['tender_id']
                    ];
                }

                $empIds = Helper::getArrayIds($input['emp_id']);

                foreach($empIds as $key => $id){
                    $insertData[$key] = [
                        'emp_id' => $id,
                        'tender_id' => $input['tender_id']
                    ];
                }

                SrmTenderBidEmployeeDetails::insert($insertData);
                return ['status' => true, 'message' => 'Employee created successfully', 'code' => 200];
            });
        } catch(\Exception $ex){
            $error_code = ($ex->getCode() == 422)? 422: 500;
            return ['status' => false, 'message' => 'Unable to create: '. $ex->getMessage(), 'code' => $error_code];
        }

    }

    public function addUserAccessEmployees($input){
        try{
            return DB::transaction(function () use ($input) {
                $userId = $input['userId'];
                $moduleId = $input['moduleId'];
                $tenderId = $input['tenderId'];
                $companyId = $input['companyId'];
                $data = [];

                $userIds = Helper::getArrayIds($userId);
                foreach($userIds as $key => $id){
                    $data[$key] = [
                        'tender_id'=> $tenderId,
                        'user_id'=> $id,
                        'module_id'=> $moduleId,
                        'company_id'=>$companyId
                    ];
                }

                SRMTenderUserAccess::insert($data);
                return ['status' => true, 'message' => 'Employee added successfully', 'code' => 200];
            });
        } catch(\Exception $ex){
            $error_code = ($ex->getCode() == 422)? 422: 500;
            return ['status' => false, 'message' => 'Unable to add: '. $ex->getMessage(), 'code' => $error_code];
        }
    }

    public function deleteAllBidMinimumApprovalDetails(Request $request){
        try
        {
            return DB::transaction(function () use ($request) {
                $tenderID = $request->input('tenderID') ?? 0;
                $tenderBidDetails = SrmTenderBidEmployeeDetails::getTenderBidEmployees($tenderID);
                if ($tenderBidDetails->isEmpty()) {
                    return ['status' => false, 'message' => 'Employees not found', 'code' => 404];
                }

                $tenderBidDetails->each->delete();
                return ['status' => true, 'message' => 'Successfully deleted', 'code' => 200];
            });
        } catch (\Exception $ex){
            $error_code = ($ex->getCode() == 422)? 422: 500;
            return ['status' => false, 'message' => 'Unable to delete: '. $ex->getMessage(), 'code' => $error_code];
        }
    }
    public function deleteAllTenderUserAccess(Request $request)
    {
        try
        {
            return DB::transaction(function () use ($request) {
                $tenderID = $request->input('tenderID') ?? 0;
                $companyID = $request->input('companyID') ?? 0;
                $moduleID = $request->input('moduleID') ?? 0;

                $getUsers = SRMTenderUserAccess::getTenderUserAccessByModule($tenderID, $companyID, $moduleID);
                if ($getUsers->isEmpty()) {
                    return ['status' => false, 'message' => 'Employees not found', 'code' => 404];
                }
                $getUsers->each->delete();
                return ['status' => true, 'message' => 'Successfully deleted', 'code' => 200];
            });
        } catch (\Exception $ex){
            $error_code = ($ex->getCode() == 422)? 422: 500;
            return ['status' => false, 'message' => 'Unable to delete: '. $ex->getMessage(), 'code' => $error_code];
        }
    }
}
