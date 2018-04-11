<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatePoAdvancePaymentRequest;
use App\Http\Requests\UpdatePoAdvancePaymentRequest;
use App\Repositories\PoAdvancePaymentRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class PoAdvancePaymentController extends AppBaseController
{
    /** @var  PoAdvancePaymentRepository */
    private $poAdvancePaymentRepository;

    public function __construct(PoAdvancePaymentRepository $poAdvancePaymentRepo)
    {
        $this->poAdvancePaymentRepository = $poAdvancePaymentRepo;
    }

    /**
     * Display a listing of the PoAdvancePayment.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->poAdvancePaymentRepository->pushCriteria(new RequestCriteria($request));
        $poAdvancePayments = $this->poAdvancePaymentRepository->all();

        return view('po_advance_payments.index')
            ->with('poAdvancePayments', $poAdvancePayments);
    }

    /**
     * Show the form for creating a new PoAdvancePayment.
     *
     * @return Response
     */
    public function create()
    {
        return view('po_advance_payments.create');
    }

    /**
     * Store a newly created PoAdvancePayment in storage.
     *
     * @param CreatePoAdvancePaymentRequest $request
     *
     * @return Response
     */
    public function store(CreatePoAdvancePaymentRequest $request)
    {
        $input = $request->all();

        $poAdvancePayment = $this->poAdvancePaymentRepository->create($input);

        Flash::success('Po Advance Payment saved successfully.');

        return redirect(route('poAdvancePayments.index'));
    }

    /**
     * Display the specified PoAdvancePayment.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $poAdvancePayment = $this->poAdvancePaymentRepository->findWithoutFail($id);

        if (empty($poAdvancePayment)) {
            Flash::error('Po Advance Payment not found');

            return redirect(route('poAdvancePayments.index'));
        }

        return view('po_advance_payments.show')->with('poAdvancePayment', $poAdvancePayment);
    }

    /**
     * Show the form for editing the specified PoAdvancePayment.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $poAdvancePayment = $this->poAdvancePaymentRepository->findWithoutFail($id);

        if (empty($poAdvancePayment)) {
            Flash::error('Po Advance Payment not found');

            return redirect(route('poAdvancePayments.index'));
        }

        return view('po_advance_payments.edit')->with('poAdvancePayment', $poAdvancePayment);
    }

    /**
     * Update the specified PoAdvancePayment in storage.
     *
     * @param  int              $id
     * @param UpdatePoAdvancePaymentRequest $request
     *
     * @return Response
     */
    public function update($id, UpdatePoAdvancePaymentRequest $request)
    {
        $poAdvancePayment = $this->poAdvancePaymentRepository->findWithoutFail($id);

        if (empty($poAdvancePayment)) {
            Flash::error('Po Advance Payment not found');

            return redirect(route('poAdvancePayments.index'));
        }

        $poAdvancePayment = $this->poAdvancePaymentRepository->update($request->all(), $id);

        Flash::success('Po Advance Payment updated successfully.');

        return redirect(route('poAdvancePayments.index'));
    }

    /**
     * Remove the specified PoAdvancePayment from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $poAdvancePayment = $this->poAdvancePaymentRepository->findWithoutFail($id);

        if (empty($poAdvancePayment)) {
            Flash::error('Po Advance Payment not found');

            return redirect(route('poAdvancePayments.index'));
        }

        $this->poAdvancePaymentRepository->delete($id);

        Flash::success('Po Advance Payment deleted successfully.');

        return redirect(route('poAdvancePayments.index'));
    }
}
