<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateTenantRequest;
use App\Http\Requests\UpdateTenantRequest;
use App\Repositories\TenantRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class TenantController extends AppBaseController
{
    /** @var  TenantRepository */
    private $tenantRepository;

    public function __construct(TenantRepository $tenantRepo)
    {
        $this->tenantRepository = $tenantRepo;
    }

    /**
     * Display a listing of the Tenant.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->tenantRepository->pushCriteria(new RequestCriteria($request));
        $tenants = $this->tenantRepository->all();

        return view('tenants.index')
            ->with('tenants', $tenants);
    }

    /**
     * Show the form for creating a new Tenant.
     *
     * @return Response
     */
    public function create()
    {
        return view('tenants.create');
    }

    /**
     * Store a newly created Tenant in storage.
     *
     * @param CreateTenantRequest $request
     *
     * @return Response
     */
    public function store(CreateTenantRequest $request)
    {
        $input = $request->all();

        $tenant = $this->tenantRepository->create($input);

        Flash::success('Tenant saved successfully.');

        return redirect(route('tenants.index'));
    }

    /**
     * Display the specified Tenant.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $tenant = $this->tenantRepository->findWithoutFail($id);

        if (empty($tenant)) {
            Flash::error('Tenant not found');

            return redirect(route('tenants.index'));
        }

        return view('tenants.show')->with('tenant', $tenant);
    }

    /**
     * Show the form for editing the specified Tenant.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $tenant = $this->tenantRepository->findWithoutFail($id);

        if (empty($tenant)) {
            Flash::error('Tenant not found');

            return redirect(route('tenants.index'));
        }

        return view('tenants.edit')->with('tenant', $tenant);
    }

    /**
     * Update the specified Tenant in storage.
     *
     * @param  int              $id
     * @param UpdateTenantRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateTenantRequest $request)
    {
        $tenant = $this->tenantRepository->findWithoutFail($id);

        if (empty($tenant)) {
            Flash::error('Tenant not found');

            return redirect(route('tenants.index'));
        }

        $tenant = $this->tenantRepository->update($request->all(), $id);

        Flash::success('Tenant updated successfully.');

        return redirect(route('tenants.index'));
    }

    /**
     * Remove the specified Tenant from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $tenant = $this->tenantRepository->findWithoutFail($id);

        if (empty($tenant)) {
            Flash::error('Tenant not found');

            return redirect(route('tenants.index'));
        }

        $this->tenantRepository->delete($id);

        Flash::success('Tenant deleted successfully.');

        return redirect(route('tenants.index'));
    }
}
