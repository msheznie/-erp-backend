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

        /*$fileName= "Consolidated_top_suppliers.png";
        return Storage::disk('public')->download($fileName);*/

        /** @var DocumentAttachments $documentAttachments */
        $documentAttachments = $this->documentAttachmentsRepository->findWithoutFail($input['id']);

        if (empty($documentAttachments)) {
            return $this->sendError('Document Attachments not found');
        }

        if(!is_null($documentAttachments->path)) {
            if ($exists = Storage::disk('public')->exists($documentAttachments->path)) {
                return Storage::disk('public')->download($documentAttachments->path, $documentAttachments->myFileName);
            } else {
                return $this->sendError('Attachments not found', 500);
            }
        }else{
            return $this->sendError('Attachment is not attached', 404);
        }
    }

    function downloadFileFrom(Request $request) {

        $input = $request->all();

        $documentAttachments = $this->documentAttachmentsRepository->findWithoutFail($input['id']);

        if (empty($documentAttachments)) {
            return $this->sendError('Document Attachments not found');
        }

        $fileName = "Desktop/upload/".$documentAttachments->path;


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
     * @param CreateDocumentAttachmentsAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateDocumentAttachmentsAPIRequest $request)
    {

        $input = $request->all();
        $extension = $input['fileType'];

        $blockExtensions = ['ace', 'ade', 'adp', 'ani', 'app', 'asp', 'aspx', 'asx', 'bas', 'bat', 'cla', 'cer', 'chm', 'cmd', 'cnt', 'com',
            'cpl', 'crt', 'csh', 'class', 'der', 'docm', 'exe', 'fxp', 'gadget', 'hlp', 'hpj', 'hta', 'htc', 'inf', 'ins', 'isp', 'its', 'jar',
            'js', 'jse', 'ksh', 'lnk', 'mad', 'maf', 'mag', 'mam', 'maq', 'mar', 'mas', 'mat', 'mau', 'mav', 'maw', 'mda', 'mdb', 'mde', 'mdt',
            'mdw', 'mdz', 'mht', 'mhtml', 'msc', 'msh', 'msh1', 'msh1xml', 'msh2', 'msh2xml', 'mshxml', 'msi', 'msp', 'mst', 'ops', 'osd',
             'ocx', 'pl', 'pcd', 'pif', 'plg', 'prf', 'prg', 'ps1', 'ps1xml', 'ps2', 'ps2xml', 'psc1', 'psc2', 'pst', 'reg', 'scf', 'scr',
              'sct', 'shb', 'shs', 'tmp', 'url', 'vb', 'vbe', 'vbp', 'vbs', 'vsmacros', 'vss', 'vst', 'vsw', 'ws', 'wsc', 'wsf', 'wsh', 'xml',
              'xbap', 'xnk','php'];

        if (in_array($extension, $blockExtensions))
        {
            return $this->sendError('This type of file not allow to upload.',500);
        }


        if(isset($input['size'])){
            if ($input['size'] > 31457280) {
                return $this->sendError("Maximum allowed file size is 30 MB. Please upload lesser than 30 MB.",500);
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

        if (isset($input['companySystemID'])) {

            $companyMaster = Company::where('companySystemID', $input['companySystemID'])->first();

            if ($companyMaster) {
                $input['companyID'] = $companyMaster->CompanyID;
            }
        }

        $documentAttachments = $this->documentAttachmentsRepository->create($input);

        $file = $request->request->get('file');
        $decodeFile = base64_decode($file);

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

        $path = $documentAttachments->path;

        $attachment = DocumentAttachments::where('attachmentID', $id)
            ->first();

        if($attachment['documentSystemID'] == 20){
            $invoice = CustomerInvoiceDirect::find($attachment['documentSystemCode']);
            if (!empty($invoice)) {
                if($invoice->confirmedYN == 1 || $invoice->approved == -1){
                    return $this->sendError('Customer invoice confirmed, you cannot delete the attachment', 500);
                }

            }
        }

        if($attachment['pullFromAnotherDocument'] == 0){
            if ($exists = Storage::disk('public')->exists($path)) {
                $documentAttachments->delete();
                Storage::disk('public')->delete($path);
            } else {
                $documentAttachments->delete();
            }
        }else if($attachment['pullFromAnotherDocument'] == -1){
            $documentAttachments->delete();
        }

        return $this->sendResponse($id, 'Document Attachments deleted successfully');

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
}
