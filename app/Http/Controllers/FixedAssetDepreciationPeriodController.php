<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateFixedAssetDepreciationPeriodRequest;
use App\Http\Requests\UpdateFixedAssetDepreciationPeriodRequest;
use App\Repositories\FixedAssetDepreciationPeriodRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class FixedAssetDepreciationPeriodController extends AppBaseController
{
    /** @var  FixedAssetDepreciationPeriodRepository */
    private $fixedAssetDepreciationPeriodRepository;

    public function __construct(FixedAssetDepreciationPeriodRepository $fixedAssetDepreciationPeriodRepo)
    {
        $this->fixedAssetDepreciationPeriodRepository = $fixedAssetDepreciationPeriodRepo;
    }

    /**
     * Display a listing of the FixedAssetDepreciationPeriod.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->fixedAssetDepreciationPeriodRepository->pushCriteria(new RequestCriteria($request));
        $fixedAssetDepreciationPeriods = $this->fixedAssetDepreciationPeriodRepository->all();

        return view('fixed_asset_depreciation_periods.index')
            ->with('fixedAssetDepreciationPeriods', $fixedAssetDepreciationPeriods);
    }

    /**
     * Show the form for creating a new FixedAssetDepreciationPeriod.
     *
     * @return Response
     */
    public function create()
    {
        return view('fixed_asset_depreciation_periods.create');
    }

    /**
     * Store a newly created FixedAssetDepreciationPeriod in storage.
     *
     * @param CreateFixedAssetDepreciationPeriodRequest $request
     *
     * @return Response
     */
    public function store(CreateFixedAssetDepreciationPeriodRequest $request)
    {
        $input = $request->all();

        $fixedAssetDepreciationPeriod = $this->fixedAssetDepreciationPeriodRepository->create($input);

        Flash::success('Fixed Asset Depreciation Period saved successfully.');

        return redirect(route('fixedAssetDepreciationPeriods.index'));
    }

    /**
     * Display the specified FixedAssetDepreciationPeriod.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $fixedAssetDepreciationPeriod = $this->fixedAssetDepreciationPeriodRepository->findWithoutFail($id);

        if (empty($fixedAssetDepreciationPeriod)) {
            Flash::error('Fixed Asset Depreciation Period not found');

            return redirect(route('fixedAssetDepreciationPeriods.index'));
        }

        return view('fixed_asset_depreciation_periods.show')->with('fixedAssetDepreciationPeriod', $fixedAssetDepreciationPeriod);
    }

    /**
     * Show the form for editing the specified FixedAssetDepreciationPeriod.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $fixedAssetDepreciationPeriod = $this->fixedAssetDepreciationPeriodRepository->findWithoutFail($id);

        if (empty($fixedAssetDepreciationPeriod)) {
            Flash::error('Fixed Asset Depreciation Period not found');

            return redirect(route('fixedAssetDepreciationPeriods.index'));
        }

        return view('fixed_asset_depreciation_periods.edit')->with('fixedAssetDepreciationPeriod', $fixedAssetDepreciationPeriod);
    }

    /**
     * Update the specified FixedAssetDepreciationPeriod in storage.
     *
     * @param  int              $id
     * @param UpdateFixedAssetDepreciationPeriodRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateFixedAssetDepreciationPeriodRequest $request)
    {
        $fixedAssetDepreciationPeriod = $this->fixedAssetDepreciationPeriodRepository->findWithoutFail($id);

        if (empty($fixedAssetDepreciationPeriod)) {
            Flash::error('Fixed Asset Depreciation Period not found');

            return redirect(route('fixedAssetDepreciationPeriods.index'));
        }

        $fixedAssetDepreciationPeriod = $this->fixedAssetDepreciationPeriodRepository->update($request->all(), $id);

        Flash::success('Fixed Asset Depreciation Period updated successfully.');

        return redirect(route('fixedAssetDepreciationPeriods.index'));
    }

    /**
     * Remove the specified FixedAssetDepreciationPeriod from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $fixedAssetDepreciationPeriod = $this->fixedAssetDepreciationPeriodRepository->findWithoutFail($id);

        if (empty($fixedAssetDepreciationPeriod)) {
            Flash::error('Fixed Asset Depreciation Period not found');

            return redirect(route('fixedAssetDepreciationPeriods.index'));
        }

        $this->fixedAssetDepreciationPeriodRepository->delete($id);

        Flash::success('Fixed Asset Depreciation Period deleted successfully.');

        return redirect(route('fixedAssetDepreciationPeriods.index'));
    }
}
