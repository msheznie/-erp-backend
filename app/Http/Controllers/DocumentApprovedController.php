<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateDocumentApprovedRequest;
use App\Http\Requests\UpdateDocumentApprovedRequest;
use App\Repositories\DocumentApprovedRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class DocumentApprovedController extends AppBaseController
{
    /** @var  DocumentApprovedRepository */
    private $documentApprovedRepository;

    public function __construct(DocumentApprovedRepository $documentApprovedRepo)
    {
        $this->documentApprovedRepository = $documentApprovedRepo;
    }

    /**
     * Display a listing of the DocumentApproved.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->documentApprovedRepository->pushCriteria(new RequestCriteria($request));
        $documentApproveds = $this->documentApprovedRepository->all();

        return view('document_approveds.index')
            ->with('documentApproveds', $documentApproveds);
    }

    /**
     * Show the form for creating a new DocumentApproved.
     *
     * @return Response
     */
    public function create()
    {
        return view('document_approveds.create');
    }

    /**
     * Store a newly created DocumentApproved in storage.
     *
     * @param CreateDocumentApprovedRequest $request
     *
     * @return Response
     */
    public function store(CreateDocumentApprovedRequest $request)
    {
        $input = $request->all();

        $documentApproved = $this->documentApprovedRepository->create($input);

        Flash::success('Document Approved saved successfully.');

        return redirect(route('documentApproveds.index'));
    }

    /**
     * Display the specified DocumentApproved.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $documentApproved = $this->documentApprovedRepository->findWithoutFail($id);

        if (empty($documentApproved)) {
            Flash::error('Document Approved not found');

            return redirect(route('documentApproveds.index'));
        }

        return view('document_approveds.show')->with('documentApproved', $documentApproved);
    }

    /**
     * Show the form for editing the specified DocumentApproved.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $documentApproved = $this->documentApprovedRepository->findWithoutFail($id);

        if (empty($documentApproved)) {
            Flash::error('Document Approved not found');

            return redirect(route('documentApproveds.index'));
        }

        return view('document_approveds.edit')->with('documentApproved', $documentApproved);
    }

    /**
     * Update the specified DocumentApproved in storage.
     *
     * @param  int              $id
     * @param UpdateDocumentApprovedRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateDocumentApprovedRequest $request)
    {
        $documentApproved = $this->documentApprovedRepository->findWithoutFail($id);

        if (empty($documentApproved)) {
            Flash::error('Document Approved not found');

            return redirect(route('documentApproveds.index'));
        }

        $documentApproved = $this->documentApprovedRepository->update($request->all(), $id);

        Flash::success('Document Approved updated successfully.');

        return redirect(route('documentApproveds.index'));
    }

    /**
     * Remove the specified DocumentApproved from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $documentApproved = $this->documentApprovedRepository->findWithoutFail($id);

        if (empty($documentApproved)) {
            Flash::error('Document Approved not found');

            return redirect(route('documentApproveds.index'));
        }

        $this->documentApprovedRepository->delete($id);

        Flash::success('Document Approved deleted successfully.');

        return redirect(route('documentApproveds.index'));
    }
}
