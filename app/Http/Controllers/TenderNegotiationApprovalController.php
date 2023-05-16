<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateTenderNegotiationApprovalRequest;
use App\Http\Requests\UpdateTenderNegotiationApprovalRequest;
use App\Repositories\TenderNegotiationApprovalRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class TenderNegotiationApprovalController extends AppBaseController
{
    /** @var  TenderNegotiationApprovalRepository */
    private $tenderNegotiationApprovalRepository;

    public function __construct(TenderNegotiationApprovalRepository $tenderNegotiationApprovalRepo)
    {
        $this->tenderNegotiationApprovalRepository = $tenderNegotiationApprovalRepo;
    }

    /**
     * Display a listing of the TenderNegotiationApproval.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->tenderNegotiationApprovalRepository->pushCriteria(new RequestCriteria($request));
        $tenderNegotiationApprovals = $this->tenderNegotiationApprovalRepository->all();

        return view('tender_negotiation_approvals.index')
            ->with('tenderNegotiationApprovals', $tenderNegotiationApprovals);
    }


    /**
     * Store a newly created TenderNegotiationApproval in storage.
     *
     * @param CreateTenderNegotiationApprovalRequest $request
     *
     * @return Response
     */
    public function store(CreateTenderNeogtiationApprovalRequest $request)
    {
        $input = $request->all();

        dd($input);
        $tenderNegotiationApproval = $this->tenderNegotiationApprovalRepository->create($input);

        Flash::success('Tender Negotiation Approval saved successfully.');

        return redirect(route('tenderNegotiationApprovals.index'));
    }

    /**
     * Display the specified TenderNegotiationApproval.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $tenderNegotiationApproval = $this->tenderNegotiationApprovalRepository->findWithoutFail($id);

        if (empty($tenderNegotiationApproval)) {
            Flash::error('Tender Negotiation Approval not found');

            return redirect(route('tenderNegotiationApprovals.index'));
        }

        return view('tender_negotiation_approvals.show')->with('tenderNegotiationApproval', $tenderNegotiationApproval);
    }

    /**
     * Show the form for editing the specified TenderNegotiationApproval.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $tenderNegotiationApproval = $this->tenderNegotiationApprovalRepository->findWithoutFail($id);

        if (empty($tenderNegotiationApproval)) {
            Flash::error('Tender Negotiation Approval not found');

            return redirect(route('tenderNegotiationApprovals.index'));
        }

        return view('tender_negotiation_approvals.edit')->with('tenderNegotiationApproval', $tenderNegotiationApproval);
    }

    /**
     * Update the specified TenderNegotiationApproval in storage.
     *
     * @param  int              $id
     * @param UpdateTenderNegotiationApprovalRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateTenderNegotiationApprovalRequest $request)
    {
        $tenderNegotiationApproval = $this->tenderNegotiationApprovalRepository->findWithoutFail($id);

        if (empty($tenderNegotiationApproval)) {
            Flash::error('Tender Negotiation Approval not found');

            return redirect(route('tenderNegotiationApprovals.index'));
        }

        $tenderNegotiationApproval = $this->tenderNegotiationApprovalRepository->update($request->all(), $id);

        Flash::success('Tender Negotiation Approval updated successfully.');

        return redirect(route('tenderNegotiationApprovals.index'));
    }

    /**
     * Remove the specified TenderNegotiationApproval from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $tenderNegotiationApproval = $this->tenderNegotiationApprovalRepository->findWithoutFail($id);

        if (empty($tenderNegotiationApproval)) {
            Flash::error('Tender Negotiation Approval not found');

            return redirect(route('tenderNegotiationApprovals.index'));
        }

        $this->tenderNegotiationApprovalRepository->delete($id);

        Flash::success('Tender Negotiation Approval deleted successfully.');

        return redirect(route('tenderNegotiationApprovals.index'));
    }
}
