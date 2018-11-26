<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateCreditNoteDetailsRefferdbackRequest;
use App\Http\Requests\UpdateCreditNoteDetailsRefferdbackRequest;
use App\Repositories\CreditNoteDetailsRefferdbackRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class CreditNoteDetailsRefferdbackController extends AppBaseController
{
    /** @var  CreditNoteDetailsRefferdbackRepository */
    private $creditNoteDetailsRefferdbackRepository;

    public function __construct(CreditNoteDetailsRefferdbackRepository $creditNoteDetailsRefferdbackRepo)
    {
        $this->creditNoteDetailsRefferdbackRepository = $creditNoteDetailsRefferdbackRepo;
    }

    /**
     * Display a listing of the CreditNoteDetailsRefferdback.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->creditNoteDetailsRefferdbackRepository->pushCriteria(new RequestCriteria($request));
        $creditNoteDetailsRefferdbacks = $this->creditNoteDetailsRefferdbackRepository->all();

        return view('credit_note_details_refferdbacks.index')
            ->with('creditNoteDetailsRefferdbacks', $creditNoteDetailsRefferdbacks);
    }

    /**
     * Show the form for creating a new CreditNoteDetailsRefferdback.
     *
     * @return Response
     */
    public function create()
    {
        return view('credit_note_details_refferdbacks.create');
    }

    /**
     * Store a newly created CreditNoteDetailsRefferdback in storage.
     *
     * @param CreateCreditNoteDetailsRefferdbackRequest $request
     *
     * @return Response
     */
    public function store(CreateCreditNoteDetailsRefferdbackRequest $request)
    {
        $input = $request->all();

        $creditNoteDetailsRefferdback = $this->creditNoteDetailsRefferdbackRepository->create($input);

        Flash::success('Credit Note Details Refferdback saved successfully.');

        return redirect(route('creditNoteDetailsRefferdbacks.index'));
    }

    /**
     * Display the specified CreditNoteDetailsRefferdback.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $creditNoteDetailsRefferdback = $this->creditNoteDetailsRefferdbackRepository->findWithoutFail($id);

        if (empty($creditNoteDetailsRefferdback)) {
            Flash::error('Credit Note Details Refferdback not found');

            return redirect(route('creditNoteDetailsRefferdbacks.index'));
        }

        return view('credit_note_details_refferdbacks.show')->with('creditNoteDetailsRefferdback', $creditNoteDetailsRefferdback);
    }

    /**
     * Show the form for editing the specified CreditNoteDetailsRefferdback.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $creditNoteDetailsRefferdback = $this->creditNoteDetailsRefferdbackRepository->findWithoutFail($id);

        if (empty($creditNoteDetailsRefferdback)) {
            Flash::error('Credit Note Details Refferdback not found');

            return redirect(route('creditNoteDetailsRefferdbacks.index'));
        }

        return view('credit_note_details_refferdbacks.edit')->with('creditNoteDetailsRefferdback', $creditNoteDetailsRefferdback);
    }

    /**
     * Update the specified CreditNoteDetailsRefferdback in storage.
     *
     * @param  int              $id
     * @param UpdateCreditNoteDetailsRefferdbackRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateCreditNoteDetailsRefferdbackRequest $request)
    {
        $creditNoteDetailsRefferdback = $this->creditNoteDetailsRefferdbackRepository->findWithoutFail($id);

        if (empty($creditNoteDetailsRefferdback)) {
            Flash::error('Credit Note Details Refferdback not found');

            return redirect(route('creditNoteDetailsRefferdbacks.index'));
        }

        $creditNoteDetailsRefferdback = $this->creditNoteDetailsRefferdbackRepository->update($request->all(), $id);

        Flash::success('Credit Note Details Refferdback updated successfully.');

        return redirect(route('creditNoteDetailsRefferdbacks.index'));
    }

    /**
     * Remove the specified CreditNoteDetailsRefferdback from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $creditNoteDetailsRefferdback = $this->creditNoteDetailsRefferdbackRepository->findWithoutFail($id);

        if (empty($creditNoteDetailsRefferdback)) {
            Flash::error('Credit Note Details Refferdback not found');

            return redirect(route('creditNoteDetailsRefferdbacks.index'));
        }

        $this->creditNoteDetailsRefferdbackRepository->delete($id);

        Flash::success('Credit Note Details Refferdback deleted successfully.');

        return redirect(route('creditNoteDetailsRefferdbacks.index'));
    }
}
