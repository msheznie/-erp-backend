<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateFixedAssetDepreciationMasterRequest;
use App\Http\Requests\UpdateFixedAssetDepreciationMasterRequest;
use App\Repositories\FixedAssetDepreciationMasterRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class FixedAssetDepreciationMasterController extends AppBaseController
{
    /** @var  FixedAssetDepreciationMasterRepository */
    private $fixedAssetDepreciationMasterRepository;

    public function __construct(FixedAssetDepreciationMasterRepository $fixedAssetDepreciationMasterRepo)
    {
        $this->fixedAssetDepreciationMasterRepository = $fixedAssetDepreciationMasterRepo;
    }

    /**
     * Display a listing of the FixedAssetDepreciationMaster.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->fixedAssetDepreciationMasterRepository->pushCriteria(new RequestCriteria($request));
        $fixedAssetDepreciationMasters = $this->fixedAssetDepreciationMasterRepository->all();

        return view('fixed_asset_depreciation_masters.index')
            ->with('fixedAssetDepreciationMasters', $fixedAssetDepreciationMasters);
    }

    /**
     * Show the form for creating a new FixedAssetDepreciationMaster.
     *
     * @return Response
     */
    public function create()
    {
        return view('fixed_asset_depreciation_masters.create');
    }

    /**
     * Store a newly created FixedAssetDepreciationMaster in storage.
     *
     * @param CreateFixedAssetDepreciationMasterRequest $request
     *
     * @return Response
     */
    public function store(CreateFixedAssetDepreciationMasterRequest $request)
    {
        $input = $request->all();

        $fixedAssetDepreciationMaster = $this->fixedAssetDepreciationMasterRepository->create($input);

        Flash::success('Fixed Asset Depreciation Master saved successfully.');

        return redirect(route('fixedAssetDepreciationMasters.index'));
    }

    /**
     * Display the specified FixedAssetDepreciationMaster.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $fixedAssetDepreciationMaster = $this->fixedAssetDepreciationMasterRepository->findWithoutFail($id);

        if (empty($fixedAssetDepreciationMaster)) {
            Flash::error('Fixed Asset Depreciation Master not found');

            return redirect(route('fixedAssetDepreciationMasters.index'));
        }

        return view('fixed_asset_depreciation_masters.show')->with('fixedAssetDepreciationMaster', $fixedAssetDepreciationMaster);
    }

    /**
     * Show the form for editing the specified FixedAssetDepreciationMaster.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $fixedAssetDepreciationMaster = $this->fixedAssetDepreciationMasterRepository->findWithoutFail($id);

        if (empty($fixedAssetDepreciationMaster)) {
            Flash::error('Fixed Asset Depreciation Master not found');

            return redirect(route('fixedAssetDepreciationMasters.index'));
        }

        return view('fixed_asset_depreciation_masters.edit')->with('fixedAssetDepreciationMaster', $fixedAssetDepreciationMaster);
    }

    /**
     * Update the specified FixedAssetDepreciationMaster in storage.
     *
     * @param  int              $id
     * @param UpdateFixedAssetDepreciationMasterRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateFixedAssetDepreciationMasterRequest $request)
    {
        $fixedAssetDepreciationMaster = $this->fixedAssetDepreciationMasterRepository->findWithoutFail($id);

        if (empty($fixedAssetDepreciationMaster)) {
            Flash::error('Fixed Asset Depreciation Master not found');

            return redirect(route('fixedAssetDepreciationMasters.index'));
        }

        $fixedAssetDepreciationMaster = $this->fixedAssetDepreciationMasterRepository->update($request->all(), $id);

        Flash::success('Fixed Asset Depreciation Master updated successfully.');

        return redirect(route('fixedAssetDepreciationMasters.index'));
    }

    /**
     * Remove the specified FixedAssetDepreciationMaster from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $fixedAssetDepreciationMaster = $this->fixedAssetDepreciationMasterRepository->findWithoutFail($id);

        if (empty($fixedAssetDepreciationMaster)) {
            Flash::error('Fixed Asset Depreciation Master not found');

            return redirect(route('fixedAssetDepreciationMasters.index'));
        }

        $this->fixedAssetDepreciationMasterRepository->delete($id);

        Flash::success('Fixed Asset Depreciation Master deleted successfully.');

        return redirect(route('fixedAssetDepreciationMasters.index'));
    }
}
