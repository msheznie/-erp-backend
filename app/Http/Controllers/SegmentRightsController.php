<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateSegmentRightsRequest;
use App\Http\Requests\UpdateSegmentRightsRequest;
use App\Repositories\SegmentRightsRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class SegmentRightsController extends AppBaseController
{
    /** @var  SegmentRightsRepository */
    private $segmentRightsRepository;

    public function __construct(SegmentRightsRepository $segmentRightsRepo)
    {
        $this->segmentRightsRepository = $segmentRightsRepo;
    }

    /**
     * Display a listing of the SegmentRights.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->segmentRightsRepository->pushCriteria(new RequestCriteria($request));
        $segmentRights = $this->segmentRightsRepository->all();

        return view('segment_rights.index')
            ->with('segmentRights', $segmentRights);
    }

    /**
     * Show the form for creating a new SegmentRights.
     *
     * @return Response
     */
    public function create()
    {
        return view('segment_rights.create');
    }

    /**
     * Store a newly created SegmentRights in storage.
     *
     * @param CreateSegmentRightsRequest $request
     *
     * @return Response
     */
    public function store(CreateSegmentRightsRequest $request)
    {
        $input = $request->all();

        $segmentRights = $this->segmentRightsRepository->create($input);

        Flash::success('Segment Rights saved successfully.');

        return redirect(route('segmentRights.index'));
    }

    /**
     * Display the specified SegmentRights.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $segmentRights = $this->segmentRightsRepository->findWithoutFail($id);

        if (empty($segmentRights)) {
            Flash::error('Segment Rights not found');

            return redirect(route('segmentRights.index'));
        }

        return view('segment_rights.show')->with('segmentRights', $segmentRights);
    }

    /**
     * Show the form for editing the specified SegmentRights.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $segmentRights = $this->segmentRightsRepository->findWithoutFail($id);

        if (empty($segmentRights)) {
            Flash::error('Segment Rights not found');

            return redirect(route('segmentRights.index'));
        }

        return view('segment_rights.edit')->with('segmentRights', $segmentRights);
    }

    /**
     * Update the specified SegmentRights in storage.
     *
     * @param  int              $id
     * @param UpdateSegmentRightsRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateSegmentRightsRequest $request)
    {
        $segmentRights = $this->segmentRightsRepository->findWithoutFail($id);

        if (empty($segmentRights)) {
            Flash::error('Segment Rights not found');

            return redirect(route('segmentRights.index'));
        }

        $segmentRights = $this->segmentRightsRepository->update($request->all(), $id);

        Flash::success('Segment Rights updated successfully.');

        return redirect(route('segmentRights.index'));
    }

    /**
     * Remove the specified SegmentRights from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $segmentRights = $this->segmentRightsRepository->findWithoutFail($id);

        if (empty($segmentRights)) {
            Flash::error('Segment Rights not found');

            return redirect(route('segmentRights.index'));
        }

        $this->segmentRightsRepository->delete($id);

        Flash::success('Segment Rights deleted successfully.');

        return redirect(route('segmentRights.index'));
    }
}
