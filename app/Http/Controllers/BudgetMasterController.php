<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateBudgetMasterRequest;
use App\Http\Requests\UpdateBudgetMasterRequest;
use App\Repositories\BudgetMasterRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class BudgetMasterController extends AppBaseController
{
    /** @var  BudgetMasterRepository */
    private $budgetMasterRepository;

    public function __construct(BudgetMasterRepository $budgetMasterRepo)
    {
        $this->budgetMasterRepository = $budgetMasterRepo;
    }

    /**
     * Display a listing of the BudgetMaster.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->budgetMasterRepository->pushCriteria(new RequestCriteria($request));
        $budgetMasters = $this->budgetMasterRepository->all();

        return view('budget_masters.index')
            ->with('budgetMasters', $budgetMasters);
    }

    /**
     * Show the form for creating a new BudgetMaster.
     *
     * @return Response
     */
    public function create()
    {
        return view('budget_masters.create');
    }

    /**
     * Store a newly created BudgetMaster in storage.
     *
     * @param CreateBudgetMasterRequest $request
     *
     * @return Response
     */
    public function store(CreateBudgetMasterRequest $request)
    {
        $input = $request->all();

        $budgetMaster = $this->budgetMasterRepository->create($input);

        Flash::success('Budget Master saved successfully.');

        return redirect(route('budgetMasters.index'));
    }

    /**
     * Display the specified BudgetMaster.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $budgetMaster = $this->budgetMasterRepository->findWithoutFail($id);

        if (empty($budgetMaster)) {
            Flash::error('Budget Master not found');

            return redirect(route('budgetMasters.index'));
        }

        return view('budget_masters.show')->with('budgetMaster', $budgetMaster);
    }

    /**
     * Show the form for editing the specified BudgetMaster.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $budgetMaster = $this->budgetMasterRepository->findWithoutFail($id);

        if (empty($budgetMaster)) {
            Flash::error('Budget Master not found');

            return redirect(route('budgetMasters.index'));
        }

        return view('budget_masters.edit')->with('budgetMaster', $budgetMaster);
    }

    /**
     * Update the specified BudgetMaster in storage.
     *
     * @param  int              $id
     * @param UpdateBudgetMasterRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateBudgetMasterRequest $request)
    {
        $budgetMaster = $this->budgetMasterRepository->findWithoutFail($id);

        if (empty($budgetMaster)) {
            Flash::error('Budget Master not found');

            return redirect(route('budgetMasters.index'));
        }

        $budgetMaster = $this->budgetMasterRepository->update($request->all(), $id);

        Flash::success('Budget Master updated successfully.');

        return redirect(route('budgetMasters.index'));
    }

    /**
     * Remove the specified BudgetMaster from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $budgetMaster = $this->budgetMasterRepository->findWithoutFail($id);

        if (empty($budgetMaster)) {
            Flash::error('Budget Master not found');

            return redirect(route('budgetMasters.index'));
        }

        $this->budgetMasterRepository->delete($id);

        Flash::success('Budget Master deleted successfully.');

        return redirect(route('budgetMasters.index'));
    }
}
