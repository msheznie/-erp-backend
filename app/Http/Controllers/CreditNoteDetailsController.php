<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateCreditNoteDetailsRequest;
use App\Http\Requests\UpdateCreditNoteDetailsRequest;
use App\Repositories\CreditNoteDetailsRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class CreditNoteDetailsController extends AppBaseController
{
    /** @var  CreditNoteDetailsRepository */
    private $creditNoteDetailsRepository;

    public function __construct(CreditNoteDetailsRepository $creditNoteDetailsRepo)
    {
        $this->creditNoteDetailsRepository = $creditNoteDetailsRepo;
    }

    /**
     * Display a listing of the CreditNoteDetails.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->creditNoteDetailsRepository->pushCriteria(new RequestCriteria($request));
        $creditNoteDetails = $this->creditNoteDetailsRepository->all();

        return view('credit_note_details.index')
            ->with('creditNoteDetails', $creditNoteDetails);
    }

    /**
     * Show the form for creating a new CreditNoteDetails.
     *
     * @return Response
     */
    public function create()
    {
        return view('credit_note_details.create');
    }

    /**
     * Store a newly created CreditNoteDetails in storage.
     *
     * @param CreateCreditNoteDetailsRequest $request
     *
     * @return Response
     */
    public function store(CreateCreditNoteDetailsRequest $request)
    {
        $input = $request->all();

        $creditNoteDetails = $this->creditNoteDetailsRepository->create($input);

        Flash::success('Credit Note Details saved successfully.');

        return redirect(route('creditNoteDetails.index'));
    }

    /**
     * Display the specified CreditNoteDetails.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $creditNoteDetails = $this->creditNoteDetailsRepository->findWithoutFail($id);

        if (empty($creditNoteDetails)) {
            Flash::error('Credit Note Details not found');

            return redirect(route('creditNoteDetails.index'));
        }

        return view('credit_note_details.show')->with('creditNoteDetails', $creditNoteDetails);
    }

    /**
     * Show the form for editing the specified CreditNoteDetails.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $creditNoteDetails = $this->creditNoteDetailsRepository->findWithoutFail($id);

        if (empty($creditNoteDetails)) {
            Flash::error('Credit Note Details not found');

            return redirect(route('creditNoteDetails.index'));
        }

        return view('credit_note_details.edit')->with('creditNoteDetails', $creditNoteDetails);
    }

    /**
     * Update the specified CreditNoteDetails in storage.
     *
     * @param  int              $id
     * @param UpdateCreditNoteDetailsRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateCreditNoteDetailsRequest $request)
    {
        $creditNoteDetails = $this->creditNoteDetailsRepository->findWithoutFail($id);

        if (empty($creditNoteDetails)) {
            Flash::error('Credit Note Details not found');

            return redirect(route('creditNoteDetails.index'));
        }

        $creditNoteDetails = $this->creditNoteDetailsRepository->update($request->all(), $id);

        Flash::success('Credit Note Details updated successfully.');

        return redirect(route('creditNoteDetails.index'));
    }

    /**
     * Remove the specified CreditNoteDetails from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $creditNoteDetails = $this->creditNoteDetailsRepository->findWithoutFail($id);

        if (empty($creditNoteDetails)) {
            Flash::error('Credit Note Details not found');

            return redirect(route('creditNoteDetails.index'));
        }

        $this->creditNoteDetailsRepository->delete($id);

        Flash::success('Credit Note Details deleted successfully.');

        return redirect(route('creditNoteDetails.index'));
    }
}
