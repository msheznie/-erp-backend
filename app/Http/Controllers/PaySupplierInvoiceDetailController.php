<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatePaySupplierInvoiceDetailRequest;
use App\Http\Requests\UpdatePaySupplierInvoiceDetailRequest;
use App\Repositories\PaySupplierInvoiceDetailRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class PaySupplierInvoiceDetailController extends AppBaseController
{
    /** @var  PaySupplierInvoiceDetailRepository */
    private $paySupplierInvoiceDetailRepository;

    public function __construct(PaySupplierInvoiceDetailRepository $paySupplierInvoiceDetailRepo)
    {
        $this->paySupplierInvoiceDetailRepository = $paySupplierInvoiceDetailRepo;
    }

    /**
     * Display a listing of the PaySupplierInvoiceDetail.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->paySupplierInvoiceDetailRepository->pushCriteria(new RequestCriteria($request));
        $paySupplierInvoiceDetails = $this->paySupplierInvoiceDetailRepository->all();

        return view('pay_supplier_invoice_details.index')
            ->with('paySupplierInvoiceDetails', $paySupplierInvoiceDetails);
    }

    /**
     * Show the form for creating a new PaySupplierInvoiceDetail.
     *
     * @return Response
     */
    public function create()
    {
        return view('pay_supplier_invoice_details.create');
    }

    /**
     * Store a newly created PaySupplierInvoiceDetail in storage.
     *
     * @param CreatePaySupplierInvoiceDetailRequest $request
     *
     * @return Response
     */
    public function store(CreatePaySupplierInvoiceDetailRequest $request)
    {
        $input = $request->all();

        $paySupplierInvoiceDetail = $this->paySupplierInvoiceDetailRepository->create($input);

        Flash::success('Pay Supplier Invoice Detail saved successfully.');

        return redirect(route('paySupplierInvoiceDetails.index'));
    }

    /**
     * Display the specified PaySupplierInvoiceDetail.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $paySupplierInvoiceDetail = $this->paySupplierInvoiceDetailRepository->findWithoutFail($id);

        if (empty($paySupplierInvoiceDetail)) {
            Flash::error('Pay Supplier Invoice Detail not found');

            return redirect(route('paySupplierInvoiceDetails.index'));
        }

        return view('pay_supplier_invoice_details.show')->with('paySupplierInvoiceDetail', $paySupplierInvoiceDetail);
    }

    /**
     * Show the form for editing the specified PaySupplierInvoiceDetail.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $paySupplierInvoiceDetail = $this->paySupplierInvoiceDetailRepository->findWithoutFail($id);

        if (empty($paySupplierInvoiceDetail)) {
            Flash::error('Pay Supplier Invoice Detail not found');

            return redirect(route('paySupplierInvoiceDetails.index'));
        }

        return view('pay_supplier_invoice_details.edit')->with('paySupplierInvoiceDetail', $paySupplierInvoiceDetail);
    }

    /**
     * Update the specified PaySupplierInvoiceDetail in storage.
     *
     * @param  int              $id
     * @param UpdatePaySupplierInvoiceDetailRequest $request
     *
     * @return Response
     */
    public function update($id, UpdatePaySupplierInvoiceDetailRequest $request)
    {
        $paySupplierInvoiceDetail = $this->paySupplierInvoiceDetailRepository->findWithoutFail($id);

        if (empty($paySupplierInvoiceDetail)) {
            Flash::error('Pay Supplier Invoice Detail not found');

            return redirect(route('paySupplierInvoiceDetails.index'));
        }

        $paySupplierInvoiceDetail = $this->paySupplierInvoiceDetailRepository->update($request->all(), $id);

        Flash::success('Pay Supplier Invoice Detail updated successfully.');

        return redirect(route('paySupplierInvoiceDetails.index'));
    }

    /**
     * Remove the specified PaySupplierInvoiceDetail from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $paySupplierInvoiceDetail = $this->paySupplierInvoiceDetailRepository->findWithoutFail($id);

        if (empty($paySupplierInvoiceDetail)) {
            Flash::error('Pay Supplier Invoice Detail not found');

            return redirect(route('paySupplierInvoiceDetails.index'));
        }

        $this->paySupplierInvoiceDetailRepository->delete($id);

        Flash::success('Pay Supplier Invoice Detail deleted successfully.');

        return redirect(route('paySupplierInvoiceDetails.index'));
    }
}
