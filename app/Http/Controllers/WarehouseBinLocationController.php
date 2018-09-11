<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateWarehouseBinLocationRequest;
use App\Http\Requests\UpdateWarehouseBinLocationRequest;
use App\Repositories\WarehouseBinLocationRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class WarehouseBinLocationController extends AppBaseController
{
    /** @var  WarehouseBinLocationRepository */
    private $warehouseBinLocationRepository;

    public function __construct(WarehouseBinLocationRepository $warehouseBinLocationRepo)
    {
        $this->warehouseBinLocationRepository = $warehouseBinLocationRepo;
    }

    /**
     * Display a listing of the WarehouseBinLocation.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->warehouseBinLocationRepository->pushCriteria(new RequestCriteria($request));
        $warehouseBinLocations = $this->warehouseBinLocationRepository->all();

        return view('warehouse_bin_locations.index')
            ->with('warehouseBinLocations', $warehouseBinLocations);
    }

    /**
     * Show the form for creating a new WarehouseBinLocation.
     *
     * @return Response
     */
    public function create()
    {
        return view('warehouse_bin_locations.create');
    }

    /**
     * Store a newly created WarehouseBinLocation in storage.
     *
     * @param CreateWarehouseBinLocationRequest $request
     *
     * @return Response
     */
    public function store(CreateWarehouseBinLocationRequest $request)
    {
        $input = $request->all();

        $warehouseBinLocation = $this->warehouseBinLocationRepository->create($input);

        Flash::success('Warehouse Bin Location saved successfully.');

        return redirect(route('warehouseBinLocations.index'));
    }

    /**
     * Display the specified WarehouseBinLocation.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $warehouseBinLocation = $this->warehouseBinLocationRepository->findWithoutFail($id);

        if (empty($warehouseBinLocation)) {
            Flash::error('Warehouse Bin Location not found');

            return redirect(route('warehouseBinLocations.index'));
        }

        return view('warehouse_bin_locations.show')->with('warehouseBinLocation', $warehouseBinLocation);
    }

    /**
     * Show the form for editing the specified WarehouseBinLocation.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $warehouseBinLocation = $this->warehouseBinLocationRepository->findWithoutFail($id);

        if (empty($warehouseBinLocation)) {
            Flash::error('Warehouse Bin Location not found');

            return redirect(route('warehouseBinLocations.index'));
        }

        return view('warehouse_bin_locations.edit')->with('warehouseBinLocation', $warehouseBinLocation);
    }

    /**
     * Update the specified WarehouseBinLocation in storage.
     *
     * @param  int              $id
     * @param UpdateWarehouseBinLocationRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateWarehouseBinLocationRequest $request)
    {
        $warehouseBinLocation = $this->warehouseBinLocationRepository->findWithoutFail($id);

        if (empty($warehouseBinLocation)) {
            Flash::error('Warehouse Bin Location not found');

            return redirect(route('warehouseBinLocations.index'));
        }

        $warehouseBinLocation = $this->warehouseBinLocationRepository->update($request->all(), $id);

        Flash::success('Warehouse Bin Location updated successfully.');

        return redirect(route('warehouseBinLocations.index'));
    }

    /**
     * Remove the specified WarehouseBinLocation from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $warehouseBinLocation = $this->warehouseBinLocationRepository->findWithoutFail($id);

        if (empty($warehouseBinLocation)) {
            Flash::error('Warehouse Bin Location not found');

            return redirect(route('warehouseBinLocations.index'));
        }

        $this->warehouseBinLocationRepository->delete($id);

        Flash::success('Warehouse Bin Location deleted successfully.');

        return redirect(route('warehouseBinLocations.index'));
    }
}
