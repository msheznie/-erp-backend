<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatePoPaymentTermsRefferedbackRequest;
use App\Http\Requests\UpdatePoPaymentTermsRefferedbackRequest;
use App\Repositories\PoPaymentTermsRefferedbackRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class PoPaymentTermsRefferedbackController extends AppBaseController
{
    /** @var  PoPaymentTermsRefferedbackRepository */
    private $poPaymentTermsRefferedbackRepository;

    public function __construct(PoPaymentTermsRefferedbackRepository $poPaymentTermsRefferedbackRepo)
    {
        $this->poPaymentTermsRefferedbackRepository = $poPaymentTermsRefferedbackRepo;
    }

    /**
     * Display a listing of the PoPaymentTermsRefferedback.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->poPaymentTermsRefferedbackRepository->pushCriteria(new RequestCriteria($request));
        $poPaymentTermsRefferedbacks = $this->poPaymentTermsRefferedbackRepository->all();

        return view('po_payment_terms_refferedbacks.index')
            ->with('poPaymentTermsRefferedbacks', $poPaymentTermsRefferedbacks);
    }

    /**
     * Show the form for creating a new PoPaymentTermsRefferedback.
     *
     * @return Response
     */
    public function create()
    {
        return view('po_payment_terms_refferedbacks.create');
    }

    /**
     * Store a newly created PoPaymentTermsRefferedback in storage.
     *
     * @param CreatePoPaymentTermsRefferedbackRequest $request
     *
     * @return Response
     */
    public function store(CreatePoPaymentTermsRefferedbackRequest $request)
    {
        $input = $request->all();

        $poPaymentTermsRefferedback = $this->poPaymentTermsRefferedbackRepository->create($input);

        Flash::success('Po Payment Terms Refferedback saved successfully.');

        return redirect(route('poPaymentTermsRefferedbacks.index'));
    }

    /**
     * Display the specified PoPaymentTermsRefferedback.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $poPaymentTermsRefferedback = $this->poPaymentTermsRefferedbackRepository->findWithoutFail($id);

        if (empty($poPaymentTermsRefferedback)) {
            Flash::error('Po Payment Terms Refferedback not found');

            return redirect(route('poPaymentTermsRefferedbacks.index'));
        }

        return view('po_payment_terms_refferedbacks.show')->with('poPaymentTermsRefferedback', $poPaymentTermsRefferedback);
    }

    /**
     * Show the form for editing the specified PoPaymentTermsRefferedback.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $poPaymentTermsRefferedback = $this->poPaymentTermsRefferedbackRepository->findWithoutFail($id);

        if (empty($poPaymentTermsRefferedback)) {
            Flash::error('Po Payment Terms Refferedback not found');

            return redirect(route('poPaymentTermsRefferedbacks.index'));
        }

        return view('po_payment_terms_refferedbacks.edit')->with('poPaymentTermsRefferedback', $poPaymentTermsRefferedback);
    }

    /**
     * Update the specified PoPaymentTermsRefferedback in storage.
     *
     * @param  int              $id
     * @param UpdatePoPaymentTermsRefferedbackRequest $request
     *
     * @return Response
     */
    public function update($id, UpdatePoPaymentTermsRefferedbackRequest $request)
    {
        $poPaymentTermsRefferedback = $this->poPaymentTermsRefferedbackRepository->findWithoutFail($id);

        if (empty($poPaymentTermsRefferedback)) {
            Flash::error('Po Payment Terms Refferedback not found');

            return redirect(route('poPaymentTermsRefferedbacks.index'));
        }

        $poPaymentTermsRefferedback = $this->poPaymentTermsRefferedbackRepository->update($request->all(), $id);

        Flash::success('Po Payment Terms Refferedback updated successfully.');

        return redirect(route('poPaymentTermsRefferedbacks.index'));
    }

    /**
     * Remove the specified PoPaymentTermsRefferedback from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $poPaymentTermsRefferedback = $this->poPaymentTermsRefferedbackRepository->findWithoutFail($id);

        if (empty($poPaymentTermsRefferedback)) {
            Flash::error('Po Payment Terms Refferedback not found');

            return redirect(route('poPaymentTermsRefferedbacks.index'));
        }

        $this->poPaymentTermsRefferedbackRepository->delete($id);

        Flash::success('Po Payment Terms Refferedback deleted successfully.');

        return redirect(route('poPaymentTermsRefferedbacks.index'));
    }
}
