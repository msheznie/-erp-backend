<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateCompanyFinancePeriodRequest;
use App\Http\Requests\UpdateCompanyFinancePeriodRequest;
use App\Repositories\CompanyFinancePeriodRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class CompanyFinancePeriodController extends AppBaseController
{
    /** @var  CompanyFinancePeriodRepository */
    private $companyFinancePeriodRepository;

    public function __construct(CompanyFinancePeriodRepository $companyFinancePeriodRepo)
    {
        $this->companyFinancePeriodRepository = $companyFinancePeriodRepo;
    }

    /**
     * Display a listing of the CompanyFinancePeriod.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->companyFinancePeriodRepository->pushCriteria(new RequestCriteria($request));
        $companyFinancePeriods = $this->companyFinancePeriodRepository->all();

        return view('company_finance_periods.index')
            ->with('companyFinancePeriods', $companyFinancePeriods);
    }

    /**
     * Show the form for creating a new CompanyFinancePeriod.
     *
     * @return Response
     */
    public function create()
    {
        return view('company_finance_periods.create');
    }

    /**
     * Store a newly created CompanyFinancePeriod in storage.
     *
     * @param CreateCompanyFinancePeriodRequest $request
     *
     * @return Response
     */
    public function store(CreateCompanyFinancePeriodRequest $request)
    {
        $input = $request->all();

        $companyFinancePeriod = $this->companyFinancePeriodRepository->create($input);

        Flash::success('Company Finance Period saved successfully.');

        return redirect(route('companyFinancePeriods.index'));
    }

    /**
     * Display the specified CompanyFinancePeriod.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $companyFinancePeriod = $this->companyFinancePeriodRepository->findWithoutFail($id);

        if (empty($companyFinancePeriod)) {
            Flash::error('Company Finance Period not found');

            return redirect(route('companyFinancePeriods.index'));
        }

        return view('company_finance_periods.show')->with('companyFinancePeriod', $companyFinancePeriod);
    }

    /**
     * Show the form for editing the specified CompanyFinancePeriod.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $companyFinancePeriod = $this->companyFinancePeriodRepository->findWithoutFail($id);

        if (empty($companyFinancePeriod)) {
            Flash::error('Company Finance Period not found');

            return redirect(route('companyFinancePeriods.index'));
        }

        return view('company_finance_periods.edit')->with('companyFinancePeriod', $companyFinancePeriod);
    }

    /**
     * Update the specified CompanyFinancePeriod in storage.
     *
     * @param  int              $id
     * @param UpdateCompanyFinancePeriodRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateCompanyFinancePeriodRequest $request)
    {
        $companyFinancePeriod = $this->companyFinancePeriodRepository->findWithoutFail($id);

        if (empty($companyFinancePeriod)) {
            Flash::error('Company Finance Period not found');

            return redirect(route('companyFinancePeriods.index'));
        }

        $companyFinancePeriod = $this->companyFinancePeriodRepository->update($request->all(), $id);

        Flash::success('Company Finance Period updated successfully.');

        return redirect(route('companyFinancePeriods.index'));
    }

    /**
     * Remove the specified CompanyFinancePeriod from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $companyFinancePeriod = $this->companyFinancePeriodRepository->findWithoutFail($id);

        if (empty($companyFinancePeriod)) {
            Flash::error('Company Finance Period not found');

            return redirect(route('companyFinancePeriods.index'));
        }

        $this->companyFinancePeriodRepository->delete($id);

        Flash::success('Company Finance Period deleted successfully.');

        return redirect(route('companyFinancePeriods.index'));
    }
}
