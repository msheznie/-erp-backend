<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateSalaryProcessEmploymentTypesRequest;
use App\Http\Requests\UpdateSalaryProcessEmploymentTypesRequest;
use App\Repositories\SalaryProcessEmploymentTypesRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class SalaryProcessEmploymentTypesController extends AppBaseController
{
    /** @var  SalaryProcessEmploymentTypesRepository */
    private $salaryProcessEmploymentTypesRepository;

    public function __construct(SalaryProcessEmploymentTypesRepository $salaryProcessEmploymentTypesRepo)
    {
        $this->salaryProcessEmploymentTypesRepository = $salaryProcessEmploymentTypesRepo;
    }

    /**
     * Display a listing of the SalaryProcessEmploymentTypes.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->salaryProcessEmploymentTypesRepository->pushCriteria(new RequestCriteria($request));
        $salaryProcessEmploymentTypes = $this->salaryProcessEmploymentTypesRepository->all();

        return view('salary_process_employment_types.index')
            ->with('salaryProcessEmploymentTypes', $salaryProcessEmploymentTypes);
    }

    /**
     * Show the form for creating a new SalaryProcessEmploymentTypes.
     *
     * @return Response
     */
    public function create()
    {
        return view('salary_process_employment_types.create');
    }

    /**
     * Store a newly created SalaryProcessEmploymentTypes in storage.
     *
     * @param CreateSalaryProcessEmploymentTypesRequest $request
     *
     * @return Response
     */
    public function store(CreateSalaryProcessEmploymentTypesRequest $request)
    {
        $input = $request->all();

        $salaryProcessEmploymentTypes = $this->salaryProcessEmploymentTypesRepository->create($input);

        Flash::success('Salary Process Employment Types saved successfully.');

        return redirect(route('salaryProcessEmploymentTypes.index'));
    }

    /**
     * Display the specified SalaryProcessEmploymentTypes.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $salaryProcessEmploymentTypes = $this->salaryProcessEmploymentTypesRepository->findWithoutFail($id);

        if (empty($salaryProcessEmploymentTypes)) {
            Flash::error('Salary Process Employment Types not found');

            return redirect(route('salaryProcessEmploymentTypes.index'));
        }

        return view('salary_process_employment_types.show')->with('salaryProcessEmploymentTypes', $salaryProcessEmploymentTypes);
    }

    /**
     * Show the form for editing the specified SalaryProcessEmploymentTypes.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $salaryProcessEmploymentTypes = $this->salaryProcessEmploymentTypesRepository->findWithoutFail($id);

        if (empty($salaryProcessEmploymentTypes)) {
            Flash::error('Salary Process Employment Types not found');

            return redirect(route('salaryProcessEmploymentTypes.index'));
        }

        return view('salary_process_employment_types.edit')->with('salaryProcessEmploymentTypes', $salaryProcessEmploymentTypes);
    }

    /**
     * Update the specified SalaryProcessEmploymentTypes in storage.
     *
     * @param  int              $id
     * @param UpdateSalaryProcessEmploymentTypesRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateSalaryProcessEmploymentTypesRequest $request)
    {
        $salaryProcessEmploymentTypes = $this->salaryProcessEmploymentTypesRepository->findWithoutFail($id);

        if (empty($salaryProcessEmploymentTypes)) {
            Flash::error('Salary Process Employment Types not found');

            return redirect(route('salaryProcessEmploymentTypes.index'));
        }

        $salaryProcessEmploymentTypes = $this->salaryProcessEmploymentTypesRepository->update($request->all(), $id);

        Flash::success('Salary Process Employment Types updated successfully.');

        return redirect(route('salaryProcessEmploymentTypes.index'));
    }

    /**
     * Remove the specified SalaryProcessEmploymentTypes from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $salaryProcessEmploymentTypes = $this->salaryProcessEmploymentTypesRepository->findWithoutFail($id);

        if (empty($salaryProcessEmploymentTypes)) {
            Flash::error('Salary Process Employment Types not found');

            return redirect(route('salaryProcessEmploymentTypes.index'));
        }

        $this->salaryProcessEmploymentTypesRepository->delete($id);

        Flash::success('Salary Process Employment Types deleted successfully.');

        return redirect(route('salaryProcessEmploymentTypes.index'));
    }
}
