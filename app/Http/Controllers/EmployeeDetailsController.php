<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateEmployeeDetailsRequest;
use App\Http\Requests\UpdateEmployeeDetailsRequest;
use App\Repositories\EmployeeDetailsRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class EmployeeDetailsController extends AppBaseController
{
    /** @var  EmployeeDetailsRepository */
    private $employeeDetailsRepository;

    public function __construct(EmployeeDetailsRepository $employeeDetailsRepo)
    {
        $this->employeeDetailsRepository = $employeeDetailsRepo;
    }

    /**
     * Display a listing of the EmployeeDetails.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->employeeDetailsRepository->pushCriteria(new RequestCriteria($request));
        $employeeDetails = $this->employeeDetailsRepository->all();

        return view('employee_details.index')
            ->with('employeeDetails', $employeeDetails);
    }

    /**
     * Show the form for creating a new EmployeeDetails.
     *
     * @return Response
     */
    public function create()
    {
        return view('employee_details.create');
    }

    /**
     * Store a newly created EmployeeDetails in storage.
     *
     * @param CreateEmployeeDetailsRequest $request
     *
     * @return Response
     */
    public function store(CreateEmployeeDetailsRequest $request)
    {
        $input = $request->all();

        $employeeDetails = $this->employeeDetailsRepository->create($input);

        Flash::success('Employee Details saved successfully.');

        return redirect(route('employeeDetails.index'));
    }

    /**
     * Display the specified EmployeeDetails.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $employeeDetails = $this->employeeDetailsRepository->findWithoutFail($id);

        if (empty($employeeDetails)) {
            Flash::error('Employee Details not found');

            return redirect(route('employeeDetails.index'));
        }

        return view('employee_details.show')->with('employeeDetails', $employeeDetails);
    }

    /**
     * Show the form for editing the specified EmployeeDetails.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $employeeDetails = $this->employeeDetailsRepository->findWithoutFail($id);

        if (empty($employeeDetails)) {
            Flash::error('Employee Details not found');

            return redirect(route('employeeDetails.index'));
        }

        return view('employee_details.edit')->with('employeeDetails', $employeeDetails);
    }

    /**
     * Update the specified EmployeeDetails in storage.
     *
     * @param  int              $id
     * @param UpdateEmployeeDetailsRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateEmployeeDetailsRequest $request)
    {
        $employeeDetails = $this->employeeDetailsRepository->findWithoutFail($id);

        if (empty($employeeDetails)) {
            Flash::error('Employee Details not found');

            return redirect(route('employeeDetails.index'));
        }

        $employeeDetails = $this->employeeDetailsRepository->update($request->all(), $id);

        Flash::success('Employee Details updated successfully.');

        return redirect(route('employeeDetails.index'));
    }

    /**
     * Remove the specified EmployeeDetails from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $employeeDetails = $this->employeeDetailsRepository->findWithoutFail($id);

        if (empty($employeeDetails)) {
            Flash::error('Employee Details not found');

            return redirect(route('employeeDetails.index'));
        }

        $this->employeeDetailsRepository->delete($id);

        Flash::success('Employee Details deleted successfully.');

        return redirect(route('employeeDetails.index'));
    }
}
