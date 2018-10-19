<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateBudgetTransferFormDetailRequest;
use App\Http\Requests\UpdateBudgetTransferFormDetailRequest;
use App\Repositories\BudgetTransferFormDetailRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class BudgetTransferFormDetailController extends AppBaseController
{
    /** @var  BudgetTransferFormDetailRepository */
    private $budgetTransferFormDetailRepository;

    public function __construct(BudgetTransferFormDetailRepository $budgetTransferFormDetailRepo)
    {
        $this->budgetTransferFormDetailRepository = $budgetTransferFormDetailRepo;
    }

    /**
     * Display a listing of the BudgetTransferFormDetail.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->budgetTransferFormDetailRepository->pushCriteria(new RequestCriteria($request));
        $budgetTransferFormDetails = $this->budgetTransferFormDetailRepository->all();

        return view('budget_transfer_form_details.index')
            ->with('budgetTransferFormDetails', $budgetTransferFormDetails);
    }

    /**
     * Show the form for creating a new BudgetTransferFormDetail.
     *
     * @return Response
     */
    public function create()
    {
        return view('budget_transfer_form_details.create');
    }

    /**
     * Store a newly created BudgetTransferFormDetail in storage.
     *
     * @param CreateBudgetTransferFormDetailRequest $request
     *
     * @return Response
     */
    public function store(CreateBudgetTransferFormDetailRequest $request)
    {
        $input = $request->all();

        $budgetTransferFormDetail = $this->budgetTransferFormDetailRepository->create($input);

        Flash::success('Budget Transfer Form Detail saved successfully.');

        return redirect(route('budgetTransferFormDetails.index'));
    }

    /**
     * Display the specified BudgetTransferFormDetail.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $budgetTransferFormDetail = $this->budgetTransferFormDetailRepository->findWithoutFail($id);

        if (empty($budgetTransferFormDetail)) {
            Flash::error('Budget Transfer Form Detail not found');

            return redirect(route('budgetTransferFormDetails.index'));
        }

        return view('budget_transfer_form_details.show')->with('budgetTransferFormDetail', $budgetTransferFormDetail);
    }

    /**
     * Show the form for editing the specified BudgetTransferFormDetail.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $budgetTransferFormDetail = $this->budgetTransferFormDetailRepository->findWithoutFail($id);

        if (empty($budgetTransferFormDetail)) {
            Flash::error('Budget Transfer Form Detail not found');

            return redirect(route('budgetTransferFormDetails.index'));
        }

        return view('budget_transfer_form_details.edit')->with('budgetTransferFormDetail', $budgetTransferFormDetail);
    }

    /**
     * Update the specified BudgetTransferFormDetail in storage.
     *
     * @param  int              $id
     * @param UpdateBudgetTransferFormDetailRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateBudgetTransferFormDetailRequest $request)
    {
        $budgetTransferFormDetail = $this->budgetTransferFormDetailRepository->findWithoutFail($id);

        if (empty($budgetTransferFormDetail)) {
            Flash::error('Budget Transfer Form Detail not found');

            return redirect(route('budgetTransferFormDetails.index'));
        }

        $budgetTransferFormDetail = $this->budgetTransferFormDetailRepository->update($request->all(), $id);

        Flash::success('Budget Transfer Form Detail updated successfully.');

        return redirect(route('budgetTransferFormDetails.index'));
    }

    /**
     * Remove the specified BudgetTransferFormDetail from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $budgetTransferFormDetail = $this->budgetTransferFormDetailRepository->findWithoutFail($id);

        if (empty($budgetTransferFormDetail)) {
            Flash::error('Budget Transfer Form Detail not found');

            return redirect(route('budgetTransferFormDetails.index'));
        }

        $this->budgetTransferFormDetailRepository->delete($id);

        Flash::success('Budget Transfer Form Detail deleted successfully.');

        return redirect(route('budgetTransferFormDetails.index'));
    }
}
