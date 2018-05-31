<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateBudgetConsumedDataRequest;
use App\Http\Requests\UpdateBudgetConsumedDataRequest;
use App\Repositories\BudgetConsumedDataRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class BudgetConsumedDataController extends AppBaseController
{
    /** @var  BudgetConsumedDataRepository */
    private $budgetConsumedDataRepository;

    public function __construct(BudgetConsumedDataRepository $budgetConsumedDataRepo)
    {
        $this->budgetConsumedDataRepository = $budgetConsumedDataRepo;
    }

    /**
     * Display a listing of the BudgetConsumedData.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->budgetConsumedDataRepository->pushCriteria(new RequestCriteria($request));
        $budgetConsumedDatas = $this->budgetConsumedDataRepository->all();

        return view('budget_consumed_datas.index')
            ->with('budgetConsumedDatas', $budgetConsumedDatas);
    }

    /**
     * Show the form for creating a new BudgetConsumedData.
     *
     * @return Response
     */
    public function create()
    {
        return view('budget_consumed_datas.create');
    }

    /**
     * Store a newly created BudgetConsumedData in storage.
     *
     * @param CreateBudgetConsumedDataRequest $request
     *
     * @return Response
     */
    public function store(CreateBudgetConsumedDataRequest $request)
    {
        $input = $request->all();

        $budgetConsumedData = $this->budgetConsumedDataRepository->create($input);

        Flash::success('Budget Consumed Data saved successfully.');

        return redirect(route('budgetConsumedDatas.index'));
    }

    /**
     * Display the specified BudgetConsumedData.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $budgetConsumedData = $this->budgetConsumedDataRepository->findWithoutFail($id);

        if (empty($budgetConsumedData)) {
            Flash::error('Budget Consumed Data not found');

            return redirect(route('budgetConsumedDatas.index'));
        }

        return view('budget_consumed_datas.show')->with('budgetConsumedData', $budgetConsumedData);
    }

    /**
     * Show the form for editing the specified BudgetConsumedData.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $budgetConsumedData = $this->budgetConsumedDataRepository->findWithoutFail($id);

        if (empty($budgetConsumedData)) {
            Flash::error('Budget Consumed Data not found');

            return redirect(route('budgetConsumedDatas.index'));
        }

        return view('budget_consumed_datas.edit')->with('budgetConsumedData', $budgetConsumedData);
    }

    /**
     * Update the specified BudgetConsumedData in storage.
     *
     * @param  int              $id
     * @param UpdateBudgetConsumedDataRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateBudgetConsumedDataRequest $request)
    {
        $budgetConsumedData = $this->budgetConsumedDataRepository->findWithoutFail($id);

        if (empty($budgetConsumedData)) {
            Flash::error('Budget Consumed Data not found');

            return redirect(route('budgetConsumedDatas.index'));
        }

        $budgetConsumedData = $this->budgetConsumedDataRepository->update($request->all(), $id);

        Flash::success('Budget Consumed Data updated successfully.');

        return redirect(route('budgetConsumedDatas.index'));
    }

    /**
     * Remove the specified BudgetConsumedData from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $budgetConsumedData = $this->budgetConsumedDataRepository->findWithoutFail($id);

        if (empty($budgetConsumedData)) {
            Flash::error('Budget Consumed Data not found');

            return redirect(route('budgetConsumedDatas.index'));
        }

        $this->budgetConsumedDataRepository->delete($id);

        Flash::success('Budget Consumed Data deleted successfully.');

        return redirect(route('budgetConsumedDatas.index'));
    }
}
