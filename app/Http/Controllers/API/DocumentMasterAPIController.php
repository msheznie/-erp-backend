<?php

namespace App\Http\Controllers\API;

/**
 * =============================================
 * -- File Name : DocumentMasterAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Document Master
 * -- Author : Mohamed Fayas
 * -- Create date : 14 - March 2018
 * -- Description : This file contains the all CRUD for Document Master
 * -- REVISION HISTORY
 */
use App\Http\Requests\API\CreateDocumentMasterAPIRequest;
use App\Http\Requests\API\UpdateDocumentMasterAPIRequest;
use App\Models\DocumentMaster;
use App\Repositories\DocumentMasterRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class DocumentMasterController
 * @package App\Http\Controllers\API
 */
class DocumentMasterAPIController extends AppBaseController
{
    /** @var  DocumentMasterRepository */
    private $documentMasterRepository;

    public function __construct(DocumentMasterRepository $documentMasterRepo)
    {
        $this->documentMasterRepository = $documentMasterRepo;
    }

    /**
     * Display a listing of the DocumentMaster.
     * GET|HEAD /documentMasters
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->documentMasterRepository->pushCriteria(new RequestCriteria($request));
        $this->documentMasterRepository->pushCriteria(new LimitOffsetCriteria($request));
        $documentMasters = $this->documentMasterRepository->all();

        return $this->sendResponse($documentMasters->toArray(), 'Document Masters retrieved successfully');
    }

    /**
     * Store a newly created DocumentMaster in storage.
     * POST /documentMasters
     *
     * @param CreateDocumentMasterAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateDocumentMasterAPIRequest $request)
    {
        $input = $request->all();

        $documentMasters = $this->documentMasterRepository->create($input);

        return $this->sendResponse($documentMasters->toArray(), 'Document Master saved successfully');
    }

    /**
     * Display the specified DocumentMaster.
     * GET|HEAD /documentMasters/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var DocumentMaster $documentMaster */
        $documentMaster = $this->documentMasterRepository->findWithoutFail($id);

        if (empty($documentMaster)) {
            return $this->sendError('Document Master not found');
        }

        return $this->sendResponse($documentMaster->toArray(), 'Document Master retrieved successfully');
    }

    /**
     * Update the specified DocumentMaster in storage.
     * PUT/PATCH /documentMasters/{id}
     *
     * @param  int $id
     * @param UpdateDocumentMasterAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateDocumentMasterAPIRequest $request)
    {
        $input = $request->all();

        /** @var DocumentMaster $documentMaster */
        $documentMaster = $this->documentMasterRepository->findWithoutFail($id);

        if (empty($documentMaster)) {
            return $this->sendError('Document Master not found');
        }

        $documentMaster = $this->documentMasterRepository->update($input, $id);

        return $this->sendResponse($documentMaster->toArray(), 'DocumentMaster updated successfully');
    }

    /**
     * Remove the specified DocumentMaster from storage.
     * DELETE /documentMasters/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var DocumentMaster $documentMaster */
        $documentMaster = $this->documentMasterRepository->findWithoutFail($id);

        if (empty($documentMaster)) {
            return $this->sendError('Document Master not found');
        }

        $documentMaster->delete();

        return $this->sendResponse($id, 'Document Master deleted successfully');
    }

    public function getAllDocuments()
    {
        $document = \Helper::getAllDocuments();
        return $this->sendResponse($document, 'record retrieved successfully');
    }

    public function getAllApprovalDocuments()
    {
        $document = DocumentMaster::select('*')->whereIn('departmentSystemID', [1, 3, 4, 11, 5])->whereIn('documentSystemID', [1, 2, 4, 5, 11, 50, 51, 52, 15, 19, 20, 21, 67, 68, 17])->get()->toArray();
        return $this->sendResponse($document, 'record retrieved successfully');
    }
}
