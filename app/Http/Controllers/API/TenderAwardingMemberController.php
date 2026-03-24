<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\AppBaseController;
use App\Services\TenderAwardingMemberService;
use Illuminate\Http\Request;
use App\Http\Requests\StoreAwardingMemberRequest;
use App\Http\Requests\GetAwardingMembersRequest;
use App\Http\Requests\DeleteAwardingMemberRequest;
use App\Http\Requests\UpdateAwardingStatusRequest;
use App\Http\Requests\AwardingApprovalCountRequest;


class TenderAwardingMemberController extends AppBaseController
{
    protected $tenderAwardingMemberService;

    public function __construct(TenderAwardingMemberService $tenderAwardingMemberService)
    {
        $this->tenderAwardingMemberService = $tenderAwardingMemberService;
    }

    /**
     * Store awarding members
     */
    public function store(StoreAwardingMemberRequest $request)
    {
        try {
            $result = $this->tenderAwardingMemberService
                ->storeAwardingMembers($request->all());

            return $result['success']
                ? $this->sendResponse([], $result['message'])
                : $this->sendError($result['message'], $result['code']);

        } catch (\Exception $ex) {
            return $this->sendError(trans('common.something_went_wrong'), 500);
        }
    }

    /**
     * Get awarding members
     */
    public function getAwardingMembers(GetAwardingMembersRequest $request)
    {
        try {
            $data = $this->tenderAwardingMemberService
                ->getAwardingMembers($request);

            return $this->sendResponse($data, 'Awarding members retrieved successfully');

        } catch (\Exception $ex) {
            return $this->sendError(trans('common.something_went_wrong'), 500);
        }
    }

    /**
     * Delete awarding member
     */
    public function deleteAwardingMember(DeleteAwardingMemberRequest $request)
    {
        try {
            $result = $this->tenderAwardingMemberService
                ->deleteAwardingMember($request->all());

            return $result['success']
                ? $this->sendResponse([], $result['message'])
                : $this->sendError($result['message'], $result['code']);

        } catch (\Exception $ex) {
            return $this->sendError(trans('common.something_went_wrong'), 500);
        }
    }

    /**
     * Get awarding approval count
     */
    public function getAwardingApprovalCount(AwardingApprovalCountRequest $request)
    {
        try {
            $count = $this->tenderAwardingMemberService
                ->getAwardingApprovalCount($request->all());

            return $this->sendResponse($count, 'Awarding approval count retrieved successfully');

        } catch (\Exception $ex) {
            return $this->sendError(trans('common.something_went_wrong'), 500);
        }
    }

    /**
     * Update awarding member status
     */
    public function updateAwardingStatus(UpdateAwardingStatusRequest $request)
    {
        try {
            $result = $this->tenderAwardingMemberService
                ->updateAwardingStatus($request->all());

            return $result['success']
                ? $this->sendResponse([], $result['message'])
                : $this->sendError($result['message'], $result['code']);

        } catch (\Exception $ex) {
            return $this->sendError(trans('common.something_went_wrong'), 500);
        }
    }

    /**
     * Delete all awarding members
     */
    public function deleteAllAwardingMembers(Request $request)
    {
        try {
            $result = $this->tenderAwardingMemberService
                ->deleteAllAwardingMembers($request->all());

            return $result['success']
                ? $this->sendResponse([], $result['message'])
                : $this->sendError($result['message'], $result['code']);

        } catch (\Exception $ex) {
            return $this->sendError(trans('common.something_went_wrong'), 500);
        }
    }
}
