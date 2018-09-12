<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateLogisticStatusRequest;
use App\Http\Requests\UpdateLogisticStatusRequest;
use App\Repositories\LogisticStatusRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class LogisticStatusController extends AppBaseController
{
    /** @var  LogisticStatusRepository */
    private $logisticStatusRepository;

    public function __construct(LogisticStatusRepository $logisticStatusRepo)
    {
        $this->logisticStatusRepository = $logisticStatusRepo;
    }

    /**
     * Display a listing of the LogisticStatus.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->logisticStatusRepository->pushCriteria(new RequestCriteria($request));
        $logisticStatuses = $this->logisticStatusRepository->all();

        return view('logistic_statuses.index')
            ->with('logisticStatuses', $logisticStatuses);
    }

    /**
     * Show the form for creating a new LogisticStatus.
     *
     * @return Response
     */
    public function create()
    {
        return view('logistic_statuses.create');
    }

    /**
     * Store a newly created LogisticStatus in storage.
     *
     * @param CreateLogisticStatusRequest $request
     *
     * @return Response
     */
    public function store(CreateLogisticStatusRequest $request)
    {
        $input = $request->all();

        $logisticStatus = $this->logisticStatusRepository->create($input);

        Flash::success('Logistic Status saved successfully.');

        return redirect(route('logisticStatuses.index'));
    }

    /**
     * Display the specified LogisticStatus.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $logisticStatus = $this->logisticStatusRepository->findWithoutFail($id);

        if (empty($logisticStatus)) {
            Flash::error('Logistic Status not found');

            return redirect(route('logisticStatuses.index'));
        }

        return view('logistic_statuses.show')->with('logisticStatus', $logisticStatus);
    }

    /**
     * Show the form for editing the specified LogisticStatus.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $logisticStatus = $this->logisticStatusRepository->findWithoutFail($id);

        if (empty($logisticStatus)) {
            Flash::error('Logistic Status not found');

            return redirect(route('logisticStatuses.index'));
        }

        return view('logistic_statuses.edit')->with('logisticStatus', $logisticStatus);
    }

    /**
     * Update the specified LogisticStatus in storage.
     *
     * @param  int              $id
     * @param UpdateLogisticStatusRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateLogisticStatusRequest $request)
    {
        $logisticStatus = $this->logisticStatusRepository->findWithoutFail($id);

        if (empty($logisticStatus)) {
            Flash::error('Logistic Status not found');

            return redirect(route('logisticStatuses.index'));
        }

        $logisticStatus = $this->logisticStatusRepository->update($request->all(), $id);

        Flash::success('Logistic Status updated successfully.');

        return redirect(route('logisticStatuses.index'));
    }

    /**
     * Remove the specified LogisticStatus from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $logisticStatus = $this->logisticStatusRepository->findWithoutFail($id);

        if (empty($logisticStatus)) {
            Flash::error('Logistic Status not found');

            return redirect(route('logisticStatuses.index'));
        }

        $this->logisticStatusRepository->delete($id);

        Flash::success('Logistic Status deleted successfully.');

        return redirect(route('logisticStatuses.index'));
    }
}
