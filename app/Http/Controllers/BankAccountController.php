<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateBankAccountRequest;
use App\Http\Requests\UpdateBankAccountRequest;
use App\Repositories\BankAccountRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class BankAccountController extends AppBaseController
{
    /** @var  BankAccountRepository */
    private $bankAccountRepository;

    public function __construct(BankAccountRepository $bankAccountRepo)
    {
        $this->bankAccountRepository = $bankAccountRepo;
    }

    /**
     * Display a listing of the BankAccount.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->bankAccountRepository->pushCriteria(new RequestCriteria($request));
        $bankAccounts = $this->bankAccountRepository->all();

        return view('bank_accounts.index')
            ->with('bankAccounts', $bankAccounts);
    }

    /**
     * Show the form for creating a new BankAccount.
     *
     * @return Response
     */
    public function create()
    {
        return view('bank_accounts.create');
    }

    /**
     * Store a newly created BankAccount in storage.
     *
     * @param CreateBankAccountRequest $request
     *
     * @return Response
     */
    public function store(CreateBankAccountRequest $request)
    {
        $input = $request->all();

        $bankAccount = $this->bankAccountRepository->create($input);

        Flash::success('Bank Account saved successfully.');

        return redirect(route('bankAccounts.index'));
    }

    /**
     * Display the specified BankAccount.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $bankAccount = $this->bankAccountRepository->findWithoutFail($id);

        if (empty($bankAccount)) {
            Flash::error('Bank Account not found');

            return redirect(route('bankAccounts.index'));
        }

        return view('bank_accounts.show')->with('bankAccount', $bankAccount);
    }

    /**
     * Show the form for editing the specified BankAccount.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $bankAccount = $this->bankAccountRepository->findWithoutFail($id);

        if (empty($bankAccount)) {
            Flash::error('Bank Account not found');

            return redirect(route('bankAccounts.index'));
        }

        return view('bank_accounts.edit')->with('bankAccount', $bankAccount);
    }

    /**
     * Update the specified BankAccount in storage.
     *
     * @param  int              $id
     * @param UpdateBankAccountRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateBankAccountRequest $request)
    {
        $bankAccount = $this->bankAccountRepository->findWithoutFail($id);

        if (empty($bankAccount)) {
            Flash::error('Bank Account not found');

            return redirect(route('bankAccounts.index'));
        }

        $bankAccount = $this->bankAccountRepository->update($request->all(), $id);

        Flash::success('Bank Account updated successfully.');

        return redirect(route('bankAccounts.index'));
    }

    /**
     * Remove the specified BankAccount from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $bankAccount = $this->bankAccountRepository->findWithoutFail($id);

        if (empty($bankAccount)) {
            Flash::error('Bank Account not found');

            return redirect(route('bankAccounts.index'));
        }

        $this->bankAccountRepository->delete($id);

        Flash::success('Bank Account deleted successfully.');

        return redirect(route('bankAccounts.index'));
    }
}
