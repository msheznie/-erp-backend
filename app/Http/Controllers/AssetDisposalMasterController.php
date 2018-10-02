<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateAssetDisposalMasterRequest;
use App\Http\Requests\UpdateAssetDisposalMasterRequest;
use App\Repositories\AssetDisposalMasterRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class AssetDisposalMasterController extends AppBaseController
{
    /** @var  AssetDisposalMasterRepository */
    private $assetDisposalMasterRepository;

    public function __construct(AssetDisposalMasterRepository $assetDisposalMasterRepo)
    {
        $this->assetDisposalMasterRepository = $assetDisposalMasterRepo;
    }

    /**
     * Display a listing of the AssetDisposalMaster.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->assetDisposalMasterRepository->pushCriteria(new RequestCriteria($request));
        $assetDisposalMasters = $this->assetDisposalMasterRepository->all();

        return view('asset_disposal_masters.index')
            ->with('assetDisposalMasters', $assetDisposalMasters);
    }

    /**
     * Show the form for creating a new AssetDisposalMaster.
     *
     * @return Response
     */
    public function create()
    {
        return view('asset_disposal_masters.create');
    }

    /**
     * Store a newly created AssetDisposalMaster in storage.
     *
     * @param CreateAssetDisposalMasterRequest $request
     *
     * @return Response
     */
    public function store(CreateAssetDisposalMasterRequest $request)
    {
        $input = $request->all();

        $assetDisposalMaster = $this->assetDisposalMasterRepository->create($input);

        Flash::success('Asset Disposal Master saved successfully.');

        return redirect(route('assetDisposalMasters.index'));
    }

    /**
     * Display the specified AssetDisposalMaster.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $assetDisposalMaster = $this->assetDisposalMasterRepository->findWithoutFail($id);

        if (empty($assetDisposalMaster)) {
            Flash::error('Asset Disposal Master not found');

            return redirect(route('assetDisposalMasters.index'));
        }

        return view('asset_disposal_masters.show')->with('assetDisposalMaster', $assetDisposalMaster);
    }

    /**
     * Show the form for editing the specified AssetDisposalMaster.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $assetDisposalMaster = $this->assetDisposalMasterRepository->findWithoutFail($id);

        if (empty($assetDisposalMaster)) {
            Flash::error('Asset Disposal Master not found');

            return redirect(route('assetDisposalMasters.index'));
        }

        return view('asset_disposal_masters.edit')->with('assetDisposalMaster', $assetDisposalMaster);
    }

    /**
     * Update the specified AssetDisposalMaster in storage.
     *
     * @param  int              $id
     * @param UpdateAssetDisposalMasterRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateAssetDisposalMasterRequest $request)
    {
        $assetDisposalMaster = $this->assetDisposalMasterRepository->findWithoutFail($id);

        if (empty($assetDisposalMaster)) {
            Flash::error('Asset Disposal Master not found');

            return redirect(route('assetDisposalMasters.index'));
        }

        $assetDisposalMaster = $this->assetDisposalMasterRepository->update($request->all(), $id);

        Flash::success('Asset Disposal Master updated successfully.');

        return redirect(route('assetDisposalMasters.index'));
    }

    /**
     * Remove the specified AssetDisposalMaster from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $assetDisposalMaster = $this->assetDisposalMasterRepository->findWithoutFail($id);

        if (empty($assetDisposalMaster)) {
            Flash::error('Asset Disposal Master not found');

            return redirect(route('assetDisposalMasters.index'));
        }

        $this->assetDisposalMasterRepository->delete($id);

        Flash::success('Asset Disposal Master deleted successfully.');

        return redirect(route('assetDisposalMasters.index'));
    }
}
