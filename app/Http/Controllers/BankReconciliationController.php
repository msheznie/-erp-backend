<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateBankReconciliationRequest;
use App\Http\Requests\UpdateBankReconciliationRequest;
use App\Repositories\BankReconciliationRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class BankReconciliationController extends AppBaseController
{
    /** @var  BankReconciliationRepository */
    private $bankReconciliationRepository;

    public function __construct(BankReconciliationRepository $bankReconciliationRepo)
    {
        $this->bankReconciliationRepository = $bankReconciliationRepo;
    }

    /**
     * Display a listing of the BankReconciliation.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->bankReconciliationRepository->pushCriteria(new RequestCriteria($request));
        $bankReconciliations = $this->bankReconciliationRepository->all();

        return view('bank_reconciliations.index')
            ->with('bankReconciliations', $bankReconciliations);
    }

    /**
     * Show the form for creating a new BankReconciliation.
     *
     * @return Response
     */
    public function create()
    {
        return view('bank_reconciliations.create');
    }

    /**
     * Store a newly created BankReconciliation in storage.
     *
     * @param CreateBankReconciliationRequest $request
     *
     * @return Response
     */
    public function store(CreateBankReconciliationRequest $request)
    {
        $input = $request->all();

        $bankReconciliation = $this->bankReconciliationRepository->create($input);

        Flash::success('Bank Reconciliation saved successfully.');

        return redirect(route('bankReconciliations.index'));
    }

    /**
     * Display the specified BankReconciliation.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $bankReconciliation = $this->bankReconciliationRepository->findWithoutFail($id);

        if (empty($bankReconciliation)) {
            Flash::error('Bank Reconciliation not found');

            return redirect(route('bankReconciliations.index'));
        }

        return view('bank_reconciliations.show')->with('bankReconciliation', $bankReconciliation);
    }

    /**
     * Show the form for editing the specified BankReconciliation.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $bankReconciliation = $this->bankReconciliationRepository->findWithoutFail($id);

        if (empty($bankReconciliation)) {
            Flash::error('Bank Reconciliation not found');

            return redirect(route('bankReconciliations.index'));
        }

        return view('bank_reconciliations.edit')->with('bankReconciliation', $bankReconciliation);
    }

    /**
     * Update the specified BankReconciliation in storage.
     *
     * @param  int              $id
     * @param UpdateBankReconciliationRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateBankReconciliationRequest $request)
    {
        $bankReconciliation = $this->bankReconciliationRepository->findWithoutFail($id);

        if (empty($bankReconciliation)) {
            Flash::error('Bank Reconciliation not found');

            return redirect(route('bankReconciliations.index'));
        }

        $bankReconciliation = $this->bankReconciliationRepository->update($request->all(), $id);

        Flash::success('Bank Reconciliation updated successfully.');

        return redirect(route('bankReconciliations.index'));
    }

    /**
     * Remove the specified BankReconciliation from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $bankReconciliation = $this->bankReconciliationRepository->findWithoutFail($id);

        if (empty($bankReconciliation)) {
            Flash::error('Bank Reconciliation not found');

            return redirect(route('bankReconciliations.index'));
        }

        $this->bankReconciliationRepository->delete($id);

        Flash::success('Bank Reconciliation deleted successfully.');

        return redirect(route('bankReconciliations.index'));
    }
}
