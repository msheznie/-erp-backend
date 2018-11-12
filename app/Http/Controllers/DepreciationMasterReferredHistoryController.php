<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateDepreciationMasterReferredHistoryRequest;
use App\Http\Requests\UpdateDepreciationMasterReferredHistoryRequest;
use App\Repositories\DepreciationMasterReferredHistoryRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class DepreciationMasterReferredHistoryController extends AppBaseController
{
    /** @var  DepreciationMasterReferredHistoryRepository */
    private $depreciationMasterReferredHistoryRepository;

    public function __construct(DepreciationMasterReferredHistoryRepository $depreciationMasterReferredHistoryRepo)
    {
        $this->depreciationMasterReferredHistoryRepository = $depreciationMasterReferredHistoryRepo;
    }

    /**
     * Display a listing of the DepreciationMasterReferredHistory.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->depreciationMasterReferredHistoryRepository->pushCriteria(new RequestCriteria($request));
        $depreciationMasterReferredHistories = $this->depreciationMasterReferredHistoryRepository->all();

        return view('depreciation_master_referred_histories.index')
            ->with('depreciationMasterReferredHistories', $depreciationMasterReferredHistories);
    }

    /**
     * Show the form for creating a new DepreciationMasterReferredHistory.
     *
     * @return Response
     */
    public function create()
    {
        return view('depreciation_master_referred_histories.create');
    }

    /**
     * Store a newly created DepreciationMasterReferredHistory in storage.
     *
     * @param CreateDepreciationMasterReferredHistoryRequest $request
     *
     * @return Response
     */
    public function store(CreateDepreciationMasterReferredHistoryRequest $request)
    {
        $input = $request->all();

        $depreciationMasterReferredHistory = $this->depreciationMasterReferredHistoryRepository->create($input);

        Flash::success('Depreciation Master Referred History saved successfully.');

        return redirect(route('depreciationMasterReferredHistories.index'));
    }

    /**
     * Display the specified DepreciationMasterReferredHistory.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $depreciationMasterReferredHistory = $this->depreciationMasterReferredHistoryRepository->findWithoutFail($id);

        if (empty($depreciationMasterReferredHistory)) {
            Flash::error('Depreciation Master Referred History not found');

            return redirect(route('depreciationMasterReferredHistories.index'));
        }

        return view('depreciation_master_referred_histories.show')->with('depreciationMasterReferredHistory', $depreciationMasterReferredHistory);
    }

    /**
     * Show the form for editing the specified DepreciationMasterReferredHistory.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $depreciationMasterReferredHistory = $this->depreciationMasterReferredHistoryRepository->findWithoutFail($id);

        if (empty($depreciationMasterReferredHistory)) {
            Flash::error('Depreciation Master Referred History not found');

            return redirect(route('depreciationMasterReferredHistories.index'));
        }

        return view('depreciation_master_referred_histories.edit')->with('depreciationMasterReferredHistory', $depreciationMasterReferredHistory);
    }

    /**
     * Update the specified DepreciationMasterReferredHistory in storage.
     *
     * @param  int              $id
     * @param UpdateDepreciationMasterReferredHistoryRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateDepreciationMasterReferredHistoryRequest $request)
    {
        $depreciationMasterReferredHistory = $this->depreciationMasterReferredHistoryRepository->findWithoutFail($id);

        if (empty($depreciationMasterReferredHistory)) {
            Flash::error('Depreciation Master Referred History not found');

            return redirect(route('depreciationMasterReferredHistories.index'));
        }

        $depreciationMasterReferredHistory = $this->depreciationMasterReferredHistoryRepository->update($request->all(), $id);

        Flash::success('Depreciation Master Referred History updated successfully.');

        return redirect(route('depreciationMasterReferredHistories.index'));
    }

    /**
     * Remove the specified DepreciationMasterReferredHistory from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $depreciationMasterReferredHistory = $this->depreciationMasterReferredHistoryRepository->findWithoutFail($id);

        if (empty($depreciationMasterReferredHistory)) {
            Flash::error('Depreciation Master Referred History not found');

            return redirect(route('depreciationMasterReferredHistories.index'));
        }

        $this->depreciationMasterReferredHistoryRepository->delete($id);

        Flash::success('Depreciation Master Referred History deleted successfully.');

        return redirect(route('depreciationMasterReferredHistories.index'));
    }
}
