<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateAssetDisposalTypeRequest;
use App\Http\Requests\UpdateAssetDisposalTypeRequest;
use App\Repositories\AssetDisposalTypeRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class AssetDisposalTypeController extends AppBaseController
{
    /** @var  AssetDisposalTypeRepository */
    private $assetDisposalTypeRepository;

    public function __construct(AssetDisposalTypeRepository $assetDisposalTypeRepo)
    {
        $this->assetDisposalTypeRepository = $assetDisposalTypeRepo;
    }

    /**
     * Display a listing of the AssetDisposalType.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->assetDisposalTypeRepository->pushCriteria(new RequestCriteria($request));
        $assetDisposalTypes = $this->assetDisposalTypeRepository->all();

        return view('asset_disposal_types.index')
            ->with('assetDisposalTypes', $assetDisposalTypes);
    }

    /**
     * Show the form for creating a new AssetDisposalType.
     *
     * @return Response
     */
    public function create()
    {
        return view('asset_disposal_types.create');
    }

    /**
     * Store a newly created AssetDisposalType in storage.
     *
     * @param CreateAssetDisposalTypeRequest $request
     *
     * @return Response
     */
    public function store(CreateAssetDisposalTypeRequest $request)
    {
        $input = $request->all();

        $assetDisposalType = $this->assetDisposalTypeRepository->create($input);

        Flash::success('Asset Disposal Type saved successfully.');

        return redirect(route('assetDisposalTypes.index'));
    }

    /**
     * Display the specified AssetDisposalType.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $assetDisposalType = $this->assetDisposalTypeRepository->findWithoutFail($id);

        if (empty($assetDisposalType)) {
            Flash::error('Asset Disposal Type not found');

            return redirect(route('assetDisposalTypes.index'));
        }

        return view('asset_disposal_types.show')->with('assetDisposalType', $assetDisposalType);
    }

    /**
     * Show the form for editing the specified AssetDisposalType.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $assetDisposalType = $this->assetDisposalTypeRepository->findWithoutFail($id);

        if (empty($assetDisposalType)) {
            Flash::error('Asset Disposal Type not found');

            return redirect(route('assetDisposalTypes.index'));
        }

        return view('asset_disposal_types.edit')->with('assetDisposalType', $assetDisposalType);
    }

    /**
     * Update the specified AssetDisposalType in storage.
     *
     * @param  int              $id
     * @param UpdateAssetDisposalTypeRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateAssetDisposalTypeRequest $request)
    {
        $assetDisposalType = $this->assetDisposalTypeRepository->findWithoutFail($id);

        if (empty($assetDisposalType)) {
            Flash::error('Asset Disposal Type not found');

            return redirect(route('assetDisposalTypes.index'));
        }

        $assetDisposalType = $this->assetDisposalTypeRepository->update($request->all(), $id);

        Flash::success('Asset Disposal Type updated successfully.');

        return redirect(route('assetDisposalTypes.index'));
    }

    /**
     * Remove the specified AssetDisposalType from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $assetDisposalType = $this->assetDisposalTypeRepository->findWithoutFail($id);

        if (empty($assetDisposalType)) {
            Flash::error('Asset Disposal Type not found');

            return redirect(route('assetDisposalTypes.index'));
        }

        $this->assetDisposalTypeRepository->delete($id);

        Flash::success('Asset Disposal Type deleted successfully.');

        return redirect(route('assetDisposalTypes.index'));
    }
}
