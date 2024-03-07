<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateSupplierBlockAPIRequest;
use App\Http\Requests\API\UpdateSupplierBlockAPIRequest;
use App\Models\SupplierBlock;
use App\Repositories\SupplierBlockRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class SupplierBlockController
 * @package App\Http\Controllers\API
 */

class SupplierBlockAPIController extends AppBaseController
{
    /** @var  SupplierBlockRepository */
    private $supplierBlockRepository;

    public function __construct(SupplierBlockRepository $supplierBlockRepo)
    {
        $this->supplierBlockRepository = $supplierBlockRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/supplierBlocks",
     *      summary="getSupplierBlockList",
     *      tags={"SupplierBlock"},
     *      description="Get all SupplierBlocks",
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
     *                  @OA\Items(ref="#/definitions/SupplierBlock")
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
        $this->supplierBlockRepository->pushCriteria(new RequestCriteria($request));
        $this->supplierBlockRepository->pushCriteria(new LimitOffsetCriteria($request));
        $supplierBlocks = $this->supplierBlockRepository->all();

        return $this->sendResponse($supplierBlocks->toArray(), 'Supplier Blocks retrieved successfully');
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/supplierBlocks",
     *      summary="createSupplierBlock",
     *      tags={"SupplierBlock"},
     *      description="Create SupplierBlock",
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
     *                  ref="#/definitions/SupplierBlock"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateSupplierBlockAPIRequest $request)
    {
        $input = $request->all();

        $supplierBlock = $this->supplierBlockRepository->create($input);

        return $this->sendResponse($supplierBlock->toArray(), 'Supplier Block saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/supplierBlocks/{id}",
     *      summary="getSupplierBlockItem",
     *      tags={"SupplierBlock"},
     *      description="Get SupplierBlock",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of SupplierBlock",
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
     *                  ref="#/definitions/SupplierBlock"
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
        /** @var SupplierBlock $supplierBlock */
        $supplierBlock = $this->supplierBlockRepository->findWithoutFail($id);

        if (empty($supplierBlock)) {
            return $this->sendError('Supplier Block not found');
        }

        return $this->sendResponse($supplierBlock->toArray(), 'Supplier Block retrieved successfully');
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/supplierBlocks/{id}",
     *      summary="updateSupplierBlock",
     *      tags={"SupplierBlock"},
     *      description="Update SupplierBlock",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of SupplierBlock",
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
     *                  ref="#/definitions/SupplierBlock"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateSupplierBlockAPIRequest $request)
    {
        $input = $request->all();

        /** @var SupplierBlock $supplierBlock */
        $supplierBlock = $this->supplierBlockRepository->findWithoutFail($id);

        if (empty($supplierBlock)) {
            return $this->sendError('Supplier Block not found');
        }

        $supplierBlock = $this->supplierBlockRepository->update($input, $id);

        return $this->sendResponse($supplierBlock->toArray(), 'SupplierBlock updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/supplierBlocks/{id}",
     *      summary="deleteSupplierBlock",
     *      tags={"SupplierBlock"},
     *      description="Delete SupplierBlock",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of SupplierBlock",
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
        /** @var SupplierBlock $supplierBlock */
        $supplierBlock = $this->supplierBlockRepository->findWithoutFail($id);

        if (empty($supplierBlock)) {
            return $this->sendError('Supplier Block not found');
        }

        $supplierBlock->delete();

        return $this->sendSuccess('Supplier Block deleted successfully');
    }
}
