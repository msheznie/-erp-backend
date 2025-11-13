<?php
/**
=============================================
-- File Name : SupplierContactDetailsAPIController.php
-- Project Name : ERP
-- Module Name :  Supplier Contact Details
-- Author : Mohamed Fayas
-- Create date : 14 - March 2018
-- Description : This file contains the all CRUD for Supplier Contact Details
-- REVISION HISTORY
-- Date: 14-March 2018 By: Fayas Description: Added new functions named as getContactDetailsBySupplier()
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateSupplierContactDetailsAPIRequest;
use App\Http\Requests\API\UpdateSupplierContactDetailsAPIRequest;
use App\Models\SupplierContactDetails;
use App\Repositories\SupplierContactDetailsRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use Illuminate\Support\Facades\DB;

/**
 * Class SupplierContactDetailsController
 * @package App\Http\Controllers\API
 */

class SupplierContactDetailsAPIController extends AppBaseController
{
    /** @var  SupplierContactDetailsRepository */
    private $supplierContactDetailsRepository;

    public function __construct(SupplierContactDetailsRepository $supplierContactDetailsRepo)
    {
        $this->supplierContactDetailsRepository = $supplierContactDetailsRepo;
    }

    /**
     * Display a listing of the SupplierContactDetails.
     * GET|HEAD /supplierContactDetails
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->supplierContactDetailsRepository->pushCriteria(new RequestCriteria($request));
        $this->supplierContactDetailsRepository->pushCriteria(new LimitOffsetCriteria($request));
        $supplierContactDetails = $this->supplierContactDetailsRepository->all();

        return $this->sendResponse($supplierContactDetails->toArray(), trans('custom.supplier_contact_details_retrieved_successfully'));
    }

    /**
     * Display a listing of the SupplierContactDetails by supplier.
     * GET|HEAD /getContactDetailsBySupplier
     *
     * @param Request $request
     * @return Response
     */
    public function getContactDetailsBySupplier(Request $request){

        $supplierId = $request['supplierId'];

        $supplierContactDetails =  DB::table('suppliercontactdetails')
            ->leftJoin('suppliercontacttype','suppliercontactdetails.contactTypeID','=','suppliercontacttype.supplierContactTypeID')
            ->where('supplierID',$supplierId)
            ->orderBy('supplierContactID', 'DESC')
            ->get();

        return $this->sendResponse($supplierContactDetails->toArray(), trans('custom.supplier_contact_details_retrieved_successfully'));
    }

    /**
     * Store a newly created SupplierContactDetails in storage.
     * POST /supplierContactDetails
     *
     * @param CreateSupplierContactDetailsAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateSupplierContactDetailsAPIRequest $request)
    {
        $input = $request->all();
        foreach ($input as $key => $value) {
            if (is_array($input[$key]))
                $input[$key] = $input[$key][0];
        }

        $input['isDefault'] = isset($input['isDefault']) ? $input['isDefault'] : false;

        if($input['isDefault'] == true || $input['isDefault'] == -1){
            $supplierAllContacts = SupplierContactDetails::where('supplierID',$input['supplierID'])->get();
            foreach ($supplierAllContacts as $sc)
            {
                $tem_sc = SupplierContactDetails::where('supplierContactID',$sc['supplierContactID'])
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

        if( array_key_exists ('supplierContactID' , $input )){
            unset($input['supplierContactTypeID']);
            unset($input['supplierContactDescription']);
            $supplierContactDetails = $this->supplierContactDetailsRepository->update($input,$input['supplierContactID']);

        }else{
            $supplierContactDetails = $this->supplierContactDetailsRepository->create($input);
        }
        return $this->sendResponse($supplierContactDetails->toArray(), trans('custom.supplier_contact_details_saved_successfully'));
    }

    /**
     * Display the specified SupplierContactDetails.
     * GET|HEAD /supplierContactDetails/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var SupplierContactDetails $supplierContactDetails */
        $supplierContactDetails = $this->supplierContactDetailsRepository->findWithoutFail($id);

        if (empty($supplierContactDetails)) {
            return $this->sendError(trans('custom.supplier_contact_details_not_found'));
        }

        return $this->sendResponse($supplierContactDetails->toArray(), trans('custom.supplier_contact_details_retrieved_successfully'));
    }

    /**
     * Update the specified SupplierContactDetails in storage.
     * PUT/PATCH /supplierContactDetails/{id}
     *
     * @param  int $id
     * @param UpdateSupplierContactDetailsAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateSupplierContactDetailsAPIRequest $request)
    {
        $input = $request->all();

        /** @var SupplierContactDetails $supplierContactDetails */
        $supplierContactDetails = $this->supplierContactDetailsRepository->findWithoutFail($id);

        if (empty($supplierContactDetails)) {
            return $this->sendError(trans('custom.supplier_contact_details_not_found'));
        }

        $supplierContactDetails = $this->supplierContactDetailsRepository->update($input, $id);

        return $this->sendResponse($supplierContactDetails->toArray(), trans('custom.suppliercontactdetails_updated_successfully'));
    }

    /**
     * Remove the specified SupplierContactDetails from storage.
     * DELETE /supplierContactDetails/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var SupplierContactDetails $supplierContactDetails */
        $supplierContactDetails = $this->supplierContactDetailsRepository->findWithoutFail($id);

        if (empty($supplierContactDetails)) {
            return $this->sendError(trans('custom.supplier_contact_details_not_found'));
        }

        $supplierContactDetails->delete();

        return $this->sendResponse($id, trans('custom.supplier_contact_details_deleted_successfully'));
    }
}
