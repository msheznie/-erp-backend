<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateCreditNoteRequest;
use App\Http\Requests\UpdateCreditNoteRequest;
use App\Repositories\CreditNoteRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class CreditNoteController extends AppBaseController
{
    /** @var  CreditNoteRepository */
    private $creditNoteRepository;

    public function __construct(CreditNoteRepository $creditNoteRepo)
    {
        $this->creditNoteRepository = $creditNoteRepo;
    }

    /**
     * Display a listing of the CreditNote.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->creditNoteRepository->pushCriteria(new RequestCriteria($request));
        $creditNotes = $this->creditNoteRepository->all();

        return view('credit_notes.index')
            ->with('creditNotes', $creditNotes);
    }

    /**
     * Show the form for creating a new CreditNote.
     *
     * @return Response
     */
    public function create()
    {
        return view('credit_notes.create');
    }

    /**
     * Store a newly created CreditNote in storage.
     *
     * @param CreateCreditNoteRequest $request
     *
     * @return Response
     */
    public function store(CreateCreditNoteRequest $request)
    {
        $input = $request->all();

        $creditNote = $this->creditNoteRepository->create($input);

        Flash::success('Credit Note saved successfully.');

        return redirect(route('creditNotes.index'));
    }

    /**
     * Display the specified CreditNote.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $creditNote = $this->creditNoteRepository->findWithoutFail($id);

        if (empty($creditNote)) {
            Flash::error('Credit Note not found');

            return redirect(route('creditNotes.index'));
        }

        return view('credit_notes.show')->with('creditNote', $creditNote);
    }

    /**
     * Show the form for editing the specified CreditNote.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $creditNote = $this->creditNoteRepository->findWithoutFail($id);

        if (empty($creditNote)) {
            Flash::error('Credit Note not found');

            return redirect(route('creditNotes.index'));
        }

        return view('credit_notes.edit')->with('creditNote', $creditNote);
    }

    /**
     * Update the specified CreditNote in storage.
     *
     * @param  int              $id
     * @param UpdateCreditNoteRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateCreditNoteRequest $request)
    {
        $creditNote = $this->creditNoteRepository->findWithoutFail($id);

        if (empty($creditNote)) {
            Flash::error('Credit Note not found');

            return redirect(route('creditNotes.index'));
        }

        $creditNote = $this->creditNoteRepository->update($request->all(), $id);

        Flash::success('Credit Note updated successfully.');

        return redirect(route('creditNotes.index'));
    }

    /**
     * Remove the specified CreditNote from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $creditNote = $this->creditNoteRepository->findWithoutFail($id);

        if (empty($creditNote)) {
            Flash::error('Credit Note not found');

            return redirect(route('creditNotes.index'));
        }

        $this->creditNoteRepository->delete($id);

        Flash::success('Credit Note deleted successfully.');

        return redirect(route('creditNotes.index'));
    }
}
