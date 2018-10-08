<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateFixedAssetCategorySubRequest;
use App\Http\Requests\UpdateFixedAssetCategorySubRequest;
use App\Repositories\FixedAssetCategorySubRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class FixedAssetCategorySubController extends AppBaseController
{
    /** @var  FixedAssetCategorySubRepository */
    private $fixedAssetCategorySubRepository;

    public function __construct(FixedAssetCategorySubRepository $fixedAssetCategorySubRepo)
    {
        $this->fixedAssetCategorySubRepository = $fixedAssetCategorySubRepo;
    }

    /**
     * Display a listing of the FixedAssetCategorySub.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->fixedAssetCategorySubRepository->pushCriteria(new RequestCriteria($request));
        $fixedAssetCategorySubs = $this->fixedAssetCategorySubRepository->all();

        return view('fixed_asset_category_subs.index')
            ->with('fixedAssetCategorySubs', $fixedAssetCategorySubs);
    }

    /**
     * Show the form for creating a new FixedAssetCategorySub.
     *
     * @return Response
     */
    public function create()
    {
        return view('fixed_asset_category_subs.create');
    }

    /**
     * Store a newly created FixedAssetCategorySub in storage.
     *
     * @param CreateFixedAssetCategorySubRequest $request
     *
     * @return Response
     */
    public function store(CreateFixedAssetCategorySubRequest $request)
    {
        $input = $request->all();

        $fixedAssetCategorySub = $this->fixedAssetCategorySubRepository->create($input);

        Flash::success('Fixed Asset Category Sub saved successfully.');

        return redirect(route('fixedAssetCategorySubs.index'));
    }

    /**
     * Display the specified FixedAssetCategorySub.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $fixedAssetCategorySub = $this->fixedAssetCategorySubRepository->findWithoutFail($id);

        if (empty($fixedAssetCategorySub)) {
            Flash::error('Fixed Asset Category Sub not found');

            return redirect(route('fixedAssetCategorySubs.index'));
        }

        return view('fixed_asset_category_subs.show')->with('fixedAssetCategorySub', $fixedAssetCategorySub);
    }

    /**
     * Show the form for editing the specified FixedAssetCategorySub.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $fixedAssetCategorySub = $this->fixedAssetCategorySubRepository->findWithoutFail($id);

        if (empty($fixedAssetCategorySub)) {
            Flash::error('Fixed Asset Category Sub not found');

            return redirect(route('fixedAssetCategorySubs.index'));
        }

        return view('fixed_asset_category_subs.edit')->with('fixedAssetCategorySub', $fixedAssetCategorySub);
    }

    /**
     * Update the specified FixedAssetCategorySub in storage.
     *
     * @param  int              $id
     * @param UpdateFixedAssetCategorySubRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateFixedAssetCategorySubRequest $request)
    {
        $fixedAssetCategorySub = $this->fixedAssetCategorySubRepository->findWithoutFail($id);

        if (empty($fixedAssetCategorySub)) {
            Flash::error('Fixed Asset Category Sub not found');

            return redirect(route('fixedAssetCategorySubs.index'));
        }

        $fixedAssetCategorySub = $this->fixedAssetCategorySubRepository->update($request->all(), $id);

        Flash::success('Fixed Asset Category Sub updated successfully.');

        return redirect(route('fixedAssetCategorySubs.index'));
    }

    /**
     * Remove the specified FixedAssetCategorySub from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $fixedAssetCategorySub = $this->fixedAssetCategorySubRepository->findWithoutFail($id);

        if (empty($fixedAssetCategorySub)) {
            Flash::error('Fixed Asset Category Sub not found');

            return redirect(route('fixedAssetCategorySubs.index'));
        }

        $this->fixedAssetCategorySubRepository->delete($id);

        Flash::success('Fixed Asset Category Sub deleted successfully.');

        return redirect(route('fixedAssetCategorySubs.index'));
    }
}
