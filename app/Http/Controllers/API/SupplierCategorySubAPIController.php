<?php
/**
=============================================
-- File Name : SupplierCategoryMasterAPIController.php
-- Project Name : ERP
-- Module Name :  Supplier Category Master
-- Author : Mohamed Fayas
-- Create date : 14 - March 2018
-- Description : This file contains the all CRUD for Supplier Category Master
-- REVISION HISTORY
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateSupplierCategorySubAPIRequest;
use App\Http\Requests\API\UpdateSupplierCategorySubAPIRequest;
use App\Models\SupplierCategorySub;
use App\Models\SupplierMaster;
use App\Models\SupplierSubCategoryAssign;
use App\Repositories\SupplierCategorySubRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class SupplierCategorySubController
 * @package App\Http\Controllers\API
 */

class SupplierCategorySubAPIController extends AppBaseController
{
    /** @var  SupplierCategorySubRepository */
    private $supplierCategorySubRepository;

    public function __construct(SupplierCategorySubRepository $supplierCategorySubRepo)
    {
        $this->supplierCategorySubRepository = $supplierCategorySubRepo;
    }

    /**
     * Display a listing of the SupplierCategorySub.
     * GET|HEAD /supplierCategorySubs
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->supplierCategorySubRepository->pushCriteria(new RequestCriteria($request));
        $this->supplierCategorySubRepository->pushCriteria(new LimitOffsetCriteria($request));
        $supplierCategorySubs = $this->supplierCategorySubRepository->all();

        return $this->sendResponse($supplierCategorySubs->toArray(), 'Supplier Category Subs retrieved successfully');
    }


    public function getSubCategoriesByMasterCategory(Request $request){

        $supplierId = $request['supplierId'];
        $supplier = SupplierMaster::where('supplierCodeSystem','=',$supplierId)->first();

        if($supplier){
            $supMasterCategoryID = $supplier['supCategoryMasterID']; //493;
            $supplierCategorySubs = SupplierCategorySub::where('supMasterCategoryID','=',$supMasterCategoryID)->get();
        }else{
            $supplierCategorySubs = [];
        }

        return $this->sendResponse($supplierCategorySubs, 'Supplier Category Subs retrieved successfully');
    }

    public function addSubCategoryToSupplier(Request $request){

        $supplierSubCategory = new SupplierSubCategoryAssign();

        $supplierSubCategory->supplierID = $request['supplierId'];
        $supplierSubCategory->supSubCategoryID = $request['subCategoryId'];
        $supplierSubCategory->save();
        return $this->sendResponse($supplierSubCategory, 'Supplier Category Subs added successfully');
    }

    public function removeSubCategoryToSupplier(Request $request){

        $supplierSubCategory = SupplierSubCategoryAssign::where('supplierSubCategoryAssignID',$request['supplierSubCategoryAssignID'])->first();

        if (empty($supplierSubCategory)) {
            return $this->sendError('Supplier Category Sub not found');
        }

        $supplierSubCategory->delete();
        return $this->sendResponse($supplierSubCategory, 'Supplier Category Subs deleted successfully');
    }


    /**
     * Store a newly created SupplierCategorySub in storage.
     * POST /supplierCategorySubs
     *
     * @param CreateSupplierCategorySubAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateSupplierCategorySubAPIRequest $request)
    {
        $input = $request->all();

        $supplierCategorySubs = $this->supplierCategorySubRepository->create($input);

        return $this->sendResponse($supplierCategorySubs->toArray(), 'Supplier Category Sub saved successfully');
    }

    /**
     * Display the specified SupplierCategorySub.
     * GET|HEAD /supplierCategorySubs/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var SupplierCategorySub $supplierCategorySub */
        $supplierCategorySub = $this->supplierCategorySubRepository->findWithoutFail($id);

        if (empty($supplierCategorySub)) {
            return $this->sendError('Supplier Category Sub not found');
        }

        return $this->sendResponse($supplierCategorySub->toArray(), 'Supplier Category Sub retrieved successfully');
    }

    /**
     * Update the specified SupplierCategorySub in storage.
     * PUT/PATCH /supplierCategorySubs/{id}
     *
     * @param  int $id
     * @param UpdateSupplierCategorySubAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateSupplierCategorySubAPIRequest $request)
    {
        $input = $request->all();

        /** @var SupplierCategorySub $supplierCategorySub */
        $supplierCategorySub = $this->supplierCategorySubRepository->findWithoutFail($id);

        if (empty($supplierCategorySub)) {
            return $this->sendError('Supplier Category Sub not found');
        }

        $supplierCategorySub = $this->supplierCategorySubRepository->update($input, $id);

        return $this->sendResponse($supplierCategorySub->toArray(), 'SupplierCategorySub updated successfully');
    }

    /**
     * Remove the specified SupplierCategorySub from storage.
     * DELETE /supplierCategorySubs/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var SupplierCategorySub $supplierCategorySub */
        $supplierCategorySub = $this->supplierCategorySubRepository->findWithoutFail($id);

        if (empty($supplierCategorySub)) {
            return $this->sendError('Supplier Category Sub not found');
        }

        $supplierCategorySub->delete();

        return $this->sendResponse($id, 'Supplier Category Sub deleted successfully');
    }
}
