<?php
namespace App\Services\OSOS_3_0;

 use GuzzleHttp\Client;
 use Illuminate\Support\Facades\DB;
 use App\Traits\OSOS_3_0\JobCommonFunctions;

 class FieldOfStudyService{
     protected $apiExternalKey;
     protected $apiExternalUrl;
     protected $fieldOfStudyData = [];
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
         $this->getPivotTableId(10);
         $this->getFieldOfStudyData();
         $this->getUrl('fos');
     }

     function execute(){
         try {

             $valResp =$this->validateApiResponse();

             if(!$valResp['status']){
                $logData = ['message' => $valResp['message'], 'id' => $this->id];
                 return $this->insertToLogTb($logData, 'error', 'Field of Study', $this->companyId);
             }

             $depName = ($this->postType != 'DELETE')? ' - '. $this->fieldOfStudyData['Title'] : '';
             $msg = "Field of Study about to trigger: " . $this->id . $depName;

             $logData = ['message' => $msg, 'id' => $this->id ];
             $this->insertToLogTb($logData, 'info', 'Field of Study', $this->companyId);

             $client = new Client();
             $headers = [
                 'content-type' => 'application/json',
                 'auth-key' =>  $this->apiExternalKey,
                 'menu-id' =>  'defualt'
             ];

             $res = $client->request("$this->postType", $this->apiExternalUrl . $this->url, [
                 'headers' => $headers,
                 'body' => json_encode($this->fieldOfStudyData)
             ]);

             $statusCode = $res->getStatusCode();
             $body = $res->getBody()->getContents();

             if (in_array($statusCode, [200, 201])) {

                 $je = json_decode($body, true);

                 if(!isset($je['id'])){
                    $logData = ['message' => 'Cannot Find Reference id from response', 'id' => $this->id ];
                     return $this->insertToLogTb($logData, 'error', 'Field of Study', $this->companyId);
                 }

                 $this->insertOrUpdateThirdPartyPivotTable($je['id']);
                 $logData = [
                     'message' => "Api Field of Study {$this->operation} successfully processed",
                     'id' => $this->id
                 ];
                 $this->insertToLogTb($logData, 'info', 'Field of Study', $this->companyId);
                 return ['status' => true, 'message' => $logData['message'], 'code' => $statusCode];

             }

             if ($statusCode == 400) {
                 $msg = $res->getBody();
                 $logData = ['message' => json_decode($msg), 'id' => $this->id ];
                 return $this->capture400Err($logData, 'Field of Study');
             }
         } catch (\Exception $e) {

             $exStatusCode = $e->getCode();
             if ($exStatusCode == 400) {
                 $msg = $e->getMessage();
                 $logData = ['message' => $msg, 'id' => $this->id ];
                 return $this->capture400Err($logData, 'Field of Study');
             }

             $msg = "Exception \n";
             $msg .= "operation : ".$this->operation."\n";;
             $msg .= "message : ".$e->getMessage()."\n";;
             $msg .= "file : ".$e->getFile()."\n";;
             $msg .= "line no : ".$e->getLine()."\n";;

             $logData = ['message' =>  $msg, 'id' => $this->id];
             $this->insertToLogTb($logData, 'error', 'Field of Study', $this->companyId);
            
            return ['status' => false, 'message' => $msg, 'code' => $exStatusCode];
         }
     }

     function validateApiResponse(){

         if(empty($this->id)){
             $error = 'Field of Study id is required';
             return ['status' =>false, 'message'=> $error];
         }

        if(empty($this->pivotTableId)){
            $error = 'Pivot table reference not found check pivot_tbl_reference.id';
            return ['status' =>false, 'message'=> $error];
        }

         if (empty($this->fieldOfStudyData) && $this->postType != 'DELETE') {
             $error = 'Field of Study not found';
             return ['status' =>false, 'message'=> $error];
         }

         if(empty($this->fieldOfStudyData['id']) && $this->postType != 'POST'){
             $error = 'Reference id not found';
             return ['status' =>false, 'message'=> $error];
         }

         if(empty($this->validateCompanyReference())){
             $error = 'Company reference not found';
             return ['status' =>false, 'message'=> $error];
         }

         return ['status' =>true, 'message'=> 'success'];
     }

     function getFieldOfStudyData()
     {
         $data = DB::table('hr_field_of_study_master')
             ->select("description")
             ->where('id', $this->id)
             ->first();

         if($this->postType != "POST") {
             $this->getReferenceId();
             $this->fieldOfStudyData['id'] = $this->masterUuId;
         }

         if(empty($data)){
             return;
         }

         $this->fieldOfStudyData = array_merge([
             "Title" => $data->description,
             "Details" => $data->description,
             "Status" => 1
         ], $this->fieldOfStudyData);
     }
 }
