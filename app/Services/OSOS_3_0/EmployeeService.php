<?php

namespace App\Services\OSOS_3_0;

use App\Models\User;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;
use App\Traits\OSOS_3_0\JobCommonFunctions;
use App\Jobs\OSOS_3_0\UsersWebHook;

class EmployeeService
{
    protected $apiExternalKey;
    protected $apiExternalUrl;
    protected $employeeData;
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
        $this->getPivotTableId(4);
        $this->getEmployeeData();
        $this->getUrl('employee');
    }

    function execute()
    {
        try {

            $valResp = $this->validateApiResponse();

            if (!$valResp['status']) {
                $logData = ['message' => $valResp['message'], 'id' => $this->id];
                return $this->insertToLogTb($logData, 'error', 'Employee', $this->companyId);
            }

            $logData = [
                'message' => "Employee about to trigger: " . $this->id . ' - ' . $this->employeeData['Name'],
                'id' => $this->id
            ];

            $this->insertToLogTb($logData, 'info', 'Employee', $this->companyId);

            $client = new Client();
            $headers = [
                'content-type' => 'application/json',
                'auth-key' => $this->apiExternalKey,
                'menu-id' => 'defualt'
            ];

            $res = $client->request("$this->postType", $this->apiExternalUrl . $this->url, [
                'headers' => $headers,
                'body' => json_encode($this->employeeData)
            ]);

            $statusCode = $res->getStatusCode();
            $body = $res->getBody()->getContents();

            if (in_array($statusCode, [200, 201])) {

                $je = json_decode($body, true);

                if (!isset($je['id'])) {
                    $logData = ['message' => 'Cannot Find Reference id from response', 'id' => $this->id];
                    return $this->insertToLogTb($logData, 'error', 'Employee', $this->companyId);
                }

                $this->insertOrUpdateThirdPartyPivotTable($je['id']);
                $this->userOSOS_3_0($this->id);
                $logData = ['message' => "Api employee {$this->operation} successfully processed", 'id' => $this->id];
                $this->insertToLogTb($logData, 'info', 'Employee', $this->companyId);
                return ['status' => true, 'message' => $logData['message'], 'code' => $statusCode];

            }

            if ($statusCode == 400) {
                $msg = $res->getBody();
                $logData = ['message' => json_decode($msg), 'id' => $this->id];
                return $this->capture400Err($logData, 'Employee');
            }
        } catch (\Exception $e) {

            $exStatusCode = $e->getCode();
            if ($exStatusCode == 400) {
                $msg = $e->getMessage();
                $logData = ['message' => $msg, 'id' => $this->id];
                return $this->capture400Err($logData, 'Employee');
            }

            $msg = "Exception \n";
            $msg .= "operation : " . $this->operation . "\n";
            $msg .= "message : " . $e->getMessage() . "\n";
            $msg .= "file : " . $e->getFile() . "\n";;
            $msg .= "line no : " . $e->getLine() . "\n";

            $logData = ['message' => $msg, 'id' => $this->id];
            $this->insertToLogTb($logData, 'error', 'Employee', $this->companyId);

            return ['status' => false, 'message' => $msg, 'code' => $exStatusCode];
        }
    }

    function validateApiResponse()
    {

        if (empty($this->id)) {
            $error = 'Employee id is required';
            return ['status' => false, 'message' => $error];
        }

        if (empty($this->pivotTableId)) {
            $error = 'Pivot table reference not found check pivot_tbl_reference.id';
            return ['status' => false, 'message' => $error];
        }

        if (empty($this->employeeData)) {
            $error = 'Employee not found';
            return ['status' => false, 'message' => $error];
        }

        if ($this->postType != 'POST') {
            if (empty($this->employeeData['id'])) {
                $error = 'Reference id not found';
                return ['status' => false, 'message' => $error];
            }
        }

        if (empty($this->employeeData['Code'])) {
            $error = 'Employee code not found';
            return ['status' => false, 'message' => $error];
        }

        if (empty($this->validateCompanyReference())) {
            $error = 'Company reference not found';
            return ['status' => false, 'message' => $error];
        }

        return ['status' => true, 'message' => 'success'];
    }

    function getEmployeeData()
    {
        $data = DB::table('srp_employeesdetails as e')
            ->selectRaw("e.ECode, e.Ename2, '' as Description, e.Erp_companyID, l.location_id,
                    IF(e.isDischarged = 1, 1, 0) as Status, 
                    e.isDischarged, e.EEmail, e.EcMobile, d.DesignationID,
                    IFNULL(dep.DepartmentMasterID,0) as DepartmentMasterID, em.managerID")
            ->leftJoin('hr_location_emp as l', function ($join) {
                $join->on('l.emp_id', '=', 'e.EIdNo')
                    ->where('l.is_active', '=', 1)
                    ->whereColumn('l.company_id', 'e.Erp_companyID');
            })
            ->leftJoin('srp_employeedesignation as d', function ($join) {
                $join->on('d.EmpID', '=', 'e.EIdNo')
                    ->where('d.isMajor', '=', 1)
                    ->whereColumn('d.Erp_companyID', 'e.Erp_companyID');
            })
            ->leftJoin('srp_empdepartments as dep', function ($join) {
                $join->on('dep.EmpID', '=', 'e.EIdNo')
                    ->where('dep.isPrimary', '=', 1)
                    ->whereColumn('dep.Erp_companyID', 'e.Erp_companyID');
            })
            ->leftJoin('srp_erp_employeemanagers as em', function ($join) {
                $join->on('em.empID', '=', 'e.EIdNo')
                    ->where('em.active', '=', 1)
                    ->whereColumn('em.companyID', 'e.Erp_companyID');
            })
            ->where([['e.EIdNo', $this->id], ['e.empConfirmedYN', 1]])
            ->first();

        if (empty($data)) {
            return;
        }

         $this->employeeData = [
             "Code" => $data->ECode,
             "Name" => $data->Ename2,
             "Status" => $data->Status,
             "ContactEmail" => $data->EEmail,
             "ContactNumber" => $data->EcMobile,
             "IsDeleted" => false,
             "DepartmentId" => $this->getOtherReferenceId($data->DepartmentMasterID, 3),
             "ReportingManagerId" => $this->getOtherReferenceId($data->managerID, 4),
             "LocationId" => $this->getOtherReferenceId($data->location_id, 1),
             "DesignationId" => $this->getOtherReferenceId($data->DesignationID, 2),
             "CompanyId" => $this->getOtherReferenceId($data->Erp_companyID, 5)
         ];

        if ($this->postType != "POST") {
            $this->getReferenceId();
            $this->employeeData['id'] = $this->masterUuId;
        }
    }

    function userOSOS_3_0($id)
    {
        $userData = $this->getUserId($id);
        if (!empty($userData->userId)){
            $userReferenceId = $this->checkReferenceId($userData->userId);
            if (empty($userReferenceId)){
                $this->postType = 'POST';
            }
            UsersWebHook::dispatch($this->dataBase, $this->postType, $id, $this->thirdPartyData);
        }
    }

    function getUserId($empId)
    {
        return User::where('employee_id', $empId)
            ->select('id as userId')
            ->first();
    }

    function checkReferenceId($userId)
    {
        return DB::table('third_party_pivot_record')
            ->where('pivot_table_id', 6)
            ->where('system_id', $userId)
            ->where('third_party_sys_det_id', $this->detailId)
            ->value('reference_id');
    }
}
