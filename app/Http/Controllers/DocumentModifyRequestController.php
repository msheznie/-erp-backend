<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateDocumentModifyRequestRequest;
use App\Http\Requests\UpdateDocumentModifyRequestRequest;
use App\Repositories\DocumentModifyRequestRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class DocumentModifyRequestController extends AppBaseController
{
    /** @var  DocumentModifyRequestRepository */
    private $documentModifyRequestRepository;

    public function __construct(DocumentModifyRequestRepository $documentModifyRequestRepo)
    {
        $this->documentModifyRequestRepository = $documentModifyRequestRepo;
    }

    /**
     * Display a listing of the DocumentModifyRequest.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->documentModifyRequestRepository->pushCriteria(new RequestCriteria($request));
        $documentModifyRequests = $this->documentModifyRequestRepository->all();

        return view('document_modify_requests.index')
            ->with('documentModifyRequests', $documentModifyRequests);
    }

    /**
     * Show the form for creating a new DocumentModifyRequest.
     *
     * @return Response
     */
    public function create()
    {
        return view('document_modify_requests.create');
    }

    /**
     * Store a newly created DocumentModifyRequest in storage.
     *
     * @param CreateDocumentModifyRequestRequest $request
     *
     * @return Response
     */
    public function store(CreateDocumentModifyRequestRequest $request)
    {
        $input = $request->all();

        $documentModifyRequest = $this->documentModifyRequestRepository->create($input);

        Flash::success('Document Modify Request saved successfully.');

        return redirect(route('documentModifyRequests.index'));
    }

    /**
     * Display the specified DocumentModifyRequest.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $documentModifyRequest = $this->documentModifyRequestRepository->findWithoutFail($id);

        if (empty($documentModifyRequest)) {
            Flash::error('Document Modify Request not found');

            return redirect(route('documentModifyRequests.index'));
        }

        return view('document_modify_requests.show')->with('documentModifyRequest', $documentModifyRequest);
    }

    /**
     * Show the form for editing the specified DocumentModifyRequest.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $documentModifyRequest = $this->documentModifyRequestRepository->findWithoutFail($id);

        if (empty($documentModifyRequest)) {
            Flash::error('Document Modify Request not found');

            return redirect(route('documentModifyRequests.index'));
        }

        return view('document_modify_requests.edit')->with('documentModifyRequest', $documentModifyRequest);
    }

    /**
     * Update the specified DocumentModifyRequest in storage.
     *
     * @param  int              $id
     * @param UpdateDocumentModifyRequestRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateDocumentModifyRequestRequest $request)
    {
        $documentModifyRequest = $this->documentModifyRequestRepository->findWithoutFail($id);

        if (empty($documentModifyRequest)) {
            Flash::error('Document Modify Request not found');

            return redirect(route('documentModifyRequests.index'));
        }

        $documentModifyRequest = $this->documentModifyRequestRepository->update($request->all(), $id);

        Flash::success('Document Modify Request updated successfully.');

        return redirect(route('documentModifyRequests.index'));
    }

    /**
     * Remove the specified DocumentModifyRequest from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $documentModifyRequest = $this->documentModifyRequestRepository->findWithoutFail($id);

        if (empty($documentModifyRequest)) {
            Flash::error('Document Modify Request not found');

            return redirect(route('documentModifyRequests.index'));
        }

        $this->documentModifyRequestRepository->delete($id);

        Flash::success('Document Modify Request deleted successfully.');

        return redirect(route('documentModifyRequests.index'));
    }
}
