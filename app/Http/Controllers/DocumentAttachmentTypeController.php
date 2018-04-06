<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateDocumentAttachmentTypeRequest;
use App\Http\Requests\UpdateDocumentAttachmentTypeRequest;
use App\Repositories\DocumentAttachmentTypeRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class DocumentAttachmentTypeController extends AppBaseController
{
    /** @var  DocumentAttachmentTypeRepository */
    private $documentAttachmentTypeRepository;

    public function __construct(DocumentAttachmentTypeRepository $documentAttachmentTypeRepo)
    {
        $this->documentAttachmentTypeRepository = $documentAttachmentTypeRepo;
    }

    /**
     * Display a listing of the DocumentAttachmentType.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->documentAttachmentTypeRepository->pushCriteria(new RequestCriteria($request));
        $documentAttachmentTypes = $this->documentAttachmentTypeRepository->all();

        return view('document_attachment_types.index')
            ->with('documentAttachmentTypes', $documentAttachmentTypes);
    }

    /**
     * Show the form for creating a new DocumentAttachmentType.
     *
     * @return Response
     */
    public function create()
    {
        return view('document_attachment_types.create');
    }

    /**
     * Store a newly created DocumentAttachmentType in storage.
     *
     * @param CreateDocumentAttachmentTypeRequest $request
     *
     * @return Response
     */
    public function store(CreateDocumentAttachmentTypeRequest $request)
    {
        $input = $request->all();

        $documentAttachmentType = $this->documentAttachmentTypeRepository->create($input);

        Flash::success('Document Attachment Type saved successfully.');

        return redirect(route('documentAttachmentTypes.index'));
    }

    /**
     * Display the specified DocumentAttachmentType.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $documentAttachmentType = $this->documentAttachmentTypeRepository->findWithoutFail($id);

        if (empty($documentAttachmentType)) {
            Flash::error('Document Attachment Type not found');

            return redirect(route('documentAttachmentTypes.index'));
        }

        return view('document_attachment_types.show')->with('documentAttachmentType', $documentAttachmentType);
    }

    /**
     * Show the form for editing the specified DocumentAttachmentType.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $documentAttachmentType = $this->documentAttachmentTypeRepository->findWithoutFail($id);

        if (empty($documentAttachmentType)) {
            Flash::error('Document Attachment Type not found');

            return redirect(route('documentAttachmentTypes.index'));
        }

        return view('document_attachment_types.edit')->with('documentAttachmentType', $documentAttachmentType);
    }

    /**
     * Update the specified DocumentAttachmentType in storage.
     *
     * @param  int              $id
     * @param UpdateDocumentAttachmentTypeRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateDocumentAttachmentTypeRequest $request)
    {
        $documentAttachmentType = $this->documentAttachmentTypeRepository->findWithoutFail($id);

        if (empty($documentAttachmentType)) {
            Flash::error('Document Attachment Type not found');

            return redirect(route('documentAttachmentTypes.index'));
        }

        $documentAttachmentType = $this->documentAttachmentTypeRepository->update($request->all(), $id);

        Flash::success('Document Attachment Type updated successfully.');

        return redirect(route('documentAttachmentTypes.index'));
    }

    /**
     * Remove the specified DocumentAttachmentType from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $documentAttachmentType = $this->documentAttachmentTypeRepository->findWithoutFail($id);

        if (empty($documentAttachmentType)) {
            Flash::error('Document Attachment Type not found');

            return redirect(route('documentAttachmentTypes.index'));
        }

        $this->documentAttachmentTypeRepository->delete($id);

        Flash::success('Document Attachment Type deleted successfully.');

        return redirect(route('documentAttachmentTypes.index'));
    }
}
