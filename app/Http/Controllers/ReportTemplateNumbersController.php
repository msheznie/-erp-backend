<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateReportTemplateNumbersRequest;
use App\Http\Requests\UpdateReportTemplateNumbersRequest;
use App\Repositories\ReportTemplateNumbersRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class ReportTemplateNumbersController extends AppBaseController
{
    /** @var  ReportTemplateNumbersRepository */
    private $reportTemplateNumbersRepository;

    public function __construct(ReportTemplateNumbersRepository $reportTemplateNumbersRepo)
    {
        $this->reportTemplateNumbersRepository = $reportTemplateNumbersRepo;
    }

    /**
     * Display a listing of the ReportTemplateNumbers.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->reportTemplateNumbersRepository->pushCriteria(new RequestCriteria($request));
        $reportTemplateNumbers = $this->reportTemplateNumbersRepository->all();

        return view('report_template_numbers.index')
            ->with('reportTemplateNumbers', $reportTemplateNumbers);
    }

    /**
     * Show the form for creating a new ReportTemplateNumbers.
     *
     * @return Response
     */
    public function create()
    {
        return view('report_template_numbers.create');
    }

    /**
     * Store a newly created ReportTemplateNumbers in storage.
     *
     * @param CreateReportTemplateNumbersRequest $request
     *
     * @return Response
     */
    public function store(CreateReportTemplateNumbersRequest $request)
    {
        $input = $request->all();

        $reportTemplateNumbers = $this->reportTemplateNumbersRepository->create($input);

        Flash::success('Report Template Numbers saved successfully.');

        return redirect(route('reportTemplateNumbers.index'));
    }

    /**
     * Display the specified ReportTemplateNumbers.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $reportTemplateNumbers = $this->reportTemplateNumbersRepository->findWithoutFail($id);

        if (empty($reportTemplateNumbers)) {
            Flash::error('Report Template Numbers not found');

            return redirect(route('reportTemplateNumbers.index'));
        }

        return view('report_template_numbers.show')->with('reportTemplateNumbers', $reportTemplateNumbers);
    }

    /**
     * Show the form for editing the specified ReportTemplateNumbers.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $reportTemplateNumbers = $this->reportTemplateNumbersRepository->findWithoutFail($id);

        if (empty($reportTemplateNumbers)) {
            Flash::error('Report Template Numbers not found');

            return redirect(route('reportTemplateNumbers.index'));
        }

        return view('report_template_numbers.edit')->with('reportTemplateNumbers', $reportTemplateNumbers);
    }

    /**
     * Update the specified ReportTemplateNumbers in storage.
     *
     * @param  int              $id
     * @param UpdateReportTemplateNumbersRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateReportTemplateNumbersRequest $request)
    {
        $reportTemplateNumbers = $this->reportTemplateNumbersRepository->findWithoutFail($id);

        if (empty($reportTemplateNumbers)) {
            Flash::error('Report Template Numbers not found');

            return redirect(route('reportTemplateNumbers.index'));
        }

        $reportTemplateNumbers = $this->reportTemplateNumbersRepository->update($request->all(), $id);

        Flash::success('Report Template Numbers updated successfully.');

        return redirect(route('reportTemplateNumbers.index'));
    }

    /**
     * Remove the specified ReportTemplateNumbers from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $reportTemplateNumbers = $this->reportTemplateNumbersRepository->findWithoutFail($id);

        if (empty($reportTemplateNumbers)) {
            Flash::error('Report Template Numbers not found');

            return redirect(route('reportTemplateNumbers.index'));
        }

        $this->reportTemplateNumbersRepository->delete($id);

        Flash::success('Report Template Numbers deleted successfully.');

        return redirect(route('reportTemplateNumbers.index'));
    }
}
