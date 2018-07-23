<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatePurchaseOrderDetailsRefferedHistoryRequest;
use App\Http\Requests\UpdatePurchaseOrderDetailsRefferedHistoryRequest;
use App\Repositories\PurchaseOrderDetailsRefferedHistoryRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class PurchaseOrderDetailsRefferedHistoryController extends AppBaseController
{
    /** @var  PurchaseOrderDetailsRefferedHistoryRepository */
    private $purchaseOrderDetailsRefferedHistoryRepository;

    public function __construct(PurchaseOrderDetailsRefferedHistoryRepository $purchaseOrderDetailsRefferedHistoryRepo)
    {
        $this->purchaseOrderDetailsRefferedHistoryRepository = $purchaseOrderDetailsRefferedHistoryRepo;
    }

    /**
     * Display a listing of the PurchaseOrderDetailsRefferedHistory.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->purchaseOrderDetailsRefferedHistoryRepository->pushCriteria(new RequestCriteria($request));
        $purchaseOrderDetailsRefferedHistories = $this->purchaseOrderDetailsRefferedHistoryRepository->all();

        return view('purchase_order_details_reffered_histories.index')
            ->with('purchaseOrderDetailsRefferedHistories', $purchaseOrderDetailsRefferedHistories);
    }

    /**
     * Show the form for creating a new PurchaseOrderDetailsRefferedHistory.
     *
     * @return Response
     */
    public function create()
    {
        return view('purchase_order_details_reffered_histories.create');
    }

    /**
     * Store a newly created PurchaseOrderDetailsRefferedHistory in storage.
     *
     * @param CreatePurchaseOrderDetailsRefferedHistoryRequest $request
     *
     * @return Response
     */
    public function store(CreatePurchaseOrderDetailsRefferedHistoryRequest $request)
    {
        $input = $request->all();

        $purchaseOrderDetailsRefferedHistory = $this->purchaseOrderDetailsRefferedHistoryRepository->create($input);

        Flash::success('Purchase Order Details Reffered History saved successfully.');

        return redirect(route('purchaseOrderDetailsRefferedHistories.index'));
    }

    /**
     * Display the specified PurchaseOrderDetailsRefferedHistory.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $purchaseOrderDetailsRefferedHistory = $this->purchaseOrderDetailsRefferedHistoryRepository->findWithoutFail($id);

        if (empty($purchaseOrderDetailsRefferedHistory)) {
            Flash::error('Purchase Order Details Reffered History not found');

            return redirect(route('purchaseOrderDetailsRefferedHistories.index'));
        }

        return view('purchase_order_details_reffered_histories.show')->with('purchaseOrderDetailsRefferedHistory', $purchaseOrderDetailsRefferedHistory);
    }

    /**
     * Show the form for editing the specified PurchaseOrderDetailsRefferedHistory.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $purchaseOrderDetailsRefferedHistory = $this->purchaseOrderDetailsRefferedHistoryRepository->findWithoutFail($id);

        if (empty($purchaseOrderDetailsRefferedHistory)) {
            Flash::error('Purchase Order Details Reffered History not found');

            return redirect(route('purchaseOrderDetailsRefferedHistories.index'));
        }

        return view('purchase_order_details_reffered_histories.edit')->with('purchaseOrderDetailsRefferedHistory', $purchaseOrderDetailsRefferedHistory);
    }

    /**
     * Update the specified PurchaseOrderDetailsRefferedHistory in storage.
     *
     * @param  int              $id
     * @param UpdatePurchaseOrderDetailsRefferedHistoryRequest $request
     *
     * @return Response
     */
    public function update($id, UpdatePurchaseOrderDetailsRefferedHistoryRequest $request)
    {
        $purchaseOrderDetailsRefferedHistory = $this->purchaseOrderDetailsRefferedHistoryRepository->findWithoutFail($id);

        if (empty($purchaseOrderDetailsRefferedHistory)) {
            Flash::error('Purchase Order Details Reffered History not found');

            return redirect(route('purchaseOrderDetailsRefferedHistories.index'));
        }

        $purchaseOrderDetailsRefferedHistory = $this->purchaseOrderDetailsRefferedHistoryRepository->update($request->all(), $id);

        Flash::success('Purchase Order Details Reffered History updated successfully.');

        return redirect(route('purchaseOrderDetailsRefferedHistories.index'));
    }

    /**
     * Remove the specified PurchaseOrderDetailsRefferedHistory from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $purchaseOrderDetailsRefferedHistory = $this->purchaseOrderDetailsRefferedHistoryRepository->findWithoutFail($id);

        if (empty($purchaseOrderDetailsRefferedHistory)) {
            Flash::error('Purchase Order Details Reffered History not found');

            return redirect(route('purchaseOrderDetailsRefferedHistories.index'));
        }

        $this->purchaseOrderDetailsRefferedHistoryRepository->delete($id);

        Flash::success('Purchase Order Details Reffered History deleted successfully.');

        return redirect(route('purchaseOrderDetailsRefferedHistories.index'));
    }
}
