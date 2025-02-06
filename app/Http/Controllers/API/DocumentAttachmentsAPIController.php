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
use App\Models\CustomerInvoiceDirect;
use App\Models\CompanyPolicyMaster;
use App\Models\DocumentAttachments;
use App\Models\DocumentAttachmentType;
use App\Models\DocumentMaster;
use App\Models\SupplierTenderNegotiation;
use App\Models\TenderBidNegotiation;
use App\Models\TenderNegotiationArea;
use App\Repositories\DocumentAttachmentsRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\Storage;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use Symfony\Component\Finder\SplFileInfo;
use App\helper\Helper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\BidDocumentVerification;
use App\Models\BidSubmissionMaster;
use App\Models\TenderMaster;
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

        foreach ($documentAttachments as $value) {
            $url = Storage::disk(Helper::policyWiseDisk($value->companySystemID, 'public'))->temporaryUrl($value->path, Carbon::now()->addHours(3));
            $value->url = $url;
        }

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

        /*$fileName= "Consolidated_top_suppliers.png";
        return Storage::disk('public')->download($fileName);*/

        /** @var DocumentAttachments $documentAttachments */
        $documentAttachments = $this->documentAttachmentsRepository->findWithoutFail($input['id']);

        if (empty($documentAttachments)) {
            return $this->sendError('Document Attachments not found');
        }

        if (!is_null($documentAttachments->path)) {
            $disk = ($documentAttachments->attachmentType == 11)
                ? 's3SRM'
                : Helper::policyWiseDisk($documentAttachments->companySystemID, 'public');
 
            if (Storage::disk($disk)->exists($documentAttachments->path)) {
                return Storage::disk($disk)->download($documentAttachments->path, $documentAttachments->myFileName);
            } else {
                return $this->sendError('Attachments not found', 500);
            }
        } else {
            return $this->sendError('Attachment is not attached', 404);
        }
    }

    function downloadFileFrom(Request $request)
    {

        $input = $request->all();

        $documentAttachments = $this->documentAttachmentsRepository->findWithoutFail($input['id']);

        if (empty($documentAttachments)) {
            return $this->sendError('Document Attachments not found');
        }

        $fileName = "Desktop/upload/" . $documentAttachments->path;


        /*$filename = 'temp-image.jpg';
        $tempImage = tempnam(sys_get_temp_dir(), $filename);
        copy('https://my-cdn.com/files/image.jpg', $tempImage);

        return response()->download($tempImage, $filename);*/
        $pathToFile = public_path('http://192.168.1.100/purchase_request_32829.pdf');


        return Response::download($pathToFile);

        //return redirect( . 'test.pdf');

        /*$ftp = Storage::createFtpDriver([
            'host'     => '192.168.1.100',
            'username' => 'administrator',
            'password' => 'asd@123',
            'port'     => '8080', // your ftp port
            'timeout'  => '30', // timeout setting
        ]);

        $filecontent = $ftp->get($fileName); // read file content
        // download file.
        return Response::make($filecontent, '200', array(
            'Content-Type' => 'application/octet-stream',
            'Content-Disposition' => 'attachment; filename="'.basename($fileName).'"'
        ));*/
    }

    /**
     * Store a newly created DocumentAttachments in storage.
     * POST /documentAttachments
     *
     * @param Request $request
     *
     * @return array
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {

            $input = $request->all();
            $extension = $input['fileType'];

            $blockExtensions = [
                'ace', 'ade', 'adp', 'ani', 'app', 'asp', 'aspx', 'asx', 'bas', 'bat', 'cla', 'cer', 'chm', 'cmd', 'cnt', 'com',
                'cpl', 'crt', 'csh', 'class', 'der', 'docm', 'exe', 'fxp', 'gadget', 'hlp', 'hpj', 'hta', 'htc', 'inf', 'ins', 'isp', 'its', 'jar',
                'js', 'jse', 'ksh', 'lnk', 'mad', 'maf', 'mag', 'mam', 'maq', 'mar', 'mas', 'mat', 'mau', 'mav', 'maw', 'mda', 'mdb', 'mde', 'mdt',
                'mdw', 'mdz', 'mht', 'mhtml', 'msc', 'msh', 'msh1', 'msh1xml', 'msh2', 'msh2xml', 'mshxml', 'msi', 'msp', 'mst', 'ops', 'osd',
                'ocx', 'pl', 'pcd', 'pif', 'plg', 'prf', 'prg', 'ps1', 'ps1xml', 'ps2', 'ps2xml', 'psc1', 'psc2', 'pst', 'reg', 'scf', 'scr',
                'sct', 'shb', 'shs', 'tmp', 'url', 'vb', 'vbe', 'vbp', 'vbs', 'vsmacros', 'vss', 'vst', 'vsw', 'ws', 'wsc', 'wsf', 'wsh', 'xml',
                'xbap', 'xnk', 'php'
            ];

            if (in_array($extension, $blockExtensions)) {
                if(isset($input['isAutoCreateDocument']) && $input['isAutoCreateDocument']){
                    return [
                        "success" => false,
                        "message" => "This type of file not allow to upload."
                    ];
                }
                else{
                    return $this->sendError('This type of file not allow to upload.', 500);
                }
            }


            if (isset($input['size'])) {
                if ($input['size'] > env('ATTACH_UPLOAD_SIZE_LIMIT')) {
                    if(isset($input['isAutoCreateDocument']) && $input['isAutoCreateDocument']){
                        return [
                            "success" => false,
                            "message" => "Maximum allowed file size is exceeded"
                        ];
                    }
                    else{
                        return $this->sendError("Maximum allowed file size is exceeded. Please upload lesser than ".\Helper::bytesToHuman(env('ATTACH_UPLOAD_SIZE_LIMIT')), 500);
                    }
                }
            }

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

            $companyID = "";
            if (isset($input['companySystemID'])) {

                $companyMaster = Company::where('companySystemID', $input['companySystemID'])->first();

                if ($companyMaster) {
                    $input['companyID'] = $companyMaster->CompanyID;
                    $companyID = $companyMaster->CompanyID;
                }
            }


            $documentAttachments = $this->documentAttachmentsRepository->create($input);

            $input['myFileName'] = $documentAttachments->companyID . '_' . $documentAttachments->documentID . '_' . $documentAttachments->documentSystemCode . '_' . $documentAttachments->attachmentID . '.' . $extension;

            if ($documentAttachments->documentID == 'PRN') {
                $documentAttachments->documentID =  $documentAttachments->documentID . 'I';
            }

            if (Helper::checkPolicy($input['companySystemID'], 50)) {
                $path = $companyID . '/G_ERP/' . $documentAttachments->documentID . '/' . $documentAttachments->documentSystemCode . '/' . $input['myFileName'];
            } else {
                $path = $documentAttachments->documentID . '/' . $documentAttachments->documentSystemCode . '/' . $input['myFileName'];
            }

            if((isset($input['isAutoCreateDocument']) && $input['isAutoCreateDocument']) && !$request->has('file')){
                Storage::disk(Helper::policyWiseDisk($input['companySystemID'], 'public'))->copy($input['path'], $path);
            }
            else{
                $file = $request->get('file');
                $decodeFile = base64_decode($file);

                Storage::disk(Helper::policyWiseDisk($input['companySystemID'], 'public'))->put($path, $decodeFile);
            }

            $input['isUploaded'] = 1;
            $input['path'] = $path;
            if(isset($input['isAutoCreateDocument']) && $input['isAutoCreateDocument']){
                $input['isAutoCreateDocument'] = 1;
            }

            $documentAttachments = $this->documentAttachmentsRepository->update($input, $documentAttachments->attachmentID);

            DB::commit();

            if(isset($input['isAutoCreateDocument']) && $input['isAutoCreateDocument']){
                return [
                    "success" => true,
                    "data" => $documentAttachments->toArray()
                ];
            }
            else{
                return $this->sendResponse($documentAttachments->toArray(), 'Document Attachments saved successfully');
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            if(isset($input['isAutoCreateDocument']) && $input['isAutoCreateDocument']){
                return [
                    "success" => false,
                    "message" => "Unable to upload the attachment"
                ];
            }
            else{
                return $this->sendError('Unable to upload the attachment', 500);
            }
        }
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
        $attachmentType = $input['attachmentType'];
        $attachmentDescription = $input['attachmentDescription'];
        $companySystemID = $input['companySystemID'];
        $documentSystemID = $input['documentSystemID'];
        $documentSystemCode = $input['documentSystemCode'];

        //Update check
        $isExist = DocumentAttachments::where('companySystemID',$companySystemID)
            ->where('attachmentID', '!=', $id)
            ->where('documentSystemID',$documentSystemID)
            ->where('attachmentType',$attachmentType)
            ->where('documentSystemCode',$documentSystemCode)
            ->where('attachmentDescription',$attachmentDescription)
            ->count();

        if($isExist >= 1){
            return $this->sendError('Description already exists', 400);
        } else {
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

        $attachmentDeleteData = self::deleteAttachmentData($documentAttachments);

        if($attachmentDeleteData['status']){
            return $this->sendResponse($attachmentDeleteData['data'], $attachmentDeleteData['message']);
        }
        else {
            return $this->sendError($attachmentDeleteData['message'], $attachmentDeleteData['code']);
        }
    }

    public function deleteAttachmentData($documentAttachments) {
        $path = $documentAttachments->path;

        $disk = Helper::policyWiseDisk($documentAttachments->companySystemID, 'public');

        $attachment = DocumentAttachments::where('attachmentID', $documentAttachments->attachmentID)
            ->first();

        if ($attachment['documentSystemID'] == 20) {
            $invoice = CustomerInvoiceDirect::find($attachment['documentSystemCode']);
            if (!empty($invoice)) {
                if ($invoice->confirmedYN == 1 || $invoice->approved == -1) {
                    return [
                        'status' => false,
                        'message' => 'Customer invoice confirmed, you cannot delete the attachment',
                        'code' => 500
                    ];
                }
            }
        }

        if ($attachment['pullFromAnotherDocument'] == 0) {
            if ($exists = Storage::disk($disk)->exists($path)) {
                $documentAttachments->delete();
                Storage::disk($disk)->delete($path);
            } else {
                $documentAttachments->delete();
            }
        } else if ($attachment['pullFromAnotherDocument'] == -1) {
            $documentAttachments->delete();
        }

        if($documentAttachments['attachmentType'] === 3){
            $exitingAmendmentRecords =  DocumentAttachments::where('companySystemID',$documentAttachments['companySystemID'])
                ->where('documentSystemID', $documentAttachments['documentSystemID'])
                ->where('attachmentType', $documentAttachments['attachmentType'])
                ->where('documentSystemCode', $documentAttachments['documentSystemCode'])
                ->orderBy('attachmentID', 'asc')
                ->get();
            $i = 1;
            foreach ($exitingAmendmentRecords as $exitingAmendmentRecord){
                $request['order_number'] = $i;
                DocumentAttachments::where('attachmentID', $exitingAmendmentRecord['attachmentID'])->update(['order_number' => $i]);
                $i++;
            }
        }
        return [
            'status' => true,
            'message' => 'Document Attachments deleted successfully',
            'data' => $documentAttachments->attachmentID
        ];
    }

    public static function getImageByPath(Request $request)
    {
        $input = $request->all();

        $exists = Storage::disk('public')->exists($input['path']);
        if ($exists) {
            $image = Storage::disk('public')->get($input['path']);
            return response($image, 200)->header('Content-Type', 'image/png');
        } else {
            return response([], 200);
        }
    }

    /**
     * Get attachment master data for list
     * @param Request $request
     * @return mixed
     */
    public function getAllAttachments(Request $request)
    {
        $input = $request->all();

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $companyId = $input['companyId'];
        $isGroup = Helper::checkIsCompanyGroup($companyId);

        if ($isGroup) {
            $childCompanies = Helper::getGroupCompany($companyId);
        } else {
            $childCompanies = [$companyId];
        }

        $documentSystemID = isset($input['documentSystemID']) ? Helper::isArray($input['documentSystemID']) : 0;
        $attachmentType = isset($input['attachmentType']) ? Helper::isArray($input['attachmentType']) : 0;
        $search = str_replace("\\", "\\\\", $request->input('search.value'));
        $attachments = DocumentAttachments::whereIn('companySystemID', $childCompanies)
            ->when($documentSystemID > 0, function ($query) use ($documentSystemID) {
                $query->where('documentSystemID', $documentSystemID);
            })
            ->when($attachmentType > 0, function ($query) use ($attachmentType) {
                $query->where('attachmentType', $attachmentType);
            })
            ->with([
                'document',
                'type' => function ($query) {
                    $query->select('travelClaimAttachmentTypeID', 'description');
                },
                'request' => function ($query) {
                    $query->select('purchaseRequestID', 'purchaseRequestCode', 'companySystemID', 'documentSystemID');
                },
                'order' => function ($query) {
                    $query->select('purchaseOrderID', 'purchaseOrderCode', 'companySystemID', 'documentSystemID');
                },
                'grv' => function ($query) {
                    $query->select('grvAutoID', 'grvPrimaryCode', 'companySystemID', 'documentSystemID');
                },
                'payment_voucher' => function ($query) {
                    $query->select('PayMasterAutoId', 'BPVcode', 'companySystemID', 'documentSystemID');
                },
                'expense_claim' => function ($query) {
                    $query->select('expenseClaimMasterAutoID', 'expenseClaimCode', 'companySystemID', 'documentSystemID');
                },
                'stock_adjustment' => function ($query) {
                    $query->select('stockAdjustmentAutoID', 'stockAdjustmentCode', 'companySystemID', 'documentSystemID');
                },
                'material_issue' => function ($query) {
                    $query->select('itemIssueAutoID', 'itemIssueCode', 'companySystemID', 'documentSystemID');
                },
                'material_request' => function ($query) {
                    $query->select('RequestID', 'RequestCode', 'companySystemID', 'documentSystemID');
                },
                'receive_stock' => function ($query) {
                    $query->select('stockReceiveAutoID', 'stockReceiveCode', 'companySystemID', 'documentSystemID');
                },
                'supplier_invoice' => function ($query) {
                    $query->select('bookingSuppMasInvAutoID', 'bookingInvCode', 'companySystemID', 'documentSystemID');
                },
                'stock_return' => function ($query) {
                    $query->select('itemReturnAutoID', 'itemReturnCode', 'companySystemID', 'documentSystemID');
                },
                'stock_transfer' => function ($query) {
                    $query->select('stockTransferAutoID', 'stockTransferCode', 'companySystemID', 'documentSystemID');
                },
                'logistic' => function ($query) {
                    $query->select('logisticMasterID', 'logisticDocCode', 'companySystemID', 'documentSystemID');
                },
                'debit_note' => function ($query) {
                    $query->select('debitNoteAutoID', 'debitNoteCode', 'companySystemID', 'documentSystemID');
                },
                /*'direct_payment'=>function($query){
                    $query->select('purchaseRequestID','purchaseRequestCode','companySystemID','documentSystemID');
                },*/
                'journal_entries' => function ($query) {
                    $query->select('jvMasterAutoId', 'JVcode', 'companySystemID', 'documentSystemID');
                },
                /*'direct_invoice'=>function($query){
                    $query->select('purchaseRequestID','purchaseRequestCode','companySystemID','documentSystemID');
                },*/
                'credit_note' => function ($query) {
                    $query->select('creditNoteAutoID', 'creditNoteCode', 'companySystemID', 'documentSystemID');
                },
                'customer_invoice' => function ($query) {
                    $query->select('custInvoiceDirectAutoID', 'bookingInvCode', 'companySystemID', 'documentSystemiD');
                },
                'bank_receipt' => function ($query) {
                    $query->select('custReceivePaymentAutoID', 'custPaymentReceiveCode', 'companySystemID', 'documentSystemID');
                },
                'fixed_asset' => function ($query) {
                    $query->select('faID', 'faCode', 'companySystemID', 'documentSystemID');
                },
                'fixed_asset_dep' => function ($query) {
                    $query->select('depMasterAutoID', 'depCode', 'companySystemID', 'documentSystemID');
                },
                'purchase_return' => function ($query) {
                    $query->select('purhaseReturnAutoID', 'purchaseReturnCode', 'companySystemID', 'documentSystemID');
                },
                /*'job_bonus'=>function($query){
                    $query->select('purchaseRequestID','purchaseRequestCode','companySystemID','documentSystemID');
                },
                'desert_allowance'=>function($query){
                    $query->select('purchaseRequestID','purchaseRequestCode','companySystemID','documentSystemID');
                },
                'salary_dec'=>function($query){
                    $query->select('purchaseRequestID','purchaseRequestCode','companySystemID','documentSystemID');
                },*/
                'monthly_addition' => function ($query) {
                    $query->select('monthlyAdditionsMasterID', 'monthlyAdditionsCode', 'companySystemID', 'documentSystemID');
                },
                /*'monthly_deduction'=>function($query){
                    $query->select('purchaseRequestID','purchaseRequestCode','companySystemID','documentSystemID');
                },
                'job_bonus_calculation'=>function($query){
                    $query->select('purchaseRequestID','purchaseRequestCode','companySystemID','documentSystemID');
                },
                'desert_allowance_calculation'=>function($query){
                    $query->select('purchaseRequestID','purchaseRequestCode','companySystemID','documentSystemID');
                },
                'over_time_calculation'=>function($query){
                    $query->select('purchaseRequestID','purchaseRequestCode','companySystemID','documentSystemID');
                },
                'loan_management'=>function($query){
                    $query->select('purchaseRequestID','purchaseRequestCode','companySystemID','documentSystemID');
                },
                'extra_pay'=>function($query){
                    $query->select('purchaseRequestID','purchaseRequestCode','companySystemID','documentSystemID');
                },
                'salary_process'=>function($query){
                    $query->select('purchaseRequestID','purchaseRequestCode','companySystemID','documentSystemID');
                },
                'split_salary'=>function($query){
                    $query->select('purchaseRequestID','purchaseRequestCode','companySystemID','documentSystemID');
                },*/
                'leave_application' => function ($query) {
                    $query->select('leavedatamasterID', 'leaveDataMasterCode', 'companySystemID', 'documentID');
                },
                /*'leave_accrual'=>function($query){
                    $query->select('purchaseRequestID','purchaseRequestCode','companySystemID','documentSystemID');
                },*/
                'batch_submission' => function ($query) {
                    $query->select('customerInvoiceTrackingID', 'customerInvoiceTrackingCode', 'companySystemID', 'documentSystemID');
                },
                /*'bonus_sheet'=>function($query){
                    $query->select('purchaseRequestID','purchaseRequestCode','companySystemID','documentSystemID');
                },*/
                'fixed_asset_disposal' => function ($query) {
                    $query->select('assetdisposalMasterAutoID', 'disposalDocumentCode', 'companySystemID', 'documentSystemID');
                },
                /*'non_salary_payment'=>function($query){
                    $query->select('purchaseRequestID','purchaseRequestCode','companySystemID','documentSystemID');
                },
                'final_settlement'=>function($query){
                    $query->select('purchaseRequestID','purchaseRequestCode','companySystemID','documentSystemID');
                },
                'travel_claim_request'=>function($query){
                    $query->select('purchaseRequestID','purchaseRequestCode','companySystemID','documentSystemID');
                },
                'travel_claim_accrual'=>function($query){
                    $query->select('purchaseRequestID','purchaseRequestCode','companySystemID','documentSystemID');
                },*/
                'budget_transfer_notes' => function ($query) {
                    $query->select('budgetTransferFormAutoID', 'transferVoucherNo', 'companySystemID', 'documentSystemID');
                },
                /*'probation_form'=>function($query){
                    $query->select('purchaseRequestID','purchaseRequestCode','companySystemID','documentSystemID');
                },
                'radio_active_allowance'=>function($query){
                    $query->select('purchaseRequestID','purchaseRequestCode','companySystemID','documentSystemID');
                },
                'job_profile'=>function($query){
                    $query->select('purchaseRequestID','purchaseRequestCode','companySystemID','documentSystemID');
                },
                'journey_plan'=>function($query){
                    $query->select('purchaseRequestID','purchaseRequestCode','companySystemID','documentSystemID');
                },
                'recruitment_request'=>function($query){
                    $query->select('purchaseRequestID','purchaseRequestCode','companySystemID','documentSystemID');
                },*/
                'supplier_master' => function ($query) {
                    $query->select('supplierCodeSystem', 'primarySupplierCode', 'primaryCompanySystemID', 'documentSystemID');
                },
                'item_master' => function ($query) {
                    $query->select('itemCodeSystem', 'primaryCode', 'primaryCompanySystemID', 'documentSystemID');
                },
                'customer_master' => function ($query) {
                    $query->select('customerCodeSystem', 'CutomerCode', 'primaryCompanySystemID', 'documentSystemID');
                },
                'chart_of_account_master' => function ($query) {
                    $query->select('chartOfAccountSystemID', 'AccountCode', 'primaryCompanySystemID', 'documentSystemID');
                },
                /*'po_logistic'=>function($query){
                    $query->select('purchaseRequestID','purchaseRequestCode','companySystemID','documentSystemID');
                },*/
                'inventory_reclassification' => function ($query) {
                    $query->select('inventoryreclassificationID', 'documentCode', 'companySystemID', 'documentSystemID');
                },
                'bank_reconciliation' => function ($query) {
                    $query->select('bankRecAutoID', 'bankRecPrimaryCode', 'companySystemID', 'documentSystemID');
                },
                'asset_capitalization' => function ($query) {
                    $query->select('capitalizationID', 'capitalizationCode', 'companySystemID', 'documentSystemID');
                },
                'payment_bank_transfer' => function ($query) {
                    $query->select('paymentBankTransferID', 'bankTransferDocumentCode', 'companySystemID', 'documentSystemID');
                },
                /*'budget'=>function($query){
                    $query->select('budgetmasterID','purchaseRequestCode','companySystemID','documentSystemID');
                },*/
                /*'bank_account'=>function($query){
                    $query->select('bankAccountAutoID','purchaseRequestCode','companySystemID','documentSystemID');
                },*/
                'sales_quotation' => function ($query) {
                    $query->select('quotationMasterID', 'quotationCode', 'companySystemID', 'documentSystemID');
                },
                'console_jv' => function ($query) {
                    $query->select('consoleJvMasterAutoId', 'consoleJVcode', 'companySystemID', 'documentSystemID');
                },
                'matching' => function ($query) {
                    $query->select('matchDocumentMasterAutoID', 'matchingDocCode', 'companySystemID', 'documentSystemID');
                },
                'delivery_order' => function ($query) {
                    $query->select('deliveryOrderID', 'deliveryOrderCode', 'companySystemID', 'documentSystemID');
                },
                /*'contracts'=>function($query){
                    $query->select('purchaseRequestID','purchaseRequestCode','companySystemID','documentSystemID');
                },
                'contract_details'=>function($query){
                    $query->select('purchaseRequestID','purchaseRequestCode','companySystemID','documentSystemID');
                },*/
               /* 'mobile_bill' => function ($query) {
                    $query->select('mobilebillMasterID', 'mobilebillmasterCode', 'documentSystemID');
                },*/
                /*'proforma'=>function($query){
                    $query->select('purchaseRequestID','purchaseRequestCode','companySystemID','documentSystemID');
                },*/
                /*'material_outward_ticket'=>function($query){
                    $query->select('purchaseRequestID','purchaseRequestCode','companySystemID','documentSystemID');
                },
                'material_inward_ticket'=>function($query){
                    $query->select('purchaseRequestID','purchaseRequestCode','companySystemID','documentSystemID');
                },
                'damage_beyond_repair'=>function($query){
                    $query->select('purchaseRequestID','purchaseRequestCode','companySystemID','documentSystemID');
                },
                'lost_in_hole'=>function($query){
                    $query->select('purchaseRequestID','purchaseRequestCode','companySystemID','documentSystemID');
                },
                'job_card'=>function($query){
                    $query->select('purchaseRequestID','purchaseRequestCode','companySystemID','documentSystemID');
                },
                'evaluation'=>function($query){
                    $query->select('purchaseRequestID','purchaseRequestCode','companySystemID','documentSystemID');
                },
                'registered_supplier'=>function($query){
                    $query->select('purchaseRequestID','purchaseRequestCode','companySystemID','documentSystemID');
                },*/
                'sales_return' => function ($query) {
                    $query->select('id', 'salesReturnCode', 'companySystemID', 'documentSystemID');
                }

            ])
            ->when($search, function ($query) use ($search, $childCompanies) {

                $query->where(function ($main) use ($search, $childCompanies) {
                    $main->where('documentID', 'LIKE', "%{$search}%")
                        ->orWhere('attachmentDescription', 'LIKE', "%{$search}%")
                        ->orWhere('myFileName', 'LIKE', "%{$search}%")
                        ->orWhere(function ($q) use ($search, $childCompanies) {
                            $q->whereHas('type', function ($q1) use ($search, $childCompanies) {
                                $q1->where('description', 'LIKE', "%{$search}%");
                            });
                        })
                        ->orWhere(function ($q) use ($search, $childCompanies) {
                            $q->whereIn('documentSystemID', [1, 50, 51])
                                ->whereHas('request', function ($q1) use ($search, $childCompanies) {
                                    $q1->whereIn('companySystemID', $childCompanies)
                                        ->where('purchaseRequestCode', 'LIKE', "%{$search}%");
                                });
                        })
                        ->orWhere(function ($q) use ($search, $childCompanies) {
                            $q->whereIn('documentSystemID', [2, 5, 52])
                                ->whereHas('order', function ($q1) use ($search, $childCompanies) {
                                    $q1->whereIn('companySystemID', $childCompanies)
                                        ->where('purchaseOrderCode', 'LIKE', "%{$search}%");
                                });
                        })
                        ->orWhere(function ($q) use ($search, $childCompanies) {
                            $q->where('documentSystemID', 3)
                                ->whereHas('grv', function ($q1) use ($search, $childCompanies) {
                                    $q1->whereIn('companySystemID', $childCompanies)
                                        ->where('grvPrimaryCode', 'LIKE', "%{$search}%");
                                });
                        })
                        ->orWhere(function ($q) use ($search, $childCompanies) {
                            $q->where('documentSystemID', 4)
                                ->whereHas('payment_voucher', function ($q1) use ($search, $childCompanies) {
                                    $q1->whereIn('companySystemID', $childCompanies)
                                        ->where('BPVcode', 'LIKE', "%{$search}%");
                                });
                        })
                        ->orWhere(function ($q) use ($search, $childCompanies) {
                            $q->where('documentSystemID', 6)
                                ->whereHas('expense_claim', function ($q1) use ($search, $childCompanies) {
                                    $q1->whereIn('companySystemID', $childCompanies)
                                        ->where('expenseClaimCode', 'LIKE', "%{$search}%");
                                });
                        })
                        ->orWhere(function ($q) use ($search, $childCompanies) {
                            $q->where('documentSystemID', 7)
                                ->whereHas('stock_adjustment', function ($q1) use ($search, $childCompanies) {
                                    $q1->whereIn('companySystemID', $childCompanies)
                                        ->where('stockAdjustmentCode', 'LIKE', "%{$search}%");
                                });
                        })
                        ->orWhere(function ($q) use ($search, $childCompanies) {
                            $q->where('documentSystemID', 8)
                                ->whereHas('material_issue', function ($q1) use ($search, $childCompanies) {
                                    $q1->whereIn('companySystemID', $childCompanies)
                                        ->where('itemIssueCode', 'LIKE', "%{$search}%");
                                });
                        })
                        ->orWhere(function ($q) use ($search, $childCompanies) {
                            $q->where('documentSystemID', 9)
                                ->whereHas('material_request', function ($q1) use ($search, $childCompanies) {
                                    $q1->whereIn('companySystemID', $childCompanies)
                                        ->where('RequestCode', 'LIKE', "%{$search}%");
                                });
                        })
                        ->orWhere(function ($q) use ($search, $childCompanies) {
                            $q->where('documentSystemID', 10)
                                ->whereHas('receive_stock', function ($q1) use ($search, $childCompanies) {
                                    $q1->whereIn('companySystemID', $childCompanies)
                                        ->where('stockReceiveCode', 'LIKE', "%{$search}%");
                                });
                        })
                        ->orWhere(function ($q) use ($search, $childCompanies) {
                            $q->where('documentSystemID', 11)
                                ->whereHas('supplier_invoice', function ($q1) use ($search, $childCompanies) {
                                    $q1->whereIn('companySystemID', $childCompanies)
                                        ->where('bookingInvCode', 'LIKE', "%{$search}%");
                                });
                        })
                        ->orWhere(function ($q) use ($search, $childCompanies) {
                            $q->where('documentSystemID', 12)
                                ->whereHas('stock_return', function ($q1) use ($search, $childCompanies) {
                                    $q1->whereIn('companySystemID', $childCompanies)
                                        ->where('itemReturnCode', 'LIKE', "%{$search}%");
                                });
                        })
                        ->orWhere(function ($q) use ($search, $childCompanies) {
                            $q->where('documentSystemID', 13)
                                ->whereHas('stock_transfer', function ($q1) use ($search, $childCompanies) {
                                    $q1->whereIn('companySystemID', $childCompanies)
                                        ->where('stockTransferCode', 'LIKE', "%{$search}%");
                                });
                        })
                        ->orWhere(function ($q) use ($search, $childCompanies) {
                            $q->where('documentSystemID', 14)
                                ->whereHas('logistic', function ($q1) use ($search, $childCompanies) {
                                    $q1->whereIn('companySystemID', $childCompanies)
                                        ->where('logisticDocCode', 'LIKE', "%{$search}%");
                                });
                        })
                        ->orWhere(function ($q) use ($search, $childCompanies) {
                            $q->where('documentSystemID', 15)
                                ->whereHas('debit_note', function ($q1) use ($search, $childCompanies) {
                                    $q1->whereIn('companySystemID', $childCompanies)
                                        ->where('debitNoteCode', 'LIKE', "%{$search}%");
                                });
                        })
                        ->orWhere(function ($q) use ($search, $childCompanies) {
                            $q->where('documentSystemID', 17)
                                ->whereHas('journal_entries', function ($q1) use ($search, $childCompanies) {
                                    $q1->whereIn('companySystemID', $childCompanies)
                                        ->where('JVcode', 'LIKE', "%{$search}%");
                                });
                        })
                        ->orWhere(function ($q) use ($search, $childCompanies) {
                            $q->where('documentSystemID', 19)
                                ->whereHas('credit_note', function ($q1) use ($search, $childCompanies) {
                                    $q1->whereIn('companySystemID', $childCompanies)
                                        ->where('creditNoteCode', 'LIKE', "%{$search}%");
                                });
                        })
                        ->orWhere(function ($q) use ($search, $childCompanies) {
                            $q->where('documentSystemID', 20)
                                ->whereHas('customer_invoice', function ($q1) use ($search, $childCompanies) {
                                    $q1->whereIn('companySystemID', $childCompanies)
                                        ->where('bookingInvCode', 'LIKE', "%{$search}%");
                                });
                        })
                        ->orWhere(function ($q) use ($search, $childCompanies) {
                            $q->where('documentSystemID', 21)
                                ->whereHas('bank_receipt', function ($q1) use ($search, $childCompanies) {
                                    $q1->whereIn('companySystemID', $childCompanies)
                                        ->where('custPaymentReceiveCode', 'LIKE', "%{$search}%");
                                });
                        })
                        ->orWhere(function ($q) use ($search, $childCompanies) {
                            $q->where('documentSystemID', 22)
                                ->whereHas('fixed_asset', function ($q1) use ($search, $childCompanies) {
                                    $q1->whereIn('companySystemID', $childCompanies)
                                        ->where('faCode', 'LIKE', "%{$search}%");
                                });
                        })
                        ->orWhere(function ($q) use ($search, $childCompanies) {
                            $q->where('documentSystemID', 23)
                                ->whereHas('fixed_asset_dep', function ($q1) use ($search, $childCompanies) {
                                    $q1->whereIn('companySystemID', $childCompanies)
                                        ->where('depCode', 'LIKE', "%{$search}%");
                                });
                        })
                        ->orWhere(function ($q) use ($search, $childCompanies) {
                            $q->where('documentSystemID', 24)
                                ->whereHas('purchase_return', function ($q1) use ($search, $childCompanies) {
                                    $q1->whereIn('companySystemID', $childCompanies)
                                        ->where('purchaseReturnCode', 'LIKE', "%{$search}%");
                                });
                        })
                        ->orWhere(function ($q) use ($search, $childCompanies) {
                            $q->where('documentSystemID', 28)
                                ->whereHas('monthly_addition', function ($q1) use ($search, $childCompanies) {
                                    $q1->whereIn('companySystemID', $childCompanies)
                                        ->where('monthlyAdditionsCode', 'LIKE', "%{$search}%");
                                });
                        })
                        ->orWhere(function ($q) use ($search, $childCompanies) {
                            $q->where('documentID', 'LA')
                                ->whereHas('leave_application', function ($q1) use ($search, $childCompanies) {
                                    $q1->whereIn('companySystemID', $childCompanies)
                                        ->where('leaveDataMasterCode', 'LIKE', "%{$search}%");
                                });
                        })
                        ->orWhere(function ($q) use ($search, $childCompanies) {
                            $q->where('documentSystemID', 39)
                                ->whereHas('batch_submission', function ($q1) use ($search, $childCompanies) {
                                    $q1->whereIn('companySystemID', $childCompanies)
                                        ->where('customerInvoiceTrackingCode', 'LIKE', "%{$search}%");
                                });
                        })
                        ->orWhere(function ($q) use ($search, $childCompanies) {
                            $q->where('documentSystemID', 41)
                                ->whereHas('fixed_asset_disposal', function ($q1) use ($search, $childCompanies) {
                                    $q1->whereIn('companySystemID', $childCompanies)
                                        ->where('disposalDocumentCode', 'LIKE', "%{$search}%");
                                });
                        })
                        ->orWhere(function ($q) use ($search, $childCompanies) {
                            $q->where('documentSystemID', 46)
                                ->whereHas('budget_transfer_notes', function ($q1) use ($search, $childCompanies) {
                                    $q1->whereIn('companySystemID', $childCompanies)
                                        ->where('transferVoucherNo', 'LIKE', "%{$search}%");
                                });
                        })
                        ->orWhere(function ($q) use ($search, $childCompanies) {
                            $q->where('documentSystemID', 56)
                                ->whereHas('supplier_master', function ($q1) use ($search, $childCompanies) {
                                    $q1->whereIn('companySystemID', $childCompanies)
                                        ->where('primarySupplierCode', 'LIKE', "%{$search}%");
                                });
                        })
                        ->orWhere(function ($q) use ($search, $childCompanies) {
                            $q->where('documentSystemID', 57)
                                ->whereHas('item_master', function ($q1) use ($search, $childCompanies) {
                                    $q1->whereIn('companySystemID', $childCompanies)
                                        ->where('primaryCode', 'LIKE', "%{$search}%");
                                });
                        })
                        ->orWhere(function ($q) use ($search, $childCompanies) {
                            $q->where('documentSystemID', 58)
                                ->whereHas('customer_master', function ($q1) use ($search, $childCompanies) {
                                    $q1->whereIn('companySystemID', $childCompanies)
                                        ->where('CutomerCode', 'LIKE', "%{$search}%");
                                });
                        })
                        ->orWhere(function ($q) use ($search, $childCompanies) {
                            $q->where('documentSystemID', 59)
                                ->whereHas('chart_of_account_master', function ($q1) use ($search, $childCompanies) {
                                    $q1->whereIn('companySystemID', $childCompanies)
                                        ->where('AccountCode', 'LIKE', "%{$search}%");
                                });
                        })
                        ->orWhere(function ($q) use ($search, $childCompanies) {
                            $q->where('documentSystemID', 61)
                                ->whereHas('inventory_reclassification', function ($q1) use ($search, $childCompanies) {
                                    $q1->whereIn('companySystemID', $childCompanies)
                                        ->where('documentCode', 'LIKE', "%{$search}%");
                                });
                        })
                        ->orWhere(function ($q) use ($search, $childCompanies) {
                            $q->where('documentSystemID', 62)
                                ->whereHas('bank_reconciliation', function ($q1) use ($search, $childCompanies) {
                                    $q1->whereIn('companySystemID', $childCompanies)
                                        ->where('bankRecPrimaryCode', 'LIKE', "%{$search}%");
                                });
                        })
                        ->orWhere(function ($q) use ($search, $childCompanies) {
                            $q->where('documentSystemID', 63)
                                ->whereHas('asset_capitalization', function ($q1) use ($search, $childCompanies) {
                                    $q1->whereIn('companySystemID', $childCompanies)
                                        ->where('capitalizationCode', 'LIKE', "%{$search}%");
                                });
                        })
                        ->orWhere(function ($q) use ($search, $childCompanies) {
                            $q->where('documentSystemID', 64)
                                ->whereHas('payment_bank_transfer', function ($q1) use ($search, $childCompanies) {
                                    $q1->whereIn('companySystemID', $childCompanies)
                                        ->where('bankTransferDocumentCode', 'LIKE', "%{$search}%");
                                });
                        })
                        ->orWhere(function ($q) use ($search, $childCompanies) {
                            $q->whereIn('documentSystemID', [67, 68])
                                ->whereHas('sales_quotation', function ($q1) use ($search, $childCompanies) {
                                    $q1->whereIn('companySystemID', $childCompanies)
                                        ->where('quotationCode', 'LIKE', "%{$search}%");
                                });
                        })
                        ->orWhere(function ($q) use ($search, $childCompanies) {
                            $q->where('documentSystemID', 69)
                                ->whereHas('console_jv', function ($q1) use ($search, $childCompanies) {
                                    $q1->whereIn('companySystemID', $childCompanies)
                                        ->where('consoleJVcode', 'LIKE', "%{$search}%");
                                });
                        })
                        ->orWhere(function ($q) use ($search, $childCompanies) {
                            $q->where('documentSystemID', 70)
                                ->whereHas('matching', function ($q1) use ($search, $childCompanies) {
                                    $q1->whereIn('companySystemID', $childCompanies)
                                        ->where('matchingDocCode', 'LIKE', "%{$search}%");
                                });
                        })
                        ->orWhere(function ($q) use ($search, $childCompanies) {
                            $q->where('documentSystemID', 71)
                                ->whereHas('delivery_order', function ($q1) use ($search, $childCompanies) {
                                    $q1->whereIn('companySystemID', $childCompanies)
                                        ->where('deliveryOrderCode', 'LIKE', "%{$search}%");
                                });
                        })
                        ->orWhere(function ($q) use ($search, $childCompanies) {
                            $q->where('documentSystemID', 74)
                                ->whereHas('mobile_bill', function ($q1) use ($search, $childCompanies) {
                                    $q1->where('mobilebillmasterCode', 'LIKE', "%{$search}%");
                                });
                        })
                        ->orWhere(function ($q) use ($search, $childCompanies) {
                            $q->where('documentSystemID', 87)
                                ->whereHas('sales_return', function ($q1) use ($search, $childCompanies) {
                                    $q1->where('salesReturnCode', 'LIKE', "%{$search}%");
                                });
                        });
                });
            })
            ->select(
                'attachmentID',
                'companySystemID',
                'companyID',
                'documentSystemID',
                'documentID',
                'documentSystemCode',
                'attachmentDescription',
                'originalFileName',
                'myFileName',
                'path',
                'attachmentType',
                'timeStamp',
                'sizeInKbs',
                'pullFromAnotherDocument'
            );

        return \DataTables::eloquent($attachments)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('attachmentID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    public function getAttachmentFormData(Request $request)
    {
        $output['documents'] = DocumentMaster::all();
        $output['attachmentTypes'] = DocumentAttachmentType::all();

        return $this->sendResponse($output, 'Record retrieved successfully');
    }

    public function downloadFileSRM(Request $request)
    {

        $input = $request->all();
        if (Storage::disk('s3SRM')->exists($input['fileName'])) {
            return Storage::disk('s3SRM')->download($input['fileName'], 'Attachment');
        } else {
            return $this->sendError('Attachments not found', 500);
        }
    }
    public function downloadFileTender(Request $request){ 
      
        $input = $request->all(); 
        $companyId = $input['company_id'];
        $filePath = $input['fileName']; 
        if (!is_null($filePath)) {
            if ($exists = Storage::disk(Helper::policyWiseDisk($companyId, 'public'))->exists($filePath)) {
                return Storage::disk(Helper::policyWiseDisk($companyId, 'public'))->download($filePath, 'File');
            } else {
                return $this->sendError('Attachments not found', 500);
            }
        } else {
            return $this->sendError('Attachment is not attached', 404);
        }
    }
    public function storeTenderDocuments(CreateDocumentAttachmentsAPIRequest $request){
        $input = $request->all();
        $attachmentType = ($input['documentSystemID'] == '128') ? 0 : $input['attachmentType'];
        $attachmentDescription = $input['attachmentDescription'];
        $companySystemID = $input['companySystemID'];
        $documentSystemID = $input['documentSystemID'];
        $documentSystemCode = $input['documentSystemCode'];

        $isExist = DocumentAttachments::where('companySystemID',$companySystemID)
        ->where('documentSystemID',$documentSystemID)
        ->where('attachmentType',$attachmentType)
        ->where('documentSystemCode',$documentSystemCode)
        ->where('attachmentDescription',$attachmentDescription)
        ->count(); 
        if($isExist >= 1){ 
           return ['status' => false, 'message' => 'Description already exists'];  
        }else {
            $i = 1;
            if($attachmentType == 3){
               $exitingAmendmentRecords =  DocumentAttachments::where('companySystemID',$companySystemID)
                    ->where('documentSystemID',$documentSystemID)
                    ->where('attachmentType',$attachmentType)
                    ->where('documentSystemCode',$documentSystemCode)
                    ->orderBy('attachmentID', 'asc')
                    ->get();

               foreach ($exitingAmendmentRecords as $exitingAmendmentRecord){
                   $request['order_number'] = $i;
                   DocumentAttachments::where('attachmentID', $exitingAmendmentRecord['attachmentID'])->update(['order_number' => $i]);
                   $i++;
               }
                $request['order_number'] = $i;
                return self::store($request);
            } else {
                return self::store($request);
            }
        } 
      
    }



    public function getTenderBitsDoc(Request $request)
    {
       
        $input = $request->all();

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }
        $sort = 'asc';
        $id = $request['id'];
        $envelopType = $request['envelopType'];

        if($this->getOldBidSubmissionCodeForTechnicalAndCommercial($id, $envelopType) != null){
            $id = $this->getOldBidSubmissionCodeForTechnicalAndCommercial($id, $envelopType);
        }

        $tenderId = $request['tenderId'];
        $documentType = TenderMaster::select('document_type')->where('id',$tenderId)->first();

        $documentSystemId = $documentType->document_type == 0 ? 108:113;

        $query = DocumentAttachments::with(['bid_verify', 'document_parent'])->where('documentSystemCode', $id)->where('documentSystemID', $documentSystemId)->where('attachmentType',0)->where('envelopType', $envelopType);

       // return $this->sendResponse($query, 'Tender Masters retrieved successfully');

        $search = $request->input('search.value');
        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $query = $query->where(function ($query) use ($search) {
                $query->where('originalFileName', 'like', "%{$search}%")
                    ->orWhere('attachmentDescription', 'like', "%{$search}%")
                    ->orWhereHas('document_parent', function ($query) use ($search) {
                        $query->where('attachmentDescription', 'like', "%{$search}%");
                    });
            });
        }


        return \DataTables::eloquent($query)
            ->order(function ($query) use ($input,$sort) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('attachmentID', $sort);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }


    public function getConsolidatedDataAttachment(Request $request)
    {
        $input = $request->all();

        $details = $input['extraParams'];

        $attachmentId = $details['attachmentId'];

       // return $this->sendResponse($details['tenderId'], 'Consolidated view data Successfully get');


        $attachment = DocumentAttachments::where('attachmentID', $attachmentId)
            ->where('documentSystemID', 108)
            ->first();

        $data['attachmentPath'] = Helper::getFileUrlFromS3($attachment['path']);
        $data['extension'] = strtolower(pathinfo($attachment['path'], PATHINFO_EXTENSION));

        return $this->sendResponse($data, 'Consolidated view data Successfully get');
 
    }



        public function tenderBIdDocApproveal(Request $request)
        {
            
            
            $input = $request->all();
            $id = $input['id'];
            $comments = $input['comments'];
            $val = $input['data']['value'];
            $verify_id = $input['verify_id'];
            $bid_sub_id = $input['bid_sub_id'];
    
            DB::beginTransaction();
            try {
                $data['status'] = $val;
                $data['remarks'] = $comments;
                $data['verified_by'] = \Helper::getEmployeeSystemID();
                $data['verified_date'] =  date('Y-m-d H:i:s');
                
                $results = BidDocumentVerification::where('id',$verify_id)->update($data,$verify_id);


                $bid_verify = BidDocumentVerification::where('bis_submission_master_id',$bid_sub_id)->where('status',0)->count();
                if($bid_verify == 0)
                {   

                    $bid_sub_data['doc_verifiy_yn'] = 1;
                    $bid_sub_data['doc_verifiy_by_emp'] = \Helper::getEmployeeSystemID();
                    $bid_sub_data['doc_verifiy_date'] =  date('Y-m-d H:i:s');

                    $results = BidSubmissionMaster::where('id',$bid_sub_id)->update($bid_sub_data,$bid_sub_id);
                }

      

        
                DB::commit();
                return ['success' => true, 'message' => 'Successfully updated', 'data' => $results];
            } catch (\Exception $e) {
                DB::rollback();
                Log::error($this->failed($e));
                return ['success' => false, 'message' => $e];
            }
        }


        public function tenderBIdDocTypeApproveal(Request $request)
        {
          
           
            $input = $request->all();
            $id = $input['id'];
           // $comments = $input['comments'];
            $val = $input['data']['value'];
            $verify_id = $input['verify_id'];

            
            DB::beginTransaction();
            try {
                $data['document_submit_type'] = $val;
              //  $data['submit_remarks'] = $comments;
                
                $results = BidDocumentVerification::where('id',$verify_id)->update($data,$verify_id);
        
                DB::commit();
                return ['success' => true, 'message' => 'Successfully updated', 'data' => $results];
            } catch (\Exception $e) {
                DB::rollback();
                Log::error($this->failed($e));
                return ['success' => false, 'message' => $e];
            }
        }


        
        public function tenderBIdDocSubmission(Request $request)
        {
          
            $input = $request->all();
            $id = $input['bid_id'];
            $comments = '';
            if(isset($input['comments']) && !empty($input['comments']))
            {
                $comments = $input['comments'];
            }
           
            $val = $input['type'];
            
            DB::beginTransaction();
            try {
                
                $bid_sub_data['doc_verifiy_by_emp'] = \Helper::getEmployeeSystemID();
                $bid_sub_data['doc_verifiy_date'] =  date('Y-m-d H:i:s');
                $bid_sub_data['doc_verifiy_status'] = $val;
                $bid_sub_data['doc_verifiy_comment'] = $comments;

                $results = BidSubmissionMaster::where('id',$id)->update($bid_sub_data,$id);
        
                DB::commit();
                return ['success' => true, 'message' => 'Successfully updated', 'data' => $results];
            } catch (\Exception $e) {
                DB::rollback();
                Log::error($this->failed($e));
                return ['success' => false, 'message' => $e];
            }
        }


        public function checkTenderBidDocExist(Request $request)
        {
            $input = $request->all();
            $details = $input['extraParams'];
            $tender_id = $details['tenderId'];
            $id = $request['id'];

            if($this->getOldBidSubmissonCode($id) != null){
                $id = $this->getOldBidSubmissonCode($id);
            }

            DB::beginTransaction();
            try {
                
                $documentType = TenderMaster::select('document_type')->where('id',$tender_id)->first();  
                $documetSystemId = ($documentType->document_type) == 0 ? 108 : 113; 
                $results = DocumentAttachments::where('documentSystemCode',$id)->where('documentSystemID', $documetSystemId)->where('envelopType',3)->count();   
                
                if($results == 0)
                {
                    $bid_sub_data['doc_verifiy_yn'] = 1;
                    $bid_sub_data['doc_verifiy_by_emp'] = \Helper::getEmployeeSystemID();
                    $bid_sub_data['doc_verifiy_date'] =  date('Y-m-d H:i:s');
                    $results = BidSubmissionMaster::where('id',$id)->update($bid_sub_data,$id);
                }
                
                $data['type'] = $documentType->document_type;
                DB::commit();
                return ['success' => true, 'message' => 'Successfully updated', 'data' => $data];
            } catch (\Exception $e) {
                DB::rollback();
                Log::error($this->failed($e));
                return ['success' => false, 'message' => $e];
            }
        }


        private function getOldBidSubmissonCode($id){
            $supplierTenderNegotiationsId = TenderBidNegotiation::select('bid_submission_master_id_old')->where('bid_submission_master_id_new', $id)
                ->select('tender_id', 'bid_submission_master_id_old', 'tender_negotiation_id')
                ->first();

            if(isset($supplierTenderNegotiationsId)){
                $TenderNegotiationArea = TenderNegotiationArea::select('tender_documents')
                    ->where('tender_negotiation_id', $supplierTenderNegotiationsId
                        ->tender_negotiation_id)->first();
                if($TenderNegotiationArea->tender_documents == 0){
                    $id = $supplierTenderNegotiationsId->bid_submission_master_id_old;
                }

                return $id;
            }

            return null;
        }

        private function getOldBidSubmissionCodeForTechnicalAndCommercial($id, $envelopType){

            $supplierTenderNegotiationsId = TenderBidNegotiation::select('bid_submission_master_id_old')->where('bid_submission_master_id_new', $id)
                ->select('tender_id', 'bid_submission_master_id_old', 'tender_negotiation_id')
                ->first();

            if(isset($supplierTenderNegotiationsId)){
                $TenderNegotiationArea = TenderNegotiationArea::select('tender_documents', 'pricing_schedule', 'technical_evaluation')
                    ->where('tender_negotiation_id', $supplierTenderNegotiationsId
                        ->tender_negotiation_id)->first();

                if(($TenderNegotiationArea->pricing_schedule == 0 && $envelopType == 1) || ($TenderNegotiationArea->technical_evaluation == 0 && $envelopType == 2)){
                    $id = $supplierTenderNegotiationsId->bid_submission_master_id_old;
                }

                if($TenderNegotiationArea->tender_documents == 0 && $envelopType == 3){
                    $id = $supplierTenderNegotiationsId->bid_submission_master_id_old;
                }

                return $id;
            }

            return null;
        }

}
