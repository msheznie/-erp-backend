<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateDesignationRequest;
use App\Http\Requests\UpdateDesignationRequest;
use App\Repositories\DesignationRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class DesignationController extends AppBaseController
{
    /** @var  DesignationRepository */
    private $designationRepository;

    public function __construct(DesignationRepository $designationRepo)
    {
        $this->designationRepository = $designationRepo;
    }

    /**
     * Display a listing of the Designation.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->designationRepository->pushCriteria(new RequestCriteria($request));
        $designations = $this->designationRepository->all();

        return view('designations.index')
            ->with('designations', $designations);
    }

    /**
     * Show the form for creating a new Designation.
     *
     * @return Response
     */
    public function create()
    {
        return view('designations.create');
    }

    /**
     * Store a newly created Designation in storage.
     *
     * @param CreateDesignationRequest $request
     *
     * @return Response
     */
    public function store(CreateDesignationRequest $request)
    {
        $input = $request->all();

        $designation = $this->designationRepository->create($input);

        Flash::success('Designation saved successfully.');

        return redirect(route('designations.index'));
    }

    /**
     * Display the specified Designation.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $designation = $this->designationRepository->findWithoutFail($id);

        if (empty($designation)) {
            Flash::error('Designation not found');

            return redirect(route('designations.index'));
        }

        return view('designations.show')->with('designation', $designation);
    }

    /**
     * Show the form for editing the specified Designation.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $designation = $this->designationRepository->findWithoutFail($id);

        if (empty($designation)) {
            Flash::error('Designation not found');

            return redirect(route('designations.index'));
        }

        return view('designations.edit')->with('designation', $designation);
    }

    /**
     * Update the specified Designation in storage.
     *
     * @param  int              $id
     * @param UpdateDesignationRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateDesignationRequest $request)
    {
        $designation = $this->designationRepository->findWithoutFail($id);

        if (empty($designation)) {
            Flash::error('Designation not found');

            return redirect(route('designations.index'));
        }

        $designation = $this->designationRepository->update($request->all(), $id);

        Flash::success('Designation updated successfully.');

        return redirect(route('designations.index'));
    }

    /**
     * Remove the specified Designation from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $designation = $this->designationRepository->findWithoutFail($id);

        if (empty($designation)) {
            Flash::error('Designation not found');

            return redirect(route('designations.index'));
        }

        $this->designationRepository->delete($id);

        Flash::success('Designation deleted successfully.');

        return redirect(route('designations.index'));
    }
}
