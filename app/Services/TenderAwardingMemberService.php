<?php

namespace App\Services;

use App\Models\SrmTenderAwardingMember;
use App\Models\SrmTenderAwardingMemberEditLog;
use App\Models\SrmTenderBidEmployeeDetails;
use App\Models\TenderConfirmationDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Helper\Helper;
use Illuminate\Support\Facades\Log;
use App\Services\SrmDocumentModifyService;

class TenderAwardingMemberService
{
    protected $documentModifyService;

    public function __construct(SrmDocumentModifyService $documentModifyService)
    {
        $this->documentModifyService = $documentModifyService;
    }
    /**
     * Store awarding members
     *
     * @param array $input
     * @return array
     */
    public function storeAwardingMembers($input)
    {
        try {
            return DB::transaction(function () use ($input) {

                $tenderID  = $input['tender_id'] ?? 0;
                $versionID = $input['versionID'] ?? 0;
                $requestData = $this->documentModifyService->checkForEditOrAmendRequest($tenderID);

                $enableChangeRequest = ($versionID > 0) || ($requestData['enableRequestChange'] ?? false);
                $versionID = $versionID > 0 ? $versionID : ($requestData['versionID'] ?? 0);

                $userIds = Helper::getArrayIds(
                    $input['user_id'] ?? $input['emp_id'] ?? []
                );

                if (empty($userIds)) {
                    return [
                        'success' => true,
                        'message' => trans('srm_tender_rfx.awarding_member_created_successfully'),
                        'code' => 200
                    ];
                }

                $insertData = [];
                $now = Carbon::now();
                $createdBy = Helper::getEmployeeID();

                foreach ($userIds as $userId) {
                    $existing = $enableChangeRequest
                        ? SrmTenderAwardingMemberEditLog::getUserTenderAwardingMember($tenderID, $userId, $versionID)
                        : SrmTenderAwardingMember::getAwardingMember($tenderID, $userId);

                    if ($existing) {
                        continue;
                    }

                    $data = [
                        'tender_id'  => $tenderID,
                        'user_id'    => $userId,
                        'status'     => 0,
                        'created_by' => $createdBy,
                        'created_at'=> $now,
                        'updated_at'=> $now,
                    ];

                    if ($enableChangeRequest) {
                        $data['version_id'] = $versionID;
                        $data['level_no']   = 1;
                        $data['id']         = null;
                    }

                    $insertData[] = $data;
                }

                if (!empty($insertData)) {
                    $enableChangeRequest
                        ? SrmTenderAwardingMemberEditLog::insert($insertData)
                        : SrmTenderAwardingMember::insert($insertData);
                }

                return [
                    'success' => true,
                    'message' => trans('srm_tender_rfx.awarding_member_created_successfully'),
                    'code' => 200
                ];
            });
        } catch (\Exception $ex) {
            return [
                'success' => false,
                'message' => trans(
                    'srm_tender_rfx.unable_to_create',
                    ['message' => $ex->getMessage()]
                ),
                'code' => $ex->getCode() == 422 ? 422 : 500
            ];
        }
    }

    /**
     * Get awarding members for a tender
     *
     * @param Request $request
     * @return array
     */
    public function getAwardingMembers($request)
    {
        try {
            $tenderID = $request->input('tender_id') ?? 0;
            $versionId = $request->input('versionID') ?? 0;
            $editOrAmend = $versionId > 0;

            $members = $editOrAmend ?
                SrmTenderAwardingMemberEditLog::getAwardingMembers($tenderID, $versionId) :
                SrmTenderAwardingMember::getAwardingMembers($tenderID);

            $memberUserIds = collect($members)->pluck('user_id')->filter()->unique()->values();
            $confirmationDetails = TenderConfirmationDetail::getTenderConfirmationDetails(
                $tenderID, TenderConfirmationDetail::MODULE_AWARDING_APPROVAL, $memberUserIds
            );

            return $members->map(function ($member) use ($confirmationDetails) {
                $confirmation = $confirmationDetails->get($member->user_id);
                return [
                    'id' => $member->id ?? null,
                    'tender_id' => $member->tender_id,
                    'emp_id' => $member->user_id ?? $member->user_id,
                    'user_id' => $member->user_id ?? $member->user_id,
                    'status' => $member->status ?? 0,
                    'awarding_remarks' => $member->awarding_remarks ?? null,
                    'tender_award_commite_mem_status' => $member->status ?? 0,
                    'tender_award_commite_mem_comment' => $member->awarding_remarks ?? null,
                    'confirmation_comment' => $confirmation ? $confirmation->comment : ($member->awarding_remarks ?? null),
                    'confirmation_action_at' => $confirmation ? $confirmation->action_at : null,
                    'employee' => $member->employee ?? null,
                    'created_at' => $member->created_at ?? null,
                    'updated_at' => $member->updated_at ?? null,
                ];
            })->toArray();
        } catch (\Exception $ex) {
            Log::error('Error getting awarding members: ' . $ex->getMessage());
            return [];
        }
    }

    /**
     * Delete awarding member
     *
     * @param Request $request
     * @return array
     */
    public function deleteAwardingMember($request)
    {
        try {
            return DB::transaction(function () use ($request) {

                $tenderID = $request['tender_id'] ?? 0;
                $memberID = $request['user_id'] ?? 0;

                $requestData = $this->documentModifyService
                    ->checkForEditOrAmendRequest($tenderID);

                $enableChangeRequest = $requestData['enableRequestChange'] ?? false;
                $versionID = $requestData['versionID'] ?? 0;

                $member = $enableChangeRequest
                    ? SrmTenderAwardingMemberEditLog::getUserTenderAwardingMember(
                        $tenderID,
                        $memberID,
                        $versionID
                    )
                    : SrmTenderAwardingMember::getAwardingMember(
                        $tenderID,
                        $memberID
                    );

                if (!$member) {
                    return [
                        'success' => false,
                        'message' => trans('srm_tender_rfx.awarding_member_not_found'),
                        'code' => 404
                    ];
                }

                if ($enableChangeRequest) {
                    $member->is_deleted = 1;
                    $member->save();
                } else {
                    $member->delete();
                }

                return [
                    'success' => true,
                    'message' => trans('srm_tender_rfx.awarding_member_deleted_successfully'),
                    'code' => 200
                ];
            });
        } catch (\Exception $ex) {
            return [
                'success' => false,
                'message' => trans('srm_tender_rfx.unable_to_delete'),
                'code' => 500
            ];
        }
    }

    /**
     * Get awarding approval count
     *
     * @param Request $request
     * @return int
     */
    public function getAwardingApprovalCount($request)
    {
        try {
            $tenderID = $request['tender_id'] ?? 0;
            return SrmTenderAwardingMember::getApprovedAwardingMembers($tenderID);
        } catch (\Exception $ex) {
            Log::error('Error getting awarding approval count: ' . $ex->getMessage());
            return 0;
        }
    }

    /**
     * Update awarding member status
     *
     * @param array $input
     * @return array
     */
    public function updateAwardingStatus($input)
    {
        try {
            return DB::transaction(function () use ($input) {
                $tenderID = $input['tender_id'] ?? 0;
                $userID = $input['user_id'] ?? $input['emp_id'] ?? 0;
                $status = $input['status'] ?? 0;
                $remarks = $input['remarks'] ?? $input['comments'] ?? null;
                $member = SrmTenderAwardingMember::getAwardingMember($tenderID, $userID);

                $member->status = $status;
                $member->awarding_remarks = $remarks;
                $member->updated_at = Carbon::now();
                $member->save();

                return [
                    'success' => true,
                    'message' => trans('srm_tender_rfx.awarding_status_updated_successfully'),
                    'code' => 200
                ];
            });
        } catch (\Exception $ex) {
            return [
                'success' => false,
                'message' => trans('srm_tender_rfx.unable_to_update', ['message' => $ex->getMessage()]),
                'code' => 500
            ];
        }
    }

    /**
     * Delete all awarding members for a tender
     *
     * @param Request $request
     * @return array
     */
    public function deleteAllAwardingMembers($request)
    {
        try {
            return DB::transaction(function () use ($request) {
                $tenderID = $request['tenderID'] ?? $request['tender_id'] ?? 0;
                $requestData = $this->documentModifyService->checkForEditOrAmendRequest($tenderID);
                $enableChangeRequest = $requestData['enableRequestChange'];
                $versionID = $requestData['versionID'] ?? 0;

                if ($enableChangeRequest) {
                    SrmTenderAwardingMemberEditLog::where('tender_id', $tenderID)
                        ->where('version_id', $versionID)
                        ->update(['is_deleted' => 1]);
                } else {
                    SrmTenderAwardingMember::where('tender_id', $tenderID)->delete();
                }

                return [
                    'success' => true,
                    'message' => trans('srm_tender_rfx.all_awarding_members_deleted_successfully', [], 'en'),
                    'code' => 200
                ];
            });
        } catch (\Exception $ex) {
            return [
                'success' => false,
                'message' => trans('srm_tender_rfx.unable_to_delete', ['message' => $ex->getMessage()], 'en'),
                'code' => 500
            ];
        }
    }
    public function saveTenderAwardingMemberHistory($tenderID, $version_id=null){
        try {
            return DB::transaction(function () use ($tenderID, $version_id) {
                $awardingMembers = SrmTenderAwardingMember::getAwardingMembers($tenderID);
                if(!empty($awardingMembers)){
                    foreach($awardingMembers as $record){
                        $levelNo = SrmTenderAwardingMemberEditLog::getLevelNo($record['id']);
                        $recordData = $record->toArray();
                        $recordData['level_no'] = $levelNo;
                        $recordData['id'] = $record['id'];
                        $recordData['version_id'] = $version_id;
                        SrmTenderAwardingMemberEditLog::create($recordData);
                    }
                }
                return ['success' => false, 'message' => trans('srm_tender_rfx.success')];
            });
        } catch (\Exception $ex){
            return ['success' => false, 'message' => $ex->getMessage()];
        }
    }
}
