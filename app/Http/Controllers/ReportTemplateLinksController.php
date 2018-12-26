<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateReportTemplateLinksRequest;
use App\Http\Requests\UpdateReportTemplateLinksRequest;
use App\Repositories\ReportTemplateLinksRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class ReportTemplateLinksController extends AppBaseController
{
    /** @var  ReportTemplateLinksRepository */
    private $reportTemplateLinksRepository;

    public function __construct(ReportTemplateLinksRepository $reportTemplateLinksRepo)
    {
        $this->reportTemplateLinksRepository = $reportTemplateLinksRepo;
    }

    /**
     * Display a listing of the ReportTemplateLinks.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->reportTemplateLinksRepository->pushCriteria(new RequestCriteria($request));
        $reportTemplateLinks = $this->reportTemplateLinksRepository->all();

        return view('report_template_links.index')
            ->with('reportTemplateLinks', $reportTemplateLinks);
    }

    /**
     * Show the form for creating a new ReportTemplateLinks.
     *
     * @return Response
     */
    public function create()
    {
        return view('report_template_links.create');
    }

    /**
     * Store a newly created ReportTemplateLinks in storage.
     *
     * @param CreateReportTemplateLinksRequest $request
     *
     * @return Response
     */
    public function store(CreateReportTemplateLinksRequest $request)
    {
        $input = $request->all();

        $reportTemplateLinks = $this->reportTemplateLinksRepository->create($input);

        Flash::success('Report Template Links saved successfully.');

        return redirect(route('reportTemplateLinks.index'));
    }

    /**
     * Display the specified ReportTemplateLinks.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $reportTemplateLinks = $this->reportTemplateLinksRepository->findWithoutFail($id);

        if (empty($reportTemplateLinks)) {
            Flash::error('Report Template Links not found');

            return redirect(route('reportTemplateLinks.index'));
        }

        return view('report_template_links.show')->with('reportTemplateLinks', $reportTemplateLinks);
    }

    /**
     * Show the form for editing the specified ReportTemplateLinks.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $reportTemplateLinks = $this->reportTemplateLinksRepository->findWithoutFail($id);

        if (empty($reportTemplateLinks)) {
            Flash::error('Report Template Links not found');

            return redirect(route('reportTemplateLinks.index'));
        }

        return view('report_template_links.edit')->with('reportTemplateLinks', $reportTemplateLinks);
    }

    /**
     * Update the specified ReportTemplateLinks in storage.
     *
     * @param  int              $id
     * @param UpdateReportTemplateLinksRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateReportTemplateLinksRequest $request)
    {
        $reportTemplateLinks = $this->reportTemplateLinksRepository->findWithoutFail($id);

        if (empty($reportTemplateLinks)) {
            Flash::error('Report Template Links not found');

            return redirect(route('reportTemplateLinks.index'));
        }

        $reportTemplateLinks = $this->reportTemplateLinksRepository->update($request->all(), $id);

        Flash::success('Report Template Links updated successfully.');

        return redirect(route('reportTemplateLinks.index'));
    }

    /**
     * Remove the specified ReportTemplateLinks from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $reportTemplateLinks = $this->reportTemplateLinksRepository->findWithoutFail($id);

        if (empty($reportTemplateLinks)) {
            Flash::error('Report Template Links not found');

            return redirect(route('reportTemplateLinks.index'));
        }

        $this->reportTemplateLinksRepository->delete($id);

        Flash::success('Report Template Links deleted successfully.');

        return redirect(route('reportTemplateLinks.index'));
    }
}
