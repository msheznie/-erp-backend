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

        return $this->sendResponse($approvalRoles->toArray(), 'Approval Roles retrieved successfully');
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

        return $this->sendResponse($approvalRoles->toArray(), 'Approval Role saved successfully');
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
            return $this->sendError('Approval Role not found');
        }

        return $this->sendResponse($approvalRole->toArray(), 'Approval Role retrieved successfully');
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
            return $this->sendError('Approval Role not found');
        }

        $approvalRole = $this->approvalRoleRepository->update($input, $id);

        return $this->sendResponse($approvalRole->toArray(), 'ApprovalRole updated successfully');
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
            return $this->sendError('Approval Role not found');
        }

        $approvalRole->delete();

        return $this->sendResponse($id, 'Approval Role deleted successfully');
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
        return $this->sendResponse($approvalRole->toArray(), 'Record retrieved successfully');
    }

    public function assignApprovalGroup(Request $request){
        $input = $request->all();
        $approvalRole = $this->approvalRoleRepository->findWithoutFail($request->rollMasterID);
        if (empty($approvalRole)) {
            return $this->sendError('Approval Groups not found');
        }
        $approvalRole->approvalGroupID = $request->approvalGroupID;
        $approvalRole->save();

        return $this->sendResponse($approvalRole->toArray(), 'ApprovalRole updated successfully');
    }
}
