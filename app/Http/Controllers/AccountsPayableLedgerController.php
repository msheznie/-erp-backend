<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateAccountsPayableLedgerRequest;
use App\Http\Requests\UpdateAccountsPayableLedgerRequest;
use App\Repositories\AccountsPayableLedgerRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class AccountsPayableLedgerController extends AppBaseController
{
    /** @var  AccountsPayableLedgerRepository */
    private $accountsPayableLedgerRepository;

    public function __construct(AccountsPayableLedgerRepository $accountsPayableLedgerRepo)
    {
        $this->accountsPayableLedgerRepository = $accountsPayableLedgerRepo;
    }

    /**
     * Display a listing of the AccountsPayableLedger.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->accountsPayableLedgerRepository->pushCriteria(new RequestCriteria($request));
        $accountsPayableLedgers = $this->accountsPayableLedgerRepository->all();

        return view('accounts_payable_ledgers.index')
            ->with('accountsPayableLedgers', $accountsPayableLedgers);
    }

    /**
     * Show the form for creating a new AccountsPayableLedger.
     *
     * @return Response
     */
    public function create()
    {
        return view('accounts_payable_ledgers.create');
    }

    /**
     * Store a newly created AccountsPayableLedger in storage.
     *
     * @param CreateAccountsPayableLedgerRequest $request
     *
     * @return Response
     */
    public function store(CreateAccountsPayableLedgerRequest $request)
    {
        $input = $request->all();

        $accountsPayableLedger = $this->accountsPayableLedgerRepository->create($input);

        Flash::success('Accounts Payable Ledger saved successfully.');

        return redirect(route('accountsPayableLedgers.index'));
    }

    /**
     * Display the specified AccountsPayableLedger.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $accountsPayableLedger = $this->accountsPayableLedgerRepository->findWithoutFail($id);

        if (empty($accountsPayableLedger)) {
            Flash::error('Accounts Payable Ledger not found');

            return redirect(route('accountsPayableLedgers.index'));
        }

        return view('accounts_payable_ledgers.show')->with('accountsPayableLedger', $accountsPayableLedger);
    }

    /**
     * Show the form for editing the specified AccountsPayableLedger.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $accountsPayableLedger = $this->accountsPayableLedgerRepository->findWithoutFail($id);

        if (empty($accountsPayableLedger)) {
            Flash::error('Accounts Payable Ledger not found');

            return redirect(route('accountsPayableLedgers.index'));
        }

        return view('accounts_payable_ledgers.edit')->with('accountsPayableLedger', $accountsPayableLedger);
    }

    /**
     * Update the specified AccountsPayableLedger in storage.
     *
     * @param  int              $id
     * @param UpdateAccountsPayableLedgerRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateAccountsPayableLedgerRequest $request)
    {
        $accountsPayableLedger = $this->accountsPayableLedgerRepository->findWithoutFail($id);

        if (empty($accountsPayableLedger)) {
            Flash::error('Accounts Payable Ledger not found');

            return redirect(route('accountsPayableLedgers.index'));
        }

        $accountsPayableLedger = $this->accountsPayableLedgerRepository->update($request->all(), $id);

        Flash::success('Accounts Payable Ledger updated successfully.');

        return redirect(route('accountsPayableLedgers.index'));
    }

    /**
     * Remove the specified AccountsPayableLedger from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $accountsPayableLedger = $this->accountsPayableLedgerRepository->findWithoutFail($id);

        if (empty($accountsPayableLedger)) {
            Flash::error('Accounts Payable Ledger not found');

            return redirect(route('accountsPayableLedgers.index'));
        }

        $this->accountsPayableLedgerRepository->delete($id);

        Flash::success('Accounts Payable Ledger deleted successfully.');

        return redirect(route('accountsPayableLedgers.index'));
    }
}
