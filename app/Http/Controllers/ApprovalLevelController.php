<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateApprovalLevelRequest;
use App\Http\Requests\UpdateApprovalLevelRequest;
use App\Repositories\ApprovalLevelRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class ApprovalLevelController extends AppBaseController
{
    /** @var  ApprovalLevelRepository */
    private $approvalLevelRepository;

    public function __construct(ApprovalLevelRepository $approvalLevelRepo)
    {
        $this->approvalLevelRepository = $approvalLevelRepo;
    }

    /**
     * Display a listing of the ApprovalLevel.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->approvalLevelRepository->pushCriteria(new RequestCriteria($request));
        $approvalLevels = $this->approvalLevelRepository->all();

        return view('approval_levels.index')
            ->with('approvalLevels', $approvalLevels);
    }

    /**
     * Show the form for creating a new ApprovalLevel.
     *
     * @return Response
     */
    public function create()
    {
        return view('approval_levels.create');
    }

    /**
     * Store a newly created ApprovalLevel in storage.
     *
     * @param CreateApprovalLevelRequest $request
     *
     * @return Response
     */
    public function store(CreateApprovalLevelRequest $request)
    {
        $input = $request->all();

        $approvalLevel = $this->approvalLevelRepository->create($input);

        Flash::success('Approval Level saved successfully.');

        return redirect(route('approvalLevels.index'));
    }

    /**
     * Display the specified ApprovalLevel.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $approvalLevel = $this->approvalLevelRepository->findWithoutFail($id);

        if (empty($approvalLevel)) {
            Flash::error('Approval Level not found');

            return redirect(route('approvalLevels.index'));
        }

        return view('approval_levels.show')->with('approvalLevel', $approvalLevel);
    }

    /**
     * Show the form for editing the specified ApprovalLevel.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $approvalLevel = $this->approvalLevelRepository->findWithoutFail($id);

        if (empty($approvalLevel)) {
            Flash::error('Approval Level not found');

            return redirect(route('approvalLevels.index'));
        }

        return view('approval_levels.edit')->with('approvalLevel', $approvalLevel);
    }

    /**
     * Update the specified ApprovalLevel in storage.
     *
     * @param  int              $id
     * @param UpdateApprovalLevelRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateApprovalLevelRequest $request)
    {
        $approvalLevel = $this->approvalLevelRepository->findWithoutFail($id);

        if (empty($approvalLevel)) {
            Flash::error('Approval Level not found');

            return redirect(route('approvalLevels.index'));
        }

        $approvalLevel = $this->approvalLevelRepository->update($request->all(), $id);

        Flash::success('Approval Level updated successfully.');

        return redirect(route('approvalLevels.index'));
    }

    /**
     * Remove the specified ApprovalLevel from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $approvalLevel = $this->approvalLevelRepository->findWithoutFail($id);

        if (empty($approvalLevel)) {
            Flash::error('Approval Level not found');

            return redirect(route('approvalLevels.index'));
        }

        $this->approvalLevelRepository->delete($id);

        Flash::success('Approval Level deleted successfully.');

        return redirect(route('approvalLevels.index'));
    }
}
