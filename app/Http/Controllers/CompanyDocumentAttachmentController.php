<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateCompanyDocumentAttachmentRequest;
use App\Http\Requests\UpdateCompanyDocumentAttachmentRequest;
use App\Repositories\CompanyDocumentAttachmentRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class CompanyDocumentAttachmentController extends AppBaseController
{
    /** @var  CompanyDocumentAttachmentRepository */
    private $companyDocumentAttachmentRepository;

    public function __construct(CompanyDocumentAttachmentRepository $companyDocumentAttachmentRepo)
    {
        $this->companyDocumentAttachmentRepository = $companyDocumentAttachmentRepo;
    }

    /**
     * Display a listing of the CompanyDocumentAttachment.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->companyDocumentAttachmentRepository->pushCriteria(new RequestCriteria($request));
        $companyDocumentAttachments = $this->companyDocumentAttachmentRepository->all();

        return view('company_document_attachments.index')
            ->with('companyDocumentAttachments', $companyDocumentAttachments);
    }

    /**
     * Show the form for creating a new CompanyDocumentAttachment.
     *
     * @return Response
     */
    public function create()
    {
        return view('company_document_attachments.create');
    }

    /**
     * Store a newly created CompanyDocumentAttachment in storage.
     *
     * @param CreateCompanyDocumentAttachmentRequest $request
     *
     * @return Response
     */
    public function store(CreateCompanyDocumentAttachmentRequest $request)
    {
        $input = $request->all();

        $companyDocumentAttachment = $this->companyDocumentAttachmentRepository->create($input);

        Flash::success('Company Document Attachment saved successfully.');

        return redirect(route('companyDocumentAttachments.index'));
    }

    /**
     * Display the specified CompanyDocumentAttachment.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $companyDocumentAttachment = $this->companyDocumentAttachmentRepository->findWithoutFail($id);

        if (empty($companyDocumentAttachment)) {
            Flash::error('Company Document Attachment not found');

            return redirect(route('companyDocumentAttachments.index'));
        }

        return view('company_document_attachments.show')->with('companyDocumentAttachment', $companyDocumentAttachment);
    }

    /**
     * Show the form for editing the specified CompanyDocumentAttachment.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $companyDocumentAttachment = $this->companyDocumentAttachmentRepository->findWithoutFail($id);

        if (empty($companyDocumentAttachment)) {
            Flash::error('Company Document Attachment not found');

            return redirect(route('companyDocumentAttachments.index'));
        }

        return view('company_document_attachments.edit')->with('companyDocumentAttachment', $companyDocumentAttachment);
    }

    /**
     * Update the specified CompanyDocumentAttachment in storage.
     *
     * @param  int              $id
     * @param UpdateCompanyDocumentAttachmentRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateCompanyDocumentAttachmentRequest $request)
    {
        $companyDocumentAttachment = $this->companyDocumentAttachmentRepository->findWithoutFail($id);

        if (empty($companyDocumentAttachment)) {
            Flash::error('Company Document Attachment not found');

            return redirect(route('companyDocumentAttachments.index'));
        }

        $companyDocumentAttachment = $this->companyDocumentAttachmentRepository->update($request->all(), $id);

        Flash::success('Company Document Attachment updated successfully.');

        return redirect(route('companyDocumentAttachments.index'));
    }

    /**
     * Remove the specified CompanyDocumentAttachment from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $companyDocumentAttachment = $this->companyDocumentAttachmentRepository->findWithoutFail($id);

        if (empty($companyDocumentAttachment)) {
            Flash::error('Company Document Attachment not found');

            return redirect(route('companyDocumentAttachments.index'));
        }

        $this->companyDocumentAttachmentRepository->delete($id);

        Flash::success('Company Document Attachment deleted successfully.');

        return redirect(route('companyDocumentAttachments.index'));
    }
}
