<?php
namespace App\Services\OSOS_3_0;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;
use App\Traits\OSOS_3_0\JobCommonFunctions;

class UserService{
    protected $apiExternalKey;
    protected $apiExternalUrl;
    protected $userData;
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
        $this->getPivotTableId(6);
        $this->getUserData();
        $this->getUrl('user');
    }

    function execute(){
        try {

            $valResp =$this->validateApiResponse();

            if(!$valResp['status']){
                $logData = ['message' => $valResp['message'], 'id' => $this->id ];
                return $this->insertToLogTb($logData, 'error', 'User', $this->companyId);
            }

            $logData = [
                'message' => "User about to trigger: " . $this->id . ' - '. $this->userData['fullName'],
                'id' => $this->id
            ];

            $this->insertToLogTb($logData, 'info', 'User', $this->companyId);

            $client = new Client();
            $headers = [
                'content-type' => 'application/json',
                'auth-key' =>  $this->apiExternalKey,
                'menu-id' =>  'defualt'
            ];

            $res = $client->request("$this->postType", $this->apiExternalUrl . $this->url, [
                'headers' => $headers,
                'body' => json_encode($this->userData)
            ]);

            $statusCode = $res->getStatusCode();
            $body = $res->getBody()->getContents();

            if (in_array($statusCode, [200, 201])) {

                $je = json_decode($body, true);

                if(!isset($je['id'])){
                    $logData = ['message' => 'Cannot Find Reference id from response', 'id' => $this->id ];
                    return $this->insertToLogTb($logData, 'error', 'User', $this->companyId);
                }

                $this->insertOrUpdateThirdPartyPivotTable($je['id']);
                $logData = ['message' => "Api user {$this->operation} successfully processed", 'id' => $this->id ];
                $this->insertToLogTb($logData, 'info', 'User', $this->companyId);
                return ['status' => true, 'message' => $logData['message'], 'code' => $statusCode];

            }

            if ($statusCode == 400) {
                $msg = $res->getBody();
                $logData = ['message' => json_decode($msg), 'id' => $this->id];
                return $this->capture400Err($logData, 'User');
            }
        } catch (\Exception $e) {

            $exStatusCode = $e->getCode();
            if ($exStatusCode == 400) {
                $msg = $e->getMessage();
                $logData = ['message' => $msg, 'id' => $this->id ];
                return $this->capture400Err($logData, 'User');
            }

            $msg = "Exception \n";
            $msg .= "operation : ".$this->operation."\n";
            $msg .= "message : ".$e->getMessage()."\n";
            $msg .= "file : ".$e->getFile()."\n";;
            $msg .= "line no : ".$e->getLine()."\n";

            $logData = ['message' =>  $msg, 'id' => $this->id ];
            $this->insertToLogTb($logData, 'error', 'User', $this->companyId);

            return ['status' => false, 'message' => $msg, 'code' => $exStatusCode];
        }
    }

    function validateApiResponse(){

        if(empty($this->id)){
            $error = 'User id is required';
            return ['status' =>false, 'message'=> $error];
        }

        if(empty($this->pivotTableId)){
            $error = 'Pivot table reference not found check pivot_tbl_reference.id';
            return ['status' =>false, 'message'=> $error];
        }

        if (empty($this->userData)) {
            $error = 'User not found';
            return ['status' =>false, 'message'=> $error];
        }

        if($this->postType != 'POST'){
            if(empty($this->userData['id'])){
                $error = 'Reference id not found';
                return ['status' =>false, 'message'=> $error];
            }
        }

        if (empty($this->userData['userName'])){
            $error = 'Username is required';
            return ['status' =>false, 'message'=> $error];
        }

        if ($this->validateUserName() > 1){
            $error = 'Username is not unique';
            return ['status' =>false, 'message'=> $error];
        }

        if(empty($this->validateCompanyReference())){
            $error = 'Company reference not found';
            return ['status' =>false, 'message'=> $error];
        }

        return ['status' =>true, 'message'=> 'success'];
    }

    function getUserData()
    {
        /*
        Query updated to get the user company Id from employees table not the srp_employeesdetails table,
        which will be used to get the reference company id from third_party_pivot_records table.
        (In portal user creation srp_employeesdetails won't be updated, to handle that above change has been done.)
        Task Id = GHR-1917
        */
        $data = DB::table('users as u')
            ->selectRaw("u.name, emp.empCompanySystemID as companyId,
                    e.EEmail, e.EcMobile, g.name as gender, u.username,
                    CASE
                        WHEN g.genderID = 1 THEN 'Mr' 
                        WHEN g.genderID = 2 THEN 'Miss'
                    END as empTitle,
                    CONCAT(IFNULL(e.EDOB, '2000-01-01'), 'T18:30:00.000Z') as dateOfBirth,
                    DATE_FORMAT(IFNULL(e.EDOB, '2000-01-01'), '%d/%m/%Y') as hiddenDateOfBirth")
            ->join('srp_employeesdetails as e', function($join) {
                $join->on('e.EIdNo', '=', 'u.employee_id');
            })
            ->join('employees as emp', function($join) {
                $join->on('emp.employeeSystemID', '=', 'u.employee_id');
            })
            ->leftJoin('hrms_gender as g', function($join) {
                $join->on('g.genderID', '=', 'e.Gender');
            })
            ->where('u.id', $this->id)
            ->first();

        if(empty($data)){
            return;
        }

        $companyId = $this->getOtherReferenceId($data->companyId, 5);

        $this->userData = [
            "activeCompany" =>$companyId,
            "company" => [
                "id" => $companyId
            ],
            "configuration" => [],
            "userName" => $data->username,
            "email" => $data->EEmail,
            "title" => $data->empTitle,
            "fullName" => $data->name,
            "gender" => $data->gender,
            "dateOfBirth" => $data->dateOfBirth,
            "hiddenDateOfBirth" => $data->hiddenDateOfBirth,
            "isActive" => true,
            "language" => "en-US",
            "mobileNo" => $data->EcMobile,
            "status" => 0,
            "timeZone" => "GMT +04:00 Oman Time",
            "userType" => "EssUser"
        ];

        if($this->postType != "POST"){
            $this->getReferenceId();
            $this->userData['id'] = $this->masterUuId;
        }
    }

    function validateUserName(){
        return DB::table('users')
            ->where('username', trim($this->userData['userName']))
            ->count();
    }
}
