<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatePurchaseOrderMasterRefferedHistoryRequest;
use App\Http\Requests\UpdatePurchaseOrderMasterRefferedHistoryRequest;
use App\Repositories\PurchaseOrderMasterRefferedHistoryRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class PurchaseOrderMasterRefferedHistoryController extends AppBaseController
{
    /** @var  PurchaseOrderMasterRefferedHistoryRepository */
    private $purchaseOrderMasterRefferedHistoryRepository;

    public function __construct(PurchaseOrderMasterRefferedHistoryRepository $purchaseOrderMasterRefferedHistoryRepo)
    {
        $this->purchaseOrderMasterRefferedHistoryRepository = $purchaseOrderMasterRefferedHistoryRepo;
    }

    /**
     * Display a listing of the PurchaseOrderMasterRefferedHistory.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->purchaseOrderMasterRefferedHistoryRepository->pushCriteria(new RequestCriteria($request));
        $purchaseOrderMasterRefferedHistories = $this->purchaseOrderMasterRefferedHistoryRepository->all();

        return view('purchase_order_master_reffered_histories.index')
            ->with('purchaseOrderMasterRefferedHistories', $purchaseOrderMasterRefferedHistories);
    }

    /**
     * Show the form for creating a new PurchaseOrderMasterRefferedHistory.
     *
     * @return Response
     */
    public function create()
    {
        return view('purchase_order_master_reffered_histories.create');
    }

    /**
     * Store a newly created PurchaseOrderMasterRefferedHistory in storage.
     *
     * @param CreatePurchaseOrderMasterRefferedHistoryRequest $request
     *
     * @return Response
     */
    public function store(CreatePurchaseOrderMasterRefferedHistoryRequest $request)
    {
        $input = $request->all();

        $purchaseOrderMasterRefferedHistory = $this->purchaseOrderMasterRefferedHistoryRepository->create($input);

        Flash::success('Purchase Order Master Reffered History saved successfully.');

        return redirect(route('purchaseOrderMasterRefferedHistories.index'));
    }

    /**
     * Display the specified PurchaseOrderMasterRefferedHistory.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $purchaseOrderMasterRefferedHistory = $this->purchaseOrderMasterRefferedHistoryRepository->findWithoutFail($id);

        if (empty($purchaseOrderMasterRefferedHistory)) {
            Flash::error('Purchase Order Master Reffered History not found');

            return redirect(route('purchaseOrderMasterRefferedHistories.index'));
        }

        return view('purchase_order_master_reffered_histories.show')->with('purchaseOrderMasterRefferedHistory', $purchaseOrderMasterRefferedHistory);
    }

    /**
     * Show the form for editing the specified PurchaseOrderMasterRefferedHistory.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $purchaseOrderMasterRefferedHistory = $this->purchaseOrderMasterRefferedHistoryRepository->findWithoutFail($id);

        if (empty($purchaseOrderMasterRefferedHistory)) {
            Flash::error('Purchase Order Master Reffered History not found');

            return redirect(route('purchaseOrderMasterRefferedHistories.index'));
        }

        return view('purchase_order_master_reffered_histories.edit')->with('purchaseOrderMasterRefferedHistory', $purchaseOrderMasterRefferedHistory);
    }

    /**
     * Update the specified PurchaseOrderMasterRefferedHistory in storage.
     *
     * @param  int              $id
     * @param UpdatePurchaseOrderMasterRefferedHistoryRequest $request
     *
     * @return Response
     */
    public function update($id, UpdatePurchaseOrderMasterRefferedHistoryRequest $request)
    {
        $purchaseOrderMasterRefferedHistory = $this->purchaseOrderMasterRefferedHistoryRepository->findWithoutFail($id);

        if (empty($purchaseOrderMasterRefferedHistory)) {
            Flash::error('Purchase Order Master Reffered History not found');

            return redirect(route('purchaseOrderMasterRefferedHistories.index'));
        }

        $purchaseOrderMasterRefferedHistory = $this->purchaseOrderMasterRefferedHistoryRepository->update($request->all(), $id);

        Flash::success('Purchase Order Master Reffered History updated successfully.');

        return redirect(route('purchaseOrderMasterRefferedHistories.index'));
    }

    /**
     * Remove the specified PurchaseOrderMasterRefferedHistory from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $purchaseOrderMasterRefferedHistory = $this->purchaseOrderMasterRefferedHistoryRepository->findWithoutFail($id);

        if (empty($purchaseOrderMasterRefferedHistory)) {
            Flash::error('Purchase Order Master Reffered History not found');

            return redirect(route('purchaseOrderMasterRefferedHistories.index'));
        }

        $this->purchaseOrderMasterRefferedHistoryRepository->delete($id);

        Flash::success('Purchase Order Master Reffered History deleted successfully.');

        return redirect(route('purchaseOrderMasterRefferedHistories.index'));
    }
}
