<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateAccountsReceivableLedgerRequest;
use App\Http\Requests\UpdateAccountsReceivableLedgerRequest;
use App\Repositories\AccountsReceivableLedgerRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class AccountsReceivableLedgerController extends AppBaseController
{
    /** @var  AccountsReceivableLedgerRepository */
    private $accountsReceivableLedgerRepository;

    public function __construct(AccountsReceivableLedgerRepository $accountsReceivableLedgerRepo)
    {
        $this->accountsReceivableLedgerRepository = $accountsReceivableLedgerRepo;
    }

    /**
     * Display a listing of the AccountsReceivableLedger.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->accountsReceivableLedgerRepository->pushCriteria(new RequestCriteria($request));
        $accountsReceivableLedgers = $this->accountsReceivableLedgerRepository->all();

        return view('accounts_receivable_ledgers.index')
            ->with('accountsReceivableLedgers', $accountsReceivableLedgers);
    }

    /**
     * Show the form for creating a new AccountsReceivableLedger.
     *
     * @return Response
     */
    public function create()
    {
        return view('accounts_receivable_ledgers.create');
    }

    /**
     * Store a newly created AccountsReceivableLedger in storage.
     *
     * @param CreateAccountsReceivableLedgerRequest $request
     *
     * @return Response
     */
    public function store(CreateAccountsReceivableLedgerRequest $request)
    {
        $input = $request->all();

        $accountsReceivableLedger = $this->accountsReceivableLedgerRepository->create($input);

        Flash::success('Accounts Receivable Ledger saved successfully.');

        return redirect(route('accountsReceivableLedgers.index'));
    }

    /**
     * Display the specified AccountsReceivableLedger.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $accountsReceivableLedger = $this->accountsReceivableLedgerRepository->findWithoutFail($id);

        if (empty($accountsReceivableLedger)) {
            Flash::error('Accounts Receivable Ledger not found');

            return redirect(route('accountsReceivableLedgers.index'));
        }

        return view('accounts_receivable_ledgers.show')->with('accountsReceivableLedger', $accountsReceivableLedger);
    }

    /**
     * Show the form for editing the specified AccountsReceivableLedger.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $accountsReceivableLedger = $this->accountsReceivableLedgerRepository->findWithoutFail($id);

        if (empty($accountsReceivableLedger)) {
            Flash::error('Accounts Receivable Ledger not found');

            return redirect(route('accountsReceivableLedgers.index'));
        }

        return view('accounts_receivable_ledgers.edit')->with('accountsReceivableLedger', $accountsReceivableLedger);
    }

    /**
     * Update the specified AccountsReceivableLedger in storage.
     *
     * @param  int              $id
     * @param UpdateAccountsReceivableLedgerRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateAccountsReceivableLedgerRequest $request)
    {
        $accountsReceivableLedger = $this->accountsReceivableLedgerRepository->findWithoutFail($id);

        if (empty($accountsReceivableLedger)) {
            Flash::error('Accounts Receivable Ledger not found');

            return redirect(route('accountsReceivableLedgers.index'));
        }

        $accountsReceivableLedger = $this->accountsReceivableLedgerRepository->update($request->all(), $id);

        Flash::success('Accounts Receivable Ledger updated successfully.');

        return redirect(route('accountsReceivableLedgers.index'));
    }

    /**
     * Remove the specified AccountsReceivableLedger from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $accountsReceivableLedger = $this->accountsReceivableLedgerRepository->findWithoutFail($id);

        if (empty($accountsReceivableLedger)) {
            Flash::error('Accounts Receivable Ledger not found');

            return redirect(route('accountsReceivableLedgers.index'));
        }

        $this->accountsReceivableLedgerRepository->delete($id);

        Flash::success('Accounts Receivable Ledger deleted successfully.');

        return redirect(route('accountsReceivableLedgers.index'));
    }
}
