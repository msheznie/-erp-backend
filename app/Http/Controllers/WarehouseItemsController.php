<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateWarehouseItemsRequest;
use App\Http\Requests\UpdateWarehouseItemsRequest;
use App\Repositories\WarehouseItemsRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class WarehouseItemsController extends AppBaseController
{
    /** @var  WarehouseItemsRepository */
    private $warehouseItemsRepository;

    public function __construct(WarehouseItemsRepository $warehouseItemsRepo)
    {
        $this->warehouseItemsRepository = $warehouseItemsRepo;
    }

    /**
     * Display a listing of the WarehouseItems.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->warehouseItemsRepository->pushCriteria(new RequestCriteria($request));
        $warehouseItems = $this->warehouseItemsRepository->all();

        return view('warehouse_items.index')
            ->with('warehouseItems', $warehouseItems);
    }

    /**
     * Show the form for creating a new WarehouseItems.
     *
     * @return Response
     */
    public function create()
    {
        return view('warehouse_items.create');
    }

    /**
     * Store a newly created WarehouseItems in storage.
     *
     * @param CreateWarehouseItemsRequest $request
     *
     * @return Response
     */
    public function store(CreateWarehouseItemsRequest $request)
    {
        $input = $request->all();

        $warehouseItems = $this->warehouseItemsRepository->create($input);

        Flash::success('Warehouse Items saved successfully.');

        return redirect(route('warehouseItems.index'));
    }

    /**
     * Display the specified WarehouseItems.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $warehouseItems = $this->warehouseItemsRepository->findWithoutFail($id);

        if (empty($warehouseItems)) {
            Flash::error('Warehouse Items not found');

            return redirect(route('warehouseItems.index'));
        }

        return view('warehouse_items.show')->with('warehouseItems', $warehouseItems);
    }

    /**
     * Show the form for editing the specified WarehouseItems.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $warehouseItems = $this->warehouseItemsRepository->findWithoutFail($id);

        if (empty($warehouseItems)) {
            Flash::error('Warehouse Items not found');

            return redirect(route('warehouseItems.index'));
        }

        return view('warehouse_items.edit')->with('warehouseItems', $warehouseItems);
    }

    /**
     * Update the specified WarehouseItems in storage.
     *
     * @param  int              $id
     * @param UpdateWarehouseItemsRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateWarehouseItemsRequest $request)
    {
        $warehouseItems = $this->warehouseItemsRepository->findWithoutFail($id);

        if (empty($warehouseItems)) {
            Flash::error('Warehouse Items not found');

            return redirect(route('warehouseItems.index'));
        }

        $warehouseItems = $this->warehouseItemsRepository->update($request->all(), $id);

        Flash::success('Warehouse Items updated successfully.');

        return redirect(route('warehouseItems.index'));
    }

    /**
     * Remove the specified WarehouseItems from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $warehouseItems = $this->warehouseItemsRepository->findWithoutFail($id);

        if (empty($warehouseItems)) {
            Flash::error('Warehouse Items not found');

            return redirect(route('warehouseItems.index'));
        }

        $this->warehouseItemsRepository->delete($id);

        Flash::success('Warehouse Items deleted successfully.');

        return redirect(route('warehouseItems.index'));
    }
}
