<?php
/**
 * =============================================
 * -- File Name : PurchaseOrderCategoryAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  PurchaseOrderCategory
 * -- Author : Mohamed Fayas
 * -- Create date : 30- May 2018
 * -- Description : This file contains the all CRUD for PurchaseOrderCategory
 * -- REVISION HISTORY
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreatePurchaseOrderCategoryAPIRequest;
use App\Http\Requests\API\UpdatePurchaseOrderCategoryAPIRequest;
use App\Models\PurchaseOrderCategory;
use App\Repositories\PurchaseOrderCategoryRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class PurchaseOrderCategoryController
 * @package App\Http\Controllers\API
 */

class PurchaseOrderCategoryAPIController extends AppBaseController
{
    /** @var  PurchaseOrderCategoryRepository */
    private $purchaseOrderCategoryRepository;

    public function __construct(PurchaseOrderCategoryRepository $purchaseOrderCategoryRepo)
    {
        $this->purchaseOrderCategoryRepository = $purchaseOrderCategoryRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/purchaseOrderCategories",
     *      summary="Get a listing of the PurchaseOrderCategories.",
     *      tags={"PurchaseOrderCategory"},
     *      description="Get all PurchaseOrderCategories",
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
     *                  @SWG\Items(ref="#/definitions/PurchaseOrderCategory")
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
        $this->purchaseOrderCategoryRepository->pushCriteria(new RequestCriteria($request));
        $this->purchaseOrderCategoryRepository->pushCriteria(new LimitOffsetCriteria($request));
        $purchaseOrderCategories = $this->purchaseOrderCategoryRepository->all();

        return $this->sendResponse($purchaseOrderCategories->toArray(), trans('custom.purchase_order_categories_retrieved_successfully'));
    }

    /**
     * @param CreatePurchaseOrderCategoryAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/purchaseOrderCategories",
     *      summary="Store a newly created PurchaseOrderCategory in storage",
     *      tags={"PurchaseOrderCategory"},
     *      description="Store PurchaseOrderCategory",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="PurchaseOrderCategory that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/PurchaseOrderCategory")
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
     *                  ref="#/definitions/PurchaseOrderCategory"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreatePurchaseOrderCategoryAPIRequest $request)
    {
        $input = $request->all();

        $purchaseOrderCategories = $this->purchaseOrderCategoryRepository->create($input);

        return $this->sendResponse($purchaseOrderCategories->toArray(), trans('custom.purchase_order_category_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/purchaseOrderCategories/{id}",
     *      summary="Display the specified PurchaseOrderCategory",
     *      tags={"PurchaseOrderCategory"},
     *      description="Get PurchaseOrderCategory",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PurchaseOrderCategory",
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
     *                  ref="#/definitions/PurchaseOrderCategory"
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
        /** @var PurchaseOrderCategory $purchaseOrderCategory */
        $purchaseOrderCategory = $this->purchaseOrderCategoryRepository->findWithoutFail($id);

        if (empty($purchaseOrderCategory)) {
            return $this->sendError(trans('custom.purchase_order_category_not_found'));
        }

        return $this->sendResponse($purchaseOrderCategory->toArray(), trans('custom.purchase_order_category_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdatePurchaseOrderCategoryAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/purchaseOrderCategories/{id}",
     *      summary="Update the specified PurchaseOrderCategory in storage",
     *      tags={"PurchaseOrderCategory"},
     *      description="Update PurchaseOrderCategory",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PurchaseOrderCategory",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="PurchaseOrderCategory that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/PurchaseOrderCategory")
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
     *                  ref="#/definitions/PurchaseOrderCategory"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdatePurchaseOrderCategoryAPIRequest $request)
    {
        $input = $request->all();

        /** @var PurchaseOrderCategory $purchaseOrderCategory */
        $purchaseOrderCategory = $this->purchaseOrderCategoryRepository->findWithoutFail($id);

        if (empty($purchaseOrderCategory)) {
            return $this->sendError(trans('custom.purchase_order_category_not_found'));
        }

        $purchaseOrderCategory = $this->purchaseOrderCategoryRepository->update($input, $id);

        return $this->sendResponse($purchaseOrderCategory->toArray(), trans('custom.purchaseordercategory_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/purchaseOrderCategories/{id}",
     *      summary="Remove the specified PurchaseOrderCategory from storage",
     *      tags={"PurchaseOrderCategory"},
     *      description="Delete PurchaseOrderCategory",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PurchaseOrderCategory",
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
        /** @var PurchaseOrderCategory $purchaseOrderCategory */
        $purchaseOrderCategory = $this->purchaseOrderCategoryRepository->findWithoutFail($id);

        if (empty($purchaseOrderCategory)) {
            return $this->sendError(trans('custom.purchase_order_category_not_found'));
        }

        $purchaseOrderCategory->delete();

        return $this->sendResponse($id, trans('custom.purchase_order_category_deleted_successfully'));
    }
}
