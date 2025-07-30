<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateDocumentModifyRequestAPIRequest;
use App\Http\Requests\API\UpdateDocumentModifyRequestAPIRequest;
use App\Models\DocumentModifyRequest;
use App\Repositories\DocumentModifyRequestRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\DocumentMaster;
use App\Models\Company;
use App\Models\TenderMaster;
use Carbon\Carbon;
use App\Services\SrmDocumentModifyService;
/**
 * Class DocumentModifyRequestController
 * @package App\Http\Controllers\API
 */

class DocumentModifyRequestAPIController extends AppBaseController
{
    /** @var  DocumentModifyRequestRepository */
    private $documentModifyRequestRepository;
    private $documentModifyRequestService;

    public function __construct(
        DocumentModifyRequestRepository $documentModifyRequestRepo,
        SrmDocumentModifyService $documentModifyRequestService
    ){
        $this->documentModifyRequestRepository = $documentModifyRequestRepo;
        $this->documentModifyRequestService = $documentModifyRequestService;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/documentModifyRequests",
     *      summary="getDocumentModifyRequestList",
     *      tags={"DocumentModifyRequest"},
     *      description="Get all DocumentModifyRequests",
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
     *                  @OA\Items(ref="#/definitions/DocumentModifyRequest")
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
        $this->documentModifyRequestRepository->pushCriteria(new RequestCriteria($request));
        $this->documentModifyRequestRepository->pushCriteria(new LimitOffsetCriteria($request));
        $documentModifyRequests = $this->documentModifyRequestRepository->all();

        return $this->sendResponse($documentModifyRequests->toArray(), 'Document Modify Requests retrieved successfully');
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/documentModifyRequests",
     *      summary="createDocumentModifyRequest",
     *      tags={"DocumentModifyRequest"},
     *      description="Create DocumentModifyRequest",
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
     *                  ref="#/definitions/DocumentModifyRequest"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateDocumentModifyRequestAPIRequest $request)
    {
        $input = $request->all();

        $documentModifyRequest = $this->documentModifyRequestRepository->create($input);

        return $this->sendResponse($documentModifyRequest->toArray(), 'Document Modify Request saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/documentModifyRequests/{id}",
     *      summary="getDocumentModifyRequestItem",
     *      tags={"DocumentModifyRequest"},
     *      description="Get DocumentModifyRequest",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of DocumentModifyRequest",
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
     *                  ref="#/definitions/DocumentModifyRequest"
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
        /** @var DocumentModifyRequest $documentModifyRequest */
        $documentModifyRequest = $this->documentModifyRequestRepository->findWithoutFail($id);

        if (empty($documentModifyRequest)) {
            return $this->sendError('Document Modify Request not found');
        }

        return $this->sendResponse($documentModifyRequest->toArray(), 'Document Modify Request retrieved successfully');
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/documentModifyRequests/{id}",
     *      summary="updateDocumentModifyRequest",
     *      tags={"DocumentModifyRequest"},
     *      description="Update DocumentModifyRequest",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of DocumentModifyRequest",
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
     *                  ref="#/definitions/DocumentModifyRequest"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateDocumentModifyRequestAPIRequest $request)
    {
        $input = $request->all();

        /** @var DocumentModifyRequest $documentModifyRequest */
        $documentModifyRequest = $this->documentModifyRequestRepository->findWithoutFail($id);

        if (empty($documentModifyRequest)) {
            return $this->sendError('Document Modify Request not found');
        }

        $documentModifyRequest = $this->documentModifyRequestRepository->update($input, $id);

        return $this->sendResponse($documentModifyRequest->toArray(), 'DocumentModifyRequest updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/documentModifyRequests/{id}",
     *      summary="deleteDocumentModifyRequest",
     *      tags={"DocumentModifyRequest"},
     *      description="Delete DocumentModifyRequest",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of DocumentModifyRequest",
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
        /** @var DocumentModifyRequest $documentModifyRequest */
        $documentModifyRequest = $this->documentModifyRequestRepository->findWithoutFail($id);

        if (empty($documentModifyRequest)) {
            return $this->sendError('Document Modify Request not found');
        }

        $documentModifyRequest->delete();

        return $this->sendSuccess('Document Modify Request deleted successfully');
    }

    public function createEditRequest(Request $request)
    {
        try {
            return $this->documentModifyRequestRepository->createEditAmendRequest($request);
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e];
        }
    }


    public function failed($exception)
    {
        return $exception->getMessage();
    }

    public function approveEditDocument(Request $request)
    {
        try {
            $response =  $this->documentModifyRequestRepository->approveDocumentEditAmendRequest($request);
            if($response['success']){
                return $this->sendResponse([], $response['message']);
            } else {
                return $this->sendError($response['message'], 500);
            }
        } catch (\Exception $e) {
            return $this->sendError('Unexpected Error: ' . $e->getMessage(), 500);
        }
    }
    public function getEditOrAmendHistory(Request $request)
    {
        try{
            $response = $this->documentModifyRequestRepository->getEditOrAmendHistory($request);
            if(!$response['success']) {
                return $this->sendError($response['message']);
            }
            return $this->sendResponse($response['data'], 'Data retrieved successfully');
        } catch (\Exception $exception) {
            return $this->sendError('Unexpected Error: ' . $exception->getMessage());
        }
    }
}
