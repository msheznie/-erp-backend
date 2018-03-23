<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateApprovalRoleRequest;
use App\Http\Requests\UpdateApprovalRoleRequest;
use App\Repositories\ApprovalRoleRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class ApprovalRoleController extends AppBaseController
{
    /** @var  ApprovalRoleRepository */
    private $approvalRoleRepository;

    public function __construct(ApprovalRoleRepository $approvalRoleRepo)
    {
        $this->approvalRoleRepository = $approvalRoleRepo;
    }

    /**
     * Display a listing of the ApprovalRole.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->approvalRoleRepository->pushCriteria(new RequestCriteria($request));
        $approvalRoles = $this->approvalRoleRepository->all();

        return view('approval_roles.index')
            ->with('approvalRoles', $approvalRoles);
    }

    /**
     * Show the form for creating a new ApprovalRole.
     *
     * @return Response
     */
    public function create()
    {
        return view('approval_roles.create');
    }

    /**
     * Store a newly created ApprovalRole in storage.
     *
     * @param CreateApprovalRoleRequest $request
     *
     * @return Response
     */
    public function store(CreateApprovalRoleRequest $request)
    {
        $input = $request->all();

        $approvalRole = $this->approvalRoleRepository->create($input);

        Flash::success('Approval Role saved successfully.');

        return redirect(route('approvalRoles.index'));
    }

    /**
     * Display the specified ApprovalRole.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $approvalRole = $this->approvalRoleRepository->findWithoutFail($id);

        if (empty($approvalRole)) {
            Flash::error('Approval Role not found');

            return redirect(route('approvalRoles.index'));
        }

        return view('approval_roles.show')->with('approvalRole', $approvalRole);
    }

    /**
     * Show the form for editing the specified ApprovalRole.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $approvalRole = $this->approvalRoleRepository->findWithoutFail($id);

        if (empty($approvalRole)) {
            Flash::error('Approval Role not found');

            return redirect(route('approvalRoles.index'));
        }

        return view('approval_roles.edit')->with('approvalRole', $approvalRole);
    }

    /**
     * Update the specified ApprovalRole in storage.
     *
     * @param  int              $id
     * @param UpdateApprovalRoleRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateApprovalRoleRequest $request)
    {
        $approvalRole = $this->approvalRoleRepository->findWithoutFail($id);

        if (empty($approvalRole)) {
            Flash::error('Approval Role not found');

            return redirect(route('approvalRoles.index'));
        }

        $approvalRole = $this->approvalRoleRepository->update($request->all(), $id);

        Flash::success('Approval Role updated successfully.');

        return redirect(route('approvalRoles.index'));
    }

    /**
     * Remove the specified ApprovalRole from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $approvalRole = $this->approvalRoleRepository->findWithoutFail($id);

        if (empty($approvalRole)) {
            Flash::error('Approval Role not found');

            return redirect(route('approvalRoles.index'));
        }

        $this->approvalRoleRepository->delete($id);

        Flash::success('Approval Role deleted successfully.');

        return redirect(route('approvalRoles.index'));
    }
}
