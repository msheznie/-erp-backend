<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateCustomerCatalogDetailRequest;
use App\Http\Requests\UpdateCustomerCatalogDetailRequest;
use App\Repositories\CustomerCatalogDetailRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class CustomerCatalogDetailController extends AppBaseController
{
    /** @var  CustomerCatalogDetailRepository */
    private $customerCatalogDetailRepository;

    public function __construct(CustomerCatalogDetailRepository $customerCatalogDetailRepo)
    {
        $this->customerCatalogDetailRepository = $customerCatalogDetailRepo;
    }

    /**
     * Display a listing of the CustomerCatalogDetail.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->customerCatalogDetailRepository->pushCriteria(new RequestCriteria($request));
        $customerCatalogDetails = $this->customerCatalogDetailRepository->all();

        return view('customer_catalog_details.index')
            ->with('customerCatalogDetails', $customerCatalogDetails);
    }

    /**
     * Show the form for creating a new CustomerCatalogDetail.
     *
     * @return Response
     */
    public function create()
    {
        return view('customer_catalog_details.create');
    }

    /**
     * Store a newly created CustomerCatalogDetail in storage.
     *
     * @param CreateCustomerCatalogDetailRequest $request
     *
     * @return Response
     */
    public function store(CreateCustomerCatalogDetailRequest $request)
    {
        $input = $request->all();

        $customerCatalogDetail = $this->customerCatalogDetailRepository->create($input);

        Flash::success('Customer Catalog Detail saved successfully.');

        return redirect(route('customerCatalogDetails.index'));
    }

    /**
     * Display the specified CustomerCatalogDetail.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $customerCatalogDetail = $this->customerCatalogDetailRepository->findWithoutFail($id);

        if (empty($customerCatalogDetail)) {
            Flash::error('Customer Catalog Detail not found');

            return redirect(route('customerCatalogDetails.index'));
        }

        return view('customer_catalog_details.show')->with('customerCatalogDetail', $customerCatalogDetail);
    }

    /**
     * Show the form for editing the specified CustomerCatalogDetail.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $customerCatalogDetail = $this->customerCatalogDetailRepository->findWithoutFail($id);

        if (empty($customerCatalogDetail)) {
            Flash::error('Customer Catalog Detail not found');

            return redirect(route('customerCatalogDetails.index'));
        }

        return view('customer_catalog_details.edit')->with('customerCatalogDetail', $customerCatalogDetail);
    }

    /**
     * Update the specified CustomerCatalogDetail in storage.
     *
     * @param  int              $id
     * @param UpdateCustomerCatalogDetailRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateCustomerCatalogDetailRequest $request)
    {
        $customerCatalogDetail = $this->customerCatalogDetailRepository->findWithoutFail($id);

        if (empty($customerCatalogDetail)) {
            Flash::error('Customer Catalog Detail not found');

            return redirect(route('customerCatalogDetails.index'));
        }

        $customerCatalogDetail = $this->customerCatalogDetailRepository->update($request->all(), $id);

        Flash::success('Customer Catalog Detail updated successfully.');

        return redirect(route('customerCatalogDetails.index'));
    }

    /**
     * Remove the specified CustomerCatalogDetail from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $customerCatalogDetail = $this->customerCatalogDetailRepository->findWithoutFail($id);

        if (empty($customerCatalogDetail)) {
            Flash::error('Customer Catalog Detail not found');

            return redirect(route('customerCatalogDetails.index'));
        }

        $this->customerCatalogDetailRepository->delete($id);

        Flash::success('Customer Catalog Detail deleted successfully.');

        return redirect(route('customerCatalogDetails.index'));
    }
}
