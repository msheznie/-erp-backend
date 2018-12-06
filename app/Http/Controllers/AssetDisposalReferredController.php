<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateAssetDisposalReferredRequest;
use App\Http\Requests\UpdateAssetDisposalReferredRequest;
use App\Repositories\AssetDisposalReferredRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class AssetDisposalReferredController extends AppBaseController
{
    /** @var  AssetDisposalReferredRepository */
    private $assetDisposalReferredRepository;

    public function __construct(AssetDisposalReferredRepository $assetDisposalReferredRepo)
    {
        $this->assetDisposalReferredRepository = $assetDisposalReferredRepo;
    }

    /**
     * Display a listing of the AssetDisposalReferred.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->assetDisposalReferredRepository->pushCriteria(new RequestCriteria($request));
        $assetDisposalReferreds = $this->assetDisposalReferredRepository->all();

        return view('asset_disposal_referreds.index')
            ->with('assetDisposalReferreds', $assetDisposalReferreds);
    }

    /**
     * Show the form for creating a new AssetDisposalReferred.
     *
     * @return Response
     */
    public function create()
    {
        return view('asset_disposal_referreds.create');
    }

    /**
     * Store a newly created AssetDisposalReferred in storage.
     *
     * @param CreateAssetDisposalReferredRequest $request
     *
     * @return Response
     */
    public function store(CreateAssetDisposalReferredRequest $request)
    {
        $input = $request->all();

        $assetDisposalReferred = $this->assetDisposalReferredRepository->create($input);

        Flash::success('Asset Disposal Referred saved successfully.');

        return redirect(route('assetDisposalReferreds.index'));
    }

    /**
     * Display the specified AssetDisposalReferred.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $assetDisposalReferred = $this->assetDisposalReferredRepository->findWithoutFail($id);

        if (empty($assetDisposalReferred)) {
            Flash::error('Asset Disposal Referred not found');

            return redirect(route('assetDisposalReferreds.index'));
        }

        return view('asset_disposal_referreds.show')->with('assetDisposalReferred', $assetDisposalReferred);
    }

    /**
     * Show the form for editing the specified AssetDisposalReferred.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $assetDisposalReferred = $this->assetDisposalReferredRepository->findWithoutFail($id);

        if (empty($assetDisposalReferred)) {
            Flash::error('Asset Disposal Referred not found');

            return redirect(route('assetDisposalReferreds.index'));
        }

        return view('asset_disposal_referreds.edit')->with('assetDisposalReferred', $assetDisposalReferred);
    }

    /**
     * Update the specified AssetDisposalReferred in storage.
     *
     * @param  int              $id
     * @param UpdateAssetDisposalReferredRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateAssetDisposalReferredRequest $request)
    {
        $assetDisposalReferred = $this->assetDisposalReferredRepository->findWithoutFail($id);

        if (empty($assetDisposalReferred)) {
            Flash::error('Asset Disposal Referred not found');

            return redirect(route('assetDisposalReferreds.index'));
        }

        $assetDisposalReferred = $this->assetDisposalReferredRepository->update($request->all(), $id);

        Flash::success('Asset Disposal Referred updated successfully.');

        return redirect(route('assetDisposalReferreds.index'));
    }

    /**
     * Remove the specified AssetDisposalReferred from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $assetDisposalReferred = $this->assetDisposalReferredRepository->findWithoutFail($id);

        if (empty($assetDisposalReferred)) {
            Flash::error('Asset Disposal Referred not found');

            return redirect(route('assetDisposalReferreds.index'));
        }

        $this->assetDisposalReferredRepository->delete($id);

        Flash::success('Asset Disposal Referred deleted successfully.');

        return redirect(route('assetDisposalReferreds.index'));
    }
}
