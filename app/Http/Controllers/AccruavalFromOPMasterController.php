<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateAccruavalFromOPMasterRequest;
use App\Http\Requests\UpdateAccruavalFromOPMasterRequest;
use App\Repositories\AccruavalFromOPMasterRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class AccruavalFromOPMasterController extends AppBaseController
{
    /** @var  AccruavalFromOPMasterRepository */
    private $accruavalFromOPMasterRepository;

    public function __construct(AccruavalFromOPMasterRepository $accruavalFromOPMasterRepo)
    {
        $this->accruavalFromOPMasterRepository = $accruavalFromOPMasterRepo;
    }

    /**
     * Display a listing of the AccruavalFromOPMaster.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->accruavalFromOPMasterRepository->pushCriteria(new RequestCriteria($request));
        $accruavalFromOPMasters = $this->accruavalFromOPMasterRepository->all();

        return view('accruaval_from_o_p_masters.index')
            ->with('accruavalFromOPMasters', $accruavalFromOPMasters);
    }

    /**
     * Show the form for creating a new AccruavalFromOPMaster.
     *
     * @return Response
     */
    public function create()
    {
        return view('accruaval_from_o_p_masters.create');
    }

    /**
     * Store a newly created AccruavalFromOPMaster in storage.
     *
     * @param CreateAccruavalFromOPMasterRequest $request
     *
     * @return Response
     */
    public function store(CreateAccruavalFromOPMasterRequest $request)
    {
        $input = $request->all();

        $accruavalFromOPMaster = $this->accruavalFromOPMasterRepository->create($input);

        Flash::success('Accruaval From O P Master saved successfully.');

        return redirect(route('accruavalFromOPMasters.index'));
    }

    /**
     * Display the specified AccruavalFromOPMaster.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $accruavalFromOPMaster = $this->accruavalFromOPMasterRepository->findWithoutFail($id);

        if (empty($accruavalFromOPMaster)) {
            Flash::error('Accruaval From O P Master not found');

            return redirect(route('accruavalFromOPMasters.index'));
        }

        return view('accruaval_from_o_p_masters.show')->with('accruavalFromOPMaster', $accruavalFromOPMaster);
    }

    /**
     * Show the form for editing the specified AccruavalFromOPMaster.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $accruavalFromOPMaster = $this->accruavalFromOPMasterRepository->findWithoutFail($id);

        if (empty($accruavalFromOPMaster)) {
            Flash::error('Accruaval From O P Master not found');

            return redirect(route('accruavalFromOPMasters.index'));
        }

        return view('accruaval_from_o_p_masters.edit')->with('accruavalFromOPMaster', $accruavalFromOPMaster);
    }

    /**
     * Update the specified AccruavalFromOPMaster in storage.
     *
     * @param  int              $id
     * @param UpdateAccruavalFromOPMasterRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateAccruavalFromOPMasterRequest $request)
    {
        $accruavalFromOPMaster = $this->accruavalFromOPMasterRepository->findWithoutFail($id);

        if (empty($accruavalFromOPMaster)) {
            Flash::error('Accruaval From O P Master not found');

            return redirect(route('accruavalFromOPMasters.index'));
        }

        $accruavalFromOPMaster = $this->accruavalFromOPMasterRepository->update($request->all(), $id);

        Flash::success('Accruaval From O P Master updated successfully.');

        return redirect(route('accruavalFromOPMasters.index'));
    }

    /**
     * Remove the specified AccruavalFromOPMaster from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $accruavalFromOPMaster = $this->accruavalFromOPMasterRepository->findWithoutFail($id);

        if (empty($accruavalFromOPMaster)) {
            Flash::error('Accruaval From O P Master not found');

            return redirect(route('accruavalFromOPMasters.index'));
        }

        $this->accruavalFromOPMasterRepository->delete($id);

        Flash::success('Accruaval From O P Master deleted successfully.');

        return redirect(route('accruavalFromOPMasters.index'));
    }
}
