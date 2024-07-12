<?php

namespace App\Http\Controllers\API\OSOS_3_0;
use App\Http\Controllers\AppBaseController;
use App\Jobs\OSOS_3_0\DepartmentWebHook;
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
        $data = ThirdPartyIntegrationKeys::whereHas('thirdPartySystem', function ($query) {
            $query->where('description', 'OSOS_3_O');
        })->first();

        if(empty($data)){
            $msg = 'The third party integration not available';
            $this->insertToLogTb($msg, 'error', '', '');
            throw new Exception($msg);
        }

        $this->thirdParty = $data->toArray();
    }

    public function location(Request $request){

        try {
            $this->verifyIntegration();
            $valResp = $this->commonValidations($request);

            if (!$valResp['status']) {
                $this->sendError($valResp['message']);
                $this->insertToLogTb($valResp['message'], 'error', 'Location', $this->thirdParty['company_id']);
                return;
            }

            $postType = $request->postType;
            $id = $request->locationId;

            $db = isset($request->db) ? $request->db : "";

            LocationWebHook::dispatch($db, $postType, $id, $this->thirdParty);

            return $this->sendResponse([], 'OSOS 3.0 location triggered success');

        } catch (\Exception $e) {
            $error = 'Error Line No: ' . $e->getLine();
            $this->insertToLogTb($error, 'error', 'Location', $this->thirdParty['company_id']);
            return $this->sendError($e->getMessage(), 500);
        }
    }

    public function designation(Request $request){
        try {
            $this->verifyIntegration();
            $valResp = $this->commonValidations($request);

            if(!$valResp['status']){
                $this->sendError($valResp['message']);
                $this->insertToLogTb($valResp['message'], 'error', 'Designation', $this->thirdParty['company_id']);
            }

            $postType = $request->postType;
            $ids =is_array($request->designationId) ? $request->designationId : [$request->designationId];
            $db = isset($request->db) ? $request->db : "";

            foreach ($ids as $id) {
                DesignationWebHook::dispatch($db, $postType, $id, $this->thirdParty);
            }

            return $this->sendResponse([], 'OSOS 3.0 | Designation | triggered success');

        } catch (\Exception $e){
            $error = 'Error Line No: ' . $e->getLine();
            $this->insertToLogTb($error, 'error', 'Designation', $this->thirdParty['company_id']);
            return $this->sendError($e->getMessage(),500);
        }
    }

    public function department(Request $request){
        try {
            $this->verifyIntegration();
            $valResp = $this->commonValidations($request);
            if(!$valResp['status']){
                $error = $valResp['message'];
                $this->insertToLogTb($error, 'error', 'Department', $this->thirdParty['company_id']);
                $this->sendError($error);
            }

            $postType = $request->postType;
            $id = $request->departmentId;
            $db = isset($request->db) ? $request->db : "";
            DepartmentWebHook::dispatch($db, $postType, $id, $this->thirdParty);

            return $this->sendResponse([], 'OSOS 3.0 department triggered');
        } catch(\Exception $e) {
            $error = 'Error Line No: ' . $e->getLine();
            $this->insertToLogTb($error, 'error', 'Department', $this->thirdParty['company_id']);

            return $this->sendError($e->getMessage(),500);
        }
    }
}
