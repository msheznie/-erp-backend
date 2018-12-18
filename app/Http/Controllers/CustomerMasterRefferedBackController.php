<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateCustomerMasterRefferedBackRequest;
use App\Http\Requests\UpdateCustomerMasterRefferedBackRequest;
use App\Repositories\CustomerMasterRefferedBackRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class CustomerMasterRefferedBackController extends AppBaseController
{
    /** @var  CustomerMasterRefferedBackRepository */
    private $customerMasterRefferedBackRepository;

    public function __construct(CustomerMasterRefferedBackRepository $customerMasterRefferedBackRepo)
    {
        $this->customerMasterRefferedBackRepository = $customerMasterRefferedBackRepo;
    }

    /**
     * Display a listing of the CustomerMasterRefferedBack.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->customerMasterRefferedBackRepository->pushCriteria(new RequestCriteria($request));
        $customerMasterRefferedBacks = $this->customerMasterRefferedBackRepository->all();

        return view('customer_master_reffered_backs.index')
            ->with('customerMasterRefferedBacks', $customerMasterRefferedBacks);
    }

    /**
     * Show the form for creating a new CustomerMasterRefferedBack.
     *
     * @return Response
     */
    public function create()
    {
        return view('customer_master_reffered_backs.create');
    }

    /**
     * Store a newly created CustomerMasterRefferedBack in storage.
     *
     * @param CreateCustomerMasterRefferedBackRequest $request
     *
     * @return Response
     */
    public function store(CreateCustomerMasterRefferedBackRequest $request)
    {
        $input = $request->all();

        $customerMasterRefferedBack = $this->customerMasterRefferedBackRepository->create($input);

        Flash::success('Customer Master Reffered Back saved successfully.');

        return redirect(route('customerMasterRefferedBacks.index'));
    }

    /**
     * Display the specified CustomerMasterRefferedBack.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $customerMasterRefferedBack = $this->customerMasterRefferedBackRepository->findWithoutFail($id);

        if (empty($customerMasterRefferedBack)) {
            Flash::error('Customer Master Reffered Back not found');

            return redirect(route('customerMasterRefferedBacks.index'));
        }

        return view('customer_master_reffered_backs.show')->with('customerMasterRefferedBack', $customerMasterRefferedBack);
    }

    /**
     * Show the form for editing the specified CustomerMasterRefferedBack.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $customerMasterRefferedBack = $this->customerMasterRefferedBackRepository->findWithoutFail($id);

        if (empty($customerMasterRefferedBack)) {
            Flash::error('Customer Master Reffered Back not found');

            return redirect(route('customerMasterRefferedBacks.index'));
        }

        return view('customer_master_reffered_backs.edit')->with('customerMasterRefferedBack', $customerMasterRefferedBack);
    }

    /**
     * Update the specified CustomerMasterRefferedBack in storage.
     *
     * @param  int              $id
     * @param UpdateCustomerMasterRefferedBackRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateCustomerMasterRefferedBackRequest $request)
    {
        $customerMasterRefferedBack = $this->customerMasterRefferedBackRepository->findWithoutFail($id);

        if (empty($customerMasterRefferedBack)) {
            Flash::error('Customer Master Reffered Back not found');

            return redirect(route('customerMasterRefferedBacks.index'));
        }

        $customerMasterRefferedBack = $this->customerMasterRefferedBackRepository->update($request->all(), $id);

        Flash::success('Customer Master Reffered Back updated successfully.');

        return redirect(route('customerMasterRefferedBacks.index'));
    }

    /**
     * Remove the specified CustomerMasterRefferedBack from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $customerMasterRefferedBack = $this->customerMasterRefferedBackRepository->findWithoutFail($id);

        if (empty($customerMasterRefferedBack)) {
            Flash::error('Customer Master Reffered Back not found');

            return redirect(route('customerMasterRefferedBacks.index'));
        }

        $this->customerMasterRefferedBackRepository->delete($id);

        Flash::success('Customer Master Reffered Back deleted successfully.');

        return redirect(route('customerMasterRefferedBacks.index'));
    }
}
