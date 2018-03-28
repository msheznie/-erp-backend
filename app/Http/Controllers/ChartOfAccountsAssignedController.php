<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateChartOfAccountsAssignedRequest;
use App\Http\Requests\UpdateChartOfAccountsAssignedRequest;
use App\Repositories\ChartOfAccountsAssignedRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class ChartOfAccountsAssignedController extends AppBaseController
{
    /** @var  ChartOfAccountsAssignedRepository */
    private $chartOfAccountsAssignedRepository;

    public function __construct(ChartOfAccountsAssignedRepository $chartOfAccountsAssignedRepo)
    {
        $this->chartOfAccountsAssignedRepository = $chartOfAccountsAssignedRepo;
    }

    /**
     * Display a listing of the ChartOfAccountsAssigned.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->chartOfAccountsAssignedRepository->pushCriteria(new RequestCriteria($request));
        $chartOfAccountsAssigneds = $this->chartOfAccountsAssignedRepository->all();

        return view('chart_of_accounts_assigneds.index')
            ->with('chartOfAccountsAssigneds', $chartOfAccountsAssigneds);
    }

    /**
     * Show the form for creating a new ChartOfAccountsAssigned.
     *
     * @return Response
     */
    public function create()
    {
        return view('chart_of_accounts_assigneds.create');
    }

    /**
     * Store a newly created ChartOfAccountsAssigned in storage.
     *
     * @param CreateChartOfAccountsAssignedRequest $request
     *
     * @return Response
     */
    public function store(CreateChartOfAccountsAssignedRequest $request)
    {
        $input = $request->all();

        $chartOfAccountsAssigned = $this->chartOfAccountsAssignedRepository->create($input);

        Flash::success('Chart Of Accounts Assigned saved successfully.');

        return redirect(route('chartOfAccountsAssigneds.index'));
    }

    /**
     * Display the specified ChartOfAccountsAssigned.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $chartOfAccountsAssigned = $this->chartOfAccountsAssignedRepository->findWithoutFail($id);

        if (empty($chartOfAccountsAssigned)) {
            Flash::error('Chart Of Accounts Assigned not found');

            return redirect(route('chartOfAccountsAssigneds.index'));
        }

        return view('chart_of_accounts_assigneds.show')->with('chartOfAccountsAssigned', $chartOfAccountsAssigned);
    }

    /**
     * Show the form for editing the specified ChartOfAccountsAssigned.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $chartOfAccountsAssigned = $this->chartOfAccountsAssignedRepository->findWithoutFail($id);

        if (empty($chartOfAccountsAssigned)) {
            Flash::error('Chart Of Accounts Assigned not found');

            return redirect(route('chartOfAccountsAssigneds.index'));
        }

        return view('chart_of_accounts_assigneds.edit')->with('chartOfAccountsAssigned', $chartOfAccountsAssigned);
    }

    /**
     * Update the specified ChartOfAccountsAssigned in storage.
     *
     * @param  int              $id
     * @param UpdateChartOfAccountsAssignedRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateChartOfAccountsAssignedRequest $request)
    {
        $chartOfAccountsAssigned = $this->chartOfAccountsAssignedRepository->findWithoutFail($id);

        if (empty($chartOfAccountsAssigned)) {
            Flash::error('Chart Of Accounts Assigned not found');

            return redirect(route('chartOfAccountsAssigneds.index'));
        }

        $chartOfAccountsAssigned = $this->chartOfAccountsAssignedRepository->update($request->all(), $id);

        Flash::success('Chart Of Accounts Assigned updated successfully.');

        return redirect(route('chartOfAccountsAssigneds.index'));
    }

    /**
     * Remove the specified ChartOfAccountsAssigned from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $chartOfAccountsAssigned = $this->chartOfAccountsAssignedRepository->findWithoutFail($id);

        if (empty($chartOfAccountsAssigned)) {
            Flash::error('Chart Of Accounts Assigned not found');

            return redirect(route('chartOfAccountsAssigneds.index'));
        }

        $this->chartOfAccountsAssignedRepository->delete($id);

        Flash::success('Chart Of Accounts Assigned deleted successfully.');

        return redirect(route('chartOfAccountsAssigneds.index'));
    }
}
