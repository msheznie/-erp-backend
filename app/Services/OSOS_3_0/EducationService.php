<?php
namespace App\Services\OSOS_3_0;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;
use App\Traits\OSOS_3_0\JobCommonFunctions;

class EducationService{
    protected $apiExternalKey;
    protected $apiExternalUrl;
    protected $educationData = [];
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
    protected $fieldOfStudyId;

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
        $this->getPivotTableId(7);
        $this->getEducationData();
        $this->getUrl('education');
    }

    function execute(){
        try {

            $valResp =$this->validateApiResponse();

            if(!$valResp['status']){
                $logData = ['message' => $valResp['message'], 'id' => $this->id];
                return $this->insertToLogTb($logData, 'error', 'Education', $this->companyId);
            }

            $eduName = ($this->postType != 'DELETE')? 'Degree Id - '. $this->educationData['degree'] : '';
            $msg = "Education about to trigger: " . $this->id . ' ' . $eduName;

            $logData = ['message' => $msg, 'id' => $this->id ];
            $this->insertToLogTb($logData, 'info', 'Education', $this->companyId);

            $client = new Client();
            $headers = [
                'content-type' => 'application/json',
                'auth-key' =>  $this->apiExternalKey,
                'menu-id' =>  'defualt'
            ];

            $res = $client->request("$this->postType", $this->apiExternalUrl . $this->url, [
                'headers' => $headers,
                'body' => json_encode($this->educationData)
            ]);

            $statusCode = $res->getStatusCode();
            $body = $res->getBody()->getContents();

            if (in_array($statusCode, [200, 201])) {

                $je = json_decode($body, true);

                if(!isset($je['id'])){
                    $logData = ['message' => 'Cannot Find Reference id from response', 'id' => $this->id ];
                    return $this->insertToLogTb($logData, 'error', 'Education', $this->companyId);
                }

                $this->insertOrUpdateThirdPartyPivotTable($je['id']);
                $logData = [
                    'message' => "Api education {$this->operation} successfully processed",
                    'id' => $this->id
                ];
                $this->insertToLogTb($logData, 'info', 'Education', $this->companyId);
                return ['status' => true, 'message' => $logData['message'], 'code' => $statusCode];

            }

            if ($statusCode == 400) {
                $msg = $res->getBody();
                $logData = ['message' => json_decode($msg), 'id' => $this->id ];
                return $this->capture400Err($logData, 'Education');
            }
        } catch (\Exception $e) {

            $exStatusCode = $e->getCode();
            if ($exStatusCode == 400) {
                $msg = $e->getMessage();
                $logData = ['message' => $msg, 'id' => $this->id ];
                return $this->capture400Err($logData, 'Education');
            }

            $msg = "Exception \n";
            $msg .= "operation : ".$this->operation."\n";;
            $msg .= "message : ".$e->getMessage()."\n";;
            $msg .= "file : ".$e->getFile()."\n";;
            $msg .= "line no : ".$e->getLine()."\n";;

            $logData = ['message' =>  $msg, 'id' => $this->id];
            $this->insertToLogTb($logData, 'error', 'Education', $this->companyId);

            return ['status' => false, 'message' => $msg, 'code' => $exStatusCode];
        }
    }

    function validateApiResponse(){

        if(empty($this->id)){
            $error = 'Education id is required';
            return ['status' =>false, 'message'=> $error];
        }

        if(empty($this->pivotTableId)){
            $error = 'Pivot table reference not found check pivot_tbl_reference.id';
            return ['status' =>false, 'message'=> $error];
        }

        if (empty($this->educationData) && $this->postType != 'DELETE') {
            $error = 'Education details not found';
            return ['status' =>false, 'message'=> $error];
        }

        if (empty($this->fieldOfStudyId) && !empty($data->fieldOfStudyId) && $this->postType != 'DELETE') {
            $error = 'Field of study reference id not found';
            return ['status' =>false, 'message'=> $error];
        }

        if(empty($this->educationData['id']) && $this->postType != 'POST'){
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

    function getEducationData()
    {
        $data = DB::table('srp_erp_employeeeducationaldetails')
            ->selectRaw("empID as empId, degree, grade, school as institution,
            CONCAT(dateTo, 'T18:30:00.000Z') as completionDate, 
            fieldOfStudyId as fieldOfStudyId")
            ->where('id', $this->id)
            ->first();

        if($this->postType != "POST") {
            $this->getReferenceId();
            $this->educationData['id'] = $this->masterUuId;
        }

        if(empty($data)){
            return;
        }

        $this->fieldOfStudyId = $this->getOtherReferenceId($data->fieldOfStudyId, 10);
        $grade =  ((int)$data->grade == 0) ? null : $data->grade;
        $this->getEmployeeReferenceId($data->empId);
        $this->educationData = array_merge([
            "institution" => $data->institution,
            "degree" => $data->degree,
            "gpa" => $grade,
            "completionDate" => $data->completionDate,
            "employeeId" => $this->empRefId,
            "fieldOfStudyId" => $this->fieldOfStudyId,
            "status" => 1
        ], $this->educationData);
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
