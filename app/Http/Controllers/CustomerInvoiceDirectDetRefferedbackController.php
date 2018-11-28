<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateCustomerInvoiceDirectDetRefferedbackRequest;
use App\Http\Requests\UpdateCustomerInvoiceDirectDetRefferedbackRequest;
use App\Repositories\CustomerInvoiceDirectDetRefferedbackRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class CustomerInvoiceDirectDetRefferedbackController extends AppBaseController
{
    /** @var  CustomerInvoiceDirectDetRefferedbackRepository */
    private $customerInvoiceDirectDetRefferedbackRepository;

    public function __construct(CustomerInvoiceDirectDetRefferedbackRepository $customerInvoiceDirectDetRefferedbackRepo)
    {
        $this->customerInvoiceDirectDetRefferedbackRepository = $customerInvoiceDirectDetRefferedbackRepo;
    }

    /**
     * Display a listing of the CustomerInvoiceDirectDetRefferedback.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->customerInvoiceDirectDetRefferedbackRepository->pushCriteria(new RequestCriteria($request));
        $customerInvoiceDirectDetRefferedbacks = $this->customerInvoiceDirectDetRefferedbackRepository->all();

        return view('customer_invoice_direct_det_refferedbacks.index')
            ->with('customerInvoiceDirectDetRefferedbacks', $customerInvoiceDirectDetRefferedbacks);
    }

    /**
     * Show the form for creating a new CustomerInvoiceDirectDetRefferedback.
     *
     * @return Response
     */
    public function create()
    {
        return view('customer_invoice_direct_det_refferedbacks.create');
    }

    /**
     * Store a newly created CustomerInvoiceDirectDetRefferedback in storage.
     *
     * @param CreateCustomerInvoiceDirectDetRefferedbackRequest $request
     *
     * @return Response
     */
    public function store(CreateCustomerInvoiceDirectDetRefferedbackRequest $request)
    {
        $input = $request->all();

        $customerInvoiceDirectDetRefferedback = $this->customerInvoiceDirectDetRefferedbackRepository->create($input);

        Flash::success('Customer Invoice Direct Det Refferedback saved successfully.');

        return redirect(route('customerInvoiceDirectDetRefferedbacks.index'));
    }

    /**
     * Display the specified CustomerInvoiceDirectDetRefferedback.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $customerInvoiceDirectDetRefferedback = $this->customerInvoiceDirectDetRefferedbackRepository->findWithoutFail($id);

        if (empty($customerInvoiceDirectDetRefferedback)) {
            Flash::error('Customer Invoice Direct Det Refferedback not found');

            return redirect(route('customerInvoiceDirectDetRefferedbacks.index'));
        }

        return view('customer_invoice_direct_det_refferedbacks.show')->with('customerInvoiceDirectDetRefferedback', $customerInvoiceDirectDetRefferedback);
    }

    /**
     * Show the form for editing the specified CustomerInvoiceDirectDetRefferedback.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $customerInvoiceDirectDetRefferedback = $this->customerInvoiceDirectDetRefferedbackRepository->findWithoutFail($id);

        if (empty($customerInvoiceDirectDetRefferedback)) {
            Flash::error('Customer Invoice Direct Det Refferedback not found');

            return redirect(route('customerInvoiceDirectDetRefferedbacks.index'));
        }

        return view('customer_invoice_direct_det_refferedbacks.edit')->with('customerInvoiceDirectDetRefferedback', $customerInvoiceDirectDetRefferedback);
    }

    /**
     * Update the specified CustomerInvoiceDirectDetRefferedback in storage.
     *
     * @param  int              $id
     * @param UpdateCustomerInvoiceDirectDetRefferedbackRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateCustomerInvoiceDirectDetRefferedbackRequest $request)
    {
        $customerInvoiceDirectDetRefferedback = $this->customerInvoiceDirectDetRefferedbackRepository->findWithoutFail($id);

        if (empty($customerInvoiceDirectDetRefferedback)) {
            Flash::error('Customer Invoice Direct Det Refferedback not found');

            return redirect(route('customerInvoiceDirectDetRefferedbacks.index'));
        }

        $customerInvoiceDirectDetRefferedback = $this->customerInvoiceDirectDetRefferedbackRepository->update($request->all(), $id);

        Flash::success('Customer Invoice Direct Det Refferedback updated successfully.');

        return redirect(route('customerInvoiceDirectDetRefferedbacks.index'));
    }

    /**
     * Remove the specified CustomerInvoiceDirectDetRefferedback from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $customerInvoiceDirectDetRefferedback = $this->customerInvoiceDirectDetRefferedbackRepository->findWithoutFail($id);

        if (empty($customerInvoiceDirectDetRefferedback)) {
            Flash::error('Customer Invoice Direct Det Refferedback not found');

            return redirect(route('customerInvoiceDirectDetRefferedbacks.index'));
        }

        $this->customerInvoiceDirectDetRefferedbackRepository->delete($id);

        Flash::success('Customer Invoice Direct Det Refferedback deleted successfully.');

        return redirect(route('customerInvoiceDirectDetRefferedbacks.index'));
    }
}
