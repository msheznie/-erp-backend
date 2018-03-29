<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateCompanyPolicyMasterRequest;
use App\Http\Requests\UpdateCompanyPolicyMasterRequest;
use App\Repositories\CompanyPolicyMasterRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class CompanyPolicyMasterController extends AppBaseController
{
    /** @var  CompanyPolicyMasterRepository */
    private $companyPolicyMasterRepository;

    public function __construct(CompanyPolicyMasterRepository $companyPolicyMasterRepo)
    {
        $this->companyPolicyMasterRepository = $companyPolicyMasterRepo;
    }

    /**
     * Display a listing of the CompanyPolicyMaster.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->companyPolicyMasterRepository->pushCriteria(new RequestCriteria($request));
        $companyPolicyMasters = $this->companyPolicyMasterRepository->all();

        return view('company_policy_masters.index')
            ->with('companyPolicyMasters', $companyPolicyMasters);
    }

    /**
     * Show the form for creating a new CompanyPolicyMaster.
     *
     * @return Response
     */
    public function create()
    {
        return view('company_policy_masters.create');
    }

    /**
     * Store a newly created CompanyPolicyMaster in storage.
     *
     * @param CreateCompanyPolicyMasterRequest $request
     *
     * @return Response
     */
    public function store(CreateCompanyPolicyMasterRequest $request)
    {
        $input = $request->all();

        $companyPolicyMaster = $this->companyPolicyMasterRepository->create($input);

        Flash::success('Company Policy Master saved successfully.');

        return redirect(route('companyPolicyMasters.index'));
    }

    /**
     * Display the specified CompanyPolicyMaster.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $companyPolicyMaster = $this->companyPolicyMasterRepository->findWithoutFail($id);

        if (empty($companyPolicyMaster)) {
            Flash::error('Company Policy Master not found');

            return redirect(route('companyPolicyMasters.index'));
        }

        return view('company_policy_masters.show')->with('companyPolicyMaster', $companyPolicyMaster);
    }

    /**
     * Show the form for editing the specified CompanyPolicyMaster.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $companyPolicyMaster = $this->companyPolicyMasterRepository->findWithoutFail($id);

        if (empty($companyPolicyMaster)) {
            Flash::error('Company Policy Master not found');

            return redirect(route('companyPolicyMasters.index'));
        }

        return view('company_policy_masters.edit')->with('companyPolicyMaster', $companyPolicyMaster);
    }

    /**
     * Update the specified CompanyPolicyMaster in storage.
     *
     * @param  int              $id
     * @param UpdateCompanyPolicyMasterRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateCompanyPolicyMasterRequest $request)
    {
        $companyPolicyMaster = $this->companyPolicyMasterRepository->findWithoutFail($id);

        if (empty($companyPolicyMaster)) {
            Flash::error('Company Policy Master not found');

            return redirect(route('companyPolicyMasters.index'));
        }

        $companyPolicyMaster = $this->companyPolicyMasterRepository->update($request->all(), $id);

        Flash::success('Company Policy Master updated successfully.');

        return redirect(route('companyPolicyMasters.index'));
    }

    /**
     * Remove the specified CompanyPolicyMaster from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $companyPolicyMaster = $this->companyPolicyMasterRepository->findWithoutFail($id);

        if (empty($companyPolicyMaster)) {
            Flash::error('Company Policy Master not found');

            return redirect(route('companyPolicyMasters.index'));
        }

        $this->companyPolicyMasterRepository->delete($id);

        Flash::success('Company Policy Master deleted successfully.');

        return redirect(route('companyPolicyMasters.index'));
    }
}
