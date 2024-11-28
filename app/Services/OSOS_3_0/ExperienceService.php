<?php
namespace App\Services\OSOS_3_0;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;
use App\Traits\OSOS_3_0\JobCommonFunctions;

class ExperienceService{
    protected $apiExternalKey;
    protected $apiExternalUrl;
    protected $experienceData = [];
    protected $thirdPartyData;
    protected $id;
    protected $postType;
    protected $url;
    protected $detailId;
    protected $apiKey;
    protected $dataBase;

    protected $companyId;
    protected $operation;
    protected $pivotTableId;
    protected $masterUuId;
    protected $empRefId;

    use JobCommonFunctions;

    public function __construct($dataBase, $id, $postType, $thirdPartyData){

        $this->dataBase = $dataBase;
        $this->postType = trim(strtoupper($postType),'"');
        $this->id = $id;
        $this->detailId = $thirdPartyData['id'];
        $this->apiKey = $thirdPartyData['api_key'];
        $this->apiExternalKey = $thirdPartyData['api_external_key'];
        $this->apiExternalUrl = $thirdPartyData['api_external_url'];
        $this->companyId = $thirdPartyData['company_id'];
        $this->thirdPartyData = $thirdPartyData;

        $this->getOperation();
        $this->getPivotTableId(9);
        $this->getExperienceData();
        $this->getUrl('experience');
    }

    function execute(){
        try {

            $valResp =$this->validateApiResponse();

            if(!$valResp['status']){
                $logData = ['message' => $valResp['message'], 'id' => $this->id];
                return $this->insertToLogTb($logData, 'error', 'Experience', $this->companyId);
            }

            $expName = ($this->postType != 'DELETE')? ' - '. $this->experienceData['title'] : '';
            $msg = "Experience about to trigger: " . $this->id . $expName;

            $logData = ['message' => $msg, 'id' => $this->id ];
            $this->insertToLogTb($logData, 'info', 'Experience', $this->companyId);

            $client = new Client();
            $headers = [
                'content-type' => 'application/json',
                'auth-key' =>  $this->apiExternalKey,
                'menu-id' =>  'defualt'
            ];

            $res = $client->request("$this->postType", $this->apiExternalUrl . $this->url, [
                'headers' => $headers,
                'body' => json_encode($this->experienceData)
            ]);

            $statusCode = $res->getStatusCode();
            $body = $res->getBody()->getContents();

            if (in_array($statusCode, [200, 201])) {

                $je = json_decode($body, true);

                if(!isset($je['id'])){
                    $logData = ['message' => 'Cannot Find Reference id from response', 'id' => $this->id ];
                    return $this->insertToLogTb($logData, 'error', 'Experience', $this->companyId);
                }

                $this->insertOrUpdateThirdPartyPivotTable($je['id']);
                $logData = [
                    'message' => "Api experience {$this->operation} successfully processed",
                    'id' => $this->id
                ];
                $this->insertToLogTb($logData, 'info', 'Experience', $this->companyId);
                return ['status' => true, 'message' => $logData['message'], 'code' => $statusCode];

            }

            if ($statusCode == 400) {
                $msg = $res->getBody();
                $logData = ['message' => json_decode($msg), 'id' => $this->id ];
                return $this->capture400Err($logData, 'Experience');
            }
        } catch (\Exception $e) {

            $exStatusCode = $e->getCode();
            if ($exStatusCode == 400) {
                $msg = $e->getMessage();
                $logData = ['message' => $msg, 'id' => $this->id ];
                return $this->capture400Err($logData, 'Experience');
            }

            $msg = "Exception \n";
            $msg .= "operation : ".$this->operation."\n";;
            $msg .= "message : ".$e->getMessage()."\n";;
            $msg .= "file : ".$e->getFile()."\n";;
            $msg .= "line no : ".$e->getLine()."\n";;

            $logData = ['message' =>  $msg, 'id' => $this->id];
            $this->insertToLogTb($logData, 'error', 'Experience', $this->companyId);

            return ['status' => false, 'message' => $msg, 'code' => $exStatusCode];
        }
    }

    function validateApiResponse(){

        if(empty($this->id)){
            $error = 'Experience id is required';
            return ['status' =>false, 'message'=> $error];
        }

        if(empty($this->pivotTableId)){
            $error = 'Pivot table reference not found check pivot_tbl_reference.id';
            return ['status' =>false, 'message'=> $error];
        }

        if (empty($this->experienceData) && $this->postType != 'DELETE') {
            $error = 'Experience details not found';
            return ['status' =>false, 'message'=> $error];
        }

        if(empty($this->experienceData['id']) && $this->postType != 'POST'){
            $error = 'Reference id not found';
            return ['status' =>false, 'message'=> $error];
        }

        if(empty($this->empRefId) && $this->postType != 'DELETE'){
            $error = 'Employee reference id not found';
            return ['status' =>false, 'message'=> $error];
        }

        if(empty($this->validateCompanyReference())){
            $error = 'Company reference not found';
            return ['status' =>false, 'message'=> $error];
        }

        return ['status' =>true, 'message'=> 'success'];
    }

    function getExperienceData()
    {
        $data = DB::table('hr_emp_experience')
            ->selectRaw("emp_id as empId, job_title as title, current as isCurrent, company_name as companyName, 
            CONCAT(date_from, 'T18:30:00.000Z') as dateFrom, CONCAT(date_to, 'T18:30:00.000Z') as dateTo")
            ->where('id', $this->id)
            ->first();

        if($this->postType != "POST") {
            $this->getReferenceId();
            $this->experienceData['id'] = $this->masterUuId;
        }

        if(empty($data)){
            return;
        }

        $isCurrent = $data->isCurrent == 1;
        $this->getEmployeeReferenceId($data->empId);
        $this->experienceData = array_merge([
            "title" => $data->title,
            "companyName" => $data->companyName,
            "fromDate" => $data->dateFrom,
            "toDate" => $data->dateTo,
            "isCurrent" => $isCurrent,
            "employeeId" => $this->empRefId,
            "status" => 1
        ], $this->experienceData);
    }

    function getEmployeeReferenceId($empId)
    {
        $this->empRefId = DB::table('third_party_pivot_record')
            ->where('pivot_table_id', 4)
            ->where('system_id', $empId)
            ->where('third_party_sys_det_id', $this->detailId)
            ->value('reference_id');
    }
}
