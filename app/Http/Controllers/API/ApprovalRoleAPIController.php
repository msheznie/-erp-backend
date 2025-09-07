<?php
/**
 * =============================================
 * -- File Name : ApprovalRoleAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Approval Setup
 * -- Author : Mubashir
 * -- Create date : 23 - April 2018
 * -- Description : This file contains the all CRUD for Approval Role
 * -- REVISION HISTORY
 * --
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateApprovalRoleAPIRequest;
use App\Http\Requests\API\UpdateApprovalRoleAPIRequest;
use App\Models\ApprovalRole;
use App\Repositories\ApprovalRoleRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class ApprovalRoleController
 * @package App\Http\Controllers\API
 */

class ApprovalRoleAPIController extends AppBaseController
{
    /** @var  ApprovalRoleRepository */
    private $approvalRoleRepository;

    public function __construct(ApprovalRoleRepository $approvalRoleRepo)
    {
        $this->approvalRoleRepository = $approvalRoleRepo;
    }

    /**
     * Display a listing of the ApprovalRole.
     * GET|HEAD /approvalRoles
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->approvalRoleRepository->pushCriteria(new RequestCriteria($request));
        $this->approvalRoleRepository->pushCriteria(new LimitOffsetCriteria($request));
        $approvalRoles = $this->approvalRoleRepository->all();

        return $this->sendResponse($approvalRoles->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.approval_roles')]));
    }

    /**
     * Store a newly created ApprovalRole in storage.
     * POST /approvalRoles
     *
     * @param CreateApprovalRoleAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateApprovalRoleAPIRequest $request)
    {
        $input = $request->all();

        $approvalRoles = $this->approvalRoleRepository->create($input);

        return $this->sendResponse($approvalRoles->toArray(), trans('custom.save', ['attribute' => trans('custom.approval_roles')]));
    }

    /**
     * Display the specified ApprovalRole.
     * GET|HEAD /approvalRoles/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var ApprovalRole $approvalRole */
        $approvalRole = $this->approvalRoleRepository->findWithoutFail($id);

        if (empty($approvalRole)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.approval_roles')]));
        }

        return $this->sendResponse($approvalRole->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.approval_roles')]));
    }

    /**
     * Update the specified ApprovalRole in storage.
     * PUT/PATCH /approvalRoles/{id}
     *
     * @param  int $id
     * @param UpdateApprovalRoleAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateApprovalRoleAPIRequest $request)
    {
        $input = $request->all();

        /** @var ApprovalRole $approvalRole */
        $approvalRole = $this->approvalRoleRepository->findWithoutFail($id);

        if (empty($approvalRole)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.approval_roles')]));
        }

        $approvalRole = $this->approvalRoleRepository->update($input, $id);

        return $this->sendResponse($approvalRole->toArray(), trans('custom.update', ['attribute' => trans('custom.approval_roles')]));
    }

    /**
     * Remove the specified ApprovalRole from storage.
     * DELETE /approvalRoles/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var ApprovalRole $approvalRole */
        $approvalRole = $this->approvalRoleRepository->findWithoutFail($id);

        if (empty($approvalRole)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.approval_roles')]));
        }

        $approvalRole->delete();

        return $this->sendResponse($id, trans('custom.delete', ['attribute' => trans('custom.approval_roles')]));
    }

    public function getApprovalRollByLevel(Request $request){
        $approvalRole = ApprovalRole::with(['company' => function($query) {
            // $query->select('CompanyName');
        },'department' => function($query) {
            //$query->select('DepartmentDescription');
        },'document' => function($query) {
            //$query->select('documentDescription');
        },'serviceline' => function($query) {
            //$query->select('ServiceLineDes');
        }])->where('approvalLevelID', $request->approvalLevelID)->orderBy('rollLevel', 'asc')->get();
        return $this->sendResponse($approvalRole->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.record')]));
    }

    public function assignApprovalGroup(Request $request){

        $approvalGroupAlreadyExist = ApprovalRole::where('approvalLevelID',$request->approvalLevelID)
                                                    ->where('approvalGroupID',$request->approvalGroupID)
                                                    ->count();

        $approvalRole = $this->approvalRoleRepository->findWithoutFail($request->rollMasterID);

        if($approvalGroupAlreadyExist > 0){
            return $this->sendError(trans('custom.the_selected_approval_group_has_already_been_assig'), 500,['type' => $approvalRole]);
        }

        if (empty($approvalRole)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.approval_groups')]));
        }
        $approvalRole->approvalGroupID = $request->approvalGroupID;
        $approvalRole->save();

        return $this->sendResponse($approvalRole->toArray(), trans('custom.update', ['attribute' => trans('custom.approval_roles')]));
    }
}
