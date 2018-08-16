<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateDebitNoteDetailsRequest;
use App\Http\Requests\UpdateDebitNoteDetailsRequest;
use App\Repositories\DebitNoteDetailsRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class DebitNoteDetailsController extends AppBaseController
{
    /** @var  DebitNoteDetailsRepository */
    private $debitNoteDetailsRepository;

    public function __construct(DebitNoteDetailsRepository $debitNoteDetailsRepo)
    {
        $this->debitNoteDetailsRepository = $debitNoteDetailsRepo;
    }

    /**
     * Display a listing of the DebitNoteDetails.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->debitNoteDetailsRepository->pushCriteria(new RequestCriteria($request));
        $debitNoteDetails = $this->debitNoteDetailsRepository->all();

        return view('debit_note_details.index')
            ->with('debitNoteDetails', $debitNoteDetails);
    }

    /**
     * Show the form for creating a new DebitNoteDetails.
     *
     * @return Response
     */
    public function create()
    {
        return view('debit_note_details.create');
    }

    /**
     * Store a newly created DebitNoteDetails in storage.
     *
     * @param CreateDebitNoteDetailsRequest $request
     *
     * @return Response
     */
    public function store(CreateDebitNoteDetailsRequest $request)
    {
        $input = $request->all();

        $debitNoteDetails = $this->debitNoteDetailsRepository->create($input);

        Flash::success('Debit Note Details saved successfully.');

        return redirect(route('debitNoteDetails.index'));
    }

    /**
     * Display the specified DebitNoteDetails.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $debitNoteDetails = $this->debitNoteDetailsRepository->findWithoutFail($id);

        if (empty($debitNoteDetails)) {
            Flash::error('Debit Note Details not found');

            return redirect(route('debitNoteDetails.index'));
        }

        return view('debit_note_details.show')->with('debitNoteDetails', $debitNoteDetails);
    }

    /**
     * Show the form for editing the specified DebitNoteDetails.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $debitNoteDetails = $this->debitNoteDetailsRepository->findWithoutFail($id);

        if (empty($debitNoteDetails)) {
            Flash::error('Debit Note Details not found');

            return redirect(route('debitNoteDetails.index'));
        }

        return view('debit_note_details.edit')->with('debitNoteDetails', $debitNoteDetails);
    }

    /**
     * Update the specified DebitNoteDetails in storage.
     *
     * @param  int              $id
     * @param UpdateDebitNoteDetailsRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateDebitNoteDetailsRequest $request)
    {
        $debitNoteDetails = $this->debitNoteDetailsRepository->findWithoutFail($id);

        if (empty($debitNoteDetails)) {
            Flash::error('Debit Note Details not found');

            return redirect(route('debitNoteDetails.index'));
        }

        $debitNoteDetails = $this->debitNoteDetailsRepository->update($request->all(), $id);

        Flash::success('Debit Note Details updated successfully.');

        return redirect(route('debitNoteDetails.index'));
    }

    /**
     * Remove the specified DebitNoteDetails from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $debitNoteDetails = $this->debitNoteDetailsRepository->findWithoutFail($id);

        if (empty($debitNoteDetails)) {
            Flash::error('Debit Note Details not found');

            return redirect(route('debitNoteDetails.index'));
        }

        $this->debitNoteDetailsRepository->delete($id);

        Flash::success('Debit Note Details deleted successfully.');

        return redirect(route('debitNoteDetails.index'));
    }
}
