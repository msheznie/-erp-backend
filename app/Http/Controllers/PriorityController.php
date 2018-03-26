<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatePriorityRequest;
use App\Http\Requests\UpdatePriorityRequest;
use App\Repositories\PriorityRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class PriorityController extends AppBaseController
{
    /** @var  PriorityRepository */
    private $priorityRepository;

    public function __construct(PriorityRepository $priorityRepo)
    {
        $this->priorityRepository = $priorityRepo;
    }

    /**
     * Display a listing of the Priority.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->priorityRepository->pushCriteria(new RequestCriteria($request));
        $priorities = $this->priorityRepository->all();

        return view('priorities.index')
            ->with('priorities', $priorities);
    }

    /**
     * Show the form for creating a new Priority.
     *
     * @return Response
     */
    public function create()
    {
        return view('priorities.create');
    }

    /**
     * Store a newly created Priority in storage.
     *
     * @param CreatePriorityRequest $request
     *
     * @return Response
     */
    public function store(CreatePriorityRequest $request)
    {
        $input = $request->all();

        $priority = $this->priorityRepository->create($input);

        Flash::success('Priority saved successfully.');

        return redirect(route('priorities.index'));
    }

    /**
     * Display the specified Priority.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $priority = $this->priorityRepository->findWithoutFail($id);

        if (empty($priority)) {
            Flash::error('Priority not found');

            return redirect(route('priorities.index'));
        }

        return view('priorities.show')->with('priority', $priority);
    }

    /**
     * Show the form for editing the specified Priority.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $priority = $this->priorityRepository->findWithoutFail($id);

        if (empty($priority)) {
            Flash::error('Priority not found');

            return redirect(route('priorities.index'));
        }

        return view('priorities.edit')->with('priority', $priority);
    }

    /**
     * Update the specified Priority in storage.
     *
     * @param  int              $id
     * @param UpdatePriorityRequest $request
     *
     * @return Response
     */
    public function update($id, UpdatePriorityRequest $request)
    {
        $priority = $this->priorityRepository->findWithoutFail($id);

        if (empty($priority)) {
            Flash::error('Priority not found');

            return redirect(route('priorities.index'));
        }

        $priority = $this->priorityRepository->update($request->all(), $id);

        Flash::success('Priority updated successfully.');

        return redirect(route('priorities.index'));
    }

    /**
     * Remove the specified Priority from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $priority = $this->priorityRepository->findWithoutFail($id);

        if (empty($priority)) {
            Flash::error('Priority not found');

            return redirect(route('priorities.index'));
        }

        $this->priorityRepository->delete($id);

        Flash::success('Priority deleted successfully.');

        return redirect(route('priorities.index'));
    }
}
