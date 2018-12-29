<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateReportTemplateColumnsRequest;
use App\Http\Requests\UpdateReportTemplateColumnsRequest;
use App\Repositories\ReportTemplateColumnsRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class ReportTemplateColumnsController extends AppBaseController
{
    /** @var  ReportTemplateColumnsRepository */
    private $reportTemplateColumnsRepository;

    public function __construct(ReportTemplateColumnsRepository $reportTemplateColumnsRepo)
    {
        $this->reportTemplateColumnsRepository = $reportTemplateColumnsRepo;
    }

    /**
     * Display a listing of the ReportTemplateColumns.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->reportTemplateColumnsRepository->pushCriteria(new RequestCriteria($request));
        $reportTemplateColumns = $this->reportTemplateColumnsRepository->all();

        return view('report_template_columns.index')
            ->with('reportTemplateColumns', $reportTemplateColumns);
    }

    /**
     * Show the form for creating a new ReportTemplateColumns.
     *
     * @return Response
     */
    public function create()
    {
        return view('report_template_columns.create');
    }

    /**
     * Store a newly created ReportTemplateColumns in storage.
     *
     * @param CreateReportTemplateColumnsRequest $request
     *
     * @return Response
     */
    public function store(CreateReportTemplateColumnsRequest $request)
    {
        $input = $request->all();

        $reportTemplateColumns = $this->reportTemplateColumnsRepository->create($input);

        Flash::success('Report Template Columns saved successfully.');

        return redirect(route('reportTemplateColumns.index'));
    }

    /**
     * Display the specified ReportTemplateColumns.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $reportTemplateColumns = $this->reportTemplateColumnsRepository->findWithoutFail($id);

        if (empty($reportTemplateColumns)) {
            Flash::error('Report Template Columns not found');

            return redirect(route('reportTemplateColumns.index'));
        }

        return view('report_template_columns.show')->with('reportTemplateColumns', $reportTemplateColumns);
    }

    /**
     * Show the form for editing the specified ReportTemplateColumns.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $reportTemplateColumns = $this->reportTemplateColumnsRepository->findWithoutFail($id);

        if (empty($reportTemplateColumns)) {
            Flash::error('Report Template Columns not found');

            return redirect(route('reportTemplateColumns.index'));
        }

        return view('report_template_columns.edit')->with('reportTemplateColumns', $reportTemplateColumns);
    }

    /**
     * Update the specified ReportTemplateColumns in storage.
     *
     * @param  int              $id
     * @param UpdateReportTemplateColumnsRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateReportTemplateColumnsRequest $request)
    {
        $reportTemplateColumns = $this->reportTemplateColumnsRepository->findWithoutFail($id);

        if (empty($reportTemplateColumns)) {
            Flash::error('Report Template Columns not found');

            return redirect(route('reportTemplateColumns.index'));
        }

        $reportTemplateColumns = $this->reportTemplateColumnsRepository->update($request->all(), $id);

        Flash::success('Report Template Columns updated successfully.');

        return redirect(route('reportTemplateColumns.index'));
    }

    /**
     * Remove the specified ReportTemplateColumns from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $reportTemplateColumns = $this->reportTemplateColumnsRepository->findWithoutFail($id);

        if (empty($reportTemplateColumns)) {
            Flash::error('Report Template Columns not found');

            return redirect(route('reportTemplateColumns.index'));
        }

        $this->reportTemplateColumnsRepository->delete($id);

        Flash::success('Report Template Columns deleted successfully.');

        return redirect(route('reportTemplateColumns.index'));
    }
}
