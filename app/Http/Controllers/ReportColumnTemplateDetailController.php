<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateReportColumnTemplateDetailRequest;
use App\Http\Requests\UpdateReportColumnTemplateDetailRequest;
use App\Repositories\ReportColumnTemplateDetailRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class ReportColumnTemplateDetailController extends AppBaseController
{
    /** @var  ReportColumnTemplateDetailRepository */
    private $reportColumnTemplateDetailRepository;

    public function __construct(ReportColumnTemplateDetailRepository $reportColumnTemplateDetailRepo)
    {
        $this->reportColumnTemplateDetailRepository = $reportColumnTemplateDetailRepo;
    }

    /**
     * Display a listing of the ReportColumnTemplateDetail.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->reportColumnTemplateDetailRepository->pushCriteria(new RequestCriteria($request));
        $reportColumnTemplateDetails = $this->reportColumnTemplateDetailRepository->all();

        return view('report_column_template_details.index')
            ->with('reportColumnTemplateDetails', $reportColumnTemplateDetails);
    }

    /**
     * Show the form for creating a new ReportColumnTemplateDetail.
     *
     * @return Response
     */
    public function create()
    {
        return view('report_column_template_details.create');
    }

    /**
     * Store a newly created ReportColumnTemplateDetail in storage.
     *
     * @param CreateReportColumnTemplateDetailRequest $request
     *
     * @return Response
     */
    public function store(CreateReportColumnTemplateDetailRequest $request)
    {
        $input = $request->all();

        $reportColumnTemplateDetail = $this->reportColumnTemplateDetailRepository->create($input);

        Flash::success('Report Column Template Detail saved successfully.');

        return redirect(route('reportColumnTemplateDetails.index'));
    }

    /**
     * Display the specified ReportColumnTemplateDetail.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $reportColumnTemplateDetail = $this->reportColumnTemplateDetailRepository->findWithoutFail($id);

        if (empty($reportColumnTemplateDetail)) {
            Flash::error('Report Column Template Detail not found');

            return redirect(route('reportColumnTemplateDetails.index'));
        }

        return view('report_column_template_details.show')->with('reportColumnTemplateDetail', $reportColumnTemplateDetail);
    }

    /**
     * Show the form for editing the specified ReportColumnTemplateDetail.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $reportColumnTemplateDetail = $this->reportColumnTemplateDetailRepository->findWithoutFail($id);

        if (empty($reportColumnTemplateDetail)) {
            Flash::error('Report Column Template Detail not found');

            return redirect(route('reportColumnTemplateDetails.index'));
        }

        return view('report_column_template_details.edit')->with('reportColumnTemplateDetail', $reportColumnTemplateDetail);
    }

    /**
     * Update the specified ReportColumnTemplateDetail in storage.
     *
     * @param  int              $id
     * @param UpdateReportColumnTemplateDetailRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateReportColumnTemplateDetailRequest $request)
    {
        $reportColumnTemplateDetail = $this->reportColumnTemplateDetailRepository->findWithoutFail($id);

        if (empty($reportColumnTemplateDetail)) {
            Flash::error('Report Column Template Detail not found');

            return redirect(route('reportColumnTemplateDetails.index'));
        }

        $reportColumnTemplateDetail = $this->reportColumnTemplateDetailRepository->update($request->all(), $id);

        Flash::success('Report Column Template Detail updated successfully.');

        return redirect(route('reportColumnTemplateDetails.index'));
    }

    /**
     * Remove the specified ReportColumnTemplateDetail from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $reportColumnTemplateDetail = $this->reportColumnTemplateDetailRepository->findWithoutFail($id);

        if (empty($reportColumnTemplateDetail)) {
            Flash::error('Report Column Template Detail not found');

            return redirect(route('reportColumnTemplateDetails.index'));
        }

        $this->reportColumnTemplateDetailRepository->delete($id);

        Flash::success('Report Column Template Detail deleted successfully.');

        return redirect(route('reportColumnTemplateDetails.index'));
    }
}
