<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateReportTemplateRequest;
use App\Http\Requests\UpdateReportTemplateRequest;
use App\Repositories\ReportTemplateRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class ReportTemplateController extends AppBaseController
{
    /** @var  ReportTemplateRepository */
    private $reportTemplateRepository;

    public function __construct(ReportTemplateRepository $reportTemplateRepo)
    {
        $this->reportTemplateRepository = $reportTemplateRepo;
    }

    /**
     * Display a listing of the ReportTemplate.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->reportTemplateRepository->pushCriteria(new RequestCriteria($request));
        $reportTemplates = $this->reportTemplateRepository->all();

        return view('report_templates.index')
            ->with('reportTemplates', $reportTemplates);
    }

    /**
     * Show the form for creating a new ReportTemplate.
     *
     * @return Response
     */
    public function create()
    {
        return view('report_templates.create');
    }

    /**
     * Store a newly created ReportTemplate in storage.
     *
     * @param CreateReportTemplateRequest $request
     *
     * @return Response
     */
    public function store(CreateReportTemplateRequest $request)
    {
        $input = $request->all();

        $reportTemplate = $this->reportTemplateRepository->create($input);

        Flash::success('Report Template saved successfully.');

        return redirect(route('reportTemplates.index'));
    }

    /**
     * Display the specified ReportTemplate.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $reportTemplate = $this->reportTemplateRepository->findWithoutFail($id);

        if (empty($reportTemplate)) {
            Flash::error('Report Template not found');

            return redirect(route('reportTemplates.index'));
        }

        return view('report_templates.show')->with('reportTemplate', $reportTemplate);
    }

    /**
     * Show the form for editing the specified ReportTemplate.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $reportTemplate = $this->reportTemplateRepository->findWithoutFail($id);

        if (empty($reportTemplate)) {
            Flash::error('Report Template not found');

            return redirect(route('reportTemplates.index'));
        }

        return view('report_templates.edit')->with('reportTemplate', $reportTemplate);
    }

    /**
     * Update the specified ReportTemplate in storage.
     *
     * @param  int              $id
     * @param UpdateReportTemplateRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateReportTemplateRequest $request)
    {
        $reportTemplate = $this->reportTemplateRepository->findWithoutFail($id);

        if (empty($reportTemplate)) {
            Flash::error('Report Template not found');

            return redirect(route('reportTemplates.index'));
        }

        $reportTemplate = $this->reportTemplateRepository->update($request->all(), $id);

        Flash::success('Report Template updated successfully.');

        return redirect(route('reportTemplates.index'));
    }

    /**
     * Remove the specified ReportTemplate from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $reportTemplate = $this->reportTemplateRepository->findWithoutFail($id);

        if (empty($reportTemplate)) {
            Flash::error('Report Template not found');

            return redirect(route('reportTemplates.index'));
        }

        $this->reportTemplateRepository->delete($id);

        Flash::success('Report Template deleted successfully.');

        return redirect(route('reportTemplates.index'));
    }
}
