<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateDocumentModifyRequestDetailAPIRequest;
use App\Http\Requests\API\UpdateDocumentModifyRequestDetailAPIRequest;
use App\Models\DocumentModifyRequestDetail;
use App\Repositories\DocumentModifyRequestDetailRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class DocumentModifyRequestDetailController
 * @package App\Http\Controllers\API
 */

class DocumentModifyRequestDetailAPIController extends AppBaseController
{
    /** @var  DocumentModifyRequestDetailRepository */
    private $documentModifyRequestDetailRepository;

    public function __construct(DocumentModifyRequestDetailRepository $documentModifyRequestDetailRepo)
    {
        $this->documentModifyRequestDetailRepository = $documentModifyRequestDetailRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/documentModifyRequestDetails",
     *      summary="getDocumentModifyRequestDetailList",
     *      tags={"DocumentModifyRequestDetail"},
     *      description="Get all DocumentModifyRequestDetails",
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
     *                  @OA\Items(ref="#/definitions/DocumentModifyRequestDetail")
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
        $this->documentModifyRequestDetailRepository->pushCriteria(new RequestCriteria($request));
        $this->documentModifyRequestDetailRepository->pushCriteria(new LimitOffsetCriteria($request));
        $documentModifyRequestDetails = $this->documentModifyRequestDetailRepository->all();

        return $this->sendResponse($documentModifyRequestDetails->toArray(), trans('custom.document_modify_request_details_retrieved_successf'));
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/documentModifyRequestDetails",
     *      summary="createDocumentModifyRequestDetail",
     *      tags={"DocumentModifyRequestDetail"},
     *      description="Create DocumentModifyRequestDetail",
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
     *                  ref="#/definitions/DocumentModifyRequestDetail"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateDocumentModifyRequestDetailAPIRequest $request)
    {
        $input = $request->all();

        $documentModifyRequestDetail = $this->documentModifyRequestDetailRepository->create($input);

        return $this->sendResponse($documentModifyRequestDetail->toArray(), trans('custom.document_modify_request_detail_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/documentModifyRequestDetails/{id}",
     *      summary="getDocumentModifyRequestDetailItem",
     *      tags={"DocumentModifyRequestDetail"},
     *      description="Get DocumentModifyRequestDetail",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of DocumentModifyRequestDetail",
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
     *                  ref="#/definitions/DocumentModifyRequestDetail"
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
        /** @var DocumentModifyRequestDetail $documentModifyRequestDetail */
        $documentModifyRequestDetail = $this->documentModifyRequestDetailRepository->findWithoutFail($id);

        if (empty($documentModifyRequestDetail)) {
            return $this->sendError(trans('custom.document_modify_request_detail_not_found'));
        }

        return $this->sendResponse($documentModifyRequestDetail->toArray(), trans('custom.document_modify_request_detail_retrieved_successfu'));
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/documentModifyRequestDetails/{id}",
     *      summary="updateDocumentModifyRequestDetail",
     *      tags={"DocumentModifyRequestDetail"},
     *      description="Update DocumentModifyRequestDetail",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of DocumentModifyRequestDetail",
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
     *                  ref="#/definitions/DocumentModifyRequestDetail"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateDocumentModifyRequestDetailAPIRequest $request)
    {
        $input = $request->all();

        /** @var DocumentModifyRequestDetail $documentModifyRequestDetail */
        $documentModifyRequestDetail = $this->documentModifyRequestDetailRepository->findWithoutFail($id);

        if (empty($documentModifyRequestDetail)) {
            return $this->sendError(trans('custom.document_modify_request_detail_not_found'));
        }

        $documentModifyRequestDetail = $this->documentModifyRequestDetailRepository->update($input, $id);

        return $this->sendResponse($documentModifyRequestDetail->toArray(), trans('custom.documentmodifyrequestdetail_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/documentModifyRequestDetails/{id}",
     *      summary="deleteDocumentModifyRequestDetail",
     *      tags={"DocumentModifyRequestDetail"},
     *      description="Delete DocumentModifyRequestDetail",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of DocumentModifyRequestDetail",
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
        /** @var DocumentModifyRequestDetail $documentModifyRequestDetail */
        $documentModifyRequestDetail = $this->documentModifyRequestDetailRepository->findWithoutFail($id);

        if (empty($documentModifyRequestDetail)) {
            return $this->sendError(trans('custom.document_modify_request_detail_not_found'));
        }

        $documentModifyRequestDetail->delete();

        return $this->sendSuccess('Document Modify Request Detail deleted successfully');
    }
}
