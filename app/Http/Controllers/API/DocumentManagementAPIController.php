<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateDocumentManagementAPIRequest;
use App\Http\Requests\API\UpdateDocumentManagementAPIRequest;
use App\Models\DocumentManagement;
use App\Repositories\DocumentManagementRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class DocumentManagementController
 * @package App\Http\Controllers\API
 */

class DocumentManagementAPIController extends AppBaseController
{
    /** @var  DocumentManagementRepository */
    private $documentManagementRepository;

    public function __construct(DocumentManagementRepository $documentManagementRepo)
    {
        $this->documentManagementRepository = $documentManagementRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/documentManagements",
     *      summary="Get a listing of the DocumentManagements.",
     *      tags={"DocumentManagement"},
     *      description="Get all DocumentManagements",
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
     *                  @SWG\Items(ref="#/definitions/DocumentManagement")
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
        $this->documentManagementRepository->pushCriteria(new RequestCriteria($request));
        $this->documentManagementRepository->pushCriteria(new LimitOffsetCriteria($request));
        $documentManagements = $this->documentManagementRepository->all();

        return $this->sendResponse($documentManagements->toArray(), trans('custom.document_managements_retrieved_successfully'));
    }

    /**
     * @param CreateDocumentManagementAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/documentManagements",
     *      summary="Store a newly created DocumentManagement in storage",
     *      tags={"DocumentManagement"},
     *      description="Store DocumentManagement",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="DocumentManagement that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/DocumentManagement")
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
     *                  ref="#/definitions/DocumentManagement"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateDocumentManagementAPIRequest $request)
    {
        $input = $request->all();

        $documentManagement = $this->documentManagementRepository->create($input);

        return $this->sendResponse($documentManagement->toArray(), trans('custom.document_management_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/documentManagements/{id}",
     *      summary="Display the specified DocumentManagement",
     *      tags={"DocumentManagement"},
     *      description="Get DocumentManagement",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of DocumentManagement",
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
     *                  ref="#/definitions/DocumentManagement"
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
        /** @var DocumentManagement $documentManagement */
        $documentManagement = $this->documentManagementRepository->findWithoutFail($id);

        if (empty($documentManagement)) {
            return $this->sendError(trans('custom.document_management_not_found'));
        }

        return $this->sendResponse($documentManagement->toArray(), trans('custom.document_management_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateDocumentManagementAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/documentManagements/{id}",
     *      summary="Update the specified DocumentManagement in storage",
     *      tags={"DocumentManagement"},
     *      description="Update DocumentManagement",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of DocumentManagement",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="DocumentManagement that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/DocumentManagement")
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
     *                  ref="#/definitions/DocumentManagement"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateDocumentManagementAPIRequest $request)
    {
        $input = $request->all();

        /** @var DocumentManagement $documentManagement */
        $documentManagement = $this->documentManagementRepository->findWithoutFail($id);

        if (empty($documentManagement)) {
            return $this->sendError(trans('custom.document_management_not_found'));
        }

        $documentManagement = $this->documentManagementRepository->update($input, $id);

        return $this->sendResponse($documentManagement->toArray(), trans('custom.documentmanagement_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/documentManagements/{id}",
     *      summary="Remove the specified DocumentManagement from storage",
     *      tags={"DocumentManagement"},
     *      description="Delete DocumentManagement",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of DocumentManagement",
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
        /** @var DocumentManagement $documentManagement */
        $documentManagement = $this->documentManagementRepository->findWithoutFail($id);

        if (empty($documentManagement)) {
            return $this->sendError(trans('custom.document_management_not_found'));
        }

        $documentManagement->delete();

        return $this->sendResponse($id, trans('custom.document_management_deleted_successfully'));
    }
}
