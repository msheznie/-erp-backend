<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateSrmBidDocumentattachmentsRequest;
use App\Http\Requests\UpdateSrmBidDocumentattachmentsRequest;
use App\Repositories\SrmBidDocumentattachmentsRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class SrmBidDocumentattachmentsController extends AppBaseController
{
    /** @var  SrmBidDocumentattachmentsRepository */
    private $srmBidDocumentattachmentsRepository;

    public function __construct(SrmBidDocumentattachmentsRepository $srmBidDocumentattachmentsRepo)
    {
        $this->srmBidDocumentattachmentsRepository = $srmBidDocumentattachmentsRepo;
    }

    /**
     * Display a listing of the SrmBidDocumentattachments.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->srmBidDocumentattachmentsRepository->pushCriteria(new RequestCriteria($request));
        $srmBidDocumentattachments = $this->srmBidDocumentattachmentsRepository->all();

        return view('srm_bid_documentattachments.index')
            ->with('srmBidDocumentattachments', $srmBidDocumentattachments);
    }

    /**
     * Show the form for creating a new SrmBidDocumentattachments.
     *
     * @return Response
     */
    public function create()
    {
        return view('srm_bid_documentattachments.create');
    }

    /**
     * Store a newly created SrmBidDocumentattachments in storage.
     *
     * @param CreateSrmBidDocumentattachmentsRequest $request
     *
     * @return Response
     */
    public function store(CreateSrmBidDocumentattachmentsRequest $request)
    {
        $input = $request->all();

        $srmBidDocumentattachments = $this->srmBidDocumentattachmentsRepository->create($input);

        Flash::success('Srm Bid Documentattachments saved successfully.');

        return redirect(route('srmBidDocumentattachments.index'));
    }

    /**
     * Display the specified SrmBidDocumentattachments.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $srmBidDocumentattachments = $this->srmBidDocumentattachmentsRepository->findWithoutFail($id);

        if (empty($srmBidDocumentattachments)) {
            Flash::error('Srm Bid Documentattachments not found');

            return redirect(route('srmBidDocumentattachments.index'));
        }

        return view('srm_bid_documentattachments.show')->with('srmBidDocumentattachments', $srmBidDocumentattachments);
    }

    /**
     * Show the form for editing the specified SrmBidDocumentattachments.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $srmBidDocumentattachments = $this->srmBidDocumentattachmentsRepository->findWithoutFail($id);

        if (empty($srmBidDocumentattachments)) {
            Flash::error('Srm Bid Documentattachments not found');

            return redirect(route('srmBidDocumentattachments.index'));
        }

        return view('srm_bid_documentattachments.edit')->with('srmBidDocumentattachments', $srmBidDocumentattachments);
    }

    /**
     * Update the specified SrmBidDocumentattachments in storage.
     *
     * @param  int              $id
     * @param UpdateSrmBidDocumentattachmentsRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateSrmBidDocumentattachmentsRequest $request)
    {
        $srmBidDocumentattachments = $this->srmBidDocumentattachmentsRepository->findWithoutFail($id);

        if (empty($srmBidDocumentattachments)) {
            Flash::error('Srm Bid Documentattachments not found');

            return redirect(route('srmBidDocumentattachments.index'));
        }

        $srmBidDocumentattachments = $this->srmBidDocumentattachmentsRepository->update($request->all(), $id);

        Flash::success('Srm Bid Documentattachments updated successfully.');

        return redirect(route('srmBidDocumentattachments.index'));
    }

    /**
     * Remove the specified SrmBidDocumentattachments from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $srmBidDocumentattachments = $this->srmBidDocumentattachmentsRepository->findWithoutFail($id);

        if (empty($srmBidDocumentattachments)) {
            Flash::error('Srm Bid Documentattachments not found');

            return redirect(route('srmBidDocumentattachments.index'));
        }

        $this->srmBidDocumentattachmentsRepository->delete($id);

        Flash::success('Srm Bid Documentattachments deleted successfully.');

        return redirect(route('srmBidDocumentattachments.index'));
    }
}
