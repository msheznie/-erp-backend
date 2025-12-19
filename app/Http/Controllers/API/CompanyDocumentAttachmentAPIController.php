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
 * -- functions : getAllCompanyDocumentAttachment() - created by Rilwan 2019-09-17
 * -- functions : getCompanyPolicyFilterOptions() - created by Rilwan 2019-09-18
 * -- functions : checkDocumentAttachmentPolicy() - created by Rilwan 2020-06-30
 */
namespace App\Http\Controllers\API;

use App\helper\Helper;
use App\Http\Requests\API\CreateCompanyDocumentAttachmentAPIRequest;
use App\Http\Requests\API\UpdateCompanyDocumentAttachmentAPIRequest;
use App\Models\Company;
use App\Models\ApprovalLevel;
use App\Models\CompanyDocumentAttachment;
use App\Models\DocumentMaster;
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

        return $this->sendResponse($companyDocumentAttachments->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.company_document_attachments')]));
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

        return $this->sendResponse($companyDocumentAttachments->toArray(), trans('custom.save', ['attribute' => trans('custom.company_document_attachments')]));
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
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.company_document_attachments')]));
        }

        return $this->sendResponse($companyDocumentAttachment->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.company_document_attachments')]));
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

        $input = array_except($input, ['companySystemID','companyID','documentSystemID','documentID','timeStamp','company','document','access']);

        $input = $this->convertArrayToValue($input);

        $companyDocumentAttachment = $this->companyDocumentAttachmentRepository->findWithoutFail($id);

        if (empty($companyDocumentAttachment)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.company_document_attachments')]));
        }

        if($companyDocumentAttachment->isServiceLineApproval != $input['isServiceLineApproval'] || $companyDocumentAttachment->isAmountApproval != $input['isAmountApproval'] || $companyDocumentAttachment->isCategoryApproval != $input['isCategoryApproval']){
        $checkForActiveApprovalLevel = ApprovalLevel::where('companySystemID', $companyDocumentAttachment->companySystemID)
                                                    ->where('documentSystemID', $companyDocumentAttachment->documentSystemID)
                                                    ->where('isActive', -1)
                                                    ->first();

            if ($checkForActiveApprovalLevel) {
             return $this->sendError(trans('custom.there_is_an_approval_level_created_for_this_docume'), 500);
            }
        }

        $companyDocumentAttachment = $this->companyDocumentAttachmentRepository->update($input, $id);

        return $this->sendResponse($companyDocumentAttachment->toArray(), trans('custom.update', ['attribute' => trans('custom.company_document_attachments')]));
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
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.company_document_attachments')]));
        }

        $companyDocumentAttachment->delete();

        return $this->sendResponse($id, trans('custom.delete', ['attribute' => trans('custom.company_document_attachments')]));
    }


    /**
     * Get company document attachment data for list
     * @param Request $request
     * @return mixed
     */
    public function getAllCompanyDocumentAttachment(Request $request){

        $input = $request->all();
        $search = $request->input('search.value');

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $companyId = $request['companySystemID'];

        $isGroup = \Helper::checkIsCompanyGroup($companyId);

        if($isGroup){
            $childCompanies = \Helper::getGroupCompany($companyId);
        }else{
            $childCompanies = [$companyId];
        }

        $companyDocumentAttachment = CompanyDocumentAttachment::whereIn('companySystemID',$childCompanies)->with(['company','document','access']);

        if (array_key_exists('documentSystemID', $input)) {
            $companyDocumentAttachment = $companyDocumentAttachment->where('documentSystemID', $input['documentSystemID']);
        }

        if($search){
            $companyDocumentAttachment = $companyDocumentAttachment
                ->where(function ($query) use($search){
                    $query->whereHas('company', function ($q) use ($search) {
                        $q->where('CompanyName','LIKE',"%{$search}%");
                    })->orWhereHas('document', function ($q) use ($search) {
                        $q->where('documentDescription','LIKE',"%{$search}%");
                    })->orWhere('docRefNumber','LIKE',"%{$search}%");
                });
        }
        return \DataTables::eloquent($companyDocumentAttachment)
            ->order(function ($query) use ($input) {
                if (request()->has('order') ) {
                    if($input['order'][0]['column'] == 0)
                    {
                        $query->orderBy('companyDocumentAttachmentID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    /*
     * get company or subcompanies
     * */
    public function getCompanyDocumentFilterOptions(Request $request)
    {
        $selectedCompanyId = $request['selectedCompanyId'];
        $isGroup = Helper::checkIsCompanyGroup($selectedCompanyId);

        if($isGroup){
            $subCompanies = Helper::getGroupCompany($selectedCompanyId);
        }else{
            $subCompanies = [$selectedCompanyId];
        }
        /**  Companies by group  Drop Down */
        $output['companies'] = Company::whereIn("companySystemID",$subCompanies)->get();
        $output['documents'] = DocumentMaster::select('documentSystemID','documentID','documentDescription')->get();
        return $this->sendResponse($output, trans('custom.retrieve', ['attribute' => trans('custom.record')]));
    }

    public function checkDocumentAttachmentPolicy(Request $request){

        $input = $request->all();
        $companySystemID = isset($input['companySystemID'])?$input['companySystemID']:0;
        $documentSystemID = isset($input['documentSystemID'])?$input['documentSystemID']:0;

        $result = CompanyDocumentAttachment::where('companySystemID',$companySystemID)
            ->where('documentSystemID',$documentSystemID)
            ->first();

        if(empty($result)){
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.policy')]));
        }

        return $this->sendResponse($result, trans('custom.retrieve', ['attribute' => trans('custom.record')]));
    }
}
