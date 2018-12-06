<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateAssetDisposalDetailReferredRequest;
use App\Http\Requests\UpdateAssetDisposalDetailReferredRequest;
use App\Repositories\AssetDisposalDetailReferredRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class AssetDisposalDetailReferredController extends AppBaseController
{
    /** @var  AssetDisposalDetailReferredRepository */
    private $assetDisposalDetailReferredRepository;

    public function __construct(AssetDisposalDetailReferredRepository $assetDisposalDetailReferredRepo)
    {
        $this->assetDisposalDetailReferredRepository = $assetDisposalDetailReferredRepo;
    }

    /**
     * Display a listing of the AssetDisposalDetailReferred.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->assetDisposalDetailReferredRepository->pushCriteria(new RequestCriteria($request));
        $assetDisposalDetailReferreds = $this->assetDisposalDetailReferredRepository->all();

        return view('asset_disposal_detail_referreds.index')
            ->with('assetDisposalDetailReferreds', $assetDisposalDetailReferreds);
    }

    /**
     * Show the form for creating a new AssetDisposalDetailReferred.
     *
     * @return Response
     */
    public function create()
    {
        return view('asset_disposal_detail_referreds.create');
    }

    /**
     * Store a newly created AssetDisposalDetailReferred in storage.
     *
     * @param CreateAssetDisposalDetailReferredRequest $request
     *
     * @return Response
     */
    public function store(CreateAssetDisposalDetailReferredRequest $request)
    {
        $input = $request->all();

        $assetDisposalDetailReferred = $this->assetDisposalDetailReferredRepository->create($input);

        Flash::success('Asset Disposal Detail Referred saved successfully.');

        return redirect(route('assetDisposalDetailReferreds.index'));
    }

    /**
     * Display the specified AssetDisposalDetailReferred.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $assetDisposalDetailReferred = $this->assetDisposalDetailReferredRepository->findWithoutFail($id);

        if (empty($assetDisposalDetailReferred)) {
            Flash::error('Asset Disposal Detail Referred not found');

            return redirect(route('assetDisposalDetailReferreds.index'));
        }

        return view('asset_disposal_detail_referreds.show')->with('assetDisposalDetailReferred', $assetDisposalDetailReferred);
    }

    /**
     * Show the form for editing the specified AssetDisposalDetailReferred.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $assetDisposalDetailReferred = $this->assetDisposalDetailReferredRepository->findWithoutFail($id);

        if (empty($assetDisposalDetailReferred)) {
            Flash::error('Asset Disposal Detail Referred not found');

            return redirect(route('assetDisposalDetailReferreds.index'));
        }

        return view('asset_disposal_detail_referreds.edit')->with('assetDisposalDetailReferred', $assetDisposalDetailReferred);
    }

    /**
     * Update the specified AssetDisposalDetailReferred in storage.
     *
     * @param  int              $id
     * @param UpdateAssetDisposalDetailReferredRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateAssetDisposalDetailReferredRequest $request)
    {
        $assetDisposalDetailReferred = $this->assetDisposalDetailReferredRepository->findWithoutFail($id);

        if (empty($assetDisposalDetailReferred)) {
            Flash::error('Asset Disposal Detail Referred not found');

            return redirect(route('assetDisposalDetailReferreds.index'));
        }

        $assetDisposalDetailReferred = $this->assetDisposalDetailReferredRepository->update($request->all(), $id);

        Flash::success('Asset Disposal Detail Referred updated successfully.');

        return redirect(route('assetDisposalDetailReferreds.index'));
    }

    /**
     * Remove the specified AssetDisposalDetailReferred from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $assetDisposalDetailReferred = $this->assetDisposalDetailReferredRepository->findWithoutFail($id);

        if (empty($assetDisposalDetailReferred)) {
            Flash::error('Asset Disposal Detail Referred not found');

            return redirect(route('assetDisposalDetailReferreds.index'));
        }

        $this->assetDisposalDetailReferredRepository->delete($id);

        Flash::success('Asset Disposal Detail Referred deleted successfully.');

        return redirect(route('assetDisposalDetailReferreds.index'));
    }
}
