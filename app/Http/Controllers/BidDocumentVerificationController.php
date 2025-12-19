<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateBidDocumentVerificationRequest;
use App\Http\Requests\UpdateBidDocumentVerificationRequest;
use App\Repositories\BidDocumentVerificationRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class BidDocumentVerificationController extends AppBaseController
{
    /** @var  BidDocumentVerificationRepository */
    private $bidDocumentVerificationRepository;

    public function __construct(BidDocumentVerificationRepository $bidDocumentVerificationRepo)
    {
        $this->bidDocumentVerificationRepository = $bidDocumentVerificationRepo;
    }

    /**
     * Display a listing of the BidDocumentVerification.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->bidDocumentVerificationRepository->pushCriteria(new RequestCriteria($request));
        $bidDocumentVerifications = $this->bidDocumentVerificationRepository->all();

        return view('bid_document_verifications.index')
            ->with('bidDocumentVerifications', $bidDocumentVerifications);
    }

    /**
     * Show the form for creating a new BidDocumentVerification.
     *
     * @return Response
     */
    public function create()
    {
        return view('bid_document_verifications.create');
    }

    /**
     * Store a newly created BidDocumentVerification in storage.
     *
     * @param CreateBidDocumentVerificationRequest $request
     *
     * @return Response
     */
    public function store(CreateBidDocumentVerificationRequest $request)
    {
        $input = $request->all();

        $bidDocumentVerification = $this->bidDocumentVerificationRepository->create($input);

        Flash::success('Bid Document Verification saved successfully.');

        return redirect(route('bidDocumentVerifications.index'));
    }

    /**
     * Display the specified BidDocumentVerification.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $bidDocumentVerification = $this->bidDocumentVerificationRepository->findWithoutFail($id);

        if (empty($bidDocumentVerification)) {
            Flash::error('Bid Document Verification not found');

            return redirect(route('bidDocumentVerifications.index'));
        }

        return view('bid_document_verifications.show')->with('bidDocumentVerification', $bidDocumentVerification);
    }

    /**
     * Show the form for editing the specified BidDocumentVerification.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $bidDocumentVerification = $this->bidDocumentVerificationRepository->findWithoutFail($id);

        if (empty($bidDocumentVerification)) {
            Flash::error('Bid Document Verification not found');

            return redirect(route('bidDocumentVerifications.index'));
        }

        return view('bid_document_verifications.edit')->with('bidDocumentVerification', $bidDocumentVerification);
    }

    /**
     * Update the specified BidDocumentVerification in storage.
     *
     * @param  int              $id
     * @param UpdateBidDocumentVerificationRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateBidDocumentVerificationRequest $request)
    {
        $bidDocumentVerification = $this->bidDocumentVerificationRepository->findWithoutFail($id);

        if (empty($bidDocumentVerification)) {
            Flash::error('Bid Document Verification not found');

            return redirect(route('bidDocumentVerifications.index'));
        }

        $bidDocumentVerification = $this->bidDocumentVerificationRepository->update($request->all(), $id);

        Flash::success('Bid Document Verification updated successfully.');

        return redirect(route('bidDocumentVerifications.index'));
    }

    /**
     * Remove the specified BidDocumentVerification from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $bidDocumentVerification = $this->bidDocumentVerificationRepository->findWithoutFail($id);

        if (empty($bidDocumentVerification)) {
            Flash::error('Bid Document Verification not found');

            return redirect(route('bidDocumentVerifications.index'));
        }

        $this->bidDocumentVerificationRepository->delete($id);

        Flash::success('Bid Document Verification deleted successfully.');

        return redirect(route('bidDocumentVerifications.index'));
    }
}
