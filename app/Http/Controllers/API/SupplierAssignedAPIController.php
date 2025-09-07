<?php
/**
=============================================
-- File Name : SupplierAssignedAPIController.php
-- Project Name : ERP
-- Module Name :  Supplier Assigned
-- Author : Mohamed Fayas
-- Create date : 14 - March 2018
-- Description : This file contains the all CRUD for Supplier Assigned
-- REVISION HISTORY
 * -- Date: 8-October 2018 By: Nazir Description: Added new function checkSupplierIsActive(),
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateSupplierAssignedAPIRequest;
use App\Http\Requests\API\UpdateSupplierAssignedAPIRequest;
use App\Models\SupplierAssigned;
use App\Models\SupplierMaster;
use App\Models\Company;
use App\Models\ChartOfAccount;
use App\Repositories\SupplierAssignedRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Illuminate\Support\Facades\DB;
use Response;

/**
 * Class SupplierAssignedController
 * @package App\Http\Controllers\API
 */

class SupplierAssignedAPIController extends AppBaseController
{
    /** @var  SupplierAssignedRepository */
    private $supplierAssignedRepository;

    public function __construct(SupplierAssignedRepository $supplierAssignedRepo)
    {
        $this->supplierAssignedRepository = $supplierAssignedRepo;
    }

    /**
     * Display a listing of the SupplierAssigned.
     * GET|HEAD /supplierAssigneds
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->supplierAssignedRepository->pushCriteria(new RequestCriteria($request));
        $this->supplierAssignedRepository->pushCriteria(new LimitOffsetCriteria($request));
        $supplierAssigneds = $this->supplierAssignedRepository->all();

        return $this->sendResponse($supplierAssigneds->toArray(), trans('custom.supplier_assigneds_retrieved_successfully'));
    }

    /**
     * Store a newly created SupplierAssigned in storage.
     * POST /supplierAssigneds
     *
     * @param CreateSupplierAssignedAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateSupplierAssignedAPIRequest $request)
    {
        $input = $request->all();
        $companies = $input['companySystemID'];
        unset($input['companySystemID']);
        unset( $input['primaryCompanySystemID']);
        unset( $input['importanceDescription']);
        unset( $input['natureDescription']);
        unset( $input['typeDescription']);
        unset( $input['suppliercriticalID']);
        unset( $input['description']);
        unset( $input['idyesNoselection']);
        unset( $input['YesNo']);
        unset( $input['masterIsMarkupPercentage']);
        unset( $input['isEEOSSPolicy']);

        $input = array_except($input, ['final_approved_by','company']);

        $input = $this->convertArrayToValue($input);
        foreach($companies as $companie)
        {
                if( array_key_exists ('supplierAssignedID' , $input )){
                    if(isset($companies)) {
                        $input['companySystemID'] = $companies[0];
                    }
                }
                else
                {
                    $input['companySystemID'] = $companie['id'];
                }
               
    
                $messages = [
                    'companySystemID.required' => 'Company field is required.',
                    'liabilityAccountSysemID.required' => 'Liability Account field is required.',
                    'UnbilledGRVAccountSystemID.required' => 'Un-billed Account field is required.',
                ];
                $validator = \Validator::make($input, [
                    'companySystemID' => 'required',
                    'liabilityAccountSysemID' => 'required',
                    'UnbilledGRVAccountSystemID' => 'required'
                ], $messages);
        
                if ($validator->fails()) {
                    return $this->sendError($validator->messages(), 422);
                }
        
             
                $liabilityAccountSysemID = ChartOfAccount::where('chartOfAccountSystemID',$input['liabilityAccountSysemID'])->first();
                $unbilledGRVAccountSystemID = ChartOfAccount::where('chartOfAccountSystemID',$input['UnbilledGRVAccountSystemID'])->first();
                $input['liabilityAccount'] = $liabilityAccountSysemID['AccountCode'];
                $input['UnbilledGRVAccount'] = $unbilledGRVAccountSystemID['AccountCode'];
                $input['isMarkupPercentage'] = isset($input['isMarkupPercentage'])?$input['isMarkupPercentage']:0;
                $input['markupPercentage'] = (isset($input['markupPercentage']) && $input['isMarkupPercentage']==1)?$input['markupPercentage']:0;
        
                if( array_key_exists ('supplierAssignedID' , $input )){
        
                    $supplierAssigneds = SupplierAssigned::where('supplierAssignedID', $input['supplierAssignedID'])->first();
        
                    if (empty($supplierAssigneds)) {
                        return $this->sendError(trans('custom.supplier_assigned_not_found_1'));
                    }
                    foreach ($input as $key => $value) {
                        $supplierAssigneds->$key = $value;
                    }
                    $supplierAssigneds->save();
                }else{
    
                       
                            $validatorResult = \Helper::checkCompanyForMasters($companie['id'], $input['supplierCodeSytem'], 'supplier');
                            if (!$validatorResult['success']) {
                                return $this->sendError($validatorResult['message']);
                            }
                           
                            $company = Company::where('companySystemID',$companie['id'])->first();
                            $input['companyID'] = $company['CompanyID'];
                            $supplierAssigneds = $this->supplierAssignedRepository->create($input);
    
                        
                   }

          }
        if(empty($companies)){
            return $this->sendError('Unable to assign no companies found');

        }
 
        

        

        return $this->sendResponse($supplierAssigneds->toArray(), trans('custom.supplier_assigned_saved_successfully'));
    }

    /**
     * Display the specified SupplierAssigned.
     * GET|HEAD /supplierAssigneds/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var SupplierAssigned $supplierAssigned */
        $supplierAssigned = $this->supplierAssignedRepository->findWithoutFail($id);

        if (empty($supplierAssigned)) {
            return $this->sendError(trans('custom.supplier_assigned_not_found'));
        }

        return $this->sendResponse($supplierAssigned->toArray(), trans('custom.supplier_assigned_retrieved_successfully'));
    }

    /**
     * Update the specified SupplierAssigned in storage.
     * PUT/PATCH /supplierAssigneds/{id}
     *
     * @param  int $id
     * @param UpdateSupplierAssignedAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateSupplierAssignedAPIRequest $request)
    {
        $input = $request->all();

        /** @var SupplierAssigned $supplierAssigned */
        $supplierAssigned = $this->supplierAssignedRepository->findWithoutFail($id);

        if (empty($supplierAssigned)) {
            return $this->sendError(trans('custom.supplier_assigned_not_found'));
        }

        $supplierAssigned = $this->supplierAssignedRepository->update($input, $id);

        return $this->sendResponse($supplierAssigned->toArray(), trans('custom.supplierassigned_updated_successfully'));
    }

    /**
     * Remove the specified SupplierAssigned from storage.
     * DELETE /supplierAssigneds/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var SupplierAssigned $supplierAssigned */
        $supplierAssigned = $this->supplierAssignedRepository->findWithoutFail($id);

        if (empty($supplierAssigned)) {
            return $this->sendError(trans('custom.supplier_assigned_not_found'));
        }

        $supplierAssigned->delete();

        return $this->sendResponse($id, trans('custom.supplier_assigned_deleted_successfully'));
    }

    public function checkSelectedSupplierIsActive(Request $request)
    {
        $id = $request['supplierID'];
        $companyId = $request['companyId'];

        $supplierData = SupplierAssigned::select(DB::raw("isActive"))
            ->where('supplierCodeSytem', $id)
            ->where('companySystemID', $companyId)
            ->first();

        return $this->sendResponse($supplierData, trans('custom.record_retrieved_successfully_1'));
    }

}
