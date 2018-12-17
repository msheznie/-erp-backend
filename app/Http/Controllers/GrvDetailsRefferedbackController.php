<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateGrvDetailsRefferedbackRequest;
use App\Http\Requests\UpdateGrvDetailsRefferedbackRequest;
use App\Repositories\GrvDetailsRefferedbackRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class GrvDetailsRefferedbackController extends AppBaseController
{
    /** @var  GrvDetailsRefferedbackRepository */
    private $grvDetailsRefferedbackRepository;

    public function __construct(GrvDetailsRefferedbackRepository $grvDetailsRefferedbackRepo)
    {
        $this->grvDetailsRefferedbackRepository = $grvDetailsRefferedbackRepo;
    }

    /**
     * Display a listing of the GrvDetailsRefferedback.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->grvDetailsRefferedbackRepository->pushCriteria(new RequestCriteria($request));
        $grvDetailsRefferedbacks = $this->grvDetailsRefferedbackRepository->all();

        return view('grv_details_refferedbacks.index')
            ->with('grvDetailsRefferedbacks', $grvDetailsRefferedbacks);
    }

    /**
     * Show the form for creating a new GrvDetailsRefferedback.
     *
     * @return Response
     */
    public function create()
    {
        return view('grv_details_refferedbacks.create');
    }

    /**
     * Store a newly created GrvDetailsRefferedback in storage.
     *
     * @param CreateGrvDetailsRefferedbackRequest $request
     *
     * @return Response
     */
    public function store(CreateGrvDetailsRefferedbackRequest $request)
    {
        $input = $request->all();

        $grvDetailsRefferedback = $this->grvDetailsRefferedbackRepository->create($input);

        Flash::success('Grv Details Refferedback saved successfully.');

        return redirect(route('grvDetailsRefferedbacks.index'));
    }

    /**
     * Display the specified GrvDetailsRefferedback.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $grvDetailsRefferedback = $this->grvDetailsRefferedbackRepository->findWithoutFail($id);

        if (empty($grvDetailsRefferedback)) {
            Flash::error('Grv Details Refferedback not found');

            return redirect(route('grvDetailsRefferedbacks.index'));
        }

        return view('grv_details_refferedbacks.show')->with('grvDetailsRefferedback', $grvDetailsRefferedback);
    }

    /**
     * Show the form for editing the specified GrvDetailsRefferedback.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $grvDetailsRefferedback = $this->grvDetailsRefferedbackRepository->findWithoutFail($id);

        if (empty($grvDetailsRefferedback)) {
            Flash::error('Grv Details Refferedback not found');

            return redirect(route('grvDetailsRefferedbacks.index'));
        }

        return view('grv_details_refferedbacks.edit')->with('grvDetailsRefferedback', $grvDetailsRefferedback);
    }

    /**
     * Update the specified GrvDetailsRefferedback in storage.
     *
     * @param  int              $id
     * @param UpdateGrvDetailsRefferedbackRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateGrvDetailsRefferedbackRequest $request)
    {
        $grvDetailsRefferedback = $this->grvDetailsRefferedbackRepository->findWithoutFail($id);

        if (empty($grvDetailsRefferedback)) {
            Flash::error('Grv Details Refferedback not found');

            return redirect(route('grvDetailsRefferedbacks.index'));
        }

        $grvDetailsRefferedback = $this->grvDetailsRefferedbackRepository->update($request->all(), $id);

        Flash::success('Grv Details Refferedback updated successfully.');

        return redirect(route('grvDetailsRefferedbacks.index'));
    }

    /**
     * Remove the specified GrvDetailsRefferedback from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $grvDetailsRefferedback = $this->grvDetailsRefferedbackRepository->findWithoutFail($id);

        if (empty($grvDetailsRefferedback)) {
            Flash::error('Grv Details Refferedback not found');

            return redirect(route('grvDetailsRefferedbacks.index'));
        }

        $this->grvDetailsRefferedbackRepository->delete($id);

        Flash::success('Grv Details Refferedback deleted successfully.');

        return redirect(route('grvDetailsRefferedbacks.index'));
    }
}
