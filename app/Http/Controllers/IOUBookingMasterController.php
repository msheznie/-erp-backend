<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateIOUBookingMasterRequest;
use App\Http\Requests\UpdateIOUBookingMasterRequest;
use App\Repositories\IOUBookingMasterRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class IOUBookingMasterController extends AppBaseController
{
    /** @var  IOUBookingMasterRepository */
    private $iOUBookingMasterRepository;

    public function __construct(IOUBookingMasterRepository $iOUBookingMasterRepo)
    {
        $this->iOUBookingMasterRepository = $iOUBookingMasterRepo;
    }

    /**
     * Display a listing of the IOUBookingMaster.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->iOUBookingMasterRepository->pushCriteria(new RequestCriteria($request));
        $iOUBookingMasters = $this->iOUBookingMasterRepository->all();

        return view('i_o_u_booking_masters.index')
            ->with('iOUBookingMasters', $iOUBookingMasters);
    }

    /**
     * Show the form for creating a new IOUBookingMaster.
     *
     * @return Response
     */
    public function create()
    {
        return view('i_o_u_booking_masters.create');
    }

    /**
     * Store a newly created IOUBookingMaster in storage.
     *
     * @param CreateIOUBookingMasterRequest $request
     *
     * @return Response
     */
    public function store(CreateIOUBookingMasterRequest $request)
    {
        $input = $request->all();

        $iOUBookingMaster = $this->iOUBookingMasterRepository->create($input);

        Flash::success('I O U Booking Master saved successfully.');

        return redirect(route('iOUBookingMasters.index'));
    }

    /**
     * Display the specified IOUBookingMaster.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $iOUBookingMaster = $this->iOUBookingMasterRepository->findWithoutFail($id);

        if (empty($iOUBookingMaster)) {
            Flash::error('I O U Booking Master not found');

            return redirect(route('iOUBookingMasters.index'));
        }

        return view('i_o_u_booking_masters.show')->with('iOUBookingMaster', $iOUBookingMaster);
    }

    /**
     * Show the form for editing the specified IOUBookingMaster.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $iOUBookingMaster = $this->iOUBookingMasterRepository->findWithoutFail($id);

        if (empty($iOUBookingMaster)) {
            Flash::error('I O U Booking Master not found');

            return redirect(route('iOUBookingMasters.index'));
        }

        return view('i_o_u_booking_masters.edit')->with('iOUBookingMaster', $iOUBookingMaster);
    }

    /**
     * Update the specified IOUBookingMaster in storage.
     *
     * @param  int              $id
     * @param UpdateIOUBookingMasterRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateIOUBookingMasterRequest $request)
    {
        $iOUBookingMaster = $this->iOUBookingMasterRepository->findWithoutFail($id);

        if (empty($iOUBookingMaster)) {
            Flash::error('I O U Booking Master not found');

            return redirect(route('iOUBookingMasters.index'));
        }

        $iOUBookingMaster = $this->iOUBookingMasterRepository->update($request->all(), $id);

        Flash::success('I O U Booking Master updated successfully.');

        return redirect(route('iOUBookingMasters.index'));
    }

    /**
     * Remove the specified IOUBookingMaster from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $iOUBookingMaster = $this->iOUBookingMasterRepository->findWithoutFail($id);

        if (empty($iOUBookingMaster)) {
            Flash::error('I O U Booking Master not found');

            return redirect(route('iOUBookingMasters.index'));
        }

        $this->iOUBookingMasterRepository->delete($id);

        Flash::success('I O U Booking Master deleted successfully.');

        return redirect(route('iOUBookingMasters.index'));
    }
}
