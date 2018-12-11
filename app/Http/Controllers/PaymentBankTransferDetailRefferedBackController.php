<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatePaymentBankTransferDetailRefferedBackRequest;
use App\Http\Requests\UpdatePaymentBankTransferDetailRefferedBackRequest;
use App\Repositories\PaymentBankTransferDetailRefferedBackRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class PaymentBankTransferDetailRefferedBackController extends AppBaseController
{
    /** @var  PaymentBankTransferDetailRefferedBackRepository */
    private $paymentBankTransferDetailRefferedBackRepository;

    public function __construct(PaymentBankTransferDetailRefferedBackRepository $paymentBankTransferDetailRefferedBackRepo)
    {
        $this->paymentBankTransferDetailRefferedBackRepository = $paymentBankTransferDetailRefferedBackRepo;
    }

    /**
     * Display a listing of the PaymentBankTransferDetailRefferedBack.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->paymentBankTransferDetailRefferedBackRepository->pushCriteria(new RequestCriteria($request));
        $paymentBankTransferDetailRefferedBacks = $this->paymentBankTransferDetailRefferedBackRepository->all();

        return view('payment_bank_transfer_detail_reffered_backs.index')
            ->with('paymentBankTransferDetailRefferedBacks', $paymentBankTransferDetailRefferedBacks);
    }

    /**
     * Show the form for creating a new PaymentBankTransferDetailRefferedBack.
     *
     * @return Response
     */
    public function create()
    {
        return view('payment_bank_transfer_detail_reffered_backs.create');
    }

    /**
     * Store a newly created PaymentBankTransferDetailRefferedBack in storage.
     *
     * @param CreatePaymentBankTransferDetailRefferedBackRequest $request
     *
     * @return Response
     */
    public function store(CreatePaymentBankTransferDetailRefferedBackRequest $request)
    {
        $input = $request->all();

        $paymentBankTransferDetailRefferedBack = $this->paymentBankTransferDetailRefferedBackRepository->create($input);

        Flash::success('Payment Bank Transfer Detail Reffered Back saved successfully.');

        return redirect(route('paymentBankTransferDetailRefferedBacks.index'));
    }

    /**
     * Display the specified PaymentBankTransferDetailRefferedBack.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $paymentBankTransferDetailRefferedBack = $this->paymentBankTransferDetailRefferedBackRepository->findWithoutFail($id);

        if (empty($paymentBankTransferDetailRefferedBack)) {
            Flash::error('Payment Bank Transfer Detail Reffered Back not found');

            return redirect(route('paymentBankTransferDetailRefferedBacks.index'));
        }

        return view('payment_bank_transfer_detail_reffered_backs.show')->with('paymentBankTransferDetailRefferedBack', $paymentBankTransferDetailRefferedBack);
    }

    /**
     * Show the form for editing the specified PaymentBankTransferDetailRefferedBack.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $paymentBankTransferDetailRefferedBack = $this->paymentBankTransferDetailRefferedBackRepository->findWithoutFail($id);

        if (empty($paymentBankTransferDetailRefferedBack)) {
            Flash::error('Payment Bank Transfer Detail Reffered Back not found');

            return redirect(route('paymentBankTransferDetailRefferedBacks.index'));
        }

        return view('payment_bank_transfer_detail_reffered_backs.edit')->with('paymentBankTransferDetailRefferedBack', $paymentBankTransferDetailRefferedBack);
    }

    /**
     * Update the specified PaymentBankTransferDetailRefferedBack in storage.
     *
     * @param  int              $id
     * @param UpdatePaymentBankTransferDetailRefferedBackRequest $request
     *
     * @return Response
     */
    public function update($id, UpdatePaymentBankTransferDetailRefferedBackRequest $request)
    {
        $paymentBankTransferDetailRefferedBack = $this->paymentBankTransferDetailRefferedBackRepository->findWithoutFail($id);

        if (empty($paymentBankTransferDetailRefferedBack)) {
            Flash::error('Payment Bank Transfer Detail Reffered Back not found');

            return redirect(route('paymentBankTransferDetailRefferedBacks.index'));
        }

        $paymentBankTransferDetailRefferedBack = $this->paymentBankTransferDetailRefferedBackRepository->update($request->all(), $id);

        Flash::success('Payment Bank Transfer Detail Reffered Back updated successfully.');

        return redirect(route('paymentBankTransferDetailRefferedBacks.index'));
    }

    /**
     * Remove the specified PaymentBankTransferDetailRefferedBack from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $paymentBankTransferDetailRefferedBack = $this->paymentBankTransferDetailRefferedBackRepository->findWithoutFail($id);

        if (empty($paymentBankTransferDetailRefferedBack)) {
            Flash::error('Payment Bank Transfer Detail Reffered Back not found');

            return redirect(route('paymentBankTransferDetailRefferedBacks.index'));
        }

        $this->paymentBankTransferDetailRefferedBackRepository->delete($id);

        Flash::success('Payment Bank Transfer Detail Reffered Back deleted successfully.');

        return redirect(route('paymentBankTransferDetailRefferedBacks.index'));
    }
}
