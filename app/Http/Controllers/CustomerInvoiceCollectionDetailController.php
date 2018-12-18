<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateCustomerInvoiceCollectionDetailRequest;
use App\Http\Requests\UpdateCustomerInvoiceCollectionDetailRequest;
use App\Repositories\CustomerInvoiceCollectionDetailRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class CustomerInvoiceCollectionDetailController extends AppBaseController
{
    /** @var  CustomerInvoiceCollectionDetailRepository */
    private $customerInvoiceCollectionDetailRepository;

    public function __construct(CustomerInvoiceCollectionDetailRepository $customerInvoiceCollectionDetailRepo)
    {
        $this->customerInvoiceCollectionDetailRepository = $customerInvoiceCollectionDetailRepo;
    }

    /**
     * Display a listing of the CustomerInvoiceCollectionDetail.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->customerInvoiceCollectionDetailRepository->pushCriteria(new RequestCriteria($request));
        $customerInvoiceCollectionDetails = $this->customerInvoiceCollectionDetailRepository->all();

        return view('customer_invoice_collection_details.index')
            ->with('customerInvoiceCollectionDetails', $customerInvoiceCollectionDetails);
    }

    /**
     * Show the form for creating a new CustomerInvoiceCollectionDetail.
     *
     * @return Response
     */
    public function create()
    {
        return view('customer_invoice_collection_details.create');
    }

    /**
     * Store a newly created CustomerInvoiceCollectionDetail in storage.
     *
     * @param CreateCustomerInvoiceCollectionDetailRequest $request
     *
     * @return Response
     */
    public function store(CreateCustomerInvoiceCollectionDetailRequest $request)
    {
        $input = $request->all();

        $customerInvoiceCollectionDetail = $this->customerInvoiceCollectionDetailRepository->create($input);

        Flash::success('Customer Invoice Collection Detail saved successfully.');

        return redirect(route('customerInvoiceCollectionDetails.index'));
    }

    /**
     * Display the specified CustomerInvoiceCollectionDetail.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $customerInvoiceCollectionDetail = $this->customerInvoiceCollectionDetailRepository->findWithoutFail($id);

        if (empty($customerInvoiceCollectionDetail)) {
            Flash::error('Customer Invoice Collection Detail not found');

            return redirect(route('customerInvoiceCollectionDetails.index'));
        }

        return view('customer_invoice_collection_details.show')->with('customerInvoiceCollectionDetail', $customerInvoiceCollectionDetail);
    }

    /**
     * Show the form for editing the specified CustomerInvoiceCollectionDetail.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $customerInvoiceCollectionDetail = $this->customerInvoiceCollectionDetailRepository->findWithoutFail($id);

        if (empty($customerInvoiceCollectionDetail)) {
            Flash::error('Customer Invoice Collection Detail not found');

            return redirect(route('customerInvoiceCollectionDetails.index'));
        }

        return view('customer_invoice_collection_details.edit')->with('customerInvoiceCollectionDetail', $customerInvoiceCollectionDetail);
    }

    /**
     * Update the specified CustomerInvoiceCollectionDetail in storage.
     *
     * @param  int              $id
     * @param UpdateCustomerInvoiceCollectionDetailRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateCustomerInvoiceCollectionDetailRequest $request)
    {
        $customerInvoiceCollectionDetail = $this->customerInvoiceCollectionDetailRepository->findWithoutFail($id);

        if (empty($customerInvoiceCollectionDetail)) {
            Flash::error('Customer Invoice Collection Detail not found');

            return redirect(route('customerInvoiceCollectionDetails.index'));
        }

        $customerInvoiceCollectionDetail = $this->customerInvoiceCollectionDetailRepository->update($request->all(), $id);

        Flash::success('Customer Invoice Collection Detail updated successfully.');

        return redirect(route('customerInvoiceCollectionDetails.index'));
    }

    /**
     * Remove the specified CustomerInvoiceCollectionDetail from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $customerInvoiceCollectionDetail = $this->customerInvoiceCollectionDetailRepository->findWithoutFail($id);

        if (empty($customerInvoiceCollectionDetail)) {
            Flash::error('Customer Invoice Collection Detail not found');

            return redirect(route('customerInvoiceCollectionDetails.index'));
        }

        $this->customerInvoiceCollectionDetailRepository->delete($id);

        Flash::success('Customer Invoice Collection Detail deleted successfully.');

        return redirect(route('customerInvoiceCollectionDetails.index'));
    }
}
