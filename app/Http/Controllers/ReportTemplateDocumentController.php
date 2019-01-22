<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateReportTemplateDocumentRequest;
use App\Http\Requests\UpdateReportTemplateDocumentRequest;
use App\Repositories\ReportTemplateDocumentRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class ReportTemplateDocumentController extends AppBaseController
{
    /** @var  ReportTemplateDocumentRepository */
    private $reportTemplateDocumentRepository;

    public function __construct(ReportTemplateDocumentRepository $reportTemplateDocumentRepo)
    {
        $this->reportTemplateDocumentRepository = $reportTemplateDocumentRepo;
    }

    /**
     * Display a listing of the ReportTemplateDocument.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->reportTemplateDocumentRepository->pushCriteria(new RequestCriteria($request));
        $reportTemplateDocuments = $this->reportTemplateDocumentRepository->all();

        return view('report_template_documents.index')
            ->with('reportTemplateDocuments', $reportTemplateDocuments);
    }

    /**
     * Show the form for creating a new ReportTemplateDocument.
     *
     * @return Response
     */
    public function create()
    {
        return view('report_template_documents.create');
    }

    /**
     * Store a newly created ReportTemplateDocument in storage.
     *
     * @param CreateReportTemplateDocumentRequest $request
     *
     * @return Response
     */
    public function store(CreateReportTemplateDocumentRequest $request)
    {
        $input = $request->all();

        $reportTemplateDocument = $this->reportTemplateDocumentRepository->create($input);

        Flash::success('Report Template Document saved successfully.');

        return redirect(route('reportTemplateDocuments.index'));
    }

    /**
     * Display the specified ReportTemplateDocument.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $reportTemplateDocument = $this->reportTemplateDocumentRepository->findWithoutFail($id);

        if (empty($reportTemplateDocument)) {
            Flash::error('Report Template Document not found');

            return redirect(route('reportTemplateDocuments.index'));
        }

        return view('report_template_documents.show')->with('reportTemplateDocument', $reportTemplateDocument);
    }

    /**
     * Show the form for editing the specified ReportTemplateDocument.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $reportTemplateDocument = $this->reportTemplateDocumentRepository->findWithoutFail($id);

        if (empty($reportTemplateDocument)) {
            Flash::error('Report Template Document not found');

            return redirect(route('reportTemplateDocuments.index'));
        }

        return view('report_template_documents.edit')->with('reportTemplateDocument', $reportTemplateDocument);
    }

    /**
     * Update the specified ReportTemplateDocument in storage.
     *
     * @param  int              $id
     * @param UpdateReportTemplateDocumentRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateReportTemplateDocumentRequest $request)
    {
        $reportTemplateDocument = $this->reportTemplateDocumentRepository->findWithoutFail($id);

        if (empty($reportTemplateDocument)) {
            Flash::error('Report Template Document not found');

            return redirect(route('reportTemplateDocuments.index'));
        }

        $reportTemplateDocument = $this->reportTemplateDocumentRepository->update($request->all(), $id);

        Flash::success('Report Template Document updated successfully.');

        return redirect(route('reportTemplateDocuments.index'));
    }

    /**
     * Remove the specified ReportTemplateDocument from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $reportTemplateDocument = $this->reportTemplateDocumentRepository->findWithoutFail($id);

        if (empty($reportTemplateDocument)) {
            Flash::error('Report Template Document not found');

            return redirect(route('reportTemplateDocuments.index'));
        }

        $this->reportTemplateDocumentRepository->delete($id);

        Flash::success('Report Template Document deleted successfully.');

        return redirect(route('reportTemplateDocuments.index'));
    }
}
