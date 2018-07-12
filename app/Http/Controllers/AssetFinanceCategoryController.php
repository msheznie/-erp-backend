<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateAssetFinanceCategoryRequest;
use App\Http\Requests\UpdateAssetFinanceCategoryRequest;
use App\Repositories\AssetFinanceCategoryRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class AssetFinanceCategoryController extends AppBaseController
{
    /** @var  AssetFinanceCategoryRepository */
    private $assetFinanceCategoryRepository;

    public function __construct(AssetFinanceCategoryRepository $assetFinanceCategoryRepo)
    {
        $this->assetFinanceCategoryRepository = $assetFinanceCategoryRepo;
    }

    /**
     * Display a listing of the AssetFinanceCategory.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->assetFinanceCategoryRepository->pushCriteria(new RequestCriteria($request));
        $assetFinanceCategories = $this->assetFinanceCategoryRepository->all();

        return view('asset_finance_categories.index')
            ->with('assetFinanceCategories', $assetFinanceCategories);
    }

    /**
     * Show the form for creating a new AssetFinanceCategory.
     *
     * @return Response
     */
    public function create()
    {
        return view('asset_finance_categories.create');
    }

    /**
     * Store a newly created AssetFinanceCategory in storage.
     *
     * @param CreateAssetFinanceCategoryRequest $request
     *
     * @return Response
     */
    public function store(CreateAssetFinanceCategoryRequest $request)
    {
        $input = $request->all();

        $assetFinanceCategory = $this->assetFinanceCategoryRepository->create($input);

        Flash::success('Asset Finance Category saved successfully.');

        return redirect(route('assetFinanceCategories.index'));
    }

    /**
     * Display the specified AssetFinanceCategory.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $assetFinanceCategory = $this->assetFinanceCategoryRepository->findWithoutFail($id);

        if (empty($assetFinanceCategory)) {
            Flash::error('Asset Finance Category not found');

            return redirect(route('assetFinanceCategories.index'));
        }

        return view('asset_finance_categories.show')->with('assetFinanceCategory', $assetFinanceCategory);
    }

    /**
     * Show the form for editing the specified AssetFinanceCategory.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $assetFinanceCategory = $this->assetFinanceCategoryRepository->findWithoutFail($id);

        if (empty($assetFinanceCategory)) {
            Flash::error('Asset Finance Category not found');

            return redirect(route('assetFinanceCategories.index'));
        }

        return view('asset_finance_categories.edit')->with('assetFinanceCategory', $assetFinanceCategory);
    }

    /**
     * Update the specified AssetFinanceCategory in storage.
     *
     * @param  int              $id
     * @param UpdateAssetFinanceCategoryRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateAssetFinanceCategoryRequest $request)
    {
        $assetFinanceCategory = $this->assetFinanceCategoryRepository->findWithoutFail($id);

        if (empty($assetFinanceCategory)) {
            Flash::error('Asset Finance Category not found');

            return redirect(route('assetFinanceCategories.index'));
        }

        $assetFinanceCategory = $this->assetFinanceCategoryRepository->update($request->all(), $id);

        Flash::success('Asset Finance Category updated successfully.');

        return redirect(route('assetFinanceCategories.index'));
    }

    /**
     * Remove the specified AssetFinanceCategory from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $assetFinanceCategory = $this->assetFinanceCategoryRepository->findWithoutFail($id);

        if (empty($assetFinanceCategory)) {
            Flash::error('Asset Finance Category not found');

            return redirect(route('assetFinanceCategories.index'));
        }

        $this->assetFinanceCategoryRepository->delete($id);

        Flash::success('Asset Finance Category deleted successfully.');

        return redirect(route('assetFinanceCategories.index'));
    }
}
