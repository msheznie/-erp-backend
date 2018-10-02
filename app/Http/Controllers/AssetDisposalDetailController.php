<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateAssetDisposalDetailRequest;
use App\Http\Requests\UpdateAssetDisposalDetailRequest;
use App\Repositories\AssetDisposalDetailRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class AssetDisposalDetailController extends AppBaseController
{
    /** @var  AssetDisposalDetailRepository */
    private $assetDisposalDetailRepository;

    public function __construct(AssetDisposalDetailRepository $assetDisposalDetailRepo)
    {
        $this->assetDisposalDetailRepository = $assetDisposalDetailRepo;
    }

    /**
     * Display a listing of the AssetDisposalDetail.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->assetDisposalDetailRepository->pushCriteria(new RequestCriteria($request));
        $assetDisposalDetails = $this->assetDisposalDetailRepository->all();

        return view('asset_disposal_details.index')
            ->with('assetDisposalDetails', $assetDisposalDetails);
    }

    /**
     * Show the form for creating a new AssetDisposalDetail.
     *
     * @return Response
     */
    public function create()
    {
        return view('asset_disposal_details.create');
    }

    /**
     * Store a newly created AssetDisposalDetail in storage.
     *
     * @param CreateAssetDisposalDetailRequest $request
     *
     * @return Response
     */
    public function store(CreateAssetDisposalDetailRequest $request)
    {
        $input = $request->all();

        $assetDisposalDetail = $this->assetDisposalDetailRepository->create($input);

        Flash::success('Asset Disposal Detail saved successfully.');

        return redirect(route('assetDisposalDetails.index'));
    }

    /**
     * Display the specified AssetDisposalDetail.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $assetDisposalDetail = $this->assetDisposalDetailRepository->findWithoutFail($id);

        if (empty($assetDisposalDetail)) {
            Flash::error('Asset Disposal Detail not found');

            return redirect(route('assetDisposalDetails.index'));
        }

        return view('asset_disposal_details.show')->with('assetDisposalDetail', $assetDisposalDetail);
    }

    /**
     * Show the form for editing the specified AssetDisposalDetail.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $assetDisposalDetail = $this->assetDisposalDetailRepository->findWithoutFail($id);

        if (empty($assetDisposalDetail)) {
            Flash::error('Asset Disposal Detail not found');

            return redirect(route('assetDisposalDetails.index'));
        }

        return view('asset_disposal_details.edit')->with('assetDisposalDetail', $assetDisposalDetail);
    }

    /**
     * Update the specified AssetDisposalDetail in storage.
     *
     * @param  int              $id
     * @param UpdateAssetDisposalDetailRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateAssetDisposalDetailRequest $request)
    {
        $assetDisposalDetail = $this->assetDisposalDetailRepository->findWithoutFail($id);

        if (empty($assetDisposalDetail)) {
            Flash::error('Asset Disposal Detail not found');

            return redirect(route('assetDisposalDetails.index'));
        }

        $assetDisposalDetail = $this->assetDisposalDetailRepository->update($request->all(), $id);

        Flash::success('Asset Disposal Detail updated successfully.');

        return redirect(route('assetDisposalDetails.index'));
    }

    /**
     * Remove the specified AssetDisposalDetail from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $assetDisposalDetail = $this->assetDisposalDetailRepository->findWithoutFail($id);

        if (empty($assetDisposalDetail)) {
            Flash::error('Asset Disposal Detail not found');

            return redirect(route('assetDisposalDetails.index'));
        }

        $this->assetDisposalDetailRepository->delete($id);

        Flash::success('Asset Disposal Detail deleted successfully.');

        return redirect(route('assetDisposalDetails.index'));
    }
}
