<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateSupplierBusinessCategoryAssignAPIRequest;
use App\Http\Requests\API\UpdateSupplierBusinessCategoryAssignAPIRequest;
use App\Models\SupplierBusinessCategoryAssign;
use App\Repositories\SupplierBusinessCategoryAssignRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\DB;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class SupplierBusinessCategoryAssignController
 * @package App\Http\Controllers\API
 */

class SupplierBusinessCategoryAssignAPIController extends AppBaseController
{
    /** @var  SupplierBusinessCategoryAssignRepository */
    private $supplierBusinessCategoryAssignRepository;

    public function __construct(SupplierBusinessCategoryAssignRepository $supplierBusinessCategoryAssignRepo)
    {
        $this->supplierBusinessCategoryAssignRepository = $supplierBusinessCategoryAssignRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/supplierBusinessCategoryAssigns",
     *      summary="getSupplierBusinessCategoryAssignList",
     *      tags={"SupplierBusinessCategoryAssign"},
     *      description="Get all SupplierBusinessCategoryAssigns",
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  type="array",
     *                  @OA\Items(ref="#/definitions/SupplierBusinessCategoryAssign")
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function index(Request $request)
    {
        $this->supplierBusinessCategoryAssignRepository->pushCriteria(new RequestCriteria($request));
        $this->supplierBusinessCategoryAssignRepository->pushCriteria(new LimitOffsetCriteria($request));
        $supplierBusinessCategoryAssigns = $this->supplierBusinessCategoryAssignRepository->all();

        return $this->sendResponse($supplierBusinessCategoryAssigns->toArray(), 'Supplier Business Category Assigns retrieved successfully');
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/supplierBusinessCategoryAssigns",
     *      summary="createSupplierBusinessCategoryAssign",
     *      tags={"SupplierBusinessCategoryAssign"},
     *      description="Create SupplierBusinessCategoryAssign",
     *      @OA\RequestBody(
     *        required=true,
     *        @OA\MediaType(
     *            mediaType="application/x-www-form-urlencoded",
     *            @OA\Schema(
     *                type="object",
     *                required={""},
     *                @OA\Property(
     *                    property="name",
     *                    description="desc",
     *                    type="string"
     *                )
     *            )
     *        )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  ref="#/definitions/SupplierBusinessCategoryAssign"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateSupplierBusinessCategoryAssignAPIRequest $request)
    {
        $input = $request->all();

        $supplierBusinessCategoryAssign = $this->supplierBusinessCategoryAssignRepository->create($input);

        return $this->sendResponse($supplierBusinessCategoryAssign->toArray(), 'Supplier Business Category Assign saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/supplierBusinessCategoryAssigns/{id}",
     *      summary="getSupplierBusinessCategoryAssignItem",
     *      tags={"SupplierBusinessCategoryAssign"},
     *      description="Get SupplierBusinessCategoryAssign",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of SupplierBusinessCategoryAssign",
     *           @OA\Schema(
     *             type="integer"
     *          ),
     *          required=true,
     *          in="path"
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  ref="#/definitions/SupplierBusinessCategoryAssign"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function show($id)
    {
        /** @var SupplierBusinessCategoryAssign $supplierBusinessCategoryAssign */
        $supplierBusinessCategoryAssign = $this->supplierBusinessCategoryAssignRepository->findWithoutFail($id);

        if (empty($supplierBusinessCategoryAssign)) {
            return $this->sendError('Supplier Business Category Assign not found');
        }

        return $this->sendResponse($supplierBusinessCategoryAssign->toArray(), 'Supplier Business Category Assign retrieved successfully');
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/supplierBusinessCategoryAssigns/{id}",
     *      summary="updateSupplierBusinessCategoryAssign",
     *      tags={"SupplierBusinessCategoryAssign"},
     *      description="Update SupplierBusinessCategoryAssign",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of SupplierBusinessCategoryAssign",
     *           @OA\Schema(
     *             type="integer"
     *          ),
     *          required=true,
     *          in="path"
     *      ),
     *      @OA\RequestBody(
     *        required=true,
     *        @OA\MediaType(
     *            mediaType="application/x-www-form-urlencoded",
     *            @OA\Schema(
     *                type="object",
     *                required={""},
     *                @OA\Property(
     *                    property="name",
     *                    description="desc",
     *                    type="string"
     *                )
     *            )
     *        )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  ref="#/definitions/SupplierBusinessCategoryAssign"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateSupplierBusinessCategoryAssignAPIRequest $request)
    {
        $input = $request->all();

        /** @var SupplierBusinessCategoryAssign $supplierBusinessCategoryAssign */
        $supplierBusinessCategoryAssign = $this->supplierBusinessCategoryAssignRepository->findWithoutFail($id);

        if (empty($supplierBusinessCategoryAssign)) {
            return $this->sendError('Supplier Business Category Assign not found');
        }

        $supplierBusinessCategoryAssign = $this->supplierBusinessCategoryAssignRepository->update($input, $id);

        return $this->sendResponse($supplierBusinessCategoryAssign->toArray(), 'SupplierBusinessCategoryAssign updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/supplierBusinessCategoryAssigns/{id}",
     *      summary="deleteSupplierBusinessCategoryAssign",
     *      tags={"SupplierBusinessCategoryAssign"},
     *      description="Delete SupplierBusinessCategoryAssign",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of SupplierBusinessCategoryAssign",
     *           @OA\Schema(
     *             type="integer"
     *          ),
     *          required=true,
     *          in="path"
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  type="string"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function destroy($id)
    {
        $supplierBusinessCategoryAssign = $this->supplierBusinessCategoryAssignRepository->find($id);

        if (empty($supplierBusinessCategoryAssign)) {
            return $this->sendError('Supplier Business Category Assign not found');
        }

        $supplierBusinessSubCategories = DB::table('suppliercategorysub')
            ->where('supMasterCategoryID',$supplierBusinessCategoryAssign->supCategoryMasterID)
            ->pluck('supCategorySubID');

        DB::table('suppliersubcategoryassign')
            ->where('supplierID',$supplierBusinessCategoryAssign->supplierID)
            ->whereIn('supSubCategoryID', $supplierBusinessSubCategories)
            ->delete();

        $supplierBusinessCategoryAssign->delete();

        return $this->sendResponse([],'Supplier Business Category Assign deleted successfully');
    }
}
