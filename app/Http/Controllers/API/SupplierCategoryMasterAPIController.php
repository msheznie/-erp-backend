<?php
/**
=============================================
-- File Name : SupplierCategoryMasterAPIController.php
-- Project Name : ERP
-- Module Name :  Supplier Category Master
-- Author : Mohamed Fayas
-- Create date : 14 - March 2018
-- Description : This file contains the all CRUD for Supplier Assigned
-- REVISION HISTORY
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateSupplierCategoryMasterAPIRequest;
use App\Http\Requests\API\UpdateSupplierCategoryMasterAPIRequest;
use App\Models\SupplierCategoryMaster;
use App\Repositories\SupplierCategoryMasterRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class SupplierCategoryMasterController
 * @package App\Http\Controllers\API
 */

class SupplierCategoryMasterAPIController extends AppBaseController
{
    /** @var  SupplierCategoryMasterRepository */
    private $supplierCategoryMasterRepository;

    public function __construct(SupplierCategoryMasterRepository $supplierCategoryMasterRepo)
    {
        $this->supplierCategoryMasterRepository = $supplierCategoryMasterRepo;
    }

    /**
     * Display a listing of the SupplierCategoryMaster.
     * GET|HEAD /supplierCategoryMasters
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->supplierCategoryMasterRepository->pushCriteria(new RequestCriteria($request));
        $this->supplierCategoryMasterRepository->pushCriteria(new LimitOffsetCriteria($request));
        $supplierCategoryMasters = $this->supplierCategoryMasterRepository->all();

        return $this->sendResponse($supplierCategoryMasters->toArray(), 'Supplier Category Masters retrieved successfully');
    }

    /**
     * Store a newly created SupplierCategoryMaster in storage.
     * POST /supplierCategoryMasters
     *
     * @param CreateSupplierCategoryMasterAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateSupplierCategoryMasterAPIRequest $request)
    {
        $input = $request->all();

        $supplierCategoryMasters = $this->supplierCategoryMasterRepository->create($input);

        return $this->sendResponse($supplierCategoryMasters->toArray(), 'Supplier Category Master saved successfully');
    }

    /**
     * Display the specified SupplierCategoryMaster.
     * GET|HEAD /supplierCategoryMasters/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var SupplierCategoryMaster $supplierCategoryMaster */
        $supplierCategoryMaster = $this->supplierCategoryMasterRepository->findWithoutFail($id);

        if (empty($supplierCategoryMaster)) {
            return $this->sendError('Supplier Category Master not found');
        }

        return $this->sendResponse($supplierCategoryMaster->toArray(), 'Supplier Category Master retrieved successfully');
    }

    /**
     * Update the specified SupplierCategoryMaster in storage.
     * PUT/PATCH /supplierCategoryMasters/{id}
     *
     * @param  int $id
     * @param UpdateSupplierCategoryMasterAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateSupplierCategoryMasterAPIRequest $request)
    {
        $input = $request->all();

        /** @var SupplierCategoryMaster $supplierCategoryMaster */
        $supplierCategoryMaster = $this->supplierCategoryMasterRepository->findWithoutFail($id);

        if (empty($supplierCategoryMaster)) {
            return $this->sendError('Supplier Category Master not found');
        }

        $supplierCategoryMaster = $this->supplierCategoryMasterRepository->update($input, $id);

        return $this->sendResponse($supplierCategoryMaster->toArray(), 'SupplierCategoryMaster updated successfully');
    }

    /**
     * Remove the specified SupplierCategoryMaster from storage.
     * DELETE /supplierCategoryMasters/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var SupplierCategoryMaster $supplierCategoryMaster */
        $supplierCategoryMaster = $this->supplierCategoryMasterRepository->findWithoutFail($id);

        if (empty($supplierCategoryMaster)) {
            return $this->sendError('Supplier Category Master not found');
        }

        $supplierCategoryMaster->delete();

        return $this->sendResponse($id, 'Supplier Category Master deleted successfully');
    }
}
