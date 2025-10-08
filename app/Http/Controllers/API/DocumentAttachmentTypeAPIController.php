<?php

namespace App\Http\Controllers\API;
/**
=============================================
-- File Name : DocumentAttachmentTypeAPIController.php
-- Project Name : ERP
-- Module Name :  Document Attachment Types
-- Author : Mohamed Fayas
-- Create date : 03 - April 2018
-- Description : This file contains the all CRUD for Document Attachment Types
-- REVISION HISTORY
 */
use App\Http\Requests\API\CreateDocumentAttachmentTypeAPIRequest;
use App\Http\Requests\API\UpdateDocumentAttachmentTypeAPIRequest;
use App\Models\AttachmentTypeConfiguration;
use App\Models\CompanyDocumentAttachment;
use App\Models\DocumentAttachmentType;
use App\Repositories\DocumentAttachmentTypeRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class DocumentAttachmentTypeController
 * @package App\Http\Controllers\API
 */

class DocumentAttachmentTypeAPIController extends AppBaseController
{
    /** @var  DocumentAttachmentTypeRepository */
    private $documentAttachmentTypeRepository;

    public function __construct(DocumentAttachmentTypeRepository $documentAttachmentTypeRepo)
    {
        $this->documentAttachmentTypeRepository = $documentAttachmentTypeRepo;
    }

    /**
     * Display a listing of the DocumentAttachmentType.
     * GET|HEAD /documentAttachmentTypes
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $documentSystemID = filter_var($request['documentSystemID'] ?? 0, FILTER_VALIDATE_INT);
        $companySystemID = filter_var($request['companySystemID'] ?? 0, FILTER_VALIDATE_INT);

        if($documentSystemID != 1){
            $this->documentAttachmentTypeRepository->pushCriteria(new RequestCriteria($request));
            $this->documentAttachmentTypeRepository->pushCriteria(new LimitOffsetCriteria($request));
            $documentAttachmentTypes = $this->documentAttachmentTypeRepository->all();
        }
        else {
            $documentAttachmentTypes = collect();

            $documentAttachment = CompanyDocumentAttachment::where('companySystemID', $companySystemID)->where('documentSystemID', $documentSystemID)->first();
            if($documentAttachment) {
                $existsAttachmentTypes = AttachmentTypeConfiguration::where('document_attachment_id', $documentAttachment->companyDocumentAttachmentID)->get();
                if (count($existsAttachmentTypes) > 0) {
                    $typeArray = $existsAttachmentTypes->pluck('attachment_type_id')->toArray();
                    $documentAttachmentTypes = DocumentAttachmentType::whereIn('travelClaimAttachmentTypeID', $typeArray)->get();
                }
            }
        }

        return $this->sendResponse($documentAttachmentTypes->toArray(), trans('custom.document_attachment_types_retrieved_successfully_1'));
    }

    /**
     * Store a newly created DocumentAttachmentType in storage.
     * POST /documentAttachmentTypes
     *
     * @param CreateDocumentAttachmentTypeAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateDocumentAttachmentTypeAPIRequest $request)
    {
        $input = $request->all();

        $documentAttachmentTypes = $this->documentAttachmentTypeRepository->create($input);

        return $this->sendResponse($documentAttachmentTypes->toArray(), trans('custom.document_attachment_type_saved_successfully'));
    }

    /**
     * Display the specified DocumentAttachmentType.
     * GET|HEAD /documentAttachmentTypes/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var DocumentAttachmentType $documentAttachmentType */
        $documentAttachmentType = $this->documentAttachmentTypeRepository->findWithoutFail($id);

        if (empty($documentAttachmentType)) {
            return $this->sendError(trans('custom.document_attachment_type_not_found'));
        }

        return $this->sendResponse($documentAttachmentType->toArray(), trans('custom.document_attachment_type_retrieved_successfully'));
    }

    /**
     * Update the specified DocumentAttachmentType in storage.
     * PUT/PATCH /documentAttachmentTypes/{id}
     *
     * @param  int $id
     * @param UpdateDocumentAttachmentTypeAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateDocumentAttachmentTypeAPIRequest $request)
    {
        $input = $request->all();

        /** @var DocumentAttachmentType $documentAttachmentType */
        $documentAttachmentType = $this->documentAttachmentTypeRepository->findWithoutFail($id);

        if (empty($documentAttachmentType)) {
            return $this->sendError(trans('custom.document_attachment_type_not_found'));
        }

        $documentAttachmentType = $this->documentAttachmentTypeRepository->update($input, $id);

        return $this->sendResponse($documentAttachmentType->toArray(), trans('custom.documentattachmenttype_updated_successfully'));
    }

    /**
     * Remove the specified DocumentAttachmentType from storage.
     * DELETE /documentAttachmentTypes/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var DocumentAttachmentType $documentAttachmentType */
        $documentAttachmentType = $this->documentAttachmentTypeRepository->findWithoutFail($id);

        if (empty($documentAttachmentType)) {
            return $this->sendError(trans('custom.document_attachment_type_not_found'));
        }

        $documentAttachmentType->delete();

        return $this->sendResponse($id, trans('custom.document_attachment_type_deleted_successfully'));
    }
}
