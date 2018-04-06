<?php
/**
 * =============================================
 * -- File Name : DocumentAttachmentsAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Document Attachments
 * -- Author : Mohamed Fayas
 * -- Create date : 03 - April 2018
 * -- Description : This file contains the all CRUD for Document Attachments
 * -- REVISION HISTORY
 * -- Date: 05-Apri 2018 By: Fayas Description: Added new functions named as downloadFile()
 *
 */
namespace App\Http\Controllers\API;

use App\Criteria\FilterDocumentAttachmentsCriteria;
use App\Http\Requests\API\CreateDocumentAttachmentsAPIRequest;
use App\Http\Requests\API\UpdateDocumentAttachmentsAPIRequest;
use App\Models\Company;
use App\Models\DocumentAttachments;
use App\Models\DocumentMaster;
use App\Repositories\DocumentAttachmentsRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\Storage;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Class DocumentAttachmentsController
 * @package App\Http\Controllers\API
 */
class DocumentAttachmentsAPIController extends AppBaseController
{
    /** @var  DocumentAttachmentsRepository */
    private $documentAttachmentsRepository;

    public function __construct(DocumentAttachmentsRepository $documentAttachmentsRepo)
    {
        $this->documentAttachmentsRepository = $documentAttachmentsRepo;
    }

    /**
     * Display a listing of the DocumentAttachments.
     * GET|HEAD /documentAttachments
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->documentAttachmentsRepository->pushCriteria(new RequestCriteria($request));
        $this->documentAttachmentsRepository->pushCriteria(new LimitOffsetCriteria($request));
        $this->documentAttachmentsRepository->pushCriteria(new FilterDocumentAttachmentsCriteria($request));
        $documentAttachments = $this->documentAttachmentsRepository->all();

        return $this->sendResponse($documentAttachments->toArray(), 'Document Attachments retrieved successfully');
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

        /** @var DocumentAttachments $documentAttachments */
        $documentAttachments = $this->documentAttachmentsRepository->findWithoutFail($input['id']);

        if (empty($documentAttachments)) {
            return $this->sendError('Document Attachments not found');
        }

        if ($exists = Storage::disk('public')->exists($documentAttachments->path)) {
            $tempContent = Storage::disk('public')->get($documentAttachments->path);
            $content = utf8_encode($tempContent);
            $array = array('content' => $content, 'name' => $documentAttachments->myFileName);
            return response()->json($array, 200, ['Content-type' => 'application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);

        } else {
            return $this->sendError('Attachments not found', 500);
        }
    }

    /**
     * Store a newly created DocumentAttachments in storage.
     * POST /documentAttachments
     *
     * @param CreateDocumentAttachmentsAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateDocumentAttachmentsAPIRequest $request)
    {


        $input = $request->all();

        if (isset($input['docExpirtyDate'])) {
            if ($input['docExpirtyDate']) {
                $input['docExpirtyDate'] = new Carbon($input['docExpirtyDate']);
            }
        }

        $input = $this->convertArrayToValue($input);

        if (isset($input['documentSystemID'])) {

            $documentMaster = DocumentMaster::where('documentSystemID', $input['documentSystemID'])->first();

            if ($documentMaster) {
                $input['documentID'] = $documentMaster->documentID;
            }
        }

        if (isset($input['companySystemID'])) {

            $companyMaster = Company::where('companySystemID', $input['companySystemID'])->first();

            if ($companyMaster) {
                $input['companyID'] = $companyMaster->CompanyID;
            }
        }

        $documentAttachments = $this->documentAttachmentsRepository->create($input);

        $file = $request->request->get('file');
        $decodeFile = base64_decode($file);

        $extension = $input['fileType'];

        $input['myFileName'] = $documentAttachments->companyID . '_' . $documentAttachments->documentID . '_' . $documentAttachments->documentSystemCode . '_' . $documentAttachments->attachmentID . '.' . $extension;

        $path = $documentAttachments->documentID . '/' . $documentAttachments->documentSystemCode . '/' . $input['myFileName'];

        Storage::disk('public')->put($path, $decodeFile);

        $input['isUploaded'] = 1;
        $input['path'] = $path;

        $documentAttachments = $this->documentAttachmentsRepository->update($input, $documentAttachments->attachmentID);

        return $this->sendResponse($documentAttachments->toArray(), 'Document Attachments saved successfully');
    }

    /**
     * Display the specified DocumentAttachments.
     * GET|HEAD /documentAttachments/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var DocumentAttachments $documentAttachments */
        $documentAttachments = $this->documentAttachmentsRepository->findWithoutFail($id);

        if (empty($documentAttachments)) {
            return $this->sendError('Document Attachments not found');
        }

        return $this->sendResponse($documentAttachments->toArray(), 'Document Attachments retrieved successfully');
    }

    /**
     * Update the specified DocumentAttachments in storage.
     * PUT/PATCH /documentAttachments/{id}
     *
     * @param  int $id
     * @param UpdateDocumentAttachmentsAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateDocumentAttachmentsAPIRequest $request)
    {
        $input = $request->all();

        if (isset($input['docExpirtyDate'])) {
            if ($input['docExpirtyDate']) {
                $input['docExpirtyDate'] = new Carbon($input['docExpirtyDate']);
            }
        }

        $input = $this->convertArrayToValue($input);

        /** @var DocumentAttachments $documentAttachments */
        $documentAttachments = $this->documentAttachmentsRepository->findWithoutFail($id);

        if (empty($documentAttachments)) {
            return $this->sendError('Document Attachments not found');
        }

        $documentAttachments = $this->documentAttachmentsRepository->update($input, $id);

        return $this->sendResponse($documentAttachments->toArray(), 'DocumentAttachments updated successfully');
    }

    /**
     * Remove the specified DocumentAttachments from storage.
     * DELETE /documentAttachments/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var DocumentAttachments $documentAttachments */
        $documentAttachments = $this->documentAttachmentsRepository->findWithoutFail($id);

        if (empty($documentAttachments)) {
            return $this->sendError('Document Attachments not found');
        }

        $documentAttachments->delete();

        return $this->sendResponse($id, 'Document Attachments deleted successfully');
    }
}
