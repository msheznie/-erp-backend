<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateGRVDetailsRequest;
use App\Http\Requests\UpdateGRVDetailsRequest;
use App\Repositories\GRVDetailsRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class GRVDetailsController extends AppBaseController
{
    /** @var  GRVDetailsRepository */
    private $gRVDetailsRepository;

    public function __construct(GRVDetailsRepository $gRVDetailsRepo)
    {
        $this->gRVDetailsRepository = $gRVDetailsRepo;
    }

    /**
     * Display a listing of the GRVDetails.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->gRVDetailsRepository->pushCriteria(new RequestCriteria($request));
        $gRVDetails = $this->gRVDetailsRepository->all();

        return view('g_r_v_details.index')
            ->with('gRVDetails', $gRVDetails);
    }

    /**
     * Show the form for creating a new GRVDetails.
     *
     * @return Response
     */
    public function create()
    {
        return view('g_r_v_details.create');
    }

    /**
     * Store a newly created GRVDetails in storage.
     *
     * @param CreateGRVDetailsRequest $request
     *
     * @return Response
     */
    public function store(CreateGRVDetailsRequest $request)
    {
        $input = $request->all();

        $gRVDetails = $this->gRVDetailsRepository->create($input);

        Flash::success('G R V Details saved successfully.');

        return redirect(route('gRVDetails.index'));
    }

    /**
     * Display the specified GRVDetails.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $gRVDetails = $this->gRVDetailsRepository->findWithoutFail($id);

        if (empty($gRVDetails)) {
            Flash::error('G R V Details not found');

            return redirect(route('gRVDetails.index'));
        }

        return view('g_r_v_details.show')->with('gRVDetails', $gRVDetails);
    }

    /**
     * Show the form for editing the specified GRVDetails.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $gRVDetails = $this->gRVDetailsRepository->findWithoutFail($id);

        if (empty($gRVDetails)) {
            Flash::error('G R V Details not found');

            return redirect(route('gRVDetails.index'));
        }

        return view('g_r_v_details.edit')->with('gRVDetails', $gRVDetails);
    }

    /**
     * Update the specified GRVDetails in storage.
     *
     * @param  int              $id
     * @param UpdateGRVDetailsRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateGRVDetailsRequest $request)
    {
        $gRVDetails = $this->gRVDetailsRepository->findWithoutFail($id);

        if (empty($gRVDetails)) {
            Flash::error('G R V Details not found');

            return redirect(route('gRVDetails.index'));
        }

        $gRVDetails = $this->gRVDetailsRepository->update($request->all(), $id);

        Flash::success('G R V Details updated successfully.');

        return redirect(route('gRVDetails.index'));
    }

    /**
     * Remove the specified GRVDetails from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $gRVDetails = $this->gRVDetailsRepository->findWithoutFail($id);

        if (empty($gRVDetails)) {
            Flash::error('G R V Details not found');

            return redirect(route('gRVDetails.index'));
        }

        $this->gRVDetailsRepository->delete($id);

        Flash::success('G R V Details deleted successfully.');

        return redirect(route('gRVDetails.index'));
    }
}
