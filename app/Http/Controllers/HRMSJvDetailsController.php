<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateHRMSJvDetailsRequest;
use App\Http\Requests\UpdateHRMSJvDetailsRequest;
use App\Repositories\HRMSJvDetailsRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class HRMSJvDetailsController extends AppBaseController
{
    /** @var  HRMSJvDetailsRepository */
    private $hRMSJvDetailsRepository;

    public function __construct(HRMSJvDetailsRepository $hRMSJvDetailsRepo)
    {
        $this->hRMSJvDetailsRepository = $hRMSJvDetailsRepo;
    }

    /**
     * Display a listing of the HRMSJvDetails.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->hRMSJvDetailsRepository->pushCriteria(new RequestCriteria($request));
        $hRMSJvDetails = $this->hRMSJvDetailsRepository->all();

        return view('h_r_m_s_jv_details.index')
            ->with('hRMSJvDetails', $hRMSJvDetails);
    }

    /**
     * Show the form for creating a new HRMSJvDetails.
     *
     * @return Response
     */
    public function create()
    {
        return view('h_r_m_s_jv_details.create');
    }

    /**
     * Store a newly created HRMSJvDetails in storage.
     *
     * @param CreateHRMSJvDetailsRequest $request
     *
     * @return Response
     */
    public function store(CreateHRMSJvDetailsRequest $request)
    {
        $input = $request->all();

        $hRMSJvDetails = $this->hRMSJvDetailsRepository->create($input);

        Flash::success('H R M S Jv Details saved successfully.');

        return redirect(route('hRMSJvDetails.index'));
    }

    /**
     * Display the specified HRMSJvDetails.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $hRMSJvDetails = $this->hRMSJvDetailsRepository->findWithoutFail($id);

        if (empty($hRMSJvDetails)) {
            Flash::error('H R M S Jv Details not found');

            return redirect(route('hRMSJvDetails.index'));
        }

        return view('h_r_m_s_jv_details.show')->with('hRMSJvDetails', $hRMSJvDetails);
    }

    /**
     * Show the form for editing the specified HRMSJvDetails.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $hRMSJvDetails = $this->hRMSJvDetailsRepository->findWithoutFail($id);

        if (empty($hRMSJvDetails)) {
            Flash::error('H R M S Jv Details not found');

            return redirect(route('hRMSJvDetails.index'));
        }

        return view('h_r_m_s_jv_details.edit')->with('hRMSJvDetails', $hRMSJvDetails);
    }

    /**
     * Update the specified HRMSJvDetails in storage.
     *
     * @param  int              $id
     * @param UpdateHRMSJvDetailsRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateHRMSJvDetailsRequest $request)
    {
        $hRMSJvDetails = $this->hRMSJvDetailsRepository->findWithoutFail($id);

        if (empty($hRMSJvDetails)) {
            Flash::error('H R M S Jv Details not found');

            return redirect(route('hRMSJvDetails.index'));
        }

        $hRMSJvDetails = $this->hRMSJvDetailsRepository->update($request->all(), $id);

        Flash::success('H R M S Jv Details updated successfully.');

        return redirect(route('hRMSJvDetails.index'));
    }

    /**
     * Remove the specified HRMSJvDetails from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $hRMSJvDetails = $this->hRMSJvDetailsRepository->findWithoutFail($id);

        if (empty($hRMSJvDetails)) {
            Flash::error('H R M S Jv Details not found');

            return redirect(route('hRMSJvDetails.index'));
        }

        $this->hRMSJvDetailsRepository->delete($id);

        Flash::success('H R M S Jv Details deleted successfully.');

        return redirect(route('hRMSJvDetails.index'));
    }
}
