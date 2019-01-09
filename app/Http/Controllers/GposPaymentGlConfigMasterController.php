<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateGposPaymentGlConfigMasterRequest;
use App\Http\Requests\UpdateGposPaymentGlConfigMasterRequest;
use App\Repositories\GposPaymentGlConfigMasterRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class GposPaymentGlConfigMasterController extends AppBaseController
{
    /** @var  GposPaymentGlConfigMasterRepository */
    private $gposPaymentGlConfigMasterRepository;

    public function __construct(GposPaymentGlConfigMasterRepository $gposPaymentGlConfigMasterRepo)
    {
        $this->gposPaymentGlConfigMasterRepository = $gposPaymentGlConfigMasterRepo;
    }

    /**
     * Display a listing of the GposPaymentGlConfigMaster.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->gposPaymentGlConfigMasterRepository->pushCriteria(new RequestCriteria($request));
        $gposPaymentGlConfigMasters = $this->gposPaymentGlConfigMasterRepository->all();

        return view('gpos_payment_gl_config_masters.index')
            ->with('gposPaymentGlConfigMasters', $gposPaymentGlConfigMasters);
    }

    /**
     * Show the form for creating a new GposPaymentGlConfigMaster.
     *
     * @return Response
     */
    public function create()
    {
        return view('gpos_payment_gl_config_masters.create');
    }

    /**
     * Store a newly created GposPaymentGlConfigMaster in storage.
     *
     * @param CreateGposPaymentGlConfigMasterRequest $request
     *
     * @return Response
     */
    public function store(CreateGposPaymentGlConfigMasterRequest $request)
    {
        $input = $request->all();

        $gposPaymentGlConfigMaster = $this->gposPaymentGlConfigMasterRepository->create($input);

        Flash::success('Gpos Payment Gl Config Master saved successfully.');

        return redirect(route('gposPaymentGlConfigMasters.index'));
    }

    /**
     * Display the specified GposPaymentGlConfigMaster.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $gposPaymentGlConfigMaster = $this->gposPaymentGlConfigMasterRepository->findWithoutFail($id);

        if (empty($gposPaymentGlConfigMaster)) {
            Flash::error('Gpos Payment Gl Config Master not found');

            return redirect(route('gposPaymentGlConfigMasters.index'));
        }

        return view('gpos_payment_gl_config_masters.show')->with('gposPaymentGlConfigMaster', $gposPaymentGlConfigMaster);
    }

    /**
     * Show the form for editing the specified GposPaymentGlConfigMaster.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $gposPaymentGlConfigMaster = $this->gposPaymentGlConfigMasterRepository->findWithoutFail($id);

        if (empty($gposPaymentGlConfigMaster)) {
            Flash::error('Gpos Payment Gl Config Master not found');

            return redirect(route('gposPaymentGlConfigMasters.index'));
        }

        return view('gpos_payment_gl_config_masters.edit')->with('gposPaymentGlConfigMaster', $gposPaymentGlConfigMaster);
    }

    /**
     * Update the specified GposPaymentGlConfigMaster in storage.
     *
     * @param  int              $id
     * @param UpdateGposPaymentGlConfigMasterRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateGposPaymentGlConfigMasterRequest $request)
    {
        $gposPaymentGlConfigMaster = $this->gposPaymentGlConfigMasterRepository->findWithoutFail($id);

        if (empty($gposPaymentGlConfigMaster)) {
            Flash::error('Gpos Payment Gl Config Master not found');

            return redirect(route('gposPaymentGlConfigMasters.index'));
        }

        $gposPaymentGlConfigMaster = $this->gposPaymentGlConfigMasterRepository->update($request->all(), $id);

        Flash::success('Gpos Payment Gl Config Master updated successfully.');

        return redirect(route('gposPaymentGlConfigMasters.index'));
    }

    /**
     * Remove the specified GposPaymentGlConfigMaster from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $gposPaymentGlConfigMaster = $this->gposPaymentGlConfigMasterRepository->findWithoutFail($id);

        if (empty($gposPaymentGlConfigMaster)) {
            Flash::error('Gpos Payment Gl Config Master not found');

            return redirect(route('gposPaymentGlConfigMasters.index'));
        }

        $this->gposPaymentGlConfigMasterRepository->delete($id);

        Flash::success('Gpos Payment Gl Config Master deleted successfully.');

        return redirect(route('gposPaymentGlConfigMasters.index'));
    }
}
