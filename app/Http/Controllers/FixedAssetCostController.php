<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateFixedAssetCostRequest;
use App\Http\Requests\UpdateFixedAssetCostRequest;
use App\Repositories\FixedAssetCostRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class FixedAssetCostController extends AppBaseController
{
    /** @var  FixedAssetCostRepository */
    private $fixedAssetCostRepository;

    public function __construct(FixedAssetCostRepository $fixedAssetCostRepo)
    {
        $this->fixedAssetCostRepository = $fixedAssetCostRepo;
    }

    /**
     * Display a listing of the FixedAssetCost.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->fixedAssetCostRepository->pushCriteria(new RequestCriteria($request));
        $fixedAssetCosts = $this->fixedAssetCostRepository->all();

        return view('fixed_asset_costs.index')
            ->with('fixedAssetCosts', $fixedAssetCosts);
    }

    /**
     * Show the form for creating a new FixedAssetCost.
     *
     * @return Response
     */
    public function create()
    {
        return view('fixed_asset_costs.create');
    }

    /**
     * Store a newly created FixedAssetCost in storage.
     *
     * @param CreateFixedAssetCostRequest $request
     *
     * @return Response
     */
    public function store(CreateFixedAssetCostRequest $request)
    {
        $input = $request->all();

        $fixedAssetCost = $this->fixedAssetCostRepository->create($input);

        Flash::success('Fixed Asset Cost saved successfully.');

        return redirect(route('fixedAssetCosts.index'));
    }

    /**
     * Display the specified FixedAssetCost.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $fixedAssetCost = $this->fixedAssetCostRepository->findWithoutFail($id);

        if (empty($fixedAssetCost)) {
            Flash::error('Fixed Asset Cost not found');

            return redirect(route('fixedAssetCosts.index'));
        }

        return view('fixed_asset_costs.show')->with('fixedAssetCost', $fixedAssetCost);
    }

    /**
     * Show the form for editing the specified FixedAssetCost.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $fixedAssetCost = $this->fixedAssetCostRepository->findWithoutFail($id);

        if (empty($fixedAssetCost)) {
            Flash::error('Fixed Asset Cost not found');

            return redirect(route('fixedAssetCosts.index'));
        }

        return view('fixed_asset_costs.edit')->with('fixedAssetCost', $fixedAssetCost);
    }

    /**
     * Update the specified FixedAssetCost in storage.
     *
     * @param  int              $id
     * @param UpdateFixedAssetCostRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateFixedAssetCostRequest $request)
    {
        $fixedAssetCost = $this->fixedAssetCostRepository->findWithoutFail($id);

        if (empty($fixedAssetCost)) {
            Flash::error('Fixed Asset Cost not found');

            return redirect(route('fixedAssetCosts.index'));
        }

        $fixedAssetCost = $this->fixedAssetCostRepository->update($request->all(), $id);

        Flash::success('Fixed Asset Cost updated successfully.');

        return redirect(route('fixedAssetCosts.index'));
    }

    /**
     * Remove the specified FixedAssetCost from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $fixedAssetCost = $this->fixedAssetCostRepository->findWithoutFail($id);

        if (empty($fixedAssetCost)) {
            Flash::error('Fixed Asset Cost not found');

            return redirect(route('fixedAssetCosts.index'));
        }

        $this->fixedAssetCostRepository->delete($id);

        Flash::success('Fixed Asset Cost deleted successfully.');

        return redirect(route('fixedAssetCosts.index'));
    }
}
