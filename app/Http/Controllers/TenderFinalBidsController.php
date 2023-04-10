<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateTenderFinalBidsRequest;
use App\Http\Requests\UpdateTenderFinalBidsRequest;
use App\Repositories\TenderFinalBidsRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class TenderFinalBidsController extends AppBaseController
{
    /** @var  TenderFinalBidsRepository */
    private $tenderFinalBidsRepository;

    public function __construct(TenderFinalBidsRepository $tenderFinalBidsRepo)
    {
        $this->tenderFinalBidsRepository = $tenderFinalBidsRepo;
    }

    /**
     * Display a listing of the TenderFinalBids.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->tenderFinalBidsRepository->pushCriteria(new RequestCriteria($request));
        $tenderFinalBids = $this->tenderFinalBidsRepository->all();

        return view('tender_final_bids.index')
            ->with('tenderFinalBids', $tenderFinalBids);
    }

    /**
     * Show the form for creating a new TenderFinalBids.
     *
     * @return Response
     */
    public function create()
    {
        return view('tender_final_bids.create');
    }

    /**
     * Store a newly created TenderFinalBids in storage.
     *
     * @param CreateTenderFinalBidsRequest $request
     *
     * @return Response
     */
    public function store(CreateTenderFinalBidsRequest $request)
    {
        $input = $request->all();

        $tenderFinalBids = $this->tenderFinalBidsRepository->create($input);

        Flash::success('Tender Final Bids saved successfully.');

        return redirect(route('tenderFinalBids.index'));
    }

    /**
     * Display the specified TenderFinalBids.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $tenderFinalBids = $this->tenderFinalBidsRepository->findWithoutFail($id);

        if (empty($tenderFinalBids)) {
            Flash::error('Tender Final Bids not found');

            return redirect(route('tenderFinalBids.index'));
        }

        return view('tender_final_bids.show')->with('tenderFinalBids', $tenderFinalBids);
    }

    /**
     * Show the form for editing the specified TenderFinalBids.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $tenderFinalBids = $this->tenderFinalBidsRepository->findWithoutFail($id);

        if (empty($tenderFinalBids)) {
            Flash::error('Tender Final Bids not found');

            return redirect(route('tenderFinalBids.index'));
        }

        return view('tender_final_bids.edit')->with('tenderFinalBids', $tenderFinalBids);
    }

    /**
     * Update the specified TenderFinalBids in storage.
     *
     * @param  int              $id
     * @param UpdateTenderFinalBidsRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateTenderFinalBidsRequest $request)
    {
        $tenderFinalBids = $this->tenderFinalBidsRepository->findWithoutFail($id);

        if (empty($tenderFinalBids)) {
            Flash::error('Tender Final Bids not found');

            return redirect(route('tenderFinalBids.index'));
        }

        $tenderFinalBids = $this->tenderFinalBidsRepository->update($request->all(), $id);

        Flash::success('Tender Final Bids updated successfully.');

        return redirect(route('tenderFinalBids.index'));
    }

    /**
     * Remove the specified TenderFinalBids from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $tenderFinalBids = $this->tenderFinalBidsRepository->findWithoutFail($id);

        if (empty($tenderFinalBids)) {
            Flash::error('Tender Final Bids not found');

            return redirect(route('tenderFinalBids.index'));
        }

        $this->tenderFinalBidsRepository->delete($id);

        Flash::success('Tender Final Bids deleted successfully.');

        return redirect(route('tenderFinalBids.index'));
    }
}
