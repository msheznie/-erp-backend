<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateInventoryReclassificationDetailRequest;
use App\Http\Requests\UpdateInventoryReclassificationDetailRequest;
use App\Repositories\InventoryReclassificationDetailRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class InventoryReclassificationDetailController extends AppBaseController
{
    /** @var  InventoryReclassificationDetailRepository */
    private $inventoryReclassificationDetailRepository;

    public function __construct(InventoryReclassificationDetailRepository $inventoryReclassificationDetailRepo)
    {
        $this->inventoryReclassificationDetailRepository = $inventoryReclassificationDetailRepo;
    }

    /**
     * Display a listing of the InventoryReclassificationDetail.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->inventoryReclassificationDetailRepository->pushCriteria(new RequestCriteria($request));
        $inventoryReclassificationDetails = $this->inventoryReclassificationDetailRepository->all();

        return view('inventory_reclassification_details.index')
            ->with('inventoryReclassificationDetails', $inventoryReclassificationDetails);
    }

    /**
     * Show the form for creating a new InventoryReclassificationDetail.
     *
     * @return Response
     */
    public function create()
    {
        return view('inventory_reclassification_details.create');
    }

    /**
     * Store a newly created InventoryReclassificationDetail in storage.
     *
     * @param CreateInventoryReclassificationDetailRequest $request
     *
     * @return Response
     */
    public function store(CreateInventoryReclassificationDetailRequest $request)
    {
        $input = $request->all();

        $inventoryReclassificationDetail = $this->inventoryReclassificationDetailRepository->create($input);

        Flash::success('Inventory Reclassification Detail saved successfully.');

        return redirect(route('inventoryReclassificationDetails.index'));
    }

    /**
     * Display the specified InventoryReclassificationDetail.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $inventoryReclassificationDetail = $this->inventoryReclassificationDetailRepository->findWithoutFail($id);

        if (empty($inventoryReclassificationDetail)) {
            Flash::error('Inventory Reclassification Detail not found');

            return redirect(route('inventoryReclassificationDetails.index'));
        }

        return view('inventory_reclassification_details.show')->with('inventoryReclassificationDetail', $inventoryReclassificationDetail);
    }

    /**
     * Show the form for editing the specified InventoryReclassificationDetail.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $inventoryReclassificationDetail = $this->inventoryReclassificationDetailRepository->findWithoutFail($id);

        if (empty($inventoryReclassificationDetail)) {
            Flash::error('Inventory Reclassification Detail not found');

            return redirect(route('inventoryReclassificationDetails.index'));
        }

        return view('inventory_reclassification_details.edit')->with('inventoryReclassificationDetail', $inventoryReclassificationDetail);
    }

    /**
     * Update the specified InventoryReclassificationDetail in storage.
     *
     * @param  int              $id
     * @param UpdateInventoryReclassificationDetailRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateInventoryReclassificationDetailRequest $request)
    {
        $inventoryReclassificationDetail = $this->inventoryReclassificationDetailRepository->findWithoutFail($id);

        if (empty($inventoryReclassificationDetail)) {
            Flash::error('Inventory Reclassification Detail not found');

            return redirect(route('inventoryReclassificationDetails.index'));
        }

        $inventoryReclassificationDetail = $this->inventoryReclassificationDetailRepository->update($request->all(), $id);

        Flash::success('Inventory Reclassification Detail updated successfully.');

        return redirect(route('inventoryReclassificationDetails.index'));
    }

    /**
     * Remove the specified InventoryReclassificationDetail from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $inventoryReclassificationDetail = $this->inventoryReclassificationDetailRepository->findWithoutFail($id);

        if (empty($inventoryReclassificationDetail)) {
            Flash::error('Inventory Reclassification Detail not found');

            return redirect(route('inventoryReclassificationDetails.index'));
        }

        $this->inventoryReclassificationDetailRepository->delete($id);

        Flash::success('Inventory Reclassification Detail deleted successfully.');

        return redirect(route('inventoryReclassificationDetails.index'));
    }
}
