<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateExpenseClaimRequest;
use App\Http\Requests\UpdateExpenseClaimRequest;
use App\Repositories\ExpenseClaimRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class ExpenseClaimController extends AppBaseController
{
    /** @var  ExpenseClaimRepository */
    private $expenseClaimRepository;

    public function __construct(ExpenseClaimRepository $expenseClaimRepo)
    {
        $this->expenseClaimRepository = $expenseClaimRepo;
    }

    /**
     * Display a listing of the ExpenseClaim.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->expenseClaimRepository->pushCriteria(new RequestCriteria($request));
        $expenseClaims = $this->expenseClaimRepository->all();

        return view('expense_claims.index')
            ->with('expenseClaims', $expenseClaims);
    }

    /**
     * Show the form for creating a new ExpenseClaim.
     *
     * @return Response
     */
    public function create()
    {
        return view('expense_claims.create');
    }

    /**
     * Store a newly created ExpenseClaim in storage.
     *
     * @param CreateExpenseClaimRequest $request
     *
     * @return Response
     */
    public function store(CreateExpenseClaimRequest $request)
    {
        $input = $request->all();

        $expenseClaim = $this->expenseClaimRepository->create($input);

        Flash::success('Expense Claim saved successfully.');

        return redirect(route('expenseClaims.index'));
    }

    /**
     * Display the specified ExpenseClaim.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $expenseClaim = $this->expenseClaimRepository->findWithoutFail($id);

        if (empty($expenseClaim)) {
            Flash::error('Expense Claim not found');

            return redirect(route('expenseClaims.index'));
        }

        return view('expense_claims.show')->with('expenseClaim', $expenseClaim);
    }

    /**
     * Show the form for editing the specified ExpenseClaim.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $expenseClaim = $this->expenseClaimRepository->findWithoutFail($id);

        if (empty($expenseClaim)) {
            Flash::error('Expense Claim not found');

            return redirect(route('expenseClaims.index'));
        }

        return view('expense_claims.edit')->with('expenseClaim', $expenseClaim);
    }

    /**
     * Update the specified ExpenseClaim in storage.
     *
     * @param  int              $id
     * @param UpdateExpenseClaimRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateExpenseClaimRequest $request)
    {
        $expenseClaim = $this->expenseClaimRepository->findWithoutFail($id);

        if (empty($expenseClaim)) {
            Flash::error('Expense Claim not found');

            return redirect(route('expenseClaims.index'));
        }

        $expenseClaim = $this->expenseClaimRepository->update($request->all(), $id);

        Flash::success('Expense Claim updated successfully.');

        return redirect(route('expenseClaims.index'));
    }

    /**
     * Remove the specified ExpenseClaim from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $expenseClaim = $this->expenseClaimRepository->findWithoutFail($id);

        if (empty($expenseClaim)) {
            Flash::error('Expense Claim not found');

            return redirect(route('expenseClaims.index'));
        }

        $this->expenseClaimRepository->delete($id);

        Flash::success('Expense Claim deleted successfully.');

        return redirect(route('expenseClaims.index'));
    }
}
