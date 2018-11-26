<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateBankMemoPayeeRequest;
use App\Http\Requests\UpdateBankMemoPayeeRequest;
use App\Repositories\BankMemoPayeeRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class BankMemoPayeeController extends AppBaseController
{
    /** @var  BankMemoPayeeRepository */
    private $bankMemoPayeeRepository;

    public function __construct(BankMemoPayeeRepository $bankMemoPayeeRepo)
    {
        $this->bankMemoPayeeRepository = $bankMemoPayeeRepo;
    }

    /**
     * Display a listing of the BankMemoPayee.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->bankMemoPayeeRepository->pushCriteria(new RequestCriteria($request));
        $bankMemoPayees = $this->bankMemoPayeeRepository->all();

        return view('bank_memo_payees.index')
            ->with('bankMemoPayees', $bankMemoPayees);
    }

    /**
     * Show the form for creating a new BankMemoPayee.
     *
     * @return Response
     */
    public function create()
    {
        return view('bank_memo_payees.create');
    }

    /**
     * Store a newly created BankMemoPayee in storage.
     *
     * @param CreateBankMemoPayeeRequest $request
     *
     * @return Response
     */
    public function store(CreateBankMemoPayeeRequest $request)
    {
        $input = $request->all();

        $bankMemoPayee = $this->bankMemoPayeeRepository->create($input);

        Flash::success('Bank Memo Payee saved successfully.');

        return redirect(route('bankMemoPayees.index'));
    }

    /**
     * Display the specified BankMemoPayee.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $bankMemoPayee = $this->bankMemoPayeeRepository->findWithoutFail($id);

        if (empty($bankMemoPayee)) {
            Flash::error('Bank Memo Payee not found');

            return redirect(route('bankMemoPayees.index'));
        }

        return view('bank_memo_payees.show')->with('bankMemoPayee', $bankMemoPayee);
    }

    /**
     * Show the form for editing the specified BankMemoPayee.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $bankMemoPayee = $this->bankMemoPayeeRepository->findWithoutFail($id);

        if (empty($bankMemoPayee)) {
            Flash::error('Bank Memo Payee not found');

            return redirect(route('bankMemoPayees.index'));
        }

        return view('bank_memo_payees.edit')->with('bankMemoPayee', $bankMemoPayee);
    }

    /**
     * Update the specified BankMemoPayee in storage.
     *
     * @param  int              $id
     * @param UpdateBankMemoPayeeRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateBankMemoPayeeRequest $request)
    {
        $bankMemoPayee = $this->bankMemoPayeeRepository->findWithoutFail($id);

        if (empty($bankMemoPayee)) {
            Flash::error('Bank Memo Payee not found');

            return redirect(route('bankMemoPayees.index'));
        }

        $bankMemoPayee = $this->bankMemoPayeeRepository->update($request->all(), $id);

        Flash::success('Bank Memo Payee updated successfully.');

        return redirect(route('bankMemoPayees.index'));
    }

    /**
     * Remove the specified BankMemoPayee from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $bankMemoPayee = $this->bankMemoPayeeRepository->findWithoutFail($id);

        if (empty($bankMemoPayee)) {
            Flash::error('Bank Memo Payee not found');

            return redirect(route('bankMemoPayees.index'));
        }

        $this->bankMemoPayeeRepository->delete($id);

        Flash::success('Bank Memo Payee deleted successfully.');

        return redirect(route('bankMemoPayees.index'));
    }
}
