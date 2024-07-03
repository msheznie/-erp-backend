<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateSupplierEvaluationTemplateCommentAPIRequest;
use App\Http\Requests\API\UpdateSupplierEvaluationTemplateCommentAPIRequest;
use App\Models\SupplierEvaluationTemplateComment;
use App\Repositories\SupplierEvaluationTemplateCommentRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class SupplierEvaluationTemplateCommentController
 * @package App\Http\Controllers\API
 */

class SupplierEvaluationTemplateCommentAPIController extends AppBaseController
{
    /** @var  SupplierEvaluationTemplateCommentRepository */
    private $supplierEvaluationTemplateCommentRepository;

    public function __construct(SupplierEvaluationTemplateCommentRepository $supplierEvaluationTemplateCommentRepo)
    {
        $this->supplierEvaluationTemplateCommentRepository = $supplierEvaluationTemplateCommentRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/supplierEvaluationTemplateComments",
     *      summary="getSupplierEvaluationTemplateCommentList",
     *      tags={"SupplierEvaluationTemplateComment"},
     *      description="Get all SupplierEvaluationTemplateComments",
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
     *                  @OA\Items(ref="#/definitions/SupplierEvaluationTemplateComment")
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
        $this->supplierEvaluationTemplateCommentRepository->pushCriteria(new RequestCriteria($request));
        $this->supplierEvaluationTemplateCommentRepository->pushCriteria(new LimitOffsetCriteria($request));
        $supplierEvaluationTemplateComments = $this->supplierEvaluationTemplateCommentRepository->all();

        return $this->sendResponse($supplierEvaluationTemplateComments->toArray(), 'Supplier Evaluation Template Comments retrieved successfully');
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/supplierEvaluationTemplateComments",
     *      summary="createSupplierEvaluationTemplateComment",
     *      tags={"SupplierEvaluationTemplateComment"},
     *      description="Create SupplierEvaluationTemplateComment",
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
     *                  ref="#/definitions/SupplierEvaluationTemplateComment"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateSupplierEvaluationTemplateCommentAPIRequest $request)
    {
        $input = $request->all();

        $supplierEvaluationTemplateComment = $this->supplierEvaluationTemplateCommentRepository->create($input);

        return $this->sendResponse($supplierEvaluationTemplateComment->toArray(), 'Supplier Evaluation Template Comment saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/supplierEvaluationTemplateComments/{id}",
     *      summary="getSupplierEvaluationTemplateCommentItem",
     *      tags={"SupplierEvaluationTemplateComment"},
     *      description="Get SupplierEvaluationTemplateComment",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of SupplierEvaluationTemplateComment",
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
     *                  ref="#/definitions/SupplierEvaluationTemplateComment"
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
        /** @var SupplierEvaluationTemplateComment $supplierEvaluationTemplateComment */
        $supplierEvaluationTemplateComment = $this->supplierEvaluationTemplateCommentRepository->findWithoutFail($id);

        if (empty($supplierEvaluationTemplateComment)) {
            return $this->sendError('Supplier Evaluation Template Comment not found');
        }

        return $this->sendResponse($supplierEvaluationTemplateComment->toArray(), 'Supplier Evaluation Template Comment retrieved successfully');
    }

    public function getAllSupplierEvaluationTemplateComments(Request $request)
    {
        $input = $request->all();
        $supplierEvaluationTemplateId = $input['supplier_evaluation_template_id'];


        $supplierEvaluationTemplateComments = SupplierEvaluationTemplateComment::where('supplier_evaluation_template_id',$supplierEvaluationTemplateId)
                                                                                ->orderBy('created_at', 'desc')
                                                                                ->get();



        return $this->sendResponse($supplierEvaluationTemplateComments, 'Supplier Evaluation Template Comments retrieved successfully');

    }

    public function updateEvaluationTemplateComment(Request $request)
    {
        return$input = $request->all();
        $supplierEvaluationTemplateId = $input['supplier_evaluation_template_id'];


        $supplierEvaluationTemplateComments = SupplierEvaluationTemplateComment::where('supplier_evaluation_template_id',$supplierEvaluationTemplateId)->get();



        return $this->sendResponse($supplierEvaluationTemplateComments, 'Supplier Evaluation Template Comments retrieved successfully');

    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/supplierEvaluationTemplateComments/{id}",
     *      summary="updateSupplierEvaluationTemplateComment",
     *      tags={"SupplierEvaluationTemplateComment"},
     *      description="Update SupplierEvaluationTemplateComment",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of SupplierEvaluationTemplateComment",
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
     *                  ref="#/definitions/SupplierEvaluationTemplateComment"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateSupplierEvaluationTemplateCommentAPIRequest $request)
    {
        $input = $request->all();

        /** @var SupplierEvaluationTemplateComment $supplierEvaluationTemplateComment */
        $supplierEvaluationTemplateComment = $this->supplierEvaluationTemplateCommentRepository->findWithoutFail($id);

        if (empty($supplierEvaluationTemplateComment)) {
            return $this->sendError('Supplier Evaluation Template Comment not found');
        }

        $supplierEvaluationTemplateComment = $this->supplierEvaluationTemplateCommentRepository->update($input, $id);

        return $this->sendResponse($supplierEvaluationTemplateComment->toArray(), 'SupplierEvaluationTemplateComment updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/supplierEvaluationTemplateComments/{id}",
     *      summary="deleteSupplierEvaluationTemplateComment",
     *      tags={"SupplierEvaluationTemplateComment"},
     *      description="Delete SupplierEvaluationTemplateComment",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of SupplierEvaluationTemplateComment",
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
        /** @var SupplierEvaluationTemplateComment $supplierEvaluationTemplateComment */
        $supplierEvaluationTemplateComment = $this->supplierEvaluationTemplateCommentRepository->findWithoutFail($id);

        if (empty($supplierEvaluationTemplateComment)) {
            return $this->sendError('Supplier Evaluation Template Comment not found');
        }

        $supplierEvaluationTemplateComment->delete();

        return $this->sendSuccess('Supplier Evaluation Template Comment deleted successfully');
    }
}
