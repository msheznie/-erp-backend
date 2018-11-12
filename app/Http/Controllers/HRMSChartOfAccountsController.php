<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateHRMSChartOfAccountsRequest;
use App\Http\Requests\UpdateHRMSChartOfAccountsRequest;
use App\Repositories\HRMSChartOfAccountsRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class HRMSChartOfAccountsController extends AppBaseController
{
    /** @var  HRMSChartOfAccountsRepository */
    private $hRMSChartOfAccountsRepository;

    public function __construct(HRMSChartOfAccountsRepository $hRMSChartOfAccountsRepo)
    {
        $this->hRMSChartOfAccountsRepository = $hRMSChartOfAccountsRepo;
    }

    /**
     * Display a listing of the HRMSChartOfAccounts.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->hRMSChartOfAccountsRepository->pushCriteria(new RequestCriteria($request));
        $hRMSChartOfAccounts = $this->hRMSChartOfAccountsRepository->all();

        return view('h_r_m_s_chart_of_accounts.index')
            ->with('hRMSChartOfAccounts', $hRMSChartOfAccounts);
    }

    /**
     * Show the form for creating a new HRMSChartOfAccounts.
     *
     * @return Response
     */
    public function create()
    {
        return view('h_r_m_s_chart_of_accounts.create');
    }

    /**
     * Store a newly created HRMSChartOfAccounts in storage.
     *
     * @param CreateHRMSChartOfAccountsRequest $request
     *
     * @return Response
     */
    public function store(CreateHRMSChartOfAccountsRequest $request)
    {
        $input = $request->all();

        $hRMSChartOfAccounts = $this->hRMSChartOfAccountsRepository->create($input);

        Flash::success('H R M S Chart Of Accounts saved successfully.');

        return redirect(route('hRMSChartOfAccounts.index'));
    }

    /**
     * Display the specified HRMSChartOfAccounts.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $hRMSChartOfAccounts = $this->hRMSChartOfAccountsRepository->findWithoutFail($id);

        if (empty($hRMSChartOfAccounts)) {
            Flash::error('H R M S Chart Of Accounts not found');

            return redirect(route('hRMSChartOfAccounts.index'));
        }

        return view('h_r_m_s_chart_of_accounts.show')->with('hRMSChartOfAccounts', $hRMSChartOfAccounts);
    }

    /**
     * Show the form for editing the specified HRMSChartOfAccounts.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $hRMSChartOfAccounts = $this->hRMSChartOfAccountsRepository->findWithoutFail($id);

        if (empty($hRMSChartOfAccounts)) {
            Flash::error('H R M S Chart Of Accounts not found');

            return redirect(route('hRMSChartOfAccounts.index'));
        }

        return view('h_r_m_s_chart_of_accounts.edit')->with('hRMSChartOfAccounts', $hRMSChartOfAccounts);
    }

    /**
     * Update the specified HRMSChartOfAccounts in storage.
     *
     * @param  int              $id
     * @param UpdateHRMSChartOfAccountsRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateHRMSChartOfAccountsRequest $request)
    {
        $hRMSChartOfAccounts = $this->hRMSChartOfAccountsRepository->findWithoutFail($id);

        if (empty($hRMSChartOfAccounts)) {
            Flash::error('H R M S Chart Of Accounts not found');

            return redirect(route('hRMSChartOfAccounts.index'));
        }

        $hRMSChartOfAccounts = $this->hRMSChartOfAccountsRepository->update($request->all(), $id);

        Flash::success('H R M S Chart Of Accounts updated successfully.');

        return redirect(route('hRMSChartOfAccounts.index'));
    }

    /**
     * Remove the specified HRMSChartOfAccounts from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $hRMSChartOfAccounts = $this->hRMSChartOfAccountsRepository->findWithoutFail($id);

        if (empty($hRMSChartOfAccounts)) {
            Flash::error('H R M S Chart Of Accounts not found');

            return redirect(route('hRMSChartOfAccounts.index'));
        }

        $this->hRMSChartOfAccountsRepository->delete($id);

        Flash::success('H R M S Chart Of Accounts deleted successfully.');

        return redirect(route('hRMSChartOfAccounts.index'));
    }
}
