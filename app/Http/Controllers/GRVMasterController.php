<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateGRVMasterRequest;
use App\Http\Requests\UpdateGRVMasterRequest;
use App\Repositories\GRVMasterRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class GRVMasterController extends AppBaseController
{
    /** @var  GRVMasterRepository */
    private $gRVMasterRepository;

    public function __construct(GRVMasterRepository $gRVMasterRepo)
    {
        $this->gRVMasterRepository = $gRVMasterRepo;
    }

    /**
     * Display a listing of the GRVMaster.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->gRVMasterRepository->pushCriteria(new RequestCriteria($request));
        $gRVMasters = $this->gRVMasterRepository->all();

        return view('g_r_v_masters.index')
            ->with('gRVMasters', $gRVMasters);
    }

    /**
     * Show the form for creating a new GRVMaster.
     *
     * @return Response
     */
    public function create()
    {
        return view('g_r_v_masters.create');
    }

    /**
     * Store a newly created GRVMaster in storage.
     *
     * @param CreateGRVMasterRequest $request
     *
     * @return Response
     */
    public function store(CreateGRVMasterRequest $request)
    {
        $input = $request->all();

        $gRVMaster = $this->gRVMasterRepository->create($input);

        Flash::success('G R V Master saved successfully.');

        return redirect(route('gRVMasters.index'));
    }

    /**
     * Display the specified GRVMaster.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $gRVMaster = $this->gRVMasterRepository->findWithoutFail($id);

        if (empty($gRVMaster)) {
            Flash::error('G R V Master not found');

            return redirect(route('gRVMasters.index'));
        }

        return view('g_r_v_masters.show')->with('gRVMaster', $gRVMaster);
    }

    /**
     * Show the form for editing the specified GRVMaster.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $gRVMaster = $this->gRVMasterRepository->findWithoutFail($id);

        if (empty($gRVMaster)) {
            Flash::error('G R V Master not found');

            return redirect(route('gRVMasters.index'));
        }

        return view('g_r_v_masters.edit')->with('gRVMaster', $gRVMaster);
    }

    /**
     * Update the specified GRVMaster in storage.
     *
     * @param  int              $id
     * @param UpdateGRVMasterRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateGRVMasterRequest $request)
    {
        $gRVMaster = $this->gRVMasterRepository->findWithoutFail($id);

        if (empty($gRVMaster)) {
            Flash::error('G R V Master not found');

            return redirect(route('gRVMasters.index'));
        }

        $gRVMaster = $this->gRVMasterRepository->update($request->all(), $id);

        Flash::success('G R V Master updated successfully.');

        return redirect(route('gRVMasters.index'));
    }

    /**
     * Remove the specified GRVMaster from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $gRVMaster = $this->gRVMasterRepository->findWithoutFail($id);

        if (empty($gRVMaster)) {
            Flash::error('G R V Master not found');

            return redirect(route('gRVMasters.index'));
        }

        $this->gRVMasterRepository->delete($id);

        Flash::success('G R V Master deleted successfully.');

        return redirect(route('gRVMasters.index'));
    }
}
