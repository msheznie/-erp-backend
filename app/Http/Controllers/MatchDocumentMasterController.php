<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateMatchDocumentMasterRequest;
use App\Http\Requests\UpdateMatchDocumentMasterRequest;
use App\Repositories\MatchDocumentMasterRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class MatchDocumentMasterController extends AppBaseController
{
    /** @var  MatchDocumentMasterRepository */
    private $matchDocumentMasterRepository;

    public function __construct(MatchDocumentMasterRepository $matchDocumentMasterRepo)
    {
        $this->matchDocumentMasterRepository = $matchDocumentMasterRepo;
    }

    /**
     * Display a listing of the MatchDocumentMaster.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->matchDocumentMasterRepository->pushCriteria(new RequestCriteria($request));
        $matchDocumentMasters = $this->matchDocumentMasterRepository->all();

        return view('match_document_masters.index')
            ->with('matchDocumentMasters', $matchDocumentMasters);
    }

    /**
     * Show the form for creating a new MatchDocumentMaster.
     *
     * @return Response
     */
    public function create()
    {
        return view('match_document_masters.create');
    }

    /**
     * Store a newly created MatchDocumentMaster in storage.
     *
     * @param CreateMatchDocumentMasterRequest $request
     *
     * @return Response
     */
    public function store(CreateMatchDocumentMasterRequest $request)
    {
        $input = $request->all();

        $matchDocumentMaster = $this->matchDocumentMasterRepository->create($input);

        Flash::success('Match Document Master saved successfully.');

        return redirect(route('matchDocumentMasters.index'));
    }

    /**
     * Display the specified MatchDocumentMaster.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $matchDocumentMaster = $this->matchDocumentMasterRepository->findWithoutFail($id);

        if (empty($matchDocumentMaster)) {
            Flash::error('Match Document Master not found');

            return redirect(route('matchDocumentMasters.index'));
        }

        return view('match_document_masters.show')->with('matchDocumentMaster', $matchDocumentMaster);
    }

    /**
     * Show the form for editing the specified MatchDocumentMaster.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $matchDocumentMaster = $this->matchDocumentMasterRepository->findWithoutFail($id);

        if (empty($matchDocumentMaster)) {
            Flash::error('Match Document Master not found');

            return redirect(route('matchDocumentMasters.index'));
        }

        return view('match_document_masters.edit')->with('matchDocumentMaster', $matchDocumentMaster);
    }

    /**
     * Update the specified MatchDocumentMaster in storage.
     *
     * @param  int              $id
     * @param UpdateMatchDocumentMasterRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateMatchDocumentMasterRequest $request)
    {
        $matchDocumentMaster = $this->matchDocumentMasterRepository->findWithoutFail($id);

        if (empty($matchDocumentMaster)) {
            Flash::error('Match Document Master not found');

            return redirect(route('matchDocumentMasters.index'));
        }

        $matchDocumentMaster = $this->matchDocumentMasterRepository->update($request->all(), $id);

        Flash::success('Match Document Master updated successfully.');

        return redirect(route('matchDocumentMasters.index'));
    }

    /**
     * Remove the specified MatchDocumentMaster from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $matchDocumentMaster = $this->matchDocumentMasterRepository->findWithoutFail($id);

        if (empty($matchDocumentMaster)) {
            Flash::error('Match Document Master not found');

            return redirect(route('matchDocumentMasters.index'));
        }

        $this->matchDocumentMasterRepository->delete($id);

        Flash::success('Match Document Master deleted successfully.');

        return redirect(route('matchDocumentMasters.index'));
    }
}
