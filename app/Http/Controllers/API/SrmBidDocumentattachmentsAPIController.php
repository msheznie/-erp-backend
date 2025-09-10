<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateSrmBidDocumentattachmentsAPIRequest;
use App\Http\Requests\API\UpdateSrmBidDocumentattachmentsAPIRequest;
use App\Models\SrmBidDocumentattachments;
use App\Repositories\SrmBidDocumentattachmentsRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use App\Criteria\FilterTenderDocumentCriteria;
use App\Models\Company;
use App\Models\CustomerInvoiceDirect;
use App\Models\CompanyPolicyMaster;
use App\Models\DocumentMaster;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Finder\SplFileInfo;
use App\helper\Helper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
/**
 * Class SrmBidDocumentattachmentsController
 * @package App\Http\Controllers\API
 */

class SrmBidDocumentattachmentsAPIController extends AppBaseController
{
    /** @var  SrmBidDocumentattachmentsRepository */
    private $srmBidDocumentattachmentsRepository;

    public function __construct(SrmBidDocumentattachmentsRepository $srmBidDocumentattachmentsRepo)
    {
        $this->srmBidDocumentattachmentsRepository = $srmBidDocumentattachmentsRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/srmBidDocumentattachments",
     *      summary="getSrmBidDocumentattachmentsList",
     *      tags={"SrmBidDocumentattachments"},
     *      description="Get all SrmBidDocumentattachments",
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
     *                  @OA\Items(ref="#/definitions/SrmBidDocumentattachments")
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
        $this->srmBidDocumentattachmentsRepository->pushCriteria(new RequestCriteria($request));
        $this->srmBidDocumentattachmentsRepository->pushCriteria(new LimitOffsetCriteria($request));
        $this->srmBidDocumentattachmentsRepository->pushCriteria(new FilterTenderDocumentCriteria($request));
        $srmBidDocumentattachments = $this->srmBidDocumentattachmentsRepository->all();

        return $this->sendResponse($srmBidDocumentattachments->toArray(), 'Srm Bid Documentattachments retrieved successfully');
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/srmBidDocumentattachments",
     *      summary="createSrmBidDocumentattachments",
     *      tags={"SrmBidDocumentattachments"},
     *      description="Create SrmBidDocumentattachments",
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
     *                  ref="#/definitions/SrmBidDocumentattachments"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateSrmBidDocumentattachmentsAPIRequest $request)
    {
        $input = $request->all();

        $srmBidDocumentattachments = $this->srmBidDocumentattachmentsRepository->create($input);

        return $this->sendResponse($srmBidDocumentattachments->toArray(), 'Srm Bid Documentattachments saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/srmBidDocumentattachments/{id}",
     *      summary="getSrmBidDocumentattachmentsItem",
     *      tags={"SrmBidDocumentattachments"},
     *      description="Get SrmBidDocumentattachments",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of SrmBidDocumentattachments",
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
     *                  ref="#/definitions/SrmBidDocumentattachments"
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
        /** @var SrmBidDocumentattachments $srmBidDocumentattachments */
        $srmBidDocumentattachments = $this->srmBidDocumentattachmentsRepository->findWithoutFail($id);

        if (empty($srmBidDocumentattachments)) {
            return $this->sendError('Srm Bid Documentattachments not found');
        }

        return $this->sendResponse($srmBidDocumentattachments->toArray(), 'Srm Bid Documentattachments retrieved successfully');
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/srmBidDocumentattachments/{id}",
     *      summary="updateSrmBidDocumentattachments",
     *      tags={"SrmBidDocumentattachments"},
     *      description="Update SrmBidDocumentattachments",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of SrmBidDocumentattachments",
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
     *                  ref="#/definitions/SrmBidDocumentattachments"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateSrmBidDocumentattachmentsAPIRequest $request)
    {
        $input = $request->all();

        /** @var SrmBidDocumentattachments $srmBidDocumentattachments */
        $srmBidDocumentattachments = $this->srmBidDocumentattachmentsRepository->findWithoutFail($id);

        if (empty($srmBidDocumentattachments)) {
            return $this->sendError('Srm Bid Documentattachments not found');
        }

        $srmBidDocumentattachments = $this->srmBidDocumentattachmentsRepository->update($input, $id);

        return $this->sendResponse($srmBidDocumentattachments->toArray(), 'SrmBidDocumentattachments updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/srmBidDocumentattachments/{id}",
     *      summary="deleteSrmBidDocumentattachments",
     *      tags={"SrmBidDocumentattachments"},
     *      description="Delete SrmBidDocumentattachments",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of SrmBidDocumentattachments",
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
        /** @var SrmBidDocumentattachments $srmBidDocumentattachments */
        $srmBidDocumentattachments = $this->srmBidDocumentattachmentsRepository->findWithoutFail($id);

        if (empty($srmBidDocumentattachments)) {
            return $this->sendError('Srm Bid Documentattachments not found');
        }

        $srmBidDocumentattachments->delete();

        return $this->sendResponse(true, 'Srm Bid Documentattachments deleted successfully');

    }

    public function storeTenderBidDocuments(Request $request){

        DB::beginTransaction();
        try {

            
        $input = $request->all();
        $attachmentDescription = $input['attachmentDescription'];
        $companySystemID = $input['companySystemID'];
        $documentSystemID = $input['documentSystemID'];
        $documentSystemCode = $input['documentSystemCode'];

        $isExist = SrmBidDocumentattachments::where('companySystemID',$companySystemID)
        ->where('documentSystemID',$documentSystemID)
        ->where('documentSystemCode',$documentSystemCode)
        ->where('attachmentDescription',$attachmentDescription)
        ->count(); 
        if($isExist >= 1){ 
           return ['status' => false, 'message' => trans('srm_bid.document_attachments_saved_successfully')];
        }else {
            

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
                return $this->sendError(trans('srm_bid.max_file_type_not_allowed'), 500);
            }


            if (isset($input['sizeInKbs'])) {
                if ($input['sizeInKbs'] > env('ATTACH_UPLOAD_SIZE_LIMIT')) {
                    return $this->sendError(trans('srm_bid.max_file_size_exceeded').' '.\Helper::bytesToHuman(env('ATTACH_UPLOAD_SIZE_LIMIT')), 500);
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


       
            $input['tender_id'] = $documentSystemCode;
            $documentAttachments = $this->srmBidDocumentattachmentsRepository->create($input);

            $file = $request->request->get('file');
            $decodeFile = base64_decode($file);

            $input['myFileName'] = $documentAttachments->companyID . '_' . $documentAttachments->documentID . '_' . $documentAttachments->documentSystemCode . '_' . $documentAttachments->id . '.' . $extension;

            if ($documentAttachments->documentID == 'PRN') {
                $documentAttachments->documentID =  $documentAttachments->documentID . 'I';
            }


            if (Helper::checkPolicy($input['companySystemID'], 50)) {
                $path = $companyID . '/G_ERP/' . $documentAttachments->documentID . '/' . $documentAttachments->documentSystemCode . '/' . $input['myFileName'];
            } else {
                $path = $documentAttachments->documentID . '/' . $documentAttachments->documentSystemCode . '/' . $input['myFileName'];
            }

            Storage::disk(Helper::policyWiseDisk($input['companySystemID'], 'public'))->put($path, $decodeFile);

            $input['isUploaded'] = 1;
            $input['path'] = $path;
        

            $documentAttachments = $this->srmBidDocumentattachmentsRepository->update($input, $documentAttachments->id);
        
            DB::commit();
            return $this->sendResponse($documentAttachments->toArray(), trans('srm_bid.document_attachments_saved_successfully'));
        }
            

        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError(trans('srm_faq.unable_to_upload_the_attachment'), 500);
        }

      
    }


    public function downloadFile(Request $request)
    {

        $input = $request->all();

        /*$fileName= "Consolidated_top_suppliers.png";
        return Storage::disk('public')->download($fileName);*/

        /** @var DocumentAttachments $documentAttachments */
        $documentAttachments = $this->srmBidDocumentattachmentsRepository->findWithoutFail($input['id']);

        if (empty($documentAttachments)) {
            return $this->sendError('Document Attachments not found');
        }

        if (!is_null($documentAttachments->path)) {
            if ($exists = Storage::disk(Helper::policyWiseDisk($documentAttachments->companySystemID, 'public'))->exists($documentAttachments->path)) {
                return Storage::disk(Helper::policyWiseDisk($documentAttachments->companySystemID, 'public'))->download($documentAttachments->path, $documentAttachments->myFileName);
            } else {
                return $this->sendError('Attachments not found', 500);
            }
        } else {
            return $this->sendError('Attachment is not attached', 404);
        }
    }








}
