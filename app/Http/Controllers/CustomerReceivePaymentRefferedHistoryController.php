<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateCustomerReceivePaymentRefferedHistoryRequest;
use App\Http\Requests\UpdateCustomerReceivePaymentRefferedHistoryRequest;
use App\Repositories\CustomerReceivePaymentRefferedHistoryRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class CustomerReceivePaymentRefferedHistoryController extends AppBaseController
{
    /** @var  CustomerReceivePaymentRefferedHistoryRepository */
    private $customerReceivePaymentRefferedHistoryRepository;

    public function __construct(CustomerReceivePaymentRefferedHistoryRepository $customerReceivePaymentRefferedHistoryRepo)
    {
        $this->customerReceivePaymentRefferedHistoryRepository = $customerReceivePaymentRefferedHistoryRepo;
    }

    /**
     * Display a listing of the CustomerReceivePaymentRefferedHistory.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->customerReceivePaymentRefferedHistoryRepository->pushCriteria(new RequestCriteria($request));
        $customerReceivePaymentRefferedHistories = $this->customerReceivePaymentRefferedHistoryRepository->all();

        return view('customer_receive_payment_reffered_histories.index')
            ->with('customerReceivePaymentRefferedHistories', $customerReceivePaymentRefferedHistories);
    }

    /**
     * Show the form for creating a new CustomerReceivePaymentRefferedHistory.
     *
     * @return Response
     */
    public function create()
    {
        return view('customer_receive_payment_reffered_histories.create');
    }

    /**
     * Store a newly created CustomerReceivePaymentRefferedHistory in storage.
     *
     * @param CreateCustomerReceivePaymentRefferedHistoryRequest $request
     *
     * @return Response
     */
    public function store(CreateCustomerReceivePaymentRefferedHistoryRequest $request)
    {
        $input = $request->all();

        $customerReceivePaymentRefferedHistory = $this->customerReceivePaymentRefferedHistoryRepository->create($input);

        Flash::success('Customer Receive Payment Reffered History saved successfully.');

        return redirect(route('customerReceivePaymentRefferedHistories.index'));
    }

    /**
     * Display the specified CustomerReceivePaymentRefferedHistory.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $customerReceivePaymentRefferedHistory = $this->customerReceivePaymentRefferedHistoryRepository->findWithoutFail($id);

        if (empty($customerReceivePaymentRefferedHistory)) {
            Flash::error('Customer Receive Payment Reffered History not found');

            return redirect(route('customerReceivePaymentRefferedHistories.index'));
        }

        return view('customer_receive_payment_reffered_histories.show')->with('customerReceivePaymentRefferedHistory', $customerReceivePaymentRefferedHistory);
    }

    /**
     * Show the form for editing the specified CustomerReceivePaymentRefferedHistory.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $customerReceivePaymentRefferedHistory = $this->customerReceivePaymentRefferedHistoryRepository->findWithoutFail($id);

        if (empty($customerReceivePaymentRefferedHistory)) {
            Flash::error('Customer Receive Payment Reffered History not found');

            return redirect(route('customerReceivePaymentRefferedHistories.index'));
        }

        return view('customer_receive_payment_reffered_histories.edit')->with('customerReceivePaymentRefferedHistory', $customerReceivePaymentRefferedHistory);
    }

    /**
     * Update the specified CustomerReceivePaymentRefferedHistory in storage.
     *
     * @param  int              $id
     * @param UpdateCustomerReceivePaymentRefferedHistoryRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateCustomerReceivePaymentRefferedHistoryRequest $request)
    {
        $customerReceivePaymentRefferedHistory = $this->customerReceivePaymentRefferedHistoryRepository->findWithoutFail($id);

        if (empty($customerReceivePaymentRefferedHistory)) {
            Flash::error('Customer Receive Payment Reffered History not found');

            return redirect(route('customerReceivePaymentRefferedHistories.index'));
        }

        $customerReceivePaymentRefferedHistory = $this->customerReceivePaymentRefferedHistoryRepository->update($request->all(), $id);

        Flash::success('Customer Receive Payment Reffered History updated successfully.');

        return redirect(route('customerReceivePaymentRefferedHistories.index'));
    }

    /**
     * Remove the specified CustomerReceivePaymentRefferedHistory from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $customerReceivePaymentRefferedHistory = $this->customerReceivePaymentRefferedHistoryRepository->findWithoutFail($id);

        if (empty($customerReceivePaymentRefferedHistory)) {
            Flash::error('Customer Receive Payment Reffered History not found');

            return redirect(route('customerReceivePaymentRefferedHistories.index'));
        }

        $this->customerReceivePaymentRefferedHistoryRepository->delete($id);

        Flash::success('Customer Receive Payment Reffered History deleted successfully.');

        return redirect(route('customerReceivePaymentRefferedHistories.index'));
    }
}
