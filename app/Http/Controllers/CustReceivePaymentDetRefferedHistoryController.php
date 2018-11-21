<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateCustReceivePaymentDetRefferedHistoryRequest;
use App\Http\Requests\UpdateCustReceivePaymentDetRefferedHistoryRequest;
use App\Repositories\CustReceivePaymentDetRefferedHistoryRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class CustReceivePaymentDetRefferedHistoryController extends AppBaseController
{
    /** @var  CustReceivePaymentDetRefferedHistoryRepository */
    private $custReceivePaymentDetRefferedHistoryRepository;

    public function __construct(CustReceivePaymentDetRefferedHistoryRepository $custReceivePaymentDetRefferedHistoryRepo)
    {
        $this->custReceivePaymentDetRefferedHistoryRepository = $custReceivePaymentDetRefferedHistoryRepo;
    }

    /**
     * Display a listing of the CustReceivePaymentDetRefferedHistory.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->custReceivePaymentDetRefferedHistoryRepository->pushCriteria(new RequestCriteria($request));
        $custReceivePaymentDetRefferedHistories = $this->custReceivePaymentDetRefferedHistoryRepository->all();

        return view('cust_receive_payment_det_reffered_histories.index')
            ->with('custReceivePaymentDetRefferedHistories', $custReceivePaymentDetRefferedHistories);
    }

    /**
     * Show the form for creating a new CustReceivePaymentDetRefferedHistory.
     *
     * @return Response
     */
    public function create()
    {
        return view('cust_receive_payment_det_reffered_histories.create');
    }

    /**
     * Store a newly created CustReceivePaymentDetRefferedHistory in storage.
     *
     * @param CreateCustReceivePaymentDetRefferedHistoryRequest $request
     *
     * @return Response
     */
    public function store(CreateCustReceivePaymentDetRefferedHistoryRequest $request)
    {
        $input = $request->all();

        $custReceivePaymentDetRefferedHistory = $this->custReceivePaymentDetRefferedHistoryRepository->create($input);

        Flash::success('Cust Receive Payment Det Reffered History saved successfully.');

        return redirect(route('custReceivePaymentDetRefferedHistories.index'));
    }

    /**
     * Display the specified CustReceivePaymentDetRefferedHistory.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $custReceivePaymentDetRefferedHistory = $this->custReceivePaymentDetRefferedHistoryRepository->findWithoutFail($id);

        if (empty($custReceivePaymentDetRefferedHistory)) {
            Flash::error('Cust Receive Payment Det Reffered History not found');

            return redirect(route('custReceivePaymentDetRefferedHistories.index'));
        }

        return view('cust_receive_payment_det_reffered_histories.show')->with('custReceivePaymentDetRefferedHistory', $custReceivePaymentDetRefferedHistory);
    }

    /**
     * Show the form for editing the specified CustReceivePaymentDetRefferedHistory.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $custReceivePaymentDetRefferedHistory = $this->custReceivePaymentDetRefferedHistoryRepository->findWithoutFail($id);

        if (empty($custReceivePaymentDetRefferedHistory)) {
            Flash::error('Cust Receive Payment Det Reffered History not found');

            return redirect(route('custReceivePaymentDetRefferedHistories.index'));
        }

        return view('cust_receive_payment_det_reffered_histories.edit')->with('custReceivePaymentDetRefferedHistory', $custReceivePaymentDetRefferedHistory);
    }

    /**
     * Update the specified CustReceivePaymentDetRefferedHistory in storage.
     *
     * @param  int              $id
     * @param UpdateCustReceivePaymentDetRefferedHistoryRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateCustReceivePaymentDetRefferedHistoryRequest $request)
    {
        $custReceivePaymentDetRefferedHistory = $this->custReceivePaymentDetRefferedHistoryRepository->findWithoutFail($id);

        if (empty($custReceivePaymentDetRefferedHistory)) {
            Flash::error('Cust Receive Payment Det Reffered History not found');

            return redirect(route('custReceivePaymentDetRefferedHistories.index'));
        }

        $custReceivePaymentDetRefferedHistory = $this->custReceivePaymentDetRefferedHistoryRepository->update($request->all(), $id);

        Flash::success('Cust Receive Payment Det Reffered History updated successfully.');

        return redirect(route('custReceivePaymentDetRefferedHistories.index'));
    }

    /**
     * Remove the specified CustReceivePaymentDetRefferedHistory from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $custReceivePaymentDetRefferedHistory = $this->custReceivePaymentDetRefferedHistoryRepository->findWithoutFail($id);

        if (empty($custReceivePaymentDetRefferedHistory)) {
            Flash::error('Cust Receive Payment Det Reffered History not found');

            return redirect(route('custReceivePaymentDetRefferedHistories.index'));
        }

        $this->custReceivePaymentDetRefferedHistoryRepository->delete($id);

        Flash::success('Cust Receive Payment Det Reffered History deleted successfully.');

        return redirect(route('custReceivePaymentDetRefferedHistories.index'));
    }
}
