<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateInsurancePolicyTypeRequest;
use App\Http\Requests\UpdateInsurancePolicyTypeRequest;
use App\Repositories\InsurancePolicyTypeRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class InsurancePolicyTypeController extends AppBaseController
{
    /** @var  InsurancePolicyTypeRepository */
    private $insurancePolicyTypeRepository;

    public function __construct(InsurancePolicyTypeRepository $insurancePolicyTypeRepo)
    {
        $this->insurancePolicyTypeRepository = $insurancePolicyTypeRepo;
    }

    /**
     * Display a listing of the InsurancePolicyType.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->insurancePolicyTypeRepository->pushCriteria(new RequestCriteria($request));
        $insurancePolicyTypes = $this->insurancePolicyTypeRepository->all();

        return view('insurance_policy_types.index')
            ->with('insurancePolicyTypes', $insurancePolicyTypes);
    }

    /**
     * Show the form for creating a new InsurancePolicyType.
     *
     * @return Response
     */
    public function create()
    {
        return view('insurance_policy_types.create');
    }

    /**
     * Store a newly created InsurancePolicyType in storage.
     *
     * @param CreateInsurancePolicyTypeRequest $request
     *
     * @return Response
     */
    public function store(CreateInsurancePolicyTypeRequest $request)
    {
        $input = $request->all();

        $insurancePolicyType = $this->insurancePolicyTypeRepository->create($input);

        Flash::success('Insurance Policy Type saved successfully.');

        return redirect(route('insurancePolicyTypes.index'));
    }

    /**
     * Display the specified InsurancePolicyType.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $insurancePolicyType = $this->insurancePolicyTypeRepository->findWithoutFail($id);

        if (empty($insurancePolicyType)) {
            Flash::error('Insurance Policy Type not found');

            return redirect(route('insurancePolicyTypes.index'));
        }

        return view('insurance_policy_types.show')->with('insurancePolicyType', $insurancePolicyType);
    }

    /**
     * Show the form for editing the specified InsurancePolicyType.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $insurancePolicyType = $this->insurancePolicyTypeRepository->findWithoutFail($id);

        if (empty($insurancePolicyType)) {
            Flash::error('Insurance Policy Type not found');

            return redirect(route('insurancePolicyTypes.index'));
        }

        return view('insurance_policy_types.edit')->with('insurancePolicyType', $insurancePolicyType);
    }

    /**
     * Update the specified InsurancePolicyType in storage.
     *
     * @param  int              $id
     * @param UpdateInsurancePolicyTypeRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateInsurancePolicyTypeRequest $request)
    {
        $insurancePolicyType = $this->insurancePolicyTypeRepository->findWithoutFail($id);

        if (empty($insurancePolicyType)) {
            Flash::error('Insurance Policy Type not found');

            return redirect(route('insurancePolicyTypes.index'));
        }

        $insurancePolicyType = $this->insurancePolicyTypeRepository->update($request->all(), $id);

        Flash::success('Insurance Policy Type updated successfully.');

        return redirect(route('insurancePolicyTypes.index'));
    }

    /**
     * Remove the specified InsurancePolicyType from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $insurancePolicyType = $this->insurancePolicyTypeRepository->findWithoutFail($id);

        if (empty($insurancePolicyType)) {
            Flash::error('Insurance Policy Type not found');

            return redirect(route('insurancePolicyTypes.index'));
        }

        $this->insurancePolicyTypeRepository->delete($id);

        Flash::success('Insurance Policy Type deleted successfully.');

        return redirect(route('insurancePolicyTypes.index'));
    }
}
