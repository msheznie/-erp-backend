<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateReportTemplateColumnLinkRequest;
use App\Http\Requests\UpdateReportTemplateColumnLinkRequest;
use App\Repositories\ReportTemplateColumnLinkRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class ReportTemplateColumnLinkController extends AppBaseController
{
    /** @var  ReportTemplateColumnLinkRepository */
    private $reportTemplateColumnLinkRepository;

    public function __construct(ReportTemplateColumnLinkRepository $reportTemplateColumnLinkRepo)
    {
        $this->reportTemplateColumnLinkRepository = $reportTemplateColumnLinkRepo;
    }

    /**
     * Display a listing of the ReportTemplateColumnLink.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->reportTemplateColumnLinkRepository->pushCriteria(new RequestCriteria($request));
        $reportTemplateColumnLinks = $this->reportTemplateColumnLinkRepository->all();

        return view('report_template_column_links.index')
            ->with('reportTemplateColumnLinks', $reportTemplateColumnLinks);
    }

    /**
     * Show the form for creating a new ReportTemplateColumnLink.
     *
     * @return Response
     */
    public function create()
    {
        return view('report_template_column_links.create');
    }

    /**
     * Store a newly created ReportTemplateColumnLink in storage.
     *
     * @param CreateReportTemplateColumnLinkRequest $request
     *
     * @return Response
     */
    public function store(CreateReportTemplateColumnLinkRequest $request)
    {
        $input = $request->all();

        $reportTemplateColumnLink = $this->reportTemplateColumnLinkRepository->create($input);

        Flash::success('Report Template Column Link saved successfully.');

        return redirect(route('reportTemplateColumnLinks.index'));
    }

    /**
     * Display the specified ReportTemplateColumnLink.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $reportTemplateColumnLink = $this->reportTemplateColumnLinkRepository->findWithoutFail($id);

        if (empty($reportTemplateColumnLink)) {
            Flash::error('Report Template Column Link not found');

            return redirect(route('reportTemplateColumnLinks.index'));
        }

        return view('report_template_column_links.show')->with('reportTemplateColumnLink', $reportTemplateColumnLink);
    }

    /**
     * Show the form for editing the specified ReportTemplateColumnLink.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $reportTemplateColumnLink = $this->reportTemplateColumnLinkRepository->findWithoutFail($id);

        if (empty($reportTemplateColumnLink)) {
            Flash::error('Report Template Column Link not found');

            return redirect(route('reportTemplateColumnLinks.index'));
        }

        return view('report_template_column_links.edit')->with('reportTemplateColumnLink', $reportTemplateColumnLink);
    }

    /**
     * Update the specified ReportTemplateColumnLink in storage.
     *
     * @param  int              $id
     * @param UpdateReportTemplateColumnLinkRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateReportTemplateColumnLinkRequest $request)
    {
        $reportTemplateColumnLink = $this->reportTemplateColumnLinkRepository->findWithoutFail($id);

        if (empty($reportTemplateColumnLink)) {
            Flash::error('Report Template Column Link not found');

            return redirect(route('reportTemplateColumnLinks.index'));
        }

        $reportTemplateColumnLink = $this->reportTemplateColumnLinkRepository->update($request->all(), $id);

        Flash::success('Report Template Column Link updated successfully.');

        return redirect(route('reportTemplateColumnLinks.index'));
    }

    /**
     * Remove the specified ReportTemplateColumnLink from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $reportTemplateColumnLink = $this->reportTemplateColumnLinkRepository->findWithoutFail($id);

        if (empty($reportTemplateColumnLink)) {
            Flash::error('Report Template Column Link not found');

            return redirect(route('reportTemplateColumnLinks.index'));
        }

        $this->reportTemplateColumnLinkRepository->delete($id);

        Flash::success('Report Template Column Link deleted successfully.');

        return redirect(route('reportTemplateColumnLinks.index'));
    }
}
