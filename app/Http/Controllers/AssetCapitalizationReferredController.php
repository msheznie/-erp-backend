<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateAssetCapitalizationReferredRequest;
use App\Http\Requests\UpdateAssetCapitalizationReferredRequest;
use App\Repositories\AssetCapitalizationReferredRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class AssetCapitalizationReferredController extends AppBaseController
{
    /** @var  AssetCapitalizationReferredRepository */
    private $assetCapitalizationReferredRepository;

    public function __construct(AssetCapitalizationReferredRepository $assetCapitalizationReferredRepo)
    {
        $this->assetCapitalizationReferredRepository = $assetCapitalizationReferredRepo;
    }

    /**
     * Display a listing of the AssetCapitalizationReferred.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->assetCapitalizationReferredRepository->pushCriteria(new RequestCriteria($request));
        $assetCapitalizationReferreds = $this->assetCapitalizationReferredRepository->all();

        return view('asset_capitalization_referreds.index')
            ->with('assetCapitalizationReferreds', $assetCapitalizationReferreds);
    }

    /**
     * Show the form for creating a new AssetCapitalizationReferred.
     *
     * @return Response
     */
    public function create()
    {
        return view('asset_capitalization_referreds.create');
    }

    /**
     * Store a newly created AssetCapitalizationReferred in storage.
     *
     * @param CreateAssetCapitalizationReferredRequest $request
     *
     * @return Response
     */
    public function store(CreateAssetCapitalizationReferredRequest $request)
    {
        $input = $request->all();

        $assetCapitalizationReferred = $this->assetCapitalizationReferredRepository->create($input);

        Flash::success('Asset Capitalization Referred saved successfully.');

        return redirect(route('assetCapitalizationReferreds.index'));
    }

    /**
     * Display the specified AssetCapitalizationReferred.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $assetCapitalizationReferred = $this->assetCapitalizationReferredRepository->findWithoutFail($id);

        if (empty($assetCapitalizationReferred)) {
            Flash::error('Asset Capitalization Referred not found');

            return redirect(route('assetCapitalizationReferreds.index'));
        }

        return view('asset_capitalization_referreds.show')->with('assetCapitalizationReferred', $assetCapitalizationReferred);
    }

    /**
     * Show the form for editing the specified AssetCapitalizationReferred.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $assetCapitalizationReferred = $this->assetCapitalizationReferredRepository->findWithoutFail($id);

        if (empty($assetCapitalizationReferred)) {
            Flash::error('Asset Capitalization Referred not found');

            return redirect(route('assetCapitalizationReferreds.index'));
        }

        return view('asset_capitalization_referreds.edit')->with('assetCapitalizationReferred', $assetCapitalizationReferred);
    }

    /**
     * Update the specified AssetCapitalizationReferred in storage.
     *
     * @param  int              $id
     * @param UpdateAssetCapitalizationReferredRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateAssetCapitalizationReferredRequest $request)
    {
        $assetCapitalizationReferred = $this->assetCapitalizationReferredRepository->findWithoutFail($id);

        if (empty($assetCapitalizationReferred)) {
            Flash::error('Asset Capitalization Referred not found');

            return redirect(route('assetCapitalizationReferreds.index'));
        }

        $assetCapitalizationReferred = $this->assetCapitalizationReferredRepository->update($request->all(), $id);

        Flash::success('Asset Capitalization Referred updated successfully.');

        return redirect(route('assetCapitalizationReferreds.index'));
    }

    /**
     * Remove the specified AssetCapitalizationReferred from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $assetCapitalizationReferred = $this->assetCapitalizationReferredRepository->findWithoutFail($id);

        if (empty($assetCapitalizationReferred)) {
            Flash::error('Asset Capitalization Referred not found');

            return redirect(route('assetCapitalizationReferreds.index'));
        }

        $this->assetCapitalizationReferredRepository->delete($id);

        Flash::success('Asset Capitalization Referred deleted successfully.');

        return redirect(route('assetCapitalizationReferreds.index'));
    }
}
