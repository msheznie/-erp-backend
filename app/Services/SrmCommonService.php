<?php

namespace App\Services;

use App\Models\SrmEmployees;
use App\Models\SrmTenderBidEmployeeDetails;
use App\Models\SrmTenderBidEmployeeDetailsEditLog;
use App\Models\SrmTenderUserAccessEditLog;
use App\Models\SRMTenderUserAccess;
use Illuminate\Support\Facades\DB;

class SrmCommonService
{
    public function getActiveEmployeesForBid($request, $requestData){
        $tenderId = $request['tender_id'];
        $companyId = $request['companyId'];
        $versionId = $requestData['versionID'] ?? null;
        $isEdit = $requestData['enableRequestChange'] ?? false;

        $existingEmployees = $isEdit
            ? SrmTenderBidEmployeeDetailsEditLog::getTenderBidEmployeesAmd($tenderId, $versionId)->toArray()
            : SrmTenderBidEmployeeDetails::getTenderBidEmployees($tenderId)->toArray();

        $existingEmployeeIDs = !empty($existingEmployees) ?  collect($existingEmployees)->pluck('emp_id') : [];
        $srmEmployees = SrmEmployees::getEmployeesDetails($companyId, $existingEmployeeIDs);

        $data = [
            'bidOpeningUserDrop'           => $this->tenderUserAccessData($tenderId, $companyId, 1, $requestData),
            'commercialBidOpeningUserDrop' => $this->tenderUserAccessData($tenderId, $companyId, 2, $requestData),
            'supplierRankingUserDrop'      => $this->tenderUserAccessData($tenderId, $companyId, 3, $requestData),
            'tenderUserAccessDetails'      => $this->getUserAccessDetails($tenderId, $companyId, $requestData),
            'employeeApproval'             => [],
        ];

        if (!empty($srmEmployees)) {
            foreach ($srmEmployees as $emp) {
                $employee = $emp->employee;
                $data['employeeApproval'][] = [
                    'employeeSystemID' => $employee->employeeSystemID,
                    'empFullName'      => $employee->empID . ' | ' . $employee->empFullName,
                ];
            }
        }
        return $data;
    }

    public function tenderUserAccessData($tenderId,$companyId,$moduleId, $requestData)
    {
        $employees = SrmEmployees::tenderUserAccessData($tenderId,$companyId,$moduleId, $requestData);

        return $employees->pluck('employee')->map(function ($employee) {
            return [
                'employeeSystemID' => $employee->employeeSystemID,
                'employeeFullName' => $employee->empFullDetails,
            ];
        });
    }

    public function getUserAccessDetails($tenderId,$companyId, $requestData)
    {
        return $requestData['enableRequestChange'] ?
            SrmTenderUserAccessEditLog::getTenderUserAccessDetails($tenderId,$companyId, $requestData['versionID']) :
            SRMTenderUserAccess::getTenderUserAccessDetails($tenderId,$companyId);

    }
}
