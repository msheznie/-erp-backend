<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateCreditNoteReferredbackRequest;
use App\Http\Requests\UpdateCreditNoteReferredbackRequest;
use App\Repositories\CreditNoteReferredbackRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class CreditNoteReferredbackController extends AppBaseController
{
    /** @var  CreditNoteReferredbackRepository */
    private $creditNoteReferredbackRepository;

    public function __construct(CreditNoteReferredbackRepository $creditNoteReferredbackRepo)
    {
        $this->creditNoteReferredbackRepository = $creditNoteReferredbackRepo;
    }

    /**
     * Display a listing of the CreditNoteReferredback.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->creditNoteReferredbackRepository->pushCriteria(new RequestCriteria($request));
        $creditNoteReferredbacks = $this->creditNoteReferredbackRepository->all();

        return view('credit_note_referredbacks.index')
            ->with('creditNoteReferredbacks', $creditNoteReferredbacks);
    }

    /**
     * Show the form for creating a new CreditNoteReferredback.
     *
     * @return Response
     */
    public function create()
    {
        return view('credit_note_referredbacks.create');
    }

    /**
     * Store a newly created CreditNoteReferredback in storage.
     *
     * @param CreateCreditNoteReferredbackRequest $request
     *
     * @return Response
     */
    public function store(CreateCreditNoteReferredbackRequest $request)
    {
        $input = $request->all();

        $creditNoteReferredback = $this->creditNoteReferredbackRepository->create($input);

        Flash::success('Credit Note Referredback saved successfully.');

        return redirect(route('creditNoteReferredbacks.index'));
    }

    /**
     * Display the specified CreditNoteReferredback.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $creditNoteReferredback = $this->creditNoteReferredbackRepository->findWithoutFail($id);

        if (empty($creditNoteReferredback)) {
            Flash::error('Credit Note Referredback not found');

            return redirect(route('creditNoteReferredbacks.index'));
        }

        return view('credit_note_referredbacks.show')->with('creditNoteReferredback', $creditNoteReferredback);
    }

    /**
     * Show the form for editing the specified CreditNoteReferredback.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $creditNoteReferredback = $this->creditNoteReferredbackRepository->findWithoutFail($id);

        if (empty($creditNoteReferredback)) {
            Flash::error('Credit Note Referredback not found');

            return redirect(route('creditNoteReferredbacks.index'));
        }

        return view('credit_note_referredbacks.edit')->with('creditNoteReferredback', $creditNoteReferredback);
    }

    /**
     * Update the specified CreditNoteReferredback in storage.
     *
     * @param  int              $id
     * @param UpdateCreditNoteReferredbackRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateCreditNoteReferredbackRequest $request)
    {
        $creditNoteReferredback = $this->creditNoteReferredbackRepository->findWithoutFail($id);

        if (empty($creditNoteReferredback)) {
            Flash::error('Credit Note Referredback not found');

            return redirect(route('creditNoteReferredbacks.index'));
        }

        $creditNoteReferredback = $this->creditNoteReferredbackRepository->update($request->all(), $id);

        Flash::success('Credit Note Referredback updated successfully.');

        return redirect(route('creditNoteReferredbacks.index'));
    }

    /**
     * Remove the specified CreditNoteReferredback from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $creditNoteReferredback = $this->creditNoteReferredbackRepository->findWithoutFail($id);

        if (empty($creditNoteReferredback)) {
            Flash::error('Credit Note Referredback not found');

            return redirect(route('creditNoteReferredbacks.index'));
        }

        $this->creditNoteReferredbackRepository->delete($id);

        Flash::success('Credit Note Referredback deleted successfully.');

        return redirect(route('creditNoteReferredbacks.index'));
    }
}
