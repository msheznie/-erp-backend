<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateHRMSJvMasterRequest;
use App\Http\Requests\UpdateHRMSJvMasterRequest;
use App\Repositories\HRMSJvMasterRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class HRMSJvMasterController extends AppBaseController
{
    /** @var  HRMSJvMasterRepository */
    private $hRMSJvMasterRepository;

    public function __construct(HRMSJvMasterRepository $hRMSJvMasterRepo)
    {
        $this->hRMSJvMasterRepository = $hRMSJvMasterRepo;
    }

    /**
     * Display a listing of the HRMSJvMaster.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->hRMSJvMasterRepository->pushCriteria(new RequestCriteria($request));
        $hRMSJvMasters = $this->hRMSJvMasterRepository->all();

        return view('h_r_m_s_jv_masters.index')
            ->with('hRMSJvMasters', $hRMSJvMasters);
    }

    /**
     * Show the form for creating a new HRMSJvMaster.
     *
     * @return Response
     */
    public function create()
    {
        return view('h_r_m_s_jv_masters.create');
    }

    /**
     * Store a newly created HRMSJvMaster in storage.
     *
     * @param CreateHRMSJvMasterRequest $request
     *
     * @return Response
     */
    public function store(CreateHRMSJvMasterRequest $request)
    {
        $input = $request->all();

        $hRMSJvMaster = $this->hRMSJvMasterRepository->create($input);

        Flash::success('H R M S Jv Master saved successfully.');

        return redirect(route('hRMSJvMasters.index'));
    }

    /**
     * Display the specified HRMSJvMaster.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $hRMSJvMaster = $this->hRMSJvMasterRepository->findWithoutFail($id);

        if (empty($hRMSJvMaster)) {
            Flash::error('H R M S Jv Master not found');

            return redirect(route('hRMSJvMasters.index'));
        }

        return view('h_r_m_s_jv_masters.show')->with('hRMSJvMaster', $hRMSJvMaster);
    }

    /**
     * Show the form for editing the specified HRMSJvMaster.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $hRMSJvMaster = $this->hRMSJvMasterRepository->findWithoutFail($id);

        if (empty($hRMSJvMaster)) {
            Flash::error('H R M S Jv Master not found');

            return redirect(route('hRMSJvMasters.index'));
        }

        return view('h_r_m_s_jv_masters.edit')->with('hRMSJvMaster', $hRMSJvMaster);
    }

    /**
     * Update the specified HRMSJvMaster in storage.
     *
     * @param  int              $id
     * @param UpdateHRMSJvMasterRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateHRMSJvMasterRequest $request)
    {
        $hRMSJvMaster = $this->hRMSJvMasterRepository->findWithoutFail($id);

        if (empty($hRMSJvMaster)) {
            Flash::error('H R M S Jv Master not found');

            return redirect(route('hRMSJvMasters.index'));
        }

        $hRMSJvMaster = $this->hRMSJvMasterRepository->update($request->all(), $id);

        Flash::success('H R M S Jv Master updated successfully.');

        return redirect(route('hRMSJvMasters.index'));
    }

    /**
     * Remove the specified HRMSJvMaster from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $hRMSJvMaster = $this->hRMSJvMasterRepository->findWithoutFail($id);

        if (empty($hRMSJvMaster)) {
            Flash::error('H R M S Jv Master not found');

            return redirect(route('hRMSJvMasters.index'));
        }

        $this->hRMSJvMasterRepository->delete($id);

        Flash::success('H R M S Jv Master deleted successfully.');

        return redirect(route('hRMSJvMasters.index'));
    }
}
