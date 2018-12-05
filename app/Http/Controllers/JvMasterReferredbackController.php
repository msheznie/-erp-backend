<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateJvMasterReferredbackRequest;
use App\Http\Requests\UpdateJvMasterReferredbackRequest;
use App\Repositories\JvMasterReferredbackRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class JvMasterReferredbackController extends AppBaseController
{
    /** @var  JvMasterReferredbackRepository */
    private $jvMasterReferredbackRepository;

    public function __construct(JvMasterReferredbackRepository $jvMasterReferredbackRepo)
    {
        $this->jvMasterReferredbackRepository = $jvMasterReferredbackRepo;
    }

    /**
     * Display a listing of the JvMasterReferredback.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->jvMasterReferredbackRepository->pushCriteria(new RequestCriteria($request));
        $jvMasterReferredbacks = $this->jvMasterReferredbackRepository->all();

        return view('jv_master_referredbacks.index')
            ->with('jvMasterReferredbacks', $jvMasterReferredbacks);
    }

    /**
     * Show the form for creating a new JvMasterReferredback.
     *
     * @return Response
     */
    public function create()
    {
        return view('jv_master_referredbacks.create');
    }

    /**
     * Store a newly created JvMasterReferredback in storage.
     *
     * @param CreateJvMasterReferredbackRequest $request
     *
     * @return Response
     */
    public function store(CreateJvMasterReferredbackRequest $request)
    {
        $input = $request->all();

        $jvMasterReferredback = $this->jvMasterReferredbackRepository->create($input);

        Flash::success('Jv Master Referredback saved successfully.');

        return redirect(route('jvMasterReferredbacks.index'));
    }

    /**
     * Display the specified JvMasterReferredback.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $jvMasterReferredback = $this->jvMasterReferredbackRepository->findWithoutFail($id);

        if (empty($jvMasterReferredback)) {
            Flash::error('Jv Master Referredback not found');

            return redirect(route('jvMasterReferredbacks.index'));
        }

        return view('jv_master_referredbacks.show')->with('jvMasterReferredback', $jvMasterReferredback);
    }

    /**
     * Show the form for editing the specified JvMasterReferredback.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $jvMasterReferredback = $this->jvMasterReferredbackRepository->findWithoutFail($id);

        if (empty($jvMasterReferredback)) {
            Flash::error('Jv Master Referredback not found');

            return redirect(route('jvMasterReferredbacks.index'));
        }

        return view('jv_master_referredbacks.edit')->with('jvMasterReferredback', $jvMasterReferredback);
    }

    /**
     * Update the specified JvMasterReferredback in storage.
     *
     * @param  int              $id
     * @param UpdateJvMasterReferredbackRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateJvMasterReferredbackRequest $request)
    {
        $jvMasterReferredback = $this->jvMasterReferredbackRepository->findWithoutFail($id);

        if (empty($jvMasterReferredback)) {
            Flash::error('Jv Master Referredback not found');

            return redirect(route('jvMasterReferredbacks.index'));
        }

        $jvMasterReferredback = $this->jvMasterReferredbackRepository->update($request->all(), $id);

        Flash::success('Jv Master Referredback updated successfully.');

        return redirect(route('jvMasterReferredbacks.index'));
    }

    /**
     * Remove the specified JvMasterReferredback from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $jvMasterReferredback = $this->jvMasterReferredbackRepository->findWithoutFail($id);

        if (empty($jvMasterReferredback)) {
            Flash::error('Jv Master Referredback not found');

            return redirect(route('jvMasterReferredbacks.index'));
        }

        $this->jvMasterReferredbackRepository->delete($id);

        Flash::success('Jv Master Referredback deleted successfully.');

        return redirect(route('jvMasterReferredbacks.index'));
    }
}
