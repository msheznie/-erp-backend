<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateApprovalGroupsRequest;
use App\Http\Requests\UpdateApprovalGroupsRequest;
use App\Repositories\ApprovalGroupsRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class ApprovalGroupsController extends AppBaseController
{
    /** @var  ApprovalGroupsRepository */
    private $approvalGroupsRepository;

    public function __construct(ApprovalGroupsRepository $approvalGroupsRepo)
    {
        $this->approvalGroupsRepository = $approvalGroupsRepo;
    }

    /**
     * Display a listing of the ApprovalGroups.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->approvalGroupsRepository->pushCriteria(new RequestCriteria($request));
        $approvalGroups = $this->approvalGroupsRepository->all();

        return view('approval_groups.index')
            ->with('approvalGroups', $approvalGroups);
    }

    /**
     * Show the form for creating a new ApprovalGroups.
     *
     * @return Response
     */
    public function create()
    {
        return view('approval_groups.create');
    }

    /**
     * Store a newly created ApprovalGroups in storage.
     *
     * @param CreateApprovalGroupsRequest $request
     *
     * @return Response
     */
    public function store(CreateApprovalGroupsRequest $request)
    {
        $input = $request->all();

        $approvalGroups = $this->approvalGroupsRepository->create($input);

        Flash::success('Approval Groups saved successfully.');

        return redirect(route('approvalGroups.index'));
    }

    /**
     * Display the specified ApprovalGroups.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $approvalGroups = $this->approvalGroupsRepository->findWithoutFail($id);

        if (empty($approvalGroups)) {
            Flash::error('Approval Groups not found');

            return redirect(route('approvalGroups.index'));
        }

        return view('approval_groups.show')->with('approvalGroups', $approvalGroups);
    }

    /**
     * Show the form for editing the specified ApprovalGroups.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $approvalGroups = $this->approvalGroupsRepository->findWithoutFail($id);

        if (empty($approvalGroups)) {
            Flash::error('Approval Groups not found');

            return redirect(route('approvalGroups.index'));
        }

        return view('approval_groups.edit')->with('approvalGroups', $approvalGroups);
    }

    /**
     * Update the specified ApprovalGroups in storage.
     *
     * @param  int              $id
     * @param UpdateApprovalGroupsRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateApprovalGroupsRequest $request)
    {
        $approvalGroups = $this->approvalGroupsRepository->findWithoutFail($id);

        if (empty($approvalGroups)) {
            Flash::error('Approval Groups not found');

            return redirect(route('approvalGroups.index'));
        }

        $approvalGroups = $this->approvalGroupsRepository->update($request->all(), $id);

        Flash::success('Approval Groups updated successfully.');

        return redirect(route('approvalGroups.index'));
    }

    /**
     * Remove the specified ApprovalGroups from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $approvalGroups = $this->approvalGroupsRepository->findWithoutFail($id);

        if (empty($approvalGroups)) {
            Flash::error('Approval Groups not found');

            return redirect(route('approvalGroups.index'));
        }

        $this->approvalGroupsRepository->delete($id);

        Flash::success('Approval Groups deleted successfully.');

        return redirect(route('approvalGroups.index'));
    }
}
