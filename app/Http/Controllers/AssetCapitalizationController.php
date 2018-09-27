<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateAssetCapitalizationRequest;
use App\Http\Requests\UpdateAssetCapitalizationRequest;
use App\Repositories\AssetCapitalizationRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class AssetCapitalizationController extends AppBaseController
{
    /** @var  AssetCapitalizationRepository */
    private $assetCapitalizationRepository;

    public function __construct(AssetCapitalizationRepository $assetCapitalizationRepo)
    {
        $this->assetCapitalizationRepository = $assetCapitalizationRepo;
    }

    /**
     * Display a listing of the AssetCapitalization.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->assetCapitalizationRepository->pushCriteria(new RequestCriteria($request));
        $assetCapitalizations = $this->assetCapitalizationRepository->all();

        return view('asset_capitalizations.index')
            ->with('assetCapitalizations', $assetCapitalizations);
    }

    /**
     * Show the form for creating a new AssetCapitalization.
     *
     * @return Response
     */
    public function create()
    {
        return view('asset_capitalizations.create');
    }

    /**
     * Store a newly created AssetCapitalization in storage.
     *
     * @param CreateAssetCapitalizationRequest $request
     *
     * @return Response
     */
    public function store(CreateAssetCapitalizationRequest $request)
    {
        $input = $request->all();

        $assetCapitalization = $this->assetCapitalizationRepository->create($input);

        Flash::success('Asset Capitalization saved successfully.');

        return redirect(route('assetCapitalizations.index'));
    }

    /**
     * Display the specified AssetCapitalization.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $assetCapitalization = $this->assetCapitalizationRepository->findWithoutFail($id);

        if (empty($assetCapitalization)) {
            Flash::error('Asset Capitalization not found');

            return redirect(route('assetCapitalizations.index'));
        }

        return view('asset_capitalizations.show')->with('assetCapitalization', $assetCapitalization);
    }

    /**
     * Show the form for editing the specified AssetCapitalization.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $assetCapitalization = $this->assetCapitalizationRepository->findWithoutFail($id);

        if (empty($assetCapitalization)) {
            Flash::error('Asset Capitalization not found');

            return redirect(route('assetCapitalizations.index'));
        }

        return view('asset_capitalizations.edit')->with('assetCapitalization', $assetCapitalization);
    }

    /**
     * Update the specified AssetCapitalization in storage.
     *
     * @param  int              $id
     * @param UpdateAssetCapitalizationRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateAssetCapitalizationRequest $request)
    {
        $assetCapitalization = $this->assetCapitalizationRepository->findWithoutFail($id);

        if (empty($assetCapitalization)) {
            Flash::error('Asset Capitalization not found');

            return redirect(route('assetCapitalizations.index'));
        }

        $assetCapitalization = $this->assetCapitalizationRepository->update($request->all(), $id);

        Flash::success('Asset Capitalization updated successfully.');

        return redirect(route('assetCapitalizations.index'));
    }

    /**
     * Remove the specified AssetCapitalization from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $assetCapitalization = $this->assetCapitalizationRepository->findWithoutFail($id);

        if (empty($assetCapitalization)) {
            Flash::error('Asset Capitalization not found');

            return redirect(route('assetCapitalizations.index'));
        }

        $this->assetCapitalizationRepository->delete($id);

        Flash::success('Asset Capitalization deleted successfully.');

        return redirect(route('assetCapitalizations.index'));
    }
}
