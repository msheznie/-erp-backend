<?php

/**
 * =============================================
 * -- File Name : ApprovalGroupsAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Approval Setup
 * -- Author : Mubashir
 * -- Create date : 23 - April 2018
 * -- Description : This file contains the all CRUD for Approval Group.
 * -- REVISION HISTORY
 * -- Date: 24-Feb 2020 By: Zakeeul Description: Added new function getDocumentAccessGroup(),
 */

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateApprovalGroupsAPIRequest;
use App\Http\Requests\API\UpdateApprovalGroupsAPIRequest;
use App\Models\ApprovalGroups;
use App\Models\DepartmentMaster;
use App\Models\DocumentMaster;
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

        return $this->sendResponse($approvalGroups->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.approval_groups')]));
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
        $input = array_except($input,'document');
        $input = $this->convertArrayToValue($input);
        $document = DocumentMaster::find($input["documentSystemID"]);
        $approvalGroups = "";
        if (isset($request->rightsGroupId))
        {
            $id = $request->rightsGroupId;
            $approvalGroups = $this->approvalGroupsRepository->findWithoutFail($id);
            if (empty($approvalGroups)) {
                return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.approval_groups')]));
            }
            $approvalGroups->documentSystemID = $input["documentSystemID"];
            $approvalGroups->rightsGroupDes = $input["rightsGroupDes"];
            $approvalGroups->departmentSystemID = $document->departmentSystemID;
            $approvalGroups->departmentID = $document->departmentID;
            $approvalGroups->documentID = $document->documentID;
            $approvalGroups->sortOrder =  $input["sortOrder"];
            $approvalGroups->save();
        }else{
            $input["departmentSystemID"] = $document->departmentSystemID;
            $input["departmentID"] = $document->departmentID;
            $input["documentID"] = $document->documentID;
            $approvalGroups = $this->approvalGroupsRepository->create($input);
        }
        return $this->sendResponse($approvalGroups->toArray(), trans('custom.save', ['attribute' => trans('custom.approval_groups')]));
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
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.approval_groups')]));
        }

        return $this->sendResponse($approvalGroups->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.approval_groups')]));
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
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.approval_groups')]));
        }

        $approvalGroups = $this->approvalGroupsRepository->update($input, $id);

        return $this->sendResponse($approvalGroups->toArray(), trans('custom.update', ['attribute' => trans('custom.approval_groups')]));
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
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.approval_groups')]));
        }

        $approvalGroups->delete();

        return $this->sendResponse($id, trans('custom.delete', ['attribute' => trans('custom.approval_groups')]));
    }

    public function getAllApprovalGroup(){
        $approvalGroups = ApprovalGroups::all();
        return $this->sendResponse($approvalGroups->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.approval_groups')]));
    }

    public function getApprovalGroupByCompanyDatatable(Request $request){
        $search = $request->input('search.value');
        $approvalGroup = ApprovalGroups::with(['document' => function ($query) use ($search){
            if ($search) {
                $query->where('documentDescription', 'LIKE', "%{$search}%");
            }
        }])->orderBy('rightsGroupId','desc');

        if ($search) {
            $approvalGroup = $approvalGroup->where('rightsGroupDes', 'LIKE', "%{$search}%");
        }

        $input = $request->all();

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        return \DataTables::eloquent($approvalGroup)
            ->order(function ($query) use ($input) {
                if (request()->has('order') ) {
                    if($input['order'][0]['column'] == 0)
                    {
                        $query->orderBy('rightsGroupId', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->addColumn('Actions', 'Actions', "Actions")
            ->make(true);
    }

    public function getDocumentAccessGroup(Request $request)
    {
        $approvalGroups = ApprovalGroups::where('documentSystemID', $request['documentSystemID'])->get();
        if (empty($approvalGroups)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.document')]));
        }
        return $this->sendResponse($approvalGroups, trans('custom.retrieve', ['attribute' => trans('custom.document')]));
    }
}
