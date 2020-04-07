<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateWarehouseSubLevelsRequest;
use App\Http\Requests\UpdateWarehouseSubLevelsRequest;
use App\Repositories\WarehouseSubLevelsRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class WarehouseSubLevelsController extends AppBaseController
{
    /** @var  WarehouseSubLevelsRepository */
    private $warehouseSubLevelsRepository;

    public function __construct(WarehouseSubLevelsRepository $warehouseSubLevelsRepo)
    {
        $this->warehouseSubLevelsRepository = $warehouseSubLevelsRepo;
    }

    /**
     * Display a listing of the WarehouseSubLevels.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->warehouseSubLevelsRepository->pushCriteria(new RequestCriteria($request));
        $warehouseSubLevels = $this->warehouseSubLevelsRepository->all();

        return view('warehouse_sub_levels.index')
            ->with('warehouseSubLevels', $warehouseSubLevels);
    }

    /**
     * Show the form for creating a new WarehouseSubLevels.
     *
     * @return Response
     */
    public function create()
    {
        return view('warehouse_sub_levels.create');
    }

    /**
     * Store a newly created WarehouseSubLevels in storage.
     *
     * @param CreateWarehouseSubLevelsRequest $request
     *
     * @return Response
     */
    public function store(CreateWarehouseSubLevelsRequest $request)
    {
        $input = $request->all();

        $warehouseSubLevels = $this->warehouseSubLevelsRepository->create($input);

        Flash::success('Warehouse Sub Levels saved successfully.');

        return redirect(route('warehouseSubLevels.index'));
    }

    /**
     * Display the specified WarehouseSubLevels.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $warehouseSubLevels = $this->warehouseSubLevelsRepository->findWithoutFail($id);

        if (empty($warehouseSubLevels)) {
            Flash::error('Warehouse Sub Levels not found');

            return redirect(route('warehouseSubLevels.index'));
        }

        return view('warehouse_sub_levels.show')->with('warehouseSubLevels', $warehouseSubLevels);
    }

    /**
     * Show the form for editing the specified WarehouseSubLevels.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $warehouseSubLevels = $this->warehouseSubLevelsRepository->findWithoutFail($id);

        if (empty($warehouseSubLevels)) {
            Flash::error('Warehouse Sub Levels not found');

            return redirect(route('warehouseSubLevels.index'));
        }

        return view('warehouse_sub_levels.edit')->with('warehouseSubLevels', $warehouseSubLevels);
    }

    /**
     * Update the specified WarehouseSubLevels in storage.
     *
     * @param  int              $id
     * @param UpdateWarehouseSubLevelsRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateWarehouseSubLevelsRequest $request)
    {
        $warehouseSubLevels = $this->warehouseSubLevelsRepository->findWithoutFail($id);

        if (empty($warehouseSubLevels)) {
            Flash::error('Warehouse Sub Levels not found');

            return redirect(route('warehouseSubLevels.index'));
        }

        $warehouseSubLevels = $this->warehouseSubLevelsRepository->update($request->all(), $id);

        Flash::success('Warehouse Sub Levels updated successfully.');

        return redirect(route('warehouseSubLevels.index'));
    }

    /**
     * Remove the specified WarehouseSubLevels from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $warehouseSubLevels = $this->warehouseSubLevelsRepository->findWithoutFail($id);

        if (empty($warehouseSubLevels)) {
            Flash::error('Warehouse Sub Levels not found');

            return redirect(route('warehouseSubLevels.index'));
        }

        $this->warehouseSubLevelsRepository->delete($id);

        Flash::success('Warehouse Sub Levels deleted successfully.');

        return redirect(route('warehouseSubLevels.index'));
    }
}
