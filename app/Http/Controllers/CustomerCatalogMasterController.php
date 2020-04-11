<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateCustomerCatalogMasterRequest;
use App\Http\Requests\UpdateCustomerCatalogMasterRequest;
use App\Repositories\CustomerCatalogMasterRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class CustomerCatalogMasterController extends AppBaseController
{
    /** @var  CustomerCatalogMasterRepository */
    private $customerCatalogMasterRepository;

    public function __construct(CustomerCatalogMasterRepository $customerCatalogMasterRepo)
    {
        $this->customerCatalogMasterRepository = $customerCatalogMasterRepo;
    }

    /**
     * Display a listing of the CustomerCatalogMaster.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->customerCatalogMasterRepository->pushCriteria(new RequestCriteria($request));
        $customerCatalogMasters = $this->customerCatalogMasterRepository->all();

        return view('customer_catalog_masters.index')
            ->with('customerCatalogMasters', $customerCatalogMasters);
    }

    /**
     * Show the form for creating a new CustomerCatalogMaster.
     *
     * @return Response
     */
    public function create()
    {
        return view('customer_catalog_masters.create');
    }

    /**
     * Store a newly created CustomerCatalogMaster in storage.
     *
     * @param CreateCustomerCatalogMasterRequest $request
     *
     * @return Response
     */
    public function store(CreateCustomerCatalogMasterRequest $request)
    {
        $input = $request->all();

        $customerCatalogMaster = $this->customerCatalogMasterRepository->create($input);

        Flash::success('Customer Catalog Master saved successfully.');

        return redirect(route('customerCatalogMasters.index'));
    }

    /**
     * Display the specified CustomerCatalogMaster.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $customerCatalogMaster = $this->customerCatalogMasterRepository->findWithoutFail($id);

        if (empty($customerCatalogMaster)) {
            Flash::error('Customer Catalog Master not found');

            return redirect(route('customerCatalogMasters.index'));
        }

        return view('customer_catalog_masters.show')->with('customerCatalogMaster', $customerCatalogMaster);
    }

    /**
     * Show the form for editing the specified CustomerCatalogMaster.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $customerCatalogMaster = $this->customerCatalogMasterRepository->findWithoutFail($id);

        if (empty($customerCatalogMaster)) {
            Flash::error('Customer Catalog Master not found');

            return redirect(route('customerCatalogMasters.index'));
        }

        return view('customer_catalog_masters.edit')->with('customerCatalogMaster', $customerCatalogMaster);
    }

    /**
     * Update the specified CustomerCatalogMaster in storage.
     *
     * @param  int              $id
     * @param UpdateCustomerCatalogMasterRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateCustomerCatalogMasterRequest $request)
    {
        $customerCatalogMaster = $this->customerCatalogMasterRepository->findWithoutFail($id);

        if (empty($customerCatalogMaster)) {
            Flash::error('Customer Catalog Master not found');

            return redirect(route('customerCatalogMasters.index'));
        }

        $customerCatalogMaster = $this->customerCatalogMasterRepository->update($request->all(), $id);

        Flash::success('Customer Catalog Master updated successfully.');

        return redirect(route('customerCatalogMasters.index'));
    }

    /**
     * Remove the specified CustomerCatalogMaster from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $customerCatalogMaster = $this->customerCatalogMasterRepository->findWithoutFail($id);

        if (empty($customerCatalogMaster)) {
            Flash::error('Customer Catalog Master not found');

            return redirect(route('customerCatalogMasters.index'));
        }

        $this->customerCatalogMasterRepository->delete($id);

        Flash::success('Customer Catalog Master deleted successfully.');

        return redirect(route('customerCatalogMasters.index'));
    }
}
