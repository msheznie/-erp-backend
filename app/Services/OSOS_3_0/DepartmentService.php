<?php
namespace App\Services\OSOS_3_0;

 use GuzzleHttp\Client;
 use Illuminate\Support\Facades\DB;
 use App\Traits\OSOS_3_0\JobCommonFunctions;

 class DepartmentService{
     protected $apiExternalKey;
     protected $apiExternalUrl;
     protected $departmentData = [];
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
         $this->getPivotTableId(3);
         $this->getDepartmentData();
         $this->getUrl('department');
     }

     function execute(){
         try {

             $valResp =$this->validateApiResponse();

             if(!$valResp['status']){
                 return $this->insertToLogTb($valResp['message'], 'error', 'Department', $this->companyId);
             }

             $depName = ($this->postType != 'DELETE')? ' - '. $this->departmentData['Name'] : '';
             $msg = "Department about to trigger: " . $this->id . $depName;
             $this->insertToLogTb($msg, 'info', 'Department', $this->companyId);

             $client = new Client();
             $headers = [
                 'content-type' => 'application/json',
                 'auth-key' =>  $this->apiExternalKey,
                 'menu-id' =>  'defualt'
             ];

             $res = $client->request("$this->postType", $this->apiExternalUrl . $this->url, [
                 'headers' => $headers,
                 'body' => json_encode($this->departmentData)
             ]);

             $statusCode = $res->getStatusCode();
             $body = $res->getBody()->getContents();

             if (in_array($statusCode, [200, 201])) {

                 $je = json_decode($body, true);

                 if(!isset($je['id'])){
                     $msg = 'Cannot Find Reference id from response';
                     return $this->insertToLogTb($msg, 'error', 'Department', $this->companyId);
                 }

                 $this->insertOrUpdateThirdPartyPivotTable($je['id']);
                 $msg = "Api department {$this->operation} successfully finished";
                 return  $this->insertToLogTb($msg, 'info', 'Department', $this->companyId);

             }

             if ($statusCode == 400) {
                 $msg = $res->getBody();
                 return $this->capture400Err(json_decode($msg), 'Department');
             }
         } catch (\Exception $e) {

             $exStatusCode = $e->getCode();
             if ($exStatusCode == 400) {
                 $msg = $e->getMessage();
                 return $this->capture400Err($msg, 'Department');
             }

             $msg = "Exception \n";
             $msg .= "operation : ".$this->operation."\n";;
             $msg .= "message : ".$e->getMessage()."\n";;
             $msg .= "file : ".$e->getFile()."\n";;
             $msg .= "line no : ".$e->getLine()."\n";;
             return $this->insertToLogTb($msg, 'error', 'Department', $this->companyId);
         }
     }

     function validateApiResponse(){

         if(empty($this->id)){
             $error = 'Department id is required';
             return ['status' =>false, 'message'=> $error];
         }

        if(empty($this->pivotTableId)){
            $error = 'Pivot table reference not found check pivot_tbl_reference.id';
            return ['status' =>false, 'message'=> $error];
        }

         if (empty($this->departmentData) && $this->postType != 'DELETE') {
             $error = 'Department not found';
             return ['status' =>false, 'message'=> $error];
         }

         if(empty($this->departmentData['id']) && $this->postType != 'POST'){
             $error = 'Reference id not found';
             return ['status' =>false, 'message'=> $error];
         }

         if(empty($this->departmentData['Code']) && $this->postType != 'DELETE'){
             $error = 'Department code not found';
             return ['status' =>false, 'message'=> $error];
         }

         if(empty($this->validateCompanyReference())){
             $error = 'Company reference not found';
             return ['status' =>false, 'message'=> $error];
         }

         return ['status' =>true, 'message'=> 'success'];
     }

     function getDepartmentData()
     {
         $data = DB::table('srp_departmentmaster')
             ->selectRaw("DepartmentMasterID as Code, DepartmentDes as Name, '' as Description, Erp_companyID,
                IF(isActive = 0, 1, 0) as Status")
             ->where('DepartmentMasterID', $this->id)
             ->first();

         if($this->postType != "POST") {
             $this->getReferenceId();
             $this->departmentData['id'] = $this->masterUuId;
         }

         if(empty($data)){
             return;
         }

         $this->departmentData = array_merge([
             "Code" => $data->Code,
             "Name" => $data->Name,
             "Description" => $data->Description,
             "Status" => $data->Status,
             "IsDeleted" => false,
             "CompanyId" => $this->getOtherReferenceId($data->Erp_companyID, 5)
         ], $this->departmentData);
     }
 }
