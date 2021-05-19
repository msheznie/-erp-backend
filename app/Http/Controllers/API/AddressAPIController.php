<?php

/**
 * =============================================
 * -- File Name : AddressAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Address
 * -- Author : Mohamed Fayas
 * -- Create date : 04- May 2018
 * -- Description : This file contains the all CRUD for Address
 * -- REVISION HISTORY
 * -- Date: 04-May 2018 By: Fayas Description: Added new functions named as getAllAddresses()
 * -- Date: 08-May 2018 By: Fayas Description: Added new functions named as getAddressFormData()
 */

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateAddressAPIRequest;
use App\Http\Requests\API\UpdateAddressAPIRequest;
use App\Models\Address;
use App\Models\AddressType;
use App\Models\Company;
use App\Repositories\AddressRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class AddressController
 * @package App\Http\Controllers\API
 */

class AddressAPIController extends AppBaseController
{
    /** @var  AddressRepository */
    private $addressRepository;

    public function __construct(AddressRepository $addressRepo)
    {
        $this->addressRepository = $addressRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/addresses",
     *      summary="Get a listing of the Addresses.",
     *      tags={"Address"},
     *      description="Get all Addresses",
     *      produces={"application/json"},
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  type="array",
     *                  @SWG\Items(ref="#/definitions/Address")
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function index(Request $request)
    {
        $this->addressRepository->pushCriteria(new RequestCriteria($request));
        $this->addressRepository->pushCriteria(new LimitOffsetCriteria($request));
        $addresses = $this->addressRepository->all();

        return $this->sendResponse($addresses->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.addresses')]));
    }

    /**
     * @param CreateAddressAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/addresses",
     *      summary="Store a newly created Address in storage",
     *      tags={"Address"},
     *      description="Store Address",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Address that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Address")
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/Address"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateAddressAPIRequest $request)
    {
        $input = $request->all();

        $company = Company::where('companySystemID',$input['companySystemID'])->first();

        if($company){
            $input['companyID'] = $company->CompanyID;
        }

        if(array_key_exists ('isDefault' , $input )) {
            if ($input['isDefault'] == true || $input['isDefault'] == 1) {
                $input['isDefault'] = -1;

                $activeAddress = Address::where('companySystemID',$input['companySystemID'])
                                        ->where('addressTypeID', $input['addressTypeID'])
                                        ->where('isDefault',-1)
                                        ->get();

                foreach ($activeAddress as $ad){
                    $temAddress = Address::where('addressID',$ad['addressID'])->first();
                    $temAddress->isDefault = 0;
                    $temAddress->save();
                }
            }
        }
        $addresses = $this->addressRepository->create($input);

        return $this->sendResponse($addresses->toArray(), trans('custom.save', ['attribute' => trans('custom.addresses')]));
    }

    /**
     * get form data for Customer Master.
     * GET /getCustomerFormData
     *
     * @param Request $request
     * @return Response
     */

    public function getAddressFormData(Request $request){

       $types = AddressType::all();

        $output = array(
            'types' => $types
        );

        return $this->sendResponse($output, trans('custom.retrieve', ['attribute' => trans('custom.record')]));
    }

    /**
     * Display a listing of the purchase address.
     * GET|HEAD /getAllAddresses
     *
     * @param Request $request
     * @return Response
     */
    public function getAllAddresses(Request $request)
    {
        $input = $request->all();

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $companyId = $request['companyId'];

        $isGroup = \Helper::checkIsCompanyGroup($companyId);

        if($isGroup){
            $childCompanies = \Helper::getGroupCompany($companyId);
        }else{
            $childCompanies = [$companyId];
        }

        $addresses = Address::whereIn('companySystemID',$childCompanies)
                                        ->with(['type'])
                                        ->select('erp_address.*');

        $search = $request->input('search.value');
        if($search){
            $addresses =   $addresses //->where('contactPersonEmail','LIKE',"%{$search}%")
                                    ->where('addressDescrption', 'LIKE', "%{$search}%");
                                    //->orWhere('contactPersonID', 'LIKE', "%{$search}%");
        }

        return \DataTables::eloquent($addresses)
            ->order(function ($query) use ($input) {
                if (request()->has('order') ) {
                    if($input['order'][0]['column'] == 0)
                    {
                        $query->orderBy('addressID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->addColumn('Actions', 'Actions', "Actions")
            //->addColumn('Index', 'Index', "Index")
            ->make(true);
    }



    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/addresses/{id}",
     *      summary="Display the specified Address",
     *      tags={"Address"},
     *      description="Get Address",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Address",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/Address"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function show($id)
    {
        /** @var Address $address */
        $address = $this->addressRepository->findWithoutFail($id);

        if (empty($address)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.addresses')]));
        }

        return $this->sendResponse($address->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.addresses')]));
    }

    /**
     * @param int $id
     * @param UpdateAddressAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/addresses/{id}",
     *      summary="Update the specified Address in storage",
     *      tags={"Address"},
     *      description="Update Address",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Address",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Address that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Address")
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/Address"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateAddressAPIRequest $request)
    {
        $input = $request->all();

        $input = $this->convertArrayToValue($input);

        /** @var Address $address */
        $address = $this->addressRepository->findWithoutFail($id);

        if (empty($address)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.addresses')]));
        }

        $company = Company::where('companySystemID',$input['companySystemID'])->first();

        if($company){
            $input['companyID'] = $company->CompanyID;
        }

        if(array_key_exists ('isDefault' , $input )) {
            if ($input['isDefault'] == true || $input['isDefault'] == 1) {
                $input['isDefault'] = -1;

                $activeAddress = Address::where('companySystemID',$input['companySystemID'])
                    ->where('addressTypeID', $input['addressTypeID'])
                    ->where('isDefault',-1)
                    ->get();

                foreach ($activeAddress as $ad){
                    $temAddress = Address::where('addressID',$ad['addressID'])->first();
                    $temAddress->isDefault = 0;
                    $temAddress->save();
                }
            }
        }

        $address = $this->addressRepository->update($input, $id);

        return $this->sendResponse($address->toArray(), trans('custom.update', ['attribute' => trans('custom.addresses')]));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/addresses/{id}",
     *      summary="Remove the specified Address from storage",
     *      tags={"Address"},
     *      description="Delete Address",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Address",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function destroy($id)
    {
        /** @var Address $address */
        $address = $this->addressRepository->findWithoutFail($id);

        if (empty($address)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.addresses')]));
        }

        $address->delete();

        return $this->sendResponse($id, trans('custom.delete', ['attribute' => trans('custom.addresses')]));
    }
}
