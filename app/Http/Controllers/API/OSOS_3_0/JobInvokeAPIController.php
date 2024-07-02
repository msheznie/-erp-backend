<?php

namespace App\Http\Controllers\API\OSOS_3_0;

use App\helper\SME;
use App\Http\Controllers\AppBaseController;
use App\Jobs\OSOS_3_0\LocationWebHook;
use App\Models\ThirdPartyIntegrationKeys;
use Illuminate\Http\Request;
use App\Traits\OSOS_3_0\JobCommonFunctions;

class JobInvokeAPIController extends AppBaseController
{
    private $thirdParty;
    use JobCommonFunctions;
    function __construct(){

        $this->thirdParty = ThirdPartyIntegrationKeys::whereHas('thirdPartySystem', function ($query) {
            $query->where('description', 'OSOS_3_O');
        })->first();

        if(empty($this->thirdParty)){
            $msg = 'The third party integration not available';
            $this->insertToLogTb($msg, 'error', 'Location', '');
            return $this->sendResponse([], $msg);
        }
    }
    public function location(Request $request){

        try {

            $valResp = $this->commonValidations($request);

            if(!$valResp['status']){
                $this->sendError($valResp['message']);
                $this->insertToLogTb($valResp['message'], 'error', 'Location', $this->thirdParty->company_id);
            }

            $postType = $request->postType;
            $id = $request->locationId;

            $db = isset($request->db) ? $request->db : "";

            LocationWebHook::dispatch($db, $postType, $id, $this->thirdParty);

            return $this->sendResponse([], 'OSOS 3.0 success');
        }
        catch(\Exception $e){
            $error = 'Error Line No: ' . $e->getLine();
            $this->insertToLogTb($error, 'error', 'Location', $this->thirdParty->company_id);
            return $this->sendError($e->getMessage(),500);
        }

    }

}
