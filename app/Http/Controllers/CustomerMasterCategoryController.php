<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateCustomerMasterCategoryRequest;
use App\Http\Requests\UpdateCustomerMasterCategoryRequest;
use App\Repositories\CustomerMasterCategoryRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class CustomerMasterCategoryController extends AppBaseController
{
    /** @var  CustomerMasterCategoryRepository */
    private $customerMasterCategoryRepository;

    public function __construct(CustomerMasterCategoryRepository $customerMasterCategoryRepo)
    {
        $this->customerMasterCategoryRepository = $customerMasterCategoryRepo;
    }

    /**
     * Display a listing of the CustomerMasterCategory.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->customerMasterCategoryRepository->pushCriteria(new RequestCriteria($request));
        $customerMasterCategories = $this->customerMasterCategoryRepository->all();

        return view('customer_master_categories.index')
            ->with('customerMasterCategories', $customerMasterCategories);
    }

    /**
     * Show the form for creating a new CustomerMasterCategory.
     *
     * @return Response
     */
    public function create()
    {
        return view('customer_master_categories.create');
    }

    /**
     * Store a newly created CustomerMasterCategory in storage.
     *
     * @param CreateCustomerMasterCategoryRequest $request
     *
     * @return Response
     */
    public function store(CreateCustomerMasterCategoryRequest $request)
    {
        $input = $request->all();

        $customerMasterCategory = $this->customerMasterCategoryRepository->create($input);

        Flash::success('Customer Master Category saved successfully.');

        return redirect(route('customerMasterCategories.index'));
    }

    /**
     * Display the specified CustomerMasterCategory.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $customerMasterCategory = $this->customerMasterCategoryRepository->findWithoutFail($id);

        if (empty($customerMasterCategory)) {
            Flash::error('Customer Master Category not found');

            return redirect(route('customerMasterCategories.index'));
        }

        return view('customer_master_categories.show')->with('customerMasterCategory', $customerMasterCategory);
    }

    /**
     * Show the form for editing the specified CustomerMasterCategory.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $customerMasterCategory = $this->customerMasterCategoryRepository->findWithoutFail($id);

        if (empty($customerMasterCategory)) {
            Flash::error('Customer Master Category not found');

            return redirect(route('customerMasterCategories.index'));
        }

        return view('customer_master_categories.edit')->with('customerMasterCategory', $customerMasterCategory);
    }

    /**
     * Update the specified CustomerMasterCategory in storage.
     *
     * @param  int              $id
     * @param UpdateCustomerMasterCategoryRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateCustomerMasterCategoryRequest $request)
    {
        $customerMasterCategory = $this->customerMasterCategoryRepository->findWithoutFail($id);

        if (empty($customerMasterCategory)) {
            Flash::error('Customer Master Category not found');

            return redirect(route('customerMasterCategories.index'));
        }

        $customerMasterCategory = $this->customerMasterCategoryRepository->update($request->all(), $id);

        Flash::success('Customer Master Category updated successfully.');

        return redirect(route('customerMasterCategories.index'));
    }

    /**
     * Remove the specified CustomerMasterCategory from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $customerMasterCategory = $this->customerMasterCategoryRepository->findWithoutFail($id);

        if (empty($customerMasterCategory)) {
            Flash::error('Customer Master Category not found');

            return redirect(route('customerMasterCategories.index'));
        }

        $this->customerMasterCategoryRepository->delete($id);

        Flash::success('Customer Master Category deleted successfully.');

        return redirect(route('customerMasterCategories.index'));
    }
}
