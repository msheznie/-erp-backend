<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatePaymentBankTransferRequest;
use App\Http\Requests\UpdatePaymentBankTransferRequest;
use App\Repositories\PaymentBankTransferRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class PaymentBankTransferController extends AppBaseController
{
    /** @var  PaymentBankTransferRepository */
    private $paymentBankTransferRepository;

    public function __construct(PaymentBankTransferRepository $paymentBankTransferRepo)
    {
        $this->paymentBankTransferRepository = $paymentBankTransferRepo;
    }

    /**
     * Display a listing of the PaymentBankTransfer.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->paymentBankTransferRepository->pushCriteria(new RequestCriteria($request));
        $paymentBankTransfers = $this->paymentBankTransferRepository->all();

        return view('payment_bank_transfers.index')
            ->with('paymentBankTransfers', $paymentBankTransfers);
    }

    /**
     * Show the form for creating a new PaymentBankTransfer.
     *
     * @return Response
     */
    public function create()
    {
        return view('payment_bank_transfers.create');
    }

    /**
     * Store a newly created PaymentBankTransfer in storage.
     *
     * @param CreatePaymentBankTransferRequest $request
     *
     * @return Response
     */
    public function store(CreatePaymentBankTransferRequest $request)
    {
        $input = $request->all();

        $paymentBankTransfer = $this->paymentBankTransferRepository->create($input);

        Flash::success('Payment Bank Transfer saved successfully.');

        return redirect(route('paymentBankTransfers.index'));
    }

    /**
     * Display the specified PaymentBankTransfer.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $paymentBankTransfer = $this->paymentBankTransferRepository->findWithoutFail($id);

        if (empty($paymentBankTransfer)) {
            Flash::error('Payment Bank Transfer not found');

            return redirect(route('paymentBankTransfers.index'));
        }

        return view('payment_bank_transfers.show')->with('paymentBankTransfer', $paymentBankTransfer);
    }

    /**
     * Show the form for editing the specified PaymentBankTransfer.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $paymentBankTransfer = $this->paymentBankTransferRepository->findWithoutFail($id);

        if (empty($paymentBankTransfer)) {
            Flash::error('Payment Bank Transfer not found');

            return redirect(route('paymentBankTransfers.index'));
        }

        return view('payment_bank_transfers.edit')->with('paymentBankTransfer', $paymentBankTransfer);
    }

    /**
     * Update the specified PaymentBankTransfer in storage.
     *
     * @param  int              $id
     * @param UpdatePaymentBankTransferRequest $request
     *
     * @return Response
     */
    public function update($id, UpdatePaymentBankTransferRequest $request)
    {
        $paymentBankTransfer = $this->paymentBankTransferRepository->findWithoutFail($id);

        if (empty($paymentBankTransfer)) {
            Flash::error('Payment Bank Transfer not found');

            return redirect(route('paymentBankTransfers.index'));
        }

        $paymentBankTransfer = $this->paymentBankTransferRepository->update($request->all(), $id);

        Flash::success('Payment Bank Transfer updated successfully.');

        return redirect(route('paymentBankTransfers.index'));
    }

    /**
     * Remove the specified PaymentBankTransfer from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $paymentBankTransfer = $this->paymentBankTransferRepository->findWithoutFail($id);

        if (empty($paymentBankTransfer)) {
            Flash::error('Payment Bank Transfer not found');

            return redirect(route('paymentBankTransfers.index'));
        }

        $this->paymentBankTransferRepository->delete($id);

        Flash::success('Payment Bank Transfer deleted successfully.');

        return redirect(route('paymentBankTransfers.index'));
    }
}
