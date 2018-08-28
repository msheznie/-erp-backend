<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateCustomerReceivePaymentDetailRequest;
use App\Http\Requests\UpdateCustomerReceivePaymentDetailRequest;
use App\Repositories\CustomerReceivePaymentDetailRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class CustomerReceivePaymentDetailController extends AppBaseController
{
    /** @var  CustomerReceivePaymentDetailRepository */
    private $customerReceivePaymentDetailRepository;

    public function __construct(CustomerReceivePaymentDetailRepository $customerReceivePaymentDetailRepo)
    {
        $this->customerReceivePaymentDetailRepository = $customerReceivePaymentDetailRepo;
    }

    /**
     * Display a listing of the CustomerReceivePaymentDetail.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->customerReceivePaymentDetailRepository->pushCriteria(new RequestCriteria($request));
        $customerReceivePaymentDetails = $this->customerReceivePaymentDetailRepository->all();

        return view('customer_receive_payment_details.index')
            ->with('customerReceivePaymentDetails', $customerReceivePaymentDetails);
    }

    /**
     * Show the form for creating a new CustomerReceivePaymentDetail.
     *
     * @return Response
     */
    public function create()
    {
        return view('customer_receive_payment_details.create');
    }

    /**
     * Store a newly created CustomerReceivePaymentDetail in storage.
     *
     * @param CreateCustomerReceivePaymentDetailRequest $request
     *
     * @return Response
     */
    public function store(CreateCustomerReceivePaymentDetailRequest $request)
    {
        $input = $request->all();

        $customerReceivePaymentDetail = $this->customerReceivePaymentDetailRepository->create($input);

        Flash::success('Customer Receive Payment Detail saved successfully.');

        return redirect(route('customerReceivePaymentDetails.index'));
    }

    /**
     * Display the specified CustomerReceivePaymentDetail.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $customerReceivePaymentDetail = $this->customerReceivePaymentDetailRepository->findWithoutFail($id);

        if (empty($customerReceivePaymentDetail)) {
            Flash::error('Customer Receive Payment Detail not found');

            return redirect(route('customerReceivePaymentDetails.index'));
        }

        return view('customer_receive_payment_details.show')->with('customerReceivePaymentDetail', $customerReceivePaymentDetail);
    }

    /**
     * Show the form for editing the specified CustomerReceivePaymentDetail.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $customerReceivePaymentDetail = $this->customerReceivePaymentDetailRepository->findWithoutFail($id);

        if (empty($customerReceivePaymentDetail)) {
            Flash::error('Customer Receive Payment Detail not found');

            return redirect(route('customerReceivePaymentDetails.index'));
        }

        return view('customer_receive_payment_details.edit')->with('customerReceivePaymentDetail', $customerReceivePaymentDetail);
    }

    /**
     * Update the specified CustomerReceivePaymentDetail in storage.
     *
     * @param  int              $id
     * @param UpdateCustomerReceivePaymentDetailRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateCustomerReceivePaymentDetailRequest $request)
    {
        $customerReceivePaymentDetail = $this->customerReceivePaymentDetailRepository->findWithoutFail($id);

        if (empty($customerReceivePaymentDetail)) {
            Flash::error('Customer Receive Payment Detail not found');

            return redirect(route('customerReceivePaymentDetails.index'));
        }

        $customerReceivePaymentDetail = $this->customerReceivePaymentDetailRepository->update($request->all(), $id);

        Flash::success('Customer Receive Payment Detail updated successfully.');

        return redirect(route('customerReceivePaymentDetails.index'));
    }

    /**
     * Remove the specified CustomerReceivePaymentDetail from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $customerReceivePaymentDetail = $this->customerReceivePaymentDetailRepository->findWithoutFail($id);

        if (empty($customerReceivePaymentDetail)) {
            Flash::error('Customer Receive Payment Detail not found');

            return redirect(route('customerReceivePaymentDetails.index'));
        }

        $this->customerReceivePaymentDetailRepository->delete($id);

        Flash::success('Customer Receive Payment Detail deleted successfully.');

        return redirect(route('customerReceivePaymentDetails.index'));
    }
}
