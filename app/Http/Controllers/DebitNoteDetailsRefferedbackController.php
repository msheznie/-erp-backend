<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateDebitNoteDetailsRefferedbackRequest;
use App\Http\Requests\UpdateDebitNoteDetailsRefferedbackRequest;
use App\Repositories\DebitNoteDetailsRefferedbackRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class DebitNoteDetailsRefferedbackController extends AppBaseController
{
    /** @var  DebitNoteDetailsRefferedbackRepository */
    private $debitNoteDetailsRefferedbackRepository;

    public function __construct(DebitNoteDetailsRefferedbackRepository $debitNoteDetailsRefferedbackRepo)
    {
        $this->debitNoteDetailsRefferedbackRepository = $debitNoteDetailsRefferedbackRepo;
    }

    /**
     * Display a listing of the DebitNoteDetailsRefferedback.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->debitNoteDetailsRefferedbackRepository->pushCriteria(new RequestCriteria($request));
        $debitNoteDetailsRefferedbacks = $this->debitNoteDetailsRefferedbackRepository->all();

        return view('debit_note_details_refferedbacks.index')
            ->with('debitNoteDetailsRefferedbacks', $debitNoteDetailsRefferedbacks);
    }

    /**
     * Show the form for creating a new DebitNoteDetailsRefferedback.
     *
     * @return Response
     */
    public function create()
    {
        return view('debit_note_details_refferedbacks.create');
    }

    /**
     * Store a newly created DebitNoteDetailsRefferedback in storage.
     *
     * @param CreateDebitNoteDetailsRefferedbackRequest $request
     *
     * @return Response
     */
    public function store(CreateDebitNoteDetailsRefferedbackRequest $request)
    {
        $input = $request->all();

        $debitNoteDetailsRefferedback = $this->debitNoteDetailsRefferedbackRepository->create($input);

        Flash::success('Debit Note Details Refferedback saved successfully.');

        return redirect(route('debitNoteDetailsRefferedbacks.index'));
    }

    /**
     * Display the specified DebitNoteDetailsRefferedback.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $debitNoteDetailsRefferedback = $this->debitNoteDetailsRefferedbackRepository->findWithoutFail($id);

        if (empty($debitNoteDetailsRefferedback)) {
            Flash::error('Debit Note Details Refferedback not found');

            return redirect(route('debitNoteDetailsRefferedbacks.index'));
        }

        return view('debit_note_details_refferedbacks.show')->with('debitNoteDetailsRefferedback', $debitNoteDetailsRefferedback);
    }

    /**
     * Show the form for editing the specified DebitNoteDetailsRefferedback.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $debitNoteDetailsRefferedback = $this->debitNoteDetailsRefferedbackRepository->findWithoutFail($id);

        if (empty($debitNoteDetailsRefferedback)) {
            Flash::error('Debit Note Details Refferedback not found');

            return redirect(route('debitNoteDetailsRefferedbacks.index'));
        }

        return view('debit_note_details_refferedbacks.edit')->with('debitNoteDetailsRefferedback', $debitNoteDetailsRefferedback);
    }

    /**
     * Update the specified DebitNoteDetailsRefferedback in storage.
     *
     * @param  int              $id
     * @param UpdateDebitNoteDetailsRefferedbackRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateDebitNoteDetailsRefferedbackRequest $request)
    {
        $debitNoteDetailsRefferedback = $this->debitNoteDetailsRefferedbackRepository->findWithoutFail($id);

        if (empty($debitNoteDetailsRefferedback)) {
            Flash::error('Debit Note Details Refferedback not found');

            return redirect(route('debitNoteDetailsRefferedbacks.index'));
        }

        $debitNoteDetailsRefferedback = $this->debitNoteDetailsRefferedbackRepository->update($request->all(), $id);

        Flash::success('Debit Note Details Refferedback updated successfully.');

        return redirect(route('debitNoteDetailsRefferedbacks.index'));
    }

    /**
     * Remove the specified DebitNoteDetailsRefferedback from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $debitNoteDetailsRefferedback = $this->debitNoteDetailsRefferedbackRepository->findWithoutFail($id);

        if (empty($debitNoteDetailsRefferedback)) {
            Flash::error('Debit Note Details Refferedback not found');

            return redirect(route('debitNoteDetailsRefferedbacks.index'));
        }

        $this->debitNoteDetailsRefferedbackRepository->delete($id);

        Flash::success('Debit Note Details Refferedback deleted successfully.');

        return redirect(route('debitNoteDetailsRefferedbacks.index'));
    }
}
