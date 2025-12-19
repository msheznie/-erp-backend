<?php

namespace App\Repositories;

use App\Models\TenderMaster;
use App\Models\TenderCustomEmail;
use App\Services\SRMService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TenderCustomEmailRepository
{
    public function storeCustomEmailData($request)
    {
        $input = $request->all();
        $data = $request->validated();

        if (!empty($request->cc_email)) {
            $ccEmails = array_map('trim', explode(',', $request->cc_email));
            $data['cc_email'] = json_encode($ccEmails);
        }

        $employee = \Helper::getEmployeeInfo();
        $additionalData = [
            'pc_id' => gethostname(),
            'created_by' => $employee->empID,
            'user_id' => $employee->employeeSystemID,
            'user_group' => $employee->userGroupID,
            'document_code' => $input['document_code'],
            'document_id' => $input['document_id'],
            'email_subject' => $input['email_subject'] ?? '',
            'negotiation_id' => $input['negotiation_id'] ?? '',
        ];

        try {
            return DB::transaction(function () use ($input, $data, $additionalData) {
                $tenderData = TenderMaster::getTenderByUuid($data['tender_uuid']);
                if(!$tenderData){
                    return [
                        "success" => false,
                        "data" => trans('srm_ranking.invalid_tender_uuid')
                    ];
                }
                foreach ($data['supplier_uuid'] as $supplierUuid) {
                    $supplierId = SRMService::getSupplierRegIdByUUID($supplierUuid);
                    if(!$supplierId){
                        return [
                            "success" => false,
                            "data" =>  trans('srm_ranking.invalid_supplier_uuid')
                        ];
                    }

                    $data['supplier_id'] = $supplierId;

                    $recordData = array_merge($data, $additionalData);
                    $result = TenderCustomEmail::createOrUpdateCustomEmail(
                        ['tender_id' => $tenderData->id, 'supplier_id' => $supplierId],
                        $recordData
                    );

                    if (!$result) {
                        return ['success' => false, 'data' =>  trans('srm_ranking.failed_to_save_record')];
                    }
                }

                return ['success' => true, 'data' =>  trans('srm_ranking.saved_successfully')];

            });
        } catch (\Exception $e) {
            return ['success' => false, 'data' => trans('srm_ranking.failed_to_save_record')];
        }
    }

    public function getCustomEmailSupplier($tenderId, $supplierUuid, $documentCode)
    {
        try {
            return DB::transaction(function () use ( $tenderId, $supplierUuid, $documentCode) {

                $tenderData = TenderMaster::getTenderByUuid($tenderId);
                if(!$tenderData){
                    return [
                        "success" => false,
                        "data" => 'Not a Valid Tender UUID'
                    ];
                }
                $supplierId = SRMService::getSupplierRegIdByUUID($supplierUuid);
                if(!$supplierId){
                    return [
                        "success" => false,
                        "data" => 'Not a Valid Supplier UUID'
                    ];
                }
                return TenderCustomEmail::getCustomEmailSupplier($tenderData['id'], $supplierId, $documentCode);
            });
        } catch (\Exception $e) {

            return [
                "success" => false,
                "data" => 'Error occurred: ' . $e->getMessage()
            ];
        }
    }

    public function getCustomEmailData($tenderId, $negotiationId)
    {
        try {
            return DB::transaction(function () use ( $tenderId, $negotiationId) {
                $tenderData = TenderMaster::getTenderByUuid($tenderId);

                if (!$tenderData) {
                    return [
                        "success" => false,
                        "data" => 'Not a Valid Tender UUID'
                    ];
                }
                return TenderCustomEmail::getCustomEmailData($tenderData['id'], $negotiationId);
            });
        } catch (\Exception $e) {
            return [
                "success" => false,
                "data" => 'Error occurred: ' . $e->getMessage()
            ];
        }
    }

    public function deleteByTenderAndSupplier($tenderId, $supplierUuid)
    {
        try {
            return DB::transaction(function () use ( $tenderId, $supplierUuid) {
                $tenderData = TenderMaster::getTenderByUuid($tenderId);
                if(!$tenderData){
                    return [
                        "success" => false,
                        "data" => trans('srm_ranking.invalid_tender_uuid')
                    ];
                }
                $supplierId = SRMService::getSupplierRegIdByUUID($supplierUuid);
                if(!$supplierId){
                    return [
                        "success" => false,
                        "data" => trans('srm_ranking.invalid_supplier_uuid')
                    ];
                }

                return TenderCustomEmail::where('tender_id', $tenderData['id'])
                        ->where('supplier_id', $supplierId)
                        ->delete() > 0;
            });
        } catch (\Exception $e) {
            return [
                "success" => false,
                "data" => 'Error occurred: ' . $e->getMessage()
            ];
        }
    }
}
