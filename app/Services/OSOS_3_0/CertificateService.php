<?php
namespace App\Services\OSOS_3_0;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;
use App\Traits\OSOS_3_0\JobCommonFunctions;

class CertificateService{
    protected $apiExternalKey;
    protected $apiExternalUrl;
    protected $certificateData = [];
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
        $this->getPivotTableId(8);
        $this->getCertificateData();
        $this->getUrl('certificate');
    }

    function execute(){
        try {

            $valResp =$this->validateApiResponse();

            if(!$valResp['status']){
                $logData = ['message' => $valResp['message'], 'id' => $this->id];
                return $this->insertToLogTb($logData, 'error', 'Certificate', $this->companyId);
            }

            $cerName = ($this->postType != 'DELETE')? ' - '. $this->certificateData['name'] : '';
            $msg = "Certificate about to trigger: " . $this->id . $cerName;

            $logData = ['message' => $msg, 'id' => $this->id ];
            $this->insertToLogTb($logData, 'info', 'Certificate', $this->companyId);

            $client = new Client();
            $headers = [
                'content-type' => 'application/json',
                'auth-key' =>  $this->apiExternalKey,
                'menu-id' =>  'defualt'
            ];

            $res = $client->request("$this->postType", $this->apiExternalUrl . $this->url, [
                'headers' => $headers,
                'body' => json_encode($this->certificateData)
            ]);

            $statusCode = $res->getStatusCode();
            $body = $res->getBody()->getContents();

            if (in_array($statusCode, [200, 201])) {

                $je = json_decode($body, true);

                if(!isset($je['id'])){
                    $logData = ['message' => 'Cannot Find Reference id from response', 'id' => $this->id ];
                    return $this->insertToLogTb($logData, 'error', 'Certificate', $this->companyId);
                }

                $this->insertOrUpdateThirdPartyPivotTable($je['id']);
                $logData = [
                    'message' => "Api certificate {$this->operation} successfully processed",
                    'id' => $this->id
                ];
                $this->insertToLogTb($logData, 'info', 'Certificate', $this->companyId);
                return ['status' => true, 'message' => $logData['message'], 'code' => $statusCode];

            }

            if ($statusCode == 400) {
                $msg = $res->getBody();
                $logData = ['message' => json_decode($msg), 'id' => $this->id ];
                return $this->capture400Err($logData, 'Certificate');
            }
        } catch (\Exception $e) {

            $exStatusCode = $e->getCode();
            if ($exStatusCode == 400) {
                $msg = $e->getMessage();
                $logData = ['message' => $msg, 'id' => $this->id ];
                return $this->capture400Err($logData, 'Certificate');
            }

            $msg = "Exception \n";
            $msg .= "operation : ".$this->operation."\n";;
            $msg .= "message : ".$e->getMessage()."\n";;
            $msg .= "file : ".$e->getFile()."\n";;
            $msg .= "line no : ".$e->getLine()."\n";;

            $logData = ['message' =>  $msg, 'id' => $this->id];
            $this->insertToLogTb($logData, 'error', 'Certificate', $this->companyId);

            return ['status' => false, 'message' => $msg, 'code' => $exStatusCode];
        }
    }

    function validateApiResponse(){

        if(empty($this->id)){
            $error = 'Certificate id is required';
            return ['status' =>false, 'message'=> $error];
        }

        if(empty($this->pivotTableId)){
            $error = 'Pivot table reference not found check pivot_tbl_reference.id';
            return ['status' =>false, 'message'=> $error];
        }

        if (empty($this->certificateData) && $this->postType != 'DELETE') {
            $error = 'Certificate details not found';
            return ['status' =>false, 'message'=> $error];
        }

        if(empty($this->certificateData['id']) && $this->postType != 'POST'){
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

    function getCertificateData()
    {
        $data = DB::table('srp_empcertification')
            ->selectRaw("EmpID as empId, Description as name, institution as institution, 
            CONCAT(AwardedDate, 'T18:30:00.000Z') as awardedDate, GPA as gpa")
            ->where('certificateID', $this->id)
            ->first();

        if($this->postType != "POST") {
            $this->getReferenceId();
            $this->certificateData['id'] = $this->masterUuId;
        }

        if(empty($data)){
            return;
        }

        $gpa =  ((int)$data->gpa == 0) ? null : $data->gpa;
        $this->getEmployeeReferenceId($data->empId);
        $this->certificateData = array_merge(
            [
                "name" => $data->name,
                "gpa" => empty($gpa) ? null : $gpa,
                "institution" => $data->institution,
                "employeeId" => $this->empRefId,
                "status" => 1
            ],
            !empty($data->awardedDate) ? ["awardedDate" => $data->awardedDate] : [],
            $this->certificateData
        );
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
