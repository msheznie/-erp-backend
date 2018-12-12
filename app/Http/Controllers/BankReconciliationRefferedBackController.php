<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateBankReconciliationRefferedBackRequest;
use App\Http\Requests\UpdateBankReconciliationRefferedBackRequest;
use App\Repositories\BankReconciliationRefferedBackRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class BankReconciliationRefferedBackController extends AppBaseController
{
    /** @var  BankReconciliationRefferedBackRepository */
    private $bankReconciliationRefferedBackRepository;

    public function __construct(BankReconciliationRefferedBackRepository $bankReconciliationRefferedBackRepo)
    {
        $this->bankReconciliationRefferedBackRepository = $bankReconciliationRefferedBackRepo;
    }

    /**
     * Display a listing of the BankReconciliationRefferedBack.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->bankReconciliationRefferedBackRepository->pushCriteria(new RequestCriteria($request));
        $bankReconciliationRefferedBacks = $this->bankReconciliationRefferedBackRepository->all();

        return view('bank_reconciliation_reffered_backs.index')
            ->with('bankReconciliationRefferedBacks', $bankReconciliationRefferedBacks);
    }

    /**
     * Show the form for creating a new BankReconciliationRefferedBack.
     *
     * @return Response
     */
    public function create()
    {
        return view('bank_reconciliation_reffered_backs.create');
    }

    /**
     * Store a newly created BankReconciliationRefferedBack in storage.
     *
     * @param CreateBankReconciliationRefferedBackRequest $request
     *
     * @return Response
     */
    public function store(CreateBankReconciliationRefferedBackRequest $request)
    {
        $input = $request->all();

        $bankReconciliationRefferedBack = $this->bankReconciliationRefferedBackRepository->create($input);

        Flash::success('Bank Reconciliation Reffered Back saved successfully.');

        return redirect(route('bankReconciliationRefferedBacks.index'));
    }

    /**
     * Display the specified BankReconciliationRefferedBack.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $bankReconciliationRefferedBack = $this->bankReconciliationRefferedBackRepository->findWithoutFail($id);

        if (empty($bankReconciliationRefferedBack)) {
            Flash::error('Bank Reconciliation Reffered Back not found');

            return redirect(route('bankReconciliationRefferedBacks.index'));
        }

        return view('bank_reconciliation_reffered_backs.show')->with('bankReconciliationRefferedBack', $bankReconciliationRefferedBack);
    }

    /**
     * Show the form for editing the specified BankReconciliationRefferedBack.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $bankReconciliationRefferedBack = $this->bankReconciliationRefferedBackRepository->findWithoutFail($id);

        if (empty($bankReconciliationRefferedBack)) {
            Flash::error('Bank Reconciliation Reffered Back not found');

            return redirect(route('bankReconciliationRefferedBacks.index'));
        }

        return view('bank_reconciliation_reffered_backs.edit')->with('bankReconciliationRefferedBack', $bankReconciliationRefferedBack);
    }

    /**
     * Update the specified BankReconciliationRefferedBack in storage.
     *
     * @param  int              $id
     * @param UpdateBankReconciliationRefferedBackRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateBankReconciliationRefferedBackRequest $request)
    {
        $bankReconciliationRefferedBack = $this->bankReconciliationRefferedBackRepository->findWithoutFail($id);

        if (empty($bankReconciliationRefferedBack)) {
            Flash::error('Bank Reconciliation Reffered Back not found');

            return redirect(route('bankReconciliationRefferedBacks.index'));
        }

        $bankReconciliationRefferedBack = $this->bankReconciliationRefferedBackRepository->update($request->all(), $id);

        Flash::success('Bank Reconciliation Reffered Back updated successfully.');

        return redirect(route('bankReconciliationRefferedBacks.index'));
    }

    /**
     * Remove the specified BankReconciliationRefferedBack from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $bankReconciliationRefferedBack = $this->bankReconciliationRefferedBackRepository->findWithoutFail($id);

        if (empty($bankReconciliationRefferedBack)) {
            Flash::error('Bank Reconciliation Reffered Back not found');

            return redirect(route('bankReconciliationRefferedBacks.index'));
        }

        $this->bankReconciliationRefferedBackRepository->delete($id);

        Flash::success('Bank Reconciliation Reffered Back deleted successfully.');

        return redirect(route('bankReconciliationRefferedBacks.index'));
    }
}
