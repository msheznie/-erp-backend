<?php
/**
=============================================
-- File Name : SupplierCategoryICVSubAPIController.php
-- Project Name : ERP
-- Module Name :  Supplier Category ICV Sub
-- Author : Mohamed Fayas
-- Create date : 03-December 2018
-- Description : This file contains the all CRUD for Supplier Category ICV Sub
-- REVISION HISTORY
 * -- Date: 03-December 2018 By: Fayas Description: Added new function subICVCategoriesByMasterCategory(),
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateSupplierCategoryICVSubAPIRequest;
use App\Http\Requests\API\UpdateSupplierCategoryICVSubAPIRequest;
use App\Models\SupplierCategoryICVSub;
use App\Repositories\SupplierCategoryICVSubRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class SupplierCategoryICVSubController
 * @package App\Http\Controllers\API
 */

class SupplierCategoryICVSubAPIController extends AppBaseController
{
    /** @var  SupplierCategoryICVSubRepository */
    private $supplierCategoryICVSubRepository;

    public function __construct(SupplierCategoryICVSubRepository $supplierCategoryICVSubRepo)
    {
        $this->supplierCategoryICVSubRepository = $supplierCategoryICVSubRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/supplierCategoryICVSubs",
     *      summary="Get a listing of the SupplierCategoryICVSubs.",
     *      tags={"SupplierCategoryICVSub"},
     *      description="Get all SupplierCategoryICVSubs",
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
     *                  @SWG\Items(ref="#/definitions/SupplierCategoryICVSub")
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
        $this->supplierCategoryICVSubRepository->pushCriteria(new RequestCriteria($request));
        $this->supplierCategoryICVSubRepository->pushCriteria(new LimitOffsetCriteria($request));
        $supplierCategoryICVSubs = $this->supplierCategoryICVSubRepository->all();

        return $this->sendResponse($supplierCategoryICVSubs->toArray(), trans('custom.supplier_category_i_c_v_subs_retrieved_successfull'));
    }

    /**
     * @param CreateSupplierCategoryICVSubAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/supplierCategoryICVSubs",
     *      summary="Store a newly created SupplierCategoryICVSub in storage",
     *      tags={"SupplierCategoryICVSub"},
     *      description="Store SupplierCategoryICVSub",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="SupplierCategoryICVSub that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/SupplierCategoryICVSub")
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
     *                  ref="#/definitions/SupplierCategoryICVSub"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateSupplierCategoryICVSubAPIRequest $request)
    {
        $input = $request->all();

        $supplierCategoryICVSubs = $this->supplierCategoryICVSubRepository->create($input);

        return $this->sendResponse($supplierCategoryICVSubs->toArray(), trans('custom.supplier_category_i_c_v_sub_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/supplierCategoryICVSubs/{id}",
     *      summary="Display the specified SupplierCategoryICVSub",
     *      tags={"SupplierCategoryICVSub"},
     *      description="Get SupplierCategoryICVSub",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SupplierCategoryICVSub",
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
     *                  ref="#/definitions/SupplierCategoryICVSub"
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
        /** @var SupplierCategoryICVSub $supplierCategoryICVSub */
        $supplierCategoryICVSub = $this->supplierCategoryICVSubRepository->findWithoutFail($id);

        if (empty($supplierCategoryICVSub)) {
            return $this->sendError(trans('custom.supplier_category_i_c_v_sub_not_found'));
        }

        return $this->sendResponse($supplierCategoryICVSub->toArray(), trans('custom.supplier_category_i_c_v_sub_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateSupplierCategoryICVSubAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/supplierCategoryICVSubs/{id}",
     *      summary="Update the specified SupplierCategoryICVSub in storage",
     *      tags={"SupplierCategoryICVSub"},
     *      description="Update SupplierCategoryICVSub",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SupplierCategoryICVSub",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="SupplierCategoryICVSub that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/SupplierCategoryICVSub")
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
     *                  ref="#/definitions/SupplierCategoryICVSub"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateSupplierCategoryICVSubAPIRequest $request)
    {
        $input = $request->all();

        /** @var SupplierCategoryICVSub $supplierCategoryICVSub */
        $supplierCategoryICVSub = $this->supplierCategoryICVSubRepository->findWithoutFail($id);

        if (empty($supplierCategoryICVSub)) {
            return $this->sendError(trans('custom.supplier_category_i_c_v_sub_not_found'));
        }

        $supplierCategoryICVSub = $this->supplierCategoryICVSubRepository->update($input, $id);

        return $this->sendResponse($supplierCategoryICVSub->toArray(), trans('custom.suppliercategoryicvsub_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/supplierCategoryICVSubs/{id}",
     *      summary="Remove the specified SupplierCategoryICVSub from storage",
     *      tags={"SupplierCategoryICVSub"},
     *      description="Delete SupplierCategoryICVSub",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SupplierCategoryICVSub",
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
        /** @var SupplierCategoryICVSub $supplierCategoryICVSub */
        $supplierCategoryICVSub = $this->supplierCategoryICVSubRepository->findWithoutFail($id);

        if (empty($supplierCategoryICVSub)) {
            return $this->sendError(trans('custom.supplier_category_i_c_v_sub_not_found'));
        }

        $supplierCategoryICVSub->delete();

        return $this->sendResponse($id, trans('custom.supplier_category_i_c_v_sub_deleted_successfully'));
    }
}
