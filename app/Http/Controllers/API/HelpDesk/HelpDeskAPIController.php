<?php

namespace App\Http\Controllers\API\HelpDesk;

use App\Http\Controllers\AppBaseController;
use App\Jobs\UserWebHook;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class HelpDeskAPIController extends AppBaseController
{
        public function postCustomers(Request $request){
            DB::beginTransaction();
            try {
                $empID = $request->employeeSystemID;
                $db = isset($request->db) ? $request->db : "";

                UserWebHook::dispatch($db, $empID, $request->api_external_key, $request->api_external_url);

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
