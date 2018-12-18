<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateChartOfAccountsRefferedBackRequest;
use App\Http\Requests\UpdateChartOfAccountsRefferedBackRequest;
use App\Repositories\ChartOfAccountsRefferedBackRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class ChartOfAccountsRefferedBackController extends AppBaseController
{
    /** @var  ChartOfAccountsRefferedBackRepository */
    private $chartOfAccountsRefferedBackRepository;

    public function __construct(ChartOfAccountsRefferedBackRepository $chartOfAccountsRefferedBackRepo)
    {
        $this->chartOfAccountsRefferedBackRepository = $chartOfAccountsRefferedBackRepo;
    }

    /**
     * Display a listing of the ChartOfAccountsRefferedBack.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->chartOfAccountsRefferedBackRepository->pushCriteria(new RequestCriteria($request));
        $chartOfAccountsRefferedBacks = $this->chartOfAccountsRefferedBackRepository->all();

        return view('chart_of_accounts_reffered_backs.index')
            ->with('chartOfAccountsRefferedBacks', $chartOfAccountsRefferedBacks);
    }

    /**
     * Show the form for creating a new ChartOfAccountsRefferedBack.
     *
     * @return Response
     */
    public function create()
    {
        return view('chart_of_accounts_reffered_backs.create');
    }

    /**
     * Store a newly created ChartOfAccountsRefferedBack in storage.
     *
     * @param CreateChartOfAccountsRefferedBackRequest $request
     *
     * @return Response
     */
    public function store(CreateChartOfAccountsRefferedBackRequest $request)
    {
        $input = $request->all();

        $chartOfAccountsRefferedBack = $this->chartOfAccountsRefferedBackRepository->create($input);

        Flash::success('Chart Of Accounts Reffered Back saved successfully.');

        return redirect(route('chartOfAccountsRefferedBacks.index'));
    }

    /**
     * Display the specified ChartOfAccountsRefferedBack.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $chartOfAccountsRefferedBack = $this->chartOfAccountsRefferedBackRepository->findWithoutFail($id);

        if (empty($chartOfAccountsRefferedBack)) {
            Flash::error('Chart Of Accounts Reffered Back not found');

            return redirect(route('chartOfAccountsRefferedBacks.index'));
        }

        return view('chart_of_accounts_reffered_backs.show')->with('chartOfAccountsRefferedBack', $chartOfAccountsRefferedBack);
    }

    /**
     * Show the form for editing the specified ChartOfAccountsRefferedBack.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $chartOfAccountsRefferedBack = $this->chartOfAccountsRefferedBackRepository->findWithoutFail($id);

        if (empty($chartOfAccountsRefferedBack)) {
            Flash::error('Chart Of Accounts Reffered Back not found');

            return redirect(route('chartOfAccountsRefferedBacks.index'));
        }

        return view('chart_of_accounts_reffered_backs.edit')->with('chartOfAccountsRefferedBack', $chartOfAccountsRefferedBack);
    }

    /**
     * Update the specified ChartOfAccountsRefferedBack in storage.
     *
     * @param  int              $id
     * @param UpdateChartOfAccountsRefferedBackRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateChartOfAccountsRefferedBackRequest $request)
    {
        $chartOfAccountsRefferedBack = $this->chartOfAccountsRefferedBackRepository->findWithoutFail($id);

        if (empty($chartOfAccountsRefferedBack)) {
            Flash::error('Chart Of Accounts Reffered Back not found');

            return redirect(route('chartOfAccountsRefferedBacks.index'));
        }

        $chartOfAccountsRefferedBack = $this->chartOfAccountsRefferedBackRepository->update($request->all(), $id);

        Flash::success('Chart Of Accounts Reffered Back updated successfully.');

        return redirect(route('chartOfAccountsRefferedBacks.index'));
    }

    /**
     * Remove the specified ChartOfAccountsRefferedBack from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $chartOfAccountsRefferedBack = $this->chartOfAccountsRefferedBackRepository->findWithoutFail($id);

        if (empty($chartOfAccountsRefferedBack)) {
            Flash::error('Chart Of Accounts Reffered Back not found');

            return redirect(route('chartOfAccountsRefferedBacks.index'));
        }

        $this->chartOfAccountsRefferedBackRepository->delete($id);

        Flash::success('Chart Of Accounts Reffered Back deleted successfully.');

        return redirect(route('chartOfAccountsRefferedBacks.index'));
    }
}
