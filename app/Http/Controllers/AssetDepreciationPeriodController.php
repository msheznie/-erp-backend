<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateAssetDepreciationPeriodRequest;
use App\Http\Requests\UpdateAssetDepreciationPeriodRequest;
use App\Repositories\AssetDepreciationPeriodRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class AssetDepreciationPeriodController extends AppBaseController
{
    /** @var  AssetDepreciationPeriodRepository */
    private $assetDepreciationPeriodRepository;

    public function __construct(AssetDepreciationPeriodRepository $assetDepreciationPeriodRepo)
    {
        $this->assetDepreciationPeriodRepository = $assetDepreciationPeriodRepo;
    }

    /**
     * Display a listing of the AssetDepreciationPeriod.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->assetDepreciationPeriodRepository->pushCriteria(new RequestCriteria($request));
        $assetDepreciationPeriods = $this->assetDepreciationPeriodRepository->all();

        return view('asset_depreciation_periods.index')
            ->with('assetDepreciationPeriods', $assetDepreciationPeriods);
    }

    /**
     * Show the form for creating a new AssetDepreciationPeriod.
     *
     * @return Response
     */
    public function create()
    {
        return view('asset_depreciation_periods.create');
    }

    /**
     * Store a newly created AssetDepreciationPeriod in storage.
     *
     * @param CreateAssetDepreciationPeriodRequest $request
     *
     * @return Response
     */
    public function store(CreateAssetDepreciationPeriodRequest $request)
    {
        $input = $request->all();

        $assetDepreciationPeriod = $this->assetDepreciationPeriodRepository->create($input);

        Flash::success('Asset Depreciation Period saved successfully.');

        return redirect(route('assetDepreciationPeriods.index'));
    }

    /**
     * Display the specified AssetDepreciationPeriod.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $assetDepreciationPeriod = $this->assetDepreciationPeriodRepository->findWithoutFail($id);

        if (empty($assetDepreciationPeriod)) {
            Flash::error('Asset Depreciation Period not found');

            return redirect(route('assetDepreciationPeriods.index'));
        }

        return view('asset_depreciation_periods.show')->with('assetDepreciationPeriod', $assetDepreciationPeriod);
    }

    /**
     * Show the form for editing the specified AssetDepreciationPeriod.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $assetDepreciationPeriod = $this->assetDepreciationPeriodRepository->findWithoutFail($id);

        if (empty($assetDepreciationPeriod)) {
            Flash::error('Asset Depreciation Period not found');

            return redirect(route('assetDepreciationPeriods.index'));
        }

        return view('asset_depreciation_periods.edit')->with('assetDepreciationPeriod', $assetDepreciationPeriod);
    }

    /**
     * Update the specified AssetDepreciationPeriod in storage.
     *
     * @param  int              $id
     * @param UpdateAssetDepreciationPeriodRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateAssetDepreciationPeriodRequest $request)
    {
        $assetDepreciationPeriod = $this->assetDepreciationPeriodRepository->findWithoutFail($id);

        if (empty($assetDepreciationPeriod)) {
            Flash::error('Asset Depreciation Period not found');

            return redirect(route('assetDepreciationPeriods.index'));
        }

        $assetDepreciationPeriod = $this->assetDepreciationPeriodRepository->update($request->all(), $id);

        Flash::success('Asset Depreciation Period updated successfully.');

        return redirect(route('assetDepreciationPeriods.index'));
    }

    /**
     * Remove the specified AssetDepreciationPeriod from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $assetDepreciationPeriod = $this->assetDepreciationPeriodRepository->findWithoutFail($id);

        if (empty($assetDepreciationPeriod)) {
            Flash::error('Asset Depreciation Period not found');

            return redirect(route('assetDepreciationPeriods.index'));
        }

        $this->assetDepreciationPeriodRepository->delete($id);

        Flash::success('Asset Depreciation Period deleted successfully.');

        return redirect(route('assetDepreciationPeriods.index'));
    }
}
