<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateReportColumnTemplateRequest;
use App\Http\Requests\UpdateReportColumnTemplateRequest;
use App\Repositories\ReportColumnTemplateRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class ReportColumnTemplateController extends AppBaseController
{
    /** @var  ReportColumnTemplateRepository */
    private $reportColumnTemplateRepository;

    public function __construct(ReportColumnTemplateRepository $reportColumnTemplateRepo)
    {
        $this->reportColumnTemplateRepository = $reportColumnTemplateRepo;
    }

    /**
     * Display a listing of the ReportColumnTemplate.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->reportColumnTemplateRepository->pushCriteria(new RequestCriteria($request));
        $reportColumnTemplates = $this->reportColumnTemplateRepository->all();

        return view('report_column_templates.index')
            ->with('reportColumnTemplates', $reportColumnTemplates);
    }

    /**
     * Show the form for creating a new ReportColumnTemplate.
     *
     * @return Response
     */
    public function create()
    {
        return view('report_column_templates.create');
    }

    /**
     * Store a newly created ReportColumnTemplate in storage.
     *
     * @param CreateReportColumnTemplateRequest $request
     *
     * @return Response
     */
    public function store(CreateReportColumnTemplateRequest $request)
    {
        $input = $request->all();

        $reportColumnTemplate = $this->reportColumnTemplateRepository->create($input);

        Flash::success('Report Column Template saved successfully.');

        return redirect(route('reportColumnTemplates.index'));
    }

    /**
     * Display the specified ReportColumnTemplate.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $reportColumnTemplate = $this->reportColumnTemplateRepository->findWithoutFail($id);

        if (empty($reportColumnTemplate)) {
            Flash::error('Report Column Template not found');

            return redirect(route('reportColumnTemplates.index'));
        }

        return view('report_column_templates.show')->with('reportColumnTemplate', $reportColumnTemplate);
    }

    /**
     * Show the form for editing the specified ReportColumnTemplate.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $reportColumnTemplate = $this->reportColumnTemplateRepository->findWithoutFail($id);

        if (empty($reportColumnTemplate)) {
            Flash::error('Report Column Template not found');

            return redirect(route('reportColumnTemplates.index'));
        }

        return view('report_column_templates.edit')->with('reportColumnTemplate', $reportColumnTemplate);
    }

    /**
     * Update the specified ReportColumnTemplate in storage.
     *
     * @param  int              $id
     * @param UpdateReportColumnTemplateRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateReportColumnTemplateRequest $request)
    {
        $reportColumnTemplate = $this->reportColumnTemplateRepository->findWithoutFail($id);

        if (empty($reportColumnTemplate)) {
            Flash::error('Report Column Template not found');

            return redirect(route('reportColumnTemplates.index'));
        }

        $reportColumnTemplate = $this->reportColumnTemplateRepository->update($request->all(), $id);

        Flash::success('Report Column Template updated successfully.');

        return redirect(route('reportColumnTemplates.index'));
    }

    /**
     * Remove the specified ReportColumnTemplate from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $reportColumnTemplate = $this->reportColumnTemplateRepository->findWithoutFail($id);

        if (empty($reportColumnTemplate)) {
            Flash::error('Report Column Template not found');

            return redirect(route('reportColumnTemplates.index'));
        }

        $this->reportColumnTemplateRepository->delete($id);

        Flash::success('Report Column Template deleted successfully.');

        return redirect(route('reportColumnTemplates.index'));
    }
}
