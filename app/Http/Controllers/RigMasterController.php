<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateRigMasterRequest;
use App\Http\Requests\UpdateRigMasterRequest;
use App\Repositories\RigMasterRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class RigMasterController extends AppBaseController
{
    /** @var  RigMasterRepository */
    private $rigMasterRepository;

    public function __construct(RigMasterRepository $rigMasterRepo)
    {
        $this->rigMasterRepository = $rigMasterRepo;
    }

    /**
     * Display a listing of the RigMaster.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->rigMasterRepository->pushCriteria(new RequestCriteria($request));
        $rigMasters = $this->rigMasterRepository->all();

        return view('rig_masters.index')
            ->with('rigMasters', $rigMasters);
    }

    /**
     * Show the form for creating a new RigMaster.
     *
     * @return Response
     */
    public function create()
    {
        return view('rig_masters.create');
    }

    /**
     * Store a newly created RigMaster in storage.
     *
     * @param CreateRigMasterRequest $request
     *
     * @return Response
     */
    public function store(CreateRigMasterRequest $request)
    {
        $input = $request->all();

        $rigMaster = $this->rigMasterRepository->create($input);

        Flash::success('Rig Master saved successfully.');

        return redirect(route('rigMasters.index'));
    }

    /**
     * Display the specified RigMaster.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $rigMaster = $this->rigMasterRepository->findWithoutFail($id);

        if (empty($rigMaster)) {
            Flash::error('Rig Master not found');

            return redirect(route('rigMasters.index'));
        }

        return view('rig_masters.show')->with('rigMaster', $rigMaster);
    }

    /**
     * Show the form for editing the specified RigMaster.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $rigMaster = $this->rigMasterRepository->findWithoutFail($id);

        if (empty($rigMaster)) {
            Flash::error('Rig Master not found');

            return redirect(route('rigMasters.index'));
        }

        return view('rig_masters.edit')->with('rigMaster', $rigMaster);
    }

    /**
     * Update the specified RigMaster in storage.
     *
     * @param  int              $id
     * @param UpdateRigMasterRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateRigMasterRequest $request)
    {
        $rigMaster = $this->rigMasterRepository->findWithoutFail($id);

        if (empty($rigMaster)) {
            Flash::error('Rig Master not found');

            return redirect(route('rigMasters.index'));
        }

        $rigMaster = $this->rigMasterRepository->update($request->all(), $id);

        Flash::success('Rig Master updated successfully.');

        return redirect(route('rigMasters.index'));
    }

    /**
     * Remove the specified RigMaster from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $rigMaster = $this->rigMasterRepository->findWithoutFail($id);

        if (empty($rigMaster)) {
            Flash::error('Rig Master not found');

            return redirect(route('rigMasters.index'));
        }

        $this->rigMasterRepository->delete($id);

        Flash::success('Rig Master deleted successfully.');

        return redirect(route('rigMasters.index'));
    }
}
