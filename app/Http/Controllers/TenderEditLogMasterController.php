<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateTenderEditLogMasterRequest;
use App\Http\Requests\UpdateTenderEditLogMasterRequest;
use App\Repositories\TenderEditLogMasterRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class TenderEditLogMasterController extends AppBaseController
{
    /** @var  TenderEditLogMasterRepository */
    private $tenderEditLogMasterRepository;

    public function __construct(TenderEditLogMasterRepository $tenderEditLogMasterRepo)
    {
        $this->tenderEditLogMasterRepository = $tenderEditLogMasterRepo;
    }

    /**
     * Display a listing of the TenderEditLogMaster.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->tenderEditLogMasterRepository->pushCriteria(new RequestCriteria($request));
        $tenderEditLogMasters = $this->tenderEditLogMasterRepository->all();

        return view('tender_edit_log_masters.index')
            ->with('tenderEditLogMasters', $tenderEditLogMasters);
    }

    /**
     * Show the form for creating a new TenderEditLogMaster.
     *
     * @return Response
     */
    public function create()
    {
        return view('tender_edit_log_masters.create');
    }

    /**
     * Store a newly created TenderEditLogMaster in storage.
     *
     * @param CreateTenderEditLogMasterRequest $request
     *
     * @return Response
     */
    public function store(CreateTenderEditLogMasterRequest $request)
    {
        $input = $request->all();

        $tenderEditLogMaster = $this->tenderEditLogMasterRepository->create($input);

        Flash::success('Tender Edit Log Master saved successfully.');

        return redirect(route('tenderEditLogMasters.index'));
    }

    /**
     * Display the specified TenderEditLogMaster.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $tenderEditLogMaster = $this->tenderEditLogMasterRepository->findWithoutFail($id);

        if (empty($tenderEditLogMaster)) {
            Flash::error('Tender Edit Log Master not found');

            return redirect(route('tenderEditLogMasters.index'));
        }

        return view('tender_edit_log_masters.show')->with('tenderEditLogMaster', $tenderEditLogMaster);
    }

    /**
     * Show the form for editing the specified TenderEditLogMaster.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $tenderEditLogMaster = $this->tenderEditLogMasterRepository->findWithoutFail($id);

        if (empty($tenderEditLogMaster)) {
            Flash::error('Tender Edit Log Master not found');

            return redirect(route('tenderEditLogMasters.index'));
        }

        return view('tender_edit_log_masters.edit')->with('tenderEditLogMaster', $tenderEditLogMaster);
    }

    /**
     * Update the specified TenderEditLogMaster in storage.
     *
     * @param  int              $id
     * @param UpdateTenderEditLogMasterRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateTenderEditLogMasterRequest $request)
    {
        $tenderEditLogMaster = $this->tenderEditLogMasterRepository->findWithoutFail($id);

        if (empty($tenderEditLogMaster)) {
            Flash::error('Tender Edit Log Master not found');

            return redirect(route('tenderEditLogMasters.index'));
        }

        $tenderEditLogMaster = $this->tenderEditLogMasterRepository->update($request->all(), $id);

        Flash::success('Tender Edit Log Master updated successfully.');

        return redirect(route('tenderEditLogMasters.index'));
    }

    /**
     * Remove the specified TenderEditLogMaster from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $tenderEditLogMaster = $this->tenderEditLogMasterRepository->findWithoutFail($id);

        if (empty($tenderEditLogMaster)) {
            Flash::error('Tender Edit Log Master not found');

            return redirect(route('tenderEditLogMasters.index'));
        }

        $this->tenderEditLogMasterRepository->delete($id);

        Flash::success('Tender Edit Log Master deleted successfully.');

        return redirect(route('tenderEditLogMasters.index'));
    }
}
