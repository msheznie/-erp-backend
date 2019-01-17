<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateReportTemplateFieldTypeRequest;
use App\Http\Requests\UpdateReportTemplateFieldTypeRequest;
use App\Repositories\ReportTemplateFieldTypeRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class ReportTemplateFieldTypeController extends AppBaseController
{
    /** @var  ReportTemplateFieldTypeRepository */
    private $reportTemplateFieldTypeRepository;

    public function __construct(ReportTemplateFieldTypeRepository $reportTemplateFieldTypeRepo)
    {
        $this->reportTemplateFieldTypeRepository = $reportTemplateFieldTypeRepo;
    }

    /**
     * Display a listing of the ReportTemplateFieldType.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->reportTemplateFieldTypeRepository->pushCriteria(new RequestCriteria($request));
        $reportTemplateFieldTypes = $this->reportTemplateFieldTypeRepository->all();

        return view('report_template_field_types.index')
            ->with('reportTemplateFieldTypes', $reportTemplateFieldTypes);
    }

    /**
     * Show the form for creating a new ReportTemplateFieldType.
     *
     * @return Response
     */
    public function create()
    {
        return view('report_template_field_types.create');
    }

    /**
     * Store a newly created ReportTemplateFieldType in storage.
     *
     * @param CreateReportTemplateFieldTypeRequest $request
     *
     * @return Response
     */
    public function store(CreateReportTemplateFieldTypeRequest $request)
    {
        $input = $request->all();

        $reportTemplateFieldType = $this->reportTemplateFieldTypeRepository->create($input);

        Flash::success('Report Template Field Type saved successfully.');

        return redirect(route('reportTemplateFieldTypes.index'));
    }

    /**
     * Display the specified ReportTemplateFieldType.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $reportTemplateFieldType = $this->reportTemplateFieldTypeRepository->findWithoutFail($id);

        if (empty($reportTemplateFieldType)) {
            Flash::error('Report Template Field Type not found');

            return redirect(route('reportTemplateFieldTypes.index'));
        }

        return view('report_template_field_types.show')->with('reportTemplateFieldType', $reportTemplateFieldType);
    }

    /**
     * Show the form for editing the specified ReportTemplateFieldType.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $reportTemplateFieldType = $this->reportTemplateFieldTypeRepository->findWithoutFail($id);

        if (empty($reportTemplateFieldType)) {
            Flash::error('Report Template Field Type not found');

            return redirect(route('reportTemplateFieldTypes.index'));
        }

        return view('report_template_field_types.edit')->with('reportTemplateFieldType', $reportTemplateFieldType);
    }

    /**
     * Update the specified ReportTemplateFieldType in storage.
     *
     * @param  int              $id
     * @param UpdateReportTemplateFieldTypeRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateReportTemplateFieldTypeRequest $request)
    {
        $reportTemplateFieldType = $this->reportTemplateFieldTypeRepository->findWithoutFail($id);

        if (empty($reportTemplateFieldType)) {
            Flash::error('Report Template Field Type not found');

            return redirect(route('reportTemplateFieldTypes.index'));
        }

        $reportTemplateFieldType = $this->reportTemplateFieldTypeRepository->update($request->all(), $id);

        Flash::success('Report Template Field Type updated successfully.');

        return redirect(route('reportTemplateFieldTypes.index'));
    }

    /**
     * Remove the specified ReportTemplateFieldType from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $reportTemplateFieldType = $this->reportTemplateFieldTypeRepository->findWithoutFail($id);

        if (empty($reportTemplateFieldType)) {
            Flash::error('Report Template Field Type not found');

            return redirect(route('reportTemplateFieldTypes.index'));
        }

        $this->reportTemplateFieldTypeRepository->delete($id);

        Flash::success('Report Template Field Type deleted successfully.');

        return redirect(route('reportTemplateFieldTypes.index'));
    }
}
