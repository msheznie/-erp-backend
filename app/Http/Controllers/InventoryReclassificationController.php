<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateInventoryReclassificationRequest;
use App\Http\Requests\UpdateInventoryReclassificationRequest;
use App\Repositories\InventoryReclassificationRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class InventoryReclassificationController extends AppBaseController
{
    /** @var  InventoryReclassificationRepository */
    private $inventoryReclassificationRepository;

    public function __construct(InventoryReclassificationRepository $inventoryReclassificationRepo)
    {
        $this->inventoryReclassificationRepository = $inventoryReclassificationRepo;
    }

    /**
     * Display a listing of the InventoryReclassification.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->inventoryReclassificationRepository->pushCriteria(new RequestCriteria($request));
        $inventoryReclassifications = $this->inventoryReclassificationRepository->all();

        return view('inventory_reclassifications.index')
            ->with('inventoryReclassifications', $inventoryReclassifications);
    }

    /**
     * Show the form for creating a new InventoryReclassification.
     *
     * @return Response
     */
    public function create()
    {
        return view('inventory_reclassifications.create');
    }

    /**
     * Store a newly created InventoryReclassification in storage.
     *
     * @param CreateInventoryReclassificationRequest $request
     *
     * @return Response
     */
    public function store(CreateInventoryReclassificationRequest $request)
    {
        $input = $request->all();

        $inventoryReclassification = $this->inventoryReclassificationRepository->create($input);

        Flash::success('Inventory Reclassification saved successfully.');

        return redirect(route('inventoryReclassifications.index'));
    }

    /**
     * Display the specified InventoryReclassification.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $inventoryReclassification = $this->inventoryReclassificationRepository->findWithoutFail($id);

        if (empty($inventoryReclassification)) {
            Flash::error('Inventory Reclassification not found');

            return redirect(route('inventoryReclassifications.index'));
        }

        return view('inventory_reclassifications.show')->with('inventoryReclassification', $inventoryReclassification);
    }

    /**
     * Show the form for editing the specified InventoryReclassification.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $inventoryReclassification = $this->inventoryReclassificationRepository->findWithoutFail($id);

        if (empty($inventoryReclassification)) {
            Flash::error('Inventory Reclassification not found');

            return redirect(route('inventoryReclassifications.index'));
        }

        return view('inventory_reclassifications.edit')->with('inventoryReclassification', $inventoryReclassification);
    }

    /**
     * Update the specified InventoryReclassification in storage.
     *
     * @param  int              $id
     * @param UpdateInventoryReclassificationRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateInventoryReclassificationRequest $request)
    {
        $inventoryReclassification = $this->inventoryReclassificationRepository->findWithoutFail($id);

        if (empty($inventoryReclassification)) {
            Flash::error('Inventory Reclassification not found');

            return redirect(route('inventoryReclassifications.index'));
        }

        $inventoryReclassification = $this->inventoryReclassificationRepository->update($request->all(), $id);

        Flash::success('Inventory Reclassification updated successfully.');

        return redirect(route('inventoryReclassifications.index'));
    }

    /**
     * Remove the specified InventoryReclassification from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $inventoryReclassification = $this->inventoryReclassificationRepository->findWithoutFail($id);

        if (empty($inventoryReclassification)) {
            Flash::error('Inventory Reclassification not found');

            return redirect(route('inventoryReclassifications.index'));
        }

        $this->inventoryReclassificationRepository->delete($id);

        Flash::success('Inventory Reclassification deleted successfully.');

        return redirect(route('inventoryReclassifications.index'));
    }
}
