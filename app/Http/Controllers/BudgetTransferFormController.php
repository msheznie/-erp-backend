<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateBudgetTransferFormRequest;
use App\Http\Requests\UpdateBudgetTransferFormRequest;
use App\Repositories\BudgetTransferFormRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class BudgetTransferFormController extends AppBaseController
{
    /** @var  BudgetTransferFormRepository */
    private $budgetTransferFormRepository;

    public function __construct(BudgetTransferFormRepository $budgetTransferFormRepo)
    {
        $this->budgetTransferFormRepository = $budgetTransferFormRepo;
    }

    /**
     * Display a listing of the BudgetTransferForm.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->budgetTransferFormRepository->pushCriteria(new RequestCriteria($request));
        $budgetTransferForms = $this->budgetTransferFormRepository->all();

        return view('budget_transfer_forms.index')
            ->with('budgetTransferForms', $budgetTransferForms);
    }

    /**
     * Show the form for creating a new BudgetTransferForm.
     *
     * @return Response
     */
    public function create()
    {
        return view('budget_transfer_forms.create');
    }

    /**
     * Store a newly created BudgetTransferForm in storage.
     *
     * @param CreateBudgetTransferFormRequest $request
     *
     * @return Response
     */
    public function store(CreateBudgetTransferFormRequest $request)
    {
        $input = $request->all();

        $budgetTransferForm = $this->budgetTransferFormRepository->create($input);

        Flash::success('Budget Transfer Form saved successfully.');

        return redirect(route('budgetTransferForms.index'));
    }

    /**
     * Display the specified BudgetTransferForm.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $budgetTransferForm = $this->budgetTransferFormRepository->findWithoutFail($id);

        if (empty($budgetTransferForm)) {
            Flash::error('Budget Transfer Form not found');

            return redirect(route('budgetTransferForms.index'));
        }

        return view('budget_transfer_forms.show')->with('budgetTransferForm', $budgetTransferForm);
    }

    /**
     * Show the form for editing the specified BudgetTransferForm.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $budgetTransferForm = $this->budgetTransferFormRepository->findWithoutFail($id);

        if (empty($budgetTransferForm)) {
            Flash::error('Budget Transfer Form not found');

            return redirect(route('budgetTransferForms.index'));
        }

        return view('budget_transfer_forms.edit')->with('budgetTransferForm', $budgetTransferForm);
    }

    /**
     * Update the specified BudgetTransferForm in storage.
     *
     * @param  int              $id
     * @param UpdateBudgetTransferFormRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateBudgetTransferFormRequest $request)
    {
        $budgetTransferForm = $this->budgetTransferFormRepository->findWithoutFail($id);

        if (empty($budgetTransferForm)) {
            Flash::error('Budget Transfer Form not found');

            return redirect(route('budgetTransferForms.index'));
        }

        $budgetTransferForm = $this->budgetTransferFormRepository->update($request->all(), $id);

        Flash::success('Budget Transfer Form updated successfully.');

        return redirect(route('budgetTransferForms.index'));
    }

    /**
     * Remove the specified BudgetTransferForm from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $budgetTransferForm = $this->budgetTransferFormRepository->findWithoutFail($id);

        if (empty($budgetTransferForm)) {
            Flash::error('Budget Transfer Form not found');

            return redirect(route('budgetTransferForms.index'));
        }

        $this->budgetTransferFormRepository->delete($id);

        Flash::success('Budget Transfer Form deleted successfully.');

        return redirect(route('budgetTransferForms.index'));
    }
}
