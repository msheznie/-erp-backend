<?php

namespace App\Http\Controllers\API\OSOS_3_0;
use App\Http\Controllers\AppBaseController;
use App\Jobs\OSOS_3_0\DesignationWebHook;
use App\Jobs\OSOS_3_0\LocationWebHook;
use App\Models\ThirdPartyIntegrationKeys;
use Illuminate\Http\Request;
use App\Traits\OSOS_3_0\JobCommonFunctions;
use Exception;

class JobInvokeAPIController extends AppBaseController
{
    private $thirdParty;
    use JobCommonFunctions;
    function __construct(){

    }
    public function verifyIntegration(){
        $this->thirdParty = ThirdPartyIntegrationKeys::whereHas('thirdPartySystem', function ($query) {
            $query->where('description', 'OSOS_3_O');
        })->first();

        if(empty($this->thirdParty)){
            $msg = 'The third party integration not available';
            $this->insertToLogTb($msg, 'error', 'Location', '');
            throw new Exception($msg);
        }
    }

    public function location(Request $request){

        try {
            $this->verifyIntegration();
            $valResp = $this->commonValidations($request);

            if(!$valResp['status']){
                $this->sendError($valResp['message']);
                $this->insertToLogTb($valResp['message'], 'error', 'Location', $this->thirdParty->company_id);
                return;
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

    public function designation(Request $request){
        try {
            $this->verifyIntegration();
            $valResp = $this->commonValidations($request);

            if(!$valResp['status']){
                $this->sendError($valResp['message']);
                $this->insertToLogTb($valResp['message'], 'error', 'Designation', $this->thirdParty->company_id);
            }

            $postType = $request->postType;
            $ids =is_array($request->designationId) ? $request->designationId : [$request->designationId];
            $db = isset($request->db) ? $request->db : "";

            foreach ($ids as $id) {

                DesignationWebHook::dispatch($db, $postType, $id, $this->thirdParty);

                $msg = "Webhook triggered for Designation ID: $id";
                $this->insertToLogTb($msg, 'info', 'Designation', $this->thirdParty->company_id);
            }

            return $this->sendResponse([], 'OSOS 3.0 success');

        } catch (\Exception $e){
            $error = 'Error Line No: ' . $e->getLine();
            $this->insertToLogTb($error, 'error', 'Designation', $this->thirdParty->company_id);
            return $this->sendError($e->getMessage(),500);
        }
    }

}
