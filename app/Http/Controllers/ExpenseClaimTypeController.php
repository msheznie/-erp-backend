<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateExpenseClaimTypeRequest;
use App\Http\Requests\UpdateExpenseClaimTypeRequest;
use App\Repositories\ExpenseClaimTypeRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class ExpenseClaimTypeController extends AppBaseController
{
    /** @var  ExpenseClaimTypeRepository */
    private $expenseClaimTypeRepository;

    public function __construct(ExpenseClaimTypeRepository $expenseClaimTypeRepo)
    {
        $this->expenseClaimTypeRepository = $expenseClaimTypeRepo;
    }

    /**
     * Display a listing of the ExpenseClaimType.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->expenseClaimTypeRepository->pushCriteria(new RequestCriteria($request));
        $expenseClaimTypes = $this->expenseClaimTypeRepository->all();

        return view('expense_claim_types.index')
            ->with('expenseClaimTypes', $expenseClaimTypes);
    }

    /**
     * Show the form for creating a new ExpenseClaimType.
     *
     * @return Response
     */
    public function create()
    {
        return view('expense_claim_types.create');
    }

    /**
     * Store a newly created ExpenseClaimType in storage.
     *
     * @param CreateExpenseClaimTypeRequest $request
     *
     * @return Response
     */
    public function store(CreateExpenseClaimTypeRequest $request)
    {
        $input = $request->all();

        $expenseClaimType = $this->expenseClaimTypeRepository->create($input);

        Flash::success('Expense Claim Type saved successfully.');

        return redirect(route('expenseClaimTypes.index'));
    }

    /**
     * Display the specified ExpenseClaimType.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $expenseClaimType = $this->expenseClaimTypeRepository->findWithoutFail($id);

        if (empty($expenseClaimType)) {
            Flash::error('Expense Claim Type not found');

            return redirect(route('expenseClaimTypes.index'));
        }

        return view('expense_claim_types.show')->with('expenseClaimType', $expenseClaimType);
    }

    /**
     * Show the form for editing the specified ExpenseClaimType.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $expenseClaimType = $this->expenseClaimTypeRepository->findWithoutFail($id);

        if (empty($expenseClaimType)) {
            Flash::error('Expense Claim Type not found');

            return redirect(route('expenseClaimTypes.index'));
        }

        return view('expense_claim_types.edit')->with('expenseClaimType', $expenseClaimType);
    }

    /**
     * Update the specified ExpenseClaimType in storage.
     *
     * @param  int              $id
     * @param UpdateExpenseClaimTypeRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateExpenseClaimTypeRequest $request)
    {
        $expenseClaimType = $this->expenseClaimTypeRepository->findWithoutFail($id);

        if (empty($expenseClaimType)) {
            Flash::error('Expense Claim Type not found');

            return redirect(route('expenseClaimTypes.index'));
        }

        $expenseClaimType = $this->expenseClaimTypeRepository->update($request->all(), $id);

        Flash::success('Expense Claim Type updated successfully.');

        return redirect(route('expenseClaimTypes.index'));
    }

    /**
     * Remove the specified ExpenseClaimType from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $expenseClaimType = $this->expenseClaimTypeRepository->findWithoutFail($id);

        if (empty($expenseClaimType)) {
            Flash::error('Expense Claim Type not found');

            return redirect(route('expenseClaimTypes.index'));
        }

        $this->expenseClaimTypeRepository->delete($id);

        Flash::success('Expense Claim Type deleted successfully.');

        return redirect(route('expenseClaimTypes.index'));
    }
}
