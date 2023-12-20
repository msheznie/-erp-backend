<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateRegisterSupplierSubcategoryAssignAPIRequest;
use App\Http\Requests\API\UpdateRegisterSupplierSubcategoryAssignAPIRequest;
use App\Models\RegisterSupplierSubcategoryAssign;
use App\Repositories\RegisterSupplierSubcategoryAssignRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class RegisterSupplierSubcategoryAssignController
 * @package App\Http\Controllers\API
 */

class RegisterSupplierSubcategoryAssignAPIController extends AppBaseController
{
    /** @var  RegisterSupplierSubcategoryAssignRepository */
    private $registerSupplierSubcategoryAssignRepository;

    public function __construct(RegisterSupplierSubcategoryAssignRepository $registerSupplierSubcategoryAssignRepo)
    {
        $this->registerSupplierSubcategoryAssignRepository = $registerSupplierSubcategoryAssignRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/registerSupplierSubcategoryAssigns",
     *      summary="getRegisterSupplierSubcategoryAssignList",
     *      tags={"RegisterSupplierSubcategoryAssign"},
     *      description="Get all RegisterSupplierSubcategoryAssigns",
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
     *                  @OA\Items(ref="#/definitions/RegisterSupplierSubcategoryAssign")
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
        $this->registerSupplierSubcategoryAssignRepository->pushCriteria(new RequestCriteria($request));
        $this->registerSupplierSubcategoryAssignRepository->pushCriteria(new LimitOffsetCriteria($request));
        $registerSupplierSubcategoryAssigns = $this->registerSupplierSubcategoryAssignRepository->all();

        return $this->sendResponse($registerSupplierSubcategoryAssigns->toArray(), 'Register Supplier Subcategory Assigns retrieved successfully');
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/registerSupplierSubcategoryAssigns",
     *      summary="createRegisterSupplierSubcategoryAssign",
     *      tags={"RegisterSupplierSubcategoryAssign"},
     *      description="Create RegisterSupplierSubcategoryAssign",
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
     *                  ref="#/definitions/RegisterSupplierSubcategoryAssign"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateRegisterSupplierSubcategoryAssignAPIRequest $request)
    {
        $input = $request->all();

        $registerSupplierSubcategoryAssign = $this->registerSupplierSubcategoryAssignRepository->create($input);

        return $this->sendResponse($registerSupplierSubcategoryAssign->toArray(), 'Register Supplier Subcategory Assign saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/registerSupplierSubcategoryAssigns/{id}",
     *      summary="getRegisterSupplierSubcategoryAssignItem",
     *      tags={"RegisterSupplierSubcategoryAssign"},
     *      description="Get RegisterSupplierSubcategoryAssign",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of RegisterSupplierSubcategoryAssign",
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
     *                  ref="#/definitions/RegisterSupplierSubcategoryAssign"
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
        /** @var RegisterSupplierSubcategoryAssign $registerSupplierSubcategoryAssign */
        $registerSupplierSubcategoryAssign = $this->registerSupplierSubcategoryAssignRepository->findWithoutFail($id);

        if (empty($registerSupplierSubcategoryAssign)) {
            return $this->sendError('Register Supplier Subcategory Assign not found');
        }

        return $this->sendResponse($registerSupplierSubcategoryAssign->toArray(), 'Register Supplier Subcategory Assign retrieved successfully');
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/registerSupplierSubcategoryAssigns/{id}",
     *      summary="updateRegisterSupplierSubcategoryAssign",
     *      tags={"RegisterSupplierSubcategoryAssign"},
     *      description="Update RegisterSupplierSubcategoryAssign",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of RegisterSupplierSubcategoryAssign",
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
     *                  ref="#/definitions/RegisterSupplierSubcategoryAssign"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateRegisterSupplierSubcategoryAssignAPIRequest $request)
    {
        $input = $request->all();

        /** @var RegisterSupplierSubcategoryAssign $registerSupplierSubcategoryAssign */
        $registerSupplierSubcategoryAssign = $this->registerSupplierSubcategoryAssignRepository->findWithoutFail($id);

        if (empty($registerSupplierSubcategoryAssign)) {
            return $this->sendError('Register Supplier Subcategory Assign not found');
        }

        $registerSupplierSubcategoryAssign = $this->registerSupplierSubcategoryAssignRepository->update($input, $id);

        return $this->sendResponse($registerSupplierSubcategoryAssign->toArray(), 'RegisterSupplierSubcategoryAssign updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/registerSupplierSubcategoryAssigns/{id}",
     *      summary="deleteRegisterSupplierSubcategoryAssign",
     *      tags={"RegisterSupplierSubcategoryAssign"},
     *      description="Delete RegisterSupplierSubcategoryAssign",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of RegisterSupplierSubcategoryAssign",
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
        /** @var RegisterSupplierSubcategoryAssign $registerSupplierSubcategoryAssign */
        $registerSupplierSubcategoryAssign = $this->registerSupplierSubcategoryAssignRepository->findWithoutFail($id);

        if (empty($registerSupplierSubcategoryAssign)) {
            return $this->sendError('Register Supplier Subcategory Assign not found');
        }

        $registerSupplierSubcategoryAssign->delete();

        return $this->sendSuccess('Register Supplier Subcategory Assign deleted successfully');
    }
}
