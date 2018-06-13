<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateCompanyFinanceYearRequest;
use App\Http\Requests\UpdateCompanyFinanceYearRequest;
use App\Repositories\CompanyFinanceYearRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class CompanyFinanceYearController extends AppBaseController
{
    /** @var  CompanyFinanceYearRepository */
    private $companyFinanceYearRepository;

    public function __construct(CompanyFinanceYearRepository $companyFinanceYearRepo)
    {
        $this->companyFinanceYearRepository = $companyFinanceYearRepo;
    }

    /**
     * Display a listing of the CompanyFinanceYear.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->companyFinanceYearRepository->pushCriteria(new RequestCriteria($request));
        $companyFinanceYears = $this->companyFinanceYearRepository->all();

        return view('company_finance_years.index')
            ->with('companyFinanceYears', $companyFinanceYears);
    }

    /**
     * Show the form for creating a new CompanyFinanceYear.
     *
     * @return Response
     */
    public function create()
    {
        return view('company_finance_years.create');
    }

    /**
     * Store a newly created CompanyFinanceYear in storage.
     *
     * @param CreateCompanyFinanceYearRequest $request
     *
     * @return Response
     */
    public function store(CreateCompanyFinanceYearRequest $request)
    {
        $input = $request->all();

        $companyFinanceYear = $this->companyFinanceYearRepository->create($input);

        Flash::success('Company Finance Year saved successfully.');

        return redirect(route('companyFinanceYears.index'));
    }

    /**
     * Display the specified CompanyFinanceYear.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $companyFinanceYear = $this->companyFinanceYearRepository->findWithoutFail($id);

        if (empty($companyFinanceYear)) {
            Flash::error('Company Finance Year not found');

            return redirect(route('companyFinanceYears.index'));
        }

        return view('company_finance_years.show')->with('companyFinanceYear', $companyFinanceYear);
    }

    /**
     * Show the form for editing the specified CompanyFinanceYear.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $companyFinanceYear = $this->companyFinanceYearRepository->findWithoutFail($id);

        if (empty($companyFinanceYear)) {
            Flash::error('Company Finance Year not found');

            return redirect(route('companyFinanceYears.index'));
        }

        return view('company_finance_years.edit')->with('companyFinanceYear', $companyFinanceYear);
    }

    /**
     * Update the specified CompanyFinanceYear in storage.
     *
     * @param  int              $id
     * @param UpdateCompanyFinanceYearRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateCompanyFinanceYearRequest $request)
    {
        $companyFinanceYear = $this->companyFinanceYearRepository->findWithoutFail($id);

        if (empty($companyFinanceYear)) {
            Flash::error('Company Finance Year not found');

            return redirect(route('companyFinanceYears.index'));
        }

        $companyFinanceYear = $this->companyFinanceYearRepository->update($request->all(), $id);

        Flash::success('Company Finance Year updated successfully.');

        return redirect(route('companyFinanceYears.index'));
    }

    /**
     * Remove the specified CompanyFinanceYear from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $companyFinanceYear = $this->companyFinanceYearRepository->findWithoutFail($id);

        if (empty($companyFinanceYear)) {
            Flash::error('Company Finance Year not found');

            return redirect(route('companyFinanceYears.index'));
        }

        $this->companyFinanceYearRepository->delete($id);

        Flash::success('Company Finance Year deleted successfully.');

        return redirect(route('companyFinanceYears.index'));
    }
}
