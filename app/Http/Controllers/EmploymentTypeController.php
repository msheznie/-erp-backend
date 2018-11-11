<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateEmploymentTypeRequest;
use App\Http\Requests\UpdateEmploymentTypeRequest;
use App\Repositories\EmploymentTypeRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class EmploymentTypeController extends AppBaseController
{
    /** @var  EmploymentTypeRepository */
    private $employmentTypeRepository;

    public function __construct(EmploymentTypeRepository $employmentTypeRepo)
    {
        $this->employmentTypeRepository = $employmentTypeRepo;
    }

    /**
     * Display a listing of the EmploymentType.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->employmentTypeRepository->pushCriteria(new RequestCriteria($request));
        $employmentTypes = $this->employmentTypeRepository->all();

        return view('employment_types.index')
            ->with('employmentTypes', $employmentTypes);
    }

    /**
     * Show the form for creating a new EmploymentType.
     *
     * @return Response
     */
    public function create()
    {
        return view('employment_types.create');
    }

    /**
     * Store a newly created EmploymentType in storage.
     *
     * @param CreateEmploymentTypeRequest $request
     *
     * @return Response
     */
    public function store(CreateEmploymentTypeRequest $request)
    {
        $input = $request->all();

        $employmentType = $this->employmentTypeRepository->create($input);

        Flash::success('Employment Type saved successfully.');

        return redirect(route('employmentTypes.index'));
    }

    /**
     * Display the specified EmploymentType.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $employmentType = $this->employmentTypeRepository->findWithoutFail($id);

        if (empty($employmentType)) {
            Flash::error('Employment Type not found');

            return redirect(route('employmentTypes.index'));
        }

        return view('employment_types.show')->with('employmentType', $employmentType);
    }

    /**
     * Show the form for editing the specified EmploymentType.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $employmentType = $this->employmentTypeRepository->findWithoutFail($id);

        if (empty($employmentType)) {
            Flash::error('Employment Type not found');

            return redirect(route('employmentTypes.index'));
        }

        return view('employment_types.edit')->with('employmentType', $employmentType);
    }

    /**
     * Update the specified EmploymentType in storage.
     *
     * @param  int              $id
     * @param UpdateEmploymentTypeRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateEmploymentTypeRequest $request)
    {
        $employmentType = $this->employmentTypeRepository->findWithoutFail($id);

        if (empty($employmentType)) {
            Flash::error('Employment Type not found');

            return redirect(route('employmentTypes.index'));
        }

        $employmentType = $this->employmentTypeRepository->update($request->all(), $id);

        Flash::success('Employment Type updated successfully.');

        return redirect(route('employmentTypes.index'));
    }

    /**
     * Remove the specified EmploymentType from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $employmentType = $this->employmentTypeRepository->findWithoutFail($id);

        if (empty($employmentType)) {
            Flash::error('Employment Type not found');

            return redirect(route('employmentTypes.index'));
        }

        $this->employmentTypeRepository->delete($id);

        Flash::success('Employment Type deleted successfully.');

        return redirect(route('employmentTypes.index'));
    }
}
