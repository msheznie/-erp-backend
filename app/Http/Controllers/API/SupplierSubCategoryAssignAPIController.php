<?php
/**
=============================================
-- File Name : SupplierSubCategoryAssignAPIController.php
-- Project Name : ERP
-- Module Name :  Supplier Sub Category Assign
-- Author : Mohamed Fayas
-- Create date : 14 - March 2018
-- Description : This file contains the all CRUD for Supplier Sub Category Assign
-- REVISION HISTORY
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateSupplierSubCategoryAssignAPIRequest;
use App\Http\Requests\API\UpdateSupplierSubCategoryAssignAPIRequest;
use App\Models\SupplierSubCategoryAssign;
use App\Repositories\SupplierSubCategoryAssignRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class SupplierSubCategoryAssignController
 * @package App\Http\Controllers\API
 */

class SupplierSubCategoryAssignAPIController extends AppBaseController
{
    /** @var  SupplierSubCategoryAssignRepository */
    private $supplierSubCategoryAssignRepository;

    public function __construct(SupplierSubCategoryAssignRepository $supplierSubCategoryAssignRepo)
    {
        $this->supplierSubCategoryAssignRepository = $supplierSubCategoryAssignRepo;
    }

    /**
     * Display a listing of the SupplierSubCategoryAssign.
     * GET|HEAD /supplierSubCategoryAssigns
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->supplierSubCategoryAssignRepository->pushCriteria(new RequestCriteria($request));
        $this->supplierSubCategoryAssignRepository->pushCriteria(new LimitOffsetCriteria($request));
        $supplierSubCategoryAssigns = $this->supplierSubCategoryAssignRepository->all();

        return $this->sendResponse($supplierSubCategoryAssigns->toArray(), trans('custom.supplier_sub_category_assigns_retrieved_successful'));
    }

    /**
     * Store a newly created SupplierSubCategoryAssign in storage.
     * POST /supplierSubCategoryAssigns
     *
     * @param CreateSupplierSubCategoryAssignAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateSupplierSubCategoryAssignAPIRequest $request)
    {
        $input = $request->all();

        $supplierSubCategoryAssigns = $this->supplierSubCategoryAssignRepository->create($input);

        return $this->sendResponse($supplierSubCategoryAssigns->toArray(), trans('custom.supplier_sub_category_assign_saved_successfully'));
    }

    /**
     * Display the specified SupplierSubCategoryAssign.
     * GET|HEAD /supplierSubCategoryAssigns/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var SupplierSubCategoryAssign $supplierSubCategoryAssign */
        $supplierSubCategoryAssign = $this->supplierSubCategoryAssignRepository->findWithoutFail($id);

        if (empty($supplierSubCategoryAssign)) {
            return $this->sendError(trans('custom.supplier_sub_category_assign_not_found'));
        }

        return $this->sendResponse($supplierSubCategoryAssign->toArray(), trans('custom.supplier_sub_category_assign_retrieved_successfull'));
    }

    /**
     * Update the specified SupplierSubCategoryAssign in storage.
     * PUT/PATCH /supplierSubCategoryAssigns/{id}
     *
     * @param  int $id
     * @param UpdateSupplierSubCategoryAssignAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateSupplierSubCategoryAssignAPIRequest $request)
    {
        $input = $request->all();

        /** @var SupplierSubCategoryAssign $supplierSubCategoryAssign */
        $supplierSubCategoryAssign = $this->supplierSubCategoryAssignRepository->findWithoutFail($id);

        if (empty($supplierSubCategoryAssign)) {
            return $this->sendError(trans('custom.supplier_sub_category_assign_not_found'));
        }

        $supplierSubCategoryAssign = $this->supplierSubCategoryAssignRepository->update($input, $id);

        return $this->sendResponse($supplierSubCategoryAssign->toArray(), trans('custom.suppliersubcategoryassign_updated_successfully'));
    }

    /**
     * Remove the specified SupplierSubCategoryAssign from storage.
     * DELETE /supplierSubCategoryAssigns/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var SupplierSubCategoryAssign $supplierSubCategoryAssign */
        $supplierSubCategoryAssign = $this->supplierSubCategoryAssignRepository->findWithoutFail($id);

        if (empty($supplierSubCategoryAssign)) {
            return $this->sendError(trans('custom.supplier_sub_category_assign_not_found'));
        }

        $supplierSubCategoryAssign->delete();

        return $this->sendResponse($id, trans('custom.supplier_sub_category_assign_deleted_successfully'));
    }
}
