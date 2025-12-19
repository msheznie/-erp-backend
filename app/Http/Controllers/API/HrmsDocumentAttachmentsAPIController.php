<?php

namespace App\Http\Controllers\API;

use App\helper\Helper;
use App\Http\Requests\API\CreateHrmsDocumentAttachmentsAPIRequest;
use App\Http\Requests\API\UpdateHrmsDocumentAttachmentsAPIRequest;
use App\Models\HrmsDocumentAttachments;
use App\Repositories\HrmsDocumentAttachmentsRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\Storage;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class HrmsDocumentAttachmentsController
 * @package App\Http\Controllers\API
 */

class HrmsDocumentAttachmentsAPIController extends AppBaseController
{
    /** @var  HrmsDocumentAttachmentsRepository */
    private $hrmsDocumentAttachmentsRepository;

    public function __construct(HrmsDocumentAttachmentsRepository $hrmsDocumentAttachmentsRepo)
    {
        $this->hrmsDocumentAttachmentsRepository = $hrmsDocumentAttachmentsRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/hrmsDocumentAttachments",
     *      summary="Get a listing of the HrmsDocumentAttachments.",
     *      tags={"HrmsDocumentAttachments"},
     *      description="Get all HrmsDocumentAttachments",
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
     *                  @SWG\Items(ref="#/definitions/HrmsDocumentAttachments")
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
        $this->hrmsDocumentAttachmentsRepository->pushCriteria(new RequestCriteria($request));
        $this->hrmsDocumentAttachmentsRepository->pushCriteria(new LimitOffsetCriteria($request));
        $hrmsDocumentAttachments = $this->hrmsDocumentAttachmentsRepository->all();

        return $this->sendResponse($hrmsDocumentAttachments->toArray(), trans('custom.hrms_document_attachments_retrieved_successfully'));
    }

    /**
     * @param CreateHrmsDocumentAttachmentsAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/hrmsDocumentAttachments",
     *      summary="Store a newly created HrmsDocumentAttachments in storage",
     *      tags={"HrmsDocumentAttachments"},
     *      description="Store HrmsDocumentAttachments",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="HrmsDocumentAttachments that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/HrmsDocumentAttachments")
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
     *                  ref="#/definitions/HrmsDocumentAttachments"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateHrmsDocumentAttachmentsAPIRequest $request)
    {
        $input = $request->all();

        $hrmsDocumentAttachments = $this->hrmsDocumentAttachmentsRepository->create($input);

        return $this->sendResponse($hrmsDocumentAttachments->toArray(), trans('custom.hrms_document_attachments_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/hrmsDocumentAttachments/{id}",
     *      summary="Display the specified HrmsDocumentAttachments",
     *      tags={"HrmsDocumentAttachments"},
     *      description="Get HrmsDocumentAttachments",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of HrmsDocumentAttachments",
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
     *                  ref="#/definitions/HrmsDocumentAttachments"
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
        /** @var HrmsDocumentAttachments $hrmsDocumentAttachments */
        $hrmsDocumentAttachments = $this->hrmsDocumentAttachmentsRepository->findWithoutFail($id);

        if (empty($hrmsDocumentAttachments)) {
            return $this->sendError(trans('custom.hrms_document_attachments_not_found'));
        }

        return $this->sendResponse($hrmsDocumentAttachments->toArray(), trans('custom.hrms_document_attachments_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateHrmsDocumentAttachmentsAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/hrmsDocumentAttachments/{id}",
     *      summary="Update the specified HrmsDocumentAttachments in storage",
     *      tags={"HrmsDocumentAttachments"},
     *      description="Update HrmsDocumentAttachments",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of HrmsDocumentAttachments",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="HrmsDocumentAttachments that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/HrmsDocumentAttachments")
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
     *                  ref="#/definitions/HrmsDocumentAttachments"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateHrmsDocumentAttachmentsAPIRequest $request)
    {
        $input = $request->all();

        /** @var HrmsDocumentAttachments $hrmsDocumentAttachments */
        $hrmsDocumentAttachments = $this->hrmsDocumentAttachmentsRepository->findWithoutFail($id);

        if (empty($hrmsDocumentAttachments)) {
            return $this->sendError(trans('custom.hrms_document_attachments_not_found'));
        }

        $hrmsDocumentAttachments = $this->hrmsDocumentAttachmentsRepository->update($input, $id);

        return $this->sendResponse($hrmsDocumentAttachments->toArray(), trans('custom.hrmsdocumentattachments_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/hrmsDocumentAttachments/{id}",
     *      summary="Remove the specified HrmsDocumentAttachments from storage",
     *      tags={"HrmsDocumentAttachments"},
     *      description="Delete HrmsDocumentAttachments",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of HrmsDocumentAttachments",
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
        /** @var HrmsDocumentAttachments $hrmsDocumentAttachments */
        $hrmsDocumentAttachments = $this->hrmsDocumentAttachmentsRepository->findWithoutFail($id);

        if (empty($hrmsDocumentAttachments)) {
            return $this->sendError(trans('custom.hrms_document_attachments_not_found'));
        }

        $disk = Helper::policyWiseDisk($hrmsDocumentAttachments->companySystemID, 'public');

        $path = $hrmsDocumentAttachments->myFileName;
        if ($exists = Storage::disk($disk)->exists($path)) {
            Storage::disk($disk)->delete($path);
        }
        $hrmsDocumentAttachments->delete();
        return $this->sendResponse($id, trans('custom.hrms_document_attachments_deleted_successfully'));

    }

    /**
     * Download the Document Attachments.
     * GET|HEAD /downloadFile
     *
     * @param Request $request
     * @return Response
     */
    public function downloadFile(Request $request)
    {
        $input = $request->all();

        $hrmsDocumentAttachments = $this->hrmsDocumentAttachmentsRepository->findWithoutFail($input['id']);

        if (empty($hrmsDocumentAttachments)) {
            return $this->sendError(trans('custom.document_attachments_not_found'));
        }

        $disk = Helper::policyWiseDisk($hrmsDocumentAttachments->companySystemID, 'public');

        if(!is_null($hrmsDocumentAttachments->myFileName)) {
            if ($exists = Storage::disk($disk)->exists($hrmsDocumentAttachments->myFileName)) {
                return Storage::disk($disk)->download($hrmsDocumentAttachments->myFileName);
            } else {
                return $this->sendError(trans('custom.attachments_detail_not_found'));
            }
        }else{
            return $this->sendError('Attachment is not attached', 404);
        }
    }

}
