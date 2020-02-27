<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateChartOfAccountAllocationDetailHistoryRequest;
use App\Http\Requests\UpdateChartOfAccountAllocationDetailHistoryRequest;
use App\Repositories\ChartOfAccountAllocationDetailHistoryRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class ChartOfAccountAllocationDetailHistoryController extends AppBaseController
{
    /** @var  ChartOfAccountAllocationDetailHistoryRepository */
    private $chartOfAccountAllocationDetailHistoryRepository;

    public function __construct(ChartOfAccountAllocationDetailHistoryRepository $chartOfAccountAllocationDetailHistoryRepo)
    {
        $this->chartOfAccountAllocationDetailHistoryRepository = $chartOfAccountAllocationDetailHistoryRepo;
    }

    /**
     * Display a listing of the ChartOfAccountAllocationDetailHistory.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->chartOfAccountAllocationDetailHistoryRepository->pushCriteria(new RequestCriteria($request));
        $chartOfAccountAllocationDetailHistories = $this->chartOfAccountAllocationDetailHistoryRepository->all();

        return view('chart_of_account_allocation_detail_histories.index')
            ->with('chartOfAccountAllocationDetailHistories', $chartOfAccountAllocationDetailHistories);
    }

    /**
     * Show the form for creating a new ChartOfAccountAllocationDetailHistory.
     *
     * @return Response
     */
    public function create()
    {
        return view('chart_of_account_allocation_detail_histories.create');
    }

    /**
     * Store a newly created ChartOfAccountAllocationDetailHistory in storage.
     *
     * @param CreateChartOfAccountAllocationDetailHistoryRequest $request
     *
     * @return Response
     */
    public function store(CreateChartOfAccountAllocationDetailHistoryRequest $request)
    {
        $input = $request->all();

        $chartOfAccountAllocationDetailHistory = $this->chartOfAccountAllocationDetailHistoryRepository->create($input);

        Flash::success('Chart Of Account Allocation Detail History saved successfully.');

        return redirect(route('chartOfAccountAllocationDetailHistories.index'));
    }

    /**
     * Display the specified ChartOfAccountAllocationDetailHistory.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $chartOfAccountAllocationDetailHistory = $this->chartOfAccountAllocationDetailHistoryRepository->findWithoutFail($id);

        if (empty($chartOfAccountAllocationDetailHistory)) {
            Flash::error('Chart Of Account Allocation Detail History not found');

            return redirect(route('chartOfAccountAllocationDetailHistories.index'));
        }

        return view('chart_of_account_allocation_detail_histories.show')->with('chartOfAccountAllocationDetailHistory', $chartOfAccountAllocationDetailHistory);
    }

    /**
     * Show the form for editing the specified ChartOfAccountAllocationDetailHistory.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $chartOfAccountAllocationDetailHistory = $this->chartOfAccountAllocationDetailHistoryRepository->findWithoutFail($id);

        if (empty($chartOfAccountAllocationDetailHistory)) {
            Flash::error('Chart Of Account Allocation Detail History not found');

            return redirect(route('chartOfAccountAllocationDetailHistories.index'));
        }

        return view('chart_of_account_allocation_detail_histories.edit')->with('chartOfAccountAllocationDetailHistory', $chartOfAccountAllocationDetailHistory);
    }

    /**
     * Update the specified ChartOfAccountAllocationDetailHistory in storage.
     *
     * @param  int              $id
     * @param UpdateChartOfAccountAllocationDetailHistoryRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateChartOfAccountAllocationDetailHistoryRequest $request)
    {
        $chartOfAccountAllocationDetailHistory = $this->chartOfAccountAllocationDetailHistoryRepository->findWithoutFail($id);

        if (empty($chartOfAccountAllocationDetailHistory)) {
            Flash::error('Chart Of Account Allocation Detail History not found');

            return redirect(route('chartOfAccountAllocationDetailHistories.index'));
        }

        $chartOfAccountAllocationDetailHistory = $this->chartOfAccountAllocationDetailHistoryRepository->update($request->all(), $id);

        Flash::success('Chart Of Account Allocation Detail History updated successfully.');

        return redirect(route('chartOfAccountAllocationDetailHistories.index'));
    }

    /**
     * Remove the specified ChartOfAccountAllocationDetailHistory from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $chartOfAccountAllocationDetailHistory = $this->chartOfAccountAllocationDetailHistoryRepository->findWithoutFail($id);

        if (empty($chartOfAccountAllocationDetailHistory)) {
            Flash::error('Chart Of Account Allocation Detail History not found');

            return redirect(route('chartOfAccountAllocationDetailHistories.index'));
        }

        $this->chartOfAccountAllocationDetailHistoryRepository->delete($id);

        Flash::success('Chart Of Account Allocation Detail History deleted successfully.');

        return redirect(route('chartOfAccountAllocationDetailHistories.index'));
    }
}
