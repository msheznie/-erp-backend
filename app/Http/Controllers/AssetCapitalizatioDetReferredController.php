<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateAssetCapitalizatioDetReferredRequest;
use App\Http\Requests\UpdateAssetCapitalizatioDetReferredRequest;
use App\Repositories\AssetCapitalizatioDetReferredRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class AssetCapitalizatioDetReferredController extends AppBaseController
{
    /** @var  AssetCapitalizatioDetReferredRepository */
    private $assetCapitalizatioDetReferredRepository;

    public function __construct(AssetCapitalizatioDetReferredRepository $assetCapitalizatioDetReferredRepo)
    {
        $this->assetCapitalizatioDetReferredRepository = $assetCapitalizatioDetReferredRepo;
    }

    /**
     * Display a listing of the AssetCapitalizatioDetReferred.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->assetCapitalizatioDetReferredRepository->pushCriteria(new RequestCriteria($request));
        $assetCapitalizatioDetReferreds = $this->assetCapitalizatioDetReferredRepository->all();

        return view('asset_capitalizatio_det_referreds.index')
            ->with('assetCapitalizatioDetReferreds', $assetCapitalizatioDetReferreds);
    }

    /**
     * Show the form for creating a new AssetCapitalizatioDetReferred.
     *
     * @return Response
     */
    public function create()
    {
        return view('asset_capitalizatio_det_referreds.create');
    }

    /**
     * Store a newly created AssetCapitalizatioDetReferred in storage.
     *
     * @param CreateAssetCapitalizatioDetReferredRequest $request
     *
     * @return Response
     */
    public function store(CreateAssetCapitalizatioDetReferredRequest $request)
    {
        $input = $request->all();

        $assetCapitalizatioDetReferred = $this->assetCapitalizatioDetReferredRepository->create($input);

        Flash::success('Asset Capitalizatio Det Referred saved successfully.');

        return redirect(route('assetCapitalizatioDetReferreds.index'));
    }

    /**
     * Display the specified AssetCapitalizatioDetReferred.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $assetCapitalizatioDetReferred = $this->assetCapitalizatioDetReferredRepository->findWithoutFail($id);

        if (empty($assetCapitalizatioDetReferred)) {
            Flash::error('Asset Capitalizatio Det Referred not found');

            return redirect(route('assetCapitalizatioDetReferreds.index'));
        }

        return view('asset_capitalizatio_det_referreds.show')->with('assetCapitalizatioDetReferred', $assetCapitalizatioDetReferred);
    }

    /**
     * Show the form for editing the specified AssetCapitalizatioDetReferred.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $assetCapitalizatioDetReferred = $this->assetCapitalizatioDetReferredRepository->findWithoutFail($id);

        if (empty($assetCapitalizatioDetReferred)) {
            Flash::error('Asset Capitalizatio Det Referred not found');

            return redirect(route('assetCapitalizatioDetReferreds.index'));
        }

        return view('asset_capitalizatio_det_referreds.edit')->with('assetCapitalizatioDetReferred', $assetCapitalizatioDetReferred);
    }

    /**
     * Update the specified AssetCapitalizatioDetReferred in storage.
     *
     * @param  int              $id
     * @param UpdateAssetCapitalizatioDetReferredRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateAssetCapitalizatioDetReferredRequest $request)
    {
        $assetCapitalizatioDetReferred = $this->assetCapitalizatioDetReferredRepository->findWithoutFail($id);

        if (empty($assetCapitalizatioDetReferred)) {
            Flash::error('Asset Capitalizatio Det Referred not found');

            return redirect(route('assetCapitalizatioDetReferreds.index'));
        }

        $assetCapitalizatioDetReferred = $this->assetCapitalizatioDetReferredRepository->update($request->all(), $id);

        Flash::success('Asset Capitalizatio Det Referred updated successfully.');

        return redirect(route('assetCapitalizatioDetReferreds.index'));
    }

    /**
     * Remove the specified AssetCapitalizatioDetReferred from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $assetCapitalizatioDetReferred = $this->assetCapitalizatioDetReferredRepository->findWithoutFail($id);

        if (empty($assetCapitalizatioDetReferred)) {
            Flash::error('Asset Capitalizatio Det Referred not found');

            return redirect(route('assetCapitalizatioDetReferreds.index'));
        }

        $this->assetCapitalizatioDetReferredRepository->delete($id);

        Flash::success('Asset Capitalizatio Det Referred deleted successfully.');

        return redirect(route('assetCapitalizatioDetReferreds.index'));
    }
}
