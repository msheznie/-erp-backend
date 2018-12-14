<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateDocumentRestrictionAssignRequest;
use App\Http\Requests\UpdateDocumentRestrictionAssignRequest;
use App\Repositories\DocumentRestrictionAssignRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class DocumentRestrictionAssignController extends AppBaseController
{
    /** @var  DocumentRestrictionAssignRepository */
    private $documentRestrictionAssignRepository;

    public function __construct(DocumentRestrictionAssignRepository $documentRestrictionAssignRepo)
    {
        $this->documentRestrictionAssignRepository = $documentRestrictionAssignRepo;
    }

    /**
     * Display a listing of the DocumentRestrictionAssign.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->documentRestrictionAssignRepository->pushCriteria(new RequestCriteria($request));
        $documentRestrictionAssigns = $this->documentRestrictionAssignRepository->all();

        return view('document_restriction_assigns.index')
            ->with('documentRestrictionAssigns', $documentRestrictionAssigns);
    }

    /**
     * Show the form for creating a new DocumentRestrictionAssign.
     *
     * @return Response
     */
    public function create()
    {
        return view('document_restriction_assigns.create');
    }

    /**
     * Store a newly created DocumentRestrictionAssign in storage.
     *
     * @param CreateDocumentRestrictionAssignRequest $request
     *
     * @return Response
     */
    public function store(CreateDocumentRestrictionAssignRequest $request)
    {
        $input = $request->all();

        $documentRestrictionAssign = $this->documentRestrictionAssignRepository->create($input);

        Flash::success('Document Restriction Assign saved successfully.');

        return redirect(route('documentRestrictionAssigns.index'));
    }

    /**
     * Display the specified DocumentRestrictionAssign.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $documentRestrictionAssign = $this->documentRestrictionAssignRepository->findWithoutFail($id);

        if (empty($documentRestrictionAssign)) {
            Flash::error('Document Restriction Assign not found');

            return redirect(route('documentRestrictionAssigns.index'));
        }

        return view('document_restriction_assigns.show')->with('documentRestrictionAssign', $documentRestrictionAssign);
    }

    /**
     * Show the form for editing the specified DocumentRestrictionAssign.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $documentRestrictionAssign = $this->documentRestrictionAssignRepository->findWithoutFail($id);

        if (empty($documentRestrictionAssign)) {
            Flash::error('Document Restriction Assign not found');

            return redirect(route('documentRestrictionAssigns.index'));
        }

        return view('document_restriction_assigns.edit')->with('documentRestrictionAssign', $documentRestrictionAssign);
    }

    /**
     * Update the specified DocumentRestrictionAssign in storage.
     *
     * @param  int              $id
     * @param UpdateDocumentRestrictionAssignRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateDocumentRestrictionAssignRequest $request)
    {
        $documentRestrictionAssign = $this->documentRestrictionAssignRepository->findWithoutFail($id);

        if (empty($documentRestrictionAssign)) {
            Flash::error('Document Restriction Assign not found');

            return redirect(route('documentRestrictionAssigns.index'));
        }

        $documentRestrictionAssign = $this->documentRestrictionAssignRepository->update($request->all(), $id);

        Flash::success('Document Restriction Assign updated successfully.');

        return redirect(route('documentRestrictionAssigns.index'));
    }

    /**
     * Remove the specified DocumentRestrictionAssign from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $documentRestrictionAssign = $this->documentRestrictionAssignRepository->findWithoutFail($id);

        if (empty($documentRestrictionAssign)) {
            Flash::error('Document Restriction Assign not found');

            return redirect(route('documentRestrictionAssigns.index'));
        }

        $this->documentRestrictionAssignRepository->delete($id);

        Flash::success('Document Restriction Assign deleted successfully.');

        return redirect(route('documentRestrictionAssigns.index'));
    }
}
