<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateShiftDetailsRequest;
use App\Http\Requests\UpdateShiftDetailsRequest;
use App\Repositories\ShiftDetailsRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class ShiftDetailsController extends AppBaseController
{
    /** @var  ShiftDetailsRepository */
    private $shiftDetailsRepository;

    public function __construct(ShiftDetailsRepository $shiftDetailsRepo)
    {
        $this->shiftDetailsRepository = $shiftDetailsRepo;
    }

    /**
     * Display a listing of the ShiftDetails.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->shiftDetailsRepository->pushCriteria(new RequestCriteria($request));
        $shiftDetails = $this->shiftDetailsRepository->all();

        return view('shift_details.index')
            ->with('shiftDetails', $shiftDetails);
    }

    /**
     * Show the form for creating a new ShiftDetails.
     *
     * @return Response
     */
    public function create()
    {
        return view('shift_details.create');
    }

    /**
     * Store a newly created ShiftDetails in storage.
     *
     * @param CreateShiftDetailsRequest $request
     *
     * @return Response
     */
    public function store(CreateShiftDetailsRequest $request)
    {
        $input = $request->all();

        $shiftDetails = $this->shiftDetailsRepository->create($input);

        Flash::success('Shift Details saved successfully.');

        return redirect(route('shiftDetails.index'));
    }

    /**
     * Display the specified ShiftDetails.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $shiftDetails = $this->shiftDetailsRepository->findWithoutFail($id);

        if (empty($shiftDetails)) {
            Flash::error('Shift Details not found');

            return redirect(route('shiftDetails.index'));
        }

        return view('shift_details.show')->with('shiftDetails', $shiftDetails);
    }

    /**
     * Show the form for editing the specified ShiftDetails.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $shiftDetails = $this->shiftDetailsRepository->findWithoutFail($id);

        if (empty($shiftDetails)) {
            Flash::error('Shift Details not found');

            return redirect(route('shiftDetails.index'));
        }

        return view('shift_details.edit')->with('shiftDetails', $shiftDetails);
    }

    /**
     * Update the specified ShiftDetails in storage.
     *
     * @param  int              $id
     * @param UpdateShiftDetailsRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateShiftDetailsRequest $request)
    {
        $shiftDetails = $this->shiftDetailsRepository->findWithoutFail($id);

        if (empty($shiftDetails)) {
            Flash::error('Shift Details not found');

            return redirect(route('shiftDetails.index'));
        }

        $shiftDetails = $this->shiftDetailsRepository->update($request->all(), $id);

        Flash::success('Shift Details updated successfully.');

        return redirect(route('shiftDetails.index'));
    }

    /**
     * Remove the specified ShiftDetails from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $shiftDetails = $this->shiftDetailsRepository->findWithoutFail($id);

        if (empty($shiftDetails)) {
            Flash::error('Shift Details not found');

            return redirect(route('shiftDetails.index'));
        }

        $this->shiftDetailsRepository->delete($id);

        Flash::success('Shift Details deleted successfully.');

        return redirect(route('shiftDetails.index'));
    }
}
