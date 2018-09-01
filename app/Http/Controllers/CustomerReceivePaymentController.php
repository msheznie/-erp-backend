<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateCustomerReceivePaymentRequest;
use App\Http\Requests\UpdateCustomerReceivePaymentRequest;
use App\Repositories\CustomerReceivePaymentRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class CustomerReceivePaymentController extends AppBaseController
{
    /** @var  CustomerReceivePaymentRepository */
    private $customerReceivePaymentRepository;

    public function __construct(CustomerReceivePaymentRepository $customerReceivePaymentRepo)
    {
        $this->customerReceivePaymentRepository = $customerReceivePaymentRepo;
    }

    /**
     * Display a listing of the CustomerReceivePayment.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->customerReceivePaymentRepository->pushCriteria(new RequestCriteria($request));
        $customerReceivePayments = $this->customerReceivePaymentRepository->all();

        return view('customer_receive_payments.index')
            ->with('customerReceivePayments', $customerReceivePayments);
    }

    /**
     * Show the form for creating a new CustomerReceivePayment.
     *
     * @return Response
     */
    public function create()
    {
        return view('customer_receive_payments.create');
    }

    /**
     * Store a newly created CustomerReceivePayment in storage.
     *
     * @param CreateCustomerReceivePaymentRequest $request
     *
     * @return Response
     */
    public function store(CreateCustomerReceivePaymentRequest $request)
    {
        $input = $request->all();

        $customerReceivePayment = $this->customerReceivePaymentRepository->create($input);

        Flash::success('Customer Receive Payment saved successfully.');

        return redirect(route('customerReceivePayments.index'));
    }

    /**
     * Display the specified CustomerReceivePayment.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $customerReceivePayment = $this->customerReceivePaymentRepository->findWithoutFail($id);

        if (empty($customerReceivePayment)) {
            Flash::error('Customer Receive Payment not found');

            return redirect(route('customerReceivePayments.index'));
        }

        return view('customer_receive_payments.show')->with('customerReceivePayment', $customerReceivePayment);
    }

    /**
     * Show the form for editing the specified CustomerReceivePayment.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $customerReceivePayment = $this->customerReceivePaymentRepository->findWithoutFail($id);

        if (empty($customerReceivePayment)) {
            Flash::error('Customer Receive Payment not found');

            return redirect(route('customerReceivePayments.index'));
        }

        return view('customer_receive_payments.edit')->with('customerReceivePayment', $customerReceivePayment);
    }

    /**
     * Update the specified CustomerReceivePayment in storage.
     *
     * @param  int              $id
     * @param UpdateCustomerReceivePaymentRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateCustomerReceivePaymentRequest $request)
    {
        $customerReceivePayment = $this->customerReceivePaymentRepository->findWithoutFail($id);

        if (empty($customerReceivePayment)) {
            Flash::error('Customer Receive Payment not found');

            return redirect(route('customerReceivePayments.index'));
        }

        $customerReceivePayment = $this->customerReceivePaymentRepository->update($request->all(), $id);

        Flash::success('Customer Receive Payment updated successfully.');

        return redirect(route('customerReceivePayments.index'));
    }

    /**
     * Remove the specified CustomerReceivePayment from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $customerReceivePayment = $this->customerReceivePaymentRepository->findWithoutFail($id);

        if (empty($customerReceivePayment)) {
            Flash::error('Customer Receive Payment not found');

            return redirect(route('customerReceivePayments.index'));
        }

        $this->customerReceivePaymentRepository->delete($id);

        Flash::success('Customer Receive Payment deleted successfully.');

        return redirect(route('customerReceivePayments.index'));
    }
}
