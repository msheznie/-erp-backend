<?php

namespace App\Services\OSOS_3_0;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;
use App\Traits\OSOS_3_0\JobCommonFunctions;

class LocationService
{
    protected $apiExternalKey;
    protected $apiExternalUrl;
    protected $locationData;
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

    public function __construct($dataBase, $id, $postType, $thirdPartyData)
    {

        $this->dataBase = $dataBase;
        $this->postType = trim(strtoupper($postType), '"');
        $this->id = $id;
        $this->detailId = $thirdPartyData['id'];
        $this->apiKey = $thirdPartyData['api_key'];
        $this->apiExternalKey = $thirdPartyData['api_external_key'];
        $this->apiExternalUrl = $thirdPartyData['api_external_url'];
        $this->companyId = $thirdPartyData['company_id'];
        $this->thirdPartyData = $thirdPartyData;

        $this->getOperation();
        $this->getPivotTableId(1);
        $this->getLocationData();
        $this->getUrl('location');

    }

    function execute()
    {
        try {

            $valResp = $this->validateApiResponse();

            if (!$valResp['status']) {
                $logData = ['message' => $valResp['message'], 'id' => $this->id ];
                return $this->insertToLogTb($logData, 'error', 'Location', $this->companyId);
            }

            $logData = [
                'message' => "Location about to trigger: " . $this->id . ' - ' . $this->locationData['name'], 
                'id' => $this->id 
            ];
            $this->insertToLogTb($logData, 'info', 'Location', $this->companyId);

            $client = new Client();
            $headers = [
                'content-type' => 'application/json',
                'auth-key' => $this->apiExternalKey,
                'menu-id' => 'defualt'
            ];

            $res = $client->request("$this->postType", $this->apiExternalUrl . $this->url, [
                'headers' => $headers,
                'body' => json_encode($this->locationData)
            ]);

            $statusCode = $res->getStatusCode();
            $body = $res->getBody()->getContents();

            if (in_array($statusCode, [200, 201])) {

                $je = json_decode($body, true);

                if (!isset($je['id'])) {
                    $logData = ['message' => 'Cannot Find Reference id from response', 'id' => $this->id ];
                    $this->insertToLogTb($logData, 'error', 'Location', $this->companyId);
                    return ['status' => false, 'message' => $logData['message'], 'code' => false];
                }

                $this->insertOrUpdateThirdPartyPivotTable($je['id']);
                $logData = ['message' => "Api location {$this->operation} successfully processed", 'id' => $this->id ];
                $this->insertToLogTb($logData, 'info', 'Location', $this->companyId);
                return ['status' => true, 'message' => $logData['message'], 'code' => $statusCode];

            }

            if ($statusCode == 400) {
                $msg = $res->getBody();
                $logData = ['message' => json_decode($msg), 'id' => $this->id ];
                $this->capture400Err($logData, 'Location');
                return ['status' => false, 'message' => $msg, 'code' => $statusCode];
            }

        } catch (\Exception $e) {

            $exStatusCode = $e->getCode();
            if ($exStatusCode == 400) {
                $msg = $e->getMessage();
                $logData = ['message' => $msg, 'id' => $this->id ];
                return $this->capture400Err($logData, 'Location');
            }

            $msg = "Exception \n";
            $msg .= "operation : " . $this->operation . "\n";;
            $msg .= "message : " . $e->getMessage() . "\n";;
            $msg .= "file : " . $e->getFile() . "\n";;
            $msg .= "line no : " . $e->getLine() . "\n";;

            $logData = ['message' =>  $msg, 'id' => $this->id ];
            $this->insertToLogTb($logData, 'error', 'Location', $this->companyId);
            
            return ['status' => false, 'message' => $msg, 'code' => $exStatusCode];
        }
    }

    function validateApiResponse()
    {

        if (empty($this->id)) {
            $error = 'Location id is required';
            return ['status' => false, 'message' => $error];
        }

        if (empty($this->pivotTableId)) {
            $error = 'Pivot table reference not found check pivot_tbl_reference.id';
            return ['status' => false, 'message' => $error];
        }

        if (empty($this->locationData)) {
            $error = 'Location not found';
            return ['status' => false, 'message' => $error];
        }

        if ($this->postType != 'POST') {
            if (empty($this->locationData['id'])) {
                $error = 'Reference id not found';
                return ['status' => false, 'message' => $error];
            }
        }

        if (empty($this->locationData['code'])) {
            $error = 'Location code not found';
            return ['status' => false, 'message' => $error];
        }

        return ['status' => true, 'message' => 'success'];
    }

    function getLocationData()
    {
        //country id oman hardcoded
        $data = DB::table('hr_location_master')
            ->selectRaw("code as Code, description as Name, '' as Description, 
                '10ccca76-1657-11ee-be56-0242ac120002' as CountryId,
                CASE 
                    WHEN is_deleted = 1 THEN 2 
                    WHEN is_active = 0 THEN 1 
                    WHEN is_active = 1 THEN 0 
                END as Status,
                CASE 
                    WHEN is_deleted = 1 THEN 'true'
                    ELSE 'false'
                END AS IsDeleted")
            ->where('id', $this->id)
            ->first();

        if (empty($data)) {
            return;
        }

        $this->locationData = [
            "code" => $data->Code,
            "name" => $data->Name,
            "description" => $data->Description,
            "countryId" => $data->CountryId,
            "status" => $data->Status,
            "isDeleted" => $data->IsDeleted,
        ];

        if ($this->postType != "POST") {
            $this->getReferenceId();
            $this->locationData['id'] = $this->masterUuId;
        }
    }
}
