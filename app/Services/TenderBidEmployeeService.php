<?php

namespace App\Services;

use App\Http\Controllers\API\TenderBidEmployeeDetails;
use App\Models\SrmTenderBidEmployeeDetails;
use App\Models\SrmTenderBidEmployeeDetailsEditLog;
use App\Models\SRMTenderUserAccess;
use App\Models\SrmTenderUserAccessEditLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Helper\Helper;
use Illuminate\Support\Facades\Log;
use App\Services\SrmDocumentModifyService;

class TenderBidEmployeeService
{
    protected $documentModifyService;
    public function __construct(SrmDocumentModifyService $documentModifyService)
    {
        $this->documentModifyService = $documentModifyService;
    }

    public function storeTenderBidEmployees($input){
        try{
            return DB::transaction(function () use ($input) {
                $insertData = [];
                $tenderID = $input['tender_id'] ?? 0;
                $enableChangeRequest = $input['enableChangeRequest'] ?? false;
                $versionID = $input['versionID'] ?? 0;

                if(isset($input['rfx'])){
                    $insertData[0] = [
                        'emp_id' => $input['emp_id'],
                        'tender_id' => $tenderID
                    ];
                    if($enableChangeRequest){
                        $insertData[0]['tender_edit_version_id'] = $versionID;
                        $insertData[0]['level_no'] = 1;
                        $insertData[0]['id'] = null;
                        $insertData[0]['created_at'] = Carbon::now();
                    }
                }

                $empIds = Helper::getArrayIds($input['emp_id']);

                foreach($empIds as $key => $id){
                    $insertData[$key] = [
                        'emp_id' => $id,
                        'tender_id' => $tenderID
                    ];
                    if($enableChangeRequest){
                        $insertData[$key]['tender_edit_version_id'] = $versionID;
                        $insertData[$key]['level_no'] = 1;
                        $insertData[$key]['id'] = null;
                        $insertData[$key]['created_at'] = Carbon::now();
                    }
                }
                $enableChangeRequest ?
                    SrmTenderBidEmployeeDetailsEditLog::insert($insertData) :
                    SrmTenderBidEmployeeDetails::insert($insertData);

                return ['success' => true, 'message' => 'Employee created successfully', 'code' => 200];
            });
        } catch(\Exception $ex){
            $error_code = ($ex->getCode() == 422)? 422: 500;
            return ['success' => false, 'message' => 'Unable to create: '. $ex->getMessage(), 'code' => $error_code];
        }

    }

    public function addUserAccessEmployees($input){
        try{
            return DB::transaction(function () use ($input) {
                $userId = $input['userId'];
                $moduleId = $input['moduleId'];
                $tenderId = $input['tenderId'];
                $companyId = $input['companyId'];
                $requestData = $this->documentModifyService->checkForEditOrAmendRequest($tenderId);
                $data = [];

                $userIds = Helper::getArrayIds($userId);
                foreach($userIds as $key => $id){
                    $data[$key] = [
                        'tender_id'=> $tenderId,
                        'user_id'=> $id,
                        'module_id'=> $moduleId,
                        'company_id'=>$companyId,
                        'created_at'=> Carbon::now()
                    ];
                    if($requestData['enableRequestChange']){
                        $data[$key]['version_id'] = $requestData['versionID'];
                        $data[$key]['level_no'] = 1;
                        $data[$key]['id'] = null;
                    }
                }
                $requestData['enableRequestChange'] ?
                    SrmTenderUserAccessEditLog::insert($data) :
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
                $requestData = $this->documentModifyService->checkForEditOrAmendRequest($tenderID);
                if($requestData['enableRequestChange']){
                    $versionID = $requestData['versionID'];
                    SrmTenderBidEmployeeDetailsEditLog::where('tender_edit_version_id', $versionID)
                        ->where('tender_id', $tenderID)->update(['is_deleted' => 1]);
                } else {
                    $tenderBidDetails = SrmTenderBidEmployeeDetails::getTenderBidEmployees($tenderID);
                    if ($tenderBidDetails->isEmpty()) {
                        return ['status' => false, 'message' => 'Employees not found', 'code' => 404];
                    }
                    $tenderBidDetails->each->delete();
                }
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
                $requestData = $this->documentModifyService->checkForEditOrAmendRequest($tenderID);

                if($requestData['enableRequestChange']){
                    $versionID = $requestData['versionID'];
                    SrmTenderUserAccessEditLog::where('version_id', $versionID)
                        ->where('tender_id', $tenderID)->where('module_id', $moduleID)->update(['is_deleted' => 1]);
                } else {
                    $getUsers = SRMTenderUserAccess::getTenderUserAccessByModule($tenderID, $companyID, $moduleID);
                    if ($getUsers->isEmpty()) {
                        return ['status' => false, 'message' => 'Employees not found', 'code' => 404];
                    }
                    $getUsers->each->delete();
                }
                return ['status' => true, 'message' => 'Successfully deleted', 'code' => 200];
            });
        } catch (\Exception $ex){
            $error_code = ($ex->getCode() == 422)? 422: 500;
            return ['status' => false, 'message' => 'Unable to delete: '. $ex->getMessage(), 'code' => $error_code];
        }
    }

    public function getEmployees(Request $request){
        $isFromTenderEdit = $request->input('isFromTenderEdit') ?? false;
        $requestData = $this->documentModifyService->checkForEditOrAmendRequest($request['tender_id']);
        return $isFromTenderEdit && $requestData['enableRequestChange'] ?
            SrmTenderBidEmployeeDetailsEditLog::getTenderBidEmployees($request['tender_id'], $requestData['versionID']) :
            SrmTenderBidEmployeeDetails::getTenderBidEmployeesEdit($request['tender_id']);

    }
    public function deleteTenderBidEmployees(Request $request){
        return DB::transaction(function () use ($request){
            $requestData = $this->documentModifyService->checkForEditOrAmendRequest($request['tender_id']);
            if($requestData['enableRequestChange']){
                $versionID = $requestData['versionID'] ?? 0;
                SrmTenderBidEmployeeDetailsEditLog::where('tender_edit_version_id', $versionID)
                    ->where('tender_id', $request['tender_id'])
                    ->where('emp_id',$request['emp_id'])
                    ->update(['is_deleted' => 1]);
            }
            else {
                $tenderEmployeeDetails = SrmTenderBidEmployeeDetails::getTenderBidEmployee($request['tender_id'], $request['emp_id']);
                $result = SrmTenderBidEmployeeDetails::find($tenderEmployeeDetails->id);

                if (empty($result)) {
                    return ['success' => false, 'message' => 'Employee Details not found'];
                }
                $result->delete();
            }
            return ['success' => true, 'message' => 'Employee deleted successfully'];
        });
    }
    public function removeTenderUserAccess($input){
        try{
            return DB::transaction(function () use ($input){
                $id = $input['id'];
                $tenderID = $input['tender_id'];
                $module_id = $input['module_id'];

                $requestData = $this->documentModifyService->checkForEditOrAmendRequest($tenderID);
                $userID = $input['user_id'];
                if($requestData['enableRequestChange']){
                    $versionID = $requestData['versionID'] ?? 0;
                    SrmTenderUserAccessEditLog::where('tender_id', $tenderID)
                        ->where('user_id', $userID)
                        ->where('module_id', $module_id)
                        ->where('version_id', $versionID)
                        ->update(['is_deleted' => 1]);
                } else {
                    $tenderUser = SRMTenderUserAccess::find($id);
                    if (empty($tenderUser)){
                        return ['success' => false, 'message' => 'User access details not found'];
                    }
                    $tenderUser->delete();
                }
                return ['success' => true, 'message' => 'Employee deleted successfully'];
            });
        } catch(\Exception $exception){
            return ['success' => false, 'message' => 'Unexpected Error: ' . $exception->getMessage()];
        }

    }
}
