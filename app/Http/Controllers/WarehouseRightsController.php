<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateWarehouseRightsRequest;
use App\Http\Requests\UpdateWarehouseRightsRequest;
use App\Repositories\WarehouseRightsRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class WarehouseRightsController extends AppBaseController
{
    /** @var  WarehouseRightsRepository */
    private $warehouseRightsRepository;

    public function __construct(WarehouseRightsRepository $warehouseRightsRepo)
    {
        $this->warehouseRightsRepository = $warehouseRightsRepo;
    }

    /**
     * Display a listing of the WarehouseRights.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->warehouseRightsRepository->pushCriteria(new RequestCriteria($request));
        $warehouseRights = $this->warehouseRightsRepository->all();

        return view('warehouse_rights.index')
            ->with('warehouseRights', $warehouseRights);
    }

    /**
     * Show the form for creating a new WarehouseRights.
     *
     * @return Response
     */
    public function create()
    {
        return view('warehouse_rights.create');
    }

    /**
     * Store a newly created WarehouseRights in storage.
     *
     * @param CreateWarehouseRightsRequest $request
     *
     * @return Response
     */
    public function store(CreateWarehouseRightsRequest $request)
    {
        $input = $request->all();

        $warehouseRights = $this->warehouseRightsRepository->create($input);

        Flash::success('Warehouse Rights saved successfully.');

        return redirect(route('warehouseRights.index'));
    }

    /**
     * Display the specified WarehouseRights.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $warehouseRights = $this->warehouseRightsRepository->findWithoutFail($id);

        if (empty($warehouseRights)) {
            Flash::error('Warehouse Rights not found');

            return redirect(route('warehouseRights.index'));
        }

        return view('warehouse_rights.show')->with('warehouseRights', $warehouseRights);
    }

    /**
     * Show the form for editing the specified WarehouseRights.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $warehouseRights = $this->warehouseRightsRepository->findWithoutFail($id);

        if (empty($warehouseRights)) {
            Flash::error('Warehouse Rights not found');

            return redirect(route('warehouseRights.index'));
        }

        return view('warehouse_rights.edit')->with('warehouseRights', $warehouseRights);
    }

    /**
     * Update the specified WarehouseRights in storage.
     *
     * @param  int              $id
     * @param UpdateWarehouseRightsRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateWarehouseRightsRequest $request)
    {
        $warehouseRights = $this->warehouseRightsRepository->findWithoutFail($id);

        if (empty($warehouseRights)) {
            Flash::error('Warehouse Rights not found');

            return redirect(route('warehouseRights.index'));
        }

        $warehouseRights = $this->warehouseRightsRepository->update($request->all(), $id);

        Flash::success('Warehouse Rights updated successfully.');

        return redirect(route('warehouseRights.index'));
    }

    /**
     * Remove the specified WarehouseRights from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $warehouseRights = $this->warehouseRightsRepository->findWithoutFail($id);

        if (empty($warehouseRights)) {
            Flash::error('Warehouse Rights not found');

            return redirect(route('warehouseRights.index'));
        }

        $this->warehouseRightsRepository->delete($id);

        Flash::success('Warehouse Rights deleted successfully.');

        return redirect(route('warehouseRights.index'));
    }
}
