<?php

namespace App\Http\Controllers\API;

use App\helper\email;
use App\helper\Helper;
use App\Http\Requests\API\CreateTenderBidClarificationsAPIRequest;
use App\Http\Requests\API\UpdateTenderBidClarificationsAPIRequest;
use App\Mail\EmailForQueuing;
use App\Models\CompanyPolicyMaster;
use App\Models\SystemConfigurationAttributes;
use App\Models\TenderBidClarifications;
use App\Repositories\TenderBidClarificationsRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Models\Company;
use App\Models\DocumentAttachments;
use App\Models\DocumentMaster;
use App\Models\Employee;
use App\Models\TenderMaster;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Exception;
use Carbon\Carbon;

/**
 * Class TenderBidClarificationsController
 * @package App\Http\Controllers\API
 */

class TenderBidClarificationsAPIController extends AppBaseController
{
    /** @var  TenderBidClarificationsRepository */
    private $tenderBidClarificationsRepository;

    public function __construct(TenderBidClarificationsRepository $tenderBidClarificationsRepo)
    {
        $this->tenderBidClarificationsRepository = $tenderBidClarificationsRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/tenderBidClarifications",
     *      summary="Get a listing of the TenderBidClarifications.",
     *      tags={"TenderBidClarifications"},
     *      description="Get all TenderBidClarifications",
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
     *                  @SWG\Items(ref="#/definitions/TenderBidClarifications")
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
        $this->tenderBidClarificationsRepository->pushCriteria(new RequestCriteria($request));
        $this->tenderBidClarificationsRepository->pushCriteria(new LimitOffsetCriteria($request));
        $tenderBidClarifications = $this->tenderBidClarificationsRepository->all();

        return $this->sendResponse($tenderBidClarifications->toArray(), trans('custom.tender_bid_clarifications_retrieved_successfully'));
    }

    /**
     * @param CreateTenderBidClarificationsAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/tenderBidClarifications",
     *      summary="Store a newly created TenderBidClarifications in storage",
     *      tags={"TenderBidClarifications"},
     *      description="Store TenderBidClarifications",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="TenderBidClarifications that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/TenderBidClarifications")
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
     *                  ref="#/definitions/TenderBidClarifications"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateTenderBidClarificationsAPIRequest $request)
    {
        $input = $request->all();

        $tenderBidClarifications = $this->tenderBidClarificationsRepository->create($input);

        return $this->sendResponse($tenderBidClarifications->toArray(), trans('custom.tender_bid_clarifications_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/tenderBidClarifications/{id}",
     *      summary="Display the specified TenderBidClarifications",
     *      tags={"TenderBidClarifications"},
     *      description="Get TenderBidClarifications",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of TenderBidClarifications",
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
     *                  ref="#/definitions/TenderBidClarifications"
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
        /** @var TenderBidClarifications $tenderBidClarifications */
        $tenderBidClarifications = $this->tenderBidClarificationsRepository->findWithoutFail($id);

        if (empty($tenderBidClarifications)) {
            return $this->sendError(trans('custom.tender_bid_clarifications_not_found'));
        }

        return $this->sendResponse($tenderBidClarifications->toArray(), trans('custom.tender_bid_clarifications_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateTenderBidClarificationsAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/tenderBidClarifications/{id}",
     *      summary="Update the specified TenderBidClarifications in storage",
     *      tags={"TenderBidClarifications"},
     *      description="Update TenderBidClarifications",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of TenderBidClarifications",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="TenderBidClarifications that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/TenderBidClarifications")
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
     *                  ref="#/definitions/TenderBidClarifications"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateTenderBidClarificationsAPIRequest $request)
    {
        $input = $request->all();

        /** @var TenderBidClarifications $tenderBidClarifications */
        $tenderBidClarifications = $this->tenderBidClarificationsRepository->findWithoutFail($id);

        if (empty($tenderBidClarifications)) {
            return $this->sendError(trans('custom.tender_bid_clarifications_not_found'));
        }

        $tenderBidClarifications = $this->tenderBidClarificationsRepository->update($input, $id);

        return $this->sendResponse($tenderBidClarifications->toArray(), trans('custom.tenderbidclarifications_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/tenderBidClarifications/{id}",
     *      summary="Remove the specified TenderBidClarifications from storage",
     *      tags={"TenderBidClarifications"},
     *      description="Delete TenderBidClarifications",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of TenderBidClarifications",
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
        /** @var TenderBidClarifications $tenderBidClarifications */
        $tenderBidClarifications = $this->tenderBidClarificationsRepository->findWithoutFail($id);

        if (empty($tenderBidClarifications)) {
            return $this->sendError(trans('custom.tender_bid_clarifications_not_found'));
        }

        $tenderBidClarifications->delete();

        return $this->sendSuccess('Tender Bid Clarifications deleted successfully');
    }
    public function getPreBidClarifications(Request $request)
    {
        $input = $request->all();
        $companyId = $input['companySystemID'];
        $tenderId = isset($input['tender']) ? $input['tender'] : 0;
        $isUnasweredYN = isset($input['isUnanswered']) ? $input['isUnanswered'] : false;
        $data = TenderMaster::with(['tenderPreBidClarification' => function ($q) use ($isUnasweredYN) {
            $q->where('parent_id', 0);
            $q->when(($isUnasweredYN == true), function ($query) {
                $query->where('is_answered', 0);
            });
            $q->with(['supplier']);
        }])
            ->whereHas('tenderPreBidClarification', function ($q) use ($isUnasweredYN) {
                $q->where('parent_id', 0);
                $q->when(($isUnasweredYN == true), function ($query) {
                    $query->where('is_answered', 0);
                });
            })->when(($tenderId > 0), function ($query) use ($tenderId) {
                $query->where('id', $tenderId);
            })->where('company_id', $companyId)
            ->get();

        return $data;
    }
    public function getPreBidClarificationsResponse(Request $request)
    {
        $input = $request->all();
        $id = $input['Id'];
        $employeeId = Helper::getEmployeeSystemID();

        $data['response'] = TenderBidClarifications::with(['supplier', 'employee' => function ($q) {
            $q->with(['profilepic']);
        }, 'attachment'])
            ->where('id', '=', $id)
            ->orWhere('parent_id', '=', $id)
            ->orderBy('parent_id', 'asc')
            ->get();
        $profilePic = Employee::with(['profilepic'])
            ->where('employeeSystemID', $employeeId)
            ->first();
        $data['profilePic'] = $profilePic['profilepic']['profile_image_url'];
        return $data;
    }
    public function createResponse(Request $request)
    {
        $input = $request->all();
        $messages = [
            'comments.required' => trans('srm_faq.comment_field_is_required')
        ];
        $validator = \Validator::make($input, [
            'comments' => 'required'
        ],$messages);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        } 
        
        $employeeId = Helper::getEmployeeSystemID();
        $response = $input['comments'];
        $id = $input['id'];
        $companySystemID = $input['companySystemID'];
        $tenderParentPost = TenderBidClarifications::where('id', $id)->first();
        $company = Company::where('companySystemID', $companySystemID)->first();
        $documentCode = DocumentMaster::where('documentSystemID', 109)->first();




        DB::beginTransaction();
        try {
            $data['tender_master_id'] = $tenderParentPost['tender_master_id'];
            $data['posted_by_type'] = 1;
            $data['post'] = $response;
            $data['user_id'] = $employeeId;
            $data['is_public'] = $tenderParentPost['is_public'];
            $data['parent_id'] = $id;
            $data['created_by'] = $employeeId;
            $data['company_id'] = $companySystemID;
            $data['document_system_id'] = $documentCode->documentSystemID;
            $data['document_id'] = $documentCode->documentID;
            $data['is_checked'] = $input['is_checked'];
            $result = TenderBidClarifications::create($data);
            if (isset($input['Attachment']) && !empty($input['Attachment'])) {
                $attachment = $input['Attachment'];
                $this->uploadAttachment($attachment, $companySystemID, $company, $documentCode, $result->id);
            }

            if ($result) {
                $updateRec['is_answered'] = 1;
                if($input['is_checked'] == 1){
                    $updateRec['is_checked'] = 1;
                }
                $result =  TenderBidClarifications::where('id', $id)
                    ->update($updateRec);
                DB::commit();
                return ['success' => true, 'message' => trans('srm_faq.successfully_saved'), 'data' => $result];
            }
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($this->failed($e));
            return ['success' => false, 'message' => $e];
        }
    }
    public function deletePreTender(Request $request)
    {
        $input = $request->all();
        $id = $input['id'];
        $companySystemID = $input['companySystemID'];
        $tenderMasterId = $input['tenderMasterId'];
        $masterResponseId = $input['masterResponseId'];
        $tenderPreBidClarification = $this->tenderBidClarificationsRepository->findWithoutFail($id);

        if (empty($tenderPreBidClarification)) {
            return $this->sendError(trans('custom.not_found_1'));
        } 

        $isLastSupplierResponse = TenderBidClarifications::select('id')
            ->where('tender_master_id', $tenderMasterId)
            ->where('parent_id', $masterResponseId) 
            ->where('posted_by_type', 0) 
            ->orderBy('id', 'desc')
            ->first();

        $supplierId = ($isLastSupplierResponse['id'] ? $isLastSupplierResponse['id'] : 0);

        $tenderPreBidClarification->delete();
        DocumentAttachments::where('documentSystemID', 109)
            ->where('companySystemID', $companySystemID)
            ->where('documentSystemCode', $id)
            ->delete();


        $isResponseExist = TenderBidClarifications::select('ids')
            ->where('tender_master_id', $tenderMasterId)
            ->where('parent_id', $masterResponseId)
            ->where('posted_by_type', 1)
            ->where('id', '>', $supplierId)
            ->count();

        if ($isResponseExist == 0) {
            $updateRec['is_answered'] = 0;
            TenderBidClarifications::where('id', $masterResponseId)
                ->update($updateRec);
        }

        return $this->sendResponse($id, trans('custom.file_deleted'));
    }
    public function getPreBidEditData(Request $request)
    {
        $input = $request->all();
        $id = $input['id'];
        $tenderId = $input['tenderMasterId'];
        $data = TenderBidClarifications::with(['attachment'])->where('id', $id)->first();
        return $data;
    }
    public function updatePreBid(Request $request)
    {
        $input = $request->all();
        $companySystemID = $input['companySystemID'];
        $company = Company::where('companySystemID', $companySystemID)->first();
        $documentCode = DocumentMaster::where('documentSystemID', 109)->first();
        DB::beginTransaction();
        try {
            $data['post'] = $input['post'];
            $this->tenderBidClarificationsRepository->update($input, $input['id']);
            $isAttachmentExist = DocumentAttachments::where('documentSystemID', 109)
                ->where('companySystemID', $companySystemID)
                ->where('documentSystemCode', $input['id'])
                ->count();

            if ($isAttachmentExist > 0 && $input['isDeleted'] == 1) {
                DocumentAttachments::where('documentSystemID', 109)
                    ->where('companySystemID', $companySystemID)
                    ->where('documentSystemCode', $input['id'])
                    ->delete();
            }

            if (isset($input['Attachment']) && !empty($input['Attachment'])) {
                $attachment = $input['Attachment'];
                $this->uploadAttachment($attachment, $companySystemID, $company, $documentCode, $input['id']);
            }

            DB::commit();
            return ['success' => true, 'message' => 'Successfully updated'];
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($this->failed($e));
            return ['success' => false, 'message' => $e];
        }
    }
    public function uploadAttachment($attachment, $companySystemID, $company, $documentCode, $id)
    {
        if (!empty($attachment) && isset($attachment['file'])) {
            $extension = $attachment['fileType'];
            $allowExtensions = ['png', 'jpg', 'jpeg', 'pdf', 'txt', 'xlsx','docx'];

            if (!in_array(strtolower($extension), $allowExtensions)) {
                return $this->sendError('This type of file not allow to upload.', 500);
            }

            if (isset($attachment['size'])) {
                if ($attachment['size'] > 2097152) {
                    return $this->sendError("Maximum allowed file size is 2 MB. Please upload lesser than 2 MB.", 500);
                }
            }
            $file = $attachment['file'];
            $decodeFile = base64_decode($file);
            $attch = time() . '_PreBidClarificationCompany.' . $extension;
            $path = $companySystemID . '/PreBidClarification/' . $attch;
            Storage::disk(Helper::policyWiseDisk($companySystemID, 'public'))->put($path, $decodeFile);

            $att['companySystemID'] = $companySystemID;
            $att['companyID'] = $company->CompanyID;
            $att['documentSystemID'] = $documentCode->documentSystemID;
            $att['documentID'] = $documentCode->documentID;
            $att['documentSystemCode'] = $id;
            $att['attachmentDescription'] = 'Pre-Bid Clarification ' . time();
            $att['path'] = $path;
            $att['originalFileName'] = $attachment['originalFileName'];
            $att['myFileName'] = $company->CompanyID . '_' . time() . '_PreBidClarification.' . $extension;
            $att['sizeInKbs'] = $attachment['sizeInKbs'];
            $att['isUploaded'] = 1;
            DocumentAttachments::create($att);
        }
    }
    public function closeThread(Request $request)
    {
        $input = $request->all();
        $id = $input['id'];
        DB::beginTransaction();
        try {
            $data['is_closed'] = 1;
            $this->tenderBidClarificationsRepository->update($data, $id);
            DB::commit();
            return ['success' => true, 'message' => trans('srm_faq.successfully_thread_closed')];
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($this->failed($e));
            return ['success' => false, 'message' => $e];
        }
    }

    public function forwardPreBidClarification(Request $request){
        $input = $request->all();
        $bidId = $input['bid_id'];
        $companyId = $input['companyId'];

        $bidClarifications = TenderBidClarifications::with(['supplier', 'employee' => function ($q) {
            $q->with(['profilepic']);
        }, 'attachment','tender'])
            ->where('id', '=', $bidId)
            ->orWhere('parent_id', '=', $bidId)
            ->orderBy('parent_id', 'asc')
            ->get();

        $tenderCode = $bidClarifications[0]->tender->tender_code;
        $tenderTitle = $bidClarifications[0]->tender->title;

        $preBidClarificationsString = "";
        $file = array();

        foreach ($bidClarifications as $bidClarification){

            if($bidClarification->supplier){
                $supplierName = isset($bidClarification->supplier) ? $bidClarification->supplier->name : "Supplier";
                $clarificationText = "<span style='font-weight: bold; font-size: 16px'>$supplierName</span><br />";
            }
            if($bidClarification->employee){
                $supplierName = isset($bidClarification->employee) ? $bidClarification->employee->empName : "Admin";
                $clarificationText = "<span style='font-weight: bold; font-size: 16px'>$supplierName</span><br />";
            }

            $createdAt = Carbon::parse($bidClarification->created_at)->format('F j, Y, g:i A');
            $clarificationText .= "<span style='font-size: 12px;font-style: italic'>$createdAt</span><br />";

            if($bidClarification->post){
                $clarificationText .= "<span style='font-size: 14px'>$bidClarification->post</span><br />";
            }

            $attachments = $bidClarification->attachment;
            if($attachments) {
                foreach ($attachments as $attachment) {
                if ($attachment) {
                    $clarificationText .= "<span style='font-size: 12px;font-weight: bold;'>$attachment->originalFileName</span><br />";
                }
                    $file[$attachment->originalFileName] = Helper::getFileUrlFromS3($attachment->path);
                }
            }

            $clarificationText .= "<br />";

            $preBidClarificationsString .= $clarificationText;
        }

        $emailString = $input['emailString'];
        $validator = Validator::make(
            ['emails' => $emailString],
            ['emails.*' => 'email']
        );

        $emailCounts = array_count_values($emailString);
        $duplicateEmails = array();
        foreach ($emailCounts as $email => $count) {
            if ($count > 1) {
                $duplicateEmails[] = $email;
            }
        }

        if ($validator->fails()) {
            $invalidEmails = [];
            foreach ($emailString as $index => $email) {
                if ($validator->errors()->has("emails.$index")) {
                    $invalidEmails[] = $email;
                }
            }
            $invalidEmailsString = implode(', ', $invalidEmails);
            $errorMessage = $invalidEmailsString . ' ' . trans('srm_faq.not_valid_emails_enter_valid');
            return response()->json(['message' => $errorMessage, 'invalid_emails' => $invalidEmails], 422);
        }elseif (!empty($duplicateEmails)) {
            $errorMessage = implode(', ', $duplicateEmails) .' '.trans('srm_faq.emails_already_exist');
            return response()->json(['message' => $errorMessage,], 422);
        } else {
            foreach ($emailString as $email){
            $forwardEmail = email::emailAddressFormat($email);
            $dataEmail['companySystemID'] = $companyId;
            $dataEmail['alertMessage'] = "Pre Bid Clarification";
            $dataEmail['empEmail'] = $forwardEmail;
            $body = "To whom it may concern,"."<br /><br />"." Supplier has requested the below Prebid Clarification regarding the ". $tenderCode ." | ". $tenderTitle .". Kindly review and provide the necessary inputs. "."<br /><br />"."$preBidClarificationsString"."</b><br /><br />"." Thank You"."<br /><br /><b>";
            $dataEmail['emailAlertMessage'] = $body;
            $dataEmail['attachmentList'] = $file;
            $sendEmail = \Email::sendEmailErp($dataEmail);
            }
        }
        return ['success' => true, 'message' => trans('srm_faq.emails_sent_successfully')];

    }

    public function getPreBidClarificationsPolicyData(Request $request){

        $input = $request->all();
        $companySystemID = $input['companySystemID'];
        $raiseAsPrivate = \Helper::checkPolicy($companySystemID,87);

        return $this->sendResponse($raiseAsPrivate, trans('custom.prebid_clarifications_policy_retrieved_successfull'));
    }
}
