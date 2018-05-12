<?php
/**
 * =============================================
 * -- File Name : CompanyDocumentAttachmentAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Company Document Attachment
 * -- Author : Mohamed Fayas
 * -- Create date : 04- May 2018
 * -- Description : This file contains the all CRUD for  Company Document Attachment
 * -- REVISION HISTORY
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateCompanyDocumentAttachmentAPIRequest;
use App\Http\Requests\API\UpdateCompanyDocumentAttachmentAPIRequest;
use App\Models\CompanyDocumentAttachment;
use App\Repositories\CompanyDocumentAttachmentRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class CompanyDocumentAttachmentController
 * @package App\Http\Controllers\API
 */

class CompanyDocumentAttachmentAPIController extends AppBaseController
{
    /** @var  CompanyDocumentAttachmentRepository */
    private $companyDocumentAttachmentRepository;

    public function __construct(CompanyDocumentAttachmentRepository $companyDocumentAttachmentRepo)
    {
        $this->companyDocumentAttachmentRepository = $companyDocumentAttachmentRepo;
    }

    /**
     * Display a listing of the CompanyDocumentAttachment.
     * GET|HEAD /companyDocumentAttachments
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->companyDocumentAttachmentRepository->pushCriteria(new RequestCriteria($request));
        $this->companyDocumentAttachmentRepository->pushCriteria(new LimitOffsetCriteria($request));
        $companyDocumentAttachments = $this->companyDocumentAttachmentRepository->all();

        return $this->sendResponse($companyDocumentAttachments->toArray(), 'Company Document Attachments retrieved successfully');
    }

    /**
     * Store a newly created CompanyDocumentAttachment in storage.
     * POST /companyDocumentAttachments
     *
     * @param CreateCompanyDocumentAttachmentAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateCompanyDocumentAttachmentAPIRequest $request)
    {
        $input = $request->all();

        $companyDocumentAttachments = $this->companyDocumentAttachmentRepository->create($input);

        return $this->sendResponse($companyDocumentAttachments->toArray(), 'Company Document Attachment saved successfully');
    }

    /**
     * Display the specified CompanyDocumentAttachment.
     * GET|HEAD /companyDocumentAttachments/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var CompanyDocumentAttachment $companyDocumentAttachment */
        $companyDocumentAttachment = $this->companyDocumentAttachmentRepository->findWithoutFail($id);

        if (empty($companyDocumentAttachment)) {
            return $this->sendError('Company Document Attachment not found');
        }

        return $this->sendResponse($companyDocumentAttachment->toArray(), 'Company Document Attachment retrieved successfully');
    }

    /**
     * Update the specified CompanyDocumentAttachment in storage.
     * PUT/PATCH /companyDocumentAttachments/{id}
     *
     * @param  int $id
     * @param UpdateCompanyDocumentAttachmentAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateCompanyDocumentAttachmentAPIRequest $request)
    {
        $input = $request->all();

        /** @var CompanyDocumentAttachment $companyDocumentAttachment */
        $companyDocumentAttachment = $this->companyDocumentAttachmentRepository->findWithoutFail($id);

        if (empty($companyDocumentAttachment)) {
            return $this->sendError('Company Document Attachment not found');
        }

        $companyDocumentAttachment = $this->companyDocumentAttachmentRepository->update($input, $id);

        return $this->sendResponse($companyDocumentAttachment->toArray(), 'CompanyDocumentAttachment updated successfully');
    }

    /**
     * Remove the specified CompanyDocumentAttachment from storage.
     * DELETE /companyDocumentAttachments/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var CompanyDocumentAttachment $companyDocumentAttachment */
        $companyDocumentAttachment = $this->companyDocumentAttachmentRepository->findWithoutFail($id);

        if (empty($companyDocumentAttachment)) {
            return $this->sendError('Company Document Attachment not found');
        }

        $companyDocumentAttachment->delete();

        return $this->sendResponse($id, 'Company Document Attachment deleted successfully');
    }
}
