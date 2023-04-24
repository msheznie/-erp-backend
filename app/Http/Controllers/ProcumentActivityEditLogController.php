<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateProcumentActivityEditLogRequest;
use App\Http\Requests\UpdateProcumentActivityEditLogRequest;
use App\Repositories\ProcumentActivityEditLogRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class ProcumentActivityEditLogController extends AppBaseController
{
    /** @var  ProcumentActivityEditLogRepository */
    private $procumentActivityEditLogRepository;

    public function __construct(ProcumentActivityEditLogRepository $procumentActivityEditLogRepo)
    {
        $this->procumentActivityEditLogRepository = $procumentActivityEditLogRepo;
    }

    /**
     * Display a listing of the ProcumentActivityEditLog.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->procumentActivityEditLogRepository->pushCriteria(new RequestCriteria($request));
        $procumentActivityEditLogs = $this->procumentActivityEditLogRepository->all();

        return view('procument_activity_edit_logs.index')
            ->with('procumentActivityEditLogs', $procumentActivityEditLogs);
    }

    /**
     * Show the form for creating a new ProcumentActivityEditLog.
     *
     * @return Response
     */
    public function create()
    {
        return view('procument_activity_edit_logs.create');
    }

    /**
     * Store a newly created ProcumentActivityEditLog in storage.
     *
     * @param CreateProcumentActivityEditLogRequest $request
     *
     * @return Response
     */
    public function store(CreateProcumentActivityEditLogRequest $request)
    {
        $input = $request->all();

        $procumentActivityEditLog = $this->procumentActivityEditLogRepository->create($input);

        Flash::success('Procument Activity Edit Log saved successfully.');

        return redirect(route('procumentActivityEditLogs.index'));
    }

    /**
     * Display the specified ProcumentActivityEditLog.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $procumentActivityEditLog = $this->procumentActivityEditLogRepository->findWithoutFail($id);

        if (empty($procumentActivityEditLog)) {
            Flash::error('Procument Activity Edit Log not found');

            return redirect(route('procumentActivityEditLogs.index'));
        }

        return view('procument_activity_edit_logs.show')->with('procumentActivityEditLog', $procumentActivityEditLog);
    }

    /**
     * Show the form for editing the specified ProcumentActivityEditLog.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $procumentActivityEditLog = $this->procumentActivityEditLogRepository->findWithoutFail($id);

        if (empty($procumentActivityEditLog)) {
            Flash::error('Procument Activity Edit Log not found');

            return redirect(route('procumentActivityEditLogs.index'));
        }

        return view('procument_activity_edit_logs.edit')->with('procumentActivityEditLog', $procumentActivityEditLog);
    }

    /**
     * Update the specified ProcumentActivityEditLog in storage.
     *
     * @param  int              $id
     * @param UpdateProcumentActivityEditLogRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateProcumentActivityEditLogRequest $request)
    {
        $procumentActivityEditLog = $this->procumentActivityEditLogRepository->findWithoutFail($id);

        if (empty($procumentActivityEditLog)) {
            Flash::error('Procument Activity Edit Log not found');

            return redirect(route('procumentActivityEditLogs.index'));
        }

        $procumentActivityEditLog = $this->procumentActivityEditLogRepository->update($request->all(), $id);

        Flash::success('Procument Activity Edit Log updated successfully.');

        return redirect(route('procumentActivityEditLogs.index'));
    }

    /**
     * Remove the specified ProcumentActivityEditLog from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $procumentActivityEditLog = $this->procumentActivityEditLogRepository->findWithoutFail($id);

        if (empty($procumentActivityEditLog)) {
            Flash::error('Procument Activity Edit Log not found');

            return redirect(route('procumentActivityEditLogs.index'));
        }

        $this->procumentActivityEditLogRepository->delete($id);

        Flash::success('Procument Activity Edit Log deleted successfully.');

        return redirect(route('procumentActivityEditLogs.index'));
    }
}
