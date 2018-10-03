<?php
/**
 * =============================================
 * -- File Name : CustomerCurrencyAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Customer Assigned
 * -- Author : Mohamed Fayas
 * -- Create date : 21 - March 2018
 * -- Description : This file contains the all CRUD for Customer Currency
 * -- REVISION HISTORY
 * -- Date: 21-March 2018 By: Fayas Description: Added new functions named as getCurrenciesByCustomer(),getNotAddedCurrenciesByCustomer
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateCustomerCurrencyAPIRequest;
use App\Http\Requests\API\UpdateCustomerCurrencyAPIRequest;
use App\Models\CurrencyMaster;
use App\Models\CustomerCurrency;
use App\Models\CustomerMaster;
use App\Repositories\CustomerCurrencyRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use Illuminate\Support\Facades\Auth;
use App\Repositories\UserRepository;

/**
 * Class CustomerCurrencyController
 * @package App\Http\Controllers\API
 */
class CustomerCurrencyAPIController extends AppBaseController
{
    /** @var  CustomerCurrencyRepository */
    private $customerCurrencyRepository;
    private $userRepository;

    public function __construct(CustomerCurrencyRepository $customerCurrencyRepo, UserRepository $userRepo)
    {
        $this->customerCurrencyRepository = $customerCurrencyRepo;
        $this->userRepository = $userRepo;
    }

    /**
     * Display a listing of the CustomerCurrency.
     * GET|HEAD /customerCurrencies
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->customerCurrencyRepository->pushCriteria(new RequestCriteria($request));
        $this->customerCurrencyRepository->pushCriteria(new LimitOffsetCriteria($request));
        $customerCurrencies = $this->customerCurrencyRepository->all();

        return $this->sendResponse($customerCurrencies->toArray(), 'Customer Currencies retrieved successfully');
    }

    /**
     * get all Added currencies for Customer
     * GET /getAddedCurrenciesByCustomer
     *
     * @param Request $request
     * @return Response
     */
    public function getAddedCurrenciesByCustomer(Request $request)
    {

        $customerId = $request['customerId'];
        $customer = CustomerMaster::where('customerCodeSystem', '=', $customerId)->first();
        if ($customer) {
            $customerCurrencies = CustomerCurrency::where('customerCodeSystem', $customerId)
                ->with(['currencyMaster'])
                ->orderBy('createdDateTime', 'DESC')
                ->get();
        } else {
            $customerCurrencies = [];
        }

        return $this->sendResponse($customerCurrencies, 'customer currencies retrieved successfully');
    }

    /**
     *  Display a listing of the currencies not assigned for specific customer.
     * Get /getNotAddedCurrenciesByCustomer
     *
     * @param Request $request
     *
     * @return Response
     */
    public function getNotAddedCurrenciesByCustomer(Request $request)
    {

        $customerId = $request->get('customerId');
        $companies = CurrencyMaster::whereDoesntHave('customerCurrencies', function ($query) use ($customerId) {
                                        $query->where('customerCodeSystem', '=', $customerId);
                                    })->get(['currencyID',
                                            'CurrencyName']);

        return $this->sendResponse($companies->toArray(), 'Companies retrieved successfully');
    }

    /**
     * Store a newly created CustomerCurrency in storage.
     * POST /customerCurrencies
     *
     * @param CreateCustomerCurrencyAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateCustomerCurrencyAPIRequest $request)
    {

        $input = $request->all();
        $id = Auth::id();
        $user = $this->userRepository->with(['employee'])->findWithoutFail($id);
        $empId = $user->employee['empID'];
        $empName = $user->employee['empName'];

        $input = array_except($input,['currency_master']);

        $input = $this->convertArrayToValue($input);

        if (array_key_exists('custCurrencyAutoID', $input)) {

            $customerCurrencies = CustomerCurrency::where('custCurrencyAutoID', $input['custCurrencyAutoID'])->first();

            if (empty($customerCurrencies)) {
                return $this->sendError('customer currency not found');
            }

            if($input['isAssigned'] == true || $input['isAssigned'] == 1){
                $input['isAssigned'] = -1;
            }

            if($input['isDefault'] == true || $input['isDefault'] == 1){
                $input['isDefault'] = -1;

                $customerCurrencies = CustomerCurrency::where('customerCodeSystem',$request['customerCodeSystem'])->get();
                foreach ($customerCurrencies as $cc){
                    $tem_cc = CustomerCurrency::where('custCurrencyAutoID',$cc['custCurrencyAutoID'])->first();
                    $tem_cc->isDefault  = 0;
                    $tem_cc->save();
                }


                //return  CustomerCurrency::where('customerCodeSystem',$request['customerCodeSystem'])->get();

            }

            $customerCurrencies = $this->customerCurrencyRepository->update($input,$input['custCurrencyAutoID']);

        } else {
            $customer = CustomerMaster::where('customerCodeSystem', $input['customerCodeSystem'])->first();
            if ($customer) {
                $input['customerCode'] = $customer->CutomerCode;
            }
            if($input['isAssigned'] == true || $input['isAssigned'] == 1){
                $input['isAssigned'] = -1;
            }
            $input['createdBy'] = $empId;
            $customerCurrencies = $this->customerCurrencyRepository->create($input);
        }

        return $this->sendResponse($customerCurrencies->toArray(), 'Customer currency saved successfully');


    }

    /**
     * Display the specified CustomerCurrency.
     * GET|HEAD /customerCurrencies/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var CustomerCurrency $customerCurrency */
        $customerCurrency = $this->customerCurrencyRepository->findWithoutFail($id);

        if (empty($customerCurrency)) {
            return $this->sendError('Customer Currency not found');
        }

        return $this->sendResponse($customerCurrency->toArray(), 'Customer Currency retrieved successfully');
    }

    /**
     * Update the specified CustomerCurrency in storage.
     * PUT/PATCH /customerCurrencies/{id}
     *
     * @param  int $id
     * @param UpdateCustomerCurrencyAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateCustomerCurrencyAPIRequest $request)
    {
        $input = $request->all();

        /** @var CustomerCurrency $customerCurrency */
        $customerCurrency = $this->customerCurrencyRepository->findWithoutFail($id);

        if (empty($customerCurrency)) {
            return $this->sendError('Customer Currency not found');
        }

        $customerCurrency = $this->customerCurrencyRepository->update($input, $id);

        return $this->sendResponse($customerCurrency->toArray(), 'CustomerCurrency updated successfully');
    }

    /**
     * Remove the specified CustomerCurrency from storage.
     * DELETE /customerCurrencies/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var CustomerCurrency $customerCurrency */
        $customerCurrency = $this->customerCurrencyRepository->findWithoutFail($id);

        if (empty($customerCurrency)) {
            return $this->sendError('Customer Currency not found');
        }

        $customerCurrency->delete();

        return $this->sendResponse($id, 'Customer Currency deleted successfully');
    }
}
