<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateCompanyFinanceYearperiodMasterRequest;
use App\Http\Requests\UpdateCompanyFinanceYearperiodMasterRequest;
use App\Repositories\CompanyFinanceYearperiodMasterRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class CompanyFinanceYearperiodMasterController extends AppBaseController
{
    /** @var  CompanyFinanceYearperiodMasterRepository */
    private $companyFinanceYearperiodMasterRepository;

    public function __construct(CompanyFinanceYearperiodMasterRepository $companyFinanceYearperiodMasterRepo)
    {
        $this->companyFinanceYearperiodMasterRepository = $companyFinanceYearperiodMasterRepo;
    }

    /**
     * Display a listing of the CompanyFinanceYearperiodMaster.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->companyFinanceYearperiodMasterRepository->pushCriteria(new RequestCriteria($request));
        $companyFinanceYearperiodMasters = $this->companyFinanceYearperiodMasterRepository->all();

        return view('company_finance_yearperiod_masters.index')
            ->with('companyFinanceYearperiodMasters', $companyFinanceYearperiodMasters);
    }

    /**
     * Show the form for creating a new CompanyFinanceYearperiodMaster.
     *
     * @return Response
     */
    public function create()
    {
        return view('company_finance_yearperiod_masters.create');
    }

    /**
     * Store a newly created CompanyFinanceYearperiodMaster in storage.
     *
     * @param CreateCompanyFinanceYearperiodMasterRequest $request
     *
     * @return Response
     */
    public function store(CreateCompanyFinanceYearperiodMasterRequest $request)
    {
        $input = $request->all();

        $companyFinanceYearperiodMaster = $this->companyFinanceYearperiodMasterRepository->create($input);

        Flash::success('Company Finance Yearperiod Master saved successfully.');

        return redirect(route('companyFinanceYearperiodMasters.index'));
    }

    /**
     * Display the specified CompanyFinanceYearperiodMaster.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $companyFinanceYearperiodMaster = $this->companyFinanceYearperiodMasterRepository->findWithoutFail($id);

        if (empty($companyFinanceYearperiodMaster)) {
            Flash::error('Company Finance Yearperiod Master not found');

            return redirect(route('companyFinanceYearperiodMasters.index'));
        }

        return view('company_finance_yearperiod_masters.show')->with('companyFinanceYearperiodMaster', $companyFinanceYearperiodMaster);
    }

    /**
     * Show the form for editing the specified CompanyFinanceYearperiodMaster.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $companyFinanceYearperiodMaster = $this->companyFinanceYearperiodMasterRepository->findWithoutFail($id);

        if (empty($companyFinanceYearperiodMaster)) {
            Flash::error('Company Finance Yearperiod Master not found');

            return redirect(route('companyFinanceYearperiodMasters.index'));
        }

        return view('company_finance_yearperiod_masters.edit')->with('companyFinanceYearperiodMaster', $companyFinanceYearperiodMaster);
    }

    /**
     * Update the specified CompanyFinanceYearperiodMaster in storage.
     *
     * @param  int              $id
     * @param UpdateCompanyFinanceYearperiodMasterRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateCompanyFinanceYearperiodMasterRequest $request)
    {
        $companyFinanceYearperiodMaster = $this->companyFinanceYearperiodMasterRepository->findWithoutFail($id);

        if (empty($companyFinanceYearperiodMaster)) {
            Flash::error('Company Finance Yearperiod Master not found');

            return redirect(route('companyFinanceYearperiodMasters.index'));
        }

        $companyFinanceYearperiodMaster = $this->companyFinanceYearperiodMasterRepository->update($request->all(), $id);

        Flash::success('Company Finance Yearperiod Master updated successfully.');

        return redirect(route('companyFinanceYearperiodMasters.index'));
    }

    /**
     * Remove the specified CompanyFinanceYearperiodMaster from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $companyFinanceYearperiodMaster = $this->companyFinanceYearperiodMasterRepository->findWithoutFail($id);

        if (empty($companyFinanceYearperiodMaster)) {
            Flash::error('Company Finance Yearperiod Master not found');

            return redirect(route('companyFinanceYearperiodMasters.index'));
        }

        $this->companyFinanceYearperiodMasterRepository->delete($id);

        Flash::success('Company Finance Yearperiod Master deleted successfully.');

        return redirect(route('companyFinanceYearperiodMasters.index'));
    }
}
