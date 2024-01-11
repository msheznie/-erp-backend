<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateRegisterSupplierBusinessCategoryAssignAPIRequest;
use App\Http\Requests\API\UpdateRegisterSupplierBusinessCategoryAssignAPIRequest;
use App\Models\RegisterSupplierBusinessCategoryAssign;
use App\Repositories\RegisterSupplierBusinessCategoryAssignRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class RegisterSupplierBusinessCategoryAssignController
 * @package App\Http\Controllers\API
 */

class RegisterSupplierBusinessCategoryAssignAPIController extends AppBaseController
{
    /** @var  RegisterSupplierBusinessCategoryAssignRepository */
    private $registerSupplierBusinessCategoryAssignRepository;

    public function __construct(RegisterSupplierBusinessCategoryAssignRepository $registerSupplierBusinessCategoryAssignRepo)
    {
        $this->registerSupplierBusinessCategoryAssignRepository = $registerSupplierBusinessCategoryAssignRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/registerSupplierBusinessCategoryAssigns",
     *      summary="getRegisterSupplierBusinessCategoryAssignList",
     *      tags={"RegisterSupplierBusinessCategoryAssign"},
     *      description="Get all RegisterSupplierBusinessCategoryAssigns",
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
     *                  @OA\Items(ref="#/definitions/RegisterSupplierBusinessCategoryAssign")
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
        $this->registerSupplierBusinessCategoryAssignRepository->pushCriteria(new RequestCriteria($request));
        $this->registerSupplierBusinessCategoryAssignRepository->pushCriteria(new LimitOffsetCriteria($request));
        $registerSupplierBusinessCategoryAssigns = $this->registerSupplierBusinessCategoryAssignRepository->all();

        return $this->sendResponse($registerSupplierBusinessCategoryAssigns->toArray(), 'Register Supplier Business Category Assigns retrieved successfully');
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/registerSupplierBusinessCategoryAssigns",
     *      summary="createRegisterSupplierBusinessCategoryAssign",
     *      tags={"RegisterSupplierBusinessCategoryAssign"},
     *      description="Create RegisterSupplierBusinessCategoryAssign",
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
     *                  ref="#/definitions/RegisterSupplierBusinessCategoryAssign"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateRegisterSupplierBusinessCategoryAssignAPIRequest $request)
    {
        $input = $request->all();

        $registerSupplierBusinessCategoryAssign = $this->registerSupplierBusinessCategoryAssignRepository->create($input);

        return $this->sendResponse($registerSupplierBusinessCategoryAssign->toArray(), 'Register Supplier Business Category Assign saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/registerSupplierBusinessCategoryAssigns/{id}",
     *      summary="getRegisterSupplierBusinessCategoryAssignItem",
     *      tags={"RegisterSupplierBusinessCategoryAssign"},
     *      description="Get RegisterSupplierBusinessCategoryAssign",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of RegisterSupplierBusinessCategoryAssign",
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
     *                  ref="#/definitions/RegisterSupplierBusinessCategoryAssign"
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
        /** @var RegisterSupplierBusinessCategoryAssign $registerSupplierBusinessCategoryAssign */
        $registerSupplierBusinessCategoryAssign = $this->registerSupplierBusinessCategoryAssignRepository->findWithoutFail($id);

        if (empty($registerSupplierBusinessCategoryAssign)) {
            return $this->sendError('Register Supplier Business Category Assign not found');
        }

        return $this->sendResponse($registerSupplierBusinessCategoryAssign->toArray(), 'Register Supplier Business Category Assign retrieved successfully');
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/registerSupplierBusinessCategoryAssigns/{id}",
     *      summary="updateRegisterSupplierBusinessCategoryAssign",
     *      tags={"RegisterSupplierBusinessCategoryAssign"},
     *      description="Update RegisterSupplierBusinessCategoryAssign",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of RegisterSupplierBusinessCategoryAssign",
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
     *                  ref="#/definitions/RegisterSupplierBusinessCategoryAssign"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateRegisterSupplierBusinessCategoryAssignAPIRequest $request)
    {
        $input = $request->all();

        /** @var RegisterSupplierBusinessCategoryAssign $registerSupplierBusinessCategoryAssign */
        $registerSupplierBusinessCategoryAssign = $this->registerSupplierBusinessCategoryAssignRepository->findWithoutFail($id);

        if (empty($registerSupplierBusinessCategoryAssign)) {
            return $this->sendError('Register Supplier Business Category Assign not found');
        }

        $registerSupplierBusinessCategoryAssign = $this->registerSupplierBusinessCategoryAssignRepository->update($input, $id);

        return $this->sendResponse($registerSupplierBusinessCategoryAssign->toArray(), 'RegisterSupplierBusinessCategoryAssign updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/registerSupplierBusinessCategoryAssigns/{id}",
     *      summary="deleteRegisterSupplierBusinessCategoryAssign",
     *      tags={"RegisterSupplierBusinessCategoryAssign"},
     *      description="Delete RegisterSupplierBusinessCategoryAssign",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of RegisterSupplierBusinessCategoryAssign",
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
        /** @var RegisterSupplierBusinessCategoryAssign $registerSupplierBusinessCategoryAssign */
        $registerSupplierBusinessCategoryAssign = $this->registerSupplierBusinessCategoryAssignRepository->findWithoutFail($id);

        if (empty($registerSupplierBusinessCategoryAssign)) {
            return $this->sendError('Register Supplier Business Category Assign not found');
        }

        $registerSupplierBusinessCategoryAssign->delete();

        return $this->sendSuccess('Register Supplier Business Category Assign deleted successfully');
    }
}
