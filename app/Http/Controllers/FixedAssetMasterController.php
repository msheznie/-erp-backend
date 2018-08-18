<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateFixedAssetMasterRequest;
use App\Http\Requests\UpdateFixedAssetMasterRequest;
use App\Repositories\FixedAssetMasterRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class FixedAssetMasterController extends AppBaseController
{
    /** @var  FixedAssetMasterRepository */
    private $fixedAssetMasterRepository;

    public function __construct(FixedAssetMasterRepository $fixedAssetMasterRepo)
    {
        $this->fixedAssetMasterRepository = $fixedAssetMasterRepo;
    }

    /**
     * Display a listing of the FixedAssetMaster.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->fixedAssetMasterRepository->pushCriteria(new RequestCriteria($request));
        $fixedAssetMasters = $this->fixedAssetMasterRepository->all();

        return view('fixed_asset_masters.index')
            ->with('fixedAssetMasters', $fixedAssetMasters);
    }

    /**
     * Show the form for creating a new FixedAssetMaster.
     *
     * @return Response
     */
    public function create()
    {
        return view('fixed_asset_masters.create');
    }

    /**
     * Store a newly created FixedAssetMaster in storage.
     *
     * @param CreateFixedAssetMasterRequest $request
     *
     * @return Response
     */
    public function store(CreateFixedAssetMasterRequest $request)
    {
        $input = $request->all();

        $fixedAssetMaster = $this->fixedAssetMasterRepository->create($input);

        Flash::success('Fixed Asset Master saved successfully.');

        return redirect(route('fixedAssetMasters.index'));
    }

    /**
     * Display the specified FixedAssetMaster.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $fixedAssetMaster = $this->fixedAssetMasterRepository->findWithoutFail($id);

        if (empty($fixedAssetMaster)) {
            Flash::error('Fixed Asset Master not found');

            return redirect(route('fixedAssetMasters.index'));
        }

        return view('fixed_asset_masters.show')->with('fixedAssetMaster', $fixedAssetMaster);
    }

    /**
     * Show the form for editing the specified FixedAssetMaster.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $fixedAssetMaster = $this->fixedAssetMasterRepository->findWithoutFail($id);

        if (empty($fixedAssetMaster)) {
            Flash::error('Fixed Asset Master not found');

            return redirect(route('fixedAssetMasters.index'));
        }

        return view('fixed_asset_masters.edit')->with('fixedAssetMaster', $fixedAssetMaster);
    }

    /**
     * Update the specified FixedAssetMaster in storage.
     *
     * @param  int              $id
     * @param UpdateFixedAssetMasterRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateFixedAssetMasterRequest $request)
    {
        $fixedAssetMaster = $this->fixedAssetMasterRepository->findWithoutFail($id);

        if (empty($fixedAssetMaster)) {
            Flash::error('Fixed Asset Master not found');

            return redirect(route('fixedAssetMasters.index'));
        }

        $fixedAssetMaster = $this->fixedAssetMasterRepository->update($request->all(), $id);

        Flash::success('Fixed Asset Master updated successfully.');

        return redirect(route('fixedAssetMasters.index'));
    }

    /**
     * Remove the specified FixedAssetMaster from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $fixedAssetMaster = $this->fixedAssetMasterRepository->findWithoutFail($id);

        if (empty($fixedAssetMaster)) {
            Flash::error('Fixed Asset Master not found');

            return redirect(route('fixedAssetMasters.index'));
        }

        $this->fixedAssetMasterRepository->delete($id);

        Flash::success('Fixed Asset Master deleted successfully.');

        return redirect(route('fixedAssetMasters.index'));
    }
}
