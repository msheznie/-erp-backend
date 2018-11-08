<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateSalaryProcessMasterRequest;
use App\Http\Requests\UpdateSalaryProcessMasterRequest;
use App\Repositories\SalaryProcessMasterRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class SalaryProcessMasterController extends AppBaseController
{
    /** @var  SalaryProcessMasterRepository */
    private $salaryProcessMasterRepository;

    public function __construct(SalaryProcessMasterRepository $salaryProcessMasterRepo)
    {
        $this->salaryProcessMasterRepository = $salaryProcessMasterRepo;
    }

    /**
     * Display a listing of the SalaryProcessMaster.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->salaryProcessMasterRepository->pushCriteria(new RequestCriteria($request));
        $salaryProcessMasters = $this->salaryProcessMasterRepository->all();

        return view('salary_process_masters.index')
            ->with('salaryProcessMasters', $salaryProcessMasters);
    }

    /**
     * Show the form for creating a new SalaryProcessMaster.
     *
     * @return Response
     */
    public function create()
    {
        return view('salary_process_masters.create');
    }

    /**
     * Store a newly created SalaryProcessMaster in storage.
     *
     * @param CreateSalaryProcessMasterRequest $request
     *
     * @return Response
     */
    public function store(CreateSalaryProcessMasterRequest $request)
    {
        $input = $request->all();

        $salaryProcessMaster = $this->salaryProcessMasterRepository->create($input);

        Flash::success('Salary Process Master saved successfully.');

        return redirect(route('salaryProcessMasters.index'));
    }

    /**
     * Display the specified SalaryProcessMaster.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $salaryProcessMaster = $this->salaryProcessMasterRepository->findWithoutFail($id);

        if (empty($salaryProcessMaster)) {
            Flash::error('Salary Process Master not found');

            return redirect(route('salaryProcessMasters.index'));
        }

        return view('salary_process_masters.show')->with('salaryProcessMaster', $salaryProcessMaster);
    }

    /**
     * Show the form for editing the specified SalaryProcessMaster.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $salaryProcessMaster = $this->salaryProcessMasterRepository->findWithoutFail($id);

        if (empty($salaryProcessMaster)) {
            Flash::error('Salary Process Master not found');

            return redirect(route('salaryProcessMasters.index'));
        }

        return view('salary_process_masters.edit')->with('salaryProcessMaster', $salaryProcessMaster);
    }

    /**
     * Update the specified SalaryProcessMaster in storage.
     *
     * @param  int              $id
     * @param UpdateSalaryProcessMasterRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateSalaryProcessMasterRequest $request)
    {
        $salaryProcessMaster = $this->salaryProcessMasterRepository->findWithoutFail($id);

        if (empty($salaryProcessMaster)) {
            Flash::error('Salary Process Master not found');

            return redirect(route('salaryProcessMasters.index'));
        }

        $salaryProcessMaster = $this->salaryProcessMasterRepository->update($request->all(), $id);

        Flash::success('Salary Process Master updated successfully.');

        return redirect(route('salaryProcessMasters.index'));
    }

    /**
     * Remove the specified SalaryProcessMaster from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $salaryProcessMaster = $this->salaryProcessMasterRepository->findWithoutFail($id);

        if (empty($salaryProcessMaster)) {
            Flash::error('Salary Process Master not found');

            return redirect(route('salaryProcessMasters.index'));
        }

        $this->salaryProcessMasterRepository->delete($id);

        Flash::success('Salary Process Master deleted successfully.');

        return redirect(route('salaryProcessMasters.index'));
    }
}
