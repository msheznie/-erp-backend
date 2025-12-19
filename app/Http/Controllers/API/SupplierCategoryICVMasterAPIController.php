<?php
/**
=============================================
-- File Name : SupplierCategoryICVMasterAPIController.php
-- Project Name : ERP
-- Module Name : Supplier Category ICV Master
-- Author : Mohamed Fayas
-- Create date : 03-December 2018
-- Description : This file contains the all CRUD for Supplier Category ICV Sub
-- REVISION HISTORY
 * -- Date: 03-December 2018 By: Fayas Description: Added new function subICVCategoriesByMasterCategory(),
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateSupplierCategoryICVMasterAPIRequest;
use App\Http\Requests\API\UpdateSupplierCategoryICVMasterAPIRequest;
use App\Models\SupplierCategoryICVMaster;
use App\Models\SupplierCategoryICVSub;
use App\Repositories\SupplierCategoryICVMasterRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class SupplierCategoryICVMasterController
 * @package App\Http\Controllers\API
 */

class SupplierCategoryICVMasterAPIController extends AppBaseController
{
    /** @var  SupplierCategoryICVMasterRepository */
    private $supplierCategoryICVMasterRepository;

    public function __construct(SupplierCategoryICVMasterRepository $supplierCategoryICVMasterRepo)
    {
        $this->supplierCategoryICVMasterRepository = $supplierCategoryICVMasterRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/supplierCategoryICVMasters",
     *      summary="Get a listing of the SupplierCategoryICVMasters.",
     *      tags={"SupplierCategoryICVMaster"},
     *      description="Get all SupplierCategoryICVMasters",
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
     *                  @SWG\Items(ref="#/definitions/SupplierCategoryICVMaster")
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
        $this->supplierCategoryICVMasterRepository->pushCriteria(new RequestCriteria($request));
        $this->supplierCategoryICVMasterRepository->pushCriteria(new LimitOffsetCriteria($request));
        $supplierCategoryICVMasters = $this->supplierCategoryICVMasterRepository->all();

        return $this->sendResponse($supplierCategoryICVMasters->toArray(), trans('custom.supplier_category_i_c_v_masters_retrieved_successf'));
    }

    /**
     * @param CreateSupplierCategoryICVMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/supplierCategoryICVMasters",
     *      summary="Store a newly created SupplierCategoryICVMaster in storage",
     *      tags={"SupplierCategoryICVMaster"},
     *      description="Store SupplierCategoryICVMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="SupplierCategoryICVMaster that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/SupplierCategoryICVMaster")
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
     *                  ref="#/definitions/SupplierCategoryICVMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateSupplierCategoryICVMasterAPIRequest $request)
    {
        $input = $request->all();

        $supplierCategoryICVMasters = $this->supplierCategoryICVMasterRepository->create($input);

        return $this->sendResponse($supplierCategoryICVMasters->toArray(), trans('custom.supplier_category_i_c_v_master_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/supplierCategoryICVMasters/{id}",
     *      summary="Display the specified SupplierCategoryICVMaster",
     *      tags={"SupplierCategoryICVMaster"},
     *      description="Get SupplierCategoryICVMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SupplierCategoryICVMaster",
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
     *                  ref="#/definitions/SupplierCategoryICVMaster"
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
        /** @var SupplierCategoryICVMaster $supplierCategoryICVMaster */
        $supplierCategoryICVMaster = $this->supplierCategoryICVMasterRepository->findWithoutFail($id);

        if (empty($supplierCategoryICVMaster)) {
            return $this->sendError(trans('custom.supplier_category_i_c_v_master_not_found'));
        }

        return $this->sendResponse($supplierCategoryICVMaster->toArray(), trans('custom.supplier_category_i_c_v_master_retrieved_successfu'));
    }

    /**
     * @param int $id
     * @param UpdateSupplierCategoryICVMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/supplierCategoryICVMasters/{id}",
     *      summary="Update the specified SupplierCategoryICVMaster in storage",
     *      tags={"SupplierCategoryICVMaster"},
     *      description="Update SupplierCategoryICVMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SupplierCategoryICVMaster",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="SupplierCategoryICVMaster that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/SupplierCategoryICVMaster")
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
     *                  ref="#/definitions/SupplierCategoryICVMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateSupplierCategoryICVMasterAPIRequest $request)
    {
        $input = $request->all();

        /** @var SupplierCategoryICVMaster $supplierCategoryICVMaster */
        $supplierCategoryICVMaster = $this->supplierCategoryICVMasterRepository->findWithoutFail($id);

        if (empty($supplierCategoryICVMaster)) {
            return $this->sendError(trans('custom.supplier_category_i_c_v_master_not_found'));
        }

        $supplierCategoryICVMaster = $this->supplierCategoryICVMasterRepository->update($input, $id);

        return $this->sendResponse($supplierCategoryICVMaster->toArray(), trans('custom.suppliercategoryicvmaster_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/supplierCategoryICVMasters/{id}",
     *      summary="Remove the specified SupplierCategoryICVMaster from storage",
     *      tags={"SupplierCategoryICVMaster"},
     *      description="Delete SupplierCategoryICVMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SupplierCategoryICVMaster",
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
        /** @var SupplierCategoryICVMaster $supplierCategoryICVMaster */
        $supplierCategoryICVMaster = $this->supplierCategoryICVMasterRepository->findWithoutFail($id);

        if (empty($supplierCategoryICVMaster)) {
            return $this->sendError(trans('custom.supplier_category_i_c_v_master_not_found'));
        }

        $supplierCategoryICVMaster->delete();

        return $this->sendResponse($id, trans('custom.supplier_category_i_c_v_master_deleted_successfull'));
    }

    public function subICVCategoriesByMasterCategory(Request $request)
    {
        $id = $request->get('supCategoryICVMasterID');
        $subCategories = SupplierCategoryICVSub::where('supCategoryICVMasterID',$id)->get();
        return $this->sendResponse($subCategories->toArray(), trans('custom.icv_sub_categories_retrieved_successfully'));
    }


}
