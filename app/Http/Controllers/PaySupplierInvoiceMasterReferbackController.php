<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatePaySupplierInvoiceMasterReferbackRequest;
use App\Http\Requests\UpdatePaySupplierInvoiceMasterReferbackRequest;
use App\Repositories\PaySupplierInvoiceMasterReferbackRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class PaySupplierInvoiceMasterReferbackController extends AppBaseController
{
    /** @var  PaySupplierInvoiceMasterReferbackRepository */
    private $paySupplierInvoiceMasterReferbackRepository;

    public function __construct(PaySupplierInvoiceMasterReferbackRepository $paySupplierInvoiceMasterReferbackRepo)
    {
        $this->paySupplierInvoiceMasterReferbackRepository = $paySupplierInvoiceMasterReferbackRepo;
    }

    /**
     * Display a listing of the PaySupplierInvoiceMasterReferback.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->paySupplierInvoiceMasterReferbackRepository->pushCriteria(new RequestCriteria($request));
        $paySupplierInvoiceMasterReferbacks = $this->paySupplierInvoiceMasterReferbackRepository->all();

        return view('pay_supplier_invoice_master_referbacks.index')
            ->with('paySupplierInvoiceMasterReferbacks', $paySupplierInvoiceMasterReferbacks);
    }

    /**
     * Show the form for creating a new PaySupplierInvoiceMasterReferback.
     *
     * @return Response
     */
    public function create()
    {
        return view('pay_supplier_invoice_master_referbacks.create');
    }

    /**
     * Store a newly created PaySupplierInvoiceMasterReferback in storage.
     *
     * @param CreatePaySupplierInvoiceMasterReferbackRequest $request
     *
     * @return Response
     */
    public function store(CreatePaySupplierInvoiceMasterReferbackRequest $request)
    {
        $input = $request->all();

        $paySupplierInvoiceMasterReferback = $this->paySupplierInvoiceMasterReferbackRepository->create($input);

        Flash::success('Pay Supplier Invoice Master Referback saved successfully.');

        return redirect(route('paySupplierInvoiceMasterReferbacks.index'));
    }

    /**
     * Display the specified PaySupplierInvoiceMasterReferback.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $paySupplierInvoiceMasterReferback = $this->paySupplierInvoiceMasterReferbackRepository->findWithoutFail($id);

        if (empty($paySupplierInvoiceMasterReferback)) {
            Flash::error('Pay Supplier Invoice Master Referback not found');

            return redirect(route('paySupplierInvoiceMasterReferbacks.index'));
        }

        return view('pay_supplier_invoice_master_referbacks.show')->with('paySupplierInvoiceMasterReferback', $paySupplierInvoiceMasterReferback);
    }

    /**
     * Show the form for editing the specified PaySupplierInvoiceMasterReferback.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $paySupplierInvoiceMasterReferback = $this->paySupplierInvoiceMasterReferbackRepository->findWithoutFail($id);

        if (empty($paySupplierInvoiceMasterReferback)) {
            Flash::error('Pay Supplier Invoice Master Referback not found');

            return redirect(route('paySupplierInvoiceMasterReferbacks.index'));
        }

        return view('pay_supplier_invoice_master_referbacks.edit')->with('paySupplierInvoiceMasterReferback', $paySupplierInvoiceMasterReferback);
    }

    /**
     * Update the specified PaySupplierInvoiceMasterReferback in storage.
     *
     * @param  int              $id
     * @param UpdatePaySupplierInvoiceMasterReferbackRequest $request
     *
     * @return Response
     */
    public function update($id, UpdatePaySupplierInvoiceMasterReferbackRequest $request)
    {
        $paySupplierInvoiceMasterReferback = $this->paySupplierInvoiceMasterReferbackRepository->findWithoutFail($id);

        if (empty($paySupplierInvoiceMasterReferback)) {
            Flash::error('Pay Supplier Invoice Master Referback not found');

            return redirect(route('paySupplierInvoiceMasterReferbacks.index'));
        }

        $paySupplierInvoiceMasterReferback = $this->paySupplierInvoiceMasterReferbackRepository->update($request->all(), $id);

        Flash::success('Pay Supplier Invoice Master Referback updated successfully.');

        return redirect(route('paySupplierInvoiceMasterReferbacks.index'));
    }

    /**
     * Remove the specified PaySupplierInvoiceMasterReferback from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $paySupplierInvoiceMasterReferback = $this->paySupplierInvoiceMasterReferbackRepository->findWithoutFail($id);

        if (empty($paySupplierInvoiceMasterReferback)) {
            Flash::error('Pay Supplier Invoice Master Referback not found');

            return redirect(route('paySupplierInvoiceMasterReferbacks.index'));
        }

        $this->paySupplierInvoiceMasterReferbackRepository->delete($id);

        Flash::success('Pay Supplier Invoice Master Referback deleted successfully.');

        return redirect(route('paySupplierInvoiceMasterReferbacks.index'));
    }
}
