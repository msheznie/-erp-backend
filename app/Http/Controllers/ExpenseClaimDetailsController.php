<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateExpenseClaimDetailsRequest;
use App\Http\Requests\UpdateExpenseClaimDetailsRequest;
use App\Repositories\ExpenseClaimDetailsRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class ExpenseClaimDetailsController extends AppBaseController
{
    /** @var  ExpenseClaimDetailsRepository */
    private $expenseClaimDetailsRepository;

    public function __construct(ExpenseClaimDetailsRepository $expenseClaimDetailsRepo)
    {
        $this->expenseClaimDetailsRepository = $expenseClaimDetailsRepo;
    }

    /**
     * Display a listing of the ExpenseClaimDetails.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->expenseClaimDetailsRepository->pushCriteria(new RequestCriteria($request));
        $expenseClaimDetails = $this->expenseClaimDetailsRepository->all();

        return view('expense_claim_details.index')
            ->with('expenseClaimDetails', $expenseClaimDetails);
    }

    /**
     * Show the form for creating a new ExpenseClaimDetails.
     *
     * @return Response
     */
    public function create()
    {
        return view('expense_claim_details.create');
    }

    /**
     * Store a newly created ExpenseClaimDetails in storage.
     *
     * @param CreateExpenseClaimDetailsRequest $request
     *
     * @return Response
     */
    public function store(CreateExpenseClaimDetailsRequest $request)
    {
        $input = $request->all();

        $expenseClaimDetails = $this->expenseClaimDetailsRepository->create($input);

        Flash::success('Expense Claim Details saved successfully.');

        return redirect(route('expenseClaimDetails.index'));
    }

    /**
     * Display the specified ExpenseClaimDetails.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $expenseClaimDetails = $this->expenseClaimDetailsRepository->findWithoutFail($id);

        if (empty($expenseClaimDetails)) {
            Flash::error('Expense Claim Details not found');

            return redirect(route('expenseClaimDetails.index'));
        }

        return view('expense_claim_details.show')->with('expenseClaimDetails', $expenseClaimDetails);
    }

    /**
     * Show the form for editing the specified ExpenseClaimDetails.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $expenseClaimDetails = $this->expenseClaimDetailsRepository->findWithoutFail($id);

        if (empty($expenseClaimDetails)) {
            Flash::error('Expense Claim Details not found');

            return redirect(route('expenseClaimDetails.index'));
        }

        return view('expense_claim_details.edit')->with('expenseClaimDetails', $expenseClaimDetails);
    }

    /**
     * Update the specified ExpenseClaimDetails in storage.
     *
     * @param  int              $id
     * @param UpdateExpenseClaimDetailsRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateExpenseClaimDetailsRequest $request)
    {
        $expenseClaimDetails = $this->expenseClaimDetailsRepository->findWithoutFail($id);

        if (empty($expenseClaimDetails)) {
            Flash::error('Expense Claim Details not found');

            return redirect(route('expenseClaimDetails.index'));
        }

        $expenseClaimDetails = $this->expenseClaimDetailsRepository->update($request->all(), $id);

        Flash::success('Expense Claim Details updated successfully.');

        return redirect(route('expenseClaimDetails.index'));
    }

    /**
     * Remove the specified ExpenseClaimDetails from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $expenseClaimDetails = $this->expenseClaimDetailsRepository->findWithoutFail($id);

        if (empty($expenseClaimDetails)) {
            Flash::error('Expense Claim Details not found');

            return redirect(route('expenseClaimDetails.index'));
        }

        $this->expenseClaimDetailsRepository->delete($id);

        Flash::success('Expense Claim Details deleted successfully.');

        return redirect(route('expenseClaimDetails.index'));
    }
}
