<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateReportTemplateDetailsRequest;
use App\Http\Requests\UpdateReportTemplateDetailsRequest;
use App\Repositories\ReportTemplateDetailsRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class ReportTemplateDetailsController extends AppBaseController
{
    /** @var  ReportTemplateDetailsRepository */
    private $reportTemplateDetailsRepository;

    public function __construct(ReportTemplateDetailsRepository $reportTemplateDetailsRepo)
    {
        $this->reportTemplateDetailsRepository = $reportTemplateDetailsRepo;
    }

    /**
     * Display a listing of the ReportTemplateDetails.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->reportTemplateDetailsRepository->pushCriteria(new RequestCriteria($request));
        $reportTemplateDetails = $this->reportTemplateDetailsRepository->all();

        return view('report_template_details.index')
            ->with('reportTemplateDetails', $reportTemplateDetails);
    }

    /**
     * Show the form for creating a new ReportTemplateDetails.
     *
     * @return Response
     */
    public function create()
    {
        return view('report_template_details.create');
    }

    /**
     * Store a newly created ReportTemplateDetails in storage.
     *
     * @param CreateReportTemplateDetailsRequest $request
     *
     * @return Response
     */
    public function store(CreateReportTemplateDetailsRequest $request)
    {
        $input = $request->all();

        $reportTemplateDetails = $this->reportTemplateDetailsRepository->create($input);

        Flash::success('Report Template Details saved successfully.');

        return redirect(route('reportTemplateDetails.index'));
    }

    /**
     * Display the specified ReportTemplateDetails.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $reportTemplateDetails = $this->reportTemplateDetailsRepository->findWithoutFail($id);

        if (empty($reportTemplateDetails)) {
            Flash::error('Report Template Details not found');

            return redirect(route('reportTemplateDetails.index'));
        }

        return view('report_template_details.show')->with('reportTemplateDetails', $reportTemplateDetails);
    }

    /**
     * Show the form for editing the specified ReportTemplateDetails.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $reportTemplateDetails = $this->reportTemplateDetailsRepository->findWithoutFail($id);

        if (empty($reportTemplateDetails)) {
            Flash::error('Report Template Details not found');

            return redirect(route('reportTemplateDetails.index'));
        }

        return view('report_template_details.edit')->with('reportTemplateDetails', $reportTemplateDetails);
    }

    /**
     * Update the specified ReportTemplateDetails in storage.
     *
     * @param  int              $id
     * @param UpdateReportTemplateDetailsRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateReportTemplateDetailsRequest $request)
    {
        $reportTemplateDetails = $this->reportTemplateDetailsRepository->findWithoutFail($id);

        if (empty($reportTemplateDetails)) {
            Flash::error('Report Template Details not found');

            return redirect(route('reportTemplateDetails.index'));
        }

        $reportTemplateDetails = $this->reportTemplateDetailsRepository->update($request->all(), $id);

        Flash::success('Report Template Details updated successfully.');

        return redirect(route('reportTemplateDetails.index'));
    }

    /**
     * Remove the specified ReportTemplateDetails from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $reportTemplateDetails = $this->reportTemplateDetailsRepository->findWithoutFail($id);

        if (empty($reportTemplateDetails)) {
            Flash::error('Report Template Details not found');

            return redirect(route('reportTemplateDetails.index'));
        }

        $this->reportTemplateDetailsRepository->delete($id);

        Flash::success('Report Template Details deleted successfully.');

        return redirect(route('reportTemplateDetails.index'));
    }
}
