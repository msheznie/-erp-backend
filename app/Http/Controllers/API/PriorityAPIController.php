<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreatePriorityAPIRequest;
use App\Http\Requests\API\UpdatePriorityAPIRequest;
use App\Models\Priority;
use App\Repositories\PriorityRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class PriorityController
 * @package App\Http\Controllers\API
 */

class PriorityAPIController extends AppBaseController
{
    /** @var  PriorityRepository */
    private $priorityRepository;

    public function __construct(PriorityRepository $priorityRepo)
    {
        $this->priorityRepository = $priorityRepo;
    }

    /**
     * Display a listing of the Priority.
     * GET|HEAD /priorities
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->priorityRepository->pushCriteria(new RequestCriteria($request));
        $this->priorityRepository->pushCriteria(new LimitOffsetCriteria($request));
        $priorities = $this->priorityRepository->all();

        return $this->sendResponse($priorities->toArray(), 'Priorities retrieved successfully');
    }

    /**
     * Store a newly created Priority in storage.
     * POST /priorities
     *
     * @param CreatePriorityAPIRequest $request
     *
     * @return Response
     */
    public function store(CreatePriorityAPIRequest $request)
    {
        $input = $request->all();

        $priorities = $this->priorityRepository->create($input);

        return $this->sendResponse($priorities->toArray(), 'Priority saved successfully');
    }

    /**
     * Display the specified Priority.
     * GET|HEAD /priorities/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var Priority $priority */
        $priority = $this->priorityRepository->findWithoutFail($id);

        if (empty($priority)) {
            return $this->sendError('Priority not found');
        }

        return $this->sendResponse($priority->toArray(), 'Priority retrieved successfully');
    }

    /**
     * Update the specified Priority in storage.
     * PUT/PATCH /priorities/{id}
     *
     * @param  int $id
     * @param UpdatePriorityAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdatePriorityAPIRequest $request)
    {
        $input = $request->all();

        /** @var Priority $priority */
        $priority = $this->priorityRepository->findWithoutFail($id);

        if (empty($priority)) {
            return $this->sendError('Priority not found');
        }

        $priority = $this->priorityRepository->update($input, $id);

        return $this->sendResponse($priority->toArray(), 'Priority updated successfully');
    }

    /**
     * Remove the specified Priority from storage.
     * DELETE /priorities/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var Priority $priority */
        $priority = $this->priorityRepository->findWithoutFail($id);

        if (empty($priority)) {
            return $this->sendError('Priority not found');
        }

        $priority->delete();

        return $this->sendResponse($id, 'Priority deleted successfully');
    }
}
