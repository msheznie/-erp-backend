<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateReportTemplateEmployeesRequest;
use App\Http\Requests\UpdateReportTemplateEmployeesRequest;
use App\Repositories\ReportTemplateEmployeesRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class ReportTemplateEmployeesController extends AppBaseController
{
    /** @var  ReportTemplateEmployeesRepository */
    private $reportTemplateEmployeesRepository;

    public function __construct(ReportTemplateEmployeesRepository $reportTemplateEmployeesRepo)
    {
        $this->reportTemplateEmployeesRepository = $reportTemplateEmployeesRepo;
    }

    /**
     * Display a listing of the ReportTemplateEmployees.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->reportTemplateEmployeesRepository->pushCriteria(new RequestCriteria($request));
        $reportTemplateEmployees = $this->reportTemplateEmployeesRepository->all();

        return view('report_template_employees.index')
            ->with('reportTemplateEmployees', $reportTemplateEmployees);
    }

    /**
     * Show the form for creating a new ReportTemplateEmployees.
     *
     * @return Response
     */
    public function create()
    {
        return view('report_template_employees.create');
    }

    /**
     * Store a newly created ReportTemplateEmployees in storage.
     *
     * @param CreateReportTemplateEmployeesRequest $request
     *
     * @return Response
     */
    public function store(CreateReportTemplateEmployeesRequest $request)
    {
        $input = $request->all();

        $reportTemplateEmployees = $this->reportTemplateEmployeesRepository->create($input);

        Flash::success('Report Template Employees saved successfully.');

        return redirect(route('reportTemplateEmployees.index'));
    }

    /**
     * Display the specified ReportTemplateEmployees.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $reportTemplateEmployees = $this->reportTemplateEmployeesRepository->findWithoutFail($id);

        if (empty($reportTemplateEmployees)) {
            Flash::error('Report Template Employees not found');

            return redirect(route('reportTemplateEmployees.index'));
        }

        return view('report_template_employees.show')->with('reportTemplateEmployees', $reportTemplateEmployees);
    }

    /**
     * Show the form for editing the specified ReportTemplateEmployees.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $reportTemplateEmployees = $this->reportTemplateEmployeesRepository->findWithoutFail($id);

        if (empty($reportTemplateEmployees)) {
            Flash::error('Report Template Employees not found');

            return redirect(route('reportTemplateEmployees.index'));
        }

        return view('report_template_employees.edit')->with('reportTemplateEmployees', $reportTemplateEmployees);
    }

    /**
     * Update the specified ReportTemplateEmployees in storage.
     *
     * @param  int              $id
     * @param UpdateReportTemplateEmployeesRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateReportTemplateEmployeesRequest $request)
    {
        $reportTemplateEmployees = $this->reportTemplateEmployeesRepository->findWithoutFail($id);

        if (empty($reportTemplateEmployees)) {
            Flash::error('Report Template Employees not found');

            return redirect(route('reportTemplateEmployees.index'));
        }

        $reportTemplateEmployees = $this->reportTemplateEmployeesRepository->update($request->all(), $id);

        Flash::success('Report Template Employees updated successfully.');

        return redirect(route('reportTemplateEmployees.index'));
    }

    /**
     * Remove the specified ReportTemplateEmployees from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $reportTemplateEmployees = $this->reportTemplateEmployeesRepository->findWithoutFail($id);

        if (empty($reportTemplateEmployees)) {
            Flash::error('Report Template Employees not found');

            return redirect(route('reportTemplateEmployees.index'));
        }

        $this->reportTemplateEmployeesRepository->delete($id);

        Flash::success('Report Template Employees deleted successfully.');

        return redirect(route('reportTemplateEmployees.index'));
    }
}
