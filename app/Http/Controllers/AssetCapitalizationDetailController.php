<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateAssetCapitalizationDetailRequest;
use App\Http\Requests\UpdateAssetCapitalizationDetailRequest;
use App\Repositories\AssetCapitalizationDetailRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class AssetCapitalizationDetailController extends AppBaseController
{
    /** @var  AssetCapitalizationDetailRepository */
    private $assetCapitalizationDetailRepository;

    public function __construct(AssetCapitalizationDetailRepository $assetCapitalizationDetailRepo)
    {
        $this->assetCapitalizationDetailRepository = $assetCapitalizationDetailRepo;
    }

    /**
     * Display a listing of the AssetCapitalizationDetail.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->assetCapitalizationDetailRepository->pushCriteria(new RequestCriteria($request));
        $assetCapitalizationDetails = $this->assetCapitalizationDetailRepository->all();

        return view('asset_capitalization_details.index')
            ->with('assetCapitalizationDetails', $assetCapitalizationDetails);
    }

    /**
     * Show the form for creating a new AssetCapitalizationDetail.
     *
     * @return Response
     */
    public function create()
    {
        return view('asset_capitalization_details.create');
    }

    /**
     * Store a newly created AssetCapitalizationDetail in storage.
     *
     * @param CreateAssetCapitalizationDetailRequest $request
     *
     * @return Response
     */
    public function store(CreateAssetCapitalizationDetailRequest $request)
    {
        $input = $request->all();

        $assetCapitalizationDetail = $this->assetCapitalizationDetailRepository->create($input);

        Flash::success('Asset Capitalization Detail saved successfully.');

        return redirect(route('assetCapitalizationDetails.index'));
    }

    /**
     * Display the specified AssetCapitalizationDetail.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $assetCapitalizationDetail = $this->assetCapitalizationDetailRepository->findWithoutFail($id);

        if (empty($assetCapitalizationDetail)) {
            Flash::error('Asset Capitalization Detail not found');

            return redirect(route('assetCapitalizationDetails.index'));
        }

        return view('asset_capitalization_details.show')->with('assetCapitalizationDetail', $assetCapitalizationDetail);
    }

    /**
     * Show the form for editing the specified AssetCapitalizationDetail.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $assetCapitalizationDetail = $this->assetCapitalizationDetailRepository->findWithoutFail($id);

        if (empty($assetCapitalizationDetail)) {
            Flash::error('Asset Capitalization Detail not found');

            return redirect(route('assetCapitalizationDetails.index'));
        }

        return view('asset_capitalization_details.edit')->with('assetCapitalizationDetail', $assetCapitalizationDetail);
    }

    /**
     * Update the specified AssetCapitalizationDetail in storage.
     *
     * @param  int              $id
     * @param UpdateAssetCapitalizationDetailRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateAssetCapitalizationDetailRequest $request)
    {
        $assetCapitalizationDetail = $this->assetCapitalizationDetailRepository->findWithoutFail($id);

        if (empty($assetCapitalizationDetail)) {
            Flash::error('Asset Capitalization Detail not found');

            return redirect(route('assetCapitalizationDetails.index'));
        }

        $assetCapitalizationDetail = $this->assetCapitalizationDetailRepository->update($request->all(), $id);

        Flash::success('Asset Capitalization Detail updated successfully.');

        return redirect(route('assetCapitalizationDetails.index'));
    }

    /**
     * Remove the specified AssetCapitalizationDetail from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $assetCapitalizationDetail = $this->assetCapitalizationDetailRepository->findWithoutFail($id);

        if (empty($assetCapitalizationDetail)) {
            Flash::error('Asset Capitalization Detail not found');

            return redirect(route('assetCapitalizationDetails.index'));
        }

        $this->assetCapitalizationDetailRepository->delete($id);

        Flash::success('Asset Capitalization Detail deleted successfully.');

        return redirect(route('assetCapitalizationDetails.index'));
    }
}
