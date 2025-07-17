<?php

namespace App\Services;


use App\helper\Helper;
use App\Models\BidSubmissionMaster;
use App\Models\CalendarDatesDetail;
use App\Models\CalendarDatesDetailEditLog;
use App\Models\CircularAmendments;
use App\Models\CircularAmendmentsEditLog;
use App\Models\CircularSuppliers;
use App\Models\CircularSuppliersEditLog;
use App\Models\DocumentAttachments;
use App\Models\DocumentAttachmentsEditLog;
use App\Models\DocumentModifyRequest;
use App\Models\EvacuationCriteriaScoreConfigLog;
use App\Models\EvaluationCriteriaDetails;
use App\Models\EvaluationCriteriaDetailsEditLog;
use App\Models\EvaluationCriteriaScoreConfig;
use App\Models\PricingScheduleDetail;
use App\Models\PricingScheduleDetailEditLog;
use App\Models\PricingScheduleMaster;
use App\Models\PricingScheduleMasterEditLog;
use App\Models\ProcumentActivity;
use App\Models\ProcumentActivityEditLog;
use App\Models\ScheduleBidFormatDetails;
use App\Models\ScheduleBidFormatDetailsLog;
use App\Models\SrmTenderBidEmployeeDetailsEditLog;
use App\Models\SrmTenderBudgetItem;
use App\Models\SrmTenderDepartment;
use App\Models\SrmTenderMasterEditLog;
use App\Models\SRMTenderUserAccess;
use App\Models\SrmTenderUserAccessEditLog;
use App\Models\TenderBoqItems;
use App\Models\TenderBoqItemsEditLog;
use App\Models\TenderBudgetItemEditLog;
use App\Models\TenderCirculars;
use App\Models\TenderCircularsEditLog;
use App\Models\TenderDepartmentEditLog;
use App\Models\TenderDocumentTypeAssign;
use App\Models\TenderDocumentTypeAssignLog;
use App\Models\TenderMaster;
use App\Models\TenderMasterSupplier;
use App\Models\SrmTenderBidEmployeeDetails;
use App\Models\TenderPurchaseRequest;
use App\Models\TenderPurchaseRequestEditLog;
use App\Models\TenderSiteVisitDateEditLog;
use App\Models\TenderSiteVisitDates;
use App\Models\TenderSupplierAssignee;
use App\Models\TenderSupplierAssigneeEditLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class SrmDocumentModifyService
{
    public static function getDocumentModifyRequestForms($tender_id, $tenderMaster){
        $documentModifyRequest = DocumentModifyRequest::getTenderModifyRequest($tender_id);
        $conditions = self::checkConditions($tender_id, $tenderMaster);
        $requestType = self::getRequestType($documentModifyRequest);
        $changeRequestStatus = self::getChangeRequestStatus($documentModifyRequest, $conditions);

        return [
            'changesRequestStatus' => $changeRequestStatus,
            'requestType' => $requestType,
            'editable' => true,
            'amendment' => true,
            'enableChangeRequest' => self::enableChangeRequest($documentModifyRequest, $conditions),
            'requestedToEditAmend' => self::getRequestedToEditAmend($documentModifyRequest)
        ];
    }
    public static function getRequestType($documentModifyRequest)
    {
        if(!empty($documentModifyRequest) &&
            $documentModifyRequest->status == 1 &&
            $documentModifyRequest->confirmation_approved != -1){
            return $documentModifyRequest->type == 1 ? 'Edit' : 'Amend';
        }
        return '';
    }
    public static function getRequestedToEditAmend($documentModifyRequest): bool{
        if (empty($documentModifyRequest)) {
            return true;
        }

        $status = $documentModifyRequest->status;
        $confirmationApproved = $documentModifyRequest->confirmation_approved;

        return ($status == 1 && $confirmationApproved != 0) || $status == 0;
    }
    public static function enableChangeRequest($documentModifyRequest, $conditions){
        if (empty($documentModifyRequest) || $documentModifyRequest->status != 1) {
            return false;
        }
        if ($documentModifyRequest->modify_type == 1
            && $documentModifyRequest->approved == -1
            && $documentModifyRequest->confirmation_approved == 0
            && $conditions['checkOpeningDate']) {
            return true;
        }
        return false;
    }
    public static function getChangeRequestStatus($documentModifyRequest, $conditions)
    {
        if (empty($documentModifyRequest) || $documentModifyRequest->status != 1) {
            return null;
        }

        $requestType = self::getRequestType($documentModifyRequest);
        if ($documentModifyRequest->modify_type == 1) {
            return ($documentModifyRequest->approved == 0 && $documentModifyRequest->confirmation_approved == 0)
                ? 'Requested for ' . $requestType . ' approval'
                : 'Requested for ' . $requestType . ' approved';
        }
        if ($documentModifyRequest->modify_type != 1 && $documentModifyRequest->approved == 1 && $documentModifyRequest->confirmation_approved == 0) {
            return 'Requested after ' . $requestType . ' approval';
        }
        return null;
    }
    public static function checkConditions($tender_id, $tenderMaster){
        $currentDate = Carbon::now()->format('Y-m-d H:i:s');
        $openingDate= Carbon::createFromFormat('Y-m-d H:i:s', $tenderMaster['bid_submission_opening_date']);
        $closingDate = Carbon::createFromFormat('Y-m-d H:i:s', $tenderMaster['bid_submission_closing_date']);

        return [
            'checkOpeningDate'               => $openingDate->gt($currentDate),
            'checkClosingDate'               => $closingDate->gt($currentDate),
            'tenderPurchasedOrProceed'       => TenderMasterSupplier::checkTenderPurchased($tender_id),
            'isTenderBidSubmitted'           => BidSubmissionMaster::checkTenderBidSubmitted($tender_id),
            'isSupplierRankingNotCompleted'  => $tenderMaster['combined_ranking_status'] == 0 || $tenderMaster['negotiation_combined_ranking_status'] == 0,
        ];
    }
    public static function checkForEditOrAmendRequest($tenderMasterID){
        $documentModify = DocumentModifyRequest::getTenderModifyRequest($tenderMasterID);
        $enableRequestChange = false;
        $versionID = 0;
        $tenderMasterHistory = null;

        if(!empty($documentModify) && $documentModify->status == 1){
            if($documentModify->approved !=0 && $documentModify->confirmation_approved == 0){
                $enableRequestChange = true;
            }
        }
        if($enableRequestChange){
            $tenderMasterHistory = SrmTenderMasterEditLog::tenderMasterHistory($tenderMasterID, 0);
            $versionID = $tenderMasterHistory['version_id'] ?? 0;
        }
        return [
            'enableRequestChange' => $enableRequestChange,
            'versionID' => $versionID,
            'tenderMasterHistory' => $tenderMasterHistory
        ];
    }
    public static function cloneHistoryToMasterTable($tenderMasterID, $documentSystemID){
        try {
            $tenderMasterHistory = SrmTenderMasterEditLog::tenderMasterHistory($tenderMasterID);
            $versionID = $tenderMasterHistory['version_id'] ?? 0;
            self::updateTenderMasterData($tenderMasterID, $versionID, $tenderMasterHistory);
            self::updateTenderBidEmployeesData($tenderMasterID, $versionID);
            self::updateTenderUserAccessData($tenderMasterID, $versionID);
            self::updateTenderDepartment($tenderMasterID, $versionID);
            self::updateTenderPurchaseRequest($tenderMasterID, $versionID);
            self::updateTenderBudgetItems($tenderMasterID, $versionID);
            self::updateCalendarDateDetails($tenderMasterID, $versionID);
            self::updateTenderSiteVisitDate($versionID, $tenderMasterID);
            self::updateProcurementActivity($versionID, $tenderMasterID);
            self::updateTenderDocumentAttachment($tenderMasterID, $versionID, $documentSystemID);
            self::updateTenderDocumentTypeAssign($tenderMasterID, $versionID);
            self::updateTenderSupplierAssignee($tenderMasterID, $versionID);
            self::updateTenderPricingScheduleMaster($tenderMasterID, $versionID);
            self::updateCirculars($tenderMasterID, $versionID);
            self::updateEvaluationCriteriaDetail($tenderMasterID, $versionID);
            self::updateEvaluationCriteriaScore($versionID);
            return ['success' => true, 'message' => 'Record updated successfully'];
        } catch (\Exception $ex) {
            return ['success' => false, 'message' => $ex->getMessage()];
        }

    }
    public static function updateTenderMasterData($tenderMasterID, $versionID, $tenderMasterHistory){
        try {
            return DB::transaction(function () use ($tenderMasterID, $versionID, $tenderMasterHistory) {
                $attributes = $tenderMasterHistory->getAttributes();
                unset($attributes['id'], $attributes['created_at'], $attributes['updated_at'] , $attributes['level_no']);
                $tenderMasterRecord = TenderMaster::find($tenderMasterID);
                $tenderMasterRecord->update($attributes);
                return ['success' => true, 'message' => 'Tender updated successfully'];
            });
        } catch (\Exception $ex) {
            return ['success' => false, 'message' => $ex->getMessage()];
        }

    }
    public static function updateTenderBidEmployeesData($tenderMasterID, $versionID){
        try {
            return DB::transaction(function () use ($tenderMasterID, $versionID) {
                $amdRecords = SrmTenderBidEmployeeDetailsEditLog::getAmendRecords($versionID, $tenderMasterID, false);
                $amdRecordIds = $amdRecords->pluck('id');
                $amdRecords->each(function ($amdRecord) {
                    $masterRecord = SrmTenderBidEmployeeDetails::find($amdRecord->id);
                    if($masterRecord){
                        $masterRecord->fill($amdRecord->toArray());
                        $masterRecord->save();
                    }
                });

                SrmTenderBidEmployeeDetails::whereNotIn('id', $amdRecordIds)
                    ->where('tender_id', $tenderMasterID)
                    ->delete();

                $newRecords = SrmTenderBidEmployeeDetailsEditLog::getAmendRecords($versionID, $tenderMasterID, true);
                $newRecords->each(function ($record) {
                    $newRecord = new SrmTenderBidEmployeeDetails();
                    foreach ($record->toArray() as $column => $value)
                    {
                        if (!in_array($column, ['amd_id', 'id', 'tender_edit_version_id', 'modify_type', 'updated_by', 'level_no', 'is_deleted']))
                        {
                            $newRecord->{$column} = $value;
                        }
                    }
                    $newRecord->save();
                    $record->id = $newRecord->id;
                    $record->save();
                });
                return ['success' => true, 'message' => 'Tender bid employee/s updated successfully'];
            });
        } catch (\Exception $ex) {
            return ['success' => false, 'message' => $ex->getMessage()];
        }
    }
    public static function updateTenderUserAccessData($tenderMasterID, $versionID){
        try {
            return DB::transaction(function () use ($tenderMasterID, $versionID) {
                $amdRecords = SrmTenderUserAccessEditLog::getAmendRecords($versionID, $tenderMasterID, false);
                $amdRecordIds = $amdRecords->pluck('id');
                $amdRecords->each(function ($amdRecord) {
                    $masterRecord = SRMTenderUserAccess::find($amdRecord->id);
                    if($masterRecord){
                        $masterRecord->fill($amdRecord->toArray());
                        $masterRecord->save();
                    }
                });

                SRMTenderUserAccess::whereNotIn('id', $amdRecordIds)
                    ->where('tender_id', $tenderMasterID)
                    ->delete();

                $newRecords = SrmTenderUserAccessEditLog::getAmendRecords($versionID, $tenderMasterID, true);
                $newRecords->each(function ($record) {
                    $newRecord = new SRMTenderUserAccess();
                    foreach ($record->toArray() as $column => $value)
                    {
                        if (!in_array($column, ['amd_id', 'id', 'version_id', 'is_deleted', 'level_no']))
                        {
                            $newRecord->{$column} = $value;
                        }
                    }
                    $newRecord->save();
                    $record->id = $newRecord->id;
                    $record->save();
                });
                return ['success' => true, 'message' => 'Tender user access updated successfully'];
            });
        } catch (\Exception $ex) {
            return ['success' => false, 'message' => $ex->getMessage()];
        }
    }
    public static function updateTenderDepartment($tenderMasterID, $versionID){
        try {
            return DB::transaction(function () use ($versionID, $tenderMasterID) {
                $amdRecords = TenderDepartmentEditLog::getAmendRecord($versionID, $tenderMasterID, false);
                $amdRecordIds = $amdRecords->pluck('id');
                $amdRecords->each(function ($amdRecord) {
                    $masterRecord = SrmTenderDepartment::find($amdRecord->id);
                    if($masterRecord){
                        $masterRecord->fill($amdRecord->toArray());
                        $masterRecord->save();
                    }
                });

                SrmTenderDepartment::whereNotIn('id', $amdRecordIds)
                    ->where('tender_id', $tenderMasterID)
                    ->delete();

                $newRecords = TenderDepartmentEditLog::getAmendRecord($versionID, $tenderMasterID, true);
                $newRecords->each(function ($record) {
                    $newRecord = new SrmTenderDepartment();
                    foreach ($record->toArray() as $column => $value) {
                        if (!in_array($column, ['amd_id', 'id', 'version_id', 'is_deleted', 'level_no'])) {
                            $newRecord->{$column} = $value;
                        }
                    }
                    $newRecord->save();
                    $record->id = $newRecord->id;
                    $record->save();
                });
                return ['success' => true, 'message' => 'Tender department updated successfully'];
            });
        } catch (\Exception $ex) {
            return ['success' => false, 'message' => $ex->getMessage()];
        }
    }
    public static function updateTenderPurchaseRequest($tenderMasterID, $versionID){
        try {
            return DB::transaction(function () use ($tenderMasterID, $versionID) {
                $amdRecords = TenderPurchaseRequestEditLog::getAmendRecords($versionID, $tenderMasterID, false);
                $amdRecordIds = $amdRecords->pluck('id');
                $amdRecords->each(function ($amdRecord) {
                    $masterRecord = TenderPurchaseRequest::find($amdRecord->id);
                    if($masterRecord){
                        $masterRecord->fill($amdRecord->toArray());
                        $masterRecord->save();
                    }
                });

                TenderPurchaseRequest::whereNotIn('id', $amdRecordIds)
                    ->where('tender_id', $tenderMasterID)
                    ->delete();

                $excludedFields = ['amd_id', 'id', 'version_id', 'is_deleted', 'level_no'];
                $newRecords = TenderPurchaseRequestEditLog::getAmendRecords($versionID, $tenderMasterID, true);
                $newRecords->each(function ($record) use ($excludedFields) {
                    $data = collect($record->getAttributes())
                        ->except($excludedFields)
                        ->toArray();

                    $newRecord = TenderPurchaseRequest::create($data);
                    $record->id = $newRecord->id;
                    $record->save();
                });
                return ['success' => true, 'message' => 'Tender department updated successfully'];
            });
        } catch (\Exception $ex) {
            return ['success' => false, 'message' => $ex->getMessage()];
        }
    }
    public static function updateTenderBudgetItems($tenderMasterID, $versionID){
        try {
            return DB::transaction(function () use ($tenderMasterID, $versionID) {
                $amdRecords = TenderBudgetItemEditLog::getAmendRecords($versionID, $tenderMasterID, false);
                $amdRecordIds = $amdRecords->pluck('id');
                $amdRecords->each(function ($amdRecord) {
                    $masterRecord = SrmTenderBudgetItem::find($amdRecord->id);
                    if($masterRecord){
                        $masterRecord->fill($amdRecord->toArray());
                        $masterRecord->save();
                    }
                });

                SrmTenderBudgetItem::whereNotIn('id', $amdRecordIds)
                    ->where('tender_id', $tenderMasterID)
                    ->delete();

                $newRecords = TenderBudgetItemEditLog::getAmendRecords($versionID, $tenderMasterID, true);
                $newRecords->each(function ($record) {
                    $newRecord = new SrmTenderBudgetItem();
                    foreach ($record->toArray() as $column => $value) {
                        if (!in_array($column, ['amd_id', 'id', 'version_id', 'is_deleted', 'level_no'])) {
                            $newRecord->{$column} = $value;
                        }
                    }
                    $newRecord->save();
                    $record->id = $newRecord->id;
                    $record->save();
                });
                return ['success' => true, 'message' => 'Tender department updated successfully'];
            });
        } catch (\Exception $ex) {
            return ['success' => false, 'message' => $ex->getMessage()];
        }
    }
    public static function updateCalendarDateDetails($tenderMasterID, $versionID){
        try {
            return DB::transaction(function () use ($tenderMasterID, $versionID) {
                $amdRecords = CalendarDatesDetailEditLog::getAmendRecords($versionID, $tenderMasterID, false);
                $amdRecordIds = $amdRecords->pluck('id');
                $amdRecords->each(function ($amdRecord) {
                    $masterRecord = CalendarDatesDetail::find($amdRecord->id);
                    if($masterRecord){
                        $masterRecord->fill($amdRecord->toArray());
                        $masterRecord->save();
                    }
                });

                CalendarDatesDetail::whereNotIn('id', $amdRecordIds)
                    ->where('tender_id', $tenderMasterID)
                    ->delete();

                $newRecords = CalendarDatesDetailEditLog::getAmendRecords($versionID, $tenderMasterID, true);
                $newRecords->each(function ($record) {
                    $newRecord = new CalendarDatesDetail();
                    foreach ($record->getAttributes() as $column => $value) {
                        if (!in_array($column, [
                            'amd_id', 'id', 'level_no', 'version_id', 'modify_type',
                            'master_id', 'ref_log_id', 'tender_edit_version_id', 'is_deleted'
                        ])) {
                            $newRecord->{$column} = $value;
                        }
                    }
                    $newRecord->save();
                    $record->id = $newRecord->id;
                    $record->save();
                });
                return ['success' => true, 'message' => 'Tender department updated successfully'];
            });
        } catch (\Exception $ex) {
            return ['success' => false, 'message' => $ex->getMessage()];
        }
    }
    public static function updateTenderSiteVisitDate($versionID, $tenderMasterID){
        try {
            return DB::transaction(function () use ($versionID, $tenderMasterID) {
                $amdRecords = TenderSiteVisitDateEditLog::getAmendRecords($versionID, $tenderMasterID, false);
                $amdRecordIds = $amdRecords->pluck('id');
                $amdRecords->each(function ($amdRecord) {
                    $masterRecord = TenderSiteVisitDates::find($amdRecord->id);
                    if($masterRecord){
                        $masterRecord->fill($amdRecord->toArray());
                        $masterRecord->save();
                    }
                });

                TenderSiteVisitDates::whereNotIn('id', $amdRecordIds)
                    ->where('tender_id', $tenderMasterID)
                    ->delete();

                $newRecords = TenderSiteVisitDateEditLog::getAmendRecords($versionID, $tenderMasterID, true);
                $newRecords->each(function ($record) {
                    $newRecord = new TenderSiteVisitDates();
                    foreach ($record->getAttributes() as $column => $value) {
                        if (!in_array($column, ['amd_id', 'id', 'version_id', 'is_deleted', 'level_no',])) {
                            $newRecord->{$column} = $value;
                        }
                    }
                    $newRecord->save();
                    $record->id = $newRecord->id;
                    $record->save();
                });
                return ['success' => true, 'message' => 'Tender department updated successfully'];
            });
        } catch (\Exception $ex) {
            return ['success' => false, 'message' => $ex->getMessage()];
        }
    }
    public static function updateProcurementActivity($versionID, $tenderMasterID){
        try {
            return DB::transaction(function () use ($versionID, $tenderMasterID) {
                $amdRecords = ProcumentActivityEditLog::getAmendRecords($versionID, $tenderMasterID, false);
                $amdRecordIds = $amdRecords->pluck('id');
                $amdRecords->each(function ($amdRecord) {
                    $masterRecord = ProcumentActivity::find($amdRecord->id);
                    if($masterRecord){
                        $masterRecord->fill($amdRecord->toArray());
                        $masterRecord->save();
                    }
                });

                ProcumentActivity::whereNotIn('id', $amdRecordIds)
                    ->where('tender_id', $tenderMasterID)
                    ->delete();

                $newRecords = ProcumentActivityEditLog::getAmendRecords($versionID, $tenderMasterID, true);
                $newRecords->each(function ($record) {
                    $newRecord = new ProcumentActivity();
                    foreach ($record->toArray() as $column => $value) {
                        if (!in_array($column, [
                            'amd_id', 'id', 'level_no', 'version_id', 'modify_type', 'master_id', 'ref_log_id',
                            'is_deleted', 'updated_at', 'updated_by'
                        ])) {
                            $newRecord->{$column} = $value;
                        }
                    }
                    $newRecord->save();
                    $record->id = $newRecord->id;
                    $record->save();
                });
                return ['success' => true, 'message' => 'Tender department updated successfully'];
            });
        } catch (\Exception $ex) {
            return ['success' => false, 'message' => $ex->getMessage()];
        }
    }
    public static function updateTenderDocumentAttachment($tenderMasterID, $versionID, $documentSystemID)
    {
        try {
            return DB::transaction(function () use ($versionID, $tenderMasterID, $documentSystemID) {
                $amdRecords = DocumentAttachmentsEditLog::getAmendRecords($versionID, $tenderMasterID, $documentSystemID, false);
                $amdRecordIds = $amdRecords->pluck('id');
                $amdRecords->each(function ($amdRecord) {
                    $masterRecord = DocumentAttachments::find($amdRecord->id);
                    if ($masterRecord) {
                        $masterRecord->fill($amdRecord->only([
                            'attachmentDescription', 'path', 'originalFileName', 'myFileName', 'docExpirtyDate', 'attachmentType',
                            'sizeInKbs', 'isUploaded', 'pullFromAnotherDocument', 'parent_id', 'envelopType', 'order_number',
                            'isAutoCreateDocument'
                        ]));
                        $masterRecord->save();
                    }
                });
                DocumentAttachments::whereNotIn('attachmentID', $amdRecordIds)
                    ->where('documentSystemID', $documentSystemID)
                    ->where('documentSystemCode', $tenderMasterID)
                    ->delete();

                $newRecords = DocumentAttachmentsEditLog::getAmendRecords($versionID, $tenderMasterID, $documentSystemID, true);
                $newRecords->each(function ($record) {
                    $newRecord = new DocumentAttachments();
                    foreach ($record->getAttributes() as $column => $value) {
                        if (!in_array($column, [
                            'amd_id', 'id', 'level_no', 'version_id', 'modify_type', 'master_id', 'ref_log_id',
                            'created_at', 'updated_at', 'version_id', 'updated_by', 'is_deleted'
                        ])) {
                            $newRecord->{$column} = $value;
                        }
                    }
                    $newRecord->save();
                    $record->id = $newRecord->attachmentID;
                    $record->save();
                });
                return ['success' => true, 'message' => 'Document attachment updated successfully'];
            });
        } catch (\Exception $ex) {
            return ['success' => false, 'message' => $ex->getMessage()];
        }
    }
    public static function updateTenderDocumentTypeAssign($tenderMasterID, $versionID)
    {
        try {
            return DB::transaction(function () use ($tenderMasterID, $versionID) {
                $amdRecords = TenderDocumentTypeAssignLog::getAmendRecords($versionID, $tenderMasterID, false);
                $amdRecordIds = $amdRecords->pluck('id');
                $amdRecords->each(function ($amdRecord) {
                    $masterRecord = TenderDocumentTypeAssign::find($amdRecord->id);
                    if($masterRecord){
                        $masterRecord->fill($amdRecord->toArray());
                        $masterRecord->save();
                    }
                });
                TenderDocumentTypeAssign::whereNotIn('id', $amdRecordIds)
                    ->where('tender_id', $tenderMasterID)
                    ->delete();

                $newRecords = TenderDocumentTypeAssignLog::getAmendRecords($versionID, $tenderMasterID, true);
                $newRecords->each(function ($record) {
                    $newRecord = new TenderDocumentTypeAssign();
                    foreach ($record->getAttributes() as $column => $value) {
                        if (!in_array($column, [
                            'amd_id', 'id', 'level_no', 'version_id', 'modify_type', 'master_id', 'ref_log_id',
                            'is_deleted', 'updated_by'
                        ])) {
                            $newRecord->{$column} = $value;
                        }
                    }
                    $newRecord->updated_by = 0;
                    $newRecord->save();
                    $record->id = $newRecord->id;
                    $record->save();
                });
                return ['success' => true, 'message' => 'Tender document type assign updated successfully'];
            });
        } catch (\Exception $ex) {
            return ['success' => false, 'message' => $ex->getMessage()];
        }
    }
    public static function updateTenderSupplierAssignee($tenderMasterID, $versionID){
        try {
            return DB::transaction(function () use ($versionID, $tenderMasterID) {
                $amdRecords = TenderSupplierAssigneeEditLog::getAmendRecords($versionID, $tenderMasterID, false);
                $amdRecordIds = $amdRecords->pluck('id');
                $amdRecords->each(function ($amdRecord) {
                    $masterRecord = TenderSupplierAssignee::find($amdRecord->id);
                    if ($masterRecord) {
                        $masterRecord->fill($amdRecord->only([
                            'supplier_assigned_id', 'supplier_name', 'supplier_email', 'registration_number', 'registration_link_id', 'mail_sent'
                        ]));
                        $masterRecord->save();
                    }
                });

                TenderSupplierAssignee::whereNotIn('id', $amdRecordIds)
                    ->where('tender_master_id', $tenderMasterID)
                    ->delete();

                $excludedFields = ['amd_id', 'version_id', 'level_no', 'id', 'is_deleted'];
                $newRecords = TenderSupplierAssigneeEditLog::getAmendRecords($versionID, $tenderMasterID, true);
                $newRecords->each(function ($record) use ($excludedFields) {
                    $data = collect($record->getAttributes())
                        ->except($excludedFields)
                        ->toArray();

                    $newRecord = TenderSupplierAssignee::create($data);
                    $record->id = $newRecord->id;
                    $record->save();
                });
                return ['success' => true, 'message' => 'Document attachment updated successfully'];
            });
        } catch (\Exception $ex) {
            return ['success' => false, 'message' => $ex->getMessage()];
        }
    }
    public static function updateTenderPricingScheduleMaster($tenderMasterID, $versionID)
    {
        try{
            return DB::transaction(function () use ($tenderMasterID, $versionID) {
                $amdRecords = PricingScheduleMasterEditLog::getScheduleMasterAmd($tenderMasterID, $versionID, false);
                $amdRecordIds = $amdRecords->pluck('id');
                $amdRecords->each(function ($amdRecord) use ($tenderMasterID, $versionID){
                    $masterRecord = PricingScheduleMaster::find($amdRecord->id);
                    if ($masterRecord) {
                        $masterRecord->fill($amdRecord->only([
                            'scheduler_name', 'price_bid_format_id', 'schedule_mandatory', 'items_mandatory', 'status', 'boq_status'
                        ]));
                        $masterRecord->save();
                    }
                    self::updateTenderPricingScheduleDetails($tenderMasterID, $versionID, $amdRecord->id, $amdRecord->amd_id);
                });

                PricingScheduleMaster::whereNotIn('id', $amdRecordIds)
                    ->where('tender_id', $tenderMasterID)
                    ->delete();

                $newRecords = PricingScheduleMasterEditLog::getScheduleMasterAmd($tenderMasterID, $versionID, true);
                $newRecords->each(function ($record) use ($tenderMasterID, $versionID) {
                    $newRecord = new PricingScheduleMaster();
                    foreach ($record->getAttributes() as $column => $value) {
                        if (!in_array($column, [
                            'amd_id', 'id', 'level_no', 'id', 'tender_edit_version_id', 'modify_type', 'master_id',
                            'red_log_id', 'is_deleted'
                        ])) {
                            $newRecord->{$column} = $value;
                        }
                    }
                    $newRecord->save();
                    $record->id = $newRecord->id;
                    $record->save();
                    self::updateTenderPricingScheduleDetails($tenderMasterID, $versionID, $newRecord->id, $record->amd_id);
                });

                return ['success' => true, 'message' => 'Pricing Schedule updated successfully'];
            });
        } catch(\Exception $exception){
            return ['success' => false, 'message' => $exception->getMessage()];
        }
    }
    public static function updateTenderPricingScheduleDetails($tenderMasterID, $versionID, $scheduleID, $amd_scheduleID)
    {
        try{
            return DB::transaction(function () use ($tenderMasterID, $versionID, $scheduleID, $amd_scheduleID) {
                $amdRecords = PricingScheduleDetailEditLog::getPricingScheduleDetailAmdRecords(
                    $amd_scheduleID, $versionID, false, $tenderMasterID
                );
                $amdRecordIds = $amdRecords->pluck('id');
                $amdRecords->each(function ($amdRecord) use ($tenderMasterID, $versionID, $scheduleID, $amd_scheduleID){
                    $masterRecord = PricingScheduleDetail::find($amdRecord->id);
                    if ($masterRecord) {
                        $masterRecord->fill($amdRecord->only([
                            'bid_format_id', 'bid_format_detail_id', 'pricing_schedule_master_id', 'label', 'field_type',
                            'is_disabled', 'boq_applicable', 'formula_string', 'tender_ranking_line_item', 'description',
                            'updated_by', 'updated_at'
                        ]));
                        $masterRecord->save();
                    }
                    self::updateScheduleBidFormatDetails(
                        $versionID, $scheduleID, $amd_scheduleID, $amdRecord->id, $amdRecord->amd_id
                    );
                    self::updateTenderBoqItems($versionID, $tenderMasterID, $amdRecord->id, $amdRecord->amd_id);
                });
                PricingScheduleDetail::whereNotIn('id', $amdRecordIds)
                    ->where('tender_id', $tenderMasterID)
                    ->where('pricing_schedule_master_id', $scheduleID)
                    ->delete();

                $newRecords = PricingScheduleDetailEditLog::getPricingScheduleDetailAmdRecords(
                    $amd_scheduleID, $versionID, true, $tenderMasterID
                );
                $newRecords->each(function ($record) use ($scheduleID, $versionID, $amd_scheduleID, $tenderMasterID){
                    $newRecord = new PricingScheduleDetail();
                    foreach ($record->getAttributes() as $column => $value) {
                        if (!in_array($column, [
                            'amd_id', 'id', 'level_no', 'id', 'amd_pricing_schedule_master_id', 'pricing_schedule_master_id',
                            'tender_edit_version_id', 'modify_type', 'deleted_by', 'deleted_at', 'master_id', 'ref_log_id', 'is_deleted'
                        ])) {
                            $newRecord->{$column} = $value;
                        }
                    }
                    $newRecord->pricing_schedule_master_id = $scheduleID;
                    $newRecord->save();

                    $record->id = $newRecord->id;
                    $record->save();

                    self::updateScheduleBidFormatDetails(
                        $versionID, $scheduleID, $amd_scheduleID, $newRecord->id, $record->amd_id
                    );
                    self::updateTenderBoqItems($versionID, $tenderMasterID, $newRecord->id, $record->amd_id);

                });
                return ['success' => true, 'message' => 'Pricing Schedule detail updated successfully'];
            });
        } catch(\Exception $exception){
            return ['success' => false, 'message' => $exception->getMessage()];
        }
    }
    public static function updateScheduleBidFormatDetails(
        $versionID, $schedule_id, $amd_scheduleID, $detailID, $amd_detailID
    ){
        try{
            return DB::transaction(function () use ($versionID, $schedule_id, $amd_scheduleID, $detailID, $amd_detailID) {
                $amdRecords = ScheduleBidFormatDetailsLog::getAmendRecords($versionID, $amd_scheduleID, $amd_detailID, false);
                $amdRecordIds = $amdRecords->pluck('id');
                $amdRecords->each(function ($amdRecord) use ($versionID){
                    $masterRecord = ScheduleBidFormatDetails::find($amdRecord->id);
                    if ($masterRecord) {
                        $masterRecord->fill($amdRecord->only(['value']));
                        $masterRecord->save();
                    }
                });
                ScheduleBidFormatDetails::whereNotIn('id', $amdRecordIds)->where('schedule_id', $schedule_id)
                    ->where('bid_format_detail_id', $detailID)->delete();

                $newRecords = ScheduleBidFormatDetailsLog::getAmendRecords(
                    $versionID, $amd_scheduleID, $amd_detailID, true
                );
                $newRecords->each(function ($record) use ($schedule_id, $detailID){
                    $newRecord = new ScheduleBidFormatDetails();
                    foreach ($record->getAttributes() as $column => $value) {
                        if (!in_array($column, [
                            'amd_id', 'id', 'level_no', 'bid_format_detail_id', 'amd_bid_format_detail_id', 'schedule_id', 'amd_pricing_schedule_master_id',
                            'tender_edit_version_id', 'modify_type', 'master_id', 'red_log_id', 'created_at', 'updated_at', 'updated_by', 'is_deleted'
                        ])) {
                            $newRecord->{$column} = $value;
                        }
                    }
                    $newRecord->bid_format_detail_id = $detailID;
                    $newRecord->schedule_id = $schedule_id;
                    $newRecord->created_at = now();
                    $newRecord->created_by = Helper::getEmployeeID();
                    $newRecord->save();

                    $record->id = $newRecord->id;
                    $record->save();

                });
                return ['success' => true, 'message' => 'Schedule bid format details updated successfully'];
            });
        } catch(\Exception $exception){
            return ['success' => false, 'message' => $exception->getMessage()];
        }
    }
    public static function updateTenderBoqItems($versionID, $tenderMasterID, $detailID, $amd_detailID){
        try{
            return DB::transaction(function () use ($versionID, $tenderMasterID, $detailID, $amd_detailID){
                $amdRecords = TenderBoqItemsEditLog::getAmendRecords($tenderMasterID, $versionID, $amd_detailID, false);
                $amdRecordIds = $amdRecords->pluck('id');
                $amdRecords->each(function ($amdRecord) use ($versionID){
                    $masterRecord = TenderBoqItems::find($amdRecord->id);
                    if ($masterRecord) {
                        $masterRecord->fill($amdRecord->only([
                            'item_name', 'description', 'uom', 'qty', 'tender_ranking_line_item', 'purchase_request_id', 'item_primary_code', 'origin'
                        ]));
                        $masterRecord->save();
                    }
                });
                TenderBoqItems::whereNotIn('id', $amdRecordIds)
                    ->where('tender_id', $tenderMasterID)
                    ->where('main_work_id', $detailID)
                    ->delete();
                $newRecords = TenderBoqItemsEditLog::getAmendRecords(
                    $tenderMasterID, $versionID, $amd_detailID, true
                );
                $newRecords->each(function ($record) use ($detailID){
                    $newRecord = new TenderBoqItems();
                    foreach ($record->getAttributes() as $column => $value) {
                        if (!in_array($column, [
                            'amd_id', 'id', 'level_no', 'main_work_id', 'amd_main_work_id', 'company_id', 'tender_edit_version_id',
                            'modify_type', 'master_id', 'ref_log_id', 'is_deleted'
                        ])) {
                            $newRecord->{$column} = $value;
                        }
                    }

                    $newRecord->main_work_id = $detailID;
                    $newRecord->save();

                    $record->id = $newRecord->id;
                    $record->save();
                });
                return ['success' => true, 'message' => 'Tender Boq items updated successfully'];
            });
        } catch(\Exception $exception){
            return ['success' => false, 'message' => $exception->getMessage()];
        }
    }
    public static function updateCirculars($tenderMasterID, $versionID)
    {
        try{
            return DB::transaction(function () use ($tenderMasterID, $versionID) {
                $amdRecords = TenderCircularsEditLog::getAmendmentRecords($tenderMasterID, $versionID, false);
                $amdRecordIds = $amdRecords->pluck('id');
                $amdRecords->each(function ($amdRecord) use ($tenderMasterID, $versionID){
                    $masterRecord = TenderCirculars::find($amdRecord->id);
                    if ($masterRecord) {
                        $masterRecord->fill($amdRecord->only([
                            'circular_name', 'tender_id', 'description', 'attachment_id', 'status'
                        ]));
                        $masterRecord->save();
                    }
                });

                TenderCirculars::whereNotIn('id', $amdRecordIds)
                    ->where('tender_id', $tenderMasterID)
                    ->delete();

                $newRecords = TenderCircularsEditLog::getAmendmentRecords($tenderMasterID, $versionID, true);
                $newRecords->each(function ($record) use ($tenderMasterID, $versionID){
                    $newRecord = new TenderCirculars();
                    foreach($record->getAttributes() as $column => $value){
                        if(!in_array($column, [
                            'amd_id', 'id', 'level_no', 'vesion_id', 'modify_type', 'master_id', 'ref_log_id', 'is_deleted'
                        ])){
                            $newRecord->{$column} = $value;
                        }

                        $newRecord->save();
                        $record->id = $newRecord->id;
                        $record->save();
                        self::updateCircularAmendment($tenderMasterID, $versionID, $record->amd_id, $newRecord->id);
                        self::updateCircularSuppliers($tenderMasterID, $versionID, $record->amd_id, $newRecord->id);
                    }
                });
                return ['success' => true, 'message' => 'Tender Circular updated successfully'];
            });
        } catch(\Exception $exception){
            return ['success' => false, 'message' => 'Unexpected Error: ' . $exception->getMessage()];
        }
    }
    public static function updateCircularAmendment($tenderMasterID, $versionID, $circular_amd_id, $new_circularID){
        try{
            return DB::transaction(function () use ($tenderMasterID, $versionID, $circular_amd_id, $new_circularID) {
                $newRecords = CircularAmendmentsEditLog::getAmendRecords($circular_amd_id, $versionID, true);
                $newRecords->each(function ($record) use ($new_circularID) {
                    $newRecord = new CircularAmendments();
                    foreach($record->getAttributes() as $column => $value){
                        if(!in_array($column, [
                            'amd_id', 'id', 'level_no', 'vesion_id', 'modify_type', 'master_id',
                            'ref_log_id', 'is_deleted', 'circular_id', 'amendment_id'
                        ])){
                            $newRecord->{$column} = $value;
                        }
                        $attachment = DocumentAttachmentsEditLog::find($record['amendment_id']);

                        $newRecord->circular_id = $new_circularID;
                        $newRecord->amendment_id = $attachment['id'];
                        $newRecord->save();

                        $record->id = $newRecord->id;
                        $record->save();
                    }
                });
                return ['success' => true, 'message' => 'Circular Amendment updated successfully'];
            });
        } catch(\Exception $exception){
            return ['success' => false, 'message' => 'Unexpected Error: ' . $exception->getMessage()];
        }
    }
    public static function updateCircularSuppliers($tenderMasterID, $versionID, $circular_amd_id, $new_circularID){
        try{
            return DB::transaction(function () use ($tenderMasterID, $versionID, $circular_amd_id, $new_circularID) {
                $newRecords = CircularSuppliersEditLog::getAmendRecords($circular_amd_id, $versionID, true);
                $newRecords->each(function ($record) use ($new_circularID) {
                    $newRecord = new CircularSuppliers();
                    foreach($record->getAttributes() as $column => $value){
                        if(!in_array($column, [
                            'amd_id', 'version_id', 'level_no', 'id', 'is_deleted'
                        ])){
                            $newRecord->{$column} = $value;
                        }
                        $newRecord->circular_id = $new_circularID;
                        $newRecord->save();

                        $record->id = $newRecord->id;
                        $record->save();
                    }
                });
                return ['success' => true, 'message' => 'Circular Amendment updated successfully'];
            });
        } catch(\Exception $exception){
            return ['success' => false, 'message' => 'Unexpected Error: ' . $exception->getMessage()];
        }
    }
    public static function updateEvaluationCriteriaDetail($tenderMasterID, $versionID){
        try{
            return DB::transaction(function () use ($tenderMasterID, $versionID) {
                $amdRecords = EvaluationCriteriaDetailsEditLog::getAmendRecords($tenderMasterID, $versionID, false);
                $amdRecordIds = $amdRecords->pluck('id');
                $amdRecords->each(function ($amdRecord) use ($tenderMasterID, $versionID){
                    $masterRecord = EvaluationCriteriaDetails::find($amdRecord->id);
                    if ($masterRecord) {
                        $masterRecord->fill($amdRecord->only([
                            'description', 'critera_type_id', 'answer_type_id', 'level', 'is_final_level', 'weightage',
                            'passing_weightage', 'min_value', 'max_value', 'sort_order', 'evaluation_criteria_master_id'
                        ]));
                        $masterRecord->save();
                    }
                });
                EvaluationCriteriaDetails::whereNotIn('id', $amdRecordIds)
                    ->where('tender_id', $tenderMasterID)
                    ->delete();
                $newRecords = EvaluationCriteriaDetailsEditLog::getAmendRecords($tenderMasterID, $versionID, true);
                $newRecords->each(function ($record) use ($tenderMasterID, $versionID){
                    $newRecord = new EvaluationCriteriaDetails();
                    foreach($record->getAttributes() as $column => $value){
                        if(!in_array($column, [
                            'amd_id', 'id', 'level_no', 'tender_version_id', 'modify_type', 'master_id', 'ref_log_id', 'is_deleted'
                        ])){
                            $newRecord->{$column} = $value;
                        }

                        if($record->parent_id != 0){
                            $masterRecord = EvaluationCriteriaDetailsEditLog::find($record->parent_id);
                            $newRecord->parent_id = $masterRecord['id'];
                        }

                        $newRecord->save();
                        $record->id = $newRecord->id;
                        $record->save();
                    }
                });

                return ['success' => true, 'message' => 'Evacuation Criteria updated successfully'];
            });
        } catch(\Exception $exception){
            return ['success' => false, 'message' => 'Unexpected Error: ' . $exception->getMessage()];
        }
    }
    public static function updateEvaluationCriteriaScore($versionID){
        try{
            return DB::transaction(function () use ($versionID) {
                $amdRecords = EvacuationCriteriaScoreConfigLog::getAmendRecords($versionID, false);
                $deletedRecordIds = EvacuationCriteriaScoreConfigLog::getDeletedIds($versionID);
                $amdRecords->each(function ($amdRecord) {
                    $masterRecord = EvaluationCriteriaScoreConfig::find($amdRecord->id);
                    if ($masterRecord) {
                        $masterRecord->fill($amdRecord->only(['label', 'score']));
                        $masterRecord->save();
                    }
                });

                EvaluationCriteriaScoreConfig::whereIn('id', $deletedRecordIds)->delete();

                $newRecords = EvacuationCriteriaScoreConfigLog::getAmendRecords($versionID, true);
                $newRecords->each(function ($record) use ($versionID){
                    $newRecord = new EvaluationCriteriaScoreConfig();
                    foreach($record->getAttributes() as $column => $value){
                        if(!in_array($column, [
                            'amd_id', 'version_id', 'level_no', 'id', 'is_deleted'
                        ])){
                            $newRecord->{$column} = $value;
                        }
                        $getMasterID = EvaluationCriteriaDetailsEditLog::find($record->criteria_detail_id);
                        $newRecord->criteria_detail_id = $getMasterID['id'];
                        $newRecord->save();
                        $record->id = $newRecord->id;
                        $record->save();
                    }
                });

                return ['success' => true, 'message' => 'Evacuation Criteria updated successfully'];
            });
        } catch(\Exception $exception){
            return ['success' => false, 'message' => 'Unexpected Error: ' . $exception->getMessage()];
        }
    }
    public static function getFieldPermissions($tender_id, $tenderMaster): array{
        $isPublished = $tenderMaster->published_yn == 1;

        $fields = [
            'tender_strategy', 'alternative_solution', 'minimum_approval', 'weightage', 'passing_weightage',
            'tender_users', 'tender_general_info', 'prebid_method', 'tender_calendar_days', 'tender_fee',
            'criteria_to_supplier', 'pricing_schedule', 'go_no_go', 'technical_evaluation', 'tender_document', 'assign_suppliers',
        ];

        $permissions = array_fill_keys($fields, true);
        if (!$isPublished) {
            return $permissions;
        }

        $conditions = self::checkConditions($tender_id, $tenderMaster);
        $isOpeningDateValid = $conditions['checkOpeningDate'] ?? false;
        $isClosingDateValid = $conditions['checkClosingDate'] ?? false;
        $isSupplierProceeded = $conditions['tenderPurchasedOrProceed'];
        $isSupplierSubmittedBid = $conditions['isTenderBidSubmitted'];
        $isSupplierRankingNotCompleted = $conditions['isSupplierRankingNotCompleted'];

        $permissions['tender_strategy'] = !$isSupplierProceeded;
        $permissions['alternative_solution'] = !$isSupplierSubmittedBid;
        $permissions['minimum_approval'] = $isSupplierRankingNotCompleted;
        $permissions['weightage'] = !$isSupplierSubmittedBid;
        $permissions['passing_weightage'] = $isSupplierRankingNotCompleted;
        $permissions['prebid_method'] = $isOpeningDateValid;
        $permissions['tender_fee'] = !$isSupplierProceeded;
        $permissions['criteria_to_supplier'] = !$isSupplierProceeded;
        $permissions['pricing_schedule'] = !$isSupplierProceeded;
        $permissions['go_no_go'] = !$isSupplierProceeded;
        $permissions['technical_evaluation'] = !$isSupplierProceeded;
        $permissions['tender_document'] = !$isSupplierProceeded;
        $permissions['assign_suppliers'] = $isClosingDateValid;

        return $permissions;
    }
}
