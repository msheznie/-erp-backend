<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatePaySupplierInvoiceDetailReferbackRequest;
use App\Http\Requests\UpdatePaySupplierInvoiceDetailReferbackRequest;
use App\Repositories\PaySupplierInvoiceDetailReferbackRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class PaySupplierInvoiceDetailReferbackController extends AppBaseController
{
    /** @var  PaySupplierInvoiceDetailReferbackRepository */
    private $paySupplierInvoiceDetailReferbackRepository;

    public function __construct(PaySupplierInvoiceDetailReferbackRepository $paySupplierInvoiceDetailReferbackRepo)
    {
        $this->paySupplierInvoiceDetailReferbackRepository = $paySupplierInvoiceDetailReferbackRepo;
    }

    /**
     * Display a listing of the PaySupplierInvoiceDetailReferback.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->paySupplierInvoiceDetailReferbackRepository->pushCriteria(new RequestCriteria($request));
        $paySupplierInvoiceDetailReferbacks = $this->paySupplierInvoiceDetailReferbackRepository->all();

        return view('pay_supplier_invoice_detail_referbacks.index')
            ->with('paySupplierInvoiceDetailReferbacks', $paySupplierInvoiceDetailReferbacks);
    }

    /**
     * Show the form for creating a new PaySupplierInvoiceDetailReferback.
     *
     * @return Response
     */
    public function create()
    {
        return view('pay_supplier_invoice_detail_referbacks.create');
    }

    /**
     * Store a newly created PaySupplierInvoiceDetailReferback in storage.
     *
     * @param CreatePaySupplierInvoiceDetailReferbackRequest $request
     *
     * @return Response
     */
    public function store(CreatePaySupplierInvoiceDetailReferbackRequest $request)
    {
        $input = $request->all();

        $paySupplierInvoiceDetailReferback = $this->paySupplierInvoiceDetailReferbackRepository->create($input);

        Flash::success('Pay Supplier Invoice Detail Referback saved successfully.');

        return redirect(route('paySupplierInvoiceDetailReferbacks.index'));
    }

    /**
     * Display the specified PaySupplierInvoiceDetailReferback.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $paySupplierInvoiceDetailReferback = $this->paySupplierInvoiceDetailReferbackRepository->findWithoutFail($id);

        if (empty($paySupplierInvoiceDetailReferback)) {
            Flash::error('Pay Supplier Invoice Detail Referback not found');

            return redirect(route('paySupplierInvoiceDetailReferbacks.index'));
        }

        return view('pay_supplier_invoice_detail_referbacks.show')->with('paySupplierInvoiceDetailReferback', $paySupplierInvoiceDetailReferback);
    }

    /**
     * Show the form for editing the specified PaySupplierInvoiceDetailReferback.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $paySupplierInvoiceDetailReferback = $this->paySupplierInvoiceDetailReferbackRepository->findWithoutFail($id);

        if (empty($paySupplierInvoiceDetailReferback)) {
            Flash::error('Pay Supplier Invoice Detail Referback not found');

            return redirect(route('paySupplierInvoiceDetailReferbacks.index'));
        }

        return view('pay_supplier_invoice_detail_referbacks.edit')->with('paySupplierInvoiceDetailReferback', $paySupplierInvoiceDetailReferback);
    }

    /**
     * Update the specified PaySupplierInvoiceDetailReferback in storage.
     *
     * @param  int              $id
     * @param UpdatePaySupplierInvoiceDetailReferbackRequest $request
     *
     * @return Response
     */
    public function update($id, UpdatePaySupplierInvoiceDetailReferbackRequest $request)
    {
        $paySupplierInvoiceDetailReferback = $this->paySupplierInvoiceDetailReferbackRepository->findWithoutFail($id);

        if (empty($paySupplierInvoiceDetailReferback)) {
            Flash::error('Pay Supplier Invoice Detail Referback not found');

            return redirect(route('paySupplierInvoiceDetailReferbacks.index'));
        }

        $paySupplierInvoiceDetailReferback = $this->paySupplierInvoiceDetailReferbackRepository->update($request->all(), $id);

        Flash::success('Pay Supplier Invoice Detail Referback updated successfully.');

        return redirect(route('paySupplierInvoiceDetailReferbacks.index'));
    }

    /**
     * Remove the specified PaySupplierInvoiceDetailReferback from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $paySupplierInvoiceDetailReferback = $this->paySupplierInvoiceDetailReferbackRepository->findWithoutFail($id);

        if (empty($paySupplierInvoiceDetailReferback)) {
            Flash::error('Pay Supplier Invoice Detail Referback not found');

            return redirect(route('paySupplierInvoiceDetailReferbacks.index'));
        }

        $this->paySupplierInvoiceDetailReferbackRepository->delete($id);

        Flash::success('Pay Supplier Invoice Detail Referback deleted successfully.');

        return redirect(route('paySupplierInvoiceDetailReferbacks.index'));
    }
}
