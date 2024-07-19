<?php

namespace App\Http\Controllers\API\HelpDesk;

use App\Http\Controllers\AppBaseController;
use App\Jobs\OSOS_3_0\EmployeeWebHook;
use App\Jobs\UserWebHook;
use App\Models\ThirdPartyIntegrationKeys;
use App\Traits\OSOS_3_0\JobCommonFunctions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class HelpDeskAPIController extends AppBaseController
{
    private $thirdParty;
    use JobCommonFunctions;
        public function postEmployee(Request $request){
            try{
                $this->employeeOSOS_3_0($request);
            } catch(\Exception $e) {
                $error = $e->getMessage().' Error Line No: ' . $e->getLine();
                $comId = isset($this->thirdParty['company_id'])? $this->thirdParty['company_id'] : 0;
                $this->insertToLogTb($error, 'error', 'Employee', $comId);
            }

            DB::beginTransaction();
            try {
                $ids = is_array($request->employeeSystemID)? $request->employeeSystemID :
                    [$request->employeeSystemID];
                $db = isset($request->db) ? $request->db : "";
                $thirdParty = ThirdPartyIntegrationKeys::where('third_party_system_id', 5)->first();
                DB::commit();

                if(!empty($thirdParty)){
                    foreach ($ids as $id) {
                        UserWebHook::dispatch($db, $id, $thirdParty->api_external_key, $thirdParty->api_external_url);
                    }
                } else {
                    return $this->sendResponse([], 'There is no third party integration');
                }
              
                return $this->sendResponse($thirdParty, 'Help Desk Info');
            }
            catch(\Exception $e){
                DB::rollback();
                Log::info('Error Line No: ' . $e->getLine());
                Log::info('Error File: ' . $e->getFile());
                Log::info($e->getMessage());
                Log::info('---- GL  End with Error-----' . date('H:i:s'));
                return $this->sendError($e->getMessage(),500);
            }

        }

    public function employeeOSOS_3_0($request){
        $this->verifyIntegration();
        $valResp = $this->commonValidations($request);
        if(!$valResp['status']){
            $error = $valResp['message'];
            $this->insertToLogTb($error, 'error', 'Employee', $this->thirdParty['company_id']);
        }

        $postType = $request->postType;
        $ids = is_array($request->employeeSystemID)? $request->employeeSystemID : [$request->employeeSystemID];
        $db = isset($request->db) ? $request->db : "";

        foreach ($ids as $id) {
            EmployeeWebHook::dispatch($db, $postType, $id, $this->thirdParty);
        }

        return $this->sendResponse([], 'OSOS 3.0 employee triggered');
    }

    public function verifyIntegration(){
        $data = ThirdPartyIntegrationKeys::whereHas('thirdPartySystem', function ($query) {
            $query->where('description', 'OSOS_3_O');
        })->first();

        if(empty($data)){
            $msg = 'The third party integration not available';
            throw new Exception($msg);
        }

        $this->thirdParty = $data->toArray();
    }
}
