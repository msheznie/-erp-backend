<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateCustomerInvoiceDirectRequest;
use App\Http\Requests\UpdateCustomerInvoiceDirectRequest;
use App\Repositories\CustomerInvoiceDirectRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class CustomerInvoiceDirectController extends AppBaseController
{
    /** @var  CustomerInvoiceDirectRepository */
    private $customerInvoiceDirectRepository;

    public function __construct(CustomerInvoiceDirectRepository $customerInvoiceDirectRepo)
    {
        $this->customerInvoiceDirectRepository = $customerInvoiceDirectRepo;
    }

    /**
     * Display a listing of the CustomerInvoiceDirect.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->customerInvoiceDirectRepository->pushCriteria(new RequestCriteria($request));
        $customerInvoiceDirects = $this->customerInvoiceDirectRepository->all();

        return view('customer_invoice_directs.index')
            ->with('customerInvoiceDirects', $customerInvoiceDirects);
    }

    /**
     * Show the form for creating a new CustomerInvoiceDirect.
     *
     * @return Response
     */
    public function create()
    {
        return view('customer_invoice_directs.create');
    }

    /**
     * Store a newly created CustomerInvoiceDirect in storage.
     *
     * @param CreateCustomerInvoiceDirectRequest $request
     *
     * @return Response
     */
    public function store(CreateCustomerInvoiceDirectRequest $request)
    {
        $input = $request->all();

        $customerInvoiceDirect = $this->customerInvoiceDirectRepository->create($input);

        Flash::success('Customer Invoice Direct saved successfully.');

        return redirect(route('customerInvoiceDirects.index'));
    }

    /**
     * Display the specified CustomerInvoiceDirect.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $customerInvoiceDirect = $this->customerInvoiceDirectRepository->findWithoutFail($id);

        if (empty($customerInvoiceDirect)) {
            Flash::error('Customer Invoice Direct not found');

            return redirect(route('customerInvoiceDirects.index'));
        }

        return view('customer_invoice_directs.show')->with('customerInvoiceDirect', $customerInvoiceDirect);
    }

    /**
     * Show the form for editing the specified CustomerInvoiceDirect.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $customerInvoiceDirect = $this->customerInvoiceDirectRepository->findWithoutFail($id);

        if (empty($customerInvoiceDirect)) {
            Flash::error('Customer Invoice Direct not found');

            return redirect(route('customerInvoiceDirects.index'));
        }

        return view('customer_invoice_directs.edit')->with('customerInvoiceDirect', $customerInvoiceDirect);
    }

    /**
     * Update the specified CustomerInvoiceDirect in storage.
     *
     * @param  int              $id
     * @param UpdateCustomerInvoiceDirectRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateCustomerInvoiceDirectRequest $request)
    {
        $customerInvoiceDirect = $this->customerInvoiceDirectRepository->findWithoutFail($id);

        if (empty($customerInvoiceDirect)) {
            Flash::error('Customer Invoice Direct not found');

            return redirect(route('customerInvoiceDirects.index'));
        }

        $customerInvoiceDirect = $this->customerInvoiceDirectRepository->update($request->all(), $id);

        Flash::success('Customer Invoice Direct updated successfully.');

        return redirect(route('customerInvoiceDirects.index'));
    }

    /**
     * Remove the specified CustomerInvoiceDirect from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $customerInvoiceDirect = $this->customerInvoiceDirectRepository->findWithoutFail($id);

        if (empty($customerInvoiceDirect)) {
            Flash::error('Customer Invoice Direct not found');

            return redirect(route('customerInvoiceDirects.index'));
        }

        $this->customerInvoiceDirectRepository->delete($id);

        Flash::success('Customer Invoice Direct deleted successfully.');

        return redirect(route('customerInvoiceDirects.index'));
    }
}
