<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateTicketMasterRequest;
use App\Http\Requests\UpdateTicketMasterRequest;
use App\Repositories\TicketMasterRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class TicketMasterController extends AppBaseController
{
    /** @var  TicketMasterRepository */
    private $ticketMasterRepository;

    public function __construct(TicketMasterRepository $ticketMasterRepo)
    {
        $this->ticketMasterRepository = $ticketMasterRepo;
    }

    /**
     * Display a listing of the TicketMaster.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->ticketMasterRepository->pushCriteria(new RequestCriteria($request));
        $ticketMasters = $this->ticketMasterRepository->all();

        return view('ticket_masters.index')
            ->with('ticketMasters', $ticketMasters);
    }

    /**
     * Show the form for creating a new TicketMaster.
     *
     * @return Response
     */
    public function create()
    {
        return view('ticket_masters.create');
    }

    /**
     * Store a newly created TicketMaster in storage.
     *
     * @param CreateTicketMasterRequest $request
     *
     * @return Response
     */
    public function store(CreateTicketMasterRequest $request)
    {
        $input = $request->all();

        $ticketMaster = $this->ticketMasterRepository->create($input);

        Flash::success('Ticket Master saved successfully.');

        return redirect(route('ticketMasters.index'));
    }

    /**
     * Display the specified TicketMaster.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $ticketMaster = $this->ticketMasterRepository->findWithoutFail($id);

        if (empty($ticketMaster)) {
            Flash::error('Ticket Master not found');

            return redirect(route('ticketMasters.index'));
        }

        return view('ticket_masters.show')->with('ticketMaster', $ticketMaster);
    }

    /**
     * Show the form for editing the specified TicketMaster.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $ticketMaster = $this->ticketMasterRepository->findWithoutFail($id);

        if (empty($ticketMaster)) {
            Flash::error('Ticket Master not found');

            return redirect(route('ticketMasters.index'));
        }

        return view('ticket_masters.edit')->with('ticketMaster', $ticketMaster);
    }

    /**
     * Update the specified TicketMaster in storage.
     *
     * @param  int              $id
     * @param UpdateTicketMasterRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateTicketMasterRequest $request)
    {
        $ticketMaster = $this->ticketMasterRepository->findWithoutFail($id);

        if (empty($ticketMaster)) {
            Flash::error('Ticket Master not found');

            return redirect(route('ticketMasters.index'));
        }

        $ticketMaster = $this->ticketMasterRepository->update($request->all(), $id);

        Flash::success('Ticket Master updated successfully.');

        return redirect(route('ticketMasters.index'));
    }

    /**
     * Remove the specified TicketMaster from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $ticketMaster = $this->ticketMasterRepository->findWithoutFail($id);

        if (empty($ticketMaster)) {
            Flash::error('Ticket Master not found');

            return redirect(route('ticketMasters.index'));
        }

        $this->ticketMasterRepository->delete($id);

        Flash::success('Ticket Master deleted successfully.');

        return redirect(route('ticketMasters.index'));
    }
}
