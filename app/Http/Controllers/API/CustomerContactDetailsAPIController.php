<?php
/**
=============================================
-- File Name : CustomerContactDetailsAPIController.php
-- Project Name : ERP
-- Module Name :  Customer Contact Details
-- Author : Mohamed Fayas
-- Create date : 25 - April 2019
-- Description : This file contains the all CRUD for Customer Contact Details
-- REVISION HISTORY
-- Date: 25 - April 2019 By: Fayas Description: Added new functions named as contactDetailsByCustomer()
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateCustomerContactDetailsAPIRequest;
use App\Http\Requests\API\UpdateCustomerContactDetailsAPIRequest;
use App\Models\CustomerContactDetails;
use App\Repositories\CustomerContactDetailsRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\DB;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class CustomerContactDetailsController
 * @package App\Http\Controllers\API
 */

class CustomerContactDetailsAPIController extends AppBaseController
{
    /** @var  CustomerContactDetailsRepository */
    private $customerContactDetailsRepository;

    public function __construct(CustomerContactDetailsRepository $customerContactDetailsRepo)
    {
        $this->customerContactDetailsRepository = $customerContactDetailsRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/customerContactDetails",
     *      summary="Get a listing of the CustomerContactDetails.",
     *      tags={"CustomerContactDetails"},
     *      description="Get all CustomerContactDetails",
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
     *                  @SWG\Items(ref="#/definitions/CustomerContactDetails")
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
        $this->customerContactDetailsRepository->pushCriteria(new RequestCriteria($request));
        $this->customerContactDetailsRepository->pushCriteria(new LimitOffsetCriteria($request));
        $customerContactDetails = $this->customerContactDetailsRepository->all();

        return $this->sendResponse($customerContactDetails->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.customer_contact_details')]));
    }

    /**
     * @param CreateCustomerContactDetailsAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/customerContactDetails",
     *      summary="Store a newly created CustomerContactDetails in storage",
     *      tags={"CustomerContactDetails"},
     *      description="Store CustomerContactDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="CustomerContactDetails that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/CustomerContactDetails")
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
     *                  ref="#/definitions/CustomerContactDetails"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateCustomerContactDetailsAPIRequest $request)
    {

        $input = $request->all();
        foreach ($input as $key => $value) {
            if (is_array($input[$key]))
                $input[$key] = $input[$key][0];
        }


        $validator = \Validator::make($input, [
            'customerID' => 'required',
            'contactTypeID' => 'required',
            'contactPersonName' => 'required',
            'contactPersonTelephone' => 'required',
            'contactPersonFax' => 'required',
            'contactPersonEmail' => 'required',
            'isDefault' => 'required'
        ]);


        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        if($input['isDefault'] == true || $input['isDefault'] == -1){
            $supplierAllContacts = CustomerContactDetails::where('customerID',$input['customerID'])->get();
            foreach ($supplierAllContacts as $sc)
            {
                $tem_sc = CustomerContactDetails::where('customerContactID',$sc['customerContactID'])
                    ->first();
                $tem_sc->isDefault  = 0;
                $tem_sc->save();
            }
        }

        if($input['isDefault'] == true){
            $input['isDefault'] = -1;
        }else if($input['isDefault'] == false){
            $input['isDefault'] = 0;
        }

        if( array_key_exists ('customerContactID' , $input )){
            unset($input['supplierContactTypeID']);
            unset($input['supplierContactDescription']);
            $customerContactDetails = $this->customerContactDetailsRepository->update($input,$input['customerContactID']);

        }else{
            $customerContactDetails = $this->customerContactDetailsRepository->create($input);
        }
        
        return $this->sendResponse($customerContactDetails->toArray(), trans('custom.save', ['attribute' => trans('custom.customer_contact_details')]));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/customerContactDetails/{id}",
     *      summary="Display the specified CustomerContactDetails",
     *      tags={"CustomerContactDetails"},
     *      description="Get CustomerContactDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CustomerContactDetails",
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
     *                  ref="#/definitions/CustomerContactDetails"
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
        /** @var CustomerContactDetails $customerContactDetails */
        $customerContactDetails = $this->customerContactDetailsRepository->findWithoutFail($id);

        if (empty($customerContactDetails)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.customer_contact_details')]));
        }

        return $this->sendResponse($customerContactDetails->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.customer_contact_details')]));
    }

    /**
     * @param int $id
     * @param UpdateCustomerContactDetailsAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/customerContactDetails/{id}",
     *      summary="Update the specified CustomerContactDetails in storage",
     *      tags={"CustomerContactDetails"},
     *      description="Update CustomerContactDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CustomerContactDetails",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="CustomerContactDetails that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/CustomerContactDetails")
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
     *                  ref="#/definitions/CustomerContactDetails"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateCustomerContactDetailsAPIRequest $request)
    {
        $input = $request->all();

        /** @var CustomerContactDetails $customerContactDetails */
        $customerContactDetails = $this->customerContactDetailsRepository->findWithoutFail($id);

        if (empty($customerContactDetails)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.customer_contact_details')]));
        }

        $customerContactDetails = $this->customerContactDetailsRepository->update($input, $id);

        return $this->sendResponse($customerContactDetails->toArray(), trans('custom.update', ['attribute' => trans('custom.customer_contact_details')]));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/customerContactDetails/{id}",
     *      summary="Remove the specified CustomerContactDetails from storage",
     *      tags={"CustomerContactDetails"},
     *      description="Delete CustomerContactDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CustomerContactDetails",
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
        /** @var CustomerContactDetails $customerContactDetails */
        $customerContactDetails = $this->customerContactDetailsRepository->findWithoutFail($id);

        if (empty($customerContactDetails)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.customer_contact_details')]));
        }

        $customerContactDetails->delete();

        return $this->sendResponse($id, trans('custom.delete', ['attribute' => trans('custom.customer_contact_details')]));
    }

    /**
     * Display a listing of the contactDetails by customer.
     * GET|HEAD /contactDetailsByCustomer
     *
     * @param Request $request
     * @return Response
     */
    public function contactDetailsByCustomer(Request $request){

        $customerId = $request->get('customerId');

        $details =  DB::table('customercontactdetails')
            ->leftJoin('suppliercontacttype','customercontactdetails.contactTypeID','=','suppliercontacttype.supplierContactTypeID')
            ->where('customerID',$customerId)
            ->orderBy('customerContactID', 'DESC')
            ->get();

        return $this->sendResponse($details->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.customer_contact_details')]));
    }

}
