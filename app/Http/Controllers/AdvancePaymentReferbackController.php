<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateAdvancePaymentReferbackRequest;
use App\Http\Requests\UpdateAdvancePaymentReferbackRequest;
use App\Repositories\AdvancePaymentReferbackRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class AdvancePaymentReferbackController extends AppBaseController
{
    /** @var  AdvancePaymentReferbackRepository */
    private $advancePaymentReferbackRepository;

    public function __construct(AdvancePaymentReferbackRepository $advancePaymentReferbackRepo)
    {
        $this->advancePaymentReferbackRepository = $advancePaymentReferbackRepo;
    }

    /**
     * Display a listing of the AdvancePaymentReferback.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->advancePaymentReferbackRepository->pushCriteria(new RequestCriteria($request));
        $advancePaymentReferbacks = $this->advancePaymentReferbackRepository->all();

        return view('advance_payment_referbacks.index')
            ->with('advancePaymentReferbacks', $advancePaymentReferbacks);
    }

    /**
     * Show the form for creating a new AdvancePaymentReferback.
     *
     * @return Response
     */
    public function create()
    {
        return view('advance_payment_referbacks.create');
    }

    /**
     * Store a newly created AdvancePaymentReferback in storage.
     *
     * @param CreateAdvancePaymentReferbackRequest $request
     *
     * @return Response
     */
    public function store(CreateAdvancePaymentReferbackRequest $request)
    {
        $input = $request->all();

        $advancePaymentReferback = $this->advancePaymentReferbackRepository->create($input);

        Flash::success('Advance Payment Referback saved successfully.');

        return redirect(route('advancePaymentReferbacks.index'));
    }

    /**
     * Display the specified AdvancePaymentReferback.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $advancePaymentReferback = $this->advancePaymentReferbackRepository->findWithoutFail($id);

        if (empty($advancePaymentReferback)) {
            Flash::error('Advance Payment Referback not found');

            return redirect(route('advancePaymentReferbacks.index'));
        }

        return view('advance_payment_referbacks.show')->with('advancePaymentReferback', $advancePaymentReferback);
    }

    /**
     * Show the form for editing the specified AdvancePaymentReferback.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $advancePaymentReferback = $this->advancePaymentReferbackRepository->findWithoutFail($id);

        if (empty($advancePaymentReferback)) {
            Flash::error('Advance Payment Referback not found');

            return redirect(route('advancePaymentReferbacks.index'));
        }

        return view('advance_payment_referbacks.edit')->with('advancePaymentReferback', $advancePaymentReferback);
    }

    /**
     * Update the specified AdvancePaymentReferback in storage.
     *
     * @param  int              $id
     * @param UpdateAdvancePaymentReferbackRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateAdvancePaymentReferbackRequest $request)
    {
        $advancePaymentReferback = $this->advancePaymentReferbackRepository->findWithoutFail($id);

        if (empty($advancePaymentReferback)) {
            Flash::error('Advance Payment Referback not found');

            return redirect(route('advancePaymentReferbacks.index'));
        }

        $advancePaymentReferback = $this->advancePaymentReferbackRepository->update($request->all(), $id);

        Flash::success('Advance Payment Referback updated successfully.');

        return redirect(route('advancePaymentReferbacks.index'));
    }

    /**
     * Remove the specified AdvancePaymentReferback from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $advancePaymentReferback = $this->advancePaymentReferbackRepository->findWithoutFail($id);

        if (empty($advancePaymentReferback)) {
            Flash::error('Advance Payment Referback not found');

            return redirect(route('advancePaymentReferbacks.index'));
        }

        $this->advancePaymentReferbackRepository->delete($id);

        Flash::success('Advance Payment Referback deleted successfully.');

        return redirect(route('advancePaymentReferbacks.index'));
    }
}
