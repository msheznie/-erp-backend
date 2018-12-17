<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateGrvMasterRefferedbackRequest;
use App\Http\Requests\UpdateGrvMasterRefferedbackRequest;
use App\Repositories\GrvMasterRefferedbackRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class GrvMasterRefferedbackController extends AppBaseController
{
    /** @var  GrvMasterRefferedbackRepository */
    private $grvMasterRefferedbackRepository;

    public function __construct(GrvMasterRefferedbackRepository $grvMasterRefferedbackRepo)
    {
        $this->grvMasterRefferedbackRepository = $grvMasterRefferedbackRepo;
    }

    /**
     * Display a listing of the GrvMasterRefferedback.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->grvMasterRefferedbackRepository->pushCriteria(new RequestCriteria($request));
        $grvMasterRefferedbacks = $this->grvMasterRefferedbackRepository->all();

        return view('grv_master_refferedbacks.index')
            ->with('grvMasterRefferedbacks', $grvMasterRefferedbacks);
    }

    /**
     * Show the form for creating a new GrvMasterRefferedback.
     *
     * @return Response
     */
    public function create()
    {
        return view('grv_master_refferedbacks.create');
    }

    /**
     * Store a newly created GrvMasterRefferedback in storage.
     *
     * @param CreateGrvMasterRefferedbackRequest $request
     *
     * @return Response
     */
    public function store(CreateGrvMasterRefferedbackRequest $request)
    {
        $input = $request->all();

        $grvMasterRefferedback = $this->grvMasterRefferedbackRepository->create($input);

        Flash::success('Grv Master Refferedback saved successfully.');

        return redirect(route('grvMasterRefferedbacks.index'));
    }

    /**
     * Display the specified GrvMasterRefferedback.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $grvMasterRefferedback = $this->grvMasterRefferedbackRepository->findWithoutFail($id);

        if (empty($grvMasterRefferedback)) {
            Flash::error('Grv Master Refferedback not found');

            return redirect(route('grvMasterRefferedbacks.index'));
        }

        return view('grv_master_refferedbacks.show')->with('grvMasterRefferedback', $grvMasterRefferedback);
    }

    /**
     * Show the form for editing the specified GrvMasterRefferedback.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $grvMasterRefferedback = $this->grvMasterRefferedbackRepository->findWithoutFail($id);

        if (empty($grvMasterRefferedback)) {
            Flash::error('Grv Master Refferedback not found');

            return redirect(route('grvMasterRefferedbacks.index'));
        }

        return view('grv_master_refferedbacks.edit')->with('grvMasterRefferedback', $grvMasterRefferedback);
    }

    /**
     * Update the specified GrvMasterRefferedback in storage.
     *
     * @param  int              $id
     * @param UpdateGrvMasterRefferedbackRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateGrvMasterRefferedbackRequest $request)
    {
        $grvMasterRefferedback = $this->grvMasterRefferedbackRepository->findWithoutFail($id);

        if (empty($grvMasterRefferedback)) {
            Flash::error('Grv Master Refferedback not found');

            return redirect(route('grvMasterRefferedbacks.index'));
        }

        $grvMasterRefferedback = $this->grvMasterRefferedbackRepository->update($request->all(), $id);

        Flash::success('Grv Master Refferedback updated successfully.');

        return redirect(route('grvMasterRefferedbacks.index'));
    }

    /**
     * Remove the specified GrvMasterRefferedback from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $grvMasterRefferedback = $this->grvMasterRefferedbackRepository->findWithoutFail($id);

        if (empty($grvMasterRefferedback)) {
            Flash::error('Grv Master Refferedback not found');

            return redirect(route('grvMasterRefferedbacks.index'));
        }

        $this->grvMasterRefferedbackRepository->delete($id);

        Flash::success('Grv Master Refferedback deleted successfully.');

        return redirect(route('grvMasterRefferedbacks.index'));
    }
}
