<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatePaymentBankTransferRefferedBackRequest;
use App\Http\Requests\UpdatePaymentBankTransferRefferedBackRequest;
use App\Repositories\PaymentBankTransferRefferedBackRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class PaymentBankTransferRefferedBackController extends AppBaseController
{
    /** @var  PaymentBankTransferRefferedBackRepository */
    private $paymentBankTransferRefferedBackRepository;

    public function __construct(PaymentBankTransferRefferedBackRepository $paymentBankTransferRefferedBackRepo)
    {
        $this->paymentBankTransferRefferedBackRepository = $paymentBankTransferRefferedBackRepo;
    }

    /**
     * Display a listing of the PaymentBankTransferRefferedBack.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->paymentBankTransferRefferedBackRepository->pushCriteria(new RequestCriteria($request));
        $paymentBankTransferRefferedBacks = $this->paymentBankTransferRefferedBackRepository->all();

        return view('payment_bank_transfer_reffered_backs.index')
            ->with('paymentBankTransferRefferedBacks', $paymentBankTransferRefferedBacks);
    }

    /**
     * Show the form for creating a new PaymentBankTransferRefferedBack.
     *
     * @return Response
     */
    public function create()
    {
        return view('payment_bank_transfer_reffered_backs.create');
    }

    /**
     * Store a newly created PaymentBankTransferRefferedBack in storage.
     *
     * @param CreatePaymentBankTransferRefferedBackRequest $request
     *
     * @return Response
     */
    public function store(CreatePaymentBankTransferRefferedBackRequest $request)
    {
        $input = $request->all();

        $paymentBankTransferRefferedBack = $this->paymentBankTransferRefferedBackRepository->create($input);

        Flash::success('Payment Bank Transfer Reffered Back saved successfully.');

        return redirect(route('paymentBankTransferRefferedBacks.index'));
    }

    /**
     * Display the specified PaymentBankTransferRefferedBack.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $paymentBankTransferRefferedBack = $this->paymentBankTransferRefferedBackRepository->findWithoutFail($id);

        if (empty($paymentBankTransferRefferedBack)) {
            Flash::error('Payment Bank Transfer Reffered Back not found');

            return redirect(route('paymentBankTransferRefferedBacks.index'));
        }

        return view('payment_bank_transfer_reffered_backs.show')->with('paymentBankTransferRefferedBack', $paymentBankTransferRefferedBack);
    }

    /**
     * Show the form for editing the specified PaymentBankTransferRefferedBack.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $paymentBankTransferRefferedBack = $this->paymentBankTransferRefferedBackRepository->findWithoutFail($id);

        if (empty($paymentBankTransferRefferedBack)) {
            Flash::error('Payment Bank Transfer Reffered Back not found');

            return redirect(route('paymentBankTransferRefferedBacks.index'));
        }

        return view('payment_bank_transfer_reffered_backs.edit')->with('paymentBankTransferRefferedBack', $paymentBankTransferRefferedBack);
    }

    /**
     * Update the specified PaymentBankTransferRefferedBack in storage.
     *
     * @param  int              $id
     * @param UpdatePaymentBankTransferRefferedBackRequest $request
     *
     * @return Response
     */
    public function update($id, UpdatePaymentBankTransferRefferedBackRequest $request)
    {
        $paymentBankTransferRefferedBack = $this->paymentBankTransferRefferedBackRepository->findWithoutFail($id);

        if (empty($paymentBankTransferRefferedBack)) {
            Flash::error('Payment Bank Transfer Reffered Back not found');

            return redirect(route('paymentBankTransferRefferedBacks.index'));
        }

        $paymentBankTransferRefferedBack = $this->paymentBankTransferRefferedBackRepository->update($request->all(), $id);

        Flash::success('Payment Bank Transfer Reffered Back updated successfully.');

        return redirect(route('paymentBankTransferRefferedBacks.index'));
    }

    /**
     * Remove the specified PaymentBankTransferRefferedBack from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $paymentBankTransferRefferedBack = $this->paymentBankTransferRefferedBackRepository->findWithoutFail($id);

        if (empty($paymentBankTransferRefferedBack)) {
            Flash::error('Payment Bank Transfer Reffered Back not found');

            return redirect(route('paymentBankTransferRefferedBacks.index'));
        }

        $this->paymentBankTransferRefferedBackRepository->delete($id);

        Flash::success('Payment Bank Transfer Reffered Back deleted successfully.');

        return redirect(route('paymentBankTransferRefferedBacks.index'));
    }
}
