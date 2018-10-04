<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateFixedAssetCategoryRequest;
use App\Http\Requests\UpdateFixedAssetCategoryRequest;
use App\Repositories\FixedAssetCategoryRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class FixedAssetCategoryController extends AppBaseController
{
    /** @var  FixedAssetCategoryRepository */
    private $fixedAssetCategoryRepository;

    public function __construct(FixedAssetCategoryRepository $fixedAssetCategoryRepo)
    {
        $this->fixedAssetCategoryRepository = $fixedAssetCategoryRepo;
    }

    /**
     * Display a listing of the FixedAssetCategory.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->fixedAssetCategoryRepository->pushCriteria(new RequestCriteria($request));
        $fixedAssetCategories = $this->fixedAssetCategoryRepository->all();

        return view('fixed_asset_categories.index')
            ->with('fixedAssetCategories', $fixedAssetCategories);
    }

    /**
     * Show the form for creating a new FixedAssetCategory.
     *
     * @return Response
     */
    public function create()
    {
        return view('fixed_asset_categories.create');
    }

    /**
     * Store a newly created FixedAssetCategory in storage.
     *
     * @param CreateFixedAssetCategoryRequest $request
     *
     * @return Response
     */
    public function store(CreateFixedAssetCategoryRequest $request)
    {
        $input = $request->all();

        $fixedAssetCategory = $this->fixedAssetCategoryRepository->create($input);

        Flash::success('Fixed Asset Category saved successfully.');

        return redirect(route('fixedAssetCategories.index'));
    }

    /**
     * Display the specified FixedAssetCategory.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $fixedAssetCategory = $this->fixedAssetCategoryRepository->findWithoutFail($id);

        if (empty($fixedAssetCategory)) {
            Flash::error('Fixed Asset Category not found');

            return redirect(route('fixedAssetCategories.index'));
        }

        return view('fixed_asset_categories.show')->with('fixedAssetCategory', $fixedAssetCategory);
    }

    /**
     * Show the form for editing the specified FixedAssetCategory.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $fixedAssetCategory = $this->fixedAssetCategoryRepository->findWithoutFail($id);

        if (empty($fixedAssetCategory)) {
            Flash::error('Fixed Asset Category not found');

            return redirect(route('fixedAssetCategories.index'));
        }

        return view('fixed_asset_categories.edit')->with('fixedAssetCategory', $fixedAssetCategory);
    }

    /**
     * Update the specified FixedAssetCategory in storage.
     *
     * @param  int              $id
     * @param UpdateFixedAssetCategoryRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateFixedAssetCategoryRequest $request)
    {
        $fixedAssetCategory = $this->fixedAssetCategoryRepository->findWithoutFail($id);

        if (empty($fixedAssetCategory)) {
            Flash::error('Fixed Asset Category not found');

            return redirect(route('fixedAssetCategories.index'));
        }

        $fixedAssetCategory = $this->fixedAssetCategoryRepository->update($request->all(), $id);

        Flash::success('Fixed Asset Category updated successfully.');

        return redirect(route('fixedAssetCategories.index'));
    }

    /**
     * Remove the specified FixedAssetCategory from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $fixedAssetCategory = $this->fixedAssetCategoryRepository->findWithoutFail($id);

        if (empty($fixedAssetCategory)) {
            Flash::error('Fixed Asset Category not found');

            return redirect(route('fixedAssetCategories.index'));
        }

        $this->fixedAssetCategoryRepository->delete($id);

        Flash::success('Fixed Asset Category deleted successfully.');

        return redirect(route('fixedAssetCategories.index'));
    }
}
