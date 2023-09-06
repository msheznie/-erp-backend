<?php

namespace App\Http\Controllers\API\HelpDesk;

use App\Http\Controllers\AppBaseController;
use App\Jobs\UserWebHook;
use App\Models\ThirdPartyIntegrationKeys;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class HelpDeskAPIController extends AppBaseController
{
        public function postEmployee(Request $request){
            DB::beginTransaction();
            try {
                $empID = $request->employeeSystemID;
                $db = isset($request->db) ? $request->db : "";
                $thirdParty = ThirdPartyIntegrationKeys::where('third_party_system_id', 5)->first();
                DB::commit();

                if(!empty($thirdParty)){
                    UserWebHook::dispatch($db, $empID, $thirdParty->api_external_key, $thirdParty->api_external_url);
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
}
