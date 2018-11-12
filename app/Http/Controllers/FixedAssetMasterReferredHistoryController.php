<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateFixedAssetMasterReferredHistoryRequest;
use App\Http\Requests\UpdateFixedAssetMasterReferredHistoryRequest;
use App\Repositories\FixedAssetMasterReferredHistoryRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class FixedAssetMasterReferredHistoryController extends AppBaseController
{
    /** @var  FixedAssetMasterReferredHistoryRepository */
    private $fixedAssetMasterReferredHistoryRepository;

    public function __construct(FixedAssetMasterReferredHistoryRepository $fixedAssetMasterReferredHistoryRepo)
    {
        $this->fixedAssetMasterReferredHistoryRepository = $fixedAssetMasterReferredHistoryRepo;
    }

    /**
     * Display a listing of the FixedAssetMasterReferredHistory.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->fixedAssetMasterReferredHistoryRepository->pushCriteria(new RequestCriteria($request));
        $fixedAssetMasterReferredHistories = $this->fixedAssetMasterReferredHistoryRepository->all();

        return view('fixed_asset_master_referred_histories.index')
            ->with('fixedAssetMasterReferredHistories', $fixedAssetMasterReferredHistories);
    }

    /**
     * Show the form for creating a new FixedAssetMasterReferredHistory.
     *
     * @return Response
     */
    public function create()
    {
        return view('fixed_asset_master_referred_histories.create');
    }

    /**
     * Store a newly created FixedAssetMasterReferredHistory in storage.
     *
     * @param CreateFixedAssetMasterReferredHistoryRequest $request
     *
     * @return Response
     */
    public function store(CreateFixedAssetMasterReferredHistoryRequest $request)
    {
        $input = $request->all();

        $fixedAssetMasterReferredHistory = $this->fixedAssetMasterReferredHistoryRepository->create($input);

        Flash::success('Fixed Asset Master Referred History saved successfully.');

        return redirect(route('fixedAssetMasterReferredHistories.index'));
    }

    /**
     * Display the specified FixedAssetMasterReferredHistory.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $fixedAssetMasterReferredHistory = $this->fixedAssetMasterReferredHistoryRepository->findWithoutFail($id);

        if (empty($fixedAssetMasterReferredHistory)) {
            Flash::error('Fixed Asset Master Referred History not found');

            return redirect(route('fixedAssetMasterReferredHistories.index'));
        }

        return view('fixed_asset_master_referred_histories.show')->with('fixedAssetMasterReferredHistory', $fixedAssetMasterReferredHistory);
    }

    /**
     * Show the form for editing the specified FixedAssetMasterReferredHistory.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $fixedAssetMasterReferredHistory = $this->fixedAssetMasterReferredHistoryRepository->findWithoutFail($id);

        if (empty($fixedAssetMasterReferredHistory)) {
            Flash::error('Fixed Asset Master Referred History not found');

            return redirect(route('fixedAssetMasterReferredHistories.index'));
        }

        return view('fixed_asset_master_referred_histories.edit')->with('fixedAssetMasterReferredHistory', $fixedAssetMasterReferredHistory);
    }

    /**
     * Update the specified FixedAssetMasterReferredHistory in storage.
     *
     * @param  int              $id
     * @param UpdateFixedAssetMasterReferredHistoryRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateFixedAssetMasterReferredHistoryRequest $request)
    {
        $fixedAssetMasterReferredHistory = $this->fixedAssetMasterReferredHistoryRepository->findWithoutFail($id);

        if (empty($fixedAssetMasterReferredHistory)) {
            Flash::error('Fixed Asset Master Referred History not found');

            return redirect(route('fixedAssetMasterReferredHistories.index'));
        }

        $fixedAssetMasterReferredHistory = $this->fixedAssetMasterReferredHistoryRepository->update($request->all(), $id);

        Flash::success('Fixed Asset Master Referred History updated successfully.');

        return redirect(route('fixedAssetMasterReferredHistories.index'));
    }

    /**
     * Remove the specified FixedAssetMasterReferredHistory from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $fixedAssetMasterReferredHistory = $this->fixedAssetMasterReferredHistoryRepository->findWithoutFail($id);

        if (empty($fixedAssetMasterReferredHistory)) {
            Flash::error('Fixed Asset Master Referred History not found');

            return redirect(route('fixedAssetMasterReferredHistories.index'));
        }

        $this->fixedAssetMasterReferredHistoryRepository->delete($id);

        Flash::success('Fixed Asset Master Referred History deleted successfully.');

        return redirect(route('fixedAssetMasterReferredHistories.index'));
    }
}
