<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateExpenseClaimCategoriesRequest;
use App\Http\Requests\UpdateExpenseClaimCategoriesRequest;
use App\Repositories\ExpenseClaimCategoriesRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class ExpenseClaimCategoriesController extends AppBaseController
{
    /** @var  ExpenseClaimCategoriesRepository */
    private $expenseClaimCategoriesRepository;

    public function __construct(ExpenseClaimCategoriesRepository $expenseClaimCategoriesRepo)
    {
        $this->expenseClaimCategoriesRepository = $expenseClaimCategoriesRepo;
    }

    /**
     * Display a listing of the ExpenseClaimCategories.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->expenseClaimCategoriesRepository->pushCriteria(new RequestCriteria($request));
        $expenseClaimCategories = $this->expenseClaimCategoriesRepository->all();

        return view('expense_claim_categories.index')
            ->with('expenseClaimCategories', $expenseClaimCategories);
    }

    /**
     * Show the form for creating a new ExpenseClaimCategories.
     *
     * @return Response
     */
    public function create()
    {
        return view('expense_claim_categories.create');
    }

    /**
     * Store a newly created ExpenseClaimCategories in storage.
     *
     * @param CreateExpenseClaimCategoriesRequest $request
     *
     * @return Response
     */
    public function store(CreateExpenseClaimCategoriesRequest $request)
    {
        $input = $request->all();

        $expenseClaimCategories = $this->expenseClaimCategoriesRepository->create($input);

        Flash::success('Expense Claim Categories saved successfully.');

        return redirect(route('expenseClaimCategories.index'));
    }

    /**
     * Display the specified ExpenseClaimCategories.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $expenseClaimCategories = $this->expenseClaimCategoriesRepository->findWithoutFail($id);

        if (empty($expenseClaimCategories)) {
            Flash::error('Expense Claim Categories not found');

            return redirect(route('expenseClaimCategories.index'));
        }

        return view('expense_claim_categories.show')->with('expenseClaimCategories', $expenseClaimCategories);
    }

    /**
     * Show the form for editing the specified ExpenseClaimCategories.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $expenseClaimCategories = $this->expenseClaimCategoriesRepository->findWithoutFail($id);

        if (empty($expenseClaimCategories)) {
            Flash::error('Expense Claim Categories not found');

            return redirect(route('expenseClaimCategories.index'));
        }

        return view('expense_claim_categories.edit')->with('expenseClaimCategories', $expenseClaimCategories);
    }

    /**
     * Update the specified ExpenseClaimCategories in storage.
     *
     * @param  int              $id
     * @param UpdateExpenseClaimCategoriesRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateExpenseClaimCategoriesRequest $request)
    {
        $expenseClaimCategories = $this->expenseClaimCategoriesRepository->findWithoutFail($id);

        if (empty($expenseClaimCategories)) {
            Flash::error('Expense Claim Categories not found');

            return redirect(route('expenseClaimCategories.index'));
        }

        $expenseClaimCategories = $this->expenseClaimCategoriesRepository->update($request->all(), $id);

        Flash::success('Expense Claim Categories updated successfully.');

        return redirect(route('expenseClaimCategories.index'));
    }

    /**
     * Remove the specified ExpenseClaimCategories from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $expenseClaimCategories = $this->expenseClaimCategoriesRepository->findWithoutFail($id);

        if (empty($expenseClaimCategories)) {
            Flash::error('Expense Claim Categories not found');

            return redirect(route('expenseClaimCategories.index'));
        }

        $this->expenseClaimCategoriesRepository->delete($id);

        Flash::success('Expense Claim Categories deleted successfully.');

        return redirect(route('expenseClaimCategories.index'));
    }
}
