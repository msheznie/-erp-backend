<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\CreateTenderNegotiationAreaRequest;
use App\Http\Requests\UpdateTenderNegotiationAreaRequest;
use App\Repositories\TenderNegotiationAreaRepository;
use App\Http\Controllers\AppBaseController;
use App\Models\SupplierTenderNegotiation;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class TenderNegotiationAreaController extends AppBaseController
{
    /** @var  TenderNegotiationAreaRepository */
    private $tenderNegotiationAreaRepository;

    public function __construct(TenderNegotiationAreaRepository $tenderNegotiationAreaRepo)
    {
        $this->tenderNegotiationAreaRepository = $tenderNegotiationAreaRepo;
    }

    /**
     * Display a listing of the TenderNegotiationArea.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->tenderNegotiationAreaRepository->pushCriteria(new RequestCriteria($request));
        $tenderNegotiationAreas = $this->tenderNegotiationAreaRepository->all();

        return view('tender_negotiation_areas.index')
            ->with('tenderNegotiationAreas', $tenderNegotiationAreas);
    }


    /**
     * Store a newly created TenderNegotiationArea in storage.
     *
     * @param CreateTenderNegotiationAreaRequest $request
     *
     * @return Response
     */
    public function store(CreateTenderNegotiationAreaRequest $request)
    {
        $input = $request->all();
        $areas =  $this->tenderNegotiationAreaRepository->where('tender_negotiation_id',$input['tender_negotiation_id'])->delete();

        $tenderNegotiationArea = $this->tenderNegotiationAreaRepository->create($input);

        if(!$tenderNegotiationArea) {
            return $this->sendError(trans('srm_ranking.tender_negotiation_area_not_found'), 404);
        }

        return $this->sendResponse($tenderNegotiationArea->toArray(), trans('srm_ranking.tender_negotiation_area_added'));

    }

    /**
     * Display the specified TenderNegotiationArea.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $tenderNegotiationArea = $this->tenderNegotiationAreaRepository->findWithoutFail($id);

        if (empty($tenderNegotiationArea)) {
            Flash::error('Tender Negotiation Area not found');

            return redirect(route('tenderNegotiationAreas.index'));
        }

        return view('tender_negotiation_areas.show')->with('tenderNegotiationArea', $tenderNegotiationArea);
    }

    /**
     * Show the form for editing the specified TenderNegotiationArea.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $tenderNegotiationArea = $this->tenderNegotiationAreaRepository->findWithoutFail($id);

        if (empty($tenderNegotiationArea)) {
            Flash::error('Tender Negotiation Area not found');

            return redirect(route('tenderNegotiationAreas.index'));
        }

        return view('tender_negotiation_areas.edit')->with('tenderNegotiationArea', $tenderNegotiationArea);
    }

    /**
     * Update the specified TenderNegotiationArea in storage.
     *
     * @param  int              $id
     * @param UpdateTenderNegotiationAreaRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateTenderNegotiationAreaRequest $request)
    {
        $tenderNegotiationArea = $this->tenderNegotiationAreaRepository->findWithoutFail($id);

        if (empty($tenderNegotiationArea)) {
            return $this->sendError('Tender Negotiation data not found', 404);
        }

        $tenderNegotiationArea = $this->tenderNegotiationAreaRepository->update($request->all(), $id);

        return $this->sendResponse($tenderNegotiationArea->toArray(), 'Tender negotiation area updated successfully');

    }

    /**
     * Remove the specified TenderNegotiationArea from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $tenderNegotiationArea = $this->tenderNegotiationAreaRepository->findWithoutFail($id);

        if (empty($tenderNegotiationArea)) {
            Flash::error('Tender Negotiation Area not found');

            return redirect(route('tenderNegotiationAreas.index'));
        }

        $this->tenderNegotiationAreaRepository->delete($id);

        Flash::success('Tender Negotiation Area deleted successfully.');

        return redirect(route('tenderNegotiationAreas.index'));
    }

    public function getSelectedAreas(Request $request) {

        $input = $request->input();
        $tenderNegotiationArea = $this->tenderNegotiationAreaRepository->getTenderNegotiationAreaBySupplierNegotiationID($input['tenderNegotiationID']);

        if(!$tenderNegotiationArea) {
            return $this->sendError('Tender negotiation area data not found', 404);
        }
        return $this->sendResponse($tenderNegotiationArea->toArray(), 'Tender negotiation area retereived successfully');

    }
}
