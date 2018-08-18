<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateDebitNoteRequest;
use App\Http\Requests\UpdateDebitNoteRequest;
use App\Repositories\DebitNoteRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class DebitNoteController extends AppBaseController
{
    /** @var  DebitNoteRepository */
    private $debitNoteRepository;

    public function __construct(DebitNoteRepository $debitNoteRepo)
    {
        $this->debitNoteRepository = $debitNoteRepo;
    }

    /**
     * Display a listing of the DebitNote.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->debitNoteRepository->pushCriteria(new RequestCriteria($request));
        $debitNotes = $this->debitNoteRepository->all();

        return view('debit_notes.index')
            ->with('debitNotes', $debitNotes);
    }

    /**
     * Show the form for creating a new DebitNote.
     *
     * @return Response
     */
    public function create()
    {
        return view('debit_notes.create');
    }

    /**
     * Store a newly created DebitNote in storage.
     *
     * @param CreateDebitNoteRequest $request
     *
     * @return Response
     */
    public function store(CreateDebitNoteRequest $request)
    {
        $input = $request->all();

        $debitNote = $this->debitNoteRepository->create($input);

        Flash::success('Debit Note saved successfully.');

        return redirect(route('debitNotes.index'));
    }

    /**
     * Display the specified DebitNote.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $debitNote = $this->debitNoteRepository->findWithoutFail($id);

        if (empty($debitNote)) {
            Flash::error('Debit Note not found');

            return redirect(route('debitNotes.index'));
        }

        return view('debit_notes.show')->with('debitNote', $debitNote);
    }

    /**
     * Show the form for editing the specified DebitNote.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $debitNote = $this->debitNoteRepository->findWithoutFail($id);

        if (empty($debitNote)) {
            Flash::error('Debit Note not found');

            return redirect(route('debitNotes.index'));
        }

        return view('debit_notes.edit')->with('debitNote', $debitNote);
    }

    /**
     * Update the specified DebitNote in storage.
     *
     * @param  int              $id
     * @param UpdateDebitNoteRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateDebitNoteRequest $request)
    {
        $debitNote = $this->debitNoteRepository->findWithoutFail($id);

        if (empty($debitNote)) {
            Flash::error('Debit Note not found');

            return redirect(route('debitNotes.index'));
        }

        $debitNote = $this->debitNoteRepository->update($request->all(), $id);

        Flash::success('Debit Note updated successfully.');

        return redirect(route('debitNotes.index'));
    }

    /**
     * Remove the specified DebitNote from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $debitNote = $this->debitNoteRepository->findWithoutFail($id);

        if (empty($debitNote)) {
            Flash::error('Debit Note not found');

            return redirect(route('debitNotes.index'));
        }

        $this->debitNoteRepository->delete($id);

        Flash::success('Debit Note deleted successfully.');

        return redirect(route('debitNotes.index'));
    }
}
