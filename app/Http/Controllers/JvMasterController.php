<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateJvMasterRequest;
use App\Http\Requests\UpdateJvMasterRequest;
use App\Repositories\JvMasterRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class JvMasterController extends AppBaseController
{
    /** @var  JvMasterRepository */
    private $jvMasterRepository;

    public function __construct(JvMasterRepository $jvMasterRepo)
    {
        $this->jvMasterRepository = $jvMasterRepo;
    }

    /**
     * Display a listing of the JvMaster.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->jvMasterRepository->pushCriteria(new RequestCriteria($request));
        $jvMasters = $this->jvMasterRepository->all();

        return view('jv_masters.index')
            ->with('jvMasters', $jvMasters);
    }

    /**
     * Show the form for creating a new JvMaster.
     *
     * @return Response
     */
    public function create()
    {
        return view('jv_masters.create');
    }

    /**
     * Store a newly created JvMaster in storage.
     *
     * @param CreateJvMasterRequest $request
     *
     * @return Response
     */
    public function store(CreateJvMasterRequest $request)
    {
        $input = $request->all();

        $jvMaster = $this->jvMasterRepository->create($input);

        Flash::success('Jv Master saved successfully.');

        return redirect(route('jvMasters.index'));
    }

    /**
     * Display the specified JvMaster.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $jvMaster = $this->jvMasterRepository->findWithoutFail($id);

        if (empty($jvMaster)) {
            Flash::error('Jv Master not found');

            return redirect(route('jvMasters.index'));
        }

        return view('jv_masters.show')->with('jvMaster', $jvMaster);
    }

    /**
     * Show the form for editing the specified JvMaster.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $jvMaster = $this->jvMasterRepository->findWithoutFail($id);

        if (empty($jvMaster)) {
            Flash::error('Jv Master not found');

            return redirect(route('jvMasters.index'));
        }

        return view('jv_masters.edit')->with('jvMaster', $jvMaster);
    }

    /**
     * Update the specified JvMaster in storage.
     *
     * @param  int              $id
     * @param UpdateJvMasterRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateJvMasterRequest $request)
    {
        $jvMaster = $this->jvMasterRepository->findWithoutFail($id);

        if (empty($jvMaster)) {
            Flash::error('Jv Master not found');

            return redirect(route('jvMasters.index'));
        }

        $jvMaster = $this->jvMasterRepository->update($request->all(), $id);

        Flash::success('Jv Master updated successfully.');

        return redirect(route('jvMasters.index'));
    }

    /**
     * Remove the specified JvMaster from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $jvMaster = $this->jvMasterRepository->findWithoutFail($id);

        if (empty($jvMaster)) {
            Flash::error('Jv Master not found');

            return redirect(route('jvMasters.index'));
        }

        $this->jvMasterRepository->delete($id);

        Flash::success('Jv Master deleted successfully.');

        return redirect(route('jvMasters.index'));
    }
}
