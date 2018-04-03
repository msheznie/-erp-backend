<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateDocumentApprovedAPIRequest;
use App\Http\Requests\API\UpdateDocumentApprovedAPIRequest;
use App\Models\DocumentApproved;
use App\Repositories\DocumentApprovedRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class DocumentApprovedController
 * @package App\Http\Controllers\API
 */

class DocumentApprovedAPIController extends AppBaseController
{
    /** @var  DocumentApprovedRepository */
    private $documentApprovedRepository;

    public function __construct(DocumentApprovedRepository $documentApprovedRepo)
    {
        $this->documentApprovedRepository = $documentApprovedRepo;
    }

    /**
     * Display a listing of the DocumentApproved.
     * GET|HEAD /documentApproveds
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->documentApprovedRepository->pushCriteria(new RequestCriteria($request));
        $this->documentApprovedRepository->pushCriteria(new LimitOffsetCriteria($request));
        $documentApproveds = $this->documentApprovedRepository->all();

        return $this->sendResponse($documentApproveds->toArray(), 'Document Approveds retrieved successfully');
    }

    /**
     * Store a newly created DocumentApproved in storage.
     * POST /documentApproveds
     *
     * @param CreateDocumentApprovedAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateDocumentApprovedAPIRequest $request)
    {
        $input = $request->all();

        $documentApproveds = $this->documentApprovedRepository->create($input);

        return $this->sendResponse($documentApproveds->toArray(), 'Document Approved saved successfully');
    }

    /**
     * Display the specified DocumentApproved.
     * GET|HEAD /documentApproveds/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var DocumentApproved $documentApproved */
        $documentApproved = $this->documentApprovedRepository->findWithoutFail($id);

        if (empty($documentApproved)) {
            return $this->sendError('Document Approved not found');
        }

        return $this->sendResponse($documentApproved->toArray(), 'Document Approved retrieved successfully');
    }

    /**
     * Update the specified DocumentApproved in storage.
     * PUT/PATCH /documentApproveds/{id}
     *
     * @param  int $id
     * @param UpdateDocumentApprovedAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateDocumentApprovedAPIRequest $request)
    {
        $input = $request->all();

        /** @var DocumentApproved $documentApproved */
        $documentApproved = $this->documentApprovedRepository->findWithoutFail($id);

        if (empty($documentApproved)) {
            return $this->sendError('Document Approved not found');
        }

        $documentApproved = $this->documentApprovedRepository->update($input, $id);

        return $this->sendResponse($documentApproved->toArray(), 'DocumentApproved updated successfully');
    }

    /**
     * Remove the specified DocumentApproved from storage.
     * DELETE /documentApproveds/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var DocumentApproved $documentApproved */
        $documentApproved = $this->documentApprovedRepository->findWithoutFail($id);

        if (empty($documentApproved)) {
            return $this->sendError('Document Approved not found');
        }

        $documentApproved->delete();

        return $this->sendResponse($id, 'Document Approved deleted successfully');
    }

}
