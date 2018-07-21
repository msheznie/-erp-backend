<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateAddonCostCategoriesRequest;
use App\Http\Requests\UpdateAddonCostCategoriesRequest;
use App\Repositories\AddonCostCategoriesRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class AddonCostCategoriesController extends AppBaseController
{
    /** @var  AddonCostCategoriesRepository */
    private $addonCostCategoriesRepository;

    public function __construct(AddonCostCategoriesRepository $addonCostCategoriesRepo)
    {
        $this->addonCostCategoriesRepository = $addonCostCategoriesRepo;
    }

    /**
     * Display a listing of the AddonCostCategories.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->addonCostCategoriesRepository->pushCriteria(new RequestCriteria($request));
        $addonCostCategories = $this->addonCostCategoriesRepository->all();

        return view('addon_cost_categories.index')
            ->with('addonCostCategories', $addonCostCategories);
    }

    /**
     * Show the form for creating a new AddonCostCategories.
     *
     * @return Response
     */
    public function create()
    {
        return view('addon_cost_categories.create');
    }

    /**
     * Store a newly created AddonCostCategories in storage.
     *
     * @param CreateAddonCostCategoriesRequest $request
     *
     * @return Response
     */
    public function store(CreateAddonCostCategoriesRequest $request)
    {
        $input = $request->all();

        $addonCostCategories = $this->addonCostCategoriesRepository->create($input);

        Flash::success('Addon Cost Categories saved successfully.');

        return redirect(route('addonCostCategories.index'));
    }

    /**
     * Display the specified AddonCostCategories.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $addonCostCategories = $this->addonCostCategoriesRepository->findWithoutFail($id);

        if (empty($addonCostCategories)) {
            Flash::error('Addon Cost Categories not found');

            return redirect(route('addonCostCategories.index'));
        }

        return view('addon_cost_categories.show')->with('addonCostCategories', $addonCostCategories);
    }

    /**
     * Show the form for editing the specified AddonCostCategories.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $addonCostCategories = $this->addonCostCategoriesRepository->findWithoutFail($id);

        if (empty($addonCostCategories)) {
            Flash::error('Addon Cost Categories not found');

            return redirect(route('addonCostCategories.index'));
        }

        return view('addon_cost_categories.edit')->with('addonCostCategories', $addonCostCategories);
    }

    /**
     * Update the specified AddonCostCategories in storage.
     *
     * @param  int              $id
     * @param UpdateAddonCostCategoriesRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateAddonCostCategoriesRequest $request)
    {
        $addonCostCategories = $this->addonCostCategoriesRepository->findWithoutFail($id);

        if (empty($addonCostCategories)) {
            Flash::error('Addon Cost Categories not found');

            return redirect(route('addonCostCategories.index'));
        }

        $addonCostCategories = $this->addonCostCategoriesRepository->update($request->all(), $id);

        Flash::success('Addon Cost Categories updated successfully.');

        return redirect(route('addonCostCategories.index'));
    }

    /**
     * Remove the specified AddonCostCategories from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $addonCostCategories = $this->addonCostCategoriesRepository->findWithoutFail($id);

        if (empty($addonCostCategories)) {
            Flash::error('Addon Cost Categories not found');

            return redirect(route('addonCostCategories.index'));
        }

        $this->addonCostCategoriesRepository->delete($id);

        Flash::success('Addon Cost Categories deleted successfully.');

        return redirect(route('addonCostCategories.index'));
    }
}
