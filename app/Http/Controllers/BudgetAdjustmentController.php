<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateBudgetAdjustmentRequest;
use App\Http\Requests\UpdateBudgetAdjustmentRequest;
use App\Repositories\BudgetAdjustmentRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class BudgetAdjustmentController extends AppBaseController
{
    /** @var  BudgetAdjustmentRepository */
    private $budgetAdjustmentRepository;

    public function __construct(BudgetAdjustmentRepository $budgetAdjustmentRepo)
    {
        $this->budgetAdjustmentRepository = $budgetAdjustmentRepo;
    }

    /**
     * Display a listing of the BudgetAdjustment.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->budgetAdjustmentRepository->pushCriteria(new RequestCriteria($request));
        $budgetAdjustments = $this->budgetAdjustmentRepository->all();

        return view('budget_adjustments.index')
            ->with('budgetAdjustments', $budgetAdjustments);
    }

    /**
     * Show the form for creating a new BudgetAdjustment.
     *
     * @return Response
     */
    public function create()
    {
        return view('budget_adjustments.create');
    }

    /**
     * Store a newly created BudgetAdjustment in storage.
     *
     * @param CreateBudgetAdjustmentRequest $request
     *
     * @return Response
     */
    public function store(CreateBudgetAdjustmentRequest $request)
    {
        $input = $request->all();

        $budgetAdjustment = $this->budgetAdjustmentRepository->create($input);

        Flash::success('Budget Adjustment saved successfully.');

        return redirect(route('budgetAdjustments.index'));
    }

    /**
     * Display the specified BudgetAdjustment.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $budgetAdjustment = $this->budgetAdjustmentRepository->findWithoutFail($id);

        if (empty($budgetAdjustment)) {
            Flash::error('Budget Adjustment not found');

            return redirect(route('budgetAdjustments.index'));
        }

        return view('budget_adjustments.show')->with('budgetAdjustment', $budgetAdjustment);
    }

    /**
     * Show the form for editing the specified BudgetAdjustment.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $budgetAdjustment = $this->budgetAdjustmentRepository->findWithoutFail($id);

        if (empty($budgetAdjustment)) {
            Flash::error('Budget Adjustment not found');

            return redirect(route('budgetAdjustments.index'));
        }

        return view('budget_adjustments.edit')->with('budgetAdjustment', $budgetAdjustment);
    }

    /**
     * Update the specified BudgetAdjustment in storage.
     *
     * @param  int              $id
     * @param UpdateBudgetAdjustmentRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateBudgetAdjustmentRequest $request)
    {
        $budgetAdjustment = $this->budgetAdjustmentRepository->findWithoutFail($id);

        if (empty($budgetAdjustment)) {
            Flash::error('Budget Adjustment not found');

            return redirect(route('budgetAdjustments.index'));
        }

        $budgetAdjustment = $this->budgetAdjustmentRepository->update($request->all(), $id);

        Flash::success('Budget Adjustment updated successfully.');

        return redirect(route('budgetAdjustments.index'));
    }

    /**
     * Remove the specified BudgetAdjustment from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $budgetAdjustment = $this->budgetAdjustmentRepository->findWithoutFail($id);

        if (empty($budgetAdjustment)) {
            Flash::error('Budget Adjustment not found');

            return redirect(route('budgetAdjustments.index'));
        }

        $this->budgetAdjustmentRepository->delete($id);

        Flash::success('Budget Adjustment deleted successfully.');

        return redirect(route('budgetAdjustments.index'));
    }
}
