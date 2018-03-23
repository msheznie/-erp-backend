<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateApprovalGroupsAPIRequest;
use App\Http\Requests\API\UpdateApprovalGroupsAPIRequest;
use App\Models\ApprovalGroups;
use App\Repositories\ApprovalGroupsRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class ApprovalGroupsController
 * @package App\Http\Controllers\API
 */

class ApprovalGroupsAPIController extends AppBaseController
{
    /** @var  ApprovalGroupsRepository */
    private $approvalGroupsRepository;

    public function __construct(ApprovalGroupsRepository $approvalGroupsRepo)
    {
        $this->approvalGroupsRepository = $approvalGroupsRepo;
    }

    /**
     * Display a listing of the ApprovalGroups.
     * GET|HEAD /approvalGroups
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->approvalGroupsRepository->pushCriteria(new RequestCriteria($request));
        $this->approvalGroupsRepository->pushCriteria(new LimitOffsetCriteria($request));
        $approvalGroups = $this->approvalGroupsRepository->all();

        return $this->sendResponse($approvalGroups->toArray(), 'Approval Groups retrieved successfully');
    }

    /**
     * Store a newly created ApprovalGroups in storage.
     * POST /approvalGroups
     *
     * @param CreateApprovalGroupsAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateApprovalGroupsAPIRequest $request)
    {
        $input = $request->all();

        $approvalGroups = $this->approvalGroupsRepository->create($input);

        return $this->sendResponse($approvalGroups->toArray(), 'Approval Groups saved successfully');
    }

    /**
     * Display the specified ApprovalGroups.
     * GET|HEAD /approvalGroups/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var ApprovalGroups $approvalGroups */
        $approvalGroups = $this->approvalGroupsRepository->findWithoutFail($id);

        if (empty($approvalGroups)) {
            return $this->sendError('Approval Groups not found');
        }

        return $this->sendResponse($approvalGroups->toArray(), 'Approval Groups retrieved successfully');
    }

    /**
     * Update the specified ApprovalGroups in storage.
     * PUT/PATCH /approvalGroups/{id}
     *
     * @param  int $id
     * @param UpdateApprovalGroupsAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateApprovalGroupsAPIRequest $request)
    {
        $input = $request->all();

        /** @var ApprovalGroups $approvalGroups */
        $approvalGroups = $this->approvalGroupsRepository->findWithoutFail($id);

        if (empty($approvalGroups)) {
            return $this->sendError('Approval Groups not found');
        }

        $approvalGroups = $this->approvalGroupsRepository->update($input, $id);

        return $this->sendResponse($approvalGroups->toArray(), 'ApprovalGroups updated successfully');
    }

    /**
     * Remove the specified ApprovalGroups from storage.
     * DELETE /approvalGroups/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var ApprovalGroups $approvalGroups */
        $approvalGroups = $this->approvalGroupsRepository->findWithoutFail($id);

        if (empty($approvalGroups)) {
            return $this->sendError('Approval Groups not found');
        }

        $approvalGroups->delete();

        return $this->sendResponse($id, 'Approval Groups deleted successfully');
    }
}
