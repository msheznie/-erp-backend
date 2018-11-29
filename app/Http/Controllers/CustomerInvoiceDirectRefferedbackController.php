<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateCustomerInvoiceDirectRefferedbackRequest;
use App\Http\Requests\UpdateCustomerInvoiceDirectRefferedbackRequest;
use App\Repositories\CustomerInvoiceDirectRefferedbackRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class CustomerInvoiceDirectRefferedbackController extends AppBaseController
{
    /** @var  CustomerInvoiceDirectRefferedbackRepository */
    private $customerInvoiceDirectRefferedbackRepository;

    public function __construct(CustomerInvoiceDirectRefferedbackRepository $customerInvoiceDirectRefferedbackRepo)
    {
        $this->customerInvoiceDirectRefferedbackRepository = $customerInvoiceDirectRefferedbackRepo;
    }

    /**
     * Display a listing of the CustomerInvoiceDirectRefferedback.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->customerInvoiceDirectRefferedbackRepository->pushCriteria(new RequestCriteria($request));
        $customerInvoiceDirectRefferedbacks = $this->customerInvoiceDirectRefferedbackRepository->all();

        return view('customer_invoice_direct_refferedbacks.index')
            ->with('customerInvoiceDirectRefferedbacks', $customerInvoiceDirectRefferedbacks);
    }

    /**
     * Show the form for creating a new CustomerInvoiceDirectRefferedback.
     *
     * @return Response
     */
    public function create()
    {
        return view('customer_invoice_direct_refferedbacks.create');
    }

    /**
     * Store a newly created CustomerInvoiceDirectRefferedback in storage.
     *
     * @param CreateCustomerInvoiceDirectRefferedbackRequest $request
     *
     * @return Response
     */
    public function store(CreateCustomerInvoiceDirectRefferedbackRequest $request)
    {
        $input = $request->all();

        $customerInvoiceDirectRefferedback = $this->customerInvoiceDirectRefferedbackRepository->create($input);

        Flash::success('Customer Invoice Direct Refferedback saved successfully.');

        return redirect(route('customerInvoiceDirectRefferedbacks.index'));
    }

    /**
     * Display the specified CustomerInvoiceDirectRefferedback.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $customerInvoiceDirectRefferedback = $this->customerInvoiceDirectRefferedbackRepository->findWithoutFail($id);

        if (empty($customerInvoiceDirectRefferedback)) {
            Flash::error('Customer Invoice Direct Refferedback not found');

            return redirect(route('customerInvoiceDirectRefferedbacks.index'));
        }

        return view('customer_invoice_direct_refferedbacks.show')->with('customerInvoiceDirectRefferedback', $customerInvoiceDirectRefferedback);
    }

    /**
     * Show the form for editing the specified CustomerInvoiceDirectRefferedback.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $customerInvoiceDirectRefferedback = $this->customerInvoiceDirectRefferedbackRepository->findWithoutFail($id);

        if (empty($customerInvoiceDirectRefferedback)) {
            Flash::error('Customer Invoice Direct Refferedback not found');

            return redirect(route('customerInvoiceDirectRefferedbacks.index'));
        }

        return view('customer_invoice_direct_refferedbacks.edit')->with('customerInvoiceDirectRefferedback', $customerInvoiceDirectRefferedback);
    }

    /**
     * Update the specified CustomerInvoiceDirectRefferedback in storage.
     *
     * @param  int              $id
     * @param UpdateCustomerInvoiceDirectRefferedbackRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateCustomerInvoiceDirectRefferedbackRequest $request)
    {
        $customerInvoiceDirectRefferedback = $this->customerInvoiceDirectRefferedbackRepository->findWithoutFail($id);

        if (empty($customerInvoiceDirectRefferedback)) {
            Flash::error('Customer Invoice Direct Refferedback not found');

            return redirect(route('customerInvoiceDirectRefferedbacks.index'));
        }

        $customerInvoiceDirectRefferedback = $this->customerInvoiceDirectRefferedbackRepository->update($request->all(), $id);

        Flash::success('Customer Invoice Direct Refferedback updated successfully.');

        return redirect(route('customerInvoiceDirectRefferedbacks.index'));
    }

    /**
     * Remove the specified CustomerInvoiceDirectRefferedback from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $customerInvoiceDirectRefferedback = $this->customerInvoiceDirectRefferedbackRepository->findWithoutFail($id);

        if (empty($customerInvoiceDirectRefferedback)) {
            Flash::error('Customer Invoice Direct Refferedback not found');

            return redirect(route('customerInvoiceDirectRefferedbacks.index'));
        }

        $this->customerInvoiceDirectRefferedbackRepository->delete($id);

        Flash::success('Customer Invoice Direct Refferedback deleted successfully.');

        return redirect(route('customerInvoiceDirectRefferedbacks.index'));
    }
}
