<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateDebitNoteMasterRefferedbackRequest;
use App\Http\Requests\UpdateDebitNoteMasterRefferedbackRequest;
use App\Repositories\DebitNoteMasterRefferedbackRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class DebitNoteMasterRefferedbackController extends AppBaseController
{
    /** @var  DebitNoteMasterRefferedbackRepository */
    private $debitNoteMasterRefferedbackRepository;

    public function __construct(DebitNoteMasterRefferedbackRepository $debitNoteMasterRefferedbackRepo)
    {
        $this->debitNoteMasterRefferedbackRepository = $debitNoteMasterRefferedbackRepo;
    }

    /**
     * Display a listing of the DebitNoteMasterRefferedback.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->debitNoteMasterRefferedbackRepository->pushCriteria(new RequestCriteria($request));
        $debitNoteMasterRefferedbacks = $this->debitNoteMasterRefferedbackRepository->all();

        return view('debit_note_master_refferedbacks.index')
            ->with('debitNoteMasterRefferedbacks', $debitNoteMasterRefferedbacks);
    }

    /**
     * Show the form for creating a new DebitNoteMasterRefferedback.
     *
     * @return Response
     */
    public function create()
    {
        return view('debit_note_master_refferedbacks.create');
    }

    /**
     * Store a newly created DebitNoteMasterRefferedback in storage.
     *
     * @param CreateDebitNoteMasterRefferedbackRequest $request
     *
     * @return Response
     */
    public function store(CreateDebitNoteMasterRefferedbackRequest $request)
    {
        $input = $request->all();

        $debitNoteMasterRefferedback = $this->debitNoteMasterRefferedbackRepository->create($input);

        Flash::success('Debit Note Master Refferedback saved successfully.');

        return redirect(route('debitNoteMasterRefferedbacks.index'));
    }

    /**
     * Display the specified DebitNoteMasterRefferedback.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $debitNoteMasterRefferedback = $this->debitNoteMasterRefferedbackRepository->findWithoutFail($id);

        if (empty($debitNoteMasterRefferedback)) {
            Flash::error('Debit Note Master Refferedback not found');

            return redirect(route('debitNoteMasterRefferedbacks.index'));
        }

        return view('debit_note_master_refferedbacks.show')->with('debitNoteMasterRefferedback', $debitNoteMasterRefferedback);
    }

    /**
     * Show the form for editing the specified DebitNoteMasterRefferedback.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $debitNoteMasterRefferedback = $this->debitNoteMasterRefferedbackRepository->findWithoutFail($id);

        if (empty($debitNoteMasterRefferedback)) {
            Flash::error('Debit Note Master Refferedback not found');

            return redirect(route('debitNoteMasterRefferedbacks.index'));
        }

        return view('debit_note_master_refferedbacks.edit')->with('debitNoteMasterRefferedback', $debitNoteMasterRefferedback);
    }

    /**
     * Update the specified DebitNoteMasterRefferedback in storage.
     *
     * @param  int              $id
     * @param UpdateDebitNoteMasterRefferedbackRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateDebitNoteMasterRefferedbackRequest $request)
    {
        $debitNoteMasterRefferedback = $this->debitNoteMasterRefferedbackRepository->findWithoutFail($id);

        if (empty($debitNoteMasterRefferedback)) {
            Flash::error('Debit Note Master Refferedback not found');

            return redirect(route('debitNoteMasterRefferedbacks.index'));
        }

        $debitNoteMasterRefferedback = $this->debitNoteMasterRefferedbackRepository->update($request->all(), $id);

        Flash::success('Debit Note Master Refferedback updated successfully.');

        return redirect(route('debitNoteMasterRefferedbacks.index'));
    }

    /**
     * Remove the specified DebitNoteMasterRefferedback from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $debitNoteMasterRefferedback = $this->debitNoteMasterRefferedbackRepository->findWithoutFail($id);

        if (empty($debitNoteMasterRefferedback)) {
            Flash::error('Debit Note Master Refferedback not found');

            return redirect(route('debitNoteMasterRefferedbacks.index'));
        }

        $this->debitNoteMasterRefferedbackRepository->delete($id);

        Flash::success('Debit Note Master Refferedback deleted successfully.');

        return redirect(route('debitNoteMasterRefferedbacks.index'));
    }
}
