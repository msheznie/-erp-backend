<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatePurchaseOrderAdvPaymentRefferedbackRequest;
use App\Http\Requests\UpdatePurchaseOrderAdvPaymentRefferedbackRequest;
use App\Repositories\PurchaseOrderAdvPaymentRefferedbackRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class PurchaseOrderAdvPaymentRefferedbackController extends AppBaseController
{
    /** @var  PurchaseOrderAdvPaymentRefferedbackRepository */
    private $purchaseOrderAdvPaymentRefferedbackRepository;

    public function __construct(PurchaseOrderAdvPaymentRefferedbackRepository $purchaseOrderAdvPaymentRefferedbackRepo)
    {
        $this->purchaseOrderAdvPaymentRefferedbackRepository = $purchaseOrderAdvPaymentRefferedbackRepo;
    }

    /**
     * Display a listing of the PurchaseOrderAdvPaymentRefferedback.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->purchaseOrderAdvPaymentRefferedbackRepository->pushCriteria(new RequestCriteria($request));
        $purchaseOrderAdvPaymentRefferedbacks = $this->purchaseOrderAdvPaymentRefferedbackRepository->all();

        return view('purchase_order_adv_payment_refferedbacks.index')
            ->with('purchaseOrderAdvPaymentRefferedbacks', $purchaseOrderAdvPaymentRefferedbacks);
    }

    /**
     * Show the form for creating a new PurchaseOrderAdvPaymentRefferedback.
     *
     * @return Response
     */
    public function create()
    {
        return view('purchase_order_adv_payment_refferedbacks.create');
    }

    /**
     * Store a newly created PurchaseOrderAdvPaymentRefferedback in storage.
     *
     * @param CreatePurchaseOrderAdvPaymentRefferedbackRequest $request
     *
     * @return Response
     */
    public function store(CreatePurchaseOrderAdvPaymentRefferedbackRequest $request)
    {
        $input = $request->all();

        $purchaseOrderAdvPaymentRefferedback = $this->purchaseOrderAdvPaymentRefferedbackRepository->create($input);

        Flash::success('Purchase Order Adv Payment Refferedback saved successfully.');

        return redirect(route('purchaseOrderAdvPaymentRefferedbacks.index'));
    }

    /**
     * Display the specified PurchaseOrderAdvPaymentRefferedback.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $purchaseOrderAdvPaymentRefferedback = $this->purchaseOrderAdvPaymentRefferedbackRepository->findWithoutFail($id);

        if (empty($purchaseOrderAdvPaymentRefferedback)) {
            Flash::error('Purchase Order Adv Payment Refferedback not found');

            return redirect(route('purchaseOrderAdvPaymentRefferedbacks.index'));
        }

        return view('purchase_order_adv_payment_refferedbacks.show')->with('purchaseOrderAdvPaymentRefferedback', $purchaseOrderAdvPaymentRefferedback);
    }

    /**
     * Show the form for editing the specified PurchaseOrderAdvPaymentRefferedback.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $purchaseOrderAdvPaymentRefferedback = $this->purchaseOrderAdvPaymentRefferedbackRepository->findWithoutFail($id);

        if (empty($purchaseOrderAdvPaymentRefferedback)) {
            Flash::error('Purchase Order Adv Payment Refferedback not found');

            return redirect(route('purchaseOrderAdvPaymentRefferedbacks.index'));
        }

        return view('purchase_order_adv_payment_refferedbacks.edit')->with('purchaseOrderAdvPaymentRefferedback', $purchaseOrderAdvPaymentRefferedback);
    }

    /**
     * Update the specified PurchaseOrderAdvPaymentRefferedback in storage.
     *
     * @param  int              $id
     * @param UpdatePurchaseOrderAdvPaymentRefferedbackRequest $request
     *
     * @return Response
     */
    public function update($id, UpdatePurchaseOrderAdvPaymentRefferedbackRequest $request)
    {
        $purchaseOrderAdvPaymentRefferedback = $this->purchaseOrderAdvPaymentRefferedbackRepository->findWithoutFail($id);

        if (empty($purchaseOrderAdvPaymentRefferedback)) {
            Flash::error('Purchase Order Adv Payment Refferedback not found');

            return redirect(route('purchaseOrderAdvPaymentRefferedbacks.index'));
        }

        $purchaseOrderAdvPaymentRefferedback = $this->purchaseOrderAdvPaymentRefferedbackRepository->update($request->all(), $id);

        Flash::success('Purchase Order Adv Payment Refferedback updated successfully.');

        return redirect(route('purchaseOrderAdvPaymentRefferedbacks.index'));
    }

    /**
     * Remove the specified PurchaseOrderAdvPaymentRefferedback from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $purchaseOrderAdvPaymentRefferedback = $this->purchaseOrderAdvPaymentRefferedbackRepository->findWithoutFail($id);

        if (empty($purchaseOrderAdvPaymentRefferedback)) {
            Flash::error('Purchase Order Adv Payment Refferedback not found');

            return redirect(route('purchaseOrderAdvPaymentRefferedbacks.index'));
        }

        $this->purchaseOrderAdvPaymentRefferedbackRepository->delete($id);

        Flash::success('Purchase Order Adv Payment Refferedback deleted successfully.');

        return redirect(route('purchaseOrderAdvPaymentRefferedbacks.index'));
    }
}
