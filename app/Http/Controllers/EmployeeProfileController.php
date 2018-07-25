<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateEmployeeProfileRequest;
use App\Http\Requests\UpdateEmployeeProfileRequest;
use App\Repositories\EmployeeProfileRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class EmployeeProfileController extends AppBaseController
{
    /** @var  EmployeeProfileRepository */
    private $employeeProfileRepository;

    public function __construct(EmployeeProfileRepository $employeeProfileRepo)
    {
        $this->employeeProfileRepository = $employeeProfileRepo;
    }

    /**
     * Display a listing of the EmployeeProfile.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->employeeProfileRepository->pushCriteria(new RequestCriteria($request));
        $employeeProfiles = $this->employeeProfileRepository->all();

        return view('employee_profiles.index')
            ->with('employeeProfiles', $employeeProfiles);
    }

    /**
     * Show the form for creating a new EmployeeProfile.
     *
     * @return Response
     */
    public function create()
    {
        return view('employee_profiles.create');
    }

    /**
     * Store a newly created EmployeeProfile in storage.
     *
     * @param CreateEmployeeProfileRequest $request
     *
     * @return Response
     */
    public function store(CreateEmployeeProfileRequest $request)
    {
        $input = $request->all();

        $employeeProfile = $this->employeeProfileRepository->create($input);

        Flash::success('Employee Profile saved successfully.');

        return redirect(route('employeeProfiles.index'));
    }

    /**
     * Display the specified EmployeeProfile.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $employeeProfile = $this->employeeProfileRepository->findWithoutFail($id);

        if (empty($employeeProfile)) {
            Flash::error('Employee Profile not found');

            return redirect(route('employeeProfiles.index'));
        }

        return view('employee_profiles.show')->with('employeeProfile', $employeeProfile);
    }

    /**
     * Show the form for editing the specified EmployeeProfile.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $employeeProfile = $this->employeeProfileRepository->findWithoutFail($id);

        if (empty($employeeProfile)) {
            Flash::error('Employee Profile not found');

            return redirect(route('employeeProfiles.index'));
        }

        return view('employee_profiles.edit')->with('employeeProfile', $employeeProfile);
    }

    /**
     * Update the specified EmployeeProfile in storage.
     *
     * @param  int              $id
     * @param UpdateEmployeeProfileRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateEmployeeProfileRequest $request)
    {
        $employeeProfile = $this->employeeProfileRepository->findWithoutFail($id);

        if (empty($employeeProfile)) {
            Flash::error('Employee Profile not found');

            return redirect(route('employeeProfiles.index'));
        }

        $employeeProfile = $this->employeeProfileRepository->update($request->all(), $id);

        Flash::success('Employee Profile updated successfully.');

        return redirect(route('employeeProfiles.index'));
    }

    /**
     * Remove the specified EmployeeProfile from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $employeeProfile = $this->employeeProfileRepository->findWithoutFail($id);

        if (empty($employeeProfile)) {
            Flash::error('Employee Profile not found');

            return redirect(route('employeeProfiles.index'));
        }

        $this->employeeProfileRepository->delete($id);

        Flash::success('Employee Profile deleted successfully.');

        return redirect(route('employeeProfiles.index'));
    }
}
