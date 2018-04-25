<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateAdvancePaymentDetailsRequest;
use App\Http\Requests\UpdateAdvancePaymentDetailsRequest;
use App\Repositories\AdvancePaymentDetailsRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class AdvancePaymentDetailsController extends AppBaseController
{
    /** @var  AdvancePaymentDetailsRepository */
    private $advancePaymentDetailsRepository;

    public function __construct(AdvancePaymentDetailsRepository $advancePaymentDetailsRepo)
    {
        $this->advancePaymentDetailsRepository = $advancePaymentDetailsRepo;
    }

    /**
     * Display a listing of the AdvancePaymentDetails.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->advancePaymentDetailsRepository->pushCriteria(new RequestCriteria($request));
        $advancePaymentDetails = $this->advancePaymentDetailsRepository->all();

        return view('advance_payment_details.index')
            ->with('advancePaymentDetails', $advancePaymentDetails);
    }

    /**
     * Show the form for creating a new AdvancePaymentDetails.
     *
     * @return Response
     */
    public function create()
    {
        return view('advance_payment_details.create');
    }

    /**
     * Store a newly created AdvancePaymentDetails in storage.
     *
     * @param CreateAdvancePaymentDetailsRequest $request
     *
     * @return Response
     */
    public function store(CreateAdvancePaymentDetailsRequest $request)
    {
        $input = $request->all();

        $advancePaymentDetails = $this->advancePaymentDetailsRepository->create($input);

        Flash::success('Advance Payment Details saved successfully.');

        return redirect(route('advancePaymentDetails.index'));
    }

    /**
     * Display the specified AdvancePaymentDetails.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $advancePaymentDetails = $this->advancePaymentDetailsRepository->findWithoutFail($id);

        if (empty($advancePaymentDetails)) {
            Flash::error('Advance Payment Details not found');

            return redirect(route('advancePaymentDetails.index'));
        }

        return view('advance_payment_details.show')->with('advancePaymentDetails', $advancePaymentDetails);
    }

    /**
     * Show the form for editing the specified AdvancePaymentDetails.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $advancePaymentDetails = $this->advancePaymentDetailsRepository->findWithoutFail($id);

        if (empty($advancePaymentDetails)) {
            Flash::error('Advance Payment Details not found');

            return redirect(route('advancePaymentDetails.index'));
        }

        return view('advance_payment_details.edit')->with('advancePaymentDetails', $advancePaymentDetails);
    }

    /**
     * Update the specified AdvancePaymentDetails in storage.
     *
     * @param  int              $id
     * @param UpdateAdvancePaymentDetailsRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateAdvancePaymentDetailsRequest $request)
    {
        $advancePaymentDetails = $this->advancePaymentDetailsRepository->findWithoutFail($id);

        if (empty($advancePaymentDetails)) {
            Flash::error('Advance Payment Details not found');

            return redirect(route('advancePaymentDetails.index'));
        }

        $advancePaymentDetails = $this->advancePaymentDetailsRepository->update($request->all(), $id);

        Flash::success('Advance Payment Details updated successfully.');

        return redirect(route('advancePaymentDetails.index'));
    }

    /**
     * Remove the specified AdvancePaymentDetails from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $advancePaymentDetails = $this->advancePaymentDetailsRepository->findWithoutFail($id);

        if (empty($advancePaymentDetails)) {
            Flash::error('Advance Payment Details not found');

            return redirect(route('advancePaymentDetails.index'));
        }

        $this->advancePaymentDetailsRepository->delete($id);

        Flash::success('Advance Payment Details deleted successfully.');

        return redirect(route('advancePaymentDetails.index'));
    }
}
