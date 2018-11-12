<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateDepreciationPeriodsReferredHistoryRequest;
use App\Http\Requests\UpdateDepreciationPeriodsReferredHistoryRequest;
use App\Repositories\DepreciationPeriodsReferredHistoryRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class DepreciationPeriodsReferredHistoryController extends AppBaseController
{
    /** @var  DepreciationPeriodsReferredHistoryRepository */
    private $depreciationPeriodsReferredHistoryRepository;

    public function __construct(DepreciationPeriodsReferredHistoryRepository $depreciationPeriodsReferredHistoryRepo)
    {
        $this->depreciationPeriodsReferredHistoryRepository = $depreciationPeriodsReferredHistoryRepo;
    }

    /**
     * Display a listing of the DepreciationPeriodsReferredHistory.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->depreciationPeriodsReferredHistoryRepository->pushCriteria(new RequestCriteria($request));
        $depreciationPeriodsReferredHistories = $this->depreciationPeriodsReferredHistoryRepository->all();

        return view('depreciation_periods_referred_histories.index')
            ->with('depreciationPeriodsReferredHistories', $depreciationPeriodsReferredHistories);
    }

    /**
     * Show the form for creating a new DepreciationPeriodsReferredHistory.
     *
     * @return Response
     */
    public function create()
    {
        return view('depreciation_periods_referred_histories.create');
    }

    /**
     * Store a newly created DepreciationPeriodsReferredHistory in storage.
     *
     * @param CreateDepreciationPeriodsReferredHistoryRequest $request
     *
     * @return Response
     */
    public function store(CreateDepreciationPeriodsReferredHistoryRequest $request)
    {
        $input = $request->all();

        $depreciationPeriodsReferredHistory = $this->depreciationPeriodsReferredHistoryRepository->create($input);

        Flash::success('Depreciation Periods Referred History saved successfully.');

        return redirect(route('depreciationPeriodsReferredHistories.index'));
    }

    /**
     * Display the specified DepreciationPeriodsReferredHistory.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $depreciationPeriodsReferredHistory = $this->depreciationPeriodsReferredHistoryRepository->findWithoutFail($id);

        if (empty($depreciationPeriodsReferredHistory)) {
            Flash::error('Depreciation Periods Referred History not found');

            return redirect(route('depreciationPeriodsReferredHistories.index'));
        }

        return view('depreciation_periods_referred_histories.show')->with('depreciationPeriodsReferredHistory', $depreciationPeriodsReferredHistory);
    }

    /**
     * Show the form for editing the specified DepreciationPeriodsReferredHistory.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $depreciationPeriodsReferredHistory = $this->depreciationPeriodsReferredHistoryRepository->findWithoutFail($id);

        if (empty($depreciationPeriodsReferredHistory)) {
            Flash::error('Depreciation Periods Referred History not found');

            return redirect(route('depreciationPeriodsReferredHistories.index'));
        }

        return view('depreciation_periods_referred_histories.edit')->with('depreciationPeriodsReferredHistory', $depreciationPeriodsReferredHistory);
    }

    /**
     * Update the specified DepreciationPeriodsReferredHistory in storage.
     *
     * @param  int              $id
     * @param UpdateDepreciationPeriodsReferredHistoryRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateDepreciationPeriodsReferredHistoryRequest $request)
    {
        $depreciationPeriodsReferredHistory = $this->depreciationPeriodsReferredHistoryRepository->findWithoutFail($id);

        if (empty($depreciationPeriodsReferredHistory)) {
            Flash::error('Depreciation Periods Referred History not found');

            return redirect(route('depreciationPeriodsReferredHistories.index'));
        }

        $depreciationPeriodsReferredHistory = $this->depreciationPeriodsReferredHistoryRepository->update($request->all(), $id);

        Flash::success('Depreciation Periods Referred History updated successfully.');

        return redirect(route('depreciationPeriodsReferredHistories.index'));
    }

    /**
     * Remove the specified DepreciationPeriodsReferredHistory from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $depreciationPeriodsReferredHistory = $this->depreciationPeriodsReferredHistoryRepository->findWithoutFail($id);

        if (empty($depreciationPeriodsReferredHistory)) {
            Flash::error('Depreciation Periods Referred History not found');

            return redirect(route('depreciationPeriodsReferredHistories.index'));
        }

        $this->depreciationPeriodsReferredHistoryRepository->delete($id);

        Flash::success('Depreciation Periods Referred History deleted successfully.');

        return redirect(route('depreciationPeriodsReferredHistories.index'));
    }
}
