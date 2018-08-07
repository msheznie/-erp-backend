<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateCustomerInvoiceDirectDetailRequest;
use App\Http\Requests\UpdateCustomerInvoiceDirectDetailRequest;
use App\Repositories\CustomerInvoiceDirectDetailRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class CustomerInvoiceDirectDetailController extends AppBaseController
{
    /** @var  CustomerInvoiceDirectDetailRepository */
    private $customerInvoiceDirectDetailRepository;

    public function __construct(CustomerInvoiceDirectDetailRepository $customerInvoiceDirectDetailRepo)
    {
        $this->customerInvoiceDirectDetailRepository = $customerInvoiceDirectDetailRepo;
    }

    /**
     * Display a listing of the CustomerInvoiceDirectDetail.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->customerInvoiceDirectDetailRepository->pushCriteria(new RequestCriteria($request));
        $customerInvoiceDirectDetails = $this->customerInvoiceDirectDetailRepository->all();

        return view('customer_invoice_direct_details.index')
            ->with('customerInvoiceDirectDetails', $customerInvoiceDirectDetails);
    }

    /**
     * Show the form for creating a new CustomerInvoiceDirectDetail.
     *
     * @return Response
     */
    public function create()
    {
        return view('customer_invoice_direct_details.create');
    }

    /**
     * Store a newly created CustomerInvoiceDirectDetail in storage.
     *
     * @param CreateCustomerInvoiceDirectDetailRequest $request
     *
     * @return Response
     */
    public function store(CreateCustomerInvoiceDirectDetailRequest $request)
    {
        $input = $request->all();

        $customerInvoiceDirectDetail = $this->customerInvoiceDirectDetailRepository->create($input);

        Flash::success('Customer Invoice Direct Detail saved successfully.');

        return redirect(route('customerInvoiceDirectDetails.index'));
    }

    /**
     * Display the specified CustomerInvoiceDirectDetail.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $customerInvoiceDirectDetail = $this->customerInvoiceDirectDetailRepository->findWithoutFail($id);

        if (empty($customerInvoiceDirectDetail)) {
            Flash::error('Customer Invoice Direct Detail not found');

            return redirect(route('customerInvoiceDirectDetails.index'));
        }

        return view('customer_invoice_direct_details.show')->with('customerInvoiceDirectDetail', $customerInvoiceDirectDetail);
    }

    /**
     * Show the form for editing the specified CustomerInvoiceDirectDetail.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $customerInvoiceDirectDetail = $this->customerInvoiceDirectDetailRepository->findWithoutFail($id);

        if (empty($customerInvoiceDirectDetail)) {
            Flash::error('Customer Invoice Direct Detail not found');

            return redirect(route('customerInvoiceDirectDetails.index'));
        }

        return view('customer_invoice_direct_details.edit')->with('customerInvoiceDirectDetail', $customerInvoiceDirectDetail);
    }

    /**
     * Update the specified CustomerInvoiceDirectDetail in storage.
     *
     * @param  int              $id
     * @param UpdateCustomerInvoiceDirectDetailRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateCustomerInvoiceDirectDetailRequest $request)
    {
        $customerInvoiceDirectDetail = $this->customerInvoiceDirectDetailRepository->findWithoutFail($id);

        if (empty($customerInvoiceDirectDetail)) {
            Flash::error('Customer Invoice Direct Detail not found');

            return redirect(route('customerInvoiceDirectDetails.index'));
        }

        $customerInvoiceDirectDetail = $this->customerInvoiceDirectDetailRepository->update($request->all(), $id);

        Flash::success('Customer Invoice Direct Detail updated successfully.');

        return redirect(route('customerInvoiceDirectDetails.index'));
    }

    /**
     * Remove the specified CustomerInvoiceDirectDetail from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $customerInvoiceDirectDetail = $this->customerInvoiceDirectDetailRepository->findWithoutFail($id);

        if (empty($customerInvoiceDirectDetail)) {
            Flash::error('Customer Invoice Direct Detail not found');

            return redirect(route('customerInvoiceDirectDetails.index'));
        }

        $this->customerInvoiceDirectDetailRepository->delete($id);

        Flash::success('Customer Invoice Direct Detail deleted successfully.');

        return redirect(route('customerInvoiceDirectDetails.index'));
    }
}
