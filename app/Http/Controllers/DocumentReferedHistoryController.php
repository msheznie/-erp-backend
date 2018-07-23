<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateDocumentReferedHistoryRequest;
use App\Http\Requests\UpdateDocumentReferedHistoryRequest;
use App\Repositories\DocumentReferedHistoryRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class DocumentReferedHistoryController extends AppBaseController
{
    /** @var  DocumentReferedHistoryRepository */
    private $documentReferedHistoryRepository;

    public function __construct(DocumentReferedHistoryRepository $documentReferedHistoryRepo)
    {
        $this->documentReferedHistoryRepository = $documentReferedHistoryRepo;
    }

    /**
     * Display a listing of the DocumentReferedHistory.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->documentReferedHistoryRepository->pushCriteria(new RequestCriteria($request));
        $documentReferedHistories = $this->documentReferedHistoryRepository->all();

        return view('document_refered_histories.index')
            ->with('documentReferedHistories', $documentReferedHistories);
    }

    /**
     * Show the form for creating a new DocumentReferedHistory.
     *
     * @return Response
     */
    public function create()
    {
        return view('document_refered_histories.create');
    }

    /**
     * Store a newly created DocumentReferedHistory in storage.
     *
     * @param CreateDocumentReferedHistoryRequest $request
     *
     * @return Response
     */
    public function store(CreateDocumentReferedHistoryRequest $request)
    {
        $input = $request->all();

        $documentReferedHistory = $this->documentReferedHistoryRepository->create($input);

        Flash::success('Document Refered History saved successfully.');

        return redirect(route('documentReferedHistories.index'));
    }

    /**
     * Display the specified DocumentReferedHistory.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $documentReferedHistory = $this->documentReferedHistoryRepository->findWithoutFail($id);

        if (empty($documentReferedHistory)) {
            Flash::error('Document Refered History not found');

            return redirect(route('documentReferedHistories.index'));
        }

        return view('document_refered_histories.show')->with('documentReferedHistory', $documentReferedHistory);
    }

    /**
     * Show the form for editing the specified DocumentReferedHistory.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $documentReferedHistory = $this->documentReferedHistoryRepository->findWithoutFail($id);

        if (empty($documentReferedHistory)) {
            Flash::error('Document Refered History not found');

            return redirect(route('documentReferedHistories.index'));
        }

        return view('document_refered_histories.edit')->with('documentReferedHistory', $documentReferedHistory);
    }

    /**
     * Update the specified DocumentReferedHistory in storage.
     *
     * @param  int              $id
     * @param UpdateDocumentReferedHistoryRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateDocumentReferedHistoryRequest $request)
    {
        $documentReferedHistory = $this->documentReferedHistoryRepository->findWithoutFail($id);

        if (empty($documentReferedHistory)) {
            Flash::error('Document Refered History not found');

            return redirect(route('documentReferedHistories.index'));
        }

        $documentReferedHistory = $this->documentReferedHistoryRepository->update($request->all(), $id);

        Flash::success('Document Refered History updated successfully.');

        return redirect(route('documentReferedHistories.index'));
    }

    /**
     * Remove the specified DocumentReferedHistory from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $documentReferedHistory = $this->documentReferedHistoryRepository->findWithoutFail($id);

        if (empty($documentReferedHistory)) {
            Flash::error('Document Refered History not found');

            return redirect(route('documentReferedHistories.index'));
        }

        $this->documentReferedHistoryRepository->delete($id);

        Flash::success('Document Refered History deleted successfully.');

        return redirect(route('documentReferedHistories.index'));
    }
}
