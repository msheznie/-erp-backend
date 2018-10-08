<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateAssetTypeRequest;
use App\Http\Requests\UpdateAssetTypeRequest;
use App\Repositories\AssetTypeRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class AssetTypeController extends AppBaseController
{
    /** @var  AssetTypeRepository */
    private $assetTypeRepository;

    public function __construct(AssetTypeRepository $assetTypeRepo)
    {
        $this->assetTypeRepository = $assetTypeRepo;
    }

    /**
     * Display a listing of the AssetType.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->assetTypeRepository->pushCriteria(new RequestCriteria($request));
        $assetTypes = $this->assetTypeRepository->all();

        return view('asset_types.index')
            ->with('assetTypes', $assetTypes);
    }

    /**
     * Show the form for creating a new AssetType.
     *
     * @return Response
     */
    public function create()
    {
        return view('asset_types.create');
    }

    /**
     * Store a newly created AssetType in storage.
     *
     * @param CreateAssetTypeRequest $request
     *
     * @return Response
     */
    public function store(CreateAssetTypeRequest $request)
    {
        $input = $request->all();

        $assetType = $this->assetTypeRepository->create($input);

        Flash::success('Asset Type saved successfully.');

        return redirect(route('assetTypes.index'));
    }

    /**
     * Display the specified AssetType.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $assetType = $this->assetTypeRepository->findWithoutFail($id);

        if (empty($assetType)) {
            Flash::error('Asset Type not found');

            return redirect(route('assetTypes.index'));
        }

        return view('asset_types.show')->with('assetType', $assetType);
    }

    /**
     * Show the form for editing the specified AssetType.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $assetType = $this->assetTypeRepository->findWithoutFail($id);

        if (empty($assetType)) {
            Flash::error('Asset Type not found');

            return redirect(route('assetTypes.index'));
        }

        return view('asset_types.edit')->with('assetType', $assetType);
    }

    /**
     * Update the specified AssetType in storage.
     *
     * @param  int              $id
     * @param UpdateAssetTypeRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateAssetTypeRequest $request)
    {
        $assetType = $this->assetTypeRepository->findWithoutFail($id);

        if (empty($assetType)) {
            Flash::error('Asset Type not found');

            return redirect(route('assetTypes.index'));
        }

        $assetType = $this->assetTypeRepository->update($request->all(), $id);

        Flash::success('Asset Type updated successfully.');

        return redirect(route('assetTypes.index'));
    }

    /**
     * Remove the specified AssetType from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $assetType = $this->assetTypeRepository->findWithoutFail($id);

        if (empty($assetType)) {
            Flash::error('Asset Type not found');

            return redirect(route('assetTypes.index'));
        }

        $this->assetTypeRepository->delete($id);

        Flash::success('Asset Type deleted successfully.');

        return redirect(route('assetTypes.index'));
    }
}
