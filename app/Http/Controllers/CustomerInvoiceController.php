<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateCustomerInvoiceRequest;
use App\Http\Requests\UpdateCustomerInvoiceRequest;
use App\Repositories\CustomerInvoiceRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class CustomerInvoiceController extends AppBaseController
{
    /** @var  CustomerInvoiceRepository */
    private $customerInvoiceRepository;

    public function __construct(CustomerInvoiceRepository $customerInvoiceRepo)
    {
        $this->customerInvoiceRepository = $customerInvoiceRepo;
    }

    /**
     * Display a listing of the CustomerInvoice.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->customerInvoiceRepository->pushCriteria(new RequestCriteria($request));
        $customerInvoices = $this->customerInvoiceRepository->all();

        return view('customer_invoices.index')
            ->with('customerInvoices', $customerInvoices);
    }

    /**
     * Show the form for creating a new CustomerInvoice.
     *
     * @return Response
     */
    public function create()
    {
        return view('customer_invoices.create');
    }

    /**
     * Store a newly created CustomerInvoice in storage.
     *
     * @param CreateCustomerInvoiceRequest $request
     *
     * @return Response
     */
    public function store(CreateCustomerInvoiceRequest $request)
    {
        $input = $request->all();

        $customerInvoice = $this->customerInvoiceRepository->create($input);

        Flash::success('Customer Invoice saved successfully.');

        return redirect(route('customerInvoices.index'));
    }

    /**
     * Display the specified CustomerInvoice.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $customerInvoice = $this->customerInvoiceRepository->findWithoutFail($id);

        if (empty($customerInvoice)) {
            Flash::error('Customer Invoice not found');

            return redirect(route('customerInvoices.index'));
        }

        return view('customer_invoices.show')->with('customerInvoice', $customerInvoice);
    }

    /**
     * Show the form for editing the specified CustomerInvoice.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $customerInvoice = $this->customerInvoiceRepository->findWithoutFail($id);

        if (empty($customerInvoice)) {
            Flash::error('Customer Invoice not found');

            return redirect(route('customerInvoices.index'));
        }

        return view('customer_invoices.edit')->with('customerInvoice', $customerInvoice);
    }

    /**
     * Update the specified CustomerInvoice in storage.
     *
     * @param  int              $id
     * @param UpdateCustomerInvoiceRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateCustomerInvoiceRequest $request)
    {
        $customerInvoice = $this->customerInvoiceRepository->findWithoutFail($id);

        if (empty($customerInvoice)) {
            Flash::error('Customer Invoice not found');

            return redirect(route('customerInvoices.index'));
        }

        $customerInvoice = $this->customerInvoiceRepository->update($request->all(), $id);

        Flash::success('Customer Invoice updated successfully.');

        return redirect(route('customerInvoices.index'));
    }

    /**
     * Remove the specified CustomerInvoice from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $customerInvoice = $this->customerInvoiceRepository->findWithoutFail($id);

        if (empty($customerInvoice)) {
            Flash::error('Customer Invoice not found');

            return redirect(route('customerInvoices.index'));
        }

        $this->customerInvoiceRepository->delete($id);

        Flash::success('Customer Invoice deleted successfully.');

        return redirect(route('customerInvoices.index'));
    }
}
