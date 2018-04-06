<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateDocumentAttachmentsRequest;
use App\Http\Requests\UpdateDocumentAttachmentsRequest;
use App\Repositories\DocumentAttachmentsRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class DocumentAttachmentsController extends AppBaseController
{
    /** @var  DocumentAttachmentsRepository */
    private $documentAttachmentsRepository;

    public function __construct(DocumentAttachmentsRepository $documentAttachmentsRepo)
    {
        $this->documentAttachmentsRepository = $documentAttachmentsRepo;
    }

    /**
     * Display a listing of the DocumentAttachments.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->documentAttachmentsRepository->pushCriteria(new RequestCriteria($request));
        $documentAttachments = $this->documentAttachmentsRepository->all();

        return view('document_attachments.index')
            ->with('documentAttachments', $documentAttachments);
    }

    /**
     * Show the form for creating a new DocumentAttachments.
     *
     * @return Response
     */
    public function create()
    {
        return view('document_attachments.create');
    }

    /**
     * Store a newly created DocumentAttachments in storage.
     *
     * @param CreateDocumentAttachmentsRequest $request
     *
     * @return Response
     */
    public function store(CreateDocumentAttachmentsRequest $request)
    {
        $input = $request->all();

        $documentAttachments = $this->documentAttachmentsRepository->create($input);

        Flash::success('Document Attachments saved successfully.');

        return redirect(route('documentAttachments.index'));
    }

    /**
     * Display the specified DocumentAttachments.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $documentAttachments = $this->documentAttachmentsRepository->findWithoutFail($id);

        if (empty($documentAttachments)) {
            Flash::error('Document Attachments not found');

            return redirect(route('documentAttachments.index'));
        }

        return view('document_attachments.show')->with('documentAttachments', $documentAttachments);
    }

    /**
     * Show the form for editing the specified DocumentAttachments.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $documentAttachments = $this->documentAttachmentsRepository->findWithoutFail($id);

        if (empty($documentAttachments)) {
            Flash::error('Document Attachments not found');

            return redirect(route('documentAttachments.index'));
        }

        return view('document_attachments.edit')->with('documentAttachments', $documentAttachments);
    }

    /**
     * Update the specified DocumentAttachments in storage.
     *
     * @param  int              $id
     * @param UpdateDocumentAttachmentsRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateDocumentAttachmentsRequest $request)
    {
        $documentAttachments = $this->documentAttachmentsRepository->findWithoutFail($id);

        if (empty($documentAttachments)) {
            Flash::error('Document Attachments not found');

            return redirect(route('documentAttachments.index'));
        }

        $documentAttachments = $this->documentAttachmentsRepository->update($request->all(), $id);

        Flash::success('Document Attachments updated successfully.');

        return redirect(route('documentAttachments.index'));
    }

    /**
     * Remove the specified DocumentAttachments from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $documentAttachments = $this->documentAttachmentsRepository->findWithoutFail($id);

        if (empty($documentAttachments)) {
            Flash::error('Document Attachments not found');

            return redirect(route('documentAttachments.index'));
        }

        $this->documentAttachmentsRepository->delete($id);

        Flash::success('Document Attachments deleted successfully.');

        return redirect(route('documentAttachments.index'));
    }
}
