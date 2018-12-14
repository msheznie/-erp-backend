<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateDocumentRestrictionPolicyRequest;
use App\Http\Requests\UpdateDocumentRestrictionPolicyRequest;
use App\Repositories\DocumentRestrictionPolicyRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class DocumentRestrictionPolicyController extends AppBaseController
{
    /** @var  DocumentRestrictionPolicyRepository */
    private $documentRestrictionPolicyRepository;

    public function __construct(DocumentRestrictionPolicyRepository $documentRestrictionPolicyRepo)
    {
        $this->documentRestrictionPolicyRepository = $documentRestrictionPolicyRepo;
    }

    /**
     * Display a listing of the DocumentRestrictionPolicy.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->documentRestrictionPolicyRepository->pushCriteria(new RequestCriteria($request));
        $documentRestrictionPolicies = $this->documentRestrictionPolicyRepository->all();

        return view('document_restriction_policies.index')
            ->with('documentRestrictionPolicies', $documentRestrictionPolicies);
    }

    /**
     * Show the form for creating a new DocumentRestrictionPolicy.
     *
     * @return Response
     */
    public function create()
    {
        return view('document_restriction_policies.create');
    }

    /**
     * Store a newly created DocumentRestrictionPolicy in storage.
     *
     * @param CreateDocumentRestrictionPolicyRequest $request
     *
     * @return Response
     */
    public function store(CreateDocumentRestrictionPolicyRequest $request)
    {
        $input = $request->all();

        $documentRestrictionPolicy = $this->documentRestrictionPolicyRepository->create($input);

        Flash::success('Document Restriction Policy saved successfully.');

        return redirect(route('documentRestrictionPolicies.index'));
    }

    /**
     * Display the specified DocumentRestrictionPolicy.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $documentRestrictionPolicy = $this->documentRestrictionPolicyRepository->findWithoutFail($id);

        if (empty($documentRestrictionPolicy)) {
            Flash::error('Document Restriction Policy not found');

            return redirect(route('documentRestrictionPolicies.index'));
        }

        return view('document_restriction_policies.show')->with('documentRestrictionPolicy', $documentRestrictionPolicy);
    }

    /**
     * Show the form for editing the specified DocumentRestrictionPolicy.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $documentRestrictionPolicy = $this->documentRestrictionPolicyRepository->findWithoutFail($id);

        if (empty($documentRestrictionPolicy)) {
            Flash::error('Document Restriction Policy not found');

            return redirect(route('documentRestrictionPolicies.index'));
        }

        return view('document_restriction_policies.edit')->with('documentRestrictionPolicy', $documentRestrictionPolicy);
    }

    /**
     * Update the specified DocumentRestrictionPolicy in storage.
     *
     * @param  int              $id
     * @param UpdateDocumentRestrictionPolicyRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateDocumentRestrictionPolicyRequest $request)
    {
        $documentRestrictionPolicy = $this->documentRestrictionPolicyRepository->findWithoutFail($id);

        if (empty($documentRestrictionPolicy)) {
            Flash::error('Document Restriction Policy not found');

            return redirect(route('documentRestrictionPolicies.index'));
        }

        $documentRestrictionPolicy = $this->documentRestrictionPolicyRepository->update($request->all(), $id);

        Flash::success('Document Restriction Policy updated successfully.');

        return redirect(route('documentRestrictionPolicies.index'));
    }

    /**
     * Remove the specified DocumentRestrictionPolicy from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $documentRestrictionPolicy = $this->documentRestrictionPolicyRepository->findWithoutFail($id);

        if (empty($documentRestrictionPolicy)) {
            Flash::error('Document Restriction Policy not found');

            return redirect(route('documentRestrictionPolicies.index'));
        }

        $this->documentRestrictionPolicyRepository->delete($id);

        Flash::success('Document Restriction Policy deleted successfully.');

        return redirect(route('documentRestrictionPolicies.index'));
    }
}
